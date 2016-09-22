<?php

namespace App\Components\TruckLease;

use App\Models\TruckleaseSearchTerm;
use DB;
use App\Models\BuyerQuoteItemView;
use App\Models\BuyerQuoteItems;
use App\Models\CartItem;
use App\Models\BuyerQuoteSellersQuotesPrices;
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
use App\Components\TruckLease\TruckLeaseBuyerComponent;
use Carbon\Carbon;
use DateTime;


class TruckLeaseBuyerComponent {

	
	/**
	 * Buyer Posts List Page
	 * Retrieval of data related to buyer posts list items to populate in the buyer list widget
	 * Displays a grid with a list of all seller posts
	 */
	public static function getLeaseBuyerPostsList($service_id, $post_status, $enquiry_type) {

		// Filters values to populate in the page  
		$from_locations = array (
				"" => "From Location"
		);
		$posted_for_types = array (
				"" => "Posted For"
		);
		$vehicle_types = array (
				"" => "Vehicle Type"
		);
		$lease_terms = array (
				"" => "Lease Term"
		);
		$from_date = '';
		$to_date = '';
		
		// query to retrieve buyer posts list and bind it to the grid
		$Query = DB::table ( 'trucklease_buyer_quote_items as bqi' );
		$Query->join ( 'lkp_vehicle_types as vt', 'vt.id', '=', 'bqi.lkp_vehicle_type_id' );
		$Query->join ( 'lkp_post_statuses as ps', 'ps.id', '=', 'bqi.lkp_post_status_id' );
		$Query->join ( 'lkp_trucklease_lease_terms as lt', 'lt.id', '=', 'bqi.lkp_trucklease_lease_term_id' );
		$Query->join ( 'lkp_cities as cf', 'bqi.from_city_id', '=', 'cf.id' );
		$Query->join ( 'trucklease_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
		$Query->join ( 'lkp_quote_accesses as lqa', 'lqa.id', '=', 'bq.lkp_quote_access_id' );
		$Query->where( 'bqi.created_by', Auth::User ()->id );
		$Query->where('bqi.lkp_post_status_id','!=',6);
		$Query->where('bqi.lkp_post_status_id','!=',7);
		$Query->where('bqi.lkp_post_status_id','!=',8);


		// conditions to make search
		if (isset ( $post_status ) && $post_status != '') {
			if($post_status == 0)
				$Query->whereIn ( 'bqi.lkp_post_status_id', array(1,2,3,4,5));
			else
				$Query->where ( 'bqi.lkp_post_status_id', '=', $post_status );
		}

		if (isset ( $_GET ['dispatch_date'] ) && $_GET ['dispatch_date'] != '') {
			$commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['dispatch_date']);
			$Query->where ( 'bqi.from_date', '>=', $commonDispatchDate );
			$from_date = $commonDispatchDate;
			//echo "From Date :"; echo $from_date;die();
		}
	 	if (isset ( $_GET ['delivery_date'] ) && $_GET ['delivery_date'] != '') {
			$commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['delivery_date']);
			$Query->where ( 'bqi.to_date', '<=', $commonDeliveryhDate);
			$to_date = $commonDeliveryhDate;
			//echo "To Date :"; echo $to_date;die();
		}

		$postResults = $Query->select ( 'bqi.*', 'vt.vehicle_type', 'ps.post_status', 'cf.city_name as fromCity','bq.lkp_quote_access_id','lqa.quote_access','bqi.lkp_trucklease_lease_term_id','lt.lease_term','bq.is_commercial')->get ();
		//echo "<pre>"; print_r($postResults);die();
		// Functionality to handle filters based on the selection starts
		foreach ( $postResults as $post ) {
			$buyer_quotes = DB::table ( 'trucklease_buyer_quote_items' )->leftJoin( 'trucklease_buyer_quotes as bq', 'bq.id', '=', 'trucklease_buyer_quote_items.buyer_quote_id' )->where ( 'trucklease_buyer_quote_items.id', $post->id )->select ( 'trucklease_buyer_quote_items.*','bq.lkp_quote_access_id' )->get ();
				
			foreach ( $buyer_quotes as $quotes ) {
				//echo "<pre>"; print_r($quotes);die();
				if (! isset ( $from_locations [$quotes->from_city_id] )) {
					$from_locations [$quotes->from_city_id] = DB::table ( 'lkp_cities' )->where ( 'id', $quotes->from_city_id )->pluck ( 'city_name' );
				}
				if (! isset ( $vehicle_types [$quotes->lkp_vehicle_type_id] )) {
					$vehicle_types [$quotes->lkp_vehicle_type_id] = DB::table ( 'lkp_vehicle_types' )->where ( 'id', $quotes->lkp_vehicle_type_id )->pluck ( 'vehicle_type' );
				}
				
				if (! isset ( $lease_terms [$quotes->lkp_trucklease_lease_term_id] )) {
					$lease_terms [$quotes->lkp_trucklease_lease_term_id] = DB::table ( 'lkp_trucklease_lease_terms' )->where ( 'id', $quotes->lkp_trucklease_lease_term_id )->pluck ( 'lease_term' );
				}
				
				if (! isset ( $posted_for_types [$quotes->lkp_quote_access_id] )) {
					$posted_for_types [$quotes->lkp_quote_access_id] = DB::table ( 'lkp_quote_accesses' )->where ( 'id', $quotes->lkp_quote_access_id )->pluck ( 'quote_access' );
				}
			}
		}
		
		$from_locations = CommonComponent::orderArray($from_locations);
		$lease_terms = CommonComponent::orderArray($lease_terms);
		$vehicle_types = CommonComponent::orderArray($vehicle_types);
		
		$grid = DataGrid::source ( $Query );

		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'fromCity', 'From', 'fromCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'from_date', 'From Date', 'from_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'to_date', 'To Date', 'to_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'vehicle_type', 'Vehicle Type', 'vehicle_type' )->attributes(array("class" => "col-md-2 padding-right-none"));
		$grid->add ( 'post_status', 'Status', 'post_status' )->attributes(array("class" => "ccol-md-1 padding-left-none"));
		$grid->add ( 'quote_access', 'Posted For', 'quote_access' )->style ( "display:none" );
		$grid->add ( 'created_by', 'dummycolumn', 'created_by' )->style ( "display:none" );
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
			$dispatchDate = $row->cells [2]->value;
			$row->cells [7]->style ( 'width:100%' );
			$buyer_access_id = $row->cells [8]->style ( 'display:none' );
			$buyer_id = $row->cells [9]->style ( 'display:none' );
			$buyerCountId = count (TruckLeaseBuyerComponent::getBuyerQuoteSellersQuotesPricesFromId( $buyer_quote_id ) );
			$post_status_id = $row->cells [10]->style ( 'display:none' );

			$arraySellerIds = BuyerComponent::getSellerIds($row->cells[9]->style ( 'display:none' ));
			$arrayBuyerLeads = BuyerComponent::getLeadsForBuyer($row->cells[11]->style ( 'display:none' ), $arraySellerIds);
			$countview = BuyerComponent::updateBuyerQuoteDetailsViewsTL($buyer_quote_id);
			
			$priceType = $row->cells [12]->style ( 'display:none' );
			if ($priceType == '2') {
				$postQuoteType = 'Response';
			} else {
				$postQuoteType = 'Quotes';
			}
			
			if ($buyer_access_id == "2" && $post_status_id == "2")  {				
				$editOption = "<a href='editbuyerquote/$buyer_id/$buyer_quote_id'><i class='fa fa-edit' title='Edit'></i></a>";
				
			} else {
				$editOption = " ";
				
			}
			
			$matchedSellerPosts = BuyerMatchingComponent::getMatchedResults(ROAD_TRUCK_LEASE,$buyer_quote_id);
			$matchedIds = array();
            foreach($matchedSellerPosts as $matchedSellerPost){
                    $matchedIds[] = $matchedSellerPost->seller_post_id;
            }

            $getSellerLeadData = DB::table('trucklease_seller_post_items as spi');
            $getSellerLeadData->leftjoin('trucklease_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
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
            $delivery_date = $row->cells [3]->value;
            $fromCity = $row->cells [1]->value;
            $vehicle_type = $row->cells [4]->value;
            $status = $row->cells [5]->value;
            $lkp_psot_status_condition = $row->cells   [10]->value;
            $msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyer_quote_id);
             //count for buyer documents
            $serviceId = Session::get('service_id');
            $fromLocationId = $row->cells [11]->value;
            $toLocationId = $row->cells [13]->value;
            $is_commercial = $row->cells [14]->value;
            $docs_buyer    =   CommonComponent::getGsaDocuments(1,$serviceId,$buyer_quote_id,$fromLocationId,$toLocationId,$is_commercial); 
            
			$row->cells [7]->value = "<div class=''><a href='/getbuyercounteroffer/$buyer_quote_id'>
										<div class='col-md-2 padding-left-none'>$fromCity</div>
										<div class='col-md-2 padding-left-none'>
										<span class='lbl padding-8'></span>".CommonComponent::checkAndGetDate($dispatchDate)."
		 								</div>
										<div class='col-md-2 padding-left-none'>".CommonComponent::checkAndGetDate($delivery_date)."</div>
										<div class='col-md-2 padding-right-none'>$vehicle_type</div>
										<div class='col-md-1 padding-none'> $status </div></a>";
										//onclick='buyerpostcancel($buyer_quote_id)'
			if ($lkp_psot_status_condition == OPEN) {
				$row->cells [7]->value .= " <div class='col-md-3 padding-none text-right'>
						$editOption
						<a href='#' data-target='#cancelbuyerpostmodal' data-toggle='modal' onclick='setcancelbuyerpostid(".$buyer_quote_id.")' ><i class='fa fa-trash buyerpostdelete' title='Delete'></i></a>
						<input type='hidden' name='buyercancellationpostid' id='buyercancellationpostid' value=[]>								
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
		$filter->add ( 'bqi.lkp_quote_access_id', 'Posted For', 'select' )->options ( $posted_for_types )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'bqi.lkp_vehicle_type_id', 'Vehicle Type', 'select' )->options ( $vehicle_types )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'bqi.lkp_trucklease_lease_term_id', 'Lease Term', 'select' )->options ( $lease_terms )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
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
    * Get Post Buyer Counter Offer Page
    * Get details of buyer counter offer 
    * @param int $buyerQuoteItemId
    * @return type
    */
    public static function getPostBuyerCounterOfferForTL($buyerQuoteItemId, $comparisonType = null,$priceVal = null,$checkIds=null)
    {
    	
        try {
            Log::info('Get posted buyer counter offer for TL: '.Auth::id(),array('c'=>'2'));
            $roleId = Auth::User()->lkp_role_id;
            if($roleId == BUYER){
    			CommonComponent::activityLog("BUYER_FETCHED_SELLER_POST",
    					BUYER_FETCHED_SELLER_POST,0,
    					HTTP_REFERRER,CURRENT_URL);
    		}
            $countview = TruckLeaseBuyerComponent::updateBuyerQuoteDetailsViews($buyerQuoteItemId);
            $arrayBuyerCounterOffer = TruckLeaseBuyerComponent::getBuyerQuoteDetailsFromId($buyerQuoteItemId);
            
            if(!empty($arrayBuyerCounterOffer)) {
                $privateSellerNames = TruckLeaseBuyerComponent::getPrivateSellerNames($buyerQuoteItemId);
                
                $arrayBuyerQuoteSellersQuotesPrices = TruckLeaseBuyerComponent::getBuyerQuoteSellersQuotesPricesFromId($buyerQuoteItemId, $comparisonType,$priceVal,$checkIds);
                
                $arraySellerIds = TruckLeaseBuyerComponent::getSellerIds($arrayBuyerCounterOffer[0]->buyer_quote_id);
                
                $arrayBuyerLeads = TruckLeaseBuyerComponent::getLeadsForBuyer($arrayBuyerCounterOffer[0]->from_city_id, $arraySellerIds);
               
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
                $dispatchDate = CommonComponent::checkAndGetDate($arrayBuyerCounterOffer[0]->from_date);
                $deliveryDate = CommonComponent::checkAndGetDate($arrayBuyerCounterOffer[0]->to_date);
                $poststatus = CommonComponent::getSellerPostStatuss($arrayBuyerCounterOffer[0]->lkp_post_status_id);
                $price = $arrayBuyerCounterOffer[0]->price;
                $leaseterm = CommonComponent::getSellerLeaseTerm($arrayBuyerCounterOffer[0]->lkp_trucklease_lease_term_id);
                $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Reporting');
                //$destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
                //$packagingType = BuyerComponent::getPackagingType('Destination');
                
                $buyerPostCounterOfferComparisonTypes = config::get('constants.TRUCKLEASE_BUYER_POST_COUNTER_OFFER_COMPARISON_TYPES');
                return [
                            'arrayBuyerCounterOffer' => $arrayBuyerCounterOffer,
                            'privateSellerNames' => $privateSellerNames,
                            'fromLocation' => $fromLocation,
                            'deliveryDate' => $deliveryDate,
                            'dispatchDate' => $dispatchDate,
                            'poststatus' => $poststatus,
                            'leaseterm' =>$leaseterm,
                            'driver_availability'=>$arrayBuyerCounterOffer[0]->driver_availability,
                		    'fuel_included'=>$arrayBuyerCounterOffer[0]->fuel_included,
                            'arrayBuyerQuoteSellersQuotesPrices' => $arrayBuyerQuoteSellersQuotesPrices,
                            'countBuyerLeads' => $countBuyerLeads,
                            'sourceLocation' => $sourceLocationType,
                            //'destinationLocation' => $destinationLocationType,
                            //'packagingType' => $packagingType,
                            'countCartItems' => $countCartItems,
                            'price'=>$price,
                            'vehicle_make_model_year'=>$arrayBuyerCounterOffer[0]->vehicle_make_model_year,
                            'lkp_quote_price_type_id'=>$arrayBuyerCounterOffer[0]->lkp_quote_price_type_id,
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
	 * Truck Lease Market lease details.
         * start
	 * Srinu and date : 4-05-2016
	 * @param
	 * $request
	 * @return Response
	 */

	public static function truckLeaseMarketLeadsDetails($statusId, $roleId, $serviceId, $id){
		try{

			//Filters values to populate in the page
			$from_locations = array(""=>"From Location");
			$to_locations = array(""=>"To Location");
			$vehicle_types = array(""=>"Vehicle Type");
			$load_types = array(""=>"Load Type");
			$Query = DB::table ( 'trucklease_seller_posts as sp' );
			$Query->leftjoin ( 'trucklease_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
			$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'sp.lkp_post_status_id' );
                        $Query->leftjoin ( 'lkp_trucklease_lease_terms as ltls', 'ltls.id', '=', 'spi.lkp_trucklease_lease_term_id' );
			if(Session::get('leads') &&  Session::get('leads')==2){
				Session::put('leads', '2');
				$Query->where('sp.lkp_access_id',1);
			}
			else{
				Session::put('leads', '1');
				$Query->leftjoin ( 'trucklease_seller_selected_buyers as bqss', 'bqss.seller_post_id', '=', 'spi.created_by' );
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
				'sp.to_date', 'sp.transaction_id' ,'spi.lkp_vehicle_type_id','spi.price',
				'sp.lkp_access_id', 'ps.post_status','sp.id as post_id','spi.from_location_id','spi.is_cancelled',
				'sp.created_by','ltls.lease_term as leasetermValue'
			)
			->groupBy('spi.id')
			->get ();
			//Functionality to handle filters based on the selection starts
			foreach($sellerresults as $seller){
				$seller_post_items  = DB::table('trucklease_seller_post_items')
					->where('trucklease_seller_post_items.id',$seller->id)
					->select('*')
					->get();
				foreach($seller_post_items as $seller_post_item){
					if(!isset($from_locations[$seller_post_item->from_location_id])){
						$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
					}
					if(!isset($vehicle_types[$seller_post_item->lkp_vehicle_type_id])){
						$vehicle_types[$seller_post_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $seller_post_item->lkp_vehicle_type_id)->pluck('vehicle_type');
					}

				}
			}
			$from_locations = CommonComponent::orderArray($from_locations);
			$vehicle_types = CommonComponent::orderArray($vehicle_types);
			//Functionality to handle filters based on the selection ends
			//echo $Query->tosql();
			//echo "<pre>";print_R($sellerresults);die;
			$grid = DataGrid::source ( $Query );

			$grid->add ( 'id', 'ID', true )->style ( "display:none" );
			$grid->add ( 'from_location_id', 'From', 'from_location_id' )->attributes(array("class" => "col-md-3 padding-left-none"));
			$grid->add ( 'leasetermValue', '', false )->style ( "display:none" );
			$grid->add ( 'lkp_vehicle_type_id', 'Vehicle Type', 'lkp_vehicle_type_id' )->attributes(array("class" => "col-md-3 padding-left-none"));
			$grid->add ( 'lkp_load_type_id', '', false )->style ( "display:none" );
			$grid->add ( 'price', 'Price', 'price' )->attributes(array("class" => "col-md-3 padding-left-none"));
			$grid->add ( 'post_status', 'Status', '' )->attributes(array("class" => "col-md-3 padding-left-none"));
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
				if($row->cells [7]->value == 1 )
					$row->cells [6]->value = "Deleted";
				else
					$row->cells [6]->value = "Open";
				
				$fromdate = $row->cells [12]->value;
				$todate = $row->cells [13]->value;
				$transaction_id = $row->cells [14]->value;
				$seller_user_id = $row->cells [15]->value;
				$leasetermValue =$row->cells [2]->value;

				//View Count
				$countview = DB::table('trucklease_seller_post_item_views')
					->where('trucklease_seller_post_item_views.seller_post_item_id','=',$spId)
					->select('trucklease_seller_post_item_views.id','trucklease_seller_post_item_views.view_counts')
					->get();
				if(!isset($countview[0]->view_counts))
					$countview = 0;
				else
					$countview = $countview[0]->view_counts;

				$row->cells [1]->value = ''.CommonComponent::getCityName($row->cells [1]->value).'';
				$row->cells [3]->value = ''.CommonComponent::getVehicleType($row->cells [3]->value).'';
				$row->cells [4]->value = ''.CommonComponent::getLoadType($row->cells [4]->value).'';
				$seller_post_items  = DB::table('trucklease_seller_post_items')
					->where('trucklease_seller_post_items.id',$spId)
					->select('*')
					->get();
				
				$row->cells [1]->attributes(array("class" => "col-md-3 padding-left-none "));
				$row->cells [2]->style ( 'display:none' );
				$row->cells [3]->attributes(array("class" => "col-md-3 padding-left-none "));
				$row->cells [4]->style ( 'display:none' );
				$row->cells [5]->attributes(array("class" => "col-md-3 padding-none "));
				$row->cells [6]->attributes(array("class" => "col-md-1 padding-none "));
				$row->cells [7]->attributes(array("class" => "col-md-2 padding-none"));
				$row->cells [10]->style ( 'display:none' );
				$row->cells [11]->style ( 'display:none' );
				$row->cells [12]->style ( 'display:none' );
				$row->cells [13]->style ( 'display:none' );
				$row->cells [14]->style ( 'display:none' );
				$row->cells [15]->style ( 'display:none' );
				
				$serviceId = Session::get ( 'service_id' );
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
                                                                                    <span class='data-head'>Lease Term</span>
                                                                                    <span class='data-value'>$leasetermValue</span>
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
         * Truck lease market leads details page
         * Srinu and date :4-05-2016
         * End
         */
    
    
    
    
    /**
     * Buyer counter offer Page
     * Method to retrieve seller lists
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getLeadsForBuyer($districtId, $sellerIds) {
    	try {
    		Log::info('Get leads for the buyer: ' . Auth::id(), array('c' => '2'));
    		$sellerData = DB::table('trucklease_seller_post_items')
    		->join('users', 'trucklease_seller_post_items.created_by', '=', 'users.id')
    		->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
    		->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
    		->distinct('trucklease_seller_post_items.created_by')
    		->where('trucklease_seller_post_items.lkp_district_id', $districtId)
    		->whereNotIn('sellers.id', $sellerIds)
    		->where('users.lkp_role_id', SELLER)
    		->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
    		->get();
    		//$arrayBuyerQuoteSellersQuotesPrices = $sellerData->get ();
    		return $sellerData;
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
    public static function getSellerIds($buyerQuoteId) {
    	try {
    		Log::info('Get seller lists for the district: ' . Auth::id(), array('c' => '2'));
    		$sellerIds = DB::table('trucklease_buyer_quote_selected_sellers as bqss')
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
     * Method to retrieve buyer quote requests data
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function updateBuyerQuoteDetailsViews($buyerQuoteItemId) {
    	try {
    		Log::info('Get update buyer quote details view: ' . Auth::id(), array('c' => '2'));
    
    		$countview = DB::table('trucklease_buyer_quote_item_views as bqiv')
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
     * Buyer counter offer Page
     * Method to retrieve buyer quote requests data
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getBuyerQuoteDetailsFromId($buyerQuoteItemId) {
    	try {
    		Log::info('Get buyer quote requests data: ' . Auth::id(), array(
    				'c' => '2'
    		));
    		$getPostBuyerCounterOfferQuery = DB::table('trucklease_buyer_quote_items as bqi');
    		$getPostBuyerCounterOfferQuery->leftjoin('trucklease_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');
    		$getPostBuyerCounterOfferQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
            $getPostBuyerCounterOfferQuery->leftjoin('lkp_trucklease_lease_terms as tlt', 'tlt.id', '=', 'bqi.lkp_trucklease_lease_term_id');
    		$getPostBuyerCounterOfferQuery->leftjoin('lkp_quote_price_types as lqpt', 'lqpt.id', '=', 'bqi.lkp_quote_price_type_id');
    		$getPostBuyerCounterOfferQuery->leftjoin('lkp_quote_accesses as lqa', 'lqa.id', '=', 'bq.lkp_quote_access_id');
    		if (!empty($buyerQuoteItemId)) {
    			$getPostBuyerCounterOfferQuery->where('bqi.id', $buyerQuoteItemId);
    		}
    		$getPostBuyerCounterOfferQuery->select('tlt.lease_term','bqi.id', 'bqi.from_date','bqi.price','bqi.lkp_quote_price_type_id','bqi.driver_availability','bqi.vehicle_make_model_year','bqi.lkp_post_status_id','bqi.lkp_trucklease_lease_term_id', 'bqi.is_cancelled', 'bqi.lkp_post_status_id','bqi.to_date', 'bqi.buyer_quote_id','bqi.fuel_included', 'bqi.from_city_id', 'bq.lkp_quote_access_id', 'lqa.quote_access', 'lvt.vehicle_type', 'lqpt.price_type', 'bq.transaction_id', 'bqi.from_date as dispatch_date');
    		$arrayBuyerCounterOffer = $getPostBuyerCounterOfferQuery->get();
    		//echo "<pre>"; print_r($arrayBuyerCounterOffer); die;
    		return $arrayBuyerCounterOffer;
    	} catch (Exception $exc) {
    		// echo $exc->getTraceAsString();
    		// TODO:: Log the error somewhere
    	}
    }
    

   /*
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
    		$getPostBuyerCounterOfferQuery = DB::table('trucklease_buyer_quote_items as bqi');
    		$getPostBuyerCounterOfferQuery->leftjoin('trucklease_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');
    		$getPostBuyerCounterOfferQuery->leftjoin('trucklease_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'bq.id');
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
    
    
    /**
     * Buyer counter offer Page
     * Method to retrieve seller lists
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getBuyerQuoteSellersQuotesPricesFromId($buyerQuoteItemId, $comparisonType = null,$priceVal = null,$checkIds = null) {
    	try {
    		Log::info('Get seller lists for the buyer: ' . Auth::id(), array('c' => '2'));
    		(object)$arrayBuyerQuoteSellersNotQuotesPrices="";
    		$getBuyerQuoteSellersQuotesPricesQuery = DB::table('trucklease_buyer_quote_sellers_quotes_prices as bqsqp');
    		$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('trucklease_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id');
    		$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('trucklease_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
    		$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
    		$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('trucklease_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id');
    		$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
    		$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('trucklease_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');
    		if (!empty($buyerQuoteItemId)) {
    			$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.buyer_quote_item_id', $buyerQuoteItemId);
    		}
    		$getBuyerQuoteSellersQuotesPricesQuery->whereRaw('(`bqsqp`.`initial_quote_price` is not NULL and `bqsqp`.`initial_quote_price` != "" or `bqsqp`.`seller_acceptence` = "1")');
    		$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.buyer_id', Auth::User()->id);
    
    		if (!empty($comparisonType)) {
    			 
    			if($checkIds){
    
    				$checkIds= explode(",",$checkIds);
    				$getBuyerQuoteSellersQuotesPricesQuery->whereIn('bqsqp.id', $checkIds);
    
    				$getBuyerQuoteSellersNotQuotesPricesQuery = DB::table('trucklease_buyer_quote_sellers_quotes_prices as bqsqp');
    				$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('trucklease_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id');
    				$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('trucklease_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
    				$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
    				$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('trucklease_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id');
    				$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
    				$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('trucklease_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');    
    				if (!empty($buyerQuoteItemId)) {
    					$getBuyerQuoteSellersNotQuotesPricesQuery->where('bqsqp.buyer_quote_item_id', $buyerQuoteItemId);
    				}
    				$getBuyerQuoteSellersNotQuotesPricesQuery->whereRaw('(`bqsqp`.`initial_quote_price` is not NULL and `bqsqp`.`initial_quote_price` != "" or `bqsqp`.`seller_acceptence` = "1")');
    				$getBuyerQuoteSellersNotQuotesPricesQuery->where('bqsqp.buyer_id', Auth::User()->id);
    
    				$getBuyerQuoteSellersNotQuotesPricesQuery->whereNotIn('bqsqp.id', $checkIds);
    
    				$getBuyerQuoteSellersNotQuotesPricesQuery->select('bqsqp.private_seller_quote_id','sp.lkp_access_id','sp.transaction_id as transaction_no','spi.minimum_lease_period','spi.lkp_trucklease_lease_term_id','bqsqp.id', 'bqsqp.buyer_id', 'bqsqp.initial_quote_price', 'bqsqp.counter_quote_price', 'bqsqp.final_quote_price', 'u.username', 'bqi.price', 'bqsqp.seller_id', 'bqsqp.buyer_quote_item_id', 'bqsqp.seller_post_item_id', 'bqsqp.firm_price', 'bqsqp.seller_acceptence', 'bq.lkp_quote_access_id', 'spi.seller_post_id', 'sp.from_date', 'sp.to_date', 'lvt.vehicle_type', 'bqi.lkp_post_status_id');
    				if ($comparisonType == '1') {
    					 
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
    				 
    				$getBuyerQuoteSellersQuotesPricesQuery->orderBy($transit, 'asc');
    
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
    				
    				$getBuyerQuoteSellersQuotesPricesQuery->orderBy($price, 'asc');
    			}
    		}
    		$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.private_seller_quote_id','sp.lkp_access_id','sp.transaction_id as transaction_no','spi.minimum_lease_period','spi.lkp_trucklease_lease_term_id','bqsqp.id', 'bqsqp.buyer_id', 'bqsqp.initial_quote_price', 'bqsqp.counter_quote_price', 'bqsqp.final_quote_price', 'u.username', 'bqi.price', 'bqsqp.seller_id', 'bqsqp.buyer_quote_item_id', 'bqsqp.seller_post_item_id', 'bqsqp.firm_price', 'bqsqp.seller_acceptence', 'bq.lkp_quote_access_id', 'spi.seller_post_id', 'sp.from_date', 'sp.to_date', 'lvt.vehicle_type', 'bqi.lkp_post_status_id');
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
    
    		$getSellerLeadData = DB::table('trucklease_seller_post_items as spi');
    		$getSellerLeadData->leftjoin('trucklease_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
    		$getSellerLeadData->leftjoin('users as u', 'u.id', '=', 'sp.seller_id');
    		$getSellerLeadData->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'spi.lkp_vehicle_type_id');
    		$getSellerLeadData->join ( 'lkp_cities as cf', 'spi.from_location_id', '=', 'cf.id' );
    		$getSellerLeadData->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
    		$getSellerLeadData->whereIn('spi.id', $matchedIds);
                $getSellerLeadData->where('spi.is_private', 0);
    		$getSellerLeadData->select('sp.credit_period','sp.credit_period_units','sp.lkp_access_id','sp.transaction_id as transaction_no','spi.*','sp.seller_id','sp.from_date','sp.to_date','lvt.vehicle_type','u.username','cf.city_name as fromcity','sp.lkp_payment_mode_id','pm.payment_mode as paymentmethod','sp.tracking','sp.terms_conditions','sp.cancellation_charge_price','sp.docket_charge_price');
    		$arraySellerLeadsData = $getSellerLeadData->get();
    
    		return $arraySellerLeadsData;
    	} catch (Exception $exc) {
    		// echo $exc->getTraceAsString();
    		// TODO:: Log the error somewhere
    	}
    }
    
    /**
     * Get Buyer market leads
     * @param int $serviceId
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getLeaseBuyerMarketLeadsList(){
    	
    		$from_locations = array(""=>"From Location");
    		$vehicle_types = array(""=>"Vehicle Type");
    		
    		// query to retrieve seller posts list and bind it to the grid
    		$Query = DB::table ( 'trucklease_seller_posts as sp' );
    		$Query->leftjoin ( 'trucklease_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
    		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'sp.lkp_post_status_id' );
    		$Query->leftjoin ( 'trucklease_seller_selected_buyers as ssb', 'ssb.seller_post_id', '=', 'sp.id' );
    		$Query->join ( 'lkp_cities as cf', 'spi.from_location_id', '=', 'cf.id' );
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
    				'us.username','cf.city_name as fromCity','sp.terms_conditions','sp.tracking','sp.lkp_payment_mode_id','spi.id as sellerpostItemId'
    		)
    		->groupBy('sp.id')
    		->get ();
    		//Functionality to handle filters based on the selection starts
    		foreach($sellerresults as $seller){
    			$seller_post_items  = DB::table('trucklease_seller_post_items')
    			->where('trucklease_seller_post_items.seller_post_id',$seller->id)
    			->select('*')
    			->get();
    			foreach($seller_post_items as $seller_post_item){
    				if(!isset($from_locations[$seller_post_item->from_location_id])){
    					$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
    				}
    				
    				if(!isset($vehicle_types[$seller_post_item->lkp_vehicle_type_id])){
    					$vehicle_types[$seller_post_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $seller_post_item->lkp_vehicle_type_id)->pluck('vehicle_type');
    				}
    			}
    		}
    		//Functionality to handle filters based on the selection ends
    		$grid = DataGrid::source ( $Query );
    		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
    		$grid->add ( 'username', 'Seller Name', 'username' )->attributes(array("class" => "col-md-2 padding-left-none"));
    		$grid->add ( 'fromCity', 'From Location', 'fromCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
    		$grid->add ( 'from_date', 'Valid From', 'post_status' )->attributes(array("class" => "col-md-2 padding-left-none"));
    		$grid->add ( 'to_date', 'Valid To', 'post_status' )->attributes(array("class" => "col-md-2 padding-left-none"));
    		$grid->add ( 'post_status', 'Status', 'post_status' )->attributes(array("class" => "col-md-1 padding-left-none"));
    		$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
    		$grid->add ( 'tracking', 'Tracking', 'tracking' )->style ( "display:none" );
    		$grid->add ( 'terms_conditions', 'Tracking', 'terms_conditions' )->style ( "display:none" );
    		$grid->add ( 'lkp_payment_mode_id', 'Payment Method', 'lkp_payment_mode_id' )->style ( "display:none" );
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
    				
    			$spId = $row->cells [0]->value;
    			$sellerName=$row->cells [1]->value;
    			$fromLocation=$row->cells [2]->value;
    			$fromDate=$row->cells [3]->value;
    			$toDate=$row->cells [4]->value;
    			$postStatus=$row->cells [5]->value;
    			$tracking=$row->cells [7]->value;
    			$termandconditions=$row->cells [8]->value;
    			$paymentMethod=$row->cells [9]->value;
                        $sellerpostItemId=$row->cells [10]->value;
    			
    				
    			$seller_post_items  = DB::table('trucklease_seller_post_items')
    			->join('trucklease_seller_posts','trucklease_seller_posts.id','=','trucklease_seller_post_items.seller_post_id')
    			->where('trucklease_seller_post_items.seller_post_id',$spId)
    			->select('*','trucklease_seller_post_items.id as spiid')
    			->get();
    			//echo "<pre>"; print_r($seller_post_items); die;
    			$seller_payment_mode_method = CommonComponent::getSellerPostPaymentMethod($paymentMethod);
    			$data_link = url()."/buyermarketleads/$spId";
    			
               $tracking_seller_post = CommonComponent::getTrackingType($tracking);
    			
                        if ($seller_payment_mode_method == 'Advance') {
                                $paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
                        } elseif ($seller_payment_mode_method == 'Credit'){
                                $credit_days = CommonComponent::getCreditdays($sellerpostItemId,'trucklease_seller_posts','trucklease_seller_post_items');
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
    			$filter->add ( 'spi.lkp_vehicle_type_id', '', 'select' )->options ( $vehicle_types )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
    			$filter->submit ( 'search' );
    			$filter->reset ( 'reset' );
    			$filter->build ();
    				
    			$result = array ();
    			$result ['grid'] = $grid;
    			$result ['filter'] = $filter;
    			return $result;
    	
    	}

	// buyer search for seller posts result component
	public static function getBuyerSearchList($request, $serviceId) 
	{
		try 
		{
			$request->is_dispatch_flexible = $request->exists('dispatch_flexible_hidden')? $request->dispatch_flexible_hidden:0;

			$request->is_delivery_flexible = $request->exists('delivery_flexible_hidden')? $request->delivery_flexible_hidden:0;

			// query to retrieve seller posts list and bind it to the grid--for filters
			$from_locations = ["" => "From Location"];
			$lease_terms 	= ["" => "Lease Term"];
			$vehicle_types 	= ["" => "Vehicle Type"];
			$sellerNames 	= [];
			$paymentMethods = [];
			$prices = [];

			$trackingfilter = [];     
            if($request->has('tracking'))
                $trackingfilter[] = $request->tracking;
                
            if($request->has('tracking1'))
            	$trackingfilter[] = $request->tracking1;
           	
           	if(count($trackingfilter)>0)
            	$request->merge(['trackingfilter' => $trackingfilter]);

			if($request->exists('filter_set') && $request->filter_set == 1) {
				
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
				
				if( $request->has('lkp_vehicle_type_id') && 
					$request->has('lkp_trucklease_lease_term_id') && 
					$request->has('from_location_id') && $request->has('from_date') && 
					$request->has('from_location') && $request->has('is_commercial'))
				{
					$sellerpost_for_buyers  =  new TruckleaseSearchTerm();
					$sellerpost_for_buyers->user_id = Auth::id();
					$sellerpost_for_buyers->from_city_id 	= $request->from_location_id;
					$sellerpost_for_buyers->from_date 		= $request->from_date;
					$sellerpost_for_buyers->to_date = $request->to_date;
					$sellerpost_for_buyers->lkp_trucklease_lease_term_id = $request->lkp_trucklease_lease_term_id;
					$sellerpost_for_buyers->lkp_vehicle_type_id = $request->lkp_vehicle_type_id;
					$sellerpost_for_buyers->with_driver = $request->driver_availability;
					$sellerpost_for_buyers->created_at = date('Y-m-d H:i:s');
					$sellerpost_for_buyers->created_ip = $_SERVER['REMOTE_ADDR'];
					$sellerpost_for_buyers->created_by = Auth::id();
					$sellerpost_for_buyers->save();

					// Storing Request Data to Session
					session()->put([
						'searchMod' => [
							'delivery_date_buyer'	=> $request->to_date,
							'dispatch_date_buyer'	=> $request->from_date,
							'vehicle_type_buyer'	=> $request->lkp_vehicle_type_id,
							'lease_term_buyer'		=> $request->lkp_trucklease_lease_term_id,
							'from_city_id_buyer'	=> $request->from_location_id,
							'from_location_buyer'	=> $request->from_location,
							'driver_availability'	=> $request->driver_availability,
	                    	'is_commercial_date_buyer'=> $request->is_commercial,
						]
					]);
				}
			}

			if (empty($Query_buyers_for_sellers_filter )) {
				CommonComponent::searchTermsSendMail ();
				session()->put('layered_filter_to_location', '');
				session()->put('layered_filter_from_location', '');
				session()->put('layered_filter_payments', '');
				session()->put('show_layered_filter','');
			}

			// Below script for filter data getting from queries --for filters
			foreach ( $Query_buyers_for_sellers_filter as $seller_post_item ) {
				
				session()->put('show_layered_filter', 1);
				
				if(!isset ( $from_locations [$seller_post_item->from_location_id] )):
					$from_locations [$seller_post_item->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $seller_post_item->from_location_id )->pluck ( 'city_name' );
				endif;

				if(! isset ( $load_types [$seller_post_item->lkp_trucklease_lease_term_id] )):
					$lease_terms [$seller_post_item->lkp_trucklease_lease_term_id] = DB::table ( 'lkp_load_types' )->where ( 'id', $seller_post_item->lkp_trucklease_lease_term_id )->pluck ( 'load_type' );
				endif;

				if(! isset ( $vehicle_types [$seller_post_item->lkp_vehicle_type_id] )):
					$vehicle_types [$seller_post_item->lkp_vehicle_type_id] = DB::table ( 'lkp_vehicle_types' )->where ( 'id', $seller_post_item->lkp_vehicle_type_id )->pluck ( 'vehicle_type' );
				endif;

				if($request->exists('is_search')):
					if(!isset( $sellerNames [$seller_post_item->seller_id] )) {
						$sellerNames[$seller_post_item->seller_id] = $seller_post_item->username;
					}
					if(!isset( $paymentMethods [$seller_post_item->lkp_payment_mode_id] )) {
						$paymentMethods[$seller_post_item->lkp_payment_mode_id] = $seller_post_item->paymentmethod;
					}
					session()->put('layered_filter', $sellerNames);
					session()->put('layered_filter_payments', $paymentMethods);
				endif;
			}

			
			$result = $Query_buyers_for_sellers->get ();
			
			$to_date = CommonComponent::convertDateForDatabase(
				session('searchMod.delivery_date_buyer')
			);

			$from_date = CommonComponent::convertDateForDatabase(
				session('searchMod.dispatch_date_buyer')
			);

			$date1 = new DateTime($from_date);
			$date2 = new DateTime($to_date);
			$diff = $date1->diff($date2);
			
			$Query_buyers_for_sellersnew = array();
			foreach($result as $Query_buyers_for_seller){
				$leasPrice=0;
				if(session('searchMod.lease_term_buyer')==1){
					$leasPrice=$Query_buyers_for_seller->price*ceil($diff->days+1);
				}
				if(session('searchMod.lease_term_buyer')==2){
					$weeks=$diff->days/7;
					$leasPrice=$Query_buyers_for_seller->price*ceil($weeks);
				}
				if(session('searchMod.lease_term_buyer')==3){
					if($diff->d > 0){
					$monhs=$diff->m+1;	
					}else{
					$monhs=$diff->m;
					}

					$leasPrice=$Query_buyers_for_seller->price*$monhs;
				}
				if(session('searchMod.lease_term_buyer')==4){
					if($diff->d > 0){
						$years=$diff->y+1;
					}else{
						$years=$diff->y;
					}
					$leasPrice=$Query_buyers_for_seller->price*$years;
				}
				
				$Query_buyers_for_seller->newprice = isset($leasPrice) ? $leasPrice : 0;
				$prices[] = $Query_buyers_for_seller->newprice;
				$Query_buyers_for_sellersnew[] = $Query_buyers_for_seller;
			}
			//dd($Query_buyers_for_sellersnew);exit;
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

			if(isset($_REQUEST['price_from']) && isset($_REQUEST['price_to'])){
                $pricefrom = $_REQUEST['price_from'];
                $priceto = $_REQUEST['price_to'];
                foreach($Query_buyers_for_sellersnew as $key => $Query_buyers_for_sellersnewrow){
                    if($Query_buyers_for_sellersnewrow->newprice >= $pricefrom && $Query_buyers_for_sellersnewrow->newprice <= $priceto){

                    }else{
                        unset($Query_buyers_for_sellersnew[$key]);
                    }
                }
                
            }
            $Query_buyers_for_sellers = $Query_buyers_for_sellersnew;
			$gridBuyer = DataGrid::source ( $Query_buyers_for_sellers );
			$gridBuyer->add ( 'id', 'ID', true )->style ( "display:none" );
			$gridBuyer->add ( 'username', 'Seller Name', true )->attributes(array("class" => "col-md-6 padding-left-none"));
			$gridBuyer->add ( 'status', 'Vendor Rating', false )->attributes(array("class" => ""))->style ( "display:none" );
			$gridBuyer->add ( 'price1', 'Price1', true )->style ( "display:none" );
			$gridBuyer->add ( 'test', 'Status', true )->style ( "display:none" );
			$gridBuyer->add ( 'fromcity', 'From city', true )->style ( "display:none" );
			$gridBuyer->add ( 'tocity', 'From city', true )->style ( "display:none" );
			$gridBuyer->add ( 'lease_term', 'Lease Term', true )->style ( "display:none" );
			$gridBuyer->add ( 'vehicle_type', 'Vehicle Type', true )->style ( "display:none" );
			$gridBuyer->add ( 'from_date', 'Valid From', true )->style ( "display:none" );
			$gridBuyer->add ( 'to_date', 'Valid To', true )->style ( "display:none" );
			$gridBuyer->add ( 'tracking', 'Tracking', true )->style ( "display:none" );
			$gridBuyer->add ( 'paymentmethod', 'Payment Mode', true )->style ( "display:none" );
			$gridBuyer->add ( 'created_by', 'created by', 'created_by' )->style ( "display:none" );
			$gridBuyer->add ( 'price', 'Price', true )->attributes(array("class" => "col-md-6 padding-left-none"));
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
			$gridBuyer->add ( 'minimum_lease_period', 'minimum_lease_period', 'minimum_lease_period' )->style('display:none');
			$gridBuyer->add ( 'vehicle_make_model_year', 'vehicle_make_model_year', 'vehicle_make_model_year' )->style('display:none');
			$gridBuyer->add ( 'fuel_included', 'fuel_included', 'fuel_included' )->style('display:none');
			$gridBuyer->add ( 'driver_availability', 'driver_availability', 'driver_availability' )->style('display:none');
			$gridBuyer->add ( 'lkp_trucklease_lease_term_id', 'lkp_trucklease_lease_term_id', 'lkp_trucklease_lease_term_id' )->style('display:none');
			$gridBuyer->add ( 'driver_charges', 'driver_charges', 'driver_charges' )->style('display:none');

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
				$row->cells [30]->style ( 'display:none' );
				$row->cells [31]->style ( 'display:none' );
				$row->cells [32]->style ( 'display:none' );
				$row->cells [33]->style ( 'display:none' );

				//number of loads calculation client new doc issue(11-03-2016)
				$vehicle_type_id = $row->cells [27]->value;
				$noofloads  = CommonComponent::ftlNoofLoads($vehicle_type_id);

				$id = $row->cells [0]->value;
				$vendorname = $row->cells [1]->value;
				$price = $row->cells [14]->value;
				$fromlocation = $row->cells [5]->value;
				$tolocation = $row->cells [6]->value;
				$lease_term = $row->cells [7]->value;
				$vehicletype = $row->cells [8]->value;
				$validfrom = $row->cells [9]->value;
				$validto = $row->cells [10]->value;
				$tracking = $row->cells [11]->value;
				$paymentmode = $row->cells [12]->value;
				$seller_id = $row->cells [13]->value;
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
				
				
				$leasPrice=$row->data->newprice;

	            // Required conditions
	            
				$dispalyCharges = $leasPrice;
                $minimumLeasePeriod=$row->cells[28]->value;
                $vehicleModelYear=$row->cells[29]->value;
                $fuelIncluded=$row->cells[30]->value;
                $driverAvail=$row->cells[31]->value;
                $leaseTermId=$row->cells[32]->value;
                $drivercost=$row->cells[33]->value;
                
				Session::put('session_lease_term_search',$lease_term);
				Session::put('session_vehicle_type_search',$vehicletype);
				Session::put('session_lease_price',$dispalyCharges);

				$tracking_text = CommonComponent::getTrackingType($tracking);
				$track_type = '<i class="fa fa-signal"></i>&nbsp;'.$tracking_text;


				if ($paymentmode == 'Advance') {
					$paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
				}elseif ($paymentmode == 'Credit'){
					$credit_days = CommonComponent::getCreditdays($id,'trucklease_seller_posts','trucklease_seller_post_items');
					
					$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode.' | '.$credit_days[0]->credit_period.' '.$credit_days[0]->credit_period_units;
				} else {
					$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode;
				}
                                
                                if ($fuelIncluded == 1) {
					$fuelInc = 'Included';
				} else {
					$fuelInc = 'Not Included';
				}
                                
                if ($driverAvail == 1) {
					$driverAvl = 'With Driver';
				} else {
					$driverAvl = 'Without Driver';
				}
                                
                   if ($leaseTermId == 1) {
					$leaseTermType = 'Days';
				} elseif($leaseTermId == 2) {
					$leaseTermType = 'Weeks';
				} elseif($leaseTermId == 3) {
					$leaseTermType = 'Months';
				} elseif($leaseTermId == 4) {
					$leaseTermType = 'Years';
				}

				$url = url().'/buyerbooknowforsearch/'.$row->cells [0];
				$row->cells [4]->value = "<div class='col-md-3 padding-left-none'>$vendorname
					<div class='red'>
					<i class='fa fa-star'></i>
					<i class='fa fa-star'></i>
					<i class='fa fa-star'></i>
					</div>

				</div>
				
				<div class='col-md-3 padding-left-none'></div>
				<div class='col-md-6 padding-left-none'> $dispalyCharges/-
                    <input type='button' class='btn red-btn pull-right buyer_book_now' data-url='$url'
                       data-buyerpostofferid='$id' data-booknow_list='$id' value='Book Now' />
				</div>

				<div class='clearfix'></div>
				<div class='pull-left'>
					<div class='info-links'>&nbsp;
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
							<i class='fa fa-map-marker'></i> $fromlocation
							<span class='close-icon'>x</span>
						</h3>
					</div>
					<div class='col-md-8 data-div'>
						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>From Date</span>
							<span class='data-value'>".CommonComponent::checkAndGetDate($validfrom)."</span>
						</div>
						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>To Date</span>
							<span class='data-value'>".CommonComponent::checkAndGetDate($validto)."</span>
						</div>
                                                <div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Lease Term</span>
							<span class='data-value'>$lease_term</span>
						</div>
						

						<div class='clearfix'></div>

						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Minimum Lease Term</span>
							<span class='data-value'>$minimumLeasePeriod $leaseTermType</span>
						</div>
						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Fuel</span>
							<span class='data-value'>$fuelInc</span>
						</div>
						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Driver</span>
							<span class='data-value'>$driverAvl</span>
						</div>
						<div class='clearfix'></div>
                                                
                        <div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Payment</span>
							<span class='data-value'>$paymentType</span>
						</div>
						
						<div class='col-md-4 padding-left-none data-fld'>
							<span class='data-head'>Documents</span>
							<span class='data-value'>0</span>
						</div>";
						if($driverAvail == 1){
							$row->cells [4]->value .="
							<div class='col-md-4 padding-left-none data-fld'>
								<span class='data-head'>Driver Cost</span>
								<span class='data-value'>$drivercost</span>
							</div>";
						}
						$row->cells [4]->value .="<div class='clearfix'></div>

						<div class='col-md-12 padding-left-none data-fld'>
							<span class='data-head'>Vehicle Make & Model & Year</span>
							<span class='data-value'>$vehicleModelYear</span>
						</div>";
						$row->cells [4]->value .="<div class='clearfix'></div>
						
						<div class='col-md-12 padding-left-none data-fld'>
						<span class='data-head'>Permit</span>
						<span class='data-value'>".rtrim(CommonComponent::checkPermit($id),', ')."</span>
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
						<input id='buyersearch_booknow_seller_price_$id' type='hidden' value=".$dispalyCharges." name='buyersearch_booknow_seller_price_$id'>
						<input id='buyersearch_booknow_from_date_$id' type='hidden' value=".$validfrom.">
						<input id='buyersearch_booknow_to_date_$id' type='hidden' value=".$validto.">
						<input id='buyersearch_booknow_dispatch_date_$id' type='hidden' value='".CommonComponent::convertDateForDatabase(session('searchMod.dispatch_date_buyer'))."'>
						<input id='buyersearch_booknow_delivery_date_$id' type='hidden' value='".CommonComponent::convertDateForDatabase(session('searchMod.delivery_date_buyer'))."'>
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
    * get buyer counter offer page
    * Insert values for booknow
    * @param Request $request
    * @return type
    */
    public static function setBuyerBooknowForTl($input)
    {
    	Log::info('Insert the buyer booknow data for Tl: '.Auth::id(),array('c'=>'2'));
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
           
                $postPaymentMethods = DB::table('trucklease_seller_posts as sp')
                                    ->leftjoin('trucklease_seller_post_items as spi','spi.seller_post_id','=','sp.id')
                                    ->leftjoin ( 'trucklease_buyer_quote_sellers_quotes_prices as bqsp', 'bqsp.seller_post_item_id', '=', 'spi.id' )
                                    ->where('spi.id',$input['postItemId'])
                                    ->select('spi.id','sp.lkp_payment_mode_id')
                                    ->get();
                
                $deliveryDate = $input['sellerPostedToDate'];
            
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
                $booknowAddToCart->delivery_date = $deliveryDate;

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
                    
                    $cartData =  DB::select( DB::raw("SELECT
			        q.*,
			        u.username,
			        q.price,
			        pz1.city_name as from_location,
			        service.service_name,
			        bq1.from_date as dispatch_date,
			        bq1.lkp_post_status_id as post_status
			        FROM
			        cart_items q
			        LEFT JOIN users u on u.id = q.seller_id
			        LEFT JOIN lkp_services service on service.id = q.lkp_service_id
			        LEFT JOIN trucklease_seller_post_items psi1 on q.seller_post_item_id = psi1.id and q.lkp_service_id = 5 
			        LEFT JOIN trucklease_seller_posts ps1 on psi1.seller_post_id = ps1.id and q.lkp_service_id = 5
			        LEFT JOIN trucklease_buyer_quote_items bq1 on bq1.id = q.buyer_quote_item_id and q.lkp_service_id = 5
			        LEFT JOIN lkp_cities pz1
			              ON (psi1.from_location_id = pz1.id and q.lkp_service_id = 5)
			              
			        where q.id ='".$cartInsertId."'"));
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
                    $booknowAddToCart->delivery_date = $deliveryDate;
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
    * Change status of seller post item
    * @param type $sellerPostItemId
    * @param type $status
    */
    public static function changeStatusForSellerPostItem($sellerPostItemId, $status)
    {
        try{
            $updatedAt = date ( 'Y-m-d H:i:s' );
            $updatedIp = $_SERVER['REMOTE_ADDR'];
            DB::table('trucklease_seller_post_items')
                        ->where('trucklease_seller_post_items.id','=',$sellerPostItemId)
                        ->update(array(
                                'lkp_post_status_id'=> $status,
                                'updated_ip'=> $updatedAt,
                                'updated_at'=> $updatedIp,
                                'updated_by'=> Auth::id()
                                ));
            CommonComponent::auditLog($sellerPostItemId,'trucklease_seller_post_items');
        } catch (Exception $e) {

        }
    }
        
        
}
