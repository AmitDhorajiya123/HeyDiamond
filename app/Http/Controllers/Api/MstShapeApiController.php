<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMstShapeRequest;
use App\Http\Requests\Api\UpdateMstShapeRequest;
use App\Http\Resources\MstShapeResource;
use App\Models\MstShape;
use App\Traits\EncryptedId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MstShapeApiController extends Controller
{
    use EncryptedId;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MstShape::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('shape_code', 'like', "%{$search}%")
                  ->orWhere('shape_name', 'like', "%{$search}%");
            });
        }

        if ($isActive = $request->input('is_active')) {
            $query->where('is_active', $isActive);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 15;

        $shapes = $query->orderBy('display_order')->orderBy('shape_id')->paginate($perPage);

        // Add encrypted_id to each item
        $shapes->getCollection()->transform(function ($shape) {
            $shape->encrypted_id = $this->encryptId($shape->shape_id);
            return $shape;
        });

        return MstShapeResource::collection($shapes);
    }

    public function store(StoreMstShapeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $shape = MstShape::create($data);
        $shape->encrypted_id = $this->encryptId($shape->shape_id);

        return (new MstShapeResource($shape))
            ->additional([
                'success' => true,
                'message' => 'Shape created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $encrypted_id): JsonResponse
    {
        $shape = $this->findByEncryptedId(MstShape::class, $encrypted_id, 'shape_id');

        if (!$shape) {
            return response()->json([
                'success' => false,
                'message' => 'Shape not found.',
            ], 404);
        }

        $shape->encrypted_id = $this->encryptId($shape->shape_id);

        return (new MstShapeResource($shape))
            ->additional([
                'success' => true,
                'message' => 'Shape retrieved successfully.',
            ])
            ->response();
    }

    public function update(UpdateMstShapeRequest $request, string $encrypted_id): JsonResponse
    {
        $shape = $this->findByEncryptedId(MstShape::class, $encrypted_id, 'shape_id');

        if (!$shape) {
            return response()->json([
                'success' => false,
                'message' => 'Shape not found.',
            ], 404);
        }

        $shape->update($request->validated());
        $shape->encrypted_id = $this->encryptId($shape->shape_id);

        return (new MstShapeResource($shape))
            ->additional([
                'success' => true,
                'message' => 'Shape updated successfully.',
            ])
            ->response();
    }

    public function destroy(string $encrypted_id): JsonResponse
    {
        $shape = $this->findByEncryptedId(MstShape::class, $encrypted_id, 'shape_id');

        if (!$shape) {
            return response()->json([
                'success' => false,
                'message' => 'Shape not found.',
            ], 404);
        }

        $shape->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shape deleted successfully.',
        ], 200);
    }
}