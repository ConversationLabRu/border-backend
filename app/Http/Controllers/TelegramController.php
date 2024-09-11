<?php

namespace App\Http\Controllers;

use App\Telegram\Handler;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    private Handler $handler;

    /**
     * @param Handler $handler
     */
    public function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }


    public function sendPhotoToChat(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->handler->sendPhoto($request);
    }
}
