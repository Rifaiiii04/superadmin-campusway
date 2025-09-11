<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MajorSubjectMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'major_id',
        'subject_id',
        'education_level',
        'mapping_type',
        'priority',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the major that owns the mapping
     */
    public function major()
    {
        return $this->belongsTo(MajorRecommendation::class, 'major_id');
    }

    /**
     * Get the subject that owns the mapping
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Scope for active mappings
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
     * Scope for mapping type
     */
    public function scopeMappingType($query, $type)
    {
        return $query->where('mapping_type', $type);
    }

    /**
     * Get subjects for a major and education level
     */
    public static function getSubjectsForMajor($majorId, $educationLevel)
    {
        return self::with('subject')
            ->where('major_id', $majorId)
            ->where('education_level', $educationLevel)
            ->where('is_active', true)
            ->orderBy('priority')
            ->get()
            ->pluck('subject');
    }

    /**
     * Get mandatory subjects for education level
     */
    public static function getMandatorySubjects($educationLevel)
    {
        return Subject::where('education_level', $educationLevel)
            ->where('subject_type', 'Wajib')
            ->where('is_active', true)
            ->orderBy('subject_number')
            ->get();
    }

    /**
     * Get all subjects (mandatory + optional) for a major and education level
     */
    public static function getAllSubjectsForMajor($majorId, $educationLevel)
    {
        $mandatorySubjects = self::getMandatorySubjects($educationLevel);
        $optionalSubjects = self::getSubjectsForMajor($majorId, $educationLevel);

        return [
            'mandatory' => $mandatorySubjects,
            'optional' => $optionalSubjects,
            'total' => $mandatorySubjects->count() + $optionalSubjects->count()
        ];
    }
}
