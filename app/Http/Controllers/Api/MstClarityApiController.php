<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMstClarityRequest;
use App\Http\Requests\Api\UpdateMstClarityRequest;
use App\Http\Resources\MstClarityResource;
use App\Models\MstClarity;
use App\Traits\EncryptedId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MstClarityApiController extends Controller
{
    use EncryptedId;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MstClarity::query();

        if ($search = $request->input('search')) {
            $query->where('clarity_code', 'like', "%{$search}%");
        }

        if ($isActive = $request->input('is_active')) {
            $query->where('is_active', $isActive);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 15;

        $clarities = $query->orderBy('display_order')->orderBy('clarity_id')->paginate($perPage);

        $clarities->getCollection()->transform(function ($clarity) {
            $clarity->encrypted_id = $this->encryptId($clarity->clarity_id);
            return $clarity;
        });

        return MstClarityResource::collection($clarities);
    }

    public function store(StoreMstClarityRequest $request): JsonResponse
    {
        $data = $request->validated();
        $clarity = MstClarity::create($data);
        $clarity->encrypted_id = $this->encryptId($clarity->clarity_id);

        return (new MstClarityResource($clarity))
            ->additional([
                'success' => true,
                'message' => 'Clarity created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $encrypted_id): JsonResponse
    {
        $clarity = $this->findByEncryptedId(MstClarity::class, $encrypted_id, 'clarity_id');

        if (!$clarity) {
            return response()->json([
                'success' => false,
                'message' => 'Clarity not found.',
            ], 404);
        }

        $clarity->encrypted_id = $this->encryptId($clarity->clarity_id);

        return (new MstClarityResource($clarity))
            ->additional([
                'success' => true,
                'message' => 'Clarity retrieved successfully.',
            ])
            ->response();
    }

    public function update(UpdateMstClarityRequest $request, string $encrypted_id): JsonResponse
    {
        $clarity = $this->findByEncryptedId(MstClarity::class, $encrypted_id, 'clarity_id');

        if (!$clarity) {
            return response()->json([
                'success' => false,
                'message' => 'Clarity not found.',
            ], 404);
        }

        $clarity->update($request->validated());
        $clarity->encrypted_id = $this->encryptId($clarity->clarity_id);

        return (new MstClarityResource($clarity))
            ->additional([
                'success' => true,
                'message' => 'Clarity updated successfully.',
            ])
            ->response();
    }

    public function destroy(string $encrypted_id): JsonResponse
    {
        $clarity = $this->findByEncryptedId(MstClarity::class, $encrypted_id, 'clarity_id');

        if (!$clarity) {
            return response()->json([
                'success' => false,
                'message' => 'Clarity not found.',
            ], 404);
        }

        $clarity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Clarity deleted successfully.',
        ], 200);
    }
}