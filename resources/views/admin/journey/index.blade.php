@extends('layouts.admin')

@section('title', 'Client Onboarding Journey')
@section('page_title', 'Client journey')
@section('page_subtitle', 'End-to-end client onboarding lifecycle & progress tracking')

@section('content')
<div class="card" style="margin-bottom: 16px;">
    <h3>Stage Progression Overview <em>5 connected milestone stages</em></h3>
    <div class="steps">
        <div class="step-item dn"><b>1</b>Lead Received</div>
        <div class="step-item cu"><b>2</b>Contacted</div>
        <div class="step-item"><b>3</b>Documents Collected</div>
        <div class="step-item"><b>4</b>In Progress</div>
        <div class="step-item"><b>5</b>Completed</div>
    </div>
</div>

<div class="card tb">
    <table>
        <thead>
            <tr>
                <th>Lead ID</th>
                <th>Client / Business</th>
                <th>Services</th>
                <th>Current Milestone</th>
                <th style="width: 220px;">Progress %</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leads as $lead)
                @php
                    $completedSteps = $lead->journeySteps->where('status', 'done')->count();
                    $totalSteps = $lead->journeySteps->count() ?: 1;
                    $percent = (int) round(($completedSteps / $totalSteps) * 100);
                    $currentStep = $lead->journeySteps->firstWhere('status', 'pending') ?: $lead->journeySteps->last();
                @endphp
                <tr>
                    <td><strong>#{{ $lead->id }}</strong></td>
                    <td>
                        <a href="{{ route('admin.leads.show', $lead->id) }}" style="color:var(--tx); text-decoration:none; font-weight:600;">
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
                        <div style="font-size:11px; color:var(--mu);">Step {{ $completedSteps }} of {{ $totalSteps }}</div>
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
                        <a href="{{ route('admin.leads.show', $lead->id) }}" class="btn s sm">Manage Steps</a>
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
