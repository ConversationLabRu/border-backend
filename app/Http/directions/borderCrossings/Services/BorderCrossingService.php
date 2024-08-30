<?php

namespace App\Http\directions\borderCrossings\Services;

use App\Http\directions\borderCrossings\Dto\CacheDTO;
use App\Http\directions\borderCrossings\Dto\CityDTO;
use App\Http\directions\borderCrossings\Dto\CountryDTO;
use App\Http\directions\borderCrossings\Dto\DirectionCrossingDTO;
use App\Http\directions\borderCrossings\Entities\BorderCrossing;
use App\Utils\LogUtils;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Response;
use Ramsey\Uuid\Type\Integer;

class BorderCrossingService
{
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
                $direction->is_car
            );

            if ($directionDTO->getFromCity()->getCountry()->getName() == "Беларусь" || $directionDTO->getToCity()->getCountry()->getName() == "Беларусь") {

                $response = $this->fetchDataFromExternalApi($directionDTO);

                if ($response !== null) $directionDTO->setCache($response->toArray());
            }


//            return $data; // Return as array
            return $directionDTO->toArray(); // Return as array
        });


        return $result;
    }

    function fetchDataFromExternalApi(DirectionCrossingDTO $directionDTO) : ?CacheDTO
    {

        if ($directionDTO->getFromCity()->getName() == "Каменный Лог" || $directionDTO->getToCity()->getName() == "Каменный Лог") {
            $data = Cache::get("kameni_log");

            if ($data == null) {
                $response = Http::get("https://belarusborder.by/info/monitoring-new?token=test&checkpointId=b60677d4-8a00-4f93-a781-e129e1692a03");
                $data = $this->convertToCacheObjectBelarusInfo($response);
                Cache::put('kameni_log', $data, now()->addMinutes(1));
            }

            return $data;
        } elseif ($directionDTO->getFromCity()->getName() == "Брест" || $directionDTO->getToCity()->getName() == "Брест") {
            $data = Cache::get("brest");

            if ($data == null) {
                $response = Http::get("https://belarusborder.by/info/monitoring-new?token=test&checkpointId=a9173a85-3fc0-424c-84f0-defa632481e4");
                $data = $this->convertToCacheObjectBelarusInfo($response);
                Cache::put('brest', $data, now()->addMinutes(5));
            }

            return $data;
        }

        $data = Cache::get("benyakoni");

        if ($data == null) {
            $response = Http::get("https://belarusborder.by/info/monitoring-new?token=test&checkpointId=53d94097-2b34-11ec-8467-ac1f6bf889c0");
            $data = $this->convertToCacheObjectBelarusInfo($response);
            Cache::put('benyakoni', $data, now()->addMinutes(5));

        }

        return $data;
    }

    function convertToCacheObjectBelarusInfo(\Illuminate\Http\Client\Response $response) : ?CacheDTO
    {
        try {
            if (sizeof($response['carLiveQueue']) == 0) {
                Log::info("Информация о машинах нет");


                $cacheDTO = new CacheDTO(
                    "0",
                    0
                );
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


                $cacheDTO = new CacheDTO(
                    "0",
                    0
                );
                return $cacheDTO;
            }
        } catch (\Exception $e) {
            $cacheDTO = new CacheDTO(
                "0",
                0
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
