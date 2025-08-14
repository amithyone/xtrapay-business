<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Site;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Display the statistics page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $businessProfile = $user->businessProfile;
        
        if (!$businessProfile) {
            return redirect()->route('business-profile.create')
                ->with('error', 'Please create a business profile first.');
        }

        $sites = $businessProfile->sites;
        $siteIds = $sites->pluck('id');

        // Get date range (default to last 7 days) - using Nigerian time
        $dateFrom = $request->get('date_from', Carbon::now()->setTimezone('Africa/Lagos')->subDays(7)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->setTimezone('Africa/Lagos')->format('Y-m-d'));

        // Get statistics
        $stats = $this->getStatistics($siteIds, $dateFrom, $dateTo);
        
        // Get chart data
        $chartData = $this->getChartData($siteIds, $dateFrom, $dateTo);
        
        // Get payment method summary
        $paymentMethodSummary = $this->getPaymentMethodSummary($siteIds, $dateFrom, $dateTo);

        // Debug: Check total transactions in database
        $totalTransactionsInDB = Transaction::count();
        $totalSuccessfulInDB = Transaction::where('status', 'success')->count();
        
        \Log::info('Database Transaction Summary', [
            'total_transactions' => $totalTransactionsInDB,
            'total_successful' => $totalSuccessfulInDB,
            'user_sites_count' => $siteIds->count(),
            'date_range' => [$dateFrom, $dateTo]
        ]);

        return view('statistics.index', compact('stats', 'chartData', 'sites', 'dateFrom', 'dateTo', 'paymentMethodSummary'));
    }

    /**
     * Get comprehensive statistics
     */
    private function getStatistics($siteIds, $dateFrom, $dateTo)
    {
        // Convert to Nigerian timezone for accurate daily calculations
        $startDate = Carbon::parse($dateFrom)->setTimezone('Africa/Lagos')->startOfDay();
        $endDate = Carbon::parse($dateTo)->setTimezone('Africa/Lagos')->endOfDay();
        $today = Carbon::now()->setTimezone('Africa/Lagos')->startOfDay();

        // Today's statistics
        $dailySuccessfulTransactions = Transaction::whereIn('site_id', $siteIds)
            ->where('status', 'success')
            ->whereDate('created_at', $today->format('Y-m-d'))
            ->count();

        // Log for debugging
        \Log::info('Daily Statistics Calculation', [
            'site_ids' => $siteIds,
            'today_date' => $today->format('Y-m-d'),
            'daily_successful_transactions' => $dailySuccessfulTransactions,
            'query' => Transaction::whereIn('site_id', $siteIds)
                ->where('status', 'success')
                ->whereDate('created_at', $today->format('Y-m-d'))
                ->toSql()
        ]);

        $dailyPendingTransactions = Transaction::whereIn('site_id', $siteIds)
            ->where('status', 'pending')
            ->whereDate('created_at', $today->format('Y-m-d'))
            ->count();

        $dailyTotalTransactions = $dailySuccessfulTransactions + $dailyPendingTransactions;
        $dailySuccessRate = $dailyTotalTransactions > 0 ? ($dailySuccessfulTransactions / $dailyTotalTransactions) * 100 : 0;

        $dailyTotalProcessed = Transaction::whereIn('site_id', $siteIds)
            ->whereDate('created_at', $today->format('Y-m-d'))
            ->sum('amount');

        // Monthly successful transactions (current month)
        $currentMonth = Carbon::now()->setTimezone('Africa/Lagos')->startOfMonth();
        $monthlySuccessfulTransactions = Transaction::whereIn('site_id', $siteIds)
            ->where('status', 'success')
            ->whereBetween('created_at', [$currentMonth, Carbon::now()->setTimezone('Africa/Lagos')])
            ->count();

        // Monthly total processed amount
        $monthlyTotalProcessed = Transaction::whereIn('site_id', $siteIds)
            ->whereBetween('created_at', [$currentMonth, Carbon::now()->setTimezone('Africa/Lagos')])
            ->sum('amount');

        return [
            'daily_successful_transactions' => $dailySuccessfulTransactions,
            'daily_pending_transactions' => $dailyPendingTransactions,
            'daily_success_rate' => $dailySuccessRate,
            'daily_total_processed' => $dailyTotalProcessed,
            'monthly_successful_transactions' => $monthlySuccessfulTransactions,
            'monthly_total_processed' => $monthlyTotalProcessed,
            'date_range' => [
                'from' => $startDate->format('M d, Y'),
                'to' => $endDate->format('M d, Y')
            ]
        ];
    }

    /**
     * Get chart data
     */
    private function getChartData($siteIds, $dateFrom, $dateTo)
    {
        // Convert to Nigerian timezone for accurate daily calculations
        $startDate = Carbon::parse($dateFrom)->setTimezone('Africa/Lagos')->startOfDay();
        $endDate = Carbon::parse($dateTo)->setTimezone('Africa/Lagos')->endOfDay();
        $today = Carbon::now()->setTimezone('Africa/Lagos')->startOfDay();

        // Transaction distribution for today (pie chart)
        $transactionDistribution = Transaction::whereIn('site_id', $siteIds)
            ->whereDate('created_at', $today->format('Y-m-d'))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => ucfirst($item->status),
                    'count' => $item->count
                ];
            });

        // Pending vs Successful daily comparison (last 7 days)
        $pendingVsSuccessful = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->setTimezone('Africa/Lagos')->subDays($i)->startOfDay();
            
            $successful = Transaction::whereIn('site_id', $siteIds)
                ->where('status', 'success')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->count();

            $pending = Transaction::whereIn('site_id', $siteIds)
                ->where('status', 'pending')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->count();

            $pendingVsSuccessful->push([
                'date' => $date->format('M d'),
                'successful' => $successful,
                'pending' => $pending
            ]);
        }

        // Daily payment method processing (today)
        $dailyPaymentMethods = Transaction::whereIn('site_id', $siteIds)
            ->whereDate('created_at', $today->format('Y-m-d'))
            ->selectRaw('payment_method, SUM(amount) as total_amount')
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'method' => ucfirst($item->payment_method),
                    'amount' => $item->total_amount
                ];
            });

        // Weekly payment method processing (past 7 days)
        $weeklyPaymentMethods = $this->getWeeklyPaymentMethodData($siteIds);

        return [
            'transaction_distribution' => $transactionDistribution,
            'pending_vs_successful' => $pendingVsSuccessful,
            'daily_payment_methods' => $dailyPaymentMethods,
            'weekly_payment_methods' => $weeklyPaymentMethods
        ];
    }

    /**
     * Get weekly payment method data
     */
    private function getWeeklyPaymentMethodData($siteIds)
    {
        $methods = ['payvibe', 'xtrapay'];
        $datasets = [];
        $labels = [];

        // Generate labels for past 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->setTimezone('Africa/Lagos')->subDays($i);
            $labels[] = $date->format('M d');
        }

        // Get data for each payment method
        foreach ($methods as $index => $method) {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->setTimezone('Africa/Lagos')->subDays($i)->startOfDay();
                
                $amount = Transaction::whereIn('site_id', $siteIds)
                    ->where('payment_method', $method)
                    ->whereDate('created_at', $date->format('Y-m-d'))
                    ->sum('amount');

                $data[] = $amount;
            }

            $datasets[] = [
                'label' => ucfirst($method),
                'data' => $data,
                'borderColor' => $index === 0 ? '#3b82f6' : '#10b981',
                'backgroundColor' => $index === 0 ? '#3b82f620' : '#10b98120',
                'tension' => 0.4,
                'fill' => false
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }

    /**
     * Get payment method summary
     */
    private function getPaymentMethodSummary($siteIds, $dateFrom, $dateTo)
    {
        $today = Carbon::now()->setTimezone('Africa/Lagos')->startOfDay();
        $weekAgo = Carbon::now()->setTimezone('Africa/Lagos')->subDays(7)->startOfDay();

        $methods = [
            'payvibe' => ['name' => 'PayVibe', 'icon' => 'fa-credit-card'],
            'xtrapay' => ['name' => 'XtraPay', 'icon' => 'fa-wallet']
        ];

        $summary = [];

        foreach ($methods as $methodKey => $methodInfo) {
            // Today's data
            $todayTransactions = Transaction::whereIn('site_id', $siteIds)
                ->where('payment_method', $methodKey)
                ->whereDate('created_at', $today->format('Y-m-d'))
                ->count();

            $todayAmount = Transaction::whereIn('site_id', $siteIds)
                ->where('payment_method', $methodKey)
                ->whereDate('created_at', $today->format('Y-m-d'))
                ->sum('amount');

            // Past 7 days data
            $weekTransactions = Transaction::whereIn('site_id', $siteIds)
                ->where('payment_method', $methodKey)
                ->whereBetween('created_at', [$weekAgo, $today])
                ->count();

            $weekAmount = Transaction::whereIn('site_id', $siteIds)
                ->where('payment_method', $methodKey)
                ->whereBetween('created_at', [$weekAgo, $today])
                ->sum('amount');

            // Success rate
            $totalTransactions = Transaction::whereIn('site_id', $siteIds)
                ->where('payment_method', $methodKey)
                ->whereBetween('created_at', [$weekAgo, $today])
                ->count();

            $successfulTransactions = Transaction::whereIn('site_id', $siteIds)
                ->where('payment_method', $methodKey)
                ->where('status', 'success')
                ->whereBetween('created_at', [$weekAgo, $today])
                ->count();

            $successRate = $totalTransactions > 0 ? ($successfulTransactions / $totalTransactions) * 100 : 0;

            $summary[] = [
                'name' => $methodInfo['name'],
                'icon' => $methodInfo['icon'],
                'today_transactions' => $todayTransactions,
                'today_amount' => $todayAmount,
                'week_transactions' => $weekTransactions,
                'week_amount' => $weekAmount,
                'success_rate' => $successRate
            ];
        }

        return $summary;
    }

    /**
     * Get chart data via AJAX
     */
    public function getChartDataAjax(Request $request)
    {
        $user = Auth::user();
        $businessProfile = $user->businessProfile;
        
        if (!$businessProfile) {
            return response()->json(['error' => 'Business profile not found'], 404);
        }

        $sites = $businessProfile->sites;
        $siteIds = $sites->pluck('id');

        $dateFrom = $request->get('date_from', Carbon::now()->setTimezone('Africa/Lagos')->subDays(7)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->setTimezone('Africa/Lagos')->format('Y-m-d'));

        $chartData = $this->getChartData($siteIds, $dateFrom, $dateTo);

        return response()->json($chartData);
    }
} 