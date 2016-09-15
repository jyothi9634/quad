<?php

namespace App\Components;

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
use App\Models\Order;
use Log;
use App\Models\LkpServiceCharges;
use App\Models\OrderReceipt;
use App\Models\OrderInvoice;
use App\Models\SellerOrderInvoice;
use App\Models\OrderTrackingDetail;
use PDF;
use App\Components\Term\TermSellerComponent;

class SellerOrderComponent {
    
	/**
	 * Ftl  Seller Order List
	 * @ Srinivas Dantha
	 * Date : july 14th,2016
	 */
	
	public static function getFtlSellerOrders() {

        $serviceId = Session::get('service_id');
        $query = DB::table ( 'orders' );
		$query->leftJoin ( 'order_payments as op', 'orders.order_payment_id', '=', 'op.id' );
		$query->leftJoin ( 'seller_order_invoices as oi', 'orders.id', '=', 'oi.order_id' );
		$query->leftJoin ( 'lkp_cities as fc', 'orders.from_city_id', '=', 'fc.id' );
		$query->leftJoin ( 'lkp_cities as tc', 'orders.to_city_id', '=', 'tc.id' );
		$query->leftJoin('users as u', 'u.id', '=', 'orders.buyer_id');
		$query->leftJoin ( 'lkp_order_statuses as os', 'orders.lkp_order_status_id', '=', 'os.id' );
		$query->leftJoin ( 'lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id' );
		$query->where ( 'orders.seller_id', '=', Auth::User ()->id );
		$query->where ( 'orders.lkp_order_status_id', '!=', ORDER_PENDING );		
		$query->where('orders.lkp_service_id', '=', ROAD_FTL);		
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}
		$query->groupBy('orders.id');
		$query->select ( 'orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.city_name as from_city', 'tc.city_name as to_city','u.username' );
		//Filters values to populate in the page		
		$from_locations[''] = "From Location";
		$to_locations['']	= "To Location";        
        $buyer_name[''] 		= "Buyer";
        $consignee_name['']		= "Consignee";
        // Getting From Locations based on result set	
        $result = $query->get();	
        //echo "<pre>"; print_r($result); die;
		foreach($result as $getData):
			$from_locations[$getData->from_city_id] 		= $getData->from_city;
			$to_locations[$getData->to_city_id] 			= $getData->to_city;
			$buyer_name[$getData->username] 				= $getData->username;
			if(isset($getData->buyer_consignee_name) && $getData->buyer_consignee_name!='')
			$consignee_name[$getData->buyer_consignee_name] = $getData->buyer_consignee_name;	
		endforeach;

		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations 	= CommonComponent::orderArray($to_locations);
		$buyer_name		= CommonComponent::orderArray($buyer_name);
		$consignee_name	= CommonComponent::orderArray($consignee_name);

		// Filters Start
		$filter = \DataFilter::source($query);
		
		$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   		});		
		$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   		});		
   		$filter->add('username', '', 'select')->options($buyer_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});   		
   		$filter->add('buyer_consignee_name', '', 'select')->options($consignee_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	
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
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('username','Buyer Name', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('from_city','From', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('to_city','To', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('order_status','Status', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");
		//Grid data append for columns
		$grid->row( function($row) {
			
			$row->cells[0]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->order_no.'</a>');
			
			$row->cells[1]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->username.'</a>');
			
			$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->from_city.'</a>');
			
			$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->to_city.'</a>');

			//Getting status bar colors progress bar
            $StatusBar = BuyerOrdersComponent :: getStatusProgressbarColor ($row->data->seller_pickup_date, $row->data->dispatch_date, $row->data->seller_delivery_date, $row->data->delivery_date);            
            $status = $row->data->order_status;
            if($status!='')
            	$statusText = $row->data->order_status;
            else
            	$statusText = '';

            $progressStatusBar = '
				<div class="status-block pull-left">
					<div class="status-bar">
					'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'
					<span class="status-text">'. $statusText .'</span>
					</div>
				</div>
			';

			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$progressStatusBar.'</a>');

			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_seller    =   CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);
			$order_id 	= $row->data->id;
			$buyer_id  = $row->data->buyer_id;			

            if($row->data->number_loads!="undefined")
            	$noOfLoads = $row->data->number_loads;
            else
            	$noOfLoads = "N/A";   

			if($row->data->lkp_order_status_id!= ORDER_CANCELLED) {
				$row->cells[6]->value .= '
				<div class="col-md-2 padding-none text-right action-block">
				<a class="btn red-btn pull-right" href=/consignment_pickup/' . $order_id . '>Update Status</a>
				</div><div class="clearfix"></div>';
			}

			$row->cells[6]->value .= '
        	<div class="pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_seller).'</span></a>
				</div>
			</div>';
			
			$row->cells[6]->value .= '
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$buyer_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
                    <h3>
                        <i class="fa fa-map-marker"></i> '.$row->data->from_city.' to '.$row->data->to_city.'
                        <span class="close-icon">x</span>
                    </h3>
                </div>                
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Dispatch Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>				
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Consignee</span>
					<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
				</div>                
                <div class="col-md-3 padding-left-none data-fld">
	                <span class="data-head">Quantity</span>
	                <span class="data-value">'.$row->data->quantity.' '.$row->data->units.'</span>
                </div>                
                <div class="col-md-3 padding-left-none data-fld">
	                <span class="data-head">No of Loads</span>
	                <span class="data-value">'.$noOfLoads.'</span>
                </div>
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}

	/**
	 * LTL +4  Seller Order List
	 * @ Srinivas Dantha
	 * Date : july 14th,2016
	 */
	
	public static function getLtlSellerOrders() {

        $serviceId = Session::get('service_id');
        $query = DB::table ( 'orders' );
		$query->leftJoin ( 'order_payments as op', 'orders.order_payment_id', '=', 'op.id' );
		$query->leftJoin ( 'seller_order_invoices as oi', 'orders.id', '=', 'oi.order_id' );
		//conditions for locations  with differnt tables in LTl +4 services
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
		$query->leftJoin('users as u', 'u.id', '=', 'orders.buyer_id');
		$query->leftJoin ( 'lkp_order_statuses as os', 'orders.lkp_order_status_id', '=', 'os.id' );
		$query->leftJoin ( 'lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id' );
		$query->where('orders.seller_id', '=', Auth::user()->id);
		$query->where('orders.lkp_service_id', '=', $serviceId);
		$query->where ( 'orders.lkp_order_status_id', '!=', ORDER_PENDING );
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}
		$query->groupBy('orders.id');
		//Getting Results from query depends on conditions
		if ($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC) {
			$query->select('orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.postoffice_name as from_city', 'tc.postoffice_name as to_city','u.username');
		} elseif ($serviceId == AIR_INTERNATIONAL) {
			$query->select('orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.airport_name as from_city', 'tc.airport_name as to_city','u.username');
		} elseif ($serviceId == OCEAN) {
			$query->select('orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.seaport_name as from_city', 'tc.seaport_name as to_city','u.username');
		} else {
			$query->select('orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.postoffice_name as from_city', 'tc.postoffice_name as to_city','u.username');
		}		

		//Filters values to populate in the page		
		$from_locations[''] = "From Location";
		$to_locations['']	= "To Location";        
        $buyer_name[''] 		= "Buyer";
        $consignee_name['']		= "Consignee";
        // Getting From Locations based on result set	
        $result = $query->get();	
        //echo "<pre>"; print_r($result); die;
		foreach($result as $getData):
			$from_locations[$getData->from_city_id] 		= $getData->from_city;
			$to_locations[$getData->to_city_id] 			= $getData->to_city;
			$buyer_name[$getData->username] 				= $getData->username;
			if(isset($getData->buyer_consignee_name) && $getData->buyer_consignee_name!='')
			$consignee_name[$getData->buyer_consignee_name] = $getData->buyer_consignee_name;	
		endforeach;

		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations 	= CommonComponent::orderArray($to_locations);
		$buyer_name		= CommonComponent::orderArray($buyer_name);
		$consignee_name	= CommonComponent::orderArray($consignee_name);

		// Filters Start
		$filter = \DataFilter::source($query);
		
		$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   		});		
		$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   		});		
   		$filter->add('username', '', 'select')->options($buyer_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});   		
   		$filter->add('buyer_consignee_name', '', 'select')->options($consignee_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	
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
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('username','Buyer Name', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('from_city','From', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('to_city','To', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('order_status','Status', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");
		//Grid data append for columns
		$grid->row( function($row) {
			
			$row->cells[0]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->order_no.'</a>');
			
			$row->cells[1]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->username.'</a>');
			
			$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->from_city.'</a>');
			
			$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->to_city.'</a>');

			//Getting status bar colors progress bar
            $StatusBar = BuyerOrdersComponent :: getStatusProgressbarColor ($row->data->seller_pickup_date, $row->data->dispatch_date, $row->data->seller_delivery_date, $row->data->delivery_date);            
            $status = $row->data->order_status;
            if($status!='')
            	$statusText = $row->data->order_status;
            else
            	$statusText = '';

            $progressStatusBar = '
				<div class="status-block pull-left">
					<div class="status-bar">
					'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'
					<span class="status-text">'. $statusText .'</span>
					</div>
				</div>
			';

			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$progressStatusBar.'</a>');

			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_seller    =   CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);
			$order_id 	= $row->data->id;
			$buyer_id  = $row->data->buyer_id;

			if($row->data->lkp_order_status_id!= ORDER_CANCELLED) {
				$row->cells[6]->value .= '
				<div class="col-md-2 padding-none text-right action-block">
				<a class="btn red-btn pull-right" href=/consignment_pickup/' . $order_id . '>Update Status</a>
				</div><div class="clearfix"></div>';
			}

			$row->cells[6]->value .= '
        	<div class="pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_seller).'</span></a>
				</div>
			</div>';
			
			$row->cells[6]->value .= '
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$buyer_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
                    <h3>
                        <i class="fa fa-map-marker"></i> '.$row->data->from_city.' to '.$row->data->to_city.'
                        <span class="close-icon">x</span>
                    </h3>
                </div>                
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Dispatch Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>				
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Consignee</span>
					<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
				</div>
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}

	/**
	 * Truck Haul and Truck Lease Seller Order List
	 * @ Srinivas Dantha
	 * Date : july 14th,2016
	 */
	
	public static function getTruckHaulLeaseSellerOrders() {

        $serviceId = Session::get('service_id');
        $query = DB::table ( 'orders' );
		$query->leftJoin ( 'order_payments as op', 'orders.order_payment_id', '=', 'op.id' );
		$query->leftJoin ( 'seller_order_invoices as oi', 'orders.id', '=', 'oi.order_id' );
		$query->leftJoin ( 'lkp_cities as fc', 'orders.from_city_id', '=', 'fc.id' );
		$query->leftJoin ( 'lkp_cities as tc', 'orders.to_city_id', '=', 'tc.id' );
		$query->leftJoin('users as u', 'u.id', '=', 'orders.buyer_id');
		$query->leftJoin ( 'lkp_order_statuses as os', 'orders.lkp_order_status_id', '=', 'os.id' );
		$query->leftJoin ( 'lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id' );		
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}
		if($serviceId==ROAD_TRUCK_HAUL){
			$query->leftJoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'orders.lkp_vehicle_type_id');
			$query->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
            $query->leftJoin('truckhaul_seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
		} else {
			$query->leftJoin('trucklease_seller_post_items as spi', 'spi.id', '=', 'orders.seller_post_item_id');
            $query->leftJoin('trucklease_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id');
            $query->leftJoin('lkp_trucklease_lease_terms as tlt', 'tlt.id', '=', 'spi.lkp_trucklease_lease_term_id');
            $query->join ( 'lkp_payment_modes as pm', 'pm.id', '=', 'sp.lkp_payment_mode_id' );
		}        
		$query->where ( 'orders.seller_id', '=', Auth::User ()->id );
		$query->where ( 'orders.lkp_order_status_id', '!=', ORDER_PENDING );		
		$query->where ('orders.lkp_service_id', '=', $serviceId);
		$query->groupBy('orders.id');

		if($serviceId == ROAD_TRUCK_HAUL) {
			$query->select ( 'orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.city_name as from_city', 'tc.city_name as to_city','u.username','lvt.vehicle_type','lkp_load_types.load_type','spi.vehicle_number' );
		} else {
			$query->select ( 'orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.city_name as from_city', 'tc.city_name as to_city','u.username','tlt.lease_term as leaseTerm','spi.driver_availability', 'pm.payment_mode as paymentmethod', 'spi.vehicle_make_model_year','sp.tracking' );
		}	

		//Filters values to populate in the page		
		$from_locations[''] = "From Location";
		$to_locations['']	= "To Location";        
        $buyer_name[''] 		= "Buyer";
        $consignee_name['']		= "Consignee";
        // Getting From Locations based on result set	
        $result = $query->get();	
        //echo "<pre>"; print_r($result); die;
		foreach($result as $getData):
			$from_locations[$getData->from_city_id] 		= $getData->from_city;
			$to_locations[$getData->to_city_id] 			= $getData->to_city;
			$buyer_name[$getData->username] 				= $getData->username;
			if(isset($getData->buyer_consignee_name) && $getData->buyer_consignee_name!='')
			$consignee_name[$getData->buyer_consignee_name] = $getData->buyer_consignee_name;	
		endforeach;

		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations 	= CommonComponent::orderArray($to_locations);
		$buyer_name		= CommonComponent::orderArray($buyer_name);
		$consignee_name	= CommonComponent::orderArray($consignee_name);

		// Filters Start
		$filter = \DataFilter::source($query);
		
		$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   		});		
		$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   		});		
   		$filter->add('username', '', 'select')->options($buyer_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});   		
   		$filter->add('buyer_consignee_name', '', 'select')->options($consignee_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	
        	return ($value!='')? $query->where('orders.buyer_consignee_name', $value):$query;
   		});
   		// Vehicle number filter	
   		$filter->add('vehicle_number', 'Vehicle No', 'text')->attr("class", "top-text-fld form-control1")->attr("onchange", "this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('spi.vehicle_number', $value):$query;
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
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('username','Buyer Name', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	if(Session::get('service_id') == ROAD_TRUCK_LEASE) {
			$grid->add('from_city','Location', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	} else {
	   		$grid->add('from_city','From', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	}
	   	if(Session::get('service_id') == ROAD_TRUCK_LEASE) {
			$grid->add('to_city','', false)->attributes(array("class" => 'col-md-2 padding-left-none'));
	   	} else {
	   		$grid->add('to_city','To', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
	   	}	   	
		$grid->add('order_status','Status', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");
		//Grid data append for columns
		$grid->row( function($row) {
			
			$row->cells[0]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->order_no.'</a>');
			
			$row->cells[1]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->username.'</a>');
			
			$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->from_city.'</a>');
			
			$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->to_city.'</a>');

			//Getting status bar colors progress bar
            $StatusBar = BuyerOrdersComponent :: getStatusProgressbarColor ($row->data->seller_pickup_date, $row->data->dispatch_date, $row->data->seller_delivery_date, $row->data->delivery_date);                        

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

            $progressStatusBar = '
				<div class="status-block pull-left">
					<div class="status-bar">
					'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'
					<span class="status-text">'. $statusText .'</span>
					</div>
				</div>
			';

			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$progressStatusBar.'</a>');

			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_seller    =   CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);
			$order_id 	= $row->data->id;
			$buyer_id  = $row->data->buyer_id;	

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

			if($row->data->lkp_order_status_id!= ORDER_CANCELLED) {
				$row->cells[6]->value .= '
				<div class="col-md-2 padding-none text-right action-block">
				<a class="btn red-btn pull-right" href=/consignment_pickup/' . $order_id . '>Update Status</a>
				</div><div class="clearfix"></div>';
			}

			$row->cells[6]->value .= '
        	<div class="pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_seller).'</span></a>
				</div>
			</div>';
			
			$row->cells[6]->value .= '
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$buyer_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
                    <h3>
                        <i class="fa fa-map-marker"></i> '.$row->data->from_city.' to '.$row->data->to_city.'
                        <span class="close-icon">x</span>
                    </h3>
                </div>
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Reporting Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>				
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Reporter Name</span>
					<span class="data-value">'.$row->data->buyer_consignor_name.'</span>
				</div> ';

			if(Session::get('service_id') == ROAD_TRUCK_HAUL)  {
				$row->cells[6]->value .= '
                <div class="col-md-3 padding-left-none data-fld">
		                <span class="data-head">Load Type</span>
		                <span class="data-value">'.$row->data->load_type.'</span>
                </div>
                <div class="col-md-3 padding-left-none data-fld">
	                <span class="data-head">Vehicle Type</span>
	                <span class="data-value">'.$row->data->vehicle_type.'</span>
                </div> ';
            } else {
            	$row->cells[6]->value .= '
                <div class="col-md-3 padding-left-none data-fld">
		                <span class="data-head">Lease Term</span>
		                <span class="data-value">'.$lease_term.'</span>
                </div>
                <div class="col-md-3 padding-left-none data-fld">
	                <span class="data-head">Driver</span>
	                <span class="data-value">'.$driverAv.'</span>
                </div> 
                <div class="col-md-3 padding-left-none data-fld">
	                <span class="data-head">Payment</span>
	                <span class="data-value">'.$paymentType.'</span>
                </div> 
                <div class="col-md-3 padding-left-none data-fld">
	                <span class="data-head">Documents</span>
	                <span class="data-value">0</span>
                </div> 
                <div class="col-md-3 padding-left-none data-fld">
	                <span class="data-head">Vehicle Make & Model & Year</span>
	                <span class="data-value">'.$row->data->vehicle_make_model_year.'</span>
                </div>';
            }
            $row->cells[6]->value .= '
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}

	 /**
	 * Courier  Seller Order List
	 * @ Srinivas Dantha
	 * Date : july 14th,2016
	 */
	
	public static function getCourierSellerOrders() {

        $serviceId = Session::get('service_id');
        $query = DB::table ( 'orders' );
		$query->leftJoin ( 'order_payments as op', 'orders.order_payment_id', '=', 'op.id' );
		$query->leftJoin ( 'seller_order_invoices as oi', 'orders.id', '=', 'oi.order_id' );
		$query->leftJoin('lkp_ptl_pincodes as fc', 'fc.id', '=', 'orders.from_city_id');
		$query->leftJoin('lkp_ptl_pincodes as tc', 'tc.id', '=', 'orders.to_city_id');
		$query->leftJoin('users as u', 'u.id', '=', 'orders.buyer_id');
		$query->leftJoin ( 'lkp_order_statuses as os', 'orders.lkp_order_status_id', '=', 'os.id' );
		$query->leftJoin ( 'lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id' );
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
		$query->where ( 'orders.seller_id', '=', Auth::User ()->id );
		$query->where ( 'orders.lkp_order_status_id', '!=', ORDER_PENDING );		
		$query->where('orders.lkp_service_id', '=', $serviceId);		
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}			
		$query->whereRaw("(case when `orders`.`lkp_order_type_id` = 1 then adspi.lkp_courier_delivery_type_id=".Session::get('delivery_type')." when `orders`.`lkp_order_type_id` = 2 then tbq.lkp_courier_delivery_type_id=".Session::get('delivery_type')." end)");		
		$query->groupBy('orders.id');
		$query->select('orders.*',DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then adspi.lkp_courier_delivery_type_id  when `orders`.`lkp_order_type_id` = 2 then tbq.lkp_courier_delivery_type_id end) as lkp_courier_delivery_type_id"), 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.postoffice_name as from_city', 'tc.postoffice_name as to_city','u.username');
		//Filters values to populate in the page		
		$from_locations[''] = "From Location";
		$to_locations['']	= "To Location";        
        $buyer_name[''] 		= "Buyer";
        $consignee_name['']		= "Consignee";
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
			$buyer_name[$getData->username] 				= $getData->username;
			if(isset($getData->buyer_consignee_name) && $getData->buyer_consignee_name!='')
			$consignee_name[$getData->buyer_consignee_name] = $getData->buyer_consignee_name;	
		endforeach;

		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations 	= CommonComponent::orderArray($to_locations);
		$buyer_name		= CommonComponent::orderArray($buyer_name);
		$consignee_name	= CommonComponent::orderArray($consignee_name);

		// Filters Start
		$filter = \DataFilter::source($query);
		
		$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   		});		
		$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   		});		
   		$filter->add('username', '', 'select')->options($buyer_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});   		
   		$filter->add('buyer_consignee_name', '', 'select')->options($consignee_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	
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
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('username','Buyer Name', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('from_city','From', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('to_city','To', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('order_status','Status', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
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
			
			$row->cells[0]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->order_no.'</a>');
			
			$row->cells[1]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->username.'</a>');
			
			$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->from_city.'</a>');
			
			$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$to_city.'</a>');

			//Getting status bar colors progress bar
            $StatusBar = BuyerOrdersComponent :: getStatusProgressbarColor ($row->data->seller_pickup_date, $row->data->dispatch_date, $row->data->seller_delivery_date, $row->data->delivery_date);            
            $status = $row->data->order_status;
            if($status!='')
            	$statusText = $row->data->order_status;
            else
            	$statusText = '';

            $progressStatusBar = '
				<div class="status-block pull-left">
					<div class="status-bar">
					'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'
					<span class="status-text">'. $statusText .'</span>
					</div>
				</div>
			';

			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$progressStatusBar.'</a>');

			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_seller    =   CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);
			$order_id 	= $row->data->id;
			$buyer_id  = $row->data->buyer_id;			

			if($row->data->lkp_order_status_id!= ORDER_CANCELLED) {
				$row->cells[6]->value .= '
				<div class="col-md-2 padding-none text-right action-block">
				<a class="btn red-btn pull-right" href=/consignment_pickup/' . $order_id . '>Update Status</a>
				</div><div class="clearfix"></div>';
			}

			$row->cells[6]->value .= '
        	<div class="pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_seller).'</span></a>
				</div>
			</div>';
			
			$row->cells[6]->value .= '
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$buyer_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
                    <h3>
                        <i class="fa fa-map-marker"></i> '.$row->data->from_city.' to '.$to_city.'
                        <span class="close-icon">x</span>
                    </h3>
                </div>                
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Dispatch Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>				
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Consignee</span>
					<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
				</div>                                
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}

	/**
	 * Relcoation Domestic and petmove Seller Order List
	 * @ Srinivas Dantha
	 * Date : july 14th,2016
	 */
	
	public static function getRelocDomPetSellerOrders() {

        $serviceId = Session::get('service_id');
        $query = DB::table ( 'orders' );
		$query->leftJoin ( 'order_payments as op', 'orders.order_payment_id', '=', 'op.id' );
		$query->leftJoin ( 'seller_order_invoices as oi', 'orders.id', '=', 'oi.order_id' );
		$query->leftJoin ( 'lkp_cities as fc', 'orders.from_city_id', '=', 'fc.id' );
		$query->leftJoin ( 'lkp_cities as tc', 'orders.to_city_id', '=', 'tc.id' );
		$query->leftJoin('users as u', 'u.id', '=', 'orders.buyer_id');
		$query->leftJoin ( 'lkp_order_statuses as os', 'orders.lkp_order_status_id', '=', 'os.id' );
		$query->leftJoin ( 'lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id' );
		$query->where ( 'orders.seller_id', '=', Auth::User ()->id );
		$query->where ( 'orders.lkp_order_status_id', '!=', ORDER_PENDING );		
		$query->where('orders.lkp_service_id', '=', $serviceId);		
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}
		$query->groupBy('orders.id');
		$query->select ( 'orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.city_name as from_city', 'tc.city_name as to_city','u.username' );
		//Filters values to populate in the page		
		$from_locations[''] = "From Location";
		$to_locations['']	= "To Location";        
        $buyer_name[''] 		= "Buyer";
        $consignee_name['']		= "Consignee";
        // Getting From Locations based on result set	
        $result = $query->get();	
        //echo "<pre>"; print_r($result); die;
		foreach($result as $getData):
			$from_locations[$getData->from_city_id] 		= $getData->from_city;
			$to_locations[$getData->to_city_id] 			= $getData->to_city;
			$buyer_name[$getData->username] 				= $getData->username;
			if(isset($getData->buyer_consignee_name) && $getData->buyer_consignee_name!='')
			$consignee_name[$getData->buyer_consignee_name] = $getData->buyer_consignee_name;	
		endforeach;

		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations 	= CommonComponent::orderArray($to_locations);
		$buyer_name		= CommonComponent::orderArray($buyer_name);
		$consignee_name	= CommonComponent::orderArray($consignee_name);

		// Filters Start
		$filter = \DataFilter::source($query);
		
		$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   		});		
		$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   		});		
   		$filter->add('username', '', 'select')->options($buyer_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});   		
   		$filter->add('buyer_consignee_name', '', 'select')->options($consignee_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	
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
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('username','Buyer Name', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('from_city','From', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('to_city','To', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('order_status','Status', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");
		//Grid data append for columns
		$grid->row( function($row) {
			
			$row->cells[0]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->order_no.'</a>');
			
			$row->cells[1]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->username.'</a>');
			
			$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->from_city.'</a>');
			
			$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->to_city.'</a>');

			//Getting status bar colors progress bar
            $StatusBar = BuyerOrdersComponent :: getStatusProgressbarColor ($row->data->seller_pickup_date, $row->data->dispatch_date, $row->data->seller_delivery_date, $row->data->delivery_date);            
            $status = $row->data->order_status;
            if($status!='')
            	$statusText = $row->data->order_status;
            else
            	$statusText = '';

            $progressStatusBar = '
				<div class="status-block pull-left">
					<div class="status-bar">
					'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'
					<span class="status-text">'. $statusText .'</span>
					</div>
				</div>
			';

			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$progressStatusBar.'</a>');

			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_seller    =   CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);
			$order_id 	= $row->data->id;
			$buyer_id  = $row->data->buyer_id;		

			if (Session::get('service_id') == RELOCATION_PET_MOVE) {
				$deliveryDate = CommonComponent::checkAndGetDate($row->data->delivery_date);
				if($deliveryDate!='')
					$delDate = $deliveryDate;
				else
					$delDate  = 'N/A';
			}

			if($row->data->lkp_order_status_id!= ORDER_CANCELLED) {
				$row->cells[6]->value .= '
				<div class="col-md-2 padding-none text-right action-block">
				<a class="btn red-btn pull-right" href=/consignment_pickup/' . $order_id . '>Update Status</a>
				</div><div class="clearfix"></div>';
			}

			$row->cells[6]->value .= '
        	<div class="pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_seller).'</span></a>
				</div>
			</div>';
			
			$row->cells[6]->value .= '
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$buyer_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
                    <h3>
                        <i class="fa fa-map-marker"></i> '.$row->data->from_city.' to '.$row->data->to_city.'
                        <span class="close-icon">x</span>
                    </h3>
                </div>';
			if (Session::get('service_id') == RELOCATION_DOMESTIC) {
				$row->cells[6]->value .= '
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Dispatch Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>				
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Consignee</span>
					<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
				</div>';
			} else {
				$buyer_post= CommonComponent::getBuyerPostFromOrder($order_id);
                $objPetmove = new \App\Models\RelocationPetBuyerPost();
                $buyer_post_details = $objPetmove->getPetmovePostDetails($buyer_post->buyer_id, $buyer_post->buyer_quote_id);
                $breedType= $buyer_post_details->breed_type;
                if($breedType!='') {
                    $breed_type=$breedType;
                } else {
                    $breed_type='N/A';
                }
				$row->cells[6]->value .= '
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Dispatch Date</span>
					<span class="data-value">'.CommonComponent::checkAndGetDate($row->data->dispatch_date).'</span>
				</div>	
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Delivery Date</span>
					<span class="data-value">'.$delDate.'</span>
				</div>				
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Consignee</span>
					<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
				</div>
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Pet Type</span>
					<span class="data-value">'.$buyer_post_details->pet_type.'</span>
				</div>
				<div class="clearfix"></div>				
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Breed</span>
					<span class="data-value">'.$breed_type.'</span>
				</div>
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Cage Type</span>
					<span class="data-value">'.$buyer_post_details->cage_type.'</span>
				</div>
				
				 ';
			}
				
			$row->cells[6]->value .= '
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}

	/**
	 * Relcation Int Seller Order List
	 * @ Srinivas Dantha
	 * Date : july 14th,2016
	 */
	
	public static function getRelocIntSellerOrders() {

        $serviceId = Session::get('service_id');
        $int_type = 1;
        $query = DB::table ( 'orders' );
		$query->leftJoin ( 'order_payments as op', 'orders.order_payment_id', '=', 'op.id' );
		$query->leftJoin ( 'seller_order_invoices as oi', 'orders.id', '=', 'oi.order_id' );
		$query->leftJoin ( 'lkp_cities as fc', 'orders.from_city_id', '=', 'fc.id' );
		$query->leftJoin ( 'lkp_cities as tc', 'orders.to_city_id', '=', 'tc.id' );
		$query->leftJoin('users as u', 'u.id', '=', 'orders.buyer_id');
		$query->leftJoin ( 'lkp_order_statuses as os', 'orders.lkp_order_status_id', '=', 'os.id' );
		$query->leftJoin ( 'lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id' );
		$query->where ( 'orders.seller_id', '=', Auth::User ()->id );
		$query->where ( 'orders.lkp_order_status_id', '!=', ORDER_PENDING );		
		$query->where('orders.lkp_service_id', '=', $serviceId);
		if (isset ( $_REQUEST ['int_type'] ) && $_REQUEST ['int_type'] != '') {
				$int_type = $_REQUEST['int_type'];
		}					
		$query->where ( 'orders.lkp_international_type_id', '=', $int_type );	
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}
		$query->groupBy('orders.id');
		$query->select ( 'orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.city_name as from_city', 'tc.city_name as to_city','u.username' );
		//Filters values to populate in the page		
		$from_locations[''] = "From Location";
		$to_locations['']	= "To Location";        
        $buyer_name[''] 		= "Buyer";
        $consignee_name['']		= "Consignee";
        // Getting From Locations based on result set	
        $result = $query->get();	
        //echo "<pre>"; print_r($result); die;
		foreach($result as $getData):
			$from_locations[$getData->from_city_id] 		= $getData->from_city;
			$to_locations[$getData->to_city_id] 			= $getData->to_city;
			$buyer_name[$getData->username] 				= $getData->username;
			if(isset($getData->buyer_consignee_name) && $getData->buyer_consignee_name!='')
			$consignee_name[$getData->buyer_consignee_name] = $getData->buyer_consignee_name;	
		endforeach;

		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations 	= CommonComponent::orderArray($to_locations);
		$buyer_name		= CommonComponent::orderArray($buyer_name);
		$consignee_name	= CommonComponent::orderArray($consignee_name);

		// Filters Start
		$filter = \DataFilter::source($query);
		
		$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   		});		
		$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   		});		
   		$filter->add('username', '', 'select')->options($buyer_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});   		
   		$filter->add('buyer_consignee_name', '', 'select')->options($consignee_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	
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
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('username','Buyer Name', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('from_city','From', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('to_city','To', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('order_status','Status', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");
		//Grid data append for columns
		$grid->row( function($row) {
			
			$row->cells[0]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->order_no.'</a>');
			
			$row->cells[1]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->username.'</a>');
			
			$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->from_city.'</a>');
			
			$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->to_city.'</a>');

			//Getting status bar colors progress bar
            $StatusBar = BuyerOrdersComponent :: getStatusProgressbarColor ($row->data->seller_pickup_date, $row->data->dispatch_date, $row->data->seller_delivery_date, $row->data->delivery_date);            
            $status = $row->data->order_status;
            if($status!='')
            	$statusText = $row->data->order_status;
            else
            	$statusText = '';

            $progressStatusBar = '
				<div class="status-block pull-left">
					<div class="status-bar">
					'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'
					<span class="status-text">'. $statusText .'</span>
					</div>
				</div>
			';

			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$progressStatusBar.'</a>');

			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_seller    =   CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);
			$order_id 	= $row->data->id;
			$buyer_id  = $row->data->buyer_id;			

            if($row->data->number_loads!="undefined")
            	$noOfLoads = $row->data->number_loads;
            else
            	$noOfLoads = "N/A";   

            $deliveryDate = CommonComponent::checkAndGetDate($row->data->delivery_date);
				if($deliveryDate!='')
					$delDate = $deliveryDate;
				else
					$delDate  = 'N/A';


			$disDate = CommonComponent::checkAndGetDate($row->data->dispatch_date);
			if($disDate!='')
					$dispatchDate = $disDate;
				else
					$dispatchDate  = 'N/A';

			if($row->data->lkp_order_status_id!= ORDER_CANCELLED) {
				$row->cells[6]->value .= '
				<div class="col-md-2 padding-none text-right action-block">
				<a class="btn red-btn pull-right" href=/consignment_pickup/' . $order_id . '>Update Status</a>
				</div><div class="clearfix"></div>';
			}

			$row->cells[6]->value .= '
        	<div class="pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_seller).'</span></a>
				</div>
			</div>';
			
			$row->cells[6]->value .= '
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$buyer_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
                    <h3>
                        <i class="fa fa-map-marker"></i> '.$row->data->from_city.' to '.$row->data->to_city.'
                        <span class="close-icon">x</span>
                    </h3>
                </div>                
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Dispatch Date</span>
					<span class="data-value">'.$dispatchDate.'</span>
				</div>	
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Delivery Date</span>
					<span class="data-value">'.$delDate.'</span>
				</div>			
				<div class="col-md-3 padding-left-none data-fld">
					<span class="data-head">Consignee</span>
					<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
				</div>
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}


	/**
	 * Relcation Global and office Seller Order List
	 * @ Srinivas Dantha
	 * Date : july 14th,2016
	 */
	
	public static function getRelocelocGlobOfficeSellerOrders() {

        $serviceId = Session::get('service_id');
        $int_type = 1;
        $query = DB::table ( 'orders' );
		$query->leftJoin ( 'order_payments as op', 'orders.order_payment_id', '=', 'op.id' );
		$query->leftJoin ( 'seller_order_invoices as oi', 'orders.id', '=', 'oi.order_id' );
		
		if($serviceId == RELOCATION_GLOBAL_MOBILITY) {
			$query->leftJoin ( 'lkp_cities as tc', 'orders.to_city_id', '=', 'tc.id' );
		} else {
			$query->leftJoin ( 'lkp_cities as fc', 'orders.from_city_id', '=', 'fc.id' );
		}
		$query->leftJoin('users as u', 'u.id', '=', 'orders.buyer_id');
		$query->leftJoin ( 'lkp_order_statuses as os', 'orders.lkp_order_status_id', '=', 'os.id' );
		$query->leftJoin ( 'lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id' );
		$query->where ( 'orders.seller_id', '=', Auth::User ()->id );
		$query->where ( 'orders.lkp_order_status_id', '!=', ORDER_PENDING );		
		$query->where('orders.lkp_service_id', '=', $serviceId);		
		if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $_REQUEST['status_id']);
		}
		$query->groupBy('orders.id');
		if($serviceId == RELOCATION_GLOBAL_MOBILITY) {
			$query->select ( 'orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'tc.city_name as to_city','u.username');
		} else {
			$query->select ( 'orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'fc.city_name as from_city', 'u.username' );
		}		
		//Filters values to populate in the page		
		if($serviceId == RELOCATION_OFFICE_MOVE) {
			$from_locations[''] = "From Location";	
		} else {
			$to_locations['']	= "Location";      
		}
        $buyer_name[''] 		= "Buyer";
        $consignee_name['']		= "Consignee";
        // Getting From Locations based on result set	
        $result = $query->get();	
        //echo "<pre>"; print_r($result); die;
		foreach($result as $getData):
			if($serviceId == RELOCATION_OFFICE_MOVE) {
				$from_locations[$getData->from_city_id] 	= $getData->from_city;
			} else {
				$to_locations[$getData->to_city_id] 		= $getData->to_city;
			}
			$buyer_name[$getData->username] 				= $getData->username;
			if(isset($getData->buyer_consignee_name) && $getData->buyer_consignee_name!='')
			$consignee_name[$getData->buyer_consignee_name] = $getData->buyer_consignee_name;	
		endforeach;

		if(Session::get('service_id') == RELOCATION_OFFICE_MOVE) {
			$from_locations = CommonComponent::orderArray($from_locations);
		} else {
			$to_locations 	= CommonComponent::orderArray($to_locations);
		}
		$buyer_name		= CommonComponent::orderArray($buyer_name);
		$consignee_name	= CommonComponent::orderArray($consignee_name);

		// Filters Start
		$filter = \DataFilter::source($query);
		
		if(Session::get('service_id') == RELOCATION_OFFICE_MOVE) {
			$filter->add('from_city_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.from_city_id', $value):$query;
   			});		
		} else {
			$filter->add('to_city_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('orders.to_city_id', $value):$query;
   			});	
		}	
   		$filter->add('username', '', 'select')->options($buyer_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!='')? $query->where('u.username', $value):$query;
   		});   		
   		$filter->add('buyer_consignee_name', '', 'select')->options($consignee_name)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	
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
		$grid->add('order_no','Order No', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('username','Buyer Name', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	if(Session::get('service_id') == RELOCATION_OFFICE_MOVE) {
	   		$grid->add('from_city','City', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   		$grid->add('to_city','', false)->attributes(array("class" => 'col-md-2 padding-left-none'));
	   	} else {
	   		$grid->add('from_city','', false)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   		$grid->add('to_city','Location', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
	   	}
	   	
	   	
		$grid->add('order_status','Status', true)->attributes(array("class" => 'col-md-2 padding-left-none'));
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");
		//Grid data append for columns
		$grid->row( function($row) {
			
			$row->cells[0]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->order_no.'</a>');
			
			$row->cells[1]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->username.'</a>');
			if(Session::get('service_id') == RELOCATION_OFFICE_MOVE) {
				$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->from_city.'</a>');			
				$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('');
			} else {
				$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('');			
				$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$row->data->to_city.'</a>');
			}

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

            $progressStatusBar = '
				<div class="status-block pull-left">
					<div class="status-bar">
					'.$StatusBar['firstOffColor'].' '.$StatusBar['SecondOffColor'].'
					<span class="status-text">'. $statusText .'</span>
					</div>
				</div>
			';

			$row->cells[4]->attributes(array('class' => 'col-md-2 padding-none'))->value('<a href="'.url('/orders/details/'.$row->data->id).'">'.$progressStatusBar.'</a>');

			$msg_cnt 	= MessagesComponent::getPerticularMessageDetailsCount(null,$row->data->id);
			$docs_seller    =   CommonComponent::getGsaDocuments(3,Session::get ( 'service_id' ),0);
			$order_id 	= $row->data->id;
			$buyer_id  = $row->data->buyer_id;		

			$disDate = CommonComponent::checkAndGetDate($row->data->dispatch_date);
			if($disDate!='')
					$dispatchDate = $disDate;
				else
					$dispatchDate  = 'N/A';

			if (Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY) {
            	$location = $row->data->to_city;
            } else {
				$location = $row->data->from_city;
            }

			if($row->data->lkp_order_status_id!= ORDER_CANCELLED) {
				$row->cells[6]->value .= '
				<div class="col-md-2 padding-none text-right action-block">
				<a class="btn red-btn pull-right" href=/consignment_pickup/' . $order_id . '>Update Status</a>
				</div><div class="clearfix"></div>';
			}

			$row->cells[6]->value .= '
        	<div class="pull-left">
				<div class="info-links">
					<a href="'.url('/getmessagedetails/0/'.$row->data->id.'/0').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.$msg_cnt.'</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
					<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">'.count($docs_seller).'</span></a>
				</div>
			</div>';
			
			$row->cells[6]->value .= '
			<div class="col-md-2 padding-none text-right pull-right">
				<div class="info-links">
					<a id="'.$order_id.'" class="show-data-link">
					<span class="show-icon spot_transaction_details_list">+</span><span class="hide-icon">-</span> Details</a>
					<a href="#" class="new_message" data-userid="'.$buyer_id.'" data-orderid="'.$order_id.'"><i class="fa fa-envelope-o"></i></a>
				</div>
			</div>
			<div class="col-md-12 show-data-div spot_transaction_details_view_list" id="spot_transaction_details_view_"'.$order_id.'">
				<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
                    <h3>
                        <i class="fa fa-map-marker"></i> '.$location.'
                        <span class="close-icon">x</span>
                    </h3>
                </div>';

                if (Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY) {
                	$row->cells[6]->value .= '
                	<div class="col-md-3 padding-left-none data-fld">
						<span class="data-head">Date</span>
						<span class="data-value">'.$dispatchDate.'</span>
					</div>';
                } else {
                	$row->cells[6]->value .= '
					<div class="col-md-3 padding-left-none data-fld">
						<span class="data-head">Dispatch Date</span>
						<span class="data-value">'.$dispatchDate.'</span>
					</div>			
					<div class="col-md-3 padding-left-none data-fld">
						<span class="data-head">Consignee</span>
						<span class="data-value">'.$row->data->buyer_consignee_name.'</span>
					</div>';
                }	
                $row->cells[6]->value .= '			
			</div>
			';
			$row->attributes(array("class" => ""));
		});
		$grid->orderBy('orders.id','desc');
   		$grid->paginate(5);   		
		return ['grid' => $grid, 'filter' => $filter];
	}

	/**
	 * generating consignmentPickup.
	 *
	 * @return void
	 */
	public static function consignmentPickup($request, $id) {
		
		$serviceId = Session::get('service_id');
                //echo "<pre>";print_r($_REQUEST);exit;
                if($serviceId==RELOCATION_GLOBAL_MOBILITY){
                    if (isset($_REQUEST['pick_date']) && $_REQUEST['pick_date'] != "") {
                        $pick_date = str_replace('/', '-', $_REQUEST['pick_date']);
                            $pickup = array(
                                    'seller_pickup_date' => date("Y-m-d", strtotime($pick_date)),
                                    'lkp_order_status_id' => ORDER_CONSIGNMENT_PICKUP,
                            );
                            Order::where("id", $id)->update($pickup);
			CommonComponent::auditLog($id, 'orders');
                    }
                }
                
		if (!empty(Input::all()) && $request->lr_no != "") {
			$created_at = date('Y-m-d H:i:s');
			$createdIp = $_SERVER ['REMOTE_ADDR'];
			$pick_date = str_replace('/', '-', $request->pick_date);
			$lr_date = str_replace('/', '-', $request->lr_date);
                        if($serviceId==RELOCATION_PET_MOVE){
                            $pickup = array(
                                    'lkp_order_status_id' => ORDER_INTRANSIT,
                                    'seller_pickup_lr_number' => $request->lr_no,
                                    'seller_pickup_date' => date("Y-m-d", strtotime($pick_date)),
                                    'seller_pickup_lr_date' => date("Y-m-d", strtotime($lr_date))
                            );
                        }else{
                            $pickup = array(
                                    'lkp_order_status_id' => ORDER_INTRANSIT,
                                    'seller_pickup_lr_number' => $request->lr_no,
                                    'seller_pickup_transport_bill_no' => $request->bill_no,
                                    'seller_pickup_customer_doc_one' => $request->info1,
                                    'seller_pickup_customer_doc_two' => $request->info2,
                                    'seller_pickup_date' => date("Y-m-d", strtotime($pick_date)),
                                    'seller_pickup_lr_date' => date("Y-m-d", strtotime($lr_date))
                            );
                        }
			Order::where("id", $id)->update($pickup);
			CommonComponent::auditLog($id, 'orders');
			//*******Send Sms to Buyer pickup***********************//
			$orderDetails = $roles = DB::table('orders')
										->where(['id' => $id])
										->select('buyer_id','order_no','seller_id')
										->first();

			$msg_params = array(
				'ordernumber' => $orderDetails->order_no,
				'servicename' => CommonComponent::getServiceName($serviceId),
				'datetime' => CommonComponent::convertDateDisplay($pick_date)
			);
			$getMobileNumberbuyer  =   CommonComponent::getMobleNumber($orderDetails->buyer_id);
			$getMobileNumberseller  =   CommonComponent::getMobleNumber($orderDetails->seller_id);
			CommonComponent::sendSMS($getMobileNumberbuyer,CONSIGNMENT_PICK_UP,$msg_params);
			CommonComponent::sendSMS($getMobileNumberseller,CONSIGNMENT_PICK_UP,$msg_params);
			//*******Send Sms to Buyer pickup***********************//

		}
                //adding tracking to DB
                if (!empty(Input::all()) && !empty($_POST ["location"])) {
                    //print_r($_POST);exit;
                    $createdIp = $_SERVER ['REMOTE_ADDR'];
                    $_POST ["date"] = str_replace('/', '-', $_POST ["date"]);
                    \DB::table('order_tracking_details')->insert([
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => Auth::id(),
                        'created_ip' => $createdIp,
                        'tracking_location' => $_POST ["location"],
                        'tracking_date' => date("Y-m-d", strtotime($_POST ["date"])),
                            'order_id' => $_POST ["order_id"]
                    ]);
                    //order status changing
                    Order::where(["id" => $_POST ["order_id"]])->update(
                                array(
                                    'lkp_order_status_id' => ORDER_INTRANSIT,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_ip' => $_SERVER['REMOTE_ADDR'],
                                    'updated_by' => Auth::User()->user_id
                                )
                        );
                    $id = OrderTrackingDetail::orderBy('id')->lists('id')->last();
                    CommonComponent::auditLog($id, 'order_tracking_details');
                }
        
		if (!empty(Input::all()) && $request->tracking_confirm != "") {
			$pickup = array(
				'tracking_confirm' => $request->tracking_confirm
			);
			Order::where("id", $id)->update($pickup);
			//order status changing
			Order::where(["id" => $id])->update(
				array(
					'lkp_order_status_id' => ORDER_REACHED_DESTINATION,
					'updated_at' => date('Y-m-d H:i:s'),
					'updated_ip' => $_SERVER['REMOTE_ADDR'],
					'updated_by' => Auth::User()->user_id
				)
			);
			CommonComponent::auditLog($id, 'orders');
		}
		if (!empty(Input::all()) && $request->vehicle_confirm != "") {
			$pickup = array(
				'vehicle_confirm' => $request->vehicle_confirm,
				'vehicle_confirmed_on' => date('Y-m-d H:i:s')
			);
                        
			Order::where("id", $id)->update($pickup);
			CommonComponent::auditLog($id, 'orders');
			//order status changing
                        if($serviceId==ROAD_TRUCK_HAUL ||  $serviceId==ROAD_TRUCK_LEASE){
                            Order::where(["id" => $id])->update(
                                    array(
                                            'lkp_order_status_id' => ORDER_CONSIGNMENT_PICKUP,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_ip' => $_SERVER['REMOTE_ADDR'],
                                            'updated_by' => Auth::User()->user_id
                                    )
                            );
                        }
		}
		// seller Delivery Details insertion to db
		if (!empty(Input::all()) && $request->delivery_date != "") {
                    $delivery_date = str_replace('/', '-', $request->delivery_date);
                    if($serviceId==ROAD_TRUCK_HAUL ){
                        $delivery_time =  $request->delivery_time;
			$pickup = array(
				'seller_delivery_address' => $request->delivery_address,
				'seller_delivery_driver_name' => $request->delivery_driver,
				'seller_delivery_additional_details' => $request->delivery_info,
				'seller_delivery_date' => date("Y-m-d", strtotime($delivery_date))." ".date("H:i:s", strtotime($delivery_time))
			);
                    }elseif($serviceId==ROAD_TRUCK_LEASE){
                        $delivery_time =  $request->delivery_time;
			$pickup = array(
				'seller_delivery_address' => $request->delivery_address,
				'seller_delivery_driver_name' => $request->delivery_driver,
				'seller_delivery_additional_details' => $request->delivery_info,
                                'open_km_reading' => $request->open_reading,
				'seller_delivery_date' => date("Y-m-d", strtotime($delivery_date))." ".date("H:i:s", strtotime($delivery_time))
			);
                    }elseif($serviceId==RELOCATION_GLOBAL_MOBILITY){
			$pickup = array(
				'seller_delivery_date' => date("Y-m-d", strtotime($delivery_date)),
                                'seller_delivery_additional_details' => $request->delivery_info,
			);
                    }else{
			
			$pickup = array(
				'seller_delivery_driver_name' => $request->delivery_driver,
				'seller_delivery_recipient_mobile' => $request->delivery_mobile,
				'seller_delivery_frieght_amt' => $request->freight_amt,
				'seller_delivery_additional_details' => $request->delivery_info,
				'seller_delivery_date' => date("Y-m-d", strtotime($delivery_date))
			);
                    }
			Order::where("id", $id)->update($pickup);
			CommonComponent::auditLog($id, 'orders');
			//order status changing
			Order::where(["id" => $id])->update(
				array(
					'lkp_order_status_id' => ORDER_DELIVERED,
					'updated_at' => date('Y-m-d H:i:s'),
					'updated_ip' => $_SERVER['REMOTE_ADDR'],
					'updated_by' => Auth::User()->user_id
				)
			);
			//*******Send Sms to Buyer pickup***********************//
			$orderDetails = $roles = DB::table('orders')
				->where(['id' => $id])
				->select('buyer_id','order_no','seller_id')
				->first();

			$msg_params = array(
				'ordernumber' => $orderDetails->order_no,
				'servicename' => CommonComponent::getServiceName($serviceId),
				'datetime' => CommonComponent::convertDateDisplay($delivery_date)
			);
			$getMobileNumberbuyer  =   CommonComponent::getMobleNumber($orderDetails->buyer_id);
			$getMobileNumberseller  =   CommonComponent::getMobleNumber($orderDetails->seller_id);
			CommonComponent::sendSMS($getMobileNumberbuyer,CONSIGNMENT_DELIVERED,$msg_params);
			CommonComponent::sendSMS($getMobileNumberseller,CONSIGNMENT_DELIVERED,$msg_params);
			//*******Send Sms to Buyer pickup***********************//

			$payment = \DB::table('orders')->leftjoin('order_payments', 'orders.order_payment_id', '=', 'order_payments.id')->leftjoin('lkp_payment_modes', 'lkp_payment_modes.id', '=', 'order_payments.lkp_payment_mode_id')->where('orders.id', $id)->select('order_payments.lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')->first();
                        if( $serviceId!=RELOCATION_GLOBAL_MOBILITY)
			SellerOrderComponent::addSellerInvoice($id,$payment->lkp_payment_mode_id);
                        if($serviceId==ROAD_TRUCK_HAUL ){
                            SellerOrderComponent::addReceipt($id);
                        }
		}
		// seller Receipt Details insertion to db
		if (!empty(Input::all()) && $request->receipts != "") {
			SellerOrderComponent::addReceipt($id);
		}


		return true;

	}

	/**
	 * generating invoice to buyer in consignmentPickup.
	 *
	 * @return void
	 */
	public static function addInvoice($id,$serviceId,$paymentMode) {
		Log::info('Create Invoice is initiated by user: ' . Auth::id(), array(
			'c' => '1'
		));
		CommonComponent::activityLog("ADD_INVOICE", ADD_INVOICE, 0, HTTP_REFERRER, CURRENT_URL);
		$order = Order::where('id', $id)->first();

		$created_at = date('Y-m-d H:i:s');
		$createdIp = $_SERVER ['REMOTE_ADDR'];
		$inv = new OrderInvoice ();
		$inv->created_at = $created_at;
		$inv->created_by = Auth::id();
		$inv->created_ip = $createdIp;
		
        $invid  =   CommonComponent::getInvID();
        $created_year = date('Y');            
            
		switch ($serviceId) {			
			case ROAD_FTL :
                            $randString = 'FTL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;
                           
				            //echo $html;
                            break;
			case ROAD_PTL :
                            $randString = 'LTL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;
                            
                            
                           // $html = 'pdf.invoice_ptl';
                            break;
            case RAIL :
                            $randString = 'RAIL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;

                            break;
            case AIR_DOMESTIC :
                            $randString = 'AIRDOMESTIC/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;
                            
                            
                            break;
            case OCEAN :
                            $randString = 'OCEAN/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;
                            
                            break;
            case AIR_INTERNATIONAL :
                            $randString = 'AIRINTERNATIONAL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;
                            
                            break;
			case ROAD_INTRACITY :
                            $randString = 'INTRA/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;
                            break;	
            case COURIER :
                            $randString = 'COURIER/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;
                            
                            break;
            case RELOCATION_DOMESTIC :
                            $randString = 'RD/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;
                            break;
            case RELOCATION_INTERNATIONAL :
                            $randString = 'REL-INT/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;
                            break;     
            case RELOCATION_OFFICE_MOVE :
                            $randString = 'REL-OFF/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;
                            break;       
            case RELOCATION_PET_MOVE :
                            $randString = 'RELOCATIONPETMOVE/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;
                            break;  
            case RELOCATION_GLOBAL_MOBILITY :
                            $randString = 'RELOCATIONGM/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;
                            break;            
            case ROAD_TRUCK_HAUL :
                            $randString = 'TRUCKHAUL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;                            
                            break;  
            case ROAD_TRUCK_LEASE :
                            $randString = 'TRUCKLEASE/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;                            
                            break;        
		}
		$inv->order_id = $id;
		$inv->from_user_id = Auth::id();
		$inv->to_user_id = $order->buyer_id;
		$inv->frieght_amt = $order->price;
		$inv->insurance = "0.00";
                if(SHOW_SERVICE_TAX){
                    if($paymentMode == CASH_ON_DELIVERY || $paymentMode == CASH_ON_PICKUP){
                       $inv->service_tax_amount = 0.00;
                    }else{
                       if(CommonComponent::getServiceGroupID($serviceId)==TRANSPORT){
                                           $inv->service_tax_amount =   PERCENT14*((PERCENT40*($order->price))/10000);
                                   }elseif(CommonComponent::getServiceGroupID($serviceId)==OTHERS){
                                           $inv->service_tax_amount =   PERCENT14*($order->price/100);
                                   }
                    }
                }else{
                    $inv->service_tax_amount = 0.00;
                }	
		$inv->total_amt =$inv->frieght_amt + $inv->service_tax_amount;

        if($inv->save()){
        
        $pdfhtml=CheckoutComponent::getBuyerInvoice($id, $serviceId);
		CommonComponent::auditLog($id, 'order_invoices');
        }

		//sending mail to user
		$users = DB::table('users')->where('id', $order->buyer_id)->get();
		$users[0]->invoice_no   = $inv->invoice_no;
		$users[0]->frieght_amt  = number_format((float)$inv->frieght_amt,2,'.',',');
		$users[0]->service_tax    = number_format((float)$inv->service_tax_amount,2,'.',',');
		$users[0]->insurance    = $inv->insurance;
		$users[0]->total_amt    = number_format((float)$inv->total_amt,2,'.',',');
		//pdf generate
        $sellerDirectory = 'uploads/buyer/' . Auth::id() ;
			
			if (!is_dir ( $sellerDirectory )) {
			
                   mkdir ( $sellerDirectory, 0777, true );
			}
                        $uniqueFileName = time() ."GeneratedInvoice.pdf";
               			$data=array();
                        $pdf = PDF::loadHTML($pdfhtml, $data);
                        $pdf->save($sellerDirectory.'/'.$uniqueFileName);
                         
                
       	$path = $sellerDirectory.'/'.$uniqueFileName;        
		CommonComponent::send_email(FTL_ORDER_INVOICE, $users, '1', $path,true);

	}

	/**
	 * generating invoice to seller in consignmentPickup.
	 *
	 * @return void
	 */
	public static function addSellerInvoice($id,$paymentMode) {
		Log::info('Create Invoice is initiated by user: ' . Auth::id(), array(
			'c' => '1'
		));
		CommonComponent::activityLog("ADD_SELLER_INVOICE", ADD_SELLER_INVOICE, 0, HTTP_REFERRER, CURRENT_URL);
		$order = Order::where('id', $id)->first();
		$servicecharges = LkpServiceCharges::where(array(
			'lkp_service_id' => Session::get('service_id'),
			'is_active' => '1'
		))->first();
		$created_at = date('Y-m-d H:i:s');
		$createdIp = $_SERVER ['REMOTE_ADDR'];
		$inv = new SellerOrderInvoice ();
		$inv->created_at = $created_at;
		$inv->created_by = Auth::id();
		$inv->created_ip = $createdIp;
		$invid  =   CommonComponent::getSellerInvID();
        $created_year = date('Y');
		$serviceId = Session::get('service_id');
		switch ($serviceId) {
			case ROAD_FTL :
                            $randString = 'FTL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;
                           
                            break;
			case ROAD_PTL :
                            $randString = 'LTL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                            $inv->invoice_no = $randString;                            
                            break;
            case RAIL :
                $randString = 'RAIL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                $inv->invoice_no = $randString;                            
                break;
            case AIR_DOMESTIC :
                $randString = 'AIRDOMESTIC/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                $inv->invoice_no = $randString;                            
                break;
            case OCEAN :
                $randString = 'OCEAN/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                $inv->invoice_no = $randString;                            
                break;
            case AIR_INTERNATIONAL :
                $randString = 'AIRINTERNATIONAL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                $inv->invoice_no = $randString;                            
                break;
            case ROAD_INTRACITY :
                $randString = 'INTRA/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                $inv->invoice_no = $randString;
                break;
            case COURIER :
                $randString = 'COURIER/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                $inv->invoice_no = $randString;
                break;
            case RELOCATION_DOMESTIC :
                $randString = 'RD/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                $inv->invoice_no = $randString;
                break;
            case RELOCATION_INTERNATIONAL :
                $randString = 'REL-INT/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                $inv->invoice_no = $randString;
                break;
            case RELOCATION_OFFICE_MOVE :
                $randString = 'REL-OFF/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                $inv->invoice_no = $randString;
                break;
            case RELOCATION_PET_MOVE :
                $randString = 'RELOCATIONPETMOVE/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                $inv->invoice_no = $randString;
                break;
            case ROAD_TRUCK_HAUL:
                $randString = 'TRUCKHAUL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                $inv->invoice_no = $randString;
                break;
            case ROAD_TRUCK_LEASE :
                $randString = 'TRUCKLEASE/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                $inv->invoice_no = $randString;
                break; 
            case RELOCATION_GLOBAL_MOBILITY :
                $randString = 'RELOCATIONGM/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT); 
                $inv->invoice_no = $randString;
                break;
			default:$inv->invoice_no = "FTL/" . $str;break;
		}
		$inv->order_id = $id;
		$inv->user_id = Auth::id();
		$inv->lkp_service_charge_id = $servicecharges->id;
		if(SHOW_SERVICE_TAX){
                        if($paymentMode == CASH_ON_DELIVERY || $paymentMode == CASH_ON_PICKUP){
                            $inv->service_tax_amount = 0.00;
                            $inv->service_charge_amount = 0.00;
                        }else{
                        $inv->service_charge_amount =PERCENT4*($order->price/100);
                                $inv->service_tax_amount =   PERCENT14*($inv->service_charge_amount/100);
                        }
                }else{
                    $inv->service_tax_amount = 0.00;
                    if($paymentMode == CASH_ON_DELIVERY || $paymentMode == CASH_ON_PICKUP){
                        $inv->service_charge_amount = 0.00;
                    }else{
                        $inv->service_charge_amount =PERCENT4*($order->price/100);
                    }
                }
		$inv->total_amount =   $inv->service_tax_amount + $inv->service_charge_amount;
		
		if($inv->save()){
		
			$pdfhtml=CheckoutComponent::getSellerInvoice($id, $serviceId);
			CommonComponent::auditLog($id, 'seller_order_invoices');
		}
		
		//sending mail to user
		$users = DB::table('users')->where('id', Auth::id())->get();
		$users[0]->invoice_no   = $inv->invoice_no;
		$users[0]->service_charge  = number_format((float)$inv->service_charge_amount,2,'.',',');
		$users[0]->service_tax    = number_format((float)$inv->service_tax_amount,2,'.',',');
		$users[0]->total_amount    = number_format((float)$inv->total_amount,2,'.',',');
		
		$sellerDirectory = 'uploads/seller/' . Auth::id() ;
			
		if (!is_dir ( $sellerDirectory )) {
				
			mkdir ( $sellerDirectory, 0777, true );
		}
		$uniqueFileName = time()."GeneratedInvoice.pdf";
		$data=array();
		$pdf = PDF::loadHTML($pdfhtml, $data);
		$pdf->save($sellerDirectory.'/'.$uniqueFileName);
		 
		$path = $sellerDirectory.'/'.$uniqueFileName;
		
		CommonComponent::send_email(FTL_SELLER_ORDER_INVOICE, $users,'1', $path,true);
	}

	/**
	 * generating invoice to seller in consignmentPickup.
	 *
	 * @return void
	 */
	public static function addReceipt($id) {
		Log::info('Create Receipt is initiated by user: ' . Auth::id(), array(
			'c' => '1'
		));
		CommonComponent::activityLog("ADD_RECEIPT", ADD_RECEIPT, 0, HTTP_REFERRER, CURRENT_URL);
		$order = Order::where('id', $id)->first();
		$created_at = date('Y-m-d H:i:s');
		$createdIp = $_SERVER ['REMOTE_ADDR'];
		$inv = new OrderReceipt ();
		$inv->created_at = $created_at;
		$inv->created_by = Auth::id();
		$inv->created_ip = $createdIp;
		
		$serviceId = Session::get('service_id');
                $created_year = date('Y');
                $recid  =   CommonComponent::getSellerReceiptID();
		switch ($serviceId) {
			case ROAD_FTL :
                $inv->receipt_no = 'FTL/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break;
			case ROAD_PTL :
                $inv->receipt_no = 'LTL/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break;
            case RAIL :
                $inv->receipt_no = 'RAIL/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break;
            case AIR_DOMESTIC :
                $inv->receipt_no = 'AIRDOMESTIC/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break;
            case AIR_INTERNATIONAL :
                $inv->receipt_no = 'AIRINTERNATIONAL/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break;
            case OCEAN :
                $inv->receipt_no = 'OCEAN/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break;
			case COURIER :
                $inv->receipt_no = 'COURIER/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break;
            case RELOCATION_DOMESTIC :
                $inv->receipt_no = 'RD/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break; 
            case RELOCATION_INTERNATIONAL :
                $inv->receipt_no = 'REL-INT/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break;   
            case RELOCATION_OFFICE_MOVE :
                $inv->receipt_no = 'REL-OFF/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break;
            case RELOCATION_PET_MOVE :
                $inv->receipt_no = 'RELOCATIONPETMOVE/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break;   
            case ROAD_TRUCK_HAUL :
                $inv->receipt_no = 'TRUCKHAUL/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break;
            case ROAD_TRUCK_LEASE :
                $inv->receipt_no = 'TRUCKLEASE/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break;
            case RELOCATION_GLOBAL_MOBILITY :
                $inv->receipt_no = 'RELOCATIONGM/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
                break; 
			default:
            $inv->receipt_no = 'FTL/' .$created_year .'/'. str_pad($recid, 6, "0", STR_PAD_LEFT); 
            break;
		}

		$inv->order_id = $id;
		$inv->user_id = Auth::id();
		$inv->frieght_amount = $order->price;
		$inv->insurance = "0.00";
		$inv->service_charge_amount = "0.00";
		$inv->service_tax_amount = "0.00";
		$inv->total_amount = $order->price + $inv->service_charge_amount + $inv->service_tax_amount;
		$inv->save();
		CommonComponent::auditLog($id, 'order_receipts');

		//sending mail to user
		$users = DB::table('users')->where('id', Auth::id())->get();
		$users[0]->receipt_no   = $inv->receipt_no;
		$users[0]->frieght_amt  = number_format((float)$inv->frieght_amount,2,'.',',');
		$users[0]->insurance    = number_format((float)$inv->insurance,2);
		$users[0]->service_charge   = number_format((float)$inv->service_charge_amount,2);
		$users[0]->service_tax      = number_format((float)$inv->service_tax_amount,2);
		$users[0]->total_amount     = number_format((float)$inv->total_amount,2,'.',',');

		CommonComponent::send_email(FTL_SELLER_ORDER_RECEIPT, $users);

	}
        
        public static function relocationIntOrderDetails($request,$id,$order,$invoice,$receipt,$locations,$payment){
            
            if(isset($order->buyer_quote_id)&& $order->buyer_quote_id!=0 && $order->buyer_quote_id!=""){				
            	
                if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                    $post = \DB::table('relocationint_buyer_posts as bq')
                        ->leftjoin('lkp_cities as c1', 'bq.from_location_id', '=', 'c1.id')
                        ->leftjoin('lkp_cities as c2', 'bq.to_location_id', '=', 'c2.id')
                        ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bq.lkp_post_status_id')
                        ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                        ->where('bq.id', $order->buyer_quote_id)
                        ->select('bq.from_location_id','bq.to_location_id','bq.id','bq.lkp_property_type_id','bq.lkp_international_type_id','bq.total_cartons_weight','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery', 'bq.transaction_id as transid','c1.city_name as from', 'c2.city_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
                  }
            }elseif(isset($order->buyer_quote_item_id)&& $order->buyer_quote_item_id!=0 && $order->buyer_quote_item_id!=""){
            	
	            if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==2){
	            $post = \DB::table('term_buyer_quote_items as bqi')
	            ->leftjoin('term_buyer_quotes as bq', 'bq.id', '=', 'bqi.term_buyer_quote_id')
	            //->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
	            //->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'bqi.lkp_vehicle_type_id')
	            ->leftjoin('lkp_cities as c1', 'bqi.from_location_id', '=', 'c1.id')
	            ->leftjoin('lkp_cities as c2', 'bqi.to_location_id', '=', 'c2.id')
	            ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
	            ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
	            ->where('bqi.id', $order->buyer_quote_item_id)
	            ->select('bqi.from_location_id','bqi.to_location_id','bq.from_date as dispatch', 'bq.to_date as delivery', 'bq.transaction_id as transid', 'c1.city_name as from', 'c2.city_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
	            }
            }
            if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                $tracking    = DB::table('relocationint_seller_posts as sp')
                                ->where('sp.id', $order->seller_post_item_id)
                                ->select('sp.tracking')->first();
                $tracking=$tracking->tracking;
            }else{
                $tracking=1;
            }
                return view('relocation.orders.seller_consignment_pickup', array(
                'order' => $order,
                'post' => $post,
                //'vehicles' => $vehicles,
                'pickExist' => $order->seller_pickup_lr_number,
                'deliveryExist' => $order->seller_delivery_driver_name,
                'trackingExist' => $order->tracking_confirm,
                //'vehicleExist' => $order->vehicle_confirm,
                'invoiceExist' => $invoice->invoice_no,
                'receiptExist' => $receipt->receipt_no,
                'locations' => $locations,
                'invoice' => $invoice,
                'receipt' => $receipt,
                'payment_mode' => $payment->payment_mode,
                'tracking'=>$tracking
                ));                
        }

}
