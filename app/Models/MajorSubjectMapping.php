<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MajorSubjectMapping extends Model
{
    use HasFactory;

    // Tambahkan property $table untuk memberitahu Laravel nama tabel yang benar
    protected $table = 'major_subject_mappings';

    protected $fillable = [
        'major_id',
        'subject_id',
        'education_level',
        'mapping_type',
        'priority',
        'is_active',
        'subject_type',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Get the major recommendation that owns the mapping
     */
    public function majorRecommendation()
    {
        return $this->belongsTo(MajorRecommendation::class, 'major_id');
    }

    /**
     * Get the subject for this mapping
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}