<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;
    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'email',
			'password',
			'is_business' 
	];
     /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
}
