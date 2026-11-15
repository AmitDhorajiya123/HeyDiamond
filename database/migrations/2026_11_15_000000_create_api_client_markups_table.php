<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-API markup slabs. Each row defines a $/ct range and the markup
     * percentage that applies to both $/ct and total price inside it.
     *
     * A null max_value means "and above" (open-ended final tier).
     * No foreign key is used because this project's tables are MyISAM.
     */
    public function up(): void
    {
        Schema::create('api_client_markups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_client_id');
            $table->decimal('min_value', 12, 2)->default(0);
            $table->decimal('max_value', 12, 2)->nullable();
            $table->decimal('markup', 8, 2)->default(0);
            $table->timestamps();

            $table->index(['api_client_id', 'min_value']);
            $table->unique(['api_client_id', 'min_value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_client_markups');
    }
};
