<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f5f9; color: #0e1530; margin: 0; padding: 24px; line-height: 1.6; }
    .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e3e8f0; overflow: hidden; }
    .header { background: linear-gradient(135deg, #0a1030, #27358f); color: #ffffff; padding: 28px; }
    .header h1 { margin: 0; font-size: 24px; font-weight: 700; }
    .header p { margin: 6px 0 0; color: #c9d3ff; font-size: 14px; }
    .body { padding: 28px; }
    .services-box { background: #f8fafc; border: 1px solid #e3e8f0; border-radius: 8px; padding: 16px; margin: 20px 0; }
    .services-box ul { margin: 8px 0 0; padding-left: 20px; }
    .services-box li { margin-bottom: 6px; }
    .services-box a { color: #3a4bdc; text-decoration: none; font-weight: 600; }
    .credentials { background: #0a1030; color: #ffffff; padding: 18px; border-radius: 8px; margin: 20px 0; }
    .credentials b { color: #0fb5a6; }
    .credentials code { background: rgba(255,255,255,0.15); padding: 2px 6px; border-radius: 4px; font-size: 15px; }
    .btn { display: inline-block; background: #3a4bdc; color: #ffffff !important; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 10px; }
    .notice { font-size: 13px; color: #d98a0b; background: #fff8eb; border: 1px solid #fed7aa; padding: 10px 14px; border-radius: 6px; margin-top: 16px; }
    .footer { padding: 20px 28px; font-size: 12px; color: #64708a; border-top: 1px solid #e3e8f0; background: #fafbfc; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>AccountAI Practice OS</h1>
        <p>UK Chartered & Certified Accounting Services</p>
    </div>
    <div class="body">
        <h2>Hello {{ $lead->first_name }},</h2>
        <p>Thank you for reaching out to AccountAI. We have successfully received your enquiry and an assigned accountancy team member will review your business requirements shortly.</p>

        <div class="services-box">
            <h4 style="margin: 0; color: #0e1530;">Requested Services:</h4>
            <ul>
                @foreach($lead->services as $service)
                    <li>
                        <a href="{{ url('/services/' . $service->slug) }}" target="_blank">
                            {{ $service->name }} ({{ ucfirst($service->type) }})
                        </a>
                        &mdash; <small>{{ Str::limit($service->description, 70) }}</small>
                    </li>
                @endforeach
            </ul>
        </div>

        <p>We have created a dedicated Client Portal account where you can track onboarding progress, upload documents, and review milestones in real time.</p>

        <div class="credentials">
            <h4 style="margin: 0 0 10px 0; color: #ffffff;">Your Client Portal Login</h4>
            <p style="margin: 4px 0;"><b>Login URL:</b> <a href="{{ route('login') }}" style="color: #c9d3ff;">{{ route('login') }}</a></p>
            <p style="margin: 4px 0;"><b>Email:</b> {{ $lead->email }}</p>
            @if($isNewAccount && $plainPassword)
                <p style="margin: 4px 0;"><b>Temporary Password:</b> <code>{{ $plainPassword }}</code></p>
            @else
                <p style="margin: 4px 0; color: #c9d3ff;"><em>(Please use your existing AccountAI password)</em></p>
            @endif
        </div>

        @if($isNewAccount && $plainPassword)
            <div class="notice">
                <strong>Important:</strong> For security compliance, please log in and change your password immediately in your profile dashboard.
            </div>
        @endif

        <p style="text-align: center; margin-top: 24px;">
            <a href="{{ route('login') }}" class="btn">Access Client Portal</a>
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} AccountAI Practice Ltd. Authorised and Regulated in the United Kingdom.
    </div>
</div>
</body>
</html>
