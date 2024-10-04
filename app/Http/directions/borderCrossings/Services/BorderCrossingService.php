<?php

namespace App\Http\directions\borderCrossings\Services;

use App\Http\directions\borderCrossings\Dto\CacheDTO;
use App\Http\directions\borderCrossings\Dto\CachePolandDTO;
use App\Http\directions\borderCrossings\Dto\CityDTO;
use App\Http\directions\borderCrossings\Dto\CountryDTO;
use App\Http\directions\borderCrossings\Dto\DirectionCrossingDTO;
use App\Http\directions\borderCrossings\Entities\BorderCrossing;
use App\Utils\LogUtils;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use DOMDocument;
use DOMXPath;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Response;
use Ramsey\Uuid\Type\Integer;
include("simple_html_dom.php");

class BorderCrossingService
{
    public static function getBorderCrossingById(int $id)
    {
        $direction = BorderCrossing::with('fromCity.country', 'toCity.country')
            ->where("id", $id)
            ->first();

        $fromCity = $direction->fromCity;
        $toCity = $direction->toCity;

        $fromCityDTO = $fromCity ? new CityDTO(
            $fromCity->name,
            $fromCity->country ? new CountryDTO(
                $fromCity->country->name,
                $fromCity->country->logo
            ) : null
        ) : null;

        $toCityDTO = $toCity ? new CityDTO(
            $toCity->name,
            $toCity->country ? new CountryDTO(
                $toCity->country->name,
                $toCity->country->logo
            ) : null
        ) : null;

        $directionDTO = new DirectionCrossingDTO(
            $direction->id,
            $direction->is_quque,
            $direction->header_image,
            $fromCityDTO,
            $toCityDTO,
            $direction->url_arcticle,
            $direction->is_bus,
            $direction->is_walking,
            $direction->is_car,
            null,
            null
        );

        return $directionDTO;
    }

    public function getAllBorderCrossings(Request $request)
    {
        $directionId = (int) $request->query('directionId');

        if ($directionId == 0) {
            throw new \ArgumentCountError("Не передан directionId");
        }

        // Загружаем данные с отношениями
        $directions = BorderCrossing::with('fromCity.country', 'toCity.country')
            ->where("direction_id", $directionId)
            ->get();

        LogUtils::elasticLog($request, "Перешел на страницу с Погран-переходами направления: ".$directionId);


        // Проверка наличия данных
        if ($directions->isEmpty()) {
            LogUtils::elasticLog($request, 'No directions found for directionId: ' . $directionId);
        }

        $result = $directions->map(function (BorderCrossing $direction) {
            $fromCity = $direction->fromCity;
            $toCity = $direction->toCity;

            $fromCityDTO = $fromCity ? new CityDTO(
                $fromCity->name,
                $fromCity->country ? new CountryDTO(
                    $fromCity->country->name,
                    $fromCity->country->logo
                ) : null
            ) : null;

            $toCityDTO = $toCity ? new CityDTO(
                $toCity->name,
                $toCity->country ? new CountryDTO(
                    $toCity->country->name,
                    $toCity->country->logo
                ) : null
            ) : null;

            $directionDTO = new DirectionCrossingDTO(
                $direction->id,
                $direction->is_quque,
                $direction->header_image,
                $fromCityDTO,
                $toCityDTO,
                $direction->url_arcticle,
                $direction->is_bus,
                $direction->is_walking,
                $direction->is_car,
                $direction->chat_id,
                $direction->chat_logo
            );

            if ($directionDTO->getFromCity()->getCountry()->getName() == "Беларусь" || $directionDTO->getToCity()->getCountry()->getName() == "Беларусь") {

//                $response = $this->fetchDataFromExternalApi($directionDTO);

                if ($directionDTO->getFromCity()->getName() == "Каменный Лог" || $directionDTO->getToCity()->getName() == "Каменный Лог") {
                    $data = Cache::get("kameni_log");
                } elseif ($directionDTO->getFromCity()->getName() == "Брест" || $directionDTO->getToCity()->getName() == "Брест") {
                    $data = Cache::get("brest");
                } else {
                    $data = Cache::get("benyakoni");
                }
//                    $data = Cache::get("kameni_log");
//
//                    if ($data == null) {
//                        $response = Http::get("https://belarusborder.by/info/monitoring-new?token=test&checkpointId=b60677d4-8a00-4f93-a781-e129e1692a03");
//                        $data = $this->convertToCacheObjectBelarusInfo($response);
//                        Cache::put('kameni_log', $data, now()->addMinutes(1));
//                    }
//
//                    return $data;
//                } elseif ($directionDTO->getFromCity()->getName() == "Брест" || $directionDTO->getToCity()->getName() == "Брест") {
//                    $data = Cache::get("brest");
//
//                    if ($data == null) {
//                        $response = Http::get("https://belarusborder.by/info/monitoring-new?token=test&checkpointId=a9173a85-3fc0-424c-84f0-defa632481e4");
//                        $data = $this->convertToCacheObjectBelarusInfo($response);
//                        Cache::put('brest', $data, now()->addMinutes(5));
//                    }
//
//                    return $data;
//                }
//
//                $data = Cache::get("benyakoni");
//
//                if ($data == null) {
//                    $response = Http::get("https://belarusborder.by/info/monitoring-new?token=test&checkpointId=53d94097-2b34-11ec-8467-ac1f6bf889c0");
//                    $data = $this->convertToCacheObjectBelarusInfo($response);
//                    Cache::put('benyakoni', $data, now()->addMinutes(5));
//
//                }
//
//                return $data;

                $response = $data;
                if ($response !== null) $directionDTO->setCache($response->toArray());
            }

            if ($directionDTO->getFromCity()->getCountry()->getName() == "Польша" || $directionDTO->getToCity()->getCountry()->getName() == "Польша") {

                $response = null;

                if ($directionDTO->getFromCity()->getName() == "Terespol" || $directionDTO->getToCity()->getName() == "Terespol")
                {
                    $data = Cache::get("terespol");

                } elseif ($directionDTO->getFromCity()->getName() == "Bezledy" || $directionDTO->getToCity()->getName() == "Bezledy") {
                    $data = Cache::get("bezledy");

                }else {
                    $data = Cache::get("grzechotki");
                }

                $response = $data;

                if ($response !== null) $directionDTO->setCachePolandInfo($response->toArray());
            }


//            return $data; // Return as array
            return $directionDTO->toArray(); // Return as array
        });


        return $result;
    }

    function fetchDataFromExternalApi()
    {
        $response = Http::get("https://belarusborder.by/info/monitoring-new?token=test&checkpointId=b60677d4-8a00-4f93-a781-e129e1692a03");
        $data = $this->convertToCacheObjectBelarusInfo($response);
        Cache::put('kameni_log', $data, now()->addHours(48));

        $response = Http::get("https://belarusborder.by/info/monitoring-new?token=test&checkpointId=a9173a85-3fc0-424c-84f0-defa632481e4");
        $data = $this->convertToCacheObjectBelarusInfo($response);
        Cache::put('brest', $data, now()->addHours(48));

        $response = Http::get("https://belarusborder.by/info/monitoring-new?token=test&checkpointId=53d94097-2b34-11ec-8467-ac1f6bf889c0");
        $data = $this->convertToCacheObjectBelarusInfo($response);
        Cache::put('benyakoni', $data, now()->addHours(48));

//        if ($directionDTO->getFromCity()->getName() == "Каменный Лог" || $directionDTO->getToCity()->getName() == "Каменный Лог") {
//            $data = Cache::get("kameni_log");
//
//            if ($data == null) {
//                $response = Http::get("https://belarusborder.by/info/monitoring-new?token=test&checkpointId=b60677d4-8a00-4f93-a781-e129e1692a03");
//                $data = $this->convertToCacheObjectBelarusInfo($response);
//                Cache::put('kameni_log', $data, now()->addMinutes(1));
//            }
//
//            return $data;
//        } elseif ($directionDTO->getFromCity()->getName() == "Брест" || $directionDTO->getToCity()->getName() == "Брест") {
//            $data = Cache::get("brest");
//
//            if ($data == null) {
//                $response = Http::get("https://belarusborder.by/info/monitoring-new?token=test&checkpointId=a9173a85-3fc0-424c-84f0-defa632481e4");
//                $data = $this->convertToCacheObjectBelarusInfo($response);
//                Cache::put('brest', $data, now()->addMinutes(5));
//            }
//
//            return $data;
//        }
//
//        $data = Cache::get("benyakoni");
//
//        if ($data == null) {
//            $response = Http::get("https://belarusborder.by/info/monitoring-new?token=test&checkpointId=53d94097-2b34-11ec-8467-ac1f6bf889c0");
//            $data = $this->convertToCacheObjectBelarusInfo($response);
//            Cache::put('benyakoni', $data, now()->addMinutes(5));
//
//        }
//
//        return $data;
    }

    function convertToCacheObjectBelarusInfo(\Illuminate\Http\Client\Response $response) : ?CacheDTO
    {
        try {
            if (sizeof($response['carLiveQueue']) == 0) {
                Log::info("Информация о машинах нет");


                $cacheDTO = new CacheDTO("0", 0);
                return $cacheDTO;
            };

            // Получаем время регистрации
            $regTime = $response['carLiveQueue'][0]['registration_date'];

            // Вычисляем разницу во времени
            $timeDifference = $this->calculateTimeDifference($regTime);
            $hours = $timeDifference['hours'];
            $minutes = $timeDifference['minutes'];

            // Формируем части строки
            $parts = [];

            if ($hours > 0) {
                $parts[] = $this->declensionHours($hours);
            }

            if ($minutes > 0) {
                $parts[] = $this->declensionMinutes($minutes);
            }

            // Формируем итоговую строку
            if (count($parts) > 0) {
                Log::info("Информация о машинах есть");

                $cacheDTO = new CacheDTO(
                    implode(' ', $parts),
                    sizeof($response->json()['carLiveQueue'])
                );
                return $cacheDTO;

            } else {
                Log::info("Информация о машинах нет");


                $cacheDTO = new CacheDTO("0", 0);
                return $cacheDTO;
            }
        } catch (\Exception $e) {
            $cacheDTO = new CacheDTO("0", 0);
            return $cacheDTO;
        }
    }

    private function setPolandCache(CachePolandDTO $data, string $keyName)
    {
        if ($data->getTimeBusFormatString() == "" || $data->getTimeAutoFormatString() == "" || $data->getTimeUpdate() == "") {
            Cache::put($keyName, $data, now()->addHours(48));
        } else {
            Cache::put($keyName, $data, now()->addHours(48));
        }
    }

    function fetchDataFromPolandApi()
    {
//        if ($directionDTO->getFromCity()->getName() == "Terespol" || $directionDTO->getToCity()->getName() == "Terespol")
//        {
//
//            $data = Cache::get("terespol");
//
//            if ($data == null) {
                // Установка User-Agent для имитации запроса от браузера
                $options = [
                    'http' => [
                        'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                    ]
                ];

                $context = stream_context_create($options);

                $response = file_get_html("https://granica.gov.pl/index_wait.php?p=b&c=t&v=ru&k=w", false, $context);

                // Получаем содержимое ответа
                $data = $this->convertToCacheObjectPolandTerespolInfo($response);

                $this->setPolandCache($data, "terespol");
//            }
//
//            return $data;
//
//        }
//
//        if ($directionDTO->getFromCity()->getName() == "Bezledy" || $directionDTO->getToCity()->getName() == "Bezledy") {
//
//            $data = Cache::get("bezledy");
//
//            if ($data == null) {
                // Установка User-Agent для имитации запроса от браузера

                $context = stream_context_create($options);

                $response = file_get_html("https://granica.gov.pl/index_wait.php?p=fr&c=t&v=ru&k=w", false, $context);

                // Получаем содержимое ответа
                $data = $this->convertToCacheObjectPolandBezledyInfo($response);

                $this->setPolandCache($data, "bezledy");
//            }
//
//            return $data;
//
//        }
//
//        if ($directionDTO->getFromCity()->getName() == "Grzechotki" || $directionDTO->getToCity()->getName() == "Grzechotki") {
//
//            $data = Cache::get("grzechotki");
//
//            if ($data == null) {
//                // Установка User-Agent для имитации запроса от браузера
//                $options = [
//                    'http' => [
//                        'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
//                    ]
//                ];
//
                $context = stream_context_create($options);

                $response = file_get_html("https://granica.gov.pl/index_wait.php?p=fr&c=t&v=ru&k=w", false, $context);

                // Получаем содержимое ответа
                $data = $this->convertToCacheObjectPolandGrzechotkiInfo($response);

                $this->setPolandCache($data, "grzechotki");
//            }
//
//            return $data;
//        }

    }

    function convertToCacheObjectPolandGrzechotkiInfo(\simple_html_dom $response) : ?CachePolandDTO
    {
        try {
            Log::info("Информация с Польских границ имеется");
            Log::info("dadada", ["dasda" => $response->find("td")]);

//            // Выполняем запрос для автобусов
            $nodes_bus = explode("&", $response->find("td", 14)->text())[0];
//
//            // Выполняем запрос для легковых автомобилей
            $nodes_car = explode("&", $response->find("td", 20)->text())[0];
//
            $nodes_query_update_time = $response->find("td", 25)->text();

            $cacheDTO = new CachePolandDTO(
                $nodes_bus,
                $nodes_car,
                $nodes_query_update_time
            );
            return $cacheDTO;

        } catch (\Exception $e) {
            Log::info("Информации с Польских границ нет");

            $cacheDTO = new CachePolandDTO(
                "0",
                "0",
                "0"
            );
            return $cacheDTO;
        }
    }

    function convertToCacheObjectPolandBezledyInfo(\simple_html_dom $response) : ?CachePolandDTO
    {
        try {
            Log::info("Информация с Польских границ имеется");
            Log::info("dadada", ["dasda" => $response->find("td")]);

//            // Выполняем запрос для автобусов
            $nodes_bus = explode("&", $response->find("td", 15)->text())[0];
//
//            // Выполняем запрос для легковых автомобилей
            $nodes_car = explode("&", $response->find("td", 21)->text())[0];
//
            $nodes_query_update_time = $response->find("td", 26)->text();

            $cacheDTO = new CachePolandDTO(
                $nodes_bus,
                $nodes_car,
                $nodes_query_update_time
            );
            return $cacheDTO;

        } catch (\Exception $e) {
            Log::info("Информации с Польских границ нет");

            $cacheDTO = new CachePolandDTO(
                "0",
                "0",
                "0"
            );
            return $cacheDTO;
        }
    }

    function convertToCacheObjectPolandTerespolInfo(\simple_html_dom $response) : ?CachePolandDTO
    {
        try {
            Log::info("Информация с Польских границ имеется");

            // Выполняем запрос для автобусов
            $nodes_bus = explode("&", $response->find(".dane", 10)->text())[0];

            // Выполняем запрос для легковых автомобилей
            $nodes_car = explode("&", $response->find(".dane1", 7)->text())[0];

            $nodes_query_update_time = $response->find("td", 35)->text();

            $cacheDTO = new CachePolandDTO(
                $nodes_bus,
                $nodes_car,
                $nodes_query_update_time
            );
            return $cacheDTO;

        } catch (\Exception $e) {
            Log::info("Информации с Польских границ нет");

            $cacheDTO = new CachePolandDTO(
                "0",
                "0",
                "0"
            );
            return $cacheDTO;
        }
    }

    function calculateTimeDifference($registrationDate) {

        // Разделяем строку на дату и время
        list($timeStr, $dateStr) = explode(' ', $registrationDate);

        // Разделяем дату на день, месяц и год
        list($day, $month, $year) = explode('.', $dateStr);

        // Разделяем время на часы, минуты и секунды
        list($hours, $minutes, $seconds) = explode(':', $timeStr);

        // Создаем объект даты и времени в UTC
        $registrationDateUTC = new DateTime("$year-$month-$day $hours:$minutes:$seconds", new DateTimeZone('Europe/Minsk'));

        // Текущее время в UTC+3 (Минск, Беларусь)
        $belarusTime = new DateTime('now', new DateTimeZone('Europe/Minsk'));

        // Вычисляем разницу
        $interval = $belarusTime->diff($registrationDateUTC);

        // Получаем разницу в часах и минутах
        $totalHours = $interval->days * 24 + $interval->h; // Учитываем дни в часы
        $totalMinutes = $totalHours * 60 + $interval->i; // Конвертируем часы в минуты и добавляем минуты

        // Для удобства, если вам нужно вернуть только часы и минуты
        $hoursDiff = floor($totalMinutes / 60);
        $minutesDiff = $totalMinutes % 60;

        return [
            'hours' => $hoursDiff,
            'minutes' => $minutesDiff
        ];
    }

    /**
     * Функция для склонения слова "час" в зависимости от числа.
     *
     * @param int $count Число, для которого нужно склонить слово.
     * @return string Склоненное слово "час" в зависимости от числа.
     */
    public static function declensionHours(int $count): string {
        $number = abs($count) % 100; // Берем абсолютное значение и последние две цифры
        $lastDigit = $number % 10; // Последняя цифра

        if ($number > 10 && $number < 20) {
            // Если число от 11 до 19 включительно
            return "$count часов";
        }

        switch ($lastDigit) {
            case 1:
                return "$count час";
            case 2:
            case 3:
            case 4:
                return "$count часа";
            default:
                return "$count часов";
        }
    }

    /**
     * Функция для склонения слова "минута" в зависимости от числа.
     *
     * @param int $count Число, для которого нужно склонить слово.
     * @return string Склоненное слово "минута" в зависимости от числа.
     */
    public static function declensionMinutes(int $count): string {
        $number = abs($count) % 100; // Берем абсолютное значение и последние две цифры
        $lastDigit = $number % 10; // Последняя цифра

        if ($number > 10 && $number < 20) {
            // Если число от 11 до 19 включительно
            return "$count минут";
        }

        switch ($lastDigit) {
            case 1:
                return "$count минута";
            case 2:
            case 3:
            case 4:
                return "$count минуты";
            default:
                return "$count минут";
        }
    }


}
