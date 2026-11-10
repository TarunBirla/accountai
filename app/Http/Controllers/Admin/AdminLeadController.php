<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminLeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with(['services', 'user'])->latest();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('company_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_id')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('services.id', $request->service_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $leads = $query->paginate(12)->withQueryString();
        $services = Service::active()->orderBy('name')->get();

        return view('admin.leads.index', compact('leads', 'services'));
    }

    public function show($id)
    {
        $lead = Lead::with(['services', 'user', 'journeySteps', 'reminders' => function ($q) {
            $q->latest();
        }])->findOrFail($id);

        if ($lead->journeySteps->count() < 12) {
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
                if (!$lead->journeySteps->firstWhere('step_order', $order)) {
                    $lead->journeySteps()->create([
                        'title' => $stageData['title'],
                        'description' => $stageData['description'],
                        'status' => $order === 1 ? 'done' : 'pending',
                        'step_order' => $order,
                    ]);
                }
            }
            $lead->load('journeySteps');
        }

        return view('admin.leads.show', compact('lead'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:new,contacted,in_progress,converted,closed'],
        ]);

        $lead = Lead::findOrFail($id);
        $lead->update(['status' => $request->status]);

        return back()->with('success', "Lead #{$lead->id} status updated to " . ucfirst(str_replace('_', ' ', $lead->status)));
    }

    public function updateNotes(Request $request, $id)
    {
        $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $lead = Lead::findOrFail($id);
        $lead->update(['notes' => $request->notes]);

        return back()->with('success', "Lead notes updated successfully.");
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Lead::with(['services'])->latest();

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('service_id')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('services.id', $request->service_id);
            });
        }

        $leads = $query->get();
        $fileName = 'leads-export-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($leads) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID',
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'Company Name',
                'Source',
                'Status',
                'Services',
                'Created At',
            ]);

            foreach ($leads as $l) {
                fputcsv($handle, [
                    $l->id,
                    $l->first_name,
                    $l->last_name,
                    $l->email,
                    $l->phone,
                    $l->company_name,
                    $l->source,
                    $l->status,
                    $l->services->pluck('name')->implode(', '),
                    $l->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
