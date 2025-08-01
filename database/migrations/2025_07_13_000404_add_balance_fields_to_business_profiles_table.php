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
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->decimal('withdrawable_balance', 15, 2)->default(0)->after('ledger_balance');
            $table->text('balance_notes')->nullable()->after('withdrawable_balance');
            $table->timestamp('last_balance_update')->nullable()->after('balance_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'withdrawable_balance',
                'balance_notes',
                'last_balance_update'
            ]);
        });
    }
};
