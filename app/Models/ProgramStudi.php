<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramStudi extends Model
{
    use HasFactory;

    // Tambahkan property $table untuk memberitahu Laravel nama tabel yang benar
    protected $table = 'program_studi';

    protected $fillable = [
        'name',
        'description',
        'rumpun_ilmu_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the rumpun ilmu that owns the program studi
     */
    public function rumpunIlmu()
    {
        return $this->belongsTo(RumpunIlmu::class, 'rumpun_ilmu_id');
    }

    /**
     * Get the subjects for this program studi
     */
    public function programStudiSubjects()
    {
        return $this->hasMany(ProgramStudiSubject::class);
    }
}
