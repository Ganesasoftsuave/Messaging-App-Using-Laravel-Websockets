<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function index()
    {
        if (Auth::check()) {
            return redirect()->intended('users/dashboard');
        }
        return view('login');
    }

    public function Login(Request $request)
    {
        try {
            $validator = $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                if ($user) {
                    return redirect()->intended('users/dashboard')->withSuccess('Signed in');
                }
            }
            $validator['emailPassword'] = 'Email address or password is incorrect.';
            return redirect("/")->withErrors($validator);
        } catch (\Exception $e) {
            return redirect("/")->with('error', 'An unexpected error occurred. Please try again.');
        }
    }



    public function registration()
    {
        return view('register');
    }

    public function customRegistration(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);
            $data = $request->only(['name', 'email', 'password']);
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);
            if ($user) {
                return redirect()->route('login')->with('success', 'Registration successful! Login to access the App.');
            } else {
                throw new \Exception('Failed to register. Please try again.');
            }
        } catch (\Exception $e) {
            return redirect("/register")->withErrors(['failed' => $e->getMessage()]);
        }
    }






    public function signOut()
    {
        Session::flush();
        Auth::logout();
        return Redirect('/');
    }


}