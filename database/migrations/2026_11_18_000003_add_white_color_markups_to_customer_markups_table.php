<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Split the old single "markup" slab into separate White and Fancy Color
     * percentages. Existing values are copied to both groups so nothing is
     * lost. The create migration already builds the final schema, so this
     * migration is deliberately idempotent for databases that were rolled
     * back and re-created afterwards.
     */
    public function up(): void
    {
        if (!Schema::hasTable('customer_markups')) {
            return;
        }

        if (!Schema::hasColumn('customer_markups', 'white_markup')) {
            Schema::table('customer_markups', function (Blueprint $table) {
                $table->decimal('white_markup', 8, 2)->default(0)->after('max_value');
            });
        }

        if (!Schema::hasColumn('customer_markups', 'color_markup')) {
            Schema::table('customer_markups', function (Blueprint $table) {
                $table->decimal('color_markup', 8, 2)->default(0)->after('white_markup');
            });
        }

        if (Schema::hasColumn('customer_markups', 'markup')) {
            DB::table('customer_markups')->update([
                'white_markup' => DB::raw('markup'),
                'color_markup' => DB::raw('markup'),
            ]);

            Schema::table('customer_markups', function (Blueprint $table) {
                $table->dropColumn('markup');
            });
        }
    }

    /**
     * The create migration owns the final schema, so rolling this batch back
     * simply leaves the White/Color columns in place. Rolling back the create
     * migration itself drops the whole table.
     */
    public function down(): void
    {
        // Intentionally empty.
    }
};
