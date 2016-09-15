<?php

namespace App\Components\Relocation;

use App\Components\Matching\SellerMatchingComponent;
use App\Models\RelocationBuyerQuoteSellersQuotesPrice;
use App\Models\RelocationSellerPost;
use App\Models\RelocationSellerPostItem;
use App\Models\RelocationSellerSelectedBuyer;
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
use App\Components\Search\SellerSearchComponent;
use App\Models\PtlZone;
use App\Models\PtlTier;
use App\Models\PtlTransitday;
use App\Models\PtlSector;
use App\Models\PtlPincodexsector;
use App\Components\MessagesComponent;

class RelocationSellerComponent {
	
	/**
	 * Relocation Seller Posts List Page - Grid and filters
	 * Retrieval of data related to seller posts list items to populate in the seller list widget
	 * Displays a grid with a list of all seller posts
	 */
	public static function RelocationSpotSellerPosts($statusId, $serviceId, $roleId,$type) {
		if(isset($_REQUEST['page'])){//echo $_REQUEST['page'];
		}		
		$from_locations = array(""=>"From Location");
		$to_locations = array(""=>"To Location");
		$post_for = array(""=>"Post For");
		
		// query to retrieve seller posts list and bind it to the grid
		$Query = DB::table ( 'relocation_seller_posts as rsp' );
		$Query->leftjoin ( 'relocation_seller_post_items as rspi', 'rspi.seller_post_id', '=', 'rsp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rsp.lkp_post_status_id' );	
		$Query->join ( 'lkp_cities as cf', 'rsp.from_location_id', '=', 'cf.id' );
		$Query->join ( 'lkp_cities as ct', 'rsp.to_location_id', '=', 'ct.id' );
		$Query->join ( 'lkp_post_ratecard_types as prct', 'rsp.rate_card_type', '=', 'prct.id' );
		$Query->join ( 'lkp_quote_accesses as qa', 'rsp.lkp_access_id', '=', 'qa.id' );
		if(isset($_REQUEST['lead_name']) && ($_REQUEST['lead_name'] ==2)){
			Session::put('leads', '2');
			$Query->where('rsp.lkp_access_id',1);
		}
		else{
			Session::put('leads', '1');
			$Query->leftjoin ( 'relocation_buyer_selected_sellers as rbqss', 'rbqss.seller_id', '=', 'rspi.created_by' );			
		}
		$Query->where('rsp.seller_id',Auth::user()->id);	
		//conditions to make search
		if(isset($statusId) && $statusId != '' && $statusId!=0){
			$Query->where('rsp.lkp_post_status_id', $statusId);
		}
		if(isset($type) && $type != ''){
			if($type==1){
				$Query->where('rsp.created_by', Auth::user()->id);
			}
		}
		if( isset($_REQUEST['search']) && $_REQUEST['from_date']!=''){
			$from=CommonComponent::convertDateForDatabase($_REQUEST['from_date']);
			$Query->whereRaw('rsp.from_date >= "'.$from.'"');
		}
	
		if( isset($_REQUEST['search']) && $_REQUEST['to_date']!=''){
			$to=CommonComponent::convertDateForDatabase($_REQUEST['to_date']);
			if($_REQUEST['from_date']!=''){
				$Query->whereBetween('rsp.to_date',array($from,$to));
			}else{
				$Query->where('rsp.to_date', $to);
			}
		}	
		
		if( isset($_REQUEST['search']) && $_REQUEST['post_for']!=''){
			$post_for=$_REQUEST['post_for'];
			$Query->whereRaw('rsp.rate_card_type = "'.$post_for.'"');
		}
		
		$sellerresults = $Query->select ( 'rsp.id', 'rsp.from_date',
				'rsp.to_date','rsp.lkp_access_id','rsp.lkp_post_status_id','rsp.from_location_id',
				'rsp.to_location_id','cf.city_name as fromLocation', 'ct.city_name as toLocation',
				'prct.ratecard_type as rateCatdType','qa.quote_access as quoteAccessType',
				'ps.post_status as postStatus','rsp.rate_card_type'
		)
		->groupBy('rsp.id')
		->get ();
		//Functionality to handle filters based on the selection starts
		foreach($sellerresults as $seller){
			$seller_post_items  = DB::table('relocation_seller_posts')
			->where('relocation_seller_posts.id',$seller->id)
			->select('*')
			->get();
			foreach($seller_post_items as $seller_post_item){
				if(!isset($from_locations[$seller_post_item->from_location_id])){
					$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
				}
				if(!isset($to_locations[$seller_post_item->to_location_id])){
					$to_locations[$seller_post_item->to_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->to_location_id)->pluck('city_name');
				}							
			}
		}
		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations = CommonComponent::orderArray($to_locations);
		//echo "<pre>"; print_r($sellerresults); die;		
		$grid = DataGrid::source ( $Query );	
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'fromLocation', 'From', 'fromLocation' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'toLocation', 'To', 'toLocation' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'rateCatdType', 'Post For', 'rateCatdType' )->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'quoteAccessType', 'Post Type', 'quoteAccessType' )->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'postStatus', 'Status', 'postStatus' )->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
		$grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );	
	
		$grid->row ( function ($row) {
            
            $sellerPostId = $row->cells [0]->value;
            $fromLcoation = $row->cells [1]->value;
          	$toLcoation = $row->cells [2]->value;
            $fromDate = $row->cells [3]->value;
            $toDate = $row->cells [4]->value;
            $postFor = $row->cells [5]->value;
            $postType = $row->cells [6]->value;
            $status = $row->cells [7]->value;
            
            $row->cells [0]->style ( 'display:none' );
            $row->cells [1]->style ( 'display:none' );
            $row->cells [2]->style ( 'display:none' );
            $row->cells [3]->style ( 'display:none' );
            $row->cells [4]->style ( 'display:none' );
            $row->cells [5]->style ( 'display:none' );
            $row->cells [6]->style ( 'display:none' );
            $row->cells [7]->style ( 'display:none' );
            
            if($status == 'Draft')
            	$data_link = url()."/relocation/updatesellerpost/$sellerPostId";
            else
            	$data_link = url()."/sellerpostdetail/$sellerPostId";       
            
			$row->cells [8]->value .= "<div class=''><a href='$data_link'>										
										<div class='col-md-2 padding-left-none'>$fromLcoation</div>
										<div class='col-md-2 padding-left-none'>$toLcoation</div>
										<div class='col-md-2 padding-left-none'>".CommonComponent::checkAndGetDate($fromDate)."</div>
										<div class='col-md-2 padding-left-none'>".CommonComponent::checkAndGetDate($toDate)."</div>
										<div class='col-md-1 padding-left-none'>$postFor</div>
										<div class='col-md-1 padding-none'>$postType</div>
										<div class='col-md-1 padding-none'> $status </div></a>";
			if ($status == 'Open' || $status == 'Draft') {
			$row->cells [8]->value .= "<div class='col-md-1 padding-none text-right'>
										<a href='javascript:void(0)' onclick='relocationsellerpostcancel($sellerPostId)'><i class='fa fa-trash' title='Delete'></i></a>
										</div>";
			}

			$enquiriesCount = SellerMatchingComponent::getMatchedResults(RELOCATION_DOMESTIC,$sellerPostId);
			$leadsCount = SellerMatchingComponent::getSellerLeads(RELOCATION_DOMESTIC,$sellerPostId);
			
			//count for seller documents
			$serviceId = Session::get('service_id');
			$docs_seller_domestic    =   CommonComponent::getGsaDocuments(SELLER,$serviceId,$sellerPostId);
			
			//view count for sellers
			$viewcount = CommonComponent::getSellersViewcountFromTable($sellerPostId,'relocation_seller_post_views');
			$msg_count  =    MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$sellerPostId);
			
			$row->cells [8]->value .= "<div class='clearfix'></div>
										<div class='pull-left'>
											<div class='info-links'>												
												<a href='/sellerpostdetail/$sellerPostId?type=messages'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>".count($msg_count['result'])."</span></a>
												<a href='/sellerpostdetail/$sellerPostId?type=enquiries'><i class='fa fa-file-text-o'></i>  Enquiries<span class='badge'>".count($enquiriesCount)."</span></a>
												<a href='/sellerpostdetail/$sellerPostId?type=leads'><i class='fa fa-bullseye'></i> Leads<span class='badge'>".count($leadsCount)."</span></a>
												<a href='javascript:void(0);'><i class='fa fa-line-chart'></i> Market Analytics<span class='badge'>0</span></a>
												<a href='/sellerpostdetail/$sellerPostId?type=documentation'><i class='fa fa-file-text-o'></i> Documentation<span class='badge'>".count($docs_seller_domestic)."</span></a>												
											</div>
										</div>
										<div class='pull-right text-right'>
											<div class='info-links'>
												<a><span class='views red'><i class='fa fa-eye' title='Views'></i> $viewcount </span></a>
											</div>
										</div>
									</div>";
			
			$row->attributes(array("class" => ""));			
				
		} );
		//Functionality to build filters in the page starts				
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'rsp.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'rsp.to_location_id', 'From Location', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->submit('search');
			$filter->reset('reset');
			$filter->build();
			//Functionality to build filters in the page ends	
			$result = array();
			$result['grid'] = $grid;
			$result['filter'] = $filter;
			return $result;
	}
	
	
	public static function RelocationSellerMarketLeads($statusId, $serviceId, $roleId,$type) {
		if(isset($_REQUEST['page'])){//echo $_REQUEST['page'];
		}
		$from_locations = array(""=>"From Location");
		$to_locations = array(""=>"To Location");
		$post_for = array(""=>"Post For");
	
		// query to retrieve seller posts list and bind it to the grid
		$Query = DB::table ( 'relocation_buyer_posts as rbp' );		
		$Query->leftjoin ( 'relocation_buyer_selected_sellers as rbss', 'rbss.buyer_post_id', '=', 'rbp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rbp.lkp_post_status_id' );
		$Query->join ( 'lkp_cities as cf', 'rbp.from_location_id', '=', 'cf.id' );
		$Query->join ( 'lkp_cities as ct', 'rbp.to_location_id', '=', 'ct.id' );		
		$Query->join('lkp_post_ratecard_types as rt', 'rt.id', '=', 'rbp.lkp_post_ratecard_type_id');
		$Query->leftjoin('lkp_property_types as pty', 'pty.id', '=', 'rbp.lkp_property_type_id');
		$Query->join ( 'lkp_quote_accesses as qa', 'rbp.lkp_quote_access_id', '=', 'qa.id' );
		$Query->leftjoin('lkp_vechicle_categorie_types as vct', 'vct.id', '=', 'rbp.lkp_vehicle_category_type_id');
		$Query->leftjoin('lkp_load_categories as lcat', 'lcat.id', '=', 'rbp.lkp_load_category_id');
		$Query->leftjoin ( 'relocation_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbp.id' );
		$Query->leftjoin ('users as us', 'us.id', '=', 'rbp.buyer_id');
		$Query->where('rbss.seller_id',Auth::user()->id);
		$Query->where('rbp.lkp_post_status_id', OPEN);
		$Query->where('rbp.lkp_quote_access_id',2);
		//conditions to make search
		if(isset($statusId) && $statusId != '' && $statusId!=0){
			$Query->where('rbp.lkp_post_status_id', $statusId);
		}
		if(isset($type) && $type != ''){
			if($type==1){
				$Query->where('rbp.created_by', Auth::user()->id);
			}
		}
		if( isset($_REQUEST['search']) && $_REQUEST['from_date']!=''){
			$from=CommonComponent::convertDateForDatabase($_REQUEST['from_date']);
			$Query->whereRaw('rbp.dispatch_date >= "'.$from.'"');
		}
	
		if( isset($_REQUEST['search']) && $_REQUEST['to_date']!=''){
			$to=CommonComponent::convertDateForDatabase($_REQUEST['to_date']);
			if($_REQUEST['from_date']!=''){
				$Query->whereBetween('rbp.delivery_date',array($from,$to));
			}else{
				$Query->where('rbp.delivery_date', $to);
			}
		}
	
		if( isset($_REQUEST['search']) && $_REQUEST['post_for']!=''){
			$post_for=$_REQUEST['post_for'];
			$Query->whereRaw('rbp.lkp_post_ratecard_type_id = "'.$post_for.'"');
		}
	
		$sellerresults = $Query->select ( 'rbp.*','us.username','cf.city_name as fromcity','ct.city_name as tocity','rt.ratecard_type','pty.property_type','pty.volume','vct.lkp_vechicle_categorie_type','lcat.load_category'
		)
		->groupBy('rbp.id')
		->get ();
		//Functionality to handle filters based on the selection starts
			foreach($sellerresults as $seller){
				$seller_post_items  = DB::table('relocation_buyer_posts')
				->where('relocation_buyer_posts.id',$seller->id)
				->select('*')
				->get();
				foreach($seller_post_items as $seller_post_item){
					if(!isset($from_locations[$seller_post_item->from_location_id])){
						$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
					}
					if(!isset($to_locations[$seller_post_item->to_location_id])){
						$to_locations[$seller_post_item->to_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->to_location_id)->pluck('city_name');
					}
				}
			}
			$from_locations = CommonComponent::orderArray($from_locations);
			$to_locations = CommonComponent::orderArray($to_locations);
			//echo "<pre>"; print_R(Session::get('layered_filter_loadtype')); die;
			//echo "<pre>"; print_R($sellerresults); die;
			
			Session::put('RelcoationRequestData', $sellerresults);			
			
			$grid = DataGrid::source ( $sellerresults );
			$grid->add ( 'id', 'ID', true )->style ( "display:none" );
			$grid->add ( 'username', 'Buyer Name', false )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'dispatch_date', 'Dispatch Date', false )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'ratecard_type', 'Post', false )->attributes(array("class" => "col-md-1 padding-left-none"));
			$grid->add ( 'property_type', 'Property Type', false )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'volume', 'Volume (CFT)', false )->attributes(array("class" => "col-md-1 padding-left-none"));
			$grid->add ( 'lkp_vechicle_categorie_type', 'Vehicle Type', false )->attributes(array("class" => "col-md-1 padding-left-none"));
			$grid->add ( 'load_category', 'Load Type', false )->attributes(array("class" => "col-md-1 padding-left-none"));
			$grid->add ( 'test', 'Below Grid', true )->style ( "display:none" );		
			$grid->add ( 'lkp_post_ratecard_type_id', 'Rate card type', false )->style ( "display:none" );
			$grid->add ( 'destination_elevator', 'Destination Eleavator', false )->style ( "display:none" );
			$grid->add ( 'origin_elevator', 'Origin Eleavator', false )->style ( "display:none" );
			$grid->add ( 'origin_storage', 'Origin storage', false )->style ( "display:none" );
			$grid->add ( 'origin_handyman_services', 'Origin Handyman service', false )->style ( "display:none" );
			
			$grid->add ( 'insurance', 'insurance', false )->style ( "display:none" );
			$grid->add ( 'escort', 'escort', false )->style ( "display:none" );
			$grid->add ( 'mobility', 'mobility', false )->style ( "display:none" );
			$grid->add ( 'property', 'property', false )->style ( "display:none" );
			$grid->add ( 'setting_service', 'setting_service', false )->style ( "display:none" );
			$grid->add ( 'insurance_industry', 'insurance_industry', false )->style ( "display:none" );			
			$grid->add ( 'origin_destination', 'origin_destination', false )->style ( "display:none" );
			$grid->add ( 'destination_handyman_services', 'destination_handyman_services', false )->style ( "display:none" );
            $grid->add ( 'transaction_id', 'transaction_id', 'transaction_id' )->style ( "display:none" );
            $grid->add ( 'created_by', 'Created By', 'created_by' )->style ( "display:none" );
			$grid->add ( 'from_location_id', 'From Location', 'from_location_id' )->style ( "display:none" );
			$grid->add ( 'to_location_id', 'To Location', 'to_location_id' )->style ( "display:none" );

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
				$row->cells [7]->style ( 'display:none' );	
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
				$id = $row->cells [0]->value;
				$buyerbussinessname = $row->cells [1]->value;
				$dispatchdate = $row->cells [2]->value;
				$post = $row->cells [3]->value;				
				$propertytype = $row->cells [4]->value;
				$volume = CommonComponent::getVolumeCft($id)+CommonComponent::getCratingVolumeCft($id);
				$fromlocation = CommonComponent::getCityName($row->cells [24]->value);
				$tolocation = CommonComponent::getCityName($row->cells [25]->value);
				$vehicletype = $row->cells [6]->value;
				$loadtype = $row->cells [7]->value;
				$ratecardtypeId = $row->cells [9]->value;
				$destinationelevator = $row->cells [10]->value;
				$origineleavtor = $row->cells [11]->value;
				$originstorage = $row->cells [12]->value;
				$originhandymanservice = $row->cells [13]->value;				
				$insurance = $row->cells [14]->value;
				$escort = $row->cells [15]->value;
				$mobility = $row->cells [16]->value;
				$property= $row->cells [17]->value;
				$setting_service = $row->cells [18]->value;
				$insurance_industry = $row->cells [19]->value;				
				$origin_destination = $row->cells [20]->value;
				$destination_handyman_services = $row->cells [21]->value;
				$transaction_id = $row->cells [22]->value;
                $buyer_id = $row->cells [23]->value;
                
                $requestSessiondata=Session::get('RelcoationRequestData');                
                
				if($propertytype!="" && $propertytype!=0) {
					$propType = $row->cells [4]->value;
				} else {
					$propType = '---';
				}
				if($volume!="" && $volume!=0) {
					$vol = $volume;
				} else {
					$vol = '---';
				}
				if($vehicletype!="" && $vehicletype!=0) {
					$vehicle = $row->cells [6]->value;
				} else {
					$vehicle = '---';
				}
				if($loadtype!="" && $loadtype!=0) {
					$loadcat = $row->cells [7]->value;
				} else {
					$loadcat = '---';
				}	
							
				if (isset($destinationelevator) &&  $destinationelevator==1) {
					$destelev= 'checked=checked';
				} else {
					$destelev= 'disabled=disabled';
				}				
				if (isset($destinationelevator) &&  $destinationelevator==0) {
					$destelevno= 'checked=checked';
				} else {
					$destelevno= 'disabled=disabled';
				}
				//Check origin and destination elevator and checkbox checkeing
				if (isset($origineleavtor) &&  $origineleavtor==1) {
					$origdev= 'checked=checked';
				} else {
					$origdev= 'disabled=disabled';
				}
				if (isset($origineleavtor) &&  $origineleavtor==0) {
					$origdevno= 'checked=checked';
				} else {
					$origdevno= 'disabled=disabled';
				}				
				if (isset($originstorage) &&  $originstorage==1) {
					$origin_storage= 'checked=checked';
				} else {
					$origin_storage= 'disabled=disabled';
				}
				//checkbox checking value set or not
				if (isset($originstorage) &&  $originstorage==1) {
					$origin_storage= 'checked=checked';
				} else {
					$origin_storage= 'disabled=disabled';
				}
				if (isset($originhandymanservice) &&  $originhandymanservice==1) {
					$originhandy_manservice= 'checked=checked';
				} else {
					$originhandy_manservice= 'disabled=disabled';
				}				
				if (isset($insurance) &&  $insurance==1) {
					$originhandy_insurance= 'checked=checked';
				} else {
					$originhandy_insurance= 'disabled=disabled';
				}
				if (isset($escort) &&  $escort==1) {
					$originhandy_escort= 'checked=checked';
				} else {
					$originhandy_escort= 'disabled=disabled';
				}
				if (isset($mobility) &&  $mobility==1) {
					$originhandy_mobility= 'checked=checked';
				} else {
					$originhandy_mobility= 'disabled=disabled';
				}
				if (isset($property) &&  $property==1) {
					$originhandy_property= 'checked=checked';
				} else {
					$originhandy_property= 'disabled=disabled';
				}
				if (isset($setting_service) &&  $setting_service==1) {
					$originhandy_setting_service = 'checked=checked';
				} else {
					$originhandy_setting_service= 'disabled=disabled';
				}
				if (isset($insurance_industry) &&  $insurance_industry==1) {
					$originhandy_insurance_industrye = 'checked=checked';
				} else {
					$originhandy_insurance_industrye= 'disabled=disabled';
				}
				
				if (isset($origin_destination) &&  $origin_destination==1) {
					$destdestination_storage = 'checked=checked';
				} else {
					$destdestination_storage= 'disabled=disabled';
				}
				if (isset($destination_handyman_services) &&  $destination_handyman_services==1) {
					$destination_handyman = 'checked=checked';
				} else {
					$destination_handyman = 'disabled=disabled';
				}
				
				//view count for sellers
				//$viewcount = CommonComponent::viewCountForBuyer(Auth::User ()->id,$id,'relocation_buyer_post_views');
								
				//Check Query for count no of room items in details section.
				$masterdata = '--';
				$bedroom1 = '--';
				$bedroom2 = '--';
				$bedroom3 = '--';
				$lobby = '--';
				$kitchen = '--';
				$bathroom = '--';
				$drawingroom = '--';
				$getrooms  = DB::table('relocation_buyer_post_inventory_particulars as rebip')
				->leftjoin ( 'lkp_inventory_rooms as itr', 'itr.id', '=', 'rebip.lkp_inventory_room_id' )
				->groupBy('rebip.lkp_inventory_room_id')
				->where('rebip.buyer_post_id',$id)->select('lkp_inventory_room_id')->get();				
				foreach($getrooms as $getroom){
					$getroomsdata  = DB::table('relocation_buyer_post_inventory_particulars as rebip')
					->leftjoin ( 'lkp_inventory_rooms as itr', 'itr.id', '=', 'rebip.lkp_inventory_room_id' )
					->where('itr.id',$getroom->lkp_inventory_room_id)
					->where('rebip.buyer_post_id',$id)
					->select('rebip.lkp_inventory_room_id',DB::raw('sum(rebip.number_of_items) AS totalItems'))
					->get();					
					//echo "<pre>"; print_r($getroomsdata);					
					foreach($getroomsdata as $getdata){
						//echo $getdata->lkp_inventory_room_id;
						if ($getdata->lkp_inventory_room_id == 1) {
							$masterdata = $getdata->totalItems;
						}
						if ($getdata->lkp_inventory_room_id == 2) {					
							$bedroom1 = $getdata->totalItems;
						} 
						if ($getdata->lkp_inventory_room_id == 3) {
							$bedroom2 = $getdata->totalItems;
						}
						if ($getdata->lkp_inventory_room_id == 4) {
							$bedroom3 = $getdata->totalItems;
						}
						if ($getdata->lkp_inventory_room_id == 5) {
							$lobby = $getdata->totalItems;
						}
						if ($getdata->lkp_inventory_room_id == 6) {
							$kitchen = $getdata->totalItems;
						}
						if ($getdata->lkp_inventory_room_id == 7) {
							$bathroom = $getdata->totalItems;
						}
						if ($getdata->lkp_inventory_room_id == 8) {
							$drawingroom = $getdata->totalItems;
						}
					}
				}
				$sellercomponent = new RelocationSellerComponent();
				$submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User ()->id,$id);
			    $enquiry = $sellercomponent::getBuyerpostById($id);
			    //echo "<pre>".$id;print_R($enquiry);die;			
            	$submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted";
				$row->cells [8]->value.="<div class='table-row inner-block-bg no-border'>
										<div class='col-md-2 padding-left-none'>
											<span class='lbl padding-8'></span>
											$buyerbussinessname
											<div class='red'>
												<i class='fa fa-star'></i>
												<i class='fa fa-star'></i>
												<i class='fa fa-star'></i>
											</div>
										</div>
										<div class='col-md-2 padding-left-none'>".CommonComponent::checkAndGetDate($dispatchdate)."</div>
										<div class='col-md-1 padding-none'>$post</div>
										<div class='col-md-2 padding-none'>$propType</div>
										<div class='col-md-1 padding-none'>$vol<input type='hidden' value='".$vol."' name='enquiry_volume_".$id."' id='enquiry_volume_".$id."'></div>
										
										<div class='col-md-1 padding-none'>$loadcat</div>
										<div class='col-md-3 padding-none'><button class='detailsslide-term btn red-btn pull-right submit-data' id ='$id'>".$submitedquotetext."</button></div>";
										
										if ($ratecardtypeId == 1) {
				$row->cells [8]->value.="<div class='clearfix'></div>
										<div class='pull-right text-right'>
											<div class='info-links'>
												<a class='show-data-link' id='$id'><span class='show-icon'>+</span><span class='hide-icon'>-</span> Details</a>
												<a href='#' data-userid='".$buyer_id."' data-buyer-transaction='".$transaction_id."' class='new_message' data-buyerquoteitemidforseller='".$id."'><i class='fa fa-envelope-o'></i></a>
											</div>
										</div>";
										}
			$buyerpostdata = array();
			$buyerpostdata['from_location_id'] = $enquiry->from_location_id;
			$buyerpostdata['to_location_id'] = $enquiry->to_location_id;
			$buyerpostdata['valid_from'] = $enquiry->dispatch_date;
			$buyerpostdata['valid_to'] = date('Y-m-d', strtotime($enquiry->dispatch_date. " + 1 days"));
			$buyerpostdata['lkp_vehicle_category_id'] = $enquiry->lkp_vehicle_category_id;
			$buyerpostdata['lkp_vehicle_category_type_id'] = $enquiry->lkp_vehicle_category_type_id;
			$buyerpostdata['property_type'] = $enquiry->lkp_property_type_id;
			$buyerpostdata['load_category'] = $enquiry->lkp_load_category_id;
			$buyerpostdata['nquiry_volume'] = $vol;
//echo "<pre>";print_R($enquiry);die;
			$SubmitquotePartial = view('relocation.sellers.submit_quote')->with([
												'submittedquote' => $submittedquote,
												'enquiry'=>$enquiry,
												'id' => $id,
												'ratecard_type' => $enquiry->lkp_post_ratecard_type_id,
												'is_search' => 1,
												'search_params' => $buyerpostdata
										])->render();

				$row->cells [8]->value.="<div class='col-md-12  padding-none padding-top term_quote_details_$id' style='display:none'>
											$SubmitquotePartial
								 		</div>

										<div class='col-md-12 show-data-div padding-top spot_transaction_details_view_list' id='spot_transaction_details_view_'".$id."'>
												<div class='table-div table-style1'>
												<h3>
													<i class='fa fa-map-marker'></i> $fromlocation to $tolocation
													<span class='close-icon'>x</span>
												</h3>
												
												<!-- Table Head Starts Here -->
												<div class='table-heading inner-block-bg'>
													<div class='col-md-2 padding-left-none'>Particulars</div>
													<div class='col-md-1 padding-left-none text-center'>Master Bedroom</div>
													<div class='col-md-1 padding-left-none text-center'>Bedroom 1</div>
													<div class='col-md-1 padding-left-none text-center'>Bedroom 2</div>
													<div class='col-md-1 padding-left-none text-center'>Bedroom 3</div>
													<div class='col-md-2 padding-left-none text-center'>Lobby / Garrage / Store Room</div>
													<div class='col-md-1 padding-left-none text-center'>Kitchen / Dinning</div>
													<div class='col-md-2 padding-left-none text-center'>Bathroom</div>
													<div class='col-md-1 padding-left-none text-center'>Living / Drawing Room</div>													
												</div>

												<!-- Table Head Ends Here -->

												<div class='table-data'>													

													<!-- Table Row Starts Here -->
													<div class='table-row inner-block-bg'>
														<div class='col-md-2 padding-left-none medium-text'>No of Items</div>
														<div class='col-md-1 padding-left-none text-center'>$masterdata</div>
														<div class='col-md-1 padding-left-none text-center'>$bedroom1</div>
														<div class='col-md-1 padding-left-none text-center'>$bedroom2</div>
														<div class='col-md-1 padding-left-none text-center'>$bedroom3</div>
														<div class='col-md-2 padding-left-none text-center'>$lobby</div>
														<div class='col-md-1 padding-left-none text-center'>$kitchen</div>
														<div class='col-md-2 padding-left-none text-center'>$bathroom</div>
														<div class='col-md-1 padding-left-none text-center'>$drawingroom</div>														
													</div>
													<!-- Table Row Ends Here -->
												</div>
											</div>	

											<div class='margin-top'>
												<div class='col-md-4 form-control-fld margin-top'>
													<div class='radio-block'>
														<span class='padding-right-15'>Origin Elevator</span> 
														<input type='radio' $origdev name='elevator1' id='elevator1_a'>
														<label for='elevator1_a'><span></span>Yes</label>
															
														<input type='radio' $origdevno name='elevator1' id='elevator1_b'>
														<label for='elevator1_b'><span></span>No</label>
													</div>
												</div>
												<div class='col-md-4 form-control-fld margin-top'>
													<div class='radio-block'>
														<span class='padding-right-15'>Destination Elevator1</span> 
														<input type='radio' $destelev name='elevator2' id='elevator2_a'>
														<label for='elevator2_a'><span></span>Yes</label>
															
														<input type='radio' $destelevno name='elevator2' id='elevator2_b'>
														<label for='elevator2_b'><span></span>No</label>
													</div>
												</div>
												<div class='clearfix'></div>

												<div class='col-md-4 form-control-fld'>
													<div class='radio-block'><input type='checkbox' $origin_storage > <span class='lbl padding-8'>Storage</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_manservice> <span class='lbl padding-8'>Handyman Services</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_insurance> <span class='lbl padding-8'>Insurance</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_escort> <span class='lbl padding-8'>Escort</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_mobility> <span class='lbl padding-8'>Mobility</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_property> <span class='lbl padding-8'>Property</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_setting_service> <span class='lbl padding-8'>Setting Service</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_insurance_industrye> <span class='lbl padding-8'>Insurance Domestic</span></div>
												</div>
												<div class='col-md-4 form-control-fld'>
													<div class='radio-block'><input type='checkbox' $destdestination_storage> <span class='lbl padding-8'>Storage</span></div>
													<div class='radio-block'><input type='checkbox' $destination_handyman> <span class='lbl padding-8'>Handyman Services</span></div>
												</div>
											</div>											
								 		</div>
									</div>";	
			} );
								
			//Functionality to build filters in the page starts
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'rbp.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'rbp.to_location_id', 'From Location', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->submit('search');
			$filter->reset('reset');
			$filter->build();
			//Functionality to build filters in the page ends
			$result = array();
			$result['grid'] = $grid;
			$result['filter'] = $filter;
			return $result;
	}
	
	
	public static function getRelocationSellerSearchResults($roleId, $serviceId,$statusId)
	{	
		try 
		{	
			$loadTypeCategory = [];
			$propertytype = [];
			$inputparams = $_REQUEST;
			$Query_buyers_for_sellers = SellerSearchComponent::search(
				$roleId, $serviceId, $statusId, $inputparams
			);
			$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();

			if (empty ( $Query_buyers_for_sellers_filter )) {
				//CommonComponent::searchTermsSendMail ();
				Session::put('layered_filter', '');
				Session::put('layered_filter_payments', '');
				Session::put('show_layered_filter','');
				Session::put('layered_filter_loadtype', '');
			}	
			
			// Below script for filter data getting from queries --for filters
			foreach ( $Query_buyers_for_sellers_filter as $seller_post_item ) {				
				if (! isset ( $from_locations [$seller_post_item->from_location_id] )) {
					$from_locations [$seller_post_item->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $seller_post_item->from_location_id )->pluck ( 'city_name' );
				}
				if (! isset ( $to_locations [$seller_post_item->to_location_id] )) {
					$to_locations [$seller_post_item->to_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $seller_post_item->to_location_id )->pluck ( 'city_name' );
				}	
				if(isset($_REQUEST['is_search'])){
					if (! isset ( $loadTypeCategory [$seller_post_item->lkp_load_category_id] )) {
						
						$loadTypeCategory[$seller_post_item->lkp_load_category_id] = $seller_post_item->load_category;
					}
					if (! isset ( $sellerNames [$seller_post_item->buyer_id] )) {
						$sellerNames[$seller_post_item->buyer_id] = $seller_post_item->username;
					}
						
					Session::put('layered_filter', $sellerNames);
					
					Session::put('layered_filter_loadtype', $loadTypeCategory);
					
					if (! isset ( $propertytype [$seller_post_item->lkp_property_type_id] )) {					
						$propertytype[$seller_post_item->lkp_property_type_id] = $seller_post_item->property_type;
					}
					Session::put('layered_filter_propertytype', $propertytype);
					
				}			
			}//echo "<pre>"; print_R(Session::get('layered_filter_loadtype')); die;
			$result = $Query_buyers_for_sellers->get ();			
			$gridBuyer = DataGrid::source ( $Query_buyers_for_sellers );
			$gridBuyer->add ( 'id', 'ID', true )->style ( "display:none" );
			$gridBuyer->add ( 'username', 'Buyer Name', false )->attributes(array("class" => "col-md-2 padding-left-none"));
			$gridBuyer->add ( 'dispatch_date', 'Dispatch Date', false )->attributes(array("class" => "col-md-2 padding-left-none"));
			$gridBuyer->add ( 'ratecard_type', 'Post', false )->attributes(array("class" => "col-md-1 padding-left-none"));
			$gridBuyer->add ( 'property_type', 'Property Type', false )->attributes(array("class" => "col-md-2 padding-left-none"));
			$gridBuyer->add ( 'volume', 'Volume (CFT)', false )->attributes(array("class" => "col-md-1 padding-left-none"));
			$gridBuyer->add ( 'lkp_vechicle_categorie_type', 'Vehicle Type', false )->attributes(array("class" => "col-md-1 padding-left-none"));
			$gridBuyer->add ( 'load_category', 'Load Type', false )->attributes(array("class" => "col-md-1 padding-left-none"));
			$gridBuyer->add ( 'test', 'Below Grid', true )->style ( "display:none" );		
			$gridBuyer->add ( 'lkp_post_ratecard_type_id', 'Rate card type', false )->style ( "display:none" );
			$gridBuyer->add ( 'destination_elevator', 'Destination Eleavator', false )->style ( "display:none" );
			$gridBuyer->add ( 'origin_elevator', 'Origin Eleavator', false )->style ( "display:none" );
			$gridBuyer->add ( 'origin_storage', 'Origin storage', false )->style ( "display:none" );
			$gridBuyer->add ( 'origin_handyman_services', 'Origin Handyman service', false )->style ( "display:none" );
			
			$gridBuyer->add ( 'insurance', 'insurance', false )->style ( "display:none" );
			$gridBuyer->add ( 'escort', 'escort', false )->style ( "display:none" );
			$gridBuyer->add ( 'mobility', 'mobility', false )->style ( "display:none" );
			$gridBuyer->add ( 'property', 'property', false )->style ( "display:none" );
			$gridBuyer->add ( 'setting_service', 'setting_service', false )->style ( "display:none" );
			$gridBuyer->add ( 'insurance_industry', 'insurance_industry', false )->style ( "display:none" );			
			$gridBuyer->add ( 'origin_destination', 'origin_destination', false )->style ( "display:none" );
			$gridBuyer->add ( 'destination_handyman_services', 'destination_handyman_services', false )->style ( "display:none" );
            $gridBuyer->add ( 'transaction_id', 'transaction_id', 'transaction_id' )->style ( "display:none" );
            $gridBuyer->add ( 'created_by', 'Created By', 'created_by' )->style ( "display:none" );
			$gridBuyer->add ( 'from_location_id', 'From Location', 'from_location_id' )->style ( "display:none" );
			$gridBuyer->add ( 'to_location_id', 'To Location', 'to_location_id' )->style ( "display:none" );
			$gridBuyer->orderBy ( 'id', 'desc' );
			$gridBuyer->paginate ( 5 );
			
		
			$gridBuyer->row ( function ($row) {
				$row->cells [0]->style ( 'display:none' );
				$row->cells [1]->style ( 'display:none' );
				$row->cells [2]->style ( 'display:none' );
				$row->cells [3]->style ( 'display:none' );
				$row->cells [4]->style ( 'display:none' );
				$row->cells [5]->style ( 'display:none' );
				$row->cells [6]->style ( 'display:none' );
				$row->cells [7]->style ( 'display:none' );	
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
				$id = $row->cells [0]->value;
				$buyerbussinessname = $row->cells [1]->value;
				$dispatchdate = $row->cells [2]->value;
				$post = $row->cells [3]->value;				
				$propertytype = $row->cells [4]->value;
				$volume = CommonComponent::getVolumeCft($id)+CommonComponent::getCratingVolumeCft($id);
				$fromlocation = CommonComponent::getCityName($row->cells [24]->value);
				$tolocation = CommonComponent::getCityName($row->cells [25]->value);
				$vehicletype = $row->cells [6]->value;
				$loadtype = $row->cells [7]->value;
				$ratecardtypeId = $row->cells [9]->value;
				$destinationelevator = $row->cells [10]->value;
				$origineleavtor = $row->cells [11]->value;
				$originstorage = $row->cells [12]->value;
				$originhandymanservice = $row->cells [13]->value;				
				$insurance = $row->cells [14]->value;
				$escort = $row->cells [15]->value;
				$mobility = $row->cells [16]->value;
				$property= $row->cells [17]->value;
				$setting_service = $row->cells [18]->value;
				$insurance_industry = $row->cells [19]->value;				
				$origin_destination = $row->cells [20]->value;
				$destination_handyman_services = $row->cells [21]->value;
				$transaction_id = $row->cells [22]->value;
                                $buyer_id = $row->cells [23]->value;
				if($propertytype!="" && $propertytype!=0) {
					$propType = $row->cells [4]->value;
				} else {
					$propType = '---';
				}
				if($volume!="" || $volume!=0) {
					$vol =$volume;
					//$vol = CommonComponent::getVolumeCft($id)+CommonComponent::getCratingVolumeCft($id);
				} else {
					$vol = '---';
				}
				if($vehicletype!="" || $vehicletype!=0) {
					$vehicle = $row->cells [6]->value;
				} else {
					$vehicle = '---';
				}
				if($loadtype!="" || $loadtype!=0) {
					$loadcat = $row->cells [7]->value;
				} else {
					$loadcat = '---';
				}	
							
				if (isset($destinationelevator) &&  $destinationelevator==1) {
					$destelev= 'checked=checked';
				} else {
					$destelev= 'disabled=disabled';
				}				
				if (isset($destinationelevator) &&  $destinationelevator==0) {
					$destelevno= 'checked=checked';
				} else {
					$destelevno= 'disabled=disabled';
				}
				//Check origin and destination elevator and checkbox checkeing
				if (isset($origineleavtor) &&  $origineleavtor==1) {
					$origdev= 'checked=checked';
				} else {
					$origdev= 'disabled=disabled';
				}
				if (isset($origineleavtor) &&  $origineleavtor==0) {
					$origdevno= 'checked=checked';
				} else {
					$origdevno= 'disabled=disabled';
				}				
				if (isset($originstorage) &&  $originstorage==1) {
					$origin_storage= 'checked=checked';
				} else {
					$origin_storage= 'disabled=disabled';
				}
				//checkbox checking value set or not
				if (isset($originstorage) &&  $originstorage==1) {
					$origin_storage= 'checked=checked';
				} else {
					$origin_storage= 'disabled=disabled';
				}
				if (isset($originhandymanservice) &&  $originhandymanservice==1) {
					$originhandy_manservice= 'checked=checked';
				} else {
					$originhandy_manservice= 'disabled=disabled';
				}				
				if (isset($insurance) &&  $insurance==1) {
					$originhandy_insurance= 'checked=checked';
				} else {
					$originhandy_insurance= 'disabled=disabled';
				}
				if (isset($escort) &&  $escort==1) {
					$originhandy_escort= 'checked=checked';
				} else {
					$originhandy_escort= 'disabled=disabled';
				}
				if (isset($mobility) &&  $mobility==1) {
					$originhandy_mobility= 'checked=checked';
				} else {
					$originhandy_mobility= 'disabled=disabled';
				}
				if (isset($property) &&  $property==1) {
					$originhandy_property= 'checked=checked';
				} else {
					$originhandy_property= 'disabled=disabled';
				}
				if (isset($setting_service) &&  $setting_service==1) {
					$originhandy_setting_service = 'checked=checked';
				} else {
					$originhandy_setting_service= 'disabled=disabled';
				}
				if (isset($insurance_industry) &&  $insurance_industry==1) {
					$originhandy_insurance_industrye = 'checked=checked';
				} else {
					$originhandy_insurance_industrye= 'disabled=disabled';
				}
				
				if (isset($origin_destination) &&  $origin_destination==1) {
					$destdestination_storage = 'checked=checked';
				} else {
					$destdestination_storage= 'disabled=disabled';
				}
				if (isset($destination_handyman_services) &&  $destination_handyman_services==1) {
					$destination_handyman = 'checked=checked';
				} else {
					$destination_handyman = 'disabled=disabled';
				}
				
				
				//commented by swathi 02-05-2016 count increasing from ajax
				//$viewcount = CommonComponent::viewCountForBuyer(Auth::User ()->id,$id,'relocation_buyer_post_views');
				//end comments				
				//Check Query for count no of room items in details section.
				$masterdata = '--';
				$bedroom1 = '--';
				$bedroom2 = '--';
				$bedroom3 = '--';
				$lobby = '--';
				$kitchen = '--';
				$bathroom = '--';
				$drawingroom = '--';
				$getrooms  = DB::table('relocation_buyer_post_inventory_particulars as rebip')
				->leftjoin ( 'lkp_inventory_rooms as itr', 'itr.id', '=', 'rebip.lkp_inventory_room_id' )
				->groupBy('rebip.lkp_inventory_room_id')
				->where('rebip.buyer_post_id',$id)->select('lkp_inventory_room_id')->get();		
				//echo "<pre>"; print_r($getrooms);
				foreach($getrooms as $getroom){
					$getroomsdata  = DB::table('relocation_buyer_post_inventory_particulars as rebip')
					->leftjoin ( 'lkp_inventory_rooms as itr', 'itr.id', '=', 'rebip.lkp_inventory_room_id' )
					->where('itr.id',$getroom->lkp_inventory_room_id)
					->where('rebip.buyer_post_id',$id)
					->select('rebip.lkp_inventory_room_id',DB::raw('sum(rebip.number_of_items) AS totalItems'))
					->get();					
					//echo "<pre>"; print_r($getroomsdata);					
					foreach($getroomsdata as $getdata){
						//echo $getdata->lkp_inventory_room_id;
						if ($getdata->lkp_inventory_room_id == 1) {
							$masterdata = $getdata->totalItems;
						}
						if ($getdata->lkp_inventory_room_id == 2) {					
							$bedroom1 = $getdata->totalItems;
						} 
						if ($getdata->lkp_inventory_room_id == 3) {
							$bedroom2 = $getdata->totalItems;
						}
						if ($getdata->lkp_inventory_room_id == 4) {
							$bedroom3 = $getdata->totalItems;
						}
						if ($getdata->lkp_inventory_room_id == 5) {
							$lobby = $getdata->totalItems;
						}
						if ($getdata->lkp_inventory_room_id == 6) {
							$kitchen = $getdata->totalItems;
						}
						if ($getdata->lkp_inventory_room_id == 7) {
							$bathroom = $getdata->totalItems;
						}
						if ($getdata->lkp_inventory_room_id == 8) {
							$drawingroom = $getdata->totalItems;
						}
					}
				}
				$sellercomponent = new RelocationSellerComponent();
				$submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User ()->id,$id);
				$enquiry = $sellercomponent::getBuyerpostById($id);
            	$submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted";
				$row->cells [8]->value.="<div class='table-row inner-block-bg'>
										<div class='col-md-2 padding-left-none'>
											<span class='lbl padding-8'></span>
											$buyerbussinessname
											<div class='red rating-margin'>
												<i class='fa fa-star'></i>
												<i class='fa fa-star'></i>
												<i class='fa fa-star'></i>
											</div>
										</div>
										<div class='col-md-2 padding-left-none'>".CommonComponent::checkAndGetDate($dispatchdate)."</div>
										<div class='col-md-1 padding-none'>$post</div>
										<div class='col-md-2 padding-none'>$propType</div>
										<div class='col-md-1 padding-none'>$vol<input type='hidden' value='".$vol."' name='enquiry_volume_".$id."' id='enquiry_volume_".$id."'></div>
										<div class='col-md-1 padding-none'>$vehicle <input type='hidden' value='".$enquiry->lkp_vehicle_category_id."' name='vehicle_type_".$id."' id=vehicle_type_".$id."'></div>
										<div class='col-md-1 padding-none'>$loadcat</div>
										<div class='col-md-2 padding-none'><button class='detailsslide-term btn red-btn pull-right submit-data' id ='$id'>".$submitedquotetext."</button></div>";
										
										if ($ratecardtypeId == 1) {
				$row->cells [8]->value.="<div class='clearfix'></div>
										<div class='pull-right text-right'>
											<div class='info-links'>
												<a class='show-data-link' id='$id'><span class='show-icon'>+</span><span class='hide-icon'>-</span> Details</a>
												<a href='#' data-userid='".$buyer_id."' data-buyer-transaction='".$transaction_id."' class='new_message' data-buyerquoteitemidforseller='".$id."'><i class='fa fa-envelope-o'></i></a>
											</div>
										</div>";
										}


				$SubmitquotePartial = view('relocation.sellers.submit_quote')->with([
												'submittedquote' => $submittedquote,
												'enquiry'=>$enquiry,
												'id' => $id,
												'ratecard_type' => $enquiry->lkp_post_ratecard_type_id,
												'is_search' => 1,
												'search_params' => $_REQUEST

											])->render();

										$row->cells [8]->value.="<div class='col-md-12  padding-none padding-top term_quote_details_$id' style='display:none'>
											$SubmitquotePartial
								 		</div>

										<div class='col-md-12 show-data-div padding-top spot_transaction_details_view_list' id='spot_transaction_details_view_'".$id."'>
												<div class='table-div table-style1'>
												<h3>
													<i class='fa fa-map-marker'></i> $fromlocation to $tolocation
													<span class='close-icon'>x</span>
												</h3>
												
												<!-- Table Head Starts Here -->
												<div class='table-heading inner-block-bg'>
													<div class='col-md-2 padding-left-none'>Particulars</div>
													<div class='col-md-1 padding-left-none text-center'>Master Bedroom</div>
													<div class='col-md-1 padding-left-none text-center'>Bedroom 1</div>
													<div class='col-md-1 padding-left-none text-center'>Bedroom 2</div>
													<div class='col-md-1 padding-left-none text-center'>Bedroom 3</div>
													<div class='col-md-2 padding-left-none text-center'>Lobby / Garrage / Store Room</div>
													<div class='col-md-1 padding-left-none text-center'>Kitchen / Dinning</div>
													<div class='col-md-2 padding-left-none text-center'>Bathroom</div>
													<div class='col-md-1 padding-left-none text-center'>Living / Drawing Room</div>													
												</div>

												<!-- Table Head Ends Here -->

												<div class='table-data'>													

													<!-- Table Row Starts Here -->
													<div class='table-row inner-block-bg'>
														<div class='col-md-2 padding-left-none medium-text'>No of Items</div>
														<div class='col-md-1 padding-left-none text-center'>$masterdata</div>
														<div class='col-md-1 padding-left-none text-center'>$bedroom1</div>
														<div class='col-md-1 padding-left-none text-center'>$bedroom2</div>
														<div class='col-md-1 padding-left-none text-center'>$bedroom3</div>
														<div class='col-md-2 padding-left-none text-center'>$lobby</div>
														<div class='col-md-1 padding-left-none text-center'>$kitchen</div>
														<div class='col-md-2 padding-left-none text-center'>$bathroom</div>
														<div class='col-md-1 padding-left-none text-center'>$drawingroom</div>														
													</div>
													<!-- Table Row Ends Here -->
												</div>
											</div>	

											<div class='margin-top'>
												<div class='col-md-4 form-control-fld margin-top'>
													<div class='radio-block'>
														<span class='padding-right-15'>Origin Elevator</span> 
														<input type='radio' $origdev name='elevator1' id='elevator1_a'>
														<label for='elevator1_a'><span></span>Yes</label>
															
														<input type='radio' $origdevno name='elevator1' id='elevator1_b'>
														<label for='elevator1_b'><span></span>No</label>
													</div>
												</div>
												<div class='col-md-4 form-control-fld margin-top'>
													<div class='radio-block'>
														<span class='padding-right-15'>Destination Elevator</span> 
														<input type='radio' $destelev name='elevator2' id='elevator2_a'>
														<label for='elevator2_a'><span></span>Yes</label>
															
														<input type='radio' $destelevno name='elevator2' id='elevator2_b'>
														<label for='elevator2_b'><span></span>No</label>
													</div>
												</div>
												<div class='clearfix'></div>

												<div class='col-md-4 form-control-fld'>
													<div class='radio-block'><input type='checkbox' $origin_storage > <span class='lbl padding-8'>Storage</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_manservice> <span class='lbl padding-8'>Handyman Services</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_insurance> <span class='lbl padding-8'>Insurance</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_escort> <span class='lbl padding-8'>Escort</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_mobility> <span class='lbl padding-8'>Mobility</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_property> <span class='lbl padding-8'>Property</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_setting_service> <span class='lbl padding-8'>Setting Service</span></div>
													<div class='radio-block'><input type='checkbox' $originhandy_insurance_industrye> <span class='lbl padding-8'>Insurance Domestic</span></div>
												</div>
												<div class='col-md-4 form-control-fld'>
													<div class='radio-block'><input type='checkbox' $destdestination_storage> <span class='lbl padding-8'>Storage</span></div>
													<div class='radio-block'><input type='checkbox' $destination_handyman> <span class='lbl padding-8'>Handyman Services</span></div>
												</div>
											</div>											
								 		</div>
									</div>";	
			} );
								
			$result = array ();
			$result ['gridBuyer'] = $gridBuyer;
			//$result ['filter'] = $filter;
			return $result;
		
		} catch ( Exception $exc ) {
		}
	}

	/**
	 * @param $id
	 */
	public static function SellerPostDetails($id){
		Session::put('seller_post_item', $id);
		$postinfo = array();
		$postDetails = DB::table('relocation_seller_posts')->select ( '*')->where('id', $id)->get();
		$postItemDetails = DB::table('relocation_seller_post_items')->select ( '*')->where('seller_post_id', $id)->get();
		//echo "<pre>";print_R($postDetails);print_R($postItemDetails);die;
		$postinfo['seller_post'] = $postDetails;
		$postinfo['seller_post_items'] = $postItemDetails;
		return $postinfo;
	}

	public static function getPrivateBuyers($id,$lkp_access_id){
     if($lkp_access_id == 2 || $lkp_access_id == 3){
			$privatebuyers  = DB::table('relocation_seller_selected_buyers')
				->leftjoin ( 'relocation_seller_posts', 'relocation_seller_posts.id', '=', 'relocation_seller_selected_buyers.seller_post_id' )
				->leftjoin('users','users.id','=','relocation_seller_selected_buyers.buyer_id')
				->leftjoin('buyer_details','buyer_details.user_id','=','users.id')
				->where('relocation_seller_selected_buyers.created_by',Auth::user()->id)
				->where('relocation_seller_selected_buyers.seller_post_id',$id)
				->select('users.username')
				->get();
			return $privatebuyers;
		}else{
			return array();
		}
	}

	/**
	 * Submitting Seller Initial Quote
	 *
	 * @param  $request
	 * @return Response
	 */
	public static function DomesticSellerQuoteSubmit($request) {

		try{
			$roleId = Auth::User()->lkp_role_id;
			if($roleId == SELLER){
				CommonComponent::activityLog("SELLER_SUBMIT_QUOTE",
					SELLER_SUBMIT_QUOTE,0,
					HTTP_REFERRER,CURRENT_URL);
			}
			$sellerInput = Input::all();
			//print_r($sellerInput);exit;
			//if(isset($_REQUEST['from_location_id'])){
				$buyerId = $_REQUEST['buyerid'];
				/*$getSellerpost  = DB::table('relocation_seller_posts')
					->where('relocation_seller_posts.from_location_id','=',$_REQUEST['from_location_id'])
					->where('relocation_seller_posts.to_location_id','=',$_REQUEST['to_location_id'])
					->where('relocation_seller_posts.rate_card_type','=',$_REQUEST['post_rate_card_type'])
					->where('relocation_seller_posts.lkp_post_status_id','=',OPEN)
					->where('relocation_seller_posts.created_by','=',Auth::user()->id)
					->select('relocation_seller_posts.id')
					->first();

				if(count($getSellerpost)>0){
					$_REQUEST['seller_post_item_id'] = $getSellerpost->id;
					$getSellersellectedbuyer    = DB::table('relocation_seller_selected_buyers')
						->where('relocation_seller_selected_buyers.seller_post_id','=',$getSellerpost->id)
						->where('relocation_seller_selected_buyers.buyer_id','=',$buyerId)
						->where('relocation_seller_selected_buyers.created_by','=',Auth::user()->id)
						->select('relocation_seller_selected_buyers.id')
						->first();
					if(count($getSellersellectedbuyer)==0) {
						date_default_timezone_set("Asia/Kolkata");
						$created_at = date('Y-m-d H:i:s');
						$createdIp = $_SERVER['REMOTE_ADDR'];
						$sellerbuyerselect = new RelocationSellerSelectedBuyer();
						$sellerbuyerselect->seller_post_id = $getSellerpost->id;
						$sellerbuyerselect->buyer_id = $buyerId;
						$sellerbuyerselect->created_by = Auth::user()->id;
						$sellerbuyerselect->created_at = $created_at;
						$sellerbuyerselect->created_ip = $createdIp;
						$sellerbuyerselect->save();
					}
				}else{*/
				
					if(isset($sellerInput['seller_post_item_id'])){
						$getSellerpostdetails   = DB::table('relocation_seller_post_items')
												->leftjoin('relocation_seller_posts','relocation_seller_posts.id','=','relocation_seller_post_items.seller_post_id')
												->where('relocation_seller_posts.id','=',$sellerInput['seller_post_item_id'])
												->where('relocation_seller_posts.created_by','=',Auth::user()->id)
												->select('relocation_seller_posts.*','relocation_seller_post_items.*')
												->get();
					}
					
					//Seller post create
					$postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
					$created_year = date('Y');
					if(Session::get('service_id') == RELOCATION_DOMESTIC){
						$randnumber = 'RD/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
					}

					date_default_timezone_set("Asia/Kolkata");
					$created_at = date ( 'Y-m-d H:i:s' );
					$nowdate = date('Y-m-d');
					$Date1 = date('Y-m-d', strtotime($nowdate. " + 1 days"));
					$createdIp = $_SERVER['REMOTE_ADDR'];
					$createsellerpost = new RelocationSellerPost();

					$createsellerpost->lkp_service_id = RELOCATION_DOMESTIC;
					$createsellerpost->from_date = $nowdate;
					$createsellerpost->to_date =$Date1;
					$createsellerpost->rate_card_type =$sellerInput ['post_rate_card_type'];
					$createsellerpost->from_location_id = $_POST ['from_location_id'];
					$createsellerpost->to_location_id = $_POST ['to_location_id'];
					$createsellerpost->seller_district_id = CommonComponent::getDistrict($_POST ['from_location_id'],RELOCATION_DOMESTIC);
					$createsellerpost->packing_loading = 0;
					$createsellerpost->crating_charges = $_POST ['creating_charges'];
					$createsellerpost->unloading_delivery_unpack = 0;
					$createsellerpost->storate_charges = $_POST ['storage_charges'];
					$createsellerpost->escort_charges = $_POST ['escort_charges'];
					$createsellerpost->handyman_charges = $_POST ['handyman_charges'];
					$createsellerpost->property_search = $_POST ['property_search'];
					$createsellerpost->setting_service = 0;
					$createsellerpost->brokerage = $_POST ['brokerage_charges'];
					$createsellerpost->total_inventory_volume = 0;
					if(!isset($sellerInput['seller_post_item_id'])){
					$createsellerpost->tracking = $sellerInput['tracking'];
						if($sellerInput['paymentoptions'] == 1){
							$createsellerpost->lkp_payment_mode_id = 1;
							$createsellerpost->accept_payment_netbanking = 1;
							$createsellerpost->accept_payment_credit = 1;
							$createsellerpost->accept_payment_debit = 1;
						}else if($sellerInput['paymentoptions'] == 2){
							$createsellerpost->lkp_payment_mode_id = 2;
						}else if($sellerInput['paymentoptions'] == 3){
							$createsellerpost->lkp_payment_mode_id = 3;
						}else{
							if(!isset($sellerInput['credit_peroid'])){
								$createsellerpost->lkp_payment_mode_id = 4;
								$createsellerpost->accept_credit_netbanking = 1;
							}else{
								$createsellerpost->lkp_payment_mode_id = 4;
								$createsellerpost->accept_credit_netbanking = 1;
								$createsellerpost->accept_credit_cheque = 1;
								$createsellerpost->credit_period = $sellerInput['credit_peroid'];
								$createsellerpost->credit_period_units = $sellerInput['credit_period_units'];
							}
						}
						$createsellerpost->terms_conditions = "";
					}
					else{
					
						$createsellerpost->tracking = $getSellerpostdetails[0]->tracking;
						$createsellerpost->terms_conditions = $getSellerpostdetails[0]->terms_conditions;
						$createsellerpost->lkp_payment_mode_id = $getSellerpostdetails[0]->lkp_payment_mode_id;
						$createsellerpost->accept_payment_netbanking = $getSellerpostdetails[0]->accept_payment_netbanking;
						$createsellerpost->accept_payment_credit = $getSellerpostdetails[0]->accept_payment_credit;
						$createsellerpost->accept_payment_debit = $getSellerpostdetails[0]->accept_payment_debit;
						$createsellerpost->credit_period = $getSellerpostdetails[0]->credit_period;
						$createsellerpost->credit_period_units = $getSellerpostdetails[0]->credit_period_units;
						$createsellerpost->accept_credit_netbanking = $getSellerpostdetails[0]->accept_credit_netbanking;
						$createsellerpost->accept_credit_cheque = $getSellerpostdetails[0]->accept_credit_cheque;
						
						
					}
					$createsellerpost->seller_id = Auth::user()->id;
					$createsellerpost->lkp_post_status_id = 2;
					$createsellerpost->cancellation_charge_price = '0.00';
					$createsellerpost->docket_charge_price = '0.00';
					$createsellerpost->transaction_id = $randnumber;
					$createsellerpost->lkp_access_id = 3;
					$createsellerpost->created_at = $created_at;
					$createsellerpost->created_by = Auth::user()->id;
					$createsellerpost->created_ip = $createdIp;
					$createsellerpost->save();

					//Post Item create
					$sellerpost_lineitem = new RelocationSellerPostItem();
					$sellerpost_lineitem->seller_post_id = $createsellerpost->id;
					$sellerpost_lineitem->rate_card_type = $_POST['post_rate_card_type'];
					if($_POST['post_rate_card_type'] == 1){
						$sellerpost_lineitem->lkp_property_type_id = $_REQUEST['property_type'];
						$sellerpost_lineitem->volume = $_REQUEST['enquiry_volume'];
						$sellerpost_lineitem->lkp_load_category_id = $_REQUEST['load_category'];
						$sellerpost_lineitem->rate_per_cft = $_REQUEST['ratepercft'];
					}else{
						$sellerpost_lineitem->lkp_vehicle_category_id = $_REQUEST['vehicle_type'];
						$sellerpost_lineitem->lkp_car_size = $_REQUEST['car_size'];
						$sellerpost_lineitem->cost = $_REQUEST['ratepercft'];
					}
					$sellerpost_lineitem->transitdays = $_REQUEST['transport_days'];
					$sellerpost_lineitem->units = "Days";
					$sellerpost_lineitem->transport_charges = $_REQUEST['transport_charges'];
					$sellerpost_lineitem->is_private = 1;
					$created_at = date ( 'Y-m-d H:i:s' );
					$createdIp = $_SERVER ['REMOTE_ADDR'];
					$sellerpost_lineitem->created_by = Auth::id ();
					$sellerpost_lineitem->created_at = $created_at;
					$sellerpost_lineitem->created_ip = $createdIp;
					$sellerpost_lineitem->save ();
					SellerMatchingComponent::insetOrUpdateMatches(RELOCATION_DOMESTIC, $createsellerpost->id, 2, array($sellerInput['buyerquote_id']));

					//Private buyer selection
					$created_at = date('Y-m-d H:i:s');
					$createdIp = $_SERVER['REMOTE_ADDR'];
					$sellerbuyerselect = new RelocationSellerSelectedBuyer();
					$sellerbuyerselect->seller_post_id = $createsellerpost->id;
					$sellerbuyerselect->buyer_id = $buyerId;
					$sellerbuyerselect->created_by = Auth::user()->id;
					$sellerbuyerselect->created_at = $created_at;
					$sellerbuyerselect->created_ip = $createdIp;
					$sellerbuyerselect->save();
					$_REQUEST['seller_post_item_id'] = $createsellerpost->id;
				//}
			//}


			if(isset($sellerInput['buyerquote_id']) && !empty($sellerInput['buyerquote_id'])) {

				date_default_timezone_set("Asia/Kolkata");
				$created_at = date ( 'Y-m-d H:i:s' );
				$initial_cretaed = date ( 'Y-m-d H:i:s' );
				$createdIp = $_SERVER['REMOTE_ADDR'];
				$sellerinitialquote = new RelocationBuyerQuoteSellersQuotesPrice();
				$sellerinitialquote->lkp_service_id = RELOCATION_DOMESTIC;
				$sellerinitialquote->buyer_id = $_REQUEST['buyerid'];;
				$sellerinitialquote->buyer_quote_id = $_REQUEST['buyerquote_id'];
				$sellerinitialquote->seller_id =Auth::user()->id;
				$sellerinitialquote->seller_post_id =(isset($_REQUEST['seller_post_item_id']) ? $_REQUEST['seller_post_item_id'] : 0);
				$sellerinitialquote->private_seller_quote_id =$sellerpost_lineitem->id;
				$sellerinitialquote->post_lead_type_id =1;
				$sellerinitialquote->rate_per_cft = $_REQUEST['ratepercft'];
				$sellerinitialquote->vehicle_charge = "";
				$sellerinitialquote->creating_charges = $_REQUEST['creating_charges'];
				$sellerinitialquote->escort_charges = $_REQUEST['escort_charges'];
				$sellerinitialquote->storage_charges = $_REQUEST['storage_charges'];
				$sellerinitialquote->handyman_charges = $_REQUEST['handyman_charges'];
				$sellerinitialquote->property_search = $_REQUEST['property_search'];
				$sellerinitialquote->brokerage_charges = $_REQUEST['brokerage_charges'];
				$sellerinitialquote->transport_charges = $_REQUEST['transport_charges'];
				$sellerinitialquote->transit_days = $_REQUEST['transport_days'];
				$sellerinitialquote->vehicle_category_id = "";
				$sellerinitialquote->total_price = $_REQUEST['total_price'];

				$sellerinitialquote->save();
				//CommonComponent::auditLog($sellerinitialquote->id,'courier_buyer_quote_sellers_quotes_prices');

				$seller_initial_quote_email = DB::table('users')->where('id', $_REQUEST['buyerid'])->get();
				$seller_initial_quote_email[0]->sellername = Auth::User()->username;

				CommonComponent::send_email(INITIAL_COUNT_BY_SELLER,$seller_initial_quote_email);
				
				//*******Send Sms to the buyers,from seller submit a quote ***********************//
				if(isset($getSellerpostdetails[0]->transaction_id)){
					$msg_params = array(
						'randnumber' => $getSellerpostdetails[0]->transaction_id,
						'sellername' => Auth::User()->username,
						'servicename' => 'RELOCATION DOMESTIC'
					);
					$getMobileNumber  =   CommonComponent::getMobleNumber($buyerId);
					CommonComponent::sendSMS($getMobileNumber,SELLER_SUBMITT_QOUTE_SMS,$msg_params);
				}


				//*******Send Sms to the buyers,from seller submit a quote ***********************//
				
				

			}

			Session::put('message', 'Quote submitted successfully');

		} catch( Exception $e ) {
			return $e->message;
		}
		return true;
		//return Redirect::back();
	}

	public static function getSellerpostEnquiries($postId,$type){
		if($type == 1){
			$matchedposts = SellerMatchingComponent::getMatchedResults(RELOCATION_DOMESTIC,$postId);
		}else{
			$matchedposts = SellerMatchingComponent::getSellerLeads(RELOCATION_DOMESTIC,$postId);
		}

		if(count($matchedposts) > 0){
			$buyerposts = array();
			foreach($matchedposts as $matchedpost){
				$buyerposts[] = $matchedpost->buyer_quote_id;
			}

			$Query_buyers_for_sellers = DB::table('relocation_buyer_posts as rbq');
			$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id' );
			$Query_buyers_for_sellers->join ( 'lkp_cities as ct', 'rbq.to_location_id', '=', 'ct.id' );
			$Query_buyers_for_sellers->join('lkp_post_ratecard_types as rt', 'rt.id', '=', 'rbq.lkp_post_ratecard_type_id');
			$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'rbq.buyer_id');
			$Query_buyers_for_sellers->leftjoin('lkp_property_types as pty', 'pty.id', '=', 'rbq.lkp_property_type_id');
			$Query_buyers_for_sellers->leftjoin('lkp_vechicle_categorie_types as vct', 'vct.id', '=', 'rbq.lkp_vehicle_category_type_id');
			$Query_buyers_for_sellers->leftjoin('lkp_load_categories as lcat', 'lcat.id', '=', 'rbq.lkp_load_category_id');
			$Query_buyers_for_sellers->leftjoin ( 'relocation_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id' );
			//$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', OPEN);

			$Query_buyers_for_sellers->whereIn('rbq.id', $buyerposts);
			$Query_buyers_for_sellers->select ('rbq.*','us.username','cf.city_name as fromcity','ct.city_name as tocity','rt.ratecard_type','pty.property_type','pty.volume','vct.lkp_vechicle_categorie_type','lcat.load_category');
			$Query_buyers_for_sellers->groupBy('rbq.id');
			$results = $Query_buyers_for_sellers->get();

			return $results;
		}
	}

	public static function getBuyerpostById($postId){


			$Query_buyers_for_sellers = DB::table('relocation_buyer_posts as rbq');
			$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id' );
			$Query_buyers_for_sellers->join ( 'lkp_cities as ct', 'rbq.to_location_id', '=', 'ct.id' );
			$Query_buyers_for_sellers->join('lkp_post_ratecard_types as rt', 'rt.id', '=', 'rbq.lkp_post_ratecard_type_id');
			$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'rbq.buyer_id');
			$Query_buyers_for_sellers->leftjoin('lkp_property_types as pty', 'pty.id', '=', 'rbq.lkp_property_type_id');
			$Query_buyers_for_sellers->leftjoin('lkp_vechicle_categorie_types as vct', 'vct.id', '=', 'rbq.lkp_vehicle_category_type_id');
			$Query_buyers_for_sellers->leftjoin('lkp_load_categories as lcat', 'lcat.id', '=', 'rbq.lkp_load_category_id');
			$Query_buyers_for_sellers->leftjoin ( 'relocation_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id' );
			$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', OPEN);

			$Query_buyers_for_sellers->where('rbq.id', $postId);
			$Query_buyers_for_sellers->select ('rbq.*','us.username','cf.city_name as fromcity','ct.city_name as tocity','rt.ratecard_type','pty.property_type','pty.volume','vct.lkp_vechicle_categorie_type','lcat.load_category','lcat.id as load_category_id');
			$Query_buyers_for_sellers->groupBy('rbq.id');
			$results = $Query_buyers_for_sellers->get();
			return $Query_buyers_for_sellers->first();

	}

	public static function getSellerSubmittedQuote($seller_id,$buyerquoteId,$sellerPostId=0){
		$Query_buyers_for_sellers = DB::table('relocation_buyer_quote_sellers_quotes_prices as sqbqp')
										->where("sqbqp.seller_id",$seller_id)
										->where("sqbqp.buyer_quote_id",$buyerquoteId);
		if($sellerPostId != 0){
			//$Query_buyers_for_sellers->where("sqbqp.seller_post_id",$sellerPostId);
		}
		$data = $Query_buyers_for_sellers->get();
		return $data;
	}

}
