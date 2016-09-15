<?php
namespace App\Components\Term;

use App\Components\CommonComponent;
use App\Components\MessagesComponent;
use App\Components\Search\TermSellerSearchComponent;
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
use App\Components\SellerComponent;
use App\Components\BuyerComponent;
use App\Components\Matching\SellerMatchingComponent;
use App\Components\Matching\BuyerMatchingComponent;

use App\Components\Term\TermBuyerComponent;

class TermSellerComponent {
/* Term Seller post lists */
	public static function  getTermSellerPostlists($serviceId, $statusId =''){

		$loginId = Auth::User()->id;
		//echo "hi i m hrer";exit;
		// Filters values to populate in the page
		$from_locations = array (
				"" => "From Location"
		);
		$to_locations = array (
				"" => "To Location"
		);
		$ratecard_types = array (
				"" => "Post For"
		);
		$ptlCourierTypes = array ("" => "Courier Type");
		$from_date = '';
		$to_date = '';
		
		// query to retrieve buyer posts list and bind it to the grid 
		$Query = DB::table ( 'term_buyer_quotes as bqi' );
		$Query->leftjoin ( 'term_buyer_quote_items as bqit', 'bqi.id', '=', 'bqit.term_buyer_quote_id' );
		$Query->leftjoin ( 'term_buyer_bid_dates as tbqd', 'bqi.id', '=', 'tbqd.term_buyer_quote_id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'bqi.lkp_post_status_id' );
		$Query->leftjoin ( 'lkp_load_types as lt', 'lt.id', '=', 'bqit.lkp_load_type_id' );
		$Query->leftjoin ( 'lkp_vehicle_types as lv', 'lv.id', '=', 'bqit.lkp_vehicle_type_id' );
		if($serviceId == ROAD_FTL){
			$Query->leftjoin ( 'lkp_cities as cf', 'bqit.from_location_id', '=', 'cf.id' );
			$Query->leftjoin ( 'lkp_cities as ct', 'bqit.to_location_id', '=', 'ct.id' );
		}elseif($serviceId == RELOCATION_DOMESTIC){
			$Query->leftjoin ( 'lkp_cities as cf', 'bqit.from_location_id', '=', 'cf.id' );
			$Query->leftjoin ( 'lkp_cities as ct', 'bqit.to_location_id', '=', 'ct.id' );
			$Query->join ( 'lkp_post_ratecard_types as prct', 'bqi.lkp_post_ratecard_type', '=', 'prct.id' );
		}elseif((Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC)) {
			$Query->leftjoin('lkp_ptl_pincodes as cf', 'bqit.from_location_id', '=', 'cf.id');
			$Query->leftjoin('lkp_ptl_pincodes as ct', 'bqit.from_location_id', '=', 'cf.id');
		}elseif(Session::get ( 'service_id' )==AIR_INTERNATIONAL) {
			$Query->leftjoin('lkp_airports as cf', 'bqit.from_location_id', '=', 'cf.id');
			$Query->leftjoin('lkp_airports as ct', 'bqit.from_location_id', '=', 'cf.id');
		}elseif(Session::get ( 'service_id' )==OCEAN) {
			$Query->leftjoin('lkp_seaports as cf', 'bqit.from_location_id', '=', 'cf.id');
			$Query->leftjoin('lkp_seaports as ct', 'bqit.from_location_id', '=', 'cf.id');
		}elseif((Session::get ( 'service_id' )==COURIER)) {
			$Query->join('lkp_ptl_pincodes as cf', 'bqit.from_location_id', '=', 'cf.id');
			$Query->leftjoin('lkp_ptl_pincodes as ct', function($join)
			{
				$join->on('bqit.to_location_id', '=', 'ct.id');
				$join->on(DB::raw('bqi.lkp_courier_delivery_type_id'),'=',DB::raw(1));
					
			});
			$Query->leftjoin('lkp_countries as ct1', function($join)
			{
				$join->on('bqit.to_location_id', '=', 'ct1.id');
				$join->on(DB::raw('bqi.lkp_courier_delivery_type_id'),'=',DB::raw(2));
					
			});
			
			
		}elseif($serviceId == RELOCATION_INTERNATIONAL){
			$Query->leftjoin ( 'lkp_cities as cf', 'bqit.from_location_id', '=', 'cf.id' );
			$Query->leftjoin ( 'lkp_cities as ct', 'bqit.to_location_id', '=', 'ct.id' );
			$Query->join ( 'lkp_international_types as lict', 'bqi.lkp_lead_type_id', '=', 'lict.id' );
		}
		elseif($serviceId == RELOCATION_GLOBAL_MOBILITY){
			$Query->leftjoin ( 'lkp_cities as cf', 'bqit.from_location_id', '=', 'cf.id' );
		}

		$Query->join ( 'users as u', 'bqi.created_by', '=', 'u.id' );
		$Query->leftjoin ( 'term_buyer_quote_selected_sellers as bqss', 'bqss.term_buyer_quote_id', '=', 'bqi.id' );

		if( isset($_REQUEST['search']) && $_REQUEST['from_date']!=''){
			$from=CommonComponent::convertDateForDatabase($_REQUEST['from_date']);
			$Query->whereRaw('bqi.from_date >= "'.$from.'"');
		}
		
		if( isset($_REQUEST['search']) && $_REQUEST['to_date']!=''){
			$to=CommonComponent::convertDateForDatabase($_REQUEST['to_date']);
			if($_REQUEST['from_date']!=''){
				$Query->whereBetween('bqi.to_date',array($from,$to));
			}else{
				$Query->where('bqi.to_date', $to);
			}
		}
		
		
		if(Session::get ( 'service_id' )  == COURIER){
			$destinationtype = Session::get('destinationtype');
			if(isset($destinationtype) && $destinationtype != ''){
			$Query->where('bqi.lkp_courier_delivery_type_id', '=', Session::get('destinationtype'));
			}
		}
		if($serviceId == RELOCATION_INTERNATIONAL){
			if(isset($_REQUEST['int_type']) && $_REQUEST['int_type']==2){
			$Query->where('bqi.lkp_lead_type_id', '=', 2);
			}else{
			$Query->where('bqi.lkp_lead_type_id', '=', 1);
			}
		}
		//service id
		if(isset($serviceId) && $serviceId != ''){
			$Query->where('bqi.lkp_service_id', $serviceId);
		}
		if(isset($_REQUEST['status']) && $_REQUEST['status']!='' && $_REQUEST['status']!=0){
			$Query->where('bqi.lkp_post_status_id',$_REQUEST['status']);
		}else{
			$Query->whereNotIn('bqi.lkp_post_status_id',array(1,6,7,8));
		}
		
		$privatequery = clone $Query;
		$privatequery->whereRaw ( "( IF(`bqi`.`lkp_quote_access_id` = 2, `bqss`.`seller_id` =  $loginId,TRUE )  )");
		$privatequery->where("bqi.lkp_quote_access_id","=",2 );
		$privatequery->groupBy("bqi.id");
		$privateResults = $privatequery->select ( 'bqi.id');
		$privateResults = $privatequery->lists ('bqi.id');
		$Query->whereNotIn('bqi.lkp_post_status_id',array(1,6,7,8));
		$Query->whereRaw ( "( IF(`bqi`.`lkp_quote_access_id` = 2, `bqss`.`seller_id` =  $loginId,TRUE )  )");

		$sellerDists = TermSellerComponent::getSellerDistricts($loginId,$serviceId);
		$sellerDists =implode(",",$sellerDists);
		$privateResults =implode(",",$privateResults);
		
		if(($serviceId == ROAD_FTL) || (Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) 
				|| (Session::get ( 'service_id' )==AIR_DOMESTIC) || ($serviceId == RELOCATION_DOMESTIC) || ($serviceId == RELOCATION_INTERNATIONAL) || ($serviceId == RELOCATION_GLOBAL_MOBILITY)) {
			if ($sellerDists!="") {
				if ($privateResults!='') {
					$Query->whereRaw( "(`cf`.`lkp_district_id` in ($sellerDists) or `bqi`.`id` in ($privateResults))");
					
				} else {
					$Query->whereRaw( "(`cf`.`lkp_district_id` in ($sellerDists))");
					
				}
			} else {
				if ($privateResults!='') {
					$Query->whereRaw( "(`cf`.`lkp_district_id`=0 or `bqi`.`id` in ($privateResults))");
					
				} else {
					$Query->where('cf.lkp_district_id', 0);
				}
			}
		}else if(($serviceId == AIR_INTERNATIONAL) || (Session::get ( 'service_id' )==OCEAN)){
			
			
			if ($sellerDists!="") {
				if ($privateResults!='') {
					$Query->whereRaw( "(`bqit`.`from_location_id` in ($sellerDists) or `bqi`.`id` in ($privateResults))");

				} else {
					$Query->whereRaw( "(`bqit`.`from_location_id` in ($sellerDists))");

				}

			}else if ($privateResults!="") {
				$Query->whereRaw( "(`bqi`.`id` in ($privateResults))");
			}
		}
		if (isset ( $post_status ) && $post_status != '') {
			$Query->where ( 'bqi.lkp_post_status_id', '=', $post_status );
		}
		
		if (isset ( $_GET ['dispatch_date'] ) && $_GET ['dispatch_date'] != '') {
			$commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['dispatch_date']);
			$Query->where ( 'bqi.from_date', '>=', $commonDispatchDate );
			$from_date = $commonDispatchDate;
			
		}
		if (isset ( $_GET ['delivery_date'] ) && $_GET ['delivery_date'] != '') {
			$commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['delivery_date']);
			$Query->where ( 'bqi.from_date', '<=', $commonDeliveryhDate);
			$to_date = $commonDeliveryhDate;
		}
		
		//Checking private sellers,
		$Query->whereRaw ( "( IF(`bqi`.`lkp_quote_access_id` = 2, `bqss`.`seller_id` =  $loginId,TRUE )  )");
		$Query->groupBy("bqi.id");
		if($serviceId == ROAD_FTL){
			$postResults = $Query->select ( 'bqi.*','bqit.lkp_load_type_id', 'bqit.term_buyer_quote_id',
					'bqit.from_location_id','bqit.to_location_id','bqit.lkp_vehicle_type_id','bqit.units','bqit.number_loads',
					'bqit.quantity','bqit.quantity','bqit.lkp_post_status_id','ps.post_status', 'ct.city_name as toCity', 'cf.city_name as fromCity','u.username','tbqd.bid_end_date','bqi.buyer_id')->get ();
		}elseif($serviceId == RELOCATION_DOMESTIC){
			$postResults = $Query->select ( 'bqi.*','bqit.lkp_load_type_id', 'bqit.term_buyer_quote_id',
				'bqit.from_location_id','bqit.to_location_id','bqit.lkp_vehicle_type_id','bqit.units','bqit.number_loads',
				'bqit.quantity','bqit.quantity','bqit.lkp_post_status_id','ps.post_status', 'ct.city_name as toCity', 'cf.city_name as fromCity','u.username','tbqd.bid_end_date','bqi.buyer_id','prct.ratecard_type as rateCatdType')->get ();
		}elseif(($serviceId==ROAD_PTL) || ($serviceId==RAIL) || ($serviceId==AIR_DOMESTIC)){
			$postResults = $Query->select ( 'bqi.*','bqit.lkp_load_type_id', 'bqit.term_buyer_quote_id',
				'bqit.from_location_id','bqit.to_location_id','bqit.lkp_vehicle_type_id','bqit.units','bqit.number_loads',
				'bqit.quantity','bqit.quantity','bqit.lkp_post_status_id','ps.post_status', 'ct.postoffice_name as toCity', 'cf.postoffice_name as fromCity','u.username','tbqd.bid_end_date','bqi.buyer_id')->get ();
		}elseif(($serviceId==COURIER)){
			$postResults = $Query->select ( 'bqi.*','bqit.lkp_load_type_id', 'bqit.term_buyer_quote_id',
				'bqit.from_location_id','bqit.to_location_id','bqit.lkp_vehicle_type_id','bqit.units','bqit.number_loads',
				'bqit.quantity','bqit.quantity','bqit.lkp_post_status_id','ps.post_status', 'ct.postoffice_name as toCity', 'cf.postoffice_name as fromCity','u.username','tbqd.bid_end_date','bqi.buyer_id')->get ();
		}elseif($serviceId==AIR_INTERNATIONAL) {
			$postResults = $Query->select ( 'bqi.*','bqit.lkp_load_type_id', 'bqit.term_buyer_quote_id',
				'bqit.from_location_id','bqit.to_location_id','bqit.lkp_vehicle_type_id','bqit.units','bqit.number_loads',
				'bqit.quantity','bqit.quantity','bqit.lkp_post_status_id','ps.post_status', 'ct.airport_name as toCity', 'cf.airport_name as fromCity','u.username','tbqd.bid_end_date','bqi.buyer_id')->get ();
		}elseif($serviceId==OCEAN) {
			$postResults = $Query->select ( 'bqi.*','bqit.lkp_load_type_id', 'bqit.term_buyer_quote_id',
				'bqit.from_location_id','bqit.to_location_id','bqit.lkp_vehicle_type_id','bqit.units','bqit.number_loads',
				'bqit.quantity','bqit.quantity','bqit.lkp_post_status_id','ps.post_status', 'ct.seaport_name as toCity', 'cf.seaport_name as fromCity','u.username','tbqd.bid_end_date','bqi.buyer_id')->get ();
		}elseif($serviceId == RELOCATION_INTERNATIONAL){
			$postResults = $Query->select ( 'bqi.*','bqit.lkp_load_type_id', 'bqit.term_buyer_quote_id',
				'bqit.from_location_id','bqit.to_location_id','bqit.lkp_vehicle_type_id','bqit.units','bqit.number_loads',
				'bqit.quantity','bqit.lkp_post_status_id','ps.post_status', 'ct.city_name as toCity', 'cf.city_name as fromCity','u.username','tbqd.bid_end_date','bqi.buyer_id','lict.international_type')->get ();
		}elseif($serviceId == RELOCATION_GLOBAL_MOBILITY){
			$postResults = $Query->select ( 'bqi.*','bqit.lkp_load_type_id', 'bqit.term_buyer_quote_id',
				'bqit.from_location_id','bqit.to_location_id','bqit.lkp_vehicle_type_id','bqit.units','bqit.number_loads',
				'bqit.quantity','bqit.lkp_post_status_id','ps.post_status', 'cf.city_name as fromCity','u.username','tbqd.bid_end_date','bqi.buyer_id','bqit.lkp_gm_service_id')->get ();
		}


		// Functionality to handle filters based on the selection starts
		foreach ( $postResults as $post ) {
			$buyer_quotes = DB::table ( 'term_buyer_quote_items' )
			->leftjoin ( 'term_buyer_quotes', 'term_buyer_quotes.id', '=', 'term_buyer_quote_items.term_buyer_quote_id' )
			->where ( 'term_buyer_quote_id', $post->id )->select ( 'term_buyer_quote_items.*','term_buyer_quotes.*')->get ();
			if($serviceId == ROAD_FTL || $serviceId == RELOCATION_DOMESTIC  || $serviceId == RELOCATION_INTERNATIONAL || $serviceId == RELOCATION_GLOBAL_MOBILITY){
			foreach ( $buyer_quotes as $quotes ) {
				if (! isset ( $from_locations [$quotes->from_location_id] )) {
					$from_locations [$quotes->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $quotes->from_location_id )->pluck ( 'city_name' );
				}
				if (! isset ( $to_locations [$quotes->to_location_id] )) {
					$to_locations [$quotes->to_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $quotes->to_location_id )->pluck ( 'city_name' );
				}
				
				}

			}
			if($serviceId == RELOCATION_DOMESTIC){
				if (! isset ( $ratecard_types [$post->lkp_post_ratecard_type] )) {
					$ratecard_types [$post->lkp_post_ratecard_type] = DB::table ( 'lkp_post_ratecard_types' )->where ( 'id', $post->lkp_post_ratecard_type )->pluck ( 'ratecard_type' );
				}
			}
			if(($serviceId==ROAD_PTL) || ($serviceId==RAIL) || ($serviceId==AIR_DOMESTIC)){
				foreach ( $buyer_quotes as $quotes ) {
					if (! isset ( $from_locations [$quotes->from_location_id] )) {
						$from_locations [$quotes->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $quotes->from_location_id )->pluck ( 'postoffice_name' );
					}
					if (! isset ( $to_locations [$quotes->to_location_id] )) {
						$to_locations [$quotes->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $quotes->to_location_id )->pluck ( 'postoffice_name' );
					}
			
			
			
				}
			}
			if(($serviceId==COURIER)){
				foreach ( $buyer_quotes as $quotes ) {
					if (! isset ( $from_locations [$quotes->from_location_id] )) {
						$from_locations [$quotes->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $quotes->from_location_id )->pluck ( 'postoffice_name' );
					}
					if (! isset ( $to_locations [$quotes->to_location_id] )) {
						$destinationtype = Session::get('destinationtype');
						if(isset($destinationtype) && $destinationtype == 1){
							$to_locations [$quotes->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $quotes->to_location_id )->pluck ( 'postoffice_name' );
						}else{
							$to_locations [$quotes->to_location_id] = DB::table ( 'lkp_countries' )->where ( 'id', $quotes->to_location_id )->pluck ( 'country_name' );
						}
						
					}
						
					if (!isset( $ptlCourierTypes [$quotes->lkp_courier_type_id] )) {
							$ptlCourierTypes [$quotes->lkp_courier_type_id] = DB::table ( 'lkp_courier_types' )->where ( 'id', $quotes->lkp_courier_type_id )->pluck ( 'courier_type' );
					}
					
						
				}
			}
			if($serviceId==AIR_INTERNATIONAL){
				foreach ( $buyer_quotes as $quotes ) {
					if (! isset ( $from_locations [$quotes->from_location_id] )) {
						$from_locations [$quotes->from_location_id] = DB::table ( 'lkp_airports' )->where ( 'id', $quotes->from_location_id )->pluck ( 'airport_name' );
					}
					if (! isset ( $to_locations [$quotes->to_location_id] )) {
						$to_locations [$quotes->to_location_id] = DB::table ( 'lkp_airports' )->where ( 'id', $quotes->to_location_id )->pluck ( 'airport_name' );
					}
						
						
						
				}
			}
			
			if($serviceId==OCEAN){
				foreach ( $buyer_quotes as $quotes ) {
					if (! isset ( $from_locations [$quotes->from_location_id] )) {
						$from_locations [$quotes->from_location_id] = DB::table ( 'lkp_seaports' )->where ( 'id', $quotes->from_location_id )->pluck ( 'seaport_name' );
					}
					if (! isset ( $to_locations [$quotes->to_location_id] )) {
						$to_locations [$quotes->to_location_id] = DB::table ( 'lkp_seaports' )->where ( 'id', $quotes->to_location_id )->pluck ( 'seaport_name' );
					}
			
			
			
				}
			}
			if($serviceId == RELOCATION_INTERNATIONAL){
				if (! isset ( $ratecard_types [$post->lkp_lead_type_id] )) {
					$ratecard_types [$post->lkp_lead_type_id] = DB::table ( 'lkp_international_types' )->where ( 'id', $post->lkp_lead_type_id )->pluck ( 'international_type' );
				}
			}


		}
		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations = CommonComponent::orderArray($to_locations);
		$ptlCourierTypes = CommonComponent::orderArray($ptlCourierTypes);
		/*echo $Query->tosql();
		echo "<pre>";
		echo "<pre>";print_R($Query->getBindings());
		print_R($Query->get());die;*/
		$grid = DataGrid::source ( $Query );

		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Buyer Name', 'username' )->style ( "display:none" )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'bid_end_date', 'Bid End Date', 'bid_end_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC){
			$grid->add ( 'rateCatdType', 'Post For', 'rateCatdType' )->attributes(array("class" => "col-md-1 padding-left-none"));
		}
		if(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){
			$grid->add ( 'international_type', '', false )->attributes(array("class" => "col-md-1 padding-left-none"));
		}
		$grid->add ( 'post_status', 'dummycolumn', 'post_status' )->style ( "display:none" );
		$grid->edit ( 'dummy', 'Status', 'post_status' )->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'lkp_quote_access_id', 'Buyer access_id', 'lkp_quote_access_id' )->style ( "display:none" );
		$grid->add ( 'buyer_quote_id', 'Buyer id', 'buyer_quote_id' )->style ( "display:none" );
		$grid->add ( 'lkp_post_status_id', 'Post status id', 'lkp_post_status_id' )->style ( "display:none" );
		$grid->add ( 'from_location_id', 'from city id', 'from_location_id' )->style ( "display:none" );
		$grid->add ( 'lkp_quote_price_type_id', 'Price Type', true )->style ( "display:none" );
		$grid->add ( 'buyer_id','buyer_id','buyer_id')->style ( "display:none" );
        $grid->add ( 'transaction_id', 'transaction_id', 'transaction_id' )->style ( "display:none" );
		$grid->orderBy ( 'bqi.id', 'desc' );
		$grid->paginate ( 5 );
		
		$grid->row ( function ($row) {
			$userId = Auth::user ()->id;
			$buyer_quote_id = $row->cells [0]->value;

			$row->cells [0]->style ( 'display:none' );
			$fromcity = $row->cells [1]->value;
			$dispatchDate = $row->cells [2]->value;
			$deliveryDate = $row->cells [3]->value;
			if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC || Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){
			$ratecardType= $row->cells [5]->value;
			}
         
			$row->cells [1]->value = $fromcity.'<div class="red">
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
											</div>';
			$row->cells [2]->value = CommonComponent::checkAndGetDate($dispatchDate);
			$row->cells [3]->value = CommonComponent::checkAndGetDate($deliveryDate);
			$bid_enddate_time_convert = explode(" ",CommonComponent::getBidDateTimeByQuoteId($buyer_quote_id,Session::get ( 'service_id' )));
			
			$bid_end_date_convert = CommonComponent::checkAndGetDate($bid_enddate_time_convert[0]);
			$row->cells [4]->value = $bid_end_date_convert." ".$bid_enddate_time_convert[1];
			if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC || Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){

			$row->cells [9]->style ( 'width:100%' );
				$buyer_access_id = $row->cells [9]->style ( 'display:none' );
				$buyer_id = $row->cells [10]->style ( 'display:none' );
				$buyerCountId = count (BuyerComponent::getBuyerQuoteSellersQuotesPricesFromId( $buyer_quote_id ) );
				$post_status_id = $row->cells [11]->style ( 'display:none' );
				
				$arraySellerIds = BuyerComponent::getSellerIds($row->cells[12]->style ( 'display:none' ));
				$arrayBuyerLeads = BuyerComponent::getLeadsForBuyer($row->cells[13]->style ( 'display:none' ), $arraySellerIds);
				$countBuyerLeads = count($arrayBuyerLeads);
				
				$priceType = $row->cells [12]->style ( 'display:none' );
					
				$user_id = $row->cells [13]->style ( 'display:none' );
				$transaction_id = $row->cells [14]->style ( 'display:none' );
			}else{
			$row->cells [7]->style ( 'width:100%' );
			$buyer_access_id = $row->cells [7]->style ( 'display:none' );
			$buyer_id = $row->cells [8]->style ( 'display:none' );
			$buyerCountId = count (BuyerComponent::getBuyerQuoteSellersQuotesPricesFromId( $buyer_quote_id ) );
			$post_status_id = $row->cells [9]->style ( 'display:none' );
		
			$arraySellerIds = BuyerComponent::getSellerIds($row->cells[10]->style ( 'display:none' ));
			$arrayBuyerLeads = BuyerComponent::getLeadsForBuyer($row->cells[10]->style ( 'display:none' ), $arraySellerIds);
			$countBuyerLeads = count($arrayBuyerLeads);
				
			$priceType = $row->cells [10]->style ( 'display:none' );
			
			$user_id = $row->cells [12]->style ( 'display:none' );
            $transaction_id = $row->cells [13]->style ( 'display:none' );
			}
			if ($priceType == '2') {
				$postQuoteType = 'Response';
			} else {
				$postQuoteType = 'Quotes';
			}
				
			if ($buyer_access_id == "2" && $post_status_id == "2")  {
				$buyer_id = "";
			} else {
				$buyer_id = " ";
			}
			
			$row->cells [1]->attributes(array("class" => "col-md-2 padding-left-none"));
			$row->cells [2]->attributes(array("class" => "col-md-2 padding-left-none"));
			$row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none"));
			$row->cells [4]->attributes(array("class" => "col-md-2 padding-left-none"));
			$row->cells [5]->attributes(array("class" => "col-md-1 padding-left-none"));
			if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC || Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){
			$row->cells [6]->attributes(array("class" => "col-md-1 padding-left-none"));
			
			}
			$getBidType = CommonComponent::getBidType($buyer_quote_id);
			$getSaveValue = CommonComponent::getIsSubmitData($buyer_quote_id);
			$getDateValue = CommonComponent::getBidDateTime($buyer_quote_id,Session::get ( 'service_id' ));
			date_default_timezone_set('Asia/Calcutta');
			$today = date("Y-m-d H:i:00");
			
			$bidDateTimes = CommonComponent::getBidDateTimeByQuoteId($buyer_quote_id,Session::get ( 'service_id' ));
			$buyer_items = CommonComponent::getItemsBuyer($buyer_quote_id);
			$buyer_items_count = count($buyer_items);
			if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC  || Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){
			 $row->cells [5]->value = $ratecardType;
			 $cellid=8;	
			}else{
			 $cellid=6;
			}
			$row->cells [$cellid]->value = "";
			$zippath = url().'/downloadbuyerbids/'.$buyer_quote_id;
			
			//check upload buyer document count.			
			$documentname = CommonComponent::getBuyerBidDocumentsCheckingCount($buyer_quote_id);
			
			if((Session::get ( 'service_id' )==ROAD_FTL) || (Session::get ( 'service_id' )==RELOCATION_DOMESTIC)){
				$packageorvehicle =  "Vehicle Type";
				$quantitylable = "Qty";
			}else if((Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC) || (Session::get ( 'service_id' )==AIR_INTERNATIONAL) || (Session::get ( 'service_id' )==OCEAN)) {
				$packageorvehicle =  "Packaging Type";
				$quantitylable = "No. Of Packages";
			}
			if((Session::get ( 'service_id' )==COURIER)){
				$isQuoteSubmittd = TermSellerComponent::checkQuoteSubmitted($buyer_items,Session::get ( 'service_id' ),Auth::user ()->id,$buyer_quote_id);
				$isQuoteSaved    = TermSellerComponent::checkQuoteSubmitted($buyer_items,Session::get ( 'service_id' ),Auth::user ()->id,$buyer_quote_id,true);
			}else{
			$isQuoteSubmittd = TermSellerComponent::checkQuoteSubmitted($buyer_items,Session::get ( 'service_id' ),Auth::user ()->id,$buyer_quote_id);
			$isQuoteSaved = TermSellerComponent::checkQuoteSubmitted($buyer_items,Session::get ( 'service_id' ),Auth::user ()->id,$buyer_quote_id,true);
			}

			$submitQuoteText = (($isQuoteSaved > 0) ? "Quote Saved" : (($isQuoteSubmittd == 0) ? "Submit Quote " : "Quote Submitted"));
				
			$row->cells [$cellid]->value .= '</a>';
			
			if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC  || Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){
			if($today < $bidDateTimes){
			$row->cells [$cellid]->value .="<div class='col-md-2 padding-none text-right'><span id ='$buyer_quote_id' class='btn detailsslide-term red-btn'>$submitQuoteText <i class='fa fa-rupee'></i></span></div>

			<div class='clearfix'></div>";
		    }else{

		    $row->cells [$cellid]->value .="<div class='col-md-2 padding-none text-right'><span id ='$buyer_quote_id' class='btn detailsslide-term red-btn'>Bid Elapsed <i class='fa fa-rupee'></i></span></div>
		    	
		    <div class='clearfix'></div>";
		    }
			}else{
				if($today < $bidDateTimes){
					$row->cells [$cellid]->value .="<div class='col-md-3 padding-none text-right'><span id ='$buyer_quote_id' class='btn detailsslide-term red-btn'>$submitQuoteText <i class='fa fa-rupee'></i></span></div>
				
					<div class='clearfix'></div>";
				}else{
				
					$row->cells [$cellid]->value .="<div class='col-md-3 padding-none text-right'><span id ='$buyer_quote_id' class='btn detailsslide-term red-btn'>Bid Elapsed <i class='fa fa-rupee'></i></span></div>
					 
					<div class='clearfix'></div>";
				}	
			}
			$row->cells [$cellid]->value .="<div class='pull-left'>
						<div class='info-links'>
								<a href='#'>
									<i class='fa fa-envelope-o'></i> Messages <span class='badge'></span>
								</a>
								
							</div>
					</div>

			<div class='pull-right text-right'>
				<div class='info-links'>
				<a class='detailsslide-term' id ='$buyer_quote_id'><span class='show_details'  style='display: inline;'>+</span><span class='hide_details' style='display: none;'>-</span> Details</a>
				<a href='#' data-userid='".$user_id."' data-buyer-transaction='".$transaction_id."' class='new_message' data-term='1' data-buyerquoteitemidforseller='".$buyer_quote_id."'><i class='fa fa-envelope-o'></i></a>
				</div>	
			</div>
				
			
			<div class='col-md-12 padding-none'>

			<div class='col-md-12 col-sm-12 col-xs-12 padding-none pull-right submit-data-div term_quote_details_".$buyer_quote_id."' style='display: none;'>";
			
			
			if(Session::get ( 'service_id' )==COURIER){
				$row->cells [$cellid]->value .="<form name ='intialquotebidding_$buyer_quote_id' id ='intialquotebidding_$buyer_quote_id' action ='' class='couriertermintialquotesubmit_form' >";
			}else{
				$row->cells [$cellid]->value .="<form name ='intialquotebidding_$buyer_quote_id' id ='intialquotebidding_$buyer_quote_id' action ='' class='termintialquotesubmit_form' >";
			}
			
			
			$row->cells [$cellid]->value .="<input type='hidden' name ='buyer_items_count' id='buyer_items_count' value='0'>
						<input type='hidden' name ='buyer_item_id' id='buyer_item_id' value='$buyer_quote_id'>	
						<input type='hidden' name ='buyer_line_item_id' id='buyer_line_item_id' value=''>";
		if(Session::get ( 'service_id' )!=RELOCATION_DOMESTIC  && Session::get ( 'service_id' )!=RELOCATION_INTERNATIONAL && Session::get ( 'service_id' )!=RELOCATION_GLOBAL_MOBILITY){		
							$row->cells [$cellid]->value .="<div class='pull-left margin-top'>
								<span class='data-head'>Bid Type : $getBidType</span>
							</div>";
					}		
		if ($documentname!="") {			
			$row->cells [$cellid]->value .= "<div class='pull-right text-right margin-top'><a id ='download_$buyer_quote_id' href='$zippath' class='detailsslide-term data-head red'>Download Bid Documents</a></div>";
		} else {
			$row->cells [$cellid]->value .= "<div class='pull-right text-right margin-top margin-bottom'></div>";
		}
			
		if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC){
			
			if($ratecardType=='HHG'){
				$row->cells [$cellid]->value .= "<div class='clearfix'></div><br>
					
				<div class='table-heading inner-block-bg'>
				<div class='col-md-2 padding-left-none'>From</div>
				<div class='col-md-2 padding-left-none'>To</div>
				<div class='col-md-2 padding-left-none'>Avg Volume/Shipment</div>
				<div class='col-md-2 padding-left-none'>No of Shipments</div>
				<div class='col-md-2 padding-left-none'>Rate per CFT</div>
				<div class='col-md-2 padding-left-none'>Transit Days</div>";
			}else{

				$row->cells [$cellid]->value .= "<div class='clearfix'></div><br>
				<div class='table-heading inner-block-bg'>
				<div class='col-md-2 padding-left-none'>From</div>
				<div class='col-md-2 padding-left-none'>To</div>
				<div class='col-md-2 padding-left-none'>Vehicle Category</div>
				<div class='col-md-1 padding-left-none'>Vehicle Category Type</div>
				<div class='col-md-1 padding-left-none'>Vehicle Model</div>
				<div class='col-md-2 padding-left-none'>Transport Charges</div>
				<div class='col-md-1 padding-left-none'>O&D Charges</div>
				<div class='col-md-1 padding-left-none'>Transit Days</div>";
			}
			
				
		}elseif(Session::get ( 'service_id' )== RELOCATION_INTERNATIONAL){
			
			if($ratecardType=="Ocean"){
				$row->cells [$cellid]->value .= "<div class='clearfix'></div><br>
				<div class='table-heading inner-block-bg'>
				<div class='col-md-3 padding-left-none '>From</div>
				<div class='col-md-3 padding-left-none '>To</div>
				<div class='col-md-3 padding-left-none'>No of Moves</div>
				<div class='col-md-3 padding-left-none'>Average CBM/Move</div>
				</div>";
			if(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL ) {
					for($i=0;$i<count($buyer_items);$i++){
						$getbuyerquoteitemrelocation = DB::table('term_buyer_quote_items')
						->where('term_buyer_quote_items.id','=',$buyer_items[$i])
						->select('from_location_id','to_location_id','number_loads','avg_kg_per_move')
						->first();
						$from_city_name = CommonComponent::getCityName($getbuyerquoteitemrelocation->from_location_id);
						$to_city_name = CommonComponent::getCityName($getbuyerquoteitemrelocation->to_location_id);
						$row->cells [$cellid]->value .='<div class="table-row inner-block-bg">
					<div class="col-md-3 padding-left-none">'.$from_city_name.'</div>
					<div class="col-md-3 padding-left-none">'.$to_city_name.'</div>
					<div class="col-md-3 padding-left-none">'.$getbuyerquoteitemrelocation->number_loads.'</div>
					<div class="col-md-3 padding-left-none">'.$getbuyerquoteitemrelocation->avg_kg_per_move.'</div>
					</div>';
							
					}
				}
			$row->cells [$cellid]->value .= "
				<div class='table-heading inner-block-bg'>
				<div class='col-md-3 padding-left-none'>From</div>
                 <div class='col-md-2 padding-left-none'>To</div>
                 <div class='col-md-1 padding-left-none'>O & D LCL(per CBM)</div>
                 <div class='col-md-1 padding-left-none'>O & D 20 FT (per CBM)</div>
				 <div class='col-md-1 padding-left-none'>O & D 40 FT (per CBM)</div>
                 <div class='col-md-1 padding-left-none'>Freight LCL (per CBM)</div>
                 <div class='col-md-1 padding-left-none'>Freight FCL 20 FT (Flat)</div>
                 <div class='col-md-1 padding-left-none'>Freight FCL 40 FT (Flat)</div>
                 <div class='col-md-1 padding-left-none'>Transit Days</div>";
			}else{
				$row->cells [$cellid]->value .= "<div class='clearfix'></div><br>
				<div class='table-heading inner-block-bg'>
				<div class='col-md-3 padding-left-none'>From</div>
				<div class='col-md-3 padding-left-none'>To</div>
				<div class='col-md-3 padding-left-none'>No of Moves</div>
				<div class='col-md-3 padding-left-none'>Average KG/Move</div>
				</div>";
				if(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL ) {
					for($i=0;$i<count($buyer_items);$i++){
						$getbuyerquoteitemrelocation = DB::table('term_buyer_quote_items')
						->where('term_buyer_quote_items.id','=',$buyer_items[$i])
						->select('from_location_id','to_location_id','number_loads','avg_kg_per_move')
						->first();
						$from_city_name = CommonComponent::getCityName($getbuyerquoteitemrelocation->from_location_id);
						$to_city_name = CommonComponent::getCityName($getbuyerquoteitemrelocation->to_location_id);
						$row->cells [$cellid]->value .='<div class="table-row inner-block-bg">
					<div class="col-md-3 padding-left-none">'.$from_city_name.'</div>
					<div class="col-md-3 padding-left-none">'.$to_city_name.'</div>
					<div class="col-md-3 padding-left-none">'.$getbuyerquoteitemrelocation->number_loads.'</div>
					<div class="col-md-3 padding-left-none">'.$getbuyerquoteitemrelocation->avg_kg_per_move.'</div>
					</div>';
							
					}
				}
				$row->cells [$cellid]->value .= "			
				<div class='table-heading inner-block-bg'>
				<div class='col-md-2 padding-left-none'>From</div>
                 <div class='col-md-2 padding-left-none'>To</div>
                 <div class='col-md-2 padding-left-none'>Freight Charges Upto<br />100 KG</div>
                 <div class='col-md-2 padding-left-none'>Freight Charges Upto<br />300 KG</div>
				 <div class='col-md-2 padding-left-none'>Freight Charges Upto<br />500 KG</div>
                 <div class='col-md-1 padding-left-none'>O&D Charges<br />(per CFT)</div>
                 <div class='col-md-1 padding-left-none'>Transit Days</div>";
			}
			
		}elseif(Session::get ( 'service_id' )==RELOCATION_GLOBAL_MOBILITY){
			
			$row->cells [$cellid]->value .= "
				<div class='table-heading inner-block-bg'>
				<div class='col-md-3 padding-left-none'>From</div>
                 <div class='col-md-3 padding-left-none'>Service</div>
                 <div class='col-md-3 padding-left-none'>Numbers</div>
                 <div class='col-md-3 padding-left-none'>Rate</div>";
			
		}
		else{
		if(Session::get ( 'service_id' )==ROAD_FTL){	
		 $row->cells [$cellid]->value .= "<div class='clearfix'></div><br>

							<div class='table-heading inner-block-bg'>
							<div class='col-md-2 padding-left-none'>From</div>
							<div class='col-md-2 padding-left-none'>To</div>
							<div class='col-md-2 padding-left-none'>Load Type</div>
							<div class='col-md-2 padding-left-none'>$packageorvehicle</div>
							<div class='col-md-1 padding-left-none'>$quantitylable</div>";
		}
		if((Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC) || (Session::get ( 'service_id' )==AIR_INTERNATIONAL) || (Session::get ( 'service_id' )==OCEAN)){
		$row->cells [$cellid]->value .= "<div class='clearfix'></div><br>
			
			<div class='table-heading inner-block-bg'>
			<div class='col-md-2 padding-left-none'>From</div>
			<div class='col-md-2 padding-left-none'>To</div>
			<div class='col-md-2 padding-left-none'>Load Type</div>
			<div class='col-md-1 padding-left-none'>$packageorvehicle</div>
			<div class='col-md-1 padding-left-none'>$quantitylable</div>";
		}
		if((Session::get ( 'service_id' )==COURIER)){
			$row->cells [$cellid]->value .= "<div class='clearfix'></div><br>
				
			<div class='table-heading inner-block-bg'>
			<div class='col-md-2 padding-left-none'>From</div>
			<div class='col-md-2 padding-left-none'>To</div>
			<div class='col-md-2 padding-left-none'>Volume</div>
			<div class='col-md-1 padding-left-none'>No of Packages</div>";
		}
		}	
				
		if($getBidType == 'Open') {
		if(Session::get ( 'service_id' )==ROAD_FTL) {	
					$row->cells [$cellid]->value .="<div class='col-md-2 padding-left-none'>Lowest Quote</div>";
					}elseif(Session::get ( 'service_id' )!=COURIER){
						$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'>Lowest Rate/Kg</div>
												  <div class='col-md-1 padding-left-none'>Lowest Kg/CFT</div>";
					}
					}
					if(Session::get ( 'service_id' )==ROAD_FTL) {
						$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'>Quote</div>";
					}elseif((Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_INTERNATIONAL) || (Session::get ( 'service_id' )==OCEAN)) {
						$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'>Rate/Kg</div>
												  <div class='col-md-1 padding-left-none'>Kg/CFT</div>";
					}

					$row->cells [$cellid]->value .="
 </div>";
								
			if(Session::get ( 'service_id' )==ROAD_FTL || Session::get ( 'service_id' )==RELOCATION_DOMESTIC 	|| Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL ) {
				for($i=0;$i<count($buyer_items);$i++){
					
					$getbuyerquoteitems = DB::table('term_buyer_quote_items')
						->where('term_buyer_quote_items.id','=',$buyer_items[$i])
						->select('from_location_id','to_location_id','lkp_load_type_id','lkp_vehicle_type_id','quantity','volume','number_packages','lkp_vehicle_category_id','lkp_vehicle_category_type_id','vehicle_model')
						->first();
					
					if(Session::get ( 'service_id' )==ROAD_FTL){
					$initialQuotePriceDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
						->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_item_id','=',$buyer_items[$i])
						->where('term_buyer_quote_sellers_quotes_prices.seller_id','=',$userId)
						->select('initial_quote_price','is_submitted')
						->first();
					}else{
					$initialQuotePriceDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
						->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_item_id','=',$buyer_items[$i])
						->where('term_buyer_quote_sellers_quotes_prices.seller_id','=',$userId)
						->select('rate_per_cft','transport_charges','odcharges','transit_days','crating_charges','storage_charges','escort_charges','handyman_charges','property_charges','brokerage_charge','is_submitted',
								'fright_hundred','fright_three_hundred','fright_five_hundred','odlcl_charges','odtwentyft_charges','odfortyft_charges','frieghtlcl_charges','frieghttwentft_charges','frieghtfortyft_charges')
						->first();
					}
					if(!empty($initialQuotePriceDisplay)){
						if(Session::get ( 'service_id' )==ROAD_FTL){
						$initialQuotePrice = $initialQuotePriceDisplay->initial_quote_price;
						}else{
						$initialQuotePrice = $initialQuotePriceDisplay->rate_per_cft;
						$initialTransitDays = $initialQuotePriceDisplay->transit_days;
						$initialtransport = $initialQuotePriceDisplay->transport_charges;
						$initialod = $initialQuotePriceDisplay->odcharges;
						$crating = $initialQuotePriceDisplay->crating_charges;
						$storage = $initialQuotePriceDisplay->storage_charges;
						$escort =  $initialQuotePriceDisplay->escort_charges;
						$handyman = $initialQuotePriceDisplay->handyman_charges;
						$property = $initialQuotePriceDisplay->property_charges;
						$brokerage = $initialQuotePriceDisplay->brokerage_charge;
						$frighthndered = $initialQuotePriceDisplay->fright_hundred;
						$frightthreehundred = $initialQuotePriceDisplay->fright_three_hundred;
						$frightfivehundred = $initialQuotePriceDisplay->fright_five_hundred;
						$odlclcharges = $initialQuotePriceDisplay->odlcl_charges;
						$odlcltwentycharges = $initialQuotePriceDisplay->odtwentyft_charges;
						$odlclfortycharges = $initialQuotePriceDisplay->odfortyft_charges;
						$frieghtlclcharges = $initialQuotePriceDisplay->frieghtlcl_charges;
						$frieghttwentylclcharges = $initialQuotePriceDisplay->frieghttwentft_charges;
						$frieghtfortylclcharges = $initialQuotePriceDisplay->frieghtfortyft_charges;
						
						}
					}else{
						$initialtransport="";
						$initialod="";
						$initialQuotePrice="";
						$initialTransitDays="";
						$crating = '';
						$storage = '';
						$escort =  '';
						$handyman = '';
						$property = '';
						$brokerage = '';
						$frighthndered = '';
						$frightthreehundred = '';
						$frightfivehundred = '';
						$odlclcharges = '';
						$odlcltwentycharges = '';
						$odlclfortycharges = '';
						$frieghtlclcharges = '';
						$frieghttwentylclcharges = '';
						$frieghtfortylclcharges = '';
					}
					$load_type_name = CommonComponent::getLoadType($getbuyerquoteitems->lkp_load_type_id);
					$vehicle_type_name = CommonComponent::getVehicleType($getbuyerquoteitems->lkp_vehicle_type_id);
					$from_city_name = CommonComponent::getCityName($getbuyerquoteitems->from_location_id);
					$to_city_name = CommonComponent::getCityName($getbuyerquoteitems->to_location_id);
					$initial_quote_price_price = CommonComponent::getLowestQuote($buyer_items[$i]);
					$initial_rate_per_kg = CommonComponent::getLowestRatePerKg($buyer_items[$i]);
					$initial_rate_per_cft = CommonComponent::getLowestRatePercft($buyer_items[$i]);
					$initial_kg_per_cft = CommonComponent::getLowestKgPerCft($buyer_items[$i]);
					if(Session::get ( 'service_id' )!=RELOCATION_INTERNATIONAL){
						$row->cells [$cellid]->value .="<div class='table-row inner-block-bg margin-none-1'> <div class='col-md-2 padding-left-none'>";
								if($today < $bidDateTimes && $isQuoteSubmittd != 1){
								$row->cells [$cellid]->value .="<input type='checkbox' name='lineitem_checkbox' id='term_lineitem_$buyer_items[$i]_$buyer_quote_id' class='lineitem_checkbox' onchange='javascript:checkSellerPostitem(this.id)'> <span class='lbl padding-8'></span>";
								}
								$row->cells [$cellid]->value .=$from_city_name."
								<input type='hidden' name='quote_id' id='quote_id' value='$buyer_quote_id'>
								</div>
								<div class='col-md-2 padding-left-none'>$to_city_name</div>";
						if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC  || Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){
							if($ratecardType=='HHG'){
							$row->cells [$cellid]->value .="<div class='col-md-2 padding-left-none'>$getbuyerquoteitems->volume</div>
							<div class='col-md-2 padding-left-none'>$getbuyerquoteitems->number_packages</div>";
							}else{
								$vehiclecat=CommonComponent::getVehicleCategoryById($getbuyerquoteitems->lkp_vehicle_category_id);
								if($getbuyerquoteitems->lkp_vehicle_category_id==1){
								$vehiclecattype=CommonComponent::getVehicleCategorytypeById($getbuyerquoteitems->lkp_vehicle_category_id);
								}else{
								$vehiclecattype="N/A";
								}
							$row->cells [$cellid]->value .="<div class='col-md-2 padding-left-none'>".$vehiclecat."</div>
							<div class='col-md-1 padding-left-none'>".$vehiclecattype."</div>
							<div class='col-md-1 padding-left-none'>$getbuyerquoteitems->vehicle_model</div>";
							}
						}else{		
						$row->cells [$cellid]->value .="<div class='col-md-2 padding-left-none'>$load_type_name</div>
								<div class='col-md-2 padding-left-none'>$vehicle_type_name</div>
								<div class='col-md-1 padding-left-none'>$getbuyerquoteitems->quantity</div>";
						}
						if($getBidType == 'Open'){
							if(Session::get ( 'service_id' )==ROAD_FTL) {
							if(empty($initial_quote_price_price) && $initial_quote_price_price == ''){
								$row->cells [$cellid]->value .="<div class='col-md-2 padding-left-none'>--</div>";
							}else{
								$row->cells [$cellid]->value .="<div class='col-md-2 padding-left-none'>".CommonComponent::getPriceType($initial_quote_price_price)."</div>";
							}
							}else{
							if(empty($initial_rate_per_kg) && $initial_rate_per_kg == ''){
									$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'>-</div>";
									$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'>-</div>";
							}else{
									$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'>$initial_rate_per_kg</div>";
									$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'>$initial_kg_per_cft</div>";
							}	
								
							}
						}
						
						
						if(Session::get ( 'service_id' )==ROAD_FTL){
						$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'>";
						if($getSaveValue){
							$row->cells [$cellid]->value .="<input class='form-control form-control1 clsFTLTQuote' id ='intialquote_".$buyer_items[$i]."' value ='".$initialQuotePrice."' name = 'intialquote_".$buyer_items[$i]."' type='text' disabled>
													  <input type='hidden' name='item_id' id='item_id' value='$buyer_items[$i]'>";
						}else{
							$row->cells [6]->value .="<input class='form-control form-control1 clsFTLTQuote' id ='intialquote_".$buyer_items[$i]."' value ='".$initialQuotePrice."' name = 'intialquote_".$buyer_items[$i]."' type='text' disabled>
													  <input type='hidden' name='item_id' id='item_id' value='$buyer_items[$i]'>";
						}
						$row->cells [$cellid]->value .="</div></div>";
						}else{
						if($getSaveValue){
							if($ratecardType=='HHG'){
								$row->cells [$cellid]->value .="<div class='col-md-2 padding-left-none'><input class='form-control fourdigitstwodecimals_deciVal numberVal form-control1' id ='rateper_kg_".$buyer_items[$i]."' value ='".$initialQuotePrice."' name = 'rateper_kg_".$buyer_items[$i]."' type='text' disabled></div>
								<div class='col-md-2 padding-left-none'><input class='form-control numericvalidation form-control1' maxlength ='3' id ='transit_days_".$buyer_items[$i]."' value ='".$initialTransitDays."' name = 'transit_days_".$buyer_items[$i]."' type='text' disabled></div>
								<input type='hidden' name='item_id' id='item_id' value='$buyer_items[$i]'>";
							}else{
								$row->cells [$cellid]->value .="
								<div class='col-md-2 padding-left-none'><input class='form-control fourdigitstwodecimals_deciVal numberVal form-control1' id ='transport_charges_".$buyer_items[$i]."' value ='".$initialtransport."' name = 'transport_charges_".$buyer_items[$i]."' type='text' disabled></div>
								<div class='col-md-1 padding-left-none'><input class='form-control fourdigitstwodecimals_deciVal numberVal form-control1' id ='od_charges_".$buyer_items[$i]."' value ='".$initialod."' name = 'od_charges_".$buyer_items[$i]."' type='text' disabled></div>
								<div class='col-md-1 padding-left-none'><input class='form-control numericvalidation form-control1' maxlength ='3' id ='transit_days_".$buyer_items[$i]."' value ='".$initialTransitDays."' name = 'transit_days_".$buyer_items[$i]."' type='text' disabled></div>
								<div class='col-md-1' name='item_id' id='item_id' value='$buyer_items[$i]'></div>";
							}
						}else{
							if($ratecardType=='HHG'){
								$row->cells [$cellid]->value .="
								<div class='col-md-2 padding-left-none'><input class='form-control fourdigitstwodecimals_deciVal numberVal form-control1' id ='rateper_kg_".$buyer_items[$i]."' value ='".$initialQuotePrice."' name = 'rateper_kg_".$buyer_items[$i]."' type='text' disabled></div>
								<div class='col-md-2 padding-left-none'><input class='form-control numericvalidation form-control1' maxlength ='3' id ='transit_days_".$buyer_items[$i]."' value ='".$initialTransitDays."' name = 'transit_days_".$buyer_items[$i]."' type='text' disabled></div>
								<input type='hidden' name='item_id' id='item_id' value='$buyer_items[$i]'>";
								
								
							}else{
								
								$row->cells [$cellid]->value .="
								<div class='col-md-2 padding-left-none'><input class='form-control fourdigitstwodecimals_deciVal numberVal form-control1' id ='transport_charges_".$buyer_items[$i]."' value ='".$initialtransport."' name = 'transport_charges_".$buyer_items[$i]."' type='text' disabled></div>
								<div class='col-md-1 padding-left-none'><input class='form-control fourdigitstwodecimals_deciVal numberVal form-control1' id ='od_charges_".$buyer_items[$i]."' value ='".$initialod."' name = 'od_charges_".$buyer_items[$i]."' type='text' disabled></div>
								<div class='col-md-1 padding-left-none'><input class='form-control numericvalidation form-control1' maxlength ='3' id ='transit_days_".$buyer_items[$i]."' value ='".$initialTransitDays."' name = 'transit_days_".$buyer_items[$i]."' type='text' disabled></div>
								<div class='col-md-1' name='item_id' id='item_id' value='$buyer_items[$i]'></div>";
								
							}
							}
							
							
						$row->cells [$cellid]->value .="</div>";
							
						}
						
					}
					
					
				if(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){
				if($ratecardType=="Ocean"){	

				$row->cells [$cellid]->value .='
				<div class="table-row inner-block-bg">
				<div class="col-md-3 padding-left-none">
				<input type="checkbox" name="lineitem_checkbox" id="term_lineitem_'.$buyer_items[$i].'_'.$buyer_quote_id.'" class="lineitem_checkbox" onchange="javascript:checkSellerPostitem(this.id)"><span class="lbl padding-8"></span>'.$from_city_name.'
				</div>
				<div class="col-md-2 padding-left-none">'.$to_city_name.'</div>
				<div class="col-md-1 padding-left-none">
				<input type="text" id ="odlcl_charges_'.$buyer_items[$i].'" name ="odlcl_charges_'.$buyer_items[$i].'" value="'.$odlclcharges.'" class="form-control form-control1 clsRIATODChargespCFT numberVal" disabled>
				</div>
				<div class="col-md-1 padding-left-none">
				<input type="text" id ="odtwentyft_charges_'.$buyer_items[$i].'" name ="odtwentyft_charges_'.$buyer_items[$i].'" value="'.$odlcltwentycharges.'" class="form-control form-control1 clsRIATODChargespCFT numberVal" disabled>
				</div>
				<div class="col-md-1 padding-left-none">
				<input type="text" id ="odfortyft_charges_'.$buyer_items[$i].'" name ="odfortyft_charges_'.$buyer_items[$i].'" value="'.$odlclfortycharges.'" class="form-control form-control1 clsRIATODChargespCFT numberVal" disabled>
				</div>
				<div class="col-md-1 padding-left-none">
				<input type="text" id ="frieghtlcl_charges_'.$buyer_items[$i].'" name ="frieghtlcl_charges_'.$buyer_items[$i].'" value="'.$frieghtlclcharges.'" class="form-control form-control1 clsRIATFreightChargespKG numberVal" disabled>
				</div>
				<div class="col-md-1 padding-left-none">
				<input type="text" id ="frieghttwenty_charges_'.$buyer_items[$i].'" name ="frieghttwenty_charges_'.$buyer_items[$i].'" value="'.$frieghttwentylclcharges.'" class="form-control form-control1 clsRIATFreightChargespKG numberVal" disabled>
				</div>
				<div class="col-md-1 padding-left-none">
				<input type="text" id ="frieghtforty_charges_'.$buyer_items[$i].'" name ="frieghtforty_charges_'.$buyer_items[$i].'" value="'.$frieghtfortylclcharges.'" class="form-control form-control1 clsRIATFreightChargespKG numberVal" disabled>
				</div>
				<div class="col-md-1 padding-left-none">
				<input type="text" id ="transit_days_'.$buyer_items[$i].'" name ="transit_days_'.$buyer_items[$i].'" value="'.$initialTransitDays.'" class="form-control form-control1 clsRIATTransitDays" disabled>
				</div>
				</div>';
					
				
				}else{
					
					$row->cells [$cellid]->value .='
					<div class="table-row inner-block-bg">
					<div class="col-md-2 padding-left-none">
					<input type="checkbox" name="lineitem_checkbox" id="term_lineitem_'.$buyer_items[$i].'_'.$buyer_quote_id.'" class="lineitem_checkbox" onchange="javascript:checkSellerPostitem(this.id)"><span class="lbl padding-8"></span>'.$from_city_name.'
					</div>
					<div class="col-md-2 padding-left-none">'.$to_city_name.'</div>
					<div class="col-md-2 padding-left-none">
					<input type="text" id ="frieghthundred_charges_'.$buyer_items[$i].'" name ="frieghthundred_charges_'.$buyer_items[$i].'" value="'.$frighthndered.'" class="form-control form-control1 clsRIATFreightChargespKG numberVal minInput" disabled>
					</div>
					<div class="col-md-2 padding-left-none">
					<input type="text" id ="frieghtthreehundred_charges_'.$buyer_items[$i].'" name ="frieghtthreehundred_charges_'.$buyer_items[$i].'" value="'.$frightthreehundred.'" class="form-control form-control1 clsRIATFreightChargespKG numberVal minInput" disabled>
					</div>
					<div class="col-md-2 padding-left-none">
					<input type="text" id ="frieghtfivehundred_charges_'.$buyer_items[$i].'" name ="frieghtfivehundred_charges_'.$buyer_items[$i].'" value="'.$frightfivehundred.'" class="form-control form-control1 clsRIATFreightChargespKG numberVal minInput" disabled>
					</div>
					<div class="col-md-1 padding-left-none">
					<input type="text" id ="od_charges_'.$buyer_items[$i].'" name ="od_charges_'.$buyer_items[$i].'" value="'.$initialod.'" class="form-control form-control1 clsRIATODChargespCFT numberVal minInput" disabled>
					</div>
					<div class="col-md-1 padding-left-none">
					<input type="text" id ="transit_days_'.$buyer_items[$i].'" name ="transit_days_'.$buyer_items[$i].'" value="'.$initialTransitDays.'" class="form-control form-control1 clsRIATTransitDays minInput" disabled>
					</div>
					</div>';
				
				}
					
					}
				}
				
				if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC){
					if($ratecardType=='HHG'){
						$row->cells [$cellid]->value .='<div class="col-md-12 padding-none filter">
									<h2 class="filter-head1 margin-bottom">Additional Charges</h2>
									<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
									<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="crating_charges_'.$buyer_quote_id.'" placeholder="Crating Charges per CFT*" name="crating_charges_'.$buyer_quote_id.'" value="'.$crating.'" type="text" disabled>
									</div>
									</div>
									<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
									<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="storate_charges_'.$buyer_quote_id.'" placeholder="Storage Charges CFT per Day*" name="storate_charges_'.$buyer_quote_id.'" value="'.$storage.'" type="text" disabled>
									</div>
									</div>
									<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
									<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="escort_charges_'.$buyer_quote_id.'" placeholder="Escort Charges per Day*" name="escort_charges_'.$buyer_quote_id.'" value="'.$escort.'" type="text" disabled>
									</div>
									</div>
									<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
									<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="handyman_charges_'.$buyer_quote_id.'" placeholder="Handyman Charges per Hour*" name="handyman_charges_'.$buyer_quote_id.'" value="'.$handyman.'" type="text" disabled>
									</div>
									</div>
				
									<div class="clearfix"></div>
				
									<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
									<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="property_search_'.$buyer_quote_id.'" placeholder="Property Search Rs*" name="property_search_'.$buyer_quote_id.'" value="'.$property.'" type="text" disabled>
									</div>
									</div>
									<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
									<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="brokerage_'.$buyer_quote_id.'" placeholder="Brokerage Rs*" name="brokerage_'.$buyer_quote_id.'" value="'.$brokerage.'" type="text" disabled>
									</div>
									</div>
				
									</div>';
				
					}else{
						$row->cells [$cellid]->value .='<div class="col-md-12 padding-none filter">
									<h2 class="filter-head1 margin-bottom">Additional Charges</h2>
				
									<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
									<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="storate_charges_'.$buyer_quote_id.'" placeholder="Storage Charges CFT per Day*" name="storate_charges_'.$buyer_quote_id.'" value="'.$storage.'" type="text" disabled>
									</div>
									</div>
				
								</div>';
					}
				}
				if(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){
				if($ratecardType=="Ocean"){
				$row->cells [$cellid]->value .='
						<div class="col-md-4 padding-left-none">
							<input type="text" name="crating_charges_'.$buyer_quote_id.'" id="crating_charges_'.$buyer_quote_id.'" placeholder="Crating Charges (per CFT)" value="'.$crating.'" class="form-control form-control1 clsRIATStorageCharges numberVal" disabled>
						</div>
						<div class="clearfix"></div>
						<div class="col-md-12 form-control-fld">
                        	<span class="data-head">Additional Charges</span>
                        </div>
						<div class="clearfix"></div>
						<div class="col-md-4 padding-left-none">
							<input type="text" name="storate_charges_'.$buyer_quote_id.'" id="storate_charges_'.$buyer_quote_id.'"  placeholder="Storage Charges" value="'.$storage.'" class="form-control form-control1 clsRIATStorageCharges numberVal" disabled>
						</div>';
				}else{
				$row->cells [$cellid]->value .='
							<div class="col-md-12 form-control-fld">
	                        	<span class="data-head">Additional Charges</span>
	                        </div>
							<div class="clearfix"></div>
							<div class="col-md-4 padding-left-none">
								<input type="text" name="storate_charges_'.$buyer_quote_id.'" id="storate_charges_'.$buyer_quote_id.'" placeholder="Storage Charges" value="'.$storage.'" class="form-control form-control1 clsRIATStorageCharges numberVal" disabled>
							</div>';
				}
				}
				
			}elseif(Session::get ( 'service_id' )==RELOCATION_GLOBAL_MOBILITY){
				for($i=0;$i<count($buyer_items);$i++){
				$getbuyerquoteitems = DB::table('term_buyer_quote_items')
				->where('term_buyer_quote_items.id','=',$buyer_items[$i])
				->select('from_location_id','lkp_gm_service_id','measurement','measurement_units')
				->first();
				
				$initialQuotePriceDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
				->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_item_id','=',$buyer_items[$i])
				->where('term_buyer_quote_sellers_quotes_prices.seller_id','=',$userId)
				->select('initial_quote_price','is_submitted')
				->first();
				if(!empty($initialQuotePriceDisplay)){
					$initialQuotePrice = $initialQuotePriceDisplay->initial_quote_price;
					
				}else{
					$initialQuotePrice = "";
					
				}
				
				$from_city_name = CommonComponent::getCityName($getbuyerquoteitems->from_location_id);
				$service_gm_name = CommonComponent::getAllGMServiceTypesById($getbuyerquoteitems->lkp_gm_service_id);
				$row->cells [$cellid]->value .="<div class='table-row inner-block-bg'>
				<div class='col-md-3 padding-left-none'>
				<input type='checkbox' name='lineitem_checkbox' id='term_lineitem_$buyer_items[$i]' class='lineitem_checkbox' onchange='javascript:checkSellerPostitem(this.id)'><span class='lbl padding-8'></span>$from_city_name
				</div>
				<div class='col-md-3 padding-left-none'>$service_gm_name</div>
				<div class='col-md-3 padding-left-none'>$getbuyerquoteitems->measurement $getbuyerquoteitems->measurement_units</div>
				<div class='col-md-3 padding-left-none'>
				<input class='form-control form-control1 clsGMTRatepService' id ='intialquote_".$buyer_items[$i]."' value ='".$initialQuotePrice."' name = 'intialquote_".$buyer_items[$i]."' type='text' disabled>
				</div>
				</div>";
			}	
				
			}elseif((Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC) || (Session::get ( 'service_id' )==AIR_INTERNATIONAL) || (Session::get ( 'service_id' )==OCEAN)) {
				for($i=0;$i<count($buyer_items);$i++){
					$getbuyerquoteitems = DB::table('term_buyer_quote_items')
						->where('term_buyer_quote_items.id','=',$buyer_items[$i])
						->select('from_location_id','to_location_id','lkp_load_type_id','lkp_vehicle_type_id','quantity','lkp_packaging_type_id','number_packages')
						->first();

					$initialQuotePriceDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
						->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_item_id','=',$buyer_items[$i])
						->where('term_buyer_quote_sellers_quotes_prices.seller_id','=',$userId)
						->select('initial_quote_price','is_submitted','initial_rate_per_kg','initial_kg_per_cft')
						->first();

					if(!empty($initialQuotePriceDisplay)){
						$initialRateperKG = $initialQuotePriceDisplay->initial_rate_per_kg;
						$initialKgperCFT = $initialQuotePriceDisplay->initial_kg_per_cft;
					}else{
						$initialRateperKG = "";
						$initialKgperCFT = "";
					}
					$load_type_name = CommonComponent::getLoadType($getbuyerquoteitems->lkp_load_type_id);
					$vehicle_type_name = CommonComponent::getPackageType($getbuyerquoteitems->lkp_packaging_type_id);
					if((Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC)){
						$from_city_name = CommonComponent::getPinName($getbuyerquoteitems->from_location_id);
						$to_city_name = CommonComponent::getPinName($getbuyerquoteitems->to_location_id);
					}else if(Session::get ( 'service_id' )==AIR_INTERNATIONAL){
						$from_city_name = CommonComponent::getAirportName($getbuyerquoteitems->from_location_id);
						$to_city_name = CommonComponent::getAirportName($getbuyerquoteitems->to_location_id);
					}else if(Session::get ( 'service_id' )==OCEAN){
						$from_city_name = CommonComponent::getSeaportName($getbuyerquoteitems->from_location_id);
						$to_city_name = CommonComponent::getSeaportName($getbuyerquoteitems->to_location_id);
					}


					$initial_quote_price_price = CommonComponent::getLowestQuote($buyer_items[$i]);
					$initial_rate_per_kg = CommonComponent::getLowestRatePerKg($buyer_items[$i]);
					$initial_kg_per_cft = CommonComponent::getLowestKgPerCft($buyer_items[$i]);
					$row->cells [6]->value .="<div class='table-row inner-block-bg'><div class='col-md-2 padding-left-none'>
							<input type='checkbox' name='lineitem_checkbox' id='term_lineitem_$buyer_items[$i]' class='lineitem_checkbox' onchange='javascript:checkSellerPostitem(this.id)'><span class='lbl padding-8'></span>$from_city_name
							</div>
							<div class='col-md-2 padding-left-none'>$to_city_name</div>
							<div class='col-md-2 padding-left-none'>$load_type_name</div>
							<div class='col-md-1 padding-left-none'>$vehicle_type_name</div>
							<div class='col-md-1 padding-left-none'>$getbuyerquoteitems->number_packages</div>";
					if($getBidType == 'Open'){
						if(Session::get ( 'service_id' )==ROAD_FTL) {
						if(empty($initial_quote_price_price) && $initial_quote_price_price == ''){
							$row->cells [$cellid]->value .="<div class='col-md-2 padding-left-none'>--</div>";
						}else{
							$row->cells [$cellid]->value .="<div class='col-md-2 padding-left-none'>$initial_quote_price_price</div>";
						}
						}else{
						if(empty($initial_rate_per_kg) && $initial_rate_per_kg == ''){
								$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'>-</div>";
								$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'>-</div>";
							}else{
								$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'>$initial_rate_per_kg</div>";
								$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'>$initial_kg_per_cft</div>";
							}	
						}
					}
					if($getSaveValue){
						$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'><div class='input-prepend'>
													 <input class='form-control form-control1 numberVal  termMin2d fourdigitstwodecimals_deciVal  numberVal ' id ='initial_rate_per_kg_".$buyer_items[$i]."' value ='".$initialRateperKG."' name = 'initial_rate_per_kg_".$buyer_items[$i]."' type='text' disabled>
													 <input type='hidden' name='item_id' id='item_id' value='$buyer_items[$i]'></div>
												</div>
												<div class='col-md-1 col-sm-12 col-xs-12 padding-none'><div class='input-prepend'>";
												if(Session::get ( 'service_id' )==AIR_DOMESTIC || Session::get ( 'service_id' )==AIR_INTERNATIONAL) {
													$row->cells [$cellid]->value .="<input class='form-control form-control1 numberVal termMin4d fourdigitsfourdecimals_deciVal ' id ='initial_kg_per_cft_".$buyer_items[$i]."' value ='".$initialKgperCFT."' name = 'initial_kg_per_cft_".$buyer_items[$i]."' type='text' disabled>";
												}else{
													$row->cells [$cellid]->value .="<input class='form-control form-control1 numberVal fourdigitsthreedecimals_deciVal' id ='initial_kg_per_cft_".$buyer_items[$i]."' value ='".$initialKgperCFT."' name = 'initial_kg_per_cft_".$buyer_items[$i]."' type='text' disabled>";
												}
					
												 $row->cells [$cellid]->value .="</div></div>";
					}else{
						$row->cells [$cellid]->value .="<div class='col-md-1 padding-left-none'><div class='input-prepend'>
														<input class='form-control form-control1 termMin2d fourdigitstwodecimals_deciVal ' id ='initial_rate_per_kg_".$buyer_items[$i]."' value ='".$initialRateperKG."' name = 'initial_rate_per_kg_".$buyer_items[$i]."' type='text' disabled>
														<input type='hidden' name='item_id' id='item_id' value='$buyer_items[$i]'>
												 </div></div>
												 <div class='col-md-1 col-sm-12 col-xs-12 padding-none'><div class='input-prepend'>";
												 if(Session::get ( 'service_id' )==AIR_DOMESTIC || Session::get ( 'service_id' )==AIR_INTERNATIONAL) 
													$row->cells [$cellid]->value .="<input class='form-control form-control1  numberVal termMin4d fourdigitsfourdecimals_deciVal ' id ='initial_kg_per_cft_".$buyer_items[$i]."' value ='".$initialKgperCFT."' name = 'initial_kg_per_cft_".$buyer_items[$i]."' type='text'>";
												 else
												 	$row->cells [$cellid]->value .="<input class='form-control form-control1  numberVal fourdigitsthreedecimals_deciVal' id ='initial_kg_per_cft_".$buyer_items[$i]."' value ='".$initialKgperCFT."' name = 'initial_kg_per_cft_".$buyer_items[$i]."' type='text'>";
												
												 $row->cells [$cellid]->value .="</div></div>";
					}
					$row->cells [$cellid]->value .="</div>";
				}
			}elseif((Session::get ( 'service_id' )==COURIER)) {
			for($i=0;$i<count($buyer_items);$i++){
					$getbuyerquoteitems = DB::table('term_buyer_quote_items')
						->leftjoin ( 'term_buyer_quotes', 'term_buyer_quotes.id', '=', 'term_buyer_quote_items.term_buyer_quote_id' )
						->where('term_buyer_quote_items.id','=',$buyer_items[$i])
						->select('from_location_id','to_location_id','lkp_courier_type_id','lkp_courier_delivery_type_id','quantity','lkp_packaging_type_id','number_packages','volume')
						->first();
					
					$initialQuotePriceDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
						->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_item_id','=',$buyer_items[$i])
						->where('term_buyer_quote_sellers_quotes_prices.seller_id','=',$userId)
						->select('initial_quote_price','is_submitted','initial_rate_per_kg','initial_kg_per_cft')
						->first();

					if(!empty($initialQuotePriceDisplay)){
						$initialRateperKG = $initialQuotePriceDisplay->initial_rate_per_kg;
						$initialKgperCFT = $initialQuotePriceDisplay->initial_kg_per_cft;
					}else{
						$initialRateperKG = "";
						$initialKgperCFT = "";
					}
					
					if((Session::get ( 'service_id' )==COURIER)){
						$from_city_name = CommonComponent::getPinName($getbuyerquoteitems->from_location_id);
						if($getbuyerquoteitems->lkp_courier_delivery_type_id==1)
							$to_city_name = CommonComponent::getPinName($getbuyerquoteitems->to_location_id);
						else 
							$to_city_name = CommonComponent::getCountry($getbuyerquoteitems->to_location_id);
					}


					$initial_quote_price_price = CommonComponent::getLowestQuote($buyer_items[$i]);
					$initial_rate_per_kg = CommonComponent::getLowestRatePerKg($buyer_items[$i]);
					$initial_kg_per_cft = CommonComponent::getLowestKgPerCft($buyer_items[$i]);
					$row->cells [6]->value .="<div class='table-row inner-block-bg'>
							<div class='col-md-2 padding-left-none'>$from_city_name</div>
							<div class='col-md-2 padding-left-none'>$to_city_name</div>
							<div class='col-md-2 padding-left-none'>$getbuyerquoteitems->volume</div>
							<div class='col-md-1 padding-left-none'>$getbuyerquoteitems->number_packages</div></div>";
					
					
					
				}
			
			}
			if((Session::get ( 'service_id' )==COURIER)) {
				if($getSaveValue){
					$slabsprice = CommonComponent::getQuotePriceDetails($buyer_quote_id,$user_id);
						
					$maxweight = CommonComponent::getMaxWeightUnits($buyer_quote_id,$user_id);
					$row->cells [6]->value .= '
						
					
										<div class="col-md-12 inner-block-bg inner-block-bg1 ">
											<div class="col-md-3 form-control-fld">
												<span class="data-value">Maximum Weight: '.$maxweight[0]->max_weight_accepted." ".CommonComponent::getWeight($maxweight[0]->lkp_ict_weight_uom_id).'</span>
											</div>
											<div class="col-md-12 padding-none">
												<div class="col-md-12 padding-none">
													<!-- Table Starts Here -->
													<div class="table-div table-style1">
														<!-- Table Head Starts Here -->
														<div class="table-heading inner-block-bg">
															<div class="col-md-3 padding-left-none">Min</div>
															<div class="col-md-3 padding-left-none">Max</div>
															<div class="col-md-3 padding-left-none">Quote</div>
														</div>
														<!-- Table Head Ends Here -->
														<div class="table-data form-control-fld padding-none">
														<!-- Table Row Starts Here -->';
					
					$slabslist = CommonComponent::getSlabs($buyer_quote_id,$user_id);
					$incrementvalue=0;
					if(count($slabslist)>0){
						foreach($slabslist as $slabsitem){
							$incrementvalue = $incrementvalue + 1;
							$row->cells [6]->value .= '
								<div class="table-row inner-block-bg">
									<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_min_rate.'</div>
									<input name="slab_min_'.$incrementvalue.'_' . $buyer_quote_id . '" value="'.$slabsitem->slab_min_rate.'" type="hidden">
									<input name="slab_max_'.$incrementvalue.'_' . $buyer_quote_id . '" value="'.$slabsitem->slab_max_rate.'" type="hidden">
									<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_max_rate.'</div>
									<div class="col-md-3 padding-left-none">';
							$slabslistsaved = CommonComponent::getQuotePriceDetailsSlabsSaved($buyer_quote_id,$user_id,$slabsitem->slab_min_rate,$slabsitem->slab_max_rate);
							
							$row->cells [6]->value .= isset($slabslistsaved[0]->slab_rate)?'<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" name="slab_'.$incrementvalue.'_' . $buyer_quote_id . '" placeholder="" value="'.$slabslistsaved[0]->slab_rate.'">':'<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" name="slab_'.$incrementvalue.'_' . $buyer_quote_id . '" placeholder="" type="text">';
							$row->cells [6]->value .= '</div>
									<div class="col-md-1 form-control-fld padding-left-none text-right"></div>
								</div>';
						}
						$row->cells [6]->value .= '<input name="increment" value="'.$incrementvalue.'" type="hidden">';
					}
					$row->cells [6]->value .= '<!-- Table Row Ends Here -->
								
														</div>';
					
					if(count($maxweight)>0 && isset($maxweight[0]->increment_weight) && $maxweight[0]->increment_weight>0 ) {
						$row->cells [6]->value .= '<div class="col-md-5 form-control-fld padding-none ">
															<div class="col-md-3 padding-left-none margin-top">'.$maxweight[0]->increment_weight.' ';
						if($maxweight[0]->lkp_ict_weight_uom_id==1)
							$row->cells [6]->value .='Kgs';
						elseif($maxweight[0]->lkp_ict_weight_uom_id==2)
						$row->cells [6]->value .='Gms';
						else
							$row->cells [6]->value .='MTS';
						$row->cells [6]->value .= '<input name="increment_weight_' . $buyer_quote_id . '" value="'.$maxweight[0]->increment_weight.'" type="hidden">
															</div>
															<div class="col-md-3 padding-left-none">';
												$row->cells [6]->value .= isset($slabsprice[0]->incremental_weight_price)?'<input class="form-control form-control1 numberVal" name="increment_value_' . $buyer_quote_id . '"  value="'.$slabsprice[0]->incremental_weight_price.'">':'<input class="form-control form-control1 numberVal" name="increment_value_' . $buyer_quote_id . '" placeholder="" type="text">';
												
												$row->cells [6]->value .='</div>
														</div>';
					}else{
						$row->cells [6]->value .= '<input type="hidden" class="form-control form-control1 numberVal" name="increment_value_' . $buyer_quote_id . '" value="0">';
					}
					$row->cells [6]->value .= '<div class="col-md-12 form-control-fld padding-none ">
															<div class="col-md-3 padding-left-none">';
															$row->cells [6]->value .=isset($slabsprice[0]->conversion_factor)?'<input class="form-control form-control1 twodigitstwodecimals_deciVal numberVal" placeholder="Conversion factor" name="conversion_' . $buyer_quote_id . '" value="'.$slabsprice[0]->conversion_factor.'">':'<input class="form-control form-control1 twodigitstwodecimals_deciVal numberVal" placeholder="Conversion factor" name="conversion_' . $buyer_quote_id . '" type="text">';
															$row->cells [6]->value .='</div>';
															$row->cells [6]->value .='<input type="hidden" class="form-control form-control1 numberVal" placeholder="Maximum weight accepted"  name="maxweightaccept_' . $buyer_quote_id . '" value="1">';
															$row->cells [6]->value .='<div class="col-md-3 padding-left-none">';
															$row->cells [6]->value .=isset($slabsprice[0]->transit_days)?'<input class="form-control form-control1 numericvalidation numberVal" maxlength ="3" placeholder="Transit days" name="transitdays_' . $buyer_quote_id . '" value="'.$slabsprice[0]->transit_days.'">':'<input class="form-control form-control1 numericvalidation" maxlength ="3" placeholder="Transit days" name="transitdays_' . $buyer_quote_id . '" type="text">';
															$row->cells [6]->value .='</div>
														</div>
														<div class="col-md-12 padding-none">
															<h5 class="caption-head">Additional Charges</h5>
															<div class="col-md-3 form-control-fld">';
															$row->cells [6]->value .= 	isset($slabsprice[0]->fuel_charges)?'<input type="text" class="form-control form-control1 twodigitstwodecimals_deciVal numberVal" name= "fuel_surcharge_' . $buyer_quote_id . '" placeholder="Fuel Surcharge %*" value="'.$slabsprice[0]->fuel_charges.'"/>':'<input type="text" class="form-control form-control1 twodigitstwodecimals_deciVal numberVal" name= "fuel_surcharge_' . $buyer_quote_id . '" placeholder="Fuel Surcharge %*" />';
															
															$row->cells [6]->value .= '</div>
															<div class="col-md-3 form-control-fld">';
															$row->cells [6]->value .= isset($slabsprice[0]->cod_charges)?'<input type="text" class="form-control form-control1 twodigitstwodecimals_deciVal numberVal" name= "cod_charge_' . $buyer_quote_id . '" placeholder="Check on Delivery %*" value="'.$slabsprice[0]->cod_charges.'"/>':'<input type="text" class="form-control form-control1 twodigitstwodecimals_deciVal numberVal" name= "cod_charge_' . $buyer_quote_id . '" placeholder="Check on Delivery %*" />';
															$row->cells [6]->value .= '</div>
															<div class="col-md-3 form-control-fld">';
															$row->cells [6]->value .= isset($slabsprice[0]->freight_charges)?'<input type="text" class="form-control form-control1 fivedigitstwodecimals_deciVal numberVal" name="freight_charge_' . $buyer_quote_id . '" placeholder="Freight Collect *" value="'.$slabsprice[0]->freight_charges.'"/>':'<input type="text" class="form-control fivedigitstwodecimals_deciVal form-control1 numberVal" name="freight_charge_' . $buyer_quote_id . '" placeholder="Freight Collect *" />';
															$row->cells [6]->value .= '</div>
															<div class="col-md-3 form-control-fld">';
															$row->cells [6]->value .= isset($slabsprice[0]->arc_charges)?'<input type="text" class="form-control twodigitstwodecimals_deciVal form-control1 numberVal" name="arc_charge_' . $buyer_quote_id . '" placeholder="ARC %*" value="'.$slabsprice[0]->arc_charges.'"/>':'<input type="text" class="form-control form-control1 twodigitstwodecimals_deciVal numberVal" name="arc_charge_' . $buyer_quote_id . '" placeholder="ARC %*" />';
															$row->cells [6]->value .= '</div>
															<div class="clearfix"></div>
															<div class="col-md-3 form-control-fld">';
															$row->cells [6]->value .= isset($slabsprice[0]->max_value)?'<input type="text" class="form-control form-control1 fivedigitstwodecimals_deciVal numberVal" name="max_value_' . $buyer_quote_id . '" placeholder="Maximum Value *" value="'.$slabsprice[0]->max_value.'"/>':'<input type="text" class="form-control form-control1 fivedigitstwodecimals_deciVal numberVal" name="max_value_' . $buyer_quote_id . '" placeholder="Maximum Value *" />';
															$row->cells [6]->value .= '</div>
														</div>
													</div>
												</div>
											</div>
										</div>';
					
				}else{
					$slabslist = CommonComponent::getQuotePriceDetailsSlabs($buyer_quote_id,$user_id);
					$maxweight = CommonComponent::getMaxWeightUnits($buyer_quote_id,$user_id);
					$row->cells [6]->value .='<div class="col-md-12 inner-block-bg inner-block-bg1 ">
							<div class="col-md-3 form-control-fld">
								<span class="data-value">Maximum Weight : '.$maxweight[0]->max_weight_accepted." ".CommonComponent::getWeight($maxweight[0]->lkp_ict_weight_uom_id).'</span>
							</div>
							<div class="col-md-12 padding-none">
							<div class="col-md-12 padding-none">
							<!-- Table Starts Here -->
							<div class="table-div table-style1">
									<!-- Table Head Starts Here -->
									<div class="table-heading inner-block-bg">
									<div class="col-md-3 padding-left-none">Min</div>
									<div class="col-md-3 padding-left-none">Max</div>
									<div class="col-md-3 padding-left-none">Quote</div>
									</div>
									<!-- Table Head Ends Here -->
									<div class="table-data form-control-fld padding-none">
									<!-- Table Row Starts Here -->';
									
									$incrementvalue=0;
									if(count($slabslist)>0){
										foreach($slabslist as $slabsitem){
											$incrementvalue = $incrementvalue + 1;
											$row->cells [6]->value .= '
											<div class="table-row inner-block-bg">
												<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_min_rate.'</div>
												<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_max_rate.'</div>
												<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_rate.'</div>
												<div class="col-md-1 form-control-fld padding-left-none text-right"></div>
											</div>';
										}
										$row->cells [6]->value .= '<input name="increment" value="'.$incrementvalue.'" type="hidden">';
									}
									$row->cells [6]->value .= '<!-- Table Row Ends Here -->
								
																			</div>';
										
									$slabsprice = CommonComponent::getQuotePriceDetails($buyer_quote_id,$user_id);
									
								
									if(count($slabsprice)>0 && isset($maxweight[0]->increment_weight) && $maxweight[0]->increment_weight>0 ){
										$row->cells [6]->value .= '<div class="col-md-5 form-control-fld padding-none ">
																				<div class="col-md-3 padding-left-none margin-top">'.$maxweight[0]->increment_weight.' ';
										if($maxweight[0]->lkp_ict_weight_uom_id==1)
											$row->cells [6]->value .='Kgs';
										elseif($maxweight[0]->lkp_ict_weight_uom_id==2)
										$row->cells [6]->value .='Gms';
										else
											$row->cells [6]->value .='MTS';
										$row->cells [6]->value .= '
																				</div>
																				<div class="col-md-3 padding-left-none margin-top ">
																					'.$slabsprice[0]->incremental_weight_price.' /-
																				</div>
																			</div>';
									}else{
										$row->cells [6]->value .= '<input type="hidden" class="form-control form-control1 numberVal" name="increment_value_' . $buyer_quote_id . '" value="0">';
									}
									$row->cells [6]->value .= '
									<div class="col-md-12 form-control-fld padding-none ">
										<div class="col-md-3 padding-left-none">Conversion factor : '.$slabsprice[0]->conversion_factor.' /-</div>
										<div class="col-md-3 padding-left-none">Transit days : '.$slabsprice[0]->transit_days.'</div>
									</div>
									<div class="col-md-12 padding-none">
									<h5 class="caption-head">Additional Charges</h5>
									<div class="col-md-3 form-control-fld">
									Fuel Surcharge : '.$slabsprice[0]->fuel_charges.' %
									</div>
									<div class="col-md-3 form-control-fld">
									COD Charge : '.$slabsprice[0]->cod_charges.' %
									</div>
									<div class="col-md-3 form-control-fld">
																	Freight Charge : '.$slabsprice[0]->freight_charges.' /-
																</div>
									<div class="col-md-3 form-control-fld">
									ARC Charge : '.$slabsprice[0]->arc_charges.' %
									</div>
									<div class="clearfix"></div>
									<div class="col-md-3 form-control-fld">
									Max Value : '.$slabsprice[0]->max_value.' /-
									</div>
									</div>
									</div>
									</div>
									</div>
									</div>';
				}
			}
			
			if($today < $bidDateTimes){
						if($getSaveValue){
							if((Session::get ( 'service_id' )!=COURIER)){
							$row->cells [$cellid]->value .="
								<div class='col-md-12 padding-none text-right'>
									<button type='button' value='save' name='save' id ='save_$buyer_quote_id' class='btn margin-top add-btn flat-btn termintialquotesubmit margin-bottom' >Save as Draft</button> 
									<button type='button'id ='submit_$buyer_quote_id' value='submit' name='submit' class='btn red-btn flat-btn margin-top pull-right termintialquotesubmit margin-bottom' >Submit</button>
								</div>";
							}else{
								$row->cells [$cellid]->value .="
								<div class='col-md-12 padding-none text-right'>
								<button type='button' value='save' name='save' id ='save_$buyer_quote_id' class='btn margin-top add-btn flat-btn couriertermintialquotesubmit margin-bottom' >Save as Draft</button>
								<button type='button' id ='submit_$buyer_quote_id' value='submit' name='submit' class='btn red-btn flat-btn margin-top pull-right couriertermintialquotesubmit margin-bottom' >Submit</button>
								</div>";
							}
						}
					}
							$row->cells [$cellid]->value .="</form>
							</div>						

		
			<!--div class='text-right pull-right'>
				$buyer_id
			</div-->

			</div>";
			$row->attributes(array("class" => "col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none table-row mobile-padding-none"));
		
		} );

		// Functionality to build filters in the page starts
		$filter = DataFilter::source ( $Query );
		$filter->add ( 'bqit.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
		$filter->add ( 'bqit.to_location_id', 'To Location', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
		$filter->add ( 'bqi.lkp_post_ratecard_type', 'Post For', 'select')->options($ratecard_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
		$filter->add ( 'bqi.lkp_courier_type_id', 'Courier Type', 'select' )->options ( $ptlCourierTypes )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->submit ( 'search' );
		$filter->reset ( 'reset' );
		$filter->build ();
		// Functionality to build filters in the page ends
		
		$result = array ();
		$result ['grid'] = $grid;
		$result ['filter'] = $filter;
		return $result;
	}
 

	public static function getTermSellerSearchList($roleId, $serviceId,$statusId) {
	
	$from_locations = array(""=>"From Location");
	$to_locations = array(""=>"To Location");
	$vehicle_types = array("" => "Vehicle Type");
	$load_types = array("" => "Load Type");
	$packagetypes = array("" => "Package Type");
	$servicetypes = array(""=>"Services");
	//$inputparams = array();
	$inputparams = $_REQUEST;
	$buyerNames = array ();
	
	if(isset($_REQUEST['term_from_city_id']) && $_REQUEST['term_from_city_id']!=''){
		$inputparams['from_location_id'] = $_REQUEST['term_from_city_id'];
	}
	if(isset($_REQUEST['term_to_city_id']) && $_REQUEST['term_to_city_id']!=''){
		$inputparams['to_location_id'] = $_REQUEST['term_to_city_id'];
	}
	
	if(isset($_REQUEST['lkp_vehicle_type_ids']) && $_REQUEST['lkp_vehicle_type_ids']!=''){
		if(isset($_REQUEST['lkp_vehicle_type_id']) && $_REQUEST['lkp_vehicle_type_id']!=''){
			$_REQUEST['lkp_vehicle_type_id'] =$_REQUEST['lkp_vehicle_type_id'];
		}else{
			$_REQUEST['lkp_vehicle_type_id'] =$_REQUEST['lkp_vehicle_type_ids'];
		}
	}
	if(isset($_REQUEST['lkp_load_type_ids']) && $_REQUEST['lkp_load_type_ids']!=''){
		if(isset($_REQUEST['lkp_load_type_id']) && $_REQUEST['lkp_load_type_id']!=''){
			$_REQUEST['lkp_load_type_id'] =$_REQUEST['lkp_load_type_id'];
		}else{
			$_REQUEST['lkp_load_type_id'] =$_REQUEST['lkp_load_type_ids'];
		}
	}

  //echo "--<pre>";print_R($inputparams);echo "</pre>";die;
	
	$Query_buyers_for_sellers = TermSellerSearchComponent::search ( $roleId, $serviceId, $statusId, $inputparams );

	if(isset($_REQUEST['lkp_vehicle_type_id']) && $_REQUEST['lkp_vehicle_type_id']!='' && isset($_REQUEST['lkp_load_type_id']) && $_REQUEST['lkp_load_type_id']!='' && isset($_REQUEST['from_location_id']) && $_REQUEST['from_location_id']!='' && isset($_REQUEST['to_location_id']) && $_REQUEST['to_location_id']!='')
	{
		$sellerpost_for_buyers  =  new FtlSearchTerm();
		$sellerpost_for_buyers->user_id = Auth::id();
		$sellerpost_for_buyers->from_city_id = $_REQUEST['from_city_id'];
		$sellerpost_for_buyers->to_city_id = $_REQUEST['to_city_id'];
		$sellerpost_for_buyers->lkp_load_type_id = $_REQUEST['lkp_load_type_id'];
		$sellerpost_for_buyers->lkp_vehicle_type_id = $_REQUEST['lkp_vehicle_type_id'];
		$sellerpost_for_buyers->quantity = 1;
		$sellerpost_for_buyers->created_at = date ( 'Y-m-d H:i:s' );
		$sellerpost_for_buyers->created_ip = $_SERVER ['REMOTE_ADDR'];
		$sellerpost_for_buyers->created_by = Auth::id();
		$sellerpost_for_buyers->save();
		//echo "<pre>";print_r($_REQUEST);exit;
		Session::put('session_delivery_date','');
		Session::put('session_dispatch_date','');
		Session::put('session_vehicle_type',$_REQUEST['lkp_vehicle_type_id']);
		Session::put('session_load_type',$_REQUEST['lkp_load_type_id']);
		Session::put('session_from_city_id',$_REQUEST['from_city_id']);
		Session::put('session_to_city_id',$_REQUEST['to_city_id']);
		Session::put('session_from_location',$_REQUEST['from_location']);
		Session::put('session_to_location',$_REQUEST['to_location']);
		Session::put('session_seller_district_id',$_REQUEST['seller_district_id']);
	
	}
	//echo "<pre>";print_r($_REQUEST);exit;
	if($serviceId==ROAD_FTL || $serviceId == RELOCATION_DOMESTIC){
		Session::put('session_delivery_date','');
		Session::put('session_dispatch_date','');
		if($serviceId == ROAD_FTL){
			Session::put('session_vehicle_type',$_REQUEST['lkp_vehicle_type_id']);
			Session::put('session_load_type',$_REQUEST['lkp_load_type_id']);
		}
		if($serviceId == RELOCATION_DOMESTIC){
			Session::put('session_post_rate_card_type',$_REQUEST['term_post_rate_card_type']);
		}
		if(isset($_REQUEST['term_from_city_id'])){
		Session::put('session_from_city_id',$_REQUEST['term_from_city_id']);
		}else{
		Session::put('session_from_city_id',$_REQUEST['from_city_id']);
		}
		if(isset($_REQUEST['term_to_city_id'])){
			Session::put('session_to_city_id',$_REQUEST['term_to_city_id']);
		}else{
			Session::put('session_to_city_id',$_REQUEST['to_city_id']);
		}
		if(isset($_REQUEST['term_from_location'])){
			Session::put('session_from_location',$_REQUEST['term_from_location']);
		}else{
			Session::put('session_from_location',$_REQUEST['from_location']);
		}
		if(isset($_REQUEST['term_to_location'])){
			Session::put('session_to_location',$_REQUEST['term_to_location']);
		}else{
			Session::put('session_to_location',$_REQUEST['to_location']);
		}

		Session::put('session_seller_district_id',$_REQUEST['seller_district_id']);
		Session::put('session_spot_or_term',$_REQUEST['spot_or_term']);
	
	}else if($serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN){
		//echo "<pre>";print_r($_REQUEST);exit;
		Session::put('session_delivery_date','');
		Session::put('session_dispatch_date','');
		Session::put('session_load_type',$_REQUEST['lkp_load_type_id']);
		Session::put('session_spot_or_term',$_REQUEST['spot_or_term']);
		Session::put('session_from_location',$_REQUEST['term_from_location']);
		Session::put('session_from_city_id',$_REQUEST['term_from_location_id']);
		Session::put('session_to_location',$_REQUEST['term_to_location']);
		Session::put('session_to_city_id',$_REQUEST['term_to_location_id']);
		Session::put('session_load_type',$_REQUEST['lkp_load_type_id']);
		Session::put('session_vehicle_type',$_REQUEST['lkp_packaging_type_id']);
		Session::put('session_spot_or_term',$_REQUEST['spot_or_term']);
		Session::put('session_shipment_type',$_REQUEST['lkp_air_ocean_shipment_type_id']);
		Session::put('session_sender_identity',$_REQUEST['lkp_air_ocean_sender_identity_id']);
	}else if($serviceId==COURIER) {
		//echo "<pre>";print_r($_REQUEST);exit;
		Session::put('session_delivery_date', '');
		Session::put('session_dispatch_date', '');
		Session::put('session_spot_or_term', $_REQUEST['spot_or_term']);
		Session::put('session_from_location', $_REQUEST['term_from_location']);
		Session::put('session_from_city_id', $_REQUEST['term_from_location_id']);
		Session::put('session_to_location', $_REQUEST['term_to_location']);
		Session::put('session_to_city_id', $_REQUEST['term_to_location_id']);
		if (isset($_REQUEST['courier_or_types'])) {
			Session::put('session_courier_types', $_REQUEST['courier_or_types']);
		}
		if (isset($_REQUEST['courier_or_types'])) {
			Session::put('session_courier_delivery_type', $_REQUEST['post_or_delivery_type']);
		}
		if (isset($_REQUEST['zone_or_location_ptl'])) {
			Session::put('zone_or_location_ptl', $_REQUEST['zone_or_location_ptl']);
		} else {
			Session::put('zone_or_location_ptl', $_REQUEST['zone_or_location']);
		}
	}else if($serviceId==RELOCATION_INTERNATIONAL){
		Session::put('session_delivery_date','');
		Session::put('session_dispatch_date','');

		if(isset($_REQUEST['term_from_city_id'])){
			Session::put('session_from_city_id',$_REQUEST['term_from_city_id']);
		}else{
			Session::put('session_from_city_id',$_REQUEST['from_city_id']);
		}
		if(isset($_REQUEST['term_to_city_id'])){
			Session::put('session_to_city_id',$_REQUEST['term_to_city_id']);
		}else{
			Session::put('session_to_city_id',$_REQUEST['to_city_id']);
		}
		if(isset($_REQUEST['term_from_location'])){
			Session::put('session_from_location',$_REQUEST['term_from_location']);
		}else{
			Session::put('session_from_location',$_REQUEST['from_location']);
		}
		if(isset($_REQUEST['term_to_location'])){
			Session::put('session_to_location',$_REQUEST['term_to_location']);
		}else{
			Session::put('session_to_location',$_REQUEST['to_location']);
		}

		Session::put('session_seller_district_id',$_REQUEST['seller_district_id']);
		Session::put('session_spot_or_term',$_REQUEST['spot_or_term']);
		Session::put('session_term_service_type',$_REQUEST['term_service_type']);
	}else if($serviceId==RELOCATION_GLOBAL_MOBILITY){
		Session::put('session_delivery_date','');
		Session::put('session_dispatch_date','');

		if(isset($_REQUEST['term_from_city_id'])){
			Session::put('session_from_city_id',$_REQUEST['term_from_city_id']);
		}else{
			Session::put('session_from_city_id',$_REQUEST['from_city_id']);
		}
		if(isset($_REQUEST['term_from_location'])){
			Session::put('session_from_location',$_REQUEST['term_from_location']);
		}else{
			Session::put('session_from_location',$_REQUEST['from_location']);
		}
		Session::put('session_seller_district_id',$_REQUEST['seller_district_id']);
		Session::put('session_spot_or_term',$_REQUEST['spot_or_term']);
		Session::put('session_term_relgm_service_type',$_REQUEST['relgm_service_type']);
	}else{
		//echo "<pre>";print_r($_REQUEST);exit;
		Session::put('session_delivery_date','');
		Session::put('session_dispatch_date','');
		Session::put('session_load_type',$_REQUEST['lkp_load_type_id']);
		Session::put('session_spot_or_term',$_REQUEST['spot_or_term']);
		Session::put('session_from_location',$_REQUEST['term_from_location']);
		Session::put('session_from_city_id',$_REQUEST['term_from_location_id']);
		Session::put('session_to_location',$_REQUEST['term_to_location']);
		Session::put('session_to_city_id',$_REQUEST['term_to_location_id']);
		Session::put('session_load_type',$_REQUEST['lkp_load_type_id']);
		Session::put('session_vehicle_type',$_REQUEST['lkp_packaging_type_id']);
		
		if(isset($_REQUEST['zone_or_location_ptl'])){
			Session::put('zone_or_location_ptl',$_REQUEST['zone_or_location_ptl']);
		}else{
			Session::put('zone_or_location_ptl',$_REQUEST['zone_or_location']);
		}
	}
	$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();
	//echo "<pre>"; print_r($Query_buyers_for_sellers_filter); echo  "</pre>"; exit;
	
	if(count($Query_buyers_for_sellers_filter) == 0 ){
		Session::put('results_count','1');
		Session::put('layered_filter','');
	}else{
		Session::put('results_count','');
		Session::put('results_count_more','2');
	}

	
	//echo "<pre>";print_R($Query_buyers_for_sellers_filter);
	foreach($Query_buyers_for_sellers_filter as $Query_buyers_for_seller){
		$buyers_for_sellers_items  = DB::table('term_buyer_quote_items')
		->where('term_buyer_quote_items.term_buyer_quote_id',$Query_buyers_for_seller->id)
		->select('*')
		->get();
		//echo "<pre>"; print_R($buyers_for_sellers_items);
		Session::put('delivery_date',$Query_buyers_for_seller->to_date);
		Session::put('dispatch_date',$Query_buyers_for_seller->from_date);
		if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
			Session::put('vehicle_type',$Query_buyers_for_seller->vehicle_type);
			Session::put('load_type',$Query_buyers_for_seller->load_type);
		}	

		foreach($buyers_for_sellers_items as $buyers_for_sellers_item){
			if(!isset($from_locations[$buyers_for_sellers_item->from_location_id])){
				$from_locations[$buyers_for_sellers_item->from_location_id] = DB::table('lkp_cities')->where('id', $buyers_for_sellers_item->from_location_id)->pluck('city_name');
			}
			if(!isset($to_locations[$buyers_for_sellers_item->to_location_id])){
				$to_locations[$buyers_for_sellers_item->to_location_id] = DB::table('lkp_cities')->where('id', $buyers_for_sellers_item->to_location_id)->pluck('city_name');
			}
			if(!isset($load_types[$buyers_for_sellers_item->lkp_load_type_id])){
				$load_types[$buyers_for_sellers_item->lkp_load_type_id] = DB::table('lkp_load_types')->where('id', $buyers_for_sellers_item->lkp_load_type_id)->pluck('load_type');
			}
			if(!isset($vehicle_types[$buyers_for_sellers_item->lkp_vehicle_type_id])){
				$vehicle_types[$buyers_for_sellers_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $buyers_for_sellers_item->lkp_vehicle_type_id)->pluck('vehicle_type');
			}
			//echo $buyers_for_sellers_item->lkp_packaging_type_id."--";
			if(!isset($packagetypes[$buyers_for_sellers_item->lkp_packaging_type_id])){
				$packagetypes[$buyers_for_sellers_item->lkp_packaging_type_id] = DB::table('lkp_packaging_types')->where('id', $buyers_for_sellers_item->lkp_packaging_type_id)->pluck('packaging_type_name');
			}

			if(isset($_REQUEST['is_search'])){
				if (! isset ( $buyerNames [$Query_buyers_for_seller->buyer_id] )) {
					$buyerNames[$Query_buyers_for_seller->buyer_id] = $Query_buyers_for_seller->username;
				}
				
				Session::put('layered_filter', $buyerNames);
			}
		}
	}
        //echo "<pre>";print_R($Query_buyers_for_sellers->get());die;

	$grid = DataGrid::source ( $Query_buyers_for_sellers );
	$grid->add ( 'id', 'ID', true )->style ( "display:none" );
	$grid->add ( 'username', 'Name', 'username' )->attributes(array("class" => "col-md-2 padding-left-none"));
	$grid->add ( 'delivery_sdate', 'Rating', 'delivery_sdate' )->attributes(array("class" => "col-md-2 padding-left-none"))->style ( "display:none" );
	$grid->add ( 'from_date', 'Valid From/To', 'from_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
	$grid->add ( 'bid_end_date', 'Bid End Date', 'bid_end_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
	$grid->add ( 'lkp_post_status_id', 'Status', 'lkp_post_status_id' )->attributes(array("class" => "col-md-1 padding-none status-right"));
	$grid->add ( 'load_type', 'LoadType', 'load_type' )->style ( "display:none" );
	$grid->add ( 'vehicle_type', 'VehicleType', 'vehicle_type' )->style ( "display:none" );
	$grid->add ( 'fromcity', 'FromCity', 'fromcity' )->style ( "display:none" );
	$grid->add ( 'tocity', 'Tocity', 'tocity' )->style ( "display:none" );
	$grid->add ( 'to_date', 'Delivery Date', 'to_date' )->style ( "display:none" );
	$grid->add ( 'created_by', 'Created By', 'created_by' )->style ( "display:none" );
	$grid->add ( 'lkp_quote_access_id', 'Quote Access', 'lkp_quote_access_id' )->style ( "display:none" );
	$grid->add ( 'transaction_id', 'transaction_id', 'transaction_id' )->style ( "display:none" );
	if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC){
		$grid->add ( 'lkp_post_ratecard_type', 'Post For', 'lkp_post_ratecard_type' )->attributes(array("class" => "col-md-1 padding-left-none"))->style ( "display:none" );
	}
        if(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){
		$grid->add ( 'number_loads', 'No of moves', 'number_loads' )->style ( "display:none" );
                $grid->add ( 'avg_kg_per_move', 'kg per move', 'avg_kg_per_move' )->style ( "display:none" );
                $grid->add ( 'lkp_lead_type_id', 'Lead Type', 'lkp_lead_type_id' )->style ( "display:none" );
	}
	$grid->orderBy ( 'id', 'desc' );
	$grid->paginate ( 5 );
	$grid->row ( function ($row) {

		$userId = Auth::user ()->id;
		$row->cells [0]->style ( 'display:none' );
		$row->cells [1]->style ( 'display:none' );
		$row->cells [2]->style ( 'display:none' );
		$row->cells [3]->style ( 'display:none' );
		$row->cells [4]->style ( 'display:none' );
		$row->cells [6]->style ( 'display:none' );
		$row->cells [7]->style ( 'display:none' );
		$row->cells [8]->style ( 'display:none' );
		$row->cells [9]->style ( 'display:none' );
		$row->cells [10]->style ( 'display:none' );
		$row->cells [11]->style ( 'display:none' );
		$row->cells [12]->style ( 'display:none' );
                $row->cells [13]->style ( 'display:none' );
		if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC) {
			$row->cells [14]->style('display:none');
		}
                if(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL) {
			$row->cells [14]->style('display:none');
                        $row->cells [15]->style('display:none');
                        $row->cells [16]->style('display:none');
		}
		$row->cells [5]->style ( 'width:100%' );
		$accessid = $row->cells [12]->value;
		$buyer_id = $row->cells [11]->value;
		$buyer_quote_id = $row->cells [0]->value;
                $transaction_id = $row->cells [13]->value;
		if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC){
			$ratecardType= $row->cells [14]->value;
		}
		$buyer_name = $row->cells [1]->value;
		$dispatch_date_buyer = $row->cells [3]->value;
		$delivery_date_buyer = $row->cells [10]->value;
		$price_buyer = $row->cells [4]->value;
		$buyer_post_status = $row->cells [5]->value;
		$load_type_buyer = $row->cells [6]->value;
		$vechile_type_buyer = $row->cells [7]->value;
		$fromcity_buyer = $row->cells [8]->value;
		$tocity_buyer = $row->cells [9]->value;
		
                if(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL) {
                    $noofmoves= $row->cells [14]->value;
                    $avgkgmove= $row->cells [15]->value;
                    $leadtype= $row->cells [16]->value;
                }
		
		$delivery_date_buyer = $row->cells [10]->value;
		if($buyer_post_status == 1){
			$buyer_post_status = 'Saved as Draft';
		}
		if($buyer_post_status == 2){
			$buyer_post_status = 'Open';
		}
		if($buyer_post_status == 3){
			$buyer_post_status = 'Closed';
		}
		if($buyer_post_status == 4){
			$buyer_post_status = 'Booked';
		}
		if($buyer_post_status == 5){
			$buyer_post_status = 'Cancelled';
		}
		if($price_buyer == 0){
			$price_buyer = "Competitive";
		}else{
			$price_buyer = "Firm";
		}
		$bidenddate = CommonComponent::getBidDateTimeByQuoteId($buyer_quote_id,Session::get ( 'service_id' ));
			
		if((Session::get ( 'service_id' )!=COURIER)){
			$row->cells [5]->value = '
			<form name ="intialquotebidding_'.$buyer_quote_id.'" id ="intialquotebidding_'.$buyer_quote_id.'" class="termintialquotesubmit_form" >';
		}else{
			$row->cells [5]->value = '
			<form name ="intialquotebidding_'.$buyer_quote_id.'" id ="intialquotebidding_'.$buyer_quote_id.'" class="couriertermintialquotesubmit_form" >';
		}
		$getInitialQuotePrice = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'initial_quote_price','buyer_quote_sellers_quotes_prices');
		$getCounterQuotePrice = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'counter_quote_price','buyer_quote_sellers_quotes_prices');
		$getFinalQuotePrice   = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'final_quote_price','buyer_quote_sellers_quotes_prices');
		$getFirmQuotePrice   = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'firm_price','buyer_quote_sellers_quotes_prices');
		$getBidType = CommonComponent::getBidType($buyer_quote_id);
		$getSaveValue = CommonComponent::getIsSubmitData($buyer_quote_id);
		$getDateValue = CommonComponent::getBidDateTime($buyer_quote_id,Session::get ( 'service_id' ));
		date_default_timezone_set('Asia/Calcutta');
		$today = date("Y-m-d H:i:00");
		$bidDateTimes = CommonComponent::getBidDateTimeByQuoteId($buyer_quote_id,Session::get ( 'service_id' ));
		$buyer_items = CommonComponent::getItemsBuyer($buyer_quote_id);
		$buyer_items_count = count($buyer_items);
		$subscription  = DB::table('sellers')
		->where('sellers.user_id',Auth::user()->id)
		->select('sellers.subscription_end_date','sellers.subscription_start_date')
		->get();
		
		$row->cells[5]->value .= '<div class="table-data text-center">
		<div class="col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none margin-bottom table-row">
	
		<div class="col-md-2 padding-none text-left">'.$buyer_name.'
		<div class="red">
			<i class="fa fa-star"></i>
			<i class="fa fa-star"></i>
			<i class="fa fa-star"></i>
		</div>
		</div>
			
			
		<div class="col-md-3 padding-none mobile-padding-none text-left">'.CommonComponent::checkAndGetDate($dispatch_date_buyer).' - '.CommonComponent::checkAndGetDate($delivery_date_buyer).'</div>
		<div class="col-md-2 padding-none text-left  hidden-xs">'.CommonComponent::checkAndGetDate($bidenddate).'</div>
		<div class="col-md-3 padding-none text-left table-details table-details-right">'.$buyer_post_status.'</div>';
		if((Session::get ( 'service_id' )==ROAD_FTL)){
			$packageorvehicle =  "Vehicle Type";
			$quantitylable = "Qty";
		}else if((Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC) || (Session::get ( 'service_id' )==AIR_INTERNATIONAL)|| (Session::get ( 'service_id' )==OCEAN)) {
			$packageorvehicle =  "Packaging Type";
			$quantitylable = "No. Of Packages";
		}else if((Session::get ( 'service_id' )==RELOCATION_DOMESTIC)){
			$packageorvehicle =  "No of Shipments";
			$quantitylable = "Avg Volume/Shipment *";
		}
		if((Session::get ( 'service_id' )==COURIER)){
			$isQuoteSubmittd = TermSellerComponent::checkQuoteSubmitted($buyer_items,Session::get ( 'service_id' ),Auth::user ()->id,$buyer_quote_id);
			$isQuoteSaved    = TermSellerComponent::checkQuoteSubmitted($buyer_items,Session::get ( 'service_id' ),Auth::user ()->id,$buyer_quote_id,true);
		}else{
			$isQuoteSubmittd = TermSellerComponent::checkQuoteSubmitted($buyer_items,Session::get ( 'service_id' ),Auth::user ()->id,$buyer_quote_id);
			$isQuoteSaved    = TermSellerComponent::checkQuoteSubmitted($buyer_items,Session::get ( 'service_id' ),Auth::user ()->id,$buyer_quote_id,true);
		}
		$submitQuoteText = (($isQuoteSaved > 0) ? "Quote Saved" : (($isQuoteSubmittd == 0) ? "Submit Quote " : "Quote Submitted"));
		
		$documentname = CommonComponent::getBuyerBidDocumentsCheckingCount($buyer_quote_id);

		$downloadlink = '';
		if($documentname != ""){
			$downloadlink = '<div class="pull-right text-right margin-top">
								<a class="detailsslide-term data-head red" href="'.url('downloadbuyerbids/'.$buyer_quote_id).'" id="download_311">Download Bid Documents</a>
							</div>';
		}

		if($today < $bidDateTimes){
		$row->cells [5]->value .= '<div class="col-md-4 padding-none text-right pull-right"><span class="sellesearchdetails_list btn red-btn " id ='.$buyer_quote_id.' data-buyersearchlistid='.$buyer_id.'_'.$buyer_quote_id.'>'.$submitQuoteText.' <i class="fa fa-rupee"></i></span></div><div class="clearfix"></div><div class="pull-right text-right"><span class="sellesearchdetails_list detailsslide-3 show-data-link" data-buyersearchlistid='.$buyer_id.'_'.$buyer_quote_id.'>';
                    if(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){
                        $row->cells [5]->value .= '<span class="show_details" style="display: inline;">+</span><span class="hide_details" style="display: none;">-</span> Details</span> | ';
                    }                    
                $row->cells [5]->value .= '<a href="#" data-term="1" data-userid="'.$buyer_id.'" data-buyer-transaction="'.$transaction_id.'" class="new_message" data-buyerquoteitemidforseller="'.$buyer_quote_id.'"><i class="fa fa-envelope-o"></i></a></div>

		<div class="clearfix"></div>';
		}else{
		$row->cells [5]->value .= '<div class="col-md-4 padding-none text-right pull-right"><span class="sellesearchdetails_list btn red-btn " id ='.$buyer_quote_id.' data-buyersearchlistid='.$buyer_id.'_'.$buyer_quote_id.'>Bid Elapsed <i class="fa fa-rupee"></i></span></div><div class="clearfix"></div><div class="pull-right text-right"><span class="sellesearchdetails_list detailsslide-3 show-data-link" data-buyersearchlistid='.$buyer_id.'_'.$buyer_quote_id.'>';
                    if(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){
                         $row->cells [5]->value .= '<span class="show_details" style="display: inline;">+</span><span class="hide_details" style="display: none;">-</span> Details</span> | ';
                    }     
                $row->cells [5]->value .= '<a href="#" data-term="1" data-userid="'.$buyer_id.'" data-buyer-transaction="'.$transaction_id.'" class="new_message" data-buyerquoteitemidforseller="'.$buyer_quote_id.'"><i class="fa fa-envelope-o"></i></a></div>
			
		<div class="clearfix"></div>';

		}
		$row->cells [5]->value .= '<div class="col-md-12 col-sm-12 col-xs-12 padding-none pull-left tableslide table-slide-1 submit-data-div seller_listdetails_'.$buyer_id.'_'.$buyer_quote_id.'" style="display: none;">
		<input type="hidden" name ="buyer_items_count" id="buyer_items_count" value="0">
						<input type="hidden" name ="buyer_item_id" id="buyer_item_id" value="'.$buyer_quote_id.'">	
						<input type="hidden" name ="buyer_line_item_id" id="buyer_line_item_id" value="">		
							<div class="col-md-12 col-sm-12 col-xs-12 margin-bottom text-left">';

		if(Session::get ( 'service_id' )!=RELOCATION_DOMESTIC && Session::get ( 'service_id' )!=RELOCATION_INTERNATIONAL && Session::get ( 'service_id' )!=RELOCATION_GLOBAL_MOBILITY){

			$row->cells [5]->value .= '<div class="pull-left margin-top">
								<span class="data-head">Bid Type : '.$getBidType.'</span>
							</div>';
		}


		$row->cells [5]->value .= $downloadlink
							.'<div class="clearfix"></div>';
		if((Session::get ( 'service_id' )==ROAD_FTL) || (Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC) || (Session::get ( 'service_id' )==AIR_INTERNATIONAL)|| (Session::get ( 'service_id' )==OCEAN) || (Session::get ( 'service_id' )==COURIER)) {
			$row->cells [5]->value .= '<div class="table-heading inner-block-bg" width="100%">';
							if(Session::get ( 'service_id' )!=COURIER){
							$row->cells [5]->value .= '
								<div class="col-md-2 padding-left-none">From</div>
								<div class="col-md-2 padding-left-none">To</div>
								<div class="col-md-2 padding-left-none">Load Type</div>
								<div class="col-md-2 padding-left-none">' . $packageorvehicle . '</div>
								<div class="col-md-1 padding-left-none">' . $quantitylable . '</div>';
								}else{
							$row->cells [5]->value .= '
								<div class="col-md-3 padding-left-none">From</div>
								<div class="col-md-3 padding-left-none">To</div>
								<div class="col-md-3 padding-left-none">Volume</div>
								<div class="col-md-3 padding-left-none">No of Packages</div>';
								}
			if ($getBidType == "open") {
				$row->cells [5]->value .= '<div class="col-md-1 padding-left-none">Lowest Quote</div>';
			}

			if (Session::get('service_id') == ROAD_FTL) {
				$row->cells [5]->value .= "<div class='col-md-1 padding-left-none'>Quote</div>";
			} elseif ((Session::get('service_id') == ROAD_PTL) || (Session::get('service_id') == RAIL) || (Session::get('service_id') == AIR_DOMESTIC) || (Session::get('service_id') == AIR_INTERNATIONAL) || (Session::get('service_id') == OCEAN)) {
				$row->cells [5]->value .= "<div class='col-md-1 padding-left-none'>Rate/Kg</div>
										  <div class='col-md-1 padding-left-none'>Kg/CFT</div>";
			}
			$row->cells [5]->value .= '</div>';
		}else if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC){

			$row->cells [5]->value .= "<div class='table-heading inner-block-bg'>";
			if($ratecardType==1){
				$row->cells [5]->value .= "<div class='col-md-2 padding-left-none'>From</div>
									<div class='col-md-2 padding-left-none'>To</div>
									<div class='col-md-2 padding-left-none'>Avg Volume/Shipment</div>
									<div class='col-md-2 padding-left-none'>No of Shipments</div>
									<div class='col-md-2 padding-left-none'>Rate per CFT</div>
									<div class='col-md-2 padding-left-none'>Transit Days</div>";
			}else{

				$row->cells [5]->value .= "<div class='col-md-2 padding-left-none'>From</div>
									<div class='col-md-2 padding-left-none'>To</div>
									<div class='col-md-2 padding-left-none'>Vehicle Category</div>
									<div class='col-md-1 padding-left-none'>Vehicle Category Type</div>
									<div class='col-md-1 padding-left-none'>Vehicle Model</div>
									<div class='col-md-2 padding-left-none'>Transport Charges</div>
									<div class='col-md-1 padding-left-none'>O&D Charges</div>
									<div class='col-md-1 padding-left-none'>Transit Days</div>";
			}
			$row->cells [5]->value .= '</div>';




		}else if(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){
			
			if(Session::get ('session_term_service_type')==2){
				
				$row->cells [5]->value .= '<div class="table-heading inner-block-bg">
                                        <div class="col-md-3 padding-left-none">From Location</div>
                                        <div class="col-md-3 padding-left-none">To Location</div>
                                        <div class="col-md-3 padding-left-none">No of Moves</div>
                                        <div class="col-md-3 padding-left-noe">Average CBM/Move</div>
                                        </div>';
				for ($i = 0; $i < count($buyer_items); $i++) {
					$getbuyerquoteitemrelocation = DB::table('term_buyer_quote_items')
					->where('term_buyer_quote_items.id','=',$buyer_items[$i])
					->select('from_location_id','to_location_id','number_loads','avg_kg_per_move')
					->first();
					$from_city_name = CommonComponent::getCityName($getbuyerquoteitemrelocation->from_location_id);
					$to_city_name = CommonComponent::getCityName($getbuyerquoteitemrelocation->to_location_id);
					$row->cells [5]->value .='<div class="table-row inner-block-bg">
					<div class="col-md-3 padding-left-none">'.$from_city_name.'</div>
					<div class="col-md-3 padding-left-none">'.$to_city_name.'</div>
					<div class="col-md-3 padding-left-none">'.$getbuyerquoteitemrelocation->number_loads.'</div>
					<div class="col-md-3 padding-left-none">'.$getbuyerquoteitemrelocation->avg_kg_per_move.'</div>
					</div>';
				
				
				}
			$row->cells [5]->value .= "<div class='table-heading inner-block-bg'>";			
			$row->cells [5]->value .= "<div class='col-md-2 padding-left-none'>From</div>
									<div class='col-md-2 padding-left-none'>To</div>
									<div class='col-md-1 padding-left-none'>O & D LCL(per CBM)</div>
									<div class='col-md-1 padding-left-none'>O & D 20 FT (per CBM)</div>
                                    <div class='col-md-1 padding-left-none'>O & D 40 FT (per CBM)</div>
                                    <div class='col-md-1 padding-left-none'>Freight LCL (per CBM)</div>
                                    <div class='col-md-1 padding-left-none'>Freight FCL 20 FT (Flat)</div>
                                    <div class='col-md-1 padding-left-none'>Freight FCL 40 FT (Flat)</div>
                                    <div class='col-md-1 padding-left-none'>Transit Days</div>";			
			$row->cells [5]->value .= '</div>';
			}else{
				
				$row->cells [5]->value .= '<div class="table-heading inner-block-bg">
                                        <div class="col-md-3 padding-left-none">From Location</div>
                                        <div class="col-md-3 padding-left-none">To Location</div>
                                        <div class="col-md-3 padding-left-none">No of Moves</div>
                                        <div class="col-md-3 padding-left-noe">Average kg/Move</div>
                                        </div>';
				for ($i = 0; $i < count($buyer_items); $i++) {
					$getbuyerquoteitemrelocation = DB::table('term_buyer_quote_items')
					->where('term_buyer_quote_items.id','=',$buyer_items[$i])
					->select('from_location_id','to_location_id','number_loads','avg_kg_per_move')
					->first();
					$from_city_name = CommonComponent::getCityName($getbuyerquoteitemrelocation->from_location_id);
					$to_city_name = CommonComponent::getCityName($getbuyerquoteitemrelocation->to_location_id);
					$row->cells [5]->value .='<div class="table-row inner-block-bg">
					<div class="col-md-3 padding-left-none">'.$from_city_name.'</div>
					<div class="col-md-3 padding-left-none">'.$to_city_name.'</div>
					<div class="col-md-3 padding-left-none">'.$getbuyerquoteitemrelocation->number_loads.'</div>
					<div class="col-md-3 padding-left-none">'.$getbuyerquoteitemrelocation->avg_kg_per_move.'</div>
					</div>';
				
				
				}
			$row->cells [5]->value .= "<div class='table-heading inner-block-bg'>";
			$row->cells [5]->value .= "<div class='col-md-2 padding-left-none arrow-down'>From</div>
                 <div class='col-md-2 padding-left-none arrow-down'>To</div>
                 <div class='col-md-2 padding-left-none'>Freight Charges Upto 100 KG</div>
                 <div class='col-md-2 padding-left-none'>Freight Charges Upto 300 KG</div>
				 <div class='col-md-2 padding-left-none'>Freight Charges Upto 500 KG</div>
                 <div class='col-md-1 padding-left-none'>O & D Charges (per CFT)</div>
                 <div class='col-md-1 padding-left-none'>Transit Days</div>";
			$row->cells [5]->value .= '</div>';
			}

		}elseif(Session::get ( 'service_id' )==RELOCATION_GLOBAL_MOBILITY){
			
			$row->cells [5]->value .= "
				<div class='table-heading inner-block-bg'>
				<div class='col-md-3 padding-left-none'>From</div>
                 <div class='col-md-3 padding-left-none'>Service</div>
                 <div class='col-md-3 padding-left-none'>Numbers</div>
                 <div class='col-md-3 padding-left-none'>Rate</div>";
			
		}
		if(Session::get ( 'service_id' )==ROAD_FTL) {
								for ($i = 0; $i < count($buyer_items); $i++) {
									$getbuyerquoteitems = DB::table('term_buyer_quote_items')
										->where('term_buyer_quote_items.id', '=', $buyer_items[$i])
										->select('from_location_id', 'to_location_id', 'lkp_load_type_id', 'lkp_vehicle_type_id', 'quantity')
										->first();

									$initialQuotePriceDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
										->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_item_id', '=', $buyer_items[$i])
										->where('term_buyer_quote_sellers_quotes_prices.seller_id','=',$userId)
										->select('initial_quote_price', 'is_submitted')
										->first();


									if (!empty($initialQuotePriceDisplay)) {
										$initialQuotePrice = $initialQuotePriceDisplay->initial_quote_price;
									} else {
										$initialQuotePrice = "";
									}
									$load_type_name = CommonComponent::getLoadType($getbuyerquoteitems->lkp_load_type_id);
									$vehicle_type_name = CommonComponent::getVehicleType($getbuyerquoteitems->lkp_vehicle_type_id);
									$from_city_name = CommonComponent::getCityName($getbuyerquoteitems->from_location_id);
									$to_city_name = CommonComponent::getCityName($getbuyerquoteitems->to_location_id);
									$initial_quote_price_price = CommonComponent::getLowestQuote($buyer_items[$i]);
									$row->cells [5]->value .= '<div class="table-data"> <div class="table-row inner-block-bg" width="100%">
											<div class="col-md-2 padding-left-none">
												<input type="checkbox" name="lineitem_checkbox" id="term_lineitem_' . $buyer_items[$i] . '" class="lineitem_checkbox" onchange="javascript:checkSellerPostitem(this.id)">
												<span class="lbl padding-8"></span>' . $from_city_name . '
											</div>
											<div class="col-md-2 padding-left-none">' . $to_city_name . '</div>
											<div class="col-md-2 padding-left-none">' . $load_type_name . '</div>
											<div class="col-md-2 padding-left-none">' . $vehicle_type_name . '</div>
											<div class="col-md-1 padding-left-none">' . $getbuyerquoteitems->quantity . '</div>';
									if ($getBidType == 'Open') {
										if (empty($initial_quote_price_price) && $initial_quote_price_price == '') {
											$row->cells [5]->value .= '<div class="col-md-1 padding-left-none">--</div>';
										} else {
											$row->cells [5]->value .= '<div class="col-md-1 padding-left-none">' . CommonComponent::getPriceType($initial_quote_price_price) . '</div>';
										}
									}
									$row->cells [5]->value .= '<div class="col-md-1 padding-left-none"><input class="form-control form-control1 clsFTLTQuote" id ="intialquote_' . $buyer_items[$i] . '" name = "intialquote_' . $buyer_items[$i] . '" type="text" value ="' . $initialQuotePrice . '" disabled>
											</div></div>
											<div class="clearfix"></div>
											</div>';
								}
							}elseif((Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC)|| (Session::get ( 'service_id' )==AIR_INTERNATIONAL)|| (Session::get ( 'service_id' )==OCEAN) || (Session::get ( 'service_id' )==COURIER)) {
								for ($i = 0; $i < count($buyer_items); $i++) {
									$getbuyerquoteitems = DB::table('term_buyer_quote_items')
										->leftjoin('term_buyer_quotes','term_buyer_quote_items.term_buyer_quote_id','=','term_buyer_quotes.id')
										->where('term_buyer_quote_items.id', '=', $buyer_items[$i])
										->select('term_buyer_quotes.lkp_courier_delivery_type_id','from_location_id','volume','to_location_id', 'lkp_load_type_id', 'lkp_vehicle_type_id', 'quantity','lkp_packaging_type_id','number_packages')
										->first();

									$initialQuotePriceDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
										->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_item_id', '=', $buyer_items[$i])
										->where('term_buyer_quote_sellers_quotes_prices.seller_id','=',$userId)
										->select('initial_quote_price', 'is_submitted','initial_rate_per_kg','initial_kg_per_cft')
										->first();

									if (!empty($initialQuotePriceDisplay)) {
										$initialRateperKG = $initialQuotePriceDisplay->initial_rate_per_kg;
										$initialKgperCFT = $initialQuotePriceDisplay->initial_kg_per_cft;
									} else {
										$initialRateperKG = '';
										$initialKgperCFT = "";
									}
									$load_type_name = CommonComponent::getLoadType($getbuyerquoteitems->lkp_load_type_id);
									$package_type_name = CommonComponent::getPackageType($getbuyerquoteitems->lkp_packaging_type_id);
									
									if((Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC)){
										$from_city_name = CommonComponent::getPinName($getbuyerquoteitems->from_location_id);
										$to_city_name = CommonComponent::getPinName($getbuyerquoteitems->to_location_id);
									}else if((Session::get ( 'service_id' )==COURIER)){
										$from_city_name = CommonComponent::getPinName($getbuyerquoteitems->from_location_id);
										if($getbuyerquoteitems->lkp_courier_delivery_type_id==1)
											$to_city_name = CommonComponent::getPinName($getbuyerquoteitems->to_location_id);
										else 
											$to_city_name = CommonComponent::getCountry($getbuyerquoteitems->to_location_id);
									}else if(Session::get ( 'service_id' )==AIR_INTERNATIONAL){
										$from_city_name = CommonComponent::getAirportName($getbuyerquoteitems->from_location_id);
										$to_city_name = CommonComponent::getAirportName($getbuyerquoteitems->to_location_id);
									}else if(Session::get ( 'service_id' )==OCEAN){
										$from_city_name = CommonComponent::getSeaportName($getbuyerquoteitems->from_location_id);
										$to_city_name = CommonComponent::getSeaportName($getbuyerquoteitems->to_location_id);
									}


									$initial_quote_price_price = CommonComponent::getLowestQuote($buyer_items[$i]);
									$row->cells [5]->value .= '<div class="table-data"></div> <div class="table-row inner-block-bg" width="100%">';
									
									if((Session::get ( 'service_id' )!=COURIER)){
										$row->cells [5]->value .= '
										<div class="col-md-2 padding-left-none"><input onchange="javascript:checkSellerPostitem(this.id)" type="checkbox" name="lineitem_checkbox" id="term_lineitem_' . $buyer_items[$i] . '" class="lineitem_checkbox"><span class="lbl padding-8"></span>' . $from_city_name . '</div>
										<div class="col-md-2 padding-left-none">' . $to_city_name . '</div>
										<div class="col-md-2 padding-left-none">' . $load_type_name . '</div>
										<div class="col-md-2 padding-left-none">' . $package_type_name . '</div>
										<div class="col-md-1 padding-left-none">' . $getbuyerquoteitems->number_packages . '</div>';
									}else{
										$row->cells [5]->value .= '
										<div class="col-md-3 padding-left-none">' . $from_city_name . '</div>
										<div class="col-md-3 padding-left-none">' . $to_city_name . '</div>
										<div class="col-md-3 padding-left-none">' . $getbuyerquoteitems->volume . '</div>
										<div class="col-md-3 padding-left-none">' . $getbuyerquoteitems->number_packages . '</div>';
									}
									if((Session::get ( 'service_id' )!=COURIER)){
										if ($getBidType == 'Open') {
											if (empty($initial_quote_price_price) && $initial_quote_price_price == '') {
												$row->cells [5]->value .= '<div class="col-md-1 padding-left-none">--</div>';
											} else {
												$row->cells [5]->value .= '<div class="col-md-1 padding-left-none">' . $initial_quote_price_price . '</div>';
											}
										}
									}
									if((Session::get ( 'service_id' )!=COURIER)){
									$row->cells [5]->value .= '
											<div class="col-md-1 padding-left-none">
												<div class="input-prepend">
													<input class="form-control form-control1 termMin2d fourdigitstwodecimals_deciVal   numberVal " id ="initial_rate_per_kg_' . $buyer_items[$i] . '" name = "initial_rate_per_kg_' . $buyer_items[$i] . '" type="text" value ="' . $initialRateperKG . '" disabled>
												</div>
											</div>
											<div class="col-md-1 padding-left-none">
												<div class="input-prepend">';
												if(Session::get ( 'service_id' ) ==  AIR_INTERNATIONAL || Session::get ( 'service_id' ) ==  AIR_DOMESTIC)
													$row->cells [5]->value .= '<input class="form-control form-control1  numberVal termMin4d fourdigitsfourdecimals_deciVal " id ="initial_kg_per_cft_' . $buyer_items[$i] . '" name = "initial_kg_per_cft_' . $buyer_items[$i] . '" type="text" value ="' . $initialKgperCFT . '" disabled>';
												else
													$row->cells [5]->value .= '<input class="form-control form-control1  numberVal fourdigitsthreedecimals_deciVal" id ="initial_kg_per_cft_' . $buyer_items[$i] . '" name = "initial_kg_per_cft_' . $buyer_items[$i] . '" type="text" value ="' . $initialKgperCFT . '" disabled>';
												$row->cells [5]->value .= '</div>
											</div>';
									}
									$row->cells [5]->value .= '<div class="clearfix"></div>';
								
								
								
									
									$row->cells [5]->value .= '</div>';
									
								}
								
								
							}else if(Session::get ( 'service_id' )==RELOCATION_DOMESTIC) {
								for ($i = 0; $i < count($buyer_items); $i++) {
									$getbuyerquoteitems = DB::table('term_buyer_quote_items')
										->where('term_buyer_quote_items.id', '=', $buyer_items[$i])
										->select('from_location_id', 'to_location_id', 'number_packages', 'volume','lkp_vehicle_category_id','lkp_vehicle_category_type_id','vehicle_model')
										->first();

									$initialQuotePriceDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
										->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_item_id','=',$buyer_items[$i])
										->where('term_buyer_quote_sellers_quotes_prices.seller_id','=',$userId)
										->select('rate_per_cft','transport_charges','odcharges','transit_days','crating_charges','storage_charges','escort_charges','handyman_charges','property_charges','brokerage_charge','is_submitted')
										->first();


									if(!empty($initialQuotePriceDisplay)){
										if(Session::get ( 'service_id' )==ROAD_FTL){
											$initialQuotePrice = $initialQuotePriceDisplay->initial_quote_price;
										}else{
											$initialQuotePrice = $initialQuotePriceDisplay->rate_per_cft;
											$transport_charges = $initialQuotePriceDisplay->transport_charges;
											$odcharges = $initialQuotePriceDisplay->odcharges;
											$transitdays = $initialQuotePriceDisplay->transit_days;
											$crating = $initialQuotePriceDisplay->crating_charges;
											$storage = $initialQuotePriceDisplay->storage_charges;
											$escort =  $initialQuotePriceDisplay->escort_charges;
											$handyman = $initialQuotePriceDisplay->handyman_charges;
											$property = $initialQuotePriceDisplay->property_charges;
											$brokerage = $initialQuotePriceDisplay->brokerage_charge;
										}
									}else{
										$initialQuotePrice="";
										$transport_charges = "";
										$odcharges = "";
										$transitdays = "";
										$crating = '';
										$storage = '';
										$escort =  '';
										$handyman = '';
										$property = '';
										$brokerage = '';
									}

									$from_city_name = CommonComponent::getCityName($getbuyerquoteitems->from_location_id);
									$to_city_name = CommonComponent::getCityName($getbuyerquoteitems->to_location_id);
									$initial_quote_price_price = CommonComponent::getLowestQuote($buyer_items[$i]);
									$row->cells [5]->value .= '<div class="table-data"> <div class="table-row inner-block-bg" width="100%">
											<div class="col-md-2 padding-left-none"><input type="checkbox" name="lineitem_checkbox" id="term_lineitem_' . $buyer_items[$i] .'_'. $buyer_quote_id. '" class="lineitem_checkbox" onchange="javascript:checkSellerPostitem(this.id)"><span class="lbl padding-8"></span>' . $from_city_name . '</div>
											<div class="col-md-2 padding-left-none">' . $to_city_name . '</div>';
									if($ratecardType==1) {
										$row->cells [5]->value .= '<div class="col-md-2 padding-left-none">' . $getbuyerquoteitems->volume . '</div>
												<div class="col-md-2 padding-left-none">' . $getbuyerquoteitems->number_packages . '</div>';
										if ($getSaveValue) {
											$row->cells [5]->value .= "<div class='col-md-2 padding-left-none'><input class='form-control form-control1 fourdigitstwodecimals_deciVal numberVal' id ='rateper_kg_" . $buyer_items[$i] . "' value ='" . $initialQuotePrice . "' name = 'rateper_kg_" . $buyer_items[$i] . "' type='text' disabled></div>
											<div class='col-md-2 padding-left-none'><input class='form-control form-control1 numericvalidation' maxlength = '3' id ='transit_days_" . $buyer_items[$i] . "' value ='" . $transitdays . "' name = 'transit_days_" . $buyer_items[$i] . "' type='text' disabled></div>
											<input type='hidden' name='item_id' id='item_id_" . $buyer_items[$i] . "' value='$buyer_items[$i]'>";
										} else {
											$row->cells [5]->value .= "<div class='col-md-2 padding-left-none'><input class='form-control fourdigitstwodecimals_deciVal numberVal form-control1' id ='rateper_kg_" . $buyer_items[$i] . "' value ='" . $initialQuotePrice . "' name = 'rateper_kg_" . $buyer_items[$i] . "' type='text' disabled></div>
											<div class='col-md-2 padding-left-none'><input class='form-control form-control1 numericvalidation' maxlength = '3' id ='transit_days_" . $buyer_items[$i] . "' value ='" . $transitdays . "' name = 'transit_days_" . $buyer_items[$i] . "' type='text' disabled></div>
											<input type='hidden' name='item_id' id='item_id_" . $buyer_items[$i] . "' value='$buyer_items[$i]'>";
										}
									}else{
										
										if($getbuyerquoteitems->lkp_vehicle_category_id==1){
											
										 $type_cat_id=CommonComponent::getVehicleCategoryById($getbuyerquoteitems->lkp_vehicle_category_type_id);	
										}else{
										 $type_cat_id='N/A';
										}
										$row->cells [5]->value .= '<div class="col-md-2 padding-left-none">' . CommonComponent::getVehicleCategoryById($getbuyerquoteitems->lkp_vehicle_category_id) . '</div>
												<div class="col-md-1 padding-left-none">' . $type_cat_id . '</div>
												<div class="col-md-1 padding-left-none">' . $getbuyerquoteitems->vehicle_model . '</div>';

										if ($getSaveValue) {
											$row->cells [5]->value .= "<div class='col-md-2 padding-left-none'><input class='form-control fourdigitstwodecimals_deciVal numberVal form-control1' id ='transport_charges_" . $buyer_items[$i] . "' value ='" . $transport_charges . "' name = 'transport_charges_" . $buyer_items[$i] . "' type='text' disabled></div>
												<div class='col-md-1 padding-left-none'><input class='form-control fourdigitstwodecimals_deciVal numberVal form-control1' id ='od_charges_" . $buyer_items[$i] . "' value ='" . $odcharges . "' name = 'od_charges_" . $buyer_items[$i] . "' type='text' disabled></div>
												<div class='col-md-1 padding-left-none'><input class='form-control numericvalidation form-control1' maxlength = '3' id ='transit_days_" . $buyer_items[$i] . "' value ='" . $transitdays . "' name = 'transit_days_" . $buyer_items[$i] . "' type='text' disabled></div>
												<input type='hidden' name='item_id' id='item_id_" . $buyer_items[$i] . "' value='$buyer_items[$i]'>";
										} else {
											$row->cells [5]->value .= "<div class='col-md-2 padding-left-none'><input class='form-control fourdigitstwodecimals_deciVal numberVal form-control1' id ='transport_charges_" . $buyer_items[$i] . "' value ='" . $transport_charges . "' name = 'transport_charges_" . $buyer_items[$i] . "' type='text' disabled></div>
											<div class='col-md-1 padding-left-none'><input class='form-control fourdigitstwodecimals_deciVal numberVal form-control1' id ='od_charges_" . $buyer_items[$i] . "' value ='" . $odcharges . "' name = 'od_charges_" . $buyer_items[$i] . "' type='text' disabled></div>
											<div class='col-md-1 padding-left-none'><input class='form-control numericvalidation form-control1' maxlength = '3' id ='transit_days_" . $buyer_items[$i] . "' value ='" . $transitdays . "' name = 'transit_days_" . $buyer_items[$i] . "' type='text' disabled></div>
											<input type='hidden' name='item_id' id='item_id_" . $buyer_items[$i] . "' value='$buyer_items[$i]'>";
										}

									}
									if($ratecardType==1) {
										$row->cells [5]->value .= '<div class="col-md-12 padding-none filter">
																		<h2 class="filter-head1 margin-bottom">Additional Charges</h2>
																		<div class="col-md-3 form-control-fld">
																			<div class="input-prepend">
																				<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="crating_charges_' . $buyer_quote_id . '" placeholder="Crating Charges per CFT*" name="crating_charges_' . $buyer_quote_id . '" value="' . $crating . '" type="text" disabled>
																			</div>
																		</div>
																		<div class="col-md-3 form-control-fld">
																			<div class="input-prepend">
																				<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="storate_charges_' . $buyer_quote_id . '" placeholder="Storage Charges CFT per Day*" name="storate_charges_' . $buyer_quote_id . '" value="' . $storage . '" type="text" disabled>
																			</div>
																		</div>
																		<div class="col-md-3 form-control-fld">
																			<div class="input-prepend">
																				<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="escort_charges_' . $buyer_quote_id . '" placeholder="Escort Charges per Day*" name="escort_charges_' . $buyer_quote_id . '" value="' . $escort . '" type="text" disabled>
																			</div>
																		</div>
																		<div class="col-md-3 form-control-fld">
																			<div class="input-prepend">
																				<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="handyman_charges_' . $buyer_quote_id . '" placeholder="Handyman Charges per Hour*" name="handyman_charges_' . $buyer_quote_id . '" value="' . $handyman . '" type="text" disabled>
																			</div>
																		</div>
																		<div class="clearfix"></div>
																		<div class="col-md-3 form-control-fld">
																			<div class="input-prepend">
																				<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="property_search_' . $buyer_quote_id . '" placeholder="Property Search Rs*" name="property_search_' . $buyer_quote_id . '" value="' . $property . '" type="text" disabled>
																			</div>
																		</div>
																		<div class="col-md-3 form-control-fld">
																			<div class="input-prepend">
																				<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="brokerage_' . $buyer_quote_id . '" placeholder="Brokerage Rs*" name="brokerage_' . $buyer_quote_id . '" value="' . $brokerage . '" type="text" disabled>
																			</div>
																		</div>

																	</div>';
									}else{
										$row->cells [5]->value .= '<div class="col-md-12 padding-none filter">
																		<h2 class="filter-head1 margin-bottom">Additional Charges</h2>
																		<div class="col-md-3 form-control-fld">
																			<div class="input-prepend">
																				<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" id="storate_charges_' . $buyer_quote_id . '" placeholder="Storage Charges CFT per Day*" name="storate_charges_' . $buyer_quote_id . '" value="' . $storage . '" type="text" disabled>
																			</div>
																		</div>
																	</div>';
									}

									//$row->cells [5]->value .= '<div class="col-md-2 padding-left-none"><input class="form-control form-control1" id ="intialquote_' . $buyer_items[$i] . '" name = "intialquote_' . $buyer_items[$i] . '" type="text" value ="' . $initialQuotePrice . '" disabled></div>';
									//$row->cells [5]->value .= '<div class="col-md-2 padding-left-none"><input class="form-control form-control1" id ="intialquote_' . $buyer_items[$i] . '" name = "intialquote_' . $buyer_items[$i] . '" type="text" value ="' . $initialQuotePrice . '" disabled></div>';


									$row->cells [5]->value .= '</div>
											<div class="clearfix"></div>
											</div>';
								}
							}else if(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL) {
								for ($i = 0; $i < count($buyer_items); $i++) {
									$getbuyerquoteitems = DB::table('term_buyer_quote_items')
										->where('term_buyer_quote_items.id', '=', $buyer_items[$i])
										->select('from_location_id', 'to_location_id', 'lkp_load_type_id', 'lkp_vehicle_type_id', 'quantity')
										->first();

									$initialQuotePriceDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
										->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_item_id', '=', $buyer_items[$i])
										->where('term_buyer_quote_sellers_quotes_prices.seller_id','=',$userId)
										->select('initial_quote_price','rate_per_cft','transport_charges','odcharges','transit_days','crating_charges','storage_charges','escort_charges','handyman_charges','property_charges','brokerage_charge','is_submitted',
										'fright_hundred','fright_three_hundred','fright_five_hundred','odlcl_charges','odtwentyft_charges','odfortyft_charges','frieghtlcl_charges','frieghttwentft_charges','frieghtfortyft_charges')
										->first();
											
									
									if (!empty($initialQuotePriceDisplay)) {
										$initialQuotePrice = $initialQuotePriceDisplay->initial_quote_price;
										$initialTransitDays = $initialQuotePriceDisplay->transit_days;
										$initialtransport = $initialQuotePriceDisplay->transport_charges;
										$initialod = $initialQuotePriceDisplay->odcharges;
										$crating = $initialQuotePriceDisplay->crating_charges;
										$storage = $initialQuotePriceDisplay->storage_charges;
										$frighthndered = $initialQuotePriceDisplay->fright_hundred;
										$frightthreehundred = $initialQuotePriceDisplay->fright_three_hundred;
										$frightfivehundred = $initialQuotePriceDisplay->fright_five_hundred;
										$odlclcharges = $initialQuotePriceDisplay->odlcl_charges;
										$odlcltwentycharges = $initialQuotePriceDisplay->odtwentyft_charges;
										$odlclfortycharges = $initialQuotePriceDisplay->odfortyft_charges;
										$frieghtlclcharges = $initialQuotePriceDisplay->frieghtlcl_charges;
										$frieghttwentylclcharges = $initialQuotePriceDisplay->frieghttwentft_charges;
										$frieghtfortylclcharges = $initialQuotePriceDisplay->frieghtfortyft_charges;
									} else {
										$initialQuotePrice = "";
										$initialod="";
										$initialTransitDays="";
										$crating = '';
										$storage = '';
										$frighthndered = '';
										$frightthreehundred = '';
										$frightfivehundred = '';
										$odlclcharges = '';
										$odlcltwentycharges = '';
										$odlclfortycharges = '';
										$frieghtlclcharges = '';
										$frieghttwentylclcharges = '';
										$frieghtfortylclcharges = '';
									}									
									
									$from_city_name = CommonComponent::getCityName($getbuyerquoteitems->from_location_id);
									$to_city_name = CommonComponent::getCityName($getbuyerquoteitems->to_location_id);
									$initial_quote_price_price = CommonComponent::getLowestQuote($buyer_items[$i]);
									$row->cells [5]->value .= '<div class="table-data"> 
											<div class="table-row inner-block-bg" width="100%">
											<div class="col-md-2 padding-left-none">
												<input type="checkbox" name="lineitem_checkbox" id="term_lineitem_' .$buyer_items[$i] .'_'.$buyer_quote_id.'" class="lineitem_checkbox" onchange="javascript:checkSellerPostitem(this.id)">
												<span class="lbl padding-8"></span>' . $from_city_name . '
											</div>
											<div class="col-md-2 padding-left-none">' . $to_city_name . '</div>';
									if(Session::get ('session_term_service_type')==2){
									$row->cells [5]->value .= '
											<div class="col-md-1 padding-left-none">
											<input type="text" id ="odlcl_charges_'.$buyer_items[$i].'" name ="odlcl_charges_'.$buyer_items[$i].'" value="'.$odlclcharges.'" class="form-control form-control1 clsRIATODChargespCFT numberVal" disabled>
											</div>
											<div class="col-md-1 padding-left-none">
											<input type="text" id ="odtwentyft_charges_'.$buyer_items[$i].'" name ="odtwentyft_charges_'.$buyer_items[$i].'" value="'.$odlcltwentycharges.'" class="form-control form-control1 clsRIATODChargespCFT numberVal" disabled>
											</div>
											<div class="col-md-1 padding-left-none">
											<input type="text" id ="odfortyft_charges_'.$buyer_items[$i].'" name ="odfortyft_charges_'.$buyer_items[$i].'" value="'.$odlclfortycharges.'" class="form-control form-control1 clsRIATODChargespCFT numberVal" disabled>
											</div>
											<div class="col-md-1 padding-left-none">
											<input type="text" id ="frieghtlcl_charges_'.$buyer_items[$i].'" name ="frieghtlcl_charges_'.$buyer_items[$i].'" value="'.$frieghtlclcharges.'" class="form-control form-control1 clsRIATFreightChargespKG numberVal" disabled>
											</div>
											<div class="col-md-1 padding-left-none">
											<input type="text" id ="frieghttwenty_charges_'.$buyer_items[$i].'" name ="frieghttwenty_charges_'.$buyer_items[$i].'" value="'.$frieghttwentylclcharges.'" class="form-control form-control1 clsRIATFreightChargespKG numberVal" disabled>
											</div>
											<div class="col-md-1 padding-left-none">
											<input type="text" id ="frieghtforty_charges_'.$buyer_items[$i].'" name ="frieghtforty_charges_'.$buyer_items[$i].'" value="'.$frieghtfortylclcharges.'" class="form-control form-control1 clsRIATFreightChargespKG numberVal" disabled>
											</div>
											<div class="col-md-1 padding-left-none">
											<input type="text" id ="transit_days_'.$buyer_items[$i].'" name ="transit_days_'.$buyer_items[$i].'" value="'.$initialTransitDays.'" class="form-control form-control1 clsRIATTransitDays" disabled>
											';
									}else{
										$row->cells [5]->value .= '
										<div class="col-md-2 padding-left-none">
										<input type="text" id ="frieghthundred_charges_'.$buyer_items[$i].'" name ="frieghthundred_charges_'.$buyer_items[$i].'" value="'.$frighthndered.'" class="form-control form-control1 clsRIATFreightChargespKG numberVal" disabled>
										</div>
										<div class="col-md-2 padding-left-none">
										<input type="text" id ="frieghtthreehundred_charges_'.$buyer_items[$i].'" name ="frieghtthreehundred_charges_'.$buyer_items[$i].'" value="'.$frightthreehundred.'" class="form-control form-control1 clsRIATFreightChargespKG numberVal" disabled>
										</div>
										<div class="col-md-2 padding-left-none">
										<input type="text" id ="frieghtfivehundred_charges_'.$buyer_items[$i].'" name ="frieghtfivehundred_charges_'.$buyer_items[$i].'" value="'.$frightfivehundred.'" class="form-control form-control1 clsRIATFreightChargespKG numberVal" disabled>
										</div>
										<div class="col-md-1 padding-left-none">
										<input type="text" id ="od_charges_'.$buyer_items[$i].'" name ="od_charges_'.$buyer_items[$i].'" value="'.$initialod.'" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" disabled>
										</div>
										<div class="col-md-1 padding-left-none">
										<input type="text" id ="transit_days_'.$buyer_items[$i].'" name ="transit_days_'.$buyer_items[$i].'" value="'.$initialTransitDays.'" class="form-control form-control1 clsRIATTransitDays" disabled>
										</div>';
									}
									$row->cells [5]->value .= '</div>
									</div>
									<div class="clearfix"></div>';
									
								
									}
								}
								if(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL){
								$initialQuotestorageDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
								->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_id', '=', $buyer_quote_id)
								->where('term_buyer_quote_sellers_quotes_prices.seller_id','=',$userId)
								->select('initial_quote_price','rate_per_cft','transport_charges','odcharges','transit_days','crating_charges','storage_charges','escort_charges','handyman_charges','property_charges','brokerage_charge','is_submitted',
										'fright_hundred','fright_three_hundred','fright_five_hundred','odlcl_charges','odtwentyft_charges','odfortyft_charges','frieghtlcl_charges','frieghttwentft_charges','frieghtfortyft_charges')
										->first();
									
								if (!empty($initialQuotestorageDisplay)) {
									
									$crating_single = $initialQuotestorageDisplay->crating_charges;
									$storage_single = $initialQuotestorageDisplay->storage_charges;
									
								} else {
									
									$crating_single = '';
									$storage_single = '';
									
								}
								if(Session::get ('session_term_service_type')==2){
									
									
									$row->cells [5]->value .='
								<div class="col-md-4 padding-left-none">
									<input type="text" name="crating_charges_'.$buyer_quote_id.'" id="crating_charges_'.$buyer_quote_id.'" placeholder="Crating Charges (per CFT)" value="'.$crating_single.'" class="form-control form-control1 clsRIATStorageCharges numberVal" disabled>
								</div>
								<div class="clearfix"></div>
								<div class="col-md-12 form-control-fld">
		                        	<span class="data-head">Additional Charges</span>
		                        </div>
								<div class="clearfix"></div>
								<div class="col-md-4 padding-left-none">
									<input type="text" name="storate_charges_'.$buyer_quote_id.'" id="storate_charges_'.$buyer_quote_id.'"  placeholder="Storage Charges" value="'.$storage_single.'" class="form-control form-control1 clsRIATStorageCharges numberVal" disabled>
								</div>';
								}else{
									$row->cells [5]->value .='
								<div class="clearfix"></div>			
								<div class="col-md-12 form-control-fld left-none">
		                        	<span class="data-head">Additional Charges</span>
		                        </div>
								<div class="clearfix"></div>
								<div class="col-md-4 padding-left-none">
									<input type="text" name="storate_charges_'.$buyer_quote_id.'" id="storate_charges_'.$buyer_quote_id.'" placeholder="Storage Charges" value="'.$storage_single.'" class="form-control form-control1 clsRIATStorageCharges numberVal" disabled>
								</div>';
								
                              
							}
							}
							if(Session::get ( 'service_id' )==RELOCATION_GLOBAL_MOBILITY){
								for($i=0;$i<count($buyer_items);$i++){
									$getbuyerquoteitems = DB::table('term_buyer_quote_items')
									->where('term_buyer_quote_items.id','=',$buyer_items[$i])
									->select('from_location_id','lkp_gm_service_id','measurement','measurement_units')
									->first();
							
									$initialQuotePriceDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
									->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_item_id','=',$buyer_items[$i])
									->where('term_buyer_quote_sellers_quotes_prices.seller_id','=',$userId)
									->select('initial_quote_price','is_submitted')
									->first();
									if(!empty($initialQuotePriceDisplay)){
										$initialQuotePrice = $initialQuotePriceDisplay->initial_quote_price;
											
									}else{
										$initialQuotePrice = "";
											
									}
							
									$from_city_name = CommonComponent::getCityName($getbuyerquoteitems->from_location_id);
									$service_gm_name = CommonComponent::getAllGMServiceTypesById($getbuyerquoteitems->lkp_gm_service_id);
									$row->cells [5]->value .="<div class='table-row inner-block-bg'>
									<div class='col-md-3 padding-left-none'>
									<input type='checkbox' name='lineitem_checkbox' id='term_lineitem_$buyer_items[$i]' class='lineitem_checkbox' onchange='javascript:checkSellerPostitem(this.id)'><span class='lbl padding-8'></span>$from_city_name
									</div>
									<div class='col-md-3 padding-left-none'>$service_gm_name</div>
									<div class='col-md-3 padding-left-none'>$getbuyerquoteitems->measurement $getbuyerquoteitems->measurement_units</div>
									<div class='col-md-3 padding-left-none'>
									<input class='form-control form-control1  clsGMTRatepService' id ='intialquote_".$buyer_items[$i]."' value ='".$initialQuotePrice."' name = 'intialquote_".$buyer_items[$i]."' type='text' disabled>
									</div>
									</div>";
								}
							
							}
							if((Session::get ( 'service_id' )==COURIER)){
								$quoteprices = CommonComponent::getQuotePriceDetails($buyer_quote_id,$buyer_id);
								if(count($quoteprices)==0){
									$maxweight = CommonComponent::getMaxWeightUnits($buyer_quote_id,$buyer_id);
									$slabslist = CommonComponent::getSlabs($buyer_quote_id,$buyer_id);
									$row->cells [5]->value .= '
											
										
										<div class="col-md-12 inner-block-bg inner-block-bg1 ">
											<div class="col-md-3 form-control-fld">
												<span class="data-value">Maximum Weight: '.$maxweight[0]->max_weight_accepted." ".CommonComponent::getWeight($maxweight[0]->lkp_ict_weight_uom_id).'</span>
											</div>
											<div class="col-md-12 padding-none">
												<div class="col-md-12 padding-none">
													<!-- Table Starts Here -->
													<div class="table-div table-style1">
														<!-- Table Head Starts Here -->
														<div class="table-heading inner-block-bg">
															<div class="col-md-3 padding-left-none">Min</div>
															<div class="col-md-3 padding-left-none">Max</div>
															<div class="col-md-3 padding-left-none">Quote</div>
														</div>
														<!-- Table Head Ends Here -->
														<div class="table-data form-control-fld padding-none">
														<!-- Table Row Starts Here -->';
														
														$incrementvalue=0;
														if(count($slabslist)>0){
															foreach($slabslist as $slabsitem){
																$incrementvalue = $incrementvalue + 1;
																$row->cells [5]->value .= '
																<div class="table-row inner-block-bg">
																	<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_min_rate.'</div>
																	<input name="slab_min_'.$incrementvalue.'_' . $buyer_quote_id . '" value="'.$slabsitem->slab_min_rate.'" type="hidden">
																	<input name="slab_max_'.$incrementvalue.'_' . $buyer_quote_id . '" value="'.$slabsitem->slab_max_rate.'" type="hidden">
																	<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_max_rate.'</div>
																	<div class="col-md-3 padding-left-none">';
																	$slabslist = CommonComponent::getQuotePriceDetailsSlabs($buyer_quote_id,$buyer_id);
																		$row->cells [5]->value .= '<input class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" name="slab_'.$incrementvalue.'_' . $buyer_quote_id . '" placeholder="" type="text">';
																	$row->cells [5]->value .= '</div>
																	<div class="col-md-1 form-control-fld padding-left-none text-right"></div>
																</div>';
															}
															$row->cells [5]->value .= '<input name="increment" value="'.$incrementvalue.'" type="hidden">';
														}	
														$row->cells [5]->value .= '<!-- Table Row Ends Here -->			
															
														</div>';
														
														if(count($maxweight)>0 && isset($maxweight[0]->increment_weight) && $maxweight[0]->increment_weight>0){
														$row->cells [5]->value .= '<div class="col-md-5 form-control-fld padding-none ">
															<div class="col-md-3 padding-left-none margin-top">'.$maxweight[0]->increment_weight.' ';
																if($maxweight[0]->lkp_ict_weight_uom_id==1)
																$row->cells [5]->value .='Kgs';
																elseif($maxweight[0]->lkp_ict_weight_uom_id==2)
																$row->cells [5]->value .='Gms';
																else 
																$row->cells [5]->value .='MTS';
															$row->cells [5]->value .= '<input name="increment_weight_' . $buyer_quote_id . '" value="'.$maxweight[0]->increment_weight.'" type="hidden">
															</div>
															<div class="col-md-3 padding-left-none">
																<input class="form-control form-control1 numberVal" name="increment_value_' . $buyer_quote_id . '" placeholder="" type="text">
															</div>
														</div>';
														}else{
															$row->cells [5]->value .= '<input type="hidden" class="form-control form-control1 numberVal" name="increment_value_' . $buyer_quote_id . '" value="0">';
														}
														$row->cells [5]->value .= '<div class="col-md-12 form-control-fld padding-none ">
															<div class="col-md-3 padding-left-none"><input class="form-control form-control1 twodigitstwodecimals_deciVal numberVal" placeholder="Conversion factor" name="conversion_' . $buyer_quote_id . '" type="text"></div>
															<input type="hidden" class="form-control form-control1 numberVal" placeholder="Maximum weight accepted"  name="maxweightaccept_' . $buyer_quote_id . '" value="1">
															<div class="col-md-3 padding-left-none"><input class="form-control form-control1 numericvalidation numberVal" maxlength = "3" placeholder="Transit days" name="transitdays_' . $buyer_quote_id . '" type="text"></div>
														</div>
														<div class="col-md-12 padding-none">
															<h5 class="caption-head">Additional Charges</h5>
															<div class="col-md-3 form-control-fld">
																<input type="text" class="form-control form-control1 twodigitstwodecimals_deciVal numberVal" name= "fuel_surcharge_' . $buyer_quote_id . '" placeholder="Fuel Surcharge *" />
															</div>
															<div class="col-md-3 form-control-fld">
																<input type="text" class="form-control form-control1 twodigitstwodecimals_deciVal numberVal" name= "cod_charge_' . $buyer_quote_id . '" placeholder="Check on Delivery *" />
															</div>
															<div class="col-md-3 form-control-fld">
																<input type="text" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" name="freight_charge_' . $buyer_quote_id . '" placeholder="Freight Collect *" />
															</div>
															<div class="col-md-3 form-control-fld">
																<input type="text" class="form-control form-control1 twodigitstwodecimals_deciVal numberVal" name="arc_charge_' . $buyer_quote_id . '" placeholder="ARC *" />
															</div>
															<div class="clearfix"></div>
															<div class="col-md-3 form-control-fld">
																<input type="text" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal" name="max_value_' . $buyer_quote_id . '" placeholder="Maximum Value *" />
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>';
								}else{
									$maxweight = CommonComponent::getMaxWeightUnits($buyer_quote_id,$buyer_id);
									$slabsprice = CommonComponent::getQuotePriceDetails($buyer_quote_id,$buyer_id);
									$row->cells [5]->value .= '
									
										
										<div class="col-md-12 inner-block-bg inner-block-bg1 ">
											<div class="col-md-3 form-control-fld">
												<span class="data-value">Maximum Weight: '.$maxweight[0]->max_weight_accepted." ".CommonComponent::getWeight($maxweight[0]->lkp_ict_weight_uom_id).'</span>
											</div>
											<div class="col-md-12 padding-none">
												<div class="col-md-12 padding-none">
													<!-- Table Starts Here -->
													<div class="table-div table-style1">
														<!-- Table Head Starts Here -->
														<div class="table-heading inner-block-bg">
															<div class="col-md-3 padding-left-none">Min</div>
															<div class="col-md-3 padding-left-none">Max</div>
															<div class="col-md-3 padding-left-none">Quote</div>
														</div>
														<!-- Table Head Ends Here -->
														<div class="table-data form-control-fld padding-none">
														<!-- Table Row Starts Here -->';
									
									if($slabsprice[0]->is_saved == 0){
										$slabslist = CommonComponent::getQuotePriceDetailsSlabs($buyer_quote_id,$buyer_id);
										$incrementvalue=0;
										if(count($slabslist)>0){
											foreach($slabslist as $slabsitem){
												
												$incrementvalue = $incrementvalue + 1;
												$row->cells [5]->value .= '
														<div class="table-row inner-block-bg">
															<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_min_rate.'</div>
															<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_max_rate.'</div>
															<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_rate.'</div>
															<div class="col-md-1 form-control-fld padding-left-none text-right"></div>
														</div>';
											}
											$row->cells [5]->value .= '<input name="increment" value="'.$incrementvalue.'" type="hidden">';
										}
										$row->cells [5]->value .= '<!-- Table Row Ends Here -->
										
															</div>';
										
										
										
										
										if(count($slabsprice)>0){
											$row->cells [5]->value .= '<div class="col-md-5 form-control-fld padding-none ">
																<div class="col-md-3 padding-left-none margin-top">'.$maxweight[0]->increment_weight.' ';
											if($maxweight[0]->lkp_ict_weight_uom_id==1)
												$row->cells [5]->value .='Kgs';
											elseif($maxweight[0]->lkp_ict_weight_uom_id==2)
											$row->cells [5]->value .='Gms';
											else
												$row->cells [5]->value .='MTS';
											$row->cells [5]->value .= '
																</div>
																<div class="col-md-3 padding-left-none margin-top ">
																	'.$slabsprice[0]->incremental_weight_price.' /-
																</div>
															</div>';
										}
										$row->cells [5]->value .= '
										<div class="col-md-12 form-control-fld padding-none ">
															<div class="col-md-3 padding-left-none">Conversion factor : '.$slabsprice[0]->conversion_factor.' /-</div>
															<div class="col-md-3 padding-left-none">Transit days : '.$slabsprice[0]->transit_days.'</div>
														</div>
														<div class="col-md-12 padding-none">
															<h5 class="caption-head">Additional Charges</h5>
															<div class="col-md-3 form-control-fld">
																Fuel Surcharge : '.$slabsprice[0]->fuel_charges.' %
															</div>
															<div class="col-md-3 form-control-fld">
																COD Charge : '.$slabsprice[0]->cod_charges.' %
															</div>
															<div class="col-md-3 form-control-fld">
																Freight Charge : '.$slabsprice[0]->freight_charges.' /-
															</div>
															<div class="col-md-3 form-control-fld">
																ARC Charge : '.$slabsprice[0]->arc_charges.' %
															</div>
															<div class="clearfix"></div>
															<div class="col-md-3 form-control-fld">
																Max Value : '.$slabsprice[0]->max_value.' /-
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>';
									}else{
										$slabslist = CommonComponent::getQuotePriceDetailsSlabs($buyer_quote_id,$buyer_id);
										$incrementvalue=0;
										if(count($slabslist)>0){
											foreach($slabslist as $slabsitem){
										
												$incrementvalue = $incrementvalue + 1;
												$row->cells [5]->value .= '
														<div class="table-row inner-block-bg">
															<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_min_rate.'</div>
															<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_max_rate.'</div>
															<input name="slab_min_'.$incrementvalue.'_' . $buyer_quote_id . '" value="'.$slabsitem->slab_min_rate.'" type="hidden">
															<input name="slab_max_'.$incrementvalue.'_' . $buyer_quote_id . '" value="'.$slabsitem->slab_max_rate.'" type="hidden">
															<div class="col-md-3 padding-left-none" >
																<input class="form-control form-control1" name="slab_'.$incrementvalue.'_' . $buyer_quote_id . '" value="'.$slabsitem->slab_rate.'">
															</div>
															<div class="col-md-1 form-control-fld padding-left-none text-right"></div>
														</div>';
											}
											$row->cells [5]->value .= '<input name="increment" value="'.$incrementvalue.'" type="hidden">';
										}
										$row->cells [5]->value .= '<!-- Table Row Ends Here -->
										
															</div>';
										
										
										$maxweight = CommonComponent::getMaxWeightUnits($buyer_quote_id,$buyer_id);
										
										if(count($slabsprice)>0 && isset($maxweight[0]->increment_weight) && $maxweight[0]->increment_weight>0){
											$row->cells [5]->value .= '<div class="col-md-5 form-control-fld padding-none ">
																<div class="col-md-3 padding-left-none margin-top">'.$maxweight[0]->increment_weight.' ';
											if($maxweight[0]->lkp_ict_weight_uom_id==1)
												$row->cells [5]->value .='Kgs';
											elseif($maxweight[0]->lkp_ict_weight_uom_id==2)
											$row->cells [5]->value .='Gms';
											else
												$row->cells [5]->value .='MTS';
											$row->cells [5]->value .= '
																</div>
																<div class="col-md-3 padding-left-none ">
																<input class="form-control form-control1" name="increment_value_' . $buyer_quote_id . '" value="'.$slabsprice[0]->incremental_weight_price.'">
																	
																</div>
															</div>';
										}else{
											$row->cells [5]->value .= '<input type="hidden" class="form-control form-control1" name="increment_value_' . $buyer_quote_id . '" value="0">';
										}
										$row->cells [5]->value .= '
										<div class="col-md-12 form-control-fld padding-none ">
															<div class="col-md-3 padding-left-none">
															<input class="form-control form-control1" placeholder="Conversion factor" name="conversion_' . $buyer_quote_id . '" value="'.$slabsprice[0]->conversion_factor.'">
															</div>
															<input type="hidden" class="form-control form-control1" placeholder="Maximum weight accepted"  name="maxweightaccept_' . $buyer_quote_id . '" value="1">
															<div class="col-md-3 padding-left-none">
															<input class="form-control form-control1" placeholder="Transit days" name="transitdays_' . $buyer_quote_id . '" value="'.$slabsprice[0]->transit_days.'">
															</div>
														</div>
														<div class="col-md-12 padding-none">
															<h5 class="caption-head">Additional Charges</h5>
															<div class="col-md-3 form-control-fld">
																<input type="text" class="form-control form-control1" name= "fuel_surcharge_' . $buyer_quote_id . '" placeholder="Fuel Surcharge *" value="'.$slabsprice[0]->fuel_charges.'"/>
															</div>
															<div class="col-md-3 form-control-fld">
																<input type="text" class="form-control form-control1" name= "cod_charge_' . $buyer_quote_id . '" placeholder="Check on Delivery *" value="'.$slabsprice[0]->cod_charges.'"/>
															</div>
															<div class="col-md-3 form-control-fld">
																<input type="text" class="form-control form-control1" name="freight_charge_' . $buyer_quote_id . '" placeholder="Freight Collect *" value="'.$slabsprice[0]->freight_charges.'"/>
															</div>
															<div class="col-md-3 form-control-fld">
																<input type="text" class="form-control form-control1" name="arc_charge_' . $buyer_quote_id . '" placeholder="ARC *" value="'.$slabsprice[0]->arc_charges.'"/>
															</div>
															<div class="clearfix"></div>
															<div class="col-md-3 form-control-fld">
																<input type="text" class="form-control form-control1" name="max_value_' . $buyer_quote_id . '" placeholder="Maximum Value *" value="'.$slabsprice[0]->max_value.'"/>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>';
									}
								}
							}
						
							if($today < $bidDateTimes){
								if((Session::get ( 'service_id' )!=COURIER)){
									if($getSaveValue){
										$row->cells [5]->value .='<div class="col-md-12 col-sm-12 col-xs-12 text-right underline_link"><button type="button" value="save" name="save" id ="save_'.$buyer_quote_id.'" class="btn add-btn flat-btn margin-top termintialquotesubmit margin-bottom" >Save as Draft</button>  <button type="button"id ="submit_'.$buyer_quote_id.'" value="submit" name="submit" class="btn red-btn flat-btn margin-top pull-right termintialquotesubmit margin-bottom" >Submit</button></div>';
									}
								}else{
									if($getSaveValue){
										$row->cells [5]->value .='
											<div class="col-md-12 col-sm-12 col-xs-12 text-right underline_link">
												<button type="button" value="0"   name="save"   id ="save_'.$buyer_quote_id.'"    class="btn add-btn flat-btn margin-top couriertermintialquotesubmit margin-bottom" >Save as Draft</button>  
												<button type="button" value="1"   name="submit" id ="submit_'.$buyer_quote_id.'"  class="btn red-btn flat-btn margin-top pull-right couriertermintialquotesubmit margin-bottom" >Submit</button>
											</div>';
									}
								}
							}
			        $row->cells [5]->value .='</div>';

				$row->cells [5]->value .='<div class="col-md-6 col-sm-6 col-xs-12 padding-none pull-right text-left table-slide table-slide-4  seller_quotedetails_'.$buyer_id.'_'.$buyer_quote_id.' "></div>';

				$row->cells [5]->value .= '</div></div></div>
				<div class="clearfix"></div>
				</form>';

	});
	//filter for buyear search list top dropdown lists---filters
	$filter = DataFilter::source ( $Query_buyers_for_sellers );
	//echo $from_locations[1];
	if($serviceId == ROAD_FTL){
		$filter->add ( 'bqi.lkp_vehicle_type_id', 'Vehicle Type', 'select')->options($vehicle_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
	}elseif((Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC) || (Session::get ( 'service_id' )==AIR_INTERNATIONAL)|| (Session::get ( 'service_id' )==OCEAN)) {
		$filter->add ( 'bqi.lkp_packaging_type_id', 'Packaging Type*', 'select')->options($packagetypes)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
	}
	
	$filter->add ( 'bqi.lkp_load_type_id', 'Load Type', 'select')->options($load_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");

	//$filter->add ( 'bqi.dispatch_date', 'Dispatch Date', 'date' )->attr("class","dateRange")->attr("id","dispatch_filter_calendar");
	//$filter->add ( 'bqi.delivery_date', 'Delivery Date', 'date' )->attr("class","dateRange")->attr("id","delivery_filter_calendar");

	$filter->submit('search');
	$filter->reset('reset');
	$filter->build();
	$result = array();
	$result['grid'] = $grid;
	$result['filter'] = $filter;
	return $result;
	}
	/**
	 * @param number $id Login Id
	 */
	public static function  getSellerDistricts($id = 0,$serviceId){
		$id = ($id == 0) ? Auth::User()->id : $id;
		if($serviceId == ROAD_FTL){
			$buyers_for_sellers_items  = DB::table('seller_posts as sp')
				->join ( 'seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' )
				->where('sp.seller_id',$id)
				->groupBy('spi.lkp_district_id')
				->lists('spi.lkp_district_id');
		}elseif(Session::get ( 'service_id' )==ROAD_PTL) {
			$buyers_for_sellers_items  = DB::table('ptl_seller_posts as sp')
				->join ( 'ptl_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' )
				->where('sp.seller_id',$id)
				->groupBy('spi.lkp_district_id')
				->lists('spi.lkp_district_id');
		}elseif(Session::get ( 'service_id' )==RAIL) {
			$buyers_for_sellers_items  = DB::table('rail_seller_posts as sp')
				->join ( 'rail_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' )
				->where('sp.seller_id',$id)
				->groupBy('spi.lkp_district_id')
				->lists('spi.lkp_district_id');
		}elseif(Session::get ( 'service_id' )==AIR_DOMESTIC) {
			$buyers_for_sellers_items  = DB::table('airdom_seller_posts as sp')
				->join ( 'airdom_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' )
				->where('sp.seller_id',$id)
				->groupBy('spi.lkp_district_id')
				->lists('spi.lkp_district_id');
		}elseif(Session::get ( 'service_id' )==AIR_INTERNATIONAL) {
			$buyers_for_sellers_items  = DB::table('airint_seller_posts as sp')
				->join ( 'airint_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' )
				->where('sp.seller_id',$id)
				->groupBy('spi.from_location_id')
				->lists('spi.from_location_id');
		}elseif(Session::get ( 'service_id' )==OCEAN) {
			$buyers_for_sellers_items  = DB::table('ocean_seller_posts as sp')
				->join ( 'ocean_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' )
				->where('sp.seller_id',$id)
				->groupBy('spi.from_location_id')
				->lists('spi.from_location_id');
		}elseif(Session::get ( 'service_id' )==COURIER) {
			$buyers_for_sellers_items  = DB::table('courier_seller_posts as sp')
				->join ( 'courier_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' )
				->where('sp.seller_id',$id)
				->groupBy('spi.lkp_district_id')
				->lists('spi.lkp_district_id');
		}
		if($serviceId == RELOCATION_DOMESTIC){
			$buyers_for_sellers_items  = DB::table('relocation_seller_posts as sp')
			->join ( 'relocation_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' )
			->where('sp.seller_id',$id)
			->groupBy('sp.seller_district_id')
			->lists('sp.seller_district_id');
		}
		if($serviceId == RELOCATION_INTERNATIONAL){
			$buyers_for_sellers_items  = DB::table('relocationint_seller_posts as sp')
			->where('sp.seller_id',$id)
			->groupBy('sp.seller_district_id')
			->lists('sp.seller_district_id');
		}
		if($serviceId == RELOCATION_GLOBAL_MOBILITY){
			$buyers_for_sellers_items  = DB::table('relocationgm_seller_posts as sp')
			->where('sp.seller_id',$id)
			->groupBy('sp.seller_district_id')
			->lists('sp.seller_district_id');
		}

		//->get();
		//echo "<pre>";print_R($buyers_for_sellers_items); echo "</pre>";
		return $buyers_for_sellers_items;
	}


        /**
         * Contracts list for sellers
	 * @param number $id Login Id
	 */
        public static function  getSellerContracts($order_type, $order_status, $service_id,$roleId){

            // Filters values to populate in the page
              if(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY){
                   $from_locations = array (
				"" => "Location"
			); 
              } else {
                    $from_locations = array (
				"" => "From Location"
			);
              }
			
			$to_locations = array (
				"" => "To Location"
			);
			$buyer_name = array (
				"" => "Buyer"
			);
			$consignee_name = array (
				"" => "Contract No"
			);
			$from_date = '';
			$to_date = '';
			$order_no = '';
			//echo Auth::User ()->id;

			$query = DB::table ( 'term_contracts as tc' );
					$query->leftJoin('term_buyer_quotes as bq', 'bq.id', '=', 'tc.term_buyer_quote_id');
					$query->leftJoin('term_buyer_quote_items as bqi', 'bqi.id', '=', 'tc.term_buyer_quote_item_id');
			$serviceId = Session::get('service_id');

			switch ($serviceId) {
				case ROAD_FTL :
				case ROAD_INTRACITY :
				case RELOCATION_DOMESTIC :
				case RELOCATION_INTERNATIONAL :

					$query->leftJoin ( 'lkp_cities as lc', 'bqi.from_location_id', '=', 'lc.id' );
					$query->leftJoin ( 'lkp_cities as lcity', 'bqi.to_location_id', '=', 'lcity.id' );
                    break;
				case ROAD_PTL :
				case RAIL :
				case AIR_DOMESTIC :
					$query->leftJoin('lkp_ptl_pincodes as lp', 'lp.id', '=', 'bqi.from_location_id');
					$query->leftJoin('lkp_ptl_pincodes as lcityp', 'lcityp.id', '=', 'bqi.to_location_id');
					break;
				case COURIER :					
					$query->join('lkp_ptl_pincodes as lp', 'bqi.from_location_id', '=', 'lp.id');
					$query->leftjoin('lkp_ptl_pincodes as lcity', function($join)
                                        {
                                            $join->on('bqi.to_location_id', '=', 'lcity.id');
                                            $join->on(DB::raw('bq.lkp_courier_delivery_type_id'),'=',DB::raw(1));
                                        });
                                        $query->leftjoin('lkp_countries as lcity1', function($join)
                                        {
                                            $join->on('bqi.to_location_id', '=', 'lcity1.id');
                                            $join->on(DB::raw('bq.lkp_courier_delivery_type_id'),'=',DB::raw(2));
                                        });
					break;
				case AIR_INTERNATIONAL :
					$query->leftJoin('lkp_airports as lp', 'lp.id', '=', 'bqi.from_location_id');
					$query->leftJoin('lkp_airports as lcityp', 'lcityp.id', '=', 'bqi.to_location_id');

					break;
				case OCEAN :
					$query->leftJoin('lkp_seaports as lp', 'lp.id', '=', 'bqi.from_location_id');
					$query->leftJoin('lkp_seaports as lcityp', 'lcityp.id', '=', 'bqi.to_location_id');
					break;
				case RELOCATION_GLOBAL_MOBILITY :
						$query->leftJoin ( 'lkp_cities as lc', 'bqi.from_location_id', '=', 'lc.id' );
						break;
				default :
					$query->leftJoin ( 'lkp_cities as lc', 'bqi.from_city_id', '=', 'lc.id' );
					$query->leftJoin ( 'lkp_cities as lcity', 'bqi.to_city_id', '=', 'lcity.id' );
					break;
			}


			$query->leftJoin('users as u', 'u.id', '=', 'tc.created_by');
			$query->leftJoin ( 'lkp_order_statuses as os', 'tc.contract_status', '=', 'os.id' );
			$query->leftJoin ( 'lkp_services as ls', 'ls.id', '=', 'tc.lkp_service_id' );
			//$query->groupby ( 'tc.seller_id', '=', Auth::User ()->id );
			$query->where ( 'tc.seller_id', '=', Auth::User ()->id );

			// conditions to make search
			if(Session::get ( 'service_id' )  == COURIER){
				$query->where('bq.lkp_courier_delivery_type_id', '=', Session::get('delivery_type'));
			}
                        
          if(Session::get ( 'service_id' )  == RELOCATION_INTERNATIONAL){
                                $int_type = 1;
                                if (isset ( $_REQUEST ['int_type'] ) && $_REQUEST ['int_type'] != '') {
                                        $int_type = $_REQUEST['int_type'];
                                }                                
				$query->where('tc.lkp_lead_type_id', '=', $int_type);
			}
                        
			if (isset ( $service_id ) && $service_id != '') {
				$query->where ( 'tc.lkp_service_id', '=', $service_id );
			}
			if (isset ( $order_status ) && $order_status != '') {
				$query->where ( 'tc.contract_status', '=', $order_status );
			}
			if (isset ( $_GET ['start_dispatch_date'] ) && $_GET ['start_dispatch_date'] != '') {
				$query->where ( 'bq.from_date', '>=', CommonComponent::convertDateForDatabase($_GET ['start_dispatch_date']) );
				$from_date = $_GET ['start_dispatch_date'];
				// echo $from_date;die();
			}
			if (isset ( $_GET ['end_dispatch_date'] ) && $_GET ['end_dispatch_date'] != '') {
				$query->where ( 'bq.from_date', '<=', CommonComponent::convertDateForDatabase($_GET ['end_dispatch_date']) );
				$to_date = $_GET ['end_dispatch_date'];
				// echo $from_date;die();
			}
			$query->groupBy('tc.term_buyer_quote_id');


			switch ($serviceId) {
				case ROAD_FTL :
				case RELOCATION_DOMESTIC :
				case RELOCATION_INTERNATIONAL :

					$orderResults = $query->select( 'tc.*', 'os.order_status as order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city','u.username','bq.from_date','bq.to_date','bq.lkp_post_ratecard_type'  )->get ();
					break;
				case ROAD_PTL :
				case RAIL           :
				case AIR_DOMESTIC   :
					$orderResults = $query->select('tc.*', 'os.order_status as order_status', 'lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city','u.username','bq.from_date','bq.to_date','bq.lkp_post_ratecard_type' )->get();
					break;
				case COURIER   :
					$orderResults = $query->select('tc.*', 'os.order_status as order_status', 'lp.postoffice_name as from_city', 
												   DB::raw("(case when `bq`.`lkp_courier_delivery_type_id` = 1 then lcity.postoffice_name  when `bq`.`lkp_courier_delivery_type_id` = 2 then lcity1.country_name end) as 'to_city'"),
												   'u.username','bq.from_date','bq.to_date','bq.lkp_post_ratecard_type' )->get();
					break;
				case AIR_INTERNATIONAL   :
					$orderResults = $query->select('tc.*', 'os.order_status as order_status', 'lp.airport_name as from_city', 'lcityp.airport_name as to_city','u.username','bq.from_date','bq.to_date','bq.lkp_post_ratecard_type' )->get();
					break;
				case OCEAN   :
					$orderResults = $query->select('tc.*', 'os.order_status as order_status',  'lp.seaport_name as from_city', 'lcityp.seaport_name as to_city','u.username','bq.from_date','bq.to_date','bq.lkp_post_ratecard_type' )->get();
					break;
				case ROAD_INTRACITY :
					$orderResults = $query->select ( 'tc.*', 'os.order_status as order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city' ,'bq.from_date','bq.to_date','bq.lkp_post_ratecard_type' )->get ();
					break;
				case RELOCATION_GLOBAL_MOBILITY :
						$orderResults = $query->select( 'tc.*', 'os.order_status as order_status', 'lc.city_name as from_city','u.username','bq.from_date','bq.to_date'  )->get ();
						break;
				default :
					$orderResults = $query->select ( 'tc.*', 'os.order_status as order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city','bq.from_date','bq.to_date','bq.lkp_post_ratecard_type'  )->get ();
					break;
			}

			
			// Functionality to handle filters based on the selection starts
			//echo "<pre>"; print_r($orderResults); die;
			if(Session::get ( 'service_id' )  == COURIER){
				$seller_orders = DB::table ( 'term_buyer_quote_items as bqi' )
                                                            ->leftjoin('term_contracts as tc','tc.term_buyer_quote_item_id','=','bqi.id')
                                                            ->leftjoin('term_buyer_quotes as bq','bqi.term_buyer_quote_id','=','bq.id')
                                                            ->leftjoin('users as u','u.id','=','bqi.created_by')
                                                            ->where ( 'tc.seller_id', Auth::User ()->id )
                                                            ->where ( 'bq.lkp_courier_delivery_type_id', Session::get('delivery_type'))
                                                            ->where ( 'tc.lkp_service_id', $serviceId)
                                                            ->select ( 'bqi.*','u.username' )->get ();		
                        }elseif(Session::get ( 'service_id' )  == RELOCATION_INTERNATIONAL){
                                $seller_orders = DB::table ( 'term_buyer_quote_items as bqi' )
                                                            ->leftjoin('term_contracts as tc','tc.term_buyer_quote_item_id','=','bqi.id')
                                                            ->leftjoin('users as u','u.id','=','bqi.created_by')
                                                            ->where ( 'tc.seller_id', Auth::User ()->id )
                                                            ->where ( 'tc.lkp_service_id', $serviceId)
                                                            ->where ( 'tc.lkp_lead_type_id', $int_type)
                                                            ->select ( 'bqi.*','u.username' )->get ();
                        } else {
                                $seller_orders = DB::table ( 'term_buyer_quote_items as bqi' )
                                                            ->leftjoin('term_contracts as tc','tc.term_buyer_quote_item_id','=','bqi.id')
                                                            ->leftjoin('users as u','u.id','=','bqi.created_by')
                                                            ->where ( 'tc.seller_id', Auth::User ()->id )
                                                            ->where ( 'tc.lkp_service_id', $serviceId)
                                                            ->select ( 'bqi.*','u.username' )->get ();
                        }
				//echo "<pre>";print_r($seller_orders);exit;		   
				foreach ( $seller_orders as $order ) {
					if (! isset ( $from_locations [$order->from_location_id] )) {

						switch ($serviceId) {
							case ROAD_FTL :
							case RELOCATION_DOMESTIC :
                            case RELOCATION_INTERNATIONAL :
								$from_locations [$order->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $order->from_location_id )->pluck ( 'city_name' );
								break;
							case ROAD_PTL :
							case RAIL           :
							case AIR_DOMESTIC   :
							case COURIER   :
								$from_locations[$order->from_location_id] = DB::table('lkp_ptl_pincodes')->where('id', $order->from_location_id)->pluck('postoffice_name');
								break;
							case AIR_INTERNATIONAL   :
								$from_locations[$order->from_location_id] = DB::table('lkp_airports')->where('id', $order->from_location_id)->pluck('airport_name');
								break;
							case OCEAN   :
								$from_locations[$order->from_location_id] = DB::table('lkp_seaports')->where('id', $order->from_location_id)->pluck('seaport_name');
								break;
							case ROAD_INTRACITY :
								$from_locations [$order->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $order->from_location_id )->pluck ( 'city_name' );
								break;
							default :
								$from_locations [$order->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $order->from_location_id )->pluck ( 'city_name' );
								break;
						}
					}
					if (! isset ( $to_locations [$order->to_location_id] )) {


						switch ($serviceId) {
							case ROAD_FTL :
							case RELOCATION_DOMESTIC :
                            case RELOCATION_INTERNATIONAL :
								$to_locations [$order->to_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $order->to_location_id )->pluck ( 'city_name' );
								break;
							case ROAD_PTL :
							case RAIL           :
							case AIR_DOMESTIC   :
								$to_locations [$order->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $order->to_location_id )->pluck ( 'postoffice_name' );
								break;
							case COURIER   :
								if(Session::get('delivery_type') == 1){
									$to_locations [$order->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $order->to_location_id )->pluck ( 'postoffice_name' );
								}else{
									$to_locations[$order->to_location_id] = DB::table('lkp_countries')->where('id', $order->to_location_id)->pluck('country_name');
								}
								break;
							case AIR_INTERNATIONAL   :
								$to_locations [$order->to_location_id] = DB::table ( 'lkp_airports' )->where ( 'id', $order->to_location_id )->pluck ( 'airport_name' );
								break;
							case OCEAN   :
								$to_locations [$order->to_location_id] = DB::table ( 'lkp_seaports' )->where ( 'id', $order->to_location_id )->pluck ( 'seaport_name' );
								break;
							case ROAD_INTRACITY :
								$to_locations [$order->to_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $order->to_location_id )->pluck ( 'city_name' );
								break;
							default :
								$to_locations [$order->to_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $order->to_location_id )->pluck ( 'city_name' );
								break;
						}

					}
					if (! isset ( $buyer_name [$order->username] )) {
						$buyer_name [$order->username] = $order->username;
					}
					//if (! isset ( $consignee_name [$order->buyer_consignee_name] )) {
						//$consignee_name [$order->buyer_consignee_name] = $order->buyer_consignee_name;
					//}
				}

			// Functionality to handle filters based on the selection ends
			$grid = DataGrid::source ( $query );

			$grid->add ( 'id', 'ID', false )->style ( 'display:none' );
			$grid->add ( 'contract_no', 'Contract No', 'contract_no' )->attributes ( array (
				"class" => "col-md-2 padding-left-none break-all") );
			$grid->add ( 'username', 'Name', 'username' )->attributes ( array (
				"class" => "col-md-2 padding-left-none") );
			if(Session::get ( 'service_id' )  == RELOCATION_GLOBAL_MOBILITY){
			$grid->add ( 'from_city', 'Location', 'from_city' )->attributes ( array (
				"class" => "col-md-4 padding-left-none") );
			}else{
			$grid->add ( 'from_city', 'From Location', 'from_city' )->attributes ( array (
						"class" => "col-md-2 padding-left-none") );
			}
			if(Session::get ( 'service_id' )  == RELOCATION_GLOBAL_MOBILITY){
			$grid->add ( 'to_city', 'Location', 'to_city' )->attributes ( array (
				"class" => "col-md-2 padding-left-none") )->style ( 'display:none' );
			}else{
				$grid->add ( 'to_city', 'To Location', 'to_city' )->attributes ( array (
						"class" => "col-md-2 padding-left-none") );
			}
			 $grid->add ( 'contract_status', 'Status', 'contract_status' )->attributes ( array (
				"class" => "col-md-2 padding-left-none"
			) );

			 $grid->add ( 'order_status', '', 'order_status' )->style ( 'display:none' );

			$grid->add ( 'a', '', '' );
			$grid->add ( 'created_by', 'To', 'created_by' )->style ( 'display:none' );
			$grid->add ( 'lkp_post_ratecard_type', 'Rate Card', 'lkp_post_ratecard_type' )->style ( 'display:none' );
			$grid->orderBy ( 'id', 'desc' );
				
			$grid->paginate ( 5 );

			$grid->row ( function ($row) {
				$order_id = $row->cells [0]->value;
				$quote_id=$row->data->term_buyer_quote_id;
				$row->cells [0]->value = '<a href=/contract/details/' . $quote_id . '>';

				$row->cells [0]->style ( 'display:none' );
				$row->cells [1]->attributes ( array (
					"class" => "col-md-2 padding-left-none"
				));
				$row->cells [2]->attributes ( array (
					"class" => "col-md-2 padding-left-none"
				));
				$row->cells [3]->attributes ( array (
					"class" => "col-md-2 padding-left-none"
				));
				$row->cells [4]->attributes ( array ("class" => "col-md-2 padding-left-none") );
				$row->cells [5]->attributes ( array (
					"class" => "col-md-2 padding-left-none"
				) )->style ( 'display:none' );
				$row->cells [6]->attributes ( array (
					"class" => "col-md-4 padding-none"
				) );
				$row->cells [7]->attributes ( array (
					"class" => "col-md-6 padding-left-none"
				) );
				$row->cells [8]->attributes ( array (
					"class" => "col-md-12 show-data-div_$order_id accept_$order_id margin-top padding-none"
				) )->style ( 'display:none' );

				//$row->cells [8]->style ( 'display:none' );
				$row->cells [9]->style ( 'display:none' );
				$orderStatusId = $row->cells [5]->value;
				$status = $row->cells [6]->value;
				

				
				$quote_items = DB::table ( 'term_buyer_quotes as bq' )
									->leftJoin ( 'term_buyer_quote_items as bqi', 'bqi.term_buyer_quote_id', '=', 'bq.id' )
									->where ( 'bq.id', $row->data->term_buyer_quote_id)->select ( 'bqi.id' )->get ();
				


				$serviceId = Session::get('service_id');
				$from = TermBuyerComponent::checkMulti($serviceId,$quote_id,"from_location_id");
				$to = 	TermBuyerComponent::checkMulti($serviceId,$quote_id,"to_location_id");
				
				if(Session::get ( 'service_id' )  != RELOCATION_GLOBAL_MOBILITY){
				if($from == "multi"){
					$row->cells [3]->value = "Many";
				}
				if($to == "multi"){
					$row->cells [4]->value = "Many";
				}
				}
				
                $seller_id=$row->cells [8]->value;
                $msg_cnt = MessagesComponent::getPerticularMessageDetailsCount(null,$order_id,1);
				$row->cells [6]->value ='</a><div class="col-md-6 padding-left-none">'.$status.'<br>';
				if($orderStatusId == PENDING_ACCEPTANCE){
				$accept ='<a href="javascript:void(0)" class="accept_contract btn red-btn pull-right show-data-link" data-orderid="'.$order_id.'" rel="Accept Contract">
						  <span class="accept_contract1" data-orderid="'.$order_id.'">Accept Contract</span></a></div>';
				}else{
				$accept = $row->cells [7]->value ='</div>';
				}
				$row->cells [7]->value .='</div>'.$accept.'<div class="clearfix"></div>
													<div class="col-md-10 padding-none pull-left">
													<div class="info-links">
													<a href="/getmessagedetails/0/'.$order_id.'/1"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
														<a href="#"><i class="fa fa-file-text-o"></i>
													Documentation
													</a></div></div>
													<div class="col-md-2 padding-none text-right pull-right">
													<div class="info-links info-links_'.$order_id.'">
													<a class="accept_contract" data-orderid="'.$order_id.'">
													<span class="accept_contract" id="minus-icon_'.$order_id.'" style="display: none;">-</span>
													<span class="accept_contract" id="plus-icon_'.$order_id.'" style="display: inline;">+</span> Details</a>
													<a href="#" class="new_message" data-userid="'.$seller_id.'" data-term="1" data-contractid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a></div>';
                                $details_contract  =    TermBuyerComponent::getTermContractDetails($row->data->term_buyer_quote_id,$serviceId);

				
			       
					if($serviceId==ROAD_FTL){
					$row->cells [8]->value ='<div class="table-div table-style table-style1 margin-none">
											<div class="table-heading inner-block-bg">
											<div class="col-md-2 padding-left-none">From Location</div>
											<div class="col-md-2 padding-left-none">To Location</div>
											<div class="col-md-3 padding-left-none">Load Type</div>
											<div class="col-md-2 padding-left-none">Vehicle Type</div>
											<div class="col-md-1 padding-left-none">Quantity</div>
											<div class="col-md-2 padding-left-none">Freight</div>
											</div></div"><div class="table-data">';
					}elseif($serviceId==RELOCATION_DOMESTIC){					
						if($row->cells [9]->value==1 || $row->cells [9]->value==0){
						$row->cells [8]->value ='<div class="table-div table-style table-style1 margin-none">
												<div class="table-heading inner-block-bg">
												<div class="col-md-3 padding-left-none">From Location</div>
												<div class="col-md-3 padding-left-none">To Location</div>
												<div class="col-md-2 padding-left-none">Volume</div>
												<div class="col-md-2 padding-left-none">Number of Shipments</div>
												<div class="col-md-2 padding-left-none">Freight</div>
												</div><div class="table-data">';
						}else{
						$row->cells [8]->value ='<div class="table-div table-style table-style1 margin-none">
												<div class="table-heading inner-block-bg">
												<div class="col-md-2 padding-left-none">From Location</div>
												<div class="col-md-2 padding-left-none">To Location</div>
												<div class="col-md-2 padding-left-none">Vehicle Category</div>
												<div class="col-md-1 padding-left-none">Vehicle Size</div>
												<div class="col-md-2 padding-left-none">Vehicle Model</div>
												<div class="col-md-1 padding-left-none">Transport Charges</div>
												<div class="col-md-1 padding-left-none">O&D Charges</div>
												<div class="col-md-1 padding-left-none">Freight</div>
												</div><div class="table-data">';
						}
					}elseif($serviceId==RELOCATION_INTERNATIONAL){	
						$row->cells [8]->value ='<div class="table-div table-style table-style1 margin-none">
												<div class="table-heading inner-block-bg">
												<div class="col-md-6 padding-left-none">From Location</div>
												<div class="col-md-6 padding-left-none">To Location</div>												
												</div><div class="table-data">';
						
					}elseif($serviceId==RELOCATION_GLOBAL_MOBILITY){	
						$row->cells [8]->value ='<div class="table-div table-style table-style1 margin-none">
												<div class="table-heading inner-block-bg">
												<div class="col-md-3 padding-left-none">From Location</div>
												<div class="col-md-3 padding-left-none">Service</div>
												<div class="col-md-3 padding-left-none">Numbers</div>
												<div class="col-md-3 padding-left-none">Rate</div>
												</div><div class="table-data">';
						
					}elseif($serviceId==COURIER){
						$row->cells [8]->value ='<div class="table-div table-style table-style1 margin-none">
											<div class="table-heading inner-block-bg">
											<div class="col-md-2 padding-left-none">From Location</div>
											<div class="col-md-2 padding-left-none">To Location</div>
											<div class="col-md-1 padding-left-none">Volume</div>
											<div class="col-md-2 padding-left-none">Number of Packages</div>
											</div><div class="table-data">';

					}else{
					$row->cells [8]->value ='<div class="table-div table-style table-style1 margin-none">
											<div class="table-heading inner-block-bg">
											<div class="col-md-2 padding-left-none">From Location</div>
											<div class="col-md-2 padding-left-none">To Location</div>
											<div class="col-md-2 padding-left-none">Load Type</div>
											<div class="col-md-2 padding-left-none">Number of Packages</div>
											<div class="col-md-1 padding-left-none">Volume</div>
											<div class="col-md-1 padding-left-none">Contract Quantity</div>
											<div class="col-md-1 padding-left-none">Rate per KG</div>
											<div class="col-md-1 padding-left-none">KG per CFT</div>
											</div><div class="table-data">';
					}
					foreach($details_contract as $details){
						if($serviceId==ROAD_FTL){
						$row->cells [8]->value .='<div class="table-row inner-block-bg">
											<div class="col-md-2 padding-left-none">'.$details->from.'</div>
											<div class="col-md-2 padding-left-none">'.$details->to.'</div>
											<div class="col-md-3 padding-left-none">'.$details->load_type.'</div>
											<div class="col-md-2 padding-left-none">'.$details->vehicle_type.'</div>
											<div class="col-md-1 padding-left-none">'.$details->contract_quantity.'</div>
											<div class="col-md-2 padding-left-none">'.$details->contract_price.'</div>
										</div>';
						}elseif($serviceId==RELOCATION_DOMESTIC){
						if($row->cells [9]->value==1 || $row->cells [9]->value==0){	
					    $row->cells [8]->value .='<div class="table-row inner-block-bg">
											<div class="col-md-3 padding-left-none">'.$details->from.'</div>
											<div class="col-md-3 padding-left-none">'.$details->to.'</div>
											<div class="col-md-2 padding-left-none">'.$details->volume.'</div>
											<div class="col-md-2 padding-left-none">'.$details->number_packages.'</div>
											<div class="col-md-2 padding-left-none">'.$details->contract_price.'</div>
											</div>';
						}else{
							if($details->lkp_vehicle_category_id==1){
							  $type_id=CommonComponent::getVehicleCategorytypeById($details->lkp_vehicle_category_type_id);	
							}else{
							 $type_id='N/A';
							}
						$row->cells [8]->value .='<div class="table-row inner-block-bg">
											<div class="col-md-2 padding-left-none">'.$details->from.'</div>
											<div class="col-md-2 padding-left-none">'.$details->to.'</div>
											<div class="col-md-2 padding-left-none">'.CommonComponent::getVehicleCategoryById($details->lkp_vehicle_category_id).'</div>
											<div class="col-md-1 padding-left-none">'.$type_id.'</div>
											<div class="col-md-2 padding-left-none">'.$details->vehicle_model.'</div>		
											<div class="col-md-1 padding-left-none">'.$details->contract_transport_charges.'</div>
											<div class="col-md-1 padding-left-none">'.$details->contract_od_charges.'</div>
											<div class="col-md-1 padding-left-none">'.$details->contract_price.'</div>
											</div>';
						}
					  }elseif($serviceId==RELOCATION_INTERNATIONAL){
						$row->cells [8]->value .='<div class="table-row inner-block-bg">
											<div class="col-md-6 padding-left-none">'.$details->from.'</div>
											<div class="col-md-6 padding-left-none">'.$details->to.'</div>												
										</div>';
					  }elseif($serviceId==RELOCATION_GLOBAL_MOBILITY){
							$row->cells [8]->value .='<div class="table-row inner-block-bg">
											<div class="col-md-3 padding-left-none">'.$details->from.'</div>
											<div class="col-md-3 padding-left-none">'.CommonComponent::getAllGMServiceTypesById($details->lkp_gm_service_id).'</div>
											<div class="col-md-3 padding-left-none">'.$details->measurement.''.$details->measurement_units.'</div>
											<div class="col-md-3 padding-left-none">'.$details->contract_price.'</div>	
													
										</div>';

					 }elseif($serviceId==COURIER){
						$row->cells [8]->value .='<div class="table-row inner-block-bg">
											<div class="col-md-2 padding-left-none">'.$details->from.'</div>
											<div class="col-md-2 padding-left-none">'.$details->to.'</div>
											<div class="col-md-1 padding-left-none">'.$details->volume.'</div>
											<div class="col-md-2 padding-left-none">'.$details->number_packages.'</div>
										</div>';
						
						
						
						

					 }else{
						$row->cells [8]->value .='<div class="table-row inner-block-bg">
											<div class="col-md-2 padding-left-none">'.$details->from.'</div>
											<div class="col-md-2 padding-left-none">'.$details->to.'</div>
											<div class="col-md-2 padding-left-none">'.$details->load_type.'</div>
											<div class="col-md-2 padding-left-none">'.$details->number_packages.'</div>
											<div class="col-md-1 padding-left-none">'.$details->volume.'</div>
											<div class="col-md-1 padding-left-none">'.$details->contract_quantity.'</div>
											<div class="col-md-1 padding-left-none">'.$details->contract_rate_per_kg.'</div>
											<div class="col-md-1 padding-left-none">'.$details->contract_kg_per_cft.'</div>
										</div>';

						}
					}
					
					if($serviceId==COURIER){
						$row->cells [8]->value .='<div class="clearfix"></div>';
						$slabslist = CommonComponent::getQuotePriceDetailsSlabs($details->term_buyer_quote_id,$details->created_by);
						
						$maxweight = CommonComponent::getMaxWeightUnits($details->term_buyer_quote_id,$details->created_by);
						$row->cells [8]->value .='<div class="col-md-12 inner-block-bg inner-block-bg1 ">
							<div class="col-md-3 form-control-fld">
								<span class="data-value">Maximum Weight : '.$maxweight[0]->max_weight_accepted." ".CommonComponent::getWeight($maxweight[0]->lkp_ict_weight_uom_id).'</span>
							</div>
							<div class="col-md-12 padding-none">
							<div class="col-md-12 padding-none">
							<!-- Table Starts Here -->
							<div class="table-div table-style1">
									<!-- Table Head Starts Here -->
									<div class="table-heading inner-block-bg">
									<div class="col-md-3 padding-left-none">Min</div>
									<div class="col-md-3 padding-left-none">Max</div>
									<div class="col-md-3 padding-left-none">Quote</div>
									</div>
									<!-- Table Head Ends Here -->
									<div class="table-data form-control-fld padding-none">
									<!-- Table Row Starts Here -->';
							
						$incrementvalue=0;
						if(count($slabslist)>0){
							foreach($slabslist as $slabsitem){
								$incrementvalue = $incrementvalue + 1;
								$row->cells [8]->value .= '
											<div class="table-row inner-block-bg">
												<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_min_rate.'</div>
												<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_max_rate.'</div>
												<div class="col-md-3 padding-left-none" >'.$slabsitem->slab_rate.'</div>
												<div class="col-md-1 form-control-fld padding-left-none text-right"></div>
											</div>';
							}
							$row->cells [8]->value .= '<input name="increment" value="'.$incrementvalue.'" type="hidden">';
						}
						$row->cells [8]->value .= '<!-- Table Row Ends Here -->
						
																			</div>';
						
						$slabsprice = CommonComponent::getQuotePriceDetails($details->term_buyer_quote_id,$details->created_by);
							
						
						if(count($slabsprice)>0){
							$row->cells [8]->value .= '<div class="col-md-5 form-control-fld padding-none ">
																				<div class="col-md-3 padding-left-none margin-top">'.$maxweight[0]->increment_weight.' ';
							if($maxweight[0]->lkp_ict_weight_uom_id==1)
								$row->cells [8]->value .='Kgs';
							elseif($maxweight[0]->lkp_ict_weight_uom_id==2)
							$row->cells [8]->value .='Gms';
							else
								$row->cells [8]->value .='MTS';
							$row->cells [8]->value .= '
																				</div>
																				<div class="col-md-3 padding-left-none margin-top ">
																					'.$slabsprice[0]->incremental_weight_price.' /-
																				</div>
																			</div>';
						}
						$row->cells [8]->value .= '
									<div class="col-md-12 form-control-fld padding-none ">
										<div class="col-md-3 padding-left-none">Conversion factor : '.$slabsprice[0]->conversion_factor.' /-</div>
										<div class="col-md-3 padding-left-none">Maximum weight accepted : '.$slabsprice[0]->max_weight_accepted.'</div>
										<div class="col-md-3 padding-left-none">Transit days : '.$slabsprice[0]->transit_days.'</div>
									</div>
									<div class="col-md-12 padding-none">
									<h5 class="caption-head">Additional Charges</h5>
									<div class="col-md-3 form-control-fld">
									Fuel Surcharge : '.$slabsprice[0]->fuel_charges.' /-
									</div>
									<div class="col-md-3 form-control-fld">
									COD Charge : '.$slabsprice[0]->cod_charges.' /-
									</div>
									<div class="col-md-3 form-control-fld">
																	Freight Charge : '.$slabsprice[0]->freight_charges.' /-
																</div>
									<div class="col-md-3 form-control-fld">
									ARC Charge : '.$slabsprice[0]->arc_charges.' /-
									</div>
									<div class="clearfix"></div>
									<div class="col-md-3 form-control-fld">
									Max Value : '.$slabsprice[0]->max_value.' /-
									</div>
									</div>
									</div>
									</div>
									</div>
									</div>';
					}

					if($orderStatusId == PENDING_ACCEPTANCE){
						$row->cells [8]->value .='</div><a href="/setcontractstatus/'.$quote_id.'" class="accept_contract1 pull-right margin-top accept_hide_button_'.$order_id.' btn add-btn flat-btn doc-btn pull-right" id="'. $order_id .'">Accept</a>';
					}else{
						//$row->cells [8]->value .='</div>';
					}
					$row->cells [8]->value .='<div class="pull-right link-text margin-top padding-top"><a href="/getcontractdownload/'.$order_id.'" >Download Contract</a></div></div>';

				//$row->cells [8]->value = '<a href="/orders/details/'.$order_id.'">'.$row->cells [8]->value.'</a>';
				$row->attributes ( array (
					"class" => ""
				) );
				// $grid->add('title','Title', true)->style("width:100px");
			} );
			// Functionality to build filters in the page starts
			$filter = DataFilter::source ( $query );
			$filter->add ( 'tc.from_location_id', '', 'select' )->options ( $from_locations )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
			$filter->add ( 'tc.to_location_id', '', 'select' )->options ( $to_locations )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
			$filter->add ( 'tc.username', '', 'select' )->options ( $buyer_name )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
			//$filter->add ( 'tc.buyer_consignee_name', '', 'select' )->options ( $consignee_name )->attr ( "class", "selectpicker" )->attr ( "onchange", "this.form.submit()" );
			$filter->add ( 'tc.contract_no', 'Contract No.', 'text' )->attr ( "class", "form-control form-control1 margin-bottom-none" );

			$filter->submit ( 'search' );

			$filter->reset ( 'reset' );
			$filter->build ();
			// Functionality to build filters in the page ends

			$result = array ();
			$result ['grid'] = $grid;
			$result ['filter'] = $filter;
			return $result;
        }
	public static function checkQuoteSubmitted($buyer_items,$service,$sellerId,$buyer_quote_id=null,$saved=false){
		$isexists = 0;
		if($service == ROAD_FTL || $service == RELOCATION_DOMESTIC || $service == RELOCATION_INTERNATIONAL || $service == RELOCATION_GLOBAL_MOBILITY){
			for ($i = 0; $i < count($buyer_items); $i++) {
				$initialQuotePriceDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
					->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_item_id', '=', $buyer_items[$i])
					->where('term_buyer_quote_sellers_quotes_prices.seller_id', '=', $sellerId);
				if($saved == true){
					$initialQuotePriceDisplay->where('term_buyer_quote_sellers_quotes_prices.is_saved', '=', 1);
					$initialQuotePriceDisplay->where('term_buyer_quote_sellers_quotes_prices.is_submitted', '!=', 1);
				}else{
					$initialQuotePriceDisplay->where('term_buyer_quote_sellers_quotes_prices.is_submitted', '=', 1);
				}

				$initialQuotePriceDisplay = $initialQuotePriceDisplay->select('initial_quote_price', 'is_submitted')
					->first();
				//echo "<pre>";echo $initialQuotePriceDisplay->tosql(); print_R($initialQuotePriceDisplay->get());echo "</pre>";
				if (!empty($initialQuotePriceDisplay)) {
					$isexists++;
				}
			}
		}elseif((Session::get ( 'service_id' )==ROAD_PTL) || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC)|| (Session::get ( 'service_id' )==AIR_INTERNATIONAL)|| (Session::get ( 'service_id' )==OCEAN)) {
			for ($i = 0; $i < count($buyer_items); $i++) {
				$getbuyerquoteitems = DB::table('term_buyer_quote_items')
					->where('term_buyer_quote_items.id', '=', $buyer_items[$i])
					->select('from_location_id', 'to_location_id', 'lkp_load_type_id', 'lkp_vehicle_type_id', 'quantity', 'lkp_packaging_type_id', 'number_packages')
					->first();

				$initialQuotePriceDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
					->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_item_id', '=', $buyer_items[$i])
					->where('term_buyer_quote_sellers_quotes_prices.seller_id', '=', $sellerId);
				if($saved == true){
					$initialQuotePriceDisplay->where('term_buyer_quote_sellers_quotes_prices.is_saved', '=', 1);
					$initialQuotePriceDisplay->where('term_buyer_quote_sellers_quotes_prices.is_submitted', '!=', 1);
				}else{
					$initialQuotePriceDisplay->where('term_buyer_quote_sellers_quotes_prices.is_submitted', '=', 1);
				}


				$initialQuotePriceDisplay = $initialQuotePriceDisplay->select('initial_quote_price', 'is_submitted', 'initial_rate_per_kg', 'initial_kg_per_cft')
					->first();

				if (!empty($initialQuotePriceDisplay)) {
					$isexists++;
				}
			}
		}elseif((Session::get ( 'service_id' )==COURIER)) {
			
			for ($i = 0; $i < count($buyer_items); $i++) {
				
				$initialQuotePriceDisplay = DB::table('term_buyer_quote_sellers_quotes_prices')
					->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_id', '=', $buyer_quote_id)
					->where('term_buyer_quote_sellers_quotes_prices.seller_id', '=', $sellerId);
				if($saved == true){
					$initialQuotePriceDisplay->where('term_buyer_quote_sellers_quotes_prices.is_saved', '=', 1);
					$initialQuotePriceDisplay->where('term_buyer_quote_sellers_quotes_prices.is_submitted', '!=', 1);
				}else{
					$initialQuotePriceDisplay->where('term_buyer_quote_sellers_quotes_prices.is_submitted', '=', 1);
				}


				$initialQuotePriceDisplay = $initialQuotePriceDisplay->select('initial_quote_price', 'is_submitted', 'initial_rate_per_kg', 'initial_kg_per_cft')
					->first();
		

				if (!empty($initialQuotePriceDisplay)) {
					$isexists++;
				}
			}
			
		}
		
		return $isexists;
	}
}