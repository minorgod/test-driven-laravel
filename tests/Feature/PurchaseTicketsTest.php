<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\BrowserKitTestCase;

/**
 * Class PurchaseTicketsTest
 * @package Tests\Feature
 */
class PurchaseTicketsTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    private $paymentGateway;
    protected $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway();
        // Bind the FakePaymentGateway class to the PaymentGateway interface so we can type hint the
        // interface in the controller methods.
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    /**
     * @param $concert
     * @param $params
     */
    private function orderTickets($concert, $params)
    {
        $this->json('POST', "/concerts/{$concert->id}/orders", $params);
    }

    /**
     * @param $field
     */
    private function assertValidationError($field)
    {
        // Laravel uses response code 422 for validation error responses
        $this->assertResponseStatus(422);

        // Assert that there are json validation errors
        $this->response->assertJsonValidationErrors($field);
        //$this->assertJsonValidationErrors($field);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function customer_can_purchase_tickets_to_a_published_concert()
    {

        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250])->addTickets(3);


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
        $this->assertTrue($concert->hasOrderFor('john@example.com'));

        // Make sure there's 3 tickets in the order
        $this->assertEquals(3, $concert->ordersFor( 'john@example.com')->first()->ticketQuantity());


    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function cannot_purchase_tickets_to_an_unpublished_concert()
    {

        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->states('unpublished')->create()->addTickets(3);

        // Act
        // Purchase concert tickets
        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        // Unpublished concerts should return a 404
        $this->assertResponseStatus(404);

        // Make sure no orders were created
        $this->assertFalse($concert->hasOrderFor('john@example.com'));

        // Make sure customer was not charged
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    /** @test */
    public function an_order_is_not_created_if_payment_fails()
    {
        $this->disableExceptionHandling();
        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->states('published')->create()->addTickets(3);

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
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
    }

    /** @test */
    public function cannot_purchase_more_tickets_than_remain()
    {

        //$this->disableExceptionHandling();
        // Arrange
        // Create a concert with 50 tickets available
        $concert = factory(Concert::class)
            ->states('published')->create()
            ->addTickets(50);


        // Act
        // Purchase concert tickets
        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 51,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        //make sure we have the proper response code
        $this->assertResponseStatus(422);

        //make sure not orders are created
        $this->assertFalse($concert->hasOrderFor('john@example.com'));

        //make sure customer is not charged
        $this->assertEquals(0, $this->paymentGateway->totalCharges());

        //make sure there's still 50 tickets left
        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    public function email_is_required_to_purchase_tickets()
    {

        // Arrange
        // Create a concert with 3 tickets available
        $concert = factory(Concert::class)
            ->states('published')->create()
            ->addTickets(3);

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
    public function email_must_be_valid_to_purchase_tickets()
    {

        // Arrange
        // Create a concert with 3 tickets available
        $concert = factory(Concert::class)
            ->states('published')->create()
            ->addTickets(3);

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
    public function ticket_quantity_is_required_to_purchase_tickets()
    {

        // Arrange
        // Create a concert with 3 tickets available
        $concert = factory(Concert::class)
            ->states('published')->create()
            ->addTickets(3);

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
    public function ticket_quantity_must_be_at_least_1_to_purchase_tickets()
    {

        // Arrange
        // Create a concert with 3 tickets available
        $concert = factory(Concert::class)
            ->states('published')->create()
            ->addTickets(3);

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
    public function payment_token_is_required_to_purchase_tickets()
    {

        // Arrange
        // Create a concert
        // Create a concert with 3 tickets available
        $concert = factory(Concert::class)
            ->states('published')->create()
            ->addTickets(3);

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


}
