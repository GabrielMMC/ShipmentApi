<?php

use App\Http\Controllers\FreightController;
use App\Http\Controllers\ShippingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'prefix' => 'price'
], function () {
    Route::post('/calculate', [FreightController::class, 'calculate']);
});

Route::group([
    'prefix' => 'shipping'
], function () {
    Route::post('/', [ShippingController::class, 'create']);
    Route::get('/{id}', [ShippingController::class, 'get']);
});
