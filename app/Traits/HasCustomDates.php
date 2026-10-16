<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

/**
 * Maintains the custom `created_date` / `modified_date` columns used by
 * the master tables (which run with `$timestamps = false`).
 *
 * - On create: fills `created_date` when empty (DB default also covers it).
 * - On update: always refreshes `modified_date` to now.
 */
trait HasCustomDates
{
    public static function bootHasCustomDates(): void
    {
        static::creating(function ($model) {
            if (empty($model->created_date)) {
                $model->created_date = Carbon::now();
            }
        });

        static::updating(function ($model) {
            $model->modified_date = Carbon::now();
        });
    }
}
