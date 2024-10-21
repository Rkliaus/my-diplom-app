<?php
namespace App\Http\Controllers;

use App\Models\HeadHunterData;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $vacancies = HeadHunterData::all();
        return response()->json($vacancies);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'company' => 'required|string',
            'salary_min' => 'required|numeric|min:0',
            'salary_max' => 'required|numeric|gte:salary_min',

            'city' => 'required|string',
            'published_at' => 'required|date',
        ]);

        $vacancy = HeadHunterData::create([
            'title' => $request->title,
            'company' => $request->company,
            'salary' => $request->salary_min . ' - ' . $request->salary_max,

            'city' => $request->city,
            'published_at' => $request->published_at,
        ]);

        return redirect()->route('vacancies')->with('success', 'Vacancy created successfully');
    }

    public function show($id)
    {
        $vacancy = HeadHunterData::findOrFail($id);
        return response()->json($vacancy);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'company' => 'required|string',
            'salary_min' => 'required|numeric|min:0',
            'salary_max' => 'required|numeric|gte:salary_min',

            'city' => 'required|string',
            'published_at' => 'required|date',
        ]);

        $vacancy = HeadHunterData::findOrFail($id);
        $vacancy->update($request->all());

        return response()->json($vacancy);
    }

    public function destroy($id)
    {
        $vacancy = HeadHunterData::findOrFail($id);
        $vacancy->delete();

        return response()->json(null, 204);
    }
}
