<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Components\CommonComponent;
use App\Components\Matching\SellerMatchingComponent;

use App\Models\PtlSellerPost;
use App\Models\PtlSellerPostItem;
use App\Models\PtlZone;
use App\Models\PtlSellerSellectedBuyer;
use App\Http\Requests;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Redirect;
use Response;
use Log;
use App\Components\Ptl\PtlSellerComponent;
use App\Http\Controllers\EditableGrid;
use App\Models\PtlTransitday;
use App\Models\PtlPincodexsector;
use App\Models\PtlTier;
use App\Models\PtlSector;

//RAIL
use App\Models\RailSellerPost;
use App\Models\RailSellerPostItem;
use App\Models\RailSellerSellectedBuyer;

//AriDom

use App\Models\AirdomSellerPost;
use App\Models\AirdomSellerPostItem;
use App\Models\AirdomSellerSellectedBuyer;

//AriInt

use App\Models\AirintSellerPost;
use App\Models\AirintSellerPostItem;
use App\Models\AirintSellerSellectedBuyer;

//Occean

use App\Models\OceanSellerPost;
use App\Models\OceanSellerPostItem;
use App\Models\OceanSellerSellectedBuyer;


//COURIER

use App\Models\CourierSellerPost;
use App\Models\CourierSellerPostItem;
use App\Models\CourierSellerSellectedBuyer;
use App\Models\CourierSellerPostItemSlab;


class PtlSellerController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function __construct() {
		$this->middleware ( 'auth' );
	}
	public function ptlCreateSellerPost() {
		if(Session::get('service_id') == ROAD_FTL){
			return redirect('createseller');
		}else if(Session::get('service_id') == RELOCATION_DOMESTIC || Session::get('service_id') == RELOCATION_PET_MOVE || Session::get('service_id') == RELOCATION_INTERNATIONAL || Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY || Session::get('service_id') == RELOCATION_OFFICE_MOVE){
                return redirect('relocation/createsellerpost');
        }else if(Session::get('service_id') == ROAD_TRUCK_HAUL){
            return redirect('truckhaul/createsellerpost');
        }else if(Session::get('service_id') == ROAD_TRUCK_LEASE){
        	return redirect('trucklease/createsellerpost');
        }
		Log::info ( 'create seller function used for creating a posts: ' . Auth::id (), array (
			'c' => '1'
		) );
		try {
			$payment_methods = CommonComponent::getPaymentTerms ();
			$volumeWeightTypes = CommonComponent::getUnitsWeight ();
			$trackingtypes = CommonComponent::getTrackingTypes();			
			$userId = Auth::User ()->id;
			$user_subcsriptions = DB::table ( 'seller_details' )->where ( 'user_id', $userId )->first ();
			if ($user_subcsriptions) {
				$subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
				$subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
				$subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
				$subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
				$current_date_seller = date ( "Y-m-d" );
			} else {
				$user_subcsriptions = DB::table ( 'sellers' )->where ( 'user_id', $userId )->first ();
				$subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
				$subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
				$subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
				$subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
				$current_date_seller = date ( "Y-m-d" );
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
				$session_search_values[] = Session::get('session_delivery_date_ptl');
				$session_search_values[] = Session::get('session_dispatch_date_ptl');
				$session_search_values[] = Session::get('session_vehicle_type_ptl');
				$session_search_values[] = Session::get('session_load_type_ptl');
				$session_search_values[] = Session::get('session_from_city_id_ptl');
				$session_search_values[] = Session::get('session_to_city_id_ptl');
				$session_search_values[] = Session::get('session_from_location_ptl');
				$session_search_values[] = Session::get('session_to_location_ptl');
				$session_search_values[] = Session::get('zone_or_location_ptl');
			}else{
				$session_search_values[] = Session::put('session_delivery_date_ptl','');
				$session_search_values[] = Session::put('session_dispatch_date_ptl','');
				$session_search_values[] = Session::put('session_vehicle_type_ptl','');
				$session_search_values[] = Session::put('session_load_type_ptl','');
				$session_search_values[] = Session::put('session_from_city_id_ptl','');
				$session_search_values[] = Session::put('session_to_city_id_ptl','');
				$session_search_values[] = Session::put('session_from_location_ptl','');
				$session_search_values[] = Session::put('session_to_location_ptl','');
				$session_search_values[] = Session::put('zone_or_location_ptl','');
			}

			//echo "<pre>";print_r($session_search_values);exit;


			return view ( 'ptl.sellers.seller_creation', [
				'paymentterms' => $payment_methods,
				'subscription_start_date_start' => $subscription_start_date_start,
				'subscription_end_date_end' => $subscription_end_date_end,
				'session_search_values_create'=> $session_search_values,
				'current_date_seller' => $current_date_seller,
				'serverpreviUrL' => $serverpreviUrL,
				'url_search_search' => $url_search_search,
				'trackingtypes'=> $trackingtypes,
				'volumeWeightTypes' =>	$volumeWeightTypes
			] );
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	}
	public function ptlPostCreation(Request $request) {
		
		//echo "<pre>";print_r($_POST);exit;
		Log::info ( 'Insert the seller posts data: ' . Auth::id (), array (
			'c' => '1'
		) );
		try {
			Session::put('session_delivery_date_ptl','');
			Session::put('session_dispatch_date_ptl','');
			Session::put('session_vehicle_type_ptl','');
			Session::put('session_load_type_ptl','');
			Session::put('session_from_city_id_ptl','');
			Session::put('session_to_city_id_ptl','');
			Session::put('session_from_location_ptl','');
			Session::put('session_to_location_ptl','');

			$roleId = Auth::User ()->lkp_role_id;
			if ($roleId == SELLER) {
				CommonComponent::activityLog ( "SELLER_CREATED_POSTS", SELLER_CREATED_POSTS, 0, HTTP_REFERRER, CURRENT_URL );
			}
			if (! empty ( Input::all () )) {				
				if (isset ( $_POST ['optradio'] )) {
					$is_private = $_POST ['optradio'];
				}
				
				$postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
				$created_year = date('Y');
				if(Session::get('service_id') == ROAD_PTL){
					$randnumber = 'LTL/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
				}
				if(Session::get('service_id') == RAIL){
					$randnumber = 'RAIL/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
				}
				if(Session::get('service_id') == AIR_DOMESTIC){
					$randnumber = 'AIRDOMESTIC/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
				}
				if(Session::get('service_id') == AIR_INTERNATIONAL){
					$randnumber = 'AIRINTERNATIONAL/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
				}
				if(Session::get('service_id') == OCEAN){
					$randnumber = 'OCEAN/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
				}
				if(Session::get('service_id') == COURIER){
					$randnumber = 'COURIER/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
				}
				$multi_data = count ( $_POST ['from_location'] );				
				
				if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
					if (isset ( $_POST ['buyer_list_for_sellers'] ) && $_POST ['buyer_list_for_sellers'] != '') {
						$buyer_list = explode ( ",", $_POST ['buyer_list_for_sellers'] );
						array_shift ( $buyer_list );
						$buyer_list_count = count ( $buyer_list );
					}
				}
				if(Session::get('service_id') == ROAD_PTL){
					$sellerpost = new PtlSellerPost();
					$sellerpost->lkp_service_id = ROAD_PTL;
				}
				if(Session::get('service_id') == RAIL){
					$sellerpost = new RailSellerPost();
					$sellerpost->lkp_service_id = RAIL;
				}
				if(Session::get('service_id') == AIR_DOMESTIC){
					$sellerpost = new AirdomSellerPost();
					$sellerpost->lkp_service_id = AIR_DOMESTIC;
				}
				if(Session::get('service_id') == AIR_INTERNATIONAL){
					$sellerpost = new AirintSellerPost();
					$sellerpost->lkp_service_id = AIR_INTERNATIONAL;
				}
				if(Session::get('service_id') == OCEAN){
					$sellerpost = new OceanSellerPost();
					$sellerpost->lkp_service_id = OCEAN;
				}
				if(Session::get('service_id') == COURIER){
					$sellerpost = new CourierSellerPost();
					$sellerpost->lkp_service_id = COURIER;
				}

				$sellerpost->from_date = $_POST ['valid_from_val'];
				$sellerpost->to_date = $_POST ['valid_to_val'];
				$sellerpost->tracking = $request->tracking;
				
				//echo $request->kgpercft;exit;
				if(Session::get('service_id') != COURIER){
				
				if ($request->kgpercft != '') {
					$sellerpost->kg_per_cft = $request->kgpercft;
				} else {
					$sellerpost->kg_per_cft = 0;
				}
				if ($request->pickup != '') {
					$sellerpost->pickup_charges = $request->pickup;
				} else {
					$sellerpost->pickup_charges = 0;
				}
				if ($request->delivery != '') {
					$sellerpost->delivery_charges = $request->delivery;
				} else {
					$sellerpost->delivery_charges = 0;
				}
				if ($request->oda != '') {
					$sellerpost->oda_charges = $request->oda;
				} else {
					$sellerpost->oda_charges = 0;
				}
				}
				if(Session::get('service_id') == COURIER){
						$sellerpost->fuel_surcharge = $request->fuel_surcharge_text;
						$sellerpost->cod_charge = $request->check_on_delivery_text;
						$sellerpost->freight_collect_charge = $request->freight_collect_text;
						$sellerpost->arc_charge = $request->arc_text;
						$sellerpost->maximum_value = $request->maximum_value_text;
						$sellerpost->conversion_factor = $request->conversion_factor_text;
						$sellerpost->max_weight_accepted = $request->max_weight_accepted_text;
						$sellerpost->lkp_ict_weight_uom_id = $request->units_max_weight;
						$sellerpost->lkp_courier_type_id = $request->courier_or_types_id;
						$sellerpost->lkp_courier_delivery_type_id = $request->post_or_delivery_type_id;
						if (isset($_POST ['check_max_weight_assign']) && $_POST ['check_max_weight_assign'] != '') {
							$sellerpost->is_incremental = $request->check_max_weight_assign;
						} else {
							$sellerpost->is_incremental = 0;
						}
						if (isset ( $_POST ['incremental_weight_text'] ) && $_POST ['incremental_weight_text'] != '') {
							$sellerpost->increment_weight = $request->incremental_weight_text;
						} else {
							$sellerpost->increment_weight = '';
						}
						if (isset ( $_POST ['rate_per_increment_text'] ) && $_POST ['rate_per_increment_text'] != '') {
							$sellerpost->rate_per_increment = $request->rate_per_increment_text;
						} else {
							$sellerpost->rate_per_increment = '';
						}
						
				}
				$sellerpost->terms_conditions = $request->terms_conditions;
				$sellerpost->lkp_payment_mode_id = $request->paymentterms;
				$sellerpost->lkp_ptl_post_type_id = $request->post_type_id;
				$sellerpost->credit_period = $request->credit_period_ptl;
				$sellerpost->credit_period_units = $request->credit_period_units;

				if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
					$sellerpost->lkp_access_id = 2;
				} else {
					$sellerpost->lkp_access_id = 1;
				}
				$sellerpost->seller_id = Auth::id ();
				$sellerpost->transaction_id = $randnumber;

				if (Input::get ( 'confirm' ) == 'Confirm') {
					$lkp_post_status_id = 2;
				} else {
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


				if (is_array ( $request->accept_payment_ptl )) {
					$sellerpost->accept_payment_netbanking = in_array ( 1, $request->accept_payment_ptl ) ? 1 : 0;
					$sellerpost->accept_payment_credit = in_array ( 2, $request->accept_payment_ptl ) ? 1 : 0;
					$sellerpost->accept_payment_debit = in_array ( 3, $request->accept_payment_ptl ) ? 1 : 0;
				} else {
					$sellerpost->accept_payment_netbanking = 0;
					$sellerpost->accept_payment_credit = 0;
					$sellerpost->accept_payment_debit = 0;
				}
				
				
				if (is_array ( $request->accept_credit_netbanking )) {
					$sellerpost->accept_credit_netbanking = in_array ( 1, $request->accept_credit_netbanking ) ? 1 : 0;
					$sellerpost->accept_credit_cheque = in_array ( 2, $request->accept_credit_netbanking ) ? 1 : 0;
				} else {
					$sellerpost->accept_credit_netbanking = 0;
					$sellerpost->accept_credit_cheque = 0;
				}
				
				/*
				if (Input::get ( 'accept_credit_netbanking' ) == 1) {
					$sellerpost->accept_credit_netbanking = $request->accept_credit_netbanking;
				} else {
					$sellerpost->accept_credit_netbanking = 0;
				}
				if (Input::get ( 'accept_credit_cheque' ) == 1) {
					$sellerpost->accept_credit_cheque = $request->accept_credit_cheque;
				} else {
					$sellerpost->accept_credit_cheque = 0;
				}
				*/
				
				
				
				$created_at = date ( 'Y-m-d H:i:s' );
				$createdIp = $_SERVER ['REMOTE_ADDR'];
				$sellerpost->created_by = Auth::id ();
				$sellerpost->created_at = $created_at;
				$sellerpost->created_ip = $createdIp;
				$matchedItems = array ();
				if ($sellerpost->save ()) {
					
					// CommonComponent::auditLog($sellerpost->id,'seller_posts');
					for($i = 0; $i < $multi_data; $i ++) {

						$district = CommonComponent::getDistrictid($_POST ['from_location'] [$i]);
						
						if(Session::get('service_id') == ROAD_PTL){
							$sellerpost_lineitem = new PtlSellerPostItem ();
						}
						if(Session::get('service_id') == RAIL){
							$sellerpost_lineitem = new RailSellerPostItem ();
						}
						if(Session::get('service_id') == AIR_DOMESTIC){
							$sellerpost_lineitem = new AirdomSellerPostItem ();
						}
						if(Session::get('service_id') == AIR_INTERNATIONAL){
							$sellerpost_lineitem = new AirintSellerPostItem ();
						}
						if(Session::get('service_id') == OCEAN){
							$sellerpost_lineitem = new OceanSellerPostItem ();
						}
						if(Session::get('service_id') == COURIER){
							$sellerpost_lineitem = new CourierSellerPostItem ();
						}
						$sellerpost_lineitem->seller_post_id = $sellerpost->id;
						$sellerpost_lineitem->from_location_id = $_POST ['from_location'] [$i];
						$sellerpost_lineitem->to_location_id = $_POST ['to_location'] [$i];
						$sellerpost_lineitem->lkp_district_id = $district;
						$sellerpost_lineitem->transitdays = $_POST ['transitdays'] [$i];
						$sellerpost_lineitem->units = $_POST ['units'] [$i];
						if(Session::get('service_id') != COURIER){
						$sellerpost_lineitem->price = $_POST ['price'] [$i];
						}
						$sellerpost_lineitem->lkp_post_status_id = $lkp_post_status_id;
						$sellerpost_lineitem->is_cancelled = 0;
						$created_at = date ( 'Y-m-d H:i:s' );
						$createdIp = $_SERVER ['REMOTE_ADDR'];
						$sellerpost_lineitem->created_by = Auth::id ();
						$sellerpost_lineitem->created_at = $created_at;
						$sellerpost_lineitem->created_ip = $createdIp;
						$sellerpost_lineitem->save ();

						///CommonComponent::auditLog($sellerpost_lineitem->id,'ptl_seller_post_items');
						
						if(Session::get('service_id') == COURIER){
							$courier_max_weight = $request->max_weight_accepted_text;
							$courier_max_weight_units = $request->units_max_weight;
							if($courier_max_weight_units == 2){
								$courier_max_weight = $courier_max_weight * 0.001;
							}else if($courier_max_weight_units == 3){
								$courier_max_weight = $courier_max_weight * 1000;
							}else{
								$courier_max_weight = $courier_max_weight;
							}
							
						}
						//*******matching engine***********************//
						if($lkp_post_status_id == 2){
							$matchedItems['zone_or_location']=$request->post_type_id;
							$matchedItems['from_location_id']=$_POST['from_location'][$i];
							$matchedItems['to_location_id']=$_POST['to_location'][$i];
							$matchedItems['valid_from']=$_POST['valid_from_val'];
							$matchedItems['valid_to']=$_POST['valid_to_val'];
							if(Session::get('service_id') == COURIER){
								$matchedItems['post_or_delivery_type']=$_POST['post_or_delivery_type_id'];
								$matchedItems['courier_max_weight']=$courier_max_weight;
							}
							if($_POST ['units'] [$i]=='Weeks')
								$matchedItems['transit_days']=$_POST['transitdays'][$i]*7;
							else 
								$matchedItems['transit_days']=$_POST['transitdays'][$i];
							SellerMatchingComponent::doMatching(Session::get('service_id'), $sellerpost_lineitem->id, 2, $matchedItems);
						}
						//*******matching engine***********************//
						
						
						
					}
					
					
					if(Session::get('service_id') == COURIER){
					$sellerpost_lineitem_slab = new CourierSellerPostItemSlab ();
					
					$sellerpost_lineitem_slab->slab_min_rate = $_POST['low_price'];
					$sellerpost_lineitem_slab->slab_max_rate = $_POST['high_price'];
					$sellerpost_lineitem_slab->price = $_POST['actual_price'];
					$sellerpost_lineitem_slab->seller_post_id = $sellerpost->id;
					$sellerpost_lineitem_slab->seller_id = Auth::id ();
					$created_at = date ( 'Y-m-d H:i:s' );
					$createdIp = $_SERVER ['REMOTE_ADDR'];
					$sellerpost_lineitem_slab->created_by = Auth::id ();
					$sellerpost_lineitem_slab->created_at = $created_at;
					$sellerpost_lineitem_slab->created_ip = $createdIp;
					$sellerpost_lineitem_slab->save ();
					}
					
					
					$low_price=1;
					$high_price=1;
					$actual_price=1;
					for($i=1;$i<=$request->price_slap_hidden_value;$i++){
					
						if(Session::get('service_id') == COURIER){
							$sellerpost_lineitem_slab = new CourierSellerPostItemSlab ();
						}
					
						if (isset ( $_POST['low_weight_salb_'.$i] ) && $_POST['low_weight_salb_'.$i] != '') {
							$sellerpost_lineitem_slab->slab_min_rate = $_POST['low_weight_salb_'.$i];
							$low_price++;
						}
						if (isset ( $_POST['low_weight_salb_'.$i] ) && $_POST['low_weight_salb_'.$i] == '') {
							$low_price++;
						}
					
						if (isset ( $_POST['high_weight_slab_'.$i] ) && $_POST['high_weight_slab_'.$i] != '') {
							$sellerpost_lineitem_slab->slab_max_rate = $_POST['high_weight_slab_'.$i];
							$high_price++;
						}
						if (isset ( $_POST['high_weight_slab_'.$i] ) && $_POST['high_weight_slab_'.$i] == '') {
							$high_price++;
						}
					
						if (isset ( $_POST['price_slab_'.$i] ) && $_POST['price_slab_'.$i] != '') {
							$sellerpost_lineitem_slab->price = $_POST['price_slab_'.$i];
							$actual_price++;
						}
						if (isset ( $_POST['price_slab_'.$i] ) && $_POST['price_slab_'.$i] == '') {
							$actual_price++;
						}
						$sellerpost_lineitem_slab->seller_post_id = $sellerpost->id;
						$sellerpost_lineitem_slab->seller_id = Auth::id ();
						$created_at = date ( 'Y-m-d H:i:s' );
						$createdIp = $_SERVER ['REMOTE_ADDR'];
						$sellerpost_lineitem_slab->created_by = Auth::id ();
						$sellerpost_lineitem_slab->created_at = $created_at;
						$sellerpost_lineitem_slab->created_ip = $createdIp;
						$sellerpost_lineitem_slab->save ();
							
					}
					
					
					
					if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
						if (isset ( $_POST ['buyer_list_for_sellers'] ) && $_POST ['buyer_list_for_sellers'] != '') {
							for($i = 0; $i < $buyer_list_count; $i ++) {

								if(Session::get('service_id') == ROAD_PTL){
									$sellerpost_for_buyers = new PtlSellerSellectedBuyer ();
								}
								if(Session::get('service_id') == RAIL){
									$sellerpost_for_buyers = new RailSellerSellectedBuyer ();
								}
								if(Session::get('service_id') == AIR_DOMESTIC){
									$sellerpost_for_buyers = new AirdomSellerSellectedBuyer ();
								}
								if(Session::get('service_id') == AIR_INTERNATIONAL){
									$sellerpost_for_buyers = new AirintSellerSellectedBuyer ();
								}
								if(Session::get('service_id') == OCEAN){
									$sellerpost_for_buyers = new OceanSellerSellectedBuyer ();
								}
								if(Session::get('service_id') == COURIER){
									$sellerpost_for_buyers = new CourierSellerSellectedBuyer ();
								}
								$sellerpost_for_buyers->seller_post_id = $sellerpost->id;
								$sellerpost_for_buyers->buyer_id = $buyer_list [$i];
								$created_at = date ( 'Y-m-d H:i:s' );
								$createdIp = $_SERVER ['REMOTE_ADDR'];
								$sellerpost_for_buyers->created_by = Auth::id ();
								$sellerpost_for_buyers->created_at = $created_at;
								$sellerpost_for_buyers->created_ip = $createdIp;
								$sellerpost_for_buyers->save ();
								$seller_selected_buyers_email = DB::table ( 'users' )->where ( 'id', $buyer_list [$i] )->get ();
								$seller_selected_buyers_email [0]->randnumber = $randnumber;
								$seller_selected_buyers_email [0]->sellername = Auth::User ()->username;
								CommonComponent::send_email ( SELLER_CREATED_POST_FOR_BUYERS, $seller_selected_buyers_email );
								// CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
								if($lkp_post_status_id == 2){
								//*******Send Sms to the private buyers***********************//
								
								if(Session::get('service_id') == ROAD_PTL){
									$servicename = 'LTL';
								}
								if(Session::get('service_id') == RAIL){
									$servicename = 'RAIL';
								}
								if(Session::get('service_id') == AIR_DOMESTIC){
									$servicename = 'AIRDOMESTIC';
								}
								if(Session::get('service_id') == AIR_INTERNATIONAL){
									$servicename = 'AIRINTERNATIONAL';
								}
								if(Session::get('service_id') == OCEAN){
									$servicename = 'OCEAN';
								}
								if(Session::get('service_id') == COURIER){
									$servicename = 'COURIER';
								}
								
								$msg_params = array(
										'randnumber' => $randnumber,
										'sellername' => Auth::User()->username,
										'servicename' => $servicename
								);
								$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
								if($getMobileNumber)
								CommonComponent::sendSMS($getMobileNumber,SELLER_CREATED_POST_FOR_BUYERS_SMS,$msg_params);
								//*******Send Sms to the private buyers***********************//
								}
								
							}
						}
					}
					if (Input::get ( 'confirm' ) == 'Confirm') {
						return $randnumber;
					} else {
						return redirect ( '/sellerlist' )->with ( 'message_create_post_ptl', 'Post was saved as draft' );
					}
				}
			}
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	}
	public function ptlUpdateSellerPost(Request $request, $sid, $lineitem = null) {

		if(Session::get('service_id') == ROAD_PTL){
			$post_id_status = DB::table('ptl_seller_posts')->where('id', $sid)->first();
		}
		if(Session::get('service_id') == RAIL){
			$post_id_status = DB::table('rail_seller_posts')->where('id', $sid)->first();
		}
		if(Session::get('service_id') == AIR_DOMESTIC){
			$post_id_status = DB::table('airdom_seller_posts')->where('id', $sid)->first();
		}
		if(Session::get('service_id') == AIR_INTERNATIONAL){
			$post_id_status = DB::table('airint_seller_posts')->where('id', $sid)->first();
		}
		if(Session::get('service_id') == OCEAN){
			$post_id_status = DB::table('ocean_seller_posts')->where('id', $sid)->first();
		}
		if(Session::get('service_id') == COURIER){
			$post_id_status = DB::table('courier_seller_posts')->where('id', $sid)->first();
		}
		$transactionId = $post_id_status->transaction_id;

		Log::info ( 'create seller function used for updating a posts: ' . Auth::id (), array (
			'c' => '1'
		) );


		if(Session::get('service_id') == ROAD_PTL){
			$sellerpost = new PtlSellerPost();

		}
		if(Session::get('service_id') == RAIL){
			$sellerpost = new RailSellerPost();

		}
		if(Session::get('service_id') == AIR_DOMESTIC){
			$sellerpost = new AirdomSellerPost();

		}
		if(Session::get('service_id') == AIR_INTERNATIONAL){
			$sellerpost = new AirintSellerPost();

		}
		if(Session::get('service_id') == OCEAN){
			$sellerpost = new OceanSellerPost();

		}
		if(Session::get('service_id') == COURIER){
			$sellerpost = new CourierSellerPost();

		}


		if (! empty ( Input::all () )) {
			
			if (Input::get ( 'confirm' ) == 'Confirm') {
				$poststatus = 2;
			} else {
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
			
			if (isset ( $_POST ['accept_payment_ptl'] ) && is_array ( $_POST ['accept_payment_ptl'] )) {
				$accept_payment_netbanking = in_array ( 1, $_POST ['accept_payment_ptl'] ) ? 1 : 0;
				$accept_payment_credit = in_array ( 2, $_POST ['accept_payment_ptl'] ) ? 1 : 0;
				$accept_payment_debit = in_array ( 3, $_POST ['accept_payment_ptl'] ) ? 1 : 0;
			} else {
				$accept_payment_netbanking = 0;
				$accept_payment_credit = 0;
				$accept_payment_debit = 0;
			}
			
			if (isset ( $_POST ['accept_credit_netbanking'] ) && is_array ( $_POST ['accept_credit_netbanking'] )) {
				$accept_credit_netbanking = in_array ( 1, $_POST ['accept_credit_netbanking'] ) ? 1 : 0;
				$accept_credit_cheque = in_array ( 2, $_POST ['accept_credit_netbanking'] ) ? 1 : 0;
			} else {
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
			if(Session::get('service_id') == COURIER){
				
				if(isset($_POST ['check_max_weight_assign']) && $request->input ( 'check_max_weight_assign' ) == 1){
					$check_max_weight = 1;
				}else{
					$check_max_weight = 0;
				}
				
				if(DB::table('courier_seller_post_item_slabs')->where('seller_post_id',$sid)->delete()){
				$low_price=1;
				$high_price=1;
				$actual_price=1;
				for($i=1;$i<=$request->price_slap_hidden_value;$i++){
						
					if(Session::get('service_id') == COURIER){
						$sellerpost_lineitem_slab = new CourierSellerPostItemSlab ();
					}
						
					if (isset ( $_POST['low_weight_salb_'.$i] ) && $_POST['low_weight_salb_'.$i] != '') {
						$sellerpost_lineitem_slab->slab_min_rate = $_POST['low_weight_salb_'.$i];
						$low_price++;
					}
					if (isset ( $_POST['low_weight_salb_'.$i] ) && $_POST['low_weight_salb_'.$i] == '') {
						$low_price++;
					}
						
					if (isset ( $_POST['high_weight_slab_'.$i] ) && $_POST['high_weight_slab_'.$i] != '') {
						$sellerpost_lineitem_slab->slab_max_rate = $_POST['high_weight_slab_'.$i];
						$high_price++;
					}
					if (isset ( $_POST['high_weight_slab_'.$i] ) && $_POST['high_weight_slab_'.$i] == '') {
						$high_price++;
					}
						
					if (isset ( $_POST['price_slab_'.$i] ) && $_POST['price_slab_'.$i] != '') {
						$sellerpost_lineitem_slab->price = $_POST['price_slab_'.$i];
						$actual_price++;
					}
					if (isset ( $_POST['price_slab_'.$i] ) && $_POST['price_slab_'.$i] == '') {
						$actual_price++;
					}
					$sellerpost_lineitem_slab->seller_post_id = $sid;
					$sellerpost_lineitem_slab->seller_id = Auth::id ();
					$created_at = date ( 'Y-m-d H:i:s' );
					$createdIp = $_SERVER ['REMOTE_ADDR'];
					$sellerpost_lineitem_slab->created_by = Auth::id ();
					$sellerpost_lineitem_slab->created_at = $created_at;
					$sellerpost_lineitem_slab->created_ip = $createdIp;
					$sellerpost_lineitem_slab->save ();
						
				}
				}
				
				$arr = array (
				'to_date' => $request->input ( 'valid_to_val' ),
				'tracking' => $request->input ( 'tracking' ),
				'terms_conditions' => $request->input ( 'terms_conditions' ),
				'other_charge1_text' => (isset ( $otherCharges ['labeltext'] ['0'] )) ? $otherCharges ['labeltext'] ['0'] : "",
				'other_charge2_text' => (isset ( $otherCharges ['labeltext'] ['1'] )) ? $otherCharges ['labeltext'] ['1'] : "",
				'other_charge3_text' => (isset ( $otherCharges ['labeltext'] ['2'] )) ? $otherCharges ['labeltext'] ['2'] : "",
				'cancellation_charge_price' => (isset ( $_POST ['terms_condtion_types1'] )) ? $_POST ['terms_condtion_types1'] : "",
				'docket_charge_price' => (isset ( $_POST ['terms_condtion_types2'] )) ? $_POST ['terms_condtion_types2'] : "",
				'other_charge1_price' => (isset ( $otherCharges ['terms_condtion_types'] [0] )) ? $otherCharges ['terms_condtion_types'] [0] : "",
				'other_charge2_price' => (isset ( $otherCharges ['terms_condtion_types'] [1] )) ? $otherCharges ['terms_condtion_types'] [1] : "",
				'other_charge3_price' => (isset ( $otherCharges ['terms_condtion_types'] [2] )) ? $otherCharges ['terms_condtion_types'] [2] : "",
				'fuel_surcharge' => $request->input ( 'fuel_surcharge_text' ),
				'cod_charge' => $request->input ( 'check_on_delivery_text' ),
				'freight_collect_charge' => $request->input ( 'freight_collect_text' ),
				'arc_charge' => $request->input ( 'arc_text' ),
				'maximum_value' => $request->input ( 'maximum_value_text' ),
				'conversion_factor' => $request->input ( 'conversion_factor_text' ),
				'max_weight_accepted' => $request->input ( 'max_weight_accepted_text' ),
				'lkp_ict_weight_uom_id' => $request->input ( 'units_max_weight' ),
				'is_incremental' => $check_max_weight,
				'increment_weight' => $request->input ( 'incremental_weight_text' ),
				'rate_per_increment' => $request->input ( 'rate_per_increment_text' ),
				'lkp_post_status_id' => $poststatus,
				'lkp_access_id' => $lkp_access_id
				);
			}
			else{
				$arr = array (
					'to_date' => $request->input ( 'valid_to_val' ),
					'tracking' => $request->input ( 'tracking' ),
					'terms_conditions' => $request->input ( 'terms_conditions' ),
					'kg_per_cft' => $request->input ( 'kgpercft' ),
					'pickup_charges' => $request->input ( 'pickup' ),
					'delivery_charges' => $request->input ( 'delivery' ),
					'oda_charges' => $request->input ( 'oda' ),
					'other_charge1_text' => (isset ( $otherCharges ['labeltext'] ['0'] )) ? $otherCharges ['labeltext'] ['0'] : "",
					'other_charge2_text' => (isset ( $otherCharges ['labeltext'] ['1'] )) ? $otherCharges ['labeltext'] ['1'] : "",
					'other_charge3_text' => (isset ( $otherCharges ['labeltext'] ['2'] )) ? $otherCharges ['labeltext'] ['2'] : "",
					'cancellation_charge_price' => (isset ( $_POST ['terms_condtion_types1'] )) ? $_POST ['terms_condtion_types1'] : "",
					'docket_charge_price' => (isset ( $_POST ['terms_condtion_types2'] )) ? $_POST ['terms_condtion_types2'] : "",
					'other_charge1_price' => (isset ( $otherCharges ['terms_condtion_types'] [0] )) ? $otherCharges ['terms_condtion_types'] [0] : "",
					'other_charge2_price' => (isset ( $otherCharges ['terms_condtion_types'] [1] )) ? $otherCharges ['terms_condtion_types'] [1] : "",
					'other_charge3_price' => (isset ( $otherCharges ['terms_condtion_types'] [2] )) ? $otherCharges ['terms_condtion_types'] [2] : "",
					'lkp_post_status_id' => $poststatus,
					'lkp_access_id' => $lkp_access_id
				);
			}
			if($post_id_status->lkp_post_status_id != 2){
				$arr['lkp_payment_mode_id']=$request->input('paymentterms');
				$arr['accept_payment_netbanking']=$accept_payment_netbanking;
				$arr['accept_payment_credit']= $accept_payment_credit;
				$arr['accept_payment_debit'] = $accept_payment_debit;
				$arr['credit_period'] = $request->input('credit_period_ptl');
				$arr['credit_period_units'] = $request->input('credit_period_units');
				$arr['accept_credit_netbanking'] = $accept_credit_netbanking;
				$arr['accept_credit_cheque'] = $accept_credit_cheque;
			}

			$sellerpost::where ( "id", $sid )->update ($arr);

			$multi_data = count ( $_POST ['from_location'] );
			for($i = 0; $i < $multi_data; $i ++) {
				if(Session::get('service_id') == ROAD_PTL){
					$sellerpost_lineitem = new PtlSellerPostItem ();
				}
				if(Session::get('service_id') == RAIL){
					$sellerpost_lineitem = new  RailSellerPostItem();

				}
				if(Session::get('service_id') == AIR_DOMESTIC){
					$sellerpost_lineitem = new  AirdomSellerPostItem();

				}
				if(Session::get('service_id') == AIR_INTERNATIONAL){
					$sellerpost_lineitem = new  AirintSellerPostItem();

				}
				if(Session::get('service_id') == OCEAN){
					$sellerpost_lineitem = new  OceanSellerPostItem();

				}
				if(Session::get('service_id') == COURIER){
					$sellerpost_lineitem = new  CourierSellerPostItem();

				}

				$sellerpost_lineitem::where ( "id", $_POST ['post_id'] [$i] )->update ( array (
					'transitdays' => $_POST ['transitdays'] [$i],
					'units' => $_POST ['units'] [$i],
					'price' => $_POST ['price'] [$i],
					'lkp_post_status_id' => $poststatus
				) );
				
				//*******matching engine***********************//
				if($poststatus == 2){
					$matchedItems['zone_or_location']=$_POST['post_type_id'];
					$matchedItems['from_location_id']=$_POST['from_location'][$i];
					$matchedItems['to_location_id']=$_POST['to_location'][$i];
					$matchedItems['valid_from']=$_POST['valid_from_val'];
					$matchedItems['valid_to']=$_POST['valid_to_val'];
					if(Session::get('service_id') == COURIER){
						$matchedItems['post_or_delivery_type']=$_POST['post_or_delivery_type_id'];
					}
					if($_POST ['units'] [$i]=='Weeks')
						$matchedItems['transit_days']=$_POST['transitdays'][$i]*7;
					else 
						$matchedItems['transit_days']=$_POST['transitdays'][$i];
					SellerMatchingComponent::doMatching(Session::get('service_id'), $_POST ['post_id'] [$i], 2, $matchedItems);
				}
				//*******matching engine***********************//

			}

			if(isset($_POST['optradio']) && $is_private == 2){
				if(isset($_POST['buyer_list_for_sellers_hidden']) && $_POST['buyer_list_for_sellers_hidden'] != ''){
						if(Session::get('service_id') == ROAD_PTL){
							$post_list_of_buyers = DB::table('ptl_seller_sellected_buyers')->where('seller_post_id', $sid)->lists('buyer_id');
							DB::table('ptl_seller_sellected_buyers')->where('seller_post_id', $sid)->delete();
						}
						if(Session::get('service_id') == RAIL){
							$post_list_of_buyers = DB::table('rail_seller_sellected_buyers')->where('seller_post_id', $sid)->lists('buyer_id');
							DB::table('rail_seller_sellected_buyers')->where('seller_post_id', $sid)->delete();
						}
						if(Session::get('service_id') == AIR_DOMESTIC){
							$post_list_of_buyers = DB::table('airdom_seller_sellected_buyers')->where('seller_post_id', $sid)->lists('buyer_id');
							DB::table('airdom_seller_sellected_buyers')->where('seller_post_id', $sid)->delete();
						}
						if(Session::get('service_id') == AIR_INTERNATIONAL){
							$post_list_of_buyers = DB::table('airint_seller_sellected_buyers')->where('seller_post_id', $sid)->lists('buyer_id');
							DB::table('airint_seller_sellected_buyers')->where('seller_post_id', $sid)->delete();
						}
						if(Session::get('service_id') == OCEAN){
							$post_list_of_buyers = DB::table('ocean_seller_sellected_buyers')->where('seller_post_id', $sid)->lists('buyer_id');
							DB::table('ocean_seller_sellected_buyers')->where('seller_post_id', $sid)->delete();
						}
						if(Session::get('service_id') == COURIER){
							$post_list_of_buyers = DB::table('courier_seller_sellected_buyers')->where('seller_post_id', $sid)->lists('buyer_id');
							DB::table('courier_seller_sellected_buyers')->where('seller_post_id', $sid)->delete();
						}
					for($i = 0; $i < $buyer_list_count; $i ++) {
						if(Session::get('service_id') == ROAD_PTL){
							$sellerpost_for_buyers  =  new PtlSellerSellectedBuyer();
						}
						if(Session::get('service_id') == RAIL){
							$sellerpost_for_buyers = new RailSellerSellectedBuyer ();
						}
						if(Session::get('service_id') == AIR_DOMESTIC){
							$sellerpost_for_buyers = new AirdomSellerSellectedBuyer ();
						}
						if(Session::get('service_id') == AIR_INTERNATIONAL){
							$sellerpost_for_buyers = new AirintSellerSellectedBuyer ();
						}
						if(Session::get('service_id') == OCEAN){
							$sellerpost_for_buyers = new OceanSellerSellectedBuyer ();
						}
						if(Session::get('service_id') == COURIER){
							$sellerpost_for_buyers = new CourierSellerSellectedBuyer ();
						}

						$sellerpost_for_buyers->seller_post_id = $sid;
						$sellerpost_for_buyers->buyer_id = $buyer_list[$i];
						$created_at = date ( 'Y-m-d H:i:s' );
						$createdIp = $_SERVER ['REMOTE_ADDR'];
						$sellerpost_for_buyers->created_by = Auth::id();
						$sellerpost_for_buyers->created_at = $created_at;
						$sellerpost_for_buyers->created_ip = $createdIp;
						$sellerpost_for_buyers->save();
						if (!in_array($buyer_list[$i], $post_list_of_buyers)){
							$seller_selected_buyers_email = DB::table('users')->where('id', $buyer_list[$i])->get();
							$seller_selected_buyers_email[0]->randnumber = $randnumber;
							$seller_selected_buyers_email[0]->sellername = Auth::User()->username;
							CommonComponent::send_email(SELLER_CREATED_POST_FOR_BUYERS,$seller_selected_buyers_email);
						}
						
						if($poststatus == 2){
							//*******Send Sms to the private buyers***********************//
						
							if(Session::get('service_id') == ROAD_PTL){
								$servicename = 'LTL';
							}
							if(Session::get('service_id') == RAIL){
								$servicename = 'RAIL';
							}
							if(Session::get('service_id') == AIR_DOMESTIC){
								$servicename = 'AIRDOMESTIC';
							}
							if(Session::get('service_id') == AIR_INTERNATIONAL){
								$servicename = 'AIRINTERNATIONAL';
							}
							if(Session::get('service_id') == OCEAN){
								$servicename = 'OCEAN';
							}
							if(Session::get('service_id') == COURIER){
								$servicename = 'COURIER';
							}
						
							$msg_params = array(
									'randnumber' => $randnumber,
									'sellername' => Auth::User()->username,
									'servicename' => $servicename
							);
							$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
							if($getMobileNumber)
								CommonComponent::sendSMS($getMobileNumber,SELLER_UPDATED_POST_FOR_BUYERS_SMS,$msg_params);
							//*******Send Sms to the private buyers***********************//
						}
					}
				}
			}
			
			if($poststatus == 2)
				return redirect ( "/ptl/updatesellerpost/$sid" )->with ( 'transId_updated', $transactionId );
			else
				return redirect ( "/sellerlist" )->with ( 'message_update_post', 'Post Updated Successfully' );
		}

		try {
			// Retrieval of payment methods
			$payment_methods = CommonComponent::getPaymentTerms ();
			$volumeWeightTypes = CommonComponent::getUnitsWeight ();
               $trackingtypes = CommonComponent::getTrackingTypes();
               
			if(Session::get('service_id') == ROAD_PTL){
				$seller_post_edit_action = DB::table ( 'ptl_seller_posts' )->where ( 'ptl_seller_posts.id', $sid )->select ( 'ptl_seller_posts.*' )->first ();

				if ($seller_post_edit_action->lkp_ptl_post_type_id == 1) {
					$seller_post_edit_action_lines = DB::table ( 'ptl_seller_post_items' )->leftjoin ( 'ptl_zones as c1', 'ptl_seller_post_items.from_location_id', '=', 'c1.id' )->leftjoin ( 'ptl_zones as c2', 'ptl_seller_post_items.to_location_id', '=', 'c2.id' )->where ( 'ptl_seller_post_items.seller_post_id', $sid )->select ( 'ptl_seller_post_items.*', 'c1.zone_name as from_locationcity', 'c2.zone_name as to_locationcity' )->get ();
				} else {
					$seller_post_edit_action_lines = DB::table ( 'ptl_seller_post_items' )->leftjoin ( 'lkp_ptl_pincodes as c1', 'ptl_seller_post_items.from_location_id', '=', 'c1.id' )->leftjoin ( 'lkp_ptl_pincodes as c2', 'ptl_seller_post_items.to_location_id', '=', 'c2.id' )->where ( 'ptl_seller_post_items.seller_post_id', $sid )->select ( 'ptl_seller_post_items.*', 'c1.pincode as from_locationcity', 'c2.pincode as to_locationcity' )->get ();
				}

				$selectedbuyers = DB::table ( 'ptl_seller_sellected_buyers' )->leftjoin ( 'users as u', 'ptl_seller_sellected_buyers.buyer_id', '=', 'u.id' )->leftjoin ( 'buyer_business_details as bbds', 'ptl_seller_sellected_buyers.buyer_id', '=', 'bbds.user_id' )->where ( 'ptl_seller_sellected_buyers.seller_post_id', $sid )->select ( 'ptl_seller_sellected_buyers.buyer_id', 'u.username', 'bbds.principal_place' )->get ();
			}
			if(Session::get('service_id') == RAIL){
				$seller_post_edit_action = DB::table ( 'rail_seller_posts' )->where ( 'rail_seller_posts.id', $sid )->select ( 'rail_seller_posts.*' )->first ();

				if ($seller_post_edit_action->lkp_ptl_post_type_id == 1) {
					$seller_post_edit_action_lines = DB::table ( 'rail_seller_post_items' )->leftjoin ( 'ptl_zones as c1', 'rail_seller_post_items.from_location_id', '=', 'c1.id' )->leftjoin ( 'ptl_zones as c2', 'rail_seller_post_items.to_location_id', '=', 'c2.id' )->where ( 'rail_seller_post_items.seller_post_id', $sid )->select ( 'rail_seller_post_items.*', 'c1.zone_name as from_locationcity', 'c2.zone_name as to_locationcity' )->get ();
				} else {
					$seller_post_edit_action_lines = DB::table ( 'rail_seller_post_items' )->leftjoin ( 'lkp_ptl_pincodes as c1', 'rail_seller_post_items.from_location_id', '=', 'c1.id' )->leftjoin ( 'lkp_ptl_pincodes as c2', 'rail_seller_post_items.to_location_id', '=', 'c2.id' )->where ( 'rail_seller_post_items.seller_post_id', $sid )->select ( 'rail_seller_post_items.*', 'c1.pincode as from_locationcity', 'c2.pincode as to_locationcity' )->get ();
				}

				$selectedbuyers = DB::table ( 'rail_seller_sellected_buyers' )->leftjoin ( 'users as u', 'rail_seller_sellected_buyers.buyer_id', '=', 'u.id' )->leftjoin ( 'buyer_business_details as bbds', 'rail_seller_sellected_buyers.buyer_id', '=', 'bbds.user_id' )->where ( 'rail_seller_sellected_buyers.seller_post_id', $sid )->select ( 'rail_seller_sellected_buyers.buyer_id', 'u.username', 'bbds.principal_place' )->get ();
			}
			if(Session::get('service_id') == AIR_DOMESTIC){
				$seller_post_edit_action = DB::table ( 'airdom_seller_posts' )->where ( 'airdom_seller_posts.id', $sid )->select ( 'airdom_seller_posts.*' )->first ();

				if ($seller_post_edit_action->lkp_ptl_post_type_id == 1) {
					$seller_post_edit_action_lines = DB::table ( 'airdom_seller_post_items' )->leftjoin ( 'ptl_zones as c1', 'airdom_seller_post_items.from_location_id', '=', 'c1.id' )->leftjoin ( 'ptl_zones as c2', 'airdom_seller_post_items.to_location_id', '=', 'c2.id' )->where ( 'airdom_seller_post_items.seller_post_id', $sid )->select ( 'airdom_seller_post_items.*', 'c1.zone_name as from_locationcity', 'c2.zone_name as to_locationcity' )->get ();
				} else {
					$seller_post_edit_action_lines = DB::table ( 'airdom_seller_post_items' )->leftjoin ( 'lkp_ptl_pincodes as c1', 'airdom_seller_post_items.from_location_id', '=', 'c1.id' )->leftjoin ( 'lkp_ptl_pincodes as c2', 'airdom_seller_post_items.to_location_id', '=', 'c2.id' )->where ( 'airdom_seller_post_items.seller_post_id', $sid )->select ( 'airdom_seller_post_items.*', 'c1.pincode as from_locationcity', 'c2.pincode as to_locationcity' )->get ();
				}

				$selectedbuyers = DB::table ( 'airdom_seller_sellected_buyers' )->leftjoin ( 'users as u', 'airdom_seller_sellected_buyers.buyer_id', '=', 'u.id' )->leftjoin ( 'buyer_business_details as bbds', 'airdom_seller_sellected_buyers.buyer_id', '=', 'bbds.user_id' )->where ( 'airdom_seller_sellected_buyers.seller_post_id', $sid )->select ( 'airdom_seller_sellected_buyers.buyer_id', 'u.username', 'bbds.principal_place' )->get ();
			}
			if(Session::get('service_id') == AIR_INTERNATIONAL){
				$seller_post_edit_action = DB::table ( 'airint_seller_posts' )->where ( 'airint_seller_posts.id', $sid )->select ( 'airint_seller_posts.*' )->first ();
				$seller_post_edit_action_lines = DB::table ( 'airint_seller_post_items' )->leftjoin ( 'lkp_airports as c1', 'airint_seller_post_items.from_location_id', '=', 'c1.id' )->leftjoin ( 'lkp_airports as c2', 'airint_seller_post_items.to_location_id', '=', 'c2.id' )->where ( 'airint_seller_post_items.seller_post_id', $sid )->select ( 'airint_seller_post_items.*', 'c1.airport_name as from_locationcity', 'c2.airport_name as to_locationcity' )->get ();
				$selectedbuyers = DB::table ( 'airint_seller_sellected_buyers' )->leftjoin ( 'users as u', 'airint_seller_sellected_buyers.buyer_id', '=', 'u.id' )->leftjoin ( 'buyer_business_details as bbds', 'airint_seller_sellected_buyers.buyer_id', '=', 'bbds.user_id' )->where ( 'airint_seller_sellected_buyers.seller_post_id', $sid )->select ( 'airint_seller_sellected_buyers.buyer_id', 'u.username', 'bbds.principal_place' )->get ();
			}
			if(Session::get('service_id') == OCEAN){

				$seller_post_edit_action = DB::table ( 'ocean_seller_posts' )->where ( 'ocean_seller_posts.id', $sid )->select ( 'ocean_seller_posts.*' )->first ();
				$seller_post_edit_action_lines = DB::table ( 'ocean_seller_post_items' )->leftjoin ( 'lkp_seaports as c1', 'c1.id', '=', 'ocean_seller_post_items.from_location_id' )->leftjoin ( 'lkp_seaports as c2', 'c2.id', '=', 'ocean_seller_post_items.to_location_id' )->where ( 'ocean_seller_post_items.seller_post_id', $sid )->select ( 'ocean_seller_post_items.*', 'c1.seaport_name as from_locationcity', 'c2.seaport_name as to_locationcity' )->get ();
				$selectedbuyers = DB::table ( 'ocean_seller_sellected_buyers' )->leftjoin ( 'users as u', 'ocean_seller_sellected_buyers.buyer_id', '=', 'u.id' )->leftjoin ( 'buyer_business_details as bbds', 'ocean_seller_sellected_buyers.buyer_id', '=', 'bbds.user_id' )->where ( 'ocean_seller_sellected_buyers.seller_post_id', $sid )->select ( 'ocean_seller_sellected_buyers.buyer_id', 'u.username', 'bbds.principal_place' )->get ();
			}
			if(Session::get('service_id') == COURIER){
				$seller_post_edit_action = DB::table ( 'courier_seller_posts' )->where ( 'courier_seller_posts.id', $sid )->select ( 'courier_seller_posts.*' )->first ();
				
				if ($seller_post_edit_action->lkp_ptl_post_type_id == 1 && $seller_post_edit_action->lkp_courier_delivery_type_id == 1) {
					$seller_post_edit_action_lines = DB::table ( 'courier_seller_post_items' )->leftjoin ( 'ptl_zones as c1', 'courier_seller_post_items.from_location_id', '=', 'c1.id' )->leftjoin ( 'ptl_zones as c2', 'courier_seller_post_items.to_location_id', '=', 'c2.id' )->where ( 'courier_seller_post_items.seller_post_id', $sid )->select ( 'courier_seller_post_items.*', 'c1.zone_name as from_locationcity', 'c2.zone_name as to_locationcity' )->get ();
				} else if ($seller_post_edit_action->lkp_ptl_post_type_id == 2 && $seller_post_edit_action->lkp_courier_delivery_type_id == 1) {
					$seller_post_edit_action_lines = DB::table ( 'courier_seller_post_items' )->leftjoin ( 'lkp_ptl_pincodes as c1', 'courier_seller_post_items.from_location_id', '=', 'c1.id' )->leftjoin ( 'lkp_ptl_pincodes as c2', 'courier_seller_post_items.to_location_id', '=', 'c2.id' )->where ( 'courier_seller_post_items.seller_post_id', $sid )->select ( 'courier_seller_post_items.*', 'c1.pincode as from_locationcity', 'c2.pincode as to_locationcity' )->get ();
				}else if ($seller_post_edit_action->lkp_ptl_post_type_id == 2 && $seller_post_edit_action->lkp_courier_delivery_type_id == 2) {
					$seller_post_edit_action_lines = DB::table ( 'courier_seller_post_items' )->leftjoin ( 'lkp_ptl_pincodes as c1', 'courier_seller_post_items.from_location_id', '=', 'c1.id' )->leftjoin ( 'lkp_ptl_pincodes as c2', 'courier_seller_post_items.to_location_id', '=', 'c2.id' )->where ( 'courier_seller_post_items.seller_post_id', $sid )->select ( 'courier_seller_post_items.*', 'c1.pincode as from_locationcity', 'c2.pincode as to_locationcity' )->get ();
					$country_for_edit = DB::table('lkp_countries')->where('id',$seller_post_edit_action_lines['0']->to_location_id)->select('country_name')->first();
					$country_for_edit->country_name;
					$seller_post_edit_action_lines[0]->to_locationcity = $country_for_edit->country_name;
				}else{
					$seller_post_edit_action_lines = DB::table ( 'courier_seller_post_items' )->leftjoin ( 'ptl_zones as c1', 'courier_seller_post_items.from_location_id', '=', 'c1.id' )->leftjoin ( 'ptl_zones as c2', 'courier_seller_post_items.to_location_id', '=', 'c2.id' )->where ( 'courier_seller_post_items.seller_post_id', $sid )->select ( 'courier_seller_post_items.*', 'c1.zone_name as from_locationcity', 'c2.zone_name as to_locationcity' )->get ();
					$country_for_edit = DB::table('lkp_countries')->where('id',$seller_post_edit_action_lines['0']->to_location_id)->select('country_name')->first();
					$country_for_edit->country_name;
					$seller_post_edit_action_lines[0]->to_locationcity = $country_for_edit->country_name;
				}

				$selectedbuyers = DB::table ( 'courier_seller_sellected_buyers' )->leftjoin ( 'users as u', 'courier_seller_sellected_buyers.buyer_id', '=', 'u.id' )->leftjoin ( 'buyer_business_details as bbds', 'courier_seller_sellected_buyers.buyer_id', '=', 'bbds.user_id' )->where ( 'courier_seller_sellected_buyers.seller_post_id', $sid )->select ( 'courier_seller_sellected_buyers.buyer_id', 'u.username', 'bbds.principal_place' )->get ();
				$pricelabs = DB::table ( 'courier_seller_post_item_slabs' )
							->where ( 'seller_post_id', $sid )
							->where ( 'seller_id', Auth::User ()->id )
							->get ();
				$pricelabs_count = count($pricelabs);
			}
			
			$userId = Auth::User ()->id;
			$user_subcsriptions = DB::table ( 'seller_details' )->where ( 'user_id', $userId )->first ();

			if ($user_subcsriptions) {
				$subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
				$subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
				$subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
				$subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
				$current_date_seller = date ( "Y-m-d" );
			} else {

				$user_subcsriptions = DB::table ( 'sellers' )->where ( 'user_id', $userId )->first ();
				$subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
				$subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
				$subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
				$subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
				$current_date_seller = date ( "Y-m-d" );
			}

			if (isset ( $seller_post_edit_action->lkp_access_id ) && $seller_post_edit_action->lkp_access_id == 1) {
				$private_seller = false;
				$public_seller = true;
			} else {
				$private_seller = true;
				$public_seller = false;
			}

			if(Session::get('service_id') == COURIER){
			return view ( 'ptl.sellers.updatepost', [
				'seller_post_edit' => $seller_post_edit_action,
				'seller_post_edit_action_lines' => $seller_post_edit_action_lines,
				'private' => $private_seller,
				'public' => $public_seller,
				'paymentterms' => $payment_methods,
				'seller_postid' => $sid,
				'selectedbuyers' => $selectedbuyers,
				'subscription_start_date_start' => $subscription_start_date_start,
				'subscription_end_date_end' => $subscription_end_date_end,
				'current_date_seller' => $current_date_seller,
				'volumeWeightTypes' =>	$volumeWeightTypes,
				'pricelabs' =>	$pricelabs,
				'pricelabs_count' => $pricelabs_count,
                    'trackingtypes'=> $trackingtypes
			] );
			}else{
			return view ( 'ptl.sellers.updatepost', [
					'seller_post_edit' => $seller_post_edit_action,
					'seller_post_edit_action_lines' => $seller_post_edit_action_lines,
					'private' => $private_seller,
					'public' => $public_seller,
					'paymentterms' => $payment_methods,
					'seller_postid' => $sid,
					'selectedbuyers' => $selectedbuyers,
					'subscription_start_date_start' => $subscription_start_date_start,
					'subscription_end_date_end' => $subscription_end_date_end,
					'current_date_seller' => $current_date_seller,
					'volumeWeightTypes' =>	$volumeWeightTypes,
                         'trackingtypes'=> $trackingtypes
					] );
			}
			
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	}
	public function ptlTransitAutofill() {
		$fromlocation_loc = Input::get ( 'fromlocation' );
		$tolocation_loc = Input::get ( 'to_location' );
		if(!empty($fromlocation_loc)){
			$fromtier = DB::table ( 'ptl_pincodexsectors' )
				->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
				->where ( 'ptl_pincodexsectors.ptl_pincode_id', $fromlocation_loc )
				->where ( 'ptl_pincodexsectors.lkp_service_id', Session::get('service_id') )
				->where ( 'ptl_pincodexsectors.seller_id', Auth::User ()->id )
				->pluck ('s1.ptl_tier_id');
		}
		if(!empty($tolocation_loc)){
			$totier = DB::table ( 'ptl_pincodexsectors' )
				->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
				->where ( 'ptl_pincodexsectors.ptl_pincode_id', $tolocation_loc )
				->where ( 'ptl_pincodexsectors.lkp_service_id', Session::get('service_id') )
				->where ( 'ptl_pincodexsectors.seller_id', Auth::User ()->id )
				->pluck ('s1.ptl_tier_id');
		}
		if(isset($fromtier) && isset($totier)){
			$noOfDays = DB::table ( 'ptl_transitdays' )
				->where ( 'from_tier_id', $fromtier )
				->where ( 'to_tier_id', $totier )
				->pluck ('no_days');
			return $noOfDays;
		}

		return 0;
	}
	public function ptlZoneAutocomplete() {
		try {
			$term = Input::get ( 'term' );
			$fromlocation_loc = Input::get ( 'ptlFromLocation' );
			$zone_location_id_value = Input::get ( 'zone_location_id_value' );
			$results = array ();
			if ($zone_location_id_value == 1) {
				if (! empty ( $fromlocation_loc )) {
					$zone_location_ids = DB::table ( 'ptl_pincodexsectors' )
						->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
						->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
						->where ( 'z2.seller_id', Auth::User ()->id )
						->where ( 'z2.lkp_service_id', Session::get('service_id') )
						->where ( 'z2.zone_name', 'LIKE', $term . '%' )->orderBy ( 'z2.zone_name', 'asc' )
						->select ( 'z2.id', 'z2.zone_name' )->groupBy ( 'z2.id' )->get ();
				} else {
					$zone_location_ids = DB::table ( 'ptl_pincodexsectors' )
						->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
						->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
						->where ( 'z2.seller_id', Auth::User ()->id )
						->where ( 'z2.lkp_service_id', Session::get('service_id') )
						->where ( 'z2.zone_name', 'LIKE', $term . '%' )->orderBy ( 'z2.zone_name', 'asc' )
						->select ( 'z2.id', 'z2.zone_name' )->groupBy ( 'z2.id' )->get ();
				}
				foreach ( $zone_location_ids as $zone_location_id ) {
					$results [] = [
						'id' => $zone_location_id->id,
						'value' => $zone_location_id->zone_name
					];
				}
				return Response::json ( $results );
			} else {
				if (isset ( $fromlocation_loc ) && $fromlocation_loc != "") {
					$queries = DB::table ( 'ptl_pincodexsectors' )
						->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
						->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
						->join ( 'lkp_ptl_pincodes as lpp', 'ptl_pincodexsectors.ptl_pincode_id', '=', 'lpp.id' )
						->where ( 'z2.seller_id', Auth::User ()->id )
						->where ( 'z2.lkp_service_id', Session::get('service_id') )
						->where ( 'lpp.id', '<>', $fromlocation_loc )
						->where ( 'lpp.pincode', 'LIKE', $term . '%' )->orderBy ( 'postoffice_name', 'asc' )
						->select ( '*' )->groupBy ( 'lpp.id' )->get ();

				} else {
					$queries = DB::table ( 'ptl_pincodexsectors' )
						->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
						->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
						->join ( 'lkp_ptl_pincodes as lpp', 'ptl_pincodexsectors.ptl_pincode_id', '=', 'lpp.id' )
						->where ( 'z2.seller_id', Auth::User ()->id )
						->where ( 'z2.lkp_service_id', Session::get('service_id') )
						->where ( 'lpp.pincode', 'LIKE', $term . '%' )->orderBy ( 'postoffice_name', 'asc' )
						->select ( '*' )->groupBy ( 'lpp.id' )->get ();

				}
				foreach ( $queries as $query ) {
					$results [] = [
						'id' => $query->id,
						//'value' => $query->zone_name
						'value' => $query->pincode."-".$query->postoffice_name.",".$query->districtname.",".$query->statename
					];
				}
				return Response::json ( $results );
			}
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	}
	
	public function ptlZoneAutocompleteCourier() {
		try {
			$term = Input::get ( 'term' );
			$fromlocation_loc = Input::get ( 'ptlFromLocation' );
			$from_loc = Input::get ( 'from' );
			$zone_location_id_value = Input::get ( 'zone_location_id_value' );
			$courier_delivery_type_value = Input::get ( 'courier_delivery_type' );
			$results = array ();
			if ($zone_location_id_value == 1) {
				if ($courier_delivery_type_value == 1) {
				if (! empty ( $fromlocation_loc )) {
					$zone_location_ids = DB::table ( 'ptl_pincodexsectors' )
					->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
					->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
					->where ( 'z2.seller_id', Auth::User ()->id )
					->where ( 'z2.lkp_service_id', Session::get('service_id') )
					->where ( 'z2.zone_name', 'LIKE', $term . '%' )
					->select ( 'z2.id', 'z2.zone_name' )->groupBy ( 'z2.id' )->get ();
				} else {
					$zone_location_ids = DB::table ( 'ptl_pincodexsectors' )
					->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
					->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
					->where ( 'z2.seller_id', Auth::User ()->id )
					->where ( 'z2.lkp_service_id', Session::get('service_id') )
					->where ( 'z2.zone_name', 'LIKE', $term . '%' )
					->select ( 'z2.id', 'z2.zone_name' )->groupBy ( 'z2.id' )->get ();
				}
				foreach ( $zone_location_ids as $zone_location_id ) {
					$results [] = [
					'id' => $zone_location_id->id,
					'value' => $zone_location_id->zone_name
					];
				}
				return Response::json ( $results );
			}else{
				if (isset ( $from_loc ) && $from_loc == 2) {
					$zone_location_ids = DB::table('lkp_countries')
					->where('country_name', 'LIKE', $term.'%')
					->take(10)->get();
					foreach ( $zone_location_ids as $zone_location_id ) {
						$results [] = [
						'id' => $zone_location_id->id,
						'value' => $zone_location_id->country_name
						];
					}
				} else {
					$zone_location_ids = DB::table ( 'ptl_pincodexsectors' )
					->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
					->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
					->where ( 'z2.seller_id', Auth::User ()->id )
					->where ( 'z2.lkp_service_id', Session::get('service_id') )
					->where ( 'z2.zone_name', 'LIKE', $term . '%' )
					->select ( 'z2.id', 'z2.zone_name' )->groupBy ( 'z2.id' )->get ();
					foreach ( $zone_location_ids as $zone_location_id ) {
						$results [] = [
						'id' => $zone_location_id->id,
						'value' => $zone_location_id->zone_name
						];
					}
				}
				
				return Response::json ( $results );
			}
			} else {
				if ($courier_delivery_type_value == 1) {
				if (isset ( $fromlocation_loc ) && $fromlocation_loc != "") {
					$queries = DB::table ( 'ptl_pincodexsectors' )
					->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
					->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
					->join ( 'lkp_ptl_pincodes as lpp', 'ptl_pincodexsectors.ptl_pincode_id', '=', 'lpp.id' )
					->where ( 'z2.seller_id', Auth::User ()->id )
					->where ( 'z2.lkp_service_id', Session::get('service_id') )
					->where ( 'lpp.id', '<>', $fromlocation_loc )
					->where ( 'lpp.pincode', 'LIKE', $term . '%' )
					->select ( '*' )->groupBy ( 'lpp.id' )->get ();
	
				} else {
					$queries = DB::table ( 'ptl_pincodexsectors' )
					->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
					->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
					->join ( 'lkp_ptl_pincodes as lpp', 'ptl_pincodexsectors.ptl_pincode_id', '=', 'lpp.id' )
					->where ( 'z2.seller_id', Auth::User ()->id )
					->where ( 'z2.lkp_service_id', Session::get('service_id') )
					->where ( 'lpp.pincode', 'LIKE', $term . '%' )
					->select ( '*' )->groupBy ( 'lpp.id' )->get ();
	
				}
				foreach ( $queries as $query ) {
					$results [] = [
					'id' => $query->id,
					'value' => $query->pincode."-".$query->postoffice_name.",".$query->districtname.",".$query->statename
					];
				}
				return Response::json ( $results );
				}
				else {
					if ($courier_delivery_type_value == 2) {
				if (isset ( $from_loc ) && $from_loc == 2) {
					$queries = DB::table('lkp_countries')
					->where('country_name', 'LIKE', $term.'%')
					->take(10)->get();
					foreach ( $queries as $zone_location_id ) {
						$results [] = [
						'id' => $zone_location_id->id,
						'value' => $zone_location_id->country_name
						];
					}
				
				} else {
					$queries = DB::table ( 'ptl_pincodexsectors' )
					->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
					->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
					->join ( 'lkp_ptl_pincodes as lpp', 'ptl_pincodexsectors.ptl_pincode_id', '=', 'lpp.id' )
					->where ( 'z2.seller_id', Auth::User ()->id )
					->where ( 'z2.lkp_service_id', Session::get('service_id') )
					->where ( 'lpp.pincode', 'LIKE', $term . '%' )
					->select ( '*' )->groupBy ( 'lpp.id' )->get ();
					foreach ( $queries as $query ) {
						$results [] = [
						'id' => $query->id,
						'value' => $query->pincode."-".$query->postoffice_name.",".$query->districtname.",".$query->statename
						];
					}
				
				}
				
				return Response::json ( $results );
				}
			}
				
				
			}
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	}
	public function ptlZoneAutocompleteCourierSearch() {
		try {
			$term = Input::get ( 'term' );
			$fromlocation_loc = Input::get ( 'ptlFromLocation' );
			$zone_location_id_value = Input::get ( 'zone_location_id_value' );
			$courier_delivery_type_value = Input::get ( 'courier_delivery_type' );
			$search_location = Input::get ( 'search' );
			$results = array ();
			if ($zone_location_id_value == 1) {
				if ($courier_delivery_type_value == 1) {
					if (! empty ( $fromlocation_loc )) {
						$zone_location_ids = DB::table ( 'ptl_pincodexsectors' )
						->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
						->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
						->where ( 'z2.seller_id', Auth::User ()->id )
						->where ( 'z2.lkp_service_id', Session::get('service_id') )
						->where ( 'z2.zone_name', 'LIKE', $term . '%' )->orderBy ( 'z2.zone_name', 'asc' )
						->select ( 'z2.id', 'z2.zone_name' )->groupBy ( 'z2.id' )->get ();
					} else {
						$zone_location_ids = DB::table ( 'ptl_pincodexsectors' )
						->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
						->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
						->where ( 'z2.seller_id', Auth::User ()->id )
						->where ( 'z2.lkp_service_id', Session::get('service_id') )
						->where ( 'z2.zone_name', 'LIKE', $term . '%' )->orderBy ( 'z2.zone_name', 'asc' )
						->select ( 'z2.id', 'z2.zone_name' )->groupBy ( 'z2.id' )->get ();
					}
					foreach ( $zone_location_ids as $zone_location_id ) {
						$results [] = [
						'id' => $zone_location_id->id,
						'value' => $zone_location_id->zone_name
						];
					}
					return Response::json ( $results );
				}else{
							if ( $search_location == "2") {
							$zone_location_ids = DB::table('lkp_countries')
							->where('country_name', 'LIKE', $term.'%')->orderBy ( 'country_name', 'asc' )
							->take(10)->get();
							foreach ( $zone_location_ids as $zone_location_id ) {
								$results [] = [
								'id' => $zone_location_id->id,
								'value' => $zone_location_id->country_name
								];
							}
						
					} else {
						if ( $search_location == "1") {
						$zone_location_ids = DB::table ( 'ptl_pincodexsectors' )
						->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
						->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
						->where ( 'z2.seller_id', Auth::User ()->id )
						->where ( 'z2.lkp_service_id', Session::get('service_id') )
						->where ( 'z2.zone_name', 'LIKE', $term . '%' )->orderBy ( 'z2.zone_name', 'asc' )
						->select ( 'z2.id', 'z2.zone_name' )->groupBy ( 'z2.id' )->get ();
						foreach ( $zone_location_ids as $zone_location_id ) {
							$results [] = [
							'id' => $zone_location_id->id,
							'value' => $zone_location_id->zone_name
							];
						}
					}
					}
	
					return Response::json ( $results );
				}
			} else {
				if ($courier_delivery_type_value == 1) {
					if (isset ( $fromlocation_loc ) && $fromlocation_loc != "") {
						$queries = DB::table ( 'lkp_ptl_pincodes' )->orderBy ( 'postoffice_name', 'asc' )->where ( 'pincode', 'LIKE', $term . '%' )->where ( 'id', '<>', $fromlocation_loc );
						$queries = $queries->take ( 10 )->get ();
					} else {
						$queries = DB::table ( 'lkp_ptl_pincodes' )->orderBy ( 'postoffice_name', 'asc' )->where ( 'pincode', 'LIKE', $term . '%' );
						$queries = $queries->take ( 10 )->get ();
	
					}
					foreach ( $queries as $query ) {
						$results [] = [
						'id' => $query->id,
						'value' => $query->pincode."-".$query->postoffice_name.",".$query->districtname.",".$query->statename
						];
					}
					return Response::json ( $results );
				} else {
					
					if ($courier_delivery_type_value == 2) {
						if ( $search_location == "2") {
							$queries = DB::table('lkp_countries')->orderBy ( 'country_name', 'asc' )
							->where('country_name', 'LIKE', $term.'%')
							->take(10)->get();
							foreach ( $queries as $zone_location_id ) {
								$results [] = [
								'id' => $zone_location_id->id,
								'value' => $zone_location_id->country_name
								];
							}
	
						} else {
							if ( $search_location == "1") {
							$queries = DB::table ( 'lkp_ptl_pincodes' )->orderBy ( 'postoffice_name', 'asc' )->where ( 'pincode', 'LIKE', $term . '%' );
							$queries = $queries->take ( 10 )->get ();
							foreach ( $queries as $query ) {
								$results [] = [
								'id' => $query->id,
								'value' => $query->pincode."-".$query->postoffice_name.",".$query->districtname.",".$query->statename
								];
							}
	
						}
						}
	
						return Response::json ( $results );
					}
				}
	
	
			}
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	}
	public function ptlZoneAutocompleteSearch() {
		try {
			if(Session::get('service_id') == AIR_INTERNATIONAL ){
				$term = Input::get('term');
				$fromlocation_loc = Input::get('ptlFromLocation');
				$country = Input::get('country');
				$results = array();
				$querydata = DB::table('lkp_airports')->orderBy ( 'airport_name', 'asc' )
					->whereRaw ("(airport_name LIKE '".$term."%' or location LIKE '".$term."%' or iata_code LIKE '".$term."%' or country_name LIKE '".$term."%')");
				if(isset($fromlocation_loc) && !empty($fromlocation_loc))
					$querydata->where('id','<>', $fromlocation_loc);
				if(isset($country) && !empty($country))
					$querydata->where('country_abbrev','=', "IN");

				$queries = $querydata->take(10)->get();
				foreach ($queries as $query)
				{
					$results[] = [ 'id' => $query->id, 'value' => $query->airport_name.' , '.$query->location ];
				}
				return Response::json($results);
			}
			elseif( Session::get('service_id') == OCEAN){
				$term = Input::get('term');
				$fromlocation_loc = Input::get('ptlFromLocation');
				$results = array();
				if(isset($fromlocation_loc)){
					$queries = DB::table('lkp_seaports')->orderBy ( 'seaport_name', 'asc' )
					->where('seaport_name', 'LIKE', $term.'%')
					->where('id','<>', $fromlocation_loc)
					->take(10)->get();
				}else {
					$queries = DB::table('lkp_seaports')->orderBy ( 'seaport_name', 'asc' )
					->where('seaport_name', 'LIKE', $term.'%')
					->take(10)->get();
				}
				foreach ($queries as $query)
				{
					$results[] = [ 'id' => $query->id, 'value' => $query->seaport_name.' , '.$query->country_name ];
				}
				return Response::json($results);
			}else{
				$term = trim(Input::get ( 'term' ));
				$fromlocation_loc = trim(Input::get ( 'ptlFromLocation' ));
				$zone_location_id_value = trim(Input::get ( 'zone_location_id_value' ));
				$results = array ();
				if ($zone_location_id_value == 1) {
					if(! empty ($term)) {
						if (! empty ( $fromlocation_loc )) {
							$zone_location_ids = DB::table ( 'ptl_pincodexsectors' )
								->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
								->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
								->where ( 'z2.seller_id', Auth::User ()->id )
								->where ( 'z2.lkp_service_id', Session::get('service_id') )
								->where ( 'z2.zone_name', 'LIKE', $term . '%' )->orderBy ( 'z2.zone_name', 'asc' )
								->select ( 'z2.id', 'z2.zone_name' )->distinct ( 'z2.id' )->get ();
						} else {
							$zone_location_ids = DB::table ( 'ptl_pincodexsectors' )
								->join ( 'ptl_sectors as s1', 'ptl_pincodexsectors.ptl_sector_id', '=', 's1.id' )
								->join ( 'ptl_zones as z2', 's1.ptl_zone_id', '=', 'z2.id' )
								->where ( 'z2.seller_id', Auth::User ()->id )
								->where ( 'z2.lkp_service_id', Session::get('service_id') )
								->where ( 'z2.zone_name', 'LIKE', $term . '%' )->orderBy ( 'z2.zone_name', 'asc' )
								->select ( 'z2.id', 'z2.zone_name' )->distinct ( 'z2.id' )->get ();
						}
						foreach ( $zone_location_ids as $zone_location_id ) {
							$results [] = [
								'id' => $zone_location_id->id,
								'value' => $zone_location_id->zone_name
							];
						}
						return Response::json ( $results );
					} }else {
					if (!preg_match('/[^A-Za-z]/', $term)) // '/[^a-z\d]/i' should also work.
					{
						echo "error";
						exit;
	
					}
					if (isset ( $fromlocation_loc ) && $fromlocation_loc != "") {
						$queries = DB::table ( 'lkp_ptl_pincodes' );
						$queries->where ( 'pincode', 'LIKE', $term . '%' );
						$queries->where ( 'id', '<>', $fromlocation_loc )->orderBy ( 'postoffice_name', 'asc' );
						$resultsset = $queries->take ( 10 )->get ();
					} else {
						$queries = DB::table ( 'lkp_ptl_pincodes' );
						$queries->where ( 'pincode', 'LIKE', $term . '%' )->orderBy ( 'postoffice_name', 'asc' );
						$resultsset = $queries->take ( 10 )->get ();
					}
					foreach ( $resultsset as $query ) {
						$results [] = [
							'id' => $query->id,
							'value' => $query->pincode."-".$query->postoffice_name.",".$query->districtname.",".$query->statename
						];
					}
					return Response::json ( $results );
				}
			}
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	}


	/*
	 * PTL MASTER STARTS HERE
	 */
	public function viewZone() {
		
		if (Input::all ()) {
			if(!isset($_GET['page']) && !isset($_GET['ord'])){
				
			$data = Input::all ();

			$is_saved = PtlSellerComponent::addPtlZone ( $data );

			if ($is_saved == '1') {

				return Redirect ( 'ptlmasters/zone' )->with ( 'ptl_success_message', 'Zone saved successfully.' );
			} elseif ($is_saved == '0') {
				return Redirect ( 'ptlmasters/zone' )->with ( 'ptl_error_message', 'Error occured while saving zone details.' );
			} else {

				return Redirect ( 'ptlmasters/zone' )->with ( 'ptl_error_message', $is_saved );
			}
			}
		}
		$grid = PtlSellerComponent::getZoneGrid ();
		return view ( 'ptl.sellers.masters.zone',['grid'=>$grid] );
	}
	public function viewSector() {
		$zonesList = CommonComponent::getPtlZonesList ();
		$tiersList = CommonComponent::getPtlTiersList ();
		if (Input::all ()) {
			if(!isset($_GET['page']) && !isset($_GET['ord'])){
			$data = Input::all ();

			$is_saved = PtlSellerComponent::addPtlSector ( $data );
			if ($is_saved == '1') {

				return Redirect ( 'ptlmasters/sector' )->with ( 'ptl_success_message', 'Sector saved successfully.' );
			} elseif ($is_saved == '0') {
				return Redirect ( 'ptlmasters/sector' )->with ( 'ptl_error_message', 'Error occured while saving sector details.' );
			} else {

				return Redirect ( 'ptlmasters/sector' )->with ( 'ptl_error_message', $is_saved );
			}
		}
		}
		$grid = PtlSellerComponent::getSectorGrid ();
		return view ( 'ptl.sellers.masters.sector', array (
			'grid'=>$grid,
			'zonesList' => $zonesList,
			'tiersList' => $tiersList
		) );
	}
	public function viewPincode() {
		$sectorsList = CommonComponent::getPtlSectorsList ();
		$grid = PtlSellerComponent::getPincodeGrid ();
		return view ( 'ptl.sellers.masters.pincode',array('sectorsList'=>$sectorsList,'grid'=>$grid) );
	}
	public function viewTransitMatrix() {
		$tiers = DB::table ( 'ptl_tiers as pt' )->Where ( 'pt.seller_id', Auth::User ()->id )->where ( 'lkp_service_id', Session::get('service_id') )->select ( '*' )->distinct ()->get ();


		return view ( 'ptl.sellers.masters.transitdays_matrix',array('tiers'=>$tiers) );
	}
	public function viewTier() {
		if (Input::all ()) {
			if(!isset($_GET['page']) && !isset($_GET['ord'])){
			$data = Input::all ();

			$is_saved = PtlSellerComponent::addPtlTier ( $data );

			if ($is_saved == '1') {

				return Redirect ( 'ptlmasters/tier' )->with ( 'ptl_success_message', 'Tier saved successfully.' );
			} elseif ($is_saved == '0') {
				return Redirect ( 'ptlmasters/tier' )->with ( 'ptl_error_message', 'Error occured while saving tier details.' );
			} else {

				return Redirect ( 'ptlmasters/tier' )->with ( 'ptl_error_message', $is_saved );
			}
		}
		}
		
		$grid = PtlSellerComponent::getTierGrid ();
		return view ( 'ptl.sellers.masters.tier',['grid'=>$grid] );
	}
	/**
	 * EDITABLE ZONE MASTER GRID
	 */
	public function editableZone() {
		$grid = new EditableGrid ();

		/*
		 * Add columns. The first argument of addColumn is the name of the field in the databse.
		 * The second argument is the label that will be displayed in the header
		 */
		$grid->addColumn('id', 'ID', false);
		$grid->addColumn ( 'seller_id', 'S.NO', 'integer', NULL, false );
		$grid->addColumn ( 'zone_name', 'Zone Name', 'string' );
		$grid->addColumn ( 'zone_code', 'Code', 'string' );
		$grid->addColumn ( 'action', 'Action', 'html', NULL, false, 'id' );

		$result = DB::table ( 'ptl_zones' )->Where ( 'seller_id', Auth::User ()->id )->where ( 'lkp_service_id', Session::get('service_id') )->select ( 'id', 'zone_name', 'zone_code' )->get ();
		
		$i=1;
		foreach($result as $res){
			$res->seller_id=$i;
			$i++;
		}
		// send data to the browser
		$grid->renderJSON ( $result );
	}
	public function editPtlZone() {
		
		// Get all parameters provided by the javascript
		$zone_name = (strip_tags ( $_POST ['zoneName'] ));
		$zone_code = (strip_tags ( $_POST ['zoneCode'] ));
		
		$id = (strip_tags ( $_POST ['zoneId'] ));
		
		$data = PtlZone::where ( 'id', $id )->where ( 'lkp_service_id', Session::get('service_id') )->where('seller_id', Auth::User ()->id)->first ();
		if (trim($zone_name) != '' && trim($zone_code) != '') {
			$data ['zone_name'] = $zone_name;
			$isUniqueZonename = PtlSellerComponent::checkUniqueZoneName ( $data );
		
			$data ['zone_code'] = $zone_code;
			$isUniqueZoneCode = PtlSellerComponent::checkUniqueZoneCode( $data );
		}

		if ($isUniqueZonename == 1 && $isUniqueZoneCode == 1) {
			try{
				DB::table ('ptl_zones')->where ( 'id', $id )->where ( 'lkp_service_id', Session::get('service_id') )->where('seller_id', Auth::User ()->id)
				->update (array('zone_name' => $zone_name,'zone_code'=> $zone_code) );
				return 'ok';
			}
			catch(Exception $e)
			{
				return 'error';
			}
		}else{
			if($isUniqueZonename !=1){
				return $isUniqueZonename;
			}
		elseif($isUniqueZoneCode !=1){
			return $isUniqueZoneCode;
		}}
	}
	public function deletePtlZone() {
		// Get all parameter provided by the javascript
		$id = (strip_tags ( $_POST ['zoneId'] ));
		
		// check in sector table
		$isZoneExist = DB::table ( 'ptl_sectors' )->where ( 'ptl_zone_id', $id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();

		if (empty ( $isZoneExist )) {
			try {
				if (DB::table ( 'ptl_zones' )->where ( 'id', $id )->where ( 'lkp_service_id', Session::get('service_id') )->delete ()) {
					return 'ok';
				} else {
					return 'error';
				}
			} catch ( Exception $ex ) {
			}
		} else {
			return "zone";
		}
	}
	/**
	 * *********ENDS***************
	 */

	/**
	 * EDITABLE TIER MASTER GRID
	 */
	public function editableTier() {
		$grid = new EditableGrid ();

		/*
		 * Add columns. The first argument of addColumn is the name of the field in the databse.
		 * The second argument is the label that will be displayed in the header
		 */
		$grid->addColumn('id', 'ID', false);
		$grid->addColumn ( 'seller_id', 'S.NO', 'integer', NULL, false );
		$grid->addColumn ( 'tier_name', 'Tier Name', 'string' );
		$grid->addColumn ( 'tier_code', 'Code', 'string' );
		$grid->addColumn ( 'action', 'Action', 'html', NULL, false, 'id' );

		$result = DB::table ( 'ptl_tiers' )->Where ( 'seller_id', Auth::User ()->id )->where ( 'lkp_service_id', Session::get('service_id') )->select ( 'id', 'tier_name', 'tier_code' )->get ();
		$i=1;
		foreach($result as $res){
			$res->seller_id=$i;
			$i++;
		}
		// send data to the browser
		$grid->renderJSON ( $result );
	}
	public function editPtlTier() {
		// Get all parameters provided by the javascript function
		$tier_name = (strip_tags ( $_POST ['tierName'] ));
		$tier_code = (strip_tags ( $_POST ['tierCode'] ));
		$id = (strip_tags ( $_POST ['tierId'] ));
		
		// Here, checking the uniqueness of tier name and tier code before updation
		$data = PtlTier::where ( 'id', $id )->where ( 'lkp_service_id', Session::get('service_id') )->where('seller_id', Auth::User ()->id)->first ();
		
		if (trim($tier_name) != '' && trim($tier_code) != '') {
			$data ['tier_name'] = $tier_name;
			$isUniqueTierName = PtlSellerComponent::checkUniqueTierName ( $data );
		
			$data ['tier_code']  = $tier_code;
			$isUniqueTierCode = PtlSellerComponent::checkUniqueTierCode( $data );
		}
		

	if ($isUniqueTierName == 1 && $isUniqueTierCode == 1) {
			try{
				DB::table ('ptl_tiers')->where ( 'id', $id )->where ( 'lkp_service_id', Session::get('service_id') )->where('seller_id', Auth::User ()->id)
				->update (array('tier_name' => $tier_name,'tier_code'=> $tier_code) );
				return 'ok';
			}
			catch(Exception $e)
			{
				return 'error';
			}
		}else{
			if($isUniqueTierName !=1){
				return $isUniqueTierName;
			}
			elseif($isUniqueTierCode !=1){
				return $isUniqueTierCode;
			}}
	}
	public function deletePtlTier() {
		// Get all parameter provided by the javascript
		$id = (strip_tags ( $_POST ['tierId'] ));

		$isSectorExist = DB::table ( 'ptl_sectors' )->where ( 'ptl_tier_id', $id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();

		if (empty ( $isSectorExist )) {
			try {
				$isDaysExist = DB::table ( 'ptl_transitdays' )->where ( 'lkp_service_id', Session::get('service_id') )->where ( 'from_tier_id', $id )->orwhere ( 'to_tier_id', $id )->first ();
			// delete tiers from transitdays table
		
				if (!empty( $isDaysExist )) {
					
					DB::table ( 'ptl_transitdays' )->where ( 'lkp_service_id', Session::get('service_id') )->where ( 'from_tier_id', $id )->orwhere ( 'to_tier_id', $id )->delete ();
					
				}
				DB::table ( 'ptl_tiers' )->where ( 'id', $id )->where ( 'lkp_service_id', Session::get('service_id') )->delete ();
					return "ok";
				
			} catch ( Ecxeption $ex ) {
				
				return 'error';
			}
		} else {
			
			return "sector";
		}
	}
	/**
	 * *********ENDS***************
	 */

	/**
	 * EDITABLE SECTOR MASTER GRID
	 */
	public function editableSector() {
		$grid = new EditableGrid ();

		/*
		 * Add columns. The first argument of addColumn is the name of the field in the databse.
		 * The second argument is the label that will be displayed in the header
		 */
		$grid->addColumn('id', 'ID', false);
		$grid->addColumn ( 'seller_id', 'S.NO', 'integer', NULL, false );
		$grid->addColumn ( 'sector_name', 'Sector Name', 'string' );
		$grid->addColumn ( 'sector_code', 'Code', 'string' );
		$grid->addColumn ( 'zone_name', 'Zone', 'string', NULL, false );
		$grid->addColumn ( 'tier_name', 'Tier', 'string' , NULL, false);
		$grid->addColumn ( 'action', 'Action', 'html', NULL, false, 'id' );		

		$result = DB::table ( 'ptl_sectors as ps' )->leftjoin ( 'ptl_zones as pz', 'ps.ptl_zone_id', '=', 'pz.id' )->leftjoin ( 'ptl_tiers as pt', 'ps.ptl_tier_id', '=', 'pt.id' )->Where ( 'ps.seller_id', Auth::User ()->id )->where ( 'ps.lkp_service_id', Session::get('service_id') )->select ( 'ps.id', 'pz.zone_name as zone_name', 'pt.tier_name as tier_name', 'ps.sector_name as sector_name', 'ps.sector_code as sector_code' )->get ();

		$i=1;
		foreach($result as $res){
			$res->seller_id=$i;
			$i++;
		}
		// send data to the browser
		$grid->renderJSON ( $result );
	}
	public function editPtlSector() {
			$sector_name = (strip_tags ( $_POST ['sectorName'] ));
			$sector_code = (strip_tags ( $_POST ['sectorCode'] ));
			$id = (strip_tags ( $_POST ['sectorId'] ));
			

		// Here, checking the uniqueness of tier name and tier code before updation
		$data = PtlSector::where ( 'id', $id )->where ( 'lkp_service_id', Session::get('service_id') )->where('seller_id', Auth::User ()->id)->first ();
		
		
		if (trim($sector_name) != '' && trim($sector_code) != '') {
			$data ['sector_name'] = $sector_name;
			$isUniqueSectorName = PtlSellerComponent::checkUniqueSectorName ( $data );
		
			$data ['sector_code']  = $sector_code;
			$isUniqueSectorCode = PtlSellerComponent::checkUniqueSectorCode( $data );
		}
		
		
		

		if ($isUniqueSectorName == 1 && $isUniqueSectorCode == 1) {
			try{
				DB::table ('ptl_sectors')->where ( 'id', $id )->where ( 'lkp_service_id', Session::get('service_id') )->where('seller_id', Auth::User ()->id)
				->update (array('sector_name' => $sector_name,'sector_code'=> $sector_code,
                                                'ptl_zone_id' => $_POST ['zone'],'ptl_tier_id'=> $_POST ['tier']) );
				return 'ok';
			}
			catch(Exception $e)
			{
				return 'error';
			}
		}else{
			if($isUniqueSectorName !=1){
				return $isUniqueSectorName;
			}
			elseif($isUniqueSectorCode !=1){
				return $isUniqueSectorCode;
			}}
		
		
		
		
	}
	public function deletePtlSector() {
		// Get all parameter provided by the javascript
		$id = (strip_tags ( $_POST ['sectorId'] ));
		$isSectorExist = DB::table ( 'ptl_pincodexsectors' )->where ( 'ptl_sector_id', $id )->where ( 'is_active', 1 )->where ( 'lkp_service_id', Session::get('service_id') )->first ();

		if (empty ( $isSectorExist )) {
			try {

				$res = DB::table ( 'ptl_sectors' )->where ( 'id', $id )
					->where ( 'lkp_service_id', Session::get('service_id') )
					->update ( array('is_active' => '0') );

				if($res == 1)
				{
					return 'ok';
				} else {
					return 'error';
				}
			} catch ( Exception $ex ) {
			}
		} else {
			return "sector";
		}
	}
	/**
	 * *********ENDS***************
	 */

	/**
	 * EDITABLE PINCODE MASTER GRID
	 */
	public function editablePincode() {
		$grid = new EditableGrid ();

		/*
		 * Add columns. The first argument of addColumn is the name of the field in the databse.
		 * The second argument is the label that will be displayed in the header
		 */
		$grid->addColumn('id', 'ID', false);
		$grid->addColumn ( 'seller_id', 'S.NO', 'integer', NULL, false );
		$grid->addColumn ( 'pincode', 'Pincode Id', 'string', NULL, false );
		$grid->addColumn ( 'location', 'Location', 'string', NULL, false );
		$grid->addColumn ( 'city', 'City', 'string', NULL, false );
		$grid->addColumn ( 'sector_name', 'Sector', 'string' );
		$grid->addColumn ( 'postal_division', 'Postal Div.', 'string', NULL, false );
		$grid->addColumn ( 'district', 'District', 'string', NULL, false );
		$grid->addColumn ( 'state', 'State', 'string', NULL, false );
		$grid->addColumn ( 'zone_name', 'Zone', 'string',Null,false );
		$grid->addColumn ( 'tier_name', 'Tier', 'string',Null,false );
		$grid->addColumn ( 'oda', 'ODA', 'string' );

		$grid->addColumn ( 'action', 'Action', 'html', NULL, false, 'id' );

		$result = DB::table ( 'ptl_pincodexsectors as pxs' )
			->leftjoin ( 'lkp_ptl_pincodes as lpp', 'pxs.ptl_pincode_id', '=', 'lpp.id' )
			->leftjoin ( 'ptl_sectors as ps', 'pxs.ptl_sector_id', '=', 'ps.id' )
			->leftjoin ( 'ptl_zones as pz', 'ps.ptl_zone_id', '=', 'pz.id' )
			->leftjoin ( 'ptl_tiers as pt', 'ps.ptl_tier_id', '=', 'pt.id' )
			->Where ( 'pxs.seller_id', Auth::User ()->id )
			->where ( 'pxs.lkp_service_id', Session::get('service_id') )
			->select ( 'pxs.*','lpp.pincode as pincode', 'pz.zone_name as zone_name', 'pt.tier_name as tier_name', 'ps.sector_name',DB::raw('(CASE WHEN pxs.oda=1 THEN "Yes" ELSE "No" END) AS oda') )
			->get ();

		//echo "<pre>";print_r($result);die();
		$i=1;
		foreach($result as $res){
			$res->seller_id=$i;
			$i++;
		}
		// send data to the browser
		$grid->renderJSON ( $result );
	}
	public function editPtlPincode() {
		// Get all parameters provided by the javascript
		$colname = (strip_tags ( $_POST ['colname'] ));
		$id = (strip_tags ( $_POST ['id'] ));		
		$value = (strip_tags ( $_POST ['newvalue'] ));
		$tablename = (strip_tags ( $_POST ['tablename'] ));

		// Here, this is a little tips to manage date format before update the table
		if ($colname == 'oda') {
			if (strtolower($value) === strtolower('Yes'))
				$value = '1';
			else {
				$value = '0';
			}
		}

		if (DB::table ( $tablename )->where ( 'id', $id )->where ( 'lkp_service_id', Session::get('service_id') )->update ( [
			$colname => $value
		] )) {
			return 'ok';
		} else {
			return 'error';
		}
	}
	public function deletePtlPincode() {
		// Get all parameter provided by the javascript
		$id = (strip_tags ( $_POST ['pincodeId'] ));
		$tablename = 'ptl_pincodexsectors';

		// This very generic. So this script can be used to update several tables.
		// $return=false;

	//(DB::table ( $tablename )->where ( 'id', $id )->where ( 'lkp_service_id', Session::get('service_id') )->delete ()) {
			if(DB::table ( $tablename )->where ( 'id', $id )
					->where ( 'lkp_service_id', Session::get('service_id') )
					->update ( ['is_active' => '0'] ))
			{
			return 'ok';
			} else {
			return 'error';
			}
	}
        //check pincode for exists in post or not
        public function checkPtlPincode() {
		// Get all parameter provided by the javascript
		$id = (strip_tags ( $_POST ['pincodeId'] ));
		$tablename = 'ptl_pincodexsectors';
		// This very generic. So this script can be used to update several tables.
		// $return=false;
                $serviceId= Session::get('service_id');
                $data=  DB::table ( $tablename )->where ( 'id', $id )
                        ->where ( 'lkp_service_id', Session::get('service_id') )
                        ->select('ptl_pincode_id','seller_id')
                        ->first ();
				switch($serviceId){
                    case ROAD_PTL       :                         
                        
						$res    =   DB::table ( 'ptl_seller_post_items as spi' )
							->leftjoin ( 'ptl_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' )
							->where ( 'spi.created_by', $data->seller_id )
							->where ( 'sp.lkp_ptl_post_type_id', 2 )
							->where('sp.lkp_post_status_id','2')
							->whereRaw( "( spi.from_location_id = $data->ptl_pincode_id or spi.to_location_id = $data->ptl_pincode_id )")
							->select('spi.seller_post_id')
							->first ();
                        break;
                    case RAIL       :
                        $res    =   DB::table ( 'rail_seller_post_items as spi' )
										->leftjoin ( 'rail_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' )
										->where ( 'spi.created_by', $data->seller_id )
										->where ( 'sp.lkp_ptl_post_type_id', 2 )
										->where('sp.lkp_post_status_id','2')
										->whereRaw( "( spi.from_location_id = $data->ptl_pincode_id or spi.to_location_id = $data->ptl_pincode_id )")
										->select('spi.seller_post_id')
										->first ();
						break;
                    case AIR_DOMESTIC       : 
                       
						$res    =   DB::table ( 'airdom_seller_post_items as spi' )
							->leftjoin ( 'airdom_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' )
							->where ( 'spi.created_by', $data->seller_id )
							->where ( 'sp.lkp_ptl_post_type_id', 2 )
							->where('sp.lkp_post_status_id','2')
							->whereRaw( "( spi.from_location_id = $data->ptl_pincode_id or spi.to_location_id = $data->ptl_pincode_id )")
							->select('spi.seller_post_id')
							->first ();
                        break;
                        case COURIER       :
                        	
							$res    =   DB::table ( 'courier_seller_post_items as spi' )
								->leftjoin ( 'courier_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' )
								->where ( 'spi.created_by', $data->seller_id )
								->where ( 'sp.lkp_ptl_post_type_id', 2 )
								->where('sp.lkp_post_status_id','2')
								->whereRaw( "( spi.from_location_id = $data->ptl_pincode_id or spi.to_location_id = $data->ptl_pincode_id )")
								->select('spi.seller_post_id')
								->first ();
                      break;
                }

                if(!empty($res) && $res->seller_post_id!=""){
			return 'ok';   
                }else{return 'delete';}
		
	}
	/**
	 * *********ENDS***************
	 */

	/**
	 * EDITABLE TRANSIT DAYS MASTER GRID
	 */
	public function editPtlTransit() {
		// Get parameters provided by the javascript
		if (isset ( $_POST ['transitId'] ) && $_POST ['transitId'] != '') {

			$transitId = $_POST ['transitId'];

			if (isset ( $_POST ['transitValue'] ) && $_POST ['transitValue'] != '') {
				$transitValue = $_POST ['transitValue'];
			}
			
			$pieces = explode ( "_", $transitId );

			$from_tier_id = $pieces [0];
			$to_tier_id = $pieces [1];

			try {
				$isRecordExist = DB::table ( 'ptl_transitdays' )->where ( 'from_tier_id', $from_tier_id )->where ( 'to_tier_id', $to_tier_id )->where ( 'lkp_service_id', Session::get('service_id') )->get ();

				if (empty ( $isRecordExist )) {

					$ptlTransitDay = new PtlTransitday ();
					$createdAt = date ( 'Y-m-d H:i:s' );
					$createdIp = $_SERVER ["REMOTE_ADDR"];

					$ptlTransitDay->from_tier_id = $from_tier_id;
					$ptlTransitDay->to_tier_id = $to_tier_id;
					$ptlTransitDay->no_days = $transitValue;
					$ptlTransitDay->is_active = '1';
					$ptlTransitDay->lkp_service_id = Session::get('service_id');
					$ptlTransitDay->created_at = $createdAt;
					$ptlTransitDay->created_ip = $createdIp;
					$ptlTransitDay->created_by = Auth::User ()->id;

					if ($ptlTransitDay->save ()) {
						return '1';
					}
				} else {

					DB::table ( 'ptl_transitdays' )->where ( 'from_tier_id', $from_tier_id )->where ( 'to_tier_id', $to_tier_id )->where ( 'lkp_service_id', Session::get('service_id') )->update ( [
						'no_days' => $transitValue
					] );
					return '1';
				}
			} catch ( Exception $ex ) {
				return $ex;
			}
		}
	}
	/**
	 * *************************ENDS ***************************
	 */
	/**
	 * ADD PINCODE PAGE & AUTOCOMPLETE PINCODES
	 */
	public function autocompletePincodes() {
		// Log::info ( 'get Intracity from location using autocomplete: ' . Auth::id (), array (
		// 'c' => '1'
		// ) );
		try {
			$term = Input::get ( 'term' );

			$results = array ();
			if (isset ( $term )) {
				CommonComponent::getPincodesSeller(Auth::User ()->id);
				$querieResult = DB::table ( 'lkp_ptl_pincodes' )
				->where ( 'pincode', 'LIKE', $term . '%' )				
				->where ( 'is_active', '1' )->take( 10 )->get ();
			}
			foreach ( $querieResult as $query ) {
				$results [] = [
					'id' => $query->id,
					'pincode' => $query->pincode,
					'value' => $query->pincode."-".$query->postoffice_name.",".$query->districtname.",".$query->statename ,
					'postoffice_name' => $query->postoffice_name ,
					'divisionname' => $query->divisionname ,
					'regionname' => $query->regionname ,
					'taluk' => $query->taluk ,
					'districtname' => $query->districtname ,
					'statename' => $query->statename
				];
			}
			return Response::json ( $results );
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	}
	public function fillEditPtlSector(){
		if(Input::all()){
			$data = Input::all();
			$pinCode = $data['pinCode'];
			
			$queryResult = DB::table ( 'ptl_pincodexsectors' )->where ( 'seller_id', Auth::User()->id )->where ( 'ptl_pincode_id', $pinCode )->where ( 'is_active', '1' )->where('lkp_service_id',Session::get('service_id'))->first();
			if(!empty($queryResult)){
				$pincodeResult[] = ['sector' =>$queryResult->ptl_sector_id,'oda'=>$queryResult->oda];
			return  $pincodeResult ;
			}
			else{return $pincodeResult[]=array() ;}
			
			
		}
		
	}
	
	public function viewAddPincode() {
		$zonesList = CommonComponent::getPtlZonesList ();
		$tiersList = CommonComponent::getPtlTiersList ();
		$sectorsList = CommonComponent::getPtlSectorsList ();

		if (Input::all ()) {
			$data = Input::all ();
					
		
                    $is_pincode_exist = DB::table ( 'ptl_pincodexsectors' )->where ( 'ptl_pincode_id', $data ['ptlPincodeId'] )->where ( 'seller_id', Auth::User()->id)->where('lkp_service_id',Session::get('service_id'))->first();
                 
                    try{
                    if(empty($is_pincode_exist)){
                    
                    	$queryResult = DB::table ( 'lkp_ptl_pincodes' )->where ( 'pincode', $data ['ptl_pincode_id'] )->where ( 'is_active', '1' )->get();
                    	 
                 //$queryResult = DB::table ( 'lkp_ptl_pincodes' )->where ( 'pincode', $query->pincode )->where ( 'is_active', '1' )->get();
                    foreach ( $queryResult as $query ) {
                        $ptlPincodeXsector = new PtlPincodexsector ();
                            $isUniquePincode = PtlSellerComponent::checkUniquePincode ($query->id, $data ['ptl_sector_id']);
                        if ($isUniquePincode == '1') {
                                $createdAt = date ( 'Y-m-d H:i:s' );
                                $createdIp = $_SERVER ["REMOTE_ADDR"];

                                $ptlPincodeXsector->ptl_pincode_id = $query->id;
                                $ptlPincodeXsector->ptl_sector_id = $data ['ptl_sector_id'];
                                $ptlPincodeXsector->oda = $data ['oda_pincode'];
                                $ptlPincodeXsector->nsa = $data ['ptl_pincode_id'];
                                $ptlPincodeXsector->seller_id = Auth::User ()->id;
                                $ptlPincodeXsector->location = $query->postoffice_name.', '.$query->taluk;
                                $ptlPincodeXsector->city = $query->regionname;
                                $ptlPincodeXsector->district = $query->districtname;
                                $ptlPincodeXsector->postal_division = $query->divisionname;
                                $ptlPincodeXsector->state = $query->statename;
                                $ptlPincodeXsector->is_active = '1';
                                $ptlPincodeXsector->lkp_service_id = Session::get('service_id');
                                $ptlPincodeXsector->created_at = $createdAt;
                                $ptlPincodeXsector->created_ip = $createdIp;
                                $ptlPincodeXsector->created_by = Auth::User ()->id;

                                if ($ptlPincodeXsector->save ()) {
                                        CommonComponent::auditLog($ptlPincodeXsector->id, 'ptl_pincodexsectors');
                                        
                                } else {
                                        return Redirect ( 'ptlmasters/pincode' )->with ( 'ptl_error_message', 'Error occured while saving' );
                                }
                        }
                        else{
                            return Redirect ( 'ptlmasters/pincode' )->with ( 'ptl_error_message', $isUniquePincode );

                        }
                    }
                    return Redirect ( 'ptlmasters/pincode' )->with ( 'ptl_success_message', 'Pincode details saved successfully.' );
                    
                    }
                  else{
                  	
			$queryResult = DB::table ( 'lkp_ptl_pincodes' )->where ( 'pincode', $data ['ptl_pincode_id'] )->get();
                  
                 
                        foreach ($queryResult as $result)
                  	{ 
                           // DB::table ('ptl_pincodexsectors')->where ( 'ptl_pincode_id', $data['ptlPincodeId'])
                            DB::table ('ptl_pincodexsectors')->where ( 'ptl_pincode_id', $result->id)
                            ->where('seller_id', Auth::User ()->id)
                            ->where('lkp_service_id', Session::get('service_id'))
                            ->update (array('ptl_sector_id' => $data['ptl_sector_id'],'oda'=> $data['oda_pincode'],'is_active'=>'1') );
                    	}
                    	if($is_pincode_exist->is_active == 0){
                    	return Redirect ( 'ptlmasters/pincode' )->with ( 'ptl_success_message', 'Pincode added successfully.' );
                    	}else{
                    		return Redirect ( 'ptlmasters/pincode' )->with ( 'ptl_success_message', 'Pincode details edited successfully.' );
                    		
                    	} 
                    }
                    
                    }
                    catch(Exception $ex){
                    	
                    
                    }
		}
		return view ( 'ptl.sellers.masters.pincode', array (
			'zonesList' => $zonesList,
			'tiersList' => $tiersList,
			'sectorsList' => $sectorsList
		) );
	}

	/**
	 * Fill the form fields as user selects the pincode
	 */
	public function autoFillForm() {		
		if (isset ( $_POST ['sectorId'] ) && $_POST ['sectorId'] != '') {

			$sectorId = $_POST ['sectorId'];
			$queryResult = DB::table ( 'ptl_sectors as ps' )->leftjoin ( 'ptl_zones as pz', 'ps.ptl_zone_id', '=', 'pz.id' )->leftjoin ( 'ptl_tiers as pt', 'ps.ptl_tier_id', '=', 'pt.id' )->Where ( 'ps.id', '=', $sectorId )->where ('ps.ptl_tier_id','ps.ptl_zone_id', 'ps.lkp_service_id', Session::get('service_id') )->select ( 'pz.zone_name', 'pt.tier_name' )->first ();			
			return json_encode ( $queryResult );			
		}
	}

	/**
	 * upload multiple pincodes into db.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function upload(Request $request) {
		$new =array();		
		try {
			
			$messages = [
				//'equipment_upload.required' => 'Bulk Upload File field is required',
				'pincode_upload.mimes' => 'Bulk Upload should be a file of type: csv.',
			];
			$rules = [
				'pincode_upload' => 'mimes:csv,txt',
			];
			$this->validate($request, $rules, $messages);

			$errors = $_FILES['pincode_upload']['error'];			
			if ($errors == 0) {
				$sellerPincodeDirectory = 'uploads/seller/' . Auth::User()->id . '/pincode/';
				if (is_dir ( $sellerPincodeDirectory )) {
				} else {
					mkdir ( $sellerPincodeDirectory, 0777, true );
				}
				$handle = fopen($_FILES['pincode_upload']['tmp_name'], 'r');
				$_FILES['pincode_upload']['name'] = 'pincode.csv';
				if ($handle) {
					move_uploaded_file ($_FILES['pincode_upload']['tmp_name'], $sellerPincodeDirectory . $_FILES['pincode_upload']['name']);
					$arrayError =  array();
					$find_header = 0;
					$err = "";
					$sectorError = '';					
					$error = array();
					while (($line = fgetcsv($handle, 1000, ",")) != FALSE) {
						if ($find_header > 0) {
							$i = $find_header;
							$sectorError[$i]='';
							$error[$i] = "";
							$created_at = date('Y-m-d H:i:s');
							$createdIp = $_SERVER ['REMOTE_ADDR'];
							$j=0;
							
							if(empty($line[0]) || empty($line[1])){
								
								if(empty($line[0])) {$error[$i].="Pincode field is required at row " . $i . ".";}
								if(empty($line[1])) {$error[$i].="Sector field is required at row " . $i . ".";}
							}else{
								if($line[2]=='0' || $line[2]=='1'){}else{$error[$i].="ODA field is required in 1/0 format at row " . $i . ".";}
								if (!empty($line[0])){
								$querieResult = DB::table ( 'lkp_ptl_pincodes' )->where ( 'pincode', $line[0] )->where ( 'is_active', '1' )->get();
								
                                 if(empty($querieResult)){
                                 	
                                 	$error[$i].="Please enter listed pincode at row " . $i . ".";
                                 
                                }
                                
								foreach ( $querieResult as $query ) {
									$ptlPincodeXsector[$i][$j] = new PtlPincodexsector();
									$ptlPincodeXsector[$i][$j]->created_at = $created_at;
									$ptlPincodeXsector[$i][$j]->created_by = Auth::User ()->id;
									$ptlPincodeXsector[$i][$j]->created_ip = $createdIp;
									$ptlPincodeXsector[$i][$j]->seller_id = Auth::User ()->id;
									$ptlPincodeXsector[$i][$j]->is_active = 1;
									$ptlPincodeXsector[$i][$j]->lkp_service_id = Session::get('service_id');
									$ptlPincodeXsector[$i][$j]->ptl_pincode_id = $query->id;
									$ptlPincodeXsector[$i][$j]->district       = $query->districtname;
									$ptlPincodeXsector[$i][$j]->city           = $query->regionname;
									$ptlPincodeXsector[$i][$j]->location       = $query->postoffice_name.', '.$query->taluk;
									$ptlPincodeXsector[$i][$j]->state          = $query->statename;
									$ptlPincodeXsector[$i][$j]->postal_division= $query->divisionname;
									if (!empty($line[1])){
										
										$queryResult = DB::table ( 'ptl_sectors' )->where('sector_name',$line[1])->where('lkp_service_id',Session::get('service_id'))->where('seller_id',Auth::User ()->id)->where ( 'is_active', '1' )->first();
                                     if(empty($queryResult)){
                                     	$sectorError[$i] = "Please enter existing sector name at row " . $i . ".";
                                     
                                       }
									else{$ptlPincodeXsector[$i][$j]->ptl_sector_id = $queryResult->id;}
									}	
									if ( $line[2]=='0' || $line[2]=='1'){
                                                                                
										$ptlPincodeXsector[$i][$j]->oda = $line[2];
									}
									$j++;
								}
								
							}
						}
						$error[$i].=$sectorError[$i];//						
						$uniqueError = false;
							if($error[$i]==""){
								for($j=0;$j<count($ptlPincodeXsector[$i]);$j++){
									$isUniquePincode = PtlSellerComponent::checkUniquePincode ( $ptlPincodeXsector[$i][$j]->ptl_pincode_id, $ptlPincodeXsector[$i][$j]->ptl_sector_id);
									if ($isUniquePincode == '1') {
										
										if ($ptlPincodeXsector[$i][$j]->save()) {
											CommonComponent::auditLog($ptlPincodeXsector[$i][$j]->id, 'ptl_pincodexsectors');
										}
									}else{
										if($isUniquePincode != "" && $uniqueError == false){
											$uniqueError = true;
											$error[$i].=$isUniquePincode ." at row " . $i . ".";
										}
									
									}
								}
									
									
									 
								}
								$err.=$error[$i] ;
								$a = file($sellerPincodeDirectory . $_FILES['pincode_upload']['name']);
								$new[$i] = trim($a[$i]).",".$error[$i];
								$arrayError[]= explode(",",$new[$i]);
								
								//return redirect('equipmentRegister')->with('message', $error[$i]." fields are missing in Uploaded file.");
							
						} $find_header++;
					}
					
					$headerArray = array(array('PINCODE','SECTOR','ODA','ERROR'));
					$finalArray= array_merge($headerArray,$arrayError);
				
				$writeCsv = fopen($sellerPincodeDirectory . $_FILES['pincode_upload']['name'],'w');
				foreach ($finalArray as $fields) {
    			fputcsv($writeCsv,$fields,",");
				}
				
				fclose($writeCsv);
				
				
	
					if ($err !='') {
						return redirect('ptlmasters/pincode')->with('ptl_error_message', "Please download the csv file and upload after rectifying errors");
					} else {}
				}
				fclose($handle);


				return redirect('ptlmasters/pincode')->with('ptl_success_message', 'Upload file successfully.');
			} else {

				return view ( 'ptl.sellers.masters.pincode' );
			}

		} catch (Exception $ex) {

		}
	}
}
