<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $fillable = [
        'contract_id',
        'task_id',
        'site_id',
        'project_id',
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
        'pay_date' => 'date'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}