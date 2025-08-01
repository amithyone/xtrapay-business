<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminCode;
use App\Models\Site;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function showCodeInput()
    {
        return view('admin.code_input');
    }

    public function verifyCode(Request $request)
    {
        $code = $request->input('code');
        $adminCode = AdminCode::where('code_type', 'first_code')->first();

        if ($adminCode && Hash::check($code, $adminCode->hashed_code)) {
            session(['first_code_verified' => true]);
            return redirect()->route('admin.second_code');
        }

        return back()->withErrors(['code' => 'Invalid code.']);
    }

    public function showSecondCode()
    {
        if (!session('first_code_verified')) {
            return redirect()->route('admin.code.input');
        }
        return view('admin.login');
    }

    public function verifySecondCode(Request $request)
    {
        $code = $request->input('code');
        $adminCode = AdminCode::where('code_type', 'second_code')->first();

        if ($adminCode && Hash::check($code, $adminCode->hashed_code)) {
            session(['admin_code_verified' => true]);
            return redirect()->route('admin.admin_login');
        }

        return back()->withErrors(['code' => 'Invalid code.']);
    }

    public function showAdminLogin()
    {
        if (!session('admin_code_verified')) {
            return redirect()->route('admin.code.input');
        }
        return view('admin.admin_login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->is_admin) {
                $request->session()->regenerate();
                return redirect()->route('admin.index');
            }
            Auth::logout();
            return back()->withErrors([
                'email' => 'You do not have admin privileges.',
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_revenue' => Transaction::where('status', 'success')->sum('amount'),
            'total_sites' => Site::count(),
            'pending_withdrawals' => Transfer::where('is_approved', false)->sum('amount'),
        ];

        return view('admin.index', compact('stats'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => $validated['is_admin'] ?? false,
        ]);

        return redirect()->route('admin.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'boolean',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->is_admin = $validated['is_admin'] ?? $user->is_admin;
        $user->save();

        return redirect()->route('admin.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.index')->with('success', 'User deleted successfully.');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'is_admin' => 'required|boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    public function toggleUserStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'message' => 'User status updated successfully'
        ]);
    }

    public function storeSite(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'api_key' => 'required|string|unique:sites',
            'webhook_url' => 'nullable|url',
            'db_connection' => 'required|in:mysql,pgsql',
        ]);

        $validated['is_active'] = true;
        $site = Site::create($validated);

        return response()->json([
            'message' => 'Site created successfully',
            'site' => $site
        ]);
    }

    public function toggleSiteStatus(Site $site)
    {
        $site->update(['is_active' => !$site->is_active]);

        return response()->json([
            'message' => 'Site status updated successfully'
        ]);
    }

    public function storeConnection(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'db_connection' => 'required|in:mysql,pgsql',
            'db_host' => 'required|string',
            'db_name' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'required|string',
        ]);

        $site = Site::findOrFail($validated['site_id']);
        $site->update([
            'db_connection' => $validated['db_connection'],
            'db_host' => $validated['db_host'],
            'db_name' => $validated['db_name'],
            'db_username' => $validated['db_username'],
            'db_password' => $validated['db_password'],
            'db_status' => false,
        ]);

        return response()->json([
            'message' => 'Database connection created successfully'
        ]);
    }

    public function testConnection(Site $site)
    {
        try {
            $connection = [
                'driver' => $site->db_connection,
                'host' => $site->db_host,
                'database' => $site->db_name,
                'username' => $site->db_username,
                'password' => $site->db_password,
            ];

            DB::connection($connection)->getPdo();
            
            $site->update(['db_status' => true]);
            
            return response()->json([
                'message' => 'Connection successful'
            ]);
        } catch (\Exception $e) {
            $site->update(['db_status' => false]);
            
            return response()->json([
                'message' => 'Connection failed: ' . $e->getMessage()
            ], 400);
        }
    }

    public function syncData(Site $site)
    {
        try {
            $connection = [
                'driver' => $site->db_connection,
                'host' => $site->db_host,
                'database' => $site->db_name,
                'username' => $site->db_username,
                'password' => $site->db_password,
            ];

            // Implement your data sync logic here
            // This could include:
            // 1. Fetching transactions from the site's database
            // 2. Updating local records
            // 3. Handling any conflicts
            // 4. Updating sync timestamp

            $site->update(['last_sync' => now()]);

            return response()->json([
                'message' => 'Data sync completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 400);
        }
    }

    public function viewSite(Site $site)
    {
        return response()->json([
            'site' => $site->load('transactions'),
            'revenue' => $site->transactions()->sum('amount'),
            'pending_transactions' => $site->transactions()->where('status', 'pending')->count(),
        ]);
    }

    public function updateSite(Request $request, Site $site)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'api_key' => 'required|string|unique:sites,api_key,' . $site->id,
            'webhook_url' => 'nullable|url',
            'db_connection' => 'required|in:mysql,pgsql',
        ]);

        $site->update($validated);

        return response()->json([
            'message' => 'Site updated successfully',
            'site' => $site
        ]);
    }

    public function approveTransaction(Transaction $transaction)
    {
        $transaction->update(['status' => 'success']);
        
        // Send Telegram notification for approved transaction
        $this->sendTelegramNotification($transaction);

        return response()->json([
            'message' => 'Transaction approved successfully'
        ]);
    }

    public function rejectTransaction(Transaction $transaction)
    {
        $transaction->update(['status' => 'failed']);
        
        // Notify user and site
        // Add notification logic here

        return response()->json([
            'message' => 'Transaction rejected successfully'
        ]);
    }

    public function approveWithdrawal(Transfer $transfer)
    {
        $transfer->update(['is_approved' => true]);
        
        // Process the withdrawal
        // Add withdrawal processing logic here
        // This could include:
        // 1. Updating user's balance
        // 2. Sending notification
        // 3. Recording the transaction
        // 4. Initiating bank transfer

        return response()->json([
            'message' => 'Withdrawal approved successfully'
        ]);
    }

    public function rejectWithdrawal(Transfer $transfer)
    {
        $transfer->update(['is_approved' => false]);
        
        // Refund the amount to user's balance
        $user = $transfer->user;
        $user->balance += $transfer->amount;
        $user->save();

        // Send notification to user
        // Add notification logic here

        return response()->json([
            'message' => 'Withdrawal rejected successfully'
        ]);
    }

    public function viewLogDetails(Transaction $transaction)
    {
        return response()->json([
            'transaction' => $transaction->load(['site', 'user']),
            'logs' => $transaction->logs ?? [],
        ]);
    }

    public function viewTransaction(Transaction $transaction)
    {
        return response()->json([
            'reference' => $transaction->reference,
            'amount' => $transaction->amount,
            'status' => $transaction->status,
            'payment_method' => $transaction->payment_method,
            'customer_name' => $transaction->customer_name,
            'customer_email' => $transaction->customer_email,
            'metadata' => $transaction->metadata,
            'site' => $transaction->site->name,
            'created_at' => $transaction->created_at->format('M d, Y H:i'),
        ]);
    }

    public function editTransaction(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,success,failed',
            'customer_name' => 'nullable|string',
            'customer_email' => 'nullable|email',
            'metadata' => 'nullable|array',
        ]);

        $transaction->update($validated);

        return response()->json([
            'message' => 'Transaction updated successfully',
            'transaction' => $transaction
        ]);
    }

    public function exportTransactions()
    {
        $transactions = Transaction::where('status', 'success')
            ->with(['site', 'user'])
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions.csv"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Reference',
                'Amount',
                'Status',
                'Payment Method',
                'Customer Name',
                'Customer Email',
                'Site',
                'Date'
            ]);

            // Add data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->reference,
                    $transaction->amount,
                    $transaction->status,
                    $transaction->payment_method,
                    $transaction->customer_name,
                    $transaction->customer_email,
                    $transaction->site->name,
                    $transaction->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Send Telegram notification for successful transaction
     */
    private function sendTelegramNotification(Transaction $transaction)
    {
        try {
            $businessProfile = $transaction->businessProfile;
            if (!$businessProfile || !$businessProfile->telegram_chat_id || !$businessProfile->telegram_bot_token) {
                Log::info('Telegram notification skipped - no chat ID or bot token configured', [
                    'transaction_id' => $transaction->id,
                    'business_profile_id' => $businessProfile ? $businessProfile->id : null
                ]);
                return;
            }

            $message = "🎉 Transaction Approved!\n\n" .
                      "💰 Amount: ₦" . number_format($transaction->amount, 2) . "\n" .
                      "📝 Reference: {$transaction->reference}\n" .
                      "🏢 Site: {$transaction->site->name}\n" .
                      "📅 Date: " . $transaction->created_at->format('M d, Y H:i') . "\n" .
                      "💳 Payment Method: {$transaction->payment_method}\n" .
                      "✅ Status: Approved by Admin";

            $response = Http::post("https://api.telegram.org/bot{$businessProfile->telegram_bot_token}/sendMessage", [
                'chat_id' => $businessProfile->telegram_chat_id,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);

            $result = $response->json();

            if ($result && isset($result['ok']) && $result['ok']) {
                Log::info('Telegram notification sent successfully for approved transaction', [
                    'transaction_id' => $transaction->id,
                    'message_id' => $result['result']['message_id'],
                    'business_profile_id' => $businessProfile->id
                ]);
            } else {
                Log::error('Failed to send Telegram notification for approved transaction', [
                    'transaction_id' => $transaction->id,
                    'response' => $result,
                    'business_profile_id' => $businessProfile->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error sending Telegram notification for approved transaction', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
