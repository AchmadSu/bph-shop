<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShipmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user());

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::controller(UserController::class)->group(function () {
    Route::post('auth', 'login');
    Route::middleware(['auth.jwt.cookie'])->group(function () {
        Route::get('me', 'me');
        Route::post('logout', 'logout');
    });
});


/*
|--------------------------------------------------------------------------
| PRODUCT ROUTES
|--------------------------------------------------------------------------
*/
Route::controller(ProductController::class)
    ->group(function () {
        Route::get('products', 'index');
        Route::middleware(['auth.jwt.cookie'])->group(function () {
            Route::get('product/{id}', 'show');
            Route::middleware('role:admin')->group(function () {
                Route::get('products/all', 'getAll');
                Route::post('products', 'store');
                Route::put('products/{id}', 'update');
                Route::post('products/import', 'importExcel');
            });
        });
    });


/*
|--------------------------------------------------------------------------
| CART ROUTES (USER)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth.jwt.cookie'])
    ->controller(CartController::class)
    ->group(function () {
        Route::get('cart', 'show');
        Route::post('cart/add', 'add');
        Route::put('cart/update', 'update');
        Route::post('cart/remove', 'remove');
    });


/*
|--------------------------------------------------------------------------
| ORDER ROUTES (USER)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth.jwt.cookie'])
    ->controller(OrderController::class)
    ->group(function () {
        Route::post('order/checkout', 'checkout');
        Route::get('orders', 'listForUser');
        Route::get('orders/{id}', 'show');
        Route::middleware('role:cs1')->group(function () {
            Route::get('payment/waiting', 'waitingVerifyOrders');
        });
    });


/*
|--------------------------------------------------------------------------
| PAYMENT ROUTES (Buyer + CS1 verification)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth.jwt.cookie'])
    ->controller(PaymentController::class)
    ->group(function () {
        Route::post('orders/{orderId}/payments', 'uploadProof');
        Route::middleware('role:cs1')->group(function () {
            Route::put('payments/{paymentId}/verify', 'verify');
        });
    });


/*
|--------------------------------------------------------------------------
| SHIPMENT ROUTES (CS2)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth.jwt.cookie'])
    ->controller(ShipmentController::class)
    ->group(function () {
        Route::middleware('role:cs2')->group(function () {
            Route::get('shipment', 'readyOrders');
            Route::put('shipment/{orderId}/status/{status}', 'updateStatus');
        });
        Route::get('shipment/{orderId}/logs', 'logs');
    });
