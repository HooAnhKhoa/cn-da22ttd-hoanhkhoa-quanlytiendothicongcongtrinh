<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email', 
        'phone',
        'user_type',
        'status',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function ownedProjects()
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function contractedProjects()
    {
        return $this->hasMany(Project::class, 'contractor_id');
    }

    public function engineeredProjects()
    {
        return $this->hasMany(Project::class, 'engineer_id');
    }

    public function progressUpdates()
    {
        return $this->hasMany(ProgressUpdate::class, 'created_by');
    }

    public function reportedIssues()
    {
        return $this->hasMany(Issue::class, 'reported_by');
    }

    public function uploadedDocuments()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function approvedDrawings()
    {
        return $this->hasMany(Drawing::class, 'approved_by');
    }

    public function equipmentUsages()
    {
        return $this->hasMany(EquipmentUsage::class, 'engineer_id');
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'engineer_id');
    }

    public function delays()
    {
        return $this->hasMany(Delay::class, 'responsible_engineer');
    }
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}