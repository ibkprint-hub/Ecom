<?php

use App\Http\Controllers\InstallController;
use App\Http\Controllers\ShopController;
use App\Http\Middleware\RedirectIfInstalled;
use Illuminate\Support\Facades\Route;

// Assistant d'installation web (désactivé une fois l'app installée)
Route::middleware(RedirectIfInstalled::class)->prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('index');
    Route::get('/configure', [InstallController::class, 'configure'])->name('configure');
    Route::post('/configure', [InstallController::class, 'process'])->name('process');
    Route::get('/complete', [InstallController::class, 'complete'])->name('complete');
});

Route::get('/', [ShopController::class, 'home'])->name('home');

// Deep-link configurateur : /c/{product}?dim=...&qty=...&utm_source=...
Route::get('/c/{product}', [ShopController::class, 'product'])->name('configurator');

Route::get('/famille/{family}', [ShopController::class, 'family'])->name('family');
Route::get('/produit/{product}', [ShopController::class, 'product'])->name('product');
Route::get('/p/{page}', [ShopController::class, 'page'])->name('page');
Route::get('/merci/{order}', [ShopController::class, 'thankyou'])->name('thankyou');
