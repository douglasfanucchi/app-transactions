<?php

namespace App;

use App\Http\Middleware\IsPayerACustomerMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use TestCase;

class IsPayerACustomerMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $request = new Request();
        $request->payer = User::factory()->defineUserAsSeller()->make();
        $middleware = new IsPayerACustomerMiddleware();

        $this->response = $middleware->handle($request, function ($req) {
            return response()->json(['message' => 'Fim da requisição']);
        });
    }

    public function testShouldSentAnErrorWhenPayerIsNotCustomer()
    {
        $this->assertEquals($this->response->getStatusCode(), 400);
    }

    public function testShouldReturnAMessageErrorWhenPayerIsNotCustomer()
    {
        $json = json_decode($this->response->getContent());

        $this->assertObjectHasAttribute('message', $json);
        $this->assertNotEmpty($json->message);
    }
}
