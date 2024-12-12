<?php

namespace App\Http\Controllers;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class HomeController extends Controller
{
    public function index()
    {

        // Pass the grouped data to the view
        return view('home.index');
    }

    public function getMonthlySubmittedParts(Request $request)
    {
        if ($request->ajax()) {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $records = DB::table('supplier_monthly_records')
                ->join('mst_parts', 'supplier_monthly_records.id_part', '=', 'mst_parts.id_part')
                ->join('mst_suppliers', 'mst_parts.id_supplier', '=', 'mst_suppliers.id') // Join with supplier table
                ->select(
                    'supplier_monthly_records.id',
                    'mst_parts.part_no',
                    'mst_parts.description',
                    'supplier_monthly_records.date',
                    'supplier_monthly_records.sample_accuracy',
                    'supplier_monthly_records.actual_accuracy',
                    'supplier_monthly_records.signals',
                    'supplier_monthly_records.qm_check',
                    'supplier_monthly_records.attachment',
                    'mst_suppliers.supplier_name' // Include supplier name
                )
                ->whereMonth('supplier_monthly_records.date', $currentMonth)
                ->whereYear('supplier_monthly_records.date', $currentYear)
                ->orderBy('supplier_monthly_records.date', 'desc') // Sort by newest date
                ->get();

            return DataTables::of($records)
                ->addIndexColumn()
                ->editColumn('signals', function ($row) {
                    return $row->signals === 'Y' ? 'Yellow' : 'Green';
                })
                ->editColumn('attachment', function ($row) {
                    if ($row->attachment) {
                        $attachments = json_decode($row->attachment, true);
                        $links = '';
                        foreach ($attachments as $attachment) {
                            $links .= '<a href="' . url($attachment) . '" target="_blank">View</a><br>';
                        }
                        return $links;
                    }
                    return 'No Attachment';
                })
                ->rawColumns(['attachment']) // To allow HTML links in the attachment column
                ->make(true);
        }
    }














}
