<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\DashboardController;
use App\Services\ShopifyService;

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');
Route::get('uploadcsv', [UploadController::class, 'index'])
    ->name('uploadcsv');
Route::post('/upload', [UploadController::class, 'store'])
    ->name('upload.csv');


