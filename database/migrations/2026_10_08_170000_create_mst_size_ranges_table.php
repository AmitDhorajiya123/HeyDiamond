<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_size_range', function (Blueprint $table) {
            $table->increments('size_range_id')->comment('Primary key');
            $table->string('size_range_code', 20)->unique()->comment('30s, 40s, 50s-60s, 70s-80s, 90s, 1ct, 1.5ct, 2ct, 3ct, 4ct, 5ct, 6-7ct, 8-9ct, 10ct+');
            $table->string('range_type', 20)->index()->comment('GENERAL / SPECIFIC (matches the tab toggle)');
            $table->decimal('min_carat', 6, 3)->nullable()->comment('Lower bound of range');
            $table->decimal('max_carat', 6, 3)->nullable()->comment('Upper bound of range (NULL = open-ended, e.g. 10ct+)');
            $table->integer('display_order')->nullable()->default(0)->comment('Sort order in UI');
            $table->boolean('is_active')->default(true)->comment('Active flag');
            $table->dateTime('created_date')->useCurrent()->comment('Record created date');
            $table->dateTime('modified_date')->nullable()->comment('Record updated date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_size_range');
    }
};