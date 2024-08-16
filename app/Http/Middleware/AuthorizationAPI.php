<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;


class AuthorizationAPI
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader("Authorization"))
            return response()->json(['message' => 'Authentication header is missing'], Response::HTTP_UNAUTHORIZED);

        $authHeader = explode(" ", $request->header('Authorization'));

        if (count($authHeader) != 2 || $authHeader[0] != "tma")
            return response()->json(['message' => 'Invalid authorization header'], Response::HTTP_UNAUTHORIZED);

        if (!$this->checkSecurityTGBot($authHeader[1]))
            return response()->json(['message' => 'Доступ к API запрещен'], Response::HTTP_UNAUTHORIZED);


        return $next($request);
    }

    public function checkSecurityTGBot(string $authHeader)
    {
        // Убираем начальные и конечные пробелы
        $rawData = trim($authHeader);
        error_log("Raw Data: " . $rawData);

        // Парсим данные
        $parsedData = $this->parseFormData($rawData);

        if ($parsedData == null) {
            error_log("Parsed Data is null.");
            return false;
        }

        error_log("Parsed Data: " . print_r($parsedData, true));

        // Извлекаем hash и удаляем его из массива
        $hashValue = $parsedData["hash"] ?? null;
        unset($parsedData["hash"]);

        // Если нет хэша - возвращаем false
        if (!$hashValue) {
            error_log("Hash value is missing.");
            return false;
        }

        error_log("Hash Value: " . $hashValue);

        // Преобразование в формат {key}={value} и сортировка по ключам
        $stringArray = [];
        foreach ($parsedData as $key => $value) {
            $stringArray[] = "$key=$value";
        }
        sort($stringArray);  // сортируем по ключам

        error_log("String Array (sorted): " . print_r($stringArray, true));

        // Соединяем все элементы через \n
        $concatenatedData = implode("\n", $stringArray);

        error_log("Concatenated Data: " . $concatenatedData);

        // Получение секретного ключа
        $secretKey = $this->hmac_sha256("WebAppData", config('app.telegram_bot_token'));

        error_log(config('app.telegram_bot_token'));

        // Получение конечного результата
        $res = strtolower(bin2hex($this->hmac_sha256($secretKey, $concatenatedData)));

        error_log("Result (hex): " . $res);

        // Сравниваем результат с hash
        $isValid = $res === $hashValue;
        error_log("Is Valid: " . ($isValid ? "true" : "false"));

        return $isValid;
    }

// Функция для HMAC SHA256
    public function hmac_sha256($key, $data)
    {
        return hash_hmac('sha256', $data, $key, true);
    }

    private function parseFormData(string $rawData)
    {
        $result = [];
        $pairs = explode('&', $rawData);

        foreach ($pairs as $pair) {
            list($key, $value) = explode('=', $pair);
            $result[$key] = urldecode($value);
        }

        error_log("Parsed Form Data: " . print_r($result, true));

        return $result;
    }

}
