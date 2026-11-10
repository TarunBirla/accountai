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
            @elseif($activeStep->isSubmitted())
                <div class="note ok" style="margin: 14px 0 20px;">
                    <strong>✓ Submitted for Review:</strong> Your accountant has received your stage details and will review and approve shortly. You may update your inputs below if needed.
                </div>
            @endif

            <form method="POST" action="{{ route('client.requests.submit', $activeStep->id) }}">
                @csrf
                @php $p = $activeStep->payload ?? []; @endphp

                <!-- Dynamic Form Parameters Based On Stage -->
                @if($activeStep->step_order == 2)
                    <!-- STAGE 2: QUALIFICATION -->
                    <div class="g g2">
                        <div class="form-group">
                            <label>Estimated Annual Turnover / Gross Income *</label>
                            <select name="annual_turnover" class="form-control" required>
                                <option value="Under £25k" {{ ($p['annual_turnover'] ?? '') == 'Under £25k' ? 'selected' : '' }}>Under £25,000</option>
                                <option value="£25k - £85k" {{ ($p['annual_turnover'] ?? '') == '£25k - £85k' ? 'selected' : '' }}>£25,000 – £85,000</option>
                                <option value="£85k - £250k" {{ ($p['annual_turnover'] ?? '') == '£85k - £250k' ? 'selected' : '' }}>£85,000 – £250,000 (VAT threshold)</option>
                                <option value="£250k - £1M" {{ ($p['annual_turnover'] ?? '') == '£250k - £1M' ? 'selected' : '' }}>£250,000 – £1,000,000</option>
                                <option value="Over £1M" {{ ($p['annual_turnover'] ?? '') == 'Over £1M' ? 'selected' : '' }}>Over £1,000,000</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Accounting / Bookkeeping Software *</label>
                            <select name="accounting_software" class="form-control" required>
                                <option value="Xero" {{ ($p['accounting_software'] ?? '') == 'Xero' ? 'selected' : '' }}>Xero</option>
                                <option value="QuickBooks" {{ ($p['accounting_software'] ?? '') == 'QuickBooks' ? 'selected' : '' }}>QuickBooks Online</option>
                                <option value="FreeAgent" {{ ($p['accounting_software'] ?? '') == 'FreeAgent' ? 'selected' : '' }}>FreeAgent</option>
                                <option value="Sage" {{ ($p['accounting_software'] ?? '') == 'Sage' ? 'selected' : '' }}>Sage</option>
                                <option value="Excel / Spreadsheets" {{ ($p['accounting_software'] ?? '') == 'Excel / Spreadsheets' ? 'selected' : '' }}>Excel / Spreadsheets</option>
                                <option value="None / Paper" {{ ($p['accounting_software'] ?? '') == 'None / Paper' ? 'selected' : '' }}>None / Paper Records</option>
                            </select>
                        </div>
                    </div>

                    <div class="g g2">
                        <div class="form-group">
                            <label>Number of Employees on Payroll</label>
                            <select name="employees_count" class="form-control">
                                <option value="0 (Just Director)" {{ ($p['employees_count'] ?? '') == '0 (Just Director)' ? 'selected' : '' }}>0 (Sole Director / Sole Trader)</option>
                                <option value="1 - 5 staff" {{ ($p['employees_count'] ?? '') == '1 - 5 staff' ? 'selected' : '' }}>1 – 5 Staff</option>
                                <option value="6 - 15 staff" {{ ($p['employees_count'] ?? '') == '6 - 15 staff' ? 'selected' : '' }}>6 – 15 Staff</option>
                                <option value="16+ staff" {{ ($p['employees_count'] ?? '') == '16+ staff' ? 'selected' : '' }}>16+ Staff</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>VAT Status *</label>
                            <select name="vat_status" class="form-control" required>
                                <option value="Not Registered" {{ ($p['vat_status'] ?? '') == 'Not Registered' ? 'selected' : '' }}>Not VAT Registered</option>
                                <option value="Registered (Standard 20%)" {{ ($p['vat_status'] ?? '') == 'Registered (Standard 20%)' ? 'selected' : '' }}>VAT Registered (Standard)</option>
                                <option value="Registered (Flat Rate)" {{ ($p['vat_status'] ?? '') == 'Registered (Flat Rate)' ? 'selected' : '' }}>VAT Registered (Flat Rate Scheme)</option>
                                <option value="Needs VAT Registration" {{ ($p['vat_status'] ?? '') == 'Needs VAT Registration' ? 'selected' : '' }}>Need AccountAI to Register for VAT</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Next Statutory Accounts / Tax Return Deadline (Approximate)</label>
                        <input type="date" name="deadline_target" class="form-control" value="{{ $p['deadline_target'] ?? '' }}">
                    </div>

                @elseif($activeStep->step_order == 3)
                    <!-- STAGE 3: CONSULTATION & QUOTE -->
                    <div class="g g2">
                        <div class="form-group">
                            <label>Preferred Consultation Channel *</label>
                            <select name="consultation_channel" class="form-control" required>
                                <option value="Video Call (Google Meet / Zoom)" {{ ($p['consultation_channel'] ?? '') == 'Video Call (Google Meet / Zoom)' ? 'selected' : '' }}>Video Call (Google Meet / Zoom)</option>
                                <option value="Telephone Call" {{ ($p['consultation_channel'] ?? '') == 'Telephone Call' ? 'selected' : '' }}>Telephone Call</option>
                                <option value="In-person (Branch Office)" {{ ($p['consultation_channel'] ?? '') == 'In-person (Branch Office)' ? 'selected' : '' }}>In-person (Branch Office)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Preferred Call Slot</label>
                            <input type="text" name="preferred_slot" class="form-control" placeholder="e.g. Weekdays mornings / Wednesday 2 PM" value="{{ $p['preferred_slot'] ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Proposed Fee Structure &amp; Scope Acceptance *</label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13.5px; background:var(--s2); border:1px solid var(--bd); padding:10px 14px; border-radius:8px;">
                            <input type="checkbox" name="quote_agreed" value="yes" required {{ ($p['quote_agreed'] ?? '') == 'yes' ? 'checked' : '' }}>
                            <span>I confirm agreement with the scope of services and fixed-fee quotation discussed.</span>
                        </label>
                    </div>

                @elseif($activeStep->step_order == 4)
                    <!-- STAGE 4: REGISTRATION -->
                    <div class="g g2">
                        <div class="form-group">
                            <label>Companies House Registration Number (CRN) *</label>
                            <input type="text" name="crn_number" class="form-control" placeholder="e.g. 12345678 or Sole Trader" value="{{ $p['crn_number'] ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label>Company Trading Name *</label>
                            <input type="text" name="registered_company_name" class="form-control" value="{{ $p['registered_company_name'] ?? $selectedLead->company_name }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Registered Office Address *</label>
                        <textarea name="registered_address" class="form-control" rows="2" placeholder="Street, City, Postal Code" required>{{ $p['registered_address'] ?? '' }}</textarea>
                    </div>
                    <div class="g g2">
                        <div class="form-group">
                            <label>Director / Principal Full Legal Name *</label>
                            <input type="text" name="director_name" class="form-control" value="{{ $p['director_name'] ?? $selectedLead->full_name }}" required>
                        </div>
                        <div class="form-group">
                            <label>Director Date of Birth *</label>
                            <input type="date" name="director_dob" class="form-control" value="{{ $p['director_dob'] ?? '' }}" required>
                        </div>
                    </div>

                @elseif($activeStep->step_order == 5)
                    <!-- STAGE 5: AML & COMPLIANCE -->
                    <div class="g g2">
                        <div class="form-group">
                            <label>Photo ID Document Type *</label>
                            <select name="aml_id_type" class="form-control" required>
                                <option value="UK Passport" {{ ($p['aml_id_type'] ?? '') == 'UK Passport' ? 'selected' : '' }}>UK Passport</option>
                                <option value="International Passport" {{ ($p['aml_id_type'] ?? '') == 'International Passport' ? 'selected' : '' }}>International Passport</option>
                                <option value="UK Full Driving Licence" {{ ($p['aml_id_type'] ?? '') == 'UK Full Driving Licence' ? 'selected' : '' }}>UK Full Driving Licence</option>
                                <option value="EU / National Identity Card" {{ ($p['aml_id_type'] ?? '') == 'EU / National Identity Card' ? 'selected' : '' }}>EU / National Identity Card</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Document Identification Number *</label>
                            <input type="text" name="aml_id_number" class="form-control" placeholder="e.g. 9-digit passport / licence code" value="{{ $p['aml_id_number'] ?? '' }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Residential Address for Proof of Identity *</label>
                        <textarea name="aml_address" class="form-control" rows="2" placeholder="Full residential address matching your utility bill or bank statement" required>{{ $p['aml_address'] ?? '' }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Politically Exposed Person (PEP) Declaration *</label>
                        <select name="aml_pep_declaration" class="form-control" required>
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
                        <label>Full Legal Name for Electronic Signature *</label>
                        <input type="text" name="signatory_name" class="form-control" placeholder="Type your full legal name" value="{{ $p['signatory_name'] ?? $selectedLead->full_name }}" required>
                    </div>
                    <div class="form-group">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="engagement_agreed" value="yes" required {{ ($p['engagement_agreed'] ?? '') == 'yes' ? 'checked' : '' }}>
                            <span>I confirm I am authorised to sign on behalf of the business and agree to the Terms of Engagement.</span>
                        </label>
                    </div>

                @elseif($activeStep->step_order == 7)
                    <!-- STAGE 7: PAYMENT (Table Record - No gateway) -->
                    <div class="note ok" style="margin-bottom:16px;">
                        <strong>Note:</strong> No card payment gateway is processed online. Please record your payment transfer details below so our practice reconciliation team can credit your account.
                    </div>
                    <div class="g g2">
                        <div class="form-group">
                            <label>Payment Method *</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="BACS / Bank Transfer" {{ ($p['payment_method'] ?? '') == 'BACS / Bank Transfer' ? 'selected' : '' }}>BACS / Direct Bank Transfer</option>
                                <option value="Standing Order / Direct Debit" {{ ($p['payment_method'] ?? '') == 'Standing Order / Direct Debit' ? 'selected' : '' }}>Standing Order / Direct Debit</option>
                                <option value="Corporate Debit Card" {{ ($p['payment_method'] ?? '') == 'Corporate Debit Card' ? 'selected' : '' }}>Corporate Debit Card</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Payment Transaction Reference *</label>
                            <input type="text" name="payment_ref" class="form-control" placeholder="e.g. BAC-889102 or Invoice INV-2026" value="{{ $p['payment_ref'] ?? '' }}" required>
                        </div>
                    </div>
                    <div class="g g2">
                        <div class="form-group">
                            <label>Amount Paid (£ GBP) *</label>
                            <input type="number" step="0.01" name="payment_amount" class="form-control" placeholder="e.g. 450.00" value="{{ $p['payment_amount'] ?? '450.00' }}" required>
                        </div>
                        <div class="form-group">
                            <label>Payment Date *</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ $p['payment_date'] ?? date('Y-m-d') }}" required>
                        </div>
                    </div>

                @elseif($activeStep->step_order == 8)
                    <!-- STAGE 8: DOCUMENT COLLECTION -->
                    <div class="form-group">
                        <label>Bank Statements Coverage *</label>
                        <select name="docs_bank_status" class="form-control" required>
                            <option value="Complete 12 Months Available" {{ ($p['docs_bank_status'] ?? '') == 'Complete 12 Months Available' ? 'selected' : '' }}>Complete 12 Months Available</option>
                            <option value="Partial (Some months pending)" {{ ($p['docs_bank_status'] ?? '') == 'Partial (Some months pending)' ? 'selected' : '' }}>Partial (Some months pending)</option>
                            <option value="Connected to Xero / QBO Feed" {{ ($p['docs_bank_status'] ?? '') == 'Connected to Xero / QBO Feed' ? 'selected' : '' }}>Connected Directly to Xero / QBO Feed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>List of Submitted Records / Invoices / Upload Summary *</label>
                        <textarea name="docs_summary" class="form-control" rows="3" placeholder="e.g. Bank statements Apr-Mar provided, P60 attached, Q1-Q4 VAT reports ready..." required>{{ $p['docs_summary'] ?? '' }}</textarea>
                    </div>

                @else
                    <!-- GENERAL PARAMETERS FOR REMAINING STAGES -->
                    <div class="form-group">
                        <label>Stage Review Confirmation &amp; Client Feedback *</label>
                        <textarea name="stage_feedback" class="form-control" rows="3" placeholder="Provide any additional details or confirmation for this stage..." required>{{ $p['stage_feedback'] ?? 'Confirmed and signed off for review.' }}</textarea>
                    </div>
                @endif

                <div style="margin-top: 20px; display:flex; gap:10px;">
                    <button type="submit" class="btn" style="padding:11px 24px;">
                        Submit Stage {{ $activeStep->step_order }} Details &rarr;
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Completed Stages History -->
    @if($historySteps->isNotEmpty())
        <div class="card">
            <h3>Completed Journey Milestones <em>{{ $historySteps->count() }} of 12 approved</em></h3>
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($historySteps as $hist)
                    <div style="background:var(--s2); border:1px solid var(--bd); border-radius:10px; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <strong style="font-size:13.5px; color:var(--ok);">✓ Stage {{ $hist->step_order }}: {{ $hist->title }}</strong>
                            <div style="font-size:12px; color:var(--mu); margin-top:2px;">{{ $hist->description }}</div>
                        </div>
                        <span class="tag ok">Approved</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endif
@endsection
