<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\SaleItemController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductListController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\SaleHistoryController;
use App\Http\Controllers\StockInItemController;
use App\Http\Controllers\StockOutItemController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\StockInHistoryController;
use App\Http\Controllers\StockOutHistoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [DashboardController::class, 'index'])->middleware('auth');

// Login
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::get('/logout', [LoginController::class, 'logout']);

// Suppliers
Route::resource('/suppliers', SupplierController::class)->middleware('auth');

// Product
Route::get('/product', function () {
    return redirect('/product/list');
});
Route::resource('/product/units', ProductUnitController::class)->middleware('auth');
Route::resource('/product/list', ProductListController::class)->middleware('auth');
Route::resource('/product/categories', ProductCategoryController::class)->middleware('auth');

// Transactions
Route::resource('/transaction/in', StockInController::class)->middleware('auth');
Route::resource('/transaction/in/items', StockInItemController::class)->middleware('auth');
Route::resource('/transaction/out', StockOutController::class)->middleware('auth');
Route::resource('/transaction/out/items', StockOutItemController::class)->middleware('auth');
Route::resource('/transaction/sales', SaleController::class)->middleware('auth');
Route::resource('/transaction/sales/items', SaleItemController::class)->middleware('auth');

// History
Route::resource('/history/sales', SaleHistoryController::class)->middleware('auth');
Route::resource('/history/in', StockInHistoryController::class)->middleware('auth');
Route::resource('/history/out', StockOutHistoryController::class)->middleware('auth');

// User
Route::put('/employee/password/{id}', [EmployeeController::class, 'updatePassword'])->middleware('auth');
Route::resource('/employee', EmployeeController::class)->middleware('auth');
