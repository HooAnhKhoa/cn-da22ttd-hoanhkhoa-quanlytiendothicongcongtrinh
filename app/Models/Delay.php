<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delay extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'reason',
        'delay_days',
        'reported_date',
        'responsible_engineer'
    ];

    protected $casts = [
        'reported_date' => 'date'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function responsibleEngineer()
    {
        return $this->belongsTo(User::class, 'responsible_engineer');
    }
}