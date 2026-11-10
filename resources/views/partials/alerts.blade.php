@if(session('success'))
    <div class="note ok" style="display:flex; justify-content:space-between; align-items:center;">
        <span><strong>✓ Success:</strong> {{ session('success') }}</span>
        <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; font-size:16px; cursor:pointer;">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="note er" style="display:flex; justify-content:space-between; align-items:center;">
        <span><strong>✕ Error:</strong> {{ session('error') }}</span>
        <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; font-size:16px; cursor:pointer;">&times;</button>
    </div>
@endif

@if($errors->any())
    <div class="note er">
        <strong>Please check the following issues:</strong>
        <ul style="margin: 6px 0 0 16px; padding: 0;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif
