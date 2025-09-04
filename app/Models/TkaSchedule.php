<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TkaSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'type',
        'instructions',
        'target_schools',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'target_schools' => 'array',
        'is_active' => 'boolean'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where(function($q) use ($schoolId) {
            $q->whereNull('target_schools')
              ->orWhereJsonContains('target_schools', $schoolId);
        });
    }

    // Accessors
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date->format('d/m/Y H:i');
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date->format('d/m/Y H:i');
    }

    public function getDurationAttribute()
    {
        return $this->start_date->diffForHumans($this->end_date, true);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'scheduled' => 'bg-blue-100 text-blue-800',
            'ongoing' => 'bg-green-100 text-green-800',
            'completed' => 'bg-gray-100 text-gray-800',
            'cancelled' => 'bg-red-100 text-red-800'
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getTypeBadgeAttribute()
    {
        $badges = [
            'regular' => 'bg-blue-100 text-blue-800',
            'makeup' => 'bg-yellow-100 text-yellow-800',
            'special' => 'bg-purple-100 text-purple-800'
        ];

        return $badges[$this->type] ?? 'bg-gray-100 text-gray-800';
    }

    // Methods
    public function isUpcoming()
    {
        return $this->start_date->isFuture();
    }

    public function isOngoing()
    {
        return $this->start_date->isPast() && $this->end_date->isFuture();
    }

    public function isCompleted()
    {
        return $this->end_date->isPast();
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function canBeEdited()
    {
        return $this->status === 'scheduled' && $this->is_active;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['scheduled', 'ongoing']) && $this->is_active;
    }
}
