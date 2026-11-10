<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Sign In – AccountAI Practice OS</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,600&display=swap">
<style>
:root{--bg:#f3f5f9;--sf:#fff;--tx:#0e1530;--mu:#64708a;--bd:#e3e8f0;--ac:#3a4bdc;--sb:#0a1030;--er:#dc3d4a;--ok:#12a36b;--f:'Figtree',sans-serif;--d:'Fraunces',Georgia,serif}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--tx);font:14px/1.5 var(--f);display:grid;place-items:center;min-height:100vh;padding:20px}
.login-card{background:var(--sf);border:1px solid var(--bd);border-radius:20px;padding:38px 36px;width:100%;max-width:420px;box-shadow:0 12px 40px rgba(10,16,48,0.08)}
.logo-center{display:flex;flex-direction:column;align-items:center;margin-bottom:24px;text-align:center}
.logo-icon{width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,#5b6bff,#0fb5a6);display:grid;place-items:center;font-weight:700;color:#fff;font-size:18px;margin-bottom:10px}
.logo-center h1{font-family:var(--d);font-size:24px;margin:0;color:var(--tx)}
.logo-center p{margin:4px 0 0;color:var(--mu);font-size:13px}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:12px;font-weight:600;color:var(--mu);margin-bottom:6px}
.form-control{width:100%;padding:10px 14px;border:1px solid var(--bd);border-radius:9px;font-size:14px;color:var(--tx);background:#fff}
.form-control:focus{border-color:var(--ac);outline:none;box-shadow:0 0 0 3px rgba(58,75,220,0.15)}
.btn{display:inline-flex;align-items:center;justify-content:center;width:100%;background:var(--ac);color:#fff;padding:12px;border:0;border-radius:9px;font-weight:600;font-size:14px;cursor:pointer;margin-top:8px;transition:background .15s}
.btn:hover{background:#2e3dc5}
.alert{background:#dc3d4a15;border:1px solid #dc3d4a40;color:var(--er);padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px}
.alert-ok{background:#12a36b15;border:1px solid #12a36b40;color:var(--ok);padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px}
.eye-toggle{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--mu);display:grid;place-items:center;padding:6px;border-radius:6px;transition:color .15s}
.eye-toggle:hover{color:var(--tx)}
</style>
</head>
<body>
<div class="login-card">
    <div class="logo-center">
        <div class="logo-icon">AP</div>
        <h1>AccountAI Practice OS</h1>
        <p>Client Portal &amp; Staff Administration</p>
    </div>

    @if(session('success'))
        <div class="alert-ok">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div class="form-group">
            <label>Registered Email Address</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="admin@accountai.com" required autofocus>
        </div>

        <div class="form-group">
            <label>Password</label>
            <div style="position: relative;">
                <input type="password" id="passwordInput" name="password" class="form-control" placeholder="••••••••" style="padding-right: 44px;" required>
                <button type="button" id="togglePasswordBtn" class="eye-toggle" aria-label="Toggle password visibility">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; font-size:13px;">
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span>Remember me</span>
            </label>
        </div>

        <button type="submit" class="btn">Sign In to Dashboard &rarr;</button>
    </form>

    <div style="text-align:center; margin-top:24px;">
        <a href="{{ route('home') }}" style="color:var(--mu); text-decoration:none; font-size:12px;">&larr; Back to Public Website</a>
    </div>
</div>

<script>
const toggleBtn = document.getElementById('togglePasswordBtn');
const pwdInput = document.getElementById('passwordInput');
const eyeIcon = document.getElementById('eyeIcon');

if (toggleBtn && pwdInput) {
    toggleBtn.addEventListener('click', function () {
        const isPassword = pwdInput.type === 'password';
        pwdInput.type = isPassword ? 'text' : 'password';

        if (isPassword) {
            // Slashed eye (hide)
            eyeIcon.innerHTML = `
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            `;
            toggleBtn.setAttribute('aria-label', 'Hide password');
        } else {
            // Open eye (show)
            eyeIcon.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            `;
            toggleBtn.setAttribute('aria-label', 'Show password');
        }
    });
}
</script>
</body>
</html>
