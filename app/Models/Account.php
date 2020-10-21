<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'account_number',
        'balance',
        'user_id',
        'type',
        'password'
    ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function typeAccount() {
        return $this->belongsTo('App\Models\TypeAccount');
    }

    use HasFactory;
}
