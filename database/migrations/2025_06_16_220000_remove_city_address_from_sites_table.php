<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sites', function (Blueprint $table) {
            if (Schema::hasColumn('sites', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('sites', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('sites', 'website_url')) {
                $table->dropColumn('website_url');
            }
        });
    }

    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('website_url')->nullable();
        });
    }
}; 