<?php 

namespace App\Telegram;

use DefStudio\Telegraph\Models\TelegraphBot;
use DefStudio\Telegraph\Models\TelegraphChat;
use App\Models\Settings;

class TelegramFacade
{
    private static ?self $instance = null;
    private ?TelegraphBot $bot = null;
    private ?TelegraphChat $chat = null;
    private ?Settings $settings = null;
		private ?ModeHandler $modeHandler = null;

		private ?int $apiChatId = null;
    private int $botId = 1;

    private array $langs = [
        'en' => 'English',
        'ua' => 'Ukrainian',
        'ru' => 'Russian',
        'ar' => 'Arabic',
        'de' => 'German',
        'it' => 'Italian',
        'pl' => 'Polish',
        'pt' => 'Portuguese',
        'es' => 'Spanish'
    ];

    private function __construct() {}

    private function __clone() {}

    private function __wakeup() {}

    public static function init(int $apiChatId): self
    {
			if (self::$instance) return self::$instance;

			self::$instance = new self();

			self::$instance->apiChatId = $apiChatId;

			self::$instance->bot = TelegraphBot::find(self::$instance->botId);
			if (!self::$instance->bot) {
				// error
			}

			self::$instance->chat = self::$instance->setChat($apiChatId);

			if(!self::$instance->settings){
				self::$instance->settings = Settings::where('telegraph_chat_id', self::$instance->chat->id)->first();
			}

			self::$instance->modeHandler = ModeHandler::init(self::$instance->settings);

			return self::$instance;
    }

    public function getChat(): ?TelegraphChat
    {
      return $this->chat;
    }

		public function setChat(int $apiChatId): TelegraphChat
    {
      $chat = TelegraphChat::where('chat_id', $apiChatId)->first();
			if(!$chat){
				$chat = $this->bot->chats()->create([
					'chat_id' => $apiChatId
				]);
	
				if ($chat) {
					$this->settings = Settings::updateOrCreate(
						['telegraph_chat_id' => $chat->id],
						['mode' => 1]
					);
				}
			}

			return $chat;
    }

    public function getBot(): ?TelegraphBot
    {
      return $this->bot;
    }

		public function getCurrentMode(): string
		{
			return $this->settings->mode;
		}

		public function setMode($mode)
		{
			if($this->settings->mode != $mode){
				$this->settings->update([
					'mode' => $mode,
				]);
			}
		}

		public function getLangs(): array
		{
			return $this->langs;
		}

		public function getModeHandler(): ?ModeHandler
    {
      return $this->modeHandler;
    }
}
