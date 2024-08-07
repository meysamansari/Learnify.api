<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController;
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



// Auth

Route::group(['prefix' => 'auth'], function () {
    Route::post('verification-code-request', [AuthController::class, 'sendVerificationCode']);
    Route::post('login/{type?}', [AuthController::class, 'login'])->whereIn('type',['student','mentor']);
    Route::post('logout', [AuthController::class, 'logout']);
});



// User

Route::group(['prefix' => 'user', 'middleware' => 'auth:sanctum'], function () {
    Route::put('/update', [UserController::class, 'update']);
    Route::get('/show', [UserController::class, 'show']);
});



// Media

Route::group(['prefix' => 'media', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/video', [MediaController::class, 'uploadVideo']);
    Route::post('/image', [MediaController::class, 'uploadImage']);
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
    Route::put('/{blog_id}',[BlogController::class, 'update']);
    Route::delete('/{id}',[BlogController::class, 'destroy']);
});



// Course

Route::group(['prefix' => 'course', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/create', [CourseController::class, 'create']);

    Route::put('/status/{course_id}', [CourseController::class, 'courseStatus']);
    Route::put('/update/{course_id}/{step?}', [CourseController::class, 'update'])->whereIn('step',[0,1,2,3,4]);
    Route::delete('/delete/{course_id}', [CourseController::class, 'destroy']);
    Route::get('/index', [CourseController::class, 'index']);
    Route::get('/latest', [CourseController::class, 'IndexLatest']);
    Route::get('/popular/free', [CourseController::class, 'IndexPopularFree']);
});

  Route::get('course/show/{course_id}', [CourseController::class, 'show']);

// Comment

Route::group(['prefix' => 'comment','middleware'=>'auth:sanctum'], function () {
    Route::get('/',[CommentController::class, 'index']);
    Route::post('/{course_id}', [CommentController::class, 'store']);
    Route::get('/{comment_id}',[CommentController::class, 'show']);
    Route::put('/{comment_id}',[CommentController::class, 'update']);
    Route::post('/reply/{course_id}',[CommentController::class, 'reply']);
    Route::delete('/{id}',[CommentController::class, 'destroy']);
});



// Ticket

Route::group(['prefix' => 'ticket','middleware'=>'auth:sanctum'], function (){
    Route::get('/',[TicketController::class, 'index']);
    Route::post('/{course_id}',[TicketController::class, 'store']);
    Route::get('/{ticket_id}',[TicketController::class, 'show']);
    Route::put('/{id}',[TicketController::class, 'update']);
    Route::post('/reply/{course_id}',[TicketController::class, 'reply']);
    Route::delete('/{id}',[TicketController::class, 'destroy']);
});



// Category

Route::group(['prefix' => 'category', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/index', [CategoryController::class, 'index']);
    Route::get('/show/{category_id}', [CategoryController::class, 'show']);
});



// Favorite

Route::group(['prefix' => 'favorite', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/index', [FavoriteController::class, 'index']);
});



// Order

Route::group(['prefix' => 'order', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/show', [OrderController::class, 'show']);
    Route::post('/store/{course_id}', [OrderController::class, 'store']);
    Route::delete('/delete/{course_id}', [OrderController::class, 'destroy']);
});


// Pay



Route::group(['prefix' => 'transaction'], function () {
    Route::post('/pay/{order_id}', [TransactionController::class, 'pay']);
    Route::get('/callback', [TransactionController::class, 'callback'])->name('payment.callback');
});


// Filter



Route::group(['prefix' => 'filter'], function () {
    Route::post('/course', [FilterController::class, 'filterAndSortCourses']);
});
