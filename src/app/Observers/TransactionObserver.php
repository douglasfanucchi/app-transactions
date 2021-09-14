<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    public function created(Transaction $transaction)
    {
        $payer = $transaction->payer()->first();
        $payee = $transaction->payee()->first();

        if (!$transaction->isValid($payer, $payee) || !$transaction->authorized()) {
            $transaction->rollback($payer, $payee);
            $transaction->status = 'failed';
            $transaction->save();
            throw new \Exception('Falha ao realizar a transação');
        }

        $transaction->status = 'success';
        $transaction->save();
    }
}
