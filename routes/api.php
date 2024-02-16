<?php

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PostController;
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

Route::post('register', [AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);

Route::middleware(['auth:api'])->group(function(){
    Route::get('profile',[ProfileController::class,'profile']);
    Route::get('profile-posts',[ProfileController::class,'profilePosts']);

    Route::post('logout',[AuthController::class,'logout']);

    Route::get('categories',[CategoryController::class,'index']);

    Route::get('post',[PostController::class,'index']);
    Route::post('post',[PostController::class,'create']);
    Route::get('post/{id}',[PostController::class,'show']);
});
