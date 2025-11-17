<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function create() {
        return view('auth/signup');
    }

    public function signUp(Request $request){
        $request->validate([
            'name'=>'required',
            //'email' => 'required|email|unique:App\Models\User',
            'email' => 'required|email',
            'password'=>'required|min:6',
        ]);

        return response()->json([
            'name'=>request('name'),
            'email'=>request('email'),
        ]);
    }
}
