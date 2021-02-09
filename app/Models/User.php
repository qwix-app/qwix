<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
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
        'email',
        'password',
        'document',
        'balance',
        'type',
    ];

    protected $hidden = [
        'password',
    ];

    public static function getRules()
    {
        return [
            'full_name' => 'required',
            'email' => [
                'required',
                'email',
                'unique:' . User::class . ',email'
            ],
            'document' => [
                'required',
                'regex:/\d{11}|\d{14}/',
                'unique:' . User::class . ',document'
            ],
            'balance' => 'required|numeric|max:1000000',
            'type' => [
                'required',
                Rule::in(self::USER_TYPES),
            ],
        ];
    }
}
