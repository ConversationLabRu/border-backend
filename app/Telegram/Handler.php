<?php

namespace App\Telegram;

use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;

class Handler extends WebhookHandler
{
    public function start(): void
    {
        Telegraph::chat($this->chat->chat_id) // Убедитесь, что вы используете правильный метод для установки чата
        ->message('Для продолжения откройте приложение!')
            ->keyboard(Keyboard::make()->buttons([
                Button::make('ОТКРЫТЬ ПРИЛОЖЕНИЕ')->webApp('https://354e-195-190-102-194.ngrok-free.app'),
            ]))
            ->send();
    }

    protected function handleUnknownCommand(Stringable $text): void
    {
        Telegraph::chat($this->chat->chat_id) // Убедитесь, что вы используете правильный метод для установки чата
        ->message('Для продолжения откройте приложение!')
            ->keyboard(Keyboard::make()->buttons([
                Button::make('ОТКРЫТЬ ПРИЛОЖЕНИЕ')->webApp('https://354e-195-190-102-194.ngrok-free.app'),
            ]))
            ->send();
    }

    protected function handleChatMessage(Stringable $text): void
    {
        Telegraph::chat($this->chat->chat_id) // Убедитесь, что вы используете правильный метод для установки чата
        ->message('Для продолжения откройте приложение!')
            ->keyboard(Keyboard::make()->buttons([
                Button::make('ОТКРЫТЬ ПРИЛОЖЕНИЕ')->webApp('https://354e-195-190-102-194.ngrok-free.app'),
            ]))
            ->send();
    }
}
