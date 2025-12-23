<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractApproval extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_id',
        'approver_id',
        'status',
        'comments',
        'reviewed_at',
        'approved_at',
        'rejected_at',
        'approval_file_path',
        'approval_file_name'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'requires_rework' => 'boolean',
        'is_final' => 'boolean'
    ];

    // Quan hệ
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // Phương thức helper
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function approve(string $comments = null): void
    {
        $this->update([
            'status' => 'approved',
            'comments' => $comments,
            'approved_at' => now(),
            'reviewed_at' => now()
        ]);

        // Cập nhật trạng thái hợp đồng
        $this->contract->update(['status' => 'active']);
    }

    public function reject(string $comments = null): void
    {
        $this->update([
            'status' => 'rejected',
            'comments' => $comments,
            'rejected_at' => now(),
            'reviewed_at' => now()
        ]);

        // Cập nhật trạng thái hợp đồng
        $this->contract->update(['status' => 'draft']);
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Chờ phê duyệt',
            'approved' => 'Đã phê duyệt',
            'rejected' => 'Đã từ chối',
            'cancelled' => 'Đã hủy'
        ];
    }
}