@extends('layouts.admin')

@section('title', 'Practice Overview Dashboard')
@section('page_title', 'Practice overview')
@section('page_subtitle', 'Every customer, every deadline, one view')

@section('content')
<div class="card hero g" style="margin-bottom:16px">
    <div style="flex:1">
        <h2>Your digital operations employee is on shift</h2>
        <p>Capturing inbound leads, classifying multi-service requirements, and tracking client onboarding across all branches 24 hours a day.</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a href="{{ route('admin.leads.index') }}" class="btn w">View All Leads ({{ $totalLeads }})</a>
        <a href="{{ route('admin.leads.export') }}" class="btn w">Export CSV</a>
    </div>
</div>

<div class="g g4" style="margin-bottom:16px">
    <div class="card kpi">
        <span>Total Leads</span>
        <b>{{ $totalLeads }}</b>
        <i>All active intake channels</i>
    </div>
    <div class="card kpi">
        <span>Website Inquiries</span>
        <b>{{ $websiteLeads }}</b>
        <i style="color:var(--ac);">Primary funnels</i>
    </div>
    <div class="card kpi">
        <span>Social Media Leads</span>
        <b>{{ $socialLeads }}</b>
        <i style="color:var(--ac2);">Paid &amp; organic campaigns</i>
    </div>
    <div class="card kpi">
        <span>Active In Progress</span>
        <b>{{ $statusCounts['in_progress'] ?? 0 }}</b>
        <i style="color:var(--ok);">{{ $statusCounts['converted'] ?? 0 }} Converted</i>
    </div>
</div>

<div class="g g2">
    <div class="card">
        <h3>Leads by Lifecycle Status <em>Real-time distribution</em></h3>
        @php
            $maxCount = max(array_values($statusCounts)) ?: 1;
            $statuses = [
                'new' => ['label' => 'New Inbound', 'color' => 'var(--ac)'],
                'contacted' => ['label' => 'Contacted / Consultation', 'color' => 'var(--wn)'],
                'in_progress' => ['label' => 'In Progress (Active Work)', 'color' => 'var(--ac2)'],
                'converted' => ['label' => 'Converted & Completed', 'color' => 'var(--ok)'],
                'closed' => ['label' => 'Closed / Archived', 'color' => 'var(--mu)'],
            ];
        @endphp

        @foreach($statuses as $key => $st)
            @php $count = $statusCounts[$key] ?? 0; @endphp
            <div style="display:grid;grid-template-columns:160px 1fr 35px;gap:10px;align-items:center;margin:12px 0;">
                <span style="font-size:13px; font-weight: 500;">{{ $st['label'] }}</span>
                <div class="bar">
                    <i style="width:{{ ($count / $maxCount) * 100 }}%; background: {{ $st['color'] }};"></i>
                </div>
                <b style="font-size: 13px;">{{ $count }}</b>
            </div>
        @endforeach
    </div>

    <div class="card">
        <h3>Statutory Deadlines &amp; Reminders <em>{{ $urgentReminders->count() }} action items</em></h3>
        @if($urgentReminders->isEmpty())
            <div class="note ok" style="margin-top: 10px;">
                ✓ All client statutory reminders are up to date.
            </div>
        @else
            <div class="feed">
                @foreach($urgentReminders as $rem)
                    <div>
                        <span class="dot {{ $rem->isOverdue() ? 'e' : 'h' }}"></span>
                        <span>
                            <b>{{ $rem->title }}</b>
                            <br>
                            <small style="color:var(--mu)">
                                Lead: {{ $rem->lead ? $rem->lead->full_name : 'General Task' }}
                            </small>
                        </span>
                        <time style="color: {{ $rem->isOverdue() ? 'var(--er)' : 'var(--wn)' }}; font-weight:600;">
                            {{ $rem->remind_at->format('d M H:i') }}
                        </time>
                    </div>
                @endforeach
            </div>
        @endif
        <div style="margin-top: 14px;">
            <a href="{{ route('admin.reminders.index') }}" class="btn s sm" style="width:100%;">Manage All Reminders &rarr;</a>
        </div>
    </div>
</div>

<div class="card" style="margin-top:16px">
    <h3>Recent Leads Captured <em>Showing latest 7 entries</em></h3>
    <div class="tb">
        <table>
            <thead>
                <tr>
                    <th>Lead ID</th>
                    <th>Customer Name</th>
                    <th>Company</th>
                    <th>Source</th>
                    <th>Services Requested</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLeads as $lead)
                    <tr class="cl" onclick="window.location='{{ route('admin.leads.show', $lead->id) }}'">
                        <td>#{{ $lead->id }}</td>
                        <td><strong>{{ $lead->full_name }}</strong></td>
                        <td>{{ $lead->company_name ?: 'Sole trader / Personal' }}</td>
                        <td>
                            <span class="tag {{ $lead->source === 'social' ? 'wn' : '' }}">{{ ucfirst($lead->source) }}</span>
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
                            @php
                                $statusClass = match($lead->status) {
                                    'new' => '',
                                    'contacted' => 'wn',
                                    'in_progress' => '',
                                    'converted' => 'ok',
                                    'closed' => 'mu',
                                    default => ''
                                };
                            @endphp
                            <span class="tag {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $lead->status)) }}</span>
                        </td>
                        <td style="color:var(--mu); font-size:12px;">{{ $lead->created_at->format('d M, H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.leads.show', $lead->id) }}" class="btn s sm" onclick="event.stopPropagation()">View Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding: 20px; color:var(--mu)">No leads received yet. Submit a test form on the public website.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
