<?php

namespace App\Http\directions\borderCrossings\Entities;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    // Указываем, что таблица называется 'cities'
    protected $table = 'cities';

    // Указываем, какие атрибуты можно массово заполнять
    protected $fillable = [
        'country_id',
        'name'
    ];

    // Если не используете timestamps, укажите это
    public $timestamps = false; // Устанавливаем в false, так как в миграции нет полей created_at и updated_at

    // Определение отношений с другими моделями

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
