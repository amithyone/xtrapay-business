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
        Schema::create('business_savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_profile_id')->constrained()->onDelete('cascade');
            $table->decimal('monthly_goal', 15, 2)->default(1600000.00); // ₦1.6M default
            $table->decimal('current_savings', 15, 2)->default(0.00);
            $table->decimal('daily_collection_target', 15, 2)->default(0.00);
            $table->integer('daily_transaction_limit')->default(5);
            $table->integer('transactions_today')->default(0);
            $table->date('last_collection_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['business_profile_id', 'is_active']);
            $table->index('last_collection_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_savings');
    }
}; 