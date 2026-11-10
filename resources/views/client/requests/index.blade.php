@extends('layouts.client')

@section('title', 'Action Requests – Client Portal')
@section('page_title', 'Action Requests')
@section('page_subtitle', 'Complete required stage parameters for your accountant')

@section('content')
@if(!$selectedLead)
    <div class="card">
        <h3>No Active Enquiries Found</h3>
        <p style="color:var(--mu);">You do not currently have any active enquiries linked to your account.</p>
    </div>
@else
    @if($leads->count() > 1)
        <div class="card" style="margin-bottom: 16px; padding: 12px 18px;">
            <div style="display:flex; align-items:center; gap: 10px; flex-wrap:wrap;">
                <span style="font-weight:600; font-size:13px; color:var(--mu);">Select Active Case:</span>
                @foreach($leads as $l)
                    <a href="{{ route('client.requests.index', ['lead_id' => $l->id]) }}" class="btn sm {{ $selectedLead->id == $l->id ? '' : 's' }}">
                        #{{ $l->id }} · {{ $l->company_name ?: $l->full_name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if(!$activeStep)
        <div class="card hero" style="text-align:center; padding: 40px 24px;">
            <h2 style="font-size: 26px;">🎉 All 12 Stages Completed!</h2>
            <p style="margin: 8px auto 0; max-width: 500px;">Your onboarding and accountancy preparation are 100% complete and fully signed off with HMRC.</p>
        </div>
    @else
        <!-- Active Stage Form Card -->
        <div class="card" style="margin-bottom: 24px; border-top: 4px solid var(--ac);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px; margin-bottom: 14px;">
                <div>
                    <span class="tag {{ $activeStep->isRevisionRequired() ? 'er' : ($activeStep->isSubmitted() ? 'wn' : 'ok') }}">
                        Stage {{ $activeStep->step_order }} of 12 &middot; {{ ucfirst(str_replace('_', ' ', $activeStep->review_status)) }}
                    </span>
                    <h2 style="font-family:var(--d); font-size:22px; margin: 8px 0 4px;">{{ $activeStep->title }}</h2>
                    <p style="color:var(--mu); font-size:13.5px; margin:0;">{{ $activeStep->description }}</p>
                </div>
            </div>

            <!-- Admin Revision Note Banner -->
            @if($activeStep->isRevisionRequired())
                <div class="note er" style="margin: 14px 0 20px;">
                    <div style="font-weight:700; font-size:14px; margin-bottom: 4px;">⚠️ Action Required: Accountant Feedback</div>
                    <div style="font-size:13.5px;">{{ $activeStep->admin_notes }}</div>
                    <small style="display:block; margin-top: 6px; color: var(--er);">Please correct the details below and re-submit for approval.</small>
                </div>
            @endif

            @if($activeStep->isSubmitted())
                <div class="note wn" style="margin: 14px 0 20px;">
                    ⏳ <strong>Under Accountant Review:</strong> Your submission for this stage has been received. Your accountant is reviewing it. You may update the details below at any time if needed.
                </div>
            @endif

            @php
                $p = is_array($activeStep->payload) ? $activeStep->payload : [];
            @endphp

            <form method="POST" action="{{ route('client.requests.submit', $activeStep->id) }}">
                @csrf

                @if($activeStep->step_order == 2)
                    <!-- STAGE 2: QUALIFICATION -->
                    <div class="g g2">
                        <div class="form-group">
                            <label>Annual Company Turnover (£ GBP)</label>
                            <input type="text" name="turnover" class="form-control" placeholder="e.g. £85,000 / £250,000" value="{{ $p['turnover'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label>Accounting Software Used</label>
                            <select name="accounting_software" class="form-control">
                                <option value="">-- Select or Leave Blank --</option>
                                <option value="Xero" {{ ($p['accounting_software'] ?? '') == 'Xero' ? 'selected' : '' }}>Xero</option>
                                <option value="QuickBooks Online" {{ ($p['accounting_software'] ?? '') == 'QuickBooks Online' ? 'selected' : '' }}>QuickBooks Online</option>
                                <option value="FreeAgent" {{ ($p['accounting_software'] ?? '') == 'FreeAgent' ? 'selected' : '' }}>FreeAgent</option>
                                <option value="Sage" {{ ($p['accounting_software'] ?? '') == 'Sage' ? 'selected' : '' }}>Sage</option>
                                <option value="Excel / Spreadsheets" {{ ($p['accounting_software'] ?? '') == 'Excel / Spreadsheets' ? 'selected' : '' }}>Excel / Spreadsheets</option>
                                <option value="None / Paper" {{ ($p['accounting_software'] ?? '') == 'None / Paper' ? 'selected' : '' }}>None / Paper Invoices</option>
                            </select>
                        </div>
                    </div>
                    <div class="g g2">
                        <div class="form-group">
                            <label>Number of Staff / Employees</label>
                            <input type="text" name="employee_count" class="form-control" placeholder="e.g. 1-5 employees" value="{{ $p['employee_count'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label>VAT Registration Status</label>
                            <select name="vat_status" class="form-control">
                                <option value="">-- Select or Leave Blank --</option>
                                <option value="Registered" {{ ($p['vat_status'] ?? '') == 'Registered' ? 'selected' : '' }}>VAT Registered</option>
                                <option value="Not Registered" {{ ($p['vat_status'] ?? '') == 'Not Registered' ? 'selected' : '' }}>Not Registered</option>
                                <option value="Planning to Register" {{ ($p['vat_status'] ?? '') == 'Planning to Register' ? 'selected' : '' }}>Planning to Register</option>
                            </select>
                        </div>
                    </div>

                @elseif($activeStep->step_order == 3)
                    <!-- STAGE 3: CONSULTATION & QUOTE -->
                    <div class="mail" style="margin-bottom:16px;">
                        <b>AccountAI Fee Proposal:</b> Based on your requirements, the proposed scope includes Annual Accounts &amp; Corporation Tax filing.
                    </div>
                    <div class="g g2">
                        <div class="form-group">
                            <label>Preferred Billing Schedule</label>
                            <select name="billing_schedule" class="form-control">
                                <option value="Monthly Direct Debit" {{ ($p['billing_schedule'] ?? '') == 'Monthly Direct Debit' ? 'selected' : '' }}>Monthly Direct Debit</option>
                                <option value="Annual Upfront (5% discount)" {{ ($p['billing_schedule'] ?? '') == 'Annual Upfront (5% discount)' ? 'selected' : '' }}>Annual Upfront (5% discount)</option>
                                <option value="Per Service Filing" {{ ($p['billing_schedule'] ?? '') == 'Per Service Filing' ? 'selected' : '' }}>Per Service Filing</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Preferred Consultation Channel</label>
                            <select name="consultation_channel" class="form-control">
                                <option value="Video Call (Zoom/Teams)" {{ ($p['consultation_channel'] ?? '') == 'Video Call (Zoom/Teams)' ? 'selected' : '' }}>Video Call (Zoom / Teams)</option>
                                <option value="Phone Call" {{ ($p['consultation_channel'] ?? '') == 'Phone Call' ? 'selected' : '' }}>Phone Call</option>
                                <option value="Email Discussion" {{ ($p['consultation_channel'] ?? '') == 'Email Discussion' ? 'selected' : '' }}>Email Discussion</option>
                            </select>
                        </div>
                    </div>

                @elseif($activeStep->step_order == 4)
                    <!-- STAGE 4: REGISTRATION -->
                    <div class="g g2">
                        <div class="form-group">
                            <label>Companies House Registration Number (CRN)</label>
                            <input type="text" name="company_reg_number" class="form-control" placeholder="e.g. 12345678" value="{{ $p['company_reg_number'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label>Company Unique Taxpayer Reference (UTR)</label>
                            <input type="text" name="company_utr" class="form-control" placeholder="10-digit Corporation Tax UTR" value="{{ $p['company_utr'] ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Registered Office Address</label>
                        <textarea name="registered_address" class="form-control" rows="2" placeholder="Street, City, Postal Code">{{ $p['registered_address'] ?? '' }}</textarea>
                    </div>
                    <div class="g g2">
                        <div class="form-group">
                            <label>Director / Principal Full Legal Name</label>
                            <input type="text" name="director_name" class="form-control" value="{{ $p['director_name'] ?? $selectedLead->full_name }}">
                        </div>
                        <div class="form-group">
                            <label>Director Date of Birth</label>
                            <input type="text" name="director_dob" class="form-control" placeholder="DD/MM/YYYY" value="{{ $p['director_dob'] ?? '' }}">
                        </div>
                    </div>

                @elseif($activeStep->step_order == 5)
                    <!-- STAGE 5: AML & COMPLIANCE -->
                    <div class="g g2">
                        <div class="form-group">
                            <label>Photo ID Document Type</label>
                            <select name="aml_id_type" class="form-control">
                                <option value="UK Passport" {{ ($p['aml_id_type'] ?? '') == 'UK Passport' ? 'selected' : '' }}>UK Passport</option>
                                <option value="International Passport" {{ ($p['aml_id_type'] ?? '') == 'International Passport' ? 'selected' : '' }}>International Passport</option>
                                <option value="UK Full Driving Licence" {{ ($p['aml_id_type'] ?? '') == 'UK Full Driving Licence' ? 'selected' : '' }}>UK Full Driving Licence</option>
                                <option value="EU / National Identity Card" {{ ($p['aml_id_type'] ?? '') == 'EU / National Identity Card' ? 'selected' : '' }}>EU / National Identity Card</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Document Identification Number</label>
                            <input type="text" name="aml_id_number" class="form-control" placeholder="e.g. 9-digit passport / licence code" value="{{ $p['aml_id_number'] ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Residential Address for Proof of Identity</label>
                        <textarea name="aml_address" class="form-control" rows="2" placeholder="Full residential address matching your utility bill or bank statement">{{ $p['aml_address'] ?? '' }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Politically Exposed Person (PEP) Declaration</label>
                        <select name="aml_pep_declaration" class="form-control">
                            <option value="No - Neither myself nor immediate family is a PEP" {{ ($p['aml_pep_declaration'] ?? '') == 'No - Neither myself nor immediate family is a PEP' ? 'selected' : '' }}>No &mdash; Neither myself nor immediate family is a PEP</option>
                            <option value="Yes - I or an immediate family member is a PEP" {{ ($p['aml_pep_declaration'] ?? '') == 'Yes - I or an immediate family member is a PEP' ? 'selected' : '' }}>Yes &mdash; I or an immediate family member is a PEP</option>
                        </select>
                    </div>

                @elseif($activeStep->step_order == 6)
                    <!-- STAGE 6: ENGAGEMENT -->
                    <div class="mail" style="margin-bottom:16px;">
                        <b>Standard UK Letter of Engagement (Scope of Practice)</b><br>
                        This letter confirms our terms of appointment as your accountants and tax advisers in the United Kingdom. Scope includes preparation of accounts, calculation of tax liabilities, submission to HMRC and Companies House, and statutory compliance.
                    </div>
                    <div class="form-group">
                        <label>Full Legal Name for Electronic Signature</label>
                        <input type="text" name="signatory_name" class="form-control" placeholder="Type your full legal name" value="{{ $p['signatory_name'] ?? $selectedLead->full_name }}">
                    </div>
                    <div class="form-group">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="engagement_agreed" value="yes" {{ ($p['engagement_agreed'] ?? '') == 'yes' ? 'checked' : '' }}>
                            <span>I confirm I am authorised to sign on behalf of the business and agree to the Terms of Engagement.</span>
                        </label>
                    </div>

                @elseif($activeStep->step_order == 7)
                    <!-- STAGE 7: PAYMENT (Table Record - No Gateway) -->
                    <div class="note ok" style="margin-bottom:16px;">
                        💳 <strong>Payment Details:</strong> Select your payment method and enter your reference number. Saved directly into your practice record without third-party gateways.
                    </div>
                    <div class="g g2">
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="BACS / Bank Transfer" {{ ($p['payment_method'] ?? '') == 'BACS / Bank Transfer' ? 'selected' : '' }}>BACS / Direct Bank Transfer</option>
                                <option value="Cash" {{ ($p['payment_method'] ?? '') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="UPI" {{ ($p['payment_method'] ?? '') == 'UPI' ? 'selected' : '' }}>UPI</option>
                                <option value="Standing Order / Direct Debit" {{ ($p['payment_method'] ?? '') == 'Standing Order / Direct Debit' ? 'selected' : '' }}>Standing Order / Direct Debit</option>
                                <option value="Corporate Debit Card" {{ ($p['payment_method'] ?? '') == 'Corporate Debit Card' ? 'selected' : '' }}>Corporate Debit Card</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Payment Transaction Reference</label>
                            <input type="text" name="payment_ref" class="form-control" placeholder="e.g. BAC-889102, Cash Ref or UPI Ref" value="{{ $p['payment_ref'] ?? '' }}">
                        </div>
                    </div>
                    <div class="g g2">
                        <div class="form-group">
                            <label>Amount Paid (£ GBP)</label>
                            <input type="text" name="payment_amount" class="form-control" placeholder="e.g. 450.00" value="{{ $p['payment_amount'] ?? '450.00' }}">
                        </div>
                        <div class="form-group">
                            <label>Payment Date</label>
                            <input type="text" name="payment_date" class="form-control" value="{{ $p['payment_date'] ?? date('d M Y') }}">
                        </div>
                    </div>

                @elseif($activeStep->step_order == 8)
                    <!-- STAGE 8: DOCUMENT COLLECTION -->
                    <div class="form-group">
                        <label>12-Month Bank Statements Confirmation</label>
                        <input type="text" name="docs_bank_statements" class="form-control" placeholder="e.g. CSV / PDF export available or linked via Xero" value="{{ $p['docs_bank_statements'] ?? '' }}">
                    </div>
                    <div class="g g2">
                        <div class="form-group">
                            <label>Sales &amp; Purchase Invoices</label>
                            <input type="text" name="docs_invoices" class="form-control" placeholder="e.g. All sales invoices up to date in software" value="{{ $p['docs_invoices'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label>Director P60 / Payroll Summary</label>
                            <input type="text" name="docs_payroll" class="form-control" placeholder="e.g. P60 documents uploaded or provided" value="{{ $p['docs_payroll'] ?? '' }}">
                        </div>
                    </div>

                @elseif($activeStep->step_order == 9)
                    <!-- STAGE 9: CLARIFICATION -->
                    <div class="form-group">
                        <label>Outstanding Items &amp; Answers to Accountant Queries</label>
                        <textarea name="clarification_notes" class="form-control" rows="3" placeholder="Provide any explanations for missing periods, director loans or business expense claims...">{{ $p['clarification_notes'] ?? '' }}</textarea>
                    </div>

                @elseif($activeStep->step_order == 10)
                    <!-- STAGE 10: ALLOCATION -->
                    <div class="g g2">
                        <div class="form-group">
                            <label>Branch / Team Allocation</label>
                            <input type="text" name="allocated_branch" class="form-control" value="{{ $p['allocated_branch'] ?? 'Clearview Branch HQ (John Smith)' }}">
                        </div>
                        <div class="form-group">
                            <label>Urgency Level</label>
                            <select name="urgency_level" class="form-control">
                                <option value="Standard" {{ ($p['urgency_level'] ?? '') == 'Standard' ? 'selected' : '' }}>Standard SLA</option>
                                <option value="Urgent Deadline Risk" {{ ($p['urgency_level'] ?? '') == 'Urgent Deadline Risk' ? 'selected' : '' }}>Urgent &mdash; HMRC Deadline Approaching</option>
                            </select>
                        </div>
                    </div>

                @elseif($activeStep->step_order == 11)
                    <!-- STAGE 11: PROCESSING & REVIEW -->
                    <div class="mail" style="margin-bottom:14px;">
                        <b>Draft Statutory Accounts &amp; Tax Review:</b> Draft figures will be prepared by your assigned accountant in your accounting software for your final approval.
                    </div>
                    <div class="form-group">
                        <label>Client Review Approval</label>
                        <input type="text" name="review_approval" class="form-control" placeholder="Type 'Approved for HMRC submission' after checking drafts" value="{{ $p['review_approval'] ?? 'Approved for HMRC submission' }}">
                    </div>

                @elseif($activeStep->step_order == 12)
                    <!-- STAGE 12: COMPLETION -->
                    <div class="form-group">
                        <label>Filing Reference &amp; Next-Cycle Scheduling</label>
                        <input type="text" name="completion_ref" class="form-control" placeholder="Companies House / HMRC electronic receipt" value="{{ $p['completion_ref'] ?? 'HMRC-ELECTRONIC-SUB-2026' }}">
                    </div>
                    <div class="form-group">
                        <label>Client Feedback &amp; Future Services Required</label>
                        <textarea name="client_feedback" class="form-control" rows="2" placeholder="Tell us about your experience and any upcoming tax planning...">{{ $p['client_feedback'] ?? '' }}</textarea>
                    </div>
                @endif

                <div style="margin-top: 20px; display:flex; justify-content:space-between; align-items:center;">
                    <button type="submit" class="btn" style="padding: 12px 28px;">
                        {{ $activeStep->isRevisionRequired() ? 'Re-Submit Corrected Details &rarr;' : 'Save & Submit Stage ' . $activeStep->step_order . ' Details &rarr;' }}
                    </button>
                    <small style="color:var(--mu);">All fields are optional. Fill whatever you have available.</small>
                </div>
            </form>
        </div>
    @endif
@endif
@endsection
