<?php
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/


Route::middleware(['guest'])->group(function (){
    Route::view('/user_login', 'frontend.auth.login')->name('user-login');
    Route::view('/user_register', 'frontend.auth.register')->name('user-register');
});


