<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'email',
        'gender',
        'birth_date',
        'course_id',
        'year_level',
        'status',
        'enrolled_at',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'year_level' => 'integer',
            'enrolled_at' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
