<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDiamondRequest;
use App\Http\Requests\Api\UpdateDiamondRequest;
use App\Http\Resources\DiamondResource;
use App\Models\ApiClient;
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
            'search' => ['nullable', 'max:120'],
            'colorMode' => ['nullable', 'in:white,fancy'],
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

        // Filter page "Stock ID / Certificate #" search (stockNo OR reportNo only)
        $stockSearch = $request->input('stockSearch');

        if (is_string($stockSearch) && ($stockSearch = trim(mb_substr($stockSearch, 0, 120))) !== '') {
            $query->where(function ($q) use ($stockSearch) {
                $q->where('stockNo', 'like', "%{$stockSearch}%")
                  ->orWhere('reportNo', 'like', "%{$stockSearch}%");
            });
        }

        // Global search across every diamond column (supports multi-word queries)
        $search = $request->input('search');

        // Accept both ?search=term and Yajra's ?search[value]=term
        if (is_array($search)) {
            $search = $search['value'] ?? '';
        }

        $search = is_string($search) ? trim(mb_substr($search, 0, 120)) : '';

        if ($search !== '') {
            $searchColumns = [
                'id', 'stockNo', 'status', 'shape', 'Diamond_Type', 'weight', 'color',
                'clarity', 'cut', 'polish', 'symmetry', 'fluorescenceIntensity',
                'fluorescenceColor', 'measurements', 'shade', 'BGM', 'milky', 'eyeClean',
                'lab', 'reportNo', 'reportDate', 'location', 'treatment', 'ratio',
                'growthType', 'rapPricePerCt', 'depth', 'table', 'girdleThin',
                'girdleThick', 'gridlePer', 'girdleCondition', 'culetSize',
                'ciletCondition', 'crownHeight', 'crownAngle', 'pavilionDepth',
                'pavilionAngle', 'starLength', 'inscription', 'laserInscription',
                'certiComment', 'keyToSymbole', 'inclusion', 'whiteInclusion',
                'blackInclusion', 'openInclusion', 'fancyColor', 'fancyColorIntensity',
                'fancyColorOvertone', 'country', 'state', 'city', 'certiFile',
                'videoLink', 'vdbVideo', 'imageLink', 'heartImage', 'arrowImage',
                'assetImage', 'heartAndArrow', 'luster', 'brand', 'origin', 'COP',
                'DNA', 'handyVideo', 'supplierComment', 'tinge', 'memberComment',
                'isPair', 'pairStock', 'tweezerVideo', 'length', 'height', 'width',
                'pricePerCt', 'totalPrice', 'discount', 'exclusive', 'created_at',
                'updated_at',
            ];

            $terms = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $terms = array_slice($terms, 0, 5);

            if (!empty($terms)) {
                // Every term must match at least one column (e.g. "round vs1")
                $query->where(function ($q) use ($searchColumns, $terms) {
                    foreach ($terms as $term) {
                        $q->where(function ($subQuery) use ($searchColumns, $term) {
                            foreach ($searchColumns as $column) {
                                $subQuery->orWhere($column, 'like', '%' . $term . '%');
                            }
                        });
                    }
                });
            }
        }

        // Color mode: "white" grades (D-Z) or "fancy" named colors.
        // The filter page sends this alongside the selected color values.
        $colorMode = $request->input('colorMode');
        $colorMode = in_array($colorMode, ['white', 'fancy'], true) ? $colorMode : null;

        $colorInput = $request->input('color');
        $colorValues = is_array($colorInput)
            ? $colorInput
            : ($colorInput !== null && $colorInput !== '' ? explode(',', (string) $colorInput) : []);
        $colorValues = array_filter($colorValues, 'is_string');
        $colorValues = array_slice($colorValues, 0, 50);
        $colorValues = array_values(array_filter(array_map(function ($item) {
            return trim(mb_substr($item, 0, 120));
        }, $colorValues), function ($item) {
            return $item !== '';
        }));
        $colorValues = array_map('strtoupper', $colorValues);

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
            'fancyColorIntensity',
            'location',
            'status',
            'heartAndArrow',
            'shade',
            'milky',
            'blackInclusion',
            'whiteInclusion',
        ];

        $fancyIntensityValues = [];

        foreach ($multiValueFilters as $field) {
            if ($field === 'color' && $colorMode !== null) {
                continue;
            }

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

            // Cut / Polish / Symmetry: the filter page shows friendly grades
            // (8X, Ideal, Excellent, ...) while the diamonds table stores short
            // codes (IDEAL, ID, EX, VG, ...). Expand each selection so both the
            // friendly label and its stored codes are matched.
            if (in_array($field, ['cut', 'polish', 'symmetry'], true)) {
                $finishGradeMap = [
                    '8X' => ['8X'],
                    'IDEAL' => ['IDEAL', 'ID'],
                    'EXCELLENT' => ['EX', 'EXCELLENT'],
                    'VERY GOOD' => ['VG', 'VERY GOOD'],
                    'GOOD' => ['GD', 'GOOD'],
                    'FAIR' => ['FR', 'FAIR'],
                    'POOR' => ['PR', 'POOR'],
                ];

                $expanded = [];

                foreach ($values as $grade) {
                    $expanded[] = $grade;
                    $key = strtoupper(trim($grade));

                    if (isset($finishGradeMap[$key])) {
                        $expanded = array_merge($expanded, $finishGradeMap[$key]);
                    }
                }

                $values = array_values(array_unique($expanded));
            }

            if ($field === 'fancyColorIntensity') {
                $fancyIntensityValues = $values;
            }

            if (count($values) === 1) {
                $query->where($field, $values[0]);
            } else {
                $query->whereIn($field, $values);
            }
        }

        // API source filter (used by the data listing page to show one API's data)
        $apiClientId = $request->input('api_client_id');

        if ($apiClientId) {
            $query->where('api_client_id', $apiClientId);
        }

        // Country / state / city are single location values and may contain commas.
        foreach (['country', 'state', 'city'] as $locationField) {
            $value = $request->input($locationField);

            if (is_array($value)) {
                $values = array_values(array_filter($value, 'is_string'));
                $values = array_slice($values, 0, 50);
                $values = array_values(array_filter(array_map(function ($item) {
                    return trim(mb_substr($item, 0, 120));
                }, $values), function ($item) {
                    return $item !== '';
                }));

                if (!empty($values)) {
                    $query->whereIn($locationField, $values);
                }

                continue;
            }

            if ($value !== null && $value !== '') {
                $normalized = trim(mb_substr((string) $value, 0, 120));

                if ($normalized !== '') {
                    $query->where($locationField, $normalized);
                }
            }
        }

        // White color grades (D-Z) vs. named fancy colors.
        if ($colorMode === 'white') {
            $whiteColors = [
                'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
                'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
                'W', 'X', 'Y', 'Z',
            ];

            $selectedColors = array_values(array_intersect(
                $colorValues,
                array_merge($whiteColors, ['W-Z'])
            ));

            if (in_array('W-Z', $selectedColors, true)) {
                $selectedColors = array_merge(
                    array_diff($selectedColors, ['W-Z']),
                    ['W', 'X', 'Y', 'Z']
                );
            }

            $selectedColors = array_values(array_unique($selectedColors));

            // No color selected in White mode = no color restriction, so
            // location / shape / clarity / cut filters also return fancy-color
            // stones. Selecting specific white grades still filters D-Z.
            if (!empty($selectedColors)) {
                $query->whereIn('color', $selectedColors);
            }
        } elseif ($colorMode === 'fancy') {
            $fancyColors = [
                'YELLOW', 'ORANGE', 'PINK', 'BLUE', 'GREEN', 'CHAMPAGNE',
                'BROWN', 'RED', 'WHITE', 'VIOLET', 'PURPLE', 'GRAY',
                'OLIVE', 'BLACK', 'ORANGY YELLOW', 'ORANGE BROWN',
                'PINK BROWN', 'GREEN YELLOW', 'BROWN YELLOW', 'COGNAC',
                'CHAMELEON',
            ];

            $selectedColors = array_values(array_intersect(
                $colorValues,
                array_merge($fancyColors, ['OTHER'])
            ));

            if (empty($selectedColors)) {
                // All fancy colors: a named fancy color, or a FANCY* color value.
                $query->where(function ($q) use ($fancyColors) {
                    $q->where(function ($sub) {
                        $sub->whereNotNull('fancyColor')
                            ->where('fancyColor', '!=', '');
                    })
                        ->orWhereIn('color', $fancyColors)
                        ->orWhere('color', 'like', 'FANCY%');
                });
            } else {
                $query->where(function ($q) use ($selectedColors, $fancyColors) {
                    foreach ($selectedColors as $color) {
                        if ($color === 'OTHER') {
                            $q->orWhere(function ($sub) use ($fancyColors) {
                                $sub->where(function ($fancy) use ($fancyColors) {
                                    $fancy->whereNotNull('fancyColor')
                                        ->where('fancyColor', '!=', '')
                                        ->whereNotIn('fancyColor', $fancyColors);
                                })->orWhere(function ($fancy) use ($fancyColors) {
                                    $fancy->where(function ($emptyFancy) {
                                        $emptyFancy->whereNull('fancyColor')
                                            ->orWhere('fancyColor', '');
                                    })->where('color', 'like', 'FANCY%');
                                    foreach ($fancyColors as $knownColor) {
                                        $fancy->where('color', 'not like', '%' . $knownColor . '%');
                                    }
                                });
                            });
                        } else {
                            $q->orWhere(function ($sub) use ($color) {
                                $sub->where('fancyColor', $color)
                                    ->orWhere('color', 'like', '%' . $color . '%');
                            });
                        }
                    }
                });
            }
        }

        // Lab: known labs, "None" for empty, "Other" for anything else.
        $labInput = $request->input('lab');
        $labValues = is_array($labInput)
            ? $labInput
            : ($labInput !== null && $labInput !== '' ? explode(',', (string) $labInput) : []);
        $labValues = array_filter($labValues, 'is_string');
        $labValues = array_slice($labValues, 0, 50);
        $labValues = array_values(array_filter(array_map(function ($item) {
            return trim(mb_substr($item, 0, 120));
        }, $labValues), function ($item) {
            return $item !== '';
        }));

        if (!empty($labValues)) {
            $knownLabs = ['IGI', 'GIA', 'GCAL', 'SGL'];
            $labLookup = array_map('strtoupper', $labValues);
            $exactLabs = array_values(array_diff($labLookup, ['NONE', 'OTHER']));
            $selectedKnownLabs = array_values(array_intersect($exactLabs, $knownLabs));
            $selectedExactLabs = array_values(array_diff($exactLabs, $knownLabs));
            $hasNoneLab = in_array('NONE', $labLookup, true);
            $hasOtherLab = in_array('OTHER', $labLookup, true);

            $query->where(function ($q) use (
                $selectedKnownLabs,
                $selectedExactLabs,
                $hasNoneLab,
                $hasOtherLab,
                $knownLabs
            ) {
                if (!empty($selectedKnownLabs)) {
                    $q->orWhereIn('lab', $selectedKnownLabs);
                }

                if (!empty($selectedExactLabs)) {
                    $q->orWhereIn('lab', $selectedExactLabs);
                }

                if ($hasNoneLab) {
                    $q->orWhere(function ($sub) {
                        $sub->whereNull('lab')->orWhere('lab', '');
                    });
                }

                if ($hasOtherLab) {
                    $q->orWhere(function ($sub) use ($knownLabs) {
                        $sub->whereNotNull('lab')
                            ->where('lab', '!=', '')
                            ->whereNotIn('lab', $knownLabs);
                    });
                }
            });
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
     * Distinct country / state / city values used by the filter page.
     */
    public function locationOptions(): JsonResponse
    {
        $distinctValues = function (string $column) {
            return Diamond::query()
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->distinct()
                ->orderBy($column)
                ->pluck($column)
                ->values();
        };

        return response()->json([
            'success' => true,
            'data' => [
                'countries' => $distinctValues('country'),
                'states' => $distinctValues('state'),
                'cities' => $distinctValues('city'),
            ],
        ]);
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
     * Helper to sync diamonds from a single client URL.
     */
    private function syncFromUrl(string $url, ?int $apiClientId = null): int
    {
        $http = Http::timeout(120);
        if (app()->environment('local', 'development')) {
            $http->withoutVerifying();
        }

        // Try GET first, fallback to POST if 405 or unsuccessful
        $response = $http->get($url);
        if (!$response->successful()) {
            $response = $http->post($url);
        }

        if (!$response->successful()) {
            throw new \Exception("Failed to fetch data from API ({$url}). Status: " . $response->status());
        }

        $diamonds = $response->json();
        if (!is_array($diamonds)) {
            throw new \Exception("Invalid data format received from API ({$url}).");
        }

        if (isset($diamonds['api_response']) && is_array($diamonds['api_response'])) {
            $data = $diamonds['api_response'];
        } elseif (isset($diamonds['data']) && is_array($diamonds['data'])) {
            $data = $diamonds['data'];
        } else {
            $data = $diamonds;
        }

        $fillableColumns = [
            'stockNo', 'status', 'shape', 'Diamond_Type', 'weight', 'color', 'clarity',
            'cut', 'polish', 'symmetry', 'fluorescenceIntensity', 'fluorescenceColor',
            'measurements', 'shade', 'BGM', 'milky', 'eyeClean', 'lab', 'reportNo',
            'reportDate', 'location', 'treatment', 'ratio', 'growthType', 'rapPricePerCt',
            'depth', 'table', 'girdleThin', 'girdleThick', 'gridlePer', 'girdleCondition',
            'culetSize', 'ciletCondition', 'crownHeight', 'crownAngle', 'pavilionDepth',
            'pavilionAngle', 'starLength', 'inscription', 'laserInscription', 'certiComment',
            'keyToSymbole', 'inclusion', 'whiteInclusion', 'blackInclusion', 'openInclusion',
            'fancyColor', 'fancyColorIntensity', 'fancyColorOvertone', 'country', 'state',
            'city', 'certiFile', 'videoLink', 'vdbVideo', 'imageLink', 'heartImage',
            'arrowImage', 'assetImage', 'heartAndArrow', 'luster', 'brand', 'origin',
            'api_client_id', 'COP', 'DNA', 'handyVideo', 'supplierComment', 'tinge', 'memberComment',
            'isPair', 'pairStock', 'tweezerVideo', 'length', 'height', 'width',
            'pricePerCt', 'totalPrice', 'discount', 'exclusive',
        ];

        $count = 0;
        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }

            $stockNo = $item['stockNo'] ?? $item['MFGID'] ?? $item['ReportNo'] ?? null;
            if (!$stockNo) {
                continue;
            }

            if (!isset($item['stockNo'])) {
                $mappedData = [
                    'stockNo'               => (string) $stockNo,
                    'status'                => $item['StnStatus'] ?? null,
                    'shape'                 => $item['Shape'] ?? null,
                    'weight'                => isset($item['Cts']) ? (string) $item['Cts'] : null,
                    'color'                 => $item['Color'] ?? null,
                    'clarity'               => $item['Purity'] ?? null,
                    'cut'                   => $item['Cut'] ?? null,
                    'polish'                => $item['Polish'] ?? null,
                    'symmetry'              => $item['Symm'] ?? null,
                    'fluorescenceIntensity' => $item['Fls'] ?? null,
                    'measurements'          => $item['Measurements'] ?? null,
                    'BGM'                   => $item['BGM'] ?? null,
                    'eyeClean'              => $item['EyeClean'] ?? null,
                    'lab'                   => $item['Lab'] ?? null,
                    'reportNo'              => isset($item['ReportNo']) ? (string) $item['ReportNo'] : null,
                    'country'               => $item['Country'] ?? null,
                    'treatment'             => $item['Treatment_value'] ?? $item['Treatment value'] ?? null,
                    'ratio'                 => isset($item['RATIO']) ? (string) $item['RATIO'] : null,
                    'rapPricePerCt'         => isset($item['RapValue']) ? (string) $item['RapValue'] : null,
                    'depth'                 => isset($item['Depth']) ? (string) $item['Depth'] : null,
                    'table'                 => isset($item['TablePer']) ? (string) $item['TablePer'] : null,
                    'gridlePer'             => isset($item['DepthPer']) ? (string) $item['DepthPer'] : null,
                    'girdleCondition'       => $item['Girdle'] ?? null,
                    'culetSize'             => $item['Culet'] ?? null,
                    'crownHeight'           => isset($item['CH']) ? (string) $item['CH'] : null,
                    'crownAngle'            => isset($item['CA']) ? (string) $item['CA'] : null,
                    'pavilionDepth'         => isset($item['PH']) ? (string) $item['PH'] : null,
                    'pavilionAngle'         => isset($item['PA']) ? (string) $item['PA'] : null,
                    'certiFile'             => $item['ReportLink'] ?? null,
                    'videoLink'             => $item['VideoLink'] ?? null,
                    'imageLink'             => $item['ImageLink'] ?? null,
                    'heartAndArrow'         => $item['H&A'] ?? null,
                    'length'                => isset($item['Length']) ? (string) $item['Length'] : null,
                    'width'                 => isset($item['Width']) ? (string) $item['Width'] : null,
                    'pricePerCt'            => isset($item['PartyRate']) ? (string) $item['PartyRate'] : null,
                    'totalPrice'            => isset($item['PartyValue']) ? (string) $item['PartyValue'] : null,
                    'discount'              => isset($item['PartyDisc']) ? (string) $item['PartyDisc'] : null,
                ];
            } else {
                $mappedData = array_intersect_key($item, array_flip($fillableColumns));
            }

            $mappedData['api_client_id'] = $apiClientId;

            Diamond::updateOrCreate(
                ['stockNo' => (string) $stockNo],
                $mappedData
            );
            $count++;
        }

        return $count;
    }

    /**
     * Sync diamonds from external API.
     */
    public function sync(Request $request): JsonResponse
    {
        $apiClientId = $request->input('api_client_id');
        $url = $request->input('source_url');

        try {
            // Case 1: Specific api_client_id requested
            if (!empty($apiClientId)) {
                $apiClient = ApiClient::find((int) $apiClientId);
                if (!$apiClient) {
                    return response()->json([
                        'success' => false,
                        'message' => 'API client not found.',
                    ], 404);
                }

                $count = $this->syncFromUrl($apiClient->url, (int) $apiClient->id);
                return response()->json([
                    'success' => true,
                    'message' => "Successfully synced {$count} diamonds from {$apiClient->name}.",
                    'data' => ['synced_count' => $count],
                ]);
            }

            // Case 2: Specific source_url provided
            if (!empty($url)) {
                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid API URL provided.',
                    ], 422);
                }

                $apiClient = ApiClient::where('url', $url)->first();
                $count = $this->syncFromUrl($url, $apiClient?->id);
                return response()->json([
                    'success' => true,
                    'message' => "Successfully synced {$count} diamonds.",
                    'data' => ['synced_count' => $count],
                ]);
            }

            // Case 3: No specific API requested -> sync ALL registered api_clients
            $clients = ApiClient::all();
            if ($clients->isNotEmpty()) {
                $totalCount = 0;
                $syncedClients = [];
                $errors = [];

                foreach ($clients as $client) {
                    if (empty($client->url)) continue;
                    try {
                        $c = $this->syncFromUrl($client->url, (int) $client->id);
                        $totalCount += $c;
                        $syncedClients[] = "{$client->name} ({$c})";
                    } catch (\Exception $ex) {
                        $errors[] = "{$client->name}: {$ex->getMessage()}";
                    }
                }

                $message = "Successfully synced {$totalCount} diamonds (" . implode(', ', $syncedClients) . ").";
                if (!empty($errors)) {
                    $message .= " Warnings: " . implode('; ', $errors);
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => ['synced_count' => $totalCount],
                ]);
            }

            // Default fallback if no clients in DB
            $defaultUrl = 'https://noamdiamonds.com/apis/api/getStockN?auth_key=h3hqs2oimfnl';
            $count = $this->syncFromUrl($defaultUrl, null);

            return response()->json([
                'success' => true,
                'message' => "Successfully fetched and stored {$count} diamonds.",
                'data' => ['synced_count' => $count],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all configured external API clients (DB driven source list).
     */
    public function apiClients(): JsonResponse
    {
        $apiClients = ApiClient::query()
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $apiClients->map(function (ApiClient $client) {
                return [
                    'id' => (int) $client->id,
                    'name' => $client->name,
                    'url' => $client->url,
                    'markup' => (float) $client->markup,
                ];
            }),
        ]);
    }

    /**
     * Store a newly created external API client.
     */
    public function storeApiClient(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:255', 'url'],
            'markup' => ['nullable', 'numeric', 'min:0', 'max:1000'],
        ]);

        $validated['markup'] = $validated['markup'] ?? 0;

        $apiClient = ApiClient::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'API client created successfully.',
            'data' => [
                'id' => (int) $apiClient->id,
                'name' => $apiClient->name,
                'url' => $apiClient->url,
                'markup' => (float) $apiClient->markup,
            ],
        ], 201);
    }

    /**
     * Update an existing external API client (name, URL and markup %).
     */
    public function updateApiClient(Request $request, $id): JsonResponse
    {
        $apiClient = ApiClient::query()->find((int) $id);

        if (!$apiClient) {
            return response()->json([
                'success' => false,
                'message' => 'API client not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:255', 'url'],
            'markup' => ['nullable', 'numeric', 'min:0', 'max:1000'],
        ]);

        $validated['markup'] = $validated['markup'] ?? 0;

        $apiClient->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'API client updated successfully.',
            'data' => [
                'id' => (int) $apiClient->id,
                'name' => $apiClient->name,
                'url' => $apiClient->url,
                'markup' => (float) $apiClient->markup,
            ],
        ]);
    }

    /**
     * Remove an external API client.
     */
    public function destroyApiClient($id): JsonResponse
    {
        $apiClient = ApiClient::query()->find((int) $id);

        if (!$apiClient) {
            return response()->json([
                'success' => false,
                'message' => 'API client not found.',
            ], 404);
        }

        $apiClient->delete();

        return response()->json([
            'success' => true,
            'message' => 'API client deleted successfully.',
        ]);
    }
}
