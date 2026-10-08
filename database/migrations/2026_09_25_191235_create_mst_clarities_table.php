<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_clarity', function (Blueprint $table) {
            $table->increments('clarity_id');
            $table->string('clarity_code', 10)->unique();
            $table->integer('display_order')->nullable()->default(0);
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_date')->useCurrent();
            $table->dateTime('modified_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_clarity');
    }
};