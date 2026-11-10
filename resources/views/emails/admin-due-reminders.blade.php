<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f3f5f9; color: #0e1530; margin: 0; padding: 20px; }
    .box { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; border: 1px solid #e3e8f0; padding: 24px; }
    .item { padding: 12px; border: 1px solid #e3e8f0; border-radius: 8px; margin-bottom: 10px; }
    .overdue { border-left: 4px solid #dc3d4a; background: #fff5f5; }
    .today { border-left: 4px solid #d98a0b; background: #fffdf5; }
    .btn { display: inline-block; background: #3a4bdc; color: #fff !important; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; margin-top: 20px; }
</style>
</head>
<body>
<div class="box">
    <h2>AccountAI Reminder Digest</h2>
    <p>The following {{ $reminders->count() }} action items are due today or overdue:</p>

    @foreach($reminders as $r)
        <div class="item {{ $r->isOverdue() ? 'overdue' : 'today' }}">
            <strong style="color: {{ $r->isOverdue() ? '#dc3d4a' : '#d98a0b' }};">
                {{ $r->isOverdue() ? 'OVERDUE' : 'DUE TODAY' }}
            </strong>: {{ $r->title }}
            <br>
            <small>
                <strong>Lead:</strong> {{ $r->lead ? $r->lead->full_name . ' (' . ($r->lead->company_name ?? 'Individual') . ')' : 'General' }} |
                <strong>Due:</strong> {{ $r->remind_at->format('d M Y H:i') }}
            </small>
        </div>
    @endforeach

    <div style="margin-top: 24px; text-align: center;">
        <a href="{{ route('admin.reminders.index') }}" class="btn">View All Reminders in Practice Dashboard</a>
    </div>
</div>
</body>
</html>
