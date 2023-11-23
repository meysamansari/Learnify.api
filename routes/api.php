<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;

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


Route::group([], function () {
    Route::post('verification-code-request', [AuthController::class, 'sendVerificationCode']);
    Route::post('login/{type}', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::post('note',[NoteController::class, 'store']);
