<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // For Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $credentials['status']='active';
        if (Auth::attempt($credentials)) {
            if(auth()->user()->role == 'user' || auth()->user()->role == 'hr') {
                $route = 'employee.home';
            } else {
                $route = 'home';
            }
            return redirect()->intended(route($route))->withSuccess('Login in Successfully.');
        }
        return redirect("login")->withInput()->withError('Login details are not valid');
    }
}
