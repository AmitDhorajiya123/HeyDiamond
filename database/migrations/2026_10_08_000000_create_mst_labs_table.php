<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_lab', function (Blueprint $table) {
            $table->increments('lab_id')->comment('Primary key');
            $table->string('lab_code', 10)->unique()->comment('IGI, RK, GIA, HRD, Other');
            $table->string('lab_name', 100)->comment('Full grading lab name');
            $table->integer('display_order')->nullable()->default(0)->comment('Sort order in UI');
            $table->boolean('is_active')->default(true)->comment('Active flag');
            $table->dateTime('created_date')->useCurrent()->comment('Record created date');
            $table->dateTime('modified_date')->nullable()->comment('Record updated date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_lab');
    }
};