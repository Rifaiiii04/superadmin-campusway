<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subjects',
        'start_time',
        'end_time',
        'status',
        'scores',
        'total_score',
        'recommendations'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'scores' => 'array',
        'recommendations' => 'array'
    ];

    /**
     * Get the student that owns the test result
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the test answers for this test result
     */
    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class);
    }
}
