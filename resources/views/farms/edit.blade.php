@extends('layouts.app')

@section('title', 'Edit Farm')

@section('content')
<h2 class="mb-4">Edit Farm</h2>
<form method="POST" action="{{ route('farms.update', $farm) }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" value="{{ old('name', $farm->name) }}" class="form-control" required>
        @error('name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" value="{{ old('location', $farm->location) }}" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control">{{ old('description', $farm->description) }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Farm Type</label>
        <select name="farm_type" class="form-select" required>
            <option value="poultry" {{ (old('farm_type', $farm->farm_type) == 'poultry') ? 'selected' : '' }}>Poultry</option>
            <option value="crop" {{ (old('farm_type', $farm->farm_type) == 'crop') ? 'selected' : '' }}>Crop</option>
            <option value="mixed" {{ (old('farm_type', $farm->farm_type) == 'mixed') ? 'selected' : '' }}>Mixed</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ route('farms.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection