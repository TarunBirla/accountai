<!DOCTYPE html>
<html lang="en-GB" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Client Portal') – AccountAI</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,600&display=swap">
<style>
:root{--bg:#f8fafc;--sf:#ffffff;--s2:#f1f5f9;--tx:#0e1530;--mu:#64708a;--bd:#e2e8f0;--ac:#3a4bdc;--ac2:#0fb5a6;--ok:#12a36b;--wn:#d98a0b;--er:#dc3d4a;--sb:#0a1030;box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px);--f:'Figtree',system-ui,-apple-system,'Segoe UI',sans-serif;--d:'Fraunces',Georgia,serif}
*{box-sizing:border-box}html{scroll-padding-top:env(safe-area-inset-top,0px);background:#f8fafc}
body{margin:0;background:#f8fafc!important;color:#0e1530!important;font:14px/1.5 var(--f)}
button,a.btn,input,select,textarea{font:inherit}
button:focus-visible,input:focus-visible{outline:2px solid var(--ac);outline-offset:2px}
.app{display:grid;grid-template-columns:240px 1fr;min-height:100vh;background:#f8fafc}
aside{background:var(--sb);color:#c7d0ee;padding:20px 12px;position:sticky;top:0;height:100vh;overflow-y:auto;display:flex;flex-direction:column}
.logo{display:flex;gap:10px;align-items:center;padding:4px 10px 22px;color:#fff;text-decoration:none}
.logo i{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#5b6bff,#0fb5a6);display:grid;place-items:center;font-style:normal;font-weight:700;color:#fff;font-size:14px}
.logo b{font-family:var(--d);font-size:17px;line-height:1.1;color:#fff}.logo small{display:block;font:400 11px var(--f);color:#8d99c4}
aside nav{display:flex;flex-direction:column;gap:3px;flex:1}
aside nav a{display:flex;gap:10px;width:100%;align-items:center;background:none;border:0;color:inherit;padding:9px 12px;border-radius:9px;text-align:left;text-decoration:none;font-size:13px;font-weight:500;transition:all .15s}
aside nav a:hover{background:#ffffff12;color:#fff}
aside nav a.on{background:linear-gradient(90deg,#3a4bdc,#3a4bdc99);color:#fff}
aside nav span{width:20px;text-align:center;font-size:15px}
aside .br{margin:18px 6px 0;padding:12px;border:1px solid #ffffff1c;border-radius:12px;font-size:12px;color:#9aa6cf;background:rgba(255,255,255,0.03)}
main{padding:22px 28px 60px;min-width:0;background:#f8fafc}
.top{display:flex;align-items:center;gap:12px;margin-bottom:20px}
.top h1{font:600 24px var(--d);margin:0;flex:1;letter-spacing:-.3px;color:#0e1530}.top h1 small{display:block;font:400 13px var(--f);color:var(--mu);margin-top:2px}
.pill{background:#ffffff;border:1px solid var(--bd);padding:7px 12px;border-radius:99px;font-size:12px;color:var(--mu)}
.av{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#ef4444);color:#fff;display:grid;place-items:center;font-weight:700;font-size:13px}
.g{display:grid;gap:16px}.g4{grid-template-columns:repeat(4,1fr)}.g2{grid-template-columns:1.6fr 1fr}.g3{grid-template-columns:repeat(3,1fr)}
.card{background:#ffffff!important;border:1px solid #e2e8f0;border-radius:14px;padding:18px;box-shadow:0 1px 3px rgba(14,21,48,0.05)}
.card h3{margin:0 0 12px;font-size:15px;display:flex;align-items:center;gap:8px;color:#0e1530}.card h3 em{font-style:normal;margin-left:auto;font-weight:400;color:var(--mu);font-size:12px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;background:var(--ac);color:#fff!important;border:0;border-radius:9px;padding:8px 14px;font-weight:600;font-size:13px;text-decoration:none;cursor:pointer}
.btn:hover{opacity:.92}.btn.s{background:#f8fafc;color:var(--tx)!important;border:1px solid var(--bd)}.btn.w{background:#fff;color:#1b2570!important}.btn.sm{padding:5px 10px;font-size:12px}
.tag{display:inline-block;padding:2px 9px;border-radius:99px;font-size:11px;font-weight:600;background:#3a4bdc1a;color:var(--ac);white-space:nowrap}
.tag.ok{background:#12a36b1f;color:var(--ok)}.tag.wn{background:#d98a0b24;color:var(--wn)}.tag.er{background:#dc3d4a1f;color:var(--er)}.tag.mu{background:#f8fafc;color:var(--mu);border:1px solid var(--bd)}
.bar{height:8px;background:#f1f5f9;border:1px solid var(--bd);border-radius:99px;overflow:hidden}.bar i{display:block;height:100%;background:linear-gradient(90deg,var(--ac),var(--ac2));border-radius:99px;transition:width .5s}
.note{background:#d98a0b1c;border:1px solid #d98a0b55;border-radius:10px;padding:12px 14px;font-size:13px;margin-bottom:14px;color:#0e1530}
.note.ok{background:#12a36b18;border-color:#12a36b50;color:#0e1530}
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:12px;font-weight:600;color:var(--mu);margin-bottom:5px}
.form-control{width:100%;background:#ffffff;border:1px solid var(--bd);color:var(--tx);padding:9px 12px;border-radius:8px;font-size:13px}

/* 12 Stages Horizontal Flow Styling */
.stages-flow {
    display: flex !important;
    flex-direction: row !important;
    gap: 8px !important;
    overflow-x: auto !important;
    padding-bottom: 12px !important;
    margin-bottom: 20px !important;
    align-items: stretch !important;
}
.stage-card {
    flex: 1 1 0% !important;
    min-width: 95px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 12px 6px !important;
    text-align: center !important;
    color: #64708a !important;
    transition: all .2s !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03) !important;
    text-decoration: none !important;
    cursor: pointer !important;
}
.stage-card:hover {
    border-color: #3a4bdc !important;
    transform: translateY(-1px) !important;
}
.stage-card .badge-icon {
    width: 26px !important;
    height: 26px !important;
    border-radius: 50% !important;
    margin-bottom: 6px !important;
    display: grid !important;
    place-items: center !important;
    font-size: 12px !important;
    font-weight: 700 !important;
}
.stage-card.done {
    border-color: #0fb5a655 !important;
    background: #ffffff !important;
}
.stage-card.done .badge-icon {
    background: #0fb5a6 !important;
    color: #ffffff !important;
}
.stage-card.done .stage-title {
    color: #0fb5a6 !important;
    font-weight: 600 !important;
}
.stage-card.active {
    border: 2px solid #3a4bdc !important;
    box-shadow: 0 0 0 3px rgba(58,75,220,0.15) !important;
    background: #ffffff !important;
}
.stage-card.active .badge-icon {
    background: #3a4bdc !important;
    color: #ffffff !important;
}
.stage-card.active .stage-title {
    color: #0e1530 !important;
    font-weight: 700 !important;
}
.stage-card.revision {
    border: 2px solid #d98a0b !important;
    background: #fffdf5 !important;
}
.stage-card.revision .badge-icon {
    background: #d98a0b !important;
    color: #ffffff !important;
}
.stage-card.revision .stage-title {
    color: #d98a0b !important;
    font-weight: 700 !important;
}
.stage-card.pending {
    background: #f8fafc !important;
}
.stage-card.pending .badge-icon {
    background: #e2e8f0 !important;
    color: #64708a !important;
}
.stage-card .stage-title {
    font-size: 11px !important;
    line-height: 1.25 !important;
    text-align: center !important;
}
@media(max-width:900px){.app{grid-template-columns:1fr}aside{position:static;height:auto;padding:10px}aside nav{display:flex;overflow-x:auto;gap:4px}.logo,.br{display:none}.g2,.g3,.g4{grid-template-columns:1fr}main{padding:16px}.top h1{font-size:20px}.pill{display:none}}
</style>
@stack('styles')
</head>
<body>
<div class="app">
    <aside>
        <a href="{{ route('client.dashboard') }}" class="logo">
            <i>AP</i>
            <div>
                <b>AccountAI</b>
                <small>Client portal</small>
            </div>
        </a>
        <nav>
            <a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'on' : '' }}">
                <span>⇄</span> Client Journey
            </a>
            <a href="{{ route('client.requests.index') }}" class="{{ request()->routeIs('client.requests.*') ? 'on' : '' }}">
                <span>📋</span> Action Requests
            </a>
            <a href="{{ route('client.profile') }}" class="{{ request()->routeIs('client.profile') ? 'on' : '' }}">
                <span>👤</span> Account &amp; Security
            </a>
            <a href="{{ route('home') }}" target="_blank">
                <span>✦</span> Practice Services ↗
            </a>
        </nav>
        <div class="br">
            <b style="color:#fff">{{ auth()->user()->name }}</b><br>
            {{ auth()->user()->email }}<br>
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 8px;">
                @csrf
                <button type="submit" class="btn sm s" style="width:100%; justify-content:center; background: rgba(255,255,255,0.08); color: #fff; border: 0;">Sign out</button>
            </form>
        </div>
    </aside>

    <main>
        <div class="top">
            <h1>
                @yield('page_title', 'Client Portal')
                <small>@yield('page_subtitle', 'Track your onboarding and accountancy services')</small>
            </h1>
            <span class="pill" id="clk"></span>
            <button class="pill" id="th" style="cursor: pointer;">Theme</button>
            <div class="av">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
        </div>

        @include('partials.alerts')
        @yield('content')
    </main>
</div>

<script>
setInterval(() => {
    const clk = document.querySelector("#clk");
    if(clk) clk.textContent = new Date().toLocaleString("en-GB", {weekday:"short", day:"numeric", month:"short", hour:"2-digit", minute:"2-digit"});
}, 1000);
// Force light theme as default
document.documentElement.dataset.theme = "light";
localStorage.setItem("theme", "light");
</script>
@stack('scripts')
</body>
</html>
