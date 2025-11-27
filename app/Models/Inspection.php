<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'task_id',
        'engineer_id',
        'result',
        'notes',
        'date',
        'attached_files'
    ];

    protected $casts = [
        'attached_files' => 'array',
        'date' => 'date'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }
}