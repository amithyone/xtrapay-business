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
        Schema::table('business_savings', function (Blueprint $table) {
            $table->integer('daily_collections_count')->default(0)->after('last_collection_date');
            $table->decimal('daily_collected_amount', 15, 2)->default(0)->after('daily_collections_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_savings', function (Blueprint $table) {
            $table->dropColumn(['daily_collections_count', 'daily_collected_amount']);
        });
    }
};
