<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Auth Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Login - check authentication.
     *
     * @var string
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $credentials['status']='active';
        if (Auth::attempt($credentials)) { //check authentication
            /** start - where to redirect users after login. */
            if(auth()->user()->role == 'user' || auth()->user()->role == 'hr') {
                $route = 'employee.home';
            } else {
                $route = 'home';
            }
            /** end - where to redirect users after login. */
            return redirect()->intended(route($route))->withSuccess('Login in Successfully.');
        }
        return redirect("login")->withInput()->withError('Login details are not valid');
    }
}
