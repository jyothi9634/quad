<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetworkNewsFeed extends Model
{
    // Table
    protected $table = 'network_newsfeed_updates';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];


    protected $fillable = [
    	'user_id', 
    	'updates_description', 
    	'created_by',
		'created_at',
		'created_ip',
		'updated_by',
		'updated_at',
		'updated_ip'
    ];
}
