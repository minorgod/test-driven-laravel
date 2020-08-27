<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{

    use DatabaseMigrations;


    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function customer_can_purchase_concert_tickets()
    {

        $paymentGateway = new FakePaymentGateway;
        // Bind the FakePaymentGateway class to the PaymentGateway interface so we can type hint the
        // interface in the controller methods.
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->create(['ticket_price' => 3250]);

        // Act
        // Purchase concert tickets
        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $paymentGateway->getValidTestToken(),
        ]);

        // Assert

        $this->assertResponseStatus(201);

        // Make sure the customer was charged the correct amount
        $this->assertEquals(9750, $paymentGateway->totalCharges());

        // Make sure that an order exists for this customer
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets->count());

    }

    /** @test */
    public function email_is_required_to_purchase_tickets(){

        // Arrange
        $paymentGateway = new FakePaymentGateway;
        // Bind the FakePaymentGateway class to the PaymentGateway interface so we can type hint the
        // interface in the controller methods.
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        // Create a concert
        $concert = factory(Concert::class)->create();

        // Act
        // Purchase concert tickets
        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'ticket_quantity' => 3,
            'payment_token' => $paymentGateway->getValidTestToken(),
        ]);

        // Laravel uses response code 422 for validation error responses
        $this->assertResponseStatus(422);

        // Assert that there are json validation errors
        $this->response->assertJsonValidationErrors('email');

    }
}
