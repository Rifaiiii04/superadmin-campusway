<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RumpunIlmu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the program studi for this rumpun ilmu
     */
    public function programStudi()
    {
        return $this->hasMany(ProgramStudi::class, 'rumpun_ilmu_id');
    }

    /**
     * Get the major recommendations for this rumpun ilmu
     */
    public function majorRecommendations()
    {
        return $this->hasMany(MajorRecommendation::class, 'rumpun_ilmu');
    }
}
