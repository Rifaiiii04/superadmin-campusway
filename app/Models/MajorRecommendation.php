<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MajorRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'major_name',
        'description',
        'required_subjects',
        'preferred_subjects',
        'kurikulum_merdeka_subjects',
        'kurikulum_2013_ipa_subjects',
        'kurikulum_2013_ips_subjects',
        'kurikulum_2013_bahasa_subjects',
        'career_prospects',
        'is_active'
    ];

    protected $casts = [
        'required_subjects' => 'array',
        'preferred_subjects' => 'array',
        'kurikulum_merdeka_subjects' => 'array',
        'kurikulum_2013_ipa_subjects' => 'array',
        'kurikulum_2013_ips_subjects' => 'array',
        'kurikulum_2013_bahasa_subjects' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the student choices for this major
     */
    public function studentChoices()
    {
        return $this->hasMany(StudentChoice::class, 'major_id');
    }

    /**
     * Check if a student's scores match this major's requirements
     */
    public function matchesStudentScores($studentScores)
    {
        $totalScore = 0;
        $subjectCount = 0;
        
        foreach ($studentScores as $score) {
            if (isset($score['score'])) {
                $totalScore += $score['score'];
                $subjectCount++;
            }
        }
        
        if ($subjectCount === 0) return false;
        
        $averageScore = $totalScore / $subjectCount;
        
        // Check if average score meets requirements
        if ($averageScore < $this->min_score || $averageScore > $this->max_score) {
            return false;
        }
        
        // Check required subjects
        if (!empty($this->required_subjects)) {
            $hasRequiredSubjects = false;
            foreach ($this->required_subjects as $requiredSubject) {
                foreach ($studentScores as $score) {
                    if (isset($score['subject']) && 
                        strtolower($score['subject']) === strtolower($requiredSubject) &&
                        $score['score'] >= 70) { // Minimum 70 for required subjects
                        $hasRequiredSubjects = true;
                        break;
                    }
                }
                if ($hasRequiredSubjects) break;
            }
            if (!$hasRequiredSubjects) return false;
        }
        
        return true;
    }

    /**
     * Calculate confidence score for this major based on student scores
     */
    public function calculateConfidenceScore($studentScores)
    {
        $totalScore = 0;
        $subjectCount = 0;
        $bonusPoints = 0;
        
        foreach ($studentScores as $score) {
            if (isset($score['score'])) {
                $totalScore += $score['score'];
                $subjectCount++;
                
                // Bonus points for preferred subjects
                if (in_array(strtolower($score['subject']), array_map('strtolower', $this->preferred_subjects ?? []))) {
                    $bonusPoints += 10;
                }
            }
        }
        
        if ($subjectCount === 0) return 0;
        
        $averageScore = $totalScore / $subjectCount;
        $baseConfidence = ($averageScore / 100) * 80; // Base confidence 80% max
        
        return min(100, $baseConfidence + $bonusPoints);
    }
}
