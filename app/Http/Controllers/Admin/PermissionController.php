<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(){

        return view("adminlayout.roles.index");
    }

    public function permissionindex(){

        return view("adminlayout.permission.index");
    }
    public function permissioncreate(){

        return view("adminlayout.permission.create");
    }
}