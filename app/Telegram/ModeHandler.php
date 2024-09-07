<?php 

namespace App\Telegram;

use App\Models\Settings;

class ModeHandler
{
	private static ?self $instance = null;
	private ?Settings $settings = null;

	const MODE_INITIAL = 1;
	const MODE_APP_LANGUAGE = 2;
	const MODE_LEARN_LANGUAGE = 3;
	const MODE_USER_LANGUAGE = 4;
	const MODE_MAIN = 5;
	const MODE_ADDING = 6;
	const MODE_LEARNING = 7;
	const MODE_CONTACT = 8;

	private function __construct() {}

	private function __clone() {}

	private function __wakeup() {}

	public static function init(Settings $settings): self
	{
		if (self::$instance) return self::$instance;

		self::$instance = new self();

		self::$instance->settings = $settings;

		return self::$instance;
	}

	public function check(): void
	{
		\Log::info('dasdasdas');
	}
}