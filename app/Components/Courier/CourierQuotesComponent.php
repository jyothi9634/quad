<?php
namespace App\Components\Courier;
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
use App\Models\CourierBuyerQuoteSellersQuotesPrice;
use App\Models\CourierSellerPost;
use App\Models\CourierSellerPostItem;
use App\Models\CourierSellerSellectedBuyer;
use App\Components\Matching\SellerMatchingComponent;


use Redirect;

class CourierQuotesComponent {
	
	/**
	 * Submitting Seller Initial Quote
	 *	
	 * @param  $request
	 * @return Response
	 */
	public static function CourierSellerQuoteSubmit($request) {
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
			if(isset($sellerInput['daysselect'])){
				if($sellerInput['daysselect'] == 1)
					$units = "Days";
				else 
					$units= "Weeks";
			}else{
				$units ="Days";
			}
				
			
			if(isset($sellerInput['buyer_buyerquote_id']) && !empty($sellerInput['buyer_buyerquote_id'])) {
			
				$buyersquotes	= DB::table('courier_buyer_quote_sellers_quotes_prices')
				->where('courier_buyer_quote_sellers_quotes_prices.buyer_quote_id',$buyerQuoteItemId)
				->where('courier_buyer_quote_sellers_quotes_prices.buyer_id',$buyerId)
				->where('courier_buyer_quote_sellers_quotes_prices.seller_id',Auth::user()->id)
				->select('courier_buyer_quote_sellers_quotes_prices.*')
				->get();
				
				
				
				$getSellerpostdetails  = DB::table('courier_seller_post_items')
				->leftjoin('courier_seller_posts','courier_seller_posts.id','=','courier_seller_post_items.seller_post_id')
				->where('courier_seller_post_items.id','=',Session::get('seller_post_item'))
				->where('courier_seller_post_items.created_by','=',Auth::user()->id)
				->select('courier_seller_posts.*','courier_seller_post_items.*')
				->get();
				
					
				$getBuyerpostdetails  = DB::table('courier_buyer_quotes')
				->where('courier_buyer_quotes.id','=',$buyerQuoteItemId)
				->where('courier_buyer_quotes.created_by','=',$buyerId)
				->select('courier_buyer_quotes.*')
				->get();
				if(count($getBuyerpostdetails)>0){
					
						$from = $getBuyerpostdetails[0]->dispatch_date;
						$to = date('Y-m-d', strtotime($from. " + 1 days"));
					
				}else{
					$from = date('Y-m-d');
					$to = date('Y-m-d', strtotime($from. " + 1 days"));
				}
				
				
				$postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
				$created_year = date('Y');
				$randnumber = 'COURIER/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
				
				date_default_timezone_set("Asia/Kolkata");
				$created_at = date ( 'Y-m-d H:i:s' );
				$nowdate = date('Y-m-d');
				if($from<$nowdate){
					
					$nowdate = $nowdate;
					$to = date('Y-m-d', strtotime($nowdate. " + 1 days"));
				}else{
				
					$nowdate = $from;
				}
				$Date1 = date('Y-m-d', strtotime($nowdate. " + ".$sellerInput['transitrValue']." days"));
				$createdIp = $_SERVER['REMOTE_ADDR'];
				$createsellerpost = new CourierSellerPost();
				$createsellerpost->lkp_service_id = COURIER;
				$createsellerpost->from_date = $nowdate;
				$createsellerpost->to_date =$to;
				
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
				$createsellerpost->lkp_ptl_post_type_id = 2;
				
				
				$createsellerpost->conversion_factor = $getSellerpostdetails[0]->conversion_factor;
				$createsellerpost->fuel_surcharge = $getSellerpostdetails[0]->fuel_surcharge;
				$createsellerpost->cod_charge = $getSellerpostdetails[0]->cod_charge;
				$createsellerpost->freight_collect_charge = $getSellerpostdetails[0]->freight_collect_charge;
				$createsellerpost->arc_charge = $getSellerpostdetails[0]->arc_charge;
				
				
				
				$createsellerpost->cancellation_charge_text = $getSellerpostdetails[0]->cancellation_charge_text;
				$createsellerpost->cancellation_charge_price = $getSellerpostdetails[0]->cancellation_charge_price;
				$createsellerpost->docket_charge_text = $getSellerpostdetails[0]->docket_charge_text;
				$createsellerpost->docket_charge_price = $getSellerpostdetails[0]->docket_charge_price;
				$createsellerpost->other_charge1_text = $getSellerpostdetails[0]->other_charge1_text;
				$createsellerpost->other_charge1_price = $getSellerpostdetails[0]->other_charge1_price;
				$createsellerpost->other_charge2_text = $getSellerpostdetails[0]->other_charge2_text;
				$createsellerpost->other_charge2_price = $getSellerpostdetails[0]->other_charge2_price;
				$createsellerpost->other_charge3_text = $getSellerpostdetails[0]->other_charge3_text;
				$createsellerpost->other_charge3_price = 
				
				$createsellerpost->transaction_id = $randnumber;
				$createsellerpost->lkp_courier_type_id = $getSellerpostdetails[0]->lkp_courier_type_id;
				$createsellerpost->lkp_courier_delivery_type_id = $getSellerpostdetails[0]->lkp_courier_delivery_type_id;
				$createsellerpost->lkp_access_id = 3;
				$createsellerpost->created_at = $created_at;
				$createsellerpost->created_by = Auth::user()->id;
				$createsellerpost->created_ip = $createdIp;
				$createsellerpost->save();
				//CommonComponent::auditLog($createsellerpost->id,'courier_seller_posts');
				
				
				//frieght amount
				
				$fromloc = $getBuyerpostdetails[0]->from_location_id;
				$toloc = $getBuyerpostdetails[0]->to_location_id;
				
				

				//echo $delivery_date." ".$dispatch_date." ".$load_type." ".$vehicle_type." ".$tansitdays;exit;
				$createsellerpostitem = new CourierSellerPostItem();
				$createsellerpostitem->seller_post_id = $createsellerpost->id;
				$createsellerpostitem->from_location_id = $getBuyerpostdetails[0]->from_location_id;
				$createsellerpostitem->to_location_id =$getBuyerpostdetails[0]->to_location_id;
				$createsellerpostitem->lkp_district_id =CommonComponent::getDistrictid($getBuyerpostdetails[0]->from_location_id);
				$createsellerpostitem->lkp_post_status_id = 2;
				$createsellerpostitem->is_private = 1;
				$createsellerpostitem->transitdays = $sellerInput['transitrValue'];
				$createsellerpostitem->units = 'Days';
				$createsellerpostitem->price = $_REQUEST['rateperkgValue'];
				$createsellerpostitem->created_by = Auth::user()->id;
				$createsellerpostitem->created_at = $created_at;
				$createsellerpostitem->created_ip = $createdIp;
				$createsellerpostitem->save();
				//CommonComponent::auditLog($createsellerpostitem->id,'courier_seller_post_items');
				
				
				
				$sellerselectedbuyer = new CourierSellerSellectedBuyer();
				$sellerselectedbuyer->seller_post_id = $createsellerpost->id;
				$sellerselectedbuyer->buyer_id = $buyerId;
				$sellerselectedbuyer->created_by = Auth::user()->id;
				$sellerselectedbuyer->created_at = $created_at;
				$sellerselectedbuyer->created_ip = $createdIp;
				$sellerselectedbuyer->save();
				//CommonComponent::auditLog($sellerselectedbuyer->id,'courier_seller_selected_buyers');
				
				
				//frieght amount
				$totalfrieghtamount=0;
				$packagescount =0;
				$packagesvalue =0;
				$totalkg=0;
				for($i=0;$i<$_REQUEST['incrementcount'];$i++){
					if(isset($hiddenfields['volumetric_'.$i])){
						if($hiddenfields['volumetric_'.$i]!='0'){
							if($_REQUEST['kgpercftValue']!=0)
								$volumeweight = ($hiddenfields['volumetric_'.$i]*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'])/$_REQUEST['kgpercftValue'];
							else
								$volumeweight = 0;
							$densityweight = $hiddenfields['units_'.$i]*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'];
							if($volumeweight >= $densityweight){
								$totalfrieghtamount = $volumeweight+$totalfrieghtamount;
								$totalkg +=($hiddenfields['volumetric_'.$i]/$_REQUEST['kgpercftValue'])*1000;
							}
							else{
								$totalfrieghtamount = $densityweight+$totalfrieghtamount;
								$totalkg += $hiddenfields['units_'.$i]*1000;
							}
						}
						else{
							$densityweight = $hiddenfields['units_'.$i]*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'];
							$totalfrieghtamount = $densityweight+$totalfrieghtamount;
						}
						$packagescount += $hiddenfields['packagenos_'.$i];
						$packagesvalue += $hiddenfields['packagevalue_'.$i];
					}
				}
					
				$fuelpercentage = ($_REQUEST['fuelvalue']/100)* $totalfrieghtamount;
				$codpercentage = ($_REQUEST['codvalue']/100)*($packagescount*$packagesvalue);
				$arcpercentage = ($_REQUEST['arcvalue']/100)*($packagescount*$packagesvalue);
					
				$initialprize = DB::table('courier_buyer_quote_sellers_quotes_prices')
				->where('courier_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
				->where('courier_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$_REQUEST['cbuyerquoteid'])
				->where('courier_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
				->select('courier_buyer_quote_sellers_quotes_prices.id')
				->get();
				
				if($getSellerpostdetails[0]->lkp_payment_mode_id==2){
					$total_initail_amount = $totalfrieghtamount + $fuelpercentage +$codpercentage + $_REQUEST['freightvalue'] + $arcpercentage ;
				}else{
					$total_initail_amount = $totalfrieghtamount + $fuelpercentage +$codpercentage + $arcpercentage ;
				}	
				
				
				if(count($buyersquotes)==0){
				$initial_cretaed = date ( 'Y-m-d H:i:s' );
				$buyerinitial = new CourierBuyerQuoteSellersQuotesPrice();
				$buyerinitial->buyer_id = $buyerId;
				$buyerinitial->buyer_quote_id = $_REQUEST['cbuyerquoteid'];
				$buyerinitial->seller_id =Auth::user()->id;
				$buyerinitial->private_seller_quote_id =$createsellerpost->id;
				$buyerinitial->initial_quote_price = $total_initail_amount;
				$buyerinitial->initial_freight_amount = $totalfrieghtamount;
				$buyerinitial->initial_rate_per_kg = $sellerInput['rateperkgValue'];
				$buyerinitial->initial_conversion_factor = $sellerInput['kgpercftValue'];
				$buyerinitial->initial_fuel_surcharge_rupees = $sellerInput['fuelvalue'];
				$buyerinitial->initial_cod_rupees = $sellerInput['codvalue'];
				$buyerinitial->initial_freight_collect_rupees = $sellerInput['freightvalue'];
				$buyerinitial->initial_arc_rupees = $sellerInput['arcvalue'];
				$buyerinitial->initial_transit_days = $sellerInput['transitrValue'];
				$buyerinitial->initial_transit_units = $units;
				$buyerinitial->seller_post_item_id =$createsellerpostitem->id;
				$buyerinitial->created_at = $created_at;
				$buyerinitial->created_by = Auth::user()->id;
				$buyerinitial->created_ip = $createdIp;
				$buyerinitial->initial_quote_created_at = $initial_cretaed;
				$buyerinitial->save();
				//CommonComponent::auditLog($buyerinitial->id,'courier_buyer_quote_sellers_quotes_prices');
				$seller_initial_quote_email = DB::table('users')->where('id', $buyerId)->get();
				$seller_initial_quote_email[0]->sellername = Auth::User()->username;
				}
				else{
					$initial_cretaed = date ( 'Y-m-d H:i:s' );
					DB::table('courier_buyer_quote_sellers_quotes_prices')
					->where('courier_buyer_quote_sellers_quotes_prices.buyer_quote_id',$buyerQuoteItemId)
					->where('courier_buyer_quote_sellers_quotes_prices.buyer_id',$buyerId)
					->where('courier_buyer_quote_sellers_quotes_prices.seller_id',Auth::user()->id)
					->update(array(
					'seller_post_item_id' =>$createsellerpostitem->id,
					'private_seller_quote_id' =>$createsellerpost->id,
					'initial_quote_price' =>$total_initail_amount,
					'initial_freight_amount'=>$totalfrieghtamount,
					'initial_rate_per_kg'=> $sellerInput['rateperkgValue'],
					'initial_conversion_factor'=>$sellerInput['kgpercftValue'],
					'initial_fuel_surcharge_rupees'=>$sellerInput['fuelvalue'],
					'initial_cod_rupees'=>$sellerInput['codvalue'],
					'initial_freight_collect_rupees'=>$sellerInput['freightvalue'],
					'initial_arc_rupees'=>$sellerInput['arcvalue'],
					'initial_transit_days'=>$sellerInput['transitrValue'],
					'initial_transit_units'=>$units,
					'initial_quote_created_at'=>$initial_cretaed));
					CommonComponent::auditLog($buyerId,'courier_buyer_quote_sellers_quotes_prices');
					$seller_initial_quote_email = DB::table('users')->where('id', $buyerId)->get();
					$seller_initial_quote_email[0]->sellername = Auth::User()->username;
				}
				
				
				//*******matching engine***********************//
				$matchedItems = array ();
				$matchedItems['zone_or_location']=2;
				$matchedItems['from_location_id']=$fromloc;
				$matchedItems['to_location_id']=$toloc;
				$matchedItems['valid_from']=$nowdate;
				$matchedItems['valid_to']=$Date1;
				$matchedItems['is_private']=1;
				$matchedItems['transit_days']=$sellerInput['transitrValue'];
				SellerMatchingComponent::doMatching(COURIER, $createsellerpostitem->id, 2, $matchedItems);
				
				//*******matching engine***********************//
				
				CommonComponent::send_email(INITIAL_COUNT_BY_SELLER,$seller_initial_quote_email);
				
				//*******Send Sms to the buyers,from seller submit a quote ***********************//
				$msg_params = array(
						'randnumber' => $getBuyerpostdetails[0]->transaction_id,
						'sellername' => Auth::User()->username,
						'servicename' => 'COURIER'
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
	public static function CourierSellerFinalQuoteSubmit($request) {
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
			}
			
			if(isset($sellerInput['daysselect'])){
				if($sellerInput['daysselect'] == 1)
					$units = "Days";
				else
					$units= "Weeks";
			}else{
				$units ="Days";
			}
			
			//frieght amount
			$totalfrieghtamount=0;
					$packagescount =0;
					$packagesvalue =0;
					$totalkg=0;
					for($i=0;$i<$_REQUEST['incrementcount'];$i++){
						if(isset($hiddenfields['volumetric_'.$i])){
							if($hiddenfields['volumetric_'.$i]!='0'){
								if($_REQUEST['kgpercftValue']!=0)
									$volumeweight = ($hiddenfields['volumetric_'.$i]*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'])/$_REQUEST['kgpercftValue'];
								else 
									$volumeweight =0;
								$densityweight = $hiddenfields['units_'.$i]*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'];
								if($volumeweight >= $densityweight){
									$totalfrieghtamount = $volumeweight+$totalfrieghtamount;
									$totalkg +=$hiddenfields['volumetric_'.$i]/$_REQUEST['kgpercftValue']*1000;
								}
								else{
									$totalfrieghtamount = $densityweight+$totalfrieghtamount;
									$totalkg += $hiddenfields['units_'.$i]*1000;
								}
							}
							else{
								$densityweight = $hiddenfields['units_'.$i]*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'];
								$totalfrieghtamount = $densityweight+$totalfrieghtamount;
							}
							$packagescount += $hiddenfields['packagenos_'.$i];
							$packagesvalue += $hiddenfields['packagevalue_'.$i];
						}
					}
					
					$fuelpercentage = ($_REQUEST['fuelvalue']/100)* $totalfrieghtamount;
					$codpercentage = ($_REQUEST['codvalue']/100)*($packagescount*$packagesvalue);
					$arcpercentage = ($_REQUEST['arcvalue']/100)*($packagescount*$packagesvalue);
					
					
					
					if($sellerInput['seller_post_item_id']!=''){
						$sellerpostidslabs   = DB::table('courier_seller_post_items')
								        ->leftjoin ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
								        ->leftjoin ( 'courier_seller_post_item_slabs', 'courier_seller_post_item_slabs.seller_post_id', '=', 'courier_seller_post_items.seller_post_id' )
										->where('courier_seller_post_items.id','=',$sellerInput['seller_post_item_id'])
										->where('courier_seller_post_items.created_by','=',Auth::user()->id)
										->select('courier_seller_post_item_slabs.*','courier_seller_post_items.seller_post_id','courier_seller_posts.increment_weight','courier_seller_posts.rate_per_increment',
			                                     'courier_seller_posts.lkp_payment_mode_id')
										->get();
					}
					
					if(isset($sellerpostidslabs[0]->lkp_payment_mode_id) && $sellerpostidslabs[0]->lkp_payment_mode_id==2){
						$total_initail_amount = $totalfrieghtamount + $fuelpercentage +$codpercentage + $_REQUEST['freightvalue'] + $arcpercentage ;
					}else{
						$total_initail_amount = $totalfrieghtamount + $fuelpercentage +$codpercentage + $arcpercentage ;
					}
					
					
					
					
					$slabcount=0;
					$slabprice=0;

					for($i=0;$i<count($sellerpostidslabs);$i++){
						$slabcount += $sellerpostidslabs[$i]->slab_max_rate-$sellerpostidslabs[$i]->slab_min_rate;
						$slabprice += $sellerpostidslabs[$i]->price;
						
					}
					

			$final_cretaed = date ( 'Y-m-d H:i:s' );
			DB::table('courier_buyer_quote_sellers_quotes_prices')
			->where('courier_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
			->where('courier_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$_REQUEST['cbuyerquoteid'])
			->where('courier_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
			->update(array(
						'final_quote_price' =>$total_initail_amount,
							'final_freight_amount'=>$totalfrieghtamount,
							'seller_acceptence'=>1,
							'final_rate_per_kg'=>$_REQUEST['rateperkgValue'],
							'final_conversion_factor'=>$_REQUEST['kgpercftValue'],
							'final_fuel_surcharge_rupees'=>$_REQUEST['fuelvalue'],
							'final_cod_rupees'=>$_REQUEST['codvalue'],
							'final_freight_collect_rupees'=>$_REQUEST['freightvalue'],
							'final_arc_rupees'=>$_REQUEST['arcvalue'],
							'final_transit_days'=>$_REQUEST['transitrValue'],
							'final_transit_units'=>$units,
							'final_quote_created_at'=>$final_cretaed));
			CommonComponent::auditLog($buyerId,'courier_buyer_quote_sellers_quotes_prices');
	
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
	public static function CouriersellerCounterAcceptance($request) {
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
					
				$getcounter = DB::table('courier_buyer_quote_sellers_quotes_prices')
				->where('courier_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
				->where('courier_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$buyerQuoteItemId)
				//->where('courier_buyer_quote_sellers_quotes_prices.seller_post_item_id','=',$sellerInput['seller_post_item_id'])
				->where('courier_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
				->select('courier_buyer_quote_sellers_quotes_prices.*')
				->get();
			}else{
				$getcounter = DB::table('courier_buyer_quote_sellers_quotes_prices')
				->where('courier_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
				->where('courier_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$buyerQuoteItemId)
				->where('courier_buyer_quote_sellers_quotes_prices.seller_post_item_id','=',$sellerInput['seller_post_item_id'])
				->where('courier_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
				->select('courier_buyer_quote_sellers_quotes_prices.*')
				->get();
			}
				
			if(count($getcounter)>0){
				$final_cretaed = date ( 'Y-m-d H:i:s' );
				DB::table('courier_buyer_quote_sellers_quotes_prices')
				->where('courier_buyer_quote_sellers_quotes_prices.buyer_id','=',$buyerId)
				->where('courier_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$buyerQuoteItemId)
				->where('courier_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::user()->id)
				->update(array(
						'final_quote_price' =>$getcounter[0]->counter_quote_price,
							'final_freight_amount'=>$getcounter[0]->counter_freight_amount,
							'seller_acceptence'=>1,
							'final_rate_per_kg'=>$getcounter[0]->counter_rate_per_kg,
							'final_conversion_factor'=>$getcounter[0]->counter_conversion_factor,
							'final_fuel_surcharge_rupees'=>$getcounter[0]->initial_fuel_surcharge_rupees,
							'final_cod_rupees'=>$getcounter[0]->initial_cod_rupees,
							'final_freight_collect_rupees'=>$getcounter[0]->initial_freight_collect_rupees,
							'final_arc_rupees'=>$getcounter[0]->initial_arc_rupees,
							'final_transit_days'=>$getcounter[0]->initial_transit_days,
							'final_transit_units'=>$getcounter[0]->initial_transit_units,
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
	 * Submitting Seller Initial Quote for search buyer quotes
	 *
	 * @param $request
	 * @return Response
	 */
	public static function CouriersellerSearchQuoteSubmit($request) {
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
		
			if($sellerInput['courierdeliverytype']==2)
				$courierdelivery = 2;
			else
				$courierdelivery = 1;
			
			if($sellerInput['couriertype']==1)
				$couriertype = 1;
			else
				$couriertype = 2;
			
			
			if(isset($sellerInput['daysselect'])){
				if($sellerInput['daysselect'] == 1)
					$units = "Days";
				else 
					$units= "Weeks";
			}else{
				$units ="Days";
			}

			$getBuyerpostdetails  = DB::table('courier_buyer_quote_items')
			->leftjoin('courier_buyer_quotes', 'courier_buyer_quotes.id', '=', 'courier_buyer_quote_items.buyer_quote_id')
			->where('courier_buyer_quote_items.id','=',$buyerQuoteItemId)
			->where('courier_buyer_quotes.created_by','=',$buyerId)
			->select('courier_buyer_quotes.*')
			->get();
			if(count($getBuyerpostdetails)>0){
				
					$from = $getBuyerpostdetails[0]->dispatch_date;
					$to = date('Y-m-d', strtotime($from. " + 1 days"));
				
			}else{
				$from = date('Y-m-d');
				$to = date('Y-m-d', strtotime($from. " + 1 days"));
			}
				
			
			DB::table('courier_seller_post_items')
			->where('courier_seller_post_items.from_location_id','=',$sellerInput['from_city_loc'])
			->where('courier_seller_post_items.to_location_id','=',$sellerInput['to_city_loc'])
			->where('courier_seller_post_items.created_by','=',Auth::user()->id)
			->select('courier_seller_post_items.seller_post_id','courier_seller_post_items.id')
			->get();
			
			
			
			$totalfrieghtamount=0;
			$packagescount =0;
			$packagesvalue =0;
			$totalkg=0;
			for($i=0;$i<$_REQUEST['incrementcount'];$i++){
				if(isset($hiddenfields['volumetric_'.$i])){
					if($hiddenfields['volumetric_'.$i]!='0'){
						if($_REQUEST['kgpercftValue']!=0)
							$volumeweight = ($hiddenfields['volumetric_'.$i]*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'])/$_REQUEST['kgpercftValue'];
						else 
							$volumeweight = 0;
						$densityweight = $hiddenfields['units_'.$i]*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'];
						if($volumeweight >= $densityweight){
							$totalfrieghtamount = $volumeweight+$totalfrieghtamount;
							$totalkg +=($hiddenfields['volumetric_'.$i]/$_REQUEST['kgpercftValue'])*1000;
						}
						else{
							$totalfrieghtamount = $densityweight+$totalfrieghtamount;
							$totalkg += $hiddenfields['units_'.$i]*1000;
						}
					}
					else{
						$densityweight = $hiddenfields['units_'.$i]*$hiddenfields['packagenos_'.$i]*$_REQUEST['rateperkgValue'];
						$totalfrieghtamount = $densityweight+$totalfrieghtamount;
					}
					$packagescount += $hiddenfields['packagenos_'.$i];
					$packagesvalue += $hiddenfields['packagevalue_'.$i];
				}
			}
				
			$fuelpercentage = ($_REQUEST['fuelvalue']/100)* $totalfrieghtamount;
			$codpercentage = ($_REQUEST['codvalue']/100)*($packagescount*$packagesvalue);
			$arcpercentage = ($_REQUEST['arcvalue']/100)*($packagescount*$packagesvalue);
			
			if($sellerInput['paymentoptions'] == 2 )
				$total_initail_amount = $totalfrieghtamount + $fuelpercentage +$codpercentage + $_REQUEST['freightvalue'] + $arcpercentage ;
			else 
				$total_initail_amount = $totalfrieghtamount + $fuelpercentage +$codpercentage + $arcpercentage ;
			
		
			$postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
			$created_year = date('Y');
			$randnumber = 'COURIER/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
				
			date_default_timezone_set("Asia/Kolkata");
			$created_at = date ( 'Y-m-d H:i:s' );
			$nowdate = date('Y-m-d');
			if($from<$nowdate){
			
				$nowdate = $nowdate;
				$to = date('Y-m-d', strtotime($nowdate. " + 1 days"));
				
			}else{
			
				$nowdate = $from;
			}
			$Date1 = date('Y-m-d', strtotime($nowdate. " + 30 days"));
			$createdIp = $_SERVER['REMOTE_ADDR'];
			$createsellerpost = new CourierSellerPost();
			$createsellerpost->lkp_service_id = COURIER;
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
			$createsellerpost->conversion_factor = $sellerInput['kgpercftValue'];
			$createsellerpost->fuel_surcharge = $sellerInput['fuelvalue'];
			$createsellerpost->cod_charge = $sellerInput['codvalue'];
			$createsellerpost->freight_collect_charge = $sellerInput['freightvalue'];
			$createsellerpost->arc_charge = $sellerInput['arcvalue'];
			$createsellerpost->cancellation_charge_price = '0.00';
			$createsellerpost->docket_charge_price = '0.00';
			$createsellerpost->transaction_id = $randnumber;	
			$createsellerpost->lkp_courier_type_id = $couriertype;
			$createsellerpost->lkp_courier_delivery_type_id = $courierdelivery;
			$createsellerpost->lkp_access_id = 3;
			$createsellerpost->created_at = $created_at;
			$createsellerpost->created_by = Auth::user()->id;
			$createsellerpost->created_ip = $createdIp;
			$createsellerpost->save();
			//CommonComponent::auditLog($createsellerpost->id,'courier_seller_posts');
			//echo $createsellerpost->id;
				
			//frieght amount
				
			$fromloc = $sellerInput['from_city_loc'];
			$toloc = $sellerInput['to_city_loc'];
				

			$district = CommonComponent::getDistrictid($fromloc);
			//echo $delivery_date." ".$dispatch_date." ".$load_type." ".$vehicle_type." ".$tansitdays;exit;
			$createsellerpostitem = new CourierSellerPostItem();
			$createsellerpostitem->seller_post_id = $createsellerpost->id;
			$createsellerpostitem->from_location_id = $fromloc;
			$createsellerpostitem->to_location_id =$toloc;
			$createsellerpostitem->lkp_district_id =$district;
			$createsellerpostitem->lkp_post_status_id = 2;
			$createsellerpostitem->is_private = 1;
			$createsellerpostitem->transitdays = $sellerInput['transitrValue'];
			$createsellerpostitem->units = 'Days';
			$createsellerpostitem->price = $_REQUEST['rateperkgValue'];
			$createsellerpostitem->created_by = Auth::user()->id;
			$createsellerpostitem->created_at = $created_at;
			$createsellerpostitem->created_ip = $createdIp;
			$createsellerpostitem->save();
			//CommonComponent::auditLog($createsellerpostitem->id,'courier_seller_post_items');
				
				
				
			$sellerselectedbuyer = new CourierSellerSellectedBuyer();
			$sellerselectedbuyer->seller_post_id = $createsellerpost->id;
			$sellerselectedbuyer->buyer_id = $buyerId;
			$sellerselectedbuyer->created_by = Auth::user()->id;
			$sellerselectedbuyer->created_at = $created_at;
			$sellerselectedbuyer->created_ip = $createdIp;
			$sellerselectedbuyer->save();
			//CommonComponent::auditLog($sellerselectedbuyer->id,'courier_seller_selected_buyers');
				
				
			$initial_cretaed = date ( 'Y-m-d H:i:s' );
			$buyerinitial = new CourierBuyerQuoteSellersQuotesPrice();
			$buyerinitial->buyer_id = $buyerId;
			$buyerinitial->buyer_quote_id = $_REQUEST['cbuyerquoteid'];
			$buyerinitial->seller_id =Auth::user()->id;
			$buyerinitial->private_seller_quote_id =$createsellerpost->id;
			$buyerinitial->initial_quote_price = $total_initail_amount;
			$buyerinitial->initial_freight_amount = $totalfrieghtamount;
			$buyerinitial->initial_rate_per_kg = $sellerInput['rateperkgValue'];
			$buyerinitial->initial_conversion_factor = $sellerInput['kgpercftValue'];
			$buyerinitial->initial_fuel_surcharge_rupees = $sellerInput['fuelvalue'];
			$buyerinitial->initial_cod_rupees = $sellerInput['codvalue'];
			$buyerinitial->initial_freight_collect_rupees = $sellerInput['freightvalue'];
			$buyerinitial->initial_arc_rupees = $sellerInput['arcvalue'];
			$buyerinitial->initial_transit_days = $sellerInput['transitrValue'];
			$buyerinitial->initial_transit_units = $units;
			$buyerinitial->seller_post_item_id =$createsellerpostitem->id;
			$buyerinitial->created_at = $created_at;
			$buyerinitial->created_by = Auth::user()->id;
			$buyerinitial->created_ip = $createdIp;
			$buyerinitial->initial_quote_created_at = $initial_cretaed;
			$buyerinitial->save();
			//CommonComponent::auditLog($buyerinitial->id,'courier_buyer_quote_sellers_quotes_prices');
			$seller_initial_quote_email = DB::table('users')->where('id', $buyerId)->get();
			$seller_initial_quote_email[0]->sellername = Auth::User()->username;
				


			//*******matching engine***********************//
			$matchedItems = array ();
			$matchedItems['zone_or_location']=2;
			$matchedItems['from_location_id']=$fromloc;
			$matchedItems['to_location_id']=$toloc;
			$matchedItems['valid_from']=$nowdate;
			$matchedItems['valid_to']=$Date1;
			$matchedItems['is_private']=1;
			$matchedItems['transit_days']=$sellerInput['transitrValue'];
			SellerMatchingComponent::doMatching(COURIER, $createsellerpostitem->id, 2, $matchedItems);

			//*******matching engine***********************//
		
			CommonComponent::send_email(INITIAL_COUNT_BY_SELLER,$seller_initial_quote_email);
			
			//*******Send Sms to the buyers,seller submit a quote from a serach***********************//
			$msg_params = array(
					'randnumber' => $getBuyerpostdetails[0]->transaction_id,
					'sellername' => Auth::User()->username,
					'servicename' => 'COURIER'
			);
			$getMobileNumber  =   CommonComponent::getMobleNumber($buyerId);
			CommonComponent::sendSMS($getMobileNumber,SELLER_SUBMITT_QOUTE_SMS,$msg_params);
			//*******Send Sms to the buyers,seller submit a quote from a serach***********************//
		
	
		} catch( Exception $e ) {
			return $e->message;
		}
		return Redirect::back();
	}
	
	
}
