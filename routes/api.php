<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::get('gethomepage', [HomeController::class, 'forhomepageapi']);
    Route::get('productdesc/{id}', [HomeController::class, 'getproductdesc']);
   
    Route::middleware('api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('verify-otp', [AuthController::class, 'forgotOTPVerify']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
        Route::post('addtocart', [HomeController::class, 'addtocart']);
        Route::post('update-cart-item', [HomeController::class, 'updateCartItem']);
        Route::post('delete-cart-item', [HomeController::class, 'deleteCartItem']);
    });
});