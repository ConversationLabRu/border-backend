<?php

namespace App\Http\directions\borderCrossings\reports\Entities;

use App\Http\directions\borderCrossings\Entities\BorderCrossing;
use App\Http\directions\Entities\User;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    // Указываем, что таблица называется 'reports'
    protected $table = 'reports';

    // Указываем, какие атрибуты можно массово заполнять
    protected $fillable = [
        'border_crossing_id',
        'transport_id',
        'user_id',
        'checkpoint_queue',
        'checkpoint_entry',
        'checkpoint_exit',
        'comment'
    ];

    // Если не используете timestamps, укажите это
    public $timestamps = false; // Устанавливаем в false, если нет полей created_at и updated_at

    // Определение отношений с другими моделями

    public function borderCrossing()
    {
        return $this->belongsTo(BorderCrossing::class, 'border_crossing_id');
    }

    public function transport()
    {
        return $this->belongsTo(Transport::class, 'transport_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
