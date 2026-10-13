<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMstFinishGradeRequest;
use App\Http\Requests\Api\UpdateMstFinishGradeRequest;
use App\Http\Resources\MstFinishGradeResource;
use App\Models\MstFinishGrade;
use App\Traits\EncryptedId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MstFinishGradeApiController extends Controller
{
    use EncryptedId;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MstFinishGrade::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('finish_grade_code', 'like', "%{$search}%")
                  ->orWhere('finish_grade_name', 'like', "%{$search}%");
            });
        }

        if ($gradeGroup = $request->input('grade_group')) {
            $query->where('grade_group', $gradeGroup);
        }

        if ($isActive = $request->input('is_active')) {
            $query->where('is_active', $isActive);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 15;

        $grades = $query->orderBy('grade_group')->orderBy('display_order')->orderBy('finish_grade_id')->paginate($perPage);

        $grades->getCollection()->transform(function ($grade) {
            $grade->encrypted_id = $this->encryptId($grade->finish_grade_id);
            return $grade;
        });

        return MstFinishGradeResource::collection($grades);
    }

    public function store(StoreMstFinishGradeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $grade = MstFinishGrade::create($data);
        $grade->encrypted_id = $this->encryptId($grade->finish_grade_id);

        return (new MstFinishGradeResource($grade))
            ->additional([
                'success' => true,
                'message' => 'Finish grade created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $encrypted_id): JsonResponse
    {
        $grade = $this->findByEncryptedId(MstFinishGrade::class, $encrypted_id, 'finish_grade_id');

        if (!$grade) {
            return response()->json([
                'success' => false,
                'message' => 'Finish grade not found.',
            ], 404);
        }

        $grade->encrypted_id = $this->encryptId($grade->finish_grade_id);

        return (new MstFinishGradeResource($grade))
            ->additional([
                'success' => true,
                'message' => 'Finish grade retrieved successfully.',
            ])
            ->response();
    }

    public function update(UpdateMstFinishGradeRequest $request, string $encrypted_id): JsonResponse
    {
        $grade = $this->findByEncryptedId(MstFinishGrade::class, $encrypted_id, 'finish_grade_id');

        if (!$grade) {
            return response()->json([
                'success' => false,
                'message' => 'Finish grade not found.',
            ], 404);
        }

        $grade->update($request->validated());
        $grade->encrypted_id = $this->encryptId($grade->finish_grade_id);

        return (new MstFinishGradeResource($grade))
            ->additional([
                'success' => true,
                'message' => 'Finish grade updated successfully.',
            ])
            ->response();
    }

    public function destroy(string $encrypted_id): JsonResponse
    {
        $grade = $this->findByEncryptedId(MstFinishGrade::class, $encrypted_id, 'finish_grade_id');

        if (!$grade) {
            return response()->json([
                'success' => false,
                'message' => 'Finish grade not found.',
            ], 404);
        }

        $grade->delete();

        return response()->json([
            'success' => true,
            'message' => 'Finish grade deleted successfully.',
        ], 200);
    }
}