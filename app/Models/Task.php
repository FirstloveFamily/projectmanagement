<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'task_category',
        'title',
        'description',
        'status',
        'priority',
        'start_date',
        'due_date',
        'completed_at',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the project that owns the task.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the checklist items for the task.
     */
    public function checklists(): HasMany
    {
        return $this->hasMany(TaskChecklist::class);
    }

    /**
     * Get the comments for the task.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    /**
     * Get the built-in task category labels.
     *
     * @return array<string, string>
     */
    public static function taskCategoryLabels(): array
    {
        return [
            'general' => 'งานทั่วไป',
            'follow_document' => 'ติดตามเอกสาร',
            'follow_mifc' => 'ติดตาม MIFC',
            'follow_other' => 'ติดตามอื่น ๆ',
        ];
    }

    /**
     * Resolve a task category label.
     */
    public static function taskCategoryLabel(?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '-';
        }

        return static::taskCategoryLabels()[$value] ?? $value;
    }

    /**
     * Resolve a stable badge color for a task category.
     */
    public static function taskCategoryColor(?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return 'gray';
        }

        return match ($value) {
            'general' => 'gray',
            'follow_document' => 'info',
            'follow_mifc' => 'warning',
            'follow_other' => 'success',
            default => collect(['primary', 'info', 'success', 'warning', 'danger', 'gray'])
                ->values()
                ->get(abs(crc32(mb_strtolower($value))) % 6, 'gray'),
        };
    }
}
