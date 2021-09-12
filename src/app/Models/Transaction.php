<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payer', 'payee', 'value'
    ];

    public function payer()
    {
        return $this->belongsTo(UserCustomer::class, 'payer', 'id');
    }

    public function payee()
    {
        return $this->belongsTo(User::class, 'payee', 'id');
    }

    public function isValid(UserCustomer $payer, User $payee): bool
    {
        $value = $this->value;

        return $payer->previous_credits - $value === $payer->credits
                && $payee->previous_credits + $value === $payee->credits;
    }

    public function rollback(UserCustomer $payer, User $payee)
    {
        $payerLastValidTransaction = Transaction::where('status', 'success')
                                        ->where(function ($query) use ($payer) {
                                            $query->where('payee', $payer->id)
                                                ->orWhere('payer', $payer->id);
                                        })
                                        ->orderByDesc('created_at')
                                        ->first();

        $payeeLastValidTransaction = Transaction::where('status', 'success')
                                        ->where(function ($query) use ($payee) {
                                            $query->where('payee', $payee->id)
                                                ->orWhere('payer', $payee->id);
                                        })
                                        ->orderByDesc('created_at')
                                        ->first();

        $payer->rollbackToTransaction($payerLastValidTransaction);
        $payee->rollbackToTransaction($payeeLastValidTransaction);

        $payee->save();
        $payer->save();
    }
}
