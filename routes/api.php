<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('lk')->name('lk.')->group(function () {

	Route::prefix('auth')->name('auth.')->group(function () {
		Route::post('send_code', 'App\Http\Controllers\Lk\Auth\AuthController@sendCode');
	    Route::post('login', 'App\Http\Controllers\Lk\Auth\AuthController@login');
	});

	Route::middleware(['jwt.verify:lk'])->group(function () {
	    Route::post('profile', 'App\Http\Controllers\Lk\ProfileController@profile');
	});
});