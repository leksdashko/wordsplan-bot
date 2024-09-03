<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class UserInfoCommand extends Command
{
    protected string $name = "userinfo";
    protected string $description = "Get information about the user";

    public function handle()
    {
        $update = $this->getUpdate();
        $message = $update->getMessage();
        $chat = $message->getChat();
        $user = $message->getFrom();

        $userId = $user->getId();
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $username = $user->getUsername();
        $languageCode = $user->getLanguageCode();
        $chatId = $chat->getId();
        $chatType = $chat->getType();

        $response = "📄 **User Information**\n";
        $response .= "• **User ID**: {$userId}\n";
        $response .= "• **Name**: {$firstName} {$lastName}\n";
        $response .= "• **Username**: @{$username}\n";
        $response .= "• **Language**: {$languageCode}\n";
        $response .= "• **Chat ID**: {$chatId}\n";
        $response .= "• **Chat Type**: {$chatType}";

        $this->replyWithMessage([
            'text' => $response,
            'parse_mode' => 'Markdown'
        ]);
    }
}
