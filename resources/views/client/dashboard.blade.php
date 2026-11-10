@extends('layouts.client')

@section('title', ($selectedLead ? ($selectedLead->company_name ?: $selectedLead->full_name) . ' – ' : '') . 'Client Journey')
@section('page_title', 'Client journey')
@section('page_subtitle', $selectedLead ? (($selectedLead->company_name ?: $selectedLead->full_name) . ' · 12 connected stages') : '12 connected stages')

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
    text-decoration: none;
    cursor: pointer;
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
.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
@media(max-width: 650px) {
    .grid-2 { grid-template-columns: 1fr; }
}
.step-box {
    border: 1px solid var(--bd);
    border-radius: 12px;
    padding: 18px;
    background: #ffffff;
    transition: all .2s;
    scroll-margin-top: 20px;
}
.step-box.highlight {
    border: 2px solid var(--ac);
    box-shadow: 0 0 0 3px rgba(58,75,220,0.12);
}
.step-box.revision-box {
    border: 2px solid var(--wn);
    background: #fffefb;
}
.services-choice-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
    gap: 8px;
    margin-top: 6px;
}
.service-check-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--s2);
    border: 1px solid var(--bd);
    padding: 7px 10px;
    border-radius: 8px;
    font-size: 12.5px;
    cursor: pointer;
}
.service-check-pill:hover {
    border-color: var(--ac);
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
    <div class="card" style="text-align: center; padding: 40px 20px;">
        <h3>No Client Onboarding Cases Found</h3>
        <p style="color:var(--mu);">You do not currently have any active enquiries or onboarding workflows linked to your account.</p>
        <a href="{{ route('home') }}" class="btn">Explore Our Accountancy Services &rarr;</a>
    </div>
@else

    @if($leads->count() > 1)
        <div class="card" style="margin-bottom: 16px; padding: 12px 18px;">
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
    <div class="card" style="padding: 18px 20px; margin-bottom: 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
            <div>
                <strong style="font-size:17px;">{{ $selectedLead->company_name ?: $selectedLead->full_name }}</strong>
                <span class="tag {{ $selectedLead->status === 'converted' ? 'ok' : 'wn' }}" style="margin-left:8px;">
                    {{ ucfirst(str_replace('_', ' ', $selectedLead->status)) }}
                </span>
                <span style="font-size:12px; color:var(--mu); margin-left:8px;">Overall Progress: <b>{{ $selectedLead->progress_percentage }}%</b></span>
            </div>
            <div>
                <span style="font-size:12px; color:var(--mu);">Assigned Practice: <b>AccountAI Clearview Practice</b></span>
            </div>
        </div>

        <div class="stages-flow">
            @php
                $firstPending = $steps->firstWhere('status', '!=', 'done');
            @endphp
            @foreach($steps as $st)
                @php
                    $isDone = $st->status === 'done';
                    $isRevision = $st->status === 'needs_revision';
                    $isActive = ($currentStep && $currentStep->id === $st->id) || (!$currentStep && $firstPending && $firstPending->id === $st->id);
                    $class = $isDone ? 'done' : ($isRevision ? 'revision' : ($isActive ? 'active' : 'pending'));
                @endphp
                <a href="#stage-step-{{ $st->step_order }}" class="stage-card {{ $class }}" onclick="scrollToStage({{ $st->step_order }})">
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

    <!-- 12 Stages Parameter Forms & Status Overview (Admin Style) -->
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px; flex-wrap:wrap; gap:10px;">
            <div>
                <h3 style="margin:0;">Client Journey Stages &amp; Parameters <em>Complete all 12 stages step-by-step</em></h3>
                <p style="color:var(--mu); font-size:12.5px; margin:4px 0 0 0;">Fill in the parameters for each stage below. There are no mandatory requirements—enter whatever details you have. Your practice accountant will review and approve each stage.</p>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:16px; margin-top:16px;">
            @foreach($steps as $step)
                @php
                    $payload = is_array($step->payload) ? $step->payload : [];
                    $order = $step->step_order;
                    $isCurrent = $currentStep && $currentStep->id === $step->id;
                    $isRevision = $step->status === 'needs_revision';
                    $isDone = $step->status === 'done';
                    $isSubmitted = $step->status === 'submitted';
                @endphp

                <div id="stage-step-{{ $order }}" class="step-box {{ $isRevision ? 'revision-box' : ($isCurrent ? 'highlight' : '') }}">
                    <!-- Step Header -->
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                        <div>
                            <span class="tag {{ $isDone ? 'ok' : ($isRevision ? 'wn' : ($isSubmitted ? '' : 'mu')) }}" style="font-size:11px;">
                                Stage {{ $order }}: {{ strtoupper($step->status) }}
                            </span>
                            <strong style="font-size:15px; margin-left:8px; color:var(--tx);">{{ $step->title }}</strong>
                        </div>
                        <div>
                            @if($isDone)
                                <span style="color:var(--ok); font-weight:600; font-size:12.5px;">✓ Verified by Accountant</span>
                            @elseif($isRevision)
                                <span style="color:var(--wn); font-weight:700; font-size:12.5px;">⚠️ Revision Requested</span>
                            @elseif($isSubmitted)
                                <span style="color:var(--ac); font-weight:600; font-size:12.5px;">⌛ Submitted &amp; Awaiting Review</span>
                            @else
                                <span style="color:var(--mu); font-size:12.5px;">Pending Details</span>
                            @endif
                        </div>
                    </div>

                    <div style="font-size:12.5px; color:var(--mu); margin: 6px 0 12px 0;">
                        {{ $step->description }}
                    </div>

                    <!-- Accountant Feedback Alert (If Revision Requested) -->
                    @if($step->admin_notes)
                        <div style="background:#fff8eb; border:1px solid #fed7aa; border-radius:8px; padding:12px 14px; margin-bottom:14px;">
                            <strong style="color:#9a6f17; font-size:13px;">⚠️ Accountant Revision Note:</strong>
                            <div style="color:#78350f; font-size:13px; margin-top:4px;">{{ $step->admin_notes }}</div>
                            <small style="color:#9a6f17; display:block; margin-top:4px;">Please review the note above, update your parameters below, and click "Save &amp; Submit Stage {{ $order }}".</small>
                        </div>
                    @endif

                    <!-- Show Currently Submitted Parameters (If any exist) -->
                    @if(!empty($payload))
                        <div style="margin-bottom:12px;">
                            <span style="font-size:11px; font-weight:700; color:var(--mu); text-transform:uppercase;">Current On-File Parameters:</span>
                            <div class="parameter-grid">
                                @foreach($payload as $pk => $pv)
                                    <div class="param-item">
                                        <strong>{{ str_replace('_', ' ', $pk) }}:</strong>
                                        <span>{{ is_array($pv) ? implode(', ', $pv) : ($pv ?: '—') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- The 12-Step Parameter Form (No Required Blockers) -->
                    <div style="background:var(--s2); border:1px solid var(--bd); border-radius:10px; padding:16px; margin-top:8px;">
                        <div style="font-size:12px; font-weight:700; color:var(--mu); margin-bottom:12px; text-transform:uppercase;">
                            {{ $isDone ? 'Update Stage Parameters (Optional):' : 'Enter / Update Stage ' . $order . ' Parameters:' }}
                        </div>

                        <form method="POST" action="{{ route('client.requests.submit', $step->id) }}">
                            @csrf

                            @if($order === 1)
                                <!-- Step 1: Lead & Enquiry -->
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
                                        <label>Email Address</label>
                                        <input type="email" name="email" class="form-control" value="{{ $payload['email'] ?? $selectedLead->email }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="tel" name="phone" class="form-control" value="{{ $payload['phone'] ?? $selectedLead->phone }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Company / Sole Trader Name</label>
                                    <input type="text" name="company_name" class="form-control" value="{{ $payload['company_name'] ?? $selectedLead->company_name }}">
                                </div>
                                <div class="form-group">
                                    <label>Initial Message / Enquiry Scope</label>
                                    <textarea name="message" class="form-control" rows="2">{{ $payload['message'] ?? $selectedLead->message }}</textarea>
                                </div>

                            @elseif($order === 2)
                                <!-- Step 2: Qualification -->
                                <div class="form-group">
                                    <label>Required Accountancy Services (Select all that apply)</label>
                                    @php
                                        $selectedServices = is_array($payload['services'] ?? null) ? $payload['services'] : ($selectedLead->services ? $selectedLead->services->pluck('name')->toArray() : ['Annual accounts', 'Corporation Tax']);
                                        $availableServices = ['Annual accounts', 'Corporation Tax', 'VAT Returns', 'Payroll & Pension', 'Self Assessment', 'Bookkeeping & MTD', 'Company Secretarial'];
                                    @endphp
                                    <div class="services-choice-grid">
                                        @foreach($availableServices as $srv)
                                            <label class="service-check-pill">
                                                <input type="checkbox" name="services[]" value="{{ $srv }}" {{ in_array($srv, $selectedServices) ? 'checked' : '' }}>
                                                <span>{{ $srv }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Business Structure</label>
                                        <select name="business_structure" class="form-control">
                                            <option value="">Choose structure</option>
                                            <option value="Limited Company" {{ ($payload['business_structure'] ?? '') === 'Limited Company' ? 'selected' : '' }}>Limited Company (Ltd)</option>
                                            <option value="Sole Trader" {{ ($payload['business_structure'] ?? '') === 'Sole Trader' ? 'selected' : '' }}>Sole Trader / Self-Employed</option>
                                            <option value="Partnership" {{ ($payload['business_structure'] ?? '') === 'Partnership' ? 'selected' : '' }}>Partnership / LLP</option>
                                            <option value="Landlord" {{ ($payload['business_structure'] ?? '') === 'Landlord' ? 'selected' : '' }}>Property Landlord</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Estimated Annual Turnover (£)</label>
                                        <input type="text" name="turnover" class="form-control" placeholder="e.g. £150,000" value="{{ $payload['turnover'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Number of Employees</label>
                                        <input type="text" name="employee_count" class="form-control" placeholder="e.g. 1-5" value="{{ $payload['employee_count'] ?? '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label>VAT Registered?</label>
                                        <select name="vat_registered" class="form-control">
                                            <option value="No" {{ ($payload['vat_registered'] ?? '') === 'No' ? 'selected' : '' }}>No</option>
                                            <option value="Yes" {{ ($payload['vat_registered'] ?? '') === 'Yes' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                </div>

                            @elseif($order === 3)
                                <!-- Step 3: Consultation & Quote -->
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Preferred Consultation Format</label>
                                        <select name="consultation_format" class="form-control">
                                            <option value="Telephone Call" {{ ($payload['consultation_format'] ?? '') === 'Telephone Call' ? 'selected' : '' }}>Telephone Call</option>
                                            <option value="Zoom / Video Conference" {{ ($payload['consultation_format'] ?? '') === 'Zoom / Video Conference' ? 'selected' : '' }}>Zoom / Google Meet Video Call</option>
                                            <option value="In-person Clearview Office" {{ ($payload['consultation_format'] ?? '') === 'In-person Clearview Office' ? 'selected' : '' }}>In-person at Branch Office</option>
                                            <option value="Email Discussion" {{ ($payload['consultation_format'] ?? '') === 'Email Discussion' ? 'selected' : '' }}>Email Summary &amp; Direct Quote</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Preferred Date &amp; Time Slot</label>
                                        <input type="text" name="preferred_datetime" class="form-control" placeholder="e.g. Weekdays 2:00 PM - 5:00 PM" value="{{ $payload['preferred_datetime'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Fee Billing Preference</label>
                                        <select name="billing_preference" class="form-control">
                                            <option value="Monthly Direct Debit" {{ ($payload['billing_preference'] ?? '') === 'Monthly Direct Debit' ? 'selected' : '' }}>Monthly Retainer</option>
                                            <option value="Annual Lump Sum" {{ ($payload['billing_preference'] ?? '') === 'Annual Lump Sum' ? 'selected' : '' }}>Annual Lump Sum (5% Discount)</option>
                                            <option value="Quarterly Invoices" {{ ($payload['billing_preference'] ?? '') === 'Quarterly Invoices' ? 'selected' : '' }}>Quarterly Invoices</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Expected Target Annual Fee (£)</label>
                                        <input type="text" name="target_fee" class="form-control" placeholder="e.g. £1,800/yr" value="{{ $payload['target_fee'] ?? '' }}">
                                    </div>
                                </div>

                            @elseif($order === 4)
                                <!-- Step 4: Registration -->
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Companies House Number (CRN)</label>
                                        <input type="text" name="crn" class="form-control" placeholder="e.g. 12345678" value="{{ $payload['crn'] ?? '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Jurisdiction / Country</label>
                                        <select name="jurisdiction" class="form-control">
                                            <option value="England & Wales" {{ ($payload['jurisdiction'] ?? '') === 'England & Wales' ? 'selected' : '' }}>England &amp; Wales</option>
                                            <option value="Scotland" {{ ($payload['jurisdiction'] ?? '') === 'Scotland' ? 'selected' : '' }}>Scotland</option>
                                            <option value="Northern Ireland" {{ ($payload['jurisdiction'] ?? '') === 'Northern Ireland' ? 'selected' : '' }}>Northern Ireland</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Registered Office Address</label>
                                    <input type="text" name="registered_address" class="form-control" placeholder="Full street address and postcode" value="{{ $payload['registered_address'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Directors &amp; Significant Beneficial Owners</label>
                                    <input type="text" name="directors" class="form-control" placeholder="Names of all active company directors" value="{{ $payload['directors'] ?? '' }}">
                                </div>

                            @elseif($order === 5)
                                <!-- Step 5: AML & Compliance -->
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Primary ID Type</label>
                                        <select name="id_type" class="form-control">
                                            <option value="UK Passport" {{ ($payload['id_type'] ?? '') === 'UK Passport' ? 'selected' : '' }}>UK Passport</option>
                                            <option value="UK Driving Licence" {{ ($payload['id_type'] ?? '') === 'UK Driving Licence' ? 'selected' : '' }}>UK Driving Licence</option>
                                            <option value="EU / International Passport" {{ ($payload['id_type'] ?? '') === 'EU / International Passport' ? 'selected' : '' }}>International Passport</option>
                                            <option value="National ID Card" {{ ($payload['id_type'] ?? '') === 'National ID Card' ? 'selected' : '' }}>National Identity Card</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>ID / Passport Number</label>
                                        <input type="text" name="id_number" class="form-control" placeholder="e.g. 509283719" value="{{ $payload['id_number'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Date of Birth</label>
                                        <input type="date" name="dob" class="form-control" value="{{ $payload['dob'] ?? '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Nationality &amp; Tax Residency</label>
                                        <input type="text" name="nationality" class="form-control" placeholder="e.g. British / UK Tax Resident" value="{{ $payload['nationality'] ?? 'British' }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Politically Exposed Person (PEP) or Sanctions Match?</label>
                                    <select name="pep_check" class="form-control">
                                        <option value="No" {{ ($payload['pep_check'] ?? '') === 'No' ? 'selected' : '' }}>No (Standard Risk)</option>
                                        <option value="Yes" {{ ($payload['pep_check'] ?? '') === 'Yes' ? 'selected' : '' }}>Yes (Requires Compliance Officer Review)</option>
                                    </select>
                                </div>

                            @elseif($order === 6)
                                <!-- Step 6: Engagement -->
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Authorised Signatory Name</label>
                                        <input type="text" name="signatory_name" class="form-control" placeholder="e.g. Tarun Birla" value="{{ $payload['signatory_name'] ?? $selectedLead->full_name }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Position in Business</label>
                                        <input type="text" name="signatory_title" class="form-control" placeholder="e.g. Managing Director" value="{{ $payload['signatory_title'] ?? 'Director' }}">
                                    </div>
                                </div>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Financial Year End Date</label>
                                        <input type="text" name="year_end" class="form-control" placeholder="e.g. 31 March" value="{{ $payload['year_end'] ?? '31 March' }}">
                                    </div>
                                    <div class="form-group">
                                        <label>E-Signature Agreement Confirmation</label>
                                        <select name="esign_confirmed" class="form-control">
                                            <option value="Agreed & Signed" {{ ($payload['esign_confirmed'] ?? '') === 'Agreed & Signed' ? 'selected' : '' }}>Agreed &amp; Signed Electronically</option>
                                            <option value="Pending Review" {{ ($payload['esign_confirmed'] ?? '') === 'Pending Review' ? 'selected' : '' }}>Pending Review of Terms</option>
                                        </select>
                                    </div>
                                </div>

                            @elseif($order === 7)
                                <!-- Step 7: Payment (No payment gateway - table record with Cash, UPI, Bank Transfer) -->
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Payment Method</label>
                                        <select name="payment_method" class="form-control">
                                            <option value="UPI" {{ ($payload['payment_method'] ?? '') === 'UPI' ? 'selected' : '' }}>UPI (Instant Transfer)</option>
                                            <option value="Cash" {{ ($payload['payment_method'] ?? '') === 'Cash' ? 'selected' : '' }}>Cash (At Branch Office)</option>
                                            <option value="Bank Transfer / BACS" {{ ($payload['payment_method'] ?? '') === 'Bank Transfer / BACS' ? 'selected' : '' }}>Bank Transfer / BACS</option>
                                            <option value="Direct Debit" {{ ($payload['payment_method'] ?? '') === 'Direct Debit' ? 'selected' : '' }}>Direct Debit Mandate</option>
                                            <option value="Cheque" {{ ($payload['payment_method'] ?? '') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Amount Paid / Agreed (£)</label>
                                        <input type="text" name="amount" class="form-control" placeholder="e.g. £450.00 deposit" value="{{ $payload['amount'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Transaction ID / UPI Reference / Receipt No</label>
                                        <input type="text" name="transaction_ref" class="form-control" placeholder="e.g. UPI-9823412 or Branch Cash Receipt #102" value="{{ $payload['transaction_ref'] ?? '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Payment Date</label>
                                        <input type="date" name="payment_date" class="form-control" value="{{ $payload['payment_date'] ?? date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Payment Confirmation Notes</label>
                                    <input type="text" name="payment_notes" class="form-control" placeholder="e.g. Paid via UPI / Handed cash to receptionist" value="{{ $payload['payment_notes'] ?? '' }}">
                                </div>

                            @elseif($order === 8)
                                <!-- Step 8: Document Collection -->
                                <div class="form-group">
                                    <label>Documents Provided / Ready for Review</label>
                                    @php
                                        $providedDocs = is_array($payload['docs_list'] ?? null) ? $payload['docs_list'] : ['Bank statements'];
                                        $standardDocs = ['Bank statements (Last 12 Months)', 'Sales invoices & sales ledger', 'Purchase invoices & receipts', 'Payroll summary / P60', 'Previous annual accounts', 'VAT return copies', 'Director loan statements'];
                                    @endphp
                                    <div class="services-choice-grid">
                                        @foreach($standardDocs as $doc)
                                            <label class="service-check-pill">
                                                <input type="checkbox" name="docs_list[]" value="{{ $doc }}" {{ in_array($doc, $providedDocs) ? 'checked' : '' }}>
                                                <span>{{ $doc }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Primary Accounting Software Used</label>
                                        <select name="accounting_software" class="form-control">
                                            <option value="Xero" {{ ($payload['accounting_software'] ?? '') === 'Xero' ? 'selected' : '' }}>Xero</option>
                                            <option value="QuickBooks Online" {{ ($payload['accounting_software'] ?? '') === 'QuickBooks Online' ? 'selected' : '' }}>QuickBooks Online</option>
                                            <option value="FreeAgent" {{ ($payload['accounting_software'] ?? '') === 'FreeAgent' ? 'selected' : '' }}>FreeAgent</option>
                                            <option value="Sage" {{ ($payload['accounting_software'] ?? '') === 'Sage' ? 'selected' : '' }}>Sage</option>
                                            <option value="Excel / Spreadsheets" {{ ($payload['accounting_software'] ?? '') === 'Excel / Spreadsheets' ? 'selected' : '' }}>Excel / Google Sheets</option>
                                            <option value="Paper Records" {{ ($payload['accounting_software'] ?? '') === 'Paper Records' ? 'selected' : '' }}>Paper Records</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Cloud Storage Link / Drive URL (Optional)</label>
                                        <input type="text" name="drive_url" class="form-control" placeholder="e.g. Google Drive / Dropbox link" value="{{ $payload['drive_url'] ?? '' }}">
                                    </div>
                                </div>

                            @elseif($order === 9)
                                <!-- Step 9: Clarification -->
                                <div class="form-group">
                                    <label>Specific Questions or Unclear Items for Accountant</label>
                                    <textarea name="questions" class="form-control" rows="2" placeholder="e.g. Have a query on vehicle capital allowances or missing statement for month of March">{{ $payload['questions'] ?? '' }}</textarea>
                                </div>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Are There Any Missing Accounting Periods?</label>
                                        <input type="text" name="missing_periods" class="form-control" placeholder="e.g. None or Missing Q2 bank statements" value="{{ $payload['missing_periods'] ?? 'None' }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Immediate Statutory Filing Deadline</label>
                                        <input type="text" name="statutory_deadline" class="form-control" placeholder="e.g. 31 Dec 2026" value="{{ $payload['statutory_deadline'] ?? '' }}">
                                    </div>
                                </div>

                            @elseif($order === 10)
                                <!-- Step 10: Allocation -->
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Preferred Accountancy Team / Seniority</label>
                                        <select name="team_preference" class="form-control">
                                            <option value="Branch Manager Review" {{ ($payload['team_preference'] ?? '') === 'Branch Manager Review' ? 'selected' : '' }}>Branch Manager (Sarah Patel)</option>
                                            <option value="Senior Tax Accountant" {{ ($payload['team_preference'] ?? '') === 'Senior Tax Accountant' ? 'selected' : '' }}>Senior Tax Accountant</option>
                                            <option value="General Practice Team" {{ ($payload['team_preference'] ?? '') === 'General Practice Team' ? 'selected' : '' }}>General Practice Team</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Key Contact Person at Your Business</label>
                                        <input type="text" name="primary_contact" class="form-control" placeholder="e.g. Tarun Birla (Director)" value="{{ $payload['primary_contact'] ?? $selectedLead->full_name }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Case Urgency Priority</label>
                                    <select name="urgency" class="form-control">
                                        <option value="Standard (Routine schedule)" {{ ($payload['urgency'] ?? '') === 'Standard (Routine schedule)' ? 'selected' : '' }}>Standard (Routine schedule)</option>
                                        <option value="Urgent (Deadline within 30 days)" {{ ($payload['urgency'] ?? '') === 'Urgent (Deadline within 30 days)' ? 'selected' : '' }}>Urgent (Deadline within 30 days)</option>
                                        <option value="Critical Emergency" {{ ($payload['urgency'] ?? '') === 'Critical Emergency' ? 'selected' : '' }}>Critical Emergency (Penalties Pending)</option>
                                    </select>
                                </div>

                            @elseif($order === 11)
                                <!-- Step 11: Processing & Review -->
                                <div class="form-group">
                                    <label>Special Notes for Technical Preparation</label>
                                    <textarea name="prep_notes" class="form-control" rows="2" placeholder="e.g. Please claim research & development allowances or home office deductions">{{ $payload['prep_notes'] ?? '' }}</textarea>
                                </div>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Director Loan Account Overdrawn?</label>
                                        <select name="director_loan" class="form-control">
                                            <option value="No" {{ ($payload['director_loan'] ?? '') === 'No' ? 'selected' : '' }}>No</option>
                                            <option value="Yes" {{ ($payload['director_loan'] ?? '') === 'Yes' ? 'selected' : '' }}>Yes (Please calculate S455 tax)</option>
                                            <option value="Not Sure" {{ ($payload['director_loan'] ?? '') === 'Not Sure' ? 'selected' : '' }}>Not Sure (Accountant to check)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Review Sign-Off Ready?</label>
                                        <select name="review_ready" class="form-control">
                                            <option value="Yes, Ready" {{ ($payload['review_ready'] ?? '') === 'Yes, Ready' ? 'selected' : '' }}>Yes, Ready for Final Accountant Review</option>
                                            <option value="More Information Pending" {{ ($payload['review_ready'] ?? '') === 'More Information Pending' ? 'selected' : '' }}>More Information Pending</option>
                                        </select>
                                    </div>
                                </div>

                            @elseif($order === 12)
                                <!-- Step 12: Completion -->
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Final Accounts Delivery Preference</label>
                                        <select name="delivery_method" class="form-control">
                                            <option value="Client Portal Download & Secure Email" {{ ($payload['delivery_method'] ?? '') === 'Client Portal Download & Secure Email' ? 'selected' : '' }}>Portal Download &amp; Secure Email (PDF)</option>
                                            <option value="Signed Bound Paper Copies by Post" {{ ($payload['delivery_method'] ?? '') === 'Signed Bound Paper Copies by Post' ? 'selected' : '' }}>Signed Paper Copies by Post</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Next-Cycle Automated Reminders</label>
                                        <select name="reminders_schedule" class="form-control">
                                            <option value="Quarterly VAT & Annual Accounts" {{ ($payload['reminders_schedule'] ?? '') === 'Quarterly VAT & Annual Accounts' ? 'selected' : '' }}>Quarterly VAT &amp; Annual Accounts</option>
                                            <option value="Annual Only" {{ ($payload['reminders_schedule'] ?? '') === 'Annual Only' ? 'selected' : '' }}>Annual Accounts Only</option>
                                            <option value="Monthly Payroll & Pension" {{ ($payload['reminders_schedule'] ?? '') === 'Monthly Payroll & Pension' ? 'selected' : '' }}>Monthly Payroll &amp; Pension</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Client Feedback &amp; Final Comments</label>
                                    <textarea name="feedback" class="form-control" rows="2" placeholder="e.g. Excellent onboarding, all documents submitted">{{ $payload['feedback'] ?? '' }}</textarea>
                                </div>
                            @endif

                            <div style="margin-top:12px; display:flex; justify-content:flex-end;">
                                <button type="submit" class="btn sm">
                                    Save &amp; Submit Stage {{ $order }} &rarr;
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<script>
function scrollToStage(stepOrder) {
    const el = document.getElementById('stage-step-' + stepOrder);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.querySelectorAll('.step-box').forEach(b => b.classList.remove('highlight'));
        el.classList.add('highlight');
    }
}
</script>

@endsection
