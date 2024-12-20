<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeographicDataController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'map');



Route::get('/geographic', [GeographicDataController::class, 'index'])->name('geographic.index');
Route::get('/geographic/data', [GeographicDataController::class, 'getAllData']);
Route::post('/geographic', [GeographicDataController::class, 'store']);
Route::delete('/geographic/{id}', [GeographicDataController::class, 'destroy']);
