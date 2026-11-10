@extends('layouts.client')

@section('title', 'Client Dashboard – AccountAI')
@section('page_title', 'Client Dashboard')
@section('page_subtitle', 'Welcome back, ' . $user->name . ' · ' . ($user->company ?: 'Personal Account'))

@push('styles')
<style>
.stages-flow {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 12px;
    margin-bottom: 20px;
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
.form-section {
    background: var(--sf);
    border: 1px solid var(--bd);
    border-radius: 14px;
    padding: 22px;
    margin-top: 10px;
}
.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}
@media(max-width: 650px) {
    .grid-2 { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="hero" style="margin-bottom: 20px;">
    <div style="flex:1;">
        <h2>Welcome to your AccountAI Practice Portal</h2>
        <p>Complete your 12 onboarding stages step-by-step. Your assigned accountant reviews each step and guides your company filing.</p>
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
                <span style="font-weight:600; font-size:13px; color:var(--mu);">Select Active Case:</span>
                @foreach($leads as $l)
                    <a href="{{ route('client.dashboard', ['lead_id' => $l->id]) }}" class="btn sm {{ $selectedLead->id == $l->id ? '' : 's' }}">
                        #{{ $l->id }} · {{ $l->company_name ?: $l->full_name }} ({{ $l->created_at->format('M Y') }})
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 12 Connected Stages Horizontal Flow (Screenshot Design Match) -->
    <div class="card" style="padding: 18px 20px; margin-bottom: 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
            <div>
                <strong style="font-size:16px;">{{ $selectedLead->company_name ?: $selectedLead->full_name }}</strong>
                <span class="tag {{ $selectedLead->status === 'converted' ? 'ok' : 'wn' }}" style="margin-left:8px;">
                    {{ ucfirst(str_replace('_', ' ', $selectedLead->status)) }}
                </span>
                <span style="font-size:12px; color:var(--mu); margin-left:8px;">Overall Progress: <b>{{ $selectedLead->progress_percentage }}%</b></span>
            </div>
            <div>
                <small style="color:var(--mu);">Click any stage pill below to view or update its details</small>
            </div>
        </div>

        <div class="stages-flow">
            @foreach($steps as $st)
                @php
                    $isDone = $st->status === 'done';
                    $isRevision = $st->status === 'needs_revision';
                    $isSelected = $currentStep && $currentStep->id === $st->id;
                    $class = $isDone ? 'done' : ($isRevision ? 'revision' : ($isSelected ? 'active' : 'pending'));
                @endphp
                <a href="{{ route('client.dashboard', ['lead_id' => $selectedLead->id, 'step' => $st->step_order]) }}" class="stage-card {{ $class }}">
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

    <!-- Active Step Form & Details -->
    @if($currentStep)
        @php
            $payload = is_array($currentStep->payload) ? $currentStep->payload : [];
            $order = $currentStep->step_order;
        @endphp

        <div class="g g2">
            <!-- Left: Step Form -->
            <div>
                <div class="form-section">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <div>
                            <span class="tag {{ $currentStep->status === 'done' ? 'ok' : ($currentStep->status === 'needs_revision' ? 'wn' : ($currentStep->status === 'submitted' ? '' : 'mu')) }}" style="font-size:11px;">
                                Stage {{ $order }} of 12: {{ strtoupper($currentStep->status) }}
                            </span>
                            <h3 style="margin: 8px 0 4px 0; font-size:18px;">{{ $currentStep->title }}</h3>
                        </div>
                    </div>
                    <p style="color:var(--mu); font-size:13px; margin:0 0 16px 0;">{{ $currentStep->description }}</p>

                    <!-- Admin Feedback Notice if Revision Needed -->
                    @if($currentStep->admin_notes)
                        <div style="background:#fff8eb; border:1px solid #fed7aa; border-radius:10px; padding:12px 14px; margin-bottom:16px;">
                            <strong style="color:#9a6f17; font-size:13px;">⚠️ Accountant Revision Note:</strong>
                            <div style="color:#78350f; font-size:13px; margin-top:4px;">{{ $currentStep->admin_notes }}</div>
                            <small style="color:#9a6f17; display:block; margin-top:4px;">Please update the parameters below and re-submit.</small>
                        </div>
                    @endif

                    @if($currentStep->status === 'done')
                        <div class="note ok" style="margin-bottom:16px;">
                            ✓ <strong>Approved:</strong> This stage has been reviewed and verified by your accountant.
                        </div>
                    @endif

                    <!-- The 12-Step Form (No Required Blocks - User can fill whatever they have) -->
                    <form method="POST" action="{{ route('client.requests.submit', $currentStep->id) }}">
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
                                <label>Company / Trading Entity</label>
                                <input type="text" name="company_name" class="form-control" value="{{ $payload['company_name'] ?? $selectedLead->company_name }}">
                            </div>

                        @elseif($order === 2)
                            <!-- Step 2: Qualification -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Estimated Annual Turnover (£)</label>
                                    <input type="text" name="turnover" class="form-control" placeholder="e.g. £85,000 or £250,000" value="{{ $payload['turnover'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Accounting Software Used</label>
                                    <select name="accounting_software" class="form-control">
                                        <option value="">-- Select or Leave Blank --</option>
                                        @foreach(['Xero', 'QuickBooks', 'FreeAgent', 'Sage', 'Excel / Spreadsheets', 'None / Paper records'] as $soft)
                                            <option value="{{ $soft }}" {{ ($payload['accounting_software'] ?? '') === $soft ? 'selected' : '' }}>{{ $soft }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Number of Employees / Directors</label>
                                    <input type="text" name="employee_count" class="form-control" placeholder="e.g. 1-5 employees" value="{{ $payload['employee_count'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Current VAT Registration</label>
                                    <select name="vat_status" class="form-control">
                                        <option value="">-- Select VAT Status --</option>
                                        <option value="Registered" {{ ($payload['vat_status'] ?? '') === 'Registered' ? 'selected' : '' }}>VAT Registered</option>
                                        <option value="Not Registered" {{ ($payload['vat_status'] ?? '') === 'Not Registered' ? 'selected' : '' }}>Not Registered</option>
                                        <option value="Applying Soon" {{ ($payload['vat_status'] ?? '') === 'Applying Soon' ? 'selected' : '' }}>Planning to Register</option>
                                    </select>
                                </div>
                            </div>

                        @elseif($order === 3)
                            <!-- Step 3: Consultation & quote -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Preferred Billing Schedule</label>
                                    <select name="billing_schedule" class="form-control">
                                        <option value="">-- Choose Option --</option>
                                        <option value="Monthly Direct Debit" {{ ($payload['billing_schedule'] ?? '') === 'Monthly Direct Debit' ? 'selected' : '' }}>Monthly Direct Debit</option>
                                        <option value="Annual Upfront" {{ ($payload['billing_schedule'] ?? '') === 'Annual Upfront' ? 'selected' : '' }}>Annual Upfront</option>
                                        <option value="Per Service Filing" {{ ($payload['billing_schedule'] ?? '') === 'Per Service Filing' ? 'selected' : '' }}>Per Service Filing</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Preferred Consultation Channel</label>
                                    <select name="consultation_channel" class="form-control">
                                        <option value="">-- Choose Format --</option>
                                        <option value="Video Call (Zoom/Teams)" {{ ($payload['consultation_channel'] ?? '') === 'Video Call (Zoom/Teams)' ? 'selected' : '' }}>Video Call (Zoom / Teams)</option>
                                        <option value="Phone Call" {{ ($payload['consultation_channel'] ?? '') === 'Phone Call' ? 'selected' : '' }}>Phone Call</option>
                                        <option value="Email Discussion" {{ ($payload['consultation_channel'] ?? '') === 'Email Discussion' ? 'selected' : '' }}>Email Discussion</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Target Onboarding Date</label>
                                <input type="text" name="target_date" class="form-control" placeholder="e.g. Immediate / Next Month" value="{{ $payload['target_date'] ?? '' }}">
                            </div>

                        @elseif($order === 4)
                            <!-- Step 4: Registration -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Companies House Registration Number (CRN)</label>
                                    <input type="text" name="company_number" class="form-control" placeholder="e.g. 12345678" value="{{ $payload['company_number'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Director UTR / NINO</label>
                                    <input type="text" name="director_utr" class="form-control" placeholder="10-digit UTR or National Insurance No" value="{{ $payload['director_utr'] ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Registered Office Address</label>
                                <textarea name="office_address" class="form-control" rows="2" placeholder="Full postal address in UK...">{{ $payload['office_address'] ?? '' }}</textarea>
                            </div>

                        @elseif($order === 5)
                            <!-- Step 5: AML & compliance -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>ID Document Type</label>
                                    <select name="id_type" class="form-control">
                                        <option value="">-- Select ID --</option>
                                        <option value="UK Passport" {{ ($payload['id_type'] ?? '') === 'UK Passport' ? 'selected' : '' }}>UK Passport</option>
                                        <option value="Full UK Driving Licence" {{ ($payload['id_type'] ?? '') === 'Full UK Driving Licence' ? 'selected' : '' }}>Full UK Driving Licence</option>
                                        <option value="EU / International Passport" {{ ($payload['id_type'] ?? '') === 'EU / International Passport' ? 'selected' : '' }}>EU / International Passport</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>ID Document Number</label>
                                    <input type="text" name="id_number" class="form-control" placeholder="Passport / License number" value="{{ $payload['id_number'] ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Proof of Address Details (Utility Bill / Bank Statement)</label>
                                <input type="text" name="proof_of_address" class="form-control" placeholder="e.g. Council Tax Bill / Barclays Statement within last 3 months" value="{{ $payload['proof_of_address'] ?? '' }}">
                            </div>

                        @elseif($order === 6)
                            <!-- Step 6: Engagement -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Authorised Signatory Name</label>
                                    <input type="text" name="signatory_name" class="form-control" placeholder="Full legal name of director / client" value="{{ $payload['signatory_name'] ?? $user->name }}">
                                </div>
                                <div class="form-group">
                                    <label>Signatory Position / Title</label>
                                    <input type="text" name="signatory_title" class="form-control" placeholder="e.g. Managing Director / Sole Trader" value="{{ $payload['signatory_title'] ?? 'Director' }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Engagement Terms Agreement</label>
                                <input type="text" name="terms_agreed" class="form-control" placeholder="Type 'I agree to terms of engagement' to confirm" value="{{ $payload['terms_agreed'] ?? 'I agree to the terms of engagement' }}">
                            </div>

                        @elseif($order === 7)
                            <!-- Step 7: Payment (Table Saved - No Payment Gateway) -->
                            <div class="note ok" style="margin-bottom:12px;">
                                💳 <strong>Payment Details:</strong> Select your payment method and enter your reference number. Saved directly into your practice record without third-party gateways.
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="">-- Select Payment Method --</option>
                                        <option value="Bank Transfer / BACS" {{ ($payload['payment_method'] ?? '') === 'Bank Transfer / BACS' ? 'selected' : '' }}>Bank Transfer / BACS</option>
                                        <option value="Cash" {{ ($payload['payment_method'] ?? '') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="UPI" {{ ($payload['payment_method'] ?? '') === 'UPI' ? 'selected' : '' }}>UPI</option>
                                        <option value="Debit / Credit Card" {{ ($payload['payment_method'] ?? '') === 'Debit / Credit Card' ? 'selected' : '' }}>Debit / Credit Card</option>
                                        <option value="Cheque" {{ ($payload['payment_method'] ?? '') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Transaction / Reference Number</label>
                                    <input type="text" name="payment_reference" class="form-control" placeholder="e.g. TXN-89218 or Bank Ref" value="{{ $payload['payment_reference'] ?? '' }}">
                                </div>
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Deposit / Fee Amount (£)</label>
                                    <input type="text" name="payment_amount" class="form-control" placeholder="e.g. £150.00" value="{{ $payload['payment_amount'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Payment Date</label>
                                    <input type="text" name="payment_date" class="form-control" placeholder="e.g. {{ date('d M Y') }}" value="{{ $payload['payment_date'] ?? date('d M Y') }}">
                                </div>
                            </div>

                        @elseif($order === 8)
                            <!-- Step 8: Document collection -->
                            <div class="form-group">
                                <label>Bank Statements Submitted / Ready</label>
                                <input type="text" name="bank_statements" class="form-control" placeholder="e.g. 12 months Barclays statement CSV/PDF" value="{{ $payload['bank_statements'] ?? '' }}">
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Sales &amp; Purchase Invoices</label>
                                    <input type="text" name="invoices_note" class="form-control" placeholder="e.g. Available in Xero / Uploaded via portal" value="{{ $payload['invoices_note'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Payroll / P60 Records</label>
                                    <input type="text" name="payroll_note" class="form-control" placeholder="e.g. 2 director P60s provided" value="{{ $payload['payroll_note'] ?? '' }}">
                                </div>
                            </div>

                        @elseif($order === 9)
                            <!-- Step 9: Clarification -->
                            <div class="form-group">
                                <label>Accounting / Filing Clarifications &amp; Notes</label>
                                <textarea name="clarification_notes" class="form-control" rows="3" placeholder="Any gaps in statements, director personal expenses to claim, or questions for your accountant...">{{ $payload['clarification_notes'] ?? '' }}</textarea>
                            </div>

                        @elseif($order === 10)
                            <!-- Step 10: Allocation -->
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Assigned Branch / Team</label>
                                    <input type="text" name="branch_pref" class="form-control" placeholder="e.g. Clearview Practice HQ" value="{{ $payload['branch_pref'] ?? 'Clearview Practice HQ' }}">
                                </div>
                                <div class="form-group">
                                    <label>Filing Deadline Priority</label>
                                    <select name="priority_level" class="form-control">
                                        <option value="Normal" {{ ($payload['priority_level'] ?? '') === 'Normal' ? 'selected' : '' }}>Standard Schedule</option>
                                        <option value="High Priority" {{ ($payload['priority_level'] ?? '') === 'High Priority' ? 'selected' : '' }}>Urgent Statutory Deadline</option>
                                    </select>
                                </div>
                            </div>

                        @elseif($order === 11)
                            <!-- Step 11: Processing & review -->
                            <div class="form-group">
                                <label>Draft Review Confirmation</label>
                                <input type="text" name="draft_review" class="form-control" placeholder="e.g. Draft profit & loss verified by director" value="{{ $payload['draft_review'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label>Review Notes / Approval for HMRC Filing</label>
                                <input type="text" name="hmrc_approval" class="form-control" placeholder="e.g. Approved to file CT600 with HMRC" value="{{ $payload['hmrc_approval'] ?? '' }}">
                            </div>

                        @elseif($order === 12)
                            <!-- Step 12: Completion -->
                            <div class="form-group">
                                <label>Final Submission Reference Number</label>
                                <input type="text" name="submission_ref" class="form-control" placeholder="e.g. HMRC-CT600-SUB-82910" value="{{ $payload['submission_ref'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label>Client Feedback &amp; Next Cycle Notes</label>
                                <textarea name="client_feedback" class="form-control" rows="2" placeholder="Your experience and future tax planning goals...">{{ $payload['client_feedback'] ?? '' }}</textarea>
                            </div>
                        @endif

                        <div style="margin-top:16px; display:flex; gap:10px; align-items:center;">
                            <button type="submit" class="btn" style="padding:10px 22px;">
                                Save &amp; Submit Stage {{ $order }} Details &rarr;
                            </button>
                            @if($order < 12)
                                <a href="{{ route('client.dashboard', ['lead_id' => $selectedLead->id, 'step' => $order + 1]) }}" class="btn s sm">
                                    Next Stage &rarr;
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right: Requested Services & Support -->
            <div>
                <!-- Detailed Services Card -->
                <div class="card" style="margin-bottom: 16px;">
                    <h3>Requested Accountancy Services <em>{{ $selectedLead->services->count() }} active</em></h3>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach($selectedLead->services as $srv)
                            <div style="background:var(--s2); border:1px solid var(--bd); border-radius:10px; padding:12px 14px;">
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <strong style="font-size:13.5px; color:var(--tx);">{{ $srv->name }}</strong>
                                    <span class="tag {{ $srv->type === 'business' ? '' : 'wn' }}">{{ ucfirst($srv->type) }}</span>
                                </div>
                                <div style="font-size:12px; color:var(--mu); margin-top:6px; line-height:1.5;">
                                    {{ $srv->description }}
                                </div>
                                <div style="margin-top:8px; font-size:11px; color:var(--ok); font-weight:600;">
                                    ✓ Qualified UK Accountant Oversight &amp; MTD Compliant
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Dedicated Support Card -->
                <div class="card">
                    <h3>Dedicated Practice Support</h3>
                    <p style="color:var(--mu); font-size:13px; margin-top:0;">Have questions about your filings or need assistance?</p>
                    <div class="mail">
                        <b>AccountAI Clearview Practice HQ</b><br>
                        Email: <a href="mailto:support@accountai.com" style="color:var(--ac);">support@accountai.com</a><br>
                        Phone: +44 (0) 20 7946 0991<br>
                        Hours: Mon–Fri 09:00–17:30 BST
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
@endsection
