<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMstSubcategoryRequest;
use App\Http\Requests\Api\UpdateMstSubcategoryRequest;
use App\Http\Resources\MstSubcategoryResource;
use App\Models\MstSubcategory;
use App\Traits\EncryptedId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MstSubcategoryApiController extends Controller
{
    use EncryptedId;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MstSubcategory::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('subcategory_code', 'like', "%{$search}%")
                  ->orWhere('subcategory_name', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($isActive = $request->input('is_active')) {
            $query->where('is_active', $isActive);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 15;

        $subcategories = $query->orderBy('category_id')->orderBy('subcategory_id')->paginate($perPage);

        $subcategories->getCollection()->transform(function ($subcategory) {
            $subcategory->encrypted_id = $this->encryptId($subcategory->subcategory_id);
            return $subcategory;
        });

        return MstSubcategoryResource::collection($subcategories);
    }

    public function store(StoreMstSubcategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $subcategory = MstSubcategory::create($data);
        $subcategory->encrypted_id = $this->encryptId($subcategory->subcategory_id);

        return (new MstSubcategoryResource($subcategory))
            ->additional([
                'success' => true,
                'message' => 'Subcategory created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $encrypted_id): JsonResponse
    {
        $subcategory = $this->findByEncryptedId(MstSubcategory::class, $encrypted_id, 'subcategory_id');

        if (!$subcategory) {
            return response()->json([
                'success' => false,
                'message' => 'Subcategory not found.',
            ], 404);
        }

        $subcategory->encrypted_id = $this->encryptId($subcategory->subcategory_id);

        return (new MstSubcategoryResource($subcategory))
            ->additional([
                'success' => true,
                'message' => 'Subcategory retrieved successfully.',
            ])
            ->response();
    }

    public function update(UpdateMstSubcategoryRequest $request, string $encrypted_id): JsonResponse
    {
        $subcategory = $this->findByEncryptedId(MstSubcategory::class, $encrypted_id, 'subcategory_id');

        if (!$subcategory) {
            return response()->json([
                'success' => false,
                'message' => 'Subcategory not found.',
            ], 404);
        }

        $subcategory->update($request->validated());
        $subcategory->encrypted_id = $this->encryptId($subcategory->subcategory_id);

        return (new MstSubcategoryResource($subcategory))
            ->additional([
                'success' => true,
                'message' => 'Subcategory updated successfully.',
            ])
            ->response();
    }

    public function destroy(string $encrypted_id): JsonResponse
    {
        $subcategory = $this->findByEncryptedId(MstSubcategory::class, $encrypted_id, 'subcategory_id');

        if (!$subcategory) {
            return response()->json([
                'success' => false,
                'message' => 'Subcategory not found.',
            ], 404);
        }

        $subcategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subcategory deleted successfully.',
        ], 200);
    }
}