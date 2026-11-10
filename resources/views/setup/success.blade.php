<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<title>AccountAI – Installation Complete</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;600;700&family=Fraunces:opsz,wght@9..144,600&display=swap">
<style>
    :root { --bg: #f3f5f9; --tx: #0e1530; --ac: #3a4bdc; --ok: #12a36b; --bd: #e3e8f0; --sf: #fff; }
    body { font-family: 'Figtree', sans-serif; background: var(--bg); color: var(--tx); margin: 0; padding: 40px 20px; display: grid; place-items: center; min-height: 100vh; }
    .card { background: var(--sf); border: 1px solid var(--bd); border-radius: 16px; padding: 36px; max-width: 650px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
    h1 { font-family: 'Fraunces', Georgia, serif; font-size: 28px; margin: 0 0 10px; color: var(--ok); }
    pre { background: #0a1030; color: #a5b4fc; padding: 18px; border-radius: 8px; font-size: 12px; overflow-x: auto; }
    .btn { display: inline-block; background: var(--ac); color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; margin-top: 15px; }
</style>
</head>
<body>
<div class="card">
    <h1>✓ AccountAI Installed Successfully</h1>
    <p>Database migrations and seeders have executed. The installation lock file <code>storage/installed.lock</code> has been created to prevent re-execution.</p>

    <h3>Artisan Output:</h3>
    <pre>{{ $output }}</pre>

    <div style="margin-top: 25px; display: flex; gap: 12px;">
        <a href="{{ route('login') }}" class="btn">Go to Login</a>
        <a href="{{ route('home') }}" class="btn" style="background: #64708a;">Go to Website</a>
    </div>
</div>
</body>
</html>
