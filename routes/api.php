<?php

use Illuminate\Support\Facades\Route;

Route::prefix('lk')->name('lk.')->middleware(['json'])->group(function () {

	Route::prefix('auth')->name('auth.')->group(function () {
		Route::post('send_code', 'App\Http\Controllers\Lk\Auth\AuthController@sendCode');
		Route::post('accept_code', 'App\Http\Controllers\Lk\Auth\AuthController@acceptCode');
	    Route::post('login', 'App\Http\Controllers\Lk\Auth\AuthController@login');
	    Route::post('register', 'App\Http\Controllers\Lk\Auth\AuthController@register');
	    Route::post('logout', 'App\Http\Controllers\Lk\Auth\AuthController@logout');
	    Route::post('refresh', 'App\Http\Controllers\Lk\Auth\AuthController@refresh');
	});

	Route::middleware(['jwt.verify', 'phone.verify'])->group(function () {
	    Route::post('profile', 'App\Http\Controllers\Lk\ProfileController@profile');
	});
});

Route::prefix('admin')->name('admin.')->middleware(['jwt.verify', 'phone.verify', 'is_admin', 'json'])->group(function () {
	Route::post('home', 'App\Http\Controllers\Admin\AdminController@home');
});