<?php

namespace App\Components;

use DB;
use App\Models\BuyerQuoteItemView;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use Auth;
use App\Http\Requests;
use Input;
use Config;
use File;
use Session;
use Redirect;
use Log;
use App\Components\SellerComponent;
use App\Components\Ptl\PtlBuyerComponent;
use App\Models\User;
use App\Models\BuyerQuoteSellersQuotesPrices;
use App\Models\CartItem;
use App\BuyerQuoteItems;
use App\Models\FtlSearchTerm;
use App\Models\IctSearchTerm;
use Maatwebsite\Excel\Facades\Excel;

class BuyerComponent {

    /**
     * Buyer counter offer Page
     * Method to retrieve buyer quote requests data
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function updateBuyerQuoteDetailsViews($buyerQuoteItemId) {
        try {
            Log::info('Get update buyer quote details view: ' . Auth::id(), array('c' => '2'));

           $countview = DB::table('buyer_quote_item_views as bqiv')
//                    ->where('bqiv.created_by', '=', Auth::user()->id)
                    ->where('bqiv.buyer_quote_item_id', '=', $buyerQuoteItemId)
                    ->select('bqiv.id', 'bqiv.view_counts')
                    ->get();
            if (!isset($countview[0]->view_counts)) {
                $countview = 0;
            } else {
                $countview = $countview[0]->view_counts;
            }
            return $countview;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }
    
    /**
     * Buyer counter offer Page
     * Method to retrieve buyer quote requests data
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function updateBuyerQuoteDetailsViewsTL($buyerQuoteItemId) {
    	try {
    		Log::info('Get update buyer quote details view: ' . Auth::id(), array('c' => '2'));
    
    		$countview = DB::table('trucklease_buyer_quote_item_views as bqiv')
    		->where('bqiv.buyer_quote_item_id', '=', $buyerQuoteItemId)
    		->select('bqiv.id', 'bqiv.view_counts')
    		->get();
    		if (!isset($countview[0]->view_counts)) {
    			$countview = 0;
    		} else {
    			$countview = $countview[0]->view_counts;
    		}
    		return $countview;
    	} catch (Exception $exc) {
    		// echo $exc->getTraceAsString();
    		// TODO:: Log the error somewhere
    	}
    }
    

    /**
    * Retrieval of IView Count
    * @param type $sellerId
    * @param type $buyerQuoteItemId
    * @return type
    */
	public static function viewCountForBuyerForFtl($sellerId,$buyerQuoteItemId) {
		try {
			$getviewcount = DB::table('buyer_quote_item_views as bqiv')
				->where('bqiv.buyer_quote_item_id','=',$buyerQuoteItemId)
				->select('bqiv.id','bqiv.view_counts')
				->get();
			if(isset($getviewcount[0]->id) && !empty($getviewcount[0]->id)) {
				DB::table('buyer_quote_item_views as bqiv')
					->where('bqiv.buyer_quote_item_id','=',$buyerQuoteItemId)
					->update(array(
						'view_counts' =>$getviewcount[0]->view_counts+1
                    ));
			} else {
				$created_at  = date ( 'Y-m-d H:i:s' );
				$createdIp = $_SERVER['REMOTE_ADDR'];
				$viewcount = new BuyerQuoteItemView();
				$viewcount->user_id = $sellerId;
				$viewcount->buyer_quote_item_id = $buyerQuoteItemId;
				$viewcount->view_counts = 1;
				$viewcount->created_at = $created_at;
				$viewcount->created_ip = $createdIp;
				$viewcount->created_by = $sellerId;
				$viewcount->save();
			}
			if(!empty($getviewcount[0]->view_counts)) {
				 $count = $getviewcount[0]->view_counts;
			}
			else {
				$count = '';
			}
			return $count;
		} catch(Exception $e) {
			//return $e->message;
		}
	}

    /**
     * Buyer counter offer Page
     * Method to retrieve buyer quote requests data
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getBuyerQuoteDetailsFromId($buyerQuoteItemId) {
        try {
            Log::info('Get buyer quote requests data: ' . Auth::id(), array(
                'c' => '2'
            ));
            $getPostBuyerCounterOfferQuery = DB::table('buyer_quote_items as bqi');
            $getPostBuyerCounterOfferQuery->leftjoin('buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');
            $getPostBuyerCounterOfferQuery->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'bqi.lkp_load_type_id');
            $getPostBuyerCounterOfferQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
            $getPostBuyerCounterOfferQuery->leftjoin('lkp_quote_price_types as lqpt', 'lqpt.id', '=', 'bqi.lkp_quote_price_type_id');
            $getPostBuyerCounterOfferQuery->leftjoin('lkp_quote_accesses as lqa', 'lqa.id', '=', 'bq.lkp_quote_access_id');
            if (!empty($buyerQuoteItemId)) {
                $getPostBuyerCounterOfferQuery->where('bqi.id', $buyerQuoteItemId);
            }
            $getPostBuyerCounterOfferQuery->select('bqi.lkp_load_type_id','bqi.id', 'bqi.dispatch_date', 'bqi.is_cancelled', 'bqi.lkp_post_status_id', 'bqi.is_dispatch_flexible', 'bqi.is_delivery_flexible', 'bqi.delivery_date', 'ldt.load_type', 'bqi.buyer_quote_id', 'bqi.from_city_id', 'bqi.to_city_id', 'bq.lkp_quote_access_id', 'lqa.quote_access', 'lvt.vehicle_type', 'lqpt.price_type', 'bq.transaction_id','bqi.quantity','bqi.number_loads','bqi.units','bq.is_commercial');
            $arrayBuyerCounterOffer = $getPostBuyerCounterOfferQuery->get();
            //echo "<pre>"; print_r($arrayBuyerCounterOffer); die;
            return $arrayBuyerCounterOffer;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }
    
    /**
    * Buyer counter offer Page
    * Method to retrieve private seller name
    * @param int $buyerQuoteItemId
    * @return array
    */
	public static function getPrivateSellerNames($buyerQuoteItemId) {
		try {
			Log::info ( 'Get private seller names: ' . Auth::id (), array (
                        'c' => '2'
                    ));
            $getPostBuyerCounterOfferQuery = DB::table('buyer_quote_items as bqi');
            $getPostBuyerCounterOfferQuery->leftjoin('buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');
            $getPostBuyerCounterOfferQuery->leftjoin('buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'bq.id');
			$getPostBuyerCounterOfferQuery->leftjoin('users as seller_names', 'seller_names.id', '=', 'pbqss.seller_id');
			if (!empty($buyerQuoteItemId)) {
                $getPostBuyerCounterOfferQuery->where('bqi.id', $buyerQuoteItemId);
            }
            $getPostBuyerCounterOfferQuery->select ('seller_names.username');
            $arrayBuyerCounterOffer = $getPostBuyerCounterOfferQuery->get ();
			return $arrayBuyerCounterOffer;
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}
        
        
         /**
    * Buyer counter offer Page
    * Method to retrieve private seller name
    * @param int $buyerQuoteItemId
    * @return array
    */
	public static function getTermAllPrivateSellerNames($buyerQuoteItemId,$serviceId) {
		try {
			Log::info ( 'Get Term private seller names: ' . Auth::id (), array (
                        'c' => '2'
                    ));
            $getPostBuyerCounterOfferQuery = DB::table('term_buyer_quote_items as bqi');
            $getPostBuyerCounterOfferQuery->leftjoin('term_buyer_quotes as bq', 'bq.id', '=', 'bqi.term_buyer_quote_id');
            $getPostBuyerCounterOfferQuery->leftjoin('term_buyer_quote_selected_sellers as pbqss', 'pbqss.term_buyer_quote_id', '=', 'bq.id');
            $getPostBuyerCounterOfferQuery->leftjoin('users as seller_names', 'seller_names.id', '=', 'pbqss.seller_id');
			if (!empty($buyerQuoteItemId)) {
                $getPostBuyerCounterOfferQuery->where('bqi.term_buyer_quote_id', $buyerQuoteItemId);
                $getPostBuyerCounterOfferQuery->where('pbqss.lkp_service_id', $serviceId);
            }
            $getPostBuyerCounterOfferQuery->select ('seller_names.username','seller_names.id');
            $arrayBuyerCounterOffer = $getPostBuyerCounterOfferQuery->groupBy('username')->get ();
			return $arrayBuyerCounterOffer;
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}
    
    

    /**
     * Buyer counter offer Page
     * Method to retrieve seller lists
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getBuyerQuoteSellersQuotesPricesFromId($buyerQuoteItemId, $comparisonType = null,$priceVal = null,$checkIds = null) {
        try {
            Log::info('Get seller lists for the buyer: ' . Auth::id(), array('c' => '2'));
            (object)$arrayBuyerQuoteSellersNotQuotesPrices="";
            $getBuyerQuoteSellersQuotesPricesQuery = DB::table('buyer_quote_sellers_quotes_prices as bqsqp');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'bqi.lkp_load_type_id');
            if (!empty($buyerQuoteItemId)) {
                $getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.buyer_quote_item_id', $buyerQuoteItemId);
            }
            $getBuyerQuoteSellersQuotesPricesQuery->whereRaw('(`bqsqp`.`initial_quote_price` is not NULL and `bqsqp`.`initial_quote_price` != "" or `bqsqp`.`seller_acceptence` = "1")');
            // $getBuyerQuoteSellersQuotesPricesQuery->whereRaw('bqsqp.initial_quote_price is not NULL');
            // $getBuyerQuoteSellersQuotesPricesQuery->whereRaw('bqsqp.initial_quote_price != "" or bqsqp.seller_acceptence = "1"');
            $getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.buyer_id', Auth::User()->id);
            //$rownum = 0;
            
            if (!empty($comparisonType)) {
            	
            	
            	
            	if($checkIds){

            		$checkIds= explode(",",$checkIds);
            		$getBuyerQuoteSellersQuotesPricesQuery->whereIn('bqsqp.id', $checkIds);
            		
            		$getBuyerQuoteSellersNotQuotesPricesQuery = DB::table('buyer_quote_sellers_quotes_prices as bqsqp');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'bqi.lkp_load_type_id');
            		
            		if (!empty($buyerQuoteItemId)) {
            			$getBuyerQuoteSellersNotQuotesPricesQuery->where('bqsqp.buyer_quote_item_id', $buyerQuoteItemId);
            		}
            		$getBuyerQuoteSellersNotQuotesPricesQuery->whereRaw('(`bqsqp`.`initial_quote_price` is not NULL and `bqsqp`.`initial_quote_price` != "" or `bqsqp`.`seller_acceptence` = "1")');
            		// $getBuyerQuoteSellersQuotesPricesQuery->whereRaw('bqsqp.initial_quote_price is not NULL');
            		// $getBuyerQuoteSellersQuotesPricesQuery->whereRaw('bqsqp.initial_quote_price != "" or bqsqp.seller_acceptence = "1"');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->where('bqsqp.buyer_id', Auth::User()->id);
            		
            		$getBuyerQuoteSellersNotQuotesPricesQuery->whereNotIn('bqsqp.id', $checkIds);
            		
            		$getBuyerQuoteSellersNotQuotesPricesQuery->select('bqsqp.private_seller_quote_id','sp.transaction_id as transaction_no','bqsqp.id', 'bqsqp.buyer_id', 'bqsqp.initial_quote_price', 'bqsqp.counter_quote_price', 'bqsqp.final_quote_price','bqsqp.initial_transit_days','bqsqp.final_transit_days', 'bqsqp.counter_transit_days', 'bqsqp.final_transit_days', 'u.username', 'ldt.load_type', 'bqi.price', 'bqsqp.seller_id', 'bqsqp.buyer_quote_item_id', 'bqsqp.seller_post_item_id', 'spi.transitdays', 'spi.units', 'bqsqp.firm_price', 'bqsqp.seller_acceptence', 'bq.lkp_quote_access_id', 'spi.seller_post_id', 'sp.from_date', 'sp.to_date', 'lvt.vehicle_type', 'bqi.lkp_post_status_id', 'bqi.quantity');
            		if ($comparisonType == '1') {
            			
            			
            				if($priceVal=="initial_transit_days"){
            					$transit='bqsqp.initial_transit_days';
            				}
            				if($priceVal=="final_transit_days"){
            					$transit='bqsqp.final_transit_days';
            				}
            				
            			$getBuyerQuoteSellersNotQuotesPricesQuery->orderBy($transit, 'asc');
            				
            			
            		
            		} elseif ($comparisonType == '2') {
            			$price="";
            			
            			if($priceVal=="final_quote_price"){
            				$price='bqsqp.final_quote_price';
            			}
            			if($priceVal=="firm_price"){
            				$price='bqsqp.firm_price';
            			}
            			if($priceVal=="counter_quote_price"){
            				$price='bqsqp.counter_quote_price';
            			}
            			if($priceVal=="initial_quote_price"){
            				$price='bqsqp.initial_quote_price';
            			}
            		
            			$getBuyerQuoteSellersNotQuotesPricesQuery->orderBy($price, 'asc');
            		}
            		
            		$arrayBuyerQuoteSellersNotQuotesPrices = $getBuyerQuoteSellersNotQuotesPricesQuery->get();


            		
            		$ni=0;
            		foreach ($arrayBuyerQuoteSellersNotQuotesPrices as $arrayBuyerQuoteSellersNotQuotesPrice) {
            			
            			$arrayBuyerQuoteSellersNotQuotesPrice->rank='-';
            			 
            			$ni++;
            			
            		}
            		
            		
            	 }
            	
                if ($comparisonType == '1') {
                	
                	if($priceVal=="initial_transit_days"){
                		$transit='bqsqp.initial_transit_days';
                	}
                	if($priceVal=="final_transit_days"){
                		$transit='bqsqp.final_transit_days';
                	}
                	
                    $getBuyerQuoteSellersQuotesPricesQuery->orderBy($transit, 'asc');
                    
                 } elseif ($comparisonType == '2') {
                 	$price="";
                 	//echo $priceVal;
            		
                 	if($priceVal=="final_quote_price"){
                 	  $price='bqsqp.final_quote_price';
                 	 // $checkprice=''
                 	}
                 	if($priceVal=="firm_price"){
                 		$price='bqsqp.firm_price';
                 	}
                 	if($priceVal=="counter_quote_price"){
                 		$price='bqsqp.counter_quote_price';
                 	}
                 	if($priceVal=="initial_quote_price"){
                 		$price='bqsqp.initial_quote_price';
                 	}
                 	//echo $price;exit;
                    $getBuyerQuoteSellersQuotesPricesQuery->orderBy($price, 'asc');
                }
            }
            $getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.private_seller_quote_id','sp.transaction_id as transaction_no','bqsqp.id', 'bqsqp.buyer_id', 'bqsqp.initial_quote_price', 'bqsqp.counter_quote_price', 'bqsqp.final_quote_price', 'bqsqp.initial_transit_days', 'bqsqp.final_transit_days','bqsqp.counter_transit_days', 'bqsqp.final_transit_days', 'u.username', 'ldt.load_type', 'bqi.price', 'bqsqp.seller_id', 'bqsqp.buyer_quote_item_id', 'bqsqp.seller_post_item_id', 'spi.transitdays', 'spi.units', 'bqsqp.firm_price', 'bqsqp.seller_acceptence', 'bq.lkp_quote_access_id', 'spi.seller_post_id', 'sp.from_date', 'sp.to_date', 'lvt.vehicle_type', 'bqi.lkp_post_status_id', 'bqi.quantity');
          
            
            $arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
            if(!empty($comparisonType)){
           
            $j=0;
            $k=1;
            $p=1;
            for ($i=0;$i<count($arrayBuyerQuoteSellersQuotesPrices);$i++) {
            	if($i==0){
            	 $j=1;
            	}
            	if($j>count($arrayBuyerQuoteSellersQuotesPrices)-1){
            		$j=count($arrayBuyerQuoteSellersQuotesPrices)-1;
            	}
            	if($comparisonType == '1'){
            		if($arrayBuyerQuoteSellersQuotesPrices[$i]->$priceVal !=$arrayBuyerQuoteSellersQuotesPrices[$j]->$priceVal ){
            			
            		      if($k<=3){
							$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="L".$k;
							} else{
							$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="-";
							}
            			$k++;
            		}else{
            		if($k<=3){
							$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="L".$k;
							} else{
							$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="-";
							}
            		}	
            	}
            	if($comparisonType == '2'){
            	if($arrayBuyerQuoteSellersQuotesPrices[$i]->$priceVal!=$arrayBuyerQuoteSellersQuotesPrices[$j]->$priceVal){
            		
            	if($p<=3){
						$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="L".$p;
						} else{
						$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="-";
						}
            	$p++;
                }else{
                if($p<=3){
                	$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="L".$p;
                	}else{
                		$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="-";
                	}		
                }
            	}
            	$j++;
            
          
            
            }
            }
            if(!empty($comparisonType) && !empty($checkIds)){
            $obj_merged = (array) array_merge((array) $arrayBuyerQuoteSellersQuotesPrices, (array) $arrayBuyerQuoteSellersNotQuotesPrices);
            return $obj_merged;
            }else{
           
            return $arrayBuyerQuoteSellersQuotesPrices;
            }
        } catch (Exception $exc) {
            
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Buyer counter offer Page
     * Method to retrieve seller lists
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getLeadsForBuyer($districtId, $sellerIds) {
        try {
            Log::info('Get leads for the buyer: ' . Auth::id(), array('c' => '2'));
            $sellerData = DB::table('seller_post_items')
                    ->join('users', 'seller_post_items.created_by', '=', 'users.id')
                    ->leftjoin('sellers', 'users.id', '=', 'sellers.user_id')
                    ->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
                    ->distinct('seller_post_items.created_by')
                    ->where('seller_post_items.lkp_district_id', $districtId)
                    ->whereNotIn('sellers.id', $sellerIds)
                    ->where('users.lkp_role_id', SELLER)
                    ->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
                    ->get();
            
            return $sellerData;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Buyer counter offer Page
     * Method to retrieve seller lists
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getSellerIds($buyerQuoteId) {
        try {
            Log::info('Get seller lists for the district: ' . Auth::id(), array('c' => '2'));
            $sellerIds = DB::table('buyer_quote_selected_sellers as bqss')
                    ->where('bqss.buyer_quote_id', $buyerQuoteId)
                    ->select('bqss.seller_id')
                    ->get();
            $arraySellerIds = [];
            if (isset($sellerIds) && !empty($sellerIds)) {
                foreach ($sellerIds as $sellerId) {
                    array_push($arraySellerIds, $sellerId->seller_id);
                }
            }
            return $arraySellerIds;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Buyer counter offer Page
     * Method to retrieve email ids of buyers
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getBuyerEnquirySellers($buyerQuoteItemId) {
        try {
            Log::info('Get seller lists for the buyer: ' . Auth::id(), array(
                'c' => '2'
            ));
            $getBuyerQuoteSellersQuotesPricesQuery = DB::table('buyer_quote_sellers_quotes_prices as bqsqp');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('users as usr', 'usr.id', '=', 'bqsqp.buyer_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_load_types as llt', 'llt.id', '=', 'bqi.lkp_load_type_id');
            $getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.buyer_quote_item_id', $buyerQuoteItemId);
            $getBuyerQuoteSellersQuotesPricesQuery->whereRaw('(`bqsqp`.`initial_quote_price` is not NULL and `bqsqp`.`initial_quote_price` != "" or `bqsqp`.`seller_acceptence` = "1")');
            $getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id as bqsqpid', 'u.id as userId', 'u.email', 'u.username as sellerName', 'usr.username as buyerName', 'llt.load_type', 'bq.transaction_id', 'bqi.from_city_id', 'bqi.to_city_id', 'bqi.dispatch_date', 'bqi.delivery_date', 'lvt.vehicle_type');
            $arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
            return $arrayBuyerQuoteSellersQuotesPrices;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Buyer counter offer Page
     * Method to get count of cart items
     *
     * @param array $arrayBuyerQuoteSellersQuotesPrices
     * @return array
     */
    public static function getCountOfCartItems($buyerId, $buyerQuoteId, $isFtl = false) {
        try {
            Log::info('Get count of cart items: ' . Auth::id(), array(
                'c' => '2'
            ));
            $getCartItemsQuery = DB::table('cart_items as ci');
            
            if($isFtl) {
                $getCartItemsQuery->where('ci.buyer_quote_item_id', $buyerQuoteId);
            } else {
                $getCartItemsQuery->where('ci.buyer_quote_id', $buyerQuoteId);
            }
            $getCartItemsQuery->where('ci.buyer_id', $buyerId);
            $getCartItemsQuery->selectRaw('count(ci.id) as count');
            $arrayCountCartItem = $getCartItemsQuery->get();
            if (!empty($arrayCountCartItem)) {
                $countCartItem = $arrayCountCartItem [0]->count;
            } else {
                $countCartItem = '0';
            }
            return $countCartItem;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Buyer counter offer Page
     * Method to get count of orders
     *
     * @param array $arrayBuyerQuoteSellersQuotesPrices
     * @return array
     */
    public static function getCountOfOrders($buyerId, $buyerQuoteId, $isFtl = false) {
        try {
            Log::info('Get count of orders: ' . Auth::id(), array(
                'c' => '2'
            ));
            $getOrdersQuery = DB::table('orders as ci');
            if($isFtl) {
                $getOrdersQuery->where('ci.buyer_quote_item_id', $buyerQuoteId);
            } else {
                $getOrdersQuery->where('ci.buyer_quote_id', $buyerQuoteId);
            }
            $getOrdersQuery->where('ci.buyer_id', $buyerId);
            $getOrdersQuery->selectRaw('count(ci.id) as count');
            $arrayCountOrder = $getOrdersQuery->get();
            if (!empty($arrayCountOrder)) {
                $countorder = $arrayCountOrder [0]->count;
            } else {
                $countorder = '0';
            }
            return $countorder;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Buyer counter offer Page
     * Method to retrieve city name from the id
     *
     * @param int $locationId
     * @return array
     */
    public static function getCityNameFromId($locationId) {
        try {
            Log::info('Get city name: ' . Auth::id(), array(
                'c' => '2'
            ));
            $getLocationQuery = DB::table('lkp_cities as ll');
            if (!empty($locationId)) {
                $getLocationQuery->where('ll.id', $locationId);
            }
            $getLocationQuery->select('ll.id', 'll.city_name');
            $arrayLocation = $getLocationQuery->get();
            $no_city = 'No city name';
            if (count($arrayLocation) > 0)
                return $arrayLocation [0]->city_name;
            else
                return $no_city;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Buyer counter offer Page
     * Method to get last and after 3 days of the given date
     *
     * @param string $date
     * @return array
     */
    public static function getPreviousNextThreeDays($date) {
        try {
            Log::info('Get city name: ' . Auth::id(), array(
                'c' => '2'
            ));
            if (!empty($date)) {
                $previousDate = date('d/m/Y', strtotime($date . ' -3 day'));
                $nextDate = date('d/m/Y', strtotime($date . ' +3 day'));
            }
            $retunDate = $previousDate . " - " . $nextDate;
            return $retunDate;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Buyer counter offer Page
     * Method to change Id format
     *
     * @param int $id
     * @return array
     */
    public static function changeDisplayIdFormat($id) {
        try {
            return sprintf("%03d\n", $id);
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Get buyer counter offer Page
     * Method to get source and destination type
     *
     * @param string $locationType
     * @return array
     */
    public static function getSourceDestinationLocation($locationType) {
        try {
            Log::info('Get city name: ' . Auth::id(), array(
                'c' => '2'
            ));
            $locationsArray = array();
            $locations = DB::table('lkp_location_types as llt')->where([
                        'llt.is_active' => 1
                    ])->orderby('llt.id', 'asc')->select('llt.id', 'llt.location_type_name')->get();
            $locationsArray [0] = "$locationType – Location Type";
            for ($i = 0; $i < count($locations); $i ++) {
                $locationsArray [$locations [$i]->id] = $locations [$i]->location_type_name;
            }
            return $locationsArray;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Get buyer counter offer Page
     * Method to retrieve packaging Type
     *
     * @return array
     */
    public static function getPackagingType() {
        try {
            Log::info('Get packaging type: ' . Auth::id(), array(
                'c' => '2'
            ));
            $packagingTypesArray = array();
            $packagingTypes = DB::table('lkp_packaging_types as lpt')->where([
                        'lpt.is_active' => 1
                    ])->orderby('lpt.packaging_type_name', 'asc')->select('lpt.id', 'lpt.packaging_type_name')->get();
            $packagingTypesArray [0] = "Packaging Type";
            for ($i = 0; $i < count($packagingTypes); $i ++) {
                $packagingTypesArray [$packagingTypes [$i]->id] = $packagingTypes [$i]->packaging_type_name;
            }
            return $packagingTypesArray;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Buyer Quote Details Page
     * Retrieval of data related to buyer posts items
     */
    public static function getBuyerQuoteItemData($buyerQuoteItemId, $serviceId=null) {
        // query to retrieve buyer posts list and bind it to the grid
        if(empty($serviceId)) {
            $serviceId = Session::get('service_id');
        }
        
        switch ($serviceId) {
            case ROAD_FTL :
                $buyerQuoteItemData = DB::table('buyer_quote_items as bqi')
                        ->join('buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bqi.id', $buyerQuoteItemId)
                        ->select('bqi.*', 'bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                break;
            case ROAD_PTL : 
                $buyerQuoteItemData = DB::table('ptl_buyer_quote_items as bqi')
                        ->join('ptl_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bq.id', $buyerQuoteItemId)
                        ->select('bqi.*', 'bq.*','bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                break;
            case AIR_DOMESTIC : 
                $buyerQuoteItemData = DB::table('airdom_buyer_quote_items as bqi')
                        ->join('airdom_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bq.id', $buyerQuoteItemId)
                        ->select('bqi.*', 'bq.*','bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                break;
            case RAIL : 
                $buyerQuoteItemData = DB::table('rail_buyer_quote_items as bqi')
                        ->join('rail_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bq.id', $buyerQuoteItemId)
                        ->select('bqi.*', 'bq.*','bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                break;
            case AIR_INTERNATIONAL : 
                $buyerQuoteItemData = DB::table('airint_buyer_quote_items as bqi')
                        ->join('airint_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bq.id', $buyerQuoteItemId)
                        ->select('bqi.*', 'bq.*','bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                break;
            case OCEAN : 
                $buyerQuoteItemData = DB::table('ocean_buyer_quote_items as bqi')
                        ->join('ocean_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bq.id', $buyerQuoteItemId)
                        ->select('bqi.*', 'bq.*','bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                break;
            case COURIER : 
                $buyerQuoteItemData = DB::table('courier_buyer_quote_items as bqi')
                        ->join('courier_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bq.id', $buyerQuoteItemId)
                        ->select('bqi.*', 'bq.*','bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                break;

            case ROAD_INTRACITY :
                $buyerQuoteItemData = DB::table('ict_buyer_quote_items as bqi')
                        ->join('ict_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bqi.id', $buyerQuoteItemId)
                        ->select('bqi.*', 'bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                break;
            // case ROAD_TRUCK_HAUL: $grid = ThaulSellerListingComponent::listTruckHaulSellerPosts($statusId, $serviceId, $roleId);
            // break;
            case RELOCATION_DOMESTIC :
                $buyerQuoteItemData = DB::table('relocation_buyer_posts as bq')
                        //->join('buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bq.id', $buyerQuoteItemId)
                        ->select('bq.*')
                        ->get();
                break;
            case RELOCATION_INTERNATIONAL :
                $buyerQuoteItemData = DB::table('relocationint_buyer_posts as bq')
                        //->join('buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bq.id', $buyerQuoteItemId)
                        ->select('bq.*')
                        ->get();
                break;
            case RELOCATION_OFFICE_MOVE :
                $buyerQuoteItemData = DB::table('relocationoffice_buyer_posts as bq')
                        //->join('buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bq.id', $buyerQuoteItemId)
                        ->select('bq.*')
                        ->get();
                break;  
            case RELOCATION_PET_MOVE :
                $buyerQuoteItemData = DB::table('relocationpet_buyer_posts as bq')
                        ->where('bq.id', $buyerQuoteItemId)
                        ->select('bq.*')
                        ->get();
                break;
            case ROAD_TRUCK_HAUL :
                $buyerQuoteItemData = DB::table('truckhaul_buyer_quote_items as bqi')
                        ->join('truckhaul_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bqi.id', $buyerQuoteItemId)
                        ->select('bqi.*', 'bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                break;
            case ROAD_TRUCK_LEASE :
                $buyerQuoteItemData = DB::table('trucklease_buyer_quote_items as bqi')
                        ->join('trucklease_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bqi.id', $buyerQuoteItemId)
                        ->select('bqi.*', 'bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                break;
            case RELOCATION_GLOBAL_MOBILITY :
                $buyerQuoteItemData = DB::table('relocationgm_buyer_posts as bq')
                        ->where('bq.id', $buyerQuoteItemId)
                        ->select('bq.*')
                        ->get();
                break;
            default :
                $buyerQuoteItemData = DB::table('buyer_quote_items as bqi')
                        ->join('buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bqi.id', $buyerQuoteItemId)
                        ->select('bqi.*', 'bq.*','bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                break;
        }

        return $buyerQuoteItemData;
    }

    /**
     * Get buyer counter offer Page
     * Method to retrieve final price
     * @return array
     */
    public static function getFinalDetails($initialValue = null, $finalValue = null) {
        try {
            if (!empty($finalValue)) {
                $returnValue = $finalValue;
            } elseif (!empty($initialValue)) {
                $returnValue = $initialValue;
            } else {
                $returnValue = '0';
            }
            return $returnValue;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }

    /**
     * Get buyer counter offer Page
     * Method to retrieve final price
     * @return array
     */
    public static function getFinalDetailsForCounterOffer($initialValue = null, $counterValue = null, $finalValue = null) {
        try {
            if (!empty($finalValue) && $finalValue != '0.0000') {
                $returnValue = $finalValue;
            } elseif (!empty($counterValue) && $counterValue != '0.0000') {
                $returnValue = $counterValue;
            } elseif (!empty($initialValue) && $initialValue != '0.0000') {
                $returnValue = $initialValue;
            } else {
                $returnValue = '0';
            }
            return $returnValue;
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }
    
    
    /**
     * TermBuyer counter offer Page
     * Method to retrieve buyer quote requests data
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function getTermBuyerQuoteDetailsFromId($buyerQuoteItemId,$service_type) {
    	try {
    		Log::info('Get buyer quote requests data: ' . Auth::id(), array(
    				'c' => '2'
    		));
    		
    		$getPostBuyerCounterOfferQuery = DB::table('term_buyer_quote_items as bqi');
    		$getPostBuyerCounterOfferQuery->leftjoin('term_buyer_quotes as bq', 'bq.id', '=', 'bqi.term_buyer_quote_id');
    		$getPostBuyerCounterOfferQuery->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'bqi.lkp_load_type_id');
    		$getPostBuyerCounterOfferQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
    		$getPostBuyerCounterOfferQuery->leftjoin('lkp_quote_accesses as lqa', 'lqa.id', '=', 'bq.lkp_quote_access_id');
    		if ($service_type == ROAD_FTL || $service_type == RELOCATION_DOMESTIC || $service_type == RELOCATION_INTERNATIONAL) {
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_cities as cf', 'bqi.from_location_id', '=', 'cf.id' );
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_cities as ct', 'bqi.to_location_id', '=', 'ct.id' );
    		} elseif ($service_type == ROAD_PTL || $service_type == RAIL || $service_type == AIR_DOMESTIC) {
    			
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_ptl_pincodes as cf', 'bqi.from_location_id', '=', 'cf.id' );
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_ptl_pincodes as ct', 'bqi.to_location_id', '=', 'ct.id' );
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_packaging_types as lp', 'bqi.lkp_packaging_type_id', '=', 'lp.id' );
    		} elseif ($service_type == COURIER) {
    			$getPostBuyerCounterOfferQuery->leftjoin('lkp_courier_delivery_types as lct', 'lct.id', '=', 'bq.lkp_courier_delivery_type_id');
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_ptl_pincodes as cf', 'bqi.from_location_id', '=', 'cf.id' );
    			$getPostBuyerCounterOfferQuery->leftjoin('lkp_ptl_pincodes as lppt', function($join)
    			{
    				$join->on('bqi.to_location_id', '=', 'lppt.id');
    				$join->on(DB::raw('bq.lkp_courier_delivery_type_id'),'=',DB::raw(1));
    					
    			});
    			$getPostBuyerCounterOfferQuery->leftjoin('lkp_countries as lppt1', function($join)
    			{
    				$join->on('bqi.to_location_id', '=', 'lppt1.id');
    				$join->on(DB::raw('bq.lkp_courier_delivery_type_id'),'=',DB::raw(2));
    					
    			});
    			
    		} elseif ($service_type == AIR_INTERNATIONAL ) {
    			
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_airports as cf', 'bqi.from_location_id', '=', 'cf.id' );
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_airports as ct', 'bqi.to_location_id', '=', 'ct.id' );
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_packaging_types as lp', 'bqi.lkp_packaging_type_id', '=', 'lp.id' );
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_air_ocean_shipment_types as laosp', 'bqi.lkp_air_ocean_shipment_type_id', '=', 'laosp.id' );
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_air_ocean_sender_identities as laosi', 'bqi.lkp_air_ocean_sender_identity_id', '=', 'laosi.id' );
    		} elseif ($service_type == OCEAN ) {
    			
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_seaports as cf', 'bqi.from_location_id', '=', 'cf.id' );
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_seaports as ct', 'bqi.to_location_id', '=', 'ct.id' );
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_packaging_types as lp', 'bqi.lkp_packaging_type_id', '=', 'lp.id' );
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_air_ocean_shipment_types as laosp', 'bqi.lkp_air_ocean_shipment_type_id', '=', 'laosp.id' );
    			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_air_ocean_sender_identities as laosi', 'bqi.lkp_air_ocean_sender_identity_id', '=', 'laosi.id' );
    		} elseif($service_type == RELOCATION_GLOBAL_MOBILITY) {
               $getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_cities as cf', 'bqi.from_location_id', '=', 'cf.id' );
               $getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_relocationgm_services as lkrgs', 'bqi.lkp_gm_service_id', '=', 'lkrgs.id' );
          }
    		
    		
		    $getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_post_statuses as ps', 'bq.lkp_post_status_id', '=', 'ps.id' );
    		if (!empty($buyerQuoteItemId)) {
    			$getPostBuyerCounterOfferQuery->where('bq.id', $buyerQuoteItemId);
    		}
    		$getPostBuyerCounterOfferQuery->where('bqi.lkp_service_id', $service_type);
    		$getPostBuyerCounterOfferQuery->where('bq.lkp_service_id', $service_type);
    		if ($service_type == ROAD_FTL) {
    			$getPostBuyerCounterOfferQuery->select('bqi.id','bqi.quantity','bq.lkp_bid_type_id', 'ps.post_status','bq.lkp_post_status_id as quoteStatus', 'bq.from_date', 'bqi.is_cancelled', 
    					'bqi.lkp_post_status_id',  'bq.to_date', 'ldt.load_type', 'bqi.term_buyer_quote_id', 'bqi.from_location_id', 'bqi.to_location_id', 
    					'bq.lkp_quote_access_id', 'lqa.quote_access', 'lvt.vehicle_type', 'bq.transaction_id','cf.city_name as from_city','ct.city_name as to_city','bq.lkp_service_id','bqi.units');
    		} elseif ($service_type == ROAD_PTL || $service_type == RAIL || $service_type == AIR_DOMESTIC) {
    			$getPostBuyerCounterOfferQuery->select('bqi.id','bqi.quantity','bq.lkp_bid_type_id', 'ps.post_status','bq.lkp_post_status_id as quoteStatus', 'bq.from_date', 'bqi.is_cancelled', 
    					'bqi.lkp_post_status_id',  'bq.to_date', 'ldt.load_type', 'bqi.term_buyer_quote_id', 'bqi.from_location_id', 'bqi.to_location_id', 'bq.lkp_quote_access_id', 'lqa.quote_access', 
    					'lvt.vehicle_type', 'bq.transaction_id','cf.postoffice_name as from_city','ct.postoffice_name as to_city','lp.packaging_type_name','bqi.number_packages','bqi.volume','bq.lkp_service_id');
    		
    		} elseif ($service_type == COURIER) {
    				$getPostBuyerCounterOfferQuery->select('bqi.id','bqi.quantity','bq.lkp_bid_type_id', 'ps.post_status','bq.lkp_post_status_id as quoteStatus', 'bq.from_date', 'bqi.is_cancelled',
    					'bqi.lkp_post_status_id','lct.id as courier_delivery_id', 'lct.courier_delivery_type',  'bq.to_date', 'ldt.load_type', 'bqi.term_buyer_quote_id', 'bqi.from_location_id', 'bqi.to_location_id', 'bq.lkp_quote_access_id', 'lqa.quote_access',
    					'lvt.vehicle_type', 'bq.transaction_id','cf.postoffice_name as from_city', DB::raw("(case when `bq`.`lkp_courier_delivery_type_id` = 1 then lppt.postoffice_name  when `bq`.`lkp_courier_delivery_type_id` = 2 then lppt1.country_name end) as to_city"),'bqi.number_packages','bqi.volume','bq.lkp_service_id');
    			
    		} elseif ($service_type == AIR_INTERNATIONAL) {
    			$getPostBuyerCounterOfferQuery->select('bqi.id','bqi.quantity','bq.lkp_bid_type_id', 'ps.post_status','bq.lkp_post_status_id as quoteStatus', 'bq.from_date', 'bqi.is_cancelled', 
    					'bqi.lkp_post_status_id',  'bq.to_date', 'ldt.load_type', 'bqi.term_buyer_quote_id', 'bqi.from_location_id', 'bqi.to_location_id', 'bq.lkp_quote_access_id', 'lqa.quote_access', 
    					'lvt.vehicle_type', 'bq.transaction_id','cf.airport_name as from_city','ct.airport_name as to_city','lp.packaging_type_name','bqi.number_packages','bqi.volume','bq.lkp_service_id','laosp.*','bqi.ie_code','bqi.product_made','bqi.id as buyerquoteItemId','laosi.*');
    			
    		} elseif ($service_type == OCEAN) {
    			$getPostBuyerCounterOfferQuery->select('bqi.id','bqi.quantity','bq.lkp_bid_type_id', 'ps.post_status','bq.lkp_post_status_id as quoteStatus', 'bq.from_date', 'bqi.is_cancelled', 
    					'bqi.lkp_post_status_id',  'bq.to_date', 'ldt.load_type', 'bqi.term_buyer_quote_id', 'bqi.from_location_id', 'bqi.to_location_id', 'bq.lkp_quote_access_id', 'lqa.quote_access', 
    					'lvt.vehicle_type', 'bq.transaction_id','cf.seaport_name as from_city','ct.seaport_name as to_city','lp.packaging_type_name','bqi.number_packages','bqi.volume','bq.lkp_service_id','laosp.*','bqi.ie_code','bqi.product_made','bqi.id as buyerquoteItemId','laosi.*');
    			
    		}
    		elseif ($service_type == RELOCATION_DOMESTIC) {
    			$getPostBuyerCounterOfferQuery->select('bqi.id','bqi.quantity','bq.lkp_bid_type_id','bq.lkp_post_ratecard_type', 'ps.post_status','bq.lkp_post_status_id as quoteStatus', 'bq.from_date', 'bqi.is_cancelled', 
    					'bqi.lkp_post_status_id',  'bq.to_date', 'ldt.load_type', 'bqi.term_buyer_quote_id', 'bqi.from_location_id', 'bqi.to_location_id', 
    					'bq.lkp_quote_access_id', 'lqa.quote_access','lvt.vehicle_type','bq.transaction_id','cf.city_name as from_city','ct.city_name as to_city','bq.lkp_service_id','bqi.volume','bqi.number_packages','bqi.lkp_vehicle_category_id','bqi.lkp_vehicle_category_type_id','bqi.vehicle_model','bqi.no_of_vehicles');
    			
    		}
    		elseif ($service_type == RELOCATION_INTERNATIONAL) {
    			$getPostBuyerCounterOfferQuery->select('bqi.id','bqi.quantity','bq.lkp_bid_type_id','bq.lkp_post_ratecard_type', 'ps.post_status','bq.lkp_post_status_id as quoteStatus', 'bq.from_date', 'bqi.is_cancelled',
    					'bqi.lkp_post_status_id',  'bq.to_date', 'ldt.load_type', 'bqi.term_buyer_quote_id', 'bqi.from_location_id', 'bqi.to_location_id', 'bq.source_storage', 'bq.source_handyman', 'bq.destination_storage', 'bq.destination_handyman', 'bq.lkp_lead_type_id',
    					'bq.lkp_quote_access_id', 'lqa.quote_access','lvt.vehicle_type','bq.transaction_id','cf.city_name as from_city','ct.city_name as to_city','bq.lkp_service_id','bqi.volume','bqi.number_loads','bqi.lkp_vehicle_category_id','bqi.lkp_vehicle_category_type_id','bqi.vehicle_model','bqi.no_of_vehicles','bqi.avg_kg_per_move');
    			 
    		}elseif ($service_type == RELOCATION_GLOBAL_MOBILITY) {
    			$getPostBuyerCounterOfferQuery->select('bqi.id','bqi.quantity','bq.lkp_bid_type_id','bq.lkp_post_ratecard_type', 'ps.post_status','bq.lkp_post_status_id as quoteStatus', 'bq.from_date', 'bqi.is_cancelled',
    					'bqi.lkp_post_status_id',  'bq.to_date', 'ldt.load_type', 'bqi.term_buyer_quote_id', 'bqi.from_location_id', 'bq.lkp_lead_type_id', 'lkrgs.service_type as serviceType',
    					'bq.lkp_quote_access_id', 'lqa.quote_access','lvt.vehicle_type','bq.transaction_id','cf.city_name as from_city','bq.lkp_service_id','bqi.volume','bqi.number_loads','bqi.lkp_vehicle_category_id','bqi.measurement','bqi.measurement_units','bqi.lkp_gm_service_id','bqi.avg_kg_per_move');
    			 
    		}
    		
    		$arrayBuyerCounterOffer = $getPostBuyerCounterOfferQuery->get();
    		
    		return $arrayBuyerCounterOffer;
    	} catch (Exception $exc) {
    		// echo $exc->getTraceAsString();
    		// TODO:: Log the error somewhere
    	}
    }

    
    /**
     * Buyer counter offer Page
     * Method to retrieve buyer quote requests data
     *
     * @param int $buyerQuoteItemId
     * @return array
     */
    public static function updateTermBuyerQuoteDetailsViews($buyerQuoteItemId,$service_id) {
    	try {
    		Log::info('Get update buyer quote details view: ' . Auth::id(), array('c' => '2'));
    
    		$buyerCounterDetails = DB::table('term_buyer_quotes as bqi')
    		->where('bqi.id', '=', $buyerQuoteItemId)
    		->where('bqi.lkp_service_id', '=', $service_id)
    		->select('bqi.id', 'bqi.created_by')
    		->get();
    		if (!empty($buyerCounterDetails) && $buyerCounterDetails[0]->created_by != Auth::user()->id) {
    			$viewCount = DB::table('term_buyer_quote_item_views as bqiv')
    			->where('bqiv.user_id', '=', Auth::user()->id)
    			->where('bqiv.buyer_quote_item_id', '=', $buyerQuoteItemId)
    			->where('bqiv.lkp_service_id', '=', $service_id)
    			->select('bqiv.id', 'bqiv.view_counts')->get();
    
    			$createdAt = date('Y-m-d H:i:s');
    			$createdIp = $_SERVER ['REMOTE_ADDR'];
    
    			if (count($viewCount) == 0) { 	
    				$viewCountInsert = new TermBuyerQuoteItemView ();
    				$viewCountInsert->user_id = Auth::user()->id;
    				$viewCountInsert->term_buyer_quote_id = $buyerQuoteItemId;
    				$viewCountInsert->view_counts = 1;
    				$viewCountInsert->lkp_service_id = $service_id;
    				$viewCountInsert->created_at = $createdAt;
    				$viewCountInsert->created_by = Auth::user()->id;
    				$viewCountInsert->created_ip = $createdIp;
    				$viewCountInsert->save();
    				$countview = 1;
    			} else {
    				$countview = $viewCount [0]->view_counts + 1;
    				DB::table('buyer_quote_item_views as bqiv')->where('bqiv.user_id', '=', Auth::user()->id)->where('bqiv.buyer_quote_item_id', '=', $buyerQuoteItemId)->update(array(
    						'bqiv.view_counts' => $countview,
    						'bqiv.updated_at' => $createdAt
    				));
    			}
    		} else {
    			$countview = DB::table('term_buyer_quote_item_views as bqiv')
    			->where('bqiv.created_by', '=', Auth::user()->id)
    			->where('bqiv.term_buyer_quote_id', '=', $buyerQuoteItemId)
    			->select('bqiv.id', 'bqiv.view_counts')
    			->get();
    			if (!isset($countview[0]->view_counts)) {
    				$countview = 0;
    			} else {
    				$countview = $countview[0]->view_counts;
    			}
    		}
    		return $countview;
    	} catch (Exception $exc) {
    		// echo $exc->getTraceAsString();
    		// TODO:: Log the error somewhere
    	}
    }
    
    public static function geneartaeQCSFTL($sellerquotes){
        //echo "<pre>";print_R($sellerquotes);die;
    	//casting export...
			Excel::create('ExcelExport', function ($excel) {
			
				 $excel->sheet('Sheetname', function ($sheet) {
				 	
					// first row styling and writing content
					$sheet->mergeCells('D1:F1');
					$sheet->mergeCells('G1:I1');
					 $sheet->row(1, function ($row) {
						 
						$row->setFontWeight('bold');
						$row->setBackground('#ffff00');
					});
						$sheet->row(1, array('','','','Rate','','','Total Value'));
						 
						// second row styling and writing content
						$sheet->row(2, function ($row) {
							 
							$row->setFontWeight('bold');
							$row->setBackground('#ffff00');
							 
						});
							 
							$sheet->row(2, array('From','To','Quantity','L1','L2','L3','L1','L2','L3'));
							$lowestquoterows = Session::get ( 'lowestquotes' );

							$i=3;
							$totall1=0;
							$totall2=0;
							$totall3=0;
							foreach ($lowestquoterows as $lowestrow) {
								$sheet->appendRow($lowestrow);
								if($lowestrow[6]!='-'){
								$totall1=$totall1+$lowestrow[6];
								}else{
								$totall1='-';
								}
								if($lowestrow[7]!='-'){
								$totall2=$totall2+$lowestrow[7];
								}else{
								$totall2='-';
								}
								if($lowestrow[8]!='-'){
								$totall3=$totall3+$lowestrow[8];
								}else{
								$totall3='-';
								}
								
								$i++;
							}
							
							$sheet->row($i, array('','','','','','Total',$totall1,$totall2,$totall3));
							$sellers = Session::get ( 'sellerquotes' );
							for($i=0;$i<=count($sellers)-1;$i++){
								
							foreach ($sellers[$i] as $selle) {
								$sheet->appendRow($selle);
							}
							 
							}
							
							 
							$sheet->setBorder('A1:I300', 'thin');
							$sheet->cells('A1:I300', function($cells) {
								$cells->setAlignment('center');
								$cells->setValignment('middle');
							});
								 
				});
			
			
					 
			})->export('xls');
    	
    } 
    
    public static function geneartaeQCSOtherServices($sellerquotes){
    	 
    	//casting export...
    	Excel::create('ExcelExport', function ($excel) {

		   $excel->sheet('Sheetname', function ($sheet) {
		
		      // first row styling and writing content
		      $sheet->mergeCells('A2:K2');
		
		      //$sheet->setAllBorders('thin');
		
		
		      $sheet->row(2, function ($row) {
		         //$row->setFontFamily('Comic Sans MS');
		         //$row->setFontSize(10);
		         $row->setFontWeight('bold');
		         $row->setBackground('#A4A4A4');
		      });
		
		      $sheet->row(1, array(''));
		      $sheet->row(2, array('Quote Comparison Result'));
		
		      // second row styling and writing content
		      $sheet->row(3, function ($row) {
		         $row->setFontWeight('bold');
		      });
		      //$sheet->row(3, array('','','','','','','','L1','L2','L3'));
               $sheet->row(3, array(''));
		
		      // second row styling and writing content
		      $sheet->row(4, function ($row) {
		         $row->setFontWeight('bold');
		      });
		      $sheet->row(4, array('From Location','To Location','Load Type','Package Type','Volume','No Of Packages','L1','L2','L3'));
		
		
		   $lowestquoterowsother = Session::get ( 'lowestquotesother' );
			
							$i=5;
							$totall1=0;
							$totall2=0;
							$totall3=0;
							foreach ($lowestquoterowsother as $lowestrow) {
								$sheet->appendRow($lowestrow);
								if($lowestrow[6]!='-'){
								$totall1=$totall1+$lowestrow[6];
								}else{
								$totall1='-';
								}
								if($lowestrow[7]!='-'){
								$totall2=$totall2+$lowestrow[7];
								}else{
								$totall2='-';
								}
								if($lowestrow[8]!='-'){
								$totall3=$totall3+$lowestrow[8];
								}else{
								$totall3='-';
								}
								
								$i++;
							}
							
							$sheet->row($i, array('','','','','','Total',$totall1,$totall2,$totall3));
							$sellers = Session::get ( 'sellerquotesother' );
							for($i=0;$i<=count($sellers)-1;$i++){
							
								foreach ($sellers[$i] as $selle) {
									$sheet->appendRow($selle);
								}
							
							}
		
				      
				    
				$sheet->setBorder('A1:K400', 'thin');
		        $sheet->cells('A1:K400', function($cells) {
		         $cells->setAlignment('center');
		         $cells->setValignment('middle');
		      });
		   });
		
		})->export('xls');
    	 
    }
    
    public static function geneartaeQCSRELOCATION($sellerquotes){
    	//echo "<pre>";print_R($sellerquotes);die;
    	//casting export...
    	Excel::create('ExcelExport', function ($excel) {
    			
    			
    			
    		$excel->sheet('Sheetname', function ($sheet) {
    
    			// first row styling and writing content
    			$sheet->mergeCells('D1:F1');
    			$sheet->mergeCells('G1:I1');
    
    
    
    			$sheet->row(1, function ($row) {
    					
    				$row->setFontWeight('bold');
    				$row->setBackground('#ffff00');
    			});
    					
    					
    				$sheet->row(1, array('','','','','Rate','',''));
    					
    				// second row styling and writing content
    				$sheet->row(2, function ($row) {
    
    					$row->setFontWeight('bold');
    					$row->setBackground('#ffff00');
    
    				});
    
    					$sheet->row(2, array('From','To','Volume','Number of Packages','L1','L2','L3'));
    						
    					$lowestquoterows = Session::get ( 'lowestquotesrelocation' );
    
    					$i=3;
    					$totall1=0;
    					$totall2=0;
    					$totall3=0;
    					foreach ($lowestquoterows as $lowestrow) {
    						$sheet->appendRow($lowestrow);
    						if($lowestrow[4]!='-'){
    							$totall1=$totall1+$lowestrow[4];
    						}else{
    							$totall1='-';
    						}
    						if($lowestrow[5]!='-'){
    							$totall2=$totall2+$lowestrow[5];
    						}else{
    							$totall2='-';
    						}
    						if($lowestrow[6]!='-'){
    							$totall3=$totall3+$lowestrow[6];
    						}else{
    							$totall3='-';
    						}
    
    						$i++;
    					}
    						
    					$sheet->row($i, array('','','','Total',$totall1,$totall2,$totall3));
    					$sellers = Session::get ( 'sellerquotesrelocation' );
    					for($i=0;$i<=count($sellers)-1;$i++){
    
    						foreach ($sellers[$i] as $selle) {
    							$sheet->appendRow($selle);
    						}
    
    					}
    						
    
    					$sheet->setBorder('A1:I300', 'thin');
    					$sheet->cells('A1:I300', function($cells) {
    						$cells->setAlignment('center');
    						$cells->setValignment('middle');
    					});
    							
    		});
    				
    				
    
    	})->export('xls');
    	 
    }
    
    public static function geneartaeQCSRELOCATIONVEHICLE($sellerquotes){
    	//echo "<pre>";print_R($sellerquotes);die;
    	//casting export...
    	Excel::create('ExcelExport', function ($excel) {
    		 
    		 
    		 
    		$excel->sheet('Sheetname', function ($sheet) {
    
    			// first row styling and writing content
    			$sheet->mergeCells('D1:F1');
    			$sheet->mergeCells('G1:I1');
    
    
    
    			$sheet->row(1, function ($row) {
    					
    				$row->setFontWeight('bold');
    				$row->setBackground('#ffff00');
    			});
    					
    					
    				$sheet->row(1, array('','','','','Rate','',''));
    					
    				// second row styling and writing content
    				$sheet->row(2, function ($row) {
    
    					$row->setFontWeight('bold');
    					$row->setBackground('#ffff00');
    
    				});
    
    					$sheet->row(2, array('From','To','Vehicle Type','Vehicle Model','No of Vehicles','L1','L2','L3'));
    
    					$lowestquoterows = Session::get ( 'lowestquotesrelocationveh' );
    
    					$i=3;
    					$totall1=0;
    					$totall2=0;
    					$totall3=0;
    					foreach ($lowestquoterows as $lowestrow) {
    						$sheet->appendRow($lowestrow);
    						if($lowestrow[5]!='-'){
    							$totall1=$totall1+$lowestrow[5];
    						}else{
    							$totall1='-';
    						}
    						if($lowestrow[6]!='-'){
    							$totall2=$totall2+$lowestrow[6];
    						}else{
    							$totall2='-';
    						}
    						if($lowestrow[7]!='-'){
    							$totall3=$totall3+$lowestrow[7];
    						}else{
    							$totall3='-';
    						}
    
    						$i++;
    					}
    
    					$sheet->row($i, array('','','','','Total',$totall1,$totall2,$totall3));
    					$sellers = Session::get ( 'sellerquotesrelocationveh' );
    					for($i=0;$i<=count($sellers)-1;$i++){
    
    						foreach ($sellers[$i] as $selle) {
    							$sheet->appendRow($selle);
    						}
    
    					}
    
    
    					$sheet->setBorder('A1:I300', 'thin');
    					$sheet->cells('A1:I300', function($cells) {
    						$cells->setAlignment('center');
    						$cells->setValignment('middle');
    					});
    							
    		});
    
    
    
    	})->export('xls');
    
    }
    public static function priceCalculations($seller_post_item){
                $serviceId = Session::get('service_id');
                $fromLocationId=$seller_post_item->from_location_id;
                $toLocationId=$seller_post_item->to_location_id;
                $kgforCft=$seller_post_item->kg_per_cft;
                $odacharges=$seller_post_item->oda_charges;
                $seller_id=$seller_post_item->seller_id;
                $price=$seller_post_item->price;
                $sellerPostId=$seller_post_item->id;
                $pickupcahrges=$seller_post_item->pickup_charges;
                $deliverycahrges=$seller_post_item->delivery_charges;
                if($serviceId == COURIER){
                    $max_weight_accepted=$seller_post_item->max_weight_accepted;
                    $is_incremental=$seller_post_item->is_incremental;
                    $incremental_weight=$seller_post_item->increment_weight;

                    $rate_per_increment=$seller_post_item->rate_per_increment;
                    $fuelsurcharge=$seller_post_item->fuel_surcharge;
                    $codcharge=$seller_post_item->cod_charge;
                    $arccharge=$seller_post_item->arc_charge;
                    $freightcollectcharge=$seller_post_item->freight_collect_charge;
                    $paymentmodeid=$seller_post_item->lkp_payment_mode_id;
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
                    $ptlBuyerSessionSearch['ptlToLocation'][]=$arr[0];
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
            $data=array();$data['tot']=0;
            foreach ($new_array as $ptlSessionLineitems) {
                
                //echo "<pre>"; print_r($ptlSessionLineitems); echo $fromLocationId;                                    
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
                        $parcel = $ptlSessionLineitems['courier_types'];
                    }
                    $noOfPackages = $ptlSessionLineitems['ptlNopackages'];
                    $ptlUnitsWeight = $ptlSessionLineitems['ptlUnitsWeight'];                                       
                    //for hidden items
                                                          
                    $volume = $ptlSessionLineitems['ptlDisplayVolumeWeight'];
                    
                    if($serviceId!=AIR_DOMESTIC && $serviceId!=AIR_INTERNATIONAL && $serviceId!=OCEAN && $serviceId!=COURIER){
                    $door_pick = $ptlSessionLineitems['ptlDoorpickup'];
                    $door_delivery = $ptlSessionLineitems['ptlDoorDelivery'];
                    }else{
                        $door_pick ="";$door_delivery ="";
                    }
                    
                    $ptlcheckweightType = $ptlSessionLineitems['ptlCheckUnitWeight'];
                    //convert weight type to KGS.but in showing line items it will show only unitweight
                    //calcuation time only it will convert kgs and calcualtions
                    if ($ptlcheckweightType == 1) {
                        $ptlConvertunitweight = $ptlUnitsWeight;
                    } else if($ptlcheckweightType == 2) {
                        $ptlConvertunitweight = ($ptlUnitsWeight*0.001);
                    } else if($ptlcheckweightType == 3) {
                        $ptlConvertunitweight = ($ptlUnitsWeight*1000);
                    }                                       

                    $res=    PtlBuyerComponent::getVolumeWeight($ptlweightType,$ptlLength,$ptlWidth,$ptlHeight);
                    
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
                        $checkOda = CommonComponent::buyerODACheck($toLocationId,$serviceId,$seller_id);
                        if($checkOda == 1) {
                            $odaPrice=$odacharges;
                        } else {
                            $odaPrice = 0;
                        }

                        $totalChargableAmount = ($displayChargableweighttotal*$price); 
                        if($serviceId!=AIR_DOMESTIC && $serviceId!=AIR_INTERNATIONAL && $serviceId!=OCEAN && $serviceId!=COURIER){
                            if(isset($door_pick) && $door_pick==1){
                                $data['tot']    +=$pickupcahrges;
                            }
                            if(isset($door_delivery) && $door_delivery==1){
                                $data['tot']    +=$deliverycahrges;
                            }
                        }
                        $data['tot']    +=$totalChargableAmount+$odaPrice;
                    }else if($serviceId == COURIER){

                        $seller_post_slab_values  = DB::table('courier_seller_posts')
                        ->join ( 'courier_seller_post_items', 'courier_seller_post_items.seller_post_id', '=', 'courier_seller_posts.id' )
                        ->join ( 'courier_seller_post_item_slabs', 'courier_seller_post_item_slabs.seller_post_id', '=', 'courier_seller_posts.id' )
                        ->where('courier_seller_post_items.id',$sellerPostId)
                        ->select('courier_seller_post_item_slabs.*')
                        ->get();
                        
                         $conversion_factor = $kgforCft;
                        if($parcel=="parcel"){
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

                        $data['tot']    +=$totalChargableAmount + $fuelsurchargeCalVal + $freightcollectcharge + $arcchargeVal;
                        if($paymentmodeid == CASH_ON_DELIVERY){
                            $data['tot']    += $codchargeVal;
                        }
                    }                               


                }
            }
            return $data;
    }
    
    public static function priceCalculationsFromSearch($fromLocationId,$toLocationId){
            $serviceId = Session::get('service_id');
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
                    $ptlBuyerSessionSearch['ptlToLocation'][]=$arr[0];
            }
            }
            foreach($ptlBuyerSessionSearch as $i=>$element)
            {
                if(is_array($element) && !empty($element)){
                    foreach($element as $j=>$sub_element)
                    {
                        
                        $new_array[$j][$i] = $sub_element; //We are basically inverting the indexes
                    }
                }
            }
            $data=array();$data['tot']=0;
            foreach ($new_array as $ptlSessionLineitems) {
                                                   
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
                        $parcel = $ptlSessionLineitems['courier_types'];
                    }
                    $noOfPackages = $ptlSessionLineitems['ptlNopackages'];
                    $ptlUnitsWeight = $ptlSessionLineitems['ptlUnitsWeight'];   
                    
                    $flexible_delivery = $ptlSessionLineitems['ptlFlexiableDelivery'];
                    $ptlcheckweightType = $ptlSessionLineitems['ptlCheckUnitWeight'];
                    //convert weight type to KGS.but in showing line items it will show only unitweight
                    //calcuation time only it will convert kgs and calcualtions
                    if ($ptlcheckweightType == 1) {
                        $ptlConvertunitweight = $ptlUnitsWeight;
                    } else if($ptlcheckweightType == 2) {
                        $ptlConvertunitweight = ($ptlUnitsWeight*0.001);
                    } else if($ptlcheckweightType == 3) {
                        $ptlConvertunitweight = ($ptlUnitsWeight*1000);
                    }
                    if($serviceId!=AIR_INTERNATIONAL && $serviceId!=OCEAN && $serviceId!=COURIER){
                        
                            $door_pick = $ptlSessionLineitems['ptlDoorpickup'];
                            $door_delivery = $ptlSessionLineitems['ptlDoorDelivery'];
                    }else{
                        $door_pick =0;$door_delivery =0;
                    }
                    //$odaPrice = 0;
                    if($serviceId == COURIER){
                        $volume=$ptlLength*$ptlWidth*$ptlHeight;
                    }
                    
                    $res=    PtlBuyerComponent::getVolumeWeight($ptlweightType,$ptlLength,$ptlWidth,$ptlHeight);
                    $res['noOfPackages']=$noOfPackages;
                    $res['ptlConvertunitweight']=$ptlConvertunitweight;
                    $res['door_pick']=$door_pick;
                    $res['door_delivery']=$door_delivery;
                    $res['ptlUnitsWeight']=$ptlUnitsWeight;
                    if($serviceId == COURIER){
                    $res['packageValue']=$packageValue;
                    $res['parcel']=$parcel;
                    $res['volume']=$volume;
                    }
                }
            }
        return  $res;   
    }
    
    public static function getPostDetails($postid){
    	
    	$serviceId = Session::get('service_id');
    	$id=$postid;	
    	switch ($serviceId) {
    		
    		case ROAD_PTL :
    			$quotes=DB::table('ptl_buyer_quote_items')->where('ptl_buyer_quote_items.buyer_quote_id', $id)->select('ptl_buyer_quote_items.*')->get();
    	
    			break;
    		case RAIL :
    			$quotes=DB::table('rail_buyer_quote_items')->where('rail_buyer_quote_items.buyer_quote_id', $id)->select('rail_buyer_quote_items.*')->get();
    			break;
    		case AIR_DOMESTIC :
    			$quotes=DB::table('airdom_buyer_quote_items')->where('airdom_buyer_quote_items.buyer_quote_id', $id)->select('airdom_buyer_quote_items.*')->get();
    	
    			break;
    		case AIR_INTERNATIONAL :
    			$quotes=DB::table('airint_buyer_quote_items')->where('airint_buyer_quote_items.buyer_quote_id', $id)->select('airint_buyer_quote_items.*')->get();
    			
    			break;
    		case OCEAN :
    			$quotes=DB::table('ocean_buyer_quote_items')->where('ocean_buyer_quote_items.buyer_quote_id', $id)->select('ocean_buyer_quote_items.*')->get();
    			break;
    		case COURIER :
    			$quotes=DB::table('courier_buyer_quote_items')->where('courier_buyer_quote_items.buyer_quote_id', $id)->select('courier_buyer_quote_items.*')->get();
    			break;
    		case ROAD_TRUCK_HAUL :
    			$quotes=DB::table('truckhaul_buyer_quote_items')->where('truckhaul_buyer_quote_items.buyer_quote_id', $id)->select('truckhaul_buyer_quote_items.*')->get();
    			
    			break;
    		case ROAD_TRUCK_LEASE :
    			$quotes=DB::table('trucklease_buyer_quote_items')->where('trucklease_buyer_quote_items.buyer_quote_id', $id)->select('trucklease_buyer_quote_items.*')->get();
    			break;
    		case ROAD_INTRACITY :
    			$quotes=DB::table('ict_buyer_quote_items')->where('ict_buyer_quote_items.buyer_quote_id', $id)->select('ict_buyer_quote_items.*')->get();
    			break;
    	}
    		
    	
    	return $quotes;
    	
    }
}