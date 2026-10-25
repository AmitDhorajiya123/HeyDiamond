<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MstCategory;
use App\Models\MstBgmBlackOption;
use App\Models\MstClarity;
use App\Models\MstColor;
use App\Models\MstFinishGrade;
use App\Models\MstFluorescence;
use App\Models\MstLab;
use App\Models\MstShape;
use App\Models\MstSizeRange;
use App\Models\MstSubcategory;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    /**
     * Get aggregated counts from all master tables.
     */
    public function counts(): JsonResponse
    {
        $counts = [
            'categories'        => MstCategory::count(),
            'bgm_black_options' => MstBgmBlackOption::count(),
            'clarities'         => MstClarity::count(),
            'colors'            => MstColor::count(),
            'finish_grades'     => MstFinishGrade::count(),
            'fluorescences'     => MstFluorescence::count(),
            'labs'              => MstLab::count(),
            'shapes'            => MstShape::count(),
            'size_ranges'       => MstSizeRange::count(),
            'subcategories'     => MstSubcategory::count(),
        ];

        $total = array_sum($counts);

        return response()->json([
            'success' => true,
            'message' => 'Dashboard counts retrieved successfully.',
            'data'    => [
                'counts' => $counts,
                'total'  => $total,
            ],
        ]);
    }
}