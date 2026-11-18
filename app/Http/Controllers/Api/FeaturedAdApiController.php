<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiamondResource;
use App\Models\Diamond;
use App\Models\FeaturedDiamondAd;
use App\Traits\EncryptedId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeaturedAdApiController extends Controller
{
    use EncryptedId;

    public const MAX_ADS = 3;

    /**
     * All featured ads, in slot order. Diamond IDs are encrypted.
     */
    public function index(Request $request): JsonResponse
    {
        $ads = FeaturedDiamondAd::query()
            ->with('diamond')
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $ads->map(function (FeaturedDiamondAd $ad) use ($request) {
                return $this->payload($ad, $request);
            })->filter()->values()->all(),
        ]);
    }

    /**
     * Add a diamond to the featured ad slots.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'encrypted_id' => ['required', 'string', 'max:500'],
        ]);

        $diamondId = $this->decryptId($validated['encrypted_id']);
        $diamond = $diamondId !== null ? Diamond::query()->find($diamondId) : null;

        if (!$diamond) {
            return response()->json([
                'success' => false,
                'message' => 'Diamond not found.',
            ], 404);
        }

        $existing = FeaturedDiamondAd::query()->where('diamond_id', $diamond->id)->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'data' => $this->payload($existing, $request),
                'message' => 'Diamond is already featured.',
            ]);
        }

        if (FeaturedDiamondAd::query()->count() >= self::MAX_ADS) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum ' . self::MAX_ADS . ' featured diamonds allowed.',
            ], 422);
        }

        $nextPosition = (int) FeaturedDiamondAd::query()->max('position');

        $ad = FeaturedDiamondAd::query()->create([
            'diamond_id' => $diamond->id,
            'position' => $nextPosition + 1,
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->payload($ad, $request),
            'message' => 'Diamond added to featured ads.',
        ], 201);
    }

    /**
     * Remove a featured ad by its encrypted ad ID.
     */
    public function destroy(string $encrypted_id): JsonResponse
    {
        $adId = $this->decryptId($encrypted_id);
        $ad = $adId !== null ? FeaturedDiamondAd::query()->find($adId) : null;

        if (!$ad) {
            return response()->json([
                'success' => false,
                'message' => 'Featured ad not found.',
            ], 404);
        }

        $ad->delete();

        return response()->json([
            'success' => true,
            'message' => 'Diamond removed from featured ads.',
        ]);
    }

    /**
     * Shape one featured ad for the dashboard cards.
     */
    private function payload(FeaturedDiamondAd $ad, Request $request): ?array
    {
        if (!$ad->relationLoaded('diamond')) {
            $ad->load('diamond');
        }

        if (!$ad->diamond) {
            return null;
        }

        $diamond = DiamondResource::make($ad->diamond)->resolve($request);
        $diamond['ad_encrypted_id'] = $this->encryptId($ad->id);

        return $diamond;
    }
}
