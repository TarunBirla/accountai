<aside>
    <a href="{{ route('admin.dashboard') }}" class="logo">
        <i>AP</i>
        <div>
            <b>AccountAI OS</b>
            <small>Operations platform</small>
        </div>
    </a>

    <nav>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'on' : '' }}">
            <span>▦</span> Dashboard
        </a>
        <a href="{{ route('admin.leads.index') }}" class="{{ request()->routeIs('admin.leads.*') ? 'on' : '' }}">
            <span>✦</span> Leads &amp; AI Intake
        </a>
        <a href="{{ route('admin.journey.index') }}" class="{{ request()->routeIs('admin.journey.*') ? 'on' : '' }}">
            <span>⇄</span> Client Journey
        </a>
        <a href="{{ route('admin.reminders.index') }}" class="{{ request()->routeIs('admin.reminders.*') ? 'on' : '' }}">
            <span>◔</span> Reminders &amp; Deadlines
        </a>
        <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'on' : '' }}">
            <span>▤</span> Services Catalogue
        </a>
        <a href="{{ route('home') }}" target="_blank">
            <span>◧</span> Public Website ↗
        </a>
    </nav>

    <div class="br">
        <b style="color:#fff">Practice HQ · London</b><br>
        Admin: {{ auth()->user()->name }}<br>
        <form method="POST" action="{{ route('logout') }}" style="margin-top: 8px;">
            @csrf
            <button type="submit" class="btn sm s" style="width:100%; justify-content:center; background: rgba(255,255,255,0.08); color: #fff; border: 0;">Sign out</button>
        </form>
    </div>
</aside>
