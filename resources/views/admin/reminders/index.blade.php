@extends('layouts.admin')

@section('title', 'Reminders & Deadlines')
@section('page_title', 'Reminders & calls')
@section('page_subtitle', 'Risk-based statutory reminders, escalation, and follow-ups')

@section('content')
<div class="g g2" style="margin-bottom: 16px;">
    <!-- Reminder Creation Card -->
    <div class="card">
        <h3>Create New Practice Task / Reminder</h3>
        <form method="POST" action="{{ route('admin.reminders.store') }}">
            @csrf
            <div class="form-group">
                <label>Related Lead (Optional)</label>
                <select name="lead_id" class="form-control">
                    <option value="">-- General Practice Task --</option>
                    @foreach($leads as $ld)
                        <option value="{{ $ld->id }}">{{ $ld->full_name }} ({{ $ld->company_name ?: 'Individual' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Task Title / Subject</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Call client for VAT backup or Companies House review" required>
            </div>
            <div class="form-group">
                <label>Remind Date &amp; Time</label>
                <input type="datetime-local" name="remind_at" class="form-control" required value="{{ now()->addDay()->format('Y-m-d\TH:i') }}">
            </div>
            <button type="submit" class="btn" style="width:100%;">Create Reminder</button>
        </form>
    </div>

    <!-- Scheduler info card -->
    <div class="card">
        <h3>Daily Automated Email Alert</h3>
        <p style="color:var(--mu); font-size:13px;">AccountAI includes an automated daily scheduler command (<code>php artisan reminders:send-due</code>) configured to email practice managers an alert for all overdue and due-today tasks.</p>
        <div class="mail">
            <b>Live Cron Integration:</b><br>
            <code>* * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1</code>
        </div>
        <div class="note ok" style="margin-top:14px;">
            Reminders are filtered and dispatched to <strong>{{ config('mail.admin_address', 'admin@accountai.com') }}</strong>.
        </div>
    </div>
</div>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 14px; flex-wrap:wrap; gap:10px;">
    <div style="display:flex; gap:6px;">
        <a href="{{ route('admin.reminders.index', ['filter' => 'pending']) }}" class="btn sm {{ $filter === 'pending' ? '' : 's' }}">Pending Active</a>
        <a href="{{ route('admin.reminders.index', ['filter' => 'today']) }}" class="btn sm {{ $filter === 'today' ? '' : 's' }}">Due Today</a>
        <a href="{{ route('admin.reminders.index', ['filter' => 'overdue']) }}" class="btn sm {{ $filter === 'overdue' ? 'er' : 's' }}">Overdue</a>
        <a href="{{ route('admin.reminders.index', ['filter' => 'done']) }}" class="btn sm {{ $filter === 'done' ? 'ok' : 's' }}">Completed</a>
        <a href="{{ route('admin.reminders.index', ['filter' => 'all']) }}" class="btn sm {{ $filter === 'all' ? '' : 's' }}">All</a>
    </div>
</div>

<div class="card tb">
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Task Title</th>
                <th>Associated Lead</th>
                <th>Due Date &amp; Time</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reminders as $rem)
                <tr class="{{ $rem->isOverdue() ? 'note er' : '' }}">
                    <td>
                        @if($rem->is_done)
                            <span class="tag ok">Done</span>
                        @elseif($rem->isOverdue())
                            <span class="tag er">OVERDUE</span>
                        @elseif($rem->isDueToday())
                            <span class="tag wn">DUE TODAY</span>
                        @else
                            <span class="tag mu">Upcoming</span>
                        @endif
                    </td>
                    <td>
                        <strong style="{{ $rem->is_done ? 'text-decoration: line-through; color:var(--mu);' : '' }}">
                            {{ $rem->title }}
                        </strong>
                    </td>
                    <td>
                        @if($rem->lead)
                            <a href="{{ route('admin.leads.show', $rem->lead_id) }}" style="color:var(--ac); text-decoration:none;">
                                {{ $rem->lead->full_name }}
                            </a>
                        @else
                            <span style="color:var(--mu)">General Practice</span>
                        @endif
                    </td>
                    <td>
                        <span style="color: {{ $rem->isOverdue() ? 'var(--er)' : 'inherit' }}; font-weight: {{ $rem->isOverdue() ? '700' : '500' }};">
                            {{ $rem->remind_at->format('d M Y, H:i') }}
                        </span>
                    </td>
                    <td style="color:var(--mu); font-size:12px;">{{ $rem->creator?->name ?? 'System' }}</td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <form method="POST" action="{{ route('admin.reminders.toggle', $rem->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn s sm">{{ $rem->is_done ? 'Reopen' : 'Mark Done' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.reminders.destroy', $rem->id) }}" onsubmit="return confirm('Delete this reminder?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn s sm" style="color:var(--er)!important;">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding: 30px; color:var(--mu);">
                        No reminders found for this filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-wrap">
    {{ $reminders->links() }}
</div>
@endsection
