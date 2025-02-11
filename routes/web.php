<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\ProductController;

Route::get('/nearby-products', [ProductController::class, 'showNearbyProducts']);


use App\Http\Controllers\UserController;


// ব্যবহারকারী রাউট
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');

// পণ্য রাউট
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');


Route::get('/search-products', [ProductController::class, 'showSearchForm'])->name('products.search.form');

// পণ্য সার্চ করুন
Route::get('/search-products/results', [ProductController::class, 'search'])->name('products.search');
