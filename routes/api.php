<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\StylistController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\TestController;

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

// Routes de test
Route::get('/test', [TestController::class, 'test']);
Route::get('/hello/{name?}', [TestController::class, 'hello']);
Route::get('/info', [TestController::class, 'info']);

// Routes d'authentification
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('profile', [AuthController::class, 'userProfile']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
});

// Routes pour les stylistes
Route::group(['prefix' => 'stylists'], function () {
    Route::get('/', [StylistController::class, 'index']);
    Route::get('/{id}', [StylistController::class, 'show']);
    Route::get('/{id}/products', [StylistController::class, 'products']);
    Route::get('/{id}/statistics', [StylistController::class, 'statistics']);
    
    // Routes protégées pour les stylistes
    Route::middleware('auth:api')->group(function () {
        Route::put('/{id}', [StylistController::class, 'update']);
        Route::put('/{id}/availability', [StylistController::class, 'updateAvailability']);
    });
});

// Routes pour les produits
Route::group(['prefix' => 'products'], function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/category/{category}', [ProductController::class, 'byCategory']);
    Route::get('/{id}', [ProductController::class, 'show']);
    
    // Routes protégées pour les produits
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });
});

// Routes pour les commandes
Route::middleware('auth:api')->group(function () {
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
    });
});

// Routes pour les avis
Route::group(['prefix' => 'reviews'], function () {
    Route::get('/product/{productId}', [ReviewController::class, 'productReviews']);
    Route::get('/stylist/{stylistId}', [ReviewController::class, 'stylistReviews']);
    
    // Routes protégées pour les avis
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [ReviewController::class, 'store']);
        Route::put('/{id}', [ReviewController::class, 'update']);
        Route::delete('/{id}', [ReviewController::class, 'destroy']);
    });
});
