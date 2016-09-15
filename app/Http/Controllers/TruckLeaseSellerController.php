<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Components\CommonComponent;
use App\Components\Matching\SellerMatchingComponent;
use App\Http\Requests;
use App\Models\TruckleaseSellerPost;
use App\Models\TruckleaseSellerPostItem;
use App\Models\TruckleaseSellerSelectedBuyer;
use App\Models\TruckleaseBuyerQuoteSellersQuotesPrices;
use App\Models\TruckleaseSellerPostItemView;
use App\Models\TruckleaseSellerPostItemGood;
use App\Models\TruckleaseSellerPostItemStatePermit;
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
use Carbon\Carbon;

class TruckLeaseSellerController extends Controller
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
     * Ajax load Lease terms based on from date & to date
     * @author Shriram
     */
     public function ajx_leaseterms(Request $request){
        if($request->has('from_date') && $request->has('to_date')):
            
            $from_date = CommonComponent::convertDateForDatabase($request->from_date);
            $to_date = CommonComponent::convertDateForDatabase($request->to_date);

            list($frmYY, $frmMM, $frmDD) = explode('-', $from_date);
            list($toYY, $toMM, $toDD)    = explode('-', $to_date);

            $frmDate = Carbon::createFromDate( $frmYY, $frmMM, $frmDD );
            $toDate = Carbon::createFromDate( $toYY, $toMM, $toDD );

            // Total Difference
            $DaysDiff = $toDate->diffInDays($frmDate); 

            // Required conditions
            if($DaysDiff <= 7){
                $reqSelectArr = ['Daily'];
            }else if($DaysDiff >=8 && $DaysDiff < 30){
                $reqSelectArr = ['Daily', 'Weekly'];
            }else if($DaysDiff >=30 && $DaysDiff < 366){
                $reqSelectArr = ['Daily', 'Weekly', 'Monthly'];
            }else{
                $reqSelectArr = ['Daily', 'Weekly', 'Monthly', 'Yearly'];
            }

            $result =  DB::table('lkp_trucklease_lease_terms')
                        ->where('is_active', 1)
                        ->whereIn('lease_term', $reqSelectArr)
                        ->orderBy('id','ASC')
                        ->lists('lease_term','id');

            // Success response
            return response()->json([ 'success' => true,
                'optHtml' => $result,
                'daysDiff' => $DaysDiff
            ]);

        else:
            // failure
            return response()->json([ 'success' => false, 'msg' => 'Invalid dates' ]);    
        endif;
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
        }else if(Session::get('service_id') == ROAD_TRUCK_HAUL){
                return redirect('truckhaul/createsellerpost');
        }
        Log::info('create seller function used for creating a posts: '.Auth::id(),array('c'=>'1'));
    	try {      	
    	$loadtypemasters = CommonComponent::getAllLoadTypes ();
    	$payment_terms = CommonComponent::getPaymentTerms ();
    	$vehicletypemasters = CommonComponent::getAllVehicleType();
    	$leasetypemasters = CommonComponent::getAllLeaseTypes();
    	$allstates = CommonComponent::getAllStates();
    	
    	$userId = Auth::User()->id;
    	$user_subcsriptions = DB::table('seller_details')->where('user_id', $userId)->first();
    	if ($user_subcsriptions) {
    	$subscription_start_date = date_create($user_subcsriptions->subscription_start_date);
    	$subscription_end_date = date_create($user_subcsriptions->subscription_end_date);
    	$subscription_start_date_start = date_format($subscription_start_date,"Y-m-d");
    	$subscription_end_date_end = date_format($subscription_end_date,"Y-m-d");
    	$current_date_seller = date("Y-m-d");
    	}else{
    		
    		$user_subcsriptions = DB::table('sellers')->where('user_id', $userId)->first();
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
    	
    	
    	if($url_search_search == 'buyersearchresults'){
    		$session_search_values[] = Session::get('session_delivery_date');
    		$session_search_values[] = Session::get('session_dispatch_date');
    		$session_search_values[] = Session::get('session_vehicle_type');
    		$session_search_values[] = Session::get('session_load_type');
    		$session_search_values[] = Session::get('session_from_city_id');
    		$session_search_values[] = Session::get('session_to_city_id');
    		$session_search_values[] = Session::get('session_from_location');
    		$session_search_values[] = Session::get('session_to_location');
    		$session_search_values[] = Session::get('session_seller_district_id');
    		$session_search_values[] = Session::get('session_lease_type');
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
    		$session_search_values[] = Session::put('session_lease_type','');
    	}
        return view('trucklease.sellers.create_seller_quote',['loadtypemasters' => $loadtypemasters,
        		'leasetypemasters' => $leasetypemasters,
        		'allstates'=>$allstates,
        		'vehicletypemasters' => $vehicletypemasters,
        		'subscription_start_date_start' => $subscription_start_date_start,
        		'subscription_end_date_end' => $subscription_end_date_end,
        		'current_date_seller' => $current_date_seller,
        		'serverpreviUrL' => $serverpreviUrL,
        		'url_search_search' => $url_search_search,
        		'session_search_values_create'=> $session_search_values,
        		'PaymentTerms' => $payment_terms]);
        
        } catch (Exception $e) {
        	
        }
    }
    //Line Item
    public function truckLeaseLineItemsCheck() {
    	Log::info('validate the line items,to avoid the dupulicates while creating posts:'.Auth::id(),array('c'=>'1'));
    	try {
    		$from_date=$_POST ['from_date_seller'];
    		$to_date=$_POST ['to_date_seller'];
    		$results = DB::table('trucklease_seller_posts as spc')
    		->join('trucklease_seller_post_items as spic','spic.seller_post_id','=','spc.id')
    		->where('spic.from_location_id', $_POST ['from_location'])
    		->whereRaw ("(`from_date` between  '$from_date' and '$to_date' or `to_date` between '$from_date' and '$to_date')")
    		->where('spic.lkp_vehicle_type_id', $_POST ['vehicle_type'])
    		->where('spic.lkp_post_status_id','<>',5)
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
    
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addSeller(Request $request)
    {
 
    	Log::info('Insert the seller posts data: '.Auth::id(),array('c'=>'1'));
    	try {
    
    		Session::put('session_delivery_date','');
    		Session::put('session_dispatch_date','');
    		Session::put('session_vehicle_type','');
    		Session::put('session_load_type','');
    		Session::put('session_from_city_id','');
    		Session::put('session_from_location','');
    		Session::put('session_seller_district_id','');
    		Session::put('session_lease_type','');
    
    
    		$roleId = Auth::User()->lkp_role_id;
    		if($roleId == SELLER){
    			CommonComponent::activityLog("SELLER_CREATED_POSTS",
    			SELLER_CREATED_POSTS,0,
    			HTTP_REFERRER,CURRENT_URL);
    		}
    		if(!empty(Input::all()))  {
    				
    			if(isset($_POST['optradio'])){
    				$is_private = $_POST['optradio'];
    			}
    			    	
    			$postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
    			$created_year = date('Y');
    			$randnumber = 'TRUCKLEASE/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
    				
    				
    			$multi_data = count($_POST['from_location']);
    			if(isset($_POST['optradio']) && $is_private == 2){
    				if(isset($_POST['buyer_list_for_sellers']) && $_POST['buyer_list_for_sellers'] != ''){
    					$buyer_list = explode(",", $_POST['buyer_list_for_sellers']);
    					array_shift($buyer_list);
    					$buyer_list_count = count($buyer_list);
    				}
    			}
    			$sellerpost  =  new TruckleaseSellerPost();
    			$sellerpost->lkp_service_id = ROAD_TRUCK_LEASE;
    			$sellerpost->from_date = $request->valid_from_val;
    			$sellerpost->to_date = $request->valid_to_val;
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
    					
    				//CommonComponent::auditLog($sellerpost->id,'trucklease_seller_posts');
    				
    				for($i = 0; $i < $multi_data; $i ++) {
    					$sellerpost_lineitem  =  new TruckleaseSellerPostItem();
    					$sellerpost_lineitem->seller_post_id = $sellerpost->id;
    					$sellerpost_lineitem->from_location_id = $_POST['from_location'][$i];
    					$sellerpost_lineitem->lkp_district_id = $_POST['sellerdistrict'][$i];
    					$sellerpost_lineitem->lkp_trucklease_lease_term_id = $_POST['lease_type'][$i];
    					$sellerpost_lineitem->lkp_vehicle_type_id = $_POST['vechile_type'][$i];
    					$sellerpost_lineitem->minimum_lease_period = $_POST['minimum_lease_period'][$i];
    					$sellerpost_lineitem->vehicle_make_model_year = $_POST['vehiclenumber'][$i];
    					$sellerpost_lineitem->price = $_POST['price'][$i];
    					$sellerpost_lineitem->driver_availability = $_POST['check_driver_availablity'][$i];
    					$sellerpost_lineitem->driver_charges = $_POST['driver_cost'][$i];
    					$sellerpost_lineitem->fuel_included = $_POST['fuel_need'][$i];
    					$sellerpost_lineitem->permit_item_id = $_POST['states'][$i];
    					$sellerpost_lineitem->lkp_post_status_id = $lkp_post_status_id;
    					$sellerpost_lineitem->is_cancelled = 0;
    					$created_at = date ( 'Y-m-d H:i:s' );
    					$createdIp = $_SERVER ['REMOTE_ADDR'];
    					$sellerpost_lineitem->created_by = Auth::User()->id;
    					$sellerpost_lineitem->created_at = $created_at;
    					$sellerpost_lineitem->created_ip = $createdIp;
    					$sellerpost_lineitem->save();
    					
    					$load_types = explode(",",$_POST['load_type'][$i]);
    					$load_types_count = count($load_types);
    					
    					for($j = 0; $j < $load_types_count; $j ++) {
    					$created_at = date ( 'Y-m-d H:i:s' );
    					$seller_post_line_item_good  =  new TruckleaseSellerPostItemGood();
    					$seller_post_line_item_good->seller_post_item_id = $sellerpost_lineitem->id;
    					$seller_post_line_item_good->lkp_load_type_id = $load_types[$j];
    					$seller_post_line_item_good->is_active = 1;
    					$seller_post_line_item_good->created_at = $created_at;
    					$seller_post_line_item_good->save();
    					}
    					
    					if($_POST['states']!=0){
	    					$statepermits = explode(",",$_POST['states'][$i]);
	    					$statepermits_count = count($statepermits);
	    						
	    					for($j = 0; $j < $statepermits_count; $j ++) {
	    						$created_at = date ( 'Y-m-d H:i:s' );
	    						$seller_post_line_item_permit  =  new TruckleaseSellerPostItemStatePermit();
	    						$seller_post_line_item_permit->seller_post_item_id = $sellerpost_lineitem->id;
	    						$seller_post_line_item_permit->lkp_state_id = $statepermits[$j];
	    						$seller_post_line_item_permit->is_active = 1;
	    						$seller_post_line_item_permit->created_at = $created_at;
	    						$seller_post_line_item_permit->save();
	    					}
    					}else{
    						for($j = 1; $j<= 36; $j ++) {
    							$created_at = date ( 'Y-m-d H:i:s' );
    							$seller_post_line_item_permit  =  new TruckleaseSellerPostItemStatePermit();
    							$seller_post_line_item_permit->seller_post_item_id = $sellerpost_lineitem->id;
    							$seller_post_line_item_permit->lkp_state_id = $j;
    							$seller_post_line_item_permit->is_active = 1;
    							$seller_post_line_item_permit->created_at = $created_at;
    							$seller_post_line_item_permit->save();
    						}
    					}
    					
    					
    					//*******matching engine***********************//
    					if($lkp_post_status_id == 2){
    						$matchedItems['from_city_id']=$_POST['from_location'][$i];
    						$matchedItems['lkp_vehicle_type_id']=$_POST['vechile_type'][$i];
    						$matchedItems['valid_from']=$_POST['valid_from_val'];
    						$matchedItems['valid_to']=$_POST['valid_to_val'];
							$matchedItems['lkp_trucklease_lease_term_id']=$_POST['lease_type'][$i];
							$matchedItems['minimum_lease_period']=$_POST['minimum_lease_period'][$i];

    						SellerMatchingComponent::doMatching(ROAD_TRUCK_LEASE,$sellerpost_lineitem->id,2,$matchedItems);

    					}
    					//*******matching engine***********************//
    					//CommonComponent::auditLog($sellerpost_lineitem->id,'trucklease_seller_post_items');
    				}
    				if(isset($_POST['optradio']) && $is_private == 2){
    					if(isset($_POST['buyer_list_for_sellers']) && $_POST['buyer_list_for_sellers'] != ''){
    						for($i = 0; $i < $buyer_list_count; $i ++) {
    							$sellerpost_for_buyers  =  new TruckleaseSellerSelectedBuyer();
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
    							CommonComponent::send_email(SELLER_CREATED_POST_FOR_BUYERS,$seller_selected_buyers_email);
    
    
    							if($lkp_post_status_id == 2){
    								//*******Send Sms to the private buyers***********************//
    								$msg_params = array(
    										'randnumber' => $randnumber,
    										'sellername' => Auth::User()->username,
    										'servicename' => 'ROAD_TRUCK_LEASE'
    								);
    								$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
    								CommonComponent::sendSMS($getMobileNumber,SELLER_CREATED_POST_FOR_BUYERS_SMS,$msg_params);
    								//*******Send Sms to the private buyers***********************//
    							}
    							//CommonComponent::auditLog($sellerpost_for_buyers->id,'trucklease_seller_selected_buyers');
    						}
    					}
    				}
					//echo "<pre>";print_R($matchedItems);die;
    					
    				if (Input::get('confirm') == 'Confirm'){
    					return $randnumber;
    				}else{
    
    					return redirect('/sellerlist')->with('message_create_post', 'Post was saved as draft');
    				}
    			}
    		}
    	} catch (Exception $e) {
    		echo 'Caught exception: ',  $e->getMessage(), "\n";
    	}
    
    }
    
}

