<?php

namespace App\Components\Intracity;

use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use App\Components\CommonComponent;
use App\Components\Intracity\IntracityCheckoutComponent;
use App\Components\BuyerComponent;
use App\Models\User;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\IctVehicleWalletTransaction;
use Log;
use App\Models\IctBuyerQuoteItem;
use App\Components\SellerOrderComponent;
use App\Components\Search\BuyerSearchComponent;
use App\libraries\RestClient as RestClient;
// Intracity Models
use App\Models\IdeaApiTruckReqResponses;
use App\Models\IdeaApiSmstriggerReqResponses;

class IntracityBuyerComponent {
	
	/**
	 *
	 * Retrieval of all the data related to the specific buyer post / quote
	 *
	 *
	 * @param unknown $postId(buyer
	 *        	quote / post id)
	 * @param unknown $serviceId(service
	 *        	id)
	 * @param unknown $roleId(user's
	 *        	role id)
	 */
	
	public static function getBuyerPostDetails ($postId, $serviceId, $roleId ){
		
		$postDetails = DB::table ( 'ict_buyer_quote_items as bqi' )
		->leftJoin ( 'ict_buyer_quotes as bq', 'bqi.buyer_quote_id', '=', 'bq.id' )
		->leftJoin ( 'lkp_cities as lc', 'bqi.ict_lkp_city_id', '=', 'lc.id' )
		->leftJoin ( 'lkp_ict_locations as Fromloc', 'bqi.from_location_id', '=', 'Fromloc.id' )
		->leftJoin ( 'lkp_ict_locations as Toloc', 'bqi.to_location_id', '=', 'Toloc.id' )
		->leftJoin ( 'lkp_load_types as lt', 'bqi.lkp_load_type_id', '=', 'lt.id' )
		->leftJoin ( 'lkp_vehicle_types as vt', 'bqi.lkp_vehicle_type_id', '=', 'vt.id' )
		->leftJoin ( 'lkp_ict_weight_uom as uom', 'bqi.lkp_ict_weight_uom_id', '=', 'uom.id' )
		->leftJoin ( 'lkp_ict_rate_types as irt', 'bqi.lkp_ict_rate_type_id', '=', 'irt.id' )
		->leftJoin ( 'lkp_post_statuses as ps', 'bqi.lkp_post_status_id', '=', 'ps.id' )
		->where ( 'bqi.id', '=', $postId )
		->where ( 'bq.buyer_id', '=', Auth::User ()->id )
		->select ('bq.transaction_id as transaction_id','bqi.*','lc.city_name','Fromloc.ict_location_name as fromLocation',
				  'Toloc.ict_location_name as toLocation','lt.load_type','vt.vehicle_type',
				  'uom.weight_type','irt.rate_type','ps.post_status','irt.rate_type as rate_name')
		->first ();
		
		//getQuotes COunt per quote_item
		$quotesCount = DB::table ( 'ict_buyer_quote_sellers_quotes_prices as bsq' )
		->leftJoin ( 'ict_buyer_quote_items as bqi', 'bqi.id', '=', 'bsq.buyer_quote_item_id' )
		->where ( 'bsq.buyer_quote_item_id', $postId)
		->count();
		
		$sellerQuotes = DB::table('ict_buyer_quote_sellers_quotes_prices as sqp')
                ->leftJoin('ict_buyer_quote_items as bqi', 'sqp.buyer_quote_item_id', '=', 'bqi.id')
                ->leftJoin('ict_buyer_quotes as bq', 'bqi.buyer_quote_id', '=', 'bq.id')
                ->leftJoin('lkp_ict_vehicles as iv', 'sqp.lkp_ict_vehicle_id', '=', 'iv.id')
                ->where('bqi.id', '=', $postId)
                ->where('bq.buyer_id', '=', Auth::User()->id)
                ->select('bqi.lkp_post_status_id','sqp.id', 'sqp.seller_acceptence', 'sqp.initial_quote_price', 'sqp.counter_quote_price', 'sqp.buyer_quote_item_id', 'sqp.lkp_ict_vehicle_id', 'iv.vehicle_number', 'bq.buyer_id')
                ->get();
        for($i=0;$i<count($sellerQuotes);$i++){
            $order  =   DB::table('ict_buyer_quote_sellers_quotes_prices as sqp')
                    ->leftJoin('orders', function($join)
                         {
                            $join->on('orders.buyer_quote_item_id', '=', 'sqp.buyer_quote_item_id');
                            $join->on('orders.lkp_ict_vehicle_id', '=', 'sqp.lkp_ict_vehicle_id');
                             
                         })
                    //->leftJoin('orders', 'orders.buyer_quote_item_id', '=', 'sqp.buyer_quote_item_id')
                    ->where('sqp.id', '=', $sellerQuotes[$i]->id)
                    ->where('orders.buyer_id', '=', Auth::User()->id)
                    ->select('orders.id')->first();
            //echo "<pre>sdsad";print_r($order);//die();
        if(!empty($order) && $order->id!='')
            $sellerQuotes[$i]->order_id = $order->id;
        
        }
        
        $result = array();
        
        $result ['postDetails'] = $postDetails;
        $result ['sellerQuotes'] = $sellerQuotes;
        $result ['quotesCount'] = $quotesCount;
        //echo "<pre>";print_r($result);die();
        return $result;
    }
	// Intracity buyer search for seller posts result component
	public static function getIntraBuyerSearchList($roleId, $serviceId,$statusId='',$request) {
		try {//echo "<pre>";print_r($request);exit;
			// query to retrieve seller posts list and bind it to the grid--for filters
            $load_types = array (
					"" => "Load Type"
			);
			$vehicle_types = array (
					"" => "Vehicle Type"
			);
			$prices = array();
                        if(isset($request['load_type'])){
                            $request['lkp_load_type_id']=$request['load_type'];
                        }
                        if(isset($request['lkp_vehicle_id'])){
                            $request['lkp_vehicle_type_id']=$request['lkp_vehicle_id'];
                        }
                       
			if (isset ( $request ['price'] ) && $request ['price'] != '') {
				$splitprice = explode("    ",$request ['price']);
				$from = trim(filter_var($splitprice[0],FILTER_SANITIZE_NUMBER_INT),"-");
				$to = trim(filter_var($splitprice[1],FILTER_SANITIZE_NUMBER_INT),"-");
				$request['price_from'] = $from;
				$request['price_to'] = $to;
			}
			if (!isset ( $request ['price'] )) {
				if(!empty($prices)){
					$request['price_from'] = min($prices);
					$request['price_to'] = max($prices);
				}else{
					$request['price_from'] = 0;
					$request['price_to'] = 1000;
				}
			}
                        
                        $request['trackingfilter'] = array();
                        if (isset ( $request ['tracking'] ) && $request ['tracking'] != '') {
                                 $request['trackingfilter'][] = $request['tracking'];
                        }
                        if (isset ( $request ['tracking1'] ) && $request ['tracking1'] != '') {
                                $request['trackingfilter'][] = $request['tracking1'];
                        }
                        
			//BuyerComponent::saveSearchTerms($request,$serviceId);	
			// Below script for buyer search for seller posts join query --for Grid
			$gridBuyer =BuyerSearchComponent::search ( $roleId, $serviceId, $statusId, $request );
                        $filtergridbuyer = $gridBuyer;	
			$gridresults = $gridBuyer->get ();
                        
			if(empty($gridresults)){
				CommonComponent::searchTermsSendMail();
                                
                                Session::put('layered_filter', '');
                                Session::put('layered_filter_payments', '');
                                Session::put('show_layered_filter','');
			}
			// echo "<pre>";//print_r($gridBuyer);//exit;
			
                        if(!empty($gridresults)){
                        Session::put('show_layered_filter',1);
                        }
			// Below script for filter data getting from queries --for filters
			$sellerresults = $filtergridbuyer->get ();
			foreach ( $sellerresults as $seller_post_item ) {

				//foreach ( $seller_post_items as $seller_post_item ) {
					$prices[] = $seller_post_item->price;
					if (! isset ( $load_types [$seller_post_item->lkp_load_type_id] )) {
						$load_types [$seller_post_item->lkp_load_type_id] = DB::table ( 'lkp_load_types' )->where ( 'id', $seller_post_item->lkp_load_type_id )->pluck ( 'load_type' );
					}
					if (! isset ( $vehicle_types [$seller_post_item->lkp_vehicle_type_id] )) {
						$vehicle_types [$seller_post_item->lkp_vehicle_type_id] = DB::table ( 'lkp_vehicle_types' )->where ( 'id', $seller_post_item->lkp_vehicle_type_id )->pluck ( 'vehicle_type' );
					}
                                        if(isset($request['is_search'])){
                                            if (! isset ( $sellerNames [$seller_post_item->seller_id] )) {
                                                    $sellerNames[$seller_post_item->seller_id] = $seller_post_item->username;
                                            }
                                            Session::put('layered_filter', $sellerNames);
                                            if (! isset ( $paymentMethods [$seller_post_item->lkp_payment_mode_id] )) {
                                                    $paymentMethods[$seller_post_item->lkp_payment_mode_id] = $seller_post_item->paymentmethod;
                                            }
                                            Session::put('layered_filter_payments', $paymentMethods);
                                        }
				//}
			}
			
			
                        if(!empty($prices) && isset($request['is_search'])){
				$_REQUEST['price_from'] = 0;
				$_REQUEST['price_to'] = max($prices);
			}
                        
			$grid = DataGrid::source ( $gridBuyer );
                        //echo "<pre>";print_r($gridBuyer);exit;
			$grid->add ( 'id', 'ID', false )->style ( "display:none" );
			$grid->add ( 'vehicle_type', 'Vehicle Type', 'vehicle_type' )->attributes(array("class" => "col-md-4 padding-none"));
			$grid->add ( 'transitdays', 'Transit Type', 'transitdays' )->attributes(array("class" => "col-md-4 padding-none hidden-xs"));
			$grid->add ( 'price', 'Price', 'price' )->attributes(array("class" => "col-md-2 padding-none"));
			$grid->add ( 'test', 'Status', false )->style ( "display:none" );
			
				
                        //print_r($request);exit;
                        if(!isset($request['rate_type'])){
                            $request['rate_type']=Session::get('buyerSessionRateType');
                        }
			
                        $grid->add ( 'waiting_charges', 'Waiting Charges', 'waiting_charges' )->style ( "display:none" );
                        $grid->add ( 'overdimension_charges', 'Overdimension Charges', 'overdimension_charges' )->style ( "display:none" );
                        $grid->add ( 'labor_charges', 'Labor Charges', 'labor_charges' )->style ( "display:none" );
                        $grid->add ( 'lkp_ict_rate_type_id', 'Rate Type', 'lkp_ict_rate_type_id' )->style ( "display:none" );
                        //echo $request['rate_type'];exit;
                        if($request['rate_type']==2){
                            $grid->add ( 'minimum_hours', 'Minimum Hours', 'minimum_hours' )->style ( "display:none" );
                            $grid->add ( 'minimum_hour_charges', 'Minimum Hour Charges', 'minimum_hour_charges' )->style ( "display:none" );
                            
                        }elseif($request['rate_type']==3){
                            $grid->add ( 'minimum_kms', 'Minimum Kms', 'minimum_kms' )->style ( "display:none" );
                            $grid->add ( 'minimum_km_charges', 'Minimum Km Charges', 'minimum_km_charges' )->style ( "display:none" );
                            
                            
                        }
                        $grid->add ( 'action', '',false )->attributes(array("class" => "col-md-2 padding-none"));//echo $str1." ".$str2;exit;
			$grid->orderBy ( 'id', 'desc' );
			$grid->paginate ( 5 );
				
			$grid->row ( function ($row) {
                            //echo "<pre>";print_r($row);exit;
				$row->cells [0]->style ( 'display:none' );
				$row->cells [1]->style ( 'display:none' );
				$row->cells [2]->style ( 'display:none' );
				$row->cells [3]->style ( 'display:none' );
				$row->cells [4]->style ( 'width:100%' );
				$row->cells [5]->style ( 'display:none' );
				$row->cells [6]->style ( 'display:none' );
				$row->cells [7]->style ( 'display:none' );
				$row->cells [8]->style ( 'display:none' );
                                
				$id = $row->cells [0]->value;
				$vehicle_type = $row->cells [1]->value;
                                $transitdays = $row->cells [2]->value;
				$price = $row->cells [3]->value;
                                
				$waiting_charges = $row->cells [5]->value;
				$overdimension_charges = $row->cells [6]->value;
				$labor_charges = $row->cells [7]->value;
                                $str_extracharges       ="";
                                //echo $row->cells [8]->value;exit;
                                if($row->cells [8]->value==2){
                                    $str1="Minimum Hours";$str2="Minimum Hour Charges";
                                }elseif($row->cells [8]->value==3){
                                    $str1="Minimum Kms";$str2="Minimum Km Charges";
                                }
                                if(isset($row->cells [9]->value) && $row->cells [9]->value!=''){
                                    $row->cells [9]->style ( 'display:none' );
                                    $row->cells [10]->style ( 'display:none' );
                                    $min            = number_format($row->cells [9]->value,2);
                                    $min_charges    = number_format($row->cells [10]->value,2);
                                    $str_extracharges       ="<div class='col-md-12 col-sm- col-xs-12 padding-none'>
                                                    <div class='col-md-10 col-sm-8 col-xs-8 padding-none'>$str1 </div>
                                                    <div class='col-md-2 col-sm-4 col-xs-4 padding-none'>$min</div>
                                            </div><div class='col-md-12 col-sm- col-xs-12 padding-none'>
                                                    <div class='col-md-10 col-sm-8 col-xs-8 padding-none'>$str2 </div>
                                                    <div class='col-md-2 col-sm-4 col-xs-4 padding-none'>$min_charges</div>
                                            </div>";
                                }
                                
				if($transitdays==1)
                                    $transitdays.=" day";
                                else
                                    $transitdays.=" days";
				$waiting_charges        =   number_format($waiting_charges,2);
                                $overdimension_charges  =   number_format($overdimension_charges,2);
                                $labor_charges          =   number_format($labor_charges,2);
                                   
				$row->cells [4]->value  = "
				<div class='col-md-4 padding-none'>$vehicle_type</div>
                <div class='col-md-4 padding-none hidden-xs'>$transitdays</div>
				<div class='col-md-2 padding-none'>$price /-</div>
				<div class='col-md-2 padding-none text-right underline_link'>
				<div class='col-md-12 col-sm-12 col-xs-12 padding-none intra_booknow_buyer_form' id='intra_booknow_buyer_form'><span class='red underline_link intrabuyerbooknow_details booknow_buyer detailsslide-3' data-intrabooknow_list=$id  style='cursor:pointer'>
				<button class='btn red-btn pull-right'>Book Now</button></span></div>
				</div>
				<div class='clearfix'></div>
                                <span class='intrabuyerdetails_list  detailsslide-3 underline_link pull-right text-right' data-sellerlistid=$id style='cursor:pointer'><span class='show_details' style='display: inline;'>+</span>
                                    <span class='hide_details' style='display: none;'>-</span> Details</span>
					
				
				<div class='col-md-12 col-sm-12 col-xs-12 padding-none table-slide margin-top details-slide-drop intrabuyer_listdetails_$id' style='display: none;'>
                                    
                                    
                                    
                                    <div class='col-md-12 padding-none margin-top'>
													<div class='col-md-3 padding-left-none data-fld'>
														<span class='data-head'>Basic Freight Amount : $price</span>
													</div>
													<div class='col-md-3 padding-left-none data-fld'>
														<span class='data-head'>Waiting Charges / Hr : $waiting_charges</span>
													</div>
													<div class='col-md-3 padding-left-none data-fld'>
														<span class='data-head'>Over Dimension Charges : $overdimension_charges</span>
													</div>
													<div class='col-md-3 padding-none data-fld'>
														<span class='data-head'> Labor Charges / Person : $labor_charges </span>
													</div>
									
                                    </div>
                            </div>	
				
				
				<div>
				<input id='buyersearch_booknow_buyer_id_$id' type='hidden' value=".Auth::User()->id." name='buyersearch_booknow_buyer_id_$id' >
				<input id='buyersearch_booknow_seller_id_$id' type='hidden'  name='buyersearch_booknow_seller_id_$id'>
				<input id='buyersearch_booknow_seller_price_$id' type='hidden' value=".$price." name='buyersearch_booknow_seller_price_$id'>
                                <input id='buyersearch_booknow_offer_consignment_pickup_date_$id' type='hidden' value=".Session::get ( 'buyerSessionFromDate' )."  name='buyersearch_booknow_offer_consignment_pickup_date_$id'>
				<input id='buyersearch_booknow_offer_consignment_pickup_time_$id' type='hidden' value=".Session::get ( 'buyerSessionFromTime' )." name='buyersearch_booknow_offer_consignment_pickup_time_$id'>
				</div>
                                <div id='search-dialog_$id' data-bqid='$id' title='Confirmation Required' class='displayNone search-dialog'>
<div class='col-md-12'><h3 class='sub-head margin-none red margin-bottom'>Please confirm that you are booking for:</h3></div> 
<div class='clearfix'></div>
<div class='col-md-12 margin-top margin-bottom'>
					<div class='col-md-3 padding-left-none data-fld'>
						<span class='data-head'>From</span> <span class='data-value'>".Session::get ( 'buyerSessionFromLocationName' )."</span>
					</div>
					<div class='col-md-3 padding-left-none data-fld'>
						<span class='data-head'>To</span> <span class='data-value'>".Session::get ( 'buyerSessionToLocationName' )."</span>
					</div>
					<div class='col-md-3 padding-left-none data-fld'>
						<span class='data-head'>Date</span> <span class='data-value'>".Session::get ( 'buyerSessionFromDate' )."</span>
					</div>
					<div class='col-md-3 padding-left-none data-fld'>
						<span class='data-head'>Time</span> <span class='data-value'>".date('h.i a', strtotime(Session::get ( 'buyerSessionFromTime' )))."</span>
					</div>
				</div></div>​";
				$row->attributes(array("class" => "col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none table-row"));
			} );
				
			// filter for buyear search list top dropdown lists---filters
			$filter = DataFilter::source ( $filtergridbuyer );
			//$filter->add ( 'sqi.lkp_city_id', 'City', 'select' )->options ( $cities )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );	
			//$filter->add ( 'sqi.from_location_id', 'From Location', 'select' )->options ( $from_locations )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
			//$filter->add ( 'sqi.to_location_id', 'From Location', 'select' )->options ( $to_locations )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
			$filter->add ( 'sqi.lkp_vehicle_type_id', 'Vehicle Type', 'select' )->options ( $vehicle_types )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
			$filter->add ( 'sqi.lkp_load_type_id', 'Load Type', 'select' )->options ( $load_types )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
				
			$filter->submit ( 'search' );
			$filter->reset ( 'reset' );
			$filter->build ();
				
			$result = array ();
			$result ['gridBuyer'] = $grid;
			$result ['filter'] = $filter;
			return $result;
				
			// return $gridBuyer;
		} catch ( Exception $exc ) {
		}
	}

	/**
	 *
	 * Intracity Buyer Post List
	 *
	 * @param unknown $order_type(buyer
	 *        	quote / post id)
	 * @param unknown $service_id(service
	 *        	id)
	 * @param unknown $roleId(user's
	 *        	role id)
	 * @param unknown $order_status(user's
	 *        	role id)
	 *        	
	 */
	public static function getIntracityBuyerPostLists($service_id, $roleId,$post_status) {
		
		// Filters values to populate in the page
		$city_name = array (
				"" => "City" 
		);
		$load_type = array (
				"" => "Load Type" 
		);
		$vehicle_type = array (
				"" => "Vehicle Type" 
		);
		//$post_status = array ("" => "Status");
		
		$from_locations = array (
				"" => "From Location" 
		);
		$to_locations = array (
				"" => "To Location" 
		);
		$from_date = '';
		$to_date = '';
		
		$query = DB::table ( 'ict_buyer_quote_items as bqi' );
		$query->leftJoin ( 'ict_buyer_quotes as bq', 'bqi.buyer_quote_id', '=', 'bq.id' );
		$query->leftJoin ( 'lkp_cities as lc', 'bqi.ict_lkp_city_id', '=', 'lc.id' );
		$query->leftJoin ( 'lkp_ict_locations as Fromloc', 'bqi.from_location_id', '=', 'Fromloc.id' );
		$query->leftJoin ( 'lkp_ict_locations as Toloc', 'bqi.to_location_id', '=', 'Toloc.id' );
		$query->leftJoin ( 'lkp_load_types as lt', 'bqi.lkp_load_type_id', '=', 'lt.id' );
		$query->leftJoin ( 'lkp_vehicle_types as vt', 'bqi.lkp_vehicle_type_id', '=', 'vt.id' );
		$query->leftJoin ( 'lkp_ict_weight_uom as uom', 'bqi.lkp_ict_weight_uom_id', '=', 'uom.id' );
		$query->leftJoin ( 'lkp_ict_rate_types as irt', 'bqi.lkp_ict_rate_type_id', '=', 'irt.id' );
		$query->leftJoin ( 'lkp_post_statuses as ps', 'bqi.lkp_post_status_id', '=', 'ps.id' );
		$query->where ( 'bq.buyer_id', '=', Auth::User ()->id );
		$query->where('bqi.lkp_post_status_id','!=',8);
		$query->where('bqi.lkp_post_status_id','!=',7);
		$query->where('bqi.lkp_post_status_id','!=',6);
		
		
		// conditions to make search
		
		if (isset ( $service_id ) && $service_id != '') {
			$query->where ( 'bq.lkp_service_id', '=', $service_id );
		}
        if (isset ( $post_status ) && !empty($post_status)) {
        	if($post_status==0)
        		$query->whereIn ( 'bqi.lkp_post_status_id', array(1,2,3,4,5) );
        	else	
				$query->where ( 'bqi.lkp_post_status_id', '=', $post_status );
		}
		
		if (isset ( $_GET ['start_pickup_date'] ) && $_GET ['start_pickup_date'] != '') {
			
			$pickupFromDate = CommonComponent::convertDateForDatabase( $_GET ['start_pickup_date'] );
			$query->where ( 'bqi.pickup_date', '>=', $pickupFromDate);
			$from_date = $pickupFromDate;
		}
                if (isset ( $_GET ['end_pickup_date'] ) && $_GET ['end_pickup_date'] != '') {
			
			$pickupToDate = CommonComponent::convertDateForDatabase( $_GET ['end_pickup_date'] );
			
			$query->where ( 'bqi.pickup_date', '<=',$pickupToDate );
			$to_date = $pickupToDate;
		}
		//echo $pickupFromDate;die();
		$postResults = $query->select ( 'bqi.*', 'lc.city_name', 'Fromloc.ict_location_name as fromLocation', 'Toloc.ict_location_name as toLocation', 'lt.load_type', 'vt.vehicle_type', 'uom.weight_type', 'irt.rate_type', 'ps.post_status' )->get ();
		
		//echo "<pre>";print_r($postResults);die();
		// Functionality to handle filters based on the selection starts
		if ($postResults) {
			foreach ( $postResults as $postList ) {
				
				if (! isset ( $city_name [$postList->ict_lkp_city_id] )) {
					
					$city_name [$postList->ict_lkp_city_id] = DB::table ( 'lkp_cities' )->where ( 'id', $postList->ict_lkp_city_id )->pluck ( 'city_name' );
				}
				if (! isset ( $from_locations [$postList->from_location_id] ) && $postList->from_location_id!=0) {
					
					$from_locations [$postList->from_location_id] = DB::table ( 'lkp_ict_locations' )->where ( 'id', $postList->from_location_id )->pluck ( 'ict_location_name' );
				}
				if (! isset ( $post_status [$postList->lkp_post_status_id] ) && $postList->lkp_post_status_id!=0) {
						
					$post_status [$postList->lkp_post_status_id] = DB::table ( 'lkp_post_statuses' )->where ( 'id', $postList->lkp_post_status_id )->pluck ( 'post_status' );
				}
				if (! isset ( $to_locations [$postList->to_location_id] ) && $postList->to_location_id!=0) {
					
					$to_locations [$postList->to_location_id] = DB::table ( 'lkp_ict_locations' )->where ( 'id', $postList->to_location_id )->pluck ( 'ict_location_name' );
				}
				if (! isset ( $vehicle_type [$postList->lkp_vehicle_type_id] )) {
					$vehicle_type [$postList->lkp_vehicle_type_id] = DB::table ( 'lkp_vehicle_types' )->where ( 'id', $postList->lkp_vehicle_type_id )->pluck ( 'vehicle_type' );
				}
				if (! isset ( $load_type [$postList->lkp_load_type_id] )) {
					$load_type [$postList->lkp_load_type_id] = DB::table ( 'lkp_load_types' )->where ( 'id', $postList->lkp_load_type_id )->pluck ( 'load_type' );
				}
			}
		}
		$city_name = CommonComponent::orderArray($city_name);
		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations = CommonComponent::orderArray($to_locations);
		$vehicle_type = CommonComponent::orderArray($vehicle_type);
		$load_type = CommonComponent::orderArray($load_type);
		
		// Functionality to handle filters based on the selection ends
		
		$grid = DataGrid::source ( $query );
		
		$grid->add ( 'id', 'ID', false )->style ( 'display:none' );
		
		$grid->add ( 'pickup_date', 'Pickup Date', 'pickup_date' )->attributes ( array (
				"class" => "col-md-2 col-sm-2 col-xs-4 padding-none text-left" 
		) );
		
		$grid->add ( 'load_type', 'Load Type', 'load_type' )->attributes ( array (
				"class" => "col-md-2 col-sm-2 col-xs-4 padding-none text-left" 
		) );
		$grid->add ( 'units', 'weight', 'units' )->attributes ( array (
				"class" => "col-md-1 col-sm-1 col-xs-2 padding-none text-left" 
		) );
		$grid->add ( 'vehicle_type', 'Vehicle Type', 'vehicle_type' )->attributes ( array (
				"class" => "col-md-2 col-sm-2 col-xs-4 hidden-xs padding-left-none mobile-padding-none text-left" 
		) );
		$grid->add ( 'fromLocation', 'From', 'fromLocation' )->attributes ( array (
				"class" => "col-md-2 col-sm-2 col-xs-2 hidden-xs padding-none text-left" 
		) );
		$grid->add ( 'toLocation', 'To', 'toLocation' )->attributes ( array (
				"class" => "col-md-2 col-sm-2 col-xs-2 hidden-xs padding-none text-left" 
		) );
		$grid->add ( 'post_status', 'Status', 'post_status' )->attributes ( array (
				"class" => "col-md-1 col-sm-1 col-xs-2 padding-none status-column" 
		) );
		$grid->add ( 'a', '', '' );
		
		$grid->orderBy ( 'bqi.id', 'desc' );
		$grid->paginate( 5 );
		
		$grid->row ( function ($row) {
			$post_id = $row->cells [0]->value;
                        $data_link = url()."/getbuyercounteroffer/$post_id";
			//$row->cells [0]->value = '<a href=/getbuyercounteroffer/' . $post_id . '>';
			$row->cells [0]->style ( 'display:none' );
                        $dispatchDate   =   $row->cells [1]->value ;
                        $row->cells [1]->value = '<span><input type="checkbox" name="buyerpostcheck" id="buyerpostcheck" class="checkBoxClass gridbuyercheckbox" value='.$post_id.'></span><span class="lbl padding-8"></span>'.CommonComponent::checkAndGetDate($dispatchDate);
			$row->cells [1]->attributes ( array (
					"class" => "col-md-2 padding-left-none html_link" , "data_link"=>$data_link
			) );
			$row->cells [2]->attributes ( array (
					"class" => "col-md-2 padding-left-none text-left html_link" , "data_link"=>$data_link
			) );
			$row->cells [3]->attributes ( array (
					"class" => "col-md-1 padding-none text-left mobile-text-center html_link" , "data_link"=>$data_link
			) );
			$row->cells [4]->attributes ( array (
					"class" => "col-md-2 hidden-xs padding-left-none mobile-padding-none text-left html_link" , "data_link"=>$data_link
			) );
			$row->cells [5]->attributes ( array (
					"class" => "col-md-2 hidden-xs padding-none text-left html_link" , "data_link"=>$data_link
			) );
			$row->cells [6]->attributes ( array (
					"class" => "col-md-2 hidden-xs padding-none text-left html_link" , "data_link"=>$data_link
			) );
			$row->cells [7]->attributes ( array (
					"class" => "col-md-1 padding-none  html_link" , "data_link"=>$data_link
			) );
			
			$row->cells [8]->attributes ( array (
					"class" => "col-md-12 col-sm-12 col-xs-12 padding-none padding-none" 
			) );
			
			
			
			//$row->cells [1]->value = '' . commonComponent::convertMysqlDate ( $row->cells [1]->value ) . '';
			//getQuotes COunt per quote_item
			$quotesCount = DB::table ( 'ict_buyer_quote_sellers_quotes_prices as bsq' )
			->leftJoin ( 'ict_buyer_quote_items as bqi', 'bqi.id', '=', 'bsq.buyer_quote_item_id' )
			->where ( 'bsq.buyer_quote_item_id', $post_id)
			->count();
				
			$row->cells [8]->value .= '<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-top">
								<div class="col-xs-12 col-sm-12 col-xs-12 padding-none">
									<div class="col-md-12 padding-none count-block count-block2">
										
										<div class="pull-left">
										<div class="padding-none text-center displayNone"><a href="#">
												<div class="margin-center">
													<i class="fa fa-envelope"></i> <span
														class="red superscript-table">0</span>
												</div> Messages
											</a>
										</div>
					
					
											<div class="info-links">
												<a href=/getbuyercounteroffer/'.$post_id.'><i class="fa fa-file-text-o"></i> Quotes<span class="badge superscript-table">'.$quotesCount.'</span></a>
												
											</div>
										
										<div class="padding-none text-center displayNone"><a>
												<div class="margin-center">
													<i class="fa fa-bullseye"></i> <span
														class="red superscript-table">0</span>
												</div> Lead
											</a>
										</div>
														<div class="padding-none text-center displayNone"><a>
												<div class="margin-center">
													<i class="fa fa-bar-chart-o"></i> <span
														class="red superscript-table"></span>
												</div> Market Analytics
											</a>
										</div>
										
										<div class="padding-none text-center displayNone"><a href="#">
												<div class="margin-center">
													<span
														class="red superscript-table">0</span>
												</div> Views
											</a>
										</div>
														
										</div>
										
														
														
														
										<div class="pull-right text-right">
											 <div class="info-links">
												<span class="views red"><i class="fa fa-eye" title="Views"></i> 0</span>';
					if ($row->cells [7]->value == 'Open') {
						$row->cells [8]->value .= '	<a href="#"	onclick="buyerpostcancel('.$post_id.')"><i class="fa fa-trash buyerpostdelete" title="Delete"></i></a>';
					}
					$row->cells [8]->value .= '	</div>
										</div>
											

										
									</div>
			 						

								</div>

							</div>';
			
			// $row->cells [8]->value .= '<a href="#">Message</a><div class="clearfix"></div></div>';
			
			$row->attributes ( array (
					"class" => "col-md-12 col-sm-12 col-xs-12 padding-left-none padding-none table-row" 
			) );
			// $grid->add('title','Title', true)->style("width:100px");
		} );
		// print_r($from_locations);die();
		// Functionality to build filters in the page starts
		$filter = DataFilter::source ( $query );
		$filter->add ( 'bqi.ict_lkp_city_id', '', 'select' )->options ( $city_name )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'bqi.from_location_id', '', 'select' )->options ( $from_locations )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'bqi.to_location_id', '', 'select' )->options ( $to_locations )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'bqi.lkp_vehicle_type_id', '', 'select' )->options ( $vehicle_type )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'bqi.lkp_post_status_id', '', 'select' )->options ( $post_status )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'bqi.lkp_load_type_id', '', 'select' )->options ( $load_type )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
		//$filter->add ( 'bqi.pickup_date', 'From', 'date' )->attr ( "class", "dateRange dateRangeFrom" );
		//$filter->add ( 'bqi.end_pickup_date', 'To', 'date' )->attr ( "class", "dateRange dateRangeTo" );
		
		$filter->submit ( 'search' );
		
		$filter->reset ( 'reset' );
		$filter->build ();
		// Functionality to build filters in the page ends
		
		$result = array ();
		$result ['grid'] = $grid;
		$result ['filter'] = $filter;
		return $result;
	}


	/**
    * get buyer counter offer page
    * Insert values for booknow
    * @param Request $request
    * @return type
    */
    public static function setBuyerBooknowForFtl($input)
    {//print_r($input);exit;
    	Log::info('Insert the buyer booknow data for ftl: '.Auth::id(),array('c'=>'2'));
        try {
            $ordid  =   CommonComponent::getOrderID();
            $serviceId = Session::get('service_id');
            $user_mobile_no  =   CommonComponent::getMobleNumber(Auth::id());

            if(isset($input['vehicleId']))
            $vehicles  = DB::table('lkp_ict_vehicles')->where('id',$input['vehicleId'])->first();
            if((isset($input['vehicleId']) && $vehicles->wallet_net_amount>=200)|| !isset($input['vehicleId'])){
            $roleId = Auth::User()->lkp_role_id;
    		if($roleId == BUYER){
    			CommonComponent::activityLog("BUYER_INSERTED_ADDTOCART",
    					BUYER_INSERTED_ADDTOCART,0,
    					HTTP_REFERRER,CURRENT_URL);
    		}
             
                $created_year = date('Y');
                
                $randString = 'INTRA/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                $orderPayment = new OrderPayment();
                $orderPayment->order_payment_no = $randString;
                $orderPayment->lkp_payment_mode_id = CASH_ON_DELIVERY;
                $orderPayment->lkp_payment_method_id = CASH_ON_DELIVERY_METHOD;
                $OrderServiceTaxes = IntracityCheckoutComponent::getOrderServiceTax($input['price'], $serviceId);
                $orderPayment->base_amount_paid = $OrderServiceTaxes['order_total_amount'];
                $orderPayment->amount_authorized = '1';
                $orderPayment->additional_data = 'Success';
                $orderPayment->transaction_id = 'some transaction id from gateway';
                $orderPayment->payment_response = "some response from gateway";
                $created_at = date('Y-m-d H:i:s');
                $createdIp = $_SERVER['REMOTE_ADDR'];
                $orderPayment->created_by = Auth::id();
                $orderPayment->created_at = $created_at;
                $orderPayment->created_ip = $createdIp;

                if ($orderPayment->save()) {
                    CommonComponent::auditLog($orderPayment->id, 'order_payments');
                    //$randOrderString = 'INTRAO' . $orderPayment->id . rand(10000,99999);
                    $ordersDetails = new Order();
                    $ordersDetails->order_no = $randString;
                    $ordersDetails->lkp_order_type_id = SPOTORDER;
                    $ordersDetails->order_payment_id = $orderPayment->id;
                    //$ordersDetails->order_invoice_id = '1';
                    $ordersDetails->buyer_id = $input['buyerId'];
                    $ordersDetails->seller_id = 0;
                    
                    $city_id = '1263';
                    $rate_type_id = '1';
                    
                    if (!empty($input['quoteItemId']) && $input['quoteItemId'] != 0 && $input['quoteItemId'] != '') {
                        $buyerQuoteItemData = BuyerComponent::getBuyerQuoteItemData($input['quoteItemId']);
                        
                        $fromCityId = $buyerQuoteItemData[0]->from_location_id;
                        $toCityId = $buyerQuoteItemData[0]->to_location_id;
                        $ordersDetails->dispatch_date = $buyerQuoteItemData[0]->pickup_date;
                        $loadTypeId = $buyerQuoteItemData[0]->lkp_load_type_id;
                        $vehicleTypeId = $buyerQuoteItemData[0]->lkp_vehicle_type_id;
                        $units = $buyerQuoteItemData[0]->units;
                        $ordersDetails->delivery_date = $buyerQuoteItemData[0]->delivery_date;
                        $ordersDetails->lkp_service_id = $buyerQuoteItemData[0]->lkp_service_id;
                        $ordersDetails->lkp_lead_type_id = $buyerQuoteItemData[0]->lkp_lead_type_id;
                        $ordersDetails->lkp_quote_access_id = $buyerQuoteItemData[0]->lkp_lead_type_id;
                        $city_id = $buyerQuoteItemData[0]->ict_lkp_city_id;
                        $rate_type_id = $buyerQuoteItemData[0]->lkp_ict_rate_type_id;
                        $ordersDetails->number_loads = $buyerQuoteItemData[0]->number_loads;
                        $ordersDetails->quantity = $buyerQuoteItemData[0]->quantity;
                        if(isset($input['postItemId'])){
                            $ordersDetails->lkp_order_status_id = '1';
                            $ordersDetails->seller_post_item_id=$input['postItemId'];
                        }else{
                            $ordersDetails->lkp_order_status_id = '2';
                            $ordersDetails->seller_post_item_id=0;
                        }
                        $ordersDetails->buyer_quote_item_id = $input['quoteItemId'];
                         
                            
                        $updatedAt = date ( 'Y-m-d H:i:s' );
                        $updatedIp = $_SERVER ["REMOTE_ADDR"];
                        if(isset($input['vehicleId']))    {
                            IctBuyerQuoteItem::where ( "buyer_quote_id", $input['quoteItemId'] )->update ( array (
                            'lkp_post_status_id' => BOOKED,
                            'updated_at' => $updatedAt,
                            'updated_by' => Auth::User ()->id,
                            'updated_ip' => $updatedIp
                            ));
                        }
                    } else {                    	
                        $sellerPostDetails = IntracityCheckoutComponent::getSellerPostDetails($input['postItemId']);                       
                        $fromCityId = $sellerPostDetails[0]->from_location_id;
                        $toCityId = $sellerPostDetails[0]->to_location_id;
                        $loadTypeId = $sellerPostDetails[0]->lkp_load_type_id;
                        $vehicleTypeId = $sellerPostDetails[0]->lkp_vehicle_type_id;
                        $units = $sellerPostDetails[0]->units;
                        $city_id = $sellerPostDetails[0]->lkp_city_id;
                        $rate_type_id = $sellerPostDetails[0]->lkp_ict_rate_type_id;
                        $ordersDetails->lkp_service_id =Session::get('service_id');
                        $ordersDetails->lkp_order_status_id = '1';
                        $ordersDetails->seller_post_item_id=$input['postItemId'];
                        $ordersDetails->buyer_quote_item_id =0;
                    }
                    $ordersDetails->from_city_id = $fromCityId;
                    $ordersDetails->to_city_id = $toCityId;
                    $ordersDetails->lkp_load_type_id = $loadTypeId;
                    $ordersDetails->lkp_vehicle_type_id = $vehicleTypeId;
                    $ordersDetails->units = $units;
                    $ordersDetails->price = $input['price'];
                    $ordersDetails->buyer_consignment_pick_up_date = CommonComponent::convertDateTimeForDatabase($input['consignmentPickupDate'], $input['consignmentPickupTime']);
                    $ordersDetails->buyer_consignment_value = '';
                    
                    
                    if(isset($input['vehicleId']))
                    $ordersDetails->lkp_ict_vehicle_id = $input['vehicleId'];
                    
                    //$ordersDetails->lkp_order_status_id = '2';
                    $created_at = date('Y-m-d H:i:s');
                    $createdIp = $_SERVER['REMOTE_ADDR'];
                    $ordersDetails->created_by = Auth::id();
                    $ordersDetails->created_at = $created_at;
                    $ordersDetails->created_ip = $createdIp;
                    $vehicle_mobile_no = '';
                    
                    if ($ordersDetails->save()) {
                        CommonComponent::auditLog($ordersDetails->id, 'orders');
                        
                        //sms for remaining seller who has sent sms
                        $sellerQuotes = DB::table('ict_buyer_quote_sellers_quotes_prices as sqp')
                        ->leftJoin('ict_buyer_quote_items as bqi', 'sqp.buyer_quote_item_id', '=', 'bqi.id')
                        ->leftJoin('ict_buyer_quotes as bq', 'bqi.buyer_quote_id', '=', 'bq.id')
                        ->leftJoin('lkp_ict_vehicles as iv', 'sqp.lkp_ict_vehicle_id', '=', 'iv.id')
                        ->where('bqi.id', '=', $input['quoteItemId'])
                        ->where('bq.buyer_id', '=', Auth::User()->id)
                        ->select('bq.transaction_id' ,  'sqp.lkp_ict_vehicle_id','iv.vehicle_number', 'iv.mobile_number')
                        ->get();
                        if(isset($input['vehicleId'])){
                            $vehicleId  =$input['vehicleId'];
                            for($i=0;$i<=count($sellerQuotes);$i++){
                                if($vehicleId==$sellerQuotes[$i]->lkp_ict_vehicle_id){
                                    $vehiclenumber  =$sellerQuotes[$i]->vehicle_number;
                                    unset($sellerQuotes[$i]);
                                }
                            }//echo '<pre>';print_r($sellerQuotes);exit;
                            foreach($sellerQuotes as $sellerQuote){
                                $getMobileNumber    =   array($sellerQuote->mobile_number);
                                $msg_params = array(
                                                'randnumber' => $sellerQuote->transaction_id
                                            );
                                CommonComponent::sendSMS($getMobileNumber,INTRACITY_BOOKED_POST_ACKOWLEDGEMENT_SMS,$msg_params);
                            }
                        }
                        //invoice generation
                        //SellerOrderComponent::addInvoice($ordersDetails->id);
                        if (isset($input['vehicleId'])) {
                            DB::update("UPDATE lkp_ict_vehicles SET wallet_net_amount = wallet_net_amount-200 WHERE lkp_ict_vehicles.id ='" . $input['vehicleId'] . "'");
                            $amount = DB::table('lkp_ict_vehicles')
                                    ->where('lkp_ict_vehicles.id', $input['vehicleId'])
                                    ->select('lkp_ict_vehicles.wallet_net_amount','lkp_ict_vehicles.mobile_number')
                                    ->first();
                            //wallet transactions table insertion
                            $wallet = new IctVehicleWalletTransaction();
                            $wallet->order_id = $ordersDetails->id;
                            $wallet->lkp_ict_vehicle_id = $input['vehicleId'];
                            $wallet->wallet_net_amount = $amount->wallet_net_amount;
                            $vehicle_mobile_no = $amount->mobile_number;
                            $wallet->transaction_amount = $ordersDetails->price;
                            $wallet->buyer_id = $input['buyerId'];
                            $wallet->created_at = $created_at;
                            $wallet->save();
                            
                            // Mail functionality to send email to buyer
                            $userData = DB::table ( 'users' )
                            ->leftJoin ( 'orders', 'orders.buyer_id', '=', 'users.id' )
                            ->leftJoin ( 'lkp_ict_vehicles as liv', 'orders.lkp_ict_vehicle_id', '=', 'liv.id' )
                            ->where ( 'users.id', $input['buyerId'])
                            ->where('orders.id',$ordersDetails->id)
                            ->select ( 'orders.*','orders.order_no as Order_No', 'users.email', 'users.username', 'liv.vehicle_number' )
                            ->get ();
                            if ($userData [0]->email) {
                                    CommonComponent::send_email ( INTRACITY_BUYER_ORDER_CONFIRMATION_MAIL, $userData );
                            }
                        }
                       
                    }
                if(isset($input['vehicleId']))    {
                    $seller_msg ="Dear Partner,
You have been assigned ".$ordersDetails->order_no.". Please contact ".Auth::User()->username." on ".$user_mobile_no[0].".
Logistiks.com";
                    $buyer_sms="Your order ".$ordersDetails->order_no." has been assigned to ".$userData[0]->vehicle_number.". Driver will contact you from ".$vehicle_mobile_no.".
Logistiks.com";
                	$params = '<?xml version="1.0" encoding="UTF-8"?>
	                <Request>
	                <SMSData>
	                  <Request_Ref_ID>ORDER_'.$ordersDetails->id.'</Request_Ref_ID>
	                  <URL>'.CommonComponent::getFrapiUrlByServerUrl().'</URL>
	                  <Requested_time>'.DATE("Hi", strtotime (date('H:i')) ).'</Requested_time>
	                  <Driver_CLI>'.$vehicle_mobile_no.'</Driver_CLI>
	                  <Driver_SMS>'.$seller_msg.'</Driver_SMS>
	                  <Customer_CLI>'.$user_mobile_no[0].'</Customer_CLI>
	                  <Customer_SMS>'.$buyer_sms.'</Customer_SMS>
	                </SMSData>
	                </Request>';
	
				//echo $params;
				//exit;						
	 				
	                $models = RestClient::post("https://ibs.ideacellular.com/Logistiks/PIService.svc/ReqSMStrans?", $params); 
	                $response = $models->getResponse();
	                //echo '<div class="response" id="response" style="font-weight:bold ">Web Service Response</div><pre name="code" class="js">'.$response.'</pre>';
	                    //exit; 
		
					//Save Idea Request 
					$createdAt = date ( 'Y-m-d H:i:s' );
					$createdIp = $_SERVER ["REMOTE_ADDR"];

					$ideaRequest = new IdeaApiSmstriggerReqResponses();
					$ideaRequest->reference_id = $ordersDetails->id;
					$ideaRequest->request_type = 1;
					$ideaRequest->idea_request_xml = $params;	
					$ideaRequest->created_at = $createdAt;
					$ideaRequest->created_ip = $createdIp;
					$ideaRequest->created_by = Auth::User ()->id;
					$ideaRequest->save();

                    return array('success' => 1, 'message' => "Order is placed successfully.");
                }
                else { 
					$conDate = str_replace ( '/', '-', $input['consignmentPickupDate']);
                	$params = '<?xml version="1.0" encoding="UTF-8"?>
									<Request>
									<Calldata>
									<Request_Ref_ID>ORDER_'.$ordersDetails->id.'</Request_Ref_ID>
									<URL>'.CommonComponent::getFrapiUrlByServerUrl().'</URL>
									<Request_type>1</Request_type>
									<From_Loc_id>'.$fromCityId.'</From_Loc_id>
									<To_Loc_id>'.$toCityId.'</To_Loc_id>  
									<Req_Date>'.DATE ( "dmY", strtotime ( $conDate ) ).'</Req_Date>
									<Req_Time>'.DATE("Hi", strtotime ($input['consignmentPickupTime']) ).'</Req_Time>
									<Truck_Type>'.$vehicleTypeId.'</Truck_Type>
									<Load_Type>'.$loadTypeId.'</Load_Type>
									<From_Loc_File_Name>COCHIN.wav</From_Loc_File_Name>
									<To_Loc_File_Name>BANGLORE.wav</To_Loc_File_Name>
									<Price>'.$input['price'].'</Price>
									<City>'.$city_id.'</City>
									<Trip_Type>'.$rate_type_id.'</Trip_Type>
									</Calldata>
									</Request>';

					//echo $params;
					//exit;						
					$models = RestClient::post("https://ibs.ideacellular.com/Logistiks/PIService.svc/Reqfortruck?", $params); 
					$response = $models->getResponse();

					//Save Idea Request 
					$createdAt = date ( 'Y-m-d H:i:s' );
					$createdIp = $_SERVER ["REMOTE_ADDR"];

					$ideaRequest = new IdeaApiTruckReqResponses();
					$ideaRequest->reference_id = $ordersDetails->id;
					$ideaRequest->request_type = 1;
					$ideaRequest->idea_request_xml = $params;
					$ideaRequest->created_at = $createdAt;
					$ideaRequest->created_ip = $createdIp;
					$ideaRequest->created_by = Auth::User ()->id;
					$ideaRequest->save();

                    //echo '<div class="response" id="response" style="font-weight:bold ">Web Service Response</div><pre name="code" class="js">'.$response.'</pre>';
                    //exit; 
 
                    return array('success' => 1, 'message' => "Thanks for placing the order with us, You will receive the order confirmation and driver details shortly.");
                	}
                }
            }else{
                return array('success' => 1, 'message' => INSUFFICIENT_WALLET);
            }
           
            //Save data into txnprojectinviteerequests
        } catch (Exception $e) {

        }
    }
	
}
