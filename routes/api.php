<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\Auth\LoginController;

Route::post('/login', [LoginController::class, 'login']);

Route::middleware('jwt.verify')->group(function() {
    Route::get('v4/quote', [StockController::class, 'show']);
});