<?php

namespace App\Components\Courier;

use DB;
use Auth;
use App\Http\Requests;
use Input;
use Config;
use File;
use Session;
use Redirect;
use Log;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;


use App\Models\CourierBuyerQuote;
use App\Models\CourierBuyerQuoteItem;
use App\Models\CourierBuyerQuoteSelectedSeller;
use App\Models\CourierBuyerQuoteSellersQuotesPrice;



use App\Models\User;
use App\Components\CommonComponent;
use App\Components\SellerComponent;
use App\Components\BuyerComponent;
use App\Components\AirDomestic\AirDomesticBuyerGetQuoteBooknowComponent;
use App\Components\Search\BuyerSearchComponent;
use App\Components\Ptl\PtlBuyerComponent;
use App\Models\ViewCartItem;

class CourierBuyerComponent {
    /**
     * Buyer Quote Creation page For PTL
     * insert data into buyer quote ptl table
     */
    public static function BuyerQuoteMainData($allRequestdata,$commercial=0) {
        try {
            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER ['REMOTE_ADDR'];
            $serviceId = Session::get('service_id');
            $ordid  =   CommonComponent::getPostID($serviceId);
            //$rand_id = rand(100000, 999999);
            $created_year = date('Y');
            
            // Gettings States ids based on from & to locations
            $stateids= CommonComponent::getStatebyPincode( $allRequestdata['ptlFromLocation'],
                $allRequestdata['ptlToLocation']
            );
            $incoming_docs = $outgoing_docs = null;
            if($stateids->from_state_id != $stateids->to_state_id):
                $documents =  CommonComponent::getStatutoryDocs(array('from_state_id'=>$stateids->from_state_id,'to_state_id'=>$stateids->to_state_id));
                $incoming_docs = $documents->incoming_doc_id;
                $outgoing_docs = $documents->outgoing_doc_id;
            endif;

            /** ****Single insert in buyer quote table******** */
            switch ($serviceId) {
                case COURIER :
                $randString = 'COURIER/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                $BuyerQuote = new CourierBuyerQuote();
                $BuyerQuote->lkp_service_id = COURIER;
                break;
            }

            $BuyerQuote->lkp_lead_type_id = SPOT;
            if (isset($allRequestdata['ptlQuoteaccessId'])) {
                $quoteAccessId = $allRequestdata['ptlQuoteaccessId'];
                $BuyerQuote->lkp_quote_access_id = $quoteAccessId;
            }
            
            $BuyerQuote->transaction_id = $randString;
            $BuyerQuote->dispatch_date = CommonComponent::convertDateForDatabase($allRequestdata['ptlDispatchDate']);
            $BuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($allRequestdata['ptlDeliveryhDate']);
            $BuyerQuote->from_location_id = $allRequestdata['ptlFromLocation'];
            $BuyerQuote->to_location_id = $allRequestdata['ptlToLocation'];
            $BuyerQuote->is_dispatch_flexible = $allRequestdata['ptlFlexiableDispatch'];
            $BuyerQuote->is_delivery_flexible = $allRequestdata['ptlFlexiableDelivery'];
            $BuyerQuote->lkp_post_status_id = OPEN;
            $BuyerQuote->is_commercial = $commercial;

            // Added for GSA docs
            $BuyerQuote->incoming_docs = $incoming_docs;
            $BuyerQuote->outgoing_docs = $outgoing_docs;

            $BuyerQuote->buyer_id = Auth::id();
            $BuyerQuote->created_by = Auth::id();
            $BuyerQuote->created_at = $created_at;
            $BuyerQuote->created_ip = $createdIp;
            if ($BuyerQuote->save()) {
               return ['buyerQuoteId'=>$BuyerQuote->id,'transactionId'=>$BuyerQuote->transaction_id];
            }
            return 0;
        } catch (Exception $e) {
            
        }
    }

    public static function BuyerQuoteItems($allRequestdata, $BuyerMaindataId,$fromcities) {
        try {
            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER ['REMOTE_ADDR'];
            $serviceId = Session::get('service_id');
            $transid  =   CommonComponent::getBuyerPostDetails($BuyerMaindataId,$serviceId);

            //echo "<pre>"; print_r($_REQUEST); echo "</pre>"; exit; 
            if (isset($allRequestdata['ptlUnitsWeight'])) {
                //$multi_data_count = count($allRequestdata['ptlUnitsWeight']);
                //for ($i = 0; $i < $multi_data_count; $i++) {
                /******Multiple insert in PTL quote items******** */
                switch ($serviceId) {
                    case COURIER :
                    $Quote_Lineitems = new CourierBuyerQuoteItem();
                    break;
                    
                    default :
                    $Quote_Lineitems = new CourierBuyerQuoteItem();
                    break;
                }
                $Quote_Lineitems->buyer_quote_id = $BuyerMaindataId;
                $Quote_Lineitems->lkp_quote_price_type_id = 1;
                $Quote_Lineitems->lkp_courier_type_id = $allRequestdata['courier_types'];
                $Quote_Lineitems->lkp_courier_delivery_type_id = $allRequestdata['post_delivery_types'];
                $Quote_Lineitems->lkp_courier_purpose_id = $allRequestdata['ptlPurposesType'];
                if($allRequestdata['courier_types'] == 2){
                $Quote_Lineitems->length = $allRequestdata['ptlLengthCourier'];
                $Quote_Lineitems->breadth = $allRequestdata['ptlWidthCourier'];
                $Quote_Lineitems->height = $allRequestdata['ptlHeightCourier'];
                $Quote_Lineitems->lkp_ptl_length_uom_id = $allRequestdata['ptlCheckVolWeightCourier'];
                }else{
                $Quote_Lineitems->length = 0;
                $Quote_Lineitems->breadth = 0;
                $Quote_Lineitems->height = 0;
                $Quote_Lineitems->lkp_ptl_length_uom_id = 0;
                }
                
                $Quote_Lineitems->calculated_volume_weight = $allRequestdata['ptlDisplayVolumeWeight'];
                $Quote_Lineitems->units = $allRequestdata['ptlUnitsWeight'];
                $Quote_Lineitems->number_packages = $allRequestdata['ptlNopackages'];
                $Quote_Lineitems->package_value = $allRequestdata['packeagevalue'];
                $Quote_Lineitems->lkp_ict_weight_uom_id = $allRequestdata['ptlCheckUnitWeight'];
                $Quote_Lineitems->lkp_post_status_id = OPEN;
                $Quote_Lineitems->created_by = Auth::id();
                $Quote_Lineitems->created_at = $created_at;
                $Quote_Lineitems->created_ip = $createdIp;
                $Quote_Lineitems->save();

                //Buyer Seller Price list code new table storing data in PTL --//print_r($_POST['seller_list']); exit;
                if (isset($allRequestdata['ptlQuoteaccessId']) == IS_ACCESS_PRIVATE) {
                    if ($allRequestdata['seller_list'] != "") {
                        $sellerList = explode(",", $allRequestdata['seller_list']);
                        $sellerListCount = count($sellerList);
                        if ($sellerListCount != 0) {
                            for ($j = 0; $j < $sellerListCount; $j ++) {
                                switch ($serviceId) {
                                    case COURIER :
                                        $checkBuyerQuoteExists =  DB::table('courier_buyer_quote_sellers_quotes_prices')
                                        ->where('buyer_quote_id', $BuyerMaindataId)
                                        ->where('seller_id', $sellerList[$j])
                                        ->get();
                                        if(count($checkBuyerQuoteExists) == 0){
                                        $QuotePriceList = new CourierBuyerQuoteSellersQuotesPrice();
                                        $QuotePriceList->buyer_id = Auth::id();
                                        //$ptlQuotePriceList->buyer_quote_item_id = $ptlQuote_Lineitems->id;
                                        $QuotePriceList->buyer_quote_id = $BuyerMaindataId;
                                        $QuotePriceList->seller_id = $sellerList[$j];
                                        $QuotePriceList->created_by = Auth::id();
                                        $QuotePriceList->created_at = $created_at;
                                        $QuotePriceList->created_ip = $createdIp;
                                        $QuotePriceList->save();
                                        }
                                        break;
                                    
                                    default :
                                        $checkBuyerQuoteExists =  DB::table('courier_buyer_quote_sellers_quotes_prices')
                                        ->where('buyer_quote_id', $BuyerMaindataId)
                                        ->where('seller_id', $sellerList[$j])
                                        ->get();
                                        if(count($checkBuyerQuoteExists) == 0){
                                        $QuotePriceList = new CourierBuyerQuoteSellersQuotesPrice();
                                        $QuotePriceList->buyer_id = Auth::id();
                                        
                                        $QuotePriceList->buyer_quote_id = $BuyerMaindataId;
                                        $QuotePriceList->seller_id = $sellerList[$j];
                                        $QuotePriceList->created_by = Auth::id();
                                        $QuotePriceList->created_at = $created_at;
                                        $QuotePriceList->created_ip = $createdIp;
                                        $QuotePriceList->save();
                                        }
                                        break;
                                }        
                                
                            }
                        }
                    }
                }

                //End Buyer Seller price insert in PTL
                //} //This braces end for above main data inser for loop
                //Buyer selected Seller Ids list code new table storing data in PTL 
                //-this loop should be started after main data insert
                if (isset($allRequestdata['ptlQuoteaccessId']) && $allRequestdata['ptlQuoteaccessId']== IS_ACCESS_PRIVATE) {
                    if ($allRequestdata['seller_list'] != "") {
                        $sellerList = explode(",", $allRequestdata['seller_list']);
                        $sellerListCount = count($sellerList);
                        if ($sellerListCount != 0) {
                            for ($k = 0; $k < $sellerListCount; $k ++) {
                                switch ($serviceId) {
                                    case COURIER :
                                    $QuoteSellerList = new CourierBuyerQuoteSelectedSeller();
                                    $servicename = 'COURIER';
                                    break;
                                    
                                    default :
                                        $QuoteSellerList = new CourierBuyerQuoteSelectedSeller();
                                        $servicename = 'LTL';
                                        break;
                                
                                }
                                $QuoteSellerList->buyer_quote_id = $BuyerMaindataId;
                                $QuoteSellerList->seller_id = $sellerList[$k];
                                $QuoteSellerList->created_by = Auth::id();
                                $QuoteSellerList->created_at = $created_at;
                                $QuoteSellerList->created_ip = $createdIp;
                                $QuoteSellerList->save();

                                //below code  for sent mails to selelcted sellers in private post
                                $buyers_selected_sellers_email = DB::table('users')->where('id', $sellerList[$k])->get();
                                $buyers_selected_sellers_email[0]->randnumber = $transid;
                                $buyers_selected_sellers_email[0]->buyername = Auth::User()->username;
                                CommonComponent::send_email(BUYER_CREATED_POST_FOR_SELLERS, $buyers_selected_sellers_email);
                                
                                //*******Send Sms to the private Sellers***********************//
                                $msg_params = array(
                                		'randnumber' => $transid,
                                		'buyername' => Auth::User()->username,
                                		'servicename' => $servicename
                                );
                                $getMobileNumber  =   CommonComponent::getMobleNumber($sellerList[$k]);
                               //echo "<pre>"; print_r($getMobileNumber);exit;
                                if($getMobileNumber)
                                CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                                //*******Send Sms to the private Sellers***********************//
                                
                            }
                        }
                    }
                }else{
			                	switch ($serviceId) {
			                		case COURIER :
			                			$servicename = 'COURIER';
			                			break;
			                	
			                		default :
			                			$servicename = 'LTL';
			                			break;
			                	
			                	}
                        		//*******Send Sms to the private Sellers***********************//
                        		$msg_params = array(
                        				'randnumber' => $transid,
                        				'buyername' => Auth::User()->username,
                        				'servicename' => $servicename
                        		);
                        		//echo "<pre>";print_r($fromcities);exit;
                        		$getSellerIds  =   CommonComponent::getAllSellerList($fromcities);
                        		//echo "<pre>";print_r($getSellerIds);exit;
                        		for($i=0;$i<count($getSellerIds);$i++){	
                        			$getMobileNumber  =   CommonComponent::getMobleNumber($getSellerIds[$i]['id']);
                        			if($getMobileNumber)
                        			CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                        		}
                        		//*******Send Sms to the private Sellers***********************//
                        	                     
                        	
                        }
            } //This braces end for check load type empty or not
        } catch (Exception $e) {
            
        }
    }
    
   
  	
    
}