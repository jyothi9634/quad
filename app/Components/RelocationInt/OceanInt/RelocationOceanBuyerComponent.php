<?php

namespace App\Components\RelocationInt\OceanInt;

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
use App\Components\MessagesComponent;

class RelocationOceanBuyerComponent {
	
	/**
	* Relocation int air seller search posts
	* @author Shriram
	* @return Grid, Filter
	*/

    public static function getVolumeInCBM($volume){
        return number_format($volume/35.5,2);
    }

	public static function getRelocationIntOceanBuyerSearchResults( $request, $serviceId) {
        //echo "<pre>";dd($request);die;
		try {
			
			$prices = array();
            $sellerNames = array();
            $paymentMethods = array ();

			$request->trackingfilter = array();
            if (isset ( $requesttracking ) && $request->tracking!= '') {
                $request->trackingfilter[] = $request->tracking;
            }
            if (isset ( $request->tracking1 ) && $request->tracking1 != '') {
                $request->trackingfilter[] = $request->tracking1;
            }


            // Getting property Info based on property type
            global $propertyInfo;
           
            	$q = DB::table('lkp_property_types')->where("id",'=', (int)$request->property_type);
				$propertyInfo = $q->first();
                $volume = $propertyInfo->volume;
          
            $volumeincbm = RelocationOceanBuyerComponent::getVolumeInCBM($volume);

            if($volumeincbm != 0){
                $shipmenttype = DB::table('lkp_relocation_shipment_volumes as spi' );
                $shipmenttype->whereRaw("$volumeincbm between min_volume and max_volume");
                $shipmenttypeInfo = $shipmenttype->first();
                $request['shipment_volume_type_id'] = $shipmenttypeInfo->id;
                $request['volume'] = $volumeincbm;
            }

            $Query_buyers_for_sellers = BuyerSearchComponent::search(
            	$roleId=null, $serviceId, $statusId=null, $request 
            );

			$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();	
			//echo "<pre>"; print_r($_REQUEST); die;

			Session::put('relocbuyerrequest', $request->all);
                       // echo "<pre>";echo Session::get('relocbuyerrequest')['chkOrgServ'];exit;
			

				if(!isset($request->chkOrgServ)){
					$request->chkOrgServ ='';
				}
				if(!isset($request->origin_handy_serivce)){
					$request->origin_handy_serivce ='';
				}
				if(!isset($request->insurance_serivce)){
					$request->insurance_serivce ='';
				}
				if(!isset($request->destination_storage_serivce)){
					$request->destination_storage_serivce ='';
				}
				if(!isset($request->destination_handy_serivce)){
					$request->destination_handy_serivce ='';
				}
				
				session()->put([
						'searchMod' => [
								'delivery_date_buyer' 		 => $request->valid_to,
								'dispatch_date_buyer' 		 => $request->valid_from,				
								'from_city_id_buyer'  		 => $request->from_location_id_intre,
								'to_city_id_buyer'    		 => $request->to_location_id_intre,
								'from_location_buyer' 		 => $request->from_location_intre,
								'to_location_buyer'    		 => $request->to_location_intre,
								'property_type_buyer' 		 => $request->property_type,
								'chkOrgServ'          		 => $request->chkOrgServ,
								'origin_handy_serivce'		 => $request->origin_handy_serivce,
								'insurance_serivce'          => $request->insurance_serivce,
								'destination_storage_serivce'=> $request->destination_storage_serivce,
								'destination_handy_serivce'  => $request->destination_handy_serivce,
								'post_type_buyer'            => $request->post_type,
								'service_type_buyer'		 => $request->post_type
				
						]
				]);
				
				
             
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


            if (empty ( $Query_buyers_for_sellers ) && !isset($request->filter_set)) {
                CommonComponent::searchTermsSendMail ();
            }
            $result = $Query_buyers_for_sellers->get ();
            //echo "<pre>";print_R($_REQUEST);print_R($result);//die;
			
			$Query_buyers_for_sellersnew = array();
            foreach($result as $Query_buyers_for_seller){
                if(isset($request->total_hidden_volume) && !empty($request->total_hidden_volume)){
                    $volumeinCBM = RelocationOceanBuyerComponent::getVolumeInCBM($request->total_hidden_volume);
                }else{
                    $volumeinCBM = RelocationOceanBuyerComponent::getVolumeInCBM($propertyInfo->volume);
                }
                Session::put('session_ocean_search_volume',$volumeinCBM);
                Session::put('session_ocean_search_no_of_items',(int)request('tot_items'));

                if($volumeinCBM <= 10){
                    $resp = ($volumeinCBM * $Query_buyers_for_seller->od_charges) + ($volumeinCBM * $Query_buyers_for_seller->freight_charges);
                }else{
                    $resp = ($volumeinCBM * $Query_buyers_for_seller->od_charges) + $Query_buyers_for_seller->freight_charges;
                }
                $prices[] = $resp;
                $Query_buyers_for_seller->volumeincbm = isset($volumeinCBM) ? $volumeinCBM : 0;
                $Query_buyers_for_seller->newprice = isset($resp) ? $resp : 0;
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
            
			$gridBuyer = DataGrid::source ( $result );
			
			$gridBuyer->add('property_type', 'Property Type', true )->attributes(array("class" => "col-md-2 padding-left-none"));
            $gridBuyer->add('volume', 'Volume', true)->attributes(array("class" => "col-md-2 padding-left-none"));
            $gridBuyer->add('no_of_items', 'No of Items', true)->attributes(array("class" => "col-md-2 padding-left-none"));
            $gridBuyer->add('tranasct_days', 'Transit Days', true)->attributes(array("class" => "col-md-2 padding-left-none"));
            $gridBuyer->add('total', 'Total', true)->attributes(array("class" => "col-md-2 padding-left-none"));
            
            $gridBuyer->add('empty_div', 'Grid Booknow', true)->attributes(array("class" => "col-md-2 padding-left-none"))->style('display:none');
            $gridBuyer->add('empty_div_1', 'clearFix', true)->attributes(array("class" => ""))->style("display:none");
            $gridBuyer->add('empty_div_2', 'PullLeftTracking', true)->style("display:none");
            $gridBuyer->add('empty_div_3', 'PullRightDetails', true)->style("display:none");
            $gridBuyer->add('empty_div_4', 'Details Dom Action', true)->style("display:none");

			$gridBuyer->row ( function ($row) {
				
				global $propertyInfo;

				// Additional variables	
				$id = $row->data->id;
                $postid = $row->data->postid;
                $buyer_id=Auth::User ()->id;


                $tracking_text = CommonComponent::getTrackingType($row->data->tracking);
                


                //$post_type = ($row->data->lkp_access_id ==1)? 'Public':'Private';
                $post_type = CommonComponent::getQuoteAccessById($row->data->lkp_access_id);

                // Seller Business name
                $row->cells[0]->attributes(array('class' => 'col-md-2 padding-left-none p'.$postid))
                        ->value( $propertyInfo->property_type );

                // Volume
                /*if(isset($_REQUEST['total_hidden_volume']) && !empty($_REQUEST['total_hidden_volume'])){
                    $volumeinCBM = RelocationOceanBuyerComponent::getVolumeInCBM($_REQUEST['total_hidden_volume']);
                }else{
                    $volumeinCBM = RelocationOceanBuyerComponent::getVolumeInCBM($propertyInfo->volume);
                }*/
                $row->cells[1]->attributes(array('class' => 'col-md-2 padding-left-none'))
                        ->value( $row->data->volumeincbm. "CBM");

				// No of items
                $row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))
                        ->value( (int)request('tot_items') );

				// Transit Days
                $row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))
                        ->value( $row->data->transitdays );
				
				// Total
                /*if($volumeinCBM <= 10){
                    $price = ($volumeinCBM * $row->data->od_charges) + ($volumeinCBM * $row->data->freight_charges);
                }else{
                    $price = ($volumeinCBM * $row->data->od_charges) + $row->data->freight_charges;
                }*/


                $row->cells[4]->attributes(array('class' => 'col-md-2 padding-left-none'))
                        ->value( $row->data->newprice . " /-" );
                 
                $totalAmount = $row->data->newprice;
                // Book now button code: Start
                $row->cells[5]->attributes(array('class' => 'col-md-2 padding-none'))
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
                $row->cells[6]->attributes(array('class' => 'clearfix'))->value('');

                // Pull left tracking, Online payment links       
                $row->cells[7]->attributes(array('class' => 'pull-left'))
                	->value = '<div class="info-links">
						<a href="#"><i class="fa fa-map-o"></i> Tracking</a>
						<a href="#"><i class="fa fa-credit-card"></i> Online Payment</a>
						<a href="#"><i class="fa fa-rupee"></i> Cash on Delivery / Pickup</a>
					</div>';

				// Pull Right Details & Mesage link
                $row->cells[8]->attributes(array('class' => 'pull-right text-right'))
                	->value = '<div class="info-links">
					<a id="'.$id.'" class="viewcount_show-data-link view_count_update" data-quoteId="'.$id.'"><span class="show-icon">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-transaction_no="'.$row->data->transaction_id.'" data-userid="'.$row->data->seller_id.'" data-buyerquoteitemid="'.$id.'"><i class="fa fa-envelope-o"></i></a>
				</div>';

				// Storage Settings
				$origin_storage = ($row->data->origin_storage)? 'checked="checked"':'';
				$destination_storage  = ($row->data->destination_storage)? 'checked="checked"':'';
				$origin_handyman_services  = ($row->data->origin_handyman_services)? 'checked="checked"':'';
				$destination_handyman_services  = ($row->data->destination_handyman_services )? 'checked="checked"':'';
				$unloading_delivery_unpack  = ($row->data->unloading_delivery_unpack )? 'checked="checked"':'';

				// Details Dom Code
                $frighttext = ($row->data->volumeincbm <= 10) ? "per CBM" : "FLAT";
                $row->cells[9]->attributes(array('class' => 'col-md-12 show-data-div padding-top break-word'))->value = '<div class="col-md-3 padding-left-none">
									<span class="data-value">Shipment Type : '.$row->data->shipment_type.'</span>
								</div>
								<div class="col-md-3 padding-left-none">
									<span class="data-value">O &amp; D Charges (per CBM) : '.$row->data->od_charges.'/-</span>
								</div>
								<div class="col-md-3 padding-left-none">
									<span class="data-value">Freight ('.$frighttext.') : '.$row->data->freight_charges.'/-</span>
								</div>

								<div class="col-md-3 padding-left-none">
									<span class="data-value">Crating Charges (per CFT) : '.$row->data->crating_charges.'/-</span>
								</div>

								<div class="clearfix"></div>

								<div class="col-md-3 form-control-fld padding-left-none">
                                    <div class="radio-block"><input type="checkbox" '.$origin_storage.' disabled /> <span class="lbl padding-8">Storage</span></div>
                                    <div class="radio-block"><input type="checkbox" disabled '.$origin_handyman_services.'> <span class="lbl padding-8">Handyman Services</span></div>
                                </div>

                                <div class="col-md-3 form-control-fld padding-left-none">
                                    <div class="radio-block"><input type="checkbox" disabled '.$destination_storage.'> <span class="lbl padding-8">Storage</span></div>
                                    <div class="radio-block"><input type="checkbox" disabled '.$destination_handyman_services.'> <span class="lbl padding-8">Handyman Services</span></div>
                                    
                                </div>
								<div class="clearfix"></div>
								<div class="col-md-3 padding-left-none">
									<span class="data-head"><u>Additional Charges</u></span>
								</div>
								<div class="clearfix"></div>
								<div class="col-md-3 padding-left-none">
									<span class="data-value">Cancellation Charges (Rs) : '.$row->data->cancellation_charge_price.'/-</span>
								</div>

								<div class="col-md-3 padding-left-none">
									<span class="data-value">Other Charges (Rs) : '.$row->data->other_charge_price.'/-</span>
								</div>';

				$row->attributes(array("class" => ""));
						
			} );
			
			$gridBuyer->orderBy ( 'id', 'desc' );
			$gridBuyer->paginate ( 5 );

			$result = array ();
			$result ['gridBuyer'] = $gridBuyer;
			return $result;
			
		} catch ( Exception $exc ) {
		}
	}

}