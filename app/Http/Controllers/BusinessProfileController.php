<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BusinessProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $profile = $user->businessProfile;
        return view('business-profile.index', compact('profile'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->businessProfile) {
            return redirect()->route('business-profile.show', ['business_profile' => $user->businessProfile->id]);
        }
        return view('business-profile.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->businessProfile) {
            return response()->json(['error' => 'Profile already exists.'], 422);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'business_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:business_profiles',
            'tax_identification_number' => 'required|string|max:255|unique:business_profiles',
            'business_type' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            'verification_id_type' => 'required|string|in:passport,national_id,drivers_license,voters_card',
            'verification_id_number' => 'required|string|max:255',
            'verification_id_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'proof_of_address_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $logoPath;
        }
        if ($request->hasFile('verification_id_file')) {
            $data['verification_id_file'] = $request->file('verification_id_file')->store('kyc/id', 'public');
        }
        if ($request->hasFile('proof_of_address_file')) {
            $data['proof_of_address_file'] = $request->file('proof_of_address_file')->store('kyc/address', 'public');
        }
        $data['user_id'] = $user->id;
        $data['is_verified'] = false;
        $profile = BusinessProfile::create($data);
        return response()->json(['profile' => $profile], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id = null)
    {
        $user = Auth::user();
        $profile = $user->businessProfile;
        if (!$profile) {
            return response()->json(['profile' => null], 200);
        }
        return response()->json(['profile' => $profile], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id = null)
    {
        $user = Auth::user();
        $profile = $user->businessProfile;
        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'business_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:business_profiles,registration_number,' . $profile->id,
            'tax_identification_number' => 'required|string|max:255|unique:business_profiles,tax_identification_number,' . $profile->id,
            'business_type' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            'verification_id_type' => 'required|string|in:passport,national_id,drivers_license,voters_card',
            'verification_id_number' => 'required|string|max:255',
            'verification_id_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'proof_of_address_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }
        if ($request->hasFile('verification_id_file')) {
            if ($profile->verification_id_file) {
                Storage::disk('public')->delete($profile->verification_id_file);
            }
            $data['verification_id_file'] = $request->file('verification_id_file')->store('kyc/id', 'public');
            $data['is_verified'] = false;
        }
        if ($request->hasFile('proof_of_address_file')) {
            if ($profile->proof_of_address_file) {
                Storage::disk('public')->delete($profile->proof_of_address_file);
            }
            $data['proof_of_address_file'] = $request->file('proof_of_address_file')->store('kyc/address', 'public');
            $data['is_verified'] = false;
        }
        $profile->update($data);
        return response()->json(['profile' => $profile], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
