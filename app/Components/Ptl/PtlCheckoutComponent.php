<?php namespace App\Components\Ptl;

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

use App\Components\CommonComponent;
use App\Components\CheckoutComponent;
use App\Components\BuyerComponent;

use App\Models\OrderPayment;
use App\Models\Order;

class PtlCheckoutComponent{   
    /**
     * Get Cart Items
     * input : Buyer Id
     * Output : Cart Items
     */
    public static function getCartItems($buyerId){
        Log::info('Get Cart Items: ',array('c'=>'2'));
        //echo $buyerId;
        $cart_items_search = DB::table('cart_items')
            ->leftjoin('users','users.id','=','cart_items.seller_id')
            ->leftjoin('ptl_seller_post_items as pspi','pspi.id','=','cart_items.seller_post_item_id')
            ->leftjoin('lkp_cities as c1','pspi.from_location_id','=','c1.id')
            ->leftjoin('lkp_cities as c2','pspi.to_location_id','=','c2.id')
            ->where('cart_items.buyer_id',$buyerId)
            ->where('cart_items.buyer_quote_item_id',0)
            ->select('cart_items.id as id','users.username','c1.city_name as from_locationcity','c2.city_name as to_locationcity',DB::raw('"Not Specified" as dispatch_date') ,'cart_items.price', 'cart_items.*')//->toSql();
            ->get();
        //echo $cart_items_search;die;
        $cart_items = DB::table('cart_items')
            ->leftjoin('users','users.id','=','cart_items.seller_id')
            ->leftjoin('ptl_buyer_quote_items as pbqi','pbqi.id','=','cart_items.buyer_quote_item_id')
            ->leftjoin('ptl_buyer_quotes as pbq','pbq.id','=','pbqi.buyer_quote_id')
            ->leftjoin('lkp_cities as c1','pbq.from_location_id','=','c1.id')
            ->leftjoin('lkp_cities as c2','pbq.to_location_id','=','c2.id')
            ->where('cart_items.buyer_id',$buyerId)
            ->where('cart_items.buyer_quote_item_id',"!=",0)
            ->select('cart_items.id as id','users.username','c1.city_name as from_locationcity','c2.city_name as to_locationcity','pbq.dispatch_date','cart_items.price', 'cart_items.*')
            ->get();
        foreach($cart_items_search as $cart_item_search){
            $cart_items[] = $cart_item_search;
        }
        return $cart_items;
    }
    /**
     * 
     * @param type $cart_items
     * @return type
     */
    public static function getCartItemsCount($cart_items){
        Log::info('Get Cart Items Count: ',array('c'=>'3'));
        return count($cart_items);
    }

    /**
     * Get Cart Item Payment Methods
     * input : Buyer Id
     * Output : Cart Items
     */
    public static function getCartItemPaymentMethods($buyerId){
        Log::info('Get Checkout Make Payment methods: ',array('c'=>'2'));
        
        $cartPaymentMethods = DB::table('cart_items')
            ->join('ptl_seller_post_items as pspi','pspi.id','=','cart_items.seller_post_item_id')
            ->join('ptl_seller_posts as psp','psp.id','=','pspi.seller_post_id')
            ->join('lkp_payment_modes','psp.lkp_payment_mode_id','=','lkp_payment_modes.id')
            ->where('cart_items.buyer_id',$buyerId)
            ->select('pspi.id', 'psp.lkp_payment_mode_id', 'psp.accept_payment_netbanking',
                    'psp.accept_payment_credit', 'psp.accept_payment_debit')
            ->get();
        return $cartPaymentMethods[0];
    }

    /**
    * Checkout Page
    * Method to retrieve seller lists
    * @param int $buyerQuoteItemId
    * @return array
    */
	public static function getSellerPostDetails($sellerPostItemId) {
		try {
			Log::info ('Get details for the seller: ' . Auth::id (), array ('c' => '2'));
			$sellerData = DB::table('ptl_seller_post_items as spi')
			->where('spi.id',$sellerPostItemId)
			->select('spi.from_location_id','spi.to_location_id','spi.units')
			->get();
			return $sellerData;
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}
    /**
    * Checkout Page
    * Method to retrieve seller lists
    * @param int $buyerQuoteItemId
    * @return array
    */
	public static function setOrderDetails($cart_items,$input,$buyerId,$serviceId) {
		try {
			Log::info ('Set order details: ' . Auth::id (), array ('c' => '2'));
            $cartItemsCount = count($cart_items);
            $orderTotal = CheckoutComponent::getOrderTotal($buyerId);
            $orderTotalToDisplay = CommonComponent::moneyFormat($orderTotal);
            $OrderServiceTaxes = CheckoutComponent::getOrderServiceTax($orderTotal, $serviceId);
            $randString = '';
            for ($c = 0; $c < count($cart_items); $c++) {
                //Insert into order payment and orders
                $randString = 'PTL' . $c . rand();
                $orderPayment = new OrderPayment();
                $orderPayment->order_payment_no = $randString;
                $orderPayment->lkp_payment_mode_id = CASH_ON_DELIVERY;
                $orderPayment->lkp_payment_method_id = CASH_ON_DELIVERY_METHOD;
                $orderPayment->base_amount_paid = $input['total_amount_paid'];
                $orderPayment->amount_authorized = '1';
                $orderPayment->additional_data = 'Success';
                $orderPayment->transaction_id = 'some transaction id from gateway';
                $orderPayment->payment_response = "some response from gateway";
                $createdIp = $_SERVER['REMOTE_ADDR'];
                $orderPayment->created_by = Auth::id();
                $orderPayment->created_at = date('Y-m-d H:i:s');
                $orderPayment->created_ip = $createdIp;

                if ($orderPayment->save()) {
                    CommonComponent::auditLog($orderPayment->id, 'order_payments');
                    $randOrderString = 'PTLO' . $orderPayment->id . rand();
                    $ordersDetails = new Order();
                    $ordersDetails->order_no = $randOrderString;
                    $ordersDetails->lkp_order_type_id = SPOTORDER;
                    $ordersDetails->order_payment_id = $orderPayment->id;
                    //$ordersDetails->order_invoice_id = '1';
                    $ordersDetails->buyer_id = $cart_items[$c]->buyer_id;
                    $ordersDetails->seller_id = $cart_items[$c]->seller_id;
                    $ordersDetails->buyer_quote_item_id = $cart_items[$c]->buyer_quote_item_id;
                    $ordersDetails->seller_post_item_id = $cart_items[$c]->seller_post_item_id;
                    $ordersDetails->lkp_packaging_type_id = $cart_items[$c]->lkp_packaging_type_id;
                    $ordersDetails->lkp_src_location_type_id = $cart_items[$c]->lkp_src_location_type_id;
                    $ordersDetails->lkp_dest_location_type_id = $cart_items[$c]->lkp_dest_location_type_id;
                    if (!empty($cart_items[$c]->buyer_quote_item_id) || $cart_items[$c]->buyer_quote_item_id != 0) {
                        $buyerQuoteItemData = BuyerComponent::getBuyerQuoteItemData($cart_items[$c]->buyer_quote_item_id);
                        $fromCityId = $buyerQuoteItemData[0]->from_city_id;
                        $toCityId = $buyerQuoteItemData[0]->to_city_id;
                        $ordersDetails->dispatch_date = $buyerQuoteItemData[0]->dispatch_date;
                        $loadTypeId = $buyerQuoteItemData[0]->lkp_load_type_id;
                        $vehicleTypeId = $buyerQuoteItemData[0]->lkp_vehicle_type_id;
                        $units = $buyerQuoteItemData[0]->units;

                        $ordersDetails->delivery_date = $buyerQuoteItemData[0]->delivery_date;
                        $ordersDetails->lkp_service_id = $buyerQuoteItemData[0]->lkp_service_id;
                        $ordersDetails->lkp_lead_type_id = $buyerQuoteItemData[0]->lkp_lead_type_id;
                        $ordersDetails->lkp_quote_access_id = $buyerQuoteItemData[0]->lkp_lead_type_id;
                        $ordersDetails->number_loads = $buyerQuoteItemData[0]->number_loads;
                        $ordersDetails->quantity = $buyerQuoteItemData[0]->quantity;
                    } else {
                        $sellerPostDetails = CheckoutComponent::getSellerPostDetails($cart_items[$c]->seller_post_item_id);
                        $fromCityId = $sellerPostDetails[0]->from_location_id;
                        $toCityId = $sellerPostDetails[0]->to_location_id;
                        $loadTypeId = $sellerPostDetails[0]->lkp_load_type_id;
                        $vehicleTypeId = $sellerPostDetails[0]->lkp_vehicle_type_id;
                        $units = $sellerPostDetails[0]->units;
                    }
                    $ordersDetails->from_city_id = $fromCityId;
                    $ordersDetails->to_city_id = $toCityId;
                    $ordersDetails->lkp_load_type_id = $loadTypeId;
                    $ordersDetails->lkp_vehicle_type_id = $vehicleTypeId;
                    $ordersDetails->units = $units;
                    $ordersDetails->price = $cart_items[$c]->price;
                    $ordersDetails->buyer_consignment_pick_up_date = $cart_items[$c]->buyer_consignment_pick_up_date;
                    $ordersDetails->buyer_consignment_value = $cart_items[$c]->buyer_consignment_value;
                    $ordersDetails->buyer_consignment_needs_insurance = $cart_items[$c]->buyer_consignment_needs_insurance;
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
                    $ordersDetails->seller_pickup_date = '';
                    $ordersDetails->seller_pickup_lr_number = '';
                    $ordersDetails->seller_pickup_lr_date = '';
                    $ordersDetails->seller_pickup_transport_bill_no = '';
                    $ordersDetails->seller_pickup_customer_doc_one = '';
                    $ordersDetails->seller_pickup_customer_doc_two = '';
                    $ordersDetails->seller_delivery_date = '';
                    $ordersDetails->seller_delivery_driver_name = '';
                    $ordersDetails->seller_delivery_recipient_mobile = '';
                    $ordersDetails->seller_delivery_frieght_amt = '';
                    $ordersDetails->seller_delivery_additional_details = '';
                    $ordersDetails->tracking_confirm = '0';
                    $ordersDetails->vehicle_confirm = '';
                    $ordersDetails->lkp_order_status_id = '1';
                    $ordersDetails->created_by = Auth::id();
                    $ordersDetails->created_at = date('Y-m-d H:i:s');
                    $ordersDetails->created_ip = $createdIp;
                    if ($ordersDetails->save()) {
                        CommonComponent::auditLog($ordersDetails->id, 'orders');
                        $emailData = DB::table('orders')
                                ->where('orders.id', $ordersDetails->id)
                                ->join('users', 'orders.buyer_id', '=', 'users.id')
                                ->select('orders.price', 'orders.order_no', 'users.username')
                                ->get();
                        $emailData[0]->email = Auth::User()->email;
                        $emailData[0]->order_service_tax_amount = $OrderServiceTaxes->order_service_tax_amount;
                        $emailData[0]->order_total_amount = $OrderServiceTaxes->order_total_amount;
                        CommonComponent::send_email(INVOICE_CONFIRMATION_MAIL, $emailData);
                        $buyerEmailData = DB::table('orders')
                                ->where('orders.id', $ordersDetails->id)
                                ->join('users', 'orders.seller_id', '=', 'users.id')
                                ->select('orders.price', 'orders.order_no', 'users.username', 'users.email')
                                ->get();
                        $buyerEmailData[0]->buyername = $emailData[0]->username;
                        CommonComponent::send_email(CHECKOUT_EMAIL_FOR_SELLER, $buyerEmailData);
                        DB::delete("delete from cart_items where buyer_id='" . $cart_items[$c]->buyer_id . "'");
                    }
                }
            }
			return array(
                        'orderServiceTax' => $OrderServiceTaxes, 
                        "cart_items_count" => $cartItemsCount, 
                        'order_total' => $orderTotalToDisplay
                    );
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}
}
