<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialUsage extends Model
{
    use HasFactory;
    
    protected $table = 'material_usages';
    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'material_id',
        'quantity',
        'usage_date'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'usage_date' => 'date'
    ];

    // Quan hệ với Task
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Quan hệ với Material
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    // Quan hệ với Site thông qua Task
    public function site()
    {
        return $this->hasOneThrough(Site::class, Task::class, 'id', 'id', 'task_id', 'site_id');
    }

    // Quan hệ với User (người ghi nhận)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}