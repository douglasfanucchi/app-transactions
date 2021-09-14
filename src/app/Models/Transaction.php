<?php

namespace App\Models;

use App\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

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

    protected static function boot()
    {
        parent::boot();
        Transaction::observe(TransactionObserver::class);
    }

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

        return abs(($payer->previous_credits - $value) - $payer->credits) < 0.0001
                && abs(($payee->previous_credits + $value) - $payee->credits) < 0.0001;
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

        if ($payerLastValidTransaction === null) {
            $payer->credits = $payer->previous_credits;
            $payer->previous_credits = 0;
        } else {
            $payer->rollbackToTransaction($payerLastValidTransaction);
        }

        if ($payeeLastValidTransaction === null) {
            $payee->credits = $payee->previous_credits;
            $payee->previous_credits = 0;
        } else {
            $payee->rollbackToTransaction($payeeLastValidTransaction);
        }

        $payee->save();
        $payer->save();
    }
}
