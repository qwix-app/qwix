<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public $fillable = [
        'value',
        'payer_id',
        'payer_prev_bal_snapshot',
        'payer_cur_bal_snapshot',
        'payee_id',
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
            'payer_id' => [
                'required',
                'exists:' . Normal::class . ',id',
                'different:payee_id',
            ],
            'payee_id' => [
                'required',
                'exists:' . User::class . ',id',
                'different:payer_id',
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
        return $this->belongsTo(Normal::class, 'payer_id', 'id');
    }

    public function payee()
    {
        return $this->belongsTo(User::class, 'payee_id', 'id');
    }

    public function commit()
    {
        $this->payer->balance = $this->payer_cur_bal_snapshot;
        $this->payee->balance = $this->payee_cur_bal_snapshot;
        $this->successful = true;
        $this->push();
    }

    public function rollback()
    {
        $this->payer->balance = $this->payer_prev_bal_snapshot;
        $this->payee->balance = $this->payee_prev_bal_snapshot;
        $this->successful = false;
        $this->push();
    }
}
