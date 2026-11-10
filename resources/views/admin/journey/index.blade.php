@extends('layouts.admin')

@section('title', 'Client Journey Overview')
@section('page_title', 'Client journeys')
@section('page_subtitle', 'Select a client to manage their 12 connected journey stages')

@section('content')
<!-- Filter bar -->
<div class="card" style="margin-bottom: 16px;">
    <form method="GET" action="{{ route('admin.journey.index') }}">
        <div style="display: grid; grid-template-columns: 1fr 180px 100px; gap: 10px; align-items: flex-end;">
            <div class="form-group" style="margin: 0;">
                <label>Search Client / Business</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, company..." value="{{ request('search') }}">
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
            <button type="submit" class="btn" style="height: 38px;">Filter</button>
        </div>
    </form>
</div>

<!-- All Clients Progression Table (Requirement 2: User List First) -->
<div class="card tb">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 14px;">
        <h3 style="margin: 0;">Client Accounts List <em>{{ $leads->total() }} cases in onboarding lifecycle</em></h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>Lead ID</th>
                <th>Client Name</th>
                <th>Company / Trading</th>
                <th>Requested Services</th>
                <th>Current Active Stage</th>
                <th style="width: 200px;">Progress %</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leads as $lead)
                @php
                    $completedSteps = $lead->journeySteps->where('status', 'done')->count();
                    $totalSteps = $lead->journeySteps->count() ?: 12;
                    $percent = (int) round(($completedSteps / $totalSteps) * 100);
                    $currentStep = $lead->journeySteps->firstWhere('status', 'pending') ?: $lead->journeySteps->last();
                @endphp
                <tr class="cl" onclick="window.location='{{ route('admin.journey.show', $lead->id) }}'">
                    <td><strong>#{{ $lead->id }}</strong></td>
                    <td>
                        <strong style="color:var(--tx);">{{ $lead->full_name }}</strong>
                        <div style="font-size:11.5px; color:var(--mu);">{{ $lead->email }}</div>
                    </td>
                    <td>{{ $lead->company_name ?: 'Sole Trader / Personal' }}</td>
                    <td>
                        @foreach($lead->services->take(2) as $s)
                            <span class="tag mu">{{ $s->name }}</span>
                        @endforeach
                        @if($lead->services->count() > 2)
                            <span class="tag mu">+{{ $lead->services->count() - 2 }}</span>
                        @endif
                    </td>
                    <td>
                        @if($currentStep)
                            <strong style="font-size:13px; color:var(--ac);">Stage {{ $currentStep->step_order }}: {{ $currentStep->title }}</strong>
                            <div style="font-size:11px; color:var(--mu);">
                                Review: <span class="tag {{ $currentStep->isSubmitted() ? 'wn' : ($currentStep->isRevisionRequired() ? 'er' : 'mu') }}" style="font-size:10px; padding:1px 6px;">
                                    {{ ucfirst(str_replace('_', ' ', $currentStep->review_status)) }}
                                </span>
                            </div>
                        @else
                            <span class="tag ok">Completed</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div class="bar" style="flex:1;">
                                <i style="width: {{ $percent }}%; background: {{ $percent == 100 ? 'var(--ok)' : 'linear-gradient(90deg,var(--ac),var(--ac2))' }};"></i>
                            </div>
                            <span style="font-size:12px; font-weight:700; width:34px; text-align:right;">{{ $percent }}%</span>
                        </div>
                    </td>
                    <td>
                        <span class="tag {{ $lead->status === 'converted' ? 'ok' : ($lead->status === 'in_progress' ? 'wn' : '') }}">
                            {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                        </span>
                    </td>
                    <td onclick="event.stopPropagation()">
                        <a href="{{ route('admin.journey.show', $lead->id) }}" class="btn s sm" style="font-weight:600;">
                            Manage 12 Stages &rarr;
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding: 30px; color:var(--mu)">
                        No client journeys found.
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
