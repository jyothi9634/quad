<?php

namespace App\Components\TruckHaul;
 
use DB;
use App\Models\TruckhaulBuyerQuoteItemView;
use App\Models\TruckhaulBuyerQuoteItem;
use App\Models\CartItem;
use App\Models\TruckhaulBuyerQuoteSellersQuotesPrice;
use App\Models\SellerPostItem;

use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use Auth;
use App\Http\Requests;
use Input;
use Config;
use File;
use Session;
use Redirect;
use Log;
use App\Components\CommonComponent;
use App\Components\SellerComponent;
use App\Components\BuyerComponent;
use App\Components\MessagesComponent;
use App\Models\User;
use App\Components\Search\BuyerSearchComponent;
use App\Models\TruckhaulSearchTerm;
use App\Models\ViewCartItem;
use App\Components\Matching\BuyerMatchingComponent;



class TruckHaulBuyerComponent {

	
	/**
	 * Buyer Posts List Page
	 * Retrieval of data related to buyer posts list items to populate in the buyer list widget
	 * Displays a grid with a list of all seller posts
	 */
	public static function getTruckHaulBuyerPostsList($service_id, $post_status, $enquiry_type) {

		// Filters values to populate in the page
		$from_locations = array (
				"" => "From Location"
		);
		$to_locations = array (
				"" => "To Location"
		);
		$posted_for_types = array (
				"" => "Posted For"
		);
		$load_types = array (
				"" => "Load Type"
		);
		$from_date = '';
		$to_date = '';
		
		// query to retrieve buyer posts list and bind it to the grid
		$Query = DB::table ( 'truckhaul_buyer_quote_items as bqi' );
		$Query->join ( 'lkp_load_types as lt', 'lt.id', '=', 'bqi.lkp_load_type_id' );
		$Query->join ( 'lkp_vehicle_types as vt', 'vt.id', '=', 'bqi.lkp_vehicle_type_id' );
		$Query->join ( 'lkp_post_statuses as ps', 'ps.id', '=', 'bqi.lkp_post_status_id' );
		$Query->join ( 'lkp_cities as cf', 'bqi.from_city_id', '=', 'cf.id' );
		$Query->join ( 'lkp_cities as ct', 'bqi.to_city_id', '=', 'ct.id' );
		$Query->join ( 'truckhaul_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
		$Query->join ( 'lkp_quote_accesses as lqa', 'lqa.id', '=', 'bq.lkp_quote_access_id' );
		$Query->where( 'bqi.created_by', Auth::User ()->id );
		$Query->where('bqi.lkp_post_status_id','!=',6);
		$Query->where('bqi.lkp_post_status_id','!=',7);
		$Query->where('bqi.lkp_post_status_id','!=',8);


		// conditions to make search

		/*
		 * if (isset ( $enquiry_type ) && $enquiry_type != '') {
		* $query->where ( 'orders.lkp_enquiry_type_id', '=', $enquiry_type );
		* }
		*/
		
		if (isset ( $post_status ) && $post_status != '') {
			if($post_status == 0)
				$Query->whereIn('bqi.lkp_post_status_id', array(1,2,3,4,5));
			else
				$Query->where('bqi.lkp_post_status_id', '=', $post_status);
		}

		if (isset ( $_GET ['dispatch_date'] ) && $_GET ['dispatch_date'] != '') {
			$commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['dispatch_date']);
			$Query->where ( 'bqi.dispatch_date', '>=', $commonDispatchDate );
			$from_date = $commonDispatchDate;
			//echo "From Date :"; echo $from_date;die();
		}
	 	if (isset ( $_GET ['delivery_date'] ) && $_GET ['delivery_date'] != '') {
			$commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['delivery_date']);
			$Query->where ( 'bqi.dispatch_date', '<=', $commonDeliveryhDate);
			$to_date = $commonDeliveryhDate;
		}

		$postResults = $Query->select ( 'bqi.*', 'lt.load_type', 'vt.vehicle_type', 'ps.post_status', 'ct.city_name as toCity', 'cf.city_name as fromCity','bq.lkp_quote_access_id','lqa.quote_access','bq.is_commercial')->get ();
		//echo "<pre>"; print_r($postResults);die();
		// Functionality to handle filters based on the selection starts
		foreach ( $postResults as $post ) {
			$buyer_quotes = DB::table ( 'truckhaul_buyer_quote_items' )->leftJoin( 'truckhaul_buyer_quotes as bq', 'bq.id', '=', 'truckhaul_buyer_quote_items.buyer_quote_id' )->where ( 'truckhaul_buyer_quote_items.id', $post->id )->select ( 'truckhaul_buyer_quote_items.*','bq.lkp_quote_access_id' )->get ();
				
			foreach ( $buyer_quotes as $quotes ) {
				//echo "<pre>"; print_r($quotes);die();
				if (! isset ( $from_locations [$quotes->from_city_id] )) {
					$from_locations [$quotes->from_city_id] = DB::table ( 'lkp_cities' )->where ( 'id', $quotes->from_city_id )->pluck ( 'city_name' );
				}
				if (! isset ( $to_locations [$quotes->to_city_id] )) {
					$to_locations [$quotes->to_city_id] = DB::table ( 'lkp_cities' )->where ( 'id', $quotes->to_city_id )->pluck ( 'city_name' );
				}
				if (! isset ( $load_types [$quotes->lkp_load_type_id] )) {
					$load_types [$quotes->lkp_load_type_id] = DB::table ( 'lkp_load_types' )->where ( 'id', $quotes->lkp_load_type_id )->pluck ( 'load_type' );
				}
				if (! isset ( $posted_for_types [$quotes->lkp_quote_access_id] )) {
					$posted_for_types [$quotes->lkp_quote_access_id] = DB::table ( 'lkp_quote_accesses' )->where ( 'id', $quotes->lkp_quote_access_id )->pluck ( 'quote_access' );
				}
			}
		}
		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations = CommonComponent::orderArray($to_locations);
		$load_types = CommonComponent::orderArray($load_types);
		
		$grid = DataGrid::source ( $Query );

		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'dispatch_date', 'Reporting Date', 'dispatch_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'fromCity', 'From', 'fromCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'toCity', 'To', 'toCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'load_type', 'Load Type', 'load_type' )->attributes(array("class" => "col-md-2 padding-right-none"));
		$grid->add ( 'quote_access', 'Posted For', 'quote_access' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'created_by', 'dummycolumn', 'created_by' )->style ( "display:none" );
	 	$grid->add ( 'post_status', 'Status', 'post_status' )->attributes(array("class" => "ccol-md-1 padding-left-none"));
		$grid->add ( 'lkp_quote_access_id', 'Buyer access_id', 'lkp_quote_access_id' )->style ( "display:none" );
		$grid->add ( 'buyer_quote_id', 'Buyer id', 'buyer_quote_id' )->style ( "display:none" );
		$grid->add ( 'lkp_post_status_id', 'Post status id', 'lkp_post_status_id' )->style ( "display:none" );
		$grid->add ( 'from_city_id', 'rom city id', 'from_city_id' )->style ( "display:none" );
		$grid->add ( 'lkp_quote_price_type_id', 'Price Type', true )->style ( "display:none" );
		$grid->add ( 'to_city_id', 'to_city_id', 'to_city_id' )->style ( "display:none" );
                $grid->add ( 'is_commercial', 'is_commercial', 'is_commercial' )->style ( "display:none" );

		$grid->orderBy ( 'bqi.id', 'desc' );
		$grid->paginate ( 5 );

		$grid->row ( function ($row) {
			$buyer_quote_id = $row->cells [0]->value;
			$row->cells [0]->style ( 'display:none' );
			$dispatchDate = $row->cells [1]->value;
			$row->cells [7]->style ( 'width:100%' );
			$buyer_access_id = $row->cells [8]->style ( 'display:none' );
			$buyer_id = $row->cells [9]->style ( 'display:none' );
			$buyerCountId = count (TruckHaulBuyerComponent::getTHBuyerQuoteSellersQuotesPricesFromId( $buyer_quote_id ) );
			$post_status_id = $row->cells [10]->style ( 'display:none' );

			$arraySellerIds = TruckHaulBuyerComponent::getTHSellerIds($row->cells[9]->style ( 'display:none' ));
			$arrayBuyerLeads = TruckHaulBuyerComponent::getTHLeadsForBuyer($row->cells[11]->style ( 'display:none' ), $arraySellerIds);
			$countview = TruckHaulBuyerComponent::updateTHBuyerQuoteDetailsViews($buyer_quote_id);
			
			$priceType = $row->cells [12]->style ( 'display:none' );
			if ($priceType == '2') {
				$postQuoteType = 'Response';
			} else {
				$postQuoteType = 'Quotes';
			}
			
			if ($buyer_access_id == "2" && $post_status_id == "2")  {				
				$editOption = "<a href='/editbuyerquote/$buyer_id/$buyer_quote_id'><i class='fa fa-edit' title='Edit'></i></a>";
				//$buyer_id = "";
			} else {
				$editOption = " ";
				// $buyer_id ='buyerposts';
			}
			
			$data_link = url()."/getbuyercounteroffer/$buyer_quote_id";	
                        
            $matchedSellerPosts = BuyerMatchingComponent::getMatchedResults(ROAD_TRUCK_HAUL,$buyer_quote_id);

            $matchedIds = array();
            foreach($matchedSellerPosts as $matchedSellerPost){
                    $matchedIds[] = $matchedSellerPost->seller_post_id;
            }

            $getSellerLeadData = DB::table('truckhaul_seller_post_items as spi');
            $getSellerLeadData->leftjoin('truckhaul_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
            $getSellerLeadData->whereIn('spi.id', $matchedIds);
            $getSellerLeadData->where('spi.is_private', 0);
            $getSellerLeadData->select('sp.transaction_id as transaction_no','spi.*');
            $arraySellerLeadsData = $getSellerLeadData->get();

			$leadscount = count($arraySellerLeadsData);

			$row->cells [0]->style ( 'display:none' );
            $row->cells [1]->style ( 'display:none' );
            $row->cells [2]->style ( 'display:none' );
            $row->cells [3]->style ( 'display:none' );
            $row->cells [4]->style ( 'display:none' );
            $row->cells [5]->style ( 'display:none' );
            $row->cells [6]->style ( 'display:none' );
            $row->cells [7]->style ( 'width:100%' );
            $row->cells [8]->style ( 'display:none' );
            $row->cells [9]->style ( 'display:none' );
            $row->cells [10]->style ( 'display:none' );
            $row->cells [11]->style ( 'display:none' );
            $row->cells [12]->style ( 'display:none' );
            $row->cells [13]->style ( 'display:none' );
            $row->cells [14]->style ( 'display:none' );

            $buyer_quote_id = $row->cells [0]->value; 
            $fromCity = $row->cells [2]->value;
            $toCity = $row->cells [3]->value;
            $posted_for = $row->cells [5]->value; //exit;
            $load_type = $row->cells [4]->value;
            $status = $row->cells [7]->value;
            $lkp_psot_status_condition = $row->cells   [10]->value;
            $msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyer_quote_id);
             //count for buyer documents
            $serviceId = Session::get('service_id');
            
            $fromLocationId = $row->cells [11]->value;
            $toLocationId = $row->cells [13]->value;
            $is_commercial = $row->cells [14]->value;
            
            $docs_buyer    =   CommonComponent::getGsaDocuments(1,$serviceId,$buyer_quote_id,$fromLocationId,$toLocationId,$is_commercial);   
            
			$row->cells [7]->value = "<div class=''><a href='/getbuyercounteroffer/$buyer_quote_id'>
										<div class='col-md-2 padding-left-none'>
										<span class='lbl padding-8'></span>".CommonComponent::checkAndGetDate($dispatchDate)."
											
										</div>
										<div class='col-md-2 padding-left-none'>$fromCity</div>
										<div class='col-md-2 padding-left-none'>$toCity</div>
										<div class='col-md-2 padding-right-none'>$load_type</div>
										<div class='col-md-2 padding-left-none'>$posted_for</div>
										<div class='col-md-1 padding-none'> $status </div></a>";
										//onclick='buyerpostcancel($buyer_quote_id)'
			if ($lkp_psot_status_condition == OPEN) {
				$row->cells [7]->value .= " <div class='col-md-1 padding-none text-right'>
						$editOption
						<a href='#' data-target='#cancelbuyerpostmodal' data-toggle='modal' onclick='setcancelbuyerpostid(".$buyer_quote_id.")' ><i class='fa fa-trash buyerpostdelete' title='Delete'></i></a>						
						</div>
				<div class='clearfix'></div>";
			}else{
			$row->cells [7]->value .= '<div class="clearfix"></div>';
			}
			
			$row->cells [7]->value .= "	<div class='pull-left'>
											<div class='info-links'>
												<a href='/getbuyercounteroffer/$buyer_quote_id?type=messages'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>".count($msg_count['result'])."</span></a>
												<a href='/getbuyercounteroffer/$buyer_quote_id?type=quotes'><i class='fa fa-file-text-o'></i> Quotes<span class='badge'>$buyerCountId</span></a>
												<a href='/getbuyercounteroffer/$buyer_quote_id?type=leads'><i class='fa fa-thumbs-o-up'></i> Leads<span class='badge'>$leadscount</span></a>
												<a href='#'><i class='fa fa-line-chart'></i> Market Analytics<span class='badge'>0</span></a>
												<a href='/getbuyercounteroffer/$buyer_quote_id?type=documentation'><i class='fa fa-file-text-o'></i> Documentation<span class='badge'>".count($docs_buyer)."</span></a>
												
											</div>
										</div>
										<div class='pull-right text-right'>
											<div class='info-links'>
												<a><span class='views red'><i class='fa fa-eye' title='Views'></i> $countview </span></a>
											</div>
										</div>
									</div>";

			
			$row->attributes(array("class" => ""));			
				
		} );
 
		// Functionality to build filters in the page starts
		$filter = DataFilter::source ( $Query );
		$filter->add ( 'bqi.from_city_id', '', 'select' )->options ( $from_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'bqi.to_city_id', '', 'select' )->options ( $to_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'bqi.lkp_quote_access_id', 'Posted For', 'select' )->options ( $posted_for_types )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'bqi.lkp_load_type_id', 'Load Type', 'select' )->options ( $load_types )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->submit ( 'search' );
		$filter->reset ( 'reset' );
		$filter->build ();
		// Functionality to build filters in the page ends

		$result = array ();
		$result ['grid'] = $grid;
		$result ['filter'] = $filter;
		return $result;
	}

	// buyer search for seller posts result component
	public static function getTruckHaulBuyerSearchList($request, $serviceId)
	{	
		try 
		{		
			$request->is_dispatch_flexible = $request->exists('dispatch_flexible_hidden')? $request->dispatch_flexible_hidden:0;

			// query to retrieve seller posts list and bind it to the grid--for filters
			$from_locations = ["" => "From Location"];
			$to_locations 	= ["" => "To Location"];
			$load_types 	= ["" => "Load Type"];
			$vehicle_types 	= ["" => "Vehicle Type"];
			$sellerNames 	= [];
			$paymentMethods = [];
			$prices 		= [];

			$trackingfilter = [];     
            if($request->has('tracking'))
                $trackingfilter[] = $request->tracking;
                
            if($request->has('tracking1'))
            	$trackingfilter[] = $request->tracking1;
           	
           	if(count($trackingfilter)>0)
            	$request->merge(['trackingfilter' => $trackingfilter]);

            if($request->exists('filter_set') && $request->filter_set == 1){
				
				if($request->has('date_flexiable'))
					$request['from_date'] = $request['date_flexiable'];

                $Query_buyers_for_sellers = BuyerSearchComponent::search($roleId=null, $serviceId,$statusId=null, $request );
				$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();

			} else {
                                    
                // Below script for buyer search for seller posts join query --for Grid				
				$Query_buyers_for_sellers = BuyerSearchComponent::search(
					$roleId=null, $serviceId, $statusId=null, $request 
				);

				$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();
				
				if( $request->has('lkp_vehicle_type_id') && $request->has('lkp_load_type_id') && 
					$request->has('from_location_id') && $request->has('to_location_id') && 
					$request->has('from_date') && $request->has('from_location') && 
					$request->has('is_commercial'))
				{
					$sellerpost_for_buyers  =  new TruckhaulSearchTerm();
					$sellerpost_for_buyers->user_id = Auth::id();
					$sellerpost_for_buyers->from_city_id 	= $request->from_location_id;
					$sellerpost_for_buyers->to_city_id 		= $request->to_location_id;
					$sellerpost_for_buyers->dispatch_date 	= $request->from_date;
					$sellerpost_for_buyers->lkp_load_type_id 	= $request->lkp_load_type_id;
					$sellerpost_for_buyers->lkp_vehicle_type_id = $request->lkp_vehicle_type_id;
					$sellerpost_for_buyers->quantity 	= $request->quantity;
					$sellerpost_for_buyers->created_at 	= date ( 'Y-m-d H:i:s' );
					$sellerpost_for_buyers->created_ip 	= $_SERVER ['REMOTE_ADDR'];
					$sellerpost_for_buyers->created_by 	= Auth::id();
					$sellerpost_for_buyers->save();								

					// Storing Request Data to Session
					session()->put([
						'searchMod' => [
							'dispatch_date_buyer'	=> $request->from_date,
							'vehicle_type_buyer'	=> $request->lkp_vehicle_type_id,
							'load_type_buyer'		=> $request->lkp_load_type_id,
							'from_city_id_buyer'	=> $request->from_location_id,
							'to_city_id_buyer'		=> $request->to_location_id,
							'from_location_buyer'	=> $request->from_location,
							'to_location_buyer'		=> $request->to_location,
							'quantity_buyer'		=> $request->quantity,
							'capacity_buyer'		=> $request->capacity,
							'fdispatch_date_buyer'	=> $request->dispatch_flexible_hidden,
		                    'is_commercial_date_buyer'	=> $request->is_commercial
						]
					]);
				}
			}			
			
			if (empty ( $Query_buyers_for_sellers_filter )) {
				CommonComponent::searchTermsSendMail ();
				session()->forget('layered_filter_to_location');
				session()->forget('layered_filter_from_location');
				session()->forget('layered_filter_payments');
				session()->forget('show_layered_filter');
            }
			
			// Below script for filter data getting from queries --for filters
			foreach ( $Query_buyers_for_sellers_filter as $seller_post_item ) {
                
                // adding New Session variable to existing session array
                session()->put('show_layered_filter', 1);

                $no_loads  = 1;
				$prices[] = $seller_post_item->price;
                if(! isset ( $from_locations [$seller_post_item->from_location_id])):
					$from_locations [$seller_post_item->from_location_id] = DB::table('lkp_cities')->where ( 'id', $seller_post_item->from_location_id )->pluck('city_name');
				endif;

				if (! isset ( $to_locations [$seller_post_item->to_location_id] )):
					$to_locations [$seller_post_item->to_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->to_location_id)->pluck('city_name');
				endif;

				if(!isset($load_types [$seller_post_item->lkp_load_type_id])):
					$load_types[$seller_post_item->lkp_load_type_id] = DB::table('lkp_load_types' )->where ( 'id', $seller_post_item->lkp_load_type_id )->pluck ( 'load_type' );
				endif;

				if(!isset ( $vehicle_types [$seller_post_item->lkp_vehicle_type_id] )):
					$vehicle_types [$seller_post_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types' )->where ( 'id', $seller_post_item->lkp_vehicle_type_id )->pluck ( 'vehicle_type' );
				endif;

				if( $request->exists('is_search') ):
					if (! isset ( $sellerNames [$seller_post_item->seller_id] )) {
						$sellerNames[$seller_post_item->seller_id] = $seller_post_item->username;
					}
					if (! isset ( $paymentMethods [$seller_post_item->lkp_payment_mode_id] )) {
						$paymentMethods[$seller_post_item->lkp_payment_mode_id] = $seller_post_item->paymentmethod;
					}
					session()->put('layered_filter_payments', $paymentMethods);
					session()->put('layered_filter', $sellerNames);
				endif;
			}

			if (isset ( $_REQUEST ['price'] ) && $_REQUEST ['price'] != '') {
				$splitprice = explode("    ",$_REQUEST ['price']);
				$from = trim(filter_var($splitprice[0],FILTER_SANITIZE_NUMBER_INT),"-");
				$to = trim(filter_var($splitprice[1],FILTER_SANITIZE_NUMBER_INT),"-");
				$request->merge([
					'price_from' => $from,
					'price_to' => $to
				]);
			}else{
				if(!empty($prices)){
					$request->merge([
						'price_from' => floor(min($prices)),
						'price_to' => ceil(max($prices))
					]);
					$request->merge([
						'filter_price_from' => $request->price_from,
						'filter_price_to' => $request->price_to
					]);
				}else{
					$request->merge([
						'price_from' => 0,
						'price_to' => 1000
					]);
				}
			}

			$result = $Query_buyers_for_sellers->get ();
			$gridBuyer = DataGrid::source ( $Query_buyers_for_sellers );
			$gridBuyer->add ( 'id', 'ID', true )->style ( "display:none" );
			$gridBuyer->add ( 'username', 'Vendor Name', true )->attributes(array("class" => "col-md-3 padding-left-none"));
			$gridBuyer->add ( 'status', 'Vendor Rating', false )->attributes(array("class" => ""))->style ( "display:none" );
			$gridBuyer->add ( 'price1', 'Price1', true )->style ( "display:none" );
			$gridBuyer->add ( 'test', 'Status', true )->style ( "display:none" );
			$gridBuyer->add ( 'fromcity', 'From city', true )->style ( "display:none" );
			$gridBuyer->add ( 'tocity', 'From city', true )->style ( "display:none" );
			$gridBuyer->add ( 'load_type', 'Load Type', true )->style ( "display:none" );
			$gridBuyer->add ( 'vehicle_type', 'Vehicle Type', true )->attributes(array("class" => "col-md-3 padding-left-none"));
			$gridBuyer->add ( 'from_date', 'Valid From', true )->style ( "display:none" );
			$gridBuyer->add ( 'to_date', 'Valid To', true )->style ( "display:none" );
			$gridBuyer->add ( 'tracking', 'Tracking', true )->style ( "display:none" );
			$gridBuyer->add ( 'paymentmethod', 'Payment Mode', true )->style ( "display:none" );
			$gridBuyer->add ( 'created_by', 'created by', 'created_by' )->style ( "display:none" );
			$gridBuyer->add ( 'transitdays', 'Transit Days', true )->attributes(array("class" => "col-md-3 padding-left-none"));
			$gridBuyer->add ( 'price', 'Price', true )->attributes(array("class" => "col-md-3 padding-left-none"));			
			$gridBuyer->add ( 'transaction_id', 'Transaction Id', 'transaction_id' )->style('display:none');
			$gridBuyer->add ( 'cancellation_charge_text', 'Cancell Text', 'cancellation_charge_text' )->style('display:none');
			$gridBuyer->add ( 'cancellation_charge_price', 'Cancellation Charge', 'cancellation_charge_price' )->style('display:none');
			$gridBuyer->add ( 'docket_charge_text', 'transaction_id', 'docket_charge_text' )->style('display:none');			
			$gridBuyer->add ( 'docket_charge_price', 'Docket Charge', 'docket_charge_price' )->style('display:none');				
			$gridBuyer->add ( 'other_charge1_text', 'Docket Charge', 'other_charge1_text' )->style('display:none');			
			$gridBuyer->add ( 'other_charge1_price', 'Other charges1', 'other_charge1_price' )->style('display:none');				
			$gridBuyer->add ( 'other_charge2_text', 'Other charges2 ', 'other_charge2_text' )->style('display:none');				
			$gridBuyer->add ( 'other_charge2_price', 'Other charges2', 'other_charge2_price' )->style('display:none');				
			$gridBuyer->add ( 'other_charge3_text', 'Other charges3', 'other_charge3_text' )->style('display:none');				
			$gridBuyer->add ( 'other_charge3_price', 'Other charges3', 'other_charge3_price' )->style('display:none');
			$gridBuyer->add ( 'lkp_vehicle_type_id', 'Vehicle type id', 'Vehicle type id' )->style('display:none');
			$gridBuyer->add ( 'vehicle_number', 'Vehicle Number', 'vehicle_number' )->style('display:none');
                        $gridBuyer->add ( 'transitunits', 'transit units', 'transitunits' )->style('display:none');
	

			$gridBuyer->orderBy ( 'id', 'desc' );
			$gridBuyer->paginate ( 5 );
				
			$gridBuyer->row ( function ($row) {
				$row->cells [0]->style ( 'display:none' );
				$row->cells [1]->style ( 'display:none' );
				$row->cells [2]->style ( 'display:none' );
				$row->cells [3]->style ( 'display:none' );
				$row->cells [4]->style ( 'width:100%' );
				$row->cells [5]->style ( 'display:none' );
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
                
                //pricing columns
                $row->cells [17]->style ( 'display:none' );
                $row->cells [18]->style ( 'display:none' );
                $row->cells [19]->style ( 'display:none' );
                $row->cells [20]->style ( 'display:none' );
                $row->cells [21]->style ( 'display:none' );
                $row->cells [22]->style ( 'display:none' );
                $row->cells [23]->style ( 'display:none' );
                $row->cells [24]->style ( 'display:none' );
                $row->cells [25]->style ( 'display:none' );
                $row->cells [26]->style ( 'display:none' );
                $row->cells [27]->style ( 'display:none' );
                $row->cells [28]->style ( 'display:none' );
                $row->cells [29]->style ( 'display:none' );
                
                //number of loads calculation client new doc issue(11-03-2016)
                $vehicle_type_id = $row->cells [27]->value;
                $vehiclenumber = $row->cells [28]->value;
                $transit_units_buyer = $row->cells [29]->value;
                $noofloads  = 1;
                
				$id = $row->cells [0]->value;
				$vendorname = $row->cells [1]->value;
				$price = $row->cells [15]->value;
				$fromlocation = $row->cells [5]->value;
				$tolocation = $row->cells [6]->value;
				$loadtype = $row->cells [7]->value;
				$vehicletype = $row->cells [8]->value;
				$validfrom = $row->cells [9]->value;
				$validto = $row->cells [10]->value;
				$tracking = $row->cells [11]->value;
				$paymentmode = $row->cells [12]->value;
				$seller_id = $row->cells [13]->value;
				$transitdays = $row->cells[14]->value;
                $transaction_id=$row->cells[16]->value;
                //pricing tags
                $cancellation_text=$row->cells[17]->value;
                $cancellation_price=$row->cells[18]->value;
                $docket_charge_text=$row->cells[19]->value;
                $docket_charge_price=$row->cells[20]->value;
                $other_charge1_text=$row->cells[21]->value;
                $other_charge1_price=$row->cells[22]->value;
                $other_charge2_text=$row->cells[23]->value;
                $other_charge2_price=$row->cells[24]->value;
                $other_charge3_text=$row->cells[25]->value;
                $other_charge3_price=$row->cells[26]->value;
                $dispalyCharges = $row->cells [15]->value * $noofloads;
                
                
				Session::put('session_load_type_search',$loadtype);
				Session::put('session_vehicle_type_search',$vehicletype);

				$tracking_text = CommonComponent::getTrackingType($tracking);
				$track_type = '<i class="fa fa-signal"></i>&nbsp;'.$tracking_text;

				if ($paymentmode == 'Advance') {
					$paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
				}elseif ($paymentmode == 'Credit'){
					$credit_days = CommonComponent::getCreditdays($id,'truckhaul_seller_posts','truckhaul_seller_post_items');
					
					$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode.' | '.$credit_days[0]->credit_period.' '.$credit_days[0]->credit_period_units;
				} else {
					$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode;
				}				

				$terms =  CommonComponent::getTermsAndConditions($id,'truckhaul_seller_posts','truckhaul_seller_post_items');
				
				$terms_and_condtions = DB::table('truckhaul_seller_posts')
				->where('id','=',$id)
				->select('cancellation_charge_text','cancellation_charge_price','docket_charge_text','docket_charge_price','other_charge1_text','other_charge1_price','other_charge2_text','other_charge2_price','other_charge3_text','other_charge3_price')
				->first();
                $url = url().'/buyerbooknowforsearch/'.$row->cells [0];
               

				$row->cells [4]->value = "<div class='col-md-3 padding-left-none'>$vendorname
					<div class='red'>
					<i class='fa fa-star'></i>
					<i class='fa fa-star'></i>
					<i class='fa fa-star'></i>
					</div>

				</div>
				<div class='col-md-3 padding-left-none'>".$vehicletype."</div>
				<div class='col-md-3 padding-left-none'>".$transitdays." ".$transit_units_buyer."</div>
				<div class='col-md-3 padding-none'> $dispalyCharges/-
                    <input type='button' class='btn red-btn pull-right buyer_book_now' data-url='$url'
                       data-buyerpostofferid='$id' data-booknow_list='$id' value='Book Now' />
				</div>

				<div class='clearfix'></div>
				<div class='pull-left'>
					<div class='info-links'>&nbsp;
						<a href='#'>$track_type</a>
						<a href='#'>$paymentType</a>
					</div>
				</div>
                                
				<div class='pull-right text-right'>
					<div class='info-links'>						
                                       
                                       <a id='".$id."' data-sellerlistid=$id class='viewcount_show-data-link' data-quoteId='$id' ><span class='show-icon spot_transaction_details_list'>+</span><span class='hide-icon'>-</span> Details</a>
	                               
	                        </span>
                        <a href='#' class='new_message' data-transaction_no='".$transaction_id."' data-userid='".$seller_id."' data-buyerquoteitemid='".$id."'><i class='fa fa-envelope-o'></i></a>

					</div>
				</div>

				<div class='col-md-12 show-data-div details-slide-drop ftl_spot_transaction_details_view_$id buyer_listdetails_$id'>
					<div class='col-md-12 tab-modal-head'>
						<h3>
							<i class='fa fa-map-marker'></i> $fromlocation to $tolocation
							<span class='close-icon'>x</span>
						</h3>
					</div>
					<div class='col-md-8 data-div'>
						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Valid From</span>
							<span class='data-value'>".CommonComponent::checkAndGetDate($validfrom)."</span>
						</div>
						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Valid To</span>
							<span class='data-value'>".CommonComponent::checkAndGetDate($validto)."</span>
						</div>
						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Load Type</span>
							<span class='data-value'>$loadtype</span>
						</div>

						<div class='clearfix'></div>

						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Vehicle Number</span>
							<span class='data-value'>$vehiclenumber</span>
						</div>
						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Payment</span>
							<span class='data-value'>$paymentType</span>
						</div>
						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Tracking</span>
							<span class='data-value'>$track_type</span>
						</div>

						<div class='clearfix'></div>

						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Document</span>
							<span class='data-value'>0</span>
						</div>
						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Transit Days</span>
							<span class='data-value'>$transitdays $transit_units_buyer</span>
						</div>
						<div class='clearfix'></div>

						<div class='col-md-12 padding-left-none data-fld'>
							<span class='data-head'>Terms & Conditions</span>
							<span class='data-value'>$terms</span>
						</div>";

					
				    
					$row->cells [4]->value .=	"</div>
					<div class='col-md-4 margin-bottom'>
						<span class='data-head'>Total Price</span>
						<span class='data-value big-value'>$dispalyCharges /-</span>
					</div>
					<div class='col-md-4 margin-bottom'>
						<span class='data-head'>Cancellation Charges</span>
						<span class='data-value big-value'>$cancellation_price /-</span>
					</div>
					<div class='col-md-4 margin-bottom'>
						<span class='data-head'>Docket Charges</span>
						<span class='data-value big-value'>$docket_charge_price /-</span>
					</div>";
					if($other_charge1_text!='' && $other_charge1_price!=''  && $other_charge1_price!='0.00' ){
					$row->cells [4]->value .=	"<div class='col-md-4'>
						<span class='data-head'>$other_charge1_text</span>
						<span class='data-value big-value'>$other_charge1_price /-</span>
					</div>";
					
					}
					
					if($other_charge2_text!='' && $other_charge2_price!='' && $other_charge2_price!='0.00'){
						$row->cells [4]->value .=	"<div class='col-md-4'>
						<span class='data-head'>$other_charge2_text</span>
						<span class='data-value big-value'>$other_charge2_price /-</span>
						</div>";
							
					}
					
					if($other_charge1_text!='' && $other_charge3_price!='' && $other_charge3_price!='0.00'){
						$row->cells [4]->value .=	"<div class='col-md-4'>
						<span class='data-head'>$other_charge3_text</span>
						<span class='data-value big-value'>$other_charge3_price /-</span>
						</div>";
							
					}
					
					$row->cells [4]->value .=	"<div>
						<input id='buyersearch_booknow_buyer_id_$id' type='hidden' value=".Auth::User()->id." name='buyersearch_booknow_buyer_id_$id' >
						<input id='buyersearch_booknow_seller_id_$id' type='hidden' value=".$seller_id." name='buyersearch_booknow_seller_id_$id'>
						<input id='buyersearch_booknow_seller_price_$id' type='hidden' value=".$price." name='buyersearch_booknow_seller_price_$id'>
						<input id='buyersearch_booknow_from_date_$id' type='hidden' value=".$validfrom.">
						<input id='buyersearch_booknow_to_date_$id' type='hidden' value=".$validto.">
						<input id='buyersearch_booknow_dispatch_date_$id' type='hidden' value='".CommonComponent::convertDateForDatabase(Session::get('session_dispatch_date_buyer'))."'>
						</div>

					<div class='col-md-12 col-sm-12 col-xs-12 padding-none margin-top buyerbooknow_listdetails_$id' style='display:none'>
					</div>
				</div>";
				
			} );
			
			$result = array ();
			$result ['gridBuyer'] = $gridBuyer;
			//$result ['filter'] = $filter;
			return $result;
				
		} catch ( Exception $exc ) {
		}
	}

    /**
    * Get Post Buyer Counter Offer Page
    * Get details of buyer counter offer 
    * @param int $buyerQuoteItemId
    * @return type
    */
    public static function getPostBuyerCounterOfferForTH($buyerQuoteItemId, $comparisonType = null,$priceVal = null,$checkIds=null)
    {
        try {
            Log::info('Get posted buyer counter offer for truckhaul: '.Auth::id(),array('c'=>'2'));
            $roleId = Auth::User()->lkp_role_id;
    		if($roleId == BUYER){
    			CommonComponent::activityLog("BUYER_FETCHED_SELLER_POST",
    					BUYER_FETCHED_SELLER_POST,0,
    					HTTP_REFERRER,CURRENT_URL);
    		}
            $countview = TruckHaulBuyerComponent::updateTHBuyerQuoteDetailsViews($buyerQuoteItemId);
            $arrayBuyerCounterOffer = TruckHaulBuyerComponent::getTHBuyerQuoteDetailsFromId($buyerQuoteItemId);

            if(!empty($arrayBuyerCounterOffer)) {
                $privateSellerNames = TruckHaulBuyerComponent::getPrivateSellerNames($buyerQuoteItemId);
                $arrayBuyerQuoteSellersQuotesPrices = TruckHaulBuyerComponent::getTHBuyerQuoteSellersQuotesPricesFromId($buyerQuoteItemId, $comparisonType,$priceVal,$checkIds);
                $arraySellerIds = TruckHaulBuyerComponent::getTHSellerIds($arrayBuyerCounterOffer[0]->buyer_quote_id);
                $arrayBuyerLeads = TruckHaulBuyerComponent::getTHLeadsForBuyer($arrayBuyerCounterOffer[0]->from_city_id, $arraySellerIds);
                $countBuyerLeads = count($arrayBuyerLeads);
                if(!empty($arrayBuyerQuoteSellersQuotesPrices)) {
                    $countCartItems = BuyerComponent::getCountOfCartItems($arrayBuyerQuoteSellersQuotesPrices[0]->buyer_id,$buyerQuoteItemId,true);
                } else {
                    $countCartItems = 0;
                }
                if(!empty($arrayBuyerQuoteSellersQuotesPrices)) {
                    $countOrders = BuyerComponent::getCountOfOrders($arrayBuyerQuoteSellersQuotesPrices[0]->buyer_id,$buyerQuoteItemId,true);
                } else {
                    $countOrders = 0;
                }

                $fromLocation = BuyerComponent::getCityNameFromId($arrayBuyerCounterOffer[0]->from_city_id);
                $toLocation = BuyerComponent::getCityNameFromId($arrayBuyerCounterOffer[0]->to_city_id);
                if($arrayBuyerCounterOffer[0]->is_dispatch_flexible == 1) {
                    $dispatchDate = BuyerComponent::getPreviousNextThreeDays($arrayBuyerCounterOffer[0]->dispatch_date);
                } else {
                    $dispatchDate = CommonComponent::checkAndGetDate($arrayBuyerCounterOffer[0]->dispatch_date);
                }
                //$deliveryDate = CommonComponent::checkAndGetDate($arrayBuyerCounterOffer[0]->delivery_date);


                $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Reporting');
                $buyerPostCounterOfferComparisonTypes = config::get('constants.BUYER_POST_COUNTER_OFFER_COMPARISON_TYPES');
                return [
                            'arrayBuyerCounterOffer' => $arrayBuyerCounterOffer,
                            'privateSellerNames' => $privateSellerNames,
                            'fromLocation' => $fromLocation,
                            'toLocation' => $toLocation,
                			'tolocationid' =>$arrayBuyerCounterOffer[0]->to_city_id,
                            
                            'dispatchDate' => $dispatchDate,
                            'arrayBuyerQuoteSellersQuotesPrices' => $arrayBuyerQuoteSellersQuotesPrices,
                            'countBuyerLeads' => $countBuyerLeads,
                            'sourceLocation' => $sourceLocationType,
                            //'destinationLocation' => $destinationLocationType,
                            //'packagingType' => $packagingType,
                            'countCartItems' => $countCartItems,
                            'countOrders' => $countOrders,
                            'countview' => $countview,
                            'buyerPostCounterOfferComparisonTypes' => $buyerPostCounterOfferComparisonTypes,
                            'comparisonType' => $comparisonType
                        ];
            }
        } catch (Exception $e) {

        }
    }

    /**
    * Get leads data
    * @param int $serviceId
    * @param int $buyerQuoteItemId
    * @return array
    */
    public static function getSellerLeadsData($serviceId, $buyerQuoteItemId) {
    	try {
    		$matchedSellerPosts = BuyerMatchingComponent::getMatchedResults($serviceId, $buyerQuoteItemId);
    		$matchedIds = array();
    		foreach($matchedSellerPosts as $matchedSellerPost){
    			$matchedIds[] = $matchedSellerPost->seller_post_id;
    		}
    		
                $getSellerLeadData = DB::table('truckhaul_seller_post_items as spi');
                $getSellerLeadData->leftjoin('truckhaul_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
                $getSellerLeadData->leftjoin('users as u', 'u.id', '=', 'sp.seller_id');
                $getSellerLeadData->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'spi.lkp_load_type_id');
                $getSellerLeadData->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'spi.lkp_vehicle_type_id');
                $getSellerLeadData->join ( 'lkp_cities as cf', 'spi.from_location_id', '=', 'cf.id' );
                $getSellerLeadData->join ( 'lkp_cities as ct', 'spi.to_location_id', '=', 'ct.id' );
                $getSellerLeadData->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
                $getSellerLeadData->whereIn('spi.id', $matchedIds);
                $getSellerLeadData->where('spi.is_private', 0);
                $getSellerLeadData->select('sp.credit_period','sp.credit_period_units','sp.transaction_id as transaction_no','spi.*','sp.seller_id','sp.from_date','sp.to_date','ldt.load_type','lvt.vehicle_type','u.username','cf.city_name as fromcity', 'ct.city_name as tocity','sp.lkp_payment_mode_id','pm.payment_mode as paymentmethod','sp.tracking','sp.terms_conditions','sp.cancellation_charge_price','sp.docket_charge_price');
                $arraySellerLeadsData = $getSellerLeadData->get();
    		
    		//echo "<pre>"; print_r($arraySellerLeadsData); exit;
    		return $arraySellerLeadsData;
    	} catch (Exception $exc) {
    		// echo $exc->getTraceAsString();
    		// TODO:: Log the error somewhere
    	}
    }
    

    /**
    * Get Post Buyer Counter Offer Page
    * Inserts counter offer price
    * @param Request $request
    * @return type
    */
    public static function setPostBuyerCounterOfferForTH($input)
    {
        try{
            Log::info('Set buyer counter offer for Truckhaul: '.Auth::id(),array('c'=>'2'));
            $roleId = Auth::User()->lkp_role_id;
    		if($roleId == BUYER){
    			CommonComponent::activityLog("BUYER_INSERTED_COUNTER_OFFER",BUYER_INSERTED_COUNTER_OFFER,0,HTTP_REFERRER,CURRENT_URL);
    		}
            //Save data into txnprojectinviteerequests
            $updatedAt = date ('Y-m-d H:i:s');
            $updatedIp = $_SERVER['REMOTE_ADDR'];
            $updatedBy = Auth::User()->user_id;
            
            TruckhaulBuyerQuoteSellersQuotesPrice::where(["id" => $input['buyerCounterOfferId']])
                                                ->update(
                                                    array(
                                                        'counter_quote_price' => $input['counterOfferValue'],
                                                        'counter_transit_days' => $input['countertransitValue'],
                                                        'updated_at' => $updatedAt,
                                                        'updated_ip' => $updatedIp,
                                                        'updated_by' => $updatedBy,
                                                        'counter_quote_created_at' => $updatedAt
                                                    )
                                                );
            CommonComponent::auditLog($input['buyerCounterOfferId'],'truckhaul_buyer_quote_sellers_quotes_prices');
            $buyerDetails = DB::table('truckhaul_buyer_quote_sellers_quotes_prices as bqsqp')
                            ->where(['id' => $input['buyerCounterOfferId'] ])->select('bqsqp.seller_id')->get();
            if(!empty($buyerDetails)) {
                //CommonComponent::sendEmail(COUNTER_OFFER_BY_BUYER,$buyerDetails[0]->seller_id);
                $sellerCounterOfferEmail = DB::table('users')->where('id', $buyerDetails[0]->seller_id)->get();
                $sellerCounterOfferEmail[0]->buyername = Auth::User()->username;
                CommonComponent::send_email(COUNTER_OFFER_BY_BUYER,$sellerCounterOfferEmail);
                
                
                //*******Send Sms to the Sellers,buyer counter offer***********************//
                
                $getBuyerpostdetails  = DB::table('truckhaul_buyer_quote_sellers_quotes_prices as bqsqp')
            				->leftjoin('truckhaul_seller_post_items as spi', 'bqsqp.seller_post_item_id', '=', 'spi.id')
            				->leftjoin('truckhaul_seller_posts as sp', 'spi.seller_post_id', '=', 'sp.id')
            				->where('bqsqp.id','=',$input['buyerCounterOfferId'])
            				->select('sp.transaction_id','bqsqp.seller_id')->get();
                $msg_params = array(
                		'randnumber' => $getBuyerpostdetails[0]->transaction_id,
                		'buyername' => Auth::User()->username,
                		'servicename' => 'FTL'
                );
                $getMobileNumber  =   CommonComponent::getMobleNumber($getBuyerpostdetails[0]->seller_id);
                CommonComponent::sendSMS($getMobileNumber,BUYER_COUNTER_OFFER_SMS,$msg_params);
                //*******Send Sms to the Sellers,buyer counter offer***********************//
                
            }
            return;
        } catch (Exception $e) {

        }
    }

    /**
    * get buyer counter offer page
    * Insert values for booknow
    * @param Request $request
    * @return type
    */
    public static function setBuyerBooknowForTH($input)
    {
    	Log::info('Insert the buyer booknow data for truckhaul: '.Auth::id(),array('c'=>'2'));
        try {
            $roleId = Auth::User()->lkp_role_id;
            $serviceId = Session::get('service_id');
    		if($roleId == BUYER){
    			CommonComponent::activityLog("BUYER_INSERTED_ADDTOCART",
    					BUYER_INSERTED_ADDTOCART,0,
    					HTTP_REFERRER,CURRENT_URL);
    		}
            
            $cartPaymentMethods = DB::table('cart_items')
            ->leftjoin('users','users.id' ,'=', 'cart_items.seller_id')
            ->where('cart_items.buyer_id',$input['buyerId'])
            ->select( 'cart_items.lkp_payment_mode_id')
            ->get();

            if(!empty($cartPaymentMethods)){
                $existingCartPaymentMethod = $cartPaymentMethods[0]->lkp_payment_mode_id;
            }
            
                $postPaymentMethods = DB::table('truckhaul_seller_posts as sp')
                                    ->leftjoin('truckhaul_seller_post_items as spi','spi.seller_post_id','=','sp.id')
                                    ->leftjoin ( 'truckhaul_buyer_quote_sellers_quotes_prices as bqsp', 'bqsp.seller_post_item_id', '=', 'spi.id' )
                                    ->where('spi.id',$input['postItemId'])
                                    ->select('spi.id', 'spi.transitdays', 'spi.units', 'sp.lkp_payment_mode_id',
                                            DB::raw("(case when `bqsp`.`final_transit_days` != 0 then bqsp.final_transit_days  when `bqsp`.`initial_transit_days` != 0 then bqsp.initial_transit_days when 'bqsp.id'=0 then spi.transitdays end) as transitdays"))
                                    ->get();
               
           if(empty($input['sellerPostedToDate']) || $input['sellerPostedToDate'] == '0000-00-00') {
                $transitTime = $postPaymentMethods[0]->transitdays;
                $transitTimeUnit = $postPaymentMethods[0]->units;
                if($transitTimeUnit == 'Weeks') {
                    $transitDays = $transitTimeUnit * 7;
                } else {
                    $transitDays = $transitTime;
                }
                $deliveryDate = date("Y-m-d", strtotime("+".$transitDays." days", strtotime(CommonComponent::convertDateForDatabase($input['consignmentPickupDate']))));

            } else {
                $deliveryDate = $input['sellerPostedToDate'];
            }
            $postPaymentMethod = $postPaymentMethods[0]->lkp_payment_mode_id;
            if((isset($existingCartPaymentMethod) && $existingCartPaymentMethod != $postPaymentMethod) && count($cartPaymentMethods)>0){
                return array('success' => 0, 
                        'message' => "You can't proceed with book now,because the payment mode of all the items in the cart should be similar!");
            } else {    
                $booknowAddToCart  =  new CartItem();
                $booknowAddToCart->seller_id = $input['sellerId'];
                $booknowAddToCart->buyer_id = $input['buyerId'];
                $booknowAddToCart->lkp_service_id = Session::get('service_id');
                $booknowAddToCart->buyer_quote_item_id = $input['quoteItemId'];
                
                $booknowAddToCart->lkp_payment_mode_id = $postPaymentMethod;
                $booknowAddToCart->seller_post_item_id = $input['postItemId'];
                $booknowAddToCart->lkp_src_location_type_id = $input['sourceLocationType'];
                if($input['sourceLocationType']=='11')
                $booknowAddToCart->other_src_location_type = $input['sourceLocationTypeOther'];
                $booknowAddToCart->price = $input['price'];
                $booknowAddToCart->buyer_consignment_pick_up_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
                $booknowAddToCart->buyer_consignment_pick_up_time_from = $input['consignmentPickupFromTime'];
                $booknowAddToCart->buyer_consignment_pick_up_time_to = $input['consignmentPickupToTime'];
                $booknowAddToCart->buyer_consignor_name = $input['consignorName'];
                $booknowAddToCart->buyer_consignor_mobile = $input['consignorNumber'];
                $booknowAddToCart->buyer_consignor_email = $input['consignorEmail'];
                $booknowAddToCart->buyer_consignor_address = $input['consignorAddress'];
                $booknowAddToCart->buyer_consignor_pincode = $input['consignorPin'];
                $booknowAddToCart->buyer_additional_details = $input['additionalDetails'];
                $booknowAddToCart->dispatch_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
                

                $created_at = date ( 'Y-m-d H:i:s' );
                $createdIp = $_SERVER['REMOTE_ADDR'];
                $booknowAddToCart->created_by = Auth::id();
                $booknowAddToCart->created_at = $created_at;
                $booknowAddToCart->created_ip = $createdIp;
                if($booknowAddToCart->save()){
                    
                    CommonComponent::auditLog($booknowAddToCart->id,'cart_items');
                    $cartInsertId = $booknowAddToCart->id;
                    
                    $cartData =  DB::select( DB::raw("SELECT
			        q.*,
			        u.username,
			        q.price,
			        pz1.city_name as from_location,
			        pzt1.city_name as to_location,
			        service.service_name,
			        bq1.dispatch_date as dispatch_date,
			        bq1.lkp_post_status_id as post_status,
                                bq1.number_loads as no_loads
			        FROM
			        cart_items q
			        LEFT JOIN users u on u.id = q.seller_id
			        LEFT JOIN lkp_services service on service.id = q.lkp_service_id
			        LEFT JOIN truckhaul_seller_post_items psi1 on q.seller_post_item_id = psi1.id and q.lkp_service_id = 4 
			        LEFT JOIN truckhaul_seller_posts ps1 on psi1.seller_post_id = ps1.id and q.lkp_service_id = 4
			        LEFT JOIN buyer_quote_items bq1 on bq1.id = q.buyer_quote_item_id and q.lkp_service_id = 4
			        LEFT JOIN lkp_cities pz1
			              ON (psi1.from_location_id = pz1.id and q.lkp_service_id = 4)
			        LEFT JOIN lkp_cities pzt1
			              ON (psi1.to_location_id = pzt1.id and q.lkp_service_id = 4)       
			        where q.id ='".$cartInsertId."'"));
                            $sellerPostTableName = 'truckhaul_seller_post_items';
                           

                    $booknowAddToCart  =  new ViewCartItem();
                    $booknowAddToCart->id = $cartInsertId;
	                $booknowAddToCart->seller_id = $input['sellerId'];
	                $booknowAddToCart->buyer_id = $input['buyerId'];
	                $booknowAddToCart->lkp_service_id = Session::get('service_id');
                    $booknowAddToCart->buyer_quote_item_id = $input['quoteItemId'];
                       
	                $booknowAddToCart->lkp_payment_mode_id = $postPaymentMethod;
	                $booknowAddToCart->seller_post_item_id = $input['postItemId'];
	                $booknowAddToCart->lkp_src_location_type_id = $input['sourceLocationType'];
	                if($input['sourceLocationType']=='11')
                        $booknowAddToCart->other_src_location_type = $input['sourceLocationTypeOther'];
                        $booknowAddToCart->price = $input['price'];
	                $booknowAddToCart->buyer_consignment_pick_up_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
                        $booknowAddToCart->buyer_consignment_pick_up_time_from = $input['consignmentPickupFromTime'];
                        $booknowAddToCart->buyer_consignment_pick_up_time_to = $input['consignmentPickupToTime'];
                        $booknowAddToCart->buyer_consignor_name = $input['consignorName'];
	                $booknowAddToCart->buyer_consignor_mobile = $input['consignorNumber'];
	                $booknowAddToCart->buyer_consignor_email = $input['consignorEmail'];
	                $booknowAddToCart->buyer_consignor_address = $input['consignorAddress'];
	                $booknowAddToCart->buyer_consignor_pincode = $input['consignorPin'];
	                $booknowAddToCart->buyer_additional_details = $input['additionalDetails'];
 	                $booknowAddToCart->dispatch_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
	                $booknowAddToCart->username = $cartData[0]->username;
	                $booknowAddToCart->from_location = $cartData[0]->from_location;
	                $booknowAddToCart->to_location = $cartData[0]->to_location;
	                $booknowAddToCart->order_dispatch_date = $cartData[0]->dispatch_date;
	                $booknowAddToCart->post_status = $cartData[0]->post_status;
                    $booknowAddToCart->service_name = "Truck"." ".$cartData[0]->service_name;


	                $created_at = date ( 'Y-m-d H:i:s' );
	                $createdIp = $_SERVER['REMOTE_ADDR'];
	                $booknowAddToCart->created_by = Auth::id();
	                $booknowAddToCart->created_at = $created_at;
	                $booknowAddToCart->created_ip = $createdIp;
	                $booknowAddToCart->save();
                }
                return array('success' => 1, 'message' => "Item is added to cart successfully.");
            }
            //Save data into txnprojectinviteerequests
        } catch (Exception $e) {

        }
    }

    /**
    * get buyer counter offer page
    * Cancel enquiry
    * @param integer $buyerQuoteItemId
    * @return type
    */
    public static function cancelEnquiryForTH($buyerQuoteItemId)
    {
        Log::info('Cancel the quote enquiry for Truckhaul: '.Auth::id(),array('c'=>'2'));
        try{
            $roleId = Auth::User()->lkp_role_id;
    		if($roleId == BUYER){
    			CommonComponent::activityLog("BUYER_CANCELED_ENQUIRY",
    					BUYER_CANCELED_ENQUIRY,0,
    					HTTP_REFERRER,CURRENT_URL);
    		}
            //Save data into txnprojectinviteerequests
            $updatedAt = date ('Y-m-d H:i:s');
            $updatedIp = $_SERVER['REMOTE_ADDR'];
            $updatedBy = Auth::User()->user_id;
            BuyerQuoteItems::where(["id" => $buyerQuoteItemId])
                                    ->update(
                                        array(
                                            'is_cancelled' => 1,
                                            'lkp_post_status_id' => CANCELLED,
                                            'updated_at' => $updatedAt,
                                            'updated_ip' => $updatedIp,
                                            'updated_by' => $updatedBy
                                        )
                                    );
            CommonComponent::auditLog($buyerQuoteItemId,'buyer_quote_items');
            $arrayBuyerEmailIds = TruckHaulBuyerComponent::getBuyerEnquirySellers($buyerQuoteItemId);
            
            $userDetails = [];
            foreach ($arrayBuyerEmailIds as $buyerDetails) {
                $userDetails[0] = $buyerDetails;
                $userDetails[0]->fromLocation = BuyerComponent::getCityNameFromId($buyerDetails->from_city_id);
                $userDetails[0]->toLocation = BuyerComponent::getCityNameFromId($buyerDetails->to_city_id);
                CommonComponent::send_email ( CANCEL_ENQUIRY_INFO_MAIL, $userDetails );
            }

            return ['cancelsuccessmessage' => 'Post deleted successfully.'];
            //Save data into txnprojectinviteerequests
        } catch (Exception $e) {

        }
    }
    
    /**
     * Buyer counter offer Page
     * Method to retrieve email ids of buyers
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getBuyerEnquirySellers($buyerQuoteItemId) {
        try {
            Log::info('Get seller lists for the buyer: ' . Auth::id(), array(
                'c' => '2'
            ));
            $getBuyerQuoteSellersQuotesPricesQuery = DB::table('truckhaul_buyer_quote_sellers_quotes_prices as bqsqp');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('users as usr', 'usr.id', '=', 'bqsqp.buyer_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('truckhaul_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('truckhaul_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_load_types as llt', 'llt.id', '=', 'bqi.lkp_load_type_id');
            $getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.buyer_quote_item_id', $buyerQuoteItemId);
            $getBuyerQuoteSellersQuotesPricesQuery->whereRaw('(`bqsqp`.`initial_quote_price` is not NULL and `bqsqp`.`initial_quote_price` != "" or `bqsqp`.`seller_acceptence` = "1")');
            $getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id as bqsqpid', 'u.id as userId', 'u.email', 'u.username as sellerName', 'usr.username as buyerName', 'llt.load_type', 'bq.transaction_id', 'bqi.from_city_id', 'bqi.to_city_id', 'bqi.dispatch_date',  'lvt.vehicle_type');
            $arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
            return $arrayBuyerQuoteSellersQuotesPrices;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
    * Change status of seller post item
    * @param type $sellerPostItemId
    * @param type $status
    */
    public static function changeStatusForSellerPostItem($sellerPostItemId, $status)
    {
        try{
            $updatedAt = date ( 'Y-m-d H:i:s' );
            $updatedIp = $_SERVER['REMOTE_ADDR'];
            $updatefinal = DB::table('truckhaul_seller_post_items')
                        ->where('truckhaul_seller_post_items.id','=',$sellerPostItemId)
                        ->update(array(
                                'lkp_post_status_id'=> $status,
                                'updated_ip'=> $updatedAt,
                                'updated_at'=> $updatedIp,
                                'updated_by'=> Auth::id()
                                ));
            CommonComponent::auditLog($sellerPostItemId,'truckhaul_seller_post_items');
        } catch (Exception $e) {

        }
    }
    
    /**
     * Truck Haul market Leads
     * srinu started here - 18-04-2016.
     * @param type $sellerPostItemId
     * @param type $status
     * all booknow button hide display none in market leads pages in buyer side -srinu
     */
    public static function getTruckHaulBuyerMarketLeadsList($service_id, $post_status, $enquiry_type){
    
    	$from_locations = array(""=>"From Location");
		$to_locations = array(""=>"To Location");
		$vehicle_types = array(""=>"Vehicle Type");
		$load_types = array(""=>"Load Type");
	
		// query to retrieve seller posts list and bind it to the grid
		$Query = DB::table ( 'truckhaul_seller_posts as sp' );
		$Query->leftjoin ( 'truckhaul_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'sp.lkp_post_status_id' );
		$Query->leftjoin ( 'truckhaul_seller_selected_buyers as ssb', 'ssb.seller_post_id', '=', 'sp.id' );
		$Query->join ( 'lkp_cities as cf', 'spi.from_location_id', '=', 'cf.id' );
		$Query->join ( 'lkp_cities as ct', 'spi.to_location_id', '=', 'ct.id' );
		$Query->join ( 'users as us', 'sp.seller_id', '=', 'us.id' );
		$Query->where( 'sp.lkp_access_id', 2);
		$Query->where('sp.lkp_post_status_id',2);		
		$Query->where( 'ssb.buyer_id', Auth::User ()->id);
		$Query->where('spi.is_private', 0);
		//conditions to make search
		if(isset($statusId) && $statusId != ''){
			$Query->where('sp.lkp_post_status_id', $statusId);
		}		
		if( isset($_REQUEST['search']) && $_REQUEST['from_date']!=''){
			$from=CommonComponent::convertDateForDatabase($_REQUEST['from_date']);
			$Query->whereRaw('sp.from_date >= "'.$from.'"');
		}		
		if( isset($_REQUEST['search']) && $_REQUEST['to_date']!=''){
			$to=CommonComponent::convertDateForDatabase($_REQUEST['to_date']);
			if($_REQUEST['from_date']!=''){
				$Query->whereBetween('sp.to_date',array($from,$to));
			}else{
				$Query->where('sp.to_date', $to);
			}
		}		
		$sellerresults = $Query->select ( 'sp.id', 'sp.from_date',
				'sp.to_date','sp.lkp_access_id','sp.lkp_post_status_id','ps.post_status',
				'us.username','ct.city_name as toCity', 'cf.city_name as fromCity',
				'sp.terms_conditions','sp.tracking','sp.lkp_payment_mode_id','spi.transitdays',
				'spi.units','spi.id as sellerpostItemId'
		)		
		->groupBy('sp.id')
		->get ();
		
		//Functionality to handle filters based on the selection starts
		foreach($sellerresults as $seller){
			$seller_post_items  = DB::table('truckhaul_seller_post_items')
			->where('truckhaul_seller_post_items.seller_post_id',$seller->id)
			->select('*')
			->get();
			foreach($seller_post_items as $seller_post_item){
				if(!isset($from_locations[$seller_post_item->from_location_id])){
					$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
				}
				if(!isset($to_locations[$seller_post_item->to_location_id])){
					$to_locations[$seller_post_item->to_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->to_location_id)->pluck('city_name');
				}
				if(!isset($load_types[$seller_post_item->lkp_load_type_id])){
					$load_types[$seller_post_item->lkp_load_type_id] = DB::table('lkp_load_types')->where('id', $seller_post_item->lkp_load_type_id)->pluck('load_type');
				}
				if(!isset($vehicle_types[$seller_post_item->lkp_vehicle_type_id])){
					$vehicle_types[$seller_post_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $seller_post_item->lkp_vehicle_type_id)->pluck('vehicle_type');
				}
			}
		}
		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations = CommonComponent::orderArray($to_locations);
		$load_types = CommonComponent::orderArray($load_types);
		$vehicle_types = CommonComponent::orderArray($vehicle_types);
		
		//Functionality to handle filters based on the selection ends	
		$grid = DataGrid::source ( $Query );	
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Seller Name', 'username' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'fromCity', 'From Location', 'fromCity' )->attributes(array("class" => "col-md-2 padding-left-none"));	
		$grid->add ( 'toCity', 'To Location', 'toCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'from_date', 'Valid From', 'post_status' )->attributes(array("class" => "col-md-2 padding-left-none"));		
		$grid->add ( 'to_date', 'Valid To', 'post_status' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'post_status', 'Status', 'post_status' )->attributes(array("class" => "col-md-1 padding-left-none"));		
		$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
		$grid->add ( 'tracking', 'Tracking', 'tracking' )->style ( "display:none" );
		$grid->add ( 'terms_conditions', 'Tracking', 'terms_conditions' )->style ( "display:none" );
		$grid->add ( 'lkp_payment_mode_id', 'Payment Method', 'lkp_payment_mode_id' )->style ( "display:none" );
		$grid->add ( 'transitdays', 'Transit Days', 'transitdays' )->style ( "display:none" );
		$grid->add ( 'units', 'Units', 'units' )->style ( "display:none" );
                $grid->add ( 'sellerpostItemId', 'Seller Post Item Id', 'sellerpostItemId' )->style ( "display:none" );
		$grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );
	
		$grid->row ( function ($row) {
			
			$row->cells [0]->style ( 'display:none' );
			$row->cells [1]->style ( 'display:none' );
			$row->cells [2]->style ( 'display:none' );
			$row->cells [3]->style ( 'display:none' );
			$row->cells [4]->style ( 'display:none' );
			$row->cells [5]->style ( 'display:none' );
			$row->cells [6]->style ( 'display:none' );			
			$row->cells [8]->style ( 'display:none' );
			$row->cells [9]->style ( 'display:none' );
			$row->cells [10]->style ( 'display:none' );
			$row->cells [11]->style ( 'display:none' );
			$row->cells [12]->style ( 'display:none' );
                        $row->cells [13]->style ( 'display:none' );
			
			$spId = $row->cells [0]->value;
			$sellerName=$row->cells [1]->value;
			$fromLocation=$row->cells [2]->value;
			$toLocation=$row->cells [3]->value;
			$fromDate=$row->cells [4]->value;
			$toDate=$row->cells [5]->value;
			$postStatus=$row->cells [6]->value;
			$tracking=$row->cells [8]->value;
			$termandconditions=$row->cells [9]->value;
			$paymentMethod=$row->cells [10]->value;
			$transitdays=$row->cells [11]->value;
			$units=$row->cells [12]->value;	
                        $sellerpostItemId=$row->cells [13]->value;
			
			$seller_post_items  = DB::table('truckhaul_seller_post_items')
							->join('truckhaul_seller_posts','truckhaul_seller_posts.id','=','truckhaul_seller_post_items.seller_post_id')
							->where('truckhaul_seller_post_items.seller_post_id',$spId)
							->select('*','truckhaul_seller_post_items.id as spiid')
							->get();			
								
			$seller_payment_mode_method = CommonComponent::getSellerPostPaymentMethod($paymentMethod);
			$data_link = url()."/buyermarketleads/$spId";			
               $tracking_seller_post = CommonComponent::getTrackingType($tracking);
			$getpostitemids = DB::table('truckhaul_seller_post_items')
			->where('truckhaul_seller_post_items.seller_post_id','=',$spId)
			->select('truckhaul_seller_post_items.id')
			->get();
                        
			if ($seller_payment_mode_method == 'Advance') {
                                $paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
                        } elseif ($seller_payment_mode_method == 'Credit'){
                                $credit_days = CommonComponent::getCreditdays($sellerpostItemId,'truckhaul_seller_posts','truckhaul_seller_post_items');
                                $paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$seller_payment_mode_method.' | '.$credit_days[0]->credit_period.' '.$credit_days[0]->credit_period_units;
                        }else {
                                $paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$seller_payment_mode_method;
                        }			
			
			$msg_count=0;
	$row->cells [7]->value = "<a href=".$data_link."><div class='table-row '>
			<div class='col-md-2 padding-left-none'>
			$sellerName
			<div class='red'>
			<i class='fa fa-star'></i>
			<i class='fa fa-star'></i>
			<i class='fa fa-star'></i>
			</div>
			</div>
			<div class='col-md-2 padding-left-none'>$fromLocation</div>
			<div class='col-md-2 padding-left-none'>$toLocation</div>
			<div class='col-md-2 padding-none'>".CommonComponent::checkAndGetDate($fromDate)."</div>
			<div class='col-md-2 padding-none'>".CommonComponent::checkAndGetDate($toDate)."</div>
			<div class='col-md-1 padding-none'>$postStatus</div>
			<div class='col-md-1 padding-none text-right'>
			<button class='btn red-btn pull-right' style='display:none'>Book Now</button>
			</div>
			</a>
			
			<div class='clearfix'></div>
			
			<div class='pull-left'>
			<div class='info-links'>
			<a href='$data_link'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>$msg_count</span></a>
			</div>
			</div>
			<div class='pull-right text-right'>
			<div class='info-links'>
			<a id='".$spId."' class='show-data-link'><span class='show-icon spot_transaction_details_list'>+</span><span class='hide-icon'>-</span> Details</a>
			</div>
			</div>
			
			<div style='display:none' class='col-md-12 show-data-div padding-top'>
			<div class='col-md-12 padding-none'>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Valid From</span>
			<span class='data-value'>".CommonComponent::checkAndGetDate($fromDate)."</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Valid To</span>
			<span class='data-value'>".CommonComponent::checkAndGetDate($toDate)."</span>
			</div>
			
			<div class='col-md-3 padding-left-none data-fld'>
			<span class='data-head'>Payment</span>
			<span class='data-value'>$paymentType</span>
			</div>
			<div class='col-md-3 padding-left-none data-fld'>
			<span class='data-head'>Tracking</span>
			<span class='data-value'><i class='fa fa-signal'></i>&nbsp;$tracking_seller_post</span>
			</div>
		
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Document</span>
			<span class='data-value'>0</span>
			</div>			<div class='clearfix'></div>";
		if($termandconditions!="")	{			
			$row->cells [7]->value .= "	<div class='col-md-3 padding-left-none data-fld'>
			<span class='data-head'>Terms &amp; Conditions</span>
			<span class='data-value'>$termandconditions</span>
			</div>";
		}	
    
    $row->cells [7]->value .= "	</div>
							    </div>
							    </div> ";
    	
    } );
    
    		$filter = DataFilter::source ( $Query );
    		$filter->add ( 'spi.from_location_id', '', 'select' )->options ( $from_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
    		$filter->add ( 'spi.to_location_id', '', 'select' )->options ( $to_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
    		$filter->add ( 'spi.lkp_load_type_id', '', 'select' )->options ( $load_types )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
    		$filter->add ( 'spi.lkp_vehicle_type_id', '', 'select' )->options ( $vehicle_types )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
    		$filter->submit ( 'search' );
    		$filter->reset ( 'reset' );
    		$filter->build ();
    					
    		$result = array ();
    		$result ['grid'] = $grid;
    		$result ['filter'] = $filter;
    		return $result;
    
    }

    /**
     * Buyer counter offer Page
     * Method to retrieve seller lists
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getTHBuyerQuoteSellersQuotesPricesFromId($buyerQuoteItemId, $comparisonType = null,$priceVal = null,$checkIds = null) {
        try {
            Log::info('Get seller lists for the buyer: ' . Auth::id(), array('c' => '2'));
            (object)$arrayBuyerQuoteSellersNotQuotesPrices="";
            $getBuyerQuoteSellersQuotesPricesQuery = DB::table('truckhaul_buyer_quote_sellers_quotes_prices as bqsqp');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('truckhaul_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('truckhaul_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('truckhaul_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('truckhaul_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'bqi.lkp_load_type_id');
            if (!empty($buyerQuoteItemId)) {
                $getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.buyer_quote_item_id', $buyerQuoteItemId);
            }
            $getBuyerQuoteSellersQuotesPricesQuery->whereRaw('(`bqsqp`.`initial_quote_price` is not NULL and `bqsqp`.`initial_quote_price` != "" or `bqsqp`.`seller_acceptence` = "1")');
            $getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.buyer_id', Auth::User()->id);
            
            
            if (!empty($comparisonType)) {
            	
            	
            	
            	if($checkIds){

            		$checkIds= explode(",",$checkIds);
            		$getBuyerQuoteSellersQuotesPricesQuery->whereIn('bqsqp.id', $checkIds);
            		
            		$getBuyerQuoteSellersNotQuotesPricesQuery = DB::table('truckhaul_buyer_quote_sellers_quotes_prices as bqsqp');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('truckhaul_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('truckhaul_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('truckhaul_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('truckhaul_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'bqi.lkp_load_type_id');
            		
            		if (!empty($buyerQuoteItemId)) {
            			$getBuyerQuoteSellersNotQuotesPricesQuery->where('bqsqp.buyer_quote_item_id', $buyerQuoteItemId);
            		}
            		$getBuyerQuoteSellersNotQuotesPricesQuery->whereRaw('(`bqsqp`.`initial_quote_price` is not NULL and `bqsqp`.`initial_quote_price` != "" or `bqsqp`.`seller_acceptence` = "1")');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->where('bqsqp.buyer_id', Auth::User()->id);
            		$getBuyerQuoteSellersNotQuotesPricesQuery->whereNotIn('bqsqp.id', $checkIds);
            		$getBuyerQuoteSellersNotQuotesPricesQuery->select('bqsqp.private_seller_quote_id','sp.transaction_id as transaction_no','bqsqp.id', 'bqsqp.buyer_id', 'bqsqp.initial_quote_price', 'bqsqp.counter_quote_price', 'bqsqp.final_quote_price','bqsqp.initial_transit_days','bqsqp.final_transit_days', 'bqsqp.counter_transit_days', 'bqsqp.final_transit_days', 'u.username', 'ldt.load_type', 'bqi.price', 'bqsqp.seller_id', 'bqsqp.buyer_quote_item_id', 'bqsqp.seller_post_item_id', 'spi.transitdays', 'spi.units', 'bqsqp.firm_price', 'bqsqp.seller_acceptence', 'bq.lkp_quote_access_id', 'spi.seller_post_id', 'sp.from_date', 'sp.to_date', 'lvt.vehicle_type', 'bqi.lkp_post_status_id', 'bqi.quantity');
            		if ($comparisonType == '1') {
            			
            			
            				if($priceVal=="initial_transit_days"){
            					$transit='bqsqp.initial_transit_days';
            				}
            				if($priceVal=="final_transit_days"){
            					$transit='bqsqp.final_transit_days';
            				}
            				
            			$getBuyerQuoteSellersNotQuotesPricesQuery->orderBy($transit, 'asc');
            				
            			
            		
            		} elseif ($comparisonType == '2') {
            			$price="";
            			
            			if($priceVal=="final_quote_price"){
            				$price='bqsqp.final_quote_price';
            			}
            			if($priceVal=="firm_price"){
            				$price='bqsqp.firm_price';
            			}
            			if($priceVal=="counter_quote_price"){
            				$price='bqsqp.counter_quote_price';
            			}
            			if($priceVal=="initial_quote_price"){
            				$price='bqsqp.initial_quote_price';
            			}
            		
            			$getBuyerQuoteSellersNotQuotesPricesQuery->orderBy($price, 'asc');
            		}
            		
            		$arrayBuyerQuoteSellersNotQuotesPrices = $getBuyerQuoteSellersNotQuotesPricesQuery->get();


            		
            		$ni=0;
            		foreach ($arrayBuyerQuoteSellersNotQuotesPrices as $arrayBuyerQuoteSellersNotQuotesPrice) {
            			
            			$arrayBuyerQuoteSellersNotQuotesPrice->rank='-';
            			 
            			$ni++;
            			
            		}
            		
            		
            	 }
            	
                if ($comparisonType == '1') {
                	
                	if($priceVal=="initial_transit_days"){
                		$transit='bqsqp.initial_transit_days';
                	}
                	if($priceVal=="final_transit_days"){
                		$transit='bqsqp.final_transit_days';
                	}
                	
                    $getBuyerQuoteSellersQuotesPricesQuery->orderBy($transit, 'asc');
                    
                 } elseif ($comparisonType == '2') {
                 	$price="";
                 	//echo $priceVal;
            		
                 	if($priceVal=="final_quote_price"){
                 	  $price='bqsqp.final_quote_price';
                 	 // $checkprice=''
                 	}
                 	if($priceVal=="firm_price"){
                 		$price='bqsqp.firm_price';
                 	}
                 	if($priceVal=="counter_quote_price"){
                 		$price='bqsqp.counter_quote_price';
                 	}
                 	if($priceVal=="initial_quote_price"){
                 		$price='bqsqp.initial_quote_price';
                 	}
                 	
                    $getBuyerQuoteSellersQuotesPricesQuery->orderBy($price, 'asc');
                }
            }
            $getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.private_seller_quote_id','sp.transaction_id as transaction_no','bqsqp.id', 'bqsqp.buyer_id', 'bqsqp.initial_quote_price', 'bqsqp.counter_quote_price', 'bqsqp.final_quote_price', 'bqsqp.initial_transit_days', 'bqsqp.final_transit_days','bqsqp.counter_transit_days', 'bqsqp.final_transit_days', 'u.username', 'ldt.load_type', 'bqi.price', 'bqsqp.seller_id', 'bqsqp.buyer_quote_item_id', 'bqsqp.seller_post_item_id', 'spi.transitdays', 'spi.units', 'bqsqp.firm_price', 'bqsqp.seller_acceptence', 'bq.lkp_quote_access_id', 'spi.seller_post_id', 'sp.from_date', 'sp.to_date', 'lvt.vehicle_type', 'bqi.lkp_post_status_id', 'bqi.quantity');
          
            
            $arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
            if(!empty($comparisonType)){
           
            $j=0;
            $k=1;
            $p=1;
            for ($i=0;$i<count($arrayBuyerQuoteSellersQuotesPrices);$i++) {
            	if($i==0){
            	 $j=1;
            	}
            	if($j>count($arrayBuyerQuoteSellersQuotesPrices)-1){
            		$j=count($arrayBuyerQuoteSellersQuotesPrices)-1;
            	}
            	if($comparisonType == '1'){
            		if($arrayBuyerQuoteSellersQuotesPrices[$i]->$priceVal !=$arrayBuyerQuoteSellersQuotesPrices[$j]->$priceVal ){
            			
            		      if($k<=3){
							$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="L".$k;
							} else{
							$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="-";
							}
            			$k++;
            		}else{
            		if($k<=3){
							$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="L".$k;
							} else{
							$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="-";
							}
            		}	
            	}
            	if($comparisonType == '2'){
            	if($arrayBuyerQuoteSellersQuotesPrices[$i]->$priceVal!=$arrayBuyerQuoteSellersQuotesPrices[$j]->$priceVal){
            		
            	if($p<=3){
						$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="L".$p;
						} else{
						$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="-";
						}
            	$p++;
                }else{
                if($p<=3){
                	$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="L".$p;
                	}else{
                		$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="-";
                	}		
                }
            	}
            	$j++;
            
          
            
            }
            }
            if(!empty($comparisonType) && !empty($checkIds)){
            $obj_merged = (array) array_merge((array) $arrayBuyerQuoteSellersQuotesPrices, (array) $arrayBuyerQuoteSellersNotQuotesPrices);
            return $obj_merged;
            }else{           
            return $arrayBuyerQuoteSellersQuotesPrices;
            }
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Buyer counter offer Page
     * Method to retrieve seller lists
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getTHSellerIds($buyerQuoteId) {
        try {
            Log::info('Get seller lists for the district: ' . Auth::id(), array('c' => '2'));
            $sellerIds = DB::table('truckhaul_buyer_quote_selected_sellers as bqss')
                    ->where('bqss.buyer_quote_id', $buyerQuoteId)
                    ->select('bqss.seller_id')
                    ->get();
            $arraySellerIds = [];
            if (isset($sellerIds) && !empty($sellerIds)) {
                foreach ($sellerIds as $sellerId) {
                    array_push($arraySellerIds, $sellerId->seller_id);
                }
            }
            return $arraySellerIds;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Buyer counter offer Page
     * Method to retrieve seller lists
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getTHLeadsForBuyer($districtId, $sellerIds) {
        try {
            Log::info('Get leads for the buyer: ' . Auth::id(), array('c' => '2'));
            $sellerData = DB::table('truckhaul_seller_post_items')
                    ->join('users', 'truckhaul_seller_post_items.created_by', '=', 'users.id')                    
                    ->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
                    ->distinct('truckhaul_seller_post_items.created_by')
                    ->where('truckhaul_seller_post_items.lkp_district_id', $districtId)
                    ->whereNotIn('sellers.id', $sellerIds)
                    ->where('users.lkp_role_id', SELLER)
                    ->select('users.id', 'users.username', 'seller_details.principal_place', 'seller_details.name', 'seller_details.contact_firstname')
                    ->get();
            return $sellerData;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Buyer counter offer Page
     * Method to retrieve buyer quote requests data
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function updateTHBuyerQuoteDetailsViews($buyerQuoteItemId) {
        try {
            Log::info('Get update buyer quote details view: ' . Auth::id(), array('c' => '2'));

            $buyerCounterDetails = DB::table('truckhaul_buyer_quote_items as bqi')
                    ->where('bqi.id', '=', $buyerQuoteItemId)
                    ->select('bqi.id', 'bqi.created_by')
                    ->get();

            $countview = DB::table('truckhaul_buyer_quote_item_views as bqiv')
                    ->where('bqiv.buyer_quote_item_id', '=', $buyerQuoteItemId)
                    ->select('bqiv.id', 'bqiv.view_counts')
                    ->get();
            if (!isset($countview[0]->view_counts)) {
                $countview = 0;
            } else {
                $countview = $countview[0]->view_counts;
            }
            return $countview;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
    * Retrieval of IView Count
    * @param type $sellerId
    * @param type $buyerQuoteItemId
    * @return type
    */
	public static function viewCountForBuyerForTruckHaul($sellerId,$buyerQuoteItemId) {
		try {
			$getviewcount = DB::table('truckhaul_buyer_quote_item_views as bqiv')
				->where('bqiv.buyer_quote_item_id','=',$buyerQuoteItemId)
				->select('bqiv.id','bqiv.view_counts')
				->get();
			if(isset($getviewcount[0]->id) && !empty($getviewcount[0]->id)) {
				$updateview = DB::table('truckhaul_buyer_quote_item_views as bqiv')
					->where('bqiv.buyer_quote_item_id','=',$buyerQuoteItemId)
					->update(array(
						'view_counts' =>$getviewcount[0]->view_counts+1
                    ));
			} else {
				$created_at  = date ( 'Y-m-d H:i:s' );
				$createdIp = $_SERVER['REMOTE_ADDR'];
				$viewcount = new TruckhaulBuyerQuoteItemView();
				$viewcount->user_id = $sellerId;
				$viewcount->buyer_quote_item_id = $buyerQuoteItemId;
				$viewcount->view_counts = 1;
				$viewcount->created_at = $created_at;
				$viewcount->created_ip = $createdIp;
				$viewcount->created_by = $sellerId;
				$viewcount->save();
			}
			if(!empty($getviewcount[0]->view_counts)) {
				 $count = $getviewcount[0]->view_counts;
			}
			else {
				$count = '';
			}
			return $count;
		} catch(Exception $e) {
			//return $e->message;
		}
	}
        

        
        /**
	 * Truck Haul Post Details List Page.
	 *
	 * @param
	 * $request
	 * @return Response
	 */

	public static function truckHaulMarketLeadsDetails($statusId, $roleId, $serviceId, $id){
		try{

			//Filters values to populate in the page
			$from_locations = array(""=>"From Location");
			$to_locations = array(""=>"To Location");
			$vehicle_types = array(""=>"Vehicle Type");
			$load_types = array(""=>"Load Type");
			$Query = DB::table ( 'truckhaul_seller_posts as sp' );
			$Query->leftjoin ( 'truckhaul_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
			$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'sp.lkp_post_status_id' );
			if(Session::get('leads') &&  Session::get('leads')==2){
				Session::put('leads', '2');
				$Query->where('sp.lkp_access_id',1);
			}
			else{
				Session::put('leads', '1');
				$Query->leftjoin ( 'truckhaul_seller_selected_buyers as bqss', 'bqss.seller_post_id', '=', 'spi.created_by' );
			}
			//$Query->where('sp.seller_id',Auth::user()->id);
			$Query->where('spi.seller_post_id',$id);

			//conditions to make search
			if(isset($statusId) && $statusId != ''){
				$Query->where('spi.lkp_post_status_id', $statusId);
			}
			if(isset($serviceId) && $serviceId != ''){
				$Query->where('sp.lkp_service_id', $serviceId);
			}

			$sellerresults = $Query->select ( 'spi.id', 'sp.from_date','spi.price','sp.lkp_post_status_id','sp.id as spostid',
				'sp.to_date', 'sp.transaction_id' ,'spi.lkp_vehicle_type_id','spi.lkp_load_type_id','spi.price',
				'sp.lkp_access_id', 'ps.post_status','sp.id as post_id','spi.from_location_id','spi.to_location_id','spi.is_cancelled',
				'spi.transitdays','spi.units','sp.created_by'
			)
			->groupBy('spi.id')
			->get ();
			//Functionality to handle filters based on the selection starts
			foreach($sellerresults as $seller){
				$seller_post_items  = DB::table('truckhaul_seller_post_items')
					->where('truckhaul_seller_post_items.id',$seller->id)
					->select('*')
					->get();
				foreach($seller_post_items as $seller_post_item){
					if(!isset($from_locations[$seller_post_item->from_location_id])){
						$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
					}
					if(!isset($to_locations[$seller_post_item->to_location_id])){
						$to_locations[$seller_post_item->to_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->to_location_id)->pluck('city_name');
					}

					if(!isset($load_types[$seller_post_item->lkp_load_type_id])){
						$load_types[$seller_post_item->lkp_load_type_id] = DB::table('lkp_load_types')->where('id', $seller_post_item->lkp_load_type_id)->pluck('load_type');
					}
					if(!isset($vehicle_types[$seller_post_item->lkp_vehicle_type_id])){
						$vehicle_types[$seller_post_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $seller_post_item->lkp_vehicle_type_id)->pluck('vehicle_type');
					}

				}
			}
			//Functionality to handle filters based on the selection ends
			//echo $Query->tosql();
			//echo "<pre>";print_R($sellerresults);die;
			$grid = DataGrid::source ( $Query );

			$grid->add ( 'id', 'ID', true )->style ( "display:none" );
			$grid->add ( 'from_location_id', 'From', 'from_location_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'to_location_id', 'To', 'to_location_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'lkp_vehicle_type_id', 'Vehicle Type', 'lkp_vehicle_type_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'lkp_load_type_id', 'Load Type', 'lkp_load_type_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'price', 'Price', 'price' )->attributes(array("class" => "col-md-1 padding-left-none"));
			$grid->add ( 'post_status', 'Status', '' )->attributes(array("class" => "col-md-1 padding-left-none"));
			$grid->add ( 'is_cancelled', 'Post Status', true )->style ( "display:none" );
			$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
			$grid->add ( 'spostid', 'Seller Post ID', true )->style ( "display:none" );
			$grid->add ( 'transitdays', 'Transit Days', 'transitdays' )->style ( "display:none" );
			$grid->add ( 'units', 'Units', 'units' )->style ( "display:none" );
			$grid->add ( 'from_date', 'From Date', 'from_date' )->style ( "display:none" );
			$grid->add ( 'to_date', 'To Date', 'to_date' )->style ( "display:none" );
			$grid->add ( 'transaction_id', 'Transaction Id', 'transaction_id' )->style ( "display:none" );
			$grid->add ( 'created_by', 'Created by', 'created_by' )->style ( "display:none" );

			$grid->orderBy ( 'id', 'desc' );
			$grid->paginate ( 5 );


			$grid->row ( function ($row) {

				$row->cells [0]->style ( 'display:none' );
				$row->cells [9]->style ( 'display:none' );
				$spId = $row->cells [0]->value;
				$price=$row->cells [5]->value;
				$row->cells [5]->value = CommonComponent::getPriceType($row->cells [5]->value);	
				$poststaus = $row->cells [7]->value;
				$spostid = $row->cells [9]->value;
				if($row->cells [7]->value == 1 )
					$row->cells [6]->value = "Deleted";
				else
					$row->cells [6]->value = "Open";
				
				$transdays = $row->cells [10]->value;
				$units = $row->cells [11]->value;
				$fromdate = $row->cells [12]->value;
				$todate = $row->cells [13]->value;
				$transaction_id = $row->cells [14]->value;
				$seller_user_id = $row->cells [15]->value;
				

				//View Count
				$countview = DB::table('truckhaul_seller_post_item_views')
					->where('truckhaul_seller_post_item_views.seller_post_item_id','=',$spId)
					->select('truckhaul_seller_post_item_views.id','truckhaul_seller_post_item_views.view_counts')
					->get();
				if(!isset($countview[0]->view_counts))
					$countview = 0;
				else
					$countview = $countview[0]->view_counts;

				$row->cells [1]->value = ''.CommonComponent::getCityName($row->cells [1]->value).'';
				$row->cells [2]->value = ''.CommonComponent::getCityName($row->cells [2]->value).'';
				$row->cells [3]->value = ''.CommonComponent::getVehicleType($row->cells [3]->value).'';
				$row->cells [4]->value = ''.CommonComponent::getLoadType($row->cells [4]->value).'';
				$seller_post_items  = DB::table('truckhaul_seller_post_items')
					->where('truckhaul_seller_post_items.id',$spId)
					->select('*')
					->get();
				
				$row->cells [1]->attributes(array("class" => "col-md-2 padding-left-none "));
				$row->cells [2]->attributes(array("class" => "col-md-2 padding-left-none "));
				$row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none "));
				$row->cells [4]->attributes(array("class" => "col-md-2 padding-left-none "));
				$row->cells [5]->attributes(array("class" => "col-md-1 padding-none "));
				$row->cells [6]->attributes(array("class" => "col-md-1 padding-none "));
				$row->cells [7]->attributes(array("class" => "col-md-2 padding-none"));
				$row->cells [10]->style ( 'display:none' );
				$row->cells [11]->style ( 'display:none' );
				$row->cells [12]->style ( 'display:none' );
				$row->cells [13]->style ( 'display:none' );
				$row->cells [14]->style ( 'display:none' );
				$row->cells [15]->style ( 'display:none' );
				//matching implmentation start
				$total_count = 0;				
				//matching implmentation end
				//Leads Count
				$serviceId = Session::get ( 'service_id' );
				$lead_count = 0;		
				$buyerId = Auth::User()->id;
				//Ftl Calculation did as per krishna sir discussion.(srinu-2-04-2016)
				//User enter no of loads * price =total fright.
				$url = url().'/buyerbooknowforsearch/'.$row->cells [0];
				$row->cells [7]->value = "<div class='col-md-12 col-sm-12 col-xs-12 text-right padding-none'>
											<input type='button' class='btn red-btn pull-right submit-data underline_link spot_transaction_details_list show-data-link'  id=''.$spId.'' value='Book Now' style='display:none'>
										</div>
									</div>

									<div class='pull-right text-right'>
										<div class='info-links'>
											<a id=''.$spId.'' class='viewcount_show-data-link' data-quoteId='$spId'><span class='show-icon spot_transaction_details_list'>+</span><span class='hide-icon'>-</span> Details</a>											
											<a href='#' class='new_message' data-transaction_no='".$transaction_id."' data-userid='".$seller_user_id."' data-buyerquoteitemid='".$spId."'><i class='fa fa-envelope-o'></i></a>
										</div>
									</div>
				
									<div class='col-md-12 show-data-div padding-none padding-top ' style='display: none;'>

											<div class='col-md-12 padding-none'>
												<div class='col-md-3 padding-left-none data-fld'>
													<span class='data-head'>Transit Days</span>
													<span class='data-value'>$transdays $units</span>
												</div>
											</div>
									
											<form method='GET' role='form' action='$url' id='addftlmarketLeadsbooknow_$spId' name='addftlmarketLeadsbooknow_$spId' style='display:none'>
											
												<div class='col-md-3 padding-left-none'>
												 <div class='input-prepend'>  	
													<input type='text' name='noofloads_$spId' id='noofloads_$spId' value='' class='checktotal_price_marketleadsftl form-control form-control1 numericvalidation' placeholder= 'No of Loads' data-id='$spId'>
												</div>
												</div>	
											<input type='hidden' name='sellerprice_$spId' id='sellerprice_$spId' value=$price>
											
											<div class='col-md-3 padding-left-none'>
												Fright : <span class='display_marketledaspriceftl_$spId'></span>
											</div>
											
											<div class='col-md-12 text-right padding-none'>
												<div class='col-md-12 col-sm-12 col-xs-12 text-right padding-none'>
													<input type='submit' class='btn red-btn pull-right buyer_book_now' data-url='$url'  data-buyerpostofferid='$spId' data-booknow_list='$spId' value='Book Now' style='display:none' />
												</div>
											</div>
											
											<input id='buyersearch_booknow_buyer_id_$spId' value='$buyerId' name='buyersearch_booknow_buyer_id_$spId' type='hidden'>
											<input id='buyersearch_booknow_seller_id_$spId' value='$spId' name='buyersearch_booknow_seller_id_$spId' type='hidden'>
											<input id='buyersearch_booknow_seller_price_$spId' value='' name='buyersearch_booknow_seller_price_$spId' type='hidden'>
											<input id='buyersearch_booknow_from_date_$spId' value='$fromdate' type='hidden'>
											<input id='buyersearch_booknow_to_date_$spId' value='$todate' type='hidden'>
											<input id='buyersearch_booknow_dispatch_date_$spId' value='$fromdate' type='hidden'>
											<input id='buyersearch_booknow_delivery_date_$spId' value='$todate' type='hidden'>
											
											</form>		
											<div>																		
									</div>";

			} );

			//Functionality to build filters in the page starts
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'spi.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.to_location_id', 'From Location', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.lkp_vehicle_type_id', 'Vehicle Type', 'select')->options($vehicle_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.lkp_load_type_id', 'Load Type', 'select')->options($load_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");

			$filter->submit('search');
			$filter->reset('reset');
			$filter->build();
			//Functionality to build filters in the page ends

			$result = array();
			$result['grid'] = $grid;
			$result['filter'] = $filter;
			return $result;

		} catch( Exception $e ) {
			return $e->message;
		}
	}

        /**
     * Buyer counter offer Page
     * Method to retrieve buyer quote requests data
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getTHBuyerQuoteDetailsFromId($buyerQuoteItemId) {
        try {
            Log::info('Get buyer quote requests data: ' . Auth::id(), array(
                'c' => '2'
            ));
            $getPostBuyerCounterOfferQuery = DB::table('truckhaul_buyer_quote_items as bqi');
            $getPostBuyerCounterOfferQuery->leftjoin('truckhaul_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');
            $getPostBuyerCounterOfferQuery->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'bqi.lkp_load_type_id');
            $getPostBuyerCounterOfferQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
            $getPostBuyerCounterOfferQuery->leftjoin('lkp_quote_price_types as lqpt', 'lqpt.id', '=', 'bqi.lkp_quote_price_type_id');
            $getPostBuyerCounterOfferQuery->leftjoin('lkp_quote_accesses as lqa', 'lqa.id', '=', 'bq.lkp_quote_access_id');
            if (!empty($buyerQuoteItemId)) {
                $getPostBuyerCounterOfferQuery->where('bqi.id', $buyerQuoteItemId);
            }
            $getPostBuyerCounterOfferQuery->select('bqi.id', 'bqi.dispatch_date', 'bqi.is_cancelled', 'bqi.lkp_post_status_id', 'bqi.is_dispatch_flexible', 'bqi.is_delivery_flexible', 'ldt.load_type', 'bqi.buyer_quote_id', 'bqi.from_city_id', 'bqi.to_city_id', 'bq.lkp_quote_access_id', 'lqa.quote_access', 'lvt.vehicle_type', 'lqpt.price_type', 'bq.transaction_id','bqi.quantity','bqi.number_loads','bqi.units');
            $arrayBuyerCounterOffer = $getPostBuyerCounterOfferQuery->get();
            
            return $arrayBuyerCounterOffer;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }
    
    /**
    * Buyer counter offer Page
    * Method to retrieve private seller name
    * @param int $buyerQuoteItemId
    * @return array
    */
	public static function getPrivateSellerNames($buyerQuoteItemId) {
		try {
			Log::info ( 'Get private seller names: ' . Auth::id (), array (
                        'c' => '2'
                    ));
            $getPostBuyerCounterOfferQuery = DB::table('truckhaul_buyer_quote_items as bqi');
            $getPostBuyerCounterOfferQuery->leftjoin('truckhaul_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');
            $getPostBuyerCounterOfferQuery->leftjoin('truckhaul_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'bq.id');
			$getPostBuyerCounterOfferQuery->leftjoin('users as seller_names', 'seller_names.id', '=', 'pbqss.seller_id');
			if (!empty($buyerQuoteItemId)) {
                $getPostBuyerCounterOfferQuery->where('bqi.id', $buyerQuoteItemId);
            }
            $getPostBuyerCounterOfferQuery->select ('seller_names.username');
            $arrayBuyerCounterOffer = $getPostBuyerCounterOfferQuery->get ();
			return $arrayBuyerCounterOffer;
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}
        
        
        public static function getTHSellerPostItemDetails($id){
		try{
			$seller_post = DB::table('truckhaul_seller_posts as sp')
			->leftjoin('truckhaul_seller_post_items as spi','spi.seller_post_id','=','sp.id')
            ->leftjoin('users','users.id','=','sp.seller_id')
            ->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'spi.lkp_load_type_id')
            ->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'spi.lkp_vehicle_type_id')
            ->leftjoin('lkp_post_statuses as lps', 'lps.id', '=', 'spi.lkp_post_status_id')
            ->leftjoin('buyer_quote_sellers_quotes_prices as bqsp', 'bqsp.seller_post_item_id', '=', 'spi.id')                    
            ->where('spi.id',$id)
            ->select('sp.*','spi.*','users.username','ldt.load_type', 'lvt.vehicle_type', 'lps.post_status',
                    DB::raw("(case when `bqsp`.`final_transit_days` != 0 then bqsp.final_transit_days  when `bqsp`.`initial_transit_days` != 0 then bqsp.initial_transit_days when 'bqsp.id'=0 then spi.transitdays end) as transitdays") )
            ->get();
            if(!empty($seller_post)) {
                $fromLocation = BuyerComponent::getCityNameFromId($seller_post[0]->from_location_id);
                $toLocation = BuyerComponent::getCityNameFromId($seller_post[0]->to_location_id);
                $dispatchDate = CommonComponent::checkAndGetDate($seller_post[0]->to_date);
            } else {
                $fromLocation = '';
                $toLocation = '';
                $deliveryDate = '';
                $dispatchDate = '';
            }
            if(isset($getpostitemids[0]->id))
				$countview = DB::table('truckhaul_seller_post_item_views as spiv')
				->where('spiv.seller_post_item_id','=',$getpostitemids[0]->id)
				->select('spiv.id','spiv.view_counts')
				->get();
			if(!isset($countview[0]->view_counts))
				$countview = 0;
			else
				$countview = $countview[0]->view_counts;
    		return array('id'=>$id,
					 'seller_post'=>$seller_post,
					 'countview'=>$countview,
					 'fromLocation'=>$fromLocation,
					 'toLocation'=>$toLocation,
    				 'tolocationid'=>$seller_post[0]->to_location_id,
					 'dispatchDate'=>$dispatchDate,
		 			);
        } catch (Exception $e) {
            
        }
	}
    
    /**
	 * TH Buyer Private Posts List Page
	 * Retrieval of data related to buyer posts list items to populate in the buyer list widget
	 * Displays a grid with a list of all seller posts
	 */
	public static function listTHBuyerPrivatePosts($statusId, $serviceId, $roleId,$type) {

		// Filters values to populate in the page
		$from_locations = array (
				"" => "From Location"
		);
		$to_locations = array (
				"" => "To Location"
		);
		
		$load_types = array (
				"" => "Load Type"
		);
		$from_date = '';
		$to_date = '';
		$order_no = '';

		// query to retrieve buyer posts list and bind it to the grid
		$Query = DB::table ( 'truckhaul_buyer_quote_items as bqi' );
		$Query->join ( 'lkp_load_types as lt', 'lt.id', '=', 'bqi.lkp_load_type_id' );
		$Query->join ( 'lkp_vehicle_types as vt', 'vt.id', '=', 'bqi.lkp_vehicle_type_id' );
		$Query->join ( 'lkp_post_statuses as ps', 'ps.id', '=', 'bqi.lkp_post_status_id' );
		$Query->join ( 'lkp_cities as cf', 'bqi.from_city_id', '=', 'cf.id' );
		$Query->join ( 'lkp_cities as ct', 'bqi.to_city_id', '=', 'ct.id' );
		$Query->join ( 'truckhaul_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
		$Query->join ( 'lkp_quote_accesses as lqa', 'lqa.id', '=', 'bq.lkp_quote_access_id' );
                $Query->leftjoin ( 'truckhaul_buyer_quote_sellers_quotes_prices as bqss', 'bqss.buyer_quote_item_id', '=', 'bqi.id' );
                $Query->leftjoin ( 'users as us', 'us.id', '=', 'bq.buyer_id' );
                $Query->where ( 'bqss.seller_id', Auth::User ()->id ); 
                
		$Query->whereIn('bqi.lkp_post_status_id',array(2,3,4,5));
                $Query->where('bq.lkp_quote_access_id','=',2);
                $Query->groupBy('bqi.buyer_quote_id');
                $Query->orderBy('bqi.buyer_quote_id', 'DESC');


		// conditions to make search
		if (isset (  $statusId ) &&  $statusId != '' && $statusId!=0) {
			$Query->where ( 'bqi.lkp_post_status_id', '=', $_GET ['status'] );
		}
		if (isset ( $_GET ['from_date'] ) && $_GET ['from_date'] != '') {
			$commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['from_date']);
			$Query->where ( 'bqi.dispatch_date', '>=', $commonDispatchDate );
			$from_date = $commonDispatchDate;
		}
	 	if (isset ( $_GET ['to_date'] ) && $_GET ['to_date'] != '') {
			$commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['to_date']);
			$Query->where ( 'bqi.dispatch_date', '<=', $commonDeliveryhDate);
			$to_date = $commonDeliveryhDate;
		}

		$postResults = $Query->select ( 'bq.buyer_id','us.username','bq.transaction_id','bqi.*', 'lt.load_type', 'vt.vehicle_type', 'ps.post_status', 'ct.city_name as tocity', 'cf.city_name as fromcity',
                        'bq.lkp_quote_access_id','lqa.quote_access')->get ();
		
		// Functionality to handle filters based on the selection starts
		foreach ( $postResults as $post ) {
			$buyer_quotes = DB::table ( 'truckhaul_buyer_quote_items as bqi' )
                                ->leftJoin( 'truckhaul_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' )
                                ->where ( 'bqi.id', $post->id )
                                ->select ( 'bqi.*','bq.lkp_quote_access_id' )->get ();
				
			foreach ( $buyer_quotes as $quotes ) {
				//echo "<pre>"; print_r($quotes);die();
				if (! isset ( $from_locations [$quotes->from_city_id] )) {
					$from_locations [$quotes->from_city_id] = DB::table ( 'lkp_cities' )->where ( 'id', $quotes->from_city_id )->pluck ( 'city_name' );
				}
				if (! isset ( $to_locations [$quotes->to_city_id] )) {
					$to_locations [$quotes->to_city_id] = DB::table ( 'lkp_cities' )->where ( 'id', $quotes->to_city_id )->pluck ( 'city_name' );
				}
				if (! isset ( $load_types [$quotes->lkp_load_type_id] )) {
					$load_types [$quotes->lkp_load_type_id] = DB::table ( 'lkp_load_types' )->where ( 'id', $quotes->lkp_load_type_id )->pluck ( 'load_type' );
				}
				
			}
		}
		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations = CommonComponent::orderArray($to_locations);
		
                //grid
                $grid = DataGrid::source ( $Query );
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Buyer Name', 'username' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'dispatch_date', 'Dispatch Date', 'dispatch_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
                $grid->add ( 'delivery_dates', 'Delivery Dates', 'delivery_dates' )->style ( "display:none" );
		$grid->add ( 'price', 'Pricing', 'price' )->attributes(array("class" => "col-md-2 padding-left-none hidden-xs"))->style ( "display:none" );
		$grid->add ( 'load_type', 'Load Type', 'load_type' )->attributes(array("class" => "col-md-3 padding-left-none"));
                $grid->add ( 'lkp_post_status_id', 'Status', 'lkp_post_status_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'vehicle_type', 'VehicleType', 'vehicle_type' )->style ( "display:none" );
		$grid->add ( 'fromcity', 'FromCity', 'fromcity' )->style ( "display:none" );
		$grid->add ( 'tocity', 'Tocity', 'tocity' )->style ( "display:none" );
		$grid->add ( 'delivery_sdate', 'Delivery Date', 'delivery_sdate' )->style ( "display:none" );
		$grid->add ( 'created_by', 'Created By', 'created_by' )->style ( "display:none" );
		$grid->add ( 'lkp_quote_access_id', 'Quote Access', 'lkp_quote_access_id' )->style ( "display:none" );
		$grid->add ( 'transaction_id', 'transaction_id', 'transaction_id' )->style ( "display:none" );
		$grid->add ( 'quantity', 'quantity', 'quantity' )->style ( "display:none" );
		$grid->add ( 'number_loads', 'number_loads', 'number_loads' )->style ( "display:none" );
		$grid->add ( 'units', 'Units', 'units' )->style ( "display:none" );
                $grid->add ( 'from_city_id', 'From City', 'from_city_id' )->style ( "display:none" );
		$grid->add ( 'to_city_id', 'To City', 'to_city_id' )->style ( "display:none" );
		$grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );
		$grid->row ( function ($row) {
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
			$row->cells [5]->style ( 'width:100%' );
                        $transaction_id=$row->cells[13]->value;
			$accessid = $row->cells [12]->value;
			$buyer_id = $row->cells [11]->value;
			$buyer_quote_id = $row->cells [0]->value;
			//$row->cells [1]->attributes(array("class" => "col-md-3 col-sm-3 col-xs-4 padding-none text-left"));
			//$row->cells [3]->attributes(array("class" => "col-md-3 col-sm-3 col-xs-4 mobile-padding-none"));
			//$row->cells [4]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-1 padding-none hidden-xs"));
			$buyer_name = $row->cells [1]->value;
			$dispatch_date_buyer = $row->cells [2]->value;
                        //$delivery_date_buyer = $row->cells [3]->value;
			$price_buyer = $row->cells [4]->value;
			$fprice = $row->cells [4]->value;			
			$getbqi = DB::table('truckhaul_buyer_quote_items')
				->where('truckhaul_buyer_quote_items.id','=',$buyer_quote_id)
				->select('price', 'lkp_quote_price_type_id')
				->get();
			//echo $buyer_quote_id;exit;
			$buyer_post_status = $row->cells [6]->value;
            $buyer_post_status_id = $row->cells [6]->value;
			$load_type_buyer = $row->cells [5]->value;
			$vechile_type_buyer = $row->cells [7]->value;
			$fromcity_buyer = $row->cells [8]->value;
			$tocity_buyer = $row->cells [9]->value;
			
			if($buyer_post_status == 2){
				$buyer_post_status = 'Open';
			}
			if($buyer_post_status == 3){
				$buyer_post_status = 'Closed';
			}
			if($buyer_post_status == 4){
				$buyer_post_status = 'Booked';
			}
			if($buyer_post_status == 5){
				$buyer_post_status = 'Cancelled';
			}
			
			$units = $row->cells [16]->value;
			$from_city_id = $row->cells [17]->value;
                        $to_city_id = $row->cells [18]->value;
			
			$row->cells [5]->value = '<form id ="addsellersearchpostquoteoffer" name ="addsellersearchpostquoteoffer">';
                        $getInitialQuotePrice = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'initial_quote_price','truckhaul_buyer_quote_sellers_quotes_prices');
                        $getCounterQuotePrice = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'counter_quote_price','truckhaul_buyer_quote_sellers_quotes_prices');
                        $getFinalQuotePrice   = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'final_quote_price','truckhaul_buyer_quote_sellers_quotes_prices');
                        $getFirmQuotePrice   = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'firm_price','truckhaul_buyer_quote_sellers_quotes_prices');
                        $subscription  = DB::table('seller_details')
                        ->where('sellers.user_id',Auth::user()->id)
                        ->select('sellers.subscription_end_date','sellers.subscription_start_date')
                        ->get();

                        if(count($subscription)==0){	
                                $subscription  = DB::table('seller_details')
                                ->where('seller_details.user_id',Auth::user()->id)
                                ->select('seller_details.subscription_end_date','seller_details.subscription_start_date')
                                ->get();
                        }
                        $qty=$row->cells [14]->value;
                        $loads=$row->cells [15]->value;

                        $subs_st_date = date('Y-m-d', strtotime($subscription[0]->subscription_start_date));
                        $subs_end_date = date('Y-m-d', strtotime($subscription[0]->subscription_end_date));
                        $now_date = date('Y-m-d');
                        $dates = CommonComponent::checkAndGetDate($dispatch_date_buyer);
			
                        $row->cells [5]->value .= '<div class=""><div class="col-md-3 padding-left-none">
											'.$buyer_name.'
											<div class="red">
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
											</div>
										</div>
										<div class="col-md-2 padding-left-none">'.CommonComponent::checkAndGetDate($dispatch_date_buyer).'</div>
										
										<div class="col-md-3 padding-left-none">'.$load_type_buyer.'</div>'
                                . '                                             <div class="col-md-2 padding-left-none">'.$buyer_post_status.'</div>';
						
						
                                                $getSellerpost  = TruckHaulSellerComponent::sellerPostDetails($from_city_id,$to_city_id,$buyer_quote_id);
						
						if(isset($getSellerpost[0]->id))
							$seller_post_id_private = $getSellerpost[0]->id;
						else
							$seller_post_id_private = 0;
						if(count($getSellerpost)>0){							
                                   $tracking = CommonComponent::getTrackingType($getSellerpost[0]->tracking);
							if($getSellerpost[0]->lkp_payment_mode_id == 1){
								$payment_type = 'Advance';
								if($getSellerpost[0]->accept_payment_netbanking == 1)
									$payment_type .= ' | NEFT/RTGS';
								if($getSellerpost[0]->accept_payment_credit == 1)
									$payment_type .= ' | Credit Card';
								if($getSellerpost[0]->accept_payment_debit == 1)
									$payment_type .= ' | Debit Card';
							}
							elseif($getSellerpost[0]->lkp_payment_mode_id == 2)
							$payment_type = 'Cash on delivery';
							elseif($getSellerpost[0]->lkp_payment_mode_id == 3)
							$payment_type = 'Cash on pickup';
							else{
								$payment_type = 'Credit';
								if($getSellerpost[0]->accept_credit_netbanking == 1)
									$payment_type .= ' | Net Banking';
								if($getSellerpost[0]->accept_credit_cheque == 1)
									$payment_type .= ' | Cheque / DD';
							}
						
						}else{
							$tracking = '';
							$payment_type ='';
						}
						
						
						$SubmitquotePartial = view('partials.seller.submit_quote')->with([
								'getFirmQuotePrice' => $getFirmQuotePrice,
								'getInitialQuotePrice'=>$getInitialQuotePrice,
								'getCounterQuotePrice'=>$getCounterQuotePrice,
								'getFinalQuotePrice' => $getFinalQuotePrice,
								'now_date' => $now_date,
								'subs_st_date' => $subs_st_date,
								'subs_end_date' => $subs_end_date,
								'getbqi' => $getbqi,
								'buyer_post_status_id'=>$buyer_post_status_id,
								'buyer_id' =>$buyer_id,
								'buyer_quote_id'=>$buyer_quote_id,
								'transaction_id'=>$transaction_id,
								'fromcity_buyer'=>$fromcity_buyer,
								'tocity_buyer'=>$tocity_buyer,
								'dispatch_date_buyer'=>$dispatch_date_buyer,
								'vechile_type_buyer'=>$vechile_type_buyer,
								'load_type_buyer'=>$load_type_buyer,
								'qty'=>$qty,
								'units'=>$units,
								'loads'=>$loads,
								'tracking'=>$tracking,
								'payment_type'=>$payment_type,
								'getSellerpost'=>$getSellerpost,
								'accessid'=>$accessid,
								])->render();
						
						$row->cells [5]->value.=$SubmitquotePartial;
                        //BuyerComponent::viewCountForBuyerForFtl(Auth::User()->id,$buyer_quote_id);
            } );
            $filter = DataFilter::source ( $Query );
            $filter->add('bqi.from_city_id', 'From City', 'select')->options($from_locations)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
            $filter->add('bqi.to_city_id', 'To City', 'select')->options($to_locations)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
			$filter->submit('search');
            $filter->reset('reset');
            $filter->build();
            //Functionality to build filters in the page ends

            $result = array();
            $result['grid'] = $grid;
            $result['filter'] = $filter;
            return $result;    
                
        }

}
