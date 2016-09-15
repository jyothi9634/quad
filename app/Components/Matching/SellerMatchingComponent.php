<?php
namespace App\Components\Matching;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use App\Components\CommonComponent;
use App\Components\Search\SellerSearchComponent;
use App\Models\User;
use App\Models\PtlSearchTerm;
use App\Models\MatchingItem;

use App\Components\SellerComponent;
class SellerMatchingComponent {

	public static function doMatching($serviceId, $postId, $statusId,$params) {

		switch($serviceId){
			case ROAD_FTL       :
			case ROAD_TRUCK_LEASE       :
				//matching
				$queryBuilder = SellerMatchingComponent::updateFtlMatching($statusId,$postId,$params,1);
				//seller leads
				if(isset($params['from_city_id']) && !empty($params['from_city_id'])){
					$district  = CommonComponent::getDistrict($params['from_city_id'],$serviceId);
					if(!empty($district)){
						unset($params['from_city_id']);unset($params['to_city_id']);
						unset($params['lkp_load_type_id']);unset($params['lkp_vehicle_type_id']);
						unset($params['valid_from']);unset($params['transit_days']);
						$params['district'] = $district;
						$queryBuilder = SellerMatchingComponent::updateFtlMatching($statusId,$postId,$params,2);
					}
				}

				break;
			case ROAD_PTL:
			case RAIL:
			case AIR_DOMESTIC:
			case COURIER:
				//echo "<pre>";print_r($params);die;
				//matching
				$queryBuilder = SellerMatchingComponent::updatePtlMatching($serviceId,$statusId,$postId,$params,1);
				//seller leads
				if(isset($params['from_location_id']) && !empty($params['from_location_id'])){
					$zone_or_location = (isset($params['zone_or_location'])) ? $params['zone_or_location'] : 2;
					$district  = CommonComponent::getDistrict($params['from_location_id'],$serviceId,$zone_or_location);
					if(!empty($district)){
						unset($params['from_location_id']);unset($params['to_location_id']);
						unset($params['valid_from']);unset($params['transit_days']);
						$params['district'] = $district;
						$queryBuilder = SellerMatchingComponent::updatePtlMatching($serviceId,$statusId,$postId,$params,2);
					}
				}
				break;
			case AIR_INTERNATIONAL:
			case OCEAN:
				$queryBuilder = SellerMatchingComponent::updateAirintOceanMatching($serviceId,$statusId,$postId,$params);

				//echo $queryBuilder->tosql();die;
				break;

			case ROAD_INTRACITY :
				//coming soon
				break;
			case ROAD_TRUCK_HAUL       :
				//matching
				$queryBuilder = SellerMatchingComponent::updateTruckHaulMatching($statusId,$postId,$params,1);
				//seller leads
				if(isset($params['from_city_id']) && !empty($params['from_city_id'])){
					$district  = CommonComponent::getDistrict($params['from_city_id'],$serviceId);
					if(!empty($district)){
						unset($params['from_city_id']);unset($params['to_city_id']);
						unset($params['lkp_load_type_id']);unset($params['lkp_vehicle_type_id']);
						unset($params['valid_from']);unset($params['transit_days']);
						$params['district'] = $district;
						$queryBuilder = SellerMatchingComponent::updateTruckHaulMatching($statusId,$postId,$params,2);
					}
				}

				break;			
			case RELOCATION_DOMESTIC:
				//matching
				$queryBuilder = SellerMatchingComponent::updateRelocationDomesticMatching($serviceId,$statusId,$postId,$params,1);
				//seller leads
				if(isset($params['from_location_id']) && !empty($params['from_location_id'])){
					$district  = CommonComponent::getDistrict($params['to_location_id'],$serviceId);
					if(!empty($district)){
						unset($params['from_location_id']);unset($params['from_location_id']);
						unset($params['valid_from']);unset($params['transit_days']);
						unset($params['propertytypes']);unset($params['vehicle_category']);
						$params['district'] = $district;
						$queryBuilder = SellerMatchingComponent::updateRelocationDomesticMatching($serviceId,$statusId,$postId,$params,2);
					}
				}

				break;			
			case RELOCATION_INTERNATIONAL:
				//matching
				$queryBuilder = SellerMatchingComponent::updateRelocationInternationalMatching($serviceId,$statusId,$postId,$params,1);
				//seller leads
				if(isset($params['from_location_id']) && !empty($params['from_location_id'])){
					//echo "<pre>";print_r($params);//exit;
                                    $district  = CommonComponent::getDistrict($params['from_location_id'],$serviceId);
					if(!empty($district)){
						unset($params['from_location_id']);unset($params['to_location_id']);
						unset($params['dispatch_date']);unset($params['delivery_date']);
                                                unset($params['transit_days']);
						$params['district'] = $district;
						$queryBuilder = SellerMatchingComponent::updateRelocationInternationalMatching($serviceId,$statusId,$postId,$params,2);
					}
				}

				break;
				
			case RELOCATION_OFFICE_MOVE:
					//matching
					$queryBuilder = SellerMatchingComponent::updateRelocationOfficeMatching($serviceId,$statusId,$postId,$params,1);
					//seller leads
					if(isset($params['from_location_id']) && !empty($params['from_location_id'])){
						$district  = CommonComponent::getDistrict($params['from_location_id'],$serviceId);
						if(!empty($district)){
							unset($params['from_location_id']);
							unset($params['valid_from']);
							$params['district'] = $district;
							$queryBuilder = SellerMatchingComponent::updateRelocationOfficeMatching($serviceId,$statusId,$postId,$params,2);
						}
					}
				
					break;
                        case RELOCATION_PET_MOVE:
					//matching
					$queryBuilder = SellerMatchingComponent::updateRelocationPetMatching($serviceId,$statusId,$postId,$params,1);
					//seller leads
					if(isset($params['from_location_id']) && !empty($params['from_location_id'])){
						$district  = CommonComponent::getDistrict($params['from_location_id'],$serviceId);
						if(!empty($district)){
							unset($params['from_location_id']);unset($params['to_location_id']);unset($params['cagetypes']);
							unset($params['valid_from']);unset($params['valid_to']);unset($params['pettypes']);
							$params['district'] = $district;
							$queryBuilder = SellerMatchingComponent::updateRelocationPetMatching($serviceId,$statusId,$postId,$params,2);
						}
					}
				
					break;  
                        case RELOCATION_GLOBAL_MOBILITY:
                                $gmServiceTypes = CommonComponent::getAllGMServiceTypesforSeller();
				//matching
				$queryBuilder = SellerMatchingComponent::updateRelocationGmMatching($serviceId,$statusId,$postId,$params,1);
				//seller leads
				if(isset($params['to_location_id']) && !empty($params['to_location_id'])){
					$district  = CommonComponent::getDistrict($params['to_location_id'],$serviceId);
					if(!empty($district)){
						unset($params['to_location_id']);
						unset($params['valid_from']);unset($params['valid_to']);
                                                foreach($gmServiceTypes as $gmServiceType){
                                                    $str_name   =   strtolower(str_replace(' ','_',$gmServiceType->service_type));
                                                    if(isset($params[$str_name]) && $params[$str_name]!=''){
                                                       
                                                        unset($params[$str_name]);
                                                    }
                                                }
						$params['district'] = $district;
						$queryBuilder = SellerMatchingComponent::updateRelocationGmMatching($serviceId,$statusId,$postId,$params,2);
					}
				}

				break;	                
			default             :
				break;
		}

		return true;
	}

	/**
	 * FTL Seller Search
	 * @param $params
	 * result $query
	 */
	public static function updateFtlMatching($statusId, $postId, $params,$matchingtype = 1){
		//echo "<pre>";print_R($params);
		/*$Query_buyers_for_sellers = SellerSearchComponent::search($roleId=null,ROAD_FTL,$statusId, $params);
		$results = $Query_buyers_for_sellers->get();*/
		$loginId = Auth::User()->id;
		$serviceId = Session::get ( 'service_id' );
		if($serviceId == ROAD_FTL){
		$Query_buyers_for_sellers = DB::table ( 'buyer_quote_items as bqi' );
		$Query_buyers_for_sellers->join( 'lkp_load_types as lt', 'lt.id', '=', 'bqi.lkp_load_type_id' );
		$Query_buyers_for_sellers->join( 'lkp_vehicle_types as vt', 'vt.id', '=', 'bqi.lkp_vehicle_type_id' );
		$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'bqi.from_city_id', '=', 'cf.id' );
		$Query_buyers_for_sellers->join ( 'lkp_cities as ct', 'bqi.to_city_id', '=', 'ct.id' );
		$Query_buyers_for_sellers->join ( 'buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
		$Query_buyers_for_sellers->join ( 'users as us', 'us.id', '=', 'bq.buyer_id' );
		$Query_buyers_for_sellers->leftjoin ( 'buyer_quote_selected_sellers as bqss', 'bqss.buyer_quote_id', '=', 'bq.id' );
		}
		else{
		$Query_buyers_for_sellers = DB::table ( 'trucklease_buyer_quote_items as bqi' );
		$Query_buyers_for_sellers->join( 'lkp_vehicle_types as vt', 'vt.id', '=', 'bqi.lkp_vehicle_type_id' );
		$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'bqi.from_city_id', '=', 'cf.id' );
		$Query_buyers_for_sellers->join ( 'trucklease_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
		$Query_buyers_for_sellers->join ( 'users as us', 'us.id', '=', 'bq.buyer_id' );
		$Query_buyers_for_sellers->leftjoin ( 'trucklease_buyer_quote_selected_sellers as bqss', 'bqss.buyer_quote_id', '=', 'bq.id' );
			
		}

		if(isset($statusId) && $statusId != ''){
			$Query_buyers_for_sellers->where('bqi.lkp_post_status_id', $statusId);
		}
		//set from location below varaibles are checking empty or not varaible in buyear search---grid
		if(isset($params['from_city_id']) && $params['from_city_id']!=''){
			$Query_buyers_for_sellers->where('bqi.from_city_id', $params['from_city_id']);
		}
		//set to location
		if($serviceId == ROAD_FTL){
			if(isset($params['to_city_id']) && $params['to_city_id']!=''){
				$Query_buyers_for_sellers->where('bqi.to_city_id',$params['to_city_id']);
			}
		}
		//set district
		if(isset($params['district']) && $params['district']!=''){
			$Query_buyers_for_sellers->where('cf.lkp_district_id',$params['district']);
		}

		//set load type
		if($serviceId == ROAD_FTL){
			if(isset($params['lkp_load_type_id']) && ($params['lkp_load_type_id']!='') && ($params['lkp_load_type_id'] != 11)){
				//$Query_buyers_for_sellers->where('bqi.lkp_load_type_id', $params['lkp_load_type_id']);
				$loadtypeid = $params ['lkp_load_type_id'];
				$Query_buyers_for_sellers->whereRaw ( "( IF(`bqi`.`lkp_load_type_id` != 11, `bqi`.`lkp_load_type_id` = $loadtypeid,TRUE )  )");
			}
		}
		//set vehicle type
		if(isset($params['lkp_vehicle_type_id']) && ($params['lkp_vehicle_type_id']!='') && ($params['lkp_vehicle_type_id'] != 20)){
			//$Query_buyers_for_sellers->where('bqi.lkp_vehicle_type_id', $params['lkp_vehicle_type_id']);
			$vehicletypeid = $params ['lkp_vehicle_type_id'];
			$Query_buyers_for_sellers->whereRaw ( "( IF(`bqi`.`lkp_vehicle_type_id` != 20, `bqi`.`lkp_vehicle_type_id` = $vehicletypeid,TRUE )  )");
		}
		//dispatch dates
		if(isset($params['valid_from']) && $params['valid_from']!='' && isset($params['valid_to']) && $params['valid_to']!=''){
			$valid_from = CommonComponent::convertDateForDatabase($params['valid_from']);
			$valid_to = CommonComponent::convertDateForDatabase($params['valid_to']);
			$valid_from_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_from']) . " - 3 day"));
			$valid_to_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_to']) . " + 3 day"));
			if($serviceId == ROAD_FTL){
				$Query_buyers_for_sellers->whereRaw ( "( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` between '$valid_from' and '$valid_to', `bqi`.`dispatch_date` between '$valid_from_minus' and '$valid_to_plus'  )  )" );
			}else{
				$Query_buyers_for_sellers->whereRaw ( "( `bqi`.`from_date` between '$valid_from' and '$valid_to'  )" );
				$Query_buyers_for_sellers->whereRaw ( "( `bqi`.`from_date` between '$valid_from' and '$valid_to'  )" );
			}
		}
		if($serviceId == ROAD_TRUCK_LEASE){
			if(isset($params['lkp_trucklease_lease_term_id']) && $params['lkp_trucklease_lease_term_id']!='') {
				$Query_buyers_for_sellers->where('bqi.lkp_trucklease_lease_term_id', $params['lkp_trucklease_lease_term_id']);
			}
			if(isset($params['minimum_lease_period']) && $params['minimum_lease_period']!='') {
				$minleasetermperiod = $params['minimum_lease_period'];
				$Query_buyers_for_sellers->whereRaw ( "IF((`bqi`.`to_date` = '0000-00-00'),TRUE, DATEDIFF(`bqi`.`to_date`,`bqi`.`from_date`)+1 >=  $minleasetermperiod) " );
			}

		}
		//transit_days
		if($serviceId == ROAD_FTL){
			if(isset($params['transit_days']) && $params['transit_days']!=''){
				$transit_days = $params['transit_days'];
				$Query_buyers_for_sellers->whereRaw ( "IF((`bqi`.`delivery_date` = '0000-00-00'),TRUE, ( IF((`bqi`.`is_dispatch_flexible` = 0 && `bqi`.`is_delivery_flexible` = 0),
															DATEDIFF(`bqi`.`delivery_date`,`bqi`.`dispatch_date`)+1 >=  $transit_days,
															IF((`bqi`.`is_dispatch_flexible` = 1 && `bqi`.`is_delivery_flexible` = 1), DATEDIFF(`bqi`.`delivery_date` + INTERVAL 3 DAY,`bqi`.`dispatch_date` - INTERVAL 3 DAY)+1 >=  $transit_days, DATEDIFF(`bqi`.`delivery_date` + INTERVAL 3 DAY,`bqi`.`dispatch_date`)+1 >=  $transit_days)
														))) " );
			}
		}
		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`bq`.`lkp_quote_access_id` = 2, `bqss`.`seller_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$Query_buyers_for_sellers->WhereIn ( 'bq.buyer_id', $selectedSellers );
		}
		if($serviceId == ROAD_FTL){
			$Query_buyers_for_sellers->select ('bq.lkp_quote_access_id','bqi.id','cf.city_name as fromcity',
				'ct.city_name as tocity', 'lt.load_type','vt.vehicle_type','bqi.dispatch_date','bqi.created_by',
				'bqi.price','us.username','bqi.delivery_date','bqi.dispatch_date', 'bqi.lkp_post_status_id','bq.buyer_id','us.username'
			);
		}else{
			$Query_buyers_for_sellers->select ('bq.lkp_quote_access_id','bqi.id','cf.city_name as fromcity',
					'vt.vehicle_type','bqi.created_by',
					'bqi.price','us.username','bqi.to_date','bqi.from_date', 'bqi.lkp_post_status_id','bq.buyer_id','us.username'
			);
		}
		/*echo $Query_buyers_for_sellers->tosql();
		$bindings = $Query_buyers_for_sellers->getBindings();
		echo "<pre>";print_R($bindings);*/
		$results = $Query_buyers_for_sellers->get();
		//echo "<pre>";print_R($results);die;

		$matchedbuyerquotes = array();
		foreach($results as $result){
			$matchedbuyerquotes[] = $result->id;
		}


		SellerMatchingComponent::insetOrUpdateMatches($serviceId,$postId,$statusId,$matchedbuyerquotes,$matchingtype);

		return true;
		//$result = $Query_buyers_for_sellers->get ();
	}
	/**
	 * PTL Matching
	 * @param $params
	 * result $query
	 */

	public static function updatePtlMatching($serviceId,$statusId, $postId, $params,$matchingtype = 1){
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
			$Query_buyers_for_sellers->where('pbq.lkp_post_status_id', $statusId);
		}
		//Zone or location

		$zone_or_location = isset($params['zone_or_location']) ? $params['zone_or_location'] : 1;

		if ($zone_or_location == 2){
			//set from location below varaibles are checking empty or not varaible in buyear search---grid
			if (isset($params['from_location_id']) && $params['from_location_id'] != '') {
				$Query_buyers_for_sellers->where('pbq.from_location_id', $params['from_location_id']);
			}
			//set to location
			if (isset($params['to_location_id']) && $params['to_location_id'] != '') {
				$Query_buyers_for_sellers->where('pbq.to_location_id', $params['to_location_id']);
			}

			//district
			if (isset($params['district']) && $params['district'] != '') {
				$Query_buyers_for_sellers->where('lppf.lkp_district_id', $params['district']);
			}
		}else{
			if (isset($params['from_location_id']) && $params['from_location_id'] != '') {
				$frompincodes = SellerSearchComponent::getPincodesByZoneId($params['from_location_id']);
				$Query_buyers_for_sellers->whereIn('pbq.from_location_id', $frompincodes);
			}
			
			if (isset($params['to_location_id']) && $params['to_location_id'] != '') {
				if($serviceId == COURIER){
					if(isset($params['post_or_delivery_type']) && $params['post_or_delivery_type']==1){
						$topincodes = SellerSearchComponent::getPincodesByZoneId($params['to_location_id']);
						$Query_buyers_for_sellers->whereIn('pbq.to_location_id', $topincodes);
					}else{
						$Query_buyers_for_sellers->where('pbq.to_location_id', $params['to_location_id']);
					}
				}else{
					if (isset($params['to_location_id']) && $params['to_location_id'] != '') {
						$topincodes = SellerSearchComponent::getPincodesByZoneId($params['to_location_id']);
						$Query_buyers_for_sellers->whereIn('pbq.to_location_id', $topincodes);
					}
				}
			}
			
			//district
			if (isset($params['district']) && $params['district'] != '') {
				$Query_buyers_for_sellers->whereIn('lppf.lkp_district_id', $params['district']);
			}
		}

		//echo "<pre>";print_R($params);die;
		//dispatch dates
		if(isset($params['valid_from']) && $params['valid_from']!='' && isset($params['valid_to']) && $params['valid_to']!=''){
			$valid_from = CommonComponent::convertDateForDatabase($params['valid_from']);
			$valid_to = CommonComponent::convertDateForDatabase($params['valid_to']);
			$valid_from_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_from']) . " - 3 day"));
			$valid_to_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_to']) . " + 3 day"));
			if($serviceId == COURIER){
				$Query_buyers_for_sellers->whereRaw ( "( IF(1=1,`pbq`.`dispatch_date` between '$valid_from' and '$valid_to', `pbq`.`dispatch_date` between '$valid_from_minus' and '$valid_to_plus'  )  )" );
			}
			else{ 
				$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`is_dispatch_flexible` = 0, `pbq`.`dispatch_date` between '$valid_from' and '$valid_to', `pbq`.`dispatch_date` between '$valid_from_minus' and '$valid_to_plus'  )  )" );
			}
			
		}
		//max weight
		if($serviceId == COURIER){
			if (isset($params['courier_max_weight']) && $params['courier_max_weight'] != '') {
				$weight = $params['courier_max_weight'];
				$weightgms = $params['courier_max_weight'] * 1000;
				$weightmts = $params['courier_max_weight'] * 0.001;
				$Query_buyers_for_sellers->whereRaw ( "( IF(`pbqi`.`lkp_ict_weight_uom_id` = 1, `pbqi`.`units` <= '$weight',
														(IF(`pbqi`.`lkp_ict_weight_uom_id` = 2, `pbqi`.`units` <= '$weightgms',`pbqi`.`units` <= '$weightmts'))
													)  )" );
			}
		}
		
		
		//transit_days
		if(isset($params['transit_days']) && $params['transit_days']!=''){
			$transit_days = $params['transit_days'];
			if($serviceId == COURIER){
				$Query_buyers_for_sellers->whereRaw ( "IF((`pbq`.`delivery_date` = '0000-00-00'),TRUE, ( IF((1=1),
						DATEDIFF(`pbq`.`delivery_date`,`pbq`.`dispatch_date`)+1 >=  $transit_days,
						IF((1=1),DATEDIFF(`pbq`.`delivery_date` + INTERVAL 3 DAY,`pbq`.`dispatch_date` - INTERVAL 3 DAY)+1 >=  $transit_days, DATEDIFF(`pbq`.`delivery_date` + INTERVAL 3 DAY,`pbq`.`dispatch_date`)+1 >=  $transit_days)
						))) " );
			}
			else{
				$Query_buyers_for_sellers->whereRaw ( "IF((`pbq`.`delivery_date` = '0000-00-00'),TRUE, ( IF((`pbq`.`is_dispatch_flexible` = 0 && `pbq`.`is_delivery_flexible` = 0),
														DATEDIFF(`pbq`.`delivery_date`,`pbq`.`dispatch_date`)+1 >=  $transit_days,
														IF((`pbq`.`is_dispatch_flexible` = 1 && `pbq`.`is_delivery_flexible` = 1), DATEDIFF(`pbq`.`delivery_date` + INTERVAL 3 DAY,`pbq`.`dispatch_date` - INTERVAL 3 DAY)+1 >=  $transit_days, DATEDIFF(`pbq`.`delivery_date` + INTERVAL 3 DAY,`pbq`.`dispatch_date`)+1 >=  $transit_days)
													))) " );
			}
		}

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`lkp_quote_access_id` = 2, `pbqss`.`seller_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$Query_buyers_for_sellers->WhereIn ( 'bq.buyer_id', $selectedSellers );
		}
		if($serviceId == COURIER){
			$QueryBuilder =  $Query_buyers_for_sellers->select ('pbqi.*','pbqi.buyer_quote_id','pbq.lkp_quote_access_id','lppf.pincode as frompincode',
				'lppt.pincode as topincode', 'lt.courier_type','pt.courier_delivery_type','pbq.dispatch_date','pbqi.created_by',
				'us.username','pbq.delivery_date', 'pbqi.lkp_post_status_id','pbq.from_location_id','pbq.to_location_id'
			);
		}
		else {
			$QueryBuilder =  $Query_buyers_for_sellers->select ('pbqi.*','pbqi.buyer_quote_id','pbq.lkp_quote_access_id','lppf.pincode as frompincode',
				'lppt.pincode as topincode', 'lt.load_type','pt.packaging_type_name','pbq.dispatch_date','pbqi.created_by',
				'us.username','pbq.delivery_date', 'pbqi.lkp_post_status_id','pbq.from_location_id','pbq.to_location_id'
			);
		}
		$QueryBuilder->groupBy("pbqi.buyer_quote_id");
		//echo $QueryBuilder->tosql();
		$results = $Query_buyers_for_sellers->get();
		//echo "<pre>";print_R($results);die;
		$matchedbuyerquotes = array();
		foreach($results as $result){
			$matchedbuyerquotes[] = $result->buyer_quote_id;
		}

		//echo $postId;
		//echo "<pre>";print_R($matchedbuyerquotes);die;
		SellerMatchingComponent::insetOrUpdateMatches($serviceId,$postId,$statusId,$matchedbuyerquotes,$matchingtype);
		return true;
	}

	/**
	 * PTL Matching
	 * @param $params
	 * result $query
	 */

	public static function updateAirintOceanMatching($serviceId,$statusId, $postId, $params){
		$loginId = Auth::User()->id;
		//echo "<pre>";print_R($params);
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
			$Query_buyers_for_sellers->where('pbq.lkp_post_status_id', $statusId);
		}
		//Zone or location


		//set from location below varaibles are checking empty or not varaible in buyear search---grid
		if (isset($params['from_location_id']) && $params['from_location_id'] != '') {
			$Query_buyers_for_sellers->where('pbq.from_location_id', $params['from_location_id']);
		}
		//set to location
		if (isset($params['to_location_id']) && $params['to_location_id'] != '') {
			$Query_buyers_for_sellers->where('pbq.to_location_id', $params['to_location_id']);
		}


		//echo "<pre>";print_R($params);die;
		//dispatch dates
		if(isset($params['valid_from']) && $params['valid_from']!='' && isset($params['valid_to']) && $params['valid_to']!=''){
			$valid_from = CommonComponent::convertDateForDatabase($params['valid_from']);
			$valid_to = CommonComponent::convertDateForDatabase($params['valid_to']);
			$valid_from_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_from']) . " - 3 day"));
			$valid_to_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_to']) . " + 3 day"));
			$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`is_dispatch_flexible` = 0, `pbq`.`dispatch_date` between '$valid_from' and '$valid_to', `pbq`.`dispatch_date` between '$valid_from_minus' and '$valid_to_plus'  )  )" );
		}
		//transit_days
		if(isset($params['transit_days']) && $params['transit_days']!=''){
			$transit_days = $params['transit_days'];
			$Query_buyers_for_sellers->whereRaw ( "IF((`pbq`.`delivery_date` = '0000-00-00'),TRUE, ( IF((`pbq`.`is_dispatch_flexible` = 0 && `pbq`.`is_delivery_flexible` = 0),
														DATEDIFF(`pbq`.`delivery_date`,`pbq`.`dispatch_date`)+1 >=  $transit_days,
														IF((`pbq`.`is_dispatch_flexible` = 1 && `pbq`.`is_delivery_flexible` = 1), DATEDIFF(`pbq`.`delivery_date` + INTERVAL 3 DAY,`pbq`.`dispatch_date` - INTERVAL 3 DAY)+1 >=  $transit_days, DATEDIFF(`pbq`.`delivery_date` + INTERVAL 3 DAY,`pbq`.`dispatch_date`)+1 >=  $transit_days)
													))) " );
		}

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`pbq`.`lkp_quote_access_id` = 2, `pbqss`.`seller_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$Query_buyers_for_sellers->WhereIn ( 'bq.buyer_id', $selectedSellers );
		}

		$QueryBuilder =  $Query_buyers_for_sellers->select ('pbqi.*','pbqi.buyer_quote_id','pbq.lkp_quote_access_id', 'lt.load_type','pt.packaging_type_name','pbq.dispatch_date','pbqi.created_by',
			'us.username','pbq.delivery_date', 'pbqi.lkp_post_status_id','pbq.from_location_id','pbq.to_location_id'
		);
		$QueryBuilder->groupBy("pbqi.buyer_quote_id");
//echo $QueryBuilder->tosql();die;
		$results = $Query_buyers_for_sellers->get();
		//echo "<pre>";print_R($results);die;
		$matchedbuyerquotes = array();
		foreach($results as $result){
			$matchedbuyerquotes[] = $result->buyer_quote_id;
		}

		//echo $postId;
		//echo "<pre>";print_R($matchedbuyerquotes);die;
		SellerMatchingComponent::insetOrUpdateMatches($serviceId,$postId,$statusId,$matchedbuyerquotes);
		return true;
	}

	/**
	 * FTL Seller Search
	 * @param $params
	 * result $query
	 */
	public static function updateRelocationDomesticMatching($serviceId,$statusId, $postId, $params,$matchingtype = 1){
		$loginId = Auth::User()->id;
		$Query_buyers_for_sellers = DB::table('relocation_buyer_posts as rbq');
		$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id' );
		$Query_buyers_for_sellers->join ( 'lkp_cities as ct', 'rbq.to_location_id', '=', 'ct.id' );
		$Query_buyers_for_sellers->join('lkp_post_ratecard_types as rt', 'rt.id', '=', 'rbq.lkp_post_ratecard_type_id');
		$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'rbq.buyer_id');
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

		//set district
		if(isset($params['district']) && $params['district']!=''){
			$Query_buyers_for_sellers->where('cf.lkp_district_id',$params['district']);
		}

		//set rate card type
		if (isset($params['post_type']) && $params['post_type'] != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_post_ratecard_type_id', $params['post_type']);
		}
		//set property type
		if (isset($params['propertytypes']) && $params['propertytypes'] != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_property_type_id', $params['propertytypes']);
		}

		//set vehicle category
		if (isset($params['vehicle_category']) && $params['vehicle_category'] != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_vehicle_category_id', $params['vehicle_category']);
		}

		//dispatch dates
		if(isset($params['valid_from']) && $params['valid_from']!='' && isset($params['valid_to']) && $params['valid_to']!=''){
			$valid_from = CommonComponent::convertDateForDatabase($params['valid_from']);
			$valid_to = CommonComponent::convertDateForDatabase($params['valid_to']);
			$Query_buyers_for_sellers->whereRaw ( "`rbq`.`dispatch_date` between '$valid_from' and '$valid_to'" );
		}
		//transit_days
		if(isset($params['transit_days']) && $params['transit_days']!=''){
			$transit_days = $params['transit_days'];
			$Query_buyers_for_sellers->whereRaw ( "IF((`rbq`.`delivery_date` = '0000-00-00'),TRUE, ( IF((`rbq`.`is_delivery_flexible` = 0),
														DATEDIFF(`rbq`.`delivery_date`,`rbq`.`dispatch_date`)+1 >=  $transit_days,
														DATEDIFF(`rbq`.`delivery_date` + INTERVAL 3 DAY,`rbq`.`dispatch_date`)+1 >=  $transit_days
													))) " );
		}

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`lkp_quote_access_id` = 2, `pbqss`.`seller_id` =  $loginId,TRUE )  )");
		$Query_buyers_for_sellers->select ('rbq.*');
		//echo $QueryBuilder->tosql();
		$matchedbuyerquotes = array();
		$results = $Query_buyers_for_sellers->get();
		$matchedbuyerquotes = array();
		foreach($results as $result){
			$matchedbuyerquotes[] = $result->id;
		}

		SellerMatchingComponent::insetOrUpdateMatches($serviceId,$postId,$statusId,$matchedbuyerquotes,$matchingtype);
		return true;
	}
	
	
	/**
	 * RELOCATION AIR Seller Search
	 * @param $params
	 * result $query
	 */
	public static function updateRelocationInternationalMatching($serviceId,$statusId, $postId, $params,$matchingtype = 1){
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
                //set district
		if(isset($params['district']) && $params['district']!=''){
			$Query_buyers_for_sellers->where('cf.lkp_district_id',$params['district']);
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
		if($params['service_type'] == 1){
			$QueryBuilder =  $Query_buyers_for_sellers->select ('rbq.*','us.username','cf.city_name as fromcity','ct.city_name as tocity');
		}else{
			$QueryBuilder =  $Query_buyers_for_sellers->select ('rbq.*','us.username','cf.city_name as fromcity','ct.city_name as tocity','pty.property_type');
		}
			//echo $QueryBuilder->tosql();
			$matchedbuyerquotes = array();
			$results = $Query_buyers_for_sellers->get();
                        
			$matchedbuyerquotes = array();
			foreach($results as $result){
			$matchedbuyerquotes[] = $result->id;
			}
	
			SellerMatchingComponent::insetOrUpdateMatches($serviceId,$postId,$statusId,$matchedbuyerquotes,$matchingtype);
			return true;
	}
	
	
	/**
	 * Sumanth
	 * Relocation Office move
	 * @param $params
	 * result $query
	 */
	public static function updateRelocationOfficeMatching($serviceId,$statusId, $postId, $params,$matchingtype = 1){
		
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
		
		//set district
		if(isset($params['district']) && $params['district']!=''){
			$Query_buyers_for_sellers->where('cf.lkp_district_id',$params['district']);
		}
	
		//dispatch dates
		if(isset($params['valid_from']) && $params['valid_from']!='' && isset($params['valid_to']) && $params['valid_to']!=''){
			$valid_from = CommonComponent::convertDateForDatabase($params['valid_from']);
			$valid_to = CommonComponent::convertDateForDatabase($params['valid_to']);
			$Query_buyers_for_sellers->whereRaw ( "`rbq`.`dispatch_date` between '$valid_from' and '$valid_to'" );
		}
		
	
		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`lkp_quote_access_id` = 2, `pbqss`.`seller_id` =  $loginId,TRUE )  )");
	
		$Query_buyers_for_sellers->select ('rbq.*');

		$matchedbuyerquotes = array();
		$results = $Query_buyers_for_sellers->get();

		$matchedbuyerquotes = array();
		foreach($results as $result){
			$matchedbuyerquotes[] = $result->id;
		}
		if(count($results)>0){
	    $Query_seller_slabs = DB::table('relocationoffice_seller_post_slabs');
		$Query_seller_slabs->where('seller_post_id', $postId);
		$Query_seller_slabs->select ( DB::Raw('max(slab_max_km) as slabmax'));
		$max_slab=$Query_seller_slabs->get();

		if($results[0]->distance<=$max_slab[0]->slabmax){
		
		SellerMatchingComponent::insetOrUpdateMatches($serviceId,$postId,$statusId,$matchedbuyerquotes,$matchingtype);
         }
         }else{
         SellerMatchingComponent::insetOrUpdateMatches($serviceId,$postId,$statusId,$matchedbuyerquotes,$matchingtype);	
         }
		return true;
	}
        
        /**
	 * Sumanth
	 * Relocation Office move
	 * @param $params
	 * result $query
	 */
	public static function updateRelocationPetMatching($serviceId,$statusId, $postId, $params,$matchingtype = 1){
		$loginId = Auth::User()->id;
		$Query_buyers_for_sellers = DB::table('relocationpet_buyer_posts as rbq');
		$Query_buyers_for_sellers->leftjoin( 'lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id' );
		$Query_buyers_for_sellers->leftjoin('users as us', 'us.id', '=', 'rbq.buyer_id');
		$Query_buyers_for_sellers->leftjoin ( 'relocationpet_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id' );
	
		if (isset($statusId) && $statusId != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', $statusId);
		}
	
		//set from location below varaibles are checking empty or not varaible in buyear search---grid
		if (isset($params['from_location_id']) && $params['from_location_id'] != '') {
			$Query_buyers_for_sellers->where('rbq.from_location_id', $params['from_location_id']);
		}
                //set from location below varaibles are checking empty or not varaible in buyear search---grid
		if (isset($params['to_location_id']) && $params['to_location_id'] != '') {
			$Query_buyers_for_sellers->where('rbq.to_location_id', $params['to_location_id']);
		}
		//set district
		if(isset($params['district']) && $params['district']!=''){
			$Query_buyers_for_sellers->where('cf.lkp_district_id',$params['district']);
		}
                //set pet type
		if (isset($params['pettypes']) && $params['pettypes'] != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_pet_type_id', $params['pettypes']);
		}

		//set cage type
		if (isset($params['cagetypes']) && $params['cagetypes'] != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_cage_type_id', $params['cagetypes']);
		}
		//dispatch dates
		if(isset($params['valid_from']) && $params['valid_from']!='' && isset($params['valid_to']) && $params['valid_to']!=''){
			$valid_from = CommonComponent::convertDateForDatabase($params['valid_from']);
			$valid_to = CommonComponent::convertDateForDatabase($params['valid_to']);
			$Query_buyers_for_sellers->whereRaw ( "`rbq`.`dispatch_date` between '$valid_from' and '$valid_to'" );
		}
		
		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`lkp_quote_access_id` = 2, `pbqss`.`seller_id` =  $loginId,TRUE )  )");
	
		$QueryBuilder =  $Query_buyers_for_sellers->select ('rbq.*');
		//echo $QueryBuilder->tosql();
		$matchedbuyerquotes = array();
		$results = $Query_buyers_for_sellers->get();
		//echo "<pre>";print_R($results);die;
		$matchedbuyerquotes = array();
		foreach($results as $result){
			$matchedbuyerquotes[] = $result->id;
		}
		//echo $postId;
		//echo "<pre>";print_R($matchedbuyerquotes);die;
		SellerMatchingComponent::insetOrUpdateMatches($serviceId,$postId,$statusId,$matchedbuyerquotes,$matchingtype);
		return true;
	}

	/**
	 * FTL Seller Search
	 * @param $params
	 * result $query
	 */
	public static function updateTruckHaulMatching($statusId, $postId, $params,$matchingtype = 1){		
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

		//set district
		if(isset($params['district']) && $params['district']!=''){
			$Query_buyers_for_sellers->where('cf.lkp_district_id',$params['district']);
		}

		//set load type
		if(isset($params['lkp_load_type_id']) && ($params['lkp_load_type_id']!='') && ($params['lkp_load_type_id'] != 11)){
			//$Query_buyers_for_sellers->where('bqi.lkp_load_type_id', $params['lkp_load_type_id']);
			$loadtypeid = $params ['lkp_load_type_id'];
			$Query_buyers_for_sellers->whereRaw ( "( IF(`bqi`.`lkp_load_type_id` != 11, `bqi`.`lkp_load_type_id` = $loadtypeid,TRUE )  )");
		}
		//set vehicle type
		if(isset($params['lkp_vehicle_type_id']) && ($params['lkp_vehicle_type_id']!='') && ($params['lkp_vehicle_type_id'] != 20)){
			//$Query_buyers_for_sellers->where('bqi.lkp_vehicle_type_id', $params['lkp_vehicle_type_id']);
			$vehicletypeid = $params ['lkp_vehicle_type_id'];
			$Query_buyers_for_sellers->whereRaw ( "( IF(`bqi`.`lkp_vehicle_type_id` != 20, `bqi`.`lkp_vehicle_type_id` = $vehicletypeid,TRUE )  )");
		}
		//dispatch dates
		if(isset($params['valid_from']) && $params['valid_from']!='' && isset($params['valid_to']) && $params['valid_to']!=''){
			$valid_from = CommonComponent::convertDateForDatabase($params['valid_from']);
			$valid_to = CommonComponent::convertDateForDatabase($params['valid_to']);
			$valid_from_minus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_from']) . " - 3 day"));
			$valid_to_plus = date('Y-m-d', strtotime(str_replace('/', '-',$params['valid_to']) . " + 3 day"));
			$Query_buyers_for_sellers->whereRaw ( "( IF(`bqi`.`is_dispatch_flexible` = 0, `bqi`.`dispatch_date` between '$valid_from' and '$valid_to', `bqi`.`dispatch_date` between '$valid_from_minus' and '$valid_to_plus'  )  )" );
		}
		//transit_days
		if(isset($params['transit_days']) && $params['transit_days']!=''){
			//$transit_days = $params['transit_days'];
			//$Query_buyers_for_sellers->whereRaw ( "IF((`bqi`.`delivery_date` = '0000-00-00'),TRUE, ( IF((`bqi`.`is_dispatch_flexible` = 0 && `bqi`.`is_delivery_flexible` = 0),
														//DATEDIFF(`bqi`.`delivery_date`,`bqi`.`dispatch_date`)+1 >=  $transit_days,
														//IF((`bqi`.`is_dispatch_flexible` = 1 && `bqi`.`is_delivery_flexible` = 1), DATEDIFF(`bqi`.`delivery_date` + INTERVAL 3 DAY,`bqi`.`dispatch_date` - INTERVAL 3 DAY)+1 >=  $transit_days, DATEDIFF(`bqi`.`delivery_date` + INTERVAL 3 DAY,`bqi`.`dispatch_date`)+1 >=  $transit_days)
													//))) " );
		}

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`bq`.`lkp_quote_access_id` = 2, `bqss`.`seller_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$Query_buyers_for_sellers->WhereIn ( 'bq.buyer_id', $selectedSellers );
		}
		$Query_buyers_for_sellers->select ('bq.lkp_quote_access_id','bqi.id','cf.city_name as fromcity',
			'ct.city_name as tocity', 'lt.load_type','vt.vehicle_type','bqi.dispatch_date','bqi.created_by',
			'bqi.price','us.username','bqi.dispatch_date', 'bqi.lkp_post_status_id','bq.buyer_id','us.username'
		);
		//echo $Query_buyers_for_sellers->tosql();
		$results = $Query_buyers_for_sellers->get();
		//echo "<pre>";print_R($results);die;

		$matchedbuyerquotes = array();
		foreach($results as $result){
			$matchedbuyerquotes[] = $result->id;
		}


		SellerMatchingComponent::insetOrUpdateMatches(ROAD_TRUCK_HAUL,$postId,$statusId,$matchedbuyerquotes,$matchingtype);

		return true;
		//$result = $Query_buyers_for_sellers->get ();
	}
	/**
	 * Inserting or updating matches
	 * @param $serviceId
	 * @param $postId
	 * @param $matchedPosts
	 * @return bool
	 */
	public static function insetOrUpdateMatches($serviceId,$postId, $statusId,$matchedQuotes,$matchingtype = 1){
		$matchedQuotes = array_unique($matchedQuotes);
		DB::table('matching_items')->where('seller_post_id', '=', $postId)->where('matching_type_id', '=', $matchingtype)->delete();
		if($statusId == OPEN){
			$recordsToInsert = array();
			foreach($matchedQuotes as $matchedQuote){
				$recordToInsert = array();
				$recordToInsert['service_id'] = $serviceId;
				$recordToInsert['buyer_quote_id'] = $matchedQuote;
				$recordToInsert['seller_post_id'] = $postId;
				$recordToInsert['matching_type_id'] = $matchingtype;
				$recordToInsert['created_at'] =  date ( 'Y-m-d H:i:s' );
				$recordToInsert['updated_at'] =  date ( 'Y-m-d H:i:s' );
				//echo "<pre>";print_R($recordToInsert);die;
				$recordsToInsert[] = $recordToInsert;
			}
			//echo "<pre>";print_R($recordsToInsert);
			$MatchingItem = new MatchingItem;
			$MatchingItem->insert($recordsToInsert);
			//echo "inserted ";die;
		}
		return true;
	}
	/**
	 * Return Matched results
	 * @param $service
	 * @param $postId
	 * @return mixed
	 */
	public static function getMatchedResults($service,$postId){
		return DB::table('matching_items')->where('service_id', '=', $service)->where('seller_post_id', '=', $postId)->where('matching_type_id', '=', 1)->get();
	}

	/**
	 * Return Seller Leads
	 * @param $service
	 * @param $postId
	 * @return mixed
	 */
	public static function getSellerLeads($service,$postId){
		$sellerleads = DB::table('matching_items')->where('service_id', '=', $service)->where('seller_post_id', '=', $postId)->where('matching_type_id', '=', 2)->get();
		$enquiries = SellerMatchingComponent::getMatchedResults($service,$postId);
		$enquiryarray = array();
		foreach($enquiries as $enquiry){
			$enquiryarray[] = $enquiry->buyer_quote_id;
		}
		foreach($sellerleads as $key => $sellerlead){
			if(in_array($sellerlead->buyer_quote_id,$enquiryarray)){
				unset($sellerleads[$key]);
			}
		}
		return $sellerleads;
	}

	public static function removeFromMatching($serviceId,$postItemId){
		DB::table('matching_items')->where('seller_post_id', '=', $postItemId)->where('service_id', '=', $serviceId)->delete();
		return true;
	}
        
        /**
	 * FTL Seller Search
	 * @param $params
	 * result $query
	 */
	public static function updateRelocationGmMatching($serviceId,$statusId, $postId, $params,$matchingtype = 1){
		$gmServiceTypes = CommonComponent::getAllGMServiceTypesforSeller();
                $loginId = Auth::User()->id;
		$Query_buyers_for_sellers = DB::table('relocationgm_buyer_posts as rbq');
                $Query_buyers_for_sellers->leftjoin( 'relocationgm_buyer_quote_items as rbqi', 'rbqi.buyer_post_id', '=', 'rbq.id' );
                $Query_buyers_for_sellers->leftjoin( 'lkp_relocationgm_services as lrs', 'lrs.id', '=', 'rbqi.lkp_gm_service_id' );
		$Query_buyers_for_sellers->leftjoin( 'lkp_cities as cf', 'rbq.location_id', '=', 'cf.id' );
		//$Query_buyers_for_sellers->join ( 'lkp_cities as ct', 'rbq.to_location_id', '=', 'ct.id' );
		//$Query_buyers_for_sellers->join('lkp_post_ratecard_types as rt', 'rt.id', '=', 'rbq.lkp_post_ratecard_type_id');
		$Query_buyers_for_sellers->leftjoin('users as us', 'us.id', '=', 'rbq.buyer_id');
		$Query_buyers_for_sellers->leftjoin ( 'relocationgm_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id' );

		if (isset($statusId) && $statusId != '') {
			$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', $statusId);
		}
                //echo "<pre>";print_r($params);die;
		
		//set to location
		if (isset($params['to_location_id']) && $params['to_location_id'] != '') {
			$Query_buyers_for_sellers->where('rbq.location_id', $params['to_location_id']);
		}

		//set district
		if(isset($params['district']) && $params['district']!=''){
			$Query_buyers_for_sellers->where('cf.lkp_district_id',$params['district']);
		}

		
		//set rate service wise
                $services_gm=array();
                foreach($gmServiceTypes as $gmServiceType){
                    $str_name   =   strtolower(str_replace(' ','_',$gmServiceType->service_type));
                    if(isset($params[$str_name]) && $params[$str_name]!=''){
                        $services_gm[] = $gmServiceType->service_type;
                        
                    }
                }
                if(!empty($services_gm)){
                $Query_buyers_for_sellers->whereIn("lrs.service_type", $services_gm);
                }
                
//		if (isset($params['propertytypes']) && $params['propertytypes'] != '') {
//			$Query_buyers_for_sellers->where('rbq.lkp_property_type_id', $params['propertytypes']);
//		}


		//dispatch dates
		if(isset($params['valid_from']) && $params['valid_from']!='' && isset($params['valid_to']) && $params['valid_to']!=''){
			$valid_from = CommonComponent::convertDateForDatabase($params['valid_from']);
			$valid_to = CommonComponent::convertDateForDatabase($params['valid_to']);
			$Query_buyers_for_sellers->whereRaw ( "`rbq`.`dispatch_date` between '$valid_from' and '$valid_to'" );
		}
		

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`rbq`.`lkp_quote_access_id` = 2, `pbqss`.`seller_id` =  $loginId,TRUE )  )");
		$Query_buyers_for_sellers->select ('rbq.*');
		//echo $QueryBuilder->tosql();
		$matchedbuyerquotes = array();
		$results = $Query_buyers_for_sellers->get();
		$matchedbuyerquotes = array();
		foreach($results as $result){
			$matchedbuyerquotes[] = $result->id;
		}

		SellerMatchingComponent::insetOrUpdateMatches($serviceId,$postId,$statusId,$matchedbuyerquotes,$matchingtype);
		return true;
	}
}
