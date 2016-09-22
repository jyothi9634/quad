<?php
namespace App\Components\Courier;
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
use App\Models\FtlSearchTerm;
use App\Models\PtlSellerPostItemView;
use App\Components\Matching\SellerMatchingComponent;
use App\Components\MessagesComponent;

use Redirect;
use PhpParser\Node\Stmt\Else_;

class CourierSellerListingComponent {
	
	/**
	 * Submitting Seller Initial Quote
	 *	
	 * @param  $request
	 * @return Response
	 */
	public static function CourierSellerList($statusId, $roleId, $serviceId) {
	
		// Filters values to populate in the page
		$fromselect=array();
		$toselect=array();
		$ptlLocationWise = array ("" => "Select");
		$ptlFromLocationPincode = array ("" => "From Location-Pincode");
		
		if(Session::get('destinationtype') == 1){
			$ptlToLocationPincode = array ("" => "To Location-Pincode");
		}else{
			$ptlToLocationPincode = array ("" => "To Country");
		}
		$ptlFromLocationZone = array ("" => "From Zone");		
		if(Session::get('destinationtype') == 1){
			$ptlToLocationZone = array ("" => "To Zone");
		}else{
			$ptlToLocationZone = array ("" => "To Country");
		}
		$ptlCourierTypes = array ("" => "Courier Type");

		// query to retrieve seller posts list and bind it to the grid
		$Query = DB::table ( 'courier_seller_posts as psp' );
		$Query->leftjoin ( 'courier_seller_post_items as pspi', 'pspi.seller_post_id', '=', 'psp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'psp.lkp_post_status_id' );
		if(isset($_REQUEST['lead_name']) && ($_REQUEST['lead_name'] ==2)){
			Session::put('leads', '2');
			$Query->where('psp.lkp_access_id',1);
		}
		else{
			Session::put('leads', '1');
			$Query->leftjoin ( 'courier_buyer_quote_selected_sellers as bqss', 'bqss.seller_id', '=', 'pspi.created_by' );
		}
		$Query->where('psp.seller_id',Auth::user()->id);
		
		//conditions to make search
		if(isset($statusId) && $statusId != '' && $statusId!=0){
			$Query->where('psp.lkp_post_status_id', $statusId);
		}
		if(Session::get ( 'service_id' )  == COURIER){
			$destinationtype = Session::get('destinationtype');
			if(isset($destinationtype) && $destinationtype != ''){
			$Query->where('psp.lkp_courier_delivery_type_id', '=', Session::get('destinationtype'));
			}
		}
		if(Session::get ( 'service_id' )  == COURIER){
			$deliverytype = Session::get('deliverytype');
			if(isset($deliverytype) && $deliverytype != ''){
				$Query->where('psp.lkp_courier_type_id', '=', Session::get('deliverytype'));
			}
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
	
		$sellerresults = $Query->select ( 'psp.id','psp.lkp_courier_type_id','psp.from_date','psp.to_date','psp.lkp_access_id','psp.lkp_post_status_id')
		->groupBy('psp.id')
		->get ();
		//Functionality to handle filters based on the selection starts
		foreach($sellerresults as $seller){
			$seller_post_items  = DB::table('courier_seller_post_items')
			->join ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
			->where('courier_seller_post_items.seller_post_id',$seller->id)
			->where ( 'courier_seller_posts.lkp_ptl_post_type_id', 2 )
			->select('*')
			->get();
			foreach($seller_post_items as $seller_post_item){
				if (!isset( $ptlFromLocationPincode [$seller_post_item->from_location_id] )) {
					$ptlFromLocationPincode [$seller_post_item->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $seller_post_item->from_location_id )->pluck ( 'pincode' );
				}
				if (!isset( $ptlToLocationPincode [$seller_post_item->to_location_id] )) {
					
					if(Session::get('destinationtype') == 2){
						//echo $seller_post_item->to_location_id;exit;
						$ptlToLocationPincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_countries' )->where ( 'id', $seller_post_item->to_location_id )->pluck ( 'country_name' );
					}else{
						$ptlToLocationPincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $seller_post_item->to_location_id )->pluck ( 'pincode' );
					}
				}
				if(Session::get ( 'service_id' )  == COURIER){
				if (!isset( $ptlCourierTypes [$seller->lkp_courier_type_id] )) {
					$ptlCourierTypes [$seller->lkp_courier_type_id] = DB::table ( 'lkp_courier_types' )->where ( 'id', $seller->lkp_courier_type_id )->pluck ( 'courier_type' );
				}
				}
			}
		}
		
		foreach($sellerresults as $seller){
			$seller_post_items  = DB::table('courier_seller_post_items')
			->join ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
			->where('courier_seller_post_items.seller_post_id',$seller->id)
			->where ( 'courier_seller_posts.lkp_ptl_post_type_id', 1 )
			->select('*')
			->get();
			foreach($seller_post_items as $seller_post_item){
				if (!isset( $ptlFromLocationZone [$seller_post_item->from_location_id] )) {
					$ptlFromLocationZone [$seller_post_item->from_location_id] = DB::table ( 'ptl_zones' )
					->where ( 'id', $seller_post_item->from_location_id )
					->pluck ( 'zone_name' );
				}
				if (!isset( $ptlToLocationZone [$seller_post_item->to_location_id] )) {
					if(Session::get('destinationtype') == 2){
						//echo $seller_post_item->to_location_id;exit;
						$ptlToLocationZone [$seller_post_item->to_location_id] = DB::table ( 'lkp_countries' )->where ( 'id', $seller_post_item->to_location_id )->pluck ( 'country_name' );
					}else{
					$ptlToLocationZone [$seller_post_item->to_location_id] = DB::table ( 'ptl_zones' )
					->where ( 'id', $seller_post_item->to_location_id )
					->pluck ( 'zone_name' );
					}
				}
			}
			if(Session::get ( 'service_id' )  == COURIER){
					if (!isset( $ptlCourierTypes [$seller->lkp_courier_type_id] )) {
						$ptlCourierTypes [$seller->lkp_courier_type_id] = DB::table ( 'lkp_courier_types' )->where ( 'id', $seller->lkp_courier_type_id )->pluck ( 'courier_type' );
					}
			}
		}
		$ptlFromLocationZone = CommonComponent::orderArray($ptlFromLocationZone);
		$ptlToLocationZone = CommonComponent::orderArray($ptlToLocationZone);
		$ptlCourierTypes = CommonComponent::orderArray($ptlCourierTypes);
		$ptlFromLocationPincode = CommonComponent::orderArray($ptlFromLocationPincode);
		$ptlToLocationPincode = CommonComponent::orderArray($ptlToLocationPincode);
		
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
		$grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class"=>"col-md-3 padding-left-none"));
		$grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class"=>"col-md-3 padding-left-none"));
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
			
			
			$getpostitemids = DB::table('courier_seller_post_items')
			->where('courier_seller_post_items.seller_post_id','=',$spId)
			->select('courier_seller_post_items.*')
			->get();
			if(isset($getpostitemids[0]->is_private)){
				$privatepost = $getpostitemids[0]->is_private;
			}else{
				$privatepost = 0;
			}
			$Ptlseller_post_items  = DB::table('courier_seller_post_items')
			->join('courier_seller_posts','courier_seller_posts.id','=','courier_seller_post_items.seller_post_id')
			->where('courier_seller_post_items.seller_post_id',$spId)
			->select('*')
			->get();
				
			
			//count for seller documents
			$serviceId = Session::get('service_id');
			$docs_seller_courier    =   CommonComponent::getGsaDocuments(SELLER,$serviceId,$spId);
			
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
			
			
			$allcountview =0;
			if(count($getpostitemids)>0){
				for($i=0;$i<count($getpostitemids);$i++){
			
					$countview = DB::table('courier_seller_post_item_views')
					->where('courier_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
					->select('courier_seller_post_item_views.id','courier_seller_post_item_views.view_counts')
					->get();
					if(isset($countview[0]->view_counts))
						$allcountview +=  $countview[0]->view_counts;
			
				}
			}
			
			$seller_post_items  = DB::table('courier_seller_post_items')
			->join ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
			->where('courier_seller_post_items.seller_post_id',$spId)
			->select('courier_seller_post_items.*','courier_seller_posts.lkp_ptl_post_type_id','courier_seller_post_items.from_location_id','courier_seller_post_items.to_location_id')
			->get();
			
			//Enquiries Count
			$total_count=0;
			if($privatepost==1){
			
				$total_count = count(CommonComponent::getPrivateBuyerMatchedResults(COURIER,$seller_post_items[0]->seller_post_id));
			
			}else{
				if(isset($seller_post_items[0]->from_location_id) && isset($seller_post_items[0]->to_location_id) && isset($seller_post_items[0]->lkp_ptl_post_type_id)){
				for($k=0;$k<count($seller_post_items);$k++)
					$total_count += count(SellerMatchingComponent::getMatchedResults(COURIER, $seller_post_items[$k]->id));
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
						$lead_count += count(SellerMatchingComponent::getSellerLeads(COURIER, $seller_post_items[$k]->id));
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
					<div class="col-md-2 padding-left-none text-right">
						<a href="javascript:void(0)"  data-target="#cancelsellerpostmodal" data-toggle="modal" onclick="setcancelpostid(\'posts\','.$spId.')">
						<i class="fa fa-trash" title="Delete"></i>
						</a>
					</div>';
			}		
					
					$row->cells [5]->value .= '<div class="clearfix"></div>
					<div class="pull-left">
						<div class="info-links">
							<a><i class="fa fa-envelope-o"></i> Messages <span class="badge">'.$msg_count.'</span></a>
							<a>
								<i class="fa fa-file-text-o"></i>Enquiries
								<span class="badge">';
			$row->cells [5]->value .=$total_count;
			$row->cells [5]->value .='
								</span>
							</a>
							<a><i class="fa fa-bullseye"></i> Leads<span class="badge">'.$lead_count.'</span></a>
							<a><i class="fa fa-line-chart"></i> Market Analytics</a>
							<a><i class="fa fa-file-text-o"></i> Documentation <span class="badge">'.count($docs_seller_courier).'</span></a>
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
			
			$row->attributes(array("class" => "col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none table-row  mobile-padding-none"));
			
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
		$filter->add ( 'pspi.lkp_courier_type_id', 'Courier Type', 'select' )->options ( $ptlCourierTypes )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
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
	
	public static function listCourierSellerPostItems($statusId, $roleId, $serviceId, $id){
		try{
	
			//Filters values to populate in the page
			$load_types = array(""=>"Load Type");
			
			$lkppoststatus  = DB::table('courier_seller_posts')
			->where('courier_seller_posts.id','=',$id)
			->select('courier_seller_posts.lkp_ptl_post_type_id','courier_seller_posts.lkp_courier_delivery_type_id')
					->get();
			
			
			$from_locationspincode = array ("" => "From Location-Pincode");
			if($lkppoststatus[0]->lkp_courier_delivery_type_id == 1){
				$to_locationspincode = array ("" => "To Location-Pincode");
			}else{
				$to_locationspincode = array ("" => "To Country");
			}
			
			$from_locationszone = array ("" => "From Zone");
			if(Session::get('destinationtype') == 1){
				$to_locationszone = array ("" => "To Zone");
			}else{
				$to_locationszone = array ("" => "To Country");
			}
			
			Session::put('lkppoststatus', $lkppoststatus[0]->lkp_ptl_post_type_id);
			$Query = DB::table ( 'courier_seller_posts as sp' );
			$Query->leftjoin ( 'courier_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
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
				
				$Query->leftjoin ( 'courier_buyer_quote_sellers_quotes_prices as bqss', 'bqss.seller_id', '=', 'spi.created_by' );
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
						'sp.conversion_factor', 'spi.transitdays' ,'sp.lkp_post_status_id','ptl_zones.zone_name as postoffice_name',
						'sp.lkp_access_id', 'ps.post_status','sp.id as post_id','spi.from_location_id','spi.to_location_id',
						'sp.lkp_ptl_post_type_id','spi.is_cancelled','sp.lkp_post_status_id','spi.is_cancelled','sp.lkp_courier_delivery_type_id');
				
				Session::put('postedType', 'Zone');
				
				$seller_post_items  = DB::table('courier_seller_post_items')
				->join ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
				->where('courier_seller_post_items.seller_post_id',$id)
				->where ( 'courier_seller_posts.lkp_ptl_post_type_id', 1 )
				->select('*')
				->get();
				foreach($seller_post_items as $seller_post_item){
					if (!isset( $from_locationszone [$seller_post_item->from_location_id] )) {
						$from_locationszone [$seller_post_item->from_location_id] = DB::table ( 'ptl_zones' )
						->where ( 'id', $seller_post_item->from_location_id )
						->pluck ( 'zone_name' );
					}
					if (!isset( $to_locationszone [$seller_post_item->to_location_id] )) {
						if($seller_post_item->lkp_courier_delivery_type_id==2){
							$to_locationszone [$seller_post_item->to_location_id] = DB::table ( 'lkp_countries' )
							->where ( 'id', $seller_post_item->to_location_id )
							->pluck ( 'country_name' );
						}else{
							$to_locationszone [$seller_post_item->to_location_id] = DB::table ( 'ptl_zones' )
							->where ( 'id', $seller_post_item->to_location_id )
							->pluck ( 'zone_name' );
						}
					}
				}
				
			}
			else{
				$sellerresults = $Query->select ( 'spi.id', 'spi.from_location_id','spi.to_location_id','spi.price',
						'sp.conversion_factor', 'spi.transitdays' ,'sp.lkp_post_status_id','lkp_ptl_pincodes.postoffice_name',
						'sp.lkp_access_id', 'ps.post_status','sp.id as post_id','spi.from_location_id','spi.to_location_id',
						'sp.lkp_ptl_post_type_id','spi.is_cancelled','sp.lkp_post_status_id','spi.is_cancelled','sp.lkp_courier_delivery_type_id');
				Session::put('postedType', 'Pincode');
				
				
				$seller_post_items  = DB::table('courier_seller_post_items')
				->join ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
				->where('courier_seller_post_items.seller_post_id',$id)
				->where ( 'courier_seller_posts.lkp_ptl_post_type_id', 2 )
				->select('*')
				->get();
				foreach($seller_post_items as $seller_post_item){
					if (!isset( $from_locationspincode [$seller_post_item->from_location_id] )) {
						$from_locationspincode [$seller_post_item->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )
						->where ( 'id', $seller_post_item->from_location_id )
						->pluck ( 'pincode' );
					}
					if (!isset( $to_locationspincode [$seller_post_item->to_location_id] )) {
						if($seller_post_item->lkp_courier_delivery_type_id==2){
							$to_locationspincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_countries' )
								->where ( 'id', $seller_post_item->to_location_id )
								->pluck ( 'country_name' );
						}else{
							$to_locationspincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )
									->where ( 'id', $seller_post_item->to_location_id )
									->pluck ( 'pincode' );
						}
					}
				}
				
			
			}
			$Query->groupBy('spi.id');
			$Query->get ();
			//Functionality to handle filters based on the selection starts
			
			//Functionality to handle filters based on the selection ends
	
			$grid = DataGrid::source ( $Query );
	
			$grid->add ( 'id', 'ID', true )->style ( "display:none" );
			$grid->add ( 'from_location_id', 'From', 'from_location_id' )->attributes(array("class" => "col-md-3 col-sm-2 col-xs-5 padding-none"));
			$grid->add ( 'to_location_id', 'To', 'to_location_id' )->attributes(array("class" => "col-md-3 col-sm-2 col-xs-2 padding-none hidden-xs"));
			$grid->add ( 'price', 'Rate per kg', 'price' )->style ( "display:none" );
			$grid->add ( 'transitdays', 'Transit Days', 'transitdays' )->attributes(array("class" => "col-md-3 col-sm-2 col-xs-2 padding-none"));
			$grid->add ( 'conversion_factor/transitdays', 'Average Market Rate / Transit Time', '' )->style ( "display:none" );
			$grid->add ( 'is_cancelled', 'Status', 'lkp_post_status_id' )->attributes(array("class" => "col-md-3 col-sm-2 col-xs-2 padding-none"));
			$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
			$grid->add ( 'lkp_courier_delivery_type_id', 'Courier Type', true )->style ( "display:none" );
			$grid->orderBy ( 'id', 'desc' );
			$grid->paginate ( 5 );
			$grid->row ( function ($row) {
				$row->cells [0]->style ( 'display:none' );
				$row->cells [3]->style ( 'display:none' );
				$row->cells [5]->style ( 'display:none' );
				$row->cells [8]->style ( 'display:none' );
				$spId = $row->cells [0]->value;
				$row->cells[0]->value = '';
				if(Session::get('lkppoststatus') == 1 ){
					$row->cells[1]->value = '<span><input type="checkbox" name="sellerpostcheck" id="sellerpostcheck" class="checkBoxClass gridcheckboxitems" value='.$spId.'></span>'.CommonComponent::getZoneName($row->cells [1]->value);
				}else{
					$row->cells[1]->value = '<span><input type="checkbox" name="sellerpostcheck" id="sellerpostcheck" class="checkBoxClass gridcheckboxitems" value='.$spId.'></span>'.CommonComponent::getZonePin($row->cells [1]->value);
				}
				//$row->cells [5]->style ( 'text-align:right' );
				//$row->cells [5]->value = 'N/A' ;
				$row->cells [6]->style ( 'width:100%' );
				//View Count
				$countview = DB::table('courier_seller_post_item_views')
				->where('courier_seller_post_item_views.seller_post_item_id','=',$spId)
				->select('courier_seller_post_item_views.id','courier_seller_post_item_views.view_counts')
				->get();
				if(!isset($countview[0]->view_counts))
					$countview = 0;
				else
					$countview = $countview[0]->view_counts;
	
				if(Session::get('lkppoststatus') == 1 ){
					if($row->cells [8]->value == 1)
						$row->cells [2]->value = ''.CommonComponent::getZoneName($row->cells [2]->value).'';
					else 
						$row->cells [2]->value = ''.CommonComponent::getCountry($row->cells [2]->value).'';
				}
				else{
					if($row->cells [8]->value == 1)
						$row->cells [2]->value = ''.CommonComponent::getZonePin($row->cells [2]->value).'';
					else
						$row->cells [2]->value = ''.CommonComponent::getCountry($row->cells [2]->value).'';
				}

				$poststatus = $row->cells [6]->value;
				
				//$val = ''.CommonComponent::getSellerPostStatuss($row->cells [6]->value).'';
				
				
				
				$seller_post_items  = DB::table('courier_seller_post_items')
				->join ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
				->where('courier_seller_post_items.id',$spId)
				->select('courier_seller_post_items.*','courier_seller_posts.lkp_ptl_post_type_id','courier_seller_posts.id as spid')
				->get();
				
				//count for seller documents
				$serviceId = Session::get('service_id');
				$docs_seller_courier    =   CommonComponent::getGsaDocuments(SELLER,$serviceId,$seller_post_items[0]->spid);
				
				//$spostid= $seller_post_items[0]->seller_post_id;
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
				$row->cells [1]->attributes(array("class" => "col-md-3 padding-left-none html_link","data_link"=>$data_link));
				$row->cells [2]->attributes(array("class" => "col-md-3 padding-left-none html_link","data_link"=>$data_link));
				//$row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none html_link","data_link"=>$data_link));
				$row->cells [4]->attributes(array("class" => "col-md-3 padding-left-none html_link","data_link"=>$data_link));
				//$row->cells [5]->attributes(array("class" => "col-md-3 padding-none html_link","data_link"=>$data_link));
				$row->cells [6]->attributes(array("class" => "col-md-3 padding-none html_link","data_link"=>$data_link));
				
				//Enquiries count
				$total_count = 0;
				if($seller_post_items[0]->is_private == 1){
					$total_count = count(CommonComponent::getPrivateBuyerMatchedResults(COURIER,$seller_post_items[0]->spid));
				}else{
					if (isset($seller_post_items[0]->from_location_id) && isset($seller_post_items[0]->to_location_id) && isset($seller_post_items[0]->lkp_ptl_post_type_id)) {
						//$total_count = PtlSellerListingComponent::enquiryCount(2, $seller_post_items[0]->from_location_id, $seller_post_items[0]->to_location_id, $seller_post_items[0]->lkp_ptl_post_type_id);
						
						$total_count += count(SellerMatchingComponent::getMatchedResults(COURIER, $spId));
					}else
						$total_count =0;
				}
				
				//Leads Count
				$lead_count = 0;
				if($seller_post_items[0]->is_private == 1){
					$lead_count = 0;
				}else{
					if (isset($seller_post_items[0]->from_location_id) && isset($seller_post_items[0]->to_location_id) && isset($seller_post_items[0]->lkp_ptl_post_type_id)) {
						//$total_count = PtlSellerListingComponent::enquiryCount(2, $seller_post_items[0]->from_location_id, $seller_post_items[0]->to_location_id, $seller_post_items[0]->lkp_ptl_post_type_id);
						$lead_count += count(SellerMatchingComponent::getSellerLeads(COURIER, $spId));
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
										<a href="/sellerpostdetail/'.$spId.'?type=documentation"><i class="fa fa-file-text-o"></i> Documentation <span class="badge">'.count($docs_seller_courier).'</span></a>
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
			$filter->add ( 'spi.to_location_id', 'To Location', 'select')->options($toselect)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.lkp_courier_type_id', 'Courier Type', 'select')->options($load_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
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
	
	public static function listCourierSellertopNavPostItems($id){
		
		$seller_post_items  = DB::table('courier_seller_posts')
							->where('courier_seller_posts.id',$id)
							->select('courier_seller_posts.id','courier_seller_posts.transaction_id','courier_seller_posts.conversion_factor',
									 'courier_seller_posts.lkp_access_id','courier_seller_posts.from_date',
									 'courier_seller_posts.to_date','courier_seller_posts.lkp_payment_mode_id',
									 'courier_seller_posts.tracking','courier_seller_posts.accept_payment_netbanking'
									 ,'courier_seller_posts.accept_payment_credit','courier_seller_posts.accept_payment_debit'
									 ,'courier_seller_posts.accept_credit_netbanking','courier_seller_posts.accept_credit_cheque'
									 ,'courier_seller_posts.cancellation_charge_text','courier_seller_posts.cancellation_charge_price'
									 ,'courier_seller_posts.docket_charge_text','courier_seller_posts.docket_charge_price','courier_seller_posts.terms_conditions'
									 ,'courier_seller_posts.other_charge1_text','courier_seller_posts.other_charge1_price'
									 ,'courier_seller_posts.other_charge2_text','courier_seller_posts.other_charge2_price'
									 ,'courier_seller_posts.other_charge3_text','courier_seller_posts.other_charge3_price'
									 ,'courier_seller_posts.conversion_factor','courier_seller_posts.max_weight_accepted'
									 ,'courier_seller_posts.lkp_ict_weight_uom_id','courier_seller_posts.is_incremental','courier_seller_posts.credit_period','courier_seller_posts.credit_period_units'
									 ,'courier_seller_posts.increment_weight','courier_seller_posts.rate_per_increment'
									 ,'courier_seller_posts.fuel_surcharge','courier_seller_posts.cod_charge'
									 ,'courier_seller_posts.freight_collect_charge','courier_seller_posts.arc_charge'
									 ,'courier_seller_posts.maximum_value')
							->get();
		
		if($seller_post_items[0]->tracking == 1)
			$tracking = 'Milestone';
		else 
			$tracking = 'Real Time';
		if(isset($seller_post_items[0]->lkp_ict_weight_uom_id) && $seller_post_items[0]->lkp_ict_weight_uom_id!=''){
			$weight = CommonComponent::getWeight($seller_post_items[0]->lkp_ict_weight_uom_id);
		}
		else{ 
			$weight ='';
		}
		
		
		$seller_post_slab_values  = DB::table('courier_seller_posts')
		->join ( 'courier_seller_post_item_slabs', 'courier_seller_post_item_slabs.seller_post_id', '=', 'courier_seller_posts.id' )
		->where('courier_seller_posts.id',$id)
		->select('courier_seller_post_item_slabs.*')
		->get();
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
			$privatebuyers  = DB::table('courier_seller_sellected_buyers')
			->leftjoin ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_sellected_buyers.seller_post_id' )
			->leftjoin('users','users.id','=','courier_seller_sellected_buyers.buyer_id')
			->where('courier_seller_sellected_buyers.created_by',Auth::user()->id)
			->where('courier_seller_sellected_buyers.seller_post_id',$id)
			->select('users.username')
			->get();
		
		$privatepost  = DB::table('courier_seller_post_items')
		->where('courier_seller_post_items.seller_post_id',$id)
		->select('courier_seller_post_items.is_private','courier_seller_post_items.price')
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
		if($seller_post_items[0]->lkp_access_id == 1){

	$postdetails .=				
				'<div>
					<p class="search-head">Public</p>
					<span class="search-result">Yes</span>
				</div>';
		}elseif ($seller_post_items[0]->lkp_access_id == 3) {
                  $postdetails .=
						'<div>
							<p class="search-head">Quote</p>
							<span class="search-result">';
							foreach($privatebuyers as $pdetails){
							$postdetails .= $pdetails->username.' | ';
							}
                  $postdetails .='</span>
				</div>';
          }else{

	$postdetails .=
						'<div>
							<p class="search-head">Private</p>
							<span class="search-result">';
							foreach($privatebuyers as $pdetails){
							$postdetails .= $pdetails->username.' | ';
							}
			$postdetails .='</span>
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
		$postdetails.='<div class="col-md-3 form-control-fld">Conversion Factor (CCM/KG): '.$seller_post_items[0]->conversion_factor.'</div>
					   <div class="col-md-3 form-control-fld">Maximum Weight Accepted: '.$seller_post_items[0]->max_weight_accepted.' '.$weight.'</div>';
					   		
					   		
		if($privatepost[0]->is_private!=1){
		$postdetails.='<div class="table-div table-style1">
							<h2 class="filter-head1 margin-left-none">Pricing Details</h2>
					
							<!-- Table Head Starts Here -->
	
							<div class="table-heading inner-block-bg">
								<div class="col-md-2 padding-left-none">Minimum Weight<i class="fa fa-caret-down"></i></div>
								<div class="col-md-2 padding-left-none">Maximum Weight<i class="fa fa-caret-down"></i></div>
								<div class="col-md-3 padding-left-none">Price<i class="fa fa-caret-down"></i></div>
							</div>
	
							<!-- Table Head Ends Here -->
	
							<div class="table-data">';
	
		
							if(count($seller_post_slab_values)>0){	
								foreach($seller_post_slab_values as $slab){
								$postdetails.='
									<!-- Table Row Ends Here -->	
									<div class="table-row inner-block-bg">
										<div class="col-md-2 padding-left-none">'.$slab->slab_min_rate.'</div>
										<div class="col-md-2 padding-left-none">'.$slab->slab_max_rate.'</div>
										<div class="col-md-3 padding-left-none">'.$slab->price.'</div>
									</div>
		
									<!-- Table Row Ends Here -->';
								}
							}else{
								
								$postdetails.='
									<!-- Table Row Ends Here -->
									<div class="table-row inner-block-bg">
										<div class="col-md-2 padding-left-none">-</div>
										<div class="col-md-2 padding-left-none">-</div>
										<div class="col-md-3 padding-left-none">-</div>
									</div>
								
									<!-- Table Row Ends Here -->';
							}
	
								
	
							$postdetails.='</div>
						</div>';
						}
						
						if(isset($seller_post_items[0]->is_incremental) && $seller_post_items[0]->is_incremental==1){
							if(isset($seller_post_items[0]->lkp_ict_weight_uom_id)){
								if($seller_post_items[0]->lkp_ict_weight_uom_id == 1){
									$uomunits= 'Kgs';
								}elseif($seller_post_items[0]->lkp_ict_weight_uom_id == 2){
									$uomunits= 'Gms';
								}else{
									$uomunits= 'Mts';
								}
							}else{
								$uomunits= 'Kgs';
							}
							$postdetails.='
								<div class="col-md-3 form-control-fld">Incremental Weight: '.$seller_post_items[0]->increment_weight.' '.$uomunits.' </div>
								<div class="col-md-3">Rate Per Incremental Weight: '.$seller_post_items[0]->rate_per_increment.' Rs</div>';
						}
						
					   	$postdetails.='<div class="col-md-12">
							<h5 class="caption-head">Additional Charges</h5>
							<div class="col-md-3 form-control-fld">Fuel Surcharge: '.$seller_post_items[0]->fuel_surcharge.'</div>	
							<div class="col-md-9 form-control-fld">
								<div class="col-md-3 form-control-fld">COD: '.$seller_post_items[0]->cod_charge.'</div>	
								<div class="col-md-3 form-control-fld">Freight Collect: '.$seller_post_items[0]->freight_collect_charge.'</div>
								<div class="col-md-3 form-control-fld">ARC: '.$seller_post_items[0]->arc_charge.'</div>
								<div class="col-md-3 form-control-fld">Maximum Value: '.$seller_post_items[0]->maximum_value.'</div>
							</div>
						</div>
					   	</div>
					   	<div class="col-md-12">';
	

		
	
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
		if($seller_post_items[0]->lkp_access_id == 2){
		$postdetails .='<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">Rate Per kG</span>
							<span class="data-value">'.CommonComponent::getPriceType($privatepost[0]->price).'</span>
						</div>';
		}
		$postdetails .='<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">Documents</span>
							<span class="data-value">'.count(CommonComponent::getGsaDocuments(SELLER,Session::get('service_id'),$id)).'</span>
						</div>';
		
						
						
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
	
	public static function listCourierSellerPostDetailsItems($id){
		Session::put('courier_seller_post_item', $id);
		try{
			$viewcount  = DB::table('courier_seller_post_items')
					->where('courier_seller_post_items.id','=',$id)
					->select('courier_seller_post_items.id',
							'courier_seller_post_items.created_by')
					->get();
	
			
			
				$countview = DB::table('courier_seller_post_item_views')
				->where('courier_seller_post_item_views.seller_post_item_id','=',$id)
				->select('courier_seller_post_item_views.id','courier_seller_post_item_views.view_counts')
				->get();
				if(!isset($countview[0]->view_counts))
					$countview = 0;
				else
					$countview = $countview[0]->view_counts;
			
			
			$seller_post = DB::table('courier_seller_posts')
			->join('courier_seller_post_items','courier_seller_post_items.seller_post_id','=','courier_seller_posts.id')
			->where('courier_seller_post_items.id',$id)
			->select('courier_seller_posts.*','courier_seller_post_items.id')
			->get();
			$sellerpostid = DB::table('courier_seller_posts')
			->join('courier_seller_post_items','courier_seller_post_items.seller_post_id','=','courier_seller_posts.id')
			->where('courier_seller_post_items.id',$id)
			->select('courier_seller_posts.id')
			->get();
			
			//count for seller documents
			$serviceId = Session::get('service_id');
			$docs_seller_courier    =   CommonComponent::getGsaDocuments(SELLER,$serviceId,$sellerpostid);
			
			$seller_post_items  = DB::table('courier_seller_post_items')
			->where('courier_seller_post_items.id',$id)
			->select('*')
			->get();
	
			$getUserrole = DB::table('users')
			->where('users.id', Auth::user()->id)
			->select('users.primary_role_id','users.is_business')
			->first();
				
				
			if($getUserrole->is_business == 1){
				$stable = 'seller_details';
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

		//Enquires code
		$matchedIds = array();
		if($seller_post_items[0]->is_private == 1){
			$matchedIds[] = CommonComponent::getPrivateBuyerMatchedResults(COURIER,$seller_post_items[0]->seller_post_id);
		}
		else{
			$buyer_quote_items_matched_data = SellerMatchingComponent::getMatchedResults(COURIER, $id);
			foreach($buyer_quote_items_matched_data as $buyer_quote_matched_item){
				$matchedIds[] = $buyer_quote_matched_item->buyer_quote_id;
			}
		}
		//echo "<pre>";print_R($matchedIds); echo "</pre>";
		if(!empty($matchedIds) && count($matchedIds) > 0){
				
			$buyerquoteid    = DB::table('courier_buyer_quotes')
			->leftjoin('courier_buyer_quote_items','courier_buyer_quote_items.buyer_quote_id','=','courier_buyer_quotes.id')
			->leftjoin('users','users.id','=','courier_buyer_quotes.created_by')
			->leftjoin('lkp_courier_types','lkp_courier_types.id','=','courier_buyer_quote_items.lkp_courier_type_id')
			->leftjoin('lkp_ptl_pincodes','lkp_ptl_pincodes.id','=','courier_buyer_quotes.from_location_id')
			->leftjoin('lkp_courier_delivery_types','lkp_courier_delivery_types.id','=','courier_buyer_quote_items.lkp_courier_delivery_type_id')
			->leftjoin('lkp_ict_weight_uom','lkp_ict_weight_uom.id','=','courier_buyer_quote_items.lkp_ict_weight_uom_id')
			->leftjoin('lkp_cities','lkp_cities.id','=','courier_buyer_quotes.from_location_id')
			->whereIn('courier_buyer_quotes.id',$matchedIds)
			->select('courier_buyer_quotes.transaction_id as transaction_no','courier_buyer_quotes.id as ptlquoteid','courier_buyer_quote_items.id','courier_buyer_quote_items.created_by as buyer_id','users.username','courier_buyer_quotes.dispatch_date',
					'courier_buyer_quotes.delivery_date','courier_buyer_quote_items.lkp_quote_price_type_id',
					'courier_buyer_quotes.from_location_id','courier_buyer_quotes.to_location_id',
					'lkp_cities.city_name','courier_buyer_quotes.from_location_id',
					'courier_buyer_quote_items.lkp_courier_type_id','courier_buyer_quotes.lkp_post_status_id',
					'courier_buyer_quote_items.lkp_courier_delivery_type_id','courier_buyer_quote_items.package_value',
					'courier_buyer_quote_items.calculated_volume_weight',
					'courier_buyer_quote_items.units','courier_buyer_quote_items.lkp_ict_weight_uom_id',
					'courier_buyer_quote_items.number_packages','lkp_courier_types.courier_type','lkp_ptl_pincodes.postoffice_name',
					'lkp_ptl_pincodes.pincode','lkp_courier_delivery_types.courier_delivery_type','lkp_ict_weight_uom.weight_type'
			)
			->groupBy('courier_buyer_quotes.id')
			->get();
				
			
			for($i=0;$i<count($buyerquoteid);$i++){
				$buyersquotes	= DB::table('courier_buyer_quote_sellers_quotes_prices')
				->where('courier_buyer_quote_sellers_quotes_prices.buyer_quote_id',$buyerquoteid[$i]->ptlquoteid)
				->where('courier_buyer_quote_sellers_quotes_prices.buyer_id',$buyerquoteid[$i]->buyer_id)
				->where('courier_buyer_quote_sellers_quotes_prices.seller_id',Auth::user()->id)
				->select('courier_buyer_quote_sellers_quotes_prices.*')
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
					$buyerquoteid[$i]->initial_conversion_factor ='';
					$buyerquoteid[$i]->counter_conversion_factor ='';
					$buyerquoteid[$i]->final_conversion_factor ='';
					$buyerquoteid[$i]->initial_fuel_surcharge_rupees ='';
					$buyerquoteid[$i]->final_fuel_surcharge_rupees ='';
					$buyerquoteid[$i]->initial_cod_rupees ='';
					$buyerquoteid[$i]->final_cod_rupees ='';
					$buyerquoteid[$i]->initial_freight_collect_rupees ='';
					$buyerquoteid[$i]->final_freight_collect_rupees ='';
					$buyerquoteid[$i]->initial_transit_days ='';
					$buyerquoteid[$i]->final_transit_days ='';
					$buyerquoteid[$i]->seller_acceptence ='';
					$buyerquoteid[$i]->initial_arc_rupees ='';
					$buyerquoteid[$i]->final_arc_rupees ='';
					$buyerquoteid[$i]->initial_transit_units ='';
					$buyerquoteid[$i]->final_transit_units ='';
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
					$buyerquoteid[$i]->initial_conversion_factor =$buyersquotes[0]->initial_conversion_factor;
					$buyerquoteid[$i]->counter_conversion_factor =$buyersquotes[0]->counter_conversion_factor;
					$buyerquoteid[$i]->final_conversion_factor =$buyersquotes[0]->final_conversion_factor;
					$buyerquoteid[$i]->initial_fuel_surcharge_rupees =$buyersquotes[0]->initial_fuel_surcharge_rupees;
					$buyerquoteid[$i]->final_fuel_surcharge_rupees =$buyersquotes[0]->final_fuel_surcharge_rupees;
					$buyerquoteid[$i]->initial_cod_rupees =$buyersquotes[0]->initial_cod_rupees;
					$buyerquoteid[$i]->final_cod_rupees =$buyersquotes[0]->final_cod_rupees;
					$buyerquoteid[$i]->initial_freight_collect_rupees =$buyersquotes[0]->initial_freight_collect_rupees;
					$buyerquoteid[$i]->final_freight_collect_rupees =$buyersquotes[0]->final_freight_collect_rupees;
					$buyerquoteid[$i]->initial_transit_days =$buyersquotes[0]->initial_transit_days;
					$buyerquoteid[$i]->final_transit_days =$buyersquotes[0]->final_transit_days;
					$buyerquoteid[$i]->seller_acceptence =$buyersquotes[0]->seller_acceptence;
					$buyerquoteid[$i]->initial_arc_rupees =$buyersquotes[0]->initial_arc_rupees;
					$buyerquoteid[$i]->final_arc_rupees =$buyersquotes[0]->final_arc_rupees;
					$buyerquoteid[$i]->initial_transit_units =$buyersquotes[0]->initial_transit_units;
					$buyerquoteid[$i]->final_transit_units =$buyersquotes[0]->final_transit_units;
			
				}
				//commented by swathi 02-05-2016 count increasing from ajax
				/*$tableName = CommonComponent::getTableNameAsPerService(Session::get('service_id'));
				if(!empty($tableName)){//echo "here".$tableName;exit;
					CommonComponent::viewCountForBuyer(Auth::User()->id,$buyerquoteid[$i]->ptlquoteid,$tableName);
				}*/
                                //end comment
			}
			
			if(!isset($buyersquotes)){
				$buyersquotes[] = array();
			}
			
			$buyerpublicquotedetails = array();
			$buyerpublicquotedetails[]    = DB::table('courier_buyer_quotes')
			->leftjoin('courier_buyer_quote_items','courier_buyer_quote_items.buyer_quote_id','=','courier_buyer_quotes.id')
			->leftjoin('users','users.id','=','courier_buyer_quotes.created_by')
			->leftjoin('lkp_courier_types','lkp_courier_types.id','=','courier_buyer_quote_items.lkp_courier_type_id')
			->leftjoin('lkp_ptl_pincodes','lkp_ptl_pincodes.id','=','courier_buyer_quotes.from_location_id')
			->leftjoin('lkp_courier_delivery_types','lkp_courier_delivery_types.id','=','courier_buyer_quote_items.lkp_courier_delivery_type_id')
			->leftjoin('lkp_ict_weight_uom','lkp_ict_weight_uom.id','=','courier_buyer_quote_items.lkp_ict_weight_uom_id')
			->leftjoin('lkp_cities','lkp_cities.id','=','courier_buyer_quotes.from_location_id')
			->whereIn('courier_buyer_quotes.id',$matchedIds)
			->select('courier_buyer_quotes.id as ptlquoteid','courier_buyer_quote_items.id','courier_buyer_quote_items.created_by as buyer_id','users.username','courier_buyer_quotes.dispatch_date',
					'courier_buyer_quotes.delivery_date','courier_buyer_quote_items.lkp_quote_price_type_id',
					'courier_buyer_quotes.from_location_id','courier_buyer_quotes.to_location_id',
					'lkp_cities.city_name','courier_buyer_quotes.from_location_id',
					'courier_buyer_quote_items.lkp_courier_type_id','courier_buyer_quotes.lkp_post_status_id',
					'courier_buyer_quote_items.lkp_courier_delivery_type_id',
					'courier_buyer_quote_items.calculated_volume_weight','courier_buyer_quote_items.package_value',
					'courier_buyer_quote_items.units','courier_buyer_quote_items.lkp_ict_weight_uom_id',
					'courier_buyer_quote_items.number_packages','lkp_courier_types.courier_type','lkp_ptl_pincodes.postoffice_name',
					'lkp_ptl_pincodes.pincode','lkp_courier_delivery_types.courier_delivery_type','lkp_ict_weight_uom.weight_type'
			)
			->groupBy('courier_buyer_quote_items.id')
			->get();
			
		}else{
				
				$buyerquoteid =array();
				$buyerpublicquotedetails = array();
				$buyersquotes =array();
			}
			
			
			
			//Lead Count
			$matchedLeadsIds = array();
			if($seller_post_items[0]->is_private == 1){
				$matchedLeadsIds[] = '';
			}
			else{
				$buyer_quote_items_leads_data = SellerMatchingComponent::getSellerLeads(COURIER, $id);
				foreach($buyer_quote_items_leads_data as $buyer_quote_lead_item){
					$matchedLeadsIds[] = $buyer_quote_lead_item->buyer_quote_id;
				}
			}
			//echo "<pre>";print_R($matchedIds); echo "</pre>";
			if(!empty($matchedLeadsIds) && count($matchedLeadsIds) > 0){
			
				$buyerleadquoteid    = DB::table('courier_buyer_quotes')
				->leftjoin('courier_buyer_quote_items','courier_buyer_quote_items.buyer_quote_id','=','courier_buyer_quotes.id')
				->leftjoin('users','users.id','=','courier_buyer_quotes.created_by')
				->leftjoin('lkp_courier_types','lkp_courier_types.id','=','courier_buyer_quote_items.lkp_courier_type_id')
				->leftjoin('lkp_ptl_pincodes','lkp_ptl_pincodes.id','=','courier_buyer_quotes.from_location_id')
				->leftjoin('lkp_courier_delivery_types','lkp_courier_delivery_types.id','=','courier_buyer_quote_items.lkp_courier_delivery_type_id')
				->leftjoin('lkp_ict_weight_uom','lkp_ict_weight_uom.id','=','courier_buyer_quote_items.lkp_ict_weight_uom_id')
				->leftjoin('lkp_cities','lkp_cities.id','=','courier_buyer_quotes.from_location_id')
				->whereIn('courier_buyer_quotes.id',$matchedLeadsIds)
				->select('courier_buyer_quotes.transaction_id as transaction_no','courier_buyer_quotes.id as ptlquoteid','courier_buyer_quote_items.id','courier_buyer_quote_items.created_by as buyer_id','users.username','courier_buyer_quotes.dispatch_date',
						'courier_buyer_quotes.delivery_date','courier_buyer_quote_items.lkp_quote_price_type_id',
						'courier_buyer_quotes.from_location_id','courier_buyer_quotes.to_location_id',
						'lkp_cities.city_name','courier_buyer_quotes.from_location_id',
						'courier_buyer_quote_items.lkp_courier_type_id','courier_buyer_quotes.lkp_post_status_id',
						'courier_buyer_quote_items.lkp_courier_delivery_type_id',
						'courier_buyer_quote_items.calculated_volume_weight',
						'courier_buyer_quote_items.units','courier_buyer_quote_items.lkp_ict_weight_uom_id',
						'courier_buyer_quote_items.number_packages','lkp_courier_types.courier_type','lkp_ptl_pincodes.postoffice_name',
						'lkp_ptl_pincodes.pincode','lkp_courier_delivery_types.courier_delivery_type','lkp_ict_weight_uom.weight_type'
				)
				->groupBy('courier_buyer_quotes.id')
				->orderBy('courier_buyer_quotes.dispatch_date', 'asc')
				->get();
			
					
				for($i=0;$i<count($buyerleadquoteid);$i++){
					$buyersquotes	= DB::table('courier_buyer_quote_sellers_quotes_prices')
					->where('courier_buyer_quote_sellers_quotes_prices.buyer_quote_id',$buyerleadquoteid[$i]->ptlquoteid)
					->where('courier_buyer_quote_sellers_quotes_prices.buyer_id',$buyerleadquoteid[$i]->buyer_id)
					->where('courier_buyer_quote_sellers_quotes_prices.seller_id',Auth::user()->id)
					->select('courier_buyer_quote_sellers_quotes_prices.*')
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
						$buyerleadquoteid[$i]->initial_conversion_factor ='';
						$buyerleadquoteid[$i]->counter_conversion_factor ='';
						$buyerleadquoteid[$i]->initial_fuel_surcharge_rupees ='';
						$buyerleadquoteid[$i]->final_fuel_surcharge_rupees ='';
						$buyerleadquoteid[$i]->initial_cod_rupees ='';
						$buyerleadquoteid[$i]->final_cod_rupees ='';
						$buyerleadquoteid[$i]->initial_freight_collect_rupees ='';
						$buyerleadquoteid[$i]->final_freight_collect_rupees ='';
						$buyerleadquoteid[$i]->initial_arc_rupees ='';
						$buyerleadquoteid[$i]->final_arc_rupees ='';
						$buyerleadquoteid[$i]->initial_transit_days ='';
						$buyerleadquoteid[$i]->initial_transit_units ='';
						$buyerleadquoteid[$i]->final_transit_days ='';
						$buyerleadquoteid[$i]->final_transit_units ='';
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
						$buyerleadquoteid[$i]->initial_conversion_factor =$buyersquotes[0]->initial_conversion_factor;
						$buyerleadquoteid[$i]->counter_conversion_factor =$buyersquotes[0]->counter_conversion_factor;
						$buyerleadquoteid[$i]->final_conversion_factor =$buyersquotes[0]->final_conversion_factor;
						$buyerleadquoteid[$i]->initial_fuel_surcharge_rupees =$buyersquotes[0]->initial_fuel_surcharge_rupees;
						$buyerleadquoteid[$i]->final_fuel_surcharge_rupees =$buyersquotes[0]->final_fuel_surcharge_rupees;
						$buyerleadquoteid[$i]->initial_cod_rupees =$buyersquotes[0]->initial_cod_rupees;
						$buyerleadquoteid[$i]->final_cod_rupees =$buyersquotes[0]->final_cod_rupees;
						$buyerleadquoteid[$i]->initial_freight_collect_rupees =$buyersquotes[0]->initial_freight_collect_rupees;
						$buyerleadquoteid[$i]->final_freight_collect_rupees =$buyersquotes[0]->final_freight_collect_rupees;
						$buyerleadquoteid[$i]->initial_arc_rupees =$buyersquotes[0]->initial_arc_rupees;
						$buyerleadquoteid[$i]->final_arc_rupees =$buyersquotes[0]->final_arc_rupees;
						$buyerleadquoteid[$i]->initial_transit_days =$buyersquotes[0]->initial_transit_days;
						$buyerleadquoteid[$i]->initial_transit_units =$buyersquotes[0]->initial_transit_units;
						$buyerleadquoteid[$i]->final_transit_days =$buyersquotes[0]->final_transit_days;
						$buyerleadquoteid[$i]->final_transit_units =$buyersquotes[0]->final_transit_units;
						$buyerleadquoteid[$i]->seller_acceptence =$buyersquotes[0]->seller_acceptence;
							
					}
					//commented by swathi 02-05-2016 count increasing from ajax
					/*$tableName = CommonComponent::getTableNameAsPerService(Session::get('service_id'));
					if(!empty($tableName)){
						CommonComponent::viewCountForBuyer(Auth::User()->id,$buyerleadquoteid[$i]->ptlquoteid,$tableName);
					}*/
                                        //end comment
				}
					
				if(!isset($buyersquotes)){
					$buyersquotes[] = array();
				}
					
				$buyerleadquotedetails = array();
				$buyerleadquotedetails[]    = DB::table('courier_buyer_quotes')
				->leftjoin('courier_buyer_quote_items','courier_buyer_quote_items.buyer_quote_id','=','courier_buyer_quotes.id')
				->leftjoin('users','users.id','=','courier_buyer_quotes.created_by')
				->leftjoin('lkp_courier_types','lkp_courier_types.id','=','courier_buyer_quote_items.lkp_courier_type_id')
				->leftjoin('lkp_ptl_pincodes','lkp_ptl_pincodes.id','=','courier_buyer_quotes.from_location_id')
				->leftjoin('lkp_courier_delivery_types','lkp_courier_delivery_types.id','=','courier_buyer_quote_items.lkp_courier_delivery_type_id')
				->leftjoin('lkp_ict_weight_uom','lkp_ict_weight_uom.id','=','courier_buyer_quote_items.lkp_ict_weight_uom_id')
				->leftjoin('lkp_cities','lkp_cities.id','=','courier_buyer_quotes.from_location_id')
				->whereIn('courier_buyer_quotes.id',$matchedLeadsIds)
				->select('courier_buyer_quotes.id as ptlquoteid','courier_buyer_quote_items.id','courier_buyer_quote_items.created_by as buyer_id','users.username','courier_buyer_quotes.dispatch_date',
						'courier_buyer_quotes.delivery_date','courier_buyer_quote_items.lkp_quote_price_type_id',
						'courier_buyer_quotes.from_location_id','courier_buyer_quotes.to_location_id',
						'lkp_cities.city_name','courier_buyer_quotes.from_location_id',
						'courier_buyer_quote_items.lkp_courier_type_id','courier_buyer_quotes.lkp_post_status_id',
						'courier_buyer_quote_items.lkp_courier_delivery_type_id',
						'courier_buyer_quote_items.calculated_volume_weight','courier_buyer_quote_items.package_value',
						'courier_buyer_quote_items.units','courier_buyer_quote_items.lkp_ict_weight_uom_id',
						'courier_buyer_quote_items.number_packages','lkp_courier_types.courier_type','lkp_ptl_pincodes.postoffice_name',
						'lkp_ptl_pincodes.pincode','lkp_courier_delivery_types.courier_delivery_type','lkp_ict_weight_uom.weight_type'
				)
				->groupBy('courier_buyer_quote_items.id')
				->orderBy('courier_buyer_quote_items.id', 'desc')
				->get();
					
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
		
		//Enquiries Count
		if($seller_post[0]->lkp_post_status_id ==1){
			$total_count =0;
		}else{
			if($seller_post_items[0]->is_private == 1){
				$total_count = count(CommonComponent::getPrivateBuyerMatchedResults(COURIER,$seller_post_items[0]->seller_post_id));
			}
			else{
			if(isset($seller_post_items[0]->from_location_id) && isset($seller_post_items[0]->to_location_id) && isset($seller_post[0]->lkp_ptl_post_type_id))
				//$total_count = AirDomesticSellerListingComponent::enquiryCount(2,$seller_post_items[0]->from_location_id,$seller_post_items[0]->to_location_id,$seller_post[0]->lkp_ptl_post_type_id );
				$total_count = count(SellerMatchingComponent::getMatchedResults(COURIER, $seller_post_items[0]->id));
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
					$lead_count = count(SellerMatchingComponent::getSellerLeads(COURIER, $seller_post_items[0]->id));
				}else
					$lead_count =0;
			}
		}
		
		$post_details='<div class="col-md-12 col-sm-12 col-xs-12 padding-none details_block">
							<h5><b>Spot Transaction</b></h5>
							<h5>
								<div class="col-md-4 col-sm-5 col-xs-6 padding-none"><b>Air Domestic</b></div>
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
									<p>Kg per CCM</p>
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
									if(empty($seller_post[0]->conversion_factor))
										$post_details .= '0';
									else 
										$post_details .= $seller_post[0]->conversion_factor;
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
				$gridtopnav = '<a href="#" class="'.$color1.'" data-showdiv="ftl-seller-messages"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$countMessages.'</span></a>
							<a href="#" class="'.$color2.'" data-showdiv="ftl-seller-enquiry"><i class="fa fa-file-text-o"></i> Enquiries<span class="badge">'.$total_count.'</span></a>
							<a href="#" class="'.$color3.'" data-showdiv="ftl-seller-leads"><i class="fa fa-thumbs-o-up"></i> Leads<span class="badge">'.$lead_count.'</span></a>
							<a href="#" data-showdiv="ftl-seller-marketanalytics"><i class="fa fa-line-chart"></i> Market Analytics</a>
							<a href="#"  class="'.$color4.'" data-showdiv="ftl-seller-documentation"><i class="fa fa-file-text-o"></i> Documentation <span class="badge">'.count($docs_seller_courier).'</span></a>';
				if($seller_post[0]->lkp_access_id == 2 || $seller_post[0]->lkp_access_id == 3)
					$privatebuyers  = DB::table('courier_seller_sellected_buyers')
					->leftjoin ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_sellected_buyers.seller_post_id' )
					->leftjoin('users','users.id','=','courier_seller_sellected_buyers.buyer_id')
					->where('courier_seller_sellected_buyers.created_by',Auth::user()->id)
					->where('courier_seller_sellected_buyers.seller_post_id',$sellerpostid[0]->id)
					->select('users.username')
					->get();
				else
					$privatebuyers =array();
				
				$seller_post_slab_values  = DB::table('courier_seller_posts')
				->join ( 'courier_seller_post_item_slabs', 'courier_seller_post_item_slabs.seller_post_id', '=', 'courier_seller_posts.id' )
				->where('courier_seller_posts.id',$sellerpostid[0]->id)
				->select('courier_seller_post_item_slabs.*')
				->get();
				
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
			'seller_post_slab_values'=>$seller_post_slab_values,	
			'kgpercft'=>$seller_post[0]->conversion_factor,
			'gridtopnav'=>$gridtopnav);
	}


	/**
	 * FTL Seller Post Details List Page.
	 *
	 * @param
	 *        	$request
	 * @return Response
	 */

	public static function listCourierBuyerMarketLeads($statusId, $roleId, $serviceId, $id){
		try{

			//Filters values to populate in the page

			$lkppoststatus  = DB::table('courier_seller_posts')
				->where('courier_seller_posts.id','=',$id)
				->select('courier_seller_posts.lkp_ptl_post_type_id','courier_seller_posts.lkp_courier_delivery_type_id')
				->get();


			$from_locationspincode = array ("" => "From Location-Pincode");
			if($lkppoststatus[0]->lkp_courier_delivery_type_id == 1){
				$to_locationspincode = array ("" => "To Location-Pincode");
			}else{
				$to_locationspincode = array ("" => "To Country");
			}

			$from_locationszone = array ("" => "From Zone");
			if(Session::get('destinationtype') == 1){
				$to_locationszone = array ("" => "To Zone");
			}else{
				$to_locationszone = array ("" => "To Country");
			}

			Session::put('lkppoststatus', $lkppoststatus[0]->lkp_ptl_post_type_id);
			$Query = DB::table ( 'courier_seller_posts as sp' );
			$Query->leftjoin ( 'courier_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
			$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'sp.lkp_post_status_id' );
			if($lkppoststatus[0]->lkp_ptl_post_type_id ==1 ){
				$Query->leftjoin('ptl_zones','ptl_zones.id','=','spi.from_location_id');
			}
			else{
				$Query->leftjoin('lkp_ptl_pincodes','lkp_ptl_pincodes.id','=','spi.from_location_id');
			}
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
					'sp.conversion_factor', 'spi.transitdays' ,'sp.lkp_post_status_id','ptl_zones.zone_name as postoffice_name',
					'sp.lkp_access_id', 'ps.post_status','sp.id as post_id','spi.from_location_id','spi.to_location_id',
					'sp.lkp_ptl_post_type_id','spi.is_cancelled','sp.lkp_post_status_id','spi.is_cancelled','sp.lkp_courier_delivery_type_id','sp.created_by','sp.transaction_id');

				Session::put('postedType', 'Zone');

				$seller_post_items  = DB::table('courier_seller_post_items')
					->join ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
					->where('courier_seller_post_items.seller_post_id',$id)
					->where ( 'courier_seller_posts.lkp_ptl_post_type_id', 1 )
					->select('*')
					->get();
				foreach($seller_post_items as $seller_post_item){
					if (!isset( $from_locationszone [$seller_post_item->from_location_id] )) {
						$from_locationszone [$seller_post_item->from_location_id] = DB::table ( 'ptl_zones' )
							->where ( 'id', $seller_post_item->from_location_id )
							->pluck ( 'zone_name' );
					}
					if (!isset( $to_locationszone [$seller_post_item->to_location_id] )) {
						if($seller_post_item->lkp_courier_delivery_type_id==2){
							$to_locationszone [$seller_post_item->to_location_id] = DB::table ( 'lkp_countries' )
								->where ( 'id', $seller_post_item->to_location_id )
								->pluck ( 'country_name' );
						}else{
							$to_locationszone [$seller_post_item->to_location_id] = DB::table ( 'ptl_zones' )
								->where ( 'id', $seller_post_item->to_location_id )
								->pluck ( 'zone_name' );
						}
					}
				}

			}
			else{
				$sellerresults = $Query->select ( 'spi.id', 'spi.from_location_id','spi.to_location_id','spi.price',
					'sp.conversion_factor', 'spi.transitdays' ,'sp.lkp_post_status_id','lkp_ptl_pincodes.postoffice_name',
					'sp.lkp_access_id', 'ps.post_status','sp.id as post_id','spi.from_location_id','spi.to_location_id',
					'sp.lkp_ptl_post_type_id','spi.is_cancelled','sp.lkp_post_status_id','spi.is_cancelled','sp.lkp_courier_delivery_type_id','sp.created_by','sp.transaction_id');
				Session::put('postedType', 'Pincode');


				$seller_post_items  = DB::table('courier_seller_post_items')
					->join ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
					->where('courier_seller_post_items.seller_post_id',$id)
					->where ( 'courier_seller_posts.lkp_ptl_post_type_id', 2 )
					->select('*')
					->get();
				foreach($seller_post_items as $seller_post_item){
					if (!isset( $from_locationspincode [$seller_post_item->from_location_id] )) {
						$from_locationspincode [$seller_post_item->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )
							->where ( 'id', $seller_post_item->from_location_id )
							->pluck ( 'pincode' );
					}
					if (!isset( $to_locationspincode [$seller_post_item->to_location_id] )) {
						if($seller_post_item->lkp_courier_delivery_type_id==2){
							$to_locationspincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_countries' )
								->where ( 'id', $seller_post_item->to_location_id )
								->pluck ( 'country_name' );
						}else{
							$to_locationspincode [$seller_post_item->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )
								->where ( 'id', $seller_post_item->to_location_id )
								->pluck ( 'pincode' );
						}
					}
				}

			}
			$Query->groupBy('spi.id');
			$Query->get ();
			//Functionality to handle filters based on the selection starts
			$grid = DataGrid::source ( $Query );
			$grid->add ( 'id', 'ID', true )->style ( "display:none" );
			$grid->add ( 'from_location_id', 'From', 'from_location_id' )->attributes(array("class" => "col-md-4 col-sm-2 col-xs-5 padding-none"));
			$grid->add ( 'to_location_id', 'To', 'to_location_id' )->attributes(array("class" => "col-md-4 col-sm-2 col-xs-2 padding-none hidden-xs"));
			$grid->add ( 'price', 'Rate per kg', 'price' )->style ( "display:none" );
			$grid->add ( 'transitdays', 'Transit Days', 'transitdays' )->attributes(array("class" => "col-md-2 col-sm-2 col-xs-2 padding-none"));
			$grid->add ( 'dummycolumn', 'Average Market Rate / Transit Time', '' )->attributes(array("class" => "col-md-3 hidden-xs hidden-sm hidden-md hidden-lg padding-left-none"));
			$grid->add ( 'is_cancelled', 'Status', 'lkp_post_status_id' )->attributes(array("class" => "col-md-1 col-sm-2 col-xs-2 padding-none"));
			$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
			$grid->add ( 'lkp_courier_delivery_type_id', 'Courier Type', true )->style ( "display:none" );
			$grid->add ( 'transaction_id', 'Transaction Id', 'transaction_id' )->style ( "display:none" );
			$grid->add ( 'created_by', 'Created by', 'created_by' )->style ( "display:none" );
			$grid->orderBy ( 'id', 'desc' );
			$grid->paginate ( 5 );
			$grid->row ( function ($row) {
				$row->cells [0]->style ( 'display:none' );
				$row->cells [3]->style ( 'display:none' );
				$row->cells [5]->style ( 'display:none' );
				$row->cells [8]->style ( 'display:none' );
				$spId = $row->cells [0]->value;
				$row->cells[0]->value = '';
				if(Session::get('lkppoststatus') == 1 ){
					$row->cells[1]->value = '<span></span>'.CommonComponent::getZoneName($row->cells [1]->value);
				}else{
					$row->cells[1]->value = '<span></span>'.CommonComponent::getZonePin($row->cells [1]->value);
				}
				
				if(Session::get('lkppoststatus') == 1 ){
					if($row->cells [8]->value == 1)
						$row->cells [2]->value = ''.CommonComponent::getZoneName($row->cells [2]->value).'';
					else
						$row->cells [2]->value = ''.CommonComponent::getCountry($row->cells [2]->value).'';
				}
				else{
					if($row->cells [8]->value == 1)
						$row->cells [2]->value = ''.CommonComponent::getZonePin($row->cells [2]->value).'';
					else
						$row->cells [2]->value = ''.CommonComponent::getCountry($row->cells [2]->value).'';
				}

				$poststatus = $row->cells [6]->value;

				$seller_post_items  = DB::table('courier_seller_post_items')
					->join ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
					->where('courier_seller_post_items.id',$spId)
					->select('courier_seller_post_items.*','courier_seller_posts.lkp_ptl_post_type_id','courier_seller_posts.id as spid')
					->get();

				if($row->cells [6]->value == 1 )
					$row->cells [6]->value = "Deleted";
				else
					$row->cells [6]->value = "Open";

				if($seller_post_items[0]->units == 'Weeks')
					$row->cells [4]->value = $row->cells [4]->value." Weeks";
				else
					$row->cells [4]->value = $row->cells [4]->value." Days";

				$poststatus = $row->cells [6]->value;$transdays=$row->cells [4]->value;			
				$row->cells [1]->attributes(array("class" => "col-md-4 padding-left-none "));
				$row->cells [2]->attributes(array("class" => "col-md-4 padding-left-none "));				
				$row->cells [4]->attributes(array("class" => "col-md-2 padding-left-none "));				
				$row->cells [6]->attributes(array("class" => "col-md-1 padding-none "));
				
				$transaction_id=$row->cells [9]->value;
				$seller_user_id=$row->cells [10]->value;
				
				$row->cells [9]->style ( 'display:none' );
				$row->cells [10]->style ( 'display:none' );

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
			$filter->add ( 'spi.to_location_id', 'To Location', 'select')->options($toselect)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
						
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
	
	
	public static function listCOURIERBuyerPrivatePosts($statusId, $roleId, $serviceId,$destinationSelected=1) {
	
		// Filters values to populate in the page
	      //echo "hhh";exit;
        // Filters values to populate in the page
        $ptlFromLocationPincode = array ("" => "From Location-Pincode");
        if($destinationSelected == 1){
        $ptlToLocationPincode = array ("" => "To Location-Pincode");
        }else{
        $ptlToLocationPincode = array ("" => "To Country");
        }
        $ptlCourierTypes = array ("" => "Courier Type");
        $dispatchDate = '';
        $deliveryDate = '';
        $ptlLocationWise = array ("" => "Select");
        $service_id = Session::get ( 'service_id' );

        // query to retrieve buyer posts list and bind it to the grid
      
        $Query = DB::table ( 'courier_buyer_quotes as ptlbq' );
        $Query->leftjoin ( 'courier_buyer_quote_items as ptlbqi', 'ptlbqi.buyer_quote_id', '=', 'ptlbq.id' );
        $Query->leftjoin ( 'lkp_courier_types as lct', 'lct.id', '=', 'ptlbqi.lkp_courier_type_id' );
		$Query->leftjoin ( 'lkp_courier_delivery_types as pt', 'pt.id', '=', 'ptlbqi.lkp_courier_delivery_type_id');
        $Query->leftjoin ( 'lkp_ptl_pincodes as ptlPins', 'ptlPins.id', '=', 'ptlbq.from_location_id' );
        //$Query->leftjoin ( 'lkp_ptl_pincodes as ptlPinsTo', 'ptlPinsTo.id', '=', 'ptlbq.to_location_id' );
        if($destinationSelected==2){
        	$Query->leftjoin('lkp_countries as ptlPinsTo1', function($join)
        	{
        		$join->on('ptlbq.to_location_id', '=', 'ptlPinsTo1.id');
        		$join->on(DB::raw('ptlbqi.lkp_courier_delivery_type_id'),'=',DB::raw(2));
        	
        	});
        
        }else{
        	$Query->leftjoin('lkp_ptl_pincodes as ptlPinsTo', function($join)
        	{
        		$join->on('ptlbq.to_location_id', '=', 'ptlPinsTo.id');
        		$join->on(DB::raw('ptlbqi.lkp_courier_delivery_type_id'),'=',DB::raw(1));
        	
        	});
        }
        
        $Query->leftjoin ( 'courier_buyer_quote_selected_sellers as bqss', 'bqss.buyer_quote_id', '=', 'ptlbq.id' );
        $Query->leftjoin ( 'users as us', 'us.id', '=', 'ptlbq.buyer_id' );
        
        		
        
        $Query->where ( 'bqss.seller_id', Auth::User ()->id );
        $Query->where('ptlbq.lkp_post_status_id','=',2);
        $Query->where('ptlbq.lkp_quote_access_id','=',2);
        $Query->where('ptlbqi.lkp_courier_delivery_type_id','=',$destinationSelected);
        $Query->groupBy('ptlbqi.buyer_quote_id');
        $Query->orderBy('ptlbqi.buyer_quote_id', 'DESC');
        
        
        //$Query->sum('ptlbqi.number_packages as totalcnt');
  
        // conditions to make search
        if (isset ( $service_id ) && !empty($service_id)) {
            $Query->where ( 'ptlbq.lkp_service_id', '=', $service_id );
        }
//         if (isset ( $delivery_type ) && !empty($delivery_type)) {
//             $Query->where ( 'ptlbqi.lkp_courier_delivery_type_id', '=', Session::get('delivery_type') );
//         }
//         if (isset ( $post_status ) && !empty($post_status)) {
//             $Query->where ( 'ptlbqi.lkp_post_status_id', '=', $post_status );
//         }
        if (isset ( $_GET ['dispatch_date'] ) && $_GET ['dispatch_date'] != '') {
            $commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['dispatch_date']);
            $Query->where ( 'ptlbq.dispatch_date', '>=', $commonDispatchDate );
            $dispatchDate = $commonDispatchDate;
        }
        if (isset ( $_GET ['delivery_date'] ) && $_GET ['delivery_date'] != '') {
            $commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['delivery_date']);
            $Query->where ( 'ptlbq.dispatch_date', '<=', $commonDeliveryhDate );
            $deliveryDate = $commonDeliveryhDate;
        }
  
        //$postResults = $Query->selectRaw('sum(ptlbqi.number_packages) as totalnoofpackes');
        if($destinationSelected==2){
        $postResults = $Query->select ('ptlbqi.id','ptlbqi.buyer_quote_id','ptlbqi.number_packages','ptlbq.dispatch_date','ptlbq.delivery_date','ptlbq.from_location_id','ptlbq.to_location_id','ptlbq.lkp_post_status_id', 'lct.courier_type','ptlbq.lkp_quote_access_id','ptlPins.postoffice_name as fromLocation',
        		'ptlPinsTo1.country_name  as toLocation','ptlbq.is_cancelled',DB::raw('sum(ptlbqi.number_packages) AS totalpackges'),'us.username','pt.id as courier_delivery_type','ptlbqi.created_by','ptlbqi.lkp_courier_type_id' )->get ();
        }else{
        	$postResults = $Query->select ('ptlbqi.id','ptlbqi.buyer_quote_id','ptlbqi.number_packages','ptlbq.dispatch_date','ptlbq.delivery_date','ptlbq.from_location_id','ptlbq.to_location_id','ptlbq.lkp_post_status_id', 'lct.courier_type','ptlbq.lkp_quote_access_id','ptlPins.postoffice_name as fromLocation',
        			'ptlPinsTo.pincode  as toLocation','ptlbq.is_cancelled',DB::raw('sum(ptlbqi.number_packages) AS totalpackges'),'us.username','pt.id as courier_delivery_type','ptlbqi.created_by','ptlbqi.lkp_courier_type_id' )->get ();
        }     
        //echo "<pre>"; print_r($postResults);die();
        $result = $Query->get ();
        
        // Functionality to handle filters based on the selection starts
		
		//Functionality to handle filters based on the selection starts
		foreach($postResults as $seller){
// 			$seller_post_items  = DB::table('courier_seller_post_items')
// 			->join ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
// 			->where('courier_seller_post_items.seller_post_id',$seller->id)
// 			->where ( 'courier_seller_posts.lkp_ptl_post_type_id', 2 )
// 			->select('*')
// 			->get();
			//foreach($seller_post_items as $seller_post_item){
				if (!isset( $ptlFromLocationPincode [$seller->from_location_id] )) {
					$ptlFromLocationPincode [$seller->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $seller->from_location_id )->pluck ( 'pincode' );
				}
				if (!isset( $ptlToLocationPincode [$seller->to_location_id] )) {
					
					if($seller->courier_delivery_type == 2){
						//echo $seller_post_item->to_location_id;exit;
						$ptlToLocationPincode [$seller->to_location_id] = DB::table ( 'lkp_countries' )->where ( 'id', $seller->to_location_id )->pluck ( 'country_name' );
					}else{
						$ptlToLocationPincode [$seller->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $seller->to_location_id )->pluck ( 'pincode' );
					}
				}
				if(Session::get ( 'service_id' )  == COURIER){
				if (!isset( $ptlCourierTypes [$seller->lkp_courier_type_id] )) {
					$ptlCourierTypes [$seller->lkp_courier_type_id] = DB::table ( 'lkp_courier_types' )->where ( 'id', $seller->lkp_courier_type_id )->pluck ( 'courier_type' );
				}
				}
			//}
		}
		
		foreach($postResults as $seller){
// 			$seller_post_items  = DB::table('courier_seller_post_items')
// 			->join ( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
// 			->where('courier_seller_post_items.seller_post_id',$seller->id)
// 			->where ( 'courier_seller_posts.lkp_ptl_post_type_id', 1 )
// 			->select('*')
// 			->get();
			//foreach($seller_post_items as $seller_post_item){
				if (!isset( $ptlFromLocationZone [$seller->from_location_id] )) {
					$ptlFromLocationZone [$seller->from_location_id] = DB::table ( 'ptl_zones' )
					->where ( 'id', $seller->from_location_id )
					->pluck ( 'zone_name' );
				}
				if (!isset( $ptlToLocationZone [$seller->to_location_id] )) {
					if($seller->courier_delivery_type == 2){
						//echo $seller_post_item->to_location_id;exit;
						$ptlToLocationZone [$seller->to_location_id] = DB::table ( 'lkp_countries' )->where ( 'id', $seller->to_location_id )->pluck ( 'country_name' );
					}else{
					$ptlToLocationZone [$seller->to_location_id] = DB::table ( 'ptl_zones' )
					->where ( 'id', $seller->to_location_id )
					->pluck ( 'zone_name' );
					}
				}
			//}
			if(Session::get ( 'service_id' )  == COURIER){
					if (!isset( $ptlCourierTypes [$seller->lkp_courier_type_id] )) {
						$ptlCourierTypes [$seller->lkp_courier_type_id] = DB::table ( 'lkp_courier_types' )->where ( 'id', $seller->lkp_courier_type_id )->pluck ( 'courier_type' );
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
       // echo "<pre>";
        //print_r($ptlLocationWise);exit;
		// echo $Query_buyers_for_sellers->tosql()."<br/>";
		
		$grid = DataGrid::source ( $Query );
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Name', 'username' )->attributes ( array (
				"class" => "col-md-2 padding-none" 
		) );
		$grid->add ( 'dispatch_date', 'Dispatch Date', 'dispatch_date' )->attributes ( array (
				"class" => "col-md-3  padding-none" 
		) );
		$grid->add ( 'from_location_id', 'From Location', 'from_location_id' )->attributes ( array (
				"class" => "col-md-2 padding-left-none" 
		) );
		$grid->add ( 'to_location_id', 'To Location', 'to_location_id' )->attributes ( array (
				"class" => "col-md-2 padding-left-none" 
		) );
		$grid->add ( 'action', ' ' )->attributes ( array (
				"class" => "col-md-2 padding-left-none"
		) );
		$grid->add ( 'status', 'ID', true )->style ( "display:none" );
		$grid->add ( 'created_by', 'ID', true )->style ( "display:none" );
		$grid->add ( 'courier_type', 'Courier Type', true )->style ( "display:none" );
		$grid->add ( 'courier_delivery_type', 'Destination Type', true )->style ( "display:none" );
		$grid->add ( 'calculated_volume_weight', 'Volume', true )->style ( "display:none" );
		$grid->add ( 'units', 'Units', true )->style ( "display:none" );
		$grid->add ( 'number_packages', 'Packages', true )->style ( "display:none" );
		$grid->add ( 'delivery_date', 'Delivery Date', 'delivery_date' )->style ( "display:none" );
        $grid->add ( 'transaction_id', 'transaction_id', 'transaction_id' )->style ( "display:none" );
        $grid->add ( 'buyer_quote_id', 'buyer_quote_id', 'buyer_quote_id' )->style ( "display:none" );
		$grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );
		$grid->row ( function ($row) {
			$row->cells [0]->style ( 'display:none' );
			$row->cells [2]->style ( 'display:none' );
			$row->cells [3]->style ( 'display:none' );
			$row->cells [4]->style ( 'display:none' );
			$row->cells [5]->style ( 'display:none' );
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
			$buyer_quote_id = $row->cells [0]->value;
			$transaction_id=$row->cells [14]->value;
			$bqid = $row->cells [15]->value;
			$buyer_name = $row->cells [1]->value;
			$from_zipcode = $row->cells [3]->value;
			$to_zipcode = $row->cells [4]->value;
			$dispatch_date_buyer = $row->cells [2]->value;
			$delivery_date_buyer = $row->cells [13]->value;
			
			$buyer_id = $row->cells [7]->value;
			
			$buyerdetailsvalue = DB::table ( 'courier_buyer_quote_items' )->where ( 'courier_buyer_quote_items.id', '=', $buyer_quote_id )->select ( 'courier_buyer_quote_items.*' )->get ();
			
			$buyer_post_status = $row->cells [5]->value;
			
			if ($buyer_post_status == 1) {
				$buyer_post_status = 'Saved as Draft';
			}
			if ($buyer_post_status == 2) {
				$buyer_post_status = 'Open';
			}
			if ($buyer_post_status == 3) {
				$buyer_post_status = 'Closed';
			}
			if ($buyer_post_status == 4) {
				$buyer_post_status = 'Booked';
			}
			if ($buyer_post_status == 5) {
				$buyer_post_status = 'Cancelled';
			}
			
			$row->cells [1]->attributes ( array (
					"class" => "col-md-2 padding-left-none" 
			) );
			$row->cells [2]->attributes ( array (
					"class" => "col-md-3 padding-left-none" 
			) );
			$row->cells [3]->attributes ( array (
					"class" => "col-md-2 padding-left-none" 
			) );
			$row->cells [4]->attributes ( array (
					"class" => "col-md-2 padding-left-none" 
			) );
			$row->cells [5]->attributes ( array (
					"class" => "col-md-3 padding-none" 
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
			
			$row->cells [3]->value = CommonComponent::getPinName($from_zipcode);
			if($row->cells [9]->value == 1){

				$row->cells [4]->value = CommonComponent::getPinName($to_zipcode);
			}
			else 
				$row->cells [4]->value = CommonComponent::getCountry($to_zipcode);
		
			$getSellerpost  = DB::table('courier_seller_post_items')
				->join( 'courier_seller_posts', 'courier_seller_posts.id', '=', 'courier_seller_post_items.seller_post_id' )
				->join( 'courier_buyer_quote_sellers_quotes_prices', 'courier_buyer_quote_sellers_quotes_prices.seller_post_item_id', '=', 'courier_seller_post_items.id' )
				->where('courier_seller_post_items.from_location_id','=',$from_zipcode)
				->where('courier_seller_post_items.to_location_id','=',$to_zipcode)
				->where('courier_seller_post_items.created_by','=',Auth::user()->id) 
				->where('courier_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$bqid)	
				->where('courier_seller_posts.lkp_post_status_id','=',OPEN)
				->select('courier_seller_post_items.seller_post_id',
						 'courier_seller_post_items.id',
						 'courier_seller_posts.tracking',
						 'courier_seller_posts.lkp_payment_mode_id',
						 'courier_seller_posts.accept_payment_netbanking',
						 'courier_seller_posts.accept_payment_credit',
						 'courier_seller_posts.accept_payment_debit',
						 'courier_seller_posts.credit_period',
						 'courier_seller_posts.credit_period_units',
						 'courier_seller_posts.accept_credit_netbanking',
						 'courier_seller_posts.accept_credit_cheque')
				->get();
			
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
				
			
			$locationids = DB::table('courier_buyer_quotes')
			->leftjoin('courier_buyer_quote_items','courier_buyer_quote_items.buyer_quote_id','=','courier_buyer_quotes.id')
			->where('courier_buyer_quote_items.id','=',$buyer_quote_id)
			->select('courier_buyer_quotes.from_location_id','courier_buyer_quotes.to_location_id')
			->get();
			
			
			$row->cells [5]->value .= '';
			
						$quoteid = DB::table('courier_buyer_quote_items')
						->where('courier_buyer_quote_items.id','=',$buyer_quote_id)
						->select('courier_buyer_quote_items.buyer_quote_id')
						->get();
							
						$quoteitems = DB::table('courier_buyer_quote_items')
						->join('lkp_courier_types','lkp_courier_types.id','=','courier_buyer_quote_items.lkp_courier_type_id')
						->join('lkp_courier_delivery_types','lkp_courier_delivery_types.id','=','courier_buyer_quote_items.lkp_courier_delivery_type_id')
						->where('courier_buyer_quote_items.buyer_quote_id','=',$quoteid[0]->buyer_quote_id)
						->select('courier_buyer_quote_items.*','lkp_courier_types.courier_type','lkp_courier_delivery_types.courier_delivery_type')
						->get();
						$getInitialQuotePrice = CommonComponent::getPTLQuotePriceForSearch($buyer_id,$quoteid[0]->buyer_quote_id,Auth::user()->id,'initial_quote_price','courier_buyer_quote_sellers_quotes_prices');
						$getCounterQuotePrice = CommonComponent::getPTLQuotePriceForSearch($buyer_id,$quoteid[0]->buyer_quote_id,Auth::user()->id,'counter_quote_price','courier_buyer_quote_sellers_quotes_prices');
						$getFinalQuotePrice   = CommonComponent::getPTLQuotePriceForSearch($buyer_id,$quoteid[0]->buyer_quote_id,Auth::user()->id,'final_quote_price','courier_buyer_quote_sellers_quotes_prices');
                                                //commented by swathi 02-05-2016 count increasing from ajax
                                                /*$tableName = CommonComponent::getTableNameAsPerService(Session::get('service_id'));
                                                if(!empty($tableName)){
                                                    CommonComponent::viewCountForBuyer(Auth::User()->id,$quoteid[0]->buyer_quote_id,$tableName);
                                                }*/
                                                //end comment
							if(!isset($getInitialQuotePrice[0]->initial_rate_per_kg) || $getInitialQuotePrice[0]->initial_rate_per_kg==''){
								$row->cells [5]->value .= '
								<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
								<button class="btn red-btn pull-right submit-data underline_link seller_submit_quote" data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Submit Quote </button>
								</div>';
							}
							if(isset($getInitialQuotePrice[0]->initial_quote_price) && $getInitialQuotePrice[0]->initial_quote_price!='0.0000'
								&&	isset($getCounterQuotePrice[0]->counter_rate_per_kg) && 
								$getCounterQuotePrice[0]->counter_rate_per_kg ==''){
								$row->cells [5]->value .= '
								<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
								<button class="btn red-btn pull-right submit-data   underline_link seller_submit_quote" data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Initial Quote Submitted </button>
								</div>';
							}
							if(isset($getCounterQuotePrice[0]->counter_rate_per_kg) && 
									$getCounterQuotePrice[0]->counter_rate_per_kg !=''
									&& isset($getFinalQuotePrice[0]->final_conversion_factor) && $getFinalQuotePrice[0]->final_conversion_factor==''){
								$row->cells [5]->value .= '
								<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
								<button  class="btn red-btn pull-right submit-data  ltlsellesearchdetails_1  underline_link " data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Submit Final Quotes </button>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
								<button class="btn red-btn pull-right submit-data  ltlsellesearchdetails_2 underline_link " data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Accept Counter Offer </button>
								</div>';
							}
							if(isset($getCounterQuotePrice[0]->counter_rate_per_kg) &&
									$getCounterQuotePrice[0]->counter_rate_per_kg !=''
									&& isset($getFinalQuotePrice[0]->final_conversion_factor) && $getFinalQuotePrice[0]->final_conversion_factor!=''){
								$row->cells [5]->value .= '<div class="col-md-12 col-sm-12 col-xs-4 padding-none text-right detailsslide1">
									<button  class="btn red-btn pull-right submit-data ltlsellesearchdetails_1 underline_link " data-buyernbuyerquoteid="'.$buyer_id.'_'.$buyer_quote_id.'" id="click-link" >Final Quote Submitted </button>
								</div>';
								
							}
													
							$row->cells [5]->value .= '<div class="pull-right text-right">
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
							<form id ="couriersearchpostquoteoffer" name ="couriersearchpostquoteoffer" class="formquoteid_'.$buyer_quote_id.'">';
							if(Session::get('session_delivery_date_ptl')=='')
								Session::put('session_delivery_date_ptl',$delivery_date_buyer);
							$row->cells [5]->value .='<input type="hidden" name="seller_post_item_id" id="seller_post_item_id" value="'.$seller_post_id_private.'">
							<input type="hidden" name="volumetric_'.$buyer_id.'_'.$buyer_quote_id.'" id="volumetric_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$buyerdetailsvalue[0]->calculated_volume_weight.'">
							<input type="hidden" name="packagenos_'.$buyer_id.'_'.$buyer_quote_id.'" id="packagenos_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$buyerdetailsvalue[0]->number_packages.'">	
							<input type="hidden" name="courier_type" id="courier_type" value="'.Session::get('session_courier').'">
							<input type="hidden" name="courier_delivery_type" id="courier_delivery_type" value="'.Session::get('session_courier_delivery_type').'">
							<input type="hidden" name="units_'.$buyer_id.'_'.$buyer_quote_id.'" id="units_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$buyerdetailsvalue[0]->units.'">
							<input type="hidden" name="buyerquoteid_'.$buyer_id.'_'.$buyer_quote_id.'" id="buyerquoteid_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$quoteid[0]->buyer_quote_id.'">';
							if(isset($locationids[0]->from_location_id) && isset($locationids[0]->to_location_id)){
							
								$row->cells [5]->value .='
									<input type="hidden" name="from_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" id="from_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$locationids[0]->from_location_id.'">
									<input type="hidden" name="to_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" id="to_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$locationids[0]->to_location_id.'">';
								
							}
							
							$row->cells [5]->value .='<div class="col-md-12 show-data-div padding-none padding-top quote_details_1_'.$buyer_id.'_'.$buyer_quote_id.' margin-top" style="display:none">
							<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
								<div class="table pull-right">
									<h2 class="sub-head"><span class="from-head">'.$row->cells [3]->value.' to  '.$row->cells [4]->value.'</span></h2>
									<div class="table-heading inner-block-bg">
										<div class="col-md-3 padding-left-none">Unit Weight (Gms)</div>
										<div class="col-md-3 padding-left-none">Package Value (Rs)</div>
										<div class="col-md-3 padding-left-none">Volume (CCM)</div>
										<div class="col-md-3 padding-left-none">No of Packages</div>
									</div>';
									for($i=0;$i<count($quoteitems);$i++){
									$row->cells [5]->value .= '
									<div class="table-data">
										<div class="inner-block-bg">
											<div class="col-md-3 padding-right-none">'.$quoteitems[$i]->units.'</div>
											<div class="col-md-3 padding-left-none">'.$quoteitems[$i]->package_value.'</div>
											<div class="col-md-3 padding-left-none">'.round($quoteitems[$i]->calculated_volume_weight,4).' CCM </div>';
											if($quoteitems[$i]->lkp_ict_weight_uom_id ==2)
											$quoteitems[$i]->units = $quoteitems[$i]->units*0.001;
											$row->cells [5]->value .= '
										
											
											<div class="col-md-3 padding-left-none">'.$quoteitems[$i]->number_packages.'</div>
										</div>	
										<input type="hidden" name="volumetric_'.$i.'" id="volumetric_'.$i.'" value="'.$quoteitems[$i]->calculated_volume_weight.'">
										<input type="hidden" name="units_'.$i.'" id="units_'.$i.'" value="'.$quoteitems[$i]->units.'">
										<input type="hidden" name="weighttype_'.$i.'" id="weighttype_'.$i.'" value="'.$quoteitems[$i]->lkp_ict_weight_uom_id.'">
										<input type="hidden" name="packagenos_'.$i.'" id="packagenos_'.$i.'" value="'.$quoteitems[$i]->number_packages.'">	
										<input type="hidden" name="packagevalue_'.$i.'" id="packagevalue_'.$i.'" value="'.$quoteitems[$i]->package_value.'">
										<input type="hidden" name="from_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" id="from_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$from_zipcode.'">
										<input type="hidden" name="to_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" id="to_city_loc_'.$buyer_id.'_'.$buyer_quote_id.'" value="'.$to_zipcode.'">
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
								<div class="col-md-3 padding-left-none form-control-fld padding-top">
									<span class="data-head">Rate per Kg </span> <span class="data-value"> Rs '.$getInitialQuotePrice[0]->initial_rate_per_kg.' /-</span>
										<input type="hidden" 
									name="initial_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="initial_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
									placeholder="Rate per Kg *" class="form-control form-control1 numberVal " value="'.$getInitialQuotePrice[0]->initial_rate_per_kg.'" >
										'; 
									else
										$row->cells [5]->value .= '
								<div class="col-md-3 padding-left-none form-control-fld padding-top">
								<input type="text" name="initial_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="initial_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
									placeholder="Rate per Kg *" class="ptl_initial_rate_per_kg numberVal fourdigitstwodecimals_deciVal form-control  form-control1" >';
									
									
									
								$row->cells [5]->value .= '</div>';
								if(isset($getInitialQuotePrice[0]->initial_conversion_factor) && $getInitialQuotePrice[0]->initial_conversion_factor!='')
									$row->cells [5]->value .='
								<div class="col-md-3 padding-left-none form-control-fld padding-top">
									<span class="data-head"> Conversion Factor </span><span class="data-value"> '.$getInitialQuotePrice[0]->initial_conversion_factor.' KG</span> 
										
										</div>';
								else{
									$row->cells [5]->value .= '
								<div class="col-md-3 padding-left-none form-control-fld padding-top">
									<input type="text"
									name="initial_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'"  
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="initial_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    placeholder="Conversion Factor *" class="ptl_initial_conversion numberVal twodigitstwodecimals_deciVal form-control  form-control1" >';
									$row->cells [5]->value .='</div>
									<div class="col-md-3 padding-left-none form-control-fld padding-top">
									<input type="hidden" id="calculatoropen" style="border:none;">
									</div>';
								}
								if(isset($getInitialQuotePrice[0]->initial_conversion_factor) && $getInitialQuotePrice[0]->initial_conversion_factor!=''){
									
								$row->cells [5]->value .= '<div class="clearfix"></div>
									<div class="col-md-12 col-sm-12 col-xs-12 padding-none">';
									$row->cells [5]->value .= '<div class="col-md-3 col-sm-2 col-xs-6 padding-none"><span class="data-head">Fuel Surcharges </span> <span class="data-value">Rs '.$getInitialQuotePrice[0]->initial_fuel_surcharge_rupees.' /-</span></div>';
									$row->cells [5]->value .= '<div class="col-md-2 col-sm-2 col-xs-6 padding-none"><span class="data-head">COD </span> <span class="data-value">Rs '.$getInitialQuotePrice[0]->initial_cod_rupees.' /-</span></div>';
									$row->cells [5]->value .= '<div class="col-md-2 col-sm-2 col-xs-6 padding-none"><span class="data-head">Freight Collect </span> <span class="data-value">Rs '.$getInitialQuotePrice[0]->initial_freight_collect_rupees.' /-</span></div>';
									$row->cells [5]->value .= '<div class="col-md-2 col-sm-2 col-xs-6 padding-none"><span class="data-head">ARC </span> <span class="data-value">'.$getInitialQuotePrice[0]->initial_arc_rupees.'</span></div>';
									$row->cells [5]->value .= '<div class="col-md-2 col-sm-2 col-xs-6 padding-none"><span class="data-head">Transit Days </span> <span class="data-value">'.$getInitialQuotePrice[0]->initial_transit_days.' '.$getInitialQuotePrice[0]->initial_transit_units.'</span></div>';
									$row->cells [5]->value .= '
										<div class="col-md-3 form-control-fld padding-none margin-top-none">
											<span class="data-head">Freight Amount </span><span class="data-value" >Rs '.CommonComponent::moneyFormat($getInitialQuotePrice[0]->initial_freight_amount,true).' /-</span>
										</div>
										<div class="col-md-3 form-control-fld padding-none margin-top-none">
											<span class="data-head">Total Amount </span><span class="data-value" >Rs '.CommonComponent::moneyFormat($getInitialQuotePrice[0]->initial_quote_price,true).' /-</span>
										</div>
									</div>';
									$row->cells [5]->value .= '
									<input type="hidden"
									name="initial_fuel_surcharge_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
									data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
									id="initial_fuel_surcharge_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
									placeholder="Fuel Surcharge (%) *" class="form-control form-group" value="'.$getInitialQuotePrice[0]->initial_fuel_surcharge_rupees.'">
											<input type="hidden"
									name="initial_cod_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
									data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
									id="initial_cod_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
									placeholder="COD (%) *" class="form-control form-group" value="'.$getInitialQuotePrice[0]->initial_cod_rupees.'">
											<input type="hidden"
									name="initial_freight_collect_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
									data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
									id="initial_freight_collect_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
									placeholder="Freight Collect *" class="form-control form-group" value="'.$getInitialQuotePrice[0]->initial_freight_collect_rupees.'">
									<input type="hidden"
									name="initial_arc_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
									data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
									id="initial_arc_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
									placeholder="ARC (%) *" class="form-control form-group" value="'.$getInitialQuotePrice[0]->initial_freight_collect_rupees.'">
											<input type="hidden"
									name="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
									data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
									id="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
									placeholder="Transit Days *" class="form-control form-group" value="'.$getInitialQuotePrice[0]->initial_transit_days.'">';
								}
								
								
								if(isset($getCounterQuotePrice[0]->counter_rate_per_kg) && $getCounterQuotePrice[0]->counter_rate_per_kg !='')
										$row->cells [5]->value .='
											<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-top form-group">
											<b>Buyer Counter Offer</b>  
											</div>
											<div class="col-md-3 padding-left-none">
												<span class="data-head">Rate per Kg </span> <span class="data-value"> Rs '.$getCounterQuotePrice[0]->counter_rate_per_kg.' /- </span></div>
													<input type="hidden"
											name="counter_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="counter_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="Conversion Factor *" class="form-control form-group margin-top"  value="'.$getCounterQuotePrice[0]->counter_rate_per_kg.'">
													'; 
									
								if(isset($getCounterQuotePrice[0]->counter_conversion_factor) && $getCounterQuotePrice[0]->counter_conversion_factor!=''){
									$row->cells [5]->value .='
								<div class="col-md-3 padding-left-none form-control-fld"><span class="data-head">Conversion Factor</span> <span class="data-value"> '.$getCounterQuotePrice[0]->counter_conversion_factor.' KG </span></div>
									<input type="hidden"
											name="counter_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'"
											data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
											id="counter_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="Conversion Factor *" class="form-control form-group margin-top"  value="'.$getCounterQuotePrice[0]->counter_conversion_factor.'">
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
												<div class="col-md-3 padding-left-none form-control-fld margin-bottom-none"><span class="data-head">Rate per Kg </span> <span class="data-value"> Rs '.$getFinalQuotePrice[0]->final_rate_per_kg.' /-</span></div>'; 
									elseif(isset($getFinalQuotePrice[0]->final_rate_per_kg) && $getFinalQuotePrice[0]->final_rate_per_kg =='' && $getInitialQuotePrice[0]->initial_rate_per_kg!='' &&  $getCounterQuotePrice[0]->counter_rate_per_kg!='')
										$row->cells [5]->value .= '
											<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-top form-group">
							 					<b>Seller Final Quote</b>   
											</div>
											<div class="col-md-3 padding-left-none form-control-fld margin-bottom-none">
											<input type="text" 
											name="final_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'" 
		                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
		                                    id="final_quote_rateperkg_'.$buyer_id.'_'.$buyer_quote_id.'"
											placeholder="Rate per Kg *" class="ptl_final_rate_per_kg form-control fourdigitstwodecimals_deciVal form-control1 numberVal " ></div>';
									
								
								if(isset($getFinalQuotePrice[0]->final_conversion_factor) && $getFinalQuotePrice[0]->final_conversion_factor!='')
									$row->cells [5]->value .='
									<div class="col-md-3 padding-left-none form-control-fld margin-bottom-none"><span class="data-head">Conversion Factor </span> <span class="data-value"> '.$getFinalQuotePrice[0]->final_conversion_factor.' KG</span></div>';
								elseif(isset($getFinalQuotePrice[0]->final_conversion_factor) && $getFinalQuotePrice[0]->final_conversion_factor=='' && $getInitialQuotePrice[0]->initial_conversion_factor!=''  &&  $getCounterQuotePrice[0]->counter_rate_per_kg!='')
									$row->cells [5]->value .= '
												<div class="col-md-3 padding-left-none form-control-fld margin-bottom-none">
									<input type="text"
									name="final_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'"  
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="final_quote_kgperdft_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    placeholder="Conversion Factor *" class="ptl_final_conversion twodigitstwodecimals_deciVal form-control form-control1 numberVal " ></div>
                                    <div class="col-md-3 padding-left-none form-control-fld ">
									<input type="hidden" id="calculatoropen" style="border:none;">
									</div>';
								
								$row->cells [5]->value .='<div class="clearfix"></div>
								<div clas="col-md-12 padding-none">';
								if(isset($getInitialQuotePrice[0]->initial_quote_price) && $getInitialQuotePrice[0]->initial_quote_price=='0.0000')
									
									$row->cells [5]->value .= '
											
									<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none">
									<input type="text" 
                                    name="initial_fuel_surcharge_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"   
		                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"  
		                            id="initial_fuel_surcharge_rupees_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    placeholder="Fuel Surcharge (%) *" class="ptl_initial_fuel form-control twodigitstwodecimals_deciVal form-control1 numberVal "></div>';
								
								elseif(isset($getCounterQuotePrice[0]->counter_quote_price) && $getCounterQuotePrice[0]->counter_quote_price !='0.0000' && isset($getFinalQuotePrice[0]->final_quote_price) && $getFinalQuotePrice[0]->final_quote_price=='0.0000')
								$row->cells [5]->value .= '<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none">
									<input type="text"
                                    name="final_fuel_surcharge_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
		                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
		                            id="final_fuel_surcharge_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="Fuel Surcharge (%) *" class="ptl_final_fuel twodigitstwodecimals_deciVal form-control form-control1 numberVal "></div>';
								elseif(!isset($getInitialQuotePrice[0]->initial_quote_price))
								$row->cells [5]->value .= '<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none">
									<input type="text"
                                    name="initial_fuel_surcharge_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
		                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
		                            id="initial_fuel_surcharge_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="Fuel Surcharge (%) *" class="ptl_initial_fuel twodigitstwodecimals_deciVal form-control form-control1 numberVal "></div>';
								
								
								if(isset($getInitialQuotePrice[0]->initial_quote_price) && $getInitialQuotePrice[0]->initial_quote_price=='0.0000')
									$row->cells [5]->value .='<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none"><input type="text" 
									name="initial_cod_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
                                    id="initial_cod_rupees_'.$buyer_id.'_'.$buyer_quote_id.'" 
		                            placeholder="COD (%) *" class="ptl_initial_cod form-control twodigitstwodecimals_deciVal form-control1 numberVal "></div>';
								
								elseif(isset($getCounterQuotePrice[0]->counter_quote_price) && $getCounterQuotePrice[0]->counter_quote_price !='0.0000' && isset($getFinalQuotePrice[0]->final_quote_price) && $getFinalQuotePrice[0]->final_quote_price=='0.0000')
								$row->cells [5]->value .= '<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none"><input type="text"
                                    name="final_cod_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
		                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
		                            id="final_cod_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="COD (%) *" class="ptl_final_cod form-control twodigitstwodecimals_deciVal form-control1 numberVal "></div>';
								elseif(!isset($getInitialQuotePrice[0]->initial_quote_price))
								$row->cells [5]->value .='<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none"><input type="text"
									name="initial_cod_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
                                    id="initial_cod_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
		                            placeholder="COD (%) *" class="ptl_initial_cod form-control twodigitstwodecimals_deciVal form-control1 numberVal "></div>';
								
								
								if(isset($getInitialQuotePrice[0]->initial_quote_price) && $getInitialQuotePrice[0]->initial_quote_price=='0.0000')
									
									$row->cells [5]->value .= '<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none"><input type="text" 
									name="initial_freight_collect_rupees_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'" 
                                    id="initial_freight_collect_rupees_'.$buyer_id.'_'.$buyer_quote_id.'" 
                                    placeholder="Freight Collect *" class="ptl_initial_freight form-control form-control1 fivedigitstwodecimals_deciVal numberVal "></div>';
								
								elseif(isset($getCounterQuotePrice[0]->counter_quote_price) && $getCounterQuotePrice[0]->counter_quote_price !='0.0000' && isset($getFinalQuotePrice[0]->final_quote_price) && $getFinalQuotePrice[0]->final_quote_price=='0.0000')
								$row->cells [5]->value .= '<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none">
									<input type="text"
                                    name="final_freight_collect_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
		                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
		                            id="final_freight_collect_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="Freight Collect *" class="ptl_final_freight form-control fivedigitstwodecimals_deciVal form-control1 numberVal "></div>';
								elseif(!isset($getInitialQuotePrice[0]->initial_quote_price))
								$row->cells [5]->value .= '<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none"><input type="text"
									name="initial_freight_collect_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
                                    id="initial_freight_collect_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="Freight Collect *" class="ptl_initial_freight form-control fivedigitstwodecimals_deciVal form-control1 numberVal "></div>';
								
								
								if(isset($getInitialQuotePrice[0]->initial_quote_price) && $getInitialQuotePrice[0]->initial_quote_price=='0.0000')
									$row->cells [5]->value .='<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none"><input type="text" 
									name="initial_arc_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
                                    id="initial_arc_rupees_'.$buyer_id.'_'.$buyer_quote_id.'" 
		                            placeholder="ARC (%) *" class="ptl_initial_arc form-control twodigitstwodecimals_deciVal form-control1 numberVal "></div>';
								
								elseif(isset($getCounterQuotePrice[0]->counter_quote_price) && $getCounterQuotePrice[0]->counter_quote_price !='0.0000' && isset($getFinalQuotePrice[0]->final_quote_price) && $getFinalQuotePrice[0]->final_quote_price=='0.0000')
								$row->cells [5]->value .= '<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none">
									<input type="text"
                                    name="final_arc_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
		                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
		                            id="final_arc_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="ARC (%) *" class="ptl_final_arc form-control twodigitstwodecimals_deciVal form-control1 numberVal "></div>';
								elseif(!isset($getInitialQuotePrice[0]->initial_quote_price))
								$row->cells [5]->value .='<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none"><input type="text"
									name="initial_arc_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
                                    id="initial_arc_rupees_'.$buyer_id.'_'.$buyer_quote_id.'"
		                            placeholder="ARC (%) *" class="ptl_initial_arc form-control twodigitstwodecimals_deciVal form-control1 numberVal "></div>';
								
								if(isset($getInitialQuotePrice[0]->initial_transit_days) && $getInitialQuotePrice[0]->initial_transit_days=='')
									$row->cells [5]->value .= '<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none"><input type="text"
									name="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"  
                                    id="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="Transit Days *" maxlength = "3" class="form-control form-control1 numericvalidation "></div>';
								
								elseif(isset($getCounterQuotePrice[0]->counter_quote_price) && $getCounterQuotePrice[0]->counter_quote_price !='0.0000' &&  isset($getFinalQuotePrice[0]->final_quote_price) && $getFinalQuotePrice[0]->final_quote_price=='0.0000')
								$row->cells [5]->value .= '<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none">
									<input type="text"
                                    name="final_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
		                            data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
		                            id="final_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="Transit Days *" maxlength = "3" class="form-control form-control1 numericvalidation "></div>';
								elseif(!isset($getInitialQuotePrice[0]->initial_transit_days))
								$row->cells [5]->value .= '<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none"><input type="text"
									name="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    data-buyerid="'.$buyer_id.'" dat-buyerqouteit="'.$buyer_quote_id.'"
                                    id="initial_quote_transit_'.$buyer_id.'_'.$buyer_quote_id.'"
                                    placeholder="Transit Days *" maxlength = "3" class="form-control form-control1 numericvalidation "></div>';
								
								
								
								
								if(isset($getInitialQuotePrice[0]->initial_transit_days) && $getInitialQuotePrice[0]->initial_transit_days=='')
									$row->cells [5]->value .= '<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none">	
									<div class="normal-select">
										<select class="selectpicker"  id="dayspicker_'.$buyer_id.'_'.$buyer_quote_id.'" name="dayspicker_'.$buyer_id.'_'.$buyer_quote_id.'">
										<option value="1">Days</option>
										<option value="2">Weeks</option>
										</select>
									</div></div>';
								
								elseif(isset($getCounterQuotePrice[0]->counter_quote_price) && $getCounterQuotePrice[0]->counter_quote_price !='0.0000' &&  isset($getFinalQuotePrice[0]->final_quote_price) && $getFinalQuotePrice[0]->final_quote_price=='0.0000')
								$row->cells [5]->value .= '<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none">
										
									<div class="normal-select">
										<select class="selectpicker"  id="dayspicker_'.$buyer_id.'_'.$buyer_quote_id.'" name="dayspicker_'.$buyer_id.'_'.$buyer_quote_id.'">
										<option value="1">Days</option>
										<option value="2">Weeks</option>
										</select>
									</div></div>';
								elseif(!isset($getInitialQuotePrice[0]->initial_transit_days))
								$row->cells [5]->value .= '<div class="col-md-2 padding-left-none form-control-fld margin-bottom-none">	
									<div class="normal-select">
										<select class="selectpicker"  id="dayspicker_'.$buyer_id.'_'.$buyer_quote_id.'" name="dayspicker_'.$buyer_id.'_'.$buyer_quote_id.'">
										<option value="1">Days</option>
										<option value="2">Weeks</option>
										</select>
									</div></div>';
								
								
										
								
								$row->cells [5]->value .= '</div>';

								if(isset($getFinalQuotePrice[0]->final_freight_amount ) && $getFinalQuotePrice[0]->final_freight_amount!=''){
								$row->cells [5]->value .= '<div class="col-md-3 col-sm-3 col-xs-6 padding-none"><span class="data-head">Fuel Surcharges </span> <span class="data-value">Rs '.$getFinalQuotePrice[0]->final_fuel_surcharge_rupees.' /-</span></div>';
								$row->cells [5]->value .= '<div class="col-md-2 col-sm-3 col-xs-6 padding-none"><span class="data-head">COD </span> <span class="data-value">Rs '.$getFinalQuotePrice[0]->final_cod_rupees.' /-</span></div>';
								$row->cells [5]->value .= '<div class="col-md-2 col-sm-3 col-xs-6 padding-none"><span class="data-head">Freight Collect </span> <span class="data-value">Rs '.$getFinalQuotePrice[0]->final_freight_collect_rupees.' /-</span></div>';
								$row->cells [5]->value .= '<div class="col-md-2 col-sm-3 col-xs-6 padding-none"><span class="data-head">ARC </span> <span class="data-value">'.$getFinalQuotePrice[0]->final_arc_rupees.'</span></div>';
								$row->cells [5]->value .= '<div class="col-md-2 col-sm-3 col-xs-6 padding-none"><span class="data-head">Transit Days </span> <span class="data-value">'.$getFinalQuotePrice[0]->final_transit_days.' '.$getFinalQuotePrice[0]->final_transit_units.'</span></div>';
								}	
										
								$row->cells [5]->value .= '<div class="clearfix"></div>';
								if(!isset($getInitialQuotePrice[0]->initial_freight_amount)  || $getInitialQuotePrice[0]->initial_freight_amount=='')
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
										
										$row->cells [5]->value .= '</div><div class="clearfix"></div></div>
															
															
										
										<div class="col-md-12 data-fld padding-none text-right">
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
									$row->cells [5]->value .= '	
									
						</div>
					</form>';
			
			$data_link = url () . "/sellerposts/$buyer_quote_id";
			$row->attributes ( array (
					"class" => "col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none ",
					"data_link" => $data_link 
			) );
		} );
		
		// filter for buyear search list top dropdown lists---filters
		$filter = DataFilter::source ( $Query );
		
		//$filter->add ( 'ptlbq.lkp_ptl_post_type_id', '', 'select' )->options ( $ptlLocationWise )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "changeLocType(this.value)" );
		$filter->add ( 'ptlbq.from_location_id', '', 'select' )->options ( $ptlFromLocationPincode )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'ptlbq.to_location_id', '', 'select' )->options ( $ptlToLocationPincode)->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'ptlbqi.lkp_courier_type_id', 'Courier Type', 'select' )->options ( $ptlCourierTypes )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		//$filter->add ( 'psp.from_date', 'From', 'date' )->attr("class","filter_calendar");
		//$filter->add ( 'psp.to_date', 'To', 'date' )->attr("class","filter_calendar");
		$filter->submit('search');
		$filter->reset('reset');
		$filter->build();
		
		$result = array ();
		$result ['grid'] = $grid;
		$result ['filter'] = $filter;
		// echo "<pre>";print_R($result);die;
		return $result;
	
	}
}
