<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendorController extends Controller
{

    public function index()  {
        
        return view('vendor.index');
    }
    public function detail($id)  {
        
        return view('vendor.detail', compact('id'));
    }

    public function orders($id)
    {
        return view('vendor.orders', compact('id'));
    }

    public function login()  {
        
        return view('vendor.login');
    }
    public function register()  {
        
        return view('vendor.registration');
    }
    public function resetPassword($token, Request $request)
    {
        return view('vendor.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

}
