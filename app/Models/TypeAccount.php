<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeAccount extends Model
{
    protected $fillable = [
        'name'
    ];

    public function account() {
        return $this->hasMany('App\Models\Account');
    }

    use HasFactory;
}
