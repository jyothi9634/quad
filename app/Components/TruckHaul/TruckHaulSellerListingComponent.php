<?php
namespace App\Components\TruckHaul;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use App\Components\CommonComponent;
use App\Components\MessagesComponent;
use App\Components\BuyerComponent;
use App\Models\User;
use App\Models\FtlSearchTerm;
use App\Models\SellerPostItemView;
use App\Components\Matching\SellerMatchingComponent;

class TruckHaulSellerListingComponent {
	
	/**
	 * TH Seller Posts List Page - Grid and filters
	 * Retrieval of data related to seller posts list items to populate in the seller list widget
	 * Displays a grid with a list of all seller posts
	 */
	public static function listTruckHaulSellerPosts($statusId, $serviceId, $roleId,$type) {
		if(isset($_REQUEST['page'])){//echo $_REQUEST['page'];
		}
		//Filters values to populate in the page
		$from_locations = array(""=>"From Location");
		$to_locations = array(""=>"To Location");
		$vehicle_types = array(""=>"Vehicle Type");
		$load_types = array(""=>"Load Type");
	
		// query to retrieve seller posts list and bind it to the grid
		$Query = DB::table ( 'truckhaul_seller_posts as sp' );
		$Query->leftjoin ( 'truckhaul_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'sp.lkp_post_status_id' );
		
		if(isset($_REQUEST['lead_name']) && ($_REQUEST['lead_name'] ==2)){
			Session::put('leads', '2');
			$Query->where('sp.lkp_access_id',1);
		}
		else{
			Session::put('leads', '1');
			$Query->leftjoin ( 'truckhaul_buyer_quote_selected_sellers as bqss', 'bqss.seller_id', '=', 'spi.created_by' );
			
		}
		
		$Query->where('sp.seller_id',Auth::user()->id);

		//conditions to make search
		if(isset($statusId) && $statusId != '' && $statusId!=0){
			$Query->where('sp.lkp_post_status_id', $statusId);
		}
		if(isset($type) && $type != ''){
			if($type==1){
			$Query->where('sp.created_by', Auth::user()->id);
			}
		}

		if( isset($_REQUEST['search']) && $_REQUEST['vehicle_number']!=''){
			$vehicle_no = $_REQUEST['vehicle_number'];
			$Query->whereRaw('spi.vehicle_number like "%'.$vehicle_no.'%"');
		}

		if( isset($_REQUEST['search']) && $_REQUEST['from_date']!=''){
			$from=CommonComponent::convertDateForDatabase($_REQUEST['from_date']);
			$Query->whereRaw('sp.from_date >= "'.$from.'"');
		}
		
		if( isset($_REQUEST['search']) && $_REQUEST['to_date']!=''){
			$to=CommonComponent::convertDateForDatabase($_REQUEST['to_date']);
			if($_REQUEST['from_date']!=''){
				$Query->whereBetween('sp.to_date',array($from,$to));
			}else{
				$Query->where('sp.to_date', $to);
			}
		}
		
		$sellerresults = $Query->select ( 'sp.id', 'sp.from_date',
				'sp.to_date','sp.lkp_access_id','sp.lkp_post_status_id','ps.post_status','spi.vehicle_number'
		)
		->groupBy('sp.id')
		->get ();
		//Functionality to handle filters based on the selection starts
		foreach($sellerresults as $seller){
			$seller_post_items  = DB::table('truckhaul_seller_post_items')
			->where('truckhaul_seller_post_items.seller_post_id',$seller->id)
			->select('*')
			->get();
			foreach($seller_post_items as $seller_post_item){
				if(!isset($from_locations[$seller_post_item->from_location_id])){
					$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
				}
				if(!isset($to_locations[$seller_post_item->to_location_id])){
					$to_locations[$seller_post_item->to_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->to_location_id)->pluck('city_name');
				}
				if(!isset($load_types[$seller_post_item->lkp_load_type_id])){
					$load_types[$seller_post_item->lkp_load_type_id] = DB::table('lkp_load_types')->where('id', $seller_post_item->lkp_load_type_id)->pluck('load_type');
				}
				if(!isset($vehicle_types[$seller_post_item->lkp_vehicle_type_id])){
					$vehicle_types[$seller_post_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $seller_post_item->lkp_vehicle_type_id)->pluck('vehicle_type');
				}
			}
		}
		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations = CommonComponent::orderArray($to_locations);
		$load_types = CommonComponent::orderArray($load_types);
		$vehicle_types = CommonComponent::orderArray($vehicle_types);
		//Functionality to handle filters based on the selection ends
	
		$grid = DataGrid::source ( $Query );
	
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-3 padding-left-none"));	
		$grid->add ( 'lkp_access_id', 'Post For', 'lkp_access_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'vehicle_number', 'Vehicle Number', 'vehicle_number' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'post_status', 'Status', 'post_status' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
		$grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );
	
	
		$grid->row ( function ($row) {
			$row->cells [0]->style ( 'display:none' );

			$spId = $row->cells [0]->value;

			$val = $row->cells [5]->value;
			
			$poststaus = $row->cells [5]->value;
			if($row->cells [5]->value == 1 )
				$row->cells [5]->value = "<a href='../updateseller/$spId'>$val</a>";
			else
				$row->cells [5]->value = $val;			
			
               $row->cells [3]->value = CommonComponent::getQuoteAccessById($row->cells [3]->value);
			
			$seller_post_items  = DB::table('truckhaul_seller_post_items')
							->join('truckhaul_seller_posts','truckhaul_seller_posts.id','=','truckhaul_seller_post_items.seller_post_id')
							->where('truckhaul_seller_post_items.seller_post_id',$spId)
							->select('*','truckhaul_seller_post_items.id as spiid')
							->get();
			//echo "<pre>";
			//print_r($seller_post_items);exit;
			
			
			//count for seller documents
               $docs_seller_hual = array();
			$serviceId = Session::get('service_id');
               if(isset($seller_post_items[0]->from_location_id))
			$docs_seller_hual    =   CommonComponent::getGsaDocuments(SELLER,$serviceId,$spId,$seller_post_items[0]->from_location_id,$seller_post_items[0]->to_location_id);
			 
			
			
			
			if(isset($seller_post_items[0]->is_private)){
				$privatepost = $seller_post_items[0]->is_private;
			}else{
				$privatepost = 0;
			}
			if(!isset($seller_post_items[0]->lkp_payment_mode_id))
				$seller_lkp_payment_mode_id =1;
			else
			$seller_lkp_payment_mode_id = $seller_post_items[0]->lkp_payment_mode_id;
			$seller_payment_mode_method = CommonComponent::getSellerPostPaymentMethod($seller_lkp_payment_mode_id);
			if(!isset($seller_post_items[0]->tracking))
				$tracking_seller =1;
			else
			$tracking_seller = $seller_post_items[0]->tracking;               
               $tracking_seller_post = CommonComponent::getTrackingType($tracking_seller);			
               
			$getpostitemids = DB::table('truckhaul_seller_post_items')
			->where('truckhaul_seller_post_items.seller_post_id','=',$spId)
			->select('truckhaul_seller_post_items.id')
			->get();
			$allcountview =0;
			if(count($getpostitemids)>0){
				for($i=0;$i<count($getpostitemids);$i++){
				
					$countview = DB::table('truckhaul_seller_post_item_views')
					->where('truckhaul_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
					->select('truckhaul_seller_post_item_views.id','truckhaul_seller_post_item_views.view_counts')
					->get();
					if(isset($countview[0]->view_counts))
					$allcountview +=  $countview[0]->view_counts;
				
				}
			}
			//matching implmentation start
			$total_count = 0;
			
			if($privatepost==1){
 				$total_count = count(CommonComponent::getPrivateBuyerMatchedResults(ROAD_TRUCK_HAUL,$seller_post_items[0]->spiid));
			}else{
				foreach($getpostitemids as $seller_post_item){
					if (isset($seller_post_item->id)) {
						$potitemId = $seller_post_item->id;
						$total_count += count(SellerMatchingComponent::getMatchedResults(ROAD_TRUCK_HAUL, $potitemId));
					}
				}
			}
			$lead_count=0;
			if($privatepost==1){
				$lead_count =0;
			}else{
				if (isset($seller_post_item->id)) {
						$potitemId = $seller_post_item->id;
					$lead_count += count(SellerMatchingComponent::getSellerLeads(ROAD_TRUCK_HAUL, $potitemId));
				}else
					$lead_count =0;
			}
                        //$msg_count  =    MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$spId);
                        $msg_count=0;
                        foreach($getpostitemids as $seller_post_item){
                            if (isset($seller_post_item->id)) {
                                $potitemId = $seller_post_item->id;
                                $msgs  =    MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$potitemId);
                                $msg_count+=    count($msgs['result']);
                            }
                        }
			
            if($poststaus == 'Draft')
				$data_link = url()."/updateseller/$spId";
			else
				$data_link = url()."/sellerposts/$spId";
			$frmdate = $row->cells [1]->value;
			$frmdate = date('d/m/Y', strtotime($frmdate));
			//$row->cells [1]->value = '<span><input type="checkbox" name="sellerpostcheck" id="sellerpostcheck" class="checkBoxClass gridcheckbox" value='.$spId.'></span>'.$frmdate;
			$row->cells [1]->value = $frmdate;
			
			$todate = $row->cells [2]->value;
			$todate = date('d/m/Y', strtotime($todate));
			$row->cells [2]->value = $todate;
			$row->cells [1]->attributes(array("class" => "html_link col-md-3 padding-left-none","data_link"=>$data_link));
			$row->cells [2]->attributes(array("class" => "html_link col-md-3 padding-left-none","data_link"=>$data_link));
			$row->cells [3]->attributes(array("class" => "html_link col-md-2 padding-left-none","data_link"=>$data_link));
			$row->cells [4]->attributes(array("class" => "html_link col-md-2 padding-left-none","data_link"=>$data_link));
			$row->cells [5]->value = $val;
			$row->cells [5]->attributes(array("class" => "col-md-1 padding-none"));
			//onclick="javascript:sellerpostcancel(\'posts\','.$spId.')"
			$row->cells [6]->value = '
				<div class="col-md-1 padding-none text-right"><a href="javascript:void(0)" data-target="#cancelsellerpostmodal" data-toggle="modal" onclick="setcancelpostid(\'posts\','.$spId.')" >';
				if($poststaus !='Deleted') {
				$row->cells [6]->value .= '<i class="fa fa-trash" title="Delete"></i>';
				}
				$row->cells [6]->value .= '</a></div>';
				$row->cells [6]->value .= '<div class="clearfix"></div>
					<div class="pull-left">
						<div class="info-links">
								<a>
									<i class="fa fa-envelope-o"></i> Messages <span class="badge">'.$msg_count.'</span>
								</a>
								<a>
									<i class="fa fa-file-text-o"></i> Enquiries<span class="badge">';
									if($poststaus == 'Draft')
										$row->cells [6]->value .='0';
									else
										$row->cells [6]->value .=$total_count;
				$row->cells [6]->value .='</span>
								</a>
								<a>
									<i class="fa fa-bullseye"></i> Leads<span class="badge">';
									if($poststaus == 'Draft')
										$row->cells [6]->value .='0';
									else
										$row->cells [6]->value .=$lead_count;
									$row->cells [6]->value .='</span>
								</a>
								<a>
									<i class="fa fa-line-chart"></i> Market Analytics
								</a>
								<a>
									<i class="fa fa-file-text-o"></i> Documentation <span class="badge">'.count($docs_seller_hual).'</span>
								</a>
							</div>
					</div>
					<div class="pull-right text-right">
						<div class="info-links">';
									if ($tracking_seller != 0){
							$row->cells [6]->value .='<a href="'.$data_link.'"><i class="fa fa-signal"></i> '.$tracking_seller_post.'</a>';
									}
							$row->cells [6]->value .='<a href="'.$data_link.'"><i class="fa fa-rupee"></i> '.$seller_payment_mode_method.'</a>
							<a>
								<span class="views red"><i class="fa fa-eye" title="Views"></i>';
							if($row->cells [5]->value == 'Draft')
								$row->cells [6]->value .='0';
							else
								$row->cells [6]->value .=$allcountview;
				$row->cells [6]->value .='</span>
							</a>
						</div>
					</div>';				
		} );
			//Functionality to build filters in the page starts
			
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'spi.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.to_location_id', 'From Location', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.lkp_vehicle_type_id', 'Vehicle Type', 'select')->options($vehicle_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.lkp_load_type_id', 'Load Type', 'select')->options($load_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
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

	public static function listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id){
		try{

			//Filters values to populate in the page			
			$from_locations = array(""=>"From Location");
			$to_locations = array(""=>"To Location");
			$vehicle_types = array(""=>"Vehicle Type");
			$load_types = array(""=>"Load Type");
			$Query = DB::table ( 'truckhaul_seller_posts as sp' );
			$Query->leftjoin ( 'truckhaul_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
			$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'spi.lkp_post_status_id' );
			if(Session::get('leads') &&  Session::get('leads')==2){
				Session::put('leads', '2');
				$Query->where('sp.lkp_access_id',1);
			}
			else{
				Session::put('leads', '1');
				$Query->leftjoin ( 'truckhaul_buyer_quote_selected_sellers as bqss', 'bqss.seller_id', '=', 'spi.created_by' );
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

			if( isset($_REQUEST['search']) && $_REQUEST['vehicle_number']!=''){
				$vehicle_no = $_REQUEST['vehicle_number'];
				$Query->whereRaw('spi.vehicle_number >= "'.$vehicle_no.'"');
			}

			$sellerresults = $Query->select ( 'spi.id', 'sp.from_date','spi.price','sp.lkp_post_status_id','sp.id as spostid',
					'sp.to_date', 'sp.transaction_id' ,'spi.lkp_vehicle_type_id','spi.lkp_load_type_id','spi.price',
					'sp.lkp_access_id', 'ps.post_status','sp.id as post_id','spi.from_location_id','spi.to_location_id','spi.is_cancelled','spi.vehicle_number'
			)
			->groupBy('spi.id')
			->get ();

			//Functionality to handle filters based on the selection starts
			foreach($sellerresults as $seller){
				$seller_post_items  = DB::table('truckhaul_seller_post_items')
					->where('truckhaul_seller_post_items.id',$seller->id)
					->select('*')
					->get();
				foreach($seller_post_items as $seller_post_item){
					if(!isset($from_locations[$seller_post_item->from_location_id])){
						$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
					}
					if(!isset($to_locations[$seller_post_item->to_location_id])){
						$to_locations[$seller_post_item->to_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->to_location_id)->pluck('city_name');
					}
					
					if(!isset($load_types[$seller_post_item->lkp_load_type_id])){
						$load_types[$seller_post_item->lkp_load_type_id] = DB::table('lkp_load_types')->where('id', $seller_post_item->lkp_load_type_id)->pluck('load_type');
					}
					if(!isset($vehicle_types[$seller_post_item->lkp_vehicle_type_id])){
						$vehicle_types[$seller_post_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $seller_post_item->lkp_vehicle_type_id)->pluck('vehicle_type');
					}
					
				}
			}
			//Functionality to handle filters based on the selection ends

			$grid = DataGrid::source ( $Query );

			$grid->add ( 'id', 'ID', true )->style ( "display:none" );
			$grid->add ( 'from_location_id', 'From', 'from_location_id' )->attributes(array("class" => "col-md-3 padding-left-none"));
			$grid->add ( 'to_location_id', 'To', 'to_location_id' )->attributes(array("class" => "col-md-3 padding-left-none"));
			$grid->add ( 'lkp_vehicle_type_id', 'Vehicle Type', 'lkp_vehicle_type_id' )->style ( "display:none" );
			$grid->add ( 'lkp_load_type_id', 'Load Type', 'lkp_load_type_id' )->style ( "display:none" );
			$grid->add ( 'vehicle_number', 'Vehicle Number', 'vehicle_number' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'price', 'Price (<i class="fa fa-inr fa-1x"></i>)', 'price' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'post_status', 'Status', '' )->attributes(array("class" => "col-md-1 padding-left-none"));
			$grid->add ( 'is_cancelled', 'Post Status', true )->style ( "display:none" );
			$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
			$grid->add ( 'spostid', 'Seller Post ID', true )->style ( "display:none" );
			
			$grid->orderBy ( 'id', 'desc' );
			$grid->paginate ( 5 );


			$grid->row ( function ($row) {	
				
				$row->cells [0]->style ( 'display:none' );	
				$row->cells [3]->style ( 'display:none' );
				$row->cells [4]->style ( 'display:none' );
 				$row->cells [9]->style ( 'display:none' );
 				$row->cells [10]->style ( 'display:none' );
				$spId = $row->cells [0]->value;
				$row->cells [6]->value = CommonComponent::getPriceType($row->cells [6]->value);	$poststaus = $row->cells [8]->value;
				$spostid = $row->cells [10]->value;

				if($row->cells [8]->value == 1 )
					$row->cells [7]->value = "Deleted";
				else if($row->cells [7]->value == 'Booked')
					$row->cells [7]->value = "Booked";
                    else if($row->cells [7]->value == 'Closed')
					$row->cells [7]->value = "Closed";
				else
					$row->cells [7]->value = "Open";
				
				//View Count
				$countview = DB::table('truckhaul_seller_post_item_views')
				->where('truckhaul_seller_post_item_views.seller_post_item_id','=',$spId)
				->select('truckhaul_seller_post_item_views.id','truckhaul_seller_post_item_views.view_counts')
				->get();
				if(!isset($countview[0]->view_counts))
					$countview = 0;
				else
					$countview = $countview[0]->view_counts;
				
				$row->cells [1]->value = ''.CommonComponent::getCityName($row->cells [1]->value).'';
				$row->cells [2]->value = ''.CommonComponent::getCityName($row->cells [2]->value).'';
				$row->cells [3]->value = ''.CommonComponent::getVehicleType($row->cells [3]->value).'';
				$row->cells [4]->value = ''.CommonComponent::getLoadType($row->cells [4]->value).'';
				$seller_post_items  = DB::table('truckhaul_seller_post_items')
					->where('truckhaul_seller_post_items.id',$spId)
					->select('*')
					->get();
				
				//count for seller documents
				$serviceId = Session::get('service_id');
				$docs_seller_hual_items    =   CommonComponent::getGsaDocuments(SELLER,$serviceId,$spId,$row->cells [1]->value,$row->cells [2]->value);
				
				
				
				$data_link = url()."/sellerpostdetail/$spId";
				$row->cells [1]->attributes(array("class" => "col-md-3 padding-left-none html_link","data_link"=>$data_link));
				$row->cells [2]->attributes(array("class" => "col-md-3 padding-left-none html_link","data_link"=>$data_link));
				//$row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none html_link","data_link"=>$data_link));
				//$row->cells [4]->attributes(array("class" => "col-md-2 padding-left-none html_link","data_link"=>$data_link));
				$row->cells [5]->attributes(array("class" => "col-md-2 padding-none html_link","data_link"=>$data_link));
				$row->cells [6]->attributes(array("class" => "col-md-2 padding-none html_link","data_link"=>$data_link));
				$row->cells [7]->attributes(array("class" => "col-md-1 padding-left-none html_link","data_link"=>$data_link));
				//matching implmentation start
				$total_count = 0;
				if($seller_post_items[0]->is_private == 1){
					$total_count = count(CommonComponent::getPrivateBuyerMatchedResults(ROAD_TRUCK_HAUL,$spId));
				}else{
					if(isset($spId)){
						$total_count = count(SellerMatchingComponent::getMatchedResults(ROAD_TRUCK_HAUL,$spId));
					}
				}
				//matching implmentation end
				

				//Leads Count
				$lead_count = 0;
				if($seller_post_items[0]->is_private == 1){
					$lead_count = 0;
				}else{
					if(isset($spId)){
						$lead_count += count(SellerMatchingComponent::getSellerLeads(ROAD_TRUCK_HAUL, $spId));
					}else
						$lead_count =0;
				}				
				$msg_count  =    MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$spId);
				$row->cells [8]->value ='';
					
                                if($poststaus !=1) {
                                	$row->cells [8]->value .= '<div class="col-md-1 padding-none text-right"><a href="javascript:void(0)" data-target="#cancelsellerpostmodal" data-toggle="modal" onclick="setcancelpostid(\'item\','.$spId.')" >';
                                	$row->cells [8]->value .= '<i class="fa fa-trash" title="Delete"></i>';
                                	$row->cells [8]->value .= '</a></div>';
                                }
                                
				$row->cells [8]->value .='<div class="clearfix"></div>
						<div class="pull-left">
							<div class="info-links">
                                <a href="/sellerpostdetail/'.$spId.'?type=messages">
									<i class="fa fa-envelope-o"></i> Messages <span class="badge">'.count($msg_count['result']).'</span>
								</a>
								<a href="/sellerpostdetail/'.$spId.'?type=enquiries">
									<i class="fa fa-file-text-o"></i> Enquiries
									<span class="badge">';
									if($poststaus ==1)
										$row->cells [8]->value .='0';
									else
										$row->cells [8]->value .=$total_count;
                                $row->cells [8]->value .='</span>
								</a>
								<a href="/sellerpostdetail/'.$spId.'?type=leads"><i class="fa fa-bullseye"></i> Leads<span class="badge">'.$lead_count.'</span></a>
								<a href="#"><i class="fa fa-line-chart"></i> Market Analytics</a>
								<a href="/sellerpostdetail/'.$spId.'?type=documentation"><i class="fa fa-file-text-o"></i> Documentation <span class="badge">'.count($docs_seller_hual_items).'</span>
								</a>
							</div>
						</div>
						<div class="pull-right text-right">
							<div class="info-links">';
                                
                                
								$row->cells [9]->value .= '<a href="/sellerpostdetail/'.$spId.'">
									<span class="views red"><i class="fa fa-eye" title="Views"></i>';									
										$row->cells [9]->value .=$countview;
				$row->cells [9]->value .='
									</span>
								</a>
							</div>
						</div>';
			} );

	    //Functionality to build filters in the page starts
	    $filter = DataFilter::source ( $Query );
	    $filter->add ( 'spi.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
		$filter->add ( 'spi.to_location_id', 'From Location', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
		$filter->add ( 'spi.lkp_vehicle_type_id', 'Vehicle Type', 'select')->options($vehicle_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
		$filter->add ( 'spi.lkp_load_type_id', 'Load Type', 'select')->options($load_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");


	    $filter->submit('search');
	    $filter->reset('reset');
	    $filter->build();
		//Functionality to build filters in the page ends

	    $result = array();
	    $result['grid'] = $grid;
	    $result['filter'] = $filter;
	    return $result;

		} catch( Exception $e ) {           
            return $e->message;	   
	    }
	}
	
	
	/**
	 * FTL Seller Post Details List Page with Quotes.
	 *
	 * @param
	 *        	$request
	 * @return Response
	 */
	
	public static function listTruckHaulSellerPostDetailsItems($id){
		Session::put('seller_post_item', $id);
		try{
				
			$viewcount  = DB::table('truckhaul_seller_post_items')
					->where('truckhaul_seller_post_items.id','=',$id)
					->select('truckhaul_seller_post_items.id',
							 'truckhaul_seller_post_items.created_by')
					->get();
				
			
			$countview = DB::table('truckhaul_seller_post_item_views')
				->where('truckhaul_seller_post_item_views.seller_post_item_id','=',$id)
				->select('truckhaul_seller_post_item_views.id','truckhaul_seller_post_item_views.view_counts')
				->get();
				if(!isset($countview[0]->view_counts))
					$countview = 0;
				else
					$countview = $countview[0]->view_counts;
			$seller_post = DB::table('truckhaul_seller_posts')
						->join('truckhaul_seller_post_items','truckhaul_seller_post_items.seller_post_id','=','truckhaul_seller_posts.id')
						->where('truckhaul_seller_post_items.id',$id)
						->select('truckhaul_seller_posts.*','truckhaul_seller_post_items.id')
						->get();
		
			$seller_post_items  = DB::table('truckhaul_seller_post_items')
					->where('truckhaul_seller_post_items.id',$id)
					->select('*')
					->get();
				
			$getUserrole = DB::table('users')
			->where('users.id', Auth::user()->id)
			->select('users.primary_role_id','users.is_business')
			->first();
				
				
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
			//load type
			$loadtype = DB::table('lkp_load_types')
			->where('lkp_load_types.id',$seller_post_items[0]->lkp_load_type_id)
			->select('id','load_type')
			->get();
			if(isset($loadtype[0]->load_type) && $loadtype[0]->load_type!='')
				$loadtype = $loadtype[0]->load_type;
			else
				$loadtype ='';
			//Vehicle type
			$vehicletype   = DB::table('lkp_vehicle_types')
			->where('lkp_vehicle_types.id',$seller_post_items[0]->lkp_vehicle_type_id)
			->select('id','vehicle_type')
			->get();
			if(isset($vehicletype[0]->vehicle_type) && $vehicletype[0]->vehicle_type!='')
				$vehicletype = $vehicletype[0]->vehicle_type;
			else
				$vehicletype ='';
			//Payments type
				
			$paymenttype    = DB::table('lkp_payment_modes')
			->where('lkp_payment_modes.id',$seller_post[0]->lkp_payment_mode_id)
			->select('id','payment_mode')
			->get();
				
			if(isset($paymenttype[0]->payment_mode) && $paymenttype[0]->payment_mode!='')
				$paymenttype = $paymenttype[0]->payment_mode;
			else
				$paymenttype ='';

			//matching implmentation start
			$total_count = 0;
			$matchedIds = array();
			$buyersquotes = array();
			if($seller_post_items[0]->is_private == 1){
				$matchedIds[] = CommonComponent::getPrivateBuyerMatchedResults(ROAD_TRUCK_HAUL,$id);
			}else{
				if(isset($id)){
					$buyer_quote_items_matched_data = SellerMatchingComponent::getMatchedResults(ROAD_TRUCK_HAUL,$id);
					$total_count = count($buyer_quote_items_matched_data);
					foreach($buyer_quote_items_matched_data as $buyer_quote_item){
						$matchedIds[] = $buyer_quote_item->buyer_quote_id;
					}
				}	
			}
		
			$buyerpublicquotedetails   = DB::table('truckhaul_buyer_quotes')
				->join('truckhaul_buyer_quote_items','truckhaul_buyer_quote_items.buyer_quote_id','=','truckhaul_buyer_quotes.id')
				->join('users','users.id','=','truckhaul_buyer_quotes.created_by')
				->join('lkp_load_types','lkp_load_types.id','=','truckhaul_buyer_quote_items.lkp_load_type_id')
				->join('lkp_cities','lkp_cities.id','=','truckhaul_buyer_quote_items.from_city_id')
				->join('lkp_vehicle_types','lkp_vehicle_types.id','=','truckhaul_buyer_quote_items.lkp_vehicle_type_id')
				->leftjoin('truckhaul_buyer_quote_sellers_quotes_prices','truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=','truckhaul_buyer_quote_items.id')
				->whereIn('truckhaul_buyer_quote_items.id',$matchedIds)
				->select('truckhaul_buyer_quotes.transaction_id as transaction_no','truckhaul_buyer_quote_items.id','users.username','truckhaul_buyer_quote_items.dispatch_date',
					'lkp_load_types.load_type','truckhaul_buyer_quote_items.lkp_post_status_id',
					'lkp_vehicle_types.vehicle_type','truckhaul_buyer_quote_items.lkp_quote_price_type_id',
					'truckhaul_buyer_quote_items.from_city_id','truckhaul_buyer_quote_items.to_city_id',
					'truckhaul_buyer_quote_items.created_by','truckhaul_buyer_quote_sellers_quotes_prices.seller_id',
					'truckhaul_buyer_quote_sellers_quotes_prices.initial_quote_price','lkp_cities.city_name',
					'truckhaul_buyer_quote_sellers_quotes_prices.counter_quote_price','truckhaul_buyer_quotes.lkp_quote_access_id',
					'truckhaul_buyer_quote_sellers_quotes_prices.final_quote_price',
					'truckhaul_buyer_quote_sellers_quotes_prices.initial_transit_days',
					'truckhaul_buyer_quote_sellers_quotes_prices.counter_transit_days',
					'truckhaul_buyer_quote_sellers_quotes_prices.final_transit_days',
					'truckhaul_buyer_quotes.buyer_id','truckhaul_buyer_quote_sellers_quotes_prices.firm_price',
					'truckhaul_buyer_quote_sellers_quotes_prices.seller_acceptence','truckhaul_buyer_quote_items.price',
					'truckhaul_buyer_quote_sellers_quotes_prices.id as bqsqpid',
					'truckhaul_buyer_quote_items.quantity',
					'truckhaul_buyer_quote_items.units',
					'truckhaul_buyer_quote_items.number_loads')
				->groupBy('truckhaul_buyer_quote_items.id')
				->get();
//print_r($buyerpublicquotedetails);
//exit;


			for($i=0;$i<count($buyerpublicquotedetails);$i++){
				$buyersquotes[]	= DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id',$buyerpublicquotedetails[$i]->id)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id',$buyerpublicquotedetails[$i]->buyer_id)
					->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id',Auth::user()->id)
					->select('truckhaul_buyer_quote_sellers_quotes_prices.initial_quote_price',
						'truckhaul_buyer_quote_sellers_quotes_prices.counter_quote_price',
						'truckhaul_buyer_quote_sellers_quotes_prices.final_quote_price',
						'truckhaul_buyer_quote_sellers_quotes_prices.firm_price',
						'truckhaul_buyer_quote_sellers_quotes_prices.initial_transit_days',
						'truckhaul_buyer_quote_sellers_quotes_prices.counter_transit_days',
						'truckhaul_buyer_quote_sellers_quotes_prices.final_transit_days',
						'truckhaul_buyer_quote_sellers_quotes_prices.seller_acceptence')
					->get();
                                //commented by swathi 02-05-2016 count increasing from ajax
                                /*$tableName = CommonComponent::getTableNameAsPerService(Session::get('service_id'));
                                if(!empty($tableName)){//echo "here".$tableName;exit;
                                    CommonComponent::viewCountForBuyer(Auth::User()->id,$buyerpublicquotedetails[$i]->id,$tableName);
                                }*/
                                //end comment
			}

			//matching implmentation end
			
			
			
			//Leads implmentation start
			$lead_count = 0;
			$matchedLeadsIds = array();
			$buyersleads = array();
                        if($seller_post_items[0]->is_private == 1){
                            $matchedLeadsIds=array();
                        }else{
			if(isset($id)){
				$buyer_quote_items_leads_data = SellerMatchingComponent::getSellerLeads(ROAD_TRUCK_HAUL,$id);
				$lead_count = count($buyer_quote_items_leads_data);
				foreach($buyer_quote_items_leads_data as $buyer_quote_lead_item){
					$matchedLeadsIds[] = $buyer_quote_lead_item->buyer_quote_id;
				}
			}
                        }
			$buyerleadsquotedetails   = DB::table('truckhaul_buyer_quotes')
			->join('truckhaul_buyer_quote_items','truckhaul_buyer_quote_items.buyer_quote_id','=','truckhaul_buyer_quotes.id')
			->join('users','users.id','=','truckhaul_buyer_quotes.created_by')
			->join('lkp_load_types','lkp_load_types.id','=','truckhaul_buyer_quote_items.lkp_load_type_id')
			->join('lkp_cities','lkp_cities.id','=','truckhaul_buyer_quote_items.from_city_id')
			->join('lkp_vehicle_types','lkp_vehicle_types.id','=','truckhaul_buyer_quote_items.lkp_vehicle_type_id')
			->leftjoin('truckhaul_buyer_quote_sellers_quotes_prices','truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=','truckhaul_buyer_quote_items.id')
			->whereIn('truckhaul_buyer_quote_items.id',$matchedLeadsIds)
			->select('truckhaul_buyer_quotes.transaction_id as transaction_no','truckhaul_buyer_quote_items.id','users.username','truckhaul_buyer_quote_items.dispatch_date',
					'lkp_load_types.load_type','truckhaul_buyer_quote_items.lkp_post_status_id',
					'lkp_vehicle_types.vehicle_type','truckhaul_buyer_quote_items.lkp_quote_price_type_id',
					'truckhaul_buyer_quote_items.from_city_id','truckhaul_buyer_quote_items.to_city_id',
					'truckhaul_buyer_quote_items.created_by','truckhaul_buyer_quote_sellers_quotes_prices.seller_id',
					'truckhaul_buyer_quote_sellers_quotes_prices.initial_quote_price','lkp_cities.city_name',
					'truckhaul_buyer_quote_sellers_quotes_prices.counter_quote_price','truckhaul_buyer_quotes.lkp_quote_access_id',
					'truckhaul_buyer_quote_sellers_quotes_prices.final_quote_price',
					'truckhaul_buyer_quote_sellers_quotes_prices.initial_transit_days',
					'truckhaul_buyer_quote_sellers_quotes_prices.counter_transit_days',
					'truckhaul_buyer_quote_sellers_quotes_prices.final_transit_days',
					'truckhaul_buyer_quotes.buyer_id','truckhaul_buyer_quote_sellers_quotes_prices.firm_price',
					'truckhaul_buyer_quote_sellers_quotes_prices.seller_acceptence','truckhaul_buyer_quote_items.price',
					'truckhaul_buyer_quote_sellers_quotes_prices.id as bqsqpid',
					'truckhaul_buyer_quote_items.quantity',
					'truckhaul_buyer_quote_items.units',
					'truckhaul_buyer_quote_items.number_loads')
					->groupBy('truckhaul_buyer_quote_items.id')
					->orderBy('truckhaul_buyer_quote_items.dispatch_date', 'asc')
					->get();

			for($i=0;$i<count($buyerleadsquotedetails);$i++){
				$buyersleads[]	= DB::table('truckhaul_buyer_quote_sellers_quotes_prices')
				->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_quote_item_id',$buyerleadsquotedetails[$i]->id)
				->where('truckhaul_buyer_quote_sellers_quotes_prices.buyer_id',$buyerleadsquotedetails[$i]->buyer_id)
				->where('truckhaul_buyer_quote_sellers_quotes_prices.seller_id',Auth::user()->id)
				->select('truckhaul_buyer_quote_sellers_quotes_prices.initial_quote_price',
						'truckhaul_buyer_quote_sellers_quotes_prices.counter_quote_price',
						'truckhaul_buyer_quote_sellers_quotes_prices.final_quote_price',
						'truckhaul_buyer_quote_sellers_quotes_prices.firm_price',
						'truckhaul_buyer_quote_sellers_quotes_prices.initial_transit_days',
						'truckhaul_buyer_quote_sellers_quotes_prices.counter_transit_days',
						'truckhaul_buyer_quote_sellers_quotes_prices.final_transit_days',
						'truckhaul_buyer_quote_sellers_quotes_prices.seller_acceptence')
						->get();
                                //commented by swathi 02-05-2016 count increasing from ajax
                                /*$tableName = CommonComponent::getTableNameAsPerService(Session::get('service_id'));
                                if(!empty($tableName)){
                                    CommonComponent::viewCountForBuyer(Auth::User()->id,$buyerleadsquotedetails[$i]->id,$tableName);
                                }*/
			}
			//Leads implmentation end
			
			
		} catch( Exception $e ) {
			return $e->message;
		}
		$subs_st_date = date('Y-m-d', strtotime($subscription[0]->subscription_start_date));
		$subs_end_date = date('Y-m-d', strtotime($subscription[0]->subscription_end_date));
		
		Session::put('message', '');

		return array('id'=>$id,
					 'seller_post'=>$seller_post,
		 			 'seller_post_items'=>$seller_post_items,
					 'fromlocations'=>$fromlocations,
					 'tolocations'=>$tolocations,
					 'loadtype'=>$loadtype,
					 'vehicletype'=>$vehicletype,
					 'paymenttype'=>$paymenttype,
					 'buyerdetails'=>array(),
					 'buyerpublicquotedetails'=>$buyerpublicquotedetails,
					 'buyerleadsquotedetails'=>$buyerleadsquotedetails,
					 'subscriptionstdate'=>$subs_st_date,
				     'subscriptionenddate'=>$subs_end_date,
					 'buyersquotes'=>$buyersquotes,
					 'buyersleads'=>$buyersleads,
					 'lead_count'=>$lead_count,
					 'viewcount'=>$countview);
		
	}


	public static function getFTLSellerPostItemDetails($id){
		try{
			$seller_post = DB::table('seller_posts')
			->leftjoin('seller_post_items','seller_post_items.seller_post_id','=','seller_posts.id')
            ->leftjoin('users','users.id','=','seller_posts.seller_id')
            ->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'seller_post_items.lkp_load_type_id')
            ->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'seller_post_items.lkp_vehicle_type_id')
            ->leftjoin('lkp_post_statuses as lps', 'lps.id', '=', 'seller_post_items.lkp_post_status_id')
            ->leftjoin('buyer_quote_sellers_quotes_prices as bqsp', 'bqsp.seller_post_item_id', '=', 'seller_post_items.id')                    
            ->where('seller_post_items.id',$id)
            ->select('seller_posts.*','seller_post_items.*','users.username','ldt.load_type', 'lvt.vehicle_type', 'lps.post_status',
                    DB::raw("(case when `bqsp`.`final_transit_days` != 0 then bqsp.final_transit_days  when `bqsp`.`initial_transit_days` != 0 then bqsp.initial_transit_days when 'bqsp.id'=0 then seller_post_items.transitdays end) as transitdays") )
            ->get();
            if(!empty($seller_post)) {
                $fromLocation = BuyerComponent::getCityNameFromId($seller_post[0]->from_location_id);
                $toLocation = BuyerComponent::getCityNameFromId($seller_post[0]->to_location_id);
                $deliveryDate = CommonComponent::checkAndGetDate($seller_post[0]->from_date);
                $dispatchDate = CommonComponent::checkAndGetDate($seller_post[0]->to_date);
            } else {
                $fromLocation = '';
                $toLocation = '';
                $deliveryDate = '';
                $dispatchDate = '';
            }
            if(isset($getpostitemids[0]->id))
				$countview = DB::table('seller_post_item_views')
				->where('seller_post_item_views.seller_post_item_id','=',$getpostitemids[0]->id)
				->select('seller_post_item_views.id','seller_post_item_views.view_counts')
				->get();
			if(!isset($countview[0]->view_counts))
				$countview = 0;
			else
				$countview = $countview[0]->view_counts;
    		return array('id'=>$id,
					 'seller_post'=>$seller_post,
					 'countview'=>$countview,
					 'fromLocation'=>$fromLocation,
					 'toLocation'=>$toLocation,
					 'deliveryDate'=>$deliveryDate,
					 'dispatchDate'=>$dispatchDate,
		 			);
        } catch (Exception $e) {
            
        }
	}

	/**
	 * FTL Seller Post Details List Page.
	 *
	 * @param
	 *        	$request
	 * @return Response
	 */

	public static function listFTLBuyerMarketLeads($statusId, $roleId, $serviceId, $id){
		try{

			//Filters values to populate in the page
			$from_locations = array(""=>"From Location");
			$to_locations = array(""=>"To Location");
			$vehicle_types = array(""=>"Vehicle Type");
			$load_types = array(""=>"Load Type");
			$Query = DB::table ( 'seller_posts as sp' );
			$Query->leftjoin ( 'seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
			$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'sp.lkp_post_status_id' );
			if(Session::get('leads') &&  Session::get('leads')==2){
				Session::put('leads', '2');
				$Query->where('sp.lkp_access_id',1);
			}
			else{
				Session::put('leads', '1');
				$Query->leftjoin ( 'buyer_quote_selected_sellers as bqss', 'bqss.seller_id', '=', 'spi.created_by' );
			}
			//$Query->where('sp.seller_id',Auth::user()->id);
			$Query->where('spi.seller_post_id',$id);

			//conditions to make search
			if(isset($statusId) && $statusId != ''){
				$Query->where('sp.lkp_post_status_id', $statusId);
			}
			if(isset($serviceId) && $serviceId != ''){
				$Query->where('sp.lkp_service_id', $serviceId);
			}

			$sellerresults = $Query->select ( 'spi.id', 'sp.from_date','spi.price','sp.lkp_post_status_id','sp.id as spostid',
				'sp.to_date', 'sp.transaction_id' ,'spi.lkp_vehicle_type_id','spi.lkp_load_type_id','spi.price',
				'sp.lkp_access_id', 'ps.post_status','sp.id as post_id','spi.from_location_id','spi.to_location_id','spi.is_cancelled',
				'spi.transitdays','spi.units','sp.created_by'
			)
				->groupBy('spi.id')
				->get ();


			//Functionality to handle filters based on the selection starts
			foreach($sellerresults as $seller){
				$seller_post_items  = DB::table('seller_post_items')
					->where('seller_post_items.id',$seller->id)
					->select('*')
					->get();
				foreach($seller_post_items as $seller_post_item){
					if(!isset($from_locations[$seller_post_item->from_location_id])){
						$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
					}
					if(!isset($to_locations[$seller_post_item->to_location_id])){
						$to_locations[$seller_post_item->to_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->to_location_id)->pluck('city_name');
					}

					if(!isset($load_types[$seller_post_item->lkp_load_type_id])){
						$load_types[$seller_post_item->lkp_load_type_id] = DB::table('lkp_load_types')->where('id', $seller_post_item->lkp_load_type_id)->pluck('load_type');
					}
					if(!isset($vehicle_types[$seller_post_item->lkp_vehicle_type_id])){
						$vehicle_types[$seller_post_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $seller_post_item->lkp_vehicle_type_id)->pluck('vehicle_type');
					}

				}
			}
			//Functionality to handle filters based on the selection ends
			//echo $Query->tosql();
			//echo "<pre>";print_R($sellerresults);die;
			$grid = DataGrid::source ( $Query );

			$grid->add ( 'id', 'ID', true )->style ( "display:none" );
			$grid->add ( 'from_location_id', 'From', 'from_location_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'to_location_id', 'To', 'to_location_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'lkp_vehicle_type_id', 'Vehicle Type', 'lkp_vehicle_type_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'lkp_load_type_id', 'Load Type', 'lkp_load_type_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'price', 'Price (<i class="fa fa-inr fa-1x"></i>)', 'price' )->attributes(array("class" => "col-md-1 padding-left-none"));
			$grid->add ( 'post_status', 'Status', '' )->attributes(array("class" => "col-md-1 padding-left-none"));
			$grid->add ( 'is_cancelled', 'Post Status', true )->style ( "display:none" );
			$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
			$grid->add ( 'spostid', 'Seller Post ID', true )->style ( "display:none" );
			$grid->add ( 'transitdays', 'Transit Days', 'transitdays' )->style ( "display:none" );
			$grid->add ( 'units', 'Units', 'units' )->style ( "display:none" );
			$grid->add ( 'from_date', 'From Date', 'from_date' )->style ( "display:none" );
			$grid->add ( 'to_date', 'To Date', 'to_date' )->style ( "display:none" );
			$grid->add ( 'transaction_id', 'Transaction Id', 'transaction_id' )->style ( "display:none" );
			$grid->add ( 'created_by', 'Created by', 'created_by' )->style ( "display:none" );

			$grid->orderBy ( 'id', 'desc' );
			$grid->paginate ( 5 );


			$grid->row ( function ($row) {

				$row->cells [0]->style ( 'display:none' );
				$row->cells [9]->style ( 'display:none' );
				$spId = $row->cells [0]->value;
				$price=$row->cells [5]->value;
				$row->cells [5]->value = CommonComponent::getPriceType($row->cells [5]->value);	
				if($row->cells [7]->value == 1 )
					$row->cells [6]->value = "Deleted";
				else
					$row->cells [6]->value = "Open";
				
				$transdays = $row->cells [10]->value;
				$units = $row->cells [11]->value;
				$fromdate = $row->cells [12]->value;
				$todate = $row->cells [13]->value;
				$transaction_id = $row->cells [14]->value;
				$seller_user_id = $row->cells [15]->value;
				

				//View Count
				$countview = DB::table('seller_post_item_views')
					->where('seller_post_item_views.seller_post_item_id','=',$spId)
					->select('seller_post_item_views.id','seller_post_item_views.view_counts')
					->get();
				if(!isset($countview[0]->view_counts))
					$countview = 0;
				else
					$countview = $countview[0]->view_counts;

				$row->cells [1]->value = ''.CommonComponent::getCityName($row->cells [1]->value).'';
				$row->cells [2]->value = ''.CommonComponent::getCityName($row->cells [2]->value).'';
				$row->cells [3]->value = ''.CommonComponent::getVehicleType($row->cells [3]->value).'';
				$row->cells [4]->value = ''.CommonComponent::getLoadType($row->cells [4]->value).'';
				$seller_post_items  = DB::table('seller_post_items')
					->where('seller_post_items.id',$spId)
					->select('*')
					->get();
				
				$row->cells [1]->attributes(array("class" => "col-md-2 padding-left-none "));
				$row->cells [2]->attributes(array("class" => "col-md-2 padding-left-none "));
				$row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none "));
				$row->cells [4]->attributes(array("class" => "col-md-2 padding-left-none "));
				$row->cells [5]->attributes(array("class" => "col-md-1 padding-none "));
				$row->cells [6]->attributes(array("class" => "col-md-1 padding-none "));
				$row->cells [7]->attributes(array("class" => "col-md-2 padding-none"));
				$row->cells [10]->style ( 'display:none' );
				$row->cells [11]->style ( 'display:none' );
				$row->cells [12]->style ( 'display:none' );
				$row->cells [13]->style ( 'display:none' );
				$row->cells [14]->style ( 'display:none' );
				$row->cells [15]->style ( 'display:none' );
				//matching implmentation start
				$total_count = 0;
				if($seller_post_items[0]->is_private == 1){
					$total_count = count(CommonComponent::getPrivateBuyerMatchedResults(ROAD_FTL,$spId));
				}else{
					if(isset($spId)){
						$total_count = count(SellerMatchingComponent::getMatchedResults(ROAD_FTL,$spId));
					}
				}
				//matching implmentation end
				//Leads Count
				$serviceId = Session::get ( 'service_id' );
				$lead_count = 0;
				if($seller_post_items[0]->is_private == 1){
					$lead_count = 0;
				}else{
					if(isset($spId)){
						$lead_count += count(SellerMatchingComponent::getSellerLeads(ROAD_FTL, $spId));
					}else
						$lead_count =0;
				}
				$msg_count  =    MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$spId);
				$buyerId = Auth::User()->id;
				//Ftl Calculation did as per krishna sir discussion.(srinu-2-04-2016)
				$url = url().'/buyerbooknowforsearch/'.$row->cells [0];
				$row->cells [7]->value = "<div class='col-md-12 col-sm-12 col-xs-12 text-right padding-none'>
											<input type='button' class='btn red-btn pull-right submit-data underline_link spot_transaction_details_list show-data-link'  id=''.$spId.'' value='Book Now' style='display:none'>
										</div>
									</div>

									<div class='pull-right text-right'>
										<div class='info-links'>
											<a id=''.$spId.'' class='show-data-link'><span class='show-icon spot_transaction_details_list'>+</span><span class='hide-icon'>-</span> Details</a>											
											<a href='#' class='new_message' data-transaction_no='".$transaction_id."' data-userid='".$seller_user_id."' data-buyerquoteitemid='".$spId."'><i class='fa fa-envelope-o'></i></a>
										</div>
									</div>
				
									<div class='col-md-12 show-data-div padding-top' style='display: none;'>

											<div class='col-md-12'>
												<div class='col-md-3 padding-left-none data-fld'>
													<span class='data-head'>Transit Days</span>
													<span class='data-value'>$transdays $units</span>
												</div>
											</div>
									
											<form method='GET' role='form' action='$url' id='addftlmarketLeadsbooknow_$spId' name='addftlmarketLeadsbooknow_$spId' style='display:none'>
											
												<div class='col-md-3 padding-left-none'>
												 <div class='input-prepend'>  	
													<input type='text' name='noofloads_$spId' id='noofloads_$spId' value='' class='checktotal_price_marketleadsftl form-control form-control1 numericvalidation' placeholder= 'No of Loads' data-id='$spId'>
												</div>
												</div>	
											<input type='hidden' name='sellerprice_$spId' id='sellerprice_$spId' value=$price>
											
											<div class='col-md-3 padding-left-none'>
												Fright : <span class='display_marketledaspriceftl_$spId'></span>
											</div>
											
											<div class='col-md-12 text-right padding-none'>
												<div class='col-md-12 col-sm-12 col-xs-12 text-right padding-none'>
													<input type='submit' class='btn red-btn pull-right buyer_book_now' data-url='$url'  data-buyerpostofferid='$spId' data-booknow_list='$spId' value='Book Now' style='display:none' />
												</div>
											</div>
											
											<input id='buyersearch_booknow_buyer_id_$spId' value='$buyerId' name='buyersearch_booknow_buyer_id_$spId' type='hidden'>
											<input id='buyersearch_booknow_seller_id_$spId' value='$spId' name='buyersearch_booknow_seller_id_$spId' type='hidden'>
											<input id='buyersearch_booknow_seller_price_$spId' value='' name='buyersearch_booknow_seller_price_$spId' type='hidden'>
											<input id='buyersearch_booknow_from_date_$spId' value='$fromdate' type='hidden'>
											<input id='buyersearch_booknow_to_date_$spId' value='$todate' type='hidden'>
											<input id='buyersearch_booknow_dispatch_date_$spId' value='$fromdate' type='hidden'>
											<input id='buyersearch_booknow_delivery_date_$spId' value='$todate' type='hidden'>
											
											</form>		
											<div>																		
									</div>";

			} );

			//Functionality to build filters in the page starts
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'spi.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.to_location_id', 'From Location', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.lkp_vehicle_type_id', 'Vehicle Type', 'select')->options($vehicle_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.lkp_load_type_id', 'Load Type', 'select')->options($load_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");


			$filter->submit('search');
			$filter->reset('reset');
			$filter->build();
			//Functionality to build filters in the page ends

			$result = array();
			$result['grid'] = $grid;
			$result['filter'] = $filter;
			return $result;

		} catch( Exception $e ) {
			return $e->message;
		}
	}
}
