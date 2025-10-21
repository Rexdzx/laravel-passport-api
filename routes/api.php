<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/reset', [AuthController::class, 'reset']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/detail', [AuthController::class, 'detail']);
    Route::post('/logout', [AuthController::class, 'logout']);
});