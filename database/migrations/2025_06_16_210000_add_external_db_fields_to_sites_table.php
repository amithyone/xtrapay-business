<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->text('external_db_host')->nullable()->after('webhook_url');
            $table->text('external_db_name')->nullable()->after('external_db_host');
            $table->text('external_db_username')->nullable()->after('external_db_name');
            $table->text('external_db_password')->nullable()->after('external_db_username');
            $table->json('field_mapping')->nullable()->after('external_db_password');
        });
    }

    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['external_db_host', 'external_db_name', 'external_db_username', 'external_db_password', 'field_mapping']);
        });
    }
}; 