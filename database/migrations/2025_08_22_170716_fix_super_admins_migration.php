<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration is to fix the foreign key issue in the super_admins migration
        // We'll handle the foreign key drop safely here
        
        if (Schema::hasTable('tickets')) {
            Schema::table('tickets', function (Blueprint $table) {
                // Check if the assigned_to column exists
                if (Schema::hasColumn('tickets', 'assigned_to')) {
                    // Try to drop the foreign key safely
                    try {
                        $table->dropForeign(['assigned_to']);
                    } catch (\Exception $e) {
                        // Foreign key doesn't exist, continue
                    }
                    
                    // Drop the columns
                    $table->dropColumn([
                        'assigned_to',
                        'assigned_at',
                        'resolved_at',
                        'resolution_notes'
                    ]);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this fix
    }
};
