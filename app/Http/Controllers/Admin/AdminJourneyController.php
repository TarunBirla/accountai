<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JourneyStep;
use App\Models\Lead;
use Illuminate\Http\Request;

class AdminJourneyController extends Controller
{
    public function index()
    {
        $leads = Lead::with(['journeySteps', 'services'])->latest()->paginate(15);

        return view('admin.journey.index', compact('leads'));
    }

    public function updateStepStatus(Request $request, $stepId)
    {
        $request->validate([
            'status' => ['required', 'in:pending,done'],
        ]);

        $step = JourneyStep::with('lead.journeySteps')->findOrFail($stepId);
        $step->update(['status' => $request->status]);

        // If all steps done, optionally set lead to converted
        $allDone = $step->lead->journeySteps->every(fn($s) => $s->id == $step->id ? $request->status === 'done' : $s->status === 'done');
        if ($allDone && $step->lead->status !== 'converted') {
            $step->lead->update(['status' => 'converted']);
        }

        return back()->with('success', "Journey step '{$step->title}' updated to " . ucfirst($step->status));
    }
}
