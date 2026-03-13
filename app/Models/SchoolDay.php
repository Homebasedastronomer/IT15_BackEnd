<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_date',
        'is_holiday',
        'event_name',
        'attendance_rate',
    ];

    protected function casts(): array
    {
        return [
            'school_date' => 'date',
            'is_holiday' => 'boolean',
            'attendance_rate' => 'float',
        ];
    }
}
