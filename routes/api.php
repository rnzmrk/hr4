<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/offer-accepted', [App\Http\Controllers\Api\OfferAcceptedController::class, 'store'])->name('offer.accepted');
