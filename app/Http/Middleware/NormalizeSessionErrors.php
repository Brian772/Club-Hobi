<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class NormalizeSessionErrors
{
    /**
     * Normalize the session errors bag so it is always a MessageBag, not a plain array.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->hasSession()) {
            $errors = $request->session()->get('errors');

            if (is_array($errors)) {
                $request->session()->put('errors', new MessageBag($errors));
            }
        }

        return $response;
    }
}
