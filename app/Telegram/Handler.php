<?php

namespace App\Telegram;

use App\Utils\LogUtils;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use Ramsey\Uuid\Type\Integer;

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

    public function sendPhoto(Request $request)
    {
        // Валидация файла изображения
        $request->validate([
            'image' => 'required|image|max:2048', // Ограничение на размер файла - 2MB
        ]);

        Log::info("test", ["Test" => $request->all()]);

        // Получаем файл изображения
        $image = $request->file('image');

        // Определяем путь сохранения файла с именем 'statistic'
        $fileName = 'statistic.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('public/images', $fileName);
        $fullPath = storage_path("app/public/images/{$fileName}");

        // Проверка, успешно ли сохранен файл
        if (file_exists($fullPath)) {
            Log::info("File successfully saved: {$fullPath}");
        } else {
            Log::error("File not saved: {$fullPath}");
        }

        // Получение параметров из заголовка
        $headerString = explode(" ", $request->header('Authorization'))[1];
        parse_str($headerString, $params);
        $userData = json_decode(urldecode($params['user']), true);
        $userId = intval($userData['id']);

        $url = "https://api.telegram.org/bot7215428078:AAFY67PRE0nifeLeoISEwznfE2WEiXF6-xU/sendPhoto";
        $data = json_encode([
            'photo' => 'https://granica.conversationlab.ru/storage/images/statistic.jpg',
            'chat_id' => $userId,
            'caption' => "Очереди на границах: @bordercrossingsbot"
        ]);

        $options = [
            'http' => [
                'header'  => "Content-type: application/json",
                'method'  => 'POST',
                'content' => $data,
            ],
        ];

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        // Вывод ответа
        return response()->json(['message' => 'Фото отправлено', 'response' => json_decode($response)], 200);
    }



}
