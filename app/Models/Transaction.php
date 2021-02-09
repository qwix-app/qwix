<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    
    public $fillable = [
        'value',
        'payer',
        'payer_prev_bal_snapshot',
        'payer_cur_bal_snapshot',
        'payee',
        'payee_prev_bal_snapshot',
        'payee_cur_bal_snapshot',
    ];

    public $hidden = [
        'payer_prev_bal_snapshot',
        'payer_cur_bal_snapshot',
        'payee_prev_bal_snapshot',
        'payee_cur_bal_snapshot',
    ];

    public static function getRules()
    {
        return [
            'value' => [
                'required',
                'numeric',
                'min:0.01',
                'max:100000',
            ],
            'payer.balance' => 'gte:value',
            'payer' => [
                'required',
                'exists:' . Normal::class . ',id',
                'different:payee',
            ],
            'payee' => [
                'required',
                'exists:' . User::class . ',id',
                'different:payer',
            ],
            'payer_prev_bal_snapshot' => 'required',
            'payer_cur_bal_snapshot' => [
                'required',
                'gte:0',
                // ensures the amount is being deduced from payer
                'lt:payer_prev_bal_snapshot',
            ],
            'payee_prev_bal_snapshot' => 'required',
            'payee_cur_bal_snapshot' => [
                'required',
                'gte:0',
                // ensures the amount is being transfered to payee
                'gt:payee_prev_bal_snapshot',
            ]
        ];
    }

    public function payer()
    { 
        return $this->belongsTo(Normal::class, 'payer', 'id');
    }

    public function payee()
    { 
        return $this->belongsTo(User::class, 'payee', 'id');
    }
}
