<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'education_level',
        'subject_type',
        'subject_number',
        'is_required',
        'is_active'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Get the questions for this subject
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'subject', 'name');
    }

    /**
     * Scope for required subjects
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope for active subjects
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for education level
     */
    public function scopeEducationLevel($query, $level)
    {
        return $query->where('education_level', $level);
    }

    /**
     * Scope for subject type
     */
    public function scopeSubjectType($query, $type)
    {
        return $query->where('subject_type', $type);
    }

    /**
     * Scope for SMK/MAK subjects
     */
    public function scopeSMKMAK($query)
    {
        return $query->where('education_level', 'SMK/MAK');
    }

    /**
     * Scope for SMA/MA subjects
     */
    public function scopeSMAMA($query)
    {
        return $query->where('education_level', 'SMA/MA');
    }
}
