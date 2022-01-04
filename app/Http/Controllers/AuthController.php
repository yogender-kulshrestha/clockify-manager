<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'role' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('role', 'email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('home')->withSuccess('Login in Successfully.');
        }
        return redirect("login")->withInput()->withError('Login details are not valid');
    }
}
