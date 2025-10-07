<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentChoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'major_id',
    ];

    /**
     * Get the student that owns the choice
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the major recommendation for this choice
     */
    public function majorRecommendation()
    {
        return $this->belongsTo(MajorRecommendation::class, 'major_id');
    }
}