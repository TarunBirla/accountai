@extends('layouts.admin')

@section('title', 'Client Journey – ' . ($lead->company_name ?: $lead->full_name))
@section('page_title', 'Client journey')
@section('page_subtitle', ($lead->company_name ?: $lead->full_name) . ' · 12 connected stages')

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
.param-table {
    width: 100%;
    border-collapse: collapse;
    background: var(--s2);
    border-radius: 10px;
    overflow: hidden;
    margin: 12px 0;
}
.param-table td {
    padding: 9px 14px;
    border-bottom: 1px solid var(--bd);
    font-size: 13px;
}
.param-table td.label {
    width: 220px;
    font-weight: 600;
    color: var(--mu);
}
</style>
@endpush

@section('content')
<div style="margin-bottom: 14px; display:flex; justify-content:space-between; align-items:center;">
    <a href="{{ route('admin.journey.index') }}" class="btn s sm">&larr; Back to Client List</a>
    <a href="{{ route('admin.leads.show', $lead->id) }}" class="btn s sm">View Full Lead File &rarr;</a>
</div>

<!-- 12 Connected Stages Flow (Screenshot Match) -->
<div class="card" style="padding: 20px; margin-bottom: 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
        <div>
            <strong style="font-size:17px;">{{ $lead->company_name ?: $lead->full_name }}</strong>
            <span class="tag {{ $lead->status === 'converted' ? 'ok' : 'wn' }}" style="margin-left:8px;">
                {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
            </span>
            <span style="font-size:12px; color:var(--mu); margin-left:8px;">Overall Progress: <b>{{ $lead->progress_percentage }}%</b></span>
        </div>
        <div style="font-size:12px; color:var(--mu);">
            Assigned Email: <b>{{ $lead->email }}</b>
        </div>
    </div>

    <div class="stages-flow">
        @php
            $steps = $lead->journeySteps->sortBy('step_order');
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
</div>

<!-- Current Stage Review & Approval Box -->
@if($activeStep)
    <div class="card" style="margin-bottom: 24px; border-top: 4px solid var(--ac);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 12px; flex-wrap:wrap; gap:8px;">
            <div>
                <span class="tag {{ $activeStep->isSubmitted() ? 'wn' : ($activeStep->isRevisionRequired() ? 'er' : 'mu') }}">
                    Stage {{ $activeStep->step_order }}: {{ ucfirst(str_replace('_', ' ', $activeStep->review_status)) }}
                </span>
                <h3 style="font-family:var(--d); font-size:20px; margin:8px 0 4px;">Review: {{ $activeStep->title }}</h3>
                <p style="color:var(--mu); font-size:13px; margin:0;">{{ $activeStep->description }}</p>
            </div>
        </div>

        @if($activeStep->isSubmitted())
            <div class="note ok" style="margin: 12px 0;">
                <strong>✓ Client Has Submitted Parameters:</strong> Review the client's submitted responses below. If correct, approve to unlock the next stage. If incorrect, enter feedback notes and return for revision.
            </div>
        @elseif($activeStep->isRevisionRequired())
            <div class="note er" style="margin: 12px 0;">
                <strong>Currently Awaiting Client Revision:</strong> You returned this stage with note: <em>"{{ $activeStep->admin_notes }}"</em>
            </div>
        @else
            <div class="note" style="margin: 12px 0;">
                <strong>Awaiting Client Action:</strong> The client has this stage open in their portal to fill required parameters.
            </div>
        @endif

        <!-- Client Submitted Parameters -->
        @if(!empty($activeStep->payload))
            <h4 style="margin: 14px 0 6px; font-size:14px;">Submitted Client Parameters:</h4>
            <table class="param-table">
                @foreach($activeStep->payload as $key => $val)
                    <tr>
                        <td class="label">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                        <td>
                            @if(is_array($val))
                                {{ implode(', ', $val) }}
                            @else
                                <strong>{{ $val ?: 'N/A' }}</strong>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif

        <!-- Admin Decision Forms -->
        <div style="background:var(--s2); border:1px solid var(--bd); border-radius:12px; padding:18px; margin-top:16px;">
            <h4 style="margin: 0 0 12px; font-size:14px;">Accountant Verification Decision</h4>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                <!-- Option A: Approve -->
                <div style="background:#fff; border:1px solid #12a36b44; border-radius:10px; padding:16px;">
                    <strong style="color:var(--ok); font-size:14px; display:block; margin-bottom:6px;">✓ Option A: Approve Stage</strong>
                    <p style="color:var(--mu); font-size:12.5px; margin: 0 0 12px;">Approving marks Stage {{ $activeStep->step_order }} complete and automatically dispatches next stage request to the client.</p>
                    
                    <form method="POST" action="{{ route('admin.journey.steps.review', $activeStep->id) }}">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn ok" style="width:100%;">
                            Approve Stage {{ $activeStep->step_order }} &amp; Send Next Request &rarr;
                        </button>
                    </form>
                </div>

                <!-- Option B: Request Revision -->
                <div style="background:#fff; border:1px solid #dc3d4a44; border-radius:10px; padding:16px;">
                    <strong style="color:var(--er); font-size:14px; display:block; margin-bottom:6px;">✕ Option B: Request Revision (Send Note Back)</strong>
                    <p style="color:var(--mu); font-size:12.5px; margin: 0 0 10px;">Specify what is incorrect so the client can fix and re-submit.</p>

                    <form method="POST" action="{{ route('admin.journey.steps.review', $activeStep->id) }}">
                        @csrf
                        <input type="hidden" name="action" value="revision">
                        <div class="form-group" style="margin-bottom:8px;">
                            <textarea name="admin_notes" class="form-control" rows="2" placeholder="e.g. Please provide valid VAT registration number or upload last quarter statement..." required>{{ old('admin_notes', $activeStep->admin_notes) }}</textarea>
                        </div>
                        <button type="submit" class="btn er" style="width:100%; font-size:12.5px;">
                            Return to Client with Note &rarr;
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="card note ok" style="text-align:center; padding: 24px;">
        <h3>✓ All 12 Connected Stages Completed</h3>
        <p style="margin: 4px 0 0;">This client case has reached full statutory completion and is officially converted.</p>
    </div>
@endif

<!-- All 12 Stages Table with Details -->
<div class="card tb">
    <h3>Detailed 12-Stage Audit Trail</h3>
    <table>
        <thead>
            <tr>
                <th>Order</th>
                <th>Stage Name</th>
                <th>Status</th>
                <th>Review Status</th>
                <th>Submitted Inputs</th>
                <th>Feedback / Notes</th>
                <th>Manual Override</th>
            </tr>
        </thead>
        <tbody>
            @foreach($steps as $step)
                <tr>
                    <td><strong>{{ $step->step_order }}</strong></td>
                    <td>
                        <strong style="font-size:13px;">{{ $step->title }}</strong>
                        <div style="font-size:11px; color:var(--mu);">{{ Str::limit($step->description, 50) }}</div>
                    </td>
                    <td>
                        <span class="tag {{ $step->status === 'done' ? 'ok' : 'mu' }}">
                            {{ $step->status === 'done' ? 'Done' : 'Pending' }}
                        </span>
                    </td>
                    <td>
                        <span class="tag {{ $step->review_status === 'approved' ? 'ok' : ($step->isSubmitted() ? 'wn' : ($step->isRevisionRequired() ? 'er' : 'mu')) }}">
                            {{ ucfirst(str_replace('_', ' ', $step->review_status)) }}
                        </span>
                    </td>
                    <td>
                        @if(!empty($step->payload))
                            <small style="color:var(--ac); font-weight:600;">{{ count($step->payload) }} fields submitted</small>
                        @else
                            <small style="color:var(--mu);">&mdash;</small>
                        @endif
                    </td>
                    <td style="max-width:200px; font-size:12px; color:var(--mu);">
                        {{ $step->admin_notes ?: '&mdash;' }}
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.journey.steps.status', $step->id) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $step->status === 'done' ? 'pending' : 'done' }}">
                            <button type="submit" class="btn sm {{ $step->status === 'done' ? 's' : 'ok' }}" style="padding:2px 8px; font-size:11px;">
                                {{ $step->status === 'done' ? 'Reopen' : 'Mark Done' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
