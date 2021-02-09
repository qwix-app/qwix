<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Normal;
use App\Models\Transaction;
use App\Models\User;
use ErrorException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $transactions = Transaction::where('payer', '=', $request->query('payer'))
            ->where('payee', '=', $request->query('payee'))
            ->paginate(10);

        return TransactionResource::collection($transactions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $payer = Normal::findOrFail($request->get('payer'));
            $payee = User::findOrFail($request->get('payee'));

            $amount = $request->get('value');

            $request->merge([
                'value' => $amount,
                'payer_prev_bal_snapshot' => $payer->balance,
                'payer_cur_bal_snapshot' => $payer->balance - $amount,
                'payee_prev_bal_snapshot' => $payee->balance,
                'payee_cur_bal_snapshot' => $payee->balance + $amount,
            ]);

            $payer->fill(['balance' => $request->get('payer_cur_bal_snapshot')]);
            $payee->fill(['balance' => $request->get('payee_cur_bal_snapshot')]);

            $this->validate($request, Transaction::getRules());

            $transaction = Transaction::create($request->all());
            $payer->save();
            $payee->save();

            return new TransactionResource($transaction);
        } catch (ModelNotFoundException $ex) {
            abort(400, "Invalid accounts provided.");
        } catch (ValidationException $ex) {
            abort(422, "Transfer not completed. Please make sure the transferred"
                . " amount does not exceed the current balance in your account.");
        } catch (\Illuminate\Database\QueryException $ex) {
            abort(500, "Fatal internal error. Contact your account manager.");
        }

        return new TransactionResource($transaction);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $transactionId
     * @return \Illuminate\Http\Response
     */
    public function show(int $transactionId)
    {
        try {
            $transaction = Transaction::findOrFail($transactionId);

            return new TransactionResource($transaction);
        } catch (ModelNotFoundException $e) {
            abort(404, "A transaction with id #$transactionId could not be found.");
        }
    }
}
