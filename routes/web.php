<?php

use Illuminate\Support\Facades\Route;

Route::get('/telegram', function () {
	Telegraph::message('this is great')->send();
});

Route::get('/', function () {
    return view('welcome');
});
