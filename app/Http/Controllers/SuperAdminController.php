<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\Transfer;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Site;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Super Admin Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_businesses' => BusinessProfile::count(),
            'total_revenue' => Transaction::where('status', 'success')->sum('amount'),
            'pending_withdrawals' => Transfer::where('is_approved', false)->sum('amount'),
            'open_tickets' => Ticket::whereIn('status', ['open', 'in_progress'])->count(),
            'total_sites' => Site::count(),
            'active_sites' => Site::where('is_active', true)->count(),
        ];

        // Recent activities
        $recentWithdrawals = Transfer::with(['businessProfile.user', 'beneficiary'])
            ->latest()
            ->take(5)
            ->get();

        $recentTickets = Ticket::with(['user', 'businessProfile'])
            ->latest()
            ->take(5)
            ->get();

        $recentTransactions = Transaction::with(['businessProfile.user', 'site'])
            ->latest()
            ->take(5)
            ->get();

        return view('super-admin.dashboard', compact('stats', 'recentWithdrawals', 'recentTickets', 'recentTransactions'));
    }

    /**
     * User Management
     */
    public function users(Request $request)
    {
        $query = User::with('businessProfile');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by admin status
        if ($request->has('admin_status')) {
            if ($request->admin_status === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->admin_status === 'user') {
                $query->where('is_admin', false);
            }
        }

        $users = $query->latest()->paginate(15);

        return view('super-admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('super-admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
            'super_admin_role' => 'required_if:is_super_admin,true',
            'super_admin_permissions' => 'array',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_admin' => $validated['is_admin'] ?? false,
            ]);

            // Create super admin if requested
            if ($validated['is_super_admin'] ?? false) {
                SuperAdmin::create([
                    'user_id' => $user->id,
                    'role' => $validated['super_admin_role'],
                    'permissions' => $validated['super_admin_permissions'] ?? [],
                    'is_active' => true,
                ]);
            }

            DB::commit();
            return redirect()->route('super-admin.users.index')->with('success', 'User created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating user: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error creating user: ' . $e->getMessage()]);
        }
    }

    public function editUser(User $user)
    {
        $permissions = SuperAdmin::getAvailablePermissions();
        return view('super-admin.users.edit', compact('user', 'permissions'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
            'super_admin_role' => 'required_if:is_super_admin,true',
            'super_admin_permissions' => 'array',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'is_admin' => $validated['is_admin'] ?? false,
            ]);

            if (!empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            // Handle super admin status
            if ($validated['is_super_admin'] ?? false) {
                $superAdmin = $user->superAdmin;
                if ($superAdmin) {
                    $superAdmin->update([
                        'role' => $validated['super_admin_role'],
                        'permissions' => $validated['super_admin_permissions'] ?? [],
                        'is_active' => true,
                    ]);
                } else {
                    SuperAdmin::create([
                        'user_id' => $user->id,
                        'role' => $validated['super_admin_role'],
                        'permissions' => $validated['super_admin_permissions'] ?? [],
                        'is_active' => true,
                    ]);
                }
            } else {
                // Remove super admin status
                $user->superAdmin?->update(['is_active' => false]);
            }

            DB::commit();
            return redirect()->route('super-admin.users.index')->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating user: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error updating user: ' . $e->getMessage()]);
        }
    }

    /**
     * Business Management
     */
    public function businesses(Request $request)
    {
        $query = BusinessProfile::with(['user', 'sites']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by verification status
        if ($request->has('verification_status')) {
            $query->where('is_verified', $request->verification_status === 'verified');
        }

        $businesses = $query->latest()->paginate(15);

        return view('super-admin.businesses.index', compact('businesses'));
    }

    public function showBusiness(BusinessProfile $business)
    {
        $business->load(['user', 'sites.transactions', 'transfers', 'beneficiaries']);
        
        // Calculate statistics
        $stats = [
            'total_sites' => $business->sites->count(),
            'active_sites' => $business->sites->where('is_active', true)->count(),
            'total_transactions' => $business->sites->flatMap->transactions->count(),
            'total_revenue' => $business->sites->flatMap->transactions->where('status', 'success')->sum('amount'),
            'total_withdrawals' => $business->transfers->where('is_approved', true)->sum('amount'),
            'pending_withdrawals' => $business->transfers->where('is_approved', false)->sum('amount'),
        ];

        return view('super-admin.businesses.show', compact('business', 'stats'));
    }

    public function updateBusinessBalance(Request $request, BusinessProfile $business)
    {
        $validated = $request->validate([
            'actual_balance' => 'required|numeric|min:0',
            'withdrawable_balance' => 'required|numeric|min:0',
            'balance' => 'required|numeric|min:0',
            'balance_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $business->update([
                'actual_balance' => $validated['actual_balance'],
                'withdrawable_balance' => $validated['withdrawable_balance'],
                'balance' => $validated['balance'],
                'balance_notes' => $validated['balance_notes'],
                'last_balance_update' => now(),
            ]);

            // Log the balance update
            Log::info('Business balance updated by super admin', [
                'business_id' => $business->id,
                'business_name' => $business->business_name,
                'actual_balance' => $validated['actual_balance'],
                'withdrawable_balance' => $validated['withdrawable_balance'],
                'balance' => $validated['balance'],
                'updated_by' => auth()->id(),
                'notes' => $validated['balance_notes'],
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Business balance updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating business balance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating business balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Withdrawal Management
     */
    public function withdrawals(Request $request)
    {
        $query = Transfer::with(['businessProfile.user', 'beneficiary']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('recipient_account_name', 'like', "%{$search}%")
                  ->orWhereHas('businessProfile.user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        $withdrawals = $query->latest()->paginate(15);

        return view('super-admin.withdrawals.index', compact('withdrawals'));
    }

    public function showWithdrawal(Transfer $withdrawal)
    {
        $withdrawal->load(['businessProfile.user', 'beneficiary']);
        
        // Get business balance information
        $business = $withdrawal->businessProfile;
        $balanceInfo = [
            'withdrawable_balance' => $business->withdrawable_balance ?? 0,
            'actual_balance' => $business->actual_balance ?? 0,
            'total_balance' => $business->balance ?? 0,
            'withdrawal_amount' => $withdrawal->amount,
            'can_approve' => ($business->withdrawable_balance ?? 0) >= $withdrawal->amount,
            'balance_shortfall' => max(0, $withdrawal->amount - ($business->withdrawable_balance ?? 0))
        ];
        
        return view('super-admin.withdrawals.show', compact('withdrawal', 'balanceInfo'));
    }

    public function approveWithdrawal(Request $request, Transfer $withdrawal)
    {
        $validated = $request->validate([
            'processing_method' => 'required|string|in:bank_transfer,manual_transfer,other',
            'admin_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Check if business has sufficient withdrawable balance
            $business = $withdrawal->businessProfile;
            
            // Get current balances
            $withdrawableBalance = $business->withdrawable_balance ?? 0;
            $actualBalance = $business->actual_balance ?? 0;
            $totalBalance = $business->balance ?? 0;
            $withdrawalAmount = $withdrawal->amount;
            
            // Check if business has sufficient balance
            if ($withdrawableBalance < $withdrawalAmount) {
                $errorMessage = "Insufficient withdrawable balance. " .
                    "Requested: ₦" . number_format($withdrawalAmount, 2) . ", " .
                    "Available: ₦" . number_format($withdrawableBalance, 2) . ". " .
                    "Business balances - Actual: ₦" . number_format($actualBalance, 2) . 
                    ", Total: ₦" . number_format($totalBalance, 2);
                
                throw new \Exception($errorMessage);
            }

            // Update withdrawal
            $withdrawal->update([
                'is_approved' => true,
                'processed_by' => auth()->user()->name,
                'admin_notes' => $validated['admin_notes'],
                'processed_at' => now(),
                'processing_method' => $validated['processing_method'],
            ]);

            // Update business balances
            $business->decrement('withdrawable_balance', $withdrawalAmount);
            $business->increment('total_withdrawals', $withdrawalAmount);

            // Log the approval
            Log::info('Withdrawal approved by super admin', [
                'withdrawal_id' => $withdrawal->id,
                'amount' => $withdrawalAmount,
                'business_id' => $business->id,
                'business_name' => $business->business_name,
                'withdrawable_balance_before' => $withdrawableBalance,
                'withdrawable_balance_after' => $withdrawableBalance - $withdrawalAmount,
                'approved_by' => auth()->id(),
                'processing_method' => $validated['processing_method'],
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Withdrawal approved successfully. ' .
                    'Withdrawable balance reduced from ₦' . number_format($withdrawableBalance, 2) . 
                    ' to ₦' . number_format($withdrawableBalance - $withdrawalAmount, 2)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error approving withdrawal: ' . $e->getMessage(), [
                'withdrawal_id' => $withdrawal->id,
                'business_id' => $withdrawal->business_profile_id,
                'amount' => $withdrawal->amount,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error approving withdrawal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectWithdrawal(Request $request, Transfer $withdrawal)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $withdrawal->update([
                'is_approved' => false,
                'processed_by' => auth()->user()->name,
                'admin_notes' => $validated['admin_notes'],
                'processed_at' => now(),
                'processing_method' => 'rejected',
            ]);

            // Log the rejection
            Log::info('Withdrawal rejected by super admin', [
                'withdrawal_id' => $withdrawal->id,
                'amount' => $withdrawal->amount,
                'business_id' => $withdrawal->business_profile_id,
                'rejected_by' => auth()->id(),
                'reason' => $validated['admin_notes'],
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Withdrawal rejected successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error rejecting withdrawal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting withdrawal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ticket Management
     */
    public function tickets(Request $request)
    {
        $query = Ticket::with(['user', 'businessProfile', 'assignedTo']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->latest()->paginate(15);

        return view('super-admin.tickets.index', compact('tickets'));
    }

    public function showTicket(Ticket $ticket)
    {
        $ticket->load(['user', 'businessProfile', 'assignedTo', 'messages.user']);
        return view('super-admin.tickets.show', compact('ticket'));
    }

    public function assignTicket(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $ticket->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket assigned successfully'
        ]);
    }

    public function updateTicketStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'resolution_notes' => 'nullable|string',
        ]);

        $ticket->update([
            'status' => $validated['status'],
            'resolution_notes' => $validated['resolution_notes'],
            'resolved_at' => $validated['status'] === 'resolved' ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket status updated successfully'
        ]);
    }

    public function replyToTicket(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'is_support' => true,
        ]);

        // Update ticket status to in_progress if it was open
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reply added successfully'
        ]);
    }

    /**
     * Reports and Analytics
     */
    public function reports()
    {
        // Overall statistics
        $overallStats = [
            'total_users' => User::count(),
            'total_businesses' => BusinessProfile::count(),
            'total_revenue' => Transaction::where('status', 'success')->sum('amount'),
            'total_withdrawals' => Transfer::where('is_approved', true)->sum('amount'),
            'pending_withdrawals' => Transfer::where('is_approved', false)->sum('amount'),
            'open_tickets' => Ticket::whereIn('status', ['open', 'in_progress'])->count(),
        ];

        // Monthly revenue data
        $monthlyRevenue = Transaction::where('status', 'success')
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::create()->month($item->month)->format('M') => $item->total];
            });

        // Recent activities
        $recentActivities = collect();
        
        // Recent transactions
        $recentTransactions = Transaction::with(['businessProfile.user', 'site'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($transaction) {
                return [
                    'type' => 'transaction',
                    'title' => 'New transaction: ₦' . number_format($transaction->amount, 2),
                    'description' => $transaction->businessProfile->business_name . ' - ' . $transaction->site->name,
                    'time' => $transaction->created_at,
                    'status' => $transaction->status,
                ];
            });

        // Recent withdrawals
        $recentWithdrawals = Transfer::with(['businessProfile.user'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($withdrawal) {
                return [
                    'type' => 'withdrawal',
                    'title' => 'Withdrawal request: ₦' . number_format($withdrawal->amount, 2),
                    'description' => $withdrawal->businessProfile->business_name,
                    'time' => $withdrawal->created_at,
                    'status' => $withdrawal->is_approved ? 'approved' : 'pending',
                ];
            });

        // Recent tickets
        $recentTickets = Ticket::with(['user', 'businessProfile'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($ticket) {
                return [
                    'type' => 'ticket',
                    'title' => 'New ticket: ' . $ticket->subject,
                    'description' => $ticket->user->name . ' - ' . $ticket->businessProfile->business_name,
                    'time' => $ticket->created_at,
                    'status' => $ticket->status,
                ];
            });

        $recentActivities = $recentTransactions->concat($recentWithdrawals)->concat($recentTickets)
            ->sortByDesc('time')
            ->take(20);

        return view('super-admin.reports.index', compact('overallStats', 'monthlyRevenue', 'recentActivities'));
    }
} 