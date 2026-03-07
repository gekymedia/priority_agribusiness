@extends('layouts.app')

@section('title', 'AI Analytics')

@section('content')
<div class="page-header">
    <h1 class="page-title">AI Analytics</h1>
    <p class="page-subtitle">Analyze your farm data and get AI-powered recommendations to improve operations</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <p class="text-muted mb-4">AI Analytics considers your farms, bird batches, egg production, egg sales, expenses, tasks, and plantings to suggest what to do better and what to watch out for.</p>

        <form action="{{ route('ai-analytics.analyze') }}" method="POST" class="mb-4">
            @csrf
            <button type="submit" class="btn btn-primary btn-lg" id="analyzeBtn">
                <i class="fas fa-brain me-2"></i>Analyze farm data & get recommendations
            </button>
        </form>

        @if(isset($recommendations))
            <div class="border-top pt-4 mt-4">
                @if(isset($analyzed_at))
                    <p class="small text-muted mb-2"><i class="fas fa-clock me-1"></i>Analyzed at {{ $analyzed_at->format('M d, Y H:i') }}</p>
                @endif
                <div class="ai-recommendations bg-light rounded-3 p-4" style="white-space: pre-wrap; font-family: inherit;">{{ $recommendations }}</div>
            </div>
        @endif
    </div>
</div>

@if(isset($recommendations) && str_contains($recommendations, '**'))
<script>
(function() {
    var el = document.querySelector('.ai-recommendations');
    if (!el) return;
    var text = el.textContent;
    var html = text.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
    el.innerHTML = html;
})();
</script>
@endif
@endsection
