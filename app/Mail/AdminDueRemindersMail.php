<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminDueRemindersMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Collection $reminders;

    public function __construct(Collection $reminders)
    {
        $this->reminders = $reminders;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Alert: ' . $this->reminders->count() . ' AccountAI Reminders Requiring Attention',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-due-reminders',
        );
    }
}
