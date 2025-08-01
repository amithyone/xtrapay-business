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

        // Get date range (default to last 30 days)
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Get statistics
        $stats = $this->getStatistics($siteIds, $dateFrom, $dateTo);
        
        // Get chart data
        $chartData = $this->getChartData($siteIds, $dateFrom, $dateTo);
        
        // Get site performance
        $sitePerformance = $this->getSitePerformance($siteIds, $dateFrom, $dateTo);

        return view('statistics.index', compact('stats', 'chartData', 'sitePerformance', 'sites', 'dateFrom', 'dateTo'));
    }

    /**
     * Get comprehensive statistics
     */
    private function getStatistics($siteIds, $dateFrom, $dateTo)
    {
        $startDate = Carbon::parse($dateFrom);
        $endDate = Carbon::parse($dateTo);

        $query = Transaction::whereIn('site_id', $siteIds)
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()]);

        $totalTransactions = $query->count();
        $totalAmount = $query->sum('amount');
        $successfulTransactions = $query->where('status', 'success')->count();
        $successfulAmount = $query->where('status', 'success')->sum('amount');
        $pendingTransactions = $query->where('status', 'pending')->count();
        $pendingAmount = $query->where('status', 'pending')->sum('amount');
        $failedTransactions = $query->where('status', 'failed')->count();

        // Calculate averages
        $averageTransactionValue = $totalTransactions > 0 ? $totalAmount / $totalTransactions : 0;
        $successRate = $totalTransactions > 0 ? ($successfulTransactions / $totalTransactions) * 100 : 0;

        // Get daily averages
        $daysInRange = $startDate->diffInDays($endDate) + 1;
        $dailyAverageTransactions = $daysInRange > 0 ? $totalTransactions / $daysInRange : 0;
        $dailyAverageAmount = $daysInRange > 0 ? $totalAmount / $daysInRange : 0;

        return [
            'total_transactions' => $totalTransactions,
            'total_amount' => $totalAmount,
            'successful_transactions' => $successfulTransactions,
            'successful_amount' => $successfulAmount,
            'pending_transactions' => $pendingTransactions,
            'pending_amount' => $pendingAmount,
            'failed_transactions' => $failedTransactions,
            'average_transaction_value' => $averageTransactionValue,
            'success_rate' => $successRate,
            'daily_average_transactions' => $dailyAverageTransactions,
            'daily_average_amount' => $dailyAverageAmount,
            'date_range' => [
                'from' => $startDate->format('M d, Y'),
                'to' => $endDate->format('M d, Y'),
                'days' => $daysInRange
            ]
        ];
    }

    /**
     * Get chart data for various charts
     */
    private function getChartData($siteIds, $dateFrom, $dateTo)
    {
        $startDate = Carbon::parse($dateFrom);
        $endDate = Carbon::parse($dateTo);

        // Daily revenue chart
        $dailyRevenue = Transaction::whereIn('site_id', $siteIds)
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total_amount, COUNT(*) as transaction_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'amount' => $item->total_amount,
                    'count' => $item->transaction_count
                ];
            });

        // Payment method distribution
        $paymentMethods = Transaction::whereIn('site_id', $siteIds)
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('payment_method')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return [
                    'method' => ucfirst($item->payment_method),
                    'count' => $item->count,
                    'amount' => $item->total_amount
                ];
            });

        // Status distribution
        $statusDistribution = Transaction::whereIn('site_id', $siteIds)
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => ucfirst($item->status),
                    'count' => $item->count
                ];
            });

        // Hourly distribution
        $hourlyDistribution = Transaction::whereIn('site_id', $siteIds)
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function ($item) {
                return [
                    'hour' => $item->hour . ':00',
                    'count' => $item->count
                ];
            });

        return [
            'daily_revenue' => $dailyRevenue,
            'payment_methods' => $paymentMethods,
            'status_distribution' => $statusDistribution,
            'hourly_distribution' => $hourlyDistribution
        ];
    }

    /**
     * Get site performance data
     */
    private function getSitePerformance($siteIds, $dateFrom, $dateTo)
    {
        $startDate = Carbon::parse($dateFrom);
        $endDate = Carbon::parse($dateTo);

        return Site::whereIn('id', $siteIds)
            ->with(['transactions' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate->endOfDay()]);
            }])
            ->get()
            ->map(function ($site) {
                $transactions = $site->transactions;
                $totalAmount = $transactions->sum('amount');
                $successfulAmount = $transactions->where('status', 'success')->sum('amount');
                $successRate = $transactions->count() > 0 
                    ? ($transactions->where('status', 'success')->count() / $transactions->count()) * 100 
                    : 0;

                return [
                    'id' => $site->id,
                    'name' => $site->name,
                    'url' => $site->url,
                    'total_transactions' => $transactions->count(),
                    'total_amount' => $totalAmount,
                    'successful_amount' => $successfulAmount,
                    'success_rate' => $successRate,
                    'is_active' => $site->is_active
                ];
            })
            ->sortByDesc('total_amount');
    }

    /**
     * Get AJAX data for charts
     */
    public function getChartDataAjax(Request $request)
    {
        $user = Auth::user();
        $businessProfile = $user->businessProfile;
        
        if (!$businessProfile) {
            return response()->json(['error' => 'No business profile found'], 404);
        }

        $siteIds = $businessProfile->sites->pluck('id');
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $chartData = $this->getChartData($siteIds, $dateFrom, $dateTo);

        return response()->json($chartData);
    }
} 