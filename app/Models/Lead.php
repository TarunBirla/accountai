<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_name',
        'source',
        'status',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'lead_service')->withTimestamps();
    }

    public function journeySteps(): HasMany
    {
        return $this->hasMany(JourneyStep::class)->orderBy('step_order', 'asc');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class)->orderBy('remind_at', 'asc');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getProgressPercentageAttribute(): int
    {
        $total = $this->journeySteps()->count();
        if ($total === 0) {
            return 0;
        }
        $completed = $this->journeySteps()->where('status', 'done')->count();
        return (int) round(($completed / $total) * 100);
    }
}
