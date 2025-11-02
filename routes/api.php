<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\LaunchController;
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

Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('spaceX-v5')->middleware('auth:sanctum')->group(function () {
    Route::get('/', function(){
        return \App\Models\User::all();
    });
});





// ===============
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [LaunchController::class, 'dashboard']);
    Route::get('/launches', [LaunchController::class, 'list']);
    Route::get('/launches/{id}', [LaunchController::class, 'show']);
    Route::post('/sync', [SyncController::class, 'resync'])->middleware('admin');
});