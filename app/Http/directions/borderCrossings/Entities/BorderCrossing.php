<?php

namespace App\Http\directions\borderCrossings\Entities;

use App\Http\directions\Entities\Direction;
use Illuminate\Database\Eloquent\Model;

class BorderCrossing extends Model
{
    // Указываем, что таблица называется 'border_crossings'
    protected $table = 'borderсrossings';

    // Указываем, какие атрибуты можно массово заполнять
    protected $fillable = [
        'direction_id',
        'from',
        'to',
        'is_queue'
    ];

    // Если не используете timestamps, укажите это
    public $timestamps = false; // Устанавливаем в false, так как в миграции нет полей created_at и updated_at

    // Определение отношений с другими моделями

    public function direction()
    {
        return $this->belongsTo(Direction::class, 'direction_id');
    }

    public function fromCity()
    {
        return $this->belongsTo(City::class, 'from');
    }

    public function toCity()
    {
        return $this->belongsTo(City::class, 'to');
    }
}
