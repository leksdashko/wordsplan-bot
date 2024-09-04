<?php 

namespace App\Telegram;

use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Models\TelegraphBot;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Stringable;
use Illuminate\Support\Str;

class Handler extends WebhookHandler
{
	public function start(): void
	{
		$bot = TelegraphBot::find(1);
		$info = $this->message->toArray();
		$chatId = $info['chat']['id'];
		$message = "Good to see you again";

		$chat = TelegraphChat::where('chat_id', $chatId)->get();
		if(!$chat){
			$message = "Hello! I'm a *WordsPlan* bot. I will help you to lern new words";

			$chat = $bot->chats()->create([
				'chat_id' => $chatId
			]);
		}

		Telegraph::message($message)->keyboard(Keyboard::make()->buttons([
			Button::make("📚 Learning")->action("learning"),
			Button::make("🛠️ Settings")->action("settings"),
			Button::make("🌎 Website")->url(env("APP_URL")),
			Button::make("📝 Contacts")->action("contact")
		])->chunk(2))->send();
	}

	public function stop(): void
	{
		$this->reply("Stopped");
	}

	public function learning(): void
	{
		// show the list of the words to learn
		Telegraph::message("Learning mode started")->send();
	}

	public function settings(): void
	{
		$this->reply("Settings action");
	}

	public function main(): void
	{
		$this->reply("Main action");
	}

	public function back(): void
	{
		$from = $this->data->get('from');

		$this->reply("Back action");
	}

	public function contact(): void
	{
		//set the mode submit form
		$message = "Write your message here. We will contact back to you!";

		Telegraph::message($message)->protected()->keyboard(Keyboard::make()->buttons([
			Button::make("👈 Go Back")->action("back")->param('from', 1),
			Button::make("🏠 Main")->action("main")
		])->chunk(2))->send();
	}

	public function add(string $text): void
	{
		$specialChars = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '=', '+', '[', ']', '{', '}', '\\', '|', ';', ':', '\'', '"', '<', '.', '>', '/', '?', '~', '`', '©', '®', '™', '§', '°', '¶', '•', '…', '¬', '±', '÷', '×', '£', '€', '¥', '¢', '¤', '¶'];
		$text = Str::title(Str::replace($specialChars, "", Str::squish($text)));
		$strings = explode(',', $text);
		foreach($strings as $words){
			if(empty(trim($words))) continue;

			$couple = explode(' ', trim($words));
			$word = $couple[0] ?? "";
			$translation = $couple[1] ?? "";

			if(!empty($translation)){
				// add to db
				Telegraph::message("✅ " . $word . " - " . $translation)->protected()->send();
			}else{
				Telegraph::message("❌ add a translation for the word - " . $word)->protected()->send();
			}
		}
	}

	public function help(): void
	{
		$this->reply('Im helper');
	}

	public function actions(): void
	{
		Telegraph::message('car')->keyboard(Keyboard::make()->buttons([
        Button::make("👀 See translation")->action("translation")->param('id', 1)->param('langs', 'en-ua'),
        Button::make("✅ Mark as learned")->action("learned")->param('id', 1)
        // Button::make("Like")->action("like"),
				// Button::make(" Open")->url('https://test.it')  
    ])->chunk(2))->send();
	}

	public function like(): void
	{
		$this->reply('Thanks for the like');
	}

	public function translation(): void
	{
		$id = $this->data->get('id');
		$langs = explode('-', $this->data->get('langs'));
		$from = $langs[0];
		$to = $langs[1];

		$this->reply('Машина - ' . $id . '; lang - ' . $from);
	}

	public function learned(): void
	{
		$id = $this->data->get('id');

		$this->reply("You've learned this word - " . $id);
	}

	public function read(): void
	{
		$this->reply('You have read the message with id : ' . $this->data->get('id'));
	}

	public function adding(): void
	{
		//set up the adding mode

		$this->reply("Adding mode");
	}

	protected function handleUnknownCommand(Stringable $text): void
	{
		$this->reply('What do you want?');
	}

	protected function handleChatMessage(Stringable $text): void
	{
		$mode = 'adding';
		if($mode == 'adding'){
			$words = explode(' ', $text->value());
			$word = $words[0] ?? "";
			$translation = $words[1] ?? "";

			$this->reply($word . ' - ' . $translation);
		}else{
			$this->reply('Just replying');
		}
	}
}