@extends('layouts.public')

@section('title', $service->name . ' – UK Accountancy Service | AccountAI')

@section('content')
<div class="container" style="max-width: 900px; margin-top: 40px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('home') }}#services-section" class="btn outline sm">&larr; Back to All Services</a>
    </div>

    <div class="card" style="padding: 36px; border-radius: 20px;">
        <span class="tag {{ $service->type === 'business' ? '' : 'wn' }}" style="margin-bottom: 12px;">
            {{ ucfirst($service->type) }} Accountancy Service
        </span>
        <h1 style="font-family:var(--d); font-size: 34px; margin: 10px 0 16px;">
            {{ $service->name }}
        </h1>
        <p style="font-size: 17px; line-height: 1.7; color: #2a3a55; margin-bottom: 28px;">
            {{ $service->description }}
        </p>

        <div style="background: var(--s2); border: 1px solid var(--bd); border-radius: 14px; padding: 24px; margin-bottom: 28px;">
            <h3 style="font-family: var(--d); font-size: 19px; margin: 0 0 12px;">Included Deliverables &amp; Guarantees</h3>
            <ul style="margin: 0; padding-left: 20px; color: var(--tx); line-height: 1.8; font-size: 14px;">
                <li>Dedicated UK-qualified accountant handling preparation and HMRC communications.</li>
                <li>Full Making Tax Digital (MTD) and Companies House electronic submission.</li>
                <li>Digital document upload and interactive progress tracking via your secure Client Portal.</li>
                <li>Statutory deadline monitoring with proactive reminder schedules.</li>
            </ul>
        </div>

        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('home') }}#lead-form" class="btn" style="padding: 12px 24px; font-size: 14px;">
                Request a Proposal for {{ $service->name }} &rarr;
            </a>
            <a href="{{ route('social') }}" class="btn outline" style="padding: 12px 24px; font-size: 14px;">
                Fast Track Consultation
            </a>
        </div>
    </div>

    @if($relatedServices->isNotEmpty())
        <div style="margin-top: 40px;">
            <h3 style="font-family:var(--d); font-size:22px; margin-bottom:16px;">Related Accountancy Services</h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap:16px;">
                @foreach($relatedServices as $rel)
                    <div class="card" style="padding: 18px;">
                        <span class="tag {{ $rel->type === 'business' ? '' : 'wn' }}" style="font-size: 10px; margin-bottom: 6px;">{{ ucfirst($rel->type) }}</span>
                        <strong style="display:block; font-size: 14px; margin-bottom: 6px;">{{ $rel->name }}</strong>
                        <p style="color:var(--mu); font-size: 12px; margin: 0 0 10px;">{{ Str::limit($rel->description, 70) }}</p>
                        <a href="{{ route('services.show', $rel->slug) }}" style="color:var(--ac); text-decoration:none; font-size:12px; font-weight:600;">Learn More &rarr;</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
