<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiToken
{
    use ApiResponser;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $user = User::where('access_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Auth::login($user);

        return $next($request);
        // $bearerToken = $request->bearerToken();

        // if (!$bearerToken) {
        //     return $this->errorResponse('Unauthorized - No token provided', null, 401);
        // }

        // $accessToken = PersonalAccessToken::findToken($bearerToken);

        // if (!$accessToken) {
        //     return $this->errorResponse('Unauthorized - Invalid token', null, 401);
        // }

        // // Check if token is expired
        // if ($accessToken->expires_at && Carbon::now()->greaterThan($accessToken->expires_at)) {
        //     return $this->errorResponse('Unauthorized - Token has expired', null, 401);
        // }

        // // Update last used timestamp
        // $accessToken->last_used_at = Carbon::now();
        // $accessToken->save();

        // // Set the authenticated user
        // $request->setUserResolver(function () use ($accessToken) {
        //     return $accessToken->tokenable;
        // });

        // return $next($request);
    }
}
