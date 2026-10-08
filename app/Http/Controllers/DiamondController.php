<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Diamond;
use Yajra\DataTables\Facades\DataTables;

class DiamondController extends Controller
{
    public function index()
    {
        return view('diamonds.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $data = Diamond::select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
        return abort(404);
    }

    public function fetchAndStoreDiamonds()
    {
        $url = 'https://noamdiamonds.com/apis/api/getStockN?auth_key=h3hqs2oimfnl';

        try {
            $response = Http::timeout(120)->post($url);

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
                        'message' => "Successfully fetched and stored {$count} diamonds."
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data format received from API.'
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch data from API. Status: ' . $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
