<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PayoutController extends Controller
{
    public function index()
    {
        return view('adminlayout.payouts.index');
    }
}

