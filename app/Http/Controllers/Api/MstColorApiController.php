<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMstColorRequest;
use App\Http\Requests\Api\UpdateMstColorRequest;
use App\Http\Resources\MstColorResource;
use App\Models\MstColor;
use App\Traits\EncryptedId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MstColorApiController extends Controller
{
    use EncryptedId;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MstColor::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('color_code', 'like', "%{$search}%")
                  ->orWhere('color_type', 'like', "%{$search}%");
            });
        }

        if ($isActive = $request->input('is_active')) {
            $query->where('is_active', $isActive);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 15;

        $colors = $query->orderBy('display_order')->orderBy('color_id')->paginate($perPage);

        $colors->getCollection()->transform(function ($color) {
            $color->encrypted_id = $this->encryptId($color->color_id);
            return $color;
        });

        return MstColorResource::collection($colors);
    }

    public function store(StoreMstColorRequest $request): JsonResponse
    {
        $data = $request->validated();
        $color = MstColor::create($data);
        $color->encrypted_id = $this->encryptId($color->color_id);

        return (new MstColorResource($color))
            ->additional([
                'success' => true,
                'message' => 'Color created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $encrypted_id): JsonResponse
    {
        $color = $this->findByEncryptedId(MstColor::class, $encrypted_id, 'color_id');

        if (!$color) {
            return response()->json([
                'success' => false,
                'message' => 'Color not found.',
            ], 404);
        }

        $color->encrypted_id = $this->encryptId($color->color_id);

        return (new MstColorResource($color))
            ->additional([
                'success' => true,
                'message' => 'Color retrieved successfully.',
            ])
            ->response();
    }

    public function update(UpdateMstColorRequest $request, string $encrypted_id): JsonResponse
    {
        $color = $this->findByEncryptedId(MstColor::class, $encrypted_id, 'color_id');

        if (!$color) {
            return response()->json([
                'success' => false,
                'message' => 'Color not found.',
            ], 404);
        }

        $color->update($request->validated());
        $color->encrypted_id = $this->encryptId($color->color_id);

        return (new MstColorResource($color))
            ->additional([
                'success' => true,
                'message' => 'Color updated successfully.',
            ])
            ->response();
    }

    public function destroy(string $encrypted_id): JsonResponse
    {
        $color = $this->findByEncryptedId(MstColor::class, $encrypted_id, 'color_id');

        if (!$color) {
            return response()->json([
                'success' => false,
                'message' => 'Color not found.',
            ], 404);
        }

        $color->delete();

        return response()->json([
            'success' => true,
            'message' => 'Color deleted successfully.',
        ], 200);
    }
}