<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class NetworkFeedLikes extends Model
{
    // Table
    protected $table = 'network_feed_likes';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];


    protected $fillable = [
    	'feed_id', 
    	'user_id',
        'is_liked',
        'created_by',
		'created_at',
		'created_ip',
		'updated_by',
		'updated_at',
		'updated_ip',
    ];

}
