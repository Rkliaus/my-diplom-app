<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Подключаем библиотеку для работы с датами

class HeadHunterData extends Model
{
    protected $fillable = [
        'vacancy_id',
        'name',
        'area_id',
        'area_name',
        'salary_from',
        'salary_to',
        'currency',
        'employer_name',
        'published_at'
    ];

    public static function storeVacancy($vacancyData)
    {
        // Преобразуем дату публикации в формат для MySQL
        $publishedAt = isset($vacancyData['published_at'])
            ? Carbon::parse($vacancyData['published_at'])->format('Y-m-d H:i:s')
            : null;

        return self::updateOrCreate(
            ['vacancy_id' => $vacancyData['id']], // уникальный идентификатор вакансии
            [
                'name' => $vacancyData['name'],
                'area_id' => $vacancyData['area']['id'],
                'area_name' => $vacancyData['area']['name'],
                'salary_from' => $vacancyData['salary']['from'] ?? null,
                'salary_to' => $vacancyData['salary']['to'] ?? null,
                'currency' => $vacancyData['salary']['currency'] ?? null,
                'employer_name' => $vacancyData['employer']['name'] ?? null,
                'published_at' => $publishedAt,
            ]
        );
    }
}
