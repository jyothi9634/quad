<?php

namespace App\Components\Ptl;

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
use App\Models\PtlBuyerQuote;
use App\Models\PtlBuyerQuoteItem;
use App\Models\PtlBuyerQuoteSellersQuotesPrice;
use App\Models\PtlBuyerQuoteSelectedSeller;
              
use App\Models\RailBuyerQuote;
use App\Models\RailBuyerQuoteItem;
use App\Models\RailBuyerQuoteSellersQuotesPrice;
use App\Models\RailBuyerQuoteSelectedSeller;

use App\Models\AirdomBuyerQuote;
use App\Models\AirdomBuyerQuoteItem;
use App\Models\AirdomBuyerQuoteSellersQuotesPrice;
use App\Models\AirdomBuyerQuoteSelectedSeller;

use App\Models\AirintBuyerQuote;
use App\Models\AirintBuyerQuoteItem;
use App\Models\AirintBuyerQuoteSellersQuotesPrice;
use App\Models\AirintBuyerQuoteSelectedSeller;

use App\Models\OceanBuyerQuote;
use App\Models\OceanBuyerQuoteItem;
use App\Models\OceanBuyerQuoteSellersQuotesPrice;
use App\Models\OceanBuyerQuoteSelectedSeller;

use App\Models\CourierBuyerQuote;
use App\Models\CourierBuyerQuoteItem;
use App\Models\CourierBuyerQuoteSellersQuotesPrice;
use App\Models\CourierBuyerQuoteSelectedSeller;

use App\Models\BuyerQuoteItemView;
use App\Models\CartItem;
use App\Models\BuyerQuoteSellersQuotesPrices;
use App\Models\User;
use App\Components\CommonComponent;
use App\Components\SellerComponent;
use App\Components\BuyerComponent;
use App\Components\Ptl\PtlBuyerGetQuoteBooknowComponent;
use App\Components\Search\BuyerSearchComponent;
use App\Models\ViewCartItem;
use App\Controllers\PtlBuyerController;
use App\Components\Matching\BuyerMatchingComponent;
use App\Components\MessagesComponent;
use Hamcrest\Arrays\IsArray;

class PtlBuyerComponent {

    /**
     * Get Post Buyer Counter Offer Page
     * Get details of buyer counter offer 
     * @param int $buyerQuoteItemId
     * @return type
     */
    public static function getPostBuyerCounterOfferForPtl($buyerQuoteId, $comparisonType = null,$priceVal = null,$checkIds= null) {
        try {
            Log::info('Get posted buyer counter offer for ptl: ' . Auth::id(), array('c' => '2'));
            $roleId = Auth::User()->lkp_role_id;
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_FETCHED_SELLER_POST", BUYER_FETCHED_SELLER_POST, 0, HTTP_REFERRER, CURRENT_URL);
            }
           $tableName = CommonComponent::getTableNameAsPerService(Session::get('service_id'));
           
            if(!empty($tableName)){
                $countview = PtlBuyerGetQuoteBooknowComponent::updateBuyerQuoteDetailsViews($buyerQuoteId, $tableName);
            } else {
                $countview = 0;
            }
            $arrayBuyerCounterOffer = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($buyerQuoteId);
            if (empty($arrayBuyerCounterOffer)) {
                return ['success' => '0','error' => 'Oops something went wrong.'];
            }
            $privateSellerNames = PtlBuyerGetQuoteBooknowComponent::getPrivateSellerNames($buyerQuoteId);
            $arrayBuyerQuoteSellersQuotesPrices = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteSellersQuotesPricesFromId($buyerQuoteId,$comparisonType,$priceVal,$checkIds);
            $arraySellerDetails = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteItems($buyerQuoteId,true);
            if (!empty($arrayBuyerQuoteSellersQuotesPrices)) {
                $countCartItems = BuyerComponent::getCountOfCartItems($arrayBuyerQuoteSellersQuotesPrices[0]->buyer_id, $buyerQuoteId);
            } else {
                $countCartItems = 0;
            }
			//echo "<pre>"; print_r($arraySellerDetails); die;
            $fromLocation = PtlBuyerComponent::getCityNameForPtl($arrayBuyerCounterOffer[0]->from_location_id);
            if(Session::get('service_id') == COURIER && $arrayBuyerCounterOffer[0]->courier_delivery_type == "International"){
                $toLocation = CommonComponent::getCountry($arrayBuyerCounterOffer[0]->to_location_id);
            }else{
                $toLocation = PtlBuyerComponent::getCityNameForPtl($arrayBuyerCounterOffer[0]->to_location_id);
            }
//            if ($arrayBuyerCounterOffer[0]->is_dispatch_flexible == 1) {
//                $dispatchDate = BuyerComponent::getPreviousNextThreeDays($arrayBuyerCounterOffer[0]->dispatch_date);
//            } else {
//                $dispatchDate = CommonComponent::checkAndGetDate($arrayBuyerCounterOffer[0]->dispatch_date);
//            }
//            $deliveryDate = CommonComponent::checkAndGetDate($arrayBuyerCounterOffer[0]->delivery_date);
//
//            if ($arrayBuyerCounterOffer[0]->is_delivery_flexible == 1 && !empty($deliveryDate)) {
//                $deliveryDate = BuyerComponent::getPreviousNextThreeDays($arrayBuyerCounterOffer[0]->delivery_date);
//            }
            if(isset($arrayBuyerCounterOffer[0]->is_dispatch_flexible) && $arrayBuyerCounterOffer[0]->is_dispatch_flexible == 1) {
                $dispatchDate = BuyerComponent::getPreviousNextThreeDays($arrayBuyerCounterOffer[0]->dispatch_date);
            } else {
                $dispatchDate = CommonComponent::checkAndGetDate($arrayBuyerCounterOffer[0]->dispatch_date);
            }
            $deliveryDate = CommonComponent::checkAndGetDate($arrayBuyerCounterOffer[0]->delivery_date);

            if (isset($arrayBuyerCounterOffer[0]->is_delivery_flexible) && $arrayBuyerCounterOffer[0]->is_delivery_flexible == 1 && !empty($deliveryDate)) {
                $deliveryDate = BuyerComponent::getPreviousNextThreeDays($arrayBuyerCounterOffer[0]->delivery_date);
            }
            $buyerPostCounterOfferComparisonTypes = config::get('constants.BUYER_POST_COUNTER_OFFER_COMPARISON_TYPES');
            return [
                'arrayBuyerCounterOffer' => $arrayBuyerCounterOffer,
                'privateSellerNames' => $privateSellerNames,
                'fromLocation' => $fromLocation,
                'toLocation' => $toLocation,
                'tolocationid' => $arrayBuyerCounterOffer[0]->to_location_id,
                'deliveryDate' => $deliveryDate,
                'dispatchDate' => $dispatchDate,
                'arrayBuyerQuoteSellersQuotesPrices' => $arrayBuyerQuoteSellersQuotesPrices,
                'arraySellerDetails' => $arraySellerDetails,
                'countCartItems' => $countCartItems,
                'countview' => $countview,
                'buyerPostCounterOfferComparisonTypes' => $buyerPostCounterOfferComparisonTypes,
                'comparisonType' => $comparisonType
            ];
        } catch (Exception $e) {
            
        }
    }

    /**
     * get buyer counter offer page
     * Cancel enquiry
     * @param integer $buyerQuoteItemId
     * @return type
     */
    public static function cancelEnquiry($buyerQuoteItemId) {
        Log::info('Cancel the quote enquiry for Ptl: ' . Auth::id(), array('c' => '2'));
        try {
            $roleId = Auth::User()->lkp_role_id;
            $serviceId = Session::get ( 'service_id' );
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_CANCELED_ENQUIRY", BUYER_CANCELED_ENQUIRY, 0, HTTP_REFERRER, CURRENT_URL);
            }
            //Save data into txnprojectinviteerequests
            $updatedAt = date('Y-m-d H:i:s');
            $updatedIp = $_SERVER['REMOTE_ADDR'];
            $updatedBy = Auth::User()->user_id;
            //buyer_quote_items  $booknowAddToCart->buyer_quote_item_id = $input['quoteItemId'];
            switch($serviceId){
                case ROAD_PTL  : 
            PtlBuyerQuote::where(["id" => $buyerQuoteItemId])
                    ->update(
                            array(
                                'is_cancelled' => 1,
                                'lkp_post_status_id' => CANCELLED,
                                'updated_at' => $updatedAt,
                                'updated_ip' => $updatedIp,
                                'updated_by' => $updatedBy
                            )
            );
            CommonComponent::auditLog($buyerQuoteItemId, 'ptl_buyer_quote_items');
            break;
            case RAIL       : 
            RailBuyerQuote::where(["id" => $buyerQuoteItemId])
                    ->update(
                            array(
                                'is_cancelled' => 1,
                                'lkp_post_status_id' => CANCELLED,
                                'updated_at' => $updatedAt,
                                'updated_ip' => $updatedIp,
                                'updated_by' => $updatedBy
                            )
            );            
            CommonComponent::auditLog($buyerQuoteItemId, 'rail_buyer_quote_items');
            break;
            case AIR_DOMESTIC       : 
            AirdomBuyerQuote::where(["id" => $buyerQuoteItemId])
                    ->update(
                            array(
                                'is_cancelled' => 1,
                                'lkp_post_status_id' => CANCELLED,
                                'updated_at' => $updatedAt,
                                'updated_ip' => $updatedIp,
                                'updated_by' => $updatedBy
                            )
            );
            CommonComponent::auditLog($buyerQuoteItemId, 'airdom_buyer_quote_items');
            break;
            case AIR_INTERNATIONAL       : 
            AirintBuyerQuote::where(["id" => $buyerQuoteItemId])
                    ->update(
                            array(
                                'is_cancelled' => 1,
                                'lkp_post_status_id' => CANCELLED,
                                'updated_at' => $updatedAt,
                                'updated_ip' => $updatedIp,
                                'updated_by' => $updatedBy
                            )
            );
            CommonComponent::auditLog($buyerQuoteItemId, 'airint_buyer_quote_items');
            break;
            case OCEAN       : 
            OceanBuyerQuote::where(["id" => $buyerQuoteItemId])
                    ->update(
                            array(
                                'is_cancelled' => 1,
                                'lkp_post_status_id' => CANCELLED,
                                'updated_at' => $updatedAt,
                                'updated_ip' => $updatedIp,
                                'updated_by' => $updatedBy
                            )
            );
            CommonComponent::auditLog($buyerQuoteItemId, 'ocean_buyer_quote_items');
            break;
            case COURIER       : 
            CourierBuyerQuote::where(["id" => $buyerQuoteItemId])
                    ->update(
                            array(
                                'is_cancelled' => 1,
                                'lkp_post_status_id' => CANCELLED,
                                'updated_at' => $updatedAt,
                                'updated_ip' => $updatedIp,
                                'updated_by' => $updatedBy
                            )
            );
            CommonComponent::auditLog($buyerQuoteItemId, 'courier_buyer_quote_items');
            break;
            }


            return ['cancelsuccessmessage' => 'Post deleted successfully.'];
            //Save data into txnprojectinviteerequests
        } catch (Exception $e) {
            
        }
    }

    /**
     * Get Post Buyer Counter Offer Page
     * Inserts counter offer price
     * @param Request $request
     * @return type
     */
    public static function getFreightDetailsForPtl($input) {
        try {
            Log::info('Set buyer counter offer for ptl: ' . Auth::id(), array('c' => '2'));
            $roleId = Auth::User()->lkp_role_id;
            $serviceId = Session::get('service_id');
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_INSERTED_COUNTER_OFFER", BUYER_INSERTED_COUNTER_OFFER, 0, HTTP_REFERRER, CURRENT_URL);
            }
            switch ($serviceId) {
            case ROAD_PTL:    
            $ltlBuyerQuoteItemDetails = DB::table ('ptl_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('ptl_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                                            ->leftjoin('ptl_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                                                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                                                'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees', 'pbqi.lkp_ict_weight_uom_id')
                                            ->get();
            break;
            case RAIL: 
            $ltlBuyerQuoteItemDetails = DB::table ('rail_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('rail_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                                            ->leftjoin('rail_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                                                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                                                'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees', 'pbqi.lkp_ict_weight_uom_id')
                                            ->get();
            break;
            case AIR_DOMESTIC: 
            $ltlBuyerQuoteItemDetails = DB::table ('airdom_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('airdom_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                                            ->leftjoin('airdom_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                                                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                                                'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees', 'pbqi.lkp_ict_weight_uom_id')
                                            ->get();
            break;
            case AIR_INTERNATIONAL: 
            $ltlBuyerQuoteItemDetails = DB::table ('airint_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('airint_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                                            ->leftjoin('airint_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                                                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                                                'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees', 'pbqi.lkp_ict_weight_uom_id')
                                            ->get();
            break;
            case OCEAN: 
            $ltlBuyerQuoteItemDetails = DB::table ('ocean_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('ocean_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                                            ->leftjoin('ocean_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                                                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                                                'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees', 'pbqi.lkp_ict_weight_uom_id')
                                            ->get();
            break;
            case COURIER:
                //echo 'jjj';exit;
                $ltlBuyerQuoteItemDetails = DB::table ('courier_buyer_quote_sellers_quotes_prices as pbqsqp')
                ->leftjoin('courier_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                ->leftjoin('courier_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units',
                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                'pbqi.lkp_ict_weight_uom_id')
                ->get();
                break;
            default:    
            $ltlBuyerQuoteItemDetails = DB::table ('ptl_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('ptl_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                                            ->leftjoin('ptl_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                                                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                                                'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees', 'pbqi.lkp_ict_weight_uom_id')
                                            ->get();
            break;
            
            }
            $freightAmountInTotal = [];
            if(!empty($ltlBuyerQuoteItemDetails)) {
                foreach ($ltlBuyerQuoteItemDetails as $ltlBuyerQuoteItem) {
                    $units = $ltlBuyerQuoteItem->units;
                    $numberPackages = $ltlBuyerQuoteItem->number_packages;
                    $calculatedVolume = $ltlBuyerQuoteItem->calculated_volume_weight;
                    $initialOda = $ltlBuyerQuoteItem->initial_oda_rupees;
                    $finalOda = $ltlBuyerQuoteItem->final_oda_rupees;
                    $initialPickupCharges = $ltlBuyerQuoteItem->initial_pick_up_rupees;
                    $finalPickupCharges = $ltlBuyerQuoteItem->final_pick_up_rupees;
                    $initialDeliveryCharges = $ltlBuyerQuoteItem->initial_delivery_rupees;
                    $finalDeliveryCharges = $ltlBuyerQuoteItem->final_delivery_rupees;

                    $volumetricWeight = $calculatedVolume * $input['conversionKgCftValue'];
                    if($ltlBuyerQuoteItem->lkp_ict_weight_uom_id == 2){
                        $densityWeight = CommonComponent::convertGramToKG($units);
                    } else {
                        $densityWeight = $units;
                    }
                    if($volumetricWeight > $densityWeight) {
                        $totalFreightAmount = $volumetricWeight * $input['counterRateForKgValue'] * $numberPackages;
                    } else {
                        $totalFreightAmount = $densityWeight * $input['counterRateForKgValue'] * $numberPackages;
                    }
                    array_push($freightAmountInTotal, $totalFreightAmount);
                }
            }
            $totalFreight = array_sum($freightAmountInTotal);

            $oda = BuyerComponent::getFinalDetails($initialOda, $finalOda);
            $pickUpPrice = BuyerComponent::getFinalDetails($initialPickupCharges, $finalPickupCharges);
            $deliveryPrice = BuyerComponent::getFinalDetails($initialDeliveryCharges, $finalDeliveryCharges);
            $totalAmount = $totalFreight + $oda + $pickUpPrice + $deliveryPrice;
            //Save data into txnprojectinviteerequests
            return ['oda' => $oda,
                    'formattedOda' => CommonComponent::moneyFormat($oda),
                    'pickUpPrice' => $pickUpPrice,
                    'formattedPickUpPrice' => CommonComponent::moneyFormat($pickUpPrice),
                    'deliveryPrice' => $deliveryPrice,
                    'formattedDeliveryPrice' => CommonComponent::moneyFormat($deliveryPrice),
                    'counterRatePerKg' => $input['counterRateForKgValue'],
                    'formattedCounterRatePerKg' => CommonComponent::moneyFormat($input['counterRateForKgValue']),
                    'totalFreightAmount' => $totalFreight,
                    'formattedTotalFreightAmount' => CommonComponent::moneyFormat($totalFreight),
                    'totalAmount' => $totalAmount,
                    'formattedTotalAmount' => CommonComponent::moneyFormat($totalAmount)];
        } catch (Exception $e) {
            
        }
    }
    
    public static function getSellerFreightDetailsForPtl($input) {
        try { 
            Log::info('Set buyer counter offer for ptl: ' . Auth::id(), array('c' => '2'));
            $roleId = Auth::User()->lkp_role_id;
            $serviceId = Session::get('service_id');
            $totalFreight = 0;
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_INSERTED_COUNTER_OFFER", BUYER_INSERTED_COUNTER_OFFER, 0, HTTP_REFERRER, CURRENT_URL);
            }
            switch ($serviceId) {
                case ROAD_PTL:
                    $buyerquotedetails   = DB::table('ptl_buyer_quote_items as bqi')
                                    ->leftjoin('ptl_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                                    ->where('bq.created_by','=',$input['buyerId'])
                                    ->where('bqi.buyer_quote_id','=',$input['buyerquoteId'])
                                    ->select('bqi.*')
                                    ->get();
                                
                    $totalfrieghtamount=0;
                    
                    for($i=0;$i<count($buyerquotedetails);$i++){
                        
                            $volumeweight = $buyerquotedetails[$i]->calculated_volume_weight*$input['conversionKgCftValue']*$buyerquotedetails[$i]->number_packages*$input['counterRateForKgValue'];
                            if($buyerquotedetails[$i]->lkp_ict_weight_uom_id==2)
                            	$units = $buyerquotedetails[$i]->units*0.001;
                            elseif($buyerquotedetails[$i]->lkp_ict_weight_uom_id==3)
                            	$units = $buyerquotedetails[$i]->units*1000;
                            else
                            	$units = $buyerquotedetails[$i]->units;
                            
                            $densityweight= $units*$buyerquotedetails[$i]->number_packages*$input['counterRateForKgValue'];
                            if($volumeweight >= $densityweight)
                                $totalFreight = $volumeweight+$totalFreight;
                            else
                                $totalFreight = $densityweight+$totalFreight;
                        
                    }

                    //total amount
                    $totalAmount = $totalFreight + $input['pickupvalue'] + $input['deliveryvalue'] +$input['odachargevalue'];
                    
                    
                    break;
                case RAIL:
                    $buyerquotedetails   = DB::table('rail_buyer_quote_items as bqi')
                                    ->leftjoin('rail_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                                    ->where('bq.created_by','=',$input['buyerId'])
                                    ->where('bqi.buyer_quote_id','=',$input['buyerquoteId'])
                                    ->select('bqi.*')
                                    ->get();
                    
                    $totalfrieghtamount=0;
                    for($i=0;$i<count($buyerquotedetails);$i++){
                        
                            $volumeweight = $buyerquotedetails[$i]->calculated_volume_weight*$input['conversionKgCftValue']*$buyerquotedetails[$i]->number_packages*$input['counterRateForKgValue'];
                            if($buyerquotedetails[$i]->lkp_ict_weight_uom_id==2)
                            	$units = $buyerquotedetails[$i]->units*0.001;
                            elseif($buyerquotedetails[$i]->lkp_ict_weight_uom_id==3)
                            	$units = $buyerquotedetails[$i]->units*1000;
                            else
                            	$units = $buyerquotedetails[$i]->units;
                            $densityweight= $units*$buyerquotedetails[$i]->number_packages*$input['counterRateForKgValue'];
                            if($volumeweight >= $densityweight)
                                $totalFreight = $volumeweight+$totalFreight;
                            else
                                $totalFreight = $densityweight+$totalFreight;
                        
                    }

                    //total amount
                    $totalAmount = $totalFreight + $input['pickupvalue'] + $input['deliveryvalue'] +$input['odachargevalue'];
                    
                    break;
                case AIR_DOMESTIC:
                    $buyerquotedetails   = DB::table('airdom_buyer_quote_items as bqi')
                                    ->leftjoin('airdom_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                                    ->where('bq.created_by','=',$input['buyerId'])
                                    ->where('bqi.buyer_quote_id','=',$input['buyerquoteId'])
                                    ->select('bqi.*')
                                    ->get();
                                
                    $totalfrieghtamount=0;
                    for($i=0;$i<count($buyerquotedetails);$i++){
                        
                            $volumeweight = $buyerquotedetails[$i]->calculated_volume_weight*$input['conversionKgCftValue']*$buyerquotedetails[$i]->number_packages*$input['counterRateForKgValue'];
                            if($buyerquotedetails[$i]->lkp_ict_weight_uom_id==2)
                            	$units = $buyerquotedetails[$i]->units*0.001;
                            elseif($buyerquotedetails[$i]->lkp_ict_weight_uom_id==3)
                            	$units = $buyerquotedetails[$i]->units*1000;
                            else
                            	$units = $buyerquotedetails[$i]->units;
                            
                            $densityweight= $units*$buyerquotedetails[$i]->number_packages*$input['counterRateForKgValue'];
                            if($volumeweight >= $densityweight)
                                $totalFreight = $volumeweight+$totalFreight;
                            else
                                $totalFreight = $densityweight+$totalFreight;
                        
                    }

                    //total amount
                    $totalAmount = $totalFreight + $input['pickupvalue'] + $input['deliveryvalue'] +$input['odachargevalue'];
                    
                    break;
                case AIR_INTERNATIONAL:
                    
                    
                    $buyerquotedetails   = DB::table('airint_buyer_quote_items as bqi')
                    ->leftjoin('airint_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                    ->where('bq.created_by','=',$input['buyerId'])
                    ->where('bqi.buyer_quote_id','=',$input['buyerquoteId'])
                    ->select('bqi.*')
                    ->get();
                    
                    $totalfrieghtamount=0;
                    for($i=0;$i<count($buyerquotedetails);$i++){
                    
                        $volumeweight = $buyerquotedetails[$i]->calculated_volume_weight*$input['conversionKgCftValue']*$buyerquotedetails[$i]->number_packages*$input['counterRateForKgValue'];
                        if($buyerquotedetails[$i]->lkp_ict_weight_uom_id==2)
                        	$units = $buyerquotedetails[$i]->units*0.001;
                        elseif($buyerquotedetails[$i]->lkp_ict_weight_uom_id==3)
                        $units = $buyerquotedetails[$i]->units*1000;
                        else
                        	$units = $buyerquotedetails[$i]->units;
                        
                        $densityweight= $units*$buyerquotedetails[$i]->number_packages*$input['counterRateForKgValue'];
                        if($volumeweight >= $densityweight)
                            $totalFreight = $volumeweight+$totalFreight;
                        else
                            $totalFreight = $densityweight+$totalFreight;
                    
                    }
                    
                    //total amount
                    $totalAmount = $totalFreight + $input['pickupvalue'] + $input['deliveryvalue'] +$input['odachargevalue'];
                    
                    
                    break;
                case OCEAN:
                    $buyerquotedetails   = DB::table('ocean_buyer_quote_items as bqi')
                    ->leftjoin('ocean_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                    ->where('bq.created_by','=',$input['buyerId'])
                    ->where('bqi.buyer_quote_id','=',$input['buyerquoteId'])
                    ->select('bqi.*')
                    ->get();
                  
                    $totalfrieghtamount=0;
                    for($i=0;$i<count($buyerquotedetails);$i++){
                  
                        $volumeweight = $buyerquotedetails[$i]->calculated_volume_weight*$input['conversionKgCftValue']*$buyerquotedetails[$i]->number_packages*$input['counterRateForKgValue'];
                  
                  
                        if($buyerquotedetails[$i]->lkp_ict_weight_uom_id==2)
                        	$units = $buyerquotedetails[$i]->units*0.001;
                        elseif($buyerquotedetails[$i]->lkp_ict_weight_uom_id==3)
                        	$units = $buyerquotedetails[$i]->units*1000;
                        else 
                        	$units = $buyerquotedetails[$i]->units;
                  
                        $densityweight= $units*$buyerquotedetails[$i]->number_packages*$input['counterRateForKgValue'];
                  
                        if($volumeweight >= $densityweight){
                            $totalFreight = $volumeweight+$totalFreight;
                  
                        }
                        else{
                            $totalFreight = $densityweight+$totalFreight;
                            
                        }
                    
                    }
                    
                    //total amount
                    $totalAmount = $totalFreight + $input['pickupvalue'] + $input['deliveryvalue'] +$input['odachargevalue'];
                    
                    break;
                case COURIER:
                    $buyerquotedetails   = DB::table('courier_buyer_quote_items as bqi')
                    ->leftjoin('courier_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                    ->where('bq.created_by','=',$input['buyerId'])
                    ->where('bqi.buyer_quote_id','=',$input['buyerquoteId'])
                    ->select('bqi.*')
                    ->get();
                    
                    $sellerpostidslabs   = DB::table('courier_seller_post_items')
                    ->leftjoin ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
                    ->leftjoin ( 'courier_seller_post_item_slabs', 'courier_seller_post_item_slabs.seller_post_id', '=', 'courier_seller_post_items.seller_post_id' )
                    ->where('courier_seller_post_items.created_by','=',Auth::user()->id)
                    ->select('courier_seller_posts.lkp_payment_mode_id')
                            ->get();
                    
                    
                    $totalfrieghtamount=0;
                    $packagescount =0;
                    $packagesvalue =0;
                    
                    for($i=0;$i<count($buyerquotedetails);$i++){
                    	if($input['conversionKgCftValue'] != 0)
                        	$volumeweight = ($buyerquotedetails[$i]->calculated_volume_weight*$input['counterRateForKgValue']*$buyerquotedetails[$i]->number_packages)/$input['conversionKgCftValue'];
                    	else 
                    		$volumeweight= 0;
                    	
                    	if($buyerquotedetails[$i]->lkp_ict_weight_uom_id==2)
                    		$units = $buyerquotedetails[$i]->units*0.001;
                    	elseif($buyerquotedetails[$i]->lkp_ict_weight_uom_id==3)
                    		$units = $buyerquotedetails[$i]->units*1000;
                    	else
                    		$units = $buyerquotedetails[$i]->units;
                    	
                    	
                        $densityweight= $units*$buyerquotedetails[$i]->number_packages*$input['counterRateForKgValue'];
                        
                        if($volumeweight >= $densityweight)
                            $totalFreight = $volumeweight+$totalFreight;
                        else
                            $totalFreight = $densityweight+$totalFreight;
                    	
                        $packagescount += $buyerquotedetails[$i]->number_packages;
                        $packagesvalue += $buyerquotedetails[$i]->package_value;
                    }
                   
                    $fuelpercentage = ($input['pickupvalue']/100)* $totalFreight;
                    $codpercentage = ($input['deliveryvalue']/100)*($packagescount*$packagesvalue);
                    $arcpercentage = ($input['arcvalue']/100)*($packagescount*$packagesvalue);
                       
                    if(isset($input['paymentval'])){
	                    if($input['paymentval']==1){
	                    	$totalAmount = $totalFreight + $fuelpercentage +$codpercentage + $input['odachargevalue'] + $arcpercentage ;
	                    }
	                    else{
							$totalAmount = $totalFreight + $fuelpercentage +$codpercentage + $arcpercentage ;
	                    }
                    }else{
                    	if(isset($sellerpostidslabs[0]->lkp_payment_mode_id) && $sellerpostidslabs[0]->lkp_payment_mode_id==2)
                    		$totalAmount = $totalFreight + $fuelpercentage +$codpercentage + $input['odachargevalue'] + $arcpercentage ;
                    	else
                    		$totalAmount = $totalFreight + $fuelpercentage +$codpercentage + $arcpercentage ;
                    	
                    }
                    break;
                default:
                    DB::table ('ptl_buyer_quote_sellers_quotes_prices as pbqsqp')
                    ->leftjoin('ptl_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                    ->leftjoin('ptl_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                    ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                    ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units',
                    'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                    'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                    'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees', 'pbqi.lkp_ict_weight_uom_id')
                    ->get();
                    break;
            
            }

            //Save data into txnprojectinviteerequests
            return ['oda' => $_REQUEST['odachargevalue'],
            'formattedOda' => CommonComponent::moneyFormat($_REQUEST['odachargevalue']),
            'pickUpPrice' => $_REQUEST['pickupvalue'],
            'formattedPickUpPrice' => CommonComponent::moneyFormat($_REQUEST['pickupvalue']),
            'deliveryPrice' => $_REQUEST['deliveryvalue'],
            'formattedDeliveryPrice' => CommonComponent::moneyFormat($_REQUEST['deliveryvalue']),
            'counterRatePerKg' => $input['counterRateForKgValue'],
            'formattedCounterRatePerKg' => CommonComponent::moneyFormat($input['counterRateForKgValue']),
            'totalFreightAmount' => $totalFreight,
            'formattedTotalFreightAmount' => CommonComponent::moneyFormat($totalFreight),
            'totalAmount' => $totalAmount,
            'formattedTotalAmount' => CommonComponent::moneyFormat($totalAmount)];
            } catch (Exception $e) {
            
            }
    }
    /**
     * Get Post Buyer Counter Offer Page
     * Inserts counter offer price
     * @param Request $request
     * @return type
     */
    public static function setPostBuyerCounterOfferForPtl($input) {
        try {
            Log::info('Set buyer counter offer for ptl: ' . Auth::id(), array('c' => '2'));
            $roleId = Auth::User()->lkp_role_id;
            $serviceId = Session::get('service_id');
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_INSERTED_COUNTER_OFFER", BUYER_INSERTED_COUNTER_OFFER, 0, HTTP_REFERRER, CURRENT_URL);
            }
            /*
            $ltlBuyerQuoteItemDetails = DB::table ('ptl_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('ptl_buyer_quote_items as pbqi','pbqi.id','=','pbqsqp.buyer_quote_item_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                                                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                                                'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees')
                                            ->get();
            */
            switch ($serviceId) {
                
            case ROAD_PTL:
            $ltlBuyerQuoteItemDetails = DB::table ('ptl_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('ptl_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                                            ->leftjoin('ptl_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                                                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                                                'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees', 'pbqi.lkp_ict_weight_uom_id')
                                            ->get();
            break;
            case RAIL:
            $ltlBuyerQuoteItemDetails = DB::table ('rail_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('rail_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                                            ->leftjoin('rail_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                                                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                                                'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees', 'pbqi.lkp_ict_weight_uom_id')
                                            ->get();
            break;
            case AIR_DOMESTIC:
            $ltlBuyerQuoteItemDetails = DB::table ('airdom_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('airdom_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                                            ->leftjoin('airdom_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                                                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                                                'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees', 'pbqi.lkp_ict_weight_uom_id')
                                            ->get();
            break;
            case COURIER:
            $ltlBuyerQuoteItemDetails = DB::table ('courier_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('courier_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                                            ->leftjoin('courier_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units','pbqsqp.seller_post_item_id', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_cod_rupees',
                                                'pbqsqp.final_cod_rupees', 'pbqsqp.initial_fuel_surcharge_rupees', 'pbqsqp.final_fuel_surcharge_rupees','pbqi.package_value',
                                                'pbqsqp.initial_freight_collect_rupees','pbqsqp.initial_arc_rupees','pbqsqp.final_arc_rupees', 'pbqsqp.final_freight_collect_rupees', 'pbqi.lkp_ict_weight_uom_id')
                                            ->get();
            break;
            case AIR_INTERNATIONAL:
            $ltlBuyerQuoteItemDetails = DB::table ('airint_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('airint_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                                            ->leftjoin('airint_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                                                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                                                'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees', 'pbqi.lkp_ict_weight_uom_id')
                                            ->get();
            break;
            case OCEAN:
            $ltlBuyerQuoteItemDetails = DB::table ('ocean_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('ocean_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                                            ->leftjoin('ocean_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                                                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                                                'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees', 'pbqi.lkp_ict_weight_uom_id')
                                            ->get();
            break;
            default:
            $ltlBuyerQuoteItemDetails = DB::table ('ptl_buyer_quote_sellers_quotes_prices as pbqsqp')
                                            ->leftjoin('ptl_buyer_quotes as pbq','pbq.id','=','pbqsqp.buyer_quote_id')
                                            ->leftjoin('ptl_buyer_quote_items as pbqi','pbq.id','=','pbqi.buyer_quote_id')
                                            ->where ('pbqsqp.id', $input['buyerCounterOfferId'])
                                            ->select ('pbqi.length', 'pbqi.breadth', 'pbqi.height', 'pbqi.units', 
                                                'pbqi.number_packages', 'pbqi.calculated_volume_weight', 'pbqsqp.initial_oda_rupees',
                                                'pbqsqp.final_oda_rupees', 'pbqsqp.initial_pick_up_rupees', 'pbqsqp.final_pick_up_rupees',
                                                'pbqsqp.initial_delivery_rupees', 'pbqsqp.final_delivery_rupees', 'pbqi.lkp_ict_weight_uom_id')
                                            ->get();
            break;
            }
            $freightAmountInTotal = [];
            $packagescount = 0;
            $packagesvalue=0;
            $totalkg=0;
            if(!empty($ltlBuyerQuoteItemDetails)) {
                foreach ($ltlBuyerQuoteItemDetails as $ltlBuyerQuoteItem) {
                    $units = $ltlBuyerQuoteItem->units;
                    $numberPackages = $ltlBuyerQuoteItem->number_packages;
                    
                    $calculatedVolume = $ltlBuyerQuoteItem->calculated_volume_weight;
                    if($serviceId == COURIER){
                        $initialCod = $ltlBuyerQuoteItem->initial_cod_rupees;
                        $initialFuelCharges = $ltlBuyerQuoteItem->initial_fuel_surcharge_rupees;
                        $initialFreightCharges = $ltlBuyerQuoteItem->initial_freight_collect_rupees;
                        $initialArcCharges = $ltlBuyerQuoteItem->initial_arc_rupees;
                        $numberPackagesvalue = $ltlBuyerQuoteItem->package_value;
                    }
                    else{ 
                        $initialOda = $ltlBuyerQuoteItem->initial_oda_rupees;
                        $finalOda = $ltlBuyerQuoteItem->final_oda_rupees;
                        $initialPickupCharges = $ltlBuyerQuoteItem->initial_pick_up_rupees;
                        $finalPickupCharges = $ltlBuyerQuoteItem->final_pick_up_rupees;
                        $initialDeliveryCharges = $ltlBuyerQuoteItem->initial_delivery_rupees;
                        $finalDeliveryCharges = $ltlBuyerQuoteItem->final_delivery_rupees;
                        $numberPackagesvalue=0;
                    }
                    if($serviceId != COURIER){
                    $volumetricWeight = $calculatedVolume * $input['conversionKgCftValue'];
                    }else{
                        $volumetricWeight = $calculatedVolume / $input['conversionKgCftValue'];
                    }
                    
                    if($ltlBuyerQuoteItem->lkp_ict_weight_uom_id == 2){
                        $densityWeight = CommonComponent::convertGramToKG($units);
                    } else {
                        $densityWeight = $units;
                    }
                    if($volumetricWeight > $densityWeight) {
                        $totalFreightAmount = $volumetricWeight * $input['counterRateForKgValue'] * $numberPackages;
                        $totalkg += $densityWeight*1000;
                    } else {
                        $totalFreightAmount = $densityWeight * $input['counterRateForKgValue'] * $numberPackages;
                        $totalkg += $densityWeight*1000;
                    }
                    $packagescount += $numberPackages;
                    $packagesvalue += $numberPackagesvalue;
                    array_push($freightAmountInTotal, $totalFreightAmount);
                }
            }
           
            $totalFreight = array_sum($freightAmountInTotal);
            if($serviceId != COURIER){
                $oda = BuyerComponent::getFinalDetails($initialOda, $finalOda);
                $pickUpPrice = BuyerComponent::getFinalDetails($initialPickupCharges, $finalPickupCharges);
                $deliveryPrice = BuyerComponent::getFinalDetails($initialDeliveryCharges, $finalDeliveryCharges);
                $totalAmount = $totalFreight + $oda + $pickUpPrice + $deliveryPrice;
            }else{
                $fuelpercentage = ($initialFuelCharges/100)* $totalFreight;
                $codpercentage = ($initialCod/100)*($packagescount*$packagesvalue);
                $arcpercentage = ($initialArcCharges/100)*($packagescount*$packagesvalue);
                
                if($ltlBuyerQuoteItemDetails[0]->seller_post_item_id!=''){
                    $sellerpostidslabs   = DB::table('courier_seller_post_items')
                    ->leftjoin ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
                    ->leftjoin ( 'courier_seller_post_item_slabs', 'courier_seller_post_item_slabs.seller_post_id', '=', 'courier_seller_post_items.seller_post_id' )
                    ->where('courier_seller_post_items.id','=',$ltlBuyerQuoteItemDetails[0]->seller_post_item_id)
                    ->select('courier_seller_post_item_slabs.*','courier_seller_post_items.seller_post_id','courier_seller_posts.increment_weight','courier_seller_posts.rate_per_increment','courier_seller_posts.lkp_payment_mode_id')
                    ->get();
                }
                    
                    
                $slabcount=0;
                $slabprice=0;

                for($i=0;$i<count($sellerpostidslabs);$i++){
                    $slabcount += $sellerpostidslabs[$i]->slab_max_rate-$sellerpostidslabs[$i]->slab_min_rate;
                    $slabprice += $sellerpostidslabs[$i]->price;
                
                }

                if($sellerpostidslabs[0]->lkp_payment_mode_id==2){
                    $totalAmount = $totalFreight + $fuelpercentage + $codpercentage + $arcpercentage + $initialFreightCharges;
                }else{
                    $totalAmount = $totalFreight + $fuelpercentage + $codpercentage + $arcpercentage;
                }
            }
            
           
            //Save data into txnprojectinviteerequests
            $updatedAt = date('Y-m-d H:i:s');
            $updatedIp = $_SERVER['REMOTE_ADDR'];
            $updatedBy = Auth::User()->user_id;
            switch ($serviceId) {
                
            case ROAD_PTL:
            PtlBuyerQuoteSellersQuotesPrice::where(["id" => $input['buyerCounterOfferId']])
                    ->update(
                            array(
                                'counter_freight_amount' => $totalFreight,
                                'counter_quote_price' => $totalAmount,
                                'counter_rate_per_kg' => $input['counterRateForKgValue'],
                                'counter_kg_per_cft' => $input['conversionKgCftValue'],
                                'updated_at' => $updatedAt,
                                'updated_ip' => $updatedIp,
                                'updated_by' => $updatedBy,
                                'counter_quote_created_at' => $updatedAt
                            )
            );
            CommonComponent::auditLog($input['buyerCounterOfferId'], 'ptl_buyer_quote_sellers_quotes_prices');
            $buyerDetails = DB::table('ptl_buyer_quote_sellers_quotes_prices as bqsqp')
                            ->where(['id' => $input['buyerCounterOfferId']])->select('bqsqp.seller_id')->get();
            
            //*******Send Sms to the Sellers,buyer counter offer***********************//
                
                $getBuyerpostdetails  = DB::table('ptl_buyer_quote_sellers_quotes_prices as bqsqp')
            				->leftjoin('ptl_seller_post_items as spi', 'bqsqp.seller_post_item_id', '=', 'spi.id')
            				->leftjoin('ptl_seller_posts as sp', 'spi.seller_post_id', '=', 'sp.id')
            				->where('bqsqp.id','=',$input['buyerCounterOfferId'])
            				->select('sp.transaction_id','bqsqp.seller_id')->get();
                $msg_params = array(
                		'randnumber' => $getBuyerpostdetails[0]->transaction_id,
                		'buyername' => Auth::User()->username,
                		'servicename' => 'LTL'
                );
                $getMobileNumber  =   CommonComponent::getMobleNumber($getBuyerpostdetails[0]->seller_id);
                CommonComponent::sendSMS($getMobileNumber,BUYER_COUNTER_OFFER_SMS,$msg_params);
                //*******Send Sms to the Sellers,buyer counter offer***********************//
            
            break;
            case RAIL:
            RailBuyerQuoteSellersQuotesPrice::where(["id" => $input['buyerCounterOfferId']])
                    ->update(
                            array(
                                'counter_freight_amount' => $totalFreight,
                                'counter_quote_price' => $totalAmount,
                                'counter_rate_per_kg' => $input['counterRateForKgValue'],
                                'counter_kg_per_cft' => $input['conversionKgCftValue'],
                                'updated_at' => $updatedAt,
                                'updated_ip' => $updatedIp,
                                'updated_by' => $updatedBy,
                                'counter_quote_created_at' => $updatedAt
                            )
            );
            CommonComponent::auditLog($input['buyerCounterOfferId'], 'rail_buyer_quote_sellers_quotes_prices');
            $buyerDetails = DB::table('rail_buyer_quote_sellers_quotes_prices as bqsqp')
                            ->where(['id' => $input['buyerCounterOfferId']])->select('bqsqp.seller_id')->get();
            
            //*******Send Sms to the Sellers,buyer counter offer***********************//
            
            $getBuyerpostdetails  = DB::table('rail_buyer_quote_sellers_quotes_prices as bqsqp')
            ->leftjoin('rail_seller_post_items as spi', 'bqsqp.seller_post_item_id', '=', 'spi.id')
            ->leftjoin('rail_seller_posts as sp', 'spi.seller_post_id', '=', 'sp.id')
            ->where('bqsqp.id','=',$input['buyerCounterOfferId'])
            ->select('sp.transaction_id','bqsqp.seller_id')->get();
            $msg_params = array(
            		'randnumber' => $getBuyerpostdetails[0]->transaction_id,
            		'buyername' => Auth::User()->username,
            		'servicename' => 'RAIL'
            );
            $getMobileNumber  =   CommonComponent::getMobleNumber($getBuyerpostdetails[0]->seller_id);
            CommonComponent::sendSMS($getMobileNumber,BUYER_COUNTER_OFFER_SMS,$msg_params);
            //*******Send Sms to the Sellers,buyer counter offer***********************//
            
            break;
            case AIR_DOMESTIC:
            AirdomBuyerQuoteSellersQuotesPrice::where(["id" => $input['buyerCounterOfferId']])
                    ->update(
                            array(
                                'counter_freight_amount' => $totalFreight,
                                'counter_quote_price' => $totalAmount,
                                'counter_rate_per_kg' => $input['counterRateForKgValue'],
                                'counter_kg_per_cft' => $input['conversionKgCftValue'],
                                'updated_at' => $updatedAt,
                                'updated_ip' => $updatedIp,
                                'updated_by' => $updatedBy,
                                'counter_quote_created_at' => $updatedAt
                            )
            );
            CommonComponent::auditLog($input['buyerCounterOfferId'], 'airdom_buyer_quote_sellers_quotes_prices');
            $buyerDetails = DB::table('airdom_buyer_quote_sellers_quotes_prices as bqsqp')
                            ->where(['id' => $input['buyerCounterOfferId']])->select('bqsqp.seller_id')->get();
            
            //*******Send Sms to the Sellers,buyer counter offer***********************//
            
            $getBuyerpostdetails  = DB::table('airdom_buyer_quote_sellers_quotes_prices as bqsqp')
            ->leftjoin('airdom_seller_post_items as spi', 'bqsqp.seller_post_item_id', '=', 'spi.id')
            ->leftjoin('airdom_seller_posts as sp', 'spi.seller_post_id', '=', 'sp.id')
            ->where('bqsqp.id','=',$input['buyerCounterOfferId'])
            ->select('sp.transaction_id','bqsqp.seller_id')->get();
            $msg_params = array(
            		'randnumber' => $getBuyerpostdetails[0]->transaction_id,
            		'buyername' => Auth::User()->username,
            		'servicename' => 'AIR DOMESTIC'
            );
            $getMobileNumber  =   CommonComponent::getMobleNumber($getBuyerpostdetails[0]->seller_id);
            CommonComponent::sendSMS($getMobileNumber,BUYER_COUNTER_OFFER_SMS,$msg_params);
            //*******Send Sms to the Sellers,buyer counter offer***********************//
            
            break;
            case COURIER:
                
            CourierBuyerQuoteSellersQuotesPrice::where(["id" => $input['buyerCounterOfferId']])
                    ->update(
                            array(
                                'counter_freight_amount' => $totalFreight,
                                'counter_quote_price' => $totalAmount,
                                'counter_rate_per_kg' => $input['counterRateForKgValue'],
                                'counter_conversion_factor' => $input['conversionKgCftValue'],
                                'updated_at' => $updatedAt,
                                'updated_ip' => $updatedIp,
                                'updated_by' => $updatedBy,
                                'counter_quote_created_at' => $updatedAt
                            )
            );
            CommonComponent::auditLog($input['buyerCounterOfferId'], 'courier_buyer_quote_sellers_quotes_prices');
            $buyerDetails = DB::table('courier_buyer_quote_sellers_quotes_prices as bqsqp')
                            ->where(['id' => $input['buyerCounterOfferId']])->select('bqsqp.seller_id')->get();
            
            //*******Send Sms to the Sellers,buyer counter offer***********************//
            
            $getBuyerpostdetails  = DB::table('courier_buyer_quote_sellers_quotes_prices as bqsqp')
            ->leftjoin('courier_seller_post_items as spi', 'bqsqp.seller_post_item_id', '=', 'spi.id')
            ->leftjoin('courier_seller_posts as sp', 'spi.seller_post_id', '=', 'sp.id')
            ->where('bqsqp.id','=',$input['buyerCounterOfferId'])
            ->select('sp.transaction_id','bqsqp.seller_id')->get();
            $msg_params = array(
            		'randnumber' => $getBuyerpostdetails[0]->transaction_id,
            		'buyername' => Auth::User()->username,
            		'servicename' => 'COURIER'
            );
            $getMobileNumber  =   CommonComponent::getMobleNumber($getBuyerpostdetails[0]->seller_id);
            CommonComponent::sendSMS($getMobileNumber,BUYER_COUNTER_OFFER_SMS,$msg_params);
            //*******Send Sms to the Sellers,buyer counter offer***********************//
            
            break;
            case AIR_INTERNATIONAL:
            AirintBuyerQuoteSellersQuotesPrice::where(["id" => $input['buyerCounterOfferId']])
                    ->update(
                            array(
                                'counter_freight_amount' => $totalFreight,
                                'counter_quote_price' => $totalAmount,
                                'counter_rate_per_kg' => $input['counterRateForKgValue'],
                                'counter_kg_per_cft' => $input['conversionKgCftValue'],
                                'updated_at' => $updatedAt,
                                'updated_ip' => $updatedIp,
                                'updated_by' => $updatedBy,
                                'counter_quote_created_at' => $updatedAt
                            )
            );
            CommonComponent::auditLog($input['buyerCounterOfferId'], 'airint_buyer_quote_sellers_quotes_prices');
            $buyerDetails = DB::table('airint_buyer_quote_sellers_quotes_prices as bqsqp')
                            ->where(['id' => $input['buyerCounterOfferId']])->select('bqsqp.seller_id')->get();
            
            //*******Send Sms to the Sellers,buyer counter offer***********************//
            
            $getBuyerpostdetails  = DB::table('airint_buyer_quote_sellers_quotes_prices as bqsqp')
            ->leftjoin('airint_seller_post_items as spi', 'bqsqp.seller_post_item_id', '=', 'spi.id')
            ->leftjoin('airint_seller_posts as sp', 'spi.seller_post_id', '=', 'sp.id')
            ->where('bqsqp.id','=',$input['buyerCounterOfferId'])
            ->select('sp.transaction_id','bqsqp.seller_id')->get();
            $msg_params = array(
            		'randnumber' => $getBuyerpostdetails[0]->transaction_id,
            		'buyername' => Auth::User()->username,
            		'servicename' => 'AIR INTERNATIONAL'
            );
            $getMobileNumber  =   CommonComponent::getMobleNumber($getBuyerpostdetails[0]->seller_id);
            CommonComponent::sendSMS($getMobileNumber,BUYER_COUNTER_OFFER_SMS,$msg_params);
            //*******Send Sms to the Sellers,buyer counter offer***********************//
            
            break;
            case OCEAN:
            OceanBuyerQuoteSellersQuotesPrice::where(["id" => $input['buyerCounterOfferId']])
                    ->update(
                            array(
                                'counter_freight_amount' => $totalFreight,
                                'counter_quote_price' => $totalAmount,
                                'counter_rate_per_kg' => $input['counterRateForKgValue'],
                                'counter_kg_per_cft' => $input['conversionKgCftValue'],
                                'updated_at' => $updatedAt,
                                'updated_ip' => $updatedIp,
                                'updated_by' => $updatedBy,
                                'counter_quote_created_at' => $updatedAt
                            )
            );
            CommonComponent::auditLog($input['buyerCounterOfferId'], 'ocean_buyer_quote_sellers_quotes_prices');
            $buyerDetails = DB::table('ocean_buyer_quote_sellers_quotes_prices as bqsqp')
                            ->where(['id' => $input['buyerCounterOfferId']])->select('bqsqp.seller_id')->get();
            
            //*******Send Sms to the Sellers,buyer counter offer***********************//
            
            $getBuyerpostdetails  = DB::table('ocean_buyer_quote_sellers_quotes_prices as bqsqp')
            ->leftjoin('ocean_seller_post_items as spi', 'bqsqp.seller_post_item_id', '=', 'spi.id')
            ->leftjoin('ocean_seller_posts as sp', 'spi.seller_post_id', '=', 'sp.id')
            ->where('bqsqp.id','=',$input['buyerCounterOfferId'])
            ->select('sp.transaction_id','bqsqp.seller_id')->get();
            $msg_params = array(
            		'randnumber' => $getBuyerpostdetails[0]->transaction_id,
            		'buyername' => Auth::User()->username,
            		'servicename' => 'AIR INTERNATIONAL'
            );
            $getMobileNumber  =   CommonComponent::getMobleNumber($getBuyerpostdetails[0]->seller_id);
            CommonComponent::sendSMS($getMobileNumber,BUYER_COUNTER_OFFER_SMS,$msg_params);
            //*******Send Sms to the Sellers,buyer counter offer***********************//
            
            
            break;
            default:
            PtlBuyerQuoteSellersQuotesPrice::where(["id" => $input['buyerCounterOfferId']])
                    ->update(
                            array(
                                'counter_freight_amount' => $totalFreight,
                                'counter_quote_price' => $totalAmount,
                                'counter_rate_per_kg' => $input['counterRateForKgValue'],
                                'counter_kg_per_cft' => $input['conversionKgCftValue'],
                                'updated_at' => $updatedAt,
                                'updated_ip' => $updatedIp,
                                'updated_by' => $updatedBy,
                                'counter_quote_created_at' => $updatedAt
                            )
            );
            CommonComponent::auditLog($input['buyerCounterOfferId'], 'ptl_buyer_quote_sellers_quotes_prices');
            $buyerDetails = DB::table('ptl_buyer_quote_sellers_quotes_prices as bqsqp')
                            ->where(['id' => $input['buyerCounterOfferId']])->select('bqsqp.seller_id')->get();
            break;
        
            }
            if (!empty($buyerDetails)) {
                //CommonComponent::sendEmail(COUNTER_OFFER_BY_BUYER,$buyerDetails[0]->seller_id);
                $sellerCounterOfferEmail = DB::table('users')->where('id', $buyerDetails[0]->seller_id)->get();
                $sellerCounterOfferEmail[0]->buyername = Auth::User()->username;
                CommonComponent::send_email(COUNTER_OFFER_BY_BUYER,$sellerCounterOfferEmail);
            }
            return;
        } catch (Exception $e) {
            
        }
    }

    /**
     * get buyer counter offer page
     * Insert values for booknow
     * @param Request $request
     * @return type
     */
    public static function setBuyerBooknow($input) {       
        Log::info('Insert the buyer booknow data for ptl: ' . Auth::id(), array('c' => '2'));
        try {
            $roleId = Auth::User()->lkp_role_id;
            $serviceId = Session::get('service_id');
            
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_INSERTED_ADDTOCART", BUYER_INSERTED_ADDTOCART, 0, HTTP_REFERRER, CURRENT_URL);
            }

            $cartPaymentMethods = DB::table('cart_items')
                    ->where('cart_items.buyer_id', $input['buyerId'])
                    ->select('cart_items.lkp_payment_mode_id')
                    ->get();
            if (!empty($cartPaymentMethods)) {
                $existingCartPaymentMethod = $cartPaymentMethods[0]->lkp_payment_mode_id;
            }
            switch ($serviceId) {
            case ROAD_PTL:
            $postPaymentMethods = DB::table('ptl_seller_posts')
                    ->leftjoin('ptl_seller_post_items', 'ptl_seller_post_items.seller_post_id', '=', 'ptl_seller_posts.id')
                    ->leftjoin ( 'ptl_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_item_id', '=', 'ptl_seller_post_items.id' )
                    ->where('ptl_seller_post_items.id', $input['postItemId'])
                    ->select('ptl_seller_post_items.id','ptl_seller_post_items.transitdays','ptl_seller_post_items.units', 'ptl_seller_posts.lkp_payment_mode_id',
                          DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then ptl_seller_post_items.transitdays end) as transitdays")  )
                    ->get();
            break;
            case RAIL:
            $postPaymentMethods = DB::table('rail_seller_posts as sp')
                    ->leftjoin('rail_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                    ->leftjoin ( 'rail_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_item_id', '=', 'spi.id' )
                    ->where('spi.id', $input['postItemId'])
                    ->select('spi.id','spi.transitdays','spi.units', 'sp.lkp_payment_mode_id',
                          DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then spi.transitdays end) as transitdays")  )
                    ->get();
            break;
            case AIR_DOMESTIC:
            $postPaymentMethods = DB::table('airdom_seller_posts as sp')
                    ->leftjoin('airdom_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                    ->leftjoin ( 'airdom_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_item_id', '=', 'spi.id' )
                    ->where('spi.id', $input['postItemId'])
                    ->select('spi.id','spi.transitdays','spi.units', 'sp.lkp_payment_mode_id',
                          DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then spi.transitdays end) as transitdays")  )
                    ->get();
            break;
            case AIR_INTERNATIONAL:
            $postPaymentMethods = DB::table('airint_seller_posts as sp')
                    ->leftjoin('airint_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                    ->leftjoin ( 'airint_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_item_id', '=', 'spi.id' )
                    ->where('spi.id', $input['postItemId'])
                    ->select('spi.id','spi.transitdays','spi.units', 'sp.lkp_payment_mode_id',
                         DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then spi.transitdays end) as transitdays")   )
                    ->get();
            break;
            case OCEAN:
            $postPaymentMethods = DB::table('ocean_seller_posts as sp')
                    ->leftjoin('ocean_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                    ->leftjoin ( 'ocean_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_item_id', '=', 'spi.id' )
                    ->where('spi.id', $input['postItemId'])
                    ->select('spi.id','spi.transitdays','spi.units', 'sp.lkp_payment_mode_id',
                        DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then spi.transitdays end) as transitdays")    )
                    ->get();
            break;
            case COURIER:
            $postPaymentMethods = DB::table('courier_seller_posts as sp')
                    ->leftjoin('courier_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                    ->leftjoin ( 'courier_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_item_id', '=', 'spi.id' )
                    ->where('spi.id', $input['postItemId'])
                    ->select('spi.id','spi.transitdays','spi.units', 'sp.lkp_payment_mode_id',
                         DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then spi.transitdays end) as transitdays")   )
                    ->get();
            break;
            default:
            $postPaymentMethods = DB::table('ptl_seller_posts')
                    ->leftjoin('ptl_seller_post_items', 'ptl_seller_post_items.seller_post_id', '=', 'ptl_seller_posts.id')
                    ->leftjoin ( 'ptl_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_item_id', '=', 'ptl_seller_post_items.id' )
                    ->where('ptl_seller_post_items.id', $input['postItemId'])
                    ->select('ptl_seller_post_items.id','ptl_seller_post_items.transitdays','ptl_seller_post_items.units', 'ptl_seller_posts.lkp_payment_mode_id',
                            DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then ptl_seller_post_items.transitdays end) as transitdays") )
                    ->get();
            break;
            }
            if(empty($input['sellerPostedToDate']) || $input['sellerPostedToDate'] == '0000-00-00') {
                $transitTime = $postPaymentMethods[0]->transitdays;
                $transitTimeUnit = $postPaymentMethods[0]->units;
                if($transitTimeUnit == 'Weeks') {
                    $transitDays = $transitTimeUnit * 7;
                } else {
                    $transitDays = $transitTime;
                }
                $deliveryDate = date("Y-m-d", strtotime("+".$transitDays." days", strtotime(CommonComponent::convertDateForDatabase($input['consignmentPickupDate']))));
            } else {
                $deliveryDate = $input['sellerPostedToDate'];
            }
            $postPaymentMethod = $postPaymentMethods[0]->lkp_payment_mode_id;

            if ((isset($existingCartPaymentMethod) && $existingCartPaymentMethod != $postPaymentMethod) && count($cartPaymentMethods) > 0) {
                //return redirect('/buyerposts')->with('succmsg', 'Buyer Quote Successfully Submitted');
                return array('success' => 0,
                    'message' => "You can't proceed with book now,because the payment mode of all the items in the cart should be similar!");
            } else {
                $booknowAddToCart = new CartItem();

                $booknowAddToCart->seller_id = $input['sellerId'];
                $booknowAddToCart->buyer_id = $input['buyerId'];
                $booknowAddToCart->lkp_service_id = Session::get('service_id');
                $booknowAddToCart->buyer_quote_item_id = $input['quoteItemId'];
                $booknowAddToCart->buyer_quote_id = $input['quoteId'];
                $booknowAddToCart->lkp_payment_mode_id = $postPaymentMethod;
                $booknowAddToCart->seller_post_item_id = $input['postItemId'];
                $booknowAddToCart->lkp_src_location_type_id = $input['sourceLocationType'];
                $booknowAddToCart->lkp_dest_location_type_id = $input['destinationLocationType'];
                $booknowAddToCart->lkp_packaging_type_id = $input['packagingType'];
                
                if($input['sourceLocationType']=='11')
                $booknowAddToCart->other_src_location_type = $input['sourceLocationTypeOther'];
                if($input['destinationLocationType']=='11')
                $booknowAddToCart->other_dest_location_type = $input['destinationLocationTypeOther'];
                if($input['packagingType']=='13')
                $booknowAddToCart->other_packaging_type = $input['packagingTypeOther'];
                
                $booknowAddToCart->price = $input['price'];
                $booknowAddToCart->buyer_consignment_pick_up_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
                $booknowAddToCart->buyer_consignment_value = $input['consignmentValue'];
                $booknowAddToCart->buyer_consignment_needs_insurance = $input['consignmentNeedInsurance'];
                $booknowAddToCart->buyer_consignment_needs_fragile = $input['consignmentNeedFragile'];
                $booknowAddToCart->buyer_consignor_name = $input['consignorName'];
                $booknowAddToCart->buyer_consignor_mobile = $input['consignorNumber'];
                $booknowAddToCart->buyer_consignor_email = $input['consignorEmail'];
                $booknowAddToCart->buyer_consignor_address = $input['consignorAddress'];
                $booknowAddToCart->buyer_consignor_pincode = $input['consignorPin'];
                $booknowAddToCart->buyer_consignee_name = $input['consigneeName'];
                $booknowAddToCart->buyer_consignee_mobile = $input['consigneeNumber'];
                $booknowAddToCart->buyer_consignee_email = $input['consigneeEmail'];
                $booknowAddToCart->buyer_consignee_pincode = $input['consigneePin'];
                $booknowAddToCart->buyer_consignee_address = $input['consigneeAddress'];
                $booknowAddToCart->buyer_additional_details = $input['additionalDetails'];
//                $booknowAddToCart->buyer_consignment_value = $input['buyerCounterOfferId'];
                $booknowAddToCart->dispatch_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
                $booknowAddToCart->delivery_date = $deliveryDate;

                $created_at = date('Y-m-d H:i:s');
                $createdIp = $_SERVER['REMOTE_ADDR'];
                $booknowAddToCart->created_by = Auth::id();
                $booknowAddToCart->created_at = $created_at;
                $booknowAddToCart->created_ip = $createdIp;

                if ($booknowAddToCart->save()) {
                    CommonComponent::auditLog($booknowAddToCart->id, 'cart_items');
                    $cartInsertId = $booknowAddToCart->id;
                    switch ($serviceId) {
                        case ROAD_PTL:
                            $cartData =  DB::select( DB::raw("SELECT
                            q.*,
                            u.username,
                            q.price,
                            concat(pp.pincode,'-',pp.postoffice_name) as from_location,
                            concat(ppt.pincode,'-',ppt.postoffice_name) as to_location,
                            service.service_name,
                            q.dispatch_date,
                            bq.lkp_post_status_id as post_status
                            FROM
                            cart_items q
                            LEFT JOIN users u on u.id = q.seller_id
                            LEFT JOIN lkp_services service on service.id = q.lkp_service_id                    
                            LEFT JOIN ptl_buyer_quotes bq on bq.id = q.buyer_quote_id                   
                            LEFT JOIN lkp_ptl_pincodes pp
                                  ON pp.id = bq.from_location_id
                            LEFT JOIN lkp_ptl_pincodes ppt
                                  ON ppt.id = bq.to_location_id     
                            where q.id ='".$cartInsertId."'"));
                            $sellerPostTableName = 'ptl_seller_post_items';
                            break;
                        case RAIL:
                            $cartData =  DB::select( DB::raw("SELECT
                            q.*,
                            u.username,
                            q.price,
                            concat(pp.pincode,'-',pp.postoffice_name) as from_location,
                            concat(ppt.pincode,'-',ppt.postoffice_name) as to_location,
                            service.service_name,
                            q.dispatch_date,
                            bq.lkp_post_status_id as post_status
                            FROM
                            cart_items q
                            LEFT JOIN users u on u.id = q.seller_id
                            LEFT JOIN lkp_services service on service.id = q.lkp_service_id                    
                            LEFT JOIN rail_buyer_quotes bq on bq.id = q.buyer_quote_id                   
                            LEFT JOIN lkp_ptl_pincodes pp
                                  ON pp.id = bq.from_location_id
                            LEFT JOIN lkp_ptl_pincodes ppt
                                  ON ppt.id = bq.to_location_id     
                            where q.id ='".$cartInsertId."'"));
                            $sellerPostTableName = 'rail_seller_post_items';
                            break;
                        case AIR_DOMESTIC:
                            $cartData =  DB::select( DB::raw("SELECT
                            q.*,
                            u.username,
                            q.price,
                            concat(pp.pincode,'-',pp.postoffice_name) as from_location,
                            concat(ppt.pincode,'-',ppt.postoffice_name) as to_location,
                            service.service_name,
                            q.dispatch_date,
                            bq.lkp_post_status_id as post_status
                            FROM
                            cart_items q
                            LEFT JOIN users u on u.id = q.seller_id
                            LEFT JOIN lkp_services service on service.id = q.lkp_service_id                    
                            LEFT JOIN airdom_buyer_quotes bq on bq.id = q.buyer_quote_id                   
                            LEFT JOIN lkp_ptl_pincodes pp
                                  ON pp.id = bq.from_location_id
                            LEFT JOIN lkp_ptl_pincodes ppt
                                  ON ppt.id = bq.to_location_id     
                            where q.id ='".$cartInsertId."'"));
                            $sellerPostTableName = 'airdom_seller_post_items';
                            break;
                        case AIR_INTERNATIONAL:
                            $cartData =  DB::select( DB::raw("SELECT
                            q.*,
                            u.username,
                            q.price,
                            concat(pp.location,'-',pp.airport_name) as from_location,
                            concat(ppt.location,'-',ppt.airport_name) as to_location,
                            service.service_name,
                            q.dispatch_date,
                            bq.lkp_post_status_id as post_status
                            FROM
                            cart_items q
                            LEFT JOIN users u on u.id = q.seller_id
                            LEFT JOIN lkp_services service on service.id = q.lkp_service_id                    
                            LEFT JOIN airint_buyer_quotes bq on bq.id = q.buyer_quote_id                   
                            LEFT JOIN lkp_airports pp
                                  ON pp.id = bq.from_location_id
                            LEFT JOIN lkp_airports ppt
                                  ON ppt.id = bq.to_location_id     
                            where q.id ='".$cartInsertId."'"));
                            $sellerPostTableName = 'airint_seller_post_items';
                            break;
                        case OCEAN:
                            $cartData =  DB::select( DB::raw("SELECT
                            q.*,
                            u.username,
                            q.price,
                            concat(pp.country_name,'-',pp.seaport_name) as from_location,
                            concat(ppt.country_name,'-',ppt.seaport_name) as to_location,
                            service.service_name,
                            q.dispatch_date,
                            bq.lkp_post_status_id as post_status
                            FROM
                            cart_items q
                            LEFT JOIN users u on u.id = q.seller_id
                            LEFT JOIN lkp_services service on service.id = q.lkp_service_id                    
                            LEFT JOIN ocean_buyer_quotes bq on bq.id = q.buyer_quote_id                   
                            LEFT JOIN lkp_seaports pp
                                  ON pp.id = bq.from_location_id
                            LEFT JOIN lkp_seaports ppt
                                  ON ppt.id = bq.to_location_id     
                            where q.id ='".$cartInsertId."'"));
                            $sellerPostTableName = 'ocean_seller_post_items';
                            break;
                        case COURIER:
                            $cartData =  DB::select( DB::raw("SELECT
                            q.*,
                            u.username,
                            q.price,
                            concat(pp.pincode,'-',pp.postoffice_name) as from_location,
                            (CASE
                                WHEN bqi.lkp_courier_delivery_type_id = 1 THEN concat(ppt.pincode,'-',ppt.postoffice_name)
                                WHEN bqi.lkp_courier_delivery_type_id = 2 THEN concat(ct.country_name)
                            END ) as to_location,
                            service.service_name,
                            q.dispatch_date,
                            bq.lkp_post_status_id as post_status
                            FROM
                            cart_items q
                            LEFT JOIN users u on u.id = q.seller_id
                            LEFT JOIN lkp_services service on service.id = q.lkp_service_id                    
                            LEFT JOIN courier_buyer_quotes bq on bq.id = q.buyer_quote_id 
                            LEFT JOIN courier_buyer_quote_items bqi on bqi.buyer_quote_id = bq.id
                            LEFT JOIN lkp_ptl_pincodes pp
                                    ON (pp.id = bq.from_location_id)        
                            
                            LEFT JOIN lkp_ptl_pincodes ppt
                                  ON (ppt.id = bq.to_location_id   AND bqi.lkp_courier_delivery_type_id = 1)  
                            LEFT JOIN lkp_countries ct
                                  ON (ct.id = bq.to_location_id   AND bqi.lkp_courier_delivery_type_id = 2)    
                            where q.id ='".$cartInsertId."'"));
                            $sellerPostTableName = 'ptl_seller_post_items';
                            break;
                    }
                    if(!empty($input['postItemId'])) {
                        //PtlBuyerComponent::changeStatusForSellerPostItem($sellerPostTableName, $input['postItemId'], INCART);
                    }

                    $booknowAddToCart  =  new ViewCartItem();
                    $booknowAddToCart->id = $cartInsertId;
                    $booknowAddToCart->seller_id = $input['sellerId'];
                    $booknowAddToCart->buyer_id = $input['buyerId'];
                    $booknowAddToCart->lkp_service_id = Session::get('service_id');
                    $booknowAddToCart->buyer_quote_item_id = $input['quoteItemId'];
                    $booknowAddToCart->buyer_quote_id = $input['quoteId'];
                    $booknowAddToCart->lkp_payment_mode_id = $postPaymentMethod;
                    $booknowAddToCart->seller_post_item_id = $input['postItemId'];
                    $booknowAddToCart->lkp_src_location_type_id = $input['sourceLocationType'];
                    $booknowAddToCart->lkp_dest_location_type_id = $input['destinationLocationType'];
                    $booknowAddToCart->lkp_packaging_type_id = $input['packagingType'];
                    
                    if($input['sourceLocationType']=='11')
                    $booknowAddToCart->other_src_location_type = $input['sourceLocationTypeOther'];
                    if($input['destinationLocationType']=='11')
                    $booknowAddToCart->other_dest_location_type = $input['destinationLocationTypeOther'];
                    if($input['packagingType']=='13')
                    $booknowAddToCart->other_packaging_type = $input['packagingTypeOther'];
                        
                    $booknowAddToCart->price = $input['price'];
                    $booknowAddToCart->buyer_consignment_pick_up_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
                    $booknowAddToCart->buyer_consignment_value = $input['consignmentValue'];
                    $booknowAddToCart->buyer_consignment_needs_insurance = $input['consignmentNeedInsurance'];
                    $booknowAddToCart->buyer_consignment_needs_fragile = $input['consignmentNeedFragile'];
                    $booknowAddToCart->buyer_consignor_name = $input['consignorName'];
                    $booknowAddToCart->buyer_consignor_mobile = $input['consignorNumber'];
                    $booknowAddToCart->buyer_consignor_email = $input['consignorEmail'];
                    $booknowAddToCart->buyer_consignor_address = $input['consignorAddress'];
                    $booknowAddToCart->buyer_consignor_pincode = $input['consignorPin'];
                    $booknowAddToCart->buyer_consignee_name = $input['consigneeName'];
                    $booknowAddToCart->buyer_consignee_mobile = $input['consigneeNumber'];
                    $booknowAddToCart->buyer_consignee_email = $input['consigneeEmail'];
                    $booknowAddToCart->buyer_consignee_pincode = $input['consigneePin'];
                    $booknowAddToCart->buyer_consignee_address = $input['consigneeAddress'];
                    $booknowAddToCart->buyer_additional_details = $input['additionalDetails'];
//                    $booknowAddToCart->buyer_consignment_value = $input['buyerCounterOfferId'];
                    $booknowAddToCart->dispatch_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
                    $booknowAddToCart->delivery_date = $deliveryDate;
                    $booknowAddToCart->username = $cartData[0]->username;
                    $booknowAddToCart->from_location = $cartData[0]->from_location;
                    $booknowAddToCart->to_location = $cartData[0]->to_location;
                    $booknowAddToCart->order_dispatch_date = $cartData[0]->dispatch_date;
                    $booknowAddToCart->post_status = $cartData[0]->post_status;
                    if(commonComponent::getGroupName($cartData[0]->lkp_service_id) != $cartData[0]->service_name){
                       $booknowAddToCart->service_name = commonComponent::getGroupName($cartData[0]->lkp_service_id)." ".$cartData[0]->service_name;
                    }else{
                       $booknowAddToCart->service_name = $cartData[0]->service_name;

                    }

                    $created_at = date ( 'Y-m-d H:i:s' );
                    $createdIp = $_SERVER['REMOTE_ADDR'];
                    $booknowAddToCart->created_by = Auth::id();
                    $booknowAddToCart->created_at = $created_at;
                    $booknowAddToCart->created_ip = $createdIp;
                    $booknowAddToCart->save();
                }
                return array('success' => 1, 'message' => "Item is added to cart successfully.");
            }
            //Save data into txnprojectinviteerequests
        } catch (Exception $e) {
            
        }
    }

    /**
     * Buyer Quote Creation page For PTL
     * insert data into buyer quote ptl table
     */
    public static function ptlBuyerQuoteMainData($allRequestdata,$commercial=0) {
        try {
        	//echo '<pre>';print_r($allRequestdata);exit;
            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER ['REMOTE_ADDR'];
            //$rand_id = rand(100000, 999999);
            $created_year = date('Y');
            $serviceId = Session::get('service_id');
            $ordid  =   CommonComponent::getPostID($serviceId);
            $trans_randid = 'LTL/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
            
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

            /******Single insert in PTL buer quote table******** */
            $ptlBuyerQuote = new PtlBuyerQuote();
            $ptlBuyerQuote->lkp_service_id = ROAD_PTL;
            $ptlBuyerQuote->lkp_lead_type_id = FTL_SPOT;
            if (isset($allRequestdata['ptlQuoteaccessId'])) {
                $quoteAccessId = $allRequestdata['ptlQuoteaccessId'];
                $ptlBuyerQuote->lkp_quote_access_id = $quoteAccessId;
            }
            $ptlBuyerQuote->transaction_id = $trans_randid;
            $ptlBuyerQuote->dispatch_date = CommonComponent::convertDateForDatabase($allRequestdata['ptlDispatchDate']);
            $ptlBuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($allRequestdata['ptlDeliveryhDate']);
            
            $ptlBuyerQuote->from_location_id = $allRequestdata['ptlFromLocation'];
            $ptlBuyerQuote->to_location_id = $allRequestdata['ptlToLocation'];

            $ptlBuyerQuote->is_dispatch_flexible = $allRequestdata['ptlFlexiableDispatch'];
            $ptlBuyerQuote->is_delivery_flexible = $allRequestdata['ptlFlexiableDelivery'];
            $ptlBuyerQuote->is_door_pickup = $allRequestdata['ptlDoorpickup'];
            $ptlBuyerQuote->is_door_delivery = $allRequestdata['ptlDoorDelivery'];
            $ptlBuyerQuote->lkp_post_status_id = OPEN;
            $ptlBuyerQuote->is_commercial = $commercial;

            // Added for GSA docs
            $ptlBuyerQuote->incoming_docs = $incoming_docs;
            $ptlBuyerQuote->outgoing_docs = $outgoing_docs;

            $ptlBuyerQuote->buyer_id = Auth::id();
            $ptlBuyerQuote->created_by = Auth::id();
            $ptlBuyerQuote->created_at = $created_at;
            $ptlBuyerQuote->created_ip = $createdIp;
            if ($ptlBuyerQuote->save()) {
                return ['buyerQuoteId'=>$ptlBuyerQuote->id,'transactionId'=>$ptlBuyerQuote->transaction_id];
            }
            return 0;
        } catch (Exception $e) {
            
        }
    }

    public static function ptlBuyerQuoteItems($allRequestdata, $ptlBuyerMaindataId,$fromcities) {
        try {
            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER ['REMOTE_ADDR'];
            $serviceId = Session::get('service_id');
            $transid  =   CommonComponent::getBuyerPostDetails($ptlBuyerMaindataId,$serviceId);
            
            //$trans_randid = $str1 . $rand_id;
            //echo "<pre>"; print_r($_REQUEST); echo "</pre>"; exit; 
            if (isset($allRequestdata['ptlLoadType'])) {

                /******Multiple insert in PTL quote items******** */
                $ptlQuote_Lineitems = new PtlBuyerQuoteItem();
                $ptlQuote_Lineitems->buyer_quote_id = $ptlBuyerMaindataId;
                $ptlQuote_Lineitems->lkp_quote_price_type_id = 1;
                $ptlQuote_Lineitems->lkp_load_type_id = $allRequestdata['ptlLoadType'];
                $ptlQuote_Lineitems->lkp_packaging_type_id = $allRequestdata['ptlPackageType'];
                $ptlQuote_Lineitems->length = $allRequestdata['ptlLength'];
                $ptlQuote_Lineitems->breadth = $allRequestdata['ptlWidth'];
                $ptlQuote_Lineitems->height = $allRequestdata['ptlHeight'];
                $ptlQuote_Lineitems->lkp_ptl_length_uom_id = $allRequestdata['ptlCheckVolWeight'];
                $ptlQuote_Lineitems->calculated_volume_weight = $allRequestdata['ptlDisplayVolumeWeight'];
                $ptlQuote_Lineitems->units = $allRequestdata['ptlUnitsWeight'];
                $ptlQuote_Lineitems->number_packages = $allRequestdata['ptlNopackages'];
                $ptlQuote_Lineitems->lkp_ict_weight_uom_id = $allRequestdata['ptlCheckUnitWeight'];
                $ptlQuote_Lineitems->lkp_post_status_id = OPEN;
                $ptlQuote_Lineitems->created_by = Auth::id();
                $ptlQuote_Lineitems->created_at = $created_at;
                $ptlQuote_Lineitems->created_ip = $createdIp;
                $ptlQuote_Lineitems->save();

                //Buyer Seller Price list code new table storing data in PTL --//print_r($_POST['seller_list']); exit;
                if (isset($allRequestdata['ptlQuoteaccessId']) && $allRequestdata['ptlQuoteaccessId']== '2') {
                    if ($allRequestdata['seller_list'] != "") {
                        $sellerList = explode(",", $allRequestdata['seller_list']);
                        $sellerListCount = count($sellerList);
                        if ($sellerListCount != 0) {
                            for ($j = 0; $j < $sellerListCount; $j ++) {
                                $checkBuyerQuoteExists =  DB::table('ptl_buyer_quote_sellers_quotes_prices')
                                ->where('buyer_quote_id', $ptlBuyerMaindataId)
                                ->where('seller_id', $sellerList[$j])
                                ->get();
                                if(count($checkBuyerQuoteExists) == 0){
                                $ptlQuotePriceList = new PtlBuyerQuoteSellersQuotesPrice();
                                $ptlQuotePriceList->buyer_id = Auth::id();
                                //$ptlQuotePriceList->buyer_quote_item_id = $ptlQuote_Lineitems->id;
                                $ptlQuotePriceList->buyer_quote_id = $ptlBuyerMaindataId;
                                $ptlQuotePriceList->seller_id = $sellerList[$j];
                                $ptlQuotePriceList->created_by = Auth::id();
                                $ptlQuotePriceList->created_at = $created_at;
                                $ptlQuotePriceList->created_ip = $createdIp;
                                $ptlQuotePriceList->save();
                                }
                            }
                        }
                    }
                }

                //End Buyer Seller price insert in PTL
                //} //This braces end for above main data inser for loop
                //Buyer selected Seller Ids list code new table storing data in PTL 
                //-this loop should be started after main data insert
                if (isset($allRequestdata['ptlQuoteaccessId']) && $allRequestdata['ptlQuoteaccessId']== '2') {
                    if ($allRequestdata['seller_list'] != "") {
                        $sellerList = explode(",", $allRequestdata['seller_list']);
                        $sellerListCount = count($sellerList);
                        if ($sellerListCount != 0) {
                            for ($k = 0; $k < $sellerListCount; $k ++) {
                                $ptlQuoteSellerList = new PtlBuyerQuoteSelectedSeller();
                                $ptlQuoteSellerList->buyer_quote_id = $ptlBuyerMaindataId;
                                $ptlQuoteSellerList->seller_id = $sellerList[$k];
                                $ptlQuoteSellerList->created_by = Auth::id();
                                $ptlQuoteSellerList->created_at = $created_at;
                                $ptlQuoteSellerList->created_ip = $createdIp;
                                $ptlQuoteSellerList->save();

                                //below code  for sent mails to selelcted sellers in private post
                                $buyers_selected_sellers_email = DB::table('users')->where('id', $sellerList[$k])->get();
                                $buyers_selected_sellers_email[0]->randnumber = $transid;
                                $buyers_selected_sellers_email[0]->buyername = Auth::User()->username;
                                CommonComponent::send_email(BUYER_CREATED_POST_FOR_SELLERS, $buyers_selected_sellers_email);
                                
                                //*******Send Sms to the private Sellers***********************//
                                $msg_params = array(
                                		'randnumber' => $transid,
                                		'buyername' => Auth::User()->username,
                                		'servicename' => 'LTL'
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
                		case ROAD_PTL :
                			$servicename = 'LTL';
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

   /**
    * Buyer Posts List Page in PTL (srinu did that page on 20-11-2015)
    * Retrieval of data related to buyer posts list items to populate in the buyer list widget    *
    */
   public static function getPtlBuyerPostsList($service_id, $post_status, $enquiry_type) 
   {
        try {
        // echo $post_status;exit;
        // Filters values to populate in the page
        $ptlFromLocationPincode = array ("" => "From Location-Pincode");
        $ptlToLocationPincode = array ("" => "To Location-Pincode");
        $ptlLoadTypes = array ("" => "Load Type");
        $ptlPackageTypes = array ("" => "Package Type");
        $dispatchDate = '';
        $deliveryDate = '';
        
        // query to retrieve buyer posts list and bind it to the grid
        switch($service_id){
            case ROAD_PTL       :
                $Query = DB::table ( 'ptl_buyer_quotes as ptlbq' );
                $Query->leftjoin ( 'ptl_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
                break;
            case RAIL       :
                $Query = DB::table ( 'rail_buyer_quotes as ptlbq' );
                $Query->leftjoin ( 'rail_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
                break;
            case AIR_DOMESTIC       :
                $Query = DB::table ( 'airdom_buyer_quotes as ptlbq' );
                $Query->leftjoin ( 'airdom_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
                break;
            case AIR_INTERNATIONAL       :
                $ptlFromLocationPincode = array ("" => "From Airport");
                $ptlToLocationPincode = array ("" => "To Airport");
                $Query = DB::table ( 'airint_buyer_quotes as ptlbq' );
                $Query->leftjoin ( 'airint_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
                break;
            case OCEAN       :
                $ptlFromLocationPincode = array ("" => "From Seaport");
                $ptlToLocationPincode = array ("" => "To Seaport");
                $Query = DB::table ( 'ocean_buyer_quotes as ptlbq' );
                $Query->leftjoin ( 'ocean_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
                break;
        }
                
        switch($service_id){
            case ROAD_PTL           :
            case RAIL               :
            case AIR_DOMESTIC       :
            case AIR_INTERNATIONAL  :
            case OCEAN              :                   
                $Query->leftjoin ( 'lkp_load_types as lt', 'lt.id', '=', 'ptlbqi.lkp_load_type_id' );
                $Query->leftjoin ( 'lkp_packaging_types as pt', 'pt.id', '=', 'ptlbqi.lkp_packaging_type_id' );
                $Query->join ( 'lkp_quote_accesses as lqa', 'lqa.id', '=', 'ptlbq.lkp_quote_access_id' );
                
                
                break;
                }       
                
        switch($service_id){
            case ROAD_PTL       :
            case RAIL       :
            case AIR_DOMESTIC       :
                $Query->leftjoin ( 'lkp_ptl_pincodes as ptlPins', 'ptlPins.id', '=', 'ptlbq.from_location_id' );
                $Query->leftjoin ( 'lkp_ptl_pincodes as ptlPinsTo', 'ptlPinsTo.id', '=', 'ptlbq.to_location_id' );
                break;
            case AIR_INTERNATIONAL       :
                $Query->leftjoin ( 'lkp_airports as ptlPins', 'ptlPins.id', '=', 'ptlbq.from_location_id' );
                $Query->leftjoin ( 'lkp_airports as ptlPinsTo', 'ptlPinsTo.id', '=', 'ptlbq.to_location_id' );
                break;
            case OCEAN       :
                $Query->leftjoin ( 'lkp_seaports as ptlPins', 'ptlPins.id', '=', 'ptlbq.from_location_id' );
                $Query->leftjoin ( 'lkp_seaports as ptlPinsTo', 'ptlPinsTo.id', '=', 'ptlbq.to_location_id' );
                break;
        }
        $Query->where ( 'ptlbq.created_by', Auth::User ()->id );
        $Query->groupBy('ptlbqi.buyer_quote_id');
        $Query->orderBy('ptlbqi.buyer_quote_id', 'DESC');
        $Query->where('ptlbq.lkp_post_status_id','!=',8);
        $Query->where('ptlbq.lkp_post_status_id','!=',7);
        $Query->where('ptlbq.lkp_post_status_id','!=',6);
        //$Query->sum('ptlbqi.number_packages as totalcnt');
        
        
        // conditions to make search
        if (isset ( $service_id ) && !empty($service_id)) {         
            $Query->where ( 'ptlbq.lkp_service_id', '=', $service_id );
        }
        if (isset ( $post_status ) && !empty($post_status)) {
        	if($post_status == 0)
        		$Query->whereIn ( 'ptlbq.lkp_post_status_id', array(1,2,3,4,5) );
        	else
	            $Query->where ( 'ptlbq.lkp_post_status_id', '=', $post_status );
        }
        if (isset ( $_GET ['dispatch_date'] ) && $_GET ['dispatch_date'] != '') {
            $commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['dispatch_date']);
            $Query->where ( 'ptlbq.dispatch_date', '>=', $commonDispatchDate );
            $dispatchDate = $commonDispatchDate;
        }
        if (isset ( $_GET ['delivery_date'] ) && $_GET ['delivery_date'] != '') {
            $commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['delivery_date']);
            $Query->where ( 'ptlbq.dispatch_date', '<=', $commonDeliveryhDate );
            $deliveryDate = $commonDeliveryhDate;
        }   
        
        //$postResults = $Query->selectRaw('sum(ptlbqi.number_packages) as totalnoofpackes');
        switch($service_id){                   
            case ROAD_PTL       :
            case RAIL           :
            case AIR_DOMESTIC   :
                $postResults = $Query->select ('ptlbq.id','ptlbq.dispatch_date','ptlbq.delivery_date','ptlbq.is_dispatch_flexible','ptlbq.from_location_id','ptlbq.to_location_id','ptlbq.lkp_post_status_id', 'lt.load_type', 'ptlbq.is_commercial','pt.packaging_type_name','ptlbq.lkp_quote_access_id','ptlPins.postoffice_name as fromLocation','ptlPinsTo.postoffice_name as toLocation','lqa.quote_access','ptlbq.is_cancelled',DB::raw('sum(ptlbqi.number_packages) AS totalpackges') )->get ();
                break;
            case AIR_INTERNATIONAL       :
                $postResults = $Query->select ('ptlbq.id','ptlbq.dispatch_date','ptlbq.delivery_date','ptlbq.is_dispatch_flexible','ptlbq.from_location_id','ptlbq.to_location_id','ptlbq.lkp_post_status_id', 'lt.load_type', 'ptlbq.is_commercial','pt.packaging_type_name','ptlbq.lkp_quote_access_id','ptlPins.airport_name as fromLocation','ptlPinsTo.airport_name as toLocation','lqa.quote_access','ptlbq.is_cancelled',DB::raw('sum(ptlbqi.number_packages) AS totalpackges') )->get ();
                break;
            case OCEAN       :
                $postResults = $Query->select ('ptlbq.id','ptlbq.dispatch_date','ptlbq.delivery_date','ptlbq.is_dispatch_flexible','ptlbq.from_location_id','ptlbq.to_location_id','ptlbq.lkp_post_status_id', 'lt.load_type', 'ptlbq.is_commercial','pt.packaging_type_name','ptlbq.lkp_quote_access_id','ptlPins.seaport_name as fromLocation','ptlPinsTo.seaport_name as toLocation','lqa.quote_access','ptlbq.is_cancelled',DB::raw('sum(ptlbqi.number_packages) AS totalpackges') )->get ();
                break;
                }
                
        // Functionality to handle filters based on the selection starts
    foreach ( $postResults as $post ) {
        switch($service_id){                    
            case ROAD_PTL       :
                $ptlBuyerQuotes = DB::table ( 'ptl_buyer_quote_items' )->where ( 'buyer_quote_id', $post->id )->select ( 'ptl_buyer_quote_items.*' )->get ();
                break;
            case RAIL       :
                $ptlBuyerQuotes = DB::table ( 'rail_buyer_quote_items' )->where ( 'buyer_quote_id', $post->id )->select ( 'rail_buyer_quote_items.*' )->get ();
                break;
            case AIR_DOMESTIC       :
                $ptlBuyerQuotes = DB::table ( 'airdom_buyer_quote_items' )->where ( 'buyer_quote_id', $post->id )->select ( 'airdom_buyer_quote_items.*' )->get ();
                break;
            case AIR_INTERNATIONAL       :
                $ptlBuyerQuotes = DB::table ( 'airint_buyer_quote_items' )->where ( 'buyer_quote_id', $post->id )->select ( 'airint_buyer_quote_items.*' )->get ();
                break;
            case OCEAN       :
                $ptlBuyerQuotes = DB::table ( 'ocean_buyer_quote_items' )->where ( 'buyer_quote_id', $post->id )->select ( 'ocean_buyer_quote_items.*' )->get ();
                break;          
    }
    foreach ( $ptlBuyerQuotes as $ptlQuotesList ) {
        switch($service_id){                   
            case ROAD_PTL       :
            case RAIL           :
            case AIR_DOMESTIC   :
                if (!isset( $ptlFromLocationPincode [$post->from_location_id] )) {
                    $ptlFromLocationPincode [$post->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $post->from_location_id )->pluck ( 'pincode' );
                }
                if (!isset( $ptlToLocationPincode [$post->to_location_id] )) {
                    $ptlToLocationPincode [$post->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $post->to_location_id )->pluck ( 'pincode' );
                }
                break;
            case AIR_INTERNATIONAL       :
                if (!isset( $ptlFromLocationPincode [$post->from_location_id] )) {
                    $ptlFromLocationPincode [$post->from_location_id] = DB::table ( 'lkp_airports' )->where ( 'id', $post->from_location_id )->pluck ( 'airport_name' );
                }
                if (!isset( $ptlToLocationPincode [$post->to_location_id] )) {
                    $ptlToLocationPincode [$post->to_location_id] = DB::table ( 'lkp_airports' )->where ( 'id', $post->to_location_id )->pluck ( 'airport_name' );
                }
                break;
            case OCEAN       :
                if (!isset( $ptlFromLocationPincode [$post->from_location_id] )) {
                    $ptlFromLocationPincode [$post->from_location_id] = DB::table ( 'lkp_seaports' )->where ( 'id', $post->from_location_id )->pluck ( 'seaport_name' );
                }
                if (!isset( $ptlToLocationPincode [$post->to_location_id] )) {
                    $ptlToLocationPincode [$post->to_location_id] = DB::table ( 'lkp_seaports' )->where ( 'id', $post->to_location_id )->pluck ( 'seaport_name' );
                }
                break;
        }
        
        switch($service_id){
            case ROAD_PTL           :
            case RAIL               :
            case AIR_DOMESTIC       :
            case AIR_INTERNATIONAL  :
            case OCEAN              :
                if (!isset( $ptlLoadTypes [$ptlQuotesList->lkp_load_type_id] )) {
                    $ptlLoadTypes [$ptlQuotesList->lkp_load_type_id] = DB::table ( 'lkp_load_types' )->where ( 'id', $ptlQuotesList->lkp_load_type_id )->pluck ( 'load_type' );
                }
                if (!isset( $ptlPackageTypes [$ptlQuotesList->lkp_packaging_type_id] )) {
                    $ptlPackageTypes [$ptlQuotesList->lkp_packaging_type_id] = DB::table ( 'lkp_packaging_types' )->where ( 'id', $ptlQuotesList->lkp_packaging_type_id )->pluck ( 'packaging_type_name' );
                }
                break;
        }
                
            }
        }
            $ptlFromLocationPincode = CommonComponent::orderArray($ptlFromLocationPincode);
            $ptlToLocationPincode = CommonComponent::orderArray($ptlToLocationPincode);
            $ptlLoadTypes = CommonComponent::orderArray($ptlLoadTypes);
            $ptlPackageTypes = CommonComponent::orderArray($ptlPackageTypes);
        //echo "<pre>"; print_r($postResults);die();
        $grid = DataGrid::source ( $Query );
        $grid->add ( 'id', 'ID', true )->style ( "display:none" );
        $grid->add ( 'dispatch_date', 'Dispatch Date', true)->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add ( 'fromLocation', 'From', true )->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add ( 'toLocation', 'To', true )->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add ( 'load_type', 'Load Type', true )->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add ( 'quote_access', 'Posted For', 'quote_access' )->attributes(array("class" => "col-md-1 padding-left-none"));
        $grid->add ( 'packaging_type_name', 'Package Type', true )->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add ( 'totalpackges', 'Packages', true )->attributes(array("class" => "col-md-1 padding-left-none hidden-md hidden-lg"));
        $grid->add ( 'lkp_post_status_id', 'Status', true)->style ( "display:none" );
        $grid->add ( 'show', 'dummycolumn', 'show' )->style ( "display:none" );
        $grid->add ( 'status', 'Status', false )->attributes(array("class" => "col-md-1 padding-left-none"));
        //$grid->add ( 'is_dispatch_flexible', 'isflexiablechecking', false )->style ( "display:none" );
        $grid->add ( 'delete', '', false )->attributes(array("class" => "col-md-1 padding-left-none"));        
        $grid->add ( 'from_location_id', 'from_location_id', 'from_location_id' )->style ( "display:none" );
        $grid->add ( 'to_location_id', 'to_location_id', 'to_location_id' )->style ( "display:none" );
        $grid->add ( 'is_commercial', 'is_commercial', 'is_commercial' )->style ( "display:none" );

        


        $grid->orderBy ( 'ptlbqi.id', 'desc' );
        $grid->paginate ( 5 );
        $grid->row ( function ($row) {  
            
        //below script for check load and package types multi or single
        switch(Session::get ( 'service_id' )){
            case ROAD_PTL       :
                $Qry = DB::table ( 'ptl_buyer_quotes as ptlbq' );
                $Qry->leftjoin ( 'ptl_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );         
                break;
            case RAIL       :
                $Qry = DB::table ( 'rail_buyer_quotes as ptlbq' );
                $Qry->leftjoin ( 'rail_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );            
                break;
            case AIR_DOMESTIC       :
                $Qry = DB::table ( 'airdom_buyer_quotes as ptlbq' );
                $Qry->leftjoin ( 'airdom_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );          
                break;
            case AIR_INTERNATIONAL       :
                $Qry = DB::table ( 'airint_buyer_quotes as ptlbq' );
                $Qry->leftjoin ( 'airint_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );          
                break;
            case OCEAN       :
                $Qry = DB::table ( 'ocean_buyer_quotes as ptlbq' );
                $Qry->leftjoin ( 'ocean_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );           
                break;
                }
                $Qry->where ( 'ptlbq.id', $row->cells [0]->value );
                
                    $Result = $Qry->select ( 'ptlbqi.lkp_load_type_id','ptlbqi.lkp_packaging_type_id')->get();
                    //echo "<pre>";print_r($Result);exit;
                    $flag=1;$flagp=1;
                    if(count($Result)!=1 && count($Result)>1){
                        for($i=0;$i<count($Result)-1;$i++){
                            if($Result[$i]->lkp_load_type_id!=$Result[$i+1]->lkp_load_type_id){
                                $flag=0;
                            }
                            if($Result[$i]->lkp_packaging_type_id!=$Result[$i+1]->lkp_packaging_type_id){
                            $flagp=0;
                            }
                                
                        }
                            if($flag==0){
                                $row->cells [4]->value ="multi";
                            }
                            if($flagp==0){
                                $row->cells [6]->value ="multi";
                            }
                    }
        
             
            
        $row->cells [0]->style ( 'display:none' );
        $buyerQuoteId = $row->cells [0]->style ( 'display:none' );  
        $data_link = url()."/getbuyercounteroffer/$buyerQuoteId";
        
        $row->cells [9]->style ( 'width:100%' );
                
        $serviceId = Session::get('service_id');
        
        $msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteId);
        $buyerCountId = count(PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteSellersQuotesPricesFromId($row->cells [0]->value));
        //$row->cells [9]->style ( 'display:none' );
        //count no of views
        $tableName = CommonComponent::getTableNameAsPerService(Session::get ( 'service_id' ));
        if(!empty($tableName)) {
            $countview = PtlBuyerGetQuoteBooknowComponent::updateBuyerQuoteDetailsViews($buyerQuoteId,$tableName);
        } else {
            $countview = PtlBuyerGetQuoteBooknowComponent::updateBuyerQuoteDetailsViews($buyerQuoteId,'ptl_buyer_quote_item_views');
        }
            
        if (!empty($countview)) {
            $viewcount = $countview;
        } else {
            $viewcount = 0;
        }   
        
        $dispatchDate = $row->cells[1]->value;
        $row->cells [1]->value = '<span><input type="checkbox" name="buyerpostptlcheck" id="buyerpostptlcheck" class="checkBoxClass gridbuyercheckbox" value='.$buyerQuoteId.'></span>'.CommonComponent::checkAndGetDate($dispatchDate);
    
                $row->cells [0]->style ( 'display:none' );
                $row->cells [1]->style ( 'display:none' );
                $row->cells [2]->style ( 'display:none' );
                $row->cells [3]->style ( 'display:none' );
                $row->cells [4]->style ( 'display:none' );
                $row->cells [5]->style ( 'display:none' );
                $row->cells [6]->style ( 'display:none' );
                $row->cells [7]->style ( 'display:none' );
                $row->cells [8]->style ( 'display:none' );
                $row->cells [9]->style ( 'width:100%' );
                $row->cells [10]->style ( 'display:none' );
                $row->cells [11]->style ( 'display:none' );
                $row->cells [12]->style ( 'display:none' );
                $row->cells [13]->style ( 'display:none' );
                $row->cells [14]->style ( 'display:none' );

                $id = $row->cells [0]->value;
                $dispatch_date = $row->cells [1]->value;

                $fromLocation = $row->cells [2]->value;
                $toLocation = $row->cells [3]->value;
                
                
                $fromLocationId = $row->cells [12]->value;
                $tolocationId = $row->cells [13]->value;
                $is_commercial = $row->cells [14]->value;
                
                $docs_buyer    =   CommonComponent::getGsaDocuments(1,$serviceId,$buyerQuoteId,$fromLocationId,$tolocationId,$is_commercial);

                $load_type = $row->cells [4]->value; //exit;
                $packaging_type_name = $row->cells [6]->value;
                
                
                $totalpackges = $row->cells [7]->value;
               // $lkp_post_status_id = $row->cells [7]->value;


                
                
                $lkp_post_status_id = $row->cells   [8]->value;
                if($lkp_post_status_id == CANCELLED) {
                    $row->cells [8]->value = "Deleted";
                } elseif($lkp_post_status_id == BOOKED) {
                    $row->cells [8]->value = "Booked";
                }elseif($lkp_post_status_id == OPEN) {
                    $row->cells [8]->value = "Open";
                }elseif($lkp_post_status_id == CLOSED) {
                    $row->cells [8]->value = "Closed";
                }else {
                    $row->cells [8]->value = "Open";
                }
                $lkp_post_status_id = $row->cells [8]->value;
                $posted_for = $row->cells [5]->value;
                
        
                if ($posted_for == "Private" && $lkp_post_status_id == "Open")  {
                	$editOption = "<a href='/editseller/$id'><i class='fa fa-edit' title='Edit'></i></a>";
                	//$buyer_id = "";
                } else {
                	$editOption = " ";
                	// $buyer_id ='buyerposts';
                }
                
                /*Srinu Added below code 
                 * 28-04-2016
                 * For Added leads count                 
                 */
                
                $matchedSellerPosts = BuyerMatchingComponent::getMatchedResults(Session::get ( 'service_id' ),$buyerQuoteId);

                $matchedIds = array();
                foreach($matchedSellerPosts as $matchedSellerPost){
                        $matchedIds[] = $matchedSellerPost->seller_post_id;
                }
                
                 //below script for check load and package types multi or single
        switch(Session::get ( 'service_id' )){
            case ROAD_PTL       :
                $getSellerLeadData = DB::table('ptl_seller_post_items as spi');
                $getSellerLeadData->leftjoin('ptl_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
                $getSellerLeadData->whereIn('spi.id', $matchedIds);
                $getSellerLeadData->where('spi.is_private', 0);
                $getSellerLeadData->select('sp.transaction_id as transaction_no','spi.*');
                $arraySellerLeadsData = $getSellerLeadData->get();        
                break;
            case RAIL       :
                $getSellerLeadData = DB::table('rail_seller_post_items as spi');
                $getSellerLeadData->leftjoin('rail_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
                $getSellerLeadData->whereIn('spi.id', $matchedIds);
                $getSellerLeadData->where('spi.is_private', 0);
                $getSellerLeadData->select('sp.transaction_id as transaction_no','spi.*');
                $arraySellerLeadsData = $getSellerLeadData->get();        
                break;
            case AIR_DOMESTIC       :
                $getSellerLeadData = DB::table('airdom_seller_post_items as spi');
                $getSellerLeadData->leftjoin('airdom_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
                $getSellerLeadData->whereIn('spi.id', $matchedIds);
                $getSellerLeadData->where('spi.is_private', 0);
                $getSellerLeadData->select('sp.transaction_id as transaction_no','spi.*');
                $arraySellerLeadsData = $getSellerLeadData->get();        
                break;
            case AIR_INTERNATIONAL       :
                $getSellerLeadData = DB::table('airint_seller_post_items as spi');
                $getSellerLeadData->leftjoin('airint_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
                $getSellerLeadData->whereIn('spi.id', $matchedIds);
                $getSellerLeadData->where('spi.is_private', 0);
                $getSellerLeadData->select('sp.transaction_id as transaction_no','spi.*');
                $arraySellerLeadsData = $getSellerLeadData->get();       
                break;
            case OCEAN       :
                $getSellerLeadData = DB::table('ocean_seller_post_items as spi');
                $getSellerLeadData->leftjoin('ocean_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
                $getSellerLeadData->whereIn('spi.id', $matchedIds);
                $getSellerLeadData->where('spi.is_private', 0);
                $getSellerLeadData->select('sp.transaction_id as transaction_no','spi.*');
                $arraySellerLeadsData = $getSellerLeadData->get();         
                break;
                }

                $leadscount = count($arraySellerLeadsData);
                
                
        
        
        $row->cells [9]->value .= " <div class='table-row '><a href='$data_link'>
            <div class='col-md-2 padding-left-none'>$dispatch_date</div>
            <div class='col-md-2 padding-left-none'>$fromLocation</div>
            <div class='col-md-2 padding-left-none'>$toLocation</div>
            <div class='col-md-2 padding-none'> $load_type</div>
            <div class='col-md-1 padding-none'> $posted_for</div>
            <div class='col-md-2 padding-none'>$packaging_type_name </div>
            <div class='col-md-1 padding-none hidden-md hidden-lg'>$totalpackges</div>
            <div class='col-md-1 padding-none'>$lkp_post_status_id </div></a>

            <div class='clearfix'></div>

            <div class='pull-left'>
                <div class='info-links'>
                    <a href='/getbuyercounteroffer/".$buyerQuoteId. "?type=messages'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>".count($msg_count['result'])."</span></a>
                    <a href='/getbuyercounteroffer/".$buyerQuoteId. "?type=quotes'><i class='fa fa-file-text-o'></i> Quotes<span class='badge'>$buyerCountId</span></a>
                    <a href='/getbuyercounteroffer/".$buyerQuoteId. "?type=leads'><i class='fa fa-thumbs-o-up'></i> Leads<span class='badge'>$leadscount</span></a>
                    <a href='#'><i class='fa fa-line-chart'></i> Market Analytics<span class='badge'>0</span></a>
                    <a href='/getbuyercounteroffer/".$buyerQuoteId. "?type=documentation'><i class='fa fa-file-text-o'></i> Documentation<span class='badge'>".count($docs_buyer)."</span></a>
                </div>
            </div>
            <div class='pull-right text-right'>
                <div class='info-links'> $editOption
        ";
        if ($lkp_post_status_id == 'Open') {
            $row->cells [9]->value .= "<a href='#' data-target='#cancelbuyerpostmodal' data-toggle='modal' onclick='setcancelbuyerpostid(".$buyerQuoteId.")' ><i class='fa fa-trash ptlbuyerpostdelete' title='Delete'></i></a>";
        }
         
             $row->cells [9]->value .= "<a class='views red'><i class='fa fa-eye' title='Views'></i> $viewcount</a>
                </div>
            </div>

        </div>";
            
        });

        // Functionality to build filters in the page starts
        $filter = DataFilter::source ( $Query );
        $filter->add ( 'ptlbq.from_location_id', '', 'select' )->options ( $ptlFromLocationPincode )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
        $filter->add ( 'ptlbq.to_location_id', '', 'select' )->options ( $ptlToLocationPincode )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
        $filter->add ( 'ptlbqi.lkp_packaging_type_id', 'Package Type', 'select' )->options ( $ptlPackageTypes )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
        $filter->add ( 'ptlbqi.lkp_load_type_id', 'Load Type', 'select' )->options ( $ptlLoadTypes )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
        $filter->submit ( 'search' );
        $filter->reset ( 'reset' );
        $filter->build ();
        // Functionality to build filters in the page ends
        $result = ['grid' => $grid, 'filter' => $filter];
        return $result;
        } catch (Exception $e) {
            
        }       
  }  
  
  /**
   * Buyer Posts List Page in COURIER (Ravi did that page on 22-02-2016)
   * Retrieval of data related to buyer posts list items to populate in the buyer list widget    *
   */
  public static function getCourierBuyerPostsList($service_id, $post_status, $enquiry_type,$delivery_type)
  {
    try {
        //echo "hhh";exit;
        // Filters values to populate in the page
        $ptlFromLocationPincode = array ("" => "From Location-Pincode");
        if(Session::get('delivery_type') == 1){
        $ptlToLocationPincode = array ("" => "To Location-Pincode");
        }else{
        $ptlToLocationPincode = array ("" => "To Country");
        }
        $ptlCourierTypes = array ("" => "Courier Type");
        $ptlPackageTypes = array ("" => "Package Type");
        $dispatchDate = '';
        $deliveryDate = '';
  
        // query to retrieve buyer posts list and bind it to the grid
        switch($service_id){
            case COURIER       :
                $Query = DB::table ( 'courier_buyer_quotes as ptlbq' );
                $Query->leftjoin ( 'courier_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
                break;
        }
  
        switch($service_id){
            case COURIER       :
                $Query->leftjoin ( 'lkp_courier_types as lct', 'lct.id', '=', 'ptlbqi.lkp_courier_type_id' );
                break;
        }
  
        switch($service_id){
            case COURIER       :
                $Query->leftjoin ( 'lkp_ptl_pincodes as ptlPins', 'ptlPins.id', '=', 'ptlbq.from_location_id' );
                $Query->leftjoin ( 'lkp_ptl_pincodes as ptlPinsTo', 'ptlPinsTo.id', '=', 'ptlbq.to_location_id' );
                break;
        }
        $Query->where ( 'ptlbq.created_by', Auth::User ()->id );
        $Query->groupBy('ptlbqi.buyer_quote_id');
        $Query->orderBy('ptlbqi.buyer_quote_id', 'DESC');
        $Query->where('ptlbq.lkp_post_status_id','!=',8);
        $Query->where('ptlbq.lkp_post_status_id','!=',7);
        $Query->where('ptlbq.lkp_post_status_id','!=',6);
        //$Query->sum('ptlbqi.number_packages as totalcnt');
  
        // conditions to make search
        if (isset ( $service_id ) && !empty($service_id)) {
            $Query->where ( 'ptlbq.lkp_service_id', '=', $service_id );
        }
        if (isset ( $delivery_type ) && !empty($delivery_type)) {
            $Query->where ( 'ptlbqi.lkp_courier_delivery_type_id', '=', Session::get('delivery_type') );
        }
        if (isset ( $post_status ) && !empty($post_status)) {
            $Query->where ( 'ptlbqi.lkp_post_status_id', '=', $post_status );
        }
        if (isset ( $_GET ['dispatch_date'] ) && $_GET ['dispatch_date'] != '') {
            $commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['dispatch_date']);
            $Query->where ( 'ptlbq.dispatch_date', '>=', $commonDispatchDate );
            $dispatchDate = $commonDispatchDate;
        }
        if (isset ( $_GET ['delivery_date'] ) && $_GET ['delivery_date'] != '') {
            $commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['delivery_date']);
            $Query->where ( 'ptlbq.dispatch_date', '<=', $commonDeliveryhDate );
            $deliveryDate = $commonDeliveryhDate;
        }
  
        //$postResults = $Query->selectRaw('sum(ptlbqi.number_packages) as totalnoofpackes');
        switch($service_id){
            case COURIER    :
                $postResults = $Query->select ('ptlbq.id','ptlbqi.number_packages','ptlbq.dispatch_date','ptlbq.delivery_date','ptlbq.from_location_id','ptlbq.to_location_id','ptlbq.lkp_post_status_id', 'lct.courier_type','ptlbq.lkp_quote_access_id','ptlPins.postoffice_name as fromLocation','ptlPinsTo.postoffice_name as toLocation','ptlbq.is_cancelled',DB::raw('sum(ptlbqi.number_packages) AS totalpackges') )->get ();
                break;
        }
        //echo "<pre>"; print_r($postResults);die();
        // Functionality to handle filters based on the selection starts
        foreach ( $postResults as $post ) {
            switch($service_id){
                case COURIER       :
                    $ptlBuyerQuotes = DB::table ( 'courier_buyer_quote_items' )->where ( 'buyer_quote_id', $post->id )->select ( 'courier_buyer_quote_items.*' )->get ();
                    break;
            }
            foreach ( $ptlBuyerQuotes as $ptlQuotesList ) {
                switch($service_id){
                    case COURIER    :
                        if (!isset( $ptlFromLocationPincode [$post->from_location_id] )) {
                            $ptlFromLocationPincode [$post->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $post->from_location_id )->pluck ( 'pincode' );
                        }
                        if (!isset( $ptlToLocationPincode [$post->to_location_id] )) {
                            if(Session::get('delivery_type') == 1){
                            $ptlToLocationPincode [$post->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $post->to_location_id )->pluck ( 'pincode' );
                            }else{
                            $ptlToLocationPincode [$post->to_location_id] = DB::table ( 'lkp_countries' )->where ( 'id', $post->to_location_id )->pluck ( 'country_name' );
                            }
                        }
                        break;
                }
  
                switch($service_id){
                    case ROAD_PTL           :
                    case RAIL               :
                    case AIR_DOMESTIC       :
                    case AIR_INTERNATIONAL  :
                    case COURIER            :
                        if (!isset( $ptlCourierTypes [$ptlQuotesList->lkp_courier_type_id] )) {
                            $ptlCourierTypes [$ptlQuotesList->lkp_courier_type_id] = DB::table ( 'lkp_courier_types' )->where ( 'id', $ptlQuotesList->lkp_courier_type_id )->pluck ( 'courier_type' );
                        }
                        break;
                    case OCEAN              :
                        if (!isset( $ptlLoadTypes [$ptlQuotesList->lkp_load_type_id] )) {
                            $ptlLoadTypes [$ptlQuotesList->lkp_load_type_id] = DB::table ( 'lkp_load_types' )->where ( 'id', $ptlQuotesList->lkp_load_type_id )->pluck ( 'load_type' );
                        }
                        if (!isset( $ptlPackageTypes [$ptlQuotesList->lkp_packaging_type_id] )) {
                            $ptlPackageTypes [$ptlQuotesList->lkp_packaging_type_id] = DB::table ( 'lkp_packaging_types' )->where ( 'id', $ptlQuotesList->lkp_packaging_type_id )->pluck ( 'packaging_type_name' );
                        }
                        break;
                }
  
            }
        }
  
        $grid = DataGrid::source ( $Query );
        $grid->add ( 'id', 'ID', true )->style ( "display:none" );
        $grid->add ( 'dispatch_date', 'Dispatch Date', true)->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add ( 'fromLocation', 'From', true )->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add ( 'toLocation', 'To', true )->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add ( 'courier_type', 'Courier Type', true )->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add ( 'number_packages', 'Packages', false )->attributes(array("class" => "col-md-1 padding-left-none"));
        $grid->add ( 'totalpackges', 'dummycolumn6', true )->attributes(array("class" => "col-md-1 padding-left-none hidden-md hidden-lg"));
        $grid->add ( 'lkp_post_status_id', 'Status', true)->style ( "display:none" );
        $grid->add ( 'show', 'dummycolumn', 'show' )->style ( "display:none" );
        $grid->add ( 'lkp_quote_access_id', 'Post For', false )->attributes(array("class" => "col-md-1 padding-left-none"));
        $grid->add ( 'status', 'Status', false )->attributes(array("class" => "col-md-1 padding-left-none"));
        $grid->add ( 'delete', '', false )->attributes(array("class" => "col-md-1 padding-left-none"));
        $grid->add ( 'to_location_id', 'dummyTolocation', 'to_location_id' )->style ( "display:none" );
        $grid->orderBy ( 'ptlbqi.id', 'desc' );
        $grid->paginate ( 5 );
        $grid->row ( function ($row) {
                
            //below script for check load and package types multi or single
            switch(Session::get ( 'service_id' )){
                case ROAD_PTL       :
                    $Qry = DB::table ( 'ptl_buyer_quotes as ptlbq' );
                    $Qry->leftjoin ( 'ptl_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
                    break;
                case RAIL       :
                    $Qry = DB::table ( 'rail_buyer_quotes as ptlbq' );
                    $Qry->leftjoin ( 'rail_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
                    break;
                case AIR_DOMESTIC       :
                    $Qry = DB::table ( 'airdom_buyer_quotes as ptlbq' );
                    $Qry->leftjoin ( 'airdom_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
                    break;
                case AIR_INTERNATIONAL       :
                    $Qry = DB::table ( 'airint_buyer_quotes as ptlbq' );
                    $Qry->leftjoin ( 'airint_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
                    break;
                case OCEAN       :
                    $Qry = DB::table ( 'ocean_buyer_quotes as ptlbq' );
                    $Qry->leftjoin ( 'ocean_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
                    break;
                case COURIER       :
                    $Qry = DB::table ( 'courier_buyer_quotes as ptlbq' );
                    $Qry->leftjoin ( 'courier_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
                    break;
            }
            $Qry->where ( 'ptlbq.id', $row->cells [0]->value );
  
                $Result = $Qry->select ( 'ptlbqi.lkp_courier_type_id','ptlbqi.lkp_courier_delivery_type_id')->get();
                //echo "<pre>";print_r($Result);exit;
                $flag=1;
                if(count($Result)!=1 && count($Result)>1){
                    for($i=0;$i<count($Result)-1;$i++){
                        if($Result[$i]->lkp_courier_type_id!=$Result[$i+1]->lkp_courier_type_id){
                            $flag=0;
                        }
                    }
                    if($flag==0){
                        $row->cells [4]->value ="multi";
                    }
                }
  
        
            $row->cells [0]->style ( 'display:none' );
            $buyerQuoteId = $row->cells [0]->style ( 'display:none' );
            $data_link = url()."/getbuyercounteroffer/$buyerQuoteId";
  
            $row->cells [8]->style ( 'width:100%' );
  
            $msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteId);
            $buyerCountId = count(PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteSellersQuotesPricesFromId($row->cells [0]->value));

            $tableName = CommonComponent::getTableNameAsPerService(Session::get ( 'service_id' ));
            if(!empty($tableName)) {
                $countview = PtlBuyerGetQuoteBooknowComponent::updateBuyerQuoteDetailsViews($buyerQuoteId,$tableName);
            } else {
                $countview = PtlBuyerGetQuoteBooknowComponent::updateBuyerQuoteDetailsViews($buyerQuoteId,'ptl_buyer_quote_item_views');
            }
                
            if (!empty($countview)) {
                $viewcount = $countview;
            } else {
                $viewcount = 0;
            }
  
            $dispatchDate = $row->cells[1]->value;
            $row->cells [1]->value = '<span><input type="checkbox" name="buyerpostptlcheck" id="buyerpostptlcheck" class="checkBoxClass gridbuyercheckbox" value='.$buyerQuoteId.'></span>'.CommonComponent::checkAndGetDate($dispatchDate);
  
            $row->cells [0]->style ( 'display:none' );
            $row->cells [1]->style ( 'display:none' );
            $row->cells [2]->style ( 'display:none' );
            $row->cells [3]->style ( 'display:none' );
            $row->cells [4]->style ( 'display:none' );
            $row->cells [5]->style ( 'display:none' );
            $row->cells [6]->style ( 'display:none' );
            $row->cells [7]->style ( 'display:none' );
            $row->cells [8]->style ( 'width:100%' );
            $row->cells [9]->style ( 'display:none' );
            $row->cells [10]->style ( 'display:none' );
            $row->cells [11]->style ( 'display:none' );
            $row->cells [12]->style ( 'display:none' );
  
  

            $dispatch_date = $row->cells [1]->value;
  
            $fromLocation = $row->cells [2]->value;
            if($Result[0]->lkp_courier_delivery_type_id == 1){
            $toLocation = $row->cells [3]->value;
            }else{
                $to_contry = DB::table ( 'lkp_countries' )->where ( 'id', $row->cells[12]->value )->pluck ( 'country_name' );
                $toLocation = $to_contry;
            }
  
            $load_type = $row->cells [4]->value; //exit;

  
  
            $totalpackges = $row->cells [6]->value;

            $lkp_post_status_id = $row->cells [7]->value;
            if($lkp_post_status_id == CANCELLED) {
                $row->cells [7]->value = "Deleted";
            } elseif($lkp_post_status_id == BOOKED) {
                $row->cells [7]->value = "Booked";
            }elseif($lkp_post_status_id == OPEN) {
                $row->cells [7]->value = "Open";
            }elseif($lkp_post_status_id == CLOSED) {
                    $row->cells [7]->value = "Closed";
            }else {
                $row->cells [7]->value = "Open";
            }
            $lkp_post_status_id = $row->cells [7]->value;
            
            $postFor = $row->cells [9]->value;
            $postForValue = CommonComponent::getQuoteAccessById($postFor);
            if($row->cells [4]->value == 'Document') {
                $corType=1;
            } else {
                $corType=2; 
            }
            
            $matchedSellerPosts = BuyerMatchingComponent::getMatchedResults(Session::get ( 'service_id' ),$buyerQuoteId);

            $matchedIds = array();
            foreach($matchedSellerPosts as $matchedSellerPost){
                    $matchedIds[] = $matchedSellerPost->seller_post_id;
            }

            $getSellerLeadData = DB::table('courier_seller_post_items as spi');
            $getSellerLeadData->leftjoin('courier_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
            $getSellerLeadData->whereIn('spi.id', $matchedIds);
            $getSellerLeadData->where('spi.is_private', 0);
            $getSellerLeadData->where('sp.lkp_courier_type_id', '=', $corType );
            $getSellerLeadData->select('sp.transaction_id as transaction_no','spi.*');
            $arraySellerLeadsData = $getSellerLeadData->get();


            $leadscount = count($arraySellerLeadsData);
            //count for buyer documents
            $serviceId = Session::get('service_id');
            $docs_buyer    =   CommonComponent::getGsaDocuments(1,$serviceId,$buyerQuoteId);    
  
            //$leadscount = count(BuyerMatchingComponent::getMatchedResults(Session::get ( 'service_id' ),$buyerQuoteId));
  
            $row->cells [8]->value = " <div class='table-row '><a href='$data_link'>
            <div class='col-md-2 padding-left-none'>$dispatch_date</div>
            <div class='col-md-2 padding-left-none'>$fromLocation</div>
            <div class='col-md-2 padding-left-none'>$toLocation</div>
            <div class='col-md-2 padding-none'> $load_type</div>
            <div class='col-md-1 padding-none'>$totalpackges </div>        
            <div class='col-md-1 padding-none'>$postForValue </div>           
            <div class='col-md-1 padding-none'>$lkp_post_status_id</div></a>";
            if($lkp_post_status_id == 'Open'){
            $row->cells [8]->value .= "<div class='col-md-1 padding-none text-right'><a href='javascript:void(0)' data-target='#cancelbuyerpostmodal' data-toggle='modal' onclick='setcancelbuyerpostid(".$buyerQuoteId.")' ><i class='fa fa-trash ptlbuyerpostdelete' title='Delete'></i></a></div>";
            }
            $row->cells [8]->value .= "<div class='clearfix'></div>

            <div class='pull-left'>
                <div class='info-links'>
                    <a href='/getbuyercounteroffer/".$buyerQuoteId. "?type=messages'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>".count($msg_count['result'])."</span></a>
                    <a href='/getbuyercounteroffer/".$buyerQuoteId. "?type=quotes'><i class='fa fa-file-text-o'></i> Quotes<span class='badge'>$buyerCountId</span></a>
                    <a href='/getbuyercounteroffer/".$buyerQuoteId. "?type=leads'><i class='fa fa-thumbs-o-up'></i> Leads<span class='badge'>$leadscount</span></a>
                    <a href='#'><i class='fa fa-line-chart'></i> Market Analytics<span class='badge'>0</span></a>
                    <a href='/getbuyercounteroffer/$buyerQuoteId?type=documentation'><i class='fa fa-file-text-o'></i> Documentation<span class='badge'>".count($docs_buyer)."</span></a>
                </div>
            </div>
            <div class='pull-right text-right'>
                <div class='info-links'>
                    <a><span class='views red'><i class='fa fa-eye' title='Views'></i> $viewcount</span></a>
                </div>
            </div>

        </div>";
                
        });
  
        // Functionality to build filters in the page starts
        $filter = DataFilter::source ( $Query );
        $filter->add ( 'ptlbq.from_location_id', '', 'select' )->options ( $ptlFromLocationPincode )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
        $filter->add ( 'ptlbq.to_location_id', '', 'select' )->options ( $ptlToLocationPincode )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
        $filter->add ( 'ptlbqi.lkp_packaging_type_id', 'Package Type', 'select' )->options ( $ptlPackageTypes )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
        $filter->add ( 'ptlbqi.lkp_courier_type_id', 'Courier Type', 'select' )->options ( $ptlCourierTypes )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
        $filter->submit ( 'search' );
        $filter->reset ( 'reset' );
        $filter->build ();
        $result = ['grid' => $grid, 'filter' => $filter];
        return $result;
    } catch (Exception $e) {
  
    }
  }
  
  
  // buyer search for seller posts result component
  public static function getPtlBuyerSearchList($request, $serviceId) {
    try {  
            $fromDate = '';
            $toDate = '';
            $sellerNames=array();
            if(isset($_REQUEST['ptlDispatchDate'][0])) {
                $fromDate = $_REQUEST['ptlDispatchDate'][0];
            }
            if(isset($_REQUEST['ptlDeliveryhDate'][0])) {
                $toDate = $_REQUEST['ptlDeliveryhDate'][0];
            }       


            //Price brand script for check from and to prices
            $prices = array(); 

                if(isset($_REQUEST['filter_set']) && $_REQUEST['filter_set'] == 1) {
                                    $ptlBuyerSearchform=Session::get('request');
                //below values set result page in date text boxes
                                if(isset($ptlBuyerSearchform['ptlDispatchDate'][0])) {
                                        $fromDate = $ptlBuyerSearchform['ptlDispatchDate'][0];
                                }
                                if(isset($ptlBuyerSearchform['ptlDeliveryhDate'][0])) {
                                        $toDate = $ptlBuyerSearchform['ptlDeliveryhDate'][0];
                                }


                if (isset ( $_REQUEST['from_location_id'] ) && $_REQUEST['from_location_id'] != '') {
                    for ($j=0;$j<count($ptlBuyerSearchform['ptlFromLocation']);$j++) {
                                            if (isset($_REQUEST['from_location_id'][$j]) && $_REQUEST['from_location_id'][$j] !="")
                                            {
                                                $ptlBuyerSearchform['ptlFromLocation'][$j]=$_REQUEST['from_location_id'][$j];
                                            }else{
                                                $ptlBuyerSearchform['ptlFromLocation'][$j]=$_REQUEST['from_location_id'][0];
                                            }
                                            
                                        }
                }
                if (isset ( $_REQUEST['to_location_id'] ) && $_REQUEST['to_location_id'] != '') {                   
                                    

                                    for ($j=0;$j<count($ptlBuyerSearchform['ptlToLocation']);$j++) {
                                            if (isset($_REQUEST['to_location_id'][$j]) && $_REQUEST['to_location_id'][$j] !="")
                                            {
                                                $ptlBuyerSearchform['ptlToLocation'][$j]=$_REQUEST['to_location_id'][$j];
                                            }else{
                                                $ptlBuyerSearchform['ptlToLocation'][$j]=$_REQUEST['to_location_id'][0];
                                            }
                                            
                                        }
                }

                                $trackingfilter = array();
                if (isset ( $_REQUEST ['ptl_tracking_milestone'] ) && $_REQUEST ['ptl_tracking_milestone'] != '' && $_REQUEST ['ptl_tracking_milestone']==1) {                  
                    $trackingfilter[] =  $_REQUEST['ptl_tracking_milestone'];
                }
                if (isset ( $_REQUEST ['ptl_tracking_realtime'] ) && $_REQUEST ['ptl_tracking_realtime'] != '' && $_REQUEST ['ptl_tracking_realtime']==2) {
                                    $trackingfilter[] =  $_REQUEST['ptl_tracking_realtime'];
                }
                                    $ptlBuyerSearchform['tracking'] =  $trackingfilter;
                //Date flexiable select dropdwon
                if (isset ( $_REQUEST ['date_flexiable'] ) && $_REQUEST ['date_flexiable'] != '') {
                    $ptlBuyerSearchform['ptlDispatchDate'][0] = $_REQUEST['date_flexiable'];
                }   
                if (isset ( $_REQUEST ['selected_users'] ) && $_REQUEST ['selected_users'] != '') {
                    $ptlBuyerSearchform['selected_users'] = $_REQUEST['selected_users'];
                }
                if (isset ( $_REQUEST ['selected_payments'] ) && $_REQUEST ['selected_payments'] != '') {
                    $ptlBuyerSearchform['selected_payments'] = $_REQUEST['selected_payments'];
                }
                
                if (isset ( $_REQUEST ['is_commercial'] ) && $_REQUEST ['is_commercial'] != '') {
                    $ptlBuyerSearchform['is_commercial'] = $_REQUEST['is_commercial'];
                }
                
                $Query_buyers_for_sellers = BuyerSearchComponent::search ($roleId=null,$serviceId,$statusId=null, $ptlBuyerSearchform );
                    

                //added for results after filtering
                    if(Session::get('show_layered_filter')!=''){
                        if(!empty($Query_buyers_for_sellers))
                        Session::put('show_layered_filter',1);
                        if (isset($_REQUEST['date_flexiable']) && $_REQUEST['date_flexiable']!='') {
                            foreach ( $Query_buyers_for_sellers as $seller_post_item ) {
                                    $sellerNames[$seller_post_item->seller_id] = $seller_post_item->username;
                            }
                            Session::put('layered_filter', $sellerNames);
                        }
                    }//added for results after filtering
                } else {      
                    if(isset($request['ptlFromLocation']))  {           
                    $cntLocations = count($request['ptlFromLocation']);
                    for ($i=0;$i<$cntLocations;$i++) {                      
                    $request['ptlFromToLocations'][$i] = $request['ptlFromLocation'][$i]. ',' .$request['ptlToLocation'][$i];
                    }
                    }
                    Session::put('ptlBuyerSearchform', $request);  
                    

                    
                    $Query_buyers_for_sellers = BuyerSearchComponent::search ($roleId=null,$serviceId,$statusId=null, $request );
                    //echo "<pre>";print_R($Query_buyers_for_sellers);//die;
                    if (!empty ( $Query_buyers_for_sellers )) {
                                            foreach ( $Query_buyers_for_sellers as $seller_post_item ) {
                                                //$resp   =   BuyerComponent::priceCalculations($seller_post_item);
                                                //$prices[] = ceil($resp['tot']);
                                                if (! isset ( $sellerNames [$seller_post_item->seller_id] )) {
                                                        $sellerNames[$seller_post_item->seller_id] = $seller_post_item->username;
                                                }
                                                Session::put('layered_filter', $sellerNames);
                                                if (! isset ( $paymentMethods [$seller_post_item->lkp_payment_mode_id] )) {
                                                    $paymentMethods[$seller_post_item->lkp_payment_mode_id] = $seller_post_item->paymentmethod;
                                                }
                                                Session::put('layered_filter_payments', $paymentMethods);
                                                if (! isset ( $from [$seller_post_item->from_location_id] )) {
                                                    $from[$seller_post_item->from_location_id] = $seller_post_item->search_from_pincode;
                                                }
                                                Session::put('layered_filter_from_location', $from);
                                                if (! isset ( $to [$seller_post_item->to_location_id] )) {
                                                    $to[$seller_post_item->to_location_id] = $seller_post_item->search_to_pincode;
                                                }
                                                Session::put('layered_filter_to_location', $to);
                                            }
                                        }else{
                                            Session::put('layered_filter_to_location', '');
                                            Session::put('layered_filter_from_location', '');
                                            Session::put('layered_filter', '');
                                            Session::put('layered_filter_payments', '');
                                            Session::put('show_layered_filter','');
                                        }
                }
            if (empty ( $Query_buyers_for_sellers ) && !isset($_REQUEST['filter_set'])) {
                CommonComponent::searchTermsSendMail ();
            }

            switch($serviceId){
                case ROAD_PTL   : 
                case RAIL       :
                case AIR_DOMESTIC   :
                case AIR_INTERNATIONAL   :
                case OCEAN   :
                case COURIER:

                    $Query_buyers_for_sellersnew = array();
                    foreach($Query_buyers_for_sellers as $Query_buyers_for_seller){
                        $resp = BuyerComponent::priceCalculations($Query_buyers_for_seller);
                        $Query_buyers_for_seller->newprice = isset($resp['tot']) ? $resp['tot'] : 0;
                        $prices[] = $Query_buyers_for_seller->newprice;
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
                            if($Query_buyers_for_sellersnewrow->newprice >= $pricefrom && $Query_buyers_for_sellersnewrow->newprice <= $priceto){

                            }else{
                                unset($Query_buyers_for_sellersnew[$key]);
                            }
                        }
                        $Query_buyers_for_sellers = $Query_buyers_for_sellersnew;
                    }
                    
                    break;
        }

        if (empty ( $Query_buyers_for_sellers )) {
            Session::put('show_layered_filter','');
        }
        //echo "<pre>";print_R($Query_buyers_for_sellers);die;
        //echo "<pre>";print_R($_REQUEST);echo "</pre>";

        $grid = DataGrid::source ( $Query_buyers_for_sellers );         
        $grid->add ( 'id', 'ID', true )->style ( "display:none" );
        $grid->add ( 'username', 'Vendor Name', true )->attributes(array("class" => "col-md-4 padding-left-none"));
        $grid->add ( 'transitdays', 'Transit Days', false )->attributes(array("class" => "col-md-3 padding-left-none"));
        $grid->add ( 'price', 'Price (<i class="fa fa-inr fa-1x"></i>)', true )->attributes(array("class" => "ccol-md-3 padding-left-none"));
        $grid->add ( 'transaction_id', 'Showgrid', true )->style ( "display:none" );        
        $grid->add ( 'seller_id', 'SellerId', true )->style ( "display:none" ); 
        $grid->add ( 'initial_quote_price', 'Initial quote Prices', true )->style ( "display:none" );
        $grid->add ( 'counter_quote_price', 'Counter quote Prices', true )->style ( "display:none" );
        $grid->add ( 'final_quote_price', 'Final quote Prices', true )->style ( "display:none" );
        $grid->add ( 'from_location_id', 'from_location_id', true )->style ( "display:none" );
        $grid->add ( 'kg_per_cft', 'kg_per_cft', true )->style ( "display:none" );
        $grid->add ( 'pickup_charges', 'pickup_charges', true )->style ( "display:none" );
        $grid->add ( 'delivery_charges', 'delivery_charges', true )->style ( "display:none" );
        $grid->add ( 'oda_charges', 'oda_charges', true )->style ( "display:none" );
        $grid->add ( 'to_location_id', 'to_location_id', true )->style ( "display:none" );
        $grid->add ( 'frompostoffice_name', 'frompostoffice_name', true )->style ( "display:none" );
        $grid->add ( 'topostoffice_name', 'topostoffice_name', true )->style ( "display:none" );
        $grid->add ( 'frompincode', 'frompincode', true )->style ( "display:none" );
        $grid->add ( 'topincode', 'topincode', true )->style ( "display:none" );
        $grid->add ( 'search_from_pincode', 'search_from_pincode', true )->style ( "display:none" );
        $grid->add ( 'search_to_pincode', 'search_to_pincode', true )->style ( "display:none" );
        $grid->add ( 'from_date', 'from_date', true )->style ( "display:none" );
        $grid->add ( 'to_date', 'to_date', true )->style ( "display:none" );
        $grid->add ( 'paymentmethod', 'paymentmethod', true )->style ( "display:none" );
        $grid->add ( 'tracking', 'tracking', true )->style ( "display:none" );
        $grid->add ( 'docket_charge_price', 'docket_charge_price', true )->style ( "display:none" );
        $grid->add ( 'units', 'units', false )->style ( "display:none" );
        $grid->add ( 'cancellation_charge_text', 'Cancelation Text', false )->style ( "display:none" );
        $grid->add ( 'cancellation_charge_price', 'Cancelation Prize', false )->style ( "display:none" );
        $grid->add ( 'docket_charge_text', 'Docket Text', false )->style ( "display:none" );
        $grid->add ( 'other_charge1_text', 'Other1 Text', false )->style ( "display:none" );
        $grid->add ( 'other_charge1_price', 'Other1 Prize', false )->style ( "display:none" );
        $grid->add ( 'other_charge2_text', 'Other2 Text', false )->style ( "display:none" );
        $grid->add ( 'other_charge2_price', 'Other2 Prize', false )->style ( "display:none" );
        $grid->add ( 'other_charge3_text', 'Other3 Text', false )->style ( "display:none" );
        $grid->add ( 'other_charge3_price', 'Other3 Price', false )->style ( "display:none" );
        //$grid->add ( 'terms_conditions', 'terms_conditions', false )->style ( "display:none" );
        $grid->add ( 'fuel_surcharge', 'Other3 Price', false )->style ( "display:none" );
        $grid->add ( 'cod_charge', 'Other3 Price', false )->style ( "display:none" );
        $grid->add ( 'freight_collect_charge', 'Other3 Price', false )->style ( "display:none" );
        $grid->add ( 'arc_charge', 'Other3 Price', false )->style ( "display:none" );
        $grid->add ( 'lkp_payment_mode_id', 'Other3 Price', false )->style ( "display:none" );
        $grid->add ( 'max_weight_accepted', 'Other3 Price', false )->style ( "display:none" );
        $grid->add ( 'increment_weight', 'Other3 Price', false )->style ( "display:none" );
        $grid->add ( 'is_incremental', 'Other3 Price', false )->style ( "display:none" );
        $grid->add ( 'rate_per_increment', 'Other3 Price', false )->style ( "display:none" );
        $grid->add ( 'maximum_value', 'Other3 Price', false )->style ( "display:none" );

        $grid->orderBy ( 'id', 'desc' );
        $grid->paginate ( 5 );
  
        $grid->row ( function ($row) {
            $row->cells [0]->style ( 'display:none' );              
            $sellerPostId = $row->cells [0]->value;
            $row->cells [1]->style ( 'display:none' );
            $row->cells [2]->style ( 'display:none' );
            $row->cells [3]->style ( 'display:none' );
            $row->cells [5]->style ( 'display:none' );
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
            $row->cells [19]->style ( 'display:none' );
            $row->cells [20]->style ( 'display:none' );     
            $row->cells [21]->style ( 'display:none' );
            $row->cells [22]->style ( 'display:none' );
            $row->cells [23]->style ( 'display:none' );
            $row->cells [24]->style ( 'display:none' );
            $row->cells [25]->style ( 'display:none' );
            $row->cells [26]->style ( 'display:none' );
            $row->cells [27]->style ( 'display:none' );
            $row->cells [28]->style ( 'display:none' );
            $row->cells [29]->style ( 'display:none' );
            $row->cells [30]->style ( 'display:none' );
            $row->cells [31]->style ( 'display:none' );
            $row->cells [32]->style ( 'display:none' );
            $row->cells [33]->style ( 'display:none' );
            $row->cells [34]->style ( 'display:none' );
            $row->cells [35]->style ( 'display:none' );
            $row->cells [36]->style ( 'display:none' );
            $row->cells [37]->style ( 'display:none' );
            $row->cells [38]->style ( 'display:none' );
            $row->cells [39]->style ( 'display:none' );
            $row->cells [40]->style ( 'display:none' );
            $row->cells [41]->style ( 'display:none' );
            $row->cells [42]->style ( 'display:none' );
            $row->cells [43]->style ( 'display:none' );
            $row->cells [44]->style ( 'display:none' );
            $row->cells [45]->style ( 'display:none' );

          //  $row->cells [27]->style ( 'display:none' ); 

            $vendorName = $row->cells [1]->value;
            $transDays = $row->cells [2]->value;
            $price = $row->cells [3]->value;
            $transaction_id=$row->cells[4]->value;
            $sellerId = $row->cells [5]->value;
            $fromLocationId = $row->cells [9]->value;
            $kgforCft = $row->cells [10]->value;            
            $pickupcharges = $row->cells [11]->value;
            $deliverycharges = $row->cells [12]->value;
            $odacharges = $row->cells [13]->value;  
            $toLocationId = $row->cells [14]->value;            
            $searchFrompincode = $row->cells [19]->value;
            $searchTopincode = $row->cells [20]->value;     

            $paymentmode = $row->cells [23]->value;
            $tracking = $row->cells [24]->value; 
            $docket_charge_price = $row->cells [25]->value; 
            $daysUnits = $row->cells [26]->value;

            //Additional Charges
            
            $canprice = $row->cells [28]->value;
            $othertext1 = $row->cells [30]->value;
            $otherprice1 = $row->cells [31]->value;
            $othertext2 = $row->cells [32]->value;
            $otherprice2 = $row->cells [33]->value;
            $othertext3 = $row->cells [34]->value;
            $otherprice3 = $row->cells [35]->value;
            $fuelsurcharge = $row->cells [36]->value;
            $codcharge = $row->cells [37]->value;
            $freightcollectcharge = $row->cells [38]->value;
            $arccharge = $row->cells [39]->value;
            $paymentmodeid = $row->cells [40]->value;
            $max_weight_accepted = $row->cells [41]->value;
            $incremental_weight = $row->cells [42]->value;
            $is_incremental = $row->cells [43]->value;
            $rate_per_increment = $row->cells [44]->value;
            $maximumvalue = $row->cells [45]->value;

            $request_buyer_data_chk = Session::get('ptlBuyerSearchform');
            $searched_delivery_type = '';
            $searched_courier_type = '';
            if(Session::get('service_id') == COURIER){
                $searched_delivery_type = $request_buyer_data_chk['post_delivery_types'][0];
                $searched_courier_type = $request_buyer_data_chk['courier_types'][0];
            }


            $tracking_text = CommonComponent::getTrackingType($tracking);
            $track_type = '<i class="fa fa-signal"></i>&nbsp;'.$tracking_text;
            
            if ($paymentmode == 'Advance') {
                $paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
            }elseif ($paymentmode == 'Credit'){
            		if(Session::get('service_id') == ROAD_PTL)
						$credit_days = CommonComponent::getCreditdays($sellerPostId,'ptl_seller_posts','ptl_seller_post_items');
            		elseif(Session::get('service_id') == RAIL)
            			$credit_days = CommonComponent::getCreditdays($sellerPostId,'rail_seller_posts','rail_seller_post_items');
            		elseif(Session::get('service_id') == AIR_DOMESTIC)
            			$credit_days = CommonComponent::getCreditdays($sellerPostId,'airdom_seller_posts','airdom_seller_post_items');
            		elseif(Session::get('service_id') == AIR_INTERNATIONAL)
            			$credit_days = CommonComponent::getCreditdays($sellerPostId,'airint_seller_posts','airint_seller_post_items');
            		elseif(Session::get('service_id') == COURIER)
            			$credit_days = CommonComponent::getCreditdays($sellerPostId,'courier_seller_posts','courier_seller_post_items');
            		elseif(Session::get('service_id') == OCEAN)
            			$credit_days = CommonComponent::getCreditdays($sellerPostId,'ocean_seller_posts','ocean_seller_post_items');
            		
					$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode.' | '.$credit_days[0]->credit_period.' '.$credit_days[0]->credit_period_units;
				} else {
                $paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode;
            }
            
            $buyerId = Auth::User()->id;            

            $serviceId = Session::get('service_id');
            switch($serviceId){
                        case ROAD_PTL:
                        case RAIL:
                        case AIR_DOMESTIC:
                        case COURIER:
                            $newFromLocationName    =   CommonComponent::getPinName($fromLocationId);
                            $newToLocationName  =   CommonComponent::getPinName($toLocationId);
                        break;
                        case AIR_INTERNATIONAL:
                            $newFromLocationName    =   CommonComponent::getAirportName($fromLocationId);
                            $newToLocationName  =   CommonComponent::getAirportName($toLocationId);
                            break;
                        case OCEAN:
                            $newFromLocationName    =   CommonComponent::getSeaportName($fromLocationId);
                            $newToLocationName  =   CommonComponent::getSeaportName($toLocationId);
                            break;
                    }           
            
            switch($serviceId){
                case ROAD_PTL:
                    $terms =  CommonComponent::getTermsAndConditions($sellerPostId,'ptl_seller_posts','ptl_seller_post_items');
                    break;
                case RAIL:
                    $terms =  CommonComponent::getTermsAndConditions($sellerPostId,'rail_seller_posts','rail_seller_post_items');
                    break;
                case AIR_DOMESTIC:
                    $terms =  CommonComponent::getTermsAndConditions($sellerPostId,'airdom_seller_posts','airdom_seller_post_items');
                    break;
                case COURIER:
                    $terms =  CommonComponent::getTermsAndConditions($sellerPostId,'courier_seller_posts','courier_seller_post_items');
                    break;  
                case AIR_INTERNATIONAL:
                    $terms =  CommonComponent::getTermsAndConditions($sellerPostId,'airint_seller_posts','airint_seller_post_items');
                    break;
                case OCEAN:
                    $terms =  CommonComponent::getTermsAndConditions($sellerPostId,'ocean_seller_posts','ocean_seller_post_items');
                    break;
            }
            $ptlBuyerSessionSearch=Session::get('ptlBuyerSearchform');
            unset($ptlBuyerSessionSearch['_token']);unset($ptlBuyerSessionSearch['search']);
            unset($ptlBuyerSessionSearch['price_from']);unset($ptlBuyerSessionSearch['price_to']);
            unset($ptlBuyerSessionSearch['priceFrom']);unset($ptlBuyerSessionSearch['priceTo']);
            unset($ptlBuyerSessionSearch['price']);unset($ptlBuyerSessionSearch['price']);
            
            $new_array = array();  //<--- This is the new array you're building
            if(isset($_REQUEST['filter_set']) && $_REQUEST['filter_set'] == 1) {
            unset($ptlBuyerSessionSearch['ptlFromLocation']);
            unset($ptlBuyerSessionSearch['ptlToLocation']);
            foreach($ptlBuyerSessionSearch['ptlFromToLocations'] as $element)
            {
                    $arr    =   explode(',',$element);
                    $ptlBuyerSessionSearch['ptlFromLocation'][]=$arr[0];
                    $ptlBuyerSessionSearch['ptlToLocation'][]=$arr[1];
            }
            }//echo "<pre>";print_r($ptlBuyerSessionSearch);exit;
            foreach($ptlBuyerSessionSearch as $i=>$element)
            {
                if(is_array($element) && !empty($element)){
                    foreach($element as $j=>$sub_element)
                    {
                        
                        $new_array[$j][$i] = $sub_element; //We are basically inverting the indexes
                    }
                }
            }
            $tot=0;
             
            $url = '/buyerbooknowforsearch/'.$sellerPostId;
            $row->cells [4]->value = "<form method='GET'role='form' action='$url' id='addptlbuyersearchbooknow_$sellerPostId' name='addptlbuyersearchbooknow_$sellerPostId'>
                                     <div class='search-items ' id='$sellerPostId'>
                                        <div class='col-md-4 padding-left-none'>$vendorName
                                            <div class='red'>
                                                <i class='fa fa-star'></i> 
                                                <i class='fa fa-star'></i> 
                                                <i class='fa fa-star'></i>
                                            </div>
                                         </div>
                                         <div class='col-md-3 padding-left-none'>$transDays $daysUnits</div>
                            <div class='col-md-3 padding-left-none' id='buyer_post_price_$sellerPostId' data-price='$price'>".$price."</div>

                            <div class='col-md-2 padding-right-none'>
                                <input type='submit' class='btn red-btn pull-right ptl_buyerbooknow_details' data-url='$url'
                                        data-buyerpostofferid='$sellerPostId' data-booknow_list='$sellerPostId' value='Book Now' />
                            </div>
                            <div class='clearfix'></div>
                            <div class='pull-left'>
                                <div class='info-links'>
                                    <a href='#'>$track_type</a>
                                    <a href='#'>$paymentType</a>
                                </div>
                            </div>
                                <div class='pull-right text-right'>
                                    <div class='info-links'>
                                    <a class='show-data-link'>
                                        <span class='ptlBuyerDetailsSlide spot_transaction_details hidden-xs'  data-ptlSellerListId=$sellerPostId style='cursor:pointer'><span class='show_details' style='display: inline;'>+</span><span class='hide_details' style='display: none;'>-</span> Details</span> 
                                        <a href='#' class='new_message' data-transaction_no='".$transaction_id."' data-userid='".$sellerId."' data-buyerquoteitemid='".$sellerPostId."'><i class='fa fa-envelope-o'></i>
                                         </a>
                                    </div>
                                </div>
                            <div class='col-md-12 padding-none ptlBuyerDetailsList-data-div ptlBuyerDetailsList_$sellerPostId' style='display:none;'>

                                <div class='col-md-12 padding-top'>
                                    <div class='col-md-3 padding-left-none data-fld'>
                                        <span class='data-head'>Base Freight</span>
                                         <span class='data-value' id='basic_$sellerPostId'></span>
                                    </div>";
            $door_pick='';$door_delivery='';
            //echo "<pre>";print_r($new_array);//exit;
            foreach($new_array as $ptlSessionLineitems){
                if($serviceId!=AIR_INTERNATIONAL && $serviceId!=OCEAN && $serviceId!=COURIER){
                    $door_pick = $ptlSessionLineitems['ptlDoorpickup'];
                    $door_delivery = $ptlSessionLineitems['ptlDoorDelivery'];
                    
                    
                    if($fromLocationId==$ptlSessionLineitems['ptlFromLocation'] && $toLocationId==$ptlSessionLineitems['ptlToLocation']){
                        if(isset($door_pick) && $door_pick!="" && $door_pick!=0){
                            $row->cells [4]->value .= "<div class='col-md-3 padding-left-none data-fld'>
                            <span class='data-head'>Doorpickup Charges </span>
                             <span class='data-value'  id='doorpickup_$sellerPostId'>".CommonComponent::getPriceType($pickupcharges)."</span>
                            </div>";
                        }
                        if(isset($door_delivery) && $door_delivery!="" && $door_delivery!=0){
                        $row->cells [4]->value .= "<div class='col-md-3 padding-left-none data-fld'>
                                <span class='data-head'>Doordelivery Charges </span>
                                 <span class='data-value'  id='doordelivery_$sellerPostId'>$deliverycharges</span>
                                </div>";
                        }
                    }
                }
            }
            
            $checkOda = CommonComponent::buyerODACheck($toLocationId,$serviceId,$sellerId);
            if($checkOda == 0) {
                $odaPrice=0;
            } else {
                $odaPrice = $odacharges;
            }           
            //exit;
             $row->cells [4]->value .= "<div class='col-md-3 padding-left-none data-fld'>
                                        <span class='data-head'>Total Price </span>
                                        <span class='data-value big-value'  id='total_$sellerPostId'></span>
                                        <input id='total_search_booknow_price_$sellerPostId' name='total_search_booknow_price_$sellerPostId' type='hidden' value=''>
                                    </div>
                                     </div>";

             if($serviceId == COURIER){
                $row->cells [4]->value .="<div class='col-md-12 padding-top'>
                <div class='col-md-3 padding-left-none data-fld'>
                <span class='data-head'>Conversion Factor(CCM/KG)</span>
              <span >".$kgforCft." </span>
               </div><div class='col-md-3 padding-left-none data-fld'>
                <span class='data-head'>Maximum Value</span>
              <span >Rs. ".$maximumvalue." /-</span>
               </div></div>";
                $row->cells [4]->value .=" <div class='col-md-12 padding-top'>
                <div class='col-md-2 padding-left-none data-fld'>
                <span class='data-head'>Fuel Surcharge</span>
              <span >".$fuelsurcharge." %</span>
               </div>";

                if($paymentmodeid == CASH_ON_DELIVERY){
                   $row->cells [4]->value .="<div class='col-md-2 padding-left-none data-fld'>
                    <span class='data-head'>COD</span>
                  <span >".$codcharge." %</span>
                   </div>";
                }    
               $row->cells [4]->value .="<div class='col-md-2 padding-left-none data-fld'>
                <span class='data-head'>Freight Collect</span>
              <span >Rs. ".$freightcollectcharge." /-</span>
               </div><div class='col-md-2 padding-left-none data-fld'>
                <span class='data-head'>ARC</span>
              <span >".$arccharge." %</span>
               </div></div>";
            }
             $row->cells [4]->value .= "<div class='col-md-12 padding-top'>
                                    
                                    <div class='col-md-3 padding-left-none data-fld'>
                                        <span class='data-head'>Cancellation Charges</span>
                                         <span class='data-value'  id='additional_$sellerPostId'>
                                         Rs. ".CommonComponent::getPriceType($canprice)."</span>
                                    </div>
                                    <div class='col-md-3 padding-left-none data-fld'>
                                        <span class='data-head'>Other Charges</span>
                                         <span class='data-value'  id='additional_$sellerPostId'>
                                         Rs. ".CommonComponent::getPriceType($docket_charge_price)."</span>
                                    </div>";
                                    
                                    
                                    if($serviceId != OCEAN && $serviceId != AIR_INTERNATIONAL){
                                     if($checkOda==1){
                                        $row->cells [4]->value .=" <div class='col-md-2 padding-left-none data-fld'>
                                        <span class='data-head'>ODA Charges</span>
                                      <span >Rs. ".CommonComponent::getPriceType($odaPrice)." </span>
                                       </div>";
                                     }
        							}
                                    if($othertext1!='' && $otherprice1!=''){
                                    $row->cells [4]->value .=" <div class='col-md-2 padding-left-none data-fld'>
                                        <span class='data-head'>".$othertext1."</span>
                                    <span >Rs. ".CommonComponent::getpricetype($otherprice1)."</span>
                                    </div>";
                                    }
                                    if($othertext2!='' && $otherprice2!=''){
                                    $row->cells [4]->value .=" <div class='col-md-2 padding-left-none data-fld'>
                                        <span class='data-head'>".$othertext2."</span>
                                    <span >Rs. ".CommonComponent::getpricetype($otherprice2)."</span>
                                    </div>";
                                    }
                                    if($othertext3!='' && $otherprice3!=''){
                                    $row->cells [4]->value .=" <div class='col-md-2 padding-left-none data-fld'>
                                        <span class='data-head'>".$othertext3."</span>
                                    <span >Rs. ".CommonComponent::getpricetype($otherprice3)." </span>
                                    </div>";
                                    }
                                    $row->cells [4]->value .="</div>
                               
                                
                                  <div class='col-md-12 padding-top'>
                                    
                                    <div class='col-md-12 padding-left-none data-fld'>
                                        <span class='data-head'>Terms & Conditions</span>
                                    <span >".$terms."</span>
                                    </div>
                                </div>
                                <div class='clearfix'></div>";
                                                                //$serviceId = Session::get('service_id');
                                                                switch($serviceId){
                                                                    case ROAD_PTL:
                                                                    //CommonComponent::viewCountForSeller(Auth::User()->id,$row->cells [0]->value,'ptl_seller_post_item_views');
                                                                        $service_wt='CFT';
                                                                        break;
                                                                    case RAIL:
                                                                    //CommonComponent::viewCountForSeller(Auth::User()->id,$row->cells [0]->value,'rail_seller_post_item_views');
                                                                        $service_wt='CFT';
                                                                        break;
                                                                    case AIR_DOMESTIC:
                                                                    //CommonComponent::viewCountForSeller(Auth::User()->id,$row->cells [0]->value,'airdom_seller_post_item_views');
                                                                        $service_wt='CCM';
                                                                        break;
                                                                    case COURIER:
                                                                    //CommonComponent::viewCountForSeller(Auth::User()->id,$row->cells [0]->value,'courier_seller_post_item_views');
                                                                        $service_wt='CCM';
                                                                        break;
                                                                    case AIR_INTERNATIONAL:
                                                                   // CommonComponent::viewCountForSeller(Auth::User()->id,$row->cells [0]->value,'airint_seller_post_item_views');
                                                                        $service_wt='CCM';
                                                                        break;
                                                                    case OCEAN:
                                                                    //CommonComponent::viewCountForSeller(Auth::User()->id,$row->cells [0]->value,'ocean_seller_post_item_views');
                                                                        $service_wt='CBM';
                                                                        break;
                                                                    default:
                                                                    //CommonComponent::viewCountForSeller(Auth::User()->id,$row->cells [0]->value,'ptl_seller_post_item_views');
                                                                        $service_wt='CFT';
                                                                        break;
                                                                }

                                if($serviceId == COURIER){
                                    if($searched_delivery_type == IS_DOMESTIC){
                                        $row->cells [4]->value.="<h2 class='sub-head'><span class='from-head'> $newFromLocationName ($searchFrompincode)</span> - <span class='to-head'> $newToLocationName ($searchTopincode)</span></h2>";
                                    }elseif($searched_delivery_type == IS_INTERNATIONAL){
                                        $row->cells [4]->value.="<h2 class='sub-head'><span class='from-head'> $newFromLocationName ($searchFrompincode)</span> - <span class='to-head'> $searchTopincode</span></h2>";
                                    }
                                    
                                }elseif($serviceId == OCEAN || $serviceId == AIR_INTERNATIONAL){
                $row->cells [4]->value.="<h2 class='sub-head'><span class='from-head'> $newFromLocationName</span> - <span class='to-head'> $newToLocationName</span></h2>";
                                }else{
                $row->cells [4]->value.="<h2 class='sub-head'><span class='from-head'> $newFromLocationName ($searchFrompincode)</span> - <span class='to-head'> $newToLocationName ($searchTopincode)</span></h2>";
                                }     
                                $row->cells [4]->value.="<div class='table-div table-style1'>
                                        <div class='table-heading inner-block-bg'>";
                                         if($serviceId != COURIER){ 
                                           $row->cells [4]->value.= "<div class='col-md-2 padding-left-none'>Load type</div>";
                                       }
                                       if($serviceId != COURIER || ($serviceId == COURIER && $searched_courier_type == IS_PARCEL)){ 
                                           $row->cells [4]->value.= "<div class='col-md-1 padding-left-none'>Volume</div>";
                                       }
                                            $row->cells [4]->value.="<div class='col-md-2 padding-left-none'>Unit Weight</div>
                                            <div class='col-md-2 padding-left-none'>No. of Packages</div>";
                                            if($serviceId == COURIER){  
                                           $row->cells [4]->value.= "<div class='col-md-2 padding-left-none'>Package Price</div>";
                                        }
                                          if($serviceId != COURIER){  
                                           $row->cells [4]->value.= "<div class='col-md-1 padding-left-none'>Kg per $service_wt</div>
                                            <div class='col-md-2 padding-left-none'>Chargable Weight</div>
                                            <div class='col-md-1 padding-left-none'>Rate/KG</div>
                                            <!--div class='col-md-2 padding-left-none'>Chargable Amount</div-->";
                                        }
                                        $row->cells [4]->value.= "</div>
                                        <div class='table-data'>";

                                
                                //$price_from   =   $ptlBuyerSessionSearch['price_from'];
                                //$price_to =   $ptlBuyerSessionSearch['price_to'];                             
                                
                                //print_r($new_array); echo $fromLocationId; exit;
                                foreach ($new_array as $ptlSessionLineitems) {
                                  // echo "<pre>"; print_r($ptlSessionLineitems); echo $fromLocationId;                                    
                                   if($ptlSessionLineitems['ptlFromLocation']==$fromLocationId){
                                        if($serviceId != COURIER){
                                            $loadTypeName = $ptlSessionLineitems['ptlLoadType'];
                                            $loadType = $ptlSessionLineitems['ptlLoadType'];
                                            $packageType = $ptlSessionLineitems['ptlPackageType'];
                                            $ptlweightType = $ptlSessionLineitems['ptlCheckVolWeight'];
                                            $ptlLength = $ptlSessionLineitems['ptlLength'];
                                            $ptlWidth = $ptlSessionLineitems['ptlWidth'];
                                            $ptlHeight = $ptlSessionLineitems['ptlHeight'];
                                        }
                                        if($serviceId == COURIER){
                                            $ptlweightType = $ptlSessionLineitems['ptlCheckVolWeightCourier'];
                                            $ptlLength = $ptlSessionLineitems['ptlLengthCourier'];
                                            $ptlWidth = $ptlSessionLineitems['ptlWidthCourier'];
                                            $ptlHeight = $ptlSessionLineitems['ptlHeightCourier'];
                                            $packageValue = $ptlSessionLineitems['packeagevalue'];
                                            $courier_type = $ptlSessionLineitems['courier_types'];
                                        }
                                        $noOfPackages = $ptlSessionLineitems['ptlNopackages'];
                                        $ptlUnitsWeight = $ptlSessionLineitems['ptlUnitsWeight'];                                       
                                        //for hidden items
                                        $dispatch = $ptlSessionLineitems['ptlDispatchDate'];
                                        $delivery = $ptlSessionLineitems['ptlDeliveryhDate'];
                                        $from = $ptlSessionLineitems['ptlFromLocation'];
                                        $to = $ptlSessionLineitems['ptlToLocation'];                                        
                                        $volume = $ptlSessionLineitems['ptlDisplayVolumeWeight'];
                                        $flexible_dispatch = $ptlSessionLineitems['ptlFlexiableDispatch'];
                                                                                if($serviceId!=AIR_INTERNATIONAL && $serviceId!=OCEAN && $serviceId!=COURIER){
                                        $door_pick = $ptlSessionLineitems['ptlDoorpickup'];
                                                                                $door_delivery = $ptlSessionLineitems['ptlDoorDelivery'];
                                                                                }else{
                                                                                    $door_pick ="";$door_delivery ="";
                                                                                }
                                                                                if($serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN){
                                        $ptlShipmentType = $ptlSessionLineitems['ptlShipmentType'];
                                                                                $ptlIECode = $ptlSessionLineitems['ptlIECode'];
                                                                                $ptlSenderIdentity = $ptlSessionLineitems['ptlSenderIdentity'];
                                                                                $ptlProductMade = $ptlSessionLineitems['ptlProductMade'];
                                                                                }else{
                                                                                    $ptlShipmentType ="";$ptlIECode ="";$ptlSenderIdentity="";$ptlProductMade="";
                                                                                }
                                        $flexible_delivery = $ptlSessionLineitems['ptlFlexiableDelivery'];
                                        
                                        $ptlcheckweightType = $ptlSessionLineitems['ptlCheckUnitWeight'];
                                        //convert weight type to KGS.but in showing line items it will show only unitweight
                                        //calcuation time only it will convert kgs and calcualtions
                                        if ($ptlcheckweightType == 1) {
                                            $ptlConvertunitweight = $ptlUnitsWeight;
                                            $ptlConvertDisplaytype = 'Kgs';
                                        } else if($ptlcheckweightType == 2) {
                                            $ptlConvertunitweight = ($ptlUnitsWeight*0.001);
                                            $ptlConvertDisplaytype = 'Gms';
                                        } else if($ptlcheckweightType == 3) {
                                            $ptlConvertunitweight = ($ptlUnitsWeight*1000);
                                            $ptlConvertDisplaytype = 'Mts';
                                        }                                       
                                        
                                        $res=    PtlBuyerComponent::getVolumeWeight($ptlweightType,$ptlLength,$ptlWidth,$ptlHeight);
                                        $vol=    $res['vol'];
                                        $displayVolumeWeight=    $res['displayVolumeWeight'];
                                        if($serviceId != COURIER){
                                            $chargableWeight = ($displayVolumeWeight *  $kgforCft * $noOfPackages);
                                            $chargeunitWeight = ($ptlConvertunitweight*$noOfPackages);
                                            if($chargableWeight > $chargeunitWeight) {
                                                $displayChargableweighttotal = $chargableWeight;
                                            } else {
                                                $displayChargableweighttotal = $chargeunitWeight;
                                            }
                                            //Check and ADD ODA amount calculation.                                         
                                            $checkOda = CommonComponent::buyerODACheck($toLocationId,$serviceId,$sellerId);
                                            if($checkOda == 0) {
                                                $odaPrice=0;
                                            } else {
                                                $odaPrice = $odacharges;
                                            }
                                                
                                            $totalChargableAmount = ($displayChargableweighttotal*$price); 
                                            $tot    +=$totalChargableAmount+$odaPrice;
                                        }else if($serviceId == COURIER){

                                            $seller_post_slab_values  = DB::table('courier_seller_posts')
                                            ->join ( 'courier_seller_post_items', 'courier_seller_post_items.seller_post_id', '=', 'courier_seller_posts.id' )
                                            ->join ( 'courier_seller_post_item_slabs', 'courier_seller_post_item_slabs.seller_post_id', '=', 'courier_seller_posts.id' )
                                            ->where('courier_seller_post_items.id',$sellerPostId)
                                            ->select('courier_seller_post_item_slabs.*')
                                            ->get();
                                            
                                            $conversion_factor = $kgforCft;
                                            
                                            if($courier_type == IS_PARCEL){
                                                $chargableWeight = ($ptlLength*$ptlWidth*$ptlHeight)/$conversion_factor;
                                                if($chargableWeight > $ptlUnitsWeight){
                                                    $displayChargableweighttotal = $chargableWeight;
                                                }else{
                                                    $displayChargableweighttotal = $ptlUnitsWeight;
                                                }
                                            }else{
                                             $displayChargableweighttotal =  $ptlUnitsWeight;
                                            }

                                            $total_slab_amount = 0;
                                            for($m=0;$m<count($seller_post_slab_values);$m++){
                                                $minVal = $seller_post_slab_values[$m]->slab_min_rate;
                                                $maxVal = $seller_post_slab_values[$m]->slab_max_rate;
                                                $total_slab_amount = $total_slab_amount + $seller_post_slab_values[$m]->price;
                                                if($displayChargableweighttotal >= $minVal && $displayChargableweighttotal <= $maxVal){
                                                    break;
                                                }

                                            }           

                                            if($displayChargableweighttotal > $max_weight_accepted){
                                                $balance_weight = $max_weight_accepted - $displayChargableweighttotal;
                                                if($is_incremental == 1){
                                                    $weight_inc = $balance_weight/$incremental_weight;
                                                    $additonal_rate = $weight_inc * $rate_per_increment;
                                                    $total_slab_amount = $total_slab_amount + $additonal_rate;
                                                }

                                            }
                                            $totalChargableAmount = ($total_slab_amount*$noOfPackages); 
                                            $fuelsurchargeCalVal = ($fuelsurcharge * $totalChargableAmount)/100;
                                            $codchargeVal = ($codcharge * $noOfPackages * $packageValue ) /100;
                                            $arcchargeVal = ($arccharge * $noOfPackages * $packageValue ) /100;
                                            
                                            $tot    +=$totalChargableAmount + $fuelsurchargeCalVal + $codchargeVal + $arcchargeVal;
                                            if($paymentmodeid == CASH_ON_DELIVERY){
                                                $tot    += $freightcollectcharge;
                                            }
                                        }                               
                                        $row->cells [4]->value.=    "
                                        <div class='table-row inner-block-bg'>";

                                        if($serviceId != COURIER){
                                         $row->cells [4]->value.= "<div class='col-md-2 padding-left-none'>".CommonComponent::getLoadType($loadTypeName)."</div>";
                                        }
                                        if($serviceId != COURIER || ($serviceId == COURIER && $searched_courier_type == IS_PARCEL)){ 
                                           $row->cells [4]->value.= "<div class='col-md-1 padding-left-none'>$vol</div>";
                                       }
                                         $row->cells [4]->value.= "<div class='col-md-2 padding-left-none'>$ptlUnitsWeight $ptlConvertDisplaytype</div>
                                            <div class='col-md-2 padding-left-none'>".CommonComponent::number_format($noOfPackages, false)."</div>";
                                            if($serviceId == COURIER){
                                                $row->cells [4]->value.= " <div class='col-md-2 padding-left-none'>".CommonComponent::number_format($packageValue, false)."</div>";
                                            }
                                            if($serviceId != COURIER){
                                            $row->cells [4]->value.= "<div class='col-md-1 padding-left-none'>$kgforCft</div>
                                            <div class='col-md-2 padding-left-none'>".CommonComponent::moneyFormat($displayChargableweighttotal)."</div>
                                            <div class='col-md-1 padding-left-none'>".CommonComponent::getPriceType($price)."</div>
                                            <!--div class='col-md-2 padding-left-none'>".CommonComponent::moneyFormat($totalChargableAmount)."</div-->";
                                            }
                                        $row->cells [4]->value.= "</div>";

//                                        if($serviceId != COURIER){
//                                        $row->cells [4]->value.="<input id='search_ptl_buyer_load_id_$sellerPostId' name='search_ptl_buyer_load_id_$sellerPostId' type='hidden' value='$loadType'>
//                                        <input id='search_ptl_buyer_pack_id_$sellerPostId' name='search_ptl_buyer_pack_id_$sellerPostId' type='hidden' value='$packageType'>";
//                                        }
//                                        $row->cells [4]->value.="<input id='search_ptl_buyer_volume_$sellerPostId' name='search_ptl_buyer_volume_$sellerPostId' type='hidden' value='$volume' class='disable'>
//                                        <input id='search_ptl_buyer_fdispatch_$sellerPostId' name='search_ptl_buyer_fdispatch_$sellerPostId' type='hidden' value='$flexible_dispatch' class='disable'>
//                                        <input id='search_ptl_buyer_doorpick_$sellerPostId' name='search_ptl_buyer_doorpick_$sellerPostId' type='hidden' value='$door_pick' class='disable'>
//                                        <input id='search_ptl_buyer_fdelivery_$sellerPostId' name='search_ptl_buyer_fdelivery_$sellerPostId' type='hidden' value='$flexible_delivery' class='disable'>
//                                        <input id='search_ptl_buyer_doordelivery_$sellerPostId' name='search_ptl_buyer_doordelivery_$sellerPostId' type='hidden' value='$door_delivery' class='disable'>
//                                        <input id='search_ptl_buyer_weight_type_$sellerPostId' name='search_ptl_buyer_weight_type_$sellerPostId' type='hidden' value='$ptlcheckweightType' class='disable'>
//                                        <input id='search_ptl_buyer_no_pack_$sellerPostId' name='search_ptl_buyer_no_pack_$sellerPostId' type='hidden' value='$noOfPackages' class='disable'>
//                                        <input id='search_ptl_buyer_unit_weight_$sellerPostId' name='search_ptl_buyer_unit_weight_$sellerPostId' type='hidden' value='$ptlUnitsWeight' class='disable'>
//                                        <input id='search_ptl_buyer_vol_type_$sellerPostId' name='search_ptl_buyer_vol_type_$sellerPostId' type='hidden' value='$ptlweightType' class='disable'>
//                                        <input id='search_ptl_buyer_length_$sellerPostId' name='search_ptl_buyer_length_$sellerPostId' type='hidden' value='$ptlLength' class='disable'>
//                                        <input id='search_ptl_buyer_width_$sellerPostId' name='search_ptl_buyer_width_$sellerPostId' type='hidden' value='$ptlWidth' class='disable'>
//                                        <input id='search_ptl_buyer_height_$sellerPostId' name='search_ptl_buyer_height_$sellerPostId' type='hidden' value='$ptlHeight' class='disable'>
//                                        <input id='search_ptl_buyer_shipment_type_$sellerPostId' name='search_ptl_buyer_shipment_type_$sellerPostId' type='hidden' value='$ptlShipmentType' class='disable'>
//                                        <input id='search_ptl_buyer_iecode_$sellerPostId' name='search_ptl_buyer_iecode_$sellerPostId' type='hidden' value='$ptlIECode' class='disable'>
//                                        <input id='search_ptl_buyer_sender_identity_$sellerPostId' name='search_ptl_buyer_sender_identity_$sellerPostId' type='hidden' value='$ptlSenderIdentity' class='disable'>
//                                        <input id='search_ptl_buyer_product_made_$sellerPostId' name='search_ptl_buyer_product_made_$sellerPostId' type='hidden' value='$ptlProductMade' class='disable'>";
                                    }
                                    }   
                                   
                                    $dispatch_order = isset($ptlSessionLineitems['ptlDispatchDate']) ? $ptlSessionLineitems['ptlDispatchDate'] : "";
                                    $delivery_order = isset($ptlSessionLineitems['ptlDeliveryhDate']) ? $ptlSessionLineitems['ptlDeliveryhDate'] : "";
                                    Session::put('session_dispatch_buyer',$dispatch_order);
                                    Session::put('session_delivery_buyer',$delivery_order);
                                    $row->cells [4]->value.="           </div> <!-- table data -->
                                                                        </div> <!-- table-div -->
                                                                        <input id='search_ptl_buyer_from_id_$sellerPostId' name='search_ptl_buyer_from_id_$sellerPostId' type='hidden' value='$from'>
                                                                        <input id='search_ptl_buyer_to_id_$sellerPostId' name='search_ptl_buyer_to_id_$sellerPostId' type='hidden' value='$to'>
                                                                        <input id='search_ptl_buyer_dispatch_$sellerPostId' name='search_ptl_buyer_dispatch_$sellerPostId' type='hidden' value='$dispatch'>
                                                                        <input id='search_ptl_buyer_delivery_$sellerPostId' name='search_ptl_buyer_delivery_$sellerPostId' type='hidden' value='$delivery'>
                                                                        <input id='search_ptl_buyer_volume_$sellerPostId' name='search_ptl_buyer_volume_$sellerPostId' type='hidden' value='$volume' class='disable'>
                                                                        <input id='search_ptl_buyer_fdispatch_$sellerPostId' name='search_ptl_buyer_fdispatch_$sellerPostId' type='hidden' value='$flexible_dispatch' class='disable'>
                                                                        <input id='search_ptl_buyer_doorpick_$sellerPostId' name='search_ptl_buyer_doorpick_$sellerPostId' type='hidden' value='$door_pick' class='disable'>
                                                                        <input id='search_ptl_buyer_fdelivery_$sellerPostId' name='search_ptl_buyer_fdelivery_$sellerPostId' type='hidden' value='$flexible_delivery' class='disable'>
                                                                        <input id='search_ptl_buyer_doordelivery_$sellerPostId' name='search_ptl_buyer_doordelivery_$sellerPostId' type='hidden' value='$door_delivery' class='disable'>
                                                                        <input id='search_ptl_buyer_weight_type_$sellerPostId' name='search_ptl_buyer_weight_type_$sellerPostId' type='hidden' value='$ptlcheckweightType' class='disable'>
                                                                        <input id='search_ptl_buyer_no_pack_$sellerPostId' name='search_ptl_buyer_no_pack_$sellerPostId' type='hidden' value='$noOfPackages' class='disable'>
                                                                        <input id='search_ptl_buyer_unit_weight_$sellerPostId' name='search_ptl_buyer_unit_weight_$sellerPostId' type='hidden' value='$ptlUnitsWeight' class='disable'>
                                                                        <input id='search_ptl_buyer_vol_type_$sellerPostId' name='search_ptl_buyer_vol_type_$sellerPostId' type='hidden' value='$ptlweightType' class='disable'>
                                                                        <input id='search_ptl_buyer_length_$sellerPostId' name='search_ptl_buyer_length_$sellerPostId' type='hidden' value='$ptlLength' class='disable'>
                                                                        <input id='search_ptl_buyer_width_$sellerPostId' name='search_ptl_buyer_width_$sellerPostId' type='hidden' value='$ptlWidth' class='disable'>
                                                                        <input id='search_ptl_buyer_height_$sellerPostId' name='search_ptl_buyer_height_$sellerPostId' type='hidden' value='$ptlHeight' class='disable'>";
                                    if($serviceId != COURIER){
                                        $row->cells [4]->value.="<input id='search_ptl_buyer_load_id_$sellerPostId' name='search_ptl_buyer_load_id_$sellerPostId' type='hidden' value='$loadType'>
                                        <input id='search_ptl_buyer_pack_id_$sellerPostId' name='search_ptl_buyer_pack_id_$sellerPostId' type='hidden' value='$packageType'>";
                                        }
                                    if($serviceId != ROAD_PTL && $serviceId != RAIL && $serviceId != AIR_DOMESTIC && $serviceId != COURIER){                                    
                                    $row->cells [4]->value.= "<input id='search_ptl_buyer_shipment_type_$sellerPostId' name='search_ptl_buyer_shipment_type_$sellerPostId' type='hidden' value='$ptlShipmentType' class='disable'>
                                                                        <input id='search_ptl_buyer_iecode_$sellerPostId' name='search_ptl_buyer_iecode_$sellerPostId' type='hidden' value='$ptlIECode' class='disable'>
                                                                        <input id='search_ptl_buyer_sender_identity_$sellerPostId' name='search_ptl_buyer_sender_identity_$sellerPostId' type='hidden' value='$ptlSenderIdentity' class='disable'>
                                                                        <input id='search_ptl_buyer_product_made_$sellerPostId' name='search_ptl_buyer_product_made_$sellerPostId' type='hidden' value='$ptlProductMade' class='disable'>";
                                            }
                                    $row->cells [4]->value.=     "<input id='search_ptl_buyer_dispatchs_$sellerPostId' name='search_ptl_buyer_dispatchs_$sellerPostId' type='hidden' value='".CommonComponent::convertDateForDatabase($dispatch_order)."'>
                                                                        <input id='search_ptl_buyer_deliverys_$sellerPostId' name='search_ptl_buyer_deliverys_$sellerPostId' type='hidden' value='".CommonComponent::convertDateForDatabase($delivery_order)."'>
                                                                        <input class='form-control' id='search_ptl_buyer_post_buyer_id_$sellerPostId' name='search_ptl_buyer_post_buyer_id_$sellerPostId' type='hidden' value='$buyerId'>
                                                                        <input class='form-control' id='search_ptl_buyer_post_seller_id_$sellerPostId' name='search_ptl_buyer_post_seller_id_$sellerPostId' type='hidden' value='$sellerId'>
                                                                        <div class='col-md-12 col-sm-12 col-xs-12 padding-none margin-top pull-left details-slide-drop ptl_buyer_listdetails_$sellerPostId' style='display:none'></div>
                                                                        <div class='hidden-md hidden-lg hidden-sm hidden-xs' id='tot_price_$sellerPostId'>".CommonComponent::moneyFormat($tot)."</div>
                                                                        <div class='hidden-md hidden-lg hidden-sm hidden-xs' id='tot_price_new_$sellerPostId'>$tot</div>

                                                                        <div class='hidden-md hidden-lg hidden-sm hidden-xs' id='base_freight_price_$sellerPostId'>".CommonComponent::moneyFormat($totalChargableAmount)."</div>";
                                     if($serviceId == COURIER){
                                     	
                                     	$row->cells [4]->value.="                                   
                                     							 <div class='hidden-md hidden-lg hidden-sm hidden-xs' id='fuel_surcharge_price_$sellerPostId'>".CommonComponent::moneyFormat($fuelsurchargeCalVal)."</div>
                                                                 <div class='hidden-md hidden-lg hidden-sm hidden-xs' id='cod_price_$sellerPostId'>".CommonComponent::moneyFormat($codchargeVal)."</div>
                                                                 <div class='hidden-md hidden-lg hidden-sm hidden-xs' id='arc_price_$sellerPostId'>".CommonComponent::moneyFormat($arcchargeVal)."</div>
                                                                 <div class='hidden-md hidden-lg hidden-sm hidden-xs' id='freight_collect_$sellerPostId'>".CommonComponent::moneyFormat($freightcollectcharge)."</div>
                                                                 <div class='hidden-md hidden-lg hidden-sm hidden-xs' id='totalcourier_$sellerPostId'>".CommonComponent::moneyFormat($tot)."</div>";
                                     }
																		
                                     $row->cells [4]->value.=" </div> <!-- show data div --> 
                                                                        </div></form>";
            $row->attributes(array("class" => ""));
        } );
             
         return view('ptl.buyers.buyer_search_results',
                 array('gridBuyer' => $grid,
                        'gridFilter'=>$Query_buyers_for_sellers,    
                        'fromDate'=>$fromDate,
                        'toDate'=>$toDate,                          
                ));          
        } catch ( Exception $exc ) {
        }
    }
    
    
    /**
     * Buyer counter offer Page
     * Method to retrieve city name from the id
     *
     * @param int $locationId
     * @return array
     */
    public static function getCityNameForPtl($locationId) {
        try {
            Log::info('Get city name: ' . Auth::id(), array(
                    'c' => '2'
            ));
                        $serviceId = Session::get('service_id');

            switch($serviceId){
                            
                        case ROAD_PTL:
                        case RAIL:
                        case AIR_DOMESTIC:
                        case COURIER:
                            $getLocationQuery = DB::table('lkp_ptl_pincodes as ltp');
                            if (!empty($locationId)) {
                                    $getLocationQuery->where('ltp.id', $locationId);
                            }
                            $getLocationQuery->select('ltp.id', 'ltp.postoffice_name');
                            $arrayLocation = $getLocationQuery->get();
                            $no_city = 'No city name';
                            if (count($arrayLocation) > 0)
                                    return $arrayLocation [0]->postoffice_name;
                            else
                                    return $no_city;
                            break;
                            case COURIER:
                                $getLocationQuery = DB::table('lkp_ptl_pincodes as ltp');
                                if (!empty($locationId)) {
                                    $getLocationQuery->where('ltp.id', $locationId);
                                }
                                $getLocationQuery->select('ltp.id', 'ltp.postoffice_name');
                                $arrayLocation = $getLocationQuery->get();
                                $no_city = 'No city name';
                                if (count($arrayLocation) > 0)
                                    return $arrayLocation [0]->postoffice_name;
                                else
                                    return $no_city;
                                break;
                        case AIR_INTERNATIONAL: 
                            $getLocationQuery = DB::table('lkp_airports as ltp');
                            if (!empty($locationId)) {
                                    $getLocationQuery->where('ltp.id', $locationId);
                            }
                            $getLocationQuery->select('ltp.id', 'ltp.airport_name');
                            $arrayLocation = $getLocationQuery->get();
                            $no_city = 'No airport name';
                            if (count($arrayLocation) > 0)
                                    return $arrayLocation [0]->airport_name;
                            else
                                    return $no_city;
                        break;
                        case OCEAN:
                            $getLocationQuery = DB::table('lkp_seaports as ltp');
                            if (!empty($locationId)) {
                                    $getLocationQuery->where('ltp.id', $locationId);
                            }
                            $getLocationQuery->select('ltp.id', 'ltp.seaport_name');
                            $arrayLocation = $getLocationQuery->get();
                            $no_city = 'No port name';
                            if (count($arrayLocation) > 0)
                                    return $arrayLocation [0]->seaport_name;
                            else
                                    return $no_city;
                        break;
                        }
                            
                        
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }
        
        /******* Below Script for get calculate ************** */
   public static function getVolumeWeight($ptlweightType='',$ptlLength='',$ptlWidth='',$ptlHeight='')
   {
    try{   
        
                $serviceId = Session::get('service_id');
                switch($serviceId){
                    case ROAD_PTL:
                    case RAIL : 
                    if($ptlweightType==FEET) {
                            $displayVolumeWeight=($ptlLength*$ptlWidth*$ptlHeight);
                    } else if($ptlweightType==INCHES) {
                            $lengthToInches=$ptlLength*0.0833;
                            $widthhToInches=$ptlWidth*0.0833;
                            $heightToInches=$ptlHeight*0.0833;
                            $displayVolumeWeight=$lengthToInches*$widthhToInches*$heightToInches;               
                    } else if($ptlweightType==METER) {
                            $lengthToMeters=$ptlLength*3.2808;
                            $widthhToMeters=$ptlWidth*3.2808;
                            $heightToMeters=$ptlHeight*3.2808;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    }
                    else if($ptlweightType==CENTIMETER) {
                            $lengthToMeters=$ptlLength*0.0328;
                            $widthhToMeters=$ptlWidth*0.0328;
                            $heightToMeters=$ptlHeight*0.0328;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    }
                    $vol=round($displayVolumeWeight,4)." CFT";
                    break;
                    case AIR_DOMESTIC:
                    case AIR_INTERNATIONAL     : 
                    if($ptlweightType==FEET) {
                            $lengthToMeters=$ptlLength*30.4800;
                            $widthhToMeters=$ptlWidth*30.4800;
                            $heightToMeters=$ptlHeight*30.4800;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    } else if($ptlweightType==INCHES) {
                            $lengthToInches=$ptlLength*2.54;
                            $widthhToInches=$ptlWidth*2.54;
                            $heightToInches=$ptlHeight*2.54;
                            $displayVolumeWeight=$lengthToInches*$widthhToInches*$heightToInches;               
                    } else if($ptlweightType==METER) {
                            $lengthToMeters=$ptlLength*100.0000;
                            $widthhToMeters=$ptlWidth*100.0000;
                            $heightToMeters=$ptlHeight*100.0000;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    }
                    else if($ptlweightType==CENTIMETER) {
                            $displayVolumeWeight=($ptlLength*$ptlWidth*$ptlHeight);
                    }
                    $vol=round($displayVolumeWeight,4)." CCM";
                    break;
                    case OCEAN       : 
                    if($ptlweightType==FEET) {
                            $lengthToMeters=$ptlLength*0.3048;
                            $widthhToMeters=$ptlWidth*0.3048;
                            $heightToMeters=$ptlHeight*0.3048;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    } else if($ptlweightType==INCHES) {
                            $lengthToInches=$ptlLength*0.0254;
                            $widthhToInches=$ptlWidth*0.0254;
                            $heightToInches=$ptlHeight*0.0254;
                            $displayVolumeWeight=$lengthToInches*$widthhToInches*$heightToInches;               
                    } else if($ptlweightType==METER) {
                            $displayVolumeWeight=($ptlLength*$ptlWidth*$ptlHeight);
                    }
                    else if($ptlweightType==CENTIMETER) {
                            $lengthToMeters=$ptlLength*0.0100;
                            $widthhToMeters=$ptlWidth*0.0100;
                            $heightToMeters=$ptlHeight*0.0100;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    }
                    $vol=round($displayVolumeWeight,4)." CBM";
                    break;
                    case COURIER       :
                        $displayVolumeWeight=0;
                        if($ptlweightType==FEET) {
                            $lengthToMeters=$ptlLength*30.4800;
                            $widthhToMeters=$ptlWidth*30.4800;
                            $heightToMeters=$ptlHeight*30.4800;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    } else if($ptlweightType==INCHES) {
                            $lengthToInches=$ptlLength*2.54;
                            $widthhToInches=$ptlWidth*2.54;
                            $heightToInches=$ptlHeight*2.54;
                            $displayVolumeWeight=$lengthToInches*$widthhToInches*$heightToInches;               
                    } else if($ptlweightType==METER) {
                            $lengthToMeters=$ptlLength*100.0000;
                            $widthhToMeters=$ptlWidth*100.0000;
                            $heightToMeters=$ptlHeight*100.0000;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    }
                    else if($ptlweightType==CENTIMETER) {
                            $displayVolumeWeight=($ptlLength*$ptlWidth*$ptlHeight);
                    }
                    $vol=round($displayVolumeWeight,4)." CCM";
                        break;
                }$res=array();
                $res['vol']=$vol;
                $res['displayVolumeWeight']=$displayVolumeWeight;
        return $res;
        die();
    }catch (Exception $e) {     
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    
   }
   
   /**
    * Change status of seller post item
    * @param type $sellerPostItemId
    * @param type $status
    */
    public static function changeStatusForSellerPostItem($sellerPostTableName, $sellerPostItemId, $status)
    {
        try{
            $updatedAt = date ( 'Y-m-d H:i:s' );
            $updatedIp = $_SERVER['REMOTE_ADDR'];
            DB::table($sellerPostTableName)
                        ->where($sellerPostTableName.'.id','=',$sellerPostItemId)
                        ->update(array(
                                'lkp_post_status_id'=> $status,
                                'updated_ip'=> $updatedAt,
                                'updated_at'=> $updatedIp,
                                'updated_by'=> Auth::id()
                                ));
            CommonComponent::auditLog($sellerPostItemId,'seller_post_items');
        } catch (Exception $e) {

        }
    }
    
    /**
     * Change status of seller post item
     * @param type $sellerPostItemId
     * @param type $status
     */
    public static function getPostDeliveryType($buyerQutoteItemId,$serviceId)
    {
        //echo $buyerQutoteItemId;exit;
        try{
            $lkp_courier_delivery_type_id = DB::table('courier_buyer_quote_items as ltp');
            $lkp_courier_delivery_type_id->where('ltp.buyer_quote_id', $buyerQutoteItemId);
            $lkp_courier_delivery_type_id->select('ltp.lkp_courier_delivery_type_id');
            $lkp_courier_delivery_type = $lkp_courier_delivery_type_id->get();
            
            if($lkp_courier_delivery_type[0]->lkp_courier_delivery_type_id == 1){
                $courier_delivery ='Domestic';
                return $courier_delivery;
                
            }else{
                $courier_delivery ='International';
                return $courier_delivery;
            }
            
        } catch (Exception $e) {
    
        }
    }
    
    
    public static function getPostCourierType($buyerQutoteItemId,$serviceId)
    {
        try{
            $lkp_courier_delivery_type_id = DB::table('courier_buyer_quote_items as ltp');
            $lkp_courier_delivery_type_id->where('ltp.buyer_quote_id', $buyerQutoteItemId);
            $lkp_courier_delivery_type_id->select('ltp.lkp_courier_type_id');
            $lkp_courier_delivery_type = $lkp_courier_delivery_type_id->get();
    
            if($lkp_courier_delivery_type[0]->lkp_courier_type_id == 1){
                $courier_delivery ='Document';
                return $courier_delivery;
    
            }else{
                $courier_delivery ='Parcel';
                return $courier_delivery;
            }
    
        } catch (Exception $e) {
    
        }
    }
    
    
    /**
     * Buyer PTL and other services market Leads
     * srinu started here - 2-04-2016.
     * @param type $sellerPostItemId
     * @param type $status
     */
    
    public static function getPtlBuyerMarketLeadsList(){
    	// Filters values to populate in the page
    	$ptlFromLocationPincode = array (
    			"" => "From Location-Pincode"
    	);
    	if(Session::get('delivery_type') == 1){
    	$ptlToLocationPincode = array ("" => "To Location-Pincode");
    	 }else{
    	$ptlToLocationPincode = array ("" => "To Location-Country");
    	}	
    	$ptlCourierTypes = array ("" => "Courier Type");
    	// query to retrieve seller posts list and bind it to the grid
    	$serviceId = Session::get ( 'service_id' );
    	switch ($serviceId) {    	
    		case ROAD_PTL:
		    	$Query = DB::table ( 'ptl_seller_posts as psp' );
		    	$Query->leftjoin ( 'ptl_seller_post_items as pspi', 'pspi.seller_post_id', '=', 'psp.id' );
		    	$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'psp.lkp_post_status_id' );
		    	$Query->leftjoin ( 'ptl_seller_sellected_buyers as ssb', 'psp.id', '=', 'ssb.seller_post_id' );
		    	$Query->join ( 'lkp_ptl_pincodes as cf', 'pspi.from_location_id', '=', 'cf.id' );
		    	$Query->join ( 'lkp_ptl_pincodes as ct', 'pspi.to_location_id', '=', 'ct.id' );
		    	$Query->join ( 'users as us', 'psp.seller_id', '=', 'us.id' );
		    	$Query->where( 'ssb.buyer_id', Auth::User ()->id);
		    	$Query->where( 'psp.lkp_access_id', 2);
		    	$Query->where('psp.lkp_post_status_id',2);
                        $Query->where('pspi.is_private', 0);
		    	$sellerresults = $Query->select ( 'psp.id', 'psp.from_date','psp.to_date','psp.lkp_access_id',
		    									'psp.lkp_post_status_id','ct.pincode as toCity', 'cf.pincode as fromCity',
		    									'us.username','psp.terms_conditions','psp.tracking','psp.lkp_payment_mode_id','pspi.transitdays',
												'pspi.units','psp.lkp_post_status_id','ps.post_status', 'pspi.id as sellerpostItemId')
		    	->groupBy('psp.id')
		    	->get ();
		    	//Functionality to handle filters based on the selection starts
		    	foreach($sellerresults as $seller){
		    		$seller_post_items  = DB::table('ptl_seller_post_items')
		    		->join ( 'ptl_seller_posts', 'ptl_seller_posts.id', '=', 'ptl_seller_post_items.seller_post_id' )
		    		->where('ptl_seller_post_items.seller_post_id',$seller->id)
		    		->where ( 'ptl_seller_posts.lkp_ptl_post_type_id', 2 )
		    		->select('*')
		    		->get();
		    		foreach($seller_post_items as $seller_post_item){
		    			if (!isset( $ptlFromLocationPincode [$seller_post_item->from_location_id] )) {
		    				$ptlFromLocationPincode [$seller_post_item->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )
		    				->where ( 'id', $seller_post_item->from_location_id )
		    				->pluck ( 'pincode' );
		    			}
		    			if (!isset( $ptlToLocationPincode [$seller_post_item->to_location_id] )) {
		    				$ptlToLocationPincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )
		    				->where ( 'id', $seller_post_item->to_location_id )
		    				->pluck ( 'pincode' );
		    			}
		    		}
		    	}
    		break;
    		case RAIL:
    			$Query = DB::table ( 'rail_seller_posts as psp' );
    			$Query->leftjoin ( 'rail_seller_post_items as pspi', 'pspi.seller_post_id', '=', 'psp.id' );
    			$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'psp.lkp_post_status_id' );
    			$Query->leftjoin ( 'rail_seller_sellected_buyers as ssb', 'psp.id', '=', 'ssb.seller_post_id' );
    			$Query->join ( 'lkp_ptl_pincodes as cf', 'pspi.from_location_id', '=', 'cf.id' );
    			$Query->join ( 'lkp_ptl_pincodes as ct', 'pspi.to_location_id', '=', 'ct.id' );
    			$Query->join ( 'users as us', 'psp.seller_id', '=', 'us.id' );
    			$Query->where( 'ssb.buyer_id', Auth::User ()->id);
    			$Query->where( 'psp.lkp_access_id', 2);
		    	$Query->where('psp.lkp_post_status_id',2);
                        $Query->where('pspi.is_private', 0);
    			$sellerresults = $Query->select ( 'psp.id', 'psp.from_date','psp.to_date','psp.lkp_access_id',
    					'psp.lkp_post_status_id','ps.post_status','ct.pincode as toCity', 'cf.pincode as fromCity',
    					'us.username','psp.terms_conditions','psp.tracking','psp.lkp_payment_mode_id','pspi.transitdays',
    					'pspi.units','pspi.id as sellerpostItemId')
    			->groupBy('psp.id')
    			->get ();    			
    			foreach($sellerresults as $seller){
    				$seller_post_items  = DB::table('rail_seller_post_items')
    				->join ( 'rail_seller_posts', 'rail_seller_posts.id', '=', 'rail_seller_post_items.seller_post_id' )
    				->where('rail_seller_post_items.seller_post_id',$seller->id)
    				->where ( 'rail_seller_posts.lkp_ptl_post_type_id', 2 )
    				->select('*')
    				->get();
    				foreach($seller_post_items as $seller_post_item){
    					if (!isset( $ptlFromLocationPincode [$seller_post_item->from_location_id] )) {
    						$ptlFromLocationPincode [$seller_post_item->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )
    						->where ( 'id', $seller_post_item->from_location_id )
    						->pluck ( 'pincode' );
    					}
    					if (!isset( $ptlToLocationPincode [$seller_post_item->to_location_id] )) {
    						$ptlToLocationPincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )
    						->where ( 'id', $seller_post_item->to_location_id )
    						->pluck ( 'pincode' );
    					}
    				}
    			}
    		break;
    		case AIR_DOMESTIC:
    			$Query = DB::table ( 'airdom_seller_posts as psp' );
    			$Query->leftjoin ( 'airdom_seller_post_items as pspi', 'pspi.seller_post_id', '=', 'psp.id' );
    			$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'psp.lkp_post_status_id' );
    			$Query->leftjoin ( 'airdom_seller_sellected_buyers as ssb', 'psp.id', '=', 'ssb.seller_post_id' );
    			$Query->join ( 'lkp_ptl_pincodes as cf', 'pspi.from_location_id', '=', 'cf.id' );
    			$Query->join ( 'lkp_ptl_pincodes as ct', 'pspi.to_location_id', '=', 'ct.id' );
    			$Query->join ( 'users as us', 'psp.seller_id', '=', 'us.id' );
    			$Query->where( 'ssb.buyer_id', Auth::User ()->id);
    			$Query->where( 'psp.lkp_access_id', 2);
    			$Query->where('psp.lkp_post_status_id',2);
                        $Query->where('pspi.is_private', 0);
    			$sellerresults = $Query->select ( 'psp.id', 'psp.from_date','psp.to_date','psp.lkp_access_id',
    					'psp.lkp_post_status_id','ps.post_status','ct.pincode as toCity', 'cf.pincode as fromCity',
    					'us.username','psp.terms_conditions','psp.tracking','psp.lkp_payment_mode_id','pspi.transitdays',
    					'pspi.units','pspi.id as sellerpostItemId')
    					->groupBy('psp.id')
    					->get ();
    			foreach($sellerresults as $seller){
    				$seller_post_items  = DB::table('airdom_seller_post_items')
    				->join ( 'airdom_seller_posts', 'airdom_seller_posts.id', '=', 'airdom_seller_post_items.seller_post_id' )
    				->where('airdom_seller_post_items.seller_post_id',$seller->id)
    				->where ( 'airdom_seller_posts.lkp_ptl_post_type_id', 2 )
    				->select('*')
    				->get();
    				foreach($seller_post_items as $seller_post_item){
    					if (!isset( $ptlFromLocationPincode [$seller_post_item->from_location_id] )) {
    						$ptlFromLocationPincode [$seller_post_item->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )
    						->where ( 'id', $seller_post_item->from_location_id )
    						->pluck ( 'pincode' );
    					}
    					if (!isset( $ptlToLocationPincode [$seller_post_item->to_location_id] )) {
    						$ptlToLocationPincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )
    						->where ( 'id', $seller_post_item->to_location_id )
    						->pluck ( 'pincode' );
    					}
    				}
    			}
    		break;
    		case AIR_INTERNATIONAL:
    			$Query = DB::table ( 'airint_seller_posts as psp' );
    			$Query->leftjoin ( 'airint_seller_post_items as pspi', 'pspi.seller_post_id', '=', 'psp.id' );
    			$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'psp.lkp_post_status_id' );
    			$Query->leftjoin ( 'airint_seller_sellected_buyers as ssb', 'psp.id', '=', 'ssb.seller_post_id' );
    			$Query->join ( 'lkp_airports as cf', 'pspi.from_location_id', '=', 'cf.id' );
    			$Query->join ( 'lkp_airports as ct', 'pspi.to_location_id', '=', 'ct.id' );
    			$Query->join ( 'users as us', 'psp.seller_id', '=', 'us.id' );
    			$Query->where( 'ssb.buyer_id', Auth::User ()->id);
    			$Query->where( 'psp.lkp_access_id', 2);
    			$Query->where('psp.lkp_post_status_id',2);
                        $Query->where('pspi.is_private', 0);
    			$sellerresults = $Query->select ( 'psp.id', 'psp.from_date','psp.to_date','psp.lkp_access_id',
    					'psp.lkp_post_status_id','ps.post_status','ct.airport_name as toCity', 'cf.airport_name as fromCity',
    					'us.username','psp.terms_conditions','psp.tracking','psp.lkp_payment_mode_id','pspi.transitdays',
    					'pspi.units','pspi.id as sellerpostItemId')
    					->groupBy('psp.id')
    					->get ();
    			foreach($sellerresults as $seller){
    				$seller_post_items  = DB::table('airint_seller_post_items')
    				->join ( 'airint_seller_posts', 'airint_seller_posts.id', '=', 'airint_seller_post_items.seller_post_id' )
    				->where('airint_seller_post_items.seller_post_id',$seller->id)
    				->where ( 'airint_seller_posts.lkp_ptl_post_type_id', 2 )
    				->select('*')
    				->get();
    				foreach($seller_post_items as $seller_post_item){
    					if (!isset( $ptlFromLocationPincode [$seller_post_item->from_location_id] )) {
    						$ptlFromLocationPincode [$seller_post_item->from_location_id] = DB::table ( 'lkp_airports' )
    						->where ( 'id', $seller_post_item->from_location_id )
    						->pluck ( 'airport_name' );
    					}
    					if (!isset( $ptlToLocationPincode [$seller_post_item->to_location_id] )) {
    						$ptlToLocationPincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_airports' )
    						->where ( 'id', $seller_post_item->to_location_id )
    						->pluck ( 'airport_name' );
    					}
    				}
    			}
    		break;
    		case OCEAN:
    			$Query = DB::table ( 'ocean_seller_posts as psp' );
				$Query->leftjoin ( 'ocean_seller_post_items as pspi', 'pspi.seller_post_id', '=', 'psp.id' );
    			$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'psp.lkp_post_status_id' );
    			$Query->leftjoin ( 'ocean_seller_sellected_buyers as ssb', 'psp.id', '=', 'ssb.seller_post_id' );
    			$Query->join ( 'lkp_seaports as cf', 'pspi.from_location_id', '=', 'cf.id' );
    			$Query->join ( 'lkp_seaports as ct', 'pspi.to_location_id', '=', 'ct.id' );
    			$Query->join ( 'users as us', 'psp.seller_id', '=', 'us.id' );
    			$Query->where( 'ssb.buyer_id', Auth::User ()->id);
    			$Query->where( 'psp.lkp_access_id', 2);
    			$Query->where('psp.lkp_post_status_id',2);
                        $Query->where('pspi.is_private', 0);
    			$sellerresults = $Query->select ( 'psp.id', 'psp.from_date','psp.to_date','psp.lkp_access_id',
    					'psp.lkp_post_status_id','ps.post_status','ct.seaport_name as toCity', 'cf.seaport_name as fromCity',
    					'us.username','psp.terms_conditions','psp.tracking','psp.lkp_payment_mode_id','pspi.transitdays',
    					'pspi.units','pspi.id as sellerpostItemId')
    					->groupBy('psp.id')
    					->get ();
    			foreach($sellerresults as $seller){
    				$seller_post_items  = DB::table('ocean_seller_post_items')
    				->join ( 'ocean_seller_posts', 'ocean_seller_posts.id', '=', 'ocean_seller_post_items.seller_post_id' )
    				->where('ocean_seller_post_items.seller_post_id',$seller->id)
    				->where ( 'ocean_seller_posts.lkp_ptl_post_type_id', 2 )
    				->select('*')
    				->get();
    				foreach($seller_post_items as $seller_post_item){
    					if (!isset( $ptlFromLocationPincode [$seller_post_item->from_location_id] )) {
    						$ptlFromLocationPincode [$seller_post_item->from_location_id] = DB::table ( 'lkp_seaports' )
    						->where ( 'id', $seller_post_item->from_location_id )
    						->pluck ( 'seaport_name' );
    					}
    					if (!isset( $ptlToLocationPincode [$seller_post_item->to_location_id] )) {
    						$ptlToLocationPincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_seaports' )
    						->where ( 'id', $seller_post_item->to_location_id )
    						->pluck ( 'seaport_name' );
    					}
    				}
    			}
    		break;
    		case COURIER:
    			$delivery_date = Session::get('delivery_type');
    			$Query = DB::table ( 'courier_seller_posts as psp' );
				$Query->leftjoin ( 'courier_seller_post_items as pspi', 'pspi.seller_post_id', '=', 'psp.id' );
    			$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'psp.lkp_post_status_id' );
    			$Query->leftjoin ( 'courier_seller_sellected_buyers as ssb', 'psp.id', '=', 'ssb.seller_post_id' );
    			$Query->join ( 'lkp_ptl_pincodes as cf', 'pspi.from_location_id', '=', 'cf.id' );
    			if(Session::get('delivery_type') != 2){
    			$Query->join ( 'lkp_ptl_pincodes as ct', 'pspi.to_location_id', '=', 'ct.id' );
    			}else{
    			$Query->join ( 'lkp_countries as ct', 'pspi.to_location_id', '=', 'ct.id' );
    			}
    			$Query->join ( 'users as us', 'psp.seller_id', '=', 'us.id' );
    			$Query->where( 'ssb.buyer_id', Auth::User ()->id);
    			
    			if (isset ( $delivery_date ) && !empty($delivery_date)) {
    				$Query->where ( 'psp.lkp_courier_delivery_type_id', '=', Session::get('delivery_type') );
    			}
    			$Query->where( 'psp.lkp_access_id', 2);
    			$Query->where('psp.lkp_post_status_id',2);
                        $Query->where('pspi.is_private', 0);
                if(Session::get('delivery_type') != 2){
    			$sellerresults = $Query->select ( 'psp.id', 'psp.from_date','psp.to_date','psp.lkp_access_id',
    					'psp.lkp_post_status_id','ps.post_status','ct.pincode as toCity', 'cf.pincode as fromCity',
    					'us.username','psp.terms_conditions','psp.tracking','psp.lkp_payment_mode_id','pspi.transitdays',
    					'pspi.units','psp.lkp_courier_type_id','pspi.id as sellerpostItemId')
    					->groupBy('psp.id')
    					->get ();
                }else{
    			$sellerresults = $Query->select ( 'psp.id', 'psp.from_date','psp.to_date','psp.lkp_access_id',
    					'psp.lkp_post_status_id','ps.post_status','ct.country_name as toCity', 'cf.pincode as fromCity',
    					'us.username','psp.terms_conditions','psp.tracking','psp.lkp_payment_mode_id','pspi.transitdays',
    					'pspi.units','psp.lkp_courier_type_id','pspi.id as sellerpostItemId')
    					->groupBy('psp.id')
    					->get ();
                }
    			//echo "<pre>"; print_r($sellerresults); die;
    			foreach($sellerresults as $seller){
    				$seller_post_items  = DB::table('courier_seller_post_items')
    				->join ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
    				->where('courier_seller_post_items.seller_post_id',$seller->id)
    				->where ( 'courier_seller_posts.lkp_ptl_post_type_id', 2 )
    				->select('*')
    				->get();
    				foreach($seller_post_items as $seller_post_item){
			    				if (!isset( $ptlFromLocationPincode [$seller_post_item->from_location_id] )) {
								$ptlFromLocationPincode [$seller_post_item->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $seller_post_item->from_location_id )->pluck ( 'pincode' );
							}
							if (!isset( $ptlToLocationPincode [$seller_post_item->to_location_id] )) {
								
								if(Session::get('delivery_type') == 2){
									//echo $seller_post_item->to_location_id;exit;
									$ptlToLocationPincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_countries' )->where ( 'id', $seller_post_item->to_location_id )->pluck ( 'country_name' );
								}else{
									$ptlToLocationPincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $seller_post_item->to_location_id )->pluck ( 'pincode' );
								}
							}
							if(Session::get ( 'service_id' )  == COURIER){
							if (!isset( $ptlCourierTypes [$seller->lkp_courier_type_id] )) {
								$ptlCourierTypes [$seller->lkp_courier_type_id] = DB::table ( 'lkp_courier_types' )->where ( 'id', $seller->lkp_courier_type_id )->pluck ( 'courier_type' );
							}
						}						
    				}
    			}
    			//print_r($ptlCourierTypes);exit;
    			break;    			
    	}
        $ptlToLocationPincode = CommonComponent::orderArray($ptlToLocationPincode);
        $ptlToLocationPincode = CommonComponent::orderArray($ptlToLocationPincode);
        $ptlCourierTypes = CommonComponent::orderArray($ptlCourierTypes);
    	//echo "<pre>"; print_r($sellerresults); die;
    	//Functionality to handle filters based on the selection ends
    	$grid = DataGrid::source ( $Query );
    	$grid->add ( 'id', 'ID', true )->style ( "display:none" );
    	$grid->add ( 'username', 'Seller Name', 'username' )->attributes(array("class" => "col-md-2 padding-left-none"));
    	$grid->add ( 'fromCity', 'From Location', 'fromCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
    	$grid->add ( 'toCity', 'To Location', 'toCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
    	$grid->add ( 'from_date', 'Valid From', 'post_status' )->attributes(array("class" => "col-md-2 padding-left-none"));
    	$grid->add ( 'to_date', 'Valid To', 'post_status' )->attributes(array("class" => "col-md-2 padding-left-none"));
    	$grid->add ( 'post_status', 'Status', 'post_status' )->attributes(array("class" => "col-md-1 padding-left-none"));
    	$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
    	$grid->add ( 'tracking', 'Tracking', 'tracking' )->style ( "display:none" );
    	$grid->add ( 'terms_conditions', 'Tracking', 'terms_conditions' )->style ( "display:none" );
    	$grid->add ( 'lkp_payment_mode_id', 'Payment Method', 'lkp_payment_mode_id' )->style ( "display:none" );
    	$grid->add ( 'transitdays', 'Transit Days', 'transitdays' )->style ( "display:none" );
    	$grid->add ( 'units', 'Units', 'units' )->style ( "display:none" );
        $grid->add ( 'sellerpostItemId', 'Seller Post Item Id', 'sellerpostItemId' )->style ( "display:none" );
    	$grid->orderBy ( 'id', 'desc' );
    	$grid->paginate ( 5 );
    	
    	$grid->row ( function ($row) {
    			
    		$row->cells [0]->style ( 'display:none' );
    		$row->cells [1]->style ( 'display:none' );
    		$row->cells [2]->style ( 'display:none' );
    		$row->cells [3]->style ( 'display:none' );
    		$row->cells [4]->style ( 'display:none' );
    		$row->cells [5]->style ( 'display:none' );
    		$row->cells [6]->style ( 'display:none' );
    		$row->cells [8]->style ( 'display:none' );
    		$row->cells [9]->style ( 'display:none' );
    		$row->cells [10]->style ( 'display:none' );
    		$row->cells [11]->style ( 'display:none' );
    		$row->cells [12]->style ( 'display:none' );
                $row->cells [13]->style ( 'display:none' );
    			
    		$spId = $row->cells [0]->value;
    		$sellerName=$row->cells [1]->value;
    		$fromLocation=$row->cells [2]->value;
    		$toLocation=$row->cells [3]->value;
    		$fromDate=$row->cells [4]->value;
    		$toDate=$row->cells [5]->value;
    		$postStatus=$row->cells [6]->value;
    		$tracking=$row->cells [8]->value;
    		$termandconditions=$row->cells [9]->value;
    		$paymentMethod=$row->cells [10]->value;
                $sellerpostItemId=$row->cells [13]->value;

    		$seller_post_items  = DB::table('seller_post_items')
    		->join('seller_posts','seller_posts.id','=','seller_post_items.seller_post_id')
    		->where('seller_post_items.seller_post_id',$spId)
    		->select('*','seller_post_items.id as spiid')
    		->get();    			
    		//echo "<pre>"; print_r($seller_post_items); die;
    		//Get Payment method type    	
    		$seller_payment_mode_method = CommonComponent::getSellerPostPaymentMethod($paymentMethod);
          $tracking_seller_post = CommonComponent::getTrackingType($tracking);
    		
                
                if ($seller_payment_mode_method == 'Advance') {
                        $paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
                } elseif ($seller_payment_mode_method == 'Credit'){
                        $serviceId = Session::get ( 'service_id' );
                        if($serviceId == ROAD_PTL) {
                            $credit_days = CommonComponent::getCreditdays($sellerpostItemId,'ptl_seller_posts','ptl_seller_post_items');
                        } elseif($serviceId == RAIL) {
                            $credit_days = CommonComponent::getCreditdays($sellerpostItemId,'rail_seller_posts','rail_seller_post_items');
                        } elseif($serviceId == AIR_DOMESTIC) {
                            $credit_days = CommonComponent::getCreditdays($sellerpostItemId,'airdom_seller_posts','airdom_seller_post_items');
                        } elseif($serviceId == AIR_INTERNATIONAL) {
                            $credit_days = CommonComponent::getCreditdays($sellerpostItemId,'airint_seller_posts','airint_seller_post_items');
                        } elseif($serviceId == OCEAN) {
                            $credit_days = CommonComponent::getCreditdays($sellerpostItemId,'ocean_seller_posts','ocean_seller_post_items');
                        } elseif($serviceId == COURIER) {
                            $credit_days = CommonComponent::getCreditdays($sellerpostItemId,'courier_seller_posts','courier_seller_post_items');
                        }
                        
                        $paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$seller_payment_mode_method.' | '.$credit_days[0]->credit_period.' '.$credit_days[0]->credit_period_units;
                }else {
                        $paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$seller_payment_mode_method;
                }
    		
    		$data_link = url()."/buyermarketleads/$spId";
    			
    		//$msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$spId);
    		$msg_count=0;
                $row->cells [7]->value .= "<a href=".$data_link."><div class='table-row '>
    		<div class='col-md-2 padding-left-none'>
    		$sellerName
    		<div class='red'>
    		<i class='fa fa-star'></i>
    		<i class='fa fa-star'></i>
    		<i class='fa fa-star'></i>
    		</div>
    		</div>
    		<div class='col-md-2 padding-left-none'>$fromLocation</div>
    		<div class='col-md-2 padding-left-none'>$toLocation</div>
    		<div class='col-md-2 padding-none'>".CommonComponent::checkAndGetDate($fromDate)."</div>
			<div class='col-md-2 padding-none'>".CommonComponent::checkAndGetDate($toDate)."</div>
    				<div class='col-md-1 padding-none'>$postStatus</div>
    				<div class='col-md-1 padding-none text-right' style='display:none'> 
    				<button class='btn red-btn pull-right'>Book Now</button>
    				</div>
    				</a>
    					
    				<div class='clearfix'></div>
    					 
    				<div class='pull-left'>
    				<div class='info-links'>
    				<a href='$data_link'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>$msg_count</span></a>
    				</div>
    				</div>
    				<div class='pull-right text-right'>
    				<div class='info-links'>
    				<a id='".$spId."' class='show-data-link'><span class='show-icon spot_transaction_details_list'>+</span><span class='hide-icon'>-</span> Details</a>
		
			</div>
			</div>
		
			<div style='display:none' class='col-md-12 show-data-div padding-top'>
			<div class='col-md-8  '>
			<div class='col-md-3 padding-left-none data-fld'>
			<span class='data-head'>Valid From</span>
			<span class='data-value'>".CommonComponent::checkAndGetDate($fromDate)."</span>
			</div>
			<div class='col-md-3 padding-left-none data-fld'>
			<span class='data-head'>Valid To</span>
			<span class='data-value'>".CommonComponent::checkAndGetDate($toDate)."</span>
    				</div>
    					
    				<div class='col-md-3 padding-left-none data-fld'>
    				<span class='data-head'>Payment</span>
    				<span class='data-value'>$paymentType</span>
    				</div>
    				<div class='col-md-3 padding-left-none data-fld'>
    				<span class='data-head'>Tracking</span>
    				<span class='data-value'><i class='fa fa-signal'></i>&nbsp;$tracking_seller_post</span>
    				</div>
    					
    				<div class='clearfix'></div>
    					
    				<div class='col-md-3 padding-left-none data-fld'>
    				<span class='data-head'>Document</span>
    				<span class='data-value'>0</span>
    				</div>";
    				if($termandconditions!="")	{			
					$row->cells [7]->value .= "	<div class='col-md-3 padding-left-none data-fld'>
					<span class='data-head'>Terms &amp; Conditions</span>
					<span class='data-value'>$termandconditions</span>
					</div>";
					}	
		    $row->cells [7]->value .= "	</div>
									    </div>
									    </div> ";    	
		    } );
    	
    		$filter = DataFilter::source ( $Query );
    		$filter->add ( 'pspi.from_location_id', '', 'select' )->options ( $ptlFromLocationPincode )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
    		$filter->add ( 'pspi.to_location_id', '', 'select' )->options ( $ptlToLocationPincode )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
    		if(Session::get ( 'service_id' )  == COURIER){
    		$filter->add ( 'psp.lkp_courier_type_id', 'Courier Type', 'select' )->options ( $ptlCourierTypes )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
    		}
    		$filter->submit ( 'search' );
    	    $filter->reset ( 'reset' );
    	    $filter->build ();
    	    			
    	    $result = array ();
    		$result ['grid'] = $grid;
    		$result ['filter'] = $filter;
    		return $result;
    }
    
}