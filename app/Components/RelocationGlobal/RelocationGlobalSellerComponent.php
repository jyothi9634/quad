<?php

namespace App\Components\RelocationGlobal;

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
use App\Components\MessagesComponent;

/**** Global Mobality Models ****/
use App\Models\RelocationgmSellerPost;
use App\Models\RelocationgmSellerPostItem;
use App\Models\RelocationgmSellerSelectedBuyer;
use App\Models\RelocationgmBuyerQuoteSellersQuotesPrice;

class RelocationGlobalSellerComponent {
	
	/**
	 * Relocation Seller Posts List Page - Grid and filters
	 * Retrieval of data related to seller posts list items to populate in the seller list widget
	 * Displays a grid with a list of all seller posts
	 */
	public static function RelocationSpotSellerPosts($statusId, $serviceId, $roleId,$type) {
		if(isset($_REQUEST['page'])){//echo $_REQUEST['page'];
		}		
		$from_locations = array(""=>"Location");
		//$to_locations = array(""=>"Location");
		$post_for = array(""=>"Post For");
		
		// query to retrieve seller posts list and bind it to the grid
		$Query = DB::table ( 'relocationgm_seller_posts as rsp' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rsp.lkp_post_status_id' );	
		$Query->join ( 'lkp_cities as cf', 'rsp.location_id', '=', 'cf.id' );
		
		$Query->join ( 'lkp_quote_accesses as qa', 'rsp.lkp_access_id', '=', 'qa.id' );
		if(isset($_REQUEST['lead_name']) && ($_REQUEST['lead_name'] ==2)){
			Session::put('leads', '2');
			$Query->where('rsp.lkp_access_id',1);
		}
//		else{
//			Session::put('leads', '1');
//			$Query->leftjoin ( 'relocationgm_buyer_selected_sellers as rbqss', 'rbqss.seller_id', '=', 'rspi.created_by' );			
//		}
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
				'rsp.to_date','rsp.lkp_access_id','rsp.lkp_post_status_id','rsp.location_id',
				'cf.city_name as fromLocation','qa.quote_access as quoteAccessType',
				'ps.post_status as postStatus')
		->groupBy('rsp.id')
		->get ();
		//Functionality to handle filters based on the selection starts
		
			foreach($sellerresults as $sellerresult){
				if(!isset($from_locations[$sellerresult->location_id])){
					$from_locations[$sellerresult->location_id] = DB::table('lkp_cities')->where('id', $sellerresult->location_id)->pluck('city_name');
				}							
			}
		
		$from_locations = CommonComponent::orderArray($from_locations);
		//echo "<pre>"; print_r($sellerresults); die;		
		$grid = DataGrid::source ( $Query );	
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'fromLocation', 'Location', 'fromLocation' )->attributes(array("class" => "col-md-3 padding-left-none"));
		//$grid->add ( 'toLocation', 'To', 'toLocation' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
		//$grid->add ( 'rateCatdType', 'Post For', 'rateCatdType' )->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'quoteAccessType', 'Post Type', 'quoteAccessType' )->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'postStatus', 'Status', 'postStatus' )->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
		$grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );	
	
		$grid->row ( function ($row) {
            
            $sellerPostId = $row->cells [0]->value;
            $fromLcoation = $row->cells [1]->value;
          	//$toLcoation = $row->cells [2]->value;
            $fromDate = $row->cells [2]->value;
            $toDate = $row->cells [3]->value;
            //$postFor = $row->cells [5]->value;
            $postType = $row->cells [4]->value;
            $status = $row->cells [5]->value;
            
            $row->cells [0]->style ( 'display:none' );
            $row->cells [1]->style ( 'display:none' );
            $row->cells [2]->style ( 'display:none' );
            $row->cells [3]->style ( 'display:none' );
            $row->cells [4]->style ( 'display:none' );
            $row->cells [5]->style ( 'display:none' );
            //$row->cells [6]->style ( 'display:none' );
            //$row->cells [7]->style ( 'display:none' );
            
            if($status == 'Draft')
            	$data_link = url()."/relocation/updatesellerpost/$sellerPostId";
            else
            	$data_link = url()."/sellerpostdetail/$sellerPostId";       
            
			$row->cells [6]->value .= "<div class=''><a href='$data_link'>										
										<div class='col-md-3 padding-left-none'>$fromLcoation</div>
										<div class='col-md-3 padding-left-none'>".CommonComponent::checkAndGetDate($fromDate)."</div>
										<div class='col-md-3 padding-left-none'>".CommonComponent::checkAndGetDate($toDate)."</div>
										<div class='col-md-1 padding-none'>$postType</div>
										<div class='col-md-1 padding-none'> $status </div></a>";
			if ($status == 'Open' || $status == 'Draft') {
			$row->cells [6]->value .= "<div class='col-md-1 padding-none text-right'>
										<a href='javascript:void(0)' onclick='relocationsellerpostcancel($sellerPostId)'><i class='fa fa-trash' title='Delete'></i></a>
										</div>";
			}

			$enquiriesCount = SellerMatchingComponent::getMatchedResults(RELOCATION_GLOBAL_MOBILITY,$sellerPostId);
			$leadsCount = SellerMatchingComponent::getSellerLeads(RELOCATION_GLOBAL_MOBILITY,$sellerPostId);
			
			//count for seller documents
			$serviceId = Session::get('service_id');
			$docs_seller_domestic    =   CommonComponent::getGsaDocuments(SELLER,$serviceId,$sellerPostId);
			
			//view count for sellers
			$viewcount = CommonComponent::getSellersViewcountFromTable($sellerPostId,'relocationgm_seller_post_views');
			$msg_count  =    MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$sellerPostId);
			
			$row->cells [6]->value .= "<div class='clearfix'></div>
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
			$filter->add ( 'rsp.location_id', 'Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			//$filter->add ( 'rsp.to_location_id', 'From Location', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
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
		$from_locations = array(""=>"Location");
		
		$services_gm=array("" => "Service");
	
		// query to retrieve seller posts list and bind it to the grid
		$Query = DB::table ( 'relocationgm_buyer_posts as rbp' );		
		$Query->leftjoin ( 'relocationgm_buyer_selected_sellers as rbss', 'rbss.buyer_post_id', '=', 'rbp.id' );
                $Query->join('relocationgm_buyer_quote_items as rbqi', 'rbqi.buyer_post_id', '=', 'rbp.id');
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rbp.lkp_post_status_id' );
		$Query->join ( 'lkp_cities as cf', 'rbp.location_id', '=', 'cf.id' );
		$Query->join ( 'lkp_quote_accesses as qa', 'rbp.lkp_quote_access_id', '=', 'qa.id' );
		$Query->leftjoin ( 'relocationgm_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbp.id' );
		$Query->leftjoin ('users as us', 'us.id', '=', 'rbp.buyer_id');
		$Query->where('rbss.seller_id',Auth::user()->id);
		$Query->where('rbp.lkp_post_status_id', OPEN);
		$Query->where('rbp.lkp_quote_access_id',2);
		//conditions to make search
		if(isset($statusId) && $statusId != '' && $statusId != 0){
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
				$Query->where('rbp.dispatch_date', $to);
			}
		}
	
		$sellerresults = $Query->select ( 'rbp.*','us.username','cf.city_name as tocity')
		->groupBy('rbp.id')
		->get ();
		//Functionality to handle filters based on the selection starts
			foreach($sellerresults as $seller_post_item){
				
                            if(!isset($from_locations[$seller_post_item->location_id])){
                                    $from_locations[$seller_post_item->location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->location_id)->pluck('city_name');
                            }
                            $querys = DB::table('relocationgm_buyer_posts as bp');
                            $querys->leftJoin('relocationgm_buyer_quote_items as bqi', 'bqi.buyer_post_id', '=', 'bp.id');
                            $querys->where('bp.id','=',$seller_post_item->id);
                            $service_ids    =   $querys->select('bqi.lkp_gm_service_id')->get();
                            foreach($service_ids as $s_id){
                                if (!isset($services_gm[$s_id->lkp_gm_service_id])) {
                                        $services_gm[$s_id->lkp_gm_service_id] = DB::table('lkp_relocationgm_services')->where('id', $s_id->lkp_gm_service_id)->pluck('service_type');
                                }
                            }
					
				
			}
			$from_locations = CommonComponent::orderArray($from_locations);
                        if(isset($services_gm))
			$services_gm = CommonComponent::orderArray($services_gm);
			//echo "<pre>"; print_R($sellerresults); die;
			
			Session::put('RelcoationRequestData', $sellerresults);			
			
			$grid = DataGrid::source ( $sellerresults );
			$grid->add ( 'id', 'ID', true )->style ( "display:none" );
			$grid->add ( 'username', 'Buyer Name', false )->attributes(array("class" => "col-md-4 padding-left-none"));
			$grid->add ( 'dispatch_date', 'Dispatch Date', false )->attributes(array("class" => "col-md-3 padding-left-none"));
			$grid->add ( 'test', 'Below Grid', true )->style ( "display:none" );		
			$grid->add ( 'transaction_id', 'transaction_id', 'transaction_id' )->style ( "display:none" );
                        $grid->add ( 'created_by', 'Created By', 'created_by' )->style ( "display:none" );
			$grid->add ( 'location_id', 'Location', 'location_id' )->attributes(array("class" => "col-md-3 padding-left-none"));
			
			$grid->orderBy ( 'id', 'desc' );
			$grid->paginate ( 5 );
		
			$grid->row ( function ($row) {
                        $row->cells [0]->style ( 'display:none' );
                        $row->cells [1]->style ( 'display:none' );
                        $row->cells [2]->style ( 'display:none' );
                        //$row->cells [3]->style ( 'display:none' );
                        $row->cells [4]->style ( 'display:none' );
                        $row->cells [5]->style ( 'display:none' );
                        $row->cells [6]->style ( 'display:none' );

                        $id = $row->cells [0]->value;
                        $buyerbussinessname = $row->cells [1]->value;
                        $dispatchdate = $row->cells [2]->value;
                        $tolocation = CommonComponent::getCityName($row->cells [6]->value);
                        $transaction_id = $row->cells [4]->value;
                        $buyer_id = $row->cells [5]->value;

                        $requestSessiondata=Session::get('RelcoationRequestData');                



                        $sellercomponent = new RelocationGlobalSellerComponent();
                        $submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User ()->id,$id);
                        $enquiry = $sellercomponent::getBuyerpostById($id);
                        $buyerquote = $sellercomponent::getBuyerQuoteItems($id);
                    //echo "<pre>".$id;print_R($submittedquote);die;			
                        $submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted";
                        $row->cells [3]->value.="<div class='table-row inner-block-bg no-border'>
                                                                        <div class='col-md-4 padding-left-none'>
                                                                                <span class='lbl padding-8'></span>
                                                                                $buyerbussinessname
                                                                                <div class='red'>
                                                                                        <i class='fa fa-star'></i>
                                                                                        <i class='fa fa-star'></i>
                                                                                        <i class='fa fa-star'></i>
                                                                                </div>
                                                                        </div>
                                                                        <div class='col-md-3 padding-left-none'>".CommonComponent::checkAndGetDate($dispatchdate)."</div>
                                                                        <div class='col-md-3 padding-none'>$tolocation</div>

                                                                        <div class='col-md-2 padding-none'><button class='detailsslide-term btn red-btn pull-right submit-data' id ='$id'>".$submitedquotetext."</button></div>";

                        
                        $row->cells [3]->value.="<div class='clearfix'></div>
                                                <div class='pull-right text-right'>
                                                        <div class='info-links'>
                                                                <a class='show-data-link' id='$id'><span class='show-icon'>+</span><span class='hide-icon'>-</span> Details</a>
                                                                <a href='#' data-userid='".$buyer_id."' data-buyer-transaction='".$transaction_id."' class='new_message' data-buyerquoteitemidforseller='".$id."'><i class='fa fa-envelope-o'></i></a>
                                                        </div>
                                                </div>";
                        
			$buyerpostdata = array();
			$buyerpostdata['to_location_id'] = $enquiry->location_id;
			$buyerpostdata['valid_from'] = $enquiry->dispatch_date;
			$buyerpostdata['valid_to'] = date('Y-m-d', strtotime($enquiry->dispatch_date. " + 1 days"));
			//$buyerpostdata['lkp_vehicle_category_id'] = $enquiry->lkp_vehicle_category_id;
			
//echo "<pre>";print_R($enquiry);die;
			$SubmitquotePartial = view('relocationglobal.sellers.submit_quote')->with([
                                                    'submittedquote' => $submittedquote,
                                                    'enquiry'=>$enquiry,
                                                    'id' => $id,
                                                    'is_search' => 1,
                                                    'search_params' => $buyerpostdata,
                                                    'buyerquote'=>$buyerquote
                                                ])->render();
                        $buyerdetailsPartial = view('relocationglobal.buyers._buyerserviceslist')->with([
                                                    'buyerpost_id' => $id
                                                ])->render();
                        $row->cells [3]->value.="<div class='col-md-12 show-data-div padding-top spot_transaction_details_view_list' id='spot_transaction_details_view_'".$id."'>
                                             $buyerdetailsPartial    
                                        </div>	<div class='col-md-12  padding-none padding-top term_quote_details_$id' style='display:none'>
                                                    $SubmitquotePartial
                                            </div>

                                            


                                            
                                    </div>";	
			} );
								
			//Functionality to build filters in the page starts
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'rbp.location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'rbqi.lkp_gm_service_id', 'Services', 'select')->options($services_gm)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->submit('search');
			$filter->reset('reset');
			$filter->build();
			//Functionality to build filters in the page ends
			$result = array();
			$result['grid'] = $grid;
			$result['filter'] = $filter;
			return $result;
	}
        
        
        /**
     * Seller search buyer posts
     * @author Swathi
     * @param mixed $request
     * @param int $serviceId
     * @return 
     */   
    public static function getRelocationSellerSearchResults($roleId, $serviceId,$statusId) {
		
        try {	
           $buyerNames = array ();
            $inputparams = $_REQUEST;
            $Query_buyers_for_sellers = SellerSearchComponent::search ($roleId=null,$serviceId,$statusId=null, $inputparams);
            $Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();
           
           	session()->put([
                'searchMod' => [
                    'session_to_location_relocation' => $_REQUEST['to_location'],
		            'session_to_location_id_relocation' =>  $_REQUEST['to_location_id'],
		            'session_seller_district_id_relocation' => $_REQUEST['seller_district_id'],
		            'session_valid_from_relocation' => $_REQUEST['valid_from'],
		            'session_valid_to_relocation' => $_REQUEST['valid_to'],
		            'session_service_type_relocation' => $_REQUEST['relgm_service_type'],
		            'session_spot_or_term', $_REQUEST['spot_or_term'],
                ]
            ]);
            
            if (isset($_REQUEST['is_search']) && empty ( $Query_buyers_for_sellers_filter )) {
                //CommonComponent::searchTermsSendMail ();
                Session::put('layered_filter', '');
                Session::put('layered_filter_payments', '');
                Session::put('show_layered_filter','');
                Session::put('layered_filter_loadtype', '');
                Session::put('layered_services_filter', '');
            }	
           if(!isset($_REQUEST['filter_set'])){ 
            // Below script for filter data getting from queries --for filters
            foreach ( $Query_buyers_for_sellers_filter as $seller_post_item ) {				
                
                if (! isset ( $to_locations [$seller_post_item->location_id] )) {
                    $to_locations [$seller_post_item->location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $seller_post_item->location_id )->pluck ( 'city_name' );
                }                    
                if (! isset ( $buyerNames [$seller_post_item->buyer_id] )) {
                        $buyerNames[$seller_post_item->buyer_id] = $seller_post_item->username;
                        Session::put('layered_filter', $buyerNames);
                }
                if(isset($_REQUEST['is_search'])){
                    $bqi=DB::table ( 'relocationgm_buyer_quote_items as bqi' )
                            ->leftjoin('lkp_relocationgm_services as lrs', 'lrs.id', '=', 'bqi.lkp_gm_service_id')
                            ->where('bqi.buyer_post_id','=',$seller_post_item->id)->select('lrs.id','lrs.service_type')->get();
                    foreach ( $bqi as $bqitem ) {
                        $buyerServices[$bqitem->id] = $bqitem->service_type;
                        Session::put('layered_services_filter', $buyerServices);
                    }
                }
                
             }
           	}
           	
           	//$result = $Query_buyers_for_sellers->get ();
            $gridBuyer = \DataGrid::source( $Query_buyers_for_sellers );
            $gridBuyer->add('username', 'Buyer Name', true )->attributes(array("class" => "col-md-3 padding-left-none"));
            $gridBuyer->add('dispatch_date', 'Date', true )->attributes(array("class" => "col-md-2 padding-left-none"));
            $gridBuyer->add('fromcity', 'Location', true )->attributes(array("class" => "col-md-2 padding-left-none"));
            $gridBuyer->add('id', 'Cage Weight', true )->attributes(array("class" => "col-md-2 padding-left-none"))->style("display:none");
            $gridBuyer->add('grid_actions', 'Grid Actions', true)->attributes(array("class" => "col-md-3 padding-none"))->style("display:none");
            $gridBuyer->add('empty_div', 'Empty', true)->attributes(array("class" => ""))->style("display:none");
            $gridBuyer->add('dom_row', 'Dom Actions', true)->attributes(array("class" => "col-md-3 padding-none"))->style("display:none");
            $gridBuyer->add('quote_submit', 'Dom Action 1', true)->attributes(array("class" => "col-md-3 padding-none"))->style("display:none");
            $gridBuyer->add('quote_details', 'Dom Action 2', true)->attributes(array("class" => "col-md-3 padding-none"))->style("display:none");
            
            $gridBuyer->row( function($row) {
                
                //dd($row);
                $id = $row->data->id;
                $buyer_id = $row->data->buyer_id;
                $transaction_id = $row->data->transaction_id;
                    
                $sellercomponent = new RelocationGlobalSellerComponent();
                $submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User()->id, $id);
                $enquiry = $sellercomponent::getBuyerpostById($id);
                $submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted";
                
                
                // Buyer Business name
                $row->cells[0]->attributes(array('class' => 'col-md-3 padding-left-none'))
                        ->value( ucfirst($row->data->username) );
                
                // Dispatch date
                $row->cells[1]->attributes(array('class' => 'col-md-2 padding-left-none'))
                        ->value(CommonComponent::checkAndGetDate($row->data->dispatch_date) );
                
                // Pet type
                $row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))
                        ->value($row->data->fromcity);
                
                // Cage weight
                $row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))
                        ->style("display:none");
                
                // Action Button
                $row->cells[4]->attributes(array('class' => 'col-md-3 pull-right'))
                        ->value("<button class='detailsslide-term btn red-btn pull-right submit-data' id ='$id'>".$submitedquotetext."</button>");
                
                // Empty Div
                $row->cells[5]->attributes(array('class' => 'clearfix'))
                        ->value('');

                // Details & Message Div       
                $row->cells[6]->attributes(array('class' => 'pull-right text-right'))
                	->value = '<div class="info-links">	
                        <span data-buyersearchlistid="'.$buyer_id.'_'.$id.'" id="'.$id.'" class="show-data-link detailsslide underline_link"><span class="show-icon">+</span><span class="hide-icon">-</span> Details</span>
                        <a href="#" data-userid="'.$buyer_id.'" data-buyer-transaction="'.$transaction_id.'" class="new_message" data-buyerquoteitemidforseller="'.$id.'"><i class="fa fa-envelope-o"></i></a>
                    	</div>';
                
                $buyerquote = $sellercomponent::getBuyerQuoteItems($id);
                $SubmitquotePartial = view('relocationglobal.sellers.submit_quote')
                    ->with([
                        'submittedquote' => $submittedquote,
                        'enquiry'=>$enquiry,
                        'id' => $id,                           
                        'is_search' => 1,
                        'search_params' => $_REQUEST,
                        'buyerquote'=>$buyerquote
                    ])->render();
                $buyerdetailsPartial = view('relocationglobal.buyers._buyerserviceslist')->with([
                                                    'buyerpost_id' => $id
                                                ])->render();

                $row->cells[8]->attributes(array('class' => 'col-md-12 submit-data-div padding-none padding-top'))->value = $SubmitquotePartial;
                
                $row->cells[7]->attributes(array('class' => 'col-md-12 show-data-div padding-none padding-top'))->value = $buyerdetailsPartial;
              
                $row->attributes(array("class" => ""));
                
            } );
		
            $gridBuyer->orderBy ( 'id', 'desc' );
            $gridBuyer->paginate ( 5 );
            
                $result = array ();
                $result ['gridBuyer'] = $gridBuyer;
                //$result ['filter'] = $filter;
                return $result;
		
            } catch ( Exception $exc ) {}
	}
        
        public static function getSellerSubmittedQuote($seller_id,$buyerquoteId,$sellerPostId=0){ 
		$Query_buyers_for_sellers = DB::table('relocationgm_buyer_quote_sellers_quotes_prices as sqbqp')
                    ->where("sqbqp.seller_id",$seller_id)
                    ->where("sqbqp.service_quote",'>',0)
                    ->where("sqbqp.buyer_post_id",$buyerquoteId)->select('sqbqp.*');
		if($sellerPostId != 0){
                    //$Query_buyers_for_sellers->where("sqbqp.seller_post_id",$sellerPostId);
		}
		$data = $Query_buyers_for_sellers->get();
		return $data;
	}
        
        public static function getBuyerpostById($postId){

			$Query_buyers_for_sellers = DB::table('relocationgm_buyer_posts as rbq');
			//$Query_buyers_for_sellers->join( 'lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id' );
			$Query_buyers_for_sellers->join ( 'lkp_cities as ct', 'rbq.location_id', '=', 'ct.id' );			
			$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'rbq.buyer_id');			
			$Query_buyers_for_sellers->leftjoin ( 'relocationgm_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id' );
			$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', OPEN);
			$Query_buyers_for_sellers->where('rbq.id', $postId);
			$Query_buyers_for_sellers->select ('rbq.*','us.username','ct.city_name as tocity');
			$Query_buyers_for_sellers->groupBy('rbq.id');
			$results = $Query_buyers_for_sellers->get();
			return $Query_buyers_for_sellers->first();

	}
        
        
        /**
	 * @param $id
	 */
	public static function SellerPostDetails($id){
		Session::put('seller_post_item', $id);
		$postinfo = array();
		$postDetails = DB::table('relocationgm_seller_posts')->select ( '*')->where('id', $id)->first();
                $countview = DB::table('relocationgm_seller_post_views as spv')
				->where('spv.seller_post_id','=',$id)
				->select('spv.id','spv.view_counts')
				->get();
				if(!isset($countview[0]->view_counts))
					$countview = 0;
				else
					$countview = $countview[0]->view_counts;
		//$postItemDetails = DB::table('relocation_seller_post_items')->select ( '*')->where('seller_post_id', $id)->get();
		//echo "<pre>";print_R($postDetails);print_R($postItemDetails);die;
		$postinfo['seller_post'] = $postDetails;
                $postinfo['countview'] = $countview;
		//$postinfo['seller_post_items'] = $postItemDetails;
		return $postinfo;
	}

	public static function getPrivateBuyers($id,$lkp_access_id){
		if($lkp_access_id == 2 || $lkp_access_id == 3){
			$privatebuyers  = DB::table('relocationgm_seller_selected_buyers as rsb')
				->leftjoin ( 'relocationgm_seller_posts as sp', 'sp.id', '=', 'rsb.seller_post_id' )
				->leftjoin('users','users.id','=','rsb.buyer_id')
				->leftjoin('buyer_details','buyer_details.user_id','=','users.id')
				->where('rsb.created_by',Auth::user()->id)
				->where('rsb.seller_post_id',$id)
				->select('users.username')
				->get();
			return $privatebuyers;
		}else{
			return array();
		}
	}
        
        public static function getSellerpostEnquiries($postId,$type){
		if($type == 1){
			$matchedposts = SellerMatchingComponent::getMatchedResults(RELOCATION_GLOBAL_MOBILITY,$postId);
		}else{
			$matchedposts = SellerMatchingComponent::getSellerLeads(RELOCATION_GLOBAL_MOBILITY,$postId);
		}

		if(count($matchedposts) > 0){
			$buyerposts = array();
			foreach($matchedposts as $matchedpost){
				$buyerposts[] = $matchedpost->buyer_quote_id;
			}

			$Query_buyers_for_sellers = DB::table('relocationgm_buyer_posts as rbq');
                        $Query_buyers_for_sellers->leftjoin( 'relocationgm_buyer_quote_items as rbqi', 'rbqi.buyer_post_id', '=', 'rbq.id' );
			$Query_buyers_for_sellers->leftjoin( 'lkp_cities as cf', 'rbq.location_id', '=', 'cf.id' );
			//$Query_buyers_for_sellers->join ( 'lkp_cities as ct', 'rbq.to_location_id', '=', 'ct.id' );
			//$Query_buyers_for_sellers->join('lkp_post_ratecard_types as rt', 'rt.id', '=', 'rbq.lkp_post_ratecard_type_id');
			$Query_buyers_for_sellers->leftjoin('users as us', 'us.id', '=', 'rbq.buyer_id');
			//$Query_buyers_for_sellers->leftjoin('lkp_property_types as pty', 'pty.id', '=', 'rbq.lkp_property_type_id');
			//$Query_buyers_for_sellers->leftjoin('lkp_vechicle_categorie_types as vct', 'vct.id', '=', 'rbq.lkp_vehicle_category_type_id');
			//$Query_buyers_for_sellers->leftjoin('lkp_load_categories as lcat', 'lcat.id', '=', 'rbq.lkp_load_category_id');
			$Query_buyers_for_sellers->leftjoin ( 'relocationgm_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id' );
			//$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', OPEN);

			$Query_buyers_for_sellers->whereIn('rbq.id', $buyerposts);
			$Query_buyers_for_sellers->select ('rbq.*','us.username','cf.city_name as fromcity','rbqi.id as bqid');
			$Query_buyers_for_sellers->groupBy('rbq.id');
			$results = $Query_buyers_for_sellers->get();

			return $results;
		}
	}
        
        
//        public static function getSellerSubmittedQuote($seller_id,$buyerquoteId,$sellerPostId=0){
//		$Query_buyers_for_sellers = DB::table('relocationgm_buyer_quote_sellers_quotes_prices as sqbqp')
//										->where("sqbqp.seller_id",$seller_id)
//										->where("sqbqp.buyer_quote_item_id",$buyerquoteId);
//		if($sellerPostId != 0){
//			//$Query_buyers_for_sellers->where("sqbqp.seller_post_id",$sellerPostId);
//		}
//		$data = $Query_buyers_for_sellers->get();
//		return $data;
//	}
        
        public static function getBuyerQuoteItems($postId){
		

		if(isset($postId)){
			
			
			$Query_buyers_for_sellers = DB::table('relocationgm_buyer_posts as rbq');
            $Query_buyers_for_sellers->leftjoin( 'relocationgm_buyer_quote_items as rbqi', 'rbqi.buyer_post_id', '=', 'rbq.id' );
			$Query_buyers_for_sellers->leftjoin( 'lkp_relocationgm_services as rs', 'rs.id', '=', 'rbqi.lkp_gm_service_id' );
			$Query_buyers_for_sellers->where('rbq.id', $postId);
			$Query_buyers_for_sellers->select ('rbqi.*','rs.service_type');
			$Query_buyers_for_sellers->groupBy('rbqi.id');
			$results = $Query_buyers_for_sellers->get();

			return $results;
		}
	}

	public static function SellerPostServicesDetails($id){
		Session::put('seller_post_item', $id);
		$postinfo = array();
		$postDetails = DB::table('relocationgm_seller_posts')->select ( '*')->where('id', $id)->get();		
		$postinfo['seller_post'] = $postDetails;

		return $postinfo;
	}

	/**
	 * Submitting Seller Initial Quote
	 *
	 * @param  $request
	 * @return Response
	 */
	public static function GmtSellerQuoteSubmit($request) {
		try{
			$roleId = Auth::User()->lkp_role_id;
			if($roleId == SELLER){
				CommonComponent::activityLog("SELLER_SUBMIT_QUOTE",
					SELLER_SUBMIT_QUOTE,0,
					HTTP_REFERRER,CURRENT_URL);
			}

			$sellerInput = Input::all();
			$formvalues = urldecode($_REQUEST['formvalues']);
			$formfields = explode("&", $formvalues);
			$hiddenfields = array();
			
			foreach($formfields as $formfield){
				$input = explode("=", $formfield);
				$hiddenfields[$input[0]] = $input[1]; 
			}

				$buyerId = $_REQUEST['buyerid'];
				
					if(isset($sellerInput['seller_post_item_id'])){
					$getSellerpostdetails   = DB::table('relocationgm_seller_posts')
						->where('relocationgm_seller_posts.id','=',$sellerInput['seller_post_item_id'])
						->where('relocationgm_seller_posts.created_by','=',Auth::user()->id)
						->select('relocationgm_seller_posts.*')
						->get();
					}
									
					//Seller post create
					$postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
					$created_year = date('Y');
					if(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY){
						$randnumber = 'RELELOCATIONGM/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
					}

					date_default_timezone_set("Asia/Kolkata");
					$created_at = date ( 'Y-m-d H:i:s' );
					$nowdate = date('Y-m-d');
					$Date1 = date('Y-m-d', strtotime($nowdate. " + 1 days"));
					$createdIp = $_SERVER['REMOTE_ADDR'];
					$createsellerpost = new RelocationgmSellerPost();

					$createsellerpost->lkp_service_id = RELOCATION_GLOBAL_MOBILITY;
					$createsellerpost->from_date = $nowdate;
					$createsellerpost->to_date =$Date1;
					$createsellerpost->is_private = 1;
					//$createsellerpost->location_id = $hiddenfields['from_location_id_'.$_POST['buyerquote_id']];
					//$createsellerpost->seller_district_id = CommonComponent::getDistrict($hiddenfields['from_location_id_'.$_POST['buyerquote_id']],RELOCATION_GLOBAL_MOBILITY);
                                        $createsellerpost->location_id = $_REQUEST['to_location_id'];
					$createsellerpost->seller_district_id = CommonComponent::getDistrict($_REQUEST['to_location_id'],RELOCATION_GLOBAL_MOBILITY);
					if(!isset($sellerInput['seller_post_item_id'])){
						$createsellerpost->lkp_payment_mode_id = 1;
                                                $createsellerpost->accept_payment_netbanking = 1;
                                                $createsellerpost->accept_payment_credit = 1;
                                                $createsellerpost->accept_payment_debit = 1;
						$createsellerpost->terms_conditions = "";
					}
					else{
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
					$createsellerpost->transaction_id = $randnumber;
					$createsellerpost->lkp_access_id = 3;
					$createsellerpost->created_at = $created_at;
					$createsellerpost->created_by = Auth::user()->id;
					$createsellerpost->created_ip = $createdIp;

					$quote_items = explode(',',$hiddenfields['quote_ids_'.$_POST['buyerquote_id']]);
					$qcount = count($quote_items);

					for($q=0;$q<$qcount;$q++){
						$quote_id = $quote_items[$q]; 
						$service_name = $hiddenfields['relgm_quote_service_'.$quote_id];
						$quote_amt = $hiddenfields['relgm_quote_'.$quote_id];
						$createsellerpost->$service_name = $quote_amt;
					}	
					$createsellerpost->save();


					//Private buyer selection
					$created_at = date('Y-m-d H:i:s');
					$createdIp = $_SERVER['REMOTE_ADDR'];
					$sellerbuyerselect = new RelocationgmSellerSelectedBuyer();
					$sellerbuyerselect->seller_post_id = $createsellerpost->id;
					$sellerbuyerselect->buyer_id = $buyerId;
					$sellerbuyerselect->created_by = Auth::user()->id;
					$sellerbuyerselect->created_at = $created_at;
					$sellerbuyerselect->created_ip = $createdIp;
					$sellerbuyerselect->save();
					//$_REQUEST['seller_post_item_id'] = $createsellerpost->id;


			if(isset($sellerInput['buyerquote_id']) && !empty($sellerInput['buyerquote_id'])) {
				date_default_timezone_set("Asia/Kolkata");

				for($q=0;$q<$qcount;$q++){
					$quote_id = $quote_items[$q]; 
					$service_name = $hiddenfields['relgm_quote_service_'.$quote_id];
					$quote_amt = $hiddenfields['relgm_quote_'.$quote_id];

					$created_at = date ( 'Y-m-d H:i:s' );
					$initial_cretaed = date ( 'Y-m-d H:i:s' );
					$createdIp = $_SERVER['REMOTE_ADDR'];
					$sellerinitialquote = new RelocationgmBuyerQuoteSellersQuotesPrice();
					$sellerinitialquote->lkp_service_id = RELOCATION_GLOBAL_MOBILITY;
					$sellerinitialquote->buyer_id = $_REQUEST['buyerid'];
					$sellerinitialquote->buyer_post_id = $_REQUEST['buyerquote_id'];
					$sellerinitialquote->buyer_quote_item_id = $quote_id;
					$sellerinitialquote->seller_post_id =(isset($_REQUEST['seller_post_item_id']) ? $_REQUEST['seller_post_item_id'] : 0);
					$sellerinitialquote->private_seller_quote_id =$createsellerpost->id;
					$sellerinitialquote->service_quote = $quote_amt;
                                        $sellerinitialquote->seller_id = Auth::user()->id;
					$sellerinitialquote->save();
				}	
				//CommonComponent::auditLog($sellerinitialquote->id,'courier_buyer_quote_sellers_quotes_prices');

				$seller_initial_quote_email = DB::table('users')->where('id', $_REQUEST['buyerid'])->get();
				$seller_initial_quote_email[0]->sellername = Auth::User()->username;

				CommonComponent::send_email(INITIAL_COUNT_BY_SELLER,$seller_initial_quote_email);
				
					//*******Send Sms to the buyers,from seller submit a quote ***********************//
					if(isset($getSellerpostdetails[0]->transaction_id)){
						
						  $servicename="RELOCATION GLOBAL MOBILITY";
						$msg_params = array(
							'randnumber' => $getSellerpostdetails[0]->transaction_id,
							'sellername' => Auth::User()->username,
							'servicename' => $servicename
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

}
