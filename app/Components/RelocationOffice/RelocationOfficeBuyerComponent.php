<?php

namespace App\Components\RelocationOffice;

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
class RelocationOfficeBuyerComponent {
	
	
	public static function getRelocationBuyerPostsList($service_id, $post_status, $enquiry_type) {
	
		
		// Filters values to populate in the page
		$from_locations = array (
				"" => "City"
		);
		
		
		$from_date = '';
		$to_date = '';
		$order_no = '';
	
		// query to retrieve buyer posts list and bind it to the grid
		$Query = DB::table ( 'relocationoffice_buyer_posts as rbs' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rbs.lkp_post_status_id' );
		$Query->leftjoin ( 'lkp_quote_accesses as qa', 'qa.id', '=', 'rbs.lkp_quote_access_id' );
		$Query->leftjoin ( 'lkp_cities as cf', 'rbs.from_location_id', '=', 'cf.id' );
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
	
		$postResults = $Query->select ( 'rbs.*', 'ps.post_status', 'cf.city_name as fromCity','qa.quote_access')->get ();
		//echo "<pre>"; print_r($postResults);die();
		// Functionality to handle filters based on the selection starts
		foreach ( $postResults as $post ) {
			
				if (! isset ( $from_locations [$post->from_location_id] )) {
					$from_locations [$post->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $post->from_location_id)->pluck ( 'city_name' );
				}
				
		}

		// filters Order By from locations
		$from_locations = CommonComponent::orderArray($from_locations);
	
		$grid = DataGrid::source ( $Query );
	
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'dispatch_date', 'Pickup Date', 'dispatch_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'delivery_date', 'To Date', 'delivery_date' )->style ( "display:none" );
		$grid->add ( 'fromCity', 'City', 'fromCity' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'distance', 'Distance', 'distance' )->attributes(array("class" => "col-md-2 padding-left-none"));		
		$grid->add ( 'quote_access', 'Post Type','quote_access')->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'post_status', 'Status', 'post_status' )->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'dummy', '', 'show' )->style("display:none");
		$grid->add ( 'lkp_quote_access_id', 'Buyer access_id', 'lkp_quote_access_id' )->style ( "display:none" );
		$grid->add ( 'buyer_post_id', 'Buyer id', 'buyer_post_id' )->style ( "display:none" );
		
		$grid->orderBy ( 'rbs.id', 'desc' );
		$grid->paginate ( 5 );
	
		$grid->row ( function ($row) {
			
			$buyer_post_id = $row->cells[0]->value;
			 $row->cells[4]->value =  $row->cells[4]->value." KM";
			$data_link = url()."/getbuyercounteroffer/$buyer_post_id";
			$row->cells [0]->style ( 'display:none' );
			$row->cells [1]->attributes(array("class" => "html_link col-md-3 padding-left-none","data_link"=>$data_link));
			//$row->cells [2]->attributes(array("class" => "html_link col-md-3 padding-left-none","data_link"=>$data_link));
			$row->cells [2]->style ( 'display:none' );
			$row->cells [3]->attributes(array("class" => "html_link col-md-3 padding-left-none","data_link"=>$data_link));
			$row->cells [4]->attributes(array("class" => "html_link col-md-2 padding-left-none","data_link"=>$data_link));
			$row->cells [5]->attributes(array("class" => "html_link col-md-2 padding-left-none","data_link"=>$data_link));
			$row->cells [6]->attributes(array("class" => "html_link col-md-1 padding-left-none","data_link"=>$data_link));
			$row->cells [7]->attributes(array("class" => "html_link","data_link"=>$data_link));
			$row->cells [8]->style("display:none");
			$row->cells [9]->style("display:none");
			$row->cells [1]->value=CommonComponent::checkAndGetDate($row->cells [1]->value);
			$row->cells [2]->value=CommonComponent::checkAndGetDate($row->cells [2]->value);
			
			
			$dispatchDate = $row->cells [1]->value;
			$fromCity = $row->cells [2]->value;
	               
	        $countview = PtlBuyerGetQuoteBooknowComponent::updateBuyerQuoteDetailsViews($buyer_post_id,'relocationoffice_buyer_post_views');
	        $msg_count  = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyer_post_id);
	        $quotescount = RelocationOfficeBuyerComponent::getQuotesCount($buyer_post_id);
	        if ( $row->cells [6]->value == 'Open') {
	        	$row->cells [7]->value .= "<div class='col-md-1 padding-left-none text-right'>
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
	        <div class='info-links'>
	        <a><span class='views red'><i class='fa fa-eye' title='Views'></i> $countview </span></a>
	        </div>
	        </div>";
	        
	        
	     
	
		} );
	
		// Functionality to build filters in the page starts
		$filter = DataFilter::source ( $Query );
		$filter->add ( 'rbs.from_location_id', '', 'select' )->options ( $from_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
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
	public static function getRelocationOfficeBuyerSearchResults($request, $serviceId) {
		try {
			$volumeCFT = $request['volume'];
			$prices = array();

			$Query_buyers_for_sellers = BuyerSearchComponent::search ($roleId=null,$serviceId,$statusId=null, $request );
			$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();	
			//Session::put('relocbuyerrequest', $request->all());
			if(isset($_REQUEST['from_location']) && $_REQUEST['from_location'] && isset($_REQUEST['from_date']) && $_REQUEST['from_date'] )
			{					
				session()->put([
					'searchMod' => [
						'dispatch_date_buyer'		=> $request->from_date,
						'delivery_date_buyer'		=> $request->to_date,
						'from_city_id_buyer'		=> $request->from_location_id,
						'from_location_buyer'		=> $request->from_location,
						'volume_buyer'				=> $request->volume,
						'distance_buyer'			=> $request->distance,
						'particulars_buyer'			=> $request->roomitems,
					]
				]);    
			}			
			//Save Data in sessions			
			if (empty ( $Query_buyers_for_sellers_filter ) && !isset($_REQUEST['filter_set'])) {
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
				$result[$key]->price= ($_REQUEST['volume']*$result[$key]->rate_per_cft) + ($_REQUEST['distance']*$result[$key]->transport_price);
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


			
			//echo "<pre>";print_R($_REQUEST);print_R($result);die;
			$gridBuyer = DataGrid::source ( $result );
			$gridBuyer->add ( 'id', 'ID', true )->style ( "display:none" );
			$gridBuyer->add ( 'username', 'Name', false )->attributes(array("class" => "col-md-3 padding-left-none"));
			$gridBuyer->add ( 'volume', 'Total CFT', false )->attributes(array("class" => "col-md-2 padding-left-none"));
			$gridBuyer->add ( 'rate_per_cft', 'Od Charges', false )->style ( "display:none" );
			$gridBuyer->add ( 'transport_charges', 'Transport Charges', false )->style ( "display:none" );
			$gridBuyer->add ( 'brokerage', 'Brokerage', false )->style ( "display:none" );
			$gridBuyer->add ( 'tracking', 'Tracking', false )->style ( "display:none" );
			$gridBuyer->add ( 'payment_mode', 'Payment mode', false )->style ( "display:none" );
			$gridBuyer->add ( 'transaction_id', 'Transaction Id',false )->style('display:none');
			$gridBuyer->add ( 'created_by', 'created by', 'created_by' )->style ( "display:none" );	
			$gridBuyer->add ( 'from_date', 'from_date', 'from_date' )->style ( "display:none" );
			$gridBuyer->add ( 'to_date', 'to_date', 'to_date' )->style ( "display:none" );
			$gridBuyer->add ( 'price', 'Total Price',false )->attributes(array("class" => "col-md-3 padding-left-none"));                        
            $gridBuyer->add ( 'cancellation_charge_price', 'cancellation_charge_price', 'cancellation_charge_price' )->style ( "display:none" );   
            $gridBuyer->add ( 'docket_charge_price', 'docket_charge_price', 'docket_charge_price' )->style ( "display:none" );   
            $gridBuyer->add ( 'transport_price', 'transport_price', 'transport_price' )->style ( "display:none" );      
            $gridBuyer->add ( 'other_charge1_text', 'other_charge1_text', 'other_charge1_text' )->style ( "display:none" );      
            $gridBuyer->add ( 'other_charge1_price', 'other_charge1_price', 'other_charge1_price' )->style ( "display:none" );
            $gridBuyer->add ( 'other_charge2_text', 'other_charge2_text', 'other_charge2_text' )->style ( "display:none" );      
            $gridBuyer->add ( 'other_charge2_price', 'other_charge2_price', 'other_charge2_price' )->style ( "display:none" );
            $gridBuyer->add ( 'other_charge3_text', 'other_charge3_text', 'other_charge3_text' )->style ( "display:none" );      
            $gridBuyer->add ( 'other_charge3_price', 'other_charge3_price', 'other_charge3_price' )->style ( "display:none" );   
            $gridBuyer->add ( 'terms_conditions', 'terms_conditions', 'terms_conditions' )->style ( "display:none" );      
               
                        
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
				$row->cells [21]->style ( 'display:none' );
				$row->cells [22]->style ( 'display:none' );

				$id = $row->cells [0]->value;
				$sellername = $row->cells [1]->value;
				if(isset($_REQUEST['total_hidden_volume']) && !empty($_REQUEST['total_hidden_volume'])){
					$volume = $_REQUEST['total_hidden_volume'];
				}elseif(isset($_REQUEST['volume']) && !empty($_REQUEST['volume'])){
					$volume = $_REQUEST['volume'];
				}else{
					$volume = $row->cells [2]->value;
				}

				$price = ($volume*$row->cells[3]->value) + ($_REQUEST['distance']*$row->cells[15]->value);

				$odcharges = $row->cells [3]->value;				
				$tracking = $row->cells [6]->value;
				$paymentmode = $row->cells [7]->value;
                $transaction_id=$row->cells[8]->value;
                $seller_id = $row->cells [9]->value;
                $validfrom=$row->cells[10]->value;
                $validto = $row->cells [11]->value;
                $cancelCharges = $row->cells [13]->value;
                $docketCharges = $row->cells [14]->value;
				$transportcharges = $row->cells[3]->value;
                $othertext1 = $row->cells [16]->value;
                $otherprice1 = $row->cells [17]->value;
                $othertext2 = $row->cells [18]->value;
                $otherprice2 = $row->cells [19]->value;
                $othertext3 = $row->cells [20]->value;
                $otherprice3 = $row->cells [21]->value;
                $termandcond = $row->cells [22]->value;

				$tracking_text = CommonComponent::getTrackingType($tracking);
				$track_type = '<i class="fa fa-signal"></i>&nbsp;'.$tracking_text;
				
				if ($paymentmode == 'Advance') {
					$paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
				} else {
					$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode;
				}
				
                                
				CommonComponent::viewCountForSeller(Auth::User()->id,$id,'relocation_seller_post_views');
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
                                
                                
				$row->cells [5]->value.="<div class='col-md-2 padding-left-none'>$volume</div>
										<div class='col-md-2 padding-left-none' id='totalestimatecharges_$id' >$price /-</div>
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
										<div class='pull-right text-right'>
											<div class='info-links'>
												<a class='viewcount_show-data-link' data-quoteId='$id' id='$id'><span class='show-icon'>+</span><span class='hide-icon'>-</span> Details</a>
												<a href='#' class='new_message' data-transaction_no='".$transaction_id."' data-userid='".$seller_id."' data-buyerquoteitemid='".$id."'><i class='fa fa-envelope-o'></i></a>
											</div>
										</div>

										<div class='col-md-12 show-data-div padding-top term_quote_details_$id'>";
				$row->cells [5]->value.="<div class='col-md-4 padding-left-none'>
					<span class='data-value' >O & D Charges (per KM) : <span id='odacharges_$id'>$odcharges</span>/-</span>
					</div>";
				$row->cells [5]->value.="<div class='clearfix'></div>
											<div class='col-md-3 padding-left-none'>
												<span class='data-head'><u>Additional Charges</u></span>
											</div>

											<div class='clearfix'></div>";
                                            if($cancelCharges!='') {
                                            $row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
                                                                                                            <span class='data-value' >Cancellation Charges (Rs) : <span id='cancellation_$id'>$cancelCharges</span>/-</span>
                                                                                                    </div>";
                                            }
											
                                            if($docketCharges!='') {
                                            $row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
                                                                                                            <span class='data-value' >Other Charges (Rs) : <span id='docket_$id'>$docketCharges</span>/-</span>
                                                                                                    </div>";
                                            }

                                            if($othertext1!='') {
                                            	$row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
                                            	<span class='data-value' >$othertext1 (Rs) : <span id='other1_$id'>$otherprice1</span>/-</span>
                                            	</div>";
                                            }

                                            if($othertext2!='') {
                                            	$row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
                                            	<span class='data-value' >$othertext2 (Rs) : <span id='other2_$id'>$otherprice2</span>/-</span>
                                            	</div>";
                                            }

                                            if($othertext3!='') {
                                            	$row->cells [5]->value.="<div class='col-md-3 padding-left-none'>
                                            	<span class='data-value' >$othertext3 (Rs) : <span id='other3_$id'>$otherprice3</span>/-</span>
                                            	</div>";
                                            }
                                            if($termandcond!='') {
                                            	$row->cells [5]->value.="<div class='col-md-12  padding-top padding-left-none'>
                                            	<span class='data-head' >Terms and Conditions </span><span lass='data-value' id='tandc_$id'>$termandcond</span>
                                            	</div>";
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
	
	public static function getRelocationBuyerLeadPostsList($serviceId,$post_status){

		$from_locations = array (
				"" => "City"
		);
		
		$Query = DB::table ( 'relocationoffice_seller_posts as rsp' );
		$Query->leftjoin ( 'relocationoffice_seller_selected_buyers as rsb', 'rsb.seller_post_id', '=', 'rsp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rsp.lkp_post_status_id' );
		$Query->join ( 'lkp_cities as cf', 'rsp.from_location_id', '=', 'cf.id' );
		$Query->join ( 'lkp_quote_accesses as qa', 'rsp.lkp_access_id', '=', 'qa.id' );
		$Query->join ( 'users as u', 'rsp.seller_id', '=', 'u.id' );
		
		$Query->where( 'rsb.buyer_id', Auth::User ()->id);
		
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
		$postResults = $Query->select ( 'rsp.*', 'u.username','u.id as user_id','ps.post_status', 'cf.city_name as fromCity')->get ();
		
		foreach ( $postResults as $post ) {
				
			if (! isset ( $from_locations [$post->from_location_id] )) {
				$from_locations [$post->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $post->from_location_id)->pluck ( 'city_name' );
			}
			
		
				
		}
		$grid = DataGrid::source ( $Query );
		
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Seller Name', 'username' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'fromCity', 'City', 'fromCity' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'from_date', 'Dispatch Date', 'from_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'to_date', 'Delivery Date', 'to_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'dummy', '', 'show' )->style ( "display:none" );
		$grid->add ( 'from_location_id', 'From', 'from_location_id' )->style ( "display:none" );
		
		$grid->orderBy ( 'rsp.id', 'desc' );
		$grid->paginate ( 5 );
		
		$grid->row ( function ($row) {
			
			$seller_post_id = $row->cells[0]->value;
			$username=$row->cells [1]->value;
			$buyer_id=Auth::User ()->id;
			$row->cells [0]->style ( 'display:none' );
			$row->cells [3]->value = CommonComponent::checkAndGetDate($row->cells [3]->value);
			$row->cells [4]->value = CommonComponent::checkAndGetDate($row->cells [4]->value);
			$row->cells [1]->value="$username
			<div class='red'>
			<i class='fa fa-star'></i>
			<i class='fa fa-star'></i>
			<i class='fa fa-star'></i>
			</div>";
			$row->cells [1]->attributes(array("class" => "col-md-3 padding-left-none"));
			$row->cells [2]->attributes(array("class" => "col-md-3 padding-left-none"));
			$row->cells [3]->attributes(array("class" => "col-md-3 padding-left-none"));
			$row->cells [4]->attributes(array("class" => "col-md-2 padding-left-none"));
			$row->cells [5]->style ( 'display:none' );
			//$row->cells [6]->style ( 'display:none' );
			$msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$seller_post_id);
			$sellerPostDetails=RelocationOfficeSellerComponent::SellerPostDetails($seller_post_id);
			
			$seller_post=$sellerPostDetails['seller_post'][0];
			$totalAmount=0;
			
			$url = url().'/buyerbooknowforsearch/'.$seller_post_id;
			$row->cells [6]->value = "<div class='col-md-2 padding-none text-right'>
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
		
			$row->cells [6]->value .="
			<div class='col-md-12 margin-top'>		
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Cancellation Charges</span>
			<span class='data-value'>$seller_post->cancellation_charge_price</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Docket Charges</span>
			<span class='data-value'>$seller_post->docket_charge_price</span>
			</div>
			</div>";
			
			$row->cells [6]->value .="<div class='col-md-12  data-fld'>
			<span class='data-head'>Terms &amp; Conditions</span>
			<span class='data-value'>$seller_post->terms_conditions</span>
			</div>";
			
			
			$row->cells [6]->value .="</div>";
			
			$row->cells [6]->value .="
			</div>
	        </div>";
			
		});
		
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'rsp.from_location_id', '', 'select' )->options ( $from_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
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
			$buyer_post_details = DB::table ( 'relocationoffice_buyer_posts' )->where ( 'id', $buyer_post_id )->get ();
			$buyer_post_inventory_details = DB::table ( 'relocationoffice_buyer_post_inventory_particulars as rbpip' )
												->join('lkp_inventory_office_particulars as liop','liop.id','=','rbpip.lkp_inventory_office_particular_id')
												->where ( 'rbpip.buyer_post_id', $buyer_post_id )->get ();
			
			
			$Query = DB::table ( 'relocationoffice_buyer_quote_sellers_quotes_prices as rsqb' );
			$Query->leftjoin ( 'users as u', 'u.id', '=', 'rsqb.seller_id' );
			//$Query->leftjoin('seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id');
            $Query->leftjoin('relocationoffice_seller_posts as sp', 'sp.id', '=', 'rsqb.seller_post_id');
            $Query->where( 'rsqb.buyer_quote_id', $buyer_post_id);
			if($comparisonType==2){
				$Query->orderBy('rsqb.total_price');
			}			
			if(count($sellerIds)!=0){
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
		
		$buyer_post_edit_seller = DB::table('relocationoffice_buyer_quote_sellers_quotes_prices')
  		->where('relocationoffice_buyer_quote_sellers_quotes_prices.buyer_quote_id', $buyer_post_id)
  		->select('relocationoffice_buyer_quote_sellers_quotes_prices.*')
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
			 case RELOCATION_OFFICE_MOVE :
			 		$query->leftJoin('lkp_cities as lc', 'lc.id', '=', 'orders.from_city_id');			 		
			 		if($order_type[0]->lkp_order_type_id==1){
			 		$query->leftJoin('relocationoffice_buyer_posts as rbq', 'rbq.id', '=', 'orders.buyer_quote_id');
			 		}
			 		else{
			 		$query->leftJoin('term_buyer_quote_items as tbqi', 'tbqi.id', '=', 'orders.buyer_quote_item_id');
			 		$query->leftJoin('term_buyer_quotes as tbq', 'tbq.id', '=', 'tbqi.term_buyer_quote_id');
			 		}				 	
             		$query->leftjoin('users as u', 'u.id', '=', 'orders.seller_id')->where('orders.id', '=', $orderId);             		
             		$query->where('orders.buyer_id', '=', $user_id);
             		if($order_type[0]->lkp_order_type_id==1){
             		$orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lkp_payment_modes.payment_mode', 'oi.total_amt as inv_total','rbq.transaction_id as postid')->first();
             		}
             		else{
             		
             	    $orders['orderDetails'] = $query->select('tbq.transaction_id as postid','tbq.id as termbuyerid','tbq.lkp_post_ratecard_type as lkp_post_ratecard_type_id','tbqi.lkp_load_type_id as lkp_load_category_id','oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lkp_payment_modes.payment_mode', 'oi.total_amt as inv_total')->first();
             		}
		            break;
					}
			return $orders;
			 
		} catch ( Exception $exc ) {
			
		}
		
	}

	public static function getSellerNames($buyer_post_id){
		try {
			
			//$privateSellers= array();
			$privateSellers = DB::table('relocationoffice_buyer_selected_sellers as rbs')
			->join('users as u','u.id','=','rbs.seller_id')
			->where('rbs.buyer_post_id','=',$buyer_post_id)
			->select('u.username')
			->get();
			
			
			return $privateSellers;
		} catch ( Exception $exc ) {
		}	
		
	}

	/**
	 * Getting volume using requested inventory details
	 * Start 
	 * Jagadeesh - 16052016
	 */
		public static function getSearchInventoryCFT($items){
			$totalcft=0;
			foreach ($items as $particular_id=>$particular_qty){
				$itemvolume = DB::table('lkp_inventory_office_particulars as lirp')
	  	 			->where('lirp.id',$particular_id)
	  	 			->select('lirp.*')
	  	 			->get();

	 			$totalcft=$totalcft+($particular_qty*$itemvolume[0]->volume);
			}
			return $totalcft;
		}	
	/**
	 * Jagadeesh - 16052016
	 * End
	 */
}
