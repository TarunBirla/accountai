@extends('layouts.public')

@section('title', 'Fast-Track Accounting Quote – AccountAI Social')

@section('content')
<div class="container" style="max-width: 860px; margin-top: 30px;">
    <!-- High-impact Social Landing Hero -->
    <div style="text-align: center; margin-bottom: 30px;">
        <span class="tag" style="background:#0fb5a622; color:var(--ac2); font-size:13px; padding: 4px 14px;">⚡ Special Campaign Offer</span>
        <h1 style="font-family:var(--d); font-size:clamp(30px, 4.5vw, 44px); line-height: 1.15; margin: 12px 0;">
            Fast-Track Your UK Company Accounts &amp; Tax Returns
        </h1>
        <p style="color:var(--mu); font-size:17px; max-width:620px; margin: 0 auto;">
            Get proactive tax savings, fast filing turnaround, and fixed transparent pricing. Complete this 60-second form to claim your consultation and instant Client Portal setup.
        </p>
    </div>

    <div class="card" style="box-shadow: 0 10px 40px rgba(11,18,48,0.09); border-radius: 20px; padding: 32px;">
        @if(session('lead_success'))
            <div class="note ok">
                <strong>✓ Submission Confirmed:</strong> {{ session('lead_success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="note er">
                <strong>Please correct the following:</strong>
                <ul style="margin: 6px 0 0 16px; padding: 0;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('lead.social') }}">
            @csrf

            <!-- Honeypot anti-spam field -->
            <input type="text" name="website_hp" class="honeypot" tabindex="-1" autocomplete="off">

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px;">
                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" placeholder="Last Name" required>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px;">
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="name@company.co.uk" required>
                </div>
                <div class="form-group">
                    <label>Mobile Number *</label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+44 7..." required>
                </div>
            </div>

            <div class="form-group">
                <label>Company / Sole Trader Name</label>
                <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="Company Name (optional)">
            </div>

            <!-- Business Services Multi-select -->
            <div class="form-group">
                <label>Select Business Services (Check all you need):</label>
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

            <!-- Personal Services Multi-select -->
            <div class="form-group">
                <label>Select Personal / Director Services:</label>
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

            <button type="submit" class="btn" style="width:100%; padding:14px; font-size:16px; margin-top:10px; background:linear-gradient(90deg, #3a4bdc, #0fb5a6);">
                Claim Free Accountancy Consultation &amp; Portal Login &rarr;
            </button>
        </form>
    </div>
</div>
@endsection
