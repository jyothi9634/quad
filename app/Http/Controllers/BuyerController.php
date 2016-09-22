<?php

namespace App\Http\Controllers;

use App\BuyerQuotes;
use App\BuyerQuoteItems;
use App\Buyer;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\BuyerQuoteSellersQuotesPrices;
use App\Models\BuyerQuoteSelectedSellers;
use App\Models\PtlBuyerQuote;
use App\Models\PtlBuyerQuoteItem;
//Truck Haul Models
use App\Models\TruckhaulBuyerQuoteSellersQuotesPrice;
use App\Models\TruckhaulBuyerQuoteSelectedSeller;
use App\Models\TruckhaulBuyerQuote;
use App\Models\TruckhaulBuyerQuoteItem;

use App\Models\TruckleaseBuyerQuoteSellersQuotesPrice;
use App\Models\TruckleaseBuyerQuoteSelectedSeller;
use App\Models\RailBuyerQuote;
use App\Models\RailBuyerQuoteItem;
use App\Models\AirdomBuyerQuote;
use App\Models\AirdomBuyerQuoteItem;
use App\Models\AirintBuyerQuote;
use App\Models\AirintBuyerQuoteItem;
use App\Models\OceanBuyerQuote;
use App\Models\OceanBuyerQuoteItem;
use App\Components\MessagesComponent;
use App\Models\CourierBuyerQuote;
use App\Models\CourierBuyerQuoteItem;
use App\Components\Ftl\FtlSellerListingComponent;
use App\Components\ptl\PtlSellerListingComponent;
use App\Components\CommonComponent;
use App\Components\Matching\BuyerMatchingComponent;
use App\Components\Matching\SellerMatchingComponent;
use App\Components\BuyerComponent;
use App\Components\Ftl\FtlBuyerComponent;
use App\Components\Ptl\PtlBuyerComponent;
use App\Components\Rail\RailBuyerComponent;
use App\Components\AirDomestic\AirDomesticBuyerComponent;
use App\Components\Intracity\IntracityBuyerComponent;
use App\Components\Term\TermBuyerComponent;
use App\Components\Relocation\RelocationBuyerComponent;
use App\Components\TruckHaul\TruckHaulBuyerComponent;
use App\Components\TruckLease\TruckLeaseBuyerComponent;
use App\Components\TruckLease\TruckLeaseSellerComponent;
use App\Components\RelocationInt\RelocationIntBuyerComponent;
use App\Models\IctBuyerQuote;
use App\Models\IctBuyerQuoteItem;
use App\Models\RelocationBuyerPost;
use App\Components\RelocationPet\RelocationPetBuyerComponent;
use App\Models\LkpPtlPincode;
use App\Models\TruckleaseBuyerQuoteItem;
use App\Models\TruckleaseBuyerQuote;
use App\Models\RelocationofficeBuyerPost;
use App\Models\RelocationPetBuyerPost;
use App\Models\RelocationintBuyerPost;
use App\Models\RelocationgmBuyerPost;
use App\Models\RelocationgmBuyerQuoteItems;
use App\Models\RelocationBuyerPostInventoryParticular;
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

use App\Components\RelocationOffice\RelocationOfficeBuyerComponent;
use App\Components\RelocationGlobal\RelocationGlobalBuyerComponent;

class BuyerController extends Controller {

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
        if(Session::get('service_id') == ROAD_PTL || Session::get('service_id') == RAIL 
        		|| Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL 
        		|| Session::get('service_id') == OCEAN || Session::get('service_id') == COURIER){
                return redirect('ptl/createbuyerquote');
        }else if(Session::get('service_id') == ROAD_INTRACITY){
                return redirect('intracity/buyer_post');
        }else if(Session::get('service_id') == RELOCATION_DOMESTIC || Session::get('service_id') == RELOCATION_PET_MOVE || Session::get('service_id') == RELOCATION_INTERNATIONAL || Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY || Session::get('service_id') == RELOCATION_OFFICE_MOVE){
                return redirect('relocation/creatbuyerrpost');
        }else if(Session::get('service_id') == ROAD_TRUCK_HAUL){
                return redirect('truckhaul/createbuyerquote');
        }
        try {
            Log::info('Create new buyer quote: ' . Auth::id(), array('c' => '1'));
            $roleId = Auth::User()->lkp_role_id;
            //Saving the user activity to the buyer new quote log table
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_ADDED_NEW_QUOTE", BUYER_ADDED_NEW_QUOTE, 0, HTTP_REFERRER, CURRENT_URL);
            }

            if (!empty(Input::all())) {
            	
                $all_var = Input::all();

                $created_at = date('Y-m-d H:i:s');
                $createdIp = $_SERVER ['REMOTE_ADDR']; 
                $serviceId = Session::get('service_id');
                $created_year = date('Y');
                $ordid  =   CommonComponent::getPostID($serviceId);                
                $trans_randid = 'FTL/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT);
                
                if (isset($all_var['lead_type']) && $all_var['lead_type']==2 &&  !empty($all_var['lead_type'])) {
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
                		//return redirect('/buyerposts')->with('succmsg', 'Post submitted successfully.');
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
                $buyerquote = new BuyerQuotes();
                $buyerquote->lkp_service_id = $request->service_id;
                $buyerquote->lkp_lead_type_id = $request->lead_type;
                $buyerquote->lkp_quote_access_id = $is_private;
                $buyerquote->transaction_id = $trans_randid;
                $buyerquote->buyer_id = Auth::id();
                $buyerquote->is_commercial = $_POST['is_commercial'];
                $buyerquote->created_by = Auth::id();
                $buyerquote->created_at = $created_at;
                $buyerquote->created_ip = $createdIp;

                //below array for matching engine in FTL
                $matchedItems = array ();
                if ($buyerquote->save()) {
                	$transactionId = $buyerquote->transaction_id;
                    //Maintaining a log of data for buyer new quote creation
                    CommonComponent::auditLog($buyerquote->id, 'buyer_quotes');
                    
                    if (!empty($all_var['load_type'])) {
                        $multi_data_count = count($request->load_type);
                        for ($i = 0; $i < $multi_data_count; $i++) {

                            $stateids= CommonComponent::getStateId($_POST['from_location'][$i],$_POST['to_location'][$i]);
                            $incoming_docs = $outgoing_docs = null;
                            if($stateids->from_state_id != $stateids->to_state_id):
                                $documents =  CommonComponent::getStatutoryDocs(array('from_state_id'=>$stateids->from_state_id,'to_state_id'=>$stateids->to_state_id));
                                $incoming_docs = $documents->incoming_doc_id;
                                $outgoing_docs = $documents->outgoing_doc_id;
                            endif; 

                            /******Multiple insert in quote items******** */
                            $Quote_Lineitems = new BuyerQuoteItems();
                            $Quote_Lineitems->buyer_quote_id = $buyerquote->id;
                            $Quote_Lineitems->dispatch_date = CommonComponent::convertDateForDatabase($request->dispatch_date[$i]);
                            $Quote_Lineitems->delivery_date = CommonComponent::convertDateForDatabase($request->delivery_date[$i]);
                            $Quote_Lineitems->lkp_quote_price_type_id = $request->quote_id[$i];
                            $Quote_Lineitems->from_city_id = $request->from_location[$i];
                            $Quote_Lineitems->to_city_id = $request->to_location[$i];
                            $Quote_Lineitems->lkp_load_type_id = $request->load_type[$i];
                            $Quote_Lineitems->lkp_vehicle_type_id = $request->vehicle_type[$i];
                            $Quote_Lineitems->units = $request->capacity[$i];
                            $Quote_Lineitems->number_loads = $request->no_of_loads[$i];
                            $Quote_Lineitems->quantity = $request->quantity[$i];
                            $Quote_Lineitems->price = $request->price[$i];
                            $Quote_Lineitems->is_dispatch_flexible = $request->is_dispatch_flexible[$i];
                            $Quote_Lineitems->is_delivery_flexible = $request->is_delivery_flexible[$i];
                            $Quote_Lineitems->lkp_post_status_id = 2;
                            $Quote_Lineitems->created_by = Auth::id();
                            $Quote_Lineitems->created_at = $created_at;
                            $Quote_Lineitems->created_ip = $createdIp;

                            // Added for GSA docs
                            $Quote_Lineitems->incoming_docs = $incoming_docs;
                            $Quote_Lineitems->outgoing_docs = $outgoing_docs;

                            $Quote_Lineitems->save();
                            $fromcities[] = $request->from_location[$i];
                            //below array for matching engine in FTL start
                            $matchedItems['from_location_id']=$_POST['from_location'][$i];
                            $matchedItems['to_location_id']=$_POST['to_location'][$i];
                            $matchedItems['lkp_load_type_id']=$_POST['load_type'][$i];
                            $matchedItems['lkp_vehicle_type_id']=$_POST['vehicle_type'][$i];
                            $matchedItems['from_date']=$_POST['dispatch_date'][$i];
                            $matchedItems['to_date']=$_POST['delivery_date'][$i];
                            BuyerMatchingComponent::doMatching("1",$Quote_Lineitems->id,2,$matchedItems);
                            //below array for matching engine in FTL end
                            
                            //Buyer Seller Price list code new table storing data
                            if ($is_private == '2' && !empty($is_private)) {
                                if ($all_var['seller_list'] != "") {
                                    if ($seller_list_count != 0) {
                                       
                                        for ($j = 0; $j < $seller_list_count; $j ++) {
                                            $Quote_quote_prices_list = new BuyerQuoteSellersQuotesPrices();
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
                            CommonComponent::auditLog($Quote_Lineitems->id, 'buyer_quote_items');
                        }

                        if ($is_private == '2') {
                            if ($all_var['seller_list'] != "") {
                                if ($seller_list_count != 0) {
                                    //echo $seller_list_count; exit;				
                                    for ($i = 0; $i < $seller_list_count; $i ++) {
                                        $Quote_seller_list = new BuyerQuoteSelectedSellers();
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
                                        CommonComponent::send_email(BUYER_CREATED_POST_FOR_SELLERS, $buyers_selected_sellers_email);
										
                                        //*******Send Sms to the private Sellers***********************//
                                        $msg_params = array(
                                        		'randnumber' => $trans_randid,
                                        		'buyername' => Auth::User()->username,
                                        		'servicename' => 'FTL'
                                        );
                                        $getMobileNumber  =   CommonComponent::getMobleNumber($seller_list[$i]);
                                        if($getMobileNumber)
                                        CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                                        //*******Send Sms to the private Sellers***********************//
                                        

                                        //Maintaining a log of data for buyer new seller data multiple  creation
                                        CommonComponent::auditLog($Quote_seller_list->id, 'buyer_quote_selected_sellers');
                                    }
                                }
                            }
                        }else{
                        		//*******Send Sms to the private Sellers***********************//
                        		$msg_params = array(
                        				'randnumber' => $trans_randid,
                        				'buyername' => Auth::User()->username,
                        				'servicename' => 'FTL'
                        		);
                        		$getSellerIds  =   BuyerController::getSellerslist($fromcities);
                        	
                        		for($i=0;$i<count($getSellerIds);$i++){	
                        			$getMobileNumber  =   CommonComponent::getMobleNumber($getSellerIds[$i]['id']);
                        			if($getMobileNumber)
                        			CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                        		}
                        		//*******Send Sms to the private Sellers***********************//
                        	                     
                        	
                        }

                      
                        $multi_data_count = count($request->load_type);
                        return redirect('/createbuyerquote')->with('transactionId', $transactionId)->with('postsCount',$multi_data_count)->with('postType',1);
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
                if(Session::get('searchMod.vehicle_type_buyer')!="20"){
                    $noofloads = CommonComponent::ftlNoofLoads(Session::get('searchMod.vehicle_type_buyer'));
                    $session_search_values[] = $noofloads;
                }else{
                    $session_search_values[] = 0;
                }
                
                $session_search_values[] = $vehicle_types[0]->dimension;
                $session_search_values[] = Session::get('searchMod.fdelivery_date_buyer');
                $session_search_values[] = Session::get('searchMod.fdispatch_date_buyer');
                
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
                $session_search_values[] = Session::put('searchMod.fdelivery_date_buyer','');
                $session_search_values[] = Session::put('searchMod.fdispatch_date_buyer','');
            }
    
            
            $vehicle_type = CommonComponent::getAllVehicleType();
            $load_type = CommonComponent::getAllLoadTypes();
            $lead_type = \DB::table('lkp_lead_types')->lists('lead_type', 'id');
            $quote_price_type = \DB::table('lkp_quote_price_types')->lists('price_type', 'id');
            $bid_type = \DB::table('lkp_bid_types')->lists('bid_type', 'id');
            return view('buyers.createbuyer', array(
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
            $serviceId = Session::get('service_id');
            switch($serviceId){
                case ROAD_FTL:
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
                            ->select('buyer_quotes.id','buyer_quotes.lkp_service_id', 'buyer_quotes.transaction_id')
                            ->first();
                                
                    $buyer_quote_lineitem_id = $mainid;
                   
                   
                    return view('buyers.editbuyer', array('buyer_post_edit' => $buyer_post_edit_action, $buyer_quote_lineitem_id, 'buyer_post_edit_seller' => $buyer_post_edit_seller, 'buyer_post_id' => $buyer_post_id));
                    break;
                case ROAD_TRUCK_HAUL:
                    $buyer_post_edit_action = DB::table('truckhaul_buyer_quotes')
                            ->leftjoin('truckhaul_buyer_quote_items', 'truckhaul_buyer_quote_items.buyer_quote_id', '=', 'truckhaul_buyer_quotes.id')
                            ->leftjoin('lkp_cities as c1', 'truckhaul_buyer_quote_items.from_city_id', '=', 'c1.id')
                            ->leftjoin('lkp_cities as c2', 'truckhaul_buyer_quote_items.to_city_id', '=', 'c2.id')
                            ->leftjoin('lkp_vehicle_types as vt', 'truckhaul_buyer_quote_items.lkp_vehicle_type_id', '=', 'vt.id')
                            ->where('truckhaul_buyer_quotes.id', $mainid)
                            ->where('truckhaul_buyer_quote_items.id', $itemid)
                            ->select('truckhaul_buyer_quote_items.*', 'vt.vehicle_type as vehicle_type', 'c1.city_name as from_locationcity', 'c2.city_name as to_locationcity')
                            ->get();
                    $buyer_post_edit_seller = DB::table('truckhaul_buyer_quotes')
                            ->leftjoin('truckhaul_buyer_quote_selected_sellers as seller', 'seller.buyer_quote_id', '=', 'truckhaul_buyer_quotes.id')
                            ->leftjoin('users as u', 'u.id', '=', 'seller.seller_id')
                            ->where('truckhaul_buyer_quotes.id', $mainid)
                            ->select('seller.seller_id', 'u.username', 'u.id')
                            ->get();
                    $buyer_post_id = DB::table('truckhaul_buyer_quotes')
                            ->where('truckhaul_buyer_quotes.id', $mainid)
                            ->select('truckhaul_buyer_quotes.id','truckhaul_buyer_quotes.lkp_service_id', 'truckhaul_buyer_quotes.transaction_id')
                            ->first();
                                 
                    $buyer_quote_lineitem_id = $mainid;
                    
                    
                    return view('buyers.editbuyer', array('buyer_post_edit' => $buyer_post_edit_action, $buyer_quote_lineitem_id, 'buyer_post_edit_seller' => $buyer_post_edit_seller, 'buyer_post_id' => $buyer_post_id));

                    break;
                    
                    
                  case ROAD_TRUCK_LEASE:
                    	$buyer_post_edit_action = DB::table('trucklease_buyer_quotes')
                    	->leftjoin('trucklease_buyer_quote_items', 'trucklease_buyer_quote_items.buyer_quote_id', '=', 'trucklease_buyer_quotes.id')
                    	->leftjoin('lkp_cities as c1', 'trucklease_buyer_quote_items.from_city_id', '=', 'c1.id')
                    	->leftjoin('lkp_vehicle_types as vt', 'trucklease_buyer_quote_items.lkp_vehicle_type_id', '=', 'vt.id')
                    	->where('trucklease_buyer_quotes.id', $mainid)
                    	->where('trucklease_buyer_quote_items.id', $itemid)
                    	->select('trucklease_buyer_quote_items.*', 'vt.vehicle_type as vehicle_type', 'c1.city_name as from_locationcity')
                    	->get();
                    	$buyer_post_edit_seller = DB::table('trucklease_buyer_quotes')
                    	->leftjoin('trucklease_buyer_quote_selected_sellers as seller', 'seller.buyer_quote_id', '=', 'trucklease_buyer_quotes.id')
                    	->leftjoin('users as u', 'u.id', '=', 'seller.seller_id')
                    	->where('trucklease_buyer_quotes.id', $mainid)
                    	->select('seller.seller_id', 'u.username', 'u.id')
                    	->get();
                    	$buyer_post_id = DB::table('trucklease_buyer_quotes')
                    	->where('trucklease_buyer_quotes.id', $mainid)
                    	->select('trucklease_buyer_quotes.id','trucklease_buyer_quotes.lkp_service_id', 'trucklease_buyer_quotes.transaction_id')
                    	->first();
                    
                    	$buyer_quote_lineitem_id = $mainid;
                    
                    	return view('trucklease.buyers.editbuyer', array('buyer_post_edit' => $buyer_post_edit_action, $buyer_quote_lineitem_id, 'buyer_post_edit_seller' => $buyer_post_edit_seller, 'buyer_post_id' => $buyer_post_id));
                    
                    	break;
                
            }
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

            $serviceId = Session::get('service_id');

            $selle_old_implode = rtrim(implode(',', $_REQUEST['seller_list']), ',');
            $seller_list = explode(",", $selle_old_implode);
            $selle_old_implode = array_filter($seller_list);
            $seller_list_count = count($selle_old_implode);
            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER ['REMOTE_ADDR'];

            switch($serviceId){
                case ROAD_FTL:
                        $rand_id = rand();
                        $str1 = "FTL_";
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

                        //return redirect('editbuyerquote/'.$request->buyer_id.'/'.$request->buyer_items_id)->with('sumsg', 'Buyer Quote Updated Successfully.');
                        return redirect('buyerposts')->with('sumsg', 'Post updated successfully.');
                    break;
                    
                    
                case ROAD_TRUCK_LEASE:
                    	$rand_id = rand();
                    	$str1 = "TRUCKLEASE_";
                    	$trans_randid = $str1 . $rand_id;
                    
                    	if ($seller_list_count != 0) {
                    		for ($i = 0; $i < $seller_list_count; $i ++) {
                    			$Quote_seller_list = new TruckleaseBuyerQuoteSelectedSeller();
                    			$Quote_seller_list->buyer_quote_id = $request->buyer_id;
                    			$Quote_seller_list->seller_id = $seller_list[$i];
                    			$Quote_seller_list->updated_by = Auth::id();
                    			$Quote_seller_list->updated_at = $created_at;
                    			$Quote_seller_list->updated_ip = $createdIp;
                    			$Quote_seller_list->save();
                    
                    			$Quote_quote_prices_list = new TruckleaseBuyerQuoteSellersQuotesPrice();
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
                    			CommonComponent::send_email(TRUCKLEASE_BUYER_CREATED_POST_FOR_SELLERS, $buyers_selected_sellers_email);
                    
                    			//Maintaining a log of data for buyer update quote in quote log table
                    			CommonComponent::auditLog($Quote_seller_list->id, 'trucklease_buyer_quote_selected_sellers');
                    		}
                    	} else {
                    		return redirect('editbuyerquote/' . $request->buyer_id . '/' . $request->buyer_items_id)->with('sumsg1', 'Buyer Quote Updated Failed.');
                    	}
                    	//return redirect('editbuyerquote/'.$request->buyer_id.'/'.$request->buyer_items_id)->with('sumsg', 'Buyer Quote Updated Successfully.');
                    	return redirect('buyerposts')->with('sumsg', 'Post updated successfully.');
                    	break;
                    	
                case ROAD_TRUCK_HAUL:
                        $rand_id = rand();
                        $str1 = "TRUCKHAUL_";
                        $trans_randid = $str1 . $rand_id;

                        if ($seller_list_count != 0) {
                            for ($i = 0; $i < $seller_list_count; $i ++) {
                                $Quote_seller_list = new TruckhaulBuyerQuoteSelectedSeller();
                                $Quote_seller_list->buyer_quote_id = $request->buyer_id;
                                $Quote_seller_list->seller_id = $seller_list[$i];
                                $Quote_seller_list->updated_by = Auth::id();
                                $Quote_seller_list->updated_at = $created_at;
                                $Quote_seller_list->updated_ip = $createdIp;
                                $Quote_seller_list->save();
                                
                                $Quote_quote_prices_list = new TruckhaulBuyerQuoteSellersQuotesPrice();
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
                                CommonComponent::send_email(TRUCKHAUL_BUYER_CREATED_POST_FOR_SELLERS, $buyers_selected_sellers_email);
                                
                                //Maintaining a log of data for buyer update quote in quote log table
                                CommonComponent::auditLog($Quote_seller_list->id, 'truckhaul_buyer_quote_selected_sellers');
                            }
                        } else {
                            return redirect('editbuyerquote/' . $request->buyer_id . '/' . $request->buyer_items_id)->with('sumsg1', 'Buyer Quote Updated Failed.');
                        }
                        //return redirect('editbuyerquote/'.$request->buyer_id.'/'.$request->buyer_items_id)->with('sumsg', 'Buyer Quote Updated Successfully.');
                        return redirect('buyerposts')->with('sumsg', 'Post updated successfully.');
                    break;
            }
            
            
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

 

    /*     * ***** Below Script for get No of loads  from vehicle type************** */

    public function getNoofLoads() {
        try {
            Log::info('Get no of loads depends on vehicle type and quantity: ' . Auth::id(), array('c' => '1'));
            $roleId = Auth::User()->lkp_role_id;
            //Saving user actitvity from buyer count no of loads
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_COUNT_NOOFLOADS", BUYER_COUNT_NOOFLOADS, 0, HTTP_REFERRER, CURRENT_URL);
            }

            $vehicle_type = $_GET['vehicle_type'];
            $vehicle_types = DB::table('lkp_vehicle_types')->select('capacity','units', 'dimension')->where('id', $vehicle_type)->get();
            $vehcile_type = $vehicle_types[0]->capacity;
            $quantity = $_GET['quantity'];  
            if($vehicle_types[0]->units=="KG")
                $quantity = $quantity*1000;
            $noofloads = ceil($quantity / $vehcile_type);           
            echo $vehicle_types[0]->dimension . "-" . $noofloads;
            die();
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /*     * ***** Below Script for get capacity(MT)  from Loads type************** */

    public function getCapacity() {
        try {
            Log::info('Get capacity depends on load type: ' . Auth::id(), array('c' => '1'));
            $roleId = Auth::User()->lkp_role_id;
            //Update the user activity to the buyer get capacity
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_CAPACITY", BUYER_CAPACITY, 0, HTTP_REFERRER, CURRENT_URL);
            }

            $load_type = $_REQUEST['load_type'];
            $load_types = DB::table('lkp_load_types')->select('ftl_measurement')->where('id', $load_type)->get();
            echo $load_types[0]->ftl_measurement;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    public function autocomplete() {
        try {
            Log::info('Seller Auto complete cities: ' . Auth::id(), array('c' => '1'));
            $term = Input::get('term');
            $fromlocation_loc = Input::get('fromlocation');
            $results = array();
            if (isset($fromlocation_loc)) {
                $queries = DB::table('lkp_cities')->orderBy ( 'city_name', 'asc' )
                                ->where('city_name', 'LIKE', $term . '%')
                                ->where('city_name', '<>', $fromlocation_loc)
                                ->take(10)->get();
            } else {
                $queries = DB::table('lkp_cities')->orderBy ( 'city_name', 'asc' )
                                ->where('city_name', 'LIKE', $term . '%')
                                ->take(10)->get();
            }
            foreach ($queries as $query) {
                $results[] = ['id' => $query->id, 'value' => $query->city_name];
            }
            return Response::json($results);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /******* Below Script for get seller list from city************** */
    public static function getSellerslist($cities = array()) {
        $results = array();
        $serviceId = Session::get('service_id');
        try {
            Log::info('Get Seller lsit from depends on from city: ' . Auth::id(), array('c' => '1'));
            $roleId = Auth::User()->lkp_role_id;
            //Update the user activity to the buyer get seller list
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_SELLERLIST", BUYER_SELLERLIST, 0, HTTP_REFERRER, CURRENT_URL);
            }
           
            if(isset($_POST['seller_list'])) {
                 $sellerlist = (count($cities) > 0) ? $cities : $_POST['seller_list'];
            }
           $district_array = array();
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
            switch($serviceId){
            	case ROAD_FTL:
            		$seller_data = DB::table('seller_post_items')
            		->leftjoin('users', 'seller_post_items.created_by', '=', 'users.id')            		
            		->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
            		->distinct('seller_post_items.created_by')
            		->whereIn('seller_post_items.lkp_district_id', $district_array)
            		->whereRaw("(users.id != ". Auth::User()->id .")")
                    ->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")->orderBy ( 'users.username', 'asc' )
            		->select('users.id', 'users.username', 'seller_details.principal_place', 'seller_details.name', 'seller_details.contact_firstname')
            		->get();            		
            		break;
                case ROAD_TRUCK_HAUL:
                    $seller_data = DB::table('truckhaul_seller_post_items')
                    ->leftjoin('users', 'truckhaul_seller_post_items.created_by', '=', 'users.id')                    
                    ->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
                    ->distinct('truckhaul_seller_post_items.created_by')
                    ->whereIn('truckhaul_seller_post_items.lkp_district_id', $district_array)
                    ->whereRaw("(users.id != ". Auth::User()->id .")")
                    ->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")->orderBy ( 'users.username', 'asc' )
                    ->select('users.id', 'users.username', 'seller_details.principal_place', 'seller_details.name', 'seller_details.contact_firstname')
                    ->get();                    
                    break;
                 case ROAD_TRUCK_LEASE:
                    	$seller_data = DB::table('trucklease_seller_post_items')
                    	->leftjoin('users', 'trucklease_seller_post_items.created_by', '=', 'users.id')
                    	->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
                    	->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
                    	->distinct('trucklease_seller_post_items.created_by')
                    	->whereIn('trucklease_seller_post_items.lkp_district_id', $district_array)
                    	->whereRaw("(users.id != ". Auth::User()->id .")")
                    	->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")->orderBy ( 'users.username', 'asc' )
                    	->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
                    	->get();
                    	break;
            	case RELOCATION_DOMESTIC:
            		$seller_data = DB::table('relocation_seller_post_items')
            		->leftjoin('users', 'relocation_seller_post_items.created_by', '=', 'users.id')
            		->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
            		->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
            		->distinct('relocation_seller_post_items.created_by')
            		->whereRaw("(users.id != ". Auth::User()->id .")")
                    ->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")->orderBy ( 'users.username', 'asc' )
            		->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
            		->get();
            		break;

            	case RELOCATION_PET_MOVE:
                    $seller_data = DB::table('relocationpet_seller_post_items')
                        ->leftjoin('users', 'relocationpet_seller_post_items.created_by', '=', 'users.id')
                        ->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
                        ->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
                        ->distinct('relocationpet_seller_post_items.created_by')
                        ->whereRaw("(users.id != ". Auth::User()->id .")")
                        ->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")->orderBy ( 'users.username', 'asc' )
                        ->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
                        ->get();
                    break;

                case RELOCATION_OFFICE_MOVE:               
                    $seller_data = DB::table('relocationoffice_seller_posts')
                    ->leftjoin('users', 'relocationoffice_seller_posts.created_by', '=', 'users.id')
                    ->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
                    ->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
                    ->distinct('relocationoffice_seller_posts.created_by')
                    ->whereRaw("(users.id != ". Auth::User()->id .")")
                    ->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")->orderBy ( 'users.username', 'asc' )
                    ->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
                    ->get();
                    break;
            	case RELOCATION_INTERNATIONAL:
                    $seller_data = DB::table('relocationint_seller_posts as sp')
                        ->leftjoin('users', 'sp.created_by', '=', 'users.id')
                        ->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
                        ->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
                        ->distinct('sp.created_by')
                        ->whereRaw("(users.id != ". Auth::User()->id .")")
                        ->whereRaw("(sp.lkp_international_type_id = ". $_POST['post_type'] .")")
                        ->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")->orderBy ( 'users.username', 'asc' )
                        ->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
                        ->get();
                    break;
                case RELOCATION_GLOBAL_MOBILITY:                    
                    $seller_data = DB::table('relocationgm_seller_posts')
                        ->leftjoin('users', 'relocationgm_seller_posts.created_by', '=', 'users.id')
                        ->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
                        ->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
                        ->distinct('relocationgm_seller_posts.created_by')
                        ->whereRaw("(users.id != ". Auth::User()->id .")")
                        ->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")->orderBy ( 'users.username', 'asc' )
                        ->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
                        ->get();
                    break;
            }
            
          
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

    /**
     * Get Post Buyer Counter Offer Page
     * Get details of buyer counter offer 
     * @param int $buyerQuoteItemId
     * @return type
     */
    public function getPostBuyerCounterOffer($buyerQuoteItemId, $comparisonType = null,$priceVal= null,$checkIds=null) {
        Log::info('Get posted buyer counter offer: ' . Auth::id(), array('c' => '1'));
        try {
            $roleId = Auth::User()->lkp_role_id;
            $serviceId = Session::get('service_id');

            
			//Loading respective service data grid
			switch($serviceId){
                case ROAD_FTL       : 

                    $allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId);
                    $buyerOfferDetails = FtlBuyerComponent::getPostBuyerCounterOfferForFtl($buyerQuoteItemId, $comparisonType,$priceVal,$checkIds);

                    $arrayLeadsData = FtlBuyerComponent::getSellerLeadsData($serviceId, $buyerQuoteItemId);//Leads query
                    return view('buyers.buyerpostcounteroffer',
                            [
                                'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                                'fromLocation' => $buyerOfferDetails['fromLocation'],
                                'toLocation' => $buyerOfferDetails['toLocation'],
                                'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                                'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                                'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                                'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
                                'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
                                'destinationLocation' =>  $buyerOfferDetails['destinationLocation'],
                                'packagingType' =>  $buyerOfferDetails['packagingType'],
                                'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                                'countview' =>  $buyerOfferDetails['countview'],
                                'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                                'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                            	'sellerDetailsLeads' =>  $arrayLeadsData,
                            	'allMessagesList' =>  $allMessagesList,
                            ]
                    );
                    break;
                    
                    
                    case ROAD_TRUCK_LEASE      :
                    
                    	$allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId);
                    	$buyerOfferDetails = TruckLeaseBuyerComponent::getPostBuyerCounterOfferForTL($buyerQuoteItemId, $comparisonType,$priceVal,$checkIds);
                    	
                    	$arrayLeadsData = TruckLeaseBuyerComponent::getSellerLeadsData($serviceId, $buyerQuoteItemId);//Leads query
                    	return view('trucklease.buyers.buyerpostcounteroffer',
                    			[
                    			'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                    			'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                    			'fromLocation' => $buyerOfferDetails['fromLocation'],
                    			'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                    			'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                    			'poststatus' => $buyerOfferDetails['poststatus'],
                    			'leaseterm' => $buyerOfferDetails['leaseterm'],
                    			'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                    			'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
                    			'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
                    			'driver_availability'=>$buyerOfferDetails['driver_availability'],
                    			'fuel'=>$buyerOfferDetails['fuel_included'],
                    			'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                    			'countview' =>  $buyerOfferDetails['countview'],
                    			'price' =>  $buyerOfferDetails['price'],
                    			'vehicle_make_model_year'=>  $buyerOfferDetails['vehicle_make_model_year'],
                    			'lkp_quote_price_type_id'=>  $buyerOfferDetails['lkp_quote_price_type_id'],
                    			'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                    			'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                    			'sellerDetailsLeads' =>  $arrayLeadsData,
                    			'allMessagesList' =>  $allMessagesList,
                    			]
                    	);
                    	break;
                    
                    
                    
                    case ROAD_PTL       : 
                    case RAIL :
                    case AIR_DOMESTIC :
                    case AIR_INTERNATIONAL :  
                    case OCEAN : 
                    $allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId);
                    $buyerOfferDetails = PtlBuyerComponent::getPostBuyerCounterOfferForPtl($buyerQuoteItemId, $comparisonType,$priceVal,$checkIds,$serviceId, $roleId);
                    $arrayLeadsData = FtlBuyerComponent::getSellerLeadsData($serviceId, $buyerQuoteItemId);//Leads query
                    if(!empty($arrayLeadsData) && !empty($arrayLeadsData['arraySellerLeadsData'])) {
                    	$fromLocationId = $arrayLeadsData['arraySellerLeadsData']->from_location_id;
                    } else {
                    	$fromLocationId = "";
                    }        
                         
                    return view('ptl.buyers.buyer_get_quote_booknow',
                            [
                                'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                                'fromLocation' => $buyerOfferDetails['fromLocation'],
                                'toLocation' => $buyerOfferDetails['toLocation'],
                                'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                                'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                                'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                                'arraySellerDetails' => $buyerOfferDetails['arraySellerDetails'],
                                'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                                'countview' =>  $buyerOfferDetails['countview'],
                            	'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                                'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                            	'sellerDetailsLeads' =>  $arrayLeadsData,
                                'allMessagesList' =>  $allMessagesList,
                            	
                            ]
                    );
                    break;
                    case COURIER :
                    	$allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId);                       
                    	$PostDeliveryType = PtlBuyerComponent::getPostDeliveryType($buyerQuoteItemId,$serviceId);
                    	$PostCourierType = PtlBuyerComponent::getPostCourierType($buyerQuoteItemId,$serviceId);
                        $buyerOfferDetails = PtlBuyerComponent::getPostBuyerCounterOfferForPtl($buyerQuoteItemId, $comparisonType,$priceVal,$checkIds,$serviceId, $roleId);
                    	$arrayLeadsData = FtlBuyerComponent::getSellerLeadsData($serviceId, $buyerQuoteItemId,$PostCourierType);//Leads query                    	
                       
                    	if($PostDeliveryType == 'International'){
                    	$toloaction_id_courier = $buyerOfferDetails['arrayBuyerCounterOffer'][0]->to_location_id;
                    	$to_contry = DB::table ( 'lkp_countries' )->where ( 'id', $toloaction_id_courier )->pluck ( 'country_name' );
                    	$buyerOfferDetails['toLocation']= $to_contry;                    	
                    	}
                    	if(!empty($arrayLeadsData) && !empty($arrayLeadsData['arraySellerLeadsData'])) {
                    		$fromLocationId = $arrayLeadsData['arraySellerLeadsData']->from_location_id;
                    	} else {
                    		$fromLocationId = "";
                    	}
             
                    	return view('ptl.buyers.buyer_get_quote_booknow',
                    			[
                    			'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                    			'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                    			'fromLocation' => $buyerOfferDetails['fromLocation'],
                    			'toLocation' => $buyerOfferDetails['toLocation'],
                    			'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                    			'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                    			'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                    			'arraySellerDetails' => $buyerOfferDetails['arraySellerDetails'],
                    			'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                    			'countview' =>  $buyerOfferDetails['countview'],
                    			'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                    			'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                    			'sellerDetailsLeads' =>  $arrayLeadsData,
                    			'allMessagesList' =>  $allMessagesList,
                    			'PostDeliveryType' =>  $PostDeliveryType,
                    			'PostCourierType' =>  $PostCourierType,
                    			]
                    	);
                    	break;
                case ROAD_INTRACITY :
                    
                    CommonComponent::activityLog("INTRA_BUYER_POST_DETAILS", INTRA_BUYER_POST_DETAILS, 0, HTTP_REFERRER, CURRENT_URL);
                    $postId = $buyerQuoteItemId;
                    $result = IntracityBuyerComponent::getBuyerPostDetails($postId, $serviceId, $roleId);
                    $packagingType = BuyerComponent::getPackagingType('Destination');
                    $postDetails = $result ['postDetails'];
                    $sellerQuotes = $result ['sellerQuotes'];
                    
                    $flag=0;
                    foreach($sellerQuotes as $sellerQuote){
                        if(isset($sellerQuote->order_id))
                            $flag=1;
                    }
                    $quotesCount = $result ['quotesCount'];
                    return view('intracity.buyers.post_details', array(
                        'postDetails' => $postDetails,
                        'buyerQuoteId' => $postId,
                        'packagingType' => $packagingType,
                        'sellerQuotes' => $sellerQuotes,
                        'quotesCount' => $quotesCount,
                        'flag' => $flag
                            ));

                    break;
                
                    case RELOCATION_DOMESTIC :
                    
                    	CommonComponent::activityLog("RELOCATION_BUYER_POST_DETAILS", RELOCATION_BUYER_POST_DETAILS, 0, HTTP_REFERRER, CURRENT_URL);
                    	$postId = $buyerQuoteItemId;
                 
                        $allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId);
                    	$result = RelocationBuyerComponent::getBuyerPostDetails($postId, $serviceId, $roleId,$comparisonType,$priceVal);
                 
                    	return view('relocation.buyers.buyer_post_details',[
									'buyer_post_details' => $result ['postDetails'],
                    				'buyer_post_inventory_details' => $result ['inventoryDetails'],
                    				'seller_quote_details' => $result ['sellerResults'],
                    				'compareid' => $comparisonType,
                                    'allMessagesList' =>  $allMessagesList
                    	]);
                    
                    	break;
                    case ROAD_TRUCK_HAUL       : 

                    $allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId);
                    $buyerOfferDetails = TruckHaulBuyerComponent::getPostBuyerCounterOfferForTH($buyerQuoteItemId, $comparisonType,$priceVal,$checkIds);

                    $arrayLeadsData = TruckHaulBuyerComponent::getSellerLeadsData($serviceId, $buyerQuoteItemId);//Leads query
                    return view('truckhaul.buyers.buyerpostcounteroffer',
                            [
                                'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                                'fromLocation' => $buyerOfferDetails['fromLocation'],
                                'toLocation' => $buyerOfferDetails['toLocation'],
                       
                                'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                                'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                                'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
                                'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
                                'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                                'countview' =>  $buyerOfferDetails['countview'],
                                'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                                'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                            	'sellerDetailsLeads' =>  $arrayLeadsData,
                            	'allMessagesList' =>  $allMessagesList,
                            ]
                    );
                    break;
                    
                    case RELOCATION_OFFICE_MOVE :
                    
                    	CommonComponent::activityLog("RELOCATION_OFFICEMOVE_BUYER_POST_DETAILS", RELOCATION_OFFICEMOVE_BUYER_POST_DETAILS, 0, HTTP_REFERRER, CURRENT_URL);
                    	$postId = $buyerQuoteItemId;
                    	
                    	$allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId);
                    	$result = RelocationOfficeBuyerComponent::getBuyerPostDetails($postId, $serviceId, $roleId,$comparisonType,$priceVal);
                    	
                    	return view('relocationoffice.buyers.buyer_post_details',[
                    			'buyer_post_details' => $result ['postDetails'],
                    			'buyer_post_inventory_details' => $result ['inventoryDetails'],
                    			'seller_quote_details' => $result ['sellerResults'],
                    			'compareid' => $comparisonType,
                    			'allMessagesList' =>  $allMessagesList
                    	]);
                    
                    	break;

                        // Relocation Pet post details page
                        case RELOCATION_PET_MOVE:
                    
                        CommonComponent::activityLog("RELOCATION_BUYER_POST_DETAILS", RELOCATION_BUYER_POST_DETAILS, 0, HTTP_REFERRER, CURRENT_URL);
                        
                        $allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId);
                        
                        $result = \App\Components\RelocationPet\RelocationPetBuyerComponent::getBuyerPostDetails($buyerQuoteItemId, $serviceId, $roleId,$comparisonType,$priceVal);
                        
                        return view('relocationpet.buyers.buyer_post_details',[
                                'buyer_post_details' => $result ['postDetails'],
                                'seller_quote_details' => $result ['sellerResults'],
                                'compareid' => $comparisonType,
                                'allMessagesList' => $allMessagesList
                        ]);
                        break;

                    case RELOCATION_INTERNATIONAL :
                    
                        CommonComponent::activityLog("RELOCATION_INTERNATIONAL_BUYER_POST_DETAILS", RELOCATION_INTERNATIONAL_BUYER_POST_DETAILS, 0, HTTP_REFERRER, CURRENT_URL);
                        $postId = $buyerQuoteItemId;
                 
                        $allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId);
                        $result = RelocationIntBuyerComponent::getBuyerPostDetails($postId, $serviceId, $roleId,$comparisonType,$priceVal);
                 
                        return view('relocationint.buyers.buyer_post_details',[
                                    'buyer_post_details' => $result ['postDetails'],
                                    'buyer_post_inventory_details' => $result ['inventoryDetails'],
                                    'seller_quote_details' => $result ['sellerResults'],
                                    'compareid' => $comparisonType,
                                    'allMessagesList' =>  $allMessagesList
                        ]);
                    
                        break;
                    
                    case RELOCATION_GLOBAL_MOBILITY :
                    
                        CommonComponent::activityLog("RELOCATION_GM_BUYER_POST_DETAILS", RELOCATION_GM_BUYER_POST_DETAILS, 0, HTTP_REFERRER, CURRENT_URL);
                        $postId = $buyerQuoteItemId;
                 
                        $allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId);
                        $result = RelocationGlobalBuyerComponent::getBuyerPostDetails($postId, $serviceId, $roleId,$comparisonType,$priceVal);
                 /*echo "<pre>";
                 print_r($result ['quoteItemsDetails']); exit;*/
                        return view('relocationglobal.buyers.buyer_post_details',[
                                    'buyer_post_details' => $result ['postDetails'],
                                    'buyer_post_quoteitems_details' => $result ['quoteItemsDetails'],
                                    'seller_quote_details' => $result ['sellerResults'],
                                    'compareid' => $comparisonType,
                                    'allMessagesList' =>  $allMessagesList
                        ]);
                    
                        break;

                    
                default :
                    $buyerOfferDetails = FtlBuyerComponent::getPostBuyerCounterOfferForFtl($buyerQuoteItemId, $comparisonType, $serviceId, $roleId);
                    return view('buyers.buyerpostcounteroffer', [
                        'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                        'fromLocation' => $buyerOfferDetails['fromLocation'],
                        'toLocation' => $buyerOfferDetails['toLocation'],
                        'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                        'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                        'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                        'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
                        'sourceLocation' => $buyerOfferDetails['sourceLocation'],
                        'destinationLocation' => $buyerOfferDetails['destinationLocation'],
                        'packagingType' => $buyerOfferDetails['packagingType'],
                        'countCartItems' => $buyerOfferDetails['countCartItems'],
                        'countview' => $buyerOfferDetails['countview'],
                        'buyerPostCounterOfferComparisonTypes' => $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                        'comparisonType' => $buyerOfferDetails['comparisonType'],
                            ]
                    );
                    break;
            }
            //rendering the view with the data grid
        } catch (Exception $e) {
            
        }
    }

    /**
     * Get Post Buyer Counter Offer Page
     * Inserts counter offer price
     * @param Request $request
     * @return type
     */
    public function setPostBuyerCounterOffer() {
        Log::info('Get posted buyer counter offer: ' . Auth::id(), array('c' => '1'));
        try {
            
            $serviceId = Session::get('service_id');
            //Saving the user activity to the log table
            if ($serviceId == ROAD_FTL) {
                CommonComponent::activityLog("BUYER_INSERTED_COUNTER_OFFER", BUYER_INSERTED_COUNTER_OFFER, 0, HTTP_REFERRER, CURRENT_URL);
            }

            $input = Input::all();
            //Loading respective service data grid
            switch ($serviceId) {
                case ROAD_FTL :
                    FtlBuyerComponent::setPostBuyerCounterOfferForFtl($input);
                    break;
                case ROAD_TRUCK_LEASE:
                	FtlBuyerComponent::setPostBuyerCounterOfferForTL($input);
                	break;
                case ROAD_PTL : 
                case RAIL : 
                case AIR_DOMESTIC :     
                case AIR_INTERNATIONAL :
                case OCEAN :
                case COURIER :
                    PtlBuyerComponent::setPostBuyerCounterOfferForPtl($input);
                    break;
                case ROAD_TRUCK_HAUL       : 
                    TruckHaulBuyerComponent::setPostBuyerCounterOfferForTH($input);
                    Break;
                default :
                    FtlBuyerComponent::setPostBuyerCounterOfferForFtl($input);
                    break;
            }
        } catch (Exception $e) {
            
        }
    }

    /**
     * Get Post Buyer Counter Offer Page
     * Inserts counter offer price
     * @param Request $request
     * @return type
     */
    public function getFreightDetailsForPtl() {
        Log::info('Get posted buyer counter offer: ' . Auth::id(), array('c' => '1'));
        try {
         
            $serviceId = Session::get('service_id');

            if (empty($serviceId) ) {
                return;
            }

            //Saving the user activity to the log table
            CommonComponent::activityLog("BUYER_CALCULATED_FREIGHT_AMOUNT", BUYER_CALCULATED_FREIGHT_AMOUNT, 0, HTTP_REFERRER, CURRENT_URL);

            $input = Input::all();
            //Loading respective service data grid
            $freightDetails = PtlBuyerComponent::getFreightDetailsForPtl($input);
            return [
                    'success' => 1,
                    'freightDetails' => [
                                            'oda' => $freightDetails['oda'],
                                            'pickUpPrice' => $freightDetails['pickUpPrice'],
                                            'deliveryPrice' => $freightDetails['deliveryPrice'],
                                            'counterRatePerKg' => $freightDetails['counterRatePerKg'],
                                            'totalFreightAmount' => $freightDetails['totalFreightAmount'],
                                            'totalAmount' => $freightDetails['totalAmount'],
                                            'formattedOda' => $freightDetails['formattedOda'],
                                            'formattedPickUpPrice' => $freightDetails['formattedPickUpPrice'],
                                            'formattedTotalAmount' => $freightDetails['formattedTotalAmount'],
                                            'formattedDeliveryPrice' => $freightDetails['formattedDeliveryPrice'],
                                            'formattedCounterRatePerKg' => $freightDetails['formattedCounterRatePerKg'],
                                            'formattedTotalFreightAmount' => $freightDetails['formattedTotalFreightAmount'],
                                        ]
                    ];
        } catch (Exception $e) {
            
        }
    }
    
    /**
     * Get Post Seller  Offer Page
     * Inserts counter offer price
     * @param Request $request
     * @return type
     */
    public function getSellerFreightDetailsForPtl() {
    	Log::info('Get posted Seller offer: ' . Auth::id(), array('c' => '1'));
    	try {
    		
    		$serviceId = Session::get('service_id');
    
    		if (empty($serviceId) ) {
    			return;
    		}
    
    		//Saving the user activity to the log table
    		//CommonComponent::activityLog("BUYER_CALCULATED_FREIGHT_AMOUNT", BUYER_CALCULATED_FREIGHT_AMOUNT, 0, HTTP_REFERRER, CURRENT_URL);
    
    		$input = Input::all();
    		//Loading respective service data grid
    		$freightDetails = PtlBuyerComponent::getSellerFreightDetailsForPtl($input);
    		return [
    		'success' => 1,
    		'freightDetails' => [
    		'oda' => $freightDetails['oda'],
    		'pickUpPrice' => $freightDetails['pickUpPrice'],
    		'deliveryPrice' => $freightDetails['deliveryPrice'],
    		'counterRatePerKg' => $freightDetails['counterRatePerKg'],
    		'totalFreightAmount' => $freightDetails['totalFreightAmount'],
    		'totalAmount' => $freightDetails['totalAmount'],
    		'formattedOda' => $freightDetails['formattedOda'],
    		'formattedPickUpPrice' => $freightDetails['formattedPickUpPrice'],
    		'formattedTotalAmount' => $freightDetails['formattedTotalAmount'],
    		'formattedDeliveryPrice' => $freightDetails['formattedDeliveryPrice'],
    		'formattedCounterRatePerKg' => $freightDetails['formattedCounterRatePerKg'],
    		'formattedTotalFreightAmount' => $freightDetails['formattedTotalFreightAmount'],
    		]
    		];
    	} catch (Exception $e) {
    
    	}
    }
    
    
    
    public function fillEditPtlSector(){
    	Log::info('Get posted Seller offer: ' . Auth::id(), array('c' => '1'));
    	try {
    		
    		$serviceId = Session::get('service_id');
    
    		if (empty($serviceId) ) {
    			return;
    		}
    
    		//Saving the user activity to the log table
    		//CommonComponent::activityLog("BUYER_CALCULATED_FREIGHT_AMOUNT", BUYER_CALCULATED_FREIGHT_AMOUNT, 0, HTTP_REFERRER, CURRENT_URL);
    
    		$input = Input::all();
    		//Loading respective service data grid
    		$freightDetails = PtlBuyerComponent::getFreightDetailsForPtl($input);
    		return [
    		'success' => 1,
    		'freightDetails' => [
    		'oda' => $freightDetails['oda'],
    		'pickUpPrice' => $freightDetails['pickUpPrice'],
    		'deliveryPrice' => $freightDetails['deliveryPrice'],
    		'counterRatePerKg' => $freightDetails['counterRatePerKg'],
    		'totalFreightAmount' => $freightDetails['totalFreightAmount'],
    		'totalAmount' => $freightDetails['totalAmount'],
    		'formattedOda' => $freightDetails['formattedOda'],
    		'formattedPickUpPrice' => $freightDetails['formattedPickUpPrice'],
    		'formattedTotalAmount' => $freightDetails['formattedTotalAmount'],
    		'formattedDeliveryPrice' => $freightDetails['formattedDeliveryPrice'],
    		'formattedCounterRatePerKg' => $freightDetails['formattedCounterRatePerKg'],
    		'formattedTotalFreightAmount' => $freightDetails['formattedTotalFreightAmount'],
    		]
    		];
    	} catch (Exception $e) {
    
    	}
    
    }	

    /**
     * get buyer counter offer page
     * Insert values for booknow
     * @param Request $request
     * @return type
     */
    public function setBuyerBooknow() 
    {
        Log::info('Insert the buyer booknow data: ' . Auth::id(), array('c' => '1'));
        try {
            $roleId = Auth::User()->lkp_role_id;
            $serviceId = Session::get('service_id');
            //Saving the user activity to the log table
            $input = Input::all();
            
           if(isset($input['quoteItemId'])){
                //BuyerMatchingComponent::removeFromMatching($serviceId,$input['quoteItemId']);
            }

            switch ($serviceId) {
                case ROAD_FTL :
                case RELOCATION_DOMESTIC :
                case RELOCATION_INTERNATIONAL :    
                case RELOCATION_OFFICE_MOVE :  
                case RELOCATION_PET_MOVE :    
                    $buyerBooknowForFtl = FtlBuyerComponent::setBuyerBooknowForFtl($input);
                    return $buyerBooknowForFtl;
                    break;
                case ROAD_PTL           : 
                case RAIL               : 
                case AIR_DOMESTIC       : 
                case AIR_INTERNATIONAL  : 
                case OCEAN              : 
                case COURIER:
                    //return array('value' => $roleId,'input' => $input);
                    $buyerBooknowForFtl = PtlBuyerComponent::setBuyerBooknow($input);
                    return $buyerBooknowForFtl;
                    break;               
                case ROAD_INTRACITY :
                    $buyerBooknowForFtl = IntracityBuyerComponent::setBuyerBooknowForFtl($input);
                    return $buyerBooknowForFtl;
                    break;
                case ROAD_TRUCK_HAUL:
                    $buyerBooknowForFtl = TruckHaulBuyerComponent::setBuyerBooknowForTH($input);
                    return $buyerBooknowForFtl;
                    break;
                case ROAD_TRUCK_LEASE:
                    $buyerBooknowForFtl = TruckLeaseBuyerComponent::setBuyerBooknowForTl($input);
                    return $buyerBooknowForFtl;
                    break;
                default :
                    $buyerBooknowForFtl = FtlBuyerComponent::setBuyerBooknowForFtl($input);
                    return $buyerBooknowForFtl;
                    break;
            }
        } catch (Exception $e) {
            
        }
    }

    /**
     * get buyer counter offer page
     * Cancel enquiry
     * @param integer $buyerQuoteItemId
     * @return type
     */
    public function cancelEnquiry($buyerQuoteItemId) {
        Log::info('Cancel the quote enquiry: ' . Auth::id(), array('c' => '1'));
        try {
            $roleId = Auth::User()->lkp_role_id;
            $serviceId = Session::get('service_id');

           BuyerMatchingComponent::removeFromMatching($serviceId,$buyerQuoteItemId);

            //Loading respective service data grid
            switch ($serviceId) {
                case ROAD_FTL :
                    $cancelEnquiryMessage = FtlBuyerComponent::cancelEnquiryForFtl($buyerQuoteItemId);
                    return redirect('getbuyercounteroffer/' . $buyerQuoteItemId)
                                    ->with('cancelsuccessmessage', $cancelEnquiryMessage['cancelsuccessmessage']);
                    break;
                    case ROAD_PTL: 
                    case RAIL:
                    case AIR_DOMESTIC: 
                    case AIR_INTERNATIONAL: 
                    case OCEAN: 
                    $cancelEnquiryMessage = PtlBuyerComponent::cancelEnquiry($buyerQuoteItemId);
                    return redirect('getbuyercounteroffer/'.$buyerQuoteItemId)
                            ->with('cancelsuccessmessage', $cancelEnquiryMessage['cancelsuccessmessage']);
                    break;
                case ROAD_TRUCK_HAUL:
                    $cancelEnquiryMessage = TruckHaulBuyerComponent::cancelEnquiryForTH($buyerQuoteItemId);
                    return redirect('getbuyercounteroffer/' . $buyerQuoteItemId)
                            ->with('cancelsuccessmessage', $cancelEnquiryMessage['cancelsuccessmessage']);
                break;    
                default :
                    $cancelEnquiryMessage = FtlBuyerComponent::cancelEnquiryForFtl($buyerQuoteItemId);
                    return redirect('getbuyercounteroffer/' . $buyerQuoteItemId)
                                    ->with('cancelsuccessmessage', $cancelEnquiryMessage['cancelsuccessmessage']);
                    break;
            }
        } catch (Exception $e) {
            
        }
    }

    //this function for get boknow details in buyer search for sellers results.
    public function getbooknowdetails()
    {
    	try
    	{
    		$input = Input::All();
                $serviceId = Session::get('service_id');
    		$buyerQuoteId = $input['buyerBooknowId'];
    		if(isset($input['isPtl']) && !empty($input['isPtl'])){
               $isPtl = $input['isPtl'];
            } else {
               $isPtl = 0;
            }
    		Log::info('Get posted buyer counter offer: '.Auth::id(),array('c'=>'1'));
    		$roleId = Auth::User()->lkp_role_id;
    		if($roleId == BUYER){
    			CommonComponent::activityLog("BUYER_FETCHED_SELLER_POST",
    					BUYER_FETCHED_SELLER_POST,0,
    					HTTP_REFERRER,CURRENT_URL);
    		}
    		$sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
    		$destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
    		$packagingType = BuyerComponent::getPackagingType('Destination');
                switch ($serviceId) {
                case ROAD_TRUCK_HAUL:
                    return view('partials.buyer_truckhaul_booknow',
    				[
    				'buyerQuoteId' => $buyerQuoteId,
    				'sourceLocation' => $sourceLocationType,
    				
    				]
    		);
                    break;
                default :
    		return view('partials.buyer_booknow',
    				[
    				'buyerQuoteId' => $buyerQuoteId,
    				'sourceLocation' => $sourceLocationType,
    				'destinationLocation' => $destinationLocationType,
    				'packagingType' => $packagingType,
    				'isltl' => $isPtl,
    				]
    		);break;
                }
    	}
    	catch (Exception $e) {
    		 
    	}   
    
   }  
    

   //this function for Intracity get booknow details in buyer search for sellers results.
    public function getIntraBookNowDetails()
    {
        try
        {
            $input = Input::All();
            $buyerQuoteId = $input['buyerBooknowId'];
            Log::info('Get posted buyer counter offer: '.Auth::id(),array('c'=>'1'));
            $roleId = Auth::User()->lkp_role_id;
            if($roleId == BUYER){
                CommonComponent::activityLog("INTRA_BUYER_FETCHED_SELLER_POST",
                        INTRA_BUYER_FETCHED_SELLER_POST,0,
                        HTTP_REFERRER,CURRENT_URL);
            }
            $packagingType = BuyerComponent::getPackagingType('Destination');
            return view('intracity.partials.buyer_booknow',
                    [
                    'buyerQuoteId' => $buyerQuoteId,
                    'packagingType' => $packagingType
                    ]
            );
        }
        catch (Exception $e) {             
        }  
   }   
            
   
   /**
    * buyer posts cancel
    */
   public function buyerpostcancel(){
   
   	if(Session::get ( 'service_id' ) != ''){
   		$serviceId = Session::get ( 'service_id' );
   	}  
   	$updatedAt = date ( 'Y-m-d H:i:s' );
   	$updatedIp = $_SERVER ["REMOTE_ADDR"];   
   	 
    $postIds= $_POST['postIds'];  
    $post_type = Session::get ( 'post_type' );
   	BuyerMatchingComponent::removeFromMatching($serviceId,$postIds);
   	if ($post_type == 'term') {   		
   		$result = TermBuyerComponent::getTermPostCancel($serviceId, $post_type, $postIds );
   		return $result;
   		exit;
   	} else {   		
   	try {
   		switch($serviceId){
   			case ROAD_FTL       : 
   				//check condition for post status open or not.
   				$checkstatus = DB::table('buyer_quote_items')
   				->where('buyer_quote_items.id', $postIds)
   				->select('buyer_quote_items.lkp_post_status_id')
   				->get();
   				foreach ($checkstatus as $query) {
   					$results[] = $query->lkp_post_status_id;
   				}
   				
   				if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
   				{
   					BuyerQuoteItems::where ( "id", $postIds )->update ( array (
   							'lkp_post_status_id' => CANCELLED,
   							'is_cancelled' => '1',
   							'updated_at' => $updatedAt,
   							'updated_by' => Auth::User ()->id,
   							'updated_ip' => $updatedIp
   					));  
   					return "Post successfully deleted";
   				} else {
   						return "Please select open posts only";
   						return 0;
   					}  
   				break;
   			case ROAD_PTL       :
   				
   					//check condition for post status open or not.
   					$checkstatus = DB::table('ptl_buyer_quotes')
   					->where('ptl_buyer_quotes.id', $postIds)
   					->select('ptl_buyer_quotes.lkp_post_status_id')
   					->get();
   					foreach ($checkstatus as $query) {
   						$results[] = $query->lkp_post_status_id;
   					}
   					if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
   					{
   					PtlBuyerQuote::where ( "id", $postIds )->update ( array (   							
   							'is_cancelled' => '1',
   							'lkp_post_status_id' => CANCELLED,
   							'updated_at' => $updatedAt,
   							'updated_by' => Auth::User ()->id,
   							'updated_ip' => $updatedIp
   					)); 
   					PtlBuyerQuoteItem::where ( "buyer_quote_id", $postIds )->update ( array (
		   					'is_cancelled' => '1',
		   					'lkp_post_status_id' => CANCELLED,
		   					'updated_at' => $updatedAt,
		   					'updated_by' => Auth::User ()->id,
		   					'updated_ip' => $updatedIp
   					));
   					
   					return "Post successfully deleted";
   					} else {
   						return "Please select open posts only";
   						return 0;
   					}    						
   				
   				break;
                        case RAIL       :
   					//check condition for post status open or not.
   					$checkstatus = DB::table('rail_buyer_quotes as bq')
   					->where('bq.id', $postIds)
   					->select('bq.lkp_post_status_id')
   					->get();
   					foreach ($checkstatus as $query) {
   						$results[] = $query->lkp_post_status_id;
   					}
   					if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
   					{
   					RailBuyerQuote::where ( "id", $postIds )->update ( array (   							
   							'is_cancelled' => '1',
   							'lkp_post_status_id' => CANCELLED,
   							'updated_at' => $updatedAt,
   							'updated_by' => Auth::User ()->id,
   							'updated_ip' => $updatedIp
   					)); 
   					RailBuyerQuoteItem::where ( "buyer_quote_id", $postIds )->update ( array (
		   					'is_cancelled' => '1',
		   					'lkp_post_status_id' => CANCELLED,
		   					'updated_at' => $updatedAt,
		   					'updated_by' => Auth::User ()->id,
		   					'updated_ip' => $updatedIp
   					));
   					
   					return "Post successfully deleted";
   					} else {
   						return "Please select open posts only";
   						return 0;
   					}    						
   				
   				break;
                        case AIR_DOMESTIC       :
   					//check condition for post status open or not.
   					$checkstatus = DB::table('airdom_buyer_quotes as bq')
   					->where('bq.id', $postIds)
   					->select('bq.lkp_post_status_id')
   					->get();
   					foreach ($checkstatus as $query) {
   						$results[] = $query->lkp_post_status_id;
   					}
   					if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
   					{
   					AirdomBuyerQuote::where ( "id", $postIds )->update ( array (   							
   							'is_cancelled' => '1',
   							'lkp_post_status_id' => CANCELLED,
   							'updated_at' => $updatedAt,
   							'updated_by' => Auth::User ()->id,
   							'updated_ip' => $updatedIp
   					)); 
   					AirdomBuyerQuoteItem::where ( "buyer_quote_id", $postIds )->update ( array (
		   					'is_cancelled' => '1',
		   					'lkp_post_status_id' => CANCELLED,
		   					'updated_at' => $updatedAt,
		   					'updated_by' => Auth::User ()->id,
		   					'updated_ip' => $updatedIp
   					));
   					
   					return "Post successfully deleted";
   					} else {
   						return "Please select open posts only";
   						return 0;
   					}
   					case COURIER       :
   						//check condition for post status open or not.
   						$checkstatus = DB::table('courier_buyer_quotes as bq')
   						->where('bq.id', $postIds)
   						->select('bq.lkp_post_status_id')
   						->get();
   						foreach ($checkstatus as $query) {
   							$results[] = $query->lkp_post_status_id;
   						}
   						if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
   						{
   							CourierBuyerQuote::where ( "id", $postIds )->update ( array (
   									'is_cancelled' => '1',
   									'lkp_post_status_id' => CANCELLED,
   									'updated_at' => $updatedAt,
   									'updated_by' => Auth::User ()->id,
   									'updated_ip' => $updatedIp
   							));
   							CourierBuyerQuoteItem::where ( "buyer_quote_id", $postIds )->update ( array (
   									'is_cancelled' => '1',
   									'lkp_post_status_id' => CANCELLED,
   									'updated_at' => $updatedAt,
   									'updated_by' => Auth::User ()->id,
   									'updated_ip' => $updatedIp
   							));
   					
   							return "Post successfully deleted";
   						} else {
   							return "Please select open posts only";
   							return 0;
   						}    						
   				break;
                        case AIR_INTERNATIONAL      :
   					//check condition for post status open or not.
   					$checkstatus = DB::table('airint_buyer_quotes as bq')
   					->where('bq.id', $postIds)
   					->select('bq.lkp_post_status_id')
   					->get();
   					foreach ($checkstatus as $query) {
   						$results[] = $query->lkp_post_status_id;
   					}
   					if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
   					{
   					AirintBuyerQuote::where ( "id", $postIds )->update ( array (   							
   							'is_cancelled' => '1',
   							'lkp_post_status_id' => CANCELLED,
   							'updated_at' => $updatedAt,
   							'updated_by' => Auth::User ()->id,
   							'updated_ip' => $updatedIp
   					)); 
   					AirintBuyerQuoteItem::where ( "buyer_quote_id", $postIds )->update ( array (
		   					'is_cancelled' => '1',
		   					'lkp_post_status_id' => CANCELLED,
		   					'updated_at' => $updatedAt,
		   					'updated_by' => Auth::User ()->id,
		   					'updated_ip' => $updatedIp
   					));
   					
   					return "Post successfully deleted";
   					} else {
   						return "Please select open posts only";
   						return 0;
   					}   						
   				
   				break;
                        case OCEAN       :
   					//check condition for post status open or not.
   					$checkstatus = DB::table('ocean_buyer_quotes as bq')
   					->where('bq.id', $postIds)
   					->select('bq.lkp_post_status_id')
   					->get();
   					foreach ($checkstatus as $query) {
   						$results[] = $query->lkp_post_status_id;
   					}
   					if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
   					{
   					OceanBuyerQuote::where ( "id", $postIds )->update ( array (   							
   							'is_cancelled' => '1',
   							'lkp_post_status_id' => CANCELLED,
   							'updated_at' => $updatedAt,
   							'updated_by' => Auth::User ()->id,
   							'updated_ip' => $updatedIp
   					)); 
   					OceanBuyerQuoteItem::where ( "buyer_quote_id", $postIds )->update ( array (
		   					'is_cancelled' => '1',
		   					'lkp_post_status_id' => CANCELLED,
		   					'updated_at' => $updatedAt,
		   					'updated_by' => Auth::User ()->id,
		   					'updated_ip' => $updatedIp
   					));
   					
   					return "Posts successfully deleted";
   					} else {
   						return "Please select open posts only";
   						return 0;
   					}    						
   				break;
   			case ROAD_INTRACITY :
   				//check condition for post status open or not.
   				$checkstatus = DB::table('ict_buyer_quote_items')
   				->where('ict_buyer_quote_items.id', $postIds)
   				->select('ict_buyer_quote_items.lkp_post_status_id')
   				->get();
   				foreach ($checkstatus as $query) {
   					$results[] = $query->lkp_post_status_id;
   				}
   				
   				if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
   				{
   					IctBuyerQuoteItem::where ( "id", $postIds )->update ( array (
   							'lkp_post_status_id' => CANCELLED,
   							'is_cancelled' => '1',
   							'updated_at' => $updatedAt,
   							'updated_by' => Auth::User ()->id,
   							'updated_ip' => $updatedIp
   					));  
   					return "Post successfully deleted";
   				} else {
                    return "Please select open posts only";
                    return 0;
   				}  
   				break;
   				case ROAD_TRUCK_LEASE:
   					//check condition for post status open or not.
   					$checkstatus = DB::table('trucklease_buyer_quote_items')
   					->where('trucklease_buyer_quote_items.id', $postIds)
   					->select('trucklease_buyer_quote_items.lkp_post_status_id')
   					->get();
   					foreach ($checkstatus as $query) {
   						$results[] = $query->lkp_post_status_id;
   					}
   				
   					if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
   					{
   						TruckleaseBuyerQuoteItem::where ( "id", $postIds )->update ( array (
   						'lkp_post_status_id' => CANCELLED,
   						'is_cancelled' => '1',
   						'updated_at' => $updatedAt,
   						'updated_by' => Auth::User ()->id,
   						'updated_ip' => $updatedIp
   						));
   						return "Post successfully deleted";
   					} else {
   						return "Please select open posts only";
   						return 0;
   					}
   					break;
            case ROAD_TRUCK_HAUL       : 
                //check condition for post status open or not.
                $checkstatus = DB::table('truckhaul_buyer_quote_items')
                ->where('truckhaul_buyer_quote_items.id', $postIds)
                ->select('truckhaul_buyer_quote_items.lkp_post_status_id')
                ->get();
                foreach ($checkstatus as $query) {
                    $results[] = $query->lkp_post_status_id;
                }
                
                if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
                {
                    TruckhaulBuyerQuoteItem::where ( "id", $postIds )->update ( array (
                            'lkp_post_status_id' => CANCELLED,
                            'is_cancelled' => '1',
                            'updated_at' => $updatedAt,
                            'updated_by' => Auth::User ()->id,
                            'updated_ip' => $updatedIp
                    ));  
                    return "Post successfully deleted";
                } else {
                        return "Please select open posts only";
                        return 0;
                    }  
                break;   			
                case RELOCATION_DOMESTIC:

                        RelocationBuyerPost::where ( "id", $postIds )->update ( array (
                                        'lkp_post_status_id' => CANCELLED,
                                        'updated_at' => $updatedAt,
                                        'updated_by' => Auth::User ()->id,
                                        'updated_ip' => $updatedIp
                        ));
                        return "Post successfully deleted";


                        break;
                
                case RELOCATION_OFFICE_MOVE:
                            RelocationofficeBuyerPost::where ( "id", $postIds )->update ( array (
                        	'lkp_post_status_id' => CANCELLED,
                        	'updated_at' => $updatedAt,
                        	'updated_by' => Auth::User ()->id,
                        	'updated_ip' => $updatedIp
                        	));
                        	return "Post successfully deleted";
                        	break;
                
                case RELOCATION_PET_MOVE:
                            $updateRec = \App\Models\RelocationPetBuyerPost::where("id", $postIds)
                                ->update ([
                                    'lkp_post_status_id' => CANCELLED,
                                    'updated_at' => $updatedAt,
                                    'updated_by' => Auth::User()->id,
                                    'updated_ip' => $updatedIp
                                ]);
                            return "Post successfully deleted";
                            break;
                case RELOCATION_INTERNATIONAL:

                        RelocationintBuyerPost::where ( "id", $postIds )->update ( array (
                                        'lkp_post_status_id' => CANCELLED,
                                        'updated_at' => $updatedAt,
                                        'updated_by' => Auth::User ()->id,
                                        'updated_ip' => $updatedIp
                        ));
                        return "Post successfully deleted";


                        break;                            

                default:
                        break;    

   		}
   
   	} catch ( Exception $ex ) {
   
   		return 0;
   	 }
   	}
   }
   
   /*     * ***** Below Script for get Vehicle types  from  weight(MT, KG,Gm)************** */

    public function getVehicles() {
        try {
            $serviceId = Session::get ( 'service_id' );
            Log::info('Get Vehicle types depends on weight: ' . Auth::id(), array('c' => '1'));
            $roleId = Auth::User()->lkp_role_id;
            //Update the user activity to the buyer get capacity
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_VEHICLES", BUYER_VEHICLES, 0, HTTP_REFERRER, CURRENT_URL);
            }
            $weight = $_REQUEST['weight'];
            $weight_type = $_REQUEST['weight_type'];
            switch($serviceId){
   		case ROAD_FTL       : 
                       
                        if (isset($weight) && $weight!='') {
                            if(isset($_REQUEST['search']) && $_REQUEST['search']==1)
                            $str = '<option value = "20">Vehicle Type (Any)</option>';
                            else
                               $str = '<option value = "">Select Vehicle Type *</option>'; 
                            if($weight_type==2){
                               
                                $vehicle_types = CommonComponent::getAllVehicleType();
                                foreach ($vehicle_types as $k => $v) {
                                $str.='<option value = "' . $k . '">' . $v . '</option>';
                                }
                            }elseif($weight_type==1){
                              
                                $mt =   $weight/1000;
                                $qry    =   DB::table('lkp_vehicle_types as v');
                                $qry->whereRaw("v.capacity <=CASE WHEN v.lkp_ict_weight_uom_id =1 THEN $weight WHEN v.lkp_ict_weight_uom_id =3 THEN $mt END");
                                $vehicle_types =$qry->lists('v.vehicle_type','v.id');
                                foreach ($vehicle_types as $k => $v) {
                                $str.='<option value = "' . $k . '">' . $v . '</option>';
                                }
                                 
                            }elseif($weight_type==3){
                              
                                $res  =   DB::select(DB::raw('select `vehicle_type`, `id` from `lkp_vehicle_types` where (`capacity` <= '.$weight.' and `lkp_ict_weight_uom_id` ='.$weight_type.') or lkp_ict_weight_uom_id=1'));
                                foreach ($res as $key) {
                                $str.='<option value = "' . $key->id . '">' . $key->vehicle_type . '</option>';
                                }
                            }
                            echo $str;
                        } 
                        else // else condition for requested weight (quantity) is empty
                        {
                            if ($roleId == SELLER) {  // If the role is seller then 
                                if(isset($_REQUEST['search']) && $_REQUEST['search']==1)
                                    $str = '<option value = "20">Vehicle Type (Any)</option>';
                                else
                                   $str = '<option value = "">Select Vehicle Type *</option>'; 
                                   
                                $vehicle_types = CommonComponent::getAllVehicleType();
                                foreach ($vehicle_types as $k => $v) {
                                    $str.='<option value = "' . $k . '">' . $v . '</option>';
                                }
                                echo $str;
                            }                                
                        }
                        break;
                        case ROAD_TRUCK_HAUL       :
                        	 
                        	if (isset($weight) && $weight!='') {
                        		if(isset($_REQUEST['search']) && $_REQUEST['search']==1)
                        			$str = '<option value = "20">Vehicle Type (Any)</option>';
                        		else
                        			$str = '<option value = "">Select Vehicle Type</option>';
                        		//if($weight_type==3){
                        
                        			//$res  =   DB::select(DB::raw('select `vehicle_type`, `id` from `lkp_vehicle_types` where `capacity` >= '.$weight.' and `lkp_ict_weight_uom_id` ='.$weight_type.' '));
                                    $res  =   DB::select(DB::raw('select `vehicle_type`, `id` from `lkp_vehicle_types` where `capacity` >= '.$weight.''));
                        			foreach ($res as $key) {
                        				$str.='<option value = "' . $key->id . '">' . $key->vehicle_type . '</option>';
                        			}
                        		//}
                        
                        		echo $str;
                        
                        
                        	}
                        	break;
                case ROAD_INTRACITY:
                    
                if (isset($weight) && $weight!='') {
                
                    if($weight_type==2){
                        $str = '<option value = "">Vehicle Type</option>';
                        $vehicle_types = CommonComponent::getAllVehicleTypes();
                        foreach ($vehicle_types as $k => $v) {
                        $str.='<option value = "' . $k . '">' . $v . '</option>';
                        }echo $str;
                    }elseif($weight_type==1){
                        $str = '<option value = "">Vehicle Type</option>';
                        $mt =   $weight/1000;
                        $qry    =   DB::table('lkp_vehicle_types as v');
                        $qry->whereRaw("v.capacity >=CASE WHEN v.lkp_ict_weight_uom_id =1 THEN $weight WHEN v.lkp_ict_weight_uom_id =3 THEN $mt END and is_intracity=1");
                        $vehicle_types =$qry->lists('v.vehicle_type','v.id');
                        foreach ($vehicle_types as $k => $v) {
                        $str.='<option value = "' . $k . '">' . $v . '</option>';
                        }echo $str;
                       
                    }elseif($weight_type==3){
                        $str = '<option value = "">Vehicle Type</option>';
                       
                        $res  =   DB::select(DB::raw('select `vehicle_type`, `id` from `lkp_vehicle_types` where `capacity` >= '.$weight.' and `lkp_ict_weight_uom_id` ='.$weight_type.' and is_intracity=1'));
                       
                        foreach ($res as $key) {
                        $str.='<option value = "' . $key->id . '">' . $key->vehicle_type . '</option>';
                        }echo $str;
                    }
                
                
                
                } break;

                case ROAD_TRUCK_HAUL       :

                    if (isset($weight) && $weight!='') {
                        if(isset($_REQUEST['search']) && $_REQUEST['search']==1)
                            $str = '<option value = "20">Vehicle Type (Any)</option>';
                        else
                            $str = '<option value = "">Select Vehicle Type</option>';
                        if($weight_type==2){

                            $vehicle_types = CommonComponent::getAllVehicleType();
                            foreach ($vehicle_types as $k => $v) {
                                $str.='<option value = "' . $k . '">' . $v . '</option>';
                            }
                        }elseif($weight_type==1){

                            $mt =   $weight/1000;
                            $qry    =   DB::table('lkp_vehicle_types as v');
                            $qry->whereRaw("v.capacity <=CASE WHEN v.lkp_ict_weight_uom_id =1 THEN $weight WHEN v.lkp_ict_weight_uom_id =3 THEN $mt END");
                            $vehicle_types =$qry->lists('v.vehicle_type','v.id');
                            foreach ($vehicle_types as $k => $v) {
                                $str.='<option value = "' . $k . '">' . $v . '</option>';
                            }

                        }elseif($weight_type==3){

                            $res  =   DB::select(DB::raw('select `vehicle_type`, `id` from `lkp_vehicle_types` where (`capacity` <= '.$weight.' and `lkp_ict_weight_uom_id` ='.$weight_type.') or lkp_ict_weight_uom_id=1'));
                            foreach ($res as $key) {
                                $str.='<option value = "' . $key->id . '">' . $key->vehicle_type . '</option>';
                            }
                        }

                        echo $str;


                    }
                    break;

       

            default             :
   				break;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }
    
    
    /**
     * Show the form for creating a new CreateBuyerQuote.
     * Create new quotes to sellers
     * @return \Illuminate\Http\Response
     */
    public function CreateSearchBuyerQuote(Request $request) 
    {   
        try {
            Log::info('Create new buyer quote: ' . Auth::id(), array('c' => '1'));
            $roleId = Auth::User()->lkp_role_id;
            //Saving the user activity to the buyer new quote log table
            if ($roleId == BUYER) {
                CommonComponent::activityLog("BUYER_ADDED_NEW_QUOTE", BUYER_ADDED_NEW_QUOTE, 0, HTTP_REFERRER, CURRENT_URL);
            }

            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER ['REMOTE_ADDR'];
            
            $serviceId = Session::get('service_id');
        switch ($serviceId) {
        case ROAD_FTL :
            $ordid  =   CommonComponent::getPostID(ROAD_FTL);
            $created_year = date('Y');
            $trans_randid = 'FTL/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
           
            /* * ****Single insert in buer quote table******** */
            $buyerquote = new BuyerQuotes();
            $buyerquote->lkp_service_id = ROAD_FTL;
            $buyerquote->lkp_lead_type_id = FTL_SPOT;
            $buyerquote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
            $buyerquote->transaction_id = $trans_randid;
            $buyerquote->is_commercial = Session::get('searchMod.is_commercial_date_buyer');
            $buyerquote->buyer_id = Auth::id();
            $buyerquote->created_by = Auth::id();
            $buyerquote->created_at = $created_at;
            $buyerquote->created_ip = $createdIp;

            if ($buyerquote->save()) {
                //Maintaining a log of data for buyer new quote creation
                CommonComponent::auditLog($buyerquote->id, 'buyer_quotes');
                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                    $post   =   CommonComponent::getSellerPostDetails($_REQUEST['postItemId']);
                }
                /* * ****Multiple insert in quote items******** */
                $Quote_Lineitems = new BuyerQuoteItems();
                $Quote_Lineitems->buyer_quote_id = $buyerquote->id;
                $Quote_Lineitems->dispatch_date = CommonComponent::convertDateForDatabase(Session::get('searchMod.dispatch_date_buyer'));
                if(Session::get('searchMod.delivery_date_buyer')!='')
                    $Quote_Lineitems->delivery_date = CommonComponent::convertDateForDatabase(Session::get('searchMod.delivery_date_buyer'));
                else{
                    if(isset($post->transitdays)){
                        $dispath    =   date('Y-m-d',strtotime( $Quote_Lineitems->dispatch_date) + ($post->transitdays*24*3600));
                        $Quote_Lineitems->delivery_date = CommonComponent::convertDateForDatabase($dispath);
                    }
                }
                $Quote_Lineitems->lkp_quote_price_type_id = FIRM;
                $Quote_Lineitems->from_city_id = Session::get('searchMod.from_city_id_buyer');
                $Quote_Lineitems->to_city_id = Session::get('searchMod.to_city_id_buyer');
                $Quote_Lineitems->lkp_load_type_id = Session::get('searchMod.load_type_buyer');
              
                $Quote_Lineitems->lkp_vehicle_type_id = $post->lkp_vehicle_type_id;
                $Quote_Lineitems->units = Session::get('searchMod.capacity_buyer');
                
                //number of loads calculation                
                $vehicle_type = $post->lkp_vehicle_type_id;
                
                
                $noofloads  =   CommonComponent::ftlNoofLoads($vehicle_type);
                $Quote_Lineitems->number_loads = $noofloads;
                
                $Quote_Lineitems->quantity = Session::get('searchMod.quantity_buyer');
                if(isset($post->price))
                $Quote_Lineitems->price = $post->price;
                $Quote_Lineitems->lkp_post_status_id = ORDERED;
                $Quote_Lineitems->created_by = Auth::id();
                $Quote_Lineitems->created_at = $created_at;
                $Quote_Lineitems->created_ip = $createdIp;
                $Quote_Lineitems->save();
                //Maintaining a log of data for buyer new quote multiple items creation
                CommonComponent::auditLog($Quote_Lineitems->id, 'buyer_quote_items');
                return $Quote_Lineitems->id;break;

            }
            case ROAD_PTL :
                $ordid  =   CommonComponent::getPostID(ROAD_PTL);
                $created_year = date('Y');
                $trans_randid = 'LTL/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
               
                
                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                        $post   =   CommonComponent::getPtlSellerPostDetails($_REQUEST['postItemId']);
                    }
                /* * ****Single insert in buer quote table******** */
                $ptlBuyerQuote = new PtlBuyerQuote();
                $ptlBuyerQuote->lkp_service_id = ROAD_PTL;
                $ptlBuyerQuote->lkp_lead_type_id = FTL_SPOT;
                $ptlBuyerQuote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
                $ptlBuyerQuote->transaction_id = $trans_randid;
                $ptlBuyerQuote->dispatch_date = CommonComponent::convertDateForDatabase($_REQUEST['dispatch']);
                if($_REQUEST['delivery']!='')
                        $ptlBuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($_REQUEST['delivery']);
                    else{
                        if(isset($post->transitdays)){
                            $dispath    =   date('Y-m-d',strtotime( $ptlBuyerQuote->dispatch_date)+($post->transitdays*24*3600));
                            $ptlBuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($dispath);
                        }
                    }
                $ptlBuyerQuote->from_location_id = $_REQUEST['from'];
                $ptlBuyerQuote->to_location_id = $_REQUEST['to'];
                $ptlBuyerQuote->is_dispatch_flexible = $_REQUEST['fdispatch'];
                $ptlBuyerQuote->is_delivery_flexible = $_REQUEST['fdelivery'];
                $ptlBuyerQuote->is_door_pickup = $_REQUEST['door_pick'];
                $ptlBuyerQuote->is_door_delivery = $_REQUEST['door_del'];
                $ptlBuyerQuote->lkp_post_status_id = ORDERED;
                
                $request_buyer_data = Session::get('ptlBuyerSearchform');
                //print_r($request_buyer_data);exit;
                $ptlBuyerQuote->is_commercial = $request_buyer_data['is_commercial'];
                $ptlBuyerQuote->buyer_id = Auth::id();
                $ptlBuyerQuote->created_by = Auth::id();
                $ptlBuyerQuote->created_at = $created_at;
                $ptlBuyerQuote->created_ip = $createdIp;


                if ($ptlBuyerQuote->save()) {
                    //Maintaining a log of data for buyer new quote creation
                    CommonComponent::auditLog($ptlBuyerQuote->id, 'ptl_buyer_quotes');
                    $ptlQuote_Lineitems = new PtlBuyerQuoteItem();
                    $ptlQuote_Lineitems->buyer_quote_id = $ptlBuyerQuote->id;
                    $ptlQuote_Lineitems->lkp_quote_price_type_id = COMPETITIVE;
                    $ptlQuote_Lineitems->lkp_load_type_id = $_REQUEST['load'];
                    $ptlQuote_Lineitems->lkp_packaging_type_id = $_REQUEST['pack'];
                    $ptlQuote_Lineitems->length = $_REQUEST['length'];
                    $ptlQuote_Lineitems->breadth = $_REQUEST['width'];
                    $ptlQuote_Lineitems->height = $_REQUEST['height'];
                    $ptlQuote_Lineitems->lkp_ptl_length_uom_id = $_REQUEST['vol_type'];
                    $ptlQuote_Lineitems->calculated_volume_weight = $_REQUEST['volume'];
                    $ptlQuote_Lineitems->units = $_REQUEST['unit_weight'];
                    $ptlQuote_Lineitems->number_packages = $_REQUEST['no_pack'];
                    $ptlQuote_Lineitems->lkp_ict_weight_uom_id = $_REQUEST['weight_type'];
                    $ptlQuote_Lineitems->lkp_post_status_id = ORDERED;
                    $ptlQuote_Lineitems->created_by = Auth::id();
                    $ptlQuote_Lineitems->created_at = $created_at;
                    $ptlQuote_Lineitems->created_ip = $createdIp;
                    $ptlQuote_Lineitems->save();
                    //Maintaining a log of data for buyer new quote multiple items creation
                    CommonComponent::auditLog($ptlQuote_Lineitems->id, 'ptl_buyer_quote_items');
                    return $ptlBuyerQuote->id;break;

                }
            
            case RAIL :
                $ordid  =   CommonComponent::getPostID(RAIL);
            
                $created_year = date('Y');
                $trans_randid = 'RAIL/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 

                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                        $post   =   CommonComponent::getPtlSellerPostDetails($_REQUEST['postItemId']);
                    }
                /* * ****Single insert in buer quote table******** */
                $ptlBuyerQuote = new RailBuyerQuote();
                $ptlBuyerQuote->lkp_service_id = RAIL;
                $ptlBuyerQuote->lkp_lead_type_id = FTL_SPOT;
                $ptlBuyerQuote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
                $ptlBuyerQuote->transaction_id = $trans_randid;
                $ptlBuyerQuote->dispatch_date = CommonComponent::convertDateForDatabase($_REQUEST['dispatch']);
                if($_REQUEST['delivery']!='')
                        $ptlBuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($_REQUEST['delivery']);
                    else{
                        if(isset($post->transitdays)){
                            $dispath    =   date('Y-m-d',strtotime( $ptlBuyerQuote->dispatch_date)+($post->transitdays*24*3600));
                            $ptlBuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($dispath);
                        }
                    }
                $ptlBuyerQuote->from_location_id = $_REQUEST['from'];
                $ptlBuyerQuote->to_location_id = $_REQUEST['to'];
                $ptlBuyerQuote->is_dispatch_flexible = $_REQUEST['fdispatch'];
                $ptlBuyerQuote->is_delivery_flexible = $_REQUEST['fdelivery'];
                $ptlBuyerQuote->is_door_pickup = $_REQUEST['door_pick'];
                $ptlBuyerQuote->is_door_delivery = $_REQUEST['door_del'];
                $ptlBuyerQuote->lkp_post_status_id = ORDERED;
                $request_buyer_data = Session::get('ptlBuyerSearchform');
                $ptlBuyerQuote->is_commercial = $request_buyer_data['is_commercial'];
                $ptlBuyerQuote->buyer_id = Auth::id();
                $ptlBuyerQuote->created_by = Auth::id();
                $ptlBuyerQuote->created_at = $created_at;
                $ptlBuyerQuote->created_ip = $createdIp;


                if ($ptlBuyerQuote->save()) {
                    //Maintaining a log of data for buyer new quote creation
                    CommonComponent::auditLog($ptlBuyerQuote->id, 'rail_buyer_quotes');
                    $ptlQuote_Lineitems = new RailBuyerQuoteItem();
                    $ptlQuote_Lineitems->buyer_quote_id = $ptlBuyerQuote->id;
                    $ptlQuote_Lineitems->lkp_quote_price_type_id = COMPETITIVE;
                    $ptlQuote_Lineitems->lkp_load_type_id = $_REQUEST['load'];
                    $ptlQuote_Lineitems->lkp_packaging_type_id = $_REQUEST['pack'];
                    $ptlQuote_Lineitems->length = $_REQUEST['length'];
                    $ptlQuote_Lineitems->breadth = $_REQUEST['width'];
                    $ptlQuote_Lineitems->height = $_REQUEST['height'];
                    $ptlQuote_Lineitems->lkp_ptl_length_uom_id = $_REQUEST['vol_type'];
                    $ptlQuote_Lineitems->calculated_volume_weight = $_REQUEST['volume'];
                    $ptlQuote_Lineitems->units = $_REQUEST['unit_weight'];
                    $ptlQuote_Lineitems->number_packages = $_REQUEST['no_pack'];
                    $ptlQuote_Lineitems->lkp_ict_weight_uom_id = $_REQUEST['weight_type'];
                    $ptlQuote_Lineitems->lkp_post_status_id = ORDERED;
                    $ptlQuote_Lineitems->created_by = Auth::id();
                    $ptlQuote_Lineitems->created_at = $created_at;
                    $ptlQuote_Lineitems->created_ip = $createdIp;
                    $ptlQuote_Lineitems->save();
                    //Maintaining a log of data for buyer new quote multiple items creation
                    CommonComponent::auditLog($ptlQuote_Lineitems->id, 'rail_buyer_quote_items');
                    return $ptlBuyerQuote->id;break;

                }
            case AIR_DOMESTIC :
                $ordid  =   CommonComponent::getPostID(AIR_DOMESTIC);
                $created_year = date('Y');
                $trans_randid = 'AIRDOMESTIC/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
               
                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                        $post   =   CommonComponent::getPtlSellerPostDetails($_REQUEST['postItemId']);
                    }
                /* * ****Single insert in buer quote table******** */
                $ptlBuyerQuote = new AirdomBuyerQuote();
                $ptlBuyerQuote->lkp_service_id = AIR_DOMESTIC;
                $ptlBuyerQuote->lkp_lead_type_id = FTL_SPOT;
                $ptlBuyerQuote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
                $ptlBuyerQuote->transaction_id = $trans_randid;
                $ptlBuyerQuote->dispatch_date = CommonComponent::convertDateForDatabase($_REQUEST['dispatch']);
                if($_REQUEST['delivery']!='')
                        $ptlBuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($_REQUEST['delivery']);
                    else{
                        if(isset($post->transitdays)){
                            $dispath    =   date('Y-m-d',strtotime( $ptlBuyerQuote->dispatch_date)+($post->transitdays*24*3600));
                            $ptlBuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($dispath);
                        }
                    }
                $ptlBuyerQuote->from_location_id = $_REQUEST['from'];
                $ptlBuyerQuote->to_location_id = $_REQUEST['to'];
                $ptlBuyerQuote->is_dispatch_flexible = $_REQUEST['fdispatch'];
                $ptlBuyerQuote->is_delivery_flexible = $_REQUEST['fdelivery'];
                $ptlBuyerQuote->is_door_pickup = $_REQUEST['door_pick'];
                $ptlBuyerQuote->is_door_delivery = $_REQUEST['door_del'];
                $ptlBuyerQuote->lkp_post_status_id = ORDERED;
                $request_buyer_data = Session::get('ptlBuyerSearchform');
                $ptlBuyerQuote->is_commercial = $request_buyer_data['is_commercial'];
                $ptlBuyerQuote->buyer_id = Auth::id();
                $ptlBuyerQuote->created_by = Auth::id();
                $ptlBuyerQuote->created_at = $created_at;
                $ptlBuyerQuote->created_ip = $createdIp;


                if ($ptlBuyerQuote->save()) {
                    //Maintaining a log of data for buyer new quote creation
                    CommonComponent::auditLog($ptlBuyerQuote->id, 'airdom_buyer_quotes');
                    $ptlQuote_Lineitems = new AirdomBuyerQuoteItem();
                    $ptlQuote_Lineitems->buyer_quote_id = $ptlBuyerQuote->id;
                    $ptlQuote_Lineitems->lkp_quote_price_type_id = COMPETITIVE;
                    $ptlQuote_Lineitems->lkp_load_type_id = $_REQUEST['load'];
                    $ptlQuote_Lineitems->lkp_packaging_type_id = $_REQUEST['pack'];
                    $ptlQuote_Lineitems->length = $_REQUEST['length'];
                    $ptlQuote_Lineitems->breadth = $_REQUEST['width'];
                    $ptlQuote_Lineitems->height = $_REQUEST['height'];
                    $ptlQuote_Lineitems->lkp_ptl_length_uom_id = $_REQUEST['vol_type'];
                    $ptlQuote_Lineitems->calculated_volume_weight = $_REQUEST['volume'];
                    $ptlQuote_Lineitems->units = $_REQUEST['unit_weight'];
                    $ptlQuote_Lineitems->number_packages = $_REQUEST['no_pack'];
                    $ptlQuote_Lineitems->lkp_ict_weight_uom_id = $_REQUEST['weight_type'];
                    $ptlQuote_Lineitems->lkp_post_status_id = ORDERED;
                    $ptlQuote_Lineitems->created_by = Auth::id();
                    $ptlQuote_Lineitems->created_at = $created_at;
                    $ptlQuote_Lineitems->created_ip = $createdIp;
                    $ptlQuote_Lineitems->save();
                    //Maintaining a log of data for buyer new quote multiple items creation
                    CommonComponent::auditLog($ptlQuote_Lineitems->id, 'airdom_buyer_quote_items');
                    return $ptlBuyerQuote->id;break;

                }
            case AIR_INTERNATIONAL :
                $ordid  =   CommonComponent::getPostID(AIR_INTERNATIONAL);
                $created_year = date('Y');
                $trans_randid = 'AIRINTERNATIONAL/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                
                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                        $post   =   CommonComponent::getPtlSellerPostDetails($_REQUEST['postItemId']);
                    }
                /* * ****Single insert in buer quote table******** */
                $ptlBuyerQuote = new AirintBuyerQuote();
                $ptlBuyerQuote->lkp_service_id = AIR_INTERNATIONAL;
                $ptlBuyerQuote->lkp_lead_type_id = FTL_SPOT;
                $ptlBuyerQuote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
                $ptlBuyerQuote->transaction_id = $trans_randid;
                $ptlBuyerQuote->dispatch_date = CommonComponent::convertDateForDatabase($_REQUEST['dispatch']);
                if($_REQUEST['delivery']!='')
                        $ptlBuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($_REQUEST['delivery']);
                    else{
                        if(isset($post->transitdays)){
                            $dispath    =   date('Y-m-d',strtotime( $ptlBuyerQuote->dispatch_date)+($post->transitdays*24*3600));
                            $ptlBuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($dispath);
                        }
                    }
                $ptlBuyerQuote->from_location_id = $_REQUEST['from'];
                $ptlBuyerQuote->to_location_id = $_REQUEST['to'];
                $ptlBuyerQuote->is_dispatch_flexible = $_REQUEST['fdispatch'];
                $ptlBuyerQuote->is_delivery_flexible = $_REQUEST['fdelivery'];
                
                $ptlBuyerQuote->lkp_air_ocean_shipment_type_id = $_REQUEST['shipment_type'];
                $ptlBuyerQuote->lkp_air_ocean_sender_identity_id = $_REQUEST['sender_identity'];
                $ptlBuyerQuote->ie_code = $_REQUEST['iecode'];
                $ptlBuyerQuote->product_made = $_REQUEST['product_made'];
                
                $ptlBuyerQuote->lkp_post_status_id = ORDERED;
                $request_buyer_data = Session::get('ptlBuyerSearchform');
                $ptlBuyerQuote->is_commercial = $request_buyer_data['is_commercial'];
                $ptlBuyerQuote->buyer_id = Auth::id();
                $ptlBuyerQuote->created_by = Auth::id();
                $ptlBuyerQuote->created_at = $created_at;
                $ptlBuyerQuote->created_ip = $createdIp;


                if ($ptlBuyerQuote->save()) {
                    //Maintaining a log of data for buyer new quote creation
                    CommonComponent::auditLog($ptlBuyerQuote->id, 'airint_buyer_quotes');
                    $ptlQuote_Lineitems = new AirintBuyerQuoteItem();
                    $ptlQuote_Lineitems->buyer_quote_id = $ptlBuyerQuote->id;
                    $ptlQuote_Lineitems->lkp_quote_price_type_id = COMPETITIVE;
                    $ptlQuote_Lineitems->lkp_load_type_id = $_REQUEST['load'];
                    $ptlQuote_Lineitems->lkp_packaging_type_id = $_REQUEST['pack'];
                    $ptlQuote_Lineitems->length = $_REQUEST['length'];
                    $ptlQuote_Lineitems->breadth = $_REQUEST['width'];
                    $ptlQuote_Lineitems->height = $_REQUEST['height'];
                    $ptlQuote_Lineitems->lkp_ptl_length_uom_id = $_REQUEST['vol_type'];
                    $ptlQuote_Lineitems->calculated_volume_weight = $_REQUEST['volume'];
                    $ptlQuote_Lineitems->units = $_REQUEST['unit_weight'];
                    $ptlQuote_Lineitems->number_packages = $_REQUEST['no_pack'];
                    $ptlQuote_Lineitems->lkp_ict_weight_uom_id = $_REQUEST['weight_type'];
                    $ptlQuote_Lineitems->lkp_post_status_id = ORDERED;
                    $ptlQuote_Lineitems->created_by = Auth::id();
                    $ptlQuote_Lineitems->created_at = $created_at;
                    $ptlQuote_Lineitems->created_ip = $createdIp;
                    $ptlQuote_Lineitems->save();
                    //Maintaining a log of data for buyer new quote multiple items creation
                    CommonComponent::auditLog($ptlQuote_Lineitems->id, 'airint_buyer_quote_items');
                    return $ptlBuyerQuote->id;break;

                }
            case OCEAN :
                $ordid  =   CommonComponent::getPostID(OCEAN);
                $created_year = date('Y');
                $trans_randid = 'OCEAN/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
               
                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                        $post   =   CommonComponent::getPtlSellerPostDetails($_REQUEST['postItemId']);
                    }
                /* * ****Single insert in buer quote table******** */
                $ptlBuyerQuote = new OceanBuyerQuote();
                $ptlBuyerQuote->lkp_service_id = OCEAN;
                $ptlBuyerQuote->lkp_lead_type_id = FTL_SPOT;
                $ptlBuyerQuote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
                $ptlBuyerQuote->transaction_id = $trans_randid;
                $ptlBuyerQuote->dispatch_date = CommonComponent::convertDateForDatabase($_REQUEST['dispatch']);
                if($_REQUEST['delivery']!='')
                        $ptlBuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($_REQUEST['delivery']);
                    else{
                        if(isset($post->transitdays)){
                            $dispath    =   date('Y-m-d',strtotime( $ptlBuyerQuote->dispatch_date)+($post->transitdays*24*3600));
                            $ptlBuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($dispath);
                        }
                    }
                $ptlBuyerQuote->from_location_id = $_REQUEST['from'];
                $ptlBuyerQuote->to_location_id = $_REQUEST['to'];
                $ptlBuyerQuote->is_dispatch_flexible = $_REQUEST['fdispatch'];
                $ptlBuyerQuote->is_delivery_flexible = $_REQUEST['fdelivery'];
                
                $ptlBuyerQuote->lkp_air_ocean_shipment_type_id = $_REQUEST['shipment_type'];
                $ptlBuyerQuote->lkp_air_ocean_sender_identity_id = $_REQUEST['sender_identity'];
                $ptlBuyerQuote->ie_code = $_REQUEST['iecode'];
                $ptlBuyerQuote->product_made = $_REQUEST['product_made'];
                
                $ptlBuyerQuote->lkp_post_status_id = ORDERED;
                $request_buyer_data = Session::get('ptlBuyerSearchform');
                $ptlBuyerQuote->is_commercial = $request_buyer_data['is_commercial'];
                $ptlBuyerQuote->buyer_id = Auth::id();
                $ptlBuyerQuote->created_by = Auth::id();
                $ptlBuyerQuote->created_at = $created_at;
                $ptlBuyerQuote->created_ip = $createdIp;


                if ($ptlBuyerQuote->save()) {
                    //Maintaining a log of data for buyer new quote creation
                    CommonComponent::auditLog($ptlBuyerQuote->id, 'ocean_buyer_quotes');
                    $ptlQuote_Lineitems = new OceanBuyerQuoteItem();
                    $ptlQuote_Lineitems->buyer_quote_id = $ptlBuyerQuote->id;
                    $ptlQuote_Lineitems->lkp_quote_price_type_id = COMPETITIVE;
                    $ptlQuote_Lineitems->lkp_load_type_id = $_REQUEST['load'];
                    $ptlQuote_Lineitems->lkp_packaging_type_id = $_REQUEST['pack'];
                    $ptlQuote_Lineitems->length = $_REQUEST['length'];
                    $ptlQuote_Lineitems->breadth = $_REQUEST['width'];
                    $ptlQuote_Lineitems->height = $_REQUEST['height'];
                    $ptlQuote_Lineitems->lkp_ptl_length_uom_id = $_REQUEST['vol_type'];
                    $ptlQuote_Lineitems->calculated_volume_weight = $_REQUEST['volume'];
                    $ptlQuote_Lineitems->units = $_REQUEST['unit_weight'];
                    $ptlQuote_Lineitems->number_packages = $_REQUEST['no_pack'];
                    $ptlQuote_Lineitems->lkp_ict_weight_uom_id = $_REQUEST['weight_type'];
                    $ptlQuote_Lineitems->lkp_post_status_id = ORDERED;
                    $ptlQuote_Lineitems->created_by = Auth::id();
                    $ptlQuote_Lineitems->created_at = $created_at;
                    $ptlQuote_Lineitems->created_ip = $createdIp;
                    $ptlQuote_Lineitems->save();
                    //Maintaining a log of data for buyer new quote multiple items creation
                    CommonComponent::auditLog($ptlQuote_Lineitems->id, 'ocean_buyer_quote_items');
                    return $ptlBuyerQuote->id;break;

                }
            case COURIER :
                $requestData= Session::get('request');
                $ordid  =   CommonComponent::getPostID(COURIER);
                $created_year = date('Y');
                $trans_randid = 'COURIER/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
               

                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                        $post   =   CommonComponent::getPtlSellerPostDetails($_REQUEST['postItemId']);
                    }
                /* * ****Single insert in buer quote table******** */
                $ptlBuyerQuote = new CourierBuyerQuote();
                $ptlBuyerQuote->lkp_service_id = COURIER;
                $ptlBuyerQuote->lkp_lead_type_id = FTL_SPOT;
                $ptlBuyerQuote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
                $ptlBuyerQuote->transaction_id = $trans_randid;
                $ptlBuyerQuote->dispatch_date = CommonComponent::convertDateForDatabase($_REQUEST['dispatch']);
                if($_REQUEST['delivery']!='')
                        $ptlBuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($_REQUEST['delivery']);
                    else{
                        if(isset($post->transitdays)){
                            $dispath    =   date('Y-m-d',strtotime( $ptlBuyerQuote->dispatch_date)+($post->transitdays*24*3600));
                            $ptlBuyerQuote->delivery_date = CommonComponent::convertDateForDatabase($dispath);
                        }
                    }
                $ptlBuyerQuote->from_location_id = $_REQUEST['from'];
                $ptlBuyerQuote->to_location_id = $_REQUEST['to'];
                $ptlBuyerQuote->lkp_post_status_id = ORDERED;
                $request_buyer_data = Session::get('ptlBuyerSearchform');
                $ptlBuyerQuote->is_commercial = $request_buyer_data['is_commercial'];
                $ptlBuyerQuote->buyer_id = Auth::id();
                $ptlBuyerQuote->created_by = Auth::id();
                $ptlBuyerQuote->created_at = $created_at;
                $ptlBuyerQuote->created_ip = $createdIp;


                if ($ptlBuyerQuote->save()) {
                    //Maintaining a log of data for buyer new quote creation
                    CommonComponent::auditLog($ptlBuyerQuote->id, 'courier_buyer_quotes');
                    $ptlQuote_Lineitems = new CourierBuyerQuoteItem();
                    $ptlQuote_Lineitems->buyer_quote_id = $ptlBuyerQuote->id;
                    $ptlQuote_Lineitems->lkp_quote_price_type_id = COMPETITIVE;
                    $ptlQuote_Lineitems->lkp_courier_type_id = $requestData['courier_types'][0];
                    $ptlQuote_Lineitems->lkp_courier_delivery_type_id = $requestData['post_delivery_types'][0];
                    $ptlQuote_Lineitems->length = $_REQUEST['length'];
                    $ptlQuote_Lineitems->breadth = $_REQUEST['width'];
                    $ptlQuote_Lineitems->height = $_REQUEST['height'];
                    $ptlQuote_Lineitems->lkp_ptl_length_uom_id = $_REQUEST['vol_type'];
                    $ptlQuote_Lineitems->calculated_volume_weight = $_REQUEST['volume'];
                    $ptlQuote_Lineitems->units = $_REQUEST['unit_weight'];
                    $ptlQuote_Lineitems->number_packages = $_REQUEST['no_pack'];
                    $ptlQuote_Lineitems->lkp_ict_weight_uom_id = $_REQUEST['weight_type'];
                    $ptlQuote_Lineitems->lkp_post_status_id = ORDERED;
                    $ptlQuote_Lineitems->created_by = Auth::id();
                    $ptlQuote_Lineitems->created_at = $created_at;
                    $ptlQuote_Lineitems->created_ip = $createdIp;
                    $ptlQuote_Lineitems->save();
                    //Maintaining a log of data for buyer new quote multiple items creation
                    CommonComponent::auditLog($ptlQuote_Lineitems->id, 'courier_buyer_quote_items');
                    return $ptlBuyerQuote->id;
                }
                break;
            case ROAD_INTRACITY :
                
                $ordid  =   CommonComponent::getPostID(ROAD_INTRACITY);
                $created_year = date('Y');
                $trans_randid = 'INTRA/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 

                
                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                        $post   =   CommonComponent::getPtlSellerPostDetails($_REQUEST['postItemId']);
                    }
                /* * ****Single insert in buer quote table******** */
                $buyerQuote = new IctBuyerQuote ();
                $buyerQuote->lkp_service_id = ROAD_INTRACITY;
                $buyerQuote->transaction_id = $trans_randid;

                $buyerQuote->is_commercial = 0;//Session::get ( 'buyerSessionCommercialType' );
                $buyerQuote->buyer_id = Auth::id();
                $buyerQuote->created_by = Auth::id();
                $buyerQuote->created_at = $created_at;
                $buyerQuote->created_ip = $createdIp;


                if ($buyerQuote->save()) {
                    //Maintaining a log of data for buyer new quote creation
                    CommonComponent::auditLog ( $buyerQuote->id, 'ict_buyer_quotes' );
                    $buyerQuoteItem = new IctBuyerQuoteItem ();

                    $buyerQuoteItem->buyer_quote_id = $buyerQuote->id;
                    $buyerQuoteItem->pickup_date = CommonComponent::convertDateForDatabase( Session::get('buyerSessionFromDate') );
                    $buyerQuoteItem->pickup_time = Session::get('buyerSessionFromTime');
                    $buyerQuoteItem->ict_lkp_city_id = Session::get('buyerSessionFromcityId');
                    $buyerQuoteItem->from_location_id = Session::get('buyerSessionFromLocationId');
                    $buyerQuoteItem->to_location_id = Session::get('buyerSessionToLocationId');
                    $buyerQuoteItem->lkp_load_type_id = Session::get('buyerSessionLoadTypeId');
                    $buyerQuoteItem->lkp_vehicle_type_id = Session::get('buyerSessionVehicleTypeId');
                    $buyerQuoteItem->lkp_ict_weight_uom_id = Session::get('buyerSessionweightType');
                    $buyerQuoteItem->units = Session::get('buyerSessionweight');
                    $buyerQuoteItem->lkp_ict_rate_type_id = Session::get('buyerSessionRateType');
                    $buyerQuoteItem->lkp_post_status_id = ORDERED;
                    $buyerQuoteItem->created_by = Auth::id();
                    $buyerQuoteItem->created_at = $created_at;
                    $buyerQuoteItem->created_ip = $createdIp;
                    $buyerQuoteItem->save();
                    //Maintaining a log of data for buyer new quote multiple items creation
                    CommonComponent::auditLog ( $buyerQuoteItem->id, 'ict_buyer_quote_items' );
                    return $buyerQuoteItem->id;

                } 
                break;                 
            case RELOCATION_DOMESTIC :
                $ordid  =   CommonComponent::getPostID(RELOCATION_DOMESTIC);
                $created_year = date('Y');
                $trans_randid = 'RD/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 

                
                /* * ****Single insert in buer quote table******** */
                $buyerquote = new RelocationBuyerPost();
                $buyerquote->lkp_service_id = RELOCATION_DOMESTIC;
                $buyerquote->lkp_lead_type_id = FTL_SPOT;
                $buyerquote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
                $buyerquote->transaction_id = $trans_randid;
                $buyerquote->buyer_id = Auth::id();
                $buyerquote->created_by = Auth::id();
                $buyerquote->created_at = $created_at;
                $buyerquote->created_ip = $createdIp;

                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                    $post   =   CommonComponent::getPtlSellerPostDetails($_REQUEST['postItemId']);
                }
                $buyerquote->dispatch_date = CommonComponent::convertDateForDatabase(Session::get('session_dispatch_date_buyer'));
                if(Session::get('session_delivery_date_buyer')!='')
                    $buyerquote->delivery_date = CommonComponent::convertDateForDatabase(Session::get('session_delivery_date_buyer'));
                else{
                    if(isset($post->transitdays)){
                        $dispath    =   date('Y-m-d',strtotime( $buyerquote->dispatch_date) + ($post->transitdays*24*3600));
                        $buyerquote->delivery_date = CommonComponent::convertDateForDatabase($dispath);
                    }
                }
                $buyerquote->from_location_id = Session::get('searchMod.from_city_id_buyer');
                $buyerquote->to_location_id = Session::get('searchMod.to_city_id_buyer');
                $buyerquote->lkp_post_ratecard_type_id = Session::get('searchMod.rate_card_type');
                $buyerquote->lkp_property_type_id = Session::get('searchMod.property_type');
                $buyerquote->lkp_load_category_id = Session::get('searchMod.load_type');
                $buyerquote->lkp_vehicle_category_id = Session::get('searchMod.vehicle_category');
                $buyerquote->lkp_vehicle_category_type_id = Session::get('searchMod.vehicle_category_type');
                $buyerquote->vehicle_model = Session::get('searchMod.vehicle_model');
                $buyerquote->lkp_post_status_id = ORDERED;
                
                if(Session::has('searchMod.elevator1')){
                $buyerquote->origin_elevator = Session::get('searchMod.elevator1');
                }
                if(Session::has('searchMod.elevator2')){
                $buyerquote->destination_elevator = Session::get('searchMod.elevator2');
                }
                if(Session::has('searchMod.origin_storage_serivce')){
                $buyerquote->origin_storage = Session::get('searchMod.origin_storage_serivce');
                }
                if(Session::has('searchMod.destination_storage_serivce')){
                $buyerquote->origin_destination = Session::get('searchMod.destination_storage_serivce');
                }
                if(Session::has('searchMod.origin_handy_serivce')){
                $buyerquote->origin_handyman_services = Session::get('searchMod.origin_handy_serivce');
                }
                if(Session::has('searchMod.destination_handy_serivce')){
                $buyerquote->destination_handyman_services = Session::get('searchMod.destination_handy_serivce');
                }
                if(Session::has('searchMod.insurance_serivce')){
                	$buyerquote->insurance = Session::get('searchMod.insurance_serivce');
                }
                if(Session::has('searchMod.escort_serivce')){
                	$buyerquote->escort = Session::get('searchMod.escort_serivce');
                }
                if(Session::has('searchMod.mobilty_serivce')){
                	$buyerquote->mobility = Session::get('searchMod.mobilty_serivce');
                }
                if(Session::has('searchMod.property_serivce')){
                	$buyerquote->property = Session::get('searchMod.property_serivce');
                }
                if(Session::has('searchMod.setting_serivce')){
                	$buyerquote->setting_service = Session::get('searchMod.setting_serivce');
                }
                if(Session::has('searchMod.insurance_domestic')){
                	$buyerquote->insurance_domestic = Session::get('searchMod.insurance_domestic');
                }
                
                
                //$buyerquote->is_commercial = Session::get ( 'session_is_commercial_date_buyer' );
                
                if ($buyerquote->save()) {
                    //Maintaining a log of data for buyer new quote creation
                	if(Session::has('masterBedRoom')){
                	
                		$particulars=CommonComponent::getParticularsByRoomId(1);
                		$created_at = date ( 'Y-m-d H:i:s' );
                		$createdIp = $_SERVER ['REMOTE_ADDR'];
                	
                	
                		$masterbedroom=array();
                		$masterbedroom=Session::get('masterBedRoom');
                	
                		foreach($particulars as $particular){
                			$buyerpost_inventory = new RelocationBuyerPostInventoryParticular();
                			$particulardata=$masterbedroom['number_items_'.$particular->id];
                			$particularcrating=$masterbedroom['crating_'.$particular->id];
                			//$particulardata=Session::get($particulardata);
                			//$particularcrating=Session::get($particularcrating);
                			//echo $particulardata;
                			if($particulardata!=""){
                				 
                				$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
                				$buyerpost_inventory->buyer_post_id=$buyerquote->id;
                				$buyerpost_inventory->lkp_inventory_room_id=1;
                				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                				$buyerpost_inventory->number_of_items=$particulardata;
                				$buyerpost_inventory->crating_required=$particularcrating;
                				$buyerpost_inventory->created_at=$created_at;
                				$buyerpost_inventory->created_ip=$createdIp;
                				$buyerpost_inventory->created_by=Auth::id ();
                				$buyerpost_inventory->save ();
                	
                			}
                			 
                			 
                		}
                		 
                	
                	}
                	 
                	 
                	if(Session::has('masterBedRoom1')){
                	
                		$particulars=CommonComponent::getParticularsByRoomId(2);
                		$created_at = date ( 'Y-m-d H:i:s' );
                		$createdIp = $_SERVER ['REMOTE_ADDR'];
                	
                		$masterbedroom1=array();
                		$masterbedroom1=Session::get('masterBedRoom1');
                		foreach($particulars as $particular){
                			$buyerpost_inventory = new RelocationBuyerPostInventoryParticular();
                			$particulardata=$masterbedroom1['number_items_'.$particular->id];
                			$particularcrating=$masterbedroom1['crating_'.$particular->id];
                			if($particulardata!=""){
                				//echo "hello";
                				$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
                				$buyerpost_inventory->buyer_post_id=$buyerquote->id;
                				$buyerpost_inventory->lkp_inventory_room_id=2;
                				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                				$buyerpost_inventory->number_of_items=$particulardata;
                				$buyerpost_inventory->crating_required=$particularcrating;
                				$buyerpost_inventory->created_at=$created_at;
                				$buyerpost_inventory->created_ip=$createdIp;
                				$buyerpost_inventory->created_by=Auth::id ();
                				$buyerpost_inventory->save ();
                			}
                	
                		}
                	
                		 
                	}
                	 
                	if(Session::has('masterBedRoom2')){
                		 
                		$particulars=CommonComponent::getParticularsByRoomId(3);
                		$created_at = date ( 'Y-m-d H:i:s' );
                		$createdIp = $_SERVER ['REMOTE_ADDR'];
                	
                		$masterbedroom2=array();
                		$masterbedroom2=Session::get('masterBedRoom2');
                		foreach($particulars as $particular){
                			$buyerpost_inventory = new RelocationBuyerPostInventoryParticular();
                			$particulardata=$masterbedroom2['number_items_'.$particular->id];
                			$particularcrating=$masterbedroom2['crating_'.$particular->id];
                			if($particulardata!=""){
                				$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
                				$buyerpost_inventory->buyer_post_id=$buyerquote->id;
                				$buyerpost_inventory->lkp_inventory_room_id=3;
                				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                				$buyerpost_inventory->number_of_items= $particulardata;
                				$buyerpost_inventory->crating_required= $particularcrating;
                				$buyerpost_inventory->created_at=$created_at;
                				$buyerpost_inventory->created_ip=$createdIp;
                				$buyerpost_inventory->created_by=Auth::id ();
                				$buyerpost_inventory->save ();
                			}
                			 
                			 
                		}
                		 
                		 
                	}
                	if(Session::has('masterBedRoom3')){
                		 
                		$particulars=CommonComponent::getParticularsByRoomId(4);
                		$created_at = date ( 'Y-m-d H:i:s' );
                		$createdIp = $_SERVER ['REMOTE_ADDR'];
                	
                		$masterbedroom3=array();
                		$masterbedroom3=Session::get('masterBedRoom3');
                		foreach($particulars as $particular){
                			$buyerpost_inventory = new RelocationBuyerPostInventoryParticular();
                			$particulardata=$masterbedroom3['number_items_'.$particular->id];
                			$particularcrating=$masterbedroom3['crating_'.$particular->id];
                			if($particulardata!=""){
                				$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
                				$buyerpost_inventory->buyer_post_id=$buyerquote->id;
                				$buyerpost_inventory->lkp_inventory_room_id=4;
                				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                				$buyerpost_inventory->number_of_items=$particulardata;
                				$buyerpost_inventory->crating_required=$particularcrating;
                				$buyerpost_inventory->created_at=$created_at;
                				$buyerpost_inventory->created_ip=$createdIp;
                				$buyerpost_inventory->created_by=Auth::id ();
                				$buyerpost_inventory->save ();
                				 
                			}
                	
                		}
                		 
                		 
                	}
                	 
                	if(Session::has('lobby')){
                		 
                		$particulars=CommonComponent::getParticularsByRoomId(5);
                		$created_at = date ( 'Y-m-d H:i:s' );
                		$createdIp = $_SERVER ['REMOTE_ADDR'];
                	
                		$lobby=array();
                		$lobby=Session::get('lobby');
                		foreach($particulars as $particular){
                			$buyerpost_inventory = new RelocationBuyerPostInventoryParticular();
                			$particulardata=$lobby['number_items_'.$particular->id];
                			$particularcrating=$lobby['crating_'.$particular->id];
                			if($particulardata!=""){
                				$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
                				$buyerpost_inventory->buyer_post_id=$buyerquote->id;
                				$buyerpost_inventory->lkp_inventory_room_id=5;
                				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                				$buyerpost_inventory->number_of_items= $particulardata;
                				$buyerpost_inventory->crating_required= $particularcrating;
                				$buyerpost_inventory->created_at=$created_at;
                				$buyerpost_inventory->created_ip=$createdIp;
                				$buyerpost_inventory->created_by=Auth::id ();
                				$buyerpost_inventory->save ();
                			}
                			 
                		}
                		 
                		 
                	}
                	 
                	if(Session::get('kitchen')){
                		 
                		$particulars=CommonComponent::getParticularsByRoomId(6);
                		$created_at = date ( 'Y-m-d H:i:s' );
                		$createdIp = $_SERVER ['REMOTE_ADDR'];
                	
                		$kitchen=array();
                		$kitchen=Session::get('kitchen');
                		foreach($particulars as $particular){
                			$buyerpost_inventory = new RelocationBuyerPostInventoryParticular();
                			$particulardata=$kitchen['number_items_'.$particular->id];
                			$particularcrating=$kitchen['crating_'.$particular->id];
                			if($particulardata!=""){
                				$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
                				$buyerpost_inventory->buyer_post_id=$buyerquote->id;
                				$buyerpost_inventory->lkp_inventory_room_id=6;
                				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                				$buyerpost_inventory->number_of_items=$particulardata;
                				$buyerpost_inventory->crating_required=$particularcrating;
                				$buyerpost_inventory->created_at=$created_at;
                				$buyerpost_inventory->created_ip=$createdIp;
                				$buyerpost_inventory->created_by=Auth::id ();
                				$buyerpost_inventory->save ();
                			}
                	
                		}
                		 
                		 
                	}
                	 
                	if(Session::has('bathroom')){
                		 
                		$particulars=CommonComponent::getParticularsByRoomId(7);
                		$created_at = date ( 'Y-m-d H:i:s' );
                		$createdIp = $_SERVER ['REMOTE_ADDR'];
                	
                		$bathroom=array();
                		$bathroom=Session::get('bathroom');
                		foreach($particulars as $particular){
                			$buyerpost_inventory = new RelocationBuyerPostInventoryParticular();
                			$particulardata=$bathroom['number_items_'.$particular->id];
                			$particularcrating=$bathroom['crating_'.$particular->id];
                			if($particulardata!=""){
                				$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
                				$buyerpost_inventory->buyer_post_id=$buyerquote->id;
                				$buyerpost_inventory->lkp_inventory_room_id=7;
                				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                				$buyerpost_inventory->number_of_items=$particulardata;
                				$buyerpost_inventory->crating_required=$particularcrating;
                				$buyerpost_inventory->created_at=$created_at;
                				$buyerpost_inventory->created_ip=$createdIp;
                				$buyerpost_inventory->created_by=Auth::id ();
                				$buyerpost_inventory->save ();
                				 
                			}
                			 
                		}
                		 
                		 
                	}
                	 
                	if(Session::has('living')){
                		 
                		$particulars=CommonComponent::getParticularsByRoomId(8);
                		$created_at = date ( 'Y-m-d H:i:s' );
                		$createdIp = $_SERVER ['REMOTE_ADDR'];
                	
                		$living=array();
                		$living=Session::get('living');
                		foreach($particulars as $particular){
                			$buyerpost_inventory = new RelocationBuyerPostInventoryParticular();
                			$particulardata=$living['number_items_'.$particular->id];
                			$particularcrating=$living['crating_'.$particular->id];
                			if($particulardata!=""){
                				$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
                				$buyerpost_inventory->buyer_post_id=$buyerquote->id;
                				$buyerpost_inventory->lkp_inventory_room_id=8;
                				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                				$buyerpost_inventory->number_of_items=$particulardata;
                				$buyerpost_inventory->crating_required=$particularcrating;
                				$buyerpost_inventory->created_at=$created_at;
                				$buyerpost_inventory->created_ip=$createdIp;
                				$buyerpost_inventory->created_by=Auth::id ();
                				$buyerpost_inventory->save ();
                				 
                			}
                	
                		}
                		 
                		 
                	}
                    CommonComponent::auditLog($buyerquote->id, 'relocation_buyer_posts');
                    return $buyerquote->id;
                }  
                break;  
            case RELOCATION_INTERNATIONAL :
                $cartons    =   CommonComponent::getCartons();
                $tot=0;$searchrequest=Session::get('relocbuyerrequest');
                $ordid  =   CommonComponent::getPostID(RELOCATION_INTERNATIONAL);
                $created_year = date('Y');
                $trans_randid = 'REL-INTR/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 

                
                /* * ****Single insert in buer quote table******** */
                $buyerquote = new RelocationintBuyerPost();
                $buyerquote->lkp_service_id = RELOCATION_INTERNATIONAL;
                $buyerquote->lkp_international_type_id = Session::get('searchMod.service_type_buyer');               
                $buyerquote->lkp_lead_type_id = FTL_SPOT;
                $buyerquote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
                $buyerquote->transaction_id = $trans_randid;
                
                //$buyerquote->is_commercial = Session::get ( 'session_is_commercial_date_buyer' );
                $buyerquote->buyer_id = Auth::id();
                $buyerquote->created_by = Auth::id();
                $buyerquote->created_at = $created_at;
                $buyerquote->created_ip = $createdIp;
                

                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                    $post   =   CommonComponent::getPtlSellerPostDetails($_REQUEST['postItemId']);
                }
                $buyerquote->dispatch_date = CommonComponent::convertDateForDatabase(Session::get('searchMod.dispatch_date_buyer'));
                if(Session::get('searchMod.delivery_date_buyer')!='')
                    $buyerquote->delivery_date = CommonComponent::convertDateForDatabase(Session::get('searchMod.delivery_date_buyer'));
                else{
                    if(isset($post->transitdays)){
                        $dispath    =   date('Y-m-d',strtotime( $buyerquote->dispatch_date) + ($post->transitdays*24*3600));
                        $buyerquote->delivery_date = CommonComponent::convertDateForDatabase($dispath);
                    }
                }
                $buyerquote->from_location_id = Session::get('searchMod.from_city_id_buyer');
                $buyerquote->to_location_id = Session::get('searchMod.to_city_id_buyer');
                $buyerquote->lkp_post_status_id = ORDERED;
                if(Session::get('searchMod.service_type_buyer')==1){
                        for($i = 1; $i <= count($cartons); $i++){  
                            if($searchrequest['cartons_'.$i]!=""){
                                $tot+=$cartons[$i-1]->weight*Session::get('searchMod.cartons_'.$i);
                                }
                        }
                    $buyerquote->total_cartons_weight =   $tot; 
                    
                }
                if(Session::get('searchMod.service_type_buyer')==2){
                
                	$buyerquote->lkp_property_type_id  = Session::get('searchMod.property_type_buyer');
                
                	if(Session::get('searchMod.chkOrgServ')!=''){
                		$buyerPostCreate->origin_storage = 1;
                	}
                	if(Session::get('searchMod.origin_handy_serivce')!=''){
                		$buyerPostCreate->origin_handyman_services = 1;
                	}
                	if(Session::get('searchMod.insurance_serivce')!=''){
                		$buyerPostCreate->insurance = 1;
                	}
                
                	if(Session::get('searchMod.destination_storage_serivce')!=''){
                		$buyerPostCreate->destination_storage = 1;
                	}
                	if(Session::get('searchMod.destination_handy_serivce')!=''){
                		$buyerPostCreate->destination_handyman_services = 1;
                	}
                
                }
                if ($buyerquote->save()) {
                    if(Session::get('searchMod.service_type_buyer')==1){
                        
                        for($i = 1; $i <= count($cartons); $i++){  

                            if($searchrequest['cartons_'.$i]!=""){
                                $petBuyerSelSeller = new \App\Models\RelocationintBuyerPostAirCarton();   
                                $petBuyerSelSeller->lkp_service_id=RELOCATION_INTERNATIONAL;
                                $petBuyerSelSeller->buyer_post_id      = $buyerquote->id;
                                $petBuyerSelSeller->lkp_air_carton_type_id      = $i;
                                $petBuyerSelSeller->number_of_cartons      = Session::get('searchMod.cartons_'.$i);
                                $petBuyerSelSeller->created_by      = Auth::id();
                                $petBuyerSelSeller->created_ip      = $createdIp;
                                $petBuyerSelSeller->created_at      = $created_at;
                                $petBuyerSelSeller->save (); 
                            }
                        }
                    }
                    
                    if(Session::get('searchMod.service_type_buyer')==2){
                    	
                    	if(Session::has('masterBedRoom')){
                    	
                    		$particulars=CommonComponent::getParticularsByRoomId(1);
                    		$created_at = date ( 'Y-m-d H:i:s' );
                    		$createdIp = $_SERVER ['REMOTE_ADDR'];
                    		$masterbedroom=array();
                    		$masterbedroom=Session::get('masterBedRoom');
                    		foreach($particulars as $particular){
                    			$buyerpost_inventory = new RelocationintBuyerPostInventoryParticular();
                    			$particulardata=$masterbedroom['number_items_'.$particular->id];
                    			$particularcrating=$masterbedroom['crating_'.$particular->id];
                    			if($particulardata!=""){
                    				$buyerpost_inventory->lkp_service_id=RELOCATION_INTERNATIONAL;
                    				$buyerpost_inventory->buyer_post_id=$buyerPostCreate->id;
                    				$buyerpost_inventory->lkp_inventory_room_id=1;
                    				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                    				$buyerpost_inventory->number_of_items=$particulardata;
                    				$buyerpost_inventory->crating_required=$particularcrating;
                    				$buyerpost_inventory->created_at=$created_at;
                    				$buyerpost_inventory->created_ip=$createdIp;
                    				$buyerpost_inventory->created_by=Auth::id ();
                    				$buyerpost_inventory->save ();
                    			}
                    		}
                    	}
                    	 
                    	if(Session::has('masterBedRoom1')){
                    	
                    		$particulars=CommonComponent::getParticularsByRoomId(2);
                    		$created_at = date ( 'Y-m-d H:i:s' );
                    		$createdIp = $_SERVER ['REMOTE_ADDR'];
                    		$masterbedroom1=array();
                    		$masterbedroom1=Session::get('masterBedRoom1');
                    		foreach($particulars as $particular){
                    			$buyerpost_inventory = new RelocationintBuyerPostInventoryParticular();
                    			$particulardata=$masterbedroom1['number_items_'.$particular->id];
                    			$particularcrating=$masterbedroom1['crating_'.$particular->id];
                    			if($particulardata!=""){
                    				$buyerpost_inventory->lkp_service_id=RELOCATION_INTERNATIONAL;
                    				$buyerpost_inventory->buyer_post_id=$buyerPostCreate->id;
                    				$buyerpost_inventory->lkp_inventory_room_id=2;
                    				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                    				$buyerpost_inventory->number_of_items=$particulardata;
                    				$buyerpost_inventory->crating_required=$particularcrating;
                    				$buyerpost_inventory->created_at=$created_at;
                    				$buyerpost_inventory->created_ip=$createdIp;
                    				$buyerpost_inventory->created_by=Auth::id ();
                    				$buyerpost_inventory->save ();
                    			}
                    		}
                    	}
                    	 
                    	if(Session::has('masterBedRoom2')){
                    		 
                    		$particulars=CommonComponent::getParticularsByRoomId(3);
                    		$created_at = date ( 'Y-m-d H:i:s' );
                    		$createdIp = $_SERVER ['REMOTE_ADDR'];
                    	
                    		$masterbedroom2=array();
                    		$masterbedroom2=Session::get('masterBedRoom2');
                    		foreach($particulars as $particular){
                    			$buyerpost_inventory = new RelocationintBuyerPostInventoryParticular();
                    			$particulardata=$masterbedroom2['number_items_'.$particular->id];
                    			$particularcrating=$masterbedroom2['crating_'.$particular->id];
                    			if($particulardata!=""){
                    				$buyerpost_inventory->lkp_service_id=RELOCATION_INTERNATIONAL;
                    				$buyerpost_inventory->buyer_post_id=$buyerPostCreate->id;
                    				$buyerpost_inventory->lkp_inventory_room_id=3;
                    				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                    				$buyerpost_inventory->number_of_items= $particulardata;
                    				$buyerpost_inventory->crating_required= $particularcrating;
                    				$buyerpost_inventory->created_at=$created_at;
                    				$buyerpost_inventory->created_ip=$createdIp;
                    				$buyerpost_inventory->created_by=Auth::id ();
                    				$buyerpost_inventory->save ();
                    			}
                    		}
                    	}
                    	
                    	if(Session::has('masterBedRoom3')){
                    		 
                    		$particulars=CommonComponent::getParticularsByRoomId(4);
                    		$created_at = date ( 'Y-m-d H:i:s' );
                    		$createdIp = $_SERVER ['REMOTE_ADDR'];
                    		$masterbedroom3=array();
                    		$masterbedroom3=Session::get('masterBedRoom3');
                    		foreach($particulars as $particular){
                    			$buyerpost_inventory = new RelocationintBuyerPostInventoryParticular();
                    			$particulardata=$masterbedroom3['number_items_'.$particular->id];
                    			$particularcrating=$masterbedroom3['crating_'.$particular->id];
                    			if($particulardata!=""){
                    				$buyerpost_inventory->lkp_service_id=RELOCATION_INTERNATIONAL;
                    				$buyerpost_inventory->buyer_post_id=$buyerPostCreate->id;
                    				$buyerpost_inventory->lkp_inventory_room_id=4;
                    				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                    				$buyerpost_inventory->number_of_items=$particulardata;
                    				$buyerpost_inventory->crating_required=$particularcrating;
                    				$buyerpost_inventory->created_at=$created_at;
                    				$buyerpost_inventory->created_ip=$createdIp;
                    				$buyerpost_inventory->created_by=Auth::id ();
                    				$buyerpost_inventory->save ();
                    			}
                    		}
                    	}
                    	 
                    	if(Session::has('lobby')){
                    		 
                    		$particulars=CommonComponent::getParticularsByRoomId(5);
                    		$created_at = date ( 'Y-m-d H:i:s' );
                    		$createdIp = $_SERVER ['REMOTE_ADDR'];
                    		$lobby=array();
                    		$lobby=Session::get('lobby');
                    		foreach($particulars as $particular){
                    			$buyerpost_inventory = new RelocationintBuyerPostInventoryParticular();
                    			$particulardata=$lobby['number_items_'.$particular->id];
                    			$particularcrating=$lobby['crating_'.$particular->id];
                    			if($particulardata!=""){
                    				$buyerpost_inventory->lkp_service_id=RELOCATION_INTERNATIONAL;
                    				$buyerpost_inventory->buyer_post_id=$buyerPostCreate->id;
                    				$buyerpost_inventory->lkp_inventory_room_id=5;
                    				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                    				$buyerpost_inventory->number_of_items= $particulardata;
                    				$buyerpost_inventory->crating_required= $particularcrating;
                    				$buyerpost_inventory->created_at=$created_at;
                    				$buyerpost_inventory->created_ip=$createdIp;
                    				$buyerpost_inventory->created_by=Auth::id ();
                    				$buyerpost_inventory->save ();
                    			}
                    		}
                    	}
                    	 
                    	if(Session::get('kitchen')){
                    		 
                    		$particulars=CommonComponent::getParticularsByRoomId(6);
                    		$created_at = date ( 'Y-m-d H:i:s' );
                    		$createdIp = $_SERVER ['REMOTE_ADDR'];
                    		$kitchen=array();
                    		$kitchen=Session::get('kitchen');
                    		foreach($particulars as $particular){
                    			$buyerpost_inventory = new RelocationintBuyerPostInventoryParticular();
                    			$particulardata=$kitchen['number_items_'.$particular->id];
                    			$particularcrating=$kitchen['crating_'.$particular->id];
                    			if($particulardata!=""){
                    				$buyerpost_inventory->lkp_service_id=RELOCATION_INTERNATIONAL;
                    				$buyerpost_inventory->buyer_post_id=$buyerPostCreate->id;
                    				$buyerpost_inventory->lkp_inventory_room_id=6;
                    				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                    				$buyerpost_inventory->number_of_items=$particulardata;
                    				$buyerpost_inventory->crating_required=$particularcrating;
                    				$buyerpost_inventory->created_at=$created_at;
                    				$buyerpost_inventory->created_ip=$createdIp;
                    				$buyerpost_inventory->created_by=Auth::id ();
                    				$buyerpost_inventory->save ();
                    			}
                    		}
                    	}
                    	 
                    	if(Session::has('bathroom')){
                    		 
                    		$particulars=CommonComponent::getParticularsByRoomId(7);
                    		$created_at = date ( 'Y-m-d H:i:s' );
                    		$createdIp = $_SERVER ['REMOTE_ADDR'];
                    		$bathroom=array();
                    		$bathroom=Session::get('bathroom');
                    		foreach($particulars as $particular){
                    			$buyerpost_inventory = new RelocationintBuyerPostInventoryParticular();
                    			$particulardata=$bathroom['number_items_'.$particular->id];
                    			$particularcrating=$bathroom['crating_'.$particular->id];
                    			if($particulardata!=""){
                    				$buyerpost_inventory->lkp_service_id=RELOCATION_INTERNATIONAL;
                    				$buyerpost_inventory->buyer_post_id=$buyerPostCreate->id;
                    				$buyerpost_inventory->lkp_inventory_room_id=7;
                    				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                    				$buyerpost_inventory->number_of_items=$particulardata;
                    				$buyerpost_inventory->crating_required=$particularcrating;
                    				$buyerpost_inventory->created_at=$created_at;
                    				$buyerpost_inventory->created_ip=$createdIp;
                    				$buyerpost_inventory->created_by=Auth::id ();
                    				$buyerpost_inventory->save ();
                    			}
                    		}
                    	}
                    	 
                    	if(Session::has('living')){
                    		 
                    		$particulars=CommonComponent::getParticularsByRoomId(8);
                    		$created_at = date ( 'Y-m-d H:i:s' );
                    		$createdIp = $_SERVER ['REMOTE_ADDR'];
                    		$living=array();
                    		$living=Session::get('living');
                    		foreach($particulars as $particular){
                    			$buyerpost_inventory = new RelocationintBuyerPostInventoryParticular();
                    			$particulardata=$living['number_items_'.$particular->id];
                    			$particularcrating=$living['crating_'.$particular->id];
                    			if($particulardata!=""){
                    				$buyerpost_inventory->lkp_service_id=RELOCATION_INTERNATIONAL;
                    				$buyerpost_inventory->buyer_post_id=$buyerPostCreate->id;
                    				$buyerpost_inventory->lkp_inventory_room_id=8;
                    				$buyerpost_inventory->lkp_inventory_room_particular_id=$particular->id;
                    				$buyerpost_inventory->number_of_items=$particulardata;
                    				$buyerpost_inventory->crating_required=$particularcrating;
                    				$buyerpost_inventory->created_at=$created_at;
                    				$buyerpost_inventory->created_ip=$createdIp;
                    				$buyerpost_inventory->created_by=Auth::id ();
                    				$buyerpost_inventory->save ();
                    			}
                    		}
                    		 
                    	}
                    }
                    
                    //Maintaining a log of data for buyer new quote creation
                    CommonComponent::auditLog($buyerquote->id, 'relocationint_buyer_posts');
                    return $buyerquote->id;
                }
                break; 
            case RELOCATION_OFFICE_MOVE :
                $ordid  =   CommonComponent::getPostID(RELOCATION_OFFICE_MOVE);
                $created_year = date('Y');
                $trans_randid = 'RD/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 

                
                /* * ****Single insert in buer quote table******** */
                $buyerquote = new RelocationOfficeBuyerPost();
                $buyerquote->lkp_service_id = RELOCATION_OFFICE_MOVE;
                $buyerquote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
                $buyerquote->transaction_id = $trans_randid;
                //$buyerquote->is_commercial = Session::get ( 'session_is_commercial_date_buyer' );
                $buyerquote->buyer_id = Auth::id();
                $buyerquote->created_by = Auth::id();
                $buyerquote->created_at = $created_at;
                $buyerquote->created_ip = $createdIp;

                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                    $post   =   CommonComponent::getPtlSellerPostDetails($_REQUEST['postItemId']);
                }
                $buyerquote->dispatch_date = CommonComponent::convertDateForDatabase(Session::get('searchMod.dispatch_date_buyer'));
                if(Session::get('searchMod.delivery_date_buyer')!='')
                    $buyerquote->delivery_date = CommonComponent::convertDateForDatabase(Session::get('searchMod.delivery_date_buyer'));
                else{
                    if(isset($post->transitdays)){
                        $dispath    =   date('Y-m-d',strtotime( $buyerquote->dispatch_date) + ($post->transitdays*24*3600));
                        $buyerquote->delivery_date = CommonComponent::convertDateForDatabase($dispath);
                    }
                }
                $buyerquote->from_location_id = Session::get('searchMod.from_city_id_buyer');
                $buyerquote->lkp_post_status_id = ORDERED;
                if ($buyerquote->save()) {
                    //Maintaining a log of data for buyer new quote creation
                    CommonComponent::auditLog($buyerquote->id, 'relocation_buyer_posts');
                    return $buyerquote->id;
                }   
                break; 
            case RELOCATION_PET_MOVE :
                $ordid  =   CommonComponent::getPostID(RELOCATION_PET_MOVE);
                $created_year = date('Y');
                $trans_randid = 'RP/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
                //$searchrequest=Session::get('relocbuyerrequest');
                
                /* * ****Single insert in buer quote table******** */
                $buyerquote = new RelocationPetBuyerPost();
                $buyerquote->lkp_service_id = RELOCATION_PET_MOVE;
                $buyerquote->lkp_lead_type_id = FTL_SPOT;
                $buyerquote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
                $buyerquote->transaction_id = $trans_randid;
                //$buyerquote->is_commercial = Session::get ( 'session_is_commercial_date_buyer' );
                $buyerquote->buyer_id = Auth::id();
                $buyerquote->created_by = Auth::id();
                $buyerquote->created_at = $created_at;
                $buyerquote->created_ip = $createdIp;

                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                    $post   =   CommonComponent::getPtlSellerPostDetails($_REQUEST['postItemId']);
                }
                $buyerquote->dispatch_date = CommonComponent::convertDateForDatabase(Session::get('searchMod.dispatch_date_buyer'));
                if(Session::get('searchMod.delivery_date_buyer')!='')
                    $buyerquote->delivery_date = CommonComponent::convertDateForDatabase(Session::get('searchMod.delivery_date_buyer'));
                else{
                    if(isset($post->transitdays)){
                        $dispath    =   date('Y-m-d',strtotime( $buyerquote->dispatch_date) + ($post->transitdays*24*3600));
                        $buyerquote->delivery_date = CommonComponent::convertDateForDatabase($dispath);
                    }
                }
                $buyerquote->from_location_id = Session::get('searchMod.from_city_id_buyer');
                $buyerquote->to_location_id = Session::get('searchMod.to_city_id_buyer');
                //$buyerquote->lkp_post_ratecard_type_id = Session::get('session_property_type');
                $buyerquote->lkp_pet_type_id = Session::get('searchMod.pet_type_reslocation');
                $buyerquote->lkp_breed_type_id = Session::get('searchMod.breed_type_reslocation');
                $buyerquote->lkp_cage_type_id = Session::get('searchMod.cage_type_reslocation');
                
                $buyerquote->lkp_post_status_id = ORDERED;
                
                if ($buyerquote->save()) {
                    //Maintaining a log of data for buyer new quote creation
                    CommonComponent::auditLog($buyerquote->id, 'relocationpet_buyer_posts');
                    return $buyerquote->id;
                } 
                break;    
            case ROAD_TRUCK_HAUL       : 
                $ordid  =   CommonComponent::getPostID(ROAD_TRUCK_HAUL);
            $created_year = date('Y');
            $trans_randid = 'TRUCKHAUL/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
               
            /* * ****Single insert in buer quote table******** */
            $buyerquote = new TruckhaulBuyerQuote();
            $buyerquote->lkp_service_id = ROAD_TRUCK_HAUL;
            $buyerquote->lkp_lead_type_id = FTL_SPOT;
            $buyerquote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
            $buyerquote->transaction_id = $trans_randid;
            $buyerquote->is_commercial = Session::get ( 'searchMod.is_commercial_date_buyer' );
            $buyerquote->buyer_id = Auth::id();
            $buyerquote->created_by = Auth::id();
            $buyerquote->created_at = $created_at;
            $buyerquote->created_ip = $createdIp;

            if ($buyerquote->save()) {
                //Maintaining a log of data for buyer new quote creation
                CommonComponent::auditLog($buyerquote->id, 'truckhaul_buyer_quotes');
                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                    $post   =   CommonComponent::getTHSellerPostDetails($_REQUEST['postItemId']);
                }
                /* * ****Multiple insert in quote items******** */
                $Quote_Lineitems = new TruckhaulBuyerQuoteItem();
                $Quote_Lineitems->buyer_quote_id = $buyerquote->id;
                $Quote_Lineitems->dispatch_date = CommonComponent::convertDateForDatabase(Session::get('searchMod.dispatch_date_buyer'));
                
                $Quote_Lineitems->lkp_quote_price_type_id = FIRM;
                $Quote_Lineitems->from_city_id = Session::get('searchMod.from_city_id_buyer');
                $Quote_Lineitems->to_city_id = Session::get('searchMod.to_city_id_buyer');
                $Quote_Lineitems->lkp_load_type_id = Session::get('searchMod.load_type_buyer');
                $Quote_Lineitems->lkp_vehicle_type_id = $post->lkp_vehicle_type_id;
                $Quote_Lineitems->units = Session::get('searchMod.capacity_buyer');
                
                $vehicle_type = $post->lkp_vehicle_type_id;
                
                $Quote_Lineitems->number_loads = 1;
                //number of loads calculation end 
                
                $Quote_Lineitems->quantity = Session::get('searchMod.quantity_buyer');
                if(isset($post->price))
                $Quote_Lineitems->price = $post->price;
                $Quote_Lineitems->lkp_post_status_id = ORDERED;
                $Quote_Lineitems->created_by = Auth::id();
                $Quote_Lineitems->created_at = $created_at;
                $Quote_Lineitems->created_ip = $createdIp;
                $Quote_Lineitems->save();
                //Maintaining a log of data for buyer new quote multiple items creation
                CommonComponent::auditLog($Quote_Lineitems->id, 'truckhaul_buyer_quote_items');
                return $Quote_Lineitems->id; break;
            }
               
                
        case ROAD_TRUCK_LEASE :
            $ordid  =   CommonComponent::getPostID(ROAD_TRUCK_LEASE);
            $created_year = date('Y');
            $trans_randid = 'TRUCKLEASE/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 
               
            /* * ****Single insert in buer quote table******** */
            $buyerquote = new TruckleaseBuyerQuote();
            $buyerquote->lkp_service_id = ROAD_TRUCK_LEASE;
            $buyerquote->lkp_lead_type_id = FTL_SPOT;
            $buyerquote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
            $buyerquote->transaction_id = $trans_randid;
            $buyerquote->is_commercial = Session::get ( 'searchMod.is_commercial_date_buyer' );
            $buyerquote->buyer_id = Auth::id();
            $buyerquote->created_by = Auth::id();
            $buyerquote->created_at = $created_at;
            $buyerquote->created_ip = $createdIp;

            if ($buyerquote->save()) {
                //Maintaining a log of data for buyer new quote creation
                //CommonComponent::auditLog($buyerquote->id, 'trucklease_buyer_quotes');
                if (isset($_REQUEST['postItemId']) && !empty($_REQUEST['postItemId']) ) {
                    $post   =   CommonComponent::getTLSellerPostDetails($_REQUEST['postItemId']);
                }
                /* * ****Multiple insert in quote items******** */
                $Quote_Lineitems = new TruckleaseBuyerQuoteItem();
                $Quote_Lineitems->buyer_quote_id = $buyerquote->id;
                $Quote_Lineitems->from_date = CommonComponent::convertDateForDatabase(Session::get('searchMod.dispatch_date_buyer'));
                $Quote_Lineitems->to_date = CommonComponent::convertDateForDatabase(Session::get('searchMod.delivery_date_buyer'));
                
                $Quote_Lineitems->lkp_quote_price_type_id = FIRM;
                $Quote_Lineitems->from_city_id = Session::get('searchMod.from_city_id_buyer');
                $Quote_Lineitems->lkp_vehicle_type_id = $post->lkp_vehicle_type_id;
                
                $Quote_Lineitems->lkp_trucklease_lease_term_id = Session::get('searchMod.lease_term_buyer');
                $Quote_Lineitems->driver_availability = Session::get('searchMod.driver_availability');
                
                if(isset($post->price))
                $Quote_Lineitems->price = $post->price;
                $Quote_Lineitems->lkp_post_status_id = ORDERED;
                $Quote_Lineitems->created_by = Auth::id();
                $Quote_Lineitems->created_at = $created_at;
                $Quote_Lineitems->created_ip = $createdIp;
                $Quote_Lineitems->save();
                //Maintaining a log of data for buyer new quote multiple items creation
                //CommonComponent::auditLog($Quote_Lineitems->id, 'trucklease_buyer_quote_items');
                return $Quote_Lineitems->id;break;

            }
                
        case RELOCATION_GLOBAL_MOBILITY:
                $ordid  =   CommonComponent::getPostID(RELOCATION_GLOBAL_MOBILITY);
                $created_year = date('Y');
                $trans_randid = 'RELOCATIONGM/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT); 

                
                /* * ****Single insert in buer quote table******** */
                $buyerquote = new RelocationgmBuyerPost();
                $buyerquote->lkp_service_id = RELOCATION_GLOBAL_MOBILITY;
                $buyerquote->lkp_quote_access_id = IS_ACCESS_PRIVATE;
                $buyerquote->transaction_id = $trans_randid;
                $buyerquote->buyer_id = Auth::id();
                $buyerquote->created_by = Auth::id();
                $buyerquote->created_at = $created_at;
                $buyerquote->created_ip = $createdIp;
                $buyerquote->dispatch_date = CommonComponent::convertDateForDatabase(Session::get('searchMod.dispatch_date_buyer'));
                $buyerquote->location_id = Session::get('searchMod.to_city_id_buyer');
                $buyerquote->lkp_post_status_id = ORDERED;
                
                if ($buyerquote->save()) {
                    $buyerquoteitem = new RelocationgmBuyerQuoteItems();
                    $buyerquoteitem->lkp_service_id = RELOCATION_GLOBAL_MOBILITY;
                    $buyerquoteitem->buyer_post_id = $buyerquote->id;
                    $buyerquoteitem->lkp_gm_service_id = Session::get('searchMod.service_type_relocation');
                    $buyerquoteitem->measurement = Session::get('searchMod.measurement_relocation');
                    $buyerquoteitem->measurement_units = CommonComponent::getAllGMServiceTypeUnitsById(Session::get('searchMod.service_type_relocation'));
                    $buyerquoteitem->save();
                    //Maintaining a log of data for buyer new quote creation
                    //CommonComponent::auditLog($buyerquote->id, 'relocationgm_buyer_posts');
                    return $buyerquote->id;
                }  
                break;          

            }        
                
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }
    /* cronjob to change all the posts status to close  which are exceeded by dispatch date*/
    public static function updatePostStatus()
    {
        try{    
                
                DB::table('buyer_quote_items as bqi')
                ->where('lkp_post_status_id','=',OPEN)
                                ->whereRaw('dispatch_date<CURDATE()' )
                ->update(array('lkp_post_status_id' =>CLOSED));
                
                DB::table('ptl_buyer_quote_items as bqi')
                                ->join('ptl_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                ->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
                ->update(array(
                        'bq.lkp_post_status_id' =>CLOSED,
                                                'bqi.lkp_post_status_id' =>CLOSED));
                
                DB::table('rail_buyer_quote_items as bqi')
                                ->join('rail_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                ->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
                ->update(array(
                        'bq.lkp_post_status_id' =>CLOSED,
                                                'bqi.lkp_post_status_id' =>CLOSED));
                
                DB::table('airdom_buyer_quote_items as bqi')
                                ->join('airdom_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                ->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
                ->update(array(
                        'bq.lkp_post_status_id' =>CLOSED,
                                                'bqi.lkp_post_status_id' =>CLOSED));
                
                DB::table('airint_buyer_quote_items as bqi')
                                ->join('airint_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                ->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
                ->update(array(
                        'bq.lkp_post_status_id' =>CLOSED,
                                                'bqi.lkp_post_status_id' =>CLOSED));
                
                DB::table('ocean_buyer_quote_items as bqi')
                                ->join('ocean_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                ->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
                ->update(array(
                        'bq.lkp_post_status_id' =>CLOSED,
                                                'bqi.lkp_post_status_id' =>CLOSED));
                
                
                //Relocation buyer posts deleting
                DB::table('relocation_buyer_posts as bq')
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
                ->update(array(
                        'bq.lkp_post_status_id' =>CLOSED));
                
                //courier buyer posts deleting
                DB::table('courier_buyer_quote_items as bqi')
                ->join('courier_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                ->where('bqi.lkp_post_status_id','=',OPEN)
                ->where('bq.lkp_post_status_id','=',OPEN)
                ->whereRaw('bq.dispatch_date<CURDATE()' )
                ->update(array(
                        'bq.lkp_post_status_id' =>CLOSED,
                        'bqi.lkp_post_status_id' =>CLOSED));
                
                //truckhaul buyer posts deleting
                DB::table('truckhaul_buyer_quote_items as bqi')
                            ->join('truckhaul_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                            ->where('bqi.lkp_post_status_id','=',OPEN)
                            ->whereRaw('bqi.dispatch_date<CURDATE()' )
                ->update(array('bqi.lkp_post_status_id' =>CLOSED));
                //truckLease buyer posts deleting
                DB::table('trucklease_buyer_quote_items as bqi')
                            ->join('trucklease_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                            ->where('bqi.lkp_post_status_id','=',OPEN)
                            ->whereRaw('bqi.from_date<CURDATE()' )
                ->update(array('bqi.lkp_post_status_id' =>CLOSED));
                
                //Relocation pet buyer posts deleting
                DB::table('relocationpet_buyer_posts as bq')
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
                ->update(array(
                        'bq.lkp_post_status_id' =>CLOSED));
                //Relocation office buyer posts deleting
                DB::table('relocationoffice_buyer_posts as bq')
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
                ->update(array(
                        'bq.lkp_post_status_id' =>CLOSED));
                //Relocation int buyer posts deleting
                DB::table('relocationint_buyer_posts as bq')
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.dispatch_date<CURDATE()' )
                ->update(array(
                        'bq.lkp_post_status_id' =>CLOSED));
                
                //intracity buyer posts deleting
                DB::table('ict_buyer_quote_items as bqi')
                                ->join('ict_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
                                ->where('bqi.lkp_post_status_id','=',OPEN)
                                ->whereRaw( "CONCAT( 'bqi.pickup_date',' ', 'bqi.pickup_time')>=DATE_FORMAT( (NOW( ) - INTERVAL 1 DAY ) ,  '%Y-%m-%d %H:%i:%s') ")
                ->update(array('bqi.lkp_post_status_id' =>CLOSED));
                
                //ftl seller post deleting
                DB::table('seller_posts')
                                ->join('seller_post_items','seller_posts.id','=','seller_post_items.seller_post_id')
                ->where('seller_posts.lkp_post_status_id','=',OPEN)
                                ->where('seller_post_items.lkp_post_status_id','=',OPEN)
                                ->whereRaw('seller_posts.to_date<CURDATE()')
                ->update(array(
                        'seller_posts.lkp_post_status_id' =>CLOSED,
                                                'seller_post_items.lkp_post_status_id' =>CLOSED));
                //ptl seller post deleting
                DB::table('ptl_seller_post_items as spi')
                                ->join('ptl_seller_posts as sp','sp.id','=','spi.seller_post_id')
                ->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
                ->update(array(
                        'sp.lkp_post_status_id' =>CLOSED,
                                                'spi.lkp_post_status_id' =>CLOSED));
                
                //rail seller post deleting
                DB::table('rail_seller_post_items as spi')
                                ->join('rail_seller_posts as sp','sp.id','=','spi.seller_post_id')
                ->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
                ->update(array(
                        'sp.lkp_post_status_id' =>CLOSED,
                                                'spi.lkp_post_status_id' =>CLOSED));
                //airdom seller post deleting
                DB::table('airdom_seller_post_items as spi')
                                ->join('airdom_seller_posts as sp','sp.id','=','spi.seller_post_id')
                ->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
                ->update(array(
                        'sp.lkp_post_status_id' =>CLOSED,
                                                'spi.lkp_post_status_id' =>CLOSED));
                //airint seller post deleting
                DB::table('airint_seller_post_items as spi')
                                ->join('airint_seller_posts as sp','sp.id','=','spi.seller_post_id')
                ->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
                ->update(array(
                        'sp.lkp_post_status_id' =>CLOSED,
                                                'spi.lkp_post_status_id' =>CLOSED));
                
                //ocean seller post deleting
                DB::table('ocean_seller_post_items as spi')
                                ->join('ocean_seller_posts as sp','sp.id','=','spi.seller_post_id')
                ->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
                ->update(array(
                        'sp.lkp_post_status_id' =>CLOSED,
                                                'spi.lkp_post_status_id' =>CLOSED));
                //relocation seller post deleting
                DB::table('relocation_seller_post_items as spi')
                                ->join('relocation_seller_posts as sp','sp.id','=','spi.seller_post_id')
                                ->where('sp.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
                                ->update(array(
                                        'sp.lkp_post_status_id' =>CLOSED));
                //courier seller post deleting
                DB::table('courier_seller_post_items as spi')
                ->join('courier_seller_posts as sp','sp.id','=','spi.seller_post_id')
                ->where('sp.lkp_post_status_id','=',OPEN)
                ->where('spi.lkp_post_status_id','=',OPEN)
                ->whereRaw('sp.to_date<CURDATE()' )
                ->update(array(
                        'sp.lkp_post_status_id' =>CLOSED,
                        'spi.lkp_post_status_id' =>CLOSED));
                
                //truckhaul seller posts deleting
                DB::table('truckhaul_seller_post_items as spi')
                                ->join('truckhaul_seller_posts as sp','sp.id','=','spi.seller_post_id')
                                ->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
                            ->update(array(
                                        'sp.lkp_post_status_id' =>CLOSED,
                                        'spi.lkp_post_status_id' =>CLOSED));
                //truckLease seller posts deleting
                DB::table('trucklease_seller_post_items as spi')
                                ->join('trucklease_seller_posts as sp','sp.id','=','spi.seller_post_id')
                                ->where('sp.lkp_post_status_id','=',OPEN)
                                ->where('spi.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
                                ->update(array(
                                    'sp.lkp_post_status_id' =>CLOSED,
                                    'spi.lkp_post_status_id' =>CLOSED));
                //relocation pet seller post deleting
                DB::table('relocationpet_seller_posts as sp')
                                ->where('sp.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
                                ->update(array(
                                        'sp.lkp_post_status_id' =>CLOSED));
                //relocation office seller post deleting
                DB::table('relocationoffice_seller_posts as sp')
                                ->where('sp.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
                                ->update(array(
                                        'sp.lkp_post_status_id' =>CLOSED));
                //relocation int seller post deleting
                DB::table('relocationint_seller_posts as sp')
                                ->where('sp.lkp_post_status_id','=',OPEN)
                                ->whereRaw('sp.to_date<CURDATE()' )
                                ->update(array(
                                        'sp.lkp_post_status_id' =>CLOSED));
                
                
                //term buyer quotes closing
                DB::table('term_buyer_quote_items as bqi')
                                ->join('term_buyer_quotes as bq','bq.id','=','bqi.term_buyer_quote_id')
                                //->where('bqi.lkp_post_status_id','=',OPEN)
                                ->where('bq.lkp_post_status_id','=',OPEN)
                                ->whereRaw('bq.from_date<CURDATE()' )
                                ->update(array('bq.lkp_post_status_id' =>CLOSED));
            }catch(\Exception $e){
                
            }
    }
    
    /**
     * Get Post Buyer Counter Offer Page
     * Get details of buyer counter offer 
     * @param int $buyerQuoteItemId
     * @return type
     */
    public function buyerBooknow($buyerQuoteItemId, $buyerQuoteSellerPriceId) {
        Log::info('Get posted buyer counter offer: ' . Auth::id(), array('c' => '1'));
        try {
            $serviceId = Session::get('service_id');
			//Loading respective service data grid
			switch($serviceId){
                case ROAD_FTL       : 
                    $buyerOfferDetails = FtlBuyerComponent::getPostBuyerCounterOfferForFtl($buyerQuoteItemId);
                    //echo "<pre>";print_R($buyerOfferDetails['arrayBuyerCounterOffer']);die;
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                    $destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
                    //$packagingType = BuyerComponent::getPackagingType('Destination');
                    $packagingType =  CommonComponent::getLoadBasedAllPackages($buyerOfferDetails['arrayBuyerCounterOffer'][0]->lkp_load_type_id);
                        return view('buyers.buyer_book_now',
                                [
                                    'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                    'fromLocation' => $buyerOfferDetails['fromLocation'],
                                    'toLocation' => $buyerOfferDetails['toLocation'],
	                        		'toLocationid' =>  $buyerOfferDetails['tolocationid'],
                                    'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                                    'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                                    'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                                    'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                                    'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
                                    'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
                                    'destinationLocation' =>  $buyerOfferDetails['destinationLocation'],
                                    'packagingType' =>  $buyerOfferDetails['packagingType'],
                                    'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                                    'countview' =>  $buyerOfferDetails['countview'],
                                    'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                                    'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'sourceLocationType' =>  $sourceLocationType,
                                    'destinationLocationType' =>  $destinationLocationType,
                                    'packagingType' =>  $packagingType,
                                    'isltl' => 0,
                                	'from_location_id' => isset($buyerOfferDetails['arrayBuyerCounterOffer'][0]->from_city_id) ? $buyerOfferDetails['arrayBuyerCounterOffer'][0]->from_city_id : 0,
                                	'to_location_id' => isset($buyerOfferDetails['arrayBuyerCounterOffer'][0]->to_city_id) ? $buyerOfferDetails['arrayBuyerCounterOffer'][0]->to_city_id : 0,
                                ]
                        );
                    break;
                    case ROAD_PTL       : 
                    case RAIL :
                    case AIR_DOMESTIC :
                    case AIR_INTERNATIONAL :  
                    case OCEAN :  
                    case ROAD_INTRACITY :
                    case COURIER:
                        $buyerOfferDetails = PtlBuyerComponent::getPostBuyerCounterOfferForPtl($buyerQuoteItemId);
                    //echo "<pre>";print_R($buyerOfferDetails);die;
                        return view('ptl.buyers.ltl_buyer_book_now',
                                [
                                    'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                    'fromLocation' => $buyerOfferDetails['fromLocation'],
                                    'toLocation' => $buyerOfferDetails['toLocation'],
	                        		'toLocationid' =>  $buyerOfferDetails['tolocationid'],
                                    'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                                    'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                                    'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                                    'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                                    'arraySellerDetails' => $buyerOfferDetails['arraySellerDetails'],
                                    'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                                    'countview' =>  $buyerOfferDetails['countview'],
                                    'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                                    'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'isltl' => 1,
                                    'booknow_flag' => 1,
                                	'from_location_id' => isset($buyerOfferDetails['arrayBuyerCounterOffer'][0]->from_location_id) ? $buyerOfferDetails['arrayBuyerCounterOffer'][0]->from_location_id : 0,
                                	'to_location_id' => isset($buyerOfferDetails['arrayBuyerCounterOffer'][0]->to_location_id) ? $buyerOfferDetails['arrayBuyerCounterOffer'][0]->to_location_id : 0,
                                ]
                        );
                        break;
                    case RELOCATION_DOMESTIC       : 
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                    $destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
                    $packagingType = BuyerComponent::getPackagingType('Destination');
                                       
                    $result = RelocationBuyerComponent::getBuyerPostDetails($buyerQuoteItemId);
                   // echo "<pre>";print_R($result);die;
                    return view('buyers.relocation.buyer_book_now',[
                                    'buyer_post_details' => $result ['postDetails'],
                                    'buyer_post_inventory_details' => $result ['inventoryDetails'],
                                    'seller_quote_details' => $result ['sellerResults'],
                                    'sourceLocationType' =>  $sourceLocationType,
                                    'destinationLocationType' =>  $destinationLocationType,
                                    'packagingType' =>  $packagingType,
                                    'buyerQuoteId' =>  $buyerQuoteItemId,
                                    'sourceLocation' =>  $sourceLocationType,
                                    'destinationLocation' =>  $destinationLocationType,
                                    'packagingType' =>  $packagingType,
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'isltl' => 0,
                    				'from_location_id' => isset($result['postDetails'][0]->from_location_id) ? $result['postDetails'][0]->from_location_id : 0,
                    				'to_location_id' => isset($result['postDetails'][0]->to_location_id) ? $result['postDetails'][0]->to_location_id : 0,
                    ]);
                    break;
                    case RELOCATION_INTERNATIONAL       : 
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                    $destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
                    $packagingType = BuyerComponent::getPackagingType('Destination');
                                       
                    $result = RelocationIntBuyerComponent::getBuyerPostDetails($buyerQuoteItemId);
                   // echo "<pre>";
                   //print_r($result);
                   //echo "</pre>";//exit;
                   $total_cartons = '';
                   if($result['postDetails'][0]->lkp_international_type_id == INTERNATIONAL_TYPE_AIR){
                    $total_cartons = 0;
                     for($k = 0; $k < count($result['inventoryDetails']); $k++){
                            $total_cartons = $total_cartons + $result['inventoryDetails'][$k]->number_of_cartons;
                     }
                   }
                 
                    return view('buyers.relocation.buyer_book_now',[
                                    'buyer_post_details' => $result ['postDetails'],
                                    'buyer_post_inventory_details' => $result ['inventoryDetails'],
                                    'seller_quote_details' => $result ['sellerResults'],
                                    'sourceLocationType' =>  $sourceLocationType,
                                    'destinationLocationType' =>  $destinationLocationType,
                                    'packagingType' =>  $packagingType,
                                    'buyerQuoteId' =>  $buyerQuoteItemId,
                                    'sourceLocation' =>  $sourceLocationType,
                                    'destinationLocation' =>  $destinationLocationType,
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'packagingType' =>  $packagingType,
                                    'isltl' => 0,
                                    'total_cartons' => $total_cartons
                    ]);
                    break;

                    case RELOCATION_OFFICE_MOVE       : 
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                    $destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
                    $particulars = CommonComponent::getOfficeParticulars();
                                       
                    $result = RelocationOfficeBuyerComponent::getBuyerPostDetails($buyerQuoteItemId);
                  
                    return view('buyers.relocation.buyer_book_now',[
                                    'buyer_post_details' => $result ['postDetails'],
                                    'buyer_post_inventory_details' => $result ['inventoryDetails'],
                                    'seller_quote_details' => $result ['sellerResults'],
                                    'sourceLocationType' =>  $sourceLocationType,
                                    'destinationLocationType' =>  $destinationLocationType,
                                    'buyerQuoteId' =>  $buyerQuoteItemId,
                                    'sourceLocation' =>  $sourceLocationType,
                                    'destinationLocation' =>  $destinationLocationType,
                                    'particulars' => $particulars,
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'isltl' => 0
                    ]);
                    break;                    
                    case RELOCATION_PET_MOVE       : 
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                    $destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
                    $result = RelocationPetBuyerComponent::getBuyerPostDetails($buyerQuoteItemId);
                  
                    return view('buyers.relocation.buyer_book_now',[
                                    'buyer_post_details' => $result ['postDetails'],
                                    //'buyer_post_inventory_details' => $result ['inventoryDetails'],
                                    'seller_quote_details' => $result ['sellerResults'],
                                    'sourceLocationType' =>  $sourceLocationType,
                                    'destinationLocationType' =>  $destinationLocationType,
                                    'buyerQuoteId' =>  $buyerQuoteItemId,
                                    'sourceLocation' =>  $sourceLocationType,
                                    'destinationLocation' =>  $destinationLocationType,
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'isltl' => 0
                    ]);
                    break;
                case ROAD_TRUCK_HAUL:
                    $buyerOfferDetails = TruckHaulBuyerComponent::getPostBuyerCounterOfferForTH($buyerQuoteItemId);
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                    return view('buyers.buyer_book_now',
                        [
                            'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                            'fromLocation' => $buyerOfferDetails['fromLocation'],
                            'toLocation' => $buyerOfferDetails['toLocation'],
	                        'toLocationid' =>  $buyerOfferDetails['tolocationid'],
                            'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                            'dispatchDate'      => $buyerOfferDetails['dispatchDate'],
                            'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                            'countBuyerLeads'   => $buyerOfferDetails['countBuyerLeads'],
                            'sourceLocation'    =>  $buyerOfferDetails['sourceLocation'],
                            'countCartItems'    =>  $buyerOfferDetails['countCartItems'],
                            'countview'         =>  $buyerOfferDetails['countview'],
                            'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                            'comparisonType'    =>  $buyerOfferDetails['comparisonType'],
                            'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                            'sourceLocationType' =>  $sourceLocationType,
                            
                        ]
                );
                    break;
                case ROAD_TRUCK_LEASE       : 
                    $buyerOfferDetails = TruckLeaseBuyerComponent::getPostBuyerCounterOfferForTL($buyerQuoteItemId);
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Reporting');
                  
                        return view('buyers.buyer_book_now',
                                [
                                    'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                    'fromLocation' => $buyerOfferDetails['fromLocation'],
                               
                                    'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                                    'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                                    'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                                    'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                                    'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
                                    'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
                               
                                    'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                                    'countview' =>  $buyerOfferDetails['countview'],
                                    'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                                    'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'sourceLocationType' =>  $sourceLocationType,
                               
                                    'isltl' => 0
                                ]
                        );
                    break;
                case RELOCATION_GLOBAL_MOBILITY       : 
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                                      
                    $result = RelocationGlobalBuyerComponent::getBuyerPostDetails($buyerQuoteItemId);
                    $toLocation=$result ['sellerResults'][0];
                   //echo "<pre>";print_R($result['sellerQuoteItems']);die;
                    return view('buyers.relocation.buyer_book_now',[
                                    'buyer_post_details' => $result ['postDetails'],
                                    'quoteItemsDetails' => $result ['quoteItemsDetails'],
                                    'seller_quote_details' => $result ['sellerResults'],
                                    'sourceLocationType' =>  $sourceLocationType,
                                    'buyerQuoteId' =>  $buyerQuoteItemId,
                                    'sourceLocation' =>  $sourceLocationType,
                                    'toLocation' =>  $toLocation->city_name,
                                    'isltl' => 0,
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    //'from_location_id' => isset($result['postDetails'][0]->from_location_id) ? $result['postDetails'][0]->from_location_id : 0,
                                    'to_location_id' => isset($result['postDetails'][0]->location_id) ? $result['postDetails'][0]->location_id : 0,
                                    'seller_quote_items_list' => $result ['sellerQuoteItems']
                    ]);
                    break;
                default :
                    return view('buyers.buyer_book_now',
                                [
                                    'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                    'fromLocation' => $buyerOfferDetails['fromLocation'],
                                    'toLocation' => $buyerOfferDetails['toLocation'],
                                    'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                                    'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                                    'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                                    'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
                                    'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
                                    'destinationLocation' =>  $buyerOfferDetails['destinationLocation'],
                                    'packagingType' =>  $buyerOfferDetails['packagingType'],
                                    'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                                    'countview' =>  $buyerOfferDetails['countview'],
                                    'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                                    'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'sourceLocationType' =>  $sourceLocationType,
                                    'destinationLocationType' =>  $destinationLocationType,
                                    'packagingType' =>  $packagingType,
                                    'isltl' => 0
                                ]
                        );
                    break;
            }
            //rendering the view with the data grid
        } catch (Exception $e) {
            
        }
    }
    /**
     * Get Post Buyer Counter Offer Page
     * Get details of buyer counter offer 
     * @param int $buyerQuoteItemId
     * @return type
     */
    public function buyerBooknowForLeads($buyerQuoteItemId, $buyerQuoteSellerPriceId) {
        Log::info('Get posted buyer counter offer: ' . Auth::id(), array('c' => '1'));
        try {
            $serviceId = Session::get('service_id');
			//Loading respective service data grid
			switch($serviceId){
                case ROAD_FTL       : 
                    $buyerOfferDetails = FtlBuyerComponent::getPostBuyerCounterOfferForFtl($buyerQuoteItemId);
                    $arrayLeadsData = FtlBuyerComponent::getSellerLeadsData($serviceId, $buyerQuoteItemId);//Leads query
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                    $destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
                    //$packagingType = BuyerComponent::getPackagingType('Destination');
                    $packagingType =  CommonComponent::getLoadBasedAllPackages($buyerOfferDetails['arrayBuyerCounterOffer'][0]->lkp_load_type_id);
                        return view('buyers.buyer_book_now',
                                [
                                    'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                    'fromLocation' => $buyerOfferDetails['fromLocation'],
                                    'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                                    'toLocation' => $buyerOfferDetails['toLocation'],
	                        		'toLocationid' =>  $buyerOfferDetails['tolocationid'],
                                    'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                                    'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                                    'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                                    'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
                                    'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
                                    'destinationLocation' =>  $buyerOfferDetails['destinationLocation'],
                                    'packagingType' =>  $buyerOfferDetails['packagingType'],
                                    'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                                    'countview' =>  $buyerOfferDetails['countview'],
                                    'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                                    'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'sourceLocationType' =>  $sourceLocationType,
                                    'destinationLocationType' =>  $destinationLocationType,
                                    'packagingType' =>  $packagingType,
                                    'sellerDetailsLeads' =>  $arrayLeadsData,
                                    'isltl' => 0,
                                    //'booknow_flag' => 1,
                                	'from_location_id' => isset($buyerOfferDetails['arrayBuyerCounterOffer'][0]->from_city_id) ? $buyerOfferDetails['arrayBuyerCounterOffer'][0]->from_city_id : 0,
                                	'to_location_id' => isset($buyerOfferDetails['arrayBuyerCounterOffer'][0]->to_city_id) ? $buyerOfferDetails['arrayBuyerCounterOffer'][0]->to_city_id : 0,
                                ]
                        );
                    break;
                    case ROAD_PTL       : 
                    case RAIL :
                    case AIR_DOMESTIC :
                    case AIR_INTERNATIONAL :  
                    case OCEAN :  
                    case COURIER :
                        $buyerOfferDetails = PtlBuyerComponent::getPostBuyerCounterOfferForPtl($buyerQuoteItemId);
                        if(Session::get('service_id') == COURIER){
                            $PostCourierType = PtlBuyerComponent::getPostCourierType($buyerQuoteItemId,$serviceId);
                            $arrayLeadsData = FtlBuyerComponent::getSellerLeadsData($serviceId, $buyerQuoteItemId,$PostCourierType);
                        }else{
                            $arrayLeadsData = FtlBuyerComponent::getSellerLeadsData($serviceId, $buyerQuoteItemId);
                        }
                        
                        return view('ptl.buyers.ltl_buyer_book_now',
                                [
                                    'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                    'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                                    'fromLocation' => $buyerOfferDetails['fromLocation'],
                                    'toLocation' => $buyerOfferDetails['toLocation'],
	                        		'toLocationid' =>  $buyerOfferDetails['tolocationid'],
                                    'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                                    'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                                	'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                                    'arraySellerDetails' => $buyerOfferDetails['arraySellerDetails'],
                                    'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                                    'countview' =>  $buyerOfferDetails['countview'],
                                    'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                                    'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'sellerDetailsLeads' =>  $arrayLeadsData,
                                    'isltl' => 1,
                                    'booknow_flag' => 1,
                                	'from_location_id' => isset($buyerOfferDetails['arrayBuyerCounterOffer'][0]->from_location_id) ? $buyerOfferDetails['arrayBuyerCounterOffer'][0]->from_location_id : 0,
                                	'to_location_id' => isset($buyerOfferDetails['arrayBuyerCounterOffer'][0]->to_location_id) ? $buyerOfferDetails['arrayBuyerCounterOffer'][0]->to_location_id : 0,
                                ]
                        );
                        break;
                    
                case ROAD_TRUCK_HAUL:
                    
                    $buyerOfferDetails = TruckHaulBuyerComponent::getPostBuyerCounterOfferForTH($buyerQuoteItemId);
                    $arrayLeadsData = TruckHaulBuyerComponent::getSellerLeadsData($serviceId, $buyerQuoteItemId);//Leads query
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Reporting');
                    return view('buyers.buyer_book_now',
                                [
                                    'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                    'fromLocation' => $buyerOfferDetails['fromLocation'],
                                    'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                                    'toLocation' => $buyerOfferDetails['toLocation'],
	                        		'toLocationid' =>  $buyerOfferDetails['tolocationid'],
                                    'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                                    'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                                    'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
                                    'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
                                    'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                                    'countview' =>  $buyerOfferDetails['countview'],
                                    'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                                    'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'sourceLocationType' =>  $sourceLocationType,
                                    'sellerDetailsLeads' =>  $arrayLeadsData,
                                    //'booknow_flag' => 1,
                                	
                                ]
                        );
                    break;
                
                case ROAD_TRUCK_LEASE       : 
                    $buyerOfferDetails = TruckLeaseBuyerComponent::getPostBuyerCounterOfferForTL($buyerQuoteItemId);
                    $arrayLeadsData = TruckLeaseBuyerComponent::getSellerLeadsData($serviceId, $buyerQuoteItemId);//Leads query
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Reporting');
                   
                    return view('buyers.buyer_book_now',
                                [
                                    'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                    'fromLocation' => $buyerOfferDetails['fromLocation'],
                                    'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                   
                                    'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                                    'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                                    'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                                    'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
                                    'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
                                    'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                                    'countview' =>  $buyerOfferDetails['countview'],
                                    'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                                    'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'sourceLocationType' =>  $sourceLocationType,
                                    'sellerDetailsLeads' =>  $arrayLeadsData,
                                    'isltl' => 0,
                                    //'booknow_flag' => 1,
                                ]
                        );
                    break;
                default :
                    return view('buyers.buyer_book_now',
                                [
                                    'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                    'fromLocation' => $buyerOfferDetails['fromLocation'],
                                    'toLocation' => $buyerOfferDetails['toLocation'],
                                    'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                                    'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                                    'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                                    'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
                                    'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
                                    'destinationLocation' =>  $buyerOfferDetails['destinationLocation'],
                                    'packagingType' =>  $buyerOfferDetails['packagingType'],
                                    'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                                    'countview' =>  $buyerOfferDetails['countview'],
                                    'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                                    'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'sourceLocationType' =>  $sourceLocationType,
                                    'destinationLocationType' =>  $destinationLocationType,
                                    'packagingType' =>  $packagingType,
                                    'isltl' => 0,
                                    'from_location_id' => isset($buyerOfferDetails['arrayBuyerCounterOffer'][0]->from_city_id) ? $buyerOfferDetails['arrayBuyerCounterOffer'][0]->from_city_id : 0,
                                    'to_location_id' => isset($buyerOfferDetails['arrayBuyerCounterOffer'][0]->to_city_id) ? $buyerOfferDetails['arrayBuyerCounterOffer'][0]->to_city_id : 0,
                                ]
                        );
                    break;
            }
            //rendering the view with the data grid
        } catch (Exception $e) {
            
        }
    }
    /**
     * Get Post Buyer Counter Offer Page
     * Get details of buyer counter offer 
     * @param int $buyerQuoteItemId
     * @return type
     */
    public function buyerBooknowFromSearchList($sellerPostItemId) {
        Log::info('Get posted buyer counter offer: ' . Auth::id(), array('c' => '1'));
        try {
            $serviceId = Session::get('service_id');
			//Loading respective service data grid
			switch($serviceId){
                case ROAD_FTL       : 
					
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                    $destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
                    $packagingType = BuyerComponent::getPackagingType('Destination');
                    $sellerDetails = FtlSellerListingComponent::getFTLSellerPostItemDetails($sellerPostItemId);
                    //echo "<pre>";print_R($sellerDetails['seller_post']);die;
                    return view('ftl.buyers.buyer_search_book_now',
                        [
                            'sellerPostId' => $sellerDetails['id'],
                            'seller_post' =>  $sellerDetails['seller_post'],
                            'countview' =>  $sellerDetails['countview'],
                            'fromLocation' =>  $sellerDetails['fromLocation'],
                            'toLocation' =>  $sellerDetails['toLocation'],
                            'toLocationid' =>  $sellerDetails['tolocationid'],
                            'delivery_date' =>  $sellerDetails['deliveryDate'],
                            'dispatch_date' =>  $sellerDetails['dispatchDate'],
                            'sourceLocation' =>  $sourceLocationType,
                            'destinationLocation' =>  $destinationLocationType,
                            'packagingType' =>  $packagingType,
                            'isltl' => 0,
                        	'from_location_id' => isset($sellerDetails['seller_post'][0]->from_location_id) ? $sellerDetails['seller_post'][0]->from_location_id : 0,
                        	'to_location_id' => isset($sellerDetails['seller_post'][0]->to_location_id) ? $sellerDetails['seller_post'][0]->to_location_id : 0,
                        ]
                    );
                    break;
                    case ROAD_PTL       : 
                    case RAIL :
                    case AIR_DOMESTIC :
                    case AIR_INTERNATIONAL :  
                    case OCEAN :  
                    case COURIER:
                        $sellerDetails = PtlSellerListingComponent::getPTLSellerPostItemDetails($sellerPostItemId,Input::all());                       
                        $allInput = Input::all();
                        return view('ptl.buyers.buyer_ltl_search_book_now',
                                [
                                    'sellerPostId' => $sellerDetails['id'],
                                    'seller_post' =>  $sellerDetails['seller_post'],
                                    'countview' =>  $sellerDetails['countview'],
                                    'fromLocation' =>  $sellerDetails['fromLocation'],
                                    'toLocation' =>  $sellerDetails['toLocation'],
                                    'toLocationid' =>  $sellerDetails['tolocationid'],
                                    'deliveryDate' =>  $sellerDetails['deliveryDate'],
                                    'dispatchDate' =>  $sellerDetails['dispatchDate'],
                                    'allInput' =>  $allInput,
                                    'isltl' => 1,
                                    'booknow_flag' => 1,
                                	'from_location_id' => isset($sellerDetails['seller_post'][0]->from_location_id) ? $sellerDetails['seller_post'][0]->from_location_id : 0,
                                	'to_location_id' => isset($sellerDetails['seller_post'][0]->to_location_id) ? $sellerDetails['seller_post'][0]->to_location_id : 0,
                                ]
                        );
                        break;
                    
                    case RELOCATION_DOMESTIC       : 
                    $allInput = Input::all();    

                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                    $destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
                    $packagingType = BuyerComponent::getPackagingType('Destination');
                    $sellerDetails = PtlSellerListingComponent::getPTLSellerPostItemDetails($sellerPostItemId,$allInput);
                    
                    return view('buyers.relocation.buyer_search_book_now',
                        [
                            'sellerPostId' => $sellerDetails['id'],
                            'seller_post' =>  $sellerDetails['seller_post'],
                            'countview' =>  $sellerDetails['countview'],
                            'fromLocation' =>  $sellerDetails['fromLocation'],
                            'toLocation' =>  $sellerDetails['toLocation'],
                            'toLocationid' =>  $sellerDetails['tolocationid'],
                            'deliveryDate' =>  $sellerDetails['deliveryDate'],
                            'dispatchDate' =>  $sellerDetails['dispatchDate'],
                            'allInput' =>  $allInput,
                            'sourceLocation' =>  $sourceLocationType,
                            'destinationLocation' =>  $destinationLocationType,
                            'packagingType' =>  $packagingType,
                            'isltl' => 0,
                        	'from_location_id' => isset($sellerDetails['seller_post'][0]->from_location_id) ? $sellerDetails['seller_post'][0]->from_location_id : 0,
                        	'to_location_id' => isset($sellerDetails['seller_post'][0]->to_location_id) ? $sellerDetails['seller_post'][0]->to_location_id : 0,
                        ]
                    );
                    break;
                    case RELOCATION_INTERNATIONAL   :
                    $allInput = Input::all();    

                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                    $destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
                    $packagingType = BuyerComponent::getPackagingType('Destination');
                    $sellerDetails = PtlSellerListingComponent::getPTLSellerPostItemDetails($sellerPostItemId,$allInput);
					                  
                    return view('buyers.relocation.buyer_search_book_now',
                        [
                            'sellerPostId' => $sellerDetails['id'],
                            'seller_post' =>  $sellerDetails['seller_post'],
                            'countview' =>  $sellerDetails['countview'],
                            'fromLocation' =>  $sellerDetails['fromLocation'],
                            'toLocation' =>  $sellerDetails['toLocation'],
                            'toLocationid' =>  $sellerDetails['tolocationid'],
                            'deliveryDate' =>  $sellerDetails['deliveryDate'],
                            'dispatchDate' =>  $sellerDetails['dispatchDate'],
                            'allInput' =>  $allInput,
                            'sourceLocation' =>  $sourceLocationType,
                            'destinationLocation' =>  $destinationLocationType,
                            'packagingType' =>  $packagingType,
                            'isltl' => 0
                        ]
                    );
                    case RELOCATION_OFFICE_MOVE     : 
                    $allInput = Input::all();    

                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                    $destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
                    $sellerDetails = PtlSellerListingComponent::getPTLSellerPostItemDetails($sellerPostItemId,$allInput);
                    
                    return view('buyers.relocation.buyer_search_book_now',
                        [
                            'sellerPostId' => $sellerDetails['id'],
                            'seller_post' =>  $sellerDetails['seller_post'],
                            'countview' =>  $sellerDetails['countview'],
                            'fromLocation' =>  $sellerDetails['fromLocation'],
                            'toLocation' =>  $sellerDetails['toLocation'],
                            'toLocationid' =>  $sellerDetails['tolocationid'],
                            'deliveryDate' =>  $sellerDetails['deliveryDate'],
                            'dispatchDate' =>  $sellerDetails['dispatchDate'],
                            'allInput' =>  $allInput,
                            'sourceLocation' =>  $sourceLocationType,
                            'destinationLocation' =>  $destinationLocationType,
                            'isltl' => 0
                        ]
                    );
                    break;
                
                case RELOCATION_PET_MOVE       : 
                    $allInput = Input::all();    
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                    $destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
                    $sellerDetails = PtlSellerListingComponent::getPTLSellerPostItemDetails($sellerPostItemId,$allInput);
                    
                    return view('buyers.relocation.buyer_search_book_now',
                        [
                            'sellerPostId' => $sellerDetails['id'],
                            'seller_post' =>  $sellerDetails['seller_post'],
                            'countview' =>  $sellerDetails['countview'],
                            'fromLocation' =>  $sellerDetails['fromLocation'],
                            'toLocation' =>  $sellerDetails['toLocation'],
                           	'toLocationid' =>  $sellerDetails['tolocationid'],
                            'deliveryDate' =>  $sellerDetails['deliveryDate'],
                            'dispatchDate' =>  $sellerDetails['dispatchDate'],
                            'allInput' =>  $allInput,
                            'sourceLocation' =>  $sourceLocationType,
                            'destinationLocation' =>  $destinationLocationType,
                            'isltl' => 0
                        ]
                    );
                    break;
                
                case ROAD_TRUCK_HAUL       : 
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Reporting');
                    $sellerDetails = TruckHaulBuyerComponent::getTHSellerPostItemDetails($sellerPostItemId);
                    return view('ftl.buyers.buyer_search_book_now',
                        [
                            'sellerPostId' => $sellerDetails['id'],
                            'seller_post' =>  $sellerDetails['seller_post'],
                            'countview' =>  $sellerDetails['countview'],
                            'fromLocation' =>  $sellerDetails['fromLocation'],
                            'toLocation' =>  $sellerDetails['toLocation'],
                           	'toLocationid' =>  $sellerDetails['tolocationid'],
                            'dispatch_date' =>  $sellerDetails['dispatchDate'],
                            'sourceLocation' =>  $sourceLocationType,
                            
                        ]
                    );
                    break;
                case ROAD_TRUCK_LEASE       : 

                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Reporting');
                    $sellerDetails = TruckLeaseSellerComponent::getTruckLeaseSellerPostItemDetails($sellerPostItemId);
                    return view('ftl.buyers.buyer_search_book_now',
                        [
                            'sellerPostId' => $sellerDetails['id'],
                            'seller_post' =>  $sellerDetails['seller_post'],
                            'countview' =>  $sellerDetails['countview'],
                            'fromLocation' =>  $sellerDetails['fromLocation'],

                            'delivery_date' =>  $sellerDetails['deliveryDate'],
                            'dispatch_date' =>  $sellerDetails['dispatchDate'],
                            'sourceLocation' =>  $sourceLocationType,
                            'isltl' => 0
                        ]
                    );
                    break;               
                case RELOCATION_GLOBAL_MOBILITY       : 
                    $allInput = Input::all();    
                    $sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
                    $sellerDetails = PtlSellerListingComponent::getPTLSellerPostItemDetails($sellerPostItemId,$allInput);
                    
                    return view('buyers.relocation.buyer_search_book_now',
                        [
                            'sellerPostId' => $sellerDetails['id'],
                            'seller_post' =>  $sellerDetails['seller_post'],
                            'countview' =>  $sellerDetails['countview'],
                            'toLocation' =>  $sellerDetails['toLocation'],
                           	'toLocationid' =>  $sellerDetails['tolocationid'],
                            'deliveryDate' =>  $sellerDetails['deliveryDate'],
                            'dispatchDate' =>  $sellerDetails['dispatchDate'],
                            'allInput' =>  $allInput,
                            'sourceLocation' =>  $sourceLocationType,
                            'isltl' => 0,
                            'to_location_id' => isset($sellerDetails['seller_post'][0]->location_id) ? $sellerDetails['seller_post'][0]->location_id : 0,
                        ]
                    );
                    break;
                default :
                    return view('buyers.buyer_book_now',
                                [
                                    'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                    'fromLocation' => $buyerOfferDetails['fromLocation'],
                                    'toLocation' => $buyerOfferDetails['toLocation'],
                                    'deliveryDate' => $buyerOfferDetails['deliveryDate'],
                                    'dispatchDate' => $buyerOfferDetails['dispatchDate'],
                                    'arrayBuyerQuoteSellersQuotesPrices' => $buyerOfferDetails['arrayBuyerQuoteSellersQuotesPrices'],
                                    'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
                                    'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
                                    'destinationLocation' =>  $buyerOfferDetails['destinationLocation'],
                                    'packagingType' =>  $buyerOfferDetails['packagingType'],
                                    'countCartItems' =>  $buyerOfferDetails['countCartItems'],
                                    'countview' =>  $buyerOfferDetails['countview'],
                                    'buyerPostCounterOfferComparisonTypes' =>  $buyerOfferDetails['buyerPostCounterOfferComparisonTypes'],
                                    'comparisonType' =>  $buyerOfferDetails['comparisonType'],
                                    'buyerQuoteSellerPriceId' =>  $buyerQuoteSellerPriceId,
                                    'sourceLocationType' =>  $sourceLocationType,
                                    'destinationLocationType' =>  $destinationLocationType,
                                    'packagingType' =>  $packagingType,
                                    'isltl' => 0
                                ]
                        );
                    break;
            }
            //rendering the view with the data grid
        } catch (Exception $e) {
            
        }
    }
    
    
    /*     * ***** Below Script for get seller list from city************** */
    
    public function getEditSellerslist() {
    	//print_r($_POST); exit;
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
    		}elseif($serviceId==ROAD_TRUCK_LEASE){
    			$sellerIds = DB::table('trucklease_buyer_quote_selected_sellers')
    			->where('trucklease_buyer_quote_selected_sellers.buyer_quote_id', $quoteId)
    			->select('trucklease_buyer_quote_selected_sellers.seller_id')
    			->get();
    		}elseif($serviceId==ROAD_TRUCK_HAUL){
    		$sellerIds = DB::table('truckhaul_buyer_quote_selected_sellers')
    		->where('truckhaul_buyer_quote_selected_sellers.buyer_quote_id', $quoteId)
    		->select('truckhaul_buyer_quote_selected_sellers.seller_id')
    		->get();  
    		}elseif($serviceId==RELOCATION_DOMESTIC){
    		$sellerIds = DB::table('relocation_buyer_selected_sellers')
    		->where('relocation_buyer_selected_sellers.buyer_post_id', $quoteId)
    		->select('relocation_buyer_selected_sellers.seller_id')
    		->get();
    		}elseif($serviceId==RELOCATION_OFFICE_MOVE){
                $sellerIds = DB::table('relocationoffice_buyer_selected_sellers')
                ->where('relocationoffice_buyer_selected_sellers.buyer_post_id', $quoteId)
                ->select('relocationoffice_buyer_selected_sellers.seller_id')
                ->get();
                }elseif($serviceId==RELOCATION_PET_MOVE){
                $sellerIds = DB::table('relocationpet_buyer_selected_sellers')
                ->where('relocationpet_buyer_selected_sellers.buyer_post_id', $quoteId)
                ->select('relocationpet_buyer_selected_sellers.seller_id')
                ->get();
                }elseif($serviceId==RELOCATION_INTERNATIONAL){
                $sellerIds = DB::table('relocationint_buyer_selected_sellers as bqss')
                        ->leftjoin('relocationint_buyer_posts as bp','bp.id','=','bqss.buyer_post_id')
                        ->where('bp.lkp_international_type_id', $_POST['post_type'])
                        ->where('bqss.buyer_post_id', $quoteId)
                        ->select('bqss.seller_id')
                        ->get();
                }else{
                $sellerIds = DB::table('buyer_quote_selected_sellers')
                ->where('buyer_quote_selected_sellers.buyer_quote_id', $quoteId)
                ->select('buyer_quote_selected_sellers.seller_id')
                ->get();                    
                }	
    		$sellerall_ids	=	array();
    		foreach ($sellerIds as $sellerdata) {
    			$sellerall_ids[] = $sellerdata->seller_id;
    		}    		
    		
    		$sellersStr = $_POST['seller_list'];
    
    		$districts = DB::table('lkp_cities')
    		->whereIn('lkp_cities.id', $sellersStr)
    		->select('lkp_cities.lkp_district_id')
    		->get();
    		
    		foreach ($districts as $dist) {
    			$district_array[] = $dist->lkp_district_id;
    		}
                if($serviceId==ROAD_FTL){
    		$seller_data = DB::table('seller_post_items')
    		->join('users', 'seller_post_items.created_by', '=', 'users.id')
    		->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
    		->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
    		->distinct('seller_post_items.created_by')
    		->whereIn('seller_post_items.lkp_district_id', $district_array)
    		->whereNotIn('seller_post_items.created_by', $sellerall_ids)
    		->where('users.lkp_role_id', SELLER)
                ->orWhere('users.secondary_role_id', SELLER)->orderBy ( 'users.username', 'asc' )
    		->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
    		->get();
                } elseif($serviceId==ROAD_TRUCK_HAUL) {
                $seller_data = DB::table('truckhaul_seller_post_items')
    		->join('users', 'truckhaul_seller_post_items.created_by', '=', 'users.id')
    		->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
    		->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
    		->distinct('truckhaul_seller_post_items.created_by')
    		->whereIn('truckhaul_seller_post_items.lkp_district_id', $district_array)
    		->whereNotIn('truckhaul_seller_post_items.created_by', $sellerall_ids)
    		->where('users.lkp_role_id', SELLER)
                ->orWhere('users.secondary_role_id', SELLER)->orderBy ( 'users.username', 'asc' )
    		->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
    		->get(); 
             }elseif($serviceId==ROAD_TRUCK_LEASE) {
                	$seller_data = DB::table('trucklease_seller_post_items')
                	->join('users', 'trucklease_seller_post_items.created_by', '=', 'users.id')
                	->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
                	->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
                	->distinct('trucklease_seller_post_items.created_by')
                	->whereIn('trucklease_seller_post_items.lkp_district_id', $district_array)
                	->whereNotIn('trucklease_seller_post_items.created_by', $sellerall_ids)
                	->where('users.lkp_role_id', SELLER)
                	->orWhere('users.secondary_role_id', SELLER)->orderBy ( 'users.username', 'asc' )
                	->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
                	->get();
                }elseif($serviceId==RELOCATION_DOMESTIC) {
                $seller_data = DB::table('relocation_seller_post_items')
    		->join('users', 'relocation_seller_post_items.created_by', '=', 'users.id')
    		->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
    		->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
    		->distinct('relocation_seller_post_items.created_by')
    		
    		->whereNotIn('relocation_seller_post_items.created_by', $sellerall_ids)
    		->where('users.lkp_role_id', SELLER)
                ->orWhere('users.secondary_role_id', SELLER)->orderBy ( 'users.username', 'asc' )
    		->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
    		->get();    
                }elseif($serviceId==RELOCATION_OFFICE_MOVE) {
                $seller_data = DB::table('relocationoffice_seller_posts')
            ->join('users', 'relocationoffice_seller_posts.created_by', '=', 'users.id')
            ->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
            ->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
            ->distinct('relocationoffice_seller_posts.created_by')
            
            ->whereNotIn('relocationoffice_seller_posts.created_by', $sellerall_ids)
            ->where('users.lkp_role_id', SELLER)
                ->orWhere('users.secondary_role_id', SELLER)->orderBy ( 'users.username', 'asc' )
            ->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
            ->get();    
                }elseif($serviceId==RELOCATION_PET_MOVE) {
                $seller_data = DB::table('relocationpet_seller_posts')
                    ->join('users', 'relocationpet_seller_posts.created_by', '=', 'users.id')
                    ->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
                    ->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
                    ->distinct('relocationpet_seller_posts.created_by')

                    ->whereNotIn('relocationpet_seller_posts.created_by', $sellerall_ids)
                    ->where('users.lkp_role_id', SELLER)
                        ->orWhere('users.secondary_role_id', SELLER)->orderBy ( 'users.username', 'asc' )
                    ->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
                    ->get();    
                }elseif($serviceId==RELOCATION_INTERNATIONAL) {
                    $seller_data = DB::table('relocationint_seller_posts as sp')
                    ->join('users', 'sp.created_by', '=', 'users.id')
                    ->leftjoin('seller_details', 'users.id', '=', 'sellers.user_id')
                    ->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
                    ->distinct('sp.created_by')
                    ->whereNotIn('sp.created_by', $sellerall_ids)
                    ->where('users.lkp_role_id', SELLER)
                    ->orWhere('users.secondary_role_id', SELLER)->orderBy ( 'users.username', 'asc' )
                    ->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
                    ->get();    
                }

    		foreach ($seller_data as $query) {
    			
    			$results[] = ['id' => $query->id, 'name' => $query->username . ' ' . $query->principal_place . ' ' . $query->id];
    		}
    		return Response::json($results);
    	} catch (Exception $e) {
    		echo 'Caught exception: ', $e->getMessage(), "\n";
    	}
    }
    
    public function updatePtlDistricts(){
    	
    	$seller_districts=DB::table('lkp_districts as ld')
    	->leftjoin('lkp_ptl_pincodes as lpp','lpp.districtname','=','ld.district_name')
    	->select('lpp.id as pincodeid','ld.id as districtid')
    	->get();
    	
    	
    	$updatedAt = date ( 'Y-m-d H:i:s' );
    	foreach ($seller_districts as $district) {
    		
    		LkpPtlPincode::where ( "id", $district->pincodeid )->update ( array (
   							'lkp_district_id' => $district->districtid,
    				        'updated_at' => $updatedAt
   							
   					));  
    	}
    	
    	
    }    
    
    /**
     * create new function for update seller FTL view count
     * start
     * @return count
     * @srinu and 2-05-2016
     */
    public function sellerViewCountUpdate(){	
        try {
            $sellepostid=$_REQUEST['sellerPostId'];               
            if(Session::get ( 'service_id' ) != ''){
                $serviceId = Session::get ( 'service_id' );
            }
            $table=CommonComponent::getSellerTableNameAsPerService($serviceId);
            $userId =  Auth::id();
            /*Switch cases for term viewcount  components**/	
            CommonComponent::viewCountForSeller($userId,$sellepostid,$table);	
        } catch (Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "\n";
        }

    }
    /**
    * End
    * @srinu and 2-05-2016
    */
     

    public function getServiceTypeMeasurementUnit() {
        try {
            Log::info('Get Service Type Measurement Unit depends on Service Type: ' . Auth::id(), array('c' => '1'));
            $roleId = Auth::User()->lkp_role_id;
            //Update the user activity to the buyer get capacity
            if ($roleId == BUYER) {
                CommonComponent::activityLog("RELOCATION_GM_BUYER_SERVICE_MEASUREMENT", RELOCATION_GM_BUYER_SERVICE_MEASUREMENT, 0, HTTP_REFERRER, CURRENT_URL);
            }

            $relgm_service_type = $_REQUEST['relgm_service_type'];
            $service_type = DB::table('lkp_relocationgm_services as relgms')->select('relgms.buyer_munits')->where('id', $relgm_service_type)->get();
            echo $service_type[0]->buyer_munits;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    } 
    public function getPackages(){
        try{
            Log::info('Get Package Type Based on Load Type: ' . Auth::id(), array('c' => '1'));
            
            $ptlLoadType = $_REQUEST['ptlLoadType'];
            $str = '<option value = "">Packaging Type *</option>';
            $package_type = DB::table('lkp_packaging_types')
                    ->where('is_active', IS_ACTIVE)->select('packaging_type_name','id')->orderBy ( 'packaging_type_name', 'asc' )->get();
            $service_type = DB::table('lkp_loadtypexpackagingtype as lp')
                    ->leftjoin('lkp_packaging_types as pt','pt.id','=','lp.package_type_id')
                    ->where('lp.load_type_id', $ptlLoadType)->select('pt.packaging_type_name','pt.id')->orderBy ( 'pt.packaging_type_name', 'asc' )->get();
            if(!empty($service_type)){
                foreach ($service_type as $service) {
                    $str.='<option value = "' . $service->id . '">' . $service->packaging_type_name . '</option>';
                }
                echo $str;
            }else{
                foreach ($package_type as $service) {
                    $str.='<option value = "' . $service->id . '">' . $service->packaging_type_name . '</option>';
                }
                echo $str;
            }
            
            
        } catch (Exception $ex) {

        }
    }

}
