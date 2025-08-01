<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sites', function (Blueprint $table) {
            if (Schema::hasColumn('sites', 'state')) {
                $table->dropColumn('state');
            }
        });
    }

    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('state')->nullable();
        });
    }
}; 