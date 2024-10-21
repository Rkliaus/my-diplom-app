<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HeadHunterData;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class HeadHunterApiController extends Controller
{
    public function fetchData(Request $request)
    {
        // Базовый URL для HeadHunter API
        $apiUrl = 'https://api.hh.ru/vacancies';

        $page = 0; // Начальная страница
        $perPage = 20; // Количество вакансий на одной странице
        $totalPages = 1; // Изначально предполагаем 1 страницу
        $defaultRegion = 16;  // ID региона по умолчанию
        // Опциональные параметры: text, area (страна, регион), и другие
        $params = [
            'text' => $request->input('text') ?? null, // Можно добавить текст поиска, если требуется
            'area' => $request->input('area') ??  $defaultRegion, // Можно указать регион или страну, если требуется
            'per_page' => $perPage,
        ];

        // Итерация по страницам API, пока есть данные
        while ($page < $totalPages) {
            $params['page'] = $page; // Устанавливаем текущую страницу

            // Отправляем GET-запрос к API
            $response = Http::get($apiUrl, $params);

            if ($response->successful()) {
                $data = $response->json();
                $vacancies = $data['items']; // Получаем вакансии из ответа
                $totalPages = $data['pages']; // Общее количество страниц

                foreach ($vacancies as $vacancy) {
                    // Проверяем, существует ли уже вакансия в базе по её id
                    $existingVacancy = HeadHunterData::where('hh_id', $vacancy['id'])->first();

                    if (!$existingVacancy) {
                        // Проверяем, указаны ли оба значения зарплаты и они не равны 'Не указана' и не содержат пробел
                        if (isset($vacancy['salary']['from'], $vacancy['salary']['to']) &&
                            $vacancy['salary']['from'] !== 'Не указана' &&
                            trim($vacancy['salary']['to']) !== '' &&
                            $vacancy['salary']['to'] !== 'Не указана') {

                            // Сохраняем вакансию в базу данных
                            HeadHunterData::create([
                                'hh_id' => $vacancy['id'],
                                'title' => $vacancy['name'],
                                'company' => $vacancy['employer']['name'],
                                'salary' => $vacancy['salary']['from'] . ' - ' . $vacancy['salary']['to'],
                                'city' => $vacancy['area']['name'], // Страна или регион

                                'published_at' => Carbon::parse($vacancy['published_at']),
                            ]);
                        }
                    }
                }

                $page++; // Переходим к следующей странице
            } else {
                return response()->json([
                    'message' => 'Ошибка при запросе данных с HeadHunter.',
                ], $response->status());
            }
        }

        return response()->json([
            'message' => 'Данные успешно загружены и сохранены.',
        ], 200);
    }

    public function getData(Request $request)
    {
        $vacancies = HeadHunterData::all(); // Получаем все вакансии из базы данных

        return response()->json($vacancies, 200); // Возвращаем данные в формате JSON
    }
}
