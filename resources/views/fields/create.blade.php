@extends('layouts.app')

@section('title', 'Add Field')

@section('content')
<h2 class="mb-4">Add Field/Plot</h2>
<form method="POST" action="{{ route('fields.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Farm</label>
        <select name="farm_id" class="form-select" required>
            @foreach($farms as $farm)
                <option value="{{ $farm->id }}">{{ $farm->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Size (acres/hectares)</label>
        <input type="number" step="0.01" name="size" value="{{ old('size') }}" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Soil Type</label>
        <input type="text" name="soil_type" value="{{ old('soil_type') }}" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('fields.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection