<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Telegram\Bot\Laravel\Facades\Telegram;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
			$url = url('/' . env('TELEGRAM_BOT_TOKEN') . '/webhook');
    	Telegram::setWebhook(['url' => $url]);
    }
}
