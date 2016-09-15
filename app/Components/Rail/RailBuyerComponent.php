<?php

namespace App\Components\Rail;

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
use App\Models\RailBuyerQuote;
use App\Models\RailBuyerQuoteItem;
use App\Models\RailBuyerQuoteSellersQuotesPrice;
use App\Models\RailBuyerQuoteSelectedSeller;
use App\Models\BuyerQuoteItemView;
use App\Models\CartItem;
use App\Models\BuyerQuoteSellersQuotesPrices;
use App\Models\User;
use App\Components\CommonComponent;
use App\Components\SellerComponent;
use App\Components\BuyerComponent;
use App\Components\Rail\RailBuyerGetQuoteBooknowComponent;
use App\Components\Search\BuyerSearchComponent;
use App\Components\Ptl\PtlBuyerComponent;
use App\Models\ViewCartItem;

class RailBuyerComponent {

   
   
    /**
     * Buyer Quote Creation page For PTL
     * insert data into buyer quote ptl table
     */
    public static function BuyerQuoteMainData($allRequestdata,$commercial=0) {
        try {
            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER ['REMOTE_ADDR'];
            $ordid  =   CommonComponent::getPostID(Session::get ( 'service_id' ));
            //$rand_id = rand(100000, 999999);
            $created_year = date('Y');
            $randString = 'RAIL/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
            
            
            $stateids= CommonComponent::getStatebyPincode($allRequestdata['ptlFromLocation'],$allRequestdata['ptlToLocation']);
            $incoming_docs = $outgoing_docs = null;
            if($stateids->from_state_id != $stateids->to_state_id){
            	
            	$documents =  CommonComponent::getStatutoryDocs(array('from_state_id'=>$stateids->from_state_id,'to_state_id'=>$stateids->to_state_id));
            	$incoming_docs = $documents->incoming_doc_id;
            	$outgoing_docs = $documents->outgoing_doc_id;
            }
            
            
            
            //$trans_randid = $str1 . $rand_id;
            /*             * ****Single insert in PTL buer quote table******** */
            $BuyerQuote = new RailBuyerQuote();
            $BuyerQuote->lkp_service_id = RAIL;
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
            $BuyerQuote->is_door_pickup = $allRequestdata['ptlDoorpickup'];
            $BuyerQuote->is_door_delivery = $allRequestdata['ptlDoorDelivery'];
            $BuyerQuote->lkp_post_status_id = OPEN;
            $BuyerQuote->is_commercial = $commercial;
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
            $transid  =   CommonComponent::getBuyerPostDetails($BuyerMaindataId,Session::get ( 'service_id' ));
            //echo "<pre>"; print_r($_REQUEST); echo "</pre>"; exit; 
            if (isset($allRequestdata['ptlLoadType'])) {

                //for ($i = 0; $i < $multi_data_count; $i++) {
                /******Multiple insert in PTL quote items******** */
                $Quote_Lineitems = new RailBuyerQuoteItem();
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
                if (isset($allRequestdata['ptlQuoteaccessId']) && $allRequestdata['ptlQuoteaccessId']== IS_ACCESS_PRIVATE) {
                    if ($allRequestdata['seller_list'] != "") {
                        $sellerList = explode(",", $allRequestdata['seller_list']);
                        $sellerListCount = count($sellerList);
                        if ($sellerListCount != 0) {
                            for ($j = 0; $j < $sellerListCount; $j ++) {
                                $checkBuyerQuoteExists =  DB::table('rail_buyer_quote_sellers_quotes_prices')
                                ->where('buyer_quote_id', $BuyerMaindataId)
                                ->where('seller_id', $sellerList[$j])
                                ->get();
                                if(count($checkBuyerQuoteExists) == 0){
                                $QuotePriceList = new RailBuyerQuoteSellersQuotesPrice();
                                $QuotePriceList->buyer_id = Auth::id();
                                //$ptlQuotePriceList->buyer_quote_item_id = $ptlQuote_Lineitems->id;
                                $QuotePriceList->buyer_quote_id = $BuyerMaindataId;
                                $QuotePriceList->seller_id = $sellerList[$j];
                                $QuotePriceList->created_by = Auth::id();
                                $QuotePriceList->created_at = $created_at;
                                $QuotePriceList->created_ip = $createdIp;
                                $QuotePriceList->save();
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
                                $QuoteSellerList = new RailBuyerQuoteSelectedSeller();
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
                                		'servicename' => 'RAIL'
                                );
                                $getMobileNumber  =   CommonComponent::getMobleNumber($sellerList[$k]);
                                if($getMobileNumber)
                                CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                                //*******Send Sms to the private Sellers***********************// 
                                
                                
                            }
                        }
                    }
                }else{
                        		//*******Send Sms to the private Sellers***********************//
                        		$msg_params = array(
                        				'randnumber' => $transid,
                        				'buyername' => Auth::User()->username,
                        				'servicename' => 'RAIL'
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