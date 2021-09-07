<?php

namespace App;

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
}
