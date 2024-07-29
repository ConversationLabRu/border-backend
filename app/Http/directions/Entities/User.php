<?php

namespace App\Http\directions\Entities;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    // Указываем, что таблица называется 'users'
    protected $table = 'users';

    // Указываем, какие атрибуты можно массово заполнять
    protected $fillable = [
        'username',
        'telegram_id',
    ];

    // Если не используете timestamps, укажите это
    public $timestamps = false; // Устанавливаем в false, так как в миграции нет полей created_at и updated_at

    // Если вы используете другие имена полей для временных меток, укажите их
    // protected $timestamps = ['created_at', 'updated_at'];

    // Вы можете определить отношения здесь, если требуется
}
