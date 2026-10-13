<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreLocationRequest;
use App\Http\Requests\Api\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocationApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Location::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('location_code', 'like', "%{$search}%")
                  ->orWhere('location_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 15;

        return LocationResource::collection(
            $query->orderBy('display_order', 'asc')->orderBy('location_id', 'asc')->paginate($perPage)
        );
    }

    public function store(StoreLocationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_date'] = now();

        $location = Location::create($data);

        return (new LocationResource($location))
            ->additional([
                'success' => true,
                'message' => 'Location created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Location $location): JsonResponse
    {
        return (new LocationResource($location))
            ->additional([
                'success' => true,
                'message' => 'Location retrieved successfully.',
            ])
            ->response();
    }

    public function update(UpdateLocationRequest $request, Location $location): JsonResponse
    {
        $data = $request->validated();
        $data['modified_date'] = now();

        $location->update($data);

        return (new LocationResource($location))
            ->additional([
                'success' => true,
                'message' => 'Location updated successfully.',
            ])
            ->response();
    }

    public function destroy(Location $location): JsonResponse
    {
        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully.',
        ], 200);
    }
}
