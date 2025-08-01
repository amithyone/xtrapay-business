<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use App\Models\BusinessProfile;
use Illuminate\Support\Str;

class AddFaddedsmmSite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:add-faddedsmm {--user-id=1 : The user ID to associate the site with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add faddedsmm.com site with proper API key for webhook integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        
        // Find the business profile for the user
        $businessProfile = BusinessProfile::where('user_id', $userId)->first();
        
        if (!$businessProfile) {
            $this->error("No business profile found for user ID: {$userId}");
            $this->info("Please create a business profile first or specify a different user ID with --user-id option.");
            return 1;
        }

        // Check if faddedsmm site already exists
        $existingSite = Site::where('name', 'faddedsmm')->first();
        if ($existingSite) {
            $this->warn("Site 'faddedsmm' already exists!");
            $this->info("Site ID: {$existingSite->id}");
            $this->info("API Key: {$existingSite->api_key}");
            $this->info("API Code: {$existingSite->api_code}");
            return 0;
        }

        // Generate a secure API key
        $apiKey = Str::random(64);
        $apiCode = 'faddedsmm_' . Str::random(16);

        try {
            $site = Site::create([
                'business_profile_id' => $businessProfile->id,
                'name' => 'faddedsmm',
                'url' => 'https://faddedsmm.com',
                'webhook_url' => 'https://faddedsmm.com/webhook',
                'api_code' => $apiCode,
                'api_key' => $apiKey,
                'daily_revenue' => 0.00,
                'monthly_revenue' => 0.00,
                'is_active' => true,
                'allowed_ips' => '127.0.0.1,::1', // Add faddedsmm.com IPs here
            ]);

            $this->info("✅ faddedsmm site created successfully!");
            $this->newLine();
            $this->info("📋 Site Details:");
            $this->info("   ID: {$site->id}");
            $this->info("   Name: {$site->name}");
            $this->info("   URL: {$site->url}");
            $this->info("   Webhook URL: {$site->webhook_url}");
            $this->newLine();
            $this->info("🔑 API Credentials:");
            $this->info("   API Key: {$site->api_key}");
            $this->info("   API Code: {$site->api_code}");
            $this->newLine();
            $this->info("🌐 Webhook Endpoint:");
            $this->info("   URL: " . url('/api/webhook/transaction'));
            $this->newLine();
            $this->warn("⚠️  Important:");
            $this->info("   1. Update the allowed_ips field with faddedsmm.com's actual IP addresses");
            $this->info("   2. Test the webhook connection using the provided API key");
            $this->newLine();
            $this->info("📝 Example webhook request:");
            $this->line("   curl -X POST " . url('/api/webhook/transaction') . " \\");
            $this->line("     -H 'Content-Type: application/json' \\");
            $this->line("     -H 'X-API-Key: {$site->api_key}' \\");
            $this->line("     -d '{\"reference\":\"TXN123\",\"amount\":1000,\"status\":\"completed\"}'");

        } catch (\Exception $e) {
            $this->error("❌ Failed to create faddedsmm site: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 