<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable;
    use Authorizable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'document', 'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function receivePayment(float $value)
    {
        $this->previous_credits = $this->credits;
        $this->credits += $value;
    }

    public function transactionAsPayee()
    {
        return $this->hasMany(Transaction::class, 'payee');
    }

    public function rollbackToTransaction(
        Transaction $transaction
    ) {
        $this->credits = $transaction->payee_current_credits;
        $this->previous_credits = $transaction->payee_previous_credits;
    }
}
