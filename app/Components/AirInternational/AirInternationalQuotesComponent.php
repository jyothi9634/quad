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
use App\Models\FtlSearchTerm;
use App\Models\AirintBuyerQuoteSellersQuotesPrice;
use App\Models\AirintSellerPost;
use App\Models\AirintSellerPostItem;
use App\Models\AirintSellerSellectedBuyer;
use App\Components\Matching\SellerMatchingComponent;


use Redirect;

class AirInternationalQuotesComponent {
	
	/**
	 * Submitting Seller Initial Quote
	 *	
	 * @param  $request
	 * @return Response
	 */
	public static function AirInternationalSellerQuoteSubmit() {
		try{
			$formvalues = urldecode($_REQUEST['formvalues']);
			$formfields = explode("&", $formvalues);
			$hiddenfields = array();
			
			foreach($formfields as $formfield){
				$input = explode("=", $formfield);
				$hiddenfields[$input[0]] = $input[1]; 
			}
			//input values 
			//print_R($_REQUEST);
			//grid values
			//print_R($hiddenfields);die;
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
			
			if(isset($sellerInput['buyer_buyerquote_id']) && !empty($sellerInput['buyer_buyerquote_id'])) {
	
				$getSellerpostdetails  = DB::table('airint_seller_post_items')
				->leftjoin('airint_seller_posts','airint_seller_posts.id','=','airint_seller_post_items.seller_post_id')
				->where('airint_seller_post_items.id','=',Session::get('seller_post_item'))
				->where('airint_seller_post_items.created_by','=',Auth::user()->id)
				->select('airint_seller_posts.*','airint_seller_post_items.*')
				->get();
				
					
				$getBuyerpostdetails  = DB::table('airint_buyer_quotes')
				->where('airint_buyer_quotes.id','=',$buyerQuoteItemId)
				->where('airint_buyer_quotes.created_by','=',$buyerId)
				->select('airint_buyer_quotes.*')
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
				$randnumber = 'AIRINTERNATIONAL/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
					
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
				$Date1 = date('Y-m-d', strtotime($nowdate. " + ".$sellerInput['transitrValue']." days"));
				$createdIp = $_SERVER['REMOTE_ADDR'];
				$createsellerpost = new AirintSellerPost();
				$createsellerpost->lkp_service_id = AIR_INTERNATIONAL;
				$createsellerpost->from_date = $nowdate;
				$createsellerpost->to_date =$to;
				
				$createsellerpost->seller_id = Auth::user()->id;
				$createsellerpost->lkp_post_status_id = 2;
				$createsellerpost->lkp_ptl_post_type_id = 2;
				$createsellerpost->kg_per_cft = $sellerInput['kgpercftValue'];
				$createsellerpost->pickup_charges = $sellerInput['pickupvalue'];
				$createsellerpost->delivery_charges = $sellerInput['deliveryvalue'];
				$createsellerpost->oda_charges = $sellerInput['odavalue'];
				
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
				
				$createsellerpost->transaction_id = $randnumber;
				$createsellerpost->lkp_access_id = 3;
				$createsellerpost->created_at = $created_at;
				$createsellerpost->created_by = Auth::user()->id;
				$createsellerpost->created_ip = $createdIp;
				$createsellerpost->save();
				//CommonComponent::auditLog($createsellerpost->id,'seller_posts');
				//echo $createsellerpost->id;


				//frieght amount
				$totalfrieghtamount=0;
				for($i=0;$i<$_REQUEST['incrementcount'];$i++){
					if(isset($hiddenfields['volumetric_'.$i])){
						$volumeweight = $hiddenfields['volumetric_'.$i]*$_REQUEST['kgpercftValue']*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'];
						$densityweight= $hiddenfields['units_'.$i]*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'];
						if($volumeweight >= $densityweight)
							$totalfrieghtamount = $volumeweight+$totalfrieghtamount;
						else
							$totalfrieghtamount = $densityweight+$totalfrieghtamount;
					}
				}
				
				//total amount
				$total_initail_amount = $totalfrieghtamount ;
					
				$fromloc = $getBuyerpostdetails[0]->from_location_id;
				$toloc = $getBuyerpostdetails[0]->to_location_id;
				
					
				//echo $delivery_date." ".$dispatch_date." ".$load_type." ".$vehicle_type." ".$tansitdays;exit;
				$createsellerpostitem = new AirintSellerPostItem();
				$createsellerpostitem->seller_post_id = $createsellerpost->id;
				$createsellerpostitem->from_location_id = $fromloc;
				$createsellerpostitem->to_location_id =$toloc;
				$createsellerpostitem->lkp_district_id =1;
				$createsellerpostitem->lkp_post_status_id = 2;
				$createsellerpostitem->is_private = 1;
				$createsellerpostitem->transitdays = $sellerInput['transitrValue'];
				$createsellerpostitem->units = 'Days';
				$createsellerpostitem->price = $_REQUEST['rateperkgValue'];
				$createsellerpostitem->created_by = Auth::user()->id;
				$createsellerpostitem->created_at = $created_at;
				$createsellerpostitem->created_ip = $createdIp;
				$createsellerpostitem->save();
				//CommonComponent::auditLog($createsellerpostitem->id,'seller_post_items');
				
				
				
				$sellerselectedbuyer = new AirintSellerSellectedBuyer();
				$sellerselectedbuyer->seller_post_id = $createsellerpost->id;
				$sellerselectedbuyer->buyer_id = $buyerId;
				$sellerselectedbuyer->created_by = Auth::user()->id;
				$sellerselectedbuyer->created_at = $created_at;
				$sellerselectedbuyer->created_ip = $createdIp;
				$sellerselectedbuyer->save();
				//CommonComponent::auditLog($sellerselectedbuyer->id,'seller_selected_buyers');
				
                        //code added by swathi for updating values from market leads	
                        $getbqsqp = DB::table('airint_buyer_quote_sellers_quotes_prices as bqsp')
                        ->where('bqsp.buyer_id','=',$buyerId)
                        ->where('bqsp.buyer_quote_id','=',$_REQUEST['cbuyerquoteid'])
                        ->where('bqsp.seller_id','=',Auth::user()->id)
                        ->select('bqsp.id')
                        ->get();
                        if(count($getbqsqp)>0){
                                $initial_cretaed = date ( 'Y-m-d H:i:s' );

                                DB::table('ptl_buyer_quote_sellers_quotes_prices')
                                ->where('ptl_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
                                ->where('ptl_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$_REQUEST['cbuyerquoteid'])
                                ->where('ptl_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
                                ->update(array('initial_quote_price' =>$total_initail_amount,
                                'initial_transit_days' => $_REQUEST['transitrValue'],
                                'seller_post_item_id'=>Session::get('seller_post_item'),

                                'private_seller_quote_id' => $createsellerpost->id,
                                'initial_freight_amount'=>$totalfrieghtamount,    
                                'initial_rate_per_kg' => $_REQUEST['rateperkgValue'],
                                'initial_kg_per_cft'=>$_REQUEST['kgpercftValue'], 
                                'initial_pick_up_rupees' => $_REQUEST['pickupvalue'],
                                'initial_delivery_rupees'=>$_REQUEST['deliveryvalue'],    
                                'initial_oda_rupees' => $_REQUEST['odavalue'],

                                'initial_quote_created_at' =>$initial_cretaed));



                        }
                        else{  
				$initial_cretaed = date ( 'Y-m-d H:i:s' );
				$sellerinitialquote = new AirintBuyerQuoteSellersQuotesPrice();
				$sellerinitialquote->buyer_id = $buyerId;
				$sellerinitialquote->buyer_quote_id = $_REQUEST['cbuyerquoteid'];
				$sellerinitialquote->seller_id =Auth::user()->id;
				$sellerinitialquote->private_seller_quote_id =$createsellerpost->id;
				$sellerinitialquote->seller_post_item_id =Session::get('seller_post_item');
				$sellerinitialquote->initial_quote_price = $total_initail_amount;
				$sellerinitialquote->initial_freight_amount =$totalfrieghtamount;
				$sellerinitialquote->initial_rate_per_kg = $_REQUEST['rateperkgValue'];
				$sellerinitialquote->initial_kg_per_cft = $_REQUEST['kgpercftValue'];	
				$sellerinitialquote->initial_pick_up_rupees = $_REQUEST['pickupvalue'];
				$sellerinitialquote->initial_delivery_rupees = $_REQUEST['deliveryvalue'];
				$sellerinitialquote->initial_oda_rupees = $_REQUEST['odavalue'];
				$sellerinitialquote->initial_transit_days = $_REQUEST['transitrValue'];
				$sellerinitialquote->created_at = $created_at;
				$sellerinitialquote->created_by = Auth::user()->id;
				$sellerinitialquote->created_ip = $createdIp;
				$sellerinitialquote->initial_quote_created_at = $initial_cretaed;
				$sellerinitialquote->save();
                        }
				//CommonComponent::auditLog($buyerinitial->id,'ocean_buyer_quote_sellers_quotes_prices');
				$seller_initial_quote_email = DB::table('users')->where('id', $buyerId)->get();
				$seller_initial_quote_email[0]->sellername = Auth::User()->username;
				
				
					
				//*******matching engine***********************//
				$matchedItems = array ();
				$matchedItems['from_location_id']=$fromloc;
				$matchedItems['to_location_id']=$toloc;
				$matchedItems['valid_from']=$nowdate;
				$matchedItems['valid_to']=$Date1;
				$matchedItems['is_private']=1;
				$matchedItems['transit_days']=$sellerInput['transitrValue'];
				SellerMatchingComponent::doMatching(Session::get('service_id'), $createsellerpostitem->id, 2, $matchedItems);
					
				//*******matching engine***********************//
				
				CommonComponent::send_email(INITIAL_COUNT_BY_SELLER,$seller_initial_quote_email);
				
				
				
				//*******Send Sms to the buyers,from seller submit a quote ***********************//
				$msg_params = array(
						'randnumber' => $getBuyerpostdetails[0]->transaction_id,
						'sellername' => Auth::User()->username,
						'servicename' => 'AIR INTERNATIONAL'
				);
				$getMobileNumber  =   CommonComponent::getMobleNumber($buyerId);
				CommonComponent::sendSMS($getMobileNumber,SELLER_SUBMITT_QOUTE_SMS,$msg_params);
				//*******Send Sms to the buyers,from seller submit a quote ***********************//
			
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
	public static function AirInternationalSellerFinalQuoteSubmit() {
		try{
			$formvalues = urldecode($_REQUEST['formvalues']);
			$formfields = explode("&", $formvalues);
			$hiddenfields = array();
			
			
			
			foreach($formfields as $formfield){
				$input = explode("=", $formfield);
				$hiddenfields[$input[0]] = $input[1];
			}
	
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
				//$buyerQuoteItemId = $arrayIds[1];
			}
			//frieght amount
			$totalfrieghtamount=0;
			for($i=0;$i<$_REQUEST['incrementcount'];$i++){
				if(isset($hiddenfields['volumetric_'.$i])){
					$volumeweight = $hiddenfields['volumetric_'.$i]*$_REQUEST['kgpercftValue']*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'];
					$densityweight= $hiddenfields['units_'.$i]*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'];
					if($volumeweight >= $densityweight)
						$totalfrieghtamount = $volumeweight+$totalfrieghtamount;
					else
						$totalfrieghtamount = $densityweight+$totalfrieghtamount;
				}
			}

			//total amount
			$total_final_amount = $totalfrieghtamount + $_REQUEST['pickupvalue'] + $_REQUEST['deliveryvalue'] +$_REQUEST['odavalue'];
			
			$final_cretaed = date ( 'Y-m-d H:i:s' );
			DB::table('airint_buyer_quote_sellers_quotes_prices')
			->where('airint_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
			->where('airint_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$_REQUEST['cbuyerquoteid'])
			->where('airint_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
			->update(array(
					'final_quote_price' =>$total_final_amount,
					'final_freight_amount'=>$totalfrieghtamount,
					'seller_acceptence'=>1,
					'final_rate_per_kg'=>$_REQUEST['rateperkgValue'],
					'final_kg_per_cft'=>$_REQUEST['kgpercftValue'],
					'final_pick_up_rupees'=>$_REQUEST['pickupvalue'],
					'final_delivery_rupees'=>$_REQUEST['deliveryvalue'],
					'final_oda_rupees'=>$_REQUEST['odavalue'],
					'final_transit_days'=>$_REQUEST['transitrValue'],
					'final_quote_created_at'=>$final_cretaed));
			CommonComponent::auditLog($buyerId,'airint_buyer_quote_sellers_quotes_prices');

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
	public static function AirInternationalsellerSearchQuoteSubmit() {
		try{
	
			$formvalues = urldecode($_REQUEST['formvalues']);
			$formfields = explode("&", $formvalues);
			$hiddenfields = array();
				
				
				
			foreach($formfields as $formfield){
				$input = explode("=", $formfield);
				$hiddenfields[$input[0]] = $input[1];
			}
			
			
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
			
			$getBuyerpostdetails  = DB::table('airint_buyer_quote_items')
			->leftjoin('airint_buyer_quotes', 'airint_buyer_quotes.id', '=', 'airint_buyer_quote_items.buyer_quote_id')
			->where('airint_buyer_quote_items.id','=',$buyerQuoteItemId)
			->where('airint_buyer_quotes.created_by','=',$buyerId)
			->select('airint_buyer_quotes.*')
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
				$randnumber = 'AIRINTERNATIONAL/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
					
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
				$Date1 = date('Y-m-d', strtotime($nowdate. " + 30 days"));
				$createdIp = $_SERVER['REMOTE_ADDR'];
				$createsellerpost = new AirintSellerPost();
				$createsellerpost->lkp_service_id = AIR_INTERNATIONAL;
				$createsellerpost->from_date = $nowdate;
				$createsellerpost->to_date =$to;
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
				$createsellerpost->lkp_ptl_post_type_id = 2;
				$createsellerpost->kg_per_cft = $sellerInput['kgpercftValue'];
				$createsellerpost->pickup_charges = $sellerInput['pickupvalue'];
				$createsellerpost->delivery_charges = $sellerInput['deliveryvalue'];
				$createsellerpost->oda_charges = $sellerInput['odavalue'];
				$createsellerpost->cancellation_charge_price = '0.00';
				$createsellerpost->docket_charge_price = '0.00';
				$createsellerpost->transaction_id = $randnumber;
				$createsellerpost->lkp_access_id = 3;
				$createsellerpost->created_at = $created_at;
				$createsellerpost->created_by = Auth::user()->id;
				$createsellerpost->created_ip = $createdIp;
				$createsellerpost->save();
				//CommonComponent::auditLog($createsellerpost->id,'seller_posts');
				//echo $createsellerpost->id;

				//frieght amount
			$totalfrieghtamount=0;
			for($i=0;$i<$_REQUEST['incrementcount'];$i++){
				if(isset($hiddenfields['volumetric_'.$i])){
					$volumeweight = $hiddenfields['volumetric_'.$i]*$_REQUEST['kgpercftValue']*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'];
					$densityweight= $hiddenfields['units_'.$i]*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'];
					if($volumeweight >= $densityweight)
						$totalfrieghtamount = $volumeweight+$totalfrieghtamount;
					else
						$totalfrieghtamount = $densityweight+$totalfrieghtamount;
				}
			}

			//total amount
			$total_final_amount = $totalfrieghtamount ;
			
			$fromloc = CommonComponent::getAirportId($sellerInput['from_city_loc']);
			$toloc = CommonComponent::getAirportId($sellerInput['to_city_loc']);
		
			
			//echo $delivery_date." ".$dispatch_date." ".$load_type." ".$vehicle_type." ".$tansitdays;exit;
			$createsellerpostitem = new AirintSellerPostItem();
			$createsellerpostitem->seller_post_id = $createsellerpost->id;
			$createsellerpostitem->from_location_id = $fromloc;
			$createsellerpostitem->to_location_id =$toloc;
			$createsellerpostitem->lkp_district_id =1;
			$createsellerpostitem->lkp_post_status_id = 2;
			$createsellerpostitem->is_private = 1;
			$createsellerpostitem->transitdays = $sellerInput['transitrValue'];
			$createsellerpostitem->units = 'Days';
			$createsellerpostitem->price = $_REQUEST['rateperkgValue'];
			$createsellerpostitem->created_by = Auth::user()->id;
			$createsellerpostitem->created_at = $created_at;
			$createsellerpostitem->created_ip = $createdIp;
			$createsellerpostitem->save();
			//CommonComponent::auditLog($createsellerpostitem->id,'seller_post_items');
				
				
				
			$sellerselectedbuyer = new AirintSellerSellectedBuyer();
			$sellerselectedbuyer->seller_post_id = $createsellerpost->id;
			$sellerselectedbuyer->buyer_id = $buyerId;
			$sellerselectedbuyer->created_by = Auth::user()->id;
			$sellerselectedbuyer->created_at = $created_at;
			$sellerselectedbuyer->created_ip = $createdIp;
			$sellerselectedbuyer->save();
			//CommonComponent::auditLog($sellerselectedbuyer->id,'seller_selected_buyers');
				
			//code added by swathi for updating values from market leads	
                        $getbqsqp = DB::table('airint_buyer_quote_sellers_quotes_prices as bqsp')
                        ->where('bqsp.buyer_id','=',$buyerId)
                        ->where('bqsp.buyer_quote_id','=',$_REQUEST['cbuyerquoteid'])
                        ->where('bqsp.seller_id','=',Auth::user()->id)
                        ->select('bqsp.id')
                        ->get();
                        if(count($getbqsqp)>0){
                                        $initial_cretaed = date ( 'Y-m-d H:i:s' );

                                        DB::table('airint_buyer_quote_sellers_quotes_prices')
                                        ->where('airint_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
                                        ->where('airint_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$_REQUEST['cbuyerquoteid'])
                                        ->where('airint_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
                                        ->update(array('initial_quote_price' =>$total_final_amount,
                                        'initial_transit_days' => $sellerInput['transitrValue'],
                                        'seller_post_item_id'=>$createsellerpostitem->id,
                                            
                                        'private_seller_quote_id' => $createsellerpost->id,
                                        'initial_freight_amount'=>$totalfrieghtamount,    
                                        'initial_rate_per_kg' => $sellerInput['rateperkgValue'],
                                        'initial_kg_per_cft'=>$sellerInput['kgpercftValue'], 
                                        'initial_pick_up_rupees' => $sellerInput['pickupvalue'],
                                        'initial_delivery_rupees'=>$sellerInput['deliveryvalue'],    
                                        'initial_oda_rupees' => $sellerInput['odavalue'],
                                        'initial_quote_created_at' =>$initial_cretaed));



                    }
                    else{  	
			$initial_cretaed = date ( 'Y-m-d H:i:s' );
			$buyerinitial = new AirintBuyerQuoteSellersQuotesPrice();
			$buyerinitial->buyer_id = $buyerId;
			$buyerinitial->buyer_quote_id = $_REQUEST['cbuyerquoteid'];
			$buyerinitial->seller_id =Auth::user()->id;
			$buyerinitial->private_seller_quote_id =$createsellerpost->id;
			$buyerinitial->initial_quote_price = $total_final_amount;
			$buyerinitial->initial_freight_amount = $totalfrieghtamount;
			$buyerinitial->initial_rate_per_kg = $sellerInput['rateperkgValue'];
			$buyerinitial->initial_kg_per_cft = $sellerInput['kgpercftValue'];
			$buyerinitial->initial_pick_up_rupees = $sellerInput['pickupvalue'];
			$buyerinitial->initial_delivery_rupees = $sellerInput['deliveryvalue'];
			$buyerinitial->initial_oda_rupees = $sellerInput['odavalue'];
			$buyerinitial->initial_transit_days = $sellerInput['transitrValue'];
			$buyerinitial->seller_post_item_id =$createsellerpostitem->id;
			$buyerinitial->created_at = $created_at;
			$buyerinitial->created_by = Auth::user()->id;
			$buyerinitial->created_ip = $createdIp;
			$buyerinitial->initial_quote_created_at = $initial_cretaed;
			$buyerinitial->save();
                    }
			//CommonComponent::auditLog($buyerinitial->id,'buyer_quote_sellers_quotes_prices');
			$seller_initial_quote_email = DB::table('users')->where('id', $buyerId)->get();
			$seller_initial_quote_email[0]->sellername = Auth::User()->username;
				
			CommonComponent::send_email(INITIAL_COUNT_BY_SELLER,$seller_initial_quote_email);
			
			//*******matching engine***********************//
			$matchedItems = array ();
			$matchedItems['from_location_id']=$fromloc;
			$matchedItems['to_location_id']=$toloc;
			$matchedItems['valid_from']=$nowdate;
			$matchedItems['valid_to']=$Date1;
			$matchedItems['is_private']=1;
			$matchedItems['transit_days']=$sellerInput['transitrValue'];
			SellerMatchingComponent::doMatching(Session::get('service_id'), $createsellerpostitem->id, 2, $matchedItems);
			
			//*******matching engine***********************//
				
			
			//*******Send Sms to the buyers,seller submit a quote from a serach***********************//
			$msg_params = array(
					'randnumber' => $getBuyerpostdetails[0]->transaction_id,
					'sellername' => Auth::User()->username,
					'servicename' => 'Air International'
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
	public static function AirInternationalsellerCounterAcceptance($request) { 
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
			//echo $buyerId." ".$buyerQuoteItemId." ".$sellerInput['seller_post_item_id'];exit;
			
			if(isset($_REQUEST['cbuyerquoteid'])){
				$buyerQuoteItemId = $_REQUEST['cbuyerquoteid'];
			
			$getcounter = DB::table('airint_buyer_quote_sellers_quotes_prices')
			->where('airint_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
			->where('airint_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$buyerQuoteItemId)
			//->where('airint_buyer_quote_sellers_quotes_prices.seller_post_item_id','=',$sellerInput['seller_post_item_id'])
			->where('airint_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
			->select('airint_buyer_quote_sellers_quotes_prices.*')
					->get();
			}else{
				$getcounter = DB::table('airint_buyer_quote_sellers_quotes_prices')
				->where('airint_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
				->where('airint_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$buyerQuoteItemId)
				->where('airint_buyer_quote_sellers_quotes_prices.seller_post_item_id','=',$sellerInput['seller_post_item_id'])
				->where('airint_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
				->select('airint_buyer_quote_sellers_quotes_prices.*')
				->get();
			}
			
			if(count($getcounter)>0){
				$final_cretaed = date ( 'Y-m-d H:i:s' );
				$updatefinal= DB::table('airint_buyer_quote_sellers_quotes_prices')
				->where('airint_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
				->where('airint_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$buyerQuoteItemId)
				->where('airint_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
				->update(array('final_quote_price' =>$getcounter[0]->counter_quote_price,
						'final_freight_amount' =>$getcounter[0]->counter_freight_amount,
						'final_rate_per_kg' =>$getcounter[0]->counter_rate_per_kg,
						'final_kg_per_cft' =>$getcounter[0]->counter_kg_per_cft,
						'final_pick_up_rupees' =>$getcounter[0]->initial_pick_up_rupees,
						'final_delivery_rupees' =>$getcounter[0]->initial_delivery_rupees,
						'final_oda_rupees' =>$getcounter[0]->initial_oda_rupees,
						'final_transit_days' =>$getcounter[0]->initial_transit_days,
						'seller_acceptence'=>1,
						'final_quote_created_at'=>$final_cretaed));

				$seller_accept_counter_offe_email = DB::table('users')->where('id', $buyerId)->get();
				$seller_accept_counter_offe_email[0]->sellername = Auth::User()->username;
				CommonComponent::send_email(ACCEPTED_COUNTER_QUOTE,$seller_accept_counter_offe_email);
				Session::put('message', 'Final Quote given successfully');
				
	
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
	public static function AirInternationalsellerQuoteAcceptance($id,$bqid,$spqi=null) {
		try{
				
				
			$roleId = Auth::User()->lkp_role_id;
	
			$getcounter = DB::table('ptl_buyer_quote_sellers_quotes_prices')
			->where('ptl_buyer_quote_sellers_quotes_prices.buyer_id','=',$id)
			->where('ptl_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$bqid)
			->where('ptl_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
			->select('ptl_buyer_quote_sellers_quotes_prices.*')
					->get();
			if(count($getcounter)>0){
				
				$final_cretaed = date ( 'Y-m-d H:i:s' );
				$updatefinal= DB::table('ptl_buyer_quote_sellers_quotes_prices')
				->where('ptl_buyer_quote_sellers_quotes_prices.buyer_id','=',$id)
				->where('ptl_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$bqid)
				->where('ptl_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
				->update(array('final_quote_price' =>$getcounter[0]->counter_quote_price,
						'final_freight_amount' =>$getcounter[0]->counter_freight_amount,
						'final_rate_per_kg' =>$getcounter[0]->counter_rate_per_kg,
						'final_kg_per_cft' =>$getcounter[0]->counter_kg_per_cft,
						'final_pick_up_rupees' =>$getcounter[0]->initial_pick_up_rupees,
						'final_delivery_rupees' =>$getcounter[0]->initial_delivery_rupees,
						'final_oda_rupees' =>$getcounter[0]->initial_oda_rupees,
						'final_transit_days' =>$getcounter[0]->initial_transit_days,
						'seller_acceptence'=>1,
						'final_quote_created_at'=>$final_cretaed));
				//CommonComponent::auditLog($id,'ptl_buyer_quote_sellers_quotes_prices');

				$seller_accept_counter_offe_email = DB::table('users')->where('id', $id)->get();
				$seller_accept_counter_offe_email[0]->sellername = Auth::User()->username;
				CommonComponent::send_email(ACCEPTED_COUNTER_QUOTE,$seller_accept_counter_offe_email);
				Session::put('message', 'Final Quote given successfully');
				
	
			}
		} catch( Exception $e ) {
			return $e->message;
		}
		return Redirect::back();
	}
	
	
}
