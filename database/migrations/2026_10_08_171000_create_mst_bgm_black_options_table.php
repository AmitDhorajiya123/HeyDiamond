<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_bgm_black_option', function (Blueprint $table) {
            $table->increments('option_id')->comment('Primary key');
            $table->string('option_code', 20)->unique()->comment('NO_BGM, NO_BLACK');
            $table->string('option_name', 50)->comment('Display label');
            $table->boolean('is_active')->default(true)->comment('Active flag');
            $table->dateTime('created_date')->useCurrent()->comment('Record created date');
            $table->dateTime('modified_date')->nullable()->comment('Record updated date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_bgm_black_option');
    }
};