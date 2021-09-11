<?php

namespace App;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserCustomer;
use TestCase;

class TransactionTest extends TestCase
{
    protected function getFixture()
    {
        return [
            new Transaction(),
            User::factory()->make(),
            UserCustomer::factory()->make(),
        ];
    }

    public function testValidMethodShouldReturnFalseWhenPreviousCreditIsNotUpdated()
    {
        list( $transaction, $payee, $payer ) = $this->getFixture();

        $transaction->value = 10;
        $transaction->payee = $payee->id;
        $transaction->payer = $payer->id;

        $payer->credits -= 10;
        $payee->credits += 10;

        $this->assertFalse($transaction->isValid($payer, $payee));
    }

    public function testValidMethodShouldReturnFalseWhenCreditsIsNotUpdated()
    {
        list( $transaction, $payee, $payer ) = $this->getFixture();

        $transaction->value = 10;
        $transaction->payee = $payee->id;
        $transaction->payer = $payer->id;

        $payer->previous_credits = $payer->credits;
        $payee->previous_credits = $payee->credits;

        $this->assertFalse($transaction->isValid($payer, $payee));
    }

    public function testShouldReturnFalseWhenWrongValueIsAddedToPayee()
    {
        list( $transaction, $payee, $payer ) = $this->getFixture();

        $transaction->value = 10;
        $transaction->payer = $payer->id;
        $transaction->payee = $payee->id;

        $payer->previous_credits = $payer->credits;
        $payee->previous_credits = $payee->credits;

        $payer->credits -= 10;
        $payee->credits += 20;

        $this->assertFalse($transaction->isValid($payer, $payee));
    }

    public function testShouldReturnFalseWhenWrongValueIsSubtractedFromPayer()
    {
        list( $transaction, $payee, $payer ) = $this->getFixture();

        $transaction->value = 10;
        $transaction->payer = $payer->id;
        $transaction->payee = $payee->id;

        $payer->previous_credits = $payer->credits;
        $payee->previous_credits = $payee->credits;

        $payer->credits -= 20;
        $payee->credits += 10;

        $this->assertFalse($transaction->isValid($payer, $payee));
    }

    public function testValidMethodShouldReturnFalseWhenATransactionIsInvalid()
    {
        list( $transaction, $payee, $payer ) = $this->getFixture();

        $transaction->value = 10;
        $transaction->payee = $payee->id;
        $transaction->payer = $payer->id;

        $this->assertFalse($transaction->isValid($payer, $payee));
    }

    public function testValidMethodShouldReturnTrueWhenATransactioIsValid()
    {
        list( $transaction, $payee, $payer ) = $this->getFixture();

        $transaction->value = 10;
        $transaction->payee = $payee->id;
        $transaction->payer = $payer->id;

        $payer->previous_credits = $payer->credits;
        $payee->previous_credits = $payee->credits;
        $payer->credits -= 10;
        $payee->credits += 10;

        $this->assertTrue($transaction->isValid($payer, $payee));
    }
}
