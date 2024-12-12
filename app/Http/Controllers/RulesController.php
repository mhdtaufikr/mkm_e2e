<?php

namespace App\Http\Controllers;
use App\Models\Rule;

use Illuminate\Http\Request;

class RulesController extends Controller
{
    public function index()
    {
        $rules = Rule::get();
        return view('rules.index',compact('rules'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'rule_name' => 'required',
            'rule_value' => 'required',
        ]);

        $addrule=Rule::create([
            'rule_name' => $request->rule_name,
            'rule_value' => $request->rule_value,
        ]);
        if ($addrule) {
            return redirect('/rule')->with('status','Success Add Rule');
        }else{
            return redirect('/rule')->with('status','Failed Add Rule');
        }
    }

    public function delete($id)
    {
        $deleterule=Rule::where('id',$id)
        ->delete();
        if ($deleterule) {
            return redirect('/rule')->with('status','Success Delete Rule');
        }else{
            return redirect('/rule')->with('status','Failed Delete Rule');
        }
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'rule_name' => 'required',
            'rule_value' => 'required',
        ]);

        //Validate Input
        $validateInput =  Rule::where('id',$id)->first();
        $validateInput->rule_name = $request->rule_name;
        $validateInput->rule_value = $request->rule_value;

       if($validateInput->isDirty()){

          try {
            $updaterule=Rule::where('id',$id)
            ->update([
                'rule_name' => $request->rule_name,
                'rule_value' => $request->rule_value,
            ]);
            if ($updaterule) {
                return redirect('/kpi/standard')->with('status','Success Update Rule');
            }else{
                return redirect('/kpi/standard')->with('failed','Failed Update Rule');
            }
          } catch (\Throwable $th) {
           return redirect('/kpi/standard')->with('failed','Failed Update Dropdown');
          }

      } else{
       return redirect('/kpi/standard')->with('failed','There is no Change in Allowance Data');
      }

    }

}
