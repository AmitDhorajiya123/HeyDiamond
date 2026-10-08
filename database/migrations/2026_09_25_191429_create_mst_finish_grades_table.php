<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_finish_grade', function (Blueprint $table) {
            $table->increments('finish_grade_id');
            $table->string('finish_grade_code', 10)->unique();
            $table->string('finish_grade_name', 30);
            $table->string('grade_group', 20)->nullable()->index();
            $table->integer('display_order')->nullable()->default(0);
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_date')->useCurrent();
            $table->dateTime('modified_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_finish_grade');
    }
};