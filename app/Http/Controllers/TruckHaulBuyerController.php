<?php

namespace App\Http\Controllers;

use App\Models\TruckhaulBuyerQuote;
use App\Models\TruckhaulBuyerQuoteItem;
use App\Buyer;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TruckhaulBuyerQuoteSellersQuotesPrice;
use App\Models\TruckhaulBuyerQuoteSelectedSeller;
use App\Components\MessagesComponent;
use App\Components\CommonComponent;
use App\Components\Matching\BuyerMatchingComponent;
use App\Components\Matching\SellerMatchingComponent;
use App\Components\BuyerComponent;
use App\Components\TruckHaul\TruckHaulBuyerComponent;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\Mail;
use Response;
use Log;

class TruckHaulBuyerController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new CreateBuyerQuote.
     * Create new quotes to sellers
     * @return \Illuminate\Http\Response
     */
    public function CreateBuyerQuote(Request $request) 
    {
    	
    	//Added condition for change services bug no : 0050171
         if(Session::get('service_id') == ROAD_FTL){
                return redirect('/createbuyerquote');
        }else if(Session::get('service_id') == ROAD_PTL || Session::get('service_id') == RAIL 
                || Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL 
                || Session::get('service_id') == OCEAN || Session::get('service_id') == COURIER){
                return redirect('ptl/createbuyerquote');
        }else if(Session::get('service_id') == ROAD_INTRACITY){
                return redirect('/intracity/buyer_post');
        }else if(Session::get('service_id') == RELOCATION_DOMESTIC || Session::get('service_id') == RELOCATION_PET_MOVE || Session::get('service_id') == RELOCATION_INTERNATIONAL || Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY || Session::get('service_id') == RELOCATION_OFFICE_MOVE){
                return redirect('/relocation/creatbuyerrpost');
        }else if(Session::get('service_id') == ROAD_TRUCK_LEASE){
            return redirect('trucklease/createbuyerquote');
        }
        try {
            Log::info('Create new truck hual buyer quote: ' . Auth::id(), array('c' => '1'));
            $roleId = Auth::User()->lkp_role_id;
            //Saving the user activity to the buyer new quote log table
            if ($roleId == BUYER) {
                CommonComponent::activityLog("TRUCKHAUL_BUYER_ADDED_NEW_QUOTE", TRUCKHAUL_BUYER_ADDED_NEW_QUOTE, 0, HTTP_REFERRER, CURRENT_URL);
            }

            if (!empty(Input::all())) {
            	//echo "<pre>";print_r($_POST);exit;
                $all_var = Input::all();

                $created_at = date('Y-m-d H:i:s');
                $createdIp = $_SERVER ['REMOTE_ADDR']; 
                $serviceId = Session::get('service_id');
                $created_year = date('Y');
                $ordid  =   CommonComponent::getPostID($serviceId);                
                $trans_randid = 'TRUCKHAUL/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT);
                
                if ($all_var['lead_type']==2 && isset($all_var['lead_type']) && !empty($all_var['lead_type'])) {
                	$postType = $all_var['lead_type'];
                	if(Session::get ( 'service_id' ) != ''){
                		$serviceId = Session::get ( 'service_id' );
                	}
					$createQuote=TermBuyerComponent::TermBuyerCreateQuote($serviceId, $_REQUEST, $postType);
                	
                	if($createQuote!=''){
                		$multi_data_count = count($all_var['load_type']);
                		//return redirect('/createbuyerquote')->with('transactionId', $createQuote)->with('postsCount',$multi_data_count)->with('postType',$postType);
                        if (!empty($_REQUEST['confirm_but']) && isset($_REQUEST['confirm_but'])) {
                            $postStatus= OPEN;
                        } else {
                            $postStatus= SAVEDASDRAFT;
                        }
                        if($postStatus == OPEN){
                            return redirect('/createbuyerquote')->with('transactionId', $createQuote)->with('postsCount',$multi_data_count)->with('postType',$postType);
                        }else{
                            return redirect('/buyerposts')->with('sumsg', "Post was saved as draft")->with('postsCount',$multi_data_count)->with('postType',$postType);
                        }
                		//return redirect('/buyerposts')->with('succmsg', 'Buyer post submitted successfully.');
                	}
                	
                } else {                
                if (isset($_REQUEST['quoteaccess_id']) && !empty($_REQUEST['quoteaccess_id']) ) {
                    $is_private = $_REQUEST['quoteaccess_id'];
                }
               
                
                if (isset($is_private) == '2' && !empty($is_private)) {
                    
                    if ($all_var['seller_list'] != "") {
                        $seller_list = explode(",", $all_var['seller_list']);
                        $seller_list_count = count($seller_list);
                    }
                } else {
                    $is_private = 1;
                }
				
                /******Single insert in buer quote table******** */
                $fromcities = array();
                $buyerquote = new TruckHaulBuyerQuote();
                $buyerquote->lkp_service_id = ROAD_TRUCK_HAUL;
                $buyerquote->lkp_lead_type_id = $request->lead_type;
                $buyerquote->lkp_quote_access_id = $is_private;
                $buyerquote->transaction_id = $trans_randid;
                $buyerquote->buyer_id = Auth::id();
                $buyerquote->is_commercial = $request->is_commercial;
                $buyerquote->created_by = Auth::id();
                $buyerquote->created_at = $created_at;
                $buyerquote->created_ip = $createdIp;

                //below array for matching engine in Truck Haul
                $matchedItems = array ();
                if ($buyerquote->save()) {
                	$transactionId = $buyerquote->transaction_id;
                    //Maintaining a log of data for buyer new quote creation
                    CommonComponent::auditLog($buyerquote->id, 'truckhaul_buyer_quotes');
                    
                    if (!empty($all_var['load_type'])) {
                        $multi_data_count = count($request->load_type);
                        for ($i = 0; $i < $multi_data_count; $i++) {
                            /******Multiple insert in quote items******** */
                            $Quote_Lineitems = new TruckHaulBuyerQuoteItem();
                            $Quote_Lineitems->buyer_quote_id = $buyerquote->id;
                            $Quote_Lineitems->dispatch_date = CommonComponent::convertDateForDatabase($request->reporting_date[$i]);
                            $Quote_Lineitems->lkp_quote_price_type_id = $request->quote_id[$i];
                            $Quote_Lineitems->from_city_id = $request->from_location[$i];
                            $Quote_Lineitems->to_city_id = $request->to_location[$i];
                            $Quote_Lineitems->lkp_load_type_id = $request->load_type[$i];
                            $Quote_Lineitems->lkp_vehicle_type_id = $request->vehicle_type[$i];
                            $Quote_Lineitems->units = $request->capacity[$i];
                            $Quote_Lineitems->number_loads = 1;
                            $Quote_Lineitems->quantity = $request->quantity[$i];
                            $Quote_Lineitems->price = $request->price[$i];
                            $Quote_Lineitems->is_dispatch_flexible = $request->is_dispatch_flexible[$i];
                            $Quote_Lineitems->is_delivery_flexible = $request->is_delivery_flexible[$i];
                            $Quote_Lineitems->lkp_post_status_id = 2;
                            $Quote_Lineitems->created_by = Auth::id();
                            $Quote_Lineitems->created_at = $created_at;
                            $Quote_Lineitems->created_ip = $createdIp;
                            $Quote_Lineitems->save();
                            $fromcities[] = $request->from_location[$i];
                            //below array for matching engine in Truck Haul start
                            $matchedItems['from_location_id']=$_POST['from_location'][$i];
                            $matchedItems['to_location_id']=$_POST['to_location'][$i];
                            $matchedItems['lkp_load_type_id']=$_POST['load_type'][$i];
                            $matchedItems['lkp_vehicle_type_id']=$_POST['vehicle_type'][$i];
                            $matchedItems['from_date']=$_POST['reporting_date'][$i];
                            //$matchedItems['to_date']=$_POST['delivery_date'][$i];
                            BuyerMatchingComponent::doMatching(ROAD_TRUCK_HAUL,$Quote_Lineitems->id,2,$matchedItems);
                            //below array for matching engine in Truck Haul end
                            
                            //Buyer Seller Price list code new table storing data
                            if ($is_private == '2' && !empty($is_private)) {
                                if ($all_var['seller_list'] != "") {
                                    if ($seller_list_count != 0) {
                                        for ($j = 0; $j < $seller_list_count; $j ++) {
                                            $Quote_quote_prices_list = new TruckHaulBuyerQuoteSellersQuotesPrice();
                                            $Quote_quote_prices_list->buyer_id = Auth::id();
                                            $Quote_quote_prices_list->buyer_quote_item_id = $Quote_Lineitems->id;
                                            $Quote_quote_prices_list->seller_id = $seller_list[$j];
                                            $Quote_quote_prices_list->firm_price = $request->price[$i];
                                            $Quote_quote_prices_list->created_by = Auth::id();
                                            $Quote_quote_prices_list->created_at = $created_at;
                                            $Quote_quote_prices_list->created_ip = $createdIp;
                                            $Quote_quote_prices_list->save();
                                        }
                                    }
                                }
                            }


                            //Maintaining a log of data for buyer new quote multiple items creation
                            CommonComponent::auditLog($Quote_Lineitems->id, 'truckhaul_buyer_quote_items');
                        }

                        if ($is_private == '2') {
                            if ($all_var['seller_list'] != "") {
                                if ($seller_list_count != 0) {
			
                                    for ($i = 0; $i < $seller_list_count; $i ++) {
                                        $Quote_seller_list = new TruckHaulBuyerQuoteSelectedSeller();
                                        $Quote_seller_list->buyer_quote_id = $buyerquote->id;
                                        $Quote_seller_list->seller_id = $seller_list[$i];
                                        $Quote_seller_list->created_by = Auth::id();
                                        $Quote_seller_list->created_at = $created_at;
                                        $Quote_seller_list->created_ip = $createdIp;
                                        $Quote_seller_list->save();

                                        //below code  for sent mails to selelcted sellers in private post
                                        $buyers_selected_sellers_email = DB::table('users')->where('id', $seller_list[$i])->get();
                                        $buyers_selected_sellers_email[0]->randnumber = $trans_randid;
                                        $buyers_selected_sellers_email[0]->buyername = Auth::User()->username;
                                        CommonComponent::send_email(TRUCKHAUL_BUYER_CREATED_POST_FOR_SELLERS, $buyers_selected_sellers_email);
										
                                        //*******Send Sms to the private Sellers***********************//
                                        $msg_params = array(
                                        		'randnumber' => $trans_randid,
                                        		'buyername' => Auth::User()->username,
                                        		'servicename' => 'Truck Haul'
                                        );
                                        $getMobileNumber  =   CommonComponent::getMobleNumber($seller_list[$i]);
                                        if($getMobileNumber)
                                        CommonComponent::sendSMS($getMobileNumber,TRUCKHAUL_BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                                        //*******Send Sms to the private Sellers***********************//
                                        

                                        //Maintaining a log of data for buyer new seller data multiple  creation
                                        CommonComponent::auditLog($Quote_seller_list->id, 'truckhaul_buyer_quote_selected_sellers');
                                    }
                                }
                            }
                        }else{
                        		//*******Send Sms to the private Sellers***********************//
                        		$msg_params = array(
                        				'randnumber' => $trans_randid,
                        				'buyername' => Auth::User()->username,
                        				'servicename' => 'Truck Haul'
                        		);
                        		$getSellerIds  =   TruckHaulBuyerController::getSellerslist($fromcities);
                        		//echo "<pre>";print_r($getSellerIds);exit;
                        		for($i=0;$i<count($getSellerIds);$i++){	
                        			$getMobileNumber  =   CommonComponent::getMobleNumber($getSellerIds[$i]['id']);
                        			if($getMobileNumber)
                        			CommonComponent::sendSMS($getMobileNumber,TRUCKHAUL_BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                        		}
                        		//*******Send Sms to the private Sellers***********************//
                        	                     
                        	
                        }

                       // return redirect('/createbuyerquote')->with('transactionId', $transactionId);
                        $multi_data_count = count($request->load_type);
                        return redirect('truckhaul/createbuyerquote')->with('transactionId', $transactionId)->with('postsCount',$multi_data_count)->with('postType',1);
                    }
                }
            }
        }
            $session_search_values = array();
            $url_search= explode("?",HTTP_REFERRER);
            $url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);
    	
            
            if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != ''){
            	$serverpreviUrL =$_SERVER['HTTP_REFERER'];
            }else{
            	$serverpreviUrL ='';
            }
            
            
            if($url_search_search == 'byersearchresults'){
            	$session_search_values[] = Session::get('searchMod.delivery_date_buyer');
            	$session_search_values[] = Session::get('searchMod.dispatch_date_buyer');
            	$session_search_values[] = Session::get('searchMod.vehicle_type_buyer');
            	$session_search_values[] = Session::get('searchMod.load_type_buyer');
            	$session_search_values[] = Session::get('searchMod.from_city_id_buyer');
            	$session_search_values[] = Session::get('searchMod.to_city_id_buyer');
            	$session_search_values[] = Session::get('searchMod.from_location_buyer');
            	$session_search_values[] = Session::get('searchMod.to_location_buyer');
            	$session_search_values[] = Session::get('searchMod.quantity_buyer');
            	$session_search_values[] = Session::get('searchMod.capacity_buyer');
                $vehicle_types = DB::table('lkp_vehicle_types')->select('capacity', 'dimension')->where('id', Session::get('searchMod.vehicle_type_buyer'))->get();
                //$vehcile_type = $vehicle_types[0]->capacity;
                //$noofloads = CommonComponent::ftlNoofLoads(Session::get('session_vehicle_type_buyer'));
                $session_search_values[] = 1;
                $session_search_values[] = $vehicle_types[0]->dimension;
                
            }else{
            	$session_search_values[] = Session::put('searchMod.delivery_date_buyer','');
            	$session_search_values[] = Session::put('searchMod.dispatch_date_buyer','');
            	$session_search_values[] = Session::put('searchMod.vehicle_type_buyer','');
            	$session_search_values[] = Session::put('searchMod.load_type_buyer','');
            	$session_search_values[] = Session::put('searchMod.from_city_id_buyer','');
            	$session_search_values[] = Session::put('searchMod.to_city_id_buyer','');
            	$session_search_values[] = Session::put('searchMod.from_location_buyer','');
            	$session_search_values[] = Session::put('searchMod.to_location_buyer','');
            	$session_search_values[] = Session::put('searchMod.quantity_buyer','');
            	$session_search_values[] = Session::put('searchMod.capacity_buyer','');
                $session_search_values[] ="";
                $session_search_values[] ="";
            }
            
            
            $vehicle_type = CommonComponent::getAllVehicleType();
            $load_type = CommonComponent::getAllLoadTypes();
            $lead_type = \DB::table('lkp_lead_types')->lists('lead_type', 'id');
            $quote_price_type = \DB::table('lkp_quote_price_types')->lists('price_type', 'id');
            $bid_type = \DB::table('lkp_bid_types')->lists('bid_type', 'id');
            return view('truckhaul.buyers.create_buyer_quote', array(
            		'transactionId'=>'',
            		'vehicle_type' => $vehicle_type, 
            		'load_type' => $load_type,
            		'url_search_search' => $url_search_search,
            		'serverpreviUrL' => $serverpreviUrL,
            		'session_search_values_create'=> $session_search_values,
            		'bid_type' => $bid_type,
            		'lead_type' => $lead_type, 'quote_price_type' => $quote_price_type));
           } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editBuyerquote($mainid, $itemid) {
        try {
            Log::info('Update  buyer quote: ' . Auth::id(), array('c' => '1'));
            $buyer_post_edit_action = DB::table('buyer_quotes')
                    ->leftjoin('buyer_quote_items', 'buyer_quote_items.buyer_quote_id', '=', 'buyer_quotes.id')
                    ->leftjoin('lkp_cities as c1', 'buyer_quote_items.from_city_id', '=', 'c1.id')
                    ->leftjoin('lkp_cities as c2', 'buyer_quote_items.to_city_id', '=', 'c2.id')
                    ->leftjoin('lkp_vehicle_types as vt', 'buyer_quote_items.lkp_vehicle_type_id', '=', 'vt.id')
                    ->where('buyer_quotes.id', $mainid)
                    ->where('buyer_quote_items.id', $itemid)
                    ->select('buyer_quote_items.*', 'vt.vehicle_type as vehicle_type', 'c1.city_name as from_locationcity', 'c2.city_name as to_locationcity')
                    ->get();
            $buyer_post_edit_seller = DB::table('buyer_quotes')
                    ->leftjoin('buyer_quote_selected_sellers as seller', 'seller.buyer_quote_id', '=', 'buyer_quotes.id')
                    ->leftjoin('users as u', 'u.id', '=', 'seller.seller_id')
                    ->where('buyer_quotes.id', $mainid)
                    ->select('seller.seller_id', 'u.username', 'u.id')
                    ->get();
            $buyer_post_id = DB::table('buyer_quotes')
                    ->where('buyer_quotes.id', $mainid)
                    ->select('buyer_quotes.id', 'buyer_quotes.transaction_id')
                    ->first();
                	
            $buyer_quote_lineitem_id = $mainid;
          
            return view('buyers.editbuyer', array('buyer_post_edit' => $buyer_post_edit_action, $buyer_quote_lineitem_id, 'buyer_post_edit_seller' => $buyer_post_edit_seller, 'buyer_post_id' => $buyer_post_id));
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    public function updateBuyer(Request $request) {
        try {
            Log::info('Update buyer quote Sellers list and insert new sellers: ' . Auth::id(), array('c' => '1'));
            $roleId = Auth::User()->lkp_role_id;
            //Update the user activity to the buyer edit  form
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_UPDATE_QUOTE", BUYER_UPDATE_QUOTE, 0, HTTP_REFERRER, CURRENT_URL);
            }

            $selle_old_implode = rtrim(implode(',', $_REQUEST['seller_list']), ',');
            $seller_list = explode(",", $selle_old_implode);
            $selle_old_implode = array_filter($seller_list);
            $seller_list_count = count($selle_old_implode);
            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER ['REMOTE_ADDR'];
                       
            $rand_id = rand();
            $str1 = "Truck Haul_";
            $trans_randid = $str1 . $rand_id;

            if ($seller_list_count != 0) {
                for ($i = 0; $i < $seller_list_count; $i ++) {
                    $Quote_seller_list = new BuyerQuoteSelectedSellers();
                    $Quote_seller_list->buyer_quote_id = $request->buyer_id;
                    $Quote_seller_list->seller_id = $seller_list[$i];
                    $Quote_seller_list->updated_by = Auth::id();
                    $Quote_seller_list->updated_at = $created_at;
                    $Quote_seller_list->updated_ip = $createdIp;
                    $Quote_seller_list->save();
                    
                    $Quote_quote_prices_list = new BuyerQuoteSellersQuotesPrices();
                    $Quote_quote_prices_list->buyer_id = Auth::id();
                    $Quote_quote_prices_list->buyer_quote_item_id = $request->buyer_items_id;;
                    $Quote_quote_prices_list->seller_id = $seller_list[$i];
                    if ($request->hidden_price_typeid == 2 ) {
                    	$Quote_quote_prices_list->firm_price = $request->hidden_price;
                    }                    
                    $Quote_quote_prices_list->created_by = Auth::id();
                    $Quote_quote_prices_list->created_at = $created_at;
                    $Quote_quote_prices_list->created_ip = $createdIp;
                    $Quote_quote_prices_list->save();
                    
                    //below code  for sent mails to selelcted sellers in private post
                    $buyers_selected_sellers_email = DB::table('users')->where('id', $seller_list[$i])->get();
                    $buyers_selected_sellers_email[0]->randnumber = $trans_randid;
                    $buyers_selected_sellers_email[0]->buyername = Auth::User()->username;
                    CommonComponent::send_email(BUYER_CREATED_POST_FOR_SELLERS, $buyers_selected_sellers_email);
                    
                    //Maintaining a log of data for buyer update quote in quote log table
                    CommonComponent::auditLog($Quote_seller_list->id, 'buyer_quote_selected_sellers');
                }
            } else {
                return redirect('editbuyerquote/' . $request->buyer_id . '/' . $request->buyer_items_id)->with('sumsg1', 'Buyer Quote Updated Failed.');
            }

            return redirect('buyerposts')->with('sumsg', 'Buyer post updated successfully.');
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

   
    
   

    /******* Below Script for get seller list from city************** */
    public static function getSellerslist($cities = array()) {

        $results = array();
       
        try {
            Log::info('Get Seller lsit from depends on from city: ' . Auth::id(), array('c' => '1'));
            $roleId = Auth::User()->lkp_role_id;
            //Update the user activity to the buyer get seller list
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_SELLERLIST", BUYER_SELLERLIST, 0, HTTP_REFERRER, CURRENT_URL);
            }

            $sellerlist = (count($cities) > 0) ? $cities : $_POST['seller_list'];
            if(isset($sellerlist)){
	            $sellersStr = $sellerlist;
	            $districts = DB::table('lkp_cities')
	                    ->whereIn('lkp_cities.id', $sellersStr)
	                    ->select('lkp_cities.lkp_district_id')
	                    ->get();

	            foreach ($districts as $dist) {
	                $district_array[] = $dist->lkp_district_id;
	            }
            }
            
               
            $seller_data = DB::table('seller_post_items')
            ->join('users', 'seller_post_items.created_by', '=', 'users.id')
            ->leftjoin('sellers', 'users.id', '=', 'sellers.user_id')
            ->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
            ->distinct('seller_post_items.created_by')
            ->whereIn('seller_post_items.lkp_district_id', $district_array)
            ->where('users.lkp_role_id', SELLER)
            ->orWhere('users.secondary_role_id', SELLER)
            ->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
            ->get();
                    
            foreach ($seller_data as $query) {
                $results[] = ['id' => $query->id, 'name' => $query->username . ' ' . $query->principal_place . ' ' . $query->id];
            }
            if(count($cities) > 0){
            	return $results;
            }else{
            	return Response::json($results);
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }


    
    /****** Below Script for get seller list from city************** */
    
    public function getEditSellerslist() {

    	$results = array();
    	try {
    		Log::info('Get Seller lsit from depends on from city: ' . Auth::id(), array('c' => '1'));
    		$roleId = Auth::User()->lkp_role_id;
    		$serviceId = Session::get('service_id');
    		//Update the user activity to the buyer get seller list
    		if ($roleId == BUYER) {
    			CommonComponent::activityLog("BUYER_SELLERLIST", BUYER_SELLERLIST, 0, HTTP_REFERRER, CURRENT_URL);
    		}
    		//getting sekller values for checking duplicates in edit seller post
    		$quoteId=  $_POST['buyer_quote_id'];
    		if($serviceId==ROAD_FTL){
    		$sellerIds = DB::table('buyer_quote_selected_sellers')
    		->where('buyer_quote_selected_sellers.buyer_quote_id', $quoteId)
    		->select('buyer_quote_selected_sellers.seller_id')
    		->get();  
    		}else{
    		$sellerIds = DB::table('relocation_buyer_selected_sellers')
    		->where('relocation_buyer_selected_sellers.buyer_post_id', $quoteId)
    		->select('relocation_buyer_selected_sellers.seller_id')
    		->get();
    		}  		
    		$sellerall_ids	=	array();
    		foreach ($sellerIds as $sellerdata) {
    			$sellerall_ids[] = $sellerdata->seller_id;
    		}    		
    		//$term = Input::get('q');
    		$sellersStr = $_POST['seller_list'];
    
    		$districts = DB::table('lkp_cities')
    		->whereIn('lkp_cities.id', $sellersStr)
    		->select('lkp_cities.lkp_district_id')
    		->get();
    		//$district_array	=	array();
    		foreach ($districts as $dist) {
    			$district_array[] = $dist->lkp_district_id;
    		}
    		$seller_data = DB::table('seller_post_items')
    		->join('users', 'seller_post_items.created_by', '=', 'users.id')
    		->leftjoin('sellers', 'users.id', '=', 'sellers.user_id')
    		->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
    		->distinct('seller_post_items.created_by')
    		->whereIn('seller_post_items.lkp_district_id', $district_array)
    		->whereNotIn('seller_post_items.created_by', $sellerall_ids)
    		->where('users.lkp_role_id', SELLER)
            ->orWhere('users.secondary_role_id', SELLER)
    		->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
    		->get();

    		foreach ($seller_data as $query) {
    			$results[] = ['id' => $query->id, 'name' => $query->username . ' ' . $query->principal_place . ' ' . $query->id];
    		}
    		return Response::json($results);
    	} catch (Exception $e) {
    		echo 'Caught exception: ', $e->getMessage(), "\n";
    	}
    }
}
