<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>AccountAI – Automated Setup &amp; Installation</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,600&display=swap">
<style>
:root {
    --bg: #f3f5f9;
    --sf: #ffffff;
    --s2: #f8fafc;
    --tx: #0e1530;
    --mu: #64708a;
    --bd: #e3e8f0;
    --ac: #3a4bdc;
    --ok: #12a36b;
    --wn: #d98a0b;
    --er: #dc3d4a;
    --f: 'Figtree', sans-serif;
    --d: 'Fraunces', Georgia, serif;
}
* { box-sizing: border-box; }
body {
    margin: 0;
    background: var(--bg);
    color: var(--tx);
    font: 14px/1.6 var(--f);
    padding: 40px 20px;
    display: grid;
    place-items: center;
    min-height: 100vh;
}
.setup-container {
    max-width: 740px;
    width: 100%;
    background: var(--sf);
    border: 1px solid var(--bd);
    border-radius: 20px;
    padding: 36px;
    box-shadow: 0 14px 40px rgba(14,21,48,0.08);
}
.header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--bd);
}
.logo-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #5b6bff, #0fb5a6);
    display: grid;
    place-items: center;
    color: #fff;
    font-weight: 700;
    font-size: 16px;
}
h1 {
    font-family: var(--d);
    font-size: 26px;
    margin: 0;
    line-height: 1.1;
}
.subtitle {
    color: var(--mu);
    font-size: 13px;
    margin-top: 3px;
}
.step-item {
    display: flex;
    gap: 14px;
    padding: 12px 14px;
    border: 1px solid var(--bd);
    border-radius: 10px;
    background: var(--s2);
    margin-bottom: 10px;
    align-items: center;
}
.badge {
    padding: 3px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.badge.success { background: #12a36b1f; color: var(--ok); }
.badge.warning { background: #d98a0b22; color: var(--wn); }
.badge.error { background: #dc3d4a20; color: var(--er); }
.badge.info { background: #3a4bdc1a; color: var(--ac); }
.badge.locked { background: #64708a20; color: var(--mu); }
.credentials-box {
    background: linear-gradient(135deg, #0a1030, #1f2b6e);
    color: #fff;
    border-radius: 14px;
    padding: 20px 24px;
    margin: 24px 0;
}
.credentials-box h3 {
    margin: 0 0 10px;
    font-size: 16px;
    color: #0fb5a6;
}
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: var(--ac);
    color: #fff !important;
    text-decoration: none;
    font-weight: 600;
    padding: 12px 20px;
    border-radius: 9px;
    font-size: 14px;
    transition: opacity .15s;
}
.btn:hover { opacity: .9; }
.btn.outline {
    background: #fff;
    color: var(--tx) !important;
    border: 1px solid var(--bd);
}
.btn.outline:hover { background: var(--s2); }
.error-box {
    background: #dc3d4a15;
    border: 1px solid #dc3d4a40;
    color: var(--er);
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 20px;
}
</style>
</head>
<body>
<div class="setup-container">
    <div class="header">
        <div class="logo-icon">AP</div>
        <div>
            <h1>AccountAI System Installer</h1>
            <div class="subtitle">One-Hit Auto-Setup: Database, Migrations, Seeders &amp; Cache</div>
        </div>
    </div>

    @if(!$success)
        <div class="error-box">
            <h3 style="margin: 0 0 6px 0;">✕ Setup Notice</h3>
            <div>{{ $error }}</div>
        </div>
    @elseif(!empty($alreadyInstalled))
        <div style="background: #12a36b15; border: 1px solid #12a36b40; color: var(--ok); border-radius: 10px; padding: 16px; margin-bottom: 20px;">
            <h3 style="margin: 0 0 6px 0;">✓ System Ready</h3>
            <p style="margin: 0 0 10px 0; color: var(--tx);">{{ $message }}</p>
        </div>
    @else
        <div style="background: #12a36b15; border: 1px solid #12a36b40; color: var(--ok); border-radius: 10px; padding: 16px; margin-bottom: 20px;">
            <h3 style="margin: 0 0 6px 0;">✓ All Commands Executed Successfully</h3>
            <div style="color: var(--tx); font-size: 13.5px;">Your database, tables, default admin, and services catalogue are ready for live operation.</div>
        </div>
    @endif

    <h3 style="margin: 20px 0 12px; font-size: 15px;">Automated Execution Log</h3>
    @foreach($steps as $step)
        <div class="step-item">
            <span class="badge {{ $step['status'] }}">{{ $step['status'] }}</span>
            <div style="flex:1;">
                <strong style="display:block; font-size:13.5px;">{{ $step['title'] }}</strong>
                <small style="color: var(--mu);">{{ $step['detail'] }}</small>
            </div>
        </div>
    @endforeach

    <div class="credentials-box">
        <h3>Default Administrator Access</h3>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; font-size:13.5px; margin-top:8px;">
            <div>
                <span style="color:#c9d3ff; display:block; font-size:12px;">Admin Email</span>
                <strong>admin@accountai.com</strong>
            </div>
            <div>
                <span style="color:#c9d3ff; display:block; font-size:12px;">Default Password</span>
                <code>admin@123</code>
            </div>
        </div>
        <small style="display:block; color:#8ea0d6; margin-top:10px;">For security, change this password immediately after your first sign in.</small>
    </div>

    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top: 24px;">
        <a href="{{ route('login') }}" class="btn">Sign In to Admin Panel &rarr;</a>
        <a href="{{ route('home') }}" class="btn outline">Visit Public Website</a>
        <a href="{{ route('setup.install', ['key' => request('key', env('SETUP_KEY', 'ACCOUNT_AI_SECURE_INSTALL_KEY_2026')), 'force' => 1]) }}" class="btn outline" style="margin-left:auto; font-size:12px;">Re-run Setup (&amp;force=1)</a>
    </div>
</div>
</body>
</html>
