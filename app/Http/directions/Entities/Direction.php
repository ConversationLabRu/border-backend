<?php

namespace App\Http\directions\Entities;

use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
    // Указываем, что таблица называется 'directions'
    protected $table = 'directions';

    // Указываем, какие атрибуты можно массово заполнять
    protected $fillable = [
        'name',
        'logo',
        'image',
        'info'
    ];

    // Если не используете timestamps, укажите это
    public $timestamps = false; // Устанавливаем в false, так как в миграции нет полей created_at и updated_at

    // Объявляем атрибут info как массив (для JSON поля)
    protected $casts = [
        'info' => 'array',
    ];
}
