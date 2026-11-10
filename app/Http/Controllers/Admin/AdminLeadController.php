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
