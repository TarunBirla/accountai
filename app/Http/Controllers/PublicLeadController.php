<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Mail\AdminNewLeadMail;
use App\Mail\ClientWelcomeMail;
use App\Models\Lead;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
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

            // Default 5 Journey Steps
            $steps = [
                ['title' => 'Lead Received', 'description' => 'Enquiry captured and initial acknowledgment issued.', 'status' => 'done', 'step_order' => 1],
                ['title' => 'Contacted', 'description' => 'Accountant initial consultation and requirements discovery.', 'status' => 'pending', 'step_order' => 2],
                ['title' => 'Documents Collected', 'description' => 'Submission of necessary accounts, P60s, bank records, or invoices.', 'status' => 'pending', 'step_order' => 3],
                ['title' => 'In Progress', 'description' => 'Active accounts computation, HMRC filing preparation, and review.', 'status' => 'pending', 'step_order' => 4],
                ['title' => 'Completed', 'description' => 'Signed off, submitted to HMRC/Companies House, and reconciled.', 'status' => 'pending', 'step_order' => 5],
            ];

            foreach ($steps as $stepData) {
                $lead->journeySteps()->create($stepData);
            }

            // Eager load services for emails
            $lead->load('services');

            // 5. Send Welcome email to Client
            try {
                Mail::to($lead->email)->queue(new ClientWelcomeMail($lead, $plainPassword, $isNewUser));
            } catch (\Throwable $e) {
                Log::error('Failed to queue ClientWelcomeMail: ' . $e->getMessage());
            }

            // 6. Send Alert email to Admin
            $adminEmail = config('mail.admin_address', env('ADMIN_NOTIFICATION_EMAIL', 'admin@accountai.com'));
            try {
                Mail::to($adminEmail)->queue(new AdminNewLeadMail($lead));
            } catch (\Throwable $e) {
                Log::error('Failed to queue AdminNewLeadMail: ' . $e->getMessage());
            }

            return $lead;
        });

        return back()->with('lead_success', 'Thank you, ' . $lead->first_name . '! Your enquiry has been received. Check your email for confirmation and Client Portal login details.');
    }
}
