<?php

namespace App\Http\Controllers\admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request){

        if (Session::has('admin_id')) {
             return redirect('admin/dashboard');
        }
          return view('admin.auth.login');
    }

    public function register(Request $request){
        return view('admin.auth.register');
    }

    public function loginSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            session(['admin_id' => $admin->id]);
            return redirect(url('admin/dashboard'))->with('success', 'Logging in successfully');
        } else {
            return redirect()->back()->with('error', 'Invalid credentials');
        }
    }

    public function registerSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
        ]);

        $admin = admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('admin')->login($admin);
        // return redirect(url('admin/dashboard'))->with('success', 'Registration successfully');
        // return redirect(url('admin/dashboard'))->with('success', 'Registration successfully');
         return  redirect()->route('company.index')->with('success', 'Registration successfully');
    }

    public function logout(){
        session()->forget('admin_id');
        Auth::guard('admin')->logout();
        return redirect(url('admin/login'))->with('success', 'Logged out successfully');
    }

}

