<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'AccountAI – Modern UK Accountancy Practice')</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,600&display=swap">
<style>
:root{--bg:#f3f5f9;--sf:#fff;--s2:#f8fafc;--tx:#0e1530;--mu:#64708a;--bd:#e3e8f0;--ac:#3a4bdc;--ac2:#0fb5a6;--ok:#12a36b;--wn:#d98a0b;--er:#dc3d4a;--sb:#0a1030;--f:'Figtree',system-ui,-apple-system,sans-serif;--d:'Fraunces',Georgia,serif}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--tx);font:15px/1.6 var(--f);-webkit-font-smoothing:antialiased}
.container{max-width:1160px;margin:0 auto;padding:0 24px}
header{background:#fff;border-bottom:1px solid var(--bd);position:sticky;top:0;z-index:100}
.header-inner{display:flex;align-items:center;justify-content:space-between;height:72px}
.logo{display:flex;gap:10px;align-items:center;text-decoration:none;color:var(--tx)}
.logo i{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#5b6bff,#0fb5a6);display:grid;place-items:center;font-style:normal;font-weight:700;color:#fff;font-size:15px}
.logo b{font-family:var(--d);font-size:19px;line-height:1.1}.logo small{display:block;font:400 11px var(--f);color:var(--mu)}
nav.public-nav{display:flex;align-items:center;gap:24px}
nav.public-nav a{text-decoration:none;color:var(--tx);font-weight:500;font-size:14px;transition:color .15s}
nav.public-nav a:hover{color:var(--ac)}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;background:var(--ac);color:#fff!important;border:0;border-radius:9px;padding:9px 18px;font-weight:600;font-size:13.5px;text-decoration:none;cursor:pointer;transition:transform .15s,box-shadow .15s}
.btn:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(58,75,220,0.25)}
.btn.outline{background:#fff;color:var(--tx)!important;border:1px solid var(--bd)}
.btn.outline:hover{background:var(--s2)}
.btn.sm{padding:6px 12px;font-size:12px}
.hero-box{background:linear-gradient(130deg,#0a1030,#1e2a78 60%,#0b8f86);color:#fff;border-radius:24px;padding:56px 48px;margin:40px 0 30px}
.hero-box h1{font:600 clamp(28px,4vw,46px)/1.15 var(--d);margin:0 0 16px;color:#fff}
.hero-box p{font-size:17px;color:#c9d3ff;max-width:680px;margin:0 0 28px}
.card{background:var(--sf);border:1px solid var(--bd);border-radius:16px;padding:26px;box-shadow:0 4px 20px -4px rgba(11,18,48,0.06)}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:12.5px;font-weight:600;color:var(--mu);margin-bottom:6px}
.form-control{width:100%;background:var(--sf);border:1px solid var(--bd);color:var(--tx);padding:10px 14px;border-radius:9px;font-size:14px;transition:border-color .15s}
.form-control:focus{border-color:var(--ac);outline:none;box-shadow:0 0 0 3px rgba(58,75,220,0.15)}
.multiselect-box{background:var(--s2);border:1px solid var(--bd);border-radius:10px;padding:12px;max-height:165px;overflow-y:auto}
.multiselect-option{display:flex;align-items:center;gap:10px;padding:6px 4px;font-size:13px;cursor:pointer}
.multiselect-option:hover{color:var(--ac)}
.multiselect-option input[type=checkbox]{accent-color:var(--ac);width:16px;height:16px}
.note{background:#d98a0b1c;border:1px solid #d98a0b55;border-radius:10px;padding:12px 16px;font-size:13.5px;margin-bottom:16px}
.note.ok{background:#12a36b18;border-color:#12a36b50;color:#0c6b45}
.note.er{background:#dc3d4a18;border-color:#dc3d4a50;color:var(--er)}
.tag{display:inline-block;padding:3px 10px;border-radius:99px;font-size:12px;font-weight:600;background:#3a4bdc1a;color:var(--ac)}
footer{background:#0a1030;color:#8d99c4;padding:48px 0 32px;margin-top:70px}
footer a{color:#c7d0ee;text-decoration:none}footer a:hover{color:#fff}
.honeypot{position:absolute!important;left:-9999px!important;top:-9999px!important;opacity:0!important;height:0!important;width:0!important}
@media(max-width:768px){.hero-box{padding:32px 24px}nav.public-nav{gap:12px}}
</style>
@stack('styles')
</head>
<body>

<header>
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="logo">
            <i>AP</i>
            <div>
                <b>AccountAI</b>
                <small>Chartered Practice OS</small>
            </div>
        </a>

        <nav class="public-nav">
            <a href="{{ route('home') }}">Home</a>
            <a href="#services-section">Services</a>
            <a href="{{ route('social') }}">Fast Track</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn sm">Admin Dashboard</a>
                @else
                    <a href="{{ route('client.dashboard') }}" class="btn sm">Client Portal</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn sm outline"> Login</a>
            @endauth
        </nav>
    </div>
</header>

@yield('content')

<footer>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:28px;">
            <div class="logo" style="color:#fff;">
                <i>AP</i>
                <div>
                    <b style="color:#fff;">AccountAI Practice Ltd</b>
                    <small style="color:#8d99c4;">UK Registered Accountancy Firm</small>
                </div>
            </div>
            <div style="display:flex; gap:20px; font-size:13px;">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('social') }}">Social Funnel</a>
                <a href="{{ route('login') }}">Client Portal</a>
            </div>
        </div>
        <div style="padding-top:24px; font-size:12px; color:#64708a; display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div>&copy; {{ date('Y') }} AccountAI Practice Ltd. Authorised &amp; Regulated in the United Kingdom.</div>
            <div>Companies House &amp; HMRC MTD Compliant Practice</div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
