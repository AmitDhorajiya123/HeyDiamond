<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\EncryptedId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserApiController extends Controller
{
    use EncryptedId;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile_no', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 15;

        $users = $query->latest('id')->paginate($perPage);

        $users->getCollection()->transform(function ($user) {
            $user->encrypted_id = $this->encryptId($user->id);
            return $user;
        });

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::create($data);
        $user->encrypted_id = $this->encryptId($user->id);

        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => 'User created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $encrypted_id): JsonResponse
    {
        $user = $this->findByEncryptedId(User::class, $encrypted_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->encrypted_id = $this->encryptId($user->id);

        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => 'User retrieved successfully.',
            ])
            ->response();
    }

    public function update(UpdateUserRequest $request, string $encrypted_id): JsonResponse
    {
        $user = $this->findByEncryptedId(User::class, $encrypted_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);
        $user->encrypted_id = $this->encryptId($user->id);

        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => 'User updated successfully.',
            ])
            ->response();
    }

    public function destroy(string $encrypted_id): JsonResponse
    {
        $user = $this->findByEncryptedId(User::class, $encrypted_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ], 200);
    }
}