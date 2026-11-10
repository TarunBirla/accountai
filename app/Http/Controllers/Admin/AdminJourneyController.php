<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JourneyStep;
use App\Models\Lead;
use Illuminate\Http\Request;

class AdminJourneyController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::with(['journeySteps', 'services'])->latest()->paginate(15);

        $selectedLeadId = $request->query('lead_id');
        $selectedLead = $selectedLeadId 
            ? Lead::with(['journeySteps', 'services'])->find($selectedLeadId) 
            : $leads->first();

        // If selected lead exists but has fewer than 12 steps (e.g. from early testing), ensure all 12 stages exist
        if ($selectedLead && $selectedLead->journeySteps->count() < 12) {
            $this->ensureTwelveStages($selectedLead);
            $selectedLead->load('journeySteps');
        }

        return view('admin.journey.index', compact('leads', 'selectedLead'));
    }

    public function updateStepStatus(Request $request, $stepId)
    {
        $request->validate([
            'status' => ['required', 'in:pending,done'],
        ]);

        $step = JourneyStep::with('lead.journeySteps')->findOrFail($stepId);
        $step->update(['status' => $request->status]);

        // If all 12 steps done, set lead to converted
        $allDone = $step->lead->journeySteps->every(fn($s) => $s->id == $step->id ? $request->status === 'done' : $s->status === 'done');
        if ($allDone && $step->lead->status !== 'converted') {
            $step->lead->update(['status' => 'converted']);
        }

        return back()->with('success', "Stage '{$step->title}' updated to " . ucfirst($step->status));
    }

    protected function ensureTwelveStages(Lead $lead): void
    {
        $stages = [
            1 => ['title' => 'Lead & enquiry', 'description' => 'Lead ID created, source tagged, intent classified and routed to branch. Acknowledgement sent.'],
            2 => ['title' => 'Qualification', 'description' => 'Questionnaire sent. Suggested annual accounts, corporation tax, VAT or payroll mapped.'],
            3 => ['title' => 'Consultation & quote', 'description' => 'Consultation notes, pricing template and proposal generated. Customer accepts online.'],
            4 => ['title' => 'Registration', 'description' => 'Customer ID and secure portal created. Duplicate accounts prevented.'],
            5 => ['title' => 'AML & compliance', 'description' => 'ID verification and risk assessment. Compliance officer approval is required.'],
            6 => ['title' => 'Engagement', 'description' => 'Engagement letter generated from approved template and e-signed.'],
            7 => ['title' => 'Payment', 'description' => 'Invoice and payment link sent. Webhook updates payment status automatically.'],
            8 => ['title' => 'Document collection', 'description' => 'Personalised checklist, OCR classification, duplicate and readability checks.'],
            9 => ['title' => 'Clarification', 'description' => 'Automated queries for missing periods and unclear items, linked to the case.'],
            10 => ['title' => 'Allocation', 'description' => 'Release checks pass. Case allocated by skill, workload and deadline.'],
            11 => ['title' => 'Processing & review', 'description' => 'Accountant works in existing accounting software. Reviewer approves.'],
            12 => ['title' => 'Completion', 'description' => 'Delivery confirmed, payment reconciled, next-cycle reminders scheduled.'],
        ];

        foreach ($stages as $order => $stageData) {
            $existing = $lead->journeySteps->firstWhere('step_order', $order);
            if (!$existing) {
                $lead->journeySteps()->create([
                    'title' => $stageData['title'],
                    'description' => $stageData['description'],
                    'status' => $order === 1 ? 'done' : 'pending',
                    'step_order' => $order,
                ]);
            }
        }
    }
}
