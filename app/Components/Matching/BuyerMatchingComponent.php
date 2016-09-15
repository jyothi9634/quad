<?php
namespace App\Components\Matching;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use App\Components\CommonComponent;
use App\Components\Search\BuyersearchComponent;
use App\Models\MatchingItem;
use Log;

class BuyerMatchingComponent {
	public static function doMatching($serviceId, $quoteId, $statusId, $params){

		switch ($serviceId) {
			case ROAD_FTL       :
				//matching
				$queryBuilder = BuyerMatchingComponent::updateFtlMatching($statusId,$quoteId,$params,1);
				//seller leads
				if(isset($params['from_location_id']) && !empty($params['from_location_id'])){
					$district  = CommonComponent::getDistrict($params['from_location_id'],$serviceId);
					if(!empty($district)){
						unset($params['from_location_id']);unset($params['to_location_id']);
						unset($params['from_date']);unset($params['to_date']);
						unset($params['lkp_load_type_id']);unset($params['lkp_vehicle_type_id']);


						$params['district'] = $district;
						$queryBuilder = BuyerMatchingComponent::updateFtlMatching($statusId,$quoteId,$params,2);
					}
				}
				break;
			case ROAD_PTL       :
			case RAIL:
			case AIR_DOMESTIC:
			case COURIER:
				//matching
				$queryBuilder = BuyerMatchingComponent::updatePtlMatching($statusId,$quoteId,$params,$serviceId = Session::get('service_id'),1);
				//seller leads
				if(isset($params['ptlFromLocation'][0]) && !empty($params['ptlFromLocation'][0])){
					$district  = CommonComponent::getDistrict($params['ptlFromLocation'][0],$serviceId);
					if(!empty($district)){
						unset($params['ptlFromLocation']);unset($params['ptlToLocation']);
						$params['district'][0] = $district;
						$queryBuilder = BuyerMatchingComponent::updatePtlMatching($statusId,$quoteId,$params,$serviceId = Session::get('service_id'),2);
					}
				}
				break;

			case AIR_INTERNATIONAL:
			case OCEAN:
				$queryBuilder = BuyerMatchingComponent::updateAirintOceanMatching($statusId,$quoteId,$params,$serviceId = Session::get('service_id'));
				break;


			case ROAD_INTRACITY :
				$queryBuilder = BuyerMatchingComponent::updateIntracityMatching($statusId,$params);
				break;
			case ROAD_TRUCK_HAUL       :
				//matching
				$queryBuilder = BuyerMatchingComponent::updateTruckHaulMatching($statusId,$quoteId,$params,1);
				//seller leads
				if(isset($params['from_location_id']) && !empty($params['from_location_id'])){
					$district  = CommonComponent::getDistrict($params['from_location_id'],$serviceId);
					if(!empty($district)){
						unset($params['from_location_id']);unset($params['to_location_id']);
						unset($params['from_date']);unset($params['to_date']);
						unset($params['lkp_load_type_id']);unset($params['lkp_vehicle_type_id']);


						$params['district'] = $district;
						$queryBuilder = BuyerMatchingComponent::updateTruckHaulMatching($statusId,$quoteId,$params,2);
					}
				}
				break;
				
		case ROAD_TRUCK_LEASE       :
					//matching
				$queryBuilder = BuyerMatchingComponent::updateTruckLeaseMatching($statusId,$quoteId,$params,1);
					//seller leads
				if(isset($params['from_location_id'])){
					$district  = CommonComponent::getDistrict($params['from_location_id'],$serviceId);
					if(!empty($district)){
						unset($params['from_location_id']);
						unset($params['from_date']);
						unset($params['to_date']);
						unset($params['lkp_vehicle_type_id']);
						$params['district'] = $district;
						$queryBuilder = BuyerMatchingComponent::updateTruckLeaseMatching($statusId,$quoteId,$params,2);
						}
					}
					break;
			case RELOCATION_DOMESTIC       :
				//matching
				$queryBuilder = BuyerMatchingComponent::updateRelocationSpotMatching($statusId,$quoteId,$params,1);
				//seller leads
				if(isset($params['from_location_id']) && !empty($params['from_location_id'])){
					$district  = CommonComponent::getDistrict($params['from_location_id'],$serviceId);
					if(!empty($district)){
						unset($params['from_location_id']);unset($params['to_location_id']);
                                                unset($params['from_date']);
						unset($params['to_date']);
						$params['district'] = $district;
						$queryBuilder = BuyerMatchingComponent::updateRelocationSpotMatching($statusId,$quoteId,$params,2);
					}
				}
				break;
			case RELOCATION_OFFICE_MOVE      :
					//matching
					$queryBuilder = BuyerMatchingComponent::updateRelocationOfficeSpotMatching($statusId,$quoteId,$params,1);
					//seller leads
					if(isset($params['from_location_id']) && !empty($params['from_location_id'])){
						$district  = CommonComponent::getDistrict($params['from_location_id'],$serviceId);
						if(!empty($district)){
							unset($params['from_location_id']);
							$params['district'] = $district;
							$queryBuilder = BuyerMatchingComponent::updateRelocationOfficeSpotMatching($statusId,$quoteId,$params,2);
						}
					}
					break;
                        case RELOCATION_PET_MOVE      :
					//matching
					$queryBuilder = BuyerMatchingComponent::updateRelocationPetSpotMatching($statusId,$quoteId,$params,1);
					//seller leads
					if(isset($params['from_location_id']) && !empty($params['from_location_id'])){
						$district  = CommonComponent::getDistrict($params['from_location_id'],$serviceId);
						if(!empty($district)){
							unset($params['from_location_id']);unset($params['to_location_id']);
                                                        unset($params['from_date']);unset($params['to_date']);
                                                        unset($params['selPettype']);unset($params['selCageType']);
							$params['district'] = $district;
							$queryBuilder = BuyerMatchingComponent::updateRelocationPetSpotMatching($statusId,$quoteId,$params,2);
						}
					}
					break;
                        case RELOCATION_INTERNATIONAL      :
					//matching
					$queryBuilder = BuyerMatchingComponent::updateRelocationIntSpotMatching($statusId,$quoteId,$params,1);
					//seller leads
					if(isset($params['from_location_id_intre']) && !empty($params['from_location_id_intre'])){
						
                                            $district  = CommonComponent::getDistrict($params['from_location_id_intre'],$serviceId);
						//echo $district;exit;
                                                if(!empty($district)){
							unset($params['from_location_id_intre']);unset($params['to_location_id_intre']);
                                                        unset($params['valid_from']);unset($params['valid_to']);
                                                        unset($params['from_location_id']);unset($params['to_location_id']);
                                                        unset($params['from_date']);unset($params['to_date']);
                                                        unset($params['shipment_volume_type_id']);
                                                        unset($params['slab_id']);unset($params['weight']);
							$params['seller_district_id_intre'] = $district;
                                                        $params['district'] = $district;
							$queryBuilder = BuyerMatchingComponent::updateRelocationIntSpotMatching($statusId,$quoteId,$params,2);
						}
					}
					break;    
                        case RELOCATION_GLOBAL_MOBILITY       :
                            $gmServiceTypes = CommonComponent::getAllGMServiceTypesforSeller();
				//matching
				$queryBuilder = BuyerMatchingComponent::updateRelocationGmSpotMatching($statusId,$quoteId,$params,1);
				//seller leads
				if(isset($params['to_location_id']) && !empty($params['to_location_id'])){
					$district  = CommonComponent::getDistrict($params['to_location_id'],$serviceId);
					if(!empty($district)){
						unset($params['to_location_id']);
                                                unset($params['from_date']);
						unset($params['to_date']);
                                                foreach($gmServiceTypes as $gmServiceType){
                                                    $str_name   =   strtolower(str_replace(' ','_',$gmServiceType->service_type));
                                                    if(isset($params[$str_name]) && $params[$str_name]!=''){
                                                       
                                                        unset($params[$str_name]);
                                                    }
                                                }
						$params['district'] = $district;
						$queryBuilder = BuyerMatchingComponent::updateRelocationGmSpotMatching($statusId,$quoteId,$params,2);
					}
				}
				break;                
			default             :
				break;
		}
		return true;

	}
	/**
	 * FTL Buyer Matching Update
	 * @param $status, $quoteId, $params
	 * result true
	 */
	public static function updateFtlMatching($statusId, $quoteId, $params,$matchingtype = 1){

		$gridBuyer = BuyersearchComponent::search($roleId=null,ROAD_FTL,$statusId, $params );
		$results = $gridBuyer->get();

		$matchedsellerposts = array();
		foreach($results as $result){
			$matchedsellerposts[] = $result->id;
		}
		BuyerMatchingComponent::insetOrUpdateMatches(ROAD_FTL,$quoteId,$statusId,$matchedsellerposts,$matchingtype);
		return true;
	}


	/**
	 * PTL Buyer Matching Update
	 * @param $params
	 * result $query
	 */
	public static function updatePtlMatching($statusId, $quoteId, $params,$serviceId,$matchingtype = 1){

		$results = BuyersearchComponent::search($roleId=null,$serviceId,$statusId, $params );
		//$results = $gridBuyer->get();

		$matchedsellerposts = array();
		foreach($results as $result){
			$matchedsellerposts[] = $result->id;
		}
		BuyerMatchingComponent::insetOrUpdateMatches($serviceId,$quoteId,$statusId,$matchedsellerposts,$matchingtype);
		return true;
	}

	/**
	 * Air Buyer Matching Update
	 * @param $params
	 * result $query
	 */
	public static function updateAirintOceanMatching($statusId, $quoteId, $params,$serviceId){

		$results = BuyersearchComponent::search($roleId=null,$serviceId,$statusId, $params );
		//$results = $gridBuyer->get();

		$matchedsellerposts = array();
		foreach($results as $result){
			$matchedsellerposts[] = $result->id;
		}
		BuyerMatchingComponent::insetOrUpdateMatches($serviceId,$quoteId,$statusId,$matchedsellerposts);
		return true;
	}
	/**
	 * Intracity Buyer Matching Update
	 * @param $params
	 * result $query
	 */
	public static function updateIntracityMatching($statusId, $quoteId, $params){
		$gridBuyer = BuyersearchComponent::search($roleId=null,ROAD_INTRACITY,$statusId, $params );
		$results = $gridBuyer->get();

		$matchedsellerposts = array();
		foreach($results as $result){
			$matchedsellerposts[] = $result->id;
		}
		BuyerMatchingComponent::insetOrUpdateMatches(ROAD_INTRACITY,$quoteId,$statusId,$matchedsellerposts);
		return $gridBuyer;
	}
	/**
	 * Relocation Buyer Matching Update
	 * @param $status, $quoteId, $params
	 * result true
	 */
	public static function updateRelocationSpotMatching($statusId, $quoteId, $params,$matchingtype = 1){

		$gridBuyer = BuyersearchComponent::search($roleId=null,RELOCATION_DOMESTIC,$statusId, $params );
		$results = $gridBuyer->get();

		$matchedsellerposts = array();
		foreach($results as $result){
			$matchedsellerposts[] = $result->id;
		}
		BuyerMatchingComponent::insetOrUpdateMatches(RELOCATION_DOMESTIC,$quoteId,$statusId,$matchedsellerposts,$matchingtype);
		return true;
	}
	
	/**
	 * Relocation Office Buyer Matching Update
	 * @param $status, $quoteId, $params
	 * result true
	 */
	public static function updateRelocationOfficeSpotMatching($statusId, $quoteId, $params,$matchingtype = 1){
	
		$gridBuyer = BuyersearchComponent::search($roleId=null,RELOCATION_OFFICE_MOVE,$statusId, $params );
		$results = $gridBuyer->get();
																				    				
		$matchedsellerposts = array();
		foreach($results as $result){
			$matchedsellerposts[] = $result->id;
		}
		BuyerMatchingComponent::insetOrUpdateMatches(RELOCATION_OFFICE_MOVE,$quoteId,$statusId,$matchedsellerposts,$matchingtype);
		return true;
	}
        
        
        /**
	 * Relocation Pet move Buyer Matching Update
	 * @param $status, $quoteId, $params
	 * result true
	 */
	public static function updateRelocationPetSpotMatching($statusId, $quoteId, $params,$matchingtype = 1){
	
		$gridBuyer = BuyersearchComponent::search($roleId=null,RELOCATION_PET_MOVE,$statusId, $params );
		$results = $gridBuyer->get();
																				    				
		$matchedsellerposts = array();
		foreach($results as $result){
			$matchedsellerposts[] = $result->postid;
		}
		BuyerMatchingComponent::insetOrUpdateMatches(RELOCATION_PET_MOVE,$quoteId,$statusId,$matchedsellerposts,$matchingtype);
		return true;
	}
        
        /**
	 * Relocation Pet move Buyer Matching Update
	 * @param $status, $quoteId, $params
	 * result true
	 */
	public static function updateRelocationIntSpotMatching($statusId, $quoteId, $params,$matchingtype = 1){
	
		$gridBuyer = BuyersearchComponent::search($roleId=null,RELOCATION_INTERNATIONAL,$statusId, $params );
		$results = $gridBuyer->get();
//                echo "<pre>";print_r($results);
//                        if($matchingtype==2)exit;
//                        if($matchingtype==1){echo "hi";//exit;
//                        }
		$matchedsellerposts = array();
		foreach($results as $result){
			$matchedsellerposts[] = $result->postid;
		}
		BuyerMatchingComponent::insetOrUpdateMatches(RELOCATION_INTERNATIONAL,$quoteId,$statusId,$matchedsellerposts,$matchingtype);
		return true;
	}
        

	/**
	 * Truck Haul Buyer Matching Update
	 * @param $status, $quoteId, $params
	 * result true
	 */
	public static function updateTruckHaulMatching($statusId, $quoteId, $params,$matchingtype = 1){

		$gridBuyer = BuyersearchComponent::search($roleId=null,ROAD_TRUCK_HAUL,$statusId, $params );
		$results = $gridBuyer->get();

		$matchedsellerposts = array();
		foreach($results as $result){
			$matchedsellerposts[] = $result->id;
		}
		BuyerMatchingComponent::insetOrUpdateMatches(ROAD_TRUCK_HAUL,$quoteId,$statusId,$matchedsellerposts,$matchingtype);
		return true;
	}
	
	/**
	 * Truck Haul Buyer Matching Update
	 * @param $status, $quoteId, $params
	 * result true
	 */
	public static function updateTruckLeaseMatching($statusId, $quoteId, $params,$matchingtype = 1){
	
		$gridBuyer = BuyersearchComponent::search($roleId=null,ROAD_TRUCK_LEASE,$statusId, $params );
		$results = $gridBuyer->get();
	
		$matchedsellerposts = array();
		foreach($results as $result){
			$matchedsellerposts[] = $result->id;
		}
		BuyerMatchingComponent::insetOrUpdateMatches(ROAD_TRUCK_LEASE,$quoteId,$statusId,$matchedsellerposts,$matchingtype);
		return true;
	}

	/**
	 * Inserting or updating matches
	 * @param $serviceId
	 * @param $quoteId
	 * @param $matchedPosts
	 * @return bool
	 */
	public static function insetOrUpdateMatches($serviceId,$quoteId, $statusId,$matchedPosts,$matchingtype = 1){
		$matchedPosts = array_unique($matchedPosts);
		DB::table('matching_items')->where('buyer_quote_id', '=', $quoteId)->where('matching_type_id', '=', $matchingtype)->delete();
		if($statusId == OPEN){
			$recordsToInsert = array();
			foreach($matchedPosts as $matchedPost){
				$recordToInsert = array();
				$recordToInsert['service_id'] = $serviceId;
				$recordToInsert['buyer_quote_id'] = $quoteId;
				$recordToInsert['seller_post_id'] = $matchedPost;
				$recordToInsert['matching_type_id'] = $matchingtype;
				$recordToInsert['created_at'] =  date ( 'Y-m-d H:i:s' );
				$recordToInsert['updated_at'] =  date ( 'Y-m-d H:i:s' );
				$recordsToInsert[] = $recordToInsert;
			}
			$MatchingItem = new MatchingItem;
			$MatchingItem->insert($recordsToInsert);
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
		return DB::table('matching_items')->where('service_id', '=', $service)->where('buyer_quote_id', '=', $postId)->where('matching_type_id', '=', 1)->get();
	}

	public static function removeFromMatching($serviceId,$quoteItemId){
		DB::table('matching_items')->where('buyer_quote_id', '=', $quoteItemId)->where('service_id', '=', $serviceId)->delete();
		return true;
	}
        
        /**
	 * Relocation GM Buyer Matching Update
	 * @param $status, $quoteId, $params
	 * result true
	 */
	public static function updateRelocationGmSpotMatching($statusId, $quoteId, $params,$matchingtype = 1){

		$gridBuyer = BuyersearchComponent::search($roleId=null,RELOCATION_GLOBAL_MOBILITY,$statusId, $params );
		$results = $gridBuyer->get();

		$matchedsellerposts = array();
		foreach($results as $result){
			$matchedsellerposts[] = $result->id;
		}
		BuyerMatchingComponent::insetOrUpdateMatches(RELOCATION_GLOBAL_MOBILITY,$quoteId,$statusId,$matchedsellerposts,$matchingtype);
		return true;
	}
}
