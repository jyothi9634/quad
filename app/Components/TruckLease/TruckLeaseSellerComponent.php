<?php
namespace App\Components\TruckLease;
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
use App\Models\TruckleaseSearchTerm;
use App\Models\SellerPostItemView;
use App\Components\Search\SellerSearchComponent;
use App\Components\Matching\SellerMatchingComponent;

use App\Components\SellerComponent;
use Faker\Provider\Payment;


class TruckLeaseSellerComponent {
	


public static function listTruckLeaseSellerPosts($statusId, $serviceId, $roleId,$type) {
		if(isset($_REQUEST['page'])){//echo $_REQUEST['page'];
		}
		//Filters values to populate in the page
		$from_locations = array(""=>"From Location");
		$to_locations = array(""=>"To Location");
		$vehicle_types = array(""=>"Vehicle Type");
		$lease_terms = array (
				"" => "Lease Term"
		);
		// query to retrieve seller posts list and bind it to the grid
		$Query = DB::table ( 'trucklease_seller_posts as sp' );
		$Query->leftjoin ( 'trucklease_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
		$Query->join ( 'lkp_trucklease_lease_terms as lt', 'lt.id', '=', 'spi.lkp_trucklease_lease_term_id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'sp.lkp_post_status_id' );
		
		if(isset($_REQUEST['lead_name']) && ($_REQUEST['lead_name'] ==2)){
			Session::put('leads', '2');
			$Query->where('sp.lkp_access_id',1);
		}
		else{
			Session::put('leads', '1');
			$Query->leftjoin ( 'trucklease_buyer_quote_selected_sellers as bqss', 'bqss.seller_id', '=', 'spi.created_by' );
			
		}
		
		$Query->where('sp.seller_id',Auth::user()->id);

		//conditions to make search
		if(isset($statusId) && $statusId != '' && $statusId !=0){
			$Query->where('sp.lkp_post_status_id', $statusId);
		}
		if(isset($type) && $type != ''){
			if($type==1){
			$Query->where('sp.created_by', Auth::user()->id);
			}
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
				'sp.to_date','sp.lkp_access_id','sp.lkp_post_status_id','ps.post_status'
		)
		->groupBy('sp.id')
		->get ();
		//Functionality to handle filters based on the selection starts
		foreach($sellerresults as $seller){
			$seller_post_items  = DB::table('trucklease_seller_post_items')
			->where('trucklease_seller_post_items.seller_post_id',$seller->id)
			->select('*')
			->get();
			
			foreach($seller_post_items as $seller_post_item){
				if(!isset($from_locations[$seller_post_item->from_location_id])){
					$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
				}
				
				if(!isset($vehicle_types[$seller_post_item->lkp_vehicle_type_id])){
					$vehicle_types[$seller_post_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $seller_post_item->lkp_vehicle_type_id)->pluck('vehicle_type');
				}
				if (! isset ( $lease_terms [$seller_post_item->lkp_trucklease_lease_term_id] )) {
					$lease_terms [$seller_post_item->lkp_trucklease_lease_term_id] = DB::table ( 'lkp_trucklease_lease_terms' )->where ( 'id', $seller_post_item->lkp_trucklease_lease_term_id )->pluck ( 'lease_term' );
				}
			}
		}
		$from_locations = CommonComponent::orderArray($from_locations);
		$vehicle_types = CommonComponent::orderArray($vehicle_types);
		//Functionality to handle filters based on the selection ends
	
		$grid = DataGrid::source ( $Query );
	
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'from_date', 'From Date', 'from_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'to_date', 'To Date', 'to_date' )->attributes(array("class" => "col-md-3 padding-left-none"));	
		$grid->add ( 'lkp_access_id', 'Post Type', 'lkp_access_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'post_status', 'Status', 'post_status' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
		$grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );
	
	
		$grid->row ( function ($row) {
			$row->cells [0]->style ( 'display:none' );

			$spId = $row->cells [0]->value;

			$val = $row->cells [4]->value;
			
			$poststaus = $row->cells [4]->value;
			if($row->cells [4]->value == 1 )
				$row->cells [4]->value = "<a href='../updateseller/$spId'>$val</a>";
			else
				$row->cells [4]->value = $val;			
			
               $row->cells [3]->value = CommonComponent::getQuoteAccessById($row->cells [3]->value);
			
			$seller_post_items  = DB::table('trucklease_seller_post_items')
							->join('trucklease_seller_posts','trucklease_seller_posts.id','=','trucklease_seller_post_items.seller_post_id')
							->where('trucklease_seller_post_items.seller_post_id',$spId)
							->select('*','trucklease_seller_post_items.id as spiid')
							->get();
			//echo "<pre>";
			//print_r($seller_post_items);exit;
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
			$getpostitemids = DB::table('trucklease_seller_post_items')
			->where('trucklease_seller_post_items.seller_post_id','=',$spId)
			->select('trucklease_seller_post_items.id')
			->get();
			
			
			
			//count for seller documents
			$serviceId = Session::get('service_id');
			$docs_seller_lease    =   CommonComponent::getGsaDocuments(SELLER,$serviceId,$spId);
			 
			
			
			$allcountview =0;
			if(count($getpostitemids)>0){
				for($i=0;$i<count($getpostitemids);$i++){
				
					$countview = DB::table('trucklease_seller_post_item_views')
					->where('trucklease_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
					->select('trucklease_seller_post_item_views.id','trucklease_seller_post_item_views.view_counts')
					->get();
					if(isset($countview[0]->view_counts))
					$allcountview +=  $countview[0]->view_counts;
				
				}
			}
			//matching implmentation start
			$total_count = 0;
			
			if($privatepost==1){
 				$total_count = count(CommonComponent::getPrivateBuyerMatchedResults(ROAD_TRUCK_LEASE,$seller_post_items[0]->spiid));
			}else{
				foreach($getpostitemids as $seller_post_item){
					if (isset($seller_post_item->id)) {
						$potitemId = $seller_post_item->id;
						$total_count += count(SellerMatchingComponent::getMatchedResults(ROAD_TRUCK_LEASE, $potitemId));
					}
				}
			}
			$lead_count=0;
			if($privatepost==1){
				$lead_count =0;
			}else{
				if (isset($seller_post_item->id)) {
						$potitemId = $seller_post_item->id;
					$lead_count += count(SellerMatchingComponent::getSellerLeads(ROAD_TRUCK_LEASE, $potitemId));
				}else
					$lead_count =0;
			}
            
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
			$row->cells [4]->value = $val;
			$row->cells [4]->attributes(array("class" => "col-md-2 padding-none"));
			//onclick="javascript:sellerpostcancel(\'posts\','.$spId.')"
			$row->cells [5]->value = '
				<div class="col-md-2 padding-none text-right"><a href="javascript:void(0)" data-target="#cancelsellerpostmodal" data-toggle="modal" onclick="setcancelpostid(\'posts\','.$spId.')" >';
				if($poststaus !='Deleted') {
				$row->cells [5]->value .= '<i class="fa fa-trash" title="Delete"></i>';
				}
				$row->cells [5]->value .= '</a></div>';
				$row->cells [5]->value .= '<div class="clearfix"></div>
					<div class="pull-left">
						<div class="info-links">
								<a>
									<i class="fa fa-envelope-o"></i> Messages <span class="badge">'.$msg_count.'</span>
								</a>
								<a>
									<i class="fa fa-file-text-o"></i> Enquiries<span class="badge">';
									if($poststaus == 'Draft' || $poststaus == 'Deleted')
										$row->cells [5]->value .='0';
									else
										$row->cells [5]->value .=$total_count;
				$row->cells [5]->value .='</span>
								</a>
								<a>
									<i class="fa fa-bullseye"></i> Leads<span class="badge">';
									if($poststaus == 'Draft')
										$row->cells [5]->value .='0';
									else
										$row->cells [5]->value .=$lead_count;
									$row->cells [5]->value .='</span>
								</a>
								<a>
									<i class="fa fa-line-chart"></i> Market Analytics
								</a>
								<a>
									<i class="fa fa-file-text-o"></i> Documentation <span class="badge">'.count($docs_seller_lease).'</span>
								</a>
							</div>
					</div>
					<div class="pull-right text-right">
						<div class="info-links">

							<a href="'.$data_link.'"><i class="fa fa-rupee"></i> '.$seller_payment_mode_method.'</a>
							<a>
								<span class="views red"><i class="fa fa-eye" title="Views"></i>';
							if($row->cells [4]->value == 'Draft')
								$row->cells [5]->value .='0';
							else
								$row->cells [5]->value .=$allcountview;
				$row->cells [5]->value .='</span>
							</a>
						</div>
					</div>';				
		} );
			//Functionality to build filters in the page starts
			
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'spi.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.lkp_vehicle_type_id', 'Vehicle Type', 'select')->options($vehicle_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.lkp_trucklease_lease_term_id', 'Lease Term', 'select')->options($lease_terms)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			
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
	 * Truck Lease Seller Post Details List Page.
	 *
	 * @param
	 * $request
	 * @return Response
	 */
	
	public static function listTruckLeaseSellerPostItems($statusId, $roleId, $serviceId, $id){
		try{
	
			//Filters values to populate in the page
			$from_locations = array(""=>"From Location");
			$to_locations = array(""=>"To Location");
			$vehicle_types = array(""=>"Vehicle Type");
			$lease_term = array(""=>"Lease Term");
			$driver = array(""=>"Driver");
			$Query = DB::table ( 'trucklease_seller_posts as sp' );
			$Query->leftjoin ( 'trucklease_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
			$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'spi.lkp_post_status_id' );
			if(Session::get('leads') &&  Session::get('leads')==2){
				Session::put('leads', '2');
				$Query->where('sp.lkp_access_id',1);
			}
			else{
				Session::put('leads', '1');
				$Query->leftjoin ( 'trucklease_buyer_quote_selected_sellers as bqss', 'bqss.seller_id', '=', 'spi.created_by' );
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
	
			$sellerresults = $Query->select ( 'spi.id', 'sp.from_date','spi.price','sp.lkp_post_status_id','sp.id as spostid','spi.minimum_lease_period',
					'sp.to_date', 'sp.transaction_id' ,'spi.lkp_vehicle_type_id','spi.price','spi.lkp_trucklease_lease_term_id',
					'sp.lkp_access_id', 'ps.post_status','sp.id as post_id','spi.from_location_id','spi.is_cancelled'
			)
			->groupBy('spi.id')
			->get ();
	
			//Functionality to handle filters based on the selection starts
			foreach($sellerresults as $seller){
				$seller_post_items  = DB::table('trucklease_seller_post_items')
				->where('trucklease_seller_post_items.id',$seller->id)
				->select('*')
				->get();
				foreach($seller_post_items as $seller_post_item){
					if(!isset($from_locations[$seller_post_item->from_location_id])){
						$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
					}
					

					if(!isset($lease_term[$seller_post_item->lkp_trucklease_lease_term_id])){
						$lease_term[$seller_post_item->lkp_trucklease_lease_term_id] = DB::table('lkp_trucklease_lease_terms')->where('id', $seller_post_item->lkp_trucklease_lease_term_id)->pluck('lease_term');
					}	
					

					if(!isset($vehicle_types[$seller_post_item->lkp_vehicle_type_id])){
						$vehicle_types[$seller_post_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $seller_post_item->lkp_vehicle_type_id)->pluck('vehicle_type');
					}
					if(!isset($driver[$seller_post_item->driver_availability])){
						if($seller_post_item->driver_availability == 1)
							$driver[$seller_post_item->driver_availability] = 'With Driver';
						else
							$driver[$seller_post_item->driver_availability] = 'Without Driver';
					}	
				}
			}
			//Functionality to handle filters based on the selection ends
	
			$grid = DataGrid::source ( $Query );
	
			$grid->add ( 'id', 'ID', true )->style ( "display:none" );
			$grid->add ( 'from_location_id', 'Location', 'from_location_id' )->attributes(array("class" => "col-md-3 padding-left-none"));
			$grid->add ( 'lkp_trucklease_lease_term_id', 'Lease Term', 'lkp_trucklease_lease_term_id' )->attributes(array("class" => "col-md-3 padding-left-none"));
			$grid->add ( 'minimum_lease_period', 'Min. Lease Period', 'minimum_lease_period' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'price', 'Rate (<i class="fa fa-inr fa-1x"></i>)', 'price' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$grid->add ( 'post_status', 'Status', '' )->attributes(array("class" => "col-md-1 padding-left-none"));
			$grid->add ( 'is_cancelled', 'Post Status', true )->style ( "display:none" );
			$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
			$grid->add ( 'spostid', 'Seller Post ID', true )->style ( "display:none" );
				
			$grid->orderBy ( 'id', 'desc' );
			$grid->paginate ( 5 );
	
	
			$grid->row ( function ($row) {
	
				$row->cells [0]->style ( 'display:none' );
				$row->cells [8]->style ( 'display:none' );
				$spId = $row->cells [0]->value;
				$row->cells [4]->value = CommonComponent::getPriceType($row->cells [4]->value);	
				$poststaus = $row->cells [6]->value;
				$spostid = $row->cells [8]->value;
				if($row->cells [6]->value == 1 )
					$row->cells [5]->value = "Deleted";
				else if($row->cells [5]->value == 'Booked')
					$row->cells [5]->value = "Booked";
				else
					$row->cells [5]->value = "Open";
	
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
				$row->cells [2]->value = ''.CommonComponent::getAllLeaseName($row->cells [2]->value).'';
				
				//count for seller documents
				$serviceId = Session::get('service_id');
				$docs_seller_lease    =   CommonComponent::getGsaDocuments(SELLER,$serviceId,$spId);
				
				
				if($row->cells [2]->value =='Daily')
					$lease = 'Days';
				elseif($row->cells [2]->value =='Weekly')
					$lease = 'Weeks';
				elseif($row->cells [2]->value =='Monthly')
					$lease = 'Months';
				else
					$lease = 'Years';
				$row->cells [3]->value = $row->cells [3]->value." ".$lease;
				
				$seller_post_items  = DB::table('trucklease_seller_post_items')
				->where('trucklease_seller_post_items.id',$spId)
				->select('*')
				->get();
                               
                                
				$data_link = url()."/sellerpostdetail/$spId";
				$row->cells [1]->attributes(array("class" => "col-md-3 padding-left-none html_link","data_link"=>$data_link));
				$row->cells [2]->attributes(array("class" => "col-md-3 padding-left-none html_link","data_link"=>$data_link));
				$row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none html_link","data_link"=>$data_link));
				$row->cells [4]->attributes(array("class" => "col-md-2 padding-none html_link","data_link"=>$data_link));
				$row->cells [5]->attributes(array("class" => "col-md-1 padding-none html_link","data_link"=>$data_link));
	
				//matching implmentation start
				$total_count = 0;
				if($seller_post_items[0]->is_private == 1){
					$total_count = count(CommonComponent::getPrivateBuyerMatchedResults(ROAD_TRUCK_LEASE,$spId));
				}else{
					if(isset($spId)){
						$total_count = count(SellerMatchingComponent::getMatchedResults(ROAD_TRUCK_LEASE,$spId));
					}
				}
				//matching implmentation end
	
	
				//Leads Count
				$lead_count = 0;
				if($seller_post_items[0]->is_private == 1){
					$lead_count = 0;
				}else{
					if(isset($spId)){
						$lead_count += count(SellerMatchingComponent::getSellerLeads(ROAD_TRUCK_LEASE, $spId));
					}else
						$lead_count =0;
				}
				$msg_count  =    MessagesComponent::listMessages(null,POSTENQURYMESSAGETYPE,null,$spId);
				$row->cells [6]->value ='';
					
				if($poststaus !=1) {
					$row->cells [6]->value .= '<div class="col-md-1 padding-none text-right"><a href="javascript:void(0)" data-target="#cancelsellerpostmodal" data-toggle="modal" onclick="setcancelpostid(\'item\','.$spId.')" >';
					$row->cells [6]->value .= '<i class="fa fa-trash" title="Delete"></i>';
					$row->cells [6]->value .= '</a></div>';
				}
	
				$row->cells [6]->value .='<div class="clearfix"></div>
						<div class="pull-left">
							<div class="info-links">
                                <a href="/sellerpostdetail/'.$spId.'?type=messages">
									<i class="fa fa-envelope-o"></i> Messages<span class="badge">'.count($msg_count['result']).'</span>
								</a>
								<a href="/sellerpostdetail/'.$spId.'?type=enquiries">
									<i class="fa fa-file-text-o"></i> Enquiries
									<span class="badge">';
				if($poststaus ==1)
					$row->cells [6]->value .='0';
				else
					$row->cells [6]->value .=$total_count;
				$row->cells [6]->value .='</span>
								</a>
								<a href="/sellerpostdetail/'.$spId.'?type=leads"><i class="fa fa-bullseye"></i> Leads<span class="badge">'.$lead_count.'</span></a>
								<a href="#"><i class="fa fa-line-chart"></i> Market Analytics</a>
								<a href="/sellerpostdetail/'.$spId.'?type=documentation"><i class="fa fa-file-text-o"></i> Documentation <span class="badge">'.count($docs_seller_lease).'</span>
								
								</a>
							</div>
						</div>
						<div class="pull-right text-right">
							<div class="info-links">';
	
	
				$row->cells [7]->value .= '<a href="/sellerpostdetail/'.$spId.'">
									<span class="views red"><i class="fa fa-eye" title="Views"></i>';
				$row->cells [7]->value .=$countview;
				$row->cells [7]->value .='
									</span>
								</a>
							</div>
						</div>';
			} );
	
				//Functionality to build filters in the page starts
				$filter = DataFilter::source ( $Query );
				$filter->add ( 'spi.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
				$filter->add ( 'spi.lkp_vehicle_type_id', 'Vehicle Type', 'select')->options($vehicle_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
				$filter->add ( 'spi.lkp_trucklease_lease_term_id', 'Lease Term', 'select')->options($lease_term)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
				$filter->add ( 'spi.driver_availability', 'Driver', 'select')->options($driver)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
	
	
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
	 * Truck lease Seller Post Details List Page with Quotes.
	 *
	 * @param
	 *        	$request
	 * @return Response
	 */
	
	public static function listTruckLeaseSellerPostDetailsItems($id){
		Session::put('seller_post_item', $id);
		try{
	
			$viewcount  = DB::table('trucklease_seller_post_items')
			->where('trucklease_seller_post_items.id','=',$id)
			->select('trucklease_seller_post_items.id',
					'trucklease_seller_post_items.created_by')
					->get();
	
				
			$countview = DB::table('trucklease_seller_post_item_views')
			->where('trucklease_seller_post_item_views.seller_post_item_id','=',$id)
			->select('trucklease_seller_post_item_views.id','trucklease_seller_post_item_views.view_counts')
			->get();
			if(!isset($countview[0]->view_counts))
				$countview = 0;
			else
				$countview = $countview[0]->view_counts;
			$seller_post = DB::table('trucklease_seller_posts')
			->join('trucklease_seller_post_items','trucklease_seller_post_items.seller_post_id','=','trucklease_seller_posts.id')
			->where('trucklease_seller_post_items.id',$id)
			->select('trucklease_seller_posts.*','trucklease_seller_post_items.id')
			->get();
	
			$seller_post_items  = DB::table('trucklease_seller_post_items')
			->where('trucklease_seller_post_items.id',$id)
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
			$tolocations = '';
			//load type
			
			$loadtype ='';
			//Lease Term
			$leaseterm   = DB::table('lkp_trucklease_lease_terms')
			->where('lkp_trucklease_lease_terms.id',$seller_post_items[0]->lkp_trucklease_lease_term_id)
			->select('id','lease_term')
			->get();
			
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
			
				$matchedIds[] = CommonComponent::getPrivateBuyerMatchedResults(ROAD_TRUCK_LEASE,$id);
			}else{
			
				if(isset($id)){
					$buyer_quote_items_matched_data = SellerMatchingComponent::getMatchedResults(ROAD_TRUCK_LEASE,$id);
					$total_count = count($buyer_quote_items_matched_data);
					foreach($buyer_quote_items_matched_data as $buyer_quote_item){
						$matchedIds[] = $buyer_quote_item->buyer_quote_id;
					}
				}
			}
	
			$buyerpublicquotedetails   = DB::table('trucklease_buyer_quotes')
			->join('trucklease_buyer_quote_items','trucklease_buyer_quote_items.buyer_quote_id','=','trucklease_buyer_quotes.id')
			->join('users','users.id','=','trucklease_buyer_quotes.created_by')
			->join('lkp_cities','lkp_cities.id','=','trucklease_buyer_quote_items.from_city_id')
			->join('lkp_vehicle_types','lkp_vehicle_types.id','=','trucklease_buyer_quote_items.lkp_vehicle_type_id')
			->leftjoin('trucklease_buyer_quote_sellers_quotes_prices','trucklease_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=','trucklease_buyer_quote_items.id')
			->whereIn('trucklease_buyer_quote_items.id',$matchedIds)
			->select('trucklease_buyer_quotes.transaction_id as transaction_no','trucklease_buyer_quote_items.id','trucklease_buyer_quote_items.lkp_trucklease_lease_term_id','users.username','trucklease_buyer_quote_items.from_date',
					'trucklease_buyer_quote_items.to_date','trucklease_buyer_quote_items.lkp_post_status_id',
					'lkp_vehicle_types.vehicle_type','trucklease_buyer_quote_items.lkp_quote_price_type_id',
					'trucklease_buyer_quote_items.from_city_id','trucklease_buyer_quote_items.lkp_vehicle_type_id',
					'trucklease_buyer_quote_items.created_by','trucklease_buyer_quote_sellers_quotes_prices.seller_id',
					'trucklease_buyer_quote_sellers_quotes_prices.initial_quote_price','lkp_cities.city_name',
					'trucklease_buyer_quote_sellers_quotes_prices.counter_quote_price','trucklease_buyer_quotes.lkp_quote_access_id',
					'trucklease_buyer_quote_sellers_quotes_prices.final_quote_price',
					'trucklease_buyer_quotes.buyer_id','trucklease_buyer_quote_sellers_quotes_prices.firm_price',
					'trucklease_buyer_quote_sellers_quotes_prices.seller_acceptence','trucklease_buyer_quote_items.price',
					'trucklease_buyer_quote_sellers_quotes_prices.id as bqsqpid')
					->groupBy('trucklease_buyer_quote_items.id')
					->get();
	
	
	
			for($i=0;$i<count($buyerpublicquotedetails);$i++){
				$buyersquotes[]	= DB::table('trucklease_buyer_quote_sellers_quotes_prices')
				->where('trucklease_buyer_quote_sellers_quotes_prices.buyer_quote_item_id',$buyerpublicquotedetails[$i]->id)
				->where('trucklease_buyer_quote_sellers_quotes_prices.buyer_id',$buyerpublicquotedetails[$i]->buyer_id)
				->where('trucklease_buyer_quote_sellers_quotes_prices.seller_id',Auth::user()->id)
				->select('trucklease_buyer_quote_sellers_quotes_prices.initial_quote_price',
						'trucklease_buyer_quote_sellers_quotes_prices.counter_quote_price',
						'trucklease_buyer_quote_sellers_quotes_prices.final_quote_price',
						'trucklease_buyer_quote_sellers_quotes_prices.firm_price',
						'trucklease_buyer_quote_sellers_quotes_prices.seller_acceptence')
						->get();
				
			}
	
			
				
			//Leads implmentation start
			$lead_count = 0;
			$matchedLeadsIds = array();
			$buyersleads = array();
			if(isset($id)){
				$buyer_quote_items_leads_data = SellerMatchingComponent::getSellerLeads(ROAD_TRUCK_LEASE,$id);
				$lead_count = count($buyer_quote_items_leads_data);
				foreach($buyer_quote_items_leads_data as $buyer_quote_lead_item){
					$matchedLeadsIds[] = $buyer_quote_lead_item->buyer_quote_id;
				}
			}
			$buyerleadsquotedetails   = DB::table('trucklease_buyer_quotes')
			->join('trucklease_buyer_quote_items','trucklease_buyer_quote_items.buyer_quote_id','=','trucklease_buyer_quotes.id')
			->join('users','users.id','=','trucklease_buyer_quotes.created_by')
			->join('lkp_cities','lkp_cities.id','=','trucklease_buyer_quote_items.from_city_id')
			->join('lkp_vehicle_types','lkp_vehicle_types.id','=','trucklease_buyer_quote_items.lkp_vehicle_type_id')
			->leftjoin('trucklease_buyer_quote_sellers_quotes_prices','trucklease_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=','trucklease_buyer_quote_items.id')
			->whereIn('trucklease_buyer_quote_items.id',$matchedLeadsIds)
			->select('trucklease_buyer_quotes.transaction_id as transaction_no','trucklease_buyer_quote_items.id','trucklease_buyer_quote_items.lkp_trucklease_lease_term_id','users.username','trucklease_buyer_quote_items.from_date',
					'trucklease_buyer_quote_items.to_date','trucklease_buyer_quote_items.lkp_post_status_id',
					'lkp_vehicle_types.vehicle_type','trucklease_buyer_quote_items.lkp_quote_price_type_id',
					'trucklease_buyer_quote_items.from_city_id','trucklease_buyer_quote_items.lkp_vehicle_type_id',
					'trucklease_buyer_quote_items.created_by','trucklease_buyer_quote_sellers_quotes_prices.seller_id',
					'trucklease_buyer_quote_sellers_quotes_prices.initial_quote_price','lkp_cities.city_name',
					'trucklease_buyer_quote_sellers_quotes_prices.counter_quote_price','trucklease_buyer_quotes.lkp_quote_access_id',
					'trucklease_buyer_quote_sellers_quotes_prices.final_quote_price',
					'trucklease_buyer_quotes.buyer_id','trucklease_buyer_quote_sellers_quotes_prices.firm_price',
					'trucklease_buyer_quote_sellers_quotes_prices.seller_acceptence','trucklease_buyer_quote_items.price',
					'trucklease_buyer_quote_sellers_quotes_prices.id as bqsqpid')
					->groupBy('trucklease_buyer_quote_items.id')
					->orderBy('trucklease_buyer_quote_items.from_date', 'asc')
					->get();
	
			for($i=0;$i<count($buyerleadsquotedetails);$i++){
				$buyersleads[]	= DB::table('trucklease_buyer_quote_sellers_quotes_prices')
				->where('trucklease_buyer_quote_sellers_quotes_prices.buyer_quote_item_id',$buyerleadsquotedetails[$i]->id)
				->where('trucklease_buyer_quote_sellers_quotes_prices.buyer_id',$buyerleadsquotedetails[$i]->buyer_id)
				->where('trucklease_buyer_quote_sellers_quotes_prices.seller_id',Auth::user()->id)
				->select('trucklease_buyer_quote_sellers_quotes_prices.initial_quote_price',
						'trucklease_buyer_quote_sellers_quotes_prices.counter_quote_price',
						'trucklease_buyer_quote_sellers_quotes_prices.final_quote_price',
						'trucklease_buyer_quote_sellers_quotes_prices.firm_price',
						'trucklease_buyer_quote_sellers_quotes_prices.seller_acceptence')
						->get();
				
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
				'leaseterm'=>$leaseterm,
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
	
	
	
	//market LEADSMESSAGETYPE

	public static function llistTruckLeasePrivatePosts($service_id, $post_status, $enquiry_type) {
	
		// Filters values to populate in the page
		$from_locations = array (
				"" => "From Location"
		);
		$to_locations = array (
				"" => "To Location"
		);
		$posted_for_types = array (
				"" => "Posted For"
		);
		$vehicle_types = array (
				"" => "Vehicle Type"
		);
		$from_date = '';
		$to_date = '';
		$order_no = '';
	
		// query to retrieve buyer posts list and bind it to the grid
		$Query = DB::table ( 'trucklease_buyer_quote_items as bqi' );
		$Query->join ( 'lkp_vehicle_types as vt', 'vt.id', '=', 'bqi.lkp_vehicle_type_id' );
		$Query->join ( 'lkp_post_statuses as ps', 'ps.id', '=', 'bqi.lkp_post_status_id' );
		$Query->join ( 'lkp_cities as cf', 'bqi.from_city_id', '=', 'cf.id' );
		$Query->join ( 'trucklease_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id' );
		$Query->join ( 'lkp_quote_accesses as lqa', 'lqa.id', '=', 'bq.lkp_quote_access_id' );
		$Query->leftjoin ( 'trucklease_buyer_quote_sellers_quotes_prices as bqss', 'bqss.buyer_quote_item_id', '=', 'bqi.id' );
		$Query->leftjoin ( 'users as us', 'us.id', '=', 'bq.buyer_id' );
		$Query->where ( 'bqss.seller_id', Auth::User ()->id );
	
		$Query->whereIn('bqi.lkp_post_status_id',array(2,3,4,5));
		$Query->where('bq.lkp_quote_access_id','=',2);
		$Query->groupBy('bqi.buyer_quote_id');
		$Query->orderBy('bqi.buyer_quote_id', 'DESC');
	
	
		// conditions to make search
		if (isset (  $post_status ) && $post_status != '' && $post_status!=0) {
			$Query->where ( 'bqi.lkp_post_status_id', '=', $post_status );
		}
		if (isset ( $_GET ['from_date'] ) && $_GET ['from_date'] != '') {
			$commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['from_date']);
			$Query->where ( 'bqi.dispatch_date', '>=', $commonDispatchDate );
			$from_date = $commonDispatchDate;
		}
		if (isset ( $_GET ['to_date'] ) && $_GET ['to_date'] != '') {
			$commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['to_date']);
			$Query->where ( 'bqi.dispatch_date', '<=', $commonDeliveryhDate);
			$to_date = $commonDeliveryhDate;
		}
	
		$postResults = $Query->select ( 'bq.buyer_id','us.username','bq.transaction_id','bqi.*', 'vt.vehicle_type', 'ps.post_status', 'cf.city_name as fromcity',
				'bq.lkp_quote_access_id','lqa.quote_access')->get ();
		
		// Functionality to handle filters based on the selection starts
		foreach ( $postResults as $post ) {
			$buyer_quotes = DB::table ( 'trucklease_buyer_quote_items' )->leftJoin( 'trucklease_buyer_quotes as bq', 'bq.id', '=', 'trucklease_buyer_quote_items.buyer_quote_id' )->where ( 'trucklease_buyer_quote_items.id', $post->id )->select ( 'trucklease_buyer_quote_items.*','bq.lkp_quote_access_id' )->get ();
	
			foreach ( $buyer_quotes as $quotes ) {
				//echo "<pre>"; print_r($quotes);die();
				if (! isset ( $from_locations [$quotes->from_city_id] )) {
					$from_locations [$quotes->from_city_id] = DB::table ( 'lkp_cities' )->where ( 'id', $quotes->from_city_id )->pluck ( 'city_name' );
				}
				if (! isset ( $vehicle_types [$quotes->lkp_vehicle_type_id] )) {
					$vehicle_types [$quotes->lkp_vehicle_type_id] = DB::table ( 'lkp_vehicle_types' )->where ( 'id', $quotes->lkp_vehicle_type_id )->pluck ( 'vehicle_type' );
				}
				if (! isset ( $posted_for_types [$quotes->lkp_quote_access_id] )) {
					$posted_for_types [$quotes->lkp_quote_access_id] = DB::table ( 'lkp_quote_accesses' )->where ( 'id', $quotes->lkp_quote_access_id )->pluck ( 'quote_access' );
				}
			}
		}
		$from_locations = CommonComponent::orderArray($from_locations);
		$vehicle_types = CommonComponent::orderArray($vehicle_types);
	
		//grid
		$grid = DataGrid::source ( $Query );
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Buyer Name', 'username' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'from_date', 'From Date', 'from_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'to_date', 'To Date', 'to_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'price', 'Pricing', 'price' )->attributes(array("class" => "col-md-2 padding-left-none hidden-xs"))->style ( "display:none" );
		$grid->add ( 'lkp_post_status_id', 'Status', 'lkp_post_status_id' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'vehicle_type', 'VehicleType', 'vehicle_type' )->style ( "display:none" );
		$grid->add ( 'fromcity', 'FromCity', 'fromcity' )->style ( "display:none" );
		$grid->add ( 'delivery_sdate', 'Delivery Date', 'delivery_sdate' )->style ( "display:none" );
		$grid->add ( 'created_by', 'Created By', 'created_by' )->style ( "display:none" );
		$grid->add ( 'lkp_quote_access_id', 'Quote Access', 'lkp_quote_access_id' )->style ( "display:none" );
		$grid->add ( 'transaction_id', 'transaction_id', 'transaction_id' )->style ( "display:none" );
		$grid->add ( 'from_city_id', 'From City', 'from_city_id' )->style ( "display:none" );
		$grid->add ( 'lkp_trucklease_lease_term_id 	', 'Lease', 'lkp_trucklease_lease_term_id 	' )->style ( "display:none" );
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
			$row->cells [8]->style ( 'display:none' );
			$row->cells [9]->style ( 'display:none' );
			$row->cells [10]->style ( 'display:none' );
			$row->cells [11]->style ( 'display:none' );
			$row->cells [12]->style ( 'display:none' );
			$row->cells [13]->style ( 'display:none' );
	
				
			$row->cells [6]->style ( 'width:100%' );
			$transaction_id=$row->cells[11]->value;
			$accessid = $row->cells [10]->value;
			$buyer_id = $row->cells [9]->value;
			$buyer_quote_id = $row->cells [0]->value;
			$buyer_name = $row->cells [1]->value;
			$dispatch_date_buyer = $row->cells [2]->value;
			$delivery_date_buyer = $row->cells [3]->value;
			$price_buyer = $row->cells [4]->value;
			$fprice = $row->cells [4]->value;
			$getbqi = DB::table('trucklease_buyer_quote_items')
			->where('trucklease_buyer_quote_items.id','=',$buyer_quote_id)
			->select('price', 'lkp_quote_price_type_id')
			->get();
			$buyer_post_status = $row->cells [5]->value;
			$buyer_post_status_id = $row->cells [5]->value;
			$vechile_type_buyer = $row->cells [6]->value;
			$fromcity_buyer = $row->cells [7]->value;
			$load_type_buyer = $row->cells [13]->value;
			
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
				
				
			$from_city_id = $row->cells [12]->value;
	
			$row->cells [6]->value = '<form id ="addsellersearchpostquoteofferTL" name ="addsellersearchpostquoteofferTL">';
			$getInitialQuotePrice = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'initial_quote_price','trucklease_buyer_quote_sellers_quotes_prices');
			$getCounterQuotePrice = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'counter_quote_price','trucklease_buyer_quote_sellers_quotes_prices');
			$getFinalQuotePrice   = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'final_quote_price','trucklease_buyer_quote_sellers_quotes_prices');
			$getFirmQuotePrice   = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'firm_price','trucklease_buyer_quote_sellers_quotes_prices');
			$subscription  = DB::table('sellers')
			->where('sellers.user_id',Auth::user()->id)
			->select('sellers.subscription_end_date','sellers.subscription_start_date')
			->get();
	
			if(count($subscription)==0){
				$subscription  = DB::table('seller_details')
				->where('seller_details.user_id',Auth::user()->id)
				->select('seller_details.subscription_end_date','seller_details.subscription_start_date')
				->get();
			}
			$qty='';
			$loads='';
			$subs_st_date = date('Y-m-d', strtotime($subscription[0]->subscription_start_date));
			$subs_end_date = date('Y-m-d', strtotime($subscription[0]->subscription_end_date));
			$now_date = date('Y-m-d');
			$delivery_date_buyer_convert = CommonComponent::checkAndGetDate($delivery_date_buyer);
			if($delivery_date_buyer_convert != ""){
				$dates = CommonComponent::checkAndGetDate($dispatch_date_buyer)." - ".$delivery_date_buyer_convert;
			}else{
				$dates = CommonComponent::checkAndGetDate($dispatch_date_buyer);
			}
			$row->cells [6]->value .= '<div class=""><div class="col-md-3 padding-left-none">
											'.$buyer_name.'
											<div class="red">
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
											</div>
										</div>
										<div class="col-md-2 padding-left-none">'.CommonComponent::checkAndGetDate($dispatch_date_buyer).'</div>
										<div class="col-md-2 padding-none">'.CommonComponent::checkAndGetDate($delivery_date_buyer).'</div>
										<div class="col-md-3 padding-left-none">'.$buyer_post_status.'</div>';
	
				
			$getSellerpost  =   SellerComponent::truckleaseSellerPostDetails($from_city_id,$buyer_quote_id);
	
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
	
	
			$SubmitquotePartial = view('partials.seller.submit_quote')->with([
					'getFirmQuotePrice' => $getFirmQuotePrice,
					'getInitialQuotePrice'=>$getInitialQuotePrice,
					'getCounterQuotePrice'=>$getCounterQuotePrice,
					'getFinalQuotePrice' => $getFinalQuotePrice,
					'now_date' => $now_date,
					'subs_st_date' => $subs_st_date,
					'subs_end_date' => $subs_end_date,
					'getbqi' => $getbqi,
					'buyer_post_status_id'=>$buyer_post_status_id,
					'delivery_date_buyer'=>$delivery_date_buyer,
					'buyer_id' =>$buyer_id,
					'buyer_quote_id'=>$buyer_quote_id,
					'transaction_id'=>$transaction_id,
					'fromcity_buyer'=>$fromcity_buyer,
					'tocity_buyer'=>'',
					'dispatch_date_buyer'=>$dispatch_date_buyer,
					'vechile_type_buyer'=>$vechile_type_buyer,
					'load_type_buyer'=>$load_type_buyer,
					'tracking'=>$tracking,
					'price_buyer'=>$price_buyer,
					'payment_type'=>$payment_type,
					'getSellerpost'=>$getSellerpost,
					'accessid'=>$accessid,
					])->render();
						
			$row->cells [6]->value.=$SubmitquotePartial;
			
		} );
		$filter = DataFilter::source ( $Query );
		$filter->add('bqi.from_city_id', 'From City', 'select')->options($from_locations)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
		$filter->add ('bqi.lkp_vehicle_type_id', 'Vehicle Type', 'select' )->options ( $vehicle_types )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->reset('reset');
		$filter->build();
		//Functionality to build filters in the page ends
	
		$result = array();
		$result['grid'] = $grid;
		$result['filter'] = $filter;
		return $result;
	
	}

	public static function getSellerSearchList($roleId, $serviceId,$statusId) {

		$from_locations = array(""=>"From Location");
		$vehicle_types = array("" => "Vehicle Type");
		$lease_terms = array("" => "Lease Term");
		$inputparams = array();
		
		$buyerNames = array ();
		$buyerPriceType = array ();

		$request['is_dispatch_flexible'] = isset($request['dispatch_flexible_hidden']) ? $request['dispatch_flexible_hidden'] : 0;
		$request['is_delivery_flexible'] = isset($request['delivery_flexible_hidden']) ? $request['delivery_flexible_hidden'] : 0;

		if(isset($_REQUEST['lkp_vehicle_type_ids']) && $_REQUEST['lkp_vehicle_type_ids']!=''){
			if(isset($_REQUEST['lkp_vehicle_type_id']) && $_REQUEST['lkp_vehicle_type_id']!=''){
				$_REQUEST['lkp_vehicle_type_id'] =$_REQUEST['lkp_vehicle_type_id'];
			}else{
				$_REQUEST['lkp_vehicle_type_id'] =$_REQUEST['lkp_vehicle_type_ids'];
			}
		}
		if(isset($_REQUEST['lkp_trucklease_lease_term_ids']) && $_REQUEST['lkp_trucklease_lease_term_ids']!=''){
			if(isset($_REQUEST['lkp_trucklease_lease_term_id']) && $_REQUEST['lkp_trucklease_lease_term_id']!=''){
				$_REQUEST['lkp_trucklease_lease_term_id'] =$_REQUEST['lkp_trucklease_lease_term_id'];
			}else{
				$_REQUEST['lkp_trucklease_lease_term_id'] =$_REQUEST['lkp_load_type_ids'];
			}
		}

		$inputparams = $_REQUEST;
		$Query_buyers_for_sellers = SellerSearchComponent::search ( $roleId, $serviceId, $statusId, $inputparams );

		if(isset($_REQUEST['lkp_vehicle_type_id']) && $_REQUEST['lkp_vehicle_type_id']!='' && isset($_REQUEST['lkp_trucklease_lease_term_id']) && $_REQUEST['lkp_trucklease_lease_term_id']!='' && isset($_REQUEST['from_city_id']) && $_REQUEST['from_city_id']!='' && isset($_REQUEST['dispatch_date']) && $_REQUEST['dispatch_date']!='' && isset($_REQUEST['delivery_date']) && $_REQUEST['delivery_date']!='')
		{
			$sellerpost_for_buyers  =  new  TruckleaseSearchTerm();
			$sellerpost_for_buyers->user_id = Auth::id();
			$sellerpost_for_buyers->from_city_id = $_REQUEST['from_city_id'];
			$sellerpost_for_buyers->from_date = $_REQUEST['dispatch_date'];
			$sellerpost_for_buyers->to_date = $_REQUEST['delivery_date'];
			$sellerpost_for_buyers->lkp_trucklease_lease_term_id = $_REQUEST['lkp_trucklease_lease_term_id'];
			$sellerpost_for_buyers->lkp_vehicle_type_id = $_REQUEST['lkp_vehicle_type_id'];
			$sellerpost_for_buyers->created_at = date ( 'Y-m-d H:i:s' );
			$sellerpost_for_buyers->created_ip = $_SERVER ['REMOTE_ADDR'];
			$sellerpost_for_buyers->created_by = Auth::id();
			$sellerpost_for_buyers->save();
		}

		$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();
		
		if(count($Query_buyers_for_sellers_filter) == 0 ){
			Session::put('results_count','1');
		}else{
			Session::put('results_count','');
			Session::put('results_count_more','2');
		}

		foreach($Query_buyers_for_sellers_filter as $Query_buyers_for_seller){
			$buyers_for_sellers_items  = DB::table('trucklease_buyer_quote_items')
				->where('trucklease_buyer_quote_items.id',$Query_buyers_for_seller->id)
				->select('*')
				->get();
			Session::put('delivery_date',$Query_buyers_for_seller->from_date);
			Session::put('dispatch_date',$Query_buyers_for_seller->to_date);
			Session::put('vehicle_type',$Query_buyers_for_seller->vehicle_type);
			Session::put('lease_term',$Query_buyers_for_seller->lease_term);

			foreach($buyers_for_sellers_items as $buyers_for_sellers_item){
				
				if(!isset($from_locations[$buyers_for_sellers_item->from_city_id])){
					$from_locations[$buyers_for_sellers_item->from_city_id] = DB::table('lkp_cities')->where('id', $buyers_for_sellers_item->from_city_id)->pluck('city_name');
				}

				
				if(!isset($vehicle_types[$buyers_for_sellers_item->lkp_vehicle_type_id])){
					$vehicle_types[$buyers_for_sellers_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $buyers_for_sellers_item->lkp_vehicle_type_id)->pluck('vehicle_type');
				}


				if(isset($_REQUEST['is_search'])){
					if (! isset ( $buyerNames [$Query_buyers_for_seller->buyer_id] )) {
						$buyerNames[$Query_buyers_for_seller->buyer_id] = $Query_buyers_for_seller->username;
					}
					if (! isset ( $buyerPriceType [$Query_buyers_for_seller->id] )) {
						$buyerPriceType[$Query_buyers_for_seller->lkp_quote_price_type_id] = $Query_buyers_for_seller->lkp_quote_price_type_id;
					}
					if (! isset ( $buyerFrom [$Query_buyers_for_seller->id] )) {
						$buyerFrom[$Query_buyers_for_seller->from_date] = $Query_buyers_for_seller->from_date;
					}

					if (! isset ( $buyerTo [$Query_buyers_for_seller->id] )) {
						$buyerTo[$Query_buyers_for_seller->to_date] = $Query_buyers_for_seller->to_date;
					}
					if (! isset ( $lease_terms [$Query_buyers_for_seller->id] )) {
						$lease_terms[$buyers_for_sellers_item->lkp_trucklease_lease_term_id] = DB::table('lkp_trucklease_lease_terms')->where('id', $buyers_for_sellers_item->lkp_trucklease_lease_term_id)->pluck('lease_term');
					}
					if (! isset ( $vehicle_types [$Query_buyers_for_seller->id] )) {
						$vehicle_types[$buyers_for_sellers_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $buyers_for_sellers_item->lkp_vehicle_type_id)->pluck('vehicle_type');
					}
					Session::put('layered_filter', $buyerNames);
					Session::put('price_filter', $buyerPriceType);
					Session::put('from_date_filter', $buyerFrom);
					Session::put('to_date_filter', $buyerTo);
					Session::put('lease_type_filter', $lease_terms);
					Session::put('vehicle_type_filter', $vehicle_types);
				}
			}
		}

		$grid = DataGrid::source ( $Query_buyers_for_sellers );
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Buyer Name', 'username' )->attributes(array("class" => "col-md-4 padding-left-none"));
		$grid->add ( 'delivery_sdate', 'Rating', 'delivery_sdate' )->attributes(array("class" => "col-md-2 padding-left-none"))->style ( "display:none" );
		$grid->add ( 'from_date', 'Dispatch Date', 'from_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'lkp_quote_price_type_id', 'Pricing', 'lkp_quote_price_type_id' )->attributes(array("class" => "col-md-2 padding-left-none hidden-xs"));
		$grid->add ( 'lkp_post_status_id', 'Status', 'lkp_post_status_id' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'lease_term', 'Lease Term', 'lease_term' )->style ( "display:none" );
		$grid->add ( 'vehicle_type', 'VehicleType', 'vehicle_type' )->style ( "display:none" );
		$grid->add ( 'from_city_id', 'FromCity', 'from_city_id' )->style ( "display:none" );
		$grid->add ( 'tocity', 'Tocity', 'tocity' )->style ( "display:none" );
		$grid->add ( 'to_date', 'Delivery Date', 'to_date' )->style ( "display:none" );
		$grid->add ( 'created_by', 'Created By', 'created_by' )->style ( "display:none" );
		$grid->add ( 'lkp_quote_access_id', 'Quote Access', 'lkp_quote_access_id' )->style ( "display:none" );
		$grid->add ( 'transaction_id', 'transaction_id', 'transaction_id' )->style ( "display:none" );

		$grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );
		$grid->row ( function ($row) {
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

			$row->cells [5]->style ( 'width:100%' );
			$transaction_id=$row->cells[13]->value;
			$accessid = $row->cells [12]->value;
			$buyer_id = $row->cells [11]->value;
			$buyer_quote_id = $row->cells [0]->value;
			$buyer_post_status_id = $row->cells [5]->value;
			$buyer_name = $row->cells [1]->value;
			$dispatch_date_buyer = $row->cells [3]->value;
			$price_buyer = $row->cells [4]->value;
			$fprice = $row->cells [4]->value;
			$getbqi = DB::table('trucklease_buyer_quote_items')
				->where('trucklease_buyer_quote_items.id','=',$buyer_quote_id)
				->select('price', 'lkp_quote_price_type_id')
				->get();
			
			$buyer_post_status = $row->cells [5]->value;
			$load_type_buyer = $row->cells [6]->value;
			$vechile_type_buyer = $row->cells [7]->value;
			$fromcity_buyer = CommonComponent::getCityName($row->cells [8]->value);
			$tocity_buyer = $row->cells [9]->value;
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
			if($price_buyer == 1){
				$price_buyer = "Competitive";
			}else{
				$price_buyer = "Firm";
			}



			$row->cells [5]->value = '<form id ="addsellersearchpostquoteofferTL" name ="addsellersearchpostquoteofferTL">';
			$getInitialQuotePrice = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'initial_quote_price','trucklease_buyer_quote_sellers_quotes_prices');
			$getCounterQuotePrice = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'counter_quote_price','trucklease_buyer_quote_sellers_quotes_prices');
			$getFinalQuotePrice   = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'final_quote_price','trucklease_buyer_quote_sellers_quotes_prices');
			$getFirmQuotePrice   = CommonComponent::getQuotePriceForSearch($buyer_id,$buyer_quote_id,Auth::user()->id,'firm_price','trucklease_buyer_quote_sellers_quotes_prices');
			$subscription  = DB::table('sellers')
				->where('sellers.user_id',Auth::user()->id)
				->select('sellers.subscription_end_date','sellers.subscription_start_date')
				->get();

			if(count($subscription)==0){

				$subscription  = DB::table('seller_details')
					->where('seller_details.user_id',Auth::user()->id)
					->select('seller_details.subscription_end_date','seller_details.subscription_start_date')
					->get();
			}

                        if($_REQUEST['lkp_trucklease_lease_term_id'] == 1) {
                            $submitQuoteClass = 'fourdigitstwodecimals_deciVal';
                        } elseif($_REQUEST['lkp_trucklease_lease_term_id'] == 2) {
                            $submitQuoteClass = 'fivedigitstwodecimals_deciVal';
                        } elseif($_REQUEST['lkp_trucklease_lease_term_id'] == 3) {
                            $submitQuoteClass = 'sixdigitstwodecimals_deciVal';
                        } elseif($_REQUEST['lkp_trucklease_lease_term_id'] == 4) {
                            $submitQuoteClass  = 'sevendigitstwodecimals_deciVal';
                        } else {
                            $submitQuoteClass  = 'fourdigitstwodecimals_deciVal';
                        }
                        

			$subs_st_date = date('Y-m-d', strtotime($subscription[0]->subscription_start_date));
			$subs_end_date = date('Y-m-d', strtotime($subscription[0]->subscription_end_date));
			$now_date = date('Y-m-d');
			$row->cells [5]->value .= '

						<div class="">
										<div class="col-md-4 padding-left-none">
											'.$buyer_name.'
											<div class="red">
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
											</div>
										</div>
										<div class="col-md-2 padding-left-none">'.CommonComponent::checkAndGetDate($dispatch_date_buyer).'</div>
										<div class="col-md-2 padding-none">'.$price_buyer.'</div>
										<div class="col-md-2 padding-left-none">'.$buyer_post_status.'</div>';

			$getSellerpost  = DB::table('trucklease_seller_post_items')
				->join( 'trucklease_seller_posts', 'trucklease_seller_posts.id', '=', 'trucklease_seller_post_items.seller_post_id' )
				->join( 'trucklease_buyer_quote_sellers_quotes_prices', 'trucklease_buyer_quote_sellers_quotes_prices.seller_post_item_id', '=', 'trucklease_seller_post_items.id' )
				->where('trucklease_seller_post_items.from_location_id','=',$_REQUEST['from_city_id'])
				->where('trucklease_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyer_quote_id)
				->where('trucklease_seller_post_items.created_by','=',Auth::user()->id)
				->where('trucklease_seller_posts.lkp_post_status_id','=',OPEN)
				->select('trucklease_seller_post_items.seller_post_id',
					'trucklease_seller_post_items.id',
					'trucklease_seller_posts.tracking',
					'trucklease_seller_posts.lkp_payment_mode_id',
					'trucklease_seller_posts.accept_payment_netbanking',
					'trucklease_seller_posts.accept_payment_credit',
					'trucklease_seller_posts.accept_payment_debit',
					'trucklease_seller_posts.credit_period',
					'trucklease_seller_posts.credit_period_units',
					'trucklease_seller_posts.accept_credit_netbanking',
					'trucklease_seller_posts.accept_credit_cheque')
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

			$SubmitquotePartial = view('partials.seller.submit_quote')->with([
				'getFirmQuotePrice' => $getFirmQuotePrice,
				'getInitialQuotePrice'=>$getInitialQuotePrice,
				'getCounterQuotePrice'=>$getCounterQuotePrice,
				'getFinalQuotePrice' => $getFinalQuotePrice,
				'now_date' => $now_date,
				'subs_st_date' => $subs_st_date,
				'subs_end_date' => $subs_end_date,
				'getbqi' => $getbqi,
				'buyer_post_status_id'=>$buyer_post_status_id,
				'delivery_date_buyer'=>$delivery_date_buyer,
				'buyer_id' =>$buyer_id,
				'buyer_quote_id'=>$buyer_quote_id,
				'transaction_id'=>$transaction_id,
				'fromcity_buyer'=>$fromcity_buyer,
				'tocity_buyer'=>$tocity_buyer,
				'dispatch_date_buyer'=>$dispatch_date_buyer,
				'vechile_type_buyer'=>$vechile_type_buyer,
				'load_type_buyer'=>$load_type_buyer,
				'tracking'=>$tracking,
				'price_buyer'=>$price_buyer,
				'payment_type'=>$payment_type,
				'getSellerpost'=>$getSellerpost,
				'accessid'=>$accessid,
				'lease_term_id'=>$_REQUEST['lkp_trucklease_lease_term_id']
				])->render();
			
			$row->cells [5]->value.=$SubmitquotePartial;
			
			
		} );
		
		$filter = DataFilter::source ( $Query_buyers_for_sellers );
		$filter->add ( 'bqi.lkp_vehicle_type_id', 'Vehicle Type', 'select')->options($vehicle_types)->attr("class"," form-control1")->attr("onchange","this.form.submit()");
		
		$filter->submit('search');
		$filter->reset('reset');
		$filter->build();
		$result = array();
		$result['grid'] = $grid;
		$result['filter'] = $filter;
		return $result;
	}
	
	public static function getTruckLeaseSellerPostItemDetails($id){
		try{
			$seller_post = DB::table('trucklease_seller_posts as sp')
			->leftjoin('trucklease_seller_post_items as spi','spi.seller_post_id','=','sp.id')
            ->leftjoin('users','users.id','=','sp.seller_id')
            ->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'spi.lkp_vehicle_type_id')
            ->leftjoin('lkp_trucklease_lease_terms as tlt', 'tlt.id', '=', 'spi.lkp_trucklease_lease_term_id')                    
            ->leftjoin('lkp_post_statuses as lps', 'lps.id', '=', 'spi.lkp_post_status_id')
            ->leftjoin('trucklease_buyer_quote_sellers_quotes_prices as bqsp', 'bqsp.seller_post_item_id', '=', 'spi.id')                    
            ->where('spi.id',$id)
            ->select('tlt.lease_term','sp.*','spi.*','users.username', 'lvt.vehicle_type', 'lps.post_status' )
            ->get();
            if(!empty($seller_post)) {
                $fromLocation = BuyerComponent::getCityNameFromId($seller_post[0]->from_location_id);
                //$toLocation = BuyerComponent::getCityNameFromId($seller_post[0]->to_location_id);
                $deliveryDate = CommonComponent::checkAndGetDate($seller_post[0]->from_date);
                $dispatchDate = CommonComponent::checkAndGetDate($seller_post[0]->to_date);
            } else {
                $fromLocation = '';
                //$toLocation = '';
                $deliveryDate = '';
                $dispatchDate = '';
            }
            if(isset($getpostitemids[0]->id))
				$countview = DB::table('trucklease_seller_post_item_views as spiv')
				->where('spiv.seller_post_item_id','=',$getpostitemids[0]->id)
				->select('spiv.id','spiv.view_counts')
				->get();
			if(!isset($countview[0]->view_counts))
				$countview = 0;
			else
				$countview = $countview[0]->view_counts;
    		return array('id'=>$id,
					 'seller_post'=>$seller_post,
					 'countview'=>$countview,
					 'fromLocation'=>$fromLocation,
					 //'toLocation'=>$toLocation,
					 'deliveryDate'=>$deliveryDate,
					 'dispatchDate'=>$dispatchDate,
		 			);
        } catch (Exception $e) {
            
        }
	}

	
}