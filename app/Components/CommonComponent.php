<?php namespace App\Components;

use Auth;

use DB;
use Input;
use Session;

use App\Models\PtlSellerPostItemView;
use App\Models\SellerPostItemView;
use Illuminate\Support\Facades\Mail;

use Zofe\Rapyd\Facades\DataGrid;
use App\Models\FtlSearchTerm;
use App\Models\IctSearchTerm;
use App\Models\User;
use App\Models\UserMessage;
use App\Models\OceanSellerPostItemView;
use App\Models\RailSellerPostItemView;
use App\Models\AirintSellerPostItemView;
use App\Models\AirdomSellerPostItemView;
use App\Models\CourierBuyerQuoteItemView;

use App\Models\BuyerQuoteItemView;
use App\Models\PtlBuyerQuoteItemView;
use App\Models\RailBuyerQuoteItemView;
use App\Models\AirdomBuyerQuoteItemView;
use App\Models\AirintBuyerQuoteItemView;
use App\Models\OceanBuyerQuoteItemView;
use App\Models\RelocationBuyerPostView;
use App\Models\RelocationpetBuyerPostView;
use App\Models\RelocationSellerPostView;
use App\Models\RelocationpetSellerPostView;
use App\Models\RelocationofficeSellerPostView;
use App\Models\TermBuyerQuoteItemView;
use App\Models\TruckhaulBuyerQuoteItemView;
use App\Models\CourierSellerPostItemView;
use App\Models\TruckhaulSellerPostItemView;
use App\Models\TruckleaseSellerPostItemView;
use App\Models\RelocationintSellerPostView;
use App\Models\RelocationgmSellerPostView;

use App\Components\Search\SellerSearchComponent;
use App\Components\Matching\SellerMatchingComponent;
use App\Models\NetworkFollowers;
use App\Models\NetworkPartners;
use App\Models\UserProfileView;
use App\Models\NetworkFeeds;
use DateTime;
use Log;
use App\Models\LogUserSms;
use App\Components\Term\TermBuyerComponent;

class CommonComponent{

	/** Number format **/
	public static function number_format($req_number, $decimalsBool = true){
		if(!empty($req_number)){
			if (function_exists('money_format')) {
				setlocale(LC_MONETARY, 'en_IN');

				if ($decimalsBool):
					return money_format('%!i', $req_number) . "/-";;
				else:
					return money_format('%!i', round($req_number, 2));
				endif;
			}
			return $req_number;
		}
		return '0.00';
	}

	/** Retrieval of page widgets from the database **/
	public static function getPageWidgets($page_name, $role_id, $serviceId)
	{
		try
		{
			$inst_id=Auth::user()->txn_institution_id;
			$widgets = DB::table('rolepagexwidgets')
				->join('rolexpages', 'rolepagexwidgets.role_page_id', '=', 'rolexpages.id')
				->join('lkp_pages', 'rolexpages.lkp_page_id', '=', 'lkp_pages.id')
				->join('lkp_roles', 'rolexpages.lkp_role_id', '=', 'lkp_roles.id')
				->join('lkp_widgets', 'rolepagexwidgets.widget_id', '=', 'lkp_widgets.id')
				->where(['rolexpages.lkp_role_id' => $role_id, 'page_name' => $page_name, 'rolepagexwidgets.is_active' => true,
					'help_contents.lkp_role_id'=>$role_id,'rolexpages'=>$serviceId])
				->orderby('rolepagexwidgets.order','asc')
				->select('lkp_widgets.id','lkp_widgets.widget_name','rolepagexwidgets.id as widget_id','rolepagexwidgets.widget_id as org_widget_id','lkp_pages.id as page_id','help_contents.title as help')
				->get();
			return $widgets;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Save data into activity tables ****/
	public static function activityLog($event,$event_description,$ref_id,$ref_url,$action_url)
	{
		//try
		//{
		$createdAt = date('Y-m-d H:i:s');
		if($ref_id != 0){
			$createdBy = Auth::User()->id;
		} else {
			$createdBy = null;
		}
		$createdIp = $_SERVER['REMOTE_ADDR'];

		$id = DB::table('log_activity')->insertGetId(
			['activity_event' => $event, 'event_description' => $event_description, 'ref_id' => $createdBy , 'referrer_url' => $ref_url, 'action_page_url' => $action_url, 'created_at' => $createdAt, 'created_by' => $createdBy, 'created_ip' => $createdIp, 'updated_at' => $createdAt, 'updated_by' => $createdBy, 'updated_ip' => $createdIp]
		);

		// }
		// catch(\Exception $e)
		// {
		//print_r($e);
		//return $e->message;
		// }
	}

	/** Save data into audit log tables **/
	public static function auditLog($id, $table){
		$qry = "insert into log_".$table." (select * from ".$table." where id ='$id')";
		DB::insert($qry);
	}

	/** Date conversion to MM/DD/YY **/

	public static function convertDate($org_date){

		//$originalDate = $org_date;
		$originalDate = str_replace('/','-',$org_date);
		$newDate = date("d-m-Y", strtotime($originalDate));

		return $newDate;

	}


	/** Convert YYYY-MM-DD to MM/DD/YY **/
	public static function convertMysqlDate($org_date){
		try{
			$originalDate = str_replace ( '/', '-', $org_date );

			$newDate = date("d/m/Y", strtotime($originalDate));
			return $newDate;
		}catch (Exception $e) {
			//TODO:: Log the error somewhere
		}
	}

	/** Date conversion to MM/DD/YY **/
	public static function convertDateDisplay($org_date){
		try{
			if($org_date != "0000-00-00" && $org_date != ""){
				$originalDate = $org_date;
				$originalDate = str_replace ( '/', '-', $originalDate );
				$newDate = date("d/m/Y", strtotime($originalDate));
				return $newDate;
			}
			return "N/A";

		}catch (Exception $e) {
			//TODO:: Log the error somewhere
		}
	}

	/** Date conversion to MM/DD/YY **/
	public static function checkAndGetDate($date){
		try{
			if($date == '0000-00-00') {
				return '';
			}
			$dateOfTraining = CommonComponent::convertDateDisplay($date);
			return $dateOfTraining;
		}catch (Exception $e) {
			//TODO:: Log the error somewhere
		}
	}

	/** Date conversion to MM/DD/YY **/
	public static function convertDateTime($org_date){

		$originalDate = $org_date;
		$newDate = date('d-m-Y H:i:s', strtotime($originalDate));

		return $newDate;

	}

	/**
	 * Get all roles-- To display in left navigation
	 * @return type
	 */
	public static function getAllRoles(){
		try{
			$json_arr = array();
			$roles = DB::table('lkp_roles')
				->where(['lkp_roles.is_active' => 1])
				->orderby('lkp_roles.id','asc')
				->select('lkp_roles.id','lkp_roles.role_name')
				->get();

			/*converting Data to Json format*/
			$json_arr[0]="Select Role";
			for($i=0;$i<count($roles);$i++) {
				$json_arr[$roles[$i]->id] = $roles[$i]->role_name;
			}
			return $json_arr;
		} catch (Exception $ex) {

		}
	}

	/*
	* Send Email function
	* Sends emails as per the configuration set based on the parameters
	*/
	public static function send_email($event_id = null,$info=null,$remdays=null,$pathToFile=null,$attachmentFlag=false)
	{
		$email_template = DB::table('lkp_email_templates')
			->where(['lkp_email_event_id' => $event_id ])
			->select('lkp_email_templates.*')
			->get();
		$info[0]->subject=$email_template[0]->subject;
		$body = $email_template[0]->body;

		
		if(is_array($info)){
			foreach ($info[0] as $key=>$value){
				$body = str_replace("{!! $key !!}", $value, $body );
			}
		}

		$site_url =  url();

		//replace site url for image paths.
		$body = str_replace("{!! site_url !!}", $site_url, $body );
		$body = str_replace("{!! remainderdays !!}", $remdays, $body );

		$serviceId = Session::get('service_id');
		switch ($serviceId) {

			case ROAD_PTL :
				$body = str_replace("FTL", 'PTL', $body );
				$info[0]->subject = str_replace("FTL", 'PTL', $info[0]->subject );
				break;
			case RAIL :
				$body = str_replace("FTL", 'RAIL', $body );
				$info[0]->subject = str_replace("FTL", 'RAIL', $info[0]->subject );
				break;
			case ROAD_TRUCK_HAUL :
				$body = str_replace("FTL", 'TRUCK HAUL', $body );
				$info[0]->subject = str_replace("FTL", 'TRUCK HAUL', $info[0]->subject );
				break;
			case ROAD_TRUCK_LEASE :
				$body = str_replace("FTL", 'TRUCK LEASE', $body );
				$info[0]->subject = str_replace("FTL", 'TRUCK LEASE', $info[0]->subject );
				break;
			case ROAD_INTRACITY :
				$body = str_replace("FTL", 'INTRA', $body );
				$info[0]->subject = str_replace("FTL", 'INTRA', $info[0]->subject );
				break;
			case AIR_DOMESTIC :
				$body = str_replace("FTL", 'AIR DOMESTIC', $body );
				$info[0]->subject = str_replace("FTL", 'AIR DOMESTIC', $info[0]->subject );
				break;
			case AIR_INTERNATIONAL :
				$body = $body;
				$info[0]->subject = str_replace("AI", 'AIR INTERNATIONAL', $info[0]->subject );
				break;
			case OCEAN :
				$body = str_replace("OCEAN", 'OCEAN', $body );
				$info[0]->subject = str_replace("FTL", 'OCEAN', $info[0]->subject );
				break;
            case COURIER :
				$body = str_replace("FTL", 'COURIER', $body );
				$info[0]->subject = str_replace("FTL", 'COURIER', $info[0]->subject );
				break; 
            case RELOCATION_DOMESTIC :
				$body = str_replace("FTL", 'RD', $body );
				$info[0]->subject = str_replace("FTL", 'RD', $info[0]->subject );
				break; 
            case RELOCATION_GLOBAL_MOBILITY :
				$body = str_replace("FTL", 'RELOCATIONGM', $body );
				$info[0]->subject = str_replace("FTL", 'RELOCATIONGM', $info[0]->subject );
				break; 
			default :
				$body = str_replace("FTL", 'FTL', $body );
				$info[0]->subject = str_replace("FTL", 'FTL', $info[0]->subject );
				break;
		}
		//echo $info[0]->email;

		//send mail to invitee to login into the system
		if(isset($info[0]->email) && !empty($info[0]->email)){
			Mail::raw($body, function($message) use ($info,$pathToFile,$attachmentFlag)
			{
				$message->from(FROM_EMAIL, APPL_TITLE);

				$message->to($info[0]->email)->subject($info[0]->subject);
				if($attachmentFlag && !empty($pathToFile)){
					$message->attach($pathToFile);
				}

			});
		}

		
	}

	/**
	 * For removing special characters from the string
	 * @param type $string
	 * @return type
	 */
	public static function removeSpecialCharacter($string) {
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}

	/** Date conversion to Y-m-d format **/
	public static function convertDateForDatabase($orgDate){
		//$date = mysql_real_escape_string($_POST['intake_date']);
		if(!empty($orgDate)){
			$newDateFormat = date('Y-m-d', strtotime(str_replace('/', '-', $orgDate)));
		} else {
			$newDateFormat = '';
		}
		
		return $newDateFormat;
	}

	/** Date conversion to Y-m-d H:i:s format **/
	public static function convertDateTimeForDatabase($orgDate,$orgTime){
		//$date = mysql_real_escape_string($_POST['intake_date']);
		$orgDate    =    str_replace('/', '-', $orgDate);
		if(!empty($orgDate) && !empty($orgTime)){
			$newDateFormat = date('Y-m-d H:i:s', strtotime($orgDate." ".$orgTime));
		} else {
			$newDateFormat = '';
		}
		return $newDateFormat;
	}

	/*money fomrat*/
	public static function moneyFormat($price,$isfrieght=false){
		if(!empty($price)){
			setlocale(LC_MONETARY, 'en_IN');
			//$formatedPrice = ($isfrieght) ?  money_format('%.4n', $price) : money_format('%!i', $price);
			$formatedPrice = number_format($price,2);
		} else {
			$formatedPrice = '0.00';
		}
		return $formatedPrice;
	}

	/** Retrieval of City id  **/
	public static function getCityId($cityName)
	{
		try
		{

			$getCityId = DB::table('lkp_cities')
				->where('lkp_cities.city_name', '=', $cityName)
				->select('lkp_cities.id')
				->get();
                        if(!empty($getCityId))
			return $getCityId;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	
	/** Retrieval of Permits  **/
	public static function checkPermit($id)
	{
		try
		{
	
			$getStates = DB::table('trucklease_seller_post_item_state_permits')
			->leftjoin('lkp_states','lkp_states.id','=','trucklease_seller_post_item_state_permits.lkp_state_id')
			->where('trucklease_seller_post_item_state_permits.seller_post_item_id', '=', $id)
			->select('lkp_states.state_name')
			->get();
			$getallstates='';
			if(!empty($getStates)){
				if(count($getStates)==36){
					$getallstates = "National";
				}
				else{
					foreach($getStates as $states){
						$getallstates .= ucwords(strtolower($states->state_name)).", ";
						
					}
					
				}
				return $getallstates;
			}
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Retrieval of Permits  **/
	public static function truckLeaseLoadTypes($id)
	{
		try
		{
	
			$getLoads = DB::table('trucklease_seller_post_item_goods')
			->leftjoin('lkp_load_types','lkp_load_types.id','=','trucklease_seller_post_item_goods.lkp_load_type_id')
			->where('trucklease_seller_post_item_goods.seller_post_item_id', '=', $id)
			->select('lkp_load_types.load_type')
			->get();
			$getallloads='';
			if(!empty($getLoads)){
				foreach($getLoads as $loads){
					$getallloads .= $loads->load_type.", ";
				}
				return $getallloads;
			}
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Get username **/
	public static function getUsername($id)
	{
		try
		{
	
			$getuname = DB::table('users')
			->where('users.id', $id)
			->select('users.username')
			->get();
	
			return $getuname[0]->username;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of initial Price  **/
	public static function getQuotePriceForSearch($buyerid,$bqitemid,$sellerid,$price,$table)
	{
		try
		{

			$getSellerPostinitial = DB::table($table)
				->where([$table.'.buyer_id' => $buyerid])
				->where([$table.'.buyer_quote_item_id' => $bqitemid])
				->where($table.'.seller_post_item_id','!=',0)
				->where([$table.'.seller_id' => $sellerid])
				->select("$table.*")
				->get();
			if(count($getSellerPostinitial)==0){
				$getSellerPostinitial = DB::table($table)
					->where([$table.'.buyer_id' => $buyerid])
					->where([$table.'.buyer_quote_item_id' => $bqitemid])
					->where([$table.'.seller_id' => $sellerid])
					->select("$table.*")
					->get();

				if(count($getSellerPostinitial)==0){
					$getSellerPostinitial[0]->$price='';
					$getSellerPostinitial[0]->seller_acceptence=0;
				}
			}

			return $getSellerPostinitial;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}


	/** Retrieval of initial Price PTL **/
	public static function getPTLQuotePriceForSearch($buyerid,$bqitemid,$sellerid,$price,$table)
	{
		try
		{
			//echo $buyerid." ".$bqitemid." ".$sellerid." ".$price." ".$table." ";
			$getSellerPostinitial = DB::table($table)
				->where([$table.'.buyer_id' => $buyerid])
				->where([$table.'.buyer_quote_id' => $bqitemid])
				->where($table.'.seller_post_item_id','!=',0)
				->where([$table.'.seller_id' => $sellerid])
				->select("$table.*")
				->get();
			//echo count($getSellerPostinitial);
			if(count($getSellerPostinitial)==0){
				$getSellerPostinitial = DB::table($table)
					->where([$table.'.buyer_id' => $buyerid])
					->where([$table.'.buyer_quote_id' => $bqitemid])
					->where([$table.'.seller_id' => $sellerid])
					->select("$table.*")
					->get();

				if(count($getSellerPostinitial)==0){
					$getSellerPostinitial[0]->$price='';
					$getSellerPostinitial[0]->seller_acceptence=0;
				}
			}
			//exit;
			return $getSellerPostinitial;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of seller / Buyer Business Name to display in the left navigation  **/
	public static function getBuyerName($userId, $userRole='')
	{

		try
		{
			$getUserBusinessName = DB::table('users')
				->where('id', $userId)
				->select('username as business_name')
				->first();

			if(count($getUserBusinessName)== 0){
				$getUserBusinessName->business_name='';
			}

			return $getUserBusinessName->business_name;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	
	/** Retrieval User Details **/
	public static function getUserDetails($id)
	{
	
		try
		{
			$getUserrole = DB::table('users')
			->where('users.id', $id)
			->select('users.primary_role_id','users.is_business')
			->first();
			
			if($getUserrole->primary_role_id==1){
				if($getUserrole->is_business == 1){
					$buyerTable = 'buyer_business_details';
					$contact = 'contact_mobile';
				}else{
					$buyerTable = 'buyer_details';
					$contact = 'mobile';
				}
			}else{
				if($getUserrole->is_business == 1){
					$buyerTable = 'sellers';
					$contact = 'contact_mobile';
				}else{
					$buyerTable = 'seller_details';
					$contact = 'contact_mobile';
				}
			}
			
			$getUserDetails = DB::table('users')
			->leftJoin( $buyerTable , 'users.id', '=', $buyerTable.'.user_id' )
			->where('users.id', $id)
			->select('users.*',$buyerTable.'.description',$buyerTable.'.address',$buyerTable.'.'.$contact .' as phone')
			->first();
			
			if(count($getUserDetails)==0)
				return $getUserDetails=array();
			else 
				return $getUserDetails;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	
	//
	
	/** Retrieval Follow Details **/
	public static function getfollowDetails($id)
	{
	
		try
		{
			$getfollowerDetails = DB::table('network_followers')
			->where('follower_user_id', $id)
			->where('user_id', Auth::user()->id)
			->select('network_followers.id')
			->first();
	
			if(count($getfollowerDetails)==0)
				return 0;
			else
				return 1;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	
	public static function getPartnerStatus($id)
	{
	
		try
		{
			$getpartners = DB::table('network_partners')
			->where('partner_user_id', $id)
			->where('user_id', Auth::user()->id)
			->select('network_partners.is_approved')
			->first();
			
			if(count($getpartners)==0){
				$getpartners = DB::table('network_partners')
				->where('user_id', $id)
				->where('partner_user_id', Auth::user()->id)
				->select('network_partners.is_approved')
				->first();
				if(count($getpartners)==0)
					return 0;
				else
					if($getpartners->is_approved==0)
						return 1;
					else
						return 2;
			}
			else
				if($getpartners->is_approved==0)
					return 1;
				else 
					return 2;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	public static function getPartnerApproved($id)
	{
	
		try
		{
			$getpartners = DB::table('network_partners')
			->where('partner_user_id', $id)
			->where('is_approved', 1)
			->select('network_partners.id')
			->get();
				
			if(count($getpartners)==0)
				return 0;
			else
				return count($getpartners);
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	
	public static function getpersonalPartnerApproved($id)
	{
	
		try
		{
			$getpartners = DB::table('network_partners')
			->where('user_id', $id)
			->where('is_approved', 1)
			->select('network_partners.id')
			->get();
	
			if(count($getpartners)==0)
				return 0;
			else
				return count($getpartners);
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	public static function getFollowingList($id)
	{
	
		try
		{
			$getfollowings = DB::table('network_followers')
			->where('user_id', $id)
			->select('network_followers.id')
			->get();
	
			if(count($getfollowings)==0)
				return 0;
			else
				return count($getfollowings);
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	//List of recommendation
	public static function getRecomendationDetails($id)
	{
	
		try
		{
			$getrecomends = DB::table('network_recommendations')
			->where('recommended_to', $id)
			->select('network_recommendations.*')
			->get();
	
			if(count($getrecomends)==0)
				return $getrecomends=array();
			else
				return $getrecomends;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	public static function getPersonalRecomendationDetails($id)
	{
	
		try
		{
			$getrecomends = DB::table('network_recommendations')
			->where('user_id', $id)
			->where('is_approved', 1)
			->select('network_recommendations.*')
			->get();
	
			if(count($getrecomends)==0)
				return $getrecomends=array();
			else
				return $getrecomends;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	//List of recommendation given
	public static function getRecomendationGiven($id)
	{
	
		try
		{
			$getrecomends = DB::table('network_recommendations')
			->where('user_id', $id)
			->select('network_recommendations.*')
			->get();
	
			if(count($getrecomends)==0)
				return $getrecomends=array();
			else
				return $getrecomends;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	
	/**
	* Get count, last five articles and articles list
	* @author Shriram
	*/
	public static function getArticlesList($id, $action = '')
	{
	
		try
		{
			switch($action):

				case 'count':
					$rows = \App\Models\NetworkFeeds::selectRaw("count(*) as totRows")
						->where('user_id', $id)
						->where('feed_type', 'article')
						->first();
					return $rows->totRows;		
					break;	

				case 'last5':
					return \App\Models\NetworkFeeds::where('user_id', $id)
						->where('feed_type', 'article')
						->orderBy('created_at', 'desc')
						->take(5)
						->get();
					break;	
						
				default:
					$getarticle = \App\Models\NetworkFeeds::get_feed_user_id([
						'user_id' => $id,
						'feed_types' => array('article','share')
					]);
					return (count($getarticle)>0)? $getarticle:array();
			endswitch;
		
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	public static function getJobs($id, $action = '')
	{
		try
		{	
			switch($action):

				case 'count':
					$rows = \App\Models\NetworkFeeds::selectRaw("count(*) as totRows")
						->where('user_id', $id)
						->where('feed_type', 'job')
						->first();
					return $rows->totRows;		
					break;	

				case 'last5':
					return \App\Models\NetworkFeeds::where('user_id', $id)
						->where('feed_type', 'job')
						->orderBy('created_at', 'desc')
						->take(5)
						->get();
					break;	
						
				default:
					$getjobs = \App\Models\NetworkFeeds::get_feed_user_id([
						'user_id' => $id,
						'feed_types' => array('job','share')
					], 0, false);
					
					return (count($getjobs)>0)? $getjobs:array();
			endswitch;

		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	//Get Recomendation
	public static function getRecomendations($id)
	{
	
		try
		{
			$getrecomends= DB::table('network_recommendations')
			->where('recommended_to', $id)
			->where('user_id', Auth::user()->id)
			->select('network_recommendations.*')
			->orderBy('network_recommendations.id','desc')
			->get();
	
			if(count($getrecomends)==0)
				return $getjobs=array();
			else
				return $getrecomends;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	//Get Articles List
	public static function getArticles($id)
	{
		
		try
		{	
			$getarticle = \App\Models\NetworkFeeds::get_feed_user_id([
				'user_id' => $id,
				'feed_types' => array('article','share')
			]);

			if(count($getarticle)==0)
				return array();
			else
				return $getarticle;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	
	//Get Follow List
	public static function getFollowList($id)
	{
	
		try
		{
			$getfollow= DB::table('network_followers')
			->leftjoin('users','users.id','=','network_followers.follower_user_id')
			->where('user_id', $id)
			->select('network_followers.*','users.username','users.id as userid','users.lkp_role_id','users.user_pic')
			->get();
	
			if(count($getfollow)==0)
				return $getfollow=array();
			else
				return $getfollow;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}
	
	public static function getJobCommnets($id)
	{
	
		try
		{
			$getcomments= DB::table('network_feed_comments')
			->leftjoin('users','users.id','=','network_feed_comments.user_id')
			->where('network_feed_comments.feed_id', $id)
			->select('network_feed_comments.*','users.username','users.id as userid','users.user_pic','users.lkp_role_id')
			->orderBy('network_feed_comments.created_at','asc')
			->get();
	
			if(count($getcomments)==0)
				return $getcomments=array();
			else
				return $getcomments;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}

	/** Retrieval of seller / Buyer Business Name to display in the left navigation  **/
	public static function getSellerNameImage($userId, $userRole)
	{
		try
		{
			$getUserBusinessName = DB::table('users')
				->where('id', $userId)
				->select('username as business_name','logo as image_logo')
				->first();

			if(count($getUserBusinessName)== 0){
				$getUserBusinessName->business_name='';
			}

			if(isset($getUserBusinessName->image_logo) && $getUserBusinessName->image_logo != ''){

				return $getUserBusinessName->image_logo.","."1";
			}else{
				return $getUserBusinessName->business_name.","."2";
			}
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	}

	public static function getPincodesSeller($sellerId)
	{
		$pincodes_sellers = DB::table('ptl_pincodexsectors');
		$pincodes_sellers->where('seller_id', '=', $sellerId);
		$pincodes_sellers->where('lkp_service_id', '=', Session::get('service_id'));
		$pincodes_sellers = $pincodes_sellers->lists('ptl_pincode_id');
		return $pincodes_sellers;
	}
	public static function getItemsBuyer($buyerId) {
		$term_buyer_quote_id = DB::table ( 'term_buyer_quote_items' );
		$term_buyer_quote_id->where ( 'term_buyer_quote_id', '=', $buyerId );
		$term_buyer_quote = $term_buyer_quote_id->lists ( 'id' );
		// echo "<pre>";print_r($term_buyer_quote);exit;
		return $term_buyer_quote;
	}
	public static function getQouteItemsBuyer($QouteItemsId) {
		$term_buyer_quote_id = DB::table ( 'term_buyer_quote_items' );
		$term_buyer_quote_id->where ( 'term_buyer_quote_id', '=', $QouteItemsId );
		$term_buyer_quote = $term_buyer_quote_id->lists ( 'id' );
		// echo "<pre>";print_r($term_buyer_quote);exit;
		return $term_buyer_quote;
	}
	public static function getBuyerId($buyerItemId) {
		$term_buyer_quote_id = DB::table ( 'term_buyer_quotes' );
		$term_buyer_quote_id->where ( 'id', '=', $buyerItemId );
		$term_buyer_id = $term_buyer_quote_id->lists ( 'buyer_id' );
		// echo "<pre>";print_r($term_buyer_quote);exit;
		return $term_buyer_id;
	}

	/** Retrieval of Post statuses  **/
	public static function getPostStatusesService($serviceCompareId)
	{
		//try
		//{
		//echo $serviceCompareId = Session::get('service_id');
		$roleCompareId = Auth::User()->lkp_role_id;

		$postStatuses = DB::table('lkp_post_statuses');
		if($serviceCompareId == ROAD_FTL){
			$postStatuses->where('is_ftl', '=', '1');
		}elseif($serviceCompareId == ROAD_PTL){
			$postStatuses->where('is_ltl', '=', '1');
		}elseif($serviceCompareId == ROAD_INTRACITY){
			$postStatuses->where('is_intracity', '=', '1');
		}elseif($serviceCompareId == ROAD_TRUCK_HAUL){
			$postStatuses->where('is_truckhaul', '=', '1');
		}elseif($serviceCompareId == ROAD_TRUCK_LEASE){
			$postStatuses->where('is_trucklease', '=', '1');
		}elseif($serviceCompareId == RAIL){
			$postStatuses->where('is_rail', '=', '1');
		}elseif($serviceCompareId == AIR_DOMESTIC){
			$postStatuses->where('is_airdomestic', '=', '1');
		}elseif($serviceCompareId == AIR_INTERNATIONAL){
			$postStatuses->where('is_airinternational', '=', '1');
		}elseif($serviceCompareId == COURIER){
			$postStatuses->where('is_courier', '=', '1');
		}elseif($serviceCompareId == OCEAN){
			$postStatuses->where('is_ocean', '=', '1');
		}elseif($serviceCompareId == HANDLING_SERVICES){
			$postStatuses->where('is_handling_services', '=', '1');
		}elseif($serviceCompareId == EQUIPMENT_LEASE){
			$postStatuses->where('is_equipment_lease', '=', '1');
		}elseif($serviceCompareId == PACKAGING_SERVICES){
			$postStatuses->where('is_packaging_services', '=', '1');
		}elseif($serviceCompareId == WAREHOUSE){
			$postStatuses->where('is_warehouse', '=', '1');
		}elseif($serviceCompareId == THIRD_PARTY_LOGISTICS){
			$postStatuses->where('is_thirdparty_logistiks', '=', '1');
		}elseif($serviceCompareId == RELOCATION_DOMESTIC){
			$postStatuses->where('is_relocation_domestic', '=', '1');
		}elseif($serviceCompareId == RELOCATION_INTERNATIONAL){
			$postStatuses->where('is_relocation_international', '=', '1');
		}elseif($serviceCompareId == RELOCATION_PET_MOVE){
			$postStatuses->where('is_relocation_pet_move', '=', '1');
		}elseif($serviceCompareId == RELOCATION_OFFICE_MOVE){
			$postStatuses->where('is_relocation_office_move', '=', '1');
		}elseif($serviceCompareId == RELOCATION_GLOBAL_MOBILITY){
			$postStatuses->where('is_relocation_global', '=', '1');
		}elseif($serviceCompareId == RELOCATION_GLOBAL_MOVE){
			$postStatuses->where('is_relocation_office_move', '=', '1');
		}elseif($serviceCompareId == MULTIMODEL){
			$postStatuses->where('is_ftl', '=', '1');
		}
		$postStatusesArr = $postStatuses->lists('id');
		return $postStatusesArr;
		/*}
		catch(\Exception $e)
		{
			//return $e->message;
		}*/
	}

	/** Retrieval of seller / Buyer Messages count to display in the Top navigation  **/
	public static function getBuyerSellerMessageCount()
	{
	try
		{
// 		$services = array(0);
		$services = Session::get('service_id');
		$messages_count = DB::table('user_messages')
		->where('lkp_service_id','=',$services)
		->where('recepient_id', Auth::user()->id)
		->where('is_read', 0)->count();
		
		return $messages_count;
		
		}
		catch(Exception $e)
		{
		}
	}
	
	/** Retrieval of seller / Buyer Messages count to display in the Top navigation with out service  **/
	public static function getBuyerSellerMessageCountDefualt()
	{
		try
		{
		$messages_count = DB::table('user_messages')
		->where('recepient_id', Auth::user()->id)
		->whereNotIn('lkp_service_id',array(17,18,19,20))
		->where('is_read', 0)->count();
		
		return $messages_count;
		
		}
		catch(Exception $e)
		{
		}
	}

	/** Retrieval of seller / Buyer Business Name to display in the left navigation  **/
	public static function getBuyerSellerCount($userId, $roleId, $service)
	{
		$posts_status_id_all = OPEN;
		
		try
		{
			//echo "hi".$service;//exit;
		switch ($service) {
			case ROAD_FTL :
				$getpoststatusservices_road_ftl = CommonComponent::getPostStatusesService(ROAD_FTL);

				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount = DB::table('seller_posts as sps')->join('seller_post_items as spis','spis.seller_post_id','=','sps.id')
							->where('sps.lkp_service_id', $service)->where('spis.lkp_post_status_id', $posts_status_id_all)
							->where('sps.seller_id', $userId)->groupBy('sps.id')->get();
				$postsCount = count($postsCount);
				}else{
						
				$postsCount_spot = DB::table('buyer_quotes')
				->join('buyer_quote_items','buyer_quote_items.buyer_quote_id','=','buyer_quotes.id')
				->where('lkp_service_id', $service)->where('lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				$postsCount_term = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', $service)->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				$postsCount = $postsCount_spot+$postsCount_term;
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;

			case ROAD_PTL :
				$getpoststatusservices_road_ptl = CommonComponent::getPostStatusesService(ROAD_PTL);
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount = DB::table('ptl_seller_posts as psps')
				->join('ptl_seller_post_items as pspis','pspis.seller_post_id','=','psps.id')
				->where('psps.lkp_service_id', $service)->where('pspis.lkp_post_status_id', $posts_status_id_all)
				->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
				$postsCount =count($postsCount);
				}else {
				$postsCount = DB::table('ptl_buyer_quotes as pbqs')
				->join('ptl_buyer_quote_items as pbqis','pbqis.buyer_quote_id','=','pbqs.id')
				->where('pbqs.lkp_service_id', $service)->where('pbqis.lkp_post_status_id', $posts_status_id_all)
				->where('pbqs.buyer_id', $userId)->groupBy('pbqs.id')->get();
				$postsCount_spot =count($postsCount);
						
				$postsCount_term = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', $service)->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				$postsCount = $postsCount_spot+$postsCount_term;
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;
				
			case RELOCATION_PET_MOVE :
					$getpoststatusservices_road_ptl = CommonComponent::getPostStatusesService(RELOCATION_PET_MOVE);
					if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
						$postsCount = DB::table('relocationpet_seller_posts as psps')
						->where('psps.lkp_service_id', $service)->where('psps.lkp_post_status_id', $posts_status_id_all)
						->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
						$postsCount =count($postsCount);
					}else {
						$postsCount = DB::table('relocationpet_buyer_posts as pbqs')
						->where('pbqs.lkp_service_id', $service)->where('pbqs.lkp_post_status_id', $posts_status_id_all)
						->where('pbqs.buyer_id', $userId)->groupBy('pbqs.id')->get();
						$postsCount_spot_pet =count($postsCount);

						$postsCount = $postsCount_spot_pet;
					}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
					break;
					
			case RELOCATION_GLOBAL_MOBILITY :
					$getpoststatusservices_road_ptl = CommonComponent::getPostStatusesService(RELOCATION_PET_MOVE);
					if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
						$postscount_seller = DB::table('relocationgm_seller_posts as psps')
						->where('psps.lkp_service_id', $service)->where('psps.lkp_post_status_id', $posts_status_id_all)
						->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
						$postsCount =count($postscount_seller);
					}else {
						$postsCount = DB::table('relocationgm_buyer_posts as pbqs')
						->where('pbqs.lkp_service_id', $service)->where('pbqs.lkp_post_status_id', $posts_status_id_all)
						->where('pbqs.buyer_id', $userId)->groupBy('pbqs.id')->get();
						$postsCount_spot_gm =count($postsCount);

						$postsCount = $postsCount_spot_gm;
					}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
					break;

			case RELOCATION_INTERNATIONAL :
					$getpoststatusservices_road_ptl = CommonComponent::getPostStatusesService(RELOCATION_PET_MOVE);
					if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
						$postsCount = DB::table('relocationint_seller_posts as psps')
						->where('psps.lkp_service_id', $service)->where('psps.lkp_post_status_id', $posts_status_id_all)
						->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
						$postsCount =count($postsCount);
						}else {
						$postsCount_int = DB::table('relocationint_buyer_posts as pbqs')
						->where('pbqs.lkp_service_id', $service)->where('pbqs.lkp_post_status_id', $posts_status_id_all)
						->where('pbqs.buyer_id', $userId)->groupBy('pbqs.id')->get();
						$postsCount =count($postsCount_int);
						}
					if($postsCount>0){
					return $postsCount;
					}else{
					return '';
					}
						break;
					
			case RELOCATION_OFFICE_MOVE :
					$getpoststatusservices_road_ptl = CommonComponent::getPostStatusesService(RELOCATION_OFFICE_MOVE);
					if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
						$postsCount = DB::table('relocationoffice_seller_posts as psps')
						->where('psps.lkp_service_id', $service)->where('psps.lkp_post_status_id', $posts_status_id_all)
						->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
						$postsCount =count($postsCount);
						}else {
						$postsCount = DB::table('relocationoffice_buyer_posts as pbqs')
						->where('pbqs.lkp_service_id', $service)->where('pbqs.lkp_post_status_id', $posts_status_id_all)
						->where('pbqs.buyer_id', $userId)->groupBy('pbqs.id')->get();
						$postsCount_spot_off =count($postsCount);
					
						$postsCount = $postsCount_spot_off;
					}
				if($postsCount>0){
					return $postsCount;
					}else{
					return '';
					}
					break;

			case COURIER :
				$getpoststatusservices_road_courier = CommonComponent::getPostStatusesService(COURIER);
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount = DB::table('courier_seller_posts as psps')
				->join('courier_seller_post_items as pspis','pspis.seller_post_id','=','psps.id')
				->where('psps.lkp_service_id', $service)->where('pspis.lkp_post_status_id', $posts_status_id_all)
				->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
				$postsCount =count($postsCount);
				}else {
				$postsCount = DB::table('courier_buyer_quotes as pbqs')
				->join('courier_buyer_quote_items as pbqis','pbqis.buyer_quote_id','=','pbqs.id')
				->where('pbqs.lkp_service_id', $service)->where('pbqis.lkp_post_status_id', $posts_status_id_all)
				->where('pbqs.buyer_id', $userId)
				->groupBy('pbqs.id')
				->get();
				$postsCount_spot =count($postsCount);
					
				$postsCount_term_courier = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', $service)->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				
				$postsCount = $postsCount_spot+$postsCount_term_courier;
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;
			case RELOCATION_DOMESTIC :
				$getpoststatusservices_road_relocation_domastic = CommonComponent::getPostStatusesService(RELOCATION_DOMESTIC);	
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount = DB::table('relocation_seller_posts as psps')
				->where('psps.lkp_service_id', $service)
				->where('psps.lkp_post_status_id', $posts_status_id_all)
				->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
				$postsCount =count($postsCount);
				}else {
				$postsCount = DB::table('relocation_buyer_posts as pbqs')
				->where('pbqs.lkp_service_id', $service)
				->where('pbqs.lkp_post_status_id', $posts_status_id_all)
				->where('pbqs.buyer_id', $userId)->groupBy('pbqs.id')->get();
				$postsCount_spot =count($postsCount);
									
				$postsCount_term_relocation = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', $service)->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				
				$postsCount = $postsCount_spot+$postsCount_term_relocation;					
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;
			case AIR_INTERNATIONAL :
				$getpoststatusservices_air_international = CommonComponent::getPostStatusesService(AIR_INTERNATIONAL);
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount = DB::table('airint_seller_posts as aisps')
				->join('airint_seller_post_items as aispis','aispis.seller_post_id','=','aisps.id')
				->where('aisps.lkp_service_id', $service)
				->where('aispis.lkp_post_status_id', $posts_status_id_all)
				->where('aisps.seller_id', $userId)->groupBy('aisps.id')->get();
				$postsCount =count($postsCount);
				}else {
				$postsCount = DB::table('airint_buyer_quotes as aibqs')
				->join('airint_buyer_quote_items as aibqis','aibqis.buyer_quote_id','=','aibqs.id')
				->where('aibqs.lkp_service_id', $service)
				->where('aibqis.lkp_post_status_id', $posts_status_id_all)
				->where('aibqs.buyer_id', $userId)->groupBy('aibqs.id')->get();						
				$postsCount_spot =count($postsCount);
						
				$postsCount_term = DB::table('term_buyer_quotes as tbq')
				//->join('term_buyer_quote_items','term_buyer_quote_items.term_buyer_quote_id','=','tbq.id')
				->where('tbq.lkp_service_id', $service)
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				$postsCount = $postsCount_spot+$postsCount_term;
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;

			case RAIL :
				$getpoststatusservices_rail = CommonComponent::getPostStatusesService(RAIL);
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount = DB::table('rail_seller_posts as rsps')
				->join('rail_seller_post_items as rspis','rspis.seller_post_id','=','rsps.id')
				->where('rsps.lkp_service_id', $service)
				->where('rspis.lkp_post_status_id', $posts_status_id_all)
				->where('rsps.seller_id', $userId)->groupBy('rsps.id')->get();
				$postsCount =count($postsCount);
				}else {
				$postsCount = DB::table('rail_buyer_quotes as rbqs')
				->join('rail_buyer_quote_items as rbqis','rbqis.buyer_quote_id','=','rbqs.id')
				->where('rbqs.lkp_service_id', $service)
				->where('rbqis.lkp_post_status_id', $posts_status_id_all)
				->where('rbqs.buyer_id', $userId)->groupBy('rbqs.id')->get();
				$postsCount_spot =count($postsCount);
							
				$postsCount_term = DB::table('term_buyer_quotes as tbq')
				//->join('term_buyer_quote_items','term_buyer_quote_items.term_buyer_quote_id','=','tbq.id')
				->where('tbq.lkp_service_id', $service)
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();	
				$postsCount = $postsCount_spot+$postsCount_term;	
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;
			case OCEAN :
				$getpoststatusservices_ocean = CommonComponent::getPostStatusesService(OCEAN);
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount = DB::table('ocean_seller_posts as osps')
				->join('ocean_seller_post_items as ospis','ospis.seller_post_id','=','osps.id')
				->where('osps.lkp_service_id', $service)
				->where('ospis.lkp_post_status_id', $posts_status_id_all)
				->where('osps.seller_id', $userId)->groupBy('osps.id')->get();
				$postsCount =count($postsCount);
				}else {
				$postsCount = DB::table('ocean_buyer_quotes as obqs')
				->join('ocean_buyer_quote_items as obqis','obqis.buyer_quote_id','=','obqs.id')
				->where('obqs.lkp_service_id', $service)
				->where('obqis.lkp_post_status_id', $posts_status_id_all)
				->where('obqs.buyer_id', $userId)->groupBy('obqs.id')->get();
				$postsCount_spot =count($postsCount);
						
				$postsCount_term = DB::table('term_buyer_quotes as tbq')
				//->join('term_buyer_quote_items','term_buyer_quote_items.term_buyer_quote_id','=','tbq.id')
				->where('tbq.lkp_service_id', $service)
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				$postsCount = $postsCount_spot+$postsCount_term;
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;
			case AIR_DOMESTIC :
				$getpoststatusservices_air_domestic = CommonComponent::getPostStatusesService(AIR_DOMESTIC);
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount = DB::table('airdom_seller_posts as asps')
				->join('airdom_seller_post_items as aspis','aspis.seller_post_id','=','asps.id')
				->where('asps.lkp_service_id', $service)
				->where('aspis.lkp_post_status_id', $posts_status_id_all)
				->where('asps.seller_id', $userId)->groupBy('asps.id')->get();
				$postsCount =count($postsCount);
				}else {
				$postsCount = DB::table('airdom_buyer_quotes as abqs')
				->join('airdom_buyer_quote_items as abqis','abqis.buyer_quote_id','=','abqs.id')
				->where('abqs.lkp_service_id', $service)
				->where('abqis.lkp_post_status_id', $posts_status_id_all)
				->where('abqs.buyer_id', $userId)->groupBy('abqs.id')->get();
				$postsCount_spot =count($postsCount);					
						
				$postsCount_term = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', $service)
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				$postsCount = $postsCount_spot+$postsCount_term;	
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;

			case ROAD_TRUCK_HAUL :
				$getpoststatusservices_road_truck_hual = CommonComponent::getPostStatusesService(ROAD_TRUCK_HAUL);
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount = DB::table('truckhaul_seller_posts as sps')->join('truckhaul_seller_post_items as spis','spis.seller_post_id','=','sps.id')
							->where('sps.lkp_service_id', $service)->where('spis.lkp_post_status_id', $posts_status_id_all)
							->where('sps.seller_id', $userId)->groupBy('sps.id')->get();
				$postsCount = count($postsCount);
				}else {
				$postsCount_spot = DB::table('truckhaul_buyer_quotes')
				->join('truckhaul_buyer_quote_items','truckhaul_buyer_quote_items.buyer_quote_id','=','truckhaul_buyer_quotes.id')
				->where('lkp_service_id', $service)->where('lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				$postsCount = $postsCount_spot;
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;

			case ROAD :
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount_ftl = DB::table('seller_posts as sps')
				->join('seller_post_items as spis','spis.seller_post_id','=','sps.id')
				->where('spis.lkp_post_status_id', $posts_status_id_all)
				->where('sps.seller_id', $userId)->groupBy('sps.id')->get();
				$postsCount_Ftls = count($postsCount_ftl);

				$postsCount_ptl = DB::table('ptl_seller_posts as psps')
				->join('ptl_seller_post_items as pspis','pspis.seller_post_id','=','psps.id')
				->where('pspis.lkp_post_status_id', $posts_status_id_all)
				->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
				$postsCount_Ptls =count($postsCount_ptl);
				
				$postsCount_rail = DB::table('rail_seller_posts as rsps')
				->join('rail_seller_post_items as rspis','rspis.seller_post_id','=','rsps.id')
				->where('rspis.lkp_post_status_id', $posts_status_id_all)
				->where('rsps.seller_id', $userId)->groupBy('rsps.id')->get();
				$postsCount_rails =count($postsCount_rail);
				
				$postsCount_airdom = DB::table('airdom_seller_posts as asps')
				->join('airdom_seller_post_items as aspis','aspis.seller_post_id','=','asps.id')
				->where('aspis.lkp_post_status_id', $posts_status_id_all)
				->where('asps.seller_id', $userId)->groupBy('asps.id')->get();
				$postsCount_airdoms =count($postsCount_airdom);
				
				$postsCount_airint = DB::table('airint_seller_posts as aisps')
				->join('airint_seller_post_items as aispis','aispis.seller_post_id','=','aisps.id')
				->where('aispis.lkp_post_status_id', $posts_status_id_all)
				->where('aisps.seller_id', $userId)->groupBy('aisps.id')->get();
				$postsCount_airints =count($postsCount_airint);
				
				$postsCount_ocean = DB::table('ocean_seller_posts as osps')
				->join('ocean_seller_post_items as ospis','ospis.seller_post_id','=','osps.id')
				->where('ospis.lkp_post_status_id', $posts_status_id_all)
				->where('osps.seller_id', $userId)->groupBy('osps.id')->get();
				$postsCount_oceans =count($postsCount_ocean);
				
				$postsCount_courier = DB::table('courier_seller_posts as psps')
				->join('courier_seller_post_items as pspis','pspis.seller_post_id','=','psps.id')
				->where('pspis.lkp_post_status_id', $posts_status_id_all)
				->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
				$postsCount_couriers =count($postsCount_courier);
				
				$postsCount_truck_hual = DB::table('truckhaul_seller_posts as sps')
				->join('truckhaul_seller_post_items as spis','spis.seller_post_id','=','sps.id')
				->where('spis.lkp_post_status_id', $posts_status_id_all)
				->where('sps.seller_id', $userId)->groupBy('sps.id')->get();
				$posts_truck_hual = count($postsCount_truck_hual);
				
				$postsCount_truck_lease = DB::table('trucklease_seller_posts as sps')
				->join('trucklease_seller_post_items as spis','spis.seller_post_id','=','sps.id')
				->where('spis.lkp_post_status_id', $posts_status_id_all)
				->where('sps.seller_id', $userId)->groupBy('sps.id')->get();
				$posts_truck_lease = count($postsCount_truck_lease);
				
				
				$postsCount_relocation_domastic = DB::table('relocation_seller_posts as psps')
				->where('psps.lkp_post_status_id', $posts_status_id_all)
				->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
				$postsCount_relocation_domastics =count($postsCount_relocation_domastic);
				
				
				$postsCount_pet_move = DB::table('relocationpet_seller_posts as psps')
				->where('psps.lkp_service_id', $service)->where('psps.lkp_post_status_id', $posts_status_id_all)
				->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
				$postsCount_pet =count($postsCount_pet_move);
				
                                
                                $postscount_gm_spot = DB::table('relocationgm_seller_posts as psps')
				->where('psps.lkp_service_id', $service)->where('psps.lkp_post_status_id', $posts_status_id_all)
				->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
				$postsCount_gm =count($postscount_gm_spot);
                                
                                
				$postsCount_office = DB::table('relocationoffice_seller_posts as psps')
				->where('psps.lkp_service_id', $service)->where('psps.lkp_post_status_id', $posts_status_id_all)
				->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
				$postsCount_office_move =count($postsCount_office);
				
				
				$postsCount_re_international = DB::table('relocationint_seller_posts as psps')
				->where('psps.lkp_service_id', $service)->where('psps.lkp_post_status_id', $posts_status_id_all)
				->where('psps.seller_id', $userId)->groupBy('psps.id')->get();
				$postsCount_re_international_spot =count($postsCount_re_international);
				
				
				
				$postsCount = $postsCount_Ftls+$postsCount_gm+$postsCount_re_international_spot+$postsCount_office_move+$postsCount_pet+$postsCount_Ptls+$postsCount_rails+$postsCount_airdoms+$postsCount_airints+$postsCount_oceans+$postsCount_couriers+$posts_truck_hual+$posts_truck_lease+$postsCount_relocation_domastics;
				}else {
				$getpoststatusservices_road_ftl = CommonComponent::getPostStatusesService(ROAD_FTL);
				$postsCount_term_ftl = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', ROAD_FTL)
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
						
				$postsCount_spot_ftl = DB::table('buyer_quotes')
				->join('buyer_quote_items','buyer_quote_items.buyer_quote_id','=','buyer_quotes.id')
				->where('lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				
				$postsCount_ftl_buyer = $postsCount_term_ftl+$postsCount_spot_ftl;
				
				$getpoststatusservices_road_ptl = CommonComponent::getPostStatusesService(ROAD_PTL);
				$postsCount_spot_ptl = DB::table('ptl_buyer_quotes as pbqs')
				->join('ptl_buyer_quote_items as pbqis','pbqis.buyer_quote_id','=','pbqs.id')
				->where('pbqis.lkp_post_status_id', $posts_status_id_all)
				->where('pbqs.buyer_id', $userId)->groupBy('pbqs.id')->get();
				$postsCount_spot_ptls =count($postsCount_spot_ptl);
				
				$postsCount_term_ptl = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('tbq.lkp_service_id', ROAD_PTL)
				->where('buyer_id', $userId)->count();
				$postsCount_ptl_buyer = $postsCount_spot_ptls+$postsCount_term_ptl;
				
				$getpoststatusservices_rail = CommonComponent::getPostStatusesService(RAIL);
				$postsCount_spot_rail = DB::table('rail_buyer_quotes as rbqs')
				->join('rail_buyer_quote_items as rbqis','rbqis.buyer_quote_id','=','rbqs.id')
				->where('rbqis.lkp_post_status_id', $posts_status_id_all)
				->where('rbqs.buyer_id', $userId)->groupBy('rbqs.id')->get();
				$postsCount_spot_rails =count($postsCount_spot_rail);
					
				$postsCount_term_rail = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', RAIL)
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				$postsCount_rail_buyer = $postsCount_spot_rails+$postsCount_term_rail;
				
				$getpoststatusservices_air_domestic = CommonComponent::getPostStatusesService(AIR_DOMESTIC);
				$postsCount_spot_air_domastic = DB::table('airdom_buyer_quotes as abqs')
				->join('airdom_buyer_quote_items as abqis','abqis.buyer_quote_id','=','abqs.id')
				->where('abqis.lkp_post_status_id', $posts_status_id_all)
				->where('abqs.buyer_id', $userId)->groupBy('abqs.id')->get();
				$postsCount_spot_air_domastics =count($postsCount_spot_air_domastic);
				
				$postsCount_term_air_domastic = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', AIR_DOMESTIC)
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				$postsCount_air_domastic_buyer = $postsCount_spot_air_domastics+$postsCount_term_air_domastic;
				
				$getpoststatusservices_air_international = CommonComponent::getPostStatusesService(AIR_INTERNATIONAL);
				$postsCount_spot_airint = DB::table('airint_buyer_quotes as aibqs')
				->join('airint_buyer_quote_items as aibqis','aibqis.buyer_quote_id','=','aibqs.id')
				->where('aibqis.lkp_post_status_id', $posts_status_id_all)
				->where('aibqs.buyer_id', $userId)->groupBy('aibqs.id')->get();
				$postsCount_spot_airints =count($postsCount_spot_airint);
				
				$postsCount_term__airints = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', AIR_INTERNATIONAL)
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				$postsCount_air_int_buyer = $postsCount_spot_airints+$postsCount_term__airints;
				
				$getpoststatusservices_ocean = CommonComponent::getPostStatusesService(OCEAN);
				$postsCount_spot_ocean = DB::table('ocean_buyer_quotes as obqs')
				->join('ocean_buyer_quote_items as obqis','obqis.buyer_quote_id','=','obqs.id')
				->where('obqis.lkp_post_status_id', $posts_status_id_all)
				->where('obqs.buyer_id', $userId)->groupBy('obqs.id')->get();
				$postsCount_spot_oceans =count($postsCount_spot_ocean);
				
				$postsCount_term_ocean = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', OCEAN)
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				$postsCount_ocean_buyer = $postsCount_spot_oceans+$postsCount_term_ocean;
				
				$getpoststatus_services_road_intracity = CommonComponent::getPostStatusesService(ROAD_INTRACITY);
				$postsCount_spot_buyer_intra = DB::table('ict_buyer_quotes')
				->join('ict_buyer_quote_items','ict_buyer_quote_items.buyer_quote_id','=','ict_buyer_quotes.id')
				->where('lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				
				$getpoststatusservices_road_courier = CommonComponent::getPostStatusesService(COURIER);
				$postsCount_spot_courier = DB::table('courier_buyer_quotes as pbqs')
				->join('courier_buyer_quote_items as pbqis','pbqis.buyer_quote_id','=','pbqs.id')
				->where('pbqis.lkp_post_status_id', $posts_status_id_all)
				->where('pbqs.buyer_id', $userId)
				->groupBy('pbqs.id')
				->get();
				
				$postsCount_term_courier = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', COURIER)
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				$posts_courier_count = count($postsCount_spot_courier);
				
				$postsCount_courier_buyer =$posts_courier_count+$postsCount_term_courier;
				
				$getpoststatusservices_road_truck_hual = CommonComponent::getPostStatusesService(ROAD_TRUCK_HAUL);
				$postsCount_spot_truckhual = DB::table('truckhaul_buyer_quotes')
				->join('truckhaul_buyer_quote_items','truckhaul_buyer_quote_items.buyer_quote_id','=','truckhaul_buyer_quotes.id')
				->where('lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				
				$getpoststatusservices_road_truck_lease = CommonComponent::getPostStatusesService(ROAD_TRUCK_LEASE);
				$posts_spot_truck_lease = DB::table('trucklease_buyer_quotes')
				->join('trucklease_buyer_quote_items','trucklease_buyer_quote_items.buyer_quote_id','=','trucklease_buyer_quotes.id')
				->where('lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				
				$getpoststatusservices_road_relocation_domastic = CommonComponent::getPostStatusesService(RELOCATION_DOMESTIC);
				$postsCount_spot_relocation = DB::table('relocation_buyer_posts as pbqs')
				->where('pbqs.lkp_post_status_id', $posts_status_id_all)
				->where('pbqs.buyer_id', $userId)->groupBy('pbqs.id')->get();
				
				
				
				$postsCount_term_relocation_domestic = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', RELOCATION_DOMESTIC)
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				
				$postsCount_spot_relocations =count($postsCount_spot_relocation)+$postsCount_term_relocation_domestic;
				
				$postsCount_pet_move = DB::table('relocationpet_buyer_posts as pbqs')
				->where('pbqs.lkp_service_id', $service)->where('pbqs.lkp_post_status_id', $posts_status_id_all)
				->where('pbqs.buyer_id', $userId)->groupBy('pbqs.id')->get();
                                
				$postsCount_pet_move_spot =count($postsCount_pet_move);
				
                                
                                
				$postsCountoffice_move = DB::table('relocationoffice_buyer_posts as pbqs')
				->where('pbqs.lkp_service_id', $service)->where('pbqs.lkp_post_status_id', $posts_status_id_all)
				->where('pbqs.buyer_id', $userId)->groupBy('pbqs.id')->get();
                                
                                
				$postsCount_spot_office =count($postsCountoffice_move);
				
                                
                                $postsCount_gm_buyer = DB::table('relocationgm_buyer_posts as pbqs')
						->where('pbqs.lkp_service_id', $service)->where('pbqs.lkp_post_status_id', $posts_status_id_all)
						->where('pbqs.buyer_id', $userId)->groupBy('pbqs.id')->get();
                                
                                
                                $postsCount_term_relocation_gm = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', RELOCATION_GLOBAL_MOBILITY)
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
                                
				$postsCount_gm_buyer_tot =count($postsCount_gm_buyer)+$postsCount_term_relocation_gm;
                                
				$postsCount_int = DB::table('relocationint_buyer_posts as pbqs')
				->where('pbqs.lkp_service_id', $service)->where('pbqs.lkp_post_status_id', $posts_status_id_all)
				->where('pbqs.buyer_id', $userId)->groupBy('pbqs.id')->get();
                                
                                $postsCount_term_relocation_air_ocean = DB::table('term_buyer_quotes as tbq')
				->where('tbq.lkp_service_id', RELOCATION_INTERNATIONAL)
				->where('tbq.lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
                                
				$postsCount_international =count($postsCount_int)+$postsCount_term_relocation_air_ocean;
				
				
				
				$postsCount =$postsCount_ftl_buyer+$postsCount_gm_buyer_tot+$postsCount_international+$postsCount_spot_office+$postsCount_pet_move_spot+$postsCount_ptl_buyer+$postsCount_rail_buyer+$postsCount_spot_truckhual+$posts_spot_truck_lease+$postsCount_air_domastic_buyer+$postsCount_air_int_buyer+$postsCount_ocean_buyer+$postsCount_spot_buyer_intra+$postsCount_courier_buyer+$postsCount_spot_relocations;
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;

			case ORDERSCOUNT :
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount_order = DB::table('orders')->where('seller_id', $userId)->where('lkp_order_status_id', $posts_status_id_all)->where('seller_id', $userId)->count();
				$postsCount = $postsCount_order;
				}else {
				$orderCount_order = DB::table('orders')->where('lkp_order_status_id', $posts_status_id_all)->where('buyer_id', $userId)->count();
				$postsCount = $orderCount_order;
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;

			case ORDERSINVUDUAL :
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount_invudual = DB::table('orders')->where('lkp_service_id', Session::get('service_id'))->where('lkp_order_status_id', $posts_status_id_all)->where('seller_id', $userId)->count();
				$postsCount = $postsCount_invudual;
				}else {
				$postsCount_invudual = DB::table('orders')->where('lkp_service_id', Session::get('service_id'))->where('lkp_order_status_id', $posts_status_id_all)->where('buyer_id', $userId)->count();
				$postsCount = $postsCount_invudual;
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;

			case ROAD_TRUCK_LEASE :
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount = DB::table('trucklease_seller_posts as sps')->join('trucklease_seller_post_items as spis','spis.seller_post_id','=','sps.id')
							->where('sps.lkp_service_id', $service)->where('spis.lkp_post_status_id', $posts_status_id_all)
							->where('sps.seller_id', $userId)->groupBy('sps.id')->get();
				$postsCount = count($postsCount);
				}else {
				$postsCount_spot = DB::table('trucklease_buyer_quotes')
				->join('trucklease_buyer_quote_items','trucklease_buyer_quote_items.buyer_quote_id','=','trucklease_buyer_quotes.id')
				->where('lkp_service_id', $service)->where('lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				$postsCount = $postsCount_spot;
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;
			case ROAD_INTRACITY :
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount = 0;
				}else {
				$getpoststatus_services_road_intracity = CommonComponent::getPostStatusesService(ROAD_INTRACITY);
				$postsCount = DB::table('ict_buyer_quotes')
				->join('ict_buyer_quote_items','ict_buyer_quote_items.buyer_quote_id','=','ict_buyer_quotes.id')
				->where('lkp_service_id', $service)
				->where('lkp_post_status_id', $posts_status_id_all)
				->where('buyer_id', $userId)->count();
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;
			default :
				if(($roleId == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
				$postsCount = DB::table('seller_posts')->where('lkp_service_id', $service)->where('seller_id', $userId)->count();
				}else {
				$postsCount = DB::table('buyer_quotes')->where('lkp_service_id', $service)->where('buyer_id', $userId)->count();
				}
				if($postsCount>0){
				return $postsCount;
				}else{
				return '';
				}
				break;
			}
		}
		catch(Exception $e)
		{
		}
	}

	/** Retrieval of City name  **/
	public static function getCityName($cityId)
	{
		try
		{

			$getCityName = DB::table('lkp_cities')
				->where('lkp_cities.id', '=', $cityId)
				->select('lkp_cities.city_name')
				->get();

			return $getCityName[0]->city_name;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of City name from pincode  **/
	public static function getPinName($pin)
	{
		try
		{
			$serviceId = Session::get('service_id');
			switch($serviceId){
                case ROAD_PTL:
                case RAIL:
                case AIR_DOMESTIC:
                case COURIER:
	                $getLocationName = DB::table('lkp_ptl_pincodes')
						->where('lkp_ptl_pincodes.id', '=', $pin)
						->select('lkp_ptl_pincodes.postoffice_name')
						->get();
					return $getLocationName[0]->postoffice_name;
                    break;                
                case AIR_INTERNATIONAL:
                	$getLocationName = DB::table('lkp_airports')
						->where('lkp_airports.id', '=', $pin)
						->select('lkp_airports.airport_name')
						->get();
					return $getLocationName[0]->airport_name;
                    break;
                case OCEAN:
	                $getLocationName = DB::table('lkp_seaports')
						->where('lkp_seaports.id', '=', $pin)
						->select('lkp_seaports.seaport_name')
						->get();
					return $getLocationName[0]->seaport_name;
                    break;               
            }  

		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Zones Code **/
	public static function zonePincodes($id)
	{
		$pin = DB::table('ptl_zones')
			->join ( 'ptl_sectors', 'ptl_sectors.ptl_zone_id', '=', 'ptl_zones.id' )
			->join ( 'ptl_pincodexsectors', 'ptl_pincodexsectors.ptl_sector_id', '=', 'ptl_sectors.id' )
			->where('ptl_zones.id',$id)
			->select('ptl_pincodexsectors.ptl_pincode_id')
			->get();
		//print_r($pin);exit;

		if(isset($pin[0]->ptl_pincode_id))
			return $pin;
		else
			return 0;

	}

	/** Retrieval of City name from pincode  **/
	public static function getPinNameWithPincode($pin)
	{
		try
		{
			$getPincityName = DB::table('lkp_ptl_pincodes')
				->where('lkp_ptl_pincodes.pincode', '=', $pin)
				->select('lkp_ptl_pincodes.postoffice_name')
				->get();
			return $getPincityName[0]->postoffice_name;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of City name from pincode  **/
	public static function getZoneName($pin)
	{
		try
		{

			$getZonecityName = DB::table('ptl_zones')
				->where('ptl_zones.id', '=', $pin)
				->select('ptl_zones.zone_name')
				->get();

			return $getZonecityName[0]->zone_name;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Retrieval of Country  **/
	public static function getCountry($pin)
	{
		try
		{
	
			$getcountryname = DB::table('lkp_countries')
			->where('lkp_countries.id', '=', $pin)
			->select('lkp_countries.country_name')
			->get();
	
			return $getcountryname[0]->country_name;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	public static function getZonePin($pin)
	{

		try
		{
			$getPincityName = DB::table('lkp_ptl_pincodes')
				->where('lkp_ptl_pincodes.id', '=',$pin)
				->select('lkp_ptl_pincodes.pincode')
				->get();

			return $getPincityName[0]->pincode;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}

	}

	public static function getPinNameFromId($pin)
	{

		try
		{
			$getPincityName = DB::table('lkp_ptl_pincodes')
				->where('lkp_ptl_pincodes.id', '=',$pin)
				->select('lkp_ptl_pincodes.postoffice_name')
				->get();

			return $getPincityName[0]->postoffice_name;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}

	}

	public static function getZonePinId($pin)
	{

		try
		{
			$getPincityName = DB::table('lkp_ptl_pincodes')
				->where('lkp_ptl_pincodes.pincode', '=',$pin)
				->select('lkp_ptl_pincodes.id')
				->get();

			return $getPincityName[0]->id;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}

	}

	/**
	 * Retrieval of Vehicle Type *
	 */
	public static function getVehicleType($vehicleid) {
		try {

			$getvehiclename = DB::table ( 'lkp_vehicle_types' )->where ( 'lkp_vehicle_types.id', '=', $vehicleid )->select ( 'lkp_vehicle_types.vehicle_type' )->get ();

			return $getvehiclename [0]->vehicle_type;
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

	/**
	* Get vehicle info based on vehicle type id
	* @author shriram
	*/
	public static function getVehicleReqCol($vehicleid, $reqField='dimension') {
		try {

			$vehicleInfo = DB::table ( 'lkp_vehicle_types' )
			->where ( 'lkp_vehicle_types.id', '=', $vehicleid )->value($reqField);
			return $vehicleInfo;

		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

	public static function getBidType($buyer_quote_id) {
		try {

			$lkp_bid_type_id = DB::table ( 'term_buyer_quotes as tbq' )->join ( 'lkp_bid_types as lbt', 'tbq.lkp_bid_type_id', '=', 'lbt.id' )->where ( 'tbq.id', '=', $buyer_quote_id )->select ( 'lbt.bid_type' )->get ();

			return $lkp_bid_type_id [0]->bid_type;
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

	/** Retrieval of Vehicle Type  **/
	public static function getPackageType($packageid)
	{
		try
		{

			$getpackagename = DB::table('lkp_packaging_types')
				->where('lkp_packaging_types.id', '=', $packageid)
				->select('lkp_packaging_types.packaging_type_name')
				->get();

			return $getpackagename[0]->packaging_type_name;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Load Type for particular id**/
	public static function getLoadType($loadid)
	{
		try
		{

			$getloadname = DB::table('lkp_load_types')
				->where('lkp_load_types.id', '=', $loadid)
				->select('lkp_load_types.load_type')
				->get();

			return $getloadname[0]->load_type;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of Courier Type for particular id**/
	public static function getCourierType($cid)
	{
		try
		{
	
			$getloadname = DB::table('lkp_courier_types')
			->where('lkp_courier_types.id', '=', $cid)
			->select('lkp_courier_types.courier_type')
			->get();
	
			return $getloadname[0]->courier_type;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of Courier Type for particular id**/
	public static function getCourierDeliveryType($cdid)
	{
		try
		{
	
			$getloadname = DB::table('lkp_courier_delivery_types')
			->where('lkp_courier_delivery_types.id', '=', $cdid)
			->select('lkp_courier_delivery_types.courier_delivery_type 	')
			->get();
	
			return $getloadname[0]->courier_delivery_type 	;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}


	/** Price type validation  **/
	public static function getPriceType($price, $boolType = true)
	{
		if (function_exists('money_format')) {
			setlocale(LC_MONETARY, 'en_IN');
			if ($boolType):
				return money_format('%!i', $price) . "/-";;
			else:
				return money_format('%!.0n', $price) . "/-";
			endif;
		}
		return $price;
	}

	/** Retrieval of Post statuses  **/
	public static function getPostStatuses()
	{
		try
		{
			$serviceCompareId = Session::get('service_id');
			$roleCompareId = Auth::User()->lkp_role_id;

			$postStatuses = DB::table('lkp_post_statuses');
                        $postStatuses->orderBy('post_status', 'asc' );
			if($serviceCompareId == ROAD_FTL){
				$postStatuses->where('is_ftl', '=', '1');
			}elseif($serviceCompareId == ROAD_PTL){
				$postStatuses->where('is_ltl', '=', '1');
			}elseif($serviceCompareId == ROAD_INTRACITY){
				$postStatuses->where('is_intracity', '=', '1');
			}elseif($serviceCompareId == ROAD_TRUCK_HAUL){
				$postStatuses->where('is_truckhaul', '=', '1');
			}elseif($serviceCompareId == ROAD_TRUCK_LEASE){
				$postStatuses->where('is_trucklease', '=', '1');
			}elseif($serviceCompareId == RAIL){
				$postStatuses->where('is_rail', '=', '1');
			}elseif($serviceCompareId == COURIER){
				$postStatuses->where('is_courier', '=', '1');
			}elseif($serviceCompareId == AIR_DOMESTIC){
				$postStatuses->where('is_airdomestic', '=', '1');
			}elseif($serviceCompareId == AIR_INTERNATIONAL){
				$postStatuses->where('is_airinternational', '=', '1');
			}elseif($serviceCompareId == OCEAN){
				$postStatuses->where('is_ocean', '=', '1');
			}elseif($serviceCompareId == HANDLING_SERVICES){
				$postStatuses->where('is_handling_services', '=', '1');
			}elseif($serviceCompareId == EQUIPMENT_LEASE){
				$postStatuses->where('is_equipment_lease', '=', '1');
			}elseif($serviceCompareId == PACKAGING_SERVICES){
				$postStatuses->where('is_packaging_services', '=', '1');
			}elseif($serviceCompareId == WAREHOUSE){
				$postStatuses->where('is_warehouse', '=', '1');
			}elseif($serviceCompareId == THIRD_PARTY_LOGISTICS){
				$postStatuses->where('is_thirdparty_logistiks', '=', '1');
			}elseif($serviceCompareId == RELOCATION_DOMESTIC){
				$postStatuses->where('is_relocation_domestic', '=', '1');
			}elseif($serviceCompareId == RELOCATION_INTERNATIONAL){
				$postStatuses->where('is_relocation_international', '=', '1');
			}elseif($serviceCompareId == RELOCATION_GLOBAL_MOBILITY){
				$postStatuses->where('is_relocation_global', '=', '1');
			}/*elseif($serviceCompareId == RELOCATION_GLOBAL_MOVE){
				$postStatuses->where('is_relocation_office_move', '=', '1');
			}*/elseif($serviceCompareId == RELOCATION_PET_MOVE){
				$postStatuses->where('is_relocation_pet_move', '=', '1');
			}elseif($serviceCompareId == RELOCATION_OFFICE_MOVE){
				$postStatuses->where('is_relocation_office_move', '=', '1');
			}elseif($serviceCompareId == MULTIMODEL){
				$postStatuses->where('is_ftl', '=', '1');
			}

			if($roleCompareId == SELLER)
				$postStatuses->where('is_seller_display', '=', '1');
			elseif($roleCompareId == BUYER)
				$postStatuses->where('is_buyer_display', '=', '1');

			$postStatusesArr = $postStatuses->lists('post_status', 'id');

			return $postStatusesArr;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Get of Post statuses  **/
	public static function getPostStatus($id)
	{
		try
		{
			$postStatuses = DB::table('lkp_post_statuses')->orderBy('post_status', 'asc' )->where('id',$id)->select ( 'id','post_status' )->lists('post_status', 'id');
			return $postStatuses;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Services  **/
	public static function getServices()
	{
		try
		{
			$services = DB::table ( 'seller_services as ss' )->join ( 'lkp_services as ls', 'ss.lkp_service_id', '=', 'ls.id' )->orderBy('ls.service_name', 'asc' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
			return $services;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/**
	 * Get all services for dropdown
	 */
	public static function getAllServices() {
		try{
//            CommonComponent::activityLog("GET_STATE", GET_STATE, 0, HTTP_REFERRER, CURRENT_URL);
			$allservices = [];
			$allservices[0] = 'Services (ALL)';
			$roleId = Auth::User()->lkp_role_id;
			$allservice = DB::table('lkp_services');
                        $allservice->orderBy('service_name', 'asc' );
			$allservice->where('is_active', '1');
			$allservice = $allservice->lists('service_name', 'id');
			foreach ($allservice as $id => $servicename) {
				$allservices[$id] = $servicename;
			}
			return $allservices;
		} catch (Exception $ex) {

		}
	}

	/** Retrieval of Lead Types  **/
	public static function getLeadTypes()
	{
		try
		{
			$leadTypes = DB::table('lkp_lead_types')->orderBy('lead_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('lead_type', 'id');
			return $leadTypes;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}


	/** Retrieval of Enquiry Types  **/
	public static function getEnquiryTypes()
	{
		try
		{
			$enquiry_types = DB::table('lkp_enquiry_types')->orderBy('enquiry_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('enquiry_type', 'id');
			return $enquiry_types;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Order Status  **/
	public static function getOrderStatuses()
	{
		try
		{
			$order_status = DB::table('lkp_order_statuses');
                        $order_status->orderBy('order_status', 'asc' );
			$serviceCompareId = Session::get('service_id');
			$roleCompareId = Auth::User()->lkp_role_id;

			if($serviceCompareId == ROAD_FTL){
				$order_status->where('is_ftl', '=', '1');
			}elseif($serviceCompareId == ROAD_PTL){
				$order_status->where('is_ltl', '=', '1');
			}elseif($serviceCompareId == ROAD_INTRACITY){
				$order_status->where('is_intracity', '=', '1');
			}elseif($serviceCompareId == ROAD_TRUCK_HAUL){
				$order_status->where('is_truckhaul', '=', '1');
			}elseif($serviceCompareId == ROAD_TRUCK_LEASE){
				$order_status->where('is_trucklease', '=', '1');
			}elseif($serviceCompareId == RAIL){
				$order_status->where('is_rail', '=', '1');
			}elseif($serviceCompareId == AIR_DOMESTIC){
				$order_status->where('is_airdomestic', '=', '1');
			}elseif($serviceCompareId == AIR_INTERNATIONAL){
				$order_status->where('is_airinternational', '=', '1');
			}elseif($serviceCompareId == OCEAN){
				$order_status->where('is_ocean', '=', '1');
			}elseif($serviceCompareId == HANDLING_SERVICES){
				$order_status->where('is_handling_services', '=', '1');
			}elseif($serviceCompareId == EQUIPMENT_LEASE){
				$order_status->where('is_equipment_lease', '=', '1');
			}elseif($serviceCompareId == PACKAGING_SERVICES){
				$order_status->where('is_packaging_services', '=', '1');
			}elseif($serviceCompareId == WAREHOUSE){
				$order_status->where('is_warehouse', '=', '1');
			}elseif($serviceCompareId == COURIER){
				$order_status->where('is_courier', '=', '1');
			}elseif($serviceCompareId == THIRD_PARTY_LOGISTICS){
				$order_status->where('is_thirdparty_logistiks', '=', '1');
			}elseif($serviceCompareId == RELOCATION_DOMESTIC){
				$order_status->where('is_relocation_domestic', '=', '1');
			}elseif($serviceCompareId == RELOCATION_INTERNATIONAL){
				$order_status->where('is_relocation_international', '=', '1');
			}elseif($serviceCompareId == RELOCATION_GLOBAL_MOBILITY){
				$order_status->where('is_relocation_global', '=', '1');
			}elseif($serviceCompareId == RELOCATION_OFFICE_MOVE){
				$order_status->where('is_relocation_office_move', '=', '1');
			}elseif($serviceCompareId == RELOCATION_PET_MOVE){
				$order_status->where('is_relocation_pet_move', '=', '1');
			}elseif($serviceCompareId == MULTIMODEL){
				$order_status->where('is_ftl', '=', '1');
			}
			$order_status->where('is_contract', '=', '0');
			if($roleCompareId == SELLER)
				$order_status->where('is_seller_display', '=', '1');
			elseif($roleCompareId == BUYER)
				$order_status->where('is_buyer_display', '=', '1');
     
			$order_status_arr = $order_status->lists('order_status', 'id');
			return $order_status_arr;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Order Types  **/
	public static function getOrderTypes()
	{
		try
		{
			$orderTypes = DB::table('lkp_order_types')->orderBy('order_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('order_type', 'id');
			return $orderTypes;

		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	public static function getPaymentTerms()
	{
		try
		{
			$serviceId = Session::get('service_id');

			if($serviceId == ROAD_TRUCK_LEASE)
				$payment_terms = DB::table('lkp_payment_modes')->orderBy('payment_mode', 'asc' )->where('is_active','=',IS_ACTIVE)->whereNotIn('id',array(2,3))->lists('payment_mode', 'id');
			else if($serviceId == AIR_INTERNATIONAL || $serviceId==OCEAN)
				$payment_terms = DB::table('lkp_payment_modes')->orderBy('payment_mode', 'asc' )->where('is_active','=',IS_ACTIVE)->whereNotIn('id',array(2))->lists('payment_mode', 'id');
			else if($serviceId == ROAD_TRUCK_HAUL)
				$payment_terms = DB::table('lkp_payment_modes')->orderBy('payment_mode', 'asc' )->where('is_active','=',IS_ACTIVE)->whereNotIn('id',array(2,3,4))->lists('payment_mode', 'id');
			else
				$payment_terms = DB::table('lkp_payment_modes')->orderBy('payment_mode', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('payment_mode', 'id');			
			return $payment_terms;

		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	
	
	/** Retrieval of all Load Types**/
	public static function getAllLoadTypes()
	{
		try
		{
                    $serviceId = Session::get ( 'service_id' );
                    if($serviceId==ROAD_INTRACITY){
                        $getAllLoadTypes = DB::table('lkp_load_types')->orderBy('load_type', 'asc' )->where('is_intracity','=',IS_ACTIVE)->lists('load_type', 'id');
                    }else{
			$getAllLoadTypes = DB::table('lkp_load_types')->orderBy('load_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('load_type', 'id');
                    }
			return $getAllLoadTypes;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of all Lease Types**/
	public static function getAllLeaseTypes()
	{
		try
		{
			$getAllleaseTypes = DB::table('lkp_trucklease_lease_terms')->orderBy('lease_term', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('lease_term', 'id');
			return $getAllleaseTypes;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	//Get Lease name
	public static function getAllLeaseName($id)
	{
		try
		{
			$getAllleasename = DB::table('lkp_trucklease_lease_terms')->where('is_active','=',IS_ACTIVE)->where('id','=',$id)->select('lease_term')->first();
			
			return $getAllleasename->lease_term;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of all Shipment Types**/
	public static function getAllShipmentTypes()
	{
		try
		{
			$getAllShipmentTypes = DB::table('lkp_air_ocean_shipment_types')->orderBy('shipment_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('shipment_type', 'id');
			return $getAllShipmentTypes;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}/** Retrieval of all Sender Identity**/
	public static function getAllSenderIdentities()
	{
		try
		{
			$getAllSenderIdentities = DB::table('lkp_air_ocean_sender_identities')->orderBy('sender_identity', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('sender_identity', 'id');
			return $getAllSenderIdentities;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	public static function getAllVehicleType()
	{
		try
		{
                    $serviceId = Session::get ( 'service_id' );
                    if($serviceId==ROAD_INTRACITY){
                        $getAllVehicleTypes = DB::table('lkp_vehicle_types')->where('is_intracity','=',IS_ACTIVE)->lists('vehicle_type', 'id');
                    }else{
                        $getAllVehicleTypes = DB::table('lkp_vehicle_types')->where('is_active','=',IS_ACTIVE)->lists('vehicle_type', 'id');
                    }
			return $getAllVehicleTypes;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	public static function getAllVehicleTypeCapacity()
	{
		try
		{
			//echo DB::table('lkp_vehicle_types')->where('is_active','=',IS_ACTIVE)->select(DB::Raw("CONCAT( `capacity`,' ', `units`) as capcity"),"id")->tosql();die;
			$getAllVehicleTypecapaties = DB::table('lkp_vehicle_types')->where('is_active','=',IS_ACTIVE)->select(DB::Raw("CONCAT( `capacity`,' ', `units`) as capcityunits"),"id")->groupBy('capcityunits')->lists('capcityunits', 'id');
			return $getAllVehicleTypecapaties;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	//States
	public static function getAllStates()
	{
		try
		{
			$getAllStates= DB::table('lkp_states')->orderBy('state_name', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('state_name', 'id');
			return $getAllStates;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of all Packaging Types**/
	public static function getAllPackageTypes()
	{
		try
		{
			$getAllPackageTypes = DB::table('lkp_packaging_types')->where('is_active','=',IS_ACTIVE)->orderBy ( 'packaging_type_name', 'asc' )->lists('packaging_type_name', 'id');
			return $getAllPackageTypes;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of all Courier Types**/
	public static function getAllCourierTypes()
	{
		try
		{
			$getAllCourierTypes = DB::table('lkp_courier_types')->orderBy ( 'courier_type', 'asc' )->lists('courier_type', 'id');
			return $getAllCourierTypes;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of all Courier Types**/
	public static function getAllCourierDeliveryTypes()
	{
		try
		{
			$getAllCourierDeliveryTypes = DB::table('lkp_courier_delivery_types')->orderBy ( 'courier_delivery_type', 'asc' )->lists('courier_delivery_type', 'id');
			return $getAllCourierDeliveryTypes;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of all Vehicle Types**/
	public static function getAllVehicleTypes()
	{
		try
		{
                    $serviceId = Session::get ( 'service_id' );
                    if($serviceId==ROAD_INTRACITY){
                        $getAllVehicleTypes = DB::table('lkp_vehicle_types')->where('is_intracity','=',IS_ACTIVE)->orderBy ( 'id', 'asc' )->lists('vehicle_type', 'id');
                    }else{
			$getAllVehicleTypes = DB::table('lkp_vehicle_types')->where('is_active','=',IS_ACTIVE)->orderBy ( 'id', 'asc' )->lists('vehicle_type', 'id');
                    }
                        return $getAllVehicleTypes;
			
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Quote Access **/
	public static function getQuoteAccesses()
	{
		try
		{
			$quoteAccesses = DB::table('lkp_quote_accesses')->orderBy ( 'quote_access', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('quote_access', 'id');
			return $quoteAccesses;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Quote Access By Id**/
	public static function getQuoteAccessById($id)
	{
		try{
			$quoteAccess = DB::table('lkp_quote_accesses')->where('id','=',$id)->pluck('quote_access');;
			return $quoteAccess;
		}catch(\Exception $e){
			//return $e->message;
		}
	}

	/** Retrieval of all Packaging Types**/
	public static function getVolumeWeightTypes()
	{
		try
		{
			$volumeWeightTypes = DB::table('lkp_ptl_length_uom')->orderBy ( 'weight_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('weight_type', 'id');
			return $volumeWeightTypes;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}


	/** Retrieval of all Weight types**/
	public static function getUnitsWeight()
	{
		try
		{
			$unitsWeightTypes = DB::table('lkp_ict_weight_uom')->orderBy ( 'weight_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('weight_type', 'id');
			return $unitsWeightTypes;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Weight  **/
	public static function getWeight($id)
	{
		try
		{
			$getweight = DB::table('lkp_ict_weight_uom')
			->where('lkp_ict_weight_uom.id', '=',$id)
			->where('lkp_ict_weight_uom.is_active', '=','1')
			->select('lkp_ict_weight_uom.weight_type')
			->get();
	
			return $getweight[0]->weight_type;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	
	/** Retrieval of all Intracity Cities**/
	public static function getIntracityCities()
	{
		try
		{
			$getAllCities = DB::table ( 'lkp_cities' )->where('is_intracity','1')->where('is_active','=',IS_ACTIVE)->orderBy ( 'city_name', 'asc' )->lists ( 'city_name', 'id' );
			return $getAllCities;

		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}



	/** Retrieval of all Intracity Rate types**/
	public static function getIntracityRateTypes()
	{
		try
		{
			$getIntracityRateTypes = DB::table ( 'lkp_ict_rate_types' )->where('is_active','=',IS_ACTIVE)->orderBy ( 'rate_type', 'asc' )->lists ( 'rate_type', 'id' );
			return $getIntracityRateTypes;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of all Intracity UOM**/
	public static function getIntracityUOM()
	{
		try
		{
			$getIntracityUOM = DB::table ( 'lkp_ict_weight_uom' )->where('is_active','=',IS_ACTIVE)->orderBy ( 'weight_type', 'asc' )->lists ( 'weight_type', 'id' );

			return $getIntracityUOM;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/**
	 * Get Full name in one string using
	 * @param string $firstname
	 * @param string $middlename
	 * @param string $lastname
	 * @return string
	 */
	public static function getFullName($firstname, $lastname) {
		try {
			$fullName = $firstname;
			if(!empty($lastname)){
				$fullName .= ' '.$lastname;
			}
			return $fullName;
		} catch (Exception $exc) {
			//echo $exc->getTraceAsString();
			//TODO:: Log the error somewhere
		}
	}

	/**
	 * Get Buyer Intracity Posts List
	 *
	 */

	public static function getBuyerIntracityPosts(){

		try
		{
			$buyerIntracityPosts = DB::table ( 'ict_buyer_quotes as bqq' )
				->leftjoin('ict_buyer_quote_items as bqi ','bqq.id','=','bqi.buyer_quote_id')
				->where('bqi.is_cancelled','!=','1')
				->orderBy ( 'bqq.id', 'desc' )->select('bqq.transaction_id', 'bqq.id')->lists('bqq.transaction_id', 'bqq.id');


			return $buyerIntracityPosts;
		}
		catch(Exception $e)
		{
			//return $e->message;
		}

	}

	/**
	 * Get Intracity Vehicle List
	 *
	 */

	public static function getIntracityVehiclesList(){

		try
		{
			$intracityVehiclesList = DB::table ('lkp_ict_vehicles' )->orderBy ( 'id', 'desc' )->lists ('vehicle_number','id');

			return $intracityVehiclesList;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}

	}

	/** Retrieval of Intracity Orders  **/
	public static function getIntracityOrders()
	{
		try
		{
			$intracityOrdersList = DB::table('orders')->where('lkp_service_id',Session::get('service_id'))->orderBy ( 'id', 'desc' )->lists('order_no', 'id');
			return $intracityOrdersList;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/**
	 * Get PTL ZONEList
	 *
	 */

	public static function getPtlZonesList(){
		$userId = Auth::user()->id;

		try
		{
			$ptlZonesList = DB::table ('ptl_zones' )->where('seller_id',$userId)->where ( 'lkp_service_id', Session::get('service_id') )->orderBy ( 'zone_name', 'asc' )->lists ('zone_name','id');

			return $ptlZonesList;
		}
		catch(Exception $ex)
		{

		}

	}


	/**
	 * Get PTL TIER List
	 *
	 */

	public static function getPtlTiersList(){
		$userId = Auth::user()->id;

		try
		{
			$ptlTiersList = DB::table ('ptl_tiers' )->where('seller_id',$userId)->where ( 'lkp_service_id', Session::get('service_id') )->orderBy ( 'tier_name', 'asc' )->lists ('tier_name','id');

			return $ptlTiersList;
		}
		catch(Exception $ex)
		{

		}

	}

	/**
	 * Get PTL SECTOR List for auth user
	 *
	 */

	public static function getPtlSectorsList(){
		$userId = Auth::user()->id;

		try
		{
			$ptlSectorsList = DB::table ('ptl_sectors' )->where('seller_id',$userId)->where ( 'lkp_service_id', Session::get('service_id') )->where ( 'is_active', 1 )->orderBy ( 'sector_name', 'asc' )->lists ('sector_name','id');

			return $ptlSectorsList;
		}
		catch(Exception $ex)
		{

		}

	}


	/** Retrieval of Intracity Orders  **/
	public static function getHighestValue($numberArray) {
		try {
			return max($numberArray);
		} catch(\Exception $e) {
			//return $e->message;
		}
	}

	/** Retrieval of IView Count  **/
	public static function viewCountForSeller($buyerid,$sellepostid,$table) {
		try {
			if($table=="relocationoffice_seller_post_views" || $table=="relocationgm_seller_post_views"){
			$getviewcount = DB::table($table)
				->where($table.'.seller_post_id','=',$sellepostid)
				->select($table.'.id',$table.'.view_counts')
				->get();
			}else{	
			$getviewcount = DB::table($table)
				->where($table.'.seller_post_item_id','=',$sellepostid)
				->select($table.'.id',$table.'.view_counts')
				->get();
			}
			if(isset($getviewcount[0]->id) && isset($getviewcount[0]->id)!=''){
				if($table=="relocationoffice_seller_post_views" || $table=="relocationgm_seller_post_views"){
				$updateview = DB::table($table)
					->where($table.'.seller_post_id','=',$sellepostid)
					->update(array(
							'view_counts' =>$getviewcount[0]->view_counts+1));
				}else{
				$updateview = DB::table($table)
					->where($table.'.seller_post_item_id','=',$sellepostid)
					->update(array(
						'view_counts' =>$getviewcount[0]->view_counts+1));
				}

			}else{
                            
				$created_at  = date ( 'Y-m-d H:i:s' );
				$createdIp = $_SERVER['REMOTE_ADDR'];
				if($table == 'ptl_seller_post_item_views')
					$viewcount = new PtlSellerPostItemView();
				elseif($table == 'seller_post_item_views')
					$viewcount = new SellerPostItemView();
				elseif($table == 'rail_seller_post_item_views')
					$viewcount = new RailSellerPostItemView();
				elseif($table == 'airdom_seller_post_item_views')
					$viewcount = new AirdomSellerPostItemView();
				elseif($table == 'airint_seller_post_item_views')
					$viewcount = new AirintSellerPostItemView();
				elseif($table == 'ocean_seller_post_item_views')
					$viewcount = new OceanSellerPostItemView();
				elseif($table == 'seller_post_item_views')
					$viewcount = new SellerPostItemView();
                                elseif($table == 'courier_seller_post_item_views')
                                        $viewcount = new CourierSellerPostItemView();
                                elseif($table == 'trucklease_seller_post_item_views')
                                        $viewcount = new TruckleaseSellerPostItemView();
                                elseif($table == 'truckhaul_seller_post_item_views')
                                        $viewcount = new TruckhaulSellerPostItemView();
				elseif($table == 'relocation_seller_post_views')
					$viewcount = new RelocationSellerPostView();
                                elseif($table == 'relocationpet_seller_post_views')
					$viewcount = new RelocationpetSellerPostView();
				elseif($table == 'relocationoffice_seller_post_views')
                                        $viewcount = new RelocationofficeSellerPostView();
                                elseif($table == 'relocationint_seller_post_views')
                                        $viewcount = new RelocationintSellerPostView();
                                elseif($table == 'relocationgm_seller_post_views')
                                        $viewcount = new RelocationgmSellerPostView();
				else
					$viewcount = new PtlSellerPostItemView();
				
				
				$viewcount->user_id = $buyerid;
				if($table=="relocationoffice_seller_post_views" || $table=="relocationgm_seller_post_views"){
				$viewcount->seller_post_id= $sellepostid;
				}else{
				$viewcount->seller_post_item_id= $sellepostid;
				}
				$viewcount->view_counts =1;
				$viewcount->created_at  =$created_at;
				$viewcount->created_ip = $createdIp;
				$viewcount->save();
			}
			return $getviewcount[0]->view_counts;

		} catch(\Exception $e) {
			//return $e->message;
		}
	}

    /**
    * Retrieval of IView Count
    * @param type $sellerId
    * @param type $buyerQuoteItemId
    * @param type $table
    * @return type
    */
	public static function viewCountForBuyer($sellerId,$buyerQuoteItemId,$table) {
		try {
			$getviewcount = DB::table($table)
				->where($table.'.buyer_quote_item_id','=',$buyerQuoteItemId)
				->select($table.'.id',$table.'.view_counts')
				->get();

            if(isset($getviewcount[0]->id) && !empty($getviewcount[0]->id)) {
				$updateview = DB::table($table)
					->where($table.'.buyer_quote_item_id','=',$buyerQuoteItemId)
					->update(array(
						'view_counts' =>$getviewcount[0]->view_counts+1
                        ));
			}else{
				$created_at  = date ( 'Y-m-d H:i:s' );
				$createdIp = $_SERVER['REMOTE_ADDR'];
				if($table == 'ptl_buyer_quote_item_views')
					$viewcount = new PtlBuyerQuoteItemView();
				elseif($table == 'rail_buyer_quote_item_views')
					$viewcount = new RailBuyerQuoteItemView();
				elseif($table == 'airdom_buyer_quote_item_views')
					$viewcount = new AirdomBuyerQuoteItemView();
				elseif($table == 'airint_buyer_quote_item_views')
					$viewcount = new AirintBuyerQuoteItemView();
				elseif($table == 'ocean_buyer_quote_item_views')
					$viewcount = new OceanBuyerQuoteItemView();
				elseif($table == 'courier_buyer_quote_item_views')
					$viewcount = new CourierBuyerQuoteItemView();
                elseif($table == 'buyer_quote_item_views')
					$viewcount = new BuyerQuoteItemView();
				elseif($table == 'relocation_buyer_post_views')
					$viewcount = new RelocationBuyerPostView();
                elseif($table == 'relocationpet_buyer_post_views')
					$viewcount = new RelocationpetBuyerPostView();
				elseif($table == 'truckhaul_buyer_quote_item_views')
					$viewcount = new TruckhaulBuyerQuoteItemView();
				
				$viewcount->user_id = $sellerId;
				$viewcount->buyer_quote_item_id= $buyerQuoteItemId;
				$viewcount->view_counts = 1;
				$viewcount->created_at  =$created_at;
				$viewcount->created_ip = $createdIp;
                $viewcount->created_by = Auth::id();
				$viewcount->save();
			}
			return $getviewcount[0]->view_counts;
		} catch(\Exception $e) {
			//return $e->message;
		}
	}

    /**
    * Retrieval of Table name according to service
    * @param type $serviceId
    * @return type
    */
	public static function getTableNameAsPerService($serviceId) {
		try {
            if($serviceId == ROAD_PTL)
                $tableName = 'ptl_buyer_quote_item_views';
            elseif($serviceId == RAIL)
                $tableName = 'rail_buyer_quote_item_views';
            elseif($serviceId == AIR_DOMESTIC)
                $tableName = 'airdom_buyer_quote_item_views';
            elseif($serviceId == AIR_INTERNATIONAL)
                $tableName = 'airint_buyer_quote_item_views';
            elseif($serviceId == OCEAN)
                $tableName = 'ocean_buyer_quote_item_views';
            elseif($serviceId == COURIER)
                $tableName = 'courier_buyer_quote_item_views';
            elseif($serviceId == ROAD_FTL)
                $tableName = 'buyer_quote_item_views';
            elseif($serviceId == ROAD_TRUCK_HAUL)
                $tableName = 'truckhaul_buyer_quote_item_views';
            elseif($serviceId == ROAD_TRUCK_LEASE)
                $tableName = 'trucklease_buyer_quote_item_views';
            elseif($serviceId == RELOCATION_DOMESTIC)
                $tableName = 'relocation_buyer_post_views';
            elseif($serviceId == RELOCATION_OFFICE_MOVE)
                $tableName = 'relocationoffice_buyer_post_views';
            elseif($serviceId == RELOCATION_PET_MOVE)
                $tableName = 'relocationpet_buyer_post_views';
            elseif($serviceId == RELOCATION_INTERNATIONAL)
                $tableName = 'relocationint_buyer_post_views';
            elseif($serviceId == RELOCATION_GLOBAL_MOBILITY)
                $tableName = 'relocationgm_buyer_post_views';
            else
                $tableName = '';
			return $tableName;
		} catch(\Exception $e) {
			//return $e->message;
		}
	}
        
        
        public static function getSellerTableNameAsPerService($serviceId) {
		try {
            if($serviceId == ROAD_PTL)
                $tableName = 'ptl_seller_post_item_views';
            elseif($serviceId == RAIL)
                $tableName = 'rail_seller_post_item_views';
            elseif($serviceId == AIR_DOMESTIC)
                $tableName = 'airdom_seller_post_item_views';
            elseif($serviceId == AIR_INTERNATIONAL)
                $tableName = 'airint_seller_post_item_views';
            elseif($serviceId == OCEAN)
                $tableName = 'ocean_seller_post_item_views';
            elseif($serviceId == COURIER)
                $tableName = 'courier_seller_post_item_views';
            elseif($serviceId == ROAD_FTL)
                $tableName = 'seller_post_item_views';
            elseif($serviceId == ROAD_TRUCK_LEASE)
                $tableName = 'trucklease_seller_post_item_views';
            elseif($serviceId == ROAD_TRUCK_HAUL)
                $tableName = 'truckhaul_seller_post_item_views';
            elseif($serviceId == RELOCATION_DOMESTIC)
                $tableName = 'relocation_seller_post_views';
            elseif($serviceId == RELOCATION_PET_MOVE)
                $tableName = 'relocationpet_seller_post_views';
            elseif($serviceId == RELOCATION_OFFICE_MOVE)
                $tableName = 'relocationoffice_seller_post_views';
            elseif($serviceId == RELOCATION_INTERNATIONAL)
                $tableName = 'relocationint_seller_post_views';
            elseif($serviceId == RELOCATION_GLOBAL_MOBILITY)
                $tableName = 'relocationgm_seller_post_views';
            else
                $tableName = '';
			return $tableName;
		} catch(\Exception $e) {
			//return $e->message;
		}
	}


	public static function getPtlTransitdaysList(){

		$userId = Auth::user()->id;

		try
		{
			$ptlTransitList = DB::table ('ptl_transitdays as trans' )->leftjoin('ptl_tiers as pt','trans.from_tier_id','=','pt.id')->where('pt.seller_id',$userId)->get();

			return $ptlTransitList;
		}
		catch(Exception $ex)
		{

		}



	}

	public static function getMatrixTransitDays($tierId, $innerTierId){
		$transitdays= DB::table ( 'ptl_transitdays as pd' )->Where ( 'pd.from_tier_id', $tierId )->Where ( 'pd.to_tier_id', $innerTierId )->select ( 'no_days' )->first ();
		return $transitdays;
	}
	/**
	 * Convert gram to KG
	 * @param string $innerTierId
	 * @return string
	 */
	public static function convertGramToKG($unitInGram)
	{
		try{
			$unitInKg = number_format($unitInGram / 1000, 4);
			return $unitInKg;
		} catch (Exception $ex) {

		}
	}

	/** Retrieval of Location name  **/
	public static function getLocationName($cityId)
	{
		try
		{

			$getCityName = DB::table('lkp_ict_locations')
				->where('lkp_ict_locations.id', '=', $cityId)
				->where('is_active','=',IS_ACTIVE)
				->select('lkp_ict_locations.ict_location_name')
				->get();

			return $getCityName[0]->ict_location_name;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Service name  **/
	public static function getServiceName($serviceId)
	{
		try
		{

			$getServiceName = DB::table('lkp_services')
				->where('lkp_services.id', '=', $serviceId)
				->select('lkp_services.service_name')
				->get();

			return $getServiceName[0]->service_name;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Service name  **/
	public static function getServiceBreadCrumbName($serviceId)
	{
		try
		{

			$getServiceName = DB::table('lkp_services')
				->where('lkp_services.id', '=', $serviceId)
				->select('lkp_services.service_crumb_name')
				->get();

			return $getServiceName[0]->service_crumb_name;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Service Group ID  **/
	public static function getServiceGroupID($service_id)
	{
		try
		{
			$services = DB::table ( 'lkp_services' )->where (  'id', $service_id )
				->select ( 'lkp_services.lkp_invoice_service_group_id' )->get();
			return $services[0]->lkp_invoice_service_group_id;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Service name  **/
	public static function getServiceGroupName($serviceId)
	{
		try
		{
			$getGroupServiceName = DB::table('lkp_invoice_service_groups')
				->join('lkp_services','lkp_invoice_service_groups.id','=','lkp_services.lkp_invoice_service_group_id')
				->where('lkp_services.id', '=', $serviceId)
				->select('lkp_invoice_service_groups.invoice_service_group_name')
				->get();

			return $getGroupServiceName[0]->invoice_service_group_name;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/**
	 * Get trimed string after n number of characters
	 * @param string $string
	 * @param type $maxLength
	 * @return string
	 */
	public static function getTrimmedString($string,$maxLength)
	{
		try {
			if (strlen($string) > $maxLength) {
				$newString = substr($string, 0, $maxLength).'...';
				return $newString;
			}
			return $string;
		} catch (Exception $ex) {
			//return $e->message;
		}
	}

	/**
	 * if no search results found then sending mail to admin
	 *
	 * @return true
	 */
	public static function searchTermsSendMail() {
		$users = DB::table('users')->where('id', ADMIN)->get();
		$user = User::where('id', Auth::user()->id)->first();
		//print_r($user);exit;
		//$users->email = 'swathi.pakala@quadone.com';
		//echo "hrre".$_REQUEST['from_location_id'];exit;
		$serviceId = Session::get('service_id');
		
		$users[0]->load_type = "";
		$users[0]->vehicle_type = "";
		$users[0]->from = "";
		$users[0]->to = "";
		
		
		
		//echo "<pre>";print_r($_REQUEST);exit;

		switch ($serviceId) {
			case ROAD_FTL :
			case ROAD_TRUCK_HAUL :
			case ROAD_TRUCK_LEASE :
				if ((isset($_REQUEST['lkp_load_type_id']) && $_REQUEST['lkp_load_type_id'] != "") || (isset($_REQUEST['load_type']) && $_REQUEST['load_type'] != "") ) {
					if(isset($_REQUEST['load_type']) && $_REQUEST['load_type']!='')
						$_REQUEST['lkp_load_type_id']=$_REQUEST['load_type'];
					$load_type = DB::table('lkp_load_types')
					->where('lkp_load_types.id', '=', $_REQUEST['lkp_load_type_id'])
					->select('lkp_load_types.load_type')->first();
					$users[0]->load_type = $load_type->load_type;
				}
				
				if ((isset($_REQUEST['lkp_vehicle_type_id']) && $_REQUEST['lkp_vehicle_type_id'] != "")|| (isset($_REQUEST['lkp_vehicle_id']) && $_REQUEST['lkp_vehicle_id'] != "")) {
					if(isset($_REQUEST['lkp_vehicle_id']) && $_REQUEST['lkp_vehicle_id'] != "")
						$_REQUEST['lkp_vehicle_type_id']=$_REQUEST['lkp_vehicle_id'];
					$vehicle_type = DB::table('lkp_vehicle_types')
					->where('lkp_vehicle_types.id', '=', $_REQUEST['lkp_vehicle_type_id'])
					->select('lkp_vehicle_types.vehicle_type')->first();
					$users[0]->vehicle_type = $vehicle_type->vehicle_type;
				}
				$users[0]->dispatch = $_REQUEST['from_date'];
				if (isset($_REQUEST['from_location_id']) && $_REQUEST['from_location_id'] != "") {
					$from = CommonComponent::getCityName($_REQUEST['from_location_id']);
					$users[0]->from = $from;
				}

				if (isset($_REQUEST['to_location_id']) && $_REQUEST['to_location_id'] != "") {
					$to = CommonComponent::getCityName($_REQUEST['to_location_id']);

					$users[0]->to = $to;
				}
				break;
			case ROAD_PTL :
			case RAIL :
			case AIR_DOMESTIC :
			case AIR_INTERNATIONAL :
			case OCEAN :
			case COURIER :
				if ((isset($_REQUEST['ptlLoadType'][0]) && $_REQUEST['ptlLoadType'][0] != "")) {
					
					$load_type = DB::table('lkp_load_types')
					->where('lkp_load_types.id', '=', $_REQUEST['ptlLoadType'][0])
					->select('lkp_load_types.load_type')->first();
					$users[0]->load_type = $load_type->load_type;
				}
				
				if ((isset($_REQUEST['ptlPackageType'][0]) && $_REQUEST['ptlPackageType'][0] != "")) {
					$vehicle_type = DB::table('lkp_packaging_types')
					->where('lkp_packaging_types.id', '=', $_REQUEST['ptlPackageType'][0])
					->select('lkp_packaging_types.packaging_type_name')->first();
					$users[0]->vehicle_type = $vehicle_type->packaging_type_name;
				}
				
				if(isset($_REQUEST['ptlDispatchDate'][0]) && $_REQUEST['ptlDispatchDate'][0]!="")
					$users[0]->dispatch = $_REQUEST['ptlDispatchDate'][0];
				elseif(isset($_REQUEST['ptlDispatchDate']) && $_REQUEST['ptlDispatchDate']!="")
					$users[0]->dispatch = $_REQUEST['ptlDispatchDate'];
				if (isset($_REQUEST['sea_ptlFromLocation']) && $_REQUEST['sea_ptlFromLocation'] != "") {
					$from = CommonComponent::getPinName($_REQUEST['sea_ptlFromLocation']);
					$users[0]->from = $from;
				}
				if($serviceId==COURIER){
					
					if (isset($_REQUEST['post_delivery_types']) && $_REQUEST['post_delivery_types'][0] == 2) {
						if (isset($_REQUEST['ptlToLocation']) && $_REQUEST['ptlToLocation'] != "") {
							$to = CommonComponent::getCountry($_REQUEST['ptlToLocation']);
							$users[0]->to = $to;
						}
					}else{
						if (isset($_REQUEST['ptlToLocation']) && $_REQUEST['ptlToLocation'] != "") {
							$to = CommonComponent::getPinName($_REQUEST['ptlToLocation']);
							$users[0]->to = $to;
						}
					}
				}
				else{
					if (isset($_REQUEST['sea_ptlToLocation']) && $_REQUEST['sea_ptlToLocation'] != "") {
						$to = CommonComponent::getPinName($_REQUEST['sea_ptlToLocation']);
						$users[0]->to = $to;
					}
				}
				break;
                        case ROAD_INTRACITY :
				$users[0]->dispatch = $_REQUEST['pickup_date'];
				if (isset($_REQUEST['from_location_id']) && $_REQUEST['from_location_id'] != "") {
					$from = CommonComponent::getLocationName($_REQUEST['from_location_id']);
					$users[0]->from = $from;
				}
				if (isset($_REQUEST['to_location_id']) && $_REQUEST['to_location_id'] != "") {
					$to = CommonComponent::getLocationName($_REQUEST['to_location_id']);
					$users[0]->to = $to;
				}
                                if ((isset($_REQUEST['lkp_load_type_id']) && $_REQUEST['lkp_load_type_id'] != "") || (isset($_REQUEST['load_type']) && $_REQUEST['load_type'] != "") ) {
					if(isset($_REQUEST['load_type']) && $_REQUEST['load_type']!='')
						$_REQUEST['lkp_load_type_id']=$_REQUEST['load_type'];
					$load_type = DB::table('lkp_load_types')
					->where('lkp_load_types.id', '=', $_REQUEST['lkp_load_type_id'])
					->select('lkp_load_types.load_type')->first();
					$users[0]->load_type = $load_type->load_type;
				}
				
				if ((isset($_REQUEST['lkp_vehicle_type_id']) && $_REQUEST['lkp_vehicle_type_id'] != "")|| (isset($_REQUEST['lkp_vehicle_id']) && $_REQUEST['lkp_vehicle_id'] != "")) {
					if(isset($_REQUEST['lkp_vehicle_id']) && $_REQUEST['lkp_vehicle_id'] != "")
						$_REQUEST['lkp_vehicle_type_id']=$_REQUEST['lkp_vehicle_id'];
					$vehicle_type = DB::table('lkp_vehicle_types')
					->where('lkp_vehicle_types.id', '=', $_REQUEST['lkp_vehicle_type_id'])
					->select('lkp_vehicle_types.vehicle_type')->first();
					$users[0]->vehicle_type = $vehicle_type->vehicle_type;
				}
				break;
			case RELOCATION_DOMESTIC :
					$users[0]->dispatch = $_REQUEST['from_date'];
					if (isset($_REQUEST['from_location_id']) && $_REQUEST['from_location_id'] != "") {
						$from = CommonComponent::getCityName($_REQUEST['from_location_id']);
						$users[0]->from = $from;
					}
				
					if (isset($_REQUEST['to_location_id']) && $_REQUEST['to_location_id'] != "") {
						$to = CommonComponent::getCityName($_REQUEST['to_location_id']);
				
						$users[0]->to = $to;
					}
                                        //load type for RD added by Swathi 02-05-2016
                                        if ((isset($_REQUEST['load_type']) && $_REQUEST['load_type'] != "") ) {
					
                                            $load_type = DB::table('lkp_load_categories')
                                            ->where('lkp_load_categories.id', '=', $_REQUEST['load_type'])
                                            ->select('lkp_load_categories.load_category')->first();
                                            $users[0]->load_type = $load_type->load_category;
                                        }
                                        //end load type for RD added by Swathi 02-05-2016
					break;
                        case RELOCATION_GLOBAL_MOBILITY :
					$users[0]->dispatch = $_REQUEST['from_date'];
					
					if (isset($_REQUEST['to_location_id']) && $_REQUEST['to_location_id'] != "") {
						$to = CommonComponent::getCityName($_REQUEST['to_location_id']);
				
						$users[0]->to = $to;
					}
                                        if ((isset($_REQUEST['relgm_service_type']) && $_REQUEST['relgm_service_type'] != "") ) {
					
                                            
                                            $users[0]->load_type = CommonComponent::getAllGMServiceTypesById($_REQUEST['relgm_service_type']);
                                        }
                                       
					break;                
			default :
				$users[0]->dispatch = $_REQUEST['from_date'];
				break;
		}
		$users[0]->user = $user['username'];
	
		if($serviceId ==  ROAD_FTL || $serviceId == ROAD_TRUCK_HAUL){
			CommonComponent::send_email(FTL_SEARCH_KEYWORDS, $users);
		}
		elseif($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC  || $serviceId == COURIER){
			CommonComponent::send_email(PTL_SEARCH_KEYWORDS, $users);
		}elseif($serviceId == AIR_INTERNATIONAL ){
			CommonComponent::send_email(AIRINTERNATIONAL_SEARCH_KEYWORDS, $users);
		}elseif($serviceId == OCEAN){
			CommonComponent::send_email(OCEAN_SEARCH_KEYWORDS, $users);
		}
		elseif($serviceId == ROAD_TRUCK_LEASE){
			CommonComponent::send_email(TRUCKLEASE_SEARCH_KEYWORDS, $users);
		}elseif($serviceId == RELOCATION_DOMESTIC || RELOCATION_GLOBAL_MOBILITY){
			CommonComponent::send_email(FTL_SEARCH_KEYWORDS, $users);
		}
		else{ 
			CommonComponent::send_email(FTL_SEARCH_KEYWORDS, $users);
		}
				
	}

	/** Capturing the search terms in the database based on the service * */
	public static function saveSearchTerms($request, $serviceId) {

		//insert buyer search terms for sellers.
		if (!empty($request) && $serviceId == ROAD_FTL) {
			$allVariables = Input::all();
			$sellerSearchTerms = new FtlSearchTerm();
			$sellerSearchTerms->user_id = Auth::User()->id;
			$sellerSearchTerms->from_city_id = $request->from_location_id;
			$sellerSearchTerms->to_city_id = $request->to_location_id;
			$sellerSearchTerms->dispatch_date = $request->from_date;
			if (isset($_REQUEST['to_date']) && $_REQUEST['to_date'] != "") {
				$sellerSearchTerms->delivery_date = $request->to_date;
			}
			$sellerSearchTerms->lkp_load_type_id = $request->lkp_load_type_id;
			$sellerSearchTerms->lkp_vehicle_type_id = $request->lkp_vehicle_type_id;
			$sellerSearchTerms->created_at = date('Y-m-d H:i:s');
			$sellerSearchTerms->created_ip = $_SERVER['REMOTE_ADDR'];
			$sellerSearchTerms->created_by = Auth::User()->id;
			$sellerSearchTerms->save();

			//Storing(putting) values in session variables for post creation

			Session::put('buyerSessionFromLocationId', $request->from_location_id);
			Session::put('buyerSessionToLocationId', $request->to_location_id);
			Session::put('buyerSessionFromLocationName', $request->from_location);
			Session::put('buyerSessionToLocationName', $request->to_location);
			Session::put('buyerSessionFromDate', $request->from_date);
			Session::put('buyerSessionToDate', $request->to_date);
			Session::put('buyerSessionLoadTypeId', $request->lkp_load_type_id);
			Session::put('buyerSessionVehicleTypeId', $request->lkp_vehicle_type_id);
			Session::put('buyerSessioncapacity', $request->capacity);
			Session::put('buyerSessionqty', $request->quantity);
			Session::put('buyerSessionNoofloads', $request->no_of_loads);



			//Session::get('seller_post_item');
		} elseif (!empty($request) && $serviceId == ROAD_INTRACITY) {
			if (isset($request["lkp_city_id"])) {
				$sellerSearchTerm = new IctSearchTerm();
				$sellerSearchTerm->user_id = Auth::User()->id;
				$sellerSearchTerm->from_city_id = $request["lkp_city_id"];
				$sellerSearchTerm->from_location_id = $request['from_location_id'];
				$sellerSearchTerm->to_location_id = $request['to_location_id'];
				$sellerSearchTerm->rate_type = $request['rate_type'];
				$sellerSearchTerm->dispatch_date = $request['pickup_date'];
				$sellerSearchTerm->dispatch_time = $request['pickup_time'];
				if(isset($request['load_type']))
					$sellerSearchTerm->lkp_load_type_id = $request['load_type'];
				if(isset($request['lkp_vehicle_id']))
					$sellerSearchTerm->lkp_vehicle_type_id = $request['lkp_vehicle_id'];
				$sellerSearchTerm->weight = $request['weight'];
				$sellerSearchTerm->weight_type = $request['weight_type'];
				$sellerSearchTerm->created_at = date('Y-m-d H:i:s');
				$sellerSearchTerm->created_ip = $_SERVER['REMOTE_ADDR'];
				$sellerSearchTerm->created_by = Auth::User()->id;
				$sellerSearchTerm->save();

				//Storing(putting) values in session variables for post creation
                                Session::put('buyerSessionDistrictId', $request['seller_district_id']);
				Session::put('buyerSessionFromcityId', $request['lkp_city_id']);
				Session::put('buyerSessionFromcityLocation', $request['intra_from_location']);
				Session::put('buyerSessionRateType', $request['rate_type']);
				Session::put('buyerSessionFromLocationId', $request['from_location_id']);
				Session::put('buyerSessionToLocationId', $request['to_location_id']);
				Session::put('buyerSessionFromLocationName', $request['from_location']);
				Session::put('buyerSessionToLocationName', $request['to_location']);
				Session::put('buyerSessionFromDate', $request['pickup_date']);
				Session::put('buyerSessionFromTime', $request['pickup_time']);
				if(isset($request['load_type']))
					Session::put('buyerSessionLoadTypeId', $request['load_type']);
				if(isset($request['lkp_vehicle_id']))
					Session::put('buyerSessionVehicleTypeId', $request['lkp_vehicle_id']);
				Session::put('buyerSessionweight', $request['weight']);
				Session::put('buyerSessionweightType', $request['weight_type']);
				return;
				//Session::get('seller_post_item');
			}
		}
	}

	/** Retrieval of Seller Post Details  **/
	public static function getSellerPostDetails($postId)
	{
		try
		{
			$getCityName = DB::table('seller_post_items')
                                ->where('seller_post_items.id', '=', $postId)
				->select('seller_post_items.transitdays','seller_post_items.price','seller_post_items.lkp_vehicle_type_id')
				->get();

			return $getCityName[0];
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
        /** Retrieval of TH Seller Post Details  **/
	public static function getTHSellerPostDetails($postId)
	{
		try
		{
			$getCityName = DB::table('truckhaul_seller_post_items as spi')
                                ->where('spi.id', '=', $postId)
				->select('spi.transitdays','spi.price','spi.lkp_vehicle_type_id')
				->get();

			return $getCityName[0];
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
        /** Retrieval of TH Seller Post Details  **/
	public static function getTLSellerPostDetails($postId)
	{
		try
		{
			$getCityName = DB::table('trucklease_seller_post_items as spi')
                                ->where('spi.id', '=', $postId)
				->select('spi.price','spi.lkp_vehicle_type_id')
				->get();

			return $getCityName[0];
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Seller Post Status  **/
	public static function getSellerPostStatuss($postId)
	{
		try
		{
			$getCityName = DB::table('lkp_post_statuses')
				->where('lkp_post_statuses.id', '=', $postId)
				->select('lkp_post_statuses.post_status')
				->get();

			return $getCityName[0]->post_status;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of Lease term  **/
	public static function getSellerLeaseTerm($id)
	{
		try
		{
			$getlease = DB::table('lkp_trucklease_lease_terms')
			->where('lkp_trucklease_lease_terms.id', '=', $id)
			->select('lkp_trucklease_lease_terms.lease_term')
			->get();
	
			return $getlease[0]->lease_term;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	
	/** Retrieval of Seller Post Details  **/
	public static function getPtlSellerPostDetails($postId)
	{
		try
		{
			$serviceId = Session::get('service_id');
			switch($serviceId){
				case ROAD_PTL:
					$getCityName = DB::table('ptl_seller_post_items as spi')
						->where('spi.id', '=', $postId)
						->select('spi.transitdays','spi.price')
						->get();
					break;
				case RAIL:
					$getCityName = DB::table('rail_seller_post_items as spi')
						->where('spi.id', '=', $postId)
						->select('spi.transitdays','spi.price')
						->get();
					break;
				case AIR_DOMESTIC:
					$getCityName = DB::table('airdom_seller_post_items as spi')
						->where('spi.id', '=', $postId)
						->select('spi.transitdays','spi.price')
						->get();
					break;
				case AIR_INTERNATIONAL:
					$getCityName = DB::table('airint_seller_post_items as spi')
						->where('spi.id', '=', $postId)
						->select('spi.transitdays','spi.price')
						->get();
					break;
				case OCEAN:
					$getCityName = DB::table('ocean_seller_post_items as spi')
						->where('spi.id', '=', $postId)
						->select('spi.transitdays','spi.price')
						->get();
					break;
				case COURIER:
					$getCityName = DB::table('courier_seller_post_items as spi')
						->where('spi.id', '=', $postId)
						->select('spi.transitdays','spi.price')
						->get();
					break;
                                case RELOCATION_DOMESTIC:
					$getCityName = DB::table('relocation_seller_posts as sp')
						->leftjoin('relocation_seller_post_items as spi','sp.id','=','spi.seller_post_id')
                                                ->where('sp.id', '=', $postId)
						->select('spi.transitdays')
						->get();
					break; 
				case RELOCATION_INTERNATIONAL:
					if(Session::get('session_service_type_buyer') == INTERNATIONAL_TYPE_AIR){
						$getCityName = DB::table('relocationint_seller_posts as sp')
	                        ->where('sp.id', '=', $postId)
							->select('sp.transitdays')
							->get();
					}else if(Session::get('session_service_type_buyer') == INTERNATIONAL_TYPE_OCEAN){
						$getCityName = DB::table('relocationint_seller_posts as sp')
							->leftjoin('relocationint_seller_post_items as spi','sp.id','=','spi.seller_post_id')
	                        ->where('sp.id', '=', $postId)
							->select('spi.transitdays')
							->get();
					}
					break;   
				default:
					$getCityName = DB::table('ptl_seller_post_items as spi')
						->where('spi.id', '=', $postId)
						->select('spi.transitdays','spi.price')
						->get();
					break;
			}
			return $getCityName[0];
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Max order id  **/
	public static function getOrderID()
	{
		try
		{
			$order = DB::table ( 'orders' )->select('id')->orderBy('id','desc')->first();
			if(!empty($order))
				return $order->id+1;
			else
				return 1;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of Max post id  **/
	public static function getPostID($serviceId)
	{
		try
		{
			switch($serviceId){
				case ROAD_FTL       :
					
					$post = DB::table ( 'buyer_quotes' )->select('id')->orderBy('id','desc')->first();
					break;
				case ROAD_PTL       :
					$post = DB::table ( 'ptl_buyer_quotes' )->select('id')->orderBy('id','desc')->first();
					break;
				case RAIL       :
					$post = DB::table ( 'rail_buyer_quotes' )->select('id')->orderBy('id','desc')->first();
					break;
				case AIR_DOMESTIC       :
					$post = DB::table ( 'airdom_buyer_quotes' )->select('id')->orderBy('id','desc')->first();
					break;
				case AIR_INTERNATIONAL       :
					$post = DB::table ( 'airint_buyer_quotes' )->select('id')->orderBy('id','desc')->first();
					break;
				case OCEAN       :
					$post = DB::table ( 'ocean_buyer_quotes' )->select('id')->orderBy('id','desc')->first();
					break;
				case ROAD_INTRACITY       :
					$post = DB::table ( 'ict_buyer_quotes' )->select('id')->orderBy('id','desc')->first();
					break;
				case COURIER       :
					$post = DB::table ( 'courier_buyer_quotes' )->select('id')->orderBy('id','desc')->first();
					break;
                 case RELOCATION_DOMESTIC       :
					$post = DB::table ( 'relocation_buyer_posts' )->select('id')->orderBy('id','desc')->first();
					break;    
                 case ROAD_TRUCK_HAUL       :
					$post = DB::table ( 'truckhaul_buyer_quotes' )->select('id')->orderBy('id','desc')->first();
					break; 

				case ROAD_TRUCK_HAUL       :
					$post = DB::table ( 'trucklease_buyer_quotes' )->select('id')->orderBy('id','desc')->first();
					break;
				case RELOCATION_OFFICE_MOVE:
						$post = DB::table ( 'relocationoffice_buyer_posts' )->select('id')->orderBy('id','desc')->first();
					break;	
                                case RELOCATION_PET_MOVE:
						$post = DB::table ( 'relocationpet_buyer_posts' )->select('id')->orderBy('id','desc')->first();
					break;	
                                case RELOCATION_INTERNATIONAL:
						$post = DB::table ( 'relocationint_buyer_posts' )->select('id')->orderBy('id','desc')->first();
					break;	    
                                case RELOCATION_GLOBAL_MOBILITY:
						$post = DB::table ( 'relocationgm_buyer_posts' )->select('id')->orderBy('id','desc')->first();
					break;	        
				default       :
					$post = DB::table ( 'ptl_buyer_quotes' )->select('id')->orderBy('id','desc')->first();
					break;
			}
			if(!empty($post))
				return $post->id+1;
			else
				return 1;

		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of Max post id  **/
	public static function getTermPostID()
	{
		try
		{
			$post = DB::table ( 'term_buyer_quotes' )->select('id')->orderBy('id','desc')->first();
				
			if(!empty($post))
				return $post->id+1;
			else
				return 1;
	
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of Buyer Post Details  **/
	public static function getBuyerPostDetails($postId,$serviceId)
	{
		try
		{
			switch($serviceId){
				case ROAD_PTL       :
					$transactionID = DB::table('ptl_buyer_quotes')
						->where('ptl_buyer_quotes.id', '=', $postId)
						->select('ptl_buyer_quotes.transaction_id')
						->first();
					break;
				case RAIL       :
					$transactionID = DB::table('rail_buyer_quotes')
						->where('rail_buyer_quotes.id', '=', $postId)
						->select('rail_buyer_quotes.transaction_id')
						->first();
					break;
				case AIR_DOMESTIC       :
					$transactionID = DB::table('airdom_buyer_quotes')
						->where('airdom_buyer_quotes.id', '=', $postId)
						->select('airdom_buyer_quotes.transaction_id')
						->first();
					break;
				case AIR_INTERNATIONAL       :
					$transactionID = DB::table('airint_buyer_quotes')
						->where('airint_buyer_quotes.id', '=', $postId)
						->select('airint_buyer_quotes.transaction_id')
						->first();
					break;
				case OCEAN       :
					$transactionID = DB::table('ocean_buyer_quotes')
						->where('ocean_buyer_quotes.id', '=', $postId)
						->select('ocean_buyer_quotes.transaction_id')
						->first();
					break;
				case COURIER       :
					$transactionID = DB::table('courier_buyer_quotes')
						->where('courier_buyer_quotes.id', '=', $postId)
						->select('courier_buyer_quotes.transaction_id')
						->first();
						break;
				case ROAD_PTL       :
					$transactionID = DB::table('ptl_buyer_quotes')
						->where('ptl_buyer_quotes.id', '=', $postId)
						->select('ptl_buyer_quotes.transaction_id')
						->first();
					break;
			}
			return $transactionID->transaction_id;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of Seller Post Payment id  **/
	public static function getSellerPostPaymentMethod($paymentId)
	{
		try
		{
			$seller_post_items_lkp_payment_mode_id  = DB::table('lkp_payment_modes')
				->where('id',$paymentId)
				->select('payment_mode')
				->get();

			return $seller_post_items_lkp_payment_mode_id[0]->payment_mode;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Max post id  **/
	public static function getSellerPostID($serviceId)
	{
		
		try
		{
			switch($serviceId){
				case ROAD_FTL       :
					$post = DB::table ( 'seller_posts' )->select('id')->orderBy('id','desc')->first();
					break;
				case ROAD_PTL       :
					$post = DB::table ( 'ptl_seller_posts' )->select('id')->orderBy('id','desc')->first();
					break;
				case RAIL       :
					$post = DB::table ( 'rail_seller_posts' )->select('id')->orderBy('id','desc')->first();
					break;
				case AIR_DOMESTIC       :
					$post = DB::table ( 'airdom_seller_posts' )->select('id')->orderBy('id','desc')->first();
					break;
				case AIR_INTERNATIONAL       :
					$post = DB::table ( 'airint_seller_posts' )->select('id')->orderBy('id','desc')->first();
					break;
				case OCEAN       :
					$post = DB::table ( 'ocean_seller_posts' )->select('id')->orderBy('id','desc')->first();
					break;
				case COURIER       :
					$post = DB::table ( 'courier_seller_posts' )->select('id')->orderBy('id','desc')->first();
					break;
				case RELOCATION_DOMESTIC       :
					$post = DB::table ( 'relocation_seller_posts' )->select('id')->orderBy('id','desc')->first();
						break;
				case RELOCATION_INTERNATIONAL       :
					$post = DB::table ( 'relocationint_seller_posts' )->select('id')->orderBy('id','desc')->first();
					break;
				case ROAD_TRUCK_LEASE :
					$post = DB::table ( 'trucklease_seller_posts' )->select('id')->orderBy('id','desc')->first();
					
					break;
				case ROAD_TRUCK_HAUL       :
					$post = DB::table ( 'truckhaul_seller_posts' )->select('id')->orderBy('id','desc')->first();
					break;
				case RELOCATION_OFFICE_MOVE       :
						$post = DB::table ( 'relocationoffice_seller_posts' )->select('id')->orderBy('id','desc')->first();
						break;
                                case RELOCATION_PET_MOVE       :
					$post = DB::table ( 'relocationpet_seller_posts' )->select('id')->orderBy('id','desc')->first();
						break;   
                                case RELOCATION_GLOBAL_MOBILITY       :
					$post = DB::table ( 'relocationgm_seller_posts' )->select('id')->orderBy('id','desc')->first();
						break;               
				default       :
					$post = DB::table ( 'ptl_seller_posts' )->select('id')->orderBy('id','desc')->first();
				break;
			}
			if(!empty($post))
				return $post->id+1;
			else
				return 1;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Max inv id  **/
	public static function getInvID()
	{
		try
		{
			$post = DB::table ( 'order_invoices' )->select('id')->orderBy('id','desc')->first();
			if(!empty($post))
				return $post->id+1;
			else
				return 1;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of Max Seller inv id  **/
	public static function getSellerInvID()
	{
		try
		{
			$post = DB::table ( 'seller_order_invoices' )->select('id')->orderBy('id','desc')->first();
			if(!empty($post))
				return $post->id+1;
			else
				return 1;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of Max Seller Receipt id  **/
	public static function getSellerReceiptID()
	{
		try
		{
			$post = DB::table ( 'order_receipts' )->select('id')->orderBy('id','desc')->first();
			if(!empty($post))
				return $post->id+1;
			else
				return 1;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of Airport name  **/
	public static function getAirportName($pin)
	{
		try
		{

			$getZonecityName = DB::table('lkp_airports')
				->where('lkp_airports.id', '=', $pin)
				->select('lkp_airports.airport_name')
				->get();

			return $getZonecityName[0]->airport_name;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	public static function getAirportId($airport)
	{
		try
		{

			$getZonecityName = DB::table('lkp_airports')
				->where('lkp_airports.airport_name', 'LIKE', '%'.$airport.'%')
				->select('lkp_airports.id')
				->get();

			return $getZonecityName[0]->id;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	public static function getOceanId($ocean)
	{
		try
		{

			$getZonecityName = DB::table('lkp_seaports')
				->where('lkp_seaports.seaport_name', 'LIKE', '%'.$ocean.'%')
				->select('lkp_seaports.id')
				->get();

			return $getZonecityName[0]->id;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of Seaport name  **/
	public static function getSeaportName($pin)
	{
		try
		{

			$getZonecityName = DB::table('lkp_seaports')
				->where('lkp_seaports.id', '=', $pin)->where('is_active','=',IS_ACTIVE)
				->select('lkp_seaports.seaport_name')
				->get();

			return $getZonecityName[0]->seaport_name;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of ShipmentTypes  **/
	public static function getShipmentTypes()
	{
		try
		{
			$shipment_types = DB::table('lkp_air_ocean_shipment_types')->orderBy('shipment_type','asc')->where('is_active','=',IS_ACTIVE)->lists('shipment_type', 'id');
			return $shipment_types;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of getSlabs  **/
	public static function getSlabs($quoteid,$id)
	{
		try
		{
			
			$pricelabs = DB::table ( 'term_buyer_quote_slabs' )
			->where ( 'buyer_quote_id', $quoteid )
			->where ( 'buyer_id', $id )
			->get ();
			
			return $pricelabs;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	/** Retrieval of getQuotePrices  **/
	public static function getQuotePriceDetails($quoteid,$id)
	{
		try
		{
				
			$price = DB::table ( 'term_buyer_quote_sellers_quotes_prices' )
			->where ( 'term_buyer_quote_id', $quoteid )
			->where ( 'buyer_id', $id )
			->where ( 'seller_id', Auth::user ()->id )
			->get ();
				
			return $price;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Retrieval of getQuotePrices  **/
	public static function getQuoteAddtionalDetails($quoteid,$sid)
	{
		try
		{
	
			$price = DB::table ( 'term_buyer_quote_sellers_quotes_prices' )
			->where ( 'term_buyer_quote_id', $quoteid )
			->where ( 'buyer_id', Auth::user ()->id )
			->where ( 'seller_id', $sid )
			->get ();
	
			return $price;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Retrieval of getSlabPrices  **/
	public static function getQuotePriceDetailsSlabs($quoteid,$id)
	{
		try
		{
	
			$pricelabs = DB::table ( 'term_buyer_quote_sellers_quotes_price_slabs' )
			->where ( 'term_buyer_quote_id', $quoteid )
			->where ( 'buyer_id', $id )
			->get ();
	
			return $pricelabs;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	
	/** Retrieval of getSlabPricesSaved  **/
	public static function getQuotePriceDetailsSlabsSaved($quoteid,$id,$min,$max)
	{
		try
		{
	
			$pricelabs = DB::table ( 'term_buyer_quote_sellers_quotes_price_slabs' )
			->where ( 'term_buyer_quote_id', $quoteid )
			->where ( 'slab_min_rate', $min )
			->where ( 'slab_max_rate', $max )
			->where ( 'buyer_id', $id )
			->get ();
	
			return $pricelabs;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Retrieval of getMaxWeightUnits  **/
	public static function getMaxWeightUnits($quoteid,$id)
	{
		try
		{
				
			$maxweight = DB::table ( 'term_buyer_quotes' )
			->where ( 'id', $quoteid )
			->where ( 'created_by', $id )
			->get();
				
			return $maxweight;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Retrieval of Sender Identity  **/
	public static function getSenderIdentity()
	{
		try
		{
			$sender_identities = DB::table('lkp_air_ocean_sender_identities')->orderBy ( 'sender_identity', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('sender_identity', 'id');
			return $sender_identities;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of District ID  **/
	public static function getDistrictid($id)
	{
		try
		{
			$getdisid = DB::table('lkp_ptl_pincodes')
				->where('lkp_ptl_pincodes.id', '=',$id)
				->select('lkp_ptl_pincodes.lkp_district_id')
				->get();

			return $getdisid[0]->lkp_district_id;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/**
	 * Get District
	 * from location,service, zone or location
	 */
	public static function getDistrict($location,$serviceId,$type = 2){
		switch($serviceId){
			case ROAD_FTL       :
                        case ROAD_TRUCK_HAUL:  
                        case ROAD_TRUCK_LEASE: 
                        case RELOCATION_DOMESTIC:
			case RELOCATION_OFFICE_MOVE:
                        case RELOCATION_PET_MOVE:
                        case RELOCATION_INTERNATIONAL:
                        case RELOCATION_GLOBAL_MOBILITY:
				return DB::table('lkp_cities')->where('id','=',$location)->pluck('lkp_district_id');
				break;
			case ROAD_PTL       :
			case RAIL:
			case AIR_DOMESTIC:
			case COURIER:
				if($type == 2){
					return DB::table('lkp_ptl_pincodes')->where('id','=',$location)->pluck('lkp_district_id');
				}else{
					$locations = SellerSearchComponent::getPincodesByZoneId($location);
					return DB::table('lkp_ptl_pincodes')->whereIn('id',$locations)->groupBy('lkp_district_id')->lists('lkp_district_id');
				}

				break;
			case AIR_INTERNATIONAL:
			case OCEAN:
				//coming soon
				break;

			case ROAD_INTRACITY :
				//coming soon
				break;
			
			default             :
				break;
		}
	}

        
        /** Retrieval of Max msg id  **/
	public static function getMessageID()
	{
            try
            {
                $post = DB::table ( 'user_messages' )->select('id')->orderBy('id','desc')->first();
                if(!empty($post))    
                    return $post->id+1;
                else
                    return 1; 
            }
            catch(\Exception $e)
            {
              //return $e->message;
            }
        }
        
        /**cronjob to update  Post status  **/
        public static function updatePostStatus()
        {
            try{
                //ftl for buyer posts deleting
                $ftlGetItems = DB::table('buyer_quote_items as bqi')
				->where('lkp_post_status_id','=',OPEN)
                                ->whereRaw('dispatch_date<CURDATE()' )
				->select('id');
                
                DB::table('buyer_quote_items as bqi')
				->where('lkp_post_status_id','=',OPEN)
                                ->whereRaw('dispatch_date<CURDATE()' )
				->update(array('lkp_post_status_id' =>CLOSED));
                
                //ptl buyer posts deleting
                $ptlGetItems = DB::table('ptl_buyer_quote_items as bqi')
                                ->join('ptl_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
				->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
				->select('bq.id');
                DB::table('ptl_buyer_quote_items as bqi')
                                ->join('ptl_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
				->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
				->update(array(
						'bq.lkp_post_status_id' =>CLOSED,
                                                'bqi.lkp_post_status_id' =>CLOSED));
                foreach($ptlGetItems as $getItem){
                    //BuyerMatchingComponent::removeFromMatching(ROAD_PTL, $getItem->id);
                }
                //rail buyer posts deleting
                $railGetItems = DB::table('rail_buyer_quote_items as bqi')
                                ->join('rail_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
				->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
				->select('bq.id');
                DB::table('rail_buyer_quote_items as bqi')
                                ->join('rail_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
				->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
				->update(array(
						'bq.lkp_post_status_id' =>CLOSED,
                                                'bqi.lkp_post_status_id' =>CLOSED));
                foreach($railGetItems as $getItem){
                    //BuyerMatchingComponent::removeFromMatching(RAIL, $getItem->id);
                }
                //airdom buyer posts deleting
                $airdomGetItems = DB::table('airdom_buyer_quote_items as bqi')
                                ->join('airdom_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
				->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
				->select('bq.id');
                DB::table('airdom_buyer_quote_items as bqi')
                                ->join('airdom_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
				->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
				->update(array(
						'bq.lkp_post_status_id' =>CLOSED,
                                                'bqi.lkp_post_status_id' =>CLOSED));
                foreach($airdomGetItems as $getItem){
                   // BuyerMatchingComponent::removeFromMatching(AIR_DOMESTIC, $getItem->id);
                }
                //airint buyer posts deleting
                $airintGetItems = DB::table('airint_buyer_quote_items as bqi')
                                ->join('airint_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
				->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
				->select('bq.id');
                DB::table('airint_buyer_quote_items as bqi')
                                ->join('airint_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
				->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
				->update(array(
						'bq.lkp_post_status_id' =>CLOSED,
                                                'bqi.lkp_post_status_id' =>CLOSED));
                foreach($airintGetItems as $getItem){
                   // BuyerMatchingComponent::removeFromMatching(AIR_INTERNATIONAL, $getItem->id);
                }
                //ocean buyer posts deleting
                $oceanGetItems = DB::table('ocean_buyer_quote_items as bqi')
                                ->join('ocean_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
				->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
				->select('bq.id');
                DB::table('ocean_buyer_quote_items as bqi')
                                ->join('ocean_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
				->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
				->update(array(
						'bq.lkp_post_status_id' =>CLOSED,
                                                'bqi.lkp_post_status_id' =>CLOSED));
                foreach($oceanGetItems as $getItem){
                   // BuyerMatchingComponent::removeFromMatching(OCEAN, $getItem->id);
                }
                //intracity buyer posts deleting
                DB::table('ict_buyer_quote_items as bqi')
                                ->join('ict_buyer_quotes as bq','ict_buyer_quotes.id','=','ict_buyer_quote_items.buyer_quote_id')
				->where('bq.lkp_post_status_id','=',OPEN)
                                ->where('bqi.lkp_post_status_id','=',OPEN)
                                ->whereRaw( "CONCAT( 'bqi.pickup_date',' ', 'bqi.pickup_time')>=DATE_FORMAT( (NOW( ) - INTERVAL 1 DAY ) ,  '%Y-%m-%d %H:%i:%s') ")
				->update(array(
						'bq.lkp_post_status_id' =>CLOSED,
                                                'bqi.lkp_post_status_id' =>CLOSED));
                
                //ftl  seller posts deleting
                $ftlPostItems = DB::table('seller_posts')
                ->join('seller_post_items','seller_posts.id','=','seller_post_items.seller_post_id')
				->where('seller_posts.lkp_post_status_id','=',OPEN)
                ->where('seller_post_items.lkp_post_status_id','=',OPEN)
                ->whereRaw('seller_posts.to_date<CURDATE()')
				->select('seller_post_items.id');
                DB::table('seller_posts')
                ->join('seller_post_items','seller_posts.id','=','seller_post_items.seller_post_id')
				->where('seller_posts.lkp_post_status_id','=',OPEN)
                ->where('seller_post_items.lkp_post_status_id','=',OPEN)
                ->whereRaw('seller_posts.to_date<CURDATE()')
				->update(array(
						'seller_posts.lkp_post_status_id' =>CLOSED,
                                                'seller_post_items.lkp_post_status_id' =>CLOSED));
                foreach($ftlPostItems as $getItem){
                    //SellerMatchingComponent::removeFromMatching(ROAD_FTL, $getItem->id);
                }
                //ptl  seller posts deleting
                $ptlPostItems = DB::table('ptl_seller_post_items as spi')
                                ->join('ptl_seller_posts as sp','sp.id','=','spi.seller_post_id')
				->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
				->select('spi.id');
                DB::table('ptl_seller_post_items as spi')
                                ->join('ptl_seller_posts as sp','sp.id','=','spi.seller_post_id')
				->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
				->update(array(
						'sp.lkp_post_status_id' =>CLOSED,
                                                'spi.lkp_post_status_id' =>CLOSED));
                foreach($ptlPostItems as $getItem){
                    //SellerMatchingComponent::removeFromMatching(ROAD_PTL, $getItem->id);
                }
                //rail  seller posts deleting
                $railPostItems = DB::table('rail_seller_post_items as spi')
                                ->join('rail_seller_posts as sp','sp.id','=','spi.seller_post_id')
				->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
				->select('spi.id');
                DB::table('rail_seller_post_items as spi')
                                ->join('rail_seller_posts as sp','sp.id','=','spi.seller_post_id')
				->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
				->update(array(
						'sp.lkp_post_status_id' =>CLOSED,
                                                'spi.lkp_post_status_id' =>CLOSED));
                foreach($railPostItems as $getItem){
                    SellerMatchingComponent::removeFromMatching(RAIL, $getItem->id);
                }
                //airdom  seller posts deleting
                $airdomPostItems = DB::table('airdom_seller_post_items as spi')
                                ->join('airdom_seller_posts as sp','sp.id','=','spi.seller_post_id')
				->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
				->select('spi.id');
                DB::table('airdom_seller_post_items as spi')
                                ->join('airdom_seller_posts as sp','sp.id','=','spi.seller_post_id')
				->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
				->update(array(
						'sp.lkp_post_status_id' =>CLOSED,
                                                'spi.lkp_post_status_id' =>CLOSED));
                foreach($airdomPostItems as $getItem){
                    //SellerMatchingComponent::removeFromMatching(AIR_DOMESTIC, $getItem->id);
                }
                //airint  seller posts deleting
                $airintPostItems = DB::table('airint_seller_post_items as spi')
                                ->join('airint_seller_posts as sp','sp.id','=','spi.seller_post_id')
				->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
				->select('spi.id');
                DB::table('airint_seller_post_items as spi')
                                ->join('airint_seller_posts as sp','sp.id','=','spi.seller_post_id')
				->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
				->update(array(
						'sp.lkp_post_status_id' =>CLOSED,
                                                'spi.lkp_post_status_id' =>CLOSED));
                foreach($airintPostItems as $getItem){
                    //SellerMatchingComponent::removeFromMatching(AIR_INTERNATIONAL, $getItem->id);
                }
                //ocean  seller posts deleting
                $oceanPostItems = DB::table('ocean_seller_post_items as spi')
                                ->join('ocean_seller_posts as sp','sp.id','=','spi.seller_post_id')
				->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
				->select('spi.id');
                DB::table('ocean_seller_post_items as spi')
                                ->join('ocean_seller_posts as sp','sp.id','=','spi.seller_post_id')
				->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
				->update(array(
						'sp.lkp_post_status_id' =>CLOSED,
                                                'spi.lkp_post_status_id' =>CLOSED));
               
                
            }catch(\Exception $e){
                
            }
        }
        
	/**
	 * For creating a folder
	 * @param string $folderDirctory
	 */
	public static function createDirectory($folderDirctory){
		try{
			if (!file_exists($folderDirctory)) {
				mkdir($folderDirctory, 0755, true);
			}
			return true;
		}catch (Exception $e) {
			//TODO:: Log the error somewhere
		}
	}
	/**
	 * Get of Lowest Quote *
	 */
	public static function getLowestQuote($id) {
		$userId = Auth::user ()->id;
		$serviceId  =   Session::get('service_id');
		try {
			$initial_quote_price = DB::table ( 'term_buyer_quote_sellers_quotes_prices' )
					->where ( 'term_buyer_quote_item_id', $id )
					->where ( 'is_submitted', 1 )
					->where ( 'lkp_service_id', Session::get ( 'service_id' ) )
					->select ( 'initial_quote_price' )
					->lists ( 'initial_quote_price' );
			$initial_quote_price_low = min ( $initial_quote_price );
			return $initial_quote_price_low;
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}


	public static function getIsSubmitData($quoteId) {
		$userId = Auth::user ()->id;
		try {
				$is_submitted_data = DB::table ( 'term_buyer_quote_sellers_quotes_prices' )
				->where ( 'term_buyer_quote_id', $quoteId )
				->where ( 'seller_id', $userId )
				->where ( 'lkp_service_id', Session::get ( 'service_id' ) )
				->lists ( 'is_submitted' );
				if (in_array ( "1", $is_submitted_data )) {
				return 0;
				} else {
				return 1;
				}
			} catch ( Exception $ex ) {
		}
	}



	/**
	 * Retrieval of Bid Types *
	 */
	public static function getAllBidTypes() {
		try {
			$bidTypes = DB::table ( 'lkp_bid_types' )->orderBy ( 'bid_type', 'asc' )->lists ( 'bid_type', 'id' );
			return $bidTypes;
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

	public static function getBidDateTime($buyer_quote_id,$serviceId) {
		try {
			$bidDate =  DB::table ( 'term_buyer_bid_dates' )
				->where ( 'term_buyer_quote_id', '=', $buyer_quote_id )
				->where ( 'lkp_service_id', '=', $serviceId )
				->select ( 'bid_end_date','bid_end_time' )->first ();


			$biddate=$bidDate->bid_end_date." ".$bidDate->bid_end_time;
			$start_date = new DateTime();
			$end_date = new DateTime($biddate);
			$interval = $start_date->diff($end_date);

			return $interval->d;

		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

	public static function getBidDateTimeByQuoteId($buyer_quote_id,$serviceId) {
		try {
			$bidDate =  DB::table ( 'term_buyer_bid_dates' )
				->where ( 'term_buyer_quote_id', '=', $buyer_quote_id )
				->where ( 'lkp_service_id', '=', $serviceId )
				->orderBy("id","desc")
				->select ( 'bid_end_date','bid_end_time' )->first ();


			return $bidDate->bid_end_date." ".$bidDate->bid_end_time;

		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}
        
        
        public static function getBidDateTimeNewFormat($buyer_quote_id,$serviceId) {
		try {
			$bidDate =  DB::table ( 'term_buyer_bid_dates' )
				->where ( 'term_buyer_quote_id', '=', $buyer_quote_id )
				->where ( 'lkp_service_id', '=', $serviceId )
				->orderBy("id","desc")
				->select ( 'bid_end_date','bid_end_time' )->first ();


			return commonComponent::checkAndGetDate($bidDate->bid_end_date)." ".$bidDate->bid_end_time;

		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}
	
	/** Date conversion to Y-m-d H:i:s format **/
	public static function getAMPM($orgDate,$orgTime){
		$date = $orgDate.''.$orgTime;
		return date('h:i A', strtotime($date));
		//return $newDateFormat;
	}
        
        
        public static function getMessageType($id) {
		
		try {
			$data = DB::table ( 'user_messages' )->where ( 'id', $id )->select ( 'lkp_message_type_id','lkp_service_id','post_item_id','order_id','enquiry_id','quote_item_id','lead_id','is_term' )->first();
			
             if (!empty ( $data )) {
				return $data;
			} 
		} catch ( Exception $ex ) {
		}
	}
        
        public static function getParentId($id) {
		
		try {
			$data = DB::table ( 'user_messages' )->where ( 'id', $id )->select ( 'parent_message_id','actual_parent_message_id' )->first();
			if($data->parent_message_id!=0){
                             return $data->actual_parent_message_id;
                        }else
                            return $id;
                        
		} catch ( Exception $ex ) {
		}
	}
	
	public static function getSellerAddress($user_id,$business){
		

		 if($business==1){
		 	$data = DB::table ( 'seller_details' )->where ( 'user_id', $user_id )->select ( 'address' )->first();
		 	
		 }else{
		 	$data = DB::table ( 'sellers' )->where ( 'user_id', $user_id )->select ( 'address' )->first();
		 }
		 
		 if(count($data)>0){
		 return $data->address;
		 }else{
		 return 1;	
		 }
	}
	
	public static function getBuyerAddress($user_id,$business){
	
	 if($business==1){
			$data = DB::table ( 'buyer_business_details' )->where ( 'user_id', $user_id )->select ( 'address' )->first();
	
		}else{
			$data = DB::table ( 'buyer_details' )->where ( 'user_id', $user_id )->select ( 'address' )->first();
		}
		
		if(count($data)>0){
			return $data->address;
		}else{
			return 1;
		}
			
		//return $data->address;
	}
        
        /** Retrieval of  order no  **/
	public static function getOrderno($id)
	{
		try
		{
			$order = DB::table ( 'orders' )->where ( 'id', $id )->select('order_no')->first();
			if(!empty($order))
				return $order->order_no;
			
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
    /** Retrieval of  contract no  **/
	public static function getContractno($id)
	{
		try
		{
			$order = DB::table ( 'term_contracts' )->where ( 'id', $id )->select('contract_no')->first();
			if(!empty($order))
				return $order->contract_no;
			
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Terms and Conditions  **/
	public static function getTermsAndConditions($spiid,$table1,$table2)
	{
		try
		{
			//echo $spiid.$table1.$table2;exit;
			$sellerpostid   = DB::table($table2)
								->where([$table2.'.id' => $spiid])
								->select("$table2.seller_post_id")
								->get();
			
			$termsnconditions   = DB::table($table1)
			->where([$table1.'.id' => $sellerpostid[0]->seller_post_id])
			->select("$table1.terms_conditions")
			->get();
			
			return $termsnconditions[0]->terms_conditions;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Credit days  **/
	public static function getCreditdays($id,$table1,$table2)
	{
		try
		{
			//echo $table.$id;exit;	
			$creditdays   = DB::table($table1)
			->leftjoin($table2,$table1.'.id', '=',$table2.'.seller_post_id' )
			->where([$table2.'.id' => $id])
			->select("$table1.credit_period","$table1.credit_period_units")
			->get();
			
							
			return $creditdays;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
        
        /** Date conversion to d/m/Y H:i:s **/
	public static function convertDateTimeDisplay($org_date){
		try{
			$originalDate = $org_date;
			$newDate = date("d/m/Y H:i:s", strtotime($originalDate));
			return $newDate;
		}catch (Exception $e) {
			//TODO:: Log the error somewhere
		}
	}

	/** Date conversion to d/m/Y H:i:s **/
	public static function checkAndGetDateTime($date){
		try{
			if($date == '0000-00-00') {
				return '';
			}
			$dateOfTraining = CommonComponent::convertDateTimeDisplay($date);
			return $dateOfTraining;
		}catch (Exception $e) {
			//TODO:: Log the error somewhere
		}
	}
	
	/**
	 * Get of Lowest rate/kg Quote *
	 */
	public static function getLowestRatePerKg($id) {
		try {
			$initial_quote_price = DB::table ( 'term_buyer_quote_sellers_quotes_prices' )
										->where ( 'term_buyer_quote_item_id', $id )
										->where ( 'is_submitted', 1 )
										->where ( 'lkp_service_id', Session::get ( 'service_id' ) )
										->select ( 'initial_rate_per_kg' )->lists ( 'initial_rate_per_kg' );
			$initial_quote_price_low = min ( $initial_quote_price );
			return $initial_quote_price_low;
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}
	
	/**
	 * Get of Lowest Quote *
	 */
	public static function getLowestKgPerCft($id) {
		try {
			$initial_quote_price = DB::table ( 'term_buyer_quote_sellers_quotes_prices' )
										->where ( 'term_buyer_quote_item_id', $id )
										->where ( 'is_submitted', 1 )
										->where ( 'lkp_service_id', Session::get ( 'service_id' ) )
										->select ( 'initial_kg_per_cft' )->lists ( 'initial_kg_per_cft' );
			$initial_quote_price_low = min ( $initial_quote_price );
			return $initial_quote_price_low;
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

	/** Get the shipment type **/
	public static function getSelectedShipmentType($typeid)
	{
		try
		{
			$shipment = DB::table ( 'lkp_air_ocean_shipment_types' )->where ( 'id', $typeid )->select('shipment_type')->first();
			if(!empty($shipment))
				return $shipment->shipment_type;
			
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Get the shipment type **/
	public static function getAirInternationalShipmentType($typeid)
	{
		try
		{
			$shipment = DB::table ( 'lkp_relocation_shipment_types' )->where ( 'id', $typeid )->select('shipment_type')->first();
			if(!empty($shipment))
				return $shipment->shipment_type;
				
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Get the shipment type **/
	public static function getAirInternationalVolumeType($typeid)
	{
		try
		{
			$volume = DB::table ( 'lkp_relocation_shipment_volumes' )->where ( 'id', $typeid )->select('volume')->first();
			if(!empty($volume))
				return $volume->volume;
	
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Get the Sender Identity **/
	public static function getSelectedSenderIdentity($identityid)
	{
		try
		{
			$sidentity = DB::table ( 'lkp_air_ocean_sender_identities' )->where ( 'id', $identityid )->select('sender_identity')->first();
			if(!empty($sidentity))
				return $sidentity->sender_identity;
			
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}


	/**
	 * Below functions - Start
	 */
	//Retrive all Rate card Types
	public static function getAllRatecardTypes(){
		try{
			$rateCardTypes = DB::table('lkp_post_ratecard_types')->orderBy ( 'ratecard_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('ratecard_type', 'id');
			return $rateCardTypes;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}
        
        /**
	 * Below functions - Start
	 */
	//Retrive all Pet Types
	public static function getAllPetTypes(){
		try{
			$petTypes = DB::table('lkp_pet_types')->orderBy ( 'pet_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('pet_type', 'id');
			return $petTypes;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}
        
        //Retrieval Pet Type By Id*
	public static function getPetType($id) {
		try {
			$properyName = DB::table ( 'lkp_pet_types' )->where ( 'lkp_pet_types.id', '=', $id )->select ( 'lkp_pet_types.pet_type' )->get ();
			if(isset($properyName [0]->pet_type)) {
				return $properyName [0]->pet_type;
			}
			return "";
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}
    
    /**
	 * Getting breed types
	 */
	public static function getAllPetBreedTypes($pet_type_id = 0){
		// If empty, then it will show all the breed
		if(!empty($pet_type_id)):
			return DB::table('lkp_breed_types')->orderBy ( 'breed_type', 'asc' )->where([
				'is_active' => IS_ACTIVE,
				'lkp_pet_type_id' => $pet_type_id
				])->lists('breed_type','id');
		endif;
	}

	/**
	 * Get breed type based on pet breed type id
	 */
	public static function getBreedType($id) {
		try {
			$breedInfo = DB::table ( 'lkp_breed_types' )
				->where('lkp_breed_types.id', '=', $id)
				->select('lkp_breed_types.breed_type')
				->get();
			if(isset($breedInfo[0]->breed_type)) {
				return $breedInfo[0]->breed_type;
			}
			return "";
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

        /**
	 * Below functions - Start
	 */
	//Retrive all Cage Types
	public static function getAllCageTypes(){
		try{
			$petTypes = DB::table('lkp_cage_types')->orderBy ( 'cage_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('cage_type', 'id');
			return $petTypes;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}
        
         /**
	 * Below functions - Start
	 */
	//Retrive all Cage Types
	public static function getAllBreedTypesList(){
		try{
			$petTypes = DB::table('lkp_breed_types')->orderBy ( 'breed_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('breed_type', 'id');
			return $petTypes;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}
        
        //Retrieval Cage Type By Id*
	public static function getCageType($id) {
		try {
			$properyName = DB::table ( 'lkp_cage_types' )->where ( 'lkp_cage_types.id', '=', $id )->select ( 'lkp_cage_types.cage_type' )->get ();
			if(isset($properyName [0]->cage_type)) {
				return $properyName [0]->cage_type;
			}
			return "";
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}
        
        //Retrieval Cage Weight By Id*
	public static function getCageWeight($id) {
		try {
			$properyName = DB::table ( 'lkp_cage_types' )->where ( 'lkp_cage_types.id', '=', $id )->select ( 'lkp_cage_types.cage_weight' )->get ();
			if(isset($properyName [0]->cage_weight)) {
				return $properyName [0]->cage_weight;
			}
			return "";
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

	//Retrieval Rate card Type By Id*
	public static function getRatecardType($id) {
		try {
			$rateCard = DB::table ( 'lkp_post_ratecard_types' )->where ( ' lkp_post_ratecard_types.id', '=', $id )->select ( ' lkp_post_ratecard_types.ratecard_type' )->get ();
			if(isset($rateCard [0]->ratecard_type)) {
				return $rateCard [0]->ratecard_type;
			}
			return "";
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

	//Retrive all Property Types
	public static function getAllPropertyTypes(){
		try{
			$getAllPropertyTypes = DB::table('lkp_property_types')->orderBy ( 'property_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('property_type', 'id');
			return $getAllPropertyTypes;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}

	//Retrieval Property Type By Id*
	public static function getPropertyType($id) {
		try {
			$properyName = DB::table ( 'lkp_property_types' )->where ( 'lkp_property_types.id', '=', $id )->select ( 'lkp_property_types.property_type' )->get ();
			if(isset($properyName [0]->property_type)) {
				return $properyName [0]->property_type;
			}
			return "";
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

	//Retrive all Load Categories
	public static function getAllLoadCategories(){
		try{
			$loadCategories = DB::table('lkp_load_categories')->orderBy ( 'load_category', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('load_category', 'id');
			return $loadCategories;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}

	//Retrieval Load Category By Id*
	public static function getLoadCategoryById($id) {
		try {
			$loadCategory = DB::table ( 'lkp_load_categories' )->where ( 'lkp_load_categories.id', '=', $id )->select ( 'lkp_load_categories.load_category' )->get ();
			if(isset($loadCategory [0]->load_category)) {
				return $loadCategory [0]->load_category;
			}
			return "";
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

	//Retrive all Vehicle Categories
	public static function getAllVehicleCategories(){
		try{
			$vehicleCategories = DB::table('lkp_vechicle_categories')->orderBy ( 'vehicle_category', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('vehicle_category', 'id');
			return $vehicleCategories;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}

	//Retrieval Vehicle Category By Id*
	public static function getVehicleCategoryById($id) {
		try {
			$vehicleCategory = DB::table ( 'lkp_vechicle_categories' )->where ( 'lkp_vechicle_categories.id', '=', $id )->select ( 'lkp_vechicle_categories.vehicle_category' )->get ();

			if(isset($vehicleCategory [0]->vehicle_category)) {
				return $vehicleCategory [0]->vehicle_category;
			}
			return "";
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

	//Retrive all Room Types
	public static function getAllRoomTypes(){
		try{
			$roomTypes = DB::table('lkp_inventory_rooms')->orderBy ( 'inventory_room_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('inventory_room_type', 'id');
			return $roomTypes;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}

	//Retrieval Room Type By Id*
	public static function getRoomTypeById($id) {
		try {
			$vehicleCategory = DB::table ( 'lkp_inventory_rooms' )->where ( 'lkp_inventory_rooms.id', '=', $id )->select ( 'lkp_inventory_rooms.inventory_room_type' )->get ();
			if(isset($vehicleCategory [0]->load_category)) {
				return $vehicleCategory [0]->load_category;
			}
			return "";
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

	//Retrieval Room Type By Property Id*
	public static function getRoomTypesByPropertyId($porpertyId) {
		try {
			$roomTypes = DB::table('propertyxrooms')
				->join('lkp_inventory_rooms', 'propertyxrooms.lkp_inventory_room_id', '=', 'lkp_inventory_rooms.id')
				->where(['propertyxrooms.lkp_property_type_id' => $porpertyId])
				->select('lkp_inventory_rooms.id','lkp_inventory_rooms.inventory_room_type')
				->lists();
			return $roomTypes;
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}

	//Retrieval Room Particulars By Room Id*
	public static function getParticularsByRoomId($roomId){
		try{
			$roomTypes = DB::table('lkp_inventory_room_particulars')->where('is_active','=',IS_ACTIVE)->where('inventory_room_type_id','=',$roomId)->select ( 'lkp_inventory_room_particulars.id','lkp_inventory_room_particulars.room_particular_type' )->get ();
			return $roomTypes;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}

/**
* Get Relocation Office Particulars
* author: Kalyani  / 10052016
*/
	public static function getOfficeParticulars(){
		try{
			$particulars = DB::table('lkp_inventory_office_particulars')->where('is_active','=',IS_ACTIVE)->select ( 'lkp_inventory_office_particulars.id','lkp_inventory_office_particulars.office_particular_type','lkp_inventory_office_particulars.volume','lkp_inventory_office_particulars.office_particular_type' )->get ();
			
			return $particulars;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}	
	
	//Retrieval CFT by Property Type  Id*
	public static function getPropertyCft($id) {
		try {
			$properyName = DB::table ( 'lkp_property_types' )->where ( 'lkp_property_types.id', '=', $id )->select ( 'lkp_property_types.volume' )->get ();
 	        return $properyName[0]->volume;
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}
	//following network profile
	public static function followNetworkProfileupdation($id,$status) {
		try {
			
			if($status == 1){
			$follow = new NetworkFollowers();
			$follow->follower_user_id = $id;
			$follow->user_id = Auth::User()->id;
			$follow->created_at = date('Y-m-d H:i:s');
			$follow->created_ip = $_SERVER['REMOTE_ADDR'];
			$follow->created_by = Auth::User()->id;
			$follow->save();
			
			$username= CommonComponent::getUsername(Auth::user()->id);
			$follower= CommonComponent::getUsername($id);
			//Insert Into Feeds
			$created_at = date ( 'Y-m-d H:i:s' );
			$createdIp = $_SERVER['REMOTE_ADDR'];
			$addrecom = new NetworkFeeds();
			$addrecom->feed_type = "follower";
			$addrecom->feed_title= "Following";
			$addrecom->feed_description =$username." is Following ".$follower;
			$addrecom->user_id =Auth::user()->id;
			$addrecom->created_by =Auth::user()->id;
			$addrecom->created_at  =$created_at;
			$addrecom->created_ip = $createdIp;
			$addrecom->updated_by =Auth::user()->id;
			$addrecom->save();
			
			return 1;
			}else{
				DB::table('network_followers')
				->where('follower_user_id', $id)
				->where('user_id', Auth::user()->id)
				->delete();
				return 0;
			}
			
			
			
			
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}
	//Partner Request Send
	public static function partnerRequestsending($id) {
		try {
				
			
				$partnerrequest = new NetworkPartners();
				$partnerrequest->partner_user_id = $id;
				$partnerrequest->user_id = Auth::User()->id;
				$partnerrequest->is_approved = 0;
				$partnerrequest->email_sent = 1;
				$partnerrequest->created_at = date('Y-m-d H:i:s');
				$partnerrequest->created_ip = $_SERVER['REMOTE_ADDR'];
				$partnerrequest->created_by = Auth::User()->id;
				$partnerrequest->save();
				
				$created_year = date('Y');
				$randnumber   = 'PartnerRequest/' .$created_year .'/'.$partnerrequest->id;
				$partnermessage = new UserMessage();
				$partnermessage->lkp_service_id = 0;
				$partnermessage->sender_id = Auth::User()->id;
				$partnermessage->recepient_id = $id;
				$partnermessage->lkp_message_type_id = 9;
				$partnermessage->message_no = $randnumber;
				$partnermessage->subject = "Partner Request";
				$partnermessage->message = "Recieved New Partner Request";
				$partnermessage->is_read = 0;
				$partnermessage->created_at = date('Y-m-d H:i:s');
				$partnermessage->created_ip = $_SERVER['REMOTE_ADDR'];
				$partnermessage->created_by = Auth::User()->id;
				$partnermessage->save();
				
				$partnerrequestmail = DB::table('users')->where('id', $id)->get();
				$partnerrequestmail[0]->sender = Auth::User()->username;
				CommonComponent::send_email(PARTNER_REQUEST,$partnerrequestmail);
			
 				return 1;
			
				
				
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}
	
	public static function getAllRooms(){
		try{
			$roomTypes = DB::table('lkp_inventory_rooms')->where('is_active','=',IS_ACTIVE)->select('inventory_room_type', 'id')->get ();
			return $roomTypes;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}
	

	//Retrive all Vehicle Categories
	public static function getAllVehicleCategoryTypes(){
		try{
			$vehicleCategories = DB::table('lkp_vechicle_categorie_types')->orderBy ( 'lkp_vechicle_categorie_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('lkp_vechicle_categorie_type', 'id');
			return $vehicleCategories;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}

	//Retrieval Vehicle Category By Id*
	public static function getVehicleCategorytypeById($id) {
		try {
			$vehicleCategory = DB::table ( 'lkp_vechicle_categorie_types' )->where ( 'lkp_vechicle_categorie_types.id', '=', $id )->select ( 'lkp_vechicle_categorie_types.lkp_vechicle_categorie_type' )->get ();
			if(isset($vehicleCategory [0]->lkp_vechicle_categorie_type)) {
				return $vehicleCategory [0]->lkp_vechicle_categorie_type;
			}
			return "";
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}
	
	public static function getBuyerDetails(){
		try {
		if(Auth::User()->is_business == 1){
			$buyerTable = 'buyer_business_details';
		}else{
			$buyerTable = 'buyer_details';
		}
		
		$buyerDetails = DB::table ( 'users' )->leftJoin( $buyerTable , 'users.id', '=', $buyerTable.'.user_id' )->where ('users.id', Auth::User()->id)->first();
		
		return $buyerDetails;
		} catch ( Exception $e ) {
			// return $e->message;
		}
	}

	public static function getUserNameDetails(){
		try {
		if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )	{
			if(Auth::User()->is_business == 1){
				$buyerTable = 'buyer_business_details';
				$buyerDetails = DB::table ( 'users' )
				->leftJoin( $buyerTable , 'users.id', '=', $buyerTable.'.user_id' )
				->where ('users.id', Auth::User()->id)
				->select($buyerTable.'.address',$buyerTable.'.contact_email',$buyerTable.'.name as contact_firstname',$buyerTable.'.contact_mobile',$buyerTable.'.principal_place')
				->first();
			}else{
				$buyerTable = 'buyer_details';
				$buyerDetails = DB::table ( 'users' )
				->leftJoin( $buyerTable , 'users.id', '=', $buyerTable.'.user_id' )
				->where ('users.id', Auth::User()->id)
				->select($buyerTable.'.address',$buyerTable.'.contact_email',$buyerTable.'.firstname as contact_firstname',$buyerTable.'.mobile as contact_mobile',$buyerTable.'.principal_place')
				->first();
			}
			if(!$buyerDetails->principal_place){
					$sellerTable = 'seller_details';
					$sellerDetails = DB::table ( 'users' )
					->leftJoin( $sellerTable , 'users.id', '=', $sellerTable.'.user_id' )
					->where ('users.id', Auth::User()->id)
					->select( $sellerTable.'.principal_place')
					->first();
				$buyerDetails->principal_place = $sellerDetails->principal_place;	
			}
		}

			return $buyerDetails;
		} catch ( Exception $e ) {
			// return $e->message;
		}
	}
        
        /** Retrieval of Max inv id  **/
	public static function getTermContractID()
	{
		try
		{
			$post = DB::table ( 'term_contracts' )->select('id')->orderBy('id','desc')->first();
			if(!empty($post))
				return $post->id+1;
			else
				return 1;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	
	/** Retrieval of all Courier purpose Types**/
	public static function getAllCourierPorposeTypes()
	{
		try
		{
			$courierTypes = DB::table('lkp_courier_purposes')->orderBy ( 'courier_purpose', 'asc' )->lists('courier_purpose', 'id');
			return $courierTypes;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	public static function getVolumeCft($buyer_post_id)
	{
		try
		{
			$buyer_post_edit_seller='';
	  		$buyer_post_inventory_details='';
	  		$buyer_post_details = DB::table ( 'relocation_buyer_posts' )->where ( 'id', $buyer_post_id )->get ();
	  		
	  	if($buyer_post_details[0]->lkp_post_ratecard_type_id==1){
	  		$buyer_post_inventory_details = DB::table ( 'relocation_buyer_post_inventory_particulars' )->where ( 'buyer_post_id', $buyer_post_id )->get ();
	  	 }
	  	 if(count($buyer_post_inventory_details)>0){
	  	 	
	  	 	$totalcft=0;
	  	 	
	  	 		$getroomsdata  = DB::table('relocation_buyer_post_inventory_particulars as rebip')
	  	 		->where('rebip.buyer_post_id',$buyer_post_id)
	  	 		->select('rebip.lkp_inventory_room_particular_id','rebip.number_of_items')
	  	 		->get();
	  	 		//echo "<pre>"; print_r($getroomsdata);
	  	 		foreach($getroomsdata as $getdata){
	  	 			//echo $getdata->lkp_inventory_room_particular_id;
	  	 			
	  	 			$itemvolume = DB::table('lkp_inventory_room_particulars as lirp')
	  	 			->where('lirp.id',$getdata->lkp_inventory_room_particular_id)
	  	 			->select('lirp.*')
	  	 			->get();

	  	 			
	  	 			$totalcft=$totalcft+$getdata->number_of_items*$itemvolume[0]->volume;
	  	 			
	  	 		}
	  	 	
	  	 	
	  	    $volume=$totalcft;	
	  	    
	  	 }else{
	  	 	
	  	   	$volume=CommonComponent::getPropertyCft($buyer_post_details[0]->lkp_property_type_id);
	  	 }
	  	 
	  	 return $volume;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
		
	}

/*
*  Method to get relocation office buyer post volume
*  author: Kalyani / 10052016
*  @param: $buyer_post_id
*  return: decimal val (Total Volume)
*/	
	public static function getOfficeBuyerVolume($buyer_post_id){
		try
		{
			$volume = '';
			$buyer_post_edit_seller='';
			$buyer_post_inventory_details='';
			$buyer_post_details = DB::table ( 'relocationoffice_buyer_posts' )->where ( 'id', $buyer_post_id )->get ();

			
			$buyer_post_inventory_details = DB::table ( 'relocationoffice_buyer_post_inventory_particulars' )->where ( 'buyer_post_id', $buyer_post_id )->get ();
			
			if(count($buyer_post_inventory_details)>0)
			{
				$total=0;
				$getroomsdata  = DB::table('relocationoffice_buyer_post_inventory_particulars as rebip')
					->where('rebip.buyer_post_id',$buyer_post_id)
					->select('rebip.lkp_inventory_office_particular_id','rebip.number_of_items')
					->get();
				foreach($getroomsdata as $getdata)
				{
					$itemvolume = DB::table('lkp_inventory_office_particulars as lirp')
					->where('lirp.id',$getdata->lkp_inventory_office_particular_id)
					->select('lirp.*')
					->get();
					$total=$total+$getdata->number_of_items*$itemvolume[0]->volume;
				}
				$volume=$total;	
			}
			return $volume;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}		
	}


	public static function getCratingVolumeCft($buyer_post_id)
	{
		try
		{
			$buyer_post_edit_seller='';
			$buyer_post_inventory_details='';

			$buyer_post_details = DB::table ( 'relocation_buyer_posts' )->where ( 'id', $buyer_post_id )->get ();


			if($buyer_post_details[0]->lkp_post_ratecard_type_id==1){
				$buyer_post_inventory_details = DB::table ( 'relocation_buyer_post_inventory_particulars' )->where ( 'buyer_post_id', $buyer_post_id )->get ();
			}

			if(count($buyer_post_inventory_details)>0){
	
				$totalcft=0;
	
				$getroomsdata  = DB::table('relocation_buyer_post_inventory_particulars as rebip')
				->where('rebip.buyer_post_id',$buyer_post_id)
				->where('rebip.crating_required',1)
				->select('rebip.lkp_inventory_room_particular_id','rebip.number_of_items')
				->get();
				//echo "<pre>"; print_r($getroomsdata);
				foreach($getroomsdata as $getdata){
					//echo $getdata->lkp_inventory_room_particular_id;

					$itemvolume = DB::table('lkp_inventory_room_particulars as lirp')
					->where('lirp.id',$getdata->lkp_inventory_room_particular_id)
					->select('lirp.*')
					->get();

	 				$totalcft=$totalcft+$getdata->number_of_items*$itemvolume[0]->volume;
				}
	
				$volume=$totalcft;
	
			}else{

				$volume=0;
			}

			return $volume;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	
	}

	/**
	 * Below functions - End
	 */
	
	public static function getSellersViewcountFromTable($sellerPostId,$tableName) {
		try {
			if($tableName=='relocationoffice_seller_post_views'){
                            $countview = DB::table($tableName.' as svic')
                            ->where('svic.seller_post_id','=',$sellerPostId)
                            ->select('svic.id','svic.view_counts')
                            ->get();
                            if(!isset($countview[0]->view_counts)) {
                                    $countview = 0;
                            } else {
                                    $countview = $countview[0]->view_counts;
                            }	
			  	
			}elseif($tableName=='relocationgm_seller_post_views'){
                            $countview = DB::table($tableName.' as svic')
                            ->where('svic.seller_post_id','=',$sellerPostId)
                            ->select('svic.id','svic.view_counts')
                            ->get();
                            if(!isset($countview[0]->view_counts)) {
                                    $countview = 0;
                            } else {
                                    $countview = $countview[0]->view_counts;
                            }	
			  	
			}else{			
                            $countview = DB::table($tableName.' as svic')			
                            ->where('svic.seller_post_item_id','=',$sellerPostId)
                            ->select('svic.id','svic.view_counts')
                            ->get();
                            if(!isset($countview[0]->view_counts)) {
                                    $countview = 0;
                            } else {
                                    $countview = $countview[0]->view_counts;
                            }
			}
			return $countview;
		} catch ( Exception $exc ) {			
		}
	}
	
	/** Retrieval of Service name  **/
	public static function getGroupName($serviceId)
	{
		try
		{
	
			$getServiceName = DB::table('lkp_services')
			->where('lkp_services.id', '=', $serviceId)
			->select('lkp_services.group_name')
			->get();
	
			return $getServiceName[0]->group_name;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Date conversion to MM/DD/YY **/
	public static function sellerODACheck($to,$service){
		try{
                    
			$getOdaservice = DB::table('ptl_pincodexsectors')
			->where('ptl_pincodexsectors.lkp_service_id', '=', $service)
			->where('ptl_pincodexsectors.seller_id', '=', Auth::user()->id)
			->where('ptl_pincodexsectors.ptl_pincode_id', '=', $to)
			->where('ptl_pincodexsectors.is_active', '=', 1)
			->select('ptl_pincodexsectors.oda')
			->get();
			if(isset($getOdaservice[0]->oda))
				$odacheck = $getOdaservice[0]->oda;
			else 
				$odacheck = 1;
			return $odacheck;
		}catch (Exception $e) {
			//TODO:: Log the error somewhere
		}
	}
	
	public static function buyerODACheck($to,$service,$userId){
		try{
                    
			$getOdaservice = DB::table('ptl_pincodexsectors')
			->where('ptl_pincodexsectors.lkp_service_id', '=', $service)
			->where('ptl_pincodexsectors.seller_id', '=', $userId)
			->where('ptl_pincodexsectors.ptl_pincode_id', '=', $to)
			->where('ptl_pincodexsectors.is_active', '=', 1)
			->select('ptl_pincodexsectors.oda')
			->get();
			if(isset($getOdaservice[0]->oda))
				$odacheck = $getOdaservice[0]->oda;
			else 
				$odacheck = 1;
			return $odacheck;
		}catch (Exception $e) {
			//TODO:: Log the error somewhere
		}
	}
	
	public static function getSellerNames($buyer_post_id, $table_name = ''){
		try {
			
			switch($table_name):
			
				case 'relocationpet_buyer_selected_sellers':
					$privateSellers = DB::table('relocationpet_buyer_selected_sellers as rbs')
					->join('users as u','u.id','=','rbs.seller_id')
					->where('rbs.buyer_post_id','=',$buyer_post_id)
					->select('u.username')
					->get();
					break;

				default:
					$privateSellers = DB::table('relocation_buyer_selected_sellers as rbs')
					->join('users as u','u.id','=','rbs.seller_id')
					->where('rbs.buyer_post_id','=',$buyer_post_id)
					->select('u.username')
					->get();
				
			endswitch;
				
			return $privateSellers;
		} 
			catch ( Exception $exc ) {
		}	
		
	}
   public static function getBuyerInventoryRoomsbyId($buyer_post_id){
		try {
				
			$serviceId  =   Session::get('service_id');
                        if($serviceId == RELOCATION_INTERNATIONAL) {
                            $getroomsdata  = DB::table('relocationint_buyer_post_inventory_particulars as rebip')
                            ->join('lkp_inventory_rooms as rooms','rooms.id','=','rebip.lkp_inventory_room_id')
                            ->where('rebip.buyer_post_id',$buyer_post_id)
                            ->groupBy('rebip.lkp_inventory_room_id')
                            ->select('rooms.inventory_room_type','rebip.lkp_inventory_room_id')
                            ->get();
                        } else {
                            $getroomsdata  = DB::table('relocation_buyer_post_inventory_particulars as rebip')
                            ->join('lkp_inventory_rooms as rooms','rooms.id','=','rebip.lkp_inventory_room_id')
                            ->where('rebip.buyer_post_id',$buyer_post_id)
                            ->groupBy('rebip.lkp_inventory_room_id')
                            ->select('rooms.inventory_room_type','rebip.lkp_inventory_room_id')
                            ->get();
                        }	
			
			return $getroomsdata;
			
		} catch ( Exception $exc ) {
		}
	
	}
	
	public static function getBuyerInventoryParticularsbyId($buyer_post_id,$room_id){
		try {
			$serviceId  =   Session::get('service_id');
                        if($serviceId == RELOCATION_INTERNATIONAL) {
                            //$privateSellers= array();
                            $getparticularsdata  = DB::table('relocationint_buyer_post_inventory_particulars as rebip')
                            ->join('lkp_inventory_room_particulars as particulars','particulars.id','=','rebip.lkp_inventory_room_particular_id')
                            ->where('rebip.buyer_post_id',$buyer_post_id)
                            ->where('rebip.lkp_inventory_room_id',$room_id)
                            ->select('rebip.lkp_inventory_room_particular_id','rebip.number_of_items','rebip.crating_required','particulars.room_particular_type')
                            ->get();
                        } else {
                            //$privateSellers= array();
                            $getparticularsdata  = DB::table('relocation_buyer_post_inventory_particulars as rebip')
                            ->join('lkp_inventory_room_particulars as particulars','particulars.id','=','rebip.lkp_inventory_room_particular_id')
                            ->where('rebip.buyer_post_id',$buyer_post_id)
                            ->where('rebip.lkp_inventory_room_id',$room_id)
                            ->select('rebip.lkp_inventory_room_particular_id','rebip.number_of_items','rebip.crating_required','particulars.room_particular_type')
                            ->get();                            
                        }	
			
				
			
			return $getparticularsdata;
		} catch ( Exception $exc ) {
		}
	
	}
	
	public static function getBuyerInventoryParticularsDataInfo($buyer_post_id){
		try {
                         $serviceId  =   Session::get('service_id');
                         if($serviceId == RELOCATION_INTERNATIONAL) {
                             $getparticularsdata  = DB::table('relocationint_buyer_post_inventory_particulars as rebip')
                            ->where('rebip.buyer_post_id',$buyer_post_id)
                            ->select('rebip.id')
                            ->get();
                             
                         } else { 
                            //$privateSellers= array();
                            $getparticularsdata  = DB::table('relocation_buyer_post_inventory_particulars as rebip')
                            ->where('rebip.buyer_post_id',$buyer_post_id)
                            ->select('rebip.id')
                            ->get();                             
                         }
                         
	
				
			return count($getparticularsdata);
		} catch ( Exception $exc ) {
		}
	
	}
        
        /** Retrieval of cart item  **/
	public static function CheckCartItem($bqid)
	{
		try
		{//echo "here".$bqid;
                    $serviceId  =   Session::get('service_id');
                    //checking for item data in cart table
                    $qry = DB::table ( 'cart_items as ci' );
                    $qry->where('ci.buyer_id',Auth::user()->id)
                            ->where('ci.lkp_service_id', Session::get('service_id'));
                    switch($serviceId){
			case ROAD_FTL       :
                        case ROAD_INTRACITY :
                        case ROAD_TRUCK_HAUL :  
                        case ROAD_TRUCK_LEASE :        
                            $qry->where('ci.buyer_quote_item_id',$bqid);
                            break;
                        case ROAD_PTL       :
                        case RAIL       :
                        case AIR_DOMESTIC       :
                        case AIR_INTERNATIONAL       : 
                        case OCEAN       :     
                        case COURIER       :     
                        case RELOCATION_DOMESTIC       :
                        case RELOCATION_OFFICE_MOVE:
                        case RELOCATION_PET_MOVE       :
                        case RELOCATION_INTERNATIONAL       :
                        case RELOCATION_GLOBAL_MOBILITY:
                            $qry->where('ci.buyer_quote_id',$bqid);
                            break;
                            
                    }
                    $cart   =   $qry->select ( 'ci.id' )->first();  
                    //checking for item data in orders table
                    $query = DB::table ( 'orders' );
                    $query->where('orders.buyer_id',Auth::user()->id)
                            ->where('orders.lkp_service_id', Session::get('service_id'));
                    switch($serviceId){
			case ROAD_FTL       : 
                        case ROAD_INTRACITY : 
                        case ROAD_TRUCK_HAUL :  
                        case ROAD_TRUCK_LEASE :      
                            $query->where('orders.buyer_quote_item_id',$bqid);
                            break;
                        case ROAD_PTL   :
                        case RAIL       :
                        case AIR_DOMESTIC       :
                        case AIR_INTERNATIONAL  : 
                        case OCEAN       :     
                        case COURIER     :     
                        case RELOCATION_DOMESTIC:
                        case RELOCATION_OFFICE_MOVE:
                        case RELOCATION_PET_MOVE:  
                        case RELOCATION_INTERNATIONAL       :
                        case RELOCATION_GLOBAL_MOBILITY:     
                            $query->where('orders.buyer_quote_id',$bqid);
                            break;
                            
                    }
                    $order   =   $query->select ( 'orders.id' )->first(); 
                    
                    if(empty($cart) && empty($order)){
                        return 1;
                    }else{
                        return 0;
                    }
			
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	public static function getSellerServiceExits(){
		
	try{
			$checkServiceForSeller = DB::table('seller_services');
			$checkServiceForSeller->where('user_id', '=', Auth::user()->id);
			$checkServiceForSeller = $checkServiceForSeller->lists('lkp_service_id');
			return $checkServiceForSeller;
		}
		catch(Exception $e)
		{
			return $e->message;
		}
	}
	//checking document is there or not in crete quote
	public static function getBuyerBidDocumentsCheckingCount($buyer_quote_id){
		try {			
			$documents = DB::table('term_buyer_quote_bid_terms_files')
			->where('term_buyer_quote_id', $buyer_quote_id)
			->select('file_name')
			->first();	
                        if(!empty($documents))
			return $documents->file_name;
                        else
                            return "";
		} catch ( Exception $exc ) {
			
		}	
	}
	
	public static function getSellerPostDelete($postid_delete){
		try{
			$array_post_status = array();
			$array_post_status[0] = 1;
			$array_post_status[1] = 2;
			$serviceId = Session::get('service_id');
			switch($serviceId){
				case ROAD_FTL       	:
					$checkServiceForSeller = DB::table('seller_post_items')->where('seller_post_id', '=', $postid_delete)
					->whereIn('lkp_post_status_id', $array_post_status)
					->where('is_private', 0)
					->select('*')->get();
					return count($checkServiceForSeller);
				case ROAD_PTL   		:
					$checkServiceForSeller = DB::table('ptl_seller_post_items')->where('seller_post_id', '=', $postid_delete)
					->whereIn('lkp_post_status_id', $array_post_status)
					->where('is_private', 0)
					->select('*')->get();
					return count($checkServiceForSeller);
					
				case RAIL       		:
					$checkServiceForSeller = DB::table('rail_seller_post_items')->where('seller_post_id', '=', $postid_delete)
					->whereIn('lkp_post_status_id', $array_post_status)
					->where('is_private', 0)
					->select('*')->get();
					return count($checkServiceForSeller);
				case ROAD_TRUCK_LEASE       		:
						$checkServiceForSeller = DB::table('trucklease_seller_post_items')->where('seller_post_id', '=', $postid_delete)
						->whereIn('lkp_post_status_id', $array_post_status)
						->where('is_private', 0)
						->select('*')->get();
						return count($checkServiceForSeller);
                    case ROAD_TRUCK_HAUL       		:
						$checkServiceForSeller = DB::table('truckhaul_seller_post_items')->where('seller_post_id', '=', $postid_delete)
						->whereIn('lkp_post_status_id', $array_post_status)
						->where('is_private', 0)
						->select('*')->get();
						return count($checkServiceForSeller);
				case AIR_DOMESTIC       :
					$checkServiceForSeller = DB::table('airdom_seller_post_items')->where('seller_post_id', '=', $postid_delete)
					->whereIn('lkp_post_status_id', $array_post_status)
					->where('is_private', 0)
					->select('*')->get();
					return count($checkServiceForSeller);
					
				case AIR_INTERNATIONAL  :
					$checkServiceForSeller = DB::table('airint_seller_post_items')->where('seller_post_id', '=', $postid_delete)
					->whereIn('lkp_post_status_id', $array_post_status)
					->where('is_private', 0)
					->select('*')->get();
					return count($checkServiceForSeller);
					
				case OCEAN       		:
					$checkServiceForSeller = DB::table('ocean_seller_post_items')->where('seller_post_id', '=', $postid_delete)
					->whereIn('lkp_post_status_id', $array_post_status)
					->where('is_private', 0)
					->select('*')->get();
					return count($checkServiceForSeller);
					
				case COURIER     		:
					$checkServiceForSeller = DB::table('courier_seller_post_items')->where('seller_post_id', '=', $postid_delete)
					->whereIn('lkp_post_status_id', $array_post_status)
					->where('is_private', 0)
					->select('*')->get();
					return count($checkServiceForSeller);
					
				case RELOCATION_DOMESTIC:
					$checkServiceForSeller = DB::table('seller_post_items')->where('seller_post_id', '=', $postid_delete)
					->whereIn('lkp_post_status_id', $array_post_status)
					->where('is_private', 0)
					->select('*')->get();
					return count($checkServiceForSeller);
					break;
				default :
					$checkServiceForSeller = DB::table('seller_post_items')->where('seller_post_id', '=', $postid_delete)
					->whereIn('lkp_post_status_id', $array_post_status)
					->where('is_private', 0)
					->select('*')->get();
					return count($checkServiceForSeller);
					break;
			}
		}
		catch(Exception $e)
		{
			return $e->message;
		}
	}
	
	
	/** Retrieval of IView Count in Term Posts **/
	public static function termViewBuyerCountUpdate($termQuoteId,$serviceId,$userId) {
		try {			
			$table = 'term_buyer_quote_item_views';
			$getviewcount = DB::table($table)
			->where($table.'.term_buyer_quote_id','=',$termQuoteId)
			->select($table.'.id',$table.'.view_counts')
			->get();			
			if(isset($getviewcount[0]->id) && isset($getviewcount[0]->id)!=''){	
				$updateview = DB::table($table)
				->where($table.'.term_buyer_quote_id','=',$termQuoteId)
				->update(array(
						'view_counts' =>$getviewcount[0]->view_counts+1));	
			}else{
				$created_at  = date ( 'Y-m-d H:i:s' );
				$createdIp = $_SERVER['REMOTE_ADDR'];				
				$viewcount = new TermBuyerQuoteItemView();
				$viewcount->user_id = $userId;
				$viewcount->term_buyer_quote_id= $termQuoteId;
				$viewcount->lkp_service_id =$serviceId;
				$viewcount->view_counts =1;
				$viewcount->created_at  =$created_at;
				$viewcount->created_ip = $createdIp;
				$viewcount->save();	
			}
			return $getviewcount[0]->view_counts;
	
		} catch(\Exception $e) {
			//return $e->message;
		}
	}
	
	//Dispaly Count no of views in term posts
	public static function termDisplayViewCount($buyerQuoteId,$serviceId) {
		try {
			$countview = DB::table('term_buyer_quote_item_views as tbqvi')			
			->where('tbqvi.term_buyer_quote_id', '=', $buyerQuoteId)
			->where('tbqvi.lkp_service_id', '=', $serviceId)
			->select('tbqvi.id', 'tbqvi.view_counts')
			->get();
			if (!isset($countview[0]->view_counts)) {
				$countview = 0;
			} else {
				$countview = $countview[0]->view_counts;
			}
			return $countview;
		} catch (Exception $exc) {			
		}
	}

	
	// get seller selected private buyer post 
	public static function getPrivateBuyerMatchedResults($service,$pid) {
		try {
			if($service == ROAD_FTL)
				$table = 'buyer_quote_sellers_quotes_prices';
			elseif($service == ROAD_PTL)
				$table = 'ptl_buyer_quote_sellers_quotes_prices';
			elseif($service == RAIL)
				$table = 'rail_buyer_quote_sellers_quotes_prices';
			elseif($service == AIR_DOMESTIC)
				$table = 'airdom_buyer_quote_sellers_quotes_prices';
			elseif($service == AIR_INTERNATIONAL)
				$table = 'airint_buyer_quote_sellers_quotes_prices';
			elseif($service == OCEAN)
				$table = 'ocean_buyer_quote_sellers_quotes_prices';
			elseif($service == COURIER)
				$table = 'courier_buyer_quote_sellers_quotes_prices';
			elseif($service == ROAD_TRUCK_HAUL)
				$table = 'truckhaul_buyer_quote_sellers_quotes_prices';
			elseif($service == ROAD_TRUCK_LEASE)
				$table = 'trucklease_buyer_quote_sellers_quotes_prices';
			elseif($service == RELOCATION_DOMESTIC)
				$table = 'relocation_buyer_quote_sellers_quotes_prices';
			elseif($service == RELOCATION_PET_MOVE)
				$table = 'relocationpet_buyer_quote_sellers_quotes_prices';
			elseif($service == RELOCATION_OFFICE_MOVE)
				$table = 'relocationoffice_buyer_quote_sellers_quotes_prices';
			elseif($service == RELOCATION_INTERNATIONAL)
				$table = 'relocationint_buyer_quote_sellers_quotes_prices';

			$privatebuyerid = DB::table($table);		
			$privatebuyerid->where($table.'.private_seller_quote_id', '=', $pid);
			if($service == ROAD_FTL || $service == ROAD_TRUCK_HAUL || $service == ROAD_TRUCK_LEASE){
			$privatebuyerid->select($table.'.buyer_quote_item_id');
			}
			else{
			$privatebuyerid->select($table.'.buyer_quote_id');
			}
			
			$privatedetails = $privatebuyerid->get();
			
			if($service == ROAD_FTL || $service == ROAD_TRUCK_HAUL|| $service == ROAD_TRUCK_LEASE){
				if (!isset($privatedetails[0]->buyer_quote_item_id)) {
					$pbuyerid = 0;
				} else {
					$pbuyerid = $privatedetails[0]->buyer_quote_item_id;
				}
			}else{
				if (!isset($privatedetails[0]->buyer_quote_id)) {
					$pbuyerid = 0;
				} else {
					$pbuyerid = $privatedetails[0]->buyer_quote_id;
				}
			}
			return $pbuyerid;

		} catch (Exception $exc) {
		}
	}
	
    //no of loads calculation
	public static function ftlNoofLoads($vehicle_type_id,$qty=NULL) {
		try {
                    $vehicle_types_Value = DB::table('lkp_vehicle_types')->select('capacity','units')->where('id', $vehicle_type_id)->get();
                    $vehicle_type_id = $vehicle_types_Value[0]->capacity;
                    if($qty!=""){
                    	if($vehicle_types_Value[0]->units!="KG")
                    		$quantity = $qty;
                    	else
                    		$quantity = $qty*1000;
                    }else{
                    	
	                    if($vehicle_types_Value[0]->units!="KG")
	                        $quantity = session('searchMod.quantity_buyer');
	                    else
	                        $quantity = session('searchMod.quantity_buyer')*1000;
                    }
                    $noofloads = ceil($quantity / $vehicle_type_id);
			return $noofloads;

		} catch (Exception $exc) {			
		}
	}
	
	//get Courier slab values from tables
	public static function getCourierSlabValues($sellerPostId) {
		try {
			$seller_post_slab_values  = DB::table('courier_seller_posts')
			->join ( 'courier_seller_post_items', 'courier_seller_post_items.seller_post_id', '=', 'courier_seller_posts.id' )
			->join ( 'courier_seller_post_item_slabs', 'courier_seller_post_item_slabs.seller_post_id', '=', 'courier_seller_posts.id' )
			->where('courier_seller_post_items.id',$sellerPostId)
			->select('courier_seller_post_item_slabs.*')
			->get();
			return $seller_post_slab_values;
	
		} catch (Exception $exc) {
		}
	}

	/*
	*
	*/
	public static function smsStatus($status){
		$status_array = array(
				0 => 'Message In Queue',
				1 => 'Submitted To Carrier',
				2 => 'Un Delivered',
				3 => 'Delivered',
				4 => 'Expired',
				8 => 'Rejected',
				9 => 'Message Sent',
				10 => 'Opted Out Mobile Number',
				11 => 'Invalid Mobile Number',
			);

		if(isset($status_array[$status]))
			return $status_array[$status];
		else
			return "Invalid status request";
	}

	/*
	*	Send SMS Component
	*/
	public static function sendSMS($phone=array(),$smsEventId,$params=array()){
		if(SMS_GATEWAY_ENABLED==0){
			return;
		}
		$error = array();
		
		if(empty($phone))
			$error[] = "Phone Number Required";

		if(empty($smsEventId))
			$error[] = "SMS Event ID Required";

		if($error){
			return $error;
		}else{
			$mobilenos = implode(',',$phone);
			$sender_id = (isset(Auth::user()->id)) ? Auth::user()->id : 0;
			$created_at  = date ( 'Y-m-d H:i:s' );
			$createdIp = $_SERVER['REMOTE_ADDR'];

			//*** Getting SMS Template Body ***//
			$msg_template = DB::table('lkp_sms_templates')
				->where(['lkp_sms_event_id' => $smsEventId ])
				->select('lkp_sms_templates.*')
				->get();

			$msg_template_id = $msg_template[0]	->id;

			$body = $msg_template[0]->body;

			// Replacing params in templage
				if($params && is_array($params)){
					foreach ($params as $key=>$value){
						$body = str_replace("{!! $key !!}", $value, $body );
					}
				}

				$site_url =  url();

				//replace site url for image paths.
				$body = str_replace("{!! site_url !!}", $site_url, $body );

				$serviceId = Session::get('service_id');
				switch ($serviceId) {

					case ROAD_PTL :
						$body = str_replace("FTL", 'PTL', $body );
						break;
					case ROAD_INTRACITY :
						$body = str_replace("FTL", 'INTRA', $body );
						break;
					case AIR_DOMESTIC :
						$body = str_replace("FTL", 'AIRDOMESTIC', $body );
						break;
					case RAIL :
						$body = str_replace("FTL", 'RAIL', $body );
						break;
					case OCEAN :
						$body = str_replace("FTL", 'OCEAN', $body );
						break;
					case AIR_INTERNATIONAL :
						$body = str_replace("FTL", 'AIRINTERNATIONAL', $body );
						break;
					default :
						$body = str_replace("FTL", 'FTL', $body );
						break;
				}

			// Send SMS Params	
			$sms_params	= array(
					'mobilenos'=>$mobilenos,
					'body' => $body,
					'requestType' => 'bulk',
				);
			
			// Send SMS request
			$curlresponse = CommonComponent::smsApiRequest($sms_params);
			
			$job_id = str_replace('OK:','',$curlresponse);

			// Save in SMS LOG
			foreach($phone as $key=>$value){
				$saveSmsLog =  new LogUserSms;
				$saveSmsLog->lkp_sms_template_id = $msg_template_id;
				$saveSmsLog->sender_user_id      = $sender_id;
				$saveSmsLog->job_id              = $job_id;
				$saveSmsLog->mobile_no           = $value;
				$saveSmsLog->template_message    = '';
				$saveSmsLog->converted_message   = $body;
				$saveSmsLog->is_sent             = 0;
				$saveSmsLog->created_at          = $created_at;
				$saveSmsLog->updated_at          = $created_at;
				$saveSmsLog->created_by          = $sender_id;
				$saveSmsLog->updated_by          = $sender_id;
				$saveSmsLog->created_ip          = $createdIp;
				$saveSmsLog->updated_ip          = $createdIp;
				$saveSmsLog->message_status          = 0;
				$saveSmsLog->save();
			}
		}
	}
	
	//checking document is there or not in crete quote
	public static function getMobleNumber($user_id){
		try {
			
			
			$role_id = DB::table('users')->where('id', $user_id)
						->select('lkp_role_id')
						->first();
			
			$user_phone = DB::table('users');
			$user_phone_number = $user_phone->where('users.id', $user_id);
			//if(($role_id->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER)){
			if($role_id->lkp_role_id == SELLER){
			 //if($role_id->lkp_role_id == SELLER){
				$user_phone_number->leftJoin('seller_details as c2', function($join)
				{
					$join->on('users.id', '=', 'c2.user_id');
					$join->on(DB::raw('users.is_business'),'=',DB::raw(0));


				});
				$user_phone_number->leftJoin('sellers as cc2', function($join)
				{
					$join->on('users.id', '=', 'cc2.user_id');
					$join->on(DB::raw('users.is_business'),'=',DB::raw(1));


				});

			//}else if(($role_id->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER)){
			}else if($role_id->lkp_role_id == BUYER){
				$user_phone_number->leftJoin('buyer_details as c2', function($join)
				{
					$join->on('users.id', '=', 'c2.user_id');
					$join->on(DB::raw('users.is_business'),'=',DB::raw(0));
					
				
				});
				$user_phone_number->leftJoin('buyer_business_details as cc2', function($join)
				{
					$join->on('users.id', '=', 'cc2.user_id');
					$join->on(DB::raw('users.is_business'),'=',DB::raw(1));
					
				
				});
			}
			if($role_id->lkp_role_id == SELLER){
				$user_phone_number->select(DB::raw("(case when users.is_business = 1 then cc2.contact_mobile when users.is_business = 0 then c2.contact_mobile end) as 'phone'"));
			}else if( $role_id->lkp_role_id == BUYER ){
				$user_phone_number->select(DB::raw("(case when users.is_business = 1 then cc2.contact_mobile when users.is_business = 0 then c2.mobile end) as 'phone'"));
					
			}
			
			$data	=	$user_phone_number->first();
			if($data->phone)
				return array($data->phone);
			else 
				return false;
			
		} catch ( Exception $exc ) {
				
		}
	}
	
	
	
	/*
	*	Update SMS Status using cron API Request
	*/
	
	//checking document is there or not in crete quote
	public static function getTransactionNumber($buyer_quote_id){
		try {
			$serviceId = Session::get('service_id');
			$transaction_id_number = DB::table('term_buyer_quotes')
			->where('id', $buyer_quote_id)
			->where('lkp_service_id', $serviceId)
			->select('transaction_id')
			->first();
			return $transaction_id_number->transaction_id;
				
		} catch ( Exception $exc ) {
	
		}
	}
	
	/*
	 *	Update SMS Status using cron API Request
	*/
	public static function storeSmsStatus(){
		$results = DB::table('log_user_sms as lus')			
			->where('lus.is_sent', '=', 0)
			->select(DB::raw('DATE_FORMAT(min(lus.created_at),"%d/%m/%Y 00:00:00") as from_date, DATE_FORMAT(max(lus.created_at),"%d/%m/%Y 23:59:59") as to_date'))
			->first();	
		if($results)	{
			$sms_params = array(
				'requestType'=>'report',
				'from'=>$results->from_date,
				'to'=>$results->to_date
			);	
			$curlresponse = CommonComponent::smsApiRequest($sms_params);
			if(!empty($curlresponse)){
				$response_slice = explode('#',$curlresponse);
				foreach($response_slice as $slice){
					$slice_values = explode('~',$slice);

					if($slice_values[2]==3) // Delivered case
						$is_sent = 1;
					else if($slice_values[2]==11) // Invalid Number case
						$is_sent = 2;
					else
						$is_sent = 0; // other Cases
					

					$update_status = DB::table('log_user_sms')
						->where('job_id','=',$slice_values[0])
						->where('mobile_no','=',(is_numeric($slice_values[1]))?substr($slice_values[1],2):$slice_values[1])
						->where('is_sent','=',0)
						->update(array(
								'is_sent' 			=> $is_sent,
								'message_status'	=> $slice_values[2],
								'updated_at'		=> date ( 'Y-m-d H:i:s' )
						));
				}
			}	
		}
	}

	/*
	*	SMS API Request send/reports
	*/
	public static function smsApiRequest($params=array()){
		//Please Enter Your Details
		$user="Logistiks"; //your username
		$password= "$"."Marketplacelogi"; //your password

		if($params['requestType']=='report'){
			$fromdate = $params['from'];
			$todate = $params['to'];
			$url="http://api.smscountry.com/smscwebservices_bulk_reports.aspx?";
			$postFields = "User=$user&passwd=$password&fromdate=$fromdate&todate=$todate";
			/*echo $postFields;
			exit;*/
		}else{
			$mobilenumbers=$params['mobilenos']; //enter Mobile numbers comma seperated
			$message = $params['body']; //enter Your Message
			$senderid="Logistiks"; //Your senderid
			$messagetype="N"; //Type Of Your Message
			$DReports="Y"; //Delivery Reports
			$message = urlencode($message);

			$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx";
			$postFields = "User=$user&passwd=$password&mobilenumber=$mobilenumbers&message=$message&sid=$senderid&mtype=$messagetype&DR=$DReports";
		}	
		$ch = curl_init();
		if (!$ch){die("Couldn't initialize a cURL handle");}
		$ret = curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt ($ch, CURLOPT_POSTFIELDS,$postFields);
		$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//If you are behind proxy then please uncomment below line and provide your proxy ip with port.
		// $ret = curl_setopt($ch, CURLOPT_PROXY, "PROXY IP ADDRESS:PORT");
		$curlresponse = curl_exec($ch); // execute

		if (curl_errno($ch) || empty($ret)) {
			// some kind of an error happened
			return curl_error($ch);
			curl_close($ch); // close cURL handler
		} else {
			$info = curl_getinfo($ch);
			curl_close($ch); // close cURL handler
			return $curlresponse; //echo "Message Sent Succesfully" ;
		}	
	}

	/*
	*	Payment Gateway Components
	*/

	public static function hdfcFields($params){

		$hashData = HDFC_PAYMENT_GATEWAY_ACCOUNT_SECRET_KEY;
		$hashMethod = HDFC_HASHING_METHOD;
		$user_det = CommonComponent::getUserNameDetails();
		$payment_mode = array('CC'=>1,'DB'=>2,'NB'=>3);

        $PaymentFields = array(
                'account_id'         => HDFC_PAYMENT_GATEWAY_ACCOUNT_ID,
                'address'            => $user_det->address,
                'amount'             => $params['amount'],
                'channel'            => '10',
                'city'               => $user_det->principal_place,
                'country'            => CURRENCY_COUNTRY,
                'currency'           => CURRENCY_TYPE,
                'description'        => 'Test Product',
                'email'              => $user_det->contact_email,
                'mode'               => HDFC_PAYMENT_GATEWAY_MODE,
                'name'               => $user_det->contact_firstname,
                'phone'              => $user_det->contact_mobile,
                'postal_code'        => '400069',
                'reference_no'       => $params['refference_id'],
                'return_url'         => url(HDFC_PAYMENT_GATEWAY_RETURN_URL_ACTION),
                'payment_mode'		 => $payment_mode[$params['payment_mode']],
                //'ship_address'       => 'Test Address',
                //'ship_city'          => 'Mumbai',
                //'ship_country'       => 'IND',
                //'ship_name'          => 'Test Name',
                //'ship_phone'         => '2211112222',
                //'ship_postal_code'   => '400069',
                //'ship_state'         => 'MH',
                //'state'              => 'MH',
            );
		
		/*echo "Has Data: ".$hashData;
		echo "<br />Hash Method: ".$hashMethod;
		echo "<br />Payment Fields: ";
		print_r($PaymentFields);*/


		ksort($PaymentFields);

		foreach($PaymentFields as $key=>$value){
			if (strlen($value) > 0) {
				$hashData .= '|'.$value;
			}
		}

		if (strlen($hashData) > 0) {
			$PaymentFields['secure_hash'] = strtoupper(hash($hashMethod, $hashData));
		}

		/*echo "<pre>";
		print_r($PaymentFields); 
		echo "</pre>";
		exit;*/
		return $PaymentFields;
	}
	

	/******* Below Script for get seller list from city************** */
	 
	public static function getAllSellerList($cities = array())
	{
		//print_r($_POST['seller_list']); exit;
		$results=array();
		try
		{
			//$allPincodeIds = $_POST['seller_list'];
			$serviceId = Session::get('service_id');
	
			//Check district match condition for seller in buyer private posts.
			//$term = Input::get('q');
	
			$sellerlist = (count($cities) > 0) ? $cities : $_POST['seller_list'];
			 
			if(isset($sellerlist)){
				$sellersStr = $sellerlist;
	
				$districts = DB::table('lkp_ptl_pincodes')
				->whereIn('lkp_ptl_pincodes.id', $sellersStr)
				->select('lkp_ptl_pincodes.lkp_district_id')
				->get();
				//$district_array	=	array();
				foreach ($districts as $dist) {
					$district_array[] = $dist->lkp_district_id;
					//print_r($district_array); die;
				}
			}
			$seller_data = array();
	
	
			switch($serviceId){
				case ROAD_PTL:
					$seller_data = DB::table('ptl_seller_post_items')
					->join('users','ptl_seller_post_items.created_by','=','users.id')
					->leftjoin ('sellers', 'users.id', '=', 'sellers.user_id')
					->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
					->leftjoin ('ptl_seller_posts', 'users.id', '=', 'ptl_seller_posts.seller_id')
					->distinct('ptl_seller_post_items.created_by')
					->whereIn('ptl_seller_post_items.lkp_district_id',$district_array)
					->where('users.lkp_role_id',SELLER)
					->where('ptl_seller_posts.lkp_ptl_post_type_id',PTL_LOCATION)
					->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
					->get();
					break;
				case RAIL:
					$seller_data = DB::table('rail_seller_post_items as spi')
					->join('users','spi.created_by','=','users.id')
					->leftjoin ('sellers', 'users.id', '=', 'sellers.user_id')
					->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
					->leftjoin ('rail_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
					->distinct('spi.created_by')
					->whereIn('spi.lkp_district_id',$district_array)
					->where('users.lkp_role_id',SELLER)
					->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)
					->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
					->get();
					break;
				case AIR_DOMESTIC:
					$seller_data = DB::table('airdom_seller_post_items as spi')
					->join('users','spi.created_by','=','users.id')
					->leftjoin ('sellers', 'users.id', '=', 'sellers.user_id')
					->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
					->leftjoin ('airdom_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
					->distinct('spi.created_by')
					->whereIn('spi.lkp_district_id',$district_array)
					->where('users.lkp_role_id',SELLER)
					->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)
					->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
					->get();
					break;
				case AIR_INTERNATIONAL:
					$seller_data = DB::table('airint_seller_post_items as spi')
					->join('users','spi.created_by','=','users.id')
					->leftjoin ('sellers', 'users.id', '=', 'sellers.user_id')
					->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
					->leftjoin ('airint_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
					->distinct('spi.created_by')
					->whereIn('spi.lkp_district_id',$district_array)
					->where('users.lkp_role_id',SELLER)
					->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)
					->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
					->get();
					break;
				case OCEAN:
					$seller_data = DB::table('ocean_seller_post_items as spi')
					->join('users','spi.created_by','=','users.id')
					->leftjoin ('sellers', 'users.id', '=', 'sellers.user_id')
					->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
					->leftjoin ('ocean_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
					->distinct('spi.created_by')
					->whereIn('spi.lkp_district_id',$district_array)
					->where('users.lkp_role_id',SELLER)
					->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)
					->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
					->get();
					break;
				case COURIER:
					$seller_data = DB::table('courier_seller_post_items as spi')
					->join('users','spi.created_by','=','users.id')
					->leftjoin ('sellers', 'users.id', '=', 'sellers.user_id')
					->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
					->leftjoin ('courier_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
					->distinct('spi.created_by')
					->whereIn('spi.lkp_district_id',$district_array)
					->where('users.lkp_role_id',SELLER)
					->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)
					->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
					->get();
					break;
			}
			//print_r($seller_data); exit;
			foreach ($seller_data as $query) {
				$results[] = ['id' => $query->id, 'name' => $query->username.' '.$query->principal_place.' '.$query->id];
			}
			if(count($cities) > 0){
				return $results;
			}else{
				return Response::json($results);
			}
		} catch (Exception $e)
		{
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}
        
        /** Retrieval of all Vehicle Types**/
	public static function getQtyBasedAllVehicleTypes($qty)
	{
		try
		{
			
                        $qry=DB::table('lkp_vehicle_types');
                        $res=     $qry->whereRaw("(`capacity` <= $qty and `lkp_ict_weight_uom_id` =3) or lkp_ict_weight_uom_id=1")->orderBy ( 'id', 'asc' )->lists('vehicle_type', 'id');
                        //echo $qry->tosql();die;
                        
                        return $res;
			
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Retrieval of all Vehicle Types**/
	public static function getQtyHualAllVehicleTypes($qty)
	{
		try
		{
				
			$qry=DB::table('lkp_vehicle_types');
			$res=     $qry->whereRaw("(`capacity` >= $qty and `lkp_ict_weight_uom_id` =3)")->orderBy ( 'id', 'asc' )->lists('vehicle_type', 'id');
			//echo $qry->tosql();die;
	
			return $res;
				
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	//******* VOLTY GPS COMPONENTS *******//

	/*
	*	VOLTY API Vehicle Registration
	*/
	public static function gpsRegistration($params){

		$configFields = array(
				"OPTION" => VOLTY_GPS_OPTION,
				"SUB_USER" => VOLTY_GPS_SUB_USER,
				"SUPER_USER" => VOLTY_GPS_SUB_USER,
				"SUPER_PASS" => VOLTY_GPS_SUPER_PASS,
			);
		$postFields = $configFields;
		$postFields = array_merge($postFields,$params);

		return CommonComponent::gpsAPIRequest($postFields);
	}

	/*
	*	VOLTY API Vehicle Track
	*/

	public static function gpsTrack($params){
		return CommonComponent::gpsAPIRequest($params);
	}

	/*
	*	VOLTY API Vehicle Track History
	*/
	public static function gpsTrackHistory($params){
		return CommonComponent::gpsAPIRequest($params);
	}


	/**
	* VOLTY API Vehicle update
	**/
	public static function gpsStoreVehicle($params){
		
		$params = array(
				'FROM' => VOLTY_GPS_SUB_USER,
				'TO' => VOLTY_GPS_SUB_USER,
				'VEH' => array(
						'REGNO' => '',
						'TYPE' => 'None',
						'NAME' => '',
						'DATE' => '',
						'IMEI' => '',
					),
				'OPTION' => 'SHIFT'
			);

		return CommonComponent::gpsAPIRequest($params);
	}

	/**
	* VOLTY API Vehicle Device update
	**/
	public static function gpsStoreVehicleDevice($params){
		
		$configFields = array(
				"OPTION" => 'R_DEVICE',
				"SUPER_USER" => VOLTY_GPS_SUB_USER,
				"SUPER_PASS" => VOLTY_GPS_SUPER_PASS,
			);
		$postFields = $configFields;
		$postFields = array_merge($postFields,$params);

		return CommonComponent::gpsAPIRequest($postFields);
	}


	/**
	* VOLTY API Vehicle drop
	**/
	public static function gpsDestroy($regno){
		$params = array(
				"OPTION" => 'D_VEHICLE',
				"SUPER_USER" => VOLTY_GPS_SUB_USER,
				"SUPER_PASS" => VOLTY_GPS_SUPER_PASS,
				"REGNO" => $regno
			);
		
		return CommonComponent::gpsAPIRequest($params);
	}

	/*
	*	GPS API Request 
	*/
	public static function gpsAPIRequest($postFields){
		$postJson = json_encode($postFields); 
		try {
			$ch = curl_init(VOLTY_GPS_API_URL);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postJson);                                                                  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			    'Content-Type: application/json',                                                                                
			    'Content-Length: ' . strlen($postJson))                                                                       
			);                                                                                                                   
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
		} catch ( Exception $exc ) {
			curl_close($ch);
			return "Curl Error : ". $exc;
		}
	}


	
	/**
	 * Get of Lowest rate/kg Quote *
	 */
	public static function getLowestRatePercft($id) {
		try {
			$initial_quote_price = DB::table ( 'term_buyer_quote_sellers_quotes_prices' )
			->where ( 'term_buyer_quote_item_id', $id )
			->where ( 'is_submitted', 1 )
			->where ( 'lkp_service_id', Session::get ( 'service_id' ) )
			->select ( 'initial_rate_per_kg' )->lists ( 'rate_per_cft' );
			$initial_quote_price_low = min ( $initial_quote_price );
			return $initial_quote_price_low;
		} catch ( \Exception $e ) {
			// return $e->message;
		}
	}
	


        //get Followers count
	public static function getFollowers($userId) {
		try {
			$followers  = DB::table('network_followers')
			->where('network_followers.user_id',$userId)
			->select('network_followers.id','network_followers.follower_user_id')
			->get();
			return $followers;
	
		} catch (Exception $exc) {
		}
	}
        //get Partners count
	public static function getPartners($userId) {
		try {
			$partners  = DB::table('network_partners')
			->where('network_partners.user_id',$userId)
			->select('network_partners.id','network_partners.partner_user_id')
			->get();
			return $partners;
	
		} catch (Exception $exc) {
		}
	}
        
        //get Partners names list
	public static function getPartnersList($userId,$term) {
		try {
			$partners  = DB::table('network_partners')
                        ->leftjoin('users','network_partners.partner_user_id','=','users.id')        
			->where('network_partners.user_id',$userId)
                        ->where('users.id', '!=', Auth::user()->id)        
                        ->where(['users.is_active' => 1])
                        ->where('users.username', 'LIKE', $term.'%')        
			->select('users.id','users.username')
			->get();
			return $partners;
	
		} catch (Exception $exc) {
		}
	}
        //get Indusries list
	public static function getIndustries() {
		try {
			$industries  = DB::table('lkp_industries')->orderBy ( 'industry_name', 'asc' )
			->where('is_active',1)
			->lists('industry_name', 'id');
			return $industries;
                }catch (Exception $exc) {
		}
	}	
	
	
	
	public static function getLogoPartners($userId,$searchText=null) {
		try {
			
		
			$partnersLogos  = DB::table('network_partners');
			$partnersLogos->join('users','network_partners.partner_user_id','=','users.id');
			$partnersLogos->where('network_partners.user_id',$userId);
			$partnersLogos->where('network_partners.is_approved',1);
			$partnersLogos->orWhere('network_partners.partner_user_id',$userId);
			if($searchText!=''){
				$partnersLogos->where('users.username', 'LIKE','%'.$searchText.'%');
			}
			$partnersLogos->select('network_partners.id','network_partners.user_id','network_partners.partner_user_id','users.logo','users.lkp_role_id');
			$partnersLogoResults=$partnersLogos->get();
			
			
			return $partnersLogoResults;
	
		} catch (Exception $exc) {
		}
	}
	//get user pic
	public static function getUserPic($userId) {
		try {
			$userpic  = DB::table('users')
			->where('id',$userId)
			->select('user_pic')
			->get();
				
				
			return $userpic[0]->user_pic;
	
		} catch (Exception $exc) {
		}
	}    
        
    //get Indusries list
	public static function getSpecialities() {
		try {
			$specialities  = DB::table('lkp_specialities')->orderBy ( 'speciality_name', 'asc' )
			->where('is_active',1)
			->lists('speciality_name', 'id');
			return $specialities;
	
		} catch (Exception $exc) {
		}
	}
        
        /** Retrieval of Services of user  **/
	public static function getUserServices($user_id)
	{
            $result = array();
            
		try
		{   
                    $role_id = DB::table('users')->where('id', $user_id)
						->select('lkp_role_id')
						->first();
                    //echo "user id ".$user_id." Role ".$role_id->lkp_role_id."<br/>";
			$services=array();
                        
			 if($role_id->lkp_role_id == SELLER){
                             
                            $sellerservices = DB::table ( 'seller_services as ss' )
                                ->join ( 'lkp_services as ls', 'ss.lkp_service_id', '=', 'ls.id' )
                                ->where('ss.user_id','=',$user_id)
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                            $result[] = $sellerservices;
                            
                            //print_r($ftlservices);die("ssss");
                         }else{
                             $ftlservices = DB::table ( 'buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             
                             if(count($ftlservices)==0){
                                 $ftlservices = DB::table ( 'term_buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)->where('bq.lkp_service_id 	','=',ROAD_FTL)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             
                             }
                             $result[] = $ftlservices;
                             $ltlservices = DB::table ( 'ptl_buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             if(count($ltlservices)==0){
                                 $ltlservices = DB::table ( 'term_buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)->where('bq.lkp_service_id 	','=',ROAD_LTL)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             
                             }
                             $result[] = $ltlservices;
                             $ictservices = DB::table ( 'ict_buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             $result[] = $ictservices;
                             $airdomservices = DB::table ( 'airdom_buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             if(count($airdomservices)==0){
                                 $airdomservices = DB::table ( 'term_buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)->where('bq.lkp_service_id 	','=',AIR_DOMESTIC)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             
                             }
                             $result[] = $airdomservices;
                             $airintservices = DB::table ( 'airint_buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             if(count($airintservices)==0){
                                 $airintservices = DB::table ( 'term_buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)->where('bq.lkp_service_id 	','=',AIR_INTERNATIONAL)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             
                             }
                             $result[] = $airintservices;
                             $oceanservices = DB::table ( 'ocean_buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             if(count($oceanservices)==0){
                                 $oceanservices = DB::table ( 'term_buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)->where('bq.lkp_service_id 	','=',OCEAN)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             
                             }
                             $result[] = $oceanservices;
                             $railservices = DB::table ( 'rail_buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             if(count($railservices)==0){
                                 $railservices = DB::table ( 'term_buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)->where('bq.lkp_service_id 	','=',RAIL)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             
                             }
                             $result[] = $railservices;
                             
                             $courierservices = DB::table ( 'courier_buyer_quotes as bq' )
                             ->where('bq.buyer_id','=',$user_id)
                             ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
                             ->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             
                             $result[] = $courierservices;
                             
                             $rdservices = DB::table ( 'relocation_buyer_posts as bq' )
                                ->where('bq.buyer_id','=',$user_id)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             if(count($rdservices)==0){
                                 $rdservices = DB::table ( 'term_buyer_quotes as bq' )
                                ->where('bq.buyer_id','=',$user_id)->where('bq.lkp_service_id 	','=',RELOCATION_DOMESTIC)
                                ->join ( 'lkp_services as ls', 'bq.lkp_service_id', '=', 'ls.id' )
				->select ( 'ls.id', 'ls.service_name' )->lists ( 'service_name', 'id' );
                             
                             }
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
        
	public static function getAllCommunityGroups($userid,$searchText=null){
	
		try {
				
			$getCommunityNewGroup = DB::table('community_groups');
			$getCommunityNewGroup->join('community_group_members','community_group_members.community_group_id','=','community_groups.id');
			$getCommunityNewGroup->where('community_group_members.user_id', $userid);
			if($searchText!=''){
				$getCommunityNewGroup->where('community_groups.group_name', 'LIKE','%'.$searchText.'%');
			}
			$getCommunityNewGroup->select('community_groups.id','community_groups.logo_file_name');
			$getCommunityNewGroupResult=$getCommunityNewGroup->get();
			
			return $getCommunityNewGroupResult;
				
		} catch (Exception $e) {
	
		}
	
	}

	
	public static function getLogoFollowers($userId,$searchText) {
		try {
			//$term='test';
			
			$followersLogos  = DB::table('network_followers');
			$followersLogos->join('users','network_followers.follower_user_id','=','users.id');
			$followersLogos->where('network_followers.user_id',$userId);
			if($searchText!=''){
			$followersLogos->where('users.username', 'LIKE','%'.$searchText.'%');
			}
			$followersLogos->select('network_followers.id','network_followers.follower_user_id','users.logo','users.lkp_role_id');
			$followersLogoResultss=$followersLogos->get();
			
				
			return $followersLogoResultss;
	
		} catch (Exception $exc) {
		}
	}
	
	/******* Below Script for get seller list from city************** */
	public static function getTermSellerList($cities = array()) {
		 
		//print_r($_POST['seller_list']); exit;
		$results = array();
		$serviceId = Session::get('service_id');
		try {
			Log::info('Get Seller lsit from depends on from city: ' . Auth::id(), array('c' => '1'));
			$roleId = Auth::User()->lkp_role_id;
			//Update the user activity to the buyer get seller list
			if ($roleId == BUYER) {
				CommonComponent::activityLog("BUYER_SELLERLIST", BUYER_SELLERLIST, 0, HTTP_REFERRER, CURRENT_URL);
			}
			//$term = Input::get('q');
			$sellerlist = (count($cities) > 0) ? $cities : $_POST['seller_list'];
			if(isset($sellerlist)){
				$sellersStr = $sellerlist;
				$districts = DB::table('lkp_cities')
				->whereIn('lkp_cities.id', $sellersStr)
				->select('lkp_cities.lkp_district_id')
				->get();
				//$district_array	=	array();
				foreach ($districts as $dist) {
					$district_array[] = $dist->lkp_district_id;
				}
			}
			switch($serviceId){
				case ROAD_FTL:
					$seller_data = DB::table('seller_post_items')
					->join('users', 'seller_post_items.created_by', '=', 'users.id')
					->leftjoin('sellers', 'users.id', '=', 'sellers.user_id')
					->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
					->distinct('seller_post_items.created_by')
					->whereIn('seller_post_items.lkp_district_id', $district_array)
					->where('users.lkp_role_id', SELLER)
					->orWhere('users.secondary_role_id', SELLER)
					->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
					->get();
	
					break;
				case RELOCATION_DOMESTIC:
					$seller_data = DB::table('relocation_seller_post_items')
					->join('users', 'relocation_seller_post_items.created_by', '=', 'users.id')
					->leftjoin('sellers', 'users.id', '=', 'sellers.user_id')
					->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
					->distinct('relocation_seller_post_items.created_by')
					//->whereIn('relocation_seller_post_items.lkp_district_id', $district_array)
					->where('users.lkp_role_id', SELLER)
					->orWhere('users.secondary_role_id', SELLER)
					->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
					->get();
					break;
				case RELOCATION_INTERNATIONAL:
						$seller_data = DB::table('relocation_seller_post_items')
						->join('users', 'relocation_seller_post_items.created_by', '=', 'users.id')
						->leftjoin('sellers', 'users.id', '=', 'sellers.user_id')
						->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
						->distinct('relocation_seller_post_items.created_by')
						//->whereIn('relocation_seller_post_items.lkp_district_id', $district_array)
						->where('users.lkp_role_id', SELLER)
						->orWhere('users.secondary_role_id', SELLER)
						->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
						->get();
						break;
					 
				case RELOCATION_GLOBAL_MOBILITY:
						$seller_data = DB::table('relocation_seller_post_items')
						->join('users', 'relocation_seller_post_items.created_by', '=', 'users.id')
						->leftjoin('sellers', 'users.id', '=', 'sellers.user_id')
						->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
						->distinct('relocation_seller_post_items.created_by')
						//->whereIn('relocation_seller_post_items.lkp_district_id', $district_array)
						->where('users.lkp_role_id', SELLER)
						->orWhere('users.secondary_role_id', SELLER)
						->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
						->get();
						break;
			}
	
			//print_r($seller_data); exit;
			foreach ($seller_data as $query) {
				//print_r($query); exit;
				$results[] = ['id' => $query->id, 'name' => $query->username . ' ' . $query->principal_place . ' ' . $query->id];
			}
			if(count($cities) > 0){
				return $results;
			}else{
				return Response::json($results);
			}
		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}
	}
        
        
        
        //get Members count
	public static function getMembers($id) {
		try {
			$members  = DB::table('community_group_members')
			->where('community_group_members.community_group_id',$id)
                        ->where('community_group_members.is_approved',1)        
			->select('community_group_members.id')
			->get();
			return $members;
	
		} catch (Exception $exc) {
		}
	}
	//get Members check in group
	public static function getMemberCheck($id) {
		try {
//			 $res   =   DB::table('community_group_members')
//                                 ->where('community_group_members.community_group_id',$id);
                    $members=array();
                    $qry = DB::table('community_groups');
			 $qry->leftjoin('community_group_members','community_groups.id','=','community_group_members.community_group_id')
			->where('community_groups.id',$id);
			$qry->whereRaw('community_group_members.user_id ='.Auth::id());
			$members=$qry->select('community_groups.id','community_group_members.is_approved','community_group_members.is_invited')->first();
			return $members;
	
		} catch (Exception $exc) {
		}
	}
        
        public static function getMemberAdminCheck($id) {
		try {
                    $members=array();
                    $qry = DB::table('community_groups')->where('community_groups.id',$id);
			$qry->whereRaw('community_groups.created_by='.Auth::id());
			$members=$qry->select('community_groups.id')->first();
			return $members;
	
		} catch (Exception $exc) {
		}
	}
        
 	
 	/**
     * Getting Complete Buyer Individual details
     * 
     * @param login user Id	
     * @return object
     */
    public static function getBuyerIndividualDetails( $userid = 0){
    	return \App\Models\Seller::getSellerBusinessDetails($userid);
    }

    /**
     * Getting Complete Buyer Business details
     * 
     * @param login user Id	
     * @return object
     */
    public static function getBuyerBusinessDetails( $userid = 0){
    	return \App\Models\Seller::getSellerBusinessDetails($userid);
    }

    /**
     * Getting Complete Seller Individual details
     * 
     * @param login user Id	
     * @return object
     */
    public static function getSellerIndividualDetails( $userid = 0){
    	return \App\Models\Seller::getSellerBusinessDetails($userid);
    }

    /**
     * Getting Complete Seller Business details
     * 
     * @param login user Id	
     * @return object
     */
    public static function getSellerBusinessDetails( $userid = 0){
    	return \App\Models\Seller::getSellerBusinessDetails($userid);
    }

    /**
     * Getting Feed Comments based on feed id
     * And also we'll comments count
     * 
     * @author Shriram
     * @param feed id
     * @return object
     */
    public static function feedComents( $feed_id = 0, $action='', $additionalConditions = array()){

    	$FeedComment = new \App\Models\NetworkFeedComments;

    	// Checking the Comment actions
    	switch($action):
    		case 'count':
    			return $FeedComment::where(['feed_id' => $feed_id])->get()->count();
    		break;

    		case 'disp':
    		default:
    			return $FeedComment::get_feed_comments($feed_id, 0, true);
    	endswitch;
    }
    
    /** Insert profile count whlile user came to the profile page * */
    public static function getProfileCount($id) {
    		
    		if($id != Auth::User()->id){
    		$previous_login = Auth::User()->previous_login;
    		$current_login = Auth::User()->current_login;
    		
    		$user_view_date = DB::table('user_profile_views');
    		$user_view_date->where('viewer_user_id',$id);
    		$user_view_date->where('user_id',Auth::User()->id);
    		$user_date = $user_view_date->select('user_last_login')->get();
    		
    		if(!empty($user_date)){
    		$user_date_format = $user_date[0]->user_last_login;
    		}else{
    		$user_date_format = "0000-00-00 00:00:00";
    		}
    	
    		//echo $previous_login."<br/>";
    		//echo $user_date_format."<br/>";
    		//echo $current_login."<br/>";
    		//exit;
    		
    		$exits_user = CommonComponent::check_in_range($previous_login, $current_login, $user_date_format);
		    		if($exits_user){	
		    		$getProfileCount = new UserProfileView();
		    		$getProfileCount->user_id = Auth::User()->id;
		    		$getProfileCount->viewer_user_id = $id;
		    		$getProfileCount->user_last_login = date('Y-m-d H:i:s');
		    		$getProfileCount->created_at = date('Y-m-d H:i:s');
		    		$getProfileCount->created_ip = $_SERVER['REMOTE_ADDR'];
		    		$getProfileCount->created_by = Auth::User()->id;
		    		$getProfileCount->save();
		    		}
    		}
    		
    }
    
    
    public static function profileViewCount()
    {
    	$previous_login = Auth::User()->previous_login;
    	$current_login = Auth::User()->current_login;
    	
    	$user_view_date = DB::table('user_profile_views');
    	$user_view_date->where('viewer_user_id',Auth::User()->id);
    	$user_view_date->whereRaw ("(`user_last_login` between  '$previous_login' and '$current_login')");
    	$user_date_count = $user_view_date->select('user_last_login')->count();
    	return $user_date_count;
    }
    
    public static function str_replace_last( $search , $replace , $str ) {
    	if( ( $pos = strrpos( $str , $search ) ) !== false ) {
    		$search_length  = strlen( $search );
    		$str    = substr_replace( $str , $replace , $pos , $search_length );
    	}
    	return $str;
    }
    
    
    public static function check_in_range($start_date, $end_date, $date_from_user)
    {
    	// Convert to timestamp
    	$start_ts = strtotime($start_date);
    	$end_ts = strtotime($end_date);
    	$user_ts = strtotime($date_from_user);
    
    	// Check that user date is between start & end
    	if (($user_ts >= $start_ts) && ($user_ts <= $end_ts)){
    		return '0';
    		
    	}else{
    		return '1';
    		
    	}
    }
    
    
    /** Network Request Count for users * */
    public static function getNetworkRequestCount() {
    	
    	$qry = DB::table('network_partners as np');
    	$qry->where('np.partner_user_id',Auth::User()->id);
    	$getNetworkRequestCount = $qry->where('np.is_approved',0)->count();
    	
    	$qry_recommendations = DB::table('network_recommendations as nr');
    	$qry_recommendations->where('nr.recommended_to',Auth::User()->id);
    	$getnetworkRecommendationCount = $qry_recommendations->where('nr.is_approved',0)->count();
    	
    	
    	$qry_group_members = DB::table('community_group_members as cgm');
    	$qry_group_members->where('cgm.user_id',Auth::User()->id);
    	$qry_group_members->where('cgm.is_invited',0)->count();
    	$getnetworkgroupmembersCount = $qry_group_members->where('cgm.is_approved',0)->count();
    	
    	$network_count = $getNetworkRequestCount+$getnetworkRecommendationCount+$getnetworkgroupmembersCount;
    	if($network_count>0){
    	return $network_count;
    	}else{
    	return '';	
    	}
    	
    }

    /**
     * Getting Feed Comments based on feed id
     * And also we'll comments count
     * 
     * @author Shriram
     * @param feed id
     * @return object
     */
    public static function feedLikes( $feed_id = 0, $action=''){

    	$FeedLikes = new \App\Models\NetworkFeedLikes;
    	$userID = Auth::User()->id;

    	// Checking the Feed Likes
    	switch($action):
    		case 'count':
    			$feed = $FeedLikes::selectRaw('count(*) as likeCount')
    				->where(['feed_id' => $feed_id,'is_liked' => 1])
    				->first();
    			return $feed->likeCount;
    			break;

    		case 'check':
    			$feed = $FeedLikes::selectRaw('count(*) as likeCount')->where(['feed_id' => $feed_id,
    				'user_id' => $userID, 'is_liked' => 1 ])->first();
				return $feed->likeCount;
    			break;

    		default:
    			return '';
    	endswitch;
    }

	public static function getDriverAvailabilities(){
		$driver_availability = array("1"=>"With Driver","0"=>"Without Driver");
		return $driver_availability;
	}
	
	public static function getPreferedGoods($sid){
		
		
		$seller_prefered_goods= DB::table('trucklease_seller_post_item_goods')
		->leftjoin('lkp_load_types as lt','trucklease_seller_post_item_goods.lkp_load_type_id','=','lt.id')
		->where('trucklease_seller_post_item_goods.seller_post_item_id',$sid)
		->select('lt.load_type')
		->get();
		
		$prefered_goods='';
		foreach($seller_prefered_goods as $seller_prefered_good){
			
			$prefered_goods=$prefered_goods.$seller_prefered_good->load_type.",";
		}
		
		return $prefered_goods;
	
	}
	
	public static function getPreferedGoodids($sid){
	
	
		$seller_prefered_goods= DB::table('trucklease_seller_post_item_goods')
		->where('trucklease_seller_post_item_goods.seller_post_item_id',$sid)
		->select('trucklease_seller_post_item_goods.lkp_load_type_id')
		->get();
	
		$prefered_good_ids='';
		foreach($seller_prefered_goods as $seller_prefered_good){
				
			$prefered_good_ids=$prefered_good_ids.$seller_prefered_good->lkp_load_type_id.",";
		}
	
		return $prefered_good_ids;
	
	}
	//Get details for State Permits
	public static function getPermitStates($sid){
	
	
		$seller_permit= DB::table('trucklease_seller_post_item_state_permits')
		->where('trucklease_seller_post_item_state_permits.seller_post_item_id',$sid)
		->select('trucklease_seller_post_item_state_permits.lkp_state_id')
		->get();
	
		$prefered_states_ids='';
		foreach($seller_permit as $seller_pstates){
	
			$prefered_states_ids=$prefered_states_ids.$seller_pstates->lkp_state_id.",";
		}
	
		return $prefered_states_ids;
	
	}
	

	 /** Check of Truck Haul Service for user  **/
	public static function checkSellerForTruckHaul($user_id)
	{
		$sellertruckhaulservice = DB::table ( 'seller_services as ss' )
                                ->where('ss.user_id','=',$user_id)
                                ->where('ss.lkp_service_id','=',ROAD_TRUCK_HAUL)
				->select ( 'ss.id' )->get ();
        if(count($sellertruckhaulservice) > 0){
        	return '1';
        } else{
        	return '0';
        }             
                     
	}

	 /** Get Details of FTL seller post item for Truck Haul Service for user  **/
	public static function getFTlSellerPostItForTruckHaul($seller_post_item_id, $fieldName,$orderid=NULL)
	{
            if($seller_post_item_id!=0){
		if($fieldName == "from_date" || $fieldName == "to_date"){
			$fieldNameClause = "sp.".$fieldName;
		}else{
			$fieldNameClause = "spi.".$fieldName;
		}
		$sellertruckhaulservice = DB::table ( 'seller_post_items as spi' )
								->join('seller_posts as sp','spi.seller_post_id','=','sp.id')
                                ->where('spi.id','=',$seller_post_item_id)
				->select ( $fieldNameClause)->get ();  
		if($fieldName == "from_date" || $fieldName == "to_date"){
			if(isset($sellertruckhaulservice[0]->$fieldName)){
				return commonComponent::convertDateDisplay($sellertruckhaulservice[0]->$fieldName) ;
			}
		}else{
			if(isset($sellertruckhaulservice[0]->$fieldName)){
				return $sellertruckhaulservice[0]->$fieldName ;
			}
		} 
            }elseif($orderid!=''){
                if($fieldName == "from_date"){
			$fieldNameClause = "dispatch_date";
		}elseif($fieldName == "to_date"){
			$fieldNameClause = "delivery_date";
		}elseif($fieldName == "from_location_id"){
			$fieldNameClause = "from_city_id";
		}elseif($fieldName == "to_location_id"){
			$fieldNameClause = "to_city_id";
		}elseif($fieldName == "price"){
			$fieldNameClause = "price";
		}else{
			$fieldNameClause = $fieldName;
		}
                $sellertruckhaulservice = DB::table ( 'orders' )
				->where('orders.id','=',$orderid)
				->select ( $fieldNameClause)->get ();  
                
                if($fieldName == "from_date" || $fieldName == "to_date"){
			if(isset($sellertruckhaulservice[0]->$fieldNameClause)){
				return commonComponent::convertDateDisplay($sellertruckhaulservice[0]->$fieldNameClause) ;
			}
		}else{
			if(isset($sellertruckhaulservice[0]->$fieldNameClause)){
				return $sellertruckhaulservice[0]->$fieldNameClause ;
			}
		}
                
            }		
				
                 
	}
        
        /** Retrieval of cart item  **/
	public static function CheckCart($bqid,$spid)
	{
		try
		{//echo "here".$bqid;
                    $serviceId  =   Session::get('service_id');
                    //checking for item data in cart table
                    $qry = DB::table ( 'cart_items as ci' );
                    $qry->where('ci.buyer_id',Auth::user()->id)
                            ->where('ci.lkp_service_id', Session::get('service_id'));
                    switch($serviceId){
			case ROAD_FTL       :
                        case ROAD_INTRACITY :
                        case ROAD_TRUCK_HAUL :  
                        case ROAD_TRUCK_LEASE :        
                            $qry->where('ci.buyer_quote_item_id',$bqid);
                            
                            break;
                        case ROAD_PTL       :
                        case RAIL       :
                        case AIR_DOMESTIC       :
                        case AIR_INTERNATIONAL       : 
                        case OCEAN       :     
                        case COURIER       :     
                        case RELOCATION_DOMESTIC:
                        case RELOCATION_PET_MOVE:
                        case RELOCATION_INTERNATIONAL:
                            $qry->where('ci.buyer_quote_id',$bqid);
                            break;
                            
                    }
                    $qry->where('ci.seller_post_item_id',$spid);
                    $cart   =   $qry->select ( 'ci.id' )->first();  
                    //echo "<pre>";print_r($cart);//exit;
                    //checking for item data in orders table
                    $query = DB::table ( 'orders' );
                    $query->where('orders.buyer_id',Auth::user()->id)
                            ->where('orders.lkp_service_id', Session::get('service_id'));
                    switch($serviceId){
			case ROAD_FTL       : 
                        case ROAD_INTRACITY : 
                        case ROAD_TRUCK_HAUL :  
                        case ROAD_TRUCK_LEASE :      
                            $query->where('orders.buyer_quote_item_id',$bqid);
                            break;
                        case ROAD_PTL   :
                        case RAIL       :
                        case AIR_DOMESTIC       :
                        case AIR_INTERNATIONAL  : 
                        case OCEAN       :     
                        case COURIER     :     
                        case RELOCATION_DOMESTIC:
                        case RELOCATION_PET_MOVE:  
                        case RELOCATION_INTERNATIONAL:      
                            $query->where('orders.buyer_quote_id',$bqid);
                            break;
                            
                    }
                    $query->where('orders.seller_post_item_id',$spid);
                    $order   =   $query->select ( 'orders.id' )->first(); 
                    //echo "<pre>";print_r($order);//exit;
                    if(empty($cart) && empty($order)){
                        return 0;
                    }else{
                        return 1;
                    }
			
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}


	//checking document is there or not in crete quote
	public static function getUserNameFromRole($role,$user_id){
		try {		
			$nameStr = '';
			$user_name = DB::table('users');
			$user_name_arr = $user_name->where('users.id', $user_id);
			
			 if($role == SELLER){
				$user_name_arr->leftJoin('seller_details as c2', function($join)
				{
					$join->on('users.id', '=', 'c2.user_id');
					$join->on(DB::raw('users.is_business'),'=',DB::raw(0));
					
						
				});
				$user_name_arr->leftJoin('sellers as cc2', function($join)
				{
					$join->on('users.id', '=', 'cc2.user_id');
					$join->on(DB::raw('users.is_business'),'=',DB::raw(1));
					
						
				});
				
			}else if( $role == BUYER ){
				$user_name_arr->leftJoin('buyer_details as c2', function($join)
				{
					$join->on('users.id', '=', 'c2.user_id');
					$join->on(DB::raw('users.is_business'),'=',DB::raw(0));
					
				
				});
				$user_name_arr->leftJoin('buyer_business_details as cc2', function($join)
				{
					$join->on('users.id', '=', 'cc2.user_id');
					$join->on(DB::raw('users.is_business'),'=',DB::raw(1));
					
				
				});
			}
			if($role == SELLER){
				$user_name_arr->select(DB::raw("(case when users.is_business = 1 then cc2.contact_firstname when users.is_business = 0 then c2.firstname end) as 'firstname'"),
					DB::raw("(case when users.is_business = 1 then cc2.contact_lastname when users.is_business = 0 then c2.lastname end) as 'lastname'"));
			}else if( $role == BUYER ){
				$user_name_arr->select(DB::raw("(case when users.is_business = 1 then cc2.contact_firstname when users.is_business = 0 then c2.firstname end) as 'firstname'"),
					DB::raw("(case when users.is_business = 1 then cc2.contact_lastname when users.is_business = 0 then c2.lastname end) as 'lastname'")
					);
					
			}
			
			$data	=	$user_name_arr->first();
			if($data->firstname){
				$nameStr = $data->firstname;
				if($data->lastname != '')
				$nameStr .= " ".$data->lastname;	
				return $nameStr;
			}
			else 
				return false;
			
		} catch ( Exception $exc ) {
				
		}
	}


   /**
    * Getting Year of Established
    * Start
    * @ return void
    * @ Jagadeesh / 29042016
    */
	public static function getYearofEstablished(){
		$return_array = array();
		for($y=YEAROFESTABLISHEDEND;$y>=YEAROFESTABLISHEDSTART;$y--){
			$return_array[$y] = $y;
		}
		return $return_array;
	}        

    /**
     * End 
    *  @Jagadeesh / 29042016
     */	
    /**
	 * Getting volume CFT
	 * @sumanth
	 */
	public static function getTermVolumeCft($contract){
		
		$termcontracts = DB::table ( 'term_contracts_indent_quantities as tq' )
		->where('tq.term_contract_id','=',$contract)
		->where('tq.lkp_service_id','=',RELOCATION_DOMESTIC)
		->select ( 'tq.volume' )->get ();
		
		
		if(count($termcontracts) > 0){
			return $termcontracts[0]->volume;
		} else{
			return '0';
		}
	 	
	}
	/**
	 * End
	 *  @Sumanth / 29042016
	 */
	
   /**
    * Remove profile previous upload files
    * Start
    * @ return void
    * @ Jagadeesh / 02052016
    */
	public static function RemoveProfilePreviousUploadFiles($user_id,$upload_path,$field_name){
		$user_details = DB::table('users')
						->where('users.id', $user_id)
						->first();
		if(isset($user_details->$field_name) && $user_details->$field_name){
			$file_name = $user_details->$field_name;
			$sizes = array(
							array(40,40),
							array(94,92),
							array(124,73),
							array(81,60),
							array(986,280),
					);
			foreach($sizes as $size){
				$ext = pathinfo($file_name, PATHINFO_EXTENSION);
				$remove_path = $upload_path.'/'.str_replace('.'.$ext,'_'.$size[0].'_'.$size[1].'.'.$ext,$file_name);
				if(file_exists($remove_path))
				unlink($remove_path);
				//echo $remove_path."<br>";
			}
			if(file_exists($upload_path.$file_name))
			unlink($upload_path.$file_name);
		}else{
			return false;
		}
		return true;				
	}        
    /**
     * End 
    *  @Jagadeesh / 02052016
     */

	/**
	 * Get latest three messages
	 * Start
	 * @ return void
	 * @ Ramu / 09052016
	 */
	public static function getHeaderMessages($user_id){
		$userMessages = DB::table('user_messages')
						->select('id','subject','message','created_at','is_term','sender_id','is_read')
						->where('recepient_id', $user_id)
						->orderby('id','desc')
						->take(3)
						->get();
		return $userMessages;
	}

	public static function getMessageShortBody($string){
		$string = strip_tags($string);
		if(strlen($string) > 100){
			return substr($string, 0, strrpos(substr($string, 0, 35), ' '))." ...";
		}else{
			return $string;
		}
	}

	public static function getProfileLogo($user_id){
		$getUserrole = DB::table('users')
			->where('users.id', $user_id)
			->select('users.lkp_role_id','users.is_business','users.logo','users.user_pic')
			->first();

		if($getUserrole->lkp_role_id==1){
			$url = "uploads/buyer/$user_id/";
		}else{
			$url = "uploads/seller/$user_id/";
		}
		if(!empty($getUserrole->user_pic)){
			$getuserpic = $url.$getUserrole->user_pic;
			$userpic =  CommonComponent::str_replace_last( '.' , '_40_40.' , $getuserpic );
			return $userpic;
		}
		return "";

		//$getlogopartner = $url.$getUserrole->logo;
		//return $getlogopartner;
	}

	public static function getMessagesCount($user_id,$serviceId,$type){

		$messageCount = DB::table('user_messages')
			->select('id','subject','created_at')
			->where('recepient_id', $user_id);
		if($type == 2){
			$messageCount->whereIn('lkp_message_type_id', array(2,4,5,6));
		}else{
			$messageCount->where('lkp_message_type_id', $type);
		}
		$messageCount->where('lkp_service_id', $serviceId);

		$messageCount = $messageCount->count();
		return $messageCount;
	}

	/** Retrieval of seller / Buyer Business Name to display in the left navigation  **/
	public static function getHeaderDashboardCount($userId, $roleId, $service,$counttype = 1)
	{
		try
		{
			switch ($service) {
				case ROAD_FTL :

					if($roleId == SELLER){
						$posts = DB::table('seller_posts as sps')->select('spis.id','spis.is_private')
											->join('seller_post_items as spis','spis.seller_post_id','=','sps.id')
											->where('sps.lkp_post_status_id', OPEN);
						if($counttype == 2){
							$posts->where("spis.is_private",0);
						}
						$posts = $posts->where('sps.seller_id', $userId)->get();
						$postsCount = CommonComponent::getMatchingPostsCount(ROAD_FTL,$posts,$counttype);

					}else{
						if($counttype ==1){
							$postsCount = DB::table('buyer_quotes as bq')->select('mi.seller_post_id')
											->join ( 'buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'bq.id' )
											->join('matching_items as mi','bq.id','=','mi.buyer_quote_id')
											->where('bqi.lkp_post_status_id', OPEN)
											->where('mi.service_id', ROAD_FTL)
											->where('mi.matching_type_id', $counttype)
											->where('bq.buyer_id', $userId)->count();
						}else{
							$postsCount = DB::table ( 'buyer_quote_sellers_quotes_prices as bqsp' )
											->join ( 'buyer_quote_items as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_item_id' )
											->where('bqi.lkp_post_status_id',OPEN)
											->where('bqsp.buyer_id',Auth::id())
											->select ( 'bqsp.id')
											->count();
						}
					}
					break;

				case ROAD_PTL :
					if($roleId == SELLER){
						$posts = DB::table('ptl_seller_posts as sps')->select('spis.id','spis.is_private')
											->join('ptl_seller_post_items as spis','spis.seller_post_id','=','sps.id')
											->where('sps.lkp_post_status_id', OPEN);
						if($counttype == 2){
							$posts->where("spis.is_private",0);
						}
						$posts = $posts->where('sps.seller_id', $userId)->get();
						$postsCount = CommonComponent::getMatchingPostsCount(ROAD_PTL,$posts,$counttype);

					}else {
						if($counttype ==1){
							$postsCount = DB::table('ptl_buyer_quotes as bq')->select('mi.seller_post_id')
								->join ( 'ptl_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'bq.id' )
								->join('matching_items as mi','bq.id','=','mi.buyer_quote_id')
								->where('bqi.lkp_post_status_id', OPEN)
								->where('mi.service_id', ROAD_PTL)
								->where('mi.matching_type_id', $counttype)
								->where('bq.buyer_id', $userId)->count();
						}else{
							$postsCount = DB::table ( 'ptl_buyer_quote_sellers_quotes_prices as bqsp' )
								->join ( 'ptl_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_item_id' )
								->where('bqi.lkp_post_status_id',OPEN)
								->where('bqsp.buyer_id',Auth::id())
								->select ( 'bqsp.id')
								->count();
						}
					}
					break;

				case COURIER :
					if($roleId == SELLER){
						$posts = DB::table('courier_seller_posts as sps')->select('spis.id','spis.is_private')
												->join('courier_seller_post_items as spis','spis.seller_post_id','=','sps.id')
												->where('sps.lkp_post_status_id', OPEN);
						if($counttype == 2){
							$posts->where("spis.is_private",0);
						}
						$posts = $posts->where('sps.seller_id', $userId)->get();
						$postsCount = CommonComponent::getMatchingPostsCount(COURIER,$posts,$counttype);
					}else {
						if($counttype ==1){
							$postsCount = DB::table('courier_buyer_quotes as bq')->select('mi.seller_post_id')
								->join ( 'courier_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'bq.id' )
								->join('matching_items as mi','bq.id','=','mi.buyer_quote_id')
								->where('bqi.lkp_post_status_id', OPEN)
								->where('mi.service_id', COURIER)
								->where('mi.matching_type_id', $counttype)
								->where('bq.buyer_id', $userId)->count();
						}else{
							$postsCount = DB::table ( 'courier_buyer_quote_sellers_quotes_prices as bqsp' )
								->join ( 'courier_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_item_id' )
								->where('bqi.lkp_post_status_id',OPEN)
								->where('bqsp.buyer_id',Auth::id())
								->select ( 'bqsp.id')
								->count();
						}
					}
					break;
				case RELOCATION_DOMESTIC :
					if($roleId == SELLER){
						$posts = DB::table('relocation_seller_posts as sps')->select('sps.id','spis.is_private')
							->join('relocation_seller_post_items as spis','spis.seller_post_id','=','sps.id')
							->where('sps.lkp_post_status_id', OPEN);
						if($counttype == 2){
							$posts->where("spis.is_private",0);
						}
						$posts = $posts->where('sps.seller_id', $userId)->get();
						$postsCount = CommonComponent::getMatchingPostsCount(RELOCATION_DOMESTIC,$posts,$counttype);
					}else {
						if($counttype ==1){
							$postsCount = DB::table('relocation_buyer_posts as bqi')->select('mi.seller_post_id')
								->join('matching_items as mi','bqi.id','=','mi.buyer_quote_id')
								->where('bqi.lkp_post_status_id', OPEN)
								->where('mi.service_id', RELOCATION_DOMESTIC)
								->where('mi.matching_type_id', $counttype)
								->where('bqi.buyer_id', $userId)->count();
						}else{
							$postsCount = DB::table ( 'relocation_buyer_quote_sellers_quotes_prices as bqsp' )
								->join ( 'relocation_buyer_posts as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_id' )
								->where('bqi.lkp_post_status_id',OPEN)
								->where('bqsp.buyer_id',Auth::id())
								->select ( 'bqsp.id')
								->count();
						}
					}
					break;
				case RELOCATION_PET_MOVE :
					if($roleId == SELLER){
						$posts = DB::table('relocationpet_seller_posts as sps')->select('sps.id','spis.is_private')
							->join('relocationpet_seller_post_items as spis','spis.seller_post_id','=','sps.id')
							->where('sps.lkp_post_status_id', OPEN);
						if($counttype == 2){
							$posts->where("spis.is_private",0);
						}
						$posts = $posts->where('sps.seller_id', $userId)->get();
						$postsCount = CommonComponent::getMatchingPostsCount(RELOCATION_PET_MOVE,$posts,$counttype);
					}else {
						if($counttype ==1){
							$postsCount = DB::table('relocationpet_buyer_posts as bqi')->select('mi.seller_post_id')
								->join('matching_items as mi','bqi.id','=','mi.buyer_quote_id')
								->where('bqi.lkp_post_status_id', OPEN)
								->where('mi.service_id', RELOCATION_PET_MOVE)
								->where('mi.matching_type_id', $counttype)
								->where('bqi.buyer_id', $userId)->count();
						}else{
							$postsCount = DB::table ( 'relocationpet_buyer_quote_sellers_quotes_prices as bqsp' )
								->join ( 'relocation_buyer_posts as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_id' )
								->where('bqi.lkp_post_status_id',OPEN)
								->where('bqsp.buyer_id',Auth::id())
								->select ( 'bqsp.id')
								->count();
						}
					}
					break;
				case RELOCATION_OFFICE_MOVE :
					if($roleId == SELLER){
						$posts = DB::table('relocationoffice_seller_posts as sps')->select('sps.id','sps.is_private')
							//->join('relocationoffice_seller_post_items as spis','spis.seller_post_id','=','sps.id')
							->where('sps.lkp_post_status_id', OPEN);
						if($counttype == 2){
							$posts->where("sps.is_private",0);
						}
						$posts = $posts->where('sps.seller_id', $userId)->get();
						$postsCount = CommonComponent::getMatchingPostsCount(RELOCATION_OFFICE_MOVE,$posts,$counttype);
					}else {
						if($counttype ==1){
							$postsCount = DB::table('relocationoffice_buyer_posts as bqi')->select('mi.seller_post_id')
								->join('matching_items as mi','bqi.id','=','mi.buyer_quote_id')
								->where('bqi.lkp_post_status_id', OPEN)
								->where('mi.service_id', RELOCATION_OFFICE_MOVE)
								->where('mi.matching_type_id', $counttype)
								->where('bqi.buyer_id', $userId)->count();
						}else{
							$postsCount = DB::table ( 'relocationoffice_buyer_quote_sellers_quotes_prices as bqsp' )
								->join ( 'relocationoffice_buyer_posts as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_id' )
								->where('bqi.lkp_post_status_id',OPEN)
								->where('bqsp.buyer_id',Auth::id())
								->select ( 'bqsp.id')
								->count();
						}
					}
					break;
				case RELOCATION_INTERNATIONAL :
					if($roleId == SELLER){
						$posts = DB::table('relocationint_seller_posts as sps')->select('sps.id','sps.is_private')
							//->join('relocationoffice_seller_post_items as spis','spis.seller_post_id','=','sps.id')
							->where('sps.lkp_post_status_id', OPEN);
						if($counttype == 2){
							$posts->where("sps.is_private",0);
						}
						$posts = $posts->where('sps.seller_id', $userId)->get();
						$postsCount = CommonComponent::getMatchingPostsCount(RELOCATION_INTERNATIONAL,$posts,$counttype);
					}else {
						if($counttype ==1){
							$postsCount = DB::table('relocationint_buyer_posts as bqi')->select('mi.seller_post_id')
								->join('matching_items as mi','bqi.id','=','mi.buyer_quote_id')
								->where('bqi.lkp_post_status_id', OPEN)
								->where('mi.service_id', RELOCATION_INTERNATIONAL)
								->where('mi.matching_type_id', $counttype)
								->where('bqi.buyer_id', $userId)->count();
						}else{
							$postsCount = DB::table ( 'relocationint_buyer_quote_sellers_quotes_prices as bqsp' )
								->join ( 'relocationint_buyer_posts as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_id' )
								->where('bqi.lkp_post_status_id',OPEN)
								->where('bqsp.buyer_id',Auth::id())
								->select ( 'bqsp.id')
								->count();
						}
					}
					break;
				case RELOCATION_GLOBAL_MOBILITY :
					if($roleId == SELLER){
						$posts = DB::table('relocationgm_seller_posts as sps')->select('sps.id','sps.is_private')
							//->join('relocationoffice_seller_post_items as spis','spis.seller_post_id','=','sps.id')
							->where('sps.lkp_post_status_id', OPEN);
						if($counttype == 2){
							$posts->where("sps.is_private",0);
						}
						$posts = $posts->where('sps.seller_id', $userId)->get();
						$postsCount = CommonComponent::getMatchingPostsCount(RELOCATION_INTERNATIONAL,$posts,$counttype);
					}else {
						if($counttype ==1){
							$postsCount = DB::table('relocationgm_buyer_posts as bqi')->select('mi.seller_post_id')
								->join('matching_items as mi','bqi.id','=','mi.buyer_quote_id')
								->where('bqi.lkp_post_status_id', OPEN)
								->where('mi.service_id', RELOCATION_INTERNATIONAL)
								->where('mi.matching_type_id', $counttype)
								->where('bqi.buyer_id', $userId)->count();
						}else{
							$postsCount = DB::table ( 'relocationgm_buyer_quote_sellers_quotes_prices as bqsp' )
								->join ( 'relocationgm_buyer_posts as bqi', 'bqi.id', '=', 'bqsp.buyer_post_id' )
								->where('bqi.lkp_post_status_id',OPEN)
								->where('bqsp.buyer_id',Auth::id())
								->select ( 'bqsp.id')
								->count();
						}
					}
					break;
				case AIR_INTERNATIONAL :
					if($roleId == SELLER){
						$posts = DB::table('airint_seller_posts as sps')->select('spis.id','spis.is_private')
							->join('airint_seller_post_items as spis','spis.seller_post_id','=','sps.id')
							->where('sps.lkp_post_status_id', OPEN);
						if($counttype == 2){
							$posts->where("spis.is_private",0);
						}
						$posts = $posts->where('sps.seller_id', $userId)->get();
						$postsCount = CommonComponent::getMatchingPostsCount(AIR_INTERNATIONAL,$posts,$counttype);
					}else {
						if($counttype ==1){
							$postsCount = DB::table('airint_buyer_quotes as bq')->select('mi.seller_post_id')
								->join ( 'airint_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'bq.id' )
								->join('matching_items as mi','bq.id','=','mi.buyer_quote_id')
								->where('bqi.lkp_post_status_id', OPEN)
								->where('mi.service_id', AIR_INTERNATIONAL)
								->where('mi.matching_type_id', $counttype)
								->where('bq.buyer_id', $userId)->count();
						}else{
							$postsCount = DB::table ( 'airint_buyer_quote_sellers_quotes_prices as bqsp' )
								->join ( 'airint_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_item_id' )
								->where('bqi.lkp_post_status_id',OPEN)
								->where('bqsp.buyer_id',Auth::id())
								->select ( 'bqsp.id')
								->count();
						}
					}
					break;

				case RAIL :
					if($roleId == SELLER){
						$posts = DB::table('rail_seller_posts as sps')->select('spis.id','spis.is_private')
							->join('rail_seller_post_items as spis','spis.seller_post_id','=','sps.id')
							->where('sps.lkp_post_status_id', OPEN);
						if($counttype == 2){
							$posts->where("spis.is_private",0);
						}
						$posts = $posts->where('sps.seller_id', $userId)->get();
						$postsCount = CommonComponent::getMatchingPostsCount(RAIL,$posts,$counttype);
					}else {
						if($counttype ==1){
							$postsCount = DB::table('rail_buyer_quotes as bq')->select('mi.seller_post_id')
								->join ( 'rail_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'bq.id' )
								->join('matching_items as mi','bq.id','=','mi.buyer_quote_id')
								->where('bqi.lkp_post_status_id', OPEN)
								->where('mi.service_id', RAIL)
								->where('mi.matching_type_id', $counttype)
								->where('bq.buyer_id', $userId)->count();
						}else{
							$postsCount = DB::table ( 'rail_buyer_quote_sellers_quotes_prices as bqsp' )
								->join ( 'rail_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_item_id' )
								->where('bqi.lkp_post_status_id',OPEN)
								->where('bqsp.buyer_id',Auth::id())
								->select ( 'bqsp.id')
								->count();
						}
					}
					break;
				case OCEAN :
					if($roleId == SELLER){
						$posts = DB::table('ocean_seller_posts as sps')->select('spis.id','spis.is_private')
							->join('ocean_seller_post_items as spis','spis.seller_post_id','=','sps.id')
							->where('sps.lkp_post_status_id', OPEN);
						if($counttype == 2){
							$posts->where("spis.is_private",0);
						}
						$posts = $posts->where('sps.seller_id', $userId)->get();
						$postsCount = CommonComponent::getMatchingPostsCount(OCEAN,$posts,$counttype);
					}else {
						if($counttype ==1){
							$postsCount = DB::table('ocean_buyer_quotes as bq')->select('mi.seller_post_id')
								->join ( 'ocean_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'bq.id' )
								->join('matching_items as mi','bq.id','=','mi.buyer_quote_id')
								->where('bqi.lkp_post_status_id', OPEN)
								->where('mi.service_id', OCEAN)
								->where('mi.matching_type_id', $counttype)
								->where('bq.buyer_id', $userId)->count();
						}else{
							$postsCount = DB::table ( 'ocean_buyer_quote_sellers_quotes_prices as bqsp' )
								->join ( 'ocean_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_item_id' )
								->where('bqi.lkp_post_status_id',OPEN)
								->where('bqsp.buyer_id',Auth::id())
								->select ( 'bqsp.id')
								->count();
						}
					}
					break;
				case AIR_DOMESTIC :
					if($roleId == SELLER){
						$posts = DB::table('airdom_seller_posts as sps')->select('spis.id','spis.is_private')
										->join('airdom_seller_post_items as spis','spis.seller_post_id','=','sps.id')
										->where('sps.lkp_post_status_id', OPEN);
						if($counttype == 2){
							$posts->where("spis.is_private",0);
						}
						$posts = $posts->where('sps.seller_id', $userId)->get();
						$postsCount = CommonComponent::getMatchingPostsCount(AIR_DOMESTIC,$posts,$counttype);

					}else {
						if($counttype ==1){
							$postsCount = DB::table('airdom_buyer_quotes as bq')->select('mi.seller_post_id')
								->join ( 'airdom_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'bq.id' )
								->join('matching_items as mi','bq.id','=','mi.buyer_quote_id')
								->where('bqi.lkp_post_status_id', OPEN)
								->where('mi.service_id', AIR_DOMESTIC)
								->where('mi.matching_type_id', $counttype)
								->where('bq.buyer_id', $userId)->count();
						}else{
							$postsCount = DB::table ( 'airdom_buyer_quote_sellers_quotes_prices as bqsp' )
								->join ( 'airdom_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_item_id' )
								->where('bqi.lkp_post_status_id',OPEN)
								->where('bqsp.buyer_id',Auth::id())
								->select ( 'bqsp.id')
								->count();
						}
					}
					break;

				case ROAD_TRUCK_HAUL :
					if($roleId == SELLER){
						$posts = DB::table('truckhaul_seller_posts as sps')->select('spis.id','spis.is_private')
										->join('truckhaul_seller_post_items as spis','spis.seller_post_id','=','sps.id')
										->where('sps.lkp_post_status_id', OPEN);
						if($counttype == 2){
							$posts->where("spis.is_private",0);
						}
						$posts = $posts->where('sps.seller_id', $userId)->get();
						$postsCount = CommonComponent::getMatchingPostsCount(ROAD_TRUCK_HAUL,$posts,$counttype);
					}else {
						if($counttype ==1){
							$postsCount = DB::table('truckhaul_buyer_quotes as bq')->select('mi.seller_post_id')
								->join ( 'truckhaul_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'bq.id' )
								->join('matching_items as mi','bqi.id','=','mi.buyer_quote_id')
								->where('bqi.lkp_post_status_id', OPEN)
								->where('mi.service_id', ROAD_TRUCK_HAUL)
								->where('mi.matching_type_id', $counttype)
								->where('bq.buyer_id', $userId)->count();

						}else{
							$postsCount = DB::table ( 'truckhaul_buyer_quote_sellers_quotes_prices as bqsp' )
								->join ( 'truckhaul_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_item_id' )
								->where('bqi.lkp_post_status_id',OPEN)
								->where('bqsp.buyer_id',Auth::id())
								->select ( 'bqsp.id')
								->count();
						}
					}
					break;
				case ROAD_TRUCK_LEASE :
					if($roleId == SELLER){
						$posts = DB::table('trucklease_seller_posts as sps')->select('spis.id','spis.is_private')
										->join('trucklease_seller_post_items as spis','spis.seller_post_id','=','sps.id')
										->where('sps.lkp_post_status_id', OPEN);
									if($counttype == 2){
										$posts->where("spis.is_private",0);
									}
									$posts = $posts->where('sps.seller_id', $userId)->get();
									$postsCount = CommonComponent::getMatchingPostsCount(ROAD_TRUCK_LEASE,$posts,$counttype);
					}else {
						if($counttype ==1){
							$postsCount = DB::table('trucklease_buyer_quotes as bq')->select('mi.seller_post_id')
								->join ( 'trucklease_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'bq.id' )
								->join('matching_items as mi','bqi.id','=','mi.buyer_quote_id')
								->where('bqi.lkp_post_status_id', OPEN)
								->where('mi.service_id', ROAD_TRUCK_LEASE)
								->where('mi.matching_type_id', $counttype)
								->where('bq.buyer_id', $userId)->count();
						}else{
							$postsCount = DB::table ( 'trucklease_buyer_quote_sellers_quotes_prices as bqsp' )
								->join ( 'trucklease_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_item_id' )
								->where('bqi.lkp_post_status_id',OPEN)
								->where('bqsp.buyer_id',Auth::id())
								->select ( 'bqsp.id')
								->count();
						}
					}
					break;
				case ROAD_INTRACITY :
					if($roleId == SELLER){
						$postsCount = 0;
					}else {
						$postsCount = 0;
					}
					break;
				default :
					if($roleId == SELLER){
						$postsCount = DB::table('seller_posts')->where('lkp_service_id', $service)->where('seller_id', $userId)->count();
					}else {
						$postsCount = DB::table('buyer_quotes')->where('lkp_service_id', $service)->where('buyer_id', $userId)->count();
					}
					break;
			}
			if($roleId == BUYER){
				$termcount = 0;
				if($counttype ==2) {
					$termBuyerQuotes = DB::table('term_buyer_quotes as bqi')->select('bqi.id')
										->leftjoin('term_buyer_quote_items as bqit', 'bqi.id', '=', 'bqit.term_buyer_quote_id')
										->where('bqi.created_by', Auth::User()->id)
										->where('bqi.lkp_service_id', $service)
										->where('bqi.lkp_post_status_id', 2)
										->groupBy('bqit.term_buyer_quote_id')
										->get();
					foreach ($termBuyerQuotes as $termBuyerQuote) {
						$termcount = $termcount + count(TermBuyerComponent::getTermBuyerQuoteSellersQuotesPricesFromId($termBuyerQuote->id, $service));
					}
					$postsCount = $postsCount + $termcount;
				}
			}
			if(isset($postsCount))
				return $postsCount;
			else
				echo "service error ".$service;
		}
		catch(Exception $e)
		{
		}
	}

	public static function getMatchingPostsCount($serviceId,$sellerpostids,$matchingtype){
		$postcount = 0;
		if($matchingtype == 1) {
			foreach ($sellerpostids as $sellerpostid){
				$buyermatchedposts = SellerMatchingComponent::getMatchedResults($serviceId, $sellerpostid->id);
				//echo "Post Id".$sellerpostid->id."--".$sellerpostid->is_private."--".count($buyermatchedposts)."--".$postcount."<br/>";
				if($sellerpostid->is_private == 1){
					$buyerMatchedPrivateposts = CommonComponent::getPrivateBuyerMatchedResults($serviceId,$sellerpostid->id);
					//echo "<pre>";print_R($buyermatchedposts);print_R($buyerMatchedPrivateposts);die;
					$postcount = $postcount + CommonComponent::getEnquiriesCount($buyermatchedposts,$buyerMatchedPrivateposts);
				}else{
					$postcount = $postcount + count($buyermatchedposts);
				}
				//echo "Post Id".$sellerpostid->id."--".$sellerpostid->is_private."--".$postcount."<br/>";
			}
		}else{
			foreach ($sellerpostids as $sellerpostid){
				$postcount = $postcount + count(SellerMatchingComponent::getSellerLeads($serviceId, $sellerpostid->id));
			}
		}

		return $postcount;
	}

	public static function getEnquiriesCount($matchedresuts,$matchedPrivateResult){
		//echo "<pre>";print_R($matchedresuts);print_R($matchedPrivateResult);
		$matchedIds = array();
		foreach($matchedresuts as $buyer_quote_item){
			$matchedIds[] = $buyer_quote_item->buyer_quote_id;
		}
		if(!empty($matchedPrivateResult)){
			if(!in_array($matchedPrivateResult,$matchedIds)){
				$matchedIds[] = $matchedPrivateResult;
			}
		}
		//echo "<pre>";print_R($matchedIds);
		return count($matchedIds);
	}
        public static function getBuyerPostFromOrder($oid){
            //echo "---".$oid;exit;
            $data   =   DB::table('orders')->select('buyer_quote_id','buyer_id')
                        ->where('id', $oid)->where('seller_id', Auth::User()->id)->first();
            return $data;
            
        }
        
        //Getshipment Types
        public static function getRelocationAllShipmentType()
        {
        	try
        	{
        		$getRelocationAllShipmentTypes = DB::table('lkp_relocation_shipment_types')->orderBy ( 'shipment_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('shipment_type', 'id');
        		return $getRelocationAllShipmentTypes;
        	}
        	catch(\Exception $e)
        	{
        		//return $e->message;
        	}
        }
        //Get VolumeTypes
        public static function getRelocationAllVolumeTypes()
        {
        	try
        	{
        		$getRelocationAllVolumeTypes = DB::table('lkp_relocation_shipment_volumes')->where('is_active','=',IS_ACTIVE)->lists('volume', 'id');
        		return $getRelocationAllVolumeTypes;
        	}
        	catch(\Exception $e)
        	{
        		//return $e->message;
        	}
        }
        
        /** Retrieval of lkp cartons **/
	public static function getCartons()
	{
		try
		{
			$quoteAccesses = DB::table('lkp_air_carton_types')->where('is_active','=',IS_ACTIVE)->select('id','carton_type', 'carton_description', 'weight')->get();
			return $quoteAccesses;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of Relocation International Types  **/
	public static function getRelocationInternationalTypes()
	{
		try
		{
			$international_types = DB::table('lkp_international_types')->orderBy ( 'international_type', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('international_type', 'id');
			return $international_types;
		}catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/** Retrieval of lkp slabs relocation int **/
	public static function getRelocationIntSlabs(){
		try
		{
			$slabs = DB::table('lkp_air_weight_slabs')->where('is_active','=',IS_ACTIVE)->select('id','min_slab_weight', 'max_slab_weight')->get();
			return $slabs;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}

	/**
	* Function to get Total Number of Cartons of a Buyer Post
	*
	*
	*/
	public static function getCartonsTotal($buyer_post_id){
		try
		{
			$cartons_total = DB::table('relocationint_buyer_post_air_cartons')
	  		->where('relocationint_buyer_post_air_cartons.buyer_post_id', $buyer_post_id)
	  		->select(DB::Raw('SUM(relocationint_buyer_post_air_cartons.number_of_cartons)  AS no_of_cartons'))
			->get();	

			return $cartons_total[0]->no_of_cartons;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}


	public static function getCartonsList(){
		$cartons    =   CommonComponent::getCartons();
		$cartons_list = array();
		foreach($cartons as $row){
			$cartons_list[$row->id]['id'] = $row->id;
			$cartons_list[$row->id]['carton_type'] = $row->carton_type;
			$cartons_list[$row->id]['carton_description'] = $row->carton_description;
			$cartons_list[$row->id]['weight'] = $row->weight;
		}
		return $cartons_list;
	}

	/**
	* Function to get Total Weight of Cartons of a Buyer Post
	*
	*
	*/
	public static function getCartonsTotalWeight($buyer_post_id){
		try
		{
			$cartons_total = DB::table('relocationint_buyer_post_air_cartons')
			->leftjoin('lkp_air_carton_types as lact','lact.id','=','relocationint_buyer_post_air_cartons.lkp_air_carton_type_id')
	  		->where('relocationint_buyer_post_air_cartons.buyer_post_id', $buyer_post_id)
	  		->select(DB::Raw('SUM(relocationint_buyer_post_air_cartons.number_of_cartons * lact.weight) AS no_of_cartons'))
			->get();	

			return $cartons_total[0]->no_of_cartons;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
        
        /**
	* Function to get Total Number of Cartons of a Buyer Post
	*
	*
	*/
	public static function getCartonDetails($buyer_post_id){
		try
		{
			$buyer_post_inventory_details =DB::table('relocationint_buyer_post_air_cartons as rbpac')
                ->leftjoin ( 'lkp_air_carton_types as lact', 'lact.id', '=', 'rbpac.lkp_air_carton_type_id' )
                ->where('rbpac.buyer_post_id',$buyer_post_id)
                ->select('rbpac.number_of_cartons','lact.carton_type','lact.carton_description')->get();	
			return $buyer_post_inventory_details;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
        
	/** Retrieval of State ID  **/
    public static function getStateId($fromid,$toid)
    {
    	$serviceId = Session::get('service_id');
    	
    	try
    	{	
    		switch($serviceId){
                case ROAD_FTL       : 
    			$sql = "SELECT
	    			c1. lkp_state_id as from_state_id, c2. lkp_state_id  as to_state_id
	    			from
	    			lkp_cities c1,
	    			lkp_cities c2
	    			WHERE
	    			c1.id = $fromid and
	    			c2.id = $toid";
    				return DB::select(DB::raw($sql))[0];
    			break;
    			case ROAD_PTL       :
    			case RAIL :
    			case AIR_DOMESTIC :
    			case AIR_INTERNATIONAL :
    			case OCEAN :
    			case ROAD_INTRACITY :
    			case COURIER:
    				$sql = "SELECT
    				c1.state_id as from_state_id, c2. 	state_id  as to_state_id
    				from
    				lkp_ptl_pincodes c1,
    				lkp_ptl_pincodes c2
    				WHERE
    				c1.id = $fromid and
    				c2.id = $toid";
    				//echo $sql;die;
    				return DB::select(DB::raw($sql))[0];
    				break;
    				default :
    					
    			break;
    		}
    	}
    	catch(\Exception $e)
    	{
    		//return $e->message;
    	}
    }

	public static function getMessageAttachments($messageId) {
		try
	{
				$getMessagesQuery = DB::table('user_message_uploads as umu');
				$getMessagesQuery->where('umu.user_message_id', $messageId);
				$messageDetails =$getMessagesQuery->select('umu.filepath','umu.name')->get();
				return $messageDetails;
			}  catch (\Exception $e){
				//return $e->message;
			}
	}

	public static function IsStatutoryApplied($role,$service){
		$StatutoryApplied = DB::table('gsa_service_wise_documents')
								->where('role_id',$role)
								->where('service_id',$service)
								->where('document_id',2)
								->get();
		if(count($StatutoryApplied) > 0){
			return true;
		}
		return false;
	}


	/*
	* To get the incoming & outgoing docs
	* @author Shriram
	*/
	public static function getStatutoryDocs($where)
	{
		$sql = "SELECT inCom.id as incoming_doc_id, outCom.id as outgoing_doc_id
				FROM  gsa_statutory_documents inCom, gsa_statutory_documents outCom
				WHERE
				inCom.id = ".$where['to_state_id']." AND 
				outCom.id = ".$where['from_state_id'];
                $q = DB::select(DB::raw($sql));
                if(count($q)!=0):
                    return $q[0];
                else:
                    $obj = new \stdClass;
                    $obj->incoming_doc_id = null;
                    $obj->outgoing_doc_id = null;
                    return $obj;
                endif;
	}
	
	/*
	* Get states ids based on from and to pin code
	* @author Shriram
	*/
    public static function getStatebyPincode($from_pincode_id, $to_pincode_id)
    {
    	$sql = "SELECT
				p1.state_id as from_state_id, p2.state_id as to_state_id
				from  
				lkp_ptl_pincodes p1,
				lkp_ptl_pincodes p2
				WHERE
				p1.id = $from_pincode_id and
				p2.id = $to_pincode_id";
		return DB::select(DB::raw($sql))[0];
    }

    
    public static function getLocationType($id){
        try{
            $location = DB::table('lkp_location_types')
                            ->where('id',$id)
                            ->select('location_type_name')
                            ->first();
            if(!empty($location))
            return $location->location_type_name;
            else 
                return false;
        } catch (Exception $ex) {

        }
    }
    
    public static function getCommercial($id){
        try{
                $serviceId = Session::get('service_id');
                $orders = DB::table('orders')->where('orders.id', $id)->select('lkp_order_type_id')->first();
                $order = DB::table('orders');
                if($orders->lkp_order_type_id==1){
                    switch ($serviceId) {
                        case ROAD_FTL :       
                            $order->leftjoin('buyer_quote_items as bqi','bqi.id','=','orders.buyer_quote_item_id')        
                            ->leftjoin('buyer_quotes as bq','bqi.buyer_quote_id','=','bq.id');        
                            break;
                        case ROAD_PTL : 
                            $order->leftjoin('ptl_buyer_quotes as bq','bq.id','=','orders.buyer_quote_id');
                            break;
                        case RAIL : 
                            $order->leftjoin('rail_buyer_quotes as bq','bq.id','=','orders.buyer_quote_id');    
                            break;
                        case AIR_DOMESTIC : 
                            $order->leftjoin('airdom_buyer_quotes as bq','bq.id','=','orders.buyer_quote_id');        

                            break;
                        case AIR_INTERNATIONAL : 
                            $order->leftjoin('airint_buyer_quotes as bq','bq.id','=','orders.buyer_quote_id'); 
                            break;
                        case OCEAN : 
                            $order->leftjoin('ocean_buyer_quotes as bq','bq.id','=','orders.buyer_quote_id'); 
                            break;
                        case COURIER : 
                            $order->leftjoin('courier_buyer_quotes as bq','bq.id','=','orders.buyer_quote_id');        
                            break;
                        case ROAD_TRUCK_HAUL : 
                            $order->leftjoin('truckhaul_buyer_quote_items as bqi','bqi.id','=','orders.buyer_quote_item_id')        
                            ->leftjoin('truckhaul_buyer_quotes as bq','bqi.buyer_quote_id','=','bq.id');
                            break;
                        case ROAD_TRUCK_LEASE : 
                            $order->leftjoin('trucklease_buyer_quote_items as bqi','bqi.id','=','orders.buyer_quote_item_id')        
                            ->leftjoin('trucklease_buyer_quotes as bq','bqi.buyer_quote_id','=','bq.id');
                            break;
                        case ROAD_INTRACITY : 
                            $order->leftjoin('ict_buyer_quote_items as bqi','bqi.id','=','orders.buyer_quote_item_id')        
                            ->leftjoin('ict_buyer_quotes as bq','bqi.buyer_quote_id','=','bq.id');
                            break;
                        case RELOCATION_DOMESTIC : 
                        case RELOCATION_PET_MOVE : 
                        case RELOCATION_OFFICE_MOVE : 
                        case RELOCATION_INTERNATIONAL :  
                        case RELOCATION_GLOBAL_MOBILITY :      
                            return false;
                    }
                }else{
//                     $order->leftjoin('term_buyer_quote_items as bqi','bqi.id','=','orders.buyer_quote_item_id')        
//                            ->leftjoin('term_buyer_quotes as bq','bqi.term_buyer_quote_id','=','bq.id')
//                             ->where('bq.lkp_service_id', $serviceId);
                    return true;
                }
                $commercial =$order->where('orders.id', $id)->select('is_commercial')->first();
                
                return $commercial->is_commercial;
                

        } catch (Exception $ex) {

        }
        
    }
    


	public static function getGsaDocuments($role,$service,$postId,$fromlocation=0,$tolocation=0,$commrcialtype = 0){
		$documents = DB::table('gsa_service_wise_documents as gswd')
			->leftjoin ( 'lkp_documents as ld', 'ld.id', '=', 'gswd.document_id' )
			->where('role_id',$role)
			->where('service_id',$service)
			->where('is_commercial',$commrcialtype) //Commercial wise documents
			->where('ld.id',"!=",2)
			->lists("ld.title");
		$issanutryapplied = CommonComponent::IsStatutoryApplied($role,$service);
		$stateids= CommonComponent::getStateId($fromlocation,$tolocation);

		if(isset($stateids->from_state_id) && ($stateids->to_state_id) &&  ($stateids->from_state_id != $stateids->to_state_id) && ($commrcialtype==1)){
			
			$statutarydocs = CommonComponent::getPostDocumentsByState($stateids->from_state_id,$stateids->to_state_id);
			if(isset($statutarydocs[0])){
				$documents[] = $statutarydocs[0];
			}
			if(isset($statutarydocs[0])){
				$documents[] = $statutarydocs[1];
			}
		}
		//if($issanutryapplied == 1){
			
			/*$sanutrydocs = CommonComponent::getPostDocuments($postId,$service);
			if(isset($sanutrydocs->incoming)){
				$documents[''] = $sanutrydocs->incoming;
			}
			if(isset($sanutrydocs->outgoing)){
				$documents[''] = $sanutrydocs->outgoing;
			}*/
			
		//}
		return $documents;

	}

	public static function getPostDocuments($postId,$service){
		if($service == ROAD_FTL){
			$table = "buyer_quote_items";
		}else if($service == ROAD_PTL){
			$table = "ptl_buyer_quotes";
		}else if($service == RAIL){
			$table = "rail_buyer_quotes";
		}else if($service == AIR_DOMESTIC){
			$table = "airdom_buyer_quotes";
		}else if($service == COURIER){
			$table = "courier_buyer_quotes";
		}
		if(isset($table)){
			$documents = DB::table("$table as gswd")
							->leftjoin ( 'lkp_documents as ld', 'ld.id', '=', 'gswd.incoming_docs' )
							->leftjoin ( 'lkp_documents as ld1', 'ld1.id', '=', 'gswd.outgoing_docs' )
							->where('gswd.id',$postId)
							->select("ld.title as incoming","ld1.title as outgoing")->first("incoming","outgoing");
			return $documents;
		}
		return;
	}
	
	public static function getPostDocumentsByState($fromstate,$tostate){
		$documents = array();
		$fromlocdoc = DB::table("gsa_statutory_documents")->where('state_id',$fromstate)->pluck("outgoing");
		$tolocdoc = DB::table("gsa_statutory_documents")->where('state_id',$tostate)->pluck("incoming");
		if(!empty($fromlocdoc)){
			$documents[] = $fromlocdoc;
		}
		if(!empty($tolocdoc)){
			$documents[] = $tolocdoc;
		}
		return $documents;
	} 
        
        
        
        public static function getUserDetConsignment(){
		
		
		$getUserrole = DB::table('users')
			->where('users.id', Auth::user()->id)
			->select('users.primary_role_id','users.is_business')
			->first();
			
			
				if($getUserrole->is_business == 1){
					$buyerTable = 'sellers';
					$contact = 'contact_mobile';
					$contactland='contact_landline';
					$gta = 'gta';
					$tin = 'tin';
					$serivce = 'service_tax_number';
					$est= 'established_in';
                     $principal_place='principal_place';
				}else{
					$buyerTable = 'seller_details';
					$contact = 'contact_mobile';
					$contactland='contact_landline';
					$gta = 'gta';
					$tin = 'tin';
					$serivce = 'service_tax_number';
					$est= 'established_in';
                                        $principal_place='principal_place';
				}
			
			
			$getUserDetails = DB::table('users')
			->leftJoin( $buyerTable , 'users.id', '=', $buyerTable.'.user_id' )
			->where('users.id', Auth::user()->id)
			->select($buyerTable.'.'.$principal_place .' as principal_place','users.*',$buyerTable.'.description',$buyerTable.'.address',$buyerTable.'.'.$contact .' as phone',
					$buyerTable.'.'.$gta .' as gat',$buyerTable.'.'.$tin .' as tin',$buyerTable.'.'.$serivce .' as service',$buyerTable.'.'.$est .' as est',$buyerTable.'.'.$contactland .' as land')
			->first();
			
		return $getUserDetails;
	}

        
        
        public static function getSellerPostOrder($id){
        try{
                $serviceId = Session::get('service_id');
                $orders = DB::table('orders')->where('orders.id', $id)->select('lkp_order_type_id')->first();
                $order = DB::table('orders');
                if($orders->lkp_order_type_id==1){
                    switch ($serviceId) {
                        case ROAD_FTL :       
                            $order->leftjoin('seller_post_items as spi','spi.id','=','orders.seller_post_item_id')        
                            ->leftjoin('seller_posts as sp','spi.seller_post_id','=','sp.id');        
                            break;
                        case ROAD_PTL : 
                            $order->leftjoin('ptl_seller_post_items as spi','spi.id','=','orders.seller_post_item_id')        
                            ->leftjoin('ptl_seller_posts as sp','spi.seller_post_id','=','sp.id');
                            break;
                        case RAIL : 
                            $order->leftjoin('rail_seller_post_items as spi','spi.id','=','orders.seller_post_item_id')        
                            ->leftjoin('rail_seller_posts as sp','spi.seller_post_id','=','sp.id');
                            break;
                        case AIR_DOMESTIC : 
                            $order->leftjoin('airdom_seller_post_items as spi','spi.id','=','orders.seller_post_item_id')        
                            ->leftjoin('airdom_seller_posts as sp','spi.seller_post_id','=','sp.id');
                            break;
                        case AIR_INTERNATIONAL : 
                            $order->leftjoin('airint_seller_post_items as spi','spi.id','=','orders.seller_post_item_id')        
                            ->leftjoin('airint_seller_posts as sp','spi.seller_post_id','=','sp.id');
                            break;
                        case OCEAN : 
                            $order->leftjoin('ocean_seller_post_items as spi','spi.id','=','orders.seller_post_item_id')        
                            ->leftjoin('ocean_seller_posts as sp','spi.seller_post_id','=','sp.id');
                            break;
                        case COURIER : 
                            $order->leftjoin('courier_seller_post_items as spi','spi.id','=','orders.seller_post_item_id')        
                            ->leftjoin('courier_seller_posts as sp','spi.seller_post_id','=','sp.id');
                            break;
                        case ROAD_TRUCK_HAUL : 
                            $order->leftjoin('truckhaul_seller_post_items as spi','spi.id','=','orders.seller_post_item_id')        
                            ->leftjoin('truckhaul_seller_posts as sp','spi.seller_post_id','=','sp.id');
                            break;
                        case ROAD_TRUCK_LEASE : 
                            $order->leftjoin('trucklease_seller_post_items as spi','spi.id','=','orders.seller_post_item_id')        
                            ->leftjoin('trucklease_seller_posts as sp','spi.seller_post_id','=','sp.id');
                            break;
                        case RELOCATION_DOMESTIC : 
                            $order->leftjoin('relocation_seller_posts as sp','sp.id','=','orders.seller_post_item_id');        
                           // ->leftjoin('relocation_seller_posts as sp','spi.seller_post_id','=','sp.id');
                            $sellerposts =$order->where('orders.id', $id)->select('sp.*')->first();
                            return $sellerposts;
                            break;
                        case RELOCATION_PET_MOVE : 
                            $order->leftjoin('relocationpet_seller_post_items as spi','spi.id','=','orders.seller_post_item_id')        
                            ->leftjoin('relocationpet_seller_posts as sp','spi.seller_post_id','=','sp.id');
                            $sellerposts =$order->where('orders.id', $id)->select('sp.*')->first();
                            return $sellerposts;
                            break;
                        case RELOCATION_OFFICE_MOVE : 
                            $order->leftjoin('relocationoffice_seller_posts as sp','sp.id','=','orders.seller_post_item_id');       
                            $sellerposts =$order->where('orders.id', $id)->select('sp.*')->first();
                            return $sellerposts;
                            break;
                        case RELOCATION_INTERNATIONAL : 
                            $order->leftjoin('relocationint_seller_post_items as spi','spi.id','=','orders.seller_post_item_id')        
                            ->leftjoin('relocationint_seller_posts as sp','spi.seller_post_id','=','sp.id');
                            $sellerposts =$order->where('orders.id', $id)->select('sp.*')->first();
                            return $sellerposts;
                            break;
                        case RELOCATION_GLOBAL_MOBILITY : 
                            $order->leftjoin('relocationgm_seller_posts as spi','spi.id','=','orders.seller_post_item_id');  
                            $sellerposts =$order->where('orders.id', $id)->select('spi.*')->first();
                            return $sellerposts;
                            break;
                        
                    }
                }else{
                     return false;
                }
                $sellerposts =$order->where('orders.id', $id)->select('terms_conditions','cancellation_charge_text','cancellation_charge_price','docket_charge_text','docket_charge_price','other_charge1_text','other_charge1_price','other_charge2_text','other_charge2_price','other_charge3_text','other_charge3_price')->first();
                
                return $sellerposts;
                

        } catch (Exception $ex) {

        }
        
    }

	
	public static function getCommercialBooknow($id){
		try{
			$serviceId = Session::get('service_id');
			
				switch ($serviceId) {
					case ROAD_FTL :
						$quotes=DB::table('buyer_quotes')->where('buyer_quotes.id', $id)->select('is_commercial')->first();
						
						break;
					case ROAD_PTL :
						$quotes=DB::table('ptl_buyer_quotes')->where('ptl_buyer_quotes.id', $id)->select('is_commercial')->first();
						
						break;
					case RAIL :
						$quotes=DB::table('rail_buyer_quotes as bq')->where('bq.id', $id)->select('is_commercial')->first();
						break;
					case AIR_DOMESTIC :
						$quotes=DB::table('airdom_buyer_quotes as bq')->where('bq.id', $id)->select('is_commercial')->first();
	
						break;
					case AIR_INTERNATIONAL :
						$quotes=DB::table('airint_buyer_quotes as bq')->where('bq.id', $id)->select('is_commercial')->first();
						break;
					case OCEAN :
						$quotes=DB::table('ocean_buyer_quotes as bq')->where('bq.id', $id)->select('is_commercial')->first();
						break;
					case COURIER :
						$quotes=DB::table('courier_buyer_quotes as bq')->where('bq.id', $id)->select('is_commercial')->first();
						break;
					case ROAD_TRUCK_HAUL :
						$quotes=DB::table('truckhaul_buyer_quotes as bq')->where('bq.id', $id)->select('is_commercial')->first();
						break;
					case ROAD_TRUCK_LEASE :
						$quotes=DB::table('trucklease_buyer_quotes as bq')->where('bq.id', $id)->select('is_commercial')->first();
						break;
					case RELOCATION_DOMESTIC :
							$quotes=DB::table('relocation_buyer_posts as bq')->where('bq.id', $id)->select('is_commercial')->first();
							break;
					case ROAD_INTRACITY :
						$quotes=DB::table('ict_buyer_quotes as bq')->where('bq.id', $id)->select('is_commercial')->first();
					
						break;
				}
			
	
			return $quotes->is_commercial;
	
	
		} catch (Exception $ex) {
	
		}

        
 }
 
 public static function getSellerOtherCharges($id){
 	
 	
 try{
			$serviceId = Session::get('service_id');
			
				switch ($serviceId) {
					case ROAD_FTL :
						$quotes=DB::table('seller_posts as sp')
						->leftjoin('seller_post_items as spi','spi.seller_post_id','=','sp.id')
						->where('spi.id', $id)
						->select('sp.*')->first();
						
						break;
					case ROAD_PTL :
						$quotes=DB::table('ptl_seller_posts as sp')
						->leftjoin('ptl_seller_post_items as spi','spi.seller_post_id','=','sp.id')
						->where('spi.id', $id)
						->select('sp.*')->first();
						break;
					case RAIL :
						$quotes=DB::table('rail_seller_posts as sp')
						->leftjoin('rail_seller_post_items as spi','spi.seller_post_id','=','sp.id')
						->where('spi.id', $id)
						->select('sp.*')->first();
						break;
					case AIR_DOMESTIC :
						$quotes=DB::table('airdom_seller_posts as sp')
						->leftjoin('airdom_seller_post_items as spi','spi.seller_post_id','=','sp.id')
						->where('spi.id', $id)
						->select('sp.*')->first();
						break;
					case AIR_INTERNATIONAL :
						$quotes=DB::table('airint_seller_posts as sp')
						->leftjoin('airint_seller_post_items as spi','spi.seller_post_id','=','sp.id')
						->where('spi.id', $id)
						->select('sp.*')->first();
						break;
					case OCEAN :
						$quotes=DB::table('ocean_seller_posts as sp')
						->leftjoin('ocean_seller_post_items as spi','spi.seller_post_id','=','sp.id')
						->where('spi.id', $id)
						->select('sp.*')->first();
						
						break;
					case COURIER :
						$quotes=DB::table('courier_seller_posts as sp')
						->leftjoin('courier_seller_post_items as spi','spi.seller_post_id','=','sp.id')
						->where('spi.id', $id)
						->select('sp.*')->first();
						break;
					case ROAD_TRUCK_HAUL :
						$quotes=DB::table('truckhaul_seller_posts as sp')
						->leftjoin('truckhaul_seller_post_items as spi','spi.seller_post_id','=','sp.id')
						->where('spi.id', $id)
						->select('sp.*')->first();
						break;
					case ROAD_TRUCK_LEASE :
						$quotes=DB::table('trucklease_seller_posts as sp')
						->leftjoin('trucklease_seller_post_items as spi','spi.seller_post_id','=','sp.id')
						->where('spi.id', $id)
						->select('sp.*')->first();
						break;
					case RELOCATION_DOMESTIC :
							$quotes=DB::table('relocation_seller_posts as sp')
							//->leftjoin('relocation_seller_post_items as spi','spi.seller_post_id','=','sp.id')
							->where('sp.id', $id)
							->select('sp.*')->first();
							break;
					case RELOCATION_PET_MOVE :
								$quotes=DB::table('relocationpet_seller_posts as sp')
								//->leftjoin('relocation_seller_post_items as spi','spi.seller_post_id','=','sp.id')
								->where('sp.id', $id)
								->select('sp.*')->first();
								break;
					case RELOCATION_INTERNATIONAL :
								$quotes=DB::table('relocationint_seller_posts as sp')
									//->leftjoin('relocation_seller_post_items as spi','spi.seller_post_id','=','sp.id')
									->where('sp.id', $id)
									->select('sp.*')->first();
									break;
					case RELOCATION_OFFICE_MOVE :
								$quotes=DB::table('relocationoffice_seller_posts as sp')
										//->leftjoin('relocation_seller_post_items as spi','spi.seller_post_id','=','sp.id')
										->where('sp.id', $id)
										->select('sp.*')->first();
										break;
                   case RELOCATION_GLOBAL_MOBILITY :
							$quotes=DB::table('relocationgm_seller_posts as sp')
							->where('sp.id', $id)
							->select('sp.*')->first();
							break;            
					case ROAD_INTRACITY :
						$quotes=DB::table('ict_seller_posts as sp')
						->leftjoin('ict_seller_post_items as spi','spi.seller_post_id','=','sp.id')
						->where('spi.id', $id)
						->select('sp.*')->first();
						break;
				}
			
			
			return $quotes;
	
	
		} catch (Exception $ex) {
	
		}
   }


	public static function orderArray($elements){
		$sortdeArray = array();
		if(isset($elements['']))
			$sortdeArray[''] = $elements[''];
		unset($elements['']);
		uasort ( $elements , function ($a, $b) {
				return strnatcmp($a,$b); // or other function/code
			}
		);
		foreach($elements as $key => $element){
			if(!empty($key) && !empty($element)){
				$sortdeArray[$key] = $element;
			}
		}
		return $sortdeArray;
	}
        
        /**
    * getseller post details
    * Method to retrieve seller post details
    * @param int $buyerQuoteId
    * @return array
    */
	public static function getSellersQuotesFromId($buyerQuoteId) {
		try {
			$serviceId = Session::get('service_id');
			
			Log::info ('Get seller lists for the buyer: ' . Auth::id (), array ('c' => '2'));
			(object)$arrayBuyerQuoteSellersNotQuotesPrices="";
			switch($serviceId){
                            case ROAD_FTL       :
                                $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'seller_post_items as sp' );
                                $getBuyerQuoteSellersQuotesPricesQuery->leftjoin ('seller_posts as sps', 'sps.id', '=', 'sp.seller_post_id' );
                		break;
                            case ROAD_PTL       :
                                $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'ptl_seller_posts as sp' );
                		break;
                            case RAIL       :
                                $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'rail_seller_posts as sp' );
                                            
                            break;
                            case AIR_DOMESTIC       :
                                $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'airdom_seller_posts as sp' );
                                break;
                            case AIR_INTERNATIONAL       :
                                $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'airint_seller_posts as sp' );
                                            
                            break;
                            case OCEAN       :
                                $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'ocean_seller_posts as sp' );
                                break;
                            case COURIER       :
                                    $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'courier_seller_posts as sp' );
                                    break;
                            case ROAD_TRUCK_LEASE       :
                            	$getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'trucklease_seller_post_items as sp' );
                            	$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ('trucklease_seller_posts as sps', 'sps.id', '=', 'sp.seller_post_id' );
                            	//$getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'trucklease_seller_posts as sp' );
                                    break;
                            case ROAD_TRUCK_HAUL       :
                            	$getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'truckhaul_seller_post_items as sp' );
                            	$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ('truckhaul_seller_posts as sps', 'sps.id', '=', 'sp.seller_post_id' );
                            	//$getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'truckhaul_seller_posts as sp' );
                                    break;
                            case RELOCATION_DOMESTIC       :
                            	$getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'relocation_seller_post_items as sp' );
                            	$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ('relocation_seller_posts as sps', 'sps.id', '=', 'sp.seller_post_id' );
                            	//$getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'relocation_seller_posts as sp' );
                                    break;
                            case RELOCATION_PET_MOVE       :
                            	$getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'relocationpet_seller_post_items as sp' );
                            	$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ('relocationpet_seller_posts as sps', 'sps.id', '=', 'sp.seller_post_id' );
                            	//$getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'relocationpet_seller_posts as sp' );
                            	
                                    break;
                            case RELOCATION_OFFICE_MOVE       :
                                    $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'relocationoffice_seller_posts as sp' );
                                    break;
                            case RELOCATION_INTERNATIONAL       :
                            	$getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'relocationint_seller_posts as sp' );
                            	$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ('relocationint_seller_post_items as sps', 'sp.id', '=', 'sps.seller_post_id' );
                                    //$getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'relocationint_seller_posts as sp' );
                                    break;
							case RELOCATION_GLOBAL_MOBILITY       :
								$getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'relocationgm_seller_posts as sp' );
								//$getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'relocationint_seller_posts as sp' );
								break;
                            default       :
                                    $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'seller_post_items as sp' );
                                $getBuyerQuoteSellersQuotesPricesQuery->leftjoin ('seller_posts as sps', 'sps.id', '=', 'sp.seller_post_id' );
                                    break;
                        }
			//echo $buyerQuoteId;
//echo $getBuyerQuoteSellersQuotesPricesQuery->where ( 'sp.id', '=', $buyerQuoteId )->select('from_date','to_date')->tosql();die;
                        $sellersQuotes=$getBuyerQuoteSellersQuotesPricesQuery->where ( 'sp.id', '=', $buyerQuoteId )->select('from_date','to_date')->first();
                       return $sellersQuotes;
                       
                }catch (Exception $exc) {
    		// echo $exc->getTraceAsString();
                }
        }
        
     /** Retrieval of weight Type**/
        public static function getVolumeWeightUnit($id)
        { 
        	try
        	{
        		$volumeWeightTypes = DB::table('lkp_ptl_length_uom')->where('id','=',$id)->select('weight_type')->first();
        		return $volumeWeightTypes->weight_type;
        	}
        	catch(\Exception $e)
        	{
        		//return $e->message;
        	}
        }     
     /**
        * getseller buyer invoice details
        * Method to retrieve buyer invoice details
        * @param int $orderid
        * @return array
        */
        public static function orderInvoiceDetails($orderid){
            try{
                //Order invoice details
                $invoice = DB::table ( 'order_invoices as inv' )->where('inv.order_id','=',$orderid)->select('frieght_amt','service_tax_amount','total_amt')->first();
                return $invoice;
            } catch (Exception $ex) {

            }
        }
        
        /*getting truck haule seller post status */
        
        public static function getTruckhaulPostitemStatus($postid){
        	
        $getsellerpositemstatus = DB::table ( 'truckhaul_seller_post_items')
        ->where('seller_post_id','=',$postid)
        ->whereIn('lkp_post_status_id', array(2,3,5))
        ->select('lkp_post_status_id')->get();
        
       return count($getsellerpositemstatus);
        }

         /*getting truck lease seller post status */
        
        public static function getTruckleasePostitemStatus($postid){
        	
        $getsellerpositemstatus = DB::table ( 'trucklease_seller_post_items')
        ->where('seller_post_id','=',$postid)
        ->whereIn('lkp_post_status_id', array(2,3,5))
        ->select('lkp_post_status_id')->get();
        
       return count($getsellerpositemstatus);
        }
        

		/**
		 * Get All Relocation Global Mobility Lookup Services
		 * @return type
		 */
		public static function getLkpRelocationGMServices(){
			try{
				$services_arr = array();
				$relgmservices = DB::table('lkp_relocationgm_services as rlgms')
					->where(['rlgms.is_active' => 1])
					->orderby('rlgms.service_type','asc')
					->select('rlgms.id','rlgms.service_type')
					->get();

				/*creating services key value pair array */
				for($i=0;$i<count($relgmservices);$i++) {
					$services_arr[$relgmservices[$i]->id] = $relgmservices[$i]->service_type;
				}
				return $services_arr;
			} catch (Exception $ex) {

			}
		}        
        
        /**
	 * get All service types functions
	 */
	//Retrive all GM Service Types
	public static function getAllGMServiceTypesforSeller(){
		try{
			$serviceTypes = DB::table('lkp_relocationgm_services')->orderBy ( 'id', 'asc' )
                                ->where('is_active','=',IS_ACTIVE)->select('service_type','measurement_units', 'id')->get();
			return $serviceTypes;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}

	//Retrive all GM Service Types
	public static function getAllGMServiceTypesById($id){
		try{
			$serviceType = DB::table ( 'lkp_relocationgm_services' )->where ( 'lkp_relocationgm_services.id', '=', $id )->select ( 'lkp_relocationgm_services.service_type' )->get ();
			if(isset($serviceType [0]->service_type)) {
				return $serviceType [0]->service_type;
			}
			return "";
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}
        public static function getAllGMServiceTypeUnitsById($id){
		try{
			$serviceType = DB::table ( 'lkp_relocationgm_services' )->where ( 'lkp_relocationgm_services.id', '=', $id )->select ( 'lkp_relocationgm_services.measurement_units' )->get ();
			if(isset($serviceType [0]->measurement_units)) {
				return $serviceType [0]->measurement_units;
			}
			return "";
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}
        
	public function getBuyerPostServicesList($buyer_post_id,$ltype){
               if($ltype == 'spot') {
                  $Query = DB::table ( 'relocationgm_buyer_quote_items as rbqi' );
                  $Query->leftjoin('lkp_relocationgm_services as lkpgms', 'lkpgms.id', '=', 'rbqi.lkp_gm_service_id');
                  $Query->where( 'rbqi.buyer_post_id', $buyer_post_id);   
               } else {
                  $Query = DB::table ( 'term_buyer_quote_items as rbqi' );
                  $Query->leftjoin('lkp_relocationgm_services as lkpgms', 'lkpgms.id', '=', 'rbqi.lkp_gm_service_id');
                  $Query->where( 'rbqi.term_buyer_quote_id', $buyer_post_id);   
               }
			
			$buyer_post_quoteitems_details = $Query->select ('rbqi.*','lkpgms.service_type')->get ();
			return $buyer_post_quoteitems_details;
	}


	public function getTotalBuyerServicesSellerQuotePrice($buyer_post_id,$seller_post_id){
		$Query = DB::table ( 'relocationgm_buyer_quote_sellers_quotes_prices as rsqb' );
		$Query->leftjoin('relocationgm_buyer_quote_items as rbqi', 'rbqi.id', '=', 'rsqb.buyer_quote_item_id');
    	$Query->where( 'rbqi.buyer_post_id', $buyer_post_id);
    	$Query->where( 'rsqb.seller_post_id', $seller_post_id);
		$sellerResults = $Query->select ('rsqb.service_quote')->get ();

		$count = count($sellerResults);
		$total_quote = 0;
		for($i = 0; $i < $count; $i++){
			$total_quote += $sellerResults[$i]->service_quote;
		}
		return $total_quote;
	}

	public function getAllBuyerServicesSellerQuotePrices($buyer_post_id,$seller_post_id){
		$Query = DB::table ( 'relocationgm_buyer_quote_sellers_quotes_prices as rsqb' );
		$Query->leftjoin('relocationgm_buyer_quote_items as rbqi', 'rbqi.id', '=', 'rsqb.buyer_quote_item_id');
		$Query->leftjoin('lkp_relocationgm_services as lkpgms', 'lkpgms.id', '=', 'rbqi.lkp_gm_service_id');
    	$Query->where( 'rbqi.buyer_post_id', $buyer_post_id);
    	$Query->where( 'rsqb.seller_post_id', $seller_post_id);
		$quotesResults = $Query->select ('rsqb.service_quote','lkpgms.id','lkpgms.service_type','lkpgms.measurement_units')->get ();

		return $quotesResults;

	}

    /*
    * get Server URL
    */
    public static function getFrapiUrlByServerUrl()
    {
        /*$server_uri = url();

        if($server_uri=='http://localhost:3000')
            $return_url = 'api.frapi';
        else if($server_uri=='http://dev.logistiks.com')
            $return_url = 'http://api.logistiks.com';
        else if($server_uri=='http://stagenew.logistiks.com')
            $return_url = 'http://stagenewapi.logistiks.com';
        else if($server_uri=='http://demo.logistiks.com')
            $return_url = 'http://demoapi.logistiks.com';
        else if($server_uri=='http://log.quad1test.com')
            $return_url = 'http://api.quad1test.com/';
        */
        $return_url = FRAPI_REDIRECT_URL;
        return  $return_url;
    }
    
    /** Retrieval of all Vehicle Types**/
	public static function getLoadBasedAllPackages($load_type='')
	{
		try
		{//echo $load_type;exit;
                    $packages=array();
			$package_type = DB::table('lkp_packaging_types')
                            ->where('is_active', IS_ACTIVE)->lists('packaging_type_name','id');
                        $service_type = DB::table('lkp_packaging_types as pt')
                            ->leftjoin('lkp_loadtypexpackagingtype as lp','pt.id','=','lp.package_type_id')
                            ->where('lp.load_type_id', $load_type)
                            ->select('pt.id','pt.packaging_type_name')->get();
                        foreach($service_type as $k){
                        $packages[$k->id] =$k->packaging_type_name ;
                        }
                        if(!empty($packages))
                        return $packages;
                        else
                            return $package_type;
			
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	
	/** Retrieval of Buyer Post Details GSA  **/
	public static function getBuyerPostDetailsGSA($postId,$order_id='')
	{
		
		
		try
		{
			if(isset($order_id) && $order_id!=''){
				
                    $quoteitems = DB::table('orders')
					->leftjoin('term_contracts_indent_quantities as tiq','tiq.id','=','orders.term_placeindent_id')
					//->leftjoin ( 'term_contracts as tc', 'tc.id', '=', 'orders.term_contract_id' )
					->where('orders.id', '=', $order_id)
					->select('tiq.*')
					->first();
                            //echo "<pre>";dd($quoteitems);exit;
                            return  $quoteitems;
                        }
			
			$serviceId = Session::get('service_id');
			
			
			switch($serviceId){
				case ROAD_PTL      :
					
					
					$quoteitems = DB::table('ptl_buyer_quotes')
					->leftjoin('ptl_buyer_quote_items as pbqi','pbqi.buyer_quote_id','=','ptl_buyer_quotes.id')
					->leftjoin ( 'lkp_load_types as ldt', 'ldt.id', '=', 'pbqi.lkp_load_type_id' )
					->leftjoin ( 'lkp_packaging_types as lpt', 'lpt.id', '=', 'pbqi.lkp_packaging_type_id' )
					->leftjoin ( 'lkp_ict_weight_uom as liwu', 'liwu.id', '=', 'pbqi.lkp_ict_weight_uom_id' )
					->leftjoin ( 'lkp_ptl_length_uom as lpl', 'lpl.id', '=', 'pbqi.lkp_ptl_length_uom_id' )
					->where('ptl_buyer_quotes.id', '=', $postId)
					->select('ptl_buyer_quotes.transaction_id','pbqi.*','liwu.weight_type','ldt.load_type','lpt.packaging_type_name','lpl.weight_type as length_weight')
					->get();
					break;
				case RAIL       :
					$quoteitems = DB::table('rail_buyer_quotes')
					->leftjoin('rail_buyer_quote_items as pbqi','pbqi.buyer_quote_id','=','rail_buyer_quotes.id')
					->leftjoin ( 'lkp_load_types as ldt', 'ldt.id', '=', 'pbqi.lkp_load_type_id' )
					->leftjoin ( 'lkp_packaging_types as lpt', 'lpt.id', '=', 'pbqi.lkp_packaging_type_id' )
					->leftjoin ( 'lkp_ict_weight_uom as liwu', 'liwu.id', '=', 'pbqi.lkp_ict_weight_uom_id' )
					->leftjoin ( 'lkp_ptl_length_uom as lpl', 'lpl.id', '=', 'pbqi.lkp_ptl_length_uom_id' )
					->where('rail_buyer_quotes.id', '=', $postId)
					->select('rail_buyer_quotes.transaction_id','pbqi.*','liwu.weight_type','ldt.load_type','lpt.packaging_type_name','lpl.weight_type as length_weight')
					->get();
					
					break;
				case AIR_DOMESTIC       :
					
					$quoteitems = DB::table('airdom_buyer_quotes')
					->leftjoin('airdom_buyer_quote_items as pbqi','pbqi.buyer_quote_id','=','airdom_buyer_quotes.id')
					->leftjoin ( 'lkp_load_types as ldt', 'ldt.id', '=', 'pbqi.lkp_load_type_id' )
					->leftjoin ( 'lkp_packaging_types as lpt', 'lpt.id', '=', 'pbqi.lkp_packaging_type_id' )
					->leftjoin ( 'lkp_ict_weight_uom as liwu', 'liwu.id', '=', 'pbqi.lkp_ict_weight_uom_id' )
					->leftjoin ( 'lkp_ptl_length_uom as lpl', 'lpl.id', '=', 'pbqi.lkp_ptl_length_uom_id' )
					->where('airdom_buyer_quotes.id', '=', $postId)
					->select('airdom_buyer_quotes.transaction_id','pbqi.*','liwu.weight_type','ldt.load_type','lpt.packaging_type_name','lpl.weight_type as length_weight')
					->get();
					
					break;
				case AIR_INTERNATIONAL       :
					
					$quoteitems = DB::table('airint_buyer_quotes')
					->leftjoin('airint_buyer_quote_items as pbqi','pbqi.buyer_quote_id','=','airint_buyer_quotes.id')
					->leftjoin ( 'lkp_load_types as ldt', 'ldt.id', '=', 'pbqi.lkp_load_type_id' )
					->leftjoin ( 'lkp_packaging_types as lpt', 'lpt.id', '=', 'pbqi.lkp_packaging_type_id' )
					->leftjoin ( 'lkp_ict_weight_uom as liwu', 'liwu.id', '=', 'pbqi.lkp_ict_weight_uom_id' )
					->leftjoin ( 'lkp_ptl_length_uom as lpl', 'lpl.id', '=', 'pbqi.lkp_ptl_length_uom_id' )
					->where('airint_buyer_quotes.id', '=', $postId)
					->select('airint_buyer_quotes.transaction_id','pbqi.*','liwu.weight_type','ldt.load_type','lpt.packaging_type_name','lpl.weight_type as length_weight')
					->get();
					
					break;
				case OCEAN       :
					$quoteitems = DB::table('ocean_buyer_quotes')
					->leftjoin('ocean_buyer_quote_items as pbqi','pbqi.buyer_quote_id','=','ocean_buyer_quotes.id')
					->leftjoin ( 'lkp_load_types as ldt', 'ldt.id', '=', 'pbqi.lkp_load_type_id' )
					->leftjoin ( 'lkp_packaging_types as lpt', 'lpt.id', '=', 'pbqi.lkp_packaging_type_id' )
					->leftjoin ( 'lkp_ict_weight_uom as liwu', 'liwu.id', '=', 'pbqi.lkp_ict_weight_uom_id' )
					->leftjoin ( 'lkp_ptl_length_uom as lpl', 'lpl.id', '=', 'pbqi.lkp_ptl_length_uom_id' )
					->where('ocean_buyer_quotes.id', '=', $postId)
					->select('ocean_buyer_quotes.transaction_id','pbqi.*','liwu.weight_type','ldt.load_type','lpt.packaging_type_name','lpl.weight_type as length_weight')
					->get();
					break;
				case COURIER       :
					$quoteitems  = DB::table('courier_buyer_quotes')
		            ->leftjoin('courier_buyer_quote_items as pbqi','pbqi.buyer_quote_id','=','courier_buyer_quotes.id')
		            ->leftjoin('lkp_courier_types','lkp_courier_types.id','=','pbqi.lkp_courier_type_id')
		            ->leftjoin('lkp_courier_delivery_types','lkp_courier_delivery_types.id','=','pbqi.lkp_courier_delivery_type_id')
		            ->leftjoin('lkp_ict_weight_uom','lkp_ict_weight_uom.id','=','pbqi.lkp_ict_weight_uom_id')
		            ->leftjoin ('lkp_ptl_length_uom as lpl', 'lpl.id', '=', 'pbqi.lkp_ptl_length_uom_id' )
		            ->where('courier_buyer_quotes.id',$postId)
		            ->select('courier_buyer_quotes.transaction_id','pbqi.*','lkp_courier_types.courier_type',
		                    'lkp_courier_delivery_types.courier_delivery_type','lkp_ict_weight_uom.weight_type','lpl.weight_type as length_weight'
		            )
		            ->get();
					break;
				
			}
			return $quoteitems;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	
	/** Retrieval of Weight  **/
	public static function getLengthWeight($id)
	{
		try
		{
			$getweight = DB::table('lkp_ptl_length_uom')
			->where('lkp_ptl_length_uom.id', '=',$id)
			->select('lkp_ptl_length_uom.weight_type')
			->get();
	
			return $getweight[0]->weight_type;
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	
	/**Relocation buyer posts **/
	
	
	public static function getBuyerRelocationDetailsGSA($id){
		
		try
		{
				
				
			$serviceId = Session::get('service_id');
				
			switch($serviceId){
				case RELOCATION_DOMESTIC      :
						
					$quoteitems = DB::table('relocation_buyer_posts')
					->where('id', '=', $id)
					->select('relocation_buyer_posts.*')
					->get();
					break;
				case RELOCATION_PET_MOVE      :
					
					$quoteitems = DB::table('relocationpet_buyer_posts')
						->where('id', '=', $id)
						->select('relocationpet_buyer_posts.*')
						->get();
						break;
			   case RELOCATION_OFFICE_MOVE      :
								
					$quoteitems = DB::table('relocationoffice_buyer_posts')
					->leftjoin('relocationoffice_buyer_post_inventory_particulars as rbip','rbip.buyer_post_id','=','relocationoffice_buyer_posts.id')
					->where('relocationoffice_buyer_posts.id', '=', $id)
					->select('relocationoffice_buyer_posts.*','rbip.lkp_inventory_office_particular_id','rbip.crating_required','rbip.number_of_items')
					->get();
					break;
				case RELOCATION_INTERNATIONAL  :
					
				$quoteitems = DB::table('relocationint_buyer_posts')
				->where('id', '=', $id)
				->select('relocationint_buyer_posts.*')
				->get();
				break;
				
				case RELOCATION_GLOBAL_MOBILITY      :
				
					$quoteitems = DB::table('relocationgm_buyer_posts')
					->leftjoin('relocationgm_buyer_quote_items as rbip','rbip.buyer_post_id','=','relocationgm_buyer_posts.id')
					->leftjoin('relocationgm_buyer_quote_sellers_quotes_prices as rgbp','rgbp.buyer_quote_item_id','=','rbip.id')
					->where('relocationgm_buyer_posts.id', '=', $id)
					->select('rbip.lkp_gm_service_id','rgbp.service_quote','rbip.measurement','rbip.measurement_units')
					->get();
                                    if(empty($quoteitems)){
                                        $quoteitems = DB::table('relocationgm_buyer_posts as rbp')
					->leftjoin('relocationgm_buyer_quote_items as rbip','rbip.buyer_post_id','=','rbp.id')
					->where('relocationgm_buyer_posts.id', '=', $id)
					->select('rbip.lkp_gm_service_id','rbip.measurement','rbip.measurement_units')
					->get();
                                        
                                    }
					break;
					
					
					
			}
			//echo "<pre>";print_r($quoteitems);exit;
			return $quoteitems;
			}
			catch(\Exception $e)
			{
			//return $e->message;
			}
		
	}
	
	
	/**
	 * Get Relocation Office Particulars
	 * author: Kalyani  / 10052016
	 */
	public static function getOfficeParticularsByid($id){
		try{
			$particulars = DB::table('lkp_inventory_office_particulars')->where('id','=',$id)
			->select ( 'lkp_inventory_office_particulars.office_particular_type' )
			->first ();
				
			return $particulars->office_particular_type;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}


	/**
	 * Get Relocation Global Mobility Particulars
	 * Jagadeesh  / 30062016
	 */
	public static function getGMTermServiceNameByPostItemId($id){
		try{
			$particulars = DB::table('term_buyer_quote_items as tbqi')
			->leftjoin('lkp_relocationgm_services as lrgms','lrgms.id','=','tbqi.lkp_gm_service_id')
			->where('tbqi.id','=',$id)
			->select ( 'lrgms.service_type' )
			->first ();
				
			return $particulars->service_type;
		}
		catch(\Exception $e){
			//return $e->message;
		}
	}	


	/**
	 * @return Tracking types
	 */
	public static function getTrackingTypes(){
		$serviceId  =   Session::get('service_id');
		$excludeRealTime = array(AIR_INTERNATIONAL,OCEAN);
		$trackingtypes = array();
		$trackingtypes[1] = TRACKING_MILE_STONE;
		$trackingtypes[2] = TRACKING_REAL_TIME;
		if(in_array($serviceId,$excludeRealTime)){
			unset($trackingtypes[2]);
		}
		return $trackingtypes;
	}

	/**
	 * @param $id
	 * @return Tracking type
	 */
	public static function getTrackingType($id){
		if($id==1){
			return TRACKING_MILE_STONE;
		}else{
			return TRACKING_REAL_TIME;
		}
	}
	public static function getTrackingTypeOptionsHtml(){
		$trackingOptions = CommonComponent::getTrackingTypes();
		$html = "";
		foreach($trackingOptions as $key => $trackingOption){
			$html .= '<option value="'.$key.'">'.$trackingOption.'</option>';
		}
		return $html;
	}
	
	/** Date conversion to MM-DD-YY **/
	public static function convertDateFlexiDisplay($org_date){
		try{
			if($org_date != "0000-00-00" && $org_date != ""){
				$originalDate = $org_date;
				$originalDate = str_replace ( '/', '-', $originalDate );
				$newDate = date("d-m-Y", strtotime($originalDate));
				return $newDate;
			}
			return "N/A";
	
		}catch (Exception $e) {
			//TODO:: Log the error somewhere
		}
	}
}
