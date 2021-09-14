<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class Transactions extends Controller
{
    public function store(Request $request)
    {
        $transactionValue = (float) $request->input('value');

        if ($transactionValue <= 0) {
            return response()->json(['message' => 'Valor da transação tem de ser maior que zero.']);
        }

        $payer = $request->payer;
        $payee = $request->payee;

        $transaction = new Transaction();
        $transaction->payer = $payer->id;
        $transaction->payee = $payee->id;

        $payer->pay($transactionValue);
        $payee->receivePayment($transactionValue);

        $transaction->value = $transactionValue;
        $transaction->payer_current_credits = $payer->credits;
        $transaction->payer_previous_credits = $payer->previous_credits;
        $transaction->payee_current_credits = $payee->credits;
        $transaction->payee_previous_credits = $payee->previous_credits;

        try {
            $payer->save();
            $payee->save();
            $transaction->save();
            return response()->json(['message' => 'Transação realizada com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
