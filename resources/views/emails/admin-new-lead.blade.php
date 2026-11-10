<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f3f5f9; color: #0e1530; margin: 0; padding: 20px; }
    .box { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; border: 1px solid #e3e8f0; padding: 24px; }
    .badge { display: inline-block; padding: 4px 10px; border-radius: 99px; font-size: 12px; font-weight: 600; background: #3a4bdc1a; color: #3a4bdc; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    td { padding: 8px; border-bottom: 1px solid #e3e8f0; font-size: 14px; }
    td.label { font-weight: 600; color: #64708a; width: 140px; }
    .btn { display: inline-block; background: #3a4bdc; color: #fff !important; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; margin-top: 20px; }
</style>
</head>
<body>
<div class="box">
    <h2>New Lead Captured <span class="badge">{{ strtoupper($lead->source) }}</span></h2>
    <p>A new enquiry has been submitted through the {{ $lead->source }} funnel:</p>

    <table>
        <tr>
            <td class="label">Name:</td>
            <td><strong>{{ $lead->full_name }}</strong></td>
        </tr>
        <tr>
            <td class="label">Email:</td>
            <td><a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></td>
        </tr>
        <tr>
            <td class="label">Phone:</td>
            <td><a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a></td>
        </tr>
        <tr>
            <td class="label">Company:</td>
            <td>{{ $lead->company_name ?? 'Sole Trader / Personal' }}</td>
        </tr>
        <tr>
            <td class="label">Services:</td>
            <td>
                @foreach($lead->services as $service)
                    <span class="badge">{{ $service->name }}</span>
                @endforeach
            </td>
        </tr>
        <tr>
            <td class="label">Submitted at:</td>
            <td>{{ $lead->created_at->format('d M Y, H:i') }}</td>
        </tr>
    </table>

    <div style="margin-top: 24px; text-align: center;">
        <a href="{{ route('admin.leads.show', $lead->id) }}" class="btn">Open Lead in Admin Dashboard</a>
    </div>
</div>
</body>
</html>
