<?php namespace App\Components;

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
use App\Models\BuyerQuoteItems;
use App\Models\PtlBuyerQuoteItem;
use App\Models\PtlBuyerQuote;
use App\Models\AirdomBuyerQuoteItem;
use App\Models\AirdomBuyerQuote;
use App\Models\RailBuyerQuoteItem;
use App\Models\RailBuyerQuote;
use App\Models\AirintBuyerQuote;
use App\Models\AirintBuyerQuoteItem;
use App\Models\OceanBuyerQuote;
use App\Models\OceanBuyerQuoteItem;
use App\Models\CourierBuyerQuoteItem;
use App\Models\CourierBuyerQuote;
use App\Models\RelocationBuyerPost;
use App\Models\RelocationintBuyerPost;
use App\Models\RelocationPetBuyerPost;

use App\Models\TruckhaulBuyerQuoteItem;
use App\Models\TruckhaulSellerPostItem;
use App\Models\TruckhaulSellerPost;
use App\Models\TruckleaseBuyerQuoteItem;

// Relocation Office Move  Models
use App\Models\RelocationofficeBuyerPost;
use App\Models\RelocationgmBuyerPost;

class CheckoutComponent{   
    /**
     * Get Cart Items
     * input : Buyer Id
     * Output : Cart Items
     */
    public static function getCartItems($buyerId){
        Log::info('Get Cart Items: ',array('c'=>'2'));       

        $cart_items =  DB::select( DB::raw("SELECT
        q.*       
        FROM
        view_cart_items q       
        where q.buyer_id ='".Auth::id ()."'"));
        
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
        $total = DB::table('view_cart_items')
            ->select(DB::raw('sum(price) as ordertotal'))
            ->where('view_cart_items.buyer_id', '=', $buyerId)
            ->first();        
        return $total->ordertotal;
    }
    /**
     * Get Cart Item Payment Methods
     * input : Buyer Id
     * Output : Cart Items
     */
    public static function getCartItemPaymentMethods($buyerId, $serviceId, $isContract = 0){
        Log::info('Get Checkout Make Payment methods: ',array('c'=>'2'));
        switch($serviceId){
                case ROAD_FTL       :  
                    if($isContract == NOT_CONTRACT) {
                        $cartPaymentMethods = DB::table('cart_items')
                            ->join('seller_post_items','seller_post_items.id','=','cart_items.seller_post_item_id')
                            ->join('seller_posts','seller_posts.id','=','seller_post_items.seller_post_id')
                            ->join('lkp_payment_modes','seller_posts.lkp_payment_mode_id','=','lkp_payment_modes.id')
                            ->where('cart_items.buyer_id',$buyerId)
                            ->select('seller_post_items.id', 'seller_posts.lkp_payment_mode_id', 'seller_posts.accept_payment_netbanking',
                                    'seller_posts.accept_payment_credit', 'seller_posts.accept_payment_debit')
                            ->get();
                    } else if($isContract == IS_CONTRACT){
                        $cartPaymentMethods = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.is_active',1)
                            ->where('lkp_payment_modes.id',4)
                            ->select('lkp_payment_modes.id as lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')
                            ->get(); 

                        
                    }

                    return $cartPaymentMethods[0];  
                    break;
                case ROAD_PTL       :

                    if($isContract == NOT_CONTRACT) {
                        $cartPaymentMethods = DB::table('cart_items')
                                        ->join('ptl_seller_post_items','ptl_seller_post_items.id','=','cart_items.seller_post_item_id')
                                        ->join('ptl_seller_posts','ptl_seller_posts.id','=','ptl_seller_post_items.seller_post_id')
                                        ->join('lkp_payment_modes','ptl_seller_posts.lkp_payment_mode_id','=','lkp_payment_modes.id')
                                        ->where('cart_items.buyer_id',$buyerId)
                                        ->select('ptl_seller_post_items.id', 'ptl_seller_posts.lkp_payment_mode_id', 'ptl_seller_posts.accept_payment_netbanking',
                                                'ptl_seller_posts.accept_payment_credit', 'ptl_seller_posts.accept_payment_debit')
                                        ->get();
                    }else if($isContract == IS_CONTRACT){
                    	$cartPaymentMethods = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.is_active',1)
                            ->where('lkp_payment_modes.id',4)
                            ->select('lkp_payment_modes.id as lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')
                            ->get();                      
                    }
                    return $cartPaymentMethods[0];
                    break;   
                case AIR_DOMESTIC       :
                    if($isContract == NOT_CONTRACT) {
                    $cartPaymentMethods = DB::table('cart_items')
                                        ->join('airdom_seller_post_items','airdom_seller_post_items.id','=','cart_items.seller_post_item_id')
                                        ->join('airdom_seller_posts','airdom_seller_posts.id','=','airdom_seller_post_items.seller_post_id')
                                        ->join('lkp_payment_modes','airdom_seller_posts.lkp_payment_mode_id','=','lkp_payment_modes.id')
                                        ->where('cart_items.buyer_id',$buyerId)
                                        ->select('airdom_seller_post_items.id', 'airdom_seller_posts.lkp_payment_mode_id', 'airdom_seller_posts.accept_payment_netbanking',
                                                'airdom_seller_posts.accept_payment_credit', 'airdom_seller_posts.accept_payment_debit')
                                        ->get();
                                     
                    }else if($isContract == IS_CONTRACT){
                        $cartPaymentMethods = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.is_active',1)
                            ->where('lkp_payment_modes.id',4)
                            ->select('lkp_payment_modes.id as lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')
                            ->get();                        
                    }return $cartPaymentMethods[0];
                                      break; 
                case RAIL       :
                    if($isContract == NOT_CONTRACT) {
                    $cartPaymentMethods = DB::table('cart_items')
                                    ->join('rail_seller_post_items','rail_seller_post_items.id','=','cart_items.seller_post_item_id')
                                    ->join('rail_seller_posts','rail_seller_posts.id','=','rail_seller_post_items.seller_post_id')
                                    ->join('lkp_payment_modes','rail_seller_posts.lkp_payment_mode_id','=','lkp_payment_modes.id')
                                    ->where('cart_items.buyer_id',$buyerId)
                                    ->select('rail_seller_post_items.id', 'rail_seller_posts.lkp_payment_mode_id', 'rail_seller_posts.accept_payment_netbanking',
                                            'rail_seller_posts.accept_payment_credit', 'rail_seller_posts.accept_payment_debit')
                                    ->get();
                    }else if($isContract == IS_CONTRACT){
                        $cartPaymentMethods = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.is_active',1)
                            ->where('lkp_payment_modes.id',4)
                            ->select('lkp_payment_modes.id as lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')
                            ->get();                          
                    }
                                 return $cartPaymentMethods[0];
                                  break;
                case AIR_INTERNATIONAL       :
                    if($isContract == NOT_CONTRACT) {
                        $cartPaymentMethods = DB::table('cart_items')
                                        ->join('airint_seller_post_items','airint_seller_post_items.id','=','cart_items.seller_post_item_id')
                                        ->join('airint_seller_posts','airint_seller_posts.id','=','airint_seller_post_items.seller_post_id')
                                        ->join('lkp_payment_modes','airint_seller_posts.lkp_payment_mode_id','=','lkp_payment_modes.id')
                                        ->where('cart_items.buyer_id',$buyerId)
                                        ->select('airint_seller_post_items.id', 'airint_seller_posts.lkp_payment_mode_id', 'airint_seller_posts.accept_payment_netbanking',
                                                'airint_seller_posts.accept_payment_credit', 'airint_seller_posts.accept_payment_debit')
                                        ->get();
                    }else if($isContract == IS_CONTRACT){
                       $cartPaymentMethods = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.is_active',1)
                            ->where('lkp_payment_modes.id',4)
                            ->select('lkp_payment_modes.id as lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')
                            ->get();                         
                    }
                                     return $cartPaymentMethods[0];

                                      break; 
                case OCEAN       :
                    if($isContract == NOT_CONTRACT) {
                        $cartPaymentMethods = DB::table('cart_items')
                                    ->join('ocean_seller_post_items','ocean_seller_post_items.id','=','cart_items.seller_post_item_id')
                                    ->join('ocean_seller_posts','ocean_seller_posts.id','=','ocean_seller_post_items.seller_post_id')
                                    ->join('lkp_payment_modes','ocean_seller_posts.lkp_payment_mode_id','=','lkp_payment_modes.id')
                                    ->where('cart_items.buyer_id',$buyerId)
                                    ->select('ocean_seller_post_items.id', 'ocean_seller_posts.lkp_payment_mode_id', 'ocean_seller_posts.accept_payment_netbanking',
                                            'ocean_seller_posts.accept_payment_credit', 'ocean_seller_posts.accept_payment_debit')
                                    ->get();
                    }else if($isContract == IS_CONTRACT){
                        $cartPaymentMethods = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.is_active',1)
                            ->where('lkp_payment_modes.id',4)
                            ->select('lkp_payment_modes.id as lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')
                            ->get();                          
                    }
                                 return $cartPaymentMethods[0];
                                  break; 
                case COURIER       :
                    if($isContract == NOT_CONTRACT) {
                        $cartPaymentMethods = DB::table('cart_items')
                                    ->join('courier_seller_post_items','courier_seller_post_items.id','=','cart_items.seller_post_item_id')
                                    ->join('courier_seller_posts','courier_seller_posts.id','=','courier_seller_post_items.seller_post_id')
                                    ->join('lkp_payment_modes','courier_seller_posts.lkp_payment_mode_id','=','lkp_payment_modes.id')
                                    ->where('cart_items.buyer_id',$buyerId)
                                    ->select('courier_seller_post_items.id', 'courier_seller_posts.lkp_payment_mode_id', 'courier_seller_posts.accept_payment_netbanking',
                                            'courier_seller_posts.accept_payment_credit', 'courier_seller_posts.accept_payment_debit')
                                    ->get();
                    }else if($isContract == IS_CONTRACT){
                       $cartPaymentMethods = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.is_active',1)
                            ->where('lkp_payment_modes.id',4)
                            ->select('lkp_payment_modes.id as lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')
                            ->get();                          
                    }
                                 return $cartPaymentMethods[0];
                                  break;  
                                  
                case RELOCATION_DOMESTIC:  
                    if($isContract == NOT_CONTRACT) {
                        $cartPaymentMethods = DB::table('cart_items')
                            ->leftjoin('relocation_seller_posts as sp','sp.id','=','cart_items.seller_post_item_id')
                            ->leftjoin('lkp_payment_modes','sp.lkp_payment_mode_id','=','lkp_payment_modes.id')
                            ->where('cart_items.buyer_id',$buyerId)
                            ->select('sp.id', 'sp.lkp_payment_mode_id', 'sp.accept_payment_netbanking',
                                    'sp.accept_payment_credit', 'sp.accept_payment_debit')
                            ->get();
                    } else if($isContract == IS_CONTRACT){
                       $cartPaymentMethods = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.is_active',1)
                            ->where('lkp_payment_modes.id',4)
                            ->select('lkp_payment_modes.id as lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')
                            ->get();                          
                    }

                    return $cartPaymentMethods[0];  
                    break;
                case RELOCATION_INTERNATIONAL:  
                    if($isContract == NOT_CONTRACT) {
                        $cartPaymentMethods = DB::table('cart_items')
                            ->leftjoin('relocationint_seller_posts as sp','sp.id','=','cart_items.seller_post_item_id')
                            ->leftjoin('lkp_payment_modes','sp.lkp_payment_mode_id','=','lkp_payment_modes.id')
                            ->where('cart_items.buyer_id',$buyerId)
                            ->select('sp.id', 'sp.lkp_payment_mode_id', 'sp.accept_payment_netbanking',
                                    'sp.accept_payment_credit', 'sp.accept_payment_debit')
                            ->get();
                    } else if($isContract == IS_CONTRACT){
                       $cartPaymentMethods = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.is_active',1)
                            ->where('lkp_payment_modes.id',4)
                            ->select('lkp_payment_modes.id as lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')
                            ->get();                          
                    }

                    return $cartPaymentMethods[0];  
                    break;
                case RELOCATION_OFFICE_MOVE:  
                    if($isContract == NOT_CONTRACT) {
                        $cartPaymentMethods = DB::table('cart_items')
                            ->leftjoin('relocationoffice_seller_posts as sp','sp.id','=','cart_items.seller_post_item_id')
                            ->leftjoin('lkp_payment_modes','sp.lkp_payment_mode_id','=','lkp_payment_modes.id')
                            ->where('cart_items.buyer_id',$buyerId)
                            ->select('sp.id', 'sp.lkp_payment_mode_id', 'sp.accept_payment_netbanking',
                                    'sp.accept_payment_credit', 'sp.accept_payment_debit')
                            ->get();
                    } else if($isContract == IS_CONTRACT){
                       $cartPaymentMethods = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.is_active',1)
                            ->where('lkp_payment_modes.id',4)
                            ->select('lkp_payment_modes.id as lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')
                            ->get();                          
                    }

                    return $cartPaymentMethods[0];  
                    break; 
                case RELOCATION_PET_MOVE:  
                        $cartPaymentMethods = DB::table('cart_items')
                            ->leftjoin('relocationpet_seller_posts as sp','sp.id','=','cart_items.seller_post_item_id')
                            ->leftjoin('lkp_payment_modes','sp.lkp_payment_mode_id','=','lkp_payment_modes.id')
                            ->where('cart_items.buyer_id',$buyerId)
                            ->select('sp.id', 'sp.lkp_payment_mode_id', 'sp.accept_payment_netbanking',
                                    'sp.accept_payment_credit', 'sp.accept_payment_debit')
                            ->get();
                    return $cartPaymentMethods[0];  
                    break;    
                    
                case ROAD_TRUCK_HAUL       :  
                    if($isContract == NOT_CONTRACT) {
                        $cartPaymentMethods = DB::table('cart_items')
                            ->join('truckhaul_seller_post_items','truckhaul_seller_post_items.id','=','cart_items.seller_post_item_id')
                            ->join('truckhaul_seller_posts','truckhaul_seller_posts.id','=','truckhaul_seller_post_items.seller_post_id')
                            ->join('lkp_payment_modes','truckhaul_seller_posts.lkp_payment_mode_id','=','lkp_payment_modes.id')
                            ->where('cart_items.buyer_id',$buyerId)
                            ->select('truckhaul_seller_post_items.id', 'truckhaul_seller_posts.lkp_payment_mode_id', 'truckhaul_seller_posts.accept_payment_netbanking',
                                    'truckhaul_seller_posts.accept_payment_credit', 'truckhaul_seller_posts.accept_payment_debit')
                            ->get();
                    } else if($isContract == IS_CONTRACT){
                       $cartPaymentMethods = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.is_active',1)
                            ->where('lkp_payment_modes.id',4)
                            ->select('lkp_payment_modes.id as lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')
                            ->get();                         
                    }

                    return $cartPaymentMethods[0];  
                    break;   
                case ROAD_TRUCK_LEASE       :  
                    if($isContract == NOT_CONTRACT) {
                        $cartPaymentMethods = DB::table('cart_items')
                            ->join('trucklease_seller_post_items as spi','spi.id','=','cart_items.seller_post_item_id')
                            ->join('trucklease_seller_posts as sp','sp.id','=','spi.seller_post_id')
                            ->join('lkp_payment_modes','sp.lkp_payment_mode_id','=','lkp_payment_modes.id')
                            ->where('cart_items.buyer_id',$buyerId)
                            ->select('spi.id', 'sp.lkp_payment_mode_id', 'sp.accept_payment_netbanking',
                                    'sp.accept_payment_credit', 'sp.accept_payment_debit')
                            ->get();
                    } else if($isContract == IS_CONTRACT){
                       $cartPaymentMethods = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.is_active',1)
                            ->where('lkp_payment_modes.id',4)
                            ->select('lkp_payment_modes.id as lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')
                            ->get();                         
                    }
                    return $cartPaymentMethods[0];  
                    break;  
                case RELOCATION_GLOBAL_MOBILITY:  
                	if($isContract == NOT_CONTRACT) {
                        $cartPaymentMethods = DB::table('cart_items')
                            ->leftjoin('relocationgm_seller_posts as sp','sp.id','=','cart_items.seller_post_item_id')
                            ->leftjoin('lkp_payment_modes','sp.lkp_payment_mode_id','=','lkp_payment_modes.id')
                            ->where('cart_items.buyer_id',$buyerId)
                            ->select('sp.id', 'sp.lkp_payment_mode_id', 'sp.accept_payment_netbanking',
                                    'sp.accept_payment_credit', 'sp.accept_payment_debit')
                            ->get();
                	}else if($isContract == IS_CONTRACT){
                       $cartPaymentMethods = DB::table('lkp_payment_modes')
                            ->where('lkp_payment_modes.is_active',1)
                            ->where('lkp_payment_modes.id',4)
                            ->select('lkp_payment_modes.id as lkp_payment_mode_id', 'lkp_payment_modes.payment_mode')
                            ->get();                         
                    }  
                    return $cartPaymentMethods[0];  
                    break;        
            }
            
            
        
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
            if(SHOW_SERVICE_TAX){
                if(CommonComponent::getServiceGroupID($lkpServiceId)==TRANSPORT){
                    $serviceCharges->order_service_tax_amount =   PERCENT14*((PERCENT40*($orderTotal))/10000);
                }elseif(CommonComponent::getServiceGroupID($lkpServiceId)==OTHERS){
                    $serviceCharges->order_service_tax_amount =  PERCENT14*($orderTotal/100);
                }
            }else{
                $serviceCharges->order_service_tax_amount =0.00;
            }
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
	public static function getSellerPostDetails($sellerPostItemId, $serviceId=null) {
		try {
			Log::info ('Get details for the seller: ' . Auth::id (), array ('c' => '2'));

            if(empty($serviceId)) {
            $serviceId = Session::get('service_id');
        }
        
        switch ($serviceId) {
            case ROAD_FTL :
               $sellerData = DB::table('seller_post_items as spi')
                            ->where('spi.id',$sellerPostItemId)
                            ->select('spi.from_location_id','spi.to_location_id','spi.lkp_load_type_id',
                                    'spi.lkp_vehicle_type_id','spi.units')
                            ->get();
                break;
            case ROAD_PTL : 
                $sellerData = DB::table('ptl_seller_post_items as spi')
                                ->where('spi.id',$sellerPostItemId)
                                ->select('spi.from_location_id','spi.to_location_id','spi.units')
                                ->get();
                break;
            case AIR_DOMESTIC : 
                $sellerData = DB::table('airdom_seller_post_items as spi')
                                ->where('spi.id',$sellerPostItemId)
                                ->select('spi.from_location_id','spi.to_location_id','spi.units')
                                ->get();
                break;
            case RAIL : 
                $sellerData = DB::table('rail_seller_post_items as spi')
                                ->where('spi.id',$sellerPostItemId)
                                ->select('spi.from_location_id','spi.to_location_id','spi.units')
                                ->get();
                break;
            case AIR_INTERNATIONAL : 
                $sellerData = DB::table('airint_seller_post_items as spi')
                                ->where('spi.id',$sellerPostItemId)
                                ->select('spi.from_location_id','spi.to_location_id','spi.units')
                                ->get();
                break;
            case OCEAN : 
                $sellerData = DB::table('ocean_seller_post_items as spi')
                                ->where('spi.id',$sellerPostItemId)
                                ->select('spi.from_location_id','spi.to_location_id','spi.units')
                                ->get();
                break;
            case COURIER : 
                $sellerData = DB::table('courier_seller_post_items as spi')
                                ->where('spi.id',$sellerPostItemId)
                                ->select('spi.from_location_id','spi.to_location_id','spi.units')
                                ->get();
                break;   
            case ROAD_INTRACITY :
                $buyerQuoteItemData = DB::table('ict_buyer_quote_items as bqi')
                        ->join('ict_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bqi.id', $buyerQuoteItemId)
                        ->select('bqi.*', 'bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                break;
            case RELOCATION_DOMESTIC:
               $sellerData = DB::table('relocation_seller_post_items as spi')
                            ->where('spi.id',$sellerPostItemId)
                            ->select('spi.from_location_id','spi.to_location_id','spi.lkp_load_type_id',
                                    'spi.lkp_vehicle_type_id','spi.units')
                            ->get();
                break;
            case RELOCATION_INTERNATIONAL:
               $sellerData = DB::table('relocationint_seller_posts as spi')
                            ->where('spi.id',$sellerPostItemId)
                            ->select('spi.from_location_id','spi.to_location_id','spi.units')
                            ->get();
                break;
            case RELOCATION_OFFICE_MOVE:
               $sellerData = DB::table('relocationoffice_seller_posts as spi')
                            ->where('spi.id',$sellerPostItemId)
                            ->select('spi.from_location_id')
                            ->get();
                break;
            case RELOCATION_GLOBAL_MOBILITY:
               $sellerData = DB::table('relocationgm_seller_posts as spi')
                            ->where('spi.id',$sellerPostItemId)
                            ->select('spi.location_id')
                            ->get();
                break;
            default :
                $buyerQuoteItemData = DB::table('buyer_quote_items as bqi')
                        ->join('buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bqi.id', $buyerQuoteItemId)
                        ->select('bqi.*', 'bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                break;
        }

        return $sellerData;			
			
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}

    /**
    * Make Payment Page
    * Method to update status for ftl
    * @param int $buyerQuoteItemId
    * @return array
    */
	public static function setBuyerQuoteStatusForPtl($buyerQuoteId, $serviceId=null ) {
		try {
			Log::info ('Update status id for PTL: ' . Auth::id (), array ('c' => '2'));
            //CommonComponent::activityLog("BUYER_CANCELED_ENQUIRY", BUYER_CANCELED_ENQUIRY, 0, HTTP_REFERRER, CURRENT_URL);

            $updatedAt = date('Y-m-d H:i:s');
            $updatedIp = $_SERVER['REMOTE_ADDR'];
            $updatedBy = Auth::User()->user_id;

            if(empty($serviceId)) {
            $serviceId = Session::get('service_id');
        }
        
        switch ($serviceId) {            
            case ROAD_PTL : 
                $data   =   DB::table('ptl_buyer_quote_items as bqi')
                    ->where('bqi.buyer_quote_id', $buyerQuoteId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                    PtlBuyerQuoteItem::where(["buyer_quote_id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    PtlBuyerQuote::where(["id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    CommonComponent::auditLog($buyerQuoteId, 'ptl_buyer_quote_items');
                }
            
            break;
            case AIR_DOMESTIC : 
                $data   =   DB::table('airdom_buyer_quote_items as bqi')
                    ->where('bqi.buyer_quote_id', $buyerQuoteId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                        AirDomBuyerQuoteItem::where(["buyer_quote_id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    AirDomBuyerQuote::where(["id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    CommonComponent::auditLog($buyerQuoteId, 'airdom_buyer_quote_items');
                }
            
            break;
            case RAIL : 
                $data   =   DB::table('rail_buyer_quote_items as bqi')
                    ->where('bqi.buyer_quote_id', $buyerQuoteId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                        RailBuyerQuoteItem::where(["buyer_quote_id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    RailBuyerQuote::where(["id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    CommonComponent::auditLog($buyerQuoteId, 'rail_buyer_quote_items');
                }
            break;
            case AIR_INTERNATIONAL : 
                $data   =   DB::table('airint_buyer_quote_items as bqi')
                    ->where('bqi.buyer_quote_id', $buyerQuoteId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                    AirIntBuyerQuoteItem::where(["buyer_quote_id" => $buyerQuoteId])
                        ->update(
                                array(
                                    'lkp_post_status_id' => BOOKED,
                                    'updated_at' => $updatedAt,
                                    'updated_ip' => $updatedIp,
                                    'updated_by' => $updatedBy
                                )
                    );
                    AirIntBuyerQuote::where(["id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    CommonComponent::auditLog($buyerQuoteId, 'airint_buyer_quote_items');
                }
            break;
            case OCEAN : 
                $data   =   DB::table('ocean_buyer_quote_items as bqi')
                    ->where('bqi.buyer_quote_id', $buyerQuoteId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                    OceanBuyerQuoteItem::where(["buyer_quote_id" => $buyerQuoteId])
                        ->update(
                                array(
                                    'lkp_post_status_id' => BOOKED,
                                    'updated_at' => $updatedAt,
                                    'updated_ip' => $updatedIp,
                                    'updated_by' => $updatedBy
                                )
                    );
                    OceanBuyerQuote::where(["id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    CommonComponent::auditLog($buyerQuoteId, 'ocean_buyer_quote_items');
                }
            break;
            case COURIER : 
                $data   =   DB::table('courier_buyer_quote_items as bqi')
                    ->where('bqi.buyer_quote_id', $buyerQuoteId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                    CourierBuyerQuoteItem::where(["buyer_quote_id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    CourierBuyerQuote::where(["id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    CommonComponent::auditLog($buyerQuoteId, 'courier_buyer_quote_items');
                }
            break;

            case ROAD_INTRACITY :
                $data   =   DB::table('ict_buyer_quote_items as bqi')
                    ->where('bqi.buyer_quote_id', $buyerQuoteId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                $buyerQuoteItemData = DB::table('ict_buyer_quote_items as bqi')
                        ->join('ict_buyer_quotes as bq', 'bq.id', '=', 'bqi.buyer_quote_id')
                        ->where('bqi.id', $buyerQuoteId)
                        ->select('bqi.*', 'bq.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                        ->get();
                }
                break;   
            
            case RELOCATION_DOMESTIC : 
                $data   =   DB::table('relocation_buyer_posts as bqi')
                    ->where('bqi.id', $buyerQuoteId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                    RelocationBuyerPost::where(["id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    CommonComponent::auditLog($buyerQuoteId, 'relocation_buyer_posts');
                }
            break;
            case RELOCATION_INTERNATIONAL : 
                $data   =   DB::table('relocationint_buyer_posts as bqi')
                    ->where('bqi.id', $buyerQuoteId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                    RelocationintBuyerPost::where(["id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    CommonComponent::auditLog($buyerQuoteId, 'relocationint_buyer_posts');
                }
            break;
            case RELOCATION_OFFICE_MOVE : 
                $data   =   DB::table('relocationoffice_buyer_posts as bqi')
                    ->where('bqi.id', $buyerQuoteId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                    RelocationOfficeBuyerPost::where(["id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    CommonComponent::auditLog($buyerQuoteId, 'relocationoffice_buyer_posts');
                }
            break;
            case RELOCATION_PET_MOVE : 
                $data   =   DB::table('relocationpet_buyer_posts as bqi')
                    ->where('bqi.id', $buyerQuoteId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                    RelocationPetBuyerPost::where(["id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    CommonComponent::auditLog($buyerQuoteId, 'relocationpet_buyer_posts');
                }
            break;
            case RELOCATION_GLOBAL_MOBILITY : 
                $data   =   DB::table('relocationgm_buyer_posts as bqi')
                    ->where('bqi.id', $buyerQuoteId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                    RelocationgmBuyerPost::where(["id" => $buyerQuoteId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    //CommonComponent::auditLog($buyerQuoteId, 'relocationgm_buyer_posts');
                }
            break;
        
        }
        return true;   
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}
    
    public static function setBuyerQuoteStatusForFtl($buyerQuoteItemId,$serviceId) {
		try {
			Log::info ('Update status id for FTL: ' . Auth::id (), array ('c' => '2'));
            //CommonComponent::activityLog("BUYER_CANCELED_ENQUIRY", BUYER_CANCELED_ENQUIRY, 0, HTTP_REFERRER, CURRENT_URL);

            $updatedAt = date('Y-m-d H:i:s');
            $updatedIp = $_SERVER['REMOTE_ADDR'];
            $updatedBy = Auth::User()->user_id;
            $data   =   DB::table('buyer_quote_items as bqi')
                    ->where('bqi.id', $buyerQuoteItemId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                    BuyerQuoteItems::where(["id" => $buyerQuoteItemId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    CommonComponent::auditLog($buyerQuoteItemId, 'buyer_quote_items');
                }
			return true;
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}

    public static function setBuyerQuoteStatusForTruckHaul($buyerQuoteItemId,$sellerPostItemId,$serviceId) {
        try {
            Log::info ('Update status id for Truck Haul: ' . Auth::id (), array ('c' => '2'));

            $updatedAt = date('Y-m-d H:i:s');
            $updatedIp = $_SERVER['REMOTE_ADDR'];
            $updatedBy = Auth::User()->user_id;
            $data   =   DB::table('truckhaul_buyer_quote_items as bqi')
                    ->where('bqi.id', $buyerQuoteItemId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                    TruckhaulBuyerQuoteItem::where(["id" => $buyerQuoteItemId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    TruckhaulSellerPostItem::where(["id" => $sellerPostItemId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => CLOSED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    $seller_post_data   =   DB::table('truckhaul_seller_post_items as si')
                    ->where('si.id', $sellerPostItemId)
                    ->select('si.seller_post_id')->first();
                    TruckhaulSellerPost::where(["id" => $seller_post_data->seller_post_id])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => CLOSED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );

                    CommonComponent::auditLog($buyerQuoteItemId, 'truckhaul_buyer_quote_items');
                    CommonComponent::auditLog($buyerQuoteItemId, 'truckhaul_seller_posts');
                    CommonComponent::auditLog($buyerQuoteItemId, 'truckhaul_seller_post_items');
                }
            return true;
        } catch ( Exception $exc ) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }
    
    
    public static function setBuyerQuoteStatusForTruckLease($buyerQuoteItemId,$serviceId) {
        try {
            Log::info ('Update status id for Truck lease: ' . Auth::id (), array ('c' => '2'));

            $updatedAt = date('Y-m-d H:i:s');
            $updatedIp = $_SERVER['REMOTE_ADDR'];
            $updatedBy = Auth::User()->user_id;
            $data   =   DB::table('trucklease_buyer_quote_items as bqi')
                    ->where('bqi.id', $buyerQuoteItemId)
                    ->select('bqi.lkp_post_status_id')->first();
                if($data->lkp_post_status_id!=ORDERED){
                    TruckleaseBuyerQuoteItem::where(["id" => $buyerQuoteItemId])
                            ->update(
                                    array(
                                        'lkp_post_status_id' => BOOKED,
                                        'updated_at' => $updatedAt,
                                        'updated_ip' => $updatedIp,
                                        'updated_by' => $updatedBy
                                    )
                    );
                    
                    //CommonComponent::auditLog($buyerQuoteItemId, 'trucklease_buyer_quote_items');
                }
            return true;
        } catch ( Exception $exc ) {
            // echo $exc->getTraceAsString();
            // TODO:: Log the error somewhere
        }
    }
	
  /**
   * 
   * @param unknown $id
   * @param unknown $serviceId
   * @return unknown
   * Invoice Genration for Buyer
   */	
  public static function getBuyerInvoice($id,$serviceId){
    $all_services = CommonComponent::getAllServices($serviceId);
  	switch ($serviceId) {
  		case ROAD_FTL :
  			$data= DB::table('orders');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('seller_details as sd', 'sd.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
                        $data->leftjoin('buyer_business_details', 'buyer_business_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
  			$data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_cities as to_city', 'to_city.id', '=', 'orders.to_city_id');
  	
  			$data->where('orders.id', $id);
  			$data->select('orders.lkp_service_id','orders.quantity','orders.price','orders.order_no',
                                DB::raw("u.username as name"),
                                DB::raw("IF(buyer_business_details.address != '', buyer_business_details.address, buyer_details.address) as address"),
                                
                                'order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
  				DB::raw("IF(sellers.tin != '', sellers.tin, sd.tin) as tin"),
                                DB::raw("IF(sellers.service_tax_number != '', sellers.service_tax_number, sd.service_tax_number) as service_tax_number"),
                                
                                'order_invoices.invoice_no','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.city_name as city_from','to_city.city_name as city_to');
  			$invoiceData = $data->get();

  			//$html = view('pdf.invoice_ftl')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;
  		case ROAD_PTL :
  			
  			$data= DB::table('orders');
                        $data->leftjoin('ptl_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'orders.buyer_quote_id');
  			$data->leftjoin('lkp_packaging_types as lpt', 'lpt.id', '=', 'bqi.lkp_packaging_type_id');
                        $data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('seller_details as sd', 'sd.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
  			//$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
  			$data->leftjoin('lkp_ptl_pincodes as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_ptl_pincodes as to_city', 'to_city.id', '=', 'orders.to_city_id');
  	
  			$data->where('orders.id', $id);
  			$data->select('lpt.packaging_type_name','orders.units','orders.price','orders.order_no',
                                DB::raw("u.username as name"),
                                DB::raw("IF(sellers.address != '', sellers.address, sd.address) as address"),
                                
                                'order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
  				DB::raw("IF(sellers.tin != '', sellers.tin, sd.tin) as tin"),
                                DB::raw("IF(sellers.service_tax_number != '', sellers.service_tax_number, sd.service_tax_number) as service_tax_number"),
                                'order_invoices.invoice_no','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_load_types.load_type','from_city.postoffice_name as city_from','to_city.postoffice_name as city_to', 'bqi.lkp_ptl_length_uom_id');
  			$invoiceData = $data->get();
                        //echo "<pre>";print_r($invoiceData);exit;
  			//$html = view('pdf.invoice_ptl')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			// $html = 'pdf.invoice_ptl';
  			break;
  		case RAIL :
  			
  			$data= DB::table('orders');
                        $data->leftjoin('rail_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'orders.buyer_quote_id');
  			$data->leftjoin('lkp_packaging_types as lpt', 'lpt.id', '=', 'bqi.lkp_packaging_type_id');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('seller_details as sd', 'sd.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
  			$data->leftjoin('lkp_ptl_pincodes as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_ptl_pincodes as to_city', 'to_city.id', '=', 'orders.to_city_id');
  	
  			$data->where('orders.id', $id);
  			$data->select('lpt.packaging_type_name','orders.units','orders.price','orders.order_no',
                                DB::raw("u.username as name"),
                                DB::raw("IF(sellers.address != '', sellers.address, sd.address) as address"),
                                'order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
  				DB::raw("IF(sellers.tin != '', sellers.tin, sd.tin) as tin"),
                                DB::raw("IF(sellers.service_tax_number != '', sellers.service_tax_number, sd.service_tax_number) as service_tax_number"),
                                'order_invoices.invoice_no','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.postoffice_name as city_from','to_city.postoffice_name as city_to', 'bqi.lkp_ptl_length_uom_id');
  			$invoiceData = $data->get();
  	
  			//$html = view('pdf.invoice_rail')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;
  		case AIR_DOMESTIC :
  			
  			$data= DB::table('orders');
                        $data->leftjoin('airdom_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'orders.buyer_quote_id');
  			$data->leftjoin('lkp_packaging_types as lpt', 'lpt.id', '=', 'bqi.lkp_packaging_type_id');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('seller_details as sd', 'sd.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
  			$data->leftjoin('lkp_ptl_pincodes as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_ptl_pincodes as to_city', 'to_city.id', '=', 'orders.to_city_id');
  	
  			$data->where('orders.id', $id);
  			$data->select('lpt.packaging_type_name','orders.units','orders.price','orders.order_no',
                                DB::raw("u.username as name"),
                                DB::raw("IF(sellers.address != '', sellers.address, sd.address) as address"),
                                'order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
  				DB::raw("IF(sellers.tin != '', sellers.tin, sd.tin) as tin"),
                                DB::raw("IF(sellers.service_tax_number != '', sellers.service_tax_number, sd.service_tax_number) as service_tax_number"),
                                'order_invoices.invoice_no','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.postoffice_name as city_from','to_city.postoffice_name as city_to', 'bqi.lkp_ptl_length_uom_id');
  			$invoiceData = $data->get();
  	
  			//$html = view('pdf.invoice_airdomestic')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;
  		case OCEAN :
  			
  			$data= DB::table('orders');
                        $data->leftjoin('ocean_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'orders.buyer_quote_id');
  			$data->leftjoin('lkp_packaging_types as lpt', 'lpt.id', '=', 'bqi.lkp_packaging_type_id');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('seller_details as sd', 'sd.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
  			$data->leftjoin('lkp_seaports as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_seaports as to_city', 'to_city.id', '=', 'orders.to_city_id');
  	
  			$data->where('orders.id', $id);
  			$data->select('lpt.packaging_type_name','orders.units','orders.price','orders.order_no',
                                DB::raw("u.username as name"),
                                DB::raw("IF(sellers.address != '', sellers.address, sd.address) as address"),
                                'order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
  				DB::raw("IF(sellers.tin != '', sellers.tin, sd.tin) as tin"),
                                DB::raw("IF(sellers.service_tax_number != '', sellers.service_tax_number, sd.service_tax_number) as service_tax_number"),
                                'order_invoices.invoice_no','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.seaport_name as city_from','to_city.seaport_name as city_to', 'bqi.lkp_ptl_length_uom_id');
  			$invoiceData = $data->get();
  	
  			//$html = view('pdf.invoice_ocean')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;
  		case AIR_INTERNATIONAL :
  			
  			$data= DB::table('orders');
                        $data->leftjoin('airint_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'orders.buyer_quote_id');
  			$data->leftjoin('lkp_packaging_types as lpt', 'lpt.id', '=', 'bqi.lkp_packaging_type_id');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('seller_details as sd', 'sd.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
  			$data->leftjoin('lkp_airports as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_airports as to_city', 'to_city.id', '=', 'orders.to_city_id');
  	
  			$data->where('orders.id', $id);
  			$data->select('lpt.packaging_type_name','orders.units','orders.price','orders.order_no',
                                DB::raw("u.username as name"),
                                DB::raw("IF(sellers.address != '', sellers.address, sd.address) as address"),
                                'order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
  				DB::raw("IF(sellers.tin != '', sellers.tin, sd.tin) as tin"),
                                DB::raw("IF(sellers.service_tax_number != '', sellers.service_tax_number, sd.service_tax_number) as service_tax_number"),
                                'order_invoices.invoice_no','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.airport_name as city_from','to_city.airport_name as city_to', 'bqi.lkp_ptl_length_uom_id');
  			$invoiceData = $data->get();
  	
  			//$html = view('pdf.invoice_airinternational')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;
  		case ROAD_INTRACITY :
  			$randString = 'INTRA/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
  			$inv->invoice_no = $randString;
  			break;
        case COURIER :
            
            $data= DB::table('orders');
            $data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
            $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
            $data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
            $data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
            $data->leftjoin('courier_buyer_quotes', 'courier_buyer_quotes.id', '=', 'orders.buyer_quote_id');
            $data->leftjoin('courier_buyer_quote_items', 'courier_buyer_quote_items.buyer_quote_id', '=', 'courier_buyer_quotes.id');
            $data->leftjoin('lkp_courier_delivery_types', 'lkp_courier_delivery_types.id', '=', 'courier_buyer_quote_items.lkp_courier_delivery_type_id');
            $data->leftjoin('lkp_courier_types', 'lkp_courier_types.id', '=', 'courier_buyer_quote_items.lkp_courier_type_id');
            $data->leftjoin('lkp_ptl_pincodes as from_city', 'from_city.id', '=', 'orders.from_city_id');

            $data->leftJoin('lkp_ptl_pincodes as c2', function($join)
             {
                 $join->on('orders.to_city_id', '=', 'c2.id');
                 $join->on(DB::raw('courier_buyer_quote_items.lkp_courier_delivery_type_id'),'=',DB::raw(1));
                
             });
             $data->leftJoin('lkp_countries as cc2', function($join)
             {
                 $join->on('orders.to_city_id', '=', 'cc2.id');
                 $join->on(DB::raw('courier_buyer_quote_items.lkp_courier_delivery_type_id'),'=',DB::raw(2));
                
             });
    
            $data->where('orders.id', $id);
            $data->select('orders.price','orders.order_no','u.username as name', 'sellers.address','order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
                    'sellers.tin','sellers.service_tax_number','order_invoices.invoice_no','orders.buyer_consignor_address','orders.buyer_consignee_address',
                    'from_city.postoffice_name as city_from','lkp_courier_delivery_types.courier_delivery_type as courier_delivery_type','lkp_courier_types.courier_type as courier_type',
                    DB::raw("(case when courier_buyer_quote_items.lkp_courier_delivery_type_id = 1 then c2.postoffice_name when courier_buyer_quote_items.lkp_courier_delivery_type_id = 2 then cc2.country_name end) as city_to,orders.units,courier_buyer_quote_items.lkp_ict_weight_uom_id") );
            $invoiceData = $data->get(); 
    /*echo "<pre>";
    print_r($invoiceData);
    exit;*/
            //$html = view('pdf.invoice_courier')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

            break;
        case RELOCATION_DOMESTIC :
  			
  			$data= DB::table('orders');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
  			$data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_cities as to_city', 'to_city.id', '=', 'orders.to_city_id');
  			$data->where('orders.id', $id);
  			$data->select('orders.price','orders.order_no','u.username as name', 'sellers.address','order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
  					'sellers.tin','sellers.service_tax_number','order_invoices.invoice_no','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.city_name as city_from','to_city.city_name as city_to');
  			$invoiceData = $data->get();

  			//$html = view('pdf.invoice_relocation')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;
        case RELOCATION_INTERNATIONAL :
            
            $data= DB::table('orders');
            $data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
            $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
            $data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
            $data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
            $data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
            $data->leftjoin('lkp_cities as to_city', 'to_city.id', '=', 'orders.to_city_id');
            $data->where('orders.id', $id);
            $data->select('orders.price','orders.order_no','u.username as name', 'sellers.address','order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
                    'sellers.tin','sellers.service_tax_number','order_invoices.invoice_no','orders.buyer_consignor_address','orders.buyer_consignee_address',
                    'from_city.city_name as city_from','to_city.city_name as city_to');
            $invoiceData = $data->get();
            
            //$html = view('pdf.invoice_relocation_international')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

            break;
        case RELOCATION_OFFICE_MOVE :
            
            $data= DB::table('orders');
            $data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
            $data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
            $data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
            $data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
            $data->where('orders.id', $id);
            $data->select('orders.price','orders.order_no','u.username as name', 'sellers.address','order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
                    'sellers.tin','sellers.service_tax_number','order_invoices.invoice_no','orders.buyer_consignor_address','orders.buyer_consignee_address','from_city.city_name as city_from');
            $invoiceData = $data->get();
            
            //$html = view('pdf.invoice_relocation_office')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

            break;   
            case RELOCATION_PET_MOVE :
  			
  			$data= DB::table('orders');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('relocationpet_buyer_posts as rbp', 'rbp.id', '=', 'orders.buyer_quote_id');
                        $data->leftjoin('lkp_pet_types', 'lkp_pet_types.id', '=', 'rbp.lkp_pet_type_id');
                        $data->leftjoin('lkp_breed_types', 'lkp_breed_types.id', '=', 'rbp.lkp_breed_type_id');
                        $data->leftjoin('lkp_cage_types', 'lkp_cage_types.id', '=', 'rbp.lkp_cage_type_id');$data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_cities as to_city', 'to_city.id', '=', 'orders.to_city_id');
  			$data->where('orders.id', $id);
  			$data->select('orders.price','orders.order_no','u.username as name', 'sellers.address','order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
  					'sellers.tin','sellers.service_tax_number','order_invoices.invoice_no','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_pet_types.pet_type','lkp_breed_types.breed_type','lkp_cage_types.cage_type','lkp_cage_types.cage_weight','from_city.city_name as city_from','to_city.city_name as city_to');
  			$invoiceData = $data->get();
  			
  			//$html = view('pdf.invoice_relocation_pet')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;
                    
            case RELOCATION_GLOBAL_MOBILITY :
  			
  			$data= DB::table('orders');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('relocationgm_buyer_posts as rbp', 'rbp.id', '=', 'orders.buyer_quote_id');
                        //$data->leftjoin('lkp_pet_types', 'lkp_pet_types.id', '=', 'rbp.lkp_pet_type_id');
                        $data->leftjoin('lkp_cities as to_city', 'to_city.id', '=', 'orders.to_city_id');
  			$data->where('orders.id', $id);
  			$data->select('orders.price','orders.order_no','u.username as name', 'sellers.address','order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
  					'sellers.tin','sellers.service_tax_number','order_invoices.invoice_no','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'to_city.city_name as city_to');
  			$invoiceData = $data->get();
  			
  			//$html = view('pdf.invoice_relocation_pet')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();
  			break;        
            case ROAD_TRUCK_HAUL :	   
  			$data= DB::table('orders');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('seller_details as sd', 'sd.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
  			$data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_cities as to_city', 'to_city.id', '=', 'orders.to_city_id');
  	
  			$data->where('orders.id', $id);
  			$data->select('orders.lkp_service_id','orders.quantity','orders.price','orders.order_no',
                                DB::raw("u.username as name"),
                                DB::raw("IF(sellers.address != '', sellers.address, sd.address) as address"),
                                
                                'order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
  				DB::raw("IF(sellers.tin != '', sellers.tin, sd.tin) as tin"),
                                DB::raw("IF(sellers.service_tax_number != '', sellers.service_tax_number, sd.service_tax_number) as service_tax_number"),
                                
                                'order_invoices.invoice_no','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.city_name as city_from','to_city.city_name as city_to');
  			$invoiceData = $data->get();

  			
  			//$html = view('pdf.invoice_th')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;        
                    
            case ROAD_TRUCK_LEASE :	    
  			$data= DB::table('orders');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('seller_details as sd', 'sd.user_id', '=', 'orders.seller_id');
                        $data->leftjoin('users as u', 'u.id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('order_invoices', 'order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->where('orders.id', $id);
  			$data->select('orders.lkp_service_id','orders.price','orders.order_no',
                                DB::raw("u.username as name"),
                                DB::raw("IF(sellers.address != '', sellers.address, sd.address) as address"),
                                'order_invoices.invoice_no','order_invoices.service_tax_amount','order_invoices.total_amt',
  				DB::raw("IF(sellers.tin != '', sellers.tin, sd.tin) as tin"),
                                DB::raw("IF(sellers.service_tax_number != '', sellers.service_tax_number, sd.service_tax_number) as service_tax_number"),
                                'order_invoices.invoice_no','orders.buyer_consignor_address',
  					'lkp_vehicle_types.vehicle_type','from_city.city_name as city_from');
  			$invoiceData = $data->get();
  			
  			//$html = view('pdf.invoice_tl')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;        
  	}
  	
  	return $html;
  	
  }	
  
  
  /**
   *
   * @param unknown $id
   * @param unknown $serviceId
   * @return unknown
   * Invoice Genration for Buyer
   */
  public static function getSellerInvoice($id,$serviceId){

    $all_services = CommonComponent::getAllServices($serviceId);
  	 
  	switch ($serviceId) {
  		case ROAD_FTL :
  				
  			 $data= DB::table('orders');
             $data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
             $data->leftjoin('seller_details as sd', 'sd.user_id', '=', 'orders.seller_id');
             $data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
             $data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
             $data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
             $data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
             $data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
             $data->leftjoin('lkp_cities as to_city', 'to_city.id', '=', 'orders.to_city_id');
                            
             $data->where('orders.id', $id);
             $data->select('orders.lkp_service_id','orders.quantity','seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name', 
                     DB::raw("IF(sellers.address != '', sellers.address, sd.address) as address"),
                     'seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
                            		'sellers.tin','orders.buyer_consignor_address','orders.buyer_consignee_address',
                            		'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.city_name as city_from','to_city.city_name as city_to');
             $invoiceData = $data->get();
                            
            
            //$html = view('pdf.invoice_ftl')->with(['invoiceData' => $invoiceData,'service_title' => "FTL",'service_id'=>$serviceId])->render();
            
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;
  		case ROAD_PTL :
  				
  			$data= DB::table('orders');
                        $data->leftjoin('ptl_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'orders.buyer_quote_id');
  			$data->leftjoin('lkp_packaging_types as lpt', 'lpt.id', '=', 'bqi.lkp_packaging_type_id');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
  			$data->leftjoin('lkp_ptl_pincodes as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_ptl_pincodes as to_city', 'to_city.id', '=', 'orders.to_city_id');
  			 
  			$data->where('orders.id', $id);
  			$data->select('lpt.packaging_type_name','orders.units','seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name','sellers.address', 'seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
  					'sellers.tin','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.postoffice_name as city_from','to_city.postoffice_name as city_to',  'bqi.lkp_ptl_length_uom_id');
  			$invoiceData = $data->get();
  			 
  			//$html = view('pdf.invoice_ptl')->with(['invoiceData' => $invoiceData,'service_title' => "LTL",'service_id'=>$serviceId])->render();
  			// $html = 'pdf.invoice_ptl';

            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;
  		case RAIL :
  				
  			$data= DB::table('orders');
                        $data->leftjoin('rail_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'orders.buyer_quote_id');
  			$data->leftjoin('lkp_packaging_types as lpt', 'lpt.id', '=', 'bqi.lkp_packaging_type_id');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
  			$data->leftjoin('lkp_ptl_pincodes as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_ptl_pincodes as to_city', 'to_city.id', '=', 'orders.to_city_id');
  			 
  			$data->where('orders.id', $id);
  			$data->select('lpt.packaging_type_name','orders.units','seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name', 'sellers.address','seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
  					'sellers.tin','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.postoffice_name as city_from','to_city.postoffice_name as city_to', 'bqi.lkp_ptl_length_uom_id');
  			$invoiceData = $data->get();
  			 
  			//$html = view('pdf.invoice_rail')->with(['invoiceData' => $invoiceData,'service_title' => "RAIL",'service_id'=>$serviceId])->render();

            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;
  		case AIR_DOMESTIC :
  				
  			$data= DB::table('orders');
                        $data->leftjoin('airdom_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'orders.buyer_quote_id');
  			$data->leftjoin('lkp_packaging_types as lpt', 'lpt.id', '=', 'bqi.lkp_packaging_type_id');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
  			$data->leftjoin('lkp_ptl_pincodes as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_ptl_pincodes as to_city', 'to_city.id', '=', 'orders.to_city_id');
  			 
  			$data->where('orders.id', $id);
  			$data->select('lpt.packaging_type_name','orders.units','seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name', 'sellers.address','seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
  					'sellers.tin','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.postoffice_name as city_from','to_city.postoffice_name as city_to', 'bqi.lkp_ptl_length_uom_id');
  			$invoiceData = $data->get();
  			 
  			//$html = view('pdf.invoice_airdomestic')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;
  		case OCEAN :
  				
  			$data= DB::table('orders');
                        $data->leftjoin('ocean_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'orders.buyer_quote_id');
  			$data->leftjoin('lkp_packaging_types as lpt', 'lpt.id', '=', 'bqi.lkp_packaging_type_id');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
  			$data->leftjoin('lkp_seaports as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_seaports as to_city', 'to_city.id', '=', 'orders.to_city_id');
  			 
  			$data->where('orders.id', $id);
  			$data->select('lpt.packaging_type_name','orders.units','seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name', 'sellers.address','seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
  					'sellers.tin','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.seaport_name as city_from','to_city.seaport_name as city_to', 'bqi.lkp_ptl_length_uom_id');
  			$invoiceData = $data->get();
  			 
  			//$html = view('pdf.invoice_ocean')->with(['invoiceData' => $invoiceData])->render();

            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;
  		case AIR_INTERNATIONAL :
  				
  			$data= DB::table('orders');
            $data->leftjoin('airint_buyer_quote_items as bqi', 'bqi.buyer_quote_id', '=', 'orders.buyer_quote_id');
  			$data->leftjoin('lkp_packaging_types as lpt', 'lpt.id', '=', 'bqi.lkp_packaging_type_id');
  			$data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
  			$data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
  			$data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
  			$data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
  			$data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
  			$data->leftjoin('lkp_airports as from_city', 'from_city.id', '=', 'orders.from_city_id');
  			$data->leftjoin('lkp_airports as to_city', 'to_city.id', '=', 'orders.to_city_id');
  			 
  			$data->where('orders.id', $id);
  			$data->select('lpt.packaging_type_name','orders.units','seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name', 'sellers.address','seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
  					'sellers.tin','orders.buyer_consignor_address','orders.buyer_consignee_address',
  					'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.airport_name as city_from','to_city.airport_name as city_to', 'bqi.lkp_ptl_length_uom_id');
  			$invoiceData = $data->get();
  			 
  			//$html = view('pdf.invoice_airinternational')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

  			break;
  		case ROAD_INTRACITY :
  			$randString = 'INTRA/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
  			$inv->invoice_no = $randString;
  			break;
        case COURIER :
            $data= DB::table('orders');
            $data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
            $data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
            $data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
            $data->leftjoin('courier_buyer_quotes', 'courier_buyer_quotes.id', '=', 'orders.buyer_quote_id');
            $data->leftjoin('courier_buyer_quote_items', 'courier_buyer_quote_items.buyer_quote_id', '=', 'courier_buyer_quotes.id');
            $data->leftjoin('lkp_courier_delivery_types', 'lkp_courier_delivery_types.id', '=', 'courier_buyer_quote_items.lkp_courier_delivery_type_id');
            $data->leftjoin('lkp_courier_types', 'lkp_courier_types.id', '=', 'courier_buyer_quote_items.lkp_courier_type_id');
            $data->leftjoin('lkp_ptl_pincodes as from_city', 'from_city.id', '=', 'orders.from_city_id');

            $data->leftJoin('lkp_ptl_pincodes as c2', function($join)
             {
                 $join->on('orders.to_city_id', '=', 'c2.id');
                 $join->on(DB::raw('courier_buyer_quote_items.lkp_courier_delivery_type_id'),'=',DB::raw(1));
                
             });
             $data->leftJoin('lkp_countries as cc2', function($join)
             {
                 $join->on('orders.to_city_id', '=', 'cc2.id');
                 $join->on(DB::raw('courier_buyer_quote_items.lkp_courier_delivery_type_id'),'=',DB::raw(2));
                
             });
    
            $data->where('orders.id', $id);
            $data->select('seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name', 'sellers.address','seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
                    'sellers.tin','orders.buyer_consignor_address','orders.buyer_consignee_address',
                    'from_city.postoffice_name as city_from','lkp_courier_delivery_types.courier_delivery_type as courier_delivery_type','lkp_courier_types.courier_type as courier_type',
                    DB::raw("(case when courier_buyer_quote_items.lkp_courier_delivery_type_id = 1 then c2.postoffice_name when courier_buyer_quote_items.lkp_courier_delivery_type_id = 2 then cc2.country_name end) as city_to,orders.units,courier_buyer_quote_items.lkp_ict_weight_uom_id") );
            $invoiceData = $data->get();
           
             
            //$html = view('pdf.invoice_courier')->with(['invoiceData' => $invoiceData])->render();
            // $html = 'pdf.invoice_ptl';
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

            break;
        case RELOCATION_DOMESTIC :
  				
            $data= DB::table('orders');
            $data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
            $data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
            $data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
            $data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
            $data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
            $data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
            $data->leftjoin('lkp_cities as to_city', 'to_city.id', '=', 'orders.to_city_id');

            $data->where('orders.id', $id);
            $data->select('seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name', 'sellers.address','seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
                                       'sellers.tin','orders.buyer_consignor_address','orders.buyer_consignee_address',
                                       'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.city_name as city_from','to_city.city_name as city_to');
            $invoiceData = $data->get();
            //$html = view('pdf.invoice_relocation')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();


            break;
        case RELOCATION_INTERNATIONAL :
                
            $data= DB::table('orders');
            $data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
            $data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
            $data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
            $data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
            $data->leftjoin('lkp_cities as to_city', 'to_city.id', '=', 'orders.to_city_id');

            $data->where('orders.id', $id);
            $data->select('seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name', 'sellers.address','seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
                                       'sellers.tin','orders.buyer_consignor_address','orders.buyer_consignee_address',
                                       'from_city.city_name as city_from','to_city.city_name as city_to');
            $invoiceData = $data->get();
            //$html = view('pdf.invoice_relocation_international')->with(['invoiceData' => $invoiceData])->render();

            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

            
            break;
        case RELOCATION_OFFICE_MOVE :
                
            $data= DB::table('orders');
            $data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
            $data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
            $data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
            $data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');

            $data->where('orders.id', $id);
            $data->select('seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name', 'sellers.address','seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
                                       'sellers.tin','orders.buyer_consignor_address','orders.buyer_consignee_address',
                                       'from_city.city_name as city_from');
            $invoiceData = $data->get();
            //$html = view('pdf.invoice_relocation_office')->with(['invoiceData' => $invoiceData])->render();

            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

            break;
        case RELOCATION_PET_MOVE :
  				
            $data= DB::table('orders');
            $data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
            $data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
            $data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
            $data->leftjoin('relocationpet_buyer_posts as rbp', 'rbp.id', '=', 'orders.buyer_quote_id');
            $data->leftjoin('lkp_pet_types', 'lkp_pet_types.id', '=', 'rbp.lkp_pet_type_id');
            $data->leftjoin('lkp_breed_types', 'lkp_breed_types.id', '=', 'rbp.lkp_breed_type_id');
            $data->leftjoin('lkp_cage_types', 'lkp_cage_types.id', '=', 'rbp.lkp_cage_type_id');
            $data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
            $data->leftjoin('lkp_cities as to_city', 'to_city.id', '=', 'orders.to_city_id');

            $data->where('orders.id', $id);
            $data->select('seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name', 'sellers.address','seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
                                       'sellers.tin','orders.buyer_consignor_address','orders.buyer_consignee_address',
                                       'lkp_pet_types.pet_type','lkp_breed_types.breed_type','lkp_cage_types.cage_type','lkp_cage_types.cage_weight','from_city.city_name as city_from','to_city.city_name as city_to');
            $invoiceData = $data->get();
            //$html = view('pdf.invoice_relocation_pet')->with(['invoiceData' => $invoiceData])->render();

            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();

            break;
        
        case ROAD_TRUCK_HAUL :			
  			 $data= DB::table('orders');
             $data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
             $data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
             $data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
             $data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
             $data->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'orders.lkp_load_type_id');
             $data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
             $data->leftjoin('lkp_cities as to_city', 'to_city.id', '=', 'orders.to_city_id');
                            
             $data->where('orders.id', $id);
             $data->select('orders.lkp_service_id','orders.quantity','seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name', 'sellers.address','seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
                            		'sellers.tin','orders.buyer_consignor_address','orders.buyer_consignee_address',
                            		'lkp_load_types.load_type','lkp_vehicle_types.vehicle_type','from_city.city_name as city_from','to_city.city_name as city_to');
             $invoiceData = $data->get();
             
             // $html = view('pdf.invoice_ftl',array('invoiceData' => $invoiceData));
             //$html = view('pdf.invoice_th')->with(['invoiceData' => $invoiceData])->render();

            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();
  			//echo $html;

  			break;
        case ROAD_TRUCK_LEASE :			
  			 $data= DB::table('orders');
             $data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
             $data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
             $data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
             $data->leftjoin('lkp_vehicle_types', 'lkp_vehicle_types.id', '=', 'orders.lkp_vehicle_type_id');
             $data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
                           
             $data->where('orders.id', $id);
             $data->select('orders.lkp_service_id','seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name', 'sellers.address','seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
                            		'sellers.tin','orders.buyer_consignor_address',
                            		'lkp_vehicle_types.vehicle_type','from_city.city_name as city_from');
             $invoiceData = $data->get();
             // $html = view('pdf.invoice_ftl',array('invoiceData' => $invoiceData));
             //$html = view('pdf.invoice_tl')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();
  			 
  			break;
        case RELOCATION_GLOBAL_MOBILITY :
                
            $data= DB::table('orders');
            $data->leftjoin('seller_details', 'sellers.user_id', '=', 'orders.seller_id');
            $data->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'orders.buyer_id');
            $data->leftjoin('seller_order_invoices', 'seller_order_invoices.order_id', '=', 'orders.id');
            $data->leftjoin('lkp_cities as from_city', 'from_city.id', '=', 'orders.from_city_id');
            $data->where('orders.id', $id);
            $data->select('seller_order_invoices.service_charge_amount as price','orders.order_no','sellers.name', 'sellers.address','seller_order_invoices.invoice_no','seller_order_invoices.service_tax_amount','seller_order_invoices.total_amount as total_amt',
                                       'sellers.tin','orders.buyer_consignor_address','orders.buyer_consignee_address',
                                       'from_city.city_name as city_from');
            $invoiceData = $data->get();
            //$html = view('pdf.invoice_relocation')->with(['invoiceData' => $invoiceData])->render();
            $html = view('pdf.invoice_global')->with(['invoiceData' => $invoiceData,'service_title' => $all_services[$serviceId],'serviceId'=>$serviceId])->render();


            break;
        
  	}
  	 
  	return $html;
  	 
  }

}
