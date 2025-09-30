<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'npsn',
        'name',
        'school_level',
        'password_hash',
        'password',
    ];

    protected $hidden = [
        'password_hash',
    ];

    /**
     * Get the students for the school
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
