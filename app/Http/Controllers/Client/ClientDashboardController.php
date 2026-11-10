<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $leads = Lead::where('user_id', $user->id)
            ->with(['services', 'journeySteps'])
            ->latest()
            ->get();

        $activeLeadId = $request->query('lead_id');
        $selectedLead = $activeLeadId
            ? $leads->firstWhere('id', $activeLeadId) ?? $leads->first()
            : $leads->first();

        return view('client.dashboard', compact('user', 'leads', 'selectedLead'));
    }
}
