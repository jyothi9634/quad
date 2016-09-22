<?php

namespace App\Http\Controllers;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Components\CommonComponent;
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
use App\Models\PtlBuyerQuote;
use App\Components\Ptl\PtlBuyerComponent;
use App\Components\Rail\RailBuyerComponent;
use App\Components\AirDomestic\AirDomesticBuyerComponent;
use App\Components\Courier\CourierBuyerComponent;
use App\Components\Ptl\PtlBuyerGetQuoteBooknowComponent;
use App\Components\Rail\RailBuyerGetQuoteBooknowComponent;
use App\Components\AirDomestic\AirDomesticBuyerGetQuoteBooknowComponent;
use App\Models\PtlBuyerQuoteSelectedSeller;
use App\Models\RailBuyerQuoteSelectedSeller;
use App\Models\AirdomBuyerQuoteSelectedSeller;
use App\Models\AirintBuyerQuoteSelectedSeller;
use App\Models\OceanBuyerQuoteSelectedSeller;
use App\Models\CourierBuyerQuoteSelectedSeller;
use App\Components\BuyerComponent;
use App\Components\Matching\BuyerMatchingComponent;
use App\Components\Term\TermBuyerComponent;

class PtlBuyerController extends Controller
{
/*
|--------------------------------------------------------------------------
| PTL Buyer functions start here. (srinu added below scripts for create getquote)
|--------------------------------------------------------------------------
|
| Below script for create getquote function
|
*/
   
   public function ptlCreateBuyerQuote()
    { 
    	$k = array();
      if(Session::get('service_id') == ROAD_FTL){
              return redirect('createbuyerquote');
      }elseif(Session::get('service_id') == ROAD_INTRACITY){
              return redirect('/intracity/buyer_post');
      }else if(Session::get('service_id') == RELOCATION_DOMESTIC || Session::get('service_id') == RELOCATION_PET_MOVE || Session::get('service_id') == RELOCATION_INTERNATIONAL || Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY || Session::get('service_id') == RELOCATION_OFFICE_MOVE){
            return redirect('/relocation/creatbuyerrpost');
      }else if(Session::get('service_id') == ROAD_TRUCK_HAUL){
            return redirect('truckhaul/createbuyerquote');
      }else if(Session::get('service_id') == ROAD_TRUCK_LEASE){
            return redirect('trucklease/createbuyerquote');
      }
      Log::info('Create new buyer quote for PTL Buyers: '.Auth::id(),array('c'=>'1'));
    	try {     
            $roleId = Auth::User()->lkp_role_id;
            //Saving the user activity to the buyer new quote for ptl buyers log table
            if($roleId == BUYER){
                    CommonComponent::activityLog("PTL_BUYER_ADDED_NEW_QUOTE",
                                    PTL_BUYER_ADDED_NEW_QUOTE,0,
                                    HTTP_REFERRER,CURRENT_URL);
            }
           
            if(!empty(Input::all()))  {
            $allRequestdata=Input::all();
            if($allRequestdata!=""){            	
                
                //Send REadirection controller to ltl term post creation
            	if (isset($allRequestdata['enquiry_type']) && !empty($allRequestdata['enquiry_type'])) {
            		
            		$postType = $allRequestdata['enquiry_type'];
            		if(Session::get ( 'service_id' ) != ''){
            			$serviceId = Session::get ( 'service_id' );
            		}

                    $createQuote=TermBuyerComponent::TermBuyerCreateQuote($serviceId, $allRequestdata, $postType);
            		if($createQuote!=''){

                        $multi_data_count = count($allRequestdata['load_type']);
                        //return redirect('/createbuyerquote')->with('transactionId', $createQuote)->with('postsCount',$multi_data_count)->with('postType',$postType);
                        if (!empty($_REQUEST['confirm_but']) && isset($_REQUEST['confirm_but'])) {
                            $postStatus= OPEN;
                        } else {
                            $postStatus= SAVEDASDRAFT;
                        }
                        if($postStatus == OPEN){
                            return redirect('/ptl/createbuyerquote')->with('transactionId', $createQuote)->with('postsCount',$multi_data_count)->with('postType',$postType);
                        }else{
                            return redirect('/buyerposts')->with('sumsg', "Post was saved as draft")->with('postsCount',$multi_data_count)->with('postType',$postType);
                        }
            			 
            		}
            		 
            	} else {
            	
                $data = array();
                if(isset($allRequestdata['ptlQuoteaccessId']) && !empty($allRequestdata['ptlQuoteaccessId'])) {
                $access=$allRequestdata['ptlQuoteaccessId'];
                }
                $sellers    =$allRequestdata['seller_list'];
                unset($allRequestdata['_token']);
                unset($allRequestdata['ptlQuoteaccessId']);
                unset($allRequestdata['seller_list']);unset($allRequestdata['Get_Quote']);
                unset($allRequestdata['agree']);unset($allRequestdata['confirm']);
                if(isset($allRequestdata['prohibited'])){                
                unset($allRequestdata['prohibited']);
                }
                
                foreach($allRequestdata as $i=>$element)
                {
                	
                    foreach($element as $j=>$sub_element)
                    {
                        //We are basically inverting the indexes
                        $data[$j][$i] = $sub_element;
                        $data[$j]['ptlQuoteaccessId'] = $access;
                        $data[$j]['seller_list'] = $sellers;                        
                    }
                }
                
                if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			} 
                //foreach($unique as $i=>$v){
                for($i=0;$i<count($allRequestdata['ptlFromLocation']);$i++){
                	
                	//for All services
                	$fromcities = array();
                	$fromcities[] = $allRequestdata['ptlFromLocation'][$i];
                	
                    switch($serviceId){
                    case ROAD_PTL:
//echo '<pre>';print_r($allRequestdata);exit;
                    if((!empty($allRequestdata['new_row'])&& in_array($i,$allRequestdata['new_row'])) || $i==0){
                    	
                        $ptlBuyerMaindataId[$i] = PtlBuyerComponent::ptlBuyerQuoteMainData($data[$i],$allRequestdata['is_commercial'][0]);
                        $k=$ptlBuyerMaindataId[$i];
                       
                        //below array for matching engine in PTL start
                        /*matching start*/
                        $matchedItems = array();
                        $matchedItems['ptlDispatchDate'][]=$allRequestdata['ptlDispatchDate'][$i];
                        $matchedItems['ptlDeliveryhDate'][]=$allRequestdata['ptlDeliveryhDate'][$i];
                        $matchedItems['ptlFromLocation'][]=$allRequestdata['ptlFromLocation'][$i];
                        $matchedItems['ptlToLocation'][]=$allRequestdata['ptlToLocation'][$i];
                        BuyerMatchingComponent::doMatching(ROAD_PTL,$k['buyerQuoteId'],2,$matchedItems);
                        /*matching end*/
                        
                      if($k['buyerQuoteId']!=0) {
                        $ptlBuyerQuoteitemData = PtlBuyerComponent::ptlBuyerQuoteItems($data[$i],$k['buyerQuoteId'],$fromcities);
                       
                      }
                    }else{
                        
                        if($k['buyerQuoteId']!=0) {
                        $ptlBuyerQuoteitemData = PtlBuyerComponent::ptlBuyerQuoteItems($data[$i],$k['buyerQuoteId'],$fromcities);
                        
                      }
                        
                    }
                    break;
                    case RAIL       : 
                    if((!empty($allRequestdata['new_row'])&& in_array($i,$allRequestdata['new_row'])) || $i==0){
                        $ptlBuyerMaindataId[$i] = RailBuyerComponent::BuyerQuoteMainData($data[$i],$allRequestdata['is_commercial'][0]);
                        $k=$ptlBuyerMaindataId[$i];
                        //below array for matching engine in PTL start
                        /*matching start*/
                        $matchedItems = array();
                        $matchedItems['ptlDispatchDate'][]=$allRequestdata['ptlDispatchDate'][$i];
                        $matchedItems['ptlDeliveryhDate'][]=$allRequestdata['ptlDeliveryhDate'][$i];
                        $matchedItems['ptlFromLocation'][]=$allRequestdata['ptlFromLocation'][$i];
                        $matchedItems['ptlToLocation'][]=$allRequestdata['ptlToLocation'][$i];
                        BuyerMatchingComponent::doMatching(RAIL,$k['buyerQuoteId'],2,$matchedItems);
                        /*matching end*/
                        
                        if($k['buyerQuoteId']!=0) {
                        $ptlBuyerQuoteitemData = RailBuyerComponent::BuyerQuoteItems($data[$i],$k['buyerQuoteId'],$fromcities);
                        
                      }
                    }else{
                       
                        if($k!=0) {
                        $ptlBuyerQuoteitemData = RailBuyerComponent::BuyerQuoteItems($data[$i],$k['buyerQuoteId'],$fromcities);
                        
                      }
                        
                    }
                    break;
                    
                    case AIR_DOMESTIC: 
                    case AIR_INTERNATIONAL: 
                    case OCEAN: 
                    if((!empty($allRequestdata['new_row'])&& in_array($i,$allRequestdata['new_row'])) || $i==0){
                        $ptlBuyerMaindataId[$i] = AirDomesticBuyerComponent::BuyerQuoteMainData($data[$i],$allRequestdata['is_commercial'][0]);
                        $k=$ptlBuyerMaindataId[$i];
                        //below array for matching engine in PTL start
                        /*matching start*/
                        $matchedItems = array();
                        $matchedItems['ptlDispatchDate'][]=$allRequestdata['ptlDispatchDate'][$i];
                        $matchedItems['ptlDeliveryhDate'][]=$allRequestdata['ptlDeliveryhDate'][$i];
                        $matchedItems['ptlFromLocation'][]=$allRequestdata['ptlFromLocation'][$i];
                        $matchedItems['ptlToLocation'][]=$allRequestdata['ptlToLocation'][$i];
                        BuyerMatchingComponent::doMatching($serviceId,$k['buyerQuoteId'],2,$matchedItems);
                        /*matching end*/
                        
                        if($k['buyerQuoteId']!=0) {
                        $ptlBuyerQuoteitemData = AirDomesticBuyerComponent::BuyerQuoteItems($data[$i],$k['buyerQuoteId'],$fromcities);
                        
                      }
                    }else{
                        
                        if($k['buyerQuoteId']!=0) {
                        $ptlBuyerQuoteitemData = AirDomesticBuyerComponent::BuyerQuoteItems($data[$i],$k['buyerQuoteId'],$fromcities);
                        
                      }
                        
                    }
                    break;

                    case COURIER:
                    	 
                    if((!empty($allRequestdata['new_row'])&& in_array($i,$allRequestdata['new_row'])) || $i==0){
                        $ptlBuyerMaindataId[$i] = CourierBuyerComponent::BuyerQuoteMainData($data[$i],$allRequestdata['is_commercial'][0]);
                        $k=$ptlBuyerMaindataId[$i];
                       
                        
                        $data[$i]['ptlUnitsWeight'];
                        $data[$i]['ptlCheckUnitWeight'];
                        
                       
                        	$courier_max_weight =  $data[$i]['ptlUnitsWeight'];
                        	$courier_max_weight_units = $data[$i]['ptlCheckUnitWeight'];
                        	if($courier_max_weight_units == 2){
                        		$courier_max_weight = $courier_max_weight * 0.001;
                        	}else if($courier_max_weight_units == 3){
                        		$courier_max_weight = $courier_max_weight * 1000;
                        	}else{
                        		$courier_max_weight = $courier_max_weight;
                        	}
                        		
                        
                        
                        //below array for matching engine in PTL start
                        /*matching start*/
                        $matchedItems = array();
                        $matchedItems['ptlDispatchDate'][]=$allRequestdata['ptlDispatchDate'][$i];
                        $matchedItems['ptlDeliveryhDate'][]=$allRequestdata['ptlDeliveryhDate'][$i];
                        $matchedItems['ptlFromLocation'][]=$allRequestdata['ptlFromLocation'][$i];
                        $matchedItems['ptlToLocation'][]=$allRequestdata['ptlToLocation'][$i];
                        $matchedItems['courier_max_weight']=$courier_max_weight;
                        $matchedItems['sea_post_delivery_types']=$allRequestdata['post_delivery_types'];
                        BuyerMatchingComponent::doMatching($serviceId,$k['buyerQuoteId'],2,$matchedItems);
                        /*matching end*/
                        
                        if($k['buyerQuoteId']!=0) {
                        $ptlBuyerQuoteitemData = CourierBuyerComponent::BuyerQuoteItems($data[$i],$k['buyerQuoteId'],$fromcities);
                        
                      }
                    }else{
                        
                        if($k['buyerQuoteId']!=0) {
                        $ptlBuyerQuoteitemData = CourierBuyerComponent::BuyerQuoteItems($data[$i],$k['buyerQuoteId'],$fromcities);
                        
                      }
                        
                    }
                    	break;
                    default       : 
                    if((!empty($allRequestdata['new_row'])&& in_array($i,$allRequestdata['new_row'])) || $i==0){
                        $ptlBuyerMaindataId[$i] = PtlBuyerComponent::ptlBuyerQuoteMainData($data[$i]);
                        $k=$ptlBuyerMaindataId[$i];
                        //below array for matching engine in PTL start
                        /*matching start*/
                        $matchedItems = array();
                        $matchedItems['ptlDispatchDate'][]=$allRequestdata['ptlDispatchDate'][$i];
                        $matchedItems['ptlDeliveryhDate'][]=$allRequestdata['ptlDeliveryhDate'][$i];
                        $matchedItems['ptlFromLocation'][]=$allRequestdata['ptlFromLocation'][$i];
                        $matchedItems['ptlToLocation'][]=$allRequestdata['ptlToLocation'][$i];
                        BuyerMatchingComponent::doMatching($serviceId,$k['buyerQuoteId'],2,$matchedItems);
                        /*matching end*/
                        if($k['buyerQuoteId']!=0) {
                        $ptlBuyerQuoteitemData = PtlBuyerComponent::ptlBuyerQuoteItems($data[$i],$k['buyerQuoteId'],$fromcities);
                        
                      }
                    }else{
                        if($k['buyerQuoteId']!=0) {
                        $ptlBuyerQuoteitemData = PtlBuyerComponent::ptlBuyerQuoteItems($data[$i],$k['buyerQuoteId'],$fromcities);
                        
                      } 
                    }
                    break;
                        
                    }
                  
                }
               $transaction_id =  $k['transactionId'];
                return redirect('ptl/createbuyerquote')->with('transactionId', $transaction_id);                
                 
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
           
           
           if(Session::get('service_id') != COURIER){
           if($url_search_search == 'byersearchresults'){
           	
           	$request_buyer_data = Session::get('ptlBuyerSearchform');           	
           	
           	if(isset($request_buyer_data['ptlFlexiableDispatch'][0]) && $request_buyer_data['ptlFlexiableDispatch'][0] != ''){
           	$ptlFlexiableDispatch = $request_buyer_data['ptlFlexiableDispatch'][0];
           	$ptlFlexiableDelivery = $request_buyer_data['ptlFlexiableDelivery'][0];
           	}else{
           		$ptlFlexiableDispatch = 0;
           		
           		$ptlFlexiableDelivery = 0;
           	}
           	$session_search_values[] = $request_buyer_data['ptlDispatchDate'][0];
           	$session_search_values[] = $request_buyer_data['ptlDeliveryhDate'][0];
           	$session_search_values[] = $request_buyer_data['ptlLoadType'][0];
           	$session_search_values[] = $request_buyer_data['ptlPackageType'][0];
           	$session_search_values[] = $request_buyer_data['ptlLength'][0];
           	$session_search_values[] = $request_buyer_data['ptlWidth'][0];
           	$session_search_values[] = $request_buyer_data['ptlHeight'][0];
           	$session_search_values[] = $request_buyer_data['ptlUnitsWeight'][0];
           	$session_search_values[] = $request_buyer_data['ptlCheckVolWeight'][0];
           	$session_search_values[] = $request_buyer_data['ptlNopackages'][0];
           	$session_search_values[] = $request_buyer_data['ptlDoorpickup'][0];
           	$session_search_values[] = $request_buyer_data['ptlDoorDelivery'][0];           	
           	$session_search_values[] = $request_buyer_data['fromlocationName'][0];
           	$session_search_values[] = $request_buyer_data['ptlFromLocation'][0];
           	$session_search_values[] = $request_buyer_data['tolocationName'][0];
           	$session_search_values[] = $request_buyer_data['ptlToLocation'][0];
           	$session_search_values[] = $request_buyer_data['ptlCheckVolWeight'][0];
           	$session_search_values[] = $request_buyer_data['ptlDisplayVolumeWeight'][0];
           	
           	if(Session::get('service_id') == AIR_INTERNATIONAL || Session::get('service_id') == OCEAN){
           	$session_search_values[] = $request_buyer_data['ptlShipmentType'][0];
           	$session_search_values[] = $request_buyer_data['ptlSenderIdentity'][0];
           	$session_search_values[] = $request_buyer_data['ptlIECode'][0];
           	$session_search_values[] = $request_buyer_data['ptlProductMade'][0];
           	}
           	
           	
           	
           }else{
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	if(Session::get('service_id') == AIR_INTERNATIONAL || Session::get('service_id') == OCEAN){
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	$session_search_values[] = '';
           	}
           	$ptlFlexiableDispatch = 0;
           	 
           	$ptlFlexiableDelivery = 0;
           }
           }else{
           	
           	if($url_search_search == 'byersearchresults'){
           	
           		
           		
           		$request_buyer_data_courier = Session::get('ptlBuyerSearchform');          		
           		
           		if(isset($request_buyer_data['ptlFlexiableDispatch'][0]) && $request_buyer_data['ptlFlexiableDispatch'][0] != ''){
           			$ptlFlexiableDispatch = $request_buyer_data_courier['ptlFlexiableDispatch'][0];
           			$ptlFlexiableDelivery = $request_buyer_data_courier['ptlFlexiableDelivery'][0];
           		}else{
           			$ptlFlexiableDispatch = 0;
           			$ptlFlexiableDelivery = 0;
           		}
           		
           		$session_search_values[] = $request_buyer_data_courier['ptlDispatchDate'][0];
           		$session_search_values[] = $request_buyer_data_courier['ptlDeliveryhDate'][0];
           		$session_search_values[] = $request_buyer_data_courier['fromlocationName'][0];
           		$session_search_values[] = $request_buyer_data_courier['ptlFromLocation'][0];
           		$session_search_values[] = $request_buyer_data_courier['tolocationName'][0];
           		$session_search_values[] = $request_buyer_data_courier['ptlToLocation'][0];
           		$session_search_values[] = $request_buyer_data_courier['post_delivery_types'][0];
           		$session_search_values[] = $request_buyer_data_courier['ptlUnitsWeight'][0];
           		$session_search_values[] = $request_buyer_data_courier['ptlCheckVolWeightCourier'][0];
           		$session_search_values[] = $request_buyer_data_courier['ptlNopackages'][0];
           		$session_search_values[] = $request_buyer_data_courier['courier_types'][0];
           		$session_search_values[] = $request_buyer_data_courier['ptlLengthCourier'][0];
           		$session_search_values[] = $request_buyer_data_courier['ptlWidthCourier'][0];
           		$session_search_values[] = $request_buyer_data_courier['ptlHeightCourier'][0];
           		$session_search_values[] = $request_buyer_data_courier['ptlCheckVolWeightCourier'][0];
           		$session_search_values[] = $request_buyer_data_courier['ptlDisplayVolumeWeight'][0];
           		$session_search_values[] = $request_buyer_data_courier['packeagevalue'][0];
           		$session_search_values[] = $request_buyer_data_courier['ptlPurposesType'][0];
           		$session_search_values[] = $request_buyer_data_courier['ptlCheckUnitWeight'][0];
           		
           	}else{
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$session_search_values[] = '';
           		$ptlFlexiableDispatch = 0;
           		 
           		$ptlFlexiableDelivery = 0;
           	}
           	
           }
           
            $loadTypes = CommonComponent::getAllLoadTypes();
            $packageTypes = CommonComponent::getAllPackageTypes();
            $volumeWeightTypes = CommonComponent::getVolumeWeightTypes();
            $unitsWeightTypes = CommonComponent::getUnitsWeight();
            $senderIdentity = CommonComponent::getSenderIdentity();
            $shipmentTypes = CommonComponent::getShipmentTypes();
            $volumeWeightcourier = CommonComponent::getUnitsWeight ();
            $bid_type = \DB::table('lkp_bid_types')->orderBy ( 'bid_type', 'desc' )->lists('bid_type', 'id');
            if(Session::get ( 'service_id' ) == '21'){
            $CourierTypes = CommonComponent::getAllCourierPorposeTypes();
            }
            if(Session::get ( 'service_id' ) == COURIER){
            return view(
            		'ptl.buyers.create_buyer_quote',
            		[
            		'loadTypes' => $loadTypes,
            		'packageTypes' => $packageTypes,
            		'volumeWeightTypes' => $volumeWeightTypes,
            		'session_search_values' =>$session_search_values,
            		'unitsWeightTypes' => $unitsWeightTypes,
            		'url_search_search' => $url_search_search,
            		'serverpreviUrL' => $serverpreviUrL,
                    'senderIdentity' => $senderIdentity,
            		'shipmentTypes' => $shipmentTypes,
            		'CourierTypes' => $CourierTypes,
            		'ptlFlexiableDispatch' => $ptlFlexiableDispatch,
            		'ptlFlexiableDelivery' => $ptlFlexiableDelivery,
            		'volumeWeightcourier' => $volumeWeightcourier,
            		 'bid_type' => $bid_type,
            		]
            );
            }else{
            return view(
            		'ptl.buyers.create_buyer_quote',
            		[
            		'loadTypes' => $loadTypes,
            		'packageTypes' => $packageTypes,
            		'volumeWeightTypes' => $volumeWeightTypes,
            		'url_search_search' => $url_search_search,
            		'serverpreviUrL' => $serverpreviUrL,
            		'session_search_values' =>$session_search_values,
            		'unitsWeightTypes' => $unitsWeightTypes,
            		'senderIdentity' => $senderIdentity,
            		'ptlFlexiableDispatch' => $ptlFlexiableDispatch,
            		'ptlFlexiableDelivery' => $ptlFlexiableDelivery,
            		'shipmentTypes' => $shipmentTypes,
            		'bid_type' => $bid_type,
            		]
            );
            }
    	} catch (Exception $e) {
            
        }	
   } 
  
public function ptlPincodesAutocomplete()
   {
   try{
   		$term = trim(Input::get('term'));
   		$strLength =  strlen($term);
   		$fromlocation_loc = Input::get('ptlFromLocation');
   		$results = array();
   		if (!preg_match('/[^A-Za-z]/', $term)) // '/[^a-z\d]/i' should also work.
   		{
   		  echo "error";
   		  exit;
   		  	
   		}
   		if(isset($fromlocation_loc)){
   			if ($strLength > 5) {
   			$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
   			->where('pincode', 'LIKE', $term.'%')
   			->where('id','<>', $fromlocation_loc) 	
   			->take(30)->get();
   			} else {
   			$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
   			->where('pincode', 'LIKE', $term.'%')
   			->where('id','<>', $fromlocation_loc)
   			->take(10)->get();
   			}
   		}else {
   			$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
   			->where('pincode', 'LIKE', $term.'%')
   			->take(10)->get();
   		}
   		foreach ($queries as $query) {   		
   			$results[] = ['id' => $query->id , 'value' => $query->pincode.' - '.$query->postoffice_name.' , '.$query->districtname.' , '.$query->statename ];   			 
   		}
   		return Response::json($results);
   	}
   	catch (Exception $e)
   	{
   		echo 'Caught exception: ',  $e->getMessage(), "\n";
   	}
   	 
   }
   

public function pincodesAutocomplete()
   {
   try{
   		$term = trim(Input::get('term'));
   		$strLength =  strlen($term);
   		$pincode = Input::get('pincode');
   		$results = array();
   		if (!preg_match('/[^A-Za-z]/', $term)) // '/[^a-z\d]/i' should also work.
   		{
   		  echo "error";
   		  exit;
   		  	
   		}
   		if(isset($pincode)){
   			if ($strLength > 5) {
   			$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
   			->where('pincode', 'LIKE', $term.'%')
   			->where('id','<>', $pincode) 	
   			->take(30)->get();
   			} else {
   			$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
   			->where('pincode', 'LIKE', $term.'%')
   			->where('id','<>', $pincode)
   			->take(10)->get();
   			}
   		}else {
   			$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
   			->where('pincode', 'LIKE', $term.'%')
   			->take(10)->get();
   		}
   		foreach ($queries as $query) {   		
   			$results[] = ['id' => $query->id , 'value' => $query->pincode.' - '.$query->postoffice_name.' , '.$query->districtname.' , '.$query->statename, 'statename' => $query->statename,'state_id' => $query->state_id,'districtname'=>$query->districtname,'lkp_district_id'=>$query->lkp_district_id,'postoffice_name'=>$query->postoffice_name,'city'=>$query->taluk,'pincode'=>$query->pincode,'region'=>$query->regionname]; 
   		}
   		return Response::json($results);
   	}
   	catch (Exception $e)
   	{
   		echo 'Caught exception: ',  $e->getMessage(), "\n";
   	}
   	 
   }


   
   //Pincode related to the To City in Checoutpage
   public function ptlToPincodesCheckout()
   {
   	try{
   		$term = trim(Input::get('term'));
   		$strLength =  strlen($term);
   		$tolocation_loc = Input::get('ptlTocheckLocation');
   		$to_districtid = Input::get('buyer_to_districtid');
   		$results = array();
   		if (!preg_match('/[^A-Za-z]/', $term)) // '/[^a-z\d]/i' should also work.
   		{
   			echo "error";
   			exit;
   
   		}
   		if(isset($fromlocation_loc)){
   			if ($strLength > 5) {
   				$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
   				->where('pincode', 'LIKE', $term.'%')
   				->where('lkp_district_id','=', $to_districtid)
   				->where('id','<>', $fromlocation_loc)
   				->take(50)->get();
   			} else {
   				$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
   				->where('pincode', 'LIKE', $term.'%')
   				->where('lkp_district_id','=', $to_districtid)
   				->where('id','<>', $fromlocation_loc)
   				->take(10)->get();
   			}
   		}else {
   			if ($strLength > 5) {
   				$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
   				->where('pincode', 'LIKE', $term.'%')
   				->where('lkp_district_id','=', $to_districtid)
   				->take(50)->get();
   			}else{
	   			$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
	   			->where('pincode', 'LIKE', $term.'%')
	   			->where('lkp_district_id','=', $to_districtid)
	   			->take(10)->get();
   			}
   		}
   		foreach ($queries as $query) {
   			$results[] = ['id' => $query->id , 'value' => $query->pincode.' - '.$query->postoffice_name.' , '.$query->districtname.' , '.$query->statename ];
   		}
   		return Response::json($results);
   	}
   	catch (Exception $e)
   	{
   		echo 'Caught exception: ',  $e->getMessage(), "\n";
   	}
   	 
   }
   
   public function ptlPincodesAutocompleteCourier()
   {
   	try{
   		$term = trim(Input::get('term'));
   		$strLength =  strlen($term);
   		$fromlocation_loc = Input::get('ptlFromLocation');
   		$courier_delivery_type_val = Input::get('courier_delivery_type');   		
   		$search_from_to = Input::get('to');
   		$results = array();
   		if($courier_delivery_type_val == 1){
   			
   			if (!preg_match('/[^A-Za-z]/', $term)) // '/[^a-z\d]/i' should also work.
   			{
   				echo "error";
   				exit;
   			
   			}
   			
	   		if(isset($fromlocation_loc)){
	   			if ($strLength > 5) {
	   				$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
	   				->where('pincode', 'LIKE', $term.'%')
	   				->where('id','<>', $fromlocation_loc)
	   				->take(30)->get();
	   			} else {
	   				$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
	   				->where('pincode', 'LIKE', $term.'%')
	   				->where('id','<>', $fromlocation_loc)
	   				->take(10)->get();
	   			}
	   		}else {
	   			$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
	   			->where('pincode', 'LIKE', $term.'%')
	   			->take(10)->get();
	   		}
	   		foreach ($queries as $query) {
	   			$results[] = ['id' => $query->id , 'value' => $query->pincode.' - '.$query->postoffice_name.' , '.$query->districtname.' , '.$query->statename ];
	   		}
	   		return Response::json($results);
   		}else{
   			if($search_from_to != 2){
   				if (!preg_match('/[^A-Za-z]/', $term)) // '/[^a-z\d]/i' should also work.
   				{
   					echo "error";
   					exit;
   				
   				}
   			if(isset($fromlocation_loc)){
   				if ($strLength > 5) {
   					$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
   					->where('pincode', 'LIKE', $term.'%')
   					->where('id','<>', $fromlocation_loc)
   					->take(30)->get();
   				} else {
   					$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
   					->where('pincode', 'LIKE', $term.'%')
   					->where('id','<>', $fromlocation_loc)
   					->take(10)->get();
   				}
   			}else {
   				$queries = DB::table('lkp_ptl_pincodes')->orderBy ( 'postoffice_name', 'asc' )
   				->where('pincode', 'LIKE', $term.'%')
   				->take(10)->get();
   			}
   			foreach ($queries as $query) {
   				$results[] = ['id' => $query->id , 'value' => $query->pincode.' - '.$query->postoffice_name.' , '.$query->districtname.' , '.$query->statename ];
   			}
   			return Response::json($results);
   		}else{
   			//
   			$queries = DB::table('lkp_countries')->orderBy ( 'country_name', 'asc' )
					->where('country_name', 'LIKE', $term.'%')
					->take(10)->get();
   			foreach ( $queries as $zone_location_id ) {
   				$results [] = ['id' => $zone_location_id->id,'value' => $zone_location_id->country_name ];
   			}
   			
   			
   			return Response::json($results);
   		}
   		}
   	}
   	catch (Exception $e)
   	{
   		echo 'Caught exception: ',  $e->getMessage(), "\n";
   	}
   	 
   }
   
   /******* Below Script for get calculate ************** */
   public function getVolumeWeight()
   {
   	try{   
   		
                    $ptlweightType = $_GET['ptlweightType'];
                
                    $ptlLength = $_GET['ptlLength'];
                
                    $ptlWidth = $_GET['ptlWidth'];
               
                    $ptlHeight = $_GET['ptlHeight'];
                $serviceId = Session::get('service_id');
                switch($serviceId){
                    case ROAD_PTL:
                    case RAIL : 
                    if($ptlweightType==FEET) {
                            $displayVolumeWeight=($ptlLength*$ptlWidth*$ptlHeight);
                    } else if($ptlweightType==INCHES) {
                            $lengthToInches=$ptlLength*0.0833;
                            $widthhToInches=$ptlWidth*0.0833;
                            $heightToInches=$ptlHeight*0.0833;
                            $displayVolumeWeight=$lengthToInches*$widthhToInches*$heightToInches;   			
                    } else if($ptlweightType==METER) {
                            $lengthToMeters=$ptlLength*3.2808;
                            $widthhToMeters=$ptlWidth*3.2808;
                            $heightToMeters=$ptlHeight*3.2808;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    }
                    else if($ptlweightType==CENTIMETER) {
                            $lengthToMeters=$ptlLength*0.0328;
                            $widthhToMeters=$ptlWidth*0.0328;
                            $heightToMeters=$ptlHeight*0.0328;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    }
                    $vol=round($displayVolumeWeight,2)." CFT";
                    break;
                    case AIR_DOMESTIC:
                    case AIR_INTERNATIONAL     : 
                    if($ptlweightType==FEET) {
                            $lengthToMeters=$ptlLength*30.4800;
                            $widthhToMeters=$ptlWidth*30.4800;
                            $heightToMeters=$ptlHeight*30.4800;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    } else if($ptlweightType==INCHES) {
                            $lengthToInches=$ptlLength*2.54;
                            $widthhToInches=$ptlWidth*2.54;
                            $heightToInches=$ptlHeight*2.54;
                            $displayVolumeWeight=$lengthToInches*$widthhToInches*$heightToInches;   			
                    } else if($ptlweightType==METER) {
                            $lengthToMeters=$ptlLength*100.0000;
                            $widthhToMeters=$ptlWidth*100.0000;
                            $heightToMeters=$ptlHeight*100.0000;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    }
                    else if($ptlweightType==CENTIMETER) {
                            $displayVolumeWeight=($ptlLength*$ptlWidth*$ptlHeight);
                    }
                    $vol=round($displayVolumeWeight,2)." CCM";
                    break;
                    case OCEAN       : 
                    if($ptlweightType==FEET) {
                            $lengthToMeters=$ptlLength*0.3048;
                            $widthhToMeters=$ptlWidth*0.3048;
                            $heightToMeters=$ptlHeight*0.3048;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    } else if($ptlweightType==INCHES) {
                            $lengthToInches=$ptlLength*0.0254;
                            $widthhToInches=$ptlWidth*0.0254;
                            $heightToInches=$ptlHeight*0.0254;
                            $displayVolumeWeight=$lengthToInches*$widthhToInches*$heightToInches;   			
                    } else if($ptlweightType==METER) {
                            $displayVolumeWeight=($ptlLength*$ptlWidth*$ptlHeight);
                    }
                    else if($ptlweightType==CENTIMETER) {
                            $lengthToMeters=$ptlLength*0.0100;
                            $widthhToMeters=$ptlWidth*0.0100;
                            $heightToMeters=$ptlHeight*0.0100;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    }
                    $vol=round($displayVolumeWeight,2)." CBM";
                    break;
                    case COURIER       :
                    	if($ptlweightType==1) {
                            $lengthToMeters=$ptlLength*30.4800;
                            $widthhToMeters=$ptlWidth*30.4800;
                            $heightToMeters=$ptlHeight*30.4800;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    } else if($ptlweightType==2) {
                            $lengthToInches=$ptlLength*2.54;
                            $widthhToInches=$ptlWidth*2.54;
                            $heightToInches=$ptlHeight*2.54;
                            $displayVolumeWeight=$lengthToInches*$widthhToInches*$heightToInches;   			
                    } else if($ptlweightType==3) {
                            $lengthToMeters=$ptlLength*100.0000;
                            $widthhToMeters=$ptlWidth*100.0000;
                            $heightToMeters=$ptlHeight*100.0000;
                            $displayVolumeWeight=$lengthToMeters*$widthhToMeters*$heightToMeters;
                    }
                    else if($ptlweightType==4) {
                            $displayVolumeWeight=($ptlLength*$ptlWidth*$ptlHeight);
                    }
                    $vol=round($displayVolumeWeight,2)." CCM";
                    	break;
                }
   		echo $vol;
   		die();
   	}catch (Exception $e) {   	
   		echo 'Caught exception: ',  $e->getMessage(), "\n";
   	}
   	
   }
   
   /******* Below Script for get seller list from city************** */
   
   public static function getPtlSellerList($cities = array())
   {
   //print_r($_POST['seller_list']); exit;
   	$results=array();
   	try
   	{            
             $serviceId = Session::get('service_id');            
            //Check district match condition for seller in buyer private posts. 
             $sellerlist = (count($cities) > 0) ? $cities : $_POST['seller_list'];
            if(isset($sellerlist)){
            	$sellersStr = $sellerlist; 
            	$districts = DB::table('lkp_ptl_pincodes')
            	->whereIn('lkp_ptl_pincodes.id', $sellersStr)
            	->select('lkp_ptl_pincodes.lkp_district_id')
            	->get();           	
            	foreach ($districts as $dist) {
            		$district_array[] = $dist->lkp_district_id;
            	}
            }
            
            
            switch($serviceId){
                case ROAD_PTL:
   		$seller_data = DB::table('ptl_seller_post_items')
   		->join('users','ptl_seller_post_items.created_by','=','users.id')   		
   		->leftjoin ('seller_details', 'users.id', '=', 'sellers.user_id')
   		->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
   		->leftjoin ('ptl_seller_posts', 'users.id', '=', 'ptl_seller_posts.seller_id')
   		->distinct('ptl_seller_post_items.created_by')
   		->whereIn('ptl_seller_post_items.lkp_district_id',$district_array)
   		->where('ptl_seller_posts.lkp_ptl_post_type_id',PTL_LOCATION) 
   		->whereRaw("(users.id != ". Auth::User()->id .")")
   		->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")
   		->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
   		->get();
                break;
                case RAIL:
   		$seller_data = DB::table('rail_seller_post_items as spi')
   		->join('users','spi.created_by','=','users.id')   		
   		->leftjoin ('seller_details', 'users.id', '=', 'sellers.user_id')
   		->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
   		->leftjoin ('rail_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
   		->distinct('spi.created_by')
   		->whereIn('spi.lkp_district_id',$district_array)
   		->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)   
   		->whereRaw("(users.id != ". Auth::User()->id .")")
   		->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")
   		->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
   		->get();
                break;
                case AIR_DOMESTIC:
   		$seller_data = DB::table('airdom_seller_post_items as spi')
   		->join('users','spi.created_by','=','users.id')   		
   		->leftjoin ('seller_details', 'users.id', '=', 'sellers.user_id')
   		->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
   		->leftjoin ('airdom_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
   		->distinct('spi.created_by')
   		->whereIn('spi.lkp_district_id',$district_array)
   		->whereRaw("(users.id != ". Auth::User()->id .")")
   		->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")
   		->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)   		
   		->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
   		->get();
                break;
                case AIR_INTERNATIONAL:
   		$seller_data = DB::table('airint_seller_post_items as spi')
   		->join('users','spi.created_by','=','users.id')   		
   		->leftjoin ('seller_details', 'users.id', '=', 'sellers.user_id')
   		->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
   		->leftjoin ('airint_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
   		->distinct('spi.created_by')
   		->whereIn('spi.lkp_district_id',$district_array)
   		->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)
   		->whereRaw("(users.id != ". Auth::User()->id .")")
   		->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")
   		->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
   		->get();
                break;
                case OCEAN:
   		$seller_data = DB::table('ocean_seller_post_items as spi')
   		->join('users','spi.created_by','=','users.id')   		
   		->leftjoin ('seller_details', 'users.id', '=', 'sellers.user_id')
   		->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
   		->leftjoin ('ocean_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
   		->distinct('spi.created_by')
   		->whereIn('spi.lkp_district_id',$district_array)
   		->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)  
   		->whereRaw("(users.id != ". Auth::User()->id .")")
   		->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")
   		->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
   		->get();
                break;
                case COURIER:
                	$seller_data = DB::table('courier_seller_post_items as spi')
                	->join('users','spi.created_by','=','users.id')
                	->leftjoin ('seller_details', 'users.id', '=', 'sellers.user_id')
                	->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
                	->leftjoin ('courier_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
                	->distinct('spi.created_by')
                	->whereIn('spi.lkp_district_id',$district_array)
                	->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)
                	->whereRaw("(users.id != ". Auth::User()->id .")")
                	->whereRaw("(users.lkp_role_id = ". SELLER ." or users.secondary_role_id = ". SELLER .")")
                	->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
                	->get();
                	break;
            }
   		
   		foreach ($seller_data as $query) {    		  			
   			$results[] = ['id' => $query->id, 'name' => $query->username.' '.$query->principal_place.' '.$query->id];   			 
   		}
   		if(count($cities) > 0){
   			return $results;
   		}else{
   		return Response::json($results);  
   		} 
   	} catch (Exception $e)
   	{
   		echo 'Caught exception: ',  $e->getMessage(), "\n";
   	}
   }
   
   
   /******* Below Script for get edit seller list from pincode************** */
    
   public function getPtlEditSellerList()
   {
   	
   	$results=array();
   	try
   	{   		
   		$serviceId = Session::get('service_id');   		
   		//getting sekller values for checking duplicates in edit seller post
   		$quoteId=  $_POST['ptl_buyer_quote_id'];
   		//Check district match condition for seller in buyer private posts.   		
   		if(isset($_POST['seller_list'])){
   			$sellersStr = $_POST['seller_list'];   		
   			$districts = DB::table('lkp_ptl_pincodes')
   			->whereIn('lkp_ptl_pincodes.id', $sellersStr)
   			->select('lkp_ptl_pincodes.lkp_district_id')
   			->get();   			
   			foreach ($districts as $dist) {
   				$district_array[] = $dist->lkp_district_id;   				
   			}
   		}   		
   		
   		switch($serviceId){
   			case ROAD_PTL:
   				$sellerIds = DB::table('ptl_buyer_quote_selected_sellers as bqs')
   				->where('bqs.buyer_quote_id', $quoteId)
   				->select('bqs.seller_id')
   				->get();
   				$sellerall_ids	=	array();
   				foreach ($sellerIds as $sellerdata) {
   					$sellerall_ids[] = $sellerdata->seller_id;
   				}
   				$seller_data = DB::table('ptl_seller_post_items')
   				->join('users','ptl_seller_post_items.created_by','=','users.id')
   				->leftjoin ('seller_details', 'users.id', '=', 'sellers.user_id')
   				->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
   				->leftjoin ('ptl_seller_posts', 'users.id', '=', 'ptl_seller_posts.seller_id')
   				->distinct('ptl_seller_post_items.created_by')
   				->whereIn('ptl_seller_post_items.lkp_district_id',$district_array)
   				->whereNotIn('ptl_seller_post_items.created_by', $sellerall_ids)
   				->where('users.lkp_role_id',SELLER)
                                ->orWhere('users.secondary_role_id', SELLER)
   				->where('ptl_seller_posts.lkp_ptl_post_type_id',PTL_LOCATION)
   				->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
   				->get();
   				break;
   			case RAIL:
   				$sellerIds = DB::table('rail_buyer_quote_selected_sellers as bqs')
   				->where('bqs.buyer_quote_id', $quoteId)
   				->select('bqs.seller_id')
   				->get();
   				$sellerall_ids	=	array();
   				foreach ($sellerIds as $sellerdata) {
   					$sellerall_ids[] = $sellerdata->seller_id;
   				}
   				$seller_data = DB::table('rail_seller_post_items as spi')
   				->join('users','spi.created_by','=','users.id')
   				->leftjoin ('seller_details', 'users.id', '=', 'sellers.user_id')
   				->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
   				->leftjoin ('rail_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
   				->distinct('spi.created_by')
   				->whereIn('spi.lkp_district_id',$district_array)
   				->whereNotIn('spi.created_by', $sellerall_ids)
   				->where('users.lkp_role_id',SELLER)
                                ->orWhere('users.secondary_role_id', SELLER)
   				->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)
   				->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
   				->get();
   				break;
   			case AIR_DOMESTIC:
   				$sellerIds = DB::table('airdom_buyer_quote_sellers_quotes_prices as bqs')
   				->where('bqs.buyer_quote_id', $quoteId)
   				->select('bqs.seller_id')
   				->get();
   				$sellerall_ids	=	array();
   				foreach ($sellerIds as $sellerdata) {
   					$sellerall_ids[] = $sellerdata->seller_id;
   				}
   				$seller_data = DB::table('airdom_seller_post_items as spi')
   				->join('users','spi.created_by','=','users.id')
   				->leftjoin ('seller_details', 'users.id', '=', 'sellers.user_id')
   				->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
   				->leftjoin ('airdom_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
   				->distinct('spi.created_by')
   				->whereIn('spi.lkp_district_id',$district_array)
   				->whereNotIn('spi.created_by', $sellerall_ids)
   				->where('users.lkp_role_id',SELLER)
                                ->orWhere('users.secondary_role_id', SELLER)
   				->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)
   				->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
   				->get();
   				break;
   			case AIR_INTERNATIONAL:
   				$sellerIds = DB::table('airint_buyer_quote_selected_sellers as bqs')
   				->where('bqs.buyer_quote_id', $quoteId)
   				->select('bqs.seller_id')
   				->get();
   				$sellerall_ids	=	array();
   				foreach ($sellerIds as $sellerdata) {
   					$sellerall_ids[] = $sellerdata->seller_id;
   				}
   				$seller_data = DB::table('airint_seller_post_items as spi')
   				->join('users','spi.created_by','=','users.id')
   				->leftjoin ('seller_details', 'users.id', '=', 'sellers.user_id')
   				->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
   				->leftjoin ('airint_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
   				->distinct('spi.created_by')
   				->whereIn('spi.lkp_district_id',$district_array)
   				->whereNotIn('spi.created_by', $sellerall_ids)
   				->where('users.lkp_role_id',SELLER)
                                ->orWhere('users.secondary_role_id', SELLER)
   				->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)
   				->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
   				->get();
   				break;
   			case OCEAN:
   				$sellerIds = DB::table('ocean_buyer_quote_selected_sellers as bqs')
   				->where('bqs.buyer_quote_id', $quoteId)
   				->select('bqs.seller_id')
   				->get();
   				$sellerall_ids	=	array();
   				foreach ($sellerIds as $sellerdata) {
   					$sellerall_ids[] = $sellerdata->seller_id;
   				}
   				$seller_data = DB::table('ocean_seller_post_items as spi')
   				->join('users','spi.created_by','=','users.id')
   				->leftjoin ('seller_details', 'users.id', '=', 'sellers.user_id')
   				->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
   				->leftjoin ('ocean_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
   				->distinct('spi.created_by')
   				->whereIn('spi.lkp_district_id',$district_array)
   				->whereNotIn('spi.created_by', $sellerall_ids)
   				->where('users.lkp_role_id',SELLER)
                                ->orWhere('users.secondary_role_id', SELLER)
   				->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)
   				->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
   				->get();
   				break;
			case COURIER:
   					$sellerIds = DB::table('courier_buyer_quote_sellers_quotes_prices as bqs')
   					->where('bqs.buyer_quote_id', $quoteId)
   					->select('bqs.seller_id')
   					->get();
   					$sellerall_ids	=	array();
   					foreach ($sellerIds as $sellerdata) {
   						$sellerall_ids[] = $sellerdata->seller_id;
   					}
   					$seller_data = DB::table('courier_seller_post_items as spi')
   					->join('users','spi.created_by','=','users.id')
   					->leftjoin ('seller_details', 'users.id', '=', 'sellers.user_id')
   					->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
   					->leftjoin ('courier_seller_posts as sp', 'users.id', '=', 'sp.seller_id')
   					->distinct('spi.created_by')
   					->whereIn('spi.lkp_district_id',$district_array)
   					->whereNotIn('spi.created_by', $sellerall_ids)
   					->where('users.lkp_role_id',SELLER)
                                        ->orWhere('users.secondary_role_id', SELLER)
   					->where('sp.lkp_ptl_post_type_id',PTL_LOCATION)
   					->select('users.id','users.username','sellers.principal_place','sellers.name','seller_details.firstname')
   					->get();
   					break;
   		}
   		
   		foreach ($seller_data as $query) {
   			$results[] = ['id' => $query->id, 'name' => $query->username.' '.$query->principal_place.' '.$query->id];
   		}
   		return Response::json($results);
   	} catch (Exception $e)
   	{
   		echo 'Caught exception: ',  $e->getMessage(), "\n";
   	}
   }
   
   public function getPinlocationInItems() 
   {
   	try {
   		$fromPincodeId = $_GET['fromPincode'];
   		$toPincodeId = $_GET['toPincode'];
                $serviceId = Session::get ( 'service_id' );
                switch($serviceId){
                    case ROAD_PTL       : 
                    case RAIL       : 
                    case AIR_DOMESTIC       : 
                    $fromLocationQuery = DB::table('lkp_ptl_pincodes')->select('postoffice_name','pincode')->where('id', $fromPincodeId)->get();
                    $fromLocation = $fromLocationQuery[0]->postoffice_name;
                    $fromLocationpin = $fromLocationQuery[0]->pincode;
                    $toLocationQuery = DB::table('lkp_ptl_pincodes')->select('postoffice_name','pincode')->where('id', $toPincodeId)->get();
                    $toLocation = $toLocationQuery[0]->postoffice_name;
                    $toLocationpin = $toLocationQuery[0]->pincode;
                    break;
                    case AIR_INTERNATIONAL       : 
                    $fromLocationQuery = DB::table('lkp_airports')->select('airport_name','location')->where('id', $fromPincodeId)->get();
                    $fromLocation = $fromLocationQuery[0]->airport_name;
                    $fromLocationpin = $fromLocationQuery[0]->location;
                    $toLocationQuery = DB::table('lkp_airports')->select('airport_name','location')->where('id', $toPincodeId)->get();
                    $toLocation = $toLocationQuery[0]->airport_name;
                    $toLocationpin = $toLocationQuery[0]->location;
                    break;
                case OCEAN       : 
                    $fromLocationQuery = DB::table('lkp_seaports')->select('seaport_name','country_name')->where('id', $fromPincodeId)->get();
                    $fromLocation = $fromLocationQuery[0]->seaport_name;
                    $fromLocationpin = $fromLocationQuery[0]->country_name;
                    $toLocationQuery = DB::table('lkp_seaports')->select('seaport_name','country_name')->where('id', $toPincodeId)->get();
                    $toLocation = $toLocationQuery[0]->seaport_name;
                    $toLocationpin = $toLocationQuery[0]->country_name;
                    break;
                }
   		echo $fromLocation . " (".$fromLocationpin . ") ~!~" . $toLocation . " (".$toLocationpin.")";		
   		
   	} catch (Exception $e) {
   		echo 'Caught exception: ', $e->getMessage(), "\n";
   	}
  }
 
 //Edit sellers in buyer post LTl section view data
public function editBuyerquoteSeller($quoteId)
    {   
    	try
    	{
    		Log::info('Edit sellers for buyers in LTL: '.Auth::id(),array('c'=>'1'));    		
    		if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			} 
    		//Edit sellers in LTL buyers section.
				switch($serviceId){
					case ROAD_PTL       	: 
					case RAIL       		: 
					case AIR_DOMESTIC   	: 
					case AIR_INTERNATIONAL  :
					case OCEAN       		:
								$result=$this->getEditSeller($quoteId);
								return view('ptl.buyers.edit_sellers',[
                                  'allBuyerQuoteDetails' => $result['arrBuyerQuoteDeatils'],
                                  'fromLocation' => $result['fromLocation'],
                                  'toLocation' => $result['toLocation'],
                                  'deliveryDate' => $result['deliveryDate'],
                                  'dispatchDate' => $result['dispatchDate'],
                                  'arraySellerDetails' => $result['arraySellerDetails'],
                                  'buyer_post_edit_seller' => $result['buyer_post_edit_seller'],
                                   ]);
					break;
					case COURIER       		:
						$result=$this->getEditSeller($quoteId);
						return view('ptl.buyers.edit_sellers',[
								'allBuyerQuoteDetails' => $result['arrBuyerQuoteDeatils'],
								'fromLocation' => $result['fromLocation'],
								'toLocation' => $result['toLocation'],
								'deliveryDate' => $result['deliveryDate'],
								'dispatchDate' => $result['dispatchDate'],
								'arraySellerDetails' => $result['arraySellerDetails'],
								'buyer_post_edit_seller' => $result['buyer_post_edit_seller'],
								]);
						break;
                               
					case ROAD_INTRACITY : 
					break;
					case ROAD_TRUCK_HAUL: 
					break;
					default             : 
                                    $result=$this->getEditSeller($quoteId);
                                    return view('ptl.buyers.edit_sellers',[
                                  'allBuyerQuoteDetails' => $result['arrBuyerQuoteDeatils'],
                                  'fromLocation' => $result['fromLocation'],
                                  'toLocation' => $result['toLocation'],
                                  'deliveryDate' => $result['deliveryDate'],
                                  'dispatchDate' => $result['dispatchDate'],
                                  'arraySellerDetails' => $result['arraySellerDetails'],
                                  'buyer_post_edit_seller' => $result['buyer_post_edit_seller'],
                                   ]);
					break;		   			  
				}   		
    		
    	}
    	catch (Exception $e) {
    	
    	}    
    }
   
  //Buyer edit total updte seler section  in LTL
  public function updatePtlSeller(Request $request) {
    	try {
    		Log::info('Update buyer quote Sellers list ltl and insert new sellers: ' . Auth::id(), array('c' => '1'));
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
    		$str1 = "FTL_";
    		$trans_randid = $str1 . $rand_id;
                if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
			
			//Loading respective service data grid
			
    		if ($seller_list_count != 0) {
    			for ($i = 0; $i < $seller_list_count; $i++) {
                            switch($serviceId){
								case ROAD_PTL       : 
                                $Quote_seller_list = new PtlBuyerQuoteSelectedSeller();break;
                                case RAIL           : 
                                $Quote_seller_list = new RailBuyerQuoteSelectedSeller();break;
                                case AIR_DOMESTIC       : 
                                $Quote_seller_list = new AirdomBuyerQuoteSelectedSeller();break;
                                case AIR_INTERNATIONAL  : 
                                $Quote_seller_list = new AirintBuyerQuoteSelectedSeller();break;
                                case OCEAN       : 
                                $Quote_seller_list = new OceanBuyerQuoteSelectedSeller();break;
                                case COURIER       :
                                $Quote_seller_list = new CourierBuyerQuoteSelectedSeller();break;
                                default       : 
                                $Quote_seller_list = new PtlBuyerQuoteSelectedSeller();break;
                            }
    				$Quote_seller_list->buyer_quote_id = $request->quoteid;
    				$Quote_seller_list->seller_id = $seller_list[$i];
    				$Quote_seller_list->updated_by = Auth::id();
    				$Quote_seller_list->updated_at = $created_at;
    				$Quote_seller_list->updated_ip = $createdIp;
    				$Quote_seller_list->save();    	

    				//below code  for sending mails to selelcted sellers in private post
    				$buyers_selected_sellers_email = DB::table('users')->where('id', $seller_list[$i])->get();
    				$buyers_selected_sellers_email[0]->randnumber = $trans_randid;
    				$buyers_selected_sellers_email[0]->buyername = Auth::User()->username;
    				CommonComponent::send_email(BUYER_CREATED_POST_FOR_SELLERS, $buyers_selected_sellers_email);
    			}
    		} else {
    			return redirect('ptlupdateseller/' . $request->quoteid)->with('ptlfailupdate', 'Buyer Quote Updated Failed.');
    		}
    
    		
    		return redirect('buyerposts')->with('ptlsuccessupdate', 'Buyer post updated successfully.');
    	} catch (Exception $e) {
    		echo 'Caught exception: ', $e->getMessage(), "\n";
    	}
    }
    public function getEditSeller($quoteId) {
    	try {
                $serviceId = Session::get ( 'service_id' );
                
                $arrBuyerQuoteDeatils = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteDetailsFromIdForPtl($quoteId);
                $fromLocation = PtlBuyerComponent::getCityNameForPtl($arrBuyerQuoteDeatils[0]->from_location_id);
                $toLocation = PtlBuyerComponent::getCityNameForPtl($arrBuyerQuoteDeatils[0]->to_location_id);
                if(isset($arrBuyerQuoteDeatils[0]->is_dispatch_flexible) && $arrBuyerQuoteDeatils[0]->is_dispatch_flexible == 1) {
                    $dispatchDate = BuyerComponent::getPreviousNextThreeDays($arrBuyerQuoteDeatils[0]->dispatch_date);
                } else {
                    $dispatchDate = CommonComponent::checkAndGetDate($arrBuyerQuoteDeatils[0]->dispatch_date);
                }
                $deliveryDate = CommonComponent::checkAndGetDate($arrBuyerQuoteDeatils[0]->delivery_date);

                if (isset($arrBuyerQuoteDeatils[0]->is_delivery_flexible) && $arrBuyerQuoteDeatils[0]->is_delivery_flexible == 1 && !empty($deliveryDate)) {
                    $deliveryDate = BuyerComponent::getPreviousNextThreeDays($arrBuyerQuoteDeatils[0]->delivery_date);
                }

                $arraySellerDetails = PtlBuyerGetQuoteBooknowComponent::getBuyerQuoteItems($quoteId,true);
                
                switch($serviceId){
                    
                case ROAD_PTL:
                $buyer_post_edit_seller = DB::table('ptl_buyer_quotes as bq')
                ->leftjoin('ptl_buyer_quote_selected_sellers as seller', 'seller.buyer_quote_id', '=', 'bq.id')
                ->leftjoin('users as u', 'u.id', '=', 'seller.seller_id')
                ->where('bq.id', $quoteId)
                ->select('seller.seller_id', 'u.username', 'u.id')
                ->get();
                    break;
                case RAIL:
                $buyer_post_edit_seller = DB::table('rail_buyer_quotes as bq')
                ->leftjoin('rail_buyer_quote_selected_sellers as seller', 'seller.buyer_quote_id', '=', 'bq.id')
                ->leftjoin('users as u', 'u.id', '=', 'seller.seller_id')
                ->where('bq.id', $quoteId)
                ->select('seller.seller_id', 'u.username', 'u.id')
                ->get();
                    break;
                case AIR_DOMESTIC:
                $buyer_post_edit_seller = DB::table('airdom_buyer_quotes as bq')
                ->leftjoin('airdom_buyer_quote_selected_sellers as seller', 'seller.buyer_quote_id', '=', 'bq.id')
                ->leftjoin('users as u', 'u.id', '=', 'seller.seller_id')
                ->where('bq.id', $quoteId)
                ->select('seller.seller_id', 'u.username', 'u.id')
                ->get();
                    break;
                case AIR_INTERNATIONAL:
                $buyer_post_edit_seller = DB::table('airint_buyer_quotes as bq')
                ->leftjoin('airint_buyer_quote_selected_sellers as seller', 'seller.buyer_quote_id', '=', 'bq.id')
                ->leftjoin('users as u', 'u.id', '=', 'seller.seller_id')
                ->where('bq.id', $quoteId)
                ->select('seller.seller_id', 'u.username', 'u.id')
                ->get();
                    break;
                case OCEAN:
                $buyer_post_edit_seller = DB::table('ocean_buyer_quotes as bq')
                ->leftjoin('ocean_buyer_quote_selected_sellers as seller', 'seller.buyer_quote_id', '=', 'bq.id')
                ->leftjoin('users as u', 'u.id', '=', 'seller.seller_id')
                ->where('bq.id', $quoteId)
                ->select('seller.seller_id', 'u.username', 'u.id')
                ->get();
                    break;
                    case COURIER:
                    	$buyer_post_edit_seller = DB::table('courier_buyer_quotes as bq')
                    	->leftjoin('courier_buyer_quote_selected_sellers as seller', 'seller.buyer_quote_id', '=', 'bq.id')
                    	->leftjoin('users as u', 'u.id', '=', 'seller.seller_id')
                    	->where('bq.id', $quoteId)
                    	->select('seller.seller_id', 'u.username', 'u.id')
                    	->get();
                    	break;
                default:
                $buyer_post_edit_seller = DB::table('ptl_buyer_quotes as bq')
                ->leftjoin('ptl_buyer_quote_selected_sellers as seller', 'seller.buyer_quote_id', '=', 'bq.id')
                ->leftjoin('users as u', 'u.id', '=', 'seller.seller_id')
                ->where('bq.id', $quoteId)
                ->select('seller.seller_id', 'u.username', 'u.id')
                ->get();
                    break;
                }$res=array();
                    CommonComponent::activityLog("LTL_BUYER_EDIT_FOR_BUYER_POSTS",
                                                                LTL_BUYER_EDIT_FOR_BUYER_POSTS,0,
                                                                HTTP_REFERRER,CURRENT_URL);
                    $res['arrBuyerQuoteDeatils']=$arrBuyerQuoteDeatils;
                    $res['fromLocation']=$fromLocation;
                    $res['toLocation']=$toLocation;
                    $res['deliveryDate']=$deliveryDate;
                    $res['dispatchDate']=$dispatchDate;
                    $res['arraySellerDetails']=$arraySellerDetails;
                    $res['buyer_post_edit_seller']=$buyer_post_edit_seller;
                    
                   return $res;                    
        }catch (Exception $e) {
    		echo 'Caught exception: ', $e->getMessage(), "\n";
    	}
    }
}
