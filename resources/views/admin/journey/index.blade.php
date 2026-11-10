@extends('layouts.admin')

@section('title', 'Client Journey')
@section('page_title', 'Client journey')
@section('page_subtitle', ($selectedLead ? ($selectedLead->company_name ?: $selectedLead->full_name) : 'ABC Trading Ltd') . ' · 12 connected stages')

@push('styles')
<style>
.stages-flow {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 10px;
    margin-bottom: 22px;
}
.stage-card {
    flex: 1;
    min-width: 95px;
    background: var(--sf);
    border: 1px solid var(--bd);
    border-radius: 12px;
    padding: 12px 6px;
    text-align: center;
    text-decoration: none;
    color: var(--mu);
    transition: all .2s;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.stage-card:hover {
    border-color: var(--ac);
    transform: translateY(-1px);
}
.stage-card .badge-icon {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    margin-bottom: 6px;
    display: grid;
    place-items: center;
    font-size: 12px;
    font-weight: 700;
}
.stage-card.done {
    border-color: #0fb5a633;
    background: #ffffff;
}
.stage-card.done .badge-icon {
    background: #0fb5a6;
    color: #ffffff;
}
.stage-card.done .stage-title {
    color: #0fb5a6;
    font-weight: 600;
}
.stage-card.active {
    border: 2px solid var(--ac);
    box-shadow: 0 0 0 3px rgba(58,75,220,0.15);
    background: #ffffff;
}
.stage-card.active .badge-icon {
    background: var(--ac);
    color: #ffffff;
}
.stage-card.active .stage-title {
    color: var(--tx);
    font-weight: 700;
}
.stage-card.pending {
    background: var(--s2);
}
.stage-card.pending .badge-icon {
    background: var(--bd);
    color: var(--mu);
}
.stage-title {
    font-size: 11px;
    line-height: 1.25;
    text-align: center;
}
</style>
@endpush

@section('content')
@if($selectedLead)
    <!-- 12 Connected Stages Flow (Matches Exact Design) -->
    <div class="card" style="padding: 18px 20px; margin-bottom: 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
            <div>
                <strong style="font-size:16px;">{{ $selectedLead->company_name ?: $selectedLead->full_name }}</strong>
                <span class="tag {{ $selectedLead->status === 'converted' ? 'ok' : 'wn' }}" style="margin-left:8px;">
                    {{ ucfirst(str_replace('_', ' ', $selectedLead->status)) }}
                </span>
                <span style="font-size:12px; color:var(--mu); margin-left:8px;">Overall Progress: <b>{{ $selectedLead->progress_percentage }}%</b></span>
            </div>
            @if($leads->count() > 1)
                <div>
                    <form method="GET" action="{{ route('admin.journey.index') }}" style="display:flex; align-items:center; gap:8px;">
                        <label style="font-size:12px; color:var(--mu);">Switch Case:</label>
                        <select name="lead_id" onchange="this.form.submit()" class="form-control" style="width:auto; padding:4px 8px; font-size:12px;">
                            @foreach($leads as $l)
                                <option value="{{ $l->id }}" {{ $selectedLead->id == $l->id ? 'selected' : '' }}>
                                    #{{ $l->id }} · {{ $l->company_name ?: $l->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif
        </div>

        <div class="stages-flow">
            @php
                $steps = $selectedLead->journeySteps->sortBy('step_order');
                $firstPending = $steps->firstWhere('status', 'pending');
            @endphp

            @foreach($steps as $step)
                @php
                    $isDone = $step->status === 'done';
                    $isActive = $firstPending && $firstPending->id === $step->id;
                    $class = $isDone ? 'done' : ($isActive ? 'active' : 'pending');
                @endphp
                <div class="stage-card {{ $class }}">
                    <div class="badge-icon">
                        @if($isDone)
                            ✓
                        @else
                            {{ $step->step_order }}
                        @endif
                    </div>
                    <div class="stage-title">{{ $step->title }}</div>
                </div>
            @endforeach
        </div>

        <!-- Interactive Stage Action for Selected Case -->
        <div style="background:var(--s2); border:1px solid var(--bd); border-radius:12px; padding:16px; margin-top:10px;">
            <div style="font-size:13px; font-weight:600; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
                <span>Stage Actions &amp; Compliance Sign-offs</span>
                <a href="{{ route('admin.leads.show', $selectedLead->id) }}" class="btn sm s">Open Full Lead Dossier &rarr;</a>
            </div>
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:10px;">
                @foreach($steps as $step)
                    <div style="display:flex; justify-content:space-between; align-items:center; background:#fff; border:1px solid var(--bd); padding:8px 12px; border-radius:8px;">
                        <div>
                            <span style="font-size:11px; font-weight:700; color:var(--mu);">{{ $step->step_order }}.</span>
                            <strong style="font-size:12.5px;">{{ $step->title }}</strong>
                        </div>
                        <form method="POST" action="{{ route('admin.journey.steps.status', $step->id) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $step->status === 'done' ? 'pending' : 'done' }}">
                            <button type="submit" class="btn sm {{ $step->status === 'done' ? 'ok' : 's' }}" style="padding:3px 8px; font-size:11px;">
                                {{ $step->status === 'done' ? '✓ Done' : 'Complete' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- All Clients Progression Table -->
<div class="card tb">
    <h3>All Practice Clients <em>12 connected stages overview</em></h3>
    <table>
        <thead>
            <tr>
                <th>Lead ID</th>
                <th>Client / Business</th>
                <th>Requested Services</th>
                <th>Active Stage</th>
                <th style="width: 220px;">Progress %</th>
                <th>Status</th>
                <th>Actions</th>
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
                <tr class="{{ $selectedLead && $selectedLead->id == $lead->id ? 'note ok' : '' }}">
                    <td><strong>#{{ $lead->id }}</strong></td>
                    <td>
                        <a href="{{ route('admin.journey.index', ['lead_id' => $lead->id]) }}" style="color:var(--tx); text-decoration:none; font-weight:600;">
                            {{ $lead->full_name }}
                        </a>
                        <br>
                        <small style="color:var(--mu)">{{ $lead->company_name ?: 'Sole Trader' }}</small>
                    </td>
                    <td>
                        @foreach($lead->services->take(2) as $s)
                            <span class="tag mu">{{ $s->name }}</span>
                        @endforeach
                        @if($lead->services->count() > 2)
                            <span class="tag mu">+{{ $lead->services->count() - 2 }}</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $currentStep ? $currentStep->title : 'Completed' }}</strong>
                        <div style="font-size:11px; color:var(--mu);">Stage {{ $completedSteps }} of {{ $totalSteps }}</div>
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div class="bar" style="flex:1;">
                                <i style="width: {{ $percent }}%; background: {{ $percent == 100 ? 'var(--ok)' : 'linear-gradient(90deg,var(--ac),var(--ac2))' }};"></i>
                            </div>
                            <span style="font-size:12px; font-weight:600; width:36px; text-align:right;">{{ $percent }}%</span>
                        </div>
                    </td>
                    <td>
                        <span class="tag {{ $lead->status === 'converted' ? 'ok' : ($lead->status === 'in_progress' ? 'wn' : '') }}">
                            {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.journey.index', ['lead_id' => $lead->id]) }}" class="btn s sm">View 12 Stages</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding: 30px; color:var(--mu)">
                        No active client journeys available.
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
