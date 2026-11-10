<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\JourneyStep;
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

        if ($selectedLead && $selectedLead->journeySteps->count() < 12) {
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
                if (!$selectedLead->journeySteps->firstWhere('step_order', $order)) {
                    $selectedLead->journeySteps()->create([
                        'title' => $stageData['title'],
                        'description' => $stageData['description'],
                        'status' => $order === 1 ? 'done' : 'pending',
                        'review_status' => $order === 1 ? 'approved' : 'pending',
                        'step_order' => $order,
                    ]);
                }
            }
            $selectedLead->load('journeySteps');
        }

        // Determine which of the 12 steps the client is viewing / filling
        $steps = $selectedLead ? $selectedLead->journeySteps->sortBy('step_order') : collect();
        $requestedStepOrder = (int) $request->query('step', 0);

        if ($requestedStepOrder > 0 && $steps->firstWhere('step_order', $requestedStepOrder)) {
            $currentStep = $steps->firstWhere('step_order', $requestedStepOrder);
        } else {
            // Default to first pending or revision-required step, or step 1
            $currentStep = $steps->firstWhere('status', 'needs_revision')
                ?? $steps->firstWhere('status', 'pending')
                ?? $steps->firstWhere('status', 'submitted')
                ?? $steps->first();
        }

        return view('client.dashboard', compact('user', 'leads', 'selectedLead', 'steps', 'currentStep'));
    }

    public function saveStepData(Request $request, $stepId)
    {
        $user = Auth::user();
        $step = JourneyStep::with('lead')->findOrFail($stepId);

        if (!$step->lead || $step->lead->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this journey step.');
        }

        // Collect all parameters without strict "required" blockers
        $payload = $request->except(['_token', '_method']);

        $step->update([
            'payload' => $payload,
            'status' => 'submitted',
            'review_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return back()->with('success', "Stage {$step->step_order} ('{$step->title}') details saved successfully and submitted to your accountant for review.");
    }
}
