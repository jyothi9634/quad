<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserProfileViews extends Model
{
    // Table
    protected $table = 'user_profile_views';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    // Table fields
    protected $fillable = [
    ];

    // Disble timestaps
    public $timestamps = false;


    // Get profile view count based on last login
    public function scopeProfileViewCount($query, $last_login)
    {
        return $query->whereDate('user_last_login', '=', date('Y-m-d', strtotime($last_login)) );
    }

}
