@extends('layouts.admin')

@section('title', 'Services Catalogue')
@section('page_title', 'Services catalogue')
@section('page_subtitle', 'Manage Business and Personal accountancy service packages')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 14px;">
    <div><strong>{{ $services->total() }}</strong> total services configured</div>
    <a href="{{ route('admin.services.create') }}" class="btn sm">+ Add New Service</a>
</div>

<div class="card tb">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Service Name</th>
                <th>Slug</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $srv)
                <tr>
                    <td>#{{ $srv->id }}</td>
                    <td>
                        <span class="tag {{ $srv->type === 'business' ? '' : 'wn' }}">
                            {{ ucfirst($srv->type) }}
                        </span>
                    </td>
                    <td><strong>{{ $srv->name }}</strong></td>
                    <td><code>{{ $srv->slug }}</code></td>
                    <td style="max-width: 320px; font-size:12px; color:var(--mu);">{{ Str::limit($srv->description, 90) }}</td>
                    <td>
                        <span class="tag {{ $srv->is_active ? 'ok' : 'mu' }}">
                            {{ $srv->is_active ? 'Active' : 'Disabled' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <a href="{{ route('admin.services.edit', $srv->id) }}" class="btn s sm">Edit</a>
                            <form method="POST" action="{{ route('admin.services.destroy', $srv->id) }}" onsubmit="return confirm('Delete this service?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn s sm" style="color:var(--er)!important;">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding: 30px; color:var(--mu);">No services in catalogue. Run <code>php artisan db:seed</code> to populate.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-wrap">
    {{ $services->links() }}
</div>
@endsection
