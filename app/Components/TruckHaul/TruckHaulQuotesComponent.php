<?php
namespace App\Components\truckHaul;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use App\Components\CommonComponent;
use App\Components\Matching\SellerMatchingComponent;
use App\Models\User;
use App\Models\TruckhaulSearchTerm;
use App\Models\TruckhaulBuyerQuoteSellersQuotesPrice;
use App\Models\TruckhaulSellerSelectedBuyer;
use App\Models\TruckhaulSellerPost;
use App\Models\TruckhaulSellerPostItem;


use Redirect;

class TruckHaulQuotesComponent {
	
	/**
	 * Submitting Seller Initial Quote
	 *	
	 * @param  $request
	 * @return Response
	 */
	public static function TruckHaulSellerQuoteSubmit($request) {
		try{
			
			$roleId = Auth::User()->lkp_role_id;
			if($roleId == SELLER){
				CommonComponent::activityLog("TRUCKHAUL_SELLER_SUBMIT_QUOTE",
				TRUCKHAUL_SELLER_SUBMIT_QUOTE,0,
				HTTP_REFERRER,CURRENT_URL);
			}
			$sellerInput = Input::all();
			 
			if(!empty($sellerInput['buyer_buyerquote_id'])) {
				$arrayIds = explode("_",$sellerInput['buyer_buyerquote_id']);
				$buyerId = $arrayIds[0];
				$buyerQuoteItemId = $arrayIds[1];
			
			}
			if(isset($sellerInput['buyer_buyerquote_id']) && !empty($sellerInput['buyer_buyerquote_id'])
					&& isset($sellerInput['initial_quote']) && !empty($sellerInput['initial_quote'])) {
			
				
					
				$getBuyerpostdetails  = DB::table('truckhaul_buyer_quote_items')
					->leftjoin('truckhaul_buyer_quotes', 'truckhaul_buyer_quotes.id', '=', 'truckhaul_buyer_quote_items.buyer_quote_id')
					->where('truckhaul_buyer_quote_items.id','=',$buyerQuoteItemId)
					->where('truckhaul_buyer_quote_items.created_by','=',$buyerId)
					->select('truckhaul_buyer_quote_items.*','truckhaul_buyer_quotes.transaction_id')
					->get();
					
				$getSellerpostdetails  = DB::table('truckhaul_seller_post_items')
					->leftjoin('truckhaul_seller_posts','truckhaul_seller_posts.id','=','truckhaul_seller_post_items.seller_post_id')
					->where('truckhaul_seller_post_items.id','=',Session::get('seller_post_item'))
					->where('truckhaul_seller_post_items.created_by','=',Auth::user()->id)
					->select('truckhaul_seller_posts.*','truckhaul_seller_post_items.*')
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
				$nowdate    = date('Y-m-d');
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
				
				$postid       =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
				$created_year = date('Y');
				$randnumber   = 'TRUCKHAUL/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
				date_default_timezone_set("Asia/Kolkata");
				$created_at = date ( 'Y-m-d H:i:s' );
			
				$Date1      = date('Y-m-d', strtotime($nowdate. " + ".$sellerInput['initial_transit']." days"));
				$createdIp = $_SERVER['REMOTE_ADDR'];
				$createsellerpost = new TruckhaulSellerPost();
				$createsellerpost->lkp_service_id = ROAD_TRUCK_HAUL;
				$createsellerpost->from_date = $nowdate;
				$createsellerpost->to_date =$to;
				$createsellerpost->cancellation_charge_text = $getSellerpostdetails[0]->cancellation_charge_text;
				$createsellerpost->cancellation_charge_price = $getSellerpostdetails[0]->cancellation_charge_price;
				$createsellerpost->docket_charge_text = $getSellerpostdetails[0]->docket_charge_text;
				$createsellerpost->docket_charge_price = $getSellerpostdetails[0]->docket_charge_price;
				$createsellerpost->other_charge1_text = $getSellerpostdetails[0]->other_charge1_text;
				$createsellerpost->other_charge1_price = $getSellerpostdetails[0]->other_charge1_price;
				$createsellerpost->other_charge2_text = $getSellerpostdetails[0]->other_charge2_text;
				$createsellerpost->other_charge2_price = $getSellerpostdetails[0]->other_charge2_price;
				$createsellerpost->other_charge3_text = $getSellerpostdetails[0]->other_charge3_text;
				$createsellerpost->other_charge3_price = $getSellerpostdetails[0]->other_charge3_price;
				$createsellerpost->tracking = $getSellerpostdetails[0]->tracking;
				$createsellerpost->terms_conditions = $getSellerpostdetails[0]->terms_conditions;
				$createsellerpost->lkp_payment_mode_id = $getSellerpostdetails[0]->lkp_payment_mode_id;
				$createsellerpost->accept_payment_netbanking = $getSellerpostdetails[0]->accept_payment_netbanking;
				$createsellerpost->accept_payment_credit = $getSellerpostdetails[0]->accept_payment_credit;
				$createsellerpost->accept_payment_debit = $getSellerpostdetails[0]->accept_payment_debit;
				$createsellerpost->credit_period = $getSellerpostdetails[0]->credit_period;
				$createsellerpost->credit_period_units = $getSellerpostdetails[0]->credit_period_units;
				$createsellerpost->accept_credit_netbanking = $getSellerpostdetails[0]->accept_credit_netbanking;
				$createsellerpost->accept_credit_cheque = $getSellerpostdetails[0]->accept_credit_cheque;
				$createsellerpost->seller_id = Auth::user()->id;
				$createsellerpost->lkp_post_status_id = 2;
				$createsellerpost->transaction_id = $randnumber;
				$createsellerpost->lkp_access_id = 3;
				$createsellerpost->created_at = $created_at;
				$createsellerpost->created_by = Auth::user()->id;
				$createsellerpost->created_ip = $createdIp;
				$createsellerpost->save();
				CommonComponent::auditLog($createsellerpost->id,'truckhaul_seller_posts');
					
				
					
				$createsellerpostitem = new TruckhaulSellerPostItem();
				$createsellerpostitem->seller_post_id = $createsellerpost->id;
				$createsellerpostitem->from_location_id = $getBuyerpostdetails[0]->from_city_id;
				$createsellerpostitem->to_location_id =$getBuyerpostdetails[0]->to_city_id;
				$createsellerpostitem->lkp_district_id =CommonComponent::getDistrictid($getBuyerpostdetails[0]->from_city_id);
				$createsellerpostitem->lkp_load_type_id = $getBuyerpostdetails[0]->lkp_load_type_id;
				$createsellerpostitem->lkp_vehicle_type_id = $getBuyerpostdetails[0]->lkp_vehicle_type_id;
                                $createsellerpost->vehicle_number = $getSellerpostdetails[0]->vehicle_number;
				$createsellerpostitem->transitdays = $sellerInput['initial_transit'];
				$createsellerpostitem->units = 'Days';
				$createsellerpostitem->lkp_post_status_id = 2;
				$createsellerpostitem->is_private = 1;
				$createsellerpostitem->price = $_POST['initial_quote'];
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
				
				
				//code added by swathi for updating values from market leads	
				$getbqsqp = DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
                                ->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
                                ->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
                                ->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
                                ->select('truckhaul_buyer_quote_sellers_quotes_prices.id')
                                ->get();
                                if(count($getbqsqp)>0){
                                        $initial_cretaed = date ( 'Y-m-d H:i:s' );
										DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
                                        ->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
                                        ->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
                                        ->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
                                        ->update(array('initial_quote_price' =>$_POST['initial_quote'],
                                                                'initial_transit_days' => $sellerInput['initial_transit'],
																'private_seller_quote_id'=>$createsellerpostitem->id,
																'seller_post_item_id'=>$sellerInput['seller_post_item_id'],
                                                        		'initial_quote_created_at' =>$initial_cretaed));

                                }
                                else{
                                        $initial_cretaed = date ( 'Y-m-d H:i:s' );
                                        $buyerinitial = new TruckhaulBuyerQuoteSellersQuotesPrice();
                                        $buyerinitial->buyer_id = $buyerId;
                                        $buyerinitial->buyer_quote_item_id = $buyerQuoteItemId;
                                        $buyerinitial->seller_id =Auth::user()->id;
                                        $buyerinitial->initial_quote_price = $_POST['initial_quote'];
                                        $buyerinitial->initial_transit_days = $sellerInput['initial_transit'];
                                        $buyerinitial->seller_post_item_id =$sellerInput['seller_post_item_id'];
                                        $buyerinitial->private_seller_quote_id =$createsellerpostitem->id;
                                        $buyerinitial->created_at = $created_at;
                                        $buyerinitial->created_by = Auth::user()->id;
                                        $buyerinitial->created_ip = $createdIp;
                                        $buyerinitial->initial_quote_created_at = $initial_cretaed;
                                        $buyerinitial->save();

                                        CommonComponent::auditLog($buyerinitial->id,'truckhaul_buyer_quote_sellers_quotes_prices');
                                        
                                }
				
				
				//*******matching engine***********************//
				$matchedItems = array();
				$matchedItems['from_city_id']=$getBuyerpostdetails[0]->from_city_id;
				$matchedItems['to_city_id']=$getBuyerpostdetails[0]->to_city_id;
				$matchedItems['lkp_load_type_id']=$getBuyerpostdetails[0]->lkp_vehicle_type_id;
				$matchedItems['lkp_vehicle_type_id']=$getBuyerpostdetails[0]->lkp_vehicle_type_id;
				$matchedItems['dispatch_date']=CommonComponent::convertMysqlDate($from);
				$matchedItems['delivery_date']=CommonComponent::convertMysqlDate($to);
				$matchedItems['is_private']=1;
				$matchedItems['transit_days']=$sellerInput['initial_transit'];
				SellerMatchingComponent::doMatching("1",$createsellerpostitem->id,2,$matchedItems);
				
				//*******matching engine***********************//
				
				$seller_initial_quote_email = DB::table('users')->where('id', $buyerId)->get();
                                $seller_initial_quote_email[0]->sellername = Auth::User()->username;
				CommonComponent::send_email(INITIAL_COUNT_BY_SELLER,$seller_initial_quote_email);
				
				
				
				//*******Send Sms to the buyers from seller submit a quote***********************//
				$msg_params = array(
						'randnumber' => $getBuyerpostdetails[0]->transaction_id,
						'sellername' => Auth::User()->username,
						'servicename' => 'FTL'
				);
				$getMobileNumber  =   CommonComponent::getMobleNumber($buyerId);
				CommonComponent::sendSMS($getMobileNumber,SELLER_SUBMITT_QOUTE_SMS,$msg_params);
				//*******Send Sms to the buyers from seller submit a quote***********************//
								
				
			}
			Session::put('message', 'Initial Quote given successfully');
			
		} catch( Exception $e ) {
			return $e->message;
		}
		return Redirect::back();
	}
	
	/**
	 * Submitting Seller Final Quote
	 *
	 * @param  $request
	 * @return Response
	 */
	public static function TruckHaulSellerFinalQuoteSubmit($request) {
		try{
	
	
			$roleId = Auth::User()->lkp_role_id;
			if($roleId == SELLER){
				CommonComponent::activityLog("TRUCKHAUL_SELLER_SUBMIT_QUOTE",
				TRUCKHAUL_SELLER_SUBMIT_QUOTE,0,
				HTTP_REFERRER,CURRENT_URL);
			}
			$sellerInput = Input::all();
			if(!empty($sellerInput['buyer_buyerquote_id'])) {
				$arrayIds = explode("_",$sellerInput['buyer_buyerquote_id']);
				$buyerId = $arrayIds[0];
				$buyerQuoteItemId = $arrayIds[1];
			}
			if(isset($sellerInput['buyer_buyerquote_id']) && !empty($sellerInput['buyer_buyerquote_id'])
					&& isset($sellerInput['final_quote']) && !empty($sellerInput['final_quote'])) {
						$final_cretaed = date ( 'Y-m-d H:i:s' );
						DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
						->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
						->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
						->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
						->update(array('final_quote_price' =>$sellerInput['final_quote'],
								'final_transit_days' =>$sellerInput['final_transit'],
								'seller_acceptence'=>1,
								'final_quote_created_at' =>$final_cretaed));
						CommonComponent::auditLog($buyerId,'truckhaul_buyer_quote_sellers_quotes_prices');
						$seller_final_quote_email = DB::table('users')->where('id', $buyerId)->get();
						$seller_final_quote_email[0]->sellername = Auth::User()->username;
			
						CommonComponent::send_email(FINAL_COUNT_BY_SELLER,$seller_final_quote_email);
			}
			Session::put('message', 'Initial Quote given successfully');
				
		} catch( Exception $e ) {
			return $e->message;
		}
		return Redirect::back();
	}
	
	
	
	/**
	 * Submitting Seller Final Quote
	 *
	 * @param  $request
	 * @return Response
	 */
	public static function TruckHaulSellerAcceptQuoteSubmit($request) {
		try{
	
	
			$roleId = Auth::User()->lkp_role_id;
			if($roleId == SELLER){
				CommonComponent::activityLog("TRUCKHAUL_SELLER_SUBMIT_QUOTE",
				TRUCKHAUL_SELLER_SUBMIT_QUOTE,0,
				HTTP_REFERRER,CURRENT_URL);
			}
			$sellerInput = Input::all();
			if(!empty($sellerInput['buyer_buyerquote_id'])) {
				$arrayIds = explode("_",$sellerInput['buyer_buyerquote_id']);
				$buyerId = $arrayIds[0];
				$buyerQuoteItemId = $arrayIds[1];
			}
			
			if(isset($sellerInput['buyer_buyerquote_id']) && !empty($sellerInput['buyer_buyerquote_id'])
					&& isset($sellerInput['accept_quote']) && !empty($sellerInput['accept_quote'])) {
		
							
				$getBuyerpostdetails  = DB::table('truckhaul_buyer_quote_items')
				->where('truckhaul_buyer_quote_items.id','=',$buyerQuoteItemId)
				->where('truckhaul_buyer_quote_items.created_by','=',$buyerId)
				->select('truckhaul_buyer_quote_items.*')
				->get();
					
				$getSellerpostdetails  = DB::table('truckhaul_seller_post_items')
				->leftjoin('truckhaul_seller_posts','truckhaul_seller_posts.id','=','truckhaul_seller_post_items.seller_post_id')
				->where('truckhaul_seller_post_items.id','=',Session::get('seller_post_item'))
				->where('truckhaul_seller_post_items.created_by','=',Auth::user()->id)
				->select('truckhaul_seller_posts.*','truckhaul_seller_post_items.*')
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
	
				$postid       =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
				$created_year = date('Y');
				$randnumber   = 'TRUCKHAUL/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
				//echo $randnumber;exit;
				date_default_timezone_set("Asia/Kolkata");
				$created_at = date ( 'Y-m-d H:i:s' );
				$nowdate    = date('Y-m-d');
				
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
				
				$Date1      = date('Y-m-d', strtotime($nowdate. " + ". $sellerInput['accept_transit']." days"));
				$createdIp = $_SERVER['REMOTE_ADDR'];
				$createsellerpost = new TruckhaulSellerPost();
				$createsellerpost->lkp_service_id = ROAD_TRUCK_HAUL;
				$createsellerpost->from_date = $nowdate;
				$createsellerpost->to_date =$to;
				$createsellerpost->cancellation_charge_text = $getSellerpostdetails[0]->cancellation_charge_text;
				$createsellerpost->cancellation_charge_price = $getSellerpostdetails[0]->cancellation_charge_price;
				$createsellerpost->docket_charge_text = $getSellerpostdetails[0]->docket_charge_text;
				$createsellerpost->docket_charge_price = $getSellerpostdetails[0]->docket_charge_price;
				$createsellerpost->other_charge1_text = $getSellerpostdetails[0]->other_charge1_text;
				$createsellerpost->other_charge1_price = $getSellerpostdetails[0]->other_charge1_price;
				$createsellerpost->other_charge2_text = $getSellerpostdetails[0]->other_charge2_text;
				$createsellerpost->other_charge2_price = $getSellerpostdetails[0]->other_charge2_price;
				$createsellerpost->other_charge3_text = $getSellerpostdetails[0]->other_charge3_text;
				$createsellerpost->other_charge3_price = $getSellerpostdetails[0]->other_charge3_price;
				$createsellerpost->tracking = $getSellerpostdetails[0]->tracking;
				$createsellerpost->terms_conditions = $getSellerpostdetails[0]->terms_conditions;
				$createsellerpost->lkp_payment_mode_id = $getSellerpostdetails[0]->lkp_payment_mode_id;
				$createsellerpost->accept_payment_netbanking = $getSellerpostdetails[0]->accept_payment_netbanking;
				$createsellerpost->accept_payment_credit = $getSellerpostdetails[0]->accept_payment_credit;
				$createsellerpost->accept_payment_debit = $getSellerpostdetails[0]->accept_payment_debit;
				$createsellerpost->credit_period = $getSellerpostdetails[0]->credit_period;
				$createsellerpost->credit_period_units = $getSellerpostdetails[0]->credit_period_units;
				$createsellerpost->accept_credit_netbanking = $getSellerpostdetails[0]->accept_credit_netbanking;
				$createsellerpost->accept_credit_cheque = $getSellerpostdetails[0]->accept_credit_cheque;
				$createsellerpost->seller_id = Auth::user()->id;
				$createsellerpost->lkp_post_status_id = 2;
				$createsellerpost->transaction_id = $randnumber;
				$createsellerpost->lkp_access_id = 3;
				$createsellerpost->created_at = $created_at;
				$createsellerpost->created_by = Auth::user()->id;
				$createsellerpost->created_ip = $createdIp;
				$createsellerpost->save();
				CommonComponent::auditLog($createsellerpost->id,'truckhaul_seller_posts');
					
	
					
				$createsellerpostitem = new TruckhaulSellerPostItem();
				$createsellerpostitem->seller_post_id = $createsellerpost->id;
				$createsellerpostitem->from_location_id = $getBuyerpostdetails[0]->from_city_id;
				$createsellerpostitem->to_location_id =$getBuyerpostdetails[0]->to_city_id;
				$createsellerpostitem->lkp_district_id =CommonComponent::getDistrictid($getBuyerpostdetails[0]->from_city_id);
				$createsellerpostitem->lkp_load_type_id = $getBuyerpostdetails[0]->lkp_load_type_id;
				$createsellerpostitem->lkp_vehicle_type_id = $getBuyerpostdetails[0]->lkp_vehicle_type_id;
				$createsellerpostitem->transitdays = $sellerInput['accept_transit'];
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
	
	
				$firm_cretaed = date ( 'Y-m-d H:i:s' );
				$buyerinitial = new TruckhaulBuyerQuoteSellersQuotesPrice();
				$buyerinitial->buyer_id = $buyerId;
				$buyerinitial->buyer_quote_item_id = $buyerQuoteItemId;
				$buyerinitial->seller_id =Auth::user()->id;
				$buyerinitial->firm_price = $sellerInput['accept_quote'];
				$buyerinitial->final_quote_price = $sellerInput['accept_quote'];
				$buyerinitial->final_transit_days = $sellerInput['accept_transit'];
				$buyerinitial->seller_post_item_id =$sellerInput['seller_post_item_id'];
				$buyerinitial->private_seller_quote_id =$createsellerpostitem->id;
				$buyerinitial->seller_acceptence =1;
				$buyerinitial->created_at = $created_at;
				$buyerinitial->created_by = Auth::user()->id;
				$buyerinitial->created_ip = $createdIp;
				$buyerinitial->firm_price_created_at = $firm_cretaed;
				$buyerinitial->final_quote_created_at = $firm_cretaed;
				$buyerinitial->save();
				
				CommonComponent::auditLog($buyerinitial->id,'truckhaul_buyer_quote_sellers_quotes_prices');
				$seller_initial_quote_email = DB::table('users')->where('id', $buyerId)->get();
				$seller_initial_quote_email[0]->sellername = Auth::User()->username;
					
	
	
				//*******matching engine***********************//
				$matchedItems = array();
				$matchedItems['from_city_id']=$getBuyerpostdetails[0]->from_city_id;
				$matchedItems['to_city_id']=$getBuyerpostdetails[0]->to_city_id;
				$matchedItems['lkp_load_type_id']=$getBuyerpostdetails[0]->lkp_vehicle_type_id;
				$matchedItems['lkp_vehicle_type_id']=$getBuyerpostdetails[0]->lkp_vehicle_type_id;
				$matchedItems['dispatch_date']=CommonComponent::convertMysqlDate($from);
				$matchedItems['delivery_date']=CommonComponent::convertMysqlDate($to);
				$matchedItems['is_private']=1;
				$matchedItems['transit_days']=$sellerInput['accept_transit'];
				SellerMatchingComponent::doMatching("1",$createsellerpostitem->id,2,$matchedItems);
	
				//*******matching engine***********************//
	
	
				CommonComponent::send_email(INITIAL_COUNT_BY_SELLER,$seller_initial_quote_email);
			}
			Session::put('message', 'Accepted Firm Offer successfully');
			
		} catch( Exception $e ) {
			return $e->message;
		}
		return Redirect::back();
	}
	
	
	
	/**
	 * Submitting Public Seller Acceptence Counter Offer
	 *
	 * @param  $request
	 * @return Response
	 */
	public static function TruckHaulSellerAcceptanceCounterOffer($request) {
		try{
				
			$roleId = Auth::User()->lkp_role_id;
			if($roleId == SELLER){
				CommonComponent::activityLog("TRUCKHAUL_SELLER_SUBMIT_QUOTE",
				TRUCKHAUL_SELLER_SUBMIT_QUOTE,0,
				HTTP_REFERRER,CURRENT_URL);
			}
			$sellerInput = Input::all();
			if(!empty($sellerInput['buyer_buyerquote_id'])) {
				$arrayIds = explode("_",$sellerInput['buyer_buyerquote_id']);
				$buyerId = $arrayIds[0];
				$buyerQuoteItemId = $arrayIds[1];
			}
							
				$getcounter = DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
				->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
				->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
				->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
				//->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_post_item_id','=',$sellerInput['seller_post_item_id'])
				->select('truckhaul_buyer_quote_sellers_quotes_prices.firm_price','truckhaul_buyer_quote_sellers_quotes_prices.counter_transit_days')
				->get();
				
				if(count($getcounter)>0){
					$final_cretaed = date ( 'Y-m-d H:i:s' );
					$updatefinal= DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
					//->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_post_item_id','=',$sellerInput['seller_post_item_id'])
					->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
					->update(array('final_quote_price' =>$sellerInput['counter_quote'],
							'final_transit_days' =>$getcounter[0]->counter_transit_days,
							'seller_acceptence'=>1,
							'final_quote_created_at'=>$final_cretaed));
					CommonComponent::auditLog($buyerId,'truckhaul_buyer_quote_sellers_quotes_prices');
					
				}	
				
		} catch( Exception $e ) {
			return $e->message;
		}
		return Redirect::back();
	}
	
	
	/**
	 * Submitting Public Quote Acceptence 
	 *
	 * @param  $request
	 * @return Response
	 */
	public static function TruckHaulSellerQuotePublicAcceptance($id,$bqid,$spqi=null,$quote=null,$pid=null) {
		try{
			
			$getcounter = DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
			->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$id)
			->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$bqid)
			->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
			->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_post_item_id','=',$spqi)
			->select('truckhaul_buyer_quote_sellers_quotes_prices.counter_quote_price',
					'truckhaul_buyer_quote_sellers_quotes_prices.firm_price')
					->get();
			if(count($getcounter)>0){
				
				if(isset($getcounter[0]->firm_price) && $getcounter[0]->firm_price==0){
					$final_cretaed = date ( 'Y-m-d H:i:s' );
					$updatefinal= DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$id)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$bqid)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
					->update(array('final_quote_price' =>$getcounter[0]->counter_quote_price,
							'seller_acceptence'=>1,
							'final_quote_created_at'=>$final_cretaed));
					CommonComponent::auditLog($id,'truckhaul_buyer_quote_sellers_quotes_prices');
						
					$seller_accept_counter_offe_email = DB::table('users')->where('id', $id)->get();
					$seller_accept_counter_offe_email[0]->sellername = Auth::User()->username;
					CommonComponent::send_email(ACCEPTED_COUNTER_QUOTE,$seller_accept_counter_offe_email);
						
				}
				else{
					$firm_cretaed = date ( 'Y-m-d H:i:s' );
					$updatefinal= DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$id)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$bqid)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
					->update(array('final_quote_price' =>$getcounter[0]->firm_price,
							'final_quote_created_at'=>$firm_cretaed,
							'firm_price' =>$getcounter[0]->firm_price,'seller_acceptence'=>1,
							'seller_post_item_id'=>$spqi,'firm_price_created_at'=>$firm_cretaed));
					CommonComponent::auditLog($id,'truckhaul_buyer_quote_sellers_quotes_prices');
						
					$seller_firm_price_email = DB::table('users')->where('id', $id)->get();
					$seller_firm_price_email[0]->sellername = Auth::User()->username;
					CommonComponent::send_email(FIRM_PRICE_ACCEPTED_BY_SELLER,$seller_firm_price_email);
						
						
				}
				Session::put('message', 'Successfully Accepted Counter Quote');
			}
			else{
				
				$getsellepostdetails = DB::table('truckhaul_seller_posts')
				->where('truckhaul_seller_posts.transaction_id','=',$pid)
				->select('*')
				->get();
				if(count($getsellepostdetails)>0){
					$created_at = date ( 'Y-m-d H:i:s' );
					$createdIp = $_SERVER['REMOTE_ADDR'];
					$sellerselectedbuyer = new TruckhaulSellerSelectedBuyer();
					$sellerselectedbuyer->seller_post_id = $getsellepostdetails[0]->id;
					$sellerselectedbuyer->buyer_id = $id;
					$sellerselectedbuyer->created_by = Auth::user()->id;
					$sellerselectedbuyer->created_at = $created_at;
					$sellerselectedbuyer->created_ip = $createdIp;
					$sellerselectedbuyer->save();
					CommonComponent::auditLog($sellerselectedbuyer->id,'truckhaul_seller_selected_buyers');
						
						
					$firm_cretaed = date ( 'Y-m-d H:i:s' );
					$buyerinitial = new TruckhaulBuyerQuoteSellersQuotesPrice();
					$buyerinitial->buyer_id = $id;
					$buyerinitial->buyer_quote_item_id = $bqid;
					$buyerinitial->seller_id =Auth::user()->id;
					$buyerinitial->final_quote_price = $quote;
					$buyerinitial->seller_post_item_id =$spqi;
					$buyerinitial->private_seller_quote_id =$spqi;
					$buyerinitial->seller_acceptence =1;
					$buyerinitial->firm_price =$quote;
					$buyerinitial->created_at = $created_at;
					$buyerinitial->created_by = Auth::user()->id;
					$buyerinitial->created_ip = $createdIp;
					$buyerinitial->firm_price_created_at = $firm_cretaed;
					$buyerinitial->final_quote_created_at = $firm_cretaed;
					$buyerinitial->save();
					CommonComponent::auditLog($buyerinitial->id,'truckhaul_buyer_quote_sellers_quotes_prices');
					$seller_firm_price_email = DB::table('users')->where('id', $id)->get();
					$seller_firm_price_email[0]->sellername = Auth::User()->username;
					CommonComponent::send_email(FIRM_PRICE_ACCEPTED_BY_SELLER,$seller_firm_price_email);
						
					Session::put('message', 'Firm price accepted successfully');
				}
				else{
			
					return redirect('sellerlist');
				}
			}
				
			
			
		} catch( Exception $e ) {
			return $e->message;
		}
		return Redirect::back();
	}
	
	
	
	/**
	 * Submitting Public Quote Acceptence
	 *
	 * @param  $request
	 * @return Response
	 */
	public static function TruckHaulSellerQuoteSearchAcceptance($request) {
		try{
				
			$roleId = Auth::User()->lkp_role_id;
			if($roleId == SELLER){
				CommonComponent::activityLog("TRUCKHAUL_SELLER_SUBMIT_QUOTE",
				TRUCKHAUL_SELLER_SUBMIT_QUOTE,0,
				HTTP_REFERRER,CURRENT_URL);
			}
			$sellerInput = Input::all();
			if(!empty($sellerInput['buyer_buyerquote_id'])) {
				$arrayIds = explode("_",$sellerInput['buyer_buyerquote_id']);
				$buyerId = $arrayIds[0];
				$buyerQuoteItemId = $arrayIds[1];
			}
			
			
			$getcounter = DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
			->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
			->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
			->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
			->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_post_item_id','=',$sellerInput['seller_post_item_id'])
			->select('truckhaul_buyer_quote_sellers_quotes_prices.counter_quote_price',
					'truckhaul_buyer_quote_sellers_quotes_prices.firm_price')
					->get();
			if(count($getcounter)>0){
	
				if(isset($getcounter[0]->firm_price) && $getcounter[0]->firm_price==0){
					$final_cretaed = date ( 'Y-m-d H:i:s' );
					$updatefinal= DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
					->update(array(
							'firm_price' =>$sellerInput['firm_quote'],
							'final_quote_price' =>$sellerInput['firm_quote'],
							'seller_acceptence'=>1,
							'final_quote_created_at'=>$final_cretaed));
					CommonComponent::auditLog($buyerId,'truckhaul_buyer_quote_sellers_quotes_prices');
	
					$seller_accept_counter_offe_email = DB::table('users')->where('id', $buyerId)->get();
					$seller_accept_counter_offe_email[0]->sellername = Auth::User()->username;
					CommonComponent::send_email(ACCEPTED_COUNTER_QUOTE,$seller_accept_counter_offe_email);
	
				}
				else{
					$firm_cretaed = date ( 'Y-m-d H:i:s' );
					$updatefinal= DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
					->update(array('final_quote_price' =>$sellerInput['firm_quote'],
							'final_quote_created_at'=>$firm_cretaed,
							'firm_price' =>$sellerInput['firm_quote'],'seller_acceptence'=>1,
							'seller_post_item_id'=>$sellerInput['seller_post_item_id'],'firm_price_created_at'=>$firm_cretaed));
					CommonComponent::auditLog($buyerId,'truckhaul_buyer_quote_sellers_quotes_prices');
	
					$seller_firm_price_email = DB::table('users')->where('id', $buyerId)->get();
					$seller_firm_price_email[0]->sellername = Auth::User()->username;
					CommonComponent::send_email(FIRM_PRICE_ACCEPTED_BY_SELLER,$seller_firm_price_email);
	
				}
				Session::put('message', 'Successfully Accepted Counter Quote');
			}
			else{
	
				$getsellepostdetails = DB::table('truckhaul_seller_posts')
				->where('truckhaul_seller_posts.transaction_id','=',$pid)
				->select('*')
				->get();
				if(count($getsellepostdetails)>0){
					$created_at = date ( 'Y-m-d H:i:s' );
					$createdIp = $_SERVER['REMOTE_ADDR'];
					$sellerselectedbuyer = new TruckhaulSellerSelectedBuyer();
					$sellerselectedbuyer->seller_post_id = $getsellepostdetails[0]->id;
					$sellerselectedbuyer->buyer_id = $buyerId;
					$sellerselectedbuyer->created_by = Auth::user()->id;
					$sellerselectedbuyer->created_at = $created_at;
					$sellerselectedbuyer->created_ip = $createdIp;
					$sellerselectedbuyer->save();
					CommonComponent::auditLog($sellerselectedbuyer->id,'truckhaul_seller_selected_buyers');
	
	
					$firm_cretaed = date ( 'Y-m-d H:i:s' );
					$buyerinitial = new TruckhaulBuyerQuoteSellersQuotesPrice();
					$buyerinitial->buyer_id = $buyerId;
					$buyerinitial->buyer_quote_item_id = $buyerQuoteItemId;
					$buyerinitial->seller_id =Auth::user()->id;
					$buyerinitial->final_quote_price = $quote;
					$buyerinitial->seller_post_item_id =$spqi;
					$buyerinitial->private_seller_quote_id =$spqi;
					$buyerinitial->seller_acceptence =1;
					$buyerinitial->firm_price =$quote;
					$buyerinitial->created_at = $created_at;
					$buyerinitial->created_by = Auth::user()->id;
					$buyerinitial->created_ip = $createdIp;
					$buyerinitial->firm_price_created_at = $firm_cretaed;
					$buyerinitial->final_quote_created_at = $firm_cretaed;
					$buyerinitial->save();
					CommonComponent::auditLog($buyerinitial->id,'truckhaul_buyer_quote_sellers_quotes_prices');
					$seller_firm_price_email = DB::table('users')->where('id', $buyerId)->get();
					$seller_firm_price_email[0]->sellername = Auth::User()->username;
					CommonComponent::send_email(FIRM_PRICE_ACCEPTED_BY_SELLER,$seller_firm_price_email);
	
					Session::put('message', 'Firm price accepted successfully');
				}
				else{
						
					return redirect('sellerlist');
				}
			}
	
				
				
		} catch( Exception $e ) {
			return $e->message;
		}
		return Redirect::back();
	}
	
	
	
	/**
	 * Submitting Seller Initial Quote for search buyer quotes
	 *
	 * @param $request
	 * @return Response
	 */
	public static function TruckHaulsellerSearchQuoteSubmit($request) {
		try{
			
			$roleId = Auth::User()->lkp_role_id;
			if($roleId == SELLER){
				CommonComponent::activityLog("TRUCKHAUL_SELLER_SUBMIT_QUOTE",
				TRUCKHAUL_SELLER_SUBMIT_QUOTE,0,
				HTTP_REFERRER,CURRENT_URL);
			}
			$sellerInput = Input::all();
			
			if(!empty($sellerInput['buyer_buyerquote_id'])) {
				$arrayIds = explode("_",$sellerInput['buyer_buyerquote_id']);
				$buyerId = $arrayIds[0];
				$buyerQuoteItemId = $arrayIds[1];
			}
			$getfromcityid  = CommonComponent::getCityId($_POST['from_city_loc']);
			$getrocityid    = CommonComponent::getCityId($_POST['to_city_loc']);
			
			$getBuyerpostdetails  = DB::table('truckhaul_buyer_quote_items')
			->leftjoin('truckhaul_buyer_quotes', 'truckhaul_buyer_quotes.id', '=', 'truckhaul_buyer_quote_items.buyer_quote_id')
			->where('truckhaul_buyer_quote_items.id','=',$buyerQuoteItemId)
			->where('truckhaul_buyer_quote_items.created_by','=',$buyerId)
			->select('truckhaul_buyer_quote_items.*','truckhaul_buyer_quotes.transaction_id')
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
			
			
			$getSellerpost  = DB::table('truckhaul_seller_post_items')
							->where('truckhaul_seller_post_items.from_location_id','=',$getfromcityid[0]->id)
							->where('truckhaul_seller_post_items.to_location_id','=',$getrocityid[0]->id)
							->where('truckhaul_seller_post_items.created_by','=',Auth::user()->id)
							->select('truckhaul_seller_post_items.seller_post_id','truckhaul_seller_post_items.id',
									 'truckhaul_seller_post_items.lkp_load_type_id','truckhaul_seller_post_items.lkp_vehicle_type_id',
									 'truckhaul_seller_post_items.transitdays')
							->get();
			
				
			
			
			$postid       =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
			$created_year = date('Y');
			$randnumber   = 'TRUCKHAUL/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
			//echo $randnumber;exit;
			date_default_timezone_set("Asia/Kolkata");
			$created_at = date ( 'Y-m-d H:i:s' );
			$nowdate    = date('Y-m-d');
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
			$Date1      = date('Y-m-d', strtotime($nowdate. " + ".$sellerInput['initial_transit']." days"));
			$createdIp = $_SERVER['REMOTE_ADDR'];
			$createsellerpost = new TruckhaulSellerPost();
			$createsellerpost->lkp_service_id = ROAD_TRUCK_HAUL;
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
			$createsellerpost->seller_id = Auth::user()->id;
			$createsellerpost->lkp_post_status_id = 2;
			$createsellerpost->transaction_id = $randnumber;
			$createsellerpost->lkp_access_id = 3;
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
			->where('lkp_vehicle_types.vehicle_type', 'LIKE','%'.Session::get('vehicle_type').'%')
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
			$createsellerpostitem->transitdays = $sellerInput['initial_transit'];
			$createsellerpostitem->units = 'Days';
			$createsellerpostitem->lkp_post_status_id = 2;
			$createsellerpostitem->is_private = 1;
			$createsellerpostitem->price = $_POST['initial_quote'];
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
		
		
			
			$getbqsqp = DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
			->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
			->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
			->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
			->select('truckhaul_buyer_quote_sellers_quotes_prices.id')
			->get();
			if(count($getbqsqp)>0){
				$initial_cretaed = date ( 'Y-m-d H:i:s' );
				
				$updateinitial = DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
				->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
				->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
				->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
				->update(array('initial_quote_price' =>$_POST['initial_quote'],
							'initial_transit_days' => $sellerInput['initial_transit'],
                                                        'seller_post_item_id'=>$createsellerpostitem->id,
                                                        'private_seller_quote_id'=>$createsellerpostitem->id,
                                                        'initial_quote_created_at' =>$initial_cretaed));
					
				
			
			}
			else{
				$initial_cretaed = date ( 'Y-m-d H:i:s' );
				$buyerinitial = new TruckhaulBuyerQuoteSellersQuotesPrice();
				$buyerinitial->buyer_id = $buyerId;
				$buyerinitial->buyer_quote_item_id = $buyerQuoteItemId;
				$buyerinitial->seller_id =Auth::user()->id;
				$buyerinitial->initial_quote_price = $_POST['initial_quote'];
				$buyerinitial->initial_transit_days = $sellerInput['initial_transit'];
				$buyerinitial->seller_post_item_id =$createsellerpostitem->id;
				$buyerinitial->private_seller_quote_id =$createsellerpostitem->id;
				$buyerinitial->created_at = $created_at;
				$buyerinitial->created_by = Auth::user()->id;
				$buyerinitial->created_ip = $createdIp;
				$buyerinitial->initial_quote_created_at = $initial_cretaed;
				$buyerinitial->save();
			
				CommonComponent::auditLog($buyerinitial->id,'truckhaul_buyer_quote_sellers_quotes_prices');
				$seller_initial_quote_email = DB::table('users')->where('id', $buyerId)->get();
				$seller_initial_quote_email[0]->sellername = Auth::User()->username;
		
				//CommonComponent::send_email(INITIAL_COUNT_BY_SELLER,$seller_initial_quote_email);
			}
	
			
			//*******matching engine***********************//
			$matchedItems = array();
			$matchedItems['from_city_id']=$getfromcityid[0]->id;
			$matchedItems['to_city_id']=$getrocityid[0]->id;
			$matchedItems['lkp_load_type_id']=$load_type[0]->id;
			$matchedItems['lkp_vehicle_type_id']=$vehicle_type[0]->id;
			$matchedItems['dispatch_date']=CommonComponent::convertMysqlDate($nowdate);
			$matchedItems['delivery_date']=CommonComponent::convertMysqlDate($Date1);
			$matchedItems['is_private']=1;
			$matchedItems['transit_days']=$tansitdays;
			SellerMatchingComponent::doMatching("1",$createsellerpostitem->id,2,$matchedItems);
				
			//*******matching engine***********************//
			
			
			//*******Send Sms to the buyers,seller submit a quote from a serach***********************//
			$msg_params = array(
					'randnumber' => $getBuyerpostdetails[0]->transaction_id,
					'sellername' => Auth::User()->username,
					'servicename' => 'FTL'
			);
			$getMobileNumber  =   CommonComponent::getMobleNumber($buyerId);
			CommonComponent::sendSMS($getMobileNumber,SELLER_SUBMITT_QOUTE_SMS,$msg_params);
			//*******Send Sms to the buyers,seller submit a quote from a serach***********************//
			
			
		} catch( Exception $e ) {
			return $e->message;
		}
		
		return Redirect::back();
	}
	
	
	/**
	 * Submitting Public Search Quote Acceptence
	 *
	 * @param  $request
	 * @return Response
	 */
	public static function TruckHaulsellerQuoteAcceptance($id,$bqid,$spqi=null) {
		try{
			
			
			$roleId = Auth::User()->lkp_role_id;
				
			$getcounter = DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
			->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$id)
			->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$bqid)
			->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
			->select('truckhaul_buyer_quote_sellers_quotes_prices.counter_quote_price',
					'truckhaul_buyer_quote_sellers_quotes_prices.firm_price')
					->get();
			if(count($getcounter)>0){
				if(isset($getcounter[0]->firm_price) && $getcounter[0]->firm_price==0){
					$final_cretaed = date ( 'Y-m-d H:i:s' );
					$updatefinal= DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$id)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$bqid)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
					->update(array('final_quote_price' =>$getcounter[0]->counter_quote_price,
							'seller_acceptence'=>1,
							'final_quote_created_at'=>$final_cretaed));
					CommonComponent::auditLog($id,'truckhaul_buyer_quote_sellers_quotes_prices');
						
					$seller_accept_counter_offe_email = DB::table('users')->where('id', $id)->get();
					$seller_accept_counter_offe_email[0]->sellername = Auth::User()->username;
					CommonComponent::send_email(ACCEPTED_COUNTER_QUOTE,$seller_accept_counter_offe_email);
				}
				else{
					
					$firm_cretaed = date ( 'Y-m-d H:i:s' );
					$updatefinal= DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$id)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$bqid)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
					->update(array('final_quote_price' =>$getcounter[0]->firm_price,
							'final_quote_created_at'=>$firm_cretaed,
							'firm_price' =>$getcounter[0]->firm_price,'seller_acceptence'=>1,
							'seller_post_item_id'=>$spqi,'firm_price_created_at'=>$firm_cretaed));
					CommonComponent::auditLog($id,'truckhaul_buyer_quote_sellers_quotes_prices');
			
					$seller_firm_price_email = DB::table('users')->where('id', $id)->get();
					$seller_firm_price_email[0]->sellername = Auth::User()->username;
					CommonComponent::send_email(FIRM_PRICE_ACCEPTED_BY_SELLER,$seller_firm_price_email);
						
						
				}
				Session::put('message', 'Final Quote given successfully');
			
	
			}
		} catch( Exception $e ) {
			return $e->message;
		}
		return Redirect::back();
	}
	
	
	
	/**
	 * Submitting Public Search Quote Acceptence
	 *
	 * @param  $request
	 * @return Response
	 */
	public static function TruckHaulsellerCounterAcceptance($request) {
		try{
				
			$roleId = Auth::User()->lkp_role_id;
			if($roleId == SELLER){
				CommonComponent::activityLog("TRUCKHAUL_SELLER_SUBMIT_QUOTE",
				TRUCKHAUL_SELLER_SUBMIT_QUOTE,0,
				HTTP_REFERRER,CURRENT_URL);
			}
			$sellerInput = Input::all();
			
			if(!empty($sellerInput['buyer_buyerquote_id'])) {
				$arrayIds = explode("_",$sellerInput['buyer_buyerquote_id']);
				$buyerId = $arrayIds[0];
				$buyerQuoteItemId = $arrayIds[1];
			}
				
				
			$getcounter = DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
			->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
			->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
			->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
			->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_post_item_id','!=',0)
			->select('truckhaul_buyer_quote_sellers_quotes_prices.counter_quote_price','truckhaul_buyer_quote_sellers_quotes_prices.counter_transit_days',
					'truckhaul_buyer_quote_sellers_quotes_prices.firm_price')
					->get();
			if(count($getcounter)>0){
				if(isset($getcounter[0]->firm_price) && $getcounter[0]->firm_price==0){
					
					$final_cretaed = date ( 'Y-m-d H:i:s' );
					$updatefinal= DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
					->update(array('final_quote_price' =>$getcounter[0]->counter_quote_price,
							'final_transit_days' =>$getcounter[0]->counter_transit_days,
							'seller_acceptence'=>1,
							'final_quote_created_at'=>$final_cretaed));
					CommonComponent::auditLog($buyerId,'truckhaul_buyer_quote_sellers_quotes_prices');
	
					$seller_accept_counter_offe_email = DB::table('users')->where('id', $buyerId)->get();
					$seller_accept_counter_offe_email[0]->sellername = Auth::User()->username;
					CommonComponent::send_email(ACCEPTED_COUNTER_QUOTE,$seller_accept_counter_offe_email);
				}
				else{
					$firm_cretaed = date ( 'Y-m-d H:i:s' );
					$updatefinal= DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
					->update(array('final_quote_price' =>$getcounter[0]->firm_price,
							'final_quote_created_at'=>$firm_cretaed,
							'firm_price' =>$getcounter[0]->firm_price,'seller_acceptence'=>1,
							'seller_post_item_id'=>$spqi,'firm_price_created_at'=>$firm_cretaed));
					CommonComponent::auditLog($id,'truckhaul_buyer_quote_sellers_quotes_prices');
						
					$seller_firm_price_email = DB::table('users')->where('id', $buyerId)->get();
					$seller_firm_price_email[0]->sellername = Auth::User()->username;
					CommonComponent::send_email(FIRM_PRICE_ACCEPTED_BY_SELLER,$seller_firm_price_email);
	
	
				}
				Session::put('message', 'Final Quote given successfully');
			}
		} catch( Exception $e ) {
			return $e->message;
		}
		return Redirect::back();
	}
	
	
	
	/**
	 * Submitting Public Search Quote Acceptence for firm prize
	 *
	 * @param  $request
	 * @return Response
	 */
	public static function TruckHaulsellerSearchAcceptance($reques){
		try{
			
			
			$roleId = Auth::User()->lkp_role_id;
			if($roleId == SELLER){
				CommonComponent::activityLog("TRUCKHAUL_SELLER_SUBMIT_QUOTE",
				TRUCKHAUL_SELLER_SUBMIT_QUOTE,0,
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
				$getSellerpost  = DB::table('truckhaul_seller_post_items')
				->where('truckhaul_seller_post_items.from_location_id','=',$getfromcityid[0]->id)
				->where('truckhaul_seller_post_items.to_location_id','=',$getrocityid[0]->id)
				->where('truckhaul_seller_post_items.created_by','=',Auth::user()->id)
				->select('truckhaul_seller_post_items.seller_post_id','truckhaul_seller_post_items.id')
				->get();
			
				$getBuyerpostdetails  = DB::table('truckhaul_buyer_quote_items')
						->leftjoin('truckhaul_buyer_quotes', 'truckhaul_buyer_quotes.id', '=', 'truckhaul_buyer_quote_items.buyer_quote_id')
						->where('truckhaul_buyer_quote_items.id','=',$buyerQuoteItemId)
						->where('truckhaul_buyer_quote_items.created_by','=',$buyerId)
						->select('truckhaul_buyer_quote_items.*','truckhaul_buyer_quotes.transaction_id')
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
				$createsellerpost->lkp_service_id = ROAD_TRUCK_HAUL;
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
				$createsellerpost->lkp_access_id = 3;
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
				
				$getSellerpost  = DB::table('truckhaul_seller_post_items')
				->where('truckhaul_seller_post_items.from_location_id','=',$getfromcityid[0]->id)
				->where('truckhaul_seller_post_items.to_location_id','=',$getrocityid[0]->id)
				->where('truckhaul_seller_post_items.created_by','=',Auth::user()->id)
				->select('truckhaul_seller_post_items.seller_post_id','truckhaul_seller_post_items.id')
				->get();
					
				$getcounter = DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
				->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
				->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
				->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
				->select('truckhaul_buyer_quote_sellers_quotes_prices.counter_quote_price',
						'truckhaul_buyer_quote_sellers_quotes_prices.firm_price')
						->get();
					
				if(count($getcounter)>0){
					$firm_cretaed = date ( 'Y-m-d H:i:s' );
					$updatefinal= DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyerQuoteItemId)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
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
