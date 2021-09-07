<?php

namespace App;

use App\Models\User;
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
}
