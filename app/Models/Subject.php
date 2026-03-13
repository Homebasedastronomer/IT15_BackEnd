<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'units',
        'year_level',
        'offered_in',
        'term_indicator',
        'course_id',
        'description',
        'prerequisites',
    ];

    protected function casts(): array
    {
        return [
            'units' => 'integer',
            'year_level' => 'integer',
            'prerequisites' => 'array',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
