<?php

namespace App\Components\RelocationInt\AirInt;

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
use App\Components\RelocationInt\AirInt\RelocationAirSellerComponent;

class RelocationAirBuyerComponent {
	
	
    
    /**
	* Relocation pet move posts list grid with filter options
	* @author Shriram
	* @return count
	*/
	public static function getBuyerPetmoveQuoteCount($buyer_post_id, $service_id){
		$rows = \App\Models\RelocationpetBuyerQuoteSellersQuotesPrice::selectRaw("count(*) as totRows")
			->where('total_price', '!=', '0.00')
			->where([
				'buyer_id' => Auth::id(),
				'buyer_quote_id' => $buyer_post_id,
				'lkp_service_id' => $service_id
			])->first();
		return $rows->totRows;
	}

    
        
	/**
	* Relocation int air seller search posts
	* @author Shriram
	* @return Grid, Filter
	*/
	public static function getRelocationIntAirBuyerSearchResults( $request, $serviceId, $totReqWeight = 0) {
		try {
			
			$prices = array();
            $sellerNames = array();
            $paymentMethods = array ();

                        
            $request['trackingfilter'] = array();
            if (isset ( $request['tracking'] ) && $request['tracking']!= '') {
                $request['trackingfilter'][] = $request['tracking'];
            }
            if (isset ( $request ['tracking1'] ) && $request ['tracking1'] != '') {
                $request['trackingfilter'][] = $request['tracking1'];
            }

            // Getting Slab Id based on Total weight
            $slabArr = array();
            global $slabInfo;
            if($totReqWeight != 0){
            	$slab = DB::table('lkp_air_weight_slabs as spi' );		
				$slab->whereRaw("$totReqWeight between min_slab_weight and max_slab_weight");
				$slabInfo = $slab->first();
				if(isset($slabInfo->id)):
					$request['slab_id'] = $slabInfo->id;
					$request['weight'] = $totReqWeight;
					$slabArr['slab_status'] = 1;
				else:
					$request['slab_id'] = '';
					$request['weight'] = 0;
					
					//temp Obj
					$slabInfo = new \stdClass;
					$slabInfo->min_slab_weight = '';
					$slabInfo->max_slab_weight = '';
					$slabArr['slab_status'] = 0;

				endif;	
            }else{
            	$slabArr['slab_status'] = 0;
            } 

			$Query_buyers_for_sellers = BuyerSearchComponent::search($roleId=null, $serviceId, $statusId=null, $request );
			$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();	
			//echo "<pre>"; print_r($Query_buyers_for_sellers_filter); die;

			Session::put('relocbuyerrequest', $request->all);
                        
			if(isset($_REQUEST['from_location']) && $_REQUEST['from_location'] && isset($_REQUEST['to_location']) && $_REQUEST['to_location']!='' && isset($_REQUEST['from_date']) && $_REQUEST['from_date'] )
			{
				
				
				$c1 = (int)$_REQUEST['cartons_1'];
				$c2 = (int)$_REQUEST['cartons_2'];
				$c3 = (int)$_REQUEST['cartons_3'];
				$cartoncount = $c1 + $c2 + $c3;
				
				session()->put([
					'searchMod' => [
						'delivery_date_buyer' => $request->to_date,
						'dispatch_date_buyer' => $request->from_date,				
						'from_city_id_buyer'  => $request->from_location_id,
						'to_city_id_buyer'    => $request->to_location_id,
						'from_location_buyer' => $request->from_location,
						'to_location_buyer'   => $request->to_location,
						'volume_buyer'        => RelocationAirSellerComponent::getCFTfromweight($totReqWeight),
						'weight_buyer'        => $totReqWeight,
						'service_type_buyer'  => $request->post_type,
						'cartons_count_buyer' => $cartoncount,
						'cartons_1'           => $request->cartons_1,
						'cartons_2'           => $request->cartons_2,
						'cartons_3'           => $request->cartons_3,
						'dispatch_flexible_hidden' => $request->dispatch_flexible_hidden,
						'delivery_flexible_hidden' => $request->delivery_flexible_hidden
					]
				]);
			}

			//Save Data in sessions			
			if (empty ( $Query_buyers_for_sellers_filter )) {
				//CommonComponent::searchTermsSendMail ();				
				Session::put('layered_filter', '');
				Session::put('layered_filter_payments', '');
				Session::put('show_layered_filter','');
			}
			
			// Below script for filter data getting from queries --for filters
            if(!isset($_REQUEST['filter_set'])){
				foreach ( $Query_buyers_for_sellers_filter as $seller_post_item ) {
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
            
			if(!empty($prices) && isset($request['is_search']) && !isset($_REQUEST['filter_set'])){
				$_REQUEST['price_from'] = 0;
				$_REQUEST['price_to'] = max($prices);
			}

            $Query_buyers_for_sellersnew = array();
            foreach($result as $Query_buyers_for_seller){
                $cftfromweight = RelocationAirSellerComponent::getCFTfromweight($Query_buyers_for_seller->weight);
				$resp = ($cftfromweight * $Query_buyers_for_seller->od_charges) + ($Query_buyers_for_seller->weight * $Query_buyers_for_seller->freight_charges);
				$prices[] = $resp;
				$Query_buyers_for_seller->newprice = isset($resp) ? $resp : 0;
				$Query_buyers_for_seller->cftfromweight = isset($cftfromweight) ? $cftfromweight : 0;
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
            if (empty ( $result )) {
                Session::put('show_layered_filter','');
            }
			



                    
                        
			//echo "<pre>";print_R($_REQUEST);print_R($result);die;
			$gridBuyer = DataGrid::source ( $result );
			
			$gridBuyer->add('username', 'Name', true )->attributes(array("class" => "col-md-4 padding-left-none"));
            $gridBuyer->add('dispatch_date', 'Total (Rs)', true)->attributes(array("class" => "col-md-4 padding-left-none"));
            $gridBuyer->add('grid_actions', 'Grid Actions', true)->attributes(array("class" => "col-md-4 padding-none"))->style("display:none");
            
            $gridBuyer->add('empty_div_1', 'clearFix', true)->attributes(array("class" => ""))->style("display:none");
            $gridBuyer->add('empty_div_2', 'PullLeftTracking', true)->style("display:none");
            $gridBuyer->add('empty_div_3', 'PullRightDetails', true)->style("display:none");
            $gridBuyer->add('empty_div_4', 'Details Dom Action', true)->style("display:none");

			$gridBuyer->row ( function ($row) {
				
				global $slabInfo;
				$weight = $row->data->weight;
				$cftfromweight = $row->data->cftfromweight;//RelocationAirSellerComponent::getCFTfromweight($weight);

				//Total CFT charges=  (CFT conversion from KGs) *(O&D charges/CFT)
				//Total Freight Charges = (Freight charges/KG)* (Weight)
				$price = $row->data->newprice;//($cftfromweight * $row->data->od_charges) + ($weight * $row->data->freight_charges);

				// Additional variables	
				$id = $row->data->id;
                $postid = $row->data->postid;
                $buyer_id=Auth::User ()->id;
				$tracking_text = CommonComponent::getTrackingType($row->data->tracking);
                //$post_type = ($row->data->lkp_access_id ==1)? 'Public':'Private';
                $post_type = CommonComponent::getQuoteAccessById($row->data->lkp_access_id);

                // Seller Business name
                $row->cells[0]->attributes(array('class' => 'col-md-4 padding-left-none'))
                        ->value( ucfirst($row->data->username) );

                // Display Total
                $row->cells[1]->attributes(array('class' => 'col-md-4 padding-left-none'))
                        ->value( "$price /-" );

                $totalAmount = $price;
                        
                // Book now button code: Start
                $row->cells[2]->attributes(array('class' => 'col-md-4 padding-none'))
                        ->value = "
                        <form name='addptlbuyersearchbooknow_$postid' id='addptlbuyersearchbooknow_$postid' action='".url('buyerbooknowforsearch/'.$postid)."' role='form' method='GET'>
							<div class='volume_calc'>
							<input type='submit' value='Book Now' class='btn red-btn pull-right buyer_book_now' />
							<input id='buyersearch_booknow_buyer_id_$postid' value='$buyer_id' name='buyersearch_booknow_buyer_id_$postid' type='hidden'>
						    <input id='buyersearch_booknow_seller_id_$postid' value='".$row->data->seller_id."' name='buyersearch_booknow_seller_id_$postid' type='hidden'>
							<input id='buyersearch_booknow_seller_price_$postid' value='".$totalAmount."' name='buyersearch_booknow_seller_price_$postid' type='hidden'>
							<input id='buyersearch_booknow_from_date_$postid' value=".CommonComponent::convertDateForDatabase($row->data->from_date)." name='buyersearch_booknow_from_date_$postid' type='hidden'>
							<input id='buyersearch_booknow_to_date_$postid' value=".CommonComponent::convertDateForDatabase($row->data->to_date)." name='buyersearch_booknow_to_date_$postid' type='hidden'>
							<input id='buyersearch_booknow_dispatch_date_$postid' value=".CommonComponent::convertDateForDatabase($row->data->from_date)." name='buyersearch_booknow_dispatch_date_$postid' type='hidden'>
							<input id='buyersearch_booknow_delivery_date_$postid' value=".CommonComponent::convertDateForDatabase($row->data->to_date)." name='buyersearch_booknow_delivery_date_$postid' type='hidden'>
							</div>
						</form>";
				// Book now button code: End

				// Clear fix div
                $row->cells[3]->attributes(array('class' => 'clearfix'))->value('');

                // Pull left tracking, Online payment links       
                $row->cells[4]->attributes(array('class' => 'pull-left'))
                	->value = '<div class="info-links">
						<a href="#"><i class="fa fa-map-o"></i> Tracking</a>
						<a href="#"><i class="fa fa-credit-card"></i> Online Payment</a>
						<a href="#"><i class="fa fa-rupee"></i> Cash on Delivery / Pickup</a>
					</div>';

				// Pull Right Details & Mesage link
                $row->cells[5]->attributes(array('class' => 'pull-right text-right'))
                	->value = '<div class="info-links">
					<span id="'.$id.'" class="viewcount_show-data-link view_count_update" data-quoteId="'.$id.'"><span class="show-icon">+</span><span class="hide-icon">-</span> Details</span>
					<a href="#" class="new_message" data-transaction_no="'.$row->data->transaction_id.'" data-userid="'.$row->data->seller_id.'" data-buyerquoteitemid="'.$id.'"><i class="fa fa-envelope-o"></i></a>
				</div>';

				// Details Dom Code
                $row->cells[6]->attributes(array('class' => 'col-md-12 show-data-div padding-top break-word'))
                	->value = '<div class="table-div table-style1 padding-none margin-none">
								<div class="table-heading inner-block-bg">
									<div class="col-md-3 padding-left-none break-word">Weight Bracket (KGs)</div>
									<div class="col-md-3 padding-left-none break-word">Transit Days</div>
									<div class="col-md-3 padding-left-none break-word">Freight Charges (Rs/KG)</div>
									<div class="col-md-3 padding-none break-word">O &amp; D Charges (Rs/CFT)</div>
								</div>
								<div class="table-data">
									<div class="table-row inner-block-bg">
										<div class="col-md-3 padding-left-none break-all">'.e($slabInfo->min_slab_weight).'-'.e($slabInfo->max_slab_weight).'</div>
										<div class="col-md-3 padding-left-none break-word">'.$row->data->transitdays.' Days</div>
										<div class="col-md-3 padding-left-none break-word">'.$row->data->freight_charges.' /-</div>
										<div class="col-md-3 padding-left-none break-word">'.$row->data->od_charges.' /-</div>
									</div>
								</div>
							</div>
						<div class="clearfix"></div>
						<div class="col-md-3 form-control-fld padding-left-none break-word">
							<span class="data-head">Tracking</span>
							<span class="data-value break-all">'.$tracking_text.'</span>
						</div>
						<div class="col-md-3 form-control-fld padding-left-none break-word">
							<span class="data-head">Payment</span>
							<span class="data-value break-word">'.$row->data->payment_mode.'</span>
						</div>
						<div class="col-md-3 form-control-fld padding-left-none break-word">
							<span class="data-head">Post Type</span>
							<span class="data-value break-all">'.$post_type.'</span>
						</div>
						<div class="col-md-12 form-control-fld padding-left-none break-word">
							<span class="data-head">Terms &amp; Conditions</span>
							<span class="data-value break-word">'.e($row->data->terms_conditions).'</span>
						</div>
						<div class="clearfix"></div>
						<div class="col-md-3 padding-left-none break-word">
							<span class="data-head"><u>Additional Charges</u></span>
						</div>
						<div class="clearfix"></div>
						<div class="col-md-3 padding-left-none break-word">
							<span class="data-head">Storage Charges (Rs) : '.e($row->data->storage_charge_price).'/-</span>
						</div>
						<div class="col-md-3 padding-left-none break-word">
							<span class="data-head">Cancellation Charges (Rs) : '.e($row->data->cancellation_charge_price).'/-</span>
						</div>
						<div class="col-md-3 padding-left-none break-word">
							<span class="data-head">Other Charges (Rs) : '.e($row->data->other_charge_price).'/-</span>
						</div>';

				$row->attributes(array("class" => ""));
						
			} );
			
			$gridBuyer->orderBy ( 'id', 'desc' );
			$gridBuyer->paginate ( 5 );

			$result = array ();
			$result ['gridBuyer'] = $gridBuyer;
			$result ['slab_status'] = $slabArr['slab_status'];
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

	/**
	* Getting buyer pet move post details & seller list details
	* @author Shriram
	*/
	public static function getBuyerPostDetails($buyer_post_id, $serviceId=null,$roleid=null,$comparisonType=null,$sellerIds=null) {
			
		$objPetmove = new \App\Models\RelocationPetBuyerPost();
		$buyer_post_details = $objPetmove->getPetmovePostDetails(Auth::user()->id, $buyer_post_id);
		
		$qryQuotePrices = DB::table ('relocationpet_buyer_quote_sellers_quotes_prices as rsqb' )
			->leftjoin('users as u', 'u.id', '=', 'rsqb.seller_id')
			->leftjoin('relocationpet_seller_posts as sp', 'sp.id', '=', 'rsqb.seller_post_id')
                        ->leftjoin('relocationpet_seller_post_items as spi', 'sp.id', '=', 'spi.seller_post_id')
                        ->where( 'rsqb.buyer_quote_id', $buyer_post_id)
                        ->where( 'rsqb.total_price', '!=', '0.00');
		
		if($comparisonType==1)
			$qryQuotePrices->orderBy('rsqb.transit_days');
		
		if($comparisonType==2)
			$qryQuotePrices->orderBy('rsqb.total_price');

		if(count($sellerIds)!=0):
			$sellerIds= explode(",",$sellerIds);
			$qryQuotePrices->whereIn( 'rsqb.seller_id', $sellerIds);			
		endif;

		$sellerResults = $qryQuotePrices->select ('sp.from_date','spi.lkp_cage_type_id','sp.to_date','sp.transaction_id as transaction_no', 'rsqb.*', 'u.username','spi.id as seller_post_item_id')->get();
		
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
		
		return [
			'postDetails' => $buyer_post_details,
			'sellerResults' => $sellerResults			
		];
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
	
        
        /**
	 * Buyer Orders Detail Page in Relocation pet Page
	 * Retrieval of data related to Buyer Orders
	 *
	 */
	public static function getRelocationPetBuyerOrderDetails($serviceId, $orderId, $user_id) {
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
			 case RELOCATION_PET_MOVE :
                                $query->leftJoin('lkp_cities as lc', 'lc.id', '=', 'orders.from_city_id');
                                $query->leftJoin('lkp_cities as lcity', 'lcity.id', '=', 'orders.to_city_id');			 		
                                $query->leftJoin('relocationpet_buyer_posts as rbq', 'rbq.id', '=', 'orders.buyer_quote_id');  
                                $query->leftJoin('lkp_pet_types as lkpt', 'lkpt.id', '=', 'rbq.lkp_pet_type_id');                                
                                $query->leftJoin('lkp_cage_types as lkct', 'lkpt.id', '=', 'rbq.lkp_cage_type_id'); 
                                $query->leftJoin('lkp_breed_types as lkbt', 'lkbt.id', '=', 'rbq.lkp_breed_type_id'); 
                                $query->leftjoin('users as u', 'u.id', '=', 'orders.seller_id')->where('orders.id', '=', $orderId);             		
                                $query->where('orders.buyer_id', '=', $user_id);             		
                                $orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', 'op.*', 
                                        'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lkp_payment_modes.payment_mode', 'oi.total_amt as inv_total','rbq.transaction_id as postid', 'lkpt.pet_type', 'lkct.cage_type', 'lkbt.breed_type')->first();
             		
		            break;
					}
			return $orders;
			 
		} catch ( Exception $exc ) {
			
		}
		
	}
}