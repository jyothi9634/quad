<?php

namespace App\Components\Ptl;

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

use App\Models\User;
use App\Models\BuyerQuoteSellersQuotesPrices;
use App\Models\CartItem;
use App\Models\PtlBuyerQuoteItemView;
use App\BuyerQuoteItems;
use App\Models\FtlSearchTerm;

class PtlBuyerGetQuoteBooknowComponent {

    /**
    * Buyer counter offer Page
    * Method to retrieve buyer quote requests data
    *
    * @param int $buyerQuoteItemId
    * @return array
    */
	public static function updateBuyerQuoteDetailsViews($buyerQuoteId, $tableName) {
		try {
			Log::info ('Get update buyer quote details view: ' . Auth::id (), array ('c' => '2'));
            $countview = DB::table($tableName.' as bqiv')
                         ->where('bqiv.buyer_quote_item_id','=',$buyerQuoteId)
                            ->select('bqiv.id','bqiv.view_counts')
                            ->get();
            if(!isset($countview[0]->view_counts)) {
                $countview = 0;
            } else {
                $countview = $countview[0]->view_counts;
            }
			return $countview;
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}

	/**
	 * Buyer counter offer Page
	 * Method to retrieve buyer quote requests data
	 * @param int $buyerQuoteItemId
	 * @return array
	 */
	public static function getBuyerQuoteDetailsFromIdForPtl($buyerQuoteId) {
		try {
			Log::info ( 'Get buyer quote requests data: ' . Auth::id (), array (
					'c' => '2'
			) );
                        $serviceId = Session::get ( 'service_id' );
                        switch($serviceId){
                            case ROAD_PTL:  
                            $getPostBuyerCounterOfferQuery = DB::table ( 'ptl_buyer_quotes as bq' );
                            $getPostBuyerCounterOfferQuery->leftjoin ( 'ptl_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            break;
                            case RAIL:
                            $getPostBuyerCounterOfferQuery = DB::table ( 'rail_buyer_quotes as bq' );
                            $getPostBuyerCounterOfferQuery->leftjoin ( 'rail_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            break;
                            case AIR_DOMESTIC       : 
                            $getPostBuyerCounterOfferQuery = DB::table ( 'airdom_buyer_quotes as bq' );
                            $getPostBuyerCounterOfferQuery->leftjoin ( 'airdom_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            break;
                            case AIR_INTERNATIONAL       : 
                            $getPostBuyerCounterOfferQuery = DB::table ( 'airint_buyer_quotes as bq' );
                            $getPostBuyerCounterOfferQuery->leftjoin ( 'airint_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            $getPostBuyerCounterOfferQuery->leftjoin('lkp_air_ocean_shipment_types as st', 'st.id', '=', 'bq.lkp_air_ocean_shipment_type_id');
                            $getPostBuyerCounterOfferQuery->leftjoin('lkp_air_ocean_sender_identities as si', 'si.id', '=', 'bq.lkp_air_ocean_sender_identity_id');
                            break;
                            case OCEAN       : 
                            $getPostBuyerCounterOfferQuery = DB::table ( 'ocean_buyer_quotes as bq' );
                            $getPostBuyerCounterOfferQuery->leftjoin ( 'ocean_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            $getPostBuyerCounterOfferQuery->leftjoin('lkp_air_ocean_shipment_types as st', 'st.id', '=', 'bq.lkp_air_ocean_shipment_type_id');
                            $getPostBuyerCounterOfferQuery->leftjoin('lkp_air_ocean_sender_identities as si', 'si.id', '=', 'bq.lkp_air_ocean_sender_identity_id');
                            break;
                            case COURIER       : 
                            $getPostBuyerCounterOfferQuery = DB::table ( 'courier_buyer_quotes as bq' );
                            $getPostBuyerCounterOfferQuery->leftjoin ( 'courier_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            $getPostBuyerCounterOfferQuery->leftjoin('lkp_courier_types as st', 'st.id', '=', 'bqi.lkp_courier_type_id');
                            $getPostBuyerCounterOfferQuery->leftjoin('lkp_courier_delivery_types as si', 'si.id', '=', 'bqi.lkp_courier_delivery_type_id');
                            break;
                            default       : 
                            $getPostBuyerCounterOfferQuery = DB::table ( 'ptl_buyer_quotes as bq' );
                            $getPostBuyerCounterOfferQuery->leftjoin ( 'ptl_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            break;
                }
            if($serviceId != COURIER){
			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_load_types as ldt', 'ldt.id', '=', 'bqi.lkp_load_type_id' );
            }
           	$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_quote_price_types as lqpt', 'lqpt.id', '=', 'bqi.lkp_quote_price_type_id' );
			$getPostBuyerCounterOfferQuery->leftjoin ( 'lkp_quote_accesses as lqa', 'lqa.id', '=', 'bq.lkp_quote_access_id' );
			$getPostBuyerCounterOfferQuery->leftjoin ( 'users as u', 'u.id', '=', 'bq.buyer_id' );
			if (! empty ( $buyerQuoteId )) {
				$getPostBuyerCounterOfferQuery->where ( 'bq.id', $buyerQuoteId );
			}
                        switch($serviceId){
                            case ROAD_PTL:  
                            case RAIL:
                            case AIR_DOMESTIC: 
			$getPostBuyerCounterOfferQuery->select ( 'bq.id', 'bq.dispatch_date', 'u.username', 
                                'bq.is_dispatch_flexible', 'bq.is_delivery_flexible', 'bq.transaction_id',
                                'bq.delivery_date', 'ldt.load_type', 'bqi.buyer_quote_id', 'bq.is_door_pickup',
                                'bq.from_location_id', 'bq.to_location_id', 'bq.lkp_quote_access_id', 
                                'lqa.quote_access', 'lqpt.price_type', 'bq.is_door_delivery','bq.lkp_post_status_id','bq.is_commercial');
                            break;
                            case AIR_INTERNATIONAL: 
                            case OCEAN :    
			$getPostBuyerCounterOfferQuery->select ( 'bq.product_made','bq.ie_code','si.sender_identity','st.shipment_type','bq.id', 'bq.dispatch_date', 'u.username', 
                                'bq.is_dispatch_flexible', 'bq.is_delivery_flexible', 'bq.transaction_id',
                                'bq.delivery_date', 'ldt.load_type', 'bqi.buyer_quote_id',
                                'bq.from_location_id', 'bq.to_location_id', 'bq.lkp_quote_access_id', 
                                'lqa.quote_access', 'lqpt.price_type','bq.lkp_post_status_id','bq.is_commercial');
                            break;
                            case COURIER :    
                        $getPostBuyerCounterOfferQuery->select ( 'si.courier_delivery_type','st.courier_type','bq.id', 'bq.dispatch_date', 'u.username', 
                                'bq.is_dispatch_flexible', 'bq.is_delivery_flexible','bq.transaction_id', 'bq.delivery_date', 'bqi.buyer_quote_id','bqi.package_value','bqi.lkp_courier_purpose_id',
                                'bq.from_location_id', 'bq.to_location_id', 'bq.lkp_quote_access_id', 
                                'lqa.quote_access', 'lqpt.price_type','bq.lkp_post_status_id','bq.is_commercial');
                            break;
                            default:  
			$getPostBuyerCounterOfferQuery->select ( 'bq.id', 'bq.dispatch_date', 'u.username', 
                                'bq.is_dispatch_flexible', 'bq.is_delivery_flexible', 'bq.transaction_id',
                                'bq.delivery_date', 'ldt.load_type', 'bqi.buyer_quote_id', 'bq.is_door_pickup',
                                'bq.from_location_id', 'bq.to_location_id', 'bq.lkp_quote_access_id', 
                                'lqa.quote_access', 'lqpt.price_type', 'bq.is_door_delivery','bq.lkp_post_status_id','bq.is_commercial');
                            break;
                        }
                                $arrayBuyerCounterOffer = $getPostBuyerCounterOfferQuery->get ();
                                //echo "<pre>";print_r($arrayBuyerCounterOffer);exit;
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
	public static function getPrivateSellerNames($buyerQuoteId) {
		try {
			Log::info ( 'Get private seller names: ' . Auth::id (), array (
                        'c' => '2'
                    ));
            $serviceId = Session::get ( 'service_id' );
            switch($serviceId){
                case ROAD_PTL:  
                    $getPostBuyerCounterOfferQuery = DB::table ( 'ptl_buyer_quotes as bq' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'ptl_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'ptl_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'bq.id' );
                    break;
                case RAIL:
                    $getPostBuyerCounterOfferQuery = DB::table ( 'rail_buyer_quotes as bq' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'rail_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'rail_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'bq.id' );
                    break;
                case AIR_DOMESTIC       : 
                    $getPostBuyerCounterOfferQuery = DB::table ( 'airdom_buyer_quotes as bq' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'airdom_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'airdom_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'bq.id' );
                    break;
                case AIR_INTERNATIONAL       : 
                    $getPostBuyerCounterOfferQuery = DB::table ( 'airint_buyer_quotes as bq' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'airint_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'airint_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'bq.id' );
                    
                    break;
                case OCEAN       : 
                    $getPostBuyerCounterOfferQuery = DB::table ( 'ocean_buyer_quotes as bq' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'ocean_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'ocean_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'bq.id' );
                    break;
                case COURIER       : 
                    $getPostBuyerCounterOfferQuery = DB::table ( 'courier_buyer_quotes as bq' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'courier_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'courier_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'bq.id' );
                    break;
                default       : 
                    $getPostBuyerCounterOfferQuery = DB::table ( 'ptl_buyer_quotes as bq' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'ptl_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' );
                    $getPostBuyerCounterOfferQuery->leftjoin ( 'ptl_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'bq.id' );
                    break;
            }
			$getPostBuyerCounterOfferQuery->leftjoin ( 'users as seller_names', 'seller_names.id', '=', 'pbqss.seller_id' );
			if (! empty ( $buyerQuoteId )) {
				$getPostBuyerCounterOfferQuery->where ( 'bq.id', $buyerQuoteId );
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
    * Method to retrieve seller lists
    * @param int $buyerQuoteItemId
    * @return array
    */
	public static function getBuyerQuoteSellersQuotesPricesFromId($buyerQuoteId,$comparisonType=null,$priceVal=null,$checkIds=null) {
		try {
			$serviceId = Session::get('service_id');
			
			Log::info ('Get seller lists for the buyer: ' . Auth::id (), array ('c' => '2'));
			(object)$arrayBuyerQuoteSellersNotQuotesPrices="";
			switch($serviceId){
                        case ROAD_PTL       :
                            $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'ptl_buyer_quote_sellers_quotes_prices as bqsqp' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'ptl_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'ptl_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'ptl_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'ptl_buyer_quotes as bq', 'bq.id', '=', 'bqsqp.buyer_quote_id' );
                        break;
                        case RAIL       :
                            $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'rail_buyer_quote_sellers_quotes_prices as bqsqp' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'rail_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'rail_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'rail_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'rail_buyer_quotes as bq', 'bq.id', '=', 'bqsqp.buyer_quote_id' );
                        break;
                        case AIR_DOMESTIC       :
                            $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'airdom_buyer_quote_sellers_quotes_prices as bqsqp' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'airdom_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'airdom_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'airdom_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'airdom_buyer_quotes as bq', 'bq.id', '=', 'bqsqp.buyer_quote_id' );
                        break;
                        case AIR_INTERNATIONAL       :
                            $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'airint_buyer_quote_sellers_quotes_prices as bqsqp' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'airint_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'airint_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'airint_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'airint_buyer_quotes as bq', 'bq.id', '=', 'bqsqp.buyer_quote_id' );
                        break;
                        case OCEAN       :
                            $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'ocean_buyer_quote_sellers_quotes_prices as bqsqp' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'ocean_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'ocean_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'ocean_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id' );
                			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'ocean_buyer_quotes as bq', 'bq.id', '=', 'bqsqp.buyer_quote_id' );
                        break;
                        case COURIER       :
                        	$getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'courier_buyer_quote_sellers_quotes_prices as bqsqp' );
                        	$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'courier_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id' );
                        	$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'courier_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' );
                        	$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'courier_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id' );
                        	$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'courier_buyer_quotes as bq', 'bq.id', '=', 'bqsqp.buyer_quote_id' );
                        	break;
                        }
                        
                        $getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'users as u', 'u.id', '=', 'bqsqp.seller_id' );
						$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_post_statuses as lps', 'bq.lkp_post_status_id', '=', 'lps.id' );
						if($serviceId != COURIER){
                        	$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_load_types as ldt', 'ldt.id', '=', 'bqi.lkp_load_type_id' );
							$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_packaging_types as lpt', 'lpt.id', '=', 'bqi.lkp_packaging_type_id' );
						}
						$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_ict_weight_uom as liwu', 'liwu.id', '=', 'bqi.lkp_ptl_length_uom_id' );
			if (! empty ( $buyerQuoteId )) {
				$getBuyerQuoteSellersQuotesPricesQuery->where ( 'bqsqp.buyer_quote_id', $buyerQuoteId );
			}
                        $getBuyerQuoteSellersQuotesPricesQuery->whereRaw('(`bqsqp`.`initial_quote_price` is not NULL and `bqsqp`.`initial_quote_price` != "" or `bqsqp`.`seller_acceptence` = "1")');
			$getBuyerQuoteSellersQuotesPricesQuery->where( 'bqsqp.buyer_id', Auth::User ()->id );
			if (!empty($comparisonType)) {
				 
			if($checkIds){

            		$checkIds= explode(",",$checkIds);
            		$getBuyerQuoteSellersQuotesPricesQuery->whereIn('bqsqp.id', $checkIds);
            		switch($serviceId){
                        case ROAD_PTL       :
                            $getBuyerQuoteSellersNotQuotesPricesQuery = DB::table ( 'ptl_buyer_quote_sellers_quotes_prices as bqsqp' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'ptl_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'ptl_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'ptl_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'ptl_buyer_quotes as bq', 'bq.id', '=', 'bqsqp.buyer_quote_id' );
                            break;
                        case RAIL       :
                            $getBuyerQuoteSellersNotQuotesPricesQuery = DB::table ( 'rail_buyer_quote_sellers_quotes_prices as bqsqp' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'rail_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'rail_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'rail_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'rail_buyer_quotes as bq', 'bq.id', '=', 'bqsqp.buyer_quote_id' );
                            break;
                        case AIR_DOMESTIC       :
                            $getBuyerQuoteSellersNotQuotesPricesQuery = DB::table ( 'airdom_buyer_quote_sellers_quotes_prices as bqsqp' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'airdom_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'airdom_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'airdom_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'airdom_buyer_quotes as bq', 'bq.id', '=', 'bqsqp.buyer_quote_id' );
                            break;

                        case AIR_INTERNATIONAL       :
                            $getBuyerQuoteSellersNotQuotesPricesQuery = DB::table ( 'airint_buyer_quote_sellers_quotes_prices as bqsqp' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'airint_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'airint_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'airint_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'airint_buyer_quotes as bq', 'bq.id', '=', 'bqsqp.buyer_quote_id' );
                            break;
                        case OCEAN       :
                            $getBuyerQuoteSellersNotQuotesPricesQuery = DB::table ( 'ocean_buyer_quote_sellers_quotes_prices as bqsqp' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'ocean_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'ocean_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'ocean_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'ocean_buyer_quotes as bq', 'bq.id', '=', 'bqsqp.buyer_quote_id' );
                            break;
                        case COURIER       :
                            $getBuyerQuoteSellersNotQuotesPricesQuery = DB::table ( 'courier_buyer_quote_sellers_quotes_prices as bqsqp' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'courier_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'courier_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'courier_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'courier_buyer_quotes as bq', 'bq.id', '=', 'bqsqp.buyer_quote_id' );
                            break;
                        default      :
                            $getBuyerQuoteSellersNotQuotesPricesQuery = DB::table ( 'ptl_buyer_quote_sellers_quotes_prices as bqsqp' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'ptl_seller_post_items as spi', 'spi.id', '=', 'bqsqp.seller_post_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'ptl_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'ptl_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.buyer_quote_item_id' );
                            $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'ptl_buyer_quotes as bq', 'bq.id', '=', 'bqsqp.buyer_quote_id' );
                            break;
                        
                        }
                        $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'users as u', 'u.id', '=', 'bqsqp.seller_id' );
                        $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'lkp_post_statuses as lps', 'bq.lkp_post_status_id', '=', 'lps.id' );
                        if($serviceId != COURIER){
                        $getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'lkp_load_types as ldt', 'ldt.id', '=', 'bqi.lkp_load_type_id' );
						$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'lkp_packaging_types as lpt', 'lpt.id', '=', 'bqi.lkp_packaging_type_id' );
                        }
						$getBuyerQuoteSellersNotQuotesPricesQuery->leftjoin ( 'lkp_ict_weight_uom as liwu', 'liwu.id', '=', 'bqi.lkp_ptl_length_uom_id' );

			
            		if (!empty($buyerQuoteId)) {
            			
            			$getBuyerQuoteSellersNotQuotesPricesQuery->where ( 'bqsqp.buyer_quote_id', $buyerQuoteId );
            		}
            		
            		$getBuyerQuoteSellersNotQuotesPricesQuery->whereRaw('(`bqsqp`.`initial_quote_price` is not NULL and `bqsqp`.`initial_quote_price` != "" or `bqsqp`.`seller_acceptence` = "1")');
            		$getBuyerQuoteSellersNotQuotesPricesQuery->where( 'bqsqp.buyer_id', Auth::User ()->id );
            		
            		$getBuyerQuoteSellersNotQuotesPricesQuery->whereNotIn('bqsqp.id', $checkIds);
            		
            		$getBuyerQuoteSellersNotQuotesPricesQuery->select( 'bqsqp.private_seller_quote_id','sp.transaction_id as transaction_no','bqsqp.id', 'bqsqp.buyer_id', 'bqsqp.initial_quote_price', 'bqsqp.initial_freight_amount', 'bqi.calculated_volume_weight', 
                                                            'bqsqp.counter_quote_price', 'bqsqp.final_quote_price', 'u.username', 'bqsqp.initial_rate_per_kg', 'bqi.units as buyerQuoteUnits', 
                                                            'ldt.load_type', 'bqsqp.seller_id', 'bqsqp.buyer_quote_item_id', 'bqsqp.initial_delivery_rupees', 'liwu.weight_type', 
                                                            'bqsqp.seller_post_item_id', 'bqsqp.initial_transit_days', 'spi.units', 'bqsqp.initial_pick_up_rupees', 'bqsqp.final_kg_per_cft', 
                                                            'bqsqp.seller_acceptence', 'bq.lkp_quote_access_id', 'spi.seller_post_id', 'bqsqp.initial_kg_per_cft', 'liwu.id as weightTypeId', 
                                                            'sp.from_date',	'sp.to_date', 'bqsqp.initial_oda_rupees', 'lpt.packaging_type_name', 'bqsqp.counter_kg_per_cft', 'bqsqp.final_transit_days', 
                                                            'bqsqp.final_oda_rupees', 'bqsqp.final_pick_up_rupees', 'bqsqp.final_delivery_rupees', 'bqsqp.counter_rate_per_kg',
                                                            'bqsqp.final_rate_per_kg','bqsqp.counter_freight_amount', 'bqsqp.final_freight_amount', 'bqsqp.counter_quote_price',
                                                            'bqsqp.final_quote_price','bq.lkp_post_status_id','lps.post_status', 'bqi.number_packages', 'bqsqp.buyer_quote_id as buyerQuoteId');
            		if ($comparisonType == '1') {
            			$getBuyerQuoteSellersNotQuotesPricesQuery->orderBy('bqsqp.initial_transit_days', 'asc');
            		
            		} elseif ($comparisonType == '2') {
            			$price="";
            			
            			if($priceVal=="final_quote_price"){
            				$price='bqsqp.final_quote_price';
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
                    $getBuyerQuoteSellersQuotesPricesQuery->orderBy('bqsqp.initial_transit_days', 'asc');
                    
                 } elseif ($comparisonType == '2') {
                 	$price="";
                 	//echo $priceVal;
            		
                 	if($priceVal=="final_quote_price"){
                 	  $price='bqsqp.final_quote_price';
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
			if($serviceId != COURIER){
			$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.private_seller_quote_id','sp.transaction_id as transaction_no', 'bqsqp.id', 'bqsqp.buyer_id', 'bqsqp.initial_quote_price', 'bqsqp.initial_freight_amount', 'bqi.calculated_volume_weight', 
                                                            'bqsqp.counter_quote_price', 'bqsqp.final_quote_price', 'u.username', 'bqsqp.initial_rate_per_kg', 'bqi.units as buyerQuoteUnits', 
                                                            'ldt.load_type', 'bqsqp.seller_id', 'bqsqp.buyer_quote_item_id', 'bqsqp.initial_delivery_rupees', 'liwu.weight_type', 
                                                            'bqsqp.seller_post_item_id', 'bqsqp.initial_transit_days', 'spi.units', 'bqsqp.initial_pick_up_rupees', 'bqsqp.final_kg_per_cft', 
                                                            'bqsqp.seller_acceptence', 'bq.lkp_quote_access_id', 'spi.seller_post_id', 'bqsqp.initial_kg_per_cft', 'liwu.id as weightTypeId', 'bqsqp.final_transit_days', 
                                                            'sp.from_date',	'sp.to_date', 'bqsqp.initial_oda_rupees', 'lpt.packaging_type_name', 'bqsqp.counter_kg_per_cft', 
                                                            'bqsqp.final_oda_rupees', 'bqsqp.final_pick_up_rupees', 'bqsqp.final_delivery_rupees', 'bqsqp.counter_rate_per_kg',
                                                            'bqsqp.final_rate_per_kg','bqsqp.counter_freight_amount', 'bqsqp.final_freight_amount', 'bqsqp.counter_quote_price',
                                                            'bqsqp.final_quote_price','bq.lkp_post_status_id','lps.post_status', 'bqi.number_packages', 'bqsqp.buyer_quote_id as buyerQuoteId');
			}
			else{
				$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.private_seller_quote_id','sp.transaction_id as transaction_no', 'bqsqp.id', 'bqsqp.buyer_id', 'bqsqp.initial_quote_price', 'bqsqp.initial_freight_amount', 'bqi.calculated_volume_weight',
															'bqsqp.counter_quote_price', 'bqsqp.final_quote_price', 'u.username', 'bqsqp.initial_rate_per_kg', 'bqi.units as buyerQuoteUnits',
															 'bqsqp.seller_id', 'bqsqp.buyer_quote_item_id',  'liwu.weight_type','bqsqp.initial_conversion_factor',
															'bqsqp.seller_post_item_id', 'bqsqp.initial_transit_days', 'spi.units','bqsqp.counter_conversion_factor',
															'bqsqp.seller_acceptence', 'bq.lkp_quote_access_id', 'spi.seller_post_id', 'liwu.id as weightTypeId', 'bqsqp.final_transit_days',
															'sp.from_date',	'sp.to_date','bqsqp.counter_rate_per_kg','bqsqp.final_conversion_factor',
															'bqsqp.final_rate_per_kg','bqsqp.counter_freight_amount', 'bqsqp.final_freight_amount', 'bqsqp.counter_quote_price',
															'bqsqp.final_quote_price','bq.lkp_post_status_id','lps.post_status', 'bqi.number_packages', 'bqsqp.buyer_quote_id as buyerQuoteId');
					
				
			}
			
				
			$arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
			
			if(!empty($comparisonType)){
				//$getBuyerQuoteSellersQuotesPricesQuery = array();
				// $i=0;
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
						if($arrayBuyerQuoteSellersQuotesPrices[$i]->initial_transit_days!=$arrayBuyerQuoteSellersQuotesPrices[$j]->initial_transit_days){
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
							} else{
								$arrayBuyerQuoteSellersQuotesPrices[$i]->rank="-";
							}
						}
						}
					$j++;
			
			
			
				}
			}
			
			
			if(!empty($comparisonType) && !empty($checkIds)){
				$obj_merged = (array) array_merge((array) $arrayBuyerQuoteSellersQuotesPrices, (array) $arrayBuyerQuoteSellersNotQuotesPrices);
				//echo "<pre>";
				//print_r($obj_merged);
				//exit;
				return $obj_merged;
			}else{
				
				//print_r($arrayBuyerQuoteSellersQuotesPrices);
				//exit;
				return $arrayBuyerQuoteSellersQuotesPrices;
			}
			
        
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}
	/**
    * Buyer counter offer Page list items
    * Method to retrieve seller lists
    * @param int $buyerQuoteItemId
    * @return array
    */
	public static function getBuyerQuoteItems($buyerQuoteId) {
		try {
			Log::info ('Get seller lists for the buyer: ' . Auth::id (), array ('c' => '2'));
			$serviceId = Session::get('service_id');

			switch($serviceId){
                            
                            case ROAD_PTL:
                            $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'ptl_buyer_quote_items as bqi' );
                            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'ptl_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            break;
                            case RAIL:
                            $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'rail_buyer_quote_items as bqi' );
                            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'rail_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            break;
                            case AIR_DOMESTIC   : 
                            $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'airdom_buyer_quote_items as bqi' );
                            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'airdom_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            break;
                            case COURIER   :
                            $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'courier_buyer_quote_items as bqi' );
                            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'courier_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            break;
                            case AIR_INTERNATIONAL   : 
                            $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'airint_buyer_quote_items as bqi' );
                            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'airint_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            break;
                            case OCEAN   : 
                            $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'ocean_buyer_quote_items as bqi' );
                            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'ocean_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            break;
                            case COURIER   : 
                            $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'courier_buyer_quote_items as bqi' );
                            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'courier_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            break;
                            default   : 
                            $getBuyerQuoteSellersQuotesPricesQuery = DB::table ( 'ptl_buyer_quote_items as bqi' );
                            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'ptl_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
                            break;
                            
                        }
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_post_statuses as lps', 'bqi.lkp_post_status_id', '=', 'lps.id' );
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'users as u', 'u.id', '=', 'bqi.created_by' );
            if($serviceId != COURIER){
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_load_types as ldt', 'ldt.id', '=', 'bqi.lkp_load_type_id' );
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_packaging_types as lpt', 'lpt.id', '=', 'bqi.lkp_packaging_type_id' );
			}
			if($serviceId == COURIER){
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_courier_types as lct', 'lct.id', '=', 'bqi.lkp_courier_type_id' );
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_courier_delivery_types as lcdt', 'lcdt.id', '=', 'bqi.lkp_courier_delivery_type_id' );
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_courier_purposes as lcps', 'lcps.id', '=', 'bqi.lkp_courier_purpose_id' );
			}
            $getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_ict_weight_uom as liwu', 'liwu.id', '=', 'bqi.lkp_ict_weight_uom_id' );

			if (! empty ( $buyerQuoteId )) {
				$getBuyerQuoteSellersQuotesPricesQuery->where ( 'bqi.buyer_quote_id', $buyerQuoteId );
			}
			$getBuyerQuoteSellersQuotesPricesQuery->where( 'bqi.created_by', Auth::User ()->id );

            
            if($serviceId != COURIER){
                $getBuyerQuoteSellersQuotesPricesQuery->select('ldt.load_type', 'bqi.calculated_volume_weight', 
                                                            'bqi.units as buyerQuoteUnits', 
                                                            'liwu.weight_type', 
                                                            'bq.lkp_quote_access_id', 'liwu.id as weightTypeId', 
                                                            'lpt.packaging_type_name', 'bqi.buyer_quote_id','u.username',
                                                            'bqi.lkp_post_status_id','lps.post_status', 'bqi.number_packages');
          
			}else{
			$getBuyerQuoteSellersQuotesPricesQuery->select( 'lct.courier_type','lcdt.courier_delivery_type','lcps.courier_purpose','bqi.calculated_volume_weight','bqi.units as buyerQuoteUnits',
					'liwu.weight_type','bq.lkp_quote_access_id', 'liwu.id as weightTypeId','bqi.buyer_quote_id','u.username',
					'bqi.lkp_post_status_id','bqi.lkp_courier_purpose_id','lps.post_status', 'bqi.number_packages', 'bqi.package_value');
			}

			$arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
            return $arrayBuyerQuoteSellersQuotesPrices;
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}
    /**
    * Buyer counter offer Page
    * Method to retrieve seller lists
    * @param int $buyerQuoteItemId
    * @return array
    */
	public static function getSellerIds($buyerQuoteId) {
		try {
			Log::info ('Get seller lists for the district: ' . Auth::id (), array ('c' => '2'));
			$serviceId = Session::get('service_id');

			switch($serviceId){
                            
                            case ROAD_PTL:
                        $sellerIds = DB::table('ptl_buyer_quote_selected_sellers as bqss')
			->where('bqss.buyer_quote_id',$buyerQuoteId)
			->select('bqss.seller_id')
			->get();
                                break;
                        case RAIL:   
                            $sellerIds = DB::table('rail_buyer_quote_selected_sellers as bqss')
			->where('bqss.buyer_quote_id',$buyerQuoteId)
			->select('bqss.seller_id')
			->get();
                                break;
                            case AIR_DOMESTIC:   
                            $sellerIds = DB::table('airdom_buyer_quote_selected_sellers as bqss')
			->where('bqss.buyer_quote_id',$buyerQuoteId)
			->select('bqss.seller_id')
			->get();
                                break;
                            case AIR_INTERNATIONAL:   
                            $sellerIds = DB::table('airint_buyer_quote_selected_sellers as bqss')
			->where('bqss.buyer_quote_id',$buyerQuoteId)
			->select('bqss.seller_id')
			->get();
                                break;
                            case OCEAN:   
                            $sellerIds = DB::table('ocean_buyer_quote_selected_sellers as bqss')
			->where('bqss.buyer_quote_id',$buyerQuoteId)
			->select('bqss.seller_id')
			->get();
                                break;
                        case COURIER:   
                            $sellerIds = DB::table('courier_buyer_quote_selected_sellers as bqss')
            ->where('bqss.buyer_quote_id',$buyerQuoteId)
            ->select('bqss.seller_id')
            ->get();
                                break;
                            default:
                        $sellerIds = DB::table('ptl_buyer_quote_selected_sellers as bqss')
			->where('bqss.buyer_quote_id',$buyerQuoteId)
			->select('bqss.seller_id')
			->get();
                                break;
                        }
			$arraySellerIds = [];
			if(isset($sellerIds) && !empty($sellerIds)){
				foreach($sellerIds as $sellerId){
					array_push($arraySellerIds, $sellerId->seller_id);
				}
			}
			return $arraySellerIds;
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}
}
