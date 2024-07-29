<?php

namespace App\Http\directions\borderCrossings\cameras\Entities;

use App\Http\directions\borderCrossings\Entities\BorderCrossing;
use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    // Указываем, что таблица называется 'cameras'
    protected $table = 'cameras';

    // Указываем, какие атрибуты можно массово заполнять
    protected $fillable = [
        'border_crossing_id',
        'url',
        'description'
    ];

    // Если не используете timestamps, укажите это
    public $timestamps = false; // Устанавливаем в false, так как в миграции нет полей created_at и updated_at

    // Определение отношений с другими моделями

    public function borderCrossing()
    {
        return $this->belongsTo(BorderCrossing::class, 'border_crossing_id');
    }
}
