<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\AdminNewLeadMail;
use App\Models\JourneyStep;
use App\Models\Lead;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        if ($selectedLead) {
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
                $existingStep = $selectedLead->journeySteps->firstWhere('step_order', $order);
                if ($existingStep) {
                    // Normalize title and description if it had old legacy names
                    if ($existingStep->title !== $stageData['title']) {
                        $existingStep->update([
                            'title' => $stageData['title'],
                            'description' => $stageData['description'],
                        ]);
                    }
                } else {
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
            // Default to first revision-required or pending or submitted step, or step 1
            $currentStep = $steps->firstWhere('review_status', 'revision_required')
                ?? $steps->firstWhere('status', 'needs_revision')
                ?? $steps->firstWhere('status', 'pending')
                ?? $steps->firstWhere('review_status', 'submitted')
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
            'status' => 'pending', // Keeps SQLite CHECK constraint valid
            'review_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return back()->with('success', "Stage {$step->step_order} ('{$step->title}') details saved successfully and submitted to your accountant for review.");
    }

    public function createRequest()
    {
        $user = Auth::user();
        $businessServices = Service::active()->business()->get();
        $personalServices = Service::active()->personal()->get();
        $recentLeads = Lead::where('user_id', $user->id)->with('services')->latest()->take(5)->get();

        return view('client.new-request', compact('user', 'businessServices', 'personalServices', 'recentLeads'));
    }

    public function storeRequest(Request $request)
    {
        $user = Auth::user();

        $lead = DB::transaction(function () use ($request, $user) {
            $nameParts = explode(' ', trim($user->name), 2);
            $firstName = $nameParts[0] ?? 'Valued';
            $lastName = $nameParts[1] ?? 'Client';

            $companyName = $request->input('company_name') ?: ($user->company ?: null);

            $lead = Lead::create([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $user->email,
                'phone' => $request->input('phone') ?: ($user->phone ?: 'N/A'),
                'company_name' => $companyName,
                'source' => 'client_portal',
                'status' => 'new',
                'notes' => $request->input('notes') ?: ('New service request submitted from Client Portal on ' . now()->format('d M Y H:i')),
            ]);

            $serviceIds = array_filter(array_merge(
                (array) $request->input('business_services', []),
                (array) $request->input('personal_services', [])
            ));

            if (!empty($serviceIds)) {
                $lead->services()->sync($serviceIds);
            }

            // Exactly 12 UK Journey Stages
            $stages = [
                [
                    'title' => 'Lead & enquiry',
                    'description' => 'Lead ID created via client portal, service scope tagged. Initial acknowledgment logged.',
                    'status' => 'done',
                    'review_status' => 'approved',
                    'approved_at' => now(),
                    'step_order' => 1,
                    'payload' => [
                        'source' => 'client_portal',
                        'business_structure' => $request->input('business_structure'),
                        'turnover' => $request->input('turnover'),
                        'vat_status' => $request->input('vat_status'),
                        'employee_count' => $request->input('employee_count'),
                        'urgency' => $request->input('urgency'),
                        'notes' => $request->input('notes'),
                        'requested_at' => now()->toIso8601String(),
                    ],
                ],
                [
                    'title' => 'Qualification',
                    'description' => 'Questionnaire sent. Suggested annual accounts, corporation tax, VAT or payroll mapped.',
                    'status' => 'pending',
                    'review_status' => 'action_required',
                    'step_order' => 2,
                ],
                [
                    'title' => 'Consultation & quote',
                    'description' => 'Consultation notes, pricing template and proposal generated. Customer accepts online.',
                    'status' => 'pending',
                    'review_status' => 'locked',
                    'step_order' => 3,
                ],
                [
                    'title' => 'Registration',
                    'description' => 'Customer ID and secure portal created. Duplicate accounts prevented.',
                    'status' => 'pending',
                    'review_status' => 'locked',
                    'step_order' => 4,
                ],
                [
                    'title' => 'AML & compliance',
                    'description' => 'ID verification and risk assessment. Compliance officer approval is required.',
                    'status' => 'pending',
                    'review_status' => 'locked',
                    'step_order' => 5,
                ],
                [
                    'title' => 'Engagement',
                    'description' => 'Engagement letter generated from approved template and e-signed.',
                    'status' => 'pending',
                    'review_status' => 'locked',
                    'step_order' => 6,
                ],
                [
                    'title' => 'Payment',
                    'description' => 'Invoice and payment link sent. Webhook updates payment status automatically.',
                    'status' => 'pending',
                    'review_status' => 'locked',
                    'step_order' => 7,
                ],
                [
                    'title' => 'Document collection',
                    'description' => 'Personalised checklist, OCR classification, duplicate and readability checks.',
                    'status' => 'pending',
                    'review_status' => 'locked',
                    'step_order' => 8,
                ],
                [
                    'title' => 'Clarification',
                    'description' => 'Automated queries for missing periods and unclear items, linked to the case.',
                    'status' => 'pending',
                    'review_status' => 'locked',
                    'step_order' => 9,
                ],
                [
                    'title' => 'Allocation',
                    'description' => 'Release checks pass. Case allocated by skill, workload and deadline.',
                    'status' => 'pending',
                    'review_status' => 'locked',
                    'step_order' => 10,
                ],
                [
                    'title' => 'Processing & review',
                    'description' => 'Accountant works in existing accounting software. Reviewer approves.',
                    'status' => 'pending',
                    'review_status' => 'locked',
                    'step_order' => 11,
                ],
                [
                    'title' => 'Completion',
                    'description' => 'Delivery confirmed, payment reconciled, next-cycle reminders scheduled.',
                    'status' => 'pending',
                    'review_status' => 'locked',
                    'step_order' => 12,
                ],
            ];

            foreach ($stages as $stageData) {
                $lead->journeySteps()->create($stageData);
            }

            $lead->load('services');

            // Notify Admin by email
            $adminEmail = env('ADMIN_MAIL', env('ADMIN_NOTIFICATION_EMAIL', 'tarun@thenexteck.com'));
            try {
                Mail::to($adminEmail)->send(new AdminNewLeadMail($lead));
            } catch (\Throwable $e) {
                Log::error('AdminNewLeadMail from client portal error: ' . $e->getMessage());
            }

            return $lead;
        });

        return redirect()->route('client.dashboard', ['lead_id' => $lead->id, 'step' => 2])
            ->with('success', 'Your new service enquiry #' . $lead->id . ' has been created successfully! You can now complete Stage 2 details below or wait for your dedicated practice accountant.');
    }
}
