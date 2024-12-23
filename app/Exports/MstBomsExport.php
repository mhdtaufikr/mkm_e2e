<?php

namespace App\Exports;

use App\Models\MstBom;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MstBomsExport implements FromCollection, WithHeadings
{
    /**
     * Retrieve the data for export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return MstBom::select('dest', 'model', 'bq', 'raw_material', 'after_press', 'after_welding')->get();
    }

    /**
     * Add headers to the exported Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Destination',
            'Model',
            'B.Q',
            'Raw Material',
            'After Press',
            'After Welding',
        ];
    }
}
