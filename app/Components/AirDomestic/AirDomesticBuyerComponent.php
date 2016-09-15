<?php

namespace App\Components\AirDomestic;

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
use App\Models\AirintBuyerQuote;
use App\Models\OceanBuyerQuote;
use App\Models\OceanBuyerQuoteItem;
use App\Models\OceanBuyerQuoteSellersQuotesPrice;
use App\Models\OceanBuyerQuoteSelectedSeller;
use App\Models\AirdomBuyerQuote;
use App\Models\AirdomBuyerQuoteItem;
use App\Models\AirintBuyerQuoteItem;
use App\Models\AirintBuyerQuoteSellersQuotesPrice;
use App\Models\AirintBuyerQuoteSelectedSeller;
use App\Models\AirdomBuyerQuoteSellersQuotesPrice;
use App\Models\AirdomBuyerQuoteSelectedSeller;
use App\Models\BuyerQuoteItemView;
use App\Models\CartItem;
use App\Models\BuyerQuoteSellersQuotesPrices;
use App\Models\User;
use App\Components\CommonComponent;
use App\Components\SellerComponent;
use App\Components\BuyerComponent;
use App\Components\AirDomestic\AirDomesticBuyerGetQuoteBooknowComponent;
use App\Components\Search\BuyerSearchComponent;
use App\Components\Ptl\PtlBuyerComponent;
use App\Models\ViewCartItem;

class AirDomesticBuyerComponent {

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
            /*             * ****Single insert in buyer quote table******** */
            switch ($serviceId) {
                case AIR_DOMESTIC :
                $randString = 'AIRDOMESTIC/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                $BuyerQuote = new AirdomBuyerQuote();
                $BuyerQuote->lkp_service_id = AIR_DOMESTIC;
                break;
                case AIR_INTERNATIONAL :
                $randString = 'AIRINTERNATIONAL/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                $BuyerQuote = new AirintBuyerQuote();
                $BuyerQuote->lkp_service_id = AIR_INTERNATIONAL;
                break;
                case OCEAN :
                $randString = 'OCEAN/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                $BuyerQuote = new OceanBuyerQuote();
                $BuyerQuote->lkp_service_id = OCEAN;
                break;
            }

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


            $BuyerQuote->lkp_lead_type_id = FTL_SPOT;
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
            
            if($serviceId==AIR_DOMESTIC){
                $BuyerQuote->is_door_pickup = $allRequestdata['ptlDoorpickup'];
                $BuyerQuote->is_door_delivery = $allRequestdata['ptlDoorDelivery'];
                
                // Added for GSA docs
                $BuyerQuote->incoming_docs = $incoming_docs;
                $BuyerQuote->outgoing_docs = $outgoing_docs;
            }
            
            if($serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN){
                $BuyerQuote->lkp_air_ocean_shipment_type_id = $allRequestdata['ptlShipmentType'];
                $BuyerQuote->lkp_air_ocean_sender_identity_id = $allRequestdata['ptlSenderIdentity'];
                $BuyerQuote->ie_code = $allRequestdata['ptlIECode'];
                $BuyerQuote->product_made = $allRequestdata['ptlProductMade'];
            }
            
            $BuyerQuote->lkp_post_status_id = OPEN;
            $BuyerQuote->is_commercial = $commercial;


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

    public static function BuyerQuoteItems($allRequestdata, $BuyerMaindataId, $fromcities) {
        try {
            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER ['REMOTE_ADDR'];
            $serviceId = Session::get('service_id');
            $transid  =   CommonComponent::getBuyerPostDetails($BuyerMaindataId,$serviceId);
            if (isset($allRequestdata['ptlLoadType'])) {
                /******Multiple insert in PTL quote items******** */
                switch ($serviceId) {
                    case AIR_DOMESTIC :
                    $Quote_Lineitems = new AirdomBuyerQuoteItem();
                    break;
                    case AIR_INTERNATIONAL :
                    $Quote_Lineitems = new AirintBuyerQuoteItem();
                    break;
                    case OCEAN :
                    $Quote_Lineitems = new OceanBuyerQuoteItem();
                    break;
                    default :
                    $Quote_Lineitems = new AirdomBuyerQuoteItem();
                    break;
                }
                $Quote_Lineitems->buyer_quote_id = $BuyerMaindataId;
                $Quote_Lineitems->lkp_quote_price_type_id = 1;
                $Quote_Lineitems->lkp_load_type_id = $allRequestdata['ptlLoadType'];
                $Quote_Lineitems->lkp_packaging_type_id = $allRequestdata['ptlPackageType'];
                $Quote_Lineitems->length = $allRequestdata['ptlLength'];
                $Quote_Lineitems->breadth = $allRequestdata['ptlWidth'];
                $Quote_Lineitems->height = $allRequestdata['ptlHeight'];
                $Quote_Lineitems->lkp_ptl_length_uom_id = $allRequestdata['ptlCheckVolWeight'];
                $Quote_Lineitems->calculated_volume_weight = $allRequestdata['ptlDisplayVolumeWeight'];
                $Quote_Lineitems->units = $allRequestdata['ptlUnitsWeight'];
                $Quote_Lineitems->number_packages = $allRequestdata['ptlNopackages'];
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
                                    case AIR_DOMESTIC :
                                        $checkBuyerQuoteExists =  DB::table('airdom_buyer_quote_sellers_quotes_prices')
                                        ->where('buyer_quote_id', $BuyerMaindataId)
                                        ->where('seller_id', $sellerList[$j])
                                        ->get();
                                        if(count($checkBuyerQuoteExists) == 0){
                                        $QuotePriceList = new AirdomBuyerQuoteSellersQuotesPrice();
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
                                    case AIR_INTERNATIONAL :
                                        $checkBuyerQuoteExists =  DB::table('airint_buyer_quote_sellers_quotes_prices')
                                        ->where('buyer_quote_id', $BuyerMaindataId)
                                        ->where('seller_id', $sellerList[$j])
                                        ->get();
                                        if(count($checkBuyerQuoteExists) == 0){
                                        $QuotePriceList = new AirintBuyerQuoteSellersQuotesPrice();
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
                                    case OCEAN :
                                        $checkBuyerQuoteExists =  DB::table('ocean_buyer_quote_sellers_quotes_prices')
                                        ->where('buyer_quote_id', $BuyerMaindataId)
                                        ->where('seller_id', $sellerList[$j])
                                        ->get();
                                        if(count($checkBuyerQuoteExists) == 0){
                                        $QuotePriceList = new OceanBuyerQuoteSellersQuotesPrice();
                                        $QuotePriceList->buyer_id = Auth::id();
                                        
                                        $QuotePriceList->buyer_quote_id = $BuyerMaindataId;
                                        $QuotePriceList->seller_id = $sellerList[$j];
                                        $QuotePriceList->created_by = Auth::id();
                                        $QuotePriceList->created_at = $created_at;
                                        $QuotePriceList->created_ip = $createdIp;
                                        $QuotePriceList->save();
                                        }
                                        break;
                                    default :
                                        $checkBuyerQuoteExists =  DB::table('airdom_buyer_quote_sellers_quotes_prices')
                                        ->where('buyer_quote_id', $BuyerMaindataId)
                                        ->where('seller_id', $sellerList[$j])
                                        ->get();
                                        if(count($checkBuyerQuoteExists) == 0){
                                        $QuotePriceList = new AirdomBuyerQuoteSellersQuotesPrice();
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
                                    case AIR_DOMESTIC :
                                    $QuoteSellerList = new AirdomBuyerQuoteSelectedSeller();
                                    $servicename = 'AIR DOMESTIC';
                                    break;
                                    case AIR_INTERNATIONAL :
                                        $QuoteSellerList = new AirintBuyerQuoteSelectedSeller();
                                        $servicename = 'AIR INTERNATIONAL';
                                        break;
                                    case OCEAN :
                                        $QuoteSellerList = new OceanBuyerQuoteSelectedSeller();
                                        $servicename = 'OCEAN';
                                        break;
                                    default :
                                        $QuoteSellerList = new AirdomBuyerQuoteSelectedSeller();
                                        $servicename = 'AIR DOMESTIC';
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
                                if($getMobileNumber)
                                CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                                //*******Send Sms to the private Sellers***********************//
                                
                                
                            }
                        }
                    }
                }else{			
			                	switch ($serviceId) {
			                		case AIR_DOMESTIC :
			                			$servicename = 'AIR DOMESTIC';
			                			break;
			                		case AIR_INTERNATIONAL :
			                			$servicename = 'AIR INTERNATIONAL';
			                			break;
			                		case OCEAN :
			                			$servicename = 'OCEAN';
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