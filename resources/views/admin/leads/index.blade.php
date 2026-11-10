@extends('layouts.admin')

@section('title', 'Leads & AI Intake')
@section('page_title', 'Leads & AI intake')
@section('page_subtitle', 'Enquiries classified, tagged and routed in real time')

@section('content')
<div class="card" style="margin-bottom: 16px;">
    <form method="GET" action="{{ route('admin.leads.index') }}">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 120px; gap: 10px; align-items: flex-end;">
            <div class="form-group" style="margin: 0;">
                <label>Search Keyword</label>
                <input type="text" name="search" class="form-control" placeholder="Name, email, company..." value="{{ request('search') }}">
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Source</label>
                <select name="source" class="form-control">
                    <option value="">All Sources</option>
                    <option value="website" {{ request('source') == 'website' ? 'selected' : '' }}>Website Form</option>
                    <option value="social" {{ request('source') == 'social' ? 'selected' : '' }}>Social Media Landing</option>
                </select>
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Service</label>
                <select name="service_id" class="form-control">
                    <option value="">All Services</option>
                    @foreach($services as $srv)
                        <option value="{{ $srv->id }}" {{ request('service_id') == $srv->id ? 'selected' : '' }}>{{ $srv->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 6px;">
                <button type="submit" class="btn" style="height: 38px; width: 100%;">Filter</button>
            </div>
        </div>
    </form>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
    <div>
        <strong>{{ $leads->total() }}</strong> enquiries found
    </div>
    <div style="display:flex; gap: 8px;">
        <a href="{{ route('admin.leads.export', request()->query()) }}" class="btn s sm">
            <span>📥</span> Export Filtered CSV
        </a>
    </div>
</div>

<div class="card tb">
    <table>
        <thead>
            <tr>
                <th>Lead ID</th>
                <th>Client Name</th>
                <th>Email &amp; Phone</th>
                <th>Company</th>
                <th>Source</th>
                <th>Selected Services</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leads as $lead)
                <tr class="cl" onclick="window.location='{{ route('admin.leads.show', $lead->id) }}'">
                    <td><strong>#{{ $lead->id }}</strong></td>
                    <td><b>{{ $lead->full_name }}</b></td>
                    <td>
                        <div style="font-size:12px;">{{ $lead->email }}</div>
                        <div style="color:var(--mu); font-size:11px;">{{ $lead->phone }}</div>
                    </td>
                    <td>{{ $lead->company_name ?: 'Personal / N/A' }}</td>
                    <td>
                        <span class="tag {{ $lead->source === 'social' ? 'wn' : '' }}">{{ ucfirst($lead->source) }}</span>
                    </td>
                    <td>
                        @foreach($lead->services as $srv)
                            <span class="tag mu" style="margin-bottom:2px;">{{ $srv->name }}</span>
                        @endforeach
                    </td>
                    <td onclick="event.stopPropagation()">
                        <form method="POST" action="{{ route('admin.leads.status', $lead->id) }}" style="margin: 0;">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="form-control" style="padding: 4px 8px; font-size: 12px; width: auto; font-weight: 600;">
                                <option value="new" {{ $lead->status == 'new' ? 'selected' : '' }}>New</option>
                                <option value="contacted" {{ $lead->status == 'contacted' ? 'selected' : '' }}>Contacted</option>
                                <option value="in_progress" {{ $lead->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="converted" {{ $lead->status == 'converted' ? 'selected' : '' }}>Converted</option>
                                <option value="closed" {{ $lead->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </form>
                    </td>
                    <td style="color:var(--mu); font-size:12px;">{{ $lead->created_at->format('d M Y') }}</td>
                    <td onclick="event.stopPropagation()">
                        <a href="{{ route('admin.leads.show', $lead->id) }}" class="btn s sm">View Details</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding: 30px; color:var(--mu)">
                        No leads matched your criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-wrap">
    {{ $leads->links() }}
</div>
@endsection
