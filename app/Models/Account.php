<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'account_number'
    ];

    protected $hidden = [
        'password'
    ];

    public user() {
        return $this->belongsTo('App\Models\User');
    }

    use HasFactory;
}
