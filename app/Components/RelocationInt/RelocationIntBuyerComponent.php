<?php

namespace App\Components\RelocationInt;

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
class RelocationIntBuyerComponent {
	
	
	public static function getRelocationBuyerPostsList($service_id, $post_status, $enquiry_type, $rel_int_type) {
	
		
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
		$Query = DB::table ( 'relocationint_buyer_posts as rbs' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rbs.lkp_post_status_id' );
		$Query->leftjoin ( 'lkp_quote_accesses as qa', 'qa.id', '=', 'rbs.lkp_quote_access_id' );
		$Query->leftjoin ( 'lkp_cities as cf', 'rbs.from_location_id', '=', 'cf.id' );
		$Query->leftjoin ( 'lkp_cities as ct', 'rbs.to_location_id', '=', 'ct.id' );
		//$Query->leftjoin ( 'relocationint_buyer_post_air_cartons as rbi', 'rbs.id', '=', 'rbi.buyer_post_id' );
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

		if (isset ( $rel_int_type ) && $rel_int_type != '') {
			$Query->where ( 'rbs.lkp_international_type_id', '=', $rel_int_type );
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
		$grid->add ( 'dispatch_date', 'Dispatch Date', true )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'delivery_date', '', false )->style ( "display:none" );
		$grid->add ( 'fromCity', 'From', true )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'toCity', 'To', true )->attributes(array("class" => "col-md-2 padding-left-none"));		
		if($rel_int_type == 1)
			$grid->add ( 'no_of_cartons', 'No of Cartons', false )->attributes(array("class" => "col-md-3 padding-left-none"));
		else
			$grid->add ( 'total_cbm', 'Volume (CBM)', false )->attributes(array("class" => "col-md-3 padding-left-none"));		

		$grid->add ( 'post_status', 'Status', true )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'buyer_post_id', 'Buyer id', 'buyer_post_id' )->style ( "display:none" );
		$grid->add ( 'lkp_international_type_id', 'International Type Id', false )->style ( "display:none" ); 
                $grid->add ( 'lkp_quote_access_id', 'Quote Access Id', false )->style ( "display:none" ); 
		
		$grid->orderBy ( 'rbs.id', 'desc' );
		$grid->paginate ( 5 );

	   	$grid->row ( function ($row) {
			
							
			$buyer_post_id = $row->cells[0]->value;
			$data_link = url()."/getbuyercounteroffer/$buyer_post_id";
			$row->cells [0]->style ( 'display:none' );
			$row->cells [1]->attributes(array("class" => "html_link col-md-2 padding-left-none","data_link"=>$data_link));
			$row->cells [2]->style ( "display:none" ); 
			$row->cells [3]->attributes(array("class" => "html_link col-md-2 padding-left-none","data_link"=>$data_link));
			$row->cells [4]->attributes(array("class" => "html_link col-md-2 padding-left-none","data_link"=>$data_link));
			$row->cells [5]->attributes(array("class" => "html_link col-md-3 padding-none","data_link"=>$data_link));
			$row->cells [6]->attributes(array("class" => "html_link col-md-2 padding-none","data_link"=>$data_link));
			$row->cells [8]->style ( "display:none" ); 
                        $row->cells [9]->style ( "display:none" ); 
			
			$row->cells [1]->value=CommonComponent::checkAndGetDate($row->cells [1]->value);
			$row->cells [2]->value=CommonComponent::checkAndGetDate($row->cells [2]->value);
						
			$dispatchDate = $row->cells [1]->value;
			$fromCity = $row->cells [2]->value;
                        $toCity = $row->cells [3]->value;
                        $international_type_id = $row->cells [8]->value;

	        if($international_type_id == 1){
		        $no_of_cartons = CommonComponent::getCartonsTotal($buyer_post_id);
				$row->cells [5]->value = $no_of_cartons;
			}else{
				//Get Total Volume CFT
		        $totalCFT = \App\Components\RelocationInt\OceanInt\RelocationOceanSellerComponent::getVolumeCft($buyer_post_id);
				//Get Total CBM
				$row->cells [5]->value = round($totalCFT/35.5, 2);
			}                        
                        $quote_access_id = $row->cells [9]->value;
                        if($quote_access_id == 1 && $row->cells [6]->value == 'Open')
                            $edit_option='';
                        else
                            $edit_option='<a href="/editrelocationbuyerquote/'.$buyer_post_id.'"><i class="fa fa-edit" title="Edit"></i></a>';                            

	        $countview = PtlBuyerGetQuoteBooknowComponent::updateBuyerQuoteDetailsViews($buyer_post_id,'relocationint_buyer_post_views');
	        $msg_count  = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyer_post_id);
	        $quotescount = RelocationIntBuyerComponent::getQuotesCount($buyer_post_id);
	        if ( $row->cells [6]->value == 'Open') {
	        	$row->cells [7]->value .= "<div class='col-md-1 padding-none text-right'>
                        $edit_option
	        	<a href='#' data-target='#cancelbuyerpostmodal' data-toggle='modal' onclick='setcancelbuyerpostid(".$buyer_post_id.")' ><i class='fa fa-trash buyerpostdelete' title='Delete'></i></a>
	        	</div>";
	        }	        
	     	$row->cells [7]->value .= "<div class='clearfix'></div><div class='pull-left'>
	        <div class='info-links'>
	        <a href='/getbuyercounteroffer/$buyer_post_id?type=messages'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>".count($msg_count['result'])."</span></a>
	        <a href='/getbuyercounteroffer/$buyer_post_id'><i class='fa fa-file-text-o'></i> Quotes<span class='badge'>$quotescount</span></a>
	        <a href='#'><i class='fa fa-line-chart'></i> Market Analytics<span class='badge'>0</span></a>
	        <a href='#'><i class='fa fa-file-text-o'></i> Documentation<span class='badge'>0</span></a>
	        </div>
	        </div>
	        <div class='pull-right text-right'>
	        <div class='info-links'><a>
	        <span class='views red'><i class='fa fa-eye' title='Views'></i> $countview </span>
	        </a>
	        </div>
	        </div>
	        
	        <div class='details-block-div clearfix' style='display:none;' id='spot_transaction_details_view_$buyer_post_id'>
	        <div class='expand-block margin-top'>";			
			
			$row->cells [7]->value .="
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
			if (isset ( $_REQUEST ['price'] ) && $_REQUEST ['price'] != '') {
				$splitprice = explode("    ",$_REQUEST ['price']);
				$from = trim(filter_var($splitprice[0],FILTER_SANITIZE_NUMBER_INT),"-");
				$to = trim(filter_var($splitprice[1],FILTER_SANITIZE_NUMBER_INT),"-");
				$_REQUEST['price_from'] = $from;
				$_REQUEST['price_to'] = $to;
				//echo "From is $from to is $to";
			}
			if (!isset ( $_REQUEST ['price'] )) {
				if(!empty($prices)){
					$_REQUEST['price_from'] = min($prices);
					$_REQUEST['price_to'] = max($prices);
				}else{
					$_REQUEST['price_from'] = 0;
					$_REQUEST['price_to'] = 1000;
				}
			}			
			$Query_buyers_for_sellers = BuyerSearchComponent::search ($roleId=null,$serviceId,$statusId=null, $request );

			$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();	
			//echo "<pre>"; print_R($Query_buyers_for_sellers_filter); die;
			//if(count($Query_buyers_for_sellers_filter)==0){
				Session::put('relocbuyerrequest', $request);
			//}			
			if(isset($_REQUEST['from_location']) && $_REQUEST['from_location'] && isset($_REQUEST['to_location']) && $_REQUEST['to_location']!='' && isset($_REQUEST['from_date']) && $_REQUEST['from_date'] )
			{					
				Session::put('session_delivery_date_buyer',$_REQUEST['to_date']);
				Session::put('session_dispatch_date_buyer',$_REQUEST['from_date']);				
				Session::put('session_property_type',$_REQUEST['property_type']);
				Session::put('session_from_city_id_buyer',$_REQUEST['from_location_id']);
				Session::put('session_to_city_id_buyer',$_REQUEST['to_location_id']);
				Session::put('session_from_location_buyer',$_REQUEST['from_location']);
				Session::put('session_to_location_buyer',$_REQUEST['to_location']);		
				Session::put('session_volume',$_REQUEST['volume']);
				Session::put('session_load_type',$_REQUEST['load_type']);
				Session::put('session_household_items',$_REQUEST['household_items']);
				Session::put('session_vehicle_category',$_REQUEST['vehicle_category']);
				Session::put('session_vehicle_model',$_REQUEST['vehicle_model']);
				Session::put('session_vehicle_category_type',$_REQUEST['vehicle_category_type']);
                                Session::put('session_rate_card_type',$_REQUEST['post_rate_card_type']);
			}			
			//Save Data in sessions			
			//echo "<pre>"; print_R($Query_buyers_for_sellers_filter); die;			
			if (empty ( $Query_buyers_for_sellers_filter )) {
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
				/*if (isset($_REQUEST['post_rate_card_type']) && $_REQUEST['post_rate_card_type'] == 1) {
					$prices[] = ($seller_post_item->volume*$seller_post_item->rate_per_cft)+$seller_post_item->transport_charges;
				}else{
					$prices[] = $seller_post_item->cost+$seller_post_item->transport_charges;
				} */
			}
                        //echo "<pre>";print_r($prices);exit;
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


			if(!empty($prices) && isset($request['is_search']) && !isset($_REQUEST['filter_set'])){
				$_REQUEST['price_from'] = 0;
				$_REQUEST['price_to'] = max($prices);
			}
			//echo "<pre>";print_R($_REQUEST);print_R($result);die;
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
				if(isset($_REQUEST['total_hidden_volume']) && !empty($_REQUEST['total_hidden_volume'])){
					$volume = $_REQUEST['total_hidden_volume'];
				}else{
					$volume = $row->cells [2]->value;
				}
				$price = $row->cells [20]->value;

				$transdays = $row->cells [3]->value;
				$estimate = $row->cells [4]->value;
                                //cost
				$odcharges = $row->cells [6]->value;				
				$transportcharges = $row->cells [7]->value;
				$creatingcharges = $row->cells [8]->value;
				$storagecharges = $row->cells [9]->value;
				$escortcharges = $row->cells [10]->value;
				$handlomancharges = $row->cells [11]->value;
				$propertysearch = $row->cells [12]->value;
				$brokerage = $row->cells [13]->value;
				$tracking = $row->cells [14]->value;
				$paymentmode = $row->cells [15]->value;
                                $transaction_id=$row->cells[16]->value;
                                $seller_id = $row->cells [17]->value;
                                $validfrom=$row->cells[18]->value;
                                $validto = $row->cells [19]->value;
                                
                                $row->cells [21]->style ( 'display:none' );   
                                $row->cells [22]->style ( 'display:none' );   
                                $cancelCharges = $row->cells [21]->value;
                                $docketCharges = $row->cells [22]->value;
				
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
                                
                                
				$row->cells [5]->value.="<div class='col-md-2 padding-left-none'>$transdays</div>
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
												<span class='data-value' >O & D Charges (per CFT) : <span id='odacharges_$id'>$odcharges</span>/-</span>
											</div>";
				} else {
				$row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
					<span class='data-value' >Cost : <span id='odacharges_$id'>$odcharges</span>/-</span>
					</div>";
				}
				$row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
												<span class='data-value' >Transport Charges : <span id='transportcharges_$id'>$transportcharges</span>/-</span>
											</div>

											<div class='clearfix'></div>
											<div class='col-md-3 padding-left-none'>
												<span class='data-head'><u>Additional Charges</u></span>
											</div>

											<div class='clearfix'></div>";
                                if ($ratecardType == 1) {
                                $row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
                                                 <span class='data-value' >Storage Charges (CFT/Day) : <span id='storagecharges_$id'>$storagecharges</span>/-</span>
                                         </div> ";
                                } else {
                                $row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
                                                 <span class='data-value' >Storage Charges (per Day) : <span id='storagecharges_$id'>$storagecharges</span>/-</span>
                                         </div> ";   
                                }
                                                                                        
                                if ($ratecardType == 1) {
                                $row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
                                                    <span class='data-value' >Crating Charges (per CFT) : <span id='creatingcharges_$id'>$creatingcharges</span>/-</span>
                                            </div>
                                            
                                            <div class='col-md-3 padding-left-none'>
                                                    <span class='data-value' >Escort Charges (per Day) : <span id='escortcharges_$id'>$escortcharges</span>/-</span>
                                            </div>
                                            <div class='col-md-3 padding-left-none'>
                                                    <span class='data-value' >Handyman Charges (per Hour): <span id='handlomechargest_$id'>$handlomancharges</span>/-</span>
                                            </div>

                                            <div class='clearfix'></div>											
                                            <div class='col-md-3 padding-left-none'>
                                                    <span class='data-value' >Property Search (Rs) : <span id='propertysearch_$id'>$propertysearch</span>/-</span>
                                            </div>
                                            <div class='col-md-3 padding-left-none'>
                                                    <span class='data-value' >Brokerage (Rs) : <span id='brokerage_$id'>$brokerage</span>/-</span>
                                            </div>";
                                            if($cancelCharges!='') {
                                            $row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
                                                                                                            <span class='data-value' >Cancellation Charges (Rs) : <span id='cancellation_$id'>$cancelCharges</span>/-</span>
                                                                                                    </div>";
                                            }

                                            if($docketCharges!='') {
                                            $row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
                                                                                                            <span class='data-value' >Docket Charges (Rs) : <span id='docket_$id'>$docketCharges</span>/-</span>
                                                                                                    </div>";
                                            }
                                    
                                }
                                
                                
                                                                                        
				$row->cells [5]->value.=" </div></div>";
                                $row->cells [5]->value .=	"<div>
						<input id='buyersearch_booknow_buyer_id_$id' type='hidden' value=".Auth::User()->id." name='buyersearch_booknow_buyer_id_$id' >
						<input id='buyersearch_booknow_seller_id_$id' type='hidden' value=".$seller_id." name='buyersearch_booknow_seller_id_$id'>
						<input id='buyersearch_booknow_seller_price_$id' type='hidden' value=".$price." name='buyersearch_booknow_seller_price_$id'>
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
	
	public static function getRelocationIntBuyerLeadPostsList($serviceId,$post_status,$enquiry_type,$rel_int_type){
                
		$from_locations = array (
				"" => "From Location"
		);
		$to_locations = array (
				"" => "To Location"
		);
               
		$Query = DB::table ( 'relocationint_seller_posts as rsp' );		
		$Query->leftjoin ( 'relocationint_seller_selected_buyers as rsb', 'rsb.seller_post_id', '=', 'rsp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rsp.lkp_post_status_id' );
		$Query->leftjoin ( 'lkp_cities as cf', 'rsp.from_location_id', '=', 'cf.id' );
		$Query->leftjoin ( 'lkp_cities as ct', 'rsp.to_location_id', '=', 'ct.id' );		
		$Query->leftjoin ( 'lkp_quote_accesses as qa', 'rsp.lkp_access_id', '=', 'qa.id' );
                $Query->leftjoin ( 'lkp_payment_modes as pm', 'pm.id', '=', 'rsp.lkp_payment_mode_id' );
		$Query->leftjoin ( 'users as u', 'rsp.seller_id', '=', 'u.id' );
		$Query->where( 'rsp.lkp_post_status_id', 2);
		$Query->where( 'rsb.buyer_id', Auth::User ()->id);
		$Query->where('rsp.is_private', 0);
                $Query->where('rsp.lkp_international_type_id', $rel_int_type);
		
		if (isset ( $post_status ) && $post_status != '') {
			if($post_status == 0)
				$Query->whereIn ( 'rsp.lkp_post_status_id', array(1,2,3,4,5));
			else
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
		
		$Query->groupBy('rsp.id');
		$postResults = $Query->select ( 'rsp.*', 'u.username','u.id as user_id','ps.post_status', 'ct.city_name as toCity', 'cf.city_name as fromCity', 'pm.payment_mode as paymentmethod')->get ();
                //echo "<pre>"; print_r($postResults); die;
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
                if($rel_int_type == 1) {
                    $grid->add ( 'username', 'Seller Name', 'username' )->attributes(array("class" => "col-md-3 padding-left-none"));
                    $grid->add ( 'fromCity', '', false )->style ( "display:none" );
                    $grid->add ( 'toCity', '', false )->style ( "display:none" );
                    $grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
                    $grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
                } else {
                    $grid->add ( 'username', 'Seller Name', 'username' )->attributes(array("class" => "col-md-2 padding-left-none"));
                    $grid->add ( 'fromCity', 'From Location', false )->attributes(array("class" => "col-md-2 padding-left-none"));
                    $grid->add ( 'toCity', 'To Location', false )->attributes(array("class" => "col-md-2 padding-left-none"));
                    $grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
                    $grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-1 padding-left-none"));
                }
		
		$grid->add ( 'ratecard_type', '', false )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'dummy', '', 'show' )->style ( "display:none" );
		$grid->add ( 'from_location_id', 'From', 'from_location_id' )->style ( "display:none" );
		$grid->add ( 'to_location_id', 'To', 'to_location_id' )->style ( "display:none" );
		$grid->add ( 'lkp_international_type_id', 'lkp_international_type_id', 'lkp_international_type_id' )->style ( "display:none" );
                $grid->add ( 'paymentmethod', 'paymentmethod', 'paymentmethod' )->style ( "display:none" );
                $grid->add ( 'tracking', 'tracking', 'tracking' )->style ( "display:none" );
                $grid->add ( 'storage_charge_price', 'storage_charge_price', 'storage_charge_price' )->style ( "display:none" );
                $grid->add ( 'cancellation_charge_price', 'cancellation_charge_price', 'cancellation_charge_price' )->style ( "display:none" );
                $grid->add ( 'other_charge_price', 'other_charge_price', 'other_charge_price' )->style ( "display:none" );
                $grid->add ( 'crating_charges', 'crating_charges', 'crating_charges' )->style ( "display:none" );
                
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
                        $int_Type=$row->cells [10]->value;			
                        if($int_Type == 1) {
                            $row->cells [1]->attributes(array("class" => "col-md-3 padding-left-none"));   
                            $row->cells [2]->style ( 'display:none' );
                            $row->cells [3]->style ( 'display:none' );
                            $row->cells [4]->attributes(array("class" => "col-md-3 padding-left-none"));
                            $row->cells [5]->attributes(array("class" => "col-md-3 padding-left-none"));
                            
                        } else {
                            $row->cells [1]->attributes(array("class" => "col-md-2 padding-left-none"));   
                            $row->cells [2]->attributes(array("class" => "col-md-2 padding-left-none"));
                            $row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none"));
                            $row->cells [4]->attributes(array("class" => "col-md-2 padding-left-none"));
                            $row->cells [5]->attributes(array("class" => "col-md-1 padding-left-none"));                            
                        }
                        
			$row->cells [6]->attributes(array("class" => "col-md-1 padding-left-none"));
			$row->cells [8]->style ( 'display:none' );
			$row->cells [9]->style ( 'display:none' );
                        $row->cells [10]->style ( 'display:none' );
                        $row->cells [11]->style ( 'display:none' );
                        $row->cells [12]->style ( 'display:none' );
                        $row->cells [13]->style ( 'display:none' );
                        $row->cells [14]->style ( 'display:none' );
                        $row->cells [15]->style ( 'display:none' );
                        $paymentmode = $row->cells [11]->value;
                        $tracking = $row->cells [12]->value;
                        $storagec = $row->cells [13]->value;                        
                        if($storagec!='') {
                            $storage_charges = $row->cells [13]->value;
                        } else {
                            $storage_charges = '0.00';
                        }
                        $cancelc = $row->cells [14]->value;
                        if($cancelc!='') {
                            $cancel_charges = $row->cells [14]->value;
                        } else {
                            $cancel_charges = '0.00';
                        }
                        $otherc = $row->cells [15]->value;
                        if($otherc!='') {
                            $other_charges = $row->cells [15]->value;
                        } else {
                            $other_charges = '0.00';
                        }
                        $cratingc = $row->cells [16]->value;
                        if($cratingc!='') {
                            $crating_charges = $row->cells [16]->value;
                        } else {
                            $crating_charges = '0.00';
                        }
                           
			$msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$seller_post_id);
			$sellerPostDetails=RelocationIntSellerComponent::SellerPostDetails($seller_post_id);                        
			$seller_post=$sellerPostDetails['seller_post'][0];
			$seller_post_items=$sellerPostDetails['seller_post_items'];
			$seller_post_slabs=$sellerPostDetails['seller_post_slabs'];                        
                        
                        $track_type = CommonComponent::getTrackingType($tracking);
                        if ($paymentmode == 'Advance') {
                                $paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
                        } elseif ($paymentmode == 'Credit'){
                                $credit_days = CommonComponent::getCreditdays($seller_post_id,'seller_posts','seller_post_items');
                                $paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode.' | '.$credit_days[0]->credit_period.' '.$credit_days[0]->credit_period_units;
                        }else {
                                $paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode;
                        }
		
			$householdItems = 0;
			$vehicleItems = 0;
                        
			$url = url().'/buyerbooknowforsearch/'.$seller_post_id;
			$row->cells [7]->value = "<div class='col-md-12 padding-none'>						
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
                                <a id='".$seller_post_id."' data-sellerlistid=$seller_post_id class='viewcount_show-data-link view_count_update' data-quoteId='$seller_post_id' ><span class='show-icon spot_transaction_details_list'>+</span><span class='hide-icon'>-</span> Details</a>
                                </div>
                            </div>
                    
                    <div class='details-block-div clearfix show-data-div' style='display:none;' id='spot_transaction_details_view_$seller_post_id'>
                    <div class='table-div table-style1 padding-none margin-none'>";
                       if($int_Type == 1) { 
                            $row->cells [7]->value .='<div class="col-md-12 tab-modal-head padding-none">
                                <h3><i class="fa fa-map-marker"></i> '.$row->cells [2]->value.' to '.$row->cells [3]->value.'</h3>
                            </div><div class="table-div table-style1 padding-none margin-none"><div class="table-heading inner-block-bg">
                                <div class="col-md-6 padding-left-none">Weight Bracket (KGs)</div>
                                <div class="col-md-3 padding-left-none">Freight Charges (Rs/KG)</div>
                                <div class="col-md-3 padding-none">O & D Charges (Rs/CFT)</div>
                            </div><div class="table-data">';
						   if(is_array($seller_post_slabs)) {
							   foreach ($seller_post_slabs as $seller_post_slab) {
								   $row->cells [7]->value .= "
								<div class='table-row inner-block-bg'>
								<div class='col-md-6 padding-left-none'>" . $seller_post_slab->min_slab_weight . "-" . $seller_post_slab->max_slab_weight . "</div>
								<div class='col-md-3 padding-left-none'>$seller_post_slab->freight_charges /-</div>
								<div class='col-md-3 padding-left-none'>$seller_post_slab->od_charges /-</div>    
								</div>";
							   }
						   }
                       } else {
                            $row->cells [7]->value .="<div class='clearfix'></div><div class='table-div table-style1 padding-none margin-top'>
                                <div class='table-heading inner-block-bg'>
                                        <div class='col-md-3 padding-left-none'>Shipment Type</div>
                                        <div class='col-md-2 padding-left-none'>Volume</div>
                                        <div class='col-md-3 padding-left-none'>O &amp; D Charges</div>
                                        <div class='col-md-2 padding-left-none'>Freight (Flat)</div>
                                        <div class='col-md-2 padding-left-none'>Transit Days</div>'
                                </div>";
                            foreach($seller_post_items as $seller_post_item_ship){
                            $row->cells [7]->value .="
                                <div class='table-data'>
                                        <div class='table-row inner-block-bg'>
                                                <div class='col-md-3 padding-left-none'>$seller_post_item_ship->shipment_type</div>
                                                <div class='col-md-2 padding-left-none'>$seller_post_item_ship->volume</div>
                                                <div class='col-md-3 padding-left-none'>$seller_post_item_ship->od_charges /- (Rs per CBM)</div>
                                                <div class='col-md-2 padding-none'>$seller_post_item_ship->freight_charges /-</div>
                                                <div class='col-md-2 padding-left-none'>$seller_post_item_ship->transitdays $seller_post_item_ship->units</div>
                                        </div>
                            </div>";
                            }
                           
                       }
                        
			$row->cells [7]->value .="</div></div><div class='clearfix'></div>";
                        
                        if($int_Type == 1) {
                                $row->cells [7]->value .=" <div class='col-md-3 form-control-fld padding-left-none'>
                                 <span class='data-head'>Tracking</span>
                                 <span class='data-value'>$track_type</span>
                                 </div><div class='col-md-3 form-control-fld padding-left-none'>
                                 <span class='data-head'>Payment</span>
                                 <span class='data-value'>$paymentType</span>
                                 </div><div class='col-md-3 form-control-fld padding-left-none'>
                                 <span class='data-head'>Post Type</span>
                                 <span class='data-value'>Private</span>
                                 </div><div class='col-md-3 form-control-fld padding-left-none'>";
						if(isset($seller_post->transitdays) && $seller_post->transitdays!='') {
							$row->cells [7]->value .= "<span class='data-head'>Transit Days</span>
                                 <span class='data-value'>" . $seller_post->transitdays . " " . $seller_post->units . "</span>";
						}
								$row->cells [7]->value .="</div>";
                                if(isset($seller_post->terms_conditions) && $seller_post->terms_conditions!='') {
                                $row->cells [7]->value .="<div class='col-md-12 form-control-fld padding-left-none'>
                                <span class='data-head'>Terms & Conditions</span>
                                <span class='data-value'>$seller_post->terms_conditions</span>
                                </div>";
                                 }	
                        }
                         if($int_Type == 2) {
                                $row->cells [7]->value .=" <div class='col-md-3 padding-left-none'>
                                                            <span class='data-head'>Crating Charges (per CFT) : $crating_charges/-/-</span>
                                                      </div>";
                         }
                        $row->cells [7]->value .="<div class='clearfix'></div><div class='col-md-3 padding-left-none'>
                                                                        <span class='data-head'><u>Additional Charges</u></span>
                                                                </div>
                                                                <div class='clearfix'></div> ";
                                if($int_Type == 1) {
                                       $row->cells [7]->value .=" <div class='col-md-3 padding-left-none'>
                                                                   <span class='data-head'>Storage Charges (Rs) : $storage_charges/-</span>
                                                             </div>";
                                }
                               $row->cells [7]->value .="   <div class='col-md-3 padding-left-none'>
                                                                        <span class='data-head'>Cancellation Charges (Rs) : $cancel_charges/-</span>
                                                                </div>

                                                                <div class='col-md-3 padding-left-none'>
                                                                        <span class='data-head'>Other Charges  (Rs) : $other_charges/-</span>
                                                                </div>";
			
			$row->cells [7]->value .="</div>";
			
			$row->cells [7]->value .="</div></div>";
			
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
			
			$buyer_post_details = DB::table ( 'relocationint_buyer_posts' )->where ( 'id', $buyer_post_id )->get ();
			
			if($buyer_post_details[0]->lkp_international_type_id==1){
                $buyer_post_inventory_details =DB::table('relocationint_buyer_post_air_cartons as rbpac')
                ->leftjoin ( 'lkp_air_carton_types as lact', 'lact.id', '=', 'rbpac.lkp_air_carton_type_id' )
                ->where('rbpac.buyer_post_id',$buyer_post_id)
                ->select('rbpac.number_of_cartons','lact.carton_type','lact.carton_description')->get();
            }
			else{
				$buyer_post_inventory_details = DB::table ( 'relocationint_buyer_post_inventory_particulars' )->where ( 'buyer_post_id', $buyer_post_id )->get ();
			}


			$Query = DB::table ( 'relocationint_buyer_quote_sellers_quotes_prices as rsqb' );
			$Query->leftjoin ( 'users as u', 'u.id', '=', 'rsqb.seller_id' );
			
                        $Query->leftjoin('relocationint_seller_posts as sp', 'sp.id', '=', 'rsqb.seller_post_id');
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
		
		$buyer_post_edit_seller = DB::table('relocationint_buyer_quote_sellers_quotes_prices')
  		->where('relocationint_buyer_quote_sellers_quotes_prices.buyer_quote_id', $buyer_post_id)
  		->select('relocationint_buyer_quote_sellers_quotes_prices.*')
		->get();
		return count($buyer_post_edit_seller);
		
	}
	


	/**
	return $cartons_total->;
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
        
        
        
        /**
	 * Buyer Orders Detail Page in Relocation pet Page
	 * Retrieval of data related to Buyer Orders
	 *
	 */
	public static function getRelocationintBuyerOrderDetails($serviceId, $orderId, $user_id) {
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
			 case RELOCATION_INTERNATIONAL :
                                $query->leftJoin('lkp_cities as lc', 'lc.id', '=', 'orders.from_city_id');
                                $query->leftJoin('lkp_cities as lcity', 'lcity.id', '=', 'orders.to_city_id');	
                                if($order_type[0]->lkp_order_type_id==1) {
                                $query->leftJoin('relocationint_buyer_posts as rbq', 'rbq.id', '=', 'orders.buyer_quote_id');                                  
                                } else {
                                    $query->leftJoin('term_buyer_quote_items as tbqi', 'tbqi.id', '=', 'orders.buyer_quote_item_id');
                                    $query->leftJoin('term_buyer_quotes as tbq', 'tbq.id', '=', 'tbqi.term_buyer_quote_id');
                                }
                                $query->leftjoin('users as u', 'u.id', '=', 'orders.seller_id')->where('orders.id', '=', $orderId);             		
                                $query->where('orders.buyer_id', '=', $user_id);      
                                if($order_type[0]->lkp_order_type_id==1) {                                
                                    $orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', 'op.*', 
                                        'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lkp_payment_modes.payment_mode', 'oi.total_amt as inv_total','rbq.transaction_id as postid','rbq.total_cartons_weight as totalweightcarton', 'rbq.lkp_property_type_id as propertytypeId','rbq.origin_storage',
                                        'rbq.destination_storage','rbq.origin_handyman_services','rbq.destination_handyman_services','rbq.insurance')->first();
                                
                                } else {             		
                                    $orders['orderDetails'] = $query->select('tbq.transaction_id as postid','tbq.id as termbuyerid','tbq.lkp_post_ratecard_type as lkp_post_ratecard_type_id','tbqi.lkp_load_type_id as lkp_load_category_id','oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lkp_payment_modes.payment_mode', 'oi.total_amt as inv_total',
                                            'tbqi.number_loads', 'tbqi.avg_kg_per_move')->first();
                                }
             		
		            break;
                case RELOCATION_GLOBAL_MOBILITY :                                
                                $query->leftJoin('lkp_cities as lcity', 'lcity.id', '=', 'orders.to_city_id');	
                                if($order_type[0]->lkp_order_type_id==1) {
                                $query->leftJoin('relocationgm_buyer_posts as rbq', 'rbq.id', '=', 'orders.buyer_quote_id');                                  
                                } else {
                                    $query->leftJoin('term_buyer_quote_items as tbqi', 'tbqi.id', '=', 'orders.buyer_quote_item_id');
                                    $query->leftJoin('term_buyer_quotes as tbq', 'tbq.id', '=', 'tbqi.term_buyer_quote_id');
                                }
                                $query->leftjoin('users as u', 'u.id', '=', 'orders.seller_id')->where('orders.id', '=', $orderId);             		
                                $query->where('orders.buyer_id', '=', $user_id);      
                                if($order_type[0]->lkp_order_type_id==1) {                                
                                    $orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', 'op.*', 
                                        'os.order_status', 'lcity.city_name as to_city', 'lkp_payment_modes.payment_mode', 'oi.total_amt as inv_total','rbq.transaction_id as postid')->first();
                                
                                } else {             		
                                    $orders['orderDetails'] = $query->select('tbq.transaction_id as postid','tbq.id as termbuyerid','tbq.lkp_post_ratecard_type as lkp_post_ratecard_type_id','tbqi.lkp_load_type_id as lkp_load_category_id','oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', 'op.*', 'os.order_status',  'lcity.city_name as to_city', 'lkp_payment_modes.payment_mode', 'oi.total_amt as inv_total',
                                            'tbqi.number_loads', 'tbqi.avg_kg_per_move')->first();
                                }
             		
		            break;
					}
			return $orders;
			 
		} catch ( Exception $exc ) {
			
		}
		
	}
	
}
