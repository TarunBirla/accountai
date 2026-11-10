@extends('layouts.client')

@section('title', ($selectedLead ? ($selectedLead->company_name ?: $selectedLead->full_name) . ' – ' : '') . 'Client Journey')
@section('page_title', 'Client journey')
@section('page_subtitle', $selectedLead ? (($selectedLead->company_name ?: $selectedLead->full_name) . ' · 12 connected stages') : '12 connected stages')

@push('styles')
<style>
.stages-flow {
    display: flex !important;
    flex-direction: row !important;
    gap: 8px !important;
    overflow-x: auto !important;
    padding-bottom: 12px !important;
    margin-bottom: 22px !important;
    align-items: stretch !important;
}
.stage-card {
    flex: 1 1 0% !important;
    min-width: 95px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 12px 6px !important;
    text-align: center !important;
    color: #64708a !important;
    transition: all .2s !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03) !important;
    text-decoration: none !important;
    cursor: pointer !important;
}
.stage-card:hover {
    border-color: #3a4bdc !important;
    transform: translateY(-1px) !important;
}
.stage-card .badge-icon {
    width: 26px !important;
    height: 26px !important;
    border-radius: 50% !important;
    margin-bottom: 6px !important;
    display: grid !important;
    place-items: center !important;
    font-size: 12px !important;
    font-weight: 700 !important;
}
.stage-card.done {
    border-color: #0fb5a655 !important;
    background: #ffffff !important;
}
.stage-card.done .badge-icon {
    background: #0fb5a6 !important;
    color: #ffffff !important;
}
.stage-card.done .stage-title {
    color: #0fb5a6 !important;
    font-weight: 600 !important;
}
.stage-card.active {
    border: 2px solid #3a4bdc !important;
    box-shadow: 0 0 0 3px rgba(58,75,220,0.15) !important;
    background: #ffffff !important;
}
.stage-card.active .badge-icon {
    background: #3a4bdc !important;
    color: #ffffff !important;
}
.stage-card.active .stage-title {
    color: #0e1530 !important;
    font-weight: 700 !important;
}
.stage-card.revision {
    border: 2px solid #d98a0b !important;
    background: #fffdf5 !important;
}
.stage-card.revision .badge-icon {
    background: #d98a0b !important;
    color: #ffffff !important;
}
.stage-card.revision .stage-title {
    color: #d98a0b !important;
    font-weight: 700 !important;
}
.stage-card.pending {
    background: #f8fafc !important;
}
.stage-card.pending .badge-icon {
    background: #e2e8f0 !important;
    color: #64708a !important;
}
.stage-card .stage-title {
    font-size: 11px !important;
    line-height: 1.25 !important;
    text-align: center !important;
}
.parameter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px;
    margin: 8px 0;
    font-size: 12px;
}
.param-item strong {
    display: block;
    font-size: 11px;
    color: #64708a;
    text-transform: capitalize;
}
.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.grid-3 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 12px;
}
@media(max-width: 768px) {
    .grid-2, .grid-3 { grid-template-columns: 1fr; }
}
.step-box {
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 24px;
    background: #ffffff !important;
    transition: all .2s;
    box-shadow: 0 1px 3px rgba(14,21,48,0.03);
}
.step-box.revision-box {
    border: 2px solid #d98a0b;
    background: #fffefb !important;
}
.services-choice-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 8px;
    margin-top: 6px;
}
.service-check-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12.5px;
    cursor: pointer;
}
.service-check-pill:hover {
    border-color: #3a4bdc;
    background: #f8fafc;
}
.uk-flag-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    background: #eef2ff;
    color: #3a4bdc;
    padding: 2px 8px;
    border-radius: 99px;
    font-weight: 600;
}
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="note ok" style="margin-bottom: 16px;">
        ✓ {{ session('success') }}
    </div>
@endif

@if(!$selectedLead)
    <div class="card" style="text-align: center; padding: 40px 20px; background:#ffffff;">
        <h3>No Client Onboarding Cases Found</h3>
        <p style="color:var(--mu);">You do not currently have any active enquiries or onboarding workflows linked to your account.</p>
        <a href="{{ route('home') }}" class="btn">Explore Our UK Accountancy Services &rarr;</a>
    </div>
@else

    @if($leads->count() > 1)
        <div class="card" style="margin-bottom: 16px; padding: 12px 18px; background:#ffffff;">
            <div style="display:flex; align-items:center; gap: 10px; flex-wrap:wrap;">
                <span style="font-weight:600; font-size:13px; color:var(--mu);">Select Active Case:</span>
                @foreach($leads as $l)
                    <a href="{{ route('client.dashboard', ['lead_id' => $l->id]) }}" class="btn sm {{ $selectedLead->id == $l->id ? '' : 's' }}">
                        #{{ $l->id }} · {{ $l->company_name ?: $l->full_name }} ({{ $l->created_at->format('M Y') }})
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 12 Connected Stages Horizontal Flow (Screenshot & Admin Match) -->
    <div class="card" style="padding: 18px 20px; margin-bottom: 20px; background:#ffffff;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
            <div>
                <strong style="font-size:17px; color:var(--tx);">{{ $selectedLead->company_name ?: $selectedLead->full_name }}</strong>
                <span class="tag {{ $selectedLead->status === 'converted' ? 'ok' : 'wn' }}" style="margin-left:8px;">
                    {{ ucfirst(str_replace('_', ' ', $selectedLead->status)) }}
                </span>
                <span class="uk-flag-badge" style="margin-left:6px;">🇬🇧 UK Practice Framework</span>
                <span style="font-size:12px; color:var(--mu); margin-left:8px;">Overall Progress: <b>{{ $selectedLead->progress_percentage }}%</b></span>
            </div>
            <div>
                <span style="font-size:12px; color:var(--mu);">Assigned Practice: <b>AccountAI Clearview Practice (London)</b></span>
            </div>
        </div>

        <div class="stages-flow">
            @php
                $firstPending = $steps->first(fn($s) => $s->status !== 'done' && $s->review_status !== 'approved');
            @endphp
            @foreach($steps as $st)
                @php
                    $isDone = $st->status === 'done' || $st->review_status === 'approved';
                    $isRevision = $st->review_status === 'revision_required' || $st->status === 'needs_revision';
                    $isActive = ($currentStep && $currentStep->id === $st->id) || (!$currentStep && $firstPending && $firstPending->id === $st->id);
                    $class = $isDone ? 'done' : ($isRevision ? 'revision' : ($isActive ? 'active' : 'pending'));
                @endphp
                <a href="javascript:void(0)" id="stage-card-{{ $st->step_order }}" class="stage-card {{ $class }}" onclick="switchStage({{ $st->step_order }})">
                    <div class="badge-icon">
                        @if($isDone)
                            ✓
                        @elseif($isRevision)
                            !
                        @else
                            {{ $st->step_order }}
                        @endif
                    </div>
                    <div class="stage-title">{{ $st->title }}</div>
                </a>
            @endforeach
        </div>

        <div class="bar">
            <i style="width: {{ $selectedLead->progress_percentage }}%;"></i>
        </div>
    </div>

    <!-- Active Stage Forms: ONLY the Active Stage is Displayed -->
    <div>
        @foreach($steps as $step)
            @php
                $payload = is_array($step->payload) ? $step->payload : [];
                $order = $step->step_order;
                $isCurrent = $currentStep && $currentStep->id === $step->id;
                $isDone = $step->status === 'done' || $step->review_status === 'approved';
                $isRevision = $step->review_status === 'revision_required' || $step->status === 'needs_revision';
                $isSubmitted = $step->review_status === 'submitted';
            @endphp

            <div id="stage-panel-{{ $order }}" class="stage-panel step-box {{ $isRevision ? 'revision-box' : '' }}" style="display: {{ $isCurrent ? 'block' : 'none' }};">
                <!-- Step Top Header -->
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">
                    <div>
                        <span class="tag {{ $isDone ? 'ok' : ($isRevision ? 'wn' : ($isSubmitted ? '' : 'mu')) }}" style="font-size:11px;">
                            Stage {{ $order }} of 12 &middot; {{ strtoupper($step->status) }}
                        </span>
                        <h2 style="font-family:var(--d); font-size:22px; margin: 8px 0 4px; color:var(--tx);">{{ $step->title }}</h2>
                        <p style="color:var(--mu); font-size:13.5px; margin:0;">{{ $step->description }}</p>
                    </div>
                    <div>
                        @if($isDone)
                            <span style="color:var(--ok); font-weight:600; font-size:13px; background:#12a36b18; padding:6px 12px; border-radius:8px;">✓ Verified &amp; Approved by Practice</span>
                        @elseif($isRevision)
                            <span style="color:var(--wn); font-weight:700; font-size:13px; background:#d98a0b20; padding:6px 12px; border-radius:8px;">⚠️ Revision Requested by Accountant</span>
                        @elseif($isSubmitted)
                            <span style="color:var(--ac); font-weight:600; font-size:13px; background:#3a4bdc18; padding:6px 12px; border-radius:8px;">⌛ Submitted &amp; Awaiting Review</span>
                        @else
                            <span style="color:var(--mu); font-size:13px; background:#f8fafc; border:1px solid #e2e8f0; padding:6px 12px; border-radius:8px;">Pending Client Details</span>
                        @endif
                    </div>
                </div>

                <!-- Accountant Feedback Alert (If Revision Requested) -->
                @if($step->admin_notes)
                    <div style="background:#fff8eb; border:1px solid #fed7aa; border-radius:8px; padding:12px 16px; margin-bottom:16px;">
                        <strong style="color:#9a6f17; font-size:13.5px;">⚠️ Accountant Revision Note:</strong>
                        <div style="color:#78350f; font-size:13px; margin-top:4px; font-weight:500;">{{ $step->admin_notes }}</div>
                        <small style="color:#9a6f17; display:block; margin-top:4px;">Please review the note above, update your parameters below, and click "Save &amp; Submit Stage {{ $order }}".</small>
                    </div>
                @endif

                <!-- Show Currently Submitted Parameters (If any exist) -->
                @if(!empty($payload))
                    <div style="margin-bottom:16px;">
                        <span style="font-size:11px; font-weight:700; color:var(--mu); text-transform:uppercase;">Currently On-File Parameters:</span>
                        <div class="parameter-grid">
                            @foreach($payload as $pk => $pv)
                                <div class="param-item" style="background:#ffffff; border:1px solid #e2e8f0; padding:8px 10px; border-radius:6px;">
                                    <strong>{{ str_replace('_', ' ', $pk) }}:</strong>
                                    <span>{{ is_array($pv) ? implode(', ', $pv) : ($pv ?: '—') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Active Step Form -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:20px;">
                    <div style="font-size:12px; font-weight:700; color:var(--mu); margin-bottom:14px; text-transform:uppercase;">
                        {{ $isDone ? 'Update Stage Parameters (Optional):' : 'Enter / Update Stage ' . $order . ' Parameters:' }}
                    </div>

                    <form method="POST" action="{{ route('client.requests.submit', $step->id) }}">
                        @csrf

                        @if($order === 1)
                            <!-- STAGE 1: Lead & enquiry -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="{{ $payload['first_name'] ?? $selectedLead->first_name }}">
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="{{ $payload['last_name'] ?? $selectedLead->last_name }}">
                                </div>
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Business Email Address</label>
                                    <input type="email" name="email" class="form-control" value="{{ $payload['email'] ?? $selectedLead->email }}">
                                </div>
                                <div class="form-group">
                                    <label>UK Phone / Mobile Number (+44)</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="e.g. +44 7911 123456" value="{{ $payload['phone'] ?? $selectedLead->phone }}">
                                </div>
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Company / Sole Trader Trading Name</label>
                                    <input type="text" name="company_name" class="form-control" value="{{ $payload['company_name'] ?? $selectedLead->company_name }}">
                                </div>
                                <div class="form-group">
                                    <label>Enquiry Channel / Source</label>
                                    <select name="source" class="form-control">
                                        <option value="Website" {{ ($payload['source'] ?? $selectedLead->source) === 'website' ? 'selected' : '' }}>Website Form</option>
                                        <option value="Google Business Profile" {{ ($payload['source'] ?? '') === 'Google Business Profile' ? 'selected' : '' }}>Google Search / Maps</option>
                                        <option value="Referral" {{ ($payload['source'] ?? '') === 'Referral' ? 'selected' : '' }}>Referral / Recommendation</option>
                                        <option value="Companies House" {{ ($payload['source'] ?? '') === 'Companies House' ? 'selected' : '' }}>Companies House Direct</option>
                                        <option value="Social Media" {{ ($payload['source'] ?? '') === 'Social Media' ? 'selected' : '' }}>LinkedIn / Social Media</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Initial Enquiry Scope &amp; UK Tax Requirements</label>
                                <textarea name="message" class="form-control" rows="2" placeholder="e.g. Need annual statutory accounts, CT600 corporation tax, quarterly VAT MTD, and payroll for 2 directors.">{{ $payload['message'] ?? $selectedLead->message }}</textarea>
                            </div>

                        @elseif($order === 2)
                            <!-- STAGE 2: Qualification -->
                            <div class="form-group">
                                <label>UK Business Legal Structure</label>
                                <select name="business_structure" class="form-control">
                                    <option value="">-- Select UK Entity Type --</option>
                                    <option value="Private Limited Company (Ltd)" {{ ($payload['business_structure'] ?? '') === 'Private Limited Company (Ltd)' ? 'selected' : '' }}>Private Limited Company (Ltd)</option>
                                    <option value="Sole Trader / Self-Employed" {{ ($payload['business_structure'] ?? '') === 'Sole Trader / Self-Employed' ? 'selected' : '' }}>Sole Trader / Self-Employed (HMRC SA100)</option>
                                    <option value="Limited Liability Partnership (LLP)" {{ ($payload['business_structure'] ?? '') === 'Limited Liability Partnership (LLP)' ? 'selected' : '' }}>Limited Liability Partnership (LLP)</option>
                                    <option value="Ordinary Partnership" {{ ($payload['business_structure'] ?? '') === 'Ordinary Partnership' ? 'selected' : '' }}>Ordinary Partnership (SA800)</option>
                                    <option value="Property SPV / Landlord" {{ ($payload['business_structure'] ?? '') === 'Property SPV / Landlord' ? 'selected' : '' }}>Property SPV / Buy-To-Let Landlord</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Required UK Accountancy &amp; Tax Services (Select all that apply)</label>
                                @php
                                    $selectedServices = is_array($payload['services'] ?? null) ? $payload['services'] : ($selectedLead->services ? $selectedLead->services->pluck('name')->toArray() : ['Annual accounts', 'Corporation Tax']);
                                    $ukServices = [
                                        'Statutory Annual Accounts (Companies House / FRS 102 1A & 105)',
                                        'Corporation Tax Return (HMRC CT600)',
                                        'Self Assessment Tax Return (HMRC SA100)',
                                        'VAT Returns (Making Tax Digital - MTD)',
                                        'Payroll & Workplace Pension (PAYE / RTI / Nest)',
                                        'Bookkeeping & Management Accounts',
                                        'Confirmation Statement (CS01) & PSC Register',
                                        'Capital Gains Tax (UK Property 60-Day Report)'
                                    ];
                                @endphp
                                <div class="services-choice-grid">
                                    @foreach($ukServices as $srv)
                                        <label class="service-check-pill">
                                            <input type="checkbox" name="services[]" value="{{ $srv }}" {{ in_array($srv, $selectedServices) ? 'checked' : '' }}>
                                            <span>{{ $srv }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="grid-3">
                                <div class="form-group">
                                    <label>Estimated Annual UK Turnover (£)</label>
                                    <select name="turnover" class="form-control">
                                        <option value="">Select turnover bracket</option>
                                        <option value="Under £50k" {{ ($payload['turnover'] ?? '') === 'Under £50k' ? 'selected' : '' }}>Under £50,000</option>
                                        <option value="£50k - £90k (VAT Threshold)" {{ ($payload['turnover'] ?? '') === '£50k - £90k (VAT Threshold)' ? 'selected' : '' }}>£50,000 – £90,000 (Near VAT Threshold)</option>
                                        <option value="£90k - £250k" {{ ($payload['turnover'] ?? '') === '£90k - £250k' ? 'selected' : '' }}>£90,000 – £250,000</option>
                                        <option value="£250k - £1m" {{ ($payload['turnover'] ?? '') === '£250k - £1m' ? 'selected' : '' }}>£250,000 – £1,000,000</option>
                                        <option value="Over £1m" {{ ($payload['turnover'] ?? '') === 'Over £1m' ? 'selected' : '' }}>Over £1,000,000</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>UK VAT Registration Status</label>
                                    <select name="vat_registered" class="form-control">
                                        <option value="Not Registered" {{ ($payload['vat_registered'] ?? '') === 'Not Registered' ? 'selected' : '' }}>Not Registered</option>
                                        <option value="VAT Registered (Standard 20%)" {{ ($payload['vat_registered'] ?? '') === 'VAT Registered (Standard 20%)' ? 'selected' : '' }}>VAT Registered (Standard 20%)</option>
                                        <option value="VAT Registered (Flat Rate Scheme)" {{ ($payload['vat_registered'] ?? '') === 'VAT Registered (Flat Rate Scheme)' ? 'selected' : '' }}>VAT Registered (Flat Rate Scheme)</option>
                                        <option value="Exceeded £90k - Needs Registration" {{ ($payload['vat_registered'] ?? '') === 'Exceeded £90k - Needs Registration' ? 'selected' : '' }}>Exceeded £90k - Needs Registration</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Staff &amp; Directors on Payroll</label>
                                    <input type="text" name="employee_count" class="form-control" placeholder="e.g. 2 directors, 3 staff" value="{{ $payload['employee_count'] ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Prior / Existing Accountant Status</label>
                                <select name="existing_accountant" class="form-control">
                                    <option value="Starting New Business" {{ ($payload['existing_accountant'] ?? '') === 'Starting New Business' ? 'selected' : '' }}>Starting New Business (No prior accountant)</option>
                                    <option value="Changing Accountant (Clearance letter needed)" {{ ($payload['existing_accountant'] ?? '') === 'Changing Accountant (Clearance letter needed)' ? 'selected' : '' }}>Changing Accountant (Professional clearance letter needed)</option>
                                    <option value="Doing Own Filing Previously" {{ ($payload['existing_accountant'] ?? '') === 'Doing Own Filing Previously' ? 'selected' : '' }}>Doing Own Filing (DIY / Software)</option>
                                </select>
                            </div>

                        @elseif($order === 3)
                            <!-- STAGE 3: Consultation & quote -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Preferred Consultation Format</label>
                                    <select name="consultation_format" class="form-control">
                                        <option value="Microsoft Teams / Zoom Call" {{ ($payload['consultation_format'] ?? '') === 'Microsoft Teams / Zoom Call' ? 'selected' : '' }}>Microsoft Teams / Zoom Video Call</option>
                                        <option value="Telephone Consultation" {{ ($payload['consultation_format'] ?? '') === 'Telephone Consultation' ? 'selected' : '' }}>Telephone Consultation</option>
                                        <option value="In-person Clearview Office" {{ ($payload['consultation_format'] ?? '') === 'In-person Clearview Office' ? 'selected' : '' }}>In-person at Clearview Office (London)</option>
                                        <option value="Written Email Consultation" {{ ($payload['consultation_format'] ?? '') === 'Written Email Consultation' ? 'selected' : '' }}>Written Email Scope &amp; Direct Quote</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Preferred Consultation Time Slot</label>
                                    <input type="text" name="preferred_datetime" class="form-control" placeholder="e.g. Tuesdays 10:00 AM - 12:00 PM" value="{{ $payload['preferred_datetime'] ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Specific UK Tax Priorities &amp; Discussion Topics</label>
                                <input type="text" name="tax_priorities" class="form-control" placeholder="e.g. Director salary (£12,570) vs dividend strategy, R&D tax credits and allowable travel expenses" value="{{ $payload['tax_priorities'] ?? '' }}">
                            </div>
                            <div class="grid-3">
                                <div class="form-group">
                                    <label>Agreed Fee Structure</label>
                                    <select name="billing_preference" class="form-control">
                                        <option value="Monthly Direct Debit Retainer" {{ ($payload['billing_preference'] ?? '') === 'Monthly Direct Debit Retainer' ? 'selected' : '' }}>Monthly Direct Debit Retainer</option>
                                        <option value="Annual Lump Sum (5% Discount)" {{ ($payload['billing_preference'] ?? '') === 'Annual Lump Sum (5% Discount)' ? 'selected' : '' }}>Annual Lump Sum (5% Discount)</option>
                                        <option value="Quarterly Invoices" {{ ($payload['billing_preference'] ?? '') === 'Quarterly Invoices' ? 'selected' : '' }}>Quarterly Invoices</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Quoted Annual Fee (£ + VAT)</label>
                                    <input type="text" name="target_fee" class="form-control" placeholder="e.g. £1,860/yr + VAT (£155/mo)" value="{{ $payload['target_fee'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Quote Reference &amp; Status</label>
                                    <input type="text" name="quote_status" class="form-control" placeholder="e.g. Quote Q-4417 Accepted Online" value="{{ $payload['quote_status'] ?? 'Quote Accepted Online' }}">
                                </div>
                            </div>

                        @elseif($order === 4)
                            <!-- STAGE 4: Registration -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Companies House Registration Number (CRN)</label>
                                    <input type="text" name="crn" class="form-control" placeholder="e.g. 10482910 or SC123456" value="{{ $payload['crn'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Incorporation Date</label>
                                    <input type="date" name="incorporation_date" class="form-control" value="{{ $payload['incorporation_date'] ?? '' }}">
                                </div>
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>UK Jurisdiction</label>
                                    <select name="jurisdiction" class="form-control">
                                        <option value="England & Wales" {{ ($payload['jurisdiction'] ?? '') === 'England & Wales' ? 'selected' : '' }}>England &amp; Wales</option>
                                        <option value="Scotland" {{ ($payload['jurisdiction'] ?? '') === 'Scotland' ? 'selected' : '' }}>Scotland</option>
                                        <option value="Northern Ireland" {{ ($payload['jurisdiction'] ?? '') === 'Northern Ireland' ? 'selected' : '' }}>Northern Ireland</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Standard Industrial Classification (SIC Code)</label>
                                    <input type="text" name="sic_code" class="form-control" placeholder="e.g. 62020 - Information technology consultancy" value="{{ $payload['sic_code'] ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>UK Registered Office Address &amp; Postcode</label>
                                <input type="text" name="registered_address" class="form-control" placeholder="e.g. 124 City Road, London, EC1V 2NX" value="{{ $payload['registered_address'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label>Company Directors &amp; Persons with Significant Control (PSC)</label>
                                <input type="text" name="directors" class="form-control" placeholder="e.g. John Smith (Director), Sarah Smith (25%+ Shareholder / PSC)" value="{{ $payload['directors'] ?? '' }}">
                            </div>

                        @elseif($order === 5)
                            <!-- STAGE 5: AML & compliance (UK MLR 2017 & 2019) -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Photographic Identity Verification Document</label>
                                    <select name="id_type" class="form-control">
                                        <option value="UK Passport" {{ ($payload['id_type'] ?? '') === 'UK Passport' ? 'selected' : '' }}>UK Passport</option>
                                        <option value="UK Photocard Driving Licence" {{ ($payload['id_type'] ?? '') === 'UK Photocard Driving Licence' ? 'selected' : '' }}>UK Photocard Driving Licence</option>
                                        <option value="Biometric Residence Permit (BRP)" {{ ($payload['id_type'] ?? '') === 'Biometric Residence Permit (BRP)' ? 'selected' : '' }}>Biometric Residence Permit (BRP)</option>
                                        <option value="EU / International Passport" {{ ($payload['id_type'] ?? '') === 'EU / International Passport' ? 'selected' : '' }}>EU / International Passport</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Document / Passport Number</label>
                                    <input type="text" name="id_number" class="form-control" placeholder="e.g. 509283719" value="{{ $payload['id_number'] ?? '' }}">
                                </div>
                            </div>
                            <div class="grid-3">
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="dob" class="form-control" value="{{ $payload['dob'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>National Insurance (NI) Number</label>
                                    <input type="text" name="ni_number" class="form-control" placeholder="e.g. QQ 12 34 56 A" value="{{ $payload['ni_number'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Nationality &amp; UK Tax Residence</label>
                                    <input type="text" name="nationality" class="form-control" placeholder="e.g. British / UK Tax Resident" value="{{ $payload['nationality'] ?? 'British' }}">
                                </div>
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Proof of Address Document (Last 3 Months)</label>
                                    <input type="text" name="proof_of_address" class="form-control" placeholder="e.g. Barclays Bank statement / Council Tax bill" value="{{ $payload['proof_of_address'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Politically Exposed Person (PEP) / Sanctions Check</label>
                                    <select name="pep_check" class="form-control">
                                        <option value="Confirmed NOT a PEP (Standard Risk)" {{ ($payload['pep_check'] ?? '') === 'Confirmed NOT a PEP (Standard Risk)' ? 'selected' : '' }}>Confirmed NOT a Politically Exposed Person (Standard Risk)</option>
                                        <option value="PEP or Associate (Requires MLRO Review)" {{ ($payload['pep_check'] ?? '') === 'PEP or Associate (Requires MLRO Review)' ? 'selected' : '' }}>PEP or Associate (Requires MLRO Enhanced Due Diligence)</option>
                                    </select>
                                </div>
                            </div>

                        @elseif($order === 6)
                            <!-- STAGE 6: Engagement (Letters of Engagement under ICAEW / ACCA) -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Authorised Signatory Name</label>
                                    <input type="text" name="signatory_name" class="form-control" placeholder="e.g. John Smith" value="{{ $payload['signatory_name'] ?? $selectedLead->full_name }}">
                                </div>
                                <div class="form-group">
                                    <label>Signatory Designation / Capacity</label>
                                    <input type="text" name="signatory_title" class="form-control" placeholder="e.g. Managing Director / Partner" value="{{ $payload['signatory_title'] ?? 'Managing Director' }}">
                                </div>
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Accounting Reference Date (Financial Year End)</label>
                                    <input type="text" name="year_end" class="form-control" placeholder="e.g. 31 March or 30 April" value="{{ $payload['year_end'] ?? '31 March' }}">
                                </div>
                                <div class="form-group">
                                    <label>Professional Clearance Status from Prior Firm</label>
                                    <select name="clearance_status" class="form-control">
                                        <option value="Clearance Letter Received" {{ ($payload['clearance_status'] ?? '') === 'Clearance Letter Received' ? 'selected' : '' }}>Clearance Letter Received</option>
                                        <option value="Clearance Request Sent" {{ ($payload['clearance_status'] ?? '') === 'Clearance Request Sent' ? 'selected' : '' }}>Clearance Request Sent to Prior Firm</option>
                                        <option value="Not Applicable (First year company)" {{ ($payload['clearance_status'] ?? '') === 'Not Applicable (First year company)' ? 'selected' : '' }}>Not Applicable (New Business / First year)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Electronic Signature (E-Sign) Status</label>
                                <select name="esign_confirmed" class="form-control">
                                    <option value="Agreed & Signed Electronically" {{ ($payload['esign_confirmed'] ?? '') === 'Agreed & Signed Electronically' ? 'selected' : '' }}>Agreed &amp; Signed Electronically (Full Terms of Engagement)</option>
                                    <option value="Pending Director Signature" {{ ($payload['esign_confirmed'] ?? '') === 'Pending Director Signature' ? 'selected' : '' }}>Pending Director Signature</option>
                                </select>
                            </div>

                        @elseif($order === 7)
                            <!-- STAGE 7: Payment (No payment gateway - table record with UK payment methods) -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="BACS / UK Bank Transfer (Faster Payments)" {{ ($payload['payment_method'] ?? '') === 'BACS / UK Bank Transfer (Faster Payments)' ? 'selected' : '' }}>BACS / UK Bank Transfer (Faster Payments)</option>
                                        <option value="UPI (Instant Transfer)" {{ ($payload['payment_method'] ?? '') === 'UPI (Instant Transfer)' ? 'selected' : '' }}>UPI (Instant Transfer)</option>
                                        <option value="Cash (Paid at Branch Office)" {{ ($payload['payment_method'] ?? '') === 'Cash (Paid at Branch Office)' ? 'selected' : '' }}>Cash (Over the counter at Branch Office)</option>
                                        <option value="Direct Debit Mandate" {{ ($payload['payment_method'] ?? '') === 'Direct Debit Mandate' ? 'selected' : '' }}>Direct Debit Mandate (GoCardless / Standing Order)</option>
                                        <option value="Corporate Debit / Credit Card" {{ ($payload['payment_method'] ?? '') === 'Corporate Debit / Credit Card' ? 'selected' : '' }}>Corporate Debit / Credit Card</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Amount Paid / Agreed (£ including 20% UK VAT)</label>
                                    <input type="text" name="amount" class="form-control" placeholder="e.g. £930.00 (50% upfront deposit)" value="{{ $payload['amount'] ?? '' }}">
                                </div>
                            </div>
                            <div class="grid-3">
                                <div class="form-group">
                                    <label>UK Invoice Reference</label>
                                    <input type="text" name="invoice_ref" class="form-control" placeholder="e.g. INV-2026-089" value="{{ $payload['invoice_ref'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Transaction / Bank / UPI Reference</label>
                                    <input type="text" name="transaction_ref" class="form-control" placeholder="e.g. BACS-84920 / UPI-98321 / Receipt #102" value="{{ $payload['transaction_ref'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Payment Date</label>
                                    <input type="date" name="payment_date" class="form-control" value="{{ $payload['payment_date'] ?? date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Payment Confirmation &amp; Reconciliation Notes</label>
                                <input type="text" name="payment_notes" class="form-control" placeholder="e.g. Transferred from Barclays business account into Clearview client account" value="{{ $payload['payment_notes'] ?? '' }}">
                            </div>

                        @elseif($order === 8)
                            <!-- STAGE 8: Document collection -->
                            <div class="form-group">
                                <label>Primary UK Accounting Software Used</label>
                                <select name="accounting_software" class="form-control">
                                    <option value="Xero" {{ ($payload['accounting_software'] ?? '') === 'Xero' ? 'selected' : '' }}>Xero Online</option>
                                    <option value="QuickBooks Online" {{ ($payload['accounting_software'] ?? '') === 'QuickBooks Online' ? 'selected' : '' }}>QuickBooks Online</option>
                                    <option value="FreeAgent" {{ ($payload['accounting_software'] ?? '') === 'FreeAgent' ? 'selected' : '' }}>FreeAgent</option>
                                    <option value="Sage Business Cloud" {{ ($payload['accounting_software'] ?? '') === 'Sage Business Cloud' ? 'selected' : '' }}>Sage Business Cloud</option>
                                    <option value="Excel / Spreadsheets (MTD Bridge)" {{ ($payload['accounting_software'] ?? '') === 'Excel / Spreadsheets (MTD Bridge)' ? 'selected' : '' }}>Excel Spreadsheets (MTD Bridge)</option>
                                    <option value="Paper Records & Invoices" {{ ($payload['accounting_software'] ?? '') === 'Paper Records & Invoices' ? 'selected' : '' }}>Paper Records &amp; Physical Receipts</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Checklist of Accounting Records Ready / Provided</label>
                                @php
                                    $providedDocs = is_array($payload['docs_list'] ?? null) ? $payload['docs_list'] : ['12 Months Business Bank Statements'];
                                    $ukDocsList = [
                                        '12 Months Business Bank Statements (CSV / PDF / Open Banking)',
                                        'Sales Invoices & Stripe / PayPal / Merchant Summaries',
                                        'Purchase Invoices & Receipts (Dext / Hubdoc / Folder)',
                                        'P60, P11D and Payroll RTI Year-End Summary',
                                        'Prior Year Statutory Accounts & CT600 Computations',
                                        'HMRC Government Gateway 64-8 Agent Authorisation Code',
                                        'VAT Return Summaries (MTD Box 1 to 9 reports)',
                                        'Director\'s Loan Account (DLA) Statements'
                                    ];
                                @endphp
                                <div class="services-choice-grid">
                                    @foreach($ukDocsList as $doc)
                                        <label class="service-check-pill">
                                            <input type="checkbox" name="docs_list[]" value="{{ $doc }}" {{ in_array($doc, $providedDocs) ? 'checked' : '' }}>
                                            <span>{{ $doc }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Secure Cloud Upload Link / Drive URL (Optional)</label>
                                <input type="text" name="drive_url" class="form-control" placeholder="e.g. OneDrive, Dropbox or Google Drive shared folder link" value="{{ $payload['drive_url'] ?? '' }}">
                            </div>

                        @elseif($order === 9)
                            <!-- STAGE 9: Clarification -->
                            <div class="form-group">
                                <label>Specific Clarifications, Missing Records or Tax Questions</label>
                                <textarea name="questions" class="form-control" rows="2" placeholder="e.g. Missing bank statement for March reconciled; director travel expenses query clarified.">{{ $payload['questions'] ?? '' }}</textarea>
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Impending Statutory HMRC / Companies House Deadlines</label>
                                    <input type="text" name="statutory_deadline" class="form-control" placeholder="e.g. Companies House: 31 Dec 2026 | HMRC CT600: 31 Mar 2027" value="{{ $payload['statutory_deadline'] ?? 'Companies House: 31 Dec 2026' }}">
                                </div>
                                <div class="form-group">
                                    <label>Clarification Resolution Status</label>
                                    <select name="clarification_status" class="form-control">
                                        <option value="All Technical Queries Answered" {{ ($payload['clarification_status'] ?? '') === 'All Technical Queries Answered' ? 'selected' : '' }}>All Technical Queries Answered</option>
                                        <option value="Awaiting Client Bank Information" {{ ($payload['clarification_status'] ?? '') === 'Awaiting Client Bank Information' ? 'selected' : '' }}>Awaiting Client Bank Information</option>
                                        <option value="Escalated to Senior Tax Partner" {{ ($payload['clarification_status'] ?? '') === 'Escalated to Senior Tax Partner' ? 'selected' : '' }}>Escalated to Senior Tax Partner</option>
                                    </select>
                                </div>
                            </div>

                        @elseif($order === 10)
                            <!-- STAGE 10: Allocation -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Assigned Practice Office / Branch</label>
                                    <input type="text" name="assigned_branch" class="form-control" value="{{ $payload['assigned_branch'] ?? 'AccountAI Clearview Practice (London)' }}">
                                </div>
                                <div class="form-group">
                                    <label>Lead Qualified Accountant</label>
                                    <select name="lead_accountant" class="form-control">
                                        <option value="Sarah Patel FCCA (Branch Manager)" {{ ($payload['lead_accountant'] ?? '') === 'Sarah Patel FCCA (Branch Manager)' ? 'selected' : '' }}>Sarah Patel FCCA (Branch Manager)</option>
                                        <option value="John Smith ACA (Senior Accountant)" {{ ($payload['lead_accountant'] ?? '') === 'John Smith ACA (Senior Accountant)' ? 'selected' : '' }}>John Smith ACA (Senior Accountant)</option>
                                        <option value="Emma Davies ATT (Tax Specialist)" {{ ($payload['lead_accountant'] ?? '') === 'Emma Davies ATT (Tax Specialist)' ? 'selected' : '' }}>Emma Davies ATT (Tax Specialist)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid-3">
                                <div class="form-group">
                                    <label>Technical Complexity</label>
                                    <select name="complexity" class="form-control">
                                        <option value="Small Company FRS 102 Section 1A" {{ ($payload['complexity'] ?? '') === 'Small Company FRS 102 Section 1A' ? 'selected' : '' }}>Small Company FRS 102 Section 1A</option>
                                        <option value="Micro-Entity FRS 105" {{ ($payload['complexity'] ?? '') === 'Micro-Entity FRS 105' ? 'selected' : '' }}>Micro-Entity FRS 105</option>
                                        <option value="Sole Trader SA100" {{ ($payload['complexity'] ?? '') === 'Sole Trader SA100' ? 'selected' : '' }}>Sole Trader SA100</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Target Filing Date</label>
                                    <input type="text" name="target_completion" class="form-control" placeholder="e.g. 15 November 2026" value="{{ $payload['target_completion'] ?? '15 November 2026' }}">
                                </div>
                                <div class="form-group">
                                    <label>Workload Priority</label>
                                    <select name="urgency" class="form-control">
                                        <option value="Standard Turnaround" {{ ($payload['urgency'] ?? '') === 'Standard Turnaround' ? 'selected' : '' }}>Standard Turnaround</option>
                                        <option value="Statutory Deadline Imminent" {{ ($payload['urgency'] ?? '') === 'Statutory Deadline Imminent' ? 'selected' : '' }}>Statutory Deadline Imminent</option>
                                        <option value="Immediate Urgent Priority" {{ ($payload['urgency'] ?? '') === 'Immediate Urgent Priority' ? 'selected' : '' }}>Immediate Urgent Priority</option>
                                    </select>
                                </div>
                            </div>

                        @elseif($order === 11)
                            <!-- STAGE 11: Processing & review -->
                            <div class="form-group">
                                <label>Accounts Production &amp; Tax Software Used</label>
                                <input type="text" name="accounts_software" class="form-control" placeholder="e.g. IRIS Accounts Production / TaxCalc / Digita" value="{{ $payload['accounts_software'] ?? 'IRIS Accounts Production & TaxCalc' }}">
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>UK Corporation Tax Reliefs Claimed</label>
                                    <input type="text" name="tax_reliefs" class="form-control" placeholder="e.g. 100% Full Expensing Capital Allowances, R&D tax relief, S455 DLA tax" value="{{ $payload['tax_reliefs'] ?? '100% Full Expensing Capital Allowances on IT equipment' }}">
                                </div>
                                <div class="form-group">
                                    <label>VAT Reconciliation Variance</label>
                                    <input type="text" name="vat_reconciliation" class="form-control" placeholder="e.g. £0.00 - Reconciled to HMRC MTD ledger" value="{{ $payload['vat_reconciliation'] ?? '£0.00 - Fully Reconciled to HMRC MTD' }}">
                                </div>
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Draft Accounts Sent to Director for Approval</label>
                                    <select name="draft_sent" class="form-control">
                                        <option value="Draft Uploaded to Portal & Sent for Approval" {{ ($payload['draft_sent'] ?? '') === 'Draft Uploaded to Portal & Sent for Approval' ? 'selected' : '' }}>Draft Uploaded to Portal &amp; Sent for Approval</option>
                                        <option value="Pending Final Adjustment" {{ ($payload['draft_sent'] ?? '') === 'Pending Final Adjustment' ? 'selected' : '' }}>Pending Final Adjustment</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Director Approval &amp; Board Minute Sign-Off</label>
                                    <select name="director_approval" class="form-control">
                                        <option value="Approved & E-Signed by Director" {{ ($payload['director_approval'] ?? '') === 'Approved & E-Signed by Director' ? 'selected' : '' }}>Approved &amp; E-Signed by Director</option>
                                        <option value="Awaiting Formal Signature" {{ ($payload['director_approval'] ?? '') === 'Awaiting Formal Signature' ? 'selected' : '' }}>Awaiting Formal Signature</option>
                                    </select>
                                </div>
                            </div>

                        @elseif($order === 12)
                            <!-- STAGE 12: Completion -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Companies House Electronic Submission Status (iXBRL)</label>
                                    <input type="text" name="companies_house_status" class="form-control" placeholder="e.g. Accepted by Companies House (Ref: CH-2026-98124)" value="{{ $payload['companies_house_status'] ?? 'Accepted by Companies House' }}">
                                </div>
                                <div class="form-group">
                                    <label>HMRC CT600 Gateway Submission Status</label>
                                    <input type="text" name="hmrc_status" class="form-control" placeholder="e.g. Accepted by HMRC Online Service (ACK-2026-004)" value="{{ $payload['hmrc_status'] ?? 'Accepted by HMRC Online Service' }}">
                                </div>
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Final Accounts Delivery Package</label>
                                    <select name="delivery_method" class="form-control">
                                        <option value="Secure Client Portal Download & PDF Email" {{ ($payload['delivery_method'] ?? '') === 'Secure Client Portal Download & PDF Email' ? 'selected' : '' }}>Secure Client Portal Download &amp; PDF Email</option>
                                        <option value="Signed Bound Paper Copies by Post" {{ ($payload['delivery_method'] ?? '') === 'Signed Bound Paper Copies by Post' ? 'selected' : '' }}>Signed Bound Paper Copies by Post</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Next Accounting Cycle Automated Reminders Active</label>
                                    <select name="reminders_schedule" class="form-control">
                                        <option value="Quarterly VAT MTD, Monthly PAYE & Annual Accounts" {{ ($payload['reminders_schedule'] ?? '') === 'Quarterly VAT MTD, Monthly PAYE & Annual Accounts' ? 'selected' : '' }}>Quarterly VAT MTD, Monthly PAYE &amp; Annual Accounts</option>
                                        <option value="Annual Statutory Accounts Only" {{ ($payload['reminders_schedule'] ?? '') === 'Annual Statutory Accounts Only' ? 'selected' : '' }}>Annual Statutory Accounts Only</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Client Feedback &amp; Review Comments</label>
                                <textarea name="feedback" class="form-control" rows="2" placeholder="e.g. Excellent proactive service, clear tax guidance and on-time statutory filing.">{{ $payload['feedback'] ?? '' }}</textarea>
                            </div>
                        @endif

                        <!-- Form Action Buttons -->
                        <div style="margin-top:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                            <div>
                                @if($order > 1)
                                    <button type="button" class="btn s sm" onclick="switchStage({{ $order - 1 }})">
                                        &larr; Stage {{ $order - 1 }}
                                    </button>
                                @endif
                            </div>
                            <div style="display:flex; gap:8px;">
                                @if($order < 12)
                                    <button type="button" class="btn s sm" onclick="switchStage({{ $order + 1 }})">
                                        Skip to Stage {{ $order + 1 }} &rarr;
                                    </button>
                                @endif
                                <button type="submit" class="btn sm">
                                    Save &amp; Submit Stage {{ $order }} &rarr;
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

<script>
function switchStage(order) {
    // Hide all stage panels
    document.querySelectorAll('.stage-panel').forEach(function(el) {
        el.style.display = 'none';
    });
    // Show selected stage panel
    const target = document.getElementById('stage-panel-' + order);
    if (target) {
        target.style.display = 'block';
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    // Update active highlight on stage pills
    document.querySelectorAll('.stage-card').forEach(function(card) {
        card.classList.remove('active');
    });
    const activePill = document.getElementById('stage-card-' + order);
    if (activePill) {
        activePill.classList.add('active');
    }
    // Update browser URL query
    try {
        const url = new URL(window.location);
        url.searchParams.set('step', order);
        window.history.pushState({}, '', url);
    } catch (e) {}
}
</script>

@endsection
