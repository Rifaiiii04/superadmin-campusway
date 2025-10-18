<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'rumpun_ilmu',
        'career_prospects',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the student choices for this major
     */
    public function studentChoices()
    {
        return $this->hasMany(StudentChoice::class);
    }

    /**
     * Get the students who chose this major
     */
    public function students()
    {
        return $this->hasManyThrough(Student::class, StudentChoice::class, 'major_id', 'id', 'id', 'student_id');
    }
}
