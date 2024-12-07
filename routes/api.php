<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeographicDataController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});





// Route untuk mendapatkan semua data GeoJSON
Route::get('/geo-data', [GeographicDataController::class, 'index']);

// Route untuk menyimpan data GeoJSON baru
Route::post('/geo-data', [GeographicDataController::class, 'store']);

// Route untuk mengupdate data GeoJSON
Route::put('/geo-data/{id}', [GeographicDataController::class, 'update']);

// Route untuk menghapus data GeoJSON
Route::delete('/geo-data/{id}', [GeographicDataController::class, 'destroy']);
