<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;

    public $timestamps = false; // THÊM DÒNG NÀY

    protected $fillable = [
        'project_id',
        'milestone_name',
        'description',
        'target_date',
        'completed_date',
        'status'
    ];

    protected $casts = [
        'target_date' => 'date',
        'completed_date' => 'date'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}