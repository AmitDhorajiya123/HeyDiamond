<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDiamondRequest;
use App\Http\Requests\Api\UpdateDiamondRequest;
use App\Http\Resources\DiamondResource;
use App\Models\Diamond;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DiamondApiController extends Controller
{
    /**
     * Display a listing of diamonds with pagination and filters.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Diamond::query();

        // Search by stockNo or reportNo
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('stockNo', 'like', "%{$search}%")
                  ->orWhere('reportNo', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($shape = $request->input('shape')) {
            $query->where('shape', $shape);
        }
        if ($color = $request->input('color')) {
            $query->where('color', $color);
        }
        if ($clarity = $request->input('clarity')) {
            $query->where('clarity', $clarity);
        }
        if ($cut = $request->input('cut')) {
            $query->where('cut', $cut);
        }
        if ($lab = $request->input('lab')) {
            $query->where('lab', $lab);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = strtolower($request->input('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['id', 'stockNo', 'weight', 'pricePerCt', 'totalPrice', 'created_at'];

        if (in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 15;

        return DiamondResource::collection($query->paginate($perPage));
    }

    /**
     * Store a newly created diamond in storage.
     */
    public function store(StoreDiamondRequest $request): JsonResponse
    {
        $diamond = Diamond::create($request->validated());

        return (new DiamondResource($diamond))
            ->additional([
                'success' => true,
                'message' => 'Diamond created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified diamond.
     */
    public function show(Diamond $diamond): JsonResponse
    {
        return (new DiamondResource($diamond))
            ->additional([
                'success' => true,
                'message' => 'Diamond retrieved successfully.',
            ])
            ->response();
    }

    /**
     * Update the specified diamond in storage.
     */
    public function update(UpdateDiamondRequest $request, Diamond $diamond): JsonResponse
    {
        $diamond->update($request->validated());

        return (new DiamondResource($diamond))
            ->additional([
                'success' => true,
                'message' => 'Diamond updated successfully.',
            ])
            ->response();
    }

    /**
     * Remove the specified diamond from storage.
     */
    public function destroy(Diamond $diamond): JsonResponse
    {
        $diamond->delete();

        return response()->json([
            'success' => true,
            'message' => 'Diamond deleted successfully.',
        ], 200);
    }
}
