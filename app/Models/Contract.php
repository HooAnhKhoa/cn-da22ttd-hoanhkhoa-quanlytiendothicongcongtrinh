<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'project_id',
        'owner_id',
        'contractor_id',
        'contract_value',
        'advance_payment',
        'signed_date',
        'due_date',
        'status',
        'payment_status',
        'total_paid',
        'remaining_amount',
        'contract_number',
        'contract_name',
        'description',
        'contract_file_path',
        'contract_file_name',
        'contract_file_size',
        'contract_file_mime',
        'additional_files'
    ];

    protected $casts = [
        'signed_date' => 'date',
        'due_date' => 'date',
        'contract_value' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'additional_files' => 'array'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function contractor()
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function approvals()
    {
        return $this->hasMany(ContractApproval::class);
    }
}