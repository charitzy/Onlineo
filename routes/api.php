<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Models\Category;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\CartController;


// Route::get('/', function () {
//     return response()->json([
//         'message' => 'Welcome App Api test'
//     ]);
// });


Route::middleware('web')->get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Route::get('/csrf-token', function (Request $request) {
//     return response()->json(['csrf_token' => csrf_token()]);
// });

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome App Api test'
    ]);
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);





// //Registration and Login 
// Route::post('/register', [AuthController::class, 'register']);

Route::get('/products', [ProductController::class, 'index']);
//Public Routes
Route::get('/category/{category_id}', [CategoryController::class, 'search']);
Route::get('/category', [CategoryController::class, 'index']);
// Route::get('/category/{id}', [CategoryController::class, 'show']);

Route::get('/product', [ProductController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::get('/products/search/{name}', [ProductController::class, 'search']);


Route::get('/review/{id}', [ReviewController::class, 'show']);
Route::get('/review', [ReviewController::class, 'index']);


Route::get('/my-cart/{id}', [CartController::class, 'getAllProductInCart']);

//Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/category', [CategoryController::class, 'store']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']);

    //create a route for the check session function in AuthController

    // Route::get('/session-status', function () {
    //     // If the user is authenticated, return a success status.
    //     return response()->json(['status' => 'success']);
    // });
    Route::get('/check-session', [AuthController::class, 'checkSession']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::put('/product/{id}', [ProductController::class, 'update']);
    Route::post('/product', [ProductController::class, 'store']);

    Route::delete('/product/{id}', [ProductController::class, 'destroy']);

    Route::get('/cart', [CartController::class, 'index']);

    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{cart}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::post('/review', [ReviewController::class, 'store']);

    Route::delete('/review/{id}', [ReviewController::class, 'destroy']);


    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

    Route::get('/order-details', [OrderDetailController::class, 'index']);
    Route::post('/order-details', [OrderDetailController::class, 'store']);
    Route::delete('/order-details/{id}', [OrderDetailController::class, 'destroy']);

    Route::post('/payments', [PaymentController::class, 'store']);
    Route::put('/payments/{id}', [PaymentController::class, 'update']);
    Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);

    Route::get('/purchases', [PurchaseController::class, 'index']);

    Route::get('/pay/{id}', [PaymentController::class, 'payViaGcash']);
});
