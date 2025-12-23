<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_id',
        'reviewer_id',
        'rating',
        'comments',
        'improvement_suggestions',
        'result',
        'requires_rework',
        'rework_instructions',
        'rework_deadline',
        'is_final',
        'reviewed_at',
        'approved_at',
        'review_files'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rework_deadline' => 'date',
        'requires_rework' => 'boolean',
        'is_final' => 'boolean',
        'review_files' => 'array'
    ];

    // Quan hệ
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // Phương thức helper
    public function isApproved(): bool
    {
        return $this->result === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->result === 'rejected';
    }

    public function needsRevision(): bool
    {
        return $this->result === 'needs_revision';
    }

    public function hasRework(): bool
    {
        return $this->requires_rework === true;
    }

    public function isFinalReview(): bool
    {
        return $this->is_final === true;
    }

    public function approve(array $data = []): void
    {
        $this->update(array_merge([
            'result' => 'approved',
            'approved_at' => now(),
            'reviewed_at' => now(),
            'is_final' => true,
            'requires_rework' => false
        ], $data));
    }

    public function reject(array $data = []): void
    {
        $this->update(array_merge([
            'result' => 'rejected',
            'reviewed_at' => now(),
            'requires_rework' => true
        ], $data));
    }

    public function requestRevision(array $data = []): void
    {
        $this->update(array_merge([
            'result' => 'needs_revision',
            'reviewed_at' => now(),
            'requires_rework' => true
        ], $data));
    }

    public static function getResults(): array
    {
        return [
            'approved' => 'Đã chấp nhận',
            'rejected' => 'Đã từ chối',
            'needs_revision' => 'Cần chỉnh sửa'
        ];
    }
}