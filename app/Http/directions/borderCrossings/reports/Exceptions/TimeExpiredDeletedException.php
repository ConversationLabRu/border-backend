<?php

namespace App\Http\directions\borderCrossings\reports\Exceptions;

use Exception;

class TimeExpiredDeletedException extends Exception
{
    // Дополнительные свойства (если необходимо)
    protected $customMessage;

    // Конструктор
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        // Вызов конструктора родительского класса
        parent::__construct($message, $code, $previous);

        // Установка дополнительного сообщения (если необходимо)
        $this->customMessage = $message;
    }

    // Дополнительный метод для получения пользовательского сообщения
    public function getCustomMessage()
    {
        return $this->customMessage;
    }
}
