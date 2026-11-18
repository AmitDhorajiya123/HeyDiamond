<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HandlesMarkupRanges
{
    /**
     * Normalise incoming ranges (numeric values, null for open-ended) and sort them.
     */
    protected function normaliseMarkupRanges(array $ranges): array
    {
        $normalised = [];

        foreach ($ranges as $range) {
            if (!is_array($range)) {
                continue;
            }

            $hasValue = false;
            foreach (['min_value', 'max_value', 'markup'] as $key) {
                if (isset($range[$key]) && $range[$key] !== '') {
                    $hasValue = true;
                    break;
                }
            }

            // Empty rows are optional; only save ranges the user actually filled.
            if (!$hasValue) {
                continue;
            }

            $normalised[] = [
                'min_value' => isset($range['min_value']) && $range['min_value'] !== ''
                    ? (float) $range['min_value']
                    : 0,
                'max_value' => isset($range['max_value']) && $range['max_value'] !== '' && $range['max_value'] !== null
                    ? (float) $range['max_value']
                    : null,
                'markup' => isset($range['markup']) && $range['markup'] !== ''
                    ? (float) $range['markup']
                    : 0,
            ];
        }

        usort($normalised, function ($a, $b) {
            return $a['min_value'] <=> $b['min_value'];
        });

        return $normalised;
    }

    /**
     * Reject overlapping ranges and open-ended ranges that are not last.
     */
    protected function markupRangesError(array $ranges): ?string
    {
        $previousMax = null;

        foreach ($ranges as $index => $range) {
            if ($range['max_value'] !== null && $range['max_value'] < $range['min_value']) {
                return 'Range ' . ($index + 1) . ': "To" must be greater than or equal to "From".';
            }

            if ($range['markup'] > 100) {
                return 'Range ' . ($index + 1) . ': markup cannot exceed 100%.';
            }

            if ($index > 0) {
                if ($previousMax === null) {
                    return 'Only the last markup range can have an empty "To" value.';
                }

                if ($range['min_value'] <= $previousMax) {
                    return 'Markup ranges must not overlap.';
                }
            }

            $previousMax = $range['max_value'];
        }

        return null;
    }

    /**
     * Replace all markup ranges for a model that has a `markups()` relation.
     */
    protected function syncMarkupRanges(Model $owner, array $ranges): void
    {
        $owner->markups()->delete();

        if (!empty($ranges)) {
            $owner->markups()->createMany($ranges);
        }

        $owner->load('markups');
    }
}
