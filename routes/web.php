<?php

use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingVisibilityController;
use App\Http\Controllers\MyListingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/sq');

Route::middleware(SetLocale::class)
    ->prefix('{locale}')
    ->where(['locale' => 'sq|en'])
    ->group(function (): void {
        Route::get('/', HomeController::class)->name('home');
        Route::get('/catalog', CatalogController::class)->name('catalog.index');
        Route::get('/items/{listing}', [ListingController::class, 'show'])->name('listings.show');
        Route::get('/members/{user}', ProfileController::class)->name('profiles.show');

        Route::middleware('guest')->group(function (): void {
            Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
            Route::post('/login', [AuthenticatedSessionController::class, 'store'])
                ->middleware('throttle:5,1');
            Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
            Route::post('/register', [RegisteredUserController::class, 'store'])
                ->middleware('throttle:5,1');
        });

        Route::middleware('auth')->group(function (): void {
            Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
            Route::get('/settings', [AccountSettingsController::class, 'edit'])->name('account.edit');
            Route::put('/settings/profile', [AccountSettingsController::class, 'updateProfile'])->name('account.profile.update');
            Route::post('/settings/addresses', [AccountSettingsController::class, 'storeAddress'])->name('account.addresses.store');
            Route::put('/settings/addresses/{address}', [AccountSettingsController::class, 'updateAddress'])->name('account.addresses.update');
            Route::delete('/settings/addresses/{address}', [AccountSettingsController::class, 'destroyAddress'])->name('account.addresses.destroy');
            Route::get('/sell', [ListingController::class, 'create'])->name('listings.create');
            Route::post('/items', [ListingController::class, 'store'])->name('listings.store');
            Route::get('/items/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
            Route::put('/items/{listing}', [ListingController::class, 'update'])->name('listings.update');
            Route::patch('/items/{listing}/visibility', ListingVisibilityController::class)->name('listings.visibility');
            Route::delete('/items/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
            Route::get('/my/listings', MyListingsController::class)->name('my-listings.index');
            Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
            Route::post('/favorites/{listing}', [FavoriteController::class, 'store'])->name('favorites.store');
            Route::delete('/favorites/{listing}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
        });
    });
