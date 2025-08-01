<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Site;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Transfer;

class DashboardController extends Controller
{
    public function index()
    {
        $businessProfile = auth()->user()->businessProfile;
        
        if (!$businessProfile) {
            return view('dashboard', [
                'businessProfile' => null,
                'sites' => collect(),
                'recentTransactions' => collect(),
                'recentTransactionsCount' => 0,
                'totalBalance' => 0,
                'dailyRevenue' => 0,
                'activeSites' => 0,
                'totalSites' => 0,
                'weeklyData' => collect(),
                'monthlyData' => collect(),
                'transactionTypes' => collect(),
                'beneficiaries' => collect(),
                'transfers' => collect(),
                'transactions' => collect()
            ]);
        }

        // Get active sites with their daily revenue (exclude archived sites from main listing)
        $sites = Site::where('business_profile_id', $businessProfile->id)
            ->where('is_archived', false) // Only show non-archived sites
            ->with(['transactions' => function ($query) {
                $query->whereDate('created_at', Carbon::today());
            }])
            ->get()
            ->map(function ($site) {
                $site->daily_revenue = $site->transactions->where('status', 'success')->sum('amount');
                return $site;
            });

        // Get recent transactions
        $recentTransactions = Transaction::where('business_profile_id', $businessProfile->id)
            ->with('site')
            ->whereHas('site') // Only include transactions with valid sites
            ->latest()
            ->take(5)
            ->get();

        // Get paginated transactions for the dashboard tab
        $paginatedTransactions = Transaction::where('business_profile_id', $businessProfile->id)
            ->with('site')
            ->whereHas('site') // Only include transactions with valid sites
            ->latest()
            ->paginate(10);

        // Get all transactions for count (keep this for statistics)
        $transactions = Transaction::where('business_profile_id', $businessProfile->id)
            ->with('site')
            ->get();

        // Get beneficiaries
        $beneficiaries = $businessProfile->beneficiaries;

        // Get transfers
        $transfers = Transfer::where('business_profile_id', $businessProfile->id)
            ->with('beneficiary')
            ->latest()
            ->take(10)
            ->get();

        // Calculate weekly performance data
        $weeklyData = Transaction::where('business_profile_id', $businessProfile->id)
            ->where('status', 'success')
            ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('D'),
                    'amount' => $item->total_amount ?? 0
                ];
            });
        $weeklyData = collect($weeklyData);

        // Calculate monthly revenue data (Fixed for MySQL)
        $monthlyData = Transaction::where('business_profile_id', $businessProfile->id)
            ->where('status', 'success')
            ->whereYear('created_at', Carbon::now()->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $monthNumber = (int)$item->month;
                // Validate month number is between 1 and 12
                if ($monthNumber < 1 || $monthNumber > 12) {
                    $monthNumber = 1; // Default to January if invalid
                }
                return [
                    'month' => Carbon::create(2024, $monthNumber, 1)->format('M'),
                    'amount' => $item->total_amount ?? 0
                ];
            });
        $monthlyData = collect($monthlyData);

        // Calculate transaction types distribution
        $transactionTypes = Transaction::where('business_profile_id', $businessProfile->id)
            ->select('payment_method as type', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => ucfirst($item->type ?? 'Unknown'),
                    'count' => $item->count
                ];
            });

        // Calculate comprehensive statistics
        $totalRevenue = $transactions->where('status', 'success')->sum('amount');
        $monthlyRevenue = $transactions->where('status', 'success')->where('created_at', '>=', Carbon::now()->startOfMonth())->sum('amount');
        
        // Withdrawal statistics
        $totalWithdrawals = Transfer::where('business_profile_id', $businessProfile->id)
            ->where('type', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');
        $pendingWithdrawals = Transfer::where('business_profile_id', $businessProfile->id)
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->sum('amount');

        // Transaction status statistics
        $successfulTransactions = $transactions->where('status', 'success')->count();
        $pendingTransactions = $transactions->where('status', 'pending')->count();
        $failedTransactions = $transactions->where('status', 'failed')->count();
        $initiatedTransactions = $transactions->where('status', 'initiated')->count();

        // Update sites with monthly revenue
        $sites = $sites->map(function ($site) {
            $site->monthly_revenue = $site->transactions->where('status', 'success')->where('created_at', '>=', Carbon::now()->startOfMonth())->sum('amount');
            return $site;
        });

        return view('dashboard', [
            'businessProfile' => $businessProfile,
            'sites' => $sites,
            'recentTransactions' => $recentTransactions,
            'recentTransactionsCount' => $successfulTransactions,
            'totalBalance' => $businessProfile->balance,
            'dailyRevenue' => $sites->sum('daily_revenue'),
            'activeSites' => $sites->where('is_active', true)->count(),
            'totalSites' => $sites->count(),
            'weeklyData' => $weeklyData,
            'monthlyData' => $monthlyData,
            'transactionTypes' => $transactionTypes,
            'beneficiaries' => $beneficiaries,
            'transfers' => $transfers,
            'transactions' => $transactions,
            'paginatedTransactions' => $paginatedTransactions,
            // Additional statistics
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'totalWithdrawals' => $totalWithdrawals,
            'pendingWithdrawals' => $pendingWithdrawals,
            'successfulTransactions' => $successfulTransactions,
            'pendingTransactions' => $pendingTransactions,
            'failedTransactions' => $failedTransactions,
            'initiatedTransactions' => $initiatedTransactions
        ]);
    }

    // API Methods
    public function generateApiToken()
    {
        $user = auth()->user();
        $token = \Str::random(60);
        
        $user->update(['api_token' => $token]);
        
        return response()->json([
            'success' => true,
            'token' => $token,
            'message' => 'API token generated successfully!'
        ]);
    }

    public function getSites(Request $request)
    {
        // Check if request expects JSON (API call)
        if ($request->expectsJson()) {
            $user = auth()->user();
            $businessProfile = $user->businessProfile;
            
            if (!$businessProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business profile not found'
                ], 404);
            }

            $sites = Site::where('business_profile_id', $businessProfile->id)
                ->with(['transactions' => function ($query) {
                    $query->whereDate('created_at', Carbon::today());
                }])
                ->get()
                ->map(function ($site) {
                    $site->daily_revenue = $site->transactions->where('status', 'success')->sum('amount');
                    $site->monthly_revenue = $site->transactions->where('status', 'success')->where('created_at', '>=', Carbon::now()->startOfMonth())->sum('amount');
                    return $site;
                });

            return response()->json([
                'success' => true,
                'data' => $sites
            ]);
        }

        // Regular web request - redirect to dashboard
        return redirect()->route('dashboard');
    }

    public function getTransactions(Request $request)
    {
        if ($request->expectsJson()) {
            $user = auth()->user();
            $businessProfile = $user->businessProfile;
            
            if (!$businessProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business profile not found'
                ], 404);
            }

            $query = Transaction::where('business_profile_id', $businessProfile->id)
                ->with('site');

            // Apply filters
            if ($request->has('site_id')) {
                $query->where('site_id', $request->site_id);
            }

            if ($request->has('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $transactions = $query->latest()->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'total' => $transactions->total(),
                    'per_page' => $transactions->perPage(),
                    'last_page' => $transactions->lastPage()
                ]
            ]);
        }

        return redirect()->route('dashboard');
    }

    public function getDashboardTransactions(Request $request)
    {
        $user = auth()->user();
        $businessProfile = $user->businessProfile;
        
        if (!$businessProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Business profile not found'
            ], 404);
        }

        $query = Transaction::where('business_profile_id', $businessProfile->id)
            ->with('site');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        // Apply site filter
        if ($request->has('site') && !empty($request->site) && $request->site !== 'All Sites') {
            $query->where('site_id', $request->site);
        }

        // Apply type filter
        if ($request->has('type') && !empty($request->type) && $request->type !== 'All Types') {
            $query->where('payment_method', $request->type);
        }

        $transactions = $query->latest()->paginate(10);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'total' => $transactions->total(),
                    'per_page' => $transactions->perPage(),
                    'last_page' => $transactions->lastPage(),
                    'from' => $transactions->firstItem(),
                    'to' => $transactions->lastItem()
                ]
            ]);
        }

        return view('dashboard', [
            'paginatedTransactions' => $transactions
        ]);
    }

    public function getRevenue(Request $request)
    {
        if ($request->expectsJson()) {
            $user = auth()->user();
            $businessProfile = $user->businessProfile;
            
            if (!$businessProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business profile not found'
                ], 404);
            }

            $sites = Site::where('business_profile_id', $businessProfile->id);
            
            $data = [
                'total_balance' => $businessProfile->balance ?? 0,
                'daily_revenue' => Transaction::where('business_profile_id', $businessProfile->id)
                    ->whereDate('created_at', Carbon::today())
                    ->sum('amount'),
                'monthly_revenue' => Transaction::where('business_profile_id', $businessProfile->id)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->sum('amount'),
                'total_sites' => $sites->count(),
                'active_sites' => $sites->where('is_active', true)->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        return redirect()->route('dashboard');
    }

    public function webhookTransaction(Request $request)
    {
        // Handle webhook transactions from external systems
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'reference' => 'required|string|unique:transactions,reference',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'status' => 'required|in:pending,success,failed',
            'payment_method' => 'nullable|string',
            'customer_email' => 'nullable|email',
            'customer_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $transaction = Transaction::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'transaction_id' => $transaction->id
        ]);
    }
}
