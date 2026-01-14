<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;

class FarmApiController extends Controller
{
    /**
     * Get all farms for authenticated user
     */
    public function index(Request $request)
    {
        $farms = $request->user()->farms()
            ->with(['houses', 'fields'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $farms->map(function ($farm) {
                return [
                    'id' => $farm->id,
                    'name' => $farm->name,
                    'location' => $farm->location,
                    'farm_type' => $farm->farm_type,
                    'created_at' => $farm->created_at,
                    'houses_count' => $farm->houses->count(),
                    'fields_count' => $farm->fields->count(),
                    'houses' => $farm->houses->map(function ($house) {
                        return [
                            'id' => $house->id,
                            'name' => $house->name,
                            'capacity' => $house->capacity,
                            'type' => $house->type,
                        ];
                    }),
                    'fields' => $farm->fields->map(function ($field) {
                        return [
                            'id' => $field->id,
                            'name' => $field->name,
                            'size' => $field->size,
                            'soil_type' => $field->soil_type,
                        ];
                    }),
                ];
            })
        ]);
    }

    /**
     * Get specific farm details
     */
    public function show(Request $request, Farm $farm)
    {
        // Ensure user owns this farm
        if ($farm->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to farm'
            ], 403);
        }

        $farm->load(['houses', 'fields', 'birdBatches']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $farm->id,
                'name' => $farm->name,
                'location' => $farm->location,
                'farm_type' => $farm->farm_type,
                'created_at' => $farm->created_at,
                'houses' => $farm->houses->map(function ($house) {
                    return [
                        'id' => $house->id,
                        'name' => $house->name,
                        'capacity' => $house->capacity,
                        'type' => $house->type,
                    ];
                }),
                'fields' => $farm->fields->map(function ($field) {
                    return [
                        'id' => $field->id,
                        'name' => $field->name,
                        'size' => $field->size,
                        'soil_type' => $field->soil_type,
                    ];
                }),
                'bird_batches' => $farm->birdBatches->map(function ($batch) {
                    return [
                        'id' => $batch->id,
                        'batch_code' => $batch->batch_code,
                        'purpose' => $batch->purpose,
                        'quantity_arrived' => $batch->quantity_arrived,
                        'status' => $batch->status,
                    ];
                }),
                'summary' => [
                    'total_houses' => $farm->houses->count(),
                    'total_fields' => $farm->fields->count(),
                    'total_bird_batches' => $farm->birdBatches->count(),
                    'total_birds' => $farm->birdBatches->sum('quantity_arrived'),
                ]
            ]
        ]);
    }
}
