<?php namespace App\Http\Middleware;

// First copy this file into your middleware directoy

use Closure;
use Auth;
use Illuminate\Auth\Authenticatable;

class CheckRole{

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		// Get the required roles from the route
		$roles = $this->getRequiredRoleForRoute($request->route());	
		
		// Check if a role is required for the route, and
		// if so, ensure that the user has that role.
		if($this->hasRole($roles) || !$roles)
		{
			return $next($request);
		}		
		return redirect('error')->with('message', 'You are not authorized to view this page');
	}

	private function getRequiredRoleForRoute($route)
	{
		$actions = $route->getAction();
		return isset($actions['roles']) ? $actions['roles'] : null;
	}
	
	public function hasRole($roles)
	{		
		if(is_array($roles)){
			foreach($roles as $need_role){
				if($this->checkIfUserHasRole($need_role)) {
					return true;
				}
			}
		} else{
			return $this->checkIfUserHasRole($roles);
		}
		return false;
	}	
	private function checkIfUserHasRole($need_role)
	{
		return (strtolower($need_role)==strtolower(Auth::User()->lkp_role_id)) ? true : false;
	}

}
