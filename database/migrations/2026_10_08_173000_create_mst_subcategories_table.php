<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_subcategory', function (Blueprint $table) {
            $table->increments('subcategory_id')->comment('Primary key');
            $table->unsignedInteger('category_id')->index();
            $table->string('subcategory_code', 20)->unique()->comment('Subcategory code');
            $table->string('subcategory_name', 100)->comment('Subcategory name');
            $table->boolean('is_active')->default(true)->comment('Active flag');
            $table->dateTime('created_date')->useCurrent()->comment('Record created date');
            $table->dateTime('modified_date')->nullable()->comment('Record updated date');

            $table->foreign('category_id')
                ->references('category_id')
                ->on('mst_category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_subcategory');
    }
};