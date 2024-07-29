<?php

namespace App\Http\directions\borderCrossings\Entities;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    // Указываем, что таблица называется 'countries'
    protected $table = 'countries';

    // Указываем, какие атрибуты можно массово заполнять
    protected $fillable = [
        'name',
        'logo'
    ];

    // Если не используете timestamps, укажите это
    public $timestamps = false; // Устанавливаем в false, так как в миграции нет полей created_at и updated_at
}
