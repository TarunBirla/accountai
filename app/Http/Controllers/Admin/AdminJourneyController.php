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
        $query = Lead::with(['journeySteps', 'services'])->latest();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('company_name', 'like', "%{$s}%");
            });
        }

        $leads = $query->paginate(15)->withQueryString();

        $selectedLeadId = $request->query('lead_id');
        $selectedLead = null;

        // ONLY show the 12 stages details if a specific lead_id is requested!
        if ($selectedLeadId) {
            $selectedLead = Lead::with(['journeySteps', 'services'])->find($selectedLeadId);
            if ($selectedLead) {
                $this->ensureTwelveStages($selectedLead);
                $selectedLead->load('journeySteps');
            }
        }

        return view('admin.journey.index', compact('leads', 'selectedLead'));
    }

    public function show($id)
    {
        return redirect()->route('admin.journey.index', ['lead_id' => $id]);
    }

    public function updateStepStatus(Request $request, $stepId)
    {
        $request->validate([
            'status' => ['required', 'in:pending,submitted,done,needs_revision'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $step = JourneyStep::with('lead.journeySteps')->findOrFail($stepId);

        $updateData = [];

        if ($request->status === 'done') {
            $updateData['status'] = 'done';
            $updateData['review_status'] = 'approved';
            $updateData['approved_at'] = now();
            $updateData['admin_notes'] = null; // Clear revision note on approval
        } elseif ($request->status === 'needs_revision') {
            $updateData['status'] = 'pending'; // Safe for SQLite CHECK (status in ('pending', 'done'))
            $updateData['review_status'] = 'revision_required';
            $updateData['admin_notes'] = $request->admin_notes;
        } else {
            $updateData['status'] = 'pending';
            $updateData['review_status'] = $request->status;
        }

        $step->update($updateData);

        // Check if all 12 steps done -> convert lead
        $allDone = $step->lead->journeySteps->every(fn($s) => $s->id == $step->id ? $request->status === 'done' : ($s->status === 'done' || $s->review_status === 'approved'));
        if ($allDone && $step->lead->status !== 'converted') {
            $step->lead->update(['status' => 'converted']);
        }

        $msg = $request->status === 'done'
            ? "Stage '{$step->title}' approved successfully."
            : "Stage '{$step->title}' marked as needs revision. Note sent to client.";

        return back()->with('success', $msg);
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
            if ($existing) {
                if ($existing->title !== $stageData['title']) {
                    $existing->update([
                        'title' => $stageData['title'],
                        'description' => $stageData['description'],
                    ]);
                }
            } else {
                $lead->journeySteps()->create([
                    'title' => $stageData['title'],
                    'description' => $stageData['description'],
                    'status' => $order === 1 ? 'done' : 'pending',
                    'review_status' => $order === 1 ? 'approved' : 'pending',
                    'step_order' => $order,
                ]);
            }
        }
    }
}
