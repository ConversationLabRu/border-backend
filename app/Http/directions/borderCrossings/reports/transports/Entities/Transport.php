<?php

namespace App\Http\directions\borderCrossings\reports\transports\Entities;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    // Указываем, что таблица называется 'transports'
    protected $table = 'transports';

    // Указываем, какие атрибуты можно массово заполнять
    protected $fillable = ['name', 'icon'];

    // Если не используете timestamps, укажите это
    public $timestamps = false; // Устанавливаем в false, так как в миграции нет временных меток
}
