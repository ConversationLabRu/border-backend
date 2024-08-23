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
        $webAppUrl = env('WEB_APP_URL', 'default_url_if_not_set'); // 'default_url_if_not_set' используется как запасной вариант на случай, если переменная не установлена


        $this->chat->message($this->chat->chat_id) // Убедитесь, что вы используете правильный метод для установки чата
        ->message('Для продолжения откройте приложение!')
            ->keyboard(Keyboard::make()->buttons([
                Button::make('ОТКРЫТЬ ПРИЛОЖЕНИЕ')->webApp($webAppUrl),
            ]))
            ->send();
    }

    protected function handleUnknownCommand(Stringable $text): void
    {
        $webAppUrl = env('WEB_APP_URL', 'default_url_if_not_set'); // 'default_url_if_not_set' используется как запасной вариант на случай, если переменная не установлена

        $this->chat->message($this->chat->chat_id) // Убедитесь, что вы используете правильный метод для установки чата
        ->message('Для продолжения откройте приложение!')
            ->keyboard(Keyboard::make()->buttons([
                Button::make('ОТКРЫТЬ ПРИЛОЖЕНИЕ')->webApp($webAppUrl),
            ]))
            ->send();
    }

    protected function handleChatMessage(Stringable $text): void
    {
        $webAppUrl = env('WEB_APP_URL', 'default_url_if_not_set'); // 'default_url_if_not_set' используется как запасной вариант на случай, если переменная не установлена


        $this->chat->message($this->chat->chat_id) // Убедитесь, что вы используете правильный метод для установки чата
        ->message('Для продолжения откройте приложение!')
            ->keyboard(Keyboard::make()->buttons([
                Button::make('ОТКРЫТЬ ПРИЛОЖЕНИЕ')->webApp($webAppUrl),
            ]))
            ->send();
    }
}
