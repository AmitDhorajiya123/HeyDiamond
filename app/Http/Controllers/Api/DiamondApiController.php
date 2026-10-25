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
use Illuminate\Support\Facades\Http;

class DiamondApiController extends Controller
{
    /**
     * Display a listing of diamonds with pagination and filters.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Validate the request payload (filters are sent in the POST body).
        // Everything is bound with placeholders below, this is an extra guard
        // against malformed / tampered values.
        $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'symbolMode' => ['nullable', 'in:contains,not_contains,only_contains'],
            'symbols' => ['nullable'],
            'sort_by' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'in:asc,desc'],
        ]);

        $numericFilterFields = [
            'weight', 'pricePerCt', 'totalPrice', 'depth', 'table', 'ratio',
            'length', 'width', 'height', 'crownAngle', 'crownHeight',
            'pavilionAngle', 'pavilionDepth',
        ];

        foreach ($numericFilterFields as $numericField) {
            foreach (['Min', 'Max'] as $suffix) {
                $value = $request->input($numericField . $suffix);

                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    abort(422, "Invalid numeric value for {$numericField}{$suffix}.");
                }
            }
        }

        $query = Diamond::query();

        // Search by stockNo or reportNo
        $search = $request->input('search');

        if (is_string($search) && ($search = trim(mb_substr($search, 0, 120))) !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('stockNo', 'like', "%{$search}%")
                  ->orWhere('reportNo', 'like', "%{$search}%");
            });
        }

        // Filters - supports both single values and comma separated / array values
        $multiValueFilters = [
            'shape',
            'color',
            'clarity',
            'cut',
            'polish',
            'symmetry',
            'fluorescenceIntensity',
            'fluorescenceColor',
            'lab',
            'location',
            'status',
            'heartAndArrow',
            'shade',
            'milky',
            'blackInclusion',
            'whiteInclusion',
        ];

        foreach ($multiValueFilters as $field) {
            $value = $request->input($field);

            if ($value === null || $value === '') {
                continue;
            }

            $values = is_array($value) ? $value : explode(',', (string) $value);
            $values = array_filter($values, 'is_string');
            $values = array_slice($values, 0, 50);
            $values = array_values(array_filter(array_map(function ($item) {
                return trim(mb_substr($item, 0, 120));
            }, $values), function ($item) {
                return $item !== '';
            }));

            if (empty($values)) {
                continue;
            }

            if (count($values) === 1) {
                $query->where($field, $values[0]);
            } else {
                $query->whereIn($field, $values);
            }
        }

        // Numeric range filters (columns are stored as text, so cast them)
        $rangeFilters = [
            'weight', 'pricePerCt', 'totalPrice', 'depth', 'table', 'ratio',
            'length', 'width', 'height',
            'crownAngle', 'crownHeight', 'pavilionAngle', 'pavilionDepth',
        ];

        foreach ($rangeFilters as $field) {
            $min = $request->input($field . 'Min');
            $max = $request->input($field . 'Max');

            if ($min !== null && $min !== '') {
                $query->whereRaw("CAST(`{$field}` AS DECIMAL(12,3)) >= ?", [(float) $min]);
            }

            if ($max !== null && $max !== '') {
                $query->whereRaw("CAST(`{$field}` AS DECIMAL(12,3)) <= ?", [(float) $max]);
            }
        }

        // Key to Symbols (comma separated inclusion list, so use a LIKE search)
        $keyToSymbole = $request->input('keyToSymbole');

        if (is_string($keyToSymbole) && ($keyToSymbole = trim(mb_substr($keyToSymbole, 0, 120))) !== '') {
            $query->where('keyToSymbole', 'like', '%' . $keyToSymbole . '%');
        }

        // Stone availability flags
        if ($request->boolean('availableOnly')) {
            $query->where('status', 'Available');
        }
        if ($request->boolean('newOnly')) {
            $query->where('status', 'New');
        }
        if ($request->boolean('offerOnly')) {
            $query->where('status', 'Offer');
        }
        if ($request->boolean('upcomingOnly')) {
            $query->where('status', 'Upcoming');
        }
        if ($request->boolean('noBgm')) {
            $query->where(function ($q) {
                $q->whereNull('BGM')
                    ->orWhere('BGM', '')
                    ->orWhere('BGM', 'NO BGM');
            });
        }
        if ($request->boolean('noBlack')) {
            $query->where(function ($q) {
                $q->whereNull('blackInclusion')
                    ->orWhere('blackInclusion', '')
                    ->orWhere('blackInclusion', 'NO');
            });
        }

        // Key to Symbols checkboxes (contains / does not contain / only contains)
        $symbols = $request->input('symbols');
        $symbols = is_array($symbols) ? $symbols : ($symbols ? explode(',', (string) $symbols) : []);
        $symbols = array_filter($symbols, 'is_string');
        $symbols = array_slice($symbols, 0, 50);
        $symbols = array_values(array_filter(array_map(function ($symbol) {
            return trim(mb_substr($symbol, 0, 120));
        }, $symbols), function ($symbol) {
            return $symbol !== '';
        }));

        if (!empty($symbols)) {
            $symbolMode = $request->input('symbolMode', 'contains');

            if ($symbolMode === 'not_contains') {
                foreach ($symbols as $symbol) {
                    $query->whereRaw("COALESCE(`keyToSymbole`, '') NOT LIKE ?", ['%' . $symbol . '%']);
                }
            } elseif ($symbolMode === 'only_contains') {
                // Keep rows whose symbol list is made up of the selected symbols only
                $expression = "COALESCE(`keyToSymbole`, '')";
                $bindings = [];
                foreach ($symbols as $symbol) {
                    $expression = "REPLACE($expression, ?, '')";
                    $bindings[] = $symbol;
                }
                $query->whereNotNull('keyToSymbole')
                    ->where('keyToSymbole', '<>', '')
                    ->whereRaw("TRIM(BOTH ',' FROM TRIM(BOTH ' ' FROM $expression)) = ''", $bindings);
            } else {
                $query->where(function ($q) use ($symbols) {
                    foreach ($symbols as $symbol) {
                        $q->orWhere('keyToSymbole', 'like', '%' . $symbol . '%');
                    }
                });
            }
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

    /**
     * Sync diamonds from external API.
     */
    public function sync(): JsonResponse
    {
        $url = 'https://noamdiamonds.com/apis/api/getStockN?auth_key=h3hqs2oimfnl';

        try {
            // Disable SSL verification for local/development environments
            // In production, ensure proper CA certificates are configured
            $http = Http::timeout(120);
            
            if (app()->environment('local', 'development')) {
                $http->withoutVerifying();
            }
            
            $response = $http->post($url);

            if ($response->successful()) {
                $diamonds = $response->json();

                $count = 0;
                if (is_array($diamonds)) {
                    // Check if the array is wrapped in a key like 'data' or if it's direct
                    $data = isset($diamonds['data']) ? $diamonds['data'] : $diamonds;

                    foreach ($data as $item) {
                        if (isset($item['stockNo'])) {
                            Diamond::updateOrCreate(
                                ['stockNo' => $item['stockNo']],
                                $item
                            );
                            $count++;
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'message' => "Successfully fetched and stored {$count} diamonds.",
                        'data' => [
                            'synced_count' => $count,
                        ],
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data format received from API.',
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch data from API. Status: ' . $response->status(),
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
