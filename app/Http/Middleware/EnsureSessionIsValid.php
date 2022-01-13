<?php

namespace App\Http\Middleware;

use App\Models\KolSession;
use Closure;
use Illuminate\Http\Request;

class EnsureSessionIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!empty($request->kol_session_id)) {
            $kolSession = KolSession::find($request->kol_session_id);
            if(!empty($kolSession->end_date_time)) {
                return sendFailResponse('Session has ended');
            }
        }

        return $next($request);
    }
}
