<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin-panel')->group(function () {
    Route::get('/products', [App\Http\Controllers\AdminPanelController::class, 'products'])->name('admin-panel-products');
    Route::post('/get-products', [App\Http\Controllers\AdminPanelController::class, 'getProducts'])->name('admin-panel-get-products');
    Route::post('/add-product', [App\Http\Controllers\AdminPanelController::class, 'addProduct'])->name('admin-panel-add-product');
    Route::get('/categories', [App\Http\Controllers\AdminPanelController::class, 'categories'])->name('admin-panel-categories');
    Route::post('/save-categories', [App\Http\Controllers\AdminPanelController::class, 'saveCategories'])->name('admin-panel-save-categories');
});

Route::get('/ttt', [App\Http\Controllers\TestController::class, 'ttt']);
