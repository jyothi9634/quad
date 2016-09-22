<?php
namespace App\Components\TruckHaul;
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
use App\Models\TruckhaulSearchTerm;
use App\Models\TruckhaulSellerPost;
use App\Models\TruckhaulSellerPostItem;
use App\Models\TruckhaulSellerSelectedBuyer;
use App\Components\Search\SellerSearchComponent;
use App\Models\TruckhaulBuyerQuoteSellersQuotesPrice;
use App\Components\SellerComponent;
use Faker\Provider\Payment;
use App\Components\Matching\SellerMatchingComponent;
use Redirect;

class TruckHaulSellerComponent {
	


public static function getTruckHaulSellerSearchList($roleId, $serviceId,$statusId) {
	
		$from_locations = array(""=>"From Location");
		$to_locations = array(""=>"To Location");
		$vehicle_types = array("" => "Vehicle Type");
		$load_types = array("" => "Load Type");
		$inputparams = array();
		$buyerNames = array ();
		$buyerPriceType = array ();

		$request['is_dispatch_flexible'] = isset($request['dispatch_flexible_hidden']) ? $request['dispatch_flexible_hidden'] : 0;
		
		
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
			$sellerpost_for_buyers  =  new TruckhaulSearchTerm();
			$sellerpost_for_buyers->user_id = Auth::id();
			$sellerpost_for_buyers->from_city_id = $_REQUEST['from_city_id'];
			$sellerpost_for_buyers->to_city_id = $_REQUEST['to_city_id'];
			$sellerpost_for_buyers->dispatch_date = $_REQUEST['dispatch_date'];
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
			Session::put('results_count','1');
        }else{
        	Session::put('results_count','');
        	Session::put('results_count_more','2');
        }
		
		
		foreach($Query_buyers_for_sellers_filter as $Query_buyers_for_seller){
			$buyers_for_sellers_items  = DB::table('truckhaul_buyer_quote_items')
			->where('truckhaul_buyer_quote_items.id',$Query_buyers_for_seller->id)
			->select('*')
			->get();
			Session::put('dispatch_date',$Query_buyers_for_seller->dispatch_date);
			Session::put('vehicle_type',$Query_buyers_for_seller->vehicle_type);
			Session::put('load_type',$Query_buyers_for_seller->load_type);
			
			foreach($buyers_for_sellers_items as $buyers_for_sellers_item){
			//echo "<pre>"; print_r($buyers_for_sellers_item); echo  "</pre>"; //exit;
				if(!isset($from_locations[$buyers_for_sellers_item->from_city_id])){
					$from_locations[$buyers_for_sellers_item->from_city_id] = DB::table('lkp_cities')->where('id', $buyers_for_sellers_item->from_city_id)->pluck('city_name');
				}
				if(!isset($to_locations[$buyers_for_sellers_item->to_city_id])){
					$to_locations[$buyers_for_sellers_item->to_city_id] = DB::table('lkp_cities')->where('id', $buyers_for_sellers_item->to_city_id)->pluck('city_name');
				}
				
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
                    if (! isset ( $load_types [$Query_buyers_for_seller->id] )) {
						$load_types[$buyers_for_sellers_item->lkp_load_type_id] = DB::table('lkp_load_types')->where('id', $buyers_for_sellers_item->lkp_load_type_id)->pluck('load_type');
					}
					if (! isset ( $vehicle_types [$Query_buyers_for_seller->id] )) {
						$vehicle_types[$buyers_for_sellers_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $buyers_for_sellers_item->lkp_vehicle_type_id)->pluck('vehicle_type');
					}
					Session::put('layered_filter', $buyerNames);
					Session::put('price_filter', $buyerPriceType);
					Session::put('from_date_filter', $buyerFrom);
                    Session::put('load_type_filter', $load_types);
                    Session::put('vehicle_type_filter', $vehicle_types);

				}
			}
		}


		$grid = DataGrid::source ( $Query_buyers_for_sellers );
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Buyer Name', 'username' )->attributes(array("class" => "col-md-4 padding-left-none"));
		$grid->add ( 'usernames', 'Rating', 'usernames' )->attributes(array("class" => "col-md-2 padding-left-none"))->style ( "display:none" );
		$grid->add ( 'dispatch_date', 'Reporting Date', 'dispatch_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'price', 'Pricing', 'price' )->attributes(array("class" => "col-md-2 padding-left-none hidden-xs"));
		$grid->add ( 'lkp_post_status_id', 'Status', 'lkp_post_status_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'load_type', 'LoadType', 'load_type' )->style ( "display:none" );
		$grid->add ( 'vehicle_type', 'VehicleType', 'vehicle_type' )->style ( "display:none" );
		$grid->add ( 'fromcity', 'FromCity', 'fromcity' )->style ( "display:none" );
		$grid->add ( 'tocity', 'Tocity', 'tocity' )->style ( "display:none" );
		$grid->add ( 'dispatch_dates', 'Delivery Date', 'dispatch_dates' )->style ( "display:none" );
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
			$getbqi = DB::table('truckhaul_buyer_quote_items')
				->where('truckhaul_buyer_quote_items.id','=',$buyer_quote_id)
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
						
						$getSellerpost  = DB::table('truckhaul_seller_post_items')
						->join( 'truckhaul_seller_posts', 'truckhaul_seller_posts.id', '=', 'truckhaul_seller_post_items.seller_post_id' )
						->join( 'truckhaul_buyer_quote_sellers_quotes_prices', 'truckhaul_buyer_quote_sellers_quotes_prices.seller_post_item_id', '=', 'truckhaul_seller_post_items.id' )
						->where('truckhaul_seller_post_items.from_location_id','=',$_REQUEST['from_city_id'])
						->where('truckhaul_seller_post_items.to_location_id','=',$_REQUEST['to_city_id'])
						->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyer_quote_id)
						->where('truckhaul_seller_post_items.created_by','=',Auth::user()->id)
						->where('truckhaul_seller_posts.lkp_post_status_id','=',OPEN)
						->select('truckhaul_seller_post_items.seller_post_id',
								'truckhaul_seller_post_items.id',
								'truckhaul_seller_posts.tracking',
								'truckhaul_seller_posts.lkp_payment_mode_id',
								'truckhaul_seller_posts.accept_payment_netbanking',
								'truckhaul_seller_posts.accept_payment_credit',
								'truckhaul_seller_posts.accept_payment_debit',
								'truckhaul_seller_posts.credit_period',
								'truckhaul_seller_posts.credit_period_units',
								'truckhaul_seller_posts.accept_credit_netbanking',
								'truckhaul_seller_posts.accept_credit_cheque')
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
                        //TruckHaulBuyerComponent::viewCountForBuyerForTruckHaul(Auth::User()->id,$buyer_quote_id);
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
	
        
        public static function sellerPostDetails($from_city_id,$to_city_id,$buyer_quote_id){
            $data=DB::table('truckhaul_seller_post_items as spi')
                ->join( 'truckhaul_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' )
                ->join( 'truckhaul_buyer_quote_sellers_quotes_prices as bqsp', 'bqsp.seller_post_item_id', '=', 'spi.id' )
                ->where('spi.from_location_id','=',$from_city_id)
                ->where('spi.to_location_id','=',$to_city_id)
                ->where('bqsp.buyer_quote_item_id','=',$buyer_quote_id)
                ->where('spi.created_by','=',Auth::user()->id)
                ->where('sp.lkp_post_status_id','=',OPEN)
                ->select('spi.seller_post_id','spi.id','sp.tracking','sp.lkp_payment_mode_id',
                                'sp.accept_payment_netbanking','sp.accept_payment_credit',
                                'sp.accept_payment_debit','sp.credit_period',
                                'sp.credit_period_units','sp.accept_credit_netbanking','sp.accept_credit_cheque')
                                ->get();
            return $data;
        }

        
        /**
	 * Submitting Public Search Quote Acceptence for firm prize
	 *
	 * @param  $request
	 * @return Response
	 */
	public static function THsellerSearchAcceptance($request){
		try{
			
			
			$roleId = Auth::User()->lkp_role_id;
			if($roleId == SELLER){
				CommonComponent::activityLog("SELLER_SUBMIT_QUOTE",
				SELLER_SUBMIT_QUOTE,0,
				HTTP_REFERRER,CURRENT_URL);
			}
			$sellerInput = Input::all();
			
			if(!empty($sellerInput['buyer_buyerquote_id'])) {
				$arrayIds = explode("_",$sellerInput['buyer_buyerquote_id']);
				$buyerId = $arrayIds[0];
				$buyerQuoteItemId = $arrayIds[1];
			}
			
			
			if($_POST['search']==1){
				
				$getfromcityid = CommonComponent::getCityId($_POST['from_city_loc']);
				$getrocityid = CommonComponent::getCityId($_POST['to_city_loc']);
				$getSellerpost  = DB::table('truckhaul_seller_post_items as spi')
				->where('spi.from_location_id','=',$getfromcityid[0]->id)
				->where('spi.to_location_id','=',$getrocityid[0]->id)
				->where('spi.created_by','=',Auth::user()->id)
				->select('spi.seller_post_id','spi.id')
				->get();
			
				$getBuyerpostdetails  = DB::table('truckhaul_buyer_quote_items as bqi')
						->leftjoin('truckhaul_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
						->where('bqi.id','=',$buyerQuoteItemId)
						->where('bqi.created_by','=',$buyerId)
						->select('bqi.*','bq.transaction_id')
						->get();
				if(count($getBuyerpostdetails)>0){
					if($getBuyerpostdetails[0]->is_dispatch_flexible == 0){
						$checkdispatch = $getBuyerpostdetails[0]->dispatch_date;
						
						$from = $getBuyerpostdetails[0]->dispatch_date;
						$to = date('Y-m-d', strtotime($from. " + 1 days"));
					}else{
						$checkdispatch = $getBuyerpostdetails[0]->dispatch_date;
				
						$from = date('Y-m-d', strtotime($checkdispatch. " - 3 days"));
						$to = date('Y-m-d', strtotime($checkdispatch. " + 3 days"));
					}
				}
				$postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
				$created_year = date('Y');
				$randnumber = 'TRUCKHAUL/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
				
				date_default_timezone_set("Asia/Kolkata");
				$created_at = date ( 'Y-m-d H:i:s' );
				$nowdate = date('Y-m-d');
				if($from<$nowdate){
						
					$nowdate = $nowdate;
					if($getBuyerpostdetails[0]->is_dispatch_flexible == 0){
						$to = date('Y-m-d', strtotime($nowdate. " + 1 days"));
					}else{
						$to = date('Y-m-d', strtotime($checkdispatch. " + 3 days"));
					}
				}else{
						
					$nowdate = $from;
				}
				$Date1 = date('Y-m-d', strtotime($nowdate. " + ".$sellerInput['accept_transit']." days"));
				$createdIp = $_SERVER['REMOTE_ADDR'];
				$createsellerpost = new TruckhaulSellerPost();
				$createsellerpost->lkp_service_id = 1;
				$createsellerpost->from_date = $nowdate;
				$createsellerpost->to_date =$to;
				$createsellerpost->cancellation_charge_text = 'NULL';
				$createsellerpost->cancellation_charge_price = 'NULL';
				$createsellerpost->docket_charge_text = 'NULL';
				$createsellerpost->docket_charge_price = 'NULL';
				$createsellerpost->tracking = $sellerInput['tracking'];
				if($sellerInput['paymentoptions'] == 1){
					$createsellerpost->lkp_payment_mode_id = 1;
					$createsellerpost->accept_payment_netbanking = 1;
					$createsellerpost->accept_payment_credit = 1;
					$createsellerpost->accept_payment_debit = 1;
				}else if($sellerInput['paymentoptions'] == 2){
					$createsellerpost->lkp_payment_mode_id = 2;
				}else if($sellerInput['paymentoptions'] == 3){
					$createsellerpost->lkp_payment_mode_id = 3;
				}else{
					if($sellerInput['credit_peroid'] == 0){
						$createsellerpost->lkp_payment_mode_id = 4;
						$createsellerpost->accept_credit_netbanking = 1;
					}else{
						$createsellerpost->lkp_payment_mode_id = 4;
						$createsellerpost->accept_credit_netbanking = 1;
						$createsellerpost->accept_credit_cheque = 1;
						$createsellerpost->credit_period = $sellerInput['credit_peroid'];
						$createsellerpost->credit_period_units = $sellerInput['credit_period_units'];
					}
				}
				$createsellerpost->accept_payment_netbanking = 1;
				$createsellerpost->seller_id = Auth::user()->id;
				$createsellerpost->lkp_post_status_id = 2;
				$createsellerpost->transaction_id = $randnumber;
				$createsellerpost->lkp_access_id = 2;
				$createsellerpost->created_at = $created_at;
				$createsellerpost->created_by = Auth::user()->id;
				$createsellerpost->created_ip = $createdIp;
				$createsellerpost->save();
				CommonComponent::auditLog($createsellerpost->id,'truckhaul_seller_posts');
				
		
				$load_type   = DB::table('lkp_load_types')
				->where('lkp_load_types.load_type', 'LIKE', Session::get('load_type').'%')
				->select('lkp_load_types.id')
				->get();
		
				
				$vehicle_type = DB::table('lkp_vehicle_types')
				->where('lkp_vehicle_types.vehicle_type', 'LIKE', Session::get('vehicle_type').'%')
				->select('lkp_vehicle_types.id')
				->get();
		
				
				
				if(isset($_POST['to_date_delivery']) && $_POST['to_date_delivery']!='' && $_POST['to_date_delivery']!='0000-00-00'){
					$fdate = str_replace("/","-",$_POST['to_date_delivery']);
					$tdate = str_replace("/","-",$_POST['from_date_dispatch']);
					$delivery_date = date("Y-m-d", strtotime($fdate));
					$dispatch_date = date("Y-m-d", strtotime($tdate));
				
					if($delivery_date != '1970-01-01' && $delivery_date != '0000-00-00'){
						$tansitdays = strtotime($delivery_date) - strtotime($dispatch_date);
						$tansitdays = floor($tansitdays/(60*60*24));
					}else
					{
						$tansitdays=0;
					}
				}else{
					$tansitdays =0;
				}
				
				$createsellerpostitem = new TruckhaulSellerPostItem();
				$createsellerpostitem->seller_post_id = $createsellerpost->id;
				$createsellerpostitem->from_location_id = $getfromcityid[0]->id;
				$createsellerpostitem->to_location_id =$getrocityid[0]->id;
				$createsellerpostitem->lkp_district_id =1;
				$createsellerpostitem->lkp_load_type_id = $load_type[0]->id;
				$createsellerpostitem->lkp_vehicle_type_id = $vehicle_type[0]->id;
				$createsellerpostitem->transitdays =  $sellerInput['accept_transit'];;
				$createsellerpostitem->units = 'Days';
				$createsellerpostitem->lkp_post_status_id = 2;
				$createsellerpostitem->is_private = 1;
				$createsellerpostitem->price = $_POST['accept_quote'];
				$createsellerpostitem->created_by = Auth::user()->id;
				$createsellerpostitem->created_at = $created_at;
				$createsellerpostitem->created_ip = $createdIp;
				$createsellerpostitem->save();
				CommonComponent::auditLog($createsellerpostitem->id,'truckhaul_seller_post_items');
		
					
		
				$sellerselectedbuyer = new TruckhaulSellerSelectedBuyer();
				$sellerselectedbuyer->seller_post_id = $createsellerpost->id;
				$sellerselectedbuyer->buyer_id = $buyerId;
				$sellerselectedbuyer->created_by = Auth::user()->id;
				$sellerselectedbuyer->created_at = $created_at;
				$sellerselectedbuyer->created_ip = $createdIp;
				$sellerselectedbuyer->save();
				CommonComponent::auditLog($sellerselectedbuyer->id,'truckhaul_seller_selected_buyers');
		
		
				date_default_timezone_set("Asia/Kolkata");
				$created_at = date ( 'Y-m-d H:i:s' );
				$final_cretaed = date ( 'Y-m-d H:i:s' );
				$createdIp = $_SERVER['REMOTE_ADDR'];
				$buyerfinal = new TruckhaulBuyerQuoteSellersQuotesPrice();
				$buyerfinal->buyer_id = $buyerId;
				$buyerfinal->seller_post_item_id = $createsellerpost->id;
				$buyerfinal->buyer_quote_item_id = $buyerQuoteItemId;
				$buyerfinal->seller_id =Auth::user()->id;
				$buyerfinal->final_quote_price = $_POST['accept_quote'];
				$buyerfinal->final_transit_days = $sellerInput['accept_transit'];
				$buyerfinal->firm_price = $_POST['accept_quote'];
				$buyerfinal->seller_post_item_id =$createsellerpostitem->id;
				$buyerfinal->private_seller_quote_id =$createsellerpostitem->id;
				$buyerfinal->created_at = $created_at;
				$buyerfinal->created_by = Auth::user()->id;
				$buyerfinal->seller_acceptence = 1;
				$buyerfinal->created_ip = $createdIp;
				$buyerfinal->final_quote_created_at = $final_cretaed;
				$buyerfinal->firm_price_created_at = $final_cretaed;
				$buyerfinal->save();
				CommonComponent::auditLog($buyerfinal->id,'truckhaul_buyer_quote_sellers_quotes_prices');
		
		
				$seller_initial_quote_email = DB::table('users')->where('id', $buyerId)->get();
				$seller_initial_quote_email[0]->sellername = Auth::User()->username;
		
				CommonComponent::send_email(FIRM_PRICE_ACCEPTED_BY_SELLER,$seller_initial_quote_email);
				
				//*******matching engine***********************//
					$matchedItems = array();
					$matchedItems['from_city_id']=$getfromcityid[0]->id;
					$matchedItems['to_city_id']=$getrocityid[0]->id;
					$matchedItems['lkp_load_type_id']=$load_type[0]->id;
					$matchedItems['lkp_vehicle_type_id']=$vehicle_type[0]->id;
					$matchedItems['dispatch_date']=CommonComponent::convertMysqlDate($nowdate);
					$matchedItems['delivery_date']=CommonComponent::convertMysqlDate($Date1);
					$matchedItems['is_private']=1;
					SellerMatchingComponent::doMatching(ROAD_TRUCK_HAUL,$createsellerpostitem->id,2,$matchedItems);
				
				//*******matching engine***********************//
				
				return Redirect::back();
				
			}else{
				
				$getfromcityid = CommonComponent::getCityId($_POST['from_city_loc']);
				$getrocityid = CommonComponent::getCityId($_POST['to_city_loc']);
				
				$getSellerpost  = DB::table('truckhaul_seller_post_items as spi')
				->where('spi.from_location_id','=',$getfromcityid[0]->id)
				->where('spi.to_location_id','=',$getrocityid[0]->id)
				->where('spi.created_by','=',Auth::user()->id)
				->select('spi.seller_post_id','spi.id')
				->get();
					
				$getcounter = DB::table('truckhaul_buyer_quote_sellers_quotes_prices as bqsp')
				->where('bqsp.buyer_id','=',$buyerId)
				->where('bqsp.buyer_quote_item_id','=',$buyerQuoteItemId)
				->where('bqsp.seller_id','=',Auth::user()->id)
				->select('bqsp.counter_quote_price',
						'bqsp.firm_price')
						->get();
					
				if(count($getcounter)>0){
					$firm_cretaed = date ( 'Y-m-d H:i:s' );
					DB::table('truckhaul_buyer_quote_sellers_quotes_prices as bqsp')
					->where('bqsp.buyer_id','=',$buyerId)
					->where('bqsp.buyer_quote_item_id','=',$buyerQuoteItemId)
					->where('bqsp.seller_id','=',Auth::user()->id)
					->update(array('final_quote_price' =>$getcounter[0]->firm_price,
							'final_transit_days' => $sellerInput['accept_transit'],
							'final_quote_created_at'=>$firm_cretaed,
							'firm_price' =>$getcounter[0]->firm_price,'seller_acceptence'=>1,
							'seller_post_item_id'=>$getSellerpost[0]->id,'firm_price_created_at'=>$firm_cretaed));
					CommonComponent::auditLog($buyerId,'truckhaul_buyer_quote_sellers_quotes_prices');
			
					$seller_firm_price_email = DB::table('users')->where('id', $buyerId)->get();
					$seller_firm_price_email[0]->sellername = Auth::User()->username;
					CommonComponent::send_email(FIRM_PRICE_ACCEPTED_BY_SELLER,$seller_firm_price_email);
			
					Session::put('message', 'Final Quote given successfully');
					return Redirect::back();
				}
			}
		
		} catch( Exception $e ) {
			return $e->message;
		}
		return Redirect::back();
	}
	
}