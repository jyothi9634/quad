<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetworkFeedShare extends Model
{
    // Table
    protected $table = 'network_feed_shares';

    public $timestamps = false;

    protected $fillable = [
    	'feed_id', 
    	'share_feed_id'
    ];
}
