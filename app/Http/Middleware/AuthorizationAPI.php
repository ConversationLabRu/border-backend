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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
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

    private function checkSecurityTGBot(String $authHeader)
    {
        $rawData = trim($authHeader[1]);

        $parseData = $this->parseFormData($rawData);

        if ($parseData == null) return false;

        $hashValue = $parseData["hash"];
        unset($parseData["hash"]);

        $stringArray = [];
        foreach ($parseData as $key => $value) {
            $stringArray[] = "$key=$value";
        }
        sort($stringArray);

        // Объединение их в одну строку
        $concatenatedData = implode("\n", $stringArray);

        // Получение секретного ключа
        $secretKey = hmac_sha256("WebAppData", env("TELEGRAM_BOT_TOKEN"));

        // Получение конечного результата
        $res = strtolower(bin2hex(hmac_sha256($secretKey, $concatenatedData)));

        return $res == $hashValue;
    }


    // Функция для HMAC SHA256
    private function hmac_sha256($key, $data) {
        return hash_hmac('sha256', $data, $key, true);
    }

    private function parseFormData(String $rawData)
    {
        try {
            $result = [];
            $pairs = explode('&', $rawData);

            foreach ($pairs as $pair) {
                list($key, $value) = explode('=', $pair);
                $result[$key] = urldecode($value);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
