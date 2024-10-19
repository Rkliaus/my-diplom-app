<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $vacancies = auth()->user()->vacancies()->get();
        return response()->json($vacancies);
    }

    public function store(Request $request)
    {
        $vacancy = $request->user()->vacancies()->create($request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'employer_name' => 'required|string|max:255',
        ]));

        return response()->json($vacancy, 201);
    }

    public function show(Vacancy $vacancy)
    {
        $this->authorize('view', $vacancy);
        return response()->json($vacancy);
    }

    public function update(Request $request, Vacancy $vacancy)
    {
        $this->authorize('update', $vacancy);
        $vacancy->update($request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'employer_name' => 'required|string|max:255',
        ]));

        return response()->json($vacancy);
    }

    public function destroy(Vacancy $vacancy)
    {
        $this->authorize('delete', $vacancy);
        $vacancy->delete();
        return response()->json(null, 204);
    }
}
