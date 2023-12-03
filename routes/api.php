<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\BlogController;
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


// Auth

Route::group(['prefix' => 'auth'], function () {
    Route::post('verification-code-request', [AuthController::class, 'sendVerificationCode']);
    Route::post('login/{type}', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
});



// User

Route::group(['prefix' => 'user', 'middleware' => 'auth:sanctum'], function () {
    Route::put('/update', [UserController::class, 'update']);
});




// Note

Route::group(['prefix' => 'notes', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/{course_id}',[NoteController::class, 'UpdateOrCreate'])->whereNumber('course_id');
    Route::get('/{id}', [NoteController::class, 'show']);
    Route::delete('/{id}', [NoteController::class, 'destroy']);
});


// Blog

Route::group(['prefix' => 'blogs','middleware'=>'auth:sanctum'], function (){
    Route::get('/',[BlogController::class, 'index']);
    Route::post('/',[BlogController::class, 'store']);
    Route::get('/{id}',[BlogController::class, 'show']);
    Route::put('/{id}',[BlogController::class, 'update']);
    Route::delete('/{id}',[BlogController::class, 'destroy']);
});



// Course

Route::group(['prefix' => 'course', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/create', [CourseController::class, 'create']);
    Route::get('/{id}', [CourseController::class, 'index']);
    Route::put('/update/{course_id}/{step?}', [CourseController::class, 'update'])->whereIn('step',[0,1,2,3,4]);
});
