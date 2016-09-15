<?php

namespace App\Components\Ftl;

use DB;
use App\Models\BuyerQuoteItemView;
use App\Models\BuyerQuoteItems;
use App\Models\CartItem;
use App\Models\BuyerQuoteSellersQuotesPrices;
use App\Models\TruckleaseBuyerQuoteSellersQuotesPrice;
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
use App\Models\FtlSearchTerm;
use App\Models\ViewCartItem;
use App\Components\Matching\BuyerMatchingComponent;



class FtlBuyerComponent {

	
	/**
	 * Buyer Posts List Page
	 * Retrieval of data related to buyer posts list items to populate in the buyer list widget
	 * Displays a grid with a list of all seller posts
	 */
	public static function getFtlBuyerPostsList($service_id, $post_status, $enquiry_type) {

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
		$Query = DB::table ( 'buyer_quote_items as bqi' );
		$Query->join ( 'lkp_load_types as lt', 'lt.id', '=', 'bqi.lkp_load_type_id' );
		$Query->join ( 'lkp_vehicle_types as vt', 'vt.id', '=', 'bqi.lkp_vehicle_type_id' );
		$Query->join ( 'lkp_post_statuses as ps', 'ps.id', '=', 'bqi.lkp_post_status_id' );
		$Query->join ( 'lkp_cities as cf', 'bqi.from_city_id', '=', 'cf.id' );
		$Query->join ( 'lkp_cities as ct', 'bqi.to_city_id', '=', 'ct.id' );
		$Query->join ( 'buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
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
				$Query->whereIn ( 'bqi.lkp_post_status_id', array(1,2,3,4,5));
			else
				$Query->where ( 'bqi.lkp_post_status_id', '=', $post_status );
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
			//echo "To Date :"; echo $to_date;die();
		}

		$postResults = $Query->select ( 'bqi.*', 'lt.load_type', 'vt.vehicle_type', 'ps.post_status', 'ct.city_name as toCity', 'cf.city_name as fromCity','bq.lkp_quote_access_id','lqa.quote_access','bq.is_commercial')->get ();
		//echo "<pre>"; print_r($postResults);die();
		// Functionality to handle filters based on the selection starts
		foreach ( $postResults as $post ) {
			$buyer_quotes = DB::table ( 'buyer_quote_items' )->leftJoin( 'buyer_quotes as bq', 'bq.id', '=', 'buyer_quote_items.buyer_quote_id' )->where ( 'buyer_quote_items.id', $post->id )->select ( 'buyer_quote_items.*','bq.lkp_quote_access_id' )->get ();
				
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
		$grid->add ( 'dispatch_date', 'Dispatch Date', 'dispatch_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
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
		$grid->add ( 'to_city_id', 'to city id', 'to_city_id' )->style ( "display:none" );
                $grid->add ( 'is_commercial', 'is_commercial', 'is_commercial' )->style ( "display:none" );

		$grid->orderBy ( 'bqi.id', 'desc' );
		$grid->paginate ( 5 );

		$grid->row ( function ($row) {
			$buyer_quote_id = $row->cells [0]->value;
			$row->cells [0]->style ( 'display:none' );
			$dispatchDate = $row->cells [1]->value;
			//$row->cells [1]->value = '<input type="checkbox" name="buyerpostcheck" id="buyerpostcheck" class="checkBoxClass gridbuyercheckbox" value='.$buyer_quote_id.'><span class="lbl padding-8"></span>'.CommonComponent::checkAndGetDate($dispatchDate);
			$row->cells [7]->style ( 'width:100%' );
			$buyer_access_id = $row->cells [8]->style ( 'display:none' );
			$buyer_id = $row->cells [9]->style ( 'display:none' );
			$buyerCountId = count (BuyerComponent::getBuyerQuoteSellersQuotesPricesFromId( $buyer_quote_id ) );
			$post_status_id = $row->cells [10]->style ( 'display:none' );

			$arraySellerIds = BuyerComponent::getSellerIds($row->cells[9]->style ( 'display:none' ));
			$arrayBuyerLeads = BuyerComponent::getLeadsForBuyer($row->cells[11]->style ( 'display:none' ), $arraySellerIds);

			$countview = BuyerComponent::updateBuyerQuoteDetailsViews($buyer_quote_id);
			
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
			


			$matchedSellerPosts = BuyerMatchingComponent::getMatchedResults(ROAD_FTL,$buyer_quote_id);

			$matchedIds = array();
			foreach($matchedSellerPosts as $matchedSellerPost){
				$matchedIds[] = $matchedSellerPost->seller_post_id;
			}

			$getSellerLeadData = DB::table('seller_post_items as spi');
			$getSellerLeadData->leftjoin('seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
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

                        $from_location_id = $row->cells [11]->value;
                        $to_location_id = $row->cells [13]->value;
                        $is_commercial = $row->cells [14]->value;
                        
                        $lkp_psot_status_condition = $row->cells   [10]->value;
                        
                        //count for buyer documents
                        $serviceId = Session::get('service_id');
                        $docs_buyer    =   CommonComponent::getGsaDocuments(1,$serviceId,$buyer_quote_id,$from_location_id,$to_location_id,$is_commercial);
                                    
                        
                        $msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyer_quote_id);
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
	public static function getFtlBuyerSearchList($request, $serviceId) 
	{
		try 
		{
			
			// Checking dispatch_flexible_hidden variable
			$request['is_dispatch_flexible'] = $request->exists('dispatch_flexible_hidden') ? $request['dispatch_flexible_hidden'] : 0;

			// Checking delivery_flexible_hidden variable
			$request['is_delivery_flexible'] = $request->exists('delivery_flexible_hidden') ? $request['delivery_flexible_hidden'] : 0;

			// Default values for Filters
			$from_locations = [ "" => "From Location" ];
			$to_locations 	= [ "" => "To Location" ];
			$load_types 	= [ "" => "Load Type" ];
			$vehicle_types 	= [ "" => "Vehicle Type"];
			$sellerNames 	= [];
			$paymentMethods = [];
			$prices 		= [];
            
            $trackingfilter = [];
            if($request->has('tracking'))
                $trackingfilter[] = $request->tracking;
                
            if($request->has('tracking1'))
            	$trackingfilter[] = $request->tracking1;
           	
           	$request->merge(['trackingfilter' => $trackingfilter]);

			// Checking Search & Equal to 1 or not
			if( $request->exists('filter_set') && $request->filter_set == 1){
			    
			    if($request->has('ftltopseller_orders'))
					$request['ftltopseller_orders'] = $request['ftltopseller_orders'];

				if($request->has('ftltopseller_rated'))
					$request['ftltopseller_rated'] = $request['ftltopseller_rated'];

				if($request->has('date_flexiable'))
					$request['from_date'] = $request['date_flexiable'];

				if($request->has('is_commercial'))
                    $request['is_commercial'] = $request['is_commercial'];

                $Query_buyers_for_sellers = BuyerSearchComponent::search ($roleId=null,$serviceId,$statusId=null, $request );
				$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();
				
			} else {

				         
                // Below script for buyer search for seller posts join query --for Grid				
				$Query_buyers_for_sellers = BuyerSearchComponent::search ($roleId=null,$serviceId,$statusId=null, $request );
				$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();
				
				if( $request->has('lkp_vehicle_type_id') && $request->has('lkp_load_type_id') && 
					$request->has('from_location_id') && $request->has('to_location_id') && 
					$request->has('from_date') && $request->has('from_location') && 
					$request->has('is_commercial')
				): 
					$sellerpost_for_buyers  =  new FtlSearchTerm();
					$sellerpost_for_buyers->user_id = Auth::id();
					$sellerpost_for_buyers->from_city_id 	= $request->from_location_id;
					$sellerpost_for_buyers->to_city_id 		= $request->to_location_id;
					$sellerpost_for_buyers->dispatch_date 	= $request->from_date;
					$sellerpost_for_buyers->delivery_date 	= $request->to_date;
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
							'delivery_date_buyer' => $request->to_date,
							'dispatch_date_buyer' => $request->from_date,
							'vehicle_type_buyer' => $request->lkp_vehicle_type_id,
							'load_type_buyer' => $request->lkp_load_type_id,
							'from_city_id_buyer' => $request->from_location_id,
							'to_city_id_buyer' => $request->to_location_id,
							'from_location_buyer' => $request->from_location,
							'to_location_buyer' => $request->to_location,
							'quantity_buyer' => $request->quantity,
							'capacity_buyer' => $request->capacity,
							'fdelivery_date_buyer' => $request->delivery_flexible_hidden,
							'fdispatch_date_buyer' => $request->dispatch_flexible_hidden,
							'is_commercial_date_buyer' => $request->is_commercial,
						]
					]);

				endif;
			}			

			// Checking Empty Query or not
			if (empty ( $Query_buyers_for_sellers_filter )):
				CommonComponent::searchTermsSendMail();
                session()->forget('layered_filter_to_location', '');
                session()->forget('layered_filter_from_location', '');
                session()->forget('show_layered_filter','');
            endif;
			
			// Below script for filter data getting from queries --for filters
			foreach ( $Query_buyers_for_sellers_filter as $seller_post_item ) {
                
                // adding New Session variable to existing session array
                session()->put('show_layered_filter', 1);

                //no of loads computation
                $no_loads  = CommonComponent::ftlNoofLoads($seller_post_item->lkp_vehicle_type_id);
				$prices[] = $seller_post_item->price*$no_loads;
                //$actual_prices[] = $seller_post_item->price;

				if (! isset ( $from_locations [$seller_post_item->from_location_id] )):
					$from_locations[$seller_post_item->from_location_id] = DB::table ( 'lkp_cities' )->where('id', $seller_post_item->from_location_id )->pluck('city_name');
				endif;

				if (! isset ( $to_locations [$seller_post_item->to_location_id] )):

					$to_locations [$seller_post_item->to_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $seller_post_item->to_location_id )->pluck ( 'city_name' );
				endif;

				if (! isset ( $load_types [$seller_post_item->lkp_load_type_id] )):
					$load_types [$seller_post_item->lkp_load_type_id] = DB::table('lkp_load_types')->where('id', $seller_post_item->lkp_load_type_id )->pluck('load_type');
				endif;

				if (! isset ( $vehicle_types [$seller_post_item->lkp_vehicle_type_id] )):
					$vehicle_types [$seller_post_item->lkp_vehicle_type_id] = DB::table ( 'lkp_vehicle_types' )->where ( 'id', $seller_post_item->lkp_vehicle_type_id )->pluck ( 'vehicle_type' );
				endif;

				if(isset($request['is_search']) || (isset($request['date_flexiable']) && $request['date_flexiable']!='')):
					if (! isset ( $sellerNames [$seller_post_item->seller_id] )) {
						$sellerNames[$seller_post_item->seller_id] = $seller_post_item->username;
					}
					session()->put('layered_filter', $sellerNames);
				endif;

				if(isset($request['is_search'])):
					if (! isset ( $paymentMethods [$seller_post_item->lkp_payment_mode_id] )) {
						$paymentMethods[$seller_post_item->lkp_payment_mode_id] = $seller_post_item->paymentmethod;
					}
					session()->put('layered_filter_payments', $paymentMethods);
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
			$gridBuyer->add ( 'vehicle_type', 'Vehicle Type', true )->style ( "display:none" );
			$gridBuyer->add ( 'from_date', 'Valid From', true )->attributes(array("class" => "col-md-3 padding-left-none"));
			$gridBuyer->add ( 'to_date', 'Valid To', true )->attributes(array("class" => "col-md-3 padding-left-none"));
			$gridBuyer->add ( 'tracking', 'Tracking', true )->style ( "display:none" );
			$gridBuyer->add ( 'paymentmethod', 'Payment Mode', true )->style ( "display:none" );
			$gridBuyer->add ( 'created_by', 'created by', 'created_by' )->style ( "display:none" );
			$gridBuyer->add ( 'price', 'Price (<i class="fa fa-inr fa-1x"></i>)', true )->attributes(array("class" => "col-md-3 padding-left-none"));
			$gridBuyer->add ( 'transitdays', 'transitdays', true )->style('display:none');
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
			$gridBuyer->add ( 'transitdaysunits', 'transit days units', 'transitdaysunits' )->style('display:none');
				

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
                
                //number of loads calculation client new doc issue(11-03-2016)
                $vehicle_type_id = $row->cells [27]->value;
                /*$vehicle_types_Value = DB::table('lkp_vehicle_types')->select('capacity','units')->where('id', $vehicle_type_id)->get();
                $vehicle_type_id = $vehicle_types_Value[0]->capacity;
                if($vehicle_types_Value[0]->units!="KG")
                    $quantity = Session::get('session_quantity_buyer');
                else
                    $quantity = Session::get('session_quantity_buyer')*1000;*/

                $noofloads  = CommonComponent::ftlNoofLoads($vehicle_type_id);
                
                //$noofloads = ceil($quantity / $vehicle_type_id);
                
				$id = $row->cells [0]->value;
				$vendorname = $row->cells [1]->value;
				$price = $row->cells [14]->value;
				$fromlocation = $row->cells [5]->value;
				$tolocation = $row->cells [6]->value;
				$loadtype = $row->cells [7]->value;
				$vehicletype = $row->cells [8]->value;
				$validfrom = $row->cells [9]->value;
				$validto = $row->cells [10]->value;
				$tracking = $row->cells [11]->value;
				$paymentmode = $row->cells [12]->value;
				$seller_id = $row->cells [13]->value;
				$transitdays = $row->cells[15]->value;
				$transitdaysunits = $row->cells[28]->value;
                $transaction_id=$row->cells[16]->value;
                //pricing tags

                $cancellation_price=$row->cells[18]->value;

                $docket_charge_price=$row->cells[20]->value;
                $other_charge1_text=$row->cells[21]->value;
                $other_charge1_price=$row->cells[22]->value;
                $other_charge2_text=$row->cells[23]->value;
                $other_charge2_price=$row->cells[24]->value;
                $other_charge3_text=$row->cells[25]->value;
                $other_charge3_price=$row->cells[26]->value;
                $dispalyCharges = $row->cells [14]->value * $noofloads;
				//echo $noofloads. "--".$row->cells [14]->value;die;

				Session::put('session_load_type_search', $loadtype);
				Session::put('session_vehicle_type_search', $vehicletype);

				$tracking_text = CommonComponent::getTrackingType($tracking);
				$track_type = '<i class="fa fa-signal"></i>&nbsp;'.$tracking_text;
				if ($paymentmode == 'Advance') {
					$paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
				} elseif ($paymentmode == 'Credit'){
					$credit_days = CommonComponent::getCreditdays($id,'seller_posts','seller_post_items');
					
					$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode.' | '.$credit_days[0]->credit_period.' '.$credit_days[0]->credit_period_units;
				}else {
					$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode;
				}				

				$terms =  CommonComponent::getTermsAndConditions($id,'seller_posts','seller_post_items');
				

                $url = url().'/buyerbooknowforsearch/'.$row->cells [0];
               

				$row->cells [4]->value = "<div class='col-md-3 padding-left-none'>$vendorname
					<div class='red'>
					<i class='fa fa-star'></i>
					<i class='fa fa-star'></i>
					<i class='fa fa-star'></i>
					</div>

				</div>
				<div class='col-md-3 padding-left-none'>".CommonComponent::checkAndGetDate($validfrom)."</div>
				<div class='col-md-3 padding-left-none'>".CommonComponent::checkAndGetDate($validto)."</div>
				<div class='col-md-3 padding-none'> ".CommonComponent::getPriceType($dispalyCharges)."
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
                                        
                        <a id='".$id."' class='viewcount_show-data-link view_count_update' data-quoteId='$id' data-table='seller_post_item_views'><span class='show-icon spot_transaction_details_list'>+</span><span class='hide-icon'>-</span> Details</a>
							
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
							<span class='data-head'>Vehicle Type</span>
							<span class='data-value'>$vehicletype</span>
						</div>

						<div class='clearfix'></div>

						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Load Type</span>
							<span class='data-value'>$loadtype</span>
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
							<span class='data-value'>$transitdays $transitdaysunits</span>
						</div>
						<div class='clearfix'></div>";
                                        if($terms!='' ){
                                            $row->cells [4]->value .= " <div class='col-md-12 padding-left-none data-fld'>
							<span class='data-head'>Terms & Conditions</span>
							<span class='data-value'>$terms</span>
						</div>";
                                        }
					
                                        if($dispalyCharges!=''){
                                            $row->cells [4]->value .= "</div>
                                            <div class='col-md-4 margin-bottom'>
                                                    <span class='data-head'>Total Price</span>
                                                    <span class='data-value big-value'>".CommonComponent::getPriceType($dispalyCharges)."</span>
                                            </div>";
                                        }
					
                                        if($cancellation_price!=''){
                                            $row->cells [4]->value .= "<div class='col-md-4 margin-bottom'>
						<span class='data-head'>Cancellation Charges</span>
						<span class='data-value big-value'>".CommonComponent::getPriceType($cancellation_price)."</span>
                                            </div>";
                                        }
					
                                        if($docket_charge_price!=''){
                                            $row->cells [4]->value .= "<div class='col-md-4 margin-bottom'>
						<span class='data-head'>Docket Charges</span>
						<span class='data-value big-value'>".CommonComponent::getPriceType($docket_charge_price)."</span>
                                            </div>";
                                        }
					
					if($other_charge1_text!='' && $other_charge1_price!=''){
					$row->cells [4]->value .=	"<div class='col-md-4'>
						<span class='data-head'>$other_charge1_text</span>
						<span class='data-value big-value'>$other_charge1_price /-</span>
					</div>";
					
					}
					
					if($other_charge2_text!='' && $other_charge2_price!=''){
						$row->cells [4]->value .=	"<div class='col-md-4'>
						<span class='data-head'>$other_charge2_text</span>
						<span class='data-value big-value'>$other_charge2_price /-</span>
						</div>";
							
					}
					
					if($other_charge3_text!='' && $other_charge3_price!=''){
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
						<input id='buyersearch_booknow_delivery_date_$id' type='hidden' value='".CommonComponent::convertDateForDatabase(Session::get('session_delivery_date_buyer'))."'>
					</div>

					<div class='col-md-12 col-sm-12 col-xs-12 padding-none margin-top buyerbooknow_listdetails_$id' style='display:none'>
					</div>
				</div>
				
				";
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
    public static function getPostBuyerCounterOfferForFtl($buyerQuoteItemId, $comparisonType = null,$priceVal = null,$checkIds=null)
    {
        try {
            Log::info('Get posted buyer counter offer for ftl: '.Auth::id(),array('c'=>'2'));
            $roleId = Auth::User()->lkp_role_id;
    		if($roleId == BUYER){
    			CommonComponent::activityLog("BUYER_FETCHED_SELLER_POST",
    					BUYER_FETCHED_SELLER_POST,0,
    					HTTP_REFERRER,CURRENT_URL);
    		}
            $countview = BuyerComponent::updateBuyerQuoteDetailsViews($buyerQuoteItemId);
            $arrayBuyerCounterOffer = BuyerComponent::getBuyerQuoteDetailsFromId($buyerQuoteItemId);

            if(!empty($arrayBuyerCounterOffer)) {
                $privateSellerNames = BuyerComponent::getPrivateSellerNames($buyerQuoteItemId);
                $arrayBuyerQuoteSellersQuotesPrices = BuyerComponent::getBuyerQuoteSellersQuotesPricesFromId($buyerQuoteItemId, $comparisonType,$priceVal,$checkIds);
                $arraySellerIds = BuyerComponent::getSellerIds($arrayBuyerCounterOffer[0]->buyer_quote_id);
                $arrayBuyerLeads = BuyerComponent::getLeadsForBuyer($arrayBuyerCounterOffer[0]->from_city_id, $arraySellerIds);
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
                $deliveryDate = CommonComponent::checkAndGetDate($arrayBuyerCounterOffer[0]->delivery_date);

                if ($arrayBuyerCounterOffer[0]->is_delivery_flexible == 1 && !empty($deliveryDate)) {
                    $deliveryDate = BuyerComponent::getPreviousNextThreeDays($arrayBuyerCounterOffer[0]->delivery_date);
                }
//                if($arrayBuyerCounterOffer[0]->is_delivery_flexible == 1) {
//                    $deliveryDate = BuyerComponent::getPreviousNextThreeDays($arrayBuyerCounterOffer[0]->delivery_date);
//                } else {
//                    $deliveryDate = CommonComponent::convertDateDisplay($arrayBuyerCounterOffer[0]->delivery_date);
//                }
                $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                $destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
                $packagingType = BuyerComponent::getPackagingType('Destination');
                $buyerPostCounterOfferComparisonTypes = config::get('constants.BUYER_POST_COUNTER_OFFER_COMPARISON_TYPES');
                return [
                            'arrayBuyerCounterOffer' => $arrayBuyerCounterOffer,
                            'privateSellerNames' => $privateSellerNames,
                            'fromLocation' => $fromLocation,
                            'toLocation' => $toLocation,
                            'tolocationid' => $arrayBuyerCounterOffer[0]->to_city_id,
                            'deliveryDate' => $deliveryDate,
                            'dispatchDate' => $dispatchDate,
                            'arrayBuyerQuoteSellersQuotesPrices' => $arrayBuyerQuoteSellersQuotesPrices,
                            'countBuyerLeads' => $countBuyerLeads,
                            'sourceLocation' => $sourceLocationType,
                            'destinationLocation' => $destinationLocationType,
                            'packagingType' => $packagingType,
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
    public static function getSellerLeadsData($serviceId, $buyerQuoteItemId, $PostCourierType=null) {
    	try {
    		$matchedSellerPosts = BuyerMatchingComponent::getMatchedResults($serviceId, $buyerQuoteItemId);
    		$matchedIds = array();
    		foreach($matchedSellerPosts as $matchedSellerPost){
    			$matchedIds[] = $matchedSellerPost->seller_post_id;
    		}
    		if ($serviceId == ROAD_FTL)	{
	    		$getSellerLeadData = DB::table('seller_post_items as spi');
	    		$getSellerLeadData->leftjoin('seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
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
    		} else if($serviceId == ROAD_PTL) {
    			$getSellerLeadData = DB::table('ptl_seller_post_items as spi');
    			$getSellerLeadData->leftjoin('ptl_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
                        $getSellerLeadData->leftjoin('users as u', 'u.id', '=', 'sp.seller_id');
                        $getSellerLeadData->join ( 'lkp_ptl_pincodes as cf', 'spi.from_location_id', '=', 'cf.id' );
                        $getSellerLeadData->join ( 'lkp_ptl_pincodes as ct', 'spi.to_location_id', '=', 'ct.id' );
                        $getSellerLeadData->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
    			$getSellerLeadData->whereIn('spi.id', $matchedIds);
                        $getSellerLeadData->where('spi.is_private', 0);
    			$getSellerLeadData->select('sp.credit_period','sp.credit_period_units','sp.lkp_payment_mode_id','sp.transaction_id as transaction_no','spi.*','u.username','sp.seller_id','sp.from_date','sp.to_date','cf.pincode as frompincode', 'ct.pincode as topincode','cf.postoffice_name as fromcity', 'ct.postoffice_name as tocity','pm.payment_mode as paymentmethod','sp.tracking','sp.terms_conditions','sp.cancellation_charge_price','sp.docket_charge_price',
    										'sp.kg_per_cft','sp.pickup_charges','sp.delivery_charges','sp.oda_charges');
    			$arraySellerLeadsData = $getSellerLeadData->get();
    		}else if($serviceId == RAIL) {
    			$getSellerLeadData = DB::table('rail_seller_post_items as spi');
    			$getSellerLeadData->leftjoin('rail_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');    			
    			$getSellerLeadData->leftjoin('users as u', 'u.id', '=', 'sp.seller_id');
    			$getSellerLeadData->join ( 'lkp_ptl_pincodes as cf', 'spi.from_location_id', '=', 'cf.id' );
    			$getSellerLeadData->join ( 'lkp_ptl_pincodes as ct', 'spi.to_location_id', '=', 'ct.id' );
    			$getSellerLeadData->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
                        $getSellerLeadData->whereIn('spi.id', $matchedIds);
                        $getSellerLeadData->where('spi.is_private', 0);
    			$getSellerLeadData->select('sp.credit_period','sp.credit_period_units','sp.lkp_payment_mode_id','sp.transaction_id as transaction_no','spi.*','u.username','sp.seller_id','sp.from_date','sp.to_date','cf.pincode as frompincode', 'ct.pincode as topincode','cf.postoffice_name as fromcity', 'ct.postoffice_name as tocity','pm.payment_mode as paymentmethod','sp.tracking','sp.terms_conditions','sp.cancellation_charge_price','sp.docket_charge_price',
    										'sp.kg_per_cft','sp.pickup_charges','sp.delivery_charges','sp.oda_charges');
    			$arraySellerLeadsData = $getSellerLeadData->get();
    		}else if($serviceId == AIR_DOMESTIC) {
    			$getSellerLeadData = DB::table('airdom_seller_post_items as spi');
    			$getSellerLeadData->leftjoin('airdom_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');    			
    			$getSellerLeadData->leftjoin('users as u', 'u.id', '=', 'sp.seller_id');
    			$getSellerLeadData->join ( 'lkp_ptl_pincodes as cf', 'spi.from_location_id', '=', 'cf.id' );
    			$getSellerLeadData->join ( 'lkp_ptl_pincodes as ct', 'spi.to_location_id', '=', 'ct.id' );
    			$getSellerLeadData->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
                        $getSellerLeadData->whereIn('spi.id', $matchedIds);
                        $getSellerLeadData->where('spi.is_private', 0);
    			$getSellerLeadData->select('sp.credit_period','sp.credit_period_units','sp.lkp_payment_mode_id','sp.transaction_id as transaction_no','spi.*','u.username','sp.seller_id','sp.from_date','sp.to_date','cf.pincode as frompincode', 'ct.pincode as topincode','cf.postoffice_name as fromcity', 'ct.postoffice_name as tocity','pm.payment_mode as paymentmethod','sp.tracking','sp.terms_conditions','sp.cancellation_charge_price','sp.docket_charge_price',
    										'sp.kg_per_cft','sp.pickup_charges','sp.delivery_charges','sp.oda_charges');
    			$arraySellerLeadsData = $getSellerLeadData->get();
    		}else if($serviceId == AIR_INTERNATIONAL) {
    			$getSellerLeadData = DB::table('airint_seller_post_items as spi');
    			$getSellerLeadData->leftjoin('airint_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');    			
    			$getSellerLeadData->leftjoin('users as u', 'u.id', '=', 'sp.seller_id');
    			$getSellerLeadData->leftJoin('lkp_airports as cf', 'spi.from_location_id', '=', 'cf.id' );
    			$getSellerLeadData->leftJoin('lkp_airports as ct', 'spi.to_location_id', '=', 'ct.id');
    			$getSellerLeadData->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
                        $getSellerLeadData->whereIn('spi.id', $matchedIds);
                        $getSellerLeadData->where('spi.is_private', 0);
    			$getSellerLeadData->select('sp.credit_period','sp.credit_period_units','sp.lkp_payment_mode_id','sp.transaction_id as transaction_no','spi.*','u.username','sp.seller_id','sp.from_date','sp.to_date','cf.location as frompincode', 'ct.location as topincode','cf.airport_name as fromcity', 'ct.airport_name as tocity','pm.payment_mode as paymentmethod','sp.tracking','sp.terms_conditions','sp.cancellation_charge_price','sp.docket_charge_price',
    										'sp.kg_per_cft','sp.pickup_charges','sp.delivery_charges','sp.oda_charges');
    			$arraySellerLeadsData = $getSellerLeadData->get();
    		}else if($serviceId == OCEAN) {
    			$getSellerLeadData = DB::table('ocean_seller_post_items as spi');
    			$getSellerLeadData->leftjoin('ocean_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');    			
    			$getSellerLeadData->leftjoin('users as u', 'u.id', '=', 'sp.seller_id');
    			$getSellerLeadData->leftJoin('lkp_seaports as cf', 'spi.from_location_id', '=', 'cf.id');
    			$getSellerLeadData->leftJoin('lkp_seaports as ct', 'spi.to_location_id', '=', 'ct.id');
    			$getSellerLeadData->join ('lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
                        $getSellerLeadData->whereIn('spi.id', $matchedIds);
                        $getSellerLeadData->where('spi.is_private', 0);
    			$getSellerLeadData->select('sp.credit_period','sp.credit_period_units','sp.lkp_payment_mode_id','sp.transaction_id as transaction_no','spi.*','u.username','sp.seller_id','sp.from_date','sp.to_date','cf.country_name as frompincode', 'ct.country_name as topincode','cf.seaport_name as fromcity', 'ct.seaport_name as tocity','pm.payment_mode as paymentmethod','sp.tracking','sp.terms_conditions','sp.cancellation_charge_price','sp.docket_charge_price',
    										'sp.kg_per_cft','sp.pickup_charges','sp.delivery_charges','sp.oda_charges');
    			$arraySellerLeadsData = $getSellerLeadData->get();
    		}else if($serviceId == COURIER) {
                        if($PostCourierType == 'Document') {
                            $corType=1;
                        } else {
                            $corType=2; 
                        }
                        $getSellerLeadData = DB::table('courier_seller_post_items as spi');
                        $getSellerLeadData->leftjoin('courier_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
                        $getSellerLeadData->leftjoin('users as u', 'u.id', '=', 'sp.seller_id');
                        $getSellerLeadData->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
                        $getSellerLeadData->whereIn('spi.id', $matchedIds);
                        $getSellerLeadData->where('spi.is_private', 0);
                        $getSellerLeadData->where('sp.lkp_courier_type_id', '=', $corType );
                        $getSellerLeadData->select('sp.credit_period','sp.credit_period_units','sp.lkp_payment_mode_id','sp.transaction_id as transaction_no','spi.*','u.username','sp.seller_id','sp.from_date','sp.to_date','pm.payment_mode as paymentmethod','pm.id as paymentmodeid','sp.tracking','sp.terms_conditions','sp.cancellation_charge_price','sp.docket_charge_price','sp.conversion_factor as kg_per_cft',
                                                                                'sp.max_weight_accepted','sp.increment_weight','sp.fuel_surcharge','sp.cod_charge','sp.arc_charge','sp.fuel_surcharge','sp.freight_collect_charge','sp.is_incremental','sp.rate_per_increment','sp.lkp_courier_type_id');
                        $arraySellerLeadsData = $getSellerLeadData->get();
                        //echo "<pre>"; print_r($arraySellerLeadsData); die;
    		}else {
    			$getSellerLeadData = DB::table('seller_post_items as spi');
	    		$getSellerLeadData->leftjoin('seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
	    		$getSellerLeadData->leftjoin('users as u', 'u.id', '=', 'sp.seller_id');
	    		$getSellerLeadData->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'spi.lkp_load_type_id');
	    		$getSellerLeadData->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'spi.lkp_vehicle_type_id');
	    		$getSellerLeadData->join ( 'lkp_cities as cf', 'spi.from_location_id', '=', 'cf.id' );
	    		$getSellerLeadData->join ( 'lkp_cities as ct', 'spi.to_location_id', '=', 'ct.id' );
	    		$getSellerLeadData->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
	    		$getSellerLeadData->whereIn('spi.id', $matchedIds);
                        $getSellerLeadData->where('spi.is_private', 0);
	    		$getSellerLeadData->select('sp.credit_period','sp.credit_period_units','sp.lkp_payment_mode_id','sp.transaction_id as transaction_no','spi.*','sp.seller_id','sp.from_date','sp.to_date','ldt.load_type','lvt.vehicle_type','u.username','cf.city_name as fromcity', 'ct.city_name as tocity','sp.lkp_payment_mode_id','pm.payment_mode as paymentmethod','sp.tracking','sp.terms_conditions','sp.cancellation_charge_price','sp.docket_charge_price');
	    		$arraySellerLeadsData = $getSellerLeadData->get();  	
    		}
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
    public static function setPostBuyerCounterOfferForFtl($input)
    {
        try{
            Log::info('Set buyer counter offer for ftl: '.Auth::id(),array('c'=>'2'));
            $roleId = Auth::User()->lkp_role_id;
    		if($roleId == BUYER){
    			CommonComponent::activityLog("BUYER_INSERTED_COUNTER_OFFER",BUYER_INSERTED_COUNTER_OFFER,0,HTTP_REFERRER,CURRENT_URL);
    		}
            //Save data into txnprojectinviteerequests
            $updatedAt = date ('Y-m-d H:i:s');
            $updatedIp = $_SERVER['REMOTE_ADDR'];
            $updatedBy = Auth::User()->user_id;
            
            BuyerQuoteSellersQuotesPrices::where(["id" => $input['buyerCounterOfferId']])
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
            CommonComponent::auditLog($input['buyerCounterOfferId'],'buyer_quote_sellers_quotes_prices');
            $buyerDetails = DB::table('buyer_quote_sellers_quotes_prices as bqsqp')
                            ->where(['id' => $input['buyerCounterOfferId'] ])->select('bqsqp.seller_id')->get();
            if(!empty($buyerDetails)) {
                //CommonComponent::sendEmail(COUNTER_OFFER_BY_BUYER,$buyerDetails[0]->seller_id);
                $sellerCounterOfferEmail = DB::table('users')->where('id', $buyerDetails[0]->seller_id)->get();
                $sellerCounterOfferEmail[0]->buyername = Auth::User()->username;
                CommonComponent::send_email(COUNTER_OFFER_BY_BUYER,$sellerCounterOfferEmail);
                
                
                //*******Send Sms to the Sellers,buyer counter offer***********************//
                
                $getBuyerpostdetails  = DB::table('buyer_quote_sellers_quotes_prices as bqsqp')
            				->leftjoin('seller_post_items as spi', 'bqsqp.seller_post_item_id', '=', 'spi.id')
            				->leftjoin('seller_posts as sp', 'spi.seller_post_id', '=', 'sp.id')
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
    
    
    //Truck Lease Counter Offer
   
    public static function setPostBuyerCounterOfferForTL($input)
    {
    	try{
    		Log::info('Set buyer counter offer for ftl: '.Auth::id(),array('c'=>'2'));
    		$roleId = Auth::User()->lkp_role_id;
    		if($roleId == BUYER){
    			CommonComponent::activityLog("BUYER_INSERTED_COUNTER_OFFER",BUYER_INSERTED_COUNTER_OFFER,0,HTTP_REFERRER,CURRENT_URL);
    		}
    		//Save data into txnprojectinviteerequests
    		$updatedAt = date ('Y-m-d H:i:s');
    		$updatedIp = $_SERVER['REMOTE_ADDR'];
    		$updatedBy = Auth::User()->user_id;
    
    		TruckleaseBuyerQuoteSellersQuotesPrice::where(["id" => $input['buyerCounterOfferId']])
    		->update(
    		array(
    		'counter_quote_price' => $input['counterOfferValue'],
    		'updated_at' => $updatedAt,
    		'updated_ip' => $updatedIp,
    		'updated_by' => $updatedBy,
    		'counter_quote_created_at' => $updatedAt
    		)
    		);
    		//CommonComponent::auditLog($input['buyerCounterOfferId'],'trucklease_buyer_quote_sellers_quotes_prices');
    		$buyerDetails = DB::table('trucklease_buyer_quote_sellers_quotes_prices as bqsqp')
    		->where(['id' => $input['buyerCounterOfferId'] ])->select('bqsqp.seller_id')->get();
    		if(!empty($buyerDetails)) {
    			//CommonComponent::sendEmail(COUNTER_OFFER_BY_BUYER,$buyerDetails[0]->seller_id);
    			$sellerCounterOfferEmail = DB::table('users')->where('id', $buyerDetails[0]->seller_id)->get();
    			$sellerCounterOfferEmail[0]->buyername = Auth::User()->username;
    			CommonComponent::send_email(COUNTER_OFFER_BY_BUYER,$sellerCounterOfferEmail);
    
    
    			//*******Send Sms to the Sellers,buyer counter offer***********************//
    
    			$getBuyerpostdetails  = DB::table('trucklease_buyer_quote_sellers_quotes_prices as bqsqp')
    			->leftjoin('trucklease_seller_post_items as spi', 'bqsqp.seller_post_item_id', '=', 'spi.id')
    			->leftjoin('trucklease_seller_posts as sp', 'spi.seller_post_id', '=', 'sp.id')
    			->where('bqsqp.id','=',$input['buyerCounterOfferId'])
    			->select('sp.transaction_id','bqsqp.seller_id')->get();
    			$msg_params = array(
    					'randnumber' => $getBuyerpostdetails[0]->transaction_id,
    					'buyername' => Auth::User()->username,
    					'servicename' => 'ROAD_TRUCK_LEASE'
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
    public static function setBuyerBooknowForFtl($input)
    {
    	Log::info('Insert the buyer booknow data for ftl: '.Auth::id(),array('c'=>'2'));
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
            switch ($serviceId) {
            case ROAD_FTL:
                $postPaymentMethods = DB::table('seller_posts')
                                    ->leftjoin('seller_post_items','seller_post_items.seller_post_id','=','seller_posts.id')
                                    ->leftjoin ( 'buyer_quote_sellers_quotes_prices as bqsp', 'bqsp.seller_post_item_id', '=', 'seller_post_items.id' )
                                    ->where('seller_post_items.id',$input['postItemId'])
                                    ->select('seller_post_items.id', 'seller_post_items.transitdays', 'seller_post_items.units', 'seller_posts.lkp_payment_mode_id',
                                            DB::raw("(case when `bqsp`.`final_transit_days` != 0 then bqsp.final_transit_days  when `bqsp`.`initial_transit_days` != 0 then bqsp.initial_transit_days when 'bqsp.id'=0 then seller_post_items.transitdays end) as transitdays"))
                                    ->get();

                break;
            case RELOCATION_DOMESTIC:
                $postPaymentMethods = DB::table('relocation_seller_posts as sp')
                                    ->leftjoin('relocation_seller_post_items as spi','spi.seller_post_id','=','sp.id')
                                    ->leftjoin ( 'relocation_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_id', '=', 'spi.id' )
                                    ->where('sp.id',$input['postItemId'])
                                    ->select('sp.id', 'spi.transitdays', 'spi.units', 'sp.lkp_payment_mode_id',
                                            DB::raw("(case when `pbqsqp`.`transit_days` != 0 then pbqsqp.transit_days   when 'pbqsqp.id'=0 then spi.transitdays end) as transitdays"))
                                    ->get();
                break;
             case RELOCATION_INTERNATIONAL:
             		$chkInternationalType = DB::table('relocationint_seller_posts as sp')
                                    ->where('sp.id',$input['postItemId'])
                                    ->select('sp.lkp_international_type_id')
                                    ->get();

            		if($chkInternationalType[0]->lkp_international_type_id == INTERNATIONAL_TYPE_AIR || Session::get('session_service_type_buyer') == INTERNATIONAL_TYPE_AIR){
						$postPaymentMethods = DB::table('relocationint_seller_posts as sp')
                                    ->leftjoin ( 'relocationint_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_id', '=', 'sp.id' )
                                    ->where('sp.id',$input['postItemId'])
                                    ->select('sp.id', 'sp.transitdays', 'sp.units', 'sp.lkp_payment_mode_id',
                                            DB::raw("(case when `pbqsqp`.`transit_days` != 0 then pbqsqp.transit_days   when 'pbqsqp.id'=0 then sp.transitdays end) as transitdays"))
                                    ->get();
					}else if($chkInternationalType[0]->lkp_international_type_id == INTERNATIONAL_TYPE_OCEAN || Session::get('session_service_type_buyer') == INTERNATIONAL_TYPE_OCEAN){
						$postPaymentMethods = DB::table('relocationint_seller_posts as sp')
                                    ->leftjoin('relocationint_seller_post_items as spi','spi.seller_post_id','=','sp.id')
                                    ->leftjoin ( 'relocation_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_id', '=', 'spi.id' )
                                    ->where('sp.id',$input['postItemId'])
                                    ->select('sp.id', 'spi.transitdays', 'spi.units', 'sp.lkp_payment_mode_id',
                                            DB::raw("(case when `pbqsqp`.`transit_days` != 0 then pbqsqp.transit_days   when 'pbqsqp.id'=0 then spi.transitdays end) as transitdays"))
                                    ->get();
					}                
                break;
            case RELOCATION_PET_MOVE:
                $postPaymentMethods = DB::table('relocationpet_seller_posts as sp')
                                    ->leftjoin('relocationpet_seller_post_items as spi','spi.seller_post_id','=','sp.id')
                                    ->leftjoin ( 'relocationpet_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_id', '=', 'spi.id' )
                                    ->where('sp.id',$input['postItemId'])
                                    ->select('sp.id', 'spi.transitdays', 'spi.units', 'sp.lkp_payment_mode_id',
                                            DB::raw("(case when `pbqsqp`.`transit_days` != 0 then pbqsqp.transit_days   when 'pbqsqp.id'=0 then spi.transitdays end) as transitdays"))
                                    ->get();
                break;
 			case RELOCATION_OFFICE_MOVE:
                $postPaymentMethods = DB::table('relocationoffice_seller_posts as sp')
                                     ->leftjoin ( 'relocationoffice_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_id', '=', 'sp.id' )
                                    ->where('sp.id',$input['postItemId'])
                                    ->select('sp.id', 'sp.lkp_payment_mode_id')
                                    ->get();
                break; 
            case RELOCATION_GLOBAL_MOBILITY:
                $postPaymentMethods = DB::table('relocationgm_seller_posts as sp')
                                     ->leftjoin ( 'relocationgm_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_id', '=', 'sp.id' )
                                    ->where('sp.id',$input['postItemId'])
                                    ->select('sp.id', 'sp.lkp_payment_mode_id')
                                    ->get();
                $buyerpost = DB::table('relocationgm_buyer_posts as bp')
                                    ->where('bp.id',$input['quoteItemId'])
                                    ->select('bp.dispatch_date')
                                    ->first();
                break;                
            default:
                $postPaymentMethods = DB::table('seller_posts')
                                    ->leftjoin('seller_post_items','seller_post_items.seller_post_id','=','seller_posts.id')
                                    ->leftjoin ( 'buyer_quote_sellers_quotes_prices as bqsp', 'bqsp.seller_post_item_id', '=', 'seller_post_items.id' )
                                    ->where('seller_post_items.id',$input['postItemId'])
                                    
                                    ->select('seller_post_items.id', 'seller_post_items.transitdays', 'seller_post_items.units', 'seller_posts.lkp_payment_mode_id',
                                            DB::raw("(case when `bqsp`.`final_transit_days` != 0 then bqsp.final_transit_days  when `bqsp`.`initial_transit_days` != 0 then bqsp.initial_transit_days when 'bqsp.id'=0 then seller_post_items.transitdays end) as transitdays")  )
                                    ->get();
                break;
            }
/*echo "<pre>";
print_r($postPaymentMethods);
exit;*/

           if((empty($input['sellerPostedToDate']) || $input['sellerPostedToDate'] == '0000-00-00')) {
                if($serviceId!=RELOCATION_OFFICE_MOVE && $serviceId!=RELOCATION_GLOBAL_MOBILITY )
	            {
	                $transitTime = $postPaymentMethods[0]->transitdays;
	                $transitTimeUnit = $postPaymentMethods[0]->units;
	                if($transitTimeUnit == 'Weeks') {
	                    $transitDays = $transitTimeUnit * 7;
	                } else {
	                    $transitDays = $transitTime;
	                }
	            }elseif($serviceId==RELOCATION_GLOBAL_MOBILITY ){
	            	$transitDays = 0;
	            }
	            else{
	            	$transitDays = RELOCAITON_OFFICE_MOVE_DELIVERDAYS;
	            }    
                if($serviceId==RELOCATION_GLOBAL_MOBILITY )    
                $deliveryDate = date("Y-m-d", strtotime("+".$transitDays." days", strtotime(CommonComponent::convertDateForDatabase($buyerpost->dispatch_date))));
                else
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
                if(Session::get('service_id')==RELOCATION_DOMESTIC || Session::get('service_id')==RELOCATION_INTERNATIONAL || Session::get('service_id')==RELOCATION_OFFICE_MOVE || Session::get('service_id')==RELOCATION_PET_MOVE || Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY){
                    $booknowAddToCart->buyer_quote_id = $input['quoteItemId'];
                }else{
                    $booknowAddToCart->buyer_quote_item_id = $input['quoteItemId'];
                }
                $booknowAddToCart->lkp_payment_mode_id = $postPaymentMethod;
                if(Session::get('service_id') == RELOCATION_INTERNATIONAL){
                	if(Session::get('session_service_type_buyer') != ''){
                		$booknowAddToCart->lkp_international_type_id = Session::get('session_service_type_buyer');
                	}else if($chkInternationalType[0]->lkp_international_type_id != ''){
                		$booknowAddToCart->lkp_international_type_id = $chkInternationalType[0]->lkp_international_type_id;
                	}else{
                		$booknowAddToCart->lkp_international_type_id = '';
                	}               		
            	}else{
            		$booknowAddToCart->lkp_international_type_id = '';
            	}
                $booknowAddToCart->seller_post_item_id = $input['postItemId'];
                $booknowAddToCart->lkp_src_location_type_id = $input['sourceLocationType'];
                if(isset($input['destinationLocationType']))
                $booknowAddToCart->lkp_dest_location_type_id = $input['destinationLocationType'];
                if(isset($input['packagingType']))
                	$booknowAddToCart->lkp_packaging_type_id = $input['packagingType'];
                if($input['sourceLocationType']=='11')
                $booknowAddToCart->other_src_location_type = $input['sourceLocationTypeOther'];
                if(isset($input['destinationLocationType']) && $input['destinationLocationType']=='11')
                $booknowAddToCart->other_dest_location_type = $input['destinationLocationTypeOther'];
                if(isset($input['packagingType']) && $input['packagingType']=='13')
                $booknowAddToCart->other_packaging_type = $input['packagingTypeOther'];

                /*if($serviceId==ROAD_FTL)
                    $booknowAddToCart->price = $cart[0]->no_loads*$input['price'];
                else*/
                    $booknowAddToCart->price = $input['price'];
                if($serviceId==RELOCATION_GLOBAL_MOBILITY)
                    $booknowAddToCart->buyer_consignment_pick_up_date = CommonComponent::convertDateForDatabase($buyerpost->dispatch_date);
                else
                $booknowAddToCart->buyer_consignment_pick_up_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
                if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
                $booknowAddToCart->buyer_consignment_value = $input['consignmentValue'];
                $booknowAddToCart->buyer_consignment_needs_insurance = $input['consignmentNeedInsurance'];
                }
                $booknowAddToCart->buyer_consignor_name = $input['consignorName'];
                $booknowAddToCart->buyer_consignor_mobile = $input['consignorNumber'];
                $booknowAddToCart->buyer_consignor_email = $input['consignorEmail'];
                $booknowAddToCart->buyer_consignor_address = $input['consignorAddress'];
                $booknowAddToCart->buyer_consignor_pincode = $input['consignorPin'];
                if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
                $booknowAddToCart->buyer_consignee_name = $input['consigneeName'];
                $booknowAddToCart->buyer_consignee_mobile = $input['consigneeNumber'];
                $booknowAddToCart->buyer_consignee_email = $input['consigneeEmail'];
                $booknowAddToCart->buyer_consignee_pincode = $input['consigneePin'];
                $booknowAddToCart->buyer_consignee_address = $input['consigneeAddress'];
                $booknowAddToCart->buyer_additional_details = $input['additionalDetails'];
                }
                //$booknowAddToCart->buyer_consignment_value = $input['buyerCounterOfferId'];
                if($serviceId==RELOCATION_GLOBAL_MOBILITY){
                    $booknowAddToCart->dispatch_date = CommonComponent::convertDateForDatabase($buyerpost->dispatch_date);
                    $booknowAddToCart->delivery_date = '';
                }else{
                    $booknowAddToCart->dispatch_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
                    $booknowAddToCart->delivery_date = $deliveryDate;
                }
                $created_at = date ( 'Y-m-d H:i:s' );
                $createdIp = $_SERVER['REMOTE_ADDR'];
                $booknowAddToCart->created_by = Auth::id();
                $booknowAddToCart->created_at = $created_at;
                $booknowAddToCart->created_ip = $createdIp;
                if($booknowAddToCart->save()){
                    if(!empty($input['postItemId'])) {
                        //FtlBuyerComponent::changeStatusForSellerPostItem($input['postItemId'], INCART);
                    }
                    CommonComponent::auditLog($booknowAddToCart->id,'cart_items');
                    $cartInsertId = $booknowAddToCart->id;
                    switch ($serviceId) {
                        case ROAD_FTL:
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
			        LEFT JOIN seller_post_items psi1 on q.seller_post_item_id = psi1.id and q.lkp_service_id = 1 
			        LEFT JOIN seller_posts ps1 on psi1.seller_post_id = ps1.id and q.lkp_service_id = 1
			        LEFT JOIN buyer_quote_items bq1 on bq1.id = q.buyer_quote_item_id and q.lkp_service_id = 1
			        LEFT JOIN lkp_cities pz1
			              ON (psi1.from_location_id = pz1.id and q.lkp_service_id = 1)
			        LEFT JOIN lkp_cities pzt1
			              ON (psi1.to_location_id = pzt1.id and q.lkp_service_id = 1)       
			        where q.id ='".$cartInsertId."'"));
                            $sellerPostTableName = 'seller_post_items';
                            break;
                    case RELOCATION_DOMESTIC:
                            $cartData =  DB::select( DB::raw("SELECT
			        q.*,
			        u.username,
			        q.price,
			        pz1.city_name as from_location,
			        pzt1.city_name as to_location,
			        service.service_name,
			        bq1.dispatch_date as dispatch_date,
			        bq1.lkp_post_status_id as post_status
			        FROM
			        cart_items q
			        LEFT JOIN users u on u.id = q.seller_id
			        LEFT JOIN lkp_services service on service.id = q.lkp_service_id
                                LEFT JOIN relocation_seller_posts ps1 on q.seller_post_item_id = ps1.id and q.lkp_service_id = 15
			        LEFT JOIN relocation_buyer_posts bq1 on bq1.id = q.buyer_quote_id and q.lkp_service_id = 15
			        LEFT JOIN lkp_cities pz1
			              ON (ps1.from_location_id = pz1.id and q.lkp_service_id = 15)
			        LEFT JOIN lkp_cities pzt1
			              ON (ps1.to_location_id = pzt1.id and q.lkp_service_id = 15)       
			        where q.id ='".$cartInsertId."'"));
                            $sellerPostTableName = 'relocation_seller_posts';
                            break;
                case RELOCATION_INTERNATIONAL:
                            $cartData =  DB::select( DB::raw("SELECT
			        q.*,
			        u.username,
			        q.price,
			        pz1.city_name as from_location,
			        pzt1.city_name as to_location,
			        service.service_name,
			        bq1.dispatch_date as dispatch_date,
			        bq1.lkp_post_status_id as post_status
			        FROM
			        cart_items q
			        LEFT JOIN users u on u.id = q.seller_id
			        LEFT JOIN lkp_services service on service.id = q.lkp_service_id
                    LEFT JOIN relocationint_seller_posts ps1 on q.seller_post_item_id = ps1.id and q.lkp_service_id = 18
			        LEFT JOIN relocation_buyer_posts bq1 on bq1.id = q.buyer_quote_id and q.lkp_service_id = 18
			        LEFT JOIN lkp_cities pz1
			              ON (ps1.from_location_id = pz1.id and q.lkp_service_id = 18)
			        LEFT JOIN lkp_cities pzt1
			              ON (ps1.to_location_id = pzt1.id and q.lkp_service_id = 18)       
			        where q.id ='".$cartInsertId."'"));
                            $sellerPostTableName = 'relocationint_seller_posts';
                            break;

       			case RELOCATION_OFFICE_MOVE:

                            $cartData =  DB::select( DB::raw("SELECT
								        q.*,
								        u.username,
								        q.price,
								        pz1.city_name as from_location,
								        service.service_name,
								        bq1.dispatch_date as dispatch_date,
								        bq1.lkp_post_status_id as post_status
								        FROM
								        cart_items q
								        LEFT JOIN users u on u.id = q.seller_id
								        LEFT JOIN lkp_services service on service.id = q.lkp_service_id
					                                LEFT JOIN relocationoffice_seller_posts ps1 on q.seller_post_item_id = ps1.id and q.lkp_service_id = 20
								        LEFT JOIN relocationoffice_buyer_posts bq1 on bq1.id = q.buyer_quote_id and q.lkp_service_id = 20
								        LEFT JOIN lkp_cities pz1
								              ON (ps1.from_location_id = pz1.id and q.lkp_service_id = 20)
								        where q.id ='".$cartInsertId."'"));
					            $sellerPostTableName = 'relocationoffice_seller_posts';
                            break;     
                        case RELOCATION_PET_MOVE:
                            $cartData =  DB::select( DB::raw("SELECT
			        q.*,
			        u.username,
			        q.price,
			        pz1.city_name as from_location,
			        pzt1.city_name as to_location,
			        service.service_name,
			        bq1.dispatch_date as dispatch_date,
			        bq1.lkp_post_status_id as post_status
			        FROM
			        cart_items q
			        LEFT JOIN users u on u.id = q.seller_id
			        LEFT JOIN lkp_services service on service.id = q.lkp_service_id
                                LEFT JOIN relocationpet_seller_posts ps1 on q.seller_post_item_id = ps1.id and q.lkp_service_id = 17
                                LEFT JOIN relocationpet_seller_post_items psi1 on psi1.seller_post_id = psi1.id and q.lkp_service_id = 17
                                
			        LEFT JOIN relocationpet_buyer_posts bq1 on bq1.id = q.buyer_quote_id and q.lkp_service_id = 17
			        LEFT JOIN lkp_cities pz1
			              ON (ps1.from_location_id = pz1.id and q.lkp_service_id = 17)
			        LEFT JOIN lkp_cities pzt1
			              ON (ps1.to_location_id = pzt1.id and q.lkp_service_id = 17)       
			        where q.id ='".$cartInsertId."'"));
                            $sellerPostTableName = 'relocationpet_seller_posts';
                            break;
                        case RELOCATION_GLOBAL_MOBILITY:
                            $cartData =  DB::select( DB::raw("SELECT
			        q.*,
			        u.username,
			        q.price,
			        pzt1.city_name as to_location,
			        service.service_name,
			        bq1.dispatch_date as dispatch_date,
			        bq1.lkp_post_status_id as post_status
			        FROM cart_items q
			        LEFT JOIN users u on u.id = q.seller_id
			        LEFT JOIN lkp_services service on service.id = q.lkp_service_id
                                LEFT JOIN relocationgm_seller_posts ps1 on q.seller_post_item_id = ps1.id and q.lkp_service_id = 19
			        LEFT JOIN relocationgm_buyer_posts bq1 on bq1.id = q.buyer_quote_id and q.lkp_service_id = 19
			        LEFT JOIN lkp_cities pzt1
			              ON (ps1.location_id = pzt1.id and q.lkp_service_id = 19)       
			        where q.id ='".$cartInsertId."'"));
                            $sellerPostTableName = 'relocationgm_seller_posts';
                            break;
                        
                        
                    }

                    $booknowAddToCart  =  new ViewCartItem();
                    $booknowAddToCart->id = $cartInsertId;
	                $booknowAddToCart->seller_id = $input['sellerId'];
	                $booknowAddToCart->buyer_id = $input['buyerId'];
	                $booknowAddToCart->lkp_service_id = Session::get('service_id');
                        if(Session::get('service_id')==RELOCATION_DOMESTIC || Session::get('service_id')==RELOCATION_INTERNATIONAL || Session::get('service_id')==RELOCATION_OFFICE_MOVE || Session::get('service_id')==RELOCATION_PET_MOVE || Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY){
                            $booknowAddToCart->buyer_quote_id = $input['quoteItemId'];
                        }else{
                            $booknowAddToCart->buyer_quote_item_id = $input['quoteItemId'];
                        }
	        
	                //$booknowAddToCart->buyer_quote_item_id = $input['quoteItemId'];
	                $booknowAddToCart->lkp_payment_mode_id = $postPaymentMethod;
	                 if(Session::get('service_id') == RELOCATION_INTERNATIONAL){
                	if(Session::get('session_service_type_buyer') != ''){
                		$booknowAddToCart->lkp_international_type_id = Session::get('session_service_type_buyer');
                	}else if($chkInternationalType[0]->lkp_international_type_id != ''){
                		$booknowAddToCart->lkp_international_type_id = $chkInternationalType[0]->lkp_international_type_id;
                	}else{
                		$booknowAddToCart->lkp_international_type_id = '';
                	}               		
            	}else{
            		$booknowAddToCart->lkp_international_type_id = '';
            	}
	                $booknowAddToCart->seller_post_item_id = $input['postItemId'];
	                $booknowAddToCart->lkp_src_location_type_id = $input['sourceLocationType'];
                        if(isset($input['destinationLocationType'])) 
	                $booknowAddToCart->lkp_dest_location_type_id = $input['destinationLocationType'];
	                if(isset($input['packagingType']))    
	                $booknowAddToCart->lkp_packaging_type_id = $input['packagingType'];
                        
                        if($input['sourceLocationType']=='11')
                        $booknowAddToCart->other_src_location_type = $input['sourceLocationTypeOther'];
                        if(isset($input['destinationLocationType']) && $input['destinationLocationType']=='11')
                        $booknowAddToCart->other_dest_location_type = $input['destinationLocationTypeOther'];
                        if(isset($input['packagingType']) && $input['packagingType']=='13') 
                        $booknowAddToCart->other_packaging_type = $input['packagingTypeOther'];
                        
                        $booknowAddToCart->price = $input['price'];
                        if($serviceId==RELOCATION_GLOBAL_MOBILITY)
                            $booknowAddToCart->buyer_consignment_pick_up_date = CommonComponent::convertDateForDatabase($buyerpost->dispatch_date);
                        else
	                $booknowAddToCart->buyer_consignment_pick_up_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
	                if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
                            $booknowAddToCart->buyer_consignment_value = $input['consignmentValue'];
                            $booknowAddToCart->buyer_consignment_needs_insurance = $input['consignmentNeedInsurance'];
                        }
	                $booknowAddToCart->buyer_consignor_name = $input['consignorName'];
	                $booknowAddToCart->buyer_consignor_mobile = $input['consignorNumber'];
	                $booknowAddToCart->buyer_consignor_email = $input['consignorEmail'];
	                $booknowAddToCart->buyer_consignor_address = $input['consignorAddress'];
	                $booknowAddToCart->buyer_consignor_pincode = $input['consignorPin'];
	                if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
                            $booknowAddToCart->buyer_consignee_name = $input['consigneeName'];
                            $booknowAddToCart->buyer_consignee_mobile = $input['consigneeNumber'];
                            $booknowAddToCart->buyer_consignee_email = $input['consigneeEmail'];
                            $booknowAddToCart->buyer_consignee_pincode = $input['consigneePin'];
                            $booknowAddToCart->buyer_consignee_address = $input['consigneeAddress'];
                            $booknowAddToCart->buyer_additional_details = $input['additionalDetails'];
                        }
//	                $booknowAddToCart->buyer_consignment_value = $input['buyerCounterOfferId'];
	                if($serviceId==RELOCATION_GLOBAL_MOBILITY){
                            $booknowAddToCart->dispatch_date = CommonComponent::convertDateForDatabase($buyerpost->dispatch_date);
                            $booknowAddToCart->delivery_date = '';
                        }else{
                        $booknowAddToCart->dispatch_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
	                $booknowAddToCart->delivery_date = $deliveryDate;
                        }
	                $booknowAddToCart->username = $cartData[0]->username;
	                $booknowAddToCart->from_location = $cartData[0]->from_location;
	                $booknowAddToCart->to_location = $cartData[0]->to_location;
	                $booknowAddToCart->order_dispatch_date = $cartData[0]->dispatch_date;
	                $booknowAddToCart->post_status = $cartData[0]->post_status;
                        $booknowAddToCart->service_name = commonComponent::getGroupName($cartData[0]->lkp_service_id)." ".$cartData[0]->service_name;


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
    public static function cancelEnquiryForFtl($buyerQuoteItemId)
    {
        Log::info('Cancel the quote enquiry for ftl: '.Auth::id(),array('c'=>'2'));
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
            //buyer_quote_items  $booknowAddToCart->buyer_quote_item_id = $input['quoteItemId'];
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
            $arrayBuyerEmailIds = BuyerComponent::getBuyerEnquirySellers($buyerQuoteItemId);
            
            $userDetails = [];
            foreach ($arrayBuyerEmailIds as $buyerDetails) {
                $userDetails[0] = $buyerDetails;
                $userDetails[0]->fromLocation = BuyerComponent::getCityNameFromId($buyerDetails->from_city_id);
                $userDetails[0]->toLocation = BuyerComponent::getCityNameFromId($buyerDetails->to_city_id);
                //$userData = DB::table ( 'users' )->where ( 'id', $this->user_pk )->select ( 'users.*' )->get ();
                CommonComponent::send_email ( CANCEL_ENQUIRY_INFO_MAIL, $userDetails );
            }

            return ['cancelsuccessmessage' => 'Post deleted successfully.'];
            //Save data into txnprojectinviteerequests
        } catch (Exception $e) {

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
            DB::table('seller_post_items')
                        ->where('seller_post_items.id','=',$sellerPostItemId)
                        ->update(array(
                                'lkp_post_status_id'=> $status,
                                'updated_ip'=> $updatedAt,
                                'updated_at'=> $updatedIp,
                                'updated_by'=> Auth::id()
                                ));
            CommonComponent::auditLog($sellerPostItemId,'seller_post_items');
        } catch (Exception $e) {

        }
    }
    
    /**
     * Buyer FTl market Leads
     * srinu started here - 1-04-2016.
     * @param type $sellerPostItemId
     * @param type $status
     * all booknow button hide display none in market leads pages in buyer side -srinu
     */
    public static function getFtlBuyerMarketLeadsList($service_id, $post_status, $enquiry_type){
    
    	$from_locations = array(""=>"From Location");
		$to_locations = array(""=>"To Location");
		$vehicle_types = array(""=>"Vehicle Type");
		$load_types = array(""=>"Load Type");
	
		// query to retrieve seller posts list and bind it to the grid
		$Query = DB::table ( 'seller_posts as sp' );
		$Query->leftjoin ( 'seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'sp.lkp_post_status_id' );
		$Query->leftjoin ( 'seller_selected_buyers as ssb', 'ssb.seller_post_id', '=', 'sp.id' );
		$Query->join ( 'lkp_cities as cf', 'spi.from_location_id', '=', 'cf.id' );
		$Query->join ( 'lkp_cities as ct', 'spi.to_location_id', '=', 'ct.id' );
		$Query->join ( 'users as us', 'sp.seller_id', '=', 'us.id' );
		$Query->where( 'sp.lkp_access_id', 2);
		$Query->where('sp.lkp_post_status_id',2);		
		$Query->where( 'ssb.buyer_id', Auth::User ()->id);
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
		$Query->where('spi.is_private', 0);
		$sellerresults = $Query->select ( 'sp.id', 'sp.from_date',
				'sp.to_date','sp.lkp_access_id','sp.lkp_post_status_id','ps.post_status',
				'us.username','ct.city_name as toCity', 'cf.city_name as fromCity',
				'sp.terms_conditions','sp.tracking','sp.lkp_payment_mode_id','spi.transitdays',
				'spi.units','spi.is_private','spi.id as sellerpostItemId'
		)		
		->groupBy('sp.id')
		->get ();
		//echo "<pre>"; print_r($sellerresults); die;
		//Functionality to handle filters based on the selection starts
		foreach($sellerresults as $seller){
			$seller_post_items  = DB::table('seller_post_items')
			->where('seller_post_items.seller_post_id',$seller->id)
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

		//echo "<pre>"; print_r($sellerresults); die;
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
                        $sellerpostItemId=$row->cells [13]->value;
			
			$seller_post_items  = DB::table('seller_post_items')
							->join('seller_posts','seller_posts.id','=','seller_post_items.seller_post_id')
							->where('seller_post_items.seller_post_id',$spId)
							->select('*','seller_post_items.id as spiid')
							->get();			
			//echo "<pre>"; print_r($seller_post_items); die;						
			$seller_payment_mode_method = CommonComponent::getSellerPostPaymentMethod($paymentMethod);
			$data_link = url()."/buyermarketleads/$spId";			
               $tracking_seller_post = CommonComponent::getTrackingType($tracking);
                        
                        if ($seller_payment_mode_method == 'Advance') {
                                $paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
                        } elseif ($seller_payment_mode_method == 'Credit'){
                                $credit_days = CommonComponent::getCreditdays($sellerpostItemId,'seller_posts','seller_post_items');
                                $paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$seller_payment_mode_method.' | '.$credit_days[0]->credit_period.' '.$credit_days[0]->credit_period_units;
                        }else {
                                $paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$seller_payment_mode_method;
                        }

						
			//$msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$spId);
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
			<div class='col-md-8  '>
			<div class='col-md-3 padding-left-none data-fld'>
			<span class='data-head'>Valid From</span>
			<span class='data-value'>".CommonComponent::checkAndGetDate($fromDate)."</span>
			</div>
			<div class='col-md-3 padding-left-none data-fld'>
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
			
			<div class='clearfix'></div>
			
			<div class='col-md-3 padding-left-none data-fld'>
			<span class='data-head'>Document</span>
			<span class='data-value'>0</span>
			</div>";
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
}
