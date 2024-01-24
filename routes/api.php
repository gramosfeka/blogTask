<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:api');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');


Route::prefix('posts')->middleware(['auth:api'])->group(function(){
    Route::get('/', [PostController::class, 'index'])->name('posts.index');
    Route::post('/store', [PostController::class, 'store'])->name('posts.store');
    Route::get('/show/{id}', [PostController::class, 'show'])->name('posts.show');
    Route::put('/update/{id}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/destroy/{id}', [PostController::class, 'destroy'])->name('posts.destroy');


});

Route::prefix('{post_id}/comments')->middleware(['auth:api'])->group(function () {
    Route::get('/', [CommentController::class, 'index'])->name('comments.index');
    Route::post('/store', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/{comment_id}', [CommentController::class, 'show'])->name('comments.show');
    Route::put('/update/{comment_id}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/destroy/{comment_id}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

