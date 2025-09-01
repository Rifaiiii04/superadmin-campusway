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

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student that owns the choice
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the major that was chosen
     */
    public function major()
    {
        return $this->belongsTo(MajorRecommendation::class, 'major_id');
    }
}
