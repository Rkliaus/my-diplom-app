<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HeadHunterApi;
use App\Models\HeadHunterData;


    class DataController extends Controller
    {
        protected $HeadHunterApi;

        public function __construct(HeadHunterApi $HeadHunterApi)
        {
            $this->HeadHunterApi = $HeadHunterApi;
        }

        public function index(Request $request)
        {
            $page = 0;  // Начнем с первой страницы
            $perPage = $request->input('per_page', 20);
            $region = $request->input('country', 1);
            $city = $request->input('city');
            $text = $request->input('text', 'php');

            // Переменная для хранения общего числа записей
            $totalVacancies = 0;

            // Цикл для перебора всех страниц
            do {
                $vacancies = $this->HeadHunterApi->getVacancies($text, $region, $page, $perPage, $city);

                // Если есть фильтр по городу, применяем его
                if ($city) {
                    $vacancies['items'] = array_filter($vacancies['items'], function ($vacancy) use ($city) {
                        return isset($vacancy['area']['id']) && $vacancy['area']['id'] == $city;
                    });
                }

                // Сохраняем вакансии в базу данных через модель HeadHunterData
                foreach ($vacancies['items'] as $vacancyData) {
                    HeadHunterData::storeVacancy($vacancyData);
                }

                // Увеличиваем счётчик страниц
                $page++;

                // Считаем общее количество обработанных вакансий
                $totalVacancies += count($vacancies['items']);

                // Цикл продолжается, пока API возвращает данные
            } while (count($vacancies['items']) > 0); // Прерываем цикл, если вакансий больше нет

            return response()->json([
                'message' => "All vacancies have been saved.",
                'total_saved' => $totalVacancies,
            ]);
        }
    }


    /*
    // Если эти методы не используются, их можно оставить как комментарии или удалить:

    public function getData()
    {
        $hhApi = new HeadHunterApi();
        $vacancies = $hhApi->getVacancies();
        return response()->json($vacancies);
    }

    public function getVacancies(Request $request)
    {
        $hhApi = new HeadHunterApi();
        $area = $request->input('area', 1); // По умолчанию Москва
        $vacancies = $hhApi->getVacancies('php', $area);
        return response()->json($vacancies);
    }
    */
