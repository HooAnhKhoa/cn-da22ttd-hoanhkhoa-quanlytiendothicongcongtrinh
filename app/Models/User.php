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
    
    public function hasPendingRoleRequest()
    {
        if (!$this->role_change_requests) {
            return false;
        }

        return collect($this->role_change_requests)
            ->contains('status', 'pending');
    }

    /**
     * Lấy tất cả yêu cầu đổi role
     */
    public function getRoleChangeRequestsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Lấy yêu cầu đổi role gần nhất
     */
    public function getLatestRoleChangeRequest()
    {
        if (!$this->role_change_requests) {
            return null;
        }

        return collect($this->role_change_requests)
            ->sortByDesc('created_at')
            ->first();
    }

    /**
     * Thêm yêu cầu đổi role mới
     */
    public function addRoleChangeRequest(array $data)
    {
        $requests = $this->role_change_requests ?? [];
        
        $newRequest = [
            'id' => uniqid(),
            'requested_role' => $data['requested_role'],
            'reason' => $data['reason'],
            'supporting_documents' => $data['supporting_documents'] ?? [],
            'status' => 'pending',
            'created_at' => now()->toDateTimeString(),
            'admin_notes' => null,
            'processed_at' => null,
            'processed_by' => null
        ];

        $requests[] = $newRequest;
        
        $this->update([
            'role_change_requests' => $requests
        ]);

        return $newRequest;
    }

    /**
     * Xử lý yêu cầu đổi role
     */
    public function processRoleChangeRequest($requestId, $status, $adminNotes = null, $processedBy = null)
    {
        $requests = $this->role_change_requests;
        
        foreach ($requests as &$request) {
            if ($request['id'] === $requestId && $request['status'] === 'pending') {
                $request['status'] = $status;
                $request['admin_notes'] = $adminNotes;
                $request['processed_at'] = now()->toDateTimeString();
                $request['processed_by'] = $processedBy;
                
                // Nếu được chấp nhận, cập nhật user_type
                if ($status === 'approved') {
                    $this->update([
                        'user_type' => $request['requested_role']
                    ]);
                }
                
                break;
            }
        }
        
        $this->update([
            'role_change_requests' => $requests
        ]);
        
        return true;
    }

    /**
     * Lấy danh sách yêu cầu đổi role (để hiển thị trong view)
     */
    public function getRoleChangeRequestsList()
    {
        return collect($this->role_change_requests)
            ->sortByDesc('created_at')
            ->values()
            ->all();
    }
}