<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('{slug?}', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/category/{slug}', [App\Http\Controllers\HomeController::class, 'category'])->name('category');

Route::prefix('admin-panel')->group(function () {
    Route::get('/products', [App\Http\Controllers\AdminPanelProductsController::class, 'products'])->name('admin-panel-products');
    Route::post('/delete-products', [App\Http\Controllers\AdminPanelProductsController::class, 'deleteProducts'])->name('admin-panel-delete-products');
    Route::post('/get-products', [App\Http\Controllers\AdminPanelProductsController::class, 'getProducts'])->name('admin-panel-get-products');
    Route::post('/add-product', [App\Http\Controllers\AdminPanelProductsController::class, 'addProduct'])->name('admin-panel-add-product');
    Route::post('/get-product-filter-options', [App\Http\Controllers\AdminPanelProductsController::class, 'getProductFilterOptions'])->name('admin-panel-get-product-filter-options');
    Route::post('/get-product-desc', [App\Http\Controllers\AdminPanelProductsController::class, 'getProductDesc'])->name('admin-panel-get-product-desc');
    Route::get('/categories', [App\Http\Controllers\AdminPanelCategoriesController::class, 'categories'])->name('admin-panel-categories');
    Route::get('/filters', [App\Http\Controllers\AdminPanelFiltersController::class, 'filters'])->name('admin-panel-filters');
    Route::post('/get-filters', [App\Http\Controllers\AdminPanelFiltersController::class, 'getFilters'])->name('admin-panel-get-filters');
    Route::post('/add-filter', [App\Http\Controllers\AdminPanelFiltersController::class, 'addFilter'])->name('admin-panel-add-filter');
    Route::post('/delete-filters', [App\Http\Controllers\AdminPanelFiltersController::class, 'deleteFilters'])->name('admin-panel-delete-filters');
    Route::get('/pages', [App\Http\Controllers\AdminPanelPagesController::class, 'pages'])->name('admin-panel-pages');
    Route::post('/get-pages', [App\Http\Controllers\AdminPanelPagesController::class, 'getPages'])->name('admin-panel-get-pages');
    Route::post('/get-page', [App\Http\Controllers\AdminPanelPagesController::class, 'getPage'])->name('admin-panel-get-page');
    Route::post('/add-page', [App\Http\Controllers\AdminPanelPagesController::class, 'addPage'])->name('admin-panel-add-page');
    Route::post('/delete-page', [App\Http\Controllers\AdminPanelPagesController::class, 'deletePages'])->name('admin-panel-delete-pages');
    Route::post('/save-categories', [App\Http\Controllers\AdminPanelCategoriesController::class, 'saveCategories'])->name('admin-panel-save-categories');
    Route::post('/add-options-to-selected-products', [App\Http\Controllers\AdminPanelProductsController::class, 'addOptionsToSelectedProducts'])->name('admin-panel-add-options-to-selected-products');
});

Route::get('/ttt', [App\Http\Controllers\TestController::class, 'ttt']);
