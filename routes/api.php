<?php

use App\Http\Controllers\ShopItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{user}', [UserController::class, 'show']);
    Route::put('/{user}', [UserController::class, 'update']);
    Route::delete('/{user}', [UserController::class, 'destroy']);
});

Route::prefix('item')->group(function () {
    Route::get('/', [ShopItemController::class, 'index']);
    Route::post('/', [ShopItemController::class, 'store']);
    Route::get('/{shopItem}', [ShopItemController::class, 'show']);
    Route::put('/{shopItem}', [ShopItemController::class, 'update']);
    Route::delete('/{shopItem}', [ShopItemController::class, 'destroy']);
});