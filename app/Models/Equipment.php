<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'equipment_name',
        'type',
        'serial',
        'status',
        'location'
    ];

    public function equipmentUsages()
    {
        return $this->hasMany(EquipmentUsage::class);
    }
}