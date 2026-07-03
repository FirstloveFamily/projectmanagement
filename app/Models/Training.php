<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'training_date',
        'start_time',
        'end_time',
        'audience',
        'trainer',
        'location',
        'google_sheet_url',
        'status',
        'capacity',
        'attended',
        'participants',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'training_date' => 'date',
            'capacity' => 'integer',
            'attended' => 'integer',
        ];
    }
}
