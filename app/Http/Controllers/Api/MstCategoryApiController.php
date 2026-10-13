<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMstCategoryRequest;
use App\Http\Requests\Api\UpdateMstCategoryRequest;
use App\Http\Resources\MstCategoryResource;
use App\Models\MstCategory;
use App\Traits\EncryptedId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MstCategoryApiController extends Controller
{
    use EncryptedId;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MstCategory::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('category_code', 'like', "%{$search}%")
                  ->orWhere('category_name', 'like', "%{$search}%");
            });
        }

        if ($isActive = $request->input('is_active')) {
            $query->where('is_active', $isActive);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 15;

        $categories = $query->orderBy('category_id')->paginate($perPage);

        $categories->getCollection()->transform(function ($category) {
            $category->encrypted_id = $this->encryptId($category->category_id);
            return $category;
        });

        return MstCategoryResource::collection($categories);
    }

    public function store(StoreMstCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $category = MstCategory::create($data);
        $category->encrypted_id = $this->encryptId($category->category_id);

        return (new MstCategoryResource($category))
            ->additional([
                'success' => true,
                'message' => 'Category created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $encrypted_id): JsonResponse
    {
        $category = $this->findByEncryptedId(MstCategory::class, $encrypted_id, 'category_id');

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        $category->encrypted_id = $this->encryptId($category->category_id);

        return (new MstCategoryResource($category))
            ->additional([
                'success' => true,
                'message' => 'Category retrieved successfully.',
            ])
            ->response();
    }

    public function update(UpdateMstCategoryRequest $request, string $encrypted_id): JsonResponse
    {
        $category = $this->findByEncryptedId(MstCategory::class, $encrypted_id, 'category_id');

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        $category->update($request->validated());
        $category->encrypted_id = $this->encryptId($category->category_id);

        return (new MstCategoryResource($category))
            ->additional([
                'success' => true,
                'message' => 'Category updated successfully.',
            ])
            ->response();
    }

    public function destroy(string $encrypted_id): JsonResponse
    {
        $category = $this->findByEncryptedId(MstCategory::class, $encrypted_id, 'category_id');

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ], 200);
    }
}