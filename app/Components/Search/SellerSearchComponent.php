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
class SellerSearchComponent {

	public static function search($roleId, $serviceId,$statusId,$params) {
		$monolog = \Log::getMonolog();
		$monolog->pushHandler(new \Monolog\Handler\FirePHPHandler());
		$monolog->addInfo('Seller Search results', array('Buyer search params' => $params,'c'=>1));


		switch($serviceId){
			case ROAD_FTL       :
				$queryBuilder = SellerSearchComponent::getFtlSellerSearchResults(OPEN,$params);
				break;
			case ROAD_PTL:
			case RAIL:
			case AIR_DOMESTIC:
			case COURIER:
				$queryBuilder = SellerSearchComponent::getPtlSellerSearchResults(OPEN,$params,$serviceId = Session::get('service_id'));
				//echo $queryBuilder->tosql();die;
				break;

			case AIR_INTERNATIONAL:
			case OCEAN:
				$queryBuilder = SellerSearchComponent::getAirIntAndOceanSellerSearchResults(OPEN,$params,$serviceId= Session::get('service_id'));
				break;
			case ROAD_INTRACITY :
				//coming soon
				break;
			case ROAD_TRUCK_HAUL:
				$queryBuilder = SellerSearchComponent::getTruckHaulSellerSearchResults(OPEN,$params);
				break;
			case ROAD_TRUCK_LEASE:
				$queryBuilder = SellerSearchComponent::getTruckLeaseSellerSearchResults(OPEN,$params);
				break;
			case RELOCATION_DOMESTIC:
				$queryBuilder = SellerSearchComponent::getRelocationSellerSearchResults(OPEN,$params);
				break;
			case RELOCATION_OFFICE_MOVE:
				$queryBuilder = SellerSearchComponent::getRelocationOfficeSellerSearchResults(OPEN,$params);
				break;
            case RELOCATION_PET_MOVE:
				$queryBuilder = SellerSearchComponent::getRelocationPetSellerSearchResults(OPEN,$params);
				break;
			case RELOCATION_INTERNATIONAL:
				$queryBuilder = SellerSearchComponent::getRelocationInternationalSellerSearchResults(OPEN,$params);
				break;
                        case RELOCATION_GLOBAL_MOBILITY:
				$queryBuilder = SellerSearchComponent::getRelocationGmSellerSearchResults(OPEN,$params);
				break;    
			default:
				break;
		}
		
		$results = $queryBuilder->get();
		$sqlquery = $queryBuilder->tosql();
		$monolog->addInfo('Seller Search query', array('Seller search results' => $sqlquery,'c'=>1));
		$monolog->addInfo('Seller Search results', array('Seller search results' => $results,'c'=>1));
		$monolog->addInfo('Seller Search binding', array('Buyer search binding' => $queryBuilder->getBindings(),'c'=>1));
		return $queryBuilder;
	}

	/**
	 * FTL Seller Search
	 * @param $params
	 * result $query
	 */
	public static function getFtlSellerSearchResults($statusId,$params){
            //print_r($params);//exit;
		$loginId = Auth::User()->id;
		$Query_buyers_for_sellers = DB::table ( 'buyer_quote_items as bqi' );
		$Query_buyers_for_sellers->join( 'lkp_load_types as lt', 'lt.id', '=', 'bqi.lkp_load_type_id' );
		$Query_buyers_for_sellers->join( 'lkp_vehicle_types as vt', 'vt.id', '=', 'bqi.lkp_vehicle_type_id' );
		$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'bqi.from_city_id', '=', 'cf.id' );
		$Query_buyers_for_sellers->join ( 'lkp_cities as ct', 'bqi.to_city_id', '=', 'ct.id' );
		$Query_buyers_for_sellers->join ( 'buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
		$Query_buyers_for_sellers->join ( 'users as us', 'us.id', '=', 'bq.buyer_id' );
		$Query_buyers_for_sellers->leftjoin ( 'buyer_quote_selected_sellers as bqss', 'bqss.buyer_quote_id', '=', 'bq.id' );


		if(isset($statusId) && $statusId != ''){
			$Query_buyers_for_sellers->where('bqi.lkp_post_status_id', $statusId);
		}
		//set from location below varaibles are checking empty or not varaible in buyear search---grid
		if(isset($params['from_city_id']) && $params['from_city_id']!=''){
			$Query_buyers_for_sellers->where('bqi.from_city_id', $params['from_city_id']);
		}
		//set to location
		if(isset($params['to_city_id']) && $params['to_city_id']!=''){
			$Query_buyers_for_sellers->where('bqi.to_city_id',$params['to_city_id']);
		}
		//set load type
		if(isset($params['lkp_load_type_id']) && ($params['lkp_load_type_id']!='') && ($params['lkp_load_type_id'] != 11)){
                    //echo "here".$params['lkp_load_type_id'];exit;
			//$Query_buyers_for_sellers->where('bqi.lkp_load_type_id', $params['lkp_load_type_id']);
			$loadtypeid = $params ['lkp_load_type_id'];
			$Query_buyers_for_sellers->whereRaw ( "`bqi`.`lkp_load_type_id` = $loadtypeid");
		}
		//set vehicle type
		if(isset($params['lkp_vehicle_type_id']) && ($params['lkp_vehicle_type_id']!='') && ($params['lkp_vehicle_type_id'] != 20)){
			//$Query_buyers_for_sellers->where('bqi.lkp_vehicle_type_id', $params['lkp_vehicle_type_id']);
			$vehicletypeid = $params ['lkp_vehicle_type_id'];
			$Query_buyers_for_sellers->whereRaw ( "`bqi`.`lkp_vehicle_type_id` = $vehicletypeid");
		}
		//pricing type
		if(isset($params['lkp_quote_price_type_id']) && $params['lkp_quote_price_type_id']!=''){
			$Query_buyers_for_sellers->where('bqi.lkp_quote_price_type_id',$params['lkp_quote_price_type_id']);
		}


		//dispatch dates
		if(isset($params['dispatch_date']) && $params['dispatch_date']!=''){
			//$dispatchflexibleflag = isset($params['dispatch_flexible_hidden']) ? $params['dispatch_flexible_hidden'] : 0;
			$dispatchflexibleflag = 0;

			if(isset($params['selected_flexible_dispatch']) &&  $params['selected_flexible_dispatch'] != ''){
				$dispatchsame = CommonComponent::convertDateForDatabase($params['selected_flexible_dispatch']);
			}else{
				$dispatchsame = CommonComponent::convertDateForDatabase($params['dispatch_date']);
			}		
			
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " + 3 day"));

			if ($dispatchflexibleflag == 1) {
				//$Query_buyers_for_sellers->where ( DB::raw("( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` between '$dispatch_minus' and '$dispatch_plus', ((`bqi`.`dispatch_date` - INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') or (`bqi`.`dispatch_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") );
				$Query_buyers_for_sellers->whereRaw ( "( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` between '$dispatch_minus' and '$dispatch_plus', ((`bqi`.`dispatch_date` - INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') or (`bqi`.`dispatch_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") ;
			}else{
				//$Query_buyers_for_sellers->where ( DB::raw("( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `bqi`.`dispatch_date` - INTERVAL 3 DAY and `bqi`.`dispatch_date` + INTERVAL 3 DAY  )  )") );
				$Query_buyers_for_sellers->whereRaw ( "( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `bqi`.`dispatch_date` - INTERVAL 3 DAY and `bqi`.`dispatch_date` + INTERVAL 3 DAY  )  )" );
			}
		}
		//Delivery dates
		if(isset($params['delivery_date']) && $params['delivery_date']!=''){
			$deliveryflexibleflag = isset($params['delivery_flexible_hidden']) ? $params['delivery_flexible_hidden'] : 0;

			if(isset($params['selected_flexible_delivery']) &&  $params['selected_flexible_delivery'] != ''){
				$deliverysame = CommonComponent::convertDateForDatabase($params['selected_flexible_delivery']);
			}else{
				$deliverysame = CommonComponent::convertDateForDatabase($params['delivery_date']);
			}

			$delivery_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['delivery_date']) . " - 3 day"));
			$delivery_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['delivery_date']) . " + 3 day"));
			if ($deliveryflexibleflag == 1) {
				//$Query_buyers_for_sellers->where ( DB::raw("( IF(`bqi`.`is_delivery_flexible` = 0, `bqi`.`delivery_date` between '$delivery_minus' and '$delivery_plus', ((`bqi`.`delivery_date` - INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') or (`bqi`.`delivery_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") );
				$Query_buyers_for_sellers->whereRaw ( "( IF(`bqi`.`is_delivery_flexible` = 0, `bqi`.`delivery_date` between '$delivery_minus' and '$delivery_plus', ((`bqi`.`delivery_date` - INTERVAL 3 DAY between '$delivery_minus' and '$delivery_plus') or (`bqi`.`delivery_date` + INTERVAL 3 DAY between '$delivery_minus' and '$delivery_plus') ) )  )") ;
			}else{
				//$Query_buyers_for_sellers->where ( DB::raw("( IF(`bqi`.`is_delivery_flexible` = 0, `bqi`.`delivery_date` = '$dispatchsame', '$dispatchsame' between `bqi`.`delivery_date` - INTERVAL 3 DAY and `bqi`.`delivery_date` + INTERVAL 3 DAY  )  )") );
				$Query_buyers_for_sellers->whereRaw ( "( IF(`bqi`.`is_delivery_flexible` = 0, `bqi`.`delivery_date` >= '$deliverysame', '$deliverysame' between `bqi`.`delivery_date` - INTERVAL 3 DAY and `bqi`.`delivery_date` + INTERVAL 3 DAY  )  )") ;
			}
		}

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`bq`.`lkp_quote_access_id` = 2, `bqss`.`seller_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$Query_buyers_for_sellers->WhereIn ( 'bq.buyer_id', $selectedSellers );
		}
		
		if(isset($params['selected_prices']) && $params['selected_prices']!='') {
			$selectedPrices = array_filter(explode(",", $params['selected_prices']));
			$Query_buyers_for_sellers->WhereIn ( 'bqi.lkp_quote_price_type_id', $selectedPrices );
		}
		if(isset($params['selected_from_date']) && $params['selected_from_date']!='') {
			$selected_from_date = array_filter(explode(",", $params['selected_from_date']));
			$Query_buyers_for_sellers->WhereIn ( 'bqi.dispatch_date', $selected_from_date );
		}
		if(isset($params['selected_to_date']) && $params['selected_to_date']!='') {
			$selected_to_date = array_filter(explode(",", $params['selected_to_date']));
			$Query_buyers_for_sellers->WhereIn ( 'bqi.delivery_date', $selected_to_date );
		}
		if(isset($params['selected_load_type_id']) && $params['selected_load_type_id']!='' && $params['selected_load_type_id']!=11) {
                    //echo "here".$params['selected_load_type_id'];exit;
			$selected_loadtype = $params['selected_load_type_id'];
			$Query_buyers_for_sellers->Where ( 'bqi.lkp_load_type_id', $selected_loadtype );
		}
		if(isset($params['selected_vehicle_type_id']) && $params['selected_vehicle_type_id']!='' && $params['selected_vehicle_type_id']!=20) {
			$selected_vehicletype = $params['selected_vehicle_type_id'];
			$Query_buyers_for_sellers->Where ( 'bqi.lkp_vehicle_type_id', $selected_vehicletype );
		}

		$Query_buyers_for_sellers->select ('bq.transaction_id','bq.lkp_quote_access_id','bqi.id','cf.city_name as fromcity',
			'ct.city_name as tocity', 'lt.load_type','vt.vehicle_type','bqi.dispatch_date','bqi.created_by','bqi.lkp_quote_price_type_id',
			'bqi.price','bqi.units','us.username','bqi.delivery_date','bqi.dispatch_date', 'bqi.quantity' ,'bqi.number_loads','bqi.lkp_post_status_id','bq.buyer_id','us.username'
		);
		//echo $Query_buyers_for_sellers->tosql ();die;
		return $Query_buyers_for_sellers;
		//$result = $Query_buyers_for_sellers->get ();
	}
	/**
	 * PTL Seller Search
	 * @param $params
	 * result $query
	 */

	public static function getPtlSellerSearchResults($statusId,$params,$serviceId)
	{
	//exit;
		
		$loginId = Auth::User()->id;
		//echo "<pre>";print_R($params);
		if($serviceId == ROAD_PTL){
			$Query_buyers_for_sellers = DB::table('ptl_buyer_quote_items as pbqi');
			$Query_buyers_for_sellers->join('ptl_buyer_quotes as pbq', 'pbq.id', '=', 'pbqi.buyer_quote_id');
			$Query_buyers_for_sellers->join('lkp_load_types as lt', 'lt.id', '=', 'pbqi.lkp_load_type_id');
			$Query_buyers_for_sellers->join('lkp_packaging_types as pt', 'pt.id', '=', 'pbqi.lkp_packaging_type_id');
			$Query_buyers_for_sellers->join('lkp_ptl_pincodes as lppf', 'pbq.from_location_id', '=', 'lppf.id');
			$Query_buyers_for_sellers->join('lkp_ptl_pincodes as lppt', 'pbq.to_location_id', '=', 'lppt.id');
			$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'pbq.buyer_id');
			$Query_buyers_for_sellers->leftjoin ( 'ptl_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'pbq.id' );
		}else if($serviceId == RAIL){
			$Query_buyers_for_sellers = DB::table('rail_buyer_quote_items as pbqi');
			$Query_buyers_for_sellers->join('rail_buyer_quotes as pbq', 'pbq.id', '=', 'pbqi.buyer_quote_id');
			$Query_buyers_for_sellers->join('lkp_load_types as lt', 'lt.id', '=', 'pbqi.lkp_load_type_id');
			$Query_buyers_for_sellers->join('lkp_packaging_types as pt', 'pt.id', '=', 'pbqi.lkp_packaging_type_id');
			$Query_buyers_for_sellers->join('lkp_ptl_pincodes as lppf', 'pbq.from_location_id', '=', 'lppf.id');
			$Query_buyers_for_sellers->join('lkp_ptl_pincodes as lppt', 'pbq.to_location_id', '=', 'lppt.id');
			$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'pbq.buyer_id');
			$Query_buyers_for_sellers->leftjoin ( 'rail_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'pbq.id' );
		}else if($serviceId == AIR_DOMESTIC){
			$Query_buyers_for_sellers = DB::table('airdom_buyer_quote_items as pbqi');
			$Query_buyers_for_sellers->join('airdom_buyer_quotes as pbq', 'pbq.id', '=', 'pbqi.buyer_quote_id');
			$Query_buyers_for_sellers->join('lkp_load_types as lt', 'lt.id', '=', 'pbqi.lkp_load_type_id');
			$Query_buyers_for_sellers->join('lkp_packaging_types as pt', 'pt.id', '=', 'pbqi.lkp_packaging_type_id');
			$Query_buyers_for_sellers->join('lkp_ptl_pincodes as lppf', 'pbq.from_location_id', '=', 'lppf.id');
			$Query_buyers_for_sellers->join('lkp_ptl_pincodes as lppt', 'pbq.to_location_id', '=', 'lppt.id');
			$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'pbq.buyer_id');
			$Query_buyers_for_sellers->leftjoin ( 'airdom_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'pbq.id' );
		}else if($serviceId == COURIER){
			$Query_buyers_for_sellers = DB::table('courier_buyer_quote_items as pbqi');
			$Query_buyers_for_sellers->join('courier_buyer_quotes as pbq', 'pbq.id', '=', 'pbqi.buyer_quote_id');
			$Query_buyers_for_sellers->join('lkp_courier_types as lt', 'lt.id', '=', 'pbqi.lkp_courier_type_id');
			$Query_buyers_for_sellers->join('lkp_courier_delivery_types as pt', 'pt.id', '=', 'pbqi.lkp_courier_delivery_type_id');
			$Query_buyers_for_sellers->join('lkp_ptl_pincodes as lppf', 'pbq.from_location_id', '=', 'lppf.id');
			//$Query_buyers_for_sellers->join('lkp_ptl_pincodes as lppt', 'pbq.to_location_id', '=', 'lppt.id');
			$Query_buyers_for_sellers->leftjoin('lkp_ptl_pincodes as lppt', function($join)
			{
				$join->on('pbq.to_location_id', '=', 'lppt.id');
				$join->on(DB::raw('pbqi.lkp_courier_delivery_type_id'),'=',DB::raw(1));
			
			});
			$Query_buyers_for_sellers->leftjoin('lkp_countries as lppt1', function($join)
			{
				$join->on('pbq.to_location_id', '=', 'lppt1.id');
				$join->on(DB::raw('pbqi.lkp_courier_delivery_type_id'),'=',DB::raw(2));
			
			});
				
			
			$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'pbq.buyer_id');
			$Query_buyers_for_sellers->leftjoin ( 'courier_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'pbq.id' );
		}


		if (isset($statusId) && $statusId != '') {
			$Query_buyers_for_sellers->where('pbqi.lkp_post_status_id', $statusId);
		}
		//Zone or location
		$zone_or_location = isset($params['zone_or_location']) ? $params['zone_or_location'] : 1;
		if ($zone_or_location == 2){
			
			//set from location below varaibles are checking empty or not varaible in buyear search---grid
			if (isset($params['from_location_id']) && $params['from_location_id'] != '') {
				$allRelatedFromPinIds = SellerSearchComponent::getAllPincodeIds($params['from_location_id']);
				$Query_buyers_for_sellers->whereIn('pbq.from_location_id', $allRelatedFromPinIds);
			}
			//set to location
			if (isset($params['to_location_id']) && $params['to_location_id'] != '') {
				$allRelatedToPinIds = SellerSearchComponent::getAllPincodeIds($params['to_location_id']);
				$Query_buyers_for_sellers->whereIn('pbq.to_location_id', $allRelatedToPinIds);
			}
		}else{
			
			if (isset($params['from_location_id']) && $params['from_location_id'] != '') {
				$frompincodes = SellerSearchComponent::getPincodesByZoneId($params['from_location_id']);
				$Query_buyers_for_sellers->whereIn('pbq.from_location_id', $frompincodes);
				
			}
			if($serviceId==COURIER){
				if(isset($params['post_or_delivery_type']) && $params['post_or_delivery_type']==1){
					$topincodes = SellerSearchComponent::getPincodesByZoneId($params['to_location_id']);
					$Query_buyers_for_sellers->whereIn('pbq.to_location_id', $topincodes);
				}else{
					$Query_buyers_for_sellers->where('pbq.to_location_id', $params['to_location_id']);
				}
			}else{
				$topincodes = SellerSearchComponent::getPincodesByZoneId($params['to_location_id']);
				$Query_buyers_for_sellers->whereIn('pbq.to_location_id', $topincodes);
			}
			
		}
		//pricing type
		if(isset($params['lkp_quote_price_type_id']) && $params['lkp_quote_price_type_id']!=''){
			$Query_buyers_for_sellers->where('pbqi.lkp_quote_price_type_id',$params['lkp_quote_price_type_id']);
		}
		
		//set to Load type
		if(isset($params['lkp_load_type_id']) && $params['lkp_load_type_id']!=''){
			if($params['lkp_load_type_id'] != 11){
				//$Query_buyers_for_sellers->where('pbqi.lkp_load_type_id',$params['lkp_load_type_id']);
				$loadtypeid = $params['lkp_load_type_id'];
				$Query_buyers_for_sellers->whereRaw ( "( IF(`pbqi`.`lkp_load_type_id` != 11, `pbqi`.`lkp_load_type_id` = $loadtypeid,TRUE )  )");
			}
		}
		//set to Package type
		if(isset($params['lkp_packaging_type_id']) && $params['lkp_packaging_type_id']!=''){
			$Query_buyers_for_sellers->where('pbqi.lkp_packaging_type_id',$params['lkp_packaging_type_id']);
		}
		if(isset($params['dispatch_date']) && $params['dispatch_date']!=''){
			$dispatchflexibleflag = isset($params['dispatch_flexible_hidden']) ? $params['dispatch_flexible_hidden'] : 0;
			
                        if(isset($params['date_flexiable']) &&  $params['date_flexiable'] != ''){
				$dispatchsame = CommonComponent::convertDateForDatabase($params['date_flexiable']);
			}else{
				$dispatchsame = CommonComponent::convertDateForDatabase($params['dispatch_date']);
			}
                        //$dispatchsame = CommonComponent::convertDateForDatabase($params['dispatch_date']);
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " + 3 day"));
			if($serviceId!=COURIER){
				if ($dispatchflexibleflag == 1 && !isset($params['is_filter'])) {//echo "here";exit;
					
					$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`is_dispatch_flexible` = 0, `pbq`.`dispatch_date` between '$dispatch_minus' and '$dispatch_plus', ((`pbq`.`dispatch_date` - INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') or (`pbq`.`dispatch_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") ;
				}else{
					//echo "hi";exit;
					$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`is_dispatch_flexible` = 0, `pbq`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `pbq`.`dispatch_date` - INTERVAL 3 DAY and `pbq`.`dispatch_date` + INTERVAL 3 DAY  )  )") ;
				}
			}else{
				$Query_buyers_for_sellers->whereRaw ( "( IF(1=1, `pbq`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `pbq`.`dispatch_date` - INTERVAL 3 DAY and `pbq`.`dispatch_date` + INTERVAL 3 DAY  )  )") ;
				
			}
		}

		if(isset($params['delivery_date']) && $params['delivery_date']!=''){
			$deliveryflexibleflag = isset($params['delivery_flexible_hidden']) ? $params['delivery_flexible_hidden'] : 0;
			$deliverysame = CommonComponent::convertDateForDatabase($params['delivery_date']);

			$delivery_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['delivery_date']) . " - 3 day"));
			$delivery_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['delivery_date']) . " + 3 day"));
			if ($deliveryflexibleflag == 1) {
				//$Query_buyers_for_sellers->where ( DB::raw("( IF(`pbq`.`is_delivery_flexible` = 0, `pbq`.`delivery_date` between '$delivery_minus' and '$delivery_plus', ((`pbq`.`delivery_date` - INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') or (`pbq`.`delivery_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") );
				$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`is_delivery_flexible` = 0, `pbq`.`delivery_date` between '$delivery_minus' and '$delivery_plus', ((`pbq`.`delivery_date` - INTERVAL 3 DAY between '$delivery_minus' and '$delivery_plus') or (`pbq`.`delivery_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") ;
			}else{
				//$Query_buyers_for_sellers->where ( DB::raw("( IF(`pbq`.`is_delivery_flexible` = 0, `pbq`.`delivery_date` = '$dispatchsame', '$dispatchsame' between `pbq`.`delivery_date` - INTERVAL 3 DAY and `pbq`.`delivery_date` + INTERVAL 3 DAY  )  )") );
				if($serviceId!=COURIER){
					$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`is_delivery_flexible` = 0, `pbq`.`delivery_date` >= '$deliverysame', '$deliverysame' between `pbq`.`delivery_date` - INTERVAL 3 DAY and `pbq`.`delivery_date` + INTERVAL 3 DAY  )  )") ;
				}
				else{
					$Query_buyers_for_sellers->whereRaw ( "( IF(1=1, `pbq`.`delivery_date` >= '$deliverysame', '$deliverysame' between `pbq`.`delivery_date` - INTERVAL 3 DAY and `pbq`.`delivery_date` + INTERVAL 3 DAY  )  )") ;
				}
					
			}
		}
		if($serviceId==COURIER){
			$Query_buyers_for_sellers->where('pbqi.lkp_courier_type_id', $params['courier_or_types']);	
		}
		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`lkp_quote_access_id` = 2, `pbqss`.`seller_id` =  $loginId,TRUE )  )");
		if($serviceId!=COURIER){
			$QueryBuilder =  $Query_buyers_for_sellers->select ('pbq.transaction_id','pbq.buyer_id','pbqi.*','pbqi.buyer_quote_id','pbq.lkp_quote_access_id','lppf.pincode as frompincode',
				'lppt.pincode as topincode', 'lt.load_type','pt.packaging_type_name','pbq.dispatch_date','pbqi.created_by','pbq.is_door_pickup','pbq.is_door_delivery',
				'us.username','pbq.delivery_date', 'pbqi.lkp_post_status_id','pbq.from_location_id','pbq.to_location_id'
			);
		}
		else{
			
			$QueryBuilder =  $Query_buyers_for_sellers->select ('pbq.transaction_id','pbq.buyer_id','pbqi.*','pbqi.buyer_quote_id','pbq.lkp_quote_access_id','lppf.pincode as frompincode',
					'lppt.pincode as topincode', 'lt.courier_type','pt.courier_delivery_type','pbq.dispatch_date','pbqi.created_by',
					'us.username','pbq.delivery_date', 'pbqi.lkp_post_status_id','pbq.from_location_id','pbq.to_location_id'
			);
		}
		$QueryBuilder->groupBy("pbqi.buyer_quote_id");
		//echo $Query_buyers_for_sellers->tosql();die;
		//$result = $Query_buyers_for_sellers->get();
		//echo "<pre>"; print_R($result);die;
		return $QueryBuilder;
	}

	//Air International and Ocean
	public static function getAirIntAndOceanSellerSearchResults($statusId,$params,$serviceId)
	{
		$loginId = Auth::User()->id;
		//echo "<pre>";print_R($params);die;
		if($serviceId == AIR_INTERNATIONAL){
			$Query_buyers_for_sellers = DB::table('airint_buyer_quote_items as pbqi');
			$Query_buyers_for_sellers->join('airint_buyer_quotes as pbq', 'pbq.id', '=', 'pbqi.buyer_quote_id');
			$Query_buyers_for_sellers->join('lkp_load_types as lt', 'lt.id', '=', 'pbqi.lkp_load_type_id');
			$Query_buyers_for_sellers->join('lkp_packaging_types as pt', 'pt.id', '=', 'pbqi.lkp_packaging_type_id');
			$Query_buyers_for_sellers->join('lkp_airports as lppf', 'pbq.from_location_id', '=', 'lppf.id');
			$Query_buyers_for_sellers->join('lkp_airports as lppt', 'pbq.to_location_id', '=', 'lppt.id');
			$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'pbq.buyer_id');
			$Query_buyers_for_sellers->leftjoin ( 'airint_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'pbq.id' );
		}else if($serviceId == OCEAN){
			$Query_buyers_for_sellers = DB::table('ocean_buyer_quote_items as pbqi');
			$Query_buyers_for_sellers->join('ocean_buyer_quotes as pbq', 'pbq.id', '=', 'pbqi.buyer_quote_id');
			$Query_buyers_for_sellers->join('lkp_load_types as lt', 'lt.id', '=', 'pbqi.lkp_load_type_id');
			$Query_buyers_for_sellers->join('lkp_packaging_types as pt', 'pt.id', '=', 'pbqi.lkp_packaging_type_id');
			$Query_buyers_for_sellers->join('lkp_seaports as lppf', 'pbq.from_location_id', '=', 'lppf.id');
			$Query_buyers_for_sellers->join('lkp_seaports as lppt', 'pbq.to_location_id', '=', 'lppt.id');
			$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'pbq.buyer_id');
			$Query_buyers_for_sellers->leftjoin ( 'ocean_buyer_quote_selected_sellers as pbqss', 'pbqss.buyer_quote_id', '=', 'pbq.id' );
		}


		if (isset($statusId) && $statusId != '') {
			$Query_buyers_for_sellers->where('pbqi.lkp_post_status_id', $statusId);
		}


		//set from location below varaibles are checking empty or not varaible in buyear search---grid
		if (isset($params['from_location_id']) && $params['from_location_id'] != '') {
			$Query_buyers_for_sellers->where('pbq.from_location_id', $params['from_location_id']);
		}
		//set to location
		if (isset($params['to_location_id']) && $params['to_location_id'] != '') {
			$Query_buyers_for_sellers->where('pbq.to_location_id', $params['to_location_id']);
		}
		//pricing type
		if(isset($params['lkp_quote_price_type_id']) && $params['lkp_quote_price_type_id']!=''){
			$Query_buyers_for_sellers->where('pbqi.lkp_quote_price_type_id',$params['lkp_quote_price_type_id']);
		}

		//print_R($frompincodes);print_R($topincodes);die;
		//set to Load type
		if(isset($params['lkp_load_type_id']) && $params['lkp_load_type_id']!=''){
			if($params['lkp_load_type_id'] != 11){
				//$Query_buyers_for_sellers->where('pbqi.lkp_load_type_id',$params['lkp_load_type_id']);
				$loadtypeid = $params['lkp_load_type_id'];
				$Query_buyers_for_sellers->whereRaw ( "( IF(`pbqi`.`lkp_load_type_id` != 11, `pbqi`.`lkp_load_type_id` = $loadtypeid,TRUE )  )");
			}
		}
		//set to Package type
		if(isset($params['lkp_packaging_type_id']) && $params['lkp_packaging_type_id']!=''){
			$Query_buyers_for_sellers->where('pbqi.lkp_packaging_type_id',$params['lkp_packaging_type_id']);
		}

		//Shipment type
		if(isset($params['lkp_air_ocean_shipment_type_id']) && $params['lkp_air_ocean_shipment_type_id']!=''){
			$Query_buyers_for_sellers->where('pbq.lkp_air_ocean_shipment_type_id',$params['lkp_air_ocean_shipment_type_id']);
		}

		//Sender Identity
		if(isset($params['lkp_air_ocean_sender_identity_id']) && $params['lkp_air_ocean_sender_identity_id']!=''){
			$Query_buyers_for_sellers->where('pbq.lkp_air_ocean_sender_identity_id',$params['lkp_air_ocean_sender_identity_id']);
		}
		
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers = array_filter(explode(",", $params['selected_users']));
			//$selectedSellers =$params['selected_users'];
			$Query_buyers_for_sellers->WhereIn ( 'us.id', $selectedSellers );
		}
		

		if(isset($params['dispatch_date']) && $params['dispatch_date']!=''){
			$dispatchflexibleflag = isset($params['dispatch_flexible_hidden']) ? $params['dispatch_flexible_hidden'] : 0;
			if(isset($params['date_flexiable']) &&  $params['date_flexiable'] != ''){
				$dispatchsame = CommonComponent::convertDateForDatabase($params['date_flexiable']);
			}else{
				$dispatchsame = CommonComponent::convertDateForDatabase($params['dispatch_date']);
			}
                        //$dispatchsame = CommonComponent::convertDateForDatabase($params['dispatch_date']);
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " + 3 day"));

			if ($dispatchflexibleflag == 1) {
				//$Query_buyers_for_sellers->where ( DB::raw("( IF(`pbq`.`is_dispatch_flexible` = 0, `pbq`.`dispatch_date` between '$dispatch_minus' and '$dispatch_plus', ((`pbq`.`dispatch_date` - INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') or (`pbq`.`dispatch_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") );
				$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`is_dispatch_flexible` = 0, `pbq`.`dispatch_date` between '$dispatch_minus' and '$dispatch_plus', ((`pbq`.`dispatch_date` - INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') or (`pbq`.`dispatch_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") ;
			}else{
				//$Query_buyers_for_sellers->where ( DB::raw("( IF(`pbq`.`is_dispatch_flexible` = 0, `pbq`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `pbq`.`dispatch_date` - INTERVAL 3 DAY and `pbq`.`dispatch_date` + INTERVAL 3 DAY  )  )") );
				$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`is_dispatch_flexible` = 0, `pbq`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `pbq`.`dispatch_date` - INTERVAL 3 DAY and `pbq`.`dispatch_date` + INTERVAL 3 DAY  )  )") ;
			}
		}

		if(isset($params['delivery_date']) && $params['delivery_date']!=''){
			$deliveryflexibleflag = isset($params['delivery_flexible_hidden']) ? $params['delivery_flexible_hidden'] : 0;
			$deliverysame = CommonComponent::convertDateForDatabase($params['delivery_date']);

			$delivery_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['delivery_date']) . " - 3 day"));
			$delivery_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['delivery_date']) . " + 3 day"));
			if ($deliveryflexibleflag == 1) {
				//$Query_buyers_for_sellers->where ( DB::raw("( IF(`pbq`.`is_delivery_flexible` = 0, `pbq`.`delivery_date` between '$delivery_minus' and '$delivery_plus', ((`pbq`.`delivery_date` - INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') or (`pbq`.`delivery_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") );
				$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`is_delivery_flexible` = 0, `pbq`.`delivery_date` between '$delivery_minus' and '$delivery_plus', ((`pbq`.`delivery_date` - INTERVAL 3 DAY between '$delivery_minus' and '$delivery_plus') or (`pbq`.`delivery_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") ;
			}else{
				//$Query_buyers_for_sellers->where ( DB::raw("( IF(`pbq`.`is_delivery_flexible` = 0, `pbq`.`delivery_date` = '$dispatchsame', '$dispatchsame' between `pbq`.`delivery_date` - INTERVAL 3 DAY and `pbq`.`delivery_date` + INTERVAL 3 DAY  )  )") );
				$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`is_delivery_flexible` = 0, `pbq`.`delivery_date` >= '$deliverysame', '$deliverysame' between `pbq`.`delivery_date` - INTERVAL 3 DAY and `pbq`.`delivery_date` + INTERVAL 3 DAY  )  )") ;
			}
		}

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`lkp_quote_access_id` = 2, `pbqss`.`seller_id` =  $loginId,TRUE )  )");
		if($serviceId == AIR_INTERNATIONAL){
			$QueryBuilder =  $Query_buyers_for_sellers->select ('pbq.transaction_id','pbq.buyer_id','pbqi.*','pbqi.buyer_quote_id','pbq.lkp_quote_access_id','lppf.airport_name as frompincode','lppf.id as fromairportid',
				'lppt.airport_name as topincode','lppt.id as toairportid', 'lt.load_type','pt.packaging_type_name','pbq.dispatch_date','pbqi.created_by',
				'us.username','pbq.delivery_date', 'pbqi.lkp_post_status_id','pbq.from_location_id','pbq.to_location_id'
			);
		}else if($serviceId == OCEAN){
			$QueryBuilder =  $Query_buyers_for_sellers->select ('pbq.transaction_id','pbq.buyer_id','pbqi.*','pbqi.buyer_quote_id','pbq.lkp_quote_access_id','lppf.seaport_name as frompincode','lppf.id as fromairportid',
				'lppt.seaport_name as topincode','lppt.id as toairportid', 'lt.load_type','pt.packaging_type_name','pbq.dispatch_date','pbqi.created_by',
				'us.username','pbq.delivery_date', 'pbqi.lkp_post_status_id','pbq.from_location_id','pbq.to_location_id'
			);
		}

		$QueryBuilder->groupBy("pbqi.buyer_quote_id");
		//echo $Query_buyers_for_sellers->tosql();die;
		//$result = $Query_buyers_for_sellers->get();
		//echo "<pre>"; print_R($result);die;
		return $QueryBuilder;
	}


	/**
	 * Relocation Seller Search
	 * @param $params
	 * result $query
	 */
	public static function getRelocationSellerSearchResults($statusId,$params){
		//echo "<pre>";print_R($params);die;
		$loginId = Auth::User()->id;
		$Query_buyers_for_sellers = DB::table('relocation_buyer_posts as rbq');
		$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id' );
		$Query_buyers_for_sellers->join ( 'lkp_cities as ct', 'rbq.to_location_id', '=', 'ct.id' );
		$Query_buyers_for_sellers->join('lkp_post_ratecard_types as rt', 'rt.id', '=', 'rbq.lkp_post_ratecard_type_id');
		$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'rbq.buyer_id');		
		$Query_buyers_for_sellers->leftjoin('lkp_property_types as pty', 'pty.id', '=', 'rbq.lkp_property_type_id');
		$Query_buyers_for_sellers->leftjoin('lkp_vechicle_categorie_types as vct', 'vct.id', '=', 'rbq.lkp_vehicle_category_type_id');
		$Query_buyers_for_sellers->leftjoin('lkp_load_categories as lcat', 'lcat.id', '=', 'rbq.lkp_load_category_id');
		$Query_buyers_for_sellers->leftjoin ( 'relocation_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id' );

		if (isset($statusId) && $statusId != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', $statusId);
		}

		//set from location below varaibles are checking empty or not varaible in buyear search---grid
		if (isset($params['from_location_id']) && $params['from_location_id'] != '') {
			$Query_buyers_for_sellers->where('rbq.from_location_id', $params['from_location_id']);
		}
		//set to location
		if (isset($params['to_location_id']) && $params['to_location_id'] != '') {
			$Query_buyers_for_sellers->where('rbq.to_location_id', $params['to_location_id']);
		}

		//set to location
		if (isset($params['post_type']) && $params['post_type'] != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_post_ratecard_type_id', $params['post_type']);
		}
		//set property type
		if (isset($params['property_type']) && $params['property_type'] != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_property_type_id', $params['property_type']);
		}
		//set load type
		if (isset($params['load_type']) && $params['load_type'] != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_load_category_id', $params['load_type']);
		}
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			//$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$selectedSellers =$params['selected_users'];
			$Query_buyers_for_sellers->WhereIn ( 'us.id', $selectedSellers );
		}

		//dispatch dates
		if(isset($params['dispatch_date']) && $params['dispatch_date']!=''){
			$dispatchflexibleflag = 0;
			$dispatchsame = CommonComponent::convertDateForDatabase($params['dispatch_date']);
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " + 3 day"));

			if ($dispatchflexibleflag == 1) {
				$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`is_dispatch_flexible` = 0, `rbq`.`dispatch_date` between '$dispatch_minus' and '$dispatch_plus', ((`rbq`.`dispatch_date` - INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') or (`rbq`.`dispatch_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") ;
			}else{
				$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`is_dispatch_flexible` = 0, `rbq`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `rbq`.`dispatch_date` - INTERVAL 3 DAY and `rbq`.`dispatch_date` + INTERVAL 3 DAY  )  )" );
			}
		}
		//Delivery dates
		if(isset($params['delivery_date']) && $params['delivery_date']!=''){
			$deliveryflexibleflag = isset($params['delivery_flexible_hidden']) ? $params['delivery_flexible_hidden'] : 0;
			$deliverysame = CommonComponent::convertDateForDatabase($params['delivery_date']);
			$delivery_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['delivery_date']) . " - 3 day"));
			$delivery_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['delivery_date']) . " + 3 day"));
			if ($deliveryflexibleflag == 1) {
				$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`is_delivery_flexible` = 0, `rbq`.`delivery_date` between '$delivery_minus' and '$delivery_plus', ((`rbq`.`delivery_date` - INTERVAL 3 DAY between '$delivery_minus' and '$delivery_plus') or (`rbq`.`delivery_date` + INTERVAL 3 DAY between '$delivery_minus' and '$delivery_plus') ) )  )") ;
			}else{
				$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`is_delivery_flexible` = 0, `rbq`.`delivery_date` >= '$deliverysame', '$deliverysame' between `rbq`.`delivery_date` - INTERVAL 3 DAY and `rbq`.`delivery_date` + INTERVAL 3 DAY  )  )") ;
			}
		}

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`lkp_quote_access_id` = 2, `pbqss`.`seller_id` =  $loginId,TRUE )  )");

		$QueryBuilder =  $Query_buyers_for_sellers->select ('rbq.*','us.username','cf.city_name as fromcity','ct.city_name as tocity','rt.ratecard_type','pty.property_type','pty.volume','vct.lkp_vechicle_categorie_type','lcat.load_category');
		//echo "<pre>"; print_R($QueryBuilder->get());die;
		return $QueryBuilder;
	}


	/**
	 * Relocation Office Move Seller Search
	 * Start
	 * @param $params
	 * result $query
	 * Jagadeesh - 12/05/2016
	 */
	public static function getRelocationOfficeSellerSearchResults($statusId,$params){
		//echo "<pre>";print_R($params);die;
		$loginId = Auth::User()->id;
		$Query_buyers_for_sellers = DB::table('relocationoffice_buyer_posts as rbq');
		$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id' );
		$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'rbq.buyer_id');		
		$Query_buyers_for_sellers->leftjoin ( 'relocationoffice_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id' );

		if (isset($statusId) && $statusId != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', $statusId);
		}

		//set from location below varaibles are checking empty or not varaible in buyear search---grid
		if (isset($params['from_location_id']) && $params['from_location_id'] != '') {
			$Query_buyers_for_sellers->where('rbq.from_location_id', $params['from_location_id']);
		}

		if(isset($params['selected_users']) && $params['selected_users']!='') {
			//$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$selectedSellers =$params['selected_users'];
			$Query_buyers_for_sellers->WhereIn ( 'us.id', $selectedSellers );
		}

		//dispatch dates
		if(isset($params['valid_from']) && $params['valid_from']!=''){
			$dispatchsame = CommonComponent::convertDateForDatabase($params['valid_from']);
			$Query_buyers_for_sellers->whereRaw ( "`rbq`.`dispatch_date` = '$dispatchsame'" );
		}
		//Delivery dates
		if(isset($params['valid_to']) && $params['valid_to']!=''){
			$deliverysame = CommonComponent::convertDateForDatabase($params['valid_to']);
			$Query_buyers_for_sellers->whereRaw ( "`rbq`.`delivery_date` >= '$deliverysame'");
		}

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`lkp_quote_access_id` = 2, `pbqss`.`seller_id` =  $loginId,TRUE )  )");

		$QueryBuilder =  $Query_buyers_for_sellers->select ('rbq.*','us.username','cf.city_name as fromcity');
		//echo "<pre>"; print_R($QueryBuilder->get());die;
		return $QueryBuilder;
	}

	/**
	 * END
	 * Jagadeesh - 12/05/2016	
	 */

	//get pincode using zone ID
	public static function getPincodesByZoneId($zoneId){
		$zone_location_ids = DB::table('ptl_pincodexsectors as ppxs')
									 ->join('ptl_sectors as s1','ppxs.ptl_sector_id','=','s1.id')
									 ->join('lkp_ptl_pincodes as lpp','lpp.id','=','ppxs.ptl_pincode_id')
									 ->where('s1.ptl_zone_id',$zoneId)
									 ->select('lpp.id')
									 ->distinct('lpp.id');
		$zone_location_ids	=	$zone_location_ids->lists('lpp.id');
		//echo "<pre>";print_R($zone_location_ids);die;
		return $zone_location_ids;
	}

	/**
	 * Truck Haul Seller Search
	 * @param $params
	 * result $query
	 */
	public static function getTruckHaulSellerSearchResults($statusId,$params){
            //print_r($params);//exit;
		$loginId = Auth::User()->id;
		$Query_buyers_for_sellers = DB::table ( 'truckhaul_buyer_quote_items as bqi' );
		$Query_buyers_for_sellers->join( 'lkp_load_types as lt', 'lt.id', '=', 'bqi.lkp_load_type_id' );
		$Query_buyers_for_sellers->join( 'lkp_vehicle_types as vt', 'vt.id', '=', 'bqi.lkp_vehicle_type_id' );
		$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'bqi.from_city_id', '=', 'cf.id' );
		$Query_buyers_for_sellers->join ( 'lkp_cities as ct', 'bqi.to_city_id', '=', 'ct.id' );
		$Query_buyers_for_sellers->join ( 'truckhaul_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
		$Query_buyers_for_sellers->join ( 'users as us', 'us.id', '=', 'bq.buyer_id' );
		$Query_buyers_for_sellers->leftjoin ( 'truckhaul_buyer_quote_selected_sellers as bqss', 'bqss.buyer_quote_id', '=', 'bq.id' );


		if(isset($statusId) && $statusId != ''){
			$Query_buyers_for_sellers->where('bqi.lkp_post_status_id', $statusId);
		}
		//set from location below varaibles are checking empty or not varaible in buyear search---grid
		if(isset($params['from_city_id']) && $params['from_city_id']!=''){
			$Query_buyers_for_sellers->where('bqi.from_city_id', $params['from_city_id']);
		}
		//set to location
		if(isset($params['to_city_id']) && $params['to_city_id']!=''){
			$Query_buyers_for_sellers->where('bqi.to_city_id',$params['to_city_id']);
		}
		//set load type
		if(isset($params['lkp_load_type_id']) && ($params['lkp_load_type_id']!='') && ($params['lkp_load_type_id'] != 11)){
			//$Query_buyers_for_sellers->where('bqi.lkp_load_type_id', $params['lkp_load_type_id']);
			$loadtypeid = $params ['lkp_load_type_id'];
			$Query_buyers_for_sellers->whereRaw ( "`bqi`.`lkp_load_type_id` = $loadtypeid");
		}
		//set vehicle type
		if(isset($params['lkp_vehicle_type_id']) && ($params['lkp_vehicle_type_id']!='') && ($params['lkp_vehicle_type_id'] != 20)){
			//$Query_buyers_for_sellers->where('bqi.lkp_vehicle_type_id', $params['lkp_vehicle_type_id']);
			$vehicletypeid = $params ['lkp_vehicle_type_id'];
			$Query_buyers_for_sellers->whereRaw ( "`bqi`.`lkp_vehicle_type_id` = $vehicletypeid");
		}
		//pricing type
		if(isset($params['lkp_quote_price_type_id']) && $params['lkp_quote_price_type_id']!=''){
			$Query_buyers_for_sellers->where('bqi.lkp_quote_price_type_id',$params['lkp_quote_price_type_id']);
		}


		//dispatch dates
		if(isset($params['dispatch_date']) && $params['dispatch_date']!=''){
			//$dispatchflexibleflag = isset($params['dispatch_flexible_hidden']) ? $params['dispatch_flexible_hidden'] : 0;
			$dispatchflexibleflag = 0;

			if(isset($params['selected_flexible_dispatch']) &&  $params['selected_flexible_dispatch'] != ''){
				$dispatchsame = CommonComponent::convertDateForDatabase($params['selected_flexible_dispatch']);
			}else{
				$dispatchsame = CommonComponent::convertDateForDatabase($params['dispatch_date']);
			}		
			
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " + 3 day"));

			if ($dispatchflexibleflag == 1) {
				//$Query_buyers_for_sellers->where ( DB::raw("( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` between '$dispatch_minus' and '$dispatch_plus', ((`bqi`.`dispatch_date` - INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') or (`bqi`.`dispatch_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") );
				$Query_buyers_for_sellers->whereRaw ( "( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` between '$dispatch_minus' and '$dispatch_plus', ((`bqi`.`dispatch_date` - INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') or (`bqi`.`dispatch_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") ;
			}else{
				//$Query_buyers_for_sellers->where ( DB::raw("( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `bqi`.`dispatch_date` - INTERVAL 3 DAY and `bqi`.`dispatch_date` + INTERVAL 3 DAY  )  )") );
				$Query_buyers_for_sellers->whereRaw ( "( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `bqi`.`dispatch_date` - INTERVAL 3 DAY and `bqi`.`dispatch_date` + INTERVAL 3 DAY  )  )" );
			}
		}		

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`bq`.`lkp_quote_access_id` = 2, `bqss`.`seller_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$Query_buyers_for_sellers->WhereIn ( 'bq.buyer_id', $selectedSellers );
		}
		
		if(isset($params['selected_prices']) && $params['selected_prices']!='') {
			$selectedPrices = array_filter(explode(",", $params['selected_prices']));
			$Query_buyers_for_sellers->WhereIn ( 'bqi.lkp_quote_price_type_id', $selectedPrices );
		}
		if(isset($params['selected_from_date']) && $params['selected_from_date']!='') {
			$selected_from_date = array_filter(explode(",", $params['selected_from_date']));
			$Query_buyers_for_sellers->WhereIn ( 'bqi.dispatch_date', $selected_from_date );
		}		
		if(isset($params['selected_load_type_id']) && $params['selected_load_type_id']!='' && $params['selected_load_type_id']!=11) {
			$selected_loadtype = $params['selected_load_type_id'];
			$Query_buyers_for_sellers->Where ( 'bqi.lkp_load_type_id', $selected_loadtype );
		}
		if(isset($params['selected_vehicle_type_id']) && $params['selected_vehicle_type_id']!='' && $params['selected_vehicle_type_id']!=20) {
			$selected_vehicletype = $params['selected_vehicle_type_id'];
			$Query_buyers_for_sellers->Where ( 'bqi.lkp_vehicle_type_id', $selected_vehicletype );
		}

		$Query_buyers_for_sellers->select ('bq.transaction_id','bq.lkp_quote_access_id','bqi.id','cf.city_name as fromcity',
			'ct.city_name as tocity', 'lt.load_type','vt.vehicle_type','bqi.dispatch_date','bqi.created_by','bqi.lkp_quote_price_type_id',
			'bqi.price','bqi.units','us.username','bqi.dispatch_date', 'bqi.quantity' ,'bqi.number_loads','bqi.lkp_post_status_id','bq.buyer_id','us.username'
		);
		//echo $Query_buyers_for_sellers->tosql ();die;
		return $Query_buyers_for_sellers;
		//$result = $Query_buyers_for_sellers->get ();
	}


	public static function getTruckLeaseSellerSearchResults($statusId,$params){
		//echo "<pre>";print_r($params);echo "</pre>";
		$loginId = Auth::User()->id;

		$Query_buyers_for_sellers = DB::table ( 'trucklease_buyer_quote_items as bqi' );
		$Query_buyers_for_sellers->join( 'lkp_vehicle_types as vt', 'vt.id', '=', 'bqi.lkp_vehicle_type_id' );
		$Query_buyers_for_sellers->join( 'lkp_trucklease_lease_terms as tlt', 'tlt.id', '=', 'bqi.lkp_trucklease_lease_term_id' );
		$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'bqi.from_city_id', '=', 'cf.id' );
		$Query_buyers_for_sellers->join ( 'trucklease_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
		$Query_buyers_for_sellers->join ( 'users as us', 'us.id', '=', 'bq.buyer_id' );
		$Query_buyers_for_sellers->leftjoin ( 'trucklease_buyer_quote_selected_sellers as bqss', 'bqss.buyer_quote_id', '=', 'bq.id' );


		if(isset($statusId) && $statusId != ''){
			$Query_buyers_for_sellers->where('bqi.lkp_post_status_id', $statusId);
		}
		//set from location below varaibles are checking empty or not varaible in buyear search---grid
		if(isset($params['from_city_id']) && $params['from_city_id']!=''){
			$Query_buyers_for_sellers->where('bqi.from_city_id', $params['from_city_id']);
		}

		//set load type
		if(isset($params['lkp_trucklease_lease_term_id']) && ($params['lkp_trucklease_lease_term_id']!='')){
			$truckleasetermid = $params ['lkp_trucklease_lease_term_id'];
			$Query_buyers_for_sellers->whereRaw ( "`bqi`.`lkp_trucklease_lease_term_id` = $truckleasetermid");
		}
		//set vehicle type
		if(isset($params['lkp_vehicle_type_id']) && ($params['lkp_vehicle_type_id']!='') && ($params['lkp_vehicle_type_id'] != 20)){
			//$Query_buyers_for_sellers->where('bqi.lkp_vehicle_type_id', $params['lkp_vehicle_type_id']);
			$vehicletypeid = $params ['lkp_vehicle_type_id'];
			$Query_buyers_for_sellers->whereRaw ( "`bqi`.`lkp_vehicle_type_id` = $vehicletypeid");
		}
		//pricing type
		if(isset($params['lkp_quote_price_type_id']) && $params['lkp_quote_price_type_id']!=''){
			$Query_buyers_for_sellers->where('bqi.lkp_quote_price_type_id',$params['lkp_quote_price_type_id']);
		}


		//dispatch dates
		if(isset($params['dispatch_date']) && $params['dispatch_date']!=''){
			$dispatchsame = CommonComponent::convertDateForDatabase($params['dispatch_date']);
			$Query_buyers_for_sellers->whereRaw ( "`bqi`.`from_date` = '$dispatchsame'" );
		}
		//Delivery dates
		if(isset($params['delivery_date']) && $params['delivery_date']!=''){
			$deliverysame = CommonComponent::convertDateForDatabase($params['delivery_date']);
			$Query_buyers_for_sellers->whereRaw ( "`bqi`.`to_date` >= '$deliverysame'");
		}

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`bq`.`lkp_quote_access_id` = 2, `bqss`.`seller_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$Query_buyers_for_sellers->WhereIn ( 'bq.buyer_id', $selectedSellers );
		}

		if(isset($params['selected_prices']) && $params['selected_prices']!='') {
			$selectedPrices = array_filter(explode(",", $params['selected_prices']));
			$Query_buyers_for_sellers->WhereIn ( 'bqi.lkp_quote_price_type_id', $selectedPrices );
		}
		if(isset($params['selected_from_date']) && $params['selected_from_date']!='') {
			$selected_from_date = array_filter(explode(",", $params['selected_from_date']));
			$Query_buyers_for_sellers->WhereIn ( 'bqi.dispatch_date', $selected_from_date );
		}
		if(isset($params['selected_to_date']) && $params['selected_to_date']!='') {
			$selected_to_date = array_filter(explode(",", $params['selected_to_date']));
			$Query_buyers_for_sellers->WhereIn ( 'bqi.delivery_date', $selected_to_date );
		}
		if(isset($params['selected_trucklease_lease_term_id']) && $params['selected_trucklease_lease_term_id']!='' && $params['selected_trucklease_lease_term_id']!=11) {
			$selected_loadtype = $params['selected_trucklease_lease_term_id'];
			$Query_buyers_for_sellers->Where ( 'bqi.lkp_trucklease_lease_term_id', $selected_loadtype );
		}
		if(isset($params['selected_vehicle_type_id']) && $params['selected_vehicle_type_id']!='' && $params['selected_vehicle_type_id']!=20) {
			$selected_vehicletype = $params['selected_vehicle_type_id'];
			$Query_buyers_for_sellers->Where ( 'bqi.lkp_vehicle_type_id', $selected_vehicletype );
		}

		$Query_buyers_for_sellers->select ('bq.transaction_id','bq.lkp_quote_access_id','bqi.*','tlt.lease_term','vt.vehicle_type','bq.buyer_id','us.username');
		/*echo "<pre>";print_R($Query_buyers_for_sellers->getBindings());
		echo $Query_buyers_for_sellers->tosql();
		$result = $Query_buyers_for_sellers->get ();
		echo "<pre>";print_R($result);
		die;*/
		return $Query_buyers_for_sellers;
		//$result = $Query_buyers_for_sellers->get ();
	}
        
    /**
	 * Relocation Pet Move Seller Search
         * @param $params
	 * result $query
	 */
	public static function getRelocationPetSellerSearchResults($statusId,$params){
			//echo "<pre>";print_R($params);die;
            $loginId = Auth::User()->id;
            $Query_buyers_for_sellers = DB::table('relocationpet_buyer_posts as rbq');
            $Query_buyers_for_sellers->join( 'lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id' );
            $Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'rbq.buyer_id');		
            $Query_buyers_for_sellers->leftjoin ( 'relocationpet_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id' );
            $Query_buyers_for_sellers->leftJoin('lkp_cage_types as lkp_ctype', 'lkp_ctype.id', '=', 'rbq.lkp_cage_type_id');
            $Query_buyers_for_sellers->leftJoin('lkp_pet_types as lkp_ptype', 'lkp_ptype.id', '=', 'rbq.lkp_pet_type_id');
            $Query_buyers_for_sellers->leftJoin('lkp_breed_types as lkp_btype', 'lkp_btype.id', '=', 'rbq.lkp_breed_type_id');
                    
            if (isset($statusId) && $statusId != '') {
                $Query_buyers_for_sellers->where('rbq.lkp_post_status_id', $statusId);
            }

            if (isset($params['pet_type']) && $params['pet_type'] != '') {
                $Query_buyers_for_sellers->where('rbq.lkp_pet_type_id', $params['pet_type']);
            }

            //set from location below varaibles are checking empty or not varaible in buyear search---grid
            if (isset($params['from_location_id']) && $params['from_location_id'] != '') {
                $Query_buyers_for_sellers->where('rbq.from_location_id', $params['from_location_id']);
            }

            if(isset($params['selected_users']) && $params['selected_users']!='') {
                //$selectedSellers = array_filter(explode(",", $params['selected_users']));
                $selectedSellers =$params['selected_users'];
                $Query_buyers_for_sellers->WhereIn ( 'us.id', $selectedSellers );
            }
			//dispatch dates
			if(isset($params['valid_from']) && $params['valid_from']!=''){
				$params['dispatch_date'] = $params['valid_from'];
			}
			if(isset($params['valid_to']) && $params['valid_to']!=''){
				$params['delivery_date'] = $params['valid_to'];
			}
            //dispatch dates
            if(isset($params['dispatch_date']) && $params['dispatch_date']!=''){
				$dispatchflexibleflag = isset($params['dispatch_flexible_hidden']) ? $params['dispatch_flexible_hidden'] : 0;
                $dispatchsame = CommonComponent::convertDateForDatabase($params['dispatch_date']);
                $dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " - 3 day"));
                $dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " + 3 day"));

				if($dispatchflexibleflag==1)
					$Query_buyers_for_sellers->whereRaw ( "(  `rbq`.`dispatch_date` between '".$dispatch_minus."' and '".$dispatch_plus."'  )") ;
				else
					$Query_buyers_for_sellers->whereRaw ( "(  `rbq`.`dispatch_date` = '".$dispatchsame."'  )") ;
            }
            //Delivery dates
            if(isset($params['delivery_date']) && $params['delivery_date']!=''){
                $deliveryflexibleflag = isset($params['delivery_flexible_hidden']) ? $params['delivery_flexible_hidden'] : 0;
                $deliverysame = CommonComponent::convertDateForDatabase($params['delivery_date']);
                $delivery_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['delivery_date']) . " - 3 day"));
                $delivery_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['delivery_date']) . " + 3 day"));

				if($deliveryflexibleflag==1)
					$Query_buyers_for_sellers->whereRaw ( "(  `rbq`.`delivery_date` between '".$delivery_minus."' and '".$delivery_plus."'  )") ;
				else
					$Query_buyers_for_sellers->whereRaw ( "(  `rbq`.`delivery_date` = '".$deliverysame."'  )") ;
            }

            //Checking private sellers
            $Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`lkp_quote_access_id` = 2, `pbqss`.`seller_id` =  $loginId,TRUE )  )");

            $QueryBuilder =  $Query_buyers_for_sellers->select ('rbq.*','us.username','cf.city_name as fromcity', 'lkp_ctype.cage_type', 'lkp_ctype.cage_weight', 'lkp_ptype.pet_type', 'lkp_btype.breed_type');
            //echo "<pre>"; print_R($QueryBuilder);die;
            return $QueryBuilder;
	}

	/**
	 * Relocation Pet Move Seller Search
	 * @param $params
	 * result $query
	 */
	public static function getRelocationInternationalSellerSearchResults($statusId,$params){
		//echo "<pre>";print_R($params);die;
		$loginId = Auth::User()->id;
		$Query_buyers_for_sellers = DB::table('relocationint_buyer_posts as rbq');
		$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id' );
		$Query_buyers_for_sellers->join ( 'lkp_cities as ct', 'rbq.to_location_id', '=', 'ct.id' );
		$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'rbq.buyer_id');
		$Query_buyers_for_sellers->leftjoin ( 'relocationint_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id' );
		if($params['service_type'] == 2){
			$Query_buyers_for_sellers->leftjoin('lkp_property_types as pty', 'pty.id', '=', 'rbq.lkp_property_type_id');
		}
		if (isset($statusId) && $statusId != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', $statusId);
		}

		//set from location below varaibles are checking empty or not varaible in buyear search---grid
		if (isset($params['from_location_id']) && $params['from_location_id'] != '') {
			$Query_buyers_for_sellers->where('rbq.from_location_id', $params['from_location_id']);
		}
		//set to location
		if (isset($params['to_location_id']) && $params['to_location_id'] != '') {
			$Query_buyers_for_sellers->where('rbq.to_location_id', $params['to_location_id']);
		}

		if(isset($params['selected_users']) && $params['selected_users']!='') {
			//$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$selectedSellers =$params['selected_users'];
			$Query_buyers_for_sellers->WhereIn ( 'us.id', $selectedSellers );
		}
		$Query_buyers_for_sellers->where('rbq.lkp_international_type_id', $params['service_type']);
		//dispatch dates
		if(isset($params['valid_from']) && $params['valid_from']!=''){
			$params['dispatch_date'] = $params['valid_from'];
		}
		if(isset($params['valid_to']) && $params['valid_to']!=''){
			$params['delivery_date'] = $params['valid_to'];
		}
		if(isset($params['dispatch_date']) && $params['dispatch_date']!=''){
			$dispatchflexibleflag = isset($params['dispatch_flexible_hidden']) ? $params['dispatch_flexible_hidden'] : 0;
			$dispatchsame = CommonComponent::convertDateForDatabase($params['dispatch_date']);
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['dispatch_date']) . " + 3 day"));

			if ($dispatchflexibleflag == 1) {
				$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`is_dispatch_flexible` = 0, `rbq`.`dispatch_date` between '$dispatch_minus' and '$dispatch_plus', ((`rbq`.`dispatch_date` - INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') or (`rbq`.`dispatch_date` + INTERVAL 3 DAY between '$dispatch_minus' and '$dispatch_plus') ) )  )") ;
			}else{
				$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`is_dispatch_flexible` = 0, `rbq`.`dispatch_date` = '$dispatchsame', '$dispatchsame' between `rbq`.`dispatch_date` - INTERVAL 3 DAY and `rbq`.`dispatch_date` + INTERVAL 3 DAY  )  )" );
			}
		}
		//Delivery dates
		if(isset($params['delivery_date']) && $params['delivery_date']!=''){
			$deliveryflexibleflag = isset($params['delivery_flexible_hidden']) ? $params['delivery_flexible_hidden'] : 0;
			$deliverysame = CommonComponent::convertDateForDatabase($params['delivery_date']);
			$delivery_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['delivery_date']) . " - 3 day"));
			$delivery_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['delivery_date']) . " + 3 day"));
			if ($deliveryflexibleflag == 1) {
				$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`is_delivery_flexible` = 0, `rbq`.`delivery_date` between '$delivery_minus' and '$delivery_plus', ((`rbq`.`delivery_date` - INTERVAL 3 DAY between '$delivery_minus' and '$delivery_plus') or (`rbq`.`delivery_date` + INTERVAL 3 DAY between '$delivery_minus' and '$delivery_plus') ) )  )") ;
			}else{
				$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`is_delivery_flexible` = 0, `rbq`.`delivery_date` >= '$deliverysame', '$deliverysame' between `rbq`.`delivery_date` - INTERVAL 3 DAY and `rbq`.`delivery_date` + INTERVAL 3 DAY  )  )") ;
			}
		}

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`lkp_quote_access_id` = 2, `pbqss`.`seller_id` =  $loginId,TRUE )  )");
		if($params['service_type'] == 1){
			$QueryBuilder =  $Query_buyers_for_sellers->select ('rbq.*','us.username','cf.city_name as fromcity','ct.city_name as tocity');
		}else{
			$QueryBuilder =  $Query_buyers_for_sellers->select ('rbq.*','us.username','cf.city_name as fromcity','ct.city_name as tocity','pty.property_type');
		}

		/*echo $QueryBuilder->tosql();
		echo "<pre>"; print_R($QueryBuilder->getBindings());
		echo "<pre>"; print_R($QueryBuilder->get());die;*/
		return $QueryBuilder;


	}

	public static function getAllPincodeIds($id){
		$getpincode = DB::table('lkp_ptl_pincodes')->where('id','=',$id)->pluck('pincode');
		if($getpincode){
			$allRelatedPincodes = DB::table('lkp_ptl_pincodes')->where('pincode','=',$getpincode)->lists('id');
		}
		return $allRelatedPincodes;
	}
        
        /**
	 * Relocation global Seller Search
	 * @param $params
	 * result $query
	 */
	public static function getRelocationGmSellerSearchResults($statusId,$params){
		//echo "<pre>";print_R($params);die;
                $gmServiceTypes = CommonComponent::getAllGMServiceTypesforSeller();
		$loginId = Auth::User()->id;
		$Query_buyers_for_sellers = DB::table('relocationgm_buyer_posts as rbq');
                $Query_buyers_for_sellers->leftjoin( 'relocationgm_buyer_quote_items as rbqi', 'rbqi.buyer_post_id', '=', 'rbq.id' );
                $Query_buyers_for_sellers->leftjoin( 'lkp_relocationgm_services as lrs', 'lrs.id', '=', 'rbqi.lkp_gm_service_id' );
		$Query_buyers_for_sellers->leftjoin( 'lkp_cities as cf', 'rbq.location_id', '=', 'cf.id' );
		//$Query_buyers_for_sellers->join ( 'lkp_cities as ct', 'rbq.to_location_id', '=', 'ct.id' );
		//$Query_buyers_for_sellers->join('lkp_post_ratecard_types as rt', 'rt.id', '=', 'rbq.lkp_post_ratecard_type_id');
		$Query_buyers_for_sellers->leftjoin('users as us', 'us.id', '=', 'rbq.buyer_id');		
		//$Query_buyers_for_sellers->leftjoin('lkp_property_types as pty', 'pty.id', '=', 'rbq.lkp_property_type_id');
		//$Query_buyers_for_sellers->leftjoin('lkp_vechicle_categorie_types as vct', 'vct.id', '=', 'rbq.lkp_vehicle_category_type_id');
		//$Query_buyers_for_sellers->leftjoin('lkp_load_categories as lcat', 'lcat.id', '=', 'rbq.lkp_load_category_id');
		$Query_buyers_for_sellers->leftjoin ( 'relocationgm_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id' );
                if (isset($params['selected_services']) && $params['selected_services'] != '') {
                    $i=1;
                    foreach($params['selected_services'] as $selected_services){
                    $Query_buyers_for_sellers->leftjoin( 'relocationgm_buyer_quote_items as rbqi'."$i", 'rbqi'."$i".'.buyer_post_id', '=', 'rbq.id' );
                    $Query_buyers_for_sellers->Where('rbqi'."$i".'.lkp_gm_service_id',$selected_services);
                    $i++;
                    }
                }
                
		if (isset($statusId) && $statusId != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', $statusId);
		}
                
		
		//set to location
		if (isset($params['to_location_id']) && $params['to_location_id'] != '') {
			$Query_buyers_for_sellers->where('rbq.location_id', $params['to_location_id']);
		}

		
                
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers =$params['selected_users'];
			$Query_buyers_for_sellers->WhereIn ( 'us.id', $selectedSellers );
		}
                if (isset($params['selected_services']) && $params['selected_services'] != '') {
//                    if (isset($params['relgm_service_type']) && $params['relgm_service_type'] != '') {
//                        $params['selected_services'][]=$params['relgm_service_type'];
//                    }
			//$Query_buyers_for_sellers->WhereIn('lrs.id', $params['selected_services']);
		}
                //set rate service wise
                if (isset($params['relgm_service_type']) && $params['relgm_service_type'] != '') {
                        $Query_buyers_for_sellers->where('lrs.id', $params['relgm_service_type']);
                }
                

		//From dates
		if(isset($params['valid_from']) && $params['valid_from']!=''){
                    
                    if(isset($params['valid_to']) && $params['valid_to']!=''){
                            $deliveryflexibleflag = isset($params['delivery_flexible_hidden']) ? $params['delivery_flexible_hidden'] : 0;
                            $dispatchflexibleflag = isset($params['dispatch_flexible_hidden']) ? $params['dispatch_flexible_hidden'] : 0;
                            $deliverysame = CommonComponent::convertDateForDatabase($params['valid_to']);
                            $dispatchsame = CommonComponent::convertDateForDatabase($params['valid_from']);
                            $delivery_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_to']) . " - 3 day"));
                            $delivery_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_to']) . " + 3 day"));
                            $dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_from']) . " - 3 day"));
                            $dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_from']) . " + 3 day"));
                            if ($deliveryflexibleflag == 1 ){
                                if($dispatchflexibleflag==1) 
                                     $Query_buyers_for_sellers->whereRaw ( "(  `rbq`.`dispatch_date` between '".$dispatch_minus."' and '".$delivery_plus."'  )") ;
                                else
                                    $Query_buyers_for_sellers->whereRaw ( "(  `rbq`.`dispatch_date` between '".$dispatchsame."' and '".$delivery_plus."'  )") ;
                            }else{
                                if($dispatchflexibleflag==1) 
                                     $Query_buyers_for_sellers->whereRaw ( "(  `rbq`.`dispatch_date` between '".$dispatch_minus."' and '".$deliverysame."'  )") ;
                                else
                                    $Query_buyers_for_sellers->whereRaw ( "(  `rbq`.`dispatch_date` between '".$dispatchsame."' and '".$deliverysame."'  )") ;
                            }
                    }else{
			$dispatchflexibleflag = isset($params['dispatch_flexible_hidden']) ? $params['dispatch_flexible_hidden'] : 0;
			$dispatchsame = CommonComponent::convertDateForDatabase($params['valid_from']);
			$dispatch_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_from']) . " - 3 day"));
			$dispatch_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_from']) . " + 3 day"));

			if ($dispatchflexibleflag == 1) {
				$Query_buyers_for_sellers->whereRaw ( "(  `rbq`.`dispatch_date` >= '".$dispatch_minus."'  )") ;
			}else{
				$Query_buyers_for_sellers->whereRaw ( "(  `rbq`.`dispatch_date` >= '".$dispatchsame."'  )") ;
			}
                    }
		}else{
                    
                    if(isset($params['valid_to']) && $params['valid_to']!=''){
                            $deliveryflexibleflag = isset($params['delivery_flexible_hidden']) ? $params['delivery_flexible_hidden'] : 0;
                            $deliverysame = CommonComponent::convertDateForDatabase($params['valid_to']);
                            $dispatchsame = CommonComponent::convertDateForDatabase($params['valid_from']);
                            $delivery_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_to']) . " - 3 day"));
                            $delivery_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_to']) . " + 3 day"));
                            if ($deliveryflexibleflag == 1) {
                                    $Query_buyers_for_sellers->whereRaw ( "(  `rbq`.`dispatch_date` <= '".$delivery_plus."'  )") ;
                            }else{
                                    $Query_buyers_for_sellers->whereRaw ( "(  `rbq`.`dispatch_date` <= '".$deliverysame."'  )") ;
                            }
                    }
                    
                }
		

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`lkp_quote_access_id` = 2, `pbqss`.`seller_id` =  $loginId,TRUE )  )");

		$QueryBuilder =  $Query_buyers_for_sellers->select ('rbq.*','us.username','cf.city_name as fromcity')->groupBy('rbq.id');
		//echo "<pre>"; print_R($QueryBuilder->get());die;
		return $QueryBuilder;
	}


}
