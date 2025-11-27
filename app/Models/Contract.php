<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'project_id',
        'contractor_id',
        'contract_value',
        'signed_date',
        'due_date',
        'status'
    ];

    protected $casts = [
        'signed_date' => 'date',
        'due_date' => 'date'
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