<?php

use App\Http\Controllers\Api\ListingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', fn () => response()->json([
        'data' => [
            'status' => 'ok',
            'api_version' => 'v1',
        ],
    ]))->name('api.v1.health');

    Route::get('/listings', [ListingController::class, 'index'])->name('api.v1.listings.index');
    Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('api.v1.listings.show');
});
