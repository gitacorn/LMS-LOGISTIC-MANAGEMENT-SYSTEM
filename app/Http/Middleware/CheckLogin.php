<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Request;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
	
	public $attributes;
    public function handle($request, Closure $next)
    {
       if( ( Session::has('isLoggedIn') ) && ( Session::get('isLoggedIn') != false ) ){
    	if( Session::get('site_title') == config('constants.SITE_TITLE')  ){
    			$request->loggedUserId = ( Session::has('user_id') ? Session::get('user_id') : 0 ) ;
    			return $next($request);
    		}
    	}
    	Session::put('url.intended',Request::url());
    	return redirect('login');
    }
}
