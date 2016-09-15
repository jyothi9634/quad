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
class TermSellerSearchComponent {

	public static function search($roleId, $serviceId,$statusId,$params) {
		$monolog = \Log::getMonolog();
		$monolog->pushHandler(new \Monolog\Handler\FirePHPHandler());
		$monolog->addInfo('Term Seller Search results', array('Buyer search params' => $params,'c'=>1));
		
		$queryBuilder = TermSellerSearchComponent::getSellerSearchResults($serviceId,OPEN,$params);
		
		$results = $queryBuilder->get();
		$sqlquery = $queryBuilder->tosql();
		$monolog->addInfo('Term Seller Search query', array('Seller search results' => $sqlquery,'c'=>1));
		$monolog->addInfo('Term Seller Search results', array('Seller search results' => $results,'c'=>1));
		$monolog->addInfo('Term  Seller Search binding', array('Buyer search binding' => $queryBuilder->getBindings(),'c'=>1));
		return $queryBuilder;
	}

	/**
	 * FTL Seller Search
	 * @param $params
	 * result $query
	 */
	public static function getSellerSearchResults($serviceId, $statusId, $params){

		//echo "<pre>";print_R($params);die;
		$loginId = Auth::User()->id;
		$Query_buyers_for_sellers = DB::table ( 'term_buyer_quotes as bq' );
		$Query_buyers_for_sellers->join ( 'term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id' );
		$Query_buyers_for_sellers->leftjoin( 'lkp_load_types as lt', 'lt.id', '=', 'bqi.lkp_load_type_id' );
		$Query_buyers_for_sellers->leftjoin( 'lkp_vehicle_types as vt', 'vt.id', '=', 'bqi.lkp_vehicle_type_id' );
		if($serviceId == ROAD_FTL || $serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_INTERNATIONAL){
			$Query_buyers_for_sellers->leftjoin( 'lkp_cities as cf', 'bqi.from_location_id', '=', 'cf.id' );
			$Query_buyers_for_sellers->leftjoin ( 'lkp_cities as ct', 'bqi.to_location_id', '=', 'ct.id' );
		}else if((Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC)) {
			$Query_buyers_for_sellers->leftjoin('lkp_ptl_pincodes as cf', 'bqi.from_location_id', '=', 'cf.id');
			$Query_buyers_for_sellers->leftjoin('lkp_ptl_pincodes as ct', 'bqi.to_location_id', '=', 'ct.id');
		}elseif(Session::get ( 'service_id' )==AIR_INTERNATIONAL) {
			$Query_buyers_for_sellers->leftjoin('lkp_airports as cf', 'bqi.from_location_id', '=', 'cf.id');
			$Query_buyers_for_sellers->leftjoin('lkp_airports as ct', 'bqi.from_location_id', '=', 'cf.id');
		}elseif(Session::get ( 'service_id' )==OCEAN) {
			$Query_buyers_for_sellers->leftjoin('lkp_seaports as cf', 'bqi.from_location_id', '=', 'cf.id');
			$Query_buyers_for_sellers->leftjoin('lkp_seaports as ct', 'bqi.from_location_id', '=', 'cf.id');
		}elseif(Session::get ( 'service_id' )==COURIER) {
			$Query_buyers_for_sellers->join('lkp_ptl_pincodes as lppf', 'bqi.from_location_id', '=', 'lppf.id');
			$Query_buyers_for_sellers->leftjoin('lkp_ptl_pincodes as lppt', function($join)
			{
				$join->on('bqi.to_location_id', '=', 'lppt.id');
				$join->on(DB::raw('bq.lkp_courier_delivery_type_id'),'=',DB::raw(1));
					
			});
			$Query_buyers_for_sellers->leftjoin('lkp_countries as lppt1', function($join)
			{
				$join->on('bqi.to_location_id', '=', 'lppt1.id');
				$join->on(DB::raw('bq.lkp_courier_delivery_type_id'),'=',DB::raw(2));
					
			});
		}else if($serviceId == RELOCATION_GLOBAL_MOBILITY){
			$Query_buyers_for_sellers->leftjoin( 'lkp_cities as cf', 'bqi.from_location_id', '=', 'cf.id' );
			$Query_buyers_for_sellers->leftjoin( 'lkp_relocationgm_services as rlgms', 'bqi.lkp_gm_service_id', '=', 'rlgms.id' );
		}
		
		if($serviceId==COURIER){
			$Query_buyers_for_sellers->where('bq.lkp_courier_type_id', $params['courier_or_types']);
		}
		
                if($serviceId==RELOCATION_INTERNATIONAL){
			$Query_buyers_for_sellers->where('bq.lkp_lead_type_id', $params['term_service_type']);
		}
		

		$Query_buyers_for_sellers->join ( 'users as us', 'us.id', '=', 'bq.buyer_id' );
		$Query_buyers_for_sellers->leftjoin ( 'term_buyer_quote_selected_sellers as bqss', 'bqss.term_buyer_quote_id', '=', 'bq.id' );

		$Query_buyers_for_sellers->leftJoin('term_buyer_bid_dates as tbbd', function($join)
													{
														$join->on('tbbd.term_buyer_quote_id', '=', 'bq.id');
														$join->on('tbbd.updated_by','=',DB::raw(0));
													});


		if(isset($statusId) && $statusId != ''){
			$Query_buyers_for_sellers->where('bq.lkp_post_status_id', $statusId);
		}
		//bidtypeFilter
		if(isset($params['bid_type_value']) && $params['bid_type_value']!='') {
			$bidtypeval=explode(",",$params['bid_type_value']);
			$Query_buyers_for_sellers->whereIn('bq.lkp_bid_type_id', $bidtypeval);
		}
		//service id
		if(isset($serviceId) && $serviceId != ''){
			$Query_buyers_for_sellers->where('bq.lkp_service_id', $serviceId);
		}
		//service id
		if(isset($params['term_post_rate_card_type']) && $params['term_post_rate_card_type'] != ''){
			$Query_buyers_for_sellers->where('bq.lkp_post_ratecard_type', $params['term_post_rate_card_type']);
		}

		if(isset($params['term_from_location_id']) && $params['term_from_location_id']!='') {
			$params['from_location_id'] = $params['term_from_location_id'];
		}
		if(isset($params['term_to_location_id']) && $params['term_to_location_id']!='') {
			$params['to_location_id'] = $params['term_to_location_id'];
		}
		if(isset($params['zone_or_location']) && ($params['zone_or_location'] == 1)){
			//set from location below varaibles are checking empty or not varaible in buyear search---grid
			if(isset($params['term_from_location_id']) && $params['from_location_id']!='') {
				$fromlocatons = TermSellerSearchComponent::getPincodesByZoneId($params['from_location_id']);
				if (isset($params['from_location_id']) && $params['from_location_id'] != '' && count($fromlocatons) > 0) {
					$Query_buyers_for_sellers->whereIn('bqi.from_location_id', $fromlocatons);
				}
			}
			//set to location
			if(isset($params['term_to_location_id']) && $params['term_to_location_id']!='') {
				$tolocatons = TermSellerSearchComponent::getPincodesByZoneId($params['term_to_location_id']);
				if (isset($params['term_to_location_id']) && $params['term_to_location_id'] != '' && count($tolocatons) > 0) {
					$Query_buyers_for_sellers->whereIn('bqi.to_location_id', $tolocatons);
				}
			}


		}else{
			//set from location below varaibles are checking empty or not varaible in buyear search---grid
			if(isset($params['from_location_id']) && $params['from_location_id']!=''){
				$Query_buyers_for_sellers->where('bqi.from_location_id', $params['from_location_id']);
			}
			//set to location
			if(isset($params['to_location_id']) && $params['to_location_id']!=''){
				$Query_buyers_for_sellers->where('bqi.to_location_id',$params['to_location_id']);
			}
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
		//set packaging type
		if(isset($params['lkp_packaging_type_id']) && ($params['lkp_packaging_type_id']!='')){
			$Query_buyers_for_sellers->where ( 'bqi.lkp_packaging_type_id',$params['lkp_packaging_type_id']);
		}

		//set lkp_air_ocean_shipment_type_id type
		if(isset($params['lkp_air_ocean_shipment_type_id']) && ($params['lkp_air_ocean_shipment_type_id']!='')){
			$Query_buyers_for_sellers->where ( 'bqi.lkp_air_ocean_shipment_type_id',$params['lkp_air_ocean_shipment_type_id']);
		}
		//set lkp_air_ocean_sender_identity_id type
		if(isset($params['lkp_air_ocean_sender_identity_id']) && ($params['lkp_air_ocean_sender_identity_id']!='')){
			$Query_buyers_for_sellers->where ( 'bqi.lkp_air_ocean_sender_identity_id',$params['lkp_air_ocean_sender_identity_id']);
		}


		if(isset($params['relgm_service_type']) && ($params['relgm_service_type']!='')){
			$Query_buyers_for_sellers->where ( 'bqi.lkp_gm_service_id',$params['relgm_service_type']);
		}

		//Checking private sellers
		$Query_buyers_for_sellers->whereRaw ( "( IF(`bq`.`lkp_quote_access_id` = 2, `bqss`.`seller_id` =  $loginId,TRUE )  )");

		//left nav seller filters
		if(isset($params['selected_users']) && $params['selected_users']!='') {
			$selectedSellers = array_filter(explode(",", $params['selected_users']));
			$Query_buyers_for_sellers->WhereIn ( 'bq.buyer_id', $selectedSellers );
		}
		$Query_buyers_for_sellers->groupBy("bqi.term_buyer_quote_id");
		if($serviceId == ROAD_FTL){
			$Query_buyers_for_sellers->select ('bq.*','bq.lkp_quote_access_id','bqi.id as buyer_quote_item_id','cf.city_name as fromcity',
				'ct.city_name as tocity', 'lt.load_type','vt.vehicle_type','bqi.created_by',
				'us.username','bq.buyer_id','us.username','tbbd.bid_end_date','tbbd.bid_end_time'
			);
		}else if((Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC)) {
			$Query_buyers_for_sellers->select ('bq.*','bq.lkp_quote_access_id','bqi.id as buyer_quote_item_id','cf.postoffice_name as fromcity',
				'ct.postoffice_name as tocity', 'lt.load_type','vt.vehicle_type','bqi.created_by',
				'us.username','bq.buyer_id','us.username','tbbd.bid_end_date','tbbd.bid_end_time','bqi.lkp_packaging_type_id'
			);
		}elseif(Session::get ( 'service_id' )==AIR_INTERNATIONAL) {
			$Query_buyers_for_sellers->select ('bq.*','bq.lkp_quote_access_id','bqi.id as buyer_quote_item_id','cf.airport_name as fromcity',
				'ct.airport_name as tocity', 'lt.load_type','vt.vehicle_type','bqi.created_by',
				'us.username','bq.buyer_id','us.username','tbbd.bid_end_date','tbbd.bid_end_time','bqi.lkp_packaging_type_id'
			);
		}elseif(Session::get ( 'service_id' )==OCEAN) {
			$Query_buyers_for_sellers->select ('bq.*','bq.lkp_quote_access_id','bqi.id as buyer_quote_item_id','cf.seaport_name as fromcity',
				'ct.seaport_name as tocity', 'lt.load_type','vt.vehicle_type','bqi.created_by',
				'us.username','bq.buyer_id','us.username','tbbd.bid_end_date','tbbd.bid_end_time','bqi.lkp_packaging_type_id'
			);
		}else if($serviceId == RELOCATION_DOMESTIC){
			$Query_buyers_for_sellers->select ('bq.*','bq.lkp_quote_access_id','bqi.id as buyer_quote_item_id','cf.city_name as fromcity',
				'ct.city_name as tocity', 'lt.load_type','vt.vehicle_type','bqi.created_by',
				'us.username','bq.buyer_id','us.username','tbbd.bid_end_date','tbbd.bid_end_time'
			);
		}else if((Session::get ( 'service_id' )==COURIER)) {
			$Query_buyers_for_sellers->select ('bq.*','bq.lkp_quote_access_id','bqi.id as buyer_quote_item_id','lppf.pincode as fromcity',
				'lppt.pincode as tocity', 'lt.load_type','vt.vehicle_type','bqi.created_by',
				'us.username','bq.buyer_id','us.username','tbbd.bid_end_date','tbbd.bid_end_time','bqi.lkp_packaging_type_id'
			);
		}else if($serviceId == RELOCATION_INTERNATIONAL){
			$Query_buyers_for_sellers->select ('bq.*','bq.lkp_quote_access_id','bqi.id as buyer_quote_item_id','cf.city_name as fromcity',
				'ct.city_name as tocity', 'lt.load_type','vt.vehicle_type','bqi.created_by',
				'us.username','bq.buyer_id','us.username','tbbd.bid_end_date','tbbd.bid_end_time','bqi.number_loads','bqi.avg_kg_per_move'
			);
		}else if($serviceId == RELOCATION_GLOBAL_MOBILITY){
			$Query_buyers_for_sellers->select ('bq.*','bq.lkp_quote_access_id','bqi.id as buyer_quote_item_id','cf.city_name as fromcity',
				'bqi.created_by','rlgms.service_type',
				'us.username','bq.buyer_id','us.username','tbbd.bid_end_date','tbbd.bid_end_time'
			);
		}


		//echo $Query_buyers_for_sellers->tosql ();
		//echo "<pre>";print_R($Query_buyers_for_sellers->getBindings());
		$Query_buyers_for_sellers->get ();

		//echo "<pre>";print_R($result);die;
		return $Query_buyers_for_sellers;
	}
	

	//get pincode using zone ID
	public static function getPincodesByZoneId($zoneId){
		$zone_location_ids = DB::table('ptl_pincodexsectors as ppxs')
									 ->join('ptl_sectors as s1','ppxs.ptl_sector_id','=','s1.id')
									 //->join('ptl_zones as z','s1.ptl_zone_id','=','z.id')
									->join('lkp_ptl_pincodes as lpp','lpp.id','=','ppxs.ptl_pincode_id')
									 //->where('z2.seller_id',Auth::User()->id)
									 ->where('s1.ptl_zone_id',$zoneId)
									 ->select('lpp.id')
									 ->distinct('lpp.id');
		//echo $zone_location_ids->tosql()."<br/>";
		$zone_location_ids	=	$zone_location_ids->lists('lpp.id');
		//echo "<pre>";print_R($zone_location_ids);die;
		return $zone_location_ids;
	}
}
