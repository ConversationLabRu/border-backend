<?php

namespace App\Utils;

use App\Http\directions\borderCrossings\reports\Services\ReportService;
use AWS\CRT\Log;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Http;

class TelegramStatsUtils
{
    private ReportService $reportService;

    /**
     * @param ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    // Функция для получения дат за последние 7 дней, начиная с вчерашнего
    private function getLast7Days() {
        $last7Days = [];
        for ($i = 0; $i < 7; $i++) { // Начинаем с 0 (вчера) и идём до 6 (7 дней)
            $last7Days[] = date('Y-m-d', strtotime("-$i days", strtotime('-1 day')));
        }
        return $last7Days; // Вернёт 7 дней, начиная со вчера
    }

    // Функция для заполнения недостающих данных
    private function fillMissingDates($data) {
        $last7Days = $this->getLast7Days();

        foreach (['car', 'bus'] as $transport) {
            // Обработка timeWeekTo
            $existingDays = array_column($data['timeWeekTo'][$transport], 'day');

            foreach ($last7Days as $day) {
                if (!in_array($day, $existingDays)) {
                    // Если дата отсутствует, добавляем её с avg_time = 0
                    $data['timeWeekTo'][$transport][] = [
                        'day' => $day,
                        'avg_time' => '0.0'
                    ];
                } else {
                    // Если дата существует, конвертируем avg_time в часы
                    foreach ($data['timeWeekTo'][$transport] as &$entry) {
                        if ($entry['day'] === $day) {
//                            $entry['avg_time'] = number_format(floatval($entry['avg_time']) / 60, 1);
                        }
                    }
                }
            }

            // Сортируем по дате
            usort($data['timeWeekTo'][$transport], function($a, $b) {
                return strcmp($a['day'], $b['day']);
            });

            $data['timeWeekTo'][$transport] = array_slice($data['timeWeekTo'][$transport], -7);

            // Обработка timeWeekToFlip
            $existingDaysFlip = array_column($data['timeWeekToFlip'][$transport], 'day');

            foreach ($last7Days as $day) {
                if (!in_array($day, $existingDaysFlip)) {
                    // Если дата отсутствует, добавляем её с avg_time = 0
                    $data['timeWeekToFlip'][$transport][] = [
                        'day' => $day,
                        'avg_time' => '0.0'
                    ];
                } else {
                    // Если дата существует, конвертируем avg_time в часы
                    foreach ($data['timeWeekToFlip'][$transport] as &$entry) {
                        if ($entry['day'] === $day) {
//                            $entry['avg_time'] = number_format(floatval($entry['avg_time']) / 60, 1);
                        }
                    }
                }
            }

            // Сортируем по дате
            usort($data['timeWeekToFlip'][$transport], function($a, $b) {
                return strcmp($a['day'], $b['day']);
            });

            $data['timeWeekToFlip'][$transport] = array_slice($data['timeWeekToFlip'][$transport], -7);
        }

        // Возвращаем отсортированный массив
        return $data;
    }

    public function sendGraphStat(int $borderCrossingId, bool $flipped, string $nameBorder, string $chatName, string $fileName)
    {
        $dates = "";

        $res = $this->fillMissingDates($this->reportService->getStatForGraphPost($borderCrossingId));

        $car = "";

        if (!$flipped) {

            for ($i = 0; $i < count($res["timeWeekTo"]["car"]); $i++) {
                if ($i == count($res["timeWeekTo"]["car"]) - 1) {
                    $dates .= "\"" . (new DateTime($res["timeWeekTo"]["car"][$i]["day"]))->format('d.m') . "\"";
                    $car .= $res["timeWeekTo"]["car"][$i]["avg_time"];
                } else {
                    $dates .= "\"" . (new DateTime($res["timeWeekTo"]["car"][$i]["day"]))->format('d.m') . "\"" . ",";
                    $car .= $res["timeWeekTo"]["car"][$i]["avg_time"] . ",";
                }
            }

        } else {

            for ($i = 0; $i < count($res["timeWeekToFlip"]["car"]); $i++) {
                if ($i == count($res["timeWeekToFlip"]["car"]) - 1) {
                    $dates .= "\"" . (new DateTime($res["timeWeekToFlip"]["car"][$i]["day"]))->format('d.m') . "\"";
                    $car .= $res["timeWeekToFlip"]["car"][$i]["avg_time"];
                } else {
                    $dates .= "\"" . (new DateTime($res["timeWeekToFlip"]["car"][$i]["day"]))->format('d.m') . "\"" . ",";
                    $car .= $res["timeWeekToFlip"]["car"][$i]["avg_time"] . ",";
                }
            }

        }

        $bus = "";

        if (!$flipped) {

            for ($i = 0; $i < count($res["timeWeekTo"]["bus"]); $i++) {
                if ($i == count($res["timeWeekTo"]["bus"]) - 1) {
                    $bus .= $res["timeWeekTo"]["bus"][$i]["avg_time"];
                } else {
                    $bus .= $res["timeWeekTo"]["bus"][$i]["avg_time"] . ",";
                }
            }

        } else {

            for ($i = 0; $i < count($res["timeWeekToFlip"]["bus"]); $i++) {
                if ($i == count($res["timeWeekToFlip"]["bus"]) - 1) {
                    $bus .= $res["timeWeekToFlip"]["bus"][$i]["avg_time"];
                } else {
                    $bus .= $res["timeWeekToFlip"]["bus"][$i]["avg_time"] . ",";
                }
            }

        }

        $jsonData = "[\"$nameBorder\", [$dates], [$bus], [$car]]";

        $data = [
            'grandata' => $jsonData,
        ];

        $response = Http::asForm()->post("https://granica.conversationlab.ru/graph-php/gran.php?token=ADeZ9GiRYiNjcXkyZNtvr9y5UqEhoa2fMXFxbk5Qm3oMvkDgOXXW", $data);

        $imageContent = $response->body();
        $filePath = "public/images/$fileName";

        \Illuminate\Support\Facades\Log::info("JSON Data {$jsonData}");

        // Сохраняем файл
        if (file_put_contents($filePath, $imageContent) !== false) {
            \Illuminate\Support\Facades\Log::info("File successfully saved: {$filePath}");

            $datesArray = explode(",", $dates);

            $dateStart = $datesArray[0];
            $dateEnd = $datesArray[count($datesArray) - 1];

            $dateStart = str_replace("\"", "", $dateStart);
            $dateEnd = str_replace("\"", "", $dateEnd);

            $timestampNow = Carbon::now()->timestamp;
            $body = [
                'chat_id' => "$chatName",
                'caption' => "Статистика прохождения границы по направлению $nameBorder с $dateStart по $dateEnd",
                'photo' => "https://granica.conversationlab.ru/images/$fileName?t=$timestampNow",
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            [
                                'text' => 'Открыть приложение',
                                'url' => 'https://t.me/bordercrossingsbot/app'
                            ]
                        ]
                    ]
                ])
            ];

            $response = Http::post("https://api.telegram.org/bot7215428078:AAFY67PRE0nifeLeoISEwznfE2WEiXF6-xU/sendPhoto", $body);

            // Обработка ответа
            if ($response->successful()) {
                \Illuminate\Support\Facades\Log::info('Сообщение успешно отправлено в Telegram.');
            } else {
                \Illuminate\Support\Facades\Log::error('Ошибка отправки сообщения: ' . $response->body());
            }
        } else {
            Log::error("File not saved: {$filePath}");
        }


    }

}
