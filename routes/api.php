<?php

use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::get('/suggestions', [App\Http\Controllers\AdminPanelProductsController::class, 'suggestions'])->name('suggestions');
