<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Site;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->isSuperAdmin()) {
            // Super admin users can see all sites
            $sites = Site::all();
        } else {
            // Regular users can only see their own sites
            $sites = Auth::user()->businessProfile->sites;
        }
        
        return view('sites.index', compact('sites'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sites.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'url' => 'required|string|max:255',
                'webhook_url' => 'required|string|max:255',
                'allowed_ips' => 'nullable|string|max:1024',
                'is_active' => 'nullable|boolean',
            ]);

            // Auto-generate API code and API key
            $validated['api_code'] = $this->generateApiCode();
            $validated['api_key'] = $this->generateApiKey();

            $businessProfile = Auth::user()->businessProfile;
            if (!$businessProfile) {
                if ($request->wantsJson() || $request->ajax() || $request->isJson()) {
                    return response()->json(['error' => 'No business profile found. Please create a business profile first.'], 422);
                }
                return redirect()->back()->withErrors(['error' => 'No business profile found. Please create a business profile first.']);
            }

            $site = $businessProfile->sites()->create($validated);

            if ($request->wantsJson() || $request->ajax() || $request->isJson()) {
                return response()->json(['site' => $site]);
            }

            return redirect()->route('dashboard')
                ->with('success', 'Site created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax() || $request->isJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Site creation error: ' . $e->getMessage(), ['exception' => $e]);
            if ($request->wantsJson() || $request->ajax() || $request->isJson()) {
                return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
            }
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Site $site)
    {
        // Allow super admin users to access any site
        if (auth()->user()->isSuperAdmin()) {
            // Continue with the method
        } else {
            // Regular users can only access their own sites
            if (!auth()->user()->businessProfile || $site->business_profile_id !== auth()->user()->businessProfile->id) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        if (request()->expectsJson()) {
            return response()->json([
                'site' => [
                    'id' => $site->id,
                    'name' => $site->name,
                    'url' => $site->url,
                    'webhook_url' => $site->webhook_url,
                    'api_code' => $site->api_code,
                    'api_key' => $site->api_key,
                    'allowed_ips' => $site->allowed_ips,
                    'is_active' => $site->is_active,
                    'created_at' => $site->created_at,
                    'updated_at' => $site->updated_at,
                    'daily_revenue' => $site->daily_revenue ?? 0,
                    'monthly_revenue' => $site->monthly_revenue ?? 0
                ]
            ]);
        }
        
        return view('sites.show', compact('site'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Site $site)
    {
        // Allow super admin users to access any site
        if (auth()->user()->isSuperAdmin()) {
            // Continue with the method
        } else {
            // Regular users can only access their own sites
            if (!auth()->user()->businessProfile || $site->business_profile_id !== auth()->user()->businessProfile->id) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        if (request()->expectsJson()) {
            return response()->json([
                'site' => [
                    'id' => $site->id,
                    'name' => $site->name,
                    'url' => $site->url,
                    'webhook_url' => $site->webhook_url,
                    'api_code' => $site->api_code,
                    'api_key' => $site->api_key,
                    'allowed_ips' => $site->allowed_ips,
                    'is_active' => $site->is_active,
                    'created_at' => $site->created_at,
                    'updated_at' => $site->updated_at
                ]
            ]);
        }
        
        return view('sites.edit', compact('site'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Site $site)
    {
        // Allow super admin users to access any site
        if (auth()->user()->isSuperAdmin()) {
            // Continue with the method
        } else {
            // Regular users can only access their own sites
            if (!auth()->user()->businessProfile || $site->business_profile_id !== auth()->user()->businessProfile->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'webhook_url' => 'required|string|max:255',
            'allowed_ips' => 'nullable|string|max:1024',
            'is_active' => 'nullable|boolean',
        ]);

        // Don't allow changing API code or API key
        unset($validated['api_code'], $validated['api_key']);

        $site->update($validated);

        if ($request->wantsJson() || $request->ajax() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Site updated successfully.',
                'site' => $site
            ]);
        }

        return redirect()->route('sites.index')
            ->with('success', 'Site updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Site $site)
    {
        // Allow super admin users to access any site
        if (auth()->user()->isSuperAdmin()) {
            // Continue with the method
        } else {
            // Regular users can only access their own sites
            if (!auth()->user()->businessProfile || $site->business_profile_id !== auth()->user()->businessProfile->id) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        try {
            // Check if site has associated transactions
            $transactionCount = $site->transactions()->count();
            
            if ($transactionCount > 0) {
                // Instead of deleting, we'll archive the site and keep transactions
                $site->update([
                    'is_active' => false,
                    'is_archived' => true,
                    'archived_at' => now()
                ]);

                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Site archived successfully. All transaction history has been preserved.'
                    ]);
                }

                return redirect()->route('sites.index')
                    ->with('success', 'Site archived successfully. All transaction history has been preserved.');
            } else {
                // Only delete if no transactions exist
                $site->delete();

                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Site deleted successfully.'
                    ]);
                }

                return redirect()->route('sites.index')
                    ->with('success', 'Site deleted successfully.');
            }
                
        } catch (\Exception $e) {
            \Log::error('Site deletion error: ' . $e->getMessage(), ['site_id' => $site->id, 'exception' => $e]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error processing site: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error processing site: ' . $e->getMessage()]);
        }
    }

    public function activate(Site $site)
    {
        // Allow super admin users to access any site
        if (auth()->user()->isSuperAdmin()) {
            // Continue with the method
        } else {
            // Regular users can only access their own sites
            if (!auth()->user()->businessProfile || $site->business_profile_id !== auth()->user()->businessProfile->id) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        try {
            $site->update(['is_active' => true]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Site activated successfully.'
                ]);
            }
            
            return redirect()->back()->with('success', 'Site activated successfully.');
        } catch (\Exception $e) {
            \Log::error('Site activation error: ' . $e->getMessage(), ['site_id' => $site->id, 'exception' => $e]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error activating site: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Error activating site: ' . $e->getMessage()]);
        }
    }

    public function deactivate(Site $site)
    {
        \Log::info('Deactivate site request', [
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'is_admin' => auth()->user()->is_admin,
            'is_super_admin' => auth()->user()->isSuperAdmin(),
            'site_id' => $site->id,
            'site_business_id' => $site->business_profile_id,
            'user_business_id' => auth()->user()->businessProfile ? auth()->user()->businessProfile->id : null
        ]);
        
        // Allow super admin users to access any site
        if (auth()->user()->isSuperAdmin()) {
            \Log::info('User is super admin, allowing access');
        } else {
            // Regular users can only access their own sites
            if (!auth()->user()->businessProfile || $site->business_profile_id !== auth()->user()->businessProfile->id) {
                \Log::error('Unauthorized access attempt', [
                    'user_id' => auth()->id(),
                    'site_id' => $site->id,
                    'site_business_id' => $site->business_profile_id,
                    'user_business_id' => auth()->user()->businessProfile ? auth()->user()->businessProfile->id : null
                ]);
                abort(403, 'Unauthorized action.');
            }
            \Log::info('User owns this site, allowing access');
        }
        
        try {
            $site->update(['is_active' => false]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Site deactivated successfully.'
                ]);
            }
            
            return redirect()->back()->with('success', 'Site deactivated successfully.');
        } catch (\Exception $e) {
            \Log::error('Site deactivation error: ' . $e->getMessage(), ['site_id' => $site->id, 'exception' => $e]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deactivating site: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Error deactivating site: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate a unique API code for the site
     */
    private function generateApiCode()
    {
        do {
            $apiCode = strtolower(\Str::random(8)); // 8 character lowercase code
        } while (Site::where('api_code', $apiCode)->exists());

        return $apiCode;
    }

    /**
     * Generate a unique API key for the site
     */
    private function generateApiKey()
    {
        do {
            $apiKey = \Str::random(64); // 64 character key
        } while (Site::where('api_key', $apiKey)->exists());

        return $apiKey;
    }
}
