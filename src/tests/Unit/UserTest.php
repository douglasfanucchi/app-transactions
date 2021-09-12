<?php

namespace App;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserCustomer;
use TestCase;

class UserTest extends TestCase
{
    public function testUserPaymentReceivement()
    {
        $payee = User::factory()->defineUserAsSeller()->make();

        $initialCredits = $payee->credits;
        $paymentValue = 10.00;

        $payee->receivePayment($paymentValue);

        $this->assertEqualsWithDelta($initialCredits + $paymentValue, $payee->credits, 0.0001);
    }

    public function testUserSentPayment()
    {
        $payer = UserCustomer::factory()->make();

        $initialCredits = $payer->credits;
        $paymentValue = 10.00;

        $payer->pay($paymentValue);

        $this->assertEqualsWithDelta($initialCredits - $paymentValue, $payer->credits, 0.0001);
    }

    public function testShouldRollbackUserSellerCreditsToFirstTransaction()
    {
        $payer = UserCustomer::factory()->make();
        $payee = User::factory()->make();
        $value = 100;
        $transactions = [
            new Transaction(),
            new Transaction(),
            new Transaction(),
        ];
        $firstTransaction = $transactions[0];

        foreach ($transactions as $transaction) {
            $this->mockTransaction($transaction, $payer, $payee, $value);
        }

        $payee->rollbackToTransaction($firstTransaction);

        $this->assertEqualsWithDelta($firstTransaction->payee_current_credits, $payee->credits, 0.0001);
        $this->assertEqualsWithDelta($firstTransaction->payee_previous_credits, $payee->previous_credits, 0.0001);
    }

    public function testShouldRollbackUserCustomerCreditsToFirstTransaction()
    {
        $payer = UserCustomer::factory()->make();
        $payee = User::factory()->make();
        $payer->id = 1;
        $payee->id = 2;
        $value = 100;
        $transactions = [
            new Transaction(),
            new Transaction(),
            new Transaction(),
        ];
        $firstTransaction = $transactions[0];

        foreach ($transactions as $transaction) {
            $this->mockTransaction($transaction, $payer, $payee, $value);
        }

        $payer->rollbackToTransaction($firstTransaction);

        $this->assertEqualsWithDelta($firstTransaction->payer_current_credits, $payer->credits, 0.0001);
    }

    protected function mockTransaction(Transaction $transaction, UserCustomer $payer, User $payee, float $value)
    {
        $transaction->payer = $payer->id;
        $transaction->payee = $payee->id;
        $transaction->value = $value;

        $payer->previous_credits = $payer->credits;
        $payer->pay($value);

        $payee->previous_credits = $payee->credits;
        $payee->receivePayment($value);

        $transaction->payer_current_credits = $payer->credits;
        $transaction->payer_previous_credits = $payer->previous_credits;
        $transaction->payee_current_credits = $payee->credits;
        $transaction->payee_previous_credits = $payee->previous_credits;
    }
}
