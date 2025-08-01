<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Site;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
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
        
        // Build query
        $query = Transaction::whereIn('site_id', $sites->pluck('id'))
            ->with(['site', 'businessProfile']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get statistics
        $stats = $this->getTransactionStats($sites->pluck('id'));
        
        // Paginate results
        $transactions = $query->latest()->paginate(20);

        // For AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'transactions' => $transactions,
                'stats' => $stats
            ]);
        }

        return view('transactions.index', compact('transactions', 'sites', 'stats'));
    }

    /**
     * Get transaction statistics
     */
    private function getTransactionStats($siteIds)
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfDay = $now->copy()->startOfDay();

        // Get site distribution data for pie chart
        $siteDistribution = Transaction::whereIn('site_id', $siteIds)
            ->where('status', 'success')
            ->with('site')
            ->get()
            ->groupBy('site_id')
            ->map(function ($transactions, $siteId) {
                $site = $transactions->first()->site;
                return [
                    'site_name' => $site ? $site->name : 'Unknown Site',
                    'total_amount' => $transactions->sum('amount'),
                    'transaction_count' => $transactions->count(),
                    'site_id' => $siteId
                ];
            })
            ->values()
            ->toArray();

        return [
            'total_transactions' => Transaction::whereIn('site_id', $siteIds)->count(),
            'total_amount' => Transaction::whereIn('site_id', $siteIds)->sum('amount'),
            'successful_amount' => Transaction::whereIn('site_id', $siteIds)
                ->where('status', 'success')->sum('amount'),
            'monthly_transactions' => Transaction::whereIn('site_id', $siteIds)
                ->where('created_at', '>=', $startOfMonth)->count(),
            'monthly_amount' => Transaction::whereIn('site_id', $siteIds)
                ->where('status', 'success')
                ->where('created_at', '>=', $startOfMonth)->sum('amount'),
            'daily_transactions' => Transaction::whereIn('site_id', $siteIds)
                ->where('created_at', '>=', $startOfDay)->count(),
            'daily_amount' => Transaction::whereIn('site_id', $siteIds)
                ->where('status', 'success')
                ->where('created_at', '>=', $startOfDay)->sum('amount'),
            'successful_transactions' => Transaction::whereIn('site_id', $siteIds)
                ->where('status', 'success')->count(),
            'pending_transactions' => Transaction::whereIn('site_id', $siteIds)
                ->where('status', 'pending')->count(),
            'failed_transactions' => Transaction::whereIn('site_id', $siteIds)
                ->where('status', 'failed')->count(),
            'site_distribution' => $siteDistribution,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sites = Auth::user()->businessProfile->sites;
        return view('transactions.create', compact('sites'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'payment_method' => 'required|string',
            'customer_email' => 'nullable|email',
            'customer_name' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ]);

        $validated['reference'] = 'TRX-' . strtoupper(uniqid());
        $validated['status'] = 'pending';

        $transaction = Transaction::create($validated);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        try {
            // Load the site relationship
            $transaction->load('site');
            
            Gate::authorize('view', $transaction);
            
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'transaction' => [
                        'id' => $transaction->id,
                        'reference' => $transaction->reference,
                        'external_id' => $transaction->external_id,
                        'site_id' => $transaction->site_id,
                        'site' => $transaction->site ? [
                            'id' => $transaction->site->id,
                            'name' => $transaction->site->name,
                            'url' => $transaction->site->url
                        ] : null,
                        'amount' => $transaction->amount,
                        'currency' => $transaction->currency,
                        'status' => $transaction->status,
                        'payment_method' => $transaction->payment_method,
                        'customer_email' => $transaction->customer_email,
                        'customer_name' => $transaction->customer_name,
                        'metadata' => $transaction->metadata,
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->updated_at
                    ]
                ]);
            }
            
            return view('transactions.show', compact('transaction'));
        } catch (\Exception $e) {
            Log::error('Transaction show error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'exception' => $e
            ]);
            
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'error' => 'Unable to load transaction details: ' . $e->getMessage()
                ], 500);
            }
            
            throw $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        Gate::authorize('update', $transaction);
        $sites = Auth::user()->businessProfile->sites;
        return view('transactions.edit', compact('transaction', 'sites'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        Gate::authorize('update', $transaction);

        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'status' => 'required|in:pending,success,failed',
            'payment_method' => 'required|string',
            'customer_email' => 'nullable|email',
            'customer_name' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        Gate::authorize('delete', $transaction);
        
        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    public function fetchAndSaveTransactions()
    {
        $sites = Site::where('is_active', true)->get();
        $now = now();

        foreach ($sites as $site) {
            $transactions = $this->fetchTransactionsFromExternalDB($site);

            foreach ($transactions as $transaction) {
                // Check if transaction already exists
                $existingTransaction = Transaction::where('external_id', $transaction['id'])->first();

                if ($existingTransaction) {
                    // If transaction is pending and more than 1 hour old, mark it as abandoned
                    if ($existingTransaction->status === 'pending' && $now->diffInHours($existingTransaction->created_at) > 1) {
                        $existingTransaction->update([
                            'status' => 'abandoned',
                            'updated_at' => $now
                        ]);
                        continue;
                    }
                    // Update existing transaction if status has changed
                    if ($existingTransaction->status !== $transaction['status']) {
                        $existingTransaction->update([
                            'status' => $transaction['status'],
                            'updated_at' => $now
                        ]);
                    }
                } else {
                    // Save new transaction
                    Transaction::create([
                        'site_id' => $site->id,
                        'external_id' => $transaction['id'],
                        'amount' => $transaction['amount'],
                        'status' => $transaction['status'],
                        'created_at' => $transaction['created_at'],
                        'updated_at' => $now
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Transactions fetched and saved successfully']);
    }
}
