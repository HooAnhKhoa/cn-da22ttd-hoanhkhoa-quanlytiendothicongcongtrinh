<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'contract_id',
        'task_id',
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
        'pay_date' => 'datetime',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    // Thêm accessor mới
    protected $appends = ['receipt_url', 'receipt_extension', 'file_size'];

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

    // Sửa lại hàm kiểm tra và lấy URL ảnh
    public function hasReceipt()
    {
        return !empty($this->receipt_file_path) && Storage::disk('public')->exists($this->receipt_file_path);
    }

    public function getReceiptUrlAttribute()
    {
        if (!$this->hasReceipt()) return null;
        return asset('storage/' . $this->receipt_file_path);
    }

    public function isReceiptImage()
    {
        if (!$this->receipt_file_path) return false;
        $ext = strtolower(pathinfo($this->receipt_file_path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    public function isReceiptPdf()
    {
        if (!$this->receipt_file_path) return false;
        $ext = strtolower(pathinfo($this->receipt_file_path, PATHINFO_EXTENSION));
        return $ext === 'pdf';
    }

    // Thêm phương thức mới để lấy extension
    public function getReceiptExtensionAttribute()
    {
        if (!$this->receipt_file_path) return null;
        return strtolower(pathinfo($this->receipt_file_path, PATHINFO_EXTENSION));
    }

    // Thêm phương thức để lấy kích thước file
    public function getFileSizeAttribute()
    {
        if (!$this->hasReceipt()) return null;
        $size = Storage::disk('public')->size($this->receipt_file_path);
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;
        while ($size >= 1024 && $index < count($units) - 1) {
            $size /= 1024;
            $index++;
        }
        
        return round($size, 2) . ' ' . $units[$index];
    }

    // Thêm phương thức để lấy icon theo loại file
    public function getFileIconAttribute()
    {
        if (!$this->receipt_file_path) return 'fas fa-file';
        
        $ext = $this->receipt_extension;
        
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return 'fas fa-file-image';
        } elseif ($ext === 'pdf') {
            return 'fas fa-file-pdf';
        } else {
            return 'fas fa-file';
        }
    }

    // Thêm phương thức để lấy màu theo loại file
    public function getFileColorAttribute()
    {
        if (!$this->receipt_file_path) return 'gray';
        
        $ext = $this->receipt_extension;
        
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return 'green';
        } elseif ($ext === 'pdf') {
            return 'red';
        } else {
            return 'gray';
        }
    }
}