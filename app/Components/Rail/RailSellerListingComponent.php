<?php
namespace App\Components\Rail;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Request;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use App\Components\CommonComponent;
use App\Models\User;
use App\Models\FtlSearchTerm;
use App\Models\PtlSellerPostItemView;
use App\Components\Matching\SellerMatchingComponent;
use App\Components\MessagesComponent;
use App\Components\SellerComponent;

use Redirect;
use PhpParser\Node\Stmt\Else_;

class RailSellerListingComponent {
	
	/**
	 * Submitting Seller Initial Quote
	 *	
	 * @param  $request
	 * @return Response
	 */
	public static function RailSellerList($statusId, $roleId, $serviceId) {
	
		// Filters values to populate in the page
		$fromselect=array();
		$toselect=array();
		$ptlLocationWise = array (
			"" => "Select"
		);
		$ptlFromLocationPincode = array (
			"" => "From Location-Pincode"
		);
		$ptlToLocationPincode = array (
			"" => "To Location-Pincode"
		);
		$ptlFromLocationZone = array (
			"" => "From Zone"
		);
		$ptlToLocationZone = array (
			"" => "To Zone"
		);

		$load_types = array(""=>"Load Type");
		// query to retrieve seller posts list and bind it to the grid
		$Query = DB::table ( 'rail_seller_posts as psp' );
		$Query->leftjoin ( 'rail_seller_post_items as pspi', 'pspi.seller_post_id', '=', 'psp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'psp.lkp_post_status_id' );
		if(isset($_REQUEST['lead_name']) && ($_REQUEST['lead_name'] ==2)){
			Session::put('leads', '2');
			$Query->where('psp.lkp_access_id',1);
		}
		else{
			Session::put('leads', '1');
			$Query->leftjoin ( 'rail_buyer_quote_selected_sellers as bqss', 'bqss.seller_id', '=', 'pspi.created_by' );
		}
		$Query->where('psp.seller_id',Auth::user()->id);
		
		//conditions to make search
		if(isset($statusId) && $statusId != ''){
			if($statusId != 0)
				$Query->where ( 'psp.lkp_post_status_id', '=', $statusId );
		}

		if(isset($serviceId) && $serviceId != ''){
			$Query->where('psp.lkp_service_id', $serviceId);
		}
		
		if( isset($_REQUEST['search']) && $_REQUEST['from_date']!=''){
			$from=CommonComponent::convertDateForDatabase($_REQUEST['from_date']);
			 
			//$Query->whereRaw('sp.from_date', $from);
			$Query->whereRaw('psp.from_date >= "'.$from.'"');
		}
		
		if( isset($_REQUEST['search']) && $_REQUEST['to_date']!=''){
			$to=CommonComponent::convertDateForDatabase($_REQUEST['to_date']);
		
			if($_REQUEST['from_date']!=''){
		
		
				$Query->whereBetween('psp.to_date',array($from,$to));
			}else{
				$Query->where('psp.to_date', $to);
			}
				
				
		}
	
		$sellerresults = $Query->select ( 'psp.id', 'psp.from_date',
				'psp.to_date','psp.lkp_access_id','psp.lkp_post_status_id'
		)
		->groupBy('psp.id')
		->get ();
		
		//echo '<pre>';print_r($sellerresults);exit;
		//Functionality to handle filters based on the selection starts
		foreach($sellerresults as $seller){
			$seller_post_items  = DB::table('rail_seller_post_items')
			->join ( 'rail_seller_posts', 'rail_seller_posts.id', '=', 'rail_seller_post_items.seller_post_id' )
			->where('rail_seller_post_items.seller_post_id',$seller->id)
			->where('rail_seller_posts.lkp_ptl_post_type_id',2)
			->select('*')
			->get();
			foreach($seller_post_items as $seller_post_item){
				if (!isset( $ptlFromLocationPincode [$seller_post_item->from_location_id] )) {
					$ptlFromLocationPincode [$seller_post_item->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $seller_post_item->from_location_id )->pluck ( 'pincode' );
				}
				if (!isset( $ptlToLocationPincode [$seller_post_item->to_location_id] )) {
					$ptlToLocationPincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $seller_post_item->to_location_id )->pluck ( 'pincode' );
				}
			}
		}
		$ptlFromLocationPincode = CommonComponent::orderArray($ptlFromLocationPincode);
		$ptlToLocationPincode = CommonComponent::orderArray($ptlToLocationPincode);
		
		foreach($sellerresults as $seller){
			$seller_post_items  = DB::table('rail_seller_post_items')
			->join ( 'rail_seller_posts', 'rail_seller_posts.id', '=', 'rail_seller_post_items.seller_post_id' )
			->where('rail_seller_post_items.seller_post_id',$seller->id)
			->where ( 'rail_seller_posts.lkp_ptl_post_type_id', 1 )
			->select('*')
			->get();
			foreach($seller_post_items as $seller_post_item){
				if (!isset( $ptlFromLocationZone [$seller_post_item->from_location_id] )) {
					$ptlFromLocationZone [$seller_post_item->from_location_id] = DB::table ( 'ptl_zones' )
					->where ( 'id', $seller_post_item->from_location_id )
					->pluck ( 'zone_name' );
				}
				if (!isset( $ptlToLocationZone [$seller_post_item->to_location_id] )) {
					$ptlToLocationZone [$seller_post_item->to_location_id] = DB::table ( 'ptl_zones' )
					->where ( 'id', $seller_post_item->to_location_id )
					->pluck ( 'zone_name' );
				}
			}
		}
		
		
		$posttypes  = DB::table('lkp_ptl_post_types as lp')
		->where(['lp.is_active' => 1])
		->select('lp.id', 'lp.post_type')
		->get();
		$l=1;
		foreach($posttypes as $posttype){
			//echo "hello";
			if($posttype->post_type=='Zone Wise'){
				$location="Zone";
			}
			if($posttype->post_type=='Location Wise'){
				$location="Pincode";
			}
			$ptlLocationWise [$l] = $location;
			$l++;
		}
		
		//Functionality to handle filters based on the selection ends
		$grid = DataGrid::source ( $Query );
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'from_date', 'From', 'from_date' )->attributes(array("class"=>"col-md-3 padding-left-none"));
		$grid->add ( 'to_date', 'To', 'to_date' )->attributes(array("class"=>"col-md-3 padding-left-none"));
		$grid->add ( 'lkp_access_id', 'Post For', 'lkp_access_id' )->attributes(array("class"=>"col-md-2 padding-left-none"));
		$grid->add ( 'lkp_post_status_id', 'Status', 'lkp_post_status_id' )->attributes(array("class"=>"col-md-2 padding-left-none"));
		$grid->add ( 'below_grid', 'Below Grid', true )->attributes(array("class"=>"col-md-2 padding-left-none"))->style ( "display:none" );
		$grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );
		$grid->row ( function ($row) {
			
			$row->cells [0]->style ( 'display:none' );
			//$row->cells [4]->style ( 'display:none' );
			$spId = $row->cells [0]->value;
			//$row->cells [1]->value ( '<div class="col-md-3 col-sm-2 col-xs-4 padding-none"><input type="checkbox" name="sellerpostcheck" id="sellerpostcheck" class="checkBoxClass" value='.$spId.'></div>' );
			$frmdate = $row->cells [1]->value;
			$frmdate = date('d/m/Y', strtotime($frmdate));
			$row->cells [1]->value = '<span><input type="checkbox" name="sellerpostcheck" id="sellerpostcheck" class="checkBoxClass gridcheckbox" value='.$spId.'></span>'.$frmdate;
			$todate = $row->cells [2]->value;
			$todate = date('d/m/Y', strtotime($todate));
			$row->cells [2]->value = $todate;
			$poststatus = $row->cells [4]->value;
			
			if($poststatus == 1)
				$data_link = url()."/ptl/updatesellerpost/$spId";
			else
				$data_link = url()."/sellerposts/$spId";
			$row->cells [1]->attributes(array("class" => "col-md-3 padding-left-none html_link","data_link"=>$data_link));
			$row->cells [2]->attributes(array("class" => "col-md-3 padding-left-none html_link","data_link"=>$data_link));
			$row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none html_link","data_link"=>$data_link));
			$row->cells [4]->attributes(array("class" => "col-md-2 padding-none"));
			
               $row->cells [3]->value = CommonComponent::getQuoteAccessById($row->cells [3]->value);			
			$val = CommonComponent::getSellerPostStatuss($row->cells [4]->value);
				
				
			if($row->cells [4]->value == 1 )
				$row->cells [4]->value = "<a href='../ptl/updatesellerpost/$spId'>$val</a>";
			else
				$row->cells [4]->value = $val;
			
			
			$getpostitemids = DB::table('rail_seller_post_items')
			->where('rail_seller_post_items.seller_post_id','=',$spId)
			->select('rail_seller_post_items.*')
				->get();
			if(isset($getpostitemids[0]->is_private)){
				$privatepost = $getpostitemids[0]->is_private;
			}else{
				$privatepost = 0;
			}
			
			$Ptlseller_post_items  = DB::table('rail_seller_post_items')
			->join('rail_seller_posts','rail_seller_posts.id','=','rail_seller_post_items.seller_post_id')
			->where('rail_seller_post_items.seller_post_id',$spId)
			->select('*')
			->get();
				
			if(isset($Ptlseller_post_items[0]->lkp_payment_mode_id)){	
			$seller_lkp_payment_mode_id = $Ptlseller_post_items[0]->lkp_payment_mode_id;
			$seller_payment_mode_method = CommonComponent::getSellerPostPaymentMethod($seller_lkp_payment_mode_id);
            }else{
                $seller_payment_mode_method='N/A';
            }
            if(isset($Ptlseller_post_items[0]->tracking)){	
			$tracking_seller = $Ptlseller_post_items[0]->tracking;			
               $tracking_seller_post = CommonComponent::getTrackingType($tracking_seller);
            }else{
                $tracking_seller_post = 'N/A';
            }
			
			
            //count for seller documents
            $serviceId = Session::get('service_id');
            $docs_seller_rail    =   CommonComponent::getGsaDocuments(SELLER,$serviceId,$spId);
            
            
			$allcountview =0;
			if(count($getpostitemids)>0){
				for($i=0;$i<count($getpostitemids);$i++){
			
					$countview = DB::table('rail_seller_post_item_views')
					->where('rail_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
					->select('rail_seller_post_item_views.id','rail_seller_post_item_views.view_counts')
					->get();
					if(isset($countview[0]->view_counts))
						$allcountview +=  $countview[0]->view_counts;
			
				}
			}
			
			$seller_post_items  = DB::table('rail_seller_post_items')
			->join ( 'rail_seller_posts', 'rail_seller_posts.id', '=', 'rail_seller_post_items.seller_post_id' )
			->where('rail_seller_post_items.seller_post_id',$spId)
			->select('rail_seller_post_items.*','rail_seller_posts.lkp_ptl_post_type_id','rail_seller_post_items.from_location_id','rail_seller_post_items.to_location_id')
			->get();
			//Enquieries count
			$total_count=0;
			if($privatepost==1){
			
				$total_count = count(CommonComponent::getPrivateBuyerMatchedResults(RAIL,$seller_post_items[0]->seller_post_id));
			
			}
			else{
				if(isset($seller_post_items[0]->from_location_id) && isset($seller_post_items[0]->to_location_id) && isset($seller_post_items[0]->lkp_ptl_post_type_id)){
					for($k=0;$k<count($seller_post_items);$k++)
						//$total_count += PtlSellerListingComponent::enquiryCount(2,$seller_post_items[$k]->from_location_id,$seller_post_items[$k]->to_location_id,$seller_post_items[$k]->lkp_ptl_post_type_id );
						$total_count += count(SellerMatchingComponent::getMatchedResults(RAIL, $seller_post_items[$k]->id));
				}
				else
					$total_count =0;
			}
			
			//Leads Count
			$lead_count=0;
			if($privatepost==1){
				$lead_count=0;
			}else{
				if(isset($seller_post_items[0]->from_location_id) && isset($seller_post_items[0]->to_location_id) && isset($seller_post_items[0]->lkp_ptl_post_type_id)){
					for($k=0;$k<count($seller_post_items);$k++)
						//$total_count += PtlSellerListingComponent::enquiryCount(2,$seller_post_items[$k]->from_location_id,$seller_post_items[$k]->to_location_id,$seller_post_items[$k]->lkp_ptl_post_type_id );
					$lead_count += count(SellerMatchingComponent::getSellerLeads(RAIL, $seller_post_items[$k]->id));
				}else
					$lead_count =0;
			}
			//$msg_count  =    MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$spId);
			$msg_count=0;
                        for($k=0;$k<count($seller_post_items);$k++){
                            if (isset($seller_post_items[$k]->id)) {
                                $potitemId = $seller_post_items[$k]->id;
                                $msgs  =    MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$potitemId);
                                $msg_count+=    count($msgs['result']);
                            }
                        }
                        
			if($poststatus !=5) {
			$row->cells [5]->value = '
					<div class="col-md-2 padding-none text-right">
						<a href="javascript:void(0)" data-target="#cancelsellerpostmodal" data-toggle="modal" onclick="setcancelpostid(\'posts\','.$spId.')" >
						<i class="fa fa-trash" title="Delete"></i>
						</a>
					</div>';
			}		
					$row->cells [5]->value .= '<div class="clearfix"></div>
					<div class="pull-left">
						<div class="info-links">
							<a ><i class="fa fa-envelope-o"></i> Messages <span class="badge">'.$msg_count.'</span></a>
							<a >
								<i class="fa fa-file-text-o"></i>Enquiries
								<span class="badge">';
			$row->cells [5]->value .=$total_count;
			$row->cells [5]->value .='
								</span>
							</a>
							<a><i class="fa fa-bullseye"></i> Leads<span class="badge">'.$lead_count.'</span></a>
							<a><i class="fa fa-line-chart"></i> Market Analytics</a>
							<a><i class="fa fa-file-text-o"></i> Documentation <span class="badge">'.count($docs_seller_rail).'</span></a>
						</div>
					</div>
					<div class="pull-right text-right">
						<div class="info-links">';
			if ($tracking_seller != 0){
							$row->cells [5]->value .='<a href="'.$data_link.'"><i class="fa fa-signal"></i>'.$tracking_seller_post.'</a>';
							}
							$row->cells [5]->value .='<a href="'.$data_link.'"><i class="fa fa-rupee"></i>'.$seller_payment_mode_method.'</a>
							<a><span class="views red"><i class="fa fa-eye" title="Views"></i>';
			if($row->cells [4]->value ==1)
				$row->cells [5]->value .='0';
			else
				$row->cells [5]->value .=$allcountview;
			$row->cells [5]->value .='
							</span></a>
						</div>
					</div>';
			
			//$row->attributes(array("class" => "col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none table-row  mobile-padding-none"));
			
		} );
		//Functionality to build filters in the page starts

		$from_id='';
		$to_id='';
		$filter = DataFilter::source ( $Query );
		if(isset($_REQUEST['search']) && $_REQUEST['lkp_ptl_post_type_id']==1){

			$fromselect=$ptlFromLocationZone;
			$toselect=$ptlToLocationZone;

			Session::put('loc_type', '1');

		}elseif(isset($_REQUEST['search']) && $_REQUEST['lkp_ptl_post_type_id']==2){
			$fromselect=$ptlFromLocationPincode;
			//print_r($fromselect);
			$toselect=$ptlToLocationPincode;

			Session::put('loc_type', '1');

		}else{

			Session::put('loc_type', '');
			$from_id='';
			$to_id='';

		}
		$filter->add ( 'pspi.lkp_ptl_post_type_id', '', 'select' )->options ( $ptlLocationWise )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "changeLocType(this.value)" );
		$filter->add ( 'pspi.from_location_id', '', 'select' )->options ( $fromselect )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'pspi.to_location_id', '', 'select' )->options ( $toselect)->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'pspi.lkp_load_type_id', 'Load Type', 'select')->options($load_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
		//$filter->add ( 'psp.from_date', 'From', 'date' )->attr("class","filter_calendar");
		//$filter->add ( 'psp.to_date', 'To', 'date' )->attr("class","filter_calendar");
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
	 * FTL Seller Post Details List Page.
	 *
	 * @param
	 *        	$request
	 * @return Response
	 */
	
	public static function listRailSellerPostItems($statusId, $roleId, $serviceId, $id){
		try{
	
			//Filters values to populate in the page
			$from_locationspincode = array(""=>"From Location");
			$to_locationspincode = array(""=>"To Location");
			$from_locationszone = array(""=>"From Location");
			$to_locationszone = array(""=>"To Location");

			$load_types = array(""=>"Load Type");
			
			$lkppoststatus  = DB::table('rail_seller_posts')
			->where('rail_seller_posts.id','=',$id)
			->select('rail_seller_posts.lkp_ptl_post_type_id')
					->get();
			
			Session::put('lkppoststatus', $lkppoststatus[0]->lkp_ptl_post_type_id);
			$Query = DB::table ( 'rail_seller_posts as sp' );
			$Query->leftjoin ( 'rail_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
			$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'sp.lkp_post_status_id' );
			if($lkppoststatus[0]->lkp_ptl_post_type_id ==1 ){
				$Query->leftjoin('ptl_zones','ptl_zones.id','=','spi.from_location_id');
			}
			else{
				$Query->leftjoin('lkp_ptl_pincodes','lkp_ptl_pincodes.id','=','spi.from_location_id');
			}
				
			
			if(Session::get('leads') &&  Session::get('leads')==2){
				Session::put('leads', '2');
				
				$Query->where('sp.lkp_access_id',1);
			}
			else{
				Session::put('leads', '1');
				
				$Query->leftjoin ( 'rail_buyer_quote_sellers_quotes_prices as bqss', 'bqss.seller_id', '=', 'spi.created_by' );
			}
			$Query->where('sp.seller_id',Auth::user()->id);
			$Query->where('spi.seller_post_id',$id);
			//conditions to make search
			if(isset($statusId) && $statusId != ''){
				$Query->where('sp.lkp_post_status_id', $statusId);
			}
			if(isset($serviceId) && $serviceId != ''){
				$Query->where('sp.lkp_service_id', $serviceId);
			}
			if($lkppoststatus[0]->lkp_ptl_post_type_id == 1 ){
				$sellerresults = $Query->select ( 'spi.id', 'spi.from_location_id','spi.to_location_id','spi.price',
						'sp.kg_per_cft', 'spi.transitdays' ,'sp.lkp_post_status_id','ptl_zones.zone_name as postoffice_name',
						'sp.lkp_access_id', 'ps.post_status','sp.id as post_id','spi.from_location_id','spi.to_location_id',
						'sp.lkp_ptl_post_type_id','spi.is_cancelled','sp.lkp_post_status_id','spi.is_cancelled');
				
				Session::put('postedType', 'Zone');
				
				$seller_post_items  = DB::table('rail_seller_post_items')
				->join ( 'rail_seller_posts', 'rail_seller_posts.id', '=', 'rail_seller_post_items.seller_post_id' )
				->where('rail_seller_post_items.seller_post_id',$id)
				->where ( 'rail_seller_posts.lkp_ptl_post_type_id', 1 )
				->select('*')
				->get();
				foreach($seller_post_items as $seller_post_item){
					if (!isset( $from_locationszone [$seller_post_item->from_location_id] )) {
						$from_locationszone [$seller_post_item->from_location_id] = DB::table ( 'ptl_zones' )
						->where ( 'id', $seller_post_item->from_location_id )
						->pluck ( 'zone_name' );
					}
					if (!isset( $to_locationszone [$seller_post_item->to_location_id] )) {
						$to_locationszone [$seller_post_item->to_location_id] = DB::table ( 'ptl_zones' )
						->where ( 'id', $seller_post_item->to_location_id )
						->pluck ( 'zone_name' );
					}
				}
			}
			else{
				$sellerresults = $Query->select ( 'spi.id', 'spi.from_location_id','spi.to_location_id','spi.price',
						'sp.kg_per_cft', 'spi.transitdays' ,'sp.lkp_post_status_id','lkp_ptl_pincodes.postoffice_name',
						'sp.lkp_access_id', 'ps.post_status','sp.id as post_id','spi.from_location_id','spi.to_location_id',
						'sp.lkp_ptl_post_type_id','spi.is_cancelled','sp.lkp_post_status_id','spi.is_cancelled');
				
				$seller_post_items  = DB::table('rail_seller_post_items')
				->join ( 'rail_seller_posts', 'rail_seller_posts.id', '=', 'rail_seller_post_items.seller_post_id' )
				->where('rail_seller_post_items.seller_post_id',$id)
				->where ( 'rail_seller_posts.lkp_ptl_post_type_id', 2 )
				->select('*')
				->get();
				foreach($seller_post_items as $seller_post_item){
					if (!isset( $from_locationspincode [$seller_post_item->from_location_id] )) {
						$from_locationspincode [$seller_post_item->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )
						->where ( 'id', $seller_post_item->from_location_id )
						->pluck ( 'pincode' );
					}
					if (!isset( $to_locationspincode [$seller_post_item->to_location_id] )) {
						$to_locationspincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )
						->where ( 'id', $seller_post_item->to_location_id )
						->pluck ( 'pincode' );
					}
				}
				
			
			}
			$Query->groupBy('spi.id');
			$Query->get ();
			//Functionality to handle filters based on the selection starts
			
			//Functionality to handle filters based on the selection ends
	
			$grid = DataGrid::source ( $Query );
	
			$grid->add ( 'id', 'ID', true )->style ( "display:none" );
			$grid->add ( 'from_location_id', 'From Location', 'from_location_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'to_location_id', 'To Location', 'to_location_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'price', 'Rate per kg', 'price' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'transitdays', 'Transit Days', 'transitdays' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'kg_per_cft/transitdays', 'Average Market Rate / Transit Time', '' )->style ( "display:none" );
			$grid->add ( 'is_cancelled', 'Status', 'lkp_post_status_id' )->attributes(array("class" => "col-md-1 padding-left-none"));
			$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
			$grid->orderBy ( 'id', 'desc' );
			$grid->paginate ( 5 );
			$grid->row ( function ($row) {
				$row->cells [0]->style ( 'display:none' );
				$spId = $row->cells [0]->value;
				$row->cells[0]->value = '';
				if(Session::get('lkppoststatus') == 1 ){
				$row->cells[1]->value = '<span><input type="checkbox" name="sellerpostcheck" id="sellerpostcheck" class="checkBoxClass gridcheckboxitems" value='.$spId.'></span>'.CommonComponent::getZoneName($row->cells [1]->value);
				}
				else{
				$row->cells[1]->value = '<span><input type="checkbox" name="sellerpostcheck" id="sellerpostcheck" class="checkBoxClass gridcheckboxitems" value='.$spId.'></span>'.CommonComponent::getZonePin($row->cells [1]->value);
				}
				//$row->cells [5]->style ( 'text-align:right' );
				//$row->cells [5]->value = 'N/A' ;
				$row->cells [6]->style ( 'width:100%' );
				//View Count
				$countview = DB::table('rail_seller_post_item_views')
				->where('rail_seller_post_item_views.seller_post_item_id','=',$spId)
				->select('rail_seller_post_item_views.id','rail_seller_post_item_views.view_counts')
				->get();
				if(!isset($countview[0]->view_counts))
					$countview = 0;
				else
					$countview = $countview[0]->view_counts;
	
				if(Session::get('lkppoststatus') == 1 ){
					$row->cells [2]->value = ''.CommonComponent::getZoneName($row->cells [2]->value).'';
				}
				else{
					$row->cells [2]->value = ''.CommonComponent::getZonePin($row->cells [2]->value).'';
				}

				$poststatus = $row->cells [6]->value;
				

				
				
				
				$seller_post_items  = DB::table('rail_seller_post_items')
				->join ( 'rail_seller_posts', 'rail_seller_posts.id', '=', 'rail_seller_post_items.seller_post_id' )
				->where('rail_seller_post_items.id',$spId)
				->select('*','rail_seller_posts.lkp_ptl_post_type_id','rail_seller_posts.id as spid')
				->get();

				if($row->cells [6]->value == 1 )
					$row->cells [6]->value = "Deleted";
				else
					$row->cells [6]->value = "Open";
				
				if($seller_post_items[0]->units == 'Weeks')
					$row->cells [4]->value = $row->cells [4]->value." Weeks";
				else 
					$row->cells [4]->value = $row->cells [4]->value." Days";
                                
                                $row->cells [3]->value = CommonComponent::getPriceType($row->cells [3]->value);
				
				$data_link = url()."/sellerpostdetail/$spId";
				$row->cells [1]->attributes(array("class" => "col-md-2 padding-left-none html_link","data_link"=>$data_link));
				$row->cells [2]->attributes(array("class" => "col-md-2 padding-left-none html_link","data_link"=>$data_link));
				$row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none html_link","data_link"=>$data_link));
				$row->cells [4]->attributes(array("class" => "col-md-2 padding-left-none html_link","data_link"=>$data_link));
				//$row->cells [5]->attributes(array("class" => "col-md-3 padding-none html_link","data_link"=>$data_link));
				$row->cells [6]->attributes(array("class" => "col-md-1 padding-none html_link","data_link"=>$data_link));
				
				
				//count for seller documents
				$serviceId = Session::get('service_id');
				$docs_seller_rail    =   CommonComponent::getGsaDocuments(SELLER,$serviceId,$seller_post_items[0]->spid);
				
				//Enquiries Count
				$total_count = 0;
				if($seller_post_items[0]->is_private == 1){
					$total_count = count(CommonComponent::getPrivateBuyerMatchedResults(RAIL,$seller_post_items[0]->spid));
				}else{
					if (isset($seller_post_items[0]->from_location_id) && isset($seller_post_items[0]->to_location_id) && isset($seller_post_items[0]->lkp_ptl_post_type_id)) {
						//$total_count = PtlSellerListingComponent::enquiryCount(2, $seller_post_items[0]->from_location_id, $seller_post_items[0]->to_location_id, $seller_post_items[0]->lkp_ptl_post_type_id);
						$total_count += count(SellerMatchingComponent::getMatchedResults(RAIL, $spId));
					}else
						$total_count =0;
				}
				//Leads Count
				$lead_count = 0;
				if($seller_post_items[0]->is_private == 1){
					$lead_count = 0;
				}else{
					if (isset($seller_post_items[0]->from_location_id) && isset($seller_post_items[0]->to_location_id) && isset($seller_post_items[0]->lkp_ptl_post_type_id)) {
						$lead_count += count(SellerMatchingComponent::getSellerLeads(RAIL, $spId));
					}else
						$lead_count =0;
				}
				$msg_count  =    MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$spId);
				$row->cells [7]->value .='<div class="clearfix"></div>
								<div class="pull-left">
									<div class="info-links">
										<a href="/sellerpostdetail/'.$spId.'?type=messages"><i class="fa fa-envelope-o"></i> Messages <span class="badge">'.count($msg_count['result']).'</span></a>
										<a href="/sellerpostdetail/'.$spId.'?type=enquiries"><i class="fa fa-file-text-o"></i> Enquiries<span class="badge">'.$total_count.'</span></a>
										<a href="/sellerpostdetail/'.$spId.'?type=leads"><i class="fa fa-bullseye"></i> Leads<span class="badge">'.$lead_count.'</span></a>
										<a href="javascript:void(0)"><i class="fa fa-line-chart"></i> <span class="badge">0</span>Market Analytics</a>
										<a href="/sellerpostdetail/'.$spId.'?type=documentation"><i class="fa fa-file-text-o"></i> Documentation <span class="badge">'.count($docs_seller_rail).'</span></a>
									</div>
								</div>
								<div class="pull-right text-right">
									<div class="info-links">';
				if($poststatus !=1) {
					$row->cells [7]->value .= '<a href="javascript:void(0)" data-target="#cancelsellerpostmodal" data-toggle="modal" onclick="setcancelpostid(\'item\','.$spId.')" >';
					$row->cells [7]->value .= '<i class="fa fa-trash" title="Delete"></i>';
					$row->cells [7]->value .= '</a>';
				}
										$row->cells [7]->value .='<a><span class="views red"><i class="fa fa-eye" title="Views"></i>';
				if($row->cells [6]->value =='Saved as Draft')
					$row->cells [7]->value .='0';
				else
					$row->cells [7]->value .=$countview;
				$row->cells [7]->value .=		'</span></a>
									</div>
								</div>';


				//$data_link = url()."/sellerpostdetail/$spId";
				//$row->attributes(array("class" => "col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none html_link mobile-padding-none","data_link"=>$data_link));
			} );
	
				if($lkppoststatus[0]->lkp_ptl_post_type_id==1){
				
					$fromselect=$from_locationszone;
					$toselect=$to_locationszone;
				
					Session::put('loc_type', '1');
				
			}
			if($lkppoststatus[0]->lkp_ptl_post_type_id==2){
					$fromselect=$from_locationspincode;
					$toselect=$to_locationspincode;
				
					Session::put('loc_type', '1');
				
				}

			//Functionality to build filters in the page starts
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'spi.from_location_id', 'From Location', 'select')->options($fromselect)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.to_location_id', 'From Location', 'select')->options($toselect)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.lkp_load_type_id', 'Load Type', 'select')->options($load_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'sp.from_date', 'From', 'date' )->attr("class","filter_calendar");
			$filter->add ( 'sp.to_date', 'To', 'date' )->attr("class","filter_calendar");
	
				$filter->submit('search');
				$filter->reset('reset');
				$filter->build();
				//Functionality ptl_seller_poststo build filters in the page ends
	
				$result = array();
				$result['grid'] = $grid;
				$result['filter'] = $filter;
				return $result;
	
		} catch( Exception $e ) {
			return $e->message;
		}
	}
	
	
	/**
	 * FTL Seller Post list top nav Details List Page.
	 *
	 * @param
	 *        	$request
	 * @return Response
	 */
	
	public static function listRailSellertopNavPostItems($id){
		
		$seller_post_items  = DB::table('rail_seller_posts')
							->where('rail_seller_posts.id',$id)
							->select('rail_seller_posts.id','rail_seller_posts.transaction_id','rail_seller_posts.kg_per_cft','rail_seller_posts.pickup_charges','rail_seller_posts.delivery_charges','rail_seller_posts.oda_charges',
									 'rail_seller_posts.lkp_access_id','rail_seller_posts.from_date',
									 'rail_seller_posts.to_date','rail_seller_posts.lkp_payment_mode_id',
									 'rail_seller_posts.tracking','rail_seller_posts.accept_payment_netbanking'
									 ,'rail_seller_posts.accept_payment_credit','rail_seller_posts.accept_payment_debit','rail_seller_posts.credit_period','rail_seller_posts.credit_period_units'
									 ,'rail_seller_posts.accept_credit_netbanking','rail_seller_posts.accept_credit_cheque'
									 ,'rail_seller_posts.cancellation_charge_text','rail_seller_posts.cancellation_charge_price'
									 ,'rail_seller_posts.docket_charge_text','rail_seller_posts.docket_charge_price','rail_seller_posts.terms_conditions'
									 ,'rail_seller_posts.other_charge1_text','rail_seller_posts.other_charge1_price'
									 ,'rail_seller_posts.other_charge2_text','rail_seller_posts.other_charge2_price'
									 ,'rail_seller_posts.other_charge3_text','rail_seller_posts.other_charge3_price')
							->get();
		
		if($seller_post_items[0]->tracking == 1)
			$tracking = 'Milestone';
		else 
			$tracking = 'Real Time';
		
		if($seller_post_items[0]->lkp_payment_mode_id == 1){
			$payment_type = 'Advance';
			if($seller_post_items[0]->accept_payment_netbanking == 1)
				$payment_type .= ' | NEFT/RTGS';
			if($seller_post_items[0]->accept_payment_credit == 1)
				$payment_type .= ' | Credit Card';
			if($seller_post_items[0]->accept_payment_debit == 1)
				$payment_type .= ' | Debit Card';
		}
		elseif($seller_post_items[0]->lkp_payment_mode_id == 2)
			$payment_type = 'Cash on delivery';
		elseif($seller_post_items[0]->lkp_payment_mode_id == 3)
			$payment_type = 'Cash on pickup';
		else{
			$payment_type = 'Credit';
			if($seller_post_items[0]->accept_credit_netbanking == 1)
				$payment_type .= ' | Net Banking';
			if($seller_post_items[0]->accept_credit_cheque == 1)
				$payment_type .= ' | Cheque / DD';
			
			$payment_type .= ' | ';
			$payment_type .= $seller_post_items[0]->credit_period;
			$payment_type .= ' ';
			$payment_type .= $seller_post_items[0]->credit_period_units;
		}
		
		if($seller_post_items[0]->lkp_access_id == 2 || $seller_post_items[0]->lkp_access_id == 3)
			$privatebuyers  = DB::table('rail_seller_sellected_buyers')
			->leftjoin ( 'rail_seller_posts', 'rail_seller_posts.id', '=', 'rail_seller_sellected_buyers.seller_post_id' )
			->leftjoin('users','users.id','=','rail_seller_sellected_buyers.buyer_id')
			->where('rail_seller_sellected_buyers.created_by',Auth::user()->id)
			->where('rail_seller_sellected_buyers.seller_post_id',$id)
			->select('users.username')
			->get();
		
		$postdetails='<div class="search-block inner-block-bg margin-bottom-less-1">

				<div class="date-area">
					<div class="col-md-6 padding-none">
						<p class="search-head">Valid From</p>
						<span class="search-result">
							<i class="fa fa-calendar-o"></i>
							'.CommonComponent::checkAndGetDate($seller_post_items[0]->from_date).'
						</span>
					</div>
					<div class="col-md-6 padding-none">
						<p class="search-head">Valid To</p>
						<span class="search-result">
							<i class="fa fa-calendar-o"></i>
							'.CommonComponent::checkAndGetDate($seller_post_items[0]->to_date).'
						</span>
					</div>
				</div>
				<div>
					<p class="search-head">Payment</p>
					<span class="search-result">'.$payment_type.'</span>
				</div>';
		$currentpagename = Request::segment(1);
			if($currentpagename!='buyermarketleads') 
			{
					if($seller_post_items[0]->lkp_access_id == 1){			
				$postdetails .=	'<div>
								<p class="search-head">Public</p>
								<span class="search-result">Yes</span>
							</div>';
					}elseif ($seller_post_items[0]->lkp_access_id == 3) {
                              $postdetails .=	'<div>
										<p class="search-head">Quote</p>
										<span class="search-result">';
										foreach($privatebuyers as $pdetails){
										$postdetails .= $pdetails->username.' | ';
										}
						$postdetails .='</span>
							</div>';
                  }else{			
						$postdetails .=	'<div>
										<p class="search-head">Private</p>
										<span class="search-result">';
										foreach($privatebuyers as $pdetails){
										$postdetails .= $pdetails->username.' | ';
										}
						$postdetails .='</span>
							</div>';
					}
		 } else {
		 	$postdetails .=	'<div>
		 			<p class="search-head">Post Type</p>
										<span class="search-result">
		 							Private
		 								</span>
							</div>';		 	
		 }

	$postdetails .='<div>
					<p class="search-head">Tracking</p>
					<span class="search-result">'.$tracking.'</span>
				</div>						
				<div class="text-right filter-details">
					<div class="info-links">
						<a class="transaction-details-expand"><span class="show-icon">+</span>
							<span class="hide-icon">-</span> Details
						</a>
					</div>
				</div>

			</div>

			<!--toggle div starts-->
			<div class="show-trans-details-div-expand trans-details-expand"> 
			   	<div class="expand-block">
			   		<div class="col-md-12">';

		if($seller_post_items[0]->kg_per_cft !='' && !empty($seller_post_items[0]->kg_per_cft)){
			$postdetails .='<div class="col-md-2 padding-left-none data-fld">
										<span class="data-head">Kg per CCM</span>
										<span class="data-value">'.CommonComponent::getPriceType($seller_post_items[0]->kg_per_cft).'</span>
									</div>';
		}
		
		if($seller_post_items[0]->pickup_charges !='' && !empty($seller_post_items[0]->pickup_charges)){
			$postdetails .='<div class="col-md-2 padding-left-none data-fld">
										<span class="data-head">Pick up charges</span>
										<span class="data-value">'.CommonComponent::getPriceType($seller_post_items[0]->pickup_charges).'</span>
									</div>';
		}
		if($seller_post_items[0]->delivery_charges !='' && !empty($seller_post_items[0]->delivery_charges)){
			$postdetails .='<div class="col-md-2 padding-left-none data-fld">
										<span class="data-head">Delivery charges</span>
										<span class="data-value">'.CommonComponent::getPriceType($seller_post_items[0]->delivery_charges).'</span>
									</div>';
		}
		if($seller_post_items[0]->oda_charges !='' && !empty($seller_post_items[0]->oda_charges)){
			$postdetails .='<div class="col-md-2 padding-left-none data-fld">
										<span class="data-head">ODA charges</span>
										<span class="data-value">'.CommonComponent::getPriceType($seller_post_items[0]->oda_charges).'</span>
									</div>';
		}
	
		$postdetails .='<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">Documents</span>
							<span class="data-value">'.count(CommonComponent::getGsaDocuments(SELLER,Session::get('service_id'),$id)).'</span>
						</div>';
	
		if($seller_post_items[0]->cancellation_charge_text !='' && !empty($seller_post_items[0]->cancellation_charge_price)){
			$postdetails .='<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">'.$seller_post_items[0]->cancellation_charge_text.'</span>
								<span class="data-value">'.CommonComponent::getPriceType($seller_post_items[0]->cancellation_charge_price).'</span>
							</div>';
		}				

		if($seller_post_items[0]->docket_charge_text !='' && !empty($seller_post_items[0]->docket_charge_price)){
			$postdetails .='<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">'.$seller_post_items[0]->docket_charge_text.'</span>
								<span class="data-value">'.CommonComponent::getPriceType($seller_post_items[0]->docket_charge_price).'</span>
							</div>';
		}

		if($seller_post_items[0]->other_charge1_text !='' && !empty($seller_post_items[0]->other_charge1_price) && $seller_post_items[0]->other_charge1_price != "0.00"){
			$postdetails .='<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">'.$seller_post_items[0]->other_charge1_text.'</span>
								<span class="data-value">'.CommonComponent::getPriceType($seller_post_items[0]->other_charge1_price).'</span>
							</div>';
		}

		if($seller_post_items[0]->other_charge2_text !='' && !empty($seller_post_items[0]->other_charge2_price) && $seller_post_items[0]->other_charge2_price != "0.00"){
			$postdetails .='<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">'.$seller_post_items[0]->other_charge2_text.'</span>
								<span class="data-value">'.CommonComponent::getPriceType($seller_post_items[0]->other_charge2_price).'</span>
							</div>';
		}

		if($seller_post_items[0]->other_charge3_text !='' && !empty($seller_post_items[0]->other_charge3_price) && $seller_post_items[0]->other_charge3_price != "0.00"){
			$postdetails .='<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">'.$seller_post_items[0]->other_charge3_text.'</span>
								<span class="data-value">'.CommonComponent::getPriceType($seller_post_items[0]->other_charge3_price).'</span>
							</div>';
		}

		
						
						
		if($seller_post_items[0]->terms_conditions != ""){
			$postdetails .='<div class="col-md-12 padding-left-none data-fld">
							<span class="data-head">Terms & Conditions</span>
							<span class="data-value">'.$seller_post_items[0]->terms_conditions.'</span>
						</div>';
		}				
	$postdetails .='</div>
					<div class="clearfix"></div>
				</div>
      		</div>';



		$result = array();
		return $result['postdetails'] = $postdetails;
	}
	
	/**
	 * PTL Seller Post Details List Page with Quotes.
	 *
	 * @param
	 *        	$request
	 * @return Response
	 */
	
	public static function listRailSellerPostDetailsItems($id){
		Session::put('rail_seller_post_item', $id);
		try{

			
			
				$countview = DB::table('rail_seller_post_item_views')
				->where('rail_seller_post_item_views.seller_post_item_id','=',$id)
				->select('rail_seller_post_item_views.id','rail_seller_post_item_views.view_counts')
				->get();
				if(!isset($countview[0]->view_counts))
					$countview = 0;
				else
					$countview = $countview[0]->view_counts;
			
			
			$seller_post = DB::table('rail_seller_posts')
			->join('rail_seller_post_items','rail_seller_post_items.seller_post_id','=','rail_seller_posts.id')
			->where('rail_seller_post_items.id',$id)
			->select('rail_seller_posts.*','rail_seller_post_items.id')
			->get();
			$sellerpostid = DB::table('rail_seller_posts')
			->join('rail_seller_post_items','rail_seller_post_items.seller_post_id','=','rail_seller_posts.id')
			->where('rail_seller_post_items.id',$id)
			->select('rail_seller_posts.id')
			->get();
			$seller_post_items  = DB::table('rail_seller_post_items')
			->where('rail_seller_post_items.id',$id)
			->select('*')
			->get();
	
			$getUserrole = DB::table('users')
			->where('users.id', Auth::user()->id)
			->select('users.primary_role_id','users.is_business')
			->first();
			
			//count for seller documents
			$serviceId = Session::get('service_id');
			$docs_seller_rail    =   CommonComponent::getGsaDocuments(SELLER,$serviceId,$sellerpostid);
				
			if($getUserrole->is_business == 1){
				$stable = 'sellers';
			}else{
				$stable = 'seller_details';
			}
				
			$subscription   = DB::table($stable)
			->where($stable.'.user_id',Auth::user()->id)
			->select($stable.'.subscription_end_date',$stable.'.subscription_start_date')
			->get();
	
			//from location
			$fromlocations  = DB::table('lkp_cities')
			->where('lkp_cities.id',$seller_post_items[0]->from_location_id)
			->select('id','city_name')
			->get();
			//to location
			$tolocations = DB::table('lkp_cities')
			->where('lkp_cities.id',$seller_post_items[0]->to_location_id)
			->select('id','city_name')
			->get();
			
			//Payments type
	
			$paymenttype    = DB::table('lkp_payment_modes')
			->where('lkp_payment_modes.id',$seller_post[0]->lkp_payment_mode_id)
			->select('id','payment_mode')
			->get();
	
			if(isset($paymenttype[0]->payment_mode) && $paymenttype[0]->payment_mode!='')
				$paymenttype = $paymenttype[0]->payment_mode;
			else
				$paymenttype ='';
				
			//Enquiries
			$matchedIds = array();
			//echo "item id ".$id;
			$buyer_quote_items_matched_data = SellerMatchingComponent::getMatchedResults(RAIL, $id);
			
			foreach($buyer_quote_items_matched_data as $buyer_quote_matched_item){
				$matchedIds[] = $buyer_quote_matched_item->buyer_quote_id;
			}
			//echo "<pre>";print_R($matchedIds); echo "</pre>";
			if(!empty($matchedIds) && count($matchedIds) > 0){
			
				$buyerquoteid    	= DB::table('rail_buyer_quotes')
									->leftjoin('rail_buyer_quote_items','rail_buyer_quote_items.buyer_quote_id','=','rail_buyer_quotes.id')
									->leftjoin('users','users.id','=','rail_buyer_quotes.created_by')
									->leftjoin('lkp_load_types','lkp_load_types.id','=','rail_buyer_quote_items.lkp_load_type_id')
									->leftjoin('lkp_ptl_pincodes','lkp_ptl_pincodes.id','=','rail_buyer_quotes.from_location_id')
									->leftjoin('lkp_packaging_types','lkp_packaging_types.id','=','rail_buyer_quote_items.lkp_packaging_type_id')
									->leftjoin('lkp_ict_weight_uom','lkp_ict_weight_uom.id','=','rail_buyer_quote_items.lkp_ict_weight_uom_id')
									->leftjoin('lkp_cities','lkp_cities.id','=','rail_buyer_quotes.from_location_id')
									->whereIn('rail_buyer_quotes.id',$matchedIds)
									->select('rail_buyer_quotes.transaction_id as transaction_no','rail_buyer_quotes.id as ptlquoteid','rail_buyer_quote_items.id','rail_buyer_quote_items.created_by as buyer_id','users.username','rail_buyer_quotes.dispatch_date',
											'rail_buyer_quotes.delivery_date','rail_buyer_quote_items.lkp_quote_price_type_id',
											'rail_buyer_quotes.from_location_id','rail_buyer_quotes.to_location_id',
											'lkp_cities.city_name','rail_buyer_quotes.from_location_id',
											'rail_buyer_quote_items.lkp_load_type_id','rail_buyer_quotes.lkp_post_status_id',
											'rail_buyer_quote_items.lkp_packaging_type_id',
											'rail_buyer_quote_items.calculated_volume_weight','rail_buyer_quotes.is_door_pickup','rail_buyer_quotes.is_door_delivery',
											'rail_buyer_quote_items.units','rail_buyer_quote_items.lkp_ict_weight_uom_id',
											'rail_buyer_quote_items.number_packages','lkp_load_types.load_type','lkp_ptl_pincodes.postoffice_name',
											'lkp_ptl_pincodes.pincode','lkp_packaging_types.packaging_type_name','lkp_ict_weight_uom.weight_type'
									)
									->groupBy('rail_buyer_quotes.id')
									->get();
					
				$buyerpublicquotedetails        = array();
				$buyerpublicquotedetails[]      = DB::table('rail_buyer_quotes')
												->leftjoin('rail_buyer_quote_items','rail_buyer_quote_items.buyer_quote_id','=','rail_buyer_quotes.id')
												->leftjoin('users','users.id','=','rail_buyer_quotes.created_by')
												->leftjoin('lkp_load_types','lkp_load_types.id','=','rail_buyer_quote_items.lkp_load_type_id')
												->leftjoin('lkp_ptl_pincodes','lkp_ptl_pincodes.id','=','rail_buyer_quotes.from_location_id')
												->leftjoin('lkp_packaging_types','lkp_packaging_types.id','=','rail_buyer_quote_items.lkp_packaging_type_id')
												->leftjoin('lkp_ict_weight_uom','lkp_ict_weight_uom.id','=','rail_buyer_quote_items.lkp_ict_weight_uom_id')
												->leftjoin('lkp_cities','lkp_cities.id','=','rail_buyer_quotes.from_location_id')
												->whereIn('rail_buyer_quotes.id',$matchedIds)
												->select('rail_buyer_quotes.id as ptlquoteid','rail_buyer_quote_items.id','rail_buyer_quote_items.created_by as buyer_id','users.username','rail_buyer_quotes.dispatch_date',
														'rail_buyer_quotes.delivery_date','rail_buyer_quote_items.lkp_quote_price_type_id',
														'rail_buyer_quotes.from_location_id','rail_buyer_quotes.to_location_id',
														'lkp_cities.city_name','rail_buyer_quotes.from_location_id',
														'rail_buyer_quote_items.lkp_load_type_id','rail_buyer_quotes.lkp_post_status_id',
														'rail_buyer_quote_items.lkp_packaging_type_id',
														'rail_buyer_quote_items.calculated_volume_weight','rail_buyer_quotes.is_door_pickup','rail_buyer_quotes.is_door_delivery',
														'rail_buyer_quote_items.units','rail_buyer_quote_items.lkp_ict_weight_uom_id',
														'rail_buyer_quote_items.number_packages','lkp_load_types.load_type','lkp_ptl_pincodes.postoffice_name',
														'lkp_ptl_pincodes.pincode','lkp_packaging_types.packaging_type_name','lkp_ict_weight_uom.weight_type'
												)
												->groupBy('rail_buyer_quote_items.id')
												->get();
					
				
				for($i=0;$i<count($buyerquoteid);$i++){
					$buyersquotes	= DB::table('rail_buyer_quote_sellers_quotes_prices')
					->where('rail_buyer_quote_sellers_quotes_prices.buyer_quote_id',$buyerquoteid[$i]->ptlquoteid)
					->where('rail_buyer_quote_sellers_quotes_prices.buyer_id',$buyerquoteid[$i]->buyer_id)
					->where('rail_buyer_quote_sellers_quotes_prices.seller_id',Auth::user()->id)
					->select('rail_buyer_quote_sellers_quotes_prices.*')
					->get();
						
					if(!isset($buyersquotes[0]->initial_quote_price)){
						$buyerquoteid[$i]->initial_quote_price ='0.0000';
						$buyerquoteid[$i]->counter_quote_price ='0.0000';
						$buyerquoteid[$i]->final_quote_price ='0.0000';
						$buyerquoteid[$i]->initial_freight_amount ='';
						$buyerquoteid[$i]->counter_freight_amount ='';
						$buyerquoteid[$i]->final_freight_amount ='';
						$buyerquoteid[$i]->initial_rate_per_kg ='';
						$buyerquoteid[$i]->counter_rate_per_kg ='';
						$buyerquoteid[$i]->final_rate_per_kg ='';
						$buyerquoteid[$i]->initial_kg_per_cft ='';
						$buyerquoteid[$i]->counter_kg_per_cft ='';
						$buyerquoteid[$i]->final_kg_per_cft ='';
						$buyerquoteid[$i]->initial_pick_up_rupees ='';
						$buyerquoteid[$i]->final_pick_up_rupees ='';
						$buyerquoteid[$i]->initial_delivery_rupees ='';
						$buyerquoteid[$i]->final_delivery_rupees ='';
						$buyerquoteid[$i]->initial_oda_rupees ='';
						$buyerquoteid[$i]->final_oda_rupees ='';
						$buyerquoteid[$i]->initial_transit_days ='';
						$buyerquoteid[$i]->final_transit_days ='';
						$buyerquoteid[$i]->seller_acceptence ='';
					}else{
						$buyerquoteid[$i]->initial_quote_price =$buyersquotes[0]->initial_quote_price;
						$buyerquoteid[$i]->counter_quote_price =$buyersquotes[0]->counter_quote_price;
						$buyerquoteid[$i]->final_quote_price =$buyersquotes[0]->final_quote_price;
						$buyerquoteid[$i]->initial_freight_amount =$buyersquotes[0]->initial_freight_amount;
						$buyerquoteid[$i]->counter_freight_amount =$buyersquotes[0]->counter_freight_amount;
						$buyerquoteid[$i]->final_freight_amount =$buyersquotes[0]->final_freight_amount;
						$buyerquoteid[$i]->initial_rate_per_kg =$buyersquotes[0]->initial_rate_per_kg;
						$buyerquoteid[$i]->counter_rate_per_kg =$buyersquotes[0]->counter_rate_per_kg;
						$buyerquoteid[$i]->final_rate_per_kg =$buyersquotes[0]->final_rate_per_kg;
						$buyerquoteid[$i]->initial_kg_per_cft =$buyersquotes[0]->initial_kg_per_cft;
						$buyerquoteid[$i]->counter_kg_per_cft =$buyersquotes[0]->counter_kg_per_cft;
						$buyerquoteid[$i]->final_kg_per_cft =$buyersquotes[0]->final_kg_per_cft;
						$buyerquoteid[$i]->initial_pick_up_rupees =$buyersquotes[0]->initial_pick_up_rupees;
						$buyerquoteid[$i]->final_pick_up_rupees =$buyersquotes[0]->final_pick_up_rupees;
						$buyerquoteid[$i]->initial_delivery_rupees =$buyersquotes[0]->initial_delivery_rupees;
						$buyerquoteid[$i]->final_delivery_rupees =$buyersquotes[0]->final_delivery_rupees;
						$buyerquoteid[$i]->initial_oda_rupees =$buyersquotes[0]->initial_oda_rupees;
						$buyerquoteid[$i]->final_oda_rupees =$buyersquotes[0]->final_oda_rupees;
						$buyerquoteid[$i]->initial_transit_days =$buyersquotes[0]->initial_transit_days;
						$buyerquoteid[$i]->final_transit_days =$buyersquotes[0]->final_transit_days;
						$buyerquoteid[$i]->seller_acceptence =$buyersquotes[0]->seller_acceptence;
				
					}
                                        
                                        //commented by swathi 02-05-2016 count increasing from ajax
                                        /*$tableName = CommonComponent::getTableNameAsPerService(Session::get('service_id'));
                                        if(!empty($tableName)){
                                            CommonComponent::viewCountForBuyer(Auth::User()->id,$buyerquoteid[$i]->ptlquoteid,$tableName);
                                        }*/
                                        //end comment
				}
				if(!isset($buyersquotes)){
					$buyersquotes[] = array();
				}
			}else{
				
				$buyerquoteid =array();
				$buyerpublicquotedetails = array();
			}
			
			
			//Lead
			$matchedLeadsIds = array();
			//echo "item id ".$id;
			$buyer_quote_items_leads_data = SellerMatchingComponent::getSellerLeads(RAIL, $id);
				
			foreach($buyer_quote_items_leads_data as $buyer_quote_lead_item){
				$matchedLeadsIds[] = $buyer_quote_lead_item->buyer_quote_id;
			}
			//echo "<pre>";print_R($matchedIds); echo "</pre>";
			if(!empty($matchedLeadsIds) && count($matchedLeadsIds) > 0){
					
				$buyerleadquoteid    	= DB::table('rail_buyer_quotes')
				->leftjoin('rail_buyer_quote_items','rail_buyer_quote_items.buyer_quote_id','=','rail_buyer_quotes.id')
				->leftjoin('users','users.id','=','rail_buyer_quotes.created_by')
				->leftjoin('lkp_load_types','lkp_load_types.id','=','rail_buyer_quote_items.lkp_load_type_id')
				->leftjoin('lkp_ptl_pincodes','lkp_ptl_pincodes.id','=','rail_buyer_quotes.from_location_id')
				->leftjoin('lkp_packaging_types','lkp_packaging_types.id','=','rail_buyer_quote_items.lkp_packaging_type_id')
				->leftjoin('lkp_ict_weight_uom','lkp_ict_weight_uom.id','=','rail_buyer_quote_items.lkp_ict_weight_uom_id')
				->leftjoin('lkp_cities','lkp_cities.id','=','rail_buyer_quotes.from_location_id')
				->whereIn('rail_buyer_quotes.id',$matchedLeadsIds)
				->select('rail_buyer_quotes.transaction_id as transaction_no','rail_buyer_quotes.id as ptlquoteid','rail_buyer_quote_items.id','rail_buyer_quote_items.created_by as buyer_id','users.username','rail_buyer_quotes.dispatch_date',
						'rail_buyer_quotes.delivery_date','rail_buyer_quote_items.lkp_quote_price_type_id',
						'rail_buyer_quotes.from_location_id','rail_buyer_quotes.to_location_id',
						'lkp_cities.city_name','rail_buyer_quotes.from_location_id',
						'rail_buyer_quote_items.lkp_load_type_id','rail_buyer_quotes.lkp_post_status_id',
						'rail_buyer_quote_items.lkp_packaging_type_id',
						'rail_buyer_quote_items.calculated_volume_weight','rail_buyer_quotes.is_door_pickup','rail_buyer_quotes.is_door_delivery',
						'rail_buyer_quote_items.units','rail_buyer_quote_items.lkp_ict_weight_uom_id',
						'rail_buyer_quote_items.number_packages','lkp_load_types.load_type','lkp_ptl_pincodes.postoffice_name',
						'lkp_ptl_pincodes.pincode','lkp_packaging_types.packaging_type_name','lkp_ict_weight_uom.weight_type'
				)
				->groupBy('rail_buyer_quotes.id')
				->orderBy('rail_buyer_quotes.dispatch_date', 'asc')
				->get();
					
				$buyerleadquotedetails        = array();
				$buyerleadquotedetails[]      = DB::table('rail_buyer_quotes')
				->leftjoin('rail_buyer_quote_items','rail_buyer_quote_items.buyer_quote_id','=','rail_buyer_quotes.id')
				->leftjoin('users','users.id','=','rail_buyer_quotes.created_by')
				->leftjoin('lkp_load_types','lkp_load_types.id','=','rail_buyer_quote_items.lkp_load_type_id')
				->leftjoin('lkp_ptl_pincodes','lkp_ptl_pincodes.id','=','rail_buyer_quotes.from_location_id')
				->leftjoin('lkp_packaging_types','lkp_packaging_types.id','=','rail_buyer_quote_items.lkp_packaging_type_id')
				->leftjoin('lkp_ict_weight_uom','lkp_ict_weight_uom.id','=','rail_buyer_quote_items.lkp_ict_weight_uom_id')
				->leftjoin('lkp_cities','lkp_cities.id','=','rail_buyer_quotes.from_location_id')
				->whereIn('rail_buyer_quotes.id',$matchedLeadsIds)
				->select('rail_buyer_quotes.id as ptlquoteid','rail_buyer_quote_items.id','rail_buyer_quote_items.created_by as buyer_id','users.username','rail_buyer_quotes.dispatch_date',
						'rail_buyer_quotes.delivery_date','rail_buyer_quote_items.lkp_quote_price_type_id',
						'rail_buyer_quotes.from_location_id','rail_buyer_quotes.to_location_id',
						'lkp_cities.city_name','rail_buyer_quotes.from_location_id',
						'rail_buyer_quote_items.lkp_load_type_id','rail_buyer_quotes.lkp_post_status_id',
						'rail_buyer_quote_items.lkp_packaging_type_id',
						'rail_buyer_quote_items.calculated_volume_weight','rail_buyer_quotes.is_door_pickup','rail_buyer_quotes.is_door_delivery',
						'rail_buyer_quote_items.units','rail_buyer_quote_items.lkp_ict_weight_uom_id',
						'rail_buyer_quote_items.number_packages','lkp_load_types.load_type','lkp_ptl_pincodes.postoffice_name',
						'lkp_ptl_pincodes.pincode','lkp_packaging_types.packaging_type_name','lkp_ict_weight_uom.weight_type'
				)
				->groupBy('rail_buyer_quote_items.id')
				->orderBy('rail_buyer_quote_items.id', 'desc')
				->get();
					
			
				for($i=0;$i<count($buyerleadquoteid);$i++){
					$buyersquotes	= DB::table('rail_buyer_quote_sellers_quotes_prices')
					->where('rail_buyer_quote_sellers_quotes_prices.buyer_quote_id',$buyerleadquoteid[$i]->ptlquoteid)
					->where('rail_buyer_quote_sellers_quotes_prices.buyer_id',$buyerleadquoteid[$i]->buyer_id)
					->where('rail_buyer_quote_sellers_quotes_prices.seller_id',Auth::user()->id)
					->select('rail_buyer_quote_sellers_quotes_prices.*')
					->get();
			
					if(!isset($buyersquotes[0]->initial_quote_price)){
						$buyerleadquoteid[$i]->initial_quote_price ='0.0000';
						$buyerleadquoteid[$i]->counter_quote_price ='0.0000';
						$buyerleadquoteid[$i]->final_quote_price ='0.0000';
						$buyerleadquoteid[$i]->initial_freight_amount ='';
						$buyerleadquoteid[$i]->counter_freight_amount ='';
						$buyerleadquoteid[$i]->final_freight_amount ='';
						$buyerleadquoteid[$i]->initial_rate_per_kg ='';
						$buyerleadquoteid[$i]->counter_rate_per_kg ='';
						$buyerleadquoteid[$i]->final_rate_per_kg ='';
						$buyerleadquoteid[$i]->initial_kg_per_cft ='';
						$buyerleadquoteid[$i]->counter_kg_per_cft ='';
						$buyerleadquoteid[$i]->final_kg_per_cft ='';
						$buyerleadquoteid[$i]->initial_pick_up_rupees ='';
						$buyerleadquoteid[$i]->final_pick_up_rupees ='';
						$buyerleadquoteid[$i]->initial_delivery_rupees ='';
						$buyerleadquoteid[$i]->final_delivery_rupees ='';
						$buyerleadquoteid[$i]->initial_oda_rupees ='';
						$buyerleadquoteid[$i]->final_oda_rupees ='';
						$buyerleadquoteid[$i]->initial_transit_days ='';
						$buyerleadquoteid[$i]->final_transit_days ='';
						$buyerleadquoteid[$i]->seller_acceptence ='';
					}else{
						$buyerleadquoteid[$i]->initial_quote_price =$buyersquotes[0]->initial_quote_price;
						$buyerleadquoteid[$i]->counter_quote_price =$buyersquotes[0]->counter_quote_price;
						$buyerleadquoteid[$i]->final_quote_price =$buyersquotes[0]->final_quote_price;
						$buyerleadquoteid[$i]->initial_freight_amount =$buyersquotes[0]->initial_freight_amount;
						$buyerleadquoteid[$i]->counter_freight_amount =$buyersquotes[0]->counter_freight_amount;
						$buyerleadquoteid[$i]->final_freight_amount =$buyersquotes[0]->final_freight_amount;
						$buyerleadquoteid[$i]->initial_rate_per_kg =$buyersquotes[0]->initial_rate_per_kg;
						$buyerleadquoteid[$i]->counter_rate_per_kg =$buyersquotes[0]->counter_rate_per_kg;
						$buyerleadquoteid[$i]->final_rate_per_kg =$buyersquotes[0]->final_rate_per_kg;
						$buyerleadquoteid[$i]->initial_kg_per_cft =$buyersquotes[0]->initial_kg_per_cft;
						$buyerleadquoteid[$i]->counter_kg_per_cft =$buyersquotes[0]->counter_kg_per_cft;
						$buyerleadquoteid[$i]->final_kg_per_cft =$buyersquotes[0]->final_kg_per_cft;
						$buyerleadquoteid[$i]->initial_pick_up_rupees =$buyersquotes[0]->initial_pick_up_rupees;
						$buyerleadquoteid[$i]->final_pick_up_rupees =$buyersquotes[0]->final_pick_up_rupees;
						$buyerleadquoteid[$i]->initial_delivery_rupees =$buyersquotes[0]->initial_delivery_rupees;
						$buyerleadquoteid[$i]->final_delivery_rupees =$buyersquotes[0]->final_delivery_rupees;
						$buyerleadquoteid[$i]->initial_oda_rupees =$buyersquotes[0]->initial_oda_rupees;
						$buyerleadquoteid[$i]->final_oda_rupees =$buyersquotes[0]->final_oda_rupees;
						$buyerleadquoteid[$i]->initial_transit_days =$buyersquotes[0]->initial_transit_days;
						$buyerleadquoteid[$i]->final_transit_days =$buyersquotes[0]->final_transit_days;
						$buyerleadquoteid[$i]->seller_acceptence =$buyersquotes[0]->seller_acceptence;
			
					}
                                        
                                        //commented by swathi 02-05-2016 count increasing from ajax
                                        /*$tableName = CommonComponent::getTableNameAsPerService(Session::get('service_id'));
                                        if(!empty($tableName)){
                                            CommonComponent::viewCountForBuyer(Auth::User()->id,$buyerleadquoteid[$i]->ptlquoteid,$tableName);
                                        }*/
				}
				if(!isset($buyersquotes)){
					$buyersquotes[] = array();
				}
			}else{
			
				$buyerleadquoteid =array();
				$buyerleadquotedetails = array();
			}
				//exit;
					
			
		} catch( Exception $e ) {
			return $e->message;
		}
		$subs_st_date = date('Y-m-d', strtotime($subscription[0]->subscription_start_date));
		$subs_end_date = date('Y-m-d', strtotime($subscription[0]->subscription_end_date));
	
		Session::put('message', '');
	
		if($seller_post[0]->lkp_payment_mode_id == 1){
			$payment_type = 'Advance';
			if($seller_post[0]->accept_payment_netbanking == 1)
				$payment_type .= ' | NEFT/RTGS';
			if($seller_post[0]->accept_payment_credit == 1)
				$payment_type .= ' | Credit Card';
			if($seller_post[0]->accept_payment_debit == 1)
				$payment_type .= ' | Debit Card';
		}
		elseif($seller_post[0]->lkp_payment_mode_id == 2)
		$payment_type = 'Cash on delivery';
		elseif($seller_post[0]->lkp_payment_mode_id == 3)
		$payment_type = 'Cash on pickup';
		else{
			$payment_type = 'Credit';
			if($seller_post[0]->accept_credit_netbanking == 1)
				$payment_type .= ' | Net Banking';
			if($seller_post[0]->accept_credit_cheque == 1)
				$payment_type .= ' | Cheque / DD';
		}
		if($seller_post[0]->tracking == 1)
			$tracking = 'Milestone';
		else
			$tracking = 'Real Time';
		if($seller_post[0]->lkp_post_status_id ==1){
			$total_count =0;
		}else{
			if($seller_post_items[0]->is_private == 1){
				$total_count = count(CommonComponent::getPrivateBuyerMatchedResults(RAIL,$seller_post_items[0]->seller_post_id));
			}else{
				if(isset($seller_post_items[0]->from_location_id) && isset($seller_post_items[0]->to_location_id) && isset($seller_post[0]->lkp_ptl_post_type_id))
					$total_count = count(SellerMatchingComponent::getMatchedResults(RAIL, $seller_post_items[0]->id));
				else
					$total_count =0;
			}
		}
		
		
		//Leads count
		if($seller_post[0]->lkp_post_status_id ==1){
			$lead_count =0;
		}else{
			if($seller_post_items[0]->is_private == 1){
				$lead_count = 0;
			}
			else{
				if(isset($seller_post_items[0]->from_location_id) && isset($seller_post_items[0]->to_location_id) && isset($seller_post[0]->lkp_ptl_post_type_id)) {
					//$total_count = PtlSellerListingComponent::enquiryCount(2,$seller_post_items[0]->from_location_id,$seller_post_items[0]->to_location_id,$seller_post[0]->lkp_ptl_post_type_id );
					$lead_count = count(SellerMatchingComponent::getSellerLeads(RAIL, $seller_post_items[0]->id));
				}else
					$lead_count =0;
			}
		}
		
		$post_details='<div class="col-md-12 col-sm-12 col-xs-12 padding-none details_block">
							<h5><b>Spot Transaction</b></h5>
							<h5>
								<div class="col-md-4 col-sm-5 col-xs-6 padding-none"><b>Rail </b></div>
								<div class="col-md-4 col-sm-5 col-xs-6 padding-none"><b>'.$seller_post[0]->transaction_id.'</b></div>
								<div class="clearfix"></div>
							</h5>
							<div class="clearfix"></div>
							<div class="col-md-4 col-sm-5 col-xs-6 padding-none">
								<p>Posted to</p>
								<p>Transit Days</p>
								<p>Validity</p>
								<p>Payment Terms</p>
							</div>
							<div class="col-md-8 col-sm-7 col-xs-6 padding-none">
								<p>';								
                                        $post_details .= CommonComponent::getQuoteAccessById($seller_post[0]->lkp_access_id);
								$post_details .='</p>
								<p>'.$seller_post_items[0]->transitdays.' '.$seller_post_items[0]->units.'</p>
								<p>'.CommonComponent::checkAndGetDate($seller_post[0]->from_date).' - '.CommonComponent::checkAndGetDate($seller_post[0]->to_date).'</p>
								<p>'.$payment_type.'<span class="pull-right spot_transaction_details mobile-margin-top mobile-margin-bottom">Details <span class="show_details">+</span><span class="hide_details">-</span></span>
								</p>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-12 col-sm-12 col-xs-12 padding-none spot_transaction_details_view">
								<div class="col-md-4 col-sm-5 col-xs-6 padding-none">
									<p>Tracking</p>
									<p>Kg per CFT</p>
									<p>';
									if($seller_post[0]->cancellation_charge_text !='')
										$post_details .= $seller_post[0]->cancellation_charge_text;
									$post_details .= '</p>
									<p>';
									if($seller_post[0]->docket_charge_text !='')
										$post_details .= $seller_post[0]->docket_charge_text;
									$post_details .= '</p>
									<p>';
									if($seller_post[0]->other_charge1_text !='')
										$post_details .= $seller_post[0]->other_charge1_text;
									$post_details .= '</p>
									<p>';
									if($seller_post[0]->other_charge2_text !='')
										$post_details .= $seller_post[0]->other_charge2_text;
									$post_details .= '</p>
									<p>';
									if($seller_post[0]->other_charge3_text !='')
										$post_details .= $seller_post[0]->other_charge3_text;
									$post_details .= '</p>
									<p>Documents</p>
								</div>
								<div class="col-md-8 col-sm-7 col-xs-6 padding-none">
									<p>'.$tracking.'</p> 
									<p>';
									if(empty($seller_post[0]->kg_per_cft))
										$post_details .= '0';
									else 
										$post_details .= $seller_post[0]->kg_per_cft;
									$post_details .='</p>
									<p>';
									if($seller_post[0]->cancellation_charge_price!='' && $seller_post[0]->cancellation_charge_text !='')
										$post_details .= $seller_post[0]->cancellation_charge_price;
									$post_details .= '</p>
									<p>';
									if($seller_post[0]->docket_charge_price!='' && $seller_post[0]->docket_charge_text !='')
										$post_details .= $seller_post[0]->docket_charge_price;
									$post_details .= '</p>
									<p>';
									if($seller_post[0]->other_charge1_price!='' && $seller_post[0]->other_charge1_text !='')
										$post_details .= $seller_post[0]->other_charge1_price.' </p>
									<p>';
									if($seller_post[0]->other_charge2_price!='' && $seller_post[0]->other_charge2_text !='')
										$post_details .= $seller_post[0]->other_charge2_price;
					
					
									$post_details .= '</p>
									<p>';
									if($seller_post[0]->other_charge3_price!='' && $seller_post[0]->other_charge3_text !='')
										$post_details .= $seller_post[0]->other_charge3_price;
									$post_details .= '</p>
									<p><i class="fa fa-lg fa-file-text"></i> <sup class="">0</sup></p> 
								</div>
							</div>
						</div>';
		$allMessagesList = MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$id);
                if(isset($allMessagesList['result']) && !empty($allMessagesList['result']))
                    $countMessages = count($allMessagesList['result']);
                else
                    $countMessages = 0 ;	

                if(isset($_GET['type'])){
                	if($_GET['type'] == 'messages'){
                		$color1 ='red';
                		$color2 = '';
                		$color3 = '';
                		$color4 = '';
                	}
                	if($_GET['type'] == 'enquiries'){
                		$color1 ='';
                		$color2 = 'red';
                		$color3 = '';
                		$color4 = '';
                	}
                	if($_GET['type'] == 'leads'){
                		$color1 ='';
                		$color2 = '';
                		$color3 = 'red';
                		$color4 = '';
                	}
                	if($_GET['type'] == 'documentation'){
                		$color1 ='';
                		$color2 = '';
                		$color3 = '';
                		$color4 = 'red';
                	}
                }else{
                	$color1 ='';
                	$color2 = 'red';
                	$color3 = '';
                	$color4 = '';
                }
                 
                
		$gridtopnav = ' <a href="#" class="'.$color1.'"  data-showdiv="ftl-seller-messages" title="messages"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$countMessages.'</span></a>
						<a href="#" class="'.$color2.'"  data-showdiv="ftl-seller-enquiry" title="enquires"><i class="fa fa-file-text-o"></i> Enquiries<span class="badge">'.$total_count.'</span></a>
						<a href="#" class="'.$color3.'"  data-showdiv="ftl-seller-leads" title="leads"><i class="fa fa-thumbs-o-up"></i> Leads<span class="badge">'.$lead_count.'</span></a>
						<a href="#" data-showdiv="ftl-seller-marketanalytics"><i class="fa fa-line-chart"></i> Market Analytics</a>
						<a href="#"  class="'.$color4.'" data-showdiv="ftl-seller-documentation"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_seller_rail).'</span></a>';
                		
		if($seller_post[0]->lkp_access_id == 2 || $seller_post[0]->lkp_access_id == 3)
				$privatebuyers  = DB::table('rail_seller_sellected_buyers')
				->leftjoin ( 'rail_seller_posts', 'rail_seller_posts.id', '=', 'rail_seller_sellected_buyers.seller_post_id' )
				->leftjoin('users','users.id','=','rail_seller_sellected_buyers.buyer_id')
				->where('rail_seller_sellected_buyers.created_by',Auth::user()->id)
				->where('rail_seller_sellected_buyers.seller_post_id',$sellerpostid[0]->id)
				->select('users.username')
				->get();
		else 
			$privatebuyers =array();
				
		return array('id'=>$id,
			'seller_post'=>$seller_post,
			'seller_post_items'=>$seller_post_items,
			'fromlocations'=>$fromlocations,
			'tolocations'=>$tolocations,
			'paymenttype'=>$paymenttype,
			'buyerquoteid'=>$buyerquoteid,
			'buyerleadquoteid'=>$buyerleadquoteid,
			'buyerpublicquotedetails'=>$buyerpublicquotedetails,
			'buyerleadquotedetails'=>$buyerleadquotedetails,
			'subscriptionstdate'=>$subs_st_date,
			'subscriptionenddate'=>$subs_end_date,
			'viewcount'=>$countview,
			'privatebuyers'=>$privatebuyers,
			'post_details'=>$post_details,
			'kgpercft'=>$seller_post[0]->kg_per_cft,
			'gridtopnav'=>$gridtopnav);
	}

        
        /**
	 * RAIL Buyer Private Posts List Page - Grid and filters
	 * Retrieval of data related to Buyer posts list items to populate in the Buyer list widget
	 * Displays a grid with a list of all seller posts
	 */
	public static function listRAILBuyerPrivatePosts($statusId, $serviceId, $roleId,$type) {
		if(isset($_REQUEST['page'])){//echo $_REQUEST['page'];
		}
		// Filters values to populate in the page
		$from_locations = array (
				"" => "From Location"
		);
		$to_locations = array (
				"" => "To Location"
		);

		$from_date = '';
		$to_date = '';


		// query to retrieve buyer posts list and bind it to the grid
                $Query = DB::table ( 'rail_buyer_quotes as ptlbq' );
                $Query->leftjoin ( 'rail_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
                $Query->leftjoin ( 'lkp_load_types as lt', 'lt.id', '=', 'ptlbqi.lkp_load_type_id' );
                $Query->leftjoin ( 'lkp_packaging_types as pt', 'pt.id', '=', 'ptlbqi.lkp_packaging_type_id' );
                $Query->leftjoin ( 'lkp_quote_accesses as lqa', 'lqa.id', '=', 'ptlbq.lkp_quote_access_id' );
                $Query->leftjoin ( 'lkp_ptl_pincodes as ptlPins', 'ptlPins.id', '=', 'ptlbq.from_location_id' );
                $Query->leftjoin ( 'lkp_ptl_pincodes as ptlPinsTo', 'ptlPinsTo.id', '=', 'ptlbq.to_location_id' );
                $Query->leftjoin ( 'rail_buyer_quote_selected_sellers as bqss', 'bqss.buyer_quote_id', '=', 'ptlbq.id' );
                $Query->leftjoin ( 'users as us', 'us.id', '=', 'ptlbq.buyer_id' );
                $Query->where ( 'bqss.seller_id', Auth::User ()->id ); 
        
                $Query->whereIn('ptlbq.lkp_post_status_id',array(2,3,4,5));
                $Query->where('ptlbq.lkp_quote_access_id','=',2);
                $Query->groupBy('ptlbqi.buyer_quote_id');
                $Query->orderBy('ptlbqi.buyer_quote_id', 'DESC');
        
		// conditions to make search
		if (isset ( $_GET ['status'] ) && $_GET ['status'] != '') {
			$Query->where ( 'ptlbq.lkp_post_status_id', '=', $_GET ['status'] );
		}
		if (isset ( $_GET ['from_date'] ) && $_GET ['from_date'] != '') {
			$commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['from_date']);
			$Query->where ( 'ptlbq.dispatch_date', '>=', $commonDispatchDate );
			$from_date = $commonDispatchDate;
		}
	 	if (isset ( $_GET ['to_date'] ) && $_GET ['to_date'] != '') {
			$commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['to_date']);
			$Query->where ( 'ptlbq.dispatch_date', '<=', $commonDeliveryhDate);
			$to_date = $commonDeliveryhDate;
		}

		$postResults = $Query->select ('ptlbqi.id','ptlbq.dispatch_date','ptlbq.delivery_date','ptlbq.is_dispatch_flexible','ptlbq.from_location_id',
                        'ptlbq.id as buyer_quote_id' ,'ptlbqi.created_by','ptlbq.to_location_id','ptlbq.lkp_post_status_id','ptlbq.transaction_id','ptlbq.buyer_id', 'us.username', 'lt.load_type','pt.packaging_type_name','ptlPins.postoffice_name as fromLocation','ptlPinsTo.postoffice_name as toLocation','ptlbq.lkp_quote_access_id','lqa.quote_access','ptlbq.is_cancelled',DB::raw('sum(ptlbqi.number_packages) AS totalpackges'))->get ();
		//echo "<pre>"; print_r($postResults);die;
		// Functionality to handle filters based on the selection starts
		foreach ( $postResults as $post ) {
			//$buyer_quotes = DB::table ( 'rail_buyer_quote_items' )->where ( 'buyer_quote_id', $post->id )->select ( 'rail_buyer_quote_items.*')->get ();
				
			//foreach ( $buyer_quotes as $quotes ) {
				
				if (! isset ( $from_locations [$post->from_location_id] )) {
					$from_locations [$post->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $post->from_location_id )->pluck ( 'pincode' );
				}
				if (! isset ( $to_locations [$post->to_location_id] )) {
					$to_locations [$post->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $post->to_location_id )->pluck ( 'pincode' );
				}
                                
			//}
		}
		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations = CommonComponent::orderArray($to_locations);
                
		//Functionality to handle filters based on the selection ends
                $grid = DataGrid::source ( $Query );
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Name', 'username' )->attributes ( array (
				"class" => "col-md-2 padding-none" 
		) );
		$grid->add ( 'dispatch_date', 'Dispatch Date', 'dispatch_date' )->attributes ( array (
				"class" => "col-md-3 form-control-fld  padding-none" 
		) );
		$grid->add ( 'from_location_id', 'From Location', 'from_location_id' )->attributes ( array (
				"class" => "col-md-2 padding-left-none" 
		) );
		$grid->add ( 'to_location_id', 'To Location', 'to_location_id' )->attributes ( array (
				"class" => "col-md-2 padding-left-none" 
		) );
		$grid->add ( 'lkp_post_status_id', 'Status' )->attributes ( array (
				"class" => "col-md-2 padding-left-none"
		) );
		$grid->add ( 'status', 'ID', true )->style ( "display:none" );
		$grid->add ( 'created_by', 'ID', true )->style ( "display:none" );
		$grid->add ( 'load_type', 'Load Type', true )->style ( "display:none" );
		$grid->add ( 'packaging_type_name', 'Packaging Type', true )->style ( "display:none" );
		$grid->add ( 'calculated_volume_weight', 'Volume', true )->style ( "display:none" );
		$grid->add ( 'units', 'Units', true )->style ( "display:none" );
		$grid->add ( 'number_packages', 'Packages', true )->style ( "display:none" );
		$grid->add ( 'delivery_date', 'Delivery Date', 'delivery_date' )->style ( "display:none" );
                $grid->add ( 'transaction_id', 'transaction_id', 'transaction_id' )->style ( "display:none" );
                $grid->add ( 'buyer_quote_id', 'buyer_quote_id', 'buyer_quote_id' )->style ( "display:none" );
                $grid->add ( 'is_door_pickup', 'is_door_pickup', 'is_door_pickup' )->style ( "display:none" );
                $grid->add ( 'is_door_delivery', 'is_door_delivery', 'is_door_delivery' )->style ( "display:none" );
		$grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );
		$grid->row ( function ($row) {
			$row->cells [0]->style ( 'display:none' );
			$row->cells [2]->style ( 'display:none' );
			$row->cells [3]->style ( 'display:none' );
			$row->cells [4]->style ( 'display:none' );
			//$row->cells [5]->style ( 'display:none' );
			$row->cells [6]->style ( 'display:none' );
			$row->cells [7]->style ( 'display:none' );
			$row->cells [8]->style ( 'display:none' );
			$row->cells [9]->style ( 'display:none' );
			$row->cells [10]->style ( 'display:none' );
			$row->cells [11]->style ( 'display:none' );
			$row->cells [12]->style ( 'display:none' );
			$row->cells [13]->style ( 'display:none' );
			$row->cells [14]->style ( 'display:none' );
                        $row->cells [15]->style ( 'display:none' );
			$row->cells [16]->style ( 'display:none' );
                        $row->cells [17]->style ( 'display:none' );

                        $transaction_id=$row->cells[14]->value;
                        $bqid=$row->cells[15]->value;
			$buyer_quote_id = $row->cells [0]->value;		
			$buyer_name = $row->cells [1]->value;
			$from_zipcode = $row->cells [3]->value;
			$to_zipcode = $row->cells [4]->value;
			$dispatch_date_buyer = $row->cells [2]->value;
			$delivery_date_buyer = $row->cells [13]->value;

			
			$dpickup = $row->cells [16]->value;
			$ddelivery = $row->cells [17]->value;
			
			
			$buyer_id = $row->cells [7]->value;
			$buyer_quote_id = $row->cells [0]->value;
			
			$buyerdetailsvalue = DB::table ( 'rail_buyer_quote_items as bqi' )
                                ->where ( 'bqi.id', '=', $buyer_quote_id )
                                ->select ( 'bqi.*' )->get ();
			
			$buyer_post_status = $row->cells [5]->value;
			$buyer_post_status_id = $row->cells [5]->value;
			
			if ($buyer_post_status == 2) {
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
			
			$row->cells [1]->attributes ( array (
					"class" => "col-md-2 padding-left-none" 
			) );
			$row->cells [2]->attributes ( array (
					"class" => "col-md-3 form-control-fld padding-left-none" 
			) );
			$row->cells [3]->attributes ( array (
					"class" => "col-md-2 padding-left-none" 
			) );
			$row->cells [4]->attributes ( array (
					"class" => "col-md-2 padding-left-none" 
			) );
			$row->cells [5]->attributes ( array (
					"class" => "col-md-3 form-control-fld padding-none" 
			) );
			// $row->cells [6]->attributes(array("class" => "col-md-12 col-sm-12 col-xs-12 padding-none"));
			
			$row->cells [1]->value = $buyer_name . '<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
									<i class="red fa fa-star"></i>
									<i class="red fa fa-star"></i>
									<i class="red fa fa-star"></i>
								</div>';
			$delivery_date_buyer_convert = CommonComponent::checkAndGetDate($delivery_date_buyer);
			
			if($delivery_date_buyer_convert != ""){
				$row->cells [2]->value = CommonComponent::checkAndGetDate($dispatch_date_buyer)." - ".$delivery_date_buyer_convert;
			}else{
				$row->cells [2]->value = CommonComponent::checkAndGetDate($dispatch_date_buyer);
			}
			
			$getSellerpost  = SellerComponent::ptlSellerPostDetails($from_zipcode,$to_zipcode, $bqid);
			
				
			if(isset($getSellerpost[0]->id))
				$seller_post_id_private = $getSellerpost[0]->id;
			else
				$seller_post_id_private = 0;
			if(count($getSellerpost)>0){
				
				$tracking = CommonComponent::getTrackingType($getSellerpost[0]->tracking);
				if($getSellerpost[0]->lkp_payment_mode_id == 1){
					$payment_type = 'Advance';
					if($getSellerpost[0]->accept_payment_netbanking == 1)
						$payment_type .= ' | NEFT/RTGS';
					if($getSellerpost[0]->accept_payment_credit == 1)
						$payment_type .= ' | Credit Card';
					if($getSellerpost[0]->accept_payment_debit == 1)
						$payment_type .= ' | Debit Card';
				}
				elseif($getSellerpost[0]->lkp_payment_mode_id == 2)
				$payment_type = 'Cash on delivery';
				elseif($getSellerpost[0]->lkp_payment_mode_id == 3)
				$payment_type = 'Cash on pickup';
				else{
					$payment_type = 'Credit';
					if($getSellerpost[0]->accept_credit_netbanking == 1)
						$payment_type .= ' | Net Banking';
					if($getSellerpost[0]->accept_credit_cheque == 1)
						$payment_type .= ' | Cheque / DD';
				}
			
			}else{
				$tracking = '';
				$payment_type ='';
			}
			
			
			
			
			$row->cells [3]->value = CommonComponent::getPinName($from_zipcode);
			$row->cells [4]->value = CommonComponent::getPinName($to_zipcode);
			
			$odacheck = CommonComponent::sellerODACheck($to_zipcode,Session::get('service_id'));
			
			$locationids = DB::table('rail_buyer_quotes as bq')
			->leftjoin('rail_buyer_quote_items as bqi','bqi.buyer_quote_id','=','bq.id')
			->where('bqi.id','=',$buyer_quote_id)
			->select('bq.from_location_id','bq.to_location_id')
			->first();
			//print_r($locationids);exit;
				if(isset($locationids->to_location_id)){
					$toloc = CommonComponent::getPinNameFromId($locationids->to_location_id);
					$fromloc = CommonComponent::getPinNameFromId($locationids->from_location_id);
				}
				
			
			$row->cells [5]->value = $buyer_post_status;

							$quoteid = DB::table('rail_buyer_quote_items as bqi')
							->where('bqi.id','=',$buyer_quote_id)
							->select('bqi.buyer_quote_id')
							->get();
							
							$quoteitems = DB::table('rail_buyer_quote_items as bqi')
							->join('lkp_load_types','lkp_load_types.id','=','bqi.lkp_load_type_id')
							->join('lkp_packaging_types','lkp_packaging_types.id','=','bqi.lkp_packaging_type_id')
							->where('bqi.buyer_quote_id','=',$quoteid[0]->buyer_quote_id)
							->select('bqi.*','lkp_load_types.load_type','lkp_packaging_types.packaging_type_name')
							->get();
							
							if(Session::get('service_id') == RAIL){
								$getInitialQuotePrice = CommonComponent::getPTLQuotePriceForSearch($buyer_id,$quoteid[0]->buyer_quote_id,Auth::user()->id,'initial_quote_price','rail_buyer_quote_sellers_quotes_prices');
								$getCounterQuotePrice = CommonComponent::getPTLQuotePriceForSearch($buyer_id,$quoteid[0]->buyer_quote_id,Auth::user()->id,'counter_quote_price','rail_buyer_quote_sellers_quotes_prices');
								$getFinalQuotePrice   = CommonComponent::getPTLQuotePriceForSearch($buyer_id,$quoteid[0]->buyer_quote_id,Auth::user()->id,'final_quote_price','rail_buyer_quote_sellers_quotes_prices');
							}
							if(Session::get('service_id') == AIR_DOMESTIC){
								$getInitialQuotePrice = CommonComponent::getPTLQuotePriceForSearch($buyer_id,$quoteid[0]->buyer_quote_id,Auth::user()->id,'initial_quote_price','airdom_buyer_quote_sellers_quotes_prices');
								$getCounterQuotePrice = CommonComponent::getPTLQuotePriceForSearch($buyer_id,$quoteid[0]->buyer_quote_id,Auth::user()->id,'counter_quote_price','airdom_buyer_quote_sellers_quotes_prices');
								$getFinalQuotePrice   = CommonComponent::getPTLQuotePriceForSearch($buyer_id,$quoteid[0]->buyer_quote_id,Auth::user()->id,'final_quote_price','airdom_buyer_quote_sellers_quotes_prices');
							}
							//commented by swathi 02-05-2016 count increasing from ajax
                                                        /*$tableName = CommonComponent::getTableNameAsPerService(Session::get('service_id'));
                                                        if(!empty($tableName)){
                                                            CommonComponent::viewCountForBuyer(Auth::User()->id,$quoteid[0]->buyer_quote_id,$tableName);
                                                        }*/
                                                        //end comment
                                                        //echo "<pre>";print_r($getInitialQuotePrice);//exit;
                                                         
							if($buyer_post_status_id==2 && (!isset($getInitialQuotePrice[0]->initial_rate_per_kg) || $getInitialQuotePrice[0]->initial_rate_per_kg=='')){
							$row->cells [5]->value .= '
							<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
							<button class="btn red-btn pull-right submit-data underline_link seller_submit_quote" data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Submit Quote </button>
							
							</div>';}
							
							if(isset($getInitialQuotePrice[0]->initial_quote_price) && $getInitialQuotePrice[0]->initial_quote_price!='0.0000'
								&&	isset($getCounterQuotePrice[0]->counter_rate_per_kg) && 
									$getCounterQuotePrice[0]->counter_rate_per_kg ==''){
								$row->cells [5]->value .= '
							<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
							 <button class="btn red-btn pull-right submit-data underline_link seller_submit_quote" data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Initial Quote Submitted </button>
							
							</div>';}
							if($buyer_post_status_id==2 && (isset($getCounterQuotePrice[0]->counter_rate_per_kg) && 
									$getCounterQuotePrice[0]->counter_rate_per_kg !='')
									&& (isset($getFinalQuotePrice[0]->final_kg_per_cft) && $getFinalQuotePrice[0]->final_kg_per_cft=='')){
							$row->cells [5]->value .= '
							<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
							<button  class="btn red-btn pull-right submit-data ltlsellesearchdetails_1  underline_link " data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Submit Final Quotes <i class="fa fa-rupee"></i></button>
							
							</div>
							<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
							<button class="btn red-btn pull-right submit-data ltlsellesearchdetails_2 underline_link " data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Accept Counter Offer <i class="fa fa-rupee"></i></button>
							</span>
							</div>';
							}
							if(isset($getCounterQuotePrice[0]->counter_rate_per_kg) &&
									$getCounterQuotePrice[0]->counter_rate_per_kg !=''
									&& isset($getFinalQuotePrice[0]->final_kg_per_cft) && $getFinalQuotePrice[0]->final_kg_per_cft!=''){
								$row->cells [5]->value .= '<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
								<button  class="btn red-btn pull-right submit-data ltlsellesearchdetails_1 underline_link " data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Final Quote Submitted <i class="fa fa-rupee"></i></button>
								
								</div>';
								
							}
													
							$row->cells [5]->value .= '
								<div class="pull-right text-right">
									<div class="info-links">
										<span class="detailsslide underline_link" data-buyersearchlistid="'.$buyer_id.'_'.$buyer_quote_id.'">
											<span class="show_details" style="display: inline;">+</span>
											<span class="hide_details" style="display: none;">-</span>
											Details
										</span> 
										<a href="#" data-userid="'.$buyer_id.'" data-buyer-transaction="'.$transaction_id.'" class="new_message" data-buyerquoteitemidforseller="'.$bqid.'"><i	class="fa fa-envelope-o"></i></a></div>
									</div>	
								</div>
							<div class="clearfix"></div>
							<form id ="addptlsellersearchpostquoteoffer" name ="addptlsellersearchpostquoteoffer" class="formquoteid_'.$buyer_quote_id.'">';
							
							if(Session::get('session_delivery_date_ptl')=='')
							Session::put('session_delivery_date_ptl',$delivery_date_buyer);
							
							
							$row->cells [5]->value .='<input type="hidden" name="seller_post_item_id" id="seller_post_item_id" value="'.Session::get('seller_post_item').'">
							<input type="hidden" name="volumetric_'.$buyer_id.'_'.$buyer_quote_id.'" id="volumetric_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$buyerdetailsvalue[0]->calculated_volume_weight.'">
							<input type="hidden" name="packagenos_'.$buyer_id.'_'.$buyer_quote_id.'" id="packagenos_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$buyerdetailsvalue[0]->number_packages.'">
							<input type="hidden" name="units_'.$buyer_id.'_'.$buyer_quote_id.'" id="units_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$buyerdetailsvalue[0]->units.'">
							<input type="hidden" name="buyerquoteid" id="buyerquoteid" value="'.$quoteid[0]->buyer_quote_id.'">';
							if(isset($locationids->from_location_id) && isset($locationids->to_location_id)){
							
								$row->cells [5]->value .='
									<input type="hidden" name="from_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" id="from_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$locationids->from_location_id.'">
									<input type="hidden" name="to_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" id="to_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$locationids->to_location_id.'">';
								
							}
							
							$row->cells [5]->value .='<div class="col-md-12 show-data-div padding-none padding-top quote_details_1_'.$buyer_id.'_'.$buyer_quote_id.' margin-top" style="display:none">
							<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
								<div class="table pull-right">
									<h2 class="sub-head"><span class="from-head">'.$fromloc.' to  '.$toloc.'</span></h2>
									<div class="table-heading inner-block-bg">
										<div class="col-md-4 padding-left-none">Load type</div>
										<div class="col-md-2 padding-left-none">Package Type</div>
										<div class="col-md-2 padding-left-none">Volume</div>
										<div class="col-md-2 padding-left-none">Unit Weight</div>
										<div class="col-md-2 padding-left-none">No of Packages</div>
									</div>';
									for($i=0;$i<count($quoteitems);$i++){
									$row->cells [5]->value .= '
									<div class="table-data">
										<div class="table-row inner-block-bg">
											<div class="col-md-4 padding-left-none">'.$quoteitems[$i]->load_type.'</div>
											<div class="col-md-2 padding-left-none">'.$quoteitems[$i]->packaging_type_name.'</div>
											<div class="col-md-2 padding-left-none">'.round($quoteitems[$i]->calculated_volume_weight,4).' CFT </div>';
											if($quoteitems[$i]->lkp_ict_weight_uom_id ==2)
											$quoteitems[$i]->units = $quoteitems[$i]->units*0.001;
											$row->cells [5]->value .= '
										
											<div class="col-md-2 padding-left-none">'.$quoteitems[$i]->units.' Kgs</div>
											<div class="col-md-2 padding-left-none">'.$quoteitems[$i]->number_packages.'</div>
										</div>	
										<input type="hidden" name="volumetric_'.$i.'" id="volumetric_'.$i.'" value="'.$quoteitems[$i]->calculated_volume_weight.'">
										<input type="hidden" name="units_'.$i.'" id="units_'.$i.'" value="'.$quoteitems[$i]->units.'">
										<input type="hidden" name="weighttype_'.$i.'" id="weighttype_'.$i.'" value="'.$quoteitems[$i]->lkp_ict_weight_uom_id.'">
										<input type="hidden" name="packagenos_'.$i.'" id="packagenos_'.$i.'" value="'.$quoteitems[$i]->number_packages.'">	
										<div class="clear-fix"></div>
									</div>';
									}
									$row->cells [5]->value .= '
									<input type="hidden" name="incrementcount_'.$buyer_id.'_'.$buyer_quote_id.'" id="incrementcount_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$i.'">
									<input type="hidden" name="buyerquoteid_'.$buyer_id.'_'.$buyer_quote_id.'" id="buyerquoteid_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$quoteid[0]->buyer_quote_id.'">										
								</div>
							</div></div>
							<div class="col-md-12 padding-none submit-data-div quote_details_2_'.$buyer_id.'_'.$buyer_quote_id.'" style="display:none">
							<div class="col-md-12 padding-none">
									<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-top ">
									<b>Seller Quote</b> 
									</div>';
									if(isset($getInitialQuotePrice[0]->initial_rate_per_kg) && $getInitialQuotePrice[0]->initial_rate_per_kg !='')
										$row->cells [5]->value .='
								<div class="col-md-3 form-control-fld padding-left-none form-control-fld padding-top">
									<span class="data-head">Rate per Kg </span> <span class="data-value"> Rs '.$getInitialQuotePrice[0]->initial_rate_per_kg.' /-</span>
										<input type="hidden" 
									name="initial_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="initial_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
									placeholder="Rate per Kg *" class="form-control form-control1 numberVal " value="'.$getInitialQuotePrice[0]->initial_rate_per_kg.'" ></div>'; 
									else
										$row->cells [5]->value .= '
								<div class="col-md-3 form-control-fld padding-left-none form-control-fld padding-top">
								<input type="text" name="initial_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="initial_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
									placeholder="Rate per Kg *" class="ptl_initial_rate_per_kg form-control  numberVal fourdigitstwodecimals_deciVal form-control1" ></div>';
									
									
									
								
								if(isset($getInitialQuotePrice[0]->initial_kg_per_cft) && $getInitialQuotePrice[0]->initial_kg_per_cft!='')
									$row->cells [5]->value .='
								<div class="col-md-3 form-control-fld padding-left-none form-control-fld padding-top">
									<span class="data-head"> Conversion KG / CFT </span><span class="data-value"> '.$getInitialQuotePrice[0]->initial_kg_per_cft.' KG</span> 
										
										</div>';
								else{
									$row->cells [5]->value .= '
								<div class="col-md-3 form-control-fld padding-left-none form-control-fld padding-top">
									<input type="text"
									name="initial_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'"  
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="initial_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    placeholder="Conversion Kg/CFT *" class="ptl_initial_conversion numberVal fourdigitsthreedecimals_deciVal form-control  form-control1" >';
									$row->cells [5]->value .='</div>
									<div class="col-md-3 form-control-fld padding-left-none form-control-fld padding-top">
									<input type="hidden" id="calculatoropen" style="border:none;">
									</div>';
								}
								if(isset($getInitialQuotePrice[0]->initial_kg_per_cft) && $getInitialQuotePrice[0]->initial_kg_per_cft!=''){
									
								$row->cells [5]->value .= '<div class="clearfix"></div>
										<div class="col-md-12 col-sm-12 col-xs-12 padding-none">';
								$row->cells [5]->value .= '<div class="col-md-3 form-control-fld col-sm-3 col-xs-6 padding-none"><span class="data-head">Pickup </span> <span class="data-value">Rs '.$getInitialQuotePrice[0]->initial_pick_up_rupees.' /-</span></div>';
								$row->cells [5]->value .= '<div class="col-md-3 form-control-fld col-sm-3 col-xs-6 padding-none"><span class="data-head">Delivery </span> <span class="data-value">Rs '.$getInitialQuotePrice[0]->initial_delivery_rupees.' /-</span></div>';
								$row->cells [5]->value .= '<div class="col-md-3 form-control-fld col-sm-3 col-xs-6 padding-none"><span class="data-head">ODA </span> <span class="data-value">Rs '.$getInitialQuotePrice[0]->initial_oda_rupees.' /-</span></div>';
								$row->cells [5]->value .= '<div class="col-md-3 form-control-fld col-sm-3 col-xs-6 padding-none"><span class="data-head">Transit Days </span> <span class="data-value">'.$getInitialQuotePrice[0]->initial_transit_days.'</span></div>';
								
								$row->cells [5]->value .= '<div class="col-md-3 form-control-fld padding-none margin-top-none">
								<span class="data-head">Freight Amount </span><span class="data-value" >Rs '.CommonComponent::moneyFormat($getInitialQuotePrice[0]->initial_freight_amount,true).' /-</span>
								
								</div>
								<div class="col-md-3 form-control-fld padding-none margin-top-none">
								<span class="data-head">Total Amount </span><span class="data-value" >Rs '.CommonComponent::moneyFormat($getInitialQuotePrice[0]->initial_quote_price,true).' /-</span>
								
								</div>';
								
									$row->cells [5]->value .= '</div>';
									$row->cells [5]->value .= '<input type="hidden"
											name="initial_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="initial_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="Pickup Rs *" class="form-control form-group" value="'.$getInitialQuotePrice[0]->initial_pick_up_rupees.'">
													<input type="hidden"
											name="initial_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="initial_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="Delivery Rs *" class="form-control form-group" value="'.$getInitialQuotePrice[0]->initial_delivery_rupees.'">
													<input type="hidden"
											name="initial_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="initial_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="ODA Rs *" class="form-control form-group" value="'.$getInitialQuotePrice[0]->initial_oda_rupees.'">
													<input type="hidden"
											name="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="transit Days *" class="form-control form-group" value="'.$getInitialQuotePrice[0]->initial_transit_days.'">';
								}
								
								
								if(isset($getCounterQuotePrice[0]->counter_rate_per_kg) && $getCounterQuotePrice[0]->counter_rate_per_kg !='')
										$row->cells [5]->value .='
											<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-top form-group">
											<b>Buyer Counter Offer</b>  
											</div>
											<div class="col-md-3 form-control-fld padding-left-none">
												<span class="data-head">Rate per Kg </span> <span class="data-value"> Rs '.$getCounterQuotePrice[0]->counter_rate_per_kg.' /- </span></div>
													<input type="hidden"
											name="counter_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="counter_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="Conversion Kg/CFT *" class="form-control form-group margin-top"  value="'.$getCounterQuotePrice[0]->counter_rate_per_kg.'">'; 
									
								if(isset($getCounterQuotePrice[0]->counter_kg_per_cft) && $getCounterQuotePrice[0]->counter_kg_per_cft!=''){
									$row->cells [5]->value .='
								<div class="col-md-3 form-control-fld padding-left-none form-control-fld"><span class="data-head">Conversion KG / CFT </span> <span class="data-value"> '.$getCounterQuotePrice[0]->counter_kg_per_cft.' KG </span></div>
									<input type="hidden"
											name="counter_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="counter_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="Conversion Kg/CFT *" class="form-control form-group margin-top"  value="'.$getCounterQuotePrice[0]->counter_kg_per_cft.'">
											<div class="clearfix margin-none"></div>
											<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Freight Amount  </span><span class="data-value" >Rs '.CommonComponent::moneyFormat($getCounterQuotePrice[0]->counter_freight_amount,true).' /-</span>
												
											</div>
											<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Total Amount  </span><span class="data-value" >Rs '.CommonComponent::moneyFormat($getCounterQuotePrice[0]->counter_quote_price,true).' /-</span>
										
											</div>';
								}
								$row->cells [5]->value .='<div class="clearfix"></div>';
								$row->cells [5]->value .='<div class="hide-final">';
									if(isset($getFinalQuotePrice[0]->final_rate_per_kg) && $getFinalQuotePrice[0]->final_rate_per_kg !='')
										$row->cells [5]->value .='
												<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-top form-group">
								 					<b>Seller Final Quote</b>   
												</div>
									<div class="col-md-3 form-control-fld padding-left-none form-control-fld margin-bottom-none"><span class="data-head">Rate per Kg </span> <span class="data-value"> Rs '.$getFinalQuotePrice[0]->final_rate_per_kg.' /-</span></div>'; 
									elseif(isset($getFinalQuotePrice[0]->final_rate_per_kg) && $getFinalQuotePrice[0]->final_rate_per_kg =='' && $getInitialQuotePrice[0]->initial_rate_per_kg!='' &&  $getCounterQuotePrice[0]->counter_rate_per_kg!='')
										$row->cells [5]->value .= '
												<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-top form-group">
								 					<b>Seller Final Quote</b>   
												</div>
									<div class="col-md-3 form-control-fld padding-left-none form-control-fld margin-bottom-none"><input type="text" 
									name="final_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="final_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
									placeholder="Rate per Kg *" class="ptl_final_rate_per_kg form-control form-control1 fourdigitstwodecimals_deciVal numberVal " ></div>';
									
								
								if(isset($getFinalQuotePrice[0]->final_kg_per_cft) && $getFinalQuotePrice[0]->final_kg_per_cft!='')
									$row->cells [5]->value .='
									<div class="col-md-3 form-control-fld padding-left-none form-control-fld margin-bottom-none"><span class="data-head">Conversion KG / CFT </span> <span class="data-value"> '.$getFinalQuotePrice[0]->final_kg_per_cft.' KG</span></div>';
								elseif(isset($getFinalQuotePrice[0]->final_kg_per_cft) && $getFinalQuotePrice[0]->final_kg_per_cft=='' && $getInitialQuotePrice[0]->initial_kg_per_cft!=''  &&  $getCounterQuotePrice[0]->counter_rate_per_kg!='')
									$row->cells [5]->value .= '
												<div class="col-md-3 form-control-fld padding-left-none form-control-fld margin-bottom-none">
									<input type="text"
									name="final_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'"  
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="final_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    placeholder="Conversion Kg/CFT *" class="ptl_final_conversion form-control form-control1 fourdigitsthreedecimals_deciVal numberVal " ></div>
                                    <div class="col-md-3 form-control-fld padding-left-none form-control-fld ">
									<input type="hidden" id="calculatoropen" style="border:none;">
									</div>';
								
								$row->cells [5]->value .='<div class="clearfix"></div>			
										
								<div class="col-md-3 form-control-fld padding-left-none form-control-fld margin-bottom-none">';
								if(isset($getInitialQuotePrice[0]->initial_quote_price) && $getInitialQuotePrice[0]->initial_quote_price=='0.0000'){
									if($dpickup == 1){
										$row->cells [5]->value .= '
										<input type="text" 
	                                    name="initial_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"   
			                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"  
			                            id="initial_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'" 
	                                    placeholder="Pickup Rs *" class="ptl_initial_pickup form-control form-control1 numberVal ">';
									}else{
										$row->cells [5]->value .= '
										<input type="text"
	                                    name="initial_quote_pickup"
			                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
			                            id="initial_quote_pickup"
	                                    placeholder="No Pickup Rs *" class="form-control form-control1 numberVal " readonly>
			                            <input type="hidden"
	                                    name="initial_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
			                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
			                            id="initial_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    placeholder="Pickup Rs *" class="ptl_initial_pickup form-control form-control1 numberVal " value="0">';
									}
								}
								elseif(isset($getCounterQuotePrice[0]->counter_quote_price) && $getCounterQuotePrice[0]->counter_quote_price !='0.0000' && isset($getFinalQuotePrice[0]->final_quote_price) && $getFinalQuotePrice[0]->final_quote_price=='0.0000'){
									if($dpickup == 1){
										$row->cells [5]->value .= '
										<input type="text"
    	                                name="final_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
			                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
		    	                        id="final_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
                	                    placeholder="Pickup Rs *" class="ptl_final_pickup form-control form-control1 numberVal ">';
									}else{
										$row->cells [5]->value .= '
										<input type="text"
    	                                name="final_quote_pickup"
			                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
		    	                        id="final_quote_pickup"
                	                    placeholder="No Pickup Rs *" class="form-control form-control1 numberVal " readonly>
		    	                        <input type="hidden"
    	                                name="final_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
			                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
		    	                        id="final_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
                	                    placeholder="Pickup Rs *" class="ptl_final_pickup form-control form-control1 numberVal " value="0">';
									}
								}
								elseif(!isset($getInitialQuotePrice[0]->initial_quote_price)){
									if($dpickup == 1){
										$row->cells [5]->value .= '
										<input type="text"
	                                    name="initial_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
			                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
			                            id="initial_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    placeholder="Pickup Rs *" class="ptl_initial_pickup form-control form-control1 numberVal ">';
									}else{
										$row->cells [5]->value .= '
										<input type="text"
	                                    name="initial_quote_pickup"
			                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
			                            id="initial_quote_pickup"
	                                    placeholder="No Pickup Rs *" class="form-control form-control1 numberVal " readonly>
			                            <input type="hidden"
	                                    name="initial_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
			                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
			                            id="initial_quote_pickup_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    placeholder="Pickup Rs *" class="ptl_initial_pickup form-control form-control1 numberVal " value="0">';
									}
								}
								$row->cells [5]->value .='</div>
										
								
								<div class="col-md-3 form-control-fld padding-left-none form-control-fld margin-bottom-none">';
								if(isset($getInitialQuotePrice[0]->initial_quote_price) && $getInitialQuotePrice[0]->initial_quote_price=='0.0000'){
									if($ddelivery == 1){
										$row->cells [5]->value .='<input type="text" 
										name="initial_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
	                                    id="initial_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'" 
			                            placeholder="Delivery Rs *" class="ptl_initial_delivery form-control form-control1 numberVal ">';
									}else{
										$row->cells [5]->value .='<input type="text"
										name="initial_quote_delivery"
	                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
	                                    id="initial_quote_delivery"
			                            placeholder="No Delivery Rs *" class="form-control form-control1 numberVal " readonly>
	                                    		<input type="hidden"
										name="initial_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
	                                    id="initial_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
			                            placeholder="Delivery Rs *" class="ptl_initial_delivery form-control form-control1 numberVal " value="0">';
									}
								}
								elseif(isset($getCounterQuotePrice[0]->counter_quote_price) && $getCounterQuotePrice[0]->counter_quote_price !='0.0000' && isset($getFinalQuotePrice[0]->final_quote_price) && $getFinalQuotePrice[0]->final_quote_price=='0.0000'){
									if($ddelivery == 1){
										$row->cells [5]->value .= '
										<input type="text"
	                                    name="final_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
			                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
			                            id="final_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    placeholder="Delivery Rs *" class="ptl_final_delivery form-control form-control1 numberVal ">';
									}else{
										$row->cells [5]->value .= '
										<input type="text"
	                                    name="final_quote_delivery"
			                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
			                            id="final_quote_delivery"
	                                    placeholder="No Delivery Rs *" class="form-control form-control1 numberVal " readonly>
			                            		<input type="hidden"
	                                    name="final_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
			                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
			                            id="final_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    placeholder="Delivery Rs *" class="ptl_final_delivery form-control form-control1 numberVal " value="0">';
										
									}
								}
								elseif(!isset($getInitialQuotePrice[0]->initial_quote_price)){
									if($ddelivery == 1){
										$row->cells [5]->value .='<input type="text"
										name="initial_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
	                                    id="initial_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
			                            placeholder="Delivery Rs *" class="ptl_initial_delivery form-control form-control1 numberVal ">';
									}else{
										$row->cells [5]->value .='<input type="text"
										name="initial_quote_delivery"
	                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
	                                    id="initial_quote_delivery"
			                            placeholder="No Delivery Rs *" class="form-control form-control1 numberVal " readonly>
	                                    <input type="hidden"
										name="initial_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
	                                    id="initial_quote_delivery_'.$buyer_id.'_'.$buyer_quote_id.'"
			                            placeholder="Delivery Rs *" class="ptl_initial_delivery form-control form-control1 numberVal " value="0">';
									}
								}
								$row->cells [5]->value .='</div>
								
									<div class="col-md-3 form-control-fld padding-left-none form-control-fld margin-bottom-none">';
								if(isset($getInitialQuotePrice[0]->initial_quote_price) && $getInitialQuotePrice[0]->initial_quote_price=='0.0000'){
									if($odacheck == 1){
									$row->cells [5]->value .= '<input type="text" 
									name="initial_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="initial_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    placeholder="ODA Rs *" class="ptl_initial_oda form-control form-control1 fourdigitstwodecimals_deciVal numberVal ">';
									}else{
										$row->cells [5]->value .= '<input type="text"
									name="initial_quote_oda"
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
                                    id="initial_quote_oda"
                                    placeholder="No ODA Rs *" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal " readonly>
                                    		<input type="hidden"
									name="initial_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
                                    id="initial_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="ODA Rs *" class="ptl_initial_oda form-control form-control1 numberVal " Value="0">';
									}
								}
								elseif(isset($getCounterQuotePrice[0]->counter_quote_price) && $getCounterQuotePrice[0]->counter_quote_price !='0.0000' && isset($getFinalQuotePrice[0]->final_quote_price) && $getFinalQuotePrice[0]->final_quote_price=='0.0000'){
									if($odacheck == 1){
										$row->cells [5]->value .= '
										<input type="text"
	                                    name="final_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
			                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
			                            id="final_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    placeholder="ODA Rs *" class="ptl_final_oda form-control form-control1 fourdigitstwodecimals_deciVal numberVal ">';
									}else{
										$row->cells [5]->value .= '
									<input type="text"
                                    name="final_quote_oda"
		                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
		                            id="final_quote_oda"
                                    placeholder="No ODA Rs *" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal " readonly>
		                            <input type="hidden"
                                    name="final_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
		                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
		                            id="final_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="ODA Rs *" class="ptl_final_oda form-control form-control1 numberVal " value="0">';
									}
								}
								elseif(!isset($getInitialQuotePrice[0]->initial_quote_price)){
									if($odacheck == 1){
										$row->cells [5]->value .= '<input type="text"
										name="initial_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
	                                    id="initial_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    placeholder="ODA Rs *" class="ptl_initial_oda form-control form-control1 fourdigitstwodecimals_deciVal numberVal ">';
									}else{
										$row->cells [5]->value .= '<input type="text"
										name="initial_quote_oda"
	                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
	                                    id="initial_quote_oda"
	                                    placeholder="No ODA Rs *" class="form-control form-control1 numberVal " readonly>
	                                    <input type="hidden"
										name="initial_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
	                                    id="initial_quote_oda_'.$buyer_id.'_'.$buyer_quote_id.'"
	                                    placeholder="ODA Rs *" class="ptl_initial_oda form-control form-control1 numberVal " value="0">';
									}
									
								}
								$row->cells [5]->value .='</div>
								
								<div class="col-md-3 form-control-fld padding-left-none form-control-fld margin-bottom-none">';
								if(isset($getInitialQuotePrice[0]->initial_transit_days) && $getInitialQuotePrice[0]->initial_transit_days=='')
									$row->cells [5]->value .= '<input type="text"
									name="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"  
                                    id="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="Transit Days *" maxlength = "2" class="form-control form-control1 numericvalidation ">';
								
								elseif(isset($getCounterQuotePrice[0]->counter_quote_price) && $getCounterQuotePrice[0]->counter_quote_price !='0.0000' &&  isset($getFinalQuotePrice[0]->final_quote_price) && $getFinalQuotePrice[0]->final_quote_price=='0.0000')
								$row->cells [5]->value .= '
									<input type="text"
                                    name="final_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
		                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
		                            id="final_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="Transit Days *" maxlength = "2" class="form-control form-control1 numericvalidation ">';
								elseif(!isset($getInitialQuotePrice[0]->initial_transit_days))
								$row->cells [5]->value .= '<input type="text"
									name="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
                                    id="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="Transit Days *" maxlength = "2" class="form-control form-control1 numericvalidation ">';
								$row->cells [5]->value .= '</div>';

								if(isset($getFinalQuotePrice[0]->final_freight_amount ) && $getFinalQuotePrice[0]->final_freight_amount!=''){
								$row->cells [5]->value .= '<div class="col-md-3 form-control-fld col-sm-3 col-xs-6 padding-none"><span class="data-head">Pickup </span> <span class="data-value">Rs '.$getFinalQuotePrice[0]->final_pick_up_rupees.' /-</span></div>';
								$row->cells [5]->value .= '<div class="col-md-3 form-control-fld col-sm-3 col-xs-6 padding-none"><span class="data-head">Delivery </span> <span class="data-value">Rs '.$getFinalQuotePrice[0]->final_delivery_rupees.' /-</span></div>';
								$row->cells [5]->value .= '<div class="col-md-3 form-control-fld col-sm-3 col-xs-6 padding-none"><span class="data-head">ODA </span> <span class="data-value">Rs '.$getFinalQuotePrice[0]->final_oda_rupees.' /-</span></div>';
								$row->cells [5]->value .= '<div class="col-md-3 form-control-fld col-sm-3 col-xs-6 padding-none"><span class="data-head">Transit Days </span> <span class="data-value">'.$getFinalQuotePrice[0]->final_transit_days.'</span></div>';
								}	
										
								$row->cells [5]->value .= '<div class="clearfix"></div>';
								if(!isset($getInitialQuotePrice[0]->initial_freight_amount) )
									$row->cells [5]->value .= '
												<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Freight Amount </span><span class="data-value" id="freight_charges_'.$buyer_id.'_'.$buyer_quote_id.'">Rs 0.00 /-</span>
									
											</div>
											<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Total Amount </span><span class="data-value" id="total_charges_'.$buyer_id.'_'.$buyer_quote_id.'">Rs 0.00 /-</span>
									
											</div>';
								elseif(isset($getInitialQuotePrice[0]->final_freight_amount) && $getInitialQuotePrice[0]->final_freight_amount=='' && isset($getCounterQuotePrice[0]->counter_freight_amount) && $getCounterQuotePrice[0]->counter_freight_amount!='')
									$row->cells [5]->value .= '
											<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Freight Amount </span><span class="data-value" id="freight_charges_'.$buyer_id.'_'.$buyer_quote_id.'">Rs 0.00 /- </span>
									
											</div>
											<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Total Amount </span><span class="data-value" id="total_charges_'.$buyer_id.'_'.$buyer_quote_id.'">Rs 0.00 /- </span>
									
											</div>';
								elseif(isset($getInitialQuotePrice[0]->final_freight_amount) && $getInitialQuotePrice[0]->final_freight_amount!='')
									$row->cells [5]->value .= '
														<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Freight Amount :  </span><span class="data-value" id="freight_charges_'.$buyer_id.'_'.$buyer_quote_id.'"> Rs '.CommonComponent::moneyFormat($getFinalQuotePrice[0]->final_freight_amount,true).' /-</span>
												
											</div>
											<div class="col-md-3 form-control-fld padding-none margin-top-none">
												<span class="data-head">Total Amount  </span><span class="data-value" id="total_charges_'.$buyer_id.'_'.$buyer_quote_id.'">Rs '.CommonComponent::moneyFormat($getFinalQuotePrice[0]->final_quote_price,true).' /-</span>
												
											</div>';
								
								
											//Tracking
											if($tracking==''){
											$row->cells [5]->value .= '
											<div class="clearfix"></div>
												<div class="col-md-3 padding-left-none track-margin">
													<div class="normal-select">
														<select class="selectpicker"  id="tracking_'.$buyer_id.'_'.$buyer_quote_id.'" name="tracking_'.$buyer_id.'_'.$buyer_quote_id.'">
															<option value="">Tracking</option>
															<option value="1">'.TRACKING_MILE_STONE.'</option>
															<option value="2">'.TRACKING_REAL_TIME.'</option>
														</select>
													</div>
												</div>';
											}
											else{
											$row->cells [5]->value .=
											'<div class="clearfix"></div>
												<div class="col-md-6 padding-none"><span class="data-head">Tracking : '.$tracking.'</span></div>
												<input type="hidden" name="tracking" id="tracking" value="'.$getSellerpost[0]->tracking.'">
													';
											}
											//Payment
											if($payment_type==''){
												$row->cells [5]->value .= '<div class="clearfix"></div>
												<h2 class="filter-head1">Payment Terms</h2>
												<div class="col-md-3 padding-left-none track-margin margin-bottom">
													<div class="normal-select">
														<select class="selectpicker ptl_payment payment_options_'.$buyer_id.'_'.$buyer_quote_id.'" id="payment_options_'.$buyer_id.'_'.$buyer_quote_id.'" name="paymentterms_'.$buyer_id.'_'.$buyer_quote_id.'">
															<option value="1">Advance</option>
															<option value="2">Cash on Delivery</option>
															<option value="3">Cash on Pickup</option>
															<option value="4">Credit</option>
														</select>
													</div>
												</div>
										
												<div class="col-md-12 padding-none" id ="show_advanced_period_'.$buyer_id.'_'.$buyer_quote_id.'">
													<div class="checkbox_inline">
														<input class="accept_payment_ptl" type="checkbox" name="accept_payment_ptl[]" id="accept_payment_ptl[]" value="1"><span class="lbl padding-8">NEFT/RTGS</span>
													</div>
													<div class="checkbox_inline">
														<input class="accept_payment_ptl" type="checkbox" name="accept_payment_ptl[]" value="2"><span class="lbl padding-8">Credit Card</span>
													</div>
													<div class="checkbox_inline">
														<input class="accept_payment_ptl" type="checkbox" name="accept_payment_ptl[]" value="3"><span class="lbl padding-8">Debit Card</span>
													</div>
												</div>
										
										
												<div class="col-md-12 form-control-fld padding-left-none" style ="display: none;" id = "show_credit_period_'.$buyer_id.'_'.$buyer_quote_id.'">
													<div class="col-md-3 form-control-fld padding-left-none">
								
													<div class="col-md-7 padding-none">
														<div class="input-prepend">
															<input class="form-control form-control1 numberVal credit_period_ptl_'.$buyer_id.'_'.$buyer_quote_id.'" type="text" name="credit_period_ptl_'.$buyer_id.'_'.$buyer_quote_id.'" id="credit_period_ptl" value="" placeholder="Credit Period"><span class="lbl padding-8">Credit Card</span>
														</div>
													</div>
													<div class="col-md-5 padding-none">
														<div class="input-prepend">
															<span class="add-on unit-days manage">
																<div class="normal-select">
																	<select class="selectpicker bs-select-hidden credit_period_units_'.$buyer_id.'_'.$buyer_quote_id.'"  id="credit_period_units" name="credit_period_units_'.$buyer_id.'_'.$buyer_quote_id.'">
																		<option value="Days">Days</option>
																		<option value="Weeks">Weeks</option>
																	</select>
											
																</div>
															</span>
														</div>
													</div>
								
								
													</div>
													<div class="col-md-12 padding-none">
														<div class="checkbox_inline" >
														<input class="accept_payment_ptl" type="checkbox" name="accept_credit_netbanking[]" value="1"><span class="lbl padding-8">Net Banking</span>
														
														</div>
														<div class="checkbox_inline">
														<input class="accept_payment_ptl" type="checkbox" name="accept_credit_netbanking[]" value="2"><span class="lbl padding-8">Cheque / DD</span>
														</div>
					
													</div>
												</div>';
											}else{
												$row->cells [5]->value .= '<div class="clearfix"></div>
												<div class="col-md-12 padding-none "><span class="data-head">Payment : '.$payment_type.'</span></div>
														  <input type="hidden" name="payment_options" id="payment_options" value="'.$payment_type.'">
														  <input type="hidden" name="credit_peroid" id="credit_peroid" value=" ">
														  <input type="hidden" name="credit_period_units" id="credit_period_units" value=" ">
														  ';
											}
									
								$row->cells [5]->value .= '</div><div class="clearfix"></div></div><div class="col-md-4 data-fld padding-none text-right pull-right">
										<div class="hide-submit">';
								if(isset($getInitialQuotePrice[0]->initial_freight_amount) && $getInitialQuotePrice[0]->initial_freight_amount!=''){
									if($getFinalQuotePrice[0]->final_freight_amount=='' && $getCounterQuotePrice[0]->counter_freight_amount!='')
									$row->cells [5]->value .= '<input id="ptl_final_quote_submit_'.$buyer_id.'_'.$buyer_quote_id.'" type="button" class="btn add-btn margin-top pull-right ptl_final_quote_submit  margin-bottom" value=" Submit " name='.$buyer_quote_id.'>';
								}
								else 
									$row->cells [5]->value .= '<input id="ptl_initail_quote_submit_'.$buyer_id.'_'.$buyer_quote_id.'" type="button" class="btn add-btn margin-top pull-right ptl_initial_quote_submit margin-bottom" value=" Submit " name='.$buyer_quote_id.'>';
							$row->cells [5]->value .= '</div>';
							if(isset($getFinalQuotePrice[0]->final_freight_amount) && $getFinalQuotePrice[0]->final_freight_amount=='' && isset($getCounterQuotePrice[0]->counter_freight_amount) && $getCounterQuotePrice[0]->counter_freight_amount!=''){
									$row->cells [5]->value .= '<div class="show-submit">
									<input id="ptl_counter_quote_submit_'.$buyer_id.'_'.$buyer_quote_id.'" type="button" class="btn add-btn margin-top pull-right ptl_counter_quote_submit margin-bottom" value=" Accept " name='.$buyer_quote_id.'>
									</div>';
							}
									$row->cells [5]->value .= '</div></form>';
			
			$data_link = url () . "/sellerposts/$buyer_quote_id";
			$row->attributes ( array (
					"class" => "col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none",
					"data_link" => $data_link 
			) );
		} );
			//Functionality to build filters in the page starts
			
			$filter = DataFilter::source ( $Query );
			//$filter->add ( 'spi.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			//$filter->add ( 'spi.to_location_id', 'From Location', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add('ptlbq.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
			$filter->add('ptlbq.to_location_id', 'To Location', 'select')->options($to_locations)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
			
                        //$filter->add ( 'spi.lkp_vehicle_type_id', 'Vehicle Type', 'select')->options($vehicle_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			//$filter->add ( 'spi.lkp_load_type_id', 'Load Type', 'select')->options($load_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
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
	 * FTL Seller Post Details List Page.
	 *
	 * @param
	 *        	$request
	 * @return Response
	 */

	public static function listRailBuyerMarketLeads($statusId, $roleId, $serviceId, $id){
		try{

			//Filters values to populate in the page

			$from_locationspincode = array(""=>"From Location");
			$to_locationspincode = array(""=>"To Location");
			
			$lkppoststatus  = DB::table('rail_seller_posts')
				->where('rail_seller_posts.id','=',$id)
				->select('rail_seller_posts.lkp_ptl_post_type_id')
				->get();
			Session::put('lkppoststatus', $lkppoststatus[0]->lkp_ptl_post_type_id);
			$Query = DB::table ( 'rail_seller_posts as sp' );
			$Query->leftjoin ( 'rail_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
			$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'sp.lkp_post_status_id' );
			$Query->leftjoin('lkp_ptl_pincodes','lkp_ptl_pincodes.id','=','spi.from_location_id');
			$Query->where('spi.seller_post_id',$id);
			//conditions to make search
			if(isset($statusId) && $statusId != ''){
				$Query->where('sp.lkp_post_status_id', $statusId);
			}
			if(isset($serviceId) && $serviceId != ''){
				$Query->where('sp.lkp_service_id', $serviceId);
			}
		
			$Query->select ( 'spi.id', 'spi.from_location_id','spi.to_location_id','spi.price',
				'sp.kg_per_cft', 'spi.transitdays' ,'sp.lkp_post_status_id','lkp_ptl_pincodes.postoffice_name',
				'sp.lkp_access_id', 'ps.post_status','sp.id as post_id','spi.from_location_id','spi.to_location_id',
				'sp.lkp_ptl_post_type_id','spi.is_cancelled','sp.lkp_post_status_id','spi.is_cancelled','sp.created_by','sp.transaction_id');

			$seller_post_items  = DB::table('rail_seller_post_items')
				->join ( 'rail_seller_posts', 'rail_seller_posts.id', '=', 'rail_seller_post_items.seller_post_id' )
				->where('rail_seller_post_items.seller_post_id',$id)
				->where ( 'rail_seller_posts.lkp_ptl_post_type_id', 2 )
				->select('*')
				->get();
			foreach($seller_post_items as $seller_post_item){
				if (!isset( $from_locationspincode [$seller_post_item->from_location_id] )) {
					$from_locationspincode [$seller_post_item->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )
						->where ( 'id', $seller_post_item->from_location_id )
						->pluck ( 'pincode' );
				}
				if (!isset( $to_locationspincode [$seller_post_item->to_location_id] )) {
					$to_locationspincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )
						->where ( 'id', $seller_post_item->to_location_id )
						->pluck ( 'pincode' );
				}
			}
			$Query->groupBy('spi.id');
			$Query->get ();		
			//Functionality to handle filters based on the selection ends
			$grid = DataGrid::source ( $Query );
			$grid->add ( 'id', 'ID', true )->style ( "display:none" );
			$grid->add ( 'from_location_id', 'From Location', 'from_location_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'to_location_id', 'To Location', 'to_location_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'price', 'Rate per kg', 'price' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'transitdays', 'Transit Days', 'transitdays' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'dummycolumn', 'Average Market Rate / Transit Time', '' )->attributes(array("class" => "col-md-3 hidden-sm hidden-md hidden-lg padding-left-none"));
			$grid->add ( 'is_cancelled', 'Status', 'lkp_post_status_id' )->attributes(array("class" => "col-md-1 padding-left-none"));
			$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
			$grid->add ( 'transaction_id', 'Transaction Id', 'transaction_id' )->style ( "display:none" );
			$grid->add ( 'created_by', 'Created by', 'created_by' )->style ( "display:none" );
			$grid->orderBy ( 'id', 'desc' );
			$grid->paginate ( 5 );
			$grid->row ( function ($row) {
			$row->cells [0]->style ( 'display:none' );
			$spId = $row->cells [0]->value;
			$row->cells[0]->value = '';
			if(Session::get('lkppoststatus') == 1 ){
				$row->cells[1]->value = '<span><input type="checkbox" name="sellerpostcheck" id="sellerpostcheck" class="checkBoxClass gridcheckboxitems" value='.$spId.'></span>'.CommonComponent::getZoneName($row->cells [1]->value);
			}
			else{
				$row->cells[1]->value = '<span><input type="checkbox" name="sellerpostcheck" id="sellerpostcheck" class="checkBoxClass gridcheckboxitems" value='.$spId.'></span>'.CommonComponent::getZonePin($row->cells [1]->value);
			}
			
			//$poststatus = $row->cells [6]->value;
			//$val = ''.CommonComponent::getSellerPostStatuss($row->cells [6]->value).'';
			$seller_post_items  = DB::table('rail_seller_post_items')
				->join ( 'rail_seller_posts', 'rail_seller_posts.id', '=', 'rail_seller_post_items.seller_post_id' )
				->where('rail_seller_post_items.id',$spId)
				->select('*','rail_seller_posts.lkp_ptl_post_type_id','rail_seller_posts.id as spid')
				->get();
			//$spostid= $seller_post_items[0]->seller_post_id;
			if($row->cells [6]->value == 1 )
				$row->cells [6]->value = "Deleted";
			else
				$row->cells [6]->value = "Open";

			if($seller_post_items[0]->units == 'Weeks')
				$row->cells [4]->value = $row->cells [4]->value." Weeks";
			else
				$row->cells [4]->value = $row->cells [4]->value." Days";
			
			$transdays=$row->cells [4]->value;			
			$row->cells [1]->attributes(array("class" => "col-md-2 padding-left-none "));
			$row->cells [2]->attributes(array("class" => "col-md-2 padding-left-none "));
			$row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none "));
			$row->cells [4]->attributes(array("class" => "col-md-2 padding-left-none "));				
			$row->cells [6]->attributes(array("class" => "col-md-1 padding-none "));
			
			$transaction_id=$row->cells [8]->value;
			$seller_user_id=$row->cells [9]->value;
				
			$row->cells [8]->style ( 'display:none' );
			$row->cells [9]->style ( 'display:none' );
			
			$row->cells [7]->value .="<div class='col-md-12 col-sm-12 col-xs-12 text-right padding-none'>
										<input type='button' class='btn red-btn pull-right submit-data underline_link spot_transaction_details_list show-data-link'  id=''.$spId.'' value='Book Now' style='display:none'>
									</div>
								</div>
								<div class='pull-right text-right'>
									<div class='info-links'>
										<a id=''.$spId.'' class='viewcount_show-data-link' data-quoteId='$spId'><span class='show-icon spot_transaction_details_list'>+</span><span class='hide-icon'>-</span> Details</a>
										<a href='#' class='new_message' data-transaction_no='".$transaction_id."' data-userid='".$seller_user_id."' data-buyerquoteitemid='".$spId."'><i class='fa fa-envelope-o'></i></a>
									</div>
								</div>			
								<div class='col-md-12 show-data-div padding-top' style='display: none;'>
										<div class='col-md-12'>
											<div class='col-md-3 padding-left-none data-fld'>
												<span class='data-head'>Transit Days</span>
												<span class='data-value'>$transdays</span>
											</div>
										</div>
								<div>																		
								</div>";
			} );

			if($lkppoststatus[0]->lkp_ptl_post_type_id==1){
				//$fromselect=$from_locationszone;
				//$toselect=$to_locationszone;
				$fromselect="";
				$toselect="";
				Session::put('loc_type', '1');
			}
			if($lkppoststatus[0]->lkp_ptl_post_type_id==2){
				$fromselect=$from_locationspincode;
				$toselect=$to_locationspincode;
				Session::put('loc_type', '1');
			}
			//Functionality to build filters in the page starts
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'spi.from_location_id', 'From Location', 'select')->options($fromselect)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.to_location_id', 'From Location', 'select')->options($toselect)->attr("class","selectpicker")->attr("onchange","this.form.submit()");			
			$filter->add ( 'sp.from_date', 'From', 'date' )->attr("class","filter_calendar");
			$filter->add ( 'sp.to_date', 'To', 'date' )->attr("class","filter_calendar");

			$filter->submit('search');
			$filter->reset('reset');
			$filter->build();
			//Functionality ptl_seller_poststo build filters in the page ends

			$result = array();
			$result['grid'] = $grid;
			$result['filter'] = $filter;
			return $result;


		} catch( Exception $e ) {
			return $e->message;
		}
	}
	
	
}
