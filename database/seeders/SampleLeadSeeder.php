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

        // 12 Connected Journey Stages (UK Practice Accounting Framework)
        $stages = [
            1 => [
                'title' => 'Lead & enquiry',
                'description' => 'Lead ID created, source tagged, intent classified and routed to branch. Acknowledgement sent.',
                'status' => 'done',
                'payload' => [
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'email' => 'client@abctrading.co.uk',
                    'phone' => '+44 20 7946 0192',
                    'company_name' => 'ABC Trading Ltd',
                    'source' => 'Website',
                    'message' => 'Looking for complete statutory annual accounts, CT600 Corporation Tax, quarterly MTD VAT returns and director payroll.',
                ],
            ],
            2 => [
                'title' => 'Qualification',
                'description' => 'Questionnaire sent. Suggested annual accounts, corporation tax, VAT or payroll mapped.',
                'status' => 'done',
                'payload' => [
                    'business_structure' => 'Private Limited Company (Ltd)',
                    'services' => ['Statutory Annual Accounts (Companies House / FRS 102 1A & 105)', 'Corporation Tax Return (HMRC CT600)', 'VAT Returns (Making Tax Digital - MTD)', 'Payroll & Workplace Pension (PAYE / RTI / Nest)'],
                    'turnover' => '£185,000',
                    'vat_registered' => 'VAT Registered (Standard 20%)',
                    'employee_count' => '3 employees including 2 directors',
                    'existing_accountant' => 'Changing Accountant (Clearance letter needed)',
                ],
            ],
            3 => [
                'title' => 'Consultation & quote',
                'description' => 'Consultation notes, pricing template and proposal generated. Customer accepts online.',
                'status' => 'done',
                'payload' => [
                    'consultation_format' => 'Microsoft Teams / Zoom Call',
                    'preferred_datetime' => 'Tuesdays 2:00 PM',
                    'tax_priorities' => 'Director salary (£12,570) vs dividend strategy, R&D tax credits and allowable travel expenses',
                    'billing_preference' => 'Monthly Direct Debit Retainer',
                    'target_fee' => '£1,860/yr + VAT (£155/mo)',
                    'quote_status' => 'Quote Q-4417 Accepted Online',
                ],
            ],
            4 => [
                'title' => 'Registration',
                'description' => 'Customer ID and secure portal created. Duplicate accounts prevented.',
                'status' => 'done',
                'payload' => [
                    'crn' => '10482910',
                    'incorporation_date' => '2019-04-18',
                    'jurisdiction' => 'England & Wales',
                    'sic_code' => '62020 - Information technology consultancy',
                    'registered_address' => '124 City Road, London, EC1V 2NX',
                    'directors' => 'John Smith (Director), Sarah Smith (25%+ Shareholder / PSC)',
                ],
            ],
            5 => [
                'title' => 'AML & compliance',
                'description' => 'ID verification and risk assessment. Compliance officer approval is required.',
                'status' => 'done',
                'payload' => [
                    'id_type' => 'UK Passport',
                    'id_number' => '509283719',
                    'dob' => '1984-06-22',
                    'ni_number' => 'QQ 12 34 56 A',
                    'nationality' => 'British / UK Tax Resident',
                    'proof_of_address' => 'Barclays Bank Statement (Dated 15 Oct 2026)',
                    'pep_check' => 'Confirmed NOT a PEP (Standard Risk)',
                ],
            ],
            6 => [
                'title' => 'Engagement',
                'description' => 'Engagement letter generated from approved template and e-signed.',
                'status' => 'done',
                'payload' => [
                    'signatory_name' => 'John Smith',
                    'signatory_title' => 'Managing Director',
                    'year_end' => '31 March',
                    'clearance_status' => 'Clearance Letter Received',
                    'esign_confirmed' => 'Agreed & Signed Electronically',
                ],
            ],
            7 => [
                'title' => 'Payment',
                'description' => 'Invoice and payment link sent. Webhook updates payment status automatically.',
                'status' => 'done',
                'payload' => [
                    'payment_method' => 'BACS / UK Bank Transfer (Faster Payments)',
                    'amount' => '£930.00 (50% upfront deposit)',
                    'invoice_ref' => 'INV-2026-089',
                    'transaction_ref' => 'BACS-84920',
                    'payment_date' => '2026-10-28',
                    'payment_notes' => 'Received via Faster Payments into client settlement account',
                ],
            ],
            8 => [
                'title' => 'Document collection',
                'description' => 'Personalised checklist, OCR classification, duplicate and readability checks.',
                'status' => 'done',
                'payload' => [
                    'accounting_software' => 'Xero',
                    'docs_list' => ['12 Months Business Bank Statements (CSV / PDF / Open Banking)', 'Sales Invoices & Stripe / PayPal / Merchant Summaries', 'Purchase Invoices & Receipts (Dext / Hubdoc / Folder)', 'P60, P11D and Payroll RTI Year-End Summary', 'HMRC Government Gateway 64-8 Agent Authorisation Code'],
                    'drive_url' => 'https://drive.google.com/drive/folders/abc-trading-ltd-2026',
                ],
            ],
            9 => [
                'title' => 'Clarification',
                'description' => 'Automated queries for missing periods and unclear items, linked to the case.',
                'status' => 'done',
                'payload' => [
                    'questions' => 'March bank statement reconciliation gap resolved. Director travel and subsistence expenses validated.',
                    'statutory_deadline' => 'Companies House: 31 Dec 2026 | HMRC CT600: 31 Mar 2027',
                    'clarification_status' => 'All Technical Queries Answered',
                ],
            ],
            10 => [
                'title' => 'Allocation',
                'description' => 'Release checks pass. Case allocated by skill, workload and deadline.',
                'status' => 'done',
                'payload' => [
                    'assigned_branch' => 'AccountAI Clearview Practice (London)',
                    'lead_accountant' => 'Sarah Patel FCCA (Branch Manager)',
                    'complexity' => 'Small Company FRS 102 Section 1A',
                    'target_completion' => '15 November 2026',
                    'urgency' => 'Standard Turnaround',
                ],
            ],
            11 => [
                'title' => 'Processing & review',
                'description' => 'Accountant works in existing accounting software. Reviewer approves.',
                'status' => 'done',
                'payload' => [
                    'accounts_software' => 'IRIS Accounts Production & TaxCalc',
                    'tax_reliefs' => '100% Full Expensing Capital Allowances on IT equipment',
                    'vat_reconciliation' => '£0.00 - Fully Reconciled to HMRC MTD',
                    'draft_sent' => 'Draft Uploaded to Portal & Sent for Approval',
                    'director_approval' => 'Approved & E-Signed by Director',
                ],
            ],
            12 => [
                'title' => 'Completion',
                'description' => 'Delivery confirmed, payment reconciled, next-cycle reminders scheduled.',
                'status' => 'pending',
                'payload' => [
                    'companies_house_status' => 'Accepted by Companies House (Submission Ref: CH-98124)',
                    'hmrc_status' => 'Accepted by HMRC Online Gateway (ACK-2026-004)',
                    'delivery_method' => 'Secure Client Portal Download & PDF Email',
                    'reminders_schedule' => 'Quarterly VAT MTD, Monthly PAYE & Annual Accounts',
                    'feedback' => 'Excellent proactive accountancy support and clear UK corporation tax advice.',
                ],
            ],
        ];

        foreach ($stages as $order => $data) {
            $lead->journeySteps()->updateOrCreate(
                ['step_order' => $order],
                [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'payload' => $data['payload'] ?? null,
                    'status' => $data['status'],
                    'review_status' => $data['status'] === 'done' ? 'approved' : 'action_required',
                    'approved_at' => $data['status'] === 'done' ? now() : null,
                ]
            );
        }
    }
}
