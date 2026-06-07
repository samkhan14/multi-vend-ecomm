<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function index()
    {
        return view('adminlayout.integrations.integrations');
    }
}