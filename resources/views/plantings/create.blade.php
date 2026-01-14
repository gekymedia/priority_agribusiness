@extends('layouts.app')

@section('title', 'Add Planting')

@section('content')
<h2 class="mb-4">Add Planting</h2>
<form method="POST" action="{{ route('plantings.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Field</label>
        <select name="field_id" class="form-select" required>
            @foreach($fields as $field)
                <option value="{{ $field->id }}">{{ $field->name }} ({{ $field->farm->name ?? '' }})</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Crop Name</label>
        <input type="text" name="crop_name" value="{{ old('crop_name') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Planting Date</label>
        <input type="date" name="planting_date" value="{{ old('planting_date') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Expected Harvest Date</label>
        <input type="date" name="expected_harvest_date" value="{{ old('expected_harvest_date') }}" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Seed Source</label>
        <input type="text" name="seed_source" value="{{ old('seed_source') }}" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Quantity Planted</label>
        <input type="text" name="quantity_planted" value="{{ old('quantity_planted') }}" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            <option value="growing">Growing</option>
            <option value="harvested">Harvested</option>
            <option value="completed">Completed</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('plantings.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection