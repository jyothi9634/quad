<?php

namespace App\Components\AirInternational;

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
use App\Components\Search\SellerSearchComponent;
use App\Models\PtlZone;
use App\Models\PtlTier;
use App\Models\PtlTransitday;
use App\Models\PtlSector;

class AirInternationalSellerComponent {
	public static function getAirInternationalSellerSearchList($roleId, $serviceId, $statusId) {
// 		/echo "asdf"; print_R($_GET);exit;
		$from_locations = array (
				"" => "From Location" 
		);
		$to_locations = array (
				"" => "To Location" 
		);
		$packaging_types = array (
				"" => "Package Type" 
		);
		$load_types = array (
				"" => "Load Type" 
		);
		
		
		$inputparams = array();
		$inputparams = $_REQUEST;
		
		
		if(isset($_REQUEST['lkp_load_type_ids']) && $_REQUEST['lkp_load_type_ids']!=''){
			if(isset($_REQUEST['lkp_load_type_id']) && $_REQUEST['lkp_load_type_id']!=''){
				$_REQUEST['lkp_load_type_id'] =$_REQUEST['lkp_load_type_id'];
			}else{
				$_REQUEST['lkp_load_type_id'] =$_REQUEST['lkp_load_type_ids'];
			}
		}
		
		if(isset($_REQUEST['lkp_packaging_type_ids']) && $_REQUEST['lkp_packaging_type_ids']!=''){
			if(isset($_REQUEST['lkp_packaging_type_id']) && $_REQUEST['lkp_packaging_type_id']!=''){
				$_REQUEST['lkp_packaging_type_id'] =$_REQUEST['lkp_packaging_type_id'];
			}else{
				$_REQUEST['lkp_packaging_type_id'] =$_REQUEST['lkp_packaging_type_ids'];
			}
		}
		
		$Query_buyers_for_sellers = SellerSearchComponent::search ( $roleId, $serviceId, $statusId, $inputparams );
//		echo "From - ".$_REQUEST ['from_location_id']." To - ".$_REQUEST ['to_location_id'];
		if (isset ( $_REQUEST ['lkp_packaging_type_id'] ) && $_REQUEST ['lkp_packaging_type_id'] != '' && isset ( $_REQUEST ['lkp_load_type_id'] ) && $_REQUEST ['lkp_load_type_id'] != '' && isset ( $_REQUEST ['from_location_id'] ) && $_REQUEST ['from_location_id'] != '' && isset ( $_REQUEST ['to_location_id'] ) && $_REQUEST ['to_location_id'] != '' && isset ( $_REQUEST ['dispatch_date'] ) && $_REQUEST ['dispatch_date'] != '') {
			$sellerpost_for_buyers = new PtlSearchTerm ();
			$sellerpost_for_buyers->user_id = Auth::id ();
			$sellerpost_for_buyers->from_location_id = $_REQUEST ['from_location_id'];
			$sellerpost_for_buyers->to_location_id = $_REQUEST ['to_location_id'];
			$sellerpost_for_buyers->dispatch_date = $_REQUEST ['dispatch_date'];
			$sellerpost_for_buyers->delivery_date = $_REQUEST ['delivery_date'];
			$sellerpost_for_buyers->lkp_load_type_id = $_REQUEST ['lkp_load_type_id'];
			$sellerpost_for_buyers->lkp_packaging_type_id = $_REQUEST ['lkp_packaging_type_id'];
			$sellerpost_for_buyers->quantity = 1;
			$sellerpost_for_buyers->created_at = date ( 'Y-m-d H:i:s' );
			$sellerpost_for_buyers->created_ip = $_SERVER ['REMOTE_ADDR'];
			$sellerpost_for_buyers->created_by = Auth::id ();
			// $sellerpost_for_buyers->save();
			
			//echo "<pre>";print_r($_REQUEST);exit;
			Session::put('session_delivery_date_ptl',$_REQUEST['delivery_date']);
			Session::put('session_dispatch_date_ptl',$_REQUEST['dispatch_date']);
			Session::put('session_vehicle_type_ptl',$_REQUEST['lkp_packaging_type_id']);
			Session::put('session_load_type_ptl',$_REQUEST['lkp_load_type_id']);
			Session::put('session_from_city_id_ptl',$_REQUEST['from_location_id']);
			Session::put('session_to_city_id_ptl',$_REQUEST['to_location_id']);
			Session::put('session_from_location_ptl',$_REQUEST['from_location']);
			Session::put('session_to_location_ptl',$_REQUEST['to_location']);
			Session::put('session_shipment_type',$_REQUEST['lkp_air_ocean_shipment_type_id']);
			Session::put('session_sender_identity',$_REQUEST['lkp_air_ocean_sender_identity_id']);
		}
		$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();
		
			
	if (empty ( $Query_buyers_for_sellers_filter )) {
				SellerComponent::searchTermsSendMail ();
				Session::put('results_count','1');
				Session::put('layered_filter_payments', '');
				Session::put('show_layered_filter','');
				Session::put('layered_filter_loadtype', '');
			}else{
				
				Session::put('results_count','');
				Session::put('layered_filter', '');
			}
		
		
		foreach ( $Query_buyers_for_sellers_filter as $Query_buyers_for_seller ) {
			
			if (! isset ( $from_locations [$Query_buyers_for_seller->from_location_id] )) {
				$from_locations [$Query_buyers_for_seller->from_location_id] = $Query_buyers_for_seller->frompincode;
			}
			if (! isset ( $to_locations [$Query_buyers_for_seller->to_location_id] )) {
				$to_locations [$Query_buyers_for_seller->to_location_id] = $Query_buyers_for_seller->topincode;
			}
			if (! isset ( $load_types [$Query_buyers_for_seller->lkp_load_type_id] )) {
				$load_types [$Query_buyers_for_seller->lkp_load_type_id] = $Query_buyers_for_seller->load_type;
			}
			if (! isset ( $packaging_types [$Query_buyers_for_seller->lkp_packaging_type_id] )) {
				$packaging_types [$Query_buyers_for_seller->lkp_packaging_type_id] = $Query_buyers_for_seller->packaging_type_name;
			}
			
			if (! isset ( $sellerNames [$Query_buyers_for_seller->buyer_id] )) {
				$sellerNames[$Query_buyers_for_seller->buyer_id] = $Query_buyers_for_seller->username;
			}
				
			Session::put('layered_filter', $sellerNames);
		}
		
		// echo $Query_buyers_for_sellers->tosql()."<br/>";
		$result = $Query_buyers_for_sellers->get ();
		
		$grid = DataGrid::source ( $Query_buyers_for_sellers );
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Name', 'username' )->attributes ( array (
				"class" => "col-md-2 padding-left-none" 
		) );
		$grid->add ( 'dispatch_date', 'Dispatch Date', 'dispatch_date' )->attributes ( array (
				"class" => "col-md-3 padding-left-none" 
		) );
		$grid->add ( 'frompincode', 'From Location', 'frompincode' )->attributes ( array (
				"class" => "col-md-2 padding-left-none" 
		) );
		$grid->add ( 'topincode', 'To Location', 'topincode' )->attributes ( array (
				"class" => "col-md-2 padding-left-none" 
		) );
		$grid->add ( 'status', 'ID', true )->style ( "display:none" );
		$grid->add ( 'created_by', 'ID', true )->style ( "display:none" );
		$grid->add ( 'load_type', 'Load Type', true )->style ( "display:none" );
		$grid->add ( 'packaging_type_name', 'Packaging Type', true )->style ( "display:none" );
		$grid->add ( 'calculated_volume_weight', 'Volume', true )->style ( "display:none" );
		$grid->add ( 'units', 'Units', true )->style ( "display:none" );
		$grid->add ( 'number_packages', 'Packages', true )->style ( "display:none" );
		$grid->add ( 'transaction_id', 'transaction_id', true )->style ( "display:none" );
        $grid->add ( 'delivery_date', 'Delivery Date', 'delivery_date' )->style ( "display:none" );
        $grid->add ( 'buyer_quote_id', 'buyer_quote_id', 'buyer_quote_id' )->style ( "display:none" );
        $grid->add ( 'fromairportid', 'fromairportid', 'fromairportid' )->style ( "display:none" );
        $grid->add ( 'toairportid', 'toairportid', 'toairportid' )->style ( "display:none" );
        $grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );
		$grid->row ( function ($row) {
			$row->cells [0]->style ( 'display:none' );
			$row->cells [2]->style ( 'display:none' );
			$row->cells [3]->style ( 'display:none' );
			$row->cells [4]->style ( 'display:none' );
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
			$buyer_quote_id = $row->cells [0]->value;
			$transaction_id=$row->cells[12]->value;
			
			$buyer_name = $row->cells [1]->value;
			$from_zipcode = $row->cells [3]->value;
			$to_zipcode = $row->cells [4]->value;
			$from_zipcode_id = $row->cells [15]->value;
			$to_zipcode_id = $row->cells [16]->value;
			$dispatch_date_buyer = $row->cells [2]->value;
			$delivery_date_buyer = $row->cells [13]->value;
                        $bqid = $row->cells [14]->value;
			$price_buyer = $row->cells [4]->value;
			$fprice = $row->cells [4]->value;
			$getbqi = DB::table ( 'buyer_quote_items' )->where ( 'buyer_quote_items.id', '=', $buyer_quote_id )->select ( 'lkp_quote_price_type_id' )->get ();
			
			$buyer_id = $row->cells [6]->value;
			$buyer_quote_id = $row->cells [0]->value;
			
			$buyerdetailsvalue = DB::table ( 'airint_buyer_quote_items' )->where ( 'airint_buyer_quote_items.id', '=', $buyer_quote_id )->select ( 'airint_buyer_quote_items.*' )->get ();
			
			$buyer_post_status = $row->cells [5]->value;
			
			if ($buyer_post_status == 1) {
				$buyer_post_status = 'Saved as Draft';
			}
			if ($buyer_post_status == 2) {
				$buyer_post_status = 'Open';
			}
			if ($buyer_post_status == 3) {
				$buyer_post_status = 'Closed';
			}
			if ($buyer_post_status == 4) {
				$buyer_post_status = 'Booked';
			}
			if ($buyer_post_status == 5) {
				$buyer_post_status = 'Cancelled';
			}
			if ($price_buyer == 0) {
				$price_buyer = "Competitive";
			} else {
				$price_buyer = "Firm";
			}
			$row->cells [1]->attributes ( array (
					"class" => "col-md-2 padding-left-none" 
			) );
			$row->cells [2]->attributes ( array (
					"class" => "col-md-3 padding-left-none" 
			) );
			$row->cells [3]->attributes ( array (
					"class" => "col-md-2 padding-left-none" 
			) );
			$row->cells [4]->attributes ( array (
					"class" => "col-md-2 padding-left-none" 
			) );
			$row->cells [5]->attributes ( array (
					"class" => "col-md-3 padding-none" 
			) );
			// $row->cells [6]->attributes(array("class" => "col-md-12 col-sm-12 col-xs-12 padding-none"));
			
			$row->cells [1]->value = $buyer_name . '<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
									<i class="red fa fa-star"></i>
									<i class="red fa fa-star"></i>
									<i class="red fa fa-star"></i>
								</div>';
			$delivery_date_buyer_convert = CommonComponent::checkAndGetDate($delivery_date_buyer);
			if($delivery_date_buyer_convert != ""){
				$row->cells [2]->value = CommonComponent::checkAndGetDate($dispatch_date_buyer)." - ".$delivery_date_buyer_convert;
			}else{
				$row->cells [2]->value = CommonComponent::checkAndGetDate($dispatch_date_buyer);
			}
			
			
			$getSellerpost  = DB::table('airint_seller_post_items')
			->join( 'airint_seller_posts', 'airint_seller_posts.id', '=', 'airint_seller_post_items.seller_post_id' )
			->join( 'airint_buyer_quote_sellers_quotes_prices', 'airint_buyer_quote_sellers_quotes_prices.seller_post_item_id', '=', 'airint_seller_post_items.id' )
			->where('airint_seller_post_items.from_location_id','=',$from_zipcode_id)
			->where('airint_seller_post_items.to_location_id','=',$to_zipcode_id)
			->where('airint_seller_post_items.created_by','=',Auth::user()->id)
			->where('airint_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$bqid)
			->where('airint_seller_posts.lkp_post_status_id','=',OPEN)
			->select('airint_seller_post_items.seller_post_id',
					'airint_seller_post_items.id',
					'airint_seller_posts.tracking',
					'airint_seller_posts.lkp_payment_mode_id',
					'airint_seller_posts.accept_payment_netbanking',
					'airint_seller_posts.accept_payment_credit',
					'airint_seller_posts.accept_payment_debit',
					'airint_seller_posts.credit_period',
					'airint_seller_posts.credit_period_units',
					'airint_seller_posts.accept_credit_netbanking',
					'airint_seller_posts.accept_credit_cheque')
					->get();
			
		
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
			
			
			$row->cells [3]->value = $from_zipcode;
			$row->cells [4]->value = $to_zipcode;
			$toloc = CommonComponent::getPinNameWithPincode($row->cells [4]->value);
		
						
						
							
			$row->cells [5]->value .= '';
							
							$cft ='Conversion Kg/CCM';
							
							$quoteid = DB::table('airint_buyer_quote_items')
							->where('airint_buyer_quote_items.id','=',$buyer_quote_id)
							->select('airint_buyer_quote_items.buyer_quote_id')
							->get();
							
							$quoteitems = DB::table('airint_buyer_quote_items')
							->join('lkp_load_types','lkp_load_types.id','=','airint_buyer_quote_items.lkp_load_type_id')
							->join('lkp_packaging_types','lkp_packaging_types.id','=','airint_buyer_quote_items.lkp_packaging_type_id')
							->where('airint_buyer_quote_items.buyer_quote_id','=',$quoteid[0]->buyer_quote_id)
							->select('airint_buyer_quote_items.*','lkp_load_types.load_type','lkp_packaging_types.packaging_type_name')
							->get();
							$getInitialQuotePrice = CommonComponent::getPTLQuotePriceForSearch($buyer_id,$quoteid[0]->buyer_quote_id,Auth::user()->id,'initial_quote_price','airint_buyer_quote_sellers_quotes_prices');
							$getCounterQuotePrice = CommonComponent::getPTLQuotePriceForSearch($buyer_id,$quoteid[0]->buyer_quote_id,Auth::user()->id,'counter_quote_price','airint_buyer_quote_sellers_quotes_prices');
							$getFinalQuotePrice   = CommonComponent::getPTLQuotePriceForSearch($buyer_id,$quoteid[0]->buyer_quote_id,Auth::user()->id,'final_quote_price','airint_buyer_quote_sellers_quotes_prices');
							//echo "<pre>";
							//print_r($getInitialQuotePrice);
							//exit;
                                                        //commented by swathi 02-05-2016 count increasing from ajax
                                                        /*$tableName = CommonComponent::getTableNameAsPerService(Session::get('service_id'));
                                                        if(!empty($tableName)){
                                                            CommonComponent::viewCountForBuyer(Auth::User()->id,$quoteid[0]->buyer_quote_id,$tableName);
                                                        }*/
                                                        //end comments
							if(!isset($getInitialQuotePrice[0]->initial_rate_per_kg) || $getInitialQuotePrice[0]->initial_rate_per_kg==''){
							$row->cells [5]->value .= '
							<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
							<button class="btn red-btn pull-right submit-data  underline_link seller_submit_quote" data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Submit Quote </button>
							
							</div>';}
							if(isset($getInitialQuotePrice[0]->initial_quote_price) && $getInitialQuotePrice[0]->initial_quote_price!='0.0000'
								&&	isset($getCounterQuotePrice[0]->counter_rate_per_kg) && 
									$getCounterQuotePrice[0]->counter_rate_per_kg ==''){
								$row->cells [5]->value .= '
							<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
							<button class="btn red-btn pull-right submit-data underline_link seller_submit_quote" data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Initial Quote Submitted </button>
							
							</div>';}
							if(isset($getCounterQuotePrice[0]->counter_rate_per_kg) && 
									$getCounterQuotePrice[0]->counter_rate_per_kg !=''
									&& isset($getFinalQuotePrice[0]->final_kg_per_cft) && $getFinalQuotePrice[0]->final_kg_per_cft==''){
							$row->cells [5]->value .= '
							<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
							<button  class="btn red-btn pull-right submit-data ltlsellesearchdetails_1  underline_link " data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Submit Final Quotes </button>
							
							</div>
							<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
							<button class="btn red-btn pull-right submit-data  ltlsellesearchdetails_2 underline_link " data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Accept Counter Offer </button>
							
							</div>';
							}
							if(isset($getCounterQuotePrice[0]->counter_rate_per_kg) &&
									$getCounterQuotePrice[0]->counter_rate_per_kg !=''
									&& isset($getFinalQuotePrice[0]->final_kg_per_cft) && $getFinalQuotePrice[0]->final_kg_per_cft!=''){
								$row->cells [5]->value .= '<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
								<button  class="btn red-btn pull-right submit-data ltlsellesearchdetails_1 underline_link " data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Final Quote Submitted </button>
								
								</div>';
								
							}
													
							$row->cells [5]->value .= '<div class="pull-right text-right">
									<div class="info-links">
										<span class="detailsslide underline_link" data-buyersearchlistid="'.$buyer_id.'_'.$buyer_quote_id.'">
											<span class="show_details" style="display: inline;">+</span>
											<span class="hide_details" style="display: none;">-</span>
											Details
										</span> 
										<a href="#" data-userid="'.$buyer_id.'" data-buyer-transaction="'.$transaction_id.'" class="new_message" data-buyerquoteitemidforseller="'.$bqid.'"><i	class="fa fa-envelope-o"></i></a></div>
									</div>	
								</div>
							<div class="clearfix"></div>
							<form id ="addptlsellersearchpostquoteoffer" name ="addptlsellersearchpostquoteoffer" class="formquoteid_'.$buyer_quote_id.'">';
							if(Session::get('session_delivery_date_ptl')=='')
								Session::put('session_delivery_date_ptl',$delivery_date_buyer);
							$row->cells [5]->value .= '<input type="hidden" id="serviceid" value="'.Session::get('service_id').'">';
							$row->cells [5]->value .='<input type="hidden" name="seller_post_item_id" id="seller_post_item_id" value="'.Session::get('seller_post_item').'">
							<input type="hidden" name="volumetric_'.$buyer_id.'_'.$buyer_quote_id.'" id="volumetric_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$buyerdetailsvalue[0]->calculated_volume_weight.'">
							<input type="hidden" name="packagenos_'.$buyer_id.'_'.$buyer_quote_id.'" id="packagenos_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$buyerdetailsvalue[0]->number_packages.'">
							<input type="hidden" name="units_'.$buyer_id.'_'.$buyer_quote_id.'" id="units_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$buyerdetailsvalue[0]->units.'">
							<input type="hidden" name="buyerquoteid" id="buyerquoteid" value="'.$quoteid[0]->buyer_quote_id.'">';
							if(isset($_REQUEST ['from_location_id']) && isset($_REQUEST ['to_location_id'])){
								if(!isset($_REQUEST['zone_or_location']))
									$_REQUEST['zone_or_location'] ='';
							$row->cells [5]->value .='
							<input type="hidden" name="zone_or_location" id="zone_or_location_change" value="'.$_REQUEST['zone_or_location'].'">
							<input type="hidden" name="from_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" id="from_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$from_zipcode.'">
							<input type="hidden" name="to_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" id="to_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$to_zipcode.'">';
							}
							
							$row->cells [5]->value .='
									
							<div class="col-md-12 show-data-div padding-none padding-top quote_details_1_'.$buyer_id.'_'.$buyer_quote_id.' margin-top" style="display:none">
							<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
								<div class="table pull-right">
								<h2 class="sub-head"><span class="from-head">'.$row->cells [3]->value.' to '.$row->cells [4]->value.'</span></h2>
								<div class="table-heading inner-block-bg">
										<div class="col-md-4 padding-left-none">Load type</div>
										<div class="col-md-2 padding-left-none">Package Type</div>
										<div class="col-md-2 padding-left-none">Volume</div>
										<div class="col-md-2 padding-left-none">Unit Weight</div>
										<div class="col-md-2 padding-left-none">No of Packages</div>
								</div>';
								for($i=0;$i<count($quoteitems);$i++){
								$row->cells [5]->value .= '<div class="table-data">
										<div class="table-row inner-block-bg">
											<div class="col-md-4 padding-left-none">'.$quoteitems[$i]->load_type.'</div>
									<div class="col-md-2 padding-left-none">'.$quoteitems[$i]->packaging_type_name.'</div>';
									$row->cells [5]->value .= '<div class="col-md-2 padding-left-none">'.round($quoteitems[$i]->calculated_volume_weight,4).' CCM </div>';
									
									if($quoteitems[$i]->lkp_ict_weight_uom_id ==2)
										$quoteitems[$i]->units = $quoteitems[$i]->units*0.001;
									if($quoteitems[$i]->lkp_ict_weight_uom_id ==3)
										$quoteitems[$i]->units = $quoteitems[$i]->units*1000;
									$row->cells [5]->value .= '<div class="col-md-2 padding-left-none">'.$quoteitems[$i]->units.' Kgs</div>
									<div class="col-md-2 padding-left-none">'.$quoteitems[$i]->number_packages.'</div>
											
									<input type="hidden" name="volumetric_'.$i.'" id="volumetric_'.$i.'" value="'.$quoteitems[$i]->calculated_volume_weight.'">
									<input type="hidden" name="units_'.$i.'" id="units_'.$i.'" value="'.$quoteitems[$i]->units.'">
									<input type="hidden" name="weighttype_'.$i.'" id="weighttype_'.$i.'" value="'.$quoteitems[$i]->lkp_ict_weight_uom_id.'">
									<input type="hidden" name="packagenos_'.$i.'" id="packagenos_'.$i.'" value="'.$quoteitems[$i]->number_packages.'">
										
											
											
								<div class="clear-fix"></div></div>
								</div>';
								}
							$row->cells [5]->value .= '
							<input type="hidden" name="incrementcount_'.$buyer_id.'_'.$buyer_quote_id.'" id="incrementcount_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$i.'">
							 <input type="hidden" name="buyerquoteid_'.$buyer_id.'_'.$buyer_quote_id.'" id="buyerquoteid_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$quoteid[0]->buyer_quote_id.'">										
							
									</div>
						</div></div>		
									
							<div class="col-md-12 padding-none submit-data-div quote_details_2_'.$buyer_id.'_'.$buyer_quote_id.'" style="display:none">
							<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
							<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
							<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-top ">
									<b>Seller Quote</b> 
									</div>';
									if(isset($getInitialQuotePrice[0]->initial_rate_per_kg) && $getInitialQuotePrice[0]->initial_rate_per_kg !='')
										$row->cells [5]->value .='
								<div class="col-md-3 padding-none"><span class="data-head">Rate per Kg </span> <span class="data-value"> Rs '.$getInitialQuotePrice[0]->initial_rate_per_kg.' /-</span>
										<input type="hidden" 
									name="initial_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="initial_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
									placeholder="Rate per Kg *" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal  margin-top" value="'.$getInitialQuotePrice[0]->initial_rate_per_kg.'" ></div>'; 
									else
										$row->cells [5]->value .= '
								<div class="col-md-3 col-sm-3 col-xs-6 padding-none"><input type="text" 
									name="initial_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="initial_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
									placeholder="Rate per Kg *" class="ptl_initial_rate_per_kg form-control form-control1 fourdigitstwodecimals_deciVal numberVal  margin-top" ></div>';
									
									
									
								
								if(isset($getInitialQuotePrice[0]->initial_kg_per_cft) && $getInitialQuotePrice[0]->initial_kg_per_cft!='')
									
									$row->cells [5]->value .='
									<div class="col-md-6 col-sm-6 col-xs-12 padding-none"><span class="data-head">'.$cft.' </span><span class="data-value"> '.$getInitialQuotePrice[0]->initial_kg_per_cft.' KG</span> 
									</div>';
										
									
								else{
								
									$row->cells [5]->value .= '
									<div class="col-md-3 col-sm-3 col-xs-6 padding-right-none">
									<input type="text"
									name="initial_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'"  
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="initial_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    placeholder="'.$cft.' *" class="ptl_initial_conversion form-control form-control1 fourdigitsfourdecimals_deciVal numberVal  margin-top" >';
									$row->cells [5]->value .='</div><div class="col-md-3 col-sm-3 col-xs-6 padding-right-none margin-top">
									<input type="hidden" id="calculatoropen" style="border:none;">
									</div>';
									
								}
								if(isset($getInitialQuotePrice[0]->initial_kg_per_cft) && $getInitialQuotePrice[0]->initial_kg_per_cft!=''){
									
								$row->cells [5]->value .= '<div class="clearfix"></div>
									<div class="col-md-12 col-sm-12 col-xs-12 padding-none">';
								
										$row->cells [5]->value .= '<div class="col-md-3 col-sm-3 col-xs-6 padding-none"><span class="data-head">Transit Days </span> <span class="data-value">'.$getInitialQuotePrice[0]->initial_transit_days.'</span></div>';
										$row->cells [5]->value .= '<div class="clearfix"></div>
										<div class="col-md-3 form-control-fld padding-none margin-top-none">
											<span class="data-head">Freight Amount </span><span class="data-value" >Rs '.CommonComponent::moneyFormat($getInitialQuotePrice[0]->initial_freight_amount,true).' /-</span>
										</div>
										<div class="col-md-3 form-control-fld padding-none margin-top-none">
											<span class="data-head">Total Amount </span><span class="data-value" >Rs '.CommonComponent::moneyFormat($getInitialQuotePrice[0]->initial_quote_price,true).' /-</span>
									
										</div>
									</div>';
									$row->cells [5]->value .= '<input type="hidden"
											name="initial_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="initial_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="Pickup Rs *" class="form-control form-control1 numberVal " value="'.$getInitialQuotePrice[0]->initial_pick_up_rupees.'">
													<input type="hidden"
											name="initial_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="initial_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="Delivery Rs *" class="form-control form-control1 numberVal " value="'.$getInitialQuotePrice[0]->initial_delivery_rupees.'">
													<input type="hidden"
											name="initial_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="initial_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="Pickup Rs *" class="form-control form-control1 numberVal " value="'.$getInitialQuotePrice[0]->initial_oda_rupees.'">
													<input type="hidden"
											name="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="Pickup Rs *" class="form-control form-control1 numberVal " value="'.$getInitialQuotePrice[0]->initial_transit_days.'">
													';
								}
								
								
								if(isset($getCounterQuotePrice[0]->counter_rate_per_kg) && $getCounterQuotePrice[0]->counter_rate_per_kg !='')
										$row->cells [5]->value .='
											<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-top form-group">
											<b>Buyer Counter Offer</b>  
											</div>
											<div class="col-md-3 padding-none"><span class="data-head">Rate per Kg </span> <span class="data-value"> Rs '.$getCounterQuotePrice[0]->counter_rate_per_kg.' /- </span></div>
													<input type="hidden"
											name="counter_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="counter_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="Conversion Kg/CCM *" class="form-control form-control1 fourdigitsthreedecimals_deciVal numberVal  margin-top"  value="'.$getCounterQuotePrice[0]->counter_rate_per_kg.'">
													'; 
									
								if(isset($getCounterQuotePrice[0]->counter_kg_per_cft) && $getCounterQuotePrice[0]->counter_kg_per_cft!=''){
									
										$row->cells [5]->value .='
										<div class="col-md-6 col-sm-6 col-xs-6 padding-none"><span class="data-head">'.$cft.' </span><span class="data-value"> '.$getCounterQuotePrice[0]->counter_kg_per_cft.' KG </span></div>
										<input type="hidden"
											name="counter_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="counter_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="Conversion Kg/CCM *" class="form-control form-control1 fourdigitsthreedecimals_deciVal numberVal  margin-top"  value="'.$getCounterQuotePrice[0]->counter_kg_per_cft.'">
											<div class="clearfix margin-none"></div>
											<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Freight Amount  </span><span class="data-value" >Rs '.CommonComponent::moneyFormat($getCounterQuotePrice[0]->counter_freight_amount,true).' /-</span>
												
											</div>
											<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Total Amount  </span><span class="data-value" >Rs '.CommonComponent::moneyFormat($getCounterQuotePrice[0]->counter_quote_price,true).' /-</span>
										
											</div>';
									
								}
								$row->cells [5]->value .='<div class="clearfix"></div>';
								$row->cells [5]->value .='<div class="hide-final">';
									if(isset($getFinalQuotePrice[0]->final_rate_per_kg) && $getFinalQuotePrice[0]->final_rate_per_kg !='')
										$row->cells [5]->value .='
												<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-top form-group">
								 					<b>Seller Final Quote</b>   
												</div>
										<div class="col-md-3 padding-none"><span class="data-head">Rate per Kg </span> <span class="data-value"> Rs '.$getFinalQuotePrice[0]->final_rate_per_kg.' /-</span></div>'; 
									elseif(isset($getFinalQuotePrice[0]->final_rate_per_kg) && $getFinalQuotePrice[0]->final_rate_per_kg =='' && $getInitialQuotePrice[0]->initial_rate_per_kg!='' &&  $getCounterQuotePrice[0]->counter_rate_per_kg!='')
										$row->cells [5]->value .= '
									<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-top form-group">
					 					<b>Seller Final Quote</b>   
									</div>
									<div class="col-md-3 col-sm-3 col-xs-6 padding-none">
									<input type="text" 
									name="final_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="final_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
									placeholder="Rate per Kg *" class="ptl_final_rate_per_kg form-control form-control1 fourdigitstwodecimals_deciVal numberVal  margin-top" ></div>';
									
								
								if(isset($getFinalQuotePrice[0]->final_kg_per_cft) && $getFinalQuotePrice[0]->final_kg_per_cft!=''){
									
										$row->cells [5]->value .='
										<div class="col-md-6 col-sm-6 col-xs-6 padding-none">
												<span class="data-head">'.$cft.' </span><span class="data-value"> '.$getFinalQuotePrice[0]->final_kg_per_cft.' KG</span>
														
										</div>';
									
								}
								elseif(isset($getFinalQuotePrice[0]->final_kg_per_cft) && $getFinalQuotePrice[0]->final_kg_per_cft=='' && $getInitialQuotePrice[0]->initial_kg_per_cft!=''  &&  $getCounterQuotePrice[0]->counter_rate_per_kg!='')
									
									$row->cells [5]->value .= '<div class="col-md-3 col-sm-3 col-xs-3 padding-right-none">
									<input type="text"
									name="final_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
                                    id="final_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="'.$cft.' *" class="ptl_final_conversion form-control form-control1 fourdigitsfourdecimals_deciVal numberVal  margin-top" ></div>
                                    <div class="col-md-3 padding-left-none form-control-fld padding-top ">
									<input type="hidden" id="calculatoropen" style="border:none;">
									</div>';
								
								$row->cells [5]->value .='<div class="clearfix"></div>';	
								
								$row->cells [5]->value .='
								<div class="col-md-3 col-sm-3 col-xs-6 padding-none">';
									if(isset($getInitialQuotePrice[0]->initial_transit_days) && $getInitialQuotePrice[0]->initial_transit_days=='')
										$row->cells [5]->value .= '<input type="text"
										name="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"  
	                                    id="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    placeholder="Transit Days *" class="form-control form-control1 numericvalidation" maxlength="2">';
								
									elseif(isset($getCounterQuotePrice[0]->counter_quote_price) && $getCounterQuotePrice[0]->counter_quote_price !='0.0000' &&  isset($getFinalQuotePrice[0]->final_quote_price) && $getFinalQuotePrice[0]->final_quote_price=='0.0000')
									$row->cells [5]->value .= '
									<input type="text"
                                    name="final_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
		                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
		                            id="final_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="Transit Days *" class="form-control form-control1 numericvalidation" maxlength="2">';
									elseif(!isset($getInitialQuotePrice[0]->initial_transit_days))
									$row->cells [5]->value .= '<input type="text"
									name="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
                                    id="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="Transit Days *" class="form-control form-control1 numericvalidation" maxlength="2">';
									$row->cells [5]->value .= '</div>';
									if(Session::get('service_id') == AIR_INTERNATIONAL || Session::get('service_id') == OCEAN){
										if(isset($getFinalQuotePrice[0]->final_freight_amount ) && $getFinalQuotePrice[0]->final_freight_amount!=''){
											$row->cells [5]->value .= '
												<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
													<div class="col-md-3 col-sm-3 col-xs-6 padding-none"><span class="data-head">Transit Days </span> <span class="data-value">'.$getFinalQuotePrice[0]->final_transit_days.'</span>
													</div>
												</div>';
									}
								}
								$row->cells [5]->value .= '</div>';
								$row->cells [5]->value .= '<div class="clearfix"></div>';
								if(!isset($getInitialQuotePrice[0]->initial_freight_amount) )
									$row->cells [5]->value .= '
												<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Freight Amount </span><span class="data-value" id="freight_charges_'.$buyer_id.'_'.$buyer_quote_id.'">Rs 0.00 /-</span>
									
											</div>
											<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Total Amount </span><span class="data-value" id="total_charges_'.$buyer_id.'_'.$buyer_quote_id.'">Rs 0.00 /-</span>
									
											</div>';
								elseif(isset($getInitialQuotePrice[0]->final_freight_amount) && $getInitialQuotePrice[0]->final_freight_amount=='' && isset($getCounterQuotePrice[0]->counter_freight_amount) && $getCounterQuotePrice[0]->counter_freight_amount!='')
									$row->cells [5]->value .= '
											<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Freight Amount </span><span class="data-value" id="freight_charges_'.$buyer_id.'_'.$buyer_quote_id.'">Rs 0.00 /- </span>
									
											</div>
											<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Total Amount </span><span class="data-value" id="total_charges_'.$buyer_id.'_'.$buyer_quote_id.'">Rs 0.00 /- </span>
									
											</div>';
								elseif(isset($getInitialQuotePrice[0]->final_freight_amount) && $getInitialQuotePrice[0]->final_freight_amount!='')
									$row->cells [5]->value .= '
														<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Freight Amount :  </span><span class="data-value" id="freight_charges_'.$buyer_id.'_'.$buyer_quote_id.'"> Rs '.CommonComponent::moneyFormat($getFinalQuotePrice[0]->final_freight_amount,true).' /-</span>
												
											</div>
											<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Total Amount  </span><span class="data-value" id="total_charges_'.$buyer_id.'_'.$buyer_quote_id.'">Rs '.CommonComponent::moneyFormat($getFinalQuotePrice[0]->final_quote_price,true).' /-</span>
												
											</div>';
									
								
								//Tracking
								if($tracking==''){
									$row->cells [5]->value .= '
											<div class="clearfix"></div>
												<div class="col-md-3 padding-left-none track-margin">
													<div class="normal-select">
														<select class="selectpicker"  id="tracking_'.$buyer_id.'_'.$buyer_quote_id.'" name="tracking_'.$buyer_id.'_'.$buyer_quote_id.'">
															<option value="">Tracking</option>
															<option value="1">'.TRACKING_MILE_STONE.'</option>';
                                                                        if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN){
                                                                            $row->cells [5]->value .= '<option value="2">'.TRACKING_REAL_TIME.'</option>';
                                                                        }
									$row->cells [5]->value .= '</select>
													</div>
												</div>';
								}
								else{
									$row->cells [5]->value .=
									'<div class="clearfix"></div>
												<div class="col-md-6 padding-none"><span class="data-head">Tracking : '.$tracking.'</span></div>
												<input type="hidden" name="tracking" id="tracking" value="'.$getSellerpost[0]->tracking.'">
													';
								}
								//Payment
								if($payment_type==''){
									$row->cells [5]->value .= '<div class="clearfix"></div>
												<div class="col-md-12 padding-none"><h2 class="filter-head1">Payment Terms</h2></div>
												<div class="col-md-3 padding-left-none track-margin margin-bottom">
													<div class="normal-select">
														<select class="selectpicker ptl_payment payment_options_'.$buyer_id.'_'.$buyer_quote_id.'" id="payment_options_'.$buyer_id.'_'.$buyer_quote_id.'" name="paymentterms_'.$buyer_id.'_'.$buyer_quote_id.'">
															<option value="1">Advance</option>';
                                                                        if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN){
                                                                            $row->cells [5]->value .= '<option value="2">Cash on Delivery</option>';
                                                                        }
									$row->cells [5]->value .= '<option value="3">Cash on Pickup</option>
															<option value="4">Credit</option>
														</select>
													</div>
												</div>
								
												<div class="col-md-12 padding-none" id ="show_advanced_period_'.$buyer_id.'_'.$buyer_quote_id.'">
													<div class="checkbox_inline">
														<input class="accept_payment_ptl" type="checkbox" name="accept_payment_ptl[]" id="accept_payment_ptl[]" value="1"><span class="lbl padding-8">NEFT/RTGS</span>
													</div>
													<div class="checkbox_inline">
														<input class="accept_payment_ptl" type="checkbox" name="accept_payment_ptl[]" value="2"><span class="lbl padding-8">Credit Card</span>
													</div>
													<div class="checkbox_inline">
														<input class="accept_payment_ptl" type="checkbox" name="accept_payment_ptl[]" value="3"><span class="lbl padding-8">Debit Card</span>
													</div>
												</div>
								
								
												<div class="col-md-12 form-control-fld padding-left-none" style ="display: none;" id = "show_credit_period_'.$buyer_id.'_'.$buyer_quote_id.'">
													<div class="col-md-3 form-control-fld padding-left-none">
								
													<div class="col-md-7 padding-none">
														<div class="input-prepend">
															<input class="form-control form-control1 numberVal credit_period_ptl_'.$buyer_id.'_'.$buyer_quote_id.'" type="text" name="credit_period_ptl_'.$buyer_id.'_'.$buyer_quote_id.'" id="credit_period_ptl" value="" placeholder="Credit Period"><span class="lbl padding-8">Credit Card</span>
														</div>
													</div>
													<div class="col-md-5 padding-none">
														<div class="input-prepend">
															<span class="add-on unit-days manage">
																<div class="normal-select">
																	<select class="selectpicker bs-select-hidden credit_period_units_'.$buyer_id.'_'.$buyer_quote_id.'"  id="credit_period_units" name="credit_period_units_'.$buyer_id.'_'.$buyer_quote_id.'">
																		<option value="Days">Days</option>
																		<option value="Weeks">Weeks</option>
																	</select>
						
																</div>
															</span>
														</div>
													</div>
								
								
													</div>
													<div class="col-md-12 padding-none">
														<div class="checkbox_inline" >
														<input class="accept_payment_ptl" type="checkbox" name="accept_credit_netbanking[]" value="1"><span class="lbl padding-8">Net Banking</span>
								
														</div>
														<div class="checkbox_inline">
														<input class="accept_payment_ptl" type="checkbox" name="accept_credit_netbanking[]" value="2"><span class="lbl padding-8">Cheque / DD</span>
														</div>
			
													</div>
												</div>';
								}else{
									$row->cells [5]->value .= '<div class="clearfix"></div>
												<div class="col-md-12 padding-none "><span class="data-head">Payment : '.$payment_type.'</span></div>
														  <input type="hidden" name="payment_options" id="payment_options" value="'.$payment_type.'">
														  <input type="hidden" name="credit_peroid" id="credit_peroid" value=" ">
														  <input type="hidden" name="credit_period_units" id="credit_period_units" value=" ">
														  ';
								}
								
								
								$row->cells [5]->value .= '</div><div class="clearfix"></div></div><div class="col-md-4 data-fld padding-none text-right pull-right">
										<div class="hide-submit">';
								if(isset($getInitialQuotePrice[0]->initial_freight_amount) && $getInitialQuotePrice[0]->initial_freight_amount!=''){
									if($getFinalQuotePrice[0]->final_freight_amount=='' && $getCounterQuotePrice[0]->counter_freight_amount!='')
									$row->cells [5]->value .= '<input id="ptl_final_quote_submit_'.$buyer_id.'_'.$buyer_quote_id.'" type="button" class="btn add-btn margin-top pull-right ptl_final_quote_submit  margin-bottom" value=" Submit " name='.$buyer_quote_id.'>';
								}
								else 
									$row->cells [5]->value .= '<input id="ptl_initail_quote_submit_'.$buyer_id.'_'.$buyer_quote_id.'" type="button" class="btn add-btn  margin-top pull-right ptl_initial_quote_submit margin-bottom" value=" Submit " name='.$buyer_quote_id.'>';
							$row->cells [5]->value .= '</div>';
							if(isset($getFinalQuotePrice[0]->final_freight_amount) && $getFinalQuotePrice[0]->final_freight_amount=='' && isset($getCounterQuotePrice[0]->counter_freight_amount) && $getCounterQuotePrice[0]->counter_freight_amount!=''){
									$row->cells [5]->value .= '<div class="show-submit">
									<input id="ptl_counter_quote_submit_'.$buyer_id.'_'.$buyer_quote_id.'" type="button" class="btn add-btn  margin-top pull-right ptl_counter_quote_submit margin-bottom" value=" Accept " name='.$buyer_quote_id.'>
									</div>';
							}
									$row->cells [5]->value .= '</div></form>';
			
			$data_link = url () . "/sellerposts/$buyer_quote_id";
			$row->attributes ( array (
					"class" => "col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none ",
					"data_link" => $data_link 
			) );
		} );
		
		// filter for buyear search list top dropdown lists---filters
		$filter = DataFilter::source ( $Query_buyers_for_sellers );
		
		
		 $filter->add ( 'lkp_packaging_type_id', 'Package Type', 'select')->options($packaging_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
		 $filter->add ( 'lkp_load_type_id', 'Load Type', 'select')->options($load_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
		
		//$filter->add ( 'bqi.dispatch_date', 'Dispatch Date', 'date' )->attr("class","filter_calendar")->attr("id","dispatch_filter_calendar");
		 //$filter->add ( 'bqi.delivery_date', 'Delivery Date', 'date' )->attr("class","filter_calendar")->attr("id","delivery_filter_calendar");
		 $filter->submit('search');
		 $filter->reset('reset');
		 $filter->build();
		
		$result = array ();
		$result ['grid'] = $grid;
		$result ['filter'] = $filter;
		// echo "<pre>";print_R($result);die;
		return $result;
	}
	
	/**
	 *
	 * ADD PTL ZONE
	 *
	 * @param $data(posted values)        	
	 *
	 */
	public static function addPtlZone($data) {
		
		// echo "<pre>";print_r($data);
		$ptlZone = new PtlZone ();
		
		$isUniqueZone = PtlSellerComponent::checkUniqueZone ( $data );
		if ($isUniqueZone == '1') {
			$createdAt = date ( 'Y-m-d H:i:s' );
			$createdIp = $_SERVER ["REMOTE_ADDR"];
			
			$ptlZone->seller_id = Auth::user ()->id;
			$ptlZone->zone_name = $data ['zone_name'];
			$ptlZone->zone_code = $data ['zone_code'];
			$ptlZone->is_active = '1';
			
                        $ptlZone->lkp_service_id = Session::get('service_id');
			$ptlZone->created_at = $createdAt;
			$ptlZone->created_ip = $createdIp;
			$ptlZone->created_by = Auth::user ()->id;
			try {
				if ($ptlZone->save ()) {
					return '1';
				} else {
					return '0';
				}
			} catch ( Exception $ex ) {
				return '0';
			}
		}
		else{
			
			return $isUniqueZone;
		}
	}
	
	/**
	 * Check if logged in Seller has used this Zone_code / Zone_name before or not
	 * 
	 * @param $data(posted values)        	
	 */
	public static function checkUniqueZone($data) {
		$isCodeExist = PtlZone::Where ( 'zone_code', $data ['zone_code'] )->Where ( 'seller_id', Auth::User()->id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();
		$isNameExist = PtlZone::Where ( 'zone_name', $data ['zone_name'] )->Where ( 'seller_id', Auth::User()->id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();
		
		if (!empty ( $isNameExist )) {
			return 'This name has been used, Please use another name.';
		} elseif (!empty ( $isCodeExist )) {
			return 'This code has been used, Please use another code.';
		} else {
			return '1';
		}
	}	
	/**
	 *
	 * ADD PTL TIER
	 *
	 * @param $data(posted values)
	 *
	 */
	public static function addPtlTier($data) {
	
		
		$ptlTier = new PtlTier ();
	
		$isUniqueTier = PtlSellerComponent::checkUniqueTier ( $data );
		if ($isUniqueTier == '1') {
			$createdAt = date ( 'Y-m-d H:i:s' );
			$createdIp = $_SERVER ["REMOTE_ADDR"];
				
			$ptlTier->seller_id = Auth::user ()->id;
			$ptlTier->tier_name = $data ['tier_name'];
			$ptlTier->tier_code = $data ['tier_code'];
			$ptlTier->is_active = '1';
			
                        $ptlTier->lkp_service_id = Session::get('service_id');
			$ptlTier->created_at = $createdAt;
			$ptlTier->created_ip = $createdIp;
			$ptlTier->created_by = Auth::user ()->id;
			try {
				if ($ptlTier->save ()) {
					$ptlTierId = $ptlTier->id;
					$tiersList =  PtlTier::where('seller_id',Auth::user ()->id)->select('id as tierId')->get();
				//echo '<pre>';print_r($tiersList);die();
				if($tiersList)
					foreach($tiersList as $tier){
					$ptlTransitDay = new PtlTransitday ();
					$ptlTransitDay->from_tier_id = $ptlTierId;
					$ptlTransitDay->to_tier_id = $tier->tierId;
					$ptlTransitDay->no_days = '';
					$ptlTransitDay->is_active = '1';
					
                                        $ptlTransitDay->lkp_service_id = Session::get('service_id');
					$ptlTransitDay->created_at = $createdAt;
					$ptlTransitDay->created_ip = $createdIp;
					$ptlTransitDay->created_by = Auth::user ()->id;
					$ptlTransitDay->save();
					
				}
					
					
				return '1';
					
					
				} else {
					return '0';
				}
			} catch ( Exception $ex ) {
				return '0';
			}
		}
		else{
				
			return $isUniqueTier;
		}
	}
	
	/**
	 * Check if logged in Seller has used this Tier_code / Tier name before or not
	 *
	 * @param $data(posted values)
	 */
	public static function checkUniqueTier($data) {
		$isCodeExist = PtlTier::Where ( 'tier_code', $data ['tier_code'] )->Where ( 'seller_id', Auth::User()->id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();
		$isNameExist = PtlTier::Where ( 'tier_name', $data ['tier_name'] )->Where ( 'seller_id', Auth::User()->id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();
		if (!empty ( $isNameExist )) {
			return 'This name has been used, Please use another name.';
		} elseif (!empty ( $isCodeExist )) {
			return 'This code has been used, Please use another code.';
		} else {
			return '1';
		}
	}
	
	
	/**
	 *
	 * ADD PTL SECTOR
	 *
	 * @param $data(posted values)
	 *
	 */
	public static function addPtlSector($data) {
	
		// echo "<pre>";print_r($data);
		$ptlSector = new PtlSector ();
	
		$isUniqueSector = PtlSellerComponent::checkUniqueSector ( $data );
		if ($isUniqueSector == '1') {
			$createdAt = date ( 'Y-m-d H:i:s' );
			$createdIp = $_SERVER ["REMOTE_ADDR"];
				
			$ptlSector->seller_id = Auth::user ()->id;
			$ptlSector->sector_name = $data ['sector_name'];
			$ptlSector->sector_code = $data ['sector_code'];
			$ptlSector->ptl_zone_id = $data ['zone_id'];
			$ptlSector->ptl_tier_id = $data ['tier_id'];
			$ptlSector->is_active = '1';
                        $ptlSector->lkp_service_id = Session::get('service_id');
			$ptlSector->created_at = $createdAt;
			$ptlSector->created_ip = $createdIp;
			$ptlSector->created_by = Auth::user ()->id;
			try {
				if ($ptlSector->save ()) {
					return '1';
				} else {
					return '0';
				}
			} catch ( Exception $ex ) {
				return '0';
			}
		}
		else{
				
			return $isUniqueSector;
		}
	}
	
	/**
	 * Check if logged in Seller has used this sector_code / sector_name before or not
	 *
	 * @param $data(posted values)
	 */
	public static function checkUniqueSector($data) {
		$isCodeExist = PtlSector::Where ( 'sector_code', $data ['sector_code'] )->Where ( 'seller_id', Auth::User()->id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();
		$isNameExist = PtlSector::Where ( 'sector_name', $data ['sector_name'] )->Where ( 'seller_id', Auth::User()->id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();
		if (!empty ( $isNameExist )) {
			return 'This name has been used, Please use another name.';
		} elseif (!empty ( $isCodeExist )) {
			return 'This code has been used, Please use another code.';
		} else {
			return '1';
		}
	}
	

	/**
	 * check zone name and code uniqueness while nline grid editting
	 */
	
	public static function checkUniqueZoneCode($data) {
		$isCodeExist = PtlZone::Where ( 'zone_code', $data ['zone_code'] )->Where ( 'seller_id', Auth::User()->id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();
	
		if (!empty ( $isCodeExist )) {
			return 'This code has been used, Please use another code.';
		} else {
			return '1';
		}
	}
	public static function checkUniqueZoneName($data) {

		$isNameExist = PtlZone::Where ( 'zone_name', $data ['zone_name'] )->Where ( 'seller_id', Auth::User()->id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();
		
		if (!empty ( $isNameExist )) {
			return 'This name has been used, Please use another name.';
		} else {
			return '1';
		}
	}
	
	/**
	 * check tier name and code uniqueness while nline grid editting
	 */
	
	public static function checkUniqueTierCode($data) {
		$isCodeExist = PtlTier::Where ( 'tier_code', $data ['tier_code'] )->Where ( 'seller_id', Auth::User()->id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();
	
		if (!empty ( $isCodeExist )) {
			return 'This code has been used, Please use another code.';
		} else {
			return '1';
		}
	}
	public static function checkUniqueTierName($data) {
	
		$isNameExist = PtlTier::Where ( 'tier_name', $data ['tier_name'] )->Where ( 'seller_id', Auth::User()->id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();
	
		if (!empty ( $isNameExist )) {
			return 'This name has been used, Please use another name.';
		} else {
			return '1';
		}
	}
	/**
	 * check sector name and code uniqueness while nline grid editting
	 */
	
	public static function checkUniqueSectorCode($data) {
		$isCodeExist = PtlSector::Where ( 'sector_code', $data ['sector_code'] )->Where ( 'seller_id', Auth::User()->id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();
	
		if (!empty ( $isCodeExist )) {
			return 'This code has been used, Please use another code.';
		} else {
			return '1';
		}
	}
	public static function checkUniqueSectorName($data) {
	
		$isNameExist = PtlSector::Where ( 'sector_name', $data ['sector_name'] )->Where ( 'seller_id', Auth::User()->id )->where ( 'lkp_service_id', Session::get('service_id') )->first ();
	
		if (!empty ( $isNameExist )) {
			return 'This name has been used, Please use another name.';
		} else {
			return '1';
		}
	}
	
}
