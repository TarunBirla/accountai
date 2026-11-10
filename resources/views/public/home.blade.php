@extends('layouts.public')

@section('title', 'AccountAI – Modern UK Accountancy & Tax Management')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="hero-box">
        <h1>Transforming UK Accountancy with Real-Time Intelligence</h1>
        <p>Combining chartered UK accountancy expertise with automated digital operations. Fast turnaround on company accounts, Corporation Tax, VAT, Self Assessment, and tax advisory.</p>
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <a href="#lead-form" class="btn" style="background:#fff; color:#0a1030!important; padding:12px 24px; font-size:15px;">Request a Quote &amp; Consultation</a>
            <a href="#services-section" class="btn outline" style="background:rgba(255,255,255,0.12); color:#fff!important; border-color:rgba(255,255,255,0.25); padding:12px 24px; font-size:15px;">Browse All Services</a>
        </div>
    </div>

    <!-- Main Grid: Value Proposition + Lead Form -->
    <div style="display:grid; grid-template-columns: 1fr 1.15fr; gap:36px; align-items:start; margin: 40px 0;" id="lead-form">
        <!-- Left: Why Choose Us -->
        <div>
            <span class="tag" style="margin-bottom:12px;">UK Chartered Practice</span>
            <h2 style="font-family:var(--d); font-size:32px; margin: 8px 0 16px;">Fixed fees, zero surprises, and dedicated client portal</h2>
            <p style="color:var(--mu); font-size:15px; margin-bottom:24px;">Whether you are an established Limited company, an ambitious start-up, or a private taxpayer, AccountAI manages your end-to-end filings with proactive deadline protection.</p>

            <div style="display:flex; flex-direction:column; gap:16px;">
                <div style="display:flex; gap:14px; background:#fff; border:1px solid var(--bd); border-radius:12px; padding:16px;">
                    <div style="width:36px; height:36px; border-radius:10px; background:#12a36b18; color:var(--ok); display:grid; place-items:center; font-weight:700; flex:none;">✓</div>
                    <div>
                        <strong style="display:block; font-size:14px;">Instant Client Portal Access</strong>
                        <small style="color:var(--mu);">Track your document submissions, milestones, and accountant notes in real-time.</small>
                    </div>
                </div>

                <div style="display:flex; gap:14px; background:#fff; border:1px solid var(--bd); border-radius:12px; padding:16px;">
                    <div style="width:36px; height:36px; border-radius:10px; background:#3a4bdc18; color:var(--ac); display:grid; place-items:center; font-weight:700; flex:none;">✓</div>
                    <div>
                        <strong style="display:block; font-size:14px;">Full HMRC &amp; Companies House Compliance</strong>
                        <small style="color:var(--mu);">Making Tax Digital compliant VAT, CT600, RTI Payroll, and SA100 Self Assessment.</small>
                    </div>
                </div>

                <div style="display:flex; gap:14px; background:#fff; border:1px solid var(--bd); border-radius:12px; padding:16px;">
                    <div style="width:36px; height:36px; border-radius:10px; background:#0fb5a618; color:var(--ac2); display:grid; place-items:center; font-weight:700; flex:none;">✓</div>
                    <div>
                        <strong style="display:block; font-size:14px;">Guaranteed Statutory Deadline Filing</strong>
                        <small style="color:var(--mu);">Automated risk-scoring protects you against late filing penalties.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Lead Capture Form -->
        <div class="card">
            <h3 style="font-family:var(--d); font-size:22px; margin:0 0 6px;">Request Your Free Consultation</h3>
            <p style="color:var(--mu); font-size:13px; margin:0 0 20px;">Complete the form below. We will immediately create your client workspace and dispatch proposal details.</p>

            @if(session('lead_success'))
                <div class="note ok">
                    <strong>✓ Success:</strong> {{ session('lead_success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="note er">
                    <strong>Please resolve the following:</strong>
                    <ul style="margin: 6px 0 0 16px; padding:0;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('lead.website') }}">
                @csrf

                <!-- Honeypot anti-spam field -->
                <input type="text" name="website_hp" class="honeypot" tabindex="-1" autocomplete="off">

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" placeholder="Sarah" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" placeholder="Patel" required>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="sarah@example.co.uk" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Phone *</label>
                        <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+44 7700 900123" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Company / Trading Entity Name (Optional)</label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="Acme Trading Ltd">
                </div>

                <!-- Dropdown 1: Business Services -->
                <div class="form-group">
                    <label style="display:flex; justify-content:space-between; align-items:center;">
                        <span>Business Services (Select all that apply)</span>
                        <small style="color:var(--mu); font-weight:normal;">Checkboxes</small>
                    </label>
                    <div class="multiselect-box">
                        @foreach($businessServices as $bService)
                            <label class="multiselect-option">
                                <input type="checkbox" name="business_services[]" value="{{ $bService->id }}"
                                    {{ (is_array(old('business_services')) && in_array($bService->id, old('business_services'))) ? 'checked' : '' }}>
                                <span>{{ $bService->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Dropdown 2: Personal Services -->
                <div class="form-group">
                    <label style="display:flex; justify-content:space-between; align-items:center;">
                        <span>Personal Services (Select all that apply)</span>
                        <small style="color:var(--mu); font-weight:normal;">Checkboxes</small>
                    </label>
                    <div class="multiselect-box">
                        @foreach($personalServices as $pService)
                            <label class="multiselect-option">
                                <input type="checkbox" name="personal_services[]" value="{{ $pService->id }}"
                                    {{ (is_array(old('personal_services')) && in_array($pService->id, old('personal_services'))) ? 'checked' : '' }}>
                                <span>{{ $pService->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn" style="width:100%; padding:13px; font-size:15px; margin-top:10px;">
                    Submit Enquiry &amp; Receive Instant Portal Access &rarr;
                </button>
                <small style="display:block; text-align:center; color:var(--mu); font-size:11.5px; margin-top:8px;">
                    We never share your personal information. Regulated by UK accounting authorities.
                </small>
            </form>
        </div>
    </div>

    <!-- Services Catalogue Section -->
    <div style="margin: 80px 0 30px;" id="services-section">
        <div style="text-align:center; max-width:600px; margin: 0 auto 40px;">
            <span class="tag">Specialist Services</span>
            <h2 style="font-family:var(--d); font-size:32px; margin:10px 0;">Comprehensive UK Accountancy Catalogue</h2>
            <p style="color:var(--mu);">Explore our complete range of tailored packages for companies and private individuals.</p>
        </div>

        <h3 style="font-family:var(--d); font-size:22px; margin-bottom:16px;">Business Services</h3>
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap:16px; margin-bottom:40px;">
            @foreach($businessServices as $service)
                <div class="card" style="padding:18px;">
                    <strong style="font-size:15px; display:block; margin-bottom:6px;">{{ $service->name }}</strong>
                    <p style="color:var(--mu); font-size:12.5px; margin:0 0 12px; min-height:40px;">{{ Str::limit($service->description, 110) }}</p>
                    <a href="{{ route('services.show', $service->slug) }}" class="btn outline sm" style="width:100%;">Read Details &rarr;</a>
                </div>
            @endforeach
        </div>

        <h3 style="font-family:var(--d); font-size:22px; margin-bottom:16px;">Personal &amp; Private Client Services</h3>
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap:16px;">
            @foreach($personalServices as $service)
                <div class="card" style="padding:18px;">
                    <strong style="font-size:15px; display:block; margin-bottom:6px;">{{ $service->name }}</strong>
                    <p style="color:var(--mu); font-size:12.5px; margin:0 0 12px; min-height:40px;">{{ Str::limit($service->description, 110) }}</p>
                    <a href="{{ route('services.show', $service->slug) }}" class="btn outline sm" style="width:100%;">Read Details &rarr;</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
