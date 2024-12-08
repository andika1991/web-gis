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

Route::view('/', 'map')->name('home');

Route::get('/geographic/data', [GeographicDataController::class, 'getAllData']);

// Route to create new geographic data
Route::post('/geographic', [GeographicDataController::class, 'store']);

// Route to update geographic data
Route::put('/geographic/edit/{id}', [GeographicDataController::class, 'update'])->name('geographic.update');


Route::get('/geographic/edit/{id}', [GeographicDataController::class, 'edit']);

// Route to delete geographic data
Route::delete('/geographic/{id}', [GeographicDataController::class, 'destroy']);