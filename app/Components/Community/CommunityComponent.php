<?php
namespace App\Components\Community;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use App\Components\CommonComponent;
use App\Models\User;
use Log;

class CommunityComponent {

	/**
	 * Get Community Group data depends on particulr Id
	 * Date : 7-04-2016 (srinu code start here)
	 * Params {{ $id }} 
	 */
		
	public static function getCommunityGroupData($id){

		try {			
			$getCommunityNewGroup = DB::table('community_groups')
			->where('community_groups.id', $id)
			//->where('community_groups.created_by', Auth::User ()->id)
			->select('community_groups.created_by','community_groups.id', 'community_groups.group_name', 'community_groups.description',
					'community_groups.is_acknowledged', 'community_groups.is_private', 'community_groups.logo_file_name', 'community_groups.is_confirmed')
			->first();
			return $getCommunityNewGroup;
			
		} catch (Exception $e) {
	
		}		
	}
	
	/**
	 * Get Community Group All Conversation data
	 * Date : 8-04-2016 (srinu code start here)
	 * Params {{ $id }}
	 */
	
	public static function getGroupConversationData($id,$skip = 0, $paginated = true){
	
		try {
			$getGroupconversationData = DB::table('community_group_comments as cgc')			
			->join  ( 'users as us', 'cgc.user_id', '=', 'us.id' )
			->where('cgc.is_reply', 0)->where('cgc.community_group_id', $id)
			->select('cgc.id', 'cgc.title', 'cgc.comments',
					 'cgc.created_by','cgc.created_at','us.username','us.logo');
			if($skip != 0){
				$getGroupconversationData->skip($skip);
			}
			if($paginated == true){
				$getGroupconversationData = $getGroupconversationData->take(AJAX_LOAD_LIMIT);
			}
			$getGroupconversationData = $getGroupconversationData->get();
			return $getGroupconversationData;
				
		} catch (Exception $e) {
	
		}
	}
	
	/**
	 * Get Community Post all Comments
	 * Date : 11-04-2016 (srinu code start here)
	 * Params {{ $groupId,$PostId }}
	 */
	
	public static function getMainPostComments($groupId,$PostId,$skip = 0, $paginated = true){
	
		try {
			$getAllComments = DB::table('community_group_comments as cgc')			
			->join  ( 'users as us', 'cgc.user_id', '=', 'us.id' )
			->where('cgc.community_group_id', $groupId)
			->where('cgc.reply_to_comment_id', $PostId)
			->where('cgc.is_reply', 1)
			->select('cgc.id', 'cgc.title', 'cgc.comments',
					'cgc.created_by','cgc.created_at','us.username','us.logo','cgc.user_id');
			if($skip != 0){
				$getAllComments->skip($skip);
			}
			if($paginated == true){
				$getAllComments = $getAllComments->take(AJAX_LOAD_LIMIT);
			}
			$getAllComments->orderBy('cgc.id', 'DESC');
			$getAllComm = $getAllComments->get();
			//echo "<pre>"; print_r($getAllComm); die;
			$getAllComm = array_reverse($getAllComm);
			return $getAllComm;
	
		} catch (Exception $e) {
	
		}
	}
	
	/*
	 * Print Limits of description in our pages
	 */	
	public static function getDescLimit($desc,$limit){
		
		try {				
			//$displayLimitchars= str_limit($desc, $limit);
                       $displayLimitchars= substr($desc,0,250);
			return $displayLimitchars;		
		} catch (Exception $e) {
		
		}
	}
	
	/**
	 * Get Community Post Like count
	 * Date : 11-04-2016 (srinu code start here)
	 * Params {{ $postId }}
	 */
	public static function postLikesCount($id){
	
		try {
			$getPostLikesCount = DB::table('community_group_likes')
			->where('community_group_likes.community_group_comment_id', $id)			
			->count();
			return $getPostLikesCount;
				
		} catch (Exception $e) {
	
		}
	}
	
	/**
	 * Get Community Post Like TExt like or unlike
	 * Date : 12-04-2016 (srinu code start here)
	 * Params {{ $postId and $UserId }}
	 */
	public static function postLikesTextChangeFun($id){
	
		try {
			$getPostLikesTextChange = DB::table('community_group_likes')
			->where('community_group_likes.community_group_comment_id', $id)
			->where('community_group_likes.user_id', Auth::id())
			->count();
			return $getPostLikesTextChange;
	
		} catch (Exception $e) {
	
		}
	}
        
        /**
	 * Get Community Post Like count
	 * Date : 11-04-2016 (srinu code start here)
	 * Params {{ $postId }}
	 */
	public static function postCommCount($groupId,$PostId){
	
		try {
			$getPostCommCount = DB::table('community_group_comments as cgc')	
			->where('cgc.community_group_id', $groupId)
			->where('cgc.reply_to_comment_id', $PostId)
			->where('cgc.is_reply', 1)
			->count();
			
			return $getPostCommCount;
				
		} catch (Exception $e) {
	
		}
	}
	
	/**
	 * Get Community All Manage Members
	 * Date : 11-04-2016 (srinu code start here)
	 * Params {{ $id }}
	 */
	
	public static function getAllManageMembers($id){
	
		try {
			$getAllMemebersData = DB::table('community_group_members')
			->join  ( 'users as us', 'community_group_members.user_id', '=', 'us.id' )
			->where('community_group_members.community_group_id', $id)		
			->where('community_group_members.is_approved', 0)
			->select('community_group_members.user_id','community_group_members.community_group_id',
					 'us.username','us.logo')
			->get();
			return $getAllMemebersData;
				
		} catch (Exception $e) {
	
		}
	}
        
        //get group member Partners list
	public static function getGroupMemberPartners($id) {
		try {
			$partners  = DB::table('community_groups as group')
                                ->leftjoin  ( 'community_group_members as gm', 'gm.community_group_id', '=', 'group.id' )
                        ->leftjoin('network_partners as p','p.partner_user_id','=','gm.user_id')
                        ->leftjoin  ( 'users as us', 'gm.user_id', '=', 'us.id' )        
			->where('p.user_id',Auth::id())
                        ->where('gm.is_approved',1) 
                        ->where('gm.is_invited',1)         
                        ->where('group.id',$id)
			->select('us.username','us.logo','us.id')
			->take(10)->get();
			return $partners;
	
		} catch (Exception $exc) {
		}
	}
	
        //get Members check in group
	public static function getMemberData($id,$uid) {
		try {
                    $members=array();
                    $qry = DB::table('community_groups');
			 $qry->leftjoin('community_group_members','community_groups.id','=','community_group_members.community_group_id')
			->where('community_groups.id',$id);
			$qry->whereRaw('community_group_members.user_id ='.$uid);
			$members=$qry->select('community_group_members.is_approved')->first();
			return $members;
	
		} catch (Exception $exc) {
		}
	}
        
        /**
	 * Get Community Post Like user names
	 * Date : 11-04-2016 (srinu code start here)
	 * Params {{ $postId }}
	 */
	public static function postLikesNames($id){
	
		try {
			$getPostLikesNames = DB::table('community_group_likes')
                        ->leftjoin('users','users.id','=','community_group_likes.user_id')        
			->where('community_group_likes.community_group_comment_id', $id)			
			->select('users.username')->get();
                        
			return $getPostLikesNames;
				
		} catch (Exception $e) {
	
		}
	}
        
        /**
	 * Get Community All Active Members
	 * Date : 11-04-2016 (srinu code start here)
	 * Params {{ $id }}
	 */
	
	public static function getAllActiveMembers($id){
	
		try {
			$getAllMemebersData = DB::table('community_group_members')
			->join  ( 'users as us', 'community_group_members.user_id', '=', 'us.id' )
			->where('community_group_members.community_group_id', $id)		
			->where('community_group_members.is_approved', 1)
			->select('community_group_members.user_id','community_group_members.community_group_id',
					 'us.username','us.logo')
			->get();
			return $getAllMemebersData;
				
		} catch (Exception $e) {
	
		}
	}
    
        
}
