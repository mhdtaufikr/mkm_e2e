<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dropdown;

class DropdownController extends Controller
{
    public function index()
    {
        $dropdown = Dropdown::get();
        return view('dropdown.index',compact('dropdown'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required',
            'name_value' => 'required',
            'code_format' => 'required',
        ]);

        $addDropdown=Dropdown::create([
            'category' => $request->category,
            'name_value' => $request->name_value,
            'code_format' => $request->code_format,
        ]);
        if ($addDropdown) {
            return redirect('/dropdown')->with('status','Success Add Dropdown');
        }else{
            return redirect('/dropdown')->with('status','Failed Add Dropdown');
        }
    }

    public function delete($id)
    {
        $deleterule=Dropdown::where('id',$id)
        ->delete();
        if ($deleterule) {
            return redirect('dropdown')->with('status','Success Delete Dropdown');
        }else{
            return redirect('dropdown')->with('status','Failed Delete Dropdown');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category' => 'required',
            'name_value' => 'required',
            'code_format' => 'required',
        ]);

         //Validate Input
         $validateInput =  Dropdown::where('id',$id)->first();
         $validateInput->category = $request->category;
         $validateInput->name_value = $request->name_value;
         $validateInput->code_format = $request->code_format;

        if($validateInput->isDirty()){

           try {
                $updaterule=Dropdown::where('id',$id)
                ->update([
                    'category' => $request->category,
                    'name_value' => $request->name_value,
                    'code_format' => $request->code_format,
                ]);
                if ($updaterule) {
                    return redirect('dropdown')->with('status','Success Update Dropdown');
                }else{
                    return redirect('dropdown')->with('failed','Failed Update Dropdown');
                }
           } catch (\Throwable $th) {
            return redirect('dropdown')->with('failed','Failed Update Dropdown');
           }

       } else{
        return redirect('dropdown')->with('failed','There is no Change in Allowance Data');
       }
    }
}
