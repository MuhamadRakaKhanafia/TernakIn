<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('CheckAuth middleware: ' . $request->path(), [
            'user_id' => Auth::id(),
            'is_authenticated' => Auth::check(),
            'session_id' => session()->getId(),
            'has_session' => session()->has('login_web_' . Auth::id()),
            'all_session_keys' => array_keys(session()->all()),
            'request_method' => $request->method(),
            'request_headers' => [
                'accept' => $request->header('Accept'),
                'content_type' => $request->header('Content-Type'),
                'x_requested_with' => $request->header('X-Requested-With'),
            ]
        ]);

        if (!Auth::check()) {
            Log::warning('User not authenticated, redirecting to login', [
                'session_data' => session()->all(),
                'cookies' => $request->cookies->all()
            ]);

            // For AJAX requests, return JSON response
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.',
                    'redirect' => route('login')
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
