<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;

class StartSessionForApi extends StartSession
{
    public function handle($request, Closure $next)
    {
        if (!$request->session()->isStarted()) {
            $request->session()->start();
        }

        return parent::handle($request, $next);
    }
}
