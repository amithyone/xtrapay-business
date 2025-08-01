<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminCodesTable extends Migration
{
    public function up()
    {
        Schema::create('admin_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code_type');
            $table->string('hashed_code');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_codes');
    }
} 