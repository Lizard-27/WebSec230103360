<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/even-odd', function () {
    return view('even-odd');
});
Route::get('/prime', function () {
    return view('prime-numbers');
});
Route::get('/multiplication', function () {
    return view('multiplication-table');
});
