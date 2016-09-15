<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\CommonComponent;
use DB;

class NetworkFeeds extends Model
{
    // Table
    protected $table = 'network_feeds';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];


    protected $fillable = [
        'user_id', 
        'feed_type',
        'feed_title',
        'feed_description', 
        'created_by',
        'created_at',
        'created_ip',
        'updated_by',
        'updated_at',
        'updated_ip',
    ];

    public function getFeedTitleAttribute($value){
        return ucfirst($value);
    }

    public function getShareFeedTitleAttribute($value){
        return ucfirst($value);
    }

    /**
     * Getting network users based on User ID, and returns comma-separated string
     * @author Shriram
     * @return object
     */
    public static function get_user_network_users( $userid = 0){
        
        $reqQuery = "SELECT CONVERT(GROUP_CONCAT(user_ids) USING 'utf8') as UserIDS
            FROM (
                SELECT 
                    CONVERT(GROUP_CONCAT(`partner_user_id`) USING 'utf8') as user_ids 
                    FROM `network_partners` as np1 WHERE `user_id` = ? AND is_approved = 1 
                UNION ALL
                (
                    SELECT 
                    CONVERT( GROUP_CONCAT(`user_id`) USING 'utf8') as user_ids 
                    FROM `network_partners` as np2 WHERE `partner_user_id` = ? AND is_approved = 1
                )
                UNION ALL
                (
                    SELECT 
                    CONVERT(GROUP_CONCAT(`follower_user_id`) USING 'utf8') as user_ids 
                    FROM `network_followers` WHERE `user_id` = ?
                )
                UNION ALL
                (
                    SELECT 
                    CONVERT(GROUP_CONCAT(`recommended_to`) USING 'utf8') as user_ids 
                    FROM `network_recommendations` WHERE `user_id` = ? AND `is_approved`=1
                )
                UNION ALL
                (
                    SELECT CONVERT(GROUP_CONCAT(`user_id`) USING 'utf8') as user_ids 
                    FROM `community_group_members`
                    WHERE `community_group_id` 
                    IN ( 
                        SELECT CONVERT(GROUP_CONCAT(`community_group_id`) USING 'utf8') as group_ids 
                        FROM `community_group_members` as cgm
                        JOIN `community_groups` as cg ON `cgm`.`community_group_id` = `cg`.`id` 
                        WHERE `cgm`.`user_id` = ? AND
                        ( (`cg`.`is_private`=1 AND `cg`.`is_confirmed`=1) OR (`cg`.`is_private`=0) )
                    )
                )

            ) tt";

        return DB::select(DB::raw($reqQuery), [$userid, $userid, $userid, $userid, $userid])[0]; 
    }
    

    /**
     * Getting network feeds of all three types
     *
     * @return object
     */
    public static function get_user_feeds( $userid = 0,$skip = 0, $paginated = true, $reqCond = [])
    {   
        /* Ex: Object[0] [UserIDS] => 314,102,102,314,333,314,103 */
        $userList = NetworkFeeds::get_user_network_users($userid);

        $finalUserArr = array_unique(explode(',', $userList->UserIDS));
        array_push($finalUserArr, $userid);

        $result = NetworkFeeds::from('network_feeds as nf')
            ->select( DB::raw( "nf.*, users.username, nfs.share_feed_id,users.user_pic,users.id as userid,users.lkp_role_id,
                nf9.feed_title as share_feed_title, nf9.feed_description as share_feed_description") 
            )
        ->leftJoin('users', 'users.id', '=', 'nf.user_id')
        ->leftJoin('network_feed_shares as nfs', 'nfs.feed_id', '=', 'nf.id')
        ->leftJoin('network_feeds as nf9', 'nfs.share_feed_id', '=', 'nf9.id')
        ->custom($reqCond)
        ->whereIn('nf.user_id', $finalUserArr);

        if($skip != 0){
            $result->skip($skip);
        }
        
        if($paginated == true){
            $result = $result->take(AJAX_LOAD_LIMIT);
        }

        return $result->orderBy('nf.created_at', 'desc')
            ->distinct()
            ->get();
    }

    /**
     * To get the designation
     *
     * @return object
     */
    public function scopeDesignation($query)
    { 
        // Feed Search keyword
        if(isset($reqCond['role']) && !empty($reqCond['role'])):
            // Todo
        endif;
    }

    /**
     * Additional Conditions
     *
     * @return object
     */
    public function scopeCustom($query, $reqCond)
    {   
        // Feed Search keyword
        if(isset($reqCond['search_keyword']) && !empty($reqCond['search_keyword'])):
            $query->where(function($query) use ($reqCond){
                $query->where('nf.feed_title', 'like', '%'.$reqCond['search_keyword'].'%');
                $query->orWhere('nf.feed_description', 'like', '%'.$reqCond['search_keyword'].'%');
            });
        endif;

        // Feed type
        if(isset($reqCond['feed_type']) && !empty($reqCond['feed_type'])):
            $query->where('nf.feed_type', '=', $reqCond['feed_type']);
        endif;

        // Feed from date
        if(isset($reqCond['feed_from_date']) && !empty($reqCond['feed_from_date'])):
            $query->where('nf.created_at', '>=', 
                CommonComponent::convertDateForDatabase($reqCond['feed_from_date']));
        endif;

        // Feed to date
        if(isset($reqCond['feed_to_date']) && !empty($reqCond['feed_to_date'])):
            $query->where('nf.created_at', '<=', 
                CommonComponent::convertDateForDatabase($reqCond['feed_to_date']));
        endif;
    }

    /**
     * Get Profile related feeds
     *
     * @return object
     */
    public static function get_feed_user_id( $reqCond = [], $skip = 0, $paginated = true)
    {   
        $result = NetworkFeeds::from('network_feeds as nf')
            ->select( DB::raw( "nf.*, users.username, nfs.share_feed_id, 
                nf9.feed_title as share_feed_title, nf9.feed_description as share_feed_description, 
                nf9.feed_type as share_feed_type"
                ) 
            )
        ->leftJoin('users', 'users.id', '=', 'nf.user_id')
        ->leftJoin('network_feed_shares as nfs', 'nfs.feed_id', '=', 'nf.id')
        ->leftJoin('network_feeds as nf9', 'nfs.share_feed_id', '=', 'nf9.id')
        ->where('nf.user_id', $reqCond['user_id'])
        ->whereIn('nf.feed_type', (is_array($reqCond['feed_types']))? $reqCond['feed_types']:explode(',',$reqCond['feed_types']) );

        if($skip != 0){
            $result->skip($skip);
        }
        
        if($paginated == true){
            $result = $result->take(AJAX_LOAD_LIMIT);
        }

        return $result->orderBy('nf.created_at', 'desc')
            ->distinct()
            ->get();
    }

}