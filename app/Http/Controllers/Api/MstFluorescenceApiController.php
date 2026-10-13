<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMstFluorescenceRequest;
use App\Http\Requests\Api\UpdateMstFluorescenceRequest;
use App\Http\Resources\MstFluorescenceResource;
use App\Models\MstFluorescence;
use App\Traits\EncryptedId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MstFluorescenceApiController extends Controller
{
    use EncryptedId;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MstFluorescence::query();

        if ($search = $request->input('search')) {
            $query->where('fluorescence_code', 'like', "%{$search}%");
        }

        if ($isActive = $request->input('is_active')) {
            $query->where('is_active', $isActive);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 15;

        $fluorescences = $query->orderBy('display_order')->orderBy('fluorescence_id')->paginate($perPage);

        $fluorescences->getCollection()->transform(function ($fluorescence) {
            $fluorescence->encrypted_id = $this->encryptId($fluorescence->fluorescence_id);
            return $fluorescence;
        });

        return MstFluorescenceResource::collection($fluorescences);
    }

    public function store(StoreMstFluorescenceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $fluorescence = MstFluorescence::create($data);
        $fluorescence->encrypted_id = $this->encryptId($fluorescence->fluorescence_id);

        return (new MstFluorescenceResource($fluorescence))
            ->additional([
                'success' => true,
                'message' => 'Fluorescence created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $encrypted_id): JsonResponse
    {
        $fluorescence = $this->findByEncryptedId(MstFluorescence::class, $encrypted_id, 'fluorescence_id');

        if (!$fluorescence) {
            return response()->json([
                'success' => false,
                'message' => 'Fluorescence not found.',
            ], 404);
        }

        $fluorescence->encrypted_id = $this->encryptId($fluorescence->fluorescence_id);

        return (new MstFluorescenceResource($fluorescence))
            ->additional([
                'success' => true,
                'message' => 'Fluorescence retrieved successfully.',
            ])
            ->response();
    }

    public function update(UpdateMstFluorescenceRequest $request, string $encrypted_id): JsonResponse
    {
        $fluorescence = $this->findByEncryptedId(MstFluorescence::class, $encrypted_id, 'fluorescence_id');

        if (!$fluorescence) {
            return response()->json([
                'success' => false,
                'message' => 'Fluorescence not found.',
            ], 404);
        }

        $fluorescence->update($request->validated());
        $fluorescence->encrypted_id = $this->encryptId($fluorescence->fluorescence_id);

        return (new MstFluorescenceResource($fluorescence))
            ->additional([
                'success' => true,
                'message' => 'Fluorescence updated successfully.',
            ])
            ->response();
    }

    public function destroy(string $encrypted_id): JsonResponse
    {
        $fluorescence = $this->findByEncryptedId(MstFluorescence::class, $encrypted_id, 'fluorescence_id');

        if (!$fluorescence) {
            return response()->json([
                'success' => false,
                'message' => 'Fluorescence not found.',
            ], 404);
        }

        $fluorescence->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fluorescence deleted successfully.',
        ], 200);
    }
}