<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_name',
        'owner_id',
        'contractor_id', 
        'engineer_id',
        'location',
        'start_date',
        'end_date',
        'total_budget',
        'description',
        'status'
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function contractor()
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function drawings()
    {
        return $this->hasMany(Drawing::class);
    }
}