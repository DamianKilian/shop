<?php

use App\Http\Controllers\Account\AddressController;
use App\Http\Controllers\Account\OrderController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/webPayment.php';

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('{slug?}', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home')
    ->middleware('cacheResponse');
Route::get('/category/{slug}', [App\Http\Controllers\HomeController::class, 'category'])
    ->name('category')
    ->middleware('cacheResponse');
Route::get('/product/{slug}', [App\Http\Controllers\HomeController::class, 'product'])
    ->name('product')
    ->middleware('cacheResponse');

Route::prefix('basket')->group(function () {
    Route::get('/index', [App\Http\Controllers\BasketController::class, 'basketIndex'])->name('basket-index');
});

Route::prefix('order')->group(function () {
    Route::post('/store', [App\Http\Controllers\BasketController::class, 'orderStore'])->name('order-store');
    Route::get('/payment/{order}', [App\Http\Controllers\BasketController::class, 'orderPayment'])->name('order-payment');
    Route::get('/completed/{order}', [App\Http\Controllers\BasketController::class, 'orderCompleted'])->name('order-completed');
});

Route::prefix('account')->group(function () {
    Route::get('/addresses', [AddressController::class, 'addresses'])->name('addresses');
    Route::post('/get-addresses', [AddressController::class, 'getAddresses'])->name('get-addresses');
    Route::post('/add-address', [AddressController::class, 'addAddress'])->name('add-address');
    Route::post('/delete-addresses', [AddressController::class, 'deleteAddresses'])->name('delete-addresses');
    Route::post('/set-default-address', [AddressController::class, 'setDefaultAddress'])->name('set-default-address');

    Route::get('/orders', [OrderController::class, 'orders'])->name('orders');
    Route::post('/get-orders', [OrderController::class, 'getOrders'])->name('get-orders');
});

Route::prefix('admin-panel')->middleware('can:admin')->group(function () {
    Route::get('/products', [App\Http\Controllers\AdminPanelProductsController::class, 'products'])->name('admin-panel-products');
    Route::post('/delete-products', [App\Http\Controllers\AdminPanelProductsController::class, 'deleteProducts'])->name('admin-panel-delete-products');
    Route::post('/get-products', [App\Http\Controllers\AdminPanelProductsController::class, 'getProducts'])->name('admin-panel-get-products');
    Route::post('/add-product', [App\Http\Controllers\AdminPanelProductsController::class, 'addProduct'])->name('admin-panel-add-product');
    Route::post('/get-product-filter-options', [App\Http\Controllers\AdminPanelProductsController::class, 'getProductFilterOptions'])->name('admin-panel-get-product-filter-options');
    Route::post('/get-product-desc', [App\Http\Controllers\AdminPanelProductsController::class, 'getProductDesc'])->name('admin-panel-get-product-desc');
    Route::post('/add-options-to-selected-products', [App\Http\Controllers\AdminPanelProductsController::class, 'addOptionsToSelectedProducts'])->name('admin-panel-add-options-to-selected-products');
    Route::post('/toggle-active-product', [App\Http\Controllers\AdminPanelProductsController::class, 'toggleActiveProduct'])->name('admin-panel-toggle-active-product');
    Route::post('/apply-changes-product', [App\Http\Controllers\AdminPanelProductsController::class, 'applyChangesProduct'])->name('admin-panel-apply-changes-product');

    Route::get('/categories', [App\Http\Controllers\AdminPanelCategoriesController::class, 'categories'])->name('admin-panel-categories');
    Route::post('/save-categories', [App\Http\Controllers\AdminPanelCategoriesController::class, 'saveCategories'])->name('admin-panel-save-categories');

    Route::get('/filters', [App\Http\Controllers\AdminPanelFiltersController::class, 'filters'])->name('admin-panel-filters');
    Route::post('/get-filters', [App\Http\Controllers\AdminPanelFiltersController::class, 'getFilters'])->name('admin-panel-get-filters');
    Route::post('/add-filter', [App\Http\Controllers\AdminPanelFiltersController::class, 'addFilter'])->name('admin-panel-add-filter');
    Route::post('/delete-filters', [App\Http\Controllers\AdminPanelFiltersController::class, 'deleteFilters'])->name('admin-panel-delete-filters');

    Route::get('/settings', [App\Http\Controllers\AdminPanelSettingsController::class, 'settings'])->name('admin-panel-settings');
    Route::post('/get-settings', [App\Http\Controllers\AdminPanelSettingsController::class, 'getSettings'])->name('admin-panel-get-settings');
    Route::post('/restore-settings', [App\Http\Controllers\AdminPanelSettingsController::class, 'restoreSettings'])->name('admin-panel-restore-settings');
    Route::post('/save-setting', [App\Http\Controllers\AdminPanelSettingsController::class, 'saveSetting'])->name('admin-panel-save-setting');

    Route::get('/footer', [App\Http\Controllers\AdminPanelFooterController::class, 'footer'])->name('admin-panel-footer');
    Route::post('/get-footer', [App\Http\Controllers\AdminPanelFooterController::class, 'getFooter'])->name('admin-panel-get-footer');
    Route::post('/save-footer', [App\Http\Controllers\AdminPanelFooterController::class, 'saveFooter'])->name('admin-panel-save-footer');

    Route::get('/pages', [App\Http\Controllers\AdminPanelPagesController::class, 'pages'])->name('admin-panel-pages');
    Route::post('/get-pages', [App\Http\Controllers\AdminPanelPagesController::class, 'getPages'])->name('admin-panel-get-pages');
    Route::post('/get-page', [App\Http\Controllers\AdminPanelPagesController::class, 'getPage'])->name('admin-panel-get-page');
    Route::post('/add-page', [App\Http\Controllers\AdminPanelPagesController::class, 'addPage'])->name('admin-panel-add-page');
    Route::post('/toggle-active', [App\Http\Controllers\AdminPanelPagesController::class, 'toggleActive'])->name('admin-panel-toggle-active');
    Route::post('/apply-changes', [App\Http\Controllers\AdminPanelPagesController::class, 'applyChanges'])->name('admin-panel-apply-changes');
    Route::post('/delete-page', [App\Http\Controllers\AdminPanelPagesController::class, 'deletePage'])->name('admin-panel-delete-page');

    Route::get('/delivery-methods', [App\Http\Controllers\AdminPanelDeliveryMethodsController::class, 'deliveryMethods'])->name('admin-panel-delivery-methods');
    Route::post('/add-delivery-method', [App\Http\Controllers\AdminPanelDeliveryMethodsController::class, 'addDeliveryMethod'])->name('admin-panel-add-delivery-method');
    Route::post('/get-delivery-methods', [App\Http\Controllers\AdminPanelDeliveryMethodsController::class, 'getDeliveryMethods'])->name('admin-panel-get-delivery-methods');
    Route::post('/delete-delivery-methods', [App\Http\Controllers\AdminPanelDeliveryMethodsController::class, 'deleteDeliveryMethods'])->name('admin-panel-delete-delivery-methods');

    Route::get('/orders', [App\Http\Controllers\AdminPanelOrdersController::class, 'orders'])->name('admin-panel-orders');
    Route::post('/add-order', [App\Http\Controllers\AdminPanelOrdersController::class, 'addOrder'])->name('admin-panel-add-order');
    Route::post('/get-orders', [App\Http\Controllers\AdminPanelOrdersController::class, 'getOrders'])->name('admin-panel-get-orders');
    Route::post('/delete-orders', [App\Http\Controllers\AdminPanelOrdersController::class, 'deleteOrders'])->name('admin-panel-delete-orders');

    Route::post('/editorjs/upload-attachment', [App\Http\Controllers\AdminPanelEditorjsController::class, 'uploadAttachment'])->name('admin-panel-upload-attachment')->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post('/editorjs/upload-file', [App\Http\Controllers\AdminPanelEditorjsController::class, 'uploadFile'])->name('admin-panel-upload-file')->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post('/editorjs/fetch-url', [App\Http\Controllers\AdminPanelEditorjsController::class, 'fetchUrl'])->name('admin-panel-fetch-url')->withoutMiddleware([VerifyCsrfToken::class]);

    Route::middleware('can:usersManagement')->group(function () {
        Route::get('/users', [App\Http\Controllers\AdminPanelUsersController::class, 'users'])->name('admin-panel-users');
        Route::post('/get-users', [App\Http\Controllers\AdminPanelUsersController::class, 'getUsers'])->name('admin-panel-get-users');
        Route::post('/search-users', [App\Http\Controllers\AdminPanelUsersController::class, 'searchUsers'])->name('admin-panel-search-users');
        Route::post('/set-permission', [App\Http\Controllers\AdminPanelUsersController::class, 'setPermission'])->name('admin-panel-set-permission');
    });
});

// Route::get('/test/ttt', [App\Http\Controllers\TestController::class, 'ttt']);
