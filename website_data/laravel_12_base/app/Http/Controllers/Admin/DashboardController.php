<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        @session_start();
    }

    public function index()
    {
        $template = 'admin.dashboard.index';
        return view('admin.layout', compact(
            'template'
        ));
    }
}
