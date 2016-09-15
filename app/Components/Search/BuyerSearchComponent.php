<?php
namespace App\Components\Search;
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
use App\Models\PtlSearchTerm;
use App\Components\SellerComponent;
use App\Components\BuyerComponent;
use App\Components\Search\SellerSearchComponent;
use Log;

class BuyersearchComponent {
	public static function search($roleId, $serviceId,$statusId,$params){
		
		$monolog = \Log::getMonolog();
		$monolog->pushHandler(new \Monolog\Handler\FirePHPHandler());
		$monolog->addInfo('Buyer Search Request Params', array('Buyer Search Request Params' => $params,'c'=>1));

		switch ($serviceId) {
			case ROAD_FTL       :
				$queryBuilder = BuyersearchComponent::getFtlBuyerSearchResults(OPEN,$params);
				break;
			case ROAD_PTL:
			case RAIL:
			case AIR_DOMESTIC:
			case COURIER:
				$queryBuilder = BuyersearchComponent::getPtlBuyerSearchResults(OPEN,$params,$serviceId);
				break;
			case AIR_INTERNATIONAL:
			case OCEAN:
				$queryBuilder = BuyersearchComponent::getAirIntAndOceanSearchResults(OPEN,$params,$serviceId);
				break;
			case ROAD_INTRACITY :
				$queryBuilder = BuyersearchComponent::getIntracityBuyerSearchResults(OPEN,$params);
				break;
			case ROAD_TRUCK_LEASE:
				$queryBuilder = BuyersearchComponent::getTruckLeaseBuyerSearchResults(OPEN,$params);
				break;
			case ROAD_TRUCK_HAUL:
				$queryBuilder = BuyersearchComponent::getTruckHaulBuyerSearchResults(OPEN,$params);
				break;
			case RELOCATION_DOMESTIC:
				$queryBuilder = BuyersearchComponent::getRelocationBuyerSearchResults(OPEN,$params);
				break;
			case RELOCATION_OFFICE_MOVE:
		            $queryBuilder = BuyersearchComponent::getRelocationOfficeBuyerSearchResults(OPEN,$params);
		            break;
		    case RELOCATION_PET_MOVE:
		            $queryBuilder = BuyersearchComponent::getRelocationPetBuyerSearchResults(OPEN,$params);
		             break;
		    case RELOCATION_INTERNATIONAL:
		    	if($params['post_type']==1):
		        	$queryBuilder = BuyersearchComponent::getRelocationAirIntBuyerSearchResults(OPEN,$params);
		        else:
		        	$queryBuilder = BuyersearchComponent::getRelocationOceanIntBuyerSearchResults(OPEN,$params);
		        endif;
		        break;
                        case RELOCATION_GLOBAL_MOBILITY:
				$queryBuilder = BuyersearchComponent::getRelocationGmBuyerSearchResults(OPEN,$params);
				break;
			default:
				break;
		}

		if($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC || $serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN  || $serviceId == COURIER){
			$results = $queryBuilder;
			$sqlquery = "mutl queries";
			$bindings = "mutl binding";
		}else{
			$results = $queryBuilder->get();
			$sqlquery = $queryBuilder->tosql();
			$bindings = $queryBuilder->getBindings();
		}
		
		
		$monolog->addInfo('Buyer Search query', array('Buyer search query' => $sqlquery,'c'=>1));
		$monolog->addInfo('Buyer Search results', array('Buyer search results' => $results,'c'=>1));
		$monolog->addInfo('Buyer Search binding', array('Buyer search binding' => $bindings,'c'=>1));
		
		
		/*echo "<pre>";print_R($params);
		print_r($queryBuilder->getBindings());
		print_r($queryBuilder->get());
		echo $queryBuilder->tosql();die;*/
		return $queryBuilder;
	}
	/**
	 * FTL Buyer Search
	 * @param $params
	 * result $query
	 */
	public static function getFtlBuyerSearchResults($statusId,$params){
		$loginId = Auth::User()->id;
		//print_R($params);//exit;
		// Below script for buyer search for seller posts join query --for Grid
		$gridBuyer = DB::table ( 'seller_post_items as sqi' );
		$gridBuyer->join ( 'lkp_load_types as lt', 'lt.id', '=', 'sqi.lkp_load_type_id' );
		$gridBuyer->join ( 'lkp_vehicle_types as vt', 'vt.id', '=', 'sqi.lkp_vehicle_type_id' );
		$gridBuyer->join ( 'lkp_cities as cf', 'sqi.from_location_id', '=', 'cf.id' );
		$gridBuyer->join ( 'lkp_cities as ct', 'sqi.to_location_id', '=', 'ct.id' );
		$gridBuyer->join ( 'seller_posts as sp', 'sp.id', '=', 'sqi.seller_post_id' );
		$gridBuyer->join ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
		$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
		$gridBuyer->leftjoin ( 'seller_selected_buyers as ssb', 'ssb.seller_post_id', '=', 'sp.id' );
        $gridBuyer->leftjoin ( 'buyer_quote_sellers_quotes_prices as bqsp', 'bqsp.seller_post_item_id', '=', 'sqi.id' );
		$gridBuyer->where('sp.lkp_post_status_id', '=', $statusId);
		$gridBuyer->where('sqi.lkp_post_status_id', '=', $statusId);
		$gridBuyer->where('sqi.is_private', '=', 0);

		// set from location below varaibles are checking empty or not varaible in buyear search---grid
		if (isset ( $params ['from_location_id'] ) && $params ['from_location_id'] != '') {
			$gridBuyer->where ( 'sqi.from_location_id', $params ['from_location_id'] );
		}
		// set to location
		if (isset ( $params ['to_location_id'] ) && $params ['to_location_id'] != '') {
			$gridBuyer->where ( 'sqi.to_location_id', $params ['to_location_id'] );
		}

		//set district
		if(isset($params['district']) && $params['district']!=''){
			$gridBuyer->where('cf.lkp_district_id',$params['district']);
		}

		// set load type
		if (isset ( $params ['lkp_load_type_id'] ) && ($params ['lkp_load_type_id'] != '') && ($params ['lkp_load_type_id'] != LOADTYPE_ALL)) {
			$loadtypeid = $params ['lkp_load_type_id'];
			$gridBuyer->whereRaw ( "( IF(`sqi`.`lkp_load_type_id` != 11, `sqi`.`lkp_load_type_id` = $loadtypeid,TRUE )  )");
		}
		// set vehicle type
		if (isset ( $params ['lkp_vehicle_type_id'] ) && ($params ['lkp_vehicle_type_id'] != '') && ($params ['lkp_vehicle_type_id'] != VEHICLETYPE_ALL)) {
			$vehicletypeid = $params ['lkp_vehicle_type_id'];
			$gridBuyer->whereRaw ( "( IF(`sqi`.`lkp_vehicle_type_id` != 20, `sqi`.`lkp_vehicle_type_id` = $vehicletypeid,TRUE )  )");
		}
		// set tracking filter milestoness
		if (isset ( $params ['trackingfilter'] ) && ($params ['trackingfilter'] != '') && is_array($params['trackingfilter']) && !empty($params ['trackingfilter']) ) {
			$gridBuyer->WhereIn ( 'sp.tracking', $params ['trackingfilter'] );
		}
		
		
		//price filter
		// set tracking2 filter realt time
		if (isset ( $params ['price'] ) && $params ['price'] != '') {

			$splitprice = explode("    ",$params ['price']);
			$from = trim(filter_var($splitprice[0],FILTER_SANITIZE_NUMBER_INT),"-");
			$to = trim(filter_var($splitprice[1],FILTER_SANITIZE_NUMBER_INT),"-");
           	$_REQUEST['price_from'] = $from;
			$_REQUEST['price_to'] = $to;
           	
           	//$from = trim($params['price_from']);
			//$to = trim($params['price_to']);

			//added by swathi for price filter changes
            $qty = session('searchMod.quantity_buyer');
            $gridBuyer->whereRaw ( " (case when `vt`.`units` != 'KG' then `sqi`.`price` >= $from/(CEIL('$qty'/ `vt`.`capacity` ) ) when `vt`.`units` = 'KG' then `sqi`.`price` >= $from/(CEIL('$qty'*1000/ `vt`.`capacity` ) ) end ) " );
            $gridBuyer->whereRaw ( " (case when `vt`.`units` != 'KG' then `sqi`.`price` <= $to/(CEIL('$qty'/ `vt`.`capacity` ) ) when `vt`.`units` = 'KG' then `sqi`.`price` <= $to/(CEIL('$qty'*1000/ `vt`.`capacity` ) ) end) " );
		}

		//dispatch dates
		if(isset($params['from_date']) && $params['from_date']!=''){
			$dispatchflexibleflag = isset($params['is_dispatch_flexible']) ? $params['is_dispatch_flexible'] : 0;
			if(isset($params['date_flexiable']) &&  $params['date_flexiable'] != ''){
				$dispatchsame = CommonComponent::convertDateForDatabase($params['date_flexiable']);
			}else{
				$dispatchsame = CommonComponent::convertDateForDatabase($params['from_date']);
			}
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " + 3 day"));
			$currentdate = date('Y-m-d');
			$dispatch_minus  = ($dispatch_minus >= $currentdate) ? $dispatch_minus : $currentdate;


			if ($dispatchflexibleflag == 1 && (isset($params['date_flexiable']) && $params['date_flexiable']=="")) {
				$gridBuyer->whereRaw ( "( ('$dispatch_minus' between `sp`.`from_date` and `sp`.`to_date`) or  ('$dispatch_plus' between `sp`.`from_date` and `sp`.`to_date`)   )") ;
			}else{
				$gridBuyer->whereRaw ( "( '$dispatchsame' between `sp`.`from_date` and `sp`.`to_date`   )") ;
				//$gridBuyer->whereRaw ( "( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `bqi`.`dispatch_date` - INTERVAL 3 DAY and `bqi`.`dispatch_date` + INTERVAL 3 DAY  )  )" );
			}

		}
		if(isset($params['from_date']) && ($params['from_date']!='') && isset($params['to_date']) && ($params['to_date']!='')){
			$deliveryflexibleflag = isset($params['is_delivery_flexible']) ? $params['is_delivery_flexible'] : 0;

			$deliverysame = CommonComponent::convertDateForDatabase($params['to_date']);

			$daysdiff = strtotime($deliverysame) - strtotime($dispatchsame);
			$daysdiff = floor($daysdiff/(60*60*24))+1;
			$daysdiff = ($dispatchflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
			$daysdiff = ($deliveryflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
			$monthsdiff = $daysdiff / 7;
			$gridBuyer->whereRaw ( "( IF(`sqi`.`units` = 'Days', `sqi`.`transitdays` <= '$daysdiff', `sqi`.`transitdays` <= '$monthsdiff'  )  )" );
		}

		//Checking private sellers
		$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_access_id` = 2, `ssb`.`buyer_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			//$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$selectedSellers =$params['selected_users'];
                    $gridBuyer->WhereIn ( 'seller_user.id', $selectedSellers );
		}

		//selected payments
		if(isset($params['selected_payments']) && $params['selected_payments']!='') {
			$selectedPayments =$params['selected_payments'];
			$gridBuyer->WhereIn ( 'sp.lkp_payment_mode_id', $selectedPayments );
		}
		$gridBuyer->select ('sp.transaction_id', 'sqi.id', 'sqi.transitdays', 'sqi.units as transitdaysunits', 'sqi.from_location_id', 'sqi.to_location_id', 'sqi.lkp_load_type_id', 'sqi.lkp_vehicle_type_id', 'sqi.created_by', 'lt.load_type', 'vt.vehicle_type', 'cf.city_name as fromcity', 'ct.city_name as tocity', 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price', 'seller_user.username','seller_user.id as seller_id', 'sp.from_date', 'sp.to_date', 'sp.tracking','sp.lkp_payment_mode_id', 'pm.payment_mode as paymentmethod', 'sqi.created_by', 'sp.cancellation_charge_text', 'sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text', 'sp.docket_charge_price','vt.capacity','vt.units' ,
        
        DB::raw("(case when `bqsp`.`final_transit_days` != 0 then bqsp.final_transit_days  when `bqsp`.`initial_transit_days` != 0 then bqsp.initial_transit_days when 'bqsp.id'=0 then sqi.transitdays end) as transitdays") );
		$gridBuyer->groupBy('sqi.id');
		
		return $gridBuyer;
	}


	/**
	 * PTL Buyer Search
	 * @param $params
	 * result $query
	 */
	public static function getPtlBuyerSearchResults($statusId,$params,$serviceId){
		$loginId = Auth::User()->id;
		// Below script for buyer search for seller posts join query --for Grid
		//echo "<pre>";print_R($params);exit;
		$result = array();
		$no_of_locations = count($params['ptlDispatchDate']);
        $sellerpostids = array();
		for($i=0;$i<$no_of_locations;$i++){
			
			$ptlFromLocation = isset($params['ptlFromLocation'][$i]) ? $params['ptlFromLocation'][$i] : "";
			//echo $ptlFromLocation;die;
			$ptlToLocation = isset($params['ptlToLocation'][$i]) ? $params['ptlToLocation'][$i] : "";
			$ptlDispatchDate = isset($params['ptlDispatchDate'][$i]) ? $params['ptlDispatchDate'][$i] : "";
			$ptlDeliveryDate = isset($params['ptlDeliveryhDate'][$i]) ? $params['ptlDeliveryhDate'][$i] : "";
			$ptlDistrict = isset($params['district'][$i]) ? $params['district'][$i] : "";

			$ptlFromPincodeText =  DB::table('lkp_ptl_pincodes')->where('id', $ptlFromLocation)->pluck('pincode');
			if($serviceId == COURIER){
				if(isset($params['post_delivery_types'][$i]) && $params['post_delivery_types'][$i] == IS_DOMESTIC){
					$ptlToPincodeText =  DB::table('lkp_ptl_pincodes')->where('id', $ptlToLocation)->pluck('pincode');
				}elseif(isset($params['post_delivery_types'][$i]) && $params['post_delivery_types'][$i] == IS_INTERNATIONAL){
					$ptlToPincodeText =  'cnt.country_name';
				}
			}
			else{
				$ptlToPincodeText =  DB::table('lkp_ptl_pincodes')->where('id', $ptlToLocation)->pluck('pincode');
			}
			if($serviceId == ROAD_PTL){
				$gridBuyer = DB::table ( 'ptl_seller_post_items as sqi' );
				$gridBuyer->leftjoin ( 'lkp_ptl_pincodes as cf', 'sqi.from_location_id', '=', 'cf.id' );
				$gridBuyer->leftjoin ( 'lkp_ptl_pincodes as ct', 'sqi.to_location_id', '=', 'ct.id' );				
				$gridBuyer->join ( 'ptl_seller_posts as sp', 'sp.id', '=', 'sqi.seller_post_id' );
				$gridBuyer->join ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
				$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
				$gridBuyer->leftjoin ( 'ptl_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_item_id', '=', 'sqi.id' );
				$gridBuyer->leftjoin ( 'ptl_seller_sellected_buyers as pssb', 'pssb.seller_post_id', '=', 'sp.id' );
			}else if($serviceId == RAIL){
				$gridBuyer = DB::table ( 'rail_seller_post_items as sqi' );
				$gridBuyer->leftjoin ( 'lkp_ptl_pincodes as cf', 'sqi.from_location_id', '=', 'cf.id' );
				$gridBuyer->leftjoin ( 'lkp_ptl_pincodes as ct', 'sqi.to_location_id', '=', 'ct.id' );
				$gridBuyer->join ( 'rail_seller_posts as sp', 'sp.id', '=', 'sqi.seller_post_id' );
				$gridBuyer->join ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
				$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
				$gridBuyer->leftjoin ( 'rail_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_item_id', '=', 'sqi.id' );
				$gridBuyer->leftjoin ( 'rail_seller_sellected_buyers as pssb', 'pssb.seller_post_id', '=', 'sp.id' );
			}else if($serviceId == AIR_DOMESTIC){
				$gridBuyer = DB::table ( 'airdom_seller_post_items as sqi' );
				$gridBuyer->leftjoin ( 'lkp_ptl_pincodes as cf', 'sqi.from_location_id', '=', 'cf.id' );
				$gridBuyer->leftjoin ( 'lkp_ptl_pincodes as ct', 'sqi.to_location_id', '=', 'ct.id' );
				$gridBuyer->join ( 'airdom_seller_posts as sp', 'sp.id', '=', 'sqi.seller_post_id' );
				$gridBuyer->join ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
				$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
				$gridBuyer->leftjoin ( 'airdom_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_item_id', '=', 'sqi.id' );
				$gridBuyer->leftjoin ( 'airdom_seller_sellected_buyers as pssb', 'pssb.seller_post_id', '=', 'sp.id' );
			}else if($serviceId == COURIER){
				$gridBuyer = DB::table ( 'courier_seller_post_items as sqi' );
				$gridBuyer->leftjoin ( 'lkp_ptl_pincodes as cf', 'sqi.from_location_id', '=', 'cf.id' );
				if(isset($params['post_delivery_types'][$i]) && $params['post_delivery_types'][$i] == IS_DOMESTIC){
					$gridBuyer->leftjoin ( 'lkp_ptl_pincodes as ct', 'sqi.to_location_id', '=', 'ct.id' );
				}elseif(isset($params['post_delivery_types'][$i]) && $params['post_delivery_types'][$i] == IS_INTERNATIONAL){
					$gridBuyer->leftjoin ( 'lkp_countries as cnt', 'sqi.to_location_id', '=', 'cnt.id' );
				}
				$gridBuyer->join ( 'courier_seller_posts as sp', 'sp.id', '=', 'sqi.seller_post_id' );
				$gridBuyer->join ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
				$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
				$gridBuyer->leftjoin ( 'courier_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_item_id', '=', 'sqi.id' );
				$gridBuyer->leftjoin ( 'courier_seller_sellected_buyers as pssb', 'pssb.seller_post_id', '=', 'sp.id' );

                //added for slab values
                $gridBuyer->leftjoin ( 'courier_seller_post_item_slabs as spis', 'spis.seller_post_id', '=', 'sp.id' );
                
				
				if(isset($params['ptlUnitsWeight'][$i]) && isset($params['packeagevalue'][$i])){
					$gridBuyer->where('sp.max_weight_accepted', '>=', $params['ptlUnitsWeight'][$i]);
					$gridBuyer->where('sp.maximum_value', '>=', $params['packeagevalue'][$i]);
				}
				if(isset($params['courier_types'][$i])){
					$gridBuyer->where('sp.lkp_courier_type_id', '=', $params['courier_types'][$i]);
				}

			}

			$gridBuyer->where('sp.lkp_post_status_id', '=', $statusId);
			$gridBuyer->where('sqi.lkp_post_status_id', '=', $statusId);
			$gridBuyer->where('sqi.is_private', '=', 0);

			// set from location below varaibles are checking empty or not varaible in buyear search---grid
			if (isset ( $ptlFromLocation ) && !empty($ptlFromLocation)) {
				$fromlocations = BuyersearchComponent::getZonePincodesByPincode($ptlFromLocation);
				$allRelatedFromPinIds = SellerSearchComponent::getAllPincodeIds($ptlFromLocation);
				$allRelatedFromPinIdsComma = implode(",",$allRelatedFromPinIds);
                $fromlocationzones = implode(",",$fromlocations['zones']);
				if($fromlocationzones != ''){
					$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_ptl_post_type_id` = 1, `sqi`.`from_location_id` in ($fromlocationzones), `sqi`.`from_location_id` in ($allRelatedFromPinIdsComma)  )  )");
				}else{
					$gridBuyer->whereIn ( 'sqi.from_location_id', $allRelatedFromPinIds );
				}
				//
			}

			// set to location
			if (isset ( $ptlToLocation ) && !empty($ptlToLocation)) {
				if(isset($_POST['sea_post_delivery_types']) &&  $_POST['sea_post_delivery_types'] == 2){
					$gridBuyer->Where ( 'sqi.to_location_id', $ptlToLocation );
				}else{
					$tolocations = BuyersearchComponent::getZonePincodesByPincode($ptlToLocation);
					$tolocationzones = implode(",",$tolocations['zones']);

					$allRelatedToPinIds = SellerSearchComponent::getAllPincodeIds($ptlToLocation);
					$allRelatedToPinIdsComma = implode(",",$allRelatedToPinIds);
					if($tolocationzones != ''){
						$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_ptl_post_type_id` = 1, `sqi`.`to_location_id` in ($tolocationzones), `sqi`.`to_location_id` in ($allRelatedToPinIdsComma) )  )");
					}else{
						$gridBuyer->WhereIn ( 'sqi.to_location_id', $allRelatedToPinIds );
					}
				}

			}

			// set to district
			if (isset ( $ptlDistrict ) && !empty($ptlDistrict)) {
				$gridBuyer->Where ( 'sqi.lkp_district_id', $ptlDistrict );
			}
//echo "<pre>";print_r($params);exit;
			if($serviceId!=COURIER){
			//dispatch dates
				
				$dispatchflexibleflag = isset($params['ptlFlexiableDispatch'][$i]) ? $params['ptlFlexiableDispatch'][$i] : 0;
				if (isset ( $ptlDispatchDate ) && !empty($ptlDispatchDate)) {
					
					/*if(isset($params['date_flexiable'][$i]) &&  $params['date_flexiable'][$i] != ''){
						$dispatchsame = CommonComponent::convertDateForDatabase($params['date_flexiable'][$i]);
					}else{*/
						$dispatchsame = CommonComponent::convertDateForDatabase($ptlDispatchDate);
					//}
					//exit;
					$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$ptlDispatchDate) . " - 3 day"));
					$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$ptlDispatchDate) . " + 3 day"));
	
					$currentdate = date('Y-m-d');
					$dispatch_minus  = ($dispatch_minus >= $currentdate) ? $dispatch_minus : $currentdate;
	
					if ($dispatchflexibleflag == 1 && isset($_REQUEST['date_flexiable']) && $_REQUEST['date_flexiable']=="") {
						$gridBuyer->whereRaw ( "( ('$dispatch_minus' between `sp`.`from_date` and `sp`.`to_date`) or  ('$dispatch_plus' between `sp`.`from_date` and `sp`.`to_date`)   )") ;
					}else{
						$gridBuyer->whereRaw ( "( '$dispatchsame' between `sp`.`from_date` and `sp`.`to_date`   )") ;
						//$gridBuyer->whereRaw ( "( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `bqi`.`dispatch_date` - INTERVAL 3 DAY and `bqi`.`dispatch_date` + INTERVAL 3 DAY  )  )" );
					}
				}
				$deliveryflexibleflag = isset($params['ptlFlexiableDelivery'][$i]) ? $params['ptlFlexiableDelivery'][$i] : 0;
				if (isset ( $ptlDispatchDate ) && !empty($ptlDispatchDate) && isset ( $ptlDeliveryDate ) && !empty($ptlDeliveryDate)) {
					$deliverysame = CommonComponent::convertDateForDatabase($ptlDeliveryDate);
	
					$daysdiff = strtotime($deliverysame) - strtotime($dispatchsame);
					$daysdiff = floor($daysdiff/(60*60*24))+1;
					$daysdiff = ($dispatchflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
					$daysdiff = ($deliveryflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
					$monthsdiff = $daysdiff / 7;
					$gridBuyer->whereRaw ( "( IF(`sqi`.`units` = 'Days', `sqi`.`transitdays` <= '$daysdiff', `sqi`.`transitdays` <= '$monthsdiff'  )  )" );
				}
			}
			// set to tracking
			if (isset ( $params ['tracking'] ) && !empty($params ['tracking']) && sizeof($params ['tracking']) > 0) {
				$gridBuyer->WhereIn ( 'sp.tracking', $params ['tracking'] );
			}

			//max weight
			if($serviceId == COURIER){
				if (isset($params['courier_max_weight']) && $params['courier_max_weight'] != '') {
					$weight = $params['courier_max_weight'];
					$weightgms = $params['courier_max_weight'] * 1000;
					$weightmts = $params['courier_max_weight'] * 0.001;
					$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_ict_weight_uom_id` = 1, `sp`.`max_weight_accepted` >= '$weight',
							(IF(`sp`.`lkp_ict_weight_uom_id` = 2, `sp`.`max_weight_accepted` >= '$weightgms',`sp`.`max_weight_accepted` >= '$weightmts'))
					)  )" );
				}
				}
			//Checking private sellers
			$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_access_id` = 2, `pssb`.`buyer_id` =  $loginId,TRUE )  )");
			//Sellers filter
			if(isset($params['selected_users']) && $params['selected_users']!='') {
				$selectedSellers = $params['selected_users'];
				$gridBuyer->WhereIn ( 'seller_user.id', $selectedSellers );
			}

			//selected payments
			if(isset($params['selected_payments']) && $params['selected_payments']!='') {
				$selectedPayments =$params['selected_payments'];
				$gridBuyer->WhereIn ( 'sp.lkp_payment_mode_id', $selectedPayments );
			}
			
        	
			$key = $ptlFromLocation ."_". $ptlToLocation;
			
			
			$strToLocation = "cf.postoffice_name as frompostoffice_name, ct.postoffice_name as topostoffice_name";
			if($serviceId == COURIER){
				if(isset($params['post_delivery_types'][$i]) && $params['post_delivery_types'][$i] == IS_DOMESTIC){
					$strToLocation = "cf.postoffice_name as frompostoffice_name, ct.postoffice_name as topostoffice_name";
				}elseif(isset($params['post_delivery_types'][$i]) && $params['post_delivery_types'][$i] == IS_INTERNATIONAL){
					$strToLocation = "cf.postoffice_name as frompostoffice_name, cnt.country_name as topostoffice_name";
				}
			}else{
				$strToLocation = "cf.postoffice_name as frompostoffice_name, ct.postoffice_name as topostoffice_name";
			}
			if(!empty($ptlFromPincodeText)){
				$gridBuyer->select ( 'sp.transaction_id');
                            if(!empty($ptlToPincodeText)){
                            	if($serviceId == COURIER){                            		
                                    $gridBuyer->select ( 'sp.transaction_id','sqi.id','seller_user.id as seller_id', 'sp.conversion_factor as kg_per_cft',DB::raw( "'$key' as matchwith"),DB::raw($ptlFromPincodeText .' as frompincode'),DB::raw($ptlToPincodeText .' as topincode'),$strToLocation, 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units',DB::raw($ptlFromLocation .' as from_location_id'),DB::raw($ptlToLocation .' as to_location_id'), 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking','sp.lkp_payment_mode_id', 'pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.freight_collect_charge as pickup_charges','sp.cod_charge as delivery_charges','sp.fuel_surcharge as oda_charges',DB::raw($ptlFromPincodeText .' as search_from_pincode'),DB::raw($ptlToPincodeText .' as search_to_pincode'),'sp.docket_charge_price', 'sp.cancellation_charge_text', 'sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text', 'sp.docket_charge_price', 'sp.fuel_surcharge', 'sp.cod_charge', 'sp.freight_collect_charge', 'sp.arc_charge' , 'sp.lkp_payment_mode_id', 'sp.max_weight_accepted', 'sp.is_incremental', 'sp.increment_weight', 'sp.rate_per_increment', 'sp.maximum_value',
                                            DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );

                                }else{
                                        $gridBuyer->select ( 'sp.transaction_id','sqi.id','seller_user.id as seller_id', 'sp.kg_per_cft',DB::raw( "'$key' as matchwith"),DB::raw($ptlFromPincodeText .' as frompincode'),DB::raw($ptlToPincodeText .' as topincode'),$strToLocation, 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units',DB::raw($ptlFromLocation .' as from_location_id'),DB::raw($ptlToLocation .' as to_location_id'), 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking','sp.lkp_payment_mode_id', 'pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.pickup_charges','sp.delivery_charges','sp.oda_charges',DB::raw($ptlFromPincodeText .' as search_from_pincode'),DB::raw($ptlToPincodeText .' as search_to_pincode'),'sp.docket_charge_price', 'sp.cancellation_charge_text', 'sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text', 'sp.docket_charge_price',
                                                 DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
                                }
                            }else{
                            	if($serviceId == COURIER){
                            		$gridBuyer->select ( 'sp.transaction_id','sqi.id','seller_user.id as seller_id', 'sp.conversion_factor as kg_per_cft',DB::raw( "'$key' as matchwith"),DB::raw($ptlFromPincodeText .' as frompincode'),$strToLocation, 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units',DB::raw($ptlFromLocation .' as from_location_id'), 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking','sp.lkp_payment_mode_id', 'pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.freight_collect_charge as pickup_charges','sp.cod_charge as delivery_charges','sp.fuel_surcharge as oda_charges',DB::raw($ptlFromPincodeText .' as search_from_pincode'),'sp.docket_charge_price', 'sp.cancellation_charge_text', 'sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text', 'sp.docket_charge_price','sp.fuel_surcharge','sp.cod_charge','sp.freight_collect_charge','sp.arc_charge', 'sp.lkp_payment_mode_id', 'sp.max_weight_accepted', 'sp.is_incremental', 'sp.increment_weight', 'sp.rate_per_increment', 'sp.maximum_value',
                                                 DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
                            	}else{
                            		$gridBuyer->select ( 'sp.transaction_id','sqi.id','seller_user.id as seller_id', 'sp.kg_per_cft',DB::raw( "'$key' as matchwith"),DB::raw($ptlFromPincodeText .' as frompincode'),$strToLocation, 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units',DB::raw($ptlFromLocation .' as from_location_id'), 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking','sp.lkp_payment_mode_id', 'pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.pickup_charges','sp.delivery_charges','sp.oda_charges',DB::raw($ptlFromPincodeText .' as search_from_pincode'),'sp.docket_charge_price', 'sp.cancellation_charge_text', 'sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text', 'sp.docket_charge_price',
                                                 DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
                            	}
							}
                           
			} elseif(!empty($ptlToPincodeText)){
				$gridBuyer->select ( 'sp.transaction_id');
					if($serviceId == COURIER){
                    $gridBuyer->select ( 'sp.transaction_id','sqi.id','seller_user.id as seller_id', 'sp.conversion_factor as kg_per_cft',DB::raw( "'$key' as matchwith"),DB::raw($ptlToPincodeText .' as topincode'),$strToLocation, 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units',DB::raw($ptlFromLocation .' as from_location_id'),DB::raw($ptlToLocation .' as to_location_id'), 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking','sp.lkp_payment_mode_id', 'pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.freight_collect_charge as pickup_charges','sp.cod_charge as delivery_charges','sp.fuel_surcharge as oda_charges',DB::raw($ptlFromPincodeText .' as search_from_pincode'),DB::raw($ptlToPincodeText .' as search_to_pincode'),'sp.docket_charge_price', 'sp.cancellation_charge_text', 'sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text', 'sp.docket_charge_price','sp.fuel_surcharge','sp.cod_charge','sp.freight_collect_charge','sp.arc_charge', 'sp.lkp_payment_mode_id', 'sp.max_weight_accepted', 'sp.is_incremental', 'sp.increment_weight', 'sp.rate_per_increment', 'sp.maximum_value',
                             DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
                    }else{
                    $gridBuyer->select ( 'sp.transaction_id','sqi.id','seller_user.id as seller_id', 'sp.kg_per_cft',DB::raw( "'$key' as matchwith"),DB::raw($ptlToPincodeText .' as topincode'),$strToLocation, 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units',DB::raw($ptlFromLocation .' as from_location_id'),DB::raw($ptlToLocation .' as to_location_id'), 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking','sp.lkp_payment_mode_id', 'pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.pickup_charges','sp.delivery_charges','sp.oda_charges',DB::raw($ptlFromPincodeText .' as search_from_pincode'),DB::raw($ptlToPincodeText .' as search_to_pincode'),'sp.docket_charge_price', 'sp.cancellation_charge_text', 'sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text', 'sp.docket_charge_price',
                             DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
                    }
                }else{
                	if($serviceId == COURIER){
						$gridBuyer->select ('sp.transaction_id', 'sqi.id','seller_user.id as seller_id', 'sp.conversion_factor as kg_per_cft',$strToLocation, 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units', 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking','sp.lkp_payment_mode_id', 'pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.freight_collect_charge as pickup_charges','sp.cod_charge as delivery_charges','sp.fuel_surcharge as oda_charges','sp.docket_charge_price', 'sp.cancellation_charge_text', 'sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text', 'sp.docket_charge_price','sp.fuel_surcharge','sp.cod_charge','sp.freight_collect_charge','sp.arc_charge', 'sp.lkp_payment_mode_id', 'sp.max_weight_accepted', 'sp.is_incremental', 'sp.increment_weight', 'sp.rate_per_increment', 'sp.maximum_value',
                                                         DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
					}else{
						$gridBuyer->select ('sp.transaction_id', 'sqi.id','seller_user.id as seller_id', 'sp.kg_per_cft',$strToLocation, 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units', 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking','sp.lkp_payment_mode_id', 'pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.pickup_charges','sp.delivery_charges','sp.oda_charges','sp.docket_charge_price', 'sp.cancellation_charge_text', 'sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text', 'sp.docket_charge_price',
                                                         DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
					}

			}

			$gridBuyer->groupBy('sqi.id');
			//echo "<pre>";
			//echo $gridBuyer->tosql();
			//print_R($gridBuyer->getBindings());
			$key = $ptlFromLocation ."_". $ptlToLocation;
			//$result["$key"] =$gridBuyer->get();
			foreach($gridBuyer->get() as $info){
                            if(!in_array($info->id, $sellerpostids)){
                                $sellerpostids[] = $info->id;
                                $result[]=$info;
                            }
			}
		}
        //echo "<pre>";print_R($result);die;
		
		return $result;
	}



	/**
	 * AirInternation and ocean Buyer Search
	 * @param $params
	 * result $query
	 */
	public static function getAirIntAndOceanSearchResults($statusId,$params,$serviceId){
		$loginId = Auth::User()->id;
		// Below script for buyer search for seller posts join query --for Grid

		$result = array();
		$no_of_locations = count($params['ptlFromLocation']);
		for($i=0;$i<$no_of_locations;$i++){
			//echo "<pre>";print_R($params);
			$ptlFromLocation = isset($params['ptlFromLocation'][$i]) ? $params['ptlFromLocation'][$i] : "";
			$ptlToLocation = isset($params['ptlToLocation'][$i]) ? $params['ptlToLocation'][$i] : "";
			$ptlDispatchDate = isset($params['ptlDispatchDate'][0]) ? $params['ptlDispatchDate'][0] : "";
			$ptlDeliveryDate = isset($params['ptlDeliveryhDate'][$i]) ? $params['ptlDeliveryhDate'][$i] : "";

			
			if($serviceId == AIR_INTERNATIONAL){
				$ptlFromPincodeText =  DB::table('lkp_airports')->where('id', $ptlFromLocation)->pluck('airport_name');
				$ptlToPincodeText =  DB::table('lkp_airports')->where('id', $ptlToLocation)->pluck('airport_name');
				$gridBuyer = DB::table ( 'airint_seller_post_items as sqi' );
				$gridBuyer->leftjoin ( 'lkp_airports as cf', 'sqi.from_location_id', '=', 'cf.id' );
				$gridBuyer->leftjoin ( 'lkp_airports as ct', 'sqi.to_location_id', '=', 'ct.id' );
				//$gridBuyer->leftjoin ( 'ptl_zones as fpz', 'sqi.from_location_id', '=', 'fpz.id' );
				//$gridBuyer->leftjoin ( 'ptl_zones as tpz', 'sqi.to_location_id', '=', 'tpz.id' );
				$gridBuyer->join ( 'airint_seller_posts as sp', 'sp.id', '=', 'sqi.seller_post_id' );
				$gridBuyer->join ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
				$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
				$gridBuyer->leftjoin ( 'airint_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_item_id', '=', 'sqi.id' );
				$gridBuyer->leftjoin ( 'airint_seller_sellected_buyers as pssb', 'pssb.seller_post_id', '=', 'sp.id' );
			}else if($serviceId == OCEAN){
				$ptlFromPincodeText =  DB::table('lkp_seaports')->where('id', $ptlFromLocation)->pluck('seaport_name');
				$ptlToPincodeText =  DB::table('lkp_seaports')->where('id', $ptlToLocation)->pluck('seaport_name');
				$gridBuyer = DB::table ( 'ocean_seller_post_items as sqi' );
				$gridBuyer->leftjoin ( 'lkp_seaports as cf', 'sqi.from_location_id', '=', 'cf.id' );
				$gridBuyer->leftjoin ( 'lkp_seaports as ct', 'sqi.to_location_id', '=', 'ct.id' );
				$gridBuyer->join ( 'ocean_seller_posts as sp', 'sp.id', '=', 'sqi.seller_post_id' );
				$gridBuyer->join ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
				$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
				$gridBuyer->leftjoin ( 'ocean_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_item_id', '=', 'sqi.id' );
				$gridBuyer->leftjoin ( 'ocean_seller_sellected_buyers as pssb', 'pssb.seller_post_id', '=', 'sp.id' );
			}
			$ptlFromPincodeText = e($ptlFromPincodeText);
			$ptlToPincodeText = e($ptlToPincodeText);
			$gridBuyer->where('sp.lkp_post_status_id', '=', $statusId);
			$gridBuyer->where('sqi.is_private', '=', 0);
			//$gridBuyer->where('sp.lkp_ptl_post_type_id', '=', 2);

			$gridBuyer->where('sqi.lkp_post_status_id', '=', $statusId);

			// set from location below varaibles are checking empty or not varaible in buyear search---grid
			if (isset ( $ptlFromLocation ) && !empty($ptlFromLocation)) {
				$fromlocations = BuyersearchComponent::getZonePincodesByPincode($ptlFromLocation);

				$fromlocationzones = implode(",",$fromlocations['zones']);
				if($fromlocationzones != ''){
					$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_ptl_post_type_id` = 1, `sqi`.`from_location_id` in ($fromlocationzones), `sqi`.`from_location_id` = '$ptlFromLocation'  )  )");
				}else{
					$gridBuyer->Where ( 'sqi.from_location_id', $ptlFromLocation );
				}
				//
			}

			// set to location
			if (isset ( $ptlToLocation ) && !empty($ptlToLocation)) {
				$tolocations = BuyersearchComponent::getZonePincodesByPincode($ptlToLocation);

				$tolocationzones = implode(",",$tolocations['zones']);
				if($tolocationzones != ''){
					$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_ptl_post_type_id` = 1, `sqi`.`to_location_id` in ($tolocationzones), `sqi`.`to_location_id` = '$ptlToLocation' )  )");
				}else{
					$gridBuyer->Where ( 'sqi.to_location_id', $ptlToLocation );
				}
			}


			//dispatch dates
			$dispatchflexibleflag = isset($params['ptlFlexiableDispatch'][$i]) ? $params['ptlFlexiableDispatch'][$i] : 0;
			if (isset ( $ptlDispatchDate ) && !empty($ptlDispatchDate)) {
				$dispatchsame = CommonComponent::convertDateForDatabase($ptlDispatchDate);
				$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$ptlDispatchDate) . " - 3 day"));
				$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$ptlDispatchDate) . " + 3 day"));

				$currentdate = date('Y-m-d');
				$dispatch_minus  = ($dispatch_minus >= $currentdate) ? $dispatch_minus : $currentdate;

				if ($dispatchflexibleflag == 1 && !isset($_REQUEST['date_flexiable']) ) {
					$gridBuyer->whereRaw ( "( ('$dispatch_minus' between `sp`.`from_date` and `sp`.`to_date`) or  ('$dispatch_plus' between `sp`.`from_date` and `sp`.`to_date`)   )") ;
				}else{
					$gridBuyer->whereRaw ( "( '$dispatchsame' between `sp`.`from_date` and `sp`.`to_date`   )") ;
					//$gridBuyer->whereRaw ( "( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `bqi`.`dispatch_date` - INTERVAL 3 DAY and `bqi`.`dispatch_date` + INTERVAL 3 DAY  )  )" );
				}
			}
			$deliveryflexibleflag = isset($params['ptlFlexiableDelivery'][$i]) ? $params['ptlFlexiableDelivery'][$i] : 0;
			if (isset ( $ptlDispatchDate ) && !empty($ptlDispatchDate) && isset ( $ptlDeliveryDate ) && !empty($ptlDeliveryDate)) {
				$deliverysame = CommonComponent::convertDateForDatabase($ptlDeliveryDate);

				$daysdiff = strtotime($deliverysame) - strtotime($dispatchsame);
				$daysdiff = floor($daysdiff/(60*60*24))+1;
				$daysdiff = ($dispatchflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
				$daysdiff = ($deliveryflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
				$monthsdiff = $daysdiff / 7;
				$gridBuyer->whereRaw ( "( IF(`sqi`.`units` = 'Days', `sqi`.`transitdays` <= '$daysdiff', `sqi`.`transitdays` <= '$monthsdiff'  )  )" );
			}

			// set to tracking
			if (isset ( $params ['tracking'] ) && !empty($params ['tracking']) && sizeof($params ['tracking']) > 0) {
				$gridBuyer->WhereIn ( 'sp.tracking', $params ['tracking'] );
			}
			
			//Checking private sellers
			$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_access_id` = 2, `pssb`.`buyer_id` =  $loginId,TRUE )  )");
			//Sellers filter
			if(isset($params['selected_users']) && $params['selected_users']!='') {
				$selectedSellers = $params['selected_users'];
                $gridBuyer->WhereIn ( 'seller_user.id', $selectedSellers );
			}
			//selected payments
			if(isset($params['selected_payments']) && $params['selected_payments']!='') {
				$selectedPayments =$params['selected_payments'];
				$gridBuyer->WhereIn ( 'sp.lkp_payment_mode_id', $selectedPayments );
			}
			$key = $ptlFromLocation ."_". $ptlToLocation;
			if($serviceId == AIR_INTERNATIONAL){
                            if(!empty($ptlFromPincodeText)){
                                if(!empty($ptlToPincodeText))
                                    $gridBuyer->select ('sp.transaction_id', 'cf.airport_name as frompostoffice_name', 'cf.airport_name as topostoffice_name','sqi.id','seller_user.id as seller_id', 'sp.kg_per_cft',DB::raw( "'$key' as matchwith"),DB::raw("'$ptlFromPincodeText' as frompincode"),DB::raw("'$ptlToPincodeText' as topincode"), 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units',DB::raw("'$ptlFromLocation' as from_location_id"),DB::raw($ptlToLocation.' as to_location_id'), 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking', 'sp.lkp_payment_mode_id','pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.pickup_charges','sp.delivery_charges','sp.oda_charges',DB::raw("'$ptlFromPincodeText' as search_from_pincode"),DB::raw("'$ptlToPincodeText' as search_to_pincode"),'sp.docket_charge_price','sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text',
                                         DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
                                else
                                    $gridBuyer->select ('sp.transaction_id', 'cf.airport_name as frompostoffice_name', 'cf.airport_name as topostoffice_name','sqi.id','seller_user.id as seller_id', 'sp.kg_per_cft',DB::raw( "'$key' as matchwith"),DB::raw("'$ptlFromPincodeText' as frompincode"), 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units',DB::raw("'$ptlFromLocation' as from_location_id"), 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking', 'sp.lkp_payment_mode_id','pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.pickup_charges','sp.delivery_charges','sp.oda_charges',DB::raw("'$ptlFromPincodeText' as search_from_pincode"),'sp.docket_charge_price','sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text',
                                         DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
                          
                            }elseif(!empty($ptlToPincodeText)){
                                    $gridBuyer->select ('sp.transaction_id', 'cf.airport_name as frompostoffice_name', 'cf.airport_name as topostoffice_name','sqi.id','seller_user.id as seller_id', 'sp.kg_per_cft',DB::raw( "'$key' as matchwith"),DB::raw("'$ptlToPincodeText' as topincode"), 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units',DB::raw("'$ptlToLocation' as to_location_id"), 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking', 'sp.lkp_payment_mode_id','pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.pickup_charges','sp.delivery_charges','sp.oda_charges',DB::raw("'$ptlToPincodeText' as search_to_pincode"),'sp.docket_charge_price','sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text',
                                             DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
                                
                            }else{
                                    $gridBuyer->select ( 'sp.transaction_id', 'cf.airport_name as frompostoffice_name', 'cf.airport_name as topostoffice_name','sqi.id','seller_user.id as seller_id', 'sp.kg_per_cft', 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units', 'sp.from_date', 'sp.to_date', 'sp.tracking', 'sp.lkp_payment_mode_id','pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.pickup_charges','sp.delivery_charges','sp.oda_charges','sp.docket_charge_price','sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text',
                                             DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays"));
                            }
                        }else{

                            if(!empty($ptlFromPincodeText)){
                                if(!empty($ptlToPincodeText))
                                    $gridBuyer->select ( 'sp.transaction_id', 'cf.seaport_name as frompostoffice_name', 'cf.seaport_name as topostoffice_name','sqi.id','seller_user.id as seller_id', 'sp.kg_per_cft',DB::raw( "'$key' as matchwith"),DB::raw("'$ptlFromPincodeText' as frompincode"),DB::raw("'$ptlToPincodeText' as topincode"), 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units',DB::raw($ptlFromLocation .' as from_location_id'),DB::raw($ptlToLocation .' as to_location_id'), 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking', 'sp.lkp_payment_mode_id','pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.pickup_charges','sp.delivery_charges','sp.oda_charges',DB::raw("'$ptlFromPincodeText ' as search_from_pincode"),DB::raw("'$ptlToPincodeText' as search_to_pincode"),'sp.docket_charge_price','sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text',
                                         DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
                                else
                                    $gridBuyer->select ( 'sp.transaction_id', 'cf.seaport_name as frompostoffice_name', 'cf.seaport_name as topostoffice_name','sqi.id','seller_user.id as seller_id', 'sp.kg_per_cft',DB::raw( "'$key' as matchwith"),DB::raw("'$ptlFromPincodeText' as frompincode"), 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units',DB::raw($ptlFromLocation .' as from_location_id'), 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking', 'sp.lkp_payment_mode_id','pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.pickup_charges','sp.delivery_charges','sp.oda_charges',DB::raw("'$ptlFromPincodeText ' as search_from_pincode"),'sp.docket_charge_price','sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text',
                                         DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
                     
                            }elseif(!empty($ptlToPincodeText)){
                                $gridBuyer->select ( 'sp.transaction_id', 'cf.seaport_name as frompostoffice_name', 'cf.seaport_name as topostoffice_name','sqi.id','seller_user.id as seller_id', 'sp.kg_per_cft',DB::raw( "'$key' as matchwith"),DB::raw("'$ptlToPincodeText' as topincode"), 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units',DB::raw($ptlToLocation .' as to_location_id'), 'seller_user.username', 'sp.from_date', 'sp.to_date', 'sp.tracking', 'sp.lkp_payment_mode_id','pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.pickup_charges','sp.delivery_charges','sp.oda_charges',DB::raw("'$ptlToPincodeText' as search_to_pincode"),'sp.docket_charge_price','sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text',
                                         DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
                            
                            }else{
                                    $gridBuyer->select ('sp.transaction_id', 'cf.seaport_name as frompostoffice_name', 'cf.seaport_name as topostoffice_name', 'sqi.id','seller_user.id as seller_id', 'sp.kg_per_cft', 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price','sqi.transitdays','sqi.units', 'sp.from_date', 'sp.to_date', 'sp.tracking', 'sp.lkp_payment_mode_id','pm.payment_mode as paymentmethod','sqi.created_by','pbqsqp.initial_quote_price','pbqsqp.counter_quote_price','pbqsqp.final_quote_price','sp.pickup_charges','sp.delivery_charges','sp.oda_charges','sp.docket_charge_price','sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text',
                                             DB::raw("(case when `pbqsqp`.`final_transit_days` != 0 then pbqsqp.final_transit_days  when `pbqsqp`.`initial_transit_days` != 0 then pbqsqp.initial_transit_days when 'pbqsqp.id'=0 then sqi.transitdays end) as transitdays") );
                            }
                        }
			$gridBuyer->groupBy('sqi.id');
			//echo "<pre>";
			//echo $gridBuyer->tosql();
			//print_R($gridBuyer->getBindings());die;
			$key = $ptlFromLocation ."_". $ptlToLocation;
			//$result["$key"] =$gridBuyer->get();
			foreach($gridBuyer->get() as $info){
				$result[]=$info;
			}

		}
		return $result;
	}
	/**
	 * Intracity Buyer Search
	 * @param $params
	 * result $query
	 */
	public static function getIntracityBuyerSearchResults($statusId,$params){
		
		$loginId = Auth::User()->id;
		// Below script for buyer search for seller posts join query --for Grid
		$gridBuyer = DB::table ( 'ict_seller_post_items as sqi' );
		$gridBuyer->join ( 'lkp_load_types as lt', 'lt.id', '=', 'sqi.lkp_load_type_id' );
		$gridBuyer->join ( 'lkp_vehicle_types as vt', 'vt.id', '=', 'sqi.lkp_vehicle_type_id' );
		$gridBuyer->join ( 'lkp_ict_locations as cf', 'sqi.from_location_id', '=', 'cf.id' );
		$gridBuyer->join ( 'lkp_ict_locations as ct', 'sqi.to_location_id', '=', 'ct.id' );
		$gridBuyer->join ( 'ict_seller_posts as sp', 'sp.id', '=', 'sqi.seller_post_id' );
		$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
		$gridBuyer->join ( 'lkp_ict_rate_types as rt', 'rt.id', '=', 'sqi.lkp_ict_rate_type_id' );
		$gridBuyer->leftjoin ( 'ict_seller_selected_buyers as issb', 'issb.seller_post_id', '=', 'sp.id' );
                $gridBuyer->leftjoin ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
		$gridBuyer->where('sp.lkp_post_status_id', '=', $statusId);
		$gridBuyer->where('sqi.lkp_post_status_id', '=', $statusId);

		// set from location below varaibles are checking empty or not varaible in buyear search---grid

		if (isset ( $params ['rate_type'] ) && $params ['rate_type'] != '') {
			$gridBuyer->Where ( 'sqi.lkp_ict_rate_type_id', $params ['rate_type'] );
		}
		if (isset ( $params ['lkp_city_id'] ) && $params ['lkp_city_id'] != '') {
			$gridBuyer->Where ( 'sqi.lkp_city_id', $params ['lkp_city_id'] );
		}

		if (isset ( $params ['from_location_id'] ) && $params ['from_location_id'] != '') {
			$gridBuyer->Where ( 'sqi.from_location_id', $params ['from_location_id'] );
		}
		// set to location
		if (isset ( $params ['to_location_id'] ) && $params ['to_location_id'] != '') {
			$gridBuyer->Where ( 'sqi.to_location_id', $params ['to_location_id'] );
		}
		// set load type
		if (isset ( $params ['load_type'] ) && ($params ['load_type'] != '') && ($params ['load_type'] != LOADTYPE_ALL)) {
			//$gridBuyer->Where ( 'sqi.lkp_load_type_id', $_REQUEST ['lkp_load_type_id'] );
			$loadtypeid = $params ['load_type'];
			$gridBuyer->whereRaw ( "( IF(`sqi`.`lkp_load_type_id` != 11, `sqi`.`lkp_load_type_id` = $loadtypeid,TRUE )  )");
		}
		// set vehicle type
		if (isset ( $params ['lkp_vehicle_id'] ) && ($params ['lkp_vehicle_id'] != '') && ($params ['lkp_vehicle_id'] != VEHICLETYPE_ALL)) {
			//$gridBuyer->Where ( 'sqi.lkp_vehicle_type_id', $_REQUEST ['lkp_vehicle_type_id'] );
			$vehicletypeid = $params ['lkp_vehicle_id'];
			$gridBuyer->whereRaw ( "( IF(`sqi`.`lkp_vehicle_type_id` != 20, `sqi`.`lkp_vehicle_type_id` = $vehicletypeid,TRUE )  )");
		}
		// set dispatch date
		if (isset ($params ['pickup_date'] ) && $params ['pickup_date'] != '') {
			$pickupdate = CommonComponent::convertDateForDatabase($params['pickup_date'] );
			$gridBuyer->whereRaw ( "( '$pickupdate' between `sp`.`from_date` and `sp`.`to_date` )") ;
		}
		//Checking private sellers
		$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_access_id` = 2, `issb`.`buyer_id` =  $loginId,TRUE )  )");

		//Sellers filter
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers = $params['selected_users'];
			$gridBuyer->WhereIn ( 'seller_user.id', $selectedSellers );
		}

		//selected payments
		if(isset($params['selected_payments']) && $params['selected_payments']!='') {
			$selectedPayments =$params['selected_payments'];
			$gridBuyer->WhereIn ( 'sp.lkp_payment_mode_id', $selectedPayments );
		}
                
                // set to tracking
                if (isset ( $params ['trackingfilter'] ) && !empty($params ['trackingfilter']) && sizeof($params ['trackingfilter']) > 0) {
                        $gridBuyer->WhereIn ( 'sp.tracking', $params ['trackingfilter'] );
                }

                //price filter
                if (isset ( $params ['price'] ) && $params ['price'] != '') {
			$splitprice = explode("    ",$params ['price']);
			$from = trim(filter_var($splitprice[0],FILTER_SANITIZE_NUMBER_INT),"-");
			$to = trim(filter_var($splitprice[1],FILTER_SANITIZE_NUMBER_INT),"-");
			$_REQUEST['price_from'] = $from;
			$_REQUEST['price_to'] = $to;
			$gridBuyer->Where ( 'price', '>=', $from );
			$gridBuyer->Where ( 'price', '<=', $to );
		}
		$gridBuyer->select ('seller_user.username','seller_user.id as seller_id','sqi.lkp_vehicle_type_id','sqi.lkp_load_type_id', 'sqi.id', 'sqi.transitdays','sqi.lkp_ict_rate_type_id', 'sqi.minimum_hours', 'sqi.minimum_hour_charges', 'sqi.minimum_kms', 'sqi.minimum_km_charges', 'sqi.waiting_charges', 'sqi.overdimension_charges', 'sqi.labor_charges','sqi.lkp_city_id', 'lt.load_type', 'vt.vehicle_type', 'cf.ict_location_name as fromcity', 'ct.ict_location_name as tocity', 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price', 'sp.tracking', 'sp.lkp_payment_mode_id','pm.payment_mode as paymentmethod','sqi.created_by', 'sp.from_date as pickup_date' );
		$gridBuyer->groupBy('sqi.id');
		return $gridBuyer;
	}

	/**
	 * FTL Buyer Search
	 * @param $params
	 * result $query
	 */
	public static function getRelocationBuyerSearchResults($statusId,$params){
		$loginId = Auth::User()->id;
		//echo "<pre>";print_R($params);die;
		// Below script for buyer search for seller posts join query --for Grid
		$gridBuyer = DB::table ( 'relocation_seller_posts as sp' );
		$gridBuyer->join ( 'relocation_seller_post_items as spi', 'sp.id', '=', 'spi.seller_post_id' );
		$gridBuyer->join ( 'lkp_cities as cf', 'sp.from_location_id', '=', 'cf.id' );
		$gridBuyer->join ( 'lkp_cities as ct', 'sp.to_location_id', '=', 'ct.id' );
		$gridBuyer->join('lkp_post_ratecard_types as rt', 'rt.id', '=', 'sp.rate_card_type');
		$gridBuyer->join ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
		$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
		$gridBuyer->leftjoin ( 'relocation_seller_selected_buyers as ssb', 'ssb.seller_post_id', '=', 'sp.id' );
                $gridBuyer->leftjoin ( 'relocation_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_id', '=', 'spi.id' );
		$gridBuyer->where('sp.lkp_post_status_id', '=', $statusId);
		$gridBuyer->where('spi.is_private', '=', 0);

		// set from location below varaibles are checking empty or not varaible in buyear search---grid
		if (isset ( $params ['from_location_id'] ) && $params ['from_location_id'] != '') {
			$gridBuyer->Where ( 'sp.from_location_id', $params ['from_location_id'] );
		}
		// set to location
		if (isset ( $params ['to_location_id'] ) && $params ['to_location_id'] != '') {
			$gridBuyer->Where ( 'sp.to_location_id', $params ['to_location_id'] );
		}

		//set district
		if(isset($params['district']) && $params['district']!=''){
			$gridBuyer->where('sp.seller_district_id',$params['district']);
		}
		if(isset($params['post_rate_card_type']) && $params['post_rate_card_type'] == 1){
			// set property type
			$gridBuyer->WhereIn ( 'spi.rate_card_type', array(1,3) );
			if (isset ( $params ['property_type'] ) && $params ['property_type'] != '') {
				$gridBuyer->Where ( 'spi.lkp_property_type_id', $params ['property_type'] );
			}
			// set load type
			if (isset ( $params ['load_type'] ) && $params ['load_type'] != '') {
				$gridBuyer->Where ( 'spi.lkp_load_category_id', $params ['load_type'] );
			}
		}
		if(isset($params['post_rate_card_type']) && $params['post_rate_card_type'] == 2) {
			$gridBuyer->WhereIn ( 'spi.rate_card_type', array(2,3) );
			// set vehicle category
			if (isset ( $params ['vehicle_category'] ) && $params ['vehicle_category'] != '') {
				$gridBuyer->Where ( 'spi.lkp_vehicle_category_id', $params ['vehicle_category'] );
			}
			// set vehicle size
			if (isset ( $params ['vehicle_category_type'] ) && $params ['vehicle_category_type'] != '') {
				$gridBuyer->Where ( 'spi.lkp_car_size', $params ['vehicle_category_type'] );
			}
		}

		// set tracking filter milestoness
		if (isset ( $params ['trackingfilter'] ) && ($params ['trackingfilter'] != '') && is_array($params['trackingfilter']) && !empty($params ['trackingfilter']) ) {
			$gridBuyer->WhereIn ( 'sp.tracking', $params ['trackingfilter'] );
		}


		//price filter
		// set tracking2 filter realt time
		if (isset ( $params ['price'] ) && $params ['price'] != '') {
			$splitprice = explode("    ",$params ['price']);
			//echo "<pre>>"; print_R($splitprice); echo "</pre>";
			$from = trim(filter_var($splitprice[0],FILTER_SANITIZE_NUMBER_INT),"-");
			$to = trim(filter_var($splitprice[1],FILTER_SANITIZE_NUMBER_INT),"-");
			$_REQUEST['price_from'] = $from;
			$_REQUEST['price_to'] = $to;
			//echo "From is $from to is $to";
			//$gridBuyer->Where ( 'price', '>=', $from );
			//$gridBuyer->Where ( 'price', '<=', $to );
            $rate_card_type    =   Session::get('session_rate_card_type');
                        
           $gridBuyer->whereRaw ( " (case when $rate_card_type=1 then (spi.volume*spi.rate_per_cft)+spi.transport_charges >= $from else (spi.cost+spi.transport_charges) >= $from end ) " );
           $gridBuyer->whereRaw ( " (case when $rate_card_type=1 then (spi.volume*spi.rate_per_cft)+spi.transport_charges <= $to else (spi.cost+spi.transport_charges) <= $to end) " );
                        
		}
		//dispatch dates
		if(isset($params['from_date']) && $params['from_date']!=''){
			$dispatchflexibleflag = isset($params['is_dispatch_flexible']) ? $params['is_dispatch_flexible'] : 0;
			$dispatchsame = CommonComponent::convertDateForDatabase($params['from_date']);
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " + 3 day"));
			$currentdate = date('Y-m-d');
			$dispatch_minus  = ($dispatch_minus >= $currentdate) ? $dispatch_minus : $currentdate;


			if ($dispatchflexibleflag == 1) {
				$gridBuyer->whereRaw ( "( ('$dispatch_minus' between `sp`.`from_date` and `sp`.`to_date`) or  ('$dispatch_plus' between `sp`.`from_date` and `sp`.`to_date`)   )") ;
			}else{
				$gridBuyer->whereRaw ( "( '$dispatchsame' between `sp`.`from_date` and `sp`.`to_date`   )") ;
				//$gridBuyer->whereRaw ( "( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `bqi`.`dispatch_date` - INTERVAL 3 DAY and `bqi`.`dispatch_date` + INTERVAL 3 DAY  )  )" );
			}

		}
		if(isset($params['from_date']) && ($params['from_date']!='') && isset($params['to_date']) && ($params['to_date']!='')){
			$deliveryflexibleflag = isset($params['is_delivery_flexible']) ? $params['is_delivery_flexible'] : 0;

			$deliverysame = CommonComponent::convertDateForDatabase($params['to_date']);

			$daysdiff = strtotime($deliverysame) - strtotime($dispatchsame);
			$daysdiff = floor($daysdiff/(60*60*24))+1;
			$daysdiff = ($dispatchflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
			$daysdiff = ($deliveryflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
			$monthsdiff = $daysdiff / 7;
			$gridBuyer->whereRaw ( "( IF(`spi`.`units` = 'Days', `spi`.`transitdays` <= '$daysdiff', `spi`.`transitdays` <= '$monthsdiff'  )  )" );
		}

		//Checking private sellers
		$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_access_id` = 2, `ssb`.`buyer_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			//$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$selectedSellers =$params['selected_users'];
			$gridBuyer->WhereIn ( 'seller_user.id', $selectedSellers );
		}

		//selected payments
		if(isset($params['selected_payments']) && $params['selected_payments']!='') {
			$selectedPayments =$params['selected_payments'];
			$gridBuyer->WhereIn ( 'sp.lkp_payment_mode_id', $selectedPayments );
		}
		$gridBuyer->select ('sp.*','seller_user.username','spi.volume','spi.transitdays','spi.units as transitdaysunits','spi.rate_per_cft','spi.transport_charges','spi.cost','pm.payment_mode',
                         DB::raw("(case when `pbqsqp`.`transit_days` != 0 then pbqsqp.transit_days   when 'pbqsqp.id'=0 then spi.transitdays end) as transitdays"));
		/*echo $gridBuyer->tosql();
		echo "<pre>";
		print_R($gridBuyer->getBindings());
		print_R($gridBuyer->get());die;*/
		return $gridBuyer;
	}


	public static function getRelocationOfficeBuyerSearchResults($statusId,$params){
		$loginId = Auth::User()->id;
		//echo "<pre>";print_R($params);die;
		// Below script for buyer search for seller posts join query --for Grid
		$gridBuyer = DB::table ( 'relocationoffice_seller_posts as sp' );
		$gridBuyer->join ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
		$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
		$gridBuyer->leftjoin ( 'relocationoffice_seller_selected_buyers as ssb', 'ssb.seller_post_id', '=', 'sp.id' );
        $gridBuyer->leftjoin ( 'relocationoffice_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_id', '=', 'sp.id' );
        $gridBuyer->leftjoin ( 'relocationoffice_seller_post_slabs as rsps', 'rsps.seller_post_id', '=', 'sp.id' );
        /*$gridBuyer->leftjoin ( DB::raw("(select transport_price,seller_post_id from relocationoffice_seller_post_slabs subrosps where (slab_max_km>=".$params['distance'].") OR (".$params['distance']." between slab_min_km and slab_max_km) limit 1) test"),function($join){

        $join->on("test.id","=","sp.id");

  } );*/
		/*$gridBuyer->leftjoin(DB::raw("(select transport_price,seller_post_id from relocationoffice_seller_post_slabs where (slab_max_km>=".$params['distance'].") limit 1) rsps"),'rsps.seller_post_id','=','sp.id');*/
		$gridBuyer->where('sp.lkp_post_status_id', '=', $statusId);
		$gridBuyer->where('sp.is_private', '=', 0);

		// set from location below varaibles are checking empty or not varaible in buyear search---grid
		if (isset ( $params ['from_location_id'] ) && $params ['from_location_id'] != '') {
			$gridBuyer->Where ( 'sp.from_location_id', $params ['from_location_id'] );
		}
		
		//set district
		if(isset($params['district']) && $params['district']!=''){
			$gridBuyer->where('sp.seller_district_id',$params['district']);
		}
		
		// set tracking filter milestoness
		if (isset ( $params ['trackingfilter'] ) && ($params ['trackingfilter'] != '') && is_array($params['trackingfilter']) && !empty($params ['trackingfilter']) ) {
			$gridBuyer->WhereIn ( 'sp.tracking', $params ['trackingfilter'] );
		}


		//price filter
		// set tracking2 filter realt time
		if (isset ( $params ['price'] ) && $params ['price'] != '') {
			$splitprice = explode("    ",$params ['price']);
			//echo "<pre>"; print_R($splitprice); echo "</pre>";
			$from = trim(filter_var($splitprice[0],FILTER_SANITIZE_NUMBER_INT),"-");
			$to = trim(filter_var($splitprice[1],FILTER_SANITIZE_NUMBER_INT),"-");
			
			$_REQUEST['price_from'] = $from;
			$_REQUEST['price_to'] = $to;
			$gridBuyer->whereRaw ( "(".$params['volume']."*sp.rate_per_cft)+(".$params['distance']."*rsps.transport_price) >= $from " );
            $gridBuyer->whereRaw ( "(".$params['volume']."*sp.rate_per_cft)+(".$params['distance']."*rsps.transport_price) <= $to " );
           
		}
		//dispatch dates
		if(isset($params['from_date']) && $params['from_date']!=''){

			$dispatchflexibleflag = isset($params['dispatch_flexible_hidden']) ? $params['dispatch_flexible_hidden'] : 0;
			if(isset($params['date_flexiable']) &&  $params['date_flexiable'] != ''){
				$dispatchsame = CommonComponent::convertDateForDatabase($params['date_flexiable']);
			}else{
				$dispatchsame = CommonComponent::convertDateForDatabase($params['from_date']);
			}
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " + 3 day"));
			$currentdate = date('Y-m-d');
			$dispatch_minus  = ($dispatch_minus >= $currentdate) ? $dispatch_minus : $currentdate;

			if ($dispatchflexibleflag == 1 && (isset($params['date_flexiable']) && $params['date_flexiable']=="")) {
				$gridBuyer->whereRaw ( "( ('$dispatch_minus' between `sp`.`from_date` and `sp`.`to_date`) or  ('$dispatch_plus' between `sp`.`from_date` and `sp`.`to_date`)   )") ;
			}else{
				$gridBuyer->whereRaw ( "( '$dispatchsame' between `sp`.`from_date` and `sp`.`to_date`   )") ;
			}

		}
		if(isset($params['from_date']) && ($params['from_date']!='') && isset($params['to_date']) && ($params['to_date']!='')){
			$deliveryflexibleflag = isset($params['delivery_flexible_hidden']) ? $params['delivery_flexible_hidden'] : 0;
			$deliverysame = CommonComponent::convertDateForDatabase($params['to_date']);
			$daysdiff = strtotime($deliverysame) - strtotime($dispatchsame);
			$daysdiff = floor($daysdiff/(60*60*24))+1;
			$daysdiff = ($dispatchflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
			$daysdiff = ($deliveryflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
		}

		//Checking private sellers
		$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_access_id` = 2, `ssb`.`buyer_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			//$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$selectedSellers =$params['selected_users'];
			$gridBuyer->WhereIn ( 'seller_user.id', $selectedSellers );
		}

		//selected payments
		if(isset($params['selected_payments']) && $params['selected_payments']!='') {
			$selectedPayments =$params['selected_payments'];
			$gridBuyer->WhereIn ( 'sp.lkp_payment_mode_id', $selectedPayments );
		}

		// Check Distance 		
		if(isset($params['distance']) && $params['distance']!='') {
			$gridBuyer->whereRaw("rsps.id in (select id from relocationoffice_seller_post_slabs where ((slab_max_km>=".$params['distance'].") OR (".$params['distance']." between slab_min_km and slab_max_km)) and seller_post_id = sp.id group by seller_post_id)");
		}
		
		$gridBuyer->select ('sp.*','seller_user.username','pm.payment_mode','rsps.transport_price');
		
		/*echo $gridBuyer->tosql();
		echo "<pre>";
		print_R($gridBuyer->getBindings());
		print_R($gridBuyer->get());die; */
		return $gridBuyer;
	}

	public static function getsellertype(){
		$loginId = Auth::id();
		$businesstype = DB::table('users')
			->where('id', $loginId)
			->select('is_business')
			->first();
		return $businesstype;
	}

	/**
	 * It returns Pincodes by taking input pincode
	 * -> fetch zone of pincods
	 * -> fetch all pincodes of zone
	 * @param $pincodes
	 * @return mixed
	 *
	 */
	public static function getZonePincodesByPincode($pincodes){
		$zone_location_ids = DB::table('ptl_pincodexsectors as ppxs')
			->join('ptl_sectors as s1','ppxs.ptl_sector_id','=','s1.id')
			->join('ptl_zones as z','s1.ptl_zone_id','=','z.id')
			->join('lkp_ptl_pincodes as lpp','lpp.id','=','ppxs.ptl_pincode_id')
			//->where('z2.seller_id',Auth::User()->id)
			->where('lpp.id',$pincodes)
			->select('z.id')
			->distinct('z.id')
			->lists('z.id');
		//echo "<pre>zones";print_R($zone_location_ids);
		$resultpincodes = array();
		foreach($zone_location_ids as $zone_location_id){
			$zonepincodes = SellerSearchComponent::getPincodesByZoneId($zone_location_id);
			foreach($zonepincodes as $zonepincode){
				$resultpincodes[] = $zonepincode;
			}
		}
		/*foreach($pincodes as $pincode){
			$resultpincodes[] = $pincode;
		}*/
		$resultpincodes[] = $pincodes;
		$resultpincodes = array_unique($resultpincodes);

		$response = array();
		$response['pincodes'] = $resultpincodes;
		$response['zones'] = $zone_location_ids;
		//echo "<pre>pincodes";print_R($resultpincodes);
		//echo "<pre>";print_R($pincodes);die;
		return $response;
	}
	/**
	 * Truck Haul Buyer Search
	 * @param $params
	 * result $query
	 */
	public static function getTruckHaulBuyerSearchResults($statusId,$params){
		$loginId = Auth::User()->id;
		//print_R($params);//exit;
		// Below script for buyer search for seller posts join query --for Grid
		$gridBuyer = DB::table ( 'truckhaul_seller_post_items as sqi' );
		$gridBuyer->leftjoin ( 'lkp_load_types as lt', 'lt.id', '=', 'sqi.lkp_load_type_id' );
		$gridBuyer->join ( 'lkp_vehicle_types as vt', 'vt.id', '=', 'sqi.lkp_vehicle_type_id' );
		$gridBuyer->join ( 'lkp_cities as cf', 'sqi.from_location_id', '=', 'cf.id' );
		$gridBuyer->join ( 'lkp_cities as ct', 'sqi.to_location_id', '=', 'ct.id' );
		$gridBuyer->join ( 'truckhaul_seller_posts as sp', 'sp.id', '=', 'sqi.seller_post_id' );
		$gridBuyer->join ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
		$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
		$gridBuyer->leftjoin ( 'truckhaul_seller_selected_buyers as ssb', 'ssb.seller_post_id', '=', 'sp.id' );
                $gridBuyer->leftjoin ( 'truckhaul_buyer_quote_sellers_quotes_prices as bqsp', 'bqsp.seller_post_item_id', '=', 'sqi.id' );
		$gridBuyer->where('sp.lkp_post_status_id', '=', $statusId);
		$gridBuyer->where('sqi.lkp_post_status_id', '=', $statusId);
		$gridBuyer->where('sqi.is_private', '=', 0);

		// set from location below varaibles are checking empty or not varaible in buyear search---grid
		if (isset ( $params ['from_location_id'] ) && $params ['from_location_id'] != '') {
			$gridBuyer->Where ( 'sqi.from_location_id', $params ['from_location_id'] );
		}
		// set to location
		if (isset ( $params ['to_location_id'] ) && $params ['to_location_id'] != '') {
			$gridBuyer->Where ( 'sqi.to_location_id', $params ['to_location_id'] );
		}

		//set district
		if(isset($params['district']) && $params['district']!=''){
			$gridBuyer->where('cf.lkp_district_id',$params['district']);
		}

		// set load type
		if (isset ( $params ['lkp_load_type_id'] ) && ($params ['lkp_load_type_id'] != '') && ($params ['lkp_load_type_id'] != LOADTYPE_ALL)) {
			//$gridBuyer->Where ( 'sqi.lkp_load_type_id', $params ['lkp_load_type_id'] );
			$loadtypeid = $params ['lkp_load_type_id'];
			$gridBuyer->whereRaw ( "( IF(`sqi`.`lkp_load_type_id` != 11, `sqi`.`lkp_load_type_id` = $loadtypeid,TRUE )  )");
		}
		// set vehicle type
		if (isset ( $params ['lkp_vehicle_type_id'] ) && ($params ['lkp_vehicle_type_id'] != '') && ($params ['lkp_vehicle_type_id'] != VEHICLETYPE_ALL)) {
			//$gridBuyer->Where ( 'sqi.lkp_vehicle_type_id', $params ['lkp_vehicle_type_id'] );
			$vehicletypeid = $params ['lkp_vehicle_type_id'];
			$gridBuyer->whereRaw ( "( IF(`sqi`.`lkp_vehicle_type_id` != 20, `sqi`.`lkp_vehicle_type_id` = $vehicletypeid,TRUE )  )");
		}
		// set tracking filter milestoness
		if (isset ( $params ['trackingfilter'] ) && ($params ['trackingfilter'] != '') && is_array($params['trackingfilter']) && !empty($params ['trackingfilter']) ) {
			//$params ['tracking'] = $params ['trackingfilter'];
			$gridBuyer->WhereIn ( 'sp.tracking', $params ['trackingfilter'] );
		}
		
		
		//price filter
		// set tracking2 filter realt time
		if (isset ( $params ['price'] ) && $params ['price'] != '') {
			$splitprice = explode("    ",$params ['price']);
			$from = trim(filter_var($splitprice[0],FILTER_SANITIZE_NUMBER_INT),"-");
			$to = trim(filter_var($splitprice[1],FILTER_SANITIZE_NUMBER_INT),"-");
                        
                        $_REQUEST['price_from'] = $from;
			$_REQUEST['price_to'] = $to;
			
			$gridBuyer->Where ( 'price', '>=', $from );
			$gridBuyer->Where ( 'price', '<=', $to );
                       
		}
		//dispatch dates
		if(isset($params['from_date']) && $params['from_date']!=''){
			$dispatchflexibleflag = isset($params['is_dispatch_flexible']) ? $params['is_dispatch_flexible'] : 0;
			if(isset($params['date_flexiable']) &&  $params['date_flexiable'] != ''){
				$dispatchsame = CommonComponent::convertDateForDatabase($params['date_flexiable']);
			}else{
				$dispatchsame = CommonComponent::convertDateForDatabase($params['from_date']);
			}
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " + 3 day"));
			$currentdate = date('Y-m-d');
			$dispatch_minus  = ($dispatch_minus >= $currentdate) ? $dispatch_minus : $currentdate;


			if ($dispatchflexibleflag == 1 && (isset($params['date_flexiable']) && $params['date_flexiable']=="")) {
				$gridBuyer->whereRaw ( "( ('$dispatch_minus' between `sp`.`from_date` and `sp`.`to_date`) or  ('$dispatch_plus' between `sp`.`from_date` and `sp`.`to_date`)   )") ;
			}else{
				$gridBuyer->whereRaw ( "( '$dispatchsame' between `sp`.`from_date` and `sp`.`to_date`   )") ;
			}

		}
		/*if(isset($params['from_date']) && ($params['from_date']!='') && isset($params['to_date']) && ($params['to_date']!='')){
			$deliveryflexibleflag = isset($params['is_delivery_flexible']) ? $params['is_delivery_flexible'] : 0;

			$deliverysame = CommonComponent::convertDateForDatabase($params['to_date']);

			$daysdiff = strtotime($deliverysame) - strtotime($dispatchsame);
			$daysdiff = floor($daysdiff/(60*60*24))+1;
			$daysdiff = ($dispatchflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
			$daysdiff = ($deliveryflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
			$monthsdiff = $daysdiff / 7;
			$gridBuyer->whereRaw ( "( IF(`sqi`.`units` = 'Days', `sqi`.`transitdays` <= '$daysdiff', `sqi`.`transitdays` <= '$monthsdiff'  )  )" );
		}*/

		//Checking private sellers
		$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_access_id` = 2, `ssb`.`buyer_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers =$params['selected_users'];
                    $gridBuyer->WhereIn ( 'seller_user.id', $selectedSellers );
		}

		//selected payments
		if(isset($params['selected_payments']) && $params['selected_payments']!='') {
			$selectedPayments =$params['selected_payments'];
			$gridBuyer->WhereIn ( 'sp.lkp_payment_mode_id', $selectedPayments );
		}
		$gridBuyer->select ('sp.transaction_id', 'sqi.id', 'sqi.transitdays', 'sqi.units as transitunits', 'sqi.from_location_id', 'sqi.to_location_id', 'sqi.lkp_load_type_id', 'sqi.lkp_vehicle_type_id','sqi.vehicle_number', 'sqi.created_by', 'lt.load_type', 'vt.vehicle_type', 'cf.city_name as fromcity', 'ct.city_name as tocity', 'sqi.created_at', 'sqi.seller_post_id', 'sqi.price', 'seller_user.username','seller_user.id as seller_id', 'sp.from_date', 'sp.to_date', 'sp.tracking','sp.lkp_payment_mode_id', 'pm.payment_mode as paymentmethod', 'sqi.created_by', 'sp.cancellation_charge_text', 'sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text', 'sp.docket_charge_price','vt.capacity','vt.units' ,
                        DB::raw("(case when `bqsp`.`final_transit_days` != 0 then bqsp.final_transit_days  when `bqsp`.`initial_transit_days` != 0 then bqsp.initial_transit_days when 'bqsp.id'=0 then sqi.transitdays end) as transitdays") );
		$gridBuyer->groupBy('sqi.id');

		return $gridBuyer;
	}
	
	/**
	 * Truck Haul Buyer Search
	 * @param $params
	 * result $query
	 */
	public static function getTruckLeaseBuyerSearchResults($statusId,$params){
		$loginId = Auth::User()->id;
		//echo "<pre>";print_R($params);
		// Below script for buyer search for seller posts join query --for Grid
		$gridBuyer = DB::table ( 'trucklease_seller_post_items as sqi' );
		$gridBuyer->join ( 'lkp_vehicle_types as vt', 'vt.id', '=', 'sqi.lkp_vehicle_type_id' );
		$gridBuyer->join( 'lkp_trucklease_lease_terms as tlt', 'tlt.id', '=', 'sqi.lkp_trucklease_lease_term_id' );
		$gridBuyer->join ( 'lkp_cities as cf', 'sqi.from_location_id', '=', 'cf.id' );
		$gridBuyer->join ( 'trucklease_seller_posts as sp', 'sp.id', '=', 'sqi.seller_post_id' );
		$gridBuyer->join ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
		$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
		$gridBuyer->leftjoin ( 'trucklease_seller_selected_buyers as ssb', 'ssb.seller_post_id', '=', 'sp.id' );
		$gridBuyer->leftjoin ( 'trucklease_buyer_quote_sellers_quotes_prices as bqsp', 'bqsp.seller_post_item_id', '=', 'sqi.id' );
		$gridBuyer->where('sp.lkp_post_status_id', '=', $statusId);
		$gridBuyer->where('sqi.lkp_post_status_id', '=', $statusId);
		$gridBuyer->where('sqi.is_private', '=', 0);
	
		// set from location below varaibles are checking empty or not varaible in buyear search---grid
		if (isset ( $params ['from_location_id'] ) && $params ['from_location_id'] != '') {
			$gridBuyer->Where ( 'sqi.from_location_id', $params ['from_location_id'] );
		}

	
		//set district
		if(isset($params['district']) && $params['district']!=''){
			$gridBuyer->where('cf.lkp_district_id',$params['district']);
		}
	

		// set vehicle type
		if (isset ( $params ['lkp_vehicle_type_id'] ) && ($params ['lkp_vehicle_type_id'] != '') && ($params ['lkp_vehicle_type_id'] != VEHICLETYPE_ALL)) {
			//$gridBuyer->Where ( 'sqi.lkp_vehicle_type_id', $params ['lkp_vehicle_type_id'] );
			$vehicletypeid = $params ['lkp_vehicle_type_id'];
			$gridBuyer->whereRaw ( "( IF(`sqi`.`lkp_vehicle_type_id` != 20, `sqi`.`lkp_vehicle_type_id` = $vehicletypeid,TRUE )  )");
		}

		// set Lease Term
		if (isset ( $params ['lkp_trucklease_lease_term_id'] ) && ($params ['lkp_trucklease_lease_term_id'] != '')) {
			//$params ['tracking'] = $params ['trackingfilter'];
			$gridBuyer->Where ( 'sqi.lkp_trucklease_lease_term_id', $params ['lkp_trucklease_lease_term_id'] );
		}

		// set Drive Availability
		if (isset ( $params ['driver_availability'] ) && ($params ['driver_availability'] != '')) {
			//$params ['tracking'] = $params ['trackingfilter'];
			$gridBuyer->Where ( 'sqi.driver_availability', $params ['driver_availability'] );
		}

		// set tracking filter milestoness
		if (isset ( $params ['trackingfilter'] ) && ($params ['trackingfilter'] != '') && is_array($params['trackingfilter']) && !empty($params ['trackingfilter']) ) {
			//$params ['tracking'] = $params ['trackingfilter'];
			$gridBuyer->WhereIn ( 'sp.tracking', $params ['trackingfilter'] );
		}
	
	
		//price filter
		// set tracking2 filter realt time
		if (isset ( $params ['price'] ) && $params ['price'] != '') {
			$splitprice = explode("    ",$params ['price']);
			$from = trim(filter_var($splitprice[0],FILTER_SANITIZE_NUMBER_INT),"-");
			$to = trim(filter_var($splitprice[1],FILTER_SANITIZE_NUMBER_INT),"-");
	
			$_REQUEST['price_from'] = $from;
			$_REQUEST['price_to'] = $to;
				
			//$gridBuyer->Where ( 'price', '>=', $from );
			//$gridBuyer->Where ( 'price', '<=', $to );
			 
		}
		//dispatch dates
		if(isset($params['from_date']) && $params['from_date']!=''){
			$dispatchsame = CommonComponent::convertDateForDatabase($params['from_date']);
			//$gridBuyer->whereRaw ( "( '$dispatchsame' between `sp`.`from_date` and `sp`.`to_date`   )") ;
			$gridBuyer->whereRaw ( "( '$dispatchsame' >= `sp`.`from_date` )");
		}
		if(isset($params['to_date']) && $params['to_date']!=''){
			$deliverysame = CommonComponent::convertDateForDatabase($params['to_date']);
			//$gridBuyer->whereRaw ( "( '$dispatchsame' between `sp`.`from_date` and `sp`.`to_date`   )") ;
			$gridBuyer->whereRaw ( "( '$deliverysame' <= `sp`.`to_date` )");
		}

		if(isset($params['from_date']) && $params['from_date']!='' && isset($params['to_date']) && $params['to_date']!=''){

			$from_date=CommonComponent::convertDateForDatabase($params['from_date']);
			$to_date=CommonComponent::convertDateForDatabase($params['to_date']);
			$daysdiff = strtotime($to_date) - strtotime($from_date);
			$leaseperiod = floor($daysdiff/(60*60*24))+1;
			if($params['lkp_trucklease_lease_term_id']==2){
			$leaseperiod=ceil($leaseperiod/7);
		    }
		    if($params['lkp_trucklease_lease_term_id']==3){
			$leaseperiod=ceil($leaseperiod/30);
		    }
		    if($params['lkp_trucklease_lease_term_id']==4){
			$leaseperiod=ceil($leaseperiod/365);
		    }
			
			$gridBuyer->whereRaw ( "`sqi`.`minimum_lease_period` <= $leaseperiod " );
		}
				//Checking private sellers
		$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_access_id` = 2, `ssb`.`buyer_id` =  $loginId,TRUE )  )");
	
		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers =$params['selected_users'];
			$gridBuyer->WhereIn ( 'seller_user.id', $selectedSellers );
		}
	
		//selected payments
		if(isset($params['selected_payments']) && $params['selected_payments']!='') {
			$selectedPayments =$params['selected_payments'];
			$gridBuyer->WhereIn ( 'sp.lkp_payment_mode_id', $selectedPayments );
		}
		$gridBuyer->select ('sp.transaction_id', 'sqi.id', 'sqi.from_location_id', 'sqi.lkp_vehicle_type_id',
                                    'sqi.created_by', 'vt.vehicle_type', 'cf.city_name as fromcity', 'sqi.created_at',
                                    'sqi.seller_post_id', 'sqi.price', 'seller_user.username','seller_user.id as seller_id', 'sp.from_date', 'sp.to_date','sp.lkp_payment_mode_id', 'pm.payment_mode as paymentmethod', 'sqi.created_by', 'sp.cancellation_charge_text', 'sp.cancellation_charge_price', 'sp.other_charge1_price','sp.other_charge1_text', 'sp.other_charge2_price','sp.other_charge2_text', 'sp.other_charge3_price', 'sp.other_charge3_text', 'sp.docket_charge_text', 
                                    'sp.docket_charge_price','vt.capacity','vt.units','sqi.lkp_trucklease_lease_term_id','tlt.lease_term','sqi.minimum_lease_period','sqi.vehicle_make_model_year','sqi.fuel_included','sqi.driver_availability','sqi.driver_charges');
		$gridBuyer->groupBy('sqi.id');
		/*echo $gridBuyer->tosql();
		$bindings = $gridBuyer->getBindings();
		echo "<pre>";print_R($bindings);
		$results = $gridBuyer->get();
		echo "<pre>";print_R($results);die;*/

		return $gridBuyer;
	}
        
        
        /**
	 * Pet move Buyer Search
	 * @param $params
	 * result $query
	 */
	public static function getRelocationPetBuyerSearchResults($statusId,$params){
		$loginId = Auth::User()->id;
		//echo "<pre>";print_R($params);die;
		// Below script for buyer search for seller posts join query --for Grid
		$gridBuyer = DB::table ( 'relocationpet_seller_post_items as spi' );
		$gridBuyer->leftjoin ( 'relocationpet_seller_posts as sp', 'spi.seller_post_id', '=', 'sp.id' );
		$gridBuyer->join ( 'lkp_cities as cf', 'sp.from_location_id', '=', 'cf.id' );
		$gridBuyer->join ( 'lkp_cities as ct', 'sp.to_location_id', '=', 'ct.id' );		
		$gridBuyer->join ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
		$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
		$gridBuyer->join ( 'lkp_pet_types as lkpt', 'lkpt.id', '=', 'spi.lkp_pet_type_id' );
		$gridBuyer->join ( 'lkp_cage_types as lkct', 'lkct.id', '=', 'spi.lkp_cage_type_id' );
		$gridBuyer->leftjoin ( 'relocationpet_seller_selected_buyers as ssb', 'ssb.seller_post_id', '=', 'sp.id' );
        $gridBuyer->leftjoin ( 'relocationpet_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_id', '=', 'spi.id' );
		$gridBuyer->where('sp.lkp_post_status_id', '=', $statusId);
		$gridBuyer->where('spi.is_private', '=', 0);

		// set from location below varaibles are checking empty or not varaible in buyear search---grid
		if (isset ( $params ['from_location_id'] ) && $params ['from_location_id'] != '') {
			$gridBuyer->Where ( 'sp.from_location_id', $params ['from_location_id'] );
		}
		// set to location
		if (isset ( $params ['to_location_id'] ) && $params ['to_location_id'] != '') {
			$gridBuyer->Where ( 'sp.to_location_id', $params ['to_location_id'] );
		}
		//set pet type
		if (isset ( $params ['selPettype'] ) && $params ['selPettype'] != '') {
			$gridBuyer->Where ( 'spi.lkp_pet_type_id', $params ['selPettype'] );
		}
		//set cage type
		if (isset ( $params ['selCageType'] ) && $params ['selCageType'] != '') {
			$gridBuyer->Where ( 'spi.lkp_cage_type_id', $params ['selCageType'] );
		}
                
		//set district
		if(isset($params['district']) && $params['district']!=''){
			$gridBuyer->where('sp.seller_district_id',$params['district']);
		}			

		// set tracking filter milestoness
		if (isset ( $params ['trackingfilter'] ) && ($params ['trackingfilter'] != '') && is_array($params['trackingfilter']) && !empty($params ['trackingfilter']) ) {
			$gridBuyer->WhereIn ( 'sp.tracking', $params ['trackingfilter'] );
		}

		//dispatch dates
		if(isset($params['from_date']) && $params['from_date']!=''){
			$dispatchflexibleflag = isset($params['dispatch_flexible_hidden']) ? $params['dispatch_flexible_hidden'] : 0;
			$dispatchsame = CommonComponent::convertDateForDatabase(isset($params['date_flexiable']) ? $params['date_flexiable'] : $params['from_date']);
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " + 3 day"));
			$currentdate = date('Y-m-d');
			$dispatch_minus  = ($dispatch_minus >= $currentdate) ? $dispatch_minus : $currentdate;

			if ($dispatchflexibleflag == 1) {
				$gridBuyer->whereRaw ( "( ('$dispatch_minus' between `sp`.`from_date` and `sp`.`to_date`) or  ('$dispatch_plus' between `sp`.`from_date` and `sp`.`to_date`)   )") ;
			}else{
				$gridBuyer->whereRaw ( "( '$dispatchsame' between `sp`.`from_date` and `sp`.`to_date`   )") ;
			}
		}
		if(isset($params['from_date']) && ($params['from_date']!='') && isset($params['to_date']) && ($params['to_date']!='')){
			$deliveryflexibleflag = isset($params['is_delivery_flexible']) ? $params['is_delivery_flexible'] : 0;

			$deliverysame = CommonComponent::convertDateForDatabase($params['to_date']);

			$daysdiff = strtotime($deliverysame) - strtotime($dispatchsame);
			$daysdiff = floor($daysdiff/(60*60*24))+1;
			$daysdiff = ($dispatchflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
			$daysdiff = ($deliveryflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
			$monthsdiff = $daysdiff / 7;
			$gridBuyer->whereRaw ( "( IF(`spi`.`units` = 'Days', `spi`.`transitdays` <= '$daysdiff', `spi`.`transitdays` <= '$monthsdiff'  )  )" );
		}

		//Checking private sellers
		$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_access_id` = 2, `ssb`.`buyer_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			//$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$selectedSellers =$params['selected_users'];
			$gridBuyer->WhereIn ( 'seller_user.id', $selectedSellers );
		}

		//selected payments
		if(isset($params['selected_payments']) && $params['selected_payments']!='') {
			$selectedPayments =$params['selected_payments'];
			$gridBuyer->WhereIn ( 'sp.lkp_payment_mode_id', $selectedPayments );
		}
		$gridBuyer->select ('sp.id as postid','spi.id','sp.from_location_id','sp.to_location_id','sp.from_date','sp.to_date','sp.tracking','seller_user.username','spi.transitdays','cf.city_name as fromcity','ct.city_name as tocity',
                                    'spi.rate_per_cft','pm.payment_mode','sp.terms_conditions','sp.cancellation_charge_price','sp.docket_charge_price','sp.seller_district_id','lkct.cage_type','lkct.cage_weight',
                                    'sp.lkp_payment_mode_id','sp.transaction_id','sp.created_by','seller_user.id as seller_id','spi.lkp_pet_type_id','spi.lkp_cage_type_id','spi.rate_per_cft','spi.units','spi.od_charges',
                         DB::raw("(case when `pbqsqp`.`transit_days` != 0 then pbqsqp.transit_days   when 'pbqsqp.id'=0 then spi.transitdays end) as transitdays"));
		/*echo $gridBuyer->tosql();
		echo "<pre>";
		print_R($gridBuyer->getBindings());
		print_R($gridBuyer->get());die;*/
		return $gridBuyer;
    }
    
    /**
	 * Relocation Int Buyer Search
	 * @param $params
	 * result $query
	 */
	public static function getRelocationAirIntBuyerSearchResults($statusId,$params){
		$loginId = Auth::User()->id;
		
		//echo "<pre>";print_R($params);//exit;
		// Below script for buyer search for seller posts join query --for Grid

		$gridBuyer = DB::table('relocationint_seller_post_air_weight_slabs as spi' );
		$gridBuyer->leftjoin('relocationint_seller_posts as sp', 'spi.seller_post_id', '=', 'sp.id' );
		$gridBuyer->leftjoin('lkp_cities as cf', 'sp.from_location_id', '=', 'cf.id');
		$gridBuyer->leftjoin('lkp_cities as ct', 'sp.to_location_id', '=', 'ct.id');		
		$gridBuyer->leftjoin('users as seller_user', 'seller_user.id', '=', 'sp.seller_id');
		$gridBuyer->leftjoin('lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id');
		$gridBuyer->leftjoin( 'relocationint_seller_selected_buyers as ssb', 'ssb.seller_post_id', '=', 'sp.id' );
                $gridBuyer->leftjoin('relocationint_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_id', '=', 'spi.id');
		$gridBuyer->where('sp.lkp_post_status_id', '=', $statusId);
		$gridBuyer->where('sp.is_private', '=', 0);
        if(!isset($params['district'])){
	        if(isset($params['slab_id']) && $params['slab_id']!='')
				$gridBuyer->where('spi.lkp_air_weight_slab_id', '=', $params['slab_id']);
			else
				$gridBuyer->where('spi.lkp_air_weight_slab_id', '=', 0);
        }
        if(isset($params['weight'])&& $params['weight']!='')
            $weight = $params['weight'];
        else
            $weight=0;

		// set from location below varaibles are checking empty or not varaible in buyear search---grid
		if(isset( $params ['from_location_id'] ) && $params ['from_location_id'] != '') {
			$gridBuyer->Where ( 'sp.from_location_id', $params ['from_location_id'] );
		}
		// set to location
		if (isset ( $params ['to_location_id'] ) && $params ['to_location_id'] != '') {
			$gridBuyer->Where ( 'sp.to_location_id', $params ['to_location_id'] );
		}
		//set district
		if(isset($params['district']) && $params['district']!=''){
			$gridBuyer->where('sp.seller_district_id',$params['district']);
		}	
		// set tracking filter milestoness
		if (isset ( $params ['trackingfilter'] ) && ($params ['trackingfilter'] != '') && is_array($params['trackingfilter']) && !empty($params ['trackingfilter']) ) {
			$gridBuyer->WhereIn ( 'sp.tracking', $params ['trackingfilter'] );
		}
		//dispatch dates
		if(isset($params['from_date']) && $params['from_date']!=''){
			$dispatchflexibleflag = isset($params['dispatch_flexible_hidden']) ? $params['dispatch_flexible_hidden'] : 0;
			$dispatchsame = CommonComponent::convertDateForDatabase(isset($params['date_flexiable']) ? $params['date_flexiable'] : $params['from_date']);
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " + 3 day"));
			$currentdate = date('Y-m-d');
			$dispatch_minus  = ($dispatch_minus >= $currentdate) ? $dispatch_minus : $currentdate;

			if ($dispatchflexibleflag == 1) {
				$gridBuyer->whereRaw ( "( ('$dispatch_minus' between `sp`.`from_date` and `sp`.`to_date`) or  ('$dispatch_plus' between `sp`.`from_date` and `sp`.`to_date`)   )") ;
			}else{
				$gridBuyer->whereRaw ( "( '$dispatchsame' between `sp`.`from_date` and `sp`.`to_date`   )") ;
			}
		}
		if(isset($params['from_date']) && ($params['from_date']!='') && isset($params['to_date']) && ($params['to_date']!='')){
			$deliveryflexibleflag = isset($params['is_delivery_flexible']) ? $params['is_delivery_flexible'] : 0;

			$deliverysame = CommonComponent::convertDateForDatabase($params['to_date']);

			$daysdiff = strtotime($deliverysame) - strtotime($dispatchsame);
			$daysdiff = floor($daysdiff/(60*60*24))+1;
			$daysdiff = ($dispatchflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
			$daysdiff = ($deliveryflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
			$monthsdiff = $daysdiff / 7;
			$gridBuyer->whereRaw ( "( IF(`sp`.`units` = 'Days', `sp`.`transitdays` <= '$daysdiff', `sp`.`transitdays` <= '$monthsdiff'  )  )" );
		}
		
		//Checking private sellers
		$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_access_id` = 2, `ssb`.`buyer_id` =  $loginId,TRUE )  )");
		
		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			//$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$selectedSellers =$params['selected_users'];
			$gridBuyer->WhereIn ( 'seller_user.id', $selectedSellers );
		}
		
		//selected payments
		if(isset($params['selected_payments']) && $params['selected_payments']!='') {
			$selectedPayments =$params['selected_payments'];
			$gridBuyer->WhereIn ( 'sp.lkp_payment_mode_id', $selectedPayments );
		}

		$gridBuyer->select ('sp.id as postid',DB::raw("$weight as weight"),'spi.id','sp.from_location_id','sp.to_location_id','sp.from_date','sp.to_date','sp.tracking','seller_user.username','sp.transitdays','cf.city_name as fromcity','ct.city_name as tocity','spi.freight_charges','pm.payment_mode','sp.terms_conditions', 'sp.storage_charge_price', 'sp.other_charge_price',
			'sp.cancellation_charge_price','sp.seller_district_id', 'sp.lkp_access_id',
            'sp.lkp_payment_mode_id','sp.transaction_id','sp.created_by','seller_user.id as seller_id','spi.lkp_air_weight_slab_id','spi.od_charges',
            DB::raw("(case when `pbqsqp`.`transit_days` != 0 then pbqsqp.transit_days   when 'pbqsqp.id'=0 then sp.transitdays end) as transitdays")
        );

		/*echo $gridBuyer->tosql();
		echo "<pre>";print_R($gridBuyer->getBindings());
		print_R($gridBuyer->get()); die;*/
		return $gridBuyer;
    }

    /**
	 * Relocation Int Buyer Ocean Search
	 * @param $params
	 * result $query
	 */
	public static function getRelocationOceanIntBuyerSearchResults($statusId,$params){

		$loginId = Auth::User()->id;

		// Below script for buyer search for seller posts join query --for Grid
		$gridBuyer = DB::table('relocationint_seller_post_items as spi' );
		$gridBuyer->leftjoin('relocationint_seller_posts as sp', 'spi.seller_post_id', '=', 'sp.id');
		$gridBuyer->leftjoin('lkp_cities as cf', 'sp.from_location_id', '=', 'cf.id');
		$gridBuyer->leftjoin('lkp_cities as ct', 'sp.to_location_id', '=', 'ct.id');		
		$gridBuyer->leftjoin('users as seller_user', 'seller_user.id', '=', 'sp.seller_id');
		$gridBuyer->leftjoin('lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id');

		$gridBuyer->leftjoin( 'relocationint_seller_selected_buyers as ssb', 'ssb.seller_post_id', '=', 'sp.id' );
                $gridBuyer->leftjoin('relocationint_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_id', '=', 'spi.id');
        
        // Added to get the shipment type 
		$gridBuyer->leftjoin('lkp_relocation_shipment_types as lkpSt', 'lkpSt.id', '=', 
			'spi.lkp_relocation_shipment_type_id');

		$gridBuyer->where('sp.lkp_post_status_id', '=', $statusId);
		$gridBuyer->where('spi.is_private', '=', 0);
                if(isset($params['shipment_volume_type_id']))
		$gridBuyer->where('spi.lkp_relocation_shipment_volume_id', '=', $params['shipment_volume_type_id']);
		// set from location below varaibles are checking empty or not varaible in buyear search---grid
		if(isset( $params ['from_location_id_intre'] ) && $params ['from_location_id_intre'] != '') {
			$gridBuyer->Where ( 'sp.from_location_id', $params ['from_location_id_intre'] );
		}
		// set to location
		if (isset ( $params ['to_location_id_intre'] ) && $params ['to_location_id_intre'] != '') {
			$gridBuyer->Where ( 'sp.to_location_id', $params ['to_location_id_intre'] );
		}
		//set district
		if(isset($params['seller_district_id_intre']) && $params['seller_district_id_intre']!=''){
			$gridBuyer->where('sp.seller_district_id',$params['seller_district_id_intre']);
		}	

		// set tracking filter milestoness
		if (isset ( $params ['trackingfilter'] ) && ($params ['trackingfilter'] != '') && is_array($params['trackingfilter']) && !empty($params ['trackingfilter']) ) {
			$gridBuyer->WhereIn ( 'sp.tracking', $params ['trackingfilter'] );
		}

		//dispatch dates
		if(isset($params['valid_from']) && $params['valid_from']!=''){
			$dispatchflexibleflag = isset($params['dispatch_flexible_hidden']) ? $params['dispatch_flexible_hidden'] : 0;
			$dispatchsame = CommonComponent::convertDateForDatabase(isset($params['date_flexiable']) ? $params['date_flexiable'] : $params['valid_from']);
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_from']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_from']) . " + 3 day"));
			$currentdate = date('Y-m-d');
			$dispatch_minus  = ($dispatch_minus >= $currentdate) ? $dispatch_minus : $currentdate;

			if ($dispatchflexibleflag == 1) {
				$gridBuyer->whereRaw ( "( ('$dispatch_minus' between `sp`.`from_date` and `sp`.`to_date`) or  ('$dispatch_plus' between `sp`.`from_date` and `sp`.`to_date`)   )") ;
			}else{
				$gridBuyer->whereRaw ( "( '$dispatchsame' between `sp`.`from_date` and `sp`.`to_date`   )") ;
			}
		}

		if(isset($params['valid_from']) && ($params['valid_from']!='') && isset($params['valid_to']) && ($params['valid_to']!='')){
			$deliveryflexibleflag = isset($params['is_delivery_flexible']) ? $params['is_delivery_flexible'] : 0;

			$deliverysame = CommonComponent::convertDateForDatabase($params['valid_to']);

			$daysdiff = strtotime($deliverysame) - strtotime($dispatchsame);
			$daysdiff = floor($daysdiff/(60*60*24))+1;
			$daysdiff = ($dispatchflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
			$daysdiff = ($deliveryflexibleflag == 1) ? $daysdiff + 3 : $daysdiff;
			$monthsdiff = $daysdiff / 7;
			$gridBuyer->whereRaw ( "( IF(`spi`.`units` = 'Days', `spi`.`transitdays` <= '$daysdiff', `sp`.`transitdays` <= '$monthsdiff'  )  )" );
		}
		
		//Checking private sellers
		$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_access_id` = 2, `ssb`.`buyer_id` =  $loginId,TRUE )  )");
		
		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			//$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$selectedSellers =$params['selected_users'];
			$gridBuyer->WhereIn ( 'seller_user.id', $selectedSellers );
		}
		
		//selected payments
		if(isset($params['selected_payments']) && $params['selected_payments']!='') {
			$selectedPayments =$params['selected_payments'];
			$gridBuyer->WhereIn ( 'sp.lkp_payment_mode_id', $selectedPayments );
		}

		$gridBuyer->select ('sp.id as postid','spi.id','sp.from_location_id','sp.to_location_id','sp.from_date','sp.to_date','sp.tracking','seller_user.username','sp.transitdays','cf.city_name as fromcity','ct.city_name as tocity','spi.freight_charges','pm.payment_mode','sp.terms_conditions', 'sp.storage_charge_price', 'sp.other_charge_price', 'sp.crating_charges', 
			'sp.cancellation_charge_price','sp.seller_district_id', 'sp.lkp_access_id',
            'sp.lkp_payment_mode_id','sp.transaction_id','sp.created_by','seller_user.id as seller_id','spi.od_charges', 'lkpSt.shipment_type',
            'sp.origin_storage', 'sp.destination_storage', 'sp.origin_handyman_services', 'sp.destination_handyman_services', 'sp.unloading_delivery_unpack',
            DB::raw("(case when `pbqsqp`.`transit_days` != 0 then pbqsqp.transit_days   when 'pbqsqp.id'=0 then spi.transitdays end) as transitdays")
        );

		/*echo $gridBuyer->tosql();
		echo "<pre>";print_R($gridBuyer->getBindings());
		print_R($gridBuyer->get());die;*/
		return $gridBuyer;
    }
    
    
    /**
	 * Global mobility Buyer Search
	 * @param $params
	 * result $query
	 */
	public static function getRelocationGmBuyerSearchResults($statusId,$params){
		$loginId = Auth::User()->id;
                $gmServiceTypes = CommonComponent::getAllGMServiceTypesforSeller();
		//echo "<pre>";print_R($params);die;
		// Below script for buyer search for seller posts join query --for Grid
		$gridBuyer = DB::table ( 'relocationgm_seller_posts as sp' );
		//$gridBuyer->join ( 'relocation_seller_post_items as spi', 'sp.id', '=', 'spi.seller_post_id' );
		$gridBuyer->join ( 'lkp_cities as cf', 'sp.location_id', '=', 'cf.id' );
		//$gridBuyer->join ( 'lkp_cities as ct', 'sp.to_location_id', '=', 'ct.id' );
		//$gridBuyer->join('lkp_post_ratecard_types as rt', 'rt.id', '=', 'sp.rate_card_type');
		$gridBuyer->join ( 'users as seller_user', 'seller_user.id', '=', 'sp.seller_id' );
		$gridBuyer->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
		$gridBuyer->leftjoin ( 'relocationgm_seller_selected_buyers as ssb', 'ssb.seller_post_id', '=', 'sp.id' );
                $gridBuyer->leftjoin ( 'relocationgm_buyer_quote_sellers_quotes_prices as pbqsqp', 'pbqsqp.seller_post_id', '=', 'sp.id' );
		$gridBuyer->where('sp.lkp_post_status_id', '=', $statusId);
		$gridBuyer->where('sp.is_private', '=', 0);

		// set to location
		if (isset ( $params ['to_location_id'] ) && $params ['to_location_id'] != '') {
			$gridBuyer->Where ( 'sp.location_id', $params ['to_location_id'] );
		}
		//set district
		if(isset($params['district']) && $params['district']!=''){
			$gridBuyer->where('sp.seller_district_id',$params['district']);
		}
                // service type condition added @ jagadeesh - 250616       
                if(isset($params['relgm_service_type']) && $params['relgm_service_type']){
                        //$service_type =$gmServiceTypes[$params['relgm_service_type']]->service_type;
                               $service_type = CommonComponent::getAllGMServiceTypesById($params['relgm_service_type']);
                   $str_name   =   strtolower(str_replace(' ','_',$service_type));
                   $gridBuyer->whereRaw("sp.$str_name<>''");
               }


		
		//price filter
//		if (isset ( $params ['price'] ) && $params ['price'] != '') {
//			$splitprice = explode("    ",$params ['price']);
//			//echo "<pre>>"; print_R($splitprice); echo "</pre>";
//			$from = trim(filter_var($splitprice[0],FILTER_SANITIZE_NUMBER_INT),"-");
//			$to = trim(filter_var($splitprice[1],FILTER_SANITIZE_NUMBER_INT),"-");
//			$_REQUEST['price_from'] = $from;
//			$_REQUEST['price_to'] = $to;
//       
//		}
		//dispatch dates
		if(isset($params['from_date']) && $params['from_date']!=''){
			$dispatchflexibleflag = isset($params['is_dispatch_flexible']) ? $params['is_dispatch_flexible'] : 0;
			$dispatchsame = CommonComponent::convertDateForDatabase($params['from_date']);
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['from_date']) . " + 3 day"));
			$currentdate = date('Y-m-d');
			$dispatch_minus  = ($dispatch_minus >= $currentdate) ? $dispatch_minus : $currentdate;


			if ($dispatchflexibleflag == 1) {
				$gridBuyer->whereRaw ( "( ('$dispatch_minus' between `sp`.`from_date` and `sp`.`to_date`) or  ('$dispatch_plus' between `sp`.`from_date` and `sp`.`to_date`)   )") ;
			}else{
				$gridBuyer->whereRaw ( "( '$dispatchsame' between `sp`.`from_date` and `sp`.`to_date`   )") ;
				//$gridBuyer->whereRaw ( "( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `bqi`.`dispatch_date` - INTERVAL 3 DAY and `bqi`.`dispatch_date` + INTERVAL 3 DAY  )  )" );
			}

		}
		

		//Checking private sellers
		$gridBuyer->whereRaw ( "( IF(`sp`.`lkp_access_id` = 2, `ssb`.`buyer_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers =$params['selected_users'];
			$gridBuyer->WhereIn ( 'seller_user.id', $selectedSellers );
		}

		//selected payments
		if(isset($params['selected_payments']) && $params['selected_payments']!='') {
			$selectedPayments =$params['selected_payments'];
			$gridBuyer->WhereIn ( 'sp.lkp_payment_mode_id', $selectedPayments );
		}
                
                $services_gm=array();
                foreach($gmServiceTypes as $gmServiceType){
                    //$str_name   =   strtolower(str_replace(' ','_',$gmServiceType->service_type));
                    if(isset($params[$gmServiceType->service_type]) && $params[$gmServiceType->service_type]!=''){
                        $services_gm[] = $gmServiceType->service_type;
                        
                    }
                }
                if(!empty($services_gm)){
                    $i=0;$str="(";
                    foreach($services_gm as $services){
                        
                    $str_name   =   strtolower(str_replace(' ','_',$services));
                    if($i==0){
                    //$gridBuyer->whereRaw("sp.$str_name !=''");
                        $str.="sp.$str_name !='' ";
                    }
                    else{
                        $str.=" or sp.$str_name !='' ";
                    }
                        
                    $i++;
                    }
                    $str.=")";
                    $gridBuyer->whereRaw(" $str");
                }
		$gridBuyer->groupBy('sp.id');
		$gridBuyer->select ('sp.*','seller_user.username','pm.payment_mode','cf.city_name as tocity');
		/*echo $gridBuyer->tosql();
		echo "<pre>";
		print_R($gridBuyer->getBindings());
		print_R($gridBuyer->get());die;*/
		return $gridBuyer;
	}
	
}
