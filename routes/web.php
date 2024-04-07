<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/order/create/{product}', [OrderController::class, 'create'])->name('order.create');
Route::post('/order/list', [OrderController::class, 'list'])->name('order.list');
Route::get('/order', [OrderController::class, 'search'])->name('order.search');
Route::get('/order/pay/retry/{order.code}', [OrderController::class, 'retryPay'])->name('order.pay.retry');
Route::get('/order/detail/{order}', [OrderController::class, 'detail'])->name('order.detail');
Route::post('/order/pay/{product}', [OrderController::class, 'pay'])->name('order.pay');
