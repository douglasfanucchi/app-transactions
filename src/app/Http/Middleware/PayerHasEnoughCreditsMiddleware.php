<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PayerHasEnoughCreditsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $payer = $request->payer;
        $trasferenceValue = $request->input('value');

        if ($payer->credits < $trasferenceValue) {
            return response()->json(['message' => 'Usuário não possui créditos o suficiente.'], 400);
        }

        return $next($request);
    }
}
