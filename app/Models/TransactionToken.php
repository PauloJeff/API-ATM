<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionToken extends Model
{
    protected $fillable = [
        'token',
        'expiration_date',
        'status',
        'user_id'
    ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
    
    use HasFactory;
}
