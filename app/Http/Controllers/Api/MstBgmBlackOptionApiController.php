<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMstBgmBlackOptionRequest;
use App\Http\Requests\Api\UpdateMstBgmBlackOptionRequest;
use App\Http\Resources\MstBgmBlackOptionResource;
use App\Models\MstBgmBlackOption;
use App\Traits\EncryptedId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MstBgmBlackOptionApiController extends Controller
{
    use EncryptedId;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MstBgmBlackOption::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('option_code', 'like', "%{$search}%")
                  ->orWhere('option_name', 'like', "%{$search}%");
            });
        }

        if ($isActive = $request->input('is_active')) {
            $query->where('is_active', $isActive);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 15;

        $options = $query->orderBy('option_id')->paginate($perPage);

        $options->getCollection()->transform(function ($option) {
            $option->encrypted_id = $this->encryptId($option->option_id);
            return $option;
        });

        return MstBgmBlackOptionResource::collection($options);
    }

    public function store(StoreMstBgmBlackOptionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $option = MstBgmBlackOption::create($data);
        $option->encrypted_id = $this->encryptId($option->option_id);

        return (new MstBgmBlackOptionResource($option))
            ->additional([
                'success' => true,
                'message' => 'BGM/Black option created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $encrypted_id): JsonResponse
    {
        $option = $this->findByEncryptedId(MstBgmBlackOption::class, $encrypted_id, 'option_id');

        if (!$option) {
            return response()->json([
                'success' => false,
                'message' => 'BGM/Black option not found.',
            ], 404);
        }

        $option->encrypted_id = $this->encryptId($option->option_id);

        return (new MstBgmBlackOptionResource($option))
            ->additional([
                'success' => true,
                'message' => 'BGM/Black option retrieved successfully.',
            ])
            ->response();
    }

    public function update(UpdateMstBgmBlackOptionRequest $request, string $encrypted_id): JsonResponse
    {
        $option = $this->findByEncryptedId(MstBgmBlackOption::class, $encrypted_id, 'option_id');

        if (!$option) {
            return response()->json([
                'success' => false,
                'message' => 'BGM/Black option not found.',
            ], 404);
        }

        $option->update($request->validated());
        $option->encrypted_id = $this->encryptId($option->option_id);

        return (new MstBgmBlackOptionResource($option))
            ->additional([
                'success' => true,
                'message' => 'BGM/Black option updated successfully.',
            ])
            ->response();
    }

    public function destroy(string $encrypted_id): JsonResponse
    {
        $option = $this->findByEncryptedId(MstBgmBlackOption::class, $encrypted_id, 'option_id');

        if (!$option) {
            return response()->json([
                'success' => false,
                'message' => 'BGM/Black option not found.',
            ], 404);
        }

        $option->delete();

        return response()->json([
            'success' => true,
            'message' => 'BGM/Black option deleted successfully.',
        ], 200);
    }
}