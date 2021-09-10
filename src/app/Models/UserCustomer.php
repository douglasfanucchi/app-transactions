<?php

namespace App\Models;

class UserCustomer extends User
{
    public function pay(float $value)
    {
        $this->credits -= $value;
    }

    public function transactionAsPayer()
    {
        return $this->belongsTo(Transaction::class, 'payer');
    }
}
