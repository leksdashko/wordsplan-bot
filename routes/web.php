<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

Route::post('/' . env('TELEGRAM_BOT_TOKEN') . '/webhook', function () {
    $update = Telegram::commandsHandler(true);
    return 'ok';
});

Route::get('/', function () {
    return view('welcome');
});
