<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'project_id',
        // Đã xóa owner_id, contractor_id (lấy từ project)
        'contract_value',
        'advance_payment',
        'signed_date',
        'due_date',
        'status',
        // Đã xóa payment_status (tính toán động từ payments)
        // Đã xóa total_paid, remaining_amount (tính toán động)
        'contract_number',
        'contract_name',
        'description',
        'contract_file_path',
        'contract_file_name',
        // Đã xóa file_size, file_mime
        'additional_files'
    ];

    protected $casts = [
        'signed_date' => 'date',
        'due_date' => 'date',
        'contract_value' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'additional_files' => 'array'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Helper để lấy owner từ project
    public function getOwnerAttribute()
    {
        return $this->project->owner;
    }

    // Helper để lấy contractor từ project
    public function getContractorAttribute()
    {
        return $this->project->contractor;
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function approvals()
    {
        return $this->hasMany(ContractApproval::class);
    }

    // Tính toán số tiền đã trả
    public function getTotalPaidAttribute()
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    // Tính toán số tiền còn lại
    public function getRemainingAmountAttribute()
    {
        return $this->contract_value - $this->total_paid;
    }

    public function contractor()
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    protected static function booted()
    {
        // 1. Khi TẠO mới hợp đồng -> Chuyển dự án sang "Chờ hợp đồng" (pending_contract)
        static::created(function ($contract) {
            $contract->load('project');
            if ($contract->project && $contract->project->status === 'draft') {
                $contract->project->update(['status' => 'pending_contract']);
            }
        });

        // 2. Khi CẬP NHẬT hợp đồng
        static::updated(function ($contract) {
            // Nếu trạng thái hợp đồng đổi sang 'active' (Đã duyệt/Hiệu lực)
            // -> Chuyển dự án sang "Đang thi công" (in_progress)
            if ($contract->isDirty('status') && $contract->status === 'active') {
                $contract->load('project');
                // Chỉ cập nhật nếu dự án chưa hoàn thành hoặc chưa hủy
                if ($contract->project && !in_array($contract->project->status, ['completed', 'cancelled', 'in_progress'])) {
                    $contract->project->update(['status' => 'in_progress']);
                }
            }
        });
    }

}