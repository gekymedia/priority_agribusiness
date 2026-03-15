<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Farm;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'name');
        $direction = strtolower($request->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['name', 'farm', 'size', 'soil_type'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }

        $query = Field::query()->with('farm');
        if ($sort === 'farm') {
            $query->leftJoin('farms', 'fields.farm_id', '=', 'farms.id')
                ->select('fields.*')
                ->orderBy('farms.name', $direction);
        } else {
            $query->orderBy('fields.' . $sort, $direction);
        }
        $fields = $query->paginate(50)->withQueryString();
        return view('fields.index', compact('fields', 'sort', 'direction'));
    }

    public function create()
    {
        $farms = Farm::all();
        return view('fields.create', compact('farms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string|max:255',
            'size' => 'nullable|numeric',
            'soil_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        Field::create($data);
        return redirect()->route('fields.index')->with('success', 'Field created successfully.');
    }

    public function show(Field $field)
    {
        return view('fields.show', compact('field'));
    }

    public function edit(Field $field)
    {
        $farms = Farm::all();
        return view('fields.edit', compact('field', 'farms'));
    }

    public function update(Request $request, Field $field)
    {
        $data = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string|max:255',
            'size' => 'nullable|numeric',
            'soil_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        $field->update($data);
        return redirect()->route('fields.index')->with('success', 'Field updated successfully.');
    }

    public function destroy(Field $field)
    {
        $field->delete();
        return redirect()->route('fields.index')->with('success', 'Field deleted successfully.');
    }
}