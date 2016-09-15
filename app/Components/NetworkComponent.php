<?php namespace App\Components;

use Auth;

use DB;
use Input;
use Session;

use Illuminate\Support\Facades\Mail;
use Zofe\Rapyd\Facades\DataGrid;


use App\Components\CommonComponent;

use App\Models\User;
use App\Models\UserMessage;
use App\Models\UserMessageUpload;
use App\Models\NetworkRecommendations;
use App\Models\NetworkFeeds;
use App\Models\UserProfileShares;



class NetworkComponent{

    /**
     * Get User Followers, Network & Recomm count
     */
    public static function get_user_network_count($user_id, $reqTable = ''){
        
        switch($reqTable):
            case 'partner':
                $row = \App\Models\NetworkPartners::selectRaw('count(*) as totalCount')
                    ->where(['user_id' => $user_id])
                    ->first();
                return $row->totalCount;

            case 'follower':
                $row = \App\Models\NetworkFollowers::selectRaw('count(*) as totalCount')
                    ->where(['follower_user_id' => $user_id])
                    ->first();
                return $row->totalCount;
                
            case 'recommendation':
                $row = \App\Models\NetworkRecommendations::selectRaw('count(*) as totalCount')
                    ->where(['user_id' => $user_id])
                    ->first();
                return $row->totalCount;
                        
            default:
                return '';
        endswitch;
    }


	/**
	 * Get all Feeds
	 */
   
    public static function getPartnerRequestList($id) {
    	try {
    		$getpartners = DB::table('network_partners as np')
    		->leftjoin ( 'users as us', 'us.id', '=', 'np.user_id' )
			->where('np.partner_user_id', $id)
			->where('np.is_approved', '=',0)
			->select('np.*','us.username')
			->get();
    		return $getpartners;
    	} catch(\Exception $e) {
    		//return $e->message;
    	}
    }
    public static function getPartnerAcceptList($id) {
    	try {
    		$getpartners = DB::table('network_partners as np')
    		->leftjoin ( 'users as us', 'us.id', '=', 'np.user_id' )
    		->where('np.partner_user_id', $id)
    		->where('np.is_approved', '=',1)
    		->select('np.*','us.username','us.id as user_id','us.lkp_role_id','us.user_pic')
    		->get();
    		return $getpartners;
    	} catch(\Exception $e) {
    		//return $e->message;
    	}
    }
    public static function getPartnerPersonalAcceptList($id) {
    	try {
    		$getpartners = DB::table('network_partners as np')
    		->leftjoin ( 'users as us', 'us.id', '=', 'np.partner_user_id' )
    		->where('np.user_id', $id)
    		->where('np.is_approved', '=',1)
    		->select('np.*','us.username','us.id as user_id','us.lkp_role_id','us.user_pic')
    		->get();
    		return $getpartners;
    	} catch(\Exception $e) {
    		//return $e->message;
    	}
    }
    public static function addRecomendationtoProfile($userid,$body) {
    	try {
    		$created_at = date ( 'Y-m-d H:i:s' );
    		$createdIp = $_SERVER['REMOTE_ADDR'];
    		$addrecom = new NetworkRecommendations();
    		$addrecom->recommendation_description = $body;
    		$addrecom->user_id= Auth::user()->id;
    		$addrecom->recommended_to =$userid;
    		$addrecom->is_approved =0;
    		$addrecom->email_sent =1;
    		$addrecom->created_at  =$created_at;
    		$addrecom->created_ip = $createdIp;
			$addrecom->created_by = Auth::user()->id;
    		$addrecom->save();
    		
    		
    		$addmessage = new UserMessage();
    		$addmessage->lkp_service_id = 0;
    		$addmessage->sender_id = Auth::User()->id;
    		$addmessage->recepient_id = $userid;
    		$addmessage->lkp_message_type_id = 9;
    		$addmessage->message_no = "Recomendation";
    		$addmessage->subject = "Recomendation";
    		$addmessage->message = $body;
    		$addmessage->is_read = 0;
    		$addmessage->created_at = date('Y-m-d H:i:s');
    		$addmessage->created_ip = $_SERVER['REMOTE_ADDR'];
    		$addmessage->created_by = Auth::User()->id;
    		$addmessage->save();
    		
    		$addmessage = DB::table('users')->where('id', $userid)->get();
    		$addmessage[0]->sender = Auth::User()->username;
    		CommonComponent::send_email(RECOMENDATION,$addmessage);
    		
    		
    		return $addrecom;
    	} catch(\Exception $e) {
    		//return $e->message;
    	}
    }
    
    public static function addMessagesToProfile($userid,$subject,$body) {
    	try {
    		
    		$addmessage = new UserMessage();
    		$addmessage->lkp_service_id = 0;
    		$addmessage->sender_id = Auth::User()->id;
    		$addmessage->recepient_id = $userid;
    		$addmessage->lkp_message_type_id = 9;
    		$addmessage->message_no = "Network Message";
    		$addmessage->subject = $subject;
    		$addmessage->message = $body;
    		$addmessage->is_read = 0;
    		$addmessage->created_at = date('Y-m-d H:i:s');
    		$addmessage->created_ip = $_SERVER['REMOTE_ADDR'];
    		$addmessage->created_by = Auth::User()->id;
    		$addmessage->save();
    		
    		$addmessage = DB::table('users')->where('id', $userid)->get();
    		$addmessage[0]->sender = Auth::User()->username;
    		CommonComponent::send_email(PROFILE_MESSAGE,$addmessage);
    
    		return $addmessage;
    	} catch(\Exception $e) {
    		//return $e->message;
    	}
    }
    
    //Profile sharing insertion
    public static function userProfileShare($userid,$subject,$body,$link) {
    	try {
    		$shareids = explode(",",$userid);
    		
    		$created_at = date ( 'Y-m-d H:i:s' );
    		$createdIp = $_SERVER['REMOTE_ADDR'];
    		
    		for($i=1;$i<count($shareids);$i++){
	    		$addshare = new UserProfileShares();
	    		$addshare->share_to_user_id = $shareids[$i];
	    		$addshare->user_id= Auth::user()->id;
	    		$addshare->subject= $subject;
	    		$addshare->body= $body;
	    		$addshare->shared_url =$link;
	    		$addshare->created_at  =$created_at;
	    		$addshare->created_ip = $createdIp;
	    		$addshare->created_by = Auth::user()->id;
	    		$addshare->save();
	    		
	    		$created_year = date('Y');
	    		$randnumber   = 'SharingRequest/' .$created_year .'/'.$addshare->id;
	    		$sharemessage = new UserMessage();
	    		$sharemessage->lkp_service_id = 0;
	    		$sharemessage->sender_id = Auth::User()->id;
	    		$sharemessage->recepient_id = $shareids[$i];
	    		$sharemessage->lkp_message_type_id = 9;
	    		$sharemessage->message_no = $randnumber;
	    		$sharemessage->subject = "Sharing Profile Request";
	    		$sharemessage->message = "Recieved New Sharing Profile Request <a href=$link>Click Here</a>";
	    		$sharemessage->is_read = 0;
	    		$sharemessage->created_at = date('Y-m-d H:i:s');
	    		$sharemessage->created_ip = $_SERVER['REMOTE_ADDR'];
	    		$sharemessage->created_by = Auth::User()->id;
	    		$sharemessage->save();
	    		
	    		$sharemessage = DB::table('users')->where('id', $shareids[$i])->get();
	    		$sharemessage[0]->sender = Auth::User()->username;
	    		CommonComponent::send_email(SHARE_REQUEST,$sharemessage);
	    		
    		}
    		return $addshare;
    	} catch(\Exception $e) {
    		//return $e->message;
    	}
    }
    public static function partnerRequestAcceptence($userid) {
    	try {
    		$getpartnersacepted = DB::table('network_partners as np')
    		->where('np.partner_user_id', Auth::user()->id)
    		->where('np.user_id', $userid)
    		->update(array('np.is_approved' => 1));
    		
    		$username= CommonComponent::getUsername(Auth::user()->id);
    		$partner= CommonComponent::getUsername($userid);
    		
    		//Insert Into Feeds
    		$created_at = date ( 'Y-m-d H:i:s' );
    		$createdIp = $_SERVER['REMOTE_ADDR'];
    		$addrecom = new NetworkFeeds();
    		$addrecom->feed_type = "partner";
    		$addrecom->feed_title= "Partner Request Accepted";
    		$addrecom->feed_description =$username." accepted ".$partner."'s Partners Request";
    		$addrecom->user_id =Auth::user()->id;
    		$addrecom->created_by =Auth::user()->id;
    		$addrecom->created_at  =$created_at;
    		$addrecom->created_ip = $createdIp;
    		$addrecom->updated_by =Auth::user()->id;
    		$addrecom->save();
    		
    		
    		return $getpartnersacepted;
    	} catch(\Exception $e) {
    		//return $e->message;
    	}
    }
    //Recomendation received
    public static function partnerRecomendationApproval($userid,$status,$type) {
    	try {
    		if($type == 1){
    			
    		$getapproval = DB::table('network_recommendations as nr')
    		->where('nr.user_id', $userid)
    		->where('nr.recommended_to', Auth::user()->id)
    		->update(array('nr.is_approved' => $status));
    		//get details
    		$getdetails = DB::table('network_recommendations as nr')
    		->where('nr.user_id', $userid)
    		->where('nr.recommended_to', Auth::user()->id)
    		->select('nr.recommendation_description','nr.recommended_to','nr.user_id')
    		->get();
    		
    		$recomend_name = CommonComponent::getUsername($getdetails[0]->recommended_to);
    		$user_name = CommonComponent::getUsername($getdetails[0]->user_id);
    		
    		$title = '<a href="/network/profile/'.$getdetails[0]->user_id.'">'.$user_name.'</a> recommended <a href="/network/profile/'.$getdetails[0]->recommended_to.'">'.$recomend_name.'</a>';
    		//Insert Into Feeds
    		
    		$created_at = date ( 'Y-m-d H:i:s' );
    		$createdIp = $_SERVER['REMOTE_ADDR'];
    		$addrecom = new NetworkFeeds();
    		$addrecom->feed_type = "recomend";
    		$addrecom->feed_title= $title;
    		$addrecom->feed_description =$getdetails[0]->recommendation_description;
    		$addrecom->user_id =$getdetails[0]->recommended_to;
    		$addrecom->created_by =$getdetails[0]->user_id;
    		$addrecom->created_at  =$created_at;
    		$addrecom->created_ip = $createdIp;
    		$addrecom->save();
    		
    		return $getapproval;
    		}
    		else{
    			 echo"dfvzdsfds";
    			$getapproval=DB::table('network_recommendations')
    			->where('recommended_to', $userid)
    			->where('user_id', Auth::user()->id)
    			->delete();
    			return $getapproval;
    			 
    		}
    	} catch(\Exception $e) {
    		//return $e->message;
    	}
    }
    
    
    
    /** Retrieval of Services of user  **/
    public static function getBuyerServices($user_id)
    {
    	$result = array();
    	try
    	{
    		
    			$ftlservices = DB::table ( 'buyer_quotes as bq' )
    			->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    			->join ( 'buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' )
    			->where('bqi.lkp_post_status_id','=',OPEN)
    			->where('bq.buyer_id','=',$user_id)
    			->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    			if(empty($ftlservices)){
    			$ftlservices_term = DB::table ( 'term_buyer_quotes as bq' )
    			->where('bq.buyer_id','=',$user_id)
    			->where('bq.lkp_service_id','=',ROAD_FTL)
    			->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    			->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    			$result[] = $ftlservices_term;
    			}else{
    			$result[] = $ftlservices;
    			}
    			$ltlservices = DB::table ( 'ptl_buyer_quotes as bq' )
    			->where('bq.buyer_id','=',$user_id)
    			->where('bq.lkp_post_status_id','=',OPEN)
    			->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    			->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    			
    			if(empty($ltlservices)){
    				$ltlservices_term = DB::table ( 'term_buyer_quotes as bq' )
    				->where('bq.buyer_id','=',$user_id)
    				->where('bq.lkp_post_status_id','=',OPEN)
    				->where('bq.lkp_service_id','=',ROAD_PTL)
    				->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    				$result[] = $ltlservices_term;
    			}else{
    				$result[] = $ltlservices;
    			}
    			
    			$ictservices = DB::table ( 'ict_buyer_quotes as bq' )
    			->where('bq.buyer_id','=',$user_id)
    			->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    			->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    			$result[] = $ictservices;
    			$railservices = DB::table ( 'rail_buyer_quotes as bq' )
    			->where('bq.buyer_id','=',$user_id)
    			->where('bq.lkp_post_status_id','=',OPEN)
    			->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    			->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    			if(empty($railservices)){
    				$railservices_term = DB::table ( 'term_buyer_quotes as bq' )
    				->where('bq.buyer_id','=',$user_id)
    				->where('bq.lkp_post_status_id','=',OPEN)
    				->where('bq.lkp_service_id','=',RAIL)
    				->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    				$result[] = $railservices_term;
    			}else{
    				$result[] = $railservices;
    			}
    			
    			$airdomservices = DB::table ( 'airdom_buyer_quotes as bq' )
    			->where('bq.buyer_id','=',$user_id)
    			->where('bq.lkp_post_status_id','=',OPEN)
    			->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    			->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    			if(empty($airdomservices)){
    				$airdomservices_term = DB::table ( 'term_buyer_quotes as bq' )
    				->where('bq.buyer_id','=',$user_id)
    				->where('bq.lkp_post_status_id','=',OPEN)
    				->where('bq.lkp_service_id','=',AIR_DOMESTIC)
    				->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    				$result[] = $airdomservices_term;
    			}else{
    				$result[] = $airdomservices;
    			}
    			$airintservices = DB::table ( 'airint_buyer_quotes as bq' )
    			->where('bq.buyer_id','=',$user_id)
    			->where('bq.lkp_post_status_id','=',OPEN)
    			->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    			->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    			
    			if(empty($airintservices)){
    				$airintservices_term = DB::table ( 'term_buyer_quotes as bq' )
    				->where('bq.buyer_id','=',$user_id)
    				->where('bq.lkp_post_status_id','=',OPEN)
    				->where('bq.lkp_service_id','=',AIR_INTERNATIONAL)
    				->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    				$result[] = $airintservices_term;
    			}else{
    				$result[] = $airintservices;
    			}
    			
    			$oceanservices = DB::table ( 'ocean_buyer_quotes as bq' )
    			->where('bq.buyer_id','=',$user_id)
    			->where('bq.lkp_post_status_id','=',OPEN)
    			->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    			->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    			
    			if(empty($oceanservices)){
    				$oceanservices_term = DB::table ( 'term_buyer_quotes as bq' )
    				->where('bq.buyer_id','=',$user_id)
    				->where('bq.lkp_post_status_id','=',OPEN)
    				->where('bq.lkp_service_id','=',OCEAN)
    				->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    				$result[] = $oceanservices_term;
    			}else{
    				$result[] = $oceanservices;
    				 
    			}
    			
    			
    			$hualservices = DB::table ( 'truckhaul_buyer_quotes as bq' )
    			->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    			->join ( 'truckhaul_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' )
    			->where('bqi.lkp_post_status_id','=',OPEN)
    			->where('bq.buyer_id','=',$user_id)
    			->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    			$result[] = $hualservices;
    			
    			$leaseservices = DB::table ( 'trucklease_buyer_quotes as bq' )
    			->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    			->join ( 'trucklease_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' )
    			->where('bqi.lkp_post_status_id','=',OPEN)
    			->where('bq.buyer_id','=',$user_id)
    			->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    			$result[] = $leaseservices;
    			
    			
    			$courierservices = DB::table ( 'courier_buyer_quotes as bq' )
    			->where('bq.buyer_id','=',$user_id)
    			->where('bq.lkp_post_status_id','=',OPEN)
    			->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    			->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    			$result[] = $courierservices;
    			
    			$rdservices = DB::table ( 'relocation_buyer_posts as bq' )
    			->where('bq.buyer_id','=',$user_id)
    			->where('bq.lkp_post_status_id','=',OPEN)
    			->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    			->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    			if(empty($rdservices)){
    				$rdservices_term = DB::table ( 'term_buyer_quotes as bq' )
    				->where('bq.buyer_id','=',$user_id)
    				->where('bq.lkp_post_status_id','=',OPEN)
    				->where('bq.lkp_service_id','=',RELOCATION_DOMESTIC)
    				->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    				$result[] = $rdservices_term;
    			}else{
    				$result[] = $rdservices;
    					
    			}    
    			 
    		
    		$finalresult = array();
    		foreach($result as $res){
    			foreach($res as $key => $resservice){
    				$finalresult[$key] = $resservice;
    			}
    		}
    		return $finalresult;
    		 
    	}
    	catch(\Exception $e)
    	{
    		//return $e->message;
    	}
    }
    
    
    /** Retrieval of Services of sellers  **/
    public static function getSellerServices($user_id)
    {
    	$result = array();
    	try
    	{
    		$role_id = DB::table('users')->where('id', $user_id)
    		->select('lkp_role_id')
    		->first();
    
    		$ftlservices = DB::table ( 'seller_posts as bq' )
    		->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    		->where('bq.seller_id','=',$user_id)
    		->where('bq.lkp_post_status_id','=',OPEN)
    		->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    		$result[] = $ftlservices;
    		    		
    		$ltlservices = DB::table ( 'ptl_seller_posts as bq' )
    		->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    		->where('bq.seller_id','=',$user_id)
    		->where('bq.lkp_post_status_id','=',OPEN)
    		->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    		$result[] = $ltlservices;
    		

    		$railservices = DB::table ( 'rail_seller_posts as bq' )
    		->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    		->where('bq.seller_id','=',$user_id)
    		->where('bq.lkp_post_status_id','=',OPEN)
    		->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    		$result[] = $railservices;
    		 
    		$airdomservices = DB::table ( 'airdom_seller_posts as bq' )
    		->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    		->where('bq.seller_id','=',$user_id)
    		->where('bq.lkp_post_status_id','=',OPEN)
    		->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    		$result[] = $airdomservices;
    		
    		$airintservices = DB::table ( 'airint_seller_posts as bq' )
    		->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    		->where('bq.seller_id','=',$user_id)
    		->where('bq.lkp_post_status_id','=',OPEN)
    		->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    		 
    		$result[] = $airintservices;
    		 
    		$oceanservices = DB::table ( 'ocean_seller_posts as bq' )
    		->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    		->where('bq.seller_id','=',$user_id)
    		->where('bq.lkp_post_status_id','=',OPEN)
    		->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    		$result[] = $oceanservices;

    		$courierservices = DB::table ( 'courier_seller_posts as bq' )
    		->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    		->where('bq.seller_id','=',$user_id)
    		->where('bq.lkp_post_status_id','=',OPEN)
    		->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    		$result[] = $courierservices;
    		 
    		
    		$truckhaulservices = DB::table ( 'truckhaul_seller_posts as bq' )
    		->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    		->where('bq.seller_id','=',$user_id)
    		->where('bq.lkp_post_status_id','=',OPEN)
    		->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    		$result[] = $truckhaulservices;
    		
    		
    		$truckleaseservices = DB::table ( 'trucklease_seller_posts as bq' )
    		->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    		->where('bq.seller_id','=',$user_id)
    		->where('bq.lkp_post_status_id','=',OPEN)
    		->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    		$result[] = $truckleaseservices;
    		
    		
    		$rdservices = DB::table ( 'relocation_seller_posts as bq' )
    		->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
    		->where('bq.seller_id','=',$user_id)
    		->where('bq.lkp_post_status_id','=',OPEN)
    		->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
    		$result[] = $rdservices;

    
    
    		$finalresult = array();
    		foreach($result as $res){
    			foreach($res as $key => $resservice){
    				$finalresult[$key] = $resservice;
    			}
    		}
    		return $finalresult;
    		 
    	}
    	catch(\Exception $e)
    	{
    		//return $e->message;
    	}
    }
    
}
