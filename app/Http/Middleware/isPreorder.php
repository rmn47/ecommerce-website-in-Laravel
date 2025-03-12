<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class isPreorder
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (addon_is_activated('preorder')) {

            // if auth user is seller but preorder product for seller is not activated
            if(Auth::check() && Auth::user()->user_type == 'seller' && (get_setting('seller_preorder_product') == 0)){
                abort(404);
            }

            return $next($request);
        }
        else{
            abort(404);
        }
    }
}
