<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerMarkup;
use App\Models\User;
use App\Traits\EncryptedId;
use App\Traits\HandlesMarkupRanges;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerMarkupApiController extends Controller
{
    use EncryptedId, HandlesMarkupRanges;

    /**
     * Paginated customer markup listing with search and sorting.
     * Only the current page is hydrated, so this stays fast with large data sets.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = max(1, min($perPage, 100));

        $sortBy = (string) $request->input('sort_by', 'name');
        $allowedSorts = ['name', 'email', 'markup', 'created_at', 'id'];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'name';
        }

        $sortOrder = strtolower((string) $request->input('sort_order', 'asc')) === 'desc' ? 'desc' : 'asc';

        $query = User::query()
            ->where('role', 'user')
            ->with('markups');

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query
            ->orderBy($sortBy, $sortOrder)
            ->orderBy('id', 'asc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $customers->getCollection()->map(function (User $customer) {
                return $this->customerPayload($customer);
            })->values(),
            'meta' => [
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
                'from' => $customers->firstItem(),
                'to' => $customers->lastItem(),
            ],
        ]);
    }

    /**
     * Lightweight searchable customer list for the picker.
     * Returns encrypted IDs only, never raw database IDs.
     */
    public function options(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 20);
        $limit = max(1, min($limit, 50));

        $query = User::query()
            ->where('role', 'user')
            ->select(['id', 'name', 'email']);

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query
            ->orderBy('name')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $customers->map(function (User $customer) {
                return [
                    'encrypted_id' => $this->encryptId($customer->id),
                    'name' => $customer->name,
                    'email' => $customer->email,
                ];
            })->values(),
        ]);
    }

    /**
     * Show one customer's markup configuration.
     */
    public function show(string $encrypted_id): JsonResponse
    {
        $customer = $this->findCustomer($encrypted_id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found.',
            ], 404);
        }

        $customer->loadMissing('markups');

        return response()->json([
            'success' => true,
            'data' => $this->customerPayload($customer),
        ]);
    }

    /**
     * Replace a customer's default markup and range slabs.
     */
    public function update(Request $request, string $encrypted_id): JsonResponse
    {
        $customer = $this->findCustomer($encrypted_id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found.',
            ], 404);
        }

        $validated = $request->validate([
            'markup' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'markups' => ['nullable', 'array'],
            'markups.*.min_value' => ['required', 'numeric', 'min:0'],
            'markups.*.max_value' => ['nullable', 'numeric', 'min:0'],
            'markups.*.markup' => ['required', 'numeric', 'min:0', 'max:1000'],
        ]);

        $ranges = $this->normaliseMarkupRanges($validated['markups'] ?? []);

        if ($error = $this->markupRangesError($ranges)) {
            return response()->json([
                'success' => false,
                'message' => $error,
                'errors' => ['markups' => [$error]],
            ], 422);
        }

        $customer->markup = $validated['markup'] ?? 0;
        $customer->save();
        $this->syncMarkupRanges($customer, $ranges);

        return response()->json([
            'success' => true,
            'message' => 'Customer markup updated successfully.',
            'data' => $this->customerPayload($customer),
        ]);
    }

    /**
     * Resolve an encrypted ID to a customer user.
     */
    private function findCustomer(string $encrypted_id): ?User
    {
        $customer = $this->findByEncryptedId(User::class, $encrypted_id);

        if (!$customer || $customer->role !== 'user') {
            return null;
        }

        return $customer;
    }

    /**
     * Shape a customer (with default markup and ranges) for the frontend.
     */
    private function customerPayload(User $customer): array
    {
        $customer->loadMissing('markups');

        return [
            'encrypted_id' => $this->encryptId($customer->id),
            'name' => $customer->name,
            'email' => $customer->email,
            'role' => $customer->role,
            'status' => $customer->status,
            'markup' => (float) $customer->markup,
            'markups' => $customer->markups->map(function (CustomerMarkup $markup) {
                return [
                    'min_value' => (float) $markup->min_value,
                    'max_value' => $markup->max_value === null ? null : (float) $markup->max_value,
                    'markup' => (float) $markup->markup,
                ];
            })->values()->all(),
        ];
    }
}
