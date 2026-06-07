<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SocialLinksController extends Controller
{
    public function index()
    {
        return view('adminlayout.social-links.index');
    }

    public function create()
    {
        return view('adminlayout.social-links.create');
    }

    public function edit($id)
    {
        return view('adminlayout.social-links.edit', compact('id'));
    }
}