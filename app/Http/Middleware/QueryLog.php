<?php

namespace App\Http\Middleware;

use Closure;
use DB;
class QueryLog
{
    public function handle($request, Closure $next)
    {
        DB::listen(function ($query){
            info($query->sql);
        });
        return $next($request);
    }
}
