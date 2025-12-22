<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'project_id',
        'contractor_id',
        'contract_value',
        'signed_date',
        'due_date',
        'status',
        'description',
        'terms'
    ];

    protected $casts = [
        'signed_date' => 'date',
        'due_date' => 'date',
        'contract_value' => 'decimal:2'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function contractor()
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}