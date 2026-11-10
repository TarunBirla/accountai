<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JourneyStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'title',
        'description',
        'status',
        'step_order',
        'review_status',
        'payload',
        'admin_notes',
        'submitted_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    public function isSubmitted(): bool
    {
        return $this->review_status === 'submitted';
    }

    public function isRevisionRequired(): bool
    {
        return $this->review_status === 'revision_required';
    }
}
