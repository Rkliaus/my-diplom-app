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
        $region = $request->input('region', 1); // Получаем ID региона из запроса, по умолчанию 1
        $sortBy = $request->input('sort_by', 'name'); // Получаем опцию сортировки, по умолчанию по имени
        $vacancies = $this->HeadHunterApi->getVacancies('php', $region, $page, $perPage); // Запрос вакансий с учетом региона
         // Проверяем опцию сортировки
         if ($sortBy === 'region') {
            // Если сортировка по региону, сортируем вакансии по полю area.name
            usort($vacancies['items'], function ($a, $b) {
                return strcmp($a['area']['name'], $b['area']['name']);
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
