<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Farm;
use Illuminate\Http\Request;

class HouseController extends Controller
{
    public function index()
    {
        $houses = House::with('farm')->paginate(15);
        return view('houses.index', compact('houses'));
    }

    public function create()
    {
        $farms = Farm::all();
        return view('houses.create', compact('farms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|integer',
            'type' => 'nullable|string|max:255',
        ]);
        House::create($data);
        return redirect()->route('houses.index')->with('success', 'House created successfully.');
    }

    public function show(House $house)
    {
        return view('houses.show', compact('house'));
    }

    public function edit(House $house)
    {
        $farms = Farm::all();
        return view('houses.edit', compact('house', 'farms'));
    }

    public function update(Request $request, House $house)
    {
        $data = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|integer',
            'type' => 'nullable|string|max:255',
        ]);
        $house->update($data);
        return redirect()->route('houses.index')->with('success', 'House updated successfully.');
    }

    public function destroy(House $house)
    {
        $house->delete();
        return redirect()->route('houses.index')->with('success', 'House deleted successfully.');
    }
}