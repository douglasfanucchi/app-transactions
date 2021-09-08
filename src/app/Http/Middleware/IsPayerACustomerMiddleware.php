<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsPayerACustomerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $payer = $request->payer;

        if ($payer->type !== 'customer') {
            return response()->json(
                array('message' => 'Apenas usuários comuns podem fazer uma transferência.'),
                400
            );
        }

        $next($request);
    }
}
