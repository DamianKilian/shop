<?php

use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::get('/suggestions', [App\Http\Controllers\AdminPanelProductsController::class, 'suggestions'])->name('suggestions');
Route::post('/get-products-view', [App\Http\Controllers\HomeController::class, 'getProductsView'])->name('get-products-view');
