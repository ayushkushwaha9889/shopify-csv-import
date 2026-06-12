<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Upload;

class DashboardController extends Controller
{
    public function index()
    {
        $uploads = Upload::with([
            'products',
            'logs'
        ])->latest()->get();

        return view('dashboard', compact('uploads'));
    }
}
