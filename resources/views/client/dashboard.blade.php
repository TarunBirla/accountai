@extends('layouts.client')

@section('title', 'Client Dashboard – AccountAI')
@section('page_title', 'Client Dashboard')
@section('page_subtitle', 'Welcome back, ' . $user->name . ' · ' . ($user->company ?: 'Personal Account'))

@section('content')
<div class="hero" style="margin-bottom: 20px;">
    <div style="flex:1;">
        <h2>Welcome to your AccountAI Practice Portal</h2>
        <p>Monitor your onboarding milestones, view assigned accounting services, and stay informed on HMRC filing progress.</p>
    </div>
    <div>
        <a href="{{ route('client.profile') }}" class="btn w">Security &amp; Password</a>
    </div>
</div>

@if($leads->isEmpty())
    <div class="card">
        <h3>No Enquiries On Record</h3>
        <p style="color:var(--mu);">You do not currently have any active enquiries linked to your account.</p>
        <a href="{{ route('home') }}" class="btn">Explore Our Accountancy Services</a>
    </div>
@else
    @if($leads->count() > 1)
        <div class="card" style="margin-bottom: 16px; padding: 12px 18px;">
            <div style="display:flex; align-items:center; gap: 10px; flex-wrap:wrap;">
                <span style="font-weight:600; font-size:13px; color:var(--mu);">Select Active Enquiry:</span>
                @foreach($leads as $l)
                    <a href="{{ route('client.dashboard', ['lead_id' => $l->id]) }}" class="btn sm {{ $selectedLead->id == $l->id ? '' : 's' }}">
                        #{{ $l->id }} · {{ $l->company_name ?: $l->full_name }} ({{ $l->created_at->format('M Y') }})
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="g g2">
        <!-- Main Journey Timeline -->
        <div class="card">
            <h3>
                Onboarding &amp; Service Journey
                <em>{{ $selectedLead->progress_percentage }}% Complete</em>
            </h3>

            <div class="bar" style="margin-bottom: 20px;">
                <i style="width: {{ $selectedLead->progress_percentage }}%;"></i>
            </div>

            <!-- Steps Bar -->
            <div class="steps">
                @foreach($selectedLead->journeySteps as $st)
                    <div class="step-item {{ $st->status === 'done' ? 'dn' : 'cu' }}">
                        <b>{{ $st->status === 'done' ? '✓' : $st->step_order }}</b>
                        {{ $st->title }}
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 20px;">
                @foreach($selectedLead->journeySteps as $step)
                    <div style="display: flex; gap: 14px; padding: 12px 0; border-bottom: 1px solid var(--bd);">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: {{ $step->status === 'done' ? 'var(--ok)' : 'var(--bd)' }}; color: {{ $step->status === 'done' ? '#fff' : 'var(--tx)' }}; display:grid; place-items:center; font-weight:700; font-size:12px; flex:none;">
                            {{ $step->status === 'done' ? '✓' : $step->step_order }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:600; font-size:14px; display:flex; align-items:center; gap:8px;">
                                {{ $step->title }}
                                @if($step->status === 'done')
                                    <span class="tag ok">Completed</span>
                                @else
                                    <span class="tag wn">Pending</span>
                                @endif
                            </div>
                            <div style="color:var(--mu); font-size:12.5px; margin-top:3px;">
                                {{ $step->description }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Sidebar Info: Services & Account Details -->
        <div>
            <div class="card" style="margin-bottom: 16px;">
                <h3>Requested Services <em>{{ $selectedLead->services->count() }} active</em></h3>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    @foreach($selectedLead->services as $srv)
                        <div style="background:var(--s2); border:1px solid var(--bd); border-radius:10px; padding:10px 12px;">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <strong style="font-size:13px;">{{ $srv->name }}</strong>
                                <span class="tag {{ $srv->type === 'business' ? '' : 'wn' }}">{{ ucfirst($srv->type) }}</span>
                            </div>
                            <div style="font-size:11.5px; color:var(--mu); margin-top:4px;">
                                {{ $srv->description }}
                            </div>
                            <a href="{{ route('services.show', $srv->slug) }}" target="_blank" style="display:inline-block; font-size:11px; color:var(--ac); text-decoration:none; margin-top:4px; font-weight:600;">Service Details &rarr;</a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <h3>Dedicated Practice Support</h3>
                <p style="color:var(--mu); font-size:13px; margin-top:0;">Need to submit extra records or discuss your filing?</p>
                <div class="mail">
                    <b>Account Practice Team</b><br>
                    Email: <a href="mailto:support@accountai.com" style="color:var(--ac);">support@accountai.com</a><br>
                    Phone: +44 (0) 20 7946 0991
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
