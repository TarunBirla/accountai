@extends('layouts.admin')

@section('title', 'Add New Service')
@section('page_title', 'Create service')
@section('page_subtitle', 'Add a new Business or Personal accountancy offering')

@section('content')
<div style="margin-bottom: 14px;">
    <a href="{{ route('admin.services.index') }}" class="btn s sm">&larr; Back to Services</a>
</div>

<div class="card" style="max-width: 600px;">
    <form method="POST" action="{{ route('admin.services.store') }}">
        @csrf
        <div class="form-group">
            <label>Service Category / Type *</label>
            <select name="type" class="form-control" required>
                <option value="business" {{ old('type') == 'business' ? 'selected' : '' }}>Business Service (e.g. Accounts, Corporation Tax)</option>
                <option value="personal" {{ old('type') == 'personal' ? 'selected' : '' }}>Personal Service (e.g. Self Assessment, CGT)</option>
            </select>
        </div>

        <div class="form-group">
            <label>Service Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Forensic Accounting" required>
        </div>

        <div class="form-group">
            <label>Slug (URL Identifier - leave blank to auto-generate)</label>
            <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="e.g. forensic-accounting">
        </div>

        <div class="form-group">
            <label>Service Scope &amp; Description</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Detail the accounting workflow, client benefits, and deliverables...">{{ old('description') }}</textarea>
        </div>

        <div class="form-group" style="display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
            <label for="is_active" style="margin: 0; cursor:pointer;">Active on public website lead forms</label>
        </div>

        <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">Save New Service</button>
    </form>
</div>
@endsection
