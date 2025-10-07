<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'level',
        'program',
        'class_number',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'class_number' => 'integer',
    ];

    /**
     * Get the school that owns the class
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the students in this class
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'kelas', 'name');
    }
}
