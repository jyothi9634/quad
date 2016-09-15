<?php

namespace App\Components\RelocationGlobal;

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
use App\Components\SellerComponent;
use App\Components\Search\BuyerSearchComponent;
use App\Components\MessagesComponent;
use App\Components\Ptl\PtlBuyerGetQuoteBooknowComponent;
use App\Components\RelocationGlobal\RelocationGlobalSellerComponent;

class RelocationGlobalBuyerComponent {

public static function getQuotesCount($buyer_post_id){
		
		$buyer_post_edit_seller = DB::table('relocationgm_buyer_quote_sellers_quotes_prices as bqsp')
  		->where('bqsp.buyer_post_id', $buyer_post_id)
  		//->groupBy('bqsp.buyer_post_id')
  		->select('bqsp.*')
		->get();
		return count($buyer_post_edit_seller);
		
	}

public static function getRelocationGmBuyerPostsList($service_id, $post_status, $enquiry_type) {
	
		
		// Filters values to populate in the page
		$to_locations = array (
				"" => "Location"
		);
			
		/*$buyer_services = array (
			"" => "Service"
		);
*/
		$from_date = '';
		$to_date = '';
		$order_no = '';
	
		// query to retrieve buyer posts list and bind it to the grid
		$Query = DB::table ( 'relocationgm_buyer_posts as rbs' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rbs.lkp_post_status_id' );
		$Query->leftjoin ( 'lkp_quote_accesses as qa', 'qa.id', '=', 'rbs.lkp_quote_access_id' );
		$Query->leftjoin ( 'lkp_cities as ct', 'rbs.location_id', '=', 'ct.id' );
		$Query->leftjoin ( 'relocationgm_buyer_quote_items as rbi', 'rbs.id', '=', 'rbi.buyer_post_id' );
		$Query->where( 'rbs.created_by', Auth::User ()->id );
		$Query->where('rbs.lkp_post_status_id','!=',6);
		$Query->where('rbs.lkp_post_status_id','!=',7);
		$Query->where('rbs.lkp_post_status_id','!=',8);	
		$Query->groupBy('rbi.buyer_post_id');
		
		// conditions to make search
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
			$Query->where ( 'rbs.dispatch_date', '<=', $commonDeliveryhDate);
			$to_date = $commonDeliveryhDate;
			//echo "To Date :"; echo $to_date;die();
		}
	
		if (isset ( $_GET ['relgm_service_type'] ) && $_GET ['relgm_service_type'] != '') {
			$Query->where ( 'rbi.lkp_gm_service_id', '=', $_GET ['relgm_service_type'] );
		}

		
		$postResults = $Query->select ( 'rbs.*','rbi.buyer_post_id','rbi.lkp_gm_service_id', 'ps.post_status', 'ct.city_name as toCity','qa.quote_access')->get ();
		
		//echo "<pre>"; print_r($postResults);die();
		// Functionality to handle filters based on the selection starts

		//$buyer_postids = array();
		foreach ( $postResults as $post ) {
				//$buyer_postids[] = $post->id;
				if (! isset ( $to_locations [$post->location_id] )) {
					$to_locations [$post->location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $post->location_id)->pluck ( 'city_name' );
				}
		}

		$to_locations = CommonComponent::orderArray($to_locations);
		
	/*	//$buyer_postids = "'".implode("','",$buyer_postids)."'";
		$buyerserviceids_qry = DB::table ( 'relocationgm_buyer_quote_items as rgmbqi' );
		$buyerserviceids_qry->whereIn( 'rgmbqi.buyer_post_id',$buyer_postids);
		$buyerserviceids_qry->orderby('rgmbqi.lkp_gm_service_id','asc');
		$buyerserviceids = $buyerserviceids_qry->select ( 'rgmbqi.lkp_gm_service_id')->get ();

		foreach ( $buyerserviceids as $buyerservice ) {
				if (! isset ( $buyer_services [$buyerservice->lkp_gm_service_id] )) {
					$buyer_services [$buyerservice->lkp_gm_service_id] = DB::table ( 'lkp_relocationgm_services' )->where ( 'id',$buyerservice->lkp_gm_service_id)->pluck ( 'service_type' );
				}
		}
		buyer_services = CommonComponent::orderArray($buyer_services);
	*/
		
		
		$grid = DataGrid::source ( $Query );
	
		$grid->add ( 'id', 'ID', 'true' )->style ( "display:none" );
		$grid->add ( 'dispatch_date', 'Date', 'dispatch_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
		
		$grid->add ( 'toCity', 'Location', 'toCity' )->attributes(array("class" => "col-md-4 padding-left-none"));
		
		$grid->add ( 'quote_access', 'Post Type','quote_access')->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'post_status', 'Status', 'post_status' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'dummy', '', 'show' )->style("display:none");
		$grid->add ( 'lkp_quote_access_id', 'Buyer access_id', 'lkp_quote_access_id' )->style ( "display:none" );
		$grid->add ( 'buyer_post_id', 'Buyer id', 'buyer_post_id' )->style ( "display:none" );
		$grid->orderBy ( 'rbs.id', 'desc' );
		$grid->paginate ( 5 );
	
		$grid->row ( function ($row) {
			
					
			$buyer_post_id = $row->cells[0]->value;

			$data_link = url()."/getbuyercounteroffer/$buyer_post_id";
			$row->cells [0]->style ( 'display:none' );
			$row->cells [1]->attributes(array("class" => "html_link col-md-3 padding-left-none","data_link"=>$data_link));
			$row->cells [2]->attributes(array("class" => "html_link col-md-4 padding-left-none","data_link"=>$data_link));
			$row->cells [3]->attributes(array("class" => "html_link col-md-2 padding-left-none","data_link"=>$data_link));
			$row->cells [4]->attributes(array("class" => "html_link col-md-2 padding-left-none","data_link"=>$data_link));
			//$row->cells [5]->style("display:none");
			$row->cells [6]->style("display:none");
			$row->cells [7]->style("display:none");
			
			$row->cells [1]->value=CommonComponent::checkAndGetDate($row->cells [1]->value);
					
                        
			$dispatchDate = $row->cells [1]->value;
	        $countview = PtlBuyerGetQuoteBooknowComponent::updateBuyerQuoteDetailsViews($buyer_post_id,'relocationgm_buyer_post_views');
	        $msg_count  = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyer_post_id);
	        $quotescount = RelocationGlobalBuyerComponent::getQuotesCount($buyer_post_id);
	        if ( $row->cells [4]->value == 'Open') {
	        	$row->cells [5]->value .= "<div class='col-md-1 padding-none text-right'>
	        	<a href='#' data-target='#cancelbuyerpostmodal' data-toggle='modal' onclick='setcancelbuyerpostid(".$buyer_post_id.")' ><i class='fa fa-trash buyerpostdelete' title='Delete'></i></a>
	        	</div>";
	        }	        
	     	$row->cells [5]->value .= "<div class='clearfix'></div><div class='pull-left'>
	        <div class='info-links'>
	        <a href='/getbuyercounteroffer/$buyer_post_id?type=messages'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>".count($msg_count['result'])."</span></a>
	        <a href='/getbuyercounteroffer/$buyer_post_id'><i class='fa fa-file-text-o'></i> Quotes<span class='badge'>$quotescount</span></a>
	        <a href='#'><i class='fa fa-line-chart'></i> Market Analytics<span class='badge'>0</span></a>
	        <a href='#'><i class='fa fa-file-text-o'></i> Documentation<span class='badge'>0</span></a>
	        </div>
	        </div>
	        <div class='pull-right text-right'>
	        <div class='info-links'>
	        
	        <a>
	        	<span class='views red'><i class='fa fa-eye' title='Views'></i> $countview </span>
	        </a>
	        </div>
	        </div>";
	
		} );
	
		// Functionality to build filters in the page starts
		$filter = DataFilter::source ( $Query );
		$filter->add ( 'rbs.location_id', '', 'select' )->options ( $to_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		
		$filter->submit ( 'search' );
		$filter->reset ( 'reset' );
		$filter->build ();
					// Functionality to build filters in the page ends
	
		$result = array ();
		$result ['grid'] = $grid;
		$result ['filter'] = $filter;
	/*	dd($postResults);
		exit;*/
		return $result;
	}		
	
	
       
		public static function getRelocationGmBuyerLeadPostsList($serviceId,$post_status,$enquiry_type){
		
		$to_locations = array (
				"" => "Location"
		);

		$Query = DB::table ( 'relocationgm_seller_posts as rsp' );
		$Query->leftjoin ( 'relocationgm_seller_selected_buyers as rsb', 'rsb.seller_post_id', '=', 'rsp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rsp.lkp_post_status_id' );		
		$Query->leftjoin ( 'lkp_cities as ct', 'rsp.location_id', '=', 'ct.id' );
        $Query->leftjoin ( 'lkp_payment_modes as paymode', 'rsp.lkp_payment_mode_id', '=', 'paymode.id' );                	
		$Query->leftjoin ( 'lkp_quote_accesses as qa', 'rsp.lkp_access_id', '=', 'qa.id' );
		$Query->leftjoin ( 'users as u', 'rsp.seller_id', '=', 'u.id' );
		$Query->where( 'rsp.lkp_post_status_id', 2);
		$Query->where( 'rsb.buyer_id', Auth::User ()->id);
		$Query->where('rsp.is_private', 0);


		if (isset($post_status ) && $post_status != '') {
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
		
		//$Query->groupBy('rsp.id');
		$postResults = $Query->select ( 'rsp.*', 'u.username','u.id as user_id','ps.post_status', 'ct.city_name as toCity', 'paymode.payment_mode as paymentmode')->get ();
		foreach ( $postResults as $post ) {
						
			if (! isset ( $to_locations [$post->location_id] )) {
				$to_locations [$post->location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $post->location_id )->pluck ( 'city_name' );
			}
		
				
		}
		$to_locations = CommonComponent::orderArray($to_locations);
		
		$grid = DataGrid::source ( $Query );
                //echo "<pre>"; dd($postResults); die;		
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Name', 'username' )->attributes(array("class" => "col-md-3 padding-left-none"));		
		$grid->add ( 'toCity', 'Location', 'toCity' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-2 padding-left-none"));		
		$grid->add ( 'dummy', '', 'show' )->style ( "display:none" );
		$grid->add ( 'location_id', 'From', 'location_id' )->style ( "display:none" );
        $grid->add ( 'terms_conditions', 'terms_conditions', 'terms_conditions' )->style ( "display:none" );
		$grid->add ( 'paymentmode', 'paymentmode', 'paymentmode' )->style ( "display:none" );
      
      //service charges 
		$grid->add ( 'city_orientation', 'city orientation', 'city orientation' )->style ( "display:none" );
		$grid->add ( 'home_view', 'home view', 'home view' )->style ( "display:none" );		
		$grid->add ( 'home_search', 'home search', 'home search' )->style ( "display:none" );		
		$grid->add ( 'frro', 'frro charges', 'frro charges' )->style ( "display:none" );
		$grid->add ( 'visa_extension', 'visa extension', 'visa extension' )->style ( "display:none" );
		$grid->add ( 'settling_services', 'settling services', 'settling services' )->style ( "display:none" );
		$grid->add ( 'cross_cultural_training', 'cross cultural training', 'cross cultural training' )->style ( "display:none" );
        $grid->add ( 'cancellation_charge_price', 'cancellation charges', 'cancellation_charge_price' )->style ( "display:none" );
        $grid->add ( 'other_charge1_price', 'other charges', 'other_charge1_price' )->style ( "display:none" );
        $grid->add ( 'transaction_id', 'transation Id', 'transaction_id' )->style ( "display:none" );
        $grid->add ( 'seller_id', 'seller Id', 'seller_id' )->style ( "display:none" );
        $grid->add ( 'credit_period', 'Credit Period', 'credit_period' )->style ( "display:none" );
        $grid->add ( 'credit_period_units', 'Credit Period Units', 'Credit Period Units' )->style ( "display:none" );
                
		$grid->orderBy ( 'rsp.id', 'desc' );
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
                    $row->cells [14]->style ( 'display:none' );
                    $row->cells [15]->style ( 'display:none' );
                    $row->cells [16]->style ( 'display:none' );
                    $row->cells [17]->style ( 'display:none' );
                    $row->cells [18]->style ( 'display:none' );
                    $row->cells [19]->style ( 'display:none' );
                    $row->cells [20]->style ( 'display:none' );
                    $row->cells [21]->style ( 'display:none' );
					$cancellationcharges = "0.00";
					$otherchages_1= "0.00";

                    $seller_post_id = $row->cells[0]->value;
                    $username=$row->cells [1]->value;
                    
                    $toCity=$row->cells [2]->value;
                    $buyer_id=Auth::User ()->id;
                    $validfrom=$row->cells [3]->value;
                    $validto=$row->cells [4]->value;
                    $termconiditons=$row->cells [7]->value;  
                    
                    $paymentmode=$row->cells [8]->value;                     
                    $cancellationcharges = ($row->cells [16]->value != "" ) ? $row->cells [16]->value : $cancellationcharges;  
                    $otherchages_1=($row->cells [17]->value != "" ) ? $row->cells [17]->value : $otherchages_1;  
                    $transaction_id=$row->cells [18]->value;  
                    $seller_user_id=$row->cells [19]->value;  
                    $credit_period = $row->cells [20]->value;  
                    $credit_period_units = $row->cells [21]->value;  
                   
                    
   					$msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$seller_post_id);
                    $sellerPostDetails=RelocationGlobalSellerComponent::SellerPostServicesDetails($seller_post_id);
                    $seller_post=$sellerPostDetails['seller_post'][0];    


                    $seller_payment_mode_method = CommonComponent::getSellerPostPaymentMethod($paymentmode);
					if ($paymentmode == 'Advance') {
					        $paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
					} elseif ($paymentmode == 'Credit'){                            
					        $paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode.' | '.$seller_post->credit_period.' '.$seller_post->credit_period_units;
					}else {
					        $paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode;
					}
                             

                    $totalAmount = 0;

                    $url = url().'/buyerbooknowforsearch/'.$seller_post_id;
                    $row->cells [7]->value = "<div class='table-data'>
                                <div class='table-row '>
                                <div class='col-md-3 padding-left-none'>
                                        $username  
                                        <div class='red'>
                                                <i class='fa fa-star'></i>
                                                <i class='fa fa-star'></i>
                                                <i class='fa fa-star'></i>
                                        </div>
                                </div>
                                 <div class='col-md-3 padding-left-none'>$toCity</div>
                                <div class='col-md-2 padding-left-none'>".CommonComponent::checkAndGetDate($validfrom)."</div>
                                <div class='col-md-3 padding-left-none'>".CommonComponent::checkAndGetDate($validto)."</div>
                                

                                <div class='clearfix'></div>

                                <div class='pull-left'>
                                        <div class='info-links'>                                           
                                                <a href='#'>$paymentType</a>
                                        </div>
                                </div>
                                <div class='pull-right text-right'>
                                        <div class='info-links'>
                                                <a id='".$seller_post_id."' class='viewcount_show-data-link view_count_update' data-quoteId='$seller_post_id'><span class='show-icon'>+</span><span class='hide-icon'>-</span> Details</a>
                                                <a href='#' class='new_message' data-transaction_no='".$transaction_id."' data-userid='".$seller_user_id."' data-buyerquoteitemid='".$seller_post_id."'><i class='fa fa-envelope-o'></i></a>
                                        </div>
                                </div>

                                <div class='col-md-12 show-data-div padding-top'> ";
                   
						$row->cells[7]->value .= "<div class='col-md-3 form-control-fld padding-left-none'>
												<span class='data-value'>City Orientation (".CommonComponent::getAllGMServiceTypeUnitsById(1).") : ".$seller_post->city_orientation."/-</span>
											</div>
									
											<div class='col-md-3 form-control-fld padding-left-none1>
												<span class='data-value'>Home View (".CommonComponent::getAllGMServiceTypeUnitsById(2).") : ".$seller_post->home_view."/-</span>
											</div>

											<div class='col-md-3 form-control-fld padding-left-none1>
												<span class='data-value'>Home Search (".CommonComponent::getAllGMServiceTypeUnitsById(3).") : ".$seller_post->home_search."/-</span>
											</div>

											<div class='col-md-3 form-control-fld padding-left-none1>
												<span class='data-value'>FRRO (".CommonComponent::getAllGMServiceTypeUnitsById(4).") : ".$seller_post->frro."/-</span>
											</div>

											<div class='col-md-3 form-control-fld padding-left-none1>
												<span class='data-value'>Visa Extension (".CommonComponent::getAllGMServiceTypeUnitsById(5).") : ".$seller_post->visa_extension."/-</span>
											</div>

											<div class='col-md-3 form-control-fld padding-left-none1>
												<span class='data-value'>Settling Services (".CommonComponent::getAllGMServiceTypeUnitsById(6).") : ".$seller_post->settling_services."/-</span>
											</div>

											<div class='col-md-3 form-control-fld padding-left-none1>
												<span class='data-value'>Cross Cultural Training (".CommonComponent::getAllGMServiceTypeUnitsById(7).") : ".$seller_post->cross_cultural_training."/-</span>
											</div>

											<div class='clearfix'></div>";                            
                        $row->cells [7]->value .=" 
               
                                <div class='col-md-12 padding-none form-control-fld'>
                                        <span class='data-head'>Additinal Charges</span>
                                </div>

                                <div class='col-md-3 padding-left-none'>
                                        <span class='data-value'>Cancellation Charges (Flat) : $cancellationcharges/-</span>
                                </div>
                                <div class='col-md-3 padding-left-none'>
                                        <span class='data-value'>Other Charges (Flat) : $otherchages_1/-</span>
                                </div>
                                <div class='clearfix'></div> ";
                     
                                
                    $row->cells [7]->value .=" </div>
                </div>
            </div>";
                        
			
			
                });

                $filter = DataFilter::source ( $Query );
                $filter->add ( 'rsp.location_id', '', 'select' )->options ( $to_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
              
                $filter->submit ( 'search' );
                $filter->reset ( 'reset' );
                $filter->build ();

                $result = array ();
                $result ['grid'] = $grid;
                $result ['filter'] = $filter;
                return $result;

	}

        
    // buyer search for seller posts result component for relocation Global Mobility
	public static function getRelocationGmBuyerSearchResults($request, $serviceId) {
		try {
			$prices = array();
            $sellerNames=array();
            $paymentMethods = array ();
			            
            $request['trackingfilter'] = array();
            if (isset ( $request['tracking'] ) && $request['tracking']!= '') {
                $request['trackingfilter'][] = $request['tracking'];
            }
            if (isset ( $request ['tracking1'] ) && $request ['tracking1'] != '') {
                $request['trackingfilter'][] = $request['tracking1'];
            }
                       
			$Query_buyers_for_sellers = BuyerSearchComponent::search ($roleId=null,$serviceId,$statusId=null, $request );
			$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();	
			//Session::put('relocbuyerrequest', $request->all());	
                        
			if(isset($request['to_location']) && $request['to_location'] && isset($request['from_date']) && $request['from_date'])
			{					
				session()->put([
					'searchMod' => [
						'dispatch_date_buyer'		=> $request->from_date,
						'to_city_id_buyer' 			=> $request->to_location_id,
						'to_location_buyer' 		=> $request->to_location,
						'service_type_relocation'	=> $request->relgm_service_type,
						'measurement_relocation'	=> $request->measurement,
					]
				]);    
			}

			//Save Data in sessions			
			if (empty ( $Query_buyers_for_sellers_filter ) && !isset($_REQUEST['filter_set'])) {
				//CommonComponent::searchTermsSendMail ();				
				Session::put('layered_filter', '');
				Session::put('layered_filter_payments', '');
				Session::put('show_layered_filter','');
			}
			// Below script for filter data getting from queries --for filters
            if(!isset($_REQUEST['filter_set'])){
				foreach ( $Query_buyers_for_sellers_filter as $seller_post_item ) {
                    $prices[] = RelocationGlobalBuyerComponent::priceCalculation($seller_post_item,$_REQUEST);
                    if (! isset ( $paymentMethods [$seller_post_item->lkp_payment_mode_id] ) ) {
                            $paymentMethods[$seller_post_item->lkp_payment_mode_id] = $seller_post_item->payment_mode;
                            Session::put('layered_filter_payments', $paymentMethods);  
                    }
                    
                    if (! isset ( $sellerNames [$seller_post_item->seller_id] ) ) {
                            $sellerNames[$seller_post_item->seller_id] = $seller_post_item->username;
                            Session::put('layered_filter', $sellerNames);	 
                    }
			    }
            }
            if (empty ( $Query_buyers_for_sellers ) && !isset($_REQUEST['filter_set'])) {
                CommonComponent::searchTermsSendMail ();
            }
            $result = $Query_buyers_for_sellers->get ();

            $Query_buyers_for_sellersnew = array();
            foreach($result as $Query_buyers_for_seller){
                $resp = RelocationGlobalBuyerComponent::priceCalculation($Query_buyers_for_seller,$_REQUEST);
                $Query_buyers_for_seller->newprice = isset($resp) ? $resp : 0;
                //get seller posted service price
                $Query_buyers_for_seller->sellerpost_service_price = RelocationGlobalBuyerComponent::getServiceWiseprice($Query_buyers_for_seller,$_REQUEST);
                $Query_buyers_for_sellersnew[] = $Query_buyers_for_seller;
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
                        
            if(isset($_REQUEST['price_from']) && isset($_REQUEST['price_to'])){
                $pricefrom = $_REQUEST['price_from'];
                $priceto = $_REQUEST['price_to'];
                foreach($Query_buyers_for_sellersnew as $key => $Query_buyers_for_sellersnewrow){
                    if($Query_buyers_for_sellersnewrow->newprice >= $pricefrom && $Query_buyers_for_sellersnewrow->newprice <= $priceto){}
                    else{
                        unset($Query_buyers_for_sellersnew[$key]);
                    }
                }
                $result = $Query_buyers_for_sellersnew;
            }
            if (empty ( $result ) && !isset($_REQUEST['filter_set'])) {
                Session::put('show_layered_filter','');
            }
                    
			$gridBuyer = DataGrid::source ( $result );
			$gridBuyer->add ( 'id', 'ID', true )->style ( "display:none" );
			$gridBuyer->add ( 'username', 'Name', false )->attributes(array("class" => "col-md-3 padding-left-none"));
            $gridBuyer->add ( 'to_date', 'Valid To', 'from_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
			//$gridBuyer->add ( 'to_date', 'Valid To ', 'to_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$gridBuyer->add ( 'test', 'Below Grid', true )->style ( "display:none" );			
			$gridBuyer->add ( 'rate_per_cft', 'Rate ', false )->style ( "display:none" );		
			$gridBuyer->add ( 'payment_mode', 'Payment mode', false )->style ( "display:none" );
			$gridBuyer->add ( 'transaction_id', 'Transaction Id',false )->style('display:none');
			$gridBuyer->add ( 'created_by', 'created by', 'created_by' )->style ( "display:none" );				
			$gridBuyer->add ( 'newprice', 'Price', false )->attributes(array("class" => "col-md-2 padding-left-none"));			              
            $gridBuyer->add ( 'cancellation_charge_price', 'cancellation_charge_price', 'cancellation_charge_price' )->style ( "display:none" );   
            $gridBuyer->add ( 'docket_charge_price', 'docket_charge_price', 'docket_charge_price' )->style ( "display:none" );   
            $gridBuyer->add ( 'tocity', 'To City', 'tocity' )->style ( "display:none" );
            $gridBuyer->add ( 'terms_conditions', 'TermsandConditions', 'terms_conditions' )->style ( "display:none" );	
            $gridBuyer->add ( 'lkp_pet_type_id', 'Pet Type Id', false )->style ( "display:none" );	
            //$gridBuyer->add ( 'postid', 'postid', 'postid' )->style ( "display:none" );
            $gridBuyer->add ( 'sellerpost_service_price', 'Service Price', false )->style ( "display:none" );	
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
				$id = $row->cells [0]->value;
				
				$buyerName = $row->cells [1]->value;	
				$toDate = $row->cells [2]->value;
				$frightPerCft = $row->cells [4]->value;
				$paymentmode = $row->cells [5]->value;	
				$transaction_id = $row->cells [6]->value;	
				$seller_id = $row->cells [7]->value;
				$cancellationCharges = $row->cells [9]->value;	
				$docketCharges = $row->cells [10]->value;	
				$toCity = $row->cells [11]->value;
				$termsAndConditions = $row->cells [12]->value;
				$sp_service_price = $row->cells [14]->value;

				if ($paymentmode == 'Advance') {
					$paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
				} else {
					$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode;
				}
				//Total Price caclulations
                $totalPrice = $row->cells [8];
				CommonComponent::viewCountForSeller(Auth::User()->id,$id,'relocationgm_seller_post_views');
                                
				$url = url().'/buyerbooknowforsearch/'.$id;
				$row->cells [5]->value="<form method='GET'role='form' action='$url' id='addptlbuyersearchbooknow_$id' name='addptlbuyersearchbooknow_$id'>"
                                        . "<div class='table-data'><div class='table-row '>
                                        <div class='col-md-3 padding-left-none'>
                                                $buyerName
                                                <div class='red'>
                                                        <i class='fa fa-star'></i>
                                                        <i class='fa fa-star'></i>
                                                        <i class='fa fa-star'></i>
                                                </div>
                                        </div>
                                       
                                        <div class='col-md-3 padding-left-none'>".CommonComponent::checkAndGetDate($toDate)."</div>
                                        <div class='col-md-2 padding-left-none'>$totalPrice /-</div>
                                        <div class='col-md-4 pull-right'>
                                                <button class='btn red-btn pull-right'>Book Now</button>
                                        </div>

                                        <div class='clearfix'></div>

                                        <div class='pull-left'>
                                                <div class='info-links'>
                                                        <a href='#'>$paymentType</a>
                                                </div>
                                        </div>
                                        <div class='pull-right text-right'>
                                                <div class='info-links'>
                                                        <a id='".$id."' class='viewcount_show-data-link view_count_update' data-quoteId='$id'><span class='show-icon'>+</span><span class='hide-icon'>-</span> Details</a>
                                                        <a href='#' class='new_message' data-transaction_no='".$transaction_id."' data-userid='".$seller_id."' data-buyerquoteitemid='".$id."'><i class='fa fa-envelope-o'></i></a>
                                                </div>
                                        </div>

                                <div class='col-md-12 show-data-div padding-top'>

                                <h3 class='margin-none'><i class='fa fa-map-marker'></i>$toCity</h3>

                                <div class='col-md-3 form-control-fld padding-left-none'>
                                <span class='data-value'>".CommonComponent::getAllGMServiceTypesById($_REQUEST['relgm_service_type'])." (".CommonComponent::getAllGMServiceTypeUnitsById($_REQUEST['relgm_service_type']).") : $sp_service_price /-</span>
                                </div>    

                                <div class='clearfix'></div>

                                <div class='col-md-12 padding-none form-control-fld'>
                                        <span class='data-head'><u>Additional Charges</u></span>
                                </div>
                                <div class='col-md-3 padding-left-none'>
                                        <span class='data-value'>Cancellation Charges(Flat) : $cancellationCharges /-</span>
                                </div>
                                <div class='col-md-3 padding-left-none'>
                                        <span class='data-value'>Docket Charges(Flat) : $docketCharges /-</span>
                                </div>
                                <div class='clearfix'></div> ";
                        
                        if($termsAndConditions!='') {
                        $row->cells [5]->value .= "<div class='col-md-12 form-control-fld padding-none'>
                                                        <span class='data-head'>Terms & Conditions</span>
                                                        <span class='data-value'>$termsAndConditions</span>
                                                   </div>";
                        }
                        
                        $row->cells [5]->value .= " </div></div></div>
                                <input id='buyersearch_booknow_buyer_id_$id' type='hidden' value=".Auth::User()->id." name='buyersearch_booknow_buyer_id_$id' >
                                <input id='buyersearch_booknow_seller_id_$id' type='hidden' value=".$seller_id." name='buyersearch_booknow_seller_id_$id'>
                                <input id='buyersearch_booknow_seller_price_$id' type='hidden' value=".$totalPrice." name='buyersearch_booknow_seller_price_$id'>
                                
                                <input id='buyersearch_booknow_to_date_$id' type='hidden' value=".$toDate.">
                                <input id='buyersearch_booknow_dispatch_date_$id' type='hidden' value='".CommonComponent::convertDateForDatabase(Session::get('session_dispatch_date_buyer'))."'></form>";
			} );
			
				
				$result = array ();
				$result ['gridBuyer'] = $gridBuyer;
				//$result ['filter'] = $filter;
				return $result;
			
		} catch ( Exception $exc ) {		
		}
	}
        
        public static function priceCalculation($sp,$request) {
            try{
                $servicetype    =   CommonComponent::getAllGMServiceTypesById($request['relgm_service_type']);
                $str_name   =   strtolower(str_replace(' ','_',$servicetype));
                if($request['relgm_service_type']==3){
                    $price  =   ($sp->$str_name/100)*$request['measurement'];
                }elseif($request['relgm_service_type']==7){
                    $price  =   $sp->$str_name;
                }else{
                    $price  =   $sp->$str_name*$request['measurement'];
                }
                return $price;
            } catch (Exception $ex) {

            }
            
        }

        public static function getServiceWiseprice($sp,$request) {
            try{
                $servicetype    =   CommonComponent::getAllGMServiceTypesById($request['relgm_service_type']);
                $str_name   =   strtolower(str_replace(' ','_',$servicetype));
                return $sp->$str_name;
            } catch (Exception $ex) {

            }
            
        }

	public static function getBuyerPostDetails($buyer_post_id, $serviceId=null,$roleid=null,$comparisonType=null,$sellerIds=null) {
		try {

			$buyer_post_edit_seller='';
			$buyer_post_quoteitems_details='';
			$buyer_post_details = DB::table ( 'relocationgm_buyer_posts' )->where ( 'id', $buyer_post_id )->get ();
			
			
			$buyer_post_quoteitems_details = DB::table ( 'relocationgm_buyer_quote_items' )->where ( 'buyer_post_id', $buyer_post_id )->get ();
			
			
			$Query = DB::table ( 'relocationgm_buyer_quote_sellers_quotes_prices as rsqb' );
			$Query->leftjoin ( 'users as u', 'u.id', '=', 'rsqb.seller_id' );
                        
			$Query->leftjoin('relocationgm_seller_posts as sp', 'sp.id', '=', 'rsqb.seller_post_id');
                        $Query->leftjoin('lkp_cities as c1', 'c1.id', '=', 'sp.location_id');
			$Query->leftjoin('relocationgm_buyer_quote_items as rbqi', 'rbqi.id', '=', 'rsqb.buyer_quote_item_id');
                        $Query->where( 'rbqi.buyer_post_id', $buyer_post_id);
                        //$Query->groupBy( 'rsqb.buyer_post_id');
		/*	if($comparisonType==1){
				$Query->orderBy('rsqb.transit_days');
			}
			if($comparisonType==2){
				$Query->orderBy('rsqb.total_price');
			}	*/		
			if($sellerIds){
			$sellerIds= explode(",",$sellerIds);
			$Query->whereIn( 'rsqb.seller_id', $sellerIds);			
			}
			$sellerResults = $Query->select ('c1.city_name','rsqb.buyer_post_id','rsqb.private_seller_quote_id','rsqb.seller_post_id','sp.from_date','sp.to_date','sp.transaction_id as transaction_no','sp.cancellation_charge_price','sp.other_charge1_price', 'rsqb.*', 'u.username')->get ();
			
			$sellerQuoteItems = DB::table('relocationgm_buyer_quote_sellers_quotes_prices as rbqsqp')
								->leftjoin('relocationgm_buyer_quote_items as rbqi','rbqi.id','=','rbqsqp.buyer_quote_item_id')
								->leftjoin('lkp_relocationgm_services as lrs','lrs.id','=','rbqi.lkp_gm_service_id')
								->where ( 'rbqi.buyer_post_id', $buyer_post_id )
								->select('lrs.*','rbqi.measurement')	
								->get ();			
			/*$j=0;
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
			}*/

			$result = array();			
			$result ['postDetails'] = $buyer_post_details;
			$result ['quoteItemsDetails'] = $buyer_post_quoteitems_details;
			$result['sellerResults'] = $sellerResults;	
			$result['sellerQuoteItems'] = $sellerQuoteItems;

			return $result;
			
		} catch ( Exception $exc ) {
		}
	}




}
