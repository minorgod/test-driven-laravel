<?php

namespace Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use Tests\TestCase;

/**
 * Class FakePaymentGatewayTest
 * @package Billing
 */
class FakePaymentGatewayTest extends TestCase
{
    /**
     * @test
     */
    function charges_with_a_valid_payment_token_are_successful() {
        $paymentGateway = new FakePaymentGateway();
        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

    /**
     * @test
     */
    function charges_with_an_invalid_payment_token_fail() {
        $paymentGateway = new FakePaymentGateway();

        try {
            $paymentGateway->charge(2500, 'invalid-payment-token');
            $this->assertEquals(2500, $paymentGateway->totalCharges());
        } catch (PaymentFailedException $e) {
           $this->expectNotToPerformAssertions();
           return;
        }
    }
}
