<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_category', function (Blueprint $table) {
            $table->increments('category_id')->comment('Primary key');
            $table->string('category_code', 20)->unique()->comment('Category code');
            $table->string('category_name', 100)->comment('Category name');
            $table->boolean('is_active')->default(true)->comment('Active flag');
            $table->dateTime('created_date')->useCurrent()->comment('Record created date');
            $table->dateTime('modified_date')->nullable()->comment('Record updated date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_category');
    }
};