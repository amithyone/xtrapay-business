<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use App\Models\User;

class ManageArchivedSites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sites:archived {action=list : Action to perform (list, restore, delete)} {--site-id= : Site ID for restore/delete actions} {--user-id= : User ID to filter sites}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage archived sites - list, restore, or permanently delete them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $siteId = $this->option('site-id');
        $userId = $this->option('user-id');

        switch ($action) {
            case 'list':
                $this->listArchivedSites($userId);
                break;
            case 'restore':
                $this->restoreSite($siteId);
                break;
            case 'delete':
                $this->permanentlyDeleteSite($siteId);
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->info('Available actions: list, restore, delete');
                return 1;
        }

        return 0;
    }

    private function listArchivedSites($userId = null)
    {
        $query = Site::where('is_archived', true)->with(['businessProfile.user', 'transactions']);

        if ($userId) {
            $query->whereHas('businessProfile', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        $archivedSites = $query->get();

        if ($archivedSites->isEmpty()) {
            $this->info('✅ No archived sites found.');
            return;
        }

        $this->info("Found {$archivedSites->count()} archived site(s):\n");

        $archivedSites->each(function ($site) {
            $transactionCount = $site->transactions->count();
            $totalAmount = $site->transactions->sum('amount');
            
            $this->line("ID: {$site->id}");
            $this->line("Name: {$site->name}");
            $this->line("URL: {$site->url}");
            $this->line("Owner: {$site->businessProfile->user->name} ({$site->businessProfile->user->email})");
            $this->line("Transactions: {$transactionCount}");
            $this->line("Total Amount: ₦" . number_format($totalAmount, 2));
            $this->line("Archived: {$site->archived_at->format('Y-m-d H:i:s')}");
            $this->line("---");
        });
    }

    private function restoreSite($siteId)
    {
        if (!$siteId) {
            $this->error('Site ID is required for restore action. Use --site-id option.');
            return;
        }

        $site = Site::where('is_archived', true)->find($siteId);

        if (!$site) {
            $this->error("Archived site with ID {$siteId} not found.");
            return;
        }

        if ($this->confirm("Are you sure you want to restore site '{$site->name}'?")) {
            $site->update([
                'is_archived' => false,
                'is_active' => true,
                'archived_at' => null
            ]);

            $this->info("✅ Site '{$site->name}' has been restored and activated.");
        } else {
            $this->info('❌ Operation cancelled.');
        }
    }

    private function permanentlyDeleteSite($siteId)
    {
        if (!$siteId) {
            $this->error('Site ID is required for delete action. Use --site-id option.');
            return;
        }

        $site = Site::where('is_archived', true)->find($siteId);

        if (!$site) {
            $this->error("Archived site with ID {$siteId} not found.");
            return;
        }

        $transactionCount = $site->transactions->count();
        
        if ($transactionCount > 0) {
            $this->warn("⚠️  WARNING: This site has {$transactionCount} associated transactions!");
            $this->warn("Deleting this site will permanently remove all transaction history!");
            
            if (!$this->confirm("Are you absolutely sure you want to permanently delete site '{$site->name}' and all its transactions?")) {
                $this->info('❌ Operation cancelled.');
                return;
            }
        } else {
            if (!$this->confirm("Are you sure you want to permanently delete site '{$site->name}'?")) {
                $this->info('❌ Operation cancelled.');
                return;
            }
        }

        // Delete associated transactions first
        if ($transactionCount > 0) {
            $site->transactions()->delete();
            $this->info("Deleted {$transactionCount} associated transaction(s).");
        }

        // Delete the site
        $site->delete();
        $this->info("✅ Site '{$site->name}' has been permanently deleted.");
    }
} 