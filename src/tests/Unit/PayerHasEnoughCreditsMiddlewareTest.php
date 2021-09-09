<?php

namespace App;

use App\Http\Middleware\PayerHasEnoughCreditsMiddleware;
use App\Models\UserCustomer;
use Illuminate\Http\Request;
use TestCase;

class PayerHasEnoughCreditsMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->payer = UserCustomer::factory()->make();
        $this->middleware = new PayerHasEnoughCreditsMiddleware();
    }

    public function testShouldReturn400StatusCodeWhenCustomerHasNotEnoughCredits()
    {
        $request = new Request();

        $request->payer = $this->payer;
        $request->merge(['value' => $this->payer->credits + 1]);

        $response = $this->middleware->handle($request, function ($req) {
        });

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldReturnAnErrorMessageWhenCustomerHasNotEnoughCredits()
    {
        $request = new Request();

        $request->payer = $this->payer;
        $request->merge(['value' => $this->payer->credits + 1]);

        $response = $this->middleware->handle($request, function () {
        });

        $json = json_decode($response->getContent());

        $this->assertObjectHasAttribute('message', $json);
    }

    public function testShouldPassMiddlewareWhenValueIsLowerThanTransferenceValue()
    {
        $request = new Request();

        $request->payer = $this->payer;
        $request->merge(['value' => $this->payer->credits - 1]);

        $response = $this->middleware->handle($request, function () {
            return response()->json(['message' => 'Transferência realizada com sucesso.']);
        });

        $json = json_decode($response->getContent());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertObjectHasAttribute('message', $json);
        $this->assertEquals('Transferência realizada com sucesso.', $json->message);
    }

    public function testShouldPassMiddlewareWhenCustomerCreditsIsEqualToTransferenceValue()
    {
        $request = new Request();

        $request->payer = $this->payer;
        $request->merge(['value' => $this->payer->credits]);

        $response = $this->middleware->handle($request, function () {
            return response()->json(['message' => 'Transferência realizada com sucesso.']);
        });

        $json = json_decode($response->getContent());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertObjectHasAttribute('message', $json);
        $this->assertEquals($json->message, 'Transferência realizada com sucesso.');
    }
}
