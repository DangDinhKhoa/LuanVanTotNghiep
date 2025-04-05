<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function __construct()
    {
        @session_start();
    }

    public function index()
    {
        $template = 'user.body.index';
        return view('user.layout', compact(
            'template'
        ));
    }

   
}
