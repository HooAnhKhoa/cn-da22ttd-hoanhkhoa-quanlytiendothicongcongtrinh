<?php

namespace App\Models;

use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function user()
    {
        // Nếu created_by là user_id (integer)
        return $this->belongsTo(User::class, 'created_by');
    }
}