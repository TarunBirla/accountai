<?php

namespace App\Console\Commands;

use App\Mail\AdminDueRemindersMail;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDueRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch daily email notification to admin with all due and overdue client reminders';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for due and overdue reminders...');

        $dueReminders = Reminder::with('lead')
            ->where('is_done', false)
            ->where('remind_at', '<=', Carbon::now()->endOfDay())
            ->orderBy('remind_at', 'asc')
            ->get();

        if ($dueReminders->isEmpty()) {
            $this->info('No due reminders found today.');
            return Command::SUCCESS;
        }

        $adminEmail = config('mail.admin_address', env('ADMIN_NOTIFICATION_EMAIL', 'admin@accountai.com'));

        try {
            Mail::to($adminEmail)->send(new AdminDueRemindersMail($dueReminders));
            $this->info("Successfully sent reminder digest for {$dueReminders->count()} tasks to {$adminEmail}.");
            Log::info("Sent daily reminder digest for {$dueReminders->count()} tasks to {$adminEmail}.");
        } catch (\Throwable $e) {
            $this->error('Failed to send reminder email: ' . $e->getMessage());
            Log::error('Reminder scheduler failure: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
