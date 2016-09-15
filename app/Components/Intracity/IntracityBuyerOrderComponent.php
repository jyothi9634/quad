<?php

namespace App\Components\intracity;

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

class IntracityBuyerOrderComponent {

    /**
     * Buyer Orders List Page
     * Retrieval of data related to Buyer Orders list items to populate in the Orders list widget
     * Displays a grid with a list of all Buyer Orders
     */
    public static function getBuyerOrdersList($post, $data) {

        $buyers = array("" => "Vehicle No");


        // query to retrieve seller posts list and bind it to the grid
        $query = DB::table('orders');
        $query->leftJoin('order_invoices as oi', 'oi.id', '=', 'orders.order_invoice_id');        
        $query->leftJoin('lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id');
        $query->leftJoin('lkp_ict_vehicles as iv', 'iv.id', '=', 'orders.lkp_ict_vehicle_id');
        $query->leftJoin('lkp_order_statuses as os', 'os.id', '=', 'orders.lkp_order_status_id');
        $query->where('orders.buyer_id', '=', Auth::user()->id);        
        
        //conditions to make search
        if (isset($post['lkp_order_type_id']) && $post['lkp_order_type_id'] != '') {
            $query->where('orders.lkp_order_type_id', $post['lkp_order_type_id']);
        }
        
        if (isset($post['status_id']) && $post['status_id'] != '') {
            $query->where('orders.lkp_order_status_id', $post['status_id']);
        }
        if (Session::get('service_id') != '') {
            $query->where('orders.lkp_service_id', Session::get('service_id'));
        }
        //FILTERS        
        if (isset($post['lkp_ict_vehicle_id']) && $post['lkp_ict_vehicle_id'] != '') {
            $query->where('orders.lkp_ict_vehicle_id', $post['lkp_ict_vehicle_id']);
        }
        if (isset ( $post ['start_dispatch_date'] ) && $post ['start_dispatch_date'] != '') {
          $query->where ( 'orders.dispatch_date', '>=', CommonComponent::convertDateForDatabase($post ['start_dispatch_date'] ));
            //$from_date = $_GET ['start_dispatch_date'];
        }
        if (isset ( $post ['end_dispatch_date'] ) && $post ['end_dispatch_date'] != '') {
          $query->where ( 'orders.dispatch_date', '<=', CommonComponent::convertDateForDatabase($post ['end_dispatch_date'] ));
           //$to_date = $_GET ['end_dispatch_date'];
        }         
        $orderresults = $query->select('orders.*', 'os.order_status', 'oi.invoice_no as invoice_no', 'iv.driver_name', 'iv.vehicle_number', 'iv.mobile_number')->get();

        
        //Functionality to handle filters based on the selection starts
        foreach ($orderresults as $order) {
            if (!isset($buyers[$order->lkp_ict_vehicle_id])) {
                $buyers[$order->lkp_ict_vehicle_id] = DB::table('lkp_ict_vehicles')->where('id', $order->lkp_ict_vehicle_id)->pluck('vehicle_number');
            }
        }
        //Functionality to handle filters based on the selection ends

        $grid = DataGrid::source($query);
        $grid->attributes(array("class" => "table table-striped"));

        $grid->add('id', 'ID', false)->style('display:none');
        $grid->add('vehicle_number', 'Vehicle Number', 'vehicle_number')->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add('driver_name', 'Driver Name', 'driver_name')->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add('mobile_number', 'Mobile Number', 'mobile_number')->attributes(array("class" => "col-md-2 padding-left-none"));        
        $grid->add('order_no|strip_tags', 'Order No', 'order_no')->attributes(array("class" => "col-md-4 padding-left-none"));
        $grid->add('invoice_no', 'Invoice No', 'invoice_no')->attributes(array("class" => "col-md-2 hidden-md hidden-lg padding-left-none"));
        $grid->add('order_status', 'Status', 'order_status')->attributes(array("class" => "col-md-2 col-sm-2 col-xs-3 hidden-xs padding-none"));
        $grid->add('a', '', '');
        $grid->orderBy('id', 'desc');
        $grid->paginate(5);
        $grid->row(function ($row) {
            
            $order_id = $row->cells [0]->value;
            $data_link = url()."/orders/buyer_orderdetails/$order_id";            
            $row->cells [0]->style('display:none');
            $row->cells [1]->attributes(array("class" => "col-md-2 padding-left-none"));
            $row->cells [2]->attributes(array("class" => "col-md-2 padding-left-none"));
            $row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none"));
            $row->cells [4]->attributes(array("class" => "col-md-4 padding-left-none"));
            $row->cells [5]->attributes(array("class" => "col-md-2 hidden-sm hidden-md hidden-lg padding-left-none"));
            $row->cells [6]->attributes(array("class" => "col-md-2 padding-none pull-left"));            
            $status = $row->cells [6]->value;
            if ($status == 'Order Placed')
                $str = 'complete-15';
            elseif ($status == 'Pickup due')
                $str = 'complete-30';
            elseif ($status == 'Consignment pickup')
                $str = 'complete-45';
            elseif ($status == 'In transit')
                $str = 'complete-60';
            elseif ($status == 'Reached destination')
                $str = 'complete-75';
            elseif ($status == 'Delivered')
                $str = 'complete-90';
            elseif ($status == 'Closed')
                $str = 'complete-100';
            elseif ($status == 'Cancelled')
                $str = 'cancelled';
            else
                $str="";

            	$str = '';
            
            $row->cells [1]->value = $row->cells [1]->value;
            $row->cells[2]->value = $row->cells[2]->value;
            $row->cells[3]->value = $row->cells[3]->value;
            $row->cells [4]->value = $row->cells [4]->value;
            //$row->cells[5]->value = '' . $row->cells[5]->value . '';
            $row->cells[6]->value = '
            		
            		<div class="col-md-2 padding-none status-block pull-left">
											<div class="status-bar">
												<div class="status-bar"></div>
												<span class="status-text">'.$status.'</span>
											</div>
										</div>';
            

            $row->attributes(array("class" => "col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none table-row html_link","data_link"=>$data_link));
        });

        //Functionality to build filters in the page starts
        $filter = DataFilter::source($query);        
        $filter->add('orders.lkp_ict_vehicle_id', 'Seller', 'select')->options($buyers)->attr("class", "form-control form-control1")->attr("onchange", "this.form.submit()");       
        $filter->add('orders.order_no', 'Order No', 'text')->attr("class", "form-control form-control1")->attr("onchange", "this.form.submit()");

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
     * Buyer Orders Detail Page
     * Retrieval of data related to Buyer Orders
     * 
     */
    public static function getBuyerOrderDetails($serviceId, $orderId, $user_id) {
        $orders = array();
        $orders['orderDetails'] = DB::table('orders')
                //->leftJoin('order_payments as op', 'orders.order_payment_id', '=', 'op.id')
                //->leftjoin('lkp_payment_modes', 'lkp_payment_modes.id', '=', 'op.lkp_payment_mode_id')
                ->leftJoin('order_invoices as oi', 'orders.id', '=', 'oi.order_id')
                ->leftJoin('lkp_ict_locations as lc', 'orders.from_city_id', '=', 'lc.id')
                ->leftJoin('lkp_ict_locations as lcity', 'orders.to_city_id', '=', 'lcity.id')
                ->leftJoin('lkp_cities as city', 'lcity.lkp_city_id', '=', 'city.id')
                ->leftJoin('lkp_order_statuses as os', 'orders.lkp_order_status_id', '=', 'os.id')
                ->leftJoin('pickup_vehicle_details as pvd', 'orders.id', '=', 'pvd.order_id')->
                // ->leftJoin ( 'order_refunds as or', 'orders.id', '=', 'or.order_id' )
                leftJoin('lkp_vehicle_types as lvt', 'orders.lkp_vehicle_type_id', '=', 'lvt.id')
                ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id')
                ->leftJoin('order_receipts as or', 'orders.id', '=', 'or.order_id')
                ->leftjoin('ict_buyer_quote_items', 'ict_buyer_quote_items.id', '=', 'orders.buyer_quote_item_id')
                ->leftjoin('ict_buyer_quotes', 'ict_buyer_quotes.id', '=', 'ict_buyer_quote_items.buyer_quote_id')
                ->leftjoin('users as u', 'u.id', '=', 'orders.buyer_id')
                ->leftjoin('lkp_ict_vehicles as ictv', 'ictv.id', '=', 'orders.lkp_ict_vehicle_id')
                ->where('orders.buyer_id', '=', $user_id)
                ->where('orders.id', '=', $orderId)
                ->select('orders.*', 'orders.id as orderid', 'orders.price as orderprice', 'u.username', 'ict_buyer_quotes.transaction_id as trans_id', 'lkp_load_types.load_type', 'ict_buyer_quote_items.*', 'or.*', 'oi.*', 'os.order_status', 'lc.ict_location_name as from_city', 'lcity.ict_location_name as to_city', 'pvd.*', 'lvt.*', 'city.city_name as city', 'ictv.vehicle_number as vehicle','ictv.driver_name as driver_name','ictv.mobile_number as driver_number')
                ->first();
        $orders['priceDetails'] = DB::table('orders')->
                // ->leftJoin ( 'order_refunds as or', 'orders.id', '=', 'or.order_id' )
                //leftJoin('buyer_quote_sellers_quotes_prices as qp', 'orders.buyer_id', '=', 'qp.buyer_id', 'orders.buyer_quote_item_id', '=', 'qp.buyer_quote_item_id', 'orders.seller_post_item_id', '=', 'qp.seller_post_item_id')
                leftJoin('ict_buyer_quote_sellers_quotes_prices as qp', function($join) {
                    $join->on('orders.buyer_id', '=', 'qp.buyer_id');
                    $join->on('orders.buyer_quote_item_id', '=', 'qp.buyer_quote_item_id');
                    //$join->on('orders.seller_post_item_id', '=', 'qp.seller_post_item_id');
                })
                ->where('orders.buyer_id', '=', $user_id)
                ->where('orders.id', '=', $orderId)
                ->select('qp.initial_quote_price', 'qp.counter_quote_price', 'qp.final_quote_price', 'qp.initial_quote_created_at', 'qp.counter_quote_created_at', 'qp.final_quote_created_at')
                ->first();
        return $orders;
    }

}
