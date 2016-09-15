<?php

namespace App\Components\Ftl;

use DB;
use App\Models\BuyerQuoteItemView;
use App\Models\BuyerQuoteItems;
use App\Models\BuyerQuoteSellersQuotesPrices;

use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use Auth;
use App\Http\Requests;
use Input;
use Config;
use Session;
use Redirect;
use Log;
use App\Components\CommonComponent;
use App\Components\MessagesComponent;
use App\Components\SellerComponent;
use App\Components\BuyerComponent;



class FtlBuyerListingComponent {

	
	/**
	 * Buyer Posts List Page
	 * Retrieval of data related to buyer posts list items to populate in the buyer list widget
	 * Displays a grid with a list of all seller posts
	 */
	public static function listFTLBuyerPrivatePosts($statusId, $serviceId, $roleId,$type) {

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
                $Query->leftjoin ( 'buyer_quote_sellers_quotes_prices as bqss', 'bqss.buyer_quote_item_id', '=', 'bqi.id' );
                $Query->leftjoin ( 'users as us', 'us.id', '=', 'bq.buyer_id' );
                $Query->where ( 'bqss.seller_id', Auth::User ()->id ); 
                
		$Query->whereIn('bqi.lkp_post_status_id',array(2,3,4,5));
                $Query->where('bq.lkp_quote_access_id','=',2);
                $Query->groupBy('bqi.buyer_quote_id');
                $Query->orderBy('bqi.buyer_quote_id', 'DESC');


		// conditions to make search
		if(isset($statusId) && $statusId != '' && $statusId!=0){
			$Query->where ( 'bqi.lkp_post_status_id', '=', $statusId );
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
		
                //grid
                $grid = DataGrid::source ( $Query );
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Buyer Name', 'username' )->attributes(array("class" => "col-md-2 padding-left-none"));
		
		$grid->add ( 'dispatch_date', 'Dispatch Date', 'dispatch_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add ( 'delivery_date', 'Delivery Date', 'delivery_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'price', 'Pricing', 'price' )->attributes(array("class" => "col-md-2 padding-left-none hidden-xs"))->style ( "display:none" );
		
		$grid->add ( 'load_type', 'Load Type', 'load_type' )->attributes(array("class" => "col-md-2 padding-left-none"));
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
                        $delivery_date_buyer = $row->cells [3]->value;


			$getbqi = DB::table('buyer_quote_items')
				->where('buyer_quote_items.id','=',$buyer_quote_id)
				->select('price', 'lkp_quote_price_type_id')
				->get();
			//echo $buyer_quote_id;exit;
            $buyer_post_status_id = $row->cells [6]->value;
			$buyer_post_status = $row->cells [6]->value;
			$load_type_buyer = $row->cells [5]->value;
			$vechile_type_buyer = $row->cells [7]->value;
			$fromcity_buyer = $row->cells [8]->value;
			$tocity_buyer = $row->cells [9]->value;
			//$delivery_date_buyer = $row->cells [10]->value;
			
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
                        $getInitialQuotePrice = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'initial_quote_price','buyer_quote_sellers_quotes_prices');
                        $getCounterQuotePrice = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'counter_quote_price','buyer_quote_sellers_quotes_prices');
                        $getFinalQuotePrice   = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'final_quote_price','buyer_quote_sellers_quotes_prices');
                        $getFirmQuotePrice   = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'firm_price','buyer_quote_sellers_quotes_prices');
                        $subscription  = DB::table('sellers')
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
                         $delivery_date_buyer_convert = CommonComponent::checkAndGetDate($delivery_date_buyer);
                        if($delivery_date_buyer_convert != ""){
				$dates = CommonComponent::checkAndGetDate($dispatch_date_buyer)." - ".$delivery_date_buyer_convert;
			}else{
				$dates = CommonComponent::checkAndGetDate($dispatch_date_buyer);
			}
                        $row->cells [5]->value .= '<div class=""><div class="col-md-2 padding-left-none">
											'.$buyer_name.'
											<div class="red">
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
											</div>
										</div>
										<div class="col-md-2 padding-left-none">'.CommonComponent::checkAndGetDate($dispatch_date_buyer).'</div>
										<div class="col-md-2 padding-none">'.CommonComponent::checkAndGetDate($delivery_date_buyer).'</div>
										<div class="col-md-2 padding-left-none">'.$load_type_buyer.'</div>'
                                . '                                             <div class="col-md-2 padding-left-none">'.$buyer_post_status.'</div>';
						
						$getSellerpost  =   SellerComponent::sellerPostDetails($from_city_id,$to_city_id,$buyer_quote_id);
						
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
								'delivery_date_buyer'=>$delivery_date_buyer,
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

            //$filter->add ( 'spi.lkp_vehicle_type_id', 'Vehicle Type', 'select')->options($vehicle_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
            //$filter->add ( 'spi.lkp_load_type_id', 'Load Type', 'select')->options($load_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
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
