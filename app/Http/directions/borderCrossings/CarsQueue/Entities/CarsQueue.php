<?php

namespace App\Http\directions\borderCrossings\CarsQueue\Entities;

use App\Http\directions\borderCrossings\Entities\BorderCrossing;
use App\Http\directions\borderCrossings\Entities\City;
use App\Http\directions\Entities\Direction;
use Illuminate\Database\Eloquent\Model;

class CarsQueue extends Model
{
    // Указываем, что таблица называется 'cars_queue'
    protected $table = 'cars_queue';

    // Указываем, какие атрибуты можно массово заполнять
    protected $fillable = [
        'border_crossing_id	',
        'create_report_timestamp',
        'count',
        'route_reverse'
    ];

    // Если не используете timestamps, укажите это
    public $timestamps = false; // Устанавливаем в false, так как в миграции нет полей created_at и updated_at

    // Определение отношений с другими моделями

    public function borderCrossing()
    {
        return $this->belongsTo(BorderCrossing::class, 'border_crossing_id	');
    }
}
