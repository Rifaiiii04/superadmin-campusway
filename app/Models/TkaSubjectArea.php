<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TkaSubjectArea extends Model
{
    use HasFactory;

    // Tambahkan property $table untuk memberitahu Laravel nama tabel yang benar
    protected $table = 'tka_subject_areas';

    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'weight',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'weight' => 'integer'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCore($query)
    {
        return $query->where('category', 'core');
    }

    public function scopeElective($query)
    {
        return $query->where('category', 'elective');
    }

    public function scopeSpecialized($query)
    {
        return $query->where('category', 'specialized');
    }

    // Relationships
    public function schedules()
    {
        return $this->belongsToMany(TkaSchedule::class, 'tka_schedule_subjects')
                    ->withPivot(['duration_minutes', 'question_count', 'passing_score'])
                    ->withTimestamps();
    }

    // Accessors
    public function getCategoryBadgeAttribute()
    {
        $badges = [
            'core' => 'bg-blue-100 text-blue-800',
            'elective' => 'bg-green-100 text-green-800',
            'specialized' => 'bg-purple-100 text-purple-800'
        ];

        return $badges[$this->category] ?? 'bg-gray-100 text-gray-800';
    }

    public function getCategoryLabelAttribute()
    {
        $labels = [
            'core' => 'Mata Pelajaran Inti',
            'elective' => 'Mata Pelajaran Pilihan',
            'specialized' => 'Mata Pelajaran Khusus'
        ];

        return $labels[$this->category] ?? 'Tidak Diketahui';
    }

    public function getWeightLabelAttribute()
    {
        return 'Bobot ' . $this->weight;
    }

    // Methods
    public function isCore()
    {
        return $this->category === 'core';
    }

    public function isElective()
    {
        return $this->category === 'elective';
    }

    public function isSpecialized()
    {
        return $this->category === 'specialized';
    }

    public function getTotalQuestionsInSchedules()
    {
        return $this->schedules()->sum('tka_schedule_subjects.question_count');
    }

    public function getTotalDurationInSchedules()
    {
        return $this->schedules()->sum('tka_schedule_subjects.duration_minutes');
    }

    public function getAveragePassingScore()
    {
        return $this->schedules()->avg('tka_schedule_subjects.passing_score');
    }
}
