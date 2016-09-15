<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;

use App\Components\NetworkComponent;
use App\Components\CommonComponent;
use DB;
use Input;
use Config;
use File;
use Session;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use Log;

//Required Models
use Validator;
use Illuminate\Http\Response;

class NetworkController extends Controller {

    /**
     * Create a new Orders controller instance.
     *
     * @return void
     */
    public $user_pk;

    public function __construct() {
        $this->middleware('auth', [
            'except' => 'getLogout'
        ]);
        if (isset(Auth::User()->id)) {

            $this->user_pk = Auth::User()->id;
        }
        
    }

    /**
     * Saves all 3 types of posting [ feed, job, article]
     * @author Shriram
     */
    public function ajxpostfeed(Request $request){
		
		// Checking post type is feed, job or article
		if($request->has('feedtype')):

			// getting post type
			$postType = $request->feedtype;
			switch($postType):

				case 'job':
					// Checking validation
					$validator = Validator::make($request->all(), [ 'ptitle' => 'required|max:200',
				            'pdesc' => 'required|max:3000' ], [ 
			            	'ptitle.required' => 'The Job title is required',
			            	'pdesc.required' => 'The Job description is required',
							'ptitle.max' => 'The Job title may not be greater than 200 characters ',
							'pdesc.max' => 'The Job description may not be greater than 3000 characters ',
			        	]
			        );
					
					if($validator->fails()):
						return response()->json([ 'success' => false,
							'errors' => $validator->messages()
						]);
					endif;

					break;

				case 'article':
					
					// Checking validation
					$validator = Validator::make($request->all(), [
				            'ptitle' => 'required|max:200',
				            'pdesc' => 'required|max:3000' 
			            ], [ 
			            	'ptitle.required' => 'The Article title is required',
			            	'pdesc.required' => 'The Article description is required',
							'ptitle.max' => 'The Article title may not be greater than 200 characters ',
							'pdesc.max' => 'The Article description may not be greater than 3000 characters ',
			        	]
			        );

					if($validator->fails()):
						return response()->json([ 'success' => false,
							'errors' => $validator->messages()
						]);
					endif;
					break;
				
				case 'feed':
				default:
					
					// Checking validation
					$validator = Validator::make($request->all(), [
			            'pdesc' => 'required|max:3000'
			        ], ['pdesc.required' => 'The Status feed is required',
						'pdesc.max' => 'The Status feed may not be greater than 3000 characters ',]);

					if($validator->fails()):
						return response()->json([ 'success' => false,
							'errors' => $validator->messages()
						]);
					endif;
					break;	

			endswitch;

			// Network feeds will saves all 3 type of feeds
			$networkFeed = \App\Models\NetworkFeeds::create([
				'feed_type' 	=> $request->feedtype,
				'feed_title'	=> $request->has('ptitle')? $request->ptitle:NULL,
				'feed_description' => $request->pdesc,
				'user_id' 		=> $this->user_pk,
				'created_by' 	=> $this->user_pk,
				'created_ip'	=> $request->ip(),
				'updated_by'	=> $this->user_pk,
				'updated_ip'	=> $request->ip()
			]);

			// Success response
			return response()->json([ 'success' => true, 'feedId' => $networkFeed->id ]);

		endif;

		// Error response
		return response()->json([
			'success' => false
		]);
	}

	/**
     * Post Feed Comment
     * @author Shriram
     */
    public function ajxpostcomment(Request $request){

    	// Checking validation
		$validator = Validator::make($request->all(), 
				['comment' => 'required|max:200'], [ 
            	'comment.required' => 'Empty comment',
            	'comment.max' => 'Comment may not be greater than 200 characters'
        	]
        );
		
		if($validator->fails()):
			return response()->json([ 'success' => false,
				'errors' => $validator->messages()
			]);
		endif;

    	$feedComment = \App\Models\NetworkFeedComments::create([
			'feed_id' 		=> $request->feedid,
			'user_id' 		=> $this->user_pk,
			'comments' 		=> $request->comment,
			'is_reply'		=> 0,
			'reply_to_comment_id' => 0,
			'created_by' 	=> $this->user_pk,
			'created_ip'	=> $request->ip(),
			'updated_by'	=> $this->user_pk,
			'updated_ip'	=> $request->ip()
		]);

		// Success response
		return response()->json([ 
			'success' => true,
			'commInfo' => \App\Models\NetworkFeedComments::find($feedComment->id)
		]);
    }

    /**
     * Feed like Count
     * @author Shriram
     */
    public function ajxfeedlike(Request $request){
    	
    	$feedLike = new \App\Models\NetworkFeedLikes;

    	$feedCnt = $feedLike::where(['feed_id' => $request->feedid,
    		'user_id' => $this->user_pk,
    	])->first();
    	
    	// If already liked, it will goes to unlike action
    	$like_status = 1;
    	if($feedCnt){

    		// Checking present record is in Like or Unliked State
    		$like_status = ($feedCnt->is_liked)? 0:1;
    		$feedComment = $feedLike::where([
    			'feed_id' => $request->feedid, 'user_id' => $this->user_pk
    			])->update([
				'is_liked' 		=> $like_status,
				'updated_by'	=> $this->user_pk,
				'updated_ip'	=> $request->ip()
			]);

    	}else{

    		$feedComment = $feedLike::create([
				'feed_id' 		=> $request->feedid,
				'user_id' 		=> $this->user_pk,
				'is_liked' 		=> $like_status,
				'created_by' 	=> $this->user_pk,
				'created_ip'	=> $request->ip(),
				'updated_by'	=> $this->user_pk,
				'updated_ip'	=> $request->ip()
			]);
    	}

		// Success response
		return response()->json(['success' => true, 'actionType' => $like_status]);
    }

    /**
     * Show ajax post on successfull insertion
     * @author Shriram
     */
    public function ajx_showpost(Request $request){
    	
    	$feedInfo = \App\Models\NetworkFeeds::from('network_feeds as nf')
    		->select( DB::raw("nf.*, users.username") )
	        ->leftJoin('users', 'users.id', '=', 'nf.user_id')
	        ->where([ 'nf.id' => $request->feedid, 'nf.user_id' => $this->user_pk ])
	        ->first();

    	return view('networks.newsfeed.single-feed-post',[
			'feedInfo' => $feedInfo
		]);
    }

    /**
     * Load more Comments by Feed ID
     * @author Shriram
     */
    public function ajx_load_more_comments(Request $request){
    	
    	$page = isset($_REQUEST['page']) ? $request->page : 1;
        $skip = $page * 5;
        
        // Comment Class
        $FeedComment = new \App\Models\NetworkFeedComments;

        // Get feed Comments
        $feedComments = $FeedComment::get_feed_comments($request->feed_id, $skip, true);
        $moreexists = (CommonComponent::feedComents($request->feed_id, 'count') > $skip + 5) ? true: false;

        // Feed Info
        $feedInfo = \App\Models\NetworkFeeds::from('network_feeds as nf')
    		->select( DB::raw("nf.*, users.username") )
	        ->leftJoin('users', 'users.id', '=', 'nf.user_id')
	        ->where([ 'nf.id' => $request->feed_id])
	        ->first();

	    $returnHTML = view('networks.newsfeed.ajax-single-feed-comments',[
						'feedComments' => $feedComments,
						'feedInfo' => $feedInfo
					])->with('CComponent', new CommonComponent)->render(); 

	    return response()->json(array('success' => true, 'html'=>$returnHTML, 'more'=>$moreexists));
    }

    /**
     * Gets Share post content and will display on pop-up
     * @author Shriram
     */
    public function ajx_sharepost(Request $request){
    	
    	$feedShare = new \App\Models\NetworkFeeds;
    	// Checking shareAction or Popup HTML
    	if($request->feedtype == 'view'):
    		return view('networks.newsfeed.share-feed-post',[
				'feedInfo' => $feedShare::from('network_feeds as nf')
	    		->select( DB::raw("nf.*, users.username") )
		        ->leftJoin('users', 'users.id', '=', 'nf.user_id')
		        ->where([ 'nf.id' => $request->feedid ])
		        ->first()
			]);
    	elseif($request->feedtype == 'action'):
    		
    		// Checking validation
    		$v = Validator::make($request->all(), 
    			['txtFeedShare' => 'required'], ['txtFeedShare.required' => 'Please enter your comments']
    		);
    		if($v->fails()):
				return response()->json([ 'success' => false, 'errors' => $v->messages()]);
			endif;

    		// Saves share record on network feed table
    		$feedInfo = $feedShare::create([
				'feed_type' 		=> 'share',
				'feed_description' 	=> $request->txtFeedShare,
				'user_id' 		=> $this->user_pk,
				'created_by' 	=> $this->user_pk,
				'created_ip'	=> $request->ip(),
				'updated_by'	=> $this->user_pk,
				'updated_ip'	=> $request->ip()
			]);

    		// Feed Share
    		$feedInfo = \App\Models\NetworkFeedShare::create([
    			'feed_id' 		=> $feedInfo->id,
				'share_feed_id' => $request->feedId
    		]);

    		return response()->json(['success' => true, 'statusMsg'=> 'Feed Shared Successfully']);

    	endif;
    }

    /**
     * Display a listing of the Feeds.
     * @author Shriram
     */
	public function index(Request $request){

		try{

			$roleId = Auth::User()->lkp_role_id;
			$userid = Auth::User()->id;

			if($request->has('q')):
				$reqCond = array(
					'search_keyword' => $request->fs,
					'feed_type' => $request->ft,
					'feed_from_date' => $request->fdd,
					'feed_to_date' => $request->fdt,
					'role' => $roleId
				);
			else:
				$reqCond = array('role' => $roleId);
			endif;	

			if(isset($_REQUEST['ajax'])){
				
				$page = isset($_REQUEST['page']) ? $_REQUEST['page']-1 : 1;
                $skip = $page * AJAX_LOAD_LIMIT;
                $networkFeeds = \App\Models\NetworkFeeds::get_user_feeds($userid, $skip, true, $reqCond);
				$returnHTML = view('networks.news-feed_ajax')->with([
					'networkFeeds' =>  $networkFeeds])
					->with('CComponent', new CommonComponent)
					->render();
				return response()->json(array('success' => true, 'html'=>$returnHTML));
			}

			$searchText='';
			
			// Getting Buyer/Seller profile details
			$userInfo = CommonComponent::getUserDetails($userid);
			//NetworkComponent::getListFeeds($roleId);
			$getLogoPartners = CommonComponent::getLogoPartners($userid);			
			$getLogoFollowers = CommonComponent::getLogoFollowers($userid,$searchText);
			$getLogoCommunitygroups = CommonComponent::getAllCommunityGroups($userid);
			
			$servicesOfferlist       = NetworkComponent::getSellerServices($userid);
			$servicesRequriedlist    = NetworkComponent::getBuyerServices($userid);			
			$additionalInfo = new \stdClass();
			//Checking Role is Seller
			if($roleId == SELLER):				
				Log::info('Seller has viewed Order List page:' . $this->user_pk, array('c' => '1'));
				
				// Checking & Getting Buyer Details based on is_business 
				if($userInfo->is_business)
					// Business Account
					$additionalInfo = CommonComponent::getSellerBusinessDetails($userid);
				else
					// Individual Account 
					$additionalInfo = CommonComponent::getSellerIndividualDetails($userid);

			else:
				
				Log::info('Buyer has viewed Order List page:' . $this->user_pk, array('c' => '1'));

				// Checking & Getting Buyer Details based on is_business 
				if($userInfo->is_business)
					// Business Account
					$additionalInfo = CommonComponent::getBuyerBusinessDetails($userid);
				else
					// Individual Account
					$additionalInfo = CommonComponent::getBuyerIndividualDetails($userid);

			endif;
			
			// Checking Filter form Queries set or not
			$networkFeeds = \App\Models\NetworkFeeds::get_user_feeds($userid,0,true,$reqCond);
			
			return view('networks.index',[
				'profiledetails' => $userInfo,
				'userDetails' => $additionalInfo,
				'totalcount' => count(\App\Models\NetworkFeeds::get_user_feeds($userid,0,false, $reqCond)),
				'networkFeeds' => $networkFeeds,	
				'getLogoCommunitygroups' => $getLogoCommunitygroups,
				'getLogoPartners' => $getLogoPartners,
				'getLogoFollowers' => $getLogoFollowers,
				'servicesOfferlists' => $servicesOfferlist,
				'servicesRequriedlists' => $servicesRequriedlist,
				'partnerCount'	=> NetworkComponent::get_user_network_count($userid, 'partner'),
				'followerCount'	=> NetworkComponent::get_user_network_count($userid, 'follower'),
				'recommendationCount' => NetworkComponent::get_user_network_count($userid, 'recommendation')
			])->with('CComponent', new CommonComponent);
				
		} catch (Exception $e) {
		
		}
		
	}   
	/**
	 * Display Profile details.
	 *
	 */
	
	public function networkProfile($id){
	
		$roleId = Auth::User()->lkp_role_id;
		$profiledetails = CommonComponent::getUserDetails($id);

		if($roleId == SELLER){
			Log::info('Seller has viewed Profile:' . $this->user_pk, array('c' => '1'));

			// Checking & Getting Buyer Details based on is_business 
			if($profiledetails->is_business)
				// Business Account
				$additionalInfo = CommonComponent::getSellerBusinessDetails($this->user_pk);
			else
				// Individual Account 
				$additionalInfo = CommonComponent::getSellerIndividualDetails($this->user_pk);

		}
		else{
	
			Log::info('Buyer has viewed Profile' . $this->user_pk, array('c' => '1'));

			// Checking & Getting Buyer Details based on is_business 
			if($profiledetails->is_business)
				// Business Account
				$additionalInfo = CommonComponent::getBuyerBusinessDetails($this->user_pk);
			else
				// Individual Account
				$additionalInfo = CommonComponent::getBuyerIndividualDetails($this->user_pk);

		}
	
		try{			
			CommonComponent::getProfileCount($id);
			$follower       = CommonComponent::getfollowDetails($id);
			$partners       = CommonComponent::getPartnerStatus($id);
			
			$getProfileJobCount  = CommonComponent::getJobs($id, 'count');
			$profileJobs  = CommonComponent::getJobs($id, 'last5');

			$recomendations = CommonComponent::getRecomendations($id);
			$recomendationlist = CommonComponent::getRecomendationDetails($id);
			$recomendationpersonallist = CommonComponent::getPersonalRecomendationDetails($id);
			$partnersrequestlist       = NetworkComponent::getPartnerRequestList(Auth::User()->id);

			$sellerservices       = NetworkComponent::getSellerServices($id);
			$buyerRequriedlist    = NetworkComponent::getBuyerServices($id);
			
			
		
			return view('networks.network_profile', [
				'id'=>$id,
				'profiledetails' => $profiledetails,
				'userDetails' => $additionalInfo,
				'follower' => $follower, 
				'partners' => $partners,
				'partnersrequestlist'=>$partnersrequestlist,
				'profileJobCount'=> $getProfileJobCount,
				'profileJobs' => $profileJobs,
				'recomendations'=>$recomendations,
				'recomendationpersonallist'=>$recomendationpersonallist,
				'recomendationlist'=>$recomendationlist,
				'sellerservices'=>$sellerservices,
				'buyerRequriedlist'=>$buyerRequriedlist,
				'partnerCount'	=> NetworkComponent::get_user_network_count($id, 'partner'),
				'followerCount'	=> NetworkComponent::get_user_network_count($id, 'follower'),
				'recommendationCount' => NetworkComponent::get_user_network_count($id, 'recommendation')
			]);

	
		} catch (Exception $e) {
	
		}
	
	}
	
	/**
	 * Display List of Partners.
	 *
	 */
	
	public function listOfPartners($id){
	
		$roleId = Auth::User()->lkp_role_id;		
		if($roleId == SELLER){
			Log::info('Seller has viewed Order List page:' . $this->user_pk, array('c' => '1'));
		}
		else{	
			Log::info('Buyer has viewed Order List page:' . $this->user_pk, array('c' => '1'));
		}
	
		try{			
			$partnersrequestlist       = NetworkComponent::getPartnerAcceptList($id);
			$personalpartnersrequestlist       = NetworkComponent::getPartnerPersonalAcceptList($id);
			return view('networks.network_partners',['partnersrequestlist' => $partnersrequestlist,'personalpartnersrequestlist' => $personalpartnersrequestlist,'id'=>$id]);
	
		} catch (Exception $e) {
	
		}
	
	}
	
	
	//Get the user Description based on user id
	public static function getDescription(){
		
		$user_id = $_REQUEST['id'];
		try {
	
	
			$role_id = DB::table('users')->where('id', $user_id)
			->select('lkp_role_id')
			->first();
	
			$user_description = DB::table('users');
			$user_description_number = $user_description->where('users.id', $user_id);
	
			if($role_id->lkp_role_id == SELLER){
				$user_description_number->leftJoin('seller_details as c2', function($join)
				{
					$join->on('users.id', '=', 'c2.user_id');
					$join->on(DB::raw('users.is_business'),'=',DB::raw(0));
	
	
				});
				$user_description_number->leftJoin('sellers as cc2', function($join)
				{
					$join->on('users.id', '=', 'cc2.user_id');
					$join->on(DB::raw('users.is_business'),'=',DB::raw(1));
	
	
				});
	
			}else if( $role_id->lkp_role_id == BUYER ){
				$user_description_number->leftJoin('buyer_details as c2', function($join)
				{
					$join->on('users.id', '=', 'c2.user_id');
					$join->on(DB::raw('users.is_business'),'=',DB::raw(0));
	
	
				});
				$user_description_number->leftJoin('buyer_business_details as cc2', function($join)
				{
					$join->on('users.id', '=', 'cc2.user_id');
					$join->on(DB::raw('users.is_business'),'=',DB::raw(1));
	
	
				});
			}
			if($role_id->lkp_role_id == SELLER){
				$user_description_number->select(DB::raw("(case when users.is_business = 1 then cc2.description when users.is_business = 0 then c2.description end) as 'description'"));
			}else if( $role_id->lkp_role_id == BUYER ){
				$user_description_number->select(DB::raw("(case when users.is_business = 1 then cc2.description when users.is_business = 0 then c2.description end) as 'description'"));
					
			}
	
			$data	=	$user_description_number->first();
			if($data->description)
				return $data->description;
			else
				return 0;
	
		} catch ( Exception $exc ) {
	
		}
	}
	
	/**
	 * Display List of getCommunityGroupDescription.
	 *
	 */
	public static function getCommunityGroupDescription(){
	
		try {
			$id = $_REQUEST['id'];
			$getCommunityNewGroupDescription = DB::table('community_groups')
			->where('community_groups.id', $id)
			->select('community_groups.description')
			->first();
			return $getCommunityNewGroupDescription->description;
	
		} catch (Exception $e) {
	
		}
	
	}
	
	/**
	 * Display List of Recomendations.
	 *
	 */
	
	public function listOfRecomendations($id){
	
		$roleId = Auth::User()->lkp_role_id;		
		if($roleId == SELLER){
			Log::info('Seller has viewed Order List page:' . $this->user_pk, array('c' => '1'));
		}
		else{
	
			Log::info('Buyer has viewed Order List page:' . $this->user_pk, array('c' => '1'));
		}
	
		try{
			$recomendationlist = CommonComponent::getRecomendationDetails($id);
			$recomendationgiven = CommonComponent::getRecomendationGiven($id);
			
			return view('networks.network_recomendation',['recomendationlist' => $recomendationlist,'recomendationgiven' => $recomendationgiven,'id'=>$id]);
	
		} catch (Exception $e) {
	
		}
	
	}
	/**
	 * Display List of Jobs.
	 *
	 */
	
	
	
	public function listOfJobs($id){
	
		$roleId = Auth::User()->lkp_role_id;		
		if($roleId == SELLER){
			Log::info('Seller has viewed Jobs List page:' . $this->user_pk, array('c' => '1'));
		}
		else{	
			Log::info('Buyer has viewed Jobs List page:' . $this->user_pk, array('c' => '1'));
		}	
		try{
			$getjobs  = CommonComponent::getJobs($id);
			return view('networks.network_jobs',['getjobs' => $getjobs,'id'=>$id]);
	
		} catch (Exception $e) {
	
		}	
	}
	/**
	 * Display List of Articles.
	 *
	 */

	public function listOfArticles($id){
	
		$roleId = Auth::User()->lkp_role_id;		
		if($roleId == SELLER){
			Log::info('Seller has viewed Articles List page:' . $this->user_pk, array('c' => '1'));
		}
		else{	
			Log::info('Buyer has viewed Articles List page:' . $this->user_pk, array('c' => '1'));
		}
	
		try{
			$getarticles = CommonComponent::getArticles($id);			
			return view('networks.network_articles',['getarticles' => $getarticles,'id'=>$id]);
	
		} catch (Exception $e) {
	
		}
	
	}
	
	/**
	 * Display List of Follow.
	 *
	 */
	
	public function listOffollowing($id){
	
		$roleId = Auth::User()->lkp_role_id;		
		if($roleId == SELLER){
			Log::info('Seller has viewed Articles List page:' . $this->user_pk, array('c' => '1'));
		}
		else{	
			Log::info('Buyer has viewed Articles List page:' . $this->user_pk, array('c' => '1'));
		}	
		try{
                        $getfollow       = CommonComponent::getFollowList($id);				
			return view('networks.network_following',['getfollow' => $getfollow,'id'=>$id]);
	
		} catch (Exception $e) {
	
		}
	
	}
	
	
	/**
	 * Acrion to follow profile.
	 *
	 */
	
	public function followProfile(){
	
		$roleId = Auth::User()->lkp_role_id;
		if($roleId == SELLER){
			Log::info('Seller has followed the network profile:' . $this->user_pk, array('c' => '1'));
		}
		else{
	
			Log::info('Buyer has followed the network profile:' . $this->user_pk, array('c' => '1'));
		}
		try{
			$follow = CommonComponent::followNetworkProfileupdation($_REQUEST['userid'],$_REQUEST['status']);
			echo $follow;
			} catch ( Exception $e ) { 
				echo 'Caught exception: ', $e->getMessage (), "\n";
			}
		
	
	}
	/**
	 * Acrion to partner Request.
	 *
	 */
	
	public function partnerRequestSend(){
	
		$roleId = Auth::User()->lkp_role_id;
		if($roleId == SELLER){
			Log::info('Seller has sent Partner Request:' . $this->user_pk, array('c' => '1'));
		}
		else{
	
			Log::info('Buyer has sent Partner Request:' . $this->user_pk, array('c' => '1'));
		}
		try{
			CommonComponent::partnerRequestsending($_REQUEST['userid']);
			
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	

	}
	/**
	 * Acrion to partner Request.
	 *
	 */
	
	public function partnerRequestAcceptnece(){
	
		$roleId = Auth::User()->lkp_role_id;
		if($roleId == SELLER){
			Log::info('Seller has accept partner request:' . $this->user_pk, array('c' => '1'));
		}
		else{
	
			Log::info('Buyer has accept partner request:' . $this->user_pk, array('c' => '1'));
		}
		try{
			
			NetworkComponent::partnerRequestAcceptence($_REQUEST['userid']);
			
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	
	
	}
	/**
	 * Acrion to partner Recomendation Approval.
	 *
	 */
	
	public function partnerRecomendation(){
	
		$roleId = Auth::User()->lkp_role_id;
		if($roleId == SELLER){
			Log::info('Seller has accept partner request:' . $this->user_pk, array('c' => '1'));
		}
		else{
	
			Log::info('Buyer has accept partner request:' . $this->user_pk, array('c' => '1'));
		}
		try{
			NetworkComponent::partnerRecomendationApproval($_REQUEST['userid'],$_REQUEST['status'],$_REQUEST['type']);
			
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	
	
	}
	/**
	 * Acrion to add Recomendations.
	 *
	 */
	
	public function addReccomendation(Request $request){
	
		$roleId = Auth::User()->lkp_role_id;
		if($roleId == SELLER){
			Log::info('Seller has accept partner request:' . $this->user_pk, array('c' => '1'));
		}
		else{
	
			Log::info('Buyer has accept partner request:' . $this->user_pk, array('c' => '1'));
		}
		try{
			
			$v = Validator::make($request->all(),
					['body' => 'required'],
					['body.required' => 'Please enter message text',
					]
			);
			if($v->fails()):
			return response()->json([ 'success' => false, 'errors' => $v->messages()]);
			else :
			NetworkComponent::addRecomendationtoProfile($_REQUEST['userid'],$_REQUEST['body']);
			return response()->json([ 'success' => true]);
			endif;
			
			
			
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	}
	
	/**
	 * Acrion to add Recomendations.
	 *
	 */
	
	public function addProfileMessage(Request $request){
	
		$roleId = Auth::User()->lkp_role_id;
		if($roleId == SELLER){
			Log::info('Seller has accept partner request:' . $this->user_pk, array('c' => '1'));
		}
		else{
	
			Log::info('Buyer has accept partner request:' . $this->user_pk, array('c' => '1'));
		}
		try{
			$v = Validator::make($request->all(),
					['message_subject' => 'required', 'message_body' => 'required'],
					['message_subject.required' => 'Please enter Subject','message_body.required' => 'Please enter message text',
					]
			);
			if($v->fails()):
			return response()->json([ 'success' => false, 'errors' => $v->messages()]);
			else :
			NetworkComponent::addMessagesToProfile($_REQUEST['userid'],$_REQUEST['message_subject'],$_REQUEST['message_body']);
			return response()->json([ 'success' => true]);
			endif;
				
			
			

		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	}
	
	//sharing profile
	
	public function sharingProfile(Request $request){
	
		$roleId = Auth::User()->lkp_role_id;
		if($roleId == SELLER){
			Log::info('Seller has accept partner request:' . $this->user_pk, array('c' => '1'));
		}
		else{
	
			Log::info('Buyer has accept partner request:' . $this->user_pk, array('c' => '1'));
		}
		try{
			$v = Validator::make($request->all(),
					['shareids' => 'required', 'sharesubject' => 'required','sharebody' => 'required'], 
					['shareids.required' => 'Please enter Username','sharesubject.required' => 'Please enter Subject','sharebody.required' => 'Please enter Message',
					]
			);
			if($v->fails()):
			return response()->json([ 'success' => false, 'errors' => $v->messages()]);
			else :
			NetworkComponent::userProfileShare($_REQUEST['shareids'],$_REQUEST['sharesubject'],$_REQUEST['sharebody'],$_REQUEST['user_link']);
				return response()->json([ 'success' => true]);
			endif;
			
			
			
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	
	
	}
	
	/***********************************This function for list of users in the network****************************************/
	public function listOfUsers() {
	Log::info('get the buyer list in creating a seller post for post public:'.Auth::id(),array('c'=>'1'));
		try {
				$term = Input::get('search');
			    $profid = $_REQUEST['profid'];		
    			$display_json = array();
				$json_arr = array();
		
				$followerslist = DB::table('users')
    			->leftjoin ('network_followers', 'users.id', '=', 'network_followers.follower_user_id')
		    	->where(['users.is_active' => 1])
		    	->where('network_followers.user_id',Auth::User()->id)
		    	->where('username', 'LIKE', $term.'%')
		    	->whereNotIn('users.id', [$profid])
		    	->orderby('users.id','asc')
		    	->select('users.id','users.username')
		    	->get();
		    	
				
				$partnerslist = DB::table('users')
				->leftjoin ('network_partners', 'users.id', '=', 'network_partners.partner_user_id')
				->where(['users.is_active' => 1])
				->where('network_partners.is_approved',1)
				->where('network_partners.user_id',Auth::User()->id)
				->where('username', 'LIKE', $term.'%')
				->whereNotIn('users.id', [$profid])
				->orderby('users.id','asc')
				->select('users.id','users.username')
				->get();
				
				$finalarray = array_unique(array_merge($followerslist,$partnerslist), SORT_REGULAR);
			
				if(count($finalarray)>0){
						for($i=0; $i<count($finalarray); $i++ ){
								$json_arr["value"] = $finalarray[$i]->id;
								//if (!empty($buyer_lisr_for_seller[$i]->principal_place)){
									$json_arr["text"] = $finalarray[$i]->username." ".$finalarray[$i]->id;
								//}else{
									//$json_arr["text"] = $followerslist[$i]->username." ".$followerslist[$i]->id;	
								//}
								array_push($display_json, $json_arr);
								}
				}else{
   	
					$json_arr["value"] = "";
					$json_arr["text"] = "No results Found";
					array_push($display_json, $json_arr);
				}
				return $display_json;
		}
	 	catch (Exception $e) {
			
		}
	
	}
	
	
	/**
	 * Delete Comment
	 */
	
	public function cancelPostComment(){
	
		try {
			
			DB::table('network_feed_comments')
	        			->leftJoin('network_feeds', 'network_feeds.id', '=', 'network_feed_comments.feed_id')
                                        ->where('network_feed_comments.id', $_REQUEST['commentIds'])
                                        ->where('network_feed_comments.created_by', $_REQUEST['id'])
                                        ->delete();
			
		} catch ( Exception $ex ) {
			
		}
	}
   
	/**
	 * Search follwers function
	 */
	public function searchFollwers(){
	
		try {			
		 
		 $userid = Auth::User()->id;
		 $searchText='';
         
			$searchText=$_POST['searchtext'];
			
			$getLogoFollowers = CommonComponent::getLogoFollowers($userid,$searchText);
		
			$returnHTML = view('networks.searchfollwers',['getLogoFollowers' => $getLogoFollowers])->render();
			return response()->json(array('success' => true,'html'=>$returnHTML));
				
		} catch ( Exception $ex ) {
				
		}
	}
	
	/**
	 * Search partners function
	 */
	public function searchPartners(){
	
		try {			
			$userid = Auth::User()->id;
			$searchText='';			 
			$searchText=$_POST['searchtext'];				
			$getLogoPartners = CommonComponent::getLogoPartners($userid,$searchText);	
			$returnHTML = view('networks.searchpartners',['getLogoPartners' => $getLogoPartners])->render();
			return response()->json(array('success' => true,'html'=>$returnHTML));
	
		} catch ( Exception $ex ) {
	
		}
	}
	
	/**
	 * Search groups function
	 */
	public function searchGroups(){
	
		try {			
			$userid = Auth::User()->id;
			$searchText='';	
			$searchText=$_POST['searchtext'];	
			$getLogoCommunitygroups = CommonComponent::getAllCommunityGroups($userid,$searchText);	
			$returnHTML = view('networks.searchgroups',['getLogoCommunitygroups' => $getLogoCommunitygroups])->render();
			return response()->json(array('success' => true,'html'=>$returnHTML));
	
		} catch ( Exception $ex ) {
	
		}
	}
	


}
