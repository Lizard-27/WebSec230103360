<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebSecController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/register', function () {
    return view('register');
});
Route::post('/register', [WebSecController::class, 'register']);

Route::get('/login', function () {
    return view('login');
});
Route::post('/login', [WebSecController::class, 'login']);

Route::post('/logout', [WebSecController::class, 'logout'])->name('logout');
