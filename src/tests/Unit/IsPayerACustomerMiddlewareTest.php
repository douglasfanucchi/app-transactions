<?php

namespace App;

use App\Http\Middleware\IsPayerACustomerMiddleware;
use App\Models\User;
use App\Models\UserCustomer;
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

        $this->failedResponse = $middleware->handle($request, function ($req) {
            return response()->json(['message' => 'Fim da requisição']);
        });

        $request->payer = UserCustomer::factory()->make();

        $this->successResponse = $middleware->handle($request, function () {
            return response()->json(['message' => 'Requisição finalizada']);
        });
    }

    public function testShouldSentAnErrorWhenPayerIsNotCustomer()
    {
        $this->assertEquals($this->failedResponse->getStatusCode(), 400);
    }

    public function testShouldReturnAMessageErrorWhenPayerIsNotCustomer()
    {
        $json = json_decode($this->failedResponse->getContent());

        $this->assertObjectHasAttribute('message', $json);
        $this->assertNotEmpty($json->message);
    }

    public function testShouldPassMiddlewareWhenPayerIsACustomer()
    {
        $json = json_decode($this->successResponse->getContent());

        $this->assertEquals(200, $this->successResponse->getStatusCode());
        $this->assertObjectHasAttribute('message', $json);
        $this->assertEquals($json->message, 'Requisição finalizada');
    }
}
