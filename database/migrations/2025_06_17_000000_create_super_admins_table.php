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
        Schema::create('super_admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('super_admin'); // super_admin, admin
            $table->json('permissions')->nullable(); // Store specific permissions
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add ledger balance field to business_profiles table for super admin manual input
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->decimal('ledger_balance', 15, 2)->default(0)->comment('Actual business ledger balance (super admin managed)');
        });

        // Add fields to transfers table for better tracking
        Schema::table('transfers', function (Blueprint $table) {
            $table->string('processed_by')->nullable()->comment('Super admin who processed the withdrawal');
            $table->text('admin_notes')->nullable()->comment('Admin notes for the withdrawal');
            $table->timestamp('processed_at')->nullable();
            $table->string('processing_method')->nullable()->comment('How the withdrawal was processed');
        });

        // Add fields to tickets table for admin management
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable()->after('assigned_to');
            $table->timestamp('resolved_at')->nullable()->after('assigned_at');
            $table->text('resolution_notes')->nullable()->after('resolved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_admins');
        
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'ledger_balance'
            ]);
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn([
                'processed_by',
                'admin_notes',
                'processed_at',
                'processing_method'
            ]);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn([
                'assigned_to',
                'assigned_at',
                'resolved_at',
                'resolution_notes'
            ]);
        });
    }
}; 