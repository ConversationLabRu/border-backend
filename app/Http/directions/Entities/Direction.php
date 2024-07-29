<?php

namespace App\Http\directions\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Direction",
 *     type="object",
 *     required={"id", "name", "logo", "image", "info"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="logo", type="string"),
 *     @OA\Property(property="image", type="string"),
 *     @OA\Property(property="info", type="object", additionalProperties=true)
 * )
 */
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
