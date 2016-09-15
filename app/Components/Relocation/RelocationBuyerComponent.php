<?php

namespace App\Components\Relocation;

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
use App\Models\PtlSearchTerm;
use App\Components\SellerComponent;
use App\Components\Search\BuyerSearchComponent;
use App\Models\PtlZone;
use App\Models\PtlTier;
use App\Models\PtlTransitday;
use App\Models\PtlSector;
use App\Models\PtlPincodexsector;
use App\Components\MessagesComponent;
use App\Components\Ptl\PtlBuyerGetQuoteBooknowComponent;
class RelocationBuyerComponent {
	
	
	public static function getRelocationBuyerPostsList($service_id, $post_status, $enquiry_type) {
	
		
		// Filters values to populate in the page
		$from_locations = array (
				"" => "From Location"
		);
		$to_locations = array (
				"" => "To Location"
		);
		$vehicle_types = array (
				"" => "Vehicle Type"
		);
		$load_types = array (
				"" => "Load Type"
		);
		$from_date = '';
		$to_date = '';
		$order_no = '';
	
		// query to retrieve buyer posts list and bind it to the grid
		$Query = DB::table ( 'relocation_buyer_posts as rbs' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rbs.lkp_post_status_id' );
		$Query->leftjoin ( 'lkp_quote_accesses as qa', 'qa.id', '=', 'rbs.lkp_quote_access_id' );
		$Query->leftjoin ( 'lkp_cities as cf', 'rbs.from_location_id', '=', 'cf.id' );
		$Query->leftjoin ( 'lkp_cities as ct', 'rbs.to_location_id', '=', 'ct.id' );
		$Query->leftjoin ( 'relocation_buyer_inventory_items as rbi', 'rbs.id', '=', 'rbi.buyer_post_id' );
		$Query->where( 'rbs.created_by', Auth::User ()->id );
		$Query->where('rbs.lkp_post_status_id','!=',6);
		$Query->where('rbs.lkp_post_status_id','!=',7);
		$Query->where('rbs.lkp_post_status_id','!=',8);		
		
		// conditions to make search	
		/*
		 * if (isset ( $enquiry_type ) && $enquiry_type != '') {
			* $query->where ( 'orders.lkp_enquiry_type_id', '=', $enquiry_type );
			* }
		*/	
		if (isset ( $post_status ) && $post_status != '') {
			if($post_status == 0)
				$Query->whereIn ( 'rbs.lkp_post_status_id', array(1,2,3,4,5));
			else
				$Query->where ( 'rbs.lkp_post_status_id', '=', $post_status );
		}
		
		if (isset ( $_GET ['dispatch_date'] ) && $_GET ['dispatch_date'] != '') {
			$commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['dispatch_date']);
			$Query->where ( 'rbs.dispatch_date', '>=', $commonDispatchDate );
			$from_date = $commonDispatchDate;
			//echo "From Date :"; echo $from_date;die();
		}
		if (isset ( $_GET ['delivery_date'] ) && $_GET ['delivery_date'] != '') {
			$commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['delivery_date']);
			$Query->where ( 'rbs.delivery_date', '<=', $commonDeliveryhDate);
			$to_date = $commonDeliveryhDate;
			//echo "To Date :"; echo $to_date;die();
		}
	
		$postResults = $Query->select ( 'rbs.*', 'ps.post_status', 'ct.city_name as toCity', 'cf.city_name as fromCity','qa.quote_access')->get ();
		//echo "<pre>"; print_r($postResults);die();
		// Functionality to handle filters based on the selection starts
		foreach ( $postResults as $post ) {
			
				if (! isset ( $from_locations [$post->from_location_id] )) {
					$from_locations [$post->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $post->from_location_id)->pluck ( 'city_name' );
				}
				if (! isset ( $to_locations [$post->to_location_id] )) {
					$to_locations [$post->to_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $post->to_location_id )->pluck ( 'city_name' );
				}
		}
		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations = CommonComponent::orderArray($to_locations);
		
		$grid = DataGrid::source ( $Query );
	
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'dispatch_date', 'Dispatch Date', 'dispatch_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'delivery_date', 'Delivery Date', 'delivery_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'fromCity', 'From', 'fromCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'toCity', 'To', 'toCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'lkp_post_ratecard_type_id', 'Post For','lkp_post_ratecard_type_id')->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'quote_access', 'Post Type','quote_access')->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'post_status', 'Status', 'post_status' )->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'dummy', '', 'show' )->style("display:none");
		$grid->add ( 'lkp_quote_access_id', 'Buyer access_id', 'lkp_quote_access_id' )->style ( "display:none" );
		$grid->add ( 'buyer_post_id', 'Buyer id', 'buyer_post_id' )->style ( "display:none" );
		$grid->add ( 'origin_elevator', 'elevator', 'origin_elevator' )->style ( "display:none" );
		$grid->add ( 'destination_elevator', 'destelevator', 'destination_elevator' )->style ( "display:none" );
		$grid->add ( 'origin_storage', 'storage', 'origin_storage' )->style ( "display:none" );
		$grid->add ( 'origin_destination', 'deststorage', 'origin_destination' )->style ( "display:none" );
		$grid->add ( 'origin_handyman_services', 'handyman', 'origin_handyman_services' )->style ( "display:none" );
		$grid->add ( 'destination_handyman_services', 'deshandyman', 'destination_handyman_services' )->style ( "display:none" );
		$grid->add ( 'insurance', 'Insurance', 'insurance' )->style ( "display:none" );
		$grid->add ( 'escort', 'Escort', 'escort' )->style ( "display:none" );
		$grid->add ( 'mobility', 'Mobility', 'mobility' )->style ( "display:none" );
		$grid->add ( 'property', 'Property', 'property' )->style ( "display:none" );
		$grid->add ( 'setting_service', 'SettingService', 'setting_service' )->style ( "display:none" );
		$grid->add ( 'insurance_industry', 'insuranceIndustry', 'insurance_industry' )->style ( "display:none" );
		$grid->add ( 'lkp_vehicle_category_id', 'VechicleCategory', 'lkp_vehicle_category_id' )->style ( "display:none" );
		$grid->add ( 'lkp_vehicle_category_type_id', 'VechicleCategory', 'lkp_vehicle_category_id' )->style ( "display:none" );
		$grid->add ( 'vehicle_model', 'VehicleModel', 'vehicle_model' )->style ( "display:none" );
		$grid->orderBy ( 'rbs.id', 'desc' );
		$grid->paginate ( 5 );
	
		$grid->row ( function ($row) {
			
			if($row->cells [5]->value==1){
				$row->cells [5]->value="HHG";
			}else{
				$row->cells [5]->value="Vehicle";
			}
			
				
			$buyer_post_id = $row->cells[0]->value;
			$data_link = url()."/getbuyercounteroffer/$buyer_post_id";
			$row->cells [0]->style ( 'display:none' );
			$row->cells [1]->attributes(array("class" => "html_link col-md-2 padding-left-none","data_link"=>$data_link));
			$row->cells [2]->attributes(array("class" => "html_link col-md-2 padding-left-none","data_link"=>$data_link));
			$row->cells [3]->attributes(array("class" => "html_link col-md-2 padding-left-none","data_link"=>$data_link));
			$row->cells [4]->attributes(array("class" => "html_link col-md-2 padding-left-none","data_link"=>$data_link));
			$row->cells [5]->attributes(array("class" => "html_link col-md-1 padding-left-none","data_link"=>$data_link));
			$row->cells [6]->attributes(array("class" => "html_link col-md-1 padding-left-none","data_link"=>$data_link));
			$row->cells [7]->attributes(array("class" => "html_link col-md-1 padding-left-none","data_link"=>$data_link));
			$row->cells [9]->style("display:none");
			$row->cells [10]->style("display:none");
			$row->cells [11]->style("display:none");
			$row->cells [12]->style("display:none");
			$row->cells [13]->style("display:none");
			$row->cells [14]->style("display:none");
			$row->cells [15]->style("display:none");
			$row->cells [16]->style("display:none");
			$row->cells [17]->style("display:none");
			$row->cells [18]->style("display:none");
			$row->cells [19]->style("display:none");
			$row->cells [20]->style("display:none");
			$row->cells [21]->style("display:none");
			$row->cells [22]->style("display:none");
			$row->cells [23]->style("display:none");
			$row->cells [24]->style("display:none");
			$row->cells [25]->style("display:none");
			$row->cells [1]->value=CommonComponent::checkAndGetDate($row->cells [1]->value);
			
			
			if($row->cells [2]->value== '0000-00-00') {
                            $row->cells [2]->value = 'NA';
                        } else {
                            $row->cells [2]->value=CommonComponent::checkAndGetDate($row->cells [2]->value);
                        }
                        
			$dispatchDate = $row->cells [1]->value;
			$fromCity = $row->cells [2]->value;
	        $toCity = $row->cells [3]->value;
	        if($row->cells [11]->value==1){
	        $originelevator='Yes';
	        }else{
	        $originelevator='No';
	        }
	        if($row->cells [12]->value==1){
	        	$destelevator='Yes';
	        }else{
	        	$destelevator='No';
	        }
	        if($row->cells [13]->value==1){
	        	$originstorage='Yes';
	        }else{
	        	$originstorage='No';
	        }
	        if($row->cells [14]->value==1){
	        	$deststorage='Yes';
	        }else{
	        	$deststorage='No';
	        }
	        if($row->cells [15]->value==1){
	        	$origin_handyman_services='Yes';
	        }else{
	        	$origin_handyman_services='No';
	        }
	        if($row->cells [16]->value==1){
	        	$destination_handyman_services='Yes';
	        }else{
	        	$destination_handyman_services='No';
	        }
	        if($row->cells [17]->value==1){
	        	$insurance='Yes';
	        }else{
	        	$insurance='No';
	        }
	        if($row->cells [18]->value==1){
	        	$escort='Yes';
	        }else{
	        	$escort='No';
	        }
	        if($row->cells [19]->value==1){
	        	$mobility='Yes';
	        }else{
	        	$mobility='No';
	        }
	        if($row->cells [20]->value==1){
	        	$property='Yes';
	        }else{
	        	$property='No';
	        }
	        if($row->cells [21]->value==1){
	        	$setting_service='Yes';
	        }else{
	        	$setting_service='No';
	        }
	        if($row->cells [22]->value==1){
	        	$insurance_industry='Yes';
	        }else{
	        	$insurance_industry='No';
	        }
	        if($row->cells [23]->value==1){
	          $vehicle_category="Car";	
	        }else{
	          $vehicle_category="Bike / Scooter / Scooty";
	        }
	        $vehicle_model=$row->cells [25]->value;	       
	        $countview = PtlBuyerGetQuoteBooknowComponent::updateBuyerQuoteDetailsViews($buyer_post_id,'relocation_buyer_post_views');
	        $msg_count  = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyer_post_id);
	        $quotescount = RelocationBuyerComponent::getQuotesCount($buyer_post_id);
	        if ( $row->cells [7]->value == 'Open') {
	        	$row->cells [8]->value .= "<div class='col-md-1 padding-none text-right'>
	        	<a href='#' data-target='#cancelbuyerpostmodal' data-toggle='modal' onclick='setcancelbuyerpostid(".$buyer_post_id.")' ><i class='fa fa-trash buyerpostdelete' title='Delete'></i></a>
	        	</div>";
	        }	        
	     	$row->cells [8]->value .= "<div class='clearfix'></div><div class='pull-left'>
	        <div class='info-links'>
	        <a href='/getbuyercounteroffer/$buyer_post_id?type=messages'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>".count($msg_count['result'])."</span></a>
	        <a href='/getbuyercounteroffer/$buyer_post_id'><i class='fa fa-file-text-o'></i> Quotes<span class='badge'>$quotescount</span></a>
	        <a href='#'><i class='fa fa-line-chart'></i> Market Analytics<span class='badge'>0</span></a>
	        <a href='#'><i class='fa fa-file-text-o'></i> Documentation<span class='badge'>0</span></a>
	        </div>
	        </div>
	        <div class='pull-right text-right'>
	        <div class='info-links'>
	        <span id='$buyer_post_id' data-sellerlistid='$buyer_post_id' class='spot_transaction_details_list'> 
	         <span class='ftl_spot_transaction_details'><span class='show_details'>+</span><span class='hide_details' style='display: none;'>-</span> Details</span> 
	        </span>
	        <a>
	        	<span class='views red'><i class='fa fa-eye' title='Views'></i> $countview </span>
	        </a>
	        </div>
	        </div>
	        
	        <div class='details-block-div clearfix' style='display:none;' id='spot_transaction_details_view_$buyer_post_id'>
	        <div class='expand-block margin-top'>";
			if($row->cells [5]->value=='HHG'){
			$row->cells [8]->value .="
			<div class='col-md-12 margin-top'>		
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Origin Elevator</span>
			<span class='data-value'>$originelevator</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Destination Elevator</span>
			<span class='data-value'>$destelevator</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Origin Storage</span>
			<span class='data-value'>$originstorage</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Destination Storage</span>
			<span class='data-value'>$deststorage</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Origin Handyman Services</span>
			<span class='data-value'>$origin_handyman_services</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Destination Handyman Services</span>
			<span class='data-value'>$destination_handyman_services</span>
			</div>
			</div>
			<div class='col-md-12 border-top-none padding-top-none'>		
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Insurane</span>
			<span class='data-value'>$insurance</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Escort</span>
			<span class='data-value'>$escort</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Mobility</span>
			<span class='data-value'>$mobility</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Property</span>
			<span class='data-value'>$property</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Setting Service</span>
			<span class='data-value'>$setting_service</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Insurance Domestic</span>
			<span class='data-value'>$insurance_industry</span>
			</div>
			</div>";
			}else{
			$row->cells [8]->value .="<div class='col-md-12 padding-none padding-top'>
				<div class='col-md-2 padding-left-none data-fld'>
				<span class='data-head'>Vehicle Catagoery</span>
				<span class='data-value'>$vehicle_category</span>
				</div>
				<div class='col-md-2 padding-left-none data-fld'>
				<span class='data-head'>Vehicle Model</span>
				<span class='data-value'>$vehicle_model</span>
				</div>
				</div>";
			}
			
			
			$row->cells [8]->value .="
			</div>
	        </div>";
	     
	
		} );
	
		// Functionality to build filters in the page starts
		$filter = DataFilter::source ( $Query );
		$filter->add ( 'rbs.from_location_id', '', 'select' )->options ( $from_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'rbs.to_location_id', '', 'select' )->options ( $to_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->submit ( 'search' );
					$filter->reset ( 'reset' );
					$filter->build ();
					// Functionality to build filters in the page ends
	
		$result = array ();
		$result ['grid'] = $grid;
		$result ['filter'] = $filter;
		return $result;
	}
	
	// buyer search for seller posts result component for relocation domestic
	public static function getRelocationBuyerSearchResults($request, $serviceId) {
		try {
			//echo "<pre>"; print_r($request); die;
			$prices = array();
			$Query_buyers_for_sellers = BuyerSearchComponent::search ($roleId=null,$serviceId,$statusId=null, $request );
			$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();	
			Session::put('relocbuyerrequest', $request->all());
					
			if(isset($_REQUEST['from_location']) && $_REQUEST['from_location'] && isset($_REQUEST['to_location']) && $_REQUEST['to_location']!='' && isset($_REQUEST['from_date']) && $_REQUEST['from_date'])
			{					
				// Storing Request Data to Session
				session()->put([
					'searchMod' => [
						'delivery_date_buyer' 	=> $request->to_date,
						'dispatch_date_buyer'	=> $request->from_date,
						'property_type' 		=> $request->property_type,
						'from_city_id_buyer' 	=> $request->from_location_id,
						'to_city_id_buyer' 		=> $request->to_location_id,
						'from_location_buyer' 	=> $request->from_location,
						'to_location_buyer' 	=> $request->to_location,
						'volume' 				=> $request->volume,
						'load_type' 			=> $request->load_type,
						'household_items' 		=> $request->household_items,
						'vehicle_category' 		=> $request->vehicle_category,
						'vehicle_model' 		=> $request->vehicle_model,
						'vehicle_category_type' => $request->vehicle_category_type,
						'rate_card_type' 		=> $request->post_rate_card_type,
						'total_hidden_volume' 	=> $request->total_hidden_volume,
					]
				]);                
				if(isset($request->elevator1)){
					session()->push('searchMod.elevator1',$request->elevator1);
				}
				if(isset($request->elevator2)){
					session()->push('searchMod.elevator2',$request->elevator2);
				}
			    if(isset($request->origin_handy_serivce)){
					session()->push('searchMod.origin_handy_serivce',$request->origin_handy_serivce);
           		}
             	if(isset($request->destination_handy_serivce)){
					session()->push('searchMod.destination_handy_serivce',$request->destination_handy_serivce);
             	}
             	if(isset($request->origin_storage_serivce)){
					session()->push('searchMod.origin_storage_serivce',$request->origin_storage_serivce);
           		}
             	if(isset($request->destination_storage_serivce)){
					session()->push('searchMod.destination_storage_serivce',$request->destination_storage_serivce);
             	}
             	if(isset($request->insurance_serivce)){
					session()->push('searchMod.insurance_serivce',$request->insurance_serivce);
             	}
             	if(isset($request->escort_serivce)){
					session()->push('searchMod.escort_serivce',$request->escort_serivce);
             	}
             	if(isset($request->mobilty_serivce)){
					session()->push('searchMod.mobilty_serivce',$request->mobilty_serivce);
             	}
             	if(isset($request->property_serivce)){
					session()->push('searchMod.property_serivce',$request->property_serivce);
             	}
             	if(isset($request->setting_serivce)){
					session()->push('searchMod.setting_serivce',$request->setting_serivce);
             	}
             	if(isset($request->insurance_domestic)){
					session()->push('searchMod.insurance_domestic',$request->insurance_domestic);
             	}
			}
			//Save Data in sessions			
			if (empty ( $Query_buyers_for_sellers_filter ) && isset($request['is_search'])) {
				CommonComponent::searchTermsSendMail ();
				Session::put('layered_filter', '');
				Session::put('layered_filter_payments', '');
				Session::put('show_layered_filter','');
			}
			// Below script for filter data getting from queries --for filters
			foreach ( $Query_buyers_for_sellers_filter as $seller_post_item ) {
				if(isset($request['is_search'])){
					if (! isset ( $paymentMethods [$seller_post_item->lkp_payment_mode_id] )) {
						$paymentMethods[$seller_post_item->lkp_payment_mode_id] = $seller_post_item->payment_mode;
					}
					
					if (! isset ( $sellerNames [$seller_post_item->seller_id] )) {
						$sellerNames[$seller_post_item->seller_id] = $seller_post_item->username;
					}
					Session::put('layered_filter_payments', $paymentMethods);
					Session::put('layered_filter', $sellerNames);
					
				}
			}
            
            $result = $Query_buyers_for_sellers->get ();

			foreach($result as $key => $res){

				if($_REQUEST['post_rate_card_type'] == 1){
					$searchvolume = (isset($_REQUEST['total_hidden_volume']) && !empty($_REQUEST['total_hidden_volume'])) ? $_REQUEST['total_hidden_volume'] : $_REQUEST['volume'];
					$result[$key]->price = ($searchvolume * $res->rate_per_cft) + $res->transport_charges;
					if(isset($_REQUEST['crating_items']) && !empty($_REQUEST['crating_items'])){
						$result[$key]->price = $result[$key]->price + ($_REQUEST['crating_items'] * $result[$key]->crating_charges);
					}
				}else{
					$result[$key]->price = $res->cost + $res->transport_charges;
				}
				$prices[] = $result[$key]->price;
			}

			if (isset ( $_REQUEST ['price'] ) && $_REQUEST ['price'] != '') {
				$splitprice = explode("    ",$_REQUEST ['price']);
				$from = trim(filter_var($splitprice[0],FILTER_SANITIZE_NUMBER_INT),"-");
				$to = trim(filter_var($splitprice[1],FILTER_SANITIZE_NUMBER_INT),"-");
				$_REQUEST['price_from'] = $from;
				$_REQUEST['price_to'] = $to;
			}else{
				if(!empty($prices)){
					$_REQUEST['price_from'] = floor(min($prices));
					$_REQUEST['price_to'] = ceil(max($prices));
					$_REQUEST['filter_price_from'] = $_REQUEST['price_from'];
					$_REQUEST['filter_price_to'] = $_REQUEST['price_to'];
				}else{
					$_REQUEST['price_from'] = 0;
					$_REQUEST['price_to'] = 1000;
				}
			}


			



			
			$gridBuyer = DataGrid::source ( $result );
			$gridBuyer->add ( 'id', 'ID', true )->style ( "display:none" );
			$gridBuyer->add ( 'username', 'Name', false )->attributes(array("class" => "col-md-3 padding-left-none"));
			if (isset($_REQUEST['post_rate_card_type']) && $_REQUEST['post_rate_card_type'] == 1) {
				$gridBuyer->add ( 'volume', 'Volume', false )->attributes(array("class" => "col-md-2 padding-left-none"));
			} else {
				$gridBuyer->add ( 'volume', '', false )->style ( "display:none" );
			}		
			$gridBuyer->add ( 'transitdays', 'Transit Days', false )->attributes(array("class" => "col-md-2 padding-left-none"));
			$gridBuyer->add ( 'property_type', 'Estimate', false )->attributes(array("class" => "col-md-2 padding-left-none"));			
			$gridBuyer->add ( 'test', 'Below Grid', true )->style ( "display:none" );
			if (isset($_REQUEST['post_rate_card_type']) && $_REQUEST['post_rate_card_type'] == 1) {
			$gridBuyer->add ( 'rate_per_cft', 'Od Charges', false )->style ( "display:none" );
			} else {
			$gridBuyer->add ( 'cost', 'vehicle cost', false )->style ( "display:none" );
			}
			$gridBuyer->add ( 'transport_charges', 'Transport Charges', false )->style ( "display:none" );
			$gridBuyer->add ( 'crating_charges', 'Creating Charges', false )->style ( "display:none" );
			$gridBuyer->add ( 'storate_charges', 'Storage Charges', false )->style ( "display:none" );
			$gridBuyer->add ( 'escort_charges', 'Escort Charges', false )->style ( "display:none" );
			$gridBuyer->add ( 'handyman_charges', 'Handyman Charges', false )->style ( "display:none" );
			$gridBuyer->add ( 'property_search', 'Property Search', false )->style ( "display:none" );
			$gridBuyer->add ( 'brokerage', 'Brokerage', false )->style ( "display:none" );
			$gridBuyer->add ( 'tracking', 'Tracking', false )->style ( "display:none" );
			$gridBuyer->add ( 'payment_mode', 'Payment mode', false )->style ( "display:none" );
			$gridBuyer->add ( 'transaction_id', 'Transaction Id',false )->style('display:none');
			$gridBuyer->add ( 'created_by', 'created by', 'created_by' )->style ( "display:none" );	
			$gridBuyer->add ( 'from_date', 'from_date', 'from_date' )->style ( "display:none" );
			$gridBuyer->add ( 'to_date', 'to_date', 'to_date' )->style ( "display:none" );
			$gridBuyer->add ( 'price', 'Price', 'price' )->style ( "display:none" );                        
            $gridBuyer->add ( 'cancellation_charge_price', 'cancellation_charge_price', 'cancellation_charge_price' )->style ( "display:none" );   
            $gridBuyer->add ( 'docket_charge_price', 'docket_charge_price', 'docket_charge_price' )->style ( "display:none" );   
            $gridBuyer->add ( 'transitdaysunits', 'transitdaysunits', 'transitdaysunits' )->style ( "display:none" );   
                        
			$gridBuyer->orderBy ( 'id', 'desc' );
			$gridBuyer->paginate ( 5 );
			
			$gridBuyer->row ( function ($row) {
				$row->cells [0]->style ( 'display:none' );
				$row->cells [1]->style ( 'display:none' );				
				$row->cells [2]->style ( 'display:none' );							
				$row->cells [3]->style ( 'display:none' );
				$row->cells [4]->style ( 'display:none' );				
				$row->cells [6]->style ( 'display:none' );				
				$row->cells [7]->style ( 'display:none' );
				$row->cells [8]->style ( 'display:none' );
				$row->cells [9]->style ( 'display:none' );
				$row->cells [10]->style ( 'display:none' );
				$row->cells [11]->style ( 'display:none' );
				$row->cells [12]->style ( 'display:none' );
				$row->cells [13]->style ( 'display:none' );
				$row->cells [14]->style ( 'display:none' );
				$row->cells [15]->style ( 'display:none' );
				$row->cells [16]->style ( 'display:none' );
                $row->cells [17]->style ( 'display:none' );
                $row->cells [18]->style ( 'display:none' );
                $row->cells [19]->style ( 'display:none' );
				$row->cells [20]->style ( 'display:none' );
				$id = $row->cells [0]->value;
				$sellername = $row->cells [1]->value;
				if(isset($_REQUEST['total_hidden_volume']) && !empty($_REQUEST['total_hidden_volume']) && $_REQUEST['total_hidden_volume']>1){
					$volume = $_REQUEST['total_hidden_volume'];
				}else{
					$volume = $row->cells [2]->value;
				}
				$price = CommonComponent::getPriceType($row->cells [20]->value);

				$transdays = $row->cells [3]->value;
				$estimate = $row->cells [4]->value;
                                //cost
				$odcharges = CommonComponent::number_format($row->cells [6]->value);				
				$transportcharges = CommonComponent::number_format($row->cells [7]->value);
				$creatingcharges = CommonComponent::number_format($row->cells [8]->value);
				$storagecharges = CommonComponent::number_format($row->cells [9]->value);
				$escortcharges = CommonComponent::number_format($row->cells [10]->value);
				$handlomancharges = CommonComponent::number_format($row->cells [11]->value);
				$propertysearch = CommonComponent::number_format($row->cells [12]->value);
				$brokerage = CommonComponent::number_format($row->cells [13]->value);
				$tracking = $row->cells [14]->value;
				$paymentmode = $row->cells [15]->value;
                                $transaction_id=$row->cells[16]->value;
                                $seller_id = $row->cells [17]->value;
                                $validfrom=$row->cells[18]->value;
                                $validto = $row->cells [19]->value;
                                $transitdaysunits = $row->cells [23]->value;
                                
                                $row->cells [21]->style ( 'display:none' );   
                                $row->cells [22]->style ( 'display:none' );
                                $row->cells [23]->style ( 'display:none' );  
                                $cancelCharges = CommonComponent::number_format($row->cells [21]->value);
                                $docketCharges = CommonComponent::number_format($row->cells [22]->value);

				$tracking_text = CommonComponent::getTrackingType($tracking);
				$track_type = '<i class="fa fa-signal"></i>&nbsp;'.$tracking_text;

				if ($paymentmode == 'Advance') {
					$paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
				} else {
					$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode;
				}
				
                                
				CommonComponent::viewCountForSeller(Auth::User()->id,$id,'relocation_seller_post_views');
				$ratecardType = $_REQUEST['post_rate_card_type']; //Ratecrad type from request data
				$url = url().'/buyerbooknowforsearch/'.$row->cells [0];
				$row->cells [5]->value="<form method='GET'role='form' action='$url' id='addptlbuyersearchbooknow_$id' name='addptlbuyersearchbooknow_$id'>"
                                        . "<div class='table-row volume_calc ' id='$id'>
										<div class='col-md-3 padding-left-none'>
											$sellername
											<div class='red'>
												<i class='fa fa-star'></i>
												<i class='fa fa-star'></i>
												<i class='fa fa-star'></i>
											</div>
										</div>";
				if ($ratecardType == 1) {
				$row->cells [5]->value.="<div class='col-md-2 padding-left-none' id='volumecft_$id'>$volume CFT</div>";
				}
                                
                                
				$row->cells [5]->value.="<div class='col-md-2 padding-left-none'>$transdays $transitdaysunits</div>
										<div class='col-md-2 padding-left-none' id='totalestimatecharges_$id' >$price</div>
										<div class='col-md-3 padding-none pull-right'>
											<input type='submit' class='btn red-btn pull-right buyer_book_now' data-url='$url'
                       data-buyerpostofferid='$id' data-booknow_list='$id' value='Book Now' />
										</div>										
										<div class='clearfix'></div>
										<div class='pull-left'>
											<div class='info-links'>
												<a href='#'>$track_type</a>												
												<a href='#'>$paymentType</a>
											</div>
										</div>
										<div id='ratecardtype_$id' style='display:none'>$ratecardType</div>
										<div class='pull-right text-right'>
											<div class='info-links'>
												<a class='viewcount_show-data-link' data-quoteId='$id' id='$id'><span class='show-icon'>+</span><span class='hide-icon'>-</span> Details</a>
												<a href='#' class='new_message' data-transaction_no='".$transaction_id."' data-userid='".$seller_id."' data-buyerquoteitemid='".$id."'><i class='fa fa-envelope-o'></i></a>
											</div>
										</div>

										<div class='col-md-12 show-data-div padding-top term_quote_details_$id'>";
				if ($ratecardType == 1) {
				$row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
												<span class='data-value' >O & D Charges (per CFT) : <span id='odacharges_$id'>$odcharges</span></span>
											</div>";
				} else {
				$row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
					<span class='data-value' >Cost : <span id='odacharges_$id'>$odcharges</span>/-</span>
					</div>";
				}
				$row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
												<span class='data-value' >Transport Charges : <span id='transportcharges_$id'>$transportcharges</span></span>
											</div>

											<div class='clearfix'></div>
											<div class='col-md-3 padding-left-none'>
												<span class='data-head'><u>Additional Charges</u></span>
											</div>

											<div class='clearfix'></div>";
                                if ($ratecardType == 1) {
                                $row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
                                                 <span class='data-value' >Storage Charges (CFT/Day) : <span id='storagecharges_$id'>$storagecharges</span></span>
                                         </div> ";
                                } else {
                                $row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
                                                 <span class='data-value' >Storage Charges (per Day) : <span id='storagecharges_$id'>$storagecharges</span></span>
                                         </div> ";   
                                }
                                                                                        
                                if ($ratecardType == 1) {
                                $row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
                                                    <span class='data-value' >Crating Charges (per CFT) : <span id='creatingcharges_$id'>$creatingcharges</span></span>
                                            </div>
                                            
                                            <div class='col-md-3 padding-left-none'>
                                                    <span class='data-value' >Escort Charges (per Day) : <span id='escortcharges_$id'>$escortcharges</span></span>
                                            </div>
                                            <div class='col-md-3 padding-left-none'>
                                                    <span class='data-value' >Handyman Charges (per Hour): <span id='handlomechargest_$id'>$handlomancharges</span></span>
                                            </div>

                                            <div class='clearfix'></div>											
                                            <div class='col-md-3 padding-left-none'>
                                                    <span class='data-value' >Property Search (Rs) : <span id='propertysearch_$id'>$propertysearch</span></span>
                                            </div>
                                            <div class='col-md-3 padding-left-none'>
                                                    <span class='data-value' >Brokerage (Rs) : <span id='brokerage_$id'>$brokerage</span></span>
                                            </div>";
                                            if($cancelCharges!='') {
                                            $row->cells [5]->value.="<div class='col-md-3 padding-left-none'>                                                                                                     <span class='data-value' >Cancellation Charges (Rs) : <span id='cancellation_$id'>$cancelCharges</span></span>                                                            </div>";
                                            }

                                            if($docketCharges!='') {
                                            $row->cells [5]->value.="<div class='col-md-3 padding-left-none'>                                                                                                            <span class='data-value' >Docket Charges (Rs) : <span id='docket_$id'>$docketCharges</span></span>                                                                                                    </div>";
                                            }
                                    
                                }
                                
                                
                                                                                        
				$row->cells [5]->value.=" </div></div>";
                                $row->cells [5]->value .=	"<div>
						<input id='buyersearch_booknow_buyer_id_$id' type='hidden' value=".Auth::User()->id." name='buyersearch_booknow_buyer_id_$id' >
						<input id='buyersearch_booknow_seller_id_$id' type='hidden' value=".$seller_id." name='buyersearch_booknow_seller_id_$id'>
						<input id='buyersearch_booknow_seller_price_$id' type='hidden' value=".$row->cells [20]->value." name='buyersearch_booknow_seller_price_$id'>
						<input id='buyersearch_booknow_from_date_$id' type='hidden' value=".$validfrom.">
						<input id='buyersearch_booknow_to_date_$id' type='hidden' value=".$validto.">
						<input id='buyersearch_booknow_dispatch_date_$id' type='hidden' value='".CommonComponent::convertDateForDatabase(Session::get('session_dispatch_date_buyer'))."'>
						<input id='buyersearch_booknow_delivery_date_$id' type='hidden' value='".CommonComponent::convertDateForDatabase(Session::get('session_delivery_date_buyer'))."'>
					</div></form>";
			} );
			
				
				$result = array ();
				$result ['gridBuyer'] = $gridBuyer;
				//$result ['filter'] = $filter;
				return $result;
			
		} catch ( Exception $exc ) {		
		}
	}
	
	public static function getRelocationBuyerLeadPostsList(){

		$from_locations = array (
				"" => "From Location"
		);
		$to_locations = array (
				"" => "To Location"
		);
		$Query = DB::table ( 'relocation_seller_posts as rsp' );
		$Query->leftjoin ( 'relocation_seller_post_items as rspi', 'rspi.seller_post_id', '=', 'rsp.id' );
		$Query->leftjoin ( 'relocation_seller_selected_buyers as rsb', 'rsb.seller_post_id', '=', 'rsp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rsp.lkp_post_status_id' );
		$Query->join ( 'lkp_cities as cf', 'rsp.from_location_id', '=', 'cf.id' );
		$Query->join ( 'lkp_cities as ct', 'rsp.to_location_id', '=', 'ct.id' );
		$Query->join ( 'lkp_post_ratecard_types as prct', 'rsp.rate_card_type', '=', 'prct.id' );
		$Query->join ( 'lkp_quote_accesses as qa', 'rsp.lkp_access_id', '=', 'qa.id' );
		$Query->join ( 'users as u', 'rsp.seller_id', '=', 'u.id' );
		$Query->where( 'rsp.lkp_post_status_id', 2);
		$Query->where( 'rsb.buyer_id', Auth::User ()->id);
		$Query->where('rspi.is_private', 0);
		if (isset ( $post_status ) && $post_status != '') {
			$Query->where ( 'rsp.lkp_post_status_id', '=', $post_status );
		}
		if (isset ( $_GET ['dispatch_date'] ) && $_GET ['dispatch_date'] != '') {
			$commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['dispatch_date']);
			$Query->where ( 'rsp.from_date', '>=', $commonDispatchDate );
			$from_date = $commonDispatchDate;
			//echo "From Date :"; echo $from_date;die();
		}
		if (isset ( $_GET ['delivery_date'] ) && $_GET ['delivery_date'] != '') {
			$commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['delivery_date']);
			$Query->where ( 'rsp.to_date', '<=', $commonDeliveryhDate);
			$to_date = $commonDeliveryhDate;
			//echo "To Date :"; echo $to_date;die();
		}
		
		if( isset($_REQUEST['search']) && $_REQUEST['post_for']!=0){
			$post_for=$_REQUEST['post_for'];
			$Query->whereRaw('rsp.rate_card_type = "'.$post_for.'"');
		}
		$Query->groupBy('rsp.id');
		$postResults = $Query->select ( 'rsp.*', 'u.username','u.id as user_id','prct.ratecard_type','ps.post_status', 'ct.city_name as toCity', 'cf.city_name as fromCity')->get ();
		foreach ( $postResults as $post ) {
				
			if (! isset ( $from_locations [$post->from_location_id] )) {
				$from_locations [$post->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $post->from_location_id)->pluck ( 'city_name' );
			}
			if (! isset ( $to_locations [$post->to_location_id] )) {
				$to_locations [$post->to_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $post->to_location_id )->pluck ( 'city_name' );
			}
		
				
		}
		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations = CommonComponent::orderArray($to_locations);
		
		$grid = DataGrid::source ( $Query );
		
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Seller Name', 'username' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'fromCity', 'From', 'fromCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'toCity', 'To', 'toCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'ratecard_type', 'Property Type', 'ratecard_type' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'dummy', '', 'show' )->style ( "display:none" );
		$grid->add ( 'from_location_id', 'From', 'from_location_id' )->style ( "display:none" );
		$grid->add ( 'to_location_id', 'To', 'to_location_id' )->style ( "display:none" );
		
		$grid->orderBy ( 'rsp.id', 'desc' );
		$grid->paginate ( 5 );
		
		$grid->row ( function ($row) {
			
			$seller_post_id = $row->cells[0]->value;
			$username=$row->cells [1]->value;
			$buyer_id=Auth::User ()->id;
			$row->cells [0]->style ( 'display:none' );
			$row->cells [4]->value = CommonComponent::checkAndGetDate($row->cells [4]->value);
			$row->cells [5]->value = CommonComponent::checkAndGetDate($row->cells [5]->value);
			$row->cells [1]->value="$username
			<div class='red'>
			<i class='fa fa-star'></i>
			<i class='fa fa-star'></i>
			<i class='fa fa-star'></i>
			</div>";
			$row->cells [1]->attributes(array("class" => "col-md-2 padding-left-none"));
			$row->cells [2]->attributes(array("class" => "col-md-2 padding-left-none"));
			$row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none"));
			$row->cells [4]->attributes(array("class" => "col-md-2 padding-left-none"));
			$row->cells [5]->attributes(array("class" => "col-md-1 padding-left-none"));
			$row->cells [6]->attributes(array("class" => "col-md-1 padding-left-none"));
			$row->cells [8]->style ( 'display:none' );
			$row->cells [9]->style ( 'display:none' );
			$msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$seller_post_id);
			$sellerPostDetails=RelocationSellerComponent::SellerPostDetails($seller_post_id);
			
			$seller_post=$sellerPostDetails['seller_post'][0];
			$seller_post_items=$sellerPostDetails['seller_post_items'];
			
		
			$householdItems = 0;
			$vehicleItems = 0;
			foreach($seller_post_items as $key=>$seller_post_edit_action_line){
				if($seller_post_edit_action_line->rate_card_type == 1){
					$householdItems++;
					$totalAmounthouse = ($seller_post_edit_action_line->volume*$seller_post_edit_action_line->rate_per_cft)+$seller_post_edit_action_line->transport_charges;
				}elseif($seller_post_edit_action_line->rate_card_type == 2){
					$vehicleItems++;
					$totalAmountveh = $seller_post_edit_action_line->rate_per_cft+$seller_post_edit_action_line->transport_charges;
				}
			}
				
			if($householdItems>0){
				$totalAmount=$totalAmounthouse;
			}
			if($vehicleItems>0){
				$totalAmount=$totalAmountveh;
			}
			if($householdItems>0 && $vehicleItems>0){
				$totalAmount=$totalAmounthouse+$totalAmountveh;
			}
			
			$url = url().'/buyerbooknowforsearch/'.$seller_post_id;
			$row->cells [7]->value = "<div class='col-md-2 padding-none text-right'>
			<form name='addptlbuyersearchbooknow_$seller_post_id' id='addptlbuyersearchbooknow_$seller_post_id' action='$url' role='form' method='GET'>
			<div class='volume_calc'>
			<!-- input type='submit' value='Book Now' data-booknow_list='56' data-buyerpostofferid='$seller_post_id' data-url='$url' class='btn red-btn pull-right buyer_book_now' --!>
			<input id='buyersearch_booknow_buyer_id_$seller_post_id' value='$buyer_id' name='buyersearch_booknow_buyer_id_$seller_post_id' type='hidden'>
		    <input id='buyersearch_booknow_seller_id_$seller_post_id' value='$seller_post->seller_id' name='buyersearch_booknow_seller_id_$seller_post_id' type='hidden'>
			<input id='buyersearch_booknow_seller_price_$seller_post_id' value='$totalAmount' name='buyersearch_booknow_seller_price_$seller_post_id' type='hidden'>
			<input id='buyersearch_booknow_from_date_$seller_post_id' value=".CommonComponent::convertDateForDatabase($row->cells [4]->value)." name='buyersearch_booknow_from_date_$seller_post_id' type='hidden'>
			<input id='buyersearch_booknow_to_date_$seller_post_id' value=".CommonComponent::convertDateForDatabase($row->cells [5]->value)." name='buyersearch_booknow_to_date_$seller_post_id' type='hidden'>
			<input id='buyersearch_booknow_dispatch_date_$seller_post_id' value=".CommonComponent::convertDateForDatabase($row->cells [4]->value)." name='buyersearch_booknow_dispatch_date_$seller_post_id' type='hidden'>
			<input id='buyersearch_booknow_delivery_date_$seller_post_id' value=".CommonComponent::convertDateForDatabase($row->cells [5]->value)." name='buyersearch_booknow_delivery_date_$seller_post_id' type='hidden'>
			</div>
			</form>
			</div>
			<div class='clearfix'></div>
			<div class='col-md-12 padding-none '>						
                            <div class='pull-left'>
                                <div class='info-links'>
                                <a href='#'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>".count($msg_count['result'])."</span></a>
                                <a href='#'><i class='fa fa-file-text-o'></i> Quotes<span class='badge'></span></a>
                                <a href='#'><i class='fa fa-line-chart'></i> Market Analytics<span class='badge'>0</span></a>
                                <a href='#'><i class='fa fa-file-text-o'></i> Documentation<span class='badge'>0</span></a>
                                </div>
                            </div>
                            <div class='pull-right text-right'>
                                <div class='info-links'>
                                <a id='".$seller_post_id."' data-sellerlistid=$seller_post_id class='viewcount_show-data-link' data-quoteId='$seller_post_id' ><span class='show-icon spot_transaction_details_list'>+</span><span class='hide-icon'>-</span> Details</a>

                                </div>
                            </div>
                    
                    <div class='details-block-div clearfix show-data-div' style='display:none;' id='spot_transaction_details_view_$seller_post_id'>
                    <div class=''>";
		
			$row->cells [7]->value .="
			<div class='col-md-12 margin-top'>		
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Cancellation Charges</span>
			<span class='data-value'>$seller_post->cancellation_charge_price</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Docket Charges</span>
			<span class='data-value'>$seller_post->docket_charge_price</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Crating Charges</span>
			<span class='data-value'>$seller_post->crating_charges</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Storage Charges</span>
			<span class='data-value'>$seller_post->storate_charges</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Escort Charges</span>
			<span class='data-value'>$seller_post->escort_charges</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Handyman Charges</span>
			<span class='data-value'>$seller_post->handyman_charges</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Property Search</span>
			<span class='data-value'>$seller_post->property_search</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Brokerage</span>
			<span class='data-value'>$seller_post->brokerage</span>
			</div>					
			</div>";
			
			$row->cells [7]->value .="<div class='col-md-12  data-fld'>
			<span class='data-head'>Terms &amp; Conditions</span>
			<span class='data-value'>$seller_post->terms_conditions</span>
			</div>";
			
			
			
			$row->cells [7]->value .="<div class='col-md-12'>";
			
			if($householdItems > 0){
			$row->cells [7]->value .="<div class='table-div table-style1 margin-top'>
				<!-- Table Head Starts Here -->
				<div class='table-heading inner-block-bg'>
				<div class='col-md-2 padding-left-none'>Property Type</div>
				<div class='col-md-2 padding-left-none'>Volume</div>
				<div class='col-md-2 padding-left-none'>Load Type</div>
				<div class='col-md-2 padding-left-none'>O & D Charges (per CFT)</div>
				<div class='col-md-2 padding-left-none'>Transport Charges</div>
				<div class='col-md-2 padding-left-none'>Transit Days</div>
				</div>
				<div class='table-data'>";
				
			foreach($seller_post_items as $seller_post_edit_action_line){
			if($seller_post_edit_action_line->rate_card_type == 1){
			$row->cells [7]->value .="<div class='table-row inner-block-bg'>
			<div class='col-md-2 padding-left-none'>".CommonComponent::getPropertyType($seller_post_edit_action_line->lkp_property_type_id)."</div>
			<div class='col-md-2 padding-left-none'>$seller_post_edit_action_line->volume CFT</div>
			<div class='col-md-2 padding-left-none'>".CommonComponent::getLoadCategoryById($seller_post_edit_action_line->lkp_load_category_id)." </div>
			<div class='col-md-2 padding-left-none'>$seller_post_edit_action_line->rate_per_cft /-</div>
			<div class='col-md-2 padding-none'>$seller_post_edit_action_line->transport_charges /-</div>
			<div class='col-md-2 padding-left-none'>$seller_post_edit_action_line->transitdays $seller_post_edit_action_line->units</div>
			</div>";
			}
			}
			
			$row->cells [7]->value .="</div>";
			
			$row->cells [7]->value .="</div>";
		    }
			
			$row->cells [7]->value .="<div class='clearfix'></div>";
			if($vehicleItems > 0){
			$row->cells [7]->value .="<div class='table-style table-style1 margin-top margin-bottom'>
			<div class='table-heading inner-block-bg'>
			<div class='col-md-3 padding-left-none'>Vehicle Category</div>
			<div class='col-md-2 padding-left-none'>Car Type</div>
			<div class='col-md-2 padding-left-none'>Cost</div>
			<div class='col-md-2 padding-none'>Transport Charges</div>
			<div class='col-md-3 padding-left-none'>Transit Days</div>
			</div>
			
			<div class='table-data'>";
			foreach($seller_post_items as $seller_post_edit_action_line){
			if($seller_post_edit_action_line->rate_card_type == 2){
			$row->cells [7]->value .="<div class='table-row inner-block-bg'>
			<div class='col-md-3 padding-left-none'>".CommonComponent::getVehicleCategoryById($seller_post_edit_action_line->lkp_vehicle_category_id)."</div>
			<div class='col-md-2 padding-left-none'>".CommonComponent::getVehicleCategorytypeById($seller_post_edit_action_line->lkp_car_size)."</div>
			<div class='col-md-2 padding-left-none'>$seller_post_edit_action_line->cost /-
			<input name='vehicle_cost_$seller_post_edit_action_line->lkp_vehicle_category_id' id='vehicle_cost_$seller_post_edit_action_line->lkp_vehicle_category_id' value='$seller_post_edit_action_line->cost' type='hidden'/>
			</div>
			<div class='col-md-2 padding-none'>$seller_post_edit_action_line->transport_charges /-</div>
			<div class='col-md-3 padding-left-none'>$seller_post_edit_action_line->transitdays $seller_post_edit_action_line->units</div>
			</div>";
			}
			}
			
			$row->cells [7]->value .="</div>
			</div>";
			}
			$row->cells [7]->value .="</div>";
			$row->cells [7]->value .="</div>";
			$row->cells [7]->value .="
			</div>
	        </div>";
			
		});
		
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'rsp.from_location_id', '', 'select' )->options ( $from_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
			$filter->add ( 'rsp.to_location_id', '', 'select' )->options ( $to_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
			$filter->submit ( 'search' );
			$filter->reset ( 'reset' );
			$filter->build ();
			
			$result = array ();
			$result ['grid'] = $grid;
			$result ['filter'] = $filter;
			return $result;
		
	}
	public static function getBuyerPostDetails($buyer_post_id, $serviceId=null,$roleid=null,$comparisonType=null,$sellerIds=null) {
		try {
	
			$buyer_post_edit_seller='';
			$buyer_post_inventory_details='';
			$buyer_post_details = DB::table ( 'relocation_buyer_posts' )->where ( 'id', $buyer_post_id )->get ();
			
			if($buyer_post_details[0]->lkp_post_ratecard_type_id==1){
				$buyer_post_inventory_details = DB::table ( 'relocation_buyer_post_inventory_particulars' )->where ( 'buyer_post_id', $buyer_post_id )->get ();
			}
			
			$Query = DB::table ( 'relocation_buyer_quote_sellers_quotes_prices as rsqb' );
			$Query->leftjoin ( 'users as u', 'u.id', '=', 'rsqb.seller_id' );
			//$Query->leftjoin('seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id');
                        $Query->leftjoin('relocation_seller_posts as sp', 'sp.id', '=', 'rsqb.seller_post_id');
                        $Query->where( 'rsqb.buyer_quote_id', $buyer_post_id);
            
			if($comparisonType==1){
				$Query->orderBy('rsqb.transit_days');
			}
			if($comparisonType==2){
				$Query->orderBy('rsqb.total_price');
			}			
			if($sellerIds){
			$sellerIds= explode(",",$sellerIds);
			$Query->whereIn( 'rsqb.seller_id', $sellerIds);			
			}
			$sellerResults = $Query->select ('rsqb.private_seller_quote_id','sp.from_date','sp.to_date','sp.transaction_id as transaction_no', 'rsqb.*', 'u.username')->get ();
			
			
			$j=0;
			$k=1;
			$p=1;
			if($comparisonType != null){
			for ($i=0;$i<count($sellerResults);$i++) {
				if($i==0){
					$j=1;
				}
				if($j>count($sellerResults)-1){
					$j=count($sellerResults)-1;
				}
				if($comparisonType == '1'){
					if($sellerResults[$i]->transit_days !=$sellerResults[$j]->transit_days ){
						 
						if($k<=3){
							$sellerResults[$i]->rank="L".$k;
						} else{
							$sellerResults[$i]->rank="-";
						}
						$k++;
					}else{
						if($k<=3){
							$sellerResults[$i]->rank="L".$k;
						} else{
							$sellerResults[$i]->rank="-";
						}
					}
				}
				if($comparisonType == '2'){
					if($sellerResults[$i]->total_price!=$sellerResults[$j]->total_price){
			
						if($p<=3){
							$sellerResults[$i]->rank="L".$p;
						} else{
							$sellerResults[$i]->rank="-";
						}
						$p++;
					}else{
						if($p<=3){
							$sellerResults[$i]->rank="L".$p;
						}else{
							$sellerResults[$i]->rank="-";
						}
					}
				}
				$j++;
			
			
			
			}
			}
			
			
			$result = array();			
			$result ['postDetails'] = $buyer_post_details;
			$result ['inventoryDetails'] = $buyer_post_inventory_details;
			$result['sellerResults'] = $sellerResults;			
			return $result;
			
		} catch ( Exception $exc ) {
		}
	}	
	
	public static function getQuotesCount($buyer_post_id){
		
		$buyer_post_edit_seller = DB::table('relocation_buyer_quote_sellers_quotes_prices')
  		->where('relocation_buyer_quote_sellers_quotes_prices.buyer_quote_id', $buyer_post_id)
  		->select('relocation_buyer_quote_sellers_quotes_prices.*')
		->get();
		return count($buyer_post_edit_seller);
		
	}
	

	/**
	 * Buyer Orders Detail Page in Relocation Page
	 * Retrieval of data related to Buyer Orders
	 *
	 */
	public static function getRelocationBuyerOrderDetails($serviceId, $orderId, $user_id) {
		try {
			

			$order_type=DB::table('orders')
			->where('orders.id', $orderId)
			->select('orders.*')
			->get();
			
			$orders=array();
			$spot=1;$term=2;
			$query = DB::table('orders');			
			$query->leftJoin('order_payments as op', 'orders.order_payment_id', '=', 'op.id')
			->leftJoin('order_invoices as oi', 'oi.order_id', '=', 'orders.id')
			->leftjoin('lkp_payment_modes', 'lkp_payment_modes.id', '=', 'op.lkp_payment_mode_id')			
			->leftJoin('lkp_order_statuses as os', 'orders.lkp_order_status_id', '=', 'os.id');
			
			
			$serviceId = Session::get('service_id');
			 switch ($serviceId) {
			 case RELOCATION_DOMESTIC :
			 		$query->leftJoin('lkp_cities as lc', 'lc.id', '=', 'orders.from_city_id');
			 		$query->leftJoin('lkp_cities as lcity', 'lcity.id', '=', 'orders.to_city_id');
			 		if($order_type[0]->lkp_order_type_id==1){
			 		$query->leftJoin('relocation_buyer_posts as rbq', 'rbq.id', '=', 'orders.buyer_quote_id');
			 		}
			 		else{
			 		$query->leftJoin('term_buyer_quote_items as tbqi', 'tbqi.id', '=', 'orders.buyer_quote_item_id');
			 		$query->leftJoin('term_buyer_quotes as tbq', 'tbq.id', '=', 'tbqi.term_buyer_quote_id');
			 		}				 	
             		$query->leftjoin('users as u', 'u.id', '=', 'orders.seller_id')->where('orders.id', '=', $orderId);             		
             		$query->where('orders.buyer_id', '=', $user_id);
             		if($order_type[0]->lkp_order_type_id==1){
             		$orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lkp_payment_modes.payment_mode', 'oi.total_amt as inv_total','rbq.transaction_id as postid','rbq.lkp_post_ratecard_type_id','rbq.lkp_vehicle_category_id','rbq.lkp_load_category_id')->first();
             		}
             		else{
             		
             	    $orders['orderDetails'] = $query->select('tbq.transaction_id as postid','tbq.id as termbuyerid','tbq.lkp_post_ratecard_type as lkp_post_ratecard_type_id','tbqi.lkp_load_type_id as lkp_load_category_id','oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lkp_payment_modes.payment_mode', 'oi.total_amt as inv_total')->first();
             		}
		            break;
					}
			return $orders;
			 
		} catch ( Exception $exc ) {
			
		}
		
	}
	
}
