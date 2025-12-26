<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'contract_id',
        'task_id',
        // Đã xóa site_id, project_id (vì dư thừa)
        'amount',
        'pay_date',
        'method',
        'transaction_code',
        'payment_type',
        'status',
        'note',
        'receipt_file_path',
        'receipt_file_name',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'pay_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}