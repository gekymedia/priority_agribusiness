@extends('layouts.app')

@section('title', 'View Planting')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h2>Planting: {{ $planting->crop_name }}</h2>
    <div>
        <a href="{{ route('plantings.edit', $planting) }}" class="btn btn-warning">Edit</a>
        <form action="{{ route('plantings.destroy', $planting) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this planting?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>
<div class="card mb-3">
    <div class="card-body">
        <p><strong>Field:</strong> {{ $planting->field->name ?? '' }}</p>
        <p><strong>Farm:</strong> {{ $planting->field->farm->name ?? '' }}</p>
        <p><strong>Planting Date:</strong> {{ \Carbon\Carbon::parse($planting->planting_date)->format('Y-m-d') }}</p>
        <p><strong>Expected Harvest Date:</strong> {{ $planting->expected_harvest_date ? \Carbon\Carbon::parse($planting->expected_harvest_date)->format('Y-m-d') : '-' }}</p>
        <p><strong>Seed Source:</strong> {{ $planting->seed_source }}</p>
        <p><strong>Quantity Planted:</strong> {{ $planting->quantity_planted }}</p>
        <p><strong>Status:</strong> {{ ucfirst($planting->status) }}</p>
    </div>
</div>
<a href="{{ route('plantings.index') }}" class="btn btn-secondary">Back to list</a>
@endsection