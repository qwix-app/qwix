<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    public const USER_TYPES = [
        'normal', // default value (first pos)
        'store',
    ];

    protected $fillable = [
        'password',
        'document',
        'balance',
        'type',
    ];

    protected $hidden = [
        'password',
    ];
}
