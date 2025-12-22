<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'project_id',
        'category',
        'document_name',
        'file_path',
        'uploaded_by',
        'uploaded_at'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}