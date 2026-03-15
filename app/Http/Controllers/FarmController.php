<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    /**
     * Display a listing of the farms.
     */
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'name');
        $direction = strtolower($request->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['name', 'location', 'farm_type'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }
        $farms = Farm::query()->orderBy($sort, $direction)->paginate(50)->withQueryString();
        return view('farms.index', compact('farms', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new farm.
     */
    public function create()
    {
        return view('farms.create');
    }

    /**
     * Store a newly created farm in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'farm_type' => 'required|string|in:poultry,crop,mixed',
        ]);

        Farm::create($data);

        return redirect()->route('farms.index')->with('success', 'Farm created successfully.');
    }

    /**
     * Display the specified farm.
     */
    public function show(Farm $farm)
    {
        return view('farms.show', compact('farm'));
    }

    /**
     * Show the form for editing the specified farm.
     */
    public function edit(Farm $farm)
    {
        return view('farms.edit', compact('farm'));
    }

    /**
     * Update the specified farm in storage.
     */
    public function update(Request $request, Farm $farm)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'farm_type' => 'required|string|in:poultry,crop,mixed',
        ]);

        $farm->update($data);

        return redirect()->route('farms.index')->with('success', 'Farm updated successfully.');
    }

    /**
     * Remove the specified farm from storage.
     */
    public function destroy(Farm $farm)
    {
        $farm->delete();
        return redirect()->route('farms.index')->with('success', 'Farm deleted successfully.');
    }
}