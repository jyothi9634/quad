<?php

namespace App\Components\RelocationOffice;

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
use App\Models\RelocationofficeSellerSelectedBuyer;
use App\Models\RelocationofficeBuyerQuoteSellersQuotesPrice;
use App\Models\RelocationofficeSellerPost;

class RelocationOfficeSellerComponent {
	
	/**
	 * Relocation Seller Posts List Page - Grid and filters
	 * Retrieval of data related to seller posts list items to populate in the seller list widget
	 * Displays a grid with a list of all seller posts
	 */
	public static function RelocationSpotSellerPosts($statusId, $serviceId, $roleId,$type) {
		if(isset($_REQUEST['page'])){//echo $_REQUEST['page'];
		}		
		$from_locations = array(""=>"City");
		
		
		// query to retrieve seller posts list and bind it to the grid
		$Query = DB::table ( 'relocationoffice_seller_posts as rsp' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rsp.lkp_post_status_id' );	
		$Query->join ( 'lkp_cities as cf', 'rsp.from_location_id', '=', 'cf.id' );
		$Query->join ( 'lkp_quote_accesses as qa', 'rsp.lkp_access_id', '=', 'qa.id' );
		if(isset($_REQUEST['lead_name']) && ($_REQUEST['lead_name'] ==2)){
			Session::put('leads', '2');
			$Query->where('rsp.lkp_access_id',1);
		}
		else{
			Session::put('leads', '1');
			$Query->leftjoin ( 'relocation_buyer_selected_sellers as rbqss', 'rbqss.seller_id', '=', 'rsp.created_by' );			
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
		
		
		$sellerresults = $Query->select ( 'rsp.id', 'rsp.from_date',
				'rsp.to_date','rsp.lkp_access_id','rsp.lkp_post_status_id','rsp.from_location_id',
				'cf.city_name as fromLocation','qa.quote_access as quoteAccessType',
				'ps.post_status as postStatus'
		)
		->groupBy('rsp.id')
		->get ();
		//Functionality to handle filters based on the selection starts
		foreach($sellerresults as $seller){
			$seller_post_items  = DB::table('relocationoffice_seller_posts')
			->where('relocationoffice_seller_posts.id',$seller->id)
			->select('*')
			->get();
			foreach($seller_post_items as $seller_post_item){
				if(!isset($from_locations[$seller_post_item->from_location_id])){
					$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
				}
											
			}
		}
		// filters Order By from locations
		$from_locations = CommonComponent::orderArray($from_locations);

		//echo "<pre>"; print_r($sellerresults); die;		
		$grid = DataGrid::source ( $Query );	
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'fromLocation', 'From', 'fromLocation' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'quoteAccessType', 'Post Type', 'quoteAccessType' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'postStatus', 'Status', 'postStatus' )->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
		$grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );	
	
		$grid->row ( function ($row) {
            
            $sellerPostId = $row->cells [0]->value;
            $fromDate = $row->cells [1]->value;
          	$toDate = $row->cells [2]->value;
            $fromLcoation = $row->cells [3]->value;
            $postType = $row->cells [4]->value;
            $status = $row->cells [5]->value;
            
            $row->cells [0]->style ( 'display:none' );
            $row->cells [1]->style ( 'display:none' );
            $row->cells [2]->style ( 'display:none' );
            $row->cells [3]->style ( 'display:none' );
            $row->cells [4]->style ( 'display:none' );
            $row->cells [5]->style ( 'display:none' );
         
            
            if($status == 'Draft')
            	$data_link = url()."/relocation/updatesellerpost/$sellerPostId";
            else
            	$data_link = url()."/sellerpostdetail/$sellerPostId";       
            
			$row->cells [6]->value .= "<div class=''><a href='$data_link'>
										<div class='col-md-2 padding-left-none'>".CommonComponent::checkAndGetDate($fromDate)."</div>
										<div class='col-md-3 padding-left-none'>".CommonComponent::checkAndGetDate($toDate)."</div>										
										<div class='col-md-3 padding-left-none'>$fromLcoation</div>
										<div class='col-md-2 padding-none'>$postType</div>
										<div class='col-md-1 padding-none'> $status </div></a>";
			if ($status == 'Open' || $status == 'Draft') {
			$row->cells [6]->value .= "<div class='col-md-1 padding-none text-right'>
										<a href='javascript:void(0)' onclick='relocationsellerpostcancel($sellerPostId)'><i class='fa fa-trash' title='Delete'></i></a>
										</div>";
			}

			$enquiriesCount = SellerMatchingComponent::getMatchedResults(RELOCATION_OFFICE_MOVE,$sellerPostId);
			$leadsCount = SellerMatchingComponent::getSellerLeads(RELOCATION_OFFICE_MOVE,$sellerPostId);
			//view count for sellers
			$viewcount = CommonComponent::getSellersViewcountFromTable($sellerPostId,'relocationoffice_seller_post_views');
			$msg_count  =    MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$sellerPostId);
			
			$row->cells [6]->value .= "<div class='clearfix'></div>
										<div class='pull-left'>
											<div class='info-links'>												
												<a href='/sellerpostdetail/$sellerPostId?type=messages'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>".count($msg_count['result'])."</span></a>
												<a href='/sellerpostdetail/$sellerPostId?type=enquiries'><i class='fa fa-file-text-o'></i> Enquiries<span class='badge'>".count($enquiriesCount)."</span></a>
												<a href='/sellerpostdetail/$sellerPostId?type=leads'><i class='fa fa-bullseye'></i> Leads<span class='badge'>".count($leadsCount)."</span></a>
												<a href='javascript:void(0);'><i class='fa fa-line-chart'></i> Market Analytics<span class='badge'>0</span></a>
												<a href='javascript:void(0);'><i class='fa fa-file-text-o'></i> Documentation<span class='badge'>0</span></a>												
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
		$from_locations = array(""=>"City");
		
		
		// query to retrieve seller posts list and bind it to the grid
		$Query = DB::table ( 'relocationoffice_buyer_posts as rbp' );		
		$Query->leftjoin ( 'relocationoffice_buyer_selected_sellers as rbss', 'rbss.buyer_post_id', '=', 'rbp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rbp.lkp_post_status_id' );
		$Query->join ( 'lkp_cities as cf', 'rbp.from_location_id', '=', 'cf.id' );
		$Query->join ( 'lkp_quote_accesses as qa', 'rbp.lkp_quote_access_id', '=', 'qa.id' );
		$Query->leftjoin ( 'relocation_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbp.id' );
		$Query->leftjoin ('users as us', 'us.id', '=', 'rbp.buyer_id');
		$Query->where('rbss.seller_id',Auth::user()->id);
		//$Query->where('rbp.lkp_post_status_id', OPEN);
		$Query->whereIn('rbp.lkp_post_status_id',array(2,3,4,5));		
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
	
		
	
		$sellerresults = $Query->select ( 'rbp.*','us.username','cf.city_name as fromcity')
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
			if(isset($to_locations))
			$to_locations = CommonComponent::orderArray($to_locations);
			//echo "<pre>"; print_R(Session::get('layered_filter_loadtype')); die;
			//echo "<pre>"; print_R($sellerresults); die;
			
			Session::put('RelcoationRequestData', $sellerresults);			
			
			$grid = DataGrid::source ( $sellerresults );
			$grid->add ( 'id', 'ID', true )->style ( "display:none" );
			$grid->add ( 'username', 'Buyer Name', false )->attributes(array("class" => "col-md-3 padding-left-none"));
			$grid->add ( 'dispatch_date', 'Dispatch Date', false )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'fromcity', 'City', false )->attributes(array("class" => "col-md-3 padding-left-none"));
			$grid->add ( 'volume', 'Volume (CFT)', false )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'test', 'Below Grid', true )->style ( "display:none" );		
			$grid->add ( 'transaction_id', 'transaction_id', 'transaction_id' )->style ( "display:none" );
            $grid->add ( 'created_by', 'Created By', 'created_by' )->style ( "display:none" );
			$grid->add ( 'from_location_id', 'From Location', 'from_location_id' )->style ( "display:none" );
			$grid->add ( 'distance', 'Distance', 'distance' )->style ( "display:none" );
			

			$grid->orderBy ( 'id', 'desc' );
			$grid->paginate ( 5 );
		
			$grid->row ( function ($row) {
				$row->cells [0]->style ( 'display:none' );
				$row->cells [1]->style ( 'display:none' );
				$row->cells [2]->style ( 'display:none' );
				$row->cells [3]->style ( 'display:none' );
				$row->cells [5]->style ( 'display:none' );
				$row->cells [6]->style ( 'display:none' );
				$row->cells [7]->style ( 'display:none' );	
				$row->cells [8]->style ( 'display:none' );
				$row->cells [9]->style ( 'display:none' );
				
				$id = $row->cells [0]->value;
				$buyerbussinessname = $row->cells [1]->value;
				$dispatchdate = $row->cells [2]->value;
				$volume = CommonComponent::getOfficeBuyerVolume($id);
				$fromlocation = CommonComponent::getCityName($row->cells [8]->value);
				$transaction_id = $row->cells [5]->value;
                $buyer_id = $row->cells [6]->value;
                $distance = $row->cells [9]->value;
                $requestSessiondata=Session::get('RelcoationRequestData');                
                
				
				if($volume!="" && $volume!=0) {
					$vol = $volume;
				} else {
					$vol = '---';
				}
				
				//view count for sellers
				//$viewcount = CommonComponent::viewCountForBuyer(Auth::User ()->id,$id,'relocation_buyer_post_views');
								
				//Check Query for count no of room items in details section.
				
				$getinventory = DB::table('relocationoffice_buyer_post_inventory_particulars as rebip')
				->leftjoin ( 'lkp_inventory_office_particulars as itr', 'itr.id', '=', 'rebip.lkp_inventory_office_particular_id' )
				->where('rebip.buyer_post_id',$id)
				->select('rebip.number_of_items','itr.office_particular_type')->get();				
				
				$sellercomponent = new RelocationOfficeSellerComponent();
				$submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User ()->id,$id);
			    $enquiry = $sellercomponent::getBuyerpostById($id);
			    //echo "<pre>".$id;print_R($enquiry);die;			
            	$submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted";
				$row->cells [4]->value.="<div class='table-row inner-block-bg no-border'>
										<div class='col-md-3 padding-left-none'>
											<span class='lbl padding-8'></span>
											$buyerbussinessname
											<div class='red'>
												<i class='fa fa-star'></i>
												<i class='fa fa-star'></i>
												<i class='fa fa-star'></i>
											</div>
										</div>
										<div class='col-md-2 padding-left-none'>".CommonComponent::checkAndGetDate($dispatchdate)."</div>
										<div class='col-md-3 padding-none'>$fromlocation</div>
										<div class='col-md-2 padding-none'>$vol<input type='hidden' value='".$vol."' name='enquiry_volume_".$id."' id='enquiry_volume_".$id."'></div>
										<div class='col-md-2 padding-none'><button class='detailsslide-term btn red-btn pull-right submit-data' id ='$id'>".$submitedquotetext."</button></div>";
										
				$row->cells [4]->value.="<div class='clearfix'></div>
				<div class='pull-right text-right'>
				<div class='info-links'>
				<a class='show-data-link' id='$id'><span class='show-icon'>+</span><span class='hide-icon'>-</span> Details</a>
				<a href='#' data-userid='".$buyer_id."' data-buyer-transaction='".$transaction_id."' class='new_message' data-buyerquoteitemidforseller='".$id."'><i class='fa fa-envelope-o'></i></a>
											</div>
										</div>";
			$buyerpostdata = array();
			$buyerpostdata['from_location_id'] = $enquiry->from_location_id;
			$buyerpostdata['valid_from'] = $enquiry->dispatch_date;
			$buyerpostdata['valid_to'] = date('Y-m-d', strtotime($enquiry->dispatch_date. " + 1 days"));
			$buyerpostdata['nquiry_volume'] = $vol;
//echo "<pre>";print_R($enquiry);die;
			$SubmitquotePartial = view('relocationoffice.sellers.submit_quote')->with([
												'submittedquote' => $submittedquote,
												'enquiry'=>$enquiry,
												'id' => $id,
												'is_search' => 1,
												'search_params' => $buyerpostdata
										])->render();

				$row->cells [4]->value.="<div class='col-md-12  padding-none padding-top term_quote_details_$id' style='display:none'>
											$SubmitquotePartial
								 		</div>

										<div class='col-md-12 show-data-div padding-top spot_transaction_details_view_list' id='spot_transaction_details_view_'".$id."'>
										<div class='col-md-12 padding-left-none'>
										<span class='data-head'>Approximate Distance : $distance KM</span>
										</div>
												<div class='table-div table-style1'>
												<h3>
													<i class='fa fa-map-marker'></i> $fromlocation
													<span class='close-icon'>x</span>
												</h3>
												
												<!-- Table Head Starts Here -->
												<div class='table-heading inner-block-bg'>
														<div class='col-md-6 padding-left-none'>&nbsp;</div>
														<div class='col-md-6 padding-left-none text-center'>No of Items</div>
														
													
													</div>
												<!-- Table Head Ends Here -->";
				foreach($getinventory as $getirtemdata){
				$row->cells [4]->value.="<div class='table-data'>													
												<!-- Table Row Starts Here -->
													<div class='table-row inner-block-bg'>
														<div class='col-md-6 padding-left-none medium-text'>$getirtemdata->office_particular_type</div>
														<div class='col-md-6 padding-left-none text-center'>$getirtemdata->number_of_items</div>
																												
													</div>
													<!-- Table Row Ends Here -->
												</div>";
				}
			   $row->cells [4]->value.="</div></div></div>";	
			} );
								
			//Functionality to build filters in the page starts
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'rbp.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->submit('search');
			$filter->reset('reset');
			$filter->build();
			//Functionality to build filters in the page ends
			$result = array();
			$result['grid'] = $grid;
			$result['filter'] = $filter;
			return $result;
	}
	
	
	public static function getRelocationOfficeSellerSearchResults($roleId, $serviceId,$statusId)
	{	
		try 
		{	
			
			$loadTypeCategory = array (
				//"0" => "Select Seller"
			);
			$propertytype = array (
				//"0" => "Select Seller"
			);
			
			$inputparams = $_REQUEST;
			$Query_buyers_for_sellers = SellerSearchComponent::search ( $roleId, $serviceId, $statusId, $inputparams );
			$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();

			session()->put([
                'searchMod' => [
                    'session_from_location_relocationoffice'  => $_REQUEST['from_location'],
                    'session_from_location_id_relocationoffice' => $_REQUEST['from_location_id'],
                    'session_valid_from_relocationoffice'    => $_REQUEST['valid_from'],
                    'session_valid_to_relocationoffice' => $_REQUEST['valid_to']
                ]
            ]);
            
            if(isset($_REQUEST['seller_district_id']))
                session()->push('searchMod.seller_district_id_relocationoffice', 
                    $_REQUEST['seller_district_id']
                );

			Session::put('seller_searchrequest_officemove',$_REQUEST);

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

				if(isset($_REQUEST['is_search'])){
					if (! isset ( $sellerNames [$seller_post_item->buyer_id] )) {
						$sellerNames[$seller_post_item->buyer_id] = $seller_post_item->username;
					}
					Session::put('layered_filter', $sellerNames);
				}			
			}//echo "<pre>"; print_R(Session::get('layered_filter_loadtype')); die;

			$result = $Query_buyers_for_sellers->get ();			
			//$gridBuyer = DataGrid::source ( $Query_buyers_for_sellers );
			$grid = DataGrid::source ( $Query_buyers_for_sellers );
			$grid->add ( 'id', 'ID', true )->style ( "display:none" );
			$grid->add ( 'username', 'Buyer Name', false )->attributes(array("class" => "col-md-3 padding-left-none"));
			$grid->add ( 'dispatch_date', 'Dispatch Date', false )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'fromcity', 'City', false )->attributes(array("class" => "col-md-3 padding-left-none"));
			$grid->add ( 'volume', 'Total (CFT)', false )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'test', 'Below Grid', true )->style ( "display:none" );		
			$grid->add ( 'transaction_id', 'transaction_id', 'transaction_id' )->style ( "display:none" );
            $grid->add ( 'created_by', 'Created By', 'created_by' )->style ( "display:none" );
			$grid->add ( 'from_location_id', 'From Location', 'from_location_id' )->style ( "display:none" );
			$grid->add ( 'distance', 'Distance', 'distance' )->style ( "display:none" );
			

			$grid->orderBy ( 'id', 'desc' );
			$grid->paginate ( 5 );
		
			$grid->row ( function ($row) {
				$row->cells [0]->style ( 'display:none' );
				$row->cells [1]->style ( 'display:none' );
				$row->cells [2]->style ( 'display:none' );
				$row->cells [3]->style ( 'display:none' );
				$row->cells [5]->style ( 'display:none' );
				$row->cells [6]->style ( 'display:none' );
				$row->cells [7]->style ( 'display:none' );	
				$row->cells [8]->style ( 'display:none' );
				$row->cells [9]->style ( 'display:none' );
				
				$id = $row->cells [0]->value;
				$buyerbussinessname = ucwords($row->cells [1]->value);
				$dispatchdate = $row->cells [2]->value;
				$volume = CommonComponent::getOfficeBuyerVolume($id);
				$fromlocation = CommonComponent::getCityName($row->cells [8]->value);
				$transaction_id = $row->cells [5]->value;
                $buyer_id = $row->cells [7]->value;
                $distance = $row->cells [9]->value;
                $requestSessiondata=Session::get('RelcoationRequestData');                
                
				
				if($volume!="" && $volume!=0) {
					$vol = $volume;
				} else {
					$vol = '---';
				}
				
				//view count for sellers
				//$viewcount = CommonComponent::viewCountForBuyer(Auth::User ()->id,$id,'relocation_buyer_post_views');
								
				//Check Query for count no of room items in details section.
				
				$getinventory = DB::table('relocationoffice_buyer_post_inventory_particulars as rebip')
				->leftjoin ( 'lkp_inventory_office_particulars as itr', 'itr.id', '=', 'rebip.lkp_inventory_office_particular_id' )
				->where('rebip.buyer_post_id',$id)
				->select('rebip.number_of_items','itr.office_particular_type')->get();				
				
				$sellercomponent = new RelocationOfficeSellerComponent();
				$submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User ()->id,$id);
			    $enquiry = $sellercomponent::getBuyerpostById($id);
			    //echo "<pre>".$id;print_R($enquiry);die;			
            	$submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted";
				$row->cells [4]->value.="<div class='table-row inner-block-bg no-border'>
										<div class='col-md-3 padding-left-none'>
											<span class='lbl padding-8'></span>
											$buyerbussinessname
											<div class='red'>
												<i class='fa fa-star'></i>
												<i class='fa fa-star'></i>
												<i class='fa fa-star'></i>
											</div>
										</div>
										<div class='col-md-2 padding-left-none'>".CommonComponent::checkAndGetDate($dispatchdate)."</div>
										<div class='col-md-3 padding-none'>$fromlocation</div>
										<div class='col-md-2 padding-none'>$vol<input type='hidden' value='".$vol."' name='enquiry_volume_".$id."' id='enquiry_volume_".$id."'></div>
										<div class='col-md-2 padding-none'><button class='detailsslide-term btn red-btn pull-right submit-data detailsslide-office' id ='$id' rel='".$buyer_id."_".$id."'>".$submitedquotetext."</button></div>";
										
				$row->cells [4]->value.="<div class='clearfix'></div>
				<div class='pull-right text-right'>
				<div class='info-links'>
				<a class='show-data-link detailsslide-office' id='$id' rel='".$buyer_id."_".$id."'><span class='show-icon'>+</span><span class='hide-icon'>-</span> Details</a>
				<a href='#' data-userid='".$buyer_id."' data-buyer-transaction='".$transaction_id."' class='new_message' data-buyerquoteitemidforseller='".$id."'><i class='fa fa-envelope-o'></i></a>
											</div>
										</div>";
			$buyerpostdata = array();
			$buyerpostdata['from_location_id'] = $enquiry->from_location_id;
			$buyerpostdata['valid_from'] = $enquiry->dispatch_date;
			$buyerpostdata['valid_to'] = date('Y-m-d', strtotime($enquiry->dispatch_date. " + 1 days"));
			$buyerpostdata['nquiry_volume'] = $vol;
//echo "<pre>";print_R($enquiry);die;
			$SubmitquotePartial = view('relocationoffice.sellers.submit_quote')->with([
												'submittedquote' => $submittedquote,
												'enquiry'=>$enquiry,
												'id' => $id,
												'is_search' => 1,
												'search_params' => $buyerpostdata
										])->render();

				$row->cells [4]->value.="<div class='col-md-12  padding-none padding-top term_quote_details_$id' style='display:none'>
											$SubmitquotePartial
								 		</div>

										<div class='col-md-12 show-data-div padding-top spot_transaction_details_view_list' id='spot_transaction_details_view_'.$id>
										<div class='col-md-12 padding-left-none'>
										<span class='data-head'>Approximate Distance : $distance KM</span>
										</div>
												<div class='table-div table-style1'>
												<h3>
													<i class='fa fa-map-marker'></i> $fromlocation
													<span class='close-icon'>x</span>
												</h3>
												
												<!-- Table Head Starts Here -->
												<div class='table-heading inner-block-bg'>
														<div class='col-md-6 padding-left-none'>&nbsp;</div>
														<div class='col-md-6 padding-left-none text-center'>No of Items</div>
														
													
													</div>
												<!-- Table Head Ends Here -->";
				foreach($getinventory as $getirtemdata){
				$row->cells [4]->value.="<div class='table-data'>													
												<!-- Table Row Starts Here -->
													<div class='table-row inner-block-bg'>
														<div class='col-md-6 padding-left-none medium-text'>$getirtemdata->office_particular_type</div>
														<div class='col-md-6 padding-left-none text-center'>$getirtemdata->number_of_items</div>
																												
													</div>
													<!-- Table Row Ends Here -->
												</div>";
				}
			   $row->cells [4]->value.="</div></div></div>";	
			} );
								
			$result = array ();
			$result ['gridBuyer'] = $grid;
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
		$postDetails = DB::table('relocationoffice_seller_posts')->select ( '*')->where('id', $id)->get();
		$postSlabsDetails = DB::table('relocationoffice_seller_post_slabs')->select ( '*')->where('seller_post_id', $id)->get();
		$postinfo['seller_post'] = $postDetails;
		$postinfo['seller_post_slabs'] = $postSlabsDetails;

		return $postinfo;
	}

	public static function getPrivateBuyers($id,$lkp_access_id){

		if($lkp_access_id == 2 || $lkp_access_id == 3){
			$privatebuyers  = DB::table('relocationoffice_seller_selected_buyers')
				->leftjoin ( 'relocationoffice_seller_posts', 'relocationoffice_seller_posts.id', '=', 'relocationoffice_seller_selected_buyers.seller_post_id' )
				->leftjoin('users','users.id','=','relocationoffice_seller_selected_buyers.buyer_id')
				->leftjoin('buyer_details','buyer_details.user_id','=','users.id')
				->where('relocationoffice_seller_selected_buyers.created_by',Auth::user()->id)
				->where('relocationoffice_seller_selected_buyers.seller_post_id',$id)
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
	public static function OfficeSellerQuoteSubmit($request) {

		try{
			$roleId = Auth::User()->lkp_role_id;
			if($roleId == SELLER){
				CommonComponent::activityLog("SELLER_SUBMIT_QUOTE",
					SELLER_SUBMIT_QUOTE,0,
					HTTP_REFERRER,CURRENT_URL);
			}
			$sellerInput = Input::all();
			
				$buyerId = $_REQUEST['buyerid'];
				$buyerQuoteItemId = $_REQUEST['buyerquote_id'];
				
				
					if(isset($sellerInput['seller_post_item_id'])){
						$getSellerpostdetails   = DB::table('relocationoffice_seller_posts')
												->where('relocationoffice_seller_posts.id','=',$sellerInput['seller_post_item_id'])
												->where('relocationoffice_seller_posts.created_by','=',Auth::user()->id)
												->select('relocationoffice_seller_posts.*')
												->get();
					}
					$getBuyerpostdetails  = DB::table('relocationoffice_buyer_posts')
					->where('relocationoffice_buyer_posts.id','=',$buyerQuoteItemId)
					->where('relocationoffice_buyer_posts.created_by','=',$buyerId)
					->select('relocationoffice_buyer_posts.*')
					->get();
					if(count($getBuyerpostdetails)>0){
						
							$checkdispatch = $getBuyerpostdetails[0]->dispatch_date;
					
							$from = $getBuyerpostdetails[0]->dispatch_date;
							$to = date('Y-m-d', strtotime($from. " + 1 days"));
						
					}
					$nowdate = date('Y-m-d');
					
					if($from<$nowdate){
						$nowdate = $nowdate;
						$to = date('Y-m-d', strtotime($nowdate. " + 1 days"));
					}else{
							
						$nowdate = $from;
					}
					
					
					//Seller post create
					$postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
					$created_year = date('Y');
					if(Session::get('service_id') == RELOCATION_OFFICE_MOVE){
						$randnumber = 'REL-OFF/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
					}

					date_default_timezone_set("Asia/Kolkata");
					$created_at = date ( 'Y-m-d H:i:s' );
					//$nowdate = date('Y-m-d');
					//$Date1 = date('Y-m-d', strtotime($nowdate. " + 1 days"));
					$createdIp = $_SERVER['REMOTE_ADDR'];
					$createsellerpost = new RelocationofficeSellerPost();

					$createsellerpost->lkp_service_id = RELOCATION_OFFICE_MOVE;
					$createsellerpost->from_date = $nowdate;
					$createsellerpost->to_date =$to;
					$createsellerpost->from_location_id = $_POST ['from_location_id'];
					$createsellerpost->seller_district_id = CommonComponent::getDistrict($_POST ['from_location_id'],RELOCATION_OFFICE_MOVE);
					$createsellerpost->total_inventory_volume = 0;
					$createsellerpost->is_private = 1;
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
					
					SellerMatchingComponent::insetOrUpdateMatches(RELOCATION_OFFICE_MOVE, $createsellerpost->id, 2, array($sellerInput['buyerquote_id']));

					//Private buyer selection
					$created_at = date('Y-m-d H:i:s');
					$createdIp = $_SERVER['REMOTE_ADDR'];
					$sellerbuyerselect = new RelocationofficeSellerSelectedBuyer();
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
				$sellerinitialquote = new RelocationofficeBuyerQuoteSellersQuotesPrice();
				$sellerinitialquote->lkp_service_id = RELOCATION_DOMESTIC;
				$sellerinitialquote->buyer_id = $_REQUEST['buyerid'];;
				$sellerinitialquote->buyer_quote_id = $_REQUEST['buyerquote_id'];
				$sellerinitialquote->seller_id =Auth::user()->id;
				$sellerinitialquote->seller_post_id =(isset($_REQUEST['seller_post_item_id']) ? $_REQUEST['seller_post_item_id'] : 0);
				$sellerinitialquote->private_seller_quote_id =$createsellerpost->id;
				$sellerinitialquote->post_lead_type_id =1;
				$sellerinitialquote->doortodoor_charges = $_REQUEST['doortodoor_charges'];
				$sellerinitialquote->cancellation_charges = $_REQUEST['cancellation_charges'];
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
						'servicename' => 'RELOCATION OFFICE'
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
			$matchedposts = SellerMatchingComponent::getMatchedResults(RELOCATION_OFFICE_MOVE,$postId);
		}else{
			$matchedposts = SellerMatchingComponent::getSellerLeads(RELOCATION_OFFICE_MOVE,$postId);
		}

		if(count($matchedposts) > 0){
			$buyerposts = array();
			foreach($matchedposts as $matchedpost){
				$buyerposts[] = $matchedpost->buyer_quote_id;
			}

			$Query_buyers_for_sellers = DB::table('relocationoffice_buyer_posts as rbq');
			$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id' );
			$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'rbq.buyer_id');
			$Query_buyers_for_sellers->leftjoin ( 'relocationoffice_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id' );
			//$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', OPEN);

			$Query_buyers_for_sellers->whereIn('rbq.id', $buyerposts);
			$Query_buyers_for_sellers->select ('rbq.*','us.username','cf.city_name as fromcity');
			$Query_buyers_for_sellers->groupBy('rbq.id');
			$results = $Query_buyers_for_sellers->get();

			return $results;
		}
	}

	public static function getBuyerpostById($postId){


			$Query_buyers_for_sellers = DB::table('relocationoffice_buyer_posts as rbq');
			$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id' );
			$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'rbq.buyer_id');
			$Query_buyers_for_sellers->leftjoin ( 'relocation_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id' );
			//$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', OPEN);
			$Query_buyers_for_sellers->whereIn('rbq.lkp_post_status_id',array(2,3,4,5));	

			$Query_buyers_for_sellers->where('rbq.id', $postId);
			$Query_buyers_for_sellers->select ('rbq.*','us.username','cf.city_name as fromcity');
			$Query_buyers_for_sellers->groupBy('rbq.id');
			$results = $Query_buyers_for_sellers->get();
			return $Query_buyers_for_sellers->first();

	}

	public static function getSellerSubmittedQuote($seller_id,$buyerquoteId,$sellerPostId=0){
		$Query_buyers_for_sellers = DB::table('relocationoffice_buyer_quote_sellers_quotes_prices as sqbqp')
										->where("sqbqp.seller_id",$seller_id)
										->where("sqbqp.buyer_quote_id",$buyerquoteId);
		if($sellerPostId != 0){
			//$Query_buyers_for_sellers->where("sqbqp.seller_post_id",$sellerPostId);
		}
		$data = $Query_buyers_for_sellers->get();
		return $data;
	}

	/**
	* Fetching Buyer Post inventory particulars
	* Kalyani K / 12052016
	*/	

	public static function getBuyerInventaryParticulars($buyer_post_id){
		$results = array();
		$Query = DB::table ( 'lkp_inventory_office_particulars as lkpiop' );		
		$Query->leftjoin ( 'relocationoffice_buyer_post_inventory_particulars as rbpip',  'rbpip.lkp_inventory_office_particular_id' ,'=', 'lkpiop.id');

        $Query->where( 'rbpip.buyer_post_id', $buyer_post_id);
		$results = $Query->select ('lkpiop.id as lkp_inventory_type_id','lkpiop.office_particular_type','lkpiop.volume','rbpip.crating_required', 'rbpip.number_of_items')->get ();  
		return $results;      
	}


}
