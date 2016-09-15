<?php namespace App\Components\Intracity;

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

use App\Models\LkpServiceCharges;

class IntracityCheckoutComponent{   
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
            ->leftjoin('seller_post_items','seller_post_items.id','=','cart_items.seller_post_item_id')
            ->leftjoin('lkp_cities as c1','seller_post_items.from_location_id','=','c1.id')
            ->leftjoin('lkp_cities as c2','seller_post_items.to_location_id','=','c2.id')
            ->leftjoin('lkp_vehicle_types as vt','seller_post_items.lkp_vehicle_type_id','=','vt.id')
            ->leftjoin('lkp_load_types as lt','seller_post_items.lkp_load_type_id','=','lt.id')
            ->where('cart_items.buyer_id',$buyerId)
            ->where('cart_items.buyer_quote_item_id',0)
            ->select('cart_items.id as id','users.username','vt.vehicle_type as vehicle_type','lt.load_type as load_type','c1.city_name as from_locationcity','c2.city_name as to_locationcity',DB::raw('"Not Specified" as dispatch_date') ,'cart_items.price', 'cart_items.*')//->toSql();
            ->get();
        //echo $cart_items_search;die;
        $cart_items = DB::table('cart_items')
            ->leftjoin('users','users.id','=','cart_items.seller_id')
            ->leftjoin('ict_buyer_quote_items','ict_buyer_quote_items.id','=','cart_items.buyer_quote_item_id')
            ->leftjoin('lkp_ict_locations as c1','ict_buyer_quote_items.from_location_id','=','c1.id')
            ->leftjoin('lkp_ict_locations as c2','ict_buyer_quote_items.to_location_id','=','c2.id')
            ->leftjoin('lkp_vehicle_types as vt','ict_buyer_quote_items.lkp_vehicle_type_id','=','vt.id')
            ->leftjoin('lkp_load_types as lt','ict_buyer_quote_items.lkp_load_type_id','=','lt.id')
            ->where('cart_items.buyer_id',$buyerId)
            ->where('cart_items.buyer_quote_item_id',"!=",0)
            ->select('cart_items.id as id','users.username','vt.vehicle_type as vehicle_type','lt.load_type as load_type','c1.ict_location_name as from_locationcity','c2.ict_location_name as to_locationcity','ict_buyer_quote_items.dispatch_date','cart_items.price', 'cart_items.*')
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
     * Get Cart Items Total
     * input : Buyer Id
     * Output : Cart Items Total
     */
    public static function getOrderTotal($buyerId){
        Log::info('Get Order Total: ',array('c'=>'2'));
        $total = DB::table('cart_items')
            ->select(DB::raw('sum(price) as ordertotal'))
            ->where('cart_items.buyer_id', '=', $buyerId)
            ->first();
        /*$query = DB::table('cart_items')
            ->select(DB::raw('sum(price) as ordertotal'))
            ->where('cart_items.buyer_id', '=', $buyerId)->toSql();*/
        return $total->ordertotal;
    }
    /**
     * Get Cart Item Payment Methods
     * input : Buyer Id
     * Output : Cart Items
     */
    public static function getCartItemPaymentMethods($buyerId){
        Log::info('Get Checkout Make Payment methods: ',array('c'=>'2'));
        
        $cartPaymentMethods = DB::table('cart_items')
            ->join('seller_post_items','seller_post_items.id','=','cart_items.seller_post_item_id')
            ->join('seller_posts','seller_posts.id','=','seller_post_items.seller_post_id')
            ->join('lkp_payment_modes','seller_posts.lkp_payment_mode_id','=','lkp_payment_modes.id')
            ->where('cart_items.buyer_id',$buyerId)
            ->select('seller_post_items.id', 'seller_posts.lkp_payment_mode_id', 'seller_posts.accept_payment_netbanking',
                    'seller_posts.accept_payment_credit', 'seller_posts.accept_payment_debit')
            ->get();
        return $cartPaymentMethods[0];
    }
    
    /**
     * Get Checkout service tax
     * input : $orderTotal, $lkpServiceId
     * Output : Cart Items
     */
    public static function getOrderServiceTax($orderTotal, $lkpServiceId){
        try {
            $serviceCharges = LkpServiceCharges::where ( array (
				'lkp_service_id' => $lkpServiceId,
				'is_active' => '1'
            ) )->first ();
            //$serviceCharges->order_service_tax_amount = $serviceCharges->service_tax_percent * ($orderTotal / 100);
            $serviceCharges->order_service_tax_amount = 0;
            $serviceCharges->order_total_amount = $orderTotal + $serviceCharges->order_service_tax_amount;
            return $serviceCharges;
        } catch (Exception $ex) {

        }
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
			$sellerData = DB::table('ict_seller_post_items as spi')
                        ->leftjoin('ict_seller_posts','ict_seller_posts.id','=','spi.seller_post_id')
			->where('spi.id',$sellerPostItemId)
			->select('ict_seller_posts.lkp_service_id','spi.from_location_id','spi.to_location_id','spi.lkp_load_type_id',
                    'spi.lkp_vehicle_type_id','spi.units','spi.lkp_city_id','spi.lkp_ict_rate_type_id')
			->get();
			return $sellerData;
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}
}
