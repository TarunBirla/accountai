<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Reminder;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalLeads = Lead::count();
        $websiteLeads = Lead::where('source', 'website')->count();
        $socialLeads = Lead::where('source', 'social')->count();

        $statusCounts = [
            'new' => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'in_progress' => Lead::where('status', 'in_progress')->count(),
            'converted' => Lead::where('status', 'converted')->count(),
            'closed' => Lead::where('status', 'closed')->count(),
        ];

        $recentLeads = Lead::with('services')->latest()->limit(7)->get();

        $urgentReminders = Reminder::with('lead')
            ->where('is_done', false)
            ->where('remind_at', '<=', Carbon::now()->endOfDay())
            ->orderBy('remind_at', 'asc')
            ->limit(5)
            ->get();

        $totalServices = Service::count();

        return view('admin.dashboard', compact(
            'totalLeads',
            'websiteLeads',
            'socialLeads',
            'statusCounts',
            'recentLeads',
            'urgentReminders',
            'totalServices'
        ));
    }
}
