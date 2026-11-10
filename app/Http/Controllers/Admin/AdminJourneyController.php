<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JourneyStep;
use App\Models\Lead;
use Illuminate\Http\Request;

class AdminJourneyController extends Controller
{
    /**
     * Requirement 2: Clean User List first on /admin/journey
     */
    public function index(Request $request)
    {
        $query = Lead::with(['journeySteps', 'services', 'user'])->latest();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('company_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leads = $query->paginate(15)->withQueryString();

        return view('admin.journey.index', compact('leads'));
    }

    /**
     * Requirement 2: Dedicated 12 Stages View for a single lead
     */
    public function show($leadId)
    {
        $lead = Lead::with(['journeySteps' => function ($q) {
            $q->orderBy('step_order', 'asc');
        }, 'services', 'user'])->findOrFail($leadId);

        // Ensure lead has all 12 stages
        if ($lead->journeySteps->count() < 12) {
            $this->ensureTwelveStages($lead);
            $lead->load('journeySteps');
        }

        $activeStep = $lead->journeySteps->firstWhere('status', 'pending');

        return view('admin.journey.show', compact('lead', 'activeStep'));
    }

    /**
     * Requirement 1: Approve Step or Request Revision with Note
     */
    public function reviewStep(Request $request, $stepId)
    {
        $request->validate([
            'action' => ['required', 'in:approve,revision'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $step = JourneyStep::with('lead.journeySteps')->findOrFail($stepId);
        $lead = $step->lead;

        if ($request->action === 'approve') {
            $step->update([
                'status' => 'done',
                'review_status' => 'approved',
                'approved_at' => now(),
                'admin_notes' => $request->admin_notes ?: $step->admin_notes,
            ]);

            // Unlock next stage for the client
            $nextStep = $lead->journeySteps->firstWhere('step_order', $step->step_order + 1);
            if ($nextStep) {
                $nextStep->update([
                    'review_status' => 'action_required',
                ]);
            } else {
                // All 12 completed!
                $lead->update(['status' => 'converted']);
            }

            return back()->with('success', "Stage '{$step->title}' approved! Next stage is now unlocked for the client.");
        }

        if ($request->action === 'revision') {
            $request->validate([
                'admin_notes' => ['required', 'string', 'min:3'],
            ], [
                'admin_notes.required' => 'Please provide feedback notes explaining what the client needs to correct.',
            ]);

            $step->update([
                'review_status' => 'revision_required',
                'admin_notes' => $request->admin_notes,
            ]);

            return back()->with('success', "Stage '{$step->title}' returned to client for revision with your feedback note.");
        }

        return back();
    }

    /**
     * Fallback manual status toggle
     */
    public function updateStepStatus(Request $request, $stepId)
    {
        $request->validate([
            'status' => ['required', 'in:pending,done'],
        ]);

        $step = JourneyStep::with('lead.journeySteps')->findOrFail($stepId);
        $newStatus = $request->status;

        $step->update([
            'status' => $newStatus,
            'review_status' => $newStatus === 'done' ? 'approved' : 'action_required',
            'approved_at' => $newStatus === 'done' ? now() : null,
        ]);

        if ($newStatus === 'done') {
            $nextStep = $step->lead->journeySteps->firstWhere('step_order', $step->step_order + 1);
            if ($nextStep) {
                $nextStep->update(['review_status' => 'action_required']);
            }
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
                    'review_status' => $order === 1 ? 'approved' : ($order === 2 ? 'action_required' : 'locked'),
                    'step_order' => $order,
                    'approved_at' => $order === 1 ? now() : null,
                ]);
            }
        }
    }
}
