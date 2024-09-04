<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use DefStudio\Telegraph\Models\TelegraphBot;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('telegram-update', function () {
	$bot = TelegraphBot::find(1);

	$bot->registerCommands([
		'/adding' => 'Start the adding mode',
		'/learning' => 'Start the learning mode',
		'/stop' => 'Turn off all the modes',
		'/help' => 'Instruction'
	])->send();
});
