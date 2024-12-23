<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MstBom;
use App\Models\InventoryItem;
use App\Models\Inventory;
use App\Models\L301;
use App\Models\L302;
use App\Models\L310;
use App\Models\L305;
use App\Models\L306;
use App\Models\MstBeginningStock;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

use App\Exports\MstBomsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MstBomsImport;



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
    $startDate = now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
    $endDate = now()->format('Y-m-d') . ' 23:59:59';

    // API URL
    $url = "https://api.mile.app/public/v1/warehouse/order";

    // Define locations and their corresponding IDs and names
    $locations = [
        ['location_id' => '5f335f29a2ef087afa109156', 'location_name' => 'L301', 'types' => ['inbound', 'outbound']],
        ['location_id' => '5fc4b12bc329204cb00b56bf', 'location_name' => 'L305', 'types' => ['inbound']],
        ['location_id' => '5ff7c27b04524f00c07b48c2', 'location_name' => 'L310', 'types' => ['inbound']],
        ['location_id' => '6290b69a982d714b69635325', 'location_name' => 'L306', 'types' => ['inbound']],
        ['location_id' => '6582efb05ffa98822f0c0c0a', 'location_name' => 'KRM', 'types' => ['inbound']],
    ];

    foreach ($locations as $location) {
        foreach ($location['types'] as $type) {
            $queryParams = [
                'location_id' => $location['location_id'],
                'stock_status' => '',
                'limit' => -1,
                'page' => 1,
                'type' => $type,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];

            $fullUrl = $url . '?' . http_build_query($queryParams);
            $headers = ['x-api-key' => '315f9f6eb55fd6db9f87c0c0862007e0615ea467'];

            $response = Http::withHeaders($headers)->get($fullUrl);

            if ($response->successful()) {
                $data = $response->json()['data'];

                DB::beginTransaction();
                try {
                    foreach ($data as $order) {
                        if (strtoupper($order['status']) === 'ONGOING') continue;

                        $existingInventory = Inventory::where('id_api', $order['_id'])->first();
                        if ($existingInventory) continue;

                        $inventory = Inventory::create([
                            'date' => substr($order['updated_at'], 0, 10),
                            'id_api' => $order['_id'],
                            'reference_no' => $order['refNumber'],
                            'vendor_name' => $order['customer_name'],
                            'total_item' => $order['total_item'] ?? 0,
                            'status' => $order['status'],
                            'type' => $type,
                            'no_po' => $order['custom_field']['no_po'] ?? null,
                            'location_code' => $location['location_id'],
                            'location_name' => $location['location_name'],
                            'created_at' => $order['created_at'],
                            'updated_at' => $order['updated_at'],
                        ]);

                        $expectedQtyByCode = [];
                        foreach ($order['item'] as $item) {
                            if (strtoupper($item['status']) === 'ONGOING') continue;
                            $key = $item['code'] ?? '';
                            $expectedQtyByCode[$key] = ($expectedQtyByCode[$key] ?? 0) + $item['qty'];
                        }
                        foreach ($order['item'] as $item) {
                            $code = null;
                            $partNo = null;
                            // Tentukan code dan part_no berdasarkan lokasi
                            switch ($location['location_name']) {
                                case 'L301':
                                    $code = $item['code'];
                                    $partNo = null;
                                    break;

                                case 'L305':
                                    $code = $item['product']['custom_field']['group_no'] ?? null;
                                    $partNo = $item['product']['custom_field']['part_no'] ?? null;
                                    break;

                                case 'L310':
                                    $code = $item['group_no'] ?? null;
                                    $partNo = $item['part_no'] ?? null;
                                    break;

                                case 'L306':
                                    $code = $item['group_no'] ?? null;
                                    $partNo = $item['part_no'] ?? null;
                                    break;

                                case 'KRM':
                                    $code = $item['group_no'] ?? null;
                                    $partNo = $item['part_no'] ?? null;
                                    break;
                            }

                            InventoryItem::create([
                                'inventory_id' => $inventory->id,
                                'code' => $code,
                                'part_no' => $partNo,
                                'serial_name' => $item['serial_number'] ?? null,
                                'expected_qty' => $expectedQtyByCode[$item['code']] ?? 0,
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

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    dd($e);
                    return response()->json(['error' => 'Data saving failed: ' . $e->getMessage()], 500);
                }
            }
        }
    }

    return response()->json(['success' => 'Data saved successfully']);
}



public function data()
{

    // Fetch distinct materials from inventory items
    $materials = InventoryItem::distinct()->pluck('code');

    foreach ($materials as $material) {
        // Calculate beg from mst_boms
        $beg = MstBeginningStock::where('material_name', $material)->where('loc','L301')->value('qty') ?? 0;

        // Calculate rcv (sum of qty where type is inbound)
        $rcv = InventoryItem::join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
        ->where('inventories.location_name','L301')
        ->where('inventory_items.code', $material)
            ->where('inventories.type', 'inbound')
            ->sum('inventory_items.qty') ?? 0;

        // Calculate supply (sum of qty where type is outbound)
        $supply = InventoryItem::join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
        ->where('inventories.location_name','L301')
            ->where('inventory_items.code', $material)
            ->where('inventories.type', 'outbound')
            ->sum('inventory_items.qty') ?? 0;

        // Calculate stock (beg + rcv - supply)
        $sto = ($beg + $rcv) - $supply;

        // Check if a record for the material and date already exists
        $existingRecord = L301::where('material_name', $material)
            ->where('date', now()->toDateString())
            ->first();

        if ($existingRecord) {
            // Update the existing record if values differ
            $existingRecord->update([
                'beg' => $beg,
                'rcv' => $rcv,
                'supply' => $supply,
                'sto' => $sto,
                'updated_at' => now(),
            ]);
        } else {
            // Insert a new record
            L301::create([
                'material_name' => $material,
                'beg' => $beg,
                'rcv' => $rcv,
                'supply' => $supply,
                'sto' => $sto,
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return response()->json(['success' => 'Data processed and stored successfully']);
}

public function l302()
{
     // Fetch supply data: sum of qty for each part_no where location is L305 and type is inbound
    $supplyData = InventoryItem::join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
    ->where('inventories.location_name', 'L305') // Filter lokasi L305
    ->where('inventories.type', 'inbound')      // Filter tipe inbound
    ->select('inventory_items.part_no', DB::raw('SUM(inventory_items.qty) as total_qty')) // Ambil part_no dan total_qty
    ->groupBy('inventory_items.part_no')        // Group berdasarkan part_no
    ->pluck('total_qty', 'inventory_items.part_no');// Create an associative array with 'code' as the key and 'qty' as the value

 $processedData = [];
 $rawMaterialSupply = [];

 foreach ($supplyData as $material => $qty) {

     // Query mst_boms for the bq value where after_press matches the material code
     $bq = MstBom::where('after_press', $material)->value('bq');
  // Skip jika bq bernilai null
  if (is_null($bq)) {
    continue;
}

     if ($bq && $bq > 0) {
         // Divide qty by bq
         $adjustedQty = $qty / $bq;

         // Save adjusted data for after_press
         $processedData[$material] = [
             'original_qty' => $qty,
             'bq' => $bq,
             'adjusted_qty' => $adjustedQty,
         ];

         // Fetch the corresponding raw material for this after_press
         $rawMaterial = MstBom::where('after_press', $material)->value('raw_material');

         if ($rawMaterial) {
             // Sum adjusted_qty for raw_material
             if (isset($rawMaterialSupply[$rawMaterial])) {
                 $rawMaterialSupply[$rawMaterial] += $adjustedQty;
             } else {
                 $rawMaterialSupply[$rawMaterial] = $adjustedQty;
             }
         }
     } else {
         // If no matching bq is found or bq is zero, skip or handle accordingly
         $processedData[$material] = [
             'original_qty' => $qty,
             'bq' => null,
             'adjusted_qty' => null, // No adjustment possible
         ];
     }
 }

    // Fetch distinct materials from inventory items where location is L302
    $materials = InventoryItem::join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
        ->where('inventories.location_name', 'L301') // Location ID for L302
        ->where('type','inbound')
        ->distinct()
        ->pluck('inventory_items.code');


        $newProcessedData = [];

        // Ambil semua data mst_boms yang diperlukan (untuk performa lebih baik)
        $mstBoms = MstBom::whereIn('after_press', array_keys($processedData))
                    ->pluck('raw_material', 'after_press');

        // Lakukan foreach pada $processedData
        foreach ($processedData as $afterPress => $data) {
            // Ambil raw_material berdasarkan after_press dari $mstBoms
            $rawMaterial = $mstBoms[$afterPress] ?? null;

            if ($rawMaterial) {
                // Gunakan raw_material sebagai kunci baru di $newProcessedData
                $newProcessedData[$rawMaterial] = $data;
            } else {
                // Jika tidak ditemukan raw_material, gunakan kunci lama
                $newProcessedData[$afterPress] = $data;
            }
        }

    foreach ($materials as $material) {

        // Calculate beg (beginning stock) from mst_beginning_stocks
        $beg = MstBeginningStock::where('material_name', $material)
            ->where('loc', 'L302') // Use L302 for the location
            ->value('qty') ?? 0;

       // Calculate rcv (sum of qty where type is outbound and location is L301)
       $rcv = InventoryItem::join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
       ->where('inventory_items.code', $material) // Replace 'TDAF21' with the $material variable as needed
       ->where('inventories.location_name', 'L301')
       ->where('inventories.type', 'outbound')
       ->sum('inventory_items.qty'); // Sum the qty column

        // If there are no matching rows, $rcv will default to 0


        // Calculate supply (sum of qty where type is outbound and location is L302)
        $supply = ceil($rawMaterialSupply[$material] ?? 0);


        // Calculate stock (sto) = (beg + rcv - supply)
        $sto = ($beg + $rcv) - $supply;

        // Check if a record for the material and date already exists in L302
        $existingRecord = L302::where('material_name', $material)
            ->where('date', now()->toDateString()) // Check for today's date
            ->first();

        if ($existingRecord) {
            // Update the existing record if values differ
            $existingRecord->update([
                'beg' => $beg,
                'rcv' => $rcv,
                'sto' => $sto,
                'supply' => $supply,
                'updated_at' => now(),
            ]);
        } else {
            // Insert a new record if no existing record is found
            L302::create([
                'material_name' => $material,
                'beg' => $beg,
                'rcv' => $rcv,
                'sto' => $sto,
                'supply' => $supply,
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return response()->json(['success' => 'Data for L302 processed and stored successfully']);
}

public function L305()
{

    // Fetch supply data: sum of qty for each material code where location is L305 and type is inbound
    $materials = InventoryItem::join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
        ->where('inventories.location_name', 'L305') // Filter for location L305
        ->where('inventories.type', 'inbound') // Filter for inbound type
        ->select('inventory_items.part_no', DB::raw('SUM(inventory_items.qty) as total_qty')) // Group by code and sum the qty
        ->groupBy('inventory_items.part_no')
        ->pluck('total_qty', 'inventory_items.part_no'); // Associative array: code => qty

    foreach ($materials as $material => $qty) {
        // Fetch beginning stock from `mst_beginning_stocks`
        $beg = MstBeginningStock::where('material_name', $material)
            ->where('loc', 'L305') // Location L305
            ->value('qty') ?? 0;

        // Fetch supply from `inventory` and `inventory_items` where location is L310
        $supply = InventoryItem::join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
            ->where('inventories.location_name', 'L310') // Filter for location L310
            ->where('inventory_items.part_no', $material) // Match material name
            ->sum('inventory_items.qty') ?? 0;

        // Calculate stock (sto) = beg + rcv - supply
        $sto = ceil($beg + $qty - $supply); // Round up to the nearest whole number

        // Check if a record already exists for this material and today's date
        $existingRecord = L305::where('material_name', $material)
            ->where('date', now()->toDateString()) // Check for today's date
            ->first();

        if ($existingRecord) {
            // Update the existing record
            $existingRecord->update([
                'beg' => $beg,
                'rcv' => $qty, // Assign received quantity
                'supply' => $supply,
                'sto' => $sto,
                'updated_at' => now(),
            ]);
        } else {
            // Insert a new record if no existing record is found
            L305::create([
                'material_name' => $material,
                'beg' => $beg,
                'rcv' => $qty, // Assign received quantity
                'supply' => $supply,
                'sto' => $sto,
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return response()->json(['success' => 'Data processed and stored successfully']);
}

public function l310()
{


   // Fetch supply data: sum of qty for each material code where location is L306 and type is inbound
$supplyData = InventoryItem::join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
->where('inventories.location_name', 'L306') // Filter for location L306
->where('inventories.type', 'inbound')       // Filter for inbound type
->select('inventory_items.part_no', DB::raw('SUM(inventory_items.qty) as total_qty')) // Group by code and sum the qty
->groupBy('inventory_items.part_no')
->pluck('total_qty', 'inventory_items.part_no'); // Create an associative array with 'code' as the key and 'qty' as the value

// Initialize an array to hold processed supply data
$supplyProcessedData = [];

// Process each item in supply data
foreach ($supplyData as $item => $qty) {
// Store processed data for the current item
$supplyProcessedData[$item] = [
    'total_qty' => $qty,             // Original quantity
    'after_press_data' => $item,     // Store item as after_press_data
];
}

// Remove entries where the key is empty ("")
foreach ($supplyProcessedData as $key => $value) {
if ($key === "") {
    unset($supplyProcessedData[$key]);
}
}

// Filter processed supply data to include only records where after_press_data is not empty
$filteredSupplyData = [];

foreach ($supplyProcessedData as $item => $data) {
if (!empty($data['after_press_data'])) { // Cek langsung tanpa array_filter
    $filteredSupplyData[$item] = [
        'total_qty' => $data['total_qty'],
        'after_press_data' => $data['after_press_data'],
    ];
}
}

// Combine after_press_data into a single array
$combinedAfterPressData = [];

foreach ($filteredSupplyData as $item => $data) {
$afterPress = $data['after_press_data'];
$combinedAfterPressData[$afterPress] = $data['total_qty'];
}
$newAfterPressData = []; // Array baru untuk menyimpan hasil

// Loop untuk setiap material di combinedAfterPressData
foreach ($combinedAfterPressData as $material => $qty) {
    // Query ke tabel mst_boms untuk mendapatkan after_press berdasarkan after_welding
    $afterPress = MstBom::where('after_welding', $material)->value('after_press');

    // Jika after_press ditemukan, gunakan sebagai kunci baru
    if ($afterPress) {
        $newAfterPressData[$afterPress] = $qty;
    } else {
        // Jika tidak ditemukan, gunakan kunci lama
        $newAfterPressData[$material] = $qty;
    }
}


// Fetch material data: sum of qty for each material code where location is L310 and type is inbound
$materials = InventoryItem::join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
    ->where('inventories.location_name', 'L310') // Location L310
    ->where('inventories.type', 'inbound')       // Inbound type
    ->select('inventory_items.part_no', DB::raw('SUM(inventory_items.qty) as total_qty'))
    ->groupBy('inventory_items.part_no')
    ->pluck('total_qty', 'inventory_items.part_no')
    ->toArray(); // Convert to array

// Remove entries with empty keys
$materials = array_filter($materials, function ($key) {
    return $key !== ""; // Skip empty keys
}, ARRAY_FILTER_USE_KEY);

// Sort array by keys (alphabetical order, descending)
krsort($materials);

// Debug the final sorted materials

   foreach ($materials as $material => $qty) {
    // Fetch beginning stock (beg)
    $beg = MstBeginningStock::where('material_name', $material)
        ->where('loc', 'L310') // Location L310
        ->value('qty') ?? 0;

    // Received stock (rcv) is the material's qty
    $rcv = $qty;

    // Supply is fetched from $supplyData using the material code
    $supply = $newAfterPressData[$material] ?? 0;

    // Calculate stock (sto)
    $sto = ceil(($beg + $rcv) - $supply); // Round up the stock calculation

    // Check if a record for the material and date already exists in l310_s
    $existingRecord = L310::where('material_name', $material)
        ->where('date', now()->toDateString()) // Check for today's date
        ->first();

    if ($existingRecord) {
        // Update the existing record if values differ
        $existingRecord->update([
            'beg' => $beg,
            'rcv' => $rcv,
            'supply' => $supply,
            'sto' => $sto,
            'updated_at' => now(),
        ]);
    } else {
        // Insert a new record if no existing record is found
        L310::create([
            'material_name' => $material,
            'beg' => $beg,
            'rcv' => $rcv,
            'supply' => $supply,
            'sto' => $sto,
            'date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

return response()->json(['success' => 'Data for L310 stored successfully.']);

}

public function l306()
{

// Fetch material data: sum of qty for each material code where location is L306 and type is inbound
$materials = InventoryItem::join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
    ->where('inventories.location_name', 'L306') // Location L306
    ->where('inventories.type', 'inbound')       // Inbound type
    ->select('inventory_items.part_no', DB::raw('SUM(inventory_items.qty) as total_qty')) // Group by code and sum the qty
    ->groupBy('inventory_items.part_no')
    ->pluck('total_qty', 'inventory_items.part_no') // Create an associative array: code => qty
    ->reject(function ($value, $key) {
        return $key === ""; // Skip empty keys
    });


    foreach ($materials as $material => $qty) {
        // Fetch beginning stock from `mst_beginning_stocks`
        $beg = MstBeginningStock::where('material_name', $material)
            ->where('loc', 'L306') // Location L306
            ->value('qty') ?? 0;

        // Fetch supply from `inventory` and `inventory_items` where location is L310
        $supply = InventoryItem::join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
            ->where('inventories.location_name', 'KRM') // Filter for location L310
            ->where('inventory_items.part_no', $material) // Match material name
            ->sum('inventory_items.qty') ?? 0;

        // Calculate stock (sto) = beg + rcv - supply
        $sto = ceil($beg + $qty - $supply); // Round up to the nearest whole number

        // Check if a record already exists for this material and today's date
        $existingRecord = L306::where('material_name', $material)
            ->where('date', now()->toDateString()) // Check for today's date
            ->first();

        if ($existingRecord) {
            // Update the existing record
            $existingRecord->update([
                'beg' => $beg,
                'rcv' => $qty, // Assign received quantity
                'supply' => $supply,
                'sto' => $sto,
                'updated_at' => now(),
            ]);
        } else {
            // Insert a new record if no existing record is found
            L306::create([
                'material_name' => $material,
                'beg' => $beg,
                'rcv' => $qty, // Assign received quantity
                'supply' => $supply,
                'sto' => $sto,
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return response()->json(['success' => 'Data for L306 processed and stored successfully']);
}

public function template()
{
    return Excel::download(new MstBomsExport, 'mst_boms_template.xlsx');
}

public function upload(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        // Process the import
        Excel::import(new MstBomsImport, $request->file('file'));

        return back()->with('success', 'Data imported successfully!');
    }

}
