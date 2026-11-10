<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientWelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Lead $lead;
    public string $plainPassword;
    public bool $isNewAccount;

    public function __construct(Lead $lead, string $plainPassword = '', bool $isNewAccount = true)
    {
        $this->lead = $lead;
        $this->plainPassword = $plainPassword;
        $this->isNewAccount = $isNewAccount;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to AccountAI – Your Accountancy Enquiry & Client Portal Access',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-welcome',
        );
    }
}
