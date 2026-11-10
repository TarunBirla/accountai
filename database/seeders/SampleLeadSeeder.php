<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleLeadSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Demo Client User: ABC Trading Ltd
        $clientUser = User::updateOrCreate(
            ['email' => 'client@abctrading.co.uk'],
            [
                'name' => 'John Smith',
                'phone' => '+44 20 7946 0192',
                'company' => 'ABC Trading Ltd',
                'password' => Hash::make('password'),
                'role' => 'client',
            ]
        );

        // 2. Demo Lead: ABC Trading Ltd
        $lead = Lead::updateOrCreate(
            ['email' => 'client@abctrading.co.uk'],
            [
                'user_id' => $clientUser->id,
                'first_name' => 'John',
                'last_name' => 'Smith',
                'phone' => '+44 20 7946 0192',
                'company_name' => 'ABC Trading Ltd',
                'source' => 'website',
                'status' => 'in_progress',
                'notes' => 'Annual accounts, Corporation Tax and VAT return package. Fast-track onboarding.',
            ]
        );

        // Attach relevant services
        $serviceIds = Service::whereIn('slug', ['business-accounts', 'corporation-tax', 'vat-returns'])->pluck('id');
        $lead->services()->sync($serviceIds);

        // 12 Connected Journey Stages
        $stages = [
            1 => ['title' => 'Lead & enquiry', 'description' => 'Lead ID created, source tagged, intent classified and routed to branch. Acknowledgement sent.', 'status' => 'done'],
            2 => ['title' => 'Qualification', 'description' => 'Questionnaire sent. Suggested annual accounts, corporation tax, VAT or payroll mapped.', 'status' => 'done'],
            3 => ['title' => 'Consultation & quote', 'description' => 'Consultation notes, pricing template and proposal generated. Customer accepts online.', 'status' => 'done'],
            4 => ['title' => 'Registration', 'description' => 'Customer ID and secure portal created. Duplicate accounts prevented.', 'status' => 'done'],
            5 => ['title' => 'AML & compliance', 'description' => 'ID verification and risk assessment. Compliance officer approval is required.', 'status' => 'done'],
            6 => ['title' => 'Engagement', 'description' => 'Engagement letter generated from approved template and e-signed.', 'status' => 'done'],
            7 => ['title' => 'Payment', 'description' => 'Invoice and payment link sent. Webhook updates payment status automatically.', 'status' => 'done'],
            8 => ['title' => 'Document collection', 'description' => 'Personalised checklist, OCR classification, duplicate and readability checks.', 'status' => 'done'],
            9 => ['title' => 'Clarification', 'description' => 'Automated queries for missing periods and unclear items, linked to the case.', 'status' => 'done'],
            10 => ['title' => 'Allocation', 'description' => 'Release checks pass. Case allocated by skill, workload and deadline.', 'status' => 'done'],
            11 => ['title' => 'Processing & review', 'description' => 'Accountant works in existing accounting software. Reviewer approves.', 'status' => 'done'],
            12 => ['title' => 'Completion', 'description' => 'Delivery confirmed, payment reconciled, next-cycle reminders scheduled.', 'status' => 'pending'],
        ];

        foreach ($stages as $order => $data) {
            $lead->journeySteps()->updateOrCreate(
                ['step_order' => $order],
                [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                    'review_status' => $data['status'] === 'done' ? 'approved' : 'action_required',
                    'approved_at' => $data['status'] === 'done' ? now() : null,
                ]
            );
        }
    }
}
