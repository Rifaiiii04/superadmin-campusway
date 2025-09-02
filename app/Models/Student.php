<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nisn',
        'name',
        'school_id',
        'kelas',
        'email',
        'phone',
        'parent_phone',
        'password',
    ];

    /**
     * Get the school that owns the student
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the major choices for the student
     */
    public function majorChoices()
    {
        return $this->hasMany(StudentChoice::class);
    }

    /**
     * Get the current major choice for the student (singular)
     */
    public function studentChoice()
    {
        return $this->hasOne(StudentChoice::class);
    }

    public function studentSubjects()
    {
        return $this->hasMany(StudentSubject::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }
}
