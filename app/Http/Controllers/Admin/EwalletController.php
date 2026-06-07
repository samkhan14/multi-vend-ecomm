<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EwalletController extends Controller
{
    public function index(){

        return view('adminlayout.e-wallet.index');
    }
}
