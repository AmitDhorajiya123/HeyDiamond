<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMstSizeRangeRequest;
use App\Http\Requests\Api\UpdateMstSizeRangeRequest;
use App\Http\Resources\MstSizeRangeResource;
use App\Models\MstSizeRange;
use App\Traits\EncryptedId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MstSizeRangeApiController extends Controller
{
    use EncryptedId;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MstSizeRange::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('size_range_code', 'like', "%{$search}%")
                  ->orWhere('range_type', 'like', "%{$search}%");
            });
        }

        if ($rangeType = $request->input('range_type')) {
            $query->where('range_type', $rangeType);
        }

        if ($isActive = $request->input('is_active')) {
            $query->where('is_active', $isActive);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 15;

        $sizeRanges = $query->orderBy('display_order')->orderBy('size_range_id')->paginate($perPage);

        $sizeRanges->getCollection()->transform(function ($sizeRange) {
            $sizeRange->encrypted_id = $this->encryptId($sizeRange->size_range_id);
            return $sizeRange;
        });

        return MstSizeRangeResource::collection($sizeRanges);
    }

    public function store(StoreMstSizeRangeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $sizeRange = MstSizeRange::create($data);
        $sizeRange->encrypted_id = $this->encryptId($sizeRange->size_range_id);

        return (new MstSizeRangeResource($sizeRange))
            ->additional([
                'success' => true,
                'message' => 'Size range created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $encrypted_id): JsonResponse
    {
        $sizeRange = $this->findByEncryptedId(MstSizeRange::class, $encrypted_id, 'size_range_id');

        if (!$sizeRange) {
            return response()->json([
                'success' => false,
                'message' => 'Size range not found.',
            ], 404);
        }

        $sizeRange->encrypted_id = $this->encryptId($sizeRange->size_range_id);

        return (new MstSizeRangeResource($sizeRange))
            ->additional([
                'success' => true,
                'message' => 'Size range retrieved successfully.',
            ])
            ->response();
    }

    public function update(UpdateMstSizeRangeRequest $request, string $encrypted_id): JsonResponse
    {
        $sizeRange = $this->findByEncryptedId(MstSizeRange::class, $encrypted_id, 'size_range_id');

        if (!$sizeRange) {
            return response()->json([
                'success' => false,
                'message' => 'Size range not found.',
            ], 404);
        }

        $sizeRange->update($request->validated());
        $sizeRange->encrypted_id = $this->encryptId($sizeRange->size_range_id);

        return (new MstSizeRangeResource($sizeRange))
            ->additional([
                'success' => true,
                'message' => 'Size range updated successfully.',
            ])
            ->response();
    }

    public function destroy(string $encrypted_id): JsonResponse
    {
        $sizeRange = $this->findByEncryptedId(MstSizeRange::class, $encrypted_id, 'size_range_id');

        if (!$sizeRange) {
            return response()->json([
                'success' => false,
                'message' => 'Size range not found.',
            ], 404);
        }

        $sizeRange->delete();

        return response()->json([
            'success' => true,
            'message' => 'Size range deleted successfully.',
        ], 200);
    }
}