<?php
namespace App\Components\Ftl;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use App\Components\CommonComponent;
use App\Components\BuyerComponent;
use App\Models\User;
use App\Models\FtlSearchTerm;
use App\Components\Search\SellerSearchComponent;

use App\Components\SellerComponent;
use Faker\Provider\Payment;


class FtlSellerComponent {
	
	public static function getFtlSellerSearchList($roleId, $serviceId,$statusId)
	{
		$from_locations = array(""=>"From Location");
		$to_locations = array(""=>"To Location");
		$vehicle_types = array("" => "Vehicle Type");
		$load_types = array("" => "Load Type");
		$inputparams = array();
		
		$buyerNames = array ();
		$buyerPriceType = array ();

		$_REQUEST['is_dispatch_flexible'] = isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : 0;
		$_REQUEST['is_delivery_flexible'] = isset($_REQUEST['delivery_flexible_hidden']) ? $_REQUEST['delivery_flexible_hidden'] : 0;
		
		if(isset($_REQUEST['lkp_vehicle_type_ids']) && $_REQUEST['lkp_vehicle_type_ids']!=''){
			if(isset($_REQUEST['lkp_vehicle_type_id']) && $_REQUEST['lkp_vehicle_type_id']!=''){
				$_REQUEST['lkp_vehicle_type_id'] =$_REQUEST['lkp_vehicle_type_id'];
			}else{
				$_REQUEST['lkp_vehicle_type_id'] =$_REQUEST['lkp_vehicle_type_ids'];
			}
		}
		if(isset($_REQUEST['lkp_load_type_ids']) && $_REQUEST['lkp_load_type_ids']!=''){
			if(isset($_REQUEST['lkp_load_type_id']) && $_REQUEST['lkp_load_type_id']!=''){
				$_REQUEST['lkp_load_type_id'] =$_REQUEST['lkp_load_type_id'];
			}else{
				$_REQUEST['lkp_load_type_id'] =$_REQUEST['lkp_load_type_ids'];
			}
		}
		
		$inputparams = $_REQUEST;
		$Query_buyers_for_sellers = SellerSearchComponent::search ( $roleId, $serviceId, $statusId, $inputparams );
		
		
		if(isset($_REQUEST['lkp_vehicle_type_id']) && $_REQUEST['lkp_vehicle_type_id']!='' && isset($_REQUEST['lkp_load_type_id']) && $_REQUEST['lkp_load_type_id']!='' && isset($_REQUEST['from_city_id']) && $_REQUEST['from_city_id']!='' && isset($_REQUEST['to_city_id']) && $_REQUEST['to_city_id']!='' && isset($_REQUEST['dispatch_date']) && $_REQUEST['dispatch_date']!='')
		{
			$sellerpost_for_buyers  =  new FtlSearchTerm();
			$sellerpost_for_buyers->user_id = Auth::id();
			$sellerpost_for_buyers->from_city_id = $_REQUEST['from_city_id'];
			$sellerpost_for_buyers->to_city_id = $_REQUEST['to_city_id'];
			$sellerpost_for_buyers->dispatch_date = $_REQUEST['dispatch_date'];
			$sellerpost_for_buyers->delivery_date = $_REQUEST['delivery_date'];
			$sellerpost_for_buyers->lkp_load_type_id = $_REQUEST['lkp_load_type_id'];
			$sellerpost_for_buyers->lkp_vehicle_type_id = $_REQUEST['lkp_vehicle_type_id'];
			$sellerpost_for_buyers->quantity = 1;
			$sellerpost_for_buyers->created_at = date ( 'Y-m-d H:i:s' );
			$sellerpost_for_buyers->created_ip = $_SERVER ['REMOTE_ADDR'];
			$sellerpost_for_buyers->created_by = Auth::id();
			$sellerpost_for_buyers->save();
		}

		$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();

		if(count($Query_buyers_for_sellers_filter) == 0 ){
            SellerComponent::searchTermsSendMail();
			Session::put('results_count','1');
        }else{
        	Session::put('results_count','');
        	Session::put('results_count_more','2');
        }
		
		foreach($Query_buyers_for_sellers_filter as $Query_buyers_for_seller){
			
			$buyers_for_sellers_items  = DB::table('buyer_quote_items')
				->where('buyer_quote_items.id',$Query_buyers_for_seller->id)
				->select('*')
				->get();

			Session::put('delivery_date',$Query_buyers_for_seller->delivery_date);
			Session::put('dispatch_date',$Query_buyers_for_seller->dispatch_date);
			Session::put('vehicle_type',$Query_buyers_for_seller->vehicle_type);
			Session::put('load_type',$Query_buyers_for_seller->load_type);
			
			foreach($buyers_for_sellers_items as $buyers_for_sellers_item){
			
				if(!isset($from_locations[$buyers_for_sellers_item->from_city_id])){
					$from_locations[$buyers_for_sellers_item->from_city_id] = DB::table('lkp_cities')->where('id', $buyers_for_sellers_item->from_city_id)->pluck('city_name');
				}
				if(!isset($to_locations[$buyers_for_sellers_item->to_city_id])){
					$to_locations[$buyers_for_sellers_item->to_city_id] = DB::table('lkp_cities')->where('id', $buyers_for_sellers_item->to_city_id)->pluck('city_name');
				}
				/*if(!isset($load_types[$buyers_for_sellers_item->lkp_load_type_id])){
					$load_types[$buyers_for_sellers_item->lkp_load_type_id] = DB::table('lkp_load_types')->where('id', $buyers_for_sellers_item->lkp_load_type_id)->pluck('load_type');
				}*/
				if(!isset($vehicle_types[$buyers_for_sellers_item->lkp_vehicle_type_id])){
					$vehicle_types[$buyers_for_sellers_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $buyers_for_sellers_item->lkp_vehicle_type_id)->pluck('vehicle_type');
				}
				

				if(isset($_REQUEST['is_search'])){
					if (! isset ( $buyerNames [$Query_buyers_for_seller->buyer_id] )) {
						$buyerNames[$Query_buyers_for_seller->buyer_id] = $Query_buyers_for_seller->username;
					}
					if (! isset ( $buyerPriceType [$Query_buyers_for_seller->id] )) {
						$buyerPriceType[$Query_buyers_for_seller->lkp_quote_price_type_id] = $Query_buyers_for_seller->lkp_quote_price_type_id;
					}
					if (! isset ( $buyerFrom [$Query_buyers_for_seller->id] )) {
						$buyerFrom[$Query_buyers_for_seller->dispatch_date] = $Query_buyers_for_seller->dispatch_date;
					}

					if (! isset ( $buyerTo [$Query_buyers_for_seller->id] )) {
						$buyerTo[$Query_buyers_for_seller->delivery_date] = $Query_buyers_for_seller->delivery_date;
					}
                    if (! isset ( $load_types [$Query_buyers_for_seller->id] )) {
						$load_types[$buyers_for_sellers_item->lkp_load_type_id] = DB::table('lkp_load_types')->where('id', $buyers_for_sellers_item->lkp_load_type_id)->pluck('load_type');
					}
					if (! isset ( $vehicle_types [$Query_buyers_for_seller->id] )) {
						$vehicle_types[$buyers_for_sellers_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $buyers_for_sellers_item->lkp_vehicle_type_id)->pluck('vehicle_type');
					}
					Session::put('layered_filter', $buyerNames);
					Session::put('price_filter', $buyerPriceType);
					Session::put('from_date_filter', $buyerFrom);
					Session::put('to_date_filter', $buyerTo);
                    Session::put('load_type_filter', $load_types);
                    Session::put('vehicle_type_filter', $vehicle_types);

				}
			}
		}
		
		$grid = DataGrid::source ( $Query_buyers_for_sellers );
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Buyer Name', 'username' )->attributes(array("class" => "col-md-4 padding-left-none"));
		$grid->add ( 'delivery_sdate', 'Rating', 'delivery_sdate' )->attributes(array("class" => "col-md-2 padding-left-none"))->style ( "display:none" );
		$grid->add ( 'dispatch_date', 'Dispatch Date', 'dispatch_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'price', 'Pricing', 'price' )->attributes(array("class" => "col-md-2 padding-left-none hidden-xs"));
		$grid->add ( 'lkp_post_status_id', 'Status', 'lkp_post_status_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'load_type', 'LoadType', 'load_type' )->style ( "display:none" );
		$grid->add ( 'vehicle_type', 'VehicleType', 'vehicle_type' )->style ( "display:none" );
		$grid->add ( 'fromcity', 'FromCity', 'fromcity' )->style ( "display:none" );
		$grid->add ( 'tocity', 'Tocity', 'tocity' )->style ( "display:none" );
		$grid->add ( 'delivery_date', 'Delivery Date', 'delivery_date' )->style ( "display:none" );
		$grid->add ( 'created_by', 'Created By', 'created_by' )->style ( "display:none" );
		$grid->add ( 'lkp_quote_access_id', 'Quote Access', 'lkp_quote_access_id' )->style ( "display:none" );
		$grid->add ( 'transaction_id', 'transaction_id', 'transaction_id' )->style ( "display:none" );
		$grid->add ( 'quantity', 'quantity', 'quantity' )->style ( "display:none" );
		$grid->add ( 'number_loads', 'number_loads', 'number_loads' )->style ( "display:none" );
		$grid->add ( 'units', 'Units', 'units' )->style ( "display:none" );
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
			$row->cells [5]->style ( 'width:100%' );
                        $transaction_id=$row->cells[13]->value;
			$accessid = $row->cells [12]->value;
			$buyer_id = $row->cells [11]->value;
			$buyer_quote_id = $row->cells [0]->value;
			$buyer_name = $row->cells [1]->value;
			$dispatch_date_buyer = $row->cells [3]->value;
			$price_buyer = $row->cells [4]->value;

			$getbqi = DB::table('buyer_quote_items')
				->where('buyer_quote_items.id','=',$buyer_quote_id)
				->select('price', 'lkp_quote_price_type_id')
				->get();
			$buyer_post_status = $row->cells [5]->value;
			$buyer_post_status_id = $row->cells [5]->value;
			$load_type_buyer = $row->cells [6]->value;
			$vechile_type_buyer = $row->cells [7]->value;
			$fromcity_buyer = $row->cells [8]->value;
			$tocity_buyer = $row->cells [9]->value;
			$delivery_date_buyer = $row->cells [10]->value;
			if($buyer_post_status == 1){
				$buyer_post_status = 'Saved as Draft';
			}
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
			if($price_buyer == 0){
				 $price_buyer = "Competitive";
			}else{
				$price_buyer = "Firm";
			}
			$units = $row->cells [16]->value;
			
			
			$row->cells [5]->value = '<form id ="addsellersearchpostquoteoffer" name ="addsellersearchpostquoteoffer">';
						$getInitialQuotePrice = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'initial_quote_price','buyer_quote_sellers_quotes_prices');
						$getCounterQuotePrice = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'counter_quote_price','buyer_quote_sellers_quotes_prices');
						$getFinalQuotePrice   = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'final_quote_price','buyer_quote_sellers_quotes_prices');
						$getFirmQuotePrice   = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'firm_price','buyer_quote_sellers_quotes_prices');
						$getUserrole = DB::table('users')
						->where('users.id', Auth::user()->id)
						->select('users.primary_role_id','users.is_business')
						->first();
						
						
						if($getUserrole->is_business == 1){
							$stable = 'sellers';
						}else{
							$stable = 'seller_details';
						}
						
						$subscription   = DB::table($stable)
							->where($stable.'.user_id',Auth::user()->id)
							->select($stable.'.subscription_end_date',$stable.'.subscription_start_date')
							->get();
						
						$qty=$row->cells [14]->value;
						$loads=$row->cells [15]->value;
						
						$subs_st_date = date('Y-m-d', strtotime($subscription[0]->subscription_start_date));
						$subs_end_date = date('Y-m-d', strtotime($subscription[0]->subscription_end_date));
						$now_date = date('Y-m-d');
						$row->cells [5]->value .= '
								
						<div class="">
										<div class="col-md-4 padding-left-none">
											'.$buyer_name.'
											<div class="red">
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
											</div>
										</div>
										<div class="col-md-2 padding-left-none">'.CommonComponent::checkAndGetDate($dispatch_date_buyer).'</div>
										<div class="col-md-2 padding-none">'.$price_buyer.'</div>
										<div class="col-md-2 padding-left-none">'.$buyer_post_status.'</div>';
						
						$getSellerpost  = DB::table('seller_post_items')
						->join( 'seller_posts', 'seller_posts.id', '=', 'seller_post_items.seller_post_id' )
						->join( 'buyer_quote_sellers_quotes_prices', 'buyer_quote_sellers_quotes_prices.seller_post_item_id', '=', 'seller_post_items.id' )
						->where('seller_post_items.from_location_id','=',$_REQUEST['from_city_id'])
						->where('seller_post_items.to_location_id','=',$_REQUEST['to_city_id'])
						->where('buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyer_quote_id)
						->where('seller_post_items.created_by','=',Auth::user()->id)
						->where('seller_posts.lkp_post_status_id','=',OPEN)
						->select('seller_post_items.seller_post_id',
								'seller_post_items.id',
								'seller_posts.tracking',
								'seller_posts.lkp_payment_mode_id',
								'seller_posts.accept_payment_netbanking',
								'seller_posts.accept_payment_credit',
								'seller_posts.accept_payment_debit',
								'seller_posts.credit_period',
								'seller_posts.credit_period_units',
								'seller_posts.accept_credit_netbanking',
								'seller_posts.accept_credit_cheque')
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
			//filter for buyear search list top dropdown lists---filters
			$filter = DataFilter::source ( $Query_buyers_for_sellers );
			//echo $from_locations[1];
			$filter->add ( 'bqi.lkp_vehicle_type_id', 'Vehicle Type', 'select')->options($vehicle_types)->attr("class"," form-control1")->attr("onchange","this.form.submit()");
			//$filter->add ( 'bqi.lkp_load_type_id', 'Load Type', 'select')->options($load_types)->attr("class"," form-control1")->attr("onchange","this.form.submit()");
			
			//$filter->add ( 'bqi.dispatch_date', 'Dispatch Date', 'date' )->attr("class","dateRange")->attr("id","dispatch_filter_calendar");
			//$filter->add ( 'bqi.delivery_date', 'Delivery Date', 'date' )->attr("class","dateRange")->attr("id","delivery_filter_calendar");
			
			$filter->submit('search');
			$filter->reset('reset');
			$filter->build();
			$result = array();
			$result['grid'] = $grid;
			$result['filter'] = $filter;
			return $result;
	}
	

	
}