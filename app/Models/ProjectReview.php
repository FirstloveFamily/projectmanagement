<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'review_date',
        'usage_status',
        'user_feedback',
        'technical_notes',
        'satisfaction_rating',
        'reviewer_id',
    ];

    protected $casts = [
        'review_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
