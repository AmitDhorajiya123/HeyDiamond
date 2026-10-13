<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
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
}
