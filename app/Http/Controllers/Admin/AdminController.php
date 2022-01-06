<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        if (!Auth::check() || !Auth::user()->hasRole('ADMIN')) return redirect('admin/login');
        
        return view('admin.dashboard');
    }
    
    public function login()
    {
        return view('admin.login');
    }
    
    public function loginverify(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);
        
//        echo Hash::make('admin123'); die();

        
        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        if (auth()->attempt([$fieldType => $input['email'], 'password' => $input['password']])) {
            
            
            if (Auth::user()->hasRole('ADMIN')) {
                return redirect('admin');
            } else {
                redirect('admin/login')->withErrors('Wrong.');
            }
        } else {
            return redirect('admin/login')->withErrors('Email Or Password is Wrong.');
        }
        return view('admin.login');
    }
    
    public function logout(Request $request) {
        Auth::logout();
        return redirect('admin/login');
    }
}
