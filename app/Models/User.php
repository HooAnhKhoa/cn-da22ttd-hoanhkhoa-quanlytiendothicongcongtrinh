<?php

namespace App\Models;

use App\Models\RoleChangeRequest;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email', 
        'phone',
        'user_type',
        'role_change_requests', // Thêm trường này
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
            'role_change_requests' => 'array', // Cast JSON thành array
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

    public function roleChangeRequests()
    {
        return $this->hasMany(RoleChangeRequest::class)->latest();
    }

    // Kiểm tra có yêu cầu đang chờ (dùng cho logic ẩn hiện nút)
    public function hasPendingRoleRequest()
    {
        return $this->roleChangeRequests()->where('status', 'pending')->exists();
    }
    
    // Hàm hỗ trợ lấy danh sách cho View (để tương thích logic cũ)
    public function getRoleChangeRequestsList()
    {
        return $this->roleChangeRequests;
    }
}