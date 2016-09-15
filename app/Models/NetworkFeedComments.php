<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class NetworkFeedComments extends Model
{
    // Table
    protected $table = 'network_feed_comments';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];


    protected $fillable = [
    	'feed_id', 
    	'user_id',
        'comments',
        'is_reply',
        'reply_to_comment_id',
    	'created_by',
		'created_at',
		'created_ip',
		'updated_by',
		'updated_at',
		'updated_ip',
    ];

    /**
     * Getting all feed Comments based on feed id
     *
     * @return object
     */
    public static function get_feed_comments( $feed_id = 0, $skip = 0, $paginated = true, $reqCond = [])
    {
        $result = NetworkFeeds::from('network_feed_comments as nfc')
            ->select( DB::raw("nfc.*, users.username, users.id as comment_user_id, users.lkp_role_id,users.user_pic,users.id as userid") )
            ->leftJoin('users', 'users.id', '=', 'nfc.user_id')
            ->where('nfc.feed_id', '=', $feed_id);

        if($skip != 0){
            $result->skip($skip);
        }
        
        if($paginated == true){
            $result = $result->take(AJAX_LOAD_LIMIT);
        }

        return $result->orderBy('nfc.created_at', 'DESC')->get();
                
    }
    
    public function getCreatedAtAttribute($date)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }
}
