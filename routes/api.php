<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/products', [ProductController::class, 'indexApi'])->name('api.products.index');
});
