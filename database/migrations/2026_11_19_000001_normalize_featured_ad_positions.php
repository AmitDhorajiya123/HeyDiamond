<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Featured ad slots are fixed (1..3). Older rows may have been stored with
     * zero-based or duplicate positions; fix only those, leaving valid slot
     * numbers untouched so nothing visually moves.
     */
    public function up(): void
    {
        if (!Schema::hasTable('featured_diamond_ads')) {
            return;
        }

        $rows = DB::table('featured_diamond_ads')
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $used = [];

        foreach ($rows as $row) {
            $position = (int) $row->position;

            if ($position < 1 || $position > 3 || in_array($position, $used, true)) {
                for ($slot = 1; $slot <= 3; $slot++) {
                    if (!in_array($slot, $used, true)) {
                        DB::table('featured_diamond_ads')
                            ->where('id', $row->id)
                            ->update(['position' => $slot]);

                        $position = $slot;
                        break;
                    }
                }
            }

            $used[] = $position;
        }
    }

    public function down(): void
    {
        // Position normalization is intentionally irreversible.
    }
};
