<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionToken extends Model
{
    protected $fillable = [
        'token',
        'expiration_date',
        'status'
    ];

    public user() {
        return $this->belongsTo('App\Models\User');
    }
    
    use HasFactory;
}
