<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Dropdown;


use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = User::get();
        $dropdown = Dropdown::where('category','Role')
        ->get();
        $types = Dropdown::where('category','Category')
        ->get();
        $plants = Dropdown::where('category','Plant')
        ->get();
        return view('users.index',compact('user','dropdown','types','plants'));
    }


    public function store(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users,username',
        'email' => 'required|email|max:255|unique:users,email',
        'role' => 'required|string',
        'password' => 'required|string|min:6',
    ]);

    // Encrypt the password
    $password = bcrypt($request->password);

    // Create the user
    $addUser = User::create([
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'password' => $password,
        'role' => $request->role,
        'last_login' => null,
        'is_active' => 1, // Set as active by default
        'login_counter' => 0, // Initialize login counter
    ]);

    // Check if user creation was successful
    if ($addUser) {
        return redirect('/user')->with('status', 'User successfully added!');
    } else {
        return redirect('/user')->with('status', 'Failed to add user.');
    }
}



    public function storePartner(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        $password = bcrypt($request->password);
        //dd($password);
        $addUser=User::create([
            'id_partner' => $request->id_partner,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'role' => 'User',
            'last_login' => null,
            'is_active' => '1',

        ]);
        if ($addUser) {
            return redirect('/partner')->with('status','Success Add User');
        }else{
            return redirect('/partner')->with('status','Failed Add User');
        }
    }

    public function revoke($id)
    {
        $revoke= User::where('id',$id)
        ->update([
            'is_active' => '0',
        ]);

            return redirect('/user')->with('status','Success Revoke User');

    }
    public function access($id)
    {
        $access= User::where('id',$id)
        ->update([
            'is_active' => '1',
        ]);
            return redirect('/user')->with('status','Success Give User Access');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'role' => 'required',
            'plant' => 'required',
            'type' => 'required',
        ]);

        $user = User::findOrFail($id);

        // Only update password if a new one is provided
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->role = $request->role;
        $user->plant = $request->plant;
        $user->type = $request->type;

        if ($user->save()) {
            return redirect('/user')->with('status', 'Success updating User');
        } else {
            return redirect('/user')->with('status', 'Failed to update User');
        }
    }

}
