<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiamondResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $customer = $this->resolveMarkupCustomer($request);

        return [
            'encrypted_id' => $this->encrypted_id ?? \App\Traits\EncryptedId::encryptIdStatic($this->id),
            'api_client_name' => $this->apiClient?->name,
            'markup_percent' => $this->resolveMarkupPercent($customer),
            'stockNo' => $this->stockNo,
            'status' => $this->status,
            'shape' => $this->shape,
            'Diamond_Type' => $this->Diamond_Type,
            'weight' => $this->weight,
            'color' => $this->color,
            'clarity' => $this->clarity,
            'cut' => $this->cut,
            'polish' => $this->polish,
            'symmetry' => $this->symmetry,
            'fluorescenceIntensity' => $this->fluorescenceIntensity,
            'fluorescenceColor' => $this->fluorescenceColor,
            'measurements' => $this->measurements,
            'shade' => $this->shade,
            'BGM' => $this->BGM,
            'milky' => $this->milky,
            'eyeClean' => $this->eyeClean,
            'lab' => $this->lab,
            'reportNo' => $this->reportNo,
            'reportDate' => $this->reportDate,
            'location' => $this->location,
            'treatment' => $this->treatment,
            'ratio' => $this->ratio,
            'growthType' => $this->growthType,
            'rapPricePerCt' => $this->rapPricePerCt,
            'depth' => $this->depth,
            'table' => $this->table,
            'girdleThin' => $this->girdleThin,
            'girdleThick' => $this->girdleThick,
            'gridlePer' => $this->gridlePer,
            'girdleCondition' => $this->girdleCondition,
            'culetSize' => $this->culetSize,
            'ciletCondition' => $this->ciletCondition,
            'crownHeight' => $this->crownHeight,
            'crownAngle' => $this->crownAngle,
            'pavilionDepth' => $this->pavilionDepth,
            'pavilionAngle' => $this->pavilionAngle,
            'starLength' => $this->starLength,
            'inscription' => $this->inscription,
            'laserInscription' => $this->laserInscription,
            'certiComment' => $this->certiComment,
            'keyToSymbole' => $this->keyToSymbole,
            'inclusion' => $this->inclusion,
            'whiteInclusion' => $this->whiteInclusion,
            'blackInclusion' => $this->blackInclusion,
            'openInclusion' => $this->openInclusion,
            'fancyColor' => $this->fancyColor,
            'fancyColorIntensity' => $this->fancyColorIntensity,
            'fancyColorOvertone' => $this->fancyColorOvertone,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'certiFile' => $this->certiFile,
            'videoLink' => $this->videoLink,
            'vdbVideo' => $this->vdbVideo,
            'imageLink' => $this->imageLink,
            'heartImage' => $this->heartImage,
            'arrowImage' => $this->arrowImage,
            'assetImage' => $this->assetImage,
            'heartAndArrow' => $this->heartAndArrow,
            'luster' => $this->luster,
            'brand' => $this->brand,
            'origin' => $this->origin,
            'COP' => $this->COP,
            'DNA' => $this->DNA,
            'handyVideo' => $this->handyVideo,
            'supplierComment' => $this->supplierComment,
            'tinge' => $this->tinge,
            'memberComment' => $this->memberComment,
            'isPair' => $this->isPair,
            'pairStock' => $this->pairStock,
            'tweezerVideo' => $this->tweezerVideo,
            'length' => $this->length,
            'height' => $this->height,
            'width' => $this->width,
            'pricePerCt' => $this->pricePerCt,
            'totalPrice' => $this->totalPrice,
            'discount' => $this->discount,
            'exclusive' => $this->exclusive,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * The customer whose markup should be applied. An admin preview can pass
     * a customer via the request attribute; otherwise the logged-in user is used.
     */
    private function resolveMarkupCustomer(Request $request): ?User
    {
        $customer = $request->attributes->get('markup_customer');

        if ($customer instanceof User) {
            return $customer;
        }

        return $request->user();
    }

    /**
     * Resolve the markup % for this diamond from the customer's price-range slabs.
     * Ranges are based on $/ct; the matched % is applied to both $/ct and Total $.
     */
    private function resolveMarkupPercent(?User $customer): float
    {
        if (!$customer) {
            return 0.0;
        }

        $fallback = (float) $customer->markup;
        $ranges = $customer->relationLoaded('markups')
            ? $customer->markups
            : $customer->markups()->get();

        if ($ranges->isEmpty() || !is_numeric($this->pricePerCt)) {
            return $fallback;
        }

        $value = (float) $this->pricePerCt;
        $sorted = $ranges->sortBy('min_value')->values();
        $matched = null;

        foreach ($sorted as $range) {
            $min = (float) $range->min_value;
            $max = $range->max_value === null ? null : (float) $range->max_value;

            if ($value >= $min && ($max === null || $value <= $max)) {
                $matched = $range;
                break;
            }
        }

        // Decimal prices can fall between integer tiers (e.g. 100.50 between
        // 0-100 and 101-200). In that case keep the highest tier whose From <= value.
        if (!$matched) {
            foreach ($sorted as $range) {
                if ($value >= (float) $range->min_value) {
                    $matched = $range;
                }
            }
        }

        return $matched ? (float) $matched->markup : $fallback;
    }
}
