<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        return view('adminlayout.payment-gateways.index');
    }
}
