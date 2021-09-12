<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\UserCustomer;
use Closure;
use Illuminate\Http\Request;

class ParseUsersFromRequest
{
    public function handle(Request $request, Closure $next)
    {
        $payerId = (int) $request->input('payer');
        $payeeId = (int) $request->input('payee');
        $payer = UserCustomer::find($payerId);
        $payee = User::find($payeeId);

        if ($payer === null) {
            return response()->json(['message' => 'Id payer inválido'], 400);
        }

        if ($payee === null) {
            return response()->json(['message' => 'Id de payee inválido'], 400);
        }

        $request->payer = $payer;
        $request->payee = $payee;

        return $next($request);
    }
}
