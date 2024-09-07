<?php 

namespace App\Telegram;

use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\Button;

class MessageBuilder
{
	private ?TelegraphChat $chat;
	private ?string $message;
	private array $data;

	public function __construct(TelegraphChat $chat, ?string $message = null, array $data = [])
	{
		$this->chat = $chat;
		$this->message = $message;
		$this->data = $data;
	}

	public function setMessage(string $message)
	{
		$this->message = $message;
	}

	public function setData(array $data)
	{
		$this->data = $data;
	}

	public function addData(array $newData)
	{
		$this->data = [...$this->data, ...$newData];
	}

	public function initChooseLanguageKeyboard()
	{
		$buttons = [];

		foreach($this->data['langs'] as $value => $title) {
			$buttons[] = Button::make($title)
				->action("settings")
				->param('key', $this->data['settings_field'])
				->param('value', $value);
		}

		return $this->chat->message($this->message)->keyboard(Keyboard::make()->buttons($buttons)->chunk(3));
	}

	public function send()
	{
		$this->chat->send();
	}
}