<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Components\CommonComponent;
use App\Components\Matching\SellerMatchingComponent;
use App\Http\Requests;
use App\Models\TruckhaulSellerPost;
use App\Models\TruckhaulSellerPostItem;
use App\Models\TruckhaulSellerSelectedBuyer;
use App\Models\BuyerQuoteSellersQuotesPrices;
use App\Models\TruckhaulSellerPostItemView;
use App\Components\MessagesComponent;
use App\Components\SellerComponent;

use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Redirect;
use Response;
use Log;

use App\Components\Ftl\FtlSellerListingComponent;
use App\Components\Ftl\FtlQuotesComponent;
use App\Components\Ptl\PtlSellerListingComponent;
use App\Components\Rail\RailSellerListingComponent;
use App\Components\Ptl\PtlQuotesComponent;
use App\Components\Rail\RailQuotesComponent;
use App\Components\AirDomestic\AirDomesticQuotesComponent;
use App\Components\AirInternational\AirInternationalQuotesComponent;
use App\Components\Occean\OceanQuotesComponent;
use App\Components\Courier\CourierQuotesComponent;
use App\Components\AirDomestic\AirDomesticSellerListingComponent;
use App\Components\AirInternational\AirInternationalSellerListingComponent;
use App\Components\Occean\OcceanSellerListingComponent;
use App\Components\Courier\CourierSellerListingComponent;
use App\Components\Relocation\RelocationSellerComponent;


class TruckHaulSellerController extends Controller
{   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function __construct() {
		$this->middleware ( 'auth' );		
	}
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function CreateSellerPost()
    {        
        if(Session::get('service_id') == ROAD_FTL){
            return redirect('createseller');
        }else if(Session::get('service_id') == ROAD_PTL || Session::get('service_id') == RAIL 
        		|| Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL 
        		|| Session::get('service_id') == OCEAN || Session::get('service_id') == COURIER){
        	return redirect('ptl/createsellerpost');
        }else if(Session::get('service_id') == RELOCATION_DOMESTIC || Session::get('service_id') == RELOCATION_PET_MOVE || Session::get('service_id') == RELOCATION_INTERNATIONAL || Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY || Session::get('service_id') == RELOCATION_OFFICE_MOVE){
                return redirect('relocation/createsellerpost');
        }else if(Session::get('service_id') == ROAD_TRUCK_LEASE){
                return redirect('trucklease/createsellerpost');
        }

        Log::info('create seller function used for creating a posts: '.Auth::id(),array('c'=>'1'));
    	try {      	
    	$loadtypemasters = CommonComponent::getAllLoadTypes ();
    	$payment_terms = CommonComponent::getPaymentTerms ();
    	$vehicletypemasters = CommonComponent::getAllVehicleType();
        $trackingtypes = CommonComponent::getTrackingTypes();
    	
    	$userId = Auth::User()->id;
    	$user_subcsriptions = DB::table('seller_details')->where('user_id', $userId)->first();
    	if ($user_subcsriptions) {
    	$subscription_start_date = date_create($user_subcsriptions->subscription_start_date);
    	$subscription_end_date = date_create($user_subcsriptions->subscription_end_date);
    	$subscription_start_date_start = date_format($subscription_start_date,"Y-m-d");
    	$subscription_end_date_end = date_format($subscription_end_date,"Y-m-d");
    	$current_date_seller = date("Y-m-d");
    	}else{
    		
    		$user_subcsriptions = DB::table('seller_details')->where('user_id', $userId)->first();
    		$subscription_start_date = date_create($user_subcsriptions->subscription_start_date);
    		$subscription_end_date = date_create($user_subcsriptions->subscription_end_date);
    		$subscription_start_date_start = date_format($subscription_start_date,"Y-m-d");
    		$subscription_end_date_end = date_format($subscription_end_date,"Y-m-d");
    		$current_date_seller = date("Y-m-d");
    		
    	}
    	$session_search_values = array();
    	$url_search= explode("?",HTTP_REFERRER);
    	$url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);
    	
    	if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != ''){
    		$serverpreviUrL =$_SERVER['HTTP_REFERER'];
    	}else{
    		$serverpreviUrL ='';
    	}
    	
    	
    	
    	if($url_search_search == 'buyersearchresults' || Session::get('redirect_truckhaul_service') == '1'){
            
    		if(Session::get('redirect_truckhaul_service') == '1') {
                    $session_search_values[] = '';
                    $session_search_values[] = '';
                    $session_search_values[] = Session::get('session_vehicle_type');
                    $session_search_values[] = '';
                    $session_search_values[] = Session::get('session_from_city_id');
                    $session_search_values[] = Session::get('session_to_city_id');
                    $session_search_values[] = Session::get('session_from_location');
                    $session_search_values[] = Session::get('session_to_location');
                    $session_search_values[] = Session::get('session_seller_district_id');
                    $session_search_values[] = '';
                    $session_search_values[] = '';
                    $session_search_values[] = Session::get('session_ftlvehicle_no');
                    $session_search_values[] = Session::get('session_truckhaul_order_no');
                    $session_search_values[] = Session::get('session_add_truck_flag');
                }else{
                    $session_search_values[] = Session::get('session_delivery_date');
                    $session_search_values[] = Session::get('session_dispatch_date');
                    $session_search_values[] = Session::get('session_vehicle_type');
                    $session_search_values[] = Session::get('session_load_type');
                    $session_search_values[] = Session::get('session_from_city_id');
                    $session_search_values[] = Session::get('session_to_city_id');
                    $session_search_values[] = Session::get('session_from_location');
                    $session_search_values[] = Session::get('session_to_location');
                    $session_search_values[] = Session::get('session_seller_district_id');
                    $session_search_values[] = Session::get('session_ftlprice');
                    $session_search_values[] = Session::get('session_tdays');
                    $session_search_values[] = Session::get('session_ftlvehicle_no');
                    $session_search_values[] = Session::get('session_truckhaul_order_no');
                    $session_search_values[] = Session::get('session_add_truck_flag');
                }

    	}else{
    		$session_search_values[] = Session::put('session_delivery_date','');
    		$session_search_values[] = Session::put('session_dispatch_date','');
    		$session_search_values[] = Session::put('session_vehicle_type','');
    		$session_search_values[] = Session::put('session_load_type','');
    		$session_search_values[] = Session::put('session_from_city_id','');
    		$session_search_values[] = Session::put('session_to_city_id','');
    		$session_search_values[] = Session::put('session_from_location','');
    		$session_search_values[] = Session::put('session_to_location','');
    		$session_search_values[] = Session::put('session_seller_district_id','');
    		$session_search_values[] = Session::put('session_ftlprice','');
    		$session_search_values[] = Session::put('session_tdays','');
    		$session_search_values[] = Session::put('session_ftlvehicle_no','');
    		$session_search_values[] = Session::put('session_truckhaul_order_no','');
    		$session_search_values[] = Session::put('session_add_truck_flag','');

    	}
        return view('truckhaul.sellers.create_seller_post',['loadtypemasters' => $loadtypemasters,
        		'vehicletypemasters' => $vehicletypemasters,
        		'subscription_start_date_start' => $subscription_start_date_start,
        		'subscription_end_date_end' => $subscription_end_date_end,
        		'current_date_seller' => $current_date_seller,
        		'serverpreviUrL' => $serverpreviUrL,
        		'url_search_search' => $url_search_search,
        		'session_search_values_create'=> $session_search_values,
                'trackingtypes'=> $trackingtypes,
        		'PaymentTerms' => $payment_terms]);
        
        } catch (Exception $e) {
        	
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addSellerPost(Request $request)
    {
    	Log::info('Insert the truck haul seller posts data: '.Auth::id(),array('c'=>'1'));
    	try {
    		
    		Session::put('session_delivery_date','');
    		Session::put('session_dispatch_date','');
    		Session::put('session_vehicle_type','');
    		Session::put('session_load_type','');
    		Session::put('session_from_city_id','');
    		Session::put('session_to_city_id','');
    		Session::put('session_from_location','');
    		Session::put('session_to_location','');
    		Session::put('session_seller_district_id','');
    		Session::put('session_ftlprice','');
    		Session::put('session_tdays','');
    		Session::put('session_ftlvehicle_no','');
    		Session::put('session_truckhaul_order_no','');
    		Session::put('session_add_truck_flag','');

    		
    		$roleId = Auth::User()->lkp_role_id;
    		if($roleId == SELLER){
    			CommonComponent::activityLog("TRUCKHAUL_SELLER_CREATED_POSTS",
    					TRUCKHAUL_SELLER_CREATED_POSTS,0,
    					HTTP_REFERRER,CURRENT_URL);
    		}
			if(!empty(Input::all()))  {

			if(isset($_POST['optradio'])){
			$is_private = $_POST['optradio'];
			}
			$randnumber_value = rand(11111,99999);
			$randnumber = "TRUCKHAUL20000".$randnumber_value;
			
			$postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
			$created_year = date('Y');
			$randnumber = 'TRUCKHAUL/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
			
			
			$multi_data = count($_POST['from_location']);
			if(isset($_POST['optradio']) && $is_private == 2){
				if(isset($_POST['buyer_list_for_sellers']) && $_POST['buyer_list_for_sellers'] != ''){
				$buyer_list = explode(",", $_POST['buyer_list_for_sellers']);
				array_shift($buyer_list);
				$buyer_list_count = count($buyer_list);
				}
			}
			$sellerpost  =  new TruckhaulSellerPost();
			$sellerpost->lkp_service_id = ROAD_TRUCK_HAUL;
			$sellerpost->tracking = $request->tracking;
			$sellerpost->lkp_payment_mode_id = $request->Payment_Terms;
			$sellerpost->credit_period = $request->credit_period;
			$sellerpost->credit_period_units = $request->credit_period_units;
			$sellerpost->terms_conditions = $request->terms_conditions;
			if(isset($_POST['optradio']) && $is_private == 2){
			$sellerpost->lkp_access_id = 2;
			}else{
			$sellerpost->lkp_access_id = 1;
			}
			$sellerpost->seller_id =Auth::id();
			$sellerpost->transaction_id =$randnumber;
			$sellerpost->from_date = $_POST['valid_from_val'];
			$sellerpost->to_date = $_POST['valid_to_val'];
			if (Input::get('confirm') == 'Confirm'){
				$lkp_post_status_id = 2;
			}else{
				$lkp_post_status_id = 1;
			}
			$sellerpost->lkp_post_status_id = $lkp_post_status_id;
	
			
			if (isset ( $request->terms_condtion_types1 ) && $request->terms_condtion_types1 != '') {
				$sellerpost->cancellation_charge_text = $request->labeltext [0];
				$sellerpost->cancellation_charge_price = $request->terms_condtion_types1;
			} else {
				$sellerpost->cancellation_charge_text = $request->labeltext [0];
				$sellerpost->cancellation_charge_price = $request->terms_condtion_types1;
			}
			
			if (isset ( $request->terms_condtion_types2 ) && $request->terms_condtion_types2 != '') {
				$sellerpost->docket_charge_text = $request->labeltext [1];
				$sellerpost->docket_charge_price = $request->terms_condtion_types2;
			} else {
				$sellerpost->docket_charge_text = $request->labeltext [1];
				$sellerpost->docket_charge_price = $request->terms_condtion_types2;
			}
						
			$f=1;
			$ft=1;
			for($i=1;$i<=$request->next_terms_count_search;$i++){
				
				
				if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] != '') {
					$field_name="other_charge".$f."_text";
					$sellerpost->$field_name = $_POST['labeltext_'.$i];
					$f++;
				}
				if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] == '') {
					$f++;
				}
				if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] != '') {
					$field_name="other_charge".$ft."_price";
					$sellerpost->$field_name = $_POST['terms_condtion_types_'.$i];
					$ft++;
				}
				if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] == '') {
					$ft++;
				}
				
			}
			
			
			if (is_array($request->accept_payment)){
			$sellerpost->accept_payment_netbanking = in_array(1,$request->accept_payment) ? 1 :0;
			$sellerpost->accept_payment_credit = in_array(2,$request->accept_payment) ? 1 :0;
			$sellerpost->accept_payment_debit = in_array(3,$request->accept_payment) ? 1 :0;
			}else{
			$sellerpost->accept_payment_netbanking = 0;
			$sellerpost->accept_payment_credit = 0;
			$sellerpost->accept_payment_debit = 0;
			}
			
			if (is_array($request->accept_credit_netbanking)){
				$sellerpost->accept_credit_netbanking = in_array(1,$request->accept_credit_netbanking) ? 1 :0;
				$sellerpost->accept_credit_cheque = in_array(2,$request->accept_credit_netbanking) ? 1 :0;
			}else{
				$sellerpost->accept_credit_netbanking = 0;
				$sellerpost->accept_credit_cheque = 0;
			}
			
			$created_at = date ( 'Y-m-d H:i:s' );
			$createdIp = $_SERVER ['REMOTE_ADDR'];
			$sellerpost->created_by = Auth::id();
			$sellerpost->created_at = $created_at;
			$sellerpost->created_ip = $createdIp;
			
			$matchedItems = array ();
				if($sellerpost->save()){
					
					CommonComponent::auditLog($sellerpost->id,'truckhaul_seller_posts');
					for($i = 0; $i < $multi_data; $i ++) {
						$sellerpost_lineitem  =  new TruckhaulSellerPostItem();
						$sellerpost_lineitem->vehicle_number = str_replace(' ','',$_POST['vehicle_number'][$i]);
						$sellerpost_lineitem->seller_post_id = $sellerpost->id;
						$sellerpost_lineitem->from_location_id = $_POST['from_location'][$i];
						$sellerpost_lineitem->to_location_id = $_POST['to_location'][$i];
						$sellerpost_lineitem->lkp_district_id = $_POST['sellerdistrict'][$i];
						if(isset($_POST['load_type'][$i]) && $_POST['load_type'][$i]!=''){
						$sellerpost_lineitem->lkp_load_type_id = $_POST['load_type'][$i];
						}
						$sellerpost_lineitem->lkp_vehicle_type_id = $_POST['vechile_type'][$i];
						$sellerpost_lineitem->transitdays = $_POST['transitdays'][$i];
						$sellerpost_lineitem->units = $_POST['units'][$i];
						$sellerpost_lineitem->price = $_POST['price'][$i];
						$sellerpost_lineitem->lkp_post_status_id = $lkp_post_status_id;
						$sellerpost_lineitem->is_cancelled = 0;
						$created_at = date ( 'Y-m-d H:i:s' );
						$createdIp = $_SERVER ['REMOTE_ADDR'];
						$sellerpost_lineitem->created_by = Auth::id();
						$sellerpost_lineitem->created_at = $created_at;
						$sellerpost_lineitem->created_ip = $createdIp;
						$sellerpost_lineitem->save();

						//*******matching engine***********************//
						if($lkp_post_status_id == 2){
							$matchedItems['from_city_id']=$_POST['from_location'][$i];
							$matchedItems['to_city_id']=$_POST['to_location'][$i];
							$matchedItems['lkp_load_type_id']=$_POST['load_type'][$i];
							$matchedItems['lkp_vehicle_type_id']=$_POST['vechile_type'][$i];
							$matchedItems['valid_from']=$_POST['valid_from_val'];
							$matchedItems['valid_to']=$_POST['valid_to_val'];
							if($_POST['units'][$i]=='Weeks')
								$matchedItems['transit_days']=$_POST['transitdays'][$i]*7;
							else 
								$matchedItems['transit_days']=$_POST['transitdays'][$i];
							SellerMatchingComponent::doMatching(ROAD_TRUCK_HAUL,$sellerpost_lineitem->id,2,$matchedItems);
						}
						//*******matching engine***********************//
						CommonComponent::auditLog($sellerpost_lineitem->id,'truckhaul_seller_post_items');
					}
					if(isset($_POST['optradio']) && $is_private == 2){
						if(isset($_POST['buyer_list_for_sellers']) && $_POST['buyer_list_for_sellers'] != ''){	
						for($i = 0; $i < $buyer_list_count; $i ++) {
						$sellerpost_for_buyers  =  new TruckhaulSellerSelectedBuyer();
						$sellerpost_for_buyers->seller_post_id = $sellerpost->id;
						$sellerpost_for_buyers->buyer_id = $buyer_list[$i];
						$created_at = date ( 'Y-m-d H:i:s' );
						$createdIp = $_SERVER ['REMOTE_ADDR'];
						$sellerpost_for_buyers->created_by = Auth::id();
						$sellerpost_for_buyers->created_at = $created_at;
						$sellerpost_for_buyers->created_ip = $createdIp;
						$sellerpost_for_buyers->save();
						$seller_selected_buyers_email = DB::table('users')->where('id', $buyer_list[$i])->get();
						$seller_selected_buyers_email[0]->randnumber = $randnumber;
						$seller_selected_buyers_email[0]->sellername = Auth::User()->username;
						CommonComponent::send_email(TRUCKHAUL_SELLER_CREATED_POST_FOR_BUYERS,$seller_selected_buyers_email);
						
						
						if($lkp_post_status_id == 2){
						//*******Send Sms to the private buyers***********************//
						$msg_params = array(
								'randnumber' => $randnumber,
								'sellername' => Auth::User()->username,
								'servicename' => 'TRUCKHAUL'
								);
						$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
						CommonComponent::sendSMS($getMobileNumber,TRUCKHAUL_SELLER_CREATED_POST_FOR_BUYERS,$msg_params);
						//*******Send Sms to the private buyers***********************//
						}
						CommonComponent::auditLog($sellerpost_for_buyers->id,'truckhaul_seller_selected_buyers');
					}
						}
					}
					if(isset($_POST['ftl_order_id']) && isset($_POST['ftl_flag_set']) && $_POST['ftl_flag_set']!= '' && $_POST['ftl_order_id']!=''){
					Session::put('service_id',ROAD_FTL);
					}
					if (Input::get('confirm') == 'Confirm'){
						return $randnumber;
					}else{
                        // if ftl_order_id isset then redirect to ftl consignment pickup page with requested ftl_order_id    
                        if(isset($_POST['ftl_order_id']) && isset($_POST['ftl_flag_set']) && $_POST['ftl_flag_set']!= '' && $_POST['ftl_order_id']!='')
                            return redirect('/consignment_pickup/'.$_POST['ftl_order_id']);
                        else // not isset ftl_order_id then redirect to truck haul list page
						  return redirect('/sellerlist')->with('message_create_post', 'Post was saved as draft');
					}
				}
		}
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

    }

   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSeller(Request $request, $sid)
    {
    	$post_id_status = DB::table('seller_posts')->where('id', $sid)->first();
    	
    	Log::info('Update the seller individual posts data: '.Auth::id(),array('c'=>'1'));
		$sellerpost  =  new TruckhaulSellerPost();


		if(!empty(Input::all()))  {
			if (Input::get('confirm') == 'Confirm'){
				$poststatus = 2;
			}else{
				$poststatus = 1;
			}
			
			if(isset($_POST['optradio'])){
				$is_private = $_POST['optradio'];
			}
			if(isset($_POST['optradio']) && $is_private == 2){
				if(isset($_POST['buyer_list_for_sellers_hidden']) && $_POST['buyer_list_for_sellers_hidden'] != ''){
					$buyer_list = explode(",", $_POST['buyer_list_for_sellers_hidden']);
					array_shift($buyer_list);
					$buyer_list_count = count($buyer_list);
				}
			}
			$randnumber_value = rand(11111,99999);
			$randnumber = "FTL20000".$randnumber_value;

			if(isset($_POST['optradio']) && $is_private == 2){
				$lkp_access_id = 2;
			}else{
				$lkp_access_id = 1;
			}
			
			if (isset($_POST['accept_payment']) && is_array($_POST['accept_payment'])){
				$accept_payment_netbanking = in_array(1,$_POST['accept_payment']) ? 1 :0;
				$accept_payment_credit = in_array(2,$_POST['accept_payment']) ? 1 :0;
				$accept_payment_debit = in_array(3,$_POST['accept_payment']) ? 1 :0;
			}else{
				$accept_payment_netbanking = 0;
				$accept_payment_credit = 0;
				$accept_payment_debit = 0;
			}
			
			if (isset($_POST['accept_credit_netbanking']) && is_array($_POST['accept_credit_netbanking'])){
				$accept_credit_netbanking = in_array(1,$_POST['accept_credit_netbanking']) ? 1 :0;
				$accept_credit_cheque = in_array(2,$_POST['accept_credit_netbanking']) ? 1 :0;
			}else{
				$accept_credit_netbanking = 0;
				$accept_credit_cheque = 0;
			}
			
			$otherCharges = array();
			if(isset($_POST['next_terms_count_search'])){
				$j = 0;
				for($i=1;$i<=$_POST['next_terms_count_search'];$i++){
					if(isset($_POST["labeltext_$i"]) && isset($_POST["terms_condtion_types_$i"])){
						$otherCharges["labeltext"][$j] = $_POST["labeltext_$i"];
						$otherCharges["terms_condtion_types"][$j] = $_POST["terms_condtion_types_$i"];
						$j++;
					}
				}
			}
				$arr	=	array(
					'to_date' => $request->input('valid_to_val'),
					'tracking' => $request->input('tracking'),
					'terms_conditions' => $request->input('terms_conditions'),
					'other_charge1_text' => (isset ( $otherCharges ['labeltext'] ['0'] )) ? $otherCharges ['labeltext'] ['0'] : "",
					'other_charge2_text' => (isset ( $otherCharges ['labeltext'] ['1'] )) ? $otherCharges ['labeltext'] ['1'] : "",
					'other_charge3_text' => (isset ( $otherCharges ['labeltext'] ['2'] )) ? $otherCharges ['labeltext'] ['2'] : "",
					'cancellation_charge_price' => (isset ( $_POST ['terms_condtion_types1'])) ? $_POST ['terms_condtion_types1'] : "",
					'docket_charge_price' => (isset ( $_POST ['terms_condtion_types2'])) ? $_POST ['terms_condtion_types2'] : "",
					'other_charge1_price' => (isset ( $otherCharges ['terms_condtion_types'] [0] )) ? $otherCharges ['terms_condtion_types'] [0] : "",
					'other_charge2_price' => (isset ( $otherCharges ['terms_condtion_types'] [1] )) ? $otherCharges ['terms_condtion_types'] [1] : "",
					'other_charge3_price' => (isset ( $otherCharges ['terms_condtion_types'] [2] )) ? $otherCharges ['terms_condtion_types'] [2] : "",
					'lkp_post_status_id' => $poststatus,
					'lkp_access_id' => $lkp_access_id
			);
			if($post_id_status->lkp_post_status_id != 2){
				$arr['lkp_payment_mode_id']=$request->input('Payment_Terms');
				$arr['accept_payment_netbanking']=$accept_payment_netbanking;
				$arr['accept_payment_credit']= $accept_payment_credit;
				$arr['accept_payment_debit'] = $accept_payment_debit;
				$arr['credit_period'] = $request->input('credit_period');
				$arr['credit_period_units'] = $request->input('credit_period_units');
				$arr['accept_credit_netbanking'] = $accept_credit_netbanking;
				$arr['accept_credit_cheque'] = $accept_credit_cheque;
			}
			$sellerpost::where(
					"id",$sid)->update($arr);
				
			$multi_data = count($_POST['from_location']);
			for($i = 0; $i < $multi_data; $i ++) {
				$sellerpost_lineitem = new TruckhaulSellerPostItem();
				
				//*******matching engine***********************//
				if($poststatus == 2){
					$matchedItems['from_city_id']=$_POST['from_location'][$i];
					$matchedItems['to_city_id']=$_POST['to_location'][$i];
					$matchedItems['lkp_load_type_id']=$_POST['load_type'][$i];
					$matchedItems['lkp_vehicle_type_id']=$_POST['vechile_type'][$i];
					$matchedItems['valid_from']=$_POST['valid_from_val'];
					$matchedItems['valid_to']=$_POST['valid_to_val'];
					if($_POST['units'][$i]=='Weeks')
						$matchedItems['transit_days']=$_POST['transitdays'][$i]*7;
					else 
						$matchedItems['transit_days']=$_POST['transitdays'][$i];
					SellerMatchingComponent::doMatching("1",$_POST['post_id'][$i],2,$matchedItems);
				}
				//*******matching engine***********************//
				
				$sellerpost_lineitem::where(
					"id",$_POST['post_id'][$i])->update(array(
					'lkp_load_type_id' => $_POST['load_type'][$i],
					'lkp_vehicle_type_id' => $_POST['vechile_type'][$i],
					'transitdays' => $_POST['transitdays'][$i],
					'units' => $_POST['units'][$i],
					'price' => $_POST['price'][$i],
					'lkp_post_status_id' => $poststatus,
				));
			}
			
			
				if(isset($_POST['optradio']) && $is_private == 2){
					if(isset($_POST['buyer_list_for_sellers_hidden']) && $_POST['buyer_list_for_sellers_hidden'] != ''){
						$post_list_of_buyers = DB::table('seller_selected_buyers')->where('seller_post_id', $sid)->lists('buyer_id');
						DB::table('seller_selected_buyers')->where('seller_post_id', $sid)->delete();
						for($i = 0; $i < $buyer_list_count; $i ++) {
							$sellerpost_for_buyers  =  new TruckhaulSellerSelectedBuyer();
							$sellerpost_for_buyers->seller_post_id = $sid;
							$sellerpost_for_buyers->buyer_id = $buyer_list[$i];
							$created_at = date ( 'Y-m-d H:i:s' );
							$createdIp = $_SERVER ['REMOTE_ADDR'];
							$sellerpost_for_buyers->created_by = Auth::id();
							$sellerpost_for_buyers->created_at = $created_at;
							$sellerpost_for_buyers->created_ip = $createdIp;
							//saving to database
							$sellerpost_for_buyers->save();
							if (!in_array($buyer_list[$i], $post_list_of_buyers)){
							$seller_selected_buyers_email = DB::table('users')->where('id', $buyer_list[$i])->get();
							$seller_selected_buyers_email[0]->randnumber = $randnumber;
							$seller_selected_buyers_email[0]->sellername = Auth::User()->username;
							CommonComponent::send_email(SELLER_CREATED_POST_FOR_BUYERS,$seller_selected_buyers_email);
							}
							//CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
						}
					}
				}
			
			return redirect("/updateseller/$post_id_status->id")->with('transactionId',$post_id_status->transaction_id);
		
		}

    	try {
    	$vehicletypemasters = DB::table('lkp_vehicle_types')->lists('vehicle_type', 'id');
    	$loadtypemasters = DB::table('lkp_load_types')->lists('load_type', 'id');
    	$payment_terms = DB::table('lkp_payment_modes')->lists('payment_mode', 'id');

		$seller_post_edit_action = DB::table('seller_posts')
								->where('seller_posts.id',$sid)
								->select('seller_posts.*')
								->first();

		$seller_post_edit_action_lines = DB::table('seller_post_items')
										->leftjoin('lkp_cities as c1','seller_post_items.from_location_id','=','c1.id')
										->leftjoin('lkp_cities as c2','seller_post_items.to_location_id','=','c2.id')
										->leftjoin('lkp_load_types as lt','seller_post_items.lkp_load_type_id','=','lt.id')
										->leftjoin('lkp_vehicle_types as vt','seller_post_items.lkp_vehicle_type_id','=','vt.id')
										->where('seller_post_items.seller_post_id',$sid)
										->select('seller_post_items.*','c1.city_name as from_locationcity','c2.city_name as to_locationcity','lt.load_type','vt.vehicle_type')
										->get();
		$selectedbuyers =  DB::table('seller_selected_buyers')
								->leftjoin('users as u','seller_selected_buyers.buyer_id','=','u.id')
								->leftjoin ( 'buyer_business_details as bbds', 'seller_selected_buyers.buyer_id', '=', 'bbds.user_id' )
								->where('seller_selected_buyers.seller_post_id',$sid)
								->select('seller_selected_buyers.buyer_id','u.username', 'bbds.principal_place')
								->get();

		$userId = Auth::User()->id;
		$user_subcsriptions = DB::table('seller_details')->where('user_id', $userId)->first();
		if ($user_subcsriptions) {
			$subscription_start_date = date_create($user_subcsriptions->subscription_start_date);
			$subscription_end_date = date_create($user_subcsriptions->subscription_end_date);
			$subscription_start_date_start = date_format($subscription_start_date,"Y-m-d");
			$subscription_end_date_end = date_format($subscription_end_date,"Y-m-d");
			$current_date_seller = date("Y-m-d");
		}else{

			$user_subcsriptions = DB::table('seller_details')->where('user_id', $userId)->first();
			$subscription_start_date = date_create($user_subcsriptions->subscription_start_date);
			$subscription_end_date = date_create($user_subcsriptions->subscription_end_date);
			$subscription_start_date_start = date_format($subscription_start_date,"Y-m-d");
			$subscription_end_date_end = date_format($subscription_end_date,"Y-m-d");
			$current_date_seller = date("Y-m-d");

		}

    	if (isset($seller_post_edit_action->lkp_access_id) && $seller_post_edit_action->lkp_access_id == 1) {
    		$private_seller = false;
    		$public_seller = true;
    	}else{
    		$private_seller = true;
    		$public_seller = false;
    	}
    	
        return view('sellers.updatepost',[
        		'transactionId'=>'',
        		'seller_post_edit' => $seller_post_edit_action,
        		//'seller_post_edit_action_single' => $seller_post_edit_action_single,
				'seller_post_edit_action_lines' => $seller_post_edit_action_lines,
        		'loadtypemasters' => $loadtypemasters,
        		'private' => $private_seller,
        		'public' => $public_seller,
        		'PaymentTerms' => $payment_terms,
        		'vehicletypemasters' => $vehicletypemasters,
        		'seller_postid' => $sid,
				'selectedbuyers' => $selectedbuyers,
				'subscription_start_date_start' => $subscription_start_date_start,
				'subscription_end_date_end' => $subscription_end_date_end,
				'current_date_seller' => $current_date_seller

        ]
        		);
        } catch (Exception $e) {
        	echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
    public function updateSellerPost(Request $request)
    {
    Log::info('Update the seller individual posts data once click on update button in edit page: '.Auth::id(),array('c'=>'1'));

    	try {
    		
    		$roleId = Auth::User()->lkp_role_id;
    		if($roleId == SELLER){
    			CommonComponent::activityLog("SELLER_UPDATED_POSTS",
    					SELLER_UPDATED_POSTS,0,
    					HTTP_REFERRER,CURRENT_URL);
    		}
    		
    		$spid= $request->input('seller_post_id');
    		$sellerpost_lineitem  =  new TruckhaulSellerPostItem();
    		$sellerpost_lineitem::where(
    				"id",$request->input('seller_post_id'))->update(array(
    						'from_location_id' => $request->input('from_location_id'),
    						'to_location_id' => $request->input('to_location_id'),
    						'lkp_load_type_id' => $request->input('LoadTypeMasters'),
    						'lkp_vehicle_type_id' => $request->input('VehicleTypeMasters'),
    						'transitdays' => $request->input('transitdays'),
    						'units' => $request->input('units'),
    						'price' => $request->input('price')
    				));
    				CommonComponent::auditLog($spid,'seller_post_items');
    				return redirect("/sellerpostdetail/$spid")->with('updatedseller', 'Post was updated Successfully');

    	} catch (Exception $e) {
    		echo 'Caught exception: ',  $e->getMessage(), "\n";
    	}
    }

    /**
     * Retrive the Vehicle dimension and Capacity
     *
     * @return \Illuminate\Http\Response
     */
    public function getVehicleType()
    {
    	Log::info('get the dimension and capacity and units of vehicle in creatation of seller post: '.Auth::id(),array('c'=>'1'));
    	try {
    	$vehicle_id = $_POST['id'];
    	$vehicle_types = DB::table('lkp_vehicle_types')->select('dimension', 'capacity', 'units')->where('id', $vehicle_id)->get();
    	echo $vehicle_types[0]->dimension. "-" .$vehicle_types[0]->capacity. "-" .$vehicle_types[0]->units;
    	die();
    	} catch (Exception $e) {
    		echo 'Caught exception: ',  $e->getMessage(), "\n";
    	}
    }
    
	public function autocomplete(){
		Log::info('get from location using autocomplete: '.Auth::id(),array('c'=>'1'));
    	try {
    		
	    	if(Session::get('service_id') == AIR_INTERNATIONAL){
	    		$term = Input::get('term');
	    		$fromlocation_loc = Input::get('fromlocation');
	    		$results = array();
	    		if(isset($fromlocation_loc)){
	    			$queries = DB::table('lkp_airports')
	    			->where('airport_name', 'LIKE', $term.'%')
	    			->where('id','<>', $fromlocation_loc)
	    			->take(10)->get();
	    		}else {
	    			$queries = DB::table('lkp_airports')
	    			->where('airport_name', 'LIKE', $term.'%')
	    			->take(10)->get();
	    		}
	    		foreach ($queries as $query)
	    		{
	    			$results[] = [ 'id' => $query->id, 'value' => $query->airport_name.' , '.$query->location ];
	    		}
	    		return Response::json($results);
	    	}elseif(Session::get('service_id') == OCEAN){
	    		$term = Input::get('term');
	    		$fromlocation_loc = Input::get('fromlocation');
	    		$results = array();
	    		if(isset($fromlocation_loc)){
	    			$queries = DB::table('lkp_seaports')
	    			->where('seaport_name', 'LIKE', $term.'%')
	    			->where('id','<>', $fromlocation_loc)
	    			->take(10)->get();
	    		}else {
	    			$queries = DB::table('lkp_seaports')
	    			->where('seaport_name', 'LIKE', $term.'%')
	    			->take(10)->get();
	    		}
	    		foreach ($queries as $query)
	    		{
	    			$results[] = [ 'id' => $query->id, 'value' => $query->seaport_name.' , '.$query->country_name ];
	    		}
	    		return Response::json($results);
	    	}
	    	else{
		    	$term = Input::get('term');
		    	$fromlocation_loc = Input::get('fromlocation');
		    	$results = array();
		    	if(isset($fromlocation_loc)){
		    		$queries = DB::table('lkp_cities')
		    		->where('city_name', 'LIKE', $term.'%')
		    		->where('id','<>', $fromlocation_loc)
		    		->take(10)->get();
		    	}else {
		    		$queries = DB::table('lkp_cities')
		    		->where('city_name', 'LIKE', $term.'%')
		    		->take(10)->get();
		    	}
		    	foreach ($queries as $query)
		    	{
		    		$results[] = [ 'id' => $query->id, 'value' => $query->city_name, 'dist_id'=> $query->lkp_district_id ];
		    	}
		    	return Response::json($results);
	    	}
    	} catch (Exception $e) {
    		echo 'Caught exception: ',  $e->getMessage(), "\n";
    	}
    }
    
    public function lineItemsCheck() {
    	Log::info('validate the line items,to avoid the dupulicates while creating truck haul seller posts:'.Auth::id(),array('c'=>'1'));
    	try {
    		$from_date=$_POST ['from_date_seller'];
    		$to_date=$_POST ['to_date_seller'];
			$results = DB::table('truckhaul_seller_posts as spc')
			->join('truckhaul_seller_post_items as spic','spic.seller_post_id','=','spc.id')
			->where('spic.from_location_id', $_POST ['from_location'])
			->where('spic.to_location_id', $_POST ['to_location'])
			->whereRaw ("(`from_date` between  '$from_date' and '$to_date' or `to_date` between '$from_date' and '$to_date')")
			->where('spic.lkp_vehicle_type_id', $_POST ['vehicle_type'])
			->where('spic.lkp_load_type_id', $_POST ['load_type'])
			->where('spic.lkp_post_status_id','<>',5)
                        ->where('spic.transitdays', $_POST ['transit_days'])        
			->where('spc.seller_id', Auth::id());
			if(isset($_POST['post_item_id'])){
				$results->where('spic.id','<>', $_POST ['post_item_id']);
			}
			$results = $results->get();
			if (count ( $results )) {
				echo '1';
			} else {
				echo '0';
			}
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

	}
	public function lineItemsCheckPtl() {
		Log::info('validate the line items,to avoid the dupulicates while creating posts:'.Auth::id(),array('c'=>'1'));
		try {
			
			
			$from_date=$_POST ['from_date_seller'];
			$to_date=$_POST ['to_date_seller'];
			
			if(Session::get('service_id') == ROAD_PTL){
				$results= DB::table('ptl_seller_posts as pspc')
						->join('ptl_seller_post_items as pspic','pspic.seller_post_id','=','pspc.id')
						->where('pspic.from_location_id', $_POST ['from_location'])
						->where('pspic.to_location_id', $_POST ['to_location'])
						->whereRaw ( "  ( `from_date` between  '$from_date'and '$to_date' or `to_date` between '$from_date'and '$to_date'  )") 			
						->where('pspc.lkp_ptl_post_type_id', $_POST ['zone_location_id_value'])
						->where('pspic.lkp_post_status_id','<>',5)
                                                ->where('pspic.transitdays', $_POST ['transit_days'])   
						->where('pspc.seller_id', Auth::id());
			}
			if(Session::get('service_id') == COURIER){
				$results= DB::table('courier_seller_posts as pspc')
                                        ->join('courier_seller_post_items as pspic','pspic.seller_post_id','=','pspc.id')
                                        ->where('pspic.from_location_id', $_POST ['from_location'])
                                        ->where('pspic.to_location_id', $_POST ['to_location'])
                                        ->whereRaw ( "  ( `from_date` between  '$from_date'and '$to_date' or `to_date` between '$from_date'and '$to_date'  )")
                                        ->where('pspc.lkp_ptl_post_type_id', $_POST ['zone_location_id_value'])
					->where('pspic.lkp_post_status_id','<>',5)
                                        ->where('pspic.transitdays', $_POST ['transit_days'])
					->where('pspc.seller_id', Auth::id());
			}
			if(Session::get('service_id') == RAIL){
				//print_r($_POST);exit;
				$results= DB::table('rail_seller_posts as pspc')
						->join('rail_seller_post_items as pspic','pspic.seller_post_id','=','pspc.id')
						->where('pspic.from_location_id', $_POST ['from_location'])
						->where('pspic.to_location_id', $_POST ['to_location'])
						->whereRaw ( "  ( `from_date` between  '$from_date'and '$to_date' or `to_date` between '$from_date'and '$to_date'  )") 			
						->where('pspc.lkp_ptl_post_type_id', $_POST ['zone_location_id_value'])
						->where('pspic.lkp_post_status_id','<>',5)
                                                ->where('pspic.transitdays', $_POST ['transit_days'])
						->where('pspc.seller_id', Auth::id());
			}
			if(Session::get('service_id') == AIR_DOMESTIC){

				$results= DB::table('airdom_seller_posts as pspc')
                                        ->join('airdom_seller_post_items as pspic','pspic.seller_post_id','=','pspc.id')
                                        ->where('pspic.from_location_id', $_POST ['from_location'])
                                        ->where('pspic.to_location_id', $_POST ['to_location'])
                                        ->whereRaw ( "  ( `from_date` between  '$from_date'and '$to_date' or `to_date` between '$from_date'and '$to_date'  )")
                                        ->where('pspc.lkp_ptl_post_type_id', $_POST ['zone_location_id_value'])
					->where('pspic.lkp_post_status_id','<>',5)
                                        ->where('pspic.transitdays', $_POST ['transit_days'])
					->where('pspc.seller_id', Auth::id());
			}
			if(Session::get('service_id') == AIR_INTERNATIONAL){

				$results= DB::table('airint_seller_posts as pspc')
				->join('airint_seller_post_items as pspic','pspic.seller_post_id','=','pspc.id')
				->where('pspic.from_location_id', $_POST ['from_location'])
				->where('pspic.to_location_id', $_POST ['to_location'])
				->whereRaw ( "  ( `from_date` between  '$from_date'and '$to_date' or `to_date` between '$from_date'and '$to_date'  )")
				->where('pspc.lkp_ptl_post_type_id', $_POST ['zone_location_id_value'])
				->where('pspic.lkp_post_status_id','<>',5)
                                ->where('pspic.transitdays', $_POST ['transit_days'])        
				->where('pspc.seller_id', Auth::id());
			}
			if(Session::get('service_id') == OCEAN){

				$results= DB::table('ocean_seller_posts as pspc')
                                        ->join('ocean_seller_post_items as pspic','pspic.seller_post_id','=','pspc.id')
                                        ->where('pspic.from_location_id', $_POST ['from_location'])
                                        ->where('pspic.to_location_id', $_POST ['to_location'])
                                        ->whereRaw ( "  ( `from_date` between  '$from_date'and '$to_date' or `to_date` between '$from_date'and '$to_date'  )")
                                        ->where('pspc.lkp_ptl_post_type_id', $_POST ['zone_location_id_value'])
					->where('pspic.lkp_post_status_id','<>',5)
                                        ->where('pspic.transitdays', $_POST ['transit_days'])
					->where('pspc.seller_id', Auth::id());
			}
			if(isset($_POST['post_item_id'])){
				$results->where('pspic.id','<>', $_POST ['post_item_id']);
			}
			$results = $results->get();
			
			if (count($results)) {
				echo '1';
			} else {
				echo '0';
			}
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	
	}

	public function lineItemsCheckRelocation(){
		Log::info('validate the line items,to avoid the dupulicates while creating posts:'.Auth::id(),array('c'=>'1'));
		try {
			$from_date=$_POST ['from_date_seller'];
			$to_date=$_POST ['to_date_seller'];
			if($_POST ['rate_card_type'] == 1){
				$ratetypes = array(1,3);
			}else if($_POST ['rate_card_type'] == 2){
				$ratetypes = array(2,3);
			}

			if(Session::get('service_id') == RELOCATION_DOMESTIC){
				$results= DB::table('relocation_seller_posts as rspc')
					->join('relocation_seller_post_items as pspic','pspic.seller_post_id','=','rspc.id')
					->where('rspc.from_location_id', $_POST ['from_location_id'])
					->where('rspc.to_location_id', $_POST ['to_location_id'])
					->whereRaw ( "  ( `rspc`.`from_date` between  '$from_date'and '$to_date' or `rspc`.`to_date` between '$from_date'and '$to_date'  )")
					->where('pspic.rate_card_type', $_POST ['rate_card_type']);
				if($_POST ['rate_card_type'] == 1){
					$results->where('pspic.lkp_property_type_id',$_POST['propertytypes']);
					$results->where('pspic.lkp_load_category_id',$_POST['load_types']);
				}else if($_POST ['rate_card_type'] == 2){
					$results->where('pspic.lkp_vehicle_category_id',$_POST['vehicle_types']);
					$results->where('pspic.lkp_car_size',$_POST['vehicle_type_category']);
				}
				$results->where('rspc.lkp_post_status_id','<>',5)
					->where('pspic.transitdays', $_POST ['transit_days'])
					->where('pspic.units', $_POST ['transit_days_units'])
					->where('rspc.seller_id', Auth::id());
			}
			if(isset($_POST['post_item_id'])){
				$results->where('pspic.id','<>', $_POST ['post_item_id']);
			}

			$results = $results->get();

			if (count($results)) {
				echo '1';
			} else {
				echo '0';
			}
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}
	public function checkSubcriptionUser() {
		try {
			if(Auth::User()->lkp_role_id == BUYER){
				echo '1';
			}else{
				$results_users = DB::table('seller_services')
				->where('lkp_service_id', $_POST ['serviceidcheck_id'])
				->where('user_id', Auth::id());
				$results_users_check = $results_users->get();
				if (count ( $results_users_check )) {
					echo '1';
				} else {
					echo '0';
				}
			}
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	
	}
	
	/**
	 * Showing the list of seller post
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function sellerPostList() {
		Log::info('get seller posts list while seller creating post:'.Auth::id(),array('c'=>'1'));
		
		try{
			$seller_post = DB::table('seller_posts')
						 ->join('seller_post_items','seller_post_items.seller_post_id','=','seller_posts.id')
						 ->select('seller_posts.from_date','seller_posts.to_date','seller_post_items.id')
						 ->get();
		} catch (Exception $e) {
		
		}
		return view('sellers.seller_post_list',['seller_post'=>$seller_post]);
	}
	
	/**
	 * Showing the detailed list of seller post
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function sellerPostDetails($id) {
		
		Log::info('Display the individual post with all details:'.Auth::id(),array('c'=>'1'));
		Session::put('seller_post_item', $id);
		try{
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}		
			// Saving the user activity to the log table
			
				CommonComponent::activityLog ( "FTL_SELLER_DETAIL",
				FTL_SELLER_DETAIL, 0,
				HTTP_REFERRER, CURRENT_URL );
			
       
			switch($serviceId){
				case ROAD_FTL       : 
				$gridval = FtlSellerListingComponent::listFTLSellerPostDetailsItems($id);
				$post_id_parent = DB::table('seller_post_items')->select('seller_post_id')->where('id', $id)->first();
				$post_id_parent->seller_post_id;
                $allMessagesList = MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$id);

				return view('sellers.seller_post_details',
                                                ['seller_post_id'=>$gridval['id'],
						'seller_post'=>$gridval['seller_post'],
                         'seller_post_id'=>$post_id_parent->seller_post_id,
						'seller_post_items'=>$gridval['seller_post_items'],
						'fromlocations'=>$gridval['fromlocations'],
						'tolocations'=>$gridval['tolocations'],
						'loadtype'=>$gridval['loadtype'],
						'vehicletype'=>$gridval['vehicletype'],
						'paymenttype'=>$gridval['paymenttype'],
						'buyerdetails'=>$gridval['buyerdetails'],
						'buyerpublicquotedetails'=>$gridval['buyerpublicquotedetails'],
						'buyerleadsquotedetails'=>$gridval['buyerleadsquotedetails'],
						'subscriptionstdate'=>$gridval['subscriptionstdate'],
						'subscriptionenddate'=>$gridval['subscriptionenddate'],
						'buyersquotes'=>$gridval['buyersquotes'],
						'viewcount'=>$gridval['viewcount'],
						'allMessagesList'=>$allMessagesList,
						'buyersleads'=>$gridval['buyersleads'],
						'lead_count'=>$gridval['lead_count'],
						'viewcount'=>$gridval['viewcount']]);

				break;
				case ROAD_PTL       : $gridval = PtlSellerListingComponent::listPTLSellerPostDetailsItems($id);
				$post_id_parent = DB::table('ptl_seller_post_items')->select('seller_post_id')->where('id', $id)->first();
				$post_id_parent->seller_post_id;
				
                $allMessagesList = MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$id);
				return view('ptl.sellers.seller_post_details',['seller_post_id'=>$gridval['id'],
						'seller_post'=>$gridval['seller_post'],
						'seller_post_items'=>$gridval['seller_post_items'],
                        'seller_post_id'=>$post_id_parent->seller_post_id,
						'fromlocations'=>$gridval['fromlocations'],
						'tolocations'=>$gridval['tolocations'],
						'paymenttype'=>$gridval['paymenttype'],
						'buyerquoteid'=>$gridval['buyerquoteid'],
						'buyerpublicquotedetails'=>$gridval['buyerpublicquotedetails'],
						'buyerleadquoteid'=>$gridval['buyerleadquoteid'],
						'buyerleadquotedetails'=>$gridval['buyerleadquotedetails'],
						'subscriptionstdate'=>$gridval['subscriptionstdate'],
						'subscriptionenddate'=>$gridval['subscriptionenddate'],
						'viewcount'=>$gridval['viewcount'],
						'privatebuyers'=>$gridval['privatebuyers'],
						'post_details'=>$gridval['post_details'],
						'kgpercft'=>$gridval['kgpercft'],
						'gridtopnav'=>$gridval['gridtopnav'],
						'allMessagesList'=>$allMessagesList,
                                    ]);
				
				break;
				case RAIL       : $gridval = RailSellerListingComponent::listRailSellerPostDetailsItems($id);
				$post_id_parent = DB::table('rail_seller_post_items')->select('seller_post_id')->where('id', $id)->first();
				$post_id_parent->seller_post_id;
				$allMessagesList = MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$id);
				return view('ptl.sellers.seller_post_details',['seller_post_id'=>$gridval['id'],
						'seller_post'=>$gridval['seller_post'],
						'seller_post_items'=>$gridval['seller_post_items'],
                        'seller_post_id'=>$post_id_parent->seller_post_id,
						'fromlocations'=>$gridval['fromlocations'],
						'tolocations'=>$gridval['tolocations'],
						'paymenttype'=>$gridval['paymenttype'],
						'buyerquoteid'=>$gridval['buyerquoteid'],
						'buyerpublicquotedetails'=>$gridval['buyerpublicquotedetails'],
						'buyerleadquoteid'=>$gridval['buyerleadquoteid'],
						'buyerleadquotedetails'=>$gridval['buyerleadquotedetails'],
						'subscriptionstdate'=>$gridval['subscriptionstdate'],
						'subscriptionenddate'=>$gridval['subscriptionenddate'],
						'viewcount'=>$gridval['viewcount'],
						'privatebuyers'=>$gridval['privatebuyers'],
						'post_details'=>$gridval['post_details'],
						'kgpercft'=>$gridval['kgpercft'],
						'gridtopnav'=>$gridval['gridtopnav'],
						'allMessagesList'=>$allMessagesList,]);
				
				break;
				case AIR_DOMESTIC       : $gridval = AirDomesticSellerListingComponent::listAirdomSellerPostDetailsItems($id);
				$post_id_parent = DB::table('airdom_seller_post_items')->select('seller_post_id')->where('id', $id)->first();
				$post_id_parent->seller_post_id;
				$allMessagesList = MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$id);
				return view('ptl.sellers.seller_post_details',['seller_post_id'=>$gridval['id'],
						'seller_post'=>$gridval['seller_post'],
						'seller_post_items'=>$gridval['seller_post_items'],
                        'seller_post_id'=>$post_id_parent->seller_post_id,
						'fromlocations'=>$gridval['fromlocations'],
						'tolocations'=>$gridval['tolocations'],
						'paymenttype'=>$gridval['paymenttype'],
						'buyerquoteid'=>$gridval['buyerquoteid'],
						'buyerpublicquotedetails'=>$gridval['buyerpublicquotedetails'],
						'buyerleadquoteid'=>$gridval['buyerleadquoteid'],
						'buyerleadquotedetails'=>$gridval['buyerleadquotedetails'],
						'subscriptionstdate'=>$gridval['subscriptionstdate'],
						'subscriptionenddate'=>$gridval['subscriptionenddate'],
						'viewcount'=>$gridval['viewcount'],
						'privatebuyers'=>$gridval['privatebuyers'],
						'post_details'=>$gridval['post_details'],
						'kgpercft'=>$gridval['kgpercft'],
						'gridtopnav'=>$gridval['gridtopnav'],
						'allMessagesList'=>$allMessagesList,]);
				
				break;
				case AIR_INTERNATIONAL       : $gridval = AirInternationalSellerListingComponent::listAirintSellerPostDetailsItems($id);
				$post_id_parent = DB::table('airint_seller_post_items')->select('seller_post_id')->where('id', $id)->first();
				$post_id_parent->seller_post_id;
				$allMessagesList = MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$id);
				return view('ptl.sellers.seller_post_details',['seller_post_id'=>$gridval['id'],
						'seller_post'=>$gridval['seller_post'],
						'seller_post_items'=>$gridval['seller_post_items'],
                                                'seller_post_id'=>$post_id_parent->seller_post_id,
						'fromlocations'=>$gridval['fromlocations'],
						'tolocations'=>$gridval['tolocations'],
						'paymenttype'=>$gridval['paymenttype'],
						'buyerquoteid'=>$gridval['buyerquoteid'],
						'buyerdetails'=>$gridval['buyerdetails'],
						'buyerpublicquotedetails'=>$gridval['buyerpublicquotedetails'],
						'buyerprivatequotedetails'=>$gridval['buyerprivatequotedetails'],
						'subscriptionstdate'=>$gridval['subscriptionstdate'],
						'subscriptionenddate'=>$gridval['subscriptionenddate'],
						'buyersquotes'=>$gridval['buyersquotes'],
						'viewcount'=>$gridval['viewcount'],
						'privatebuyers'=>$gridval['privatebuyers'],
						'post_details'=>$gridval['post_details'],
						'kgpercft'=>$gridval['kgpercft'],
						'gridtopnav'=>$gridval['gridtopnav'],
						'allMessagesList'=>$allMessagesList,]);
				
				break;
				case OCEAN       : $gridval = OcceanSellerListingComponent::listOcceanSellerPostDetailsItems($id);
				$post_id_parent = DB::table('ocean_seller_post_items')->select('seller_post_id')->where('id', $id)->first();
				$post_id_parent->seller_post_id;
				$allMessagesList = MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$id);
				return view('ptl.sellers.seller_post_details',['seller_post_id'=>$gridval['id'],
						'seller_post'=>$gridval['seller_post'],
						'seller_post_items'=>$gridval['seller_post_items'],
                        'seller_post_id'=>$post_id_parent->seller_post_id,
						'fromlocations'=>$gridval['fromlocations'],
						'tolocations'=>$gridval['tolocations'],
						'paymenttype'=>$gridval['paymenttype'],
						'buyerquoteid'=>$gridval['buyerquoteid'],
						'buyerdetails'=>$gridval['buyerdetails'],
						'buyerpublicquotedetails'=>$gridval['buyerpublicquotedetails'],
						'buyerprivatequotedetails'=>$gridval['buyerprivatequotedetails'],
						'subscriptionstdate'=>$gridval['subscriptionstdate'],
						'subscriptionenddate'=>$gridval['subscriptionenddate'],
						'buyersquotes'=>$gridval['buyersquotes'],
						'viewcount'=>$gridval['viewcount'],
						'privatebuyers'=>$gridval['privatebuyers'],
						'post_details'=>$gridval['post_details'],
						'kgpercft'=>$gridval['kgpercft'],
						'gridtopnav'=>$gridval['gridtopnav'],
						'allMessagesList'=>$allMessagesList,]);
				
				break;
				case COURIER       : $gridval = CourierSellerListingComponent::listCourierSellerPostDetailsItems($id);
				$post_id_parent = DB::table('courier_seller_post_items')->select('seller_post_id')->where('id', $id)->first();
				$post_id_parent->seller_post_id;
				$allMessagesList = MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$id);
				return view('ptl.sellers.seller_post_details',['seller_post_id'=>$gridval['id'],
						'seller_post'=>$gridval['seller_post'],
						'seller_post_items'=>$gridval['seller_post_items'],
                        'seller_post_id'=>$post_id_parent->seller_post_id,
						'fromlocations'=>$gridval['fromlocations'],
						'tolocations'=>$gridval['tolocations'],
						'paymenttype'=>$gridval['paymenttype'],
						'buyerquoteid'=>$gridval['buyerquoteid'],
						'buyerpublicquotedetails'=>$gridval['buyerpublicquotedetails'],
						'buyerleadquoteid'=>$gridval['buyerleadquoteid'],
						'buyerleadquotedetails'=>$gridval['buyerleadquotedetails'],
						'subscriptionstdate'=>$gridval['subscriptionstdate'],
						'subscriptionenddate'=>$gridval['subscriptionenddate'],
						'viewcount'=>$gridval['viewcount'],
						'privatebuyers'=>$gridval['privatebuyers'],
						'post_details'=>$gridval['post_details'],
						'seller_post_slab_values'=>$gridval['seller_post_slab_values'],
						'kgpercft'=>$gridval['kgpercft'],
						'gridtopnav'=>$gridval['gridtopnav'],
						'allMessagesList'=>$allMessagesList,]);
				
				break;
				case RELOCATION_DOMESTIC       : $gridval = RelocationSellerComponent::SellerPostDetails($id);
					$enquiries = RelocationSellerComponent::getSellerpostEnquiries($id,1);
					$leads = RelocationSellerComponent::getSellerpostEnquiries($id,2);

					$allMessagesList = MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$id);
					return view('relocation.sellers.seller_post_details',['seller_post_id'=>$id,
						'seller_post'=>$gridval['seller_post'][0],
						'seller_post_items'=>$gridval['seller_post_items'],
						'buyerpublicquotedetails'=>array(),
						'enquiries' => $enquiries,
						'leads' => $leads,
						'lead_count'=>count(SellerMatchingComponent::getSellerLeads(RELOCATION_DOMESTIC,$id)),
						'viewcount'=>1,
						'allMessagesList' =>  $allMessagesList,
						'privatebuyers'=>RelocationSellerComponent::getPrivateBuyers($id,$gridval['seller_post'][0]->lkp_access_id)
						]);


					break;
				case ROAD_INTRACITY : $grid = FtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				case ROAD_TRUCK_HAUL: $grid = FtlSellerListingComponent::listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				default             : $grid = FtlSellerListingComponent::listFTLSellerPostDetails($statusId, $roleId, $serviceId, $id);
				break;
			}
		}
		catch( Exception $e ) {
			return $e->message;
		}
	}
	
	/**
	 * Submitting Seller Initial Quote
	 *	
	 * @param  int  $id
	 * @return Response
	 */
	public function sellerQuoteSubmit(Request $request) {
            try{
                Log::info('Seller submit a quote for buyer:'.Auth::id(),array('c'=>'1'));
                if(Session::get ( 'service_id' ) != ''){
                    $serviceId = Session::get ( 'service_id' );
                }
            
                // Saving the user activity to the log table
            
            	CommonComponent::activityLog ( "FTL_SELLER_DETAIL",
            	FTL_SELLER_DETAIL, 0,
            	HTTP_REFERRER, CURRENT_URL );
            
			
            switch($serviceId){
            	case ROAD_FTL       : FtlQuotesComponent::FTLSellerQuoteSubmit($request);
            	break;
            	case ROAD_PTL       : PtlQuotesComponent::PTLSellerQuoteSubmit($request);
            	break;
            	case ROAD_INTRACITY : FtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
            	break;
            	case RAIL      		: RailQuotesComponent::RailSellerQuoteSubmit($request);
            	break;
            	case AIR_DOMESTIC   : AirDomesticQuotesComponent::AirDomesticSellerQuoteSubmit($request);
            	break;
            	case AIR_INTERNATIONAL   : AirInternationalQuotesComponent::AirInternationalSellerQuoteSubmit($request);
            	break;
            	case OCEAN           : OceanQuotesComponent::oceanSellerQuoteSubmit($request);
            	break;
            	case COURIER         : CourierQuotesComponent::courierSellerQuoteSubmit($request);
            	break;
				case RELOCATION_DOMESTIC         :
					RelocationSellerComponent::DomesticSellerQuoteSubmit($request);
					break;
            	case ROAD_TRUCK_HAUL: FtlSellerListingComponent::listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id);
            	break;
            	default             : FtlSellerListingComponent::listFTLSellerPostDetails($statusId, $roleId, $serviceId, $id);
            	break;
            }
           //return 'sellerposts';
            
		} catch (Exception $e) {
		
		}
		return Redirect::back();
	}
	
	/**
	 * Submitting Seller Accept Firm Quote
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function sellerAcceptance(Request $request) {
		try{
			Log::info('Seller submit a accept Firm for buyer:'.Auth::id(),array('c'=>'1'));
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
	
			// Saving the user activity to the log table
			
				CommonComponent::activityLog ( "FTL_SELLER_DETAIL",
				FTL_SELLER_DETAIL, 0,
				HTTP_REFERRER, CURRENT_URL );
			
				
			switch($serviceId){
				case ROAD_FTL       : FtlQuotesComponent::FTLSellerAcceptQuoteSubmit($request);
				break;
				case ROAD_PTL       : PtlQuotesComponent::PTLSellerQuoteSubmit($request);
				break;
				case ROAD_INTRACITY : FtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				case ROAD_TRUCK_HAUL: FtlSellerListingComponent::listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				default             : FtlSellerListingComponent::listFTLSellerPostDetails($statusId, $roleId, $serviceId, $id);
				break;
			}
			//return 'sellerposts';
	
		} catch (Exception $e) {
	
		}
		return Redirect::back();
	}
	
	
	/**
	 * Submitting Seller Initial Quote for search buyer quotes
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function sellerSearchQuoteSubmit(Request $request) {
		try{
			Log::info('Seller submit a quote for buyer Search:'.Auth::id(),array('c'=>'1'));

			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
			
			// Saving the user activity to the log table
			
				CommonComponent::activityLog ( "FTL_SELLER_DETAIL",
				FTL_SELLER_DETAIL, 0,
				HTTP_REFERRER, CURRENT_URL );
			
			switch($serviceId){
				case ROAD_FTL       : FtlQuotesComponent::FTLsellerSearchQuoteSubmit($request);
				break;
				case ROAD_PTL       : PtlQuotesComponent::PTLsellerSearchQuoteSubmit($request);
				break;
				case ROAD_INTRACITY : FtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				case RAIL           : RailQuotesComponent::RailsellerSearchQuoteSubmit($request);
				break;
				case AIR_DOMESTIC   : AirDomesticQuotesComponent::AirDomesticsellerSearchQuoteSubmit($request);
				break;
				case COURIER        : CourierQuotesComponent::CouriersellerSearchQuoteSubmit($request);
				break;
				case AIR_INTERNATIONAL   : AirInternationalQuotesComponent::AirInternationalsellerSearchQuoteSubmit($request);
				break;
                                case OCEAN          : OceanQuotesComponent::OceansellerSearchQuoteSubmit($request);
				break;
				case ROAD_TRUCK_HAUL: FtlSellerListingComponent::listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				default             : FtlSellerListingComponent::listFTLSellerPostDetails($statusId, $roleId, $serviceId, $id);
				break;
			}
			
		} catch (Exception $e) {
	
		}
	}
	
	
	/**
	 * Submitting Seller Initial/Final Quote
	 *	
	 * @param  int  $id
	 * @return Response
	 */
	public function sellerFinalQuoteSubmit(Request $request) {
		try{
            Log::info('Seller submit Final quote for buyer:'.Auth::id(),array('c'=>'1'));
			if(Session::get ( 'service_id' ) != ''){
            	$serviceId = Session::get ( 'service_id' );
            }
            
            // Saving the user activity to the log table
            
            	CommonComponent::activityLog ( "FTL_SELLER_DETAIL",
            	FTL_SELLER_DETAIL, 0,
            	HTTP_REFERRER, CURRENT_URL );
            
            switch($serviceId){
            	case ROAD_FTL       : FtlQuotesComponent::FTLSellerFinalQuoteSubmit($request);
            	break;
            	case ROAD_PTL       : PtlQuotesComponent::PTLSellerFinalQuoteSubmit($request);
            	break;
            	case ROAD_INTRACITY : FtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
            	break;
            	case RAIL           : RailQuotesComponent::RailSellerFinalQuoteSubmit($request);
            	break;
            	case AIR_DOMESTIC   : AirDomesticQuotesComponent::AirdomesticSellerFinalQuoteSubmit($request);
            	break;
            	case COURIER        : CourierQuotesComponent::CourierSellerFinalQuoteSubmit($request);
            	break;
            	case AIR_INTERNATIONAL   : AirInternationalQuotesComponent::AirInternationalSellerFinalQuoteSubmit($request);
            	break;
            	case OCEAN   		: OceanQuotesComponent::OceanSellerFinalQuoteSubmit($request);
            	break;
            	case ROAD_TRUCK_HAUL: FtlSellerListingComponent::listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id);
            	break;
            	default             : FtlSellerListingComponent::listFTLSellerPostDetails($statusId, $roleId, $serviceId, $id);
            	break;
            }
           
		} catch (Exception $e) {
		
		}
		die;
	}
	
	/***********************************This function for buyer list****************************************/
	public function buyerList() {
		Log::info('get the buyer list in creating a seller post for post public:'.Auth::id(),array('c'=>'1'));
		try {
				$term = Input::get('search');
			    		
    			$display_json = array();
				$json_arr = array();
		
				$buyer_lisr_for_sellers = DB::table('users')
    			->leftjoin ('buyer_business_details', 'users.id', '=', 'buyer_business_details.user_id')
    			->leftjoin ('buyer_details', 'users.id', '=', 'buyer_details.user_id')
		    	->where(['users.is_active' => 1])
		    	->whereRaw("(users.lkp_role_id = ". BUYER ." or users.secondary_role_id = ". BUYER .")")
		    	//->where(['users.lkp_role_id' => BUYER])
		    	//->orWhere(['users.secondary_role_id' => BUYER])
		    	->where('username', 'LIKE', $term.'%')
		    	->orderby('users.id','asc')
		    	->select('users.id','users.username','buyer_business_details.principal_place')
		    	->get();
				$cnt  = count($buyer_lisr_for_sellers);
				
				if($cnt>0){
						for($i=0; $i<$cnt; $i++ ){
								$json_arr["value"] = $buyer_lisr_for_sellers[$i]->id;
								//if (!empty($buyer_lisr_for_seller[$i]->principal_place)){
									$json_arr["text"] = $buyer_lisr_for_sellers[$i]->username." ".$buyer_lisr_for_sellers[$i]->principal_place." ".$buyer_lisr_for_sellers[$i]->id;
								//}else{
									//$json_arr["text"] = $buyer_lisr_for_sellers[$i]->username." ".$buyer_lisr_for_sellers[$i]->id;	
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
	 * Submitting Seller Acceptance Quote
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function sellerQuoteAcceptance($id,$bqid,$spqi=null) {
		Log::info('seller Quote Acceptance for buyer:'.Auth::id(),array('c'=>'1'));
		try{
			
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
			
			// Saving the user activity to the log table
			
				CommonComponent::activityLog ( "FTL_SELLER_DETAIL",
				FTL_SELLER_DETAIL, 0,
				HTTP_REFERRER, CURRENT_URL );
			
			switch($serviceId){
				case ROAD_FTL       : FtlQuotesComponent::FTLsellerQuoteAcceptance($id,$bqid,$spqi);
				break;
				case ROAD_PTL       : PtlQuotesComponent::PTLsellerQuoteAcceptance($id,$bqid,$spqi);
				
				break;
				case ROAD_INTRACITY : FtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				case ROAD_TRUCK_HAUL: FtlSellerListingComponent::listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				default             : FtlSellerListingComponent::listFTLSellerPostDetails($statusId, $roleId, $serviceId, $id);
				break;
			}
		} catch (Exception $e) {
		}
		return Redirect::back();
		
	}
	
	/**
	 * Submitting Seller Acceptance Quote
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function sellerCounterAcceptance(Request $request) {
		Log::info('seller Quote Acceptance for buyer:'.Auth::id(),array('c'=>'1'));
		try{
				
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
				
			// Saving the user activity to the log table
			
                        CommonComponent::activityLog ( "FTL_SELLER_DETAIL",
                        FTL_SELLER_DETAIL, 0,
                        HTTP_REFERRER, CURRENT_URL );
			
			switch($serviceId){
				case ROAD_FTL       : FtlQuotesComponent::FTLsellerCounterAcceptance($request);
				break;
				case ROAD_PTL       : PtlQuotesComponent::PTLsellerCounterAcceptance($request);
				break;
				case ROAD_INTRACITY : FtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				case RAIL       	: RailQuotesComponent::RailsellerCounterAcceptance($request);
				break;
				case AIR_DOMESTIC   : AirDomesticQuotesComponent::AirdomesticsellerCounterAcceptance($request);
				break;
				case COURIER        : CourierQuotesComponent::CouriersellerCounterAcceptance($request);
				break;
				case AIR_INTERNATIONAL   : AirInternationalQuotesComponent::AirInternationalsellerCounterAcceptance($request);
				break;
				case OCEAN   		: OceanQuotesComponent::OceansellerCounterAcceptance($request);
				break;
				case ROAD_TRUCK_HAUL: FtlSellerListingComponent::listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				default             : FtlSellerListingComponent::listFTLSellerPostDetails($statusId, $roleId, $serviceId, $id);
				break;
			}
		} catch (Exception $e) {
		}
		return Redirect::back();
	
	}
	
	
	/**
	 * Submitting Seller Acceptance Public Quote
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function sellerQuotePublicAcceptance($id,$bqid,$spqi=null,$quote=null,$pid=null) {
		Log::info('seller Quote Acceptance for buyer:'.Auth::id(),array('c'=>'1'));
		try{
			
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
			
			// Saving the user activity to the log table
			
                        CommonComponent::activityLog ( "FTL_SELLER_DETAIL",
                        FTL_SELLER_DETAIL, 0,
                        HTTP_REFERRER, CURRENT_URL );
			
			switch($serviceId){
				case ROAD_FTL       : FtlQuotesComponent::FTLSellerQuotePublicAcceptance($id,$bqid,$spqi,$quote,$pid);
				break;
				case ROAD_PTL       : FtlSellerListingComponent::listPTLSellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				case ROAD_INTRACITY : FtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				case ROAD_TRUCK_HAUL: FtlSellerListingComponent::listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				default             : FtlSellerListingComponent::listFTLSellerPostDetails($statusId, $roleId, $serviceId, $id);
				break;
			}
		}
		 catch (Exception $e) {
		
		}
		return redirect('sellerpostdetail/'.$spqi);
		
	}
	
	
	/**
	 * Submitting Seller Acceptance Public Quote
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function sellerCounterAcceptence(Request $request) {
		Log::info('seller Quote Acceptance for buyer:'.Auth::id(),array('c'=>'1'));
		try{
				
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
				
			// Saving the user activity to the log table
			
				CommonComponent::activityLog ( "FTL_SELLER_DETAIL",
				FTL_SELLER_DETAIL, 0,
				HTTP_REFERRER, CURRENT_URL );
			
			switch($serviceId){
				case ROAD_FTL       : FtlQuotesComponent::FTLSellerAcceptanceCounterOffer($request);
				break;
				case ROAD_PTL       : FtlSellerListingComponent::listPTLSellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				case ROAD_INTRACITY : FtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				case ROAD_TRUCK_HAUL: FtlSellerListingComponent::listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				default             : FtlSellerListingComponent::listFTLSellerPostDetails($statusId, $roleId, $serviceId, $id);
				break;
			}
		}
		catch (Exception $e) {
	
		}
		//return redirect('sellerpostdetail/'.$spqi);
	
	}
	
	
	
	
	/**
	 * Submitting Public Search Quote Acceptence for firm prize
	 *
	 * @param  $request
	 * @return Response
	 */
	public function  sellerQuotePublicSearchAcceptance($id,$bqid,$fromcity,$tocity,$quote,$search) {
		try{
			
			
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
				
			// Saving the user activity to the log table
			
				CommonComponent::activityLog ( "FTL_SELLER_DETAIL",
				FTL_SELLER_DETAIL, 0,
				HTTP_REFERRER, CURRENT_URL );
			
			switch($serviceId){
				case ROAD_FTL       : FtlQuotesComponent::FTLsellerQuotePublicSearchAcceptance($id,$bqid,$fromcity,$tocity,$quote,$search);
				break;
				case ROAD_PTL       : FtlSellerListingComponent::listPTLSellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				case ROAD_INTRACITY : FtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				case ROAD_TRUCK_HAUL: FtlSellerListingComponent::listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				default             : FtlSellerListingComponent::listFTLSellerPostDetails($statusId, $roleId, $serviceId, $id);
				break;
			}
		
		}catch (Exception $e) {
		
		}
		return Redirect::back();
	}
	
	/**
	 * Submitting Public Search Quote Acceptence for firm prize
	 *
	 * @param  $request
	 * @return Response
	 */
	public function  sellerSearchAcceptance(Request $request) {
		try{
				
				
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
	
			// Saving the user activity to the log table
			
				CommonComponent::activityLog ( "FTL_SELLER_DETAIL",
				FTL_SELLER_DETAIL, 0,
				HTTP_REFERRER, CURRENT_URL );
			
				
			switch($serviceId){
				case ROAD_FTL       : FtlQuotesComponent::FTLsellerSearchAcceptance($request);
				break;
				case ROAD_PTL       : FtlSellerListingComponent::listPTLSellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				case ROAD_INTRACITY : FtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				case ROAD_TRUCK_HAUL: FtlSellerListingComponent::listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id);
				break;
				default             : FtlSellerListingComponent::listFTLSellerPostDetails($statusId, $roleId, $serviceId, $id);
				break;
			}
	
		}catch (Exception $e) {
	
		}
		return Redirect::back();
	}
	
	
	/**
	 * Seller list initial page
	 *
	 * 
	 * @return Response
	 */
	public function sellerLists() {
		try{
			
			$posts_status = DB::table('lkp_post_statuses')->lists('post_status', 'id');
			$lkp_services_seller = DB::table ( 'seller_services as ss' )
								->join ( 'lkp_services as ls', 'ss.lkp_service_id', '=', 'ls.id' )
								->select ( 'ls.id', 'ls.service_name')
								->lists('service_name', 'id');
			
			$lkp_lead_types = DB::table('lkp_lead_types')->lists('lead_type', 'id');
			$roleId = Auth::User()->lkp_role_id;
			$serviceId = '1';
			//Saving the user activity to the log table
			if($roleId == SELLER){
				CommonComponent::activityLog("SELLER_LISTED_POSTS",
											 SELLER_LISTED_POSTS,0,
											 HTTP_REFERRER,CURRENT_URL);
			}
			CommonComponent::getPageWidgets('SELLER_POSTS_LISTS_PAGE', $roleId, $serviceId);
			$statusId = '';
			$serviceId = '';
			if ( !empty($_POST) ){
				if(isset($_POST['status']) && $_POST['status'] != ''){	
					$statusId = $_POST['status'];
					Session::put('status_search', $_POST['status']);
				}
				if(isset($_POST['service']) && $_POST['service'] != ''){
					$serviceId = $_POST['service'];
					//Session::put('service_id', $_POST['service']);
				}
			}else if(isset($_GET['page'])){
					$statusId = Session::get('status_search');
					$serviceId = Session::get('service_id');		
					
			}else{
				$statusId = '';
				$serviceId = '';
				Session::put('status_search','');
				//Session::put('service_id','');
			}
			$grid = SellerComponent::getSellerList($statusId, $serviceId, $roleId, $serviceId);		
			return view('sellers.seller_list',$grid, [
					'statusSelected' => $statusId,
					'posts_status_list'=>$posts_status, 'services_seller'=>$lkp_services_seller,
					'lead_types_seller'=>$lkp_lead_types]);
			
	
		} catch (Exception $e) {
		
		}
		
	}
        
        /**
	 * Seller list initial page
	 *
	 * 
	 * @return Response
	 */
	public function setcontractstatus($quoteid) {
            try{
                $serviceId = Session::get('service_id');
                DB::table ( 'term_contracts as tc' )
                                    ->where ( 'tc.term_buyer_quote_id', '=', $quoteid )
                                    ->where ( 'tc.lkp_service_id', '=', $serviceId )
                                    ->where ( 'tc.seller_id', '=', Auth::User ()->id )    
                                    ->update (array (
                                        'tc.contract_status'=> CONTRACT_ACCEPTED));


				//*******Send Sms to Seller***********************//
				$quotedata = DB::table('term_buyer_quotes')->where('id','=',$quoteid)->select('buyer_id','transaction_id')->first();
				$contractno = DB::table('term_contracts')->where ( 'term_buyer_quote_id', '=', $quoteid )
									->where ( 'lkp_service_id', '=', $serviceId )
									->where ( 'seller_id', '=', Auth::User ()->id )
									->pluck('contract_no');
				$msg_params = array(
					'buyerpostid' => $quotedata->transaction_id,
					'sellername' => Auth::User()->username,
					'servicename' => CommonComponent::getServiceName($serviceId),
					'contractno' => $contractno,
					'buyername' => DB::table('users')->where('id','=',$quotedata->buyer_id)->pluck('username')

				);
				$getMobileNumber  =   CommonComponent::getMobleNumber($quotedata->buyer_id);
				CommonComponent::sendSMS($getMobileNumber,CONTRACT_ACCEPTANCE_REJECTION,$msg_params);
				//*******Send Sms to Seller***********************//

                //return Redirect::back();
                return redirect('sellerorderSearch?lkp_order_type_id=2')->with ( 'message_accept_contract', 'Contract accepted successfully' );

            } catch (Exception $e) {

            }
		
	}
        /**
	 * Seller list initial page
	 *
	 * 
	 * @return Response
	 */
	public function getcontractdownload($quoteid) {
			if(Auth::User ()->lkp_role_id == SELLER )  {
				$res = DB::table('term_contracts as tc')
				->where('tc.term_buyer_quote_id','=',$quoteid)
				->where('tc.seller_id','=',Auth::User ()->id)
				->select('tc.file_path_one')->first();
			} else {
				$res = DB::table('term_contracts as tc')
				->where('tc.term_buyer_quote_id','=',$quoteid)			
				->select('tc.file_path_one')->first();
			}
            $path   =   $res->file_path_one;
           // echo $path;
            //exit;
            //$filename = array_pop(explode("/",$path));
            try{    
                $file = $path;
                header('Content-type: application/pdf');
                return response()->download($file);

            }catch (Exception $e) {

            }
            
		
	}
	public function downloadTemplate() {
		try{    
			$csvFile = "../public/downloads/pincode.csv";
			$csv = $this->readCSV($csvFile);
			$this->download_csv_results($csv, 'pincode.csv');
            }catch (Exception $e) {

            }
	}
	public function downloadErrorsTemplate() {
		try{
			$csvFile = "../public/uploads/seller/".Auth::User()->id."/pincode/pincode.csv";
			$csv = $this->readCSV($csvFile);
			$this->download_csv_results($csv, 'pincode.csv');
		}catch (Exception $e) {
	
		}
	}
	public function download_csv_results($results, $name = NULL)
	{
		if( ! $name)
		{
			$name = md5(uniqid() . microtime(TRUE) . mt_rand()). '.csv';
		}
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename='. $name);
		header('Pragma: no-cache');
		header("Expires: 0");
		$outstream = fopen("php://output", "w");
	
		foreach($results as $result)
		{
			if(is_array($result)){
				fputcsv($outstream, $result);
			}
		}
	
		fclose($outstream);
	}
	
	public function readCSV($csvFile){
		$file_handle = fopen($csvFile, 'r');
		while (!feof($file_handle) ) {
			$line_of_text[] = fgetcsv($file_handle, 1024);
		}
		fclose($file_handle);
		return $line_of_text;
	}
	
}
