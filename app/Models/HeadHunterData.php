<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadHunterData extends Model
{
    use HasFactory;

    protected $fillable = [
        'hh_id',        // Уникальный идентификатор вакансии
        'title',        // Название вакансии
        'company',      // Название компании
        'salary',       // Диапазон зарплат

        'city',         // Город
        'published_at', // Дата публикации
    ];
}
