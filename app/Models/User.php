<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parental\HasChildren;

class User extends Model
{
    use HasFactory, HasChildren;

    public const USER_TYPES = [
        'normal', // default value (first pos)
        'store',
    ];

    protected $childTypes = [
        'normal' => Normal::class,
        'store' => Store::class,
    ];

    protected $fillable = [
        'full_name',
        'document',
        'balance',
        'type',
    ];

    protected $hidden = [
        'password',
    ];
}
