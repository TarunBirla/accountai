<div class="top">
    <h1>
        @yield('page_title', 'Practice overview')
        <small>@yield('page_subtitle', 'Every customer, every deadline, one view')</small>
    </h1>
    <span class="pill" id="clk"></span>
    <button class="pill" id="th" aria-label="Toggle theme" style="cursor: pointer;">Theme</button>
    <div class="av" title="{{ auth()->user()->name }}">
        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
    </div>
</div>
