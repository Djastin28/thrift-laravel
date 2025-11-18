<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
// nanti kalau sudah buat ItemController:
// use App\Http\Controllers\Api\ItemController;

/*
|--------------------------------------------------------------------------
| Public routes (tanpa token)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected routes (wajib token Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // profil & logout
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // contoh: kalau nanti sudah ada ItemController
    // Route::apiResource('items', ItemController::class);
});

// Kalau mau, route /user bawaan Laravel boleh dihapus saja
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
