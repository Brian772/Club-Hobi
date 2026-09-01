<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    protected array $alwaysAllowedRoutes = [
        'home',
        'login',
        'banned',
        'register',
        'appeal.create',
        'appeal.store',
        'logout',
    ];
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        if ($user->status === 'suspended' && $user->suspended_until && now()->greaterThan($user->suspended_until)) {
            $user->update([
                'status' => 'active',
                'suspended_until' => null,
            ]);
        }

        $routeName = $request->route()->getName();

        if ($user->status === 'banned') {
            if (!in_array($routeName, $this->alwaysAllowedRoutes)) {
                return redirect()->route('banned')->with('error', 'Your account has been banned. You cannot access this page.');
            }
        }

        if ($user->status === 'suspended') {
            $isWrite = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
            if ($isWrite && !in_array($routeName, $this->alwaysAllowedRoutes)) {
                return redirect()->route('dashboard')->with('warning', 'Your account has been suspended. You can only view content.');
            }
        }

        return $next($request);
    }
}
