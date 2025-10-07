<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramStudiSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_studi_id',
        'subject_id',
        'kurikulum_type',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Get the program studi that owns the subject mapping
     */
    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    /**
     * Get the subject for this mapping
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
