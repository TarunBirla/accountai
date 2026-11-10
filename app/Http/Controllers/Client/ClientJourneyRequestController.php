<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\JourneyStep;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientJourneyRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $leads = Lead::where('user_id', $user->id)
            ->with(['services', 'journeySteps'])
            ->latest()
            ->get();

        $selectedLeadId = $request->query('lead_id');
        $selectedLead = $selectedLeadId
            ? $leads->firstWhere('id', $selectedLeadId) ?? $leads->first()
            : $leads->first();

        $activeStep = null;
        $historySteps = collect();

        if ($selectedLead) {
            // Find step awaiting client action or under review
            $activeStep = $selectedLead->journeySteps
                ->where('status', 'pending')
                ->whereIn('review_status', ['action_required', 'revision_required', 'submitted'])
                ->first();

            // Fallback: first pending step
            if (!$activeStep) {
                $activeStep = $selectedLead->journeySteps->firstWhere('status', 'pending');
                if ($activeStep && $activeStep->review_status === 'locked') {
                    $activeStep->update(['review_status' => 'action_required']);
                }
            }

            $historySteps = $selectedLead->journeySteps->where('status', 'done')->sortBy('step_order');
        }

        return view('client.requests.index', compact('user', 'leads', 'selectedLead', 'activeStep', 'historySteps'));
    }

    public function submit(Request $request, $stepId)
    {
        $user = Auth::user();

        $step = JourneyStep::whereHas('lead', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->findOrFail($stepId);

        $payload = $request->except(['_token', '_method']);

        $step->update([
            'payload' => $payload,
            'status' => 'pending', // Keeps SQLite CHECK constraint valid (status in 'pending', 'done')
            'review_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $nextStepOrder = min(12, $step->step_order + 1);

        return redirect()->route('client.dashboard', [
            'lead_id' => $step->lead_id,
            'step' => $step->step_order < 12 ? $nextStepOrder : 12,
        ])->with('success', "Stage {$step->step_order} ('{$step->title}') saved & submitted successfully. " . ($step->step_order < 12 ? "Proceeding to Stage {$nextStepOrder}." : "All 12 stages submitted."));
    }
}
