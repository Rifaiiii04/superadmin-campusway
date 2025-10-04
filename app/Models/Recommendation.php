<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'recommended_major',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
