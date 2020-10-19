<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'name',
        'birthday',
        'cpf'
    ];

    public function account() {
        return $this->hasOne('App\Models\Account');
    }

    public function token() {
        return $this->hasOne('App\Models\TransactionToken');
    }

    use HasFactory;
}
