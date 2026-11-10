@extends('layouts.admin')

@section('title', $selectedLead ? ($selectedLead->company_name ?: $selectedLead->full_name) . ' – Client Journey' : 'Client Journey')
@section('page_title', 'Client journey')
@section('page_subtitle', $selectedLead ? (($selectedLead->company_name ?: $selectedLead->full_name) . ' · 12 connected stages') : 'Overview of all client onboarding workflows')

@push('styles')
<style>
.stages-flow {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 12px;
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
    color: var(--mu);
    transition: all .2s;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
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
.stage-card.revision {
    border: 2px solid var(--wn);
    background: #fffdf5;
}
.stage-card.revision .badge-icon {
    background: var(--wn);
    color: #fff;
}
.stage-card.revision .stage-title {
    color: var(--wn);
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
.parameter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 8px;
    background: var(--s2);
    border: 1px solid var(--bd);
    border-radius: 8px;
    padding: 10px;
    margin: 8px 0;
    font-size: 12px;
}
.param-item strong {
    display: block;
    font-size: 11px;
    color: var(--mu);
    text-transform: capitalize;
}
</style>
@endpush

@section('content')

@if(!$selectedLead)
    <!-- VIEW 1: USER LIST FIRST (Clean Table) -->
    <div class="card" style="margin-bottom: 16px;">
        <form method="GET" action="{{ route('admin.journey.index') }}" style="display:flex; gap:10px; align-items:center;">
            <input type="text" name="search" class="form-control" placeholder="Search client by name, email or company..." value="{{ request('search') }}" style="max-width: 400px;">
            <button type="submit" class="btn sm">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.journey.index') }}" class="btn s sm">Reset</a>
            @endif
        </form>
    </div>

    <div class="card tb">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 14px;">
            <h3 style="margin:0;">All Client Journey Workflows <em>Select a client to view their 12 connected stages</em></h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Lead ID</th>
                    <th>Client / Company</th>
                    <th>Email &amp; Phone</th>
                    <th>Requested Services</th>
                    <th>Active Stage</th>
                    <th style="width: 200px;">Overall Progress %</th>
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
                        $currentStep = $lead->journeySteps->firstWhere('status', '!=', 'done') ?: $lead->journeySteps->last();
                    @endphp
                    <tr class="cl" onclick="window.location='{{ route('admin.journey.index', ['lead_id' => $lead->id]) }}'">
                        <td><strong>#{{ $lead->id }}</strong></td>
                        <td>
                            <b>{{ $lead->full_name }}</b>
                            <br>
                            <small style="color:var(--mu)">{{ $lead->company_name ?: 'Sole Trader / Personal' }}</small>
                        </td>
                        <td>
                            <div style="font-size:12px;">{{ $lead->email }}</div>
                            <div style="font-size:11px; color:var(--mu);">{{ $lead->phone }}</div>
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
                        <td onclick="event.stopPropagation()">
                            <a href="{{ route('admin.journey.index', ['lead_id' => $lead->id]) }}" class="btn sm">View 12 Stages &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding: 30px; color:var(--mu)">No clients found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $leads->links() }}
    </div>

@else
    <!-- VIEW 2: SPECIFIC CLIENT 12 STAGES DETAILS -->
    <div style="margin-bottom: 14px; display:flex; justify-content:space-between; align-items:center;">
        <a href="{{ route('admin.journey.index') }}" class="btn s sm">&larr; Back to Client List</a>
        <a href="{{ route('admin.leads.show', $selectedLead->id) }}" class="btn s sm">Open Full Dossier &rarr;</a>
    </div>

    <div class="card" style="padding: 18px 20px; margin-bottom: 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
            <div>
                <strong style="font-size:17px;">{{ $selectedLead->company_name ?: $selectedLead->full_name }}</strong>
                <span class="tag {{ $selectedLead->status === 'converted' ? 'ok' : 'wn' }}" style="margin-left:8px;">
                    {{ ucfirst(str_replace('_', ' ', $selectedLead->status)) }}
                </span>
                <span style="font-size:12px; color:var(--mu); margin-left:8px;">Progress: <b>{{ $selectedLead->progress_percentage }}%</b></span>
            </div>
            <div>
                <span style="font-size:12px; color:var(--mu);">Client Email: <b>{{ $selectedLead->email }}</b> | Tel: <b>{{ $selectedLead->phone }}</b></span>
            </div>
        </div>

        <!-- 12 Connected Stages Horizontal Flow (Screenshot Match) -->
        <div class="stages-flow">
            @php
                $steps = $selectedLead->journeySteps->sortBy('step_order');
                $firstPending = $steps->firstWhere('status', '!=', 'done');
            @endphp

            @foreach($steps as $step)
                @php
                    $isDone = $step->status === 'done';
                    $isRevision = $step->status === 'needs_revision';
                    $isActive = $firstPending && $firstPending->id === $step->id;
                    $class = $isDone ? 'done' : ($isRevision ? 'revision' : ($isActive ? 'active' : 'pending'));
                @endphp
                <div class="stage-card {{ $class }}">
                    <div class="badge-icon">
                        @if($isDone)
                            ✓
                        @elseif($isRevision)
                            !
                        @else
                            {{ $step->step_order }}
                        @endif
                    </div>
                    <div class="stage-title">{{ $step->title }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Step Parameters Review & Admin Approval/Revision Section -->
    <div class="card">
        <h3>Step Parameter Verification &amp; Quality Approvals <em>Review client inputs for each stage</em></h3>
        <p style="color:var(--mu); font-size:12.5px; margin-top:0;">Check client submissions below. If any parameter is incorrect, write a note and click <strong>"Request Revision"</strong> so the client can correct it.</p>

        <div style="display:flex; flex-direction:column; gap:14px; margin-top:16px;">
            @foreach($steps as $step)
                <div style="border: 1px solid {{ $step->status === 'needs_revision' ? 'var(--wn)' : 'var(--bd)' }}; border-radius:10px; padding:14px; background: {{ $step->status === 'done' ? '#fcfdfd' : '#fff' }};">
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                        <div>
                            <span class="tag {{ $step->status === 'done' ? 'ok' : ($step->status === 'needs_revision' ? 'wn' : ($step->status === 'submitted' ? '' : 'mu')) }}" style="font-size:10.5px;">
                                Stage {{ $step->step_order }}: {{ strtoupper($step->status) }}
                            </span>
                            <strong style="font-size:14px; margin-left:6px;">{{ $step->title }}</strong>
                        </div>
                        <div style="display:flex; gap:6px;">
                            <!-- Option 1: Approve Button -->
                            <form method="POST" action="{{ route('admin.journey.steps.status', $step->id) }}" style="margin:0;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="done">
                                <button type="submit" class="btn sm {{ $step->status === 'done' ? 'ok' : 's' }}">
                                    {{ $step->status === 'done' ? '✓ Approved' : 'Approve Stage' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Client Submitted Parameters -->
                    <div style="margin-top:10px;">
                        <span style="font-size:11px; font-weight:700; color:var(--mu); text-transform:uppercase;">Client Form Submission:</span>
                        @if($step->payload && is_array($step->payload) && count($step->payload) > 0)
                            <div class="parameter-grid">
                                @foreach($step->payload as $key => $val)
                                    <div class="param-item">
                                        <strong>{{ str_replace('_', ' ', $key) }}:</strong>
                                        <span>{{ is_array($val) ? implode(', ', $val) : ($val ?: '—') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="font-size:12px; color:var(--mu); font-style:italic; padding:6px 0;">
                                No form data submitted by client for this stage yet.
                            </div>
                        @endif
                    </div>

                    <!-- Admin Feedback / Revision Note Box -->
                    @if($step->admin_notes)
                        <div style="background:#fff8eb; border:1px solid #fed7aa; border-radius:6px; padding:8px 12px; font-size:12px; color:#9a6f17; margin-top:8px;">
                            <strong>Active Revision Note to Client:</strong> {{ $step->admin_notes }}
                        </div>
                    @endif

                    <!-- Revision Form for Admin -->
                    <div style="margin-top:10px; border-top:1px dashed var(--bd); padding-top:8px;">
                        <form method="POST" action="{{ route('admin.journey.steps.status', $step->id) }}" style="display:flex; gap:8px; align-items:center;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="needs_revision">
                            <input type="text" name="admin_notes" class="form-control" style="font-size:12px; padding:6px 10px;" placeholder="Write feedback note to client if details are incorrect..." value="{{ $step->admin_notes }}">
                            <button type="submit" class="btn s sm" style="white-space:nowrap; color:var(--wn)!important;">Request Revision</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@endsection
