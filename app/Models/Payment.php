<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'contract_id',
        'amount',
        'pay_date',
        'method',
        'note'
    ];

    protected $casts = [
        'pay_date' => 'date'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}