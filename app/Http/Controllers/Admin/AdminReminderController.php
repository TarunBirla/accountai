<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminReminderController extends Controller
{
    public function index(Request $request)
    {
        $query = Reminder::with(['lead', 'creator'])->latest('remind_at');

        $filter = $request->query('filter', 'pending');

        if ($filter === 'pending') {
            $query->where('is_done', false);
        } elseif ($filter === 'today') {
            $query->where('is_done', false)->whereDate('remind_at', Carbon::today());
        } elseif ($filter === 'overdue') {
            $query->where('is_done', false)->where('remind_at', '<', Carbon::now());
        } elseif ($filter === 'done') {
            $query->where('is_done', true);
        }

        $reminders = $query->paginate(15)->withQueryString();
        $leads = Lead::orderBy('first_name')->get();

        return view('admin.reminders.index', compact('reminders', 'leads', 'filter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => ['nullable', 'exists:leads,id'],
            'title' => ['required', 'string', 'max:255'],
            'remind_at' => ['required', 'date'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_done'] = false;

        Reminder::create($validated);

        return back()->with('success', 'Reminder created successfully.');
    }

    public function toggleDone($id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->update(['is_done' => !$reminder->is_done]);

        $statusText = $reminder->is_done ? 'completed' : 'reopened';
        return back()->with('success', "Reminder marked as {$statusText}.");
    }

    public function destroy($id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->delete();

        return back()->with('success', 'Reminder deleted.');
    }
}
