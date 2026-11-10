@extends('layouts.admin')

@section('title', 'Edit Service: ' . $service->name)
@section('page_title', 'Edit service')
@section('page_subtitle', $service->name)

@section('content')
<div style="margin-bottom: 14px;">
    <a href="{{ route('admin.services.index') }}" class="btn s sm">&larr; Back to Services</a>
</div>

<div class="card" style="max-width: 600px;">
    <form method="POST" action="{{ route('admin.services.update', $service->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Service Category / Type *</label>
            <select name="type" class="form-control" required>
                <option value="business" {{ old('type', $service->type) == 'business' ? 'selected' : '' }}>Business Service</option>
                <option value="personal" {{ old('type', $service->type) == 'personal' ? 'selected' : '' }}>Personal Service</option>
            </select>
        </div>

        <div class="form-group">
            <label>Service Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $service->name) }}" required>
        </div>

        <div class="form-group">
            <label>Slug (URL Identifier) *</label>
            <input type="text" name="slug" class="form-control" value="{{ old('slug', $service->slug) }}" required>
        </div>

        <div class="form-group">
            <label>Service Scope &amp; Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $service->description) }}</textarea>
        </div>

        <div class="form-group" style="display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
            <label for="is_active" style="margin: 0; cursor:pointer;">Active on public website lead forms</label>
        </div>

        <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">Update Service</button>
    </form>
</div>
@endsection
