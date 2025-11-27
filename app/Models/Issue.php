<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'task_id',
        'description',
        'severity',
        'status',
        'reported_by',
        'date_reported',
        'date_resolved'
    ];

    protected $casts = [
        'date_reported' => 'date',
        'date_resolved' => 'date'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}