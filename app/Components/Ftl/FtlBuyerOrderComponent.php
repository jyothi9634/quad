<?php
namespace App\Components\Ftl;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use App\Components\CommonComponent;
use App\Components\Rail\RailBuyerGetQuoteBooknowComponent;
use App\Components\AirDomestic\AirDomesticBuyerGetQuoteBooknowComponent;
use App\Components\Term\TermBuyerComponent;
use App\Models\User;
use App\Models\FtlSearchTerm;
use App\Components\MessagesComponent;

class FtlBuyerOrderComponent {
        
     /**
	 * Buyer Orders Detail Page
	 * Retrieval of data related to Buyer Orders
	 * 
	 */
	public static function getBuyerOrderDetails($serviceId, $orderId, $user_id) {
            $orders=array();
            $query = DB::table('orders');
            //$orders['orderDetails'] = DB::table('orders')->leftJoin('order_payments as op', 'orders.order_payment_id', '=', 'op.id');
           $query->leftJoin('order_payments as op', 'orders.order_payment_id', '=', 'op.id')
           ->leftjoin('lkp_payment_modes', 'lkp_payment_modes.id', '=', 'op.lkp_payment_mode_id')
           ->leftJoin('order_invoices as oi', 'orders.id', '=', 'oi.order_id');
           $serviceId = Session::get('service_id');
                switch ($serviceId) {
                    case ROAD_FTL :
                    $query->leftJoin('lkp_cities as lc', 'lc.id', '=', 'orders.from_city_id');
                    $query->leftJoin('lkp_cities as lcity', 'lcity.id', '=', 'orders.to_city_id');                    
                    
                    $query->leftJoin('buyer_quote_items as bqi', function($join)
                         {
                             $join->on('orders.buyer_quote_item_id', '=', 'bqi.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('term_buyer_quote_items as tbqi', function($join)
                         {
                             $join->on('orders.buyer_quote_item_id', '=', 'tbqi.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         });
                    $query->leftJoin('buyer_quotes as bq', function($join)
                         {
                             $join->on('bqi.buyer_quote_id', '=', 'bq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('term_buyer_quotes as tbq', function($join)
                         {
                             $join->on('tbqi.term_buyer_quote_id', '=', 'tbq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         });
                   
                    break;
                    case ROAD_PTL :
                    $query->leftJoin('lkp_ptl_pincodes as lp', 'lp.id', '=', 'orders.from_city_id');
                    $query->leftJoin('lkp_ptl_pincodes as lcityp', 'lcityp.id', '=', 'orders.to_city_id');
                    $query->leftJoin('ptl_buyer_quotes as bq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'bq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('term_buyer_quotes as tbq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'tbq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         });
                    $query->leftJoin('ptl_buyer_quote_items as bqi', function($join)
                         {
                             $join->on('bq.id', '=', 'bqi.buyer_quote_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('term_buyer_quote_items as tbqi', function($join)
                         {
                             $join->on('tbq.id', '=', 'tbqi.term_buyer_quote_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         });
                     
                    break;
                    case RAIL :
                    $query->leftJoin('lkp_ptl_pincodes as lp', 'lp.id', '=', 'orders.from_city_id');
                    $query->leftJoin('lkp_ptl_pincodes as lcityp', 'lcityp.id', '=', 'orders.to_city_id');
                    $query->leftJoin('rail_buyer_quotes as bq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'bq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('term_buyer_quotes as tbq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'tbq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         });
                    $query->leftJoin('rail_buyer_quote_items as bqi', function($join)
                         {
                             $join->on('bq.id', '=', 'bqi.buyer_quote_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('term_buyer_quote_items as tbqi', function($join)
                         {
                             $join->on('tbq.id', '=', 'tbqi.term_buyer_quote_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         });
                     
                    break;
                    case AIR_DOMESTIC :
                    $query->leftJoin('lkp_ptl_pincodes as lp', 'lp.id', '=', 'orders.from_city_id');
                    $query->leftJoin('lkp_ptl_pincodes as lcityp', 'lcityp.id', '=', 'orders.to_city_id');
                    $query->leftJoin('airdom_buyer_quotes as bq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'bq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('term_buyer_quotes as tbq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'tbq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         });
                    $query->leftJoin('airdom_buyer_quote_items as bqi', function($join)
                         {
                             $join->on('bq.id', '=', 'bqi.buyer_quote_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('term_buyer_quote_items as tbqi', function($join)
                         {
                             $join->on('tbq.id', '=', 'tbqi.term_buyer_quote_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         });
                     
                    break;
                    case COURIER :
                    	$query->leftJoin('lkp_ptl_pincodes as lp', 'lp.id', '=', 'orders.from_city_id');
                    	
                    	$query->leftJoin('courier_buyer_quotes as bq', function($join)
                    	{
                    		$join->on('orders.buyer_quote_id', '=', 'bq.id');
                    		$join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                    		 
                    	});
                    	
                    	$query->leftJoin('courier_buyer_quote_items as bqi', function($join)
                    	{
                    		$join->on('bq.id', '=', 'bqi.buyer_quote_id');
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
                    	
                    	
                    	$query->leftJoin('lkp_ptl_pincodes as lcityp', function($join)
                    	{
                    		$join->on('orders.to_city_id', '=', 'lcityp.id');
                    		$join->on(DB::raw('bqi.lkp_courier_delivery_type_id'),'=',DB::raw(1));
                    		 
                    	});
                    	$query->leftJoin('lkp_countries as lcs', function($join)
                    	{
                    		$join->on('orders.to_city_id', '=', 'lcs.id');
                    		$join->on(DB::raw('bqi.lkp_courier_delivery_type_id'),'=',DB::raw(2));
                    		 
                    	});
                    	$query->leftJoin('lkp_ptl_pincodes as lcitypt', function($join)
                    	{
                    		$join->on('orders.to_city_id', '=', 'lcitypt.id');
                    		$join->on(DB::raw('tbq.lkp_courier_delivery_type_id'),'=',DB::raw(1));
                    		 
                    	});
                    	$query->leftJoin('lkp_countries as lcst', function($join)
                    	{
                    		$join->on('orders.to_city_id', '=', 'lcst.id');
                    		$join->on(DB::raw('tbq.lkp_courier_delivery_type_id'),'=',DB::raw(2));
                    		 
                    	});
                    	
                    	
                    	break;
                    case AIR_INTERNATIONAL :

                    $query->leftJoin('lkp_airports as lp', 'lp.id', '=', 'orders.from_city_id');
                    $query->leftJoin('lkp_airports as lcityp', 'lcityp.id', '=', 'orders.to_city_id');
                    $query->leftJoin('airint_buyer_quotes as bq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'bq.id');
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
                    $query->leftJoin('airint_buyer_quote_items as bqi', function($join)
                         {
                             $join->on('bq.id', '=', 'bqi.buyer_quote_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('lkp_air_ocean_shipment_types as st', function($join)
                         {
                             $join->on('bq.lkp_air_ocean_shipment_type_id', '=', 'st.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });     
                    $query->leftJoin('lkp_air_ocean_sender_identities as si', function($join)
                         {
                             $join->on('bq.lkp_air_ocean_sender_identity_id', '=', 'si.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });  
                    $query->leftJoin('lkp_air_ocean_shipment_types as st1', function($join)
                         {
                             $join->on('tbqi.lkp_air_ocean_shipment_type_id', '=', 'st1.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         });     
                    $query->leftJoin('lkp_air_ocean_sender_identities as si1', function($join)
                         {
                             $join->on('tbqi.lkp_air_ocean_sender_identity_id', '=', 'si1.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         }); 
                    break;
                    case OCEAN :
                    $query->leftJoin('lkp_seaports as lp', 'lp.id', '=', 'orders.from_city_id');
                    $query->leftJoin('lkp_seaports as lcityp', 'lcityp.id', '=', 'orders.to_city_id');
                    $query->leftJoin('ocean_buyer_quotes as bq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'bq.id');
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
                    $query->leftJoin('ocean_buyer_quote_items as bqi', function($join)
                         {
                             $join->on('bq.id', '=', 'bqi.buyer_quote_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('lkp_air_ocean_shipment_types as st', function($join)
                         {
                             $join->on('bq.lkp_air_ocean_shipment_type_id', '=', 'st.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });     
                    $query->leftJoin('lkp_air_ocean_sender_identities as si', function($join)
                         {
                             $join->on('bq.lkp_air_ocean_sender_identity_id', '=', 'si.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });  
                    $query->leftJoin('lkp_air_ocean_shipment_types as st1', function($join)
                         {
                             $join->on('tbqi.lkp_air_ocean_shipment_type_id', '=', 'st1.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         });     
                    $query->leftJoin('lkp_air_ocean_sender_identities as si1', function($join)
                         {
                             $join->on('tbqi.lkp_air_ocean_sender_identity_id', '=', 'si1.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         }); 
                    break;
                
                case ROAD_TRUCK_HAUL :
                    $query->leftJoin('lkp_cities as lc', 'lc.id', '=', 'orders.from_city_id');
                    $query->leftJoin('lkp_cities as lcity', 'lcity.id', '=', 'orders.to_city_id');                                        
                    $query->leftJoin('truckhaul_buyer_quote_items as bqi', function($join)
                         {
                             $join->on('orders.buyer_quote_item_id', '=', 'bqi.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });                    
                    $query->leftJoin('truckhaul_buyer_quotes as bq', function($join)
                         {
                             $join->on('bqi.buyer_quote_id', '=', 'bq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });                  
                   
                    break;
                case ROAD_TRUCK_LEASE :
                    $query->leftJoin('lkp_cities as lc', 'lc.id', '=', 'orders.from_city_id');                    
                    $query->leftJoin('trucklease_buyer_quote_items as bqi', function($join)
                         {
                             $join->on('orders.buyer_quote_item_id', '=', 'bqi.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });                    
                    $query->leftJoin('trucklease_buyer_quotes as bq', function($join)
                         {
                             $join->on('bqi.buyer_quote_id', '=', 'bq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });                  
                   
                    break;
                    case ROAD_INTRACITY :
                    $query->leftJoin('lkp_ict_locations as lc', 'lc.id', '=', 'orders.from_city_id');
                    $query->leftJoin('lkp_ict_locations as lcity', 'lcity.id', '=', 'orders.to_city_id');
                    break;                   
                }
            $query->leftJoin('lkp_order_statuses as os', 'orders.lkp_order_status_id', '=', 'os.id')
            ->leftJoin('pickup_vehicle_details as pvd', 'orders.id', '=', 'pvd.order_id')->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');

            if($serviceId !=  AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER){
            $query->leftJoin('lkp_vehicle_types as lvt', 'orders.lkp_vehicle_type_id', '=', 'lvt.id');
         	}

            $query->leftJoin('order_receipts as or', 'orders.id', '=', 'or.order_id');
              $query->leftjoin('users as u', 'u.id', '=', 'orders.seller_id')->where('orders.id', '=', $orderId);
                    switch ($serviceId) {
                    case ROAD_FTL :
                        $orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id")  , 'lkp_load_types.load_type',  'or.*', 'oi.total_amt as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'pvd.*', 'lvt.vehicle_type as vehicle_type', 'lkp_payment_modes.payment_mode')->first();
                        
                        
                    $orders['priceDetails'] =DB::table('orders')->leftJoin('buyer_quote_sellers_quotes_prices as qp', function($join){
                             $join->on('orders.buyer_id', '=', 'qp.buyer_id');
                             $join->on('orders.buyer_quote_item_id', '=', 'qp.buyer_quote_item_id');
                             $join->on('orders.seller_post_item_id', '=', 'qp.seller_post_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                         })
                        ->leftJoin('term_buyer_quote_sellers_quotes_prices as tqp', function($join){
                             $join->on('orders.buyer_id', '=', 'tqp.buyer_id');
                             $join->on('orders.buyer_quote_item_id', '=', 'tqp.term_buyer_quote_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                         })
                         ->where('orders.buyer_id', '=', $user_id)->where('orders.id', '=', $orderId)
                         ->select(DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_price when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_price  end) as initial_quote_price"),DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_price end) as counter_quote_price") , DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_price end) as final_quote_price"), DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_created_at when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_created_at end) as initial_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_created_at  end) as counter_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_created_at end) as final_quote_created_at"))
                    ->first();
                        break;
                    //echo "<pre>";print_r($orders['priceDetails']);exit;
                    case ROAD_PTL :
                    
                        $orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id") , 'lkp_load_types.load_type', 'or.*', 'oi.total_amt as inv_total', 'op.*', 'os.order_status',  'lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city', 'pvd.*', 'lvt.*', 'lkp_payment_modes.payment_mode')
                    ->first();
                        $orders['priceDetails'] = DB::table('orders')->leftJoin('ptl_buyer_quote_sellers_quotes_prices as qp', function($join){
                             $join->on('orders.buyer_id', '=', 'qp.buyer_id');
                             $join->on('orders.buyer_quote_id', '=', 'qp.buyer_quote_id');
                             $join->on('orders.seller_post_item_id', '=', 'qp.seller_post_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                         })
                        ->leftJoin('term_buyer_quote_sellers_quotes_prices as tqp', function($join){
                             $join->on('orders.buyer_id', '=', 'tqp.buyer_id');
                             $join->on('orders.buyer_quote_item_id', '=', 'tqp.term_buyer_quote_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                         })
                    ->where('orders.buyer_id', '=', $user_id)
                    ->where('orders.id', '=', $orderId)
                    ->select(DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_price when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_price  end) as initial_quote_price"),DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_price end) as counter_quote_price") , DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_price end) as final_quote_price"), DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_created_at when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_created_at end) as initial_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_created_at  end) as counter_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_created_at end) as final_quote_created_at"))
                    ->first();
                        break;
                    case RAIL :
                    
                        $orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id") , 'lkp_load_types.load_type', 'or.*', 'oi.total_amt as inv_total', 'op.*', 'os.order_status',  'lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city', 'pvd.*', 'lvt.*', 'lkp_payment_modes.payment_mode')
                    ->first();
                        $orders['priceDetails'] = DB::table('orders')->leftJoin('rail_buyer_quote_sellers_quotes_prices as qp', function($join){
                             $join->on('orders.buyer_id', '=', 'qp.buyer_id');
                             $join->on('orders.buyer_quote_id', '=', 'qp.buyer_quote_id');
                             $join->on('orders.seller_post_item_id', '=', 'qp.seller_post_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                         })
                        ->leftJoin('term_buyer_quote_sellers_quotes_prices as tqp', function($join){
                             $join->on('orders.buyer_id', '=', 'tqp.buyer_id');
                             $join->on('orders.buyer_quote_item_id', '=', 'tqp.term_buyer_quote_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                         })
                    ->where('orders.buyer_id', '=', $user_id)
                    ->where('orders.id', '=', $orderId)
                    ->select(DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_price when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_price  end) as initial_quote_price"),DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_price end) as counter_quote_price") , DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_price end) as final_quote_price"), DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_created_at when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_created_at end) as initial_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_created_at  end) as counter_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_created_at end) as final_quote_created_at"))
                    ->first();
                        break;
                    
                    case AIR_DOMESTIC:
                        $orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id") , 'lkp_load_types.load_type', 'or.*',  'oi.total_amt as inv_total', 'op.*', 'os.order_status',  'lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city', 'pvd.*', 'lvt.*', 'lkp_payment_modes.payment_mode')
                    ->first();
                        $orders['priceDetails'] = DB::table('orders')->leftJoin('airdom_buyer_quote_sellers_quotes_prices as qp', function($join){
                             $join->on('orders.buyer_id', '=', 'qp.buyer_id');
                             $join->on('orders.buyer_quote_id', '=', 'qp.buyer_quote_id');
                             $join->on('orders.seller_post_item_id', '=', 'qp.seller_post_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                         })
                        ->leftJoin('term_buyer_quote_sellers_quotes_prices as tqp', function($join){
                             $join->on('orders.buyer_id', '=', 'tqp.buyer_id');
                             $join->on('orders.buyer_quote_item_id', '=', 'tqp.term_buyer_quote_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                         })
                    ->where('orders.buyer_id', '=', $user_id)
                    ->where('orders.id', '=', $orderId)
                    ->select(DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_price when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_price  end) as initial_quote_price"),DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_price end) as counter_quote_price") , DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_price end) as final_quote_price"), DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_created_at when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_created_at end) as initial_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_created_at  end) as counter_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_created_at end) as final_quote_created_at"))
                    ->first();
                        break;
                    
                    case COURIER:
                        	
                       	$orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id") , 'lkp_load_types.load_type', 'or.*','oi.total_amt as inv_total', 'op.*', 'os.order_status', 
                       	 'lp.postoffice_name as from_city', 
                       	 DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then (case when `bqi`.`lkp_courier_delivery_type_id` = 1 then lcityp.postoffice_name  when `bqi`.`lkp_courier_delivery_type_id` = 2 then lcs.country_name end) when `orders`.`lkp_order_type_id` = 2 then (case when `tbq`.`lkp_courier_delivery_type_id` = 1 then lcitypt.postoffice_name  when `tbq`.`lkp_courier_delivery_type_id` = 2 then lcst.country_name end)  end ) as to_city"),
                       	 'pvd.*', 'lkp_payment_modes.payment_mode')
                       	->first();
                        	$orders['priceDetails'] = DB::table('orders')->leftJoin('courier_buyer_quote_sellers_quotes_prices as qp', function($join){
                        		$join->on('orders.buyer_id', '=', 'qp.buyer_id');
                        		$join->on('orders.buyer_quote_id', '=', 'qp.buyer_quote_id');
                        		$join->on('orders.seller_post_item_id', '=', 'qp.seller_post_item_id');
                        		$join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                        	})
                        	->leftJoin('term_buyer_quote_sellers_quotes_prices as tqp', function($join){
                        		$join->on('orders.buyer_id', '=', 'tqp.buyer_id');
                        		$join->on('orders.buyer_quote_item_id', '=', 'tqp.term_buyer_quote_item_id');
                        		$join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                        	})
                        	->where('orders.buyer_id', '=', $user_id)
                        	->where('orders.id', '=', $orderId)
                        	->select(DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_price when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_price  end) as initial_quote_price"),DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_price end) as counter_quote_price") , DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_price end) as final_quote_price"), DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_created_at when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_created_at end) as initial_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_created_at  end) as counter_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_created_at end) as final_quote_created_at"))
                        	->first();
                        	break;
                            
                    case AIR_INTERNATIONAL:
                        $orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','bq.product_made','bq.ie_code',                        	
                        	'orders.*','orders.dispatch_date as orderdispatchdate',
                        	'orders.delivery_date as orderdeliverydate','orders.id as orderid',
                        	 'orders.price as orderprice', 'u.username', 
                        	 DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id") ,
                        	  'lkp_load_types.load_type', 'or.*', 'oi.total_amt as inv_total', 'op.*', 'os.order_status',  'lp.airport_name as from_city', 
                        	  'lcityp.airport_name as to_city', 'pvd.*', 'lkp_payment_modes.payment_mode',DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then si.sender_identity when `orders`.`lkp_order_type_id` = 2 then si1.sender_identity end) as sender_identity"), 
                    	DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then st.shipment_type when `orders`.`lkp_order_type_id` = 2 then st1.shipment_type end) as shipment_type"))
                    ->first();
                        $orders['priceDetails'] = DB::table('orders')->leftJoin('airint_buyer_quote_sellers_quotes_prices as qp', function($join){
                             $join->on('orders.buyer_id', '=', 'qp.buyer_id');
                             $join->on('orders.buyer_quote_id', '=', 'qp.buyer_quote_id');
                             $join->on('orders.seller_post_item_id', '=', 'qp.seller_post_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                         })
                        ->leftJoin('term_buyer_quote_sellers_quotes_prices as tqp', function($join){
                             $join->on('orders.buyer_id', '=', 'tqp.buyer_id');
                             $join->on('orders.buyer_quote_item_id', '=', 'tqp.term_buyer_quote_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                         })
                    ->where('orders.buyer_id', '=', $user_id)
                    ->where('orders.id', '=', $orderId)
                    ->select(DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_price when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_price  end) as initial_quote_price"),DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_price end) as counter_quote_price") , DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_price end) as final_quote_price"), DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_created_at when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_created_at end) as initial_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_created_at  end) as counter_quote_created_at"),  
                    	DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_created_at end) as final_quote_created_at"))
                    	->first();
                        break;
                    
                    case OCEAN:
                        $orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','bq.product_made','bq.ie_code','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id") , 'lkp_load_types.load_type', 'or.*', 'oi.total_amt as inv_total', 'op.*', 'os.order_status',  'lp.seaport_name as from_city', 'lcityp.seaport_name as to_city', 'pvd.*', 'lkp_payment_modes.payment_mode'
                                ,DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then si.sender_identity when `orders`.`lkp_order_type_id` = 2 then si1.sender_identity end) as sender_identity"), 
                    	DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then st.shipment_type when `orders`.`lkp_order_type_id` = 2 then st1.shipment_type end) as shipment_type"))
                        ->first();                  

                        $orders['priceDetails'] = DB::table('orders')->leftJoin('ocean_buyer_quote_sellers_quotes_prices as qp', function($join){
                             $join->on('orders.buyer_id', '=', 'qp.buyer_id');
                             $join->on('orders.buyer_quote_id', '=', 'qp.buyer_quote_id');
                             $join->on('orders.seller_post_item_id', '=', 'qp.seller_post_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                         })
                        ->leftJoin('term_buyer_quote_sellers_quotes_prices as tqp', function($join){
                             $join->on('orders.buyer_id', '=', 'tqp.buyer_id');
                             $join->on('orders.buyer_quote_item_id', '=', 'tqp.term_buyer_quote_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                         })                         
                    ->where('orders.buyer_id', '=', $user_id)
                    ->where('orders.id', '=', $orderId)
                    ->select(DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_price when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_price  end) as initial_quote_price"),
                    	DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_price end) as counter_quote_price") , 
                    	DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_price end) as final_quote_price"), 
                    	DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_created_at when `orders`.`lkp_order_type_id` = 2 then tqp.initial_quote_created_at end) as initial_quote_created_at"), 
                    	DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_created_at  end) as counter_quote_created_at"), 
                    	  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_created_at end) as final_quote_created_at"))
                    ->first();
                        break; 
                    
                    case ROAD_TRUCK_HAUL :
                        $orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id end) as trans_id")  , 'lkp_load_types.load_type',  'or.*', 'oi.total_amt as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'pvd.*', 'lvt.vehicle_type as vehicle_type', 'lkp_payment_modes.payment_mode')->first();                                                
                        $orders['priceDetails'] =DB::table('orders')->leftJoin('truckhaul_buyer_quote_sellers_quotes_prices as qp', function($join){
                             $join->on('orders.buyer_id', '=', 'qp.buyer_id');
                             $join->on('orders.buyer_quote_item_id', '=', 'qp.buyer_quote_item_id');
                             $join->on('orders.seller_post_item_id', '=', 'qp.seller_post_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                         })                        
                         ->where('orders.buyer_id', '=', $user_id)->where('orders.id', '=', $orderId)
                         ->select(DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_price end) as initial_quote_price"),DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_price end) as counter_quote_price") , DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_price end) as final_quote_price"), DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_created_at end) as initial_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_created_at  end) as counter_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_created_at end) as final_quote_created_at"))
                    ->first();
                        break;
                    
                    case ROAD_TRUCK_LEASE :
                        $orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice',
                                                                 'u.username', DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id end) as trans_id")  , 'lkp_load_types.load_type',  'or.*', 'oi.total_amt as inv_total', 'op.*', 'os.order_status',
                                                                 'lc.city_name as from_city', 'pvd.*', 'lvt.vehicle_type as vehicle_type',
                                                                 'lkp_payment_modes.payment_mode')->first();                                                
                        $orders['priceDetails'] =DB::table('orders')->leftJoin('trucklease_buyer_quote_sellers_quotes_prices as qp', function($join){
                             $join->on('orders.buyer_id', '=', 'qp.buyer_id');
                             $join->on('orders.buyer_quote_item_id', '=', 'qp.buyer_quote_item_id');
                             $join->on('orders.seller_post_item_id', '=', 'qp.seller_post_item_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                         })                        
                         ->where('orders.buyer_id', '=', $user_id)->where('orders.id', '=', $orderId)
                         ->select(DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_price end) as initial_quote_price"),DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_price end) as counter_quote_price") , DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_price end) as final_quote_price"), DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.initial_quote_created_at end) as initial_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.counter_quote_created_at  end) as counter_quote_created_at"),  DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then qp.final_quote_created_at end) as final_quote_created_at"))
                    ->first();
                        break;
                    
                }
            
            return $orders;
        }
	
	
}
