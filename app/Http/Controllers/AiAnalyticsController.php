<?php

namespace App\Http\Controllers;

use App\Services\FarmAnalyticsService;
use Illuminate\Http\Request;

class AiAnalyticsController extends Controller
{
    public function index()
    {
        return view('ai-analytics.index');
    }

    public function analyze(Request $request, FarmAnalyticsService $analytics)
    {
        $recommendations = $analytics->getRecommendations();

        if ($request->wantsJson()) {
            return response()->json(['recommendations' => $recommendations]);
        }

        return view('ai-analytics.index', [
            'recommendations' => $recommendations,
            'analyzed_at' => now(),
        ]);
    }
}
