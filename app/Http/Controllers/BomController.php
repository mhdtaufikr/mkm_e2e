<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MstBom;
use App\Models\InventoryItem;
use App\Models\Inventory;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


class BomController extends Controller
{
    public function index(Request $request)
{
    if ($request->ajax()) {
        $data = MstBom::select([
            'id',
            'dest',
            'model',
            'bq',
            'raw_material',
            'after_press',
            'after_welding',
            'created_at',
            'updated_at'
        ]);
        return DataTables::of($data)
            ->addIndexColumn() // Adds a column for row numbers
            ->make(true);
    }

    return view('bom.index');
}


public function store(Request $request)
{
    $validated = $request->validate([
        'dest' => 'required|string|max:255',
        'model' => 'required|string|max:255',
        'bq' => 'required|integer|min:1',
        'raw_material' => 'required|string|max:255',
        'after_press' => 'required|string|max:255',
        'after_welding' => 'required|string|max:255',
    ]);

    MstBom::create($validated);

    return response()->json(['success' => 'BOM saved successfully.']);
}

public function saveApiData()
{
    // Get the current date for dynamic date range
    $now = now();
    $startDate = $now->startOfDay()->format('Y-m-d H:i:s');
    $endDate = $now->endOfDay()->format('Y-m-d H:i:s');

    // API URL
    $url = "https://api.mile.app/public/v1/warehouse/order";

    // Query parameters
    $queryParams = [
        'location_id' => '5f335f29a2ef087afa109156',
        'stock_status' => '',
        'limit' => -1,
        'page' => 1,
        's' => '',
        'type' => 'outbound',
        'show_item' => 'false',
        'start_date' => $startDate,
        'end_date' => $endDate,
    ];

    // Build the full URL with query parameters
    $fullUrl = $url . '?' . http_build_query($queryParams);

    // Headers
    $headers = [
        'x-api-key' => '315f9f6eb55fd6db9f87c0c0862007e0615ea467',
    ];

    // Make the API request
    $response = Http::withHeaders($headers)->get($fullUrl);
    // Process the response if successful
    if ($response->successful()) {
        $data = $response->json()['data']; // Extract 'data' array

        DB::beginTransaction(); // Start a transaction
        try {
            foreach ($data as $order) {

                // Skip the order if its status is "ONGOING"
                if (strtoupper($order['status']) === 'ONGOING') {
                    continue;
                }

                // Save inventory
                $inventory = Inventory::create([
                    'date' => now()->format('Y-m-d'), // Use the current date or extract from API
                    'id_api'=> $order['_id'],
                    'reference_no' => $order['refNumber'],
                    'vendor_name' => $order['customer_name'],
                    'total_item' => $order['total_item'] ?? 0,
                    'status' => $order['status'],
                    'type' => $order['type'],
                    'no_po' => $order['custom_field']['no_po'] ?? null,
                    'location_code' => $order['location_id'] ?? null,
                    'location_name' => $order['item'][0]['location_code'] ?? 'L301',
                    'created_at' => $order['created_at'],
                    'updated_at' => $order['updated_at'],
                ]);

                // Calculate expected quantity by code
                $expectedQtyByCode = [];
                foreach ($order['item'] as $item) {
                    // Skip the item if its status is "ONGOING"
                    if (strtoupper($item['status']) === 'ONGOING') {
                        continue;
                    }

                    if (isset($expectedQtyByCode[$item['code']])) {
                        $expectedQtyByCode[$item['code']] += $item['qty'];
                    } else {
                        $expectedQtyByCode[$item['code']] = $item['qty'];
                    }
                }

                // Save inventory items
                foreach ($order['item'] as $item) {
                    // Skip the item if its status is "ONGOING"
                    if (strtoupper($item['status']) === 'ONGOING') {
                        continue;
                    }

                    InventoryItem::create([
                        'inventory_id' => $inventory->id,
                        'code' => $item['code'],
                        'serial_name' => $item['serial_number'], // Assuming serial_name is same as code
                        'expected_qty' => $expectedQtyByCode[$item['code']],
                        'qty' => $item['qty'] ?? 0,
                        'unit' => $item['order_unit'] ?? 'pcs',
                        'status' => $item['status'] ?? 'unknown',
                        'serial_no' => $item['serial_number'] ?? null,
                        'delivery_date' => isset($item['custom_field']['delivery_date'])
                            ? \Carbon\Carbon::parse($item['custom_field']['delivery_date'])->format('Y-m-d H:i:s')
                            : now()->format('Y-m-d H:i:s'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit(); // Commit the transaction

            return response()->json(['success' => 'Data saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack(); // Roll back the transaction in case of an error
            return response()->json(['error' => 'Data saving failed: ' . $e->getMessage()], 500);
        }
    }

}





}
