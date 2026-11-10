<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $businessServices = [
            'Business accounts' => 'Comprehensive annual accounts preparation and filing for limited companies, partnerships and sole traders.',
            'Corporation Tax' => 'Expert CT600 preparation, capital allowance optimisation, and HMRC tax liability computations.',
            'VAT returns' => 'Making Tax Digital compliant quarterly VAT calculations, filings, and reconciliations.',
            'Bookkeeping' => 'Day-to-day transaction matching, bank reconciliation, and general ledger maintenance.',
            'Payroll & HR' => 'RTI compliant payroll processing, auto-enrolment pensions, payslips, and P60/P45 filings.',
            'Tax planning' => 'Strategic corporate tax structuring to maximise allowances, reliefs, and profit extraction.',
            'R&D tax credits' => 'Identify qualifying innovation costs and claim valuable HMRC research & development relief.',
            'Cash-flow management' => 'Detailed 13-week cash flow forecasts, working capital monitoring, and runway management.',
            'Business plans' => 'Bank-ready financial models and investor-grade pitch documents with detailed pro-formas.',
            'Making Tax Digital' => 'HMRC MTD transition, cloud accounting software setup, and continuous digital compliance.',
            'Tax investigations' => 'Professional representation, negotiation, and penalty mitigation for HMRC audits and enquiries.',
            'Business advisory' => 'Quarterly management accounts, board reporting, KPI dashboards, and virtual CFO guidance.',
        ];

        $personalServices = [
            'Self Assessment' => 'Fast, error-free SA100 filing for sole traders, freelancers, high earners, and company directors.',
            'Capital Gains Tax' => '60-day residential property disposal reporting, asset gains optimisation, and relief claims.',
            'Property income tax' => 'Section 24 mortgage interest relief calculations, buy-to-let portfolios, and FHL deductions.',
            'Personal tax planning' => 'Estate, inheritance, dividend, and remuneration planning tailored to your financial goals.',
            'Cryptocurrency tax' => 'Trading pooling, staking income, DeFi yields, and crypto disposals calculated according to HMRC manual.',
            'Director tax returns' => 'Harmonised dividend vs salary optimisation, P11D benefits, and director loan tax treatment.',
            'Pension-related tax' => 'Annual allowance, lifetime allowance protection, and tapered allowance management.',
            'Employment tax' => 'PAYE tax code reviews, multiple employment reconciliations, and employment benefits advisory.',
        ];

        foreach ($businessServices as $name => $desc) {
            Service::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'type' => 'business',
                    'name' => $name,
                    'description' => $desc,
                    'is_active' => true,
                ]
            );
        }

        foreach ($personalServices as $name => $desc) {
            Service::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'type' => 'personal',
                    'name' => $name,
                    'description' => $desc,
                    'is_active' => true,
                ]
            );
        }
    }
}
