<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst_shape', function (Blueprint $table) {
            $table->increments('shape_id');
            $table->string('shape_code', 20)->unique();
            $table->string('shape_name', 50)->unique();
            $table->integer('display_order')->nullable()->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->dateTime('created_date')->useCurrent();
            $table->dateTime('modified_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_shape');
    }
};