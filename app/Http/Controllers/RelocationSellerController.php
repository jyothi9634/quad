<?php

namespace App\Http\Controllers;

use App\Models\RelocationSellerPost;
use App\Models\RelocationSellerPostItem;
use App\Models\RelocationSellerSelectedBuyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Components\CommonComponent;
use App\Components\Matching\SellerMatchingComponent;

use App\Models\PtlSellerPost;
use App\Models\RelocationintSellerPost;
use App\Models\RelocationintSellerPostItem;
use App\Models\RelocationintSellerSelectedBuyer;
use App\Models\PtlSellerPostItem;
use App\Models\PtlZone;
use App\Models\PtlSellerSellectedBuyer;
use App\Http\Requests;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Redirect;
use Response;
use Log;
use App\Components\Ptl\PtlSellerComponent;
use App\Http\Controllers\EditableGrid;
use App\Models\PtlTransitday;
use App\Models\PtlPincodexsector;
use App\Models\PtlTier;
use App\Models\PtlSector;

//RAIL
use App\Models\RailSellerPost;
use App\Models\RailSellerPostItem;
use App\Models\RailSellerSellectedBuyer;

//AriDom

use App\Models\AirdomSellerPost;
use App\Models\AirdomSellerPostItem;
use App\Models\AirdomSellerSellectedBuyer;

//AriInt

use App\Models\AirintSellerPost;
use App\Models\AirintSellerPostItem;
use App\Models\AirintSellerSellectedBuyer;

//Occean

use App\Models\OceanSellerPost;
use App\Models\OceanSellerPostItem;
use App\Models\OceanSellerSellectedBuyer;


//COURIER

use App\Models\CourierSellerPost;
use App\Models\CourierSellerPostItem;
use App\Models\CourierSellerSellectedBuyer;

//Relocationoffice
use App\Models\RelocationofficeSellerPost;
use App\Models\RelocationofficeSellerSelectedBuyer;
use App\Models\RelocationofficeSellerPostSlab;

use App\Models\RelocationpetSellerPost;
use App\Models\RelocationpetSellerPostItem;
use App\Models\RelocationpetSellerSelectedBuyer;

//relocation gm
use App\Models\RelocationgmSellerPost;
use App\Models\RelocationgmSellerSelectedBuyer;

class RelocationSellerController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware ( 'auth' );
    }
    public function relocationCreateSellerPost() {
        if(Session::get('service_id') == ROAD_FTL){
            return redirect('/createseller');
        }else if(Session::get('service_id') == ROAD_PTL || Session::get('service_id') == RAIL 
                || Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL 
                || Session::get('service_id') == OCEAN || Session::get('service_id') == COURIER){
            return redirect('/ptl/createsellerpost');
        }else if(Session::get('service_id') == ROAD_TRUCK_HAUL){
            return redirect('truckhaul/createsellerpost');
        }else if(Session::get('service_id') == ROAD_TRUCK_LEASE){
            return redirect('trucklease/createsellerpost');
        }
        Log::info ( 'create seller function used for creating a posts: ' . Auth::id (), array (
            'c' => '1'
        ) );
        try {
        	$serviceId = Session::get('service_id');
            $trackingtypes = CommonComponent::getTrackingTypes();

        	switch($serviceId){
        	case RELOCATION_DOMESTIC:
            $payment_methods = CommonComponent::getPaymentTerms ();
            $ratecardTypes = CommonComponent::getAllRatecardTypes();
            $propertyTypes = CommonComponent::getAllPropertyTypes();
            $loadtypes =  CommonComponent::getAllLoadCategories();
            $vehicletypes = CommonComponent::getAllVehicleCategories();
            $vehicletypecategories = CommonComponent::getAllVehicleCategoryTypes();
            //echo "<pre>--";print_R($vehicletypecategories);die;
            $userId = Auth::User ()->id;
            $user_subcsriptions = DB::table ( 'seller_details' )->where ( 'user_id', $userId )->first ();
            if ($user_subcsriptions) {
                $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                $current_date_seller = date ( "Y-m-d" );
            } else {
                $user_subcsriptions = DB::table ( 'sellers' )->where ( 'user_id', $userId )->first ();
                $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                $current_date_seller = date ( "Y-m-d" );
            }




           $session_search_values = array();
           $url_search= explode("?",HTTP_REFERRER);
           $url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);
           
           if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != ''){
           	$serverpreviUrL =$_SERVER['HTTP_REFERER'];
           }else{
           	$serverpreviUrL ='';
           }
           
            if($url_search_search == 'buyersearchresults'){
                $session_search_values[] = Session::get('session_from_location_relocation');
                $session_search_values[] = Session::get('session_from_location_id_relocation');
                $session_search_values[] = Session::get('session_seller_district_id_relocation');
                $session_search_values[] = Session::get('session_to_location_relocation');
                $session_search_values[] = Session::get('session_to_location_id_relocation');
                $session_search_values[] = Session::get('session_valid_from_relocation');
                $session_search_values[] = Session::get('session_valid_to_relocation');
                $session_search_values[] = Session::get('session_post_type_relocation');
            }else{
                $session_search_values[] = Session::put('session_from_location_relocation','');
                $session_search_values[] = Session::put('session_from_location_id_relocation','');
                $session_search_values[] = Session::put('session_to_location_relocation','');
                $session_search_values[] = Session::put('session_to_location_id_relocation','');
                $session_search_values[] = Session::put('session_valid_from_relocation','');
                $session_search_values[] = Session::put('session_valid_to_relocation','');
                $session_search_values[] = Session::put('session_post_type_relocation','');
                $session_search_values[] = Session::put('session_seller_district_id_relocation','');
            }
	
            return view ( 'relocation.sellers.seller_creation', [
                'paymentterms' => $payment_methods,
                'ratecardtypes' => $ratecardTypes,
                'propertytypes' => $propertyTypes,
                'loadtypes' => $loadtypes,
                'vehicletypes' => $vehicletypes,
                'vehicletypecategories' => $vehicletypecategories,
            	'url_search_search' => $url_search_search,
            	'serverpreviUrL' => $serverpreviUrL,
                'subscription_start_date_start' => $subscription_start_date_start,
                'subscription_end_date_end' => $subscription_end_date_end,
                'session_search_values_create'=> $session_search_values,
                'trackingtypes'=> $trackingtypes,
                'current_date_seller' => $current_date_seller
            ] );
			break;
            case RELOCATION_OFFICE_MOVE:

            $payment_methods = CommonComponent::getPaymentTerms ();
            $userId = Auth::User ()->id;
            $url_search= explode("?",HTTP_REFERRER);
            $url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);
            
            if($url_search_search != 'buyersearchresults'){   
                Session::put('seller_searchrequest_officemove','');
                Session::put('session_from_location_relocationoffice','');
                Session::put('session_from_location_id_relocationoffice','');
                Session::put('session_valid_from_relocationoffice','');
                Session::put('session_valid_to_relocationoffice','');        
            }         

            
            $user_subcsriptions = DB::table ( 'seller_details' )->where ( 'user_id', $userId )->first ();
            if ($user_subcsriptions) {
            	$subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
            	$subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
            	$subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
            	$subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
            	$current_date_seller = date ( "Y-m-d" );
            } else {
            	$user_subcsriptions = DB::table ( 'sellers' )->where ( 'user_id', $userId )->first ();
            	$subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
            	$subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
            	$subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
            	$subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
            	$current_date_seller = date ( "Y-m-d" );
            }
            
            return view ( 'relocationoffice.sellers.seller_creation', [
            			'paymentterms' => $payment_methods,
            			'subscription_start_date_start' => $subscription_start_date_start,
            			'subscription_end_date_end' => $subscription_end_date_end,
            		    'url_search_search' => $url_search_search,
                        'trackingtypes'=> $trackingtypes,
            			'current_date_seller' => $current_date_seller
            	] );
            break;
            
            case RELOCATION_PET_MOVE:
            $payment_methods = CommonComponent::getPaymentTerms ();
            $petTypes = CommonComponent::getAllPetTypes();
            $cageTypes = CommonComponent::getAllCageTypes();
            //echo "<pre>--";print_R($vehicletypecategories);die;
            $userId = Auth::User ()->id;
            $user_subcsriptions = DB::table ( 'seller_details' )->where ( 'user_id', $userId )->first ();
            if ($user_subcsriptions) {
                $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                $current_date_seller = date ( "Y-m-d" );
            } else {
                $user_subcsriptions = DB::table ( 'sellers' )->where ( 'user_id', $userId )->first ();
                $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                $current_date_seller = date ( "Y-m-d" );
            }

           $session_search_values = array();
           $url_search= explode("?",HTTP_REFERRER);
           $url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);
           
            if($url_search_search == 'buyersearchresults'){
                $session_search_values[] = Session::get('session_from_location_relocation');
                $session_search_values[] = Session::get('session_from_location_id_relocation');
                $session_search_values[] = Session::get('session_seller_district_id_relocation');
                $session_search_values[] = Session::get('session_to_location_relocation');
                $session_search_values[] = Session::get('session_to_location_id_relocation');
                $session_search_values[] = Session::get('session_valid_from_relocation');
                $session_search_values[] = Session::get('session_valid_to_relocation');
                $session_search_values[] = Session::get('session_post_type_relocation');
            }else{
                $session_search_values[] = Session::put('session_from_location_relocation','');
                $session_search_values[] = Session::put('session_from_location_id_relocation','');
                $session_search_values[] = Session::put('session_to_location_relocation','');
                $session_search_values[] = Session::put('session_to_location_id_relocation','');
                $session_search_values[] = Session::put('session_valid_from_relocation','');
                $session_search_values[] = Session::put('session_valid_to_relocation','');
                $session_search_values[] = Session::put('session_post_type_relocation','');
                $session_search_values[] = Session::put('session_seller_district_id_relocation','');
            }
		//echo "<pre>";print_R($session_search_values);exit;
            return view ( 'relocationpet.sellers.seller_creation', [
                'paymentterms' => $payment_methods,
                'petTypes' => $petTypes,
                'cageTypes' => $cageTypes,
            	'url_search_search' => $url_search_search,
                'subscription_start_date_start' => $subscription_start_date_start,
                'subscription_end_date_end' => $subscription_end_date_end,
                'session_search_values_create'=> $session_search_values,
                'trackingtypes'=> $trackingtypes,
                'current_date_seller' => $current_date_seller
            ] );
			break;
			
            case RELOCATION_INTERNATIONAL:
                    $payment_methods = CommonComponent::getPaymentTerms();
                    $petTypes = CommonComponent::getAllPetTypes();
                    $cageTypes = CommonComponent::getAllCageTypes();
                    //echo "<pre>--";print_R($vehicletypecategories);die;
                    $userId = Auth::User ()->id;
                    $user_subcsriptions = DB::table ( 'seller_details' )->where ( 'user_id', $userId )->first ();
                    if ($user_subcsriptions) {
                            $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                            $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                            $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                            $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                            $current_date_seller = date ( "Y-m-d" );
                    } else {
                            $user_subcsriptions = DB::table ( 'sellers' )->where ( 'user_id', $userId )->first ();
                            $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                            $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                            $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                            $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                            $current_date_seller = date ( "Y-m-d" );
                    }

                    $url_search= explode("?",HTTP_REFERRER);
                    $url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);

                    if($url_search_search != 'buyersearchresults'){

                    if(Session::has('seller_searchrequest_relocationint_type'))
                    {
                        if(Session::get("seller_searchrequest_relocationint_type") == 1)
                            Session::put('seller_searchrequest_relint_air','');
                        else
                            Session::put('seller_searchrequest_relint_ocean','');
                    }else{
                        Session::put('seller_searchrequest_relint_air','');
                        Session::put('seller_searchrequest_relint_ocean','');
                    }
                    Session::put('seller_searchrequest_relocationint_type',"");                    
                                        Session::put('session_from_location_relocation','');
                                        Session::put('session_from_location_id_relocation','');
                                        Session::put('session_to_location_relocation','');
                                        Session::put('session_to_location_id_relocation','');
                                        Session::put('session_valid_from_relocation','');
                                        Session::put('session_valid_to_relocation','');
                                        Session::put('session_post_type_relocation','');
                    if(Session::has('session_seller_district_id_relocation'))
                            Session::put('session_seller_district_id_relocation','');
                    }

                    return view ( 'relocationint.sellers.seller_creation', [
                                    'paymentterms' => $payment_methods,
                                    'petTypes' => $petTypes,
                                    'cageTypes' => $cageTypes,
                                    'url_search_search' => $url_search_search,
                                    'subscription_start_date_start' => $subscription_start_date_start,
                                    'subscription_end_date_end' => $subscription_end_date_end,
                                    'current_date_seller' => $current_date_seller,
                                    'trackingtypes'=> $trackingtypes
                                    ] );
                    break;
            case RELOCATION_GLOBAL_MOBILITY:
                $payment_methods = CommonComponent::getPaymentTerms ();
                $gmServiceTypes = CommonComponent::getAllGMServiceTypesforSeller();
                if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != ''){
                     $serverpreviUrL =$_SERVER['HTTP_REFERER'];
                }else{
                     $serverpreviUrL ='';
                }
            //echo "<pre>--";print_R($gmServiceTypes);die;
                $userId = Auth::User ()->id;
                $user_subcsriptions = DB::table ( 'seller_details' )->where ( 'user_id', $userId )->first ();
                if ($user_subcsriptions) {
                    $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                    $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                    $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                    $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                    $current_date_seller = date ( "Y-m-d" );
                } else {
                    $user_subcsriptions = DB::table ( 'sellers' )->where ( 'user_id', $userId )->first ();
                    $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                    $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                    $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                    $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                    $current_date_seller = date ( "Y-m-d" );
                }

               $session_search_values = array();
               $url_search= explode("?",HTTP_REFERRER);
               $url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);

                if($url_search_search == 'buyersearchresults'){
                   
                    
                    $session_search_values[] = Session::get('session_to_location_relocation');
                    $session_search_values[] = Session::get('session_to_location_id_relocation');
                    $session_search_values[] = Session::get('session_valid_from_relocation');
                    $session_search_values[] = Session::get('session_valid_to_relocation');
                    $session_search_values[] = Session::get('session_service_type_relocation');
                    $session_search_values[] = Session::get('session_seller_district_id_relocation');
                }else{
                   
                    $session_search_values[] = Session::put('session_to_location_relocation','');
                    $session_search_values[] = Session::put('session_to_location_id_relocation','');
                    $session_search_values[] = Session::put('session_valid_from_relocation','');
                    $session_search_values[] = Session::put('session_valid_to_relocation','');
                    $session_search_values[] = Session::put('session_service_type_relocation','');
                    $session_search_values[] = Session::put('session_seller_district_id_relocation','');
                }
		//echo "<pre>";print_R($session_search_values);exit;
                return view ( 'relocationglobal.sellers.seller_creation', [
                    'paymentterms' => $payment_methods,
                    'gmServiceTypes' => $gmServiceTypes,
                    'url_search_search' => $url_search_search,
                    'subscription_start_date_start' => $subscription_start_date_start,
                    'subscription_end_date_end' => $subscription_end_date_end,
                    'session_search_values_create'=> $session_search_values,
                    'current_date_seller' => $current_date_seller,
                    'serverpreviUrL'=>$serverpreviUrL
                ] );
			break;
			
        	}
        } catch ( Exception $e ) {
            echo 'Caught exception: ', $e->getMessage (), "\n";
        }
    }

    public function relocationPostCreation(Request $request) {

        //Session::put('service_id', RELOCATION_DOMESTIC);
        //echo "<pre>";print_r($_POST);exit;
        Log::info ( 'Insert the seller posts data: ' . Auth::id (), array (
            'c' => '1'
        ) );
        try {
        	$serviceId = Session::get('service_id');
        switch($serviceId){	
        	
        	case RELOCATION_DOMESTIC:
            Session::put('session_delivery_date_ptl','');
            Session::put('session_dispatch_date_ptl','');
            Session::put('session_vehicle_type_ptl','');
            Session::put('session_load_type_ptl','');
            Session::put('session_from_city_id_ptl','');
            Session::put('session_to_city_id_ptl','');
            Session::put('session_from_location_ptl','');
            Session::put('session_to_location_ptl','');

            $roleId = Auth::User ()->lkp_role_id;
            if ($roleId == SELLER) {
                CommonComponent::activityLog ( "SELLER_CREATED_POSTS", SELLER_CREATED_POSTS, 0, HTTP_REFERRER, CURRENT_URL );
            }
            if (! empty ( Input::all () )) {
                //echo "<pre>";print_R(Input::all ());die;

                $msg = '';
                if (isset ( $_POST ['optradio'] )) {
                    $is_private = $_POST ['optradio'];
                }
                $randnumber_value = rand ( 11111, 99999 );
                $postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));

                $created_year = date('Y');
                if(Session::get('service_id') == RELOCATION_DOMESTIC){
                    $randnumber = 'RD/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
                }
                $multi_household_data = $_POST ['household_items'];
                $multi_vehicle_data = $_POST ['vehicle_items'];
                
                if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
                    if (isset ( $_POST ['buyer_list_for_sellers'] ) && $_POST ['buyer_list_for_sellers'] != '') {
                        $buyer_list = explode ( ",", $_POST ['buyer_list_for_sellers'] );
                        array_shift ( $buyer_list );
                        $buyer_list_count = count ( $buyer_list );
                    }
                }
                if(Session::get('service_id') == RELOCATION_DOMESTIC){
                    $sellerpost = new RelocationSellerPost();
                    $sellerpost->lkp_service_id = RELOCATION_DOMESTIC;
                }

                $sellerpost->rate_card_type = $_POST ['post_rate_card_type'];
                $sellerpost->from_date = CommonComponent::convertDateForDatabase($_POST ['valid_from_val']);
                $sellerpost->to_date = CommonComponent::convertDateForDatabase($_POST ['valid_to_val']);

                $sellerpost->from_location_id = $_POST ['from_location_id'];
                $sellerpost->to_location_id = $_POST ['to_location_id'];
                $sellerpost->seller_district_id = $_POST ['seller_district_id'];
                $sellerpost->packing_loading = 0;
                if(isset($_POST ['crating_charges']))
                $sellerpost->crating_charges = $_POST ['crating_charges'];
                $sellerpost->unloading_delivery_unpack = 0;
                if(isset($_POST ['storate_charges']))
                $sellerpost->storate_charges = $_POST ['storate_charges'];
                if(isset($_POST ['escort_charges']))
                $sellerpost->escort_charges = $_POST ['escort_charges'];
                if(isset($_POST ['handyman_charges']))
                $sellerpost->handyman_charges = $_POST ['handyman_charges'];
                if(isset($_POST ['property_search']))
                $sellerpost->property_search = $_POST ['property_search'];
                $sellerpost->setting_service = 0;
                if(isset($_POST ['brokerage']))
                $sellerpost->brokerage = $_POST ['brokerage'];
                $sellerpost->total_inventory_volume = 0;

                $sellerpost->tracking = $request->tracking;
                $sellerpost->terms_conditions = $request->terms_conditions;
                $sellerpost->lkp_payment_mode_id = $request->paymentterms;
                $sellerpost->credit_period = $request->credit_period_ptl;
                $sellerpost->credit_period_units = $request->credit_period_units;

                if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
                    $sellerpost->lkp_access_id = 2;
                } else {
                    $sellerpost->lkp_access_id = 1;
                }
                $sellerpost->seller_id = Auth::id ();
                $sellerpost->transaction_id = $randnumber;

                if (Input::get ( 'confirm' ) == 'Confirm' && $_POST['sellerpoststatus'] == 1) {
                    $lkp_post_status_id = 2;
                } else {
                    $lkp_post_status_id = 1;
                }

                $sellerpost->lkp_post_status_id = $lkp_post_status_id;
                $sellerpost->cancellation_charge_text = "cancellation Charges";
                if(isset($_POST['cancellation_charge_price']))
                $sellerpost->cancellation_charge_price = $request->cancellation_charge_price;
                $sellerpost->docket_charge_text = "Other Charges";
                if(isset($_POST['docket_charge_price']))
                $sellerpost->docket_charge_price = $request->docket_charge_price;
                $f=1;
                $ft=1;
                for($i=1;$i<=$request->next_terms_count_search;$i++){
                    if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] != '') {
                        $field_name="other_charge".$f."_text";
                        $sellerpost->$field_name = $_POST['labeltext_'.$i];
                        $f++;
                    }
                    if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] == '') {
                        $f++;
                    }
                    if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] != '') {
                        $field_name="other_charge".$ft."_price";
                        $sellerpost->$field_name = $_POST['terms_condtion_types_'.$i];
                        $ft++;
                    }
                    if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] == '') {
                        $ft++;
                    }

                }


                if (is_array ( $request->accept_payment_ptl )) {
                    $sellerpost->accept_payment_netbanking = in_array ( 1, $request->accept_payment_ptl ) ? 1 : 0;
                    $sellerpost->accept_payment_credit = in_array ( 2, $request->accept_payment_ptl ) ? 1 : 0;
                    $sellerpost->accept_payment_debit = in_array ( 3, $request->accept_payment_ptl ) ? 1 : 0;
                } else {
                    $sellerpost->accept_payment_netbanking = 0;
                    $sellerpost->accept_payment_credit = 0;
                    $sellerpost->accept_payment_debit = 0;
                }


                if (is_array ( $request->accept_credit_netbanking )) {
                    $sellerpost->accept_credit_netbanking = in_array ( 1, $request->accept_credit_netbanking ) ? 1 : 0;
                    $sellerpost->accept_credit_cheque = in_array ( 2, $request->accept_credit_netbanking ) ? 1 : 0;
                } else {
                    $sellerpost->accept_credit_netbanking = 0;
                    $sellerpost->accept_credit_cheque = 0;
                }
                //echo "<pre>";print_R($_POST);
                //echo $multi_vehicle_data;die;
                //print_R($sellerpost);
                //die("test");


                $created_at = date ( 'Y-m-d H:i:s' );
                $createdIp = $_SERVER ['REMOTE_ADDR'];
                $sellerpost->created_by = Auth::id ();
                $sellerpost->created_at = $created_at;
                $sellerpost->created_ip = $createdIp;
                
                if ($sellerpost->save ()) {

                    // CommonComponent::auditLog($sellerpost->id,'seller_posts');
                    for($i = 0; $i < $multi_household_data; $i ++) {
                        if(Session::get('service_id') == RELOCATION_DOMESTIC){
                            $sellerpost_lineitem = new RelocationSellerPostItem();
                        }
                        $sellerpost_lineitem->seller_post_id = $sellerpost->id;
                        $sellerpost_lineitem->rate_card_type = 1;
                        $sellerpost_lineitem->lkp_property_type_id = $_POST ['propertytypes_hidden'] [$i];
                        $sellerpost_lineitem->volume = $_POST ['volume_hidden'] [$i];
                        $sellerpost_lineitem->lkp_load_category_id = $_POST ['load_types_hidden'] [$i];
                        $sellerpost_lineitem->rate_per_cft = $_POST ['rate_per_cft_hidden'] [$i];
                        $sellerpost_lineitem->lkp_vehicle_category_id = "";
                        $sellerpost_lineitem->lkp_car_size = "";
                        $sellerpost_lineitem->cost = "";
                        $sellerpost_lineitem->transitdays = $_POST ['transit_days_hidden'] [$i];
                        $sellerpost_lineitem->units = $_POST ['transitdays_units_relocation_hidden'] [$i];
                        $sellerpost_lineitem->transport_charges = $_POST ['transport_charges_hidden'] [$i];
                        $created_at = date ( 'Y-m-d H:i:s' );
                        $createdIp = $_SERVER ['REMOTE_ADDR'];
                        $sellerpost_lineitem->created_by = Auth::id ();
                        $sellerpost_lineitem->created_at = $created_at;
                        $sellerpost_lineitem->created_ip = $createdIp;
                        $sellerpost_lineitem->save ();

                        //*******matching engine***********************//
                        $request = array();
                        $request['from_location_id'] = $_POST ['from_location_id'];
                        $request['to_location_id'] = $_POST ['to_location_id'];
                        $request['post_type'] = 1;
                        $request['propertytypes'] = $_POST ['propertytypes_hidden'] [$i];
                        $request['valid_from'] = CommonComponent::convertDateForDatabase($_POST ['valid_from_val']);
                        $request['valid_to'] = CommonComponent::convertDateForDatabase($_POST ['valid_to_val']);
                        $request['transit_days'] = $_POST ['transit_days_hidden'] [$i];
                        SellerMatchingComponent::doMatching(RELOCATION_DOMESTIC, $sellerpost->id, 2, $request);
                        //*******matching engine***********************//
                    }

                    for($i = 0; $i < $multi_vehicle_data; $i ++) {
                        if(Session::get('service_id') == RELOCATION_DOMESTIC){
                            $sellerpost_lineitem = new RelocationSellerPostItem();
                        }
                        $sellerpost_lineitem->seller_post_id = $sellerpost->id;
                        $sellerpost_lineitem->rate_card_type = 2;
                        $sellerpost_lineitem->lkp_property_type_id = "";
                        $sellerpost_lineitem->volume = "";
                        $sellerpost_lineitem->lkp_load_category_id = "";
                        $sellerpost_lineitem->rate_per_cft = "";
                        $sellerpost_lineitem->lkp_vehicle_category_id = $_POST ['vehicle_types_hidden'] [$i];
                        $sellerpost_lineitem->lkp_car_size = $_POST ['vehicle_type_category_hidden'] [$i];
                        $sellerpost_lineitem->cost = $_POST ['cost_hidden'] [$i];
                        $sellerpost_lineitem->transitdays = $_POST ['transit_days_vehicle_hidden'] [$i];
                        $sellerpost_lineitem->units = $_POST ['transitdays_units_relocation_vehicle_hidden'] [$i];
                        $sellerpost_lineitem->transport_charges = $_POST ['transport_charges_vehicle_hidden'] [$i];
                        $created_at = date ( 'Y-m-d H:i:s' );
                        $createdIp = $_SERVER ['REMOTE_ADDR'];
                        $sellerpost_lineitem->created_by = Auth::id ();
                        $sellerpost_lineitem->created_at = $created_at;
                        $sellerpost_lineitem->created_ip = $createdIp;
                        $sellerpost_lineitem->save ();

                        //*******matching engine***********************//
                        if($lkp_post_status_id == OPEN){
                            $request = array();
                            $request['from_location_id'] = $_POST ['from_location_id'];
                            $request['to_location_id'] = $_POST ['to_location_id'];
                            $request['post_type'] = 2;
                            $request['vehicle_category'] = $_POST ['vehicle_types_hidden'][$i];
                            $request['valid_from'] = CommonComponent::convertDateForDatabase($_POST ['valid_from_val']);
                            $request['valid_to'] = CommonComponent::convertDateForDatabase($_POST ['valid_to_val']);
                            $request['transit_days'] = $_POST ['transit_days_vehicle_hidden'] [$i];
                            SellerMatchingComponent::doMatching(RELOCATION_DOMESTIC, $sellerpost->id, 2, $request);
                        }
                        //*******matching engine***********************//
                    }

                    if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
                        if (isset ( $_POST ['buyer_list_for_sellers'] ) && $_POST ['buyer_list_for_sellers'] != '') {
                            for($i = 0; $i < $buyer_list_count; $i ++) {

                                if(Session::get('service_id') == RELOCATION_DOMESTIC){
                                    $sellerpost_for_buyers = new RelocationSellerSelectedBuyer();
                                }

                                $sellerpost_for_buyers->seller_post_id = $sellerpost->id;
                                $sellerpost_for_buyers->buyer_id = $buyer_list [$i];
                                $created_at = date ( 'Y-m-d H:i:s' );
                                $createdIp = $_SERVER ['REMOTE_ADDR'];
                                $sellerpost_for_buyers->created_by = Auth::id ();
                                $sellerpost_for_buyers->created_at = $created_at;
                                $sellerpost_for_buyers->created_ip = $createdIp;
                                $sellerpost_for_buyers->save ();
                                $seller_selected_buyers_email = DB::table ( 'users' )->where ( 'id', $buyer_list [$i] )->get ();
                                $seller_selected_buyers_email [0]->randnumber = $randnumber;
                                $seller_selected_buyers_email [0]->sellername = Auth::User ()->username;
                                CommonComponent::send_email ( SELLER_CREATED_POST_FOR_BUYERS, $seller_selected_buyers_email );
                                // CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
                                
                                //*******Send Sms to the private buyers***********************//
                                if($lkp_post_status_id == OPEN){
                                	$msg_params = array(
                                			'randnumber' => $randnumber,
                                			'sellername' => Auth::User()->username,
                                			'servicename' => "RELOCATION DOMESTIC"
                                	);
                                	$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
                                	if($getMobileNumber)
                                		CommonComponent::sendSMS($getMobileNumber,SELLER_CREATED_POST_FOR_BUYERS_SMS,$msg_params);
                                }
                                //*******Send Sms to the private buyers***********************//
                                
                                
                                
                            }
                        }
                    }

                    if (Input::get ( 'confirm' ) == 'Confirm' && $_POST['sellerpoststatus'] == 1) {
                        return $randnumber;
                    } else {
                        return redirect ( '/sellerlist' )->with ( 'message_create_post_ptl', 'Post was saved as draft' );
                    }
                }
            }
          break;
          
          case RELOCATION_INTERNATIONAL:
    		
    		Session::put('session_delivery_date','');
    		Session::put('session_dispatch_date','');
    		Session::put('session_vehicle_type','');
    		Session::put('session_load_type','');
    		Session::put('session_from_city_id','');
    		Session::put('session_to_city_id','');
    		Session::put('session_from_location','');
    		Session::put('session_to_location','');
    		Session::put('session_seller_district_id','');
    		
    		
    		$roleId = Auth::User()->lkp_role_id;
    		if($roleId == SELLER){
    			CommonComponent::activityLog("SELLER_CREATED_POSTS",SELLER_CREATED_POSTS,0,HTTP_REFERRER,CURRENT_URL);
    		}
			if(!empty(Input::all()))  {
			
			
			$postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
			$created_year = date('Y');
			$randnumber = 'REL-INT/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
			
			
			if($_POST['int_air_coean']==1){
				
				//echo '<pre>';print_r($_POST);exit;
				if(isset($_POST['optradio'])){
					$is_private = $_POST['optradio'];
				}
				if(isset($_POST['optradio']) && $is_private == 2){
					if(isset($_POST['buyer_list_for_sellers']) && $_POST['buyer_list_for_sellers'] != ''){
						$buyer_list = explode(",", $_POST['buyer_list_for_sellers']);
						array_shift($buyer_list);
						$buyer_list_count = count($buyer_list);
					}
				}
			$slabscount = 6;
			$sellerpost  =  new RelocationintSellerPost();
			$sellerpost->lkp_service_id = $serviceId;
			$sellerpost->from_location_id = $request->from_location_id;
			$sellerpost->tracking = $request->tracking;
			$sellerpost->transitdays = $request->transitdays;
			$sellerpost->units = $request->units;
			$sellerpost->to_location_id = $request->to_location_id;
			$sellerpost->seller_district_id = $request->seller_district_id;
			$sellerpost->lkp_payment_mode_id = $request->paymentterms;
			$sellerpost->credit_period = $request->credit_period;
                        $sellerpost->storage_charges = $request->storate_charges;
			$sellerpost->credit_period_units = $request->credit_period_units;
			$sellerpost->terms_conditions = $request->terms_conditions;
			if(isset($_POST ['storate_charges']))
				$sellerpost->storage_charge_text = "Storage Charges";
				$sellerpost->storage_charge_price = $_POST ['storate_charges'];
			if(isset($_POST['optradio']) && $is_private == 2){
			$sellerpost->lkp_access_id = 2;
			}else{
			$sellerpost->lkp_access_id = 1;
			}
			$sellerpost->seller_id =Auth::id();
			$sellerpost->transaction_id =$randnumber;
			$sellerpost->from_date = CommonComponent::convertDateForDatabase($_POST['valid_from']);
			$sellerpost->to_date = CommonComponent::convertDateForDatabase($_POST['valid_to']);
			if(isset($_POST['sellerpoststatus']) && $_POST['sellerpoststatus'] == 1){
				$lkp_post_status_id = 2;
			}else{
				$lkp_post_status_id = 1;
			}
			$sellerpost->lkp_post_status_id = $lkp_post_status_id;
			
			if (isset ($_POST['terms_condtion_types1']) && $_POST['terms_condtion_types1'] != '') {
				$sellerpost->cancellation_charge_text = $request->labeltext [0];
				$sellerpost->cancellation_charge_price = $request->terms_condtion_types1;
			} else {
				$sellerpost->cancellation_charge_text = '';
				$sellerpost->cancellation_charge_price = '';
			}
			if (isset ( $_POST['terms_condtion_types2'] ) && $_POST['terms_condtion_types2'] != '') {
				$sellerpost->other_charge_text = "Other Charges";
				$sellerpost->other_charge_price = $request->terms_condtion_types2;
			} else {
				$sellerpost->other_charge_text = "Other Charges";
				$sellerpost->other_charge_price = $request->terms_condtion_types2;
			}
			
			$f=1;
			$ft=1;
			for($i=1;$i<=$request->next_terms_count_search;$i++){
				
				
				if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] != '') {
					$field_name="other_charge".$f."_text";
					$sellerpost->$field_name = $_POST['labeltext_'.$i];
					$f++;
				}
				if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] == '') {
					$f++;
				}
				if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] != '') {
					$field_name="other_charge".$ft."_price";
					$sellerpost->$field_name = $_POST['terms_condtion_types_'.$i];
					$ft++;
				}
				if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] == '') {
					$ft++;
				}
				
			}
			
			
			if (is_array($request->accept_payment)){
			$sellerpost->accept_payment_netbanking = in_array(1,$request->accept_payment) ? 1 :0;
			$sellerpost->accept_payment_credit = in_array(2,$request->accept_payment) ? 1 :0;
			$sellerpost->accept_payment_debit = in_array(3,$request->accept_payment) ? 1 :0;
			}else{
			$sellerpost->accept_payment_netbanking = 0;
			$sellerpost->accept_payment_credit = 0;
			$sellerpost->accept_payment_debit = 0;
			}
			
			if (is_array($request->accept_credit_netbanking)){
				$sellerpost->accept_credit_netbanking = in_array(1,$request->accept_credit_netbanking) ? 1 :0;
				$sellerpost->accept_credit_cheque = in_array(2,$request->accept_credit_netbanking) ? 1 :0;
			}else{
				$sellerpost->accept_credit_netbanking = 0;
				$sellerpost->accept_credit_cheque = 0;
			}
			
			$created_at = date ( 'Y-m-d H:i:s' );
			$createdIp = $_SERVER ['REMOTE_ADDR'];
			$sellerpost->created_by = Auth::id();
			$sellerpost->created_at = $created_at;
			$sellerpost->created_ip = $createdIp;
			
			$matchedItems = array ();
				if($sellerpost->save()){
					
					//CommonComponent::auditLog($sellerpost->id,'seller_posts');
					
					//Relcation Seller Slabs
					
					for($i = 1; $i <=$slabscount; $i++){
						$SellerSlabs = new \App\Models\RelocationintSellerPostAirWeightSlab();
						$SellerSlabs->lkp_service_id=RELOCATION_INTERNATIONAL;
						$SellerSlabs->seller_post_id      = $sellerpost->id;
						$SellerSlabs->lkp_air_weight_slab_id      = $i;
						$SellerSlabs->freight_charges      = $_POST['freightcharge_'.$i];
						$SellerSlabs->od_charges      = $_POST['odcharges_'.$i];
						$SellerSlabs->created_by      = Auth::id();
						$SellerSlabs->created_ip      = $createdIp;
						$SellerSlabs->created_at      = $created_at;
						$SellerSlabs->save ();
					
					}
					
						//*******matching engine***********************//
						if($lkp_post_status_id == OPEN){
							$request = array();
							$request['from_location_id'] = $_POST['from_location_id'];
							$request['to_location_id'] = $_POST['to_location_id'];
							$request['post_type'] = 2;
							$request['service_type'] = 1;
							$request['dispatch_date'] = CommonComponent::convertDateForDatabase($_POST['valid_from']);
							$request['delivery_date'] = CommonComponent::convertDateForDatabase($_POST['valid_to']);
							$request['transit_days'] = $_POST['transitdays'];
							SellerMatchingComponent::doMatching(RELOCATION_INTERNATIONAL, $sellerpost->id, 2, $request);
						}
						//*******matching engine***********************//
								
					
					
					if(isset($_POST['optradio']) && $is_private == 2){
						if(isset($_POST['buyer_list_for_sellers']) && $_POST['buyer_list_for_sellers'] != ''){	
							for($i = 0; $i < $buyer_list_count; $i ++) {
								$sellerpost_for_buyers  =  new RelocationintSellerSelectedBuyer();
								$sellerpost_for_buyers->seller_post_id = $sellerpost->id;
								$sellerpost_for_buyers->buyer_id = $buyer_list[$i];
								$created_at = date ( 'Y-m-d H:i:s' );
								$createdIp = $_SERVER ['REMOTE_ADDR'];
								$sellerpost_for_buyers->created_by = Auth::id();
								$sellerpost_for_buyers->created_at = $created_at;
								$sellerpost_for_buyers->created_ip = $createdIp;
								$sellerpost_for_buyers->save();
								$seller_selected_buyers_email = DB::table('users')->where('id', $buyer_list[$i])->get();
								$seller_selected_buyers_email[0]->randnumber = $randnumber;
								$seller_selected_buyers_email[0]->sellername = Auth::User()->username;
								CommonComponent::send_email(SELLER_CREATED_POST_FOR_BUYERS,$seller_selected_buyers_email);
								
								
								if($lkp_post_status_id == 2){
									//*******Send Sms to the private buyers***********************//
									$msg_params = array(
											'randnumber' => $randnumber,
											'sellername' => Auth::User()->username,
											'servicename' => 'RELOCATION INTERNATIONAL'
											);
									$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
									if($getMobileNumber)
									CommonComponent::sendSMS($getMobileNumber,SELLER_CREATED_POST_FOR_BUYERS_SMS,$msg_params);
									//*******Send Sms to the private buyers***********************//
								}
								//CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
							}
						}
					}
					
					
					
					

					if(isset($_POST['sellerpoststatus']) && $_POST['sellerpoststatus'] == 1){
						return $randnumber;
					}else{
						
						return redirect('/sellerlist')->with('message_create_post', 'Post was saved as draft');
					}
				}
			}else{
				//echo "<pre>";print_r($_POST);exit;
				if(isset($_POST['optradio_ocen'])){
					$is_private = $_POST['optradio_ocen'];
				}
				if(isset($_POST['optradio_ocen']) && $is_private == 2){
					if(isset($_POST['buyer_list_for_sellers_ocen']) && $_POST['buyer_list_for_sellers_ocen'] != ''){
						$buyer_list = explode(",", $_POST['buyer_list_for_sellers_ocen']);
						array_shift($buyer_list);
						$buyer_list_count = count($buyer_list);
					}
				}
				
				$sellerpost  =  new RelocationintSellerPost();
				$sellerpost->lkp_service_id = $serviceId;
				$sellerpost->lkp_international_type_id=2;
				$sellerpost->from_location_id = $_POST['from_location'][0];
				$sellerpost->crating_charges = $_POST['crating_charges'];
				$sellerpost->tracking = $_POST['ocen_tracking'];
				$sellerpost->to_location_id = $_POST['to_location'][0];
				$sellerpost->seller_district_id = $_POST['sellerdistrict'][0];
				
				if(isset($_POST['origin_storage']))
				$sellerpost->origin_storage = $_POST['origin_storage'];
				if(isset($_POST['destination_storage']))
				$sellerpost->destination_storage = $_POST['destination_storage'];
				if(isset($_POST['origin_handyman_services']))
				$sellerpost->origin_handyman_services = $_POST['origin_handyman_services'];
				if(isset($_POST['destination_handyman_services']))
				$sellerpost->destination_handyman_services = $_POST['destination_handyman_services'];
				
				
				
				$sellerpost->lkp_payment_mode_id = $_POST['ocean_paymentterms'];
				$sellerpost->credit_period = $_POST['credit_period_ocen'];
				$sellerpost->credit_period_units = $_POST['credit_period_units_ocen'];
				$sellerpost->terms_conditions = $_POST['terms_conditions'];

				if(isset($_POST['optradio_ocen']) && $is_private == 2){
					$sellerpost->lkp_access_id = 2;
				}else{
					$sellerpost->lkp_access_id = 1;
				}
				$sellerpost->seller_id =Auth::id();
				$sellerpost->transaction_id =$randnumber;
				$sellerpost->from_date = $_POST['ocen_valid_from_val'];
				$sellerpost->to_date = $_POST['ocen_valid_to_val'];
				if(isset($_POST['oceansellerpoststatus']) && $_POST['oceansellerpoststatus'] == 1){
					$lkp_post_status_id = 2;
				}else{
					$lkp_post_status_id = 1;
				}
				$sellerpost->lkp_post_status_id = $lkp_post_status_id;
					
				if (isset ($_POST['terms_condtion_types1']) && $_POST['terms_condtion_types1'] != '') {
					$sellerpost->cancellation_charge_text = $request->labeltext [0];
					$sellerpost->cancellation_charge_price = $request->terms_condtion_types1;
				} else {
					$sellerpost->cancellation_charge_text = '';
					$sellerpost->cancellation_charge_price = '';
				}
				if (isset ( $_POST['terms_condtion_types2'] ) && $_POST['terms_condtion_types2'] != '') {
					$sellerpost->other_charge_text = "Other Charges";
					$sellerpost->other_charge_price = $_POST['terms_condtion_types2'];
				} else {
					$sellerpost->other_charge_text = "Other Charges";
					$sellerpost->other_charge_price = $request->terms_condtion_types2;
				}
					
				$f=1;
				$ft=1;
				for($i=1;$i<=$request->next_terms_count_search_ocen;$i++){
				
				
					if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] != '') {
						$field_name="other_charge".$f."_text";
						$sellerpost->$field_name = $_POST['labeltext_'.$i];
						$f++;
					}
					if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] == '') {
						$f++;
					}
					if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] != '') {
						$field_name="other_charge".$ft."_price";
						$sellerpost->$field_name = $_POST['terms_condtion_types_'.$i];
						$ft++;
					}
					if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] == '') {
						$ft++;
					}
				
				}
					
					
				if (is_array($request->accept_payment_ocen)){
					$sellerpost->accept_payment_netbanking = in_array(1,$request->accept_payment_ocen) ? 1 :0;
					$sellerpost->accept_payment_credit = in_array(2,$request->accept_payment_ocen) ? 1 :0;
					$sellerpost->accept_payment_debit = in_array(3,$request->accept_payment_ocen) ? 1 :0;
				}else{
					$sellerpost->accept_payment_netbanking = 0;
					$sellerpost->accept_payment_credit = 0;
					$sellerpost->accept_payment_debit = 0;
				}
					
				if (is_array($request->accept_credit_netbanking_ocen)){
					$sellerpost->accept_credit_netbanking = in_array(1,$request->accept_credit_netbanking_ocen) ? 1 :0;
					$sellerpost->accept_credit_cheque = in_array(2,$request->accept_credit_netbanking_ocen) ? 1 :0;
				}else{
					$sellerpost->accept_credit_netbanking = 0;
					$sellerpost->accept_credit_cheque = 0;
				}
					
				$created_at = date ( 'Y-m-d H:i:s' );
				$createdIp = $_SERVER ['REMOTE_ADDR'];
				$sellerpost->created_by = Auth::id();
				$sellerpost->created_at = $created_at;
				$sellerpost->created_ip = $createdIp;
					
				$matchedItems = array ();
				if($sellerpost->save()){
						
					for($i=0;$i<count($_POST['shipment_types']);$i++){
							
						//Relcation Seller Line items
						$sellerpostitem  =  new RelocationintSellerPostItem();
						$sellerpostitem->seller_post_id = $sellerpost->id;
						$sellerpostitem->lkp_relocation_shipment_type_id = $_POST['shipment_types'][$i];
						$sellerpostitem->freight_charges = $_POST['freight_charge'][$i];
						$sellerpostitem->lkp_relocation_shipment_volume_id= $_POST['volume_types'][$i];
						$sellerpostitem->od_charges = $_POST['od_charge'][$i];
						$sellerpostitem->transitdays =$_POST['transitdays'][$i];
						$sellerpostitem->units = $_POST['units'][$i];
						$sellerpostitem->is_private = 0;
						$sellerpostitem->created_by = Auth::id();
						$sellerpostitem->created_at = $created_at;
						$sellerpostitem->created_ip = $createdIp;
						$sellerpostitem->save();
						
						
						
						//*******matching engine***********************//
						if($lkp_post_status_id == OPEN){
							$request = array();
							$request['from_location_id'] = $_POST['from_location'][0];
							$request['to_location_id'] = $_POST['to_location'][0];
							$request['post_type'] = 2;
							$request['service_type'] = 2;
							$request['dispatch_date'] = CommonComponent::convertDateForDatabase($_POST['ocen_valid_from_val']);
							$request['delivery_date'] = CommonComponent::convertDateForDatabase($_POST['ocen_valid_to_val']);
							$request['transit_days'] = $_POST['transitdays'][$i];
							SellerMatchingComponent::doMatching(RELOCATION_INTERNATIONAL, $sellerpost->id, 2, $request);
						}
						//*******matching engine***********************//
							
					}		
					if(isset($_POST['optradio_ocen']) && $is_private == 2){
						if(isset($_POST['buyer_list_for_sellers_ocen']) && $_POST['buyer_list_for_sellers_ocen'] != ''){
							for($i = 0; $i < $buyer_list_count; $i ++) {
								$sellerpost_for_buyers  =  new RelocationintSellerSelectedBuyer();
								$sellerpost_for_buyers->seller_post_id = $sellerpost->id;
								$sellerpost_for_buyers->buyer_id = $buyer_list[$i];
								$created_at = date ( 'Y-m-d H:i:s' );
								$createdIp = $_SERVER ['REMOTE_ADDR'];
								$sellerpost_for_buyers->created_by = Auth::id();
								$sellerpost_for_buyers->created_at = $created_at;
								$sellerpost_for_buyers->created_ip = $createdIp;
								$sellerpost_for_buyers->save();
								$seller_selected_buyers_email = DB::table('users')->where('id', $buyer_list[$i])->get();
								$seller_selected_buyers_email[0]->randnumber = $randnumber;
								$seller_selected_buyers_email[0]->sellername = Auth::User()->username;
								CommonComponent::send_email(SELLER_CREATED_POST_FOR_BUYERS,$seller_selected_buyers_email);
				
				
								if($lkp_post_status_id == 2){
									//*******Send Sms to the private buyers***********************//
									$msg_params = array(
											'randnumber' => $randnumber,
											'sellername' => Auth::User()->username,
											'servicename' => 'RELOCATION INTERNATIONAL'
									);
									$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
									if($getMobileNumber)
										CommonComponent::sendSMS($getMobileNumber,SELLER_CREATED_POST_FOR_BUYERS_SMS,$msg_params);
									//*******Send Sms to the private buyers***********************//
								}
								//CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
							}
						}
					}
					
					//*******matching engine***********************//
					if($lkp_post_status_id == OPEN){
						$request = array();
						$request['from_location_id'] = $_POST['from_location'][0];
						$request['to_location_id'] = $_POST['to_location'][0];
						$request['post_type'] = 2;
						$request['service_type'] = 2;
						$request['valid_from'] = CommonComponent::convertDateForDatabase($_POST['ocen_valid_from_val']);
						$request['valid_to'] = CommonComponent::convertDateForDatabase($_POST['ocen_valid_to_val']);
						$request['transit_days'] = $_POST['transitdays'];
						SellerMatchingComponent::doMatching(RELOCATION_INTERNATIONAL, $sellerpost->id, 2, $request);
					}
					//*******matching engine***********************//
						
				
					if(isset($_POST['oceansellerpoststatus']) && $_POST['oceansellerpoststatus'] == 1){
						return $randnumber;
					}else{
				
						return redirect('/sellerlist')->with('message_create_post', 'Post was saved as draft');
					}
				}
				
				
			}
			}
			
          	break;
          
          
          
          case RELOCATION_OFFICE_MOVE:
          	$roleId = Auth::User ()->lkp_role_id;
          	$userId = Auth::User ()->lkp_role_id;
          	if ($roleId == SELLER) {
          		CommonComponent::activityLog ( "SELLER_CREATED_POSTS", SELLER_CREATED_POSTS, 0, HTTP_REFERRER, CURRENT_URL );
          	}
          	if (! empty ( Input::all () )) {
          		
          	//echo "<pre>";print_R(Input::all ());die;
          	$frDate=CommonComponent::convertDateForDatabase($_POST ['valid_from']);
          	$toDate=CommonComponent::convertDateForDatabase($_POST ['valid_to']);
          	$sellerpostcheck=DB::table('relocationoffice_seller_posts as sellerposts')
          		->where('sellerposts.from_location_id', $_POST ['from_location_id'])
          		->where('sellerposts.created_by', $userId)
          		->whereRaw ("(`from_date` between  '$frDate' and '$toDate' or `to_date` between '$frDate' and '$toDate')")
          		->select('sellerposts.id')
          		->count();
          	
          	if($sellerpostcheck==0){
          	$msg = '';
          	if (isset ( $_POST ['optradio'] )) {
          			$is_private = $_POST ['optradio'];
          	}
          	
          	$randnumber_value = rand ( 11111, 99999 );
          	$postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
          	$created_year = date('Y');
          	 if(Session::get('service_id') == RELOCATION_OFFICE_MOVE){
          			$randnumber = 'REL-OFF/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
          		}
          	if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
          			if (isset ( $_POST ['buyer_list_for_sellers'] ) && $_POST ['buyer_list_for_sellers'] != '') {
          				$buyer_list = explode ( ",", $_POST ['buyer_list_for_sellers'] );
          				array_shift ( $buyer_list );
          				$buyer_list_count = count ( $buyer_list );
          			}
          		}
          	if(Session::get('service_id') == RELOCATION_OFFICE_MOVE){
          			$sellerpost = new RelocationofficeSellerPost();
          			$sellerpost->lkp_service_id = RELOCATION_OFFICE_MOVE;
          		}

          		$sellerpost->from_date = CommonComponent::convertDateForDatabase($_POST ['valid_from']);
          		$sellerpost->to_date = CommonComponent::convertDateForDatabase($_POST ['valid_to']);
          		
          		$sellerpost->from_location_id = $_POST ['from_location_id'];
          		$sellerpost->seller_district_id = $_POST ['seller_district_id'];
          		$sellerpost->total_inventory_volume = 0;
          		$sellerpost->rate_per_cft = $request->rate_per_cft;
          		$sellerpost->tracking = $request->tracking;
          		$sellerpost->terms_conditions = $request->terms_conditions;
          		$sellerpost->lkp_payment_mode_id = $request->paymentterms;
          		$sellerpost->credit_period = $request->credit_period_ptl;
          		$sellerpost->credit_period_units = $request->credit_period_units;
          		
          		if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
          			$sellerpost->lkp_access_id = 2;
          		} else {
          			$sellerpost->lkp_access_id = 1;
          		}
          		$sellerpost->seller_id = Auth::id ();
          		$sellerpost->transaction_id = $randnumber;
          		
          		if (Input::get ( 'confirm' ) == 'Confirm' && $_POST['sellerpoststatus'] == 1) {
          			$lkp_post_status_id = 2;
          		} else {
          			$lkp_post_status_id = 1;
          		}
          		$sellerpost->lkp_post_status_id = $lkp_post_status_id;
          		$sellerpost->cancellation_charge_text = "cancellation Charges";
          		if(isset($_POST['cancellation_charge_price']))
          			$sellerpost->cancellation_charge_price = $request->cancellation_charge_price;

                $sellerpost->docket_charge_text = "docket Charges";
                if(isset($_POST['docket_charge_price']))
                    $sellerpost->docket_charge_price = $request->docket_charge_price;

          		$f=1;
          		$ft=1;
          		for($i=1;$i<=$request->next_terms_count_search;$i++){
          			if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] != '') {
          				$field_name="other_charge".$f."_text";
          				$sellerpost->$field_name = $_POST['labeltext_'.$i];
          				$f++;
          			}
          			if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] == '') {
          				$f++;
          			}
          			if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] != '') {
          				$field_name="other_charge".$ft."_price";
          				$sellerpost->$field_name = $_POST['terms_condtion_types_'.$i];
          				$ft++;
          			}
          			if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] == '') {
          				$ft++;
          			}
          		
          		}
          		
          		
          		if (is_array ( $request->accept_payment_ptl )) {
          			$sellerpost->accept_payment_netbanking = in_array ( 1, $request->accept_payment_ptl ) ? 1 : 0;
          			$sellerpost->accept_payment_credit = in_array ( 2, $request->accept_payment_ptl ) ? 1 : 0;
          			$sellerpost->accept_payment_debit = in_array ( 3, $request->accept_payment_ptl ) ? 1 : 0;
          		} else {
          			$sellerpost->accept_payment_netbanking = 0;
          			$sellerpost->accept_payment_credit = 0;
          			$sellerpost->accept_payment_debit = 0;
          		}
          		
          		
          		if (is_array ( $request->accept_credit_netbanking )) {
          			$sellerpost->accept_credit_netbanking = in_array ( 1, $request->accept_credit_netbanking ) ? 1 : 0;
          			$sellerpost->accept_credit_cheque = in_array ( 2, $request->accept_credit_netbanking ) ? 1 : 0;
          		} else {
          			$sellerpost->accept_credit_netbanking = 0;
          			$sellerpost->accept_credit_cheque = 0;
          		}
          		//echo "<pre>";print_R($_POST);
          		//echo $multi_vehicle_data;die;
          		//print_R($sellerpost);
          		//die("test");
          		$created_at = date ( 'Y-m-d H:i:s' );
          		$createdIp = $_SERVER ['REMOTE_ADDR'];
          		$sellerpost->created_by = Auth::id ();
          		$sellerpost->created_at = $created_at;
          		$sellerpost->created_ip = $createdIp;
                $sellerpost->save ();

                $sid=$sellerpost->id;
          		$sellerpost_lineitem_slab_default = new RelocationofficeSellerPostSlab();
          		$sellerpost_lineitem_slab_default->slab_min_km = $_POST['min_distance_slab'];
          		$sellerpost_lineitem_slab_default->slab_max_km = $_POST['max_distance_slab'];
          		$sellerpost_lineitem_slab_default->transport_price = $_POST['transport_charges_slab'];
          		
          		$sellerpost_lineitem_slab_default->seller_post_id = $sid;
          		$sellerpost_lineitem_slab_default->seller_id = Auth::id ();
          		$created_at = date ( 'Y-m-d H:i:s' );
          		$createdIp = $_SERVER ['REMOTE_ADDR'];
          		$sellerpost_lineitem_slab_default->created_by = Auth::id ();
          		$sellerpost_lineitem_slab_default->created_at = $created_at;
          		$sellerpost_lineitem_slab_default->created_ip = $createdIp;
          		$sellerpost_lineitem_slab_default->save ();
          		$low_price=0;
          		$high_price=0;
          		$actual_price=0;
          		for($i=1;$i<=$request->price_slap_hidden_value;$i++){
          			
          			
          			 $sellerpost_lineitem_slab = new RelocationofficeSellerPostSlab();
          			
          			if (isset ( $_POST['min_distance_slab_'.$i] ) && $_POST['min_distance_slab_'.$i] != '') {
          				$sellerpost_lineitem_slab->slab_min_km = $_POST['min_distance_slab_'.$i];
          				$low_price++;
          			}
          			if (isset ( $_POST['min_distance_slab_'.$i] ) && $_POST['min_distance_slab_'.$i] == '') {
          				$low_price++;
          			}
          			
          			if (isset ( $_POST['max_distance_slab_'.$i] ) && $_POST['max_distance_slab_'.$i] != '') {
          				$sellerpost_lineitem_slab->slab_max_km = $_POST['max_distance_slab_'.$i];
          				$high_price++;
          			}
          			if (isset ( $_POST['max_distance_slab_'.$i] ) && $_POST['max_distance_slab_'.$i] == '') {
          				$high_price++;
          			}
          			
          			if (isset ( $_POST['transport_charges_slab_'.$i] ) && $_POST['transport_charges_slab_'.$i] != '') {
          				$sellerpost_lineitem_slab->transport_price = $_POST['transport_charges_slab_'.$i];
          				$actual_price++;
          			}
          			if (isset ( $_POST['transport_charges_slab_'.$i] ) && $_POST['transport_charges_slab_'.$i] == '') {
          				$actual_price++;
          			}
          			$sellerpost_lineitem_slab->seller_post_id = $sid;
          			$sellerpost_lineitem_slab->seller_id = Auth::id ();
          			$created_at = date ( 'Y-m-d H:i:s' );
          			$createdIp = $_SERVER ['REMOTE_ADDR'];
          			$sellerpost_lineitem_slab->created_by = Auth::id ();
          			$sellerpost_lineitem_slab->created_at = $created_at;
          			$sellerpost_lineitem_slab->created_ip = $createdIp;
          			$sellerpost_lineitem_slab->save ();
          			
          		}
          		
                if($lkp_post_status_id==2){
              		//*******matching engine***********************//
              		$request = array();
              		$request['from_location_id'] = $_POST ['from_location_id'];
              		$request['valid_from'] = CommonComponent::convertDateForDatabase($_POST ['valid_from']);
              		$request['valid_to'] = CommonComponent::convertDateForDatabase($_POST ['valid_to']);
              		SellerMatchingComponent::doMatching(RELOCATION_OFFICE_MOVE, $sellerpost->id, 2, $request);
              		//*******matching engine***********************//
                }          		
          		
          		if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
          			if (isset ( $_POST ['buyer_list_for_sellers'] ) && $_POST ['buyer_list_for_sellers'] != '') {
          				for($i = 0; $i < $buyer_list_count; $i ++) {
          		
          					if(Session::get('service_id') == RELOCATION_OFFICE_MOVE){
          						$sellerpost_for_buyers = new RelocationofficeSellerSelectedBuyer();
          					}
          		
          					$sellerpost_for_buyers->seller_post_id = $sellerpost->id;
          					$sellerpost_for_buyers->buyer_id = $buyer_list [$i];
          					$created_at = date ( 'Y-m-d H:i:s' );
          					$createdIp = $_SERVER ['REMOTE_ADDR'];
          					$sellerpost_for_buyers->created_by = Auth::id ();
          					$sellerpost_for_buyers->created_at = $created_at;
          					$sellerpost_for_buyers->created_ip = $createdIp;
          					$sellerpost_for_buyers->save ();
          					$seller_selected_buyers_email = DB::table ( 'users' )->where ( 'id', $buyer_list [$i] )->get ();
          					$seller_selected_buyers_email [0]->randnumber = $randnumber;
          					$seller_selected_buyers_email [0]->sellername = Auth::User ()->username;
          					CommonComponent::send_email ( SELLER_CREATED_POST_FOR_BUYERS, $seller_selected_buyers_email );
          					// CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
          		
          					//*******Send Sms to the private buyers***********************//
          					if($lkp_post_status_id == OPEN){
          						$msg_params = array(
          								'randnumber' => $randnumber,
          								'sellername' => Auth::User()->username,
          								'servicename' => RELOCATIONOFFICE_SELLER_SMS_SERVICENAME
          						);
          						$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
          						if($getMobileNumber)
          							CommonComponent::sendSMS($getMobileNumber,SELLER_CREATED_POST_FOR_BUYERS_SMS,$msg_params);
          					}
          					//*******Send Sms to the private buyers***********************//
          		
          		
          		
          				}
          			}
          		}
          	if (Input::get ( 'confirm' ) == 'Confirm' && $_POST['sellerpoststatus'] == 1) {
          			return $randnumber;
          		} else {
          			return redirect ( '/sellerlist' )->with ( 'message_create_post_ptl', 'Post was saved as draft' );
          		}
          		
          	}else{
          		
          		$randnumber=0;
          		if (Input::get ( 'confirm' ) == 'Confirm' && $_POST['sellerpoststatus'] == 1) {
          			return $randnumber;
          		} else {
          			return redirect ( '/relocation/createsellerpost' )->with ( 'message_create_post_duplicate','Post already exist with details');
          		}
          		
          	
          	}
          	  
          	}
          break;
        case RELOCATION_PET_MOVE:
           return $this->relocationpetCreatePost($request);
        case RELOCATION_GLOBAL_MOBILITY:
           return $this->relocationGmCreatePost($request);    
          
        }
        } catch ( Exception $e ) {
            echo 'Caught exception: ', $e->getMessage (), "\n";
        }
    }
    public function relocationpetCreatePost($request){
        
       
            Session::put('session_delivery_date_ptl','');
            Session::put('session_dispatch_date_ptl','');
            Session::put('session_vehicle_type_ptl','');
            Session::put('session_load_type_ptl','');
            Session::put('session_from_city_id_ptl','');
            Session::put('session_to_city_id_ptl','');
            Session::put('session_from_location_ptl','');
            Session::put('session_to_location_ptl','');

            $roleId = Auth::User ()->lkp_role_id;
            if ($roleId == SELLER) {
                CommonComponent::activityLog ( "SELLER_CREATED_POSTS", SELLER_CREATED_POSTS, 0, HTTP_REFERRER, CURRENT_URL );
            }
            if (! empty ( Input::all () )) {
                
                if (isset ( $_POST ['optradio'] )) {
                    $is_private = $_POST ['optradio'];
                }
                //$randnumber_value = rand ( 11111, 99999 );
                $postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));

                $created_year = date('Y');
                $randnumber = 'RELOCATIONPET/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
                
                $multi_pet_data = $_POST ['pet_items'];
                
                if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
                    if (isset ( $_POST ['buyer_list_for_sellers'] ) && $_POST ['buyer_list_for_sellers'] != '') {
                        $buyer_list = explode ( ",", $_POST ['buyer_list_for_sellers'] );
                        array_shift ( $buyer_list );
                        $buyer_list_count = count ( $buyer_list );
                    }
                }
                
                $sellerpost = new RelocationpetSellerPost();
                $sellerpost->lkp_service_id = RELOCATION_PET_MOVE;
                $sellerpost->from_date = CommonComponent::convertDateForDatabase($_POST ['valid_from_val']);
                $sellerpost->to_date = CommonComponent::convertDateForDatabase($_POST ['valid_to_val']);
                $sellerpost->from_location_id = $_POST ['from_location_id'];
                $sellerpost->to_location_id = $_POST ['to_location_id'];
                $sellerpost->seller_district_id = $_POST ['seller_district_id'];
                
                $sellerpost->tracking = $request->tracking;
                $sellerpost->terms_conditions = $request->terms_conditions;
                $sellerpost->lkp_payment_mode_id = $request->paymentterms;
                $sellerpost->credit_period = $request->credit_period_ptl;
                $sellerpost->credit_period_units = $request->credit_period_units;

                if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
                    $sellerpost->lkp_access_id = 2;
                } else {
                    $sellerpost->lkp_access_id = 1;
                }
                $sellerpost->seller_id = Auth::id ();
                $sellerpost->transaction_id = $randnumber;
                if (Input::get ( 'confirm' ) == 'Confirm' && $_POST['sellerpoststatus'] == 1) {
                    $lkp_post_status_id = 2;
                } else {
                    $lkp_post_status_id = 1;
                }

                $sellerpost->lkp_post_status_id = $lkp_post_status_id;
                $sellerpost->cancellation_charge_text = "cancellation Charges";
                if(isset($_POST['cancellation_charge_price']))
                $sellerpost->cancellation_charge_price = $request->cancellation_charge_price;
                $sellerpost->docket_charge_text = "Other Charges";
                if(isset($_POST['docket_charge_price']))
                $sellerpost->docket_charge_price = $request->docket_charge_price;
                $f=1;
                $ft=1;
                for($i=1;$i<=$request->next_terms_count_search;$i++){
                    if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] != '') {
                        $field_name="other_charge".$f."_text";
                        $sellerpost->$field_name = $_POST['labeltext_'.$i];
                        $f++;
                    }
                    if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] == '') {
                        $f++;
                    }
                    if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] != '') {
                        $field_name="other_charge".$ft."_price";
                        $sellerpost->$field_name = $_POST['terms_condtion_types_'.$i];
                        $ft++;
                    }
                    if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] == '') {
                        $ft++;
                    }

                }
                if (is_array ( $request->accept_payment_ptl )) {
                    $sellerpost->accept_payment_netbanking = in_array ( 1, $request->accept_payment_ptl ) ? 1 : 0;
                    $sellerpost->accept_payment_credit = in_array ( 2, $request->accept_payment_ptl ) ? 1 : 0;
                    $sellerpost->accept_payment_debit = in_array ( 3, $request->accept_payment_ptl ) ? 1 : 0;
                } else {
                    $sellerpost->accept_payment_netbanking = 0;
                    $sellerpost->accept_payment_credit = 0;
                    $sellerpost->accept_payment_debit = 0;
                }
                if (is_array ( $request->accept_credit_netbanking )) {
                    $sellerpost->accept_credit_netbanking = in_array ( 1, $request->accept_credit_netbanking ) ? 1 : 0;
                    $sellerpost->accept_credit_cheque = in_array ( 2, $request->accept_credit_netbanking ) ? 1 : 0;
                } else {
                    $sellerpost->accept_credit_netbanking = 0;
                    $sellerpost->accept_credit_cheque = 0;
                }
               
                $created_at = date ( 'Y-m-d H:i:s' );
                $createdIp = $_SERVER ['REMOTE_ADDR'];
                $sellerpost->created_by = Auth::id ();
                $sellerpost->created_at = $created_at;
                $sellerpost->created_ip = $createdIp;
                
                if ($sellerpost->save ()) {

                    // CommonComponent::auditLog($sellerpost->id,'seller_posts');
                    for($i = 0; $i < $multi_pet_data; $i ++) {
                        
                        $sellerpost_lineitem = new RelocationpetSellerPostItem();
                        $sellerpost_lineitem->seller_post_id = $sellerpost->id;
                        $sellerpost_lineitem->lkp_pet_type_id = $_POST ['pettypes_hidden'] [$i];
                        $sellerpost_lineitem->lkp_cage_type_id = $_POST ['cagetypes_hidden'] [$i];
                        $sellerpost_lineitem->rate_per_cft = $_POST ['freight_hidden'] [$i];
                        $sellerpost_lineitem->transitdays = $_POST ['transit_days_hidden'] [$i];
                        $sellerpost_lineitem->units = $_POST ['transitdays_units_relocation_hidden'] [$i];
                        $sellerpost_lineitem->od_charges = $_POST ['od_charges_hidden'] [$i];
                        $created_at = date ( 'Y-m-d H:i:s' );
                        $createdIp = $_SERVER ['REMOTE_ADDR'];
                        $sellerpost_lineitem->created_by = Auth::id ();
                        $sellerpost_lineitem->created_at = $created_at;
                        $sellerpost_lineitem->created_ip = $createdIp;
                        $sellerpost_lineitem->save ();

                        //*******matching engine***********************//
                        $request = array();
                        $request['from_location_id'] = $_POST ['from_location_id'];
                        $request['to_location_id'] = $_POST ['to_location_id'];
                        $request['post_type'] = 1;
                        $request['pettypes'] = $_POST ['pettypes_hidden'] [$i];
                        $request['cagetypes'] = $_POST ['cagetypes_hidden'] [$i];
                        $request['valid_from'] = CommonComponent::convertDateForDatabase($_POST ['valid_from_val']);
                        $request['valid_to'] = CommonComponent::convertDateForDatabase($_POST ['valid_to_val']);
                        $request['transit_days'] = $_POST ['transit_days_hidden'] [$i];
                        SellerMatchingComponent::doMatching(RELOCATION_PET_MOVE, $sellerpost->id, 2, $request);
                        //*******matching engine***********************//
                    }

                    if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
                        if (isset ( $_POST ['buyer_list_for_sellers'] ) && $_POST ['buyer_list_for_sellers'] != '') {
                            for($i = 0; $i < $buyer_list_count; $i ++) {

                                $sellerpost_for_buyers = new RelocationpetSellerSelectedBuyer();
                                
                                $sellerpost_for_buyers->seller_post_id = $sellerpost->id;
                                $sellerpost_for_buyers->buyer_id = $buyer_list [$i];
                                $created_at = date ( 'Y-m-d H:i:s' );
                                $createdIp = $_SERVER ['REMOTE_ADDR'];
                                $sellerpost_for_buyers->created_by = Auth::id ();
                                $sellerpost_for_buyers->created_at = $created_at;
                                $sellerpost_for_buyers->created_ip = $createdIp;
                                $sellerpost_for_buyers->save ();
                                $seller_selected_buyers_email = DB::table ( 'users' )->where ( 'id', $buyer_list [$i] )->get ();
                                $seller_selected_buyers_email [0]->randnumber = $randnumber;
                                $seller_selected_buyers_email [0]->sellername = Auth::User ()->username;
                                CommonComponent::send_email ( SELLER_CREATED_POST_FOR_BUYERS, $seller_selected_buyers_email );
                                // CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
                                
                                //*******Send Sms to the private buyers***********************//
                                if($lkp_post_status_id == OPEN){
                                	$msg_params = array(
                                			'randnumber' => $randnumber,
                                			'sellername' => Auth::User()->username,
                                			'servicename' => "RELOCATION PET"
                                	);
                                	$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
                                	if($getMobileNumber)
                                		CommonComponent::sendSMS($getMobileNumber,SELLER_CREATED_POST_FOR_BUYERS_SMS,$msg_params);
                                }
                                //*******Send Sms to the private buyers***********************//
                                
                                
                                
                            }
                        }
                    }

                    if (Input::get ( 'confirm' ) == 'Confirm' && $_POST['sellerpoststatus'] == 1) {
                        return $randnumber;
                    } else {
                        return redirect ( '/sellerlist' )->with ( 'message_create_post_ptl', 'Post was saved as draft' );
                    }
                }
            }
         
    }

    public function relocationUpdateSellerPost(Request $request, $sid) {

        Log::info ( 'create seller function used for updating a posts: ' . Auth::id (), array (
            'c' => '1'
        ) );

        try 
        {
            $serviceId = Session::get('service_id');
            switch($serviceId)
            { 
                case RELOCATION_DOMESTIC:

                    if(Session::get('service_id') == RELOCATION_DOMESTIC){
                        $sellerpost = new RelocationSellerPost();
                    }

                    if (! empty ( Input::all () )) 
                    {

                        if (Input::get ( 'confirm' ) == 'Confirm' && $_POST['sellerpoststatus'] == 1) {
                            $poststatus = 2;
                        } else {
                            $poststatus = 1;
                        }
                        if(isset($_POST['optradio'])){
                            $is_private = $_POST['optradio'];
                        }
                        if(isset($_POST['optradio']) && $is_private == 2){
                            if(isset($_POST['buyer_list_for_sellers_hidden']) && $_POST['buyer_list_for_sellers_hidden'] != ''){
                                $buyer_list = explode(",", $_POST['buyer_list_for_sellers_hidden']);
                                array_shift($buyer_list);
                                $buyer_list_count = count($buyer_list);
                            }
                        }
                        $randnumber_value = rand(11111,99999);
                        $randnumber = "FTL20000".$randnumber_value;
                        //echo "<pre>"; print_R($buyer_list);die;
                        if(isset($_POST['optradio']) && $is_private == 2){
                            $lkp_access_id = 2;
                        }else{
                            $lkp_access_id = 1;
                        }

                        if (isset ( $_POST ['accept_payment_ptl'] ) && is_array ( $_POST ['accept_payment_ptl'] )) {
                            $accept_payment_netbanking = in_array ( 1, $_POST ['accept_payment_ptl'] ) ? 1 : 0;
                            $accept_payment_credit = in_array ( 2, $_POST ['accept_payment_ptl'] ) ? 1 : 0;
                            $accept_payment_debit = in_array ( 3, $_POST ['accept_payment_ptl'] ) ? 1 : 0;
                        } else {
                            $accept_payment_netbanking = 0;
                            $accept_payment_credit = 0;
                            $accept_payment_debit = 0;
                        }

                        if (isset ( $_POST ['accept_credit_netbanking'] ) && is_array ( $_POST ['accept_credit_netbanking'] )) {
                            $accept_credit_netbanking = in_array ( 1, $_POST ['accept_credit_netbanking'] ) ? 1 : 0;
                            $accept_credit_cheque = in_array ( 2, $_POST ['accept_credit_netbanking'] ) ? 1 : 0;
                        } else {
                            $accept_credit_netbanking = 0;
                            $accept_credit_cheque = 0;
                        }



                        $otherCharges = array();
                        if(isset($_POST['next_terms_count_search'])){
                            $j = 0;
                            for($i=1;$i<=$_POST['next_terms_count_search'];$i++){
                                if(isset($_POST["labeltext_$i"]) && isset($_POST["terms_condtion_types_$i"])){
                                    $otherCharges["labeltext"][$j] = $_POST["labeltext_$i"];
                                    $otherCharges["terms_condtion_types"][$j] = $_POST["terms_condtion_types_$i"];
                                    $j++;
                                }
                            }
                        }
                       // echo "<pre>";print_R($request->input);print_R($_POST);die;

                    if(isset($_POST['sellerpoststatus_previous']) && ($_POST['sellerpoststatus_previous'] == 1 || $_POST['sellerpoststatus_previous'] == 2)){
                            $arr = array (
                                'to_date' => CommonComponent::convertDateForDatabase($request->input ( 'valid_to' )),
                                'packing_loading' => 0,
                                'crating_charges' => $request->input ( 'crating_charges' ),
                                'unloading_delivery_unpack' => 0,
                                'storate_charges' => $request->input ( 'storate_charges' ),
                                'escort_charges' => $request->input ( 'escort_charges' ),
                                'handyman_charges' => $request->input ( 'handyman_charges' ),
                                'property_search' => $request->input ( 'property_search' ),
                                'setting_service' => 0,
                                'brokerage' => $request->input ( 'brokerage' ),
                                'tracking' => $request->input ( 'tracking' ),
                                'terms_conditions' => $request->input ( 'terms_conditions' ),
                                'lkp_payment_mode_id' => $request->input ( 'paymentterms' ),
                                'accept_payment_netbanking' => $accept_payment_netbanking,
                                'accept_payment_credit' => $accept_payment_credit,
                                'accept_payment_debit' => $accept_payment_debit,
                                'credit_period' => $request->input ( 'credit_period_ptl' ),
                                'credit_period_units' => $request->input ( 'credit_period_units' ),
                                'accept_credit_netbanking' => $accept_credit_netbanking,
                                'accept_credit_cheque' => $accept_credit_cheque,
                                'credit_period' => $request->input ( 'credit_period_ptl' ),
                                'credit_period_units' => $request->input ( 'credit_period_units' ),
                                'other_charge1_text' => (isset ( $otherCharges ['labeltext'] ['0'] )) ? $otherCharges ['labeltext'] ['0'] : "",
                                'other_charge2_text' => (isset ( $otherCharges ['labeltext'] ['1'] )) ? $otherCharges ['labeltext'] ['1'] : "",
                                'other_charge3_text' => (isset ( $otherCharges ['labeltext'] ['2'] )) ? $otherCharges ['labeltext'] ['2'] : "",
                                'cancellation_charge_price' => (isset ( $_POST ['cancellation_charge_price'] )) ? $_POST ['cancellation_charge_price'] : "",
                                'docket_charge_price' => (isset ( $_POST ['docket_charge_price'] )) ? $_POST ['docket_charge_price'] : "",
                                'other_charge1_price' => (isset ( $otherCharges ['terms_condtion_types'] [0] )) ? $otherCharges ['terms_condtion_types'] [0] : "",
                                'other_charge2_price' => (isset ( $otherCharges ['terms_condtion_types'] [1] )) ? $otherCharges ['terms_condtion_types'] [1] : "",
                                'other_charge3_price' => (isset ( $otherCharges ['terms_condtion_types'] [2] )) ? $otherCharges ['terms_condtion_types'] [2] : "",
                                'lkp_post_status_id' => $poststatus,
                                'lkp_access_id' => $lkp_access_id
                            );
                        }else{
                            $arr = array (
                                'to_date' => CommonComponent::convertDateForDatabase($request->input ( 'valid_to' )),
                            );
                        }
                        if($_POST['sellerpoststatus_previous'] == 2){
                            unset($arr['tracking']);    unset($arr['lkp_payment_mode_id']);    unset($arr['accept_payment_netbanking']);
                            unset($arr['accept_payment_credit']);    unset($arr['accept_payment_debit']);    unset($arr['credit_period']);
                            unset($arr['credit_period_units']);    unset($arr['accept_credit_netbanking']);    unset($arr['accept_credit_cheque']);
                            unset($arr['credit_period']);    unset($arr['credit_period_units']);
                        }


                        $sellerpost::where ( "id", $sid )->update ($arr);
                        $multi_household_data = $_POST ['household_items'];
                        $multi_vehicle_data = $_POST ['vehicle_items'];

                        for($i = 0; $i < $multi_household_data; $i++) {
                            if (Session::get('service_id') == RELOCATION_DOMESTIC) {
                                $sellerpost_lineitem = new RelocationSellerPostItem();
                            }
                            $sellerpost_lineitem::where("id", $_POST['property_post_id'][$i])->update(array(
                                'lkp_property_type_id' => $_POST['propertytypes_hidden'][$i],
                                'lkp_load_category_id' => $_POST['load_types_hidden'][$i],
                                'volume' => $_POST['volume_hidden'][$i],
                                'rate_per_cft' => $_POST['rate_per_cft_hidden'][$i],
                                'transitdays' => $_POST['transit_days_hidden'][$i],
                                'units' => $_POST['transitdays_units_relocation_hidden'][$i],
                                'transport_charges' => $_POST['transport_charges_hidden'][$i]
                            ));

                            //*******matching engine***********************//
                            if($poststatus == 2){
                                $request = array();
                                $request['from_location_id'] = $_POST ['from_location_id'];
                                $request['to_location_id'] = $_POST ['to_location_id'];
                                $request['post_type'] = 1;
                                $request['propertytypes'] = $_POST ['propertytypes_hidden'] [$i];
                                $request['valid_from'] = $_POST ['valid_from_hidden'];
                                $request['valid_to'] = CommonComponent::convertDateForDatabase($_POST ['valid_to']);
                                $request['transit_days'] = $_POST['transit_days_hidden'][$i];
                                SellerMatchingComponent::doMatching(RELOCATION_DOMESTIC, $sid, 2, $request);
                            }
                            //*******matching engine***********************//
                        }

                        for($i = 0; $i < $multi_vehicle_data; $i ++) {
                            if (Session::get('service_id') == RELOCATION_DOMESTIC) {
                                $sellerpost_lineitem = new RelocationSellerPostItem();
                            }
                            $sellerpost_lineitem::where("id", $_POST ['vehicle_post_id'] [$i])->update(array(
                                'lkp_vehicle_category_id' => $_POST['vehicle_types_hidden'][$i],
                                'lkp_car_size' => $_POST['vehicle_type_category_hidden'][$i],
                                'cost' => $_POST ['cost_hidden'] [$i],
                                'transitdays' => $_POST ['transit_days_vehicle_hidden'] [$i],
                                'units' => $_POST['transitdays_units_relocation_vehicle_hidden'][$i],
                                'transport_charges' => $_POST['transport_charges_vehicle_hidden'][$i]
                            ));
                            //*******matching engine***********************//
                            if($poststatus == 2){
                                $request = array();
                                $request['from_location_id'] = $_POST ['from_location_id'];
                                $request['to_location_id'] = $_POST ['to_location_id'];
                                $request['post_type'] = 2;
                                $request['vehicle_category'] = $_POST ['vehicle_types_hidden'][$i];
                                $request['valid_from'] = $_POST ['valid_from_hidden'];
                                $request['valid_to'] = CommonComponent::convertDateForDatabase($_POST ['valid_to']);
                                $request['transit_days'] = $_POST ['transit_days_vehicle_hidden'] [$i];
                                SellerMatchingComponent::doMatching(RELOCATION_DOMESTIC, $sid, 2, $request);
                                //echo "<pre> $sid";print_R($request);die;
                            }
                            //*******matching engine***********************//
                        }

                        if(isset($_POST['optradio']) && $is_private == 2){
                            if(isset($_POST['buyer_list_for_sellers_hidden']) && $_POST['buyer_list_for_sellers_hidden'] != ''){
                                $post_list_of_buyers = DB::table('relocation_seller_selected_buyers')->where('seller_post_id', $sid)->lists('buyer_id');
                                DB::table('relocation_seller_selected_buyers')->where('seller_post_id', $sid)->delete();
                                for($i = 0; $i < $buyer_list_count; $i ++) {
                                    if(Session::get('service_id') == RELOCATION_DOMESTIC){
                                        $sellerpost_for_buyers  =  new RelocationSellerSelectedBuyer();
                                    }

                                    $sellerpost_for_buyers->seller_post_id = $sid;
                                    $sellerpost_for_buyers->buyer_id = $buyer_list[$i];
                                    $created_at = date ( 'Y-m-d H:i:s' );
                                    $createdIp = $_SERVER ['REMOTE_ADDR'];
                                    $sellerpost_for_buyers->created_by = Auth::id();
                                    $sellerpost_for_buyers->created_at = $created_at;
                                    $sellerpost_for_buyers->created_ip = $createdIp;
                                    $sellerpost_for_buyers->save();
                                    if (!in_array($buyer_list[$i], $post_list_of_buyers)){
                                        $seller_selected_buyers_email = DB::table('users')->where('id', $buyer_list[$i])->get();
                                        $seller_selected_buyers_email[0]->randnumber = $randnumber;
                                        $seller_selected_buyers_email[0]->sellername = Auth::User()->username;
                                        CommonComponent::send_email(SELLER_CREATED_POST_FOR_BUYERS,$seller_selected_buyers_email);
                                    }
                                    //CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
                                    
                                    
                                    if($poststatus == OPEN){
                                        //*******Send Sms to the private buyers***********************//
                                        $msg_params = array(
                                                'randnumber' => $randnumber,
                                                'sellername' => Auth::User()->username,
                                                'servicename' => 'RELOCATION DOMESTIC'
                                        );
                                        $getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
                                        if($getMobileNumber)
                                            CommonComponent::sendSMS($getMobileNumber,SELLER_UPDATED_POST_FOR_BUYERS_SMS,$msg_params);
                                        //*******Send Sms to the private buyers***********************//
                                    }
                                    
                                }
                            }
                        }

                        if($poststatus == 2)
                            return redirect ( "/sellerlist" )->with ( 'message_update_post', 'Post Updated Successfully' );
                        else
                            return redirect ( "/sellerlist" )->with ( 'message_update_post', 'Post Updated Successfully' );
                    }

                    // Retrieval of payment methods
                    $payment_methods = CommonComponent::getPaymentTerms ();
                    $ratecardTypes = CommonComponent::getAllRatecardTypes();
                    $propertyTypes = CommonComponent::getAllPropertyTypes();
                    $loadtypes =  CommonComponent::getAllLoadCategories();
                    $vehicletypes = CommonComponent::getAllVehicleCategories();
                    $vehicletypecategories = CommonComponent::getAllVehicleCategoryTypes();
                    $trackingtypes = CommonComponent::getTrackingTypes();

                    if(Session::get('service_id') == RELOCATION_DOMESTIC){
                        $seller_post_edit_action = DB::table ( 'relocation_seller_posts' )->where ( 'relocation_seller_posts.id', $sid )->select ( 'relocation_seller_posts.*' )->first ();
                        $seller_post_edit_action_lines = DB::table ( 'relocation_seller_post_items' )
                                                                ->where ( 'relocation_seller_post_items.seller_post_id', $sid )
                                                                ->select ( 'relocation_seller_post_items.*' )->get ();

                        $selectedbuyers = DB::table ( 'relocation_seller_selected_buyers' )
                                            ->leftjoin ( 'users as u', 'relocation_seller_selected_buyers.buyer_id', '=', 'u.id' )
                                            ->leftjoin ( 'buyer_business_details as bbds', 'relocation_seller_selected_buyers.buyer_id', '=', 'bbds.user_id' )
                                            ->where ( 'relocation_seller_selected_buyers.seller_post_id', $sid )
                                            ->select ( 'relocation_seller_selected_buyers.buyer_id', 'u.username', 'bbds.principal_place' )->get ();
                    }
                    //echo "<pre>";print_R($seller_post_edit_action);print_R($seller_post_edit_action_lines);die;

                    $userId = Auth::User ()->id;
                    $user_subcsriptions = DB::table ( 'seller_details' )->where ( 'user_id', $userId )->first ();

                    if ($user_subcsriptions) {
                        $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                        $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                        $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                        $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                        $current_date_seller = date ( "Y-m-d" );
                    } else {
                        $user_subcsriptions = DB::table ( 'sellers' )->where ( 'user_id', $userId )->first ();
                        $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                        $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                        $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                        $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                        $current_date_seller = date ( "Y-m-d" );
                    }

                    if (isset ( $seller_post_edit_action->lkp_access_id ) && $seller_post_edit_action->lkp_access_id == 1) {
                        $private_seller = false;
                        $public_seller = true;
                    } else {
                        $private_seller = true;
                        $public_seller = false;
                    }



                    return view ( 'relocation.sellers.updatepost', [
                        'seller_post_edit' => $seller_post_edit_action,
                        'seller_post_edit_action_lines' => $seller_post_edit_action_lines,
                        'private' => $private_seller,
                        'public' => $public_seller,
                        'paymentterms' => $payment_methods,
                        'ratecardtypes' => $ratecardTypes,
                        'propertytypes' => $propertyTypes,
                        'loadtypes' => $loadtypes,
                        'vehicletypes' => $vehicletypes,
                        'vehicletypecategories' => $vehicletypecategories,
                        'seller_postid' => $sid,
                        'selectedbuyers' => $selectedbuyers,
                        'subscription_start_date_start' => $subscription_start_date_start,
                        'subscription_end_date_end' => $subscription_end_date_end,
                        'current_date_seller' => $current_date_seller,
                        'trackingtypes'=> $trackingtypes
                    ] );
                break;
                case RELOCATION_OFFICE_MOVE:

                    $post_details = DB::table('relocationoffice_seller_posts')
                                            ->where('id',$sid)
                                            ->first();
                    $trackingtypes = CommonComponent::getTrackingTypes();
                    if (! empty ( Input::all () )) 
                    {
                        if (isset ( $_POST ['optradio'] )) {
                                $is_private = $_POST ['optradio'];
                        }

                        $randnumber_value = rand ( 11111, 99999 );
                        $postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));
                        $created_year = date('Y');
                        
                        if(Session::get('service_id') == RELOCATION_OFFICE_MOVE)
                        {
                            $randnumber = 'REL-OFF/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
                        }

                        if(isset($_POST['optradio']) && $is_private == 2){
                            if(isset($_POST['buyer_list_for_sellers_hidden']) && $_POST['buyer_list_for_sellers_hidden'] != ''){
                                $buyer_list = explode(",", $_POST['buyer_list_for_sellers_hidden']);
                                array_shift($buyer_list);
                                $buyer_list_count = count($buyer_list);
                            }
                        }

                        if(Session::get('service_id') == RELOCATION_OFFICE_MOVE)
                        {
                            $sellerpost = new RelocationofficeSellerPost();
                        }

                            $update_array['from_date'] = CommonComponent::convertDateForDatabase($_POST ['valid_from']);
                            $update_array['to_date'] = CommonComponent::convertDateForDatabase($_POST ['valid_to']);
                            
                            $update_array['from_location_id'] = $_POST ['from_location_id'];
                            $update_array['seller_district_id'] = $_POST ['seller_district_id'];
                            $update_array['total_inventory_volume'] = 0;
                            $update_array['rate_per_cft'] = $request->rate_per_cft;
                            if($request->tracking){
                                $update_array['tracking'] = $request->tracking;
                            }
                            $update_array['terms_conditions'] = $request->terms_conditions;
                            if($request->paymentterms){
                                $update_array['lkp_payment_mode_id'] = $request->paymentterms;
                            }
                            $update_array['credit_period'] = $request->credit_period_ptl;
                            $update_array['credit_period_units'] = $request->credit_period_units;
                            
                            if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
                                $update_array['lkp_access_id'] = 2;
                            } else {
                                $update_array['lkp_access_id'] = 1;
                            }
                            
                            if ($_POST['sellerpoststatus'] == 1) {
                                $lkp_post_status_id = 2;
                            } else {
                                $lkp_post_status_id = 1;
                            }

                            $update_array['lkp_post_status_id'] = $lkp_post_status_id;
                            $update_array['cancellation_charge_text'] = "cancellation Charges";
                            if(isset($_POST['cancellation_charge_price']))
                                $update_array['cancellation_charge_price'] = $request->cancellation_charge_price;

                            $update_array['docket_charge_text'] = "docket Charges";
                            if(isset($_POST['docket_charge_price']))
                                $update_array['docket_charge_price'] = $request->docket_charge_price;

                            $f=1;
                            $ft=1;
                            for($i=1;$i<=$request->next_terms_count_search;$i++){
                                if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] != '') {
                                    $field_name="other_charge".$f."_text";
                                    $update_array[$field_name] = $_POST['labeltext_'.$i];
                                    $f++;
                                }
                                if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] == '') {
                                    $f++;
                                }
                                if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] != '') {
                                    $field_name="other_charge".$ft."_price";
                                    $update_array[$field_name] = $_POST['terms_condtion_types_'.$i];
                                    $ft++;
                                }
                                if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] == '') {
                                    $ft++;
                                }
                            
                            }
                            
                            if($request->accept_payment_ptl){
                                if (is_array ( $request->accept_payment_ptl )) {
                                    $update_array['accept_payment_netbanking'] = in_array ( 1, $request->accept_payment_ptl ) ? 1 : 0;
                                    $update_array['accept_payment_credit'] = in_array ( 2, $request->accept_payment_ptl ) ? 1 : 0;
                                    $update_array['accept_payment_debit'] = in_array ( 3, $request->accept_payment_ptl ) ? 1 : 0;
                                } else {
                                    $update_array['accept_payment_netbanking'] = 0;
                                    $update_array['accept_payment_credit'] = 0;
                                    $update_array['accept_payment_debit'] = 0;
                                }
                            }                        
                                
                            if (is_array ( $request->accept_credit_netbanking )) {
                                $update_array['accept_credit_netbanking'] = in_array ( 1, $request->accept_credit_netbanking ) ? 1 : 0;
                                $update_array['accept_credit_cheque'] = in_array ( 2, $request->accept_credit_netbanking ) ? 1 : 0;
                            } else {
                                $update_array['accept_credit_netbanking'] = 0;
                                $update_array['accept_credit_cheque'] = 0;
                            }

                            
                            $created_at = date ( 'Y-m-d H:i:s' );
                            $createdIp = $_SERVER ['REMOTE_ADDR'];
                            $update_array['updated_by'] = Auth::id ();
                            $update_array['updated_at'] = $created_at;
                            $update_array['updated_ip'] = $createdIp;

                            $sellerpost::where('id',$sid)->update($update_array);

                            //Delete Previous line item slab for this post
                            DB::table('relocationoffice_seller_post_slabs')
                                ->where('seller_post_id',$sid)
                                ->delete();

                            $sellerpost_lineitem_slab_default = new RelocationofficeSellerPostSlab();
                            $sellerpost_lineitem_slab_default->slab_min_km = $_POST['min_distance_slab'];
                            $sellerpost_lineitem_slab_default->slab_max_km = $_POST['max_distance_slab'];
                            $sellerpost_lineitem_slab_default->transport_price = $_POST['transport_charges_slab'];
                            
                            $sellerpost_lineitem_slab_default->seller_post_id = $sid;
                            $sellerpost_lineitem_slab_default->seller_id = Auth::id ();
                            $created_at = date ( 'Y-m-d H:i:s' );
                            $createdIp = $_SERVER ['REMOTE_ADDR'];
                            $sellerpost_lineitem_slab_default->created_by = Auth::id ();
                            $sellerpost_lineitem_slab_default->created_at = $created_at;
                            $sellerpost_lineitem_slab_default->created_ip = $createdIp;
                            $sellerpost_lineitem_slab_default->save ();
                            $low_price=0;
                            $high_price=0;
                            $actual_price=0;
                            for($i=1;$i<=$request->price_slap_hidden_value;$i++){
                                
                                
                                 $sellerpost_lineitem_slab = new RelocationofficeSellerPostSlab();
                                
                                if (isset ( $_POST['min_distance_slab_'.$i] ) && $_POST['min_distance_slab_'.$i] != '') {
                                    $sellerpost_lineitem_slab->slab_min_km = $_POST['min_distance_slab_'.$i];
                                    $low_price++;
                                }
                                if (isset ( $_POST['min_distance_slab_'.$i] ) && $_POST['min_distance_slab_'.$i] == '') {
                                    $low_price++;
                                }
                                
                                if (isset ( $_POST['max_distance_slab_'.$i] ) && $_POST['max_distance_slab_'.$i] != '') {
                                    $sellerpost_lineitem_slab->slab_max_km = $_POST['max_distance_slab_'.$i];
                                    $high_price++;
                                }
                                if (isset ( $_POST['max_distance_slab_'.$i] ) && $_POST['max_distance_slab_'.$i] == '') {
                                    $high_price++;
                                }
                                
                                if (isset ( $_POST['transport_charges_slab_'.$i] ) && $_POST['transport_charges_slab_'.$i] != '') {
                                    $sellerpost_lineitem_slab->transport_price = $_POST['transport_charges_slab_'.$i];
                                    $actual_price++;
                                }
                                if (isset ( $_POST['transport_charges_slab_'.$i] ) && $_POST['transport_charges_slab_'.$i] == '') {
                                    $actual_price++;
                                }
                                $sellerpost_lineitem_slab->seller_post_id = $sid;
                                $sellerpost_lineitem_slab->seller_id = Auth::id ();
                                $created_at = date ( 'Y-m-d H:i:s' );
                                $createdIp = $_SERVER ['REMOTE_ADDR'];
                                $sellerpost_lineitem_slab->created_by = Auth::id ();
                                $sellerpost_lineitem_slab->created_at = $created_at;
                                $sellerpost_lineitem_slab->created_ip = $createdIp;
                                $sellerpost_lineitem_slab->save ();
                                
                            }
                          if($post_details->lkp_post_status_id!=2)  {
                            //*******matching engine***********************//
                            $request = array();
                            $request['from_location_id'] = $_POST ['from_location_id'];
                            $request['valid_from'] = CommonComponent::convertDateForDatabase($_POST ['valid_to']);
                            $request['valid_to'] = CommonComponent::convertDateForDatabase($_POST ['valid_to']);
                            SellerMatchingComponent::doMatching(RELOCATION_OFFICE_MOVE, $sellerpost->id, 2, $request);
                            //*******matching engine***********************//
                          }  
                            
                        if(isset($_POST['optradio']) && $is_private == 2){
                            if(isset($_POST['buyer_list_for_sellers_hidden']) && $_POST['buyer_list_for_sellers_hidden'] != ''){
                                $post_list_of_buyers = DB::table('relocationoffice_seller_selected_buyers')->where('seller_post_id', $sid)->lists('buyer_id');
                                DB::table('relocationoffice_seller_selected_buyers')->where('seller_post_id', $sid)->delete();
                                for($i = 0; $i < $buyer_list_count; $i ++) {
                                    if(Session::get('service_id') == RELOCATION_OFFICE_MOVE){
                                        $sellerpost_for_buyers = new RelocationofficeSellerSelectedBuyer();
                                    }

                                    $sellerpost_for_buyers->seller_post_id = $sid;
                                    $sellerpost_for_buyers->buyer_id = $buyer_list[$i];
                                    $created_at = date ( 'Y-m-d H:i:s' );
                                    $createdIp = $_SERVER ['REMOTE_ADDR'];
                                    $sellerpost_for_buyers->created_by = Auth::id();
                                    $sellerpost_for_buyers->created_at = $created_at;
                                    $sellerpost_for_buyers->created_ip = $createdIp;
                                    $sellerpost_for_buyers->save();
                                    if (!in_array($buyer_list[$i], $post_list_of_buyers)){
                                        $seller_selected_buyers_email = DB::table('users')->where('id', $buyer_list[$i])->get();
                                        $seller_selected_buyers_email[0]->randnumber = $randnumber;
                                        $seller_selected_buyers_email[0]->sellername = Auth::User()->username;
                                        CommonComponent::send_email(SELLER_CREATED_POST_FOR_BUYERS,$seller_selected_buyers_email);
                                    }
                                    //CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
                                    
                                    //*******Send Sms to the private buyers***********************//
                                    if($lkp_post_status_id == OPEN){
                                        $msg_params = array(
                                                'randnumber' => $randnumber,
                                                'sellername' => Auth::User()->username,
                                                'servicename' => RELOCATIONOFFICE_SELLER_SMS_SERVICENAME
                                        );
                                        $getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
                                        if($getMobileNumber)
                                            CommonComponent::sendSMS($getMobileNumber,SELLER_CREATED_POST_FOR_BUYERS_SMS,$msg_params);
                                    }
                                    //*******Send Sms to the private buyers***********************//
                                }
                            }
                        }

                            if ($_POST['sellerpoststatus'] == 1){
                                if($post_details->lkp_post_status_id == 1){
                                    return $randnumber;
                                }else{
                                    return redirect ( "/sellerlist" )->with ( 'message_update_post', 'Post Updated Successfully' );
                                }
                            }else{
                                return redirect ( '/sellerlist' )->with ( 'message_create_post_ptl', 'Post was saved as draft' );
                            }
                    }

                    if($post_details)
                    {
                        $payment_methods = CommonComponent::getPaymentTerms ();
                        $userId = Auth::User ()->id;
                        $url_search= explode("?",HTTP_REFERRER);
                        $url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);

                        if($userId!=$post_details->created_by)
                        {
                            return redirect ( "/sellerlist" )->with ( 'message_update_post_fail', 'Invalid Request' );
                        }
                        
                        /*if($post_details->lkp_post_status_id==2)
                        {
                            return redirect ( "/sellerlist" )->with ( 'message_update_post_fail', 'Confirm Post! Not Allowed to Update' );
                        }*/

                        $user_subcsriptions = DB::table ( 'seller_details' )->where ( 'user_id', $userId )->first ();
                        if ($user_subcsriptions) {
                            $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                            $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                            $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                            $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                            $current_date_seller = date ( "Y-m-d" );
                        } else {
                            $user_subcsriptions = DB::table ( 'sellers' )->where ( 'user_id', $userId )->first ();
                            $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                            $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                            $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                            $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                            $current_date_seller = date ( "Y-m-d" );
                        }
                        
                        // Get Seller post slabs
                        $post_slabs = DB::table('relocationoffice_seller_post_slabs')
                                    ->where('seller_post_id',$sid)
                                    ->get();
                        // Get Sellected Private Sellers List             
                        $selectedbuyers = DB::table ( 'relocationoffice_seller_selected_buyers' )
                                            ->leftjoin ( 'users as u', 'relocationoffice_seller_selected_buyers.buyer_id', '=', 'u.id' )
                                            ->leftjoin ( 'buyer_business_details as bbds', 'relocationoffice_seller_selected_buyers.buyer_id', '=', 'bbds.user_id' )
                                            ->where ( 'relocationoffice_seller_selected_buyers.seller_post_id', $sid )
                                            ->select ( 'relocationoffice_seller_selected_buyers.buyer_id', 'u.username', 'bbds.principal_place' )->get ();
                        
                        // Checking Public or private post
                        if (isset ( $post_details->lkp_access_id ) && $post_details->lkp_access_id == 1) {
                            $private_seller = false;
                            $public_seller = true;
                        } else {
                            $private_seller = true;
                            $public_seller = false;
                        }

                        return view ( 'relocationoffice.sellers.seller_update', [
                                    'paymentterms' => $payment_methods,
                                    'subscription_start_date_start' => $subscription_start_date_start,
                                    'subscription_end_date_end' => $subscription_end_date_end,
                                    'url_search_search' => $url_search_search,
                                    'current_date_seller' => $current_date_seller,
                                    'post_details' => $post_details,
                                    'post_slabs' => $post_slabs,
                                    'selectedbuyers' => $selectedbuyers,
                                    'private' => $private_seller,
                                    'public' => $public_seller,
                                    'trackingtypes'=> $trackingtypes
                            ] );
                    }
                    else
                    {
                        return redirect ( "/sellerlist" )->with ( 'message_update_post_fail', 'Invalid Request' );
                    }                    
                break;
                case RELOCATION_PET_MOVE:
                    return $this->relocationPetUpdateSellerPost($request,$sid);
                break;
                case RELOCATION_INTERNATIONAL:
                	return $this->relocationIntUpdateSellerPost($request,$sid);
                break;
                case RELOCATION_GLOBAL_MOBILITY:
                	return $this->relocationGmUpdateSellerPost($request,$sid);
                break;
            }
        } catch ( Exception $e ) {
            echo 'Caught exception: ', $e->getMessage (), "\n";
        }
    }
    
    
    
    public function relocationIntUpdateSellerPost($request,$sid){
    
    	try{
    		$post_id_status = DB::table('relocationint_seller_posts')->where('id', $sid)->first();
    		$transactionId = $post_id_status->transaction_id;
    		
    		$ocen_air = $post_id_status->lkp_international_type_id;
          $trackingtypes = CommonComponent::getTrackingTypes();
    		
    		if($ocen_air == 1){
    		$sellerpost = new RelocationintSellerPost();
    		//echo "<pre>";print_R(Input::all ());exit;
    		if (! empty ( Input::all () ))
    		{
    			if (Input::get ( 'confirm' ) == 'Confirm') {
    				$poststatus = 2;
    			} else {
    				$poststatus = 1;
    			}
    			if(isset($_POST['optradio'])){
    				$is_private = $_POST['optradio'];
    			}
    			if(isset($_POST['optradio']) && $is_private == 2){
    				if(isset($_POST['buyer_list_for_sellers_hidden']) && $_POST['buyer_list_for_sellers_hidden'] != ''){
    					$buyer_list = explode(",", $_POST['buyer_list_for_sellers_hidden']);
    					array_shift($buyer_list);
    					$buyer_list_count = count($buyer_list);
    				}
    			}
    			
    			//echo "<pre>"; print_R($buyer_list);die;
    			if(isset($_POST['optradio']) && $is_private == 2){
    				$lkp_access_id = 2;
    			}else{
    				$lkp_access_id = 1;
    			}
    
    			if (isset ( $_POST ['accept_payment'] ) && is_array ( $_POST ['accept_payment'] )) {
    				$accept_payment_netbanking = in_array ( 1, $_POST ['accept_payment'] ) ? 1 : 0;
    				$accept_payment_credit = in_array ( 2, $_POST ['accept_payment'] ) ? 1 : 0;
    				$accept_payment_debit = in_array ( 3, $_POST ['accept_payment'] ) ? 1 : 0;
    			} else {
    				$accept_payment_netbanking = 0;
    				$accept_payment_credit = 0;
    				$accept_payment_debit = 0;
    			}
    
    			if (isset ( $_POST ['accept_credit_netbanking'] ) && is_array ( $_POST ['accept_credit_netbanking'] )) {
    				$accept_credit_netbanking = in_array ( 1, $_POST ['accept_credit_netbanking'] ) ? 1 : 0;
    				$accept_credit_cheque = in_array ( 2, $_POST ['accept_credit_netbanking'] ) ? 1 : 0;
    			} else {
    				$accept_credit_netbanking = 0;
    				$accept_credit_cheque = 0;
    			}
    
    			
    
    			$otherCharges = array();
    			if(isset($_POST['next_terms_count_search'])){
    				$j = 0;
    				for($i=1;$i<=$_POST['next_terms_count_search'];$i++){
    					if(isset($_POST["labeltext_$i"]) && isset($_POST["terms_condtion_types_$i"])){
    						$otherCharges["labeltext"][$j] = $_POST["labeltext_$i"];
    						$otherCharges["terms_condtion_types"][$j] = $_POST["terms_condtion_types_$i"];
    						$j++;
    					}
    				}
    			}
    			
    			//echo "<pre>";print_r($otherCharges);exit;
    			$created_at = date ( 'Y-m-d H:i:s' );
    			$createdIp = $_SERVER ['REMOTE_ADDR'];
    			if(isset($_POST['sellerpoststatus_previous']) && $_POST['sellerpoststatus_previous'] == 1){
    				//echo "<pre>";print_r($_POST['credit_period']);exit;
    				$arr = array (
    						'credit_period' => $_REQUEST['credit_period'],
    						'to_date' => CommonComponent::convertDateForDatabase($request->input ( 'valid_to' )),
    						'tracking' => $request->input ( 'tracking' ),
    						'transitdays' => $request->input ( 'transitdays' ),
    						'units' => $request->input ( 'units' ),
    						'terms_conditions' => $request->input ( 'terms_conditions' ),
    						'lkp_payment_mode_id' => $request->input ( 'payment_methods' ),
    						'accept_payment_netbanking' => $accept_payment_netbanking,
    						'accept_payment_credit' => $accept_payment_credit,
    						'accept_payment_debit' => $accept_payment_debit,    						
    						'credit_period_units' => $request->input ( 'credit_period_units' ),
    						'accept_credit_netbanking' => $accept_credit_netbanking,
    						'accept_credit_cheque' => $accept_credit_cheque,
    						'other_charge1_text' => (isset ( $otherCharges ['labeltext'] ['0'] )) ? $otherCharges ['labeltext'] ['0'] : "",
    						'other_charge2_text' => (isset ( $otherCharges ['labeltext'] ['1'] )) ? $otherCharges ['labeltext'] ['1'] : "",
    						'other_charge3_text' => (isset ( $otherCharges ['labeltext'] ['2'] )) ? $otherCharges ['labeltext'] ['2'] : "",
    						'cancellation_charge_price' => (isset ( $_POST ['terms_condtion_types1'] )) ? $_POST ['terms_condtion_types1'] : "",
    						'storage_charges' => (isset ( $_POST ['storage_charges'] )) ? $_POST ['storage_charges'] : "",
    						'other_charge_price' => (isset ( $_POST ['terms_condtion_types2'] )) ? $_POST ['terms_condtion_types2'] : "",
    						'other_charge1_price' => (isset ( $otherCharges ['terms_condtion_types'] [0] )) ? $otherCharges ['terms_condtion_types'] [0] : "",
    						'other_charge2_price' => (isset ( $otherCharges ['terms_condtion_types'] [1] )) ? $otherCharges ['terms_condtion_types'] [1] : "",
    						'other_charge3_price' => (isset ( $otherCharges ['terms_condtion_types'] [2] )) ? $otherCharges ['terms_condtion_types'] [2] : "",
    						'lkp_post_status_id' => $poststatus,
    						'lkp_access_id' => $lkp_access_id,
    						
    				);
    			}else{
    				$arr = array (
    						'to_date' => CommonComponent::convertDateForDatabase($request->input ( 'valid_to' )),
    						'transitdays' => $request->input ( 'transitdays' ),
    						'units' => $request->input ( 'units' ),
    						'other_charge1_text' => (isset ( $otherCharges ['labeltext'] ['0'] )) ? $otherCharges ['labeltext'] ['0'] : "",
    						'other_charge2_text' => (isset ( $otherCharges ['labeltext'] ['1'] )) ? $otherCharges ['labeltext'] ['1'] : "",
    						'other_charge3_text' => (isset ( $otherCharges ['labeltext'] ['2'] )) ? $otherCharges ['labeltext'] ['2'] : "",
    						'cancellation_charge_price' => (isset ( $_POST ['terms_condtion_types1'] )) ? $_POST ['terms_condtion_types1'] : "",
    						'storage_charges' => (isset ( $_POST ['storage_charges'] )) ? $_POST ['storage_charges'] : "",
    						'other_charge_price' => (isset ( $_POST ['terms_condtion_types2'] )) ? $_POST ['terms_condtion_types2'] : "",
    						'other_charge1_price' => (isset ( $otherCharges ['terms_condtion_types'] [0] )) ? $otherCharges ['terms_condtion_types'] [0] : "",
    						'other_charge2_price' => (isset ( $otherCharges ['terms_condtion_types'] [1] )) ? $otherCharges ['terms_condtion_types'] [1] : "",
    						'other_charge3_price' => (isset ( $otherCharges ['terms_condtion_types'] [2] )) ? $otherCharges ['terms_condtion_types'] [2] : "",
    						
    				);
    			}
    
    
    			$sellerpost::where ( "id", $sid )->update ($arr);
    			$slabscount = 6;
    			DB::table('relocationint_seller_post_air_weight_slabs')->where('seller_post_id', $sid)->delete();
    			//Relcation Seller Slabs
    				
    			for($i = 1; $i <=$slabscount; $i++){
    				$SellerSlabs = new \App\Models\RelocationintSellerPostAirWeightSlab();
    				$SellerSlabs->lkp_service_id=RELOCATION_INTERNATIONAL;
    				$SellerSlabs->seller_post_id      = $sid;
    				$SellerSlabs->lkp_air_weight_slab_id      = $i;
    				$SellerSlabs->freight_charges      = $_POST['freightcharge_'.$i];
    				$SellerSlabs->od_charges      = $_POST['odcharges_'.$i];
    				$SellerSlabs->created_by      = Auth::id();
    				$SellerSlabs->created_ip      = $createdIp;
    				$SellerSlabs->created_at      = $created_at;
    				$SellerSlabs->save ();
    					
    			}
    			
    			
    			//*******matching engine***********************//
    			if($poststatus== OPEN){
    				$request = array();
    				$request['from_location_id'] = $_POST['from_location_id'];
    				$request['to_location_id'] = $_POST['to_location_id'];
    				$request['post_type'] = 2;
    				$request['service_type'] = 1;
    				$request['valid_from'] = CommonComponent::convertDateForDatabase($_POST['valid_from_hidden']);
    				$request['valid_to'] = CommonComponent::convertDateForDatabase($_POST['valid_to']);
    				$request['transit_days'] = $_POST['transitdays'];
    				SellerMatchingComponent::doMatching(RELOCATION_INTERNATIONAL, $sid, 2, $request);
    			}
    			//*******matching engine***********************//
    			
    			
    
    
    			if(isset($_POST['optradio']) && $is_private == 2){
    				if(isset($_POST['buyer_list_for_sellers_hidden']) && $_POST['buyer_list_for_sellers_hidden'] != ''){
    					$post_list_of_buyers = DB::table('relocationint_seller_selected_buyers')->where('seller_post_id', $sid)->lists('buyer_id');
    					DB::table('relocationint_seller_selected_buyers')->where('seller_post_id', $sid)->delete();
    					for($i = 0; $i < $buyer_list_count; $i ++) {
    						$sellerpost_for_buyers  =  new RelocationintSellerSelectedBuyer();
    
    						$sellerpost_for_buyers->seller_post_id = $sid;
    						$sellerpost_for_buyers->buyer_id = $buyer_list[$i];
    						$created_at = date ( 'Y-m-d H:i:s' );
    						$createdIp = $_SERVER ['REMOTE_ADDR'];
    						$sellerpost_for_buyers->created_by = Auth::id();
    						$sellerpost_for_buyers->created_at = $created_at;
    						$sellerpost_for_buyers->created_ip = $createdIp;
    						$sellerpost_for_buyers->save();
    						if (!in_array($buyer_list[$i], $post_list_of_buyers)){
    							$seller_selected_buyers_email = DB::table('users')->where('id', $buyer_list[$i])->get();
    							$seller_selected_buyers_email[0]->randnumber = $transactionId;
    							$seller_selected_buyers_email[0]->sellername = Auth::User()->username;
    							CommonComponent::send_email(SELLER_CREATED_POST_FOR_BUYERS,$seller_selected_buyers_email);
    						}
    						//CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
    
    						if($poststatus == OPEN){
    							//*******Send Sms to the private buyers***********************//
    							$msg_params = array(
    									'randnumber' => $transactionId,
    									'sellername' => Auth::User()->username,
    									'servicename' => 'RELOCATION INTERNATIONAL'
    							);
    							$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
    							if($getMobileNumber)
    								CommonComponent::sendSMS($getMobileNumber,SELLER_UPDATED_POST_FOR_BUYERS_SMS,$msg_params);
    							//*******Send Sms to the private buyers***********************//
    						}
    
    					}
    				}
    			}
    			if($poststatus == 2)
    				//return redirect ( "/sellerlist" )->with ( 'message_update_post', 'Post Updated Successfully' );
    				return redirect ( "/relocation/updatesellerpost/$sid" )->with ( 'transId_updated', $transactionId );
    			else
    				return redirect ( "/sellerlist" )->with ( 'message_update_post', 'Post Updated Successfully' );
    		}
    
    		// Retrieval of payment methods
    		$payment_methods = CommonComponent::getPaymentTerms ();
    		$seller_post_edit_action = DB::table ( 'relocationint_seller_posts as rsp' )->where ( 'rsp.id', $sid )->select ( 'rsp.*' )->first ();

    		$seller_slabs = DB::table ( 'relocationint_seller_post_air_weight_slabs as rsp' )->where ( 'rsp.seller_post_id', $sid )->select ( 'rsp.*' )->get ();
    		//echo "<pre>";print_r($seller_slabs);exit;

    		$selectedbuyers = DB::table ( 'relocationint_seller_selected_buyers as rpss' )
    		->leftjoin ( 'users as u', 'rpss.buyer_id', '=', 'u.id' )
    		->leftjoin ( 'buyer_business_details as bbds', 'rpss.buyer_id', '=', 'bbds.user_id' )
    		->where ( 'rpss.seller_post_id', $sid )
    		->select ( 'rpss.buyer_id', 'u.username', 'bbds.principal_place' )->get ();
        
    		$userId = Auth::User ()->id;
    		$user_subcsriptions = DB::table ( 'seller_details' )->where ( 'user_id', $userId )->first ();
    
    		if ($user_subcsriptions) {
    			$subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
    			$subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
    			$subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
    			$subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
    			$current_date_seller = date ( "Y-m-d" );
    		} else {
    			$user_subcsriptions = DB::table ( 'sellers' )->where ( 'user_id', $userId )->first ();
    			$subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
    			$subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
    			$subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
    			$subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
    			$current_date_seller = date ( "Y-m-d" );
    		}
    
    		if (isset ( $seller_post_edit_action->lkp_access_id ) && $seller_post_edit_action->lkp_access_id == 1) {
    			$private_seller = false;
    			$public_seller = true;
    		} else {
    			$private_seller = true;
    			$public_seller = false;
    		}
    		return view ( 'relocationint.airint.sellers.updatepost', [
    				'seller_post_edit' => $seller_post_edit_action,
    				'private' => $private_seller,

    				'seller_slabs' => $seller_slabs,

    				'public' => $public_seller,
    				'paymentterms' => $payment_methods,
    				'seller_postid' => $sid,
    				'selectedbuyers' => $selectedbuyers,
    				'subscription_start_date_start' => $subscription_start_date_start,
    				'subscription_end_date_end' => $subscription_end_date_end,
    				'current_date_seller' => $current_date_seller,
                    'trackingtypes'=> $trackingtypes
    				] );
    		
    	}else{
    		
    		$sellerpost = new RelocationintSellerPost();
    		
    		if (! empty ( Input::all () ))
    		{
    			//echo "<pre>";print_r($_POST);exit;
    			if (Input::get ( 'confirm' ) == 'Confirm') {
    				$poststatus = 2;
    			} else {
    				$poststatus = 1;
    			}
    			if(isset($_POST['optradio_ocen'])){
    				$is_private = $_POST['optradio_ocen'];
    			}
    			if(isset($_POST['optradio_ocen']) && $is_private == 2){
    				if(isset($_POST['buyer_list_for_sellers_ocen']) && $_POST['buyer_list_for_sellers_ocen'] != ''){
    					$buyer_list = explode(",", $_POST['buyer_list_for_sellers_ocen']);
    					array_shift($buyer_list);
    					$buyer_list_count = count($buyer_list);
    				}
    			}
    			if(isset($_POST['optradio_ocen']) && $is_private == 2){
    				$lkp_access_id = 2;
    			}else{
    				$lkp_access_id = 1;
    			}
    		
    			if (isset ( $_POST ['accept_payment_ocen'] ) && is_array ( $_POST ['accept_payment_ocen'] )) {
    				$accept_payment_netbanking = in_array ( 1, $_POST ['accept_payment_ocen'] ) ? 1 : 0;
    				$accept_payment_credit = in_array ( 2, $_POST ['accept_payment_ocen'] ) ? 1 : 0;
    				$accept_payment_debit = in_array ( 3, $_POST ['accept_payment_ocen'] ) ? 1 : 0;
    			} else {
    				$accept_payment_netbanking = 0;
    				$accept_payment_credit = 0;
    				$accept_payment_debit = 0;
    			}
    		
    			if (isset ( $_POST ['accept_credit_netbanking_ocen'] ) && is_array ( $_POST ['accept_credit_netbanking_ocen'] )) {
    				$accept_credit_netbanking = in_array ( 1, $_POST ['accept_credit_netbanking_ocen'] ) ? 1 : 0;
    				$accept_credit_cheque = in_array ( 2, $_POST ['accept_credit_netbanking_ocen'] ) ? 1 : 0;
    			} else {
    				$accept_credit_netbanking = 0;
    				$accept_credit_cheque = 0;
    			}
    		
    		
    		
    			$otherCharges = array();
    			if(isset($_POST['next_terms_count_search_ocen'])){
    				$j = 0;
    				for($i=1;$i<=$_POST['next_terms_count_search_ocen'];$i++){
    					if(isset($_POST["labeltext_$i"]) && isset($_POST["terms_condtion_types_$i"])){
    						$otherCharges["labeltext"][$j] = $_POST["labeltext_$i"];
    						$otherCharges["terms_condtion_types"][$j] = $_POST["terms_condtion_types_$i"];
    						$j++;
    					}
    				}
    			}
    			//echo "<pre>";print_R($otherCharges);print_R($_POST);die;
    		
    		if(isset($_POST['sellerpoststatus_previous']) && $_POST['sellerpoststatus_previous'] == 1){
    				//echo "<pre>";print_r($_POST['credit_period']);exit;
    				$arr = array (
    						'credit_period' => $_REQUEST['credit_period_ocen'],
    						'to_date' => CommonComponent::convertDateForDatabase($request->input ( 'valid_to_val' )),
    						
    						'origin_storage' => (isset ( $_POST ['origin_storage'] )) ? $_POST ['origin_storage'] : "",
    						'origin_handyman_services' => (isset ( $_POST ['origin_handyman_services'] )) ? $_POST ['origin_handyman_services'] : "",
    						'destination_storage' => (isset ( $_POST ['destination_storage'] )) ? $_POST ['destination_storage'] : "",
    						'destination_handyman_services' => (isset ( $_POST ['destination_handyman_services'] )) ? $_POST ['destination_handyman_services'] : "",
    						
    						
    						'tracking' => $request->input ( 'ocen_tracking' ),
    						'terms_conditions' => $request->input ( 'terms_conditions' ),
    						'crating_charges' => $request->input ( 'crating_charges' ),
    						'lkp_payment_mode_id' => $request->input ( 'ocean_paymentterms' ),
    						'accept_payment_netbanking' => $accept_payment_netbanking,
    						'accept_payment_credit' => $accept_payment_credit,
    						'accept_payment_debit' => $accept_payment_debit,    						
    						'credit_period_units' => $request->input ( 'credit_period_units_ocen' ),
    						'accept_credit_netbanking' => $accept_credit_netbanking,
    						'accept_credit_cheque' => $accept_credit_cheque,
    						'other_charge1_text' => (isset ( $otherCharges ['labeltext'] ['0'] )) ? $otherCharges ['labeltext'] ['0'] : "",
    						'other_charge2_text' => (isset ( $otherCharges ['labeltext'] ['1'] )) ? $otherCharges ['labeltext'] ['1'] : "",
    						'other_charge3_text' => (isset ( $otherCharges ['labeltext'] ['2'] )) ? $otherCharges ['labeltext'] ['2'] : "",
    						'cancellation_charge_price' => (isset ( $_POST ['terms_condtion_types1'] )) ? $_POST ['terms_condtion_types1'] : "",
    						'other_charge_price' => (isset ( $_POST ['terms_condtion_types2'] )) ? $_POST ['terms_condtion_types2'] : "",
    						'other_charge1_price' => (isset ( $otherCharges ['terms_condtion_types'] [0] )) ? $otherCharges ['terms_condtion_types'] [0] : "",
    						'other_charge2_price' => (isset ( $otherCharges ['terms_condtion_types'] [1] )) ? $otherCharges ['terms_condtion_types'] [1] : "",
    						'other_charge3_price' => (isset ( $otherCharges ['terms_condtion_types'] [2] )) ? $otherCharges ['terms_condtion_types'] [2] : "",
    						'lkp_post_status_id' => $poststatus,
    						'lkp_access_id' => $lkp_access_id,
    						
    				);
    			}else{
    				$arr = array (
    						'to_date' => CommonComponent::convertDateForDatabase($request->input ( 'valid_to' )),
    						'origin_storage' => (isset ( $_POST ['origin_storage'] )) ? $_POST ['origin_storage'] : "",
    						'origin_handyman_services' => (isset ( $_POST ['origin_handyman_services'] )) ? $_POST ['origin_handyman_services'] : "",
    						'destination_storage' => (isset ( $_POST ['destination_storage'] )) ? $_POST ['destination_storage'] : "",
    						'destination_handyman_services' => (isset ( $_POST ['destination_handyman_services'] )) ? $_POST ['destination_handyman_services'] : "",
    						'crating_charges' => $request->input ( 'crating_charges' ),
    						'other_charge1_text' => (isset ( $otherCharges ['labeltext'] ['0'] )) ? $otherCharges ['labeltext'] ['0'] : "",
    						'other_charge2_text' => (isset ( $otherCharges ['labeltext'] ['1'] )) ? $otherCharges ['labeltext'] ['1'] : "",
    						'other_charge3_text' => (isset ( $otherCharges ['labeltext'] ['2'] )) ? $otherCharges ['labeltext'] ['2'] : "",
    						'cancellation_charge_price' => (isset ( $_POST ['terms_condtion_types1'] )) ? $_POST ['terms_condtion_types1'] : "",
    						'other_charge_price' => (isset ( $_POST ['terms_condtion_types2'] )) ? $_POST ['terms_condtion_types2'] : "",
    						'other_charge1_price' => (isset ( $otherCharges ['terms_condtion_types'] [0] )) ? $otherCharges ['terms_condtion_types'] [0] : "",
    						'other_charge2_price' => (isset ( $otherCharges ['terms_condtion_types'] [1] )) ? $otherCharges ['terms_condtion_types'] [1] : "",
    						'other_charge3_price' => (isset ( $otherCharges ['terms_condtion_types'] [2] )) ? $otherCharges ['terms_condtion_types'] [2] : "",
    						
    				);
    			}
    		
    		
    			$sellerpost::where ( "id", $sid )->update ($arr);
    			$multi_shipment_data = $_POST ['shipment_volume'];
    			 
    		
    			for($i = 0; $i < count($multi_shipment_data); $i++) {
    				$sellerpost_lineitem = new RelocationintSellerPostItem();
    				$sellerpost_lineitem::where("id", $_POST['post_id'][$i])->update(array(
    						'lkp_relocation_shipment_type_id' => $_POST['shipment_type'][$i],
    						'lkp_relocation_shipment_volume_id' => $_POST['shipment_volume'][$i],
    						'od_charges' => $_POST['od_charges'][$i],
    						'freight_charges' => $_POST['freight_charges'][$i],
    						'transitdays' => $_POST['transitdays'][$i],
    						'units' => $_POST['units'][$i]
    				));
    		
    				//*******matching engine***********************//
    				if($poststatus == 2){
    					$request = array();
    					$request['from_location_id'] = $_POST ['from_location_id'];
    					$request['to_location_id'] = $_POST ['to_location_id'];
    					$request['post_type'] = 2;
    					$request['service_type'] = 2;
    					$request['valid_from'] = $_POST ['valid_from_val'];
    					$request['valid_to'] = CommonComponent::convertDateForDatabase($_POST ['valid_to_val']);
    					$request['transit_days'] = $_POST['transitdays'][$i];
    					SellerMatchingComponent::doMatching(RELOCATION_INTERNATIONAL, $sid, 2, $request);
    				}
    				//*******matching engine***********************//
    			}
    		
    			if(isset($_POST['optradio_ocen']) && $is_private == 2){
    				if(isset($_POST['buyer_list_for_sellers_ocen']) && $_POST['buyer_list_for_sellers_ocen'] != ''){
    					$post_list_of_buyers = DB::table('relocationint_seller_selected_buyers')->where('seller_post_id', $sid)->lists('buyer_id');
    					DB::table('relocationint_seller_selected_buyers')->where('seller_post_id', $sid)->delete();
    					for($i = 0; $i < $buyer_list_count; $i ++) {
    						$sellerpost_for_buyers  =  new RelocationintSellerSelectedBuyer();
    		
    						$sellerpost_for_buyers->seller_post_id = $sid;
    						$sellerpost_for_buyers->buyer_id = $buyer_list[$i];
    						$created_at = date ( 'Y-m-d H:i:s' );
    						$createdIp = $_SERVER ['REMOTE_ADDR'];
    						$sellerpost_for_buyers->created_by = Auth::id();
    						$sellerpost_for_buyers->created_at = $created_at;
    						$sellerpost_for_buyers->created_ip = $createdIp;
    						$sellerpost_for_buyers->save();
    						if (!in_array($buyer_list[$i], $post_list_of_buyers)){
    							$seller_selected_buyers_email = DB::table('users')->where('id', $buyer_list[$i])->get();
    							$seller_selected_buyers_email[0]->randnumber = $transactionId;
    							$seller_selected_buyers_email[0]->sellername = Auth::User()->username;
    							CommonComponent::send_email(SELLER_CREATED_POST_FOR_BUYERS,$seller_selected_buyers_email);
    						}
    						//CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
    		
    						if($poststatus == OPEN){
    							//*******Send Sms to the private buyers***********************//
    							$msg_params = array(
    									'randnumber' => $transactionId,
    									'sellername' => Auth::User()->username,
    									'servicename' => 'RELOCATION INTERNATIONAL'
    							);
    							$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
    							if($getMobileNumber)
    								CommonComponent::sendSMS($getMobileNumber,SELLER_UPDATED_POST_FOR_BUYERS_SMS,$msg_params);
    							//*******Send Sms to the private buyers***********************//
    						}
    		
    					}
    				}
    			}
    			if($poststatus == 2)
    				//return redirect ( "/sellerlist" )->with ( 'message_update_post', 'Post Updated Successfully' );
    				return redirect ( "/relocation/updatesellerpost/$sid" )->with ( 'transId_updated', $transactionId );
    			else
    				return redirect ( "/sellerlist" )->with ( 'message_update_post', 'Post Updated Successfully' );
    		}
    		
    		// Retrieval of payment methods
    		$payment_methods = CommonComponent::getPaymentTerms ();
    		$seller_post_edit_action = DB::table ( 'relocationint_seller_posts as rsp' )->where ( 'rsp.id', $sid )->select ( 'rsp.*' )->first ();
    		$seller_post_edit_action_lines = DB::table ( 'relocationint_seller_post_items as rspi' )
    		->where ( 'rspi.seller_post_id', $sid )
    		->select ( 'rspi.*' )->get ();
    		
    		$selectedbuyers = DB::table ( 'relocationint_seller_selected_buyers as rpss' )
    		->leftjoin ( 'users as u', 'rpss.buyer_id', '=', 'u.id' )
    		->leftjoin ( 'buyer_business_details as bbds', 'rpss.buyer_id', '=', 'bbds.user_id' )
    		->where ( 'rpss.seller_post_id', $sid )
    		->select ( 'rpss.buyer_id', 'u.username', 'bbds.principal_place' )->get ();
    		
    		//echo "<pre>";print_R($seller_post_edit_action);
    		//print_R($seller_post_edit_action_lines);
    		//die;
    		
    		$userId = Auth::User ()->id;
    		$user_subcsriptions = DB::table ( 'seller_details' )->where ( 'user_id', $userId )->first ();
    		
    		if ($user_subcsriptions) {
    			$subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
    			$subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
    			$subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
    			$subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
    			$current_date_seller = date ( "Y-m-d" );
    		} else {
    			$user_subcsriptions = DB::table ( 'sellers' )->where ( 'user_id', $userId )->first ();
    			$subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
    			$subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
    			$subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
    			$subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
    			$current_date_seller = date ( "Y-m-d" );
    		}
    		
    		if (isset ( $seller_post_edit_action->lkp_access_id ) && $seller_post_edit_action->lkp_access_id == 1) {
    			$private_seller = false;
    			$public_seller = true;
    		} else {
    			$private_seller = true;
    			$public_seller = false;
    		}
    		return view ( 'relocationint.ocean.sellers.updatepost', [
    				'seller_post_edit' => $seller_post_edit_action,
    				'seller_post_edit_action_lines' => $seller_post_edit_action_lines,
    				'private' => $private_seller,
    				'public' => $public_seller,
    				'paymentterms' => $payment_methods,
    				'seller_postid' => $sid,
    				'selectedbuyers' => $selectedbuyers,
    				'subscription_start_date_start' => $subscription_start_date_start,
    				'subscription_end_date_end' => $subscription_end_date_end,
    				'current_date_seller' => $current_date_seller,
                    'trackingtypes'=> $trackingtypes
    				] );
    		
    		
    		
    	}
    	
    	} catch ( Exception $e ) {
    		echo 'Caught exception: ', $e->getMessage (), "\n";
    	}
    }
    
    
    
    
    
    
    
    public function relocationPetUpdateSellerPost($request,$sid){
        
        try{
                    DB::table('relocationpet_seller_posts')->where('id', $sid)->first();
                    $sellerpost = new RelocationpetSellerPost();                    
                    $trackingtypes = CommonComponent::getTrackingTypes();
                    
                    if (! empty ( Input::all () )) 
                    {
                        if (Input::get ( 'confirm' ) == 'Confirm' && $_POST['sellerpoststatus'] == 1) {
                            $poststatus = 2;
                        } else {
                            $poststatus = 1;
                        }
                        if(isset($_POST['optradio'])){
                            $is_private = $_POST['optradio'];
                        }
                        if(isset($_POST['optradio']) && $is_private == 2){
                            if(isset($_POST['buyer_list_for_sellers_hidden']) && $_POST['buyer_list_for_sellers_hidden'] != ''){
                                $buyer_list = explode(",", $_POST['buyer_list_for_sellers_hidden']);
                                array_shift($buyer_list);
                                $buyer_list_count = count($buyer_list);
                            }
                        }
                        $randnumber_value = rand(11111,99999);
                        $randnumber = "RELOCATIONPET/2016/0".$randnumber_value;
                        //echo "<pre>"; print_R($buyer_list);die;
                        if(isset($_POST['optradio']) && $is_private == 2){
                            $lkp_access_id = 2;
                        }else{
                            $lkp_access_id = 1;
                        }

                        if (isset ( $_POST ['accept_payment_ptl'] ) && is_array ( $_POST ['accept_payment_ptl'] )) {
                            $accept_payment_netbanking = in_array ( 1, $_POST ['accept_payment_ptl'] ) ? 1 : 0;
                            $accept_payment_credit = in_array ( 2, $_POST ['accept_payment_ptl'] ) ? 1 : 0;
                            $accept_payment_debit = in_array ( 3, $_POST ['accept_payment_ptl'] ) ? 1 : 0;
                        } else {
                            $accept_payment_netbanking = 0;
                            $accept_payment_credit = 0;
                            $accept_payment_debit = 0;
                        }

                        if (isset ( $_POST ['accept_credit_netbanking'] ) && is_array ( $_POST ['accept_credit_netbanking'] )) {
                            $accept_credit_netbanking = in_array ( 1, $_POST ['accept_credit_netbanking'] ) ? 1 : 0;
                            $accept_credit_cheque = in_array ( 2, $_POST ['accept_credit_netbanking'] ) ? 1 : 0;
                        } else {
                            $accept_credit_netbanking = 0;
                            $accept_credit_cheque = 0;
                        }



                        $otherCharges = array();
                        if(isset($_POST['next_terms_count_search'])){
                            $j = 0;
                            for($i=1;$i<=$_POST['next_terms_count_search'];$i++){
                                if(isset($_POST["labeltext_$i"]) && isset($_POST["terms_condtion_types_$i"])){
                                    $otherCharges["labeltext"][$j] = $_POST["labeltext_$i"];
                                    $otherCharges["terms_condtion_types"][$j] = $_POST["terms_condtion_types_$i"];
                                    $j++;
                                }
                            }
                        }
                        //echo "<pre>";print_R($otherCharges);print_R($_POST);die;

                        if(isset($_POST['sellerpoststatus_previous']) && $_POST['sellerpoststatus_previous'] == 1){
                            $arr = array (
                                'to_date' => CommonComponent::convertDateForDatabase($request->input ( 'valid_to' )),
                                
                                'tracking' => $request->input ( 'tracking' ),
                                'terms_conditions' => $request->input ( 'terms_conditions' ),
                                'lkp_payment_mode_id' => $request->input ( 'paymentterms' ),
                                'accept_payment_netbanking' => $accept_payment_netbanking,
                                'accept_payment_credit' => $accept_payment_credit,
                                'accept_payment_debit' => $accept_payment_debit,
                                'credit_period' => $request->input ( 'credit_period_ptl' ),
                                'credit_period_units' => $request->input ( 'credit_period_units' ),
                                'accept_credit_netbanking' => $accept_credit_netbanking,
                                'accept_credit_cheque' => $accept_credit_cheque,
                                'credit_period' => $request->input ( 'credit_period_ptl' ),
                                'credit_period_units' => $request->input ( 'credit_period_units' ),
                                'other_charge1_text' => (isset ( $otherCharges ['labeltext'] ['0'] )) ? $otherCharges ['labeltext'] ['0'] : "",
                                'other_charge2_text' => (isset ( $otherCharges ['labeltext'] ['1'] )) ? $otherCharges ['labeltext'] ['1'] : "",
                                'other_charge3_text' => (isset ( $otherCharges ['labeltext'] ['2'] )) ? $otherCharges ['labeltext'] ['2'] : "",
                                'cancellation_charge_price' => (isset ( $_POST ['cancellation_charge_price'] )) ? $_POST ['cancellation_charge_price'] : "",
                                'docket_charge_price' => (isset ( $_POST ['docket_charge_price'] )) ? $_POST ['docket_charge_price'] : "",
                                'other_charge1_price' => (isset ( $otherCharges ['terms_condtion_types'] [0] )) ? $otherCharges ['terms_condtion_types'] [0] : "",
                                'other_charge2_price' => (isset ( $otherCharges ['terms_condtion_types'] [1] )) ? $otherCharges ['terms_condtion_types'] [1] : "",
                                'other_charge3_price' => (isset ( $otherCharges ['terms_condtion_types'] [2] )) ? $otherCharges ['terms_condtion_types'] [2] : "",
                                'lkp_post_status_id' => $poststatus,
                                'lkp_access_id' => $lkp_access_id
                            );
                        }else{
                            $arr = array (
                                'to_date' => CommonComponent::convertDateForDatabase($request->input ( 'valid_to' )),
                            	'other_charge1_text' => (isset ( $otherCharges ['labeltext'] ['0'] )) ? $otherCharges ['labeltext'] ['0'] : "",
                            	'other_charge2_text' => (isset ( $otherCharges ['labeltext'] ['1'] )) ? $otherCharges ['labeltext'] ['1'] : "",
                            	'other_charge3_text' => (isset ( $otherCharges ['labeltext'] ['2'] )) ? $otherCharges ['labeltext'] ['2'] : "",
                            		'cancellation_charge_price' => (isset ( $_POST ['cancellation_charge_price'] )) ? $_POST ['cancellation_charge_price'] : "",
                            		'docket_charge_price' => (isset ( $_POST ['docket_charge_price'] )) ? $_POST ['docket_charge_price'] : "",
                            		'other_charge1_price' => (isset ( $otherCharges ['terms_condtion_types'] [0] )) ? $otherCharges ['terms_condtion_types'] [0] : "",
                            		'other_charge2_price' => (isset ( $otherCharges ['terms_condtion_types'] [1] )) ? $otherCharges ['terms_condtion_types'] [1] : "",
                            		'other_charge3_price' => (isset ( $otherCharges ['terms_condtion_types'] [2] )) ? $otherCharges ['terms_condtion_types'] [2] : "",
                            		
                            );
                        }


                        $sellerpost::where ( "id", $sid )->update ($arr);
                        $multi_pet_data = $_POST ['pet_items'];
                       

                        for($i = 0; $i < $multi_pet_data; $i++) {
                            $sellerpost_lineitem = new RelocationpetSellerPostItem();
                            $sellerpost_lineitem::where("id", $_POST['property_post_id'][$i])->update(array(
                                'lkp_pet_type_id' => $_POST['pettypes_hidden'][$i],
                                'lkp_cage_type_id' => $_POST['cagetypes_hidden'][$i],
                                'rate_per_cft' => $_POST['freight_hidden'][$i],
                                'od_charges' => $_POST['od_charges_hidden'][$i],
                                'transitdays' => $_POST['transit_days_hidden'][$i],
                                'units' => $_POST['transitdays_units_relocation_hidden'][$i]                                
                            ));

                            //*******matching engine***********************//
                            if($poststatus == 2){
                                $request = array();
                                $request['from_location_id'] = $_POST ['from_location_id'];
                                $request['to_location_id'] = $_POST ['to_location_id'];
                                $request['post_type'] = 1;
                                $request['pettypes'] = $_POST ['pettypes_hidden'] [$i];
                                $request['cagetypes'] = $_POST ['cagetypes_hidden'] [$i];
                                $request['valid_from'] = $_POST ['valid_from_hidden'];
                                $request['valid_to'] = CommonComponent::convertDateForDatabase($_POST ['valid_to']);
                                $request['transit_days'] = $_POST['transit_days_hidden'][$i];
                                SellerMatchingComponent::doMatching(RELOCATION_PET_MOVE, $sid, 2, $request);
                            }
                            //*******matching engine***********************//
                        }

                        if(isset($_POST['optradio']) && $is_private == 2){
                            if(isset($_POST['buyer_list_for_sellers_hidden']) && $_POST['buyer_list_for_sellers_hidden'] != ''){
                                $post_list_of_buyers = DB::table('relocationpet_seller_selected_buyers')->where('seller_post_id', $sid)->lists('buyer_id');
                                DB::table('relocationpet_seller_selected_buyers')->where('seller_post_id', $sid)->delete();
                                for($i = 0; $i < $buyer_list_count; $i ++) {
                                    $sellerpost_for_buyers  =  new RelocationpetSellerSelectedBuyer();
                    
                                    $sellerpost_for_buyers->seller_post_id = $sid;
                                    $sellerpost_for_buyers->buyer_id = $buyer_list[$i];
                                    $created_at = date ( 'Y-m-d H:i:s' );
                                    $createdIp = $_SERVER ['REMOTE_ADDR'];
                                    $sellerpost_for_buyers->created_by = Auth::id();
                                    $sellerpost_for_buyers->created_at = $created_at;
                                    $sellerpost_for_buyers->created_ip = $createdIp;
                                    $sellerpost_for_buyers->save();
                                    if (!in_array($buyer_list[$i], $post_list_of_buyers)){
                                        $seller_selected_buyers_email = DB::table('users')->where('id', $buyer_list[$i])->get();
                                        $seller_selected_buyers_email[0]->randnumber = $randnumber;
                                        $seller_selected_buyers_email[0]->sellername = Auth::User()->username;
                                        CommonComponent::send_email(SELLER_CREATED_POST_FOR_BUYERS,$seller_selected_buyers_email);
                                    }
                                    //CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
                                    
                                    if($poststatus == OPEN){
                                        //*******Send Sms to the private buyers***********************//
                                        $msg_params = array(
                                                'randnumber' => $randnumber,
                                                'sellername' => Auth::User()->username,
                                                'servicename' => 'RELOCATION PET MOVE'
                                        );
                                        $getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
                                        if($getMobileNumber)
                                            CommonComponent::sendSMS($getMobileNumber,SELLER_UPDATED_POST_FOR_BUYERS_SMS,$msg_params);
                                        //*******Send Sms to the private buyers***********************//
                                    }
                                    
                                }
                            }
                        }
                        if($poststatus == 2)
                            return redirect ( "/sellerlist" )->with ( 'message_update_post', 'Post Updated Successfully' );
                        else
                            return redirect ( "/sellerlist" )->with ( 'message_update_post', 'Post Updated Successfully' );
                    }

                    // Retrieval of payment methods
                    $payment_methods = CommonComponent::getPaymentTerms ();
                    $petTypes = CommonComponent::getAllPetTypes();
                    $cagetypes =  CommonComponent::getAllCageTypes();
                    $seller_post_edit_action = DB::table ( 'relocationpet_seller_posts as rsp' )->where ( 'rsp.id', $sid )->select ( 'rsp.*' )->first ();
                    $seller_post_edit_action_lines = DB::table ( 'relocationpet_seller_post_items as rspi' )
                                                            ->where ( 'rspi.seller_post_id', $sid )
                                                            ->select ( 'rspi.*' )->get ();

                    $selectedbuyers = DB::table ( 'relocationpet_seller_selected_buyers as rpss' )
                                        ->leftjoin ( 'users as u', 'rpss.buyer_id', '=', 'u.id' )
                                        ->leftjoin ( 'buyer_business_details as bbds', 'rpss.buyer_id', '=', 'bbds.user_id' )
                                        ->where ( 'rpss.seller_post_id', $sid )
                                        ->select ( 'rpss.buyer_id', 'u.username', 'bbds.principal_place' )->get ();
                    
                    //echo "<pre>";print_R($seller_post_edit_action);print_R($seller_post_edit_action_lines);die;

                    $userId = Auth::User ()->id;
                    $user_subcsriptions = DB::table ( 'seller_details' )->where ( 'user_id', $userId )->first ();

                    if ($user_subcsriptions) {
                        $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                        $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                        $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                        $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                        $current_date_seller = date ( "Y-m-d" );
                    } else {
                        $user_subcsriptions = DB::table ( 'sellers' )->where ( 'user_id', $userId )->first ();
                        $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                        $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                        $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                        $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                        $current_date_seller = date ( "Y-m-d" );
                    }

                    if (isset ( $seller_post_edit_action->lkp_access_id ) && $seller_post_edit_action->lkp_access_id == 1) {
                        $private_seller = false;
                        $public_seller = true;
                    } else {
                        $private_seller = true;
                        $public_seller = false;
                    }
                    return view ( 'relocationpet.sellers.updatepost', [
                        'seller_post_edit' => $seller_post_edit_action,
                        'seller_post_edit_action_lines' => $seller_post_edit_action_lines,
                        'private' => $private_seller,
                        'public' => $public_seller,
                        'paymentterms' => $payment_methods,
                        'petTypes' => $petTypes,
                        'cagetypes' => $cagetypes,
                        'seller_postid' => $sid,
                        'selectedbuyers' => $selectedbuyers,
                        'subscription_start_date_start' => $subscription_start_date_start,
                        'subscription_end_date_end' => $subscription_end_date_end,
                        'current_date_seller' => $current_date_seller,
                        'trackingtypes'=> $trackingtypes
                    ] );
        } catch ( Exception $e ) {
            echo 'Caught exception: ', $e->getMessage (), "\n";
        }            
    }

    public function deleteSellerRelocationPost($postId) {    	
    	$updatedAt = date ( 'Y-m-d H:i:s' );
    	$updatedIp = $_SERVER ["REMOTE_ADDR"];    
    	try {
    		$serviceId = Session::get('service_id');
    		if($serviceId==RELOCATION_DOMESTIC){
                    RelocationSellerPost::where ( "id", $postId )->update ( array (
                    'lkp_post_status_id' => CANCELLED,
                    'updated_at' => $updatedAt,
                    'updated_by' => Auth::User ()->id,
                    'updated_ip' => $updatedIp
                    ));  
    		}
    		if($serviceId==RELOCATION_OFFICE_MOVE){
                    RelocationofficeSellerPost::where ( "id", $postId )->update ( array (
    						'lkp_post_status_id' => CANCELLED,
    						'updated_at' => $updatedAt,
    						'updated_by' => Auth::User ()->id,
    						'updated_ip' => $updatedIp
    				));
    		}
                if($serviceId==RELOCATION_PET_MOVE){
                    RelocationpetSellerPost::where ( "id", $postId )->update ( array (
    						'lkp_post_status_id' => CANCELLED,
    						'updated_at' => $updatedAt,
    						'updated_by' => Auth::User ()->id,
    						'updated_ip' => $updatedIp
    				));
    		}
            if($serviceId==RELOCATION_INTERNATIONAL){
                    RelocationintSellerPost::where ( "id", $postId )->update ( array (
                            'lkp_post_status_id' => CANCELLED,
                            'updated_at' => $updatedAt,
                            'updated_by' => Auth::User ()->id,
                            'updated_ip' => $updatedIp
                    ));
            }
            if($serviceId==RELOCATION_GLOBAL_MOBILITY){
                    RelocationgmSellerPost::where ( "id", $postId )->update ( array (
                            'lkp_post_status_id' => CANCELLED,
                            'updated_at' => $updatedAt,
                            'updated_by' => Auth::User ()->id,
                            'updated_ip' => $updatedIp
                    ));
            }
    		return "Seller posts successfully deleted";
    	} catch ( Exception $ex ) {
    
    		return 0;
    	}
    }
    public function getcageweight(){
        try {
            $cage_id    =   $_GET['cage_id'];
            $data       =   DB::table ( 'lkp_cage_types' )->where ( 'id', $cage_id )->select('cage_weight')->first ();
            echo $data->cage_weight;
            
        } catch (Exception $ex) {

        }
    }
    
 public function chekcksellerofficepost(){
 	
 	$userId = Auth::User ()->id;
 	$frDate=CommonComponent::convertDateForDatabase($_POST ['from_date']);
 	$toDate=CommonComponent::convertDateForDatabase($_POST ['to_date']);
 	$sellerpostcheck=DB::table('relocationoffice_seller_posts as sellerposts')
 	->where('sellerposts.from_location_id', $_POST ['city'])
 	->where('sellerposts.created_by', $userId)
 	->whereRaw ("(`from_date` between  '$frDate' and '$toDate' or `to_date` between '$frDate' and '$toDate')")
 	->select('sellerposts.id')
 	->count();
 	
 	return $sellerpostcheck;
 	     	
   }
    
   public function relocationGmCreatePost($request){
        
       
            Session::put('session_delivery_date_ptl','');
            Session::put('session_dispatch_date_ptl','');
            
            Session::put('session_to_city_id_ptl','');
            
            Session::put('session_to_location_ptl','');
            

            $roleId = Auth::User ()->lkp_role_id;
            if ($roleId == SELLER) {
                CommonComponent::activityLog ( "SELLER_CREATED_POSTS", SELLER_CREATED_POSTS, 0, HTTP_REFERRER, CURRENT_URL );
            }
            if (! empty ( Input::all () )) {
                
                if (isset ( $_POST ['optradio'] )) {
                    $is_private = $_POST ['optradio'];
                }
                //$randnumber_value = rand ( 11111, 99999 );
                $postid  =   CommonComponent::getSellerPostID(Session::get ( 'service_id' ));

                $created_year = date('Y');
                $randnumber = 'RELOCATIONGM/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
                
                //$multi_pet_data = $_POST ['pet_items'];
                
                if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
                    if (isset ( $_POST ['buyer_list_for_sellers'] ) && $_POST ['buyer_list_for_sellers'] != '') {
                        $buyer_list = explode ( ",", $_POST ['buyer_list_for_sellers'] );
                        array_shift ( $buyer_list );
                        $buyer_list_count = count ( $buyer_list );
                    }
                }
                
                $sellerpost = new RelocationgmSellerPost();
                $sellerpost->lkp_service_id = RELOCATION_GLOBAL_MOBILITY;
                $sellerpost->from_date = CommonComponent::convertDateForDatabase($_POST ['valid_from']);
                $sellerpost->to_date = CommonComponent::convertDateForDatabase($_POST ['valid_to']);
                $sellerpost->location_id = $_POST ['to_location_id'];
                $sellerpost->seller_district_id = $_POST ['seller_district_id'];
                $gmServiceTypes =CommonComponent::getAllGMServiceTypesforSeller();
                foreach($gmServiceTypes as $gmServiceType){
                    $sid        =   $gmServiceType->id;
                    $str_name   =   strtolower(str_replace(' ','_',$gmServiceType->service_type));
                    if($_POST[$str_name]!=''){
                        $sellerpost->$str_name = $_POST[$str_name];
                    }
                }
                
                $sellerpost->terms_conditions = $request->terms_conditions;
                $sellerpost->lkp_payment_mode_id = $request->paymentterms;
                $sellerpost->credit_period = $request->credit_period_ptl;
                $sellerpost->credit_period_units = $request->credit_period_units;

                if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
                    $sellerpost->lkp_access_id = 2;
                } else {
                    $sellerpost->lkp_access_id = 1;
                }
                $sellerpost->seller_id = Auth::id ();
                $sellerpost->transaction_id = $randnumber;
                if (Input::get ( 'confirm' ) == 'Confirm' && $_POST['sellerpoststatus'] == 1) {
                    $lkp_post_status_id = 2;
                } else {
                    $lkp_post_status_id = 1;
                }

                $sellerpost->lkp_post_status_id = $lkp_post_status_id;
                $sellerpost->cancellation_charge_text = "cancellation Charges";
                if(isset($_POST['cancellation_charge_price']))
                $sellerpost->cancellation_charge_price = $request->cancellation_charge_price;
                $sellerpost->docket_charge_text = "Other Charges";
                if(isset($_POST['docket_charge_price']))
                $sellerpost->docket_charge_price = $request->docket_charge_price;
                $f=1;
                $ft=1;
                for($i=1;$i<=$request->next_terms_count_search;$i++){
                    if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] != '') {
                        $field_name="other_charge".$f."_text";
                        $sellerpost->$field_name = $_POST['labeltext_'.$i];
                        $f++;
                    }
                    if (isset ( $_POST['labeltext_'.$i] ) && $_POST['labeltext_'.$i] == '') {
                        $f++;
                    }
                    if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] != '') {
                        $field_name="other_charge".$ft."_price";
                        $sellerpost->$field_name = $_POST['terms_condtion_types_'.$i];
                        $ft++;
                    }
                    if (isset ( $_POST['terms_condtion_types_'.$i] ) && $_POST['terms_condtion_types_'.$i] == '') {
                        $ft++;
                    }

                }
                if (is_array ( $request->accept_payment_ptl )) {
                    $sellerpost->accept_payment_netbanking = in_array ( 1, $request->accept_payment_ptl ) ? 1 : 0;
                    $sellerpost->accept_payment_credit = in_array ( 2, $request->accept_payment_ptl ) ? 1 : 0;
                    $sellerpost->accept_payment_debit = in_array ( 3, $request->accept_payment_ptl ) ? 1 : 0;
                } else {
                    $sellerpost->accept_payment_netbanking = 0;
                    $sellerpost->accept_payment_credit = 0;
                    $sellerpost->accept_payment_debit = 0;
                }
                if (is_array ( $request->accept_credit_netbanking )) {
                    $sellerpost->accept_credit_netbanking = in_array ( 1, $request->accept_credit_netbanking ) ? 1 : 0;
                    $sellerpost->accept_credit_cheque = in_array ( 2, $request->accept_credit_netbanking ) ? 1 : 0;
                } else {
                    $sellerpost->accept_credit_netbanking = 0;
                    $sellerpost->accept_credit_cheque = 0;
                }
               
                $created_at = date ( 'Y-m-d H:i:s' );
                $createdIp = $_SERVER ['REMOTE_ADDR'];
                $sellerpost->created_by = Auth::id ();
                $sellerpost->created_at = $created_at;
                $sellerpost->created_ip = $createdIp;
                
                if ($sellerpost->save ()) {

                    // CommonComponent::auditLog($sellerpost->id,'seller_posts');
                        //*******matching engine***********************//
                        $request = array();
                        //$request['from_location_id'] = $_POST ['from_location_id'];
                        $request['to_location_id'] = $_POST ['to_location_id'];
                        $request['post_type'] = 1;
                        foreach($gmServiceTypes as $gmServiceType){
                            $str_name   =   strtolower(str_replace(' ','_',$gmServiceType->service_type));
                            if($_POST[$str_name]!=''){
                                $request[$str_name] = $_POST[$str_name];
                            }
                        }
                        $request['valid_from'] = CommonComponent::convertDateForDatabase($_POST ['valid_from']);
                        $request['valid_to'] = CommonComponent::convertDateForDatabase($_POST ['valid_to']);
                       
                        SellerMatchingComponent::doMatching(RELOCATION_GLOBAL_MOBILITY, $sellerpost->id, 2, $request);
                        //*******matching engine***********************//
                    

                    if (isset ( $_POST ['optradio'] ) && $is_private == 2) {
                        if (isset ( $_POST ['buyer_list_for_sellers'] ) && $_POST ['buyer_list_for_sellers'] != '') {
                            for($i = 0; $i < $buyer_list_count; $i ++) {

                                $sellerpost_for_buyers = new RelocationgmSellerSelectedBuyer();
                                
                                $sellerpost_for_buyers->seller_post_id = $sellerpost->id;
                                $sellerpost_for_buyers->buyer_id = $buyer_list [$i];
                                $created_at = date ( 'Y-m-d H:i:s' );
                                $createdIp = $_SERVER ['REMOTE_ADDR'];
                                $sellerpost_for_buyers->created_by = Auth::id ();
                                $sellerpost_for_buyers->created_at = $created_at;
                                $sellerpost_for_buyers->created_ip = $createdIp;
                                $sellerpost_for_buyers->save ();
                                $seller_selected_buyers_email = DB::table ( 'users' )->where ( 'id', $buyer_list [$i] )->get ();
                                $seller_selected_buyers_email [0]->randnumber = $randnumber;
                                $seller_selected_buyers_email [0]->sellername = Auth::User ()->username;
                                CommonComponent::send_email ( SELLER_CREATED_POST_FOR_BUYERS, $seller_selected_buyers_email );
                                // CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
                                
                                //*******Send Sms to the private buyers***********************//
                                if($lkp_post_status_id == OPEN){
                                	$msg_params = array(
                                			'randnumber' => $randnumber,
                                			'sellername' => Auth::User()->username,
                                			'servicename' => "RELOCATION PET"
                                	);
                                	$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
                                	if($getMobileNumber)
                                		CommonComponent::sendSMS($getMobileNumber,SELLER_CREATED_POST_FOR_BUYERS_SMS,$msg_params);
                                }
                                //*******Send Sms to the private buyers***********************//
                                
                                
                                
                            }
                        }
                    }

                    if (Input::get ( 'confirm' ) == 'Confirm' && $_POST['sellerpoststatus'] == 1) {
                        return $randnumber;
                    } else {
                        return redirect ( '/sellerlist' )->with ( 'message_create_post_ptl', 'Post was saved as draft' );
                    }
                }
            }
         
    }
    
    public function relocationGmUpdateSellerPost($request,$sid){
        
        try{//echo "<pre>"; print_R($_POST);die;
                    $gmServiceTypes = CommonComponent::getAllGMServiceTypesforSeller();
                    DB::table('relocationgm_seller_posts')->where('id', $sid)->first();
                    $sellerpost = new RelocationgmSellerPost();
                    
                    if (! empty ( Input::all () )) 
                    {
                        if (Input::get ( 'confirm' ) == 'Confirm' && $_POST['sellerpoststatus'] == 1) {
                            $poststatus = 2;
                        } else {
                            $poststatus = 1;
                        }
                        if(isset($_POST['optradio'])){
                            $is_private = $_POST['optradio'];
                        }
                        if(isset($_POST['optradio']) && $is_private == 2){
                            if(isset($_POST['buyer_list_for_sellers_hidden']) && $_POST['buyer_list_for_sellers_hidden'] != ''){
                                $buyer_list = explode(",", $_POST['buyer_list_for_sellers_hidden']);
                                array_shift($buyer_list);
                                $buyer_list_count = count($buyer_list);
                            }
                        }
                        $randnumber_value = rand(11111,99999);
                        $randnumber = "RELOCATIONGM/2016/0".$randnumber_value;
                        //echo "<pre>"; print_R($buyer_list);die;
                        if(isset($_POST['optradio']) && $is_private == 2){
                            $lkp_access_id = 2;
                        }else{
                            $lkp_access_id = 1;
                        }

                        if (isset ( $_POST ['accept_payment_ptl'] ) && is_array ( $_POST ['accept_payment_ptl'] )) {
                            $accept_payment_netbanking = in_array ( 1, $_POST ['accept_payment_ptl'] ) ? 1 : 0;
                            $accept_payment_credit = in_array ( 2, $_POST ['accept_payment_ptl'] ) ? 1 : 0;
                            $accept_payment_debit = in_array ( 3, $_POST ['accept_payment_ptl'] ) ? 1 : 0;
                        } else {
                            $accept_payment_netbanking = 0;
                            $accept_payment_credit = 0;
                            $accept_payment_debit = 0;
                        }

                        if (isset ( $_POST ['accept_credit_netbanking'] ) && is_array ( $_POST ['accept_credit_netbanking'] )) {
                            $accept_credit_netbanking = in_array ( 1, $_POST ['accept_credit_netbanking'] ) ? 1 : 0;
                            $accept_credit_cheque = in_array ( 2, $_POST ['accept_credit_netbanking'] ) ? 1 : 0;
                        } else {
                            $accept_credit_netbanking = 0;
                            $accept_credit_cheque = 0;
                        }



                        $otherCharges = array();
                        if(isset($_POST['next_terms_count_search'])){
                            $j = 0;
                            for($i=1;$i<=$_POST['next_terms_count_search'];$i++){
                                if(isset($_POST["labeltext_$i"]) && isset($_POST["terms_condtion_types_$i"])){
                                    $otherCharges["labeltext"][$j] = $_POST["labeltext_$i"];
                                    $otherCharges["terms_condtion_types"][$j] = $_POST["terms_condtion_types_$i"];
                                    $j++;
                                }
                            }
                        }
                        //echo "<pre>";print_R($otherCharges);print_R($_POST);die;
                        $gmServiceTypes =CommonComponent::getAllGMServiceTypesforSeller();
                
                        if(isset($_POST['sellerpoststatus_previous']) && $_POST['sellerpoststatus_previous'] == 1){
                            $arr = array (
                                'to_date' => CommonComponent::convertDateForDatabase($request->input ( 'valid_to' )),
                                
                                'terms_conditions' => $request->input ( 'terms_conditions' ),
                                'lkp_payment_mode_id' => $request->input ( 'paymentterms' ),
                                'accept_payment_netbanking' => $accept_payment_netbanking,
                                'accept_payment_credit' => $accept_payment_credit,
                                'accept_payment_debit' => $accept_payment_debit,
                                'credit_period' => $request->input ( 'credit_period_ptl' ),
                                'credit_period_units' => $request->input ( 'credit_period_units' ),
                                'accept_credit_netbanking' => $accept_credit_netbanking,
                                'accept_credit_cheque' => $accept_credit_cheque,
                                
                                'other_charge1_text' => (isset ( $otherCharges ['labeltext'] ['0'] )) ? $otherCharges ['labeltext'] ['0'] : "",
                                'other_charge2_text' => (isset ( $otherCharges ['labeltext'] ['1'] )) ? $otherCharges ['labeltext'] ['1'] : "",
                                'other_charge3_text' => (isset ( $otherCharges ['labeltext'] ['2'] )) ? $otherCharges ['labeltext'] ['2'] : "",
                                'cancellation_charge_price' => (isset ( $_POST ['cancellation_charge_price'] )) ? $_POST ['cancellation_charge_price'] : "",
                                'docket_charge_price' => (isset ( $_POST ['docket_charge_price'] )) ? $_POST ['docket_charge_price'] : "",
                                'other_charge1_price' => (isset ( $otherCharges ['terms_condtion_types'] [0] )) ? $otherCharges ['terms_condtion_types'] [0] : "",
                                'other_charge2_price' => (isset ( $otherCharges ['terms_condtion_types'] [1] )) ? $otherCharges ['terms_condtion_types'] [1] : "",
                                'other_charge3_price' => (isset ( $otherCharges ['terms_condtion_types'] [2] )) ? $otherCharges ['terms_condtion_types'] [2] : "",
                                'lkp_post_status_id' => $poststatus,
                                'lkp_access_id' => $lkp_access_id
                            );
                            
                        }else{
                            $arr = array (
                                'to_date' => CommonComponent::convertDateForDatabase($request->input ( 'valid_to' )),
                                'other_charge1_text' => (isset ( $otherCharges ['labeltext'] ['0'] )) ? $otherCharges ['labeltext'] ['0'] : "",
                                'other_charge2_text' => (isset ( $otherCharges ['labeltext'] ['1'] )) ? $otherCharges ['labeltext'] ['1'] : "",
                                'other_charge3_text' => (isset ( $otherCharges ['labeltext'] ['2'] )) ? $otherCharges ['labeltext'] ['2'] : "",
                                'cancellation_charge_price' => (isset ( $_POST ['cancellation_charge_price'] )) ? $_POST ['cancellation_charge_price'] : "",
                                'docket_charge_price' => (isset ( $_POST ['docket_charge_price'] )) ? $_POST ['docket_charge_price'] : "",
                                'other_charge1_price' => (isset ( $otherCharges ['terms_condtion_types'] [0] )) ? $otherCharges ['terms_condtion_types'] [0] : "",
                                'other_charge2_price' => (isset ( $otherCharges ['terms_condtion_types'] [1] )) ? $otherCharges ['terms_condtion_types'] [1] : "",
                                'other_charge3_price' => (isset ( $otherCharges ['terms_condtion_types'] [2] )) ? $otherCharges ['terms_condtion_types'] [2] : "",
                            );
                        }
                        foreach($gmServiceTypes as $gmServiceType){
                            //$sid        =   $gmServiceType->id;
                            $str_name   =   strtolower(str_replace(' ','_',$gmServiceType->service_type));
                            if($_POST[$str_name]!=''){
                                $arr[$str_name] = $_POST[$str_name];
                            }else{
                                $arr[$str_name] ="";
                            }
                        }

                        $sellerpost::where ( "id", $sid )->update ($arr);
                        
                            //*******matching engine***********************//
                            if($poststatus == 2){
                                $request = array();
                                
                                $request['to_location_id'] = $_POST ['to_location_id'];
                                $request['post_type'] = 1;
                                //$request['pettypes'] = $_POST ['pettypes_hidden'] [$i];
                                foreach($gmServiceTypes as $gmServiceType){
                                    $str_name   =   strtolower(str_replace(' ','_',$gmServiceType->service_type));
                                    if($_POST[$str_name]!=''){
                                        $request[$str_name] = $_POST[$str_name];
                                    }
                                }
                                $request['valid_from'] = $_POST ['valid_from_hidden'];
                                $request['valid_to'] = CommonComponent::convertDateForDatabase($_POST ['valid_to']);
                                
                                SellerMatchingComponent::doMatching(RELOCATION_GLOBAL_MOBILITY, $sid, 2, $request);
                            }
                            //*******matching engine***********************//
                        

                        if(isset($_POST['optradio']) && $is_private == 2){
                            if(isset($_POST['buyer_list_for_sellers_hidden']) && $_POST['buyer_list_for_sellers_hidden'] != ''){
                                $post_list_of_buyers = DB::table('relocationpet_seller_selected_buyers')->where('seller_post_id', $sid)->lists('buyer_id');
                                DB::table('relocationpet_seller_selected_buyers')->where('seller_post_id', $sid)->delete();
                                for($i = 0; $i < $buyer_list_count; $i ++) {
                                    $sellerpost_for_buyers  =  new RelocationpetSellerSelectedBuyer();
                    
                                    $sellerpost_for_buyers->seller_post_id = $sid;
                                    $sellerpost_for_buyers->buyer_id = $buyer_list[$i];
                                    $created_at = date ( 'Y-m-d H:i:s' );
                                    $createdIp = $_SERVER ['REMOTE_ADDR'];
                                    $sellerpost_for_buyers->created_by = Auth::id();
                                    $sellerpost_for_buyers->created_at = $created_at;
                                    $sellerpost_for_buyers->created_ip = $createdIp;
                                    $sellerpost_for_buyers->save();
                                    if (!in_array($buyer_list[$i], $post_list_of_buyers)){
                                        $seller_selected_buyers_email = DB::table('users')->where('id', $buyer_list[$i])->get();
                                        $seller_selected_buyers_email[0]->randnumber = $randnumber;
                                        $seller_selected_buyers_email[0]->sellername = Auth::User()->username;
                                        CommonComponent::send_email(SELLER_CREATED_POST_FOR_BUYERS,$seller_selected_buyers_email);
                                    }
                                    //CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
                                    
                                    if($poststatus == OPEN){
                                        //*******Send Sms to the private buyers***********************//
                                        $msg_params = array(
                                                'randnumber' => $randnumber,
                                                'sellername' => Auth::User()->username,
                                                'servicename' => 'RELOCATION PET MOVE'
                                        );
                                        $getMobileNumber  =   CommonComponent::getMobleNumber($buyer_list[$i]);
                                        if($getMobileNumber)
                                            CommonComponent::sendSMS($getMobileNumber,SELLER_UPDATED_POST_FOR_BUYERS_SMS,$msg_params);
                                        //*******Send Sms to the private buyers***********************//
                                    }
                                    
                                }
                            }
                        }
                        if($poststatus == 2)
                            return redirect ( "/sellerlist" )->with ( 'message_update_post', 'Post Updated Successfully' );
                        else
                            return redirect ( "/sellerlist" )->with ( 'message_update_post', 'Post Updated Successfully' );
                    }

                    // Retrieval of payment methods
                    $payment_methods = CommonComponent::getPaymentTerms ();
                    
                    
                    $seller_post_edit_action = DB::table ( 'relocationgm_seller_posts as rsp' )->where ( 'rsp.id', $sid )->select ( 'rsp.*' )->first ();
                    

                    $selectedbuyers = DB::table ( 'relocationgm_seller_selected_buyers as rpss' )
                                        ->leftjoin ( 'users as u', 'rpss.buyer_id', '=', 'u.id' )
                                        ->leftjoin ( 'buyer_business_details as bbds', 'rpss.buyer_id', '=', 'bbds.user_id' )
                                        ->where ( 'rpss.seller_post_id', $sid )
                                        ->select ( 'rpss.buyer_id', 'u.username', 'bbds.principal_place' )->get ();
                    
                    //echo "<pre>";print_R($seller_post_edit_action);print_R($seller_post_edit_action_lines);die;

                    $userId = Auth::User ()->id;
                    $user_subcsriptions = DB::table ( 'seller_details' )->where ( 'user_id', $userId )->first ();

                    if ($user_subcsriptions) {
                        $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                        $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                        $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                        $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                        $current_date_seller = date ( "Y-m-d" );
                    } else {
                        $user_subcsriptions = DB::table ( 'sellers' )->where ( 'user_id', $userId )->first ();
                        $subscription_start_date = date_create ( $user_subcsriptions->subscription_start_date );
                        $subscription_end_date = date_create ( $user_subcsriptions->subscription_end_date );
                        $subscription_start_date_start = date_format ( $subscription_start_date, "Y-m-d" );
                        $subscription_end_date_end = date_format ( $subscription_end_date, "Y-m-d" );
                        $current_date_seller = date ( "Y-m-d" );
                    }

                    if (isset ( $seller_post_edit_action->lkp_access_id ) && $seller_post_edit_action->lkp_access_id == 1) {
                        $private_seller = false;
                        $public_seller = true;
                    } else {
                        $private_seller = true;
                        $public_seller = false;
                    }
                    return view ( 'relocationglobal.sellers.seller_creation', [
                        'seller_post_edit' => $seller_post_edit_action,
                        'url_search_search' => '',
                        'private' => $private_seller,
                        'public' => $public_seller,
                        'paymentterms' => $payment_methods,
                        'gmServiceTypes' => $gmServiceTypes,
                        'seller_postid' => $sid,
                        'selectedbuyers' => $selectedbuyers,
                        'subscription_start_date_start' => $subscription_start_date_start,
                        'subscription_end_date_end' => $subscription_end_date_end,
                        'current_date_seller' => $current_date_seller
                    ] );
        } catch ( Exception $e ) {
            echo 'Caught exception: ', $e->getMessage (), "\n";
        }            
    }



}
