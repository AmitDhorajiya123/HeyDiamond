<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDiamondRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $diamondId = $this->route('diamond') ? ($this->route('diamond')->id ?? $this->route('diamond')) : null;

        return [
            'stockNo' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('diamonds', 'stockNo')->ignore($diamondId)],
            'status' => ['nullable', 'string'],
            'shape' => ['nullable', 'string'],
            'Diamond_Type' => ['nullable', 'string'],
            'weight' => ['nullable', 'string'],
            'color' => ['nullable', 'string'],
            'clarity' => ['nullable', 'string'],
            'cut' => ['nullable', 'string'],
            'polish' => ['nullable', 'string'],
            'symmetry' => ['nullable', 'string'],
            'fluorescenceIntensity' => ['nullable', 'string'],
            'fluorescenceColor' => ['nullable', 'string'],
            'measurements' => ['nullable', 'string'],
            'shade' => ['nullable', 'string'],
            'BGM' => ['nullable', 'string'],
            'milky' => ['nullable', 'string'],
            'eyeClean' => ['nullable', 'string'],
            'lab' => ['nullable', 'string'],
            'reportNo' => ['nullable', 'string'],
            'reportDate' => ['nullable', 'string'],
            'location' => ['nullable', 'string'],
            'treatment' => ['nullable', 'string'],
            'ratio' => ['nullable', 'string'],
            'growthType' => ['nullable', 'string'],
            'rapPricePerCt' => ['nullable', 'string'],
            'depth' => ['nullable', 'string'],
            'table' => ['nullable', 'string'],
            'girdleThin' => ['nullable', 'string'],
            'girdleThick' => ['nullable', 'string'],
            'gridlePer' => ['nullable', 'string'],
            'girdleCondition' => ['nullable', 'string'],
            'culetSize' => ['nullable', 'string'],
            'ciletCondition' => ['nullable', 'string'],
            'crownHeight' => ['nullable', 'string'],
            'crownAngle' => ['nullable', 'string'],
            'pavilionDepth' => ['nullable', 'string'],
            'pavilionAngle' => ['nullable', 'string'],
            'starLength' => ['nullable', 'string'],
            'inscription' => ['nullable', 'string'],
            'laserInscription' => ['nullable', 'string'],
            'certiComment' => ['nullable', 'string'],
            'keyToSymbole' => ['nullable', 'string'],
            'inclusion' => ['nullable', 'string'],
            'whiteInclusion' => ['nullable', 'string'],
            'blackInclusion' => ['nullable', 'string'],
            'openInclusion' => ['nullable', 'string'],
            'fancyColor' => ['nullable', 'string'],
            'fancyColorIntensity' => ['nullable', 'string'],
            'fancyColorOvertone' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'certiFile' => ['nullable', 'string'],
            'videoLink' => ['nullable', 'string'],
            'vdbVideo' => ['nullable', 'string'],
            'imageLink' => ['nullable', 'string'],
            'heartImage' => ['nullable', 'string'],
            'arrowImage' => ['nullable', 'string'],
            'assetImage' => ['nullable', 'string'],
            'heartAndArrow' => ['nullable', 'string'],
            'luster' => ['nullable', 'string'],
            'brand' => ['nullable', 'string'],
            'origin' => ['nullable', 'string'],
            'COP' => ['nullable', 'string'],
            'DNA' => ['nullable', 'string'],
            'handyVideo' => ['nullable', 'string'],
            'supplierComment' => ['nullable', 'string'],
            'tinge' => ['nullable', 'string'],
            'memberComment' => ['nullable', 'string'],
            'isPair' => ['nullable', 'string'],
            'pairStock' => ['nullable', 'string'],
            'tweezerVideo' => ['nullable', 'string'],
            'length' => ['nullable', 'string'],
            'height' => ['nullable', 'string'],
            'width' => ['nullable', 'string'],
            'pricePerCt' => ['nullable', 'string'],
            'totalPrice' => ['nullable', 'string'],
            'discount' => ['nullable', 'string'],
            'exclusive' => ['nullable', 'string'],
        ];
    }
}
