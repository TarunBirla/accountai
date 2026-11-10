<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Mail\AdminNewLeadMail;
use App\Mail\ClientWelcomeMail;
use App\Models\Lead;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PublicLeadController extends Controller
{
    public function home()
    {
        $businessServices = Service::active()->business()->get();
        $personalServices = Service::active()->personal()->get();

        return view('public.home', compact('businessServices', 'personalServices'));
    }

    public function social()
    {
        $businessServices = Service::active()->business()->get();
        $personalServices = Service::active()->personal()->get();

        return view('public.social', compact('businessServices', 'personalServices'));
    }

    public function serviceDetail($slug)
    {
        $service = Service::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $relatedServices = Service::active()->where('id', '!=', $service->id)->limit(4)->get();

        return view('public.service-detail', compact('service', 'relatedServices'));
    }

    public function storeWebsite(StoreLeadRequest $request)
    {
        return $this->processLead($request, 'website');
    }

    public function storeSocial(StoreLeadRequest $request)
    {
        return $this->processLead($request, 'social');
    }

    protected function processLead(StoreLeadRequest $request, string $source)
    {
        $validated = $request->validated();

        $lead = DB::transaction(function () use ($validated, $source) {
            $user = User::where('email', $validated['email'])->first();
            $plainPassword = '';
            $isNewUser = false;

            if (!$user) {
                $plainPassword = Str::random(10);
                $user = User::create([
                    'name' => trim("{$validated['first_name']} {$validated['last_name']}"),
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'company' => $validated['company_name'] ?? null,
                    'password' => Hash::make($plainPassword),
                    'role' => 'client',
                ]);
                $isNewUser = true;
            }

            $lead = Lead::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'company_name' => $validated['company_name'] ?? null,
                'source' => $source,
                'status' => 'new',
                'notes' => 'Enquiry submitted via ' . ucfirst($source) . ' form on ' . now()->format('d M Y H:i'),
            ]);

            $servicesToSync = array_filter(array_merge(
                $validated['business_services'] ?? [],
                $validated['personal_services'] ?? []
            ));

            if (!empty($servicesToSync)) {
                $lead->services()->sync($servicesToSync);
            }

            // Exactly 12 Connected Journey Stages (as per AccountAI Practice OS design)
            $stages = [
                ['title' => 'Lead & enquiry', 'description' => 'Lead ID created, source tagged, intent classified and routed to branch. Acknowledgement sent.', 'status' => 'done', 'review_status' => 'approved', 'approved_at' => now(), 'step_order' => 1],
                ['title' => 'Qualification', 'description' => 'Questionnaire sent. Suggested annual accounts, corporation tax, VAT or payroll mapped.', 'status' => 'pending', 'review_status' => 'action_required', 'step_order' => 2],
                ['title' => 'Consultation & quote', 'description' => 'Consultation notes, pricing template and proposal generated. Customer accepts online.', 'status' => 'pending', 'review_status' => 'locked', 'step_order' => 3],
                ['title' => 'Registration', 'description' => 'Customer ID and secure portal created. Duplicate accounts prevented.', 'status' => 'pending', 'review_status' => 'locked', 'step_order' => 4],
                ['title' => 'AML & compliance', 'description' => 'ID verification and risk assessment. Compliance officer approval is required.', 'status' => 'pending', 'review_status' => 'locked', 'step_order' => 5],
                ['title' => 'Engagement', 'description' => 'Engagement letter generated from approved template and e-signed.', 'status' => 'pending', 'review_status' => 'locked', 'step_order' => 6],
                ['title' => 'Payment', 'description' => 'Invoice and payment link sent. Webhook updates payment status automatically.', 'status' => 'pending', 'review_status' => 'locked', 'step_order' => 7],
                ['title' => 'Document collection', 'description' => 'Personalised checklist, OCR classification, duplicate and readability checks.', 'status' => 'pending', 'review_status' => 'locked', 'step_order' => 8],
                ['title' => 'Clarification', 'description' => 'Automated queries for missing periods and unclear items, linked to the case.', 'status' => 'pending', 'review_status' => 'locked', 'step_order' => 9],
                ['title' => 'Allocation', 'description' => 'Release checks pass. Case allocated by skill, workload and deadline.', 'status' => 'pending', 'review_status' => 'locked', 'step_order' => 10],
                ['title' => 'Processing & review', 'description' => 'Accountant works in existing accounting software. Reviewer approves.', 'status' => 'pending', 'review_status' => 'locked', 'step_order' => 11],
                ['title' => 'Completion', 'description' => 'Delivery confirmed, payment reconciled, next-cycle reminders scheduled.', 'status' => 'pending', 'review_status' => 'locked', 'step_order' => 12],
            ];

            foreach ($stages as $stageData) {
                $lead->journeySteps()->create($stageData);
            }

            $lead->load('services');

            // Send Welcome Email directly to Client via SMTP
            try {
                Mail::to($lead->email)->send(new ClientWelcomeMail($lead, $plainPassword, $isNewUser));
            } catch (\Throwable $e) {
                Log::error('ClientWelcomeMail error: ' . $e->getMessage());
            }

            // Send Notification Email directly to Admin via SMTP (configured via ADMIN_MAIL)
            $adminEmail = env('ADMIN_MAIL', env('ADMIN_NOTIFICATION_EMAIL', 'tarun@thenexteck.com'));
            try {
                Mail::to($adminEmail)->send(new AdminNewLeadMail($lead));
            } catch (\Throwable $e) {
                Log::error('AdminNewLeadMail error: ' . $e->getMessage());
            }

            return $lead;
        });

        return back()->with('lead_success', 'Thank you, ' . $lead->first_name . '! Your enquiry has been received. Check your email (' . $lead->email . ') for login credentials and client portal access.');
    }
}
