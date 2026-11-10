<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin Dashboard') – AccountAI Practice OS</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,600&display=swap">
<style>
:root{--bg:#f3f5f9;--sf:#fff;--s2:#f8fafc;--tx:#0e1530;--mu:#64708a;--bd:#e3e8f0;--ac:#3a4bdc;--ac2:#0fb5a6;--ok:#12a36b;--wn:#d98a0b;--er:#dc3d4a;--sb:#0a1030;box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px);--f:'Figtree',system-ui,-apple-system,'Segoe UI',sans-serif;--d:'Fraunces',Georgia,serif}
@media(prefers-color-scheme:dark){:root:not([data-theme=light]){--bg:#070b1a;--sf:#101731;--s2:#0c1229;--tx:#e9edf8;--mu:#8e9ab8;--bd:#1f2a4a;--ac:#7c8bff;--ok:#2fd196;--wn:#f2b14a;--er:#ff6b77}}
:root[data-theme=dark]{--bg:#070b1a;--sf:#101731;--s2:#0c1229;--tx:#e9edf8;--mu:#8e9ab8;--bd:#1f2a4a;--ac:#7c8bff;--ok:#2fd196;--wn:#f2b14a;--er:#ff6b77}
*{box-sizing:border-box}html{scroll-padding-top:env(safe-area-inset-top,0px)}
body{margin:0;background:var(--bg);color:var(--tx);font:14px/1.5 var(--f)}
button,a.btn,input,select,textarea{font:inherit}
button:focus-visible,input:focus-visible,select:focus-visible,textarea:focus-visible{outline:2px solid var(--ac);outline-offset:2px}
.app{display:grid;grid-template-columns:240px 1fr;min-height:100vh}
aside{background:var(--sb);color:#c7d0ee;padding:20px 12px;position:sticky;top:0;height:100vh;overflow-y:auto;display:flex;flex-direction:column}
.logo{display:flex;gap:10px;align-items:center;padding:4px 10px 22px;color:#fff;text-decoration:none}
.logo i{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#5b6bff,#0fb5a6);display:grid;place-items:center;font-style:normal;font-weight:700;color:#fff;font-size:14px}
.logo b{font-family:var(--d);font-size:17px;line-height:1.1;color:#fff}.logo small{display:block;font:400 11px var(--f);color:#8d99c4}
aside nav{display:flex;flex-direction:column;gap:3px;flex:1}
aside nav a,aside nav button.nav-link{display:flex;gap:10px;width:100%;align-items:center;background:none;border:0;color:inherit;padding:9px 12px;border-radius:9px;text-align:left;text-decoration:none;cursor:pointer;font-size:13px;font-weight:500;transition:all .15s}
aside nav a:hover,aside nav button.nav-link:hover{background:#ffffff12;color:#fff}
aside nav a.on{background:linear-gradient(90deg,#3a4bdc,#3a4bdc99);color:#fff}
aside nav span{width:20px;text-align:center;font-size:15px}
aside .br{margin:18px 6px 0;padding:12px;border:1px solid #ffffff1c;border-radius:12px;font-size:12px;color:#9aa6cf;background:rgba(255,255,255,0.03)}
main{padding:22px 28px 60px;min-width:0}
.top{display:flex;align-items:center;gap:12px;margin-bottom:20px}
.top h1{font:600 24px var(--d);margin:0;flex:1;letter-spacing:-.3px}.top h1 small{display:block;font:400 13px var(--f);color:var(--mu);margin-top:2px}
.pill{background:var(--sf);border:1px solid var(--bd);padding:7px 12px;border-radius:99px;font-size:12px;color:var(--mu);display:inline-flex;align-items:center;gap:6px}
.av{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#ef4444);color:#fff;display:grid;place-items:center;font-weight:700;font-size:13px}
.g{display:grid;gap:16px}.g4{grid-template-columns:repeat(4,1fr)}.g2{grid-template-columns:1.6fr 1fr}.g3{grid-template-columns:repeat(3,1fr)}
.card{background:var(--sf);border:1px solid var(--bd);border-radius:14px;padding:18px;box-shadow:0 1px 2px #0b12301a,0 8px 24px -14px #0b123040}
.card h3{margin:0 0 12px;font-size:15px;display:flex;align-items:center;gap:8px}.card h3 em{font-style:normal;margin-left:auto;font-weight:400;color:var(--mu);font-size:12px}
.kpi b{display:block;font:600 30px var(--d);letter-spacing:-.5px;color:var(--tx)}.kpi span{color:var(--mu);font-size:13px}.kpi i{font-style:normal;color:var(--ok);font-size:12px;font-weight:600}
.hero{background:linear-gradient(120deg,#0a1030,#27358f 60%,#0b8f86);color:#fff;border:0;display:flex;gap:20px;align-items:center;flex-wrap:wrap}
.hero h2{font:600 23px/1.2 var(--d);margin:0 0 6px}.hero p{margin:0;color:#c9d3ff;max-width:560px;font-size:13.5px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;background:var(--ac);color:#fff!important;border:0;border-radius:9px;padding:8px 14px;font-weight:600;font-size:13px;text-decoration:none;cursor:pointer;transition:opacity .15s}
.btn:hover{opacity:.92}.btn.s{background:var(--s2);color:var(--tx)!important;border:1px solid var(--bd)}.btn.w{background:#fff;color:#1b2570!important}.btn.sm{padding:5px 10px;font-size:12px}.btn.er{background:var(--er);color:#fff!important}.btn.ok{background:var(--ok);color:#fff!important}
.btn:disabled{opacity:.45;cursor:not-allowed}
table{width:100%;border-collapse:collapse}th{text-align:left;font-weight:600;color:var(--mu);font-size:12px;padding:9px 10px;border-bottom:1px solid var(--bd)}td{padding:11px 10px;border-bottom:1px solid var(--bd);vertical-align:middle;font-size:13px}tr.cl{cursor:pointer}tr.cl:hover td{background:var(--s2)}
.tb{overflow-x:auto}
.tag{display:inline-block;padding:2px 9px;border-radius:99px;font-size:11px;font-weight:600;background:#3a4bdc1a;color:var(--ac);white-space:nowrap}
.tag.ok{background:#12a36b1f;color:var(--ok)}.tag.wn{background:#d98a0b24;color:var(--wn)}.tag.er{background:#dc3d4a1f;color:var(--er)}.tag.mu{background:var(--s2);color:var(--mu);border:1px solid var(--bd)}
.bar{height:8px;background:var(--s2);border:1px solid var(--bd);border-radius:9px;overflow:hidden}.bar i{display:block;height:100%;background:linear-gradient(90deg,var(--ac),var(--ac2));border-radius:9px;transition:width .5s}
.feed{max-height:420px;overflow-y:auto}.feed div{display:flex;gap:10px;padding:9px 0;border-bottom:1px solid var(--bd);font-size:13px}.feed time{color:var(--mu);font-size:11px;white-space:nowrap;margin-left:auto}
.dot{width:8px;height:8px;border-radius:50%;background:var(--ac2);margin-top:6px;flex:none}.dot.h{background:var(--wn)}.dot.e{background:var(--er)}
.steps{display:flex;gap:6px;overflow-x:auto;padding-bottom:6px;margin-bottom:14px}
.steps .step-item{flex:1;min-width:110px;background:var(--s2);border:1px solid var(--bd);border-radius:10px;padding:10px 8px;color:var(--mu);font-size:11.5px;text-align:center}
.steps .step-item b{display:grid;place-items:center;width:24px;height:24px;border-radius:50%;margin:0 auto 5px;background:var(--bd);color:var(--tx);font-size:12px}
.steps .step-item.dn{color:var(--ok);border-color:var(--ok)}.steps .step-item.dn b{background:var(--ok);color:#fff}
.steps .step-item.cu{border-color:var(--ac);color:var(--tx);box-shadow:0 0 0 3px #3a4bdc26}.steps .step-item.cu b{background:var(--ac);color:#fff}
.note{background:#d98a0b1c;border:1px solid #d98a0b55;border-radius:10px;padding:12px 14px;font-size:13px;margin-bottom:14px}
.note.ok{background:#12a36b18;border-color:#12a36b50;color:var(--tx)}
.note.er{background:#dc3d4a18;border-color:#dc3d4a50;color:var(--er)}
.mail{background:var(--s2);border:1px dashed var(--bd);border-radius:10px;padding:12px;font-size:13px;margin-top:12px}
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:12px;font-weight:600;color:var(--mu);margin-bottom:5px}
.form-control{width:100%;background:var(--sf);border:1px solid var(--bd);color:var(--tx);padding:8px 12px;border-radius:8px;font-size:13px}
.form-control:focus{border-color:var(--ac);outline:none}
.pagination-wrap{display:flex;justify-content:space-between;align-items:center;margin-top:15px}
.pagination-wrap nav svg{width:16px;height:16px}
.toast-alert{position:fixed;right:18px;bottom:calc(18px + env(safe-area-inset-bottom,0px));background:#0a1030;color:#fff;padding:12px 18px;border-radius:12px;box-shadow:0 12px 30px #0006;z-index:9999;max-width:380px;animation:slideUp .3s ease-out;display:flex;align-items:center;gap:10px;font-size:13px}
@keyframes slideUp{from{transform:translateY(80px);opacity:0}to{transform:translateY(0);opacity:1}}
@media(max-width:900px){.app{grid-template-columns:1fr}aside{position:static;height:auto;padding:10px}aside nav{display:flex;overflow-x:auto;gap:4px}aside nav a,aside nav button.nav-link{white-space:nowrap;width:auto}.logo,.br{display:none}.g4,.g2,.g3{grid-template-columns:1fr 1fr}.g2{grid-template-columns:1fr}main{padding:16px}.top h1{font-size:20px}.pill{display:none}}
@media(max-width:560px){.g4,.g3{grid-template-columns:1fr}}
</style>
@stack('styles')
</head>
<body>
<div class="app">
    @include('partials.admin-sidebar')

    <main>
        @include('partials.admin-topbar')
        @include('partials.alerts')

        @yield('content')
    </main>
</div>

<script>
const $ = s => document.querySelector(s);
// Live clock
setInterval(() => {
    const clk = $("#clk");
    if(clk) clk.textContent = new Date().toLocaleString("en-GB", {weekday:"short", day:"numeric", month:"short", hour:"2-digit", minute:"2-digit"});
}, 1000);

// Theme Toggle
const thBtn = $("#th");
if (thBtn) {
    thBtn.onclick = () => {
        const r = document.documentElement;
        const currentIsDark = getComputedStyle(r).getPropertyValue("--bg").trim() === "#070b1a" || r.dataset.theme === "dark";
        r.dataset.theme = currentIsDark ? "light" : "dark";
        localStorage.setItem("theme", r.dataset.theme);
    };
}
if (localStorage.getItem("theme")) {
    document.documentElement.dataset.theme = localStorage.getItem("theme");
}
</script>
@stack('scripts')
</body>
</html>
