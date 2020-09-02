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
    private $paymentGateway;

    protected function setUp(): void{
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway;
        // Bind the FakePaymentGateway class to the PaymentGateway interface so we can type hint the
        // interface in the controller methods.
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    private function orderTickets($concert, $params){
        $this->json('POST', "/concerts/{$concert->id}/orders", $params);
    }

    private function assertValidationError($field){
        // Laravel uses response code 422 for validation error responses
        $this->assertResponseStatus(422);

        // Assert that there are json validation errors
        $this->response->assertJsonValidationErrors($field);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function customer_can_purchase_concert_tickets()
    {


        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->create(['ticket_price' => 3250]);

        // Act
        // Purchase concert tickets
        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        // Assert
        $this->assertResponseStatus(201);

        // Make sure the customer was charged the correct amount
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        // Make sure that an order exists for this customer
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets->count());

    }

    /** @test */
    public function email_is_required_to_purchase_tickets(){

        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->create();

        // Act
        // Purchase concert tickets
        $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        // Assert that there are json validation errors
        $this->assertValidationError('email');

    }

    /** @test */
    public function email_must_be_valid_to_purchase_tickets(){

        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->create();

        // Act
        // Purchase concert tickets
        $this->orderTickets($concert, [
            'email' => 'not-an-email-address',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        // Assert that there are json validation errors
        $this->assertValidationError('email');

    }


    /** @test */
    public function ticket_quantity_is_required_to_purchase_tickets(){

        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->create();

        // Act
        // Purchase concert tickets
        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            //'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        // Assert that there are json validation errors
        $this->assertValidationError('ticket_quantity');

    }

    /** @test */
    public function ticket_quantity_must_be_at_least_1_to_purchase_tickets(){

        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->create();

        // Act
        // Purchase concert tickets
        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        // Assert that there are json validation errors
        $this->assertValidationError('ticket_quantity');

    }

    /** @test */
    public function payment_token_is_required_to_purchase_tickets(){

        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->create();

        // Act
        // Purchase concert tickets
        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            //'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        // Assert that there are json validation errors
        $this->assertValidationError('payment_token');

    }

    /** @test */
    public function an_order_is_not_created_if_payment_fails(){

        $this->disableExceptionHandling();
        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->create();

        // Act
        // Purchase concert tickets
        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token',
        ]);

        //use response code 422 for unprocessable entity
        $this->assertResponseStatus(422);

        // Make sure that an order does not exists for this customer
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNull($order);

    }
}
