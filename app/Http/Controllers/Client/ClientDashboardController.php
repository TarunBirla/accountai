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
                        'step_order' => $order,
                    ]);
                }
            }
            $selectedLead->load('journeySteps');
        }

        return view('client.dashboard', compact('user', 'leads', 'selectedLead'));
    }
}
