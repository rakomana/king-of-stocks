<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//to be used as mimic for cron jobs
Route::get('/fetch-prices', [StockController::class, 'store']);
Route::get('/clean-up', [StockController::class, 'cleanUp']);
Route::get('/send-emails', [StockController::class, 'sendEmail']);