@extends('layouts.client')

@section('title', 'My Profile & Security – AccountAI')
@section('page_title', 'Security & Profile')
@section('page_subtitle', 'Manage your account credentials and personal details')

@section('content')
<div class="g g2">
    <!-- Left: Profile info -->
    <div class="card">
        <h3>Client Profile Details</h3>
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" class="form-control" value="{{ $user->name }}" disabled>
        </div>
        <div class="form-group">
            <label>Registered Email</label>
            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
        </div>
        <div class="form-group">
            <label>Telephone</label>
            <input type="text" class="form-control" value="{{ $user->phone ?: 'Not provided' }}" disabled>
        </div>
        <div class="form-group">
            <label>Company / Trading Entity</label>
            <input type="text" class="form-control" value="{{ $user->company ?: 'Sole Trader / Personal' }}" disabled>
        </div>
        <p style="color:var(--mu); font-size:12px; margin-bottom:0;">
            To modify contact details or company registration numbers, please contact your assigned accountant.
        </p>
    </div>

    <!-- Right: Change password -->
    <div class="card">
        <h3>Change Password <em>Keep your portal secure</em></h3>
        
        <form method="POST" action="{{ route('client.profile.password') }}">
            @csrf

            <div class="form-group">
                <label>Current / Temporary Password *</label>
                <input type="password" name="current_password" class="form-control" required placeholder="Enter current password">
            </div>

            <div class="form-group">
                <label>New Password (min 8 characters) *</label>
                <input type="password" name="password" class="form-control" required placeholder="Enter new strong password">
            </div>

            <div class="form-group">
                <label>Confirm New Password *</label>
                <input type="password" name="password_confirmation" class="form-control" required placeholder="Repeat new password">
            </div>

            <button type="submit" class="btn" style="width:100%; margin-top:10px;">Update Password</button>
        </form>
    </div>
</div>
@endsection
