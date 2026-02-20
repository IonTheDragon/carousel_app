<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function home()
    {
        return response()->json([
            'success' => true,
            'user' => auth()->user()
        ]);
    }
}