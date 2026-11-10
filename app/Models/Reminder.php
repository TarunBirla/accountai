<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'title',
        'remind_at',
        'is_done',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'remind_at' => 'datetime',
            'is_done' => 'boolean',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOverdue(): bool
    {
        return !$this->is_done && $this->remind_at->isPast();
    }

    public function isDueToday(): bool
    {
        return !$this->is_done && $this->remind_at->isToday();
    }
}
