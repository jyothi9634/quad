<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
//use App\Http\Controllers\OrdersController;
use Auth;
use App\Components\CommonComponent;
use App\Components\SellerOrderComponent;
use App\Components\Ptl\PtlBuyerGetQuoteBooknowComponent;
use App\Components\Rail\RailBuyerGetQuoteBooknowComponent;
use App\Components\AirDomestic\AirDomesticBuyerGetQuoteBooknowComponent;
use App\Components\ResizeImage;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderTrackingDetail;
use App\Models\PickupVehicleDetail;
use App\Models\OrderInvoice;
use App\Models\SellerOrderInvoice;
use App\Models\LkpServiceCharges;
use App\Models\OrderReceipt;
use App\Components\MessagesComponent;
use DB;
use Input;
use Config;
use File;
use Session;
use Response;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use Log;
use App\Components\SellerComponent;
use App\Components\Ftl\FtlBuyerOrderComponent;
use App\Components\Intracity\IntracityBuyerOrderComponent;
use App\Components\Relocation\RelocationBuyerComponent;
use App\Components\RelocationOffice\RelocationOfficeBuyerComponent;
use App\Components\RelocationPet\RelocationPetBuyerComponent;
use App\Components\TruckHaul\TruckHaulBuyerComponent;
use App\Components\RelocationInt\RelocationIntBuyerComponent;
use App\Components\BuyerOrdersComponent;
use App\Components\Term\TermBuyerComponent;
use App\Components\Term\TermSellerComponent;

class OrdersController extends Controller {

    /**
     * Create a new Orders controller instance.
     *
     * @return void
     */
    public $user_pk;

    public function __construct() {
        $this->middleware('auth', [
            'except' => 'getLogout'
        ]);
        if (isset(Auth::User()->id)) {

            $this->user_pk = Auth::User()->id;
        }
        
    }

    /**
     * Display a listing of the Orders.
     *
     * @return \Illuminate\Http\Response
     */

    
    
public function sellerOrders(){
	

	 Log::info('Seller has viewed Order List page:' . $this->user_pk, array(
            'c' => '1'
        ));
	try{
		$roleId = Auth::User()->lkp_role_id;
			
		//Retrieval of order statuses
		$order_status = CommonComponent::getOrderStatuses();
	
		//Retrieval of seller services
		$lkp_services_seller = CommonComponent::getServices();
	
		//Retrieval of order types
		$order_types = CommonComponent::getOrderTypes();
			
		//Search Form logic
		$statusId = '';
		$orderType = SPOT;
		if ( !empty($_REQUEST) ){
			//echo $_REQUEST['page'];
			if(isset($_REQUEST['status_id']) ){
				$statusId = $_REQUEST['status_id'];
				Session::put('status_search', $_REQUEST['status_id']);
			}
			if(isset($_REQUEST['service_id']) && $_REQUEST['service_id'] != ''){
				$serviceId = $_REQUEST['service_id'];
				//Session::put('service_id', $_POST['service_id']);
			}
                    if (isset($_REQUEST ['lkp_order_type_id']) && $_REQUEST ['lkp_order_type_id'] != '') {
                        $orderType = $_REQUEST ['lkp_order_type_id'];
                        Session::put('orderType_id', $_REQUEST['lkp_order_type_id']);
                    }
                    if(Session::get ( 'service_id' ) == COURIER){
                        if(isset($_POST['delivery_type']) && $_POST['delivery_type'] != ''){
                                $delivery_type = Session::get('delivery_type');
                                Session::put('delivery_type', $_REQUEST['delivery_type']);
                        }
                    }
		}else {
                    $_REQUEST = array();
                    if(Session::get ( 'service_id' ) == COURIER){
                    Session::put('delivery_type', 1);
                    $_REQUEST['delivery_type'] = Session::get('delivery_type');
                    $delivery_type = Session::get('delivery_type');
                    }
                }
		
		if(!empty($_GET)){
			if(isset($_GET['page'])){
				$statusId = Session::get('status_search');
				$serviceId = Session::get('service_id');
				$orderType = Session::get('orderType_id');
                                
				if(Session::get ( 'service_id' ) == COURIER){
					$delivery_type = Session::get('delivery_type');
				}
			}else{
				if(Session::get ( 'service_id' ) == COURIER){
					$delivery_type = Session::get('delivery_type');
				}
			}
		}
	
		if(Session::get ( 'service_id' ) != ''){
			$serviceId = Session::get ( 'service_id' );
		}		
	
		/**
		 * Saving the user activity to the log table 
		 * &
		 * Loading respective service data grid
		 *  Switch cases for services
		 */		

        if($orderType==CONTRACTS) {
        //rendering the view with the data grid
        $result= TermSellerComponent::getSellerContracts($orderType,$statusId, $serviceId, $roleId); 
        $grid = $result ['grid'];
        $filter = $result ['filter'];            
        return view('orders.seller_orders', [
                'grid' => $grid,
                'filter' => $filter],
                array(
                        'services' => $lkp_services_seller,
                        'status' => $order_status,
                        'order_types' => $order_types,
                        'order_type' => $orderType,
                        'service_id' => $serviceId,
                        'order_status' => $statusId
                ));

        } else {
            switch($serviceId){
            case ROAD_FTL   :
                $result = SellerOrderComponent::getFtlSellerOrders();
                CommonComponent::activityLog("SELLER_VIEWED_ORDERS",SELLER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                break;

            case ROAD_PTL   :
            case RAIL       :
            case AIR_DOMESTIC:
            case AIR_INTERNATIONAL:
            case OCEAN:
                $result = SellerOrderComponent::getLtlSellerOrders();
                CommonComponent::activityLog("SELLER_VIEWED_ORDERS",SELLER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                break;

            case COURIER:
                $result = SellerOrderComponent::getCourierSellerOrders();
                CommonComponent::activityLog("SELLER_VIEWED_ORDERS",SELLER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);                
                $grid = $result ['grid'];
                $filter = $result ['filter'];                    
                return view('orders.seller_orders', [
                        'grid' => $grid,
                        'filter' => $filter],
                        array(
                            'services' => $lkp_services_seller,
                            'status' => $order_status,
                            'order_types' => $order_types,
                            'order_type' => $orderType,
                            'domestic_or_international_selected'=>Session::get('delivery_type'),
                            'service_id' => $serviceId,
                            'order_status' => $statusId
                        ));
                break;

            case ROAD_TRUCK_HAUL:
            case ROAD_TRUCK_LEASE:
                $result = SellerOrderComponent::getTruckHaulLeaseSellerOrders();
                CommonComponent::activityLog("SELLER_VIEWED_ORDERS",SELLER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                break;

            case RELOCATION_DOMESTIC :   
            case RELOCATION_PET_MOVE :  
                $result = SellerOrderComponent::getRelocDomPetSellerOrders();
                CommonComponent::activityLog("SELLER_VIEWED_ORDERS",SELLER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                break;

            case RELOCATION_INTERNATIONAL : 
                $result = SellerOrderComponent::getRelocIntSellerOrders();
                CommonComponent::activityLog("SELLER_VIEWED_ORDERS",SELLER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                break;

            case RELOCATION_OFFICE_MOVE :  
            case RELOCATION_GLOBAL_MOBILITY :
                $result = SellerOrderComponent::getRelocelocGlobOfficeSellerOrders();
                CommonComponent::activityLog("SELLER_VIEWED_ORDERS",SELLER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                break;

            default  : 
                CommonComponent::activityLog("SELLER_VIEWED_ORDERS",SELLER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                $result = SellerOrderComponent::getSellerOrders($orderType,$statusId, $serviceId, $roleId);
                break;      
        }
        
        //rendering the view with the data grid
        $grid = $result ['grid'];
        $filter = $result ['filter'];            
        return view('orders.seller_orders', [
                'grid' => $grid,
                'filter' => $filter],
                array(
                    'services' => $lkp_services_seller,
                    'status' => $order_status,
                    'order_types' => $order_types,
                    'order_type' => $orderType,
                    'service_id' => $serviceId,
                    'order_status' => $statusId
                ));
        }		
			
	} catch (Exception $e) {
	
	}
	
}   

    public function showDetails($id) {
        Log::info('Seller has viewed Order Details page:' . $this->user_pk, array('c' => '1'));
        if (isset($id) && ($id > 0)) {
            $orderId = $id;
            $qry        =   DB::table('pickup_vehicle_details as pvd');
            $vehicles   =   $qry->leftjoin('vehicle_details as veh','veh.vehicle_number','=','pvd.vehicle_no')
                                ->where('pvd.order_id', '=', $orderId)->select('pvd.*','veh.volty_register')
                                ->groupby('veh.vehicle_number')
                                ->get();
            $query = DB::table('orders');
            //$orderDetails->leftJoin('order_payments as op', 'orders.order_payment_id', '=', 'op.id')
            $query->leftJoin('order_payments as op', 'orders.order_payment_id', '=', 'op.id')
                    ->leftjoin('lkp_payment_modes', 'lkp_payment_modes.id', '=', 'op.lkp_payment_mode_id')
                    ->leftJoin('seller_order_invoices as oi', 'orders.id', '=', 'oi.order_id')
                    ->leftJoin('order_invoices as invoice', 'orders.id', '=', 'invoice.order_id');
                    /*->leftJoin('lkp_cities as lc', 'orders.from_city_id', '=', 'lc.id')
                    ->leftJoin('lkp_cities as lcity', 'orders.to_city_id', '=', 'lcity.id')*/
                    $serviceId = Session::get('service_id');
                switch ($serviceId) {
                    case ROAD_FTL :
                    $query->leftJoin('lkp_cities as lc', 'orders.from_city_id', '=', 'lc.id');
                    $query->leftJoin('lkp_cities as lcity', 'orders.to_city_id', '=', 'lcity.id');
                    //$query->leftjoin('buyer_quote_items', 'buyer_quote_items.id', '=', 'orders.buyer_quote_item_id');
                    //$query->leftjoin('buyer_quotes', 'buyer_quotes.id', '=', 'buyer_quote_items.buyer_quote_id');
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
                    //$query->leftjoin('ptl_buyer_quotes', 'ptl_buyer_quotes.id', '=', 'orders.buyer_quote_id');
                    //$query->leftjoin('ptl_buyer_quote_items', 'ptl_buyer_quote_items.id', '=', 'orders.buyer_quote_item_id');
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
                    //$query->leftjoin('rail_buyer_quotes', 'rail_buyer_quotes.id', '=', 'orders.buyer_quote_id');
                    //$query->leftjoin('rail_buyer_quote_items', 'rail_buyer_quote_items.id', '=', 'orders.buyer_quote_item_id');
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
                    //$query->leftjoin('airdom_buyer_quotes', 'airdom_buyer_quotes.id', '=', 'orders.buyer_quote_id');
                    //$query->leftjoin('airdom_buyer_quote_items', 'airdom_buyer_quote_items.id', '=', 'orders.buyer_quote_item_id');
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
                    //$query->leftjoin('airint_buyer_quotes', 'airint_buyer_quotes.id', '=', 'orders.buyer_quote_id');
                    //$query->leftjoin('airint_buyer_quote_items', 'airint_buyer_quote_items.id', '=', 'orders.buyer_quote_item_id');
                    $query->leftJoin('airint_buyer_quotes as bq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'bq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('term_buyer_quotes as tbq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'tbq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         });
                    $query->leftJoin('airint_buyer_quote_items as bqi', function($join)
                         {
                             $join->on('bq.id', '=', 'bqi.buyer_quote_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('term_buyer_quote_items as tbqi', function($join)
                         {
                             $join->on('tbq.id', '=', 'tbqi.term_buyer_quote_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
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
                    //$query->leftjoin('lkp_air_ocean_shipment_types as st', 'st.id', '=', 'airint_buyer_quotes.lkp_air_ocean_shipment_type_id');
                    //$query->leftjoin('lkp_air_ocean_sender_identities as si', 'si.id', '=', 'airint_buyer_quotes.lkp_air_ocean_sender_identity_id');
                    break;
                    case OCEAN :
                    $query->leftJoin('lkp_seaports as lp', 'lp.id', '=', 'orders.from_city_id');
                    $query->leftJoin('lkp_seaports as lcityp', 'lcityp.id', '=', 'orders.to_city_id');
                    //$query->leftjoin('ocean_buyer_quotes', 'ocean_buyer_quotes.id', '=', 'orders.buyer_quote_id');
                    //$query->leftjoin('ocean_buyer_quote_items', 'ocean_buyer_quote_items.id', '=', 'orders.buyer_quote_item_id');
                    //$query->leftjoin('lkp_air_ocean_shipment_types as st', 'st.id', '=', 'ocean_buyer_quotes.lkp_air_ocean_shipment_type_id');
                    //$query->leftjoin('lkp_air_ocean_sender_identities as si', 'si.id', '=', 'ocean_buyer_quotes.lkp_air_ocean_sender_identity_id');
                    $query->leftJoin('ocean_buyer_quotes as bq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'bq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('term_buyer_quotes as tbq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'tbq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
                         });
                    $query->leftJoin('ocean_buyer_quote_items as bqi', function($join)
                         {
                             $join->on('bq.id', '=', 'bqi.buyer_quote_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                    $query->leftJoin('term_buyer_quote_items as tbqi', function($join)
                         {
                             $join->on('tbq.id', '=', 'tbqi.term_buyer_quote_id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(2));
                             
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
                    case ROAD_INTRACITY :
                    $query->leftJoin('lkp_cities as lc', 'orders.from_city_id', '=', 'lc.id');
                    $query->leftJoin('lkp_cities as lcity', 'orders.to_city_id', '=', 'lcity.id');
                    $query->leftjoin('buyer_quote_items', 'buyer_quote_items.id', '=', 'orders.buyer_quote_item_id');
                    $query->leftjoin('buyer_quotes', 'buyer_quotes.id', '=', 'buyer_quote_items.buyer_quote_id');
                    break;
                    case RELOCATION_DOMESTIC :
                    $query->leftJoin('lkp_cities as lc', 'orders.from_city_id', '=', 'lc.id');
                    $query->leftJoin('lkp_cities as lcity', 'orders.to_city_id', '=', 'lcity.id');
                    //$query->leftjoin('buyer_quote_items', 'buyer_quote_items.id', '=', 'orders.buyer_quote_item_id');
                    //$query->leftjoin('buyer_quotes', 'buyer_quotes.id', '=', 'buyer_quote_items.buyer_quote_id');
                    $query->leftJoin('relocation_buyer_posts as bq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'bq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                   
                    break;
                    case RELOCATION_OFFICE_MOVE :
                        $query->leftJoin('lkp_cities as lc', 'orders.from_city_id', '=', 'lc.id');
                        $query->leftJoin('relocationoffice_buyer_posts as bq', 'bq.id', '=', 'orders.buyer_quote_id');
                    break;
                    case RELOCATION_PET_MOVE :
                    $query->leftJoin('lkp_cities as lc', 'orders.from_city_id', '=', 'lc.id');
                    $query->leftJoin('lkp_cities as lcity', 'orders.to_city_id', '=', 'lcity.id');
                    $query->leftJoin('relocationpet_buyer_posts as bq', 'bq.id', '=', 'orders.buyer_quote_id');
                    
                    break;
                
                    case ROAD_TRUCK_HAUL :
                    $query->leftJoin('lkp_cities as lc', 'orders.from_city_id', '=', 'lc.id');
                    $query->leftJoin('lkp_cities as lcity', 'orders.to_city_id', '=', 'lcity.id');                   
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
                    $query->leftJoin('lkp_cities as lc', 'orders.from_city_id', '=', 'lc.id');                                     
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
                    case RELOCATION_INTERNATIONAL :
                    $query->leftJoin('lkp_cities as lc', 'orders.from_city_id', '=', 'lc.id');
                    $query->leftJoin('lkp_cities as lcity', 'orders.to_city_id', '=', 'lcity.id');
                    //$query->leftjoin('buyer_quote_items', 'buyer_quote_items.id', '=', 'orders.buyer_quote_item_id');
                    //$query->leftjoin('buyer_quotes', 'buyer_quotes.id', '=', 'buyer_quote_items.buyer_quote_id');
                    $query->leftJoin('relocationint_buyer_posts as bq', function($join)
                         {
                             $join->on('orders.buyer_quote_id', '=', 'bq.id');
                             $join->on(DB::raw('orders.lkp_order_type_id'),'=',DB::raw(1));
                             
                         });
                   
                    break;
                    case RELOCATION_GLOBAL_MOBILITY :
                        $query->leftJoin('lkp_cities as lc', 'orders.to_city_id', '=', 'lc.id');
                        $query->leftJoin('relocationgm_buyer_posts as bq', 'bq.id', '=', 'orders.buyer_quote_id');
                    break;                    
                    default :
                    $query->leftJoin('lkp_cities as lc', 'orders.from_city_id', '=', 'lc.id');
                    $query->leftJoin('lkp_cities as lcity', 'orders.to_city_id', '=', 'lcity.id');
                    $query->leftjoin('buyer_quote_items', 'buyer_quote_items.id', '=', 'orders.buyer_quote_item_id');
                    $query->leftjoin('buyer_quotes', 'buyer_quotes.id', '=', 'buyer_quote_items.buyer_quote_id');
                    break;
                }
                    $query->leftJoin('lkp_order_statuses as os', 'orders.lkp_order_status_id', '=', 'os.id');
                    if($serviceId !=  AIR_INTERNATIONAL && $serviceId != OCEAN){
                    $query->leftJoin('lkp_vehicle_types as lvt', 'orders.lkp_vehicle_type_id', '=', 'lvt.id');
                    }
                    $query->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id')
                    ->leftJoin('order_receipts as or', 'orders.id', '=', 'or.order_id')
                    ->leftjoin('users as u', 'u.id', '=', 'orders.buyer_id')
                    ->where('orders.seller_id', '=', $this->user_pk)->where('orders.id', '=', $orderId);
                    
                    switch ($serviceId) {
                    case ROAD_FTL :
                    $orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','u.username', DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id"), 'lkp_load_types.load_type',  'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lvt.vehicle_type as vehicle_type', 'lkp_payment_modes.payment_mode')->first();
                    //echo "<pre>";print_r($orderDetails);exit;
                    //$orderDetails = $query->select('orders.*','orders.id as orderid', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','u.username', 'buyer_quotes.transaction_id as trans_id', 'lkp_load_types.load_type', 'buyer_quote_items.*', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                    //echo $orderDetails->seller_post_item_id;
                        $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                      if($orderDetails->lkp_order_type_id==1)   { 
                    $seller_post_payment    = DB::table('seller_post_items')
                    ->join('seller_posts','seller_posts.id','=','seller_post_items.seller_post_id')
                    ->where('seller_post_items.id',$orderDetails->seller_post_item_id)
                    ->select('seller_posts.lkp_payment_mode_id','seller_posts.tracking')
                    ->first();
                    
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();
	
                    
                    return view('orders.seller_order_details', array(
                    		'orderDetails' => $orderDetails,
                    		'payment_mode_seller' => $payment_buyer_details->payment_mode,
                                'vehicles' => $vehicles,
                                'allMessagesList' => $allMessagesList,
                            'tracking_order' => $seller_post_payment->tracking,
                    ));
                    }else{
                    return view('orders.seller_order_details', array(
                    		'orderDetails' => $orderDetails,
                    		'vehicles' => $vehicles,
                            'allMessagesList' => $allMessagesList,  
                            //'tracking_order' => $seller_post_payment->tracking,
                    ));    
                    }
                    break;
                    case ROAD_PTL :
                    //$orderDetails = $query->select('orders.*','orders.id as orderid', 'u.username', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','ptl_buyer_quotes.transaction_id as trans_id', 'lkp_load_types.load_type', 'ptl_buyer_quote_items.*', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status','lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city',  'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                    $orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'u.username', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery',DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id"), 'lkp_load_types.load_type',  'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status','lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city',  'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                    //echo "<pre>";print_r($orderDetails);exit;
                        $buyer_order_details = DB::table('orders')
                    ->where('order_no',$orderDetails->order_no)
                    ->select('buyer_quote_id')
                    ->first();
                        
                        
                        //for packaging type details
                        if(isset($orderDetails->buyer_quote_id)&& $orderDetails->buyer_quote_id!=0 && $orderDetails->buyer_quote_id!="") { 
                       $post_items = \DB::table('ptl_buyer_quotes as bq')
                                ->leftjoin('ptl_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                                ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                ->where('bq.id',$orderDetails->buyer_quote_id)
                                ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')
                                ->get();
                        }
                        elseif(isset($orderDetails->buyer_quote_item_id)&& $orderDetails->buyer_quote_item_id!=0 && $orderDetails->buyer_quote_item_id!="") { 
                            if(isset($orderDetails->lkp_order_type_id)&& $orderDetails->lkp_order_type_id==2){
                                $post_items = \DB::table('term_buyer_quotes as bq')
                                    ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                    //->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                    ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                    ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                    ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                                    ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                                    ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')
                                    ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                                    ->select('bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                            }
                        }//end packaging type details
                        //echo "<pre>";print_r($post_items);exit;
                    $orderDetails_buyer_pickups = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($buyer_order_details->buyer_quote_id);
                    
                        $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                        if($orderDetails->lkp_order_type_id==1)   {
                    $seller_post_payment    = DB::table('ptl_seller_post_items')
                   ->join('ptl_seller_posts','ptl_seller_posts.id','=','ptl_seller_post_items.seller_post_id')
                    ->where('ptl_seller_post_items.id',$orderDetails->seller_post_item_id)
                    ->select('ptl_seller_posts.lkp_payment_mode_id','ptl_seller_posts.tracking')
            		->first();
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();

                    return view('orders.seller_order_details_ptl', array(
                    		'orderDetails' => $orderDetails,
                    		'payment_mode_seller' => $payment_buyer_details->payment_mode,
                    		'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                    		'tracking_order' => $seller_post_payment->tracking,
                                'vehicles' => $vehicles,
                                'allMessagesList' => $allMessagesList,
                                'post_items'=>$post_items
                    ));
                        }else{
                            return view('orders.seller_order_details_ptl', array(
                    		'orderDetails' => $orderDetails,
                    		'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                    		//'tracking_order' => $seller_post_payment->tracking,
                                'vehicles' => $vehicles,
                                'allMessagesList' => $allMessagesList,
                                'post_items'=>$post_items
                    ));
                        }
                    break;
                    case RAIL :
                    $orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'u.username', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery',DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id"), 'lkp_load_types.load_type',  'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status','lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city',  'lvt.*', 'lkp_payment_modes.payment_mode')->first();    
                    //$orderDetails = $query->select('orders.*','orders.id as orderid', 'u.username', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','rail_buyer_quotes.transaction_id as trans_id', 'lkp_load_types.load_type', 'rail_buyer_quote_items.*', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status','lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city',  'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                    //echo "<pre>";print_r($orderDetails);exit;
                        $buyer_order_details = DB::table('orders')
                    ->where('order_no',$orderDetails->order_no)
                    ->select('buyer_quote_id')
                    ->first();
                        
                        //for packaging type details
                        if(isset($orderDetails->buyer_quote_id)&& $orderDetails->buyer_quote_id!=0 && $orderDetails->buyer_quote_id!="") { 
                       $post_items = \DB::table('rail_buyer_quotes as bq')
                                ->leftjoin('rail_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                                ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                ->where('bq.id',$orderDetails->buyer_quote_id)
                                ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')
                                ->get();
                        }
                        elseif(isset($orderDetails->buyer_quote_item_id)&& $orderDetails->buyer_quote_item_id!=0 && $orderDetails->buyer_quote_item_id!="") { 
                            if(isset($orderDetails->lkp_order_type_id)&& $orderDetails->lkp_order_type_id==2){
                                $post_items = \DB::table('term_buyer_quotes as bq')
                                    ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                    //->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                    ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                    ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                    ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                                    ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                                    ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')    
                                    ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                                    ->select('bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                            }
                        }//end packaging type details
                    $orderDetails_buyer_pickups = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($buyer_order_details->buyer_quote_id);
                    
                    $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                    if($orderDetails->lkp_order_type_id==1)   {
                    $seller_post_payment    = DB::table('rail_seller_post_items as spi')
                   ->join('rail_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->where('spi.id',$orderDetails->seller_post_item_id)
                    ->select('sp.lkp_payment_mode_id','sp.tracking')
            		->first();
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();
                    return view('orders.seller_order_details_ptl', array(
                    		'orderDetails' => $orderDetails,
                    		'payment_mode_seller' => $payment_buyer_details->payment_mode,
                    		'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                    		'tracking_order' => $seller_post_payment->tracking,
                                'vehicles' => $vehicles,
                                'allMessagesList' => $allMessagesList,
                                'post_items'=>$post_items
                    ));
                        }else{
                            return view('orders.seller_order_details_ptl', array(
                    		'orderDetails' => $orderDetails,
                    		'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                    		//'tracking_order' => $seller_post_payment->tracking,
                                'vehicles' => $vehicles,
                                'allMessagesList' => $allMessagesList,
                                'post_items'=>$post_items
                    ));
                        }
                    break;
                    case AIR_DOMESTIC :
                    $orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'u.username', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery',DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id"), 'lkp_load_types.load_type',  'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status','lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city',  'lvt.*', 'lkp_payment_modes.payment_mode')->first();        
                    //$orderDetails = $query->select('orders.*','orders.id as orderid', 'u.username', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','airdom_buyer_quotes.transaction_id as trans_id', 'lkp_load_types.load_type', 'airdom_buyer_quote_items.*', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status','lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city',  'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                    //echo "<pre>";print_r($orderDetails);exit;
                    $buyer_order_details = DB::table('orders')
                    ->where('order_no',$orderDetails->order_no)
                    ->select('buyer_quote_id')
                    ->first();
                    
                    //for packaging type details
                        if(isset($orderDetails->buyer_quote_id)&& $orderDetails->buyer_quote_id!=0 && $orderDetails->buyer_quote_id!="") { 
                       $post_items = \DB::table('airdom_buyer_quotes as bq')
                                ->leftjoin('airdom_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                                ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                ->where('bq.id',$orderDetails->buyer_quote_id)
                                ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')
                                ->get();
                        }
                        elseif(isset($orderDetails->buyer_quote_item_id)&& $orderDetails->buyer_quote_item_id!=0 && $orderDetails->buyer_quote_item_id!="") { 
                            if(isset($orderDetails->lkp_order_type_id)&& $orderDetails->lkp_order_type_id==2){
                                $post_items = \DB::table('term_buyer_quotes as bq')
                                    ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                    //->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                    ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                    ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                    ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                                    ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                                    ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')    
                                    ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                                    ->select('bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                            }
                        }//end packaging type details
                    $orderDetails_buyer_pickups = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($buyer_order_details->buyer_quote_id);
                    $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                    if($orderDetails->lkp_order_type_id==1)   {    
                    $seller_post_payment    = DB::table('airdom_seller_post_items as spi')
                   ->join('airdom_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->where('spi.id',$orderDetails->seller_post_item_id)
                    ->select('sp.lkp_payment_mode_id','sp.tracking')
            		->first();
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();

                    return view('orders.seller_order_details_ptl', array(
                    		'orderDetails' => $orderDetails,
                    		'payment_mode_seller' => $payment_buyer_details->payment_mode,
                    		'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                    		'tracking_order' => $seller_post_payment->tracking,
                                'vehicles' => $vehicles,
                                'allMessagesList' => $allMessagesList,
                                'post_items'=>$post_items
                    ));
                    }else{
                        return view('orders.seller_order_details_ptl', array(
                    		'orderDetails' => $orderDetails,
                    		'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                    		//'tracking_order' => $seller_post_payment->tracking,
                                'vehicles' => $vehicles,
                                'allMessagesList' => $allMessagesList,
                                'post_items'=>$post_items
                    ));
                    }
                    break;
                    
                    case COURIER :
                    	$orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'u.username', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery',DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id"), 'lkp_load_types.load_type',  'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status',
                    	'lp.postoffice_name as from_city', 
                    	DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then (case when `bqi`.`lkp_courier_delivery_type_id` = 1 then lcityp.postoffice_name  when `bqi`.`lkp_courier_delivery_type_id` = 2 then lcs.country_name end) when `orders`.`lkp_order_type_id` = 2 then (case when `tbq`.`lkp_courier_delivery_type_id` = 1 then lcitypt.postoffice_name  when `tbq`.`lkp_courier_delivery_type_id` = 2 then lcst.country_name end)  end ) as to_city"),  
                    	'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                    	//$orderDetails = $query->select('orders.*','orders.id as orderid', 'u.username', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','airdom_buyer_quotes.transaction_id as trans_id', 'lkp_load_types.load_type', 'airdom_buyer_quote_items.*', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status','lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city',  'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                    	//echo "<pre>";print_r($orderDetails);exit;
                    	$buyer_order_details = DB::table('orders')
                    	->where('order_no',$orderDetails->order_no)
                    	->select('buyer_quote_id')
                    	->first();
                        
                        //for packaging type details
                        if(isset($orderDetails->buyer_quote_id)&& $orderDetails->buyer_quote_id!=0 && $orderDetails->buyer_quote_id!="") { 
                       $post_items = \DB::table('courier_buyer_quotes as bq')
                            ->leftjoin('courier_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                             ->leftjoin('lkp_courier_types', 'lkp_courier_types.id', '=', 'bqi.lkp_courier_type_id')
                                ->leftjoin('lkp_courier_delivery_types as pt', 'pt.id', '=', 'bqi.lkp_courier_delivery_type_id')
                            ->where('bqi.id',$orderDetails->buyer_quote_id)
                            ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.courier_delivery_type as courier_delivery_type', 'lkp_courier_types.courier_type as courier_type')
                               ->get();
                        }
                        elseif(isset($orderDetails->buyer_quote_item_id)&& $orderDetails->buyer_quote_item_id!=0 && $orderDetails->buyer_quote_item_id!="") { 
                            if(isset($orderDetails->lkp_order_type_id)&& $orderDetails->lkp_order_type_id==2){
                                $post_items = \DB::table('term_buyer_quotes as bq')
                            ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                            //->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                             ->leftjoin('lkp_courier_types', 'lkp_courier_types.id', '=', 'bq.lkp_courier_type_id')
                                ->leftjoin('lkp_courier_delivery_types as pt', 'pt.id', '=', 'bq.lkp_courier_delivery_type_id')
                            ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                            ->select('bqi.volume as cft','bqi.id','bqi.units as unit',  'pt.courier_delivery_type as courier_delivery_type', 'lkp_courier_types.courier_type as courier_type')->get();
                            }
                        }//end packaging type details
                    	$orderDetails_buyer_pickups = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($buyer_order_details->buyer_quote_id);
                    	$allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                    	if($orderDetails->lkp_order_type_id==1)   {
                    		$seller_post_payment    = DB::table('courier_seller_post_items as spi')
                    		->join('courier_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    		->where('spi.id',$orderDetails->seller_post_item_id)
                    		->select('sp.lkp_payment_mode_id','sp.tracking')
                    		->first();
                    		//echo $seller_post_payment->seller_post_item_id;exit;
                    		//echo "<pre>";print_r($seller_post_payment);exit;
                    		$payment_buyer_details = DB::table('lkp_payment_modes')
                    		->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    		->select('id','payment_mode')
                    		->first();
                    
                    		return view('orders.seller_order_details_ptl', array(
                    				'orderDetails' => $orderDetails,
                    				'payment_mode_seller' => $payment_buyer_details->payment_mode,
                    				'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                    				'tracking_order' => $seller_post_payment->tracking,
                    				'vehicles' => $vehicles,
                    				'allMessagesList' => $allMessagesList,
                                                'post_items'=>$post_items
                    		));
                    	}else{
                    		return view('orders.seller_order_details_ptl', array(
                    				'orderDetails' => $orderDetails,
                    				'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                    				//'tracking_order' => $seller_post_payment->tracking,
                    				'vehicles' => $vehicles,
                    				'allMessagesList' => $allMessagesList,
                                                'post_items'=>$post_items
                    		));
                    	}
                    	break;
                    case AIR_INTERNATIONAL :
                    $orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'u.username', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery',DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id"), 'lkp_load_types.load_type',  'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status','lp.airport_name as from_city', 'lcityp.airport_name as to_city',  'lkp_payment_modes.payment_mode',
                            DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then si.sender_identity when `orders`.`lkp_order_type_id` = 2 then si1.sender_identity end) as sender_identity"), 
                    	DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then st.shipment_type when `orders`.`lkp_order_type_id` = 2 then st1.shipment_type end) as shipment_type"))->first();        
                    //$orderDetails = $query->select('airint_buyer_quotes.product_made','airint_buyer_quotes.ie_code','si.sender_identity','st.shipment_type','orders.*','orders.id as orderid', 'u.username', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','airint_buyer_quotes.transaction_id as trans_id', 'lkp_load_types.load_type', 'airint_buyer_quote_items.*', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status','lp.airport_name as from_city', 'lcityp.airport_name as to_city',  'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                    //echo "<pre>";print_r($orderDetails);exit;
                    $buyer_order_details = DB::table('orders')
                    ->where('order_no',$orderDetails->order_no)
                    ->select('buyer_quote_id')
                    ->first();
                    
                        //for packaging type details
                        if(isset($orderDetails->buyer_quote_id)&& $orderDetails->buyer_quote_id!=0 && $orderDetails->buyer_quote_id!="") { 
                       $post_items = \DB::table('airint_buyer_quotes as bq')
                                ->leftjoin('airint_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                                ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                ->where('bq.id',$orderDetails->buyer_quote_id)
                                ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')
                                ->get();
                        }
                        elseif(isset($orderDetails->buyer_quote_item_id)&& $orderDetails->buyer_quote_item_id!=0 && $orderDetails->buyer_quote_item_id!="") { 
                            if(isset($orderDetails->lkp_order_type_id)&& $orderDetails->lkp_order_type_id==2){
                                $post_items = \DB::table('term_buyer_quotes as bq')
                                    ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                    //->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                    ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                    ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                    ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                                    ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                                    ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')
                                    ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                                    ->select('bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                            }
                        }//end packaging type details
                    $orderDetails_buyer_pickups = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($buyer_order_details->buyer_quote_id);
                    $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                    if($orderDetails->lkp_order_type_id==1)   {     
                    $seller_post_payment    = DB::table('airint_seller_post_items as spi')
                   ->join('airint_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->where('spi.id',$orderDetails->seller_post_item_id)
                    ->select('sp.lkp_payment_mode_id','sp.tracking')
            		->first();
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();
                    return view('orders.seller_order_details_ptl', array(
                    		'orderDetails' => $orderDetails,
                    		'payment_mode_seller' => $payment_buyer_details->payment_mode,
                    		'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                    		'tracking_order' => $seller_post_payment->tracking,
                                'vehicles' => $vehicles,
                                'allMessagesList' => $allMessagesList,
                                'post_items'=>$post_items
                    ));
                    }else{
                        return view('orders.seller_order_details_ptl', array(
                    		'orderDetails' => $orderDetails,
                    		'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                    		//'tracking_order' => $seller_post_payment->tracking,
                                'vehicles' => $vehicles,
                                'allMessagesList' => $allMessagesList,
                                'post_items'=>$post_items
                    ));
                    }
                    break;
                    case OCEAN :
                    $orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'u.username', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery',DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id  when `orders`.`lkp_order_type_id` = 2 then tbq.transaction_id end) as trans_id"), 'lkp_load_types.load_type',  'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status','lp.seaport_name as from_city', 'lcityp.seaport_name as to_city', 'lkp_payment_modes.payment_mode',
                            DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then si.sender_identity when `orders`.`lkp_order_type_id` = 2 then si1.sender_identity end) as sender_identity"), 
                    	DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then st.shipment_type when `orders`.`lkp_order_type_id` = 2 then st1.shipment_type end) as shipment_type"))->first();    
                    //$orderDetails = $query->select('ocean_buyer_quotes.product_made','ocean_buyer_quotes.ie_code','si.sender_identity','st.shipment_type','orders.*','orders.id as orderid', 'u.username', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','ocean_buyer_quotes.transaction_id as trans_id', 'lkp_load_types.load_type', 'ocean_buyer_quote_items.*', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status','lp.seaport_name as from_city', 'lcityp.seaport_name as to_city',  'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                    //echo "<pre>";print_r($orderDetails);exit;
                    $buyer_order_details = DB::table('orders')
                    ->where('order_no',$orderDetails->order_no)
                    ->select('buyer_quote_id')
                    ->first();
                    
                        //for packaging type details
                        if(isset($orderDetails->buyer_quote_id)&& $orderDetails->buyer_quote_id!=0 && $orderDetails->buyer_quote_id!="") { 
                       $post_items = \DB::table('ocean_buyer_quotes as bq')
                                ->leftjoin('ocean_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                                ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                ->where('bq.id',$orderDetails->buyer_quote_id)
                                ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')
                                ->get();
                        }
                        elseif(isset($orderDetails->buyer_quote_item_id)&& $orderDetails->buyer_quote_item_id!=0 && $orderDetails->buyer_quote_item_id!="") { 
                            if(isset($orderDetails->lkp_order_type_id)&& $orderDetails->lkp_order_type_id==2){
                                $post_items = \DB::table('term_buyer_quotes as bq')
                                    ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                    //->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                    ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                    ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                    ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                                    ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                                    ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')   
                                    ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                                    ->select('bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                            }
                        }//end packaging type details
                    
                    $orderDetails_buyer_pickups = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($buyer_order_details->buyer_quote_id);
                    $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                    if($orderDetails->lkp_order_type_id==1)   {        
                    $seller_post_payment    = DB::table('ocean_seller_post_items as spi')
                   ->join('ocean_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->where('spi.id',$orderDetails->seller_post_item_id)
                    ->select('sp.lkp_payment_mode_id','sp.tracking')
            		->first();
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();
                    return view('orders.seller_order_details_ptl', array(
                    		'orderDetails' => $orderDetails,
                    		'payment_mode_seller' => $payment_buyer_details->payment_mode,
                    		'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                    		'tracking_order' => $seller_post_payment->tracking,
                                'vehicles' => $vehicles,
                                'allMessagesList' => $allMessagesList,
                                'post_items'=>$post_items
                                
                    ));
                    }else{
                        return view('orders.seller_order_details_ptl', array(
                    		'orderDetails' => $orderDetails,
                    		'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                    		//'tracking_order' => $seller_post_payment->tracking,
                                'vehicles' => $vehicles,
                                'allMessagesList' => $allMessagesList,
                                'post_items'=>$post_items
                    ));
                    }
                    break;
                    case RELOCATION_DOMESTIC :
                    	$orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','u.username', 'bq.transaction_id as trans_id','bq.lkp_post_ratecard_type_id','bq.lkp_load_category_id','bq.lkp_vehicle_category_id', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                      
                    	//$orderDetails = $query->select('orders.*','orders.id as orderid', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','u.username', 'buyer_quotes.transaction_id as trans_id', 'lkp_load_types.load_type', 'buyer_quote_items.*', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                    	//echo $orderDetails->seller_post_item_id;
                    	$allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                    	
                    	if($orderDetails->lkp_order_type_id==1)   {
                    		$seller_post_payment    = DB::table('relocation_seller_posts')
                    		->join('relocation_seller_post_items','relocation_seller_posts.id','=','relocation_seller_post_items.seller_post_id')
                    		->where('relocation_seller_posts.id',$orderDetails->seller_post_item_id)
                    		->select('relocation_seller_posts.lkp_payment_mode_id')
                    		->first();
                    		
                    		
                    		$payment_buyer_details = DB::table('lkp_payment_modes')
                    		->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    		->select('id','payment_mode')
                    		->first();
                    
                    
                    		return view('orders.seller_order_details', array(
                    				'orderDetails' => $orderDetails,
                    				'payment_mode_seller' => $payment_buyer_details->payment_mode,
                    				//'vehicles' => $vehicles,
                    				'allMessagesList' => $allMessagesList
                    		));
                    	}else{
                    		
                    		$post_items = \DB::table('term_buyer_quotes as bq')
                    		->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                    		->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                    		->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                    		->where('bqi.id',$orderDetails->buyer_quote_item_id)
                    		->select('bq.*','bqi.*','ciq.volume as cft','bqi.id','bqi.units as unit', 'ciq.volumetricweight as unit')->get();
                    		
                    		
                    		return view('orders.seller_order_details', array(
                    				'orderDetails' => $orderDetails,
                    				//'vehicles' => $vehicles,
                    				'post_items' => $post_items,
                    				'allMessagesList' => $allMessagesList
                    		));
                    	}
                    	break;
                    case RELOCATION_OFFICE_MOVE :
                        $orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','u.username', 'bq.transaction_id as trans_id','or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                      
                        //$orderDetails = $query->select('orders.*','orders.id as orderid', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','u.username', 'buyer_quotes.transaction_id as trans_id', 'lkp_load_types.load_type', 'buyer_quote_items.*', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                        //echo $orderDetails->seller_post_item_id;
                        $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                        
                        if($orderDetails->lkp_order_type_id==1)   {
                            $seller_post_payment    = DB::table('relocationoffice_seller_posts')
                            ->where('relocationoffice_seller_posts.id',$orderDetails->seller_post_item_id)
                            ->select('relocationoffice_seller_posts.lkp_payment_mode_id')
                            ->first();
                            
                            
                            $payment_buyer_details = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                            ->select('id','payment_mode')
                            ->first();
                    
                    
                            return view('orders.seller_order_details', array(
                                    'orderDetails' => $orderDetails,
                                    'payment_mode_seller' => $payment_buyer_details->payment_mode,
                                    //'vehicles' => $vehicles,
                                    'allMessagesList' => $allMessagesList
                            ));
                        }else{
                            
                            $post_items = \DB::table('term_buyer_quotes as bq')
                            ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                            ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                            ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                            ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                            ->select('bq.*','bqi.*','ciq.volume as cft','bqi.id','bqi.units as unit', 'ciq.volumetricweight as unit')->get();
                            
                            
                            return view('orders.seller_order_details', array(
                                    'orderDetails' => $orderDetails,
                                    //'vehicles' => $vehicles,
                                    'post_items' => $post_items,
                                    'allMessagesList' => $allMessagesList
                            ));
                        }
                        break;
                        
                    case RELOCATION_PET_MOVE :
                    	$orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','u.username', 'bq.transaction_id as trans_id','bq.lkp_pet_type_id','bq.lkp_breed_type_id','bq.lkp_cage_type_id', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                      
                    	$allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                        $seller_post_payment    = DB::table('relocationpet_seller_posts as sp')
                        //->join('relocation_seller_post_items as spi','sp.id','=','spi.seller_post_id')
                        ->where('sp.id',$orderDetails->seller_post_item_id)
                        ->select('sp.lkp_payment_mode_id')
                        ->first();
                        $payment_buyer_details = DB::table('lkp_payment_modes')
                        ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                        ->select('id','payment_mode')
                        ->first();
                        return view('orders.seller_order_details', array(
                                        'orderDetails' => $orderDetails,
                                        'payment_mode_seller' => $payment_buyer_details->payment_mode,
                                        'allMessagesList' => $allMessagesList
                        ));
                    	break;    
                        
                    case ROAD_TRUCK_HAUL :
                    $orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','u.username', DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id end) as trans_id"), 'lkp_load_types.load_type',  'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lvt.vehicle_type as vehicle_type', 'lkp_payment_modes.payment_mode', 'bqi.lkp_quote_price_type_id')->first();
                    //echo "<pre>";print_r($orderDetails);exit;                    
                    $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                    if($orderDetails->lkp_order_type_id==1)   { 
                    $seller_post_payment    = DB::table('truckhaul_seller_post_items')
                    ->join('truckhaul_seller_posts','truckhaul_seller_posts.id','=','truckhaul_seller_post_items.seller_post_id')
                    ->where('truckhaul_seller_post_items.id',$orderDetails->seller_post_item_id)
                    ->select('truckhaul_seller_posts.lkp_payment_mode_id','truckhaul_seller_posts.tracking')
                    ->first();

                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();	

                    return view('truckhaul.sellers.seller_order_details', array(
                                'orderDetails' => $orderDetails,
                                'payment_mode_seller' => $payment_buyer_details->payment_mode,
                                'vehicles' => $vehicles,
                                'allMessagesList' => $allMessagesList,
                                'tracking_order' => $seller_post_payment->tracking,
                    ));
                    }  
                    break;
                    
                    case ROAD_TRUCK_LEASE :
                    $orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','u.username', DB::raw("(case when `orders`.`lkp_order_type_id` = 1 then bq.transaction_id end) as trans_id"), 'lkp_load_types.load_type',  'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city',  'lkp_payment_modes.payment_mode', 'bqi.lkp_quote_price_type_id')->first();
                    //echo "<pre>";print_r($orderDetails);exit;                    
                    $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                    if($orderDetails->lkp_order_type_id==1)   { 
                    $seller_post_payment    = DB::table('trucklease_seller_post_items')
                    ->join('trucklease_seller_posts','trucklease_seller_posts.id','=','trucklease_seller_post_items.seller_post_id')
                    ->where('trucklease_seller_post_items.id',$orderDetails->seller_post_item_id)
                    ->select('trucklease_seller_posts.lkp_payment_mode_id','trucklease_seller_posts.tracking','trucklease_seller_post_items.lkp_trucklease_lease_term_id',
                            'trucklease_seller_post_items.minimum_lease_period','trucklease_seller_post_items.vehicle_make_model_year','trucklease_seller_post_items.fuel_included','trucklease_seller_post_items.driver_availability')
                    ->first();
                    //echo "<pre>";print_r($seller_post_payment);exit;        
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();	

                    return view('trucklease.sellers.seller_order_details', array(
                                'orderDetails' => $orderDetails,
                                'payment_mode_seller' => $payment_buyer_details->payment_mode,
                                'vehicles' => $vehicles,
                                'allMessagesList' => $allMessagesList,
                                'tracking' => $seller_post_payment->tracking,                               
                                'minimumLeasePeriod' => $seller_post_payment->minimum_lease_period,
                                'vehicleMakeModelYear' => $seller_post_payment->vehicle_make_model_year,
                                'fuelIncluded' => $seller_post_payment->fuel_included,
                                'driverAvailability' => $seller_post_payment->driver_availability,
                                'truckleaseLeaseTermId' => $seller_post_payment->lkp_trucklease_lease_term_id,
                    ));
                    }  
                    break;
                    case RELOCATION_INTERNATIONAL :
                        $orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','u.username', 'bq.transaction_id as trans_id', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                      
                        //$orderDetails = $query->select('orders.*','orders.id as orderid', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','u.username', 'buyer_quotes.transaction_id as trans_id', 'lkp_load_types.load_type', 'buyer_quote_items.*', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                        //echo $orderDetails->seller_post_item_id;
                        $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                        
                        if($orderDetails->lkp_order_type_id==1)   {
                            if($orderDetails->lkp_international_type_id==2){
                                $seller_post_payment    = DB::table('relocationint_seller_posts')
                                ->join('relocationint_seller_post_items','relocationint_seller_posts.id','=','relocationint_seller_post_items.seller_post_id')
                                ->where('relocationint_seller_posts.id',$orderDetails->seller_post_item_id)
                                ->select('relocationint_seller_posts.lkp_payment_mode_id')
                                ->first();
                            }else{
                                $seller_post_payment    = DB::table('relocationint_seller_posts')
                                ->where('relocationint_seller_posts.id',$orderDetails->seller_post_item_id)
                                ->select('relocationint_seller_posts.lkp_payment_mode_id')
                                ->first();
                            }
                            $payment_buyer_details = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                            ->select('id','payment_mode')
                            ->first();
                            return view('orders.seller_order_details', array(
                                    'orderDetails' => $orderDetails,
                                    'payment_mode_seller' => $payment_buyer_details->payment_mode,
                                    //'vehicles' => $vehicles,
                                    'allMessagesList' => $allMessagesList
                            ));
                        }else{
                            
                            $post_items = \DB::table('term_buyer_quotes as bq')
                            ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                            ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                            ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                            ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                            ->select('bq.*','bqi.*','ciq.volume as cft','bqi.id','bqi.units as unit', 'ciq.volumetricweight as unit', 'bqi.number_loads','bqi.avg_kg_per_move')->get();
                            
                            
                            return view('orders.seller_order_details', array(
                                    'orderDetails' => $orderDetails,
                                    //'vehicles' => $vehicles,
                                    'post_items' => $post_items,
                                    'allMessagesList' => $allMessagesList
                            ));
                        }
                        break;
                    case RELOCATION_GLOBAL_MOBILITY :
                        $orderDetails = $query->select('invoice.total_amt as inv_price','orders.*','orders.id as orderid', 'orders.dispatch_date as orderdispatch','orders.delivery_date as orderdelivery','u.username', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                      
                        $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                        
                        if($orderDetails->lkp_order_type_id==1)   {
                            $seller_post_payment    = DB::table('relocationgm_seller_posts')
                            ->where('relocationgm_seller_posts.id',$orderDetails->seller_post_item_id)
                            ->select('relocationgm_seller_posts.lkp_payment_mode_id')
                            ->first();
                            
                            
                            $payment_buyer_details = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                            ->select('id','payment_mode')
                            ->first();
                    
                    
                            return view('orders.seller_order_details', array(
                                    'orderDetails' => $orderDetails,
                                    'payment_mode_seller' => $payment_buyer_details->payment_mode,
                                    'allMessagesList' => $allMessagesList
                            ));
                        }else{
                            
                            $post_items = \DB::table('term_buyer_quotes as bq')
                            ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                            ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                            ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                            ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                            ->select('bq.*','bqi.*','ciq.volume as cft','bqi.id','bqi.units as unit', 'ciq.volumetricweight as unit')->get();
                            
                            
                            return view('orders.seller_order_details', array(
                                    'orderDetails' => $orderDetails,
                                    //'vehicles' => $vehicles,
                                    'post_items' => $post_items,
                                    'allMessagesList' => $allMessagesList
                            ));
                        }
                        break;

                    
                    default :
                    $orderDetails = $query->select('orders.*', 'u.username', 'buyer_quotes.transaction_id as trans_id', 'lkp_load_types.load_type', 'buyer_quote_items.*', 'or.receipt_no as receipt', 'or.frieght_amount as receipt_frieght', 'or.insurance as receipt_insurance', 'or.service_charge_amount as receipt_service_charge', 'or.service_tax_amount as receipt_service_tax', 'or.total_amount as receipt_total', 'oi.invoice_no as invoice', 'oi.service_charge_amount as inv_service_charge', 'oi.service_tax_amount as inv_service_tax', 'oi.total_amount as inv_total', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'pvd.*', 'lvt.*', 'lkp_payment_modes.payment_mode')->first();
                    return view('orders.seller_order_details', array(
                    		'orderDetails' => $orderDetails,
                                'vehicles' => $vehicles
                    ));
                    break;
                }
            // echo $this->user_pk;
            // echo "<pre>";print_r($orderDetails);die();
            
        } else {
            return view('orders.seller_order');
        }
    }

    /**
     * generating consignmentPickup.
     *
     * @return void
     */
    public function consignmentPickup(Request $request, $id) {
       // print_r($request);exit;
        Log::info('consignmentPickup is initiated by user: ' . Auth::id(), array(
            'c' => '1'
        ));
        CommonComponent::activityLog("SELLER_CONSIGNMENT_PICKUP", SELLER_CONSIGNMENT_PICKUP, 0, HTTP_REFERRER, CURRENT_URL);
        if (!empty(Input::all())&& !empty($request)){
            SellerOrderComponent::consignmentPickup($request, $id);            
        }
        
        $order = Order::select('orders.*','lkp_order_statuses.order_status')
                ->where('orders.id', $id)->where('orders.seller_id', Auth::id())
                ->leftjoin('lkp_order_statuses', 'lkp_order_statuses.id', '=', 'orders.lkp_order_status_id')->first();        
        $qry        =   DB::table('pickup_vehicle_details as pvd');
        $vehicles   =   $qry->leftjoin('vehicle_details as veh','veh.vehicle_number','=','pvd.vehicle_no')
                                ->where('pvd.order_id', '=', $id)->select('pvd.*','veh.volty_register')                               
                                ->groupby('pvd.vehicle_no')
                                ->get();

        $locations = OrderTrackingDetail::select('tracking_location', 'tracking_date')->where('order_id', $id)->get();
        $invoice = SellerOrderInvoice::where('order_id', $id)->first();
        $receipt = OrderReceipt::where('order_id', $id)->first();
        $payment = \DB::table('orders')->leftjoin('order_payments', 'orders.order_payment_id', '=', 'order_payments.id')->leftjoin('lkp_payment_modes', 'lkp_payment_modes.id', '=', 'order_payments.lkp_payment_mode_id')->where('orders.id', $id)->select('lkp_payment_modes.payment_mode')->first();
        if (empty($invoice)) {
            $invoice = new SellerOrderInvoice ();
            $invoice->invoice_no = '';
        }
        if (empty($receipt)) {
            $receipt = new OrderReceipt ();
            $receipt->receipt_no = '';
        }
        $serviceId = Session::get('service_id');
        $pickup_charges='0.00';$delivery_charges='0.00';$oda_charges='0.00';
        if(!empty($order)){
        switch ($serviceId) {
            case ROAD_FTL :
            if(isset($order->buyer_quote_item_id)&& $order->buyer_quote_item_id!=0 && $order->buyer_quote_item_id!=""){                
                if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1)
                    $post = \DB::table('buyer_quote_items')
                        ->leftjoin('buyer_quotes', 'buyer_quotes.id', '=', 'buyer_quote_items.buyer_quote_id')
                        ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'buyer_quote_items.lkp_load_type_id')
                        ->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'buyer_quote_items.lkp_vehicle_type_id')
                        ->leftjoin('lkp_cities as c1', 'buyer_quote_items.from_city_id', '=', 'c1.id')
                        ->leftjoin('lkp_cities as c2', 'buyer_quote_items.to_city_id', '=', 'c2.id')
                        ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'buyer_quote_items.lkp_post_status_id')
                        ->leftjoin('users as u', 'u.id', '=', 'buyer_quotes.buyer_id')
                        ->where('buyer_quote_items.id', $order->buyer_quote_item_id)
                        ->select('buyer_quote_items.from_city_id as from_location_id','buyer_quote_items.to_city_id as to_location_id','buyer_quote_items.number_loads','buyer_quote_items.dispatch_date as dispatch', 'buyer_quote_items.delivery_date as delivery', 'buyer_quotes.transaction_id as transid', 'lkp_vehicle_types.vehicle_type as vehicle', 'lkp_load_types.load_type as load', 'c1.city_name as from', 'c2.city_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
                elseif(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==2)
                    $post = \DB::table('term_buyer_quote_items as bqi')
                        ->leftjoin('term_buyer_quotes as bq', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                        ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                        ->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'bqi.lkp_vehicle_type_id')
                        ->leftjoin('lkp_cities as c1', 'bqi.from_location_id', '=', 'c1.id')
                        ->leftjoin('lkp_cities as c2', 'bqi.to_location_id', '=', 'c2.id')
                        ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
                        ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                        ->where('bqi.id', $order->buyer_quote_item_id)->select('bqi.from_location_id','bqi.to_location_id','bq.from_date as dispatch', 'bq.to_date as delivery', 'bq.transaction_id as transid', 'lkp_vehicle_types.vehicle_type as vehicle', 'lkp_load_types.load_type as load', 'c1.city_name as from', 'c2.city_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
            }else{
                $post    = DB::table('seller_post_items as spi')
                   ->join('seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'spi.lkp_load_type_id')
                    ->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'spi.lkp_vehicle_type_id')
                    ->leftjoin('lkp_cities as c1', 'spi.from_location_id', '=', 'c1.id')
                    ->leftjoin('lkp_cities as c2', 'spi.to_location_id', '=', 'c2.id')
                    ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'sp.lkp_post_status_id')
                    ->leftjoin('users as u', 'u.id', '=', 'sp.seller_id')
                    ->where('spi.id', $order->seller_post_item_id)
                    ->select('sp.from_date as dispatch', 'sp.to_date as delivery', 'sp.transaction_id as transid', 'lkp_vehicle_types.vehicle_type as vehicle', 'lkp_load_types.load_type as load', 'c1.city_name as from', 'c2.city_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
            } 
            if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                $tracking    = DB::table('seller_post_items as spi')
                ->join('seller_posts as sp','sp.id','=','spi.seller_post_id')->where('spi.id', $order->seller_post_item_id)
                ->select('sp.tracking')->first();
                $tracking=$tracking->tracking;
            }else{
                $tracking=1;
            }
                return view('orders.seller_consignment_pickup', array(
                'order' => $order,
                'post' => $post,
                'vehicles' => $vehicles,
                'pickExist' => $order->seller_pickup_lr_number,
                'deliveryExist' => $order->seller_delivery_driver_name,
                'trackingExist' => $order->tracking_confirm,
                'vehicleExist' => $order->vehicle_confirm,
                'invoiceExist' => $invoice->invoice_no,
                'receiptExist' => $receipt->receipt_no,
                'locations' => $locations,
                'invoice' => $invoice,
                'receipt' => $receipt,
                'payment_mode' => $payment->payment_mode,
                'tracking'=>$tracking
                ));
            
            
                break;
            case ROAD_PTL     :
                if(isset($order->buyer_quote_id)&& $order->buyer_quote_id!=0 && $order->buyer_quote_id!="") { 
                    
                        $post = \DB::table('ptl_buyer_quotes as bq')->leftjoin('ptl_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')->leftjoin('lkp_ptl_pincodes as c1', 'bq.from_location_id', '=', 'c1.id')->leftjoin('lkp_ptl_pincodes as c2', 'bq.to_location_id', '=', 'c2.id')->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')->where('bq.id', $order->buyer_quote_id)
                                ->select('bq.from_location_id','bq.to_location_id','bq.is_dispatch_flexible as dispatch_flexible', 'bq.is_delivery_flexible as delivery_flexible','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery','bq.is_door_pickup as door_pickup','bq.is_door_delivery as door_delivery', 'bq.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id')->first();
                        $post_items = \DB::table('ptl_buyer_quotes as bq')
                                ->leftjoin('ptl_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                                ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                ->where('bq.id',$post->buyer_quote_id)
                                ->select('bqi.number_packages','bqi.length','bqi.breadth','bqi.height','bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')->get();
                    
                }
                elseif(isset($order->buyer_quote_item_id)&& $order->buyer_quote_item_id!=0 && $order->buyer_quote_item_id!="") { 
                    if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                    $post = \DB::table('ptl_buyer_quotes as bq')->leftjoin('ptl_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')->leftjoin('lkp_ptl_pincodes as c1', 'bq.from_location_id', '=', 'c1.id')->leftjoin('lkp_ptl_pincodes as c2', 'bq.to_location_id', '=', 'c2.id')->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')->where('bqi.id', $order->buyer_quote_item_id)
                            ->select('bq.is_dispatch_flexible as dispatch_flexible', 'bq.is_delivery_flexible as delivery_flexible','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery','bq.is_door_pickup as door_pickup','bq.is_door_delivery as door_delivery', 'bq.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id','bqi.id as buyer_quote_item_id')->first();
                    $post_items = \DB::table('ptl_buyer_quotes as bq')
                            ->leftjoin('ptl_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->where('bqi.id',$post->buyer_quote_item_id)
                            ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')->get();
                    }else{
                        $post = \DB::table('term_buyer_quotes as bq')
                                ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                ->leftjoin('lkp_ptl_pincodes as c1', 'bqi.from_location_id', '=', 'c1.id')
                                ->leftjoin('lkp_ptl_pincodes as c2', 'bqi.to_location_id', '=', 'c2.id')->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')->where('bqi.id', $order->buyer_quote_item_id)
                            ->select('bqi.from_location_id','bqi.to_location_id','bq.from_date as dispatch', 'bq.to_date as delivery','bq.is_door_pickup as door_pickup','bq.is_door_delivery as door_delivery', 'bq.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id','bqi.id as buyer_quote_item_id')->first();
                        $post_items = \DB::table('term_buyer_quotes as bq')
                            ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                            //->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                            ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')        
                            ->where('bqi.id',$post->buyer_quote_item_id)
                            ->select('ciq.noofpackages','bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                    }
                }
                else{
                    $post    = DB::table('ptl_seller_post_items as spi')
                    ->join('ptl_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->leftjoin('lkp_ptl_pincodes as c1', 'spi.from_location_id', '=', 'c1.id')
                    ->leftjoin('lkp_ptl_pincodes as c2', 'spi.to_location_id', '=', 'c2.id')
                    ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'sp.lkp_post_status_id')
                    ->leftjoin('users as u', 'u.id', '=', 'sp.seller_id')
                    ->where('spi.id', $order->seller_post_item_id)
                    ->select('sp.from_date as dispatch', 'sp.to_date as delivery', 'sp.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','sp.id as buyer_quote_id')->first();
                    $post_items = \DB::table('ptl_seller_posts as sp')
                    ->leftjoin('ptl_seller_post_items as spi', 'sp.id', '=', 'spi.seller_post_id')                    
                    ->where('sp.id',$post->buyer_quote_id)
                    ->select('spi.id','spi.units as unit')->get();
                    
                }
                if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                $tracking    = DB::table('ptl_seller_post_items as spi')
                ->join('ptl_seller_posts as sp','sp.id','=','spi.seller_post_id')->where('spi.id', $order->seller_post_item_id)
                ->select('sp.tracking','sp.pickup_charges','sp.delivery_charges','sp.oda_charges')->first();
            
            $pickup_charges=$tracking->pickup_charges;$delivery_charges=$tracking->delivery_charges;$oda_charges=$tracking->oda_charges;
            $tracking=$tracking->tracking;
            }else{
                $tracking=1;
            }//echo "<pre>";print_r($post);exit;
                    return view('ptl.sellers.seller_consignment_pickup', array(
            'order' => $order,
            'post' => $post,
            'vehicles' => $vehicles,
            'pickExist' => $order->seller_pickup_lr_number,
            'deliveryExist' => $order->seller_delivery_driver_name,
            'trackingExist' => $order->tracking_confirm,
            'vehicleExist' => $order->vehicle_confirm,
            'invoiceExist' => $invoice->invoice_no,
            'receiptExist' => $receipt->receipt_no,
            'locations' => $locations,
            'invoice' => $invoice,
            'receipt' => $receipt,
            'payment_mode' => $payment->payment_mode,
            'post_items' => $post_items,
            'tracking'=>$tracking,
            'pickup_charges'=>$pickup_charges,            
            'delivery_charges'=>$delivery_charges,
            'oda_charges'=>$oda_charges                
        ));
                
                break;
            case RAIL     :
                if(isset($order->buyer_quote_id)&& $order->buyer_quote_id!=0 && $order->buyer_quote_id!="") { 
                    $post = \DB::table('rail_buyer_quotes as bq')->leftjoin('rail_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')->leftjoin('lkp_ptl_pincodes as c1', 'bq.from_location_id', '=', 'c1.id')->leftjoin('lkp_ptl_pincodes as c2', 'bq.to_location_id', '=', 'c2.id')->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')->where('bq.id', $order->buyer_quote_id)
                            ->select('bq.from_location_id','bq.to_location_id','bq.is_dispatch_flexible as dispatch_flexible', 'bq.is_delivery_flexible as delivery_flexible','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery','bq.is_door_pickup as door_pickup','bq.is_door_delivery as door_delivery', 'bq.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id')->first();
                    $post_items = \DB::table('rail_buyer_quotes as bq')
                            ->leftjoin('rail_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->where('bq.id',$post->buyer_quote_id)
                            ->select('bqi.number_packages','bqi.length','bqi.breadth','bqi.height','bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')->get();
                }
                elseif(isset($order->buyer_quote_item_id)&& $order->buyer_quote_item_id!=0 && $order->buyer_quote_item_id!="") { 
                    if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                    $post = \DB::table('rail_buyer_quotes as bq')->leftjoin('rail_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')->leftjoin('lkp_ptl_pincodes as c1', 'bq.from_location_id', '=', 'c1.id')->leftjoin('lkp_ptl_pincodes as c2', 'bq.to_location_id', '=', 'c2.id')->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')->where('bqi.id', $order->buyer_quote_item_id)
                            ->select('bq.is_dispatch_flexible as dispatch_flexible', 'bq.is_delivery_flexible as delivery_flexible','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery','bq.is_door_pickup as door_pickup','bq.is_door_delivery as door_delivery', 'bq.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id','bqi.id as buyer_quote_item_id')->first();
                    $post_items = \DB::table('rail_buyer_quotes as bq')
                            ->leftjoin('rail_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->where('bqi.id',$post->buyer_quote_item_id)
                            ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')->get();
                    }else{
                        $post = \DB::table('term_buyer_quotes as bq')
                                ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                ->leftjoin('lkp_ptl_pincodes as c1', 'bqi.from_location_id', '=', 'c1.id')
                                ->leftjoin('lkp_ptl_pincodes as c2', 'bqi.to_location_id', '=', 'c2.id')
                                ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')->where('bqi.id', $order->buyer_quote_item_id)
                            ->select('bqi.from_location_id','bqi.to_location_id','bq.from_date as dispatch', 'bq.to_date as delivery','bq.is_door_pickup as door_pickup','bq.is_door_delivery as door_delivery', 'bq.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id','bqi.id as buyer_quote_item_id')->first();
                        $post_items = \DB::table('term_buyer_quotes as bq')
                            ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                            ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')     
                            ->where('bqi.id',$post->buyer_quote_item_id)
                            ->select('ciq.noofpackages','bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                    }
                }
                else{
                    $post    = DB::table('rail_seller_post_items as spi')
                    ->join('rail_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->leftjoin('lkp_ptl_pincodes as c1', 'spi.from_location_id', '=', 'c1.id')
                    ->leftjoin('lkp_ptl_pincodes as c2', 'spi.to_location_id', '=', 'c2.id')
                    ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'sp.lkp_post_status_id')
                    ->leftjoin('users as u', 'u.id', '=', 'sp.seller_id')
                    ->where('spi.id', $order->seller_post_item_id)
                    ->select('sp.from_date as dispatch', 'sp.to_date as delivery', 'sp.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','sp.id as buyer_quote_id')->first();
                    $post_items = \DB::table('rail_seller_posts as sp')
                    ->leftjoin('rail_seller_post_items as spi', 'sp.id', '=', 'spi.seller_post_id')
                    ->where('sp.id',$post->buyer_quote_id)
                    ->select('spi.id','spi.units as unit')->get();
                    
                }
                if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                $tracking    = DB::table('rail_seller_post_items as spi')
                ->join('rail_seller_posts as sp','sp.id','=','spi.seller_post_id')->where('spi.id', $order->seller_post_item_id)
                ->select('sp.tracking','sp.pickup_charges','sp.delivery_charges','sp.oda_charges')->first();
            
            $pickup_charges=$tracking->pickup_charges;$delivery_charges=$tracking->delivery_charges;$oda_charges=$tracking->oda_charges;
            $tracking=$tracking->tracking;
            }else{
                $tracking=1;
            }
                    return view('ptl.sellers.seller_consignment_pickup', array(
            'order' => $order,
            'post' => $post,
            'vehicles' => $vehicles,
            'pickExist' => $order->seller_pickup_lr_number,
            'deliveryExist' => $order->seller_delivery_driver_name,
            'trackingExist' => $order->tracking_confirm,
            'vehicleExist' => $order->vehicle_confirm,
            'invoiceExist' => $invoice->invoice_no,
            'receiptExist' => $receipt->receipt_no,
            'locations' => $locations,
            'invoice' => $invoice,
            'receipt' => $receipt,
            'payment_mode' => $payment->payment_mode,
            'post_items' => $post_items,
            'tracking'=>$tracking,
            'pickup_charges'=>$pickup_charges,            
            'delivery_charges'=>$delivery_charges,
            'oda_charges'=>$oda_charges            
        ));
                
                break;
            case AIR_DOMESTIC     :
                if(isset($order->buyer_quote_id)&& $order->buyer_quote_id!=0 && $order->buyer_quote_id!="") { 
                    $post = \DB::table('airdom_buyer_quotes as bq')->leftjoin('airdom_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')->leftjoin('lkp_ptl_pincodes as c1', 'bq.from_location_id', '=', 'c1.id')->leftjoin('lkp_ptl_pincodes as c2', 'bq.to_location_id', '=', 'c2.id')->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')->where('bq.id', $order->buyer_quote_id)
                            ->select('bq.from_location_id','bq.to_location_id','bq.is_dispatch_flexible as dispatch_flexible', 'bq.is_delivery_flexible as delivery_flexible','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery','bq.is_door_pickup as door_pickup','bq.is_door_delivery as door_delivery', 'bq.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id')->first();
                    $post_items = \DB::table('airdom_buyer_quotes as bq')
                            ->leftjoin('airdom_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->where('bq.id',$post->buyer_quote_id)
                            ->select('bqi.number_packages','bqi.length','bqi.breadth','bqi.height','bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')->get();
                }
                elseif(isset($order->buyer_quote_item_id)&& $order->buyer_quote_item_id!=0 && $order->buyer_quote_item_id!="") { 
                    if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                    $post = \DB::table('airdom_buyer_quotes as bq')->leftjoin('airdom_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')->leftjoin('lkp_ptl_pincodes as c1', 'bq.from_location_id', '=', 'c1.id')->leftjoin('lkp_ptl_pincodes as c2', 'bq.to_location_id', '=', 'c2.id')->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')->where('bqi.id', $order->buyer_quote_item_id)
                            ->select('bq.is_dispatch_flexible as dispatch_flexible', 'bq.is_delivery_flexible as delivery_flexible','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery','bq.is_door_pickup as door_pickup','bq.is_door_delivery as door_delivery', 'bq.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id','bqi.id as buyer_quote_item_id')->first();
                    $post_items = \DB::table('airdom_buyer_quotes as bq')
                            ->leftjoin('airdom_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->where('bqi.id',$post->buyer_quote_item_id)
                            ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')->get();
                    }else{
                        $post = \DB::table('term_buyer_quotes as bq')
                                ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                ->leftjoin('lkp_ptl_pincodes as c1', 'bqi.from_location_id', '=', 'c1.id')
                                ->leftjoin('lkp_ptl_pincodes as c2', 'bqi.to_location_id', '=', 'c2.id')->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')->where('bqi.id', $order->buyer_quote_item_id)
                            ->select('bqi.from_location_id','bqi.to_location_id','bq.from_date as dispatch', 'bq.to_date as delivery','bq.is_door_pickup as door_pickup','bq.is_door_delivery as door_delivery', 'bq.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id','bqi.id as buyer_quote_item_id')->first();
                        $post_items = \DB::table('term_buyer_quotes as bq')
                            ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                            ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')     
                            ->where('bqi.id',$post->buyer_quote_item_id)
                            ->select('ciq.noofpackages','bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                    }
                }
                else{
                    $post    = DB::table('airdom_seller_post_items as spi')
                    ->join('airdom_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->leftjoin('lkp_ptl_pincodes as c1', 'spi.from_location_id', '=', 'c1.id')
                    ->leftjoin('lkp_ptl_pincodes as c2', 'spi.to_location_id', '=', 'c2.id')
                    ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'sp.lkp_post_status_id')
                    ->leftjoin('users as u', 'u.id', '=', 'sp.seller_id')
                    ->where('spi.id', $order->seller_post_item_id)
                    ->select('sp.from_date as dispatch', 'sp.to_date as delivery', 'sp.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','sp.id as buyer_quote_id')->first();
                    $post_items = \DB::table('airdom_seller_posts as sp')
                    ->leftjoin('airdom_seller_post_items as spi', 'sp.id', '=', 'spi.seller_post_id')
                    ->where('sp.id',$post->buyer_quote_id)
                    ->select('spi.id','spi.units as unit')->get();
                    
                }
                if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                $tracking    = DB::table('airdom_seller_post_items as spi')
                ->join('airdom_seller_posts as sp','sp.id','=','spi.seller_post_id')->where('spi.id', $order->seller_post_item_id)
                ->select('sp.tracking','sp.oda_charges')->first();
            
            $oda_charges=$tracking->oda_charges;
            $tracking=$tracking->tracking;
            }else{
                $tracking=1;
            }
                    return view('ptl.sellers.seller_consignment_pickup', array(
            'order' => $order,
            'post' => $post,
            'vehicles' => $vehicles,
            'pickExist' => $order->seller_pickup_lr_number,
            'deliveryExist' => $order->seller_delivery_driver_name,
            'trackingExist' => $order->tracking_confirm,
            'vehicleExist' => $order->vehicle_confirm,
            'invoiceExist' => $invoice->invoice_no,
            'receiptExist' => $receipt->receipt_no,
            'locations' => $locations,
            'invoice' => $invoice,
            'receipt' => $receipt,
            'payment_mode' => $payment->payment_mode,
            'post_items' => $post_items,
            'tracking'=>$tracking,
            'oda_charges'=>$oda_charges
        ));
                
                break;
            case AIR_INTERNATIONAL     :
                if(isset($order->buyer_quote_id)&& $order->buyer_quote_id!=0 && $order->buyer_quote_id!="") { 
                    $post = \DB::table('airint_buyer_quotes as bq')
                            ->leftjoin('airint_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_airports as c1', 'bq.from_location_id', '=', 'c1.id')
                            ->leftjoin('lkp_airports as c2', 'bq.to_location_id', '=', 'c2.id')
                            ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
                            ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                            ->leftjoin('lkp_air_ocean_shipment_types as st', 'st.id', '=', 'bq.lkp_air_ocean_shipment_type_id')
                            ->leftjoin('lkp_air_ocean_sender_identities as si', 'si.id', '=', 'bq.lkp_air_ocean_sender_identity_id')
                            ->where('bq.id', $order->buyer_quote_id)
                            ->select('bq.from_location_id','bq.to_location_id','bq.product_made','bq.ie_code','si.sender_identity','st.shipment_type','bq.is_dispatch_flexible as dispatch_flexible', 'bq.is_delivery_flexible as delivery_flexible','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery', 'bq.transaction_id as transid', 'c1.airport_name as from', 'c2.airport_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id')->first();
                    $post_items = \DB::table('airint_buyer_quotes as bq')
                            ->leftjoin('airint_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->where('bq.id',$post->buyer_quote_id)
                            ->select('bqi.number_packages','bqi.length','bqi.breadth','bqi.height','bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')->get();
                }
                elseif(isset($order->buyer_quote_item_id)&& $order->buyer_quote_item_id!=0 && $order->buyer_quote_item_id!="") { 
                    if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                    $post = \DB::table('airint_buyer_quotes as bq')
                            ->leftjoin('airint_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_airports as c1', 'bq.from_location_id', '=', 'c1.id')
                            ->leftjoin('lkp_airports as c2', 'bq.to_location_id', '=', 'c2.id')
                            ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
                            ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                            ->leftjoin('lkp_air_ocean_shipment_types as st', 'st.id', '=', 'bq.lkp_air_ocean_shipment_type_id')
                            ->leftjoin('lkp_air_ocean_sender_identities as si', 'si.id', '=', 'bq.lkp_air_ocean_sender_identity_id')
                            ->where('bqi.id', $order->buyer_quote_item_id)
                            ->select('bq.product_made','bq.ie_code','si.sender_identity','st.shipment_type','bq.is_dispatch_flexible as dispatch_flexible', 'bq.is_delivery_flexible as delivery_flexible','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery', 'bq.transaction_id as transid', 'c1.airport_name as from', 'c2.airport_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id','bqi.id as buyer_quote_item_id')->first();
                    $post_items = \DB::table('airint_buyer_quotes as bq')
                            ->leftjoin('airint_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->where('bqi.id',$post->buyer_quote_item_id)
                            ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')->get();
                    }else{
                        $post = \DB::table('term_buyer_quotes as bq')
                                ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                ->leftjoin('lkp_airports as c1', 'bqi.from_location_id', '=', 'c1.id')
                                ->leftjoin('lkp_airports as c2', 'bqi.to_location_id', '=', 'c2.id')
                                ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
                                ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                                ->leftjoin('lkp_air_ocean_shipment_types as st', 'st.id', '=', 'bqi.lkp_air_ocean_shipment_type_id')
                                ->leftjoin('lkp_air_ocean_sender_identities as si', 'si.id', '=', 'bqi.lkp_air_ocean_sender_identity_id')
                                ->where('bqi.id', $order->buyer_quote_item_id)
                            ->select('bqi.from_location_id','bqi.to_location_id','bqi.product_made','bqi.ie_code','si.sender_identity','st.shipment_type','bq.from_date as dispatch', 'bq.to_date as delivery','bq.is_door_pickup as door_pickup','bq.is_door_delivery as door_delivery', 'bq.transaction_id as transid', 'c1.airport_name as from', 'c2.airport_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id','bqi.id as buyer_quote_item_id')->first();
                        $post_items = \DB::table('term_buyer_quotes as bq')
                            ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                            ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')     
                            ->where('bqi.id',$post->buyer_quote_item_id)
                            ->select('ciq.noofpackages','bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                    }
                }
                else{
                    $post    = DB::table('airint_seller_post_items as spi')
                    ->join('airint_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->leftjoin('lkp_airports as c1', 'spi.from_location_id', '=', 'c1.id')
                    ->leftjoin('lkp_airports as c2', 'spi.to_location_id', '=', 'c2.id')
                    ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'sp.lkp_post_status_id')
                    ->leftjoin('users as u', 'u.id', '=', 'sp.seller_id')
                    ->where('spi.id', $order->seller_post_item_id)
                    ->select('sp.from_date as dispatch', 'sp.to_date as delivery', 'sp.transaction_id as transid', 'c1.airport_name as from', 'c2.airport_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','sp.id as buyer_quote_id')->first();
                    $post_items = \DB::table('airint_seller_posts as sp')
                    ->leftjoin('airint_seller_post_items as spi', 'sp.id', '=', 'spi.seller_post_id')
                    ->where('sp.id',$post->buyer_quote_id)
                    ->select('spi.id','spi.units as unit')->get();
                    
                }
                if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                $tracking    = DB::table('airint_seller_post_items as spi')
                ->join('airint_seller_posts as sp','sp.id','=','spi.seller_post_id')->where('spi.id', $order->seller_post_item_id)
                ->select('sp.tracking')->first();
            $tracking=$tracking->tracking;
            }else{
                $tracking=1;
            }
                    return view('ptl.sellers.seller_consignment_pickup', array(
                        'order' => $order,
                        'post' => $post,
                        'vehicles' => $vehicles,
                        'pickExist' => $order->seller_pickup_lr_number,
                        'deliveryExist' => $order->seller_delivery_driver_name,
                        'trackingExist' => $order->tracking_confirm,
                        'vehicleExist' => $order->vehicle_confirm,
                        'invoiceExist' => $invoice->invoice_no,
                        'receiptExist' => $receipt->receipt_no,
                        'locations' => $locations,
                        'invoice' => $invoice,
                        'receipt' => $receipt,
                        'payment_mode' => $payment->payment_mode,
                        'post_items' => $post_items,
                                    'tracking'=>$tracking
                    ));
                break;
            case OCEAN     :
                if(isset($order->buyer_quote_id)&& $order->buyer_quote_id!=0 && $order->buyer_quote_id!="") { 
                    $post = \DB::table('ocean_buyer_quotes as bq')
                            ->leftjoin('ocean_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_seaports as c1', 'bq.from_location_id', '=', 'c1.id')
                            ->leftjoin('lkp_seaports as c2', 'bq.to_location_id', '=', 'c2.id')
                            ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
                            ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                            ->leftjoin('lkp_air_ocean_shipment_types as st', 'st.id', '=', 'bq.lkp_air_ocean_shipment_type_id')
                            ->leftjoin('lkp_air_ocean_sender_identities as si', 'si.id', '=', 'bq.lkp_air_ocean_sender_identity_id')
                            ->where('bq.id', $order->buyer_quote_id)
                            ->select('bq.from_location_id','bq.to_location_id','bq.product_made','bq.ie_code','si.sender_identity','st.shipment_type','bq.is_dispatch_flexible as dispatch_flexible', 'bq.is_delivery_flexible as delivery_flexible','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery', 'bq.transaction_id as transid', 'c1.seaport_name as from', 'c2.seaport_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id')->first();
                    $post_items = \DB::table('ocean_buyer_quotes as bq')
                            ->leftjoin('ocean_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->where('bq.id',$post->buyer_quote_id)
                            ->select('bqi.number_packages','bqi.length','bqi.breadth','bqi.height','bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')->get();
                }
                elseif(isset($order->buyer_quote_item_id)&& $order->buyer_quote_item_id!=0 && $order->buyer_quote_item_id!="") { 
                    if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                    $post = \DB::table('ocean_buyer_quotes as bq')
                            ->leftjoin('ocean_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_seaports as c1', 'bq.from_location_id', '=', 'c1.id')
                            ->leftjoin('lkp_seaports as c2', 'bq.to_location_id', '=', 'c2.id')
                            ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
                            ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                            ->leftjoin('lkp_air_ocean_shipment_types as st', 'st.id', '=', 'bqi.lkp_air_ocean_shipment_type_id')
                            ->leftjoin('lkp_air_ocean_sender_identities as si', 'si.id', '=', 'bqi.lkp_air_ocean_sender_identity_id')
                            ->where('bqi.id', $order->buyer_quote_item_id)
                            ->select('bq.product_made','bq.ie_code','si.sender_identity','st.shipment_type','bq.is_dispatch_flexible as dispatch_flexible', 'bq.is_delivery_flexible as delivery_flexible','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery', 'bq.transaction_id as transid', 'c1.seaport_name as from', 'c2.seaport_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id','bqi.id as buyer_quote_item_id')->first();
                    $post_items = \DB::table('ocean_buyer_quotes as bq')
                            ->leftjoin('ocean_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->where('bqi.id',$post->buyer_quote_item_id)
                            ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')->get();
                    }else{
                        $post = \DB::table('term_buyer_quotes as bq')
                                ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                ->leftjoin('lkp_seaports as c1', 'bqi.from_location_id', '=', 'c1.id')
                                ->leftjoin('lkp_seaports as c2', 'bqi.to_location_id', '=', 'c2.id')
                                ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
                                ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                                ->leftjoin('lkp_air_ocean_shipment_types as st', 'st.id', '=', 'bqi.lkp_air_ocean_shipment_type_id')
                                ->leftjoin('lkp_air_ocean_sender_identities as si', 'si.id', '=', 'bqi.lkp_air_ocean_sender_identity_id')
                                ->where('bqi.id', $order->buyer_quote_item_id)
                            ->select('bqi.from_location_id','bqi.to_location_id','bqi.product_made','bqi.ie_code','si.sender_identity','st.shipment_type','bq.from_date as dispatch', 'bq.to_date as delivery','bq.is_door_pickup as door_pickup','bq.is_door_delivery as door_delivery', 'bq.transaction_id as transid', 'c1.seaport_name as from', 'c2.seaport_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id','bqi.id as buyer_quote_item_id')->first();
                        $post_items = \DB::table('term_buyer_quotes as bq')
                            ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                            ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                            ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                            ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')     
                            ->where('bqi.id',$post->buyer_quote_item_id)
                            ->select('ciq.noofpackages','bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                    }
                }
                else{
                    $post    = DB::table('ocean_seller_post_items as spi')
                    ->join('ocean_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->leftjoin('lkp_seaports as c1', 'spi.from_location_id', '=', 'c1.id')
                    ->leftjoin('lkp_seaports as c2', 'spi.to_location_id', '=', 'c2.id')
                    ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'sp.lkp_post_status_id')
                    ->leftjoin('users as u', 'u.id', '=', 'sp.seller_id')
                    ->where('spi.id', $order->seller_post_item_id)
                    ->select('sp.from_date as dispatch', 'sp.to_date as delivery', 'sp.transaction_id as transid', 'c1.seaport_name as from', 'c2.seaport_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','sp.id as buyer_quote_id')->first();
                    $post_items = \DB::table('ocean_seller_posts as sp')
                    ->leftjoin('ocean_seller_post_items as spi', 'sp.id', '=', 'spi.seller_post_id')
                    ->where('sp.id',$post->buyer_quote_id)
                    ->select('spi.id','spi.units as unit')->get();
                    
                }
                if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                $tracking    = DB::table('ocean_seller_post_items as spi')
                ->join('ocean_seller_posts as sp','sp.id','=','spi.seller_post_id')->where('spi.id', $order->seller_post_item_id)
                ->select('sp.tracking')->first();
                $tracking=$tracking->tracking;
            }else{
                $tracking=1;
            }
                    return view('ptl.sellers.seller_consignment_pickup', array(
                    'order' => $order,
                    'post' => $post,
                    'vehicles' => $vehicles,
                    'pickExist' => $order->seller_pickup_lr_number,
                    'deliveryExist' => $order->seller_delivery_driver_name,
                    'trackingExist' => $order->tracking_confirm,
                    'vehicleExist' => $order->vehicle_confirm,
                    'invoiceExist' => $invoice->invoice_no,
                    'receiptExist' => $receipt->receipt_no,
                    'locations' => $locations,
                    'invoice' => $invoice,
                    'receipt' => $receipt,
                    'payment_mode' => $payment->payment_mode,
                    'post_items' => $post_items,
                    'tracking'=>$tracking
                ));
                
                break;

        case COURIER     :
                if(isset($order->buyer_quote_id)&& $order->buyer_quote_id!=0 && $order->buyer_quote_id!="") { 
                    
                       $postQ = DB::table('courier_buyer_quotes as bq');
                        $postQ->leftjoin('courier_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id');
                        $postQ->leftjoin('lkp_ptl_pincodes as c1', 'bq.from_location_id', '=', 'c1.id');
                        $postQ->leftJoin('lkp_ptl_pincodes as c2', function($join)
                         {
                             $join->on('bq.to_location_id', '=', 'c2.id');
                             $join->on(DB::raw('bqi.lkp_courier_delivery_type_id'),'=',DB::raw(1));
                            
                         });
                         $postQ->leftJoin('lkp_countries as cc2', function($join)
                         {
                             $join->on('bq.to_location_id', '=', 'cc2.id');
                             $join->on(DB::raw('bqi.lkp_courier_delivery_type_id'),'=',DB::raw(2));
                            
                         });
                        $postQ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id');
                        $postQ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id');
                        $postQ->leftjoin('lkp_courier_types', 'lkp_courier_types.id', '=', 'bqi.lkp_courier_type_id');
                        $postQ->leftjoin('lkp_courier_delivery_types as pt', 'pt.id', '=', 'bqi.lkp_courier_delivery_type_id');
                        $postQ->where('bq.id', $order->buyer_quote_id);
                        $post = $postQ->select('bq.from_location_id','bq.to_location_id','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery', 'bq.transaction_id as transid', 'c1.postoffice_name as from',
                        DB::raw("(case when bqi.lkp_courier_delivery_type_id = 1 then c2.postoffice_name when bqi.lkp_courier_delivery_type_id = 2 then cc2.country_name end) as 'to'"),
                        'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id','pt.courier_delivery_type as courier_delivery_type', 'lkp_courier_types.courier_type as courier_type')->first();
                        $post_items = \DB::table('courier_buyer_quotes as bq')
                                ->leftjoin('courier_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                                ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                ->leftjoin('lkp_courier_types', 'lkp_courier_types.id', '=', 'bqi.lkp_courier_type_id')
                                ->leftjoin('lkp_courier_purposes', 'lkp_courier_purposes.id', '=', 'bqi.lkp_courier_purpose_id')
                                ->leftjoin('lkp_courier_delivery_types as pt', 'pt.id', '=', 'bqi.lkp_courier_delivery_type_id')
                                ->where('bq.id',$post->buyer_quote_id)
                                ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','bqi.number_packages','uom.weight_type', 'pt.courier_delivery_type as courier_delivery_type', 'lkp_courier_types.courier_type as courier_type', 'lkp_courier_purposes.courier_purpose as courier_purpose')->get();
                    
                }
                elseif(isset($order->buyer_quote_item_id)&& $order->buyer_quote_item_id!=0 && $order->buyer_quote_item_id!="") { 
                    if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                    $post = \DB::table('courier_buyer_quotes as bq')->leftjoin('courier_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')->leftjoin('lkp_ptl_pincodes as c1', 'bq.from_location_id', '=', 'c1.id')->leftjoin('lkp_ptl_pincodes as c2', 'bq.to_location_id', '=', 'c2.id')->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')->where('bqi.id', $order->buyer_quote_item_id)
                            ->select('bq.is_dispatch_flexible as dispatch_flexible', 'bq.is_delivery_flexible as delivery_flexible','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery','bq.is_door_pickup as door_pickup','bq.is_door_delivery as door_delivery', 'bq.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id','bqi.id as buyer_quote_item_id')->first();
                    $post_items = \DB::table('courier_buyer_quotes as bq')
                            ->leftjoin('courier_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                             ->leftjoin('lkp_courier_types', 'lkp_courier_types.id', '=', 'bqi.lkp_courier_type_id')
                                ->leftjoin('lkp_courier_delivery_types as pt', 'pt.id', '=', 'bqi.lkp_courier_delivery_type_id')
                            ->where('bqi.id',$post->buyer_quote_item_id)
                            ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.courier_delivery_type as courier_delivery_type', 'lkp_courier_types.courier_type as courier_type')->get();
                    }else{
                        $post = \DB::table('term_buyer_quotes as bq')
                                ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                ->leftjoin('lkp_ptl_pincodes as c1', 'bqi.from_location_id', '=', 'c1.id')                        
                             	->leftjoin('lkp_courier_types', 'lkp_courier_types.id', '=', 'bq.lkp_courier_type_id')
                                ->leftjoin('lkp_courier_delivery_types as pt', 'pt.id', '=', 'bq.lkp_courier_delivery_type_id')
                                ->leftjoin('lkp_ptl_pincodes as c2', 'bqi.to_location_id', '=', 'c2.id')
                                ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
                                ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                                ->where('bqi.id', $order->buyer_quote_item_id)
                            ->select('bqi.from_location_id','bqi.to_location_id','bq.from_date as dispatch', 'bq.to_date as delivery', 'lkp_courier_types.courier_type as courier_type',  'pt.courier_delivery_type as courier_delivery_type','bq.is_door_pickup as door_pickup','bq.is_door_delivery as door_delivery', 'bq.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','bq.id as buyer_quote_id','bqi.id as buyer_quote_item_id')->first();
                        $post_items = \DB::table('term_buyer_quotes as bq')
                            ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')                            
                             ->leftjoin('lkp_courier_types', 'lkp_courier_types.id', '=', 'bq.lkp_courier_type_id')
                                ->leftjoin('lkp_courier_delivery_types as pt', 'pt.id', '=', 'bq.lkp_courier_delivery_type_id')
                            ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                            ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')    
                            ->where('bqi.id',$post->buyer_quote_item_id)
                            ->select('ciq.noofpackages','bqi.volume as cft','bqi.id','bqi.units as unit',  'pt.courier_delivery_type as courier_delivery_type', 'lkp_courier_types.courier_type as courier_type','bqi.number_packages')->get();
                        
                    }
                }
                else{
                    $post    = DB::table('courier_seller_post_items as spi')
                    ->join('courier_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->leftjoin('lkp_ptl_pincodes as c1', 'spi.from_location_id', '=', 'c1.id')
                    ->leftjoin('lkp_ptl_pincodes as c2', 'spi.to_location_id', '=', 'c2.id')
                    ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'sp.lkp_post_status_id')
                    ->leftjoin('users as u', 'u.id', '=', 'sp.seller_id')
                    ->where('spi.id', $order->seller_post_item_id)
                    ->select('sp.from_date as dispatch', 'sp.to_date as delivery', 'sp.transaction_id as transid', 'c1.postoffice_name as from', 'c2.postoffice_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name','sp.id as buyer_quote_id')->first();
                    $post_items = \DB::table('ptl_seller_posts as sp')
                    ->leftjoin('ptl_seller_post_items as spi', 'sp.id', '=', 'spi.seller_post_id')                    
                    ->where('sp.id',$post->buyer_quote_id)
                    ->select('spi.id','spi.units as unit')->get();
                    
                }
                if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                $tracking    = DB::table('courier_seller_post_items as spi')
                ->join('courier_seller_posts as sp','sp.id','=','spi.seller_post_id')->where('spi.id', $order->seller_post_item_id)
                ->select('sp.tracking')->first();
            $tracking=$tracking->tracking;
            }else{
                $tracking=1;
            }
                    return view('ptl.sellers.seller_consignment_pickup', array(
            'order' => $order,
            'post' => $post,
            'vehicles' => $vehicles,
            'pickExist' => $order->seller_pickup_lr_number,
            'deliveryExist' => $order->seller_delivery_driver_name,
            'trackingExist' => $order->tracking_confirm,
            'vehicleExist' => $order->vehicle_confirm,
            'invoiceExist' => $invoice->invoice_no,
            'receiptExist' => $receipt->receipt_no,
            'locations' => $locations,
            'invoice' => $invoice,
            'receipt' => $receipt,
            'payment_mode' => $payment->payment_mode,
            'post_items' => $post_items,
                    'tracking'=>$tracking
        ));
                
                break;
            case RELOCATION_DOMESTIC :
            	
            if(isset($order->buyer_quote_id)&& $order->buyer_quote_id!=0 && $order->buyer_quote_id!=""){				
            	
                if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                    $post = \DB::table('relocation_buyer_posts as bq')
                        ->leftjoin('lkp_cities as c1', 'bq.from_location_id', '=', 'c1.id')
                        ->leftjoin('lkp_cities as c2', 'bq.to_location_id', '=', 'c2.id')
                        ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bq.lkp_post_status_id')
                        ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                        ->where('bq.id', $order->buyer_quote_id)
                        ->select('bq.from_location_id','bq.to_location_id','bq.id','bq.lkp_post_ratecard_type_id','bq.lkp_load_category_id','bq.lkp_vehicle_category_id','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery', 'bq.transaction_id as transid','c1.city_name as from', 'c2.city_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
                  }
            }elseif(isset($order->buyer_quote_item_id)&& $order->buyer_quote_item_id!=0 && $order->buyer_quote_item_id!=""){
            	
	            if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==2){
	            $post = \DB::table('term_buyer_quote_items as bqi')
	            ->leftjoin('term_buyer_quotes as bq', 'bq.id', '=', 'bqi.term_buyer_quote_id')
	            ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
	            ->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'bqi.lkp_vehicle_type_id')
	            ->leftjoin('lkp_cities as c1', 'bqi.from_location_id', '=', 'c1.id')
	            ->leftjoin('lkp_cities as c2', 'bqi.to_location_id', '=', 'c2.id')
	            ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
	            ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
	            ->where('bqi.id', $order->buyer_quote_item_id)
	            ->select('bqi.from_location_id','bqi.to_location_id','bq.from_date as dispatch', 'bq.to_date as delivery', 'bq.transaction_id as transid','bq.lkp_post_ratecard_type as lkp_post_ratecard_type_id', 'lkp_vehicle_types.vehicle_type as vehicle', 'lkp_load_types.load_type as load', 'c1.city_name as from', 'c2.city_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
	            }
            }
            if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                $tracking    = DB::table('relocation_seller_posts as sp')
                                ->where('sp.id', $order->seller_post_item_id)
                                ->select('sp.tracking')->first();
                $tracking=$tracking->tracking;
            }else{
                $tracking=1;
            }
                return view('relocation.orders.seller_consignment_pickup', array(
                'order' => $order,
                'post' => $post,
                'vehicles' => $vehicles,
                'pickExist' => $order->seller_pickup_lr_number,
                'deliveryExist' => $order->seller_delivery_driver_name,
                'trackingExist' => $order->tracking_confirm,
                'vehicleExist' => $order->vehicle_confirm,
                'invoiceExist' => $invoice->invoice_no,
                'receiptExist' => $receipt->receipt_no,
                'locations' => $locations,
                'invoice' => $invoice,
                'receipt' => $receipt,
                'payment_mode' => $payment->payment_mode,
                'tracking'=>$tracking
                ));
            
            
                break;
            case RELOCATION_OFFICE_MOVE :
                
            if(isset($order->buyer_quote_id)&& $order->buyer_quote_id!=0 && $order->buyer_quote_id!=""){                
                
                if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                    $post = \DB::table('relocationoffice_buyer_posts as bq')
                        ->leftjoin('lkp_cities as c1', 'bq.from_location_id', '=', 'c1.id')
                        ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bq.lkp_post_status_id')
                        ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                        ->where('bq.id', $order->buyer_quote_id)
                        ->select('bq.from_location_id','bq.id','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery', 'bq.transaction_id as transid','c1.city_name as from', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
                  }
            }elseif(isset($order->buyer_quote_item_id)&& $order->buyer_quote_item_id!=0 && $order->buyer_quote_item_id!=""){
                
                if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==2){
                $post = \DB::table('term_buyer_quote_items as bqi')
                ->leftjoin('term_buyer_quotes as bq', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                ->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'bqi.lkp_vehicle_type_id')
                ->leftjoin('lkp_cities as c1', 'bqi.from_location_id', '=', 'c1.id')
                ->leftjoin('lkp_cities as c2', 'bqi.to_location_id', '=', 'c2.id')
                ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
                ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                ->where('bqi.id', $order->buyer_quote_item_id)
                ->select('bqi.from_location_id','bqi.to_location_id','bq.from_date as dispatch', 'bq.to_date as delivery', 'bq.transaction_id as transid','bq.lkp_post_ratecard_type as lkp_post_ratecard_type_id', 'lkp_vehicle_types.vehicle_type as vehicle', 'lkp_load_types.load_type as load', 'c1.city_name as from', 'c2.city_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
                }
            }
            if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                $tracking    = DB::table('relocationoffice_seller_posts as sp')
                                ->where('sp.id', $order->seller_post_item_id)
                                ->select('sp.tracking')->first();
                $tracking=$tracking->tracking;
            }else{
                $tracking=1;
            }
            return view('relocation.orders.seller_consignment_pickup', array(
                'order' => $order,
                'post' => $post,
                'vehicles' => $vehicles,
                'pickExist' => $order->seller_pickup_lr_number,
                'deliveryExist' => $order->seller_delivery_driver_name,
                'trackingExist' => $order->tracking_confirm,
                'vehicleExist' => $order->vehicle_confirm,
                'invoiceExist' => $invoice->invoice_no,
                'receiptExist' => $receipt->receipt_no,
                'locations' => $locations,
                'invoice' => $invoice,
                'receipt' => $receipt,
                'payment_mode' => $payment->payment_mode,
                'tracking'=>$tracking
                ));
            break;        
            case RELOCATION_PET_MOVE :
            				
                $post = \DB::table('relocationpet_buyer_posts as bq')
                        ->leftjoin('lkp_cities as c1', 'bq.from_location_id', '=', 'c1.id')
                        ->leftjoin('lkp_cities as c2', 'bq.to_location_id', '=', 'c2.id')
                        ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bq.lkp_post_status_id')
                        ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                        ->where('bq.id', $order->buyer_quote_id)
                        ->select('bq.from_location_id','bq.to_location_id','bq.id','bq.lkp_pet_type_id','bq.lkp_breed_type_id','bq.lkp_cage_type_id','bq.dispatch_date as dispatch', 'bq.delivery_date as delivery', 'bq.transaction_id as transid','c1.city_name as from', 'c2.city_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
            
            $tracking    = DB::table('relocationpet_seller_posts as sp')
                            ->where('sp.id', $order->seller_post_item_id)
                            ->select('sp.tracking')->first();
            $tracking=$tracking->tracking;
                return view('relocation.orders.seller_consignment_pickup', array(
                'order' => $order,
                'post' => $post,
                'vehicles' => $vehicles,
                'pickExist' => $order->seller_pickup_lr_number,
                'deliveryExist' => $order->seller_delivery_driver_name,
                'trackingExist' => $order->tracking_confirm,
                'vehicleExist' => $order->vehicle_confirm,
                'invoiceExist' => $invoice->invoice_no,
                'receiptExist' => $receipt->receipt_no,
                'locations' => $locations,
                'invoice' => $invoice,
                'receipt' => $receipt,
                'payment_mode' => $payment->payment_mode,
                'tracking'=>$tracking
                ));
            
                break;    
            case RELOCATION_INTERNATIONAL :
            	
                return SellerOrderComponent::relocationIntOrderDetails($request, $id, $order,$invoice,$receipt,$locations,$payment);
            
            
                break;
            case ROAD_TRUCK_HAUL :
            if(isset($order->buyer_quote_item_id)&& $order->buyer_quote_item_id!=0 && $order->buyer_quote_item_id!=""){    
               
                    $post = \DB::table('truckhaul_buyer_quote_items as bqi')
                        ->leftjoin('truckhaul_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                        ->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'bqi.lkp_vehicle_type_id')
                        ->leftjoin('lkp_cities as c1', 'bqi.from_city_id', '=', 'c1.id')
                        ->leftjoin('lkp_cities as c2', 'bqi.to_city_id', '=', 'c2.id')
                        ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
                        ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                        ->where('bqi.id', $order->buyer_quote_item_id)
                        ->select('bqi.number_loads','bqi.dispatch_date as dispatch', 'bq.transaction_id as transid', 'lkp_vehicle_types.vehicle_type as vehicle', 'lkp_load_types.load_type as load', 'c1.city_name as from', 'c2.city_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
                }else{
                    $post    = DB::table('truckhaul_seller_post_items as spi')
                        ->join('truckhaul_seller_posts as sp','sp.id','=','spi.seller_post_id')
                        ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'spi.lkp_load_type_id')
                        ->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'spi.lkp_vehicle_type_id')
                        ->leftjoin('lkp_cities as c1', 'spi.from_location_id', '=', 'c1.id')
                        ->leftjoin('lkp_cities as c2', 'spi.to_location_id', '=', 'c2.id')
                        ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'sp.lkp_post_status_id')
                        ->leftjoin('users as u', 'u.id', '=', 'sp.seller_id')
                        ->where('spi.id', $order->seller_post_item_id)
                        ->select('sp.from_date as dispatch', 'sp.to_date as delivery', 'sp.transaction_id as transid', 'lkp_vehicle_types.vehicle_type as vehicle', 'lkp_load_types.load_type as load', 'c1.city_name as from', 'c2.city_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
                } 
            
                $tracking    = DB::table('truckhaul_seller_post_items as spi')
                ->join('truckhaul_seller_posts as sp','sp.id','=','spi.seller_post_id')
                        ->where('spi.id', $order->seller_post_item_id)
                ->select('sp.tracking')->first();
                $tracking=$tracking->tracking;
            
                return view('truckhaul.orders.seller_consignment_pickup', array(
                'order' => $order,
                'post' => $post,
                'vehicles' => $vehicles,
                'deliveryExist' => $order->seller_delivery_driver_name,
                'vehicleExist' => $order->vehicle_confirm,
                'payment_mode' => $payment->payment_mode,
                'tracking'=>$tracking    
                ));
            
            
                break;
            case ROAD_TRUCK_LEASE :
            if(isset($order->buyer_quote_item_id)&& $order->buyer_quote_item_id!=0 && $order->buyer_quote_item_id!=""){    
               
                    $post = \DB::table('trucklease_buyer_quote_items as bqi')
                        ->leftjoin('trucklease_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')                        
                        ->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'bqi.lkp_vehicle_type_id')
                        ->leftjoin('lkp_trucklease_lease_terms as tlt', 'tlt.id', '=', 'bqi.lkp_trucklease_lease_term_id')    
                        ->leftjoin('lkp_cities as c1', 'bqi.from_city_id', '=', 'c1.id')                        
                        ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
                        ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                        ->where('bqi.id', $order->buyer_quote_item_id)
                        ->select('tlt.lease_term','bqi.from_date as dispatch', 'bq.transaction_id as transid', 'lkp_vehicle_types.vehicle_type as vehicle', 'c1.city_name as from',  'lkp_post_statuses.post_status as status', 'u.username as name')->first();
                }else{
                    $post    = DB::table('trucklease_seller_post_items as spi')
                        ->join('trucklease_seller_posts as sp','sp.id','=','spi.seller_post_id')                        
                        ->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'spi.lkp_vehicle_type_id')
                        ->leftjoin('lkp_cities as c1', 'spi.from_location_id', '=', 'c1.id')                        
                        ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'sp.lkp_post_status_id')
                        ->leftjoin('users as u', 'u.id', '=', 'sp.seller_id')
                        ->where('spi.id', $order->seller_post_item_id)
                        ->select('sp.from_date as dispatch', 'sp.to_date as delivery', 'sp.transaction_id as transid', 'lkp_vehicle_types.vehicle_type as vehicle', 'c1.city_name as from', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
                } 
            
                $tracking    = DB::table('trucklease_seller_post_items as spi')
                ->join('trucklease_seller_posts as sp','sp.id','=','spi.seller_post_id')
                        ->where('spi.id', $order->seller_post_item_id)
                ->select('sp.tracking')->first();
                $tracking=$tracking->tracking;
            
                return view('trucklease.orders.seller_consignment_pickup', array(
                'order' => $order,
                'post' => $post,
                'vehicles' => $vehicles,
                'deliveryExist' => $order->seller_delivery_driver_name,
                'vehicleExist' => $order->vehicle_confirm,
                'payment_mode' => $payment->payment_mode,
                'tracking'=>$tracking        
                ));           
            
                break;
                
            case RELOCATION_GLOBAL_MOBILITY :
            	
            if(isset($order->buyer_quote_id)&& $order->buyer_quote_id!=0 && $order->buyer_quote_id!=""){				
            	
                if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==1){
                    $post = \DB::table('relocationgm_buyer_posts as bq')
                        ->leftjoin('lkp_cities as c1', 'bq.location_id', '=', 'c1.id')
                        ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bq.lkp_post_status_id')
                        ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
                        ->where('bq.id', $order->buyer_quote_id)
                        ->select('bq.location_id','bq.id','bq.dispatch_date as dispatch', 'bq.transaction_id as transid','c1.city_name as from', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
                  }
            }elseif(isset($order->buyer_quote_item_id)&& $order->buyer_quote_item_id!=0 && $order->buyer_quote_item_id!=""){
            	
	           if(isset($order->lkp_order_type_id)&& $order->lkp_order_type_id==2){
	            $post = \DB::table('term_buyer_quote_items as bqi')
	            ->leftjoin('term_buyer_quotes as bq', 'bq.id', '=', 'bqi.term_buyer_quote_id')
	            ->leftjoin('lkp_cities as c1', 'bqi.from_location_id', '=', 'c1.id')
	            ->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'bqi.lkp_post_status_id')
	            ->leftjoin('users as u', 'u.id', '=', 'bq.buyer_id')
	            ->where('bqi.id', $order->buyer_quote_item_id)
	            ->select('bq.id','bqi.from_location_id as location_id','bq.from_date as dispatch', 'bq.to_date as delivery', 'bq.transaction_id as transid','bq.lkp_post_ratecard_type as lkp_post_ratecard_type_id', 'c1.city_name as from', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
	            } 
            }
           
                return view('relocation.orders.seller_consignment_pickup', array(
                'order' => $order,
                'post' => $post,
                'pickExist' => $order->seller_pickup_date,
                'deliveryExist' => $order->seller_delivery_date,
                'payment_mode' => $payment->payment_mode,
                ));
            
            
                break;    
                
            default:
                $post = \DB::table('buyer_quote_items')->leftjoin('buyer_quotes', 'buyer_quotes.id', '=', 'buyer_quote_items.buyer_quote_id')->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'buyer_quote_items.lkp_load_type_id')->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'buyer_quote_items.lkp_vehicle_type_id')->leftjoin('lkp_cities as c1', 'buyer_quote_items.from_city_id', '=', 'c1.id')->leftjoin('lkp_cities as c2', 'buyer_quote_items.to_city_id', '=', 'c2.id')->leftjoin('lkp_post_statuses', 'lkp_post_statuses.id', '=', 'buyer_quote_items.lkp_post_status_id')->leftjoin('users as u', 'u.id', '=', 'buyer_quotes.buyer_id')->where('buyer_quote_items.id', $order->buyer_quote_item_id)->select('buyer_quote_items.dispatch_date as dispatch', 'buyer_quote_items.delivery_date as delivery', 'buyer_quotes.transaction_id as transid', 'lkp_vehicle_types.vehicle_type as vehicle', 'lkp_load_types.load_type as load', 'c1.city_name as from', 'c2.city_name as to', 'lkp_post_statuses.post_status as status', 'u.username as name')->first();
                break;
        }
        }else{
            return redirect('home');
        }
        
    }

    /**
     * adding vehicles to consignmentPickup.
     *
     * @return void
     */
    public function addVehicle() {
        Log::info('Pickup Vehicle add is initiated by user: ' . Auth::id(), array(
            'c' => '1'
        ));        
        CommonComponent::activityLog("ADD_VEHICLE", ADD_VEHICLE, 0, HTTP_REFERRER, CURRENT_URL);       
        if(!empty($_POST ["add_truck_flag"]) && $_POST ["add_truck_flag"] == "1"){
            Session::put('redirect_truckhaul_service','1');
        }else{
            Session::put('redirect_truckhaul_service','0');
        }
        if (!empty($_POST ["vehicle"])) {
            $vehicles   =\DB::table('pickup_vehicle_details')->where('vehicle_no',$_POST ["vehicle"])
                    ->where('order_id',$_POST ["order_id"])->select('id')->first();
            if(empty($vehicles) || $vehicles->id==""){
            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER ['REMOTE_ADDR'];
            $vehicle = new PickupVehicleDetail ();
            $vehicle->created_at = $created_at;
            $vehicle->created_by = Auth::id();
            $vehicle->created_ip = $createdIp;
            $vehicle->vehicle_no = $_POST ["vehicle"];
            $vehicle->order_id = $_POST ["order_id"];
            $vehicle->driver_name = $_POST ["driver"];
            $vehicle->mobile = $_POST ["mobile"];
            if(Session::get('service_id')==ROAD_TRUCK_LEASE){
                $vehicle->engine_number = $_POST ["engine"];
                $vehicle->chassis_number = $_POST ["chasis"];
                $vehicle->present_km_reading = $_POST ["present_reading"];
                $vehicle->vehicle_insurance_number = $_POST ["vehicle_insurance"];
                $vehicle->insurance_valid_to = CommonComponent::convertDateForDatabase($_POST ["insurance_date"]);                
            }
            $vehicle->save();

            //*******Send Sms to buyer about vehicle***********************//
            $orderDetails =  DB::table('orders')
                                ->where(['id' => $_POST ["order_id"]])
                                ->select('buyer_id','order_no')
                                ->first();
            $msg_params = array(
                'vehicleno' => $_POST ["vehicle"],
                'sellername' => Auth::User()->username,
                'ordernumber' => $orderDetails->order_no,
                'drivername' => $_POST ["driver"],
                'mobile' => $_POST ["mobile"],
                'datetime' => CommonComponent::convertDateDisplay(date('Y-m-d H:i:s'))
            );
            $getMobileNumber  =   CommonComponent::getMobleNumber($orderDetails->buyer_id);
            CommonComponent::sendSMS($getMobileNumber,TRUCK_PLACEMENT,$msg_params);
            //*******Send Sms to buyer about vehicle***********************//


            CommonComponent::auditLog($vehicle->id, 'pickup_vehicle_details');

            if(Session::get('redirect_truckhaul_service') == '1'){
            Session::put('session_delivery_date',$_POST['truckhaul_valid_to']);
            Session::put('session_dispatch_date',$_POST['truckhaul_valid_from']);
            Session::put('session_vehicle_type',$_POST['truckhaul_vehicle_type_id']);
            Session::put('session_load_type',$_POST['truckhaul_load_type_id']);
            Session::put('session_from_city_id',$_POST['truckhaul_from_location_id']);
            Session::put('session_to_city_id',$_POST['truckhaul_to_location_id']);
            Session::put('session_from_location',commonComponent::getCityName($_POST['truckhaul_from_location_id']));
            Session::put('session_to_location',commonComponent::getCityName($_POST['truckhaul_to_location_id']));
            Session::put('session_seller_district_id',$_POST['truckhaul_district_id']);
            Session::put('session_ftlprice',$_POST['truckhaul_price']);
            Session::put('session_tdays',$_POST['truckhaul_transit_days']); 
            Session::put('session_ftlprice',$_POST['truckhaul_price']);
            Session::put('session_ftlvehicle_no',$_POST['vehicle']); 
            Session::put('session_truckhaul_order_no',$_POST['truckhaul_order_no']);
            Session::put('session_add_truck_flag',$_POST['add_truck_flag']);    
            
            Session::put('service_id',ROAD_TRUCK_HAUL);
            }
            echo "1";
            }else{
                echo "0";
            }
        }
    }

    /**
     * adding Tracking Locations to consignmentPickup.
     *
     * @return void
     */
    public function addLocation() {
        Log::info('Tracking Location add is initiated by user: ' . Auth::id(), array(
            'c' => '1'
        ));
        CommonComponent::activityLog("ADD_LOCATION", ADD_LOCATION, 0, HTTP_REFERRER, CURRENT_URL);
        if (!empty($_POST ["location"])) {

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
    }

    

    public function buyerOrders() {
        Log::info('Buyer has viewed Order List page:' . $this->user_pk, array(
            'c' => '1'
        ));
        $services = CommonComponent::getServices();
        $post_status = CommonComponent::getOrderStatuses();
        $order_types = CommonComponent::getOrderTypes();
        $order_type = 1;
        $service_id = '';
        $order_status = '';       
        
        if(Session::get ( 'service_id' ) != ''){
			$serviceId = Session::get ( 'service_id' );
		}else{
            return redirect('home')
                            ->with('message', 'Order created successfully.');
        }
        if(isset($_REQUEST['page'])){        	
        	$order_type = Session::get('order_type');        
        }
	      
        if (!empty(Input::all())) {
            $data = Input::all();
            if (isset($_REQUEST['lkp_order_type_id']) && $_REQUEST['lkp_order_type_id'] != '') {
            	
                $order_type = $_REQUEST['lkp_order_type_id'];
                Session::put('order_type', $_REQUEST['lkp_order_type_id']);
            }
            if (isset($_REQUEST['service_id']) && $_REQUEST['service_id'] != '') {
                $service_id = $_REQUEST['service_id'];               
            }
            if (isset($_REQUEST['status_id']) ) {
                $order_status = $_REQUEST['status_id'];
                Session::put('order_status_id', $_REQUEST['status_id']);
            }            
            if(Session::get ( 'service_id' ) == COURIER){
            	if(isset($_REQUEST['delivery_type']) && $_REQUEST['delivery_type'] != ''){
            		$delivery_type = Session::get('delivery_type');
            		Session::put('delivery_type', $_REQUEST['delivery_type']);
            	}
            }
            
        } else {
            $_REQUEST = array();
            $data = array();
            if(Session::get ( 'service_id' ) == COURIER){
            	Session::put('delivery_type', 1);
            	$_REQUEST['delivery_type'] = Session::get('delivery_type');
            	$delivery_type = Session::get('delivery_type');
            }
        }
             
        if(!empty($_REQUEST)){
        	if(isset($_REQUEST['page'])){//echo "here";
        		$order_type = Session::get('order_type');
                        $_REQUEST['status_id'] = Session::get('order_status_id');
                        $data['status_id'] = Session::get('order_status_id');
                        $order_status =Session::get('order_status_id');
        	if(Session::get ( 'service_id' ) == COURIER){
					$delivery_type = Session::get('delivery_type');
				}
        	}else{
        	if(Session::get ( 'service_id' ) == COURIER){
					$delivery_type = Session::get('delivery_type');
				}
        	}
        }   
       

    //Check the condition for spot or term orders routing
    if($order_type==CONTRACTS){
        $result = TermBuyerComponent::getTermBuyerContractList($order_type, $_REQUEST, $data);
        $grid = $result['grid'];
        $filter = $result['filter'];        
        return view('ftl.orders.buyer_orders', [
            'grid' => $grid,
            'filter' => $filter,
            'services' => $services,
            'status' => $post_status,
            'order_types' => $order_types,
            'order_type' => $order_type,
            'service_id' => $service_id,
            'order_status' => $order_status
        ]);
    } else {
        //Loading respective service data grid
        switch($serviceId){
            case ROAD_FTL   : 
                $result = BuyerOrdersComponent::getFtlBuyerOrdersList();
                CommonComponent::activityLog("BUYER_VIEWED_ORDERS",BUYER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                break;

            case ROAD_TRUCK_HAUL:
            case ROAD_TRUCK_LEASE:   
                $result = BuyerOrdersComponent::getTruckHaulLeaseBuyerOrdersList();
                CommonComponent::activityLog("BUYER_VIEWED_ORDERS",BUYER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                break;   

            case ROAD_PTL   : 
            case RAIL       :
            case AIR_DOMESTIC:
            case AIR_INTERNATIONAL:  
            case OCEAN: 
                $result = BuyerOrdersComponent::getLtlBuyerOrdersList();
                CommonComponent::activityLog("BUYER_VIEWED_ORDERS",BUYER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                break;  

            case COURIER:
                $result = BuyerOrdersComponent::getCourierBuyerOrdersList();                
                CommonComponent::activityLog("BUYER_VIEWED_ORDERS",BUYER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);            
                $grid = $result['grid'];
                $filter = $result['filter'];
                return view('ftl.orders.buyer_orders', [
                    'grid' => $grid, 
                    'filter' => $filter,
                    'services' => $services,
                    'status' => $post_status,
                    'order_types' => $order_types,
                    'order_type' => $order_type,
                    'service_id' => $service_id,
                    'domestic_or_international_selected'=>$delivery_type,
                    'order_status' => $order_status
                ]);
                break;   

            case RELOCATION_DOMESTIC:
            case RELOCATION_PET_MOVE :  
                $result = BuyerOrdersComponent::getRelocDomPetBuyerOrdersList();
                CommonComponent::activityLog("BUYER_VIEWED_ORDERS",BUYER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                break;      

            case RELOCATION_INTERNATIONAL:
                $result = BuyerOrdersComponent::getRelocIntBuyerOrdersList();
                CommonComponent::activityLog("BUYER_VIEWED_ORDERS",BUYER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                break;  

            case RELOCATION_GLOBAL_MOBILITY :  
            case RELOCATION_OFFICE_MOVE:
                $result = BuyerOrdersComponent::getRelocGlobOfficeBuyerOrdersList();
                CommonComponent::activityLog("BUYER_VIEWED_ORDERS",BUYER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                break;

            case ROAD_INTRACITY : 
                $result = IntracityBuyerOrderComponent::getBuyerOrdersList($order_type, $_REQUEST,$data);
                CommonComponent::activityLog("INTRA_BUYER_VIEWED_ORDERS",INTRA_BUYER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                $grid = $result['grid'];
                $filter = $result['filter'];
                return view('intracity.orders.buyer_orders', [
                    'grid' => $grid,
                    'filter' => $filter,
                    'services' => $services,
                    'status' => $post_status,
                    'order_types' => $order_types,
                    'order_type' => $order_type,
                    'service_id' => $service_id,
                    'order_status' => $order_status
                ]);                       
                break;

            default             : 
                $result = FtlBuyerOrderComponent::getBuyerOrdersList($order_type, $_REQUEST,$data);
                CommonComponent::activityLog("BUYER_VIEWED_ORDERS",BUYER_VIEWED_ORDERS,0,HTTP_REFERRER,CURRENT_URL);
                break;
        }
        
        $grid = $result['grid'];
        $filter = $result['filter'];        
        return view('ftl.orders.buyer_orders', [
            'grid' => $grid,
            'filter' => $filter,
            'services' => $services,
            'status' => $post_status,
            'order_types' => $order_types,
            'order_type' => $order_type,
            'service_id' => $service_id,
            'order_status' => $order_status
        ]);

    }
        
}

    public function buyerOrderShowDetails($id) {
        Log::info('Buyer has viewed Order Details page:' . $this->user_pk, array(
            'c' => '1'
        ));
        if(Session::get ( 'service_id' ) != ''){
			$serviceId = Session::get ( 'service_id' );
		}
        if (isset($id) && ($id > 0)) {

            $orderId = $id;

            $qry        =   DB::table('pickup_vehicle_details as pvd');
            $vehicles   =   $qry->leftjoin('vehicle_details as veh','veh.vehicle_number','=','pvd.vehicle_no')
                                ->where('pvd.order_id', '=', $orderId)->select('pvd.*','veh.volty_register')
                                ->groupby('veh.vehicle_number')
                                ->get();
            
            switch($serviceId){
                case ROAD_FTL  		: $result = FtlBuyerOrderComponent::getBuyerOrderDetails($serviceId,$orderId,$this->user_pk);                    
                    $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date ) ); 
                    CommonComponent::activityLog("FTL_BUYER_ORDER_DETAIL",FTL_BUYER_ORDER_DETAIL,0,HTTP_REFERRER,CURRENT_URL);
                    $orderDetails =$result['orderDetails'];                                 
                    $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                    $priceDetails =$result['priceDetails'];
                    if(isset($orderDetails->seller_post_item_id) && $orderDetails->seller_post_item_id!=0){
                    $seller_post_payment    = DB::table('seller_post_items')
                   ->join('seller_posts','seller_posts.id','=','seller_post_items.seller_post_id')
                    ->where('seller_post_items.id',$orderDetails->seller_post_item_id)
                    ->select('seller_posts.lkp_payment_mode_id','seller_posts.tracking')
                    ->first();                   
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                   ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                   ->select('id','payment_mode')
                   ->first();
                    
                        return view('ftl.orders.buyer_order_details', array(
                            'orderDetails' => $orderDetails,
                            'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode,
                            'priceDetails' => $priceDetails,
                            'cancel_book_date' => $cancel_book_date,
                            'allMessagesList' => $allMessagesList,
                            'tracking' => $seller_post_payment->tracking,
                            'vehicles' => $vehicles
                        ));
                    }else{
                        
                        return view('ftl.orders.buyer_order_details', array(
                            'orderDetails' => $orderDetails,
                            'priceDetails' => $priceDetails,
                            'cancel_book_date' => $cancel_book_date,
                            'allMessagesList' => $allMessagesList,
                            
                            'vehicles' => $vehicles
                        ));
                    }
                break;
                case ROAD_PTL       : $result = FtlBuyerOrderComponent::getBuyerOrderDetails($serviceId,$orderId,$this->user_pk);
                $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date."-1 days" ) ); 
                    CommonComponent::activityLog("PTL_BUYER_ORDER_DETAIL",
			PTL_BUYER_ORDER_DETAIL,0,
			HTTP_REFERRER,CURRENT_URL);
            $orderDetails =$result['orderDetails'];            
                    $buyer_order_details = DB::table('orders')
                    ->where('id',$orderDetails->orderid)
                    ->select('buyer_quote_id')
                    ->first();
                    //for packaging type details
                        if(isset($orderDetails->buyer_quote_id)&& $orderDetails->buyer_quote_id!=0 && $orderDetails->buyer_quote_id!="") { 
                       $post_items = \DB::table('ptl_buyer_quotes as bq')
                                ->leftjoin('ptl_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                                ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                ->where('bq.id',$orderDetails->buyer_quote_id)
                                ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')
                                ->get();
                        }
                        elseif(isset($orderDetails->buyer_quote_item_id)&& $orderDetails->buyer_quote_item_id!=0 && $orderDetails->buyer_quote_item_id!="") { 
                            if(isset($orderDetails->lkp_order_type_id)&& $orderDetails->lkp_order_type_id==2){
                                $post_items = \DB::table('term_buyer_quotes as bq')
                                    ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                    //->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                    ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                    ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                    ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                                    ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                                    ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')    
                                    ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                                    ->select('bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                            }
                        }//end packaging type details
                    
            		$orderDetails_buyer_pickups = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($buyer_order_details->buyer_quote_id);
            		
                    $priceDetails =$result['priceDetails'];
                    if(isset($orderDetails->seller_post_item_id) && $orderDetails->seller_post_item_id!=0){
            		$seller_post_payment    = DB::table('ptl_seller_post_items')
                   ->join('ptl_seller_posts','ptl_seller_posts.id','=','ptl_seller_post_items.seller_post_id')
                    ->where('ptl_seller_post_items.id',$orderDetails->seller_post_item_id)
                    ->select('ptl_seller_posts.lkp_payment_mode_id','ptl_seller_posts.tracking')
            		->first();
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();
                        return view('ptl.orders.buyer_order_details', array(
                                'orderDetails' => $orderDetails,
                        	'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                        	'tracking_order' => $seller_post_payment->tracking,
                        	'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode,
                                'priceDetails' => $priceDetails,
                                'cancel_book_date' => $cancel_book_date,
                                'vehicles' => $vehicles,
                                'post_items'=>$post_items
                        ));
                    }else{
                        return view('ptl.orders.buyer_order_details', array(
                                'orderDetails' => $orderDetails,
                        	'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                        	'priceDetails' => $priceDetails,
                                'cancel_book_date' => $cancel_book_date,
                                'vehicles' => $vehicles,
                                'post_items'=>$post_items
                        ));
                    }
                    
                        
                break;
                case RAIL       : $result = FtlBuyerOrderComponent::getBuyerOrderDetails($serviceId,$orderId,$this->user_pk);
                $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date."-1 days" ) ); 
                    CommonComponent::activityLog("RAIL_BUYER_ORDER_DETAIL",
			RAIL_BUYER_ORDER_DETAIL,0,
			HTTP_REFERRER,CURRENT_URL);
                        $orderDetails =$result['orderDetails'];
                        $buyer_order_details = DB::table('orders')
                    ->where('id',$orderDetails->orderid)
                    ->select('buyer_quote_id')
                    ->first();
                        //for packaging type details
                        if(isset($orderDetails->buyer_quote_id)&& $orderDetails->buyer_quote_id!=0 && $orderDetails->buyer_quote_id!="") { 
                       $post_items = \DB::table('rail_buyer_quotes as bq')
                                ->leftjoin('rail_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                                ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                ->where('bq.id',$orderDetails->buyer_quote_id)
                                ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')
                                ->get();
                        }
                        elseif(isset($orderDetails->buyer_quote_item_id)&& $orderDetails->buyer_quote_item_id!=0 && $orderDetails->buyer_quote_item_id!="") { 
                            if(isset($orderDetails->lkp_order_type_id)&& $orderDetails->lkp_order_type_id==2){
                                $post_items = \DB::table('term_buyer_quotes as bq')
                                    ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                    //->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                    ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                    ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                    ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                                    ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                                    ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')    
                                    ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                                    ->select('bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                            }
                        }//end packaging type details
                        
            		$orderDetails_buyer_pickups = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($buyer_order_details->buyer_quote_id);
            		
                        $priceDetails =$result['priceDetails'];
                        if(isset($orderDetails->seller_post_item_id) && $orderDetails->seller_post_item_id!=0){
            		$seller_post_payment    = DB::table('rail_seller_post_items as spi')
                   ->join('rail_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->where('spi.id',$orderDetails->seller_post_item_id)
                    ->select('sp.lkp_payment_mode_id','sp.tracking')
            		->first();
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();
                    
                        return view('ptl.orders.buyer_order_details', array(
                                'orderDetails' => $orderDetails,
                        	'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                        	'tracking_order' => $seller_post_payment->tracking,
                        	'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode,
                                'priceDetails' => $priceDetails,
                                'cancel_book_date' => $cancel_book_date,
                                'vehicles' => $vehicles,
                                'post_items'=>$post_items
                        ));
                        }else{
                            return view('ptl.orders.buyer_order_details', array(
                                'orderDetails' => $orderDetails,
                        	'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                        	'priceDetails' => $priceDetails,
                                'cancel_book_date' => $cancel_book_date,
                                'vehicles' => $vehicles,
                                'post_items'=>$post_items
                        ));
                        }
                break;
            case AIR_DOMESTIC       : $result = FtlBuyerOrderComponent::getBuyerOrderDetails($serviceId,$orderId,$this->user_pk);
                        $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date."-1 days" ) ); 
                        CommonComponent::activityLog("AIRDOMESTIC_BUYER_ORDER_DETAIL",AIRDOMESTIC_BUYER_ORDER_DETAIL,0,HTTP_REFERRER,CURRENT_URL);
                        $orderDetails =$result['orderDetails'];
            
                        $buyer_order_details = DB::table('orders')
                    ->where('id',$orderDetails->orderid)
                    ->select('buyer_quote_id')
                    ->first();
                        //for packaging type details
                        if(isset($orderDetails->buyer_quote_id)&& $orderDetails->buyer_quote_id!=0 && $orderDetails->buyer_quote_id!="") { 
                       $post_items = \DB::table('airdom_buyer_quotes as bq')
                                ->leftjoin('airdom_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                                ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                ->where('bq.id',$orderDetails->buyer_quote_id)
                                ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')
                                ->get();
                        }
                        elseif(isset($orderDetails->buyer_quote_item_id)&& $orderDetails->buyer_quote_item_id!=0 && $orderDetails->buyer_quote_item_id!="") { 
                            if(isset($orderDetails->lkp_order_type_id)&& $orderDetails->lkp_order_type_id==2){
                                $post_items = \DB::table('term_buyer_quotes as bq')
                                    ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                    //->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                    ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                    ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                    ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                                    ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                                    ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')    
                                    ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                                    ->select('bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                            }
                        }//end packaging type details
                        
            		$orderDetails_buyer_pickups = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($buyer_order_details->buyer_quote_id);
            		
            		$priceDetails =$result['priceDetails'];
                        if(isset($orderDetails->seller_post_item_id) && $orderDetails->seller_post_item_id!=0){
            		$seller_post_payment    = DB::table('airdom_seller_post_items as spi')
                   ->join('airdom_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->where('spi.id',$orderDetails->seller_post_item_id)
                    ->select('sp.lkp_payment_mode_id','sp.tracking')
            		->first();
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();
                        return view('ptl.orders.buyer_order_details', array(
                                'orderDetails' => $orderDetails,
                        	'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                        	'tracking_order' => $seller_post_payment->tracking,
                        	'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode,
                                'priceDetails' => $priceDetails,
                                'cancel_book_date' => $cancel_book_date,
                                'vehicles' => $vehicles,
                                'post_items'=>$post_items
                        ));
                        }else{
                            return view('ptl.orders.buyer_order_details', array(
                                'orderDetails' => $orderDetails,
                        	'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                        	'priceDetails' => $priceDetails,
                                'cancel_book_date' => $cancel_book_date,
                                'vehicles' => $vehicles,
                                'post_items'=>$post_items
                        ));
                        }
                break;
                
                case COURIER       : $result = FtlBuyerOrderComponent::getBuyerOrderDetails($serviceId,$orderId,$this->user_pk);
                $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date."-1 days" ) );
                CommonComponent::activityLog("COURIER_BUYER_ORDER_DETAIL",COURIER_BUYER_ORDER_DETAIL,0,HTTP_REFERRER,CURRENT_URL);
                $orderDetails =$result['orderDetails'];
                //for packaging type details
                        if(isset($orderDetails->buyer_quote_id)&& $orderDetails->buyer_quote_id!=0 && $orderDetails->buyer_quote_id!="") { 
                       $post_items = \DB::table('courier_buyer_quotes as bq')
                            ->leftjoin('courier_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                            ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                             ->leftjoin('lkp_courier_types', 'lkp_courier_types.id', '=', 'bqi.lkp_courier_type_id')
                                ->leftjoin('lkp_courier_delivery_types as pt', 'pt.id', '=', 'bqi.lkp_courier_delivery_type_id')
                            ->where('bqi.id',$orderDetails->buyer_quote_id)
                            ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.courier_delivery_type as courier_delivery_type', 'lkp_courier_types.courier_type as courier_type')
                               ->get();
                        }
                        elseif(isset($orderDetails->buyer_quote_item_id)&& $orderDetails->buyer_quote_item_id!=0 && $orderDetails->buyer_quote_item_id!="") { 
                            if(isset($orderDetails->lkp_order_type_id)&& $orderDetails->lkp_order_type_id==2){
                                $post_items = \DB::table('term_buyer_quotes as bq')
                            ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                            //->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                             ->leftjoin('lkp_courier_types', 'lkp_courier_types.id', '=', 'bq.lkp_courier_type_id')
                                ->leftjoin('lkp_courier_delivery_types as pt', 'pt.id', '=', 'bq.lkp_courier_delivery_type_id')
                            ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                            ->select('bqi.volume as cft','bqi.id','bqi.units as unit',  'pt.courier_delivery_type as courier_delivery_type', 'lkp_courier_types.courier_type as courier_type')->get();
                            }
                        }//end packaging type details
                        
                
                $buyer_order_details = DB::table('orders')->where('id',$orderDetails->orderid)
                ->select('buyer_quote_id')
                ->first();
                $orderDetails_buyer_pickups = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($buyer_order_details->buyer_quote_id);
                
                $priceDetails =$result['priceDetails'];
                if(isset($orderDetails->seller_post_item_id) && $orderDetails->seller_post_item_id!=0){
                	$seller_post_payment    = DB::table('courier_seller_post_items as spi')
                	->join('courier_seller_posts as sp','sp.id','=','spi.seller_post_id')
                	->where('spi.id',$orderDetails->seller_post_item_id)
                	->select('sp.lkp_payment_mode_id','sp.tracking')
                	->first();
                	$payment_buyer_details = DB::table('lkp_payment_modes')
                	->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                	->select('id','payment_mode')
                	->first();
                	return view('ptl.orders.buyer_order_details', array(
                			'orderDetails' => $orderDetails,
                			'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                			'tracking_order' => $seller_post_payment->tracking,
                			'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode,
                			'priceDetails' => $priceDetails,
                			'cancel_book_date' => $cancel_book_date,
                                        'vehicles' => $vehicles,
                                        'post_items'=>$post_items
                	));
                }else{
                	return view('ptl.orders.buyer_order_details', array(
                			'orderDetails' => $orderDetails,
                			'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                			'priceDetails' => $priceDetails,
                			'cancel_book_date' => $cancel_book_date,
                                        'vehicles' => $vehicles,
                                        'post_items'=>$post_items
                	));
                }
                break;
                
                case AIR_INTERNATIONAL       : $result = FtlBuyerOrderComponent::getBuyerOrderDetails($serviceId,$orderId,$this->user_pk);
                        $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date."-1 days" ) ); 
                        CommonComponent::activityLog("PTL_BUYER_ORDER_DETAIL",
			PTL_BUYER_ORDER_DETAIL,0,
			HTTP_REFERRER,CURRENT_URL);
                        $orderDetails =$result['orderDetails'];
                        //for packaging type details
                        if(isset($orderDetails->buyer_quote_id)&& $orderDetails->buyer_quote_id!=0 && $orderDetails->buyer_quote_id!="") { 
                       $post_items = \DB::table('airint_buyer_quotes as bq')
                                ->leftjoin('airint_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                                ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                ->where('bq.id',$orderDetails->buyer_quote_id)
                                ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')
                                ->get();
                        }
                        elseif(isset($orderDetails->buyer_quote_item_id)&& $orderDetails->buyer_quote_item_id!=0 && $orderDetails->buyer_quote_item_id!="") { 
                            if(isset($orderDetails->lkp_order_type_id)&& $orderDetails->lkp_order_type_id==2){
                                $post_items = \DB::table('term_buyer_quotes as bq')
                                    ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                    //->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                    ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                    ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                    ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                                    ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                                    ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')    
                                    ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                                    ->select('bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                            }
                        }//end packaging type details
                        
            
                        $buyer_order_details = DB::table('orders')
                    ->where('id',$orderDetails->orderid)
                    ->select('buyer_quote_id')
                    ->first();
            		$orderDetails_buyer_pickups = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($buyer_order_details->buyer_quote_id);
            		
                    $priceDetails =$result['priceDetails'];
                        if(isset($orderDetails->seller_post_item_id) && $orderDetails->seller_post_item_id!=0){
            		$seller_post_payment    = DB::table('airint_seller_post_items as spi')
                   ->join('airint_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->where('spi.id',$orderDetails->seller_post_item_id)
                    ->select('sp.lkp_payment_mode_id','sp.tracking')
            		->first();
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();
                        return view('ptl.orders.buyer_order_details', array(
                                'orderDetails' => $orderDetails,
                        	'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                        	'tracking_order' => $seller_post_payment->tracking,
                        	'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode,
                                'priceDetails' => $priceDetails,
                                'cancel_book_date' => $cancel_book_date,
                                'vehicles' => $vehicles,
                                'post_items'=>$post_items
                        ));
                        }else{
                            return view('ptl.orders.buyer_order_details', array(
                                'orderDetails' => $orderDetails,
                        	'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                        	'priceDetails' => $priceDetails,
                                'cancel_book_date' => $cancel_book_date,
                                'vehicles' => $vehicles,
                                'post_items'=>$post_items
                        ));
                        }
                break;
                case OCEAN       : $result = FtlBuyerOrderComponent::getBuyerOrderDetails($serviceId,$orderId,$this->user_pk);
                        $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date."-1 days" ) ); 
                        CommonComponent::activityLog("PTL_BUYER_ORDER_DETAIL",
			PTL_BUYER_ORDER_DETAIL,0,
			HTTP_REFERRER,CURRENT_URL);
                        $orderDetails =$result['orderDetails'];
                        //for packaging type details
                        if(isset($orderDetails->buyer_quote_id)&& $orderDetails->buyer_quote_id!=0 && $orderDetails->buyer_quote_id!="") { 
                       $post_items = \DB::table('ocean_buyer_quotes as bq')
                                ->leftjoin('ocean_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id')
                                ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                ->where('bq.id',$orderDetails->buyer_quote_id)
                                ->select('bqi.calculated_volume_weight as cft','bqi.id','bqi.units as unit','uom.weight_type', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load')
                                ->get();
                        }
                        elseif(isset($orderDetails->buyer_quote_item_id)&& $orderDetails->buyer_quote_item_id!=0 && $orderDetails->buyer_quote_item_id!="") { 
                            if(isset($orderDetails->lkp_order_type_id)&& $orderDetails->lkp_order_type_id==2){
                                $post_items = \DB::table('term_buyer_quotes as bq')
                                    ->leftjoin('term_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                                    //->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','bqi.lkp_ict_weight_uom_id')
                                    ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                                    ->leftjoin('lkp_packaging_types as pt', 'pt.id', '=', 'bqi.lkp_packaging_type_id')
                                    ->leftjoin('term_contracts as tc', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                                    ->leftjoin('term_contracts_indent_quantities as ciq', 'ciq.term_contract_id', '=', 'tc.id')
                                    ->leftjoin('lkp_ict_weight_uom as uom','uom.id','=','ciq.lkp_ict_weight_type_id')    
                                    ->where('bqi.id',$orderDetails->buyer_quote_item_id)
                                    ->select('bqi.volume as cft','bqi.id','bqi.units as unit', 'pt.packaging_type_name as packaging', 'lkp_load_types.load_type as load','ciq.volumetricweight as unit','uom.weight_type')->get();
                            }
                        }//end packaging type details                        
            
                        $buyer_order_details = DB::table('orders')
                    ->where('id',$orderDetails->orderid)
                    ->select('buyer_quote_id')
                    ->first();
            		$orderDetails_buyer_pickups = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($buyer_order_details->buyer_quote_id);
            		
                        $priceDetails =$result['priceDetails'];
                        if(isset($orderDetails->seller_post_item_id) && $orderDetails->seller_post_item_id!=0){
            		$seller_post_payment    = DB::table('ocean_seller_post_items as spi')
                   ->join('ocean_seller_posts as sp','sp.id','=','spi.seller_post_id')
                    ->where('spi.id',$orderDetails->seller_post_item_id)
                    ->select('sp.lkp_payment_mode_id','sp.tracking')
            		->first();
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();
                    
                    
                        return view('ptl.orders.buyer_order_details', array(
                                'orderDetails' => $orderDetails,
                        	'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                        	'tracking_order' => $seller_post_payment->tracking,
                        	'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode,
                                'priceDetails' => $priceDetails,
                                'cancel_book_date' => $cancel_book_date,
                                'vehicles' => $vehicles,
                                'post_items'=>$post_items
                        ));
                        }else{
                            return view('ptl.orders.buyer_order_details', array(
                                'orderDetails' => $orderDetails,
                        	'orderDetails_buyer_pickups_veiw' => $orderDetails_buyer_pickups,
                        	'priceDetails' => $priceDetails,
                                'cancel_book_date' => $cancel_book_date,
                                'vehicles' => $vehicles,
                                'post_items'=>$post_items
                        ));
                        }
                break;
                case ROAD_INTRACITY : $result = IntracityBuyerOrderComponent::getBuyerOrderDetails($serviceId,$orderId,$this->user_pk);
                    //$cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date."-2 hours" ) );
                    $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date ) );
                    CommonComponent::activityLog("INTRA_BUYER_ORDER_DETAIL",
			INTRA_BUYER_ORDER_DETAIL,0,
			HTTP_REFERRER,CURRENT_URL);
                    $orderDetails =$result['orderDetails'];  
                    $priceDetails =$result['priceDetails'];
                    
                        return view('intracity.orders.buyer_order_details', array(
                            'orderDetails' => $orderDetails,
                            'priceDetails' => $priceDetails,
                            'cancel_book_date' => $cancel_book_date,
                            'vehicles' => $vehicles
                        ));
                break;
                
                case RELOCATION_DOMESTIC : $result = RelocationBuyerComponent::getRelocationBuyerOrderDetails($serviceId,$orderId,$this->user_pk);                
                $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date ) );
                CommonComponent::activityLog("RELOCATION_BUYER_ORDER_DETAIL",
                RELOCATION_BUYER_ORDER_DETAIL,0,
                HTTP_REFERRER,CURRENT_URL);
                $orderDetails =$result['orderDetails'];                              
                $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);
                if(isset($orderDetails->seller_post_item_id) && $orderDetails->seller_post_item_id!=0){                
                $seller_post_payment    = DB::table('relocation_seller_posts as rsp')
                ->join('relocation_seller_post_items as rspi','rspi.seller_post_id','=','rsp.id')
                ->where('rsp.id',$orderDetails->seller_post_item_id)
                ->select('rsp.lkp_payment_mode_id','rsp.tracking')
                ->first();                
                $payment_buyer_details = DB::table('lkp_payment_modes')
                ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                ->select('id','payment_mode')
                ->first();                
                return view('ftl.orders.buyer_order_details', array(
                		'orderDetails' => $orderDetails,
                		'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode, 
                		'tracking_order' => $seller_post_payment->tracking,
                		'cancel_book_date' => $cancel_book_date,
                		'allMessagesList' => $allMessagesList,
                        'vehicles' => $vehicles
                	)); 
                }else{
                	
                	return view('ftl.orders.buyer_order_details', array(
                			'orderDetails' => $orderDetails,
                			'cancel_book_date' => $cancel_book_date,
                			'allMessagesList' => $allMessagesList,
                        	'vehicles' => $vehicles
                	));
                	
                }              
                break;
                
                case RELOCATION_OFFICE_MOVE : $result = RelocationOfficeBuyerComponent::getRelocationBuyerOrderDetails($serviceId,$orderId,$this->user_pk);

                $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date ) );
                CommonComponent::activityLog("RELOCATION_OFFICE_MOVE_BUYER_ORDER_DETAIL",
                RELOCATION_OFFICE_MOVE_BUYER_ORDER_DETAIL,0,
                HTTP_REFERRER,CURRENT_URL);
                $orderDetails =$result['orderDetails'];                              
                $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);

                if(isset($orderDetails->seller_post_item_id) && $orderDetails->seller_post_item_id!=0){                
                    $seller_post_payment    = DB::table('relocationoffice_seller_posts as rsp')
                    ->where('rsp.id',$orderDetails->seller_post_item_id)
                    ->select('rsp.lkp_payment_mode_id','rsp.tracking')
                    ->first(); 

                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();                
                    return view('ftl.orders.buyer_order_details', array(
                            'orderDetails' => $orderDetails,
                            'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode, 
                            'tracking_order' => $seller_post_payment->tracking,
                            'cancel_book_date' => $cancel_book_date,
                            'allMessagesList' => $allMessagesList,
                            'vehicles' => $vehicles
                        )); 
                }              
                break;

                case RELOCATION_PET_MOVE: $result =  RelocationPetBuyerComponent::getRelocationPetBuyerOrderDetails($serviceId,$orderId,$this->user_pk);                   
                    $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date ) );                     
                    CommonComponent::activityLog("RELOCATION_PET_MOVE_BUYER_ORDER_DETAIL",
                    RELOCATION_PET_MOVE_BUYER_ORDER_DETAIL,0,
                    HTTP_REFERRER,CURRENT_URL);
                    $orderDetails =$result['orderDetails'];					
                    $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);                   
                   // $priceDetails =$result['priceDetails'];
                    if(isset($orderDetails->seller_post_item_id) && $orderDetails->seller_post_item_id!=0){
                    $seller_post_payment = DB::table('relocationpet_seller_posts')
                    ->join('relocationpet_seller_post_items','relocationpet_seller_posts.id','=','relocationpet_seller_post_items.seller_post_id')
                    ->where('relocationpet_seller_posts.id',$orderDetails->seller_post_item_id)
                    ->select('relocationpet_seller_posts.lkp_payment_mode_id','relocationpet_seller_posts.tracking','relocationpet_seller_post_items.transitdays')
                    ->first();       
                    
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();
                    
                    return view('relocationpet.buyers.buyer_order_details', array(
                        'orderDetails' => $orderDetails,
                        'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode,
                       // 'priceDetails' => $priceDetails,
                        'cancel_book_date' => $cancel_book_date,
                        'allMessagesList' => $allMessagesList,
                        'tracking' => $seller_post_payment->tracking, 
                        'transitdays' =>  $seller_post_payment->transitdays,     
                        'vehicles' => $vehicles
                    ));
                    }
                break;
                
                case RELOCATION_INTERNATIONAL: $result =  RelocationintBuyerComponent::getRelocationintBuyerOrderDetails($serviceId,$orderId,$this->user_pk);                   
                    $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date ) );                     
                    CommonComponent::activityLog("RELOCATION_INT_BUYER_ORDER_DETAIL",
                    RELOCATION_INT_BUYER_ORDER_DETAIL,0,
                    HTTP_REFERRER,CURRENT_URL);
                    $orderDetails =$result['orderDetails'];					
                    $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);                   
                   // $priceDetails =$result['priceDetails'];
                    if(isset($orderDetails->seller_post_item_id) && $orderDetails->seller_post_item_id!=0){
                    $seller_post_payment = DB::table('relocationint_seller_posts')
                    ->leftjoin('relocationint_seller_post_items','relocationint_seller_posts.id','=','relocationint_seller_post_items.seller_post_id')
                    ->where('relocationint_seller_posts.id',$orderDetails->seller_post_item_id)
                    ->select('relocationint_seller_posts.lkp_payment_mode_id','relocationint_seller_posts.tracking','relocationint_seller_post_items.transitdays')
                    ->first();       
                    //echo "<pre>"; print_r($orderDetails); die;
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();
                    
                    return view('relocationint.buyers.buyer_order_details', array(
                        'orderDetails' => $orderDetails,
                        'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode,
                       // 'priceDetails' => $priceDetails,
                        'cancel_book_date' => $cancel_book_date,
                        'allMessagesList' => $allMessagesList,
                        'tracking' => $seller_post_payment->tracking, 
                        'transitdays' =>  $seller_post_payment->transitdays,     
                        'vehicles' => $vehicles
                    ));
                    } else{
                	
                	return view('relocationint.buyers.buyer_order_details', array(
                        'orderDetails' => $orderDetails,                       
                        'cancel_book_date' => $cancel_book_date,
                        'allMessagesList' => $allMessagesList,                                                 
                        'vehicles' => $vehicles
                	));
                	
                }      
                break;
                
                case RELOCATION_GLOBAL_MOBILITY: $result =  RelocationintBuyerComponent::getRelocationintBuyerOrderDetails($serviceId,$orderId,$this->user_pk);                   
                    $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date ) );                     
                    CommonComponent::activityLog("RELOCATION_GLOBAL_MOBILITY_BUYER_ORDER_DETAIL",
                    RELOCATION_GLOBAL_MOBILITY_BUYER_ORDER_DETAIL,0,
                    HTTP_REFERRER,CURRENT_URL);
                    $orderDetails =$result['orderDetails'];					
                    $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);                   
                   // $priceDetails =$result['priceDetails'];
                    if(isset($orderDetails->seller_post_item_id) && $orderDetails->seller_post_item_id!=0){
                    $seller_post_payment = DB::table('relocationgm_seller_posts')                    
                    ->where('relocationgm_seller_posts.id',$orderDetails->seller_post_item_id)
                    ->select('relocationgm_seller_posts.lkp_payment_mode_id')
                    ->first();       
                    //echo "<pre>"; print_r($orderDetails); die;
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();
                    
                    return view('relocationint.buyers.buyer_order_details', array(
                        'orderDetails' => $orderDetails,
                        'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode,                       
                        'cancel_book_date' => $cancel_book_date,
                        'allMessagesList' => $allMessagesList, 
                        'vehicles' => $vehicles
                    ));
                    } else{
                	
                	return view('relocationint.buyers.buyer_order_details', array(
                        'orderDetails' => $orderDetails,                       
                        'cancel_book_date' => $cancel_book_date,
                        'allMessagesList' => $allMessagesList,                                                 
                        'vehicles' => $vehicles
                	));
                	
                }      
                break;
                
                case ROAD_TRUCK_HAUL: $result =  FtlBuyerOrderComponent::getBuyerOrderDetails($serviceId,$orderId,$this->user_pk);                   
                    $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date ) );                     
                    $orderDetails =$result['orderDetails'];					
                    $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);                   
                    $priceDetails =$result['priceDetails'];
                    if(isset($orderDetails->seller_post_item_id) && $orderDetails->seller_post_item_id!=0){
                    $seller_post_payment = DB::table('truckhaul_seller_post_items')
                    ->join('truckhaul_seller_posts','truckhaul_seller_posts.id','=','truckhaul_seller_post_items.seller_post_id')
                    ->where('truckhaul_seller_post_items.id',$orderDetails->seller_post_item_id)
                    ->select('truckhaul_seller_posts.lkp_payment_mode_id','truckhaul_seller_posts.tracking','truckhaul_seller_post_items.units','truckhaul_seller_post_items.transitdays','truckhaul_seller_post_items.vehicle_number')
                    ->first();       
                    
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();
                    
                    return view('truckhaul.buyers.buyer_order_details', array(
                        'orderDetails' => $orderDetails,
                        'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode,
                        'priceDetails' => $priceDetails,
                        'cancel_book_date' => $cancel_book_date,
                        'allMessagesList' => $allMessagesList,
                        'tracking' => $seller_post_payment->tracking,
                        'vehicles' => $vehicles,
                        'units' =>  $seller_post_payment->units,
                        'transitdays' =>  $seller_post_payment->transitdays,
                        'vehicleNumber' =>  $seller_post_payment->vehicle_number,
                    ));
                    }
                break;
                case ROAD_TRUCK_LEASE: $result =  FtlBuyerOrderComponent::getBuyerOrderDetails($serviceId,$orderId,$this->user_pk);                                       
                    $cancel_book_date   = date ( 'Y-m-d H:i:s', strtotime ( $result['orderDetails']->buyer_consignment_pick_up_date ) );                    
                    $orderDetails =$result['orderDetails'];                   
                    $allMessagesList = MessagesComponent::listMessages(null,ORDERMESSAGETYPE,null);                   
                    $priceDetails =$result['priceDetails'];                    
                    
                    $seller_post_payment = DB::table('trucklease_seller_post_items')
                    ->join('trucklease_seller_posts','trucklease_seller_posts.id','=','trucklease_seller_post_items.seller_post_id')
                    ->where('trucklease_seller_post_items.id',$orderDetails->seller_post_item_id)
                    ->select('trucklease_seller_posts.lkp_payment_mode_id','trucklease_seller_posts.tracking','trucklease_seller_post_items.lkp_trucklease_lease_term_id',
                            'trucklease_seller_post_items.minimum_lease_period','trucklease_seller_post_items.vehicle_make_model_year','trucklease_seller_post_items.fuel_included','trucklease_seller_post_items.driver_availability')
                    ->first(); 
                    $payment_buyer_details = DB::table('lkp_payment_modes')
                    ->where('lkp_payment_modes.id',$seller_post_payment->lkp_payment_mode_id)
                    ->select('id','payment_mode')
                    ->first();                    
                    return view('trucklease.buyers.buyer_order_details', array(
                        'orderDetails' => $orderDetails,
                        'payment_buyer_details_veiw' => $payment_buyer_details->payment_mode,
                        'priceDetails' => $priceDetails,
                        'cancel_book_date' => $cancel_book_date,
                        'allMessagesList' => $allMessagesList,
                        'tracking' => $seller_post_payment->tracking,
                        'vehicles' => $vehicles,  
                        'minimumLeasePeriod' => $seller_post_payment->minimum_lease_period,
                        'vehicleMakeModelYear' => $seller_post_payment->vehicle_make_model_year,
                        'fuelIncluded' => $seller_post_payment->fuel_included,
                        'driverAvailability' => $seller_post_payment->driver_availability,
                        'truckleaseLeaseTermId' => $seller_post_payment->lkp_trucklease_lease_term_id,
                    ));
                    
                break;
                default : $result = FtlBuyerOrderComponent::getBuyerOrderDetails($serviceId,$orderId,$this->user_pk);
                break;

        }
            
        } else {
            return view('ftl.orders.buyer_orders');
        }
    }

    /**
     * buyer order detail page
     * Cancel order
     * @param integer $orderId
     * @return type
     */
    public function cancelOrder($orderId) {
        try {
            Log::info('Buyer Canceled the order: ' . Auth::id(), array('c' => '1'));
            $roleId = Auth::User()->lkp_role_id;
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_CANCELED_ORDER", BUYER_CANCELED_ORDER, 0, HTTP_REFERRER, CURRENT_URL);
            }
            //update order status
            $order = Order::where('id', $orderId)->first();
            if ($order->seller_pickup_lr_number == "" && $order->lkp_order_status_id!=ORDER_CANCELLED) {
                //order status changing
                Order::where(["id" => $orderId])->update(
                        array(
                            'lkp_order_status_id' => ORDER_CANCELLED,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_ip' => $_SERVER['REMOTE_ADDR'],
                            'updated_by' => Auth::User()->user_id
                        )
                );
                CommonComponent::auditLog($orderId, 'orders');
                if(isset($order->seller_id) && $order->seller_id!=0 && $order->seller_id!=''){
                    $users = DB::table('users')->where('id', $order->seller_id)->get();
                    //$users->email = 'swathi.pakala@quadone.com';
                    if(!empty($users))
                    $users[0]->order_no = $order->order_no;
                    CommonComponent::send_email(CANCEL_ORDER_INFO_MAIL, $users);
                }
            }else{
                return redirect('orders/buyer_orderdetails/' . $orderId)
                            ->with('cancelsuccessmessage', 'Seller has already intiated Pickup (or) Order Cancelled already');
            }

            return redirect('orders/buyer_orderdetails/' . $orderId)
                            ->with('cancelsuccessmessage', 'Order cancelled successfully.');
        } catch (Exception $e) {
            
        }
    }
    
    public function autocompleteVehicles() {
        try {
            Log::info('Auto complete vehicles: ' . Auth::id(), array('c' => '1'));
            $term = Input::get('term');
            $results = array();
            
                $queries = DB::table('vehicle_details')
                                ->where('vehicle_number', 'LIKE', $term . '%')
                                ->where('owner_id',  Auth::id() )
                                ->take(10)->get();
             
            foreach ($queries as $query) {
                $results[] = ['id' => $query->id, 'value' => $query->vehicle_number];
            }
            return Response::json($results);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }
    
    
    public function getVehicleDetails() {
        try {
            Log::info('get vehicle details: ' . Auth::id(), array('c' => '1'));
            $vehicle = $_REQUEST['vehicle'];
            $vehicle    =str_replace(" ","",$vehicle);
            $results = array();
            
                $queries = DB::table('vehicle_details')
                                ->whereRaw("replace(vehicle_number , ' ','')='".$vehicle."'" )
                                ->where('owner_id',  Auth::id() )
                                ->select('chasis_number','engine_number')->first();
             
//            foreach ($queries as $query) {
//                $results[] = ['id' => $query->id, 'value' => $query->vehicle_number];
//            }
            return Response::json($queries);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }
    
    public function addGsaTerms() {
        try{
            //update order with GSA TERMS
            if($_POST ["order_id"]!=''){
            Order::where(["id" => $_POST ["order_id"]])->update(
                    array(
                        'gsa_accepted' => 1,
                        'gsa_accepted_on' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $_SERVER['REMOTE_ADDR'],
                        'updated_by' => Auth::User()->user_id
                    )
            );
                echo "1";
            }else{
                echo "0";
            }
            
        } catch (Exception $ex) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }
    
    

}
