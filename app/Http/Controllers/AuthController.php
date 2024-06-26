<?php

namespace App\Http\Controllers;


use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


/**
 * Class AuthController
 *
 * You can ignoare this controller as this controller handles user authentication related operations.
 */
class AuthController extends Controller
{

    public function index(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended('users/dashboard');
        }
        return view('login');
    }

    public function postLogin(Request $request):RedirectResponse
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



    public function getRegister():View
    {
        return view('register');
    }

    public function postRegister(Request $request):RedirectResponse
    {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required',
            ]);
            try {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                if ($user) {
                    return redirect()->route('login')->with('success', 'Registration successful! Login to access the App.');
                } else {
                    throw new \Exception('Failed to register. Please try again.');
                }
            } catch (\Exception $e) {
    
                \Log::error('Registration error: ' . $e->getMessage());
                return redirect()->route('get.register')->withErrors(['failed' => 'Failed to register. Please try again.']);
            }
    }
    public function signOut():RedirectResponse
    {
        Session::flush();
        Auth::logout();
        return Redirect('/');
    }

}