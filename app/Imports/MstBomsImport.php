<?php

namespace App\Imports;

use App\Models\MstBom;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class MstBomsImport implements ToCollection
{
    /**
     * Handle the imported data.
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // Skip the header row
        $rows->shift();

        foreach ($rows as $row) {
            // Check if the record already exists
            $exists = MstBom::where('dest', $row[0])
                ->where('model', $row[1])
                ->where('bq', $row[2])
                ->where('raw_material', $row[3])
                ->where('after_press', $row[4])
                ->where('after_welding', $row[5])
                ->exists();

            if (!$exists) {
                // Insert the record if it doesn't exist
                MstBom::create([
                    'dest' => $row[0],
                    'model' => $row[1],
                    'bq' => $row[2],
                    'raw_material' => $row[3],
                    'after_press' => $row[4],
                    'after_welding' => $row[5],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

