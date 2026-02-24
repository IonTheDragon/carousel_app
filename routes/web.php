<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('lk')->name('lk.')->group(function () {

	Route::prefix('auth')->name('auth.')->group(function () {
		Route::get('vk_auth', 'App\Http\Controllers\Lk\Auth\AuthController@vkAuth')->name('vk_auth');
		Route::get('ya_auth', 'App\Http\Controllers\Lk\Auth\AuthController@yaAuth')->name('ya_auth');
	});
});
