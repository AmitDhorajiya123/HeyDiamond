<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\EncryptedId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class EncryptionTestController extends Controller
{
    use EncryptedId;

    /**
     * Encrypt a plain ID
     * POST /api/test/encrypt
     * Body: { "id": 123, "type": "user" }
     */
    public function encrypt(Request $request): JsonResponse
    {
        $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'type' => ['nullable', 'string'], // optional: user, diamond, location, shape, etc.
        ]);

        $id = $request->input('id');
        $encryptedId = $this->encryptId($id);

        return response()->json([
            'success' => true,
            'message' => 'ID encrypted successfully.',
            'data' => [
                'plain_id' => $id,
                'encrypted_id' => $encryptedId,
                'type' => $request->input('type'),
            ],
        ]);
    }

    /**
     * Decrypt an encrypted ID
     * POST /api/test/decrypt
     * Body: { "encrypted_id": "eyJpdiI6..." }
     */
    public function decrypt(Request $request): JsonResponse
    {
        $request->validate([
            'encrypted_id' => ['required', 'string'],
        ]);

        $encryptedId = $request->input('encrypted_id');

        try {
            $decrypted = Crypt::decryptString($encryptedId);
            $id = (int) $decrypted;

            return response()->json([
                'success' => true,
                'message' => 'ID decrypted successfully.',
                'data' => [
                    'encrypted_id' => $encryptedId,
                    'plain_id' => $id,
                ],
            ]);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid encrypted ID. Unable to decrypt.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Test: Find model by encrypted ID (shows which model it belongs to)
     * POST /api/test/find-by-encrypted-id
     * Body: { "encrypted_id": "eyJpdiI6...", "model": "user" }
     */
    public function findByEncryptedId(Request $request): JsonResponse
    {
        $request->validate([
            'encrypted_id' => ['required', 'string'],
            'model' => ['required', 'string', 'in:user,diamond,location,shape,color,clarity,finish_grade,fluorescence,lab,size_range,bgm_black_option,category,subcategory'],
        ]);

        $encryptedId = $request->input('encrypted_id');
        $modelType = $request->input('model');

        $modelMap = [
            'user' => \App\Models\User::class,
            'diamond' => \App\Models\Diamond::class,
            'location' => \App\Models\Location::class,
            'shape' => \App\Models\MstShape::class,
            'color' => \App\Models\MstColor::class,
            'clarity' => \App\Models\MstClarity::class,
            'finish_grade' => \App\Models\MstFinishGrade::class,
            'fluorescence' => \App\Models\MstFluorescence::class,
            'lab' => \App\Models\MstLab::class,
            'size_range' => \App\Models\MstSizeRange::class,
            'bgm_black_option' => \App\Models\MstBgmBlackOption::class,
            'category' => \App\Models\MstCategory::class,
            'subcategory' => \App\Models\MstSubcategory::class,
        ];

        $modelClass = $modelMap[$modelType];
        $keyMap = [
            'user' => 'id',
            'diamond' => 'id',
            'location' => 'location_id',
            'shape' => 'shape_id',
            'color' => 'color_id',
            'clarity' => 'clarity_id',
            'finish_grade' => 'finish_grade_id',
            'fluorescence' => 'fluorescence_id',
            'lab' => 'lab_id',
            'size_range' => 'size_range_id',
            'bgm_black_option' => 'option_id',
            'category' => 'category_id',
            'subcategory' => 'subcategory_id',
        ];

        $key = $keyMap[$modelType];
        $model = $this->findByEncryptedId($modelClass, $encryptedId, $key);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => "No {$modelType} found with this encrypted ID.",
            ], 404);
        }

        $model->encrypted_id = $this->encryptId($model->{$key});

        return response()->json([
            'success' => true,
            'message' => "{$modelType} found successfully.",
            'data' => $model->toArray(),
        ]);
    }
}