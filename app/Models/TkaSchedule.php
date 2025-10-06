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
        'created_by',
        // PUSMENDIK Essential Fields
        'gelombang',
        'hari_pelaksanaan',
        'exam_venue',
        'exam_room',
        'contact_person',
        'contact_phone',
        'requirements',
        'materials_needed'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'target_schools' => 'array',
        'is_active' => 'boolean',
        'subject_areas' => 'array',
        'registration_deadline' => 'datetime',
        'result_announcement' => 'datetime',
        'appeal_deadline' => 'datetime',
        'certificate_issuance' => 'datetime',
        'is_mandatory' => 'boolean'
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
              ->orWhere('target_schools', '[]')
              ->orWhere('target_schools', '')
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

    // PUSMENDIK Standard Methods
    public function subjectAreas()
    {
        return $this->belongsToMany(TkaSubjectArea::class, 'tka_schedule_subjects')
                    ->withPivot(['duration_minutes', 'question_count', 'passing_score'])
                    ->withTimestamps();
    }

    public function examVenue()
    {
        return $this->belongsTo(TkaExamVenue::class, 'exam_venue', 'name');
    }

    public function isRegistrationOpen()
    {
        return $this->registration_deadline && $this->registration_deadline->isFuture();
    }

    public function isRegistrationClosed()
    {
        return $this->registration_deadline && $this->registration_deadline->isPast();
    }

    public function getFormattedDurationAttribute()
    {
        if ($this->duration_minutes) {
            $hours = floor($this->duration_minutes / 60);
            $minutes = $this->duration_minutes % 60;
            
            if ($hours > 0) {
                return $hours . ' jam ' . $minutes . ' menit';
            }
            return $minutes . ' menit';
        }
        return 'Tidak ditentukan';
    }

    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'low' => 'bg-gray-100 text-gray-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-orange-100 text-orange-800',
            'critical' => 'bg-red-100 text-red-800'
        ];

        return $badges[$this->priority_level] ?? 'bg-gray-100 text-gray-800';
    }

    public function getExamFormatBadgeAttribute()
    {
        $badges = [
            'online' => 'bg-blue-100 text-blue-800',
            'offline' => 'bg-green-100 text-green-800',
            'hybrid' => 'bg-purple-100 text-purple-800'
        ];

        return $badges[$this->exam_format] ?? 'bg-gray-100 text-gray-800';
    }

    public function getAssessmentTypeBadgeAttribute()
    {
        $badges = [
            'regular' => 'bg-blue-100 text-blue-800',
            'remedial' => 'bg-yellow-100 text-yellow-800',
            'susulan' => 'bg-orange-100 text-orange-800'
        ];

        return $badges[$this->assessment_type] ?? 'bg-gray-100 text-gray-800';
    }

    public function getTotalQuestionsAttribute()
    {
        return $this->subjectAreas()->sum('tka_schedule_subjects.question_count');
    }

    public function getTotalDurationAttribute()
    {
        return $this->subjectAreas()->sum('tka_schedule_subjects.duration_minutes');
    }

    public function getAvailableSlotsAttribute()
    {
        if ($this->max_participants) {
            // This would need to be calculated based on actual registrations
            return $this->max_participants;
        }
        return null;
    }

    public function isFullyBooked()
    {
        if ($this->max_participants) {
            // This would need to check actual registration count
            return false; // Placeholder
        }
        return false;
    }

    public function getRegistrationStatusAttribute()
    {
        if (!$this->registration_deadline) {
            return 'not_required';
        }

        if ($this->registration_deadline->isFuture()) {
            return 'open';
        }

        return 'closed';
    }

    public function getDaysUntilExamAttribute()
    {
        return $this->start_date->diffInDays(now());
    }

    public function getHoursUntilExamAttribute()
    {
        return $this->start_date->diffInHours(now());
    }

    public function getMinutesUntilExamAttribute()
    {
        return $this->start_date->diffInMinutes(now());
    }

    public function isUrgent()
    {
        return $this->days_until_exam <= 3;
    }

    public function isVeryUrgent()
    {
        return $this->days_until_exam <= 1;
    }

    public function getUrgencyLevelAttribute()
    {
        if ($this->isVeryUrgent()) {
            return 'very_urgent';
        }
        if ($this->isUrgent()) {
            return 'urgent';
        }
        if ($this->days_until_exam <= 7) {
            return 'soon';
        }
        return 'normal';
    }
}
