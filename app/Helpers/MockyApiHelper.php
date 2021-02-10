<?php

namespace App\Helpers;

use App\Models\Normal;
use App\Models\Transaction;
use App\Models\User;
use GuzzleHttp\Client;

class MockyApiHelper extends Client
{
  public function __construct()
  {
    parent::__construct([
      'base_uri' => env('MOCKY_BASE_URI'),
    ]);
  }

  public function authorizeTransaction(float $value, Normal $payer, User $payee)
  {
    return $this->request('POST', env('MOCKY_AUTH_URI'), [
      'form_params' => [
        'amount' => $value,
        'source' => $payer->document,
        'target' => $payee->document,
      ],
    ]);
  }

  public function nudgePayee(Transaction $transaction)
  {
    return $this->request('POST', env('MOCKY_NOTIFICATION_URI'), [
      'form_params' => [
        'email' => $transaction->payee->email,
        'message' => "A total amount of {$transaction->value} was just "
          . "transferred into your account from {$transaction->payer->full_name}.",
      ],
    ]);
  }
}