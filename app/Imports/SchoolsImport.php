<?php

namespace App\Imports;

use App\Models\School;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;

class SchoolsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new School([
            'npsn' => $row['npsn'],
            'name' => $row['name'],
            'password_hash' => Hash::make($row['password'] ?? 'password123'),
        ]);
    }

    public function rules(): array
    {
        return [
            'npsn' => 'required|string|max:8|unique:schools,npsn',
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
        ];
    }
}
