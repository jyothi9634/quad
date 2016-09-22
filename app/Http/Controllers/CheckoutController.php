<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Components\CommonComponent;
use App\Components\CheckoutComponent;
use App\Components\Ptl\PtlCheckoutComponent;
use App\Components\BuyerComponent;
use App\Components\Ftl\FtlBuyerComponent;
use App\Components\Ptl\PtlBuyerComponent;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use App\Models\BuyerDetail;
use App\Models\BuyerBusinessDetail;
use App\Models\Seller;
use App\Models\User;
use App\Models\OrderPayment;
use App\Models\Order;
use App\Components\Intracity\IntracityCheckoutComponent;
use App\Components\SellerOrderComponent;
use Log;
use App\Components\Matching\BuyerMatchingComponent;
use App\Components\Term\TermBuyerComponent;
use App\Models\LogPaymentGateway;
use App\Models\TermContractsIndentQuantitie;
use App\Components\TruckLease\TruckLeaseBuyerComponent;
use App\Components\TruckHaul\TruckHaulBuyerComponent;
// Truck Lease Models
use App\Models\TruckleaseSellerPost;
use App\Models\TruckleaseSellerPostItem;
// Truck Haul Models
use App\Models\TruckhaulSellerPostItem;
use App\Models\TruckhaulSellerPost;



class CheckoutController extends Controller {

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Cart Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        try {
            Log::info('Checkout cart User Id: ' . Auth::id(), array('c' => '1'));
            $buyerId = Auth::id();
                
            $cart_items = CheckoutComponent::getCartItems($buyerId);
            $cartItemsCount = count($cart_items);
            $orderTotal = CommonComponent::moneyFormat(CheckoutComponent::getOrderTotal($buyerId));
            return view('checkout.cart', array('cart_items' => $cart_items, "cart_items_count" => $cartItemsCount, 'order_total' => $orderTotal));
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
     * Delete item from cart
     * @input cart item id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        Log::info('Delete item from cart item id : ' . $id, array('c' => '1'));
        $serviceId = DB::table('cart_items')
            ->select('lkp_service_id')
            ->where('cart_items.id', '=', $id)
            ->first()->lkp_service_id;

        $cartItemArr = DB::table('cart_items')
            ->select('lkp_service_id','is_contract','seller_post_item_id')
            ->where('cart_items.id', '=', $id)
            ->get();
        $isContract = $cartItemArr[0]->is_contract;
        $sellerPostItemId = $cartItemArr[0]->seller_post_item_id;

        switch ($serviceId) {
            case ROAD_FTL :
                if($isContract == 0 || $isContract == '' || $isContract == null){
                    $QuoteItemID    =   DB::table('cart_items')->select('cart_items.buyer_quote_item_id','buyer_quote_items.lkp_post_status_id')->leftjoin('buyer_quote_items','buyer_quote_items.id','=','cart_items.buyer_quote_item_id')->where('cart_items.id', '=', $id)->where('cart_items.lkp_service_id', '=', $serviceId)->get();

                    if($QuoteItemID[0]->lkp_post_status_id==ORDERED)
                    DB::table('buyer_quote_items')->where('id', '=', $QuoteItemID[0]->buyer_quote_item_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                }
                break;
            case ROAD_PTL :
                if($isContract == 0 || $isContract == '' || $isContract == null){
                    $QuoteItemID    =   DB::table('cart_items')->select('cart_items.buyer_quote_item_id','ptl_buyer_quote_items.lkp_post_status_id','ptl_buyer_quote_items.buyer_quote_id')->leftjoin('ptl_buyer_quote_items','ptl_buyer_quote_items.id','=','cart_items.buyer_quote_item_id')->where('cart_items.id', '=', $id)->where('cart_items.lkp_service_id', '=', $serviceId)->get();
                    if(isset($QuoteItemID[0]->lkp_post_status_id) && $QuoteItemID[0]->lkp_post_status_id==ORDERED){
                    DB::table('ptl_buyer_quote_items')->where('id', '=', $QuoteItemID[0]->buyer_quote_item_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    
                    DB::table('ptl_buyer_quotes')->where('id', '=', $QuoteItemID[0]->buyer_quote_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    }
                }
                $sellerPostTableName = 'ptl_seller_post_items';
                break;

            case AIR_DOMESTIC :
                if($isContract == 0 || $isContract == '' || $isContract == null){
                    $QuoteItemID    =   DB::table('cart_items')->select('cart_items.buyer_quote_item_id','airdom_buyer_quote_items.lkp_post_status_id','airdom_buyer_quote_items.buyer_quote_id')->leftjoin('airdom_buyer_quote_items','airdom_buyer_quote_items.id','=','cart_items.buyer_quote_item_id')->where('cart_items.id', '=', $id)->where('cart_items.lkp_service_id', '=', $serviceId)->get();
                    if(isset($QuoteItemID[0]->lkp_post_status_id) && $QuoteItemID[0]->lkp_post_status_id==ORDERED){
                    DB::table('airdom_buyer_quote_items')->where('id', '=', $QuoteItemID[0]->buyer_quote_item_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    
                    DB::table('airdom_buyer_quotes')->where('id', '=', $QuoteItemID[0]->buyer_quote_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    }
                }
                $sellerPostTableName = 'airdom_seller_post_items';
                break;

            case AIR_INTERNATIONAL :
                if($isContract == 0 || $isContract == '' || $isContract == null){
                    $QuoteItemID    =   DB::table('cart_items')->select('cart_items.buyer_quote_item_id','airint_buyer_quote_items.lkp_post_status_id','airint_buyer_quote_items.buyer_quote_id')->leftjoin('airint_buyer_quote_items','airint_buyer_quote_items.id','=','cart_items.buyer_quote_item_id')->where('cart_items.id', '=', $id)->where('cart_items.lkp_service_id', '=', $serviceId)->get();
                    if(isset($QuoteItemID[0]->lkp_post_status_id) && $QuoteItemID[0]->lkp_post_status_id==ORDERED){
                    DB::table('airint_buyer_quote_items')->where('id', '=', $QuoteItemID[0]->buyer_quote_item_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    
                    DB::table('airint_buyer_quotes')->where('id', '=', $QuoteItemID[0]->buyer_quote_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    }
                }
                $sellerPostTableName = 'airint_seller_post_items';
                break;

            case RAIL :
                if($isContract == 0 || $isContract == '' || $isContract == null){
                    $QuoteItemID    =   DB::table('cart_items')->select('cart_items.buyer_quote_item_id','rail_buyer_quote_items.lkp_post_status_id','rail_buyer_quote_items.buyer_quote_id')->leftjoin('rail_buyer_quote_items','rail_buyer_quote_items.id','=','cart_items.buyer_quote_item_id')->where('cart_items.id', '=', $id)->where('cart_items.lkp_service_id', '=', $serviceId)->get();
                    if(isset($QuoteItemID[0]->lkp_post_status_id) && $QuoteItemID[0]->lkp_post_status_id==ORDERED){
                    DB::table('rail_buyer_quote_items')->where('id', '=', $QuoteItemID[0]->buyer_quote_item_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    
                    DB::table('rail_buyer_quotes')->where('id', '=', $QuoteItemID[0]->buyer_quote_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    }
                }
                $sellerPostTableName = 'rail_seller_post_items';
                break;

            case OCEAN :
                if($isContract == 0 || $isContract == '' || $isContract == null){
                    $QuoteItemID    =   DB::table('cart_items')->select('cart_items.buyer_quote_item_id','ocean_buyer_quote_items.lkp_post_status_id','ocean_buyer_quote_items.buyer_quote_id')->leftjoin('ocean_buyer_quote_items','ocean_buyer_quote_items.id','=','cart_items.buyer_quote_item_id')->where('cart_items.id', '=', $id)->where('cart_items.lkp_service_id', '=', $serviceId)->get();
                    if(isset($QuoteItemID[0]->lkp_post_status_id) && $QuoteItemID[0]->lkp_post_status_id==ORDERED){
                    DB::table('ocean_buyer_quote_items')->where('id', '=', $QuoteItemID[0]->buyer_quote_item_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    
                    DB::table('ocean_buyer_quotes')->where('id', '=', $QuoteItemID[0]->buyer_quote_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    }
                }
                $sellerPostTableName = 'ocean_seller_post_items';
                break;
            case COURIER :
                if($isContract == 0 || $isContract == '' || $isContract == null){
                    $QuoteItemID    =   DB::table('cart_items')->select('cart_items.buyer_quote_item_id','courier_buyer_quote_items.lkp_post_status_id','courier_buyer_quote_items.buyer_quote_id')->leftjoin('courier_buyer_quote_items','courier_buyer_quote_items.id','=','cart_items.buyer_quote_item_id')->where('cart_items.id', '=', $id)->where('cart_items.lkp_service_id', '=', $serviceId)->get();
                    if(isset($QuoteItemID[0]->lkp_post_status_id) && $QuoteItemID[0]->lkp_post_status_id==ORDERED){
                    DB::table('courier_buyer_quote_items')->where('id', '=', $QuoteItemID[0]->buyer_quote_item_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    
                    DB::table('courier_buyer_quotes')->where('id', '=', $QuoteItemID[0]->buyer_quote_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    }
                }
                $sellerPostTableName = 'courier_seller_post_items';
                break;
            case RELOCATION_DOMESTIC :
                if($isContract == 0 || $isContract == '' || $isContract == null){
                    $QuoteItemID    =   DB::table('cart_items')->select('cart_items.buyer_quote_id','relocation_buyer_posts.lkp_post_status_id','relocation_buyer_posts.id')->leftjoin('relocation_buyer_posts','relocation_buyer_posts.id','=','cart_items.buyer_quote_id')->where('cart_items.id', '=', $id)->where('cart_items.lkp_service_id', '=', $serviceId)->get();
                    if(isset($QuoteItemID[0]->lkp_post_status_id) && $QuoteItemID[0]->lkp_post_status_id==ORDERED){
                    
                    DB::table('relocation_buyer_posts')->where('id', '=', $QuoteItemID[0]->buyer_quote_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    }
                }
                $sellerPostTableName = 'relocation_seller_posts';
                break;
            case RELOCATION_OFFICE_MOVE :
                if($isContract == 0 || $isContract == '' || $isContract == null){
                    $QuoteItemID    =   DB::table('cart_items')->select('cart_items.buyer_quote_id','relocationoffice_buyer_posts.lkp_post_status_id','relocationoffice_buyer_posts.id')->leftjoin('relocationoffice_buyer_posts','relocationoffice_buyer_posts.id','=','cart_items.buyer_quote_id')->where('cart_items.id', '=', $id)->where('cart_items.lkp_service_id', '=', $serviceId)->get();
                    if(isset($QuoteItemID[0]->lkp_post_status_id) && $QuoteItemID[0]->lkp_post_status_id==ORDERED){
                    
                    DB::table('relocationoffice_buyer_posts')->where('id', '=', $QuoteItemID[0]->buyer_quote_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    }
                }
                $sellerPostTableName = 'relocationoffice_seller_posts';
                break;  
            case RELOCATION_PET_MOVE :
                if($isContract == 0 || $isContract == '' || $isContract == null){
                    $QuoteItemID    =   DB::table('cart_items')->select('cart_items.buyer_quote_id','bp.lkp_post_status_id','bp.id')->leftjoin('relocationpet_buyer_posts as bp','bp.id','=','cart_items.buyer_quote_id')->where('cart_items.id', '=', $id)->where('cart_items.lkp_service_id', '=', $serviceId)->get();
                    if(isset($QuoteItemID[0]->lkp_post_status_id) && $QuoteItemID[0]->lkp_post_status_id==ORDERED){
                    
                    DB::table('relocationpet_buyer_posts')->where('id', '=', $QuoteItemID[0]->buyer_quote_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    }
                }
                $sellerPostTableName = 'relocationpet_seller_posts';
                break;  
            case RELOCATION_GLOBAL_MOBILITY :
                if($isContract == 0 || $isContract == '' || $isContract == null){
                    $QuoteItemID    =   DB::table('cart_items')->select('cart_items.buyer_quote_id','bp.lkp_post_status_id','bp.id')->leftjoin('relocationgm_buyer_posts as bp','bp.id','=','cart_items.buyer_quote_id')->where('cart_items.id', '=', $id)->where('cart_items.lkp_service_id', '=', $serviceId)->get();
                    if(isset($QuoteItemID[0]->lkp_post_status_id) && $QuoteItemID[0]->lkp_post_status_id==ORDERED){
                    
                    DB::table('relocationgm_buyer_posts')->where('id', '=', $QuoteItemID[0]->buyer_quote_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                    }
                }
                $sellerPostTableName = 'relocationgm_seller_posts';
                break;    
            case ROAD_TRUCK_HAUL :
                if($isContract == 0 || $isContract == '' || $isContract == null){
                    $QuoteItemID    =   DB::table('cart_items')->select('cart_items.buyer_quote_item_id','truckhaul_buyer_quote_items.lkp_post_status_id')->leftjoin('truckhaul_buyer_quote_items','truckhaul_buyer_quote_items.id','=','cart_items.buyer_quote_item_id')->where('cart_items.id', '=', $id)->where('cart_items.lkp_service_id', '=', $serviceId)->get();

                    if($QuoteItemID[0]->lkp_post_status_id==ORDERED)
                    DB::table('truckhaul_buyer_quote_items')->where('id', '=', $QuoteItemID[0]->buyer_quote_item_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                }
                break;
            case ROAD_TRUCK_LEASE :
                if($isContract == 0 || $isContract == '' || $isContract == null){
                    $QuoteItemID    =   DB::table('cart_items')->select('cart_items.buyer_quote_item_id','trucklease_buyer_quote_items.lkp_post_status_id')->leftjoin('trucklease_buyer_quote_items','trucklease_buyer_quote_items.id','=','cart_items.buyer_quote_item_id')->where('cart_items.id', '=', $id)->where('cart_items.lkp_service_id', '=', $serviceId)->get();

                    if($QuoteItemID[0]->lkp_post_status_id==ORDERED)
                    DB::table('trucklease_buyer_quote_items')->where('id', '=', $QuoteItemID[0]->buyer_quote_item_id)->update(array(
                                                            'lkp_post_status_id' => ABANDONED));
                }
                break;    
                
           
        }
        DB::table('cart_items')->where('id', '=', $id)->delete();
        DB::table('view_cart_items')->where('id', '=', $id)->delete();
        if($serviceId == ROAD_FTL && empty($sellerPostTableName) && !empty($sellerPostItemId)) {
            FtlBuyerComponent::changeStatusForSellerPostItem($sellerPostItemId, OPEN);
        } elseif($serviceId != ROAD_FTL && !empty($sellerPostItemId) && !empty($sellerPostTableName)) {
            PtlBuyerComponent::changeStatusForSellerPostItem($sellerPostTableName, $sellerPostItemId, OPEN);
        } elseif($serviceId == ROAD_TRUCK_HAUL && empty($sellerPostTableName) && !empty($sellerPostItemId)) {
            TruckHaulBuyerComponent::changeStatusForSellerPostItem($sellerPostItemId, OPEN);
        }elseif($serviceId == ROAD_TRUCK_LEASE && empty($sellerPostTableName) && !empty($sellerPostItemId)) {
            TruckLeaseBuyerComponent::changeStatusForSellerPostItem($sellerPostItemId, OPEN);
        }

        return redirect('cart')
                        ->with('message', CART_ITEM_DELETED);
    }

    /**
     * Clearing cart items
     *
     * @return \Illuminate\Http\Response
     */
    public function clear() {
        $buyerId = Auth::id();
        Log::info('Clearing cart items  user id: ' . $buyerId, array('c' => '1'));
        DB::table('cart_items')->where('buyer_id', '=', $buyerId)->delete();
        DB::table('view_cart_items')->where('buyer_id', '=', $buyerId)->delete();

        return redirect('cart')
                        ->with('message', CART_CLEARED);
    }

    /**
     * Checkout Payment Page
     *
     * @return \Illuminate\Http\Response
     */
    public function makePayment() {
        try {
            Log::info('Checkout Make Payment User Id: ' . Auth::id(), array('c' => '1'));
            $buyerId = Auth::id();
            $cart_items = CheckoutComponent::getCartItems($buyerId);
            $cartItemsCount = count($cart_items);
            if($cartItemsCount == '' || $cartItemsCount =='0'){
                return redirect('cart')
                        ->with('message', NO_ITEMS_TO_CHECKOUT);
            }
            $orderTotal = CheckoutComponent::getOrderTotal($buyerId);
            $orderTotalToDisplay = CommonComponent::moneyFormat($orderTotal);
            for($k=0;$k<$cartItemsCount;$k++){
                $serviceId = $cart_items[$k]->lkp_service_id;
                $is_contract = $cart_items[$k]->is_contract;
                if($serviceId == ROAD_FTL) {
                    $checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId, $serviceId, $cart_items[$k]->is_contract);                   
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, ROAD_FTL);
                } elseif($serviceId == ROAD_PTL) {
                    $checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId,$serviceId, $cart_items[$k]->is_contract);
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, ROAD_PTL);
                } elseif($serviceId == AIR_DOMESTIC) {
                    $checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId,$serviceId, $cart_items[$k]->is_contract);
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, AIR_DOMESTIC);
                } elseif($serviceId == AIR_INTERNATIONAL) {
                    $checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId,$serviceId, $cart_items[$k]->is_contract);
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, AIR_INTERNATIONAL);
                } elseif($serviceId == RAIL) {
                    $checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId,$serviceId, $cart_items[$k]->is_contract);
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, RAIL);
                }elseif($serviceId == OCEAN) {
                    $checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId,$serviceId, $cart_items[$k]->is_contract);
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, OCEAN);
                }elseif($serviceId == COURIER) {
                    $checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId,$serviceId, $cart_items[$k]->is_contract);
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, COURIER);
                }elseif($serviceId == RELOCATION_DOMESTIC) {
                    $checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId,$serviceId, $cart_items[$k]->is_contract);
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, RELOCATION_DOMESTIC);
                }elseif($serviceId == RELOCATION_INTERNATIONAL) {
                    $checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId,$serviceId, $cart_items[$k]->is_contract);
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, RELOCATION_INTERNATIONAL);
                }elseif($serviceId == RELOCATION_OFFICE_MOVE) {
                    $checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId,$serviceId, $cart_items[$k]->is_contract);
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, RELOCATION_DOMESTIC);
                }elseif($serviceId == RELOCATION_PET_MOVE) {
                    $checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId,$serviceId, $cart_items[$k]->is_contract);
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, RELOCATION_PET_MOVE);
                }elseif($serviceId == RELOCATION_GLOBAL_MOBILITY) {
                	$checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId,$serviceId, $cart_items[$k]->is_contract);
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, RELOCATION_GLOBAL_MOBILITY);
                }elseif($serviceId == ROAD_TRUCK_HAUL) {
                    $checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId, $serviceId, $cart_items[$k]->is_contract);                   
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, ROAD_TRUCK_HAUL);
                }elseif($serviceId == ROAD_TRUCK_LEASE) {
                    $checkoutMethods = CheckoutComponent::getCartItemPaymentMethods($buyerId, $serviceId, $cart_items[$k]->is_contract);                   
                    $orderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, ROAD_TRUCK_LEASE);
                    
                }
            }
            
            if(!empty($checkoutMethods) && $checkoutMethods->lkp_payment_mode_id == CASH_ON_DELIVERY || $checkoutMethods->lkp_payment_mode_id == CASH_ON_PICKUP){
                $orderServiceTaxes->order_total_amount = $orderTotal;
                $orderServiceTaxes->order_service_tax_amount = 0.00;
            }
          
           return view('checkout.make_payment', array('checkout_methods' => $checkoutMethods, 
                'orderServiceTax' => $orderServiceTaxes,"cart_items_count" => $cartItemsCount,
                'order_total' => $orderTotalToDisplay, "cart_items" => $cart_items,
                'is_contract' => $is_contract));
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
     * Create order payment page
     *
     * @return \Illuminate\Http\Response
     */
    public function _createOrderPayment($request) {
    	
    	
        try {
            Log::info('Checkout Confirm Payment User Id: ' . Auth::id(), array('c' => '1'));
            $buyerId = Auth::id();
            $input = Input::all();
            $serviceId = Session::get('service_id');
            $serviceName = '';
            $created_year = date('Y');
            
            
            $cart_items = CheckoutComponent::getCartItems($buyerId);
            $cartItemsCount = count($cart_items);
            if($cartItemsCount == '' || $cartItemsCount =='0'){
                return redirect('cart')
                        ->with('message', NO_ITEMS_TO_CHECKOUT);
            }
            $orderTotal = CheckoutComponent::getOrderTotal($buyerId);
            CommonComponent::moneyFormat($orderTotal);
            $randString = '';
            $orderData = array();
            $orderData = $cart_items;
            $order_confirm_total = 0;

            $randStringPayment = $created_year.'/'. str_pad($input['total_amount_paid'], 6, "0", STR_PAD_LEFT); 
            $orderPayment = new OrderPayment();
            $orderPayment->order_payment_no = $randStringPayment;
           
            $orderPayment->lkp_payment_mode_id = $input['lkp_payment_mode_id'];
            $orderPayment->lkp_payment_method_id = $input['payment_method'];
           
            $orderPayment->base_amount_paid = $input['total_amount_paid'];
            $orderPayment->amount_authorized = '0';
            $orderPayment->additional_data = '';
            $orderPayment->transaction_id = '';
            $orderPayment->payment_response = "";
            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER['REMOTE_ADDR'];
            $orderPayment->created_by = Auth::id();
            $orderPayment->created_at = $created_at;
            $orderPayment->created_ip = $createdIp;
            $orderPayment->save();

            count($cart_items);
            //dd($cart_items);
            for ($c = 0; $c < count($cart_items); $c++) {
                //Insert into order payment and orders
                $serviceId = $cart_items[$c]->lkp_service_id;
                $OrderServiceTaxes = CheckoutComponent::getOrderServiceTax($cart_items[$c]->price, $serviceId);
                $ordid  = CommonComponent::getOrderID();
                if($serviceId == ROAD_FTL) {
                    $serviceName = 'FTL/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                } elseif($serviceId == ROAD_PTL) {
                    $serviceName = 'LTL/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                } elseif($serviceId == AIR_DOMESTIC) {
                    $serviceName = 'AIRDOMESTIC/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                }elseif($serviceId == AIR_INTERNATIONAL) {
                    $serviceName = 'AIRINTERNATIONAL/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                }elseif($serviceId == RAIL) {
                    $serviceName = 'RAIL/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                }elseif($serviceId == OCEAN) {
                    $serviceName = 'OCEAN/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                }elseif($serviceId == COURIER) {
                    $serviceName = 'COURIER/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                }elseif($serviceId == RELOCATION_DOMESTIC) {
                    $serviceName = 'RD/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                }elseif($serviceId == RELOCATION_INTERNATIONAL) {
                    $serviceName = 'REL-INT/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                }elseif($serviceId == RELOCATION_OFFICE_MOVE) {
                    $serviceName = 'REL-OFF/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                }elseif($serviceId == RELOCATION_PET_MOVE) {
                    $serviceName = 'RELOCATIONPETMOVE/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                }elseif($serviceId == RELOCATION_GLOBAL_MOBILITY) {
                    $serviceName = 'RELOCATIONGM/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                }elseif($serviceId == ROAD_TRUCK_HAUL) {
                    $serviceName = 'TRUCKHAUL/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                }elseif($serviceId == ROAD_TRUCK_LEASE) {
                    $serviceName = 'TRUCKLEASE/'.$created_year.'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                }else {
                    return ['success' => 0];
                }
                $randString = $serviceName;
                
                    CommonComponent::auditLog($orderPayment->id, 'order_payments');
                    $ordersDetails = new Order();
                    $ordersDetails->order_no = $randString;
                    if($cart_items[$c]->is_contract ==  IS_CONTRACT){
                    $ordersDetails->lkp_order_type_id = TERMSORDER;
                    }else if($cart_items[$c]->is_contract ==  NOT_CONTRACT){
                    $ordersDetails->lkp_order_type_id = SPOTORDER;
                    }
                    $ordersDetails->order_payment_id = $orderPayment->id;
                    //$ordersDetails->order_invoice_id = '1';
                    $ordersDetails->buyer_id = $cart_items[$c]->buyer_id;
                    $ordersDetails->seller_id = $cart_items[$c]->seller_id;
                    $ordersDetails->lkp_international_type_id = $cart_items[$c]->lkp_international_type_id;
                    if($serviceId == ROAD_PTL || $serviceId == AIR_DOMESTIC 
                        || $serviceId == AIR_INTERNATIONAL || $serviceId == RAIL 
                        || $serviceId == OCEAN || $serviceId == COURIER || $serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_INTERNATIONAL || $serviceId == RELOCATION_OFFICE_MOVE || $serviceId == RELOCATION_PET_MOVE || $serviceId == RELOCATION_GLOBAL_MOBILITY) {
                        if($cart_items[$c]->buyer_quote_item_id!="" && $cart_items[$c]->buyer_quote_item_id!=0)
                            $ordersDetails->buyer_quote_item_id = $cart_items[$c]->buyer_quote_item_id;
                            
                        else
                            $ordersDetails->buyer_quote_id = $cart_items[$c]->buyer_quote_id;
                    }elseif($serviceId == ROAD_FTL || $serviceId == ROAD_TRUCK_HAUL || $serviceId == ROAD_TRUCK_LEASE){
                        $ordersDetails->buyer_quote_item_id = $cart_items[$c]->buyer_quote_item_id;
                    }
                    $ordersDetails->seller_post_item_id = $cart_items[$c]->seller_post_item_id;
                    $ordersDetails->lkp_src_location_type_id = $cart_items[$c]->lkp_src_location_type_id;
                    $ordersDetails->lkp_dest_location_type_id = $cart_items[$c]->lkp_dest_location_type_id;
                    //other fields
                    if($cart_items[$c]->lkp_src_location_type_id=='11')
                    $ordersDetails->other_src_location_type = $cart_items[$c]->other_src_location_type;
                    if($cart_items[$c]->lkp_dest_location_type_id=='11')
                    $ordersDetails->other_dest_location_type = $cart_items[$c]->other_dest_location_type;
                    if($cart_items[$c]->lkp_packaging_type_id=='13')
                    $ordersDetails->other_packaging_type = $cart_items[$c]->other_packaging_type;
                   
                    $ordersDetails->dispatch_date = $cart_items[$c]->dispatch_date;
                    $ordersDetails->delivery_date = $cart_items[$c]->delivery_date;
                   
                    if ((!empty($cart_items[$c]->buyer_quote_item_id) && $cart_items[$c]->buyer_quote_item_id != 0) ||
                        (!empty($cart_items[$c]->buyer_quote_id) && $cart_items[$c]->buyer_quote_id != 0)) {
						if($serviceId == ROAD_PTL || $serviceId == AIR_DOMESTIC 
                            || $serviceId == AIR_INTERNATIONAL || $serviceId == RAIL 
                            || $serviceId == OCEAN || $serviceId == COURIER || $serviceId == RELOCATION_DOMESTIC  || $serviceId == RELOCATION_INTERNATIONAL || $serviceId == RELOCATION_OFFICE_MOVE || $serviceId == RELOCATION_PET_MOVE || $serviceId == RELOCATION_GLOBAL_MOBILITY) {
                            if($cart_items[$c]->is_contract ==  IS_CONTRACT){
                                $buyerQuoteItemData = TermBuyerComponent::getBuyerTermQuoteItemData($cart_items[$c]->buyer_quote_item_id,$serviceId);
                                if($serviceId == RELOCATION_GLOBAL_MOBILITY){
                                $fromCityId = '';
                                $toCityId = $buyerQuoteItemData[0]->from_location_id;
                                }else{
                                $fromCityId = $buyerQuoteItemData[0]->from_location_id;
                                $toCityId = $buyerQuoteItemData[0]->to_location_id;
                                }
                                //$ordersDetails->dispatch_date = $cart_items[$c]->contract_from_date;
                                
                                if($serviceId == RELOCATION_GLOBAL_MOBILITY){
                                $ordersDetails->dispatch_date = $cart_items[$c]->dispatch_date;
                                }else{
                                $ordersDetails->dispatch_date = $cart_items[$c]->buyer_consignment_pick_up_date;
                                }
                                $ordersDetails->delivery_date = $cart_items[$c]->contract_to_date;
                                
                                if( $serviceId == RELOCATION_INTERNATIONAL){
                                	 
                                	if($buyerQuoteItemData[0]->term_buyer_quote_id){
                                		$intleadtype=TermBuyerComponent::getInternationalType($buyerQuoteItemData[0]->term_buyer_quote_id);
                                		$ordersDetails->lkp_international_type_id = $intleadtype;
                                	}else{
                                		$ordersDetails->lkp_international_type_id = 1;
                                	}
                                }
                            }else if($cart_items[$c]->is_contract ==  NOT_CONTRACT){
                                $buyerQuoteItemData = BuyerComponent::getBuyerQuoteItemData($cart_items[$c]->buyer_quote_id,$serviceId);
                                if($serviceId != RELOCATION_GLOBAL_MOBILITY)
                                $fromCityId = $buyerQuoteItemData[0]->from_location_id;
                                if($serviceId != RELOCATION_OFFICE_MOVE){
                                    if($serviceId == RELOCATION_GLOBAL_MOBILITY){
                                        $toCityId = $buyerQuoteItemData[0]->location_id;
                                        $fromCityId='';
                                    }else
                                    $toCityId = $buyerQuoteItemData[0]->to_location_id;
                                }
                            }
                            if($serviceId != COURIER && $serviceId != RELOCATION_DOMESTIC && $serviceId != RELOCATION_INTERNATIONAL && $serviceId != RELOCATION_OFFICE_MOVE && $serviceId != RELOCATION_PET_MOVE && $serviceId != RELOCATION_GLOBAL_MOBILITY){
                                $ordersDetails->lkp_packaging_type_id = $buyerQuoteItemData[0]->lkp_packaging_type_id;
                                $ordersDetails->lkp_load_type_id = $buyerQuoteItemData[0]->lkp_load_type_id;
                            }
                            if( $serviceId != RELOCATION_DOMESTIC  && $serviceId != RELOCATION_INTERNATIONAL && $serviceId != RELOCATION_OFFICE_MOVE && $serviceId != RELOCATION_PET_MOVE && $serviceId != RELOCATION_GLOBAL_MOBILITY){
                                $ordersDetails->number_loads = $buyerQuoteItemData[0]->number_packages;                            
                            }

                            
                        }elseif($serviceId == ROAD_FTL || $serviceId == ROAD_TRUCK_HAUL || $serviceId == ROAD_TRUCK_LEASE || $serviceId == RELOCATION_INTERNATIONAL || $serviceId == RELOCATION_GLOBAL_MOBILITY){
                        	
                            if($cart_items[$c]->is_contract ==  IS_CONTRACT){
                                $buyerQuoteItemData = TermBuyerComponent::getBuyerTermQuoteItemData($cart_items[$c]->buyer_quote_item_id,$serviceId);
                                $fromCityId = $buyerQuoteItemData[0]->from_location_id;
                                if($serviceId != ROAD_TRUCK_LEASE)
                                $toCityId = $buyerQuoteItemData[0]->to_location_id;
                                //$ordersDetails->dispatch_date = $cart_items[$c]->contract_from_date;
                                if($serviceId == RELOCATION_GLOBAL_MOBILITY){
                                	$ordersDetails->dispatch_date = $cart_items[$c]->dispatch_date;
                                }else{
                                $ordersDetails->dispatch_date = $cart_items[$c]->buyer_consignment_pick_up_date;
                                }
                                $ordersDetails->delivery_date = $cart_items[$c]->contract_to_date;
                                
                                if(Session::has('indentdata')){
                                $indentdata=Session::get('indentdata'); 
                                if(isset($indentdata['valid_id']) && $indentdata['valid_id']!=''){
                                $cont_id=$indentdata['valid_id'];
                                $ordersDetails->quantity = $indentdata['current_indenet_quantity_'.$cont_id];
                                $ordersDetails->number_loads = $indentdata['noofloads_'.$cont_id];
                                }
                                }
                                
                                
                                
                            }else if($cart_items[$c]->is_contract ==  NOT_CONTRACT){
                                $buyerQuoteItemData = BuyerComponent::getBuyerQuoteItemData($cart_items[$c]->buyer_quote_item_id,$serviceId);
                                $fromCityId = $buyerQuoteItemData[0]->from_city_id;
                                if($serviceId != ROAD_TRUCK_LEASE){
                                $toCityId = $buyerQuoteItemData[0]->to_city_id;
                                $ordersDetails->quantity = $buyerQuoteItemData[0]->quantity;
                                $ordersDetails->number_loads = $buyerQuoteItemData[0]->number_loads;
                                }
                            }                            
                            $vehicleTypeId = $buyerQuoteItemData[0]->lkp_vehicle_type_id;
                            if($serviceId != ROAD_TRUCK_LEASE)
                            $ordersDetails->lkp_load_type_id = $buyerQuoteItemData[0]->lkp_load_type_id;
                            $ordersDetails->lkp_vehicle_type_id = $buyerQuoteItemData[0]->lkp_vehicle_type_id;
                            
                           
                            
                        }
                        if($serviceId != COURIER && $serviceId != RELOCATION_DOMESTIC  && $serviceId != RELOCATION_INTERNATIONAL && $serviceId != ROAD_TRUCK_LEASE && $serviceId != RELOCATION_OFFICE_MOVE && $serviceId != RELOCATION_PET_MOVE && $serviceId != RELOCATION_GLOBAL_MOBILITY){
                            $loadTypeId = $buyerQuoteItemData[0]->lkp_load_type_id;
                        }
                        if( $serviceId != RELOCATION_DOMESTIC  && $serviceId != RELOCATION_INTERNATIONAL && $serviceId != ROAD_TRUCK_LEASE && $serviceId != RELOCATION_OFFICE_MOVE && $serviceId != RELOCATION_PET_MOVE && $serviceId != RELOCATION_GLOBAL_MOBILITY){
                            $units = $buyerQuoteItemData[0]->units;
                        }
                        $ordersDetails->lkp_service_id = $serviceId;
                        if($serviceId != RELOCATION_OFFICE_MOVE)
                            if(isset($buyerQuoteItemData[0]->lkp_lead_type_id))
                            $ordersDetails->lkp_lead_type_id = $buyerQuoteItemData[0]->lkp_lead_type_id;
                        $ordersDetails->lkp_quote_access_id = $buyerQuoteItemData[0]->lkp_quote_access_id;
                    } else {                       
                        $sellerPostDetails = CheckoutComponent::getSellerPostDetails($cart_items[$c]->seller_post_item_id,$serviceId);
                        if($serviceId==RELOCATION_GLOBAL_MOBILITY){
                            $fromCityId='';
                            $toCityId = $sellerPostDetails[0]->location_id;
                        }else{
                        $fromCityId = $sellerPostDetails[0]->from_location_id;
                        $toCityId = $sellerPostDetails[0]->to_location_id;
                        
                        }
                        if($serviceId != ROAD_PTL && $serviceId != AIR_DOMESTIC 
                        && $serviceId != AIR_INTERNATIONAL && $serviceId != RAIL 
                        && $serviceId != OCEAN && $serviceId != COURIER && $serviceId != RELOCATION_DOMESTIC  && $serviceId != RELOCATION_INTERNATIONAL && $serviceId != RELOCATION_OFFICE_MOVE && $serviceId != RELOCATION_PET_MOVE && $serviceId!=RELOCATION_GLOBAL_MOBILITY) {
                            $loadTypeId = $sellerPostDetails[0]->lkp_load_type_id;
                            $vehicleTypeId = $sellerPostDetails[0]->lkp_vehicle_type_id;
                        }
                        if($serviceId!=RELOCATION_GLOBAL_MOBILITY)
                        $units = $sellerPostDetails[0]->units;
                    }
                    $ordersDetails->from_city_id = $fromCityId;
                    if($serviceId != ROAD_TRUCK_LEASE && $serviceId != RELOCATION_OFFICE_MOVE)
                    $ordersDetails->to_city_id = $toCityId;
                    if($serviceId != ROAD_PTL && $serviceId != AIR_DOMESTIC 
                        && $serviceId != AIR_INTERNATIONAL && $serviceId != RAIL 
                        && $serviceId != OCEAN && $serviceId != COURIER && $serviceId != RELOCATION_DOMESTIC  && $serviceId != RELOCATION_INTERNATIONAL && $serviceId != RELOCATION_OFFICE_MOVE && $serviceId != RELOCATION_PET_MOVE && $serviceId!=RELOCATION_GLOBAL_MOBILITY) {
                        if($serviceId != ROAD_TRUCK_LEASE )
                        $ordersDetails->lkp_load_type_id = $loadTypeId;
                        $ordersDetails->lkp_vehicle_type_id = $vehicleTypeId;
                    }
                    if( $serviceId != RELOCATION_DOMESTIC  && $serviceId != RELOCATION_INTERNATIONAL && $serviceId != ROAD_TRUCK_LEASE && $serviceId != RELOCATION_OFFICE_MOVE && $serviceId != RELOCATION_PET_MOVE && $serviceId!=RELOCATION_GLOBAL_MOBILITY){
                        $ordersDetails->units = $units;
                    }
                    if($cart_items[$c]->is_contract == '' || $cart_items[$c]->is_contract == null || $cart_items[$c]->is_contract == 0){
                        $ordersDetails->is_contract = NOT_CONTRACT;
                    }else{
                        $ordersDetails->is_contract = $cart_items[$c]->is_contract;
                    }
                    $ordersDetails->term_contract_id = $cart_items[$c]->term_contract_id;
                    if(isset($cart_items[$c]->term_contract_id)){
                        $term_indent_sql = DB::table('term_contracts_indent_quantities');
                        $term_indent_data = $term_indent_sql->select('id')->where('created_by','=',$cart_items[$c]->buyer_id)
                                ->where('term_contract_id','=',$cart_items[$c]->term_contract_id)
                                ->orderBy('created_at','DESC')->first();
                        //echo "<pre>";dd($term_indent_data);exit;
                        $ordersDetails->term_placeindent_id = $term_indent_data->id;
                    }
                    $ordersDetails->price = $cart_items[$c]->price;
                    $ordersDetails->buyer_consignment_pick_up_date = $cart_items[$c]->buyer_consignment_pick_up_date;
                    if($serviceId == ROAD_TRUCK_HAUL || $serviceId == ROAD_TRUCK_LEASE){
                    $ordersDetails->buyer_consignment_pick_up_time_from = $cart_items[$c]->buyer_consignment_pick_up_time_from;
                    $ordersDetails->buyer_consignment_pick_up_time_to = $cart_items[$c]->buyer_consignment_pick_up_time_to;
                    }
                    $ordersDetails->buyer_consignment_value = $cart_items[$c]->buyer_consignment_value;
                    $ordersDetails->buyer_consignment_needs_insurance = $cart_items[$c]->buyer_consignment_needs_insurance;
                    $ordersDetails->buyer_consignment_needs_fragile = $cart_items[$c]->buyer_consignment_needs_fragile;
                    $ordersDetails->buyer_consignor_name = $cart_items[$c]->buyer_consignor_name;
                    $ordersDetails->buyer_consignor_address = $cart_items[$c]->buyer_consignor_address;
                    $ordersDetails->buyer_consignor_mobile = $cart_items[$c]->buyer_consignor_mobile;
                    $ordersDetails->buyer_consignor_email = $cart_items[$c]->buyer_consignor_email;
                    $ordersDetails->buyer_consignor_pincode = $cart_items[$c]->buyer_consignor_pincode;
                    $ordersDetails->buyer_consignee_name = $cart_items[$c]->buyer_consignee_name;
                    $ordersDetails->buyer_consignee_address = $cart_items[$c]->buyer_consignee_address;
                    $ordersDetails->buyer_consignee_mobile = $cart_items[$c]->buyer_consignee_mobile;
                    $ordersDetails->buyer_consignee_email = $cart_items[$c]->buyer_consignee_email;
                    $ordersDetails->buyer_consignee_pincode = $cart_items[$c]->buyer_consignee_pincode;
                    $ordersDetails->buyer_additional_details = $cart_items[$c]->buyer_additional_details;
                    $ordersDetails->lkp_service_id = $serviceId;
                    $ordersDetails->tracking_confirm = '0';
                    $ordersDetails->lkp_order_status_id = ORDER_PENDING;
                    $created_at = date('Y-m-d H:i:s');
                    $ordersDetails->created_by = Auth::id();
                    $ordersDetails->created_at = $created_at;
                    $ordersDetails->created_ip = $createdIp;
                    if ($ordersDetails->save()) {
                        CommonComponent::auditLog($ordersDetails->id, 'orders');
                        $orderData[$c]->order_id = $randString;
                        $emailData = DB::table('orders')
                                ->where('orders.id', $ordersDetails->id)
                                ->join('users', 'orders.buyer_id', '=', 'users.id')
                                ->select('orders.price', 'orders.order_no', 'users.username')
                                ->get();
                        $emailData[0]->email = Auth::User()->email;
                        if($cart_items[$c]->lkp_payment_mode_id == CASH_ON_DELIVERY || $cart_items[$c]->lkp_payment_mode_id == CASH_ON_PICKUP){
                            $emailData[0]->order_total_amount = $cart_items[$c]->price;
                            $emailData[0]->order_service_tax_amount =0.00;
                        }else{
                           $emailData[0]->order_service_tax_amount = $OrderServiceTaxes->order_service_tax_amount;
                           $emailData[0]->order_total_amount = $OrderServiceTaxes->order_total_amount;
                        }
                        $orderData[$c]->order_total_amount = $emailData[0]->order_total_amount;
                        $order_confirm_total += $emailData[0]->order_total_amount;
                        //CommonComponent::send_email(INVOICE_CONFIRMATION_MAIL, $emailData);
                        $payment = \DB::table('orders')->leftjoin('order_payments', 'orders.order_payment_id', '=', 'order_payments.id')->leftjoin('lkp_payment_modes', 'lkp_payment_modes.id', '=', 'order_payments.lkp_payment_mode_id')->where('orders.id', $ordersDetails->id)->select('order_payments.lkp_payment_mode_id')->get();
                        
                        if ($payment[0]->lkp_payment_mode_id == ADVANCED) {
                            //SellerOrderComponent::addInvoice($ordersDetails->id, $serviceId, $cart_items[$c]->lkp_payment_mode_id);
                        }
                       DB::delete("delete from cart_items where buyer_id='" . $cart_items[$c]->buyer_id . "' and id='".$cart_items[$c]->id."'");

                       DB::insert(DB::raw("INSERT INTO confirmed_cart_items (buyer_id,seller_id,lkp_service_id,buyer_quote_item_id,buyer_quote_id,seller_post_item_id,lkp_packaging_type_id,lkp_src_location_type_id,lkp_dest_location_type_id,other_packaging_type,other_src_location_type,other_dest_location_type,price,dispatch_date,delivery_date,buyer_consignment_pick_up_date,buyer_consignment_value,buyer_consignment_needs_insurance,buyer_consignment_needs_fragile,buyer_consignor_name,buyer_consignor_address,buyer_consignor_mobile,buyer_consignor_email,buyer_consignor_pincode,buyer_consignee_name,buyer_consignee_address,buyer_consignee_mobile,buyer_consignee_email,buyer_consignee_pincode,buyer_additional_details,lkp_ict_vehicle_id,lkp_payment_mode_id,username,from_location,to_location,order_dispatch_date,post_status,service_name,is_contract,term_contract_id,order_payment_id,contract_from_date,contract_to_date,created_by,created_at,created_ip,updated_by,updated_at,updated_ip) select buyer_id,seller_id,lkp_service_id,buyer_quote_item_id,buyer_quote_id,seller_post_item_id,lkp_packaging_type_id,lkp_src_location_type_id,lkp_dest_location_type_id,other_packaging_type,other_src_location_type,other_dest_location_type,price,dispatch_date,delivery_date,buyer_consignment_pick_up_date,buyer_consignment_value,buyer_consignment_needs_insurance,buyer_consignment_needs_fragile,buyer_consignor_name,buyer_consignor_address,buyer_consignor_mobile,buyer_consignor_email,buyer_consignor_pincode,buyer_consignee_name,buyer_consignee_address,buyer_consignee_mobile,buyer_consignee_email,buyer_consignee_pincode,buyer_additional_details,lkp_ict_vehicle_id,lkp_payment_mode_id,username,from_location,to_location,order_dispatch_date,post_status,service_name,is_contract,term_contract_id,".$orderPayment->id.",contract_from_date,contract_to_date,created_by,created_at,created_ip,updated_by,updated_at,updated_ip from view_cart_items where buyer_id='".$cart_items[$c]->buyer_id."' and id='".$cart_items[$c]->id."'"));

                       DB::delete("delete from view_cart_items where buyer_id='" . $cart_items[$c]->buyer_id . "' and id='".$cart_items[$c]->id."'");

                        if($serviceId == ROAD_PTL || $serviceId == AIR_DOMESTIC 
                            || $serviceId == AIR_INTERNATIONAL || $serviceId == RAIL 
                            || $serviceId == OCEAN || $serviceId == COURIER || $serviceId == RELOCATION_DOMESTIC  || $serviceId == RELOCATION_INTERNATIONAL ||  $serviceId == RELOCATION_OFFICE_MOVE ||  $serviceId == RELOCATION_PET_MOVE ||  $serviceId == RELOCATION_GLOBAL_MOBILITY) {
                            if($cart_items[$c]->is_contract ==  NOT_CONTRACT && !empty($cart_items[$c]->buyer_quote_id) && $cart_items[$c]->buyer_quote_id != "0") {
                                CheckoutComponent::setBuyerQuoteStatusForPtl($cart_items[$c]->buyer_quote_id,$serviceId);
                                //BuyerMatchingComponent::removeFromMatching($serviceId,$cart_items[$c]->buyer_quote_id);
                            }
                        }elseif($serviceId == ROAD_FTL){
                            if($cart_items[$c]->is_contract ==  NOT_CONTRACT && !empty($cart_items[$c]->buyer_quote_item_id) && $cart_items[$c]->buyer_quote_item_id != "0") {
                                CheckoutComponent::setBuyerQuoteStatusForFtl($cart_items[$c]->buyer_quote_item_id,$serviceId);
                                //BuyerMatchingComponent::removeFromMatching($serviceId,$cart_items[$c]->buyer_quote_item_id);
                            }
                        }elseif($serviceId == ROAD_TRUCK_HAUL){
                            if($cart_items[$c]->is_contract ==  NOT_CONTRACT && !empty($cart_items[$c]->buyer_quote_item_id) && $cart_items[$c]->buyer_quote_item_id != "0") {
                                CheckoutComponent::setBuyerQuoteStatusForTruckHaul($cart_items[$c]->buyer_quote_item_id,$cart_items[$c]->seller_post_item_id,$serviceId);
                                //BuyerMatchingComponent::removeFromMatching($serviceId,$cart_items[$c]->buyer_quote_item_id);
                            }
                        }elseif($serviceId == ROAD_TRUCK_LEASE){
                            if($cart_items[$c]->is_contract ==  NOT_CONTRACT && !empty($cart_items[$c]->buyer_quote_item_id) && $cart_items[$c]->buyer_quote_item_id != "0") {
                                CheckoutComponent::setBuyerQuoteStatusForTruckLease($cart_items[$c]->buyer_quote_item_id,$serviceId);
                                //BuyerMatchingComponent::removeFromMatching($serviceId,$cart_items[$c]->buyer_quote_item_id);
                            }
                        }
                    }
                   
                    if($serviceId != RELOCATION_DOMESTIC && $serviceId != RELOCATION_INTERNATIONAL && $serviceId != RELOCATION_OFFICE_MOVE && $serviceId != RELOCATION_PET_MOVE){
		            	TermContractsIndentQuantitie::where([
		            			"term_contract_id" => $cart_items[$c]->term_contract_id])
		            	->update(array('is_saved' => 1));
            			}
		                   

            }
           
            return $orderPayment->id;
           
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
     * Confirm Order Page
     *
     * @return \Illuminate\Http\Response
     */
    public function confirmOrder($paymentID){
        
        $order_refference = base64_decode($paymentID);
        $orderPaymentDetails = DB::table('order_payments')
                            ->where('id','=',$order_refference)
                            ->first();
        if($orderPaymentDetails){
            $order_confirm_total = $orderPaymentDetails->base_amount_paid;                    
            $orderData = DB::table('confirmed_cart_items')   
                            ->where('confirmed_cart_items.order_payment_id','=',$order_refference)
                            ->leftjoin('orders', 'orders.order_payment_id','=','confirmed_cart_items.order_payment_id')
                            //->join('orders', 'orders.buyer_quote_item_id','=','confirmed_cart_items.buyer_quote_item_id')
                            ->select('confirmed_cart_items.*','orders.order_no as order_id','orders.price as order_total_amount')
                            //->groupBy("orders.order_no")
                            ->groupBy("orders.id")
                            ->get();

            $total = DB::table('confirmed_cart_items')
                ->select(DB::raw('sum(price) as ordertotal'))
                ->where('order_payment_id','=',$order_refference)
                ->first();        

            $orderTotalToDisplay = $total->ordertotal;

            $cnt = count($orderData);

            return view('checkout.confirm_payment', array('order_total' => $orderTotalToDisplay,"orderData" => $orderData, 'order_confirm_total' => $order_confirm_total,'count'=>$cnt));
        }else{
            return redirect('home')
                        ->with('message', 'Invalid Request');                
        }    
    }

    /**
     * Payment Page
     *
     * @return \Illuminate\Http\Response
     */
    public function postPayment(Request $request) {
        try {
			
                $order_refference = $this->_createOrderPayment($request);
                $request_data = $request->input();
                $cash_delivery = array('C','COD','COP','NB');
                $ordersDetails = OrderPayment::where('id','=',$order_refference)->first();			
                // Check Credit / Cash on delivery / Cash on Pickup and confirm order
                if(in_array($request_data['payment_method'], $cash_delivery)){                // Update Order Status
                    CheckoutController::confirmOrderStatus($order_refference);
                    return redirect('confirmorder/'.base64_encode($order_refference));
                  //return $this->_confirmOrder($order_refference,$ordersDetails['base_amount_paid']);
                }           

                $params = array(
                    'amount' => $ordersDetails['base_amount_paid'],
                    'refference_id' => $order_refference,
                    //'refference_id' => 456789
                    'payment_mode' => $request_data['payment_method']
                );

                if($request_data['gatewayName']=='HDFC'){
                    $PaymentFields = CommonComponent::hdfcFields($params);
                    $PaymentURL = HDFC_PAYMENT_GATEWAY_URL;
                }

                //Save Log Payment Gateway
                $saveLogPayment = new LogPaymentGateway();
                $saveLogPayment->order_payment_id = $order_refference;
                $saveLogPayment->request = serialize($PaymentFields);
                $saveLogPayment->gateway = $request_data['gatewayName'];
                $saveLogPayment->amount = $params['amount'];
                $saveLogPayment->verified_status = 'Pending';
                $saveLogPayment->order_status = 'Pending';
                $saveLogPayment->created_at = date('Y-m-d H:i:s');
                $saveLogPayment->updated_at = date('Y-m-d H:i:s');
                $saveLogPayment->save();

            return view('checkout.post_payment',array(
                        'PaymentFields'=> $PaymentFields,
                        'PaymentURL'   => $PaymentURL,
                    ));
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
    *   HDFC Gateway Responce
    */
    public function hdfcresponse(){

        $gatewayResponse = Input::all();

        $ref_no = $gatewayResponse['MerchantRefNo'];
        $verified_status = 'Payment Failed';
        $order_status = 'Payment Failed';

        $getOrderLogDetails = LogPaymentGateway::where('order_payment_id','=',$ref_no)->first();

        $storeData = array(
                'response'       => serialize($gatewayResponse),
                'transaction_id' => $gatewayResponse['TransactionID'],
                'order_status'   => $order_status,
                'verified_status'=> $verified_status
        );

        //successs
        if($gatewayResponse['ResponseCode']==0){

            // Update Order Status
            CheckoutController::confirmOrderStatus($ref_no);

            if($getOrderLogDetails->amount ==$gatewayResponse['Amount']){
                $storeData['verified_status'] = "Success";
                $storeData['order_status'] = "Success";
                $error = false;
                $order_status = '1';
            }else{//failure
                $storeData['verified_status'] = "Payment Fraud";
                $storeData['order_status'] = "Payment Fraud";
                $error = true;
                $order_status = '0';
            }   

            $storeLogPaymentGatewayData = LogPaymentGateway::where('order_payment_id','=',$ref_no)
                ->update($storeData);

            Order::where('order_payment_id','=',$ref_no)
                ->update(array('lkp_payment_status_id'=>$order_status));

            if(!$error){
                //return $this->_confirmOrder($ref_no,$getOrderLogDetails->amount);
                return redirect('confirmorder/'.base64_encode($ref_no));
            }else{
                return redirect('home')
                    ->with('message', 'Payment Failed');                
            }    
        }
        else{
            $storeData['verified_status'] = "Payment Fail";
            $storeData['order_status'] = "Payment Fail";
            $storeLogPaymentGatewayData = LogPaymentGateway::where('order_payment_id','=',$ref_no)
                ->update($storeData);
            return redirect('home')
                        ->with('message', 'Payment Failed');                
        }        
    }
    /**
     * Confirm Order Status
     * Start
     * Jagadeesh - 04052016
     */
        public function confirmOrderStatus($ref_no){   

            Order::where('order_payment_id','=',$ref_no)
            ->update(array('lkp_order_status_id'=>ORDER_PICKUP_DUE));

            $orderDetails = Order::where('order_payment_id','=',$ref_no)
                            ->get();
            foreach($orderDetails as $orders){
                $randString = $orders->order_no;
                $serviceId = $orders->lkp_service_id;
                $seller_id = $orders->seller_id;

                //*******Send Sms to Seller***********************//
                $msg_params = array(
                    'ordernumber' => $randString,
                    'buyername' => Auth::User()->username,
                    'servicename' => CommonComponent::getServiceName($serviceId)
                );
                $getMobileNumber  =   CommonComponent::getMobleNumber($seller_id);
                CommonComponent::sendSMS($getMobileNumber,BUYER_BOOKS_CONSIGNMENT_SPOT_TERM,$msg_params);
                //*******Send Sms to Seller***********************//
                //*******Send Email to Seller /Buyer***********************//

                    $OrderServiceTaxes = CheckoutComponent::getOrderServiceTax($orders->price, $serviceId);

                    $emailData = DB::table('orders')
                            ->where('orders.id', $orders->id)
                            ->join('users', 'orders.buyer_id', '=', 'users.id')
                            ->select('orders.price', 'orders.order_no', 'users.username')
                            ->get();
                    $emailData[0]->email = Auth::User()->email;
                    //code added by swathi 05/05/2016 and conditions changed
                    $payment = \DB::table('orders')
                            ->leftjoin('order_payments', 'orders.order_payment_id', '=', 'order_payments.id')
                            ->where('orders.id', $orders->id)->select('order_payments.lkp_payment_mode_id')->get();
                  
                  // Updating post status to booked if the service is truckhaul
                  if($orders->lkp_service_id==ROAD_TRUCK_HAUL){
                        $post_item_details =  TruckhaulSellerPostItem::where('id','=',$orders->seller_post_item_id)->first();
                      /*TruckhaulSellerPost::where('id','=',$post_item_details->seller_post_id)
                        ->update(array('lkp_post_status_id'=>BOOKED));*/
                      TruckhaulSellerPostItem::where('id','=',$orders->seller_post_item_id)
                        ->update(array('lkp_post_status_id'=>CLOSED));
                        $bookedStatus=CommonComponent::getTruckhaulPostitemStatus($post_item_details->seller_post_id);
                        
                       if($bookedStatus==0){
                       	
                       	TruckhaulSellerPost::where('id','=',$post_item_details->seller_post_id)
                       	->update(array('lkp_post_status_id'=>CLOSED));
                       } 
                        
                  }

                  if($orders->lkp_service_id==ROAD_TRUCK_LEASE){
                        $post_item_details =  TruckleaseSellerPostItem::where('id','=',$orders->seller_post_item_id)->first();
                      /*TruckleaseSellerPost::where('id','=',$post_item_details->seller_post_id)
                        ->update(array('lkp_post_status_id'=>BOOKED));*/
                      TruckleaseSellerPostItem::where('id','=',$orders->seller_post_item_id)
                        ->update(array('lkp_post_status_id'=>BOOKED));

                $bookedStatus=CommonComponent::getTruckleasePostitemStatus($post_item_details->seller_post_id); 
                       if($bookedStatus==0){
                        
                       TruckleaseSellerPost::where('id','=',$post_item_details->seller_post_id)
                        ->update(array('lkp_post_status_id'=>BOOKED));
                       } 
                  }


                    if($payment[0]->lkp_payment_mode_id == CASH_ON_DELIVERY || $payment[0]->lkp_payment_mode_id == CASH_ON_PICKUP){
                        $emailData[0]->order_total_amount = $orders->price;
                        $emailData[0]->order_service_tax_amount =0.00;
                    }else{
                       $emailData[0]->order_service_tax_amount = $OrderServiceTaxes->order_service_tax_amount;
                       $emailData[0]->order_total_amount = $OrderServiceTaxes->order_total_amount;
                    }
                    //end comments
                    CommonComponent::send_email(INVOICE_CONFIRMATION_MAIL, $emailData);
                    if ($payment[0]->lkp_payment_mode_id == ADVANCED) {
                        SellerOrderComponent::addInvoice($orders->id, $serviceId, $orders->lkp_payment_mode_id);
                    }
                    $buyerEmailData = DB::table('orders')
                                ->where('orders.id', $orders->id)
                                ->join('users', 'orders.seller_id', '=', 'users.id')
                                ->select('orders.price', 'orders.order_no', 'users.username', 'users.email')
                                ->get();
                        $buyerEmailData[0]->buyername = $emailData[0]->username;
                        CommonComponent::send_email(CHECKOUT_EMAIL_FOR_SELLER, $buyerEmailData);
                //*******Send Email to Seller /Buyer***********************//
                return true;        
            }    
        }
    /**
     * Jagadeesh - 04052016
     * END
     */
}