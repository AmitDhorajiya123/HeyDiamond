<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diamond extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Named fancy colours, matching the Inventory filter's Fancy Color palette.
     * "WHITE" here is a fancy colour grade, distinct from the D-Z white grades.
     */
    public const FANCY_COLORS = [
        'YELLOW', 'ORANGE', 'PINK', 'BLUE', 'GREEN', 'CHAMPAGNE',
        'BROWN', 'RED', 'WHITE', 'VIOLET', 'PURPLE', 'GRAY',
        'OLIVE', 'BLACK', 'ORANGY YELLOW', 'ORANGE BROWN',
        'PINK BROWN', 'GREEN YELLOW', 'BROWN YELLOW', 'COGNAC',
        'CHAMELEON',
    ];

    /**
     * True when this stone should use the "color_markup" slab rather than the
     * "white_markup" slab. Mirrors the Fancy Color logic used by the filter.
     */
    public function isFancyColor(): bool
    {
        $fancyColor = strtoupper(trim((string) $this->fancyColor));

        if ($fancyColor !== '') {
            return true;
        }

        $intensity = strtoupper(trim((string) $this->fancyColorIntensity));

        if ($intensity !== '') {
            return true;
        }

        $color = strtoupper(trim((string) $this->color));

        if ($color === '') {
            return false;
        }

        return str_starts_with($color, 'FANCY') || in_array($color, self::FANCY_COLORS, true);
    }

    public function apiClient()
    {
        return $this->belongsTo(ApiClient::class, 'api_client_id');
    }
}
