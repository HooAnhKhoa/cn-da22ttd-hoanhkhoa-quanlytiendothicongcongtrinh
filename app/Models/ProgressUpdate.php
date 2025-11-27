<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'date',
        'progress_percent',
        'description',
        'attached_files',
        'created_by'
    ];

    protected $casts = [
        'attached_files' => 'array',
        'date' => 'date'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}