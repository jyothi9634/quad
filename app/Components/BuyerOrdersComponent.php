<?php

namespace App\Components;

use DB;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use Auth;
use App\Http\Requests;
use Input;
use Config;
use File;
use Session;
use Redirect;
use Log;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

/* Ftl Buyer Orders list new component start
*  @srinivas dantha
*  Date : 12 th July,2016
*/
class BuyerOrdersComponent {

	public static function getFtlBuyerOrdersList() {

        $serviceId = Session::get('service_id');
        $query = DB::table('orders');
		$query->leftJoin('order_payments as op', 'op.id', '=', 'orders.order_payment_id');
		$query->leftJoin('order_invoices as oi', 'oi.order_id', '=', 'orders.id');
		$query->leftJoin('lkp_cities as fc', 'fc.id', '=', 'orders.from_city_id');
		$query->leftJoin('lkp_cities as tc', 'tc.id', '=', 'orders.to_city_id');
		$query->leftJoin('users as u', 'u.id', '=', 'orders.seller_id');
		$query->leftJoin('seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
		$query->leftJoin('seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
		$query->leftJoin('lkp_load_types as llt', 'llt.id', '=', 'orders.lkp_load_type_id');
		$query->leftJoin('lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id');
		$query->leftJoin('lkp_order_statuses as os', 'os.id', '=', 'orders.lkp_order_status_id');
		$query->leftJoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'orders.lkp_vehicle_type_id');	
		$query->where('orders.buyer_id', '=', Auth::user()->id);
		$query->where('orders.lkp_service_id', '=', ROAD_FTL);
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}
		$query->groupBy('orders.id');
		$query->select('sp.tracking','oi.total_amt as inv_total','orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.city_name as from_city', 'tc.city_name as to_city','u.username','lvt.vehicle_type','llt.load_type','sp.tracking');		
		//Filters values to populate in the page		
		$from_locations[''] = "From Location";
		$to_locations['']	= "To Location";        
        $buyers[''] 		= "Seller";
        $consignee['']		= "Consignee";
        // Getting From Locations based on result set	
        $result = $query->get();	
        //echo "<pre>"; print_r($result); die;
		foreach($result as $getData):
			$from_locations[$getData->from_city_id] 		= $getData->from_city;
			$to_locations[$getData->to_city_id] 			= $getData->to_city;
			$buyers[$getData->username] 					= $getData->username;
			if(isset($getData->buyer_consignee_name) && $getData->buyer_consignee_name!='')
			$consignee[$getData->buyer_consignee_name] 		= $getData->buyer_consignee_name;	
		endforeach;

		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations 	= CommonComponent::orderArray($to_locations);
		$buyers 		= CommonComponent::orderArray($buyers);
		$consignee 		= CommonComponent::orderArray($consignee);

		// Filters Start
		$filter = \DataFilter::source($query);

		// From locations dropdown
		$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   		});
		// To locations dropdown
		$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   		});
		// Seller Name dropdown
   		$filter->add('username', '', 'select')->options($buyers)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});
   		// consignee Name dropdown
   		$filter->add('buyer_consignee_name', '', 'select')->options($consignee)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	
        	return ($value!='')? $query->where('orders.buyer_consignee_name', $value):$query;
   		});
   		$filter->add('order_no', 'Order No', 'text')->attr("class", "top-text-fld form-control1")->attr("onchange", "this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('orders.order_no', $value):$query;
       	});
   		// Dispatch date
		$filter->add('start_dispatch_date', 'From Date', 'text')->attr(["class" => "dateRange", "onchange"=>"this.form.submit()"])->scope( function ($query, $value) {        	
        	if(!empty($value)):        		
        		return $query->where ( 'orders.dispatch_date', '>=', CommonComponent::convertDateForDatabase($value) );			
        	else:
        		return $query;
        	endif;
   		});

		// delivery_date 
		$filter->add('end_dispatch_date', 'To Date', 'text')->attr(["class" => "dateRange","onchange"=>"this.form.submit()"])->scope( function ($query, $value) {
        	if(!empty($value)):
        		return $query->where ( 'orders.delivery_date', '>=', CommonComponent::convertDateForDatabase($value) );        		
        	else:
        		return $query;
        	endif;
   		});

		$filter->build();
		$grid = \DataGrid::source($query);		
		// Grid Headings
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-3 padding-left-none')); 
	   	$grid->add('username','Vendor Name', true)->attributes(array("class" => 'col-md-3 padding-left-none')); 
	   	$grid->add('from_city','From', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('to_city','To', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");
		//Grid data append for columns
		$grid->row( function($row) {
			// Order No
			$row->cells[0]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->order_no.'</a>');
			// User Name
			$row->cells[1]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->username.'</a>');
			// From city
			$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->from_city.'</a>');
			// To city
			$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->to_city.'</a>');

			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-left-none text-right'));

			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_buyer = CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);
			$order_id 	= $row->data->id;
			$seller_id  = $row->data->seller_id;
			$tracking_status = CommonComponent::getTrackingType($row->data->tracking);
            if($row->data->lkp_order_type_id==TERM)
                $tracking_status = 'N/A'; 

            if($row->data->number_loads!="undefined")
            	$noOfLoads = $row->data->number_loads;
            else
            	$noOfLoads = "N/A";

        	if($row->data->inv_total!=0)
                $order_price = CommonComponent::moneyFormat($row->data->inv_total);
            else
                $order_price = CommonComponent::moneyFormat($row->data->price);

            //Getting status bar colors progress bar
            $StatusBar = BuyerOrdersComponent :: getStatusProgressbarColor ($row->data->seller_pickup_date, $row->data->dispatch_date, $row->data->seller_delivery_date, $row->data->delivery_date);            
            $status = $row->data->order_status;
            if($status!='')
            	$statusText = $row->data->order_status;
            else
            	$statusText = '';

			$row->cells[5]->value = '
			<div class="col-md-2 padding-none status-block">
				<div class="status-bar">
					<div class="status-bar">
						'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'
						<span class="status-text">'.$statusText.'</span>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
        	<div class="col-md-10 padding-none pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_buyer).'</span></a>
				</div>
			</div>
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$seller_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 tab-modal-head">
                    <h3>
                        <i class="fa fa-map-marker"></i> '.$row->data->from_city.' to '.$row->data->to_city.'
                        <span class="close-icon">x</span>
                    </h3>
                </div>
                <div class="clearfix"></div>  
                <div class="col-md-8 data-div break-word">              
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Dispatch Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>				
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Consignee</span>
					<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
				</div>
				<div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Vehicle Type</span>
	                <span class="data-value">'.$row->data->vehicle_type.'</span>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Load Type</span>
	                <span class="data-value">'.$row->data->load_type.'</span>
                </div>
                <div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Tracking</span>
	                <span class="data-value">'.$tracking_status.'</span>
                </div>
                <div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Quantity</span>
	                <span class="data-value">'.$row->data->quantity.' '.$row->data->units.'</span>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">No of Loads</span>
	                <span class="data-value">'.$noOfLoads.'</span>
                </div>
                </div>                
                <div class="col-md-4">
                    <span class="data-head">Total Price</span>
                    <span class="data-value big-value">'.$order_price.' /-</span>
                </div>
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}

/* TruckHaul and Lease Buyer Orders list new component start
*  @srinivas dantha
*  Date : 12 th July,2016
*/

	public static function getTruckHaulLeaseBuyerOrdersList() {

        $serviceId = Session::get('service_id');
        $query = DB::table('orders');
		$query->leftJoin('order_payments as op', 'op.id', '=', 'orders.order_payment_id');
		$query->leftJoin('order_invoices as oi', 'oi.order_id', '=', 'orders.id');
		$query->leftJoin('lkp_cities as fc', 'fc.id', '=', 'orders.from_city_id');
		$query->leftJoin('lkp_cities as tc', 'tc.id', '=', 'orders.to_city_id');
		$query->leftJoin('users as u', 'u.id', '=', 'orders.seller_id');
		$query->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'op.lkp_payment_mode_id' );
		$query->leftJoin('lkp_order_statuses as os', 'os.id', '=', 'orders.lkp_order_status_id');
		if($serviceId == ROAD_TRUCK_HAUL) {
			$query->leftJoin('truckhaul_seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
			$query->leftJoin('truckhaul_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
			$query->leftJoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'orders.lkp_vehicle_type_id');
			$query->leftJoin('lkp_load_types as llt', 'llt.id', '=', 'orders.lkp_load_type_id');
		} else {			                        
			$query->leftJoin('trucklease_seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
			$query->leftJoin('trucklease_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
            $query->leftJoin('lkp_trucklease_lease_terms as tlt', 'tlt.id', '=', 'spi.lkp_trucklease_lease_term_id');			
		}
		$query->where('orders.buyer_id', '=', Auth::user()->id);
		$query->where('orders.lkp_service_id', '=', $serviceId);
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}
		$query->groupBy('orders.id');
		if($serviceId == ROAD_TRUCK_HAUL) {
			$query->select('sp.tracking','oi.total_amt as inv_total','orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.city_name as from_city', 'tc.city_name as to_city','u.username','lvt.vehicle_type','llt.load_type','spi.vehicle_number');
		} else {
			$query->select('sp.tracking','oi.total_amt as inv_total','orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.city_name as from_city',  'tc.city_name as to_city', 'u.username','tlt.lease_term as leaseTerm','spi.driver_availability', 'pm.payment_mode as paymentmethod', 'spi.vehicle_make_model_year' );
		}		
		//Filters values to populate in the page		
		$from_locations[''] = "From Location";
		$to_locations['']	= "To Location";        
        $buyers[''] 		= "Seller";        
        // Getting From Locations based on result set	
        $result = $query->get();	
        //echo "<pre>"; print_r($result); die;
		foreach($result as $getData):
			$from_locations[$getData->from_city_id] 		= $getData->from_city;
			$to_locations[$getData->to_city_id] 			= $getData->to_city;
			$buyers[$getData->username] 					= $getData->username;				
		endforeach;

		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations 	= CommonComponent::orderArray($to_locations);
		$buyers 		= CommonComponent::orderArray($buyers);		

		// Filters Start
		$filter = \DataFilter::source($query);
		// From locations dropdown
		$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   		});
		// To locations dropdown
		$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   		});
		// Seller Name dropdown
   		$filter->add('username', '', 'select')->options($buyers)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});
   		// Vehicle number filter	
   		$filter->add('vehicle_number', 'Vehicle No', 'text')->attr("class", "top-text-fld form-control1")->attr("onchange", "this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('spi.vehicle_number', $value):$query;
       	});
   		// Order number filter	
   		$filter->add('order_no', 'Order No', 'text')->attr("class", "top-text-fld form-control1")->attr("onchange", "this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('orders.order_no', $value):$query;
       	});
   		// Dispatch date
		$filter->add('start_dispatch_date', 'From Date', 'text')->attr(["class" => "dateRange", "onchange"=>"this.form.submit()"])->scope( function ($query, $value) {        	
        	if(!empty($value)):        		
        		return $query->where ( 'orders.dispatch_date', '>=', CommonComponent::convertDateForDatabase($value) );			
        	else:
        		return $query;
        	endif;
   		});
		// delivery_date 
		$filter->add('end_dispatch_date', 'To Date', 'text')->attr(["class" => "dateRange","onchange"=>"this.form.submit()"])->scope( function ($query, $value) {
        	if(!empty($value)):
        		return $query->where ( 'orders.delivery_date', '>=', CommonComponent::convertDateForDatabase($value) );        		
        	else:
        		return $query;
        	endif;
   		});

		$filter->build();
		$grid = \DataGrid::source($query);		
		// Grid Headings
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-3 padding-left-none')); 
	   	$grid->add('username','Vendor Name', true)->attributes(array("class" => 'col-md-3 padding-left-none'));
	   	if(Session::get('service_id') == ROAD_TRUCK_HAUL) {
	   		$grid->add('from_city','From', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	} else {
	   		$grid->add('from_city','Location', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	}
	   	
	   	//Check conditon for truck haul or truck lease no need to location for TLease
	   	if(Session::get('service_id') == ROAD_TRUCK_HAUL) {
	   		$grid->add('to_city','To', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
	   	} else {
	   		$grid->add('to_city','', false)->attributes(array("class" => 'col-md-2 padding-left-none'));
	   	}
	   	
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");
		//Grid data append for columns
		$grid->row( function($row) {
			
			$row->cells[0]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->order_no.'</a>');			
			$row->cells[1]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->username.'</a>');			
			$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->from_city.'</a>');			
			if(Session::get('service_id') == ROAD_TRUCK_HAUL) {
				$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->to_city.'</a>');
			} else {
				$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('');
			}
			
			$tracking_status = CommonComponent::getTrackingType($row->data->tracking);
            if($row->data->lkp_order_type_id==TERM)
                $tracking_status = 'N/A'; 

			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-left-none text-right'));
			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_buyer = CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);
			$order_id 	= $row->data->id;
			$seller_id  = $row->data->seller_id;
			
        	if($row->data->inv_total!=0)
                $order_price = CommonComponent::moneyFormat($row->data->inv_total);
            else
                $order_price = CommonComponent::moneyFormat($row->data->price);
            //This condition only for truck lease 
			if(Session::get('service_id') != ROAD_TRUCK_HAUL) {
				if($row->data->leaseTerm!='')
					$lease_term = $row->data->leaseTerm;
				else
					$lease_term = 'N/A';

	            if($row->data->driver_availability == 1)
	            	$driverAv= ' With Driver ';
	            else
	            	$driverAv= ' Without Driver ';

	            if($row->data->paymentmethod == 'Advance')
	            	$paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
	            else
	            	$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$row->data->paymentmethod;
			}	

			//Getting status bar colors progress bar
            $StatusBar = BuyerOrdersComponent :: getStatusProgressbarColor ($row->data->seller_pickup_date, $row->data->dispatch_date, $row->data->seller_delivery_date, $row->data->delivery_date,$row->data->buyer_consignment_pick_up_date); 

			$checkstatus = $row->data->order_status;
            if($checkstatus == 'Pickup due') {
                $statusText = "Placement Due";
            } elseif($checkstatus == 'Consignment pickup') {
                $statusText = "Placed";
            } elseif($checkstatus == 'Delivered'){
               $statusText = "Reported";
            }else{
                $statusText = "Pending";
            }		

			$row->cells[5]->value = '
			<div class="col-md-2 padding-none status-block">
				<div class="status-bar">
					<div class="status-bar">
						'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'       		
						<span class="status-text">'.$statusText.'</span>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
        	<div class="col-md-10 padding-none pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_buyer).'</span></a>
				</div>
			</div>
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$seller_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 tab-modal-head">
                    <h3>';
						if(Session::get('service_id') == ROAD_TRUCK_HAUL) {
							$row->cells[5]->value .= '
							<i class="fa fa-map-marker"></i> '.$row->data->from_city.' to '.$row->data->to_city.'';
						} else {
							$row->cells[5]->value .= '
							<i class="fa fa-map-marker"></i> '.$row->data->from_city.'';
						}
                        $row->cells[5]->value .= '
                        <span class="close-icon">x</span>
                    </h3>
                </div>
                <div class="clearfix"></div>  
                <div class="col-md-8 data-div break-word">              
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Reporting Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>				
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Reporter Name</span>
					<span class="data-value">'.$row->data->buyer_consignor_name.'</span>
				</div>';
				if(Session::get('service_id') == ROAD_TRUCK_HAUL)  {
					$row->cells[5]->value .= '
					<div class="col-md-4 padding-left-none data-fld">
		                <span class="data-head">Vehicle Type</span>
		                <span class="data-value">'.$row->data->vehicle_type.'</span>
	                </div>
	                <div class="clearfix"></div>
	                <div class="col-md-4 padding-left-none data-fld">
		                <span class="data-head">Load Type</span>
		                <span class="data-value">'.$row->data->load_type.'</span>
	                </div>
	                <div class="col-md-4 padding-left-none data-fld">
		                <span class="data-head">Tracking</span>
		                <span class="data-value">'.$tracking_status.'</span>
	                </div>';
				}
				if(Session::get('service_id') == ROAD_TRUCK_LEASE)  {
					$row->cells[5]->value .= '					
	                <div class="col-md-4 padding-left-none data-fld">
		                <span class="data-head">Lease Term</span>
		                <span class="data-value">'.$lease_term.'</span>
	                </div>
	                <div class="col-md-4 padding-left-none data-fld">
		                <span class="data-head">Driver</span>
		                <span class="data-value">'.$driverAv.'</span>
	                </div>
	                <div class="col-md-4 padding-left-none data-fld">
		                <span class="data-head">Payment</span>
		                <span class="data-value">'.$paymentType.'</span>
	                </div>
	                <div class="col-md-4 padding-left-none data-fld">
		                <span class="data-head">Documents</span>
		                <span class="data-value">0</span>
	                </div>
	                <div class="col-md-4 padding-left-none data-fld">
		                <span class="data-head">Vehicle Make & Model & Year</span>
		                <span class="data-value">'.$row->data->vehicle_make_model_year.'</span>
	                </div>';
				}
				$row->cells[5]->value .= '		               
                </div>                
                <div class="col-md-4">
                    <span class="data-head">Total Price</span>
                    <span class="data-value big-value">'.$order_price.' /-</span>
                </div>
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}

/* LTL + 4 service Orders list new component start
*  @srinivas dantha
*  Date : 12 th July,2016
*/
	public static function getLtlBuyerOrdersList() {

        $serviceId = Session::get('service_id');
        $query = DB::table('orders');
		$query->leftJoin('order_payments as op', 'op.id', '=', 'orders.order_payment_id');
		$query->leftJoin('order_invoices as oi', 'oi.order_id', '=', 'orders.id');
		//conditions for locations and package types with differnt tables in LTl +4 services
		if ($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC) {
			$query->leftJoin('lkp_ptl_pincodes as fc', 'fc.id', '=', 'orders.from_city_id');
			$query->leftJoin('lkp_ptl_pincodes as tc', 'tc.id', '=', 'orders.to_city_id');			
		} elseif ($serviceId == AIR_INTERNATIONAL) {
			$query->leftJoin('lkp_airports as fc', 'fc.id', '=', 'orders.from_city_id');
			$query->leftJoin('lkp_airports as tc', 'tc.id', '=', 'orders.to_city_id');
		} elseif ($serviceId == OCEAN) {
			$query->leftJoin('lkp_seaports as fc', 'fc.id', '=', 'orders.from_city_id');
			$query->leftJoin('lkp_seaports as tc', 'tc.id', '=', 'orders.to_city_id');
		} else {
			$query->leftJoin('lkp_ptl_pincodes as fc', 'fc.id', '=', 'orders.from_city_id');
			$query->leftJoin('lkp_ptl_pincodes as tc', 'tc.id', '=', 'orders.to_city_id');	
		}
		//Check conditions for seller getting data tables
		if ($serviceId == ROAD_PTL) {
			$query->leftJoin('ptl_seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
			$query->leftJoin('ptl_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
		} elseif ($serviceId == RAIL) {
			$query->leftJoin('rail_seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
			$query->leftJoin('rail_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
		} elseif ($serviceId == AIR_DOMESTIC) {
			$query->leftJoin('airdom_seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
			$query->leftJoin('airdom_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
		} elseif ($serviceId == AIR_INTERNATIONAL) {
			$query->leftJoin('airint_seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
			$query->leftJoin('airint_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
		} elseif ($serviceId == OCEAN) {
			$query->leftJoin('ocean_seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
			$query->leftJoin('ocean_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
		} else {
			$query->leftJoin('ptl_seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
			$query->leftJoin('ptl_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
		}		
		$query->leftJoin('users as u', 'u.id', '=', 'orders.seller_id');	
		$query->leftJoin('lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id');
		$query->leftJoin('lkp_packaging_types as lpt', 'lpt.id', '=', 'orders.lkp_packaging_type_id');
		$query->leftJoin('lkp_load_types as llt', 'llt.id', '=', 'orders.lkp_load_type_id');
		$query->leftJoin('lkp_order_statuses as os', 'os.id', '=', 'orders.lkp_order_status_id');
		$query->where('orders.buyer_id', '=', Auth::user()->id);
		$query->where('orders.lkp_service_id', '=', $serviceId);
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}
		$query->groupBy('orders.id');
		//Getting Results from query depends on conditions
		if ($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC) {
			$query->select('sp.tracking','oi.total_amt as inv_total','orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.postoffice_name as from_city', 'tc.postoffice_name as to_city','u.username','llt.load_type', 'lpt.packaging_type_name');
		} elseif ($serviceId == AIR_INTERNATIONAL) {
			$query->select('sp.tracking','oi.total_amt as inv_total','orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.airport_name as from_city', 'tc.airport_name as to_city','u.username','llt.load_type', 'lpt.packaging_type_name');
		} elseif ($serviceId == OCEAN) {
			$query->select('sp.tracking','oi.total_amt as inv_total','orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.seaport_name as from_city', 'tc.seaport_name as to_city','u.username','llt.load_type', 'lpt.packaging_type_name');
		} else {
			$query->select('sp.tracking','oi.total_amt as inv_total','orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.postoffice_name as from_city', 'tc.postoffice_name as to_city','u.username','llt.load_type', 'lpt.packaging_type_name');
		}		

		//Filters values to populate in the page		
		$from_locations[''] = "From Location";
		$to_locations['']	= "To Location";        
        $buyers[''] 		= "Seller";
        $consignee['']		= "Consignee";       
        $result = $query->get();	
        //echo "<pre>"; print_r($result); die;
		foreach($result as $getData):
			$from_locations[$getData->from_city_id] 		= $getData->from_city;
			$to_locations[$getData->to_city_id] 			= $getData->to_city;
			$buyers[$getData->username] 					= $getData->username;
			$consignee[$getData->buyer_consignee_name] 		= $getData->buyer_consignee_name;	
		endforeach;

		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations 	= CommonComponent::orderArray($to_locations);
		$buyers 		= CommonComponent::orderArray($buyers);
		$consignee 		= CommonComponent::orderArray($consignee);

		// Filters Start
		$filter = \DataFilter::source($query);
		// From locations dropdown
		$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   		});
		// To locations dropdown
		$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   		});
		// Seller Name dropdown
   		$filter->add('username', '', 'select')->options($buyers)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});
   		// consignee Name dropdown
   		$filter->add('buyer_consignee_name', '', 'select')->options($consignee)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	
        	return ($value!='')? $query->where('orders.buyer_consignee_name', $value):$query;
   		});
   		$filter->add('order_no', 'Order No', 'text')->attr("class", "top-text-fld form-control1")->attr("onchange", "this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('orders.order_no', $value):$query;
       	});
   		// Dispatch date
		$filter->add('start_dispatch_date', 'From Date', 'text')->attr(["class" => "dateRange", "onchange"=>"this.form.submit()"])->scope( function ($query, $value) {        	
        	if(!empty($value)):        		
        		return $query->where ( 'orders.dispatch_date', '>=', CommonComponent::convertDateForDatabase($value) );			
        	else:
        		return $query;
        	endif;
   		});
		// delivery_date 
		$filter->add('end_dispatch_date', 'To Date', 'text')->attr(["class" => "dateRange","onchange"=>"this.form.submit()"])->scope( function ($query, $value) {
        	if(!empty($value)):
        		return $query->where ( 'orders.delivery_date', '>=', CommonComponent::convertDateForDatabase($value) );        		
        	else:
        		return $query;
        	endif;
   		});

		$filter->build();
		$grid = \DataGrid::source($query);		
		// Grid Headings
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-3 padding-left-none')); 
	   	$grid->add('username','Vendor Name', true)->attributes(array("class" => 'col-md-3 padding-left-none')); 
	   	$grid->add('from_city','From', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('to_city','To', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");
		//Grid data append for columns
		$grid->row( function($row) {
			
			$row->cells[0]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->order_no.'</a>');			
			$row->cells[1]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->username.'</a>');			
			$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->from_city.'</a>');			
			$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->to_city.'</a>');
			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-left-none text-right'));
			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_buyer = CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);
			$order_id 	= $row->data->id;
			$seller_id  = $row->data->seller_id;
			$tracking_status = CommonComponent::getTrackingType($row->data->tracking);
            if($row->data->lkp_order_type_id==TERM)
                $tracking_status = 'N/A'; 

        	if($row->data->inv_total!=0)
                $order_price = CommonComponent::moneyFormat($row->data->inv_total);
            else
                $order_price = CommonComponent::moneyFormat($row->data->price);

            if ($row->data->packaging_type_name!='')
            	$packageType = $row->data->packaging_type_name;
            else
            	$packageType = 'N/A';

            //Getting status bar colors progress bar
            $StatusBar = BuyerOrdersComponent :: getStatusProgressbarColor ($row->data->seller_pickup_date, $row->data->dispatch_date, $row->data->seller_delivery_date, $row->data->delivery_date);            
            $status = $row->data->order_status;
            if($status!='')
            	$statusText = $row->data->order_status;
            else
            	$statusText = '';


			$row->cells[5]->value = '
			<div class="col-md-2 padding-none status-block">
				<div class="status-bar">
					<div class="status-bar">
						'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'	
						<span class="status-text">'.$statusText.'</span>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
        	<div class="col-md-10 padding-none pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_buyer).'</span></a>
				</div>
			</div>
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$seller_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 tab-modal-head">
                    <h3>
                        <i class="fa fa-map-marker"></i> '.$row->data->from_city.' to '.$row->data->to_city.'
                        <span class="close-icon">x</span>
                    </h3>
                </div>
                <div class="clearfix"></div>  
                <div class="col-md-8 data-div break-word">              
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Dispatch Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>				
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Consignee</span>
					<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
				</div>	
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Packaging Type</span>
					<span class="data-value">'.$packageType.'</span>
				</div>				
                <div class="clearfix"></div>
                <div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Load Type</span>
	                <span class="data-value">'.$row->data->load_type.'</span>
                </div>
                <div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Tracking</span>
	                <span class="data-value">'.$tracking_status.'</span>
                </div>                
                </div>                
                <div class="col-md-4">
                    <span class="data-head">Total Price</span>
                    <span class="data-value big-value">'.$order_price.' /-</span>
                </div>
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}

/* Courier Buyer Orders list new component start
*  @srinivas dantha
*  Date : 13 th July,2016
*/
	public static function getCourierBuyerOrdersList() {

        $serviceId = Session::get('service_id');
        $query = DB::table('orders');
		$query->leftJoin('order_payments as op', 'op.id', '=', 'orders.order_payment_id');
		$query->leftJoin('order_invoices as oi', 'oi.order_id', '=', 'orders.id');
		$query->leftJoin('lkp_ptl_pincodes as fc', 'fc.id', '=', 'orders.from_city_id');
		$query->leftJoin('lkp_ptl_pincodes as tc', 'tc.id', '=', 'orders.to_city_id');
		$query->leftJoin('users as u', 'u.id', '=', 'orders.seller_id');
		//Check condition for buyer term and spot order details
		$query->leftJoin('courier_buyer_quotes as adsp', function($join)
		{
			$join->on('orders.buyer_quote_id', '=', 'adsp.id');
			$join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));			 
		});
		$query->leftJoin('term_buyer_quote_items as tbqi', function($join)
		{	
			$join->on('orders.buyer_quote_item_id', '=', 'tbqi.id');
			$join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));		
		});
		$query->leftJoin('term_buyer_quotes as tbq', function($join)
		{	
			$join->on('tbq.id', '=', 'tbqi.term_buyer_quote_id');
			$join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));			 
		});
		$query->leftJoin('courier_buyer_quote_items as adspi', function($join)
		{
			$join->on('adsp.id', '=', 'adspi.buyer_quote_id');
			$join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));			 
		});
		$query->leftJoin('courier_seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
		$query->leftJoin('courier_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
		$query->leftJoin('lkp_order_statuses as os', 'os.id', '=', 'orders.lkp_order_status_id');	
		$query->where('orders.buyer_id', '=', Auth::user()->id);
		$query->where('orders.lkp_service_id', '=', $serviceId);
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}
		$query->whereRaw("(case when `orders`.`lkp_order_type_id` = 1 then adspi.lkp_courier_delivery_type_id=".Session::get('delivery_type')." when `orders`.`lkp_order_type_id` = 2 then tbq.lkp_courier_delivery_type_id=".Session::get('delivery_type')." end)");
		$query->groupBy('orders.id');			
			
		$query->select('sp.tracking','oi.total_amt as inv_total','orders.*',DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then adspi.lkp_courier_delivery_type_id  when `orders`.`lkp_order_type_id` = 2 then tbq.lkp_courier_delivery_type_id end) as lkp_courier_delivery_type_id"),DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then adspi.lkp_courier_type_id  when `orders`.`lkp_order_type_id` = 2 then tbq.lkp_courier_type_id end) as lkp_courier_type_id"), 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.postoffice_name as from_city', 'tc.postoffice_name as to_city','u.username');		
		//Filters values to populate in the page		
		$from_locations[''] = "From Location";
		$to_locations['']	= "To Location";        
        $buyers[''] 		= "Seller";
        $consignee['']		= "Consignee";
        // Getting From Locations based on result set	
        $result = $query->get();	
        //echo "<pre>"; print_r($result); die;
		foreach($result as $getData):
			$from_locations[$getData->from_city_id] 		= $getData->from_city;
			if(isset($_REQUEST['delivery_type']) && $_REQUEST['delivery_type'] == 1) {
				$to_locations[$getData->to_city_id] 			= $getData->to_city;
			} else {
				$to_locations[$getData->to_city_id] = DB::table('lkp_countries')->where('id', $getData->to_city_id)->pluck('country_name');
			}
			$buyers[$getData->username] 					= $getData->username;
			$consignee[$getData->buyer_consignee_name] 		= $getData->buyer_consignee_name;	
		endforeach;

		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations 	= CommonComponent::orderArray($to_locations);
		$buyers 		= CommonComponent::orderArray($buyers);
		$consignee 		= CommonComponent::orderArray($consignee);
		// Filters Start
		$filter = \DataFilter::source($query);
		
		$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   		});		
		$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   		});		
   		$filter->add('username', '', 'select')->options($buyers)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});   		
   		$filter->add('buyer_consignee_name', '', 'select')->options($consignee)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	
        	return ($value!='')? $query->where('orders.buyer_consignee_name', $value):$query;
   		});
   		$filter->add('order_no', 'Order No', 'text')->attr("class", "top-text-fld form-control1")->attr("onchange", "this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('orders.order_no', $value):$query;
       	});   		
		$filter->add('start_dispatch_date', 'From Date', 'text')->attr(["class" => "dateRange", "onchange"=>"this.form.submit()"])->scope( function ($query, $value) {        	
        	if(!empty($value)):        		
        		return $query->where ( 'orders.dispatch_date', '>=', CommonComponent::convertDateForDatabase($value) );			
        	else:
        		return $query;
        	endif;
   		});		
		$filter->add('end_dispatch_date', 'To Date', 'text')->attr(["class" => "dateRange","onchange"=>"this.form.submit()"])->scope( function ($query, $value) {
        	if(!empty($value)):
        		return $query->where ( 'orders.delivery_date', '>=', CommonComponent::convertDateForDatabase($value) );        		
        	else:
        		return $query;
        	endif;
   		});

		$filter->build();
		$grid = \DataGrid::source($query);		
		// Grid Headings
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-3 padding-left-none')); 
	   	$grid->add('username','Vendor Name', true)->attributes(array("class" => 'col-md-3 padding-left-none')); 
	   	$grid->add('from_city','From', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('to_city','To', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");
		//Grid data append for columns
		$grid->row( function($row) {
			
			$order_id 	= $row->data->id;
			$seller_id  = $row->data->seller_id;
			//append to city for grid
			if($row->data->lkp_courier_delivery_type_id == COURIER_DOMESTIC_DELIVERY) {
            	$courierDeliveryType = 'Domestic';
            	$to_city = $row->data->to_city;
            } else {
            	$courierDeliveryType = 'International';
            	$order_id_for_country = DB::table ( 'orders' )->where ( 'id', $order_id )->select('to_city_id')->first();
            	$to_city = DB::table ( 'lkp_countries' )->where ( 'id', $order_id_for_country->to_city_id )->pluck ( 'country_name' );            	
            }	

			// Order No
			$row->cells[0]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->order_no.'</a>');
			// User Name
			$row->cells[1]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->username.'</a>');
			// From city
			$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->from_city.'</a>');
			// To city
			$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$to_city.'</a>');

			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-left-none text-right'));

			


			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_buyer = CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);			
			$tracking_status = CommonComponent::getTrackingType($row->data->tracking);
            if($row->data->lkp_order_type_id==TERM)
                $tracking_status = 'N/A';
            		
            if($row->data->lkp_courier_type_id == COURIER_TYPE_DOCS)
            	$courierType = 'Document';
            else
            	$courierType = 'Parcel';

        	if($row->data->inv_total!=0)
                $order_price = CommonComponent::moneyFormat($row->data->inv_total);
            else
                $order_price = CommonComponent::moneyFormat($row->data->price);

            //Getting status bar colors progress bar
            $StatusBar = BuyerOrdersComponent :: getStatusProgressbarColor ($row->data->seller_pickup_date, $row->data->dispatch_date, $row->data->seller_delivery_date, $row->data->delivery_date);            
            $status = $row->data->order_status;
            if($status!='')
            	$statusText = $row->data->order_status;
            else
            	$statusText = '';

			$row->cells[5]->value = '
			<div class="col-md-2 padding-none status-block">
				<div class="status-bar">
					<div class="status-bar">
						'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'       		
						<span class="status-text">'.$statusText.'</span>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
        	<div class="col-md-10 padding-none pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_buyer).'</span></a>
				</div>
			</div>
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$seller_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 tab-modal-head">
                    <h3>
                        <i class="fa fa-map-marker"></i> '.$row->data->from_city.' to '.$to_city.'
                        <span class="close-icon">x</span>
                    </h3>
                </div>
                <div class="clearfix"></div>  
                <div class="col-md-8 data-div break-word">              
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Dispatch Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>				
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Consignee</span>
					<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
				</div>	
				<div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Courier Delivery Type</span>
	                <span class="data-value">'.$courierDeliveryType.'</span>
                </div>                
                <div class="clearfix"></div>
                <div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Courier Type</span>
	                <span class="data-value">'.$courierType.'</span>
                </div>                 
                <div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Tracking</span>
	                <span class="data-value">'.$tracking_status.'</span>
                </div>
                </div>                
                <div class="col-md-4">
                    <span class="data-head">Total Price</span>
                    <span class="data-value big-value">'.$order_price.' /-</span>
                </div>
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}

/*
* Relcoation Pet move and domestic move service orders starts
* @srinivas dantha
* Date : july 13th,2016
*
*/

public static function getRelocDomPetBuyerOrdersList() {

        $serviceId = Session::get('service_id');
        $query = DB::table('orders');
		$query->leftJoin('order_payments as op', 'op.id', '=', 'orders.order_payment_id');
		$query->leftJoin('order_invoices as oi', 'oi.order_id', '=', 'orders.id');
		$query->leftJoin('lkp_cities as fc', 'fc.id', '=', 'orders.from_city_id');
		$query->leftJoin('lkp_cities as tc', 'tc.id', '=', 'orders.to_city_id');
		$query->leftJoin('users as u', 'u.id', '=', 'orders.seller_id');
		//Check below condtions for rel dom or pet move
		if ($serviceId == RELOCATION_DOMESTIC) {
			$query->leftJoin('relocation_seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
			$query->leftJoin('relocation_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');                         
		} elseif ($serviceId == RELOCATION_PET_MOVE) {
			$query->leftJoin('relocationpet_seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
			$query->leftJoin('relocationpet_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');                                
            $query->leftJoin('lkp_pet_types as lkpt', 'lkpt.id', '=', 'spi.lkp_pet_type_id');  
            $query->leftJoin('lkp_cage_types as lkct', 'lkpt.id', '=', 'spi.lkp_cage_type_id'); 
		}
		$query->leftJoin('lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id');
		$query->leftJoin('lkp_order_statuses as os', 'os.id', '=', 'orders.lkp_order_status_id');
		$query->leftJoin ('lkp_payment_modes as pm', 'pm.id', '=', 'op.lkp_payment_mode_id' );
		$query->where('orders.buyer_id', '=', Auth::user()->id);
		$query->where('orders.lkp_service_id', '=', $serviceId);
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}
		$query->groupBy('orders.id');
		if ($serviceId == RELOCATION_DOMESTIC) {
			$query->select('sp.tracking','oi.total_amt as inv_total','orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.city_name as from_city', 'tc.city_name as to_city','u.username');
		} elseif ($serviceId == RELOCATION_PET_MOVE) {
			$query->select('sp.tracking','oi.total_amt as inv_total','orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.city_name as from_city', 'tc.city_name as to_city', 'u.username', 'pm.payment_mode as paymentmethod','lkpt.pet_type', 'lkct.cage_type');
		}
		//Filters values to populate in the page		
		$from_locations[''] = "From Location";
		$to_locations['']	= "To Location";        
        $buyers[''] 		= "Seller";
        $consignee['']		= "Consignee";
        // Getting From Locations based on result set	
        $result = $query->get();	
		foreach($result as $getData):
			$from_locations[$getData->from_city_id] 		= $getData->from_city;
			$to_locations[$getData->to_city_id] 			= $getData->to_city;
			$buyers[$getData->username] 					= $getData->username;
			$consignee[$getData->buyer_consignee_name] 		= $getData->buyer_consignee_name;	
		endforeach;

		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations 	= CommonComponent::orderArray($to_locations);
		$buyers 		= CommonComponent::orderArray($buyers);
		$consignee 		= CommonComponent::orderArray($consignee);

		// Filters Start
		$filter = \DataFilter::source($query);

		// From locations dropdown
		$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   		});
		// To locations dropdown
		$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   		});
		// Seller Name dropdown
   		$filter->add('username', '', 'select')->options($buyers)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});
   		// consignee Name dropdown
   		$filter->add('buyer_consignee_name', '', 'select')->options($consignee)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	
        	return ($value!='')? $query->where('orders.buyer_consignee_name', $value):$query;
   		});
   		$filter->add('order_no', 'Order No', 'text')->attr("class", "top-text-fld form-control1")->attr("onchange", "this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('orders.order_no', $value):$query;
       	});
   		// Dispatch date
		$filter->add('start_dispatch_date', 'From Date', 'text')->attr(["class" => "dateRange", "onchange"=>"this.form.submit()"])->scope( function ($query, $value) {        	
        	if(!empty($value)):        		
        		return $query->where ( 'orders.dispatch_date', '>=', CommonComponent::convertDateForDatabase($value) );			
        	else:
        		return $query;
        	endif;
   		});

		// delivery_date 
		$filter->add('end_dispatch_date', 'To Date', 'text')->attr(["class" => "dateRange","onchange"=>"this.form.submit()"])->scope( function ($query, $value) {
        	if(!empty($value)):
        		return $query->where ( 'orders.delivery_date', '>=', CommonComponent::convertDateForDatabase($value) );        		
        	else:
        		return $query;
        	endif;
   		});

		$filter->build();
		$grid = \DataGrid::source($query);		
		// Grid Headings
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-3 padding-left-none')); 
	   	$grid->add('username','Vendor Name', true)->attributes(array("class" => 'col-md-3 padding-left-none')); 
	   	$grid->add('from_city','From', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('to_city','To', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");
		//Grid data append for columns
		$grid->row( function($row) {
			// Order No
			$row->cells[0]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->order_no.'</a>');
			// User Name
			$row->cells[1]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->username.'</a>');
			// From city
			$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->from_city.'</a>');
			// To city
			$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->to_city.'</a>');

			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-left-none text-right'));

			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_buyer = CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);
			$order_id 	= $row->data->id;
			$seller_id  = $row->data->seller_id;
			$tracking_status = CommonComponent::getTrackingType($row->data->tracking);
            if($row->data->lkp_order_type_id==TERM)
                $tracking_status = 'N/A'; 

			if (Session::get('service_id') == RELOCATION_PET_MOVE) {
				$deliveryDate = CommonComponent::checkAndGetDate($row->data->delivery_date);
				if($deliveryDate!='')
					$delDate = $deliveryDate;
				else
					$delDate  = 'N/A';

				if($row->data->cage_type!='')
					$cageType = $row->data->cage_type;
				else
					$cageType = 'N/A';

				if($row->data->paymentmethod == 'Advance')
	            	$paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
	            else
	            	$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$row->data->paymentmethod;
			}

        	if($row->data->inv_total!=0)
                $order_price = CommonComponent::moneyFormat($row->data->inv_total);
            else
                $order_price = CommonComponent::moneyFormat($row->data->price);

            //Getting status bar colors progress bar
            $StatusBar = BuyerOrdersComponent :: getStatusProgressbarColor ($row->data->seller_pickup_date, $row->data->dispatch_date, $row->data->seller_delivery_date, $row->data->delivery_date);            
            $status = $row->data->order_status;
            if($status!='')
            	$statusText = $row->data->order_status;
            else
            	$statusText = '';


			$row->cells[5]->value .= '
			<div class="col-md-2 padding-none status-block">
				<div class="status-bar">
					<div class="status-bar">
						'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'       		
						<span class="status-text">'.$statusText.'</span>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
        	<div class="col-md-10 padding-none pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_buyer).'</span></a>
				</div>
			</div>
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$seller_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 tab-modal-head">
                    <h3>
                        <i class="fa fa-map-marker"></i> '.$row->data->from_city.' to '.$row->data->to_city.'
                        <span class="close-icon">x</span>
                    </h3>
                </div>
                <div class="clearfix"></div>  
                <div class="col-md-8 data-div break-word">';
            	if (Session::get('service_id') == RELOCATION_DOMESTIC) {
            	$row->cells[5]->value .= '
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Dispatch Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>				
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Consignee</span>
					<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
				</div>';
            	} elseif (Session::get('service_id') == RELOCATION_PET_MOVE) {
            		$row->cells[5]->value .= '
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Dispatch Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>	
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Delivery Date</span>
					<span class="data-value">'.$delDate.'</span>
				</div>				
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Consignee</span>
					<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
				</div>
				<div class="clearfix"></div>
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Pet Type</span>
					<span class="data-value">'.$row->data->pet_type.'</span>
				</div>
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Cage Type</span>
					<span class="data-value">'.$cageType.'</span>
				</div>
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Payment Type</span>
					<span class="data-value">'.$paymentType.'</span>
				</div>      
				';
            	}
				$row->cells[5]->value .= '
                <div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Tracking</span>
	                <span class="data-value">'.$tracking_status.'</span>
                </div>                
                <div class="clearfix"></div>                
                </div>                
                <div class="col-md-4">
                    <span class="data-head">Total Price</span>
                    <span class="data-value big-value">'.$order_price.' /-</span>
                </div>
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}

/*
* Relcoation International service orders starts
* @srinivas dantha
* Date : july 13th,2016
*
*/

	public static function getRelocIntBuyerOrdersList() {

        $serviceId = Session::get('service_id');
        $int_type = 1;
        $query = DB::table('orders');
		$query->leftJoin('order_payments as op', 'op.id', '=', 'orders.order_payment_id');
		$query->leftJoin('order_invoices as oi', 'oi.order_id', '=', 'orders.id');
		$query->leftJoin('lkp_cities as fc', 'fc.id', '=', 'orders.from_city_id');
		$query->leftJoin('lkp_cities as tc', 'tc.id', '=', 'orders.to_city_id');
		$query->leftJoin('users as u', 'u.id', '=', 'orders.seller_id');
		$query->leftJoin('relocationint_seller_posts as sp', 'sp.id', '=', 'orders.seller_post_item_id');
        $query->leftJoin ('lkp_payment_modes as pm', 'pm.id', '=', 'op.lkp_payment_mode_id' );
        $query->leftJoin('relocationint_buyer_posts as ribp', 'ribp.id', '=', 'orders.buyer_quote_id');		
		$query->leftJoin('lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id');
		$query->leftJoin('lkp_order_statuses as os', 'os.id', '=', 'orders.lkp_order_status_id');	
		$query->where('orders.buyer_id', '=', Auth::user()->id);
		$query->where('orders.lkp_service_id', '=', $serviceId);
		if (isset ( $_REQUEST ['order_int_type'] ) && $_REQUEST ['order_int_type'] != '') {
				$int_type = $_REQUEST['order_int_type'];
			}					
		$query->where ( 'orders.lkp_international_type_id', '=', $int_type );
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}
		$query->groupBy('orders.id');
		$query->select('sp.tracking','oi.total_amt as inv_total','orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.city_name as from_city', 'tc.city_name as to_city','u.username', 'orders.lkp_international_type_id as InternationalTypeId', 'pm.payment_mode as paymentmethod', 'ribp.total_cartons_weight')->get();		
		//Filters values to populate in the page		
		$from_locations[''] = "From Location";
		$to_locations['']	= "To Location";        
        $buyers[''] 		= "Seller";
        $consignee['']		= "Consignee";
        // Getting From Locations based on result set	
        $result = $query->get();	
		foreach($result as $getData):
			$from_locations[$getData->from_city_id] 		= $getData->from_city;
			$to_locations[$getData->to_city_id] 			= $getData->to_city;
			$buyers[$getData->username] 					= $getData->username;
			$consignee[$getData->buyer_consignee_name] 		= $getData->buyer_consignee_name;	
		endforeach;

		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations 	= CommonComponent::orderArray($to_locations);
		$buyers 		= CommonComponent::orderArray($buyers);
		$consignee 		= CommonComponent::orderArray($consignee);

		// Filters Start
		$filter = \DataFilter::source($query);

		// From locations dropdown
		$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   		});
		// To locations dropdown
		$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   		});
		// Seller Name dropdown
   		$filter->add('username', '', 'select')->options($buyers)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});
   		// consignee Name dropdown
   		$filter->add('buyer_consignee_name', '', 'select')->options($consignee)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	
        	return ($value!='')? $query->where('orders.buyer_consignee_name', $value):$query;
   		});
   		$filter->add('order_no', 'Order No', 'text')->attr("class", "top-text-fld form-control1")->attr("onchange", "this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('orders.order_no', $value):$query;
       	});
   		// Dispatch date
		$filter->add('start_dispatch_date', 'From Date', 'text')->attr(["class" => "dateRange", "onchange"=>"this.form.submit()"])->scope( function ($query, $value) {        	
        	if(!empty($value)):        		
        		return $query->where ( 'orders.dispatch_date', '>=', CommonComponent::convertDateForDatabase($value) );			
        	else:
        		return $query;
        	endif;
   		});

		// delivery_date 
		$filter->add('end_dispatch_date', 'To Date', 'text')->attr(["class" => "dateRange","onchange"=>"this.form.submit()"])->scope( function ($query, $value) {
        	if(!empty($value)):
        		return $query->where ( 'orders.delivery_date', '>=', CommonComponent::convertDateForDatabase($value) );        		
        	else:
        		return $query;
        	endif;
   		});

		$filter->build();
		$grid = \DataGrid::source($query);		
		// Grid Headings
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-3 padding-left-none')); 
	   	$grid->add('username','Vendor Name', true)->attributes(array("class" => 'col-md-3 padding-left-none')); 
	   	$grid->add('from_city','From', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('to_city','To', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");
		//Grid data append for columns
		$grid->row( function($row) {
			// Order No
			$row->cells[0]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->order_no.'</a>');
			// User Name
			$row->cells[1]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->username.'</a>');
			// From city
			$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->from_city.'</a>');
			// To city
			$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->to_city.'</a>');

			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-left-none text-right'));

			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_buyer = CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);
			$order_id 	= $row->data->id;
			$seller_id  = $row->data->seller_id;
			$tracking_status = CommonComponent::getTrackingType($row->data->tracking);
            if($row->data->lkp_order_type_id==TERM)
                $tracking_status = 'N/A'; 

            $deliveryDate = CommonComponent::checkAndGetDate($row->data->delivery_date);
				if($deliveryDate!='')
					$delDate = $deliveryDate;
				else
					$delDate  = 'N/A';

        	if($row->data->inv_total!=0)
                $order_price = CommonComponent::moneyFormat($row->data->inv_total);
            else
                $order_price = CommonComponent::moneyFormat($row->data->price);

            if($row->data->paymentmethod == 'Advance')
	            	$paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
	            else
	            	$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$row->data->paymentmethod;

            $noofcortons_data = CommonComponent::getCartonsTotal($row->data->buyer_quote_id);
            if($noofcortons_data!='')
                $noofcortons = $noofcortons_data;
           else
                $noofcortons = 'N/A';            

			$cartonweight_display = $row->data->total_cartons_weight;
			if($cartonweight_display!='')
				$cartonWeight = $row->data->total_cartons_weight;
			else
				$cartonWeight = 'N/A';

			//Getting status bar colors progress bar
            $StatusBar = BuyerOrdersComponent :: getStatusProgressbarColor ($row->data->seller_pickup_date, $row->data->dispatch_date, $row->data->seller_delivery_date, $row->data->delivery_date);            
            $status = $row->data->order_status;
            if($status!='')
            	$statusText = $row->data->order_status;
            else
            	$statusText = '';

			$row->cells[5]->value .= '
			<div class="col-md-2 padding-none status-block">
				<div class="status-bar">
					<div class="status-bar">
						'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'       		
						<span class="status-text">'.$statusText.'</span>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
        	<div class="col-md-10 padding-none pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_buyer).'</span></a>
				</div>
			</div>
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$seller_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 tab-modal-head">
                    <h3>
                        <i class="fa fa-map-marker"></i> '.$row->data->from_city.' to '.$row->data->to_city.'
                        <span class="close-icon">x</span>
                    </h3>
                </div>
                <div class="clearfix"></div>  
                <div class="col-md-8 data-div break-word">              
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Dispatch Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>
				<div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Delivery Date</span>
	                <span class="data-value">'.$delDate.'</span>
                </div>				
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Consignee</span>
					<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
				</div>				
                <div class="clearfix"></div>
                <div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Consignor</span>
	                <span class="data-value">'.$row->data->buyer_consignor_name.'</span>
                </div>
                <div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Tracking</span>
	                <span class="data-value">'.$tracking_status.'</span>
                </div>
                <div class="col-md-4 padding-left-none data-fld">
	                <span class="data-head">Payment Type</span>
	                <span class="data-value">'.$paymentType.'</span>
                </div>
				<div class="clearfix"></div>';
				if($row->data->InternationalTypeId == INTERNATIONAL_TYPE_AIR) {
					$row->cells[5]->value .= '
					<div class="col-md-4 padding-left-none data-fld">
		                <span class="data-head">No of Cartons</span>
		                <span class="data-value">'.$noofcortons.'</span>
	                </div>
	                <div class="col-md-4 padding-left-none data-fld">
		                <span class="data-head">Weight</span>
		                <span class="data-value">'.$cartonWeight.'</span>
	                </div>';
				}

				$row->cells[5]->value .= '
                </div>                
                <div class="col-md-4">
                    <span class="data-head">Total Price</span>
                    <span class="data-value big-value">'.$order_price.' /-</span>
                </div>
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}

/* Rel globalmobility and office Buyer Orders list new component start
*  @srinivas dantha
*  Date : 13 th July,2016
*/

	public static function getRelocGlobOfficeBuyerOrdersList() {

        $serviceId = Session::get('service_id');
        $query = DB::table('orders');
		$query->leftJoin('order_payments as op', 'op.id', '=', 'orders.order_payment_id');
		$query->leftJoin('order_invoices as oi', 'oi.order_id', '=', 'orders.id');
		if($serviceId == RELOCATION_GLOBAL_MOBILITY) {
			$query->leftJoin('lkp_cities as tc', 'tc.id', '=', 'orders.to_city_id');
			$query->leftJoin('relocationgm_seller_posts as sp', 'sp.id', '=', 'orders.seller_post_item_id');            
            $query->leftJoin('relocationgm_buyer_posts as ribp', 'ribp.id', '=', 'orders.buyer_quote_id');
            $query->leftJoin('relocationgm_buyer_quote_items as rbqi', 'rbqi.buyer_post_id', '=', 'orders.buyer_quote_id');
            $query->leftJoin('lkp_relocationgm_services as lrgs', 'lrgs.id', '=', 'rbqi.lkp_gm_service_id');
		} elseif ($serviceId == RELOCATION_OFFICE_MOVE) {
			$query->leftJoin('lkp_cities as fc', 'fc.id', '=', 'orders.from_city_id');
			$query->leftJoin('relocationoffice_seller_posts as sp', 'sp.id', '=', 'orders.seller_post_item_id');
		}		
		$query->join ('lkp_payment_modes as pm', 'pm.id', '=', 'op.lkp_payment_mode_id' );
		$query->leftJoin('users as u', 'u.id', '=', 'orders.seller_id');		
		$query->leftJoin('lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id');
		$query->leftJoin('lkp_order_statuses as os', 'os.id', '=', 'orders.lkp_order_status_id');	
		$query->where('orders.buyer_id', '=', Auth::user()->id);
		$query->where('orders.lkp_service_id', '=', $serviceId);
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}
		$query->groupBy('orders.id');

		if($serviceId == RELOCATION_GLOBAL_MOBILITY) {
			$query->select('u.username as tracking','oi.total_amt as inv_total','orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no','tc.city_name as to_city', 'u.username', 'pm.payment_mode as paymentmethod', 'rbqi.lkp_gm_service_id','lrgs.service_type');

		} elseif ($serviceId == RELOCATION_OFFICE_MOVE) {
			$query->select('sp.tracking','oi.total_amt as inv_total','orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.city_name as from_city', 'u.username', 'pm.payment_mode as paymentmethod' );
		}				
		//Filters values to populate in the page	
		if($serviceId == RELOCATION_GLOBAL_MOBILITY) {
			$to_locations['']	= "Location";  
			$services_gm['']	= "Service";
			$consiginor['']		= "Consiginor";
		} else {
			$from_locations[''] = "From Location";
		}	      
        $buyers[''] 		= "Seller";
        $consignee['']		= "Consignee";
        
        // Getting From Locations based on result set	
        $result = $query->get();	        
        //echo "<pre>"; print_r($result); die;
		foreach($result as $getData):
			if($serviceId == RELOCATION_OFFICE_MOVE) {
				$from_locations[$getData->from_city_id] 	= $getData->from_city;
			} else {
				$to_locations[$getData->to_city_id] 		= $getData->to_city;
				if(isset($getData->lkp_gm_service_id) && $getData->lkp_gm_service_id!='')
				$services_gm[$getData->lkp_gm_service_id] 	= $getData->service_type;

				$consiginor[$getData->buyer_consignor_name] = $getData->buyer_consignor_name;
			}
			
			$buyers[$getData->username] 					= $getData->username;
			$consignee[$getData->buyer_consignee_name] 		= $getData->buyer_consignee_name;	
		endforeach;

		if($serviceId == RELOCATION_OFFICE_MOVE) {
			$from_locations = CommonComponent::orderArray($from_locations);
		} else {
			$to_locations 	= CommonComponent::orderArray($to_locations);
			$services_gm 	= CommonComponent::orderArray($services_gm);
			$consiginor 	= CommonComponent::orderArray($consiginor);
		}		
		$buyers 		= CommonComponent::orderArray($buyers);
		$consignee 		= CommonComponent::orderArray($consignee);

		// Filters Start
		$filter = \DataFilter::source($query);

		if($serviceId == RELOCATION_OFFICE_MOVE) {
			$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   			});
		} else {

			$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   			});
   			//Service global mobility
   			$filter->add('service_type', '', 'select')->options($services_gm)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('rbqi.lkp_gm_service_id', $value):$query;
   			});
   			//Buyer consigner name
   			$filter->add('buyer_consignor_name', '', 'select')->options($consiginor)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {        	
        	return ($value!='')? $query->where('orders.buyer_consignor_name', $value):$query;
   			});

		}
		// Seller Name dropdown
   		$filter->add('username', '', 'select')->options($buyers)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});
   		// consignee Name dropdown
   		$filter->add('buyer_consignee_name', '', 'select')->options($consignee)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	
        	return ($value!='')? $query->where('orders.buyer_consignee_name', $value):$query;
   		});
   		$filter->add('order_no', 'Order No', 'text')->attr("class", "top-text-fld form-control1")->attr("onchange", "this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('orders.order_no', $value):$query;
       	});
   		// Dispatch date
		$filter->add('start_dispatch_date', 'From Date', 'text')->attr(["class" => "dateRange", "onchange"=>"this.form.submit()"])->scope( function ($query, $value) {        	
        	if(!empty($value)):        		
        		return $query->where ( 'orders.dispatch_date', '>=', CommonComponent::convertDateForDatabase($value) );			
        	else:
        		return $query;
        	endif;
   		});

		// delivery_date 
		$filter->add('end_dispatch_date', 'To Date', 'text')->attr(["class" => "dateRange","onchange"=>"this.form.submit()"])->scope( function ($query, $value) {
        	if(!empty($value)):
        		return $query->where ( 'orders.delivery_date', '>=', CommonComponent::convertDateForDatabase($value) );        		
        	else:
        		return $query;
        	endif;
   		});

		$filter->build();
		$grid = \DataGrid::source($query);		
		// Grid Headings
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-3 padding-left-none')); 
	   	$grid->add('username','Vendor Name', true)->attributes(array("class" => 'col-md-3 padding-left-none')); 
		if (Session::get('service_id') == RELOCATION_OFFICE_MOVE) {
				$grid->add('from_city','City', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
				$grid->add('to_city','', false)->attributes(array("class" => 'col-md-2 padding-left-none'));
		} else {
				$grid->add('from_city','', false);
				$grid->add('to_city','Location', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		}
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");
		//Grid data append for columns
		$grid->row( function($row) {
			
			$row->cells[0]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->order_no.'</a>');
			
			$row->cells[1]->attributes(array('class' => 'col-md-3 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->username.'</a>');
			
			if (Session::get('service_id') == RELOCATION_OFFICE_MOVE) {				
				$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->from_city.'</a>');			
			} else {
				$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/buyer_orderdetails/'.$row->data->id).'">'.$row->data->to_city.'</a>');	
			}
			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-left-none text-right'));

			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_buyer = CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);
			$order_id 	= $row->data->id;
			$seller_id  = $row->data->seller_id;
			$tracking_status = CommonComponent::getTrackingType($row->data->tracking);
            if($row->data->lkp_order_type_id==TERM)
                $tracking_status = 'N/A'; 

            if (Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY) {
            	$location = $row->data->to_city;
            } else {
				$location = $row->data->from_city;
            }
           

        	if($row->data->inv_total!=0)
                $order_price = CommonComponent::moneyFormat($row->data->inv_total);
            else
                $order_price = CommonComponent::moneyFormat($row->data->price);

            //Getting status bar colors progress bar
            $StatusBar = BuyerOrdersComponent :: getStatusProgressbarColor ($row->data->seller_pickup_date, $row->data->dispatch_date, $row->data->seller_delivery_date, $row->data->delivery_date);    
           
			if(Session::get ( 'service_id' )  == RELOCATION_GLOBAL_MOBILITY){
                $checkstatus = $row->data->order_status;
                if($checkstatus == 'Pickup due') {
                    $statusText = "Commencement Due";
                } elseif($checkstatus == 'Consignment pickup'){
                   $statusText = "Commencement Started";
                } elseif($checkstatus == 'Delivered'){
                   $statusText = "Commencement Completed";
                }else{
                    $statusText = "Commencement Completed";
                }
            } else {
            	$statusText = $row->data->order_status;
            }


			$row->cells[5]->value = '
			<div class="col-md-2 padding-none status-block">
				<div class="status-bar">
					<div class="status-bar">
						'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'       		
						<span class="status-text">'.$statusText.'</span>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
        	<div class="col-md-10 padding-none pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_buyer).'</span></a>
				</div>
			</div>
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$seller_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 tab-modal-head">
                    <h3>
                        <i class="fa fa-map-marker"></i> '.$location.'
                        <span class="close-icon">x</span>
                    </h3>
                </div>
                <div class="clearfix"></div>  
                <div class="col-md-8 data-div break-word">              
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Dispatch Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>';

				if (Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY) {
					$row->cells[5]->value .= '
					<div class="col-md-4 padding-left-none data-fld">
						<span class="data-head">Consignor Name</span>
						<span class="data-value">'.$row->data->buyer_consignor_name.'</span>
					</div>
	                ';
	            } else {
	            	$row->cells[5]->value .= '
					<div class="col-md-4 padding-left-none data-fld">
						<span class="data-head">Consignee</span>
						<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
					</div>
	                <div class="col-md-4 padding-left-none data-fld">
		                <span class="data-head">Tracking</span>
		                <span class="data-value">'.$tracking_status.'</span>
	                </div> ';
	            }
				$row->cells[5]->value .= '
                </div>                
                <div class="col-md-4">
                    <span class="data-head">Total Price</span>
                    <span class="data-value big-value">'.$order_price.' /-</span>
                </div>
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}

/* Orders Progress Status bar colors
* @ Srinivas Dantha
* Date : 12th July,2016
* @return status color html strtings
*/
public static function getStatusProgressbarColor($SellerPickupDate, $DispatchDate, $SellerDeliveryDate, $DeliveryDate, $buyerPickupDate = null) {

	$current_date_seller = date("Y-m-d");
	$delvieryforstatus=CommonComponent::convertDateForDatabase($DeliveryDate);
	$str="";
	$strdelivery="";

if(Session::get ( 'service_id' ) ==ROAD_TRUCK_HAUL || Session::get ( 'service_id' ) ==ROAD_TRUCK_LEASE) {

	$splitBuyepick = explode(" ",$buyerPickupDate);
    $splitpick = $splitBuyepick[0]; 
   if ($SellerDeliveryDate == '0000-00-00 00:00:00')
    {				
            if ($current_date_seller < $splitpick) {
                    //echo "gray"; exit;
                    $str="";
                    $strdelivery="";					
            } else if ($current_date_seller > $splitpick) {
                    //echo "red"; exit;
                    $str='<div class="status-bar-left"></div>';
                    $strdelivery="";					
            } else if ($current_date_seller == $splitpick) {
                    //echo "gray2"; exit;
                    $str='';
                    $strdelivery="";					
            } 				

    } else{                              
        $splitTimeStamp = explode(" ",$SellerDeliveryDate);
        $splitdateDelivery = $splitTimeStamp[0];                               
        if ($splitdateDelivery == $current_date_seller) {                                            
                    //echo "green1"; exit;
                    $sellerpickupcolor="green";
                    $str='<div class="status-bar-right-full"></div>';
                    $strdelivery="";
            } else if ($splitdateDelivery." 00:00:00" <= $buyerPickupDate." 00:00:00") {    
                    //echo "green2"; exit;
                    $sellerpickupcolor="green";
                    $str='<div class="status-bar-right-full"></div>';
                    $strdelivery="";
            } else if ($splitdateDelivery." 00:00:00" > $buyerPickupDate." 00:00:00") { 
                    //echo "red"; exit; 
                    $sellerpickupcolor="red";
                    $str='<div class="status-bar-left-full"></div>';
                    $strdelivery="";
            }   
        
    }
} else {
	if ($SellerPickupDate == '0000-00-00 00:00:00' && $SellerDeliveryDate == '0000-00-00 00:00:00') {
        if ($current_date_seller < $DispatchDate) {
                //echo "gray"; exit;
                $str="";
                $strdelivery="";					
        } elseif ($current_date_seller > $DispatchDate) {
                //echo "red"; exit;
                $str='<div class="status-bar-left"></div>';
                $strdelivery="";					
        } elseif ($current_date_seller == $DispatchDate) {
                //echo "gray2"; exit;
                $str='';
                $strdelivery="";					
        } 				

    } elseif ($SellerPickupDate != '0000-00-00 00:00:00' && $SellerDeliveryDate == '0000-00-00 00:00:00') {
            if ($SellerPickupDate <= $DispatchDate." 00:00:00") {
                    //echo "green"; exit;
                    $sellerpickupcolor="green";
                    $str='<div class="status-bar-left-green"></div>';
                    $strdelivery="";
            } elseif ($SellerPickupDate > $DispatchDate." 00:00:00") {
                    //echo "red"; exit;
                    $sellerpickupcolor="red";
                    $str='<div class="status-bar-left"></div>';
                    $strdelivery="";
            }
            if ($current_date_seller < $delvieryforstatus) {
                   // echo "gray"; exit;                    
                    $strdelivery='';
            } elseif ($current_date_seller > $delvieryforstatus) {
                    //echo "red"; exit;                    
                    $strdelivery='<div class="status-bar-right-red"></div>';
            } elseif ($current_date_seller == $delvieryforstatus) {
                    //echo "gray2"; exit;                    
                    $strdelivery='';
            }				

    } else {	
            //echo "sellerpikupdate and seller delivery is there then execute below conditions";             	
         if ($DispatchDate == $current_date_seller) {
                //echo "green"; exit;
                $sellerpickupcolor="green";
                $str='<div class="status-bar-left-green"></div>';
                $strdelivery="";
        } elseif ($SellerPickupDate <= $DispatchDate." 00:00:00") {
                //echo "green"; exit;
                $sellerpickupcolor="green";
                $str='<div class="status-bar-right-full"></div>';
                $strdelivery="";
        } elseif ($SellerPickupDate > $DispatchDate." 00:00:00") {
                //echo "red"; exit;
                $sellerpickupcolor="red";
                $str='<div class="status-bar-left"></div>';
                $strdelivery="";
        }
        //Seller delivery date conditions
        if ($SellerDeliveryDate <= $delvieryforstatus." 00:00:00") {
                //echo "green"; exit;                
                if($sellerpickupcolor!=""){
                	$strdelivery='<div class="status-bar-right"></div>';
                }										
        } elseif ($SellerDeliveryDate > $delvieryforstatus." 00:00:00") {
                //echo "red"; exit;                
                if($sellerpickupcolor!=""){
                	$strdelivery='<div class="status-bar-right-red"></div>';
                }
        }
	}

}
	//return array($str, $strdelivery);
	return array('firstOffColor' => $str, "SecondOffColor" => $strdelivery);

}


}
