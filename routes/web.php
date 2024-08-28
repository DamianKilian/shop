<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/category/{slug}', [App\Http\Controllers\HomeController::class, 'category'])->name('category');

Route::prefix('admin-panel')->group(function () {
    Route::get('/products', [App\Http\Controllers\AdminPanelProductsController::class, 'products'])->name('admin-panel-products');
    Route::post('/delete-products', [App\Http\Controllers\AdminPanelProductsController::class, 'deleteProducts'])->name('admin-panel-delete-products');
    Route::post('/get-products', [App\Http\Controllers\AdminPanelProductsController::class, 'getProducts'])->name('admin-panel-get-products');
    Route::post('/add-product', [App\Http\Controllers\AdminPanelProductsController::class, 'addProduct'])->name('admin-panel-add-product');
    Route::post('/get-product-filter-options', [App\Http\Controllers\AdminPanelProductsController::class, 'getProductFilterOptions'])->name('admin-panel-get-product-filter-options');
    Route::get('/categories', [App\Http\Controllers\AdminPanelCategoriesController::class, 'categories'])->name('admin-panel-categories');
    Route::get('/filters', [App\Http\Controllers\AdminPanelFiltersController::class, 'filters'])->name('admin-panel-filters');
    Route::post('/get-filters', [App\Http\Controllers\AdminPanelFiltersController::class, 'getFilters'])->name('admin-panel-get-filters');
    Route::post('/add-filter', [App\Http\Controllers\AdminPanelFiltersController::class, 'addFilter'])->name('admin-panel-add-filter');
    Route::post('/delete-filters', [App\Http\Controllers\AdminPanelFiltersController::class, 'deleteFilters'])->name('admin-panel-delete-filters');
    Route::post('/save-categories', [App\Http\Controllers\AdminPanelCategoriesController::class, 'saveCategories'])->name('admin-panel-save-categories');
});

Route::get('/ttt', [App\Http\Controllers\TestController::class, 'ttt']);
