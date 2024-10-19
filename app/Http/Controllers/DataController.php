<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HeadHunterApi;

class DataController extends Controller
{
    protected $HeadHunterApi;

    public function __construct(HeadHunterApi $HeadHunterApi)
    {
        $this->HeadHunterApi = $HeadHunterApi;
    }
    public function index(Request $request)
    {
        $page = $request->input('page', 0);
        $perPage = $request->input('per_page', 20);
        $region = $request->input('country', 1); // Изменим 'region' на 'country'
        $city = $request->input('city'); // Получаем город, если выбран
        $text = $request->input('text', 'php'); // Строка поиска

        // Добавляем фильтр по городу, если он есть
        $vacancies = $this->HeadHunterApi->getVacancies($text, $region, $page, $perPage, $city);

        // Если фильтр по городу, сортируем вакансии по полю города
        if ($city) {
            $vacancies['items'] = array_filter($vacancies['items'], function ($vacancy) use ($city) {
                return isset($vacancy['area']['id']) && $vacancy['area']['id'] == $city;
            });
        }

        return response()->json($vacancies);
    }





    // public function getData()
    // {
    //     $hhApi = new HeadHunterApi();
    //     $vacancies = $hhApi->getVacancies();
    //     return response()->json($vacancies);
    // }


    // public function getVacancies(Request $request)
    // {
    //     $hhApi = new HeadHunterApi();
    //     $area = $request->input('area', 1); // По умолчанию Москва
    //     $vacancies = $hhApi->getVacancies('php', $area);
    //     return response()->json($vacancies);
    // }
}
