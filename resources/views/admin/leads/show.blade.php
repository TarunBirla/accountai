@extends('layouts.admin')

@section('title', 'Lead #' . $lead->id . ' – ' . $lead->full_name)
@section('page_title', $lead->full_name)
@section('page_subtitle', ($lead->company_name ?: 'Individual Client') . ' · Intake Lead #' . $lead->id)

@section('content')
<div style="margin-bottom: 14px;">
    <a href="{{ route('admin.leads.index') }}" class="btn s sm">&larr; Back to Leads List</a>
</div>

<div class="g g2">
    <!-- Left Column: Lead Overview & Journey -->
    <div>
        <div class="card" style="margin-bottom: 16px;">
            <h3>Client Information &amp; Scope <em>Source: {{ ucfirst($lead->source) }}</em></h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div>
                    <span style="color:var(--mu); font-size:12px;">Full Name</span>
                    <div style="font-size:15px; font-weight:600;">{{ $lead->full_name }}</div>
                </div>
                <div>
                    <span style="color:var(--mu); font-size:12px;">Company / Trading Name</span>
                    <div style="font-size:15px; font-weight:600;">{{ $lead->company_name ?: 'Sole Trader / Personal' }}</div>
                </div>
                <div>
                    <span style="color:var(--mu); font-size:12px;">Email Address</span>
                    <div><a href="mailto:{{ $lead->email }}" style="color:var(--ac); font-weight:500;">{{ $lead->email }}</a></div>
                </div>
                <div>
                    <span style="color:var(--mu); font-size:12px;">Contact Telephone</span>
                    <div><a href="tel:{{ $lead->phone }}" style="color:var(--ac); font-weight:500;">{{ $lead->phone }}</a></div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--bd); padding-top: 12px; margin-top: 12px;">
                <span style="color:var(--mu); font-size:12px; display:block; margin-bottom: 6px;">Requested Accountancy Services</span>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    @foreach($lead->services as $srv)
                        <span class="tag {{ $srv->type === 'business' ? '' : 'wn' }}">
                            {{ $srv->name }} ({{ ucfirst($srv->type) }})
                        </span>
                    @endforeach
                </div>
            </div>

            <div style="border-top: 1px solid var(--bd); padding-top: 12px; margin-top: 14px; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <span style="color:var(--mu); font-size:12px;">Current Status:</span>
                    <strong>{{ ucfirst(str_replace('_', ' ', $lead->status)) }}</strong>
                </div>
                <form method="POST" action="{{ route('admin.leads.status', $lead->id) }}" style="display:flex; gap: 6px;">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-control" style="width: auto; padding: 4px 8px; font-size: 12px;">
                        <option value="new" {{ $lead->status == 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ $lead->status == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="in_progress" {{ $lead->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="converted" {{ $lead->status == 'converted' ? 'selected' : '' }}>Converted</option>
                        <option value="closed" {{ $lead->status == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    <button type="submit" class="btn sm">Update</button>
                </form>
            </div>
        </div>

        <!-- Journey Steps Timeline -->
        <div class="card">
            <h3>Client Onboarding Journey Timeline <em>Overall: {{ $lead->progress_percentage }}%</em></h3>
            <div class="bar" style="margin-bottom: 16px;">
                <i style="width: {{ $lead->progress_percentage }}%;"></i>
            </div>

            <div class="steps">
                @foreach($lead->journeySteps as $st)
                    <div class="step-item {{ $st->status === 'done' ? 'dn' : 'cu' }}">
                        <b>{{ $st->status === 'done' ? '✓' : $st->step_order }}</b>
                        {{ $st->title }}
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 16px;">
                @foreach($lead->journeySteps as $step)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--bd);">
                        <div>
                            <strong>{{ $step->step_order }}. {{ $step->title }}</strong>
                            <div style="font-size: 12px; color: var(--mu);">{{ $step->description }}</div>
                        </div>
                        <form method="POST" action="{{ route('admin.journey.steps.status', $step->id) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $step->status === 'done' ? 'pending' : 'done' }}">
                            <button type="submit" class="btn sm {{ $step->status === 'done' ? 'ok' : 's' }}">
                                {{ $step->status === 'done' ? '✓ Completed' : 'Mark Done' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Column: Notes & Reminders -->
    <div>
        <div class="card" style="margin-bottom: 16px;">
            <h3>Internal Notes <em>Shared across practice accountants</em></h3>
            <form method="POST" action="{{ route('admin.leads.notes', $lead->id) }}">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <textarea name="notes" class="form-control" rows="5" placeholder="Add consultation notes, client records, or compliance flags...">{{ old('notes', $lead->notes) }}</textarea>
                </div>
                <button type="submit" class="btn sm" style="width:100%;">Save Notes</button>
            </form>
        </div>

        <div class="card">
            <h3>Reminders &amp; Deadlines for this Lead</h3>
            
            <form method="POST" action="{{ route('admin.reminders.store') }}" style="margin-bottom: 14px;">
                @csrf
                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                <div class="form-group">
                    <label>Task / Reminder Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Chase P60 or call director" required>
                </div>
                <div class="form-group">
                    <label>Remind At Date &amp; Time</label>
                    <input type="datetime-local" name="remind_at" class="form-control" required value="{{ now()->addDays(2)->format('Y-m-d\TH:i') }}">
                </div>
                <button type="submit" class="btn sm" style="width:100%;">+ Add Reminder</button>
            </form>

            @if($lead->reminders->isEmpty())
                <p style="color:var(--mu); font-size:12px; margin: 0;">No active reminders scheduled for this lead.</p>
            @else
                <div class="feed" style="max-height: 250px;">
                    @foreach($lead->reminders as $rem)
                        <div>
                            <span class="dot {{ $rem->is_done ? 'ok' : ($rem->isOverdue() ? 'e' : 'h') }}"></span>
                            <span style="flex:1;">
                                <span style="{{ $rem->is_done ? 'text-decoration: line-through; color: var(--mu);' : 'font-weight:600;' }}">
                                    {{ $rem->title }}
                                </span>
                                <br>
                                <small style="color: {{ $rem->isOverdue() ? 'var(--er)' : 'var(--mu)' }};">
                                    Due: {{ $rem->remind_at->format('d M Y H:i') }}
                                </small>
                            </span>
                            <form method="POST" action="{{ route('admin.reminders.toggle', $rem->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn s sm">{{ $rem->is_done ? 'Reopen' : 'Done' }}</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
