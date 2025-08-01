<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businessProfile = $user->businessProfile;

        // Check if business profile exists
        if (!$businessProfile) {
            return redirect()->route('business-profile.create')
                ->with('error', 'Please complete your business profile before accessing withdrawals.');
        }

        $withdrawals = Transfer::where('business_profile_id', $businessProfile->id)
            ->where('type', 'withdrawal')
            ->latest()
            ->paginate(10);

        $beneficiaries = $businessProfile->beneficiaries;

        return view('withdrawals.index', compact('withdrawals', 'beneficiaries'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'beneficiary_id' => 'required|exists:beneficiaries,id',
                'amount' => 'required|numeric|min:1',
                'narration' => 'nullable|string|max:255',
                'pin' => 'required|string|size:4'
            ]);

            $user = auth()->user();
            $business = $user->businessProfile;

            // Check if business profile exists
            if (!$business) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Business profile not found. Please complete your profile first.'
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['general' => 'Business profile not found. Please complete your profile first.'])
                    ->withInput();
            }

            $beneficiary = Beneficiary::findOrFail($request->beneficiary_id);

            // Verify PIN
            if (!$business->pin || $business->pin !== $request->pin) {
                \Log::info('PIN Debug', [
                    'stored_pin' => $business->pin,
                    'received_pin' => $request->pin,
                    'pin_type' => gettype($request->pin),
                    'stored_type' => gettype($business->pin),
                    'comparison' => $business->pin === $request->pin
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid PIN'
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['pin' => 'Invalid PIN'])
                    ->withInput();
            }

            // Check if user has sufficient balance
            $currentBalance = $business->balance ?? 0;
            $requestedAmount = $request->amount;

            if ($currentBalance < $requestedAmount) {
                $errorMessage = "Insufficient balance. Available: ₦" . number_format($currentBalance, 2) . ", Requested: ₦" . number_format($requestedAmount, 2);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['amount' => $errorMessage])
                    ->withInput();
            }

            // Generate unique reference
            $reference = 'WD-' . strtoupper(uniqid());

            // Create withdrawal record
            $withdrawal = Transfer::create([
                'business_profile_id' => $business->id,
                'beneficiary_id' => $beneficiary->id,
                'amount' => $requestedAmount,
                'narration' => $request->narration,
                'reference' => $reference,
                'status' => 'pending',
                'recipient_account_number' => $beneficiary->account_number,
                'recipient_account_name' => $beneficiary->account_name,
                'recipient_bank' => $beneficiary->bank,
                'type' => 'withdrawal'
            ]);

            // Deduct from balance
            $business->balance = $currentBalance - $requestedAmount;
            $business->save();

            $successMessage = 'Withdrawal request submitted successfully! Reference: ' . $reference . ' | Amount: ₦' . number_format($requestedAmount, 2);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage
                ]);
            }
            return redirect()->route('withdrawals.dashboard')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            Log::error('Withdrawal Error: ' . $e->getMessage());
            $errorMessage = 'An error occurred while processing the withdrawal. Please try again.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            return redirect()->back()
                ->withErrors(['general' => $errorMessage])
                ->withInput();
        }
    }

    public function withdrawalDashboard()
    {
        $businessProfile = auth()->user()->businessProfile;
        if (!$businessProfile) {
            return view('withdrawals.dashboard', [
                'businessProfile' => null,
                'withdrawals' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'beneficiaries' => collect(),
                'totalWithdrawals' => 0,
                'pendingWithdrawals' => 0
            ]);
        }

        $withdrawals = \App\Models\Transfer::where('business_profile_id', $businessProfile->id)
            ->where('type', 'withdrawal')
            ->latest()
            ->paginate(10);
        $beneficiaries = $businessProfile->beneficiaries;
        $totalWithdrawals = \App\Models\Transfer::where('business_profile_id', $businessProfile->id)
            ->where('type', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');
        $pendingWithdrawals = \App\Models\Transfer::where('business_profile_id', $businessProfile->id)
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->count();

        return view('withdrawals.dashboard', [
            'businessProfile' => $businessProfile,
            'withdrawals' => $withdrawals,
            'beneficiaries' => $beneficiaries,
            'totalWithdrawals' => $totalWithdrawals,
            'pendingWithdrawals' => $pendingWithdrawals
        ]);
    }
} 