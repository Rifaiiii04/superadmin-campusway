<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class School extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'npsn',
        'name',
        'school_level',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Get the students for the school
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
