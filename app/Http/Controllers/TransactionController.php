<?php

namespace App\Http\Controllers;

use App\Helpers\MockyApiHelper;
use App\Http\Resources\TransactionResource;
use App\Models\Normal;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TransactionController extends Controller
{
    private MockyApiHelper $extApi;

    public function __construct(MockyApiHelper $extApi)
    {
        $this->extApi = $extApi;
    }

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
                'payer_id' => $payer->id,
                'payee_id' => $payee->id,
                'payer_prev_bal_snapshot' => $payer->balance,
                'payer_cur_bal_snapshot' => $payer->balance - $amount,
                'payee_prev_bal_snapshot' => $payee->balance,
                'payee_cur_bal_snapshot' => $payee->balance + $amount,
            ]);

            $this->validate($request, Transaction::getRules());

            $transaction = Transaction::create($request->all());

            $this->extApi->authorizeTransaction(
                $amount,
                $payer,
                $payee
            );

            $transaction->commit();

            return new TransactionResource($transaction);
        } catch (Exception $ex) {
            if (isset($transaction))
                $transaction->rollback();

            switch (get_class($ex)) {
                case ModelNotFoundException::class:
                    abort(400, "No valid accounts provided.");
                    break;
                case UnauthorizedHttpException::class:
                    abort(401, "Unauthorized transaction.");
                    break;
                case ValidationException::class:
                    abort(422, "Please make sure the transferred amount"
                    . " does not exceed the current balance in your account.");
                case QueryException::class:
                    abort(500, "Fatal internal error. Contact your account manager.");
                    break;
                default:
                    abort(500, "Unexpected server error.");
                    break;
            }
        }

        $this->extApi->nudgePayee($transaction);

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
