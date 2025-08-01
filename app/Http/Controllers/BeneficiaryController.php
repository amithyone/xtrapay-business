<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;

class BeneficiaryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $businessProfile = $user->businessProfile;
        
        if (!$businessProfile) {
            return redirect()->route('business-profile.create')
                ->with('error', 'Please complete your business profile first.');
        }

        $beneficiaries = $businessProfile->beneficiaries;
        
        return view('beneficiaries.index', compact('beneficiaries', 'businessProfile'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank' => 'required|string|max:100',
            'account_number' => 'required|string|max:20',
            'account_name' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        $businessProfile = $user->businessProfile;
        if (!$businessProfile) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No business profile found.'
                ], 400);
            }
            return redirect()->route('beneficiaries.index')
                ->with('error', 'No business profile found.');
        }

        // Check if user already has 2 beneficiaries
        $existingCount = Beneficiary::where('business_profile_id', $businessProfile->id)->count();
        if ($existingCount >= 2) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum of 2 beneficiaries allowed.'
                ], 400);
            }
            return redirect()->route('beneficiaries.index')
                ->with('error', 'Maximum of 2 beneficiaries allowed.');
        }

        $beneficiary = Beneficiary::create([
            'business_profile_id' => $businessProfile->id,
            'bank' => $request->bank,
            'bank_name' => $request->bank,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'name' => $request->account_name,
            'account_type' => 'savings',
            'is_active' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Beneficiary added successfully!'
            ]);
        }

        return redirect()->route('beneficiaries.index')
            ->with('success', 'Beneficiary added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bank' => 'required|string|max:100',
            'account_number' => 'required|string|max:20',
            'account_name' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        $businessProfile = $user->businessProfile;
        if (!$businessProfile) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No business profile found.'
                ], 400);
            }
            return redirect()->route('beneficiaries.index')
                ->with('error', 'No business profile found.');
        }

        $beneficiary = Beneficiary::where('id', $id)
            ->where('business_profile_id', $businessProfile->id)
            ->first();

        if (!$beneficiary) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary not found.'
                ], 404);
            }
            return redirect()->route('beneficiaries.index')
                ->with('error', 'Beneficiary not found.');
        }

        $beneficiary->update([
            'bank' => $request->bank,
            'bank_name' => $request->bank,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'name' => $request->account_name,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Beneficiary updated successfully!'
            ]);
        }

        return redirect()->route('beneficiaries.index')
            ->with('success', 'Beneficiary updated successfully!');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $businessProfile = $user->businessProfile;
        if (!$businessProfile) {
            return redirect()->route('beneficiaries.index')
                ->with('error', 'No business profile found.');
        }

        $beneficiary = Beneficiary::where('id', $id)
            ->where('business_profile_id', $businessProfile->id)
            ->first();

        if (!$beneficiary) {
            return redirect()->route('beneficiaries.index')
                ->with('error', 'Beneficiary not found.');
        }

        $beneficiary->delete();

        return redirect()->route('beneficiaries.index')
            ->with('success', 'Beneficiary deleted successfully!');
    }
}
