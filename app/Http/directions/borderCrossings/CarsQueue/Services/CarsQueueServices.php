<?php

namespace App\Http\directions\borderCrossings\CarsQueue\Services;

use App\Http\directions\borderCrossings\CarsQueue\Dto\CarsQueueCreateDto;
use App\Http\directions\borderCrossings\CarsQueue\Dto\CarsQueueDto;
use App\Http\directions\borderCrossings\CarsQueue\Entities\CarsQueue;
use App\Http\directions\borderCrossings\Entities\BorderCrossing;
use App\Utils\LogUtils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CarsQueueServices
{
    public function getLastQueueByBorderCrossing(Request $request)
    {
        $borderCrossingId = (int) $request->query('border_crossing_id');


        $carsCountTime = Carbon::now();
        $carsCountReverseTime = Carbon::now();
        $carsTime = Carbon::now();

        // Первый запрос
        $carsCount = CarsQueue::where("border_crossing_id", $borderCrossingId)
            ->where("route_reverse", false)
            ->where('create_report_timestamp', '>=', $carsCountTime->subHours(2)) // Условие на 2 часа
            ->latest('id')
            ->first();

        Log::info($carsCountTime);

        // Второй запрос
        $carsCountReverse = CarsQueue::where("border_crossing_id", $borderCrossingId)
            ->where("route_reverse", true)
            ->where('create_report_timestamp', '>=', $carsCountReverseTime->subHours(2)) // То же условие для обратного маршрута
            ->latest('id')
            ->first();

        Log::info($carsCountReverseTime);


        // Функция для форматирования времени
        function formatTimeAgo($timestamp, $curTime)
        {
            $diffInSeconds = $curTime->diffInSeconds($timestamp);

            $hours = floor($diffInSeconds / 3600);
            $minutes = floor(($diffInSeconds % 3600) / 60);

            // Форматирование с ведущими нулями
            $formattedMinutes = sprintf('%02d', $minutes);

            return "{$hours}:{$formattedMinutes} назад";
        }

        // Получаем количество
        $carsCountDto = new CarsQueueDto(
            $carsCount ? $carsCount->count . ' (' . formatTimeAgo($carsCount->create_report_timestamp, $carsTime) . ')' : "Нет данных",
            $carsCountReverse ? $carsCountReverse->count . ' (' . formatTimeAgo($carsCountReverse->create_report_timestamp, $carsTime) . ')' : "Нет данных"
        );

        return $carsCountDto->toArray();
    }

    public function createQueue(Request $request)
    {
        $request->validate([
            'border_crossing_id' => 'required|exists:borderсrossings,id',
            'count' => 'required|integer',
            'route_reverse' => 'nullable|boolean'
        ]);

        $carsQueue = new CarsQueue();

        $carsQueue->border_crossing_id = $request->border_crossing_id; // Убедитесь, что это значение получает
        $carsQueue->count = $request->count;
        $carsQueue->route_reverse = $request->route_reverse;

        $carsQueue->create_report_timestamp = Carbon::now(); // Используйте объект Carbon напрямую

        $carsQueue->save();

        LogUtils::elasticLog($request, "Указал количество машин на переходе {$request->get('border_crossing_id')}");


        $headerString = explode(" ", $request->header('Authorization'))[1];

        // Парсинг строки запроса
        parse_str($headerString, $params);

        // Декодирование параметра user
        $userData = json_decode(urldecode($params['user']), true);

        $id = $userData['id'];

        if ($id !== 241666959) {
            $firstName = $userData['first_name'];

            $body2 = [
                'chat_id' => 241666959,
                'text' => "Создал отчет о количестве машин перед КПП $request->border_crossing_id" . "\n\n" . "Кол-во машин: $request->count" . "\n\n" . "FirstName: " . $firstName,
            ];

            $response2 = Http::post("https://api.telegram.org/bot7215428078:AAFY67PRE0nifeLeoISEwznfE2WEiXF6-xU/sendMessage", $body2);

            // Обработка ответа
            if ($response2->successful()) {
                Log::info('Сообщение успешно отправлено в Telegram.');
            } else {
                Log::error('Ошибка отправки сообщения: ' . $response2->body());
            }
        }

        return $carsQueue;
    }
}
