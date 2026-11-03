<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the per-API markup margin (percentage) used to calculate
     * the marked-up $/ct and total price shown on the inventory listing.
     */
    public function up(): void
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->decimal('markup', 8, 2)->default(0)->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->dropColumn('markup');
        });
    }
};
