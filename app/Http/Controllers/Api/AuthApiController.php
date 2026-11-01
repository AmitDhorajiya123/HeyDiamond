<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthApiController extends Controller
{
    /**
     * Register a new user (does NOT auto-login).
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'mobile_no' => $data['mobile_no'] ?? null,
            'password'  => $data['password'],
            'role'      => 'user',
            'status'    => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. Please login to continue.',
            'data'    => new UserResource($user),
        ], 201);
    }

    /**
     * Authenticate user and issue access token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
            ], 401);
        }

        if ($user->status && strtolower($user->status) !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact support.',
            ], 403);
        }

        // Ensure Passport Personal Access Client exists
        $clientRepo = app(\Laravel\Passport\ClientRepository::class);
        try {
            $clientRepo->personalAccessClient('users');
        } catch (\Throwable $e) {
            $clientRepo->createPersonalAccessGrantClient('HeyDiamond Personal Access Client', 'users');
        }

        // Generate Passport token
        $tokenResult = $user->createToken('HeyDiamondApiToken');
        $accessToken = $tokenResult->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $accessToken,
                'user' => new UserResource($user),
            ],
        ], 200);
    }

    /**
     * Get the authenticated user's profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully.',
            'data' => new UserResource($user),
        ], 200);
    }

    /**
     * Revoke current user's access token (logout).
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->token();

        if ($token) {
            $token->revoke();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ], 200);
    }




public function testGet(): JsonResponse
{
    return $this->testExternalApi('GET');
}

public function testPost(): JsonResponse
{
    return $this->testExternalApi('POST');
}

private function testExternalApi(string $method): JsonResponse
{
    $url = 'https://noamdiamonds.com/apis/api/getStockN?auth_key=h3hqs2oimfnl';
    // $url = 'https://shivkrishnadiamond.com/papi/get_stock';

    try {
        $http = Http::timeout(120);

        // Only for local testing
        if (app()->environment('local', 'development')) {
            $http->withoutVerifying();
        }

        if ($method === 'GET') {
            $response = $http->get($url);
        } else {
            $response = $http->post($url);
        }

        return response()->json([
            'success' => $response->successful(),
            'requested_method' => $method,
            'requested_url' => $url,
            'api_status' => $response->status(),
            'api_headers' => $response->headers(),
            'api_response' => $response->json() ?? $response->body(),
        ], 200);

    } catch (\Throwable $e) {

        return response()->json([
            'success' => false,
            'requested_method' => $method,
            'requested_url' => $url,
            'error' => $e->getMessage(),
        ], 500);
    }
}
}
