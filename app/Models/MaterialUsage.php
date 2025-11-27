<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialUsage extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'task_id',
        'material_id',
        'quantity',
        'usage_date'
    ];

    protected $casts = [
        'usage_date' => 'date'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}