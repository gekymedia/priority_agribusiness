<?php

namespace App\Http\Controllers;

use App\Models\Planting;
use App\Models\Field;
use Illuminate\Http\Request;

class PlantingController extends Controller
{
    public function index()
    {
        $plantings = Planting::with('field')->paginate(15);
        return view('plantings.index', compact('plantings'));
    }

    public function create()
    {
        $fields = Field::all();
        return view('plantings.create', compact('fields'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'crop_name' => 'required|string|max:255',
            'planting_date' => 'required|date',
            'expected_harvest_date' => 'nullable|date',
            'seed_source' => 'nullable|string|max:255',
            'quantity_planted' => 'nullable|string|max:255',
            'status' => 'required|string',
        ]);
        Planting::create($data);
        return redirect()->route('plantings.index')->with('success', 'Planting created successfully.');
    }

    public function show(Planting $planting)
    {
        return view('plantings.show', compact('planting'));
    }

    public function edit(Planting $planting)
    {
        $fields = Field::all();
        return view('plantings.edit', compact('planting', 'fields'));
    }

    public function update(Request $request, Planting $planting)
    {
        $data = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'crop_name' => 'required|string|max:255',
            'planting_date' => 'required|date',
            'expected_harvest_date' => 'nullable|date',
            'seed_source' => 'nullable|string|max:255',
            'quantity_planted' => 'nullable|string|max:255',
            'status' => 'required|string',
        ]);
        $planting->update($data);
        return redirect()->route('plantings.index')->with('success', 'Planting updated successfully.');
    }

    public function destroy(Planting $planting)
    {
        $planting->delete();
        return redirect()->route('plantings.index')->with('success', 'Planting deleted successfully.');
    }
}