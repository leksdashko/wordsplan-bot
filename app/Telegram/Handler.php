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
use App\Models\Vocabulary;
use App\Models\Settings;
use App\Telegram\TelegramFacade;

class Handler extends WebhookHandler
{
	public function start(): void
	{
		$facade = TelegramFacade::init($this->chat->chat_id);
		$chat = $facade->getChat();
		$message = "Good to see you again";
		
		if($facade->getCurrentMode() === ModeHandler::MODE_INITIAL){
			// new user
			$message = "Hello! I'm a *WordsPlan* bot. I will help you to lern new words";

			$facade->setMode(ModeHandler::MODE_APP_LANGUAGE);
		}

		$this->_checkAccess($facade, function() use ($chat, $message) {
			$chat->message($message)->keyboard(Keyboard::make()->buttons([
				Button::make("📚 Learning")
					->action("learning")
					->param('chat_id', $chat->id),
				Button::make("🛠️ Settings")
					->action("settings")
					->param('chat_id', $chat->id),
				Button::make("🌎 Website")
					->url(env("APP_URL")),
				Button::make("📝 Contacts")
					->action("contact")
					->param('chat_id', $chat->id)
			])->chunk(2))->send();
		});
	}

	public function stop(): void
	{
		$chat = $this->_getChat();

		$chat->message("Stopped")->send();
	}

	public function learning(): void
	{
		\Log::info($this->chat);
		
		// show the list of the words to learn
		// $chat->message("Learning mode started")->send();
	}

	public function settings(): void
	{
		$facade = TelegramFacade::init($this->chat->chat_id);
		$chat = $facade->getChat();

		$facade->getModeHandler()->check();

		$this->_checkAccess($facade, function() use ($chat) {
			
		});
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
		$chat = $this->_getChat();

		//set the mode submit form
		$message = "Write your message here. We will contact back to you!";

		$chat->message($message)->protected()->keyboard(Keyboard::make()->buttons([
			Button::make("👈 Go Back")->action("back")->param('from', 1),
			Button::make("🏠 Main")->action("main")
		])->chunk(2))->send();
	}

	public function add(string $text): void
	{
		$specialChars = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '=', '+', '[', ']', '{', '}', '\\', '|', ';', ':', '\'', '"', '<', '.', '>', '/', '?', '~', '`', '©', '®', '™', '§', '°', '¶', '•', '…', '¬', '±', '÷', '×', '£', '€', '¥', '¢', '¤', '¶'];
		$chat = $this->_getChat();

		$text = Str::title(Str::replace($specialChars, "", Str::squish($text)));
		$strings = explode(',', $text);
		foreach($strings as $words){
			if(empty(trim($words))) continue;

			$couple = explode(' ', trim($words));
			$word = $couple[0] ?? "";
			$translation = $couple[1] ?? "";

			if(!empty($translation)){
				try {
					$vc = Vocabulary::where([
						'word' => $word,
						'telegraph_chat_id' => $chat->id
					])->first();

					if($vc){
						$vc->update([
							'translation' => $translation,
						]);
					}else{
						Vocabulary::create([
							'telegraph_chat_id' => $chat->id,
							'word' => $word,
							'translation' => $translation,
						]);
					}

					$chat->message("✅ " . $word . " - " . $translation)->protected()->send();
				} catch (\Exception $e){
					\Log::info($e);
					$chat->message("❌ Error! Try to add it again. " . $word . " - " . $translation)->protected()->send();
				}
			}else{
				$chat->message("❌ add a translation for the word - " . $word)->protected()->send();
			}
		}
	}

	public function help(): void
	{
		$this->reply('Im helper');
	}

	public function actions()
	{
		$is_learned = $this->data->get('type') == 'repeat' ? true : false;
		$chat = $this->_getChat();
		$vc = Vocabulary::where([
			'telegraph_chat_id' => $chat->id,
			'is_learned' => $is_learned
		])->first();

		if(!$vc){
			return $chat->message('You should add new words 📚')
				->keyboard(Keyboard::make()
				->buttons([
					Button::make("💪 Repeat old words")->action("actions")->param('chat_id', $chat->chat_id)->param('type', 'repeat'),
				])
				->chunk(2))->send();
		}

		$buttons = [
			Button::make("👀 See translation")->action("translation")->param('id', $vc->id)->param('chat_id', $chat->chat_id),
    ];

		if(!$vc->is_learned){
			$buttons[] = Button::make("✅ Mark as learned")->action("learned")->param('id', $vc->id)->param('chat_id', $chat->chat_id);
		} else {
			$buttons[] = Button::make("🤷‍♂️ Learn again")
				->action("learned")
				->param('id', $vc->id)
				->param('chat_id', $chat->chat_id)
				->param('again', true);
		}

		$chat->message($vc->word)->keyboard(Keyboard::make()->buttons($buttons)->chunk(2))->send();
	}

	public function like(): void
	{
		$this->reply('Thanks for the like');
	}

	public function translation(): void
	{
		$id = $this->data->get('id');
		$chat = $this->_getChat();
		
		try {
			$vc = Vocabulary::where([
				'id' => $id,
				'telegraph_chat_id' => $chat->id
			])->first();

			if(!$vc) {
				$this->reply("Error");
			}

			$this->reply($vc->translation);
		} catch (\Exception $e) {
			$this->reply("Error! Try again");
		}
	}

	public function repeat(): void
	{
		$chat = $this->_getChat();
		$vc = Vocabulary::where([
			'telegraph_chat_id' => $chat->id,
			'is_learned' => true
		])->first();
	}

	public function learned(): void
	{
		$learnAgain = $this->data->get('again');
		$id = $this->data->get('id');
		$chat = $this->_getChat();
		
		try {
			$vc = Vocabulary::where([
				'id' => $id,
				'telegraph_chat_id' => $chat->id
			])->first();

			if($vc) {
				$vc->update([
					'is_learned' => $learnAgain ? false : true,
				]);

				$message = "You've learned - " . $vc->word;
				if($learnAgain){
					$message = 'You will learn word "' . $vc->word . '" again';
				}

				$this->reply($message);
			}
		} catch (\Exception $e) {
			$this->reply("Error! Try again");
		}
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
		\Log::info($text->value());

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

	private function _checkAccess($facade, $nextStep)
	{
		$messageBuilder = new MessageBuilder($facade->getChat());
		$messageBuilder->setData(['langs' => $facade->getLangs()]);

		switch($facade->getCurrentMode()) {
			case ModeHandler::MODE_APP_LANGUAGE:
				$messageBuilder->setMessage("Choose app language:");
				$messageBuilder->addData(['settings_field' => 'app_lang']);
				$messageBuilder->initChooseLanguageKeyboard()->send();
				break;
			case ModeHandler::MODE_LEARN_LANGUAGE:
				$messageBuilder->setMessage("Choose learning language:");
				$messageBuilder->addData(['settings_field' => 'learning_lang']);
				$messageBuilder->initChooseLanguageKeyboard()->send();
				break;
			case ModeHandler::MODE_USER_LANGUAGE:
				$messageBuilder->setMessage("Choose your language:");
				$messageBuilder->addData(['settings_field' => 'user_lang']);
				$messageBuilder->initChooseLanguageKeyboard()->send();
				break;
			default:
				$nextStep();
		}
	}
}