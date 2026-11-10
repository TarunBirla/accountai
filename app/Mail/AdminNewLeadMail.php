<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewLeadMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Lead $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[New Lead - {$this->lead->source}] {$this->lead->full_name} ({$this->lead->company_name})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-new-lead',
        );
    }
}
