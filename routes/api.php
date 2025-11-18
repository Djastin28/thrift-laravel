<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;

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

      // Modul 2: CRUD Items
    Route::apiResource('items', ItemController::class);
});

