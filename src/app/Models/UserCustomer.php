<?php

namespace App\Models;

class UserCustomer extends User
{
    protected $table = 'users';

    public function pay(float $value)
    {
        $this->previous_credits = $this->credits;
        $this->credits -= $value;
    }

    public function transactionAsPayer()
    {
        return $this->belongsTo(Transaction::class, 'payer');
    }

    public function rollbackToTransaction(
        Transaction $transaction
    ) {
        $isCurrentUserPayee = $transaction->payee === $this->id;

        if ($isCurrentUserPayee) {
            return parent::rollbackToTransaction($transaction);
        }

        $this->credits = $transaction->payer_current_credits;
        $this->previous_credits = $transaction->payer_previous_credits;
    }
}
