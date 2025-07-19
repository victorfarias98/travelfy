<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TravelRequest extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'user_id',
        'destination',
        'departure_date',
        'return_date',
        'status'
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDestination($query, string $destination)
    {
        return $query->where('destination', 'LIKE', "%{$destination}%");
    }

    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('departure_date', [$startDate, $endDate]);
    }

    public function scopeUpcoming($query, int $days = 7)
    {
        return $query->where('status', 'approved')
                    ->whereBetween('departure_date', [now(), now()->addDays($days)]);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'requested' => 'Solicitado',
            'approved' => 'Aprovado',
            'cancelled' => 'Cancelado',
            default => $this->status
        };
    }

    public function getDurationAttribute(): int
    {
        return $this->departure_date->diffInDays($this->return_date);
    }

    public function canBeCancelled(): bool
    {
        if ($this->status === 'requested') {
            return true;
        }

        if ($this->status === 'approved') {
            $hoursElapsed = $this->updated_at->diffInHours(now());
            return $hoursElapsed <= 48;
        }

        return false;
    }

    public function isUpcoming(int $days = 7): bool
    {
        return $this->status === 'approved' && 
               $this->departure_date->isBetween(now(), now()->addDays($days));
    }

    public function getDaysUntilTravelAttribute(): ?int
    {
        if ($this->status !== 'approved' || $this->departure_date->isPast()) {
            return null;
        }

        return now()->diffInDays($this->departure_date);
    }
}