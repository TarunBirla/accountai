@extends('layouts.client')

@section('title', 'Submit New Service Enquiry')
@section('page_title', 'Submit New Service Enquiry')
@section('page_subtitle', 'Request additional UK accountancy services or onboard another business entity')

@push('styles')
<style>
.service-selector-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 14px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    transition: all .15s ease;
}
.service-selector-card:hover {
    border-color: #3a4bdc;
    background: #f8fafc;
}
.service-selector-card input[type="checkbox"] {
    margin-top: 3px;
    width: 17px;
    height: 17px;
    accent-color: #3a4bdc;
    cursor: pointer;
}
.service-selector-card b {
    display: block;
    font-size: 13px;
    color: #0e1530;
    margin-bottom: 2px;
}
.service-selector-card small {
    display: block;
    font-size: 11.5px;
    color: #64708a;
    line-height: 1.35;
}
.section-title {
    font-size: 14px;
    font-weight: 700;
    color: #0e1530;
    margin: 0 0 14px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.section-title .badge {
    font-size: 11px;
    font-weight: 600;
    background: #eef2ff;
    color: #3a4bdc;
    padding: 2px 8px;
    border-radius: 99px;
}
.info-callout {
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
.info-callout i {
    font-style: normal;
    font-size: 18px;
}
</style>
@endpush

@section('content')
<div style="max-width: 1050px; margin: 0 auto;">

    <!-- Info Banner -->
    <div class="info-callout">
        <i>💡</i>
        <div>
            <strong style="color: #1e1b4b; font-size: 13.5px;">Logged in as {{ $user->name }} ({{ $user->email }})</strong>
            <p style="margin: 3px 0 0; color: #4338ca; font-size: 12.5px;">
                Submitting this form creates a new case linked to your account with its own 12-stage onboarding and delivery journey. You will receive an immediate confirmation and your dedicated practice accountant will review it.
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('client.new-request.store') }}">
        @csrf

        <div class="card" style="margin-bottom: 20px; padding: 22px; background:#ffffff;">
            <div class="section-title">
                <span>1. Business &amp; Entity Information</span>
                <span class="badge">UK Compliance</span>
            </div>

            <div class="g2" style="grid-template-columns: 1fr 1fr; margin-bottom: 14px;">
                <div class="form-group">
                    <label>Company / Trading Name</label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $user->company) }}" placeholder="e.g. Acme Tech Solutions Ltd or John Doe Consulting">
                    <small style="color:var(--mu); font-size:11px;">Enter Ltd company name, partnership name or sole trader trading title</small>
                </div>
                <div class="form-group">
                    <label>Business Structure / Legal Entity</label>
                    <select name="business_structure" class="form-control">
                        <option value="Private Limited Company (Ltd)" {{ old('business_structure') == 'Private Limited Company (Ltd)' ? 'selected' : '' }}>Private Limited Company (Ltd)</option>
                        <option value="Sole Trader / Self-Employed" {{ old('business_structure') == 'Sole Trader / Self-Employed' ? 'selected' : '' }}>Sole Trader / Self-Employed</option>
                        <option value="Limited Liability Partnership (LLP)" {{ old('business_structure') == 'Limited Liability Partnership (LLP)' ? 'selected' : '' }}>Limited Liability Partnership (LLP)</option>
                        <option value="Ordinary Partnership" {{ old('business_structure') == 'Ordinary Partnership' ? 'selected' : '' }}>Ordinary Partnership</option>
                        <option value="Individual / Personal Taxpayer" {{ old('business_structure') == 'Individual / Personal Taxpayer' ? 'selected' : '' }}>Individual / Personal Taxpayer</option>
                        <option value="Starting New Business / Not Formed" {{ old('business_structure') == 'Starting New Business / Not Formed' ? 'selected' : '' }}>Starting New Business / Not Formed</option>
                    </select>
                </div>
            </div>

            <div class="g3" style="grid-template-columns: repeat(3, 1fr);">
                <div class="form-group">
                    <label>Estimated Annual Turnover (£)</label>
                    <select name="turnover" class="form-control">
                        <option value="Under £50k">Under £50,000</option>
                        <option value="£50k - £90k (VAT Threshold)" selected>£50,000 - £90,000 (VAT Threshold)</option>
                        <option value="£90k - £250k">£90,000 - £250,000</option>
                        <option value="£250k - £1m">£250,000 - £1,000,000</option>
                        <option value="£1m+">£1,000,000+</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>VAT Status</label>
                    <select name="vat_status" class="form-control">
                        <option value="Not VAT Registered">Not VAT Registered</option>
                        <option value="VAT Registered (Standard Rate 20%)">VAT Registered (Standard Rate 20%)</option>
                        <option value="VAT Registered (Flat Rate Scheme)">VAT Registered (Flat Rate Scheme)</option>
                        <option value="Need Assistance with VAT Registration">Need Assistance with VAT Registration</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Employee &amp; Payroll Count</label>
                    <select name="employee_count" class="form-control">
                        <option value="0 (Director Only / None)">None (Director Only)</option>
                        <option value="1 - 5 Employees">1 - 5 Employees</option>
                        <option value="6 - 20 Employees">6 - 20 Employees</option>
                        <option value="20+ Employees">20+ Employees</option>
                    </select>
                </div>
            </div>

            <div class="g2" style="grid-template-columns: 1fr 1fr;">
                <div class="form-group">
                    <label>Contact Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+44 7123 456789">
                </div>
                <div class="form-group">
                    <label>Urgency / Target Completion Date</label>
                    <select name="urgency" class="form-control">
                        <option value="Standard Onboarding">Standard Onboarding (within 2-4 weeks)</option>
                        <option value="Urgent Filing Deadline (< 2 weeks)">Urgent Filing Deadline (&lt; 2 weeks)</option>
                        <option value="Immediate / Overdue HMRC Deadline">Immediate / Overdue HMRC Deadline</option>
                        <option value="Planning Ahead for Next Financial Year">Planning Ahead for Next Financial Year</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- UK Business Services Selection -->
        <div class="card" style="margin-bottom: 20px; padding: 22px; background:#ffffff;">
            <div class="section-title">
                <span>2. Select Required Business Accounting Services</span>
                <span class="badge">Tick all that apply</span>
            </div>

            <div class="g3" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 10px;">
                @foreach($businessServices as $service)
                    <label class="service-selector-card">
                        <input type="checkbox" name="business_services[]" value="{{ $service->id }}" {{ $loop->index < 2 ? 'checked' : '' }}>
                        <div>
                            <b>{{ $service->name }}</b>
                            <small>{{ Str::limit($service->description, 95) }}</small>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Personal Tax & Advisory Services Selection -->
        <div class="card" style="margin-bottom: 20px; padding: 22px; background:#ffffff;">
            <div class="section-title">
                <span>3. Select Personal Tax &amp; Director Services</span>
                <span class="badge">Optional</span>
            </div>

            <div class="g3" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 10px;">
                @foreach($personalServices as $service)
                    <label class="service-selector-card">
                        <input type="checkbox" name="personal_services[]" value="{{ $service->id }}">
                        <div>
                            <b>{{ $service->name }}</b>
                            <small>{{ Str::limit($service->description, 95) }}</small>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Notes / Deadlines / Instructions -->
        <div class="card" style="margin-bottom: 20px; padding: 22px; background:#ffffff;">
            <div class="section-title">
                <span>4. Additional Details &amp; Deadlines</span>
                <span class="badge">Notes for Accountant</span>
            </div>

            <div class="form-group">
                <label>Specific Instructions, Upcoming Deadlines, or Previous Accountant Transfer Info</label>
                <textarea name="notes" rows="4" class="form-control" placeholder="Tell us about your accounting year-end, current bookkeeping software (Xero, QuickBooks, FreeAgent, Excel), any pending HMRC letters, or handover requirements from your previous accountant...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Action Bar -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 40px;">
            <a href="{{ route('client.dashboard') }}" class="btn s">
                &larr; Back to Client Journey
            </a>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="padding: 10px 24px; font-size: 14px;">
                    Submit Service Request &rarr;
                </button>
            </div>
        </div>
    </form>

    <!-- Recent Cases Overview -->
    @if($recentLeads->isNotEmpty())
        <div class="card" style="padding: 20px; background:#ffffff;">
            <h3 style="margin-top: 0; font-size: 14px; color: var(--tx);">
                Your Existing Active Cases ({{ $recentLeads->count() }})
            </h3>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($recentLeads as $lead)
                    <div style="display:flex; justify-content:space-between; align-items:center; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; flex-wrap:wrap; gap: 8px;">
                        <div>
                            <strong style="color: #0e1530;">#{{ $lead->id }} · {{ $lead->company_name ?: $lead->full_name }}</strong>
                            <div style="font-size: 12px; color: var(--mu); margin-top: 2px;">
                                Submitted {{ $lead->created_at->format('d M Y') }} · {{ $lead->services->pluck('name')->join(', ') ?: 'No services listed' }}
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap: 10px;">
                            <span class="tag {{ $lead->status === 'converted' ? 'ok' : 'wn' }}">
                                {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                            </span>
                            <a href="{{ route('client.dashboard', ['lead_id' => $lead->id]) }}" class="btn sm s">
                                Open Case &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
