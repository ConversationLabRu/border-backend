<?php

namespace App\Utils;

use Illuminate\Http\Request;

class LogUtils
{
    public static function elasticLog(Request $request, string $message)
    {
//        $headerString = explode(" ", $request->header('Authorization'))[1];
//
//        // Парсинг строки запроса
//        parse_str($headerString, $params);
//
//        // Декодирование параметра user
//        $userData = json_decode(urldecode($params['user']), true);

        // Получение user id
//        $userId = $userData['id'];
//        $userName = $userData['username'];
//        $firstName = $userData['first_name'];
        $userId = "1";
        $userName = "2";
        $firstName = "3";

        $logger = app('log');
        $logger->info($message, [
            'tg_id' => $userId,
            'username' => $userName,
            'first_name' => $firstName
        ]);
    }
}
