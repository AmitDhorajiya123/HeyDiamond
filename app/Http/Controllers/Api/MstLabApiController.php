<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMstLabRequest;
use App\Http\Requests\Api\UpdateMstLabRequest;
use App\Http\Resources\MstLabResource;
use App\Models\MstLab;
use App\Traits\EncryptedId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MstLabApiController extends Controller
{
    use EncryptedId;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MstLab::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('lab_code', 'like', "%{$search}%")
                  ->orWhere('lab_name', 'like', "%{$search}%");
            });
        }

        if ($isActive = $request->input('is_active')) {
            $query->where('is_active', $isActive);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 15;

        $labs = $query->orderBy('display_order')->orderBy('lab_id')->paginate($perPage);

        $labs->getCollection()->transform(function ($lab) {
            $lab->encrypted_id = $this->encryptId($lab->lab_id);
            return $lab;
        });

        return MstLabResource::collection($labs);
    }

    public function store(StoreMstLabRequest $request): JsonResponse
    {
        $data = $request->validated();
        $lab = MstLab::create($data);
        $lab->encrypted_id = $this->encryptId($lab->lab_id);

        return (new MstLabResource($lab))
            ->additional([
                'success' => true,
                'message' => 'Lab created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $encrypted_id): JsonResponse
    {
        $lab = $this->findByEncryptedId(MstLab::class, $encrypted_id, 'lab_id');

        if (!$lab) {
            return response()->json([
                'success' => false,
                'message' => 'Lab not found.',
            ], 404);
        }

        $lab->encrypted_id = $this->encryptId($lab->lab_id);

        return (new MstLabResource($lab))
            ->additional([
                'success' => true,
                'message' => 'Lab retrieved successfully.',
            ])
            ->response();
    }

    public function update(UpdateMstLabRequest $request, string $encrypted_id): JsonResponse
    {
        $lab = $this->findByEncryptedId(MstLab::class, $encrypted_id, 'lab_id');

        if (!$lab) {
            return response()->json([
                'success' => false,
                'message' => 'Lab not found.',
            ], 404);
        }

        $lab->update($request->validated());
        $lab->encrypted_id = $this->encryptId($lab->lab_id);

        return (new MstLabResource($lab))
            ->additional([
                'success' => true,
                'message' => 'Lab updated successfully.',
            ])
            ->response();
    }

    public function destroy(string $encrypted_id): JsonResponse
    {
        $lab = $this->findByEncryptedId(MstLab::class, $encrypted_id, 'lab_id');

        if (!$lab) {
            return response()->json([
                'success' => false,
                'message' => 'Lab not found.',
            ], 404);
        }

        $lab->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lab deleted successfully.',
        ], 200);
    }
}