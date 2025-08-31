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
        'password_hash',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
