<?php

use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'home'])->name('home');

// Deep-link configurateur : /c/{product}?dim=...&qty=...&utm_source=...
Route::get('/c/{product}', [ShopController::class, 'product'])->name('configurator');

Route::get('/famille/{family}', [ShopController::class, 'family'])->name('family');
Route::get('/produit/{product}', [ShopController::class, 'product'])->name('product');
Route::get('/p/{page}', [ShopController::class, 'page'])->name('page');
Route::get('/merci/{order}', [ShopController::class, 'thankyou'])->name('thankyou');
