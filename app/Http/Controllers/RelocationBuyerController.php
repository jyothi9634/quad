<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Components\CommonComponent;
use App\Components\Matching\SellerMatchingComponent;
use App\Components\Term\TermBuyerComponent;

use App\Models\PtlSellerPost;
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
use App\Components\Matching\BuyerMatchingComponent;
use App\Http\Controllers\EditableGrid;
use App\Models\PtlTransitday;
use App\Models\PtlPincodexsector;
use App\Models\PtlTier;
use App\Models\PtlSector;

use App\Models\RelocationBuyerPost;
use App\Models\RelocationBuyerPostInventoryParticular;
use App\Models\RelocationintBuyerPostInventoryParticular;
use App\Models\RelocationBuyerSelectedSeller;


use App\Models\RelocationofficeBuyerPost;
use App\Models\RelocationofficeBuyerSelectedSeller;
use App\Models\RelocationPetBuyerSelectedSellers;
use App\Models\RelocationofficeBuyerPostInventoryParticular;

// Carbon date time
use Carbon\Carbon as Carbon;

class RelocationBuyerController extends Controller {
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware ( 'auth' );
    }

    public function relocationCreateBuyerPost() {    	
    	
    	//Added condition for change services bug no : 0050171
    	if(Session::get('service_id') == ROAD_PTL || Session::get('service_id') == RAIL 
        	|| Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL 
        	|| Session::get('service_id') == OCEAN || Session::get('service_id') == COURIER)
        {
            return redirect('ptl/createbuyerquote');
        }else if(Session::get('service_id') == ROAD_INTRACITY){
            return redirect('/intracity/buyer_post');
        }else if(Session::get('service_id') == ROAD_FTL){
            return redirect('createbuyerquote');
        }else if(Session::get('service_id') == ROAD_TRUCK_HAUL){
            return redirect('truckhaul/createbuyerquote');
        }else if(Session::get('service_id') == ROAD_TRUCK_LEASE){
            return redirect('trucklease/createbuyerquote');
        }

        Log::info ( 'create seller function used for creating a posts: ' . Auth::id (), array (
            'c' => '1'
        ) );
        try {
        	$serviceId = Session::get('service_id');
            
            switch($serviceId){
            	case RELOCATION_DOMESTIC:
        	if(!isset($_REQUEST['search'])){
                session()->forget('searchMod');
        		session()->forget('relocbuyerrequest');
        	}
        	$payment_methods = CommonComponent::getPaymentTerms ();
        	$ratecardTypes = CommonComponent::getAllRatecardTypes();
        	$propertyTypes = CommonComponent::getAllPropertyTypes();
        	$loadTypes = CommonComponent::getAllLoadCategories();
        	$roomTypes = CommonComponent::getAllRoomTypes();
        	$vehicletypecategories = CommonComponent::getAllVehicleCategories();
        	$vehicletypecategorietypes = CommonComponent::getAllVehicleCategoryTypes();
        	//echo "<pre>--";print_R($ratecardTypes);die;
        	$userId = Auth::User ()->id;
        	
        	$session_search_values = array();
                $url_search= explode("?",HTTP_REFERRER);
                $url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);
        	
        	if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != ''){
        		$serverpreviUrL =$_SERVER['HTTP_REFERER'];
        	}else{
        		$serverpreviUrL ='';
        	}
        	
        	
        	if($url_search_search == 'byersearchresults'){
        		/*$session_search_values[] = Session::get('session_delivery_date_reslocation');
        		$session_search_values[] = Session::get('session_dispatch_date_reslocation');
        		$session_search_values[] = Session::get('session_vehicle_type_reslocation');
        		$session_search_values[] = Session::get('session_load_type_reslocation');
        		$session_search_values[] = Session::get('session_from_city_id_reslocation');
        		$session_search_values[] = Session::get('session_to_city_id_reslocation');
        		$session_search_values[] = Session::get('session_from_location_reslocation');
        		$session_search_values[] = Session::get('session_to_location_reslocation');
        		$session_search_values[] = "";*/
        		$session_search_values[8] = "";
        		
        	}else{
                session()->forget('searchMod');
        	}
        	
            return view ( 'relocation.buyers.buyer_creation',[
            		'paymentterms' => $payment_methods,
            		'ratecardtypes' => $ratecardTypes,
            		'session_search_values_create'=> $session_search_values,
            		'property_types' => $propertyTypes,
            		'load_types' => $loadTypes,
            		'url_search_search' => $url_search_search,
            		'serverpreviUrL' => $serverpreviUrL,
            		'room_types' =>$roomTypes,
            		'vehicletypecategories' => $vehicletypecategories,
            		'vehicletypecategorietypes' => $vehicletypecategorietypes
            ]);
            break;
                

            case RELOCATION_PET_MOVE:
                    if(!isset($_REQUEST['search'])){
                            Session::forget('relocbuyerrequest');
                    }
                    return $this->petmove_create();
                    break;

            case RELOCATION_OFFICE_MOVE:
                    $url_search= explode("?",HTTP_REFERRER);
                    $url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);

                    if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != ''){
                    $serverpreviUrL =$_SERVER['HTTP_REFERER'];
                    }else{
                    $serverpreviUrL ='';
                    }

                    if($url_search_search != 'byersearchresults'){ 
                        if(session()->has('searchMod'))                       
                            session()->forget('searchMod');
                    }

                    $particulars = array();
                    $particulars = CommonComponent::getOfficeParticulars();
                    return view ('relocationoffice.buyers.buyer_creation',[
                                        'particulars' => $particulars,
                                        'url_search_search' => $url_search_search,
                                        'serverpreviUrL' => $serverpreviUrL
                                    ]);
                break;
            case RELOCATION_INTERNATIONAL:
                    if(!isset($_REQUEST['search'])){
                            Session::forget('relocbuyerrequest');
                    }
                    Session::forget('masterBedRoom');
                    Session::forget('masterBedRoom1');
                    Session::forget('masterBedRoom2');
                    Session::forget('masterBedRoom3');
                    Session::forget('lobby','');
                    Session::forget('kitchen');
                    Session::forget('bathroom');
                    Session::forget('living');
                    return $this->relocation_international_create();
                    break;    
            case RELOCATION_GLOBAL_MOBILITY:
                    return $this->relocation_global_createpost();
                break;
                    
            }
        } catch ( Exception $e ) {
            echo 'Caught exception: ', $e->getMessage (), "\n";
        }
    }
    
 
    /**
    * Relocation Global
    * @author Swathi
    */
    private function relocation_global_createpost(){
        $session_search_values = array();
        $lkp_relgm_services = array();
        $userId = Auth::User ()->id;
        $url_search= explode("?",HTTP_REFERRER);
        $url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);
    
        if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != ''){
            $serverpreviUrL =$_SERVER['HTTP_REFERER'];
        }else{
            $serverpreviUrL ='';
        }
        if($url_search_search == 'byersearchresults'){
            $session_search_values[] = array();
            
        }else{
            session()->forget('searchMod');
        }
        
        $lkp_relgm_services = CommonComponent::getLkpRelocationGMServices();
        return view ( 'relocationglobal.buyers.buyer_creation',[                  
                    //'session_search_values'=> $session_search_values,
                    'url_search_search' => $url_search_search,
                    'serverpreviUrL' => $serverpreviUrL,
                    'lkp_relgm_services' => $lkp_relgm_services
            ]);
    }


    /**
    * Relocation International
    * @author Shriram
    */
    private function relocation_international_create(){
        
        $session_search_values = array();
        $url_search= explode("?",HTTP_REFERRER);
        $url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);
        $bid_type = \DB::table('lkp_bid_types')->lists('bid_type', 'id');
        if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != ''):
            $serverpreviUrL =$_SERVER['HTTP_REFERER'];
        else:
            $serverpreviUrL ='';
        endif;

        if($url_search_search == 'byersearchresults'){
            $session_search_values[] = array();
        }else{
            session()->forget('searchMod');
        }
        
        return view('relocationint.buyers.buyer_creation',[
            'url_search_search' => $url_search_search,
			'serverpreviUrL' => $serverpreviUrL,
        	'bid_type' => $bid_type
           
        ]);
    }
    
    /**
    * Relocation Pet move
    * @author Shriram
    */
    private function petmove_create(){
        
        $session_search_values = array();
        $url_search= explode("?",HTTP_REFERRER);
        $url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);

        if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != ''):
            $serverpreviUrL =$_SERVER['HTTP_REFERER'];
        else:
            $serverpreviUrL ='';
        endif;

        if($url_search_search == 'byersearchresults'){
            $session_search_values[] = array();
        }else{
            session()->forget('searchMod');                
        }

        return view('relocationpet.buyers.buyer_creation',[
            'url_search_search' => $url_search_search,
            'serverpreviUrL' => $serverpreviUrL,
            'getAllPetTypes' => CommonComponent::getAllPetTypes(),
            'getAllCageTypes' => CommonComponent::getAllCageTypes(),
            'getAllBreedTypes' => CommonComponent::getAllBreedTypesList()
            
        ]);
    }

 
 public function getPropertyCft(){
 	
 	
 	//echo $_REQUEST['prop_id'];
 	try {
 	$volumecft = CommonComponent::getPropertyCft($_REQUEST['prop_id']);
 	echo $volumecft;
 	} catch ( Exception $e ) {
            echo 'Caught exception: ', $e->getMessage (), "\n";
   }
 }
 
 
 public function getPropertyParticulars(){ 
 
 	//echo $_REQUEST['prop_id'];
        $serviceId = Session::get('service_id'); 	
 	try { 		
 	$roomtypes = CommonComponent::getParticularsByRoomId($_REQUEST['room_id']);
 	if($serviceId == 18) {
            $returnHTML = view('relocationint.ocean.buyers.rooms_inventory')->with(['room_id'=>$_REQUEST['room_id'],'roomtypes' => $roomtypes ])->render();
        } else {
           $returnHTML = view('relocation.buyers.rooms_inventory')->with(['room_id'=>$_REQUEST['room_id'],'roomtypes' => $roomtypes ])->render(); 
        } 	
 	return response()->json(array('success' => true, 'html'=>$returnHTML));
 	} catch ( Exception $e ) {
            echo 'Caught exception: ', $e->getMessage (), "\n";
    }
 }
 
public function getOfficePropertyParticulars(){
     //echo $_REQUEST['prop_id']; 
    try {       
    $particulars = CommonComponent::getOfficeParticulars();
    $returnHTML = view('relocationoffice.buyers.rooms_inventory')->with(['particulars' => $particulars ])->render();
    return response()->json(array('success' => true, 'html'=>$returnHTML));
    } catch ( Exception $e ) {
            echo 'Caught exception: ', $e->getMessage (), "\n";
    }
 }

 public function saveinventorydetails(){
 	
 	try {
 		
 		$masterBedRoom=array();
 		$masterBedRoom1=array();
 		$masterBedRoom2=array();
 		$masterBedRoom3=array();
 		$lobby=array();
 		$kitchen=array();
 		$bathroom=array();
 		$living=array();
 		
 		
 		
 		  
 		  $masterBedRoomTotal=0;
 		  $masterBedRoom1Total=0;
 		  $masterBedRoom2Total=0;
 		  $masterBedRoom3Total=0;
 		  $lobbyTotal=0;
 		  $kitchenTotal=0;
 		  $bathroomTotal=0;
 		  $livingTotal=0;
 		  $Totalvolume=0;
 		  $Totalamount=0;
 		  $TotalIndentVolume=0;
 		  $TotalIndentPrice=0;
 		  if($_REQUEST['room_type']==1){
 		  	
 		  	$particulars=CommonComponent::getParticularsByRoomId(1);
 		  
 		  	foreach($particulars as $particular){
 		  		
 		  	   $particulardata='roomitems_'.$particular->id;
 		  	   $particularcrating='roomcrating_'.$particular->id;
 		  	   
 		  	   if(isset($_REQUEST[$particulardata])){
 		  	   
 		  	   	if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){
 		  	   		
 		  	   		$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
 		  	   		->where('particulars.id', $particular->id)
 		  	   		->select('particulars.volume')
 		  	   		->get();
					
 		  	   		$crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
 		  	   		->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
 		  	   		->select('sellerquotes.crating_charges')
 		  	   		->get();
 		  	   		
 		  	   		
 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
 		  	   			
 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
 		  	   		}else{
 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
 		  	   		}
 		  	   		$Totalamount=$Totalvolume*$_REQUEST['contractprice'];
 		  	   		
 		  	   		$masterBedRoom['indentvolume']=$Totalvolume;
 		  	   		$masterBedRoom['indetnfreight']=$Totalamount;
 		  	   	}
 		  	   	
 		  	   	$masterBedRoom['number_items_'.$particular->id]=$_REQUEST[$particulardata];
 		  	   	if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
 		  	   	$masterBedRoom['crating_'.$particular->id]=1;
 		  	   	}else{
 		  	   	$masterBedRoom['crating_'.$particular->id]=0;
 		  	   	}
 		  	   	
 		  	   	$masterBedRoomTotal=$masterBedRoomTotal+$_REQUEST[$particulardata];
 		  	   	$masterBedRoom['total']=$masterBedRoomTotal;
 		  	   }else{
 		  	   	$masterBedRoom['number_items_'.$particular->id]='';
 		  	   	$masterBedRoom['crating_'.$particular->id]=0;
 		  	   }
 		  	   
 		  	   Session::put('masterBedRoom','');
 		  	   Session::put('masterBedRoom',$masterBedRoom);

 		  	   }
 		  }  
 		 
 		
 		  	   if($_REQUEST['room_type']==2){
 		  	   	$particulars=CommonComponent::getParticularsByRoomId(2);
 		  	   	foreach($particulars as $particular){
 		  	   		 
 		  	   		$particulardata='roomitems_'.$particular->id;
 		  	   		$particularcrating='roomcrating_'.$particular->id;
 		  	   		 
 		  	   	if(isset($_REQUEST[$particulardata])){
 		  	   		 
 		  	   		if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){
 		  	   				
 		  	   			$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
 		  	   			->where('particulars.id', $particular->id)
 		  	   			->select('particulars.volume')
 		  	   			->get();
 		  	   		
	 		  	   		$crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
	 		  	   		->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
	 		  	   		->select('sellerquotes.crating_charges')
	 		  	   		->get();
	 		  	   		
	 		  	   		
	 		  	   		
	 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
	 		  	   			
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
	 		  	   		}else{
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
	 		  	   		}
	 		  	   		
 		  	   			$Totalamount=$Totalvolume*$_REQUEST['contractprice'];
 		  	   				
 		  	   			$masterBedRoom1['indentvolume']=$Totalvolume;
 		  	   			$masterBedRoom1['indetnfreight']=$Totalamount;
 		  	   		}
 		  	   			
 		  	   		$masterBedRoom1['number_items_'.$particular->id]=$_REQUEST[$particulardata];
 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
 		  	   			$masterBedRoom1['crating_'.$particular->id]=1;
 		  	   		}else{
 		  	   			$masterBedRoom1['crating_'.$particular->id]=0;
 		  	   		}
 		  	   	$masterBedRoom1Total=$masterBedRoom1Total+$_REQUEST[$particulardata];
 		  	   	$masterBedRoom1['total']=$masterBedRoom1Total;
 		  	   	}else{
 		  	   		$masterBedRoom1['number_items_'.$particular->id]='';
 		  	   		$masterBedRoom1['crating_'.$particular->id]=0;
 		  	   	}
 		  	   	
 		  	   	Session::put('masterBedRoom1','');
 		  	   	Session::put('masterBedRoom1',$masterBedRoom1);
 		  	   }
 		  	  }
 		  	  
 		  	   if($_REQUEST['room_type']==3){
 		  	   	$particulars=CommonComponent::getParticularsByRoomId(3);
 		  	   	foreach($particulars as $particular){
 		  	   	
 		  	   		$particulardata='roomitems_'.$particular->id;
 		  	   		$particularcrating='roomcrating_'.$particular->id;
 		  	   	if(isset($_REQUEST[$particulardata])){
 		  	   
 		  	   		if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){
 		  	   			 
 		  	   			$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
 		  	   			->where('particulars.id', $particular->id)
 		  	   			->select('particulars.volume')
 		  	   			->get();
 		  	   				
 		  	   		   $crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
	 		  	   		->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
	 		  	   		->select('sellerquotes.crating_charges')
	 		  	   		->get();
	 		  	   		
	 		  	   		
	 		  	   		
	 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
	 		  	   			
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
	 		  	   		}else{
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
	 		  	   		}
 		  	   			$Totalamount=$Totalvolume*$_REQUEST['contractprice'];
 		  	   			 
 		  	   			$masterBedRoom2['indentvolume']=$Totalvolume;
 		  	   			$masterBedRoom2['indetnfreight']=$Totalamount;
 		  	   		}
 		  	   		
 		  	   		$masterBedRoom2['number_items_'.$particular->id]=$_REQUEST[$particulardata];
 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
 		  	   			$masterBedRoom2['crating_'.$particular->id]=1;
 		  	   		}else{
 		  	   			$masterBedRoom2['crating_'.$particular->id]=0;
 		  	   		}
 		  	   	$masterBedRoom2Total=$masterBedRoom2Total+$_REQUEST[$particulardata];
 		  	   	$masterBedRoom2['total']=$masterBedRoom2Total;
 		  	   	}else{
 		  	   		$masterBedRoom2['number_items_'.$particular->id]='';
 		  	   		$masterBedRoom2['crating_'.$particular->id]=0;
 		  	   	}
 		  	   
 		  	   	Session::put('masterBedRoom2','');
 		  	   	Session::put('masterBedRoom2',$masterBedRoom2);
 		  	   }
 		  	   }
 		  	  
 		  	   
 		  	   if($_REQUEST['room_type']==4){
 		  	   	$particulars=CommonComponent::getParticularsByRoomId(4);
 		  	   	foreach($particulars as $particular){
 		  	   	
 		  	   		$particulardata='roomitems_'.$particular->id;
 		  	   		$particularcrating='roomcrating_'.$particular->id;
 		  	   	if(isset($_REQUEST[$particulardata])){
 		  	   		 
 		  	   		if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){
 		  	   			 
 		  	   			$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
 		  	   			->where('particulars.id', $particular->id)
 		  	   			->select('particulars.volume')
 		  	   			->get();
 		  	   				
 		  	   			$crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
	 		  	   		->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
	 		  	   		->select('sellerquotes.crating_charges')
	 		  	   		->get();
	 		  	   		
	 		  	   		
	 		  	   		
	 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
	 		  	   			
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
	 		  	   		}else{
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
	 		  	   		}
 		  	   			$Totalamount=$Totalvolume*$_REQUEST['contractprice'];
 		  	   			 
 		  	   			$masterBedRoom3['indentvolume']=$Totalvolume;
 		  	   			$masterBedRoom3['indetnfreight']=$Totalamount;
 		  	   		}
 		  	   		
 		  	   		$masterBedRoom3['number_items_'.$particular->id]=$_REQUEST[$particulardata];
 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
 		  	   			$masterBedRoom3['crating_'.$particular->id]=1;
 		  	   		}else{
 		  	   			$masterBedRoom3['crating_'.$particular->id]=0;
 		  	   		}
 		  	   	$masterBedRoom3Total=$masterBedRoom3Total+$_REQUEST[$particulardata];
 		  	   	$masterBedRoom3['total']=$masterBedRoom3Total;
 		  	   	}else{
 		  	   		$masterBedRoom3['number_items_'.$particular->id]='';
 		  	   		$masterBedRoom3['crating_'.$particular->id]=0;
 		  	   	}
 		  	   
 		  	   	Session::put('masterBedRoom3','');
 		  	   	Session::put('masterBedRoom3',$masterBedRoom3);
 		  	   }
 		  	   }
 		  	   
 		  	   if($_REQUEST['room_type']==5){
 		  	   	$particulars=CommonComponent::getParticularsByRoomId(5);
 		  	   	foreach($particulars as $particular){
 		  	   	
 		  	   		$particulardata='roomitems_'.$particular->id;
 		  	   		$particularcrating='roomcrating_'.$particular->id;
 		  	   	if(isset($_REQUEST[$particulardata])){
 		  	   		 
 		  	   		if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){
 		  	   			 
 		  	   			$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
 		  	   			->where('particulars.id', $particular->id)
 		  	   			->select('particulars.volume')
 		  	   			->get();
 		  	   			 
 		  	   		 $crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
	 		  	   		->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
	 		  	   		->select('sellerquotes.crating_charges')
	 		  	   		->get();
	 		  	   		
	 		  	   		
	 		  	   		
	 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
	 		  	   			
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
	 		  	   		}else{
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
	 		  	   		}
	 		  	   		
 		  	   			$Totalamount=$Totalvolume*$_REQUEST['contractprice'];
 		  	   			 
 		  	   			$lobby['indentvolume']=$Totalvolume;
 		  	   			$lobby['indetnfreight']=$Totalamount;
 		  	   		}
 		  	   			
 		  	   		$lobby['number_items_'.$particular->id]=$_REQUEST[$particulardata];
 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
 		  	   			$lobby['crating_'.$particular->id]=1;
 		  	   		}else{
 		  	   			$lobby['crating_'.$particular->id]=0;
 		  	   		}
 		  	   	$lobbyTotal=$lobbyTotal+$_REQUEST[$particulardata];
 		  	   	$lobby['total']=$lobbyTotal;
 		  	   	}else{
 		  	   		$lobby['number_items_'.$particular->id]='';
 		  	   		$lobby['crating_'.$particular->id]=0;
 		  	   	}
 		  	   	
 		  	   	Session::put('lobby','');
 		  	   	Session::put('lobby',$lobby);
 		  	   }
 		  	   }
 		  	   
 		  	   if($_REQUEST['room_type']==6){
 		  	   	$particulars=CommonComponent::getParticularsByRoomId(6);
 		  	   	foreach($particulars as $particular){
 		  	   	
 		  	   		$particulardata='roomitems_'.$particular->id;
 		  	   		$particularcrating='roomcrating_'.$particular->id;
 		  	   	if(isset($_REQUEST[$particulardata])){
 		  	   
 		  	   		if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){
 		  	   			 
 		  	   			$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
 		  	   			->where('particulars.id', $particular->id)
 		  	   			->select('particulars.volume')
 		  	   			->get();
 		  	   			 
 		  	   			$crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
	 		  	   		->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
	 		  	   		->select('sellerquotes.crating_charges')
	 		  	   		->get();
	 		  	   		
	 		  	   		
	 		  	   		
	 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
	 		  	   			
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
	 		  	   		}else{
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
	 		  	   		}
	 		  	   		
 		  	   			$Totalamount=$Totalvolume*$_REQUEST['contractprice'];
 		  	   			 
 		  	   			$kitchen['indentvolume']=$Totalvolume;
 		  	   			$kitchen['indetnfreight']=$Totalamount;
 		  	   		}
 		  	   		
 		  	   		$kitchen['number_items_'.$particular->id]=$_REQUEST[$particulardata];
 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
 		  	   			$kitchen['crating_'.$particular->id]=1;
 		  	   		}else{
 		  	   			$kitchen['crating_'.$particular->id]=0;
 		  	   		}
 		  	   	$kitchenTotal=$kitchenTotal+$_REQUEST[$particulardata];
 		  	   	$kitchen['total']=$kitchenTotal;
 		  	   	}else{
 		  	   		$kitchen['number_items_'.$particular->id]='';
 		  	   		$kitchen['crating_'.$particular->id]=0;
 		  	   	}
 		  	   	
 		  	   	Session::put('kitchen','');
 		  	   	Session::put('kitchen',$kitchen);
 		  	   }
 		  	   }
 		  	   
 		  	   
 		  	   if($_REQUEST['room_type']==7){
 		  	   	$particulars=CommonComponent::getParticularsByRoomId(7);
 		  	   	foreach($particulars as $particular){
 		  	   	
 		  	   		$particulardata='roomitems_'.$particular->id;
 		  	   		$particularcrating='roomcrating_'.$particular->id;
 		  	   	if(isset($_REQUEST[$particulardata])){
 		  	   		 
 		  	   		if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){
 		  	   			 
 		  	   			$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
 		  	   			->where('particulars.id', $particular->id)
 		  	   			->select('particulars.volume')
 		  	   			->get();
 		  	   			 
 		  	   		  $crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
	 		  	   		->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
	 		  	   		->select('sellerquotes.crating_charges')
	 		  	   		->get();
	 		  	   		
	 		  	   		
	 		  	   		
	 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
	 		  	   			
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
	 		  	   		}else{
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
	 		  	   		}
 		  	   			$Totalamount=$Totalvolume*$_REQUEST['contractprice'];
 		  	   			 
 		  	   			$bathroom['indentvolume']=$Totalvolume;
 		  	   			$bathroom['indetnfreight']=$Totalamount;
 		  	   		}
 		  	   		
 		  	   		$bathroom['number_items_'.$particular->id]=$_REQUEST[$particulardata];
 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
 		  	   			$bathroom['crating_'.$particular->id]=1;
 		  	   		}else{
 		  	   			$bathroom['crating_'.$particular->id]=0;
 		  	   		}
 		  	   	$bathroomTotal=$bathroomTotal+$_REQUEST[$particulardata];
 		  	   	$bathroom['total']=$bathroomTotal;
 		  	   	}else{
 		  	   		$bathroom['number_items_'.$particular->id]='';
 		  	   		$bathroom['crating_'.$particular->id]=0;
 		  	   	}
 		  	   	
 		  	   
 		  	   	Session::put('bathroom','');
 		  	   	Session::put('bathroom',$bathroom);
 		  	   }
 		  	   } 
 		  	  
 		  	   if($_REQUEST['room_type']==8){
 		  	   	$particulars=CommonComponent::getParticularsByRoomId(8);
 		  	   	foreach($particulars as $particular){
 		  	   	
 		  	   		$particulardata='roomitems_'.$particular->id;
 		  	   		$particularcrating='roomcrating_'.$particular->id;
 		  	   	if(isset($_REQUEST[$particulardata])){
 		  	   
 		  	   		if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){
 		  	   			 
 		  	   			$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
 		  	   			->where('particulars.id', $particular->id)
 		  	   			->select('particulars.volume')
 		  	   			->get();
 		  	   			 
 		  	   		 $crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
	 		  	   		->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
	 		  	   		->select('sellerquotes.crating_charges')
	 		  	   		->get();
	 		  	   		
	 		  	   		
	 		  	   		
	 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
	 		  	   			
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
	 		  	   		}else{
	 		  	   		$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
	 		  	   		}
 		  	   			$Totalamount=$Totalvolume*$_REQUEST['contractprice'];
 		  	   			 
 		  	   			$living['indentvolume']=$Totalvolume;
 		  	   			$living['indetnfreight']=$Totalamount;
 		  	   		}
 		  	   		 
 		  	   		$living['number_items_'.$particular->id]=$_REQUEST[$particulardata];
 		  	   		if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
 		  	   			$living['crating_'.$particular->id]=1;
 		  	   		}else{
 		  	   			$living['crating_'.$particular->id]=0;
 		  	   		}
 		  	   	$livingTotal=$livingTotal+$_REQUEST[$particulardata];
 		  	   	$living['total']=$livingTotal;
 		  	   	}else{
 		  	   		$living['number_items_'.$particular->id]='';
 		  	   		$living['crating_'.$particular->id]=0;
 		  	   	}
 		  	   	
 		  	   
 		  	   	Session::put('living','');
 		  	   	Session::put('living',$living);
 		  	   }
 		  	   
 		  	  } 
 		  	  
 		if(isset($_REQUEST['placeindent'])){
 		$TotalIndentVolume=0;
 		$TotalIndentPrice=0;
 		
 		if(Session::has('masterBedRoom')){	
 		$masterBedRoomVolme=Session::get('masterBedRoom');
 		$TotalIndentVolume=$TotalIndentVolume+$masterBedRoomVolme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$masterBedRoomVolme['indetnfreight'];
 		}
 		if(Session::has('masterBedRoom1')){
 		$masterBedRoom1Volme=Session::get('masterBedRoom1');
 		$TotalIndentVolume=$TotalIndentVolume+$masterBedRoom1Volme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$masterBedRoom1Volme['indetnfreight'];
 		}
 		if(Session::has('masterBedRoom2')){
 		$masterBedRoom2Volme=Session::get('masterBedRoom2');
 		$TotalIndentVolume=$TotalIndentVolume+$masterBedRoom2Volme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$masterBedRoom2Volme['indetnfreight'];
 		}
 		if(Session::has('masterBedRoom3')){
 		$masterBedRoom3Volme=Session::get('masterBedRoom3');
 		$TotalIndentVolume=$TotalIndentVolume+$masterBedRoom3Volme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$masterBedRoom3Volme['indetnfreight'];
		}
		if(Session::has('lobby')){
 		$lobbyVolme=Session::get('lobby');
 		$TotalIndentVolume=$TotalIndentVolume+$lobbyVolme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$lobbyVolme['indetnfreight'];
		}
		if(Session::has('kitchen')){
 		$kitchenvolume=Session::get('kitchen');
 		$TotalIndentVolume=$TotalIndentVolume+$kitchenvolume['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$kitchenvolume['indetnfreight'];
		}
		if(Session::has('bathroom')){
 		$bathroomVolme=Session::get('bathroom');
 		$TotalIndentVolume=$TotalIndentVolume+$bathroomVolme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$bathroomVolme['indetnfreight'];
		}
		if(Session::has('living')){			
 		$livingroomVolme=Session::get('living');
 		$TotalIndentVolume=$TotalIndentVolume+$livingroomVolme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$livingroomVolme['indetnfreight'];
		}
 		
 		$TotalIndentVolume=$TotalIndentVolume;
 		$TotalIndentPrice=$TotalIndentPrice;
 		
 		
 		}
 		
 		$returnHTML = view('relocation.buyers.rooms_inventory_count')->render();
 		return response()->json(array('success' => true,'TotalIndentVolume'=>$TotalIndentVolume,'TotalIndentPrice'=>$TotalIndentPrice, 'html'=>$returnHTML));
 		
 	} catch ( Exception $e ) {
 		echo 'Caught exception: ', $e->getMessage (), "\n";
 	}
 	
 }
 
/******* Function to Insert Relocation Office Move  *******/
    public function saveRelocatoinOfficeBuyerPost(){
        //where insert here
        Log::info ( 'Insert the buyer posts data: ' . Auth::id (), array (
            'c' => '1') );
        try {
                if (isset ( $_POST ['optradio'] )) {
                    $is_private = $_POST ['optradio'];
                }
                $randnumber_value = rand ( 11111, 99999 );
                $postid  =   CommonComponent::getPostID(Session::get ( 'service_id' ));
                
                $created_year = date('Y');
                if(Session::get('service_id') == RELOCATION_OFFICE_MOVE){
                    $randnumber = 'REL-OFF/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
                }
                
                
                
                if (isset ( $_POST['ptlQuoteaccessId'] ) && $_POST['ptlQuoteaccessId'] == 2) {
                    if (isset ( $_POST ['seller_list'] ) && $_POST ['seller_list'] != '') {
                        $seller_list = explode ( ",", $_POST ['seller_list'] );
                        
                        //array_shift ( $seller_list );
                        $seller_list_count = count ( $seller_list );
                    }
                }
                
                if(Session::get('service_id') == RELOCATION_OFFICE_MOVE){
                    $buyerpostreloffice = new RelocationofficeBuyerPost();
                    $buyerpostreloffice->lkp_service_id = RELOCATION_OFFICE_MOVE;
                }
                $buyerpostreloffice->dispatch_date = CommonComponent::convertDateForDatabase($_POST ['valid_from']);
                $buyerpostreloffice->delivery_date = CommonComponent::convertDateForDatabase($_POST ['valid_to']);
                    
                $fromcities = array();
                $fromcities[] = $_POST ['from_location_id'];
                
                $buyerpostreloffice->from_location_id = $_POST ['from_location_id'];
                $buyerpostreloffice->distance = $_POST ['distance'];
                $buyerpostreloffice->terms_conditions = 1;
                $buyerpostreloffice->lkp_quote_access_id = $_POST['ptlQuoteaccessId'];
                $buyerpostreloffice->lkp_post_status_id = 2;
                $buyerpostreloffice->created_by = Auth::id ();
                $buyerpostreloffice->buyer_id = Auth::id ();
                $buyerpostreloffice->transaction_id = $randnumber;
                $buyerpostreloffice->save (); //save into 


                //Insert Buyer post inventory
                $particulars = CommonComponent::getOfficeParticulars();
                $created_at = date ( 'Y-m-d H:i:s' );
                $createdIp = $_SERVER ['REMOTE_ADDR'];
                              
                $posted_particulars= ($_POST['roomitems']) ? $_POST['roomitems'] : array();
                foreach($particulars as $particular){
                    $buyerpost_reloffice_inventory = new RelocationofficeBuyerPostInventoryParticular();
                    $particulardata = $posted_particulars[$particular->id];
                    if($particulardata!=""){
                        $buyerpost_reloffice_inventory->lkp_service_id=RELOCATION_OFFICE_MOVE;
                        $buyerpost_reloffice_inventory->buyer_post_id=$buyerpostreloffice->id;
                        $buyerpost_reloffice_inventory->lkp_inventory_office_particular_id=$particular->id;
                        $buyerpost_reloffice_inventory->number_of_items=$particulardata;
                        $buyerpost_reloffice_inventory->created_at=$created_at;
                        $buyerpost_reloffice_inventory->created_ip=$createdIp;
                        $buyerpost_reloffice_inventory->created_by=Auth::id ();                            
                        $buyerpost_reloffice_inventory->save ();
                    }                        
                }

                 
                if (isset ( $_POST['ptlQuoteaccessId'] ) && $_POST['ptlQuoteaccessId'] == 2) {
                        if (isset ( $_POST ['seller_list'] ) && $_POST ['seller_list'] != '') {
                            for($i = 0; $i <$seller_list_count; $i ++) {                                
                                if(Session::get('service_id') == RELOCATION_OFFICE_MOVE){
                                    $buyerpost_for_sellers = new RelocationofficeBuyerSelectedSeller();
                                }
                    
                                $buyerpost_for_sellers->buyer_post_id = $buyerpostreloffice->id;
                                $buyerpost_for_sellers->seller_id = $seller_list [$i];
                                $created_at = date ( 'Y-m-d H:i:s' );
                                $createdIp = $_SERVER ['REMOTE_ADDR'];
                                $buyerpost_for_sellers->lkp_service_id = RELOCATION_OFFICE_MOVE;
                                $buyerpost_for_sellers->created_by = Auth::id ();
                                $buyerpost_for_sellers->created_at = $created_at;
                                $buyerpost_for_sellers->created_ip = $createdIp;
                                $buyerpost_for_sellers->save ();
                                
                                $buyer_selected_buyers_email = DB::table ( 'users' )->where ( 'id', $seller_list [$i] )->get ();
                                $buyer_selected_buyers_email [0]->randnumber = $randnumber;
                                $buyer_selected_buyers_email [0]->buyername = Auth::User ()->username;
                                CommonComponent::send_email ( SELLER_CREATED_POST_FOR_BUYERS, $buyer_selected_buyers_email );
                                // CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
                                
                                
                                //*******Send Sms to the private Sellers***********************//
                                $msg_params = array(
                                        'randnumber' => $randnumber,
                                        'buyername' => Auth::User()->username,
                                        'servicename' => RELOCATIONOFFICE_BUYER_SMS_SERVICENAME
                                );
                                $getMobileNumber  =   CommonComponent::getMobleNumber($seller_list[$i]);
                                if($getMobileNumber)
                                    CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                                //*******Send Sms to the private Sellers***********************//
                                
                            }
                        }
                    }else{
                            //*******Send Sms to the private Sellers***********************//
                            $msg_params = array(
                                    'randnumber' => $randnumber,
                                    'buyername' => Auth::User()->username,
                                    'servicename' => RELOCATIONOFFICE_BUYER_SMS_SERVICENAME
                            );
                            $getSellerIds  =   RelocationBuyerController::getSellerslist($fromcities);
                            //echo "<pre>";print_r($getSellerIds);exit;
                            for($i=0;$i<count($getSellerIds);$i++){ 
                                $getMobileNumber  =   CommonComponent::getMobleNumber($getSellerIds[$i]['id']);
                                if($getMobileNumber)
                                CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                            }
                            //*******Send Sms to the private Sellers***********************//
                                                             
                    }                
                                 
                    //below array for matching engine in FTL start                    
                
                        $matchedItems['from_location_id']=$_POST ['from_location_id'];
                        $matchedItems['from_date']=$_POST['valid_from'];
                        $matchedItems['to_date']=$_POST['valid_to'];
                        $matchedItems['distance']=$_POST['distance'];
                        
                  

                    BuyerMatchingComponent::doMatching(RELOCATION_OFFICE_MOVE,$buyerpostreloffice->id,2,$matchedItems);
                    //below array for matching engine in FTL end
                    return $randnumber;
              
       }catch ( Exception $e ) {
            echo 'Caught exception: ', $e->getMessage (), "\n";
        }
    }

 public function relocationBuyerPostcreation(Request $request){
 	
 	Log::info ( 'Insert the seller posts data: ' . Auth::id (), array (
 			'c' => '1'
 	) );
 	try {
 		
        $serviceId = Session::get('service_id');
        switch($serviceId){
            case RELOCATION_DOMESTIC:	

                   
             		if (isset ( $_POST ['optradio'] )) {
             			$is_private = $_POST ['optradio'];
             		}
             		$randnumber_value = rand ( 11111, 99999 );
             		$postid  =   CommonComponent::getPostID(Session::get ( 'service_id' ));
             		
             		$created_year = date('Y');
             		if(Session::get('service_id') == RELOCATION_DOMESTIC){
             			$randnumber = 'RD/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
             		}
             		
             		
             		
             		if (isset ( $_POST['ptlQuoteaccessId'] ) && $_POST['ptlQuoteaccessId'] == 2) {
             			if (isset ( $_POST ['seller_list'] ) && $_POST ['seller_list'] != '') {
             				$seller_list = explode ( ",", $_POST ['seller_list'] );
             				
             				//array_shift ( $seller_list );
             				$seller_list_count = count ( $seller_list );
             			}
             		}
             		
             		if(Session::get('service_id') == RELOCATION_DOMESTIC){
             			$buyerpostrpost = new RelocationBuyerPost();
             			$buyerpostrpost->lkp_service_id = RELOCATION_DOMESTIC;
             		}
             		
             		$buyerpostrpost->lkp_post_ratecard_type_id = $_POST ['post_rate_card_type'];
             		$buyerpostrpost->dispatch_date = CommonComponent::convertDateForDatabase($_POST ['valid_from']);
             		$buyerpostrpost->delivery_date = CommonComponent::convertDateForDatabase($_POST ['valid_to']);
             		
             		$fromcities = array();
             		$fromcities[] = $_POST ['from_location_id'];
             		
             		$buyerpostrpost->from_location_id = $_POST ['from_location_id'];
             		$buyerpostrpost->to_location_id = $_POST ['to_location_id'];
             		$buyerpostrpost->lkp_lead_type_id = SPOT;
             		$buyerpostrpost->lkp_property_type_id = $_POST['property_type'];
             		$buyerpostrpost->lkp_load_category_id = $_POST['load_type']; 
             		$buyerpostrpost->terms_conditions = 1;
             		$buyerpostrpost->lkp_quote_access_id = $_POST['ptlQuoteaccessId'];
             		$buyerpostrpost->lkp_post_status_id = 2;
             		$buyerpostrpost->created_by = Auth::id ();
             		//$buyerpostrpost->is_commercial = $_POST['is_commercial'];
             		$buyerpostrpost->buyer_id = Auth::id ();
             		$buyerpostrpost->transaction_id = $randnumber;
             		
             		if($_POST ['post_rate_card_type']==1){
                        if(isset($_POST['elevator_origin'])){
             		$buyerpostrpost->origin_elevator = $_POST['elevator_origin'];
                        }
                        if(isset($_POST['elevator_destination'])){
             		$buyerpostrpost->destination_elevator = $_POST['elevator_destination'];
                        }
             		if(isset($_POST['origin_handy_serivce'])){
             		$buyerpostrpost->origin_handyman_services = 1;
             		}
             		if(isset($_POST['destination_handy_serivce'])){
             			$buyerpostrpost->destination_handyman_services = 1;
             		}
             		if(isset($_POST['origin_storage_serivce'])){
             			$buyerpostrpost->origin_storage = 1;
             		}
             		if(isset($_POST['destination_storage_serivce'])){
             			$buyerpostrpost->origin_destination = 1;
             		}
             		if(isset($_POST['insurance_serivce'])){
             			$buyerpostrpost->insurance = 1;
             		}
             		if(isset($_POST['escort_serivce'])){
             			$buyerpostrpost->escort = 1;
             		}
             		if(isset($_POST['mobilty_serivce'])){
             			$buyerpostrpost->mobility = 1;
             		}
             		if(isset($_POST['property_serivce'])){
             			$buyerpostrpost->property = 1;
             		}
             		if(isset($_POST['setting_serivce'])){
             			$buyerpostrpost->setting_service = 1;
             		}
             		if(isset($_POST['insurance_domestic'])){
             			$buyerpostrpost->insurance_domestic = 1;
             		}
             		
             	
             		}else{
             		
             			$buyerpostrpost->lkp_vehicle_category_id = $_POST['vehicle_category'];
             			if(isset($_POST['vehicle_category_type']) && $_POST['vehicle_category_type']){
             			$buyerpostrpost->lkp_vehicle_category_type_id = $_POST['vehicle_category_type'];
             			}
             			$buyerpostrpost->vehicle_model = $_POST['vehicle_model'];
             		
             		}
             		$buyerpostrpost->save ();
             		
             		
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
             					$buyerpost_inventory->buyer_post_id=$buyerpostrpost->id;
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
             					$buyerpost_inventory->buyer_post_id=$buyerpostrpost->id;
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
             					$buyerpost_inventory->buyer_post_id=$buyerpostrpost->id;
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
             					$buyerpost_inventory->buyer_post_id=$buyerpostrpost->id;
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
             					$buyerpost_inventory->buyer_post_id=$buyerpostrpost->id;
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
             					$buyerpost_inventory->buyer_post_id=$buyerpostrpost->id;
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
             					$buyerpost_inventory->buyer_post_id=$buyerpostrpost->id;
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
             					$buyerpost_inventory->buyer_post_id=$buyerpostrpost->id;
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
             		
             		if (isset ( $_POST['ptlQuoteaccessId'] ) && $_POST['ptlQuoteaccessId'] == 2) {
             			
             			
             			if (isset ( $_POST ['seller_list'] ) && $_POST ['seller_list'] != '') {
             				
             				for($i = 0; $i <$seller_list_count; $i ++) {
             					
             					if(Session::get('service_id') == RELOCATION_DOMESTIC){
             						$buyerpost_for_sellers = new RelocationBuyerSelectedSeller();
             					}
             		
             					$buyerpost_for_sellers->buyer_post_id = $buyerpostrpost->id;
             					$buyerpost_for_sellers->seller_id = $seller_list [$i];
             					$created_at = date ( 'Y-m-d H:i:s' );
             					$createdIp = $_SERVER ['REMOTE_ADDR'];
             					$buyerpost_for_sellers->lkp_service_id = RELOCATION_DOMESTIC;
             					$buyerpost_for_sellers->created_by = Auth::id ();
             					$buyerpost_for_sellers->created_at = $created_at;
             					$buyerpost_for_sellers->created_ip = $createdIp;
             					$buyerpost_for_sellers->save ();
             					
             					$buyer_selected_buyers_email = DB::table ( 'users' )->where ( 'id', $seller_list [$i] )->get ();
             					$buyer_selected_buyers_email [0]->randnumber = $randnumber;
             					$buyer_selected_buyers_email [0]->buyername = Auth::User ()->username;
             					CommonComponent::send_email ( SELLER_CREATED_POST_FOR_BUYERS, $buyer_selected_buyers_email );
             					// CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
             					
             					
             					//*******Send Sms to the private Sellers***********************//
             					$msg_params = array(
             							'randnumber' => $randnumber,
             							'buyername' => Auth::User()->username,
             							'servicename' => 'RELOCATION DOMESTIC'
             					);
             					$getMobileNumber  =   CommonComponent::getMobleNumber($seller_list[$i]);
             					if($getMobileNumber)
             						CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
             					//*******Send Sms to the private Sellers***********************//
             					
             				}
             			}
             		}else{
                                    		//*******Send Sms to the private Sellers***********************//
                                    		$msg_params = array(
                                    				'randnumber' => $randnumber,
                                    				'buyername' => Auth::User()->username,
                                    				'servicename' => 'RELOCATION DOMESTIC'
                                    		);
                                    		$getSellerIds  =   RelocationBuyerController::getSellerslist($fromcities);
                                    		//echo "<pre>";print_r($getSellerIds);exit;
                                    		for($i=0;$i<count($getSellerIds);$i++){	
                                    			$getMobileNumber  =   CommonComponent::getMobleNumber($getSellerIds[$i]['id']);
                                    			if($getMobileNumber)
                                    			CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                                    		}
                                    		//*******Send Sms to the private Sellers***********************//
                                    	                     
                                    	
                                    }
             		//below array for matching engine in FTL start
             		
             		if($_POST ['post_rate_card_type']==1){
            			$matchedItems['from_location_id']=$_POST ['from_location_id'];
            			$matchedItems['to_location_id']=$_POST ['to_location_id'];
            			$matchedItems['from_date']=$_POST['valid_from'];
            			$matchedItems['to_date']=$_POST['valid_to'];
            			$matchedItems['to_date']=$_POST['valid_to'];
            			$matchedItems['property_type']=$_POST['property_type'];
            			$matchedItems['post_rate_card_type']=1;
             	    }else{
            			$matchedItems['from_location_id']=$_POST ['from_location_id'];
            			$matchedItems['to_location_id']=$_POST ['to_location_id'];
            			$matchedItems['from_date']=$_POST['valid_from'];
            			$matchedItems['to_date']=$_POST['valid_to'];
            			$matchedItems['vehicle_category']=$_POST['vehicle_category'];
            			$matchedItems['vehicle_category_type']=$_POST['vehicle_category_type'];
            			$matchedItems['post_rate_card_type']=2;
             	    }

             		BuyerMatchingComponent::doMatching(RELOCATION_DOMESTIC,$buyerpostrpost->id,2,$matchedItems);
             		//below array for matching engine in FTL end
             		 	
             		
             		//echo "all done";die;
             		return redirect ( '/relocation/creatbuyerrpost' )->with ( 'relocationtransactionNumber', $randnumber )->with('postType',1);
                
                break;
            
            case RELOCATION_OFFICE_MOVE:
                $randnumber = $this->saveRelocatoinOfficeBuyerPost();
                return redirect ( '/relocation/creatbuyerrpost' )->with ( 'relocationtransactionNumber', $randnumber )->with('postType',1);   
                break;

            case RELOCATION_PET_MOVE:
                return $this->saveRelocationPetMove($request);    
                break;  
            case RELOCATION_INTERNATIONAL:
                return $this->saveRelocationInt($_REQUEST);    
                break;  
            case RELOCATION_GLOBAL_MOBILITY:
                return $this->saveRelocationGm($_REQUEST);    
                break;  

            default: die("Invalid Service ID");    
        }//end switch case
 	
 	}
 catch ( Exception $e ) {
 		echo 'Caught exception: ', $e->getMessage (), "\n";
 	}
 	
 	
 }

 /**
 * Ajax load breed types based on pet type id
 * @author Shriram
 */
 public function ajxBreedTypes(Request $request){
    if($request->has('pettypeid')):
        // Success response
        return response()->json([ 'success' => true,
            'optHtml' => CommonComponent::getAllPetBreedTypes($request->pettypeid)
        ]);
    else:
        // failure
        return response()->json([ 'success' => false, 'msg' => 'Pet breed type id not found' ]);    
    endif;
 }
    
 /**
 * Saving Relocation pet move buyer post creation
 * @author Shriram
 * @param POST data
 */
 private function saveRelocationPetMove($request){
    
    // Below lines will generate unique transaction id
    $postid  =   CommonComponent::getPostID(Session::get('service_id'));
    $randnumber = 'RP/'.date('Y').'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);

    $buyerPostCreate = \App\Models\RelocationPetBuyerPost::create([
        'from_location_id'      => $request->from_location_id,
        'to_location_id'        => $request->to_location_id,
        'lkp_service_id'        => RELOCATION_PET_MOVE,
        'buyer_id'              => Auth::id(),
        'transaction_id'        => $randnumber,  
        'lkp_lead_type_id'      => SPOT,    // Spot or Term
        'lkp_post_status_id'    => 2,       // Post type - open or draft
        'lkp_quote_access_id'   => $request->ptlQuoteaccessId, // Private-1, public-0 
        'dispatch_date'         => CommonComponent::convertDateForDatabase($request->valid_from),
        'delivery_date'         => CommonComponent::convertDateForDatabase($request->valid_to),
        'is_delivery_flexible'  => 0,
        'lkp_pet_type_id'       => $request->selPettype,
        'lkp_breed_type_id'     => $request->selBreedtype,
        'lkp_cage_type_id'      => $request->selCageType,
        'created_by'            => Auth::id(),
        'updated_by'            => Auth::id(),
        'created_ip'            => $request->ip(),
        'updated_ip'            => $request->ip()
    ]);
    
    // if is private post
    if ($request->ptlQuoteaccessId == 2):
        
        // Checking private seller list
        if ($request->seller_list != '') {
            
            // Converting comma separated input into Array and then couting the seller list
            $seller_list = explode (",", $request->seller_list);
            $seller_list_count = count($seller_list);

            // Initialise the Relocation Pet Selected Sellers
            $petBuyerSelSeller = new \App\Models\RelocationPetBuyerSelectedSellers();
            for($i = 0; $i <$seller_list_count; $i++):                            
                
                // Saving Records on relocationpet_buyer_selected_sellers                
                $buyerPet = $petBuyerSelSeller::create([
                    'lkp_service_id'    => RELOCATION_PET_MOVE,
                    'buyer_post_id'     => $buyerPostCreate->id,
                    'seller_id'         => $seller_list[$i],
                    'created_by'        => Auth::id(),
                    'updated_by'        => Auth::id(),
                    'created_ip'        => $request->ip(),
                    'updated_ip'        => $request->ip()
                ]);

                // Saving Records on relocationpet_buyer_quote_sellers_quotes_prices
                $petBuyerQuotePrice = new \App\Models\RelocationpetBuyerQuoteSellersQuotesPrice;
                $petBuyerQuotePrice->lkp_service_id = RELOCATION_PET_MOVE;
                $petBuyerQuotePrice->buyer_id       = Auth::id();
                $petBuyerQuotePrice->buyer_quote_id = $buyerPostCreate->id;
                $petBuyerQuotePrice->seller_id      = $seller_list[$i];
                $petBuyerQuotePrice->created_by     = Auth::id();
                $petBuyerQuotePrice->updated_by     = Auth::id();
                $petBuyerQuotePrice->created_ip     = $request->ip();
                $petBuyerQuotePrice->updated_ip     = $request->ip();
                $petBuyerQuotePrice->save();

                $buyer_selected_buyers_email = DB::table('users')
                    ->where('id', $seller_list[$i])->get();
                $buyer_selected_buyers_email[0]->randnumber = $randnumber;
                $buyer_selected_buyers_email[0]->buyername = Auth::User()->username;
                CommonComponent::send_email(BUYER_CREATED_POST_FOR_SELLERS, $buyer_selected_buyers_email
                );
                
                //*******Send Sms to the private Sellers***********************//
                $getMobileNumber  =   CommonComponent::getMobleNumber($seller_list[$i]);
                if($getMobileNumber):
                    CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,[
                        'randnumber' => $randnumber,
                        'buyername' => Auth::User()->username,
                        'servicename' => RELOCATIONPETMOVE_BUYER_SMS_SERVICENAME
                    ]);
                endif;
                //*******Send Sms to the private Sellers***********************//
                
            endfor;
        }
    endif;    
    
    //below array for matching engine in Relocation pet start
    $matchedItems['from_location_id']=$_POST ['from_location_id'];
    $matchedItems['to_location_id']=$_POST ['to_location_id'];
    $matchedItems['from_date']=$_POST['valid_from'];
    $matchedItems['to_date']=$_POST['valid_to']; 
    $matchedItems['selPettype']=$_POST['selPettype'];
    $matchedItems['selCageType']=$_POST['selCageType'];   
    BuyerMatchingComponent::doMatching(RELOCATION_PET_MOVE,$buyerPostCreate->id,2,$matchedItems);
    
    return redirect('/relocation/creatbuyerrpost')->with('transactionId', $randnumber);
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
 		//$term = Input::get('q');
 		if(isset($_POST['seller_list'])) {
 			$sellerlist = (count($cities) > 0) ? $cities : $_POST['seller_list'];
 		}
 		 
 		if(isset($sellerlist)){
 			$sellersStr = $sellerlist;
 			$districts = DB::table('lkp_cities')
 			->whereIn('lkp_cities.id', $sellersStr)
 			->select('lkp_cities.lkp_district_id')
 			->get();
 			//$district_array	=	array();
 			foreach ($districts as $dist) {
 				$district_array[] = $dist->lkp_district_id;
 			}
 		}
 		switch($serviceId){
 			case RELOCATION_DOMESTIC:
 				$seller_data = DB::table('relocation_seller_posts')
 				->join('users', 'relocation_seller_posts.created_by', '=', 'users.id')
 				->leftjoin('sellers', 'users.id', '=', 'sellers.user_id')
 				->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
 				->distinct('relocation_seller_posts.created_by')
 				->whereIn('relocation_seller_posts.seller_district_id', $district_array)
 				->where('users.lkp_role_id', SELLER)
 				->orWhere('users.secondary_role_id', SELLER)
 				->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
 				->get();
 				break;
                        case RELOCATION_OFFICE_MOVE:
                                $seller_data = DB::table('relocationoffice_seller_posts')
                                ->join('users', 'relocationoffice_seller_posts.created_by', '=', 'users.id')
                                ->leftjoin('sellers', 'users.id', '=', 'sellers.user_id')
                                ->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
                                ->distinct('relocationoffice_seller_posts.created_by')
                                ->whereIn('relocationoffice_seller_posts.seller_district_id', $district_array)
                                ->where('users.lkp_role_id', SELLER)
                                ->orWhere('users.secondary_role_id', SELLER)
                                ->select('users.id', 'users.username', 'sellers.principal_place', 'sellers.name', 'seller_details.firstname')
                                ->get();
                                break;
 		}
 
 		//print_r($seller_data); exit;
 		foreach ($seller_data as $query) {
 			//print_r($query); exit;
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
 
 
  public function editBuyerQuote($buyer_post_id){
 	
  	try {

        $serviceId = Session::get('service_id');
        switch($serviceId){
            case RELOCATION_DOMESTIC:   
                    $buyer_post_edit_seller='';
                    $buyer_post_inventory_details='';
                    $buyer_post_details = DB::table ( 'relocation_buyer_posts' )->where ( 'id', $buyer_post_id )->get ();
                    
                    if($buyer_post_details[0]->lkp_post_ratecard_type_id==1){
                    $buyer_post_inventory_details = DB::table ( 'relocation_buyer_post_inventory_particulars' )->where ( 'buyer_post_id', $buyer_post_id )->get ();
                    }
                    
                    $buyer_post_edit_seller = DB::table('relocation_buyer_posts')
                    ->leftjoin('relocation_buyer_selected_sellers as seller', 'seller.buyer_post_id', '=', 'relocation_buyer_posts.id')
                    ->leftjoin('users as u', 'u.id', '=', 'seller.seller_id')
                    ->where('relocation_buyer_posts.id', $buyer_post_id)
                    ->select('seller.seller_id', 'u.username', 'u.id')
                    ->get();
                            
                    return view ( 'relocation.buyers.buyer_edit_seller',[
                            'buyer_post_details' => $buyer_post_details,
                            'buyer_post_inventory_details' => $buyer_post_inventory_details,
                            'buyer_post_edit_seller' =>$buyer_post_edit_seller
                     ]);
                break;
            
            case RELOCATION_OFFICE_MOVE:
                    $office_buyer_post_edit_seller='';
                    $office_buyer_post_inventory_details='';
                    $office_buyer_post_details = DB::table ( 'relocationoffice_buyer_posts' )->where ( 'id', $buyer_post_id )->get ();
                    
                    $office_buyer_post_inventory_details = DB::table ( 'relocationoffice_buyer_post_inventory_particulars' )->where ( 'buyer_post_id', $buyer_post_id )->get ();                    
                    
                    $office_buyer_post_edit_seller = DB::table('relocationoffice_buyer_posts')
                    ->leftjoin('relocationoffice_buyer_selected_sellers as seller', 'seller.buyer_post_id', '=', 'relocationoffice_buyer_posts.id')
                    ->leftjoin('users as u', 'u.id', '=', 'seller.seller_id')
                    ->where('relocationoffice_buyer_posts.id', $buyer_post_id)
                    ->select('seller.seller_id', 'u.username', 'u.id')
                    ->get();
                            
                    return view ( 'relocationoffice.buyers.buyer_edit_seller',[
                            'buyer_post_details' => $office_buyer_post_details,
                            'buyer_post_inventory_details' => $office_buyer_post_inventory_details,
                            'buyer_post_edit_seller' =>$office_buyer_post_edit_seller
                     ]);              
                break;
            case RELOCATION_PET_MOVE:
                    $relocationpet_buyer_post_edit_seller='';                    
                    $relocationpet_buyer_post_details = DB::table ( 'relocationpet_buyer_posts' )->where ( 'id', $buyer_post_id )->get ();                    
                   
                    $relocationpet_buyer_post_edit_seller = DB::table('relocationpet_buyer_posts')
                    ->leftjoin('relocationpet_buyer_selected_sellers as seller', 'seller.buyer_post_id', '=', 'relocationpet_buyer_posts.id')
                    ->leftjoin('users as u', 'u.id', '=', 'seller.seller_id')
                    ->where('relocationpet_buyer_posts.id', $buyer_post_id)
                    ->select('seller.seller_id', 'u.username', 'u.id')
                    ->get();
                            
                    return view ( 'relocationpet.buyers.buyer_edit_seller',[
                            'buyer_post_details' => $relocationpet_buyer_post_details,                            
                            'buyer_post_edit_seller' =>$relocationpet_buyer_post_edit_seller
                     ]);              
                break;
            
            case RELOCATION_INTERNATIONAL:   
                    $buyer_post_edit_seller='';
                    $buyer_post_inventory_details='';
                    $buyer_post_details = DB::table ( 'relocationint_buyer_posts' )->where ( 'id', $buyer_post_id )->get ();
                    
                    if($buyer_post_details[0]->lkp_international_type_id==1){
                        $buyer_post_inventory_details = DB::table ( 'relocationint_buyer_post_air_cartons' )->where ( 'buyer_post_id', $buyer_post_id )->get ();
                    }else{
                        $buyer_post_inventory_details = DB::table ( 'relocationint_buyer_post_inventory_particulars' )->where ( 'buyer_post_id', $buyer_post_id )->get ();
                    }
                    
                    $buyer_post_edit_seller = DB::table('relocationint_buyer_posts as bp')
                    ->leftjoin('relocationint_buyer_selected_sellers as bss', 'bss.buyer_post_id', '=', 'bp.id')
                    ->leftjoin('users as u', 'u.id', '=', 'bss.seller_id')
                    ->where('bp.id', $buyer_post_id)
                    ->select('bss.seller_id', 'u.username', 'u.id')
                    ->get();
                    //echo "<pre>";print_r($buyer_post_details);exit;
                    if($buyer_post_details[0]->lkp_international_type_id==1){                        
                        $cartons    =   CommonComponent::getCartons();
                        $cartonids=array();
                        foreach($buyer_post_inventory_details as $carton){
                            $cartonids[$carton->lkp_air_carton_type_id]  =   $carton->number_of_cartons;
                        }//echo "<pre>";print_r($cartonids);exit;
                        return view ( 'relocationint.airint.buyers.buyer_edit_seller',[
                                'buyer_post_details' => $buyer_post_details,
                                'cartons' => $cartons,
                                'cartonids'=>$cartonids,
                                'buyer_post_inventory_details' => $buyer_post_inventory_details,
                                'buyer_post_edit_seller' =>$buyer_post_edit_seller
                         ]);
                    }else{
                        return view ( 'relocationint.ocean.buyers.buyer_edit_seller',[
                            'buyer_post_details' => $buyer_post_details,
                            'buyer_post_inventory_details' => $buyer_post_inventory_details,
                            'buyer_post_edit_seller' =>$buyer_post_edit_seller
                        ]);
                    }
                break;
        }//end switch case


  	} catch ( Exception $e ) {
  		echo 'Caught exception: ', $e->getMessage (), "\n";
  	}
    
  }
  
  public function updateRelocationBuyer(){
  	
  	try {
        $serviceId = Session::get('service_id');
        switch($serviceId){
            case RELOCATION_DOMESTIC:   
              		if (isset ( $_POST ['seller_list'] ) && $_POST ['seller_list'] != '') {
              			$seller_list = explode ( ",", $_POST ['seller_list'][0] );
              				
              			//array_shift ( $seller_list );
              			$seller_list_count = count ( $seller_list );
              		}
              		 if(!empty($_POST ['seller_list'][0]) && $seller_list_count > 0)
                        {  
                            for($i = 0; $i <$seller_list_count; $i ++) {

                                    if(Session::get('service_id') == RELOCATION_DOMESTIC){
                                            $buyerpost_for_sellers = new RelocationBuyerSelectedSeller();
                                    }

                                    $buyerpost_for_sellers->buyer_post_id = $_REQUEST['ftl_buyer_quoteid'];
                                    $buyerpost_for_sellers->seller_id = $seller_list [$i];
                                    $created_at = date ( 'Y-m-d H:i:s' );
                                    $createdIp = $_SERVER ['REMOTE_ADDR'];
                                    $buyerpost_for_sellers->lkp_service_id = RELOCATION_DOMESTIC;
                                    $buyerpost_for_sellers->created_by = Auth::id ();
                                    $buyerpost_for_sellers->created_at = $created_at;
                                    $buyerpost_for_sellers->created_ip = $createdIp;
                                    $buyerpost_for_sellers->save ();

                                    $buyer_selected_buyers_email = DB::table ( 'users' )->where ( 'id', $seller_list [$i] )->get ();
                                    //$buyer_selected_buyers_email [0]->randnumber = $randnumber;
                                    $buyer_selected_buyers_email [0]->buyername = Auth::User ()->username;
                                    CommonComponent::send_email ( SELLER_CREATED_POST_FOR_BUYERS, $buyer_selected_buyers_email );
                                    // CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
                            }
                        }
              		return redirect ( '/buyerposts' )->with ( 'message_create_post_ptl', 'Post was updated successfully' );
                break;

            case RELOCATION_OFFICE_MOVE:
                    $seller_list_count = 0;
                   // print_r($_POST ['seller_list']);exit;
                    if (isset ( $_POST ['seller_list'] ) && $_POST ['seller_list'] != '') {
                        $seller_list = explode ( ",", $_POST ['seller_list'][0] );
                        $seller_list_count = count ( $seller_list );
                    }
                    //cho $seller_list_count;exit;
                    if(!empty($_POST ['seller_list'][0]) && $seller_list_count > 0)
                    {    
                        for($i = 0; $i <$seller_list_count; $i ++) 
                        {                    
                            if(Session::get('service_id') == RELOCATION_OFFICE_MOVE)
                            {
                                $buyerpost_for_sellers = new RelocationofficeBuyerSelectedSeller();
                            }
                                
                            $buyerpost_for_sellers->buyer_post_id = $_REQUEST['ftl_buyer_quoteid'];
                            $buyerpost_for_sellers->seller_id = $seller_list [$i];
                            $created_at = date ( 'Y-m-d H:i:s' );
                            $createdIp = $_SERVER ['REMOTE_ADDR'];
                            $buyerpost_for_sellers->lkp_service_id = RELOCATION_OFFICE_MOVE;
                            $buyerpost_for_sellers->created_by = Auth::id ();
                            $buyerpost_for_sellers->created_at = $created_at;
                            $buyerpost_for_sellers->created_ip = $createdIp;
                            $buyerpost_for_sellers->save ();
                        
                            $buyer_selected_buyers_email = DB::table ( 'users' )->where ( 'id', $seller_list [$i] )->get ();
                            //$buyer_selected_buyers_email [0]->randnumber = $randnumber;
                            $buyer_selected_buyers_email [0]->buyername = Auth::User ()->username;
                            CommonComponent::send_email ( SELLER_CREATED_POST_FOR_BUYERS, $buyer_selected_buyers_email );
                            // CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
                        }
                    }
                    return redirect ( '/buyerposts' )->with ( 'message_create_post_ptl', 'Post was updated successfully' );                
                break;
                
                case RELOCATION_PET_MOVE:
                    $seller_list_count = 0;
                    if (isset ( $_POST ['seller_list'] ) && $_POST ['seller_list'] != '') {
                        $seller_list = explode ( ",", $_POST ['seller_list'][0] );
                        $seller_list_count = count ( $seller_list );
                    }
                   //echo "srinu"; echo $seller_list_count; die;
                    if(!empty($_POST ['seller_list'][0]) && $seller_list_count > 0)
                    {   // echo 1; die;
                        for($i = 0; $i <$seller_list_count; $i ++) 
                        {                    
                            if(Session::get('service_id') == RELOCATION_PET_MOVE)
                            {
                                $buyerpost_for_sellers = new RelocationPetBuyerSelectedSellers();
                            }
                                
                            $buyerpost_for_sellers->buyer_post_id = $_REQUEST['ftl_buyer_quoteid'];
                            $buyerpost_for_sellers->seller_id = $seller_list [$i];
                            $created_at = date ( 'Y-m-d H:i:s' );
                            $createdIp = $_SERVER ['REMOTE_ADDR'];
                            $buyerpost_for_sellers->lkp_service_id = RELOCATION_PET_MOVE;
                            $buyerpost_for_sellers->created_by = Auth::id ();
                            $buyerpost_for_sellers->created_at = $created_at;
                            $buyerpost_for_sellers->created_ip = $createdIp;
                            $buyerpost_for_sellers->save ();
                        
                            $buyer_selected_buyers_email = DB::table ( 'users' )->where ( 'id', $seller_list [$i] )->get ();
                            //$buyer_selected_buyers_email [0]->randnumber = $randnumber;
                            $buyer_selected_buyers_email [0]->buyername = Auth::User ()->username;
                            CommonComponent::send_email ( SELLER_CREATED_POST_FOR_BUYERS, $buyer_selected_buyers_email );
                            // CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
                        }
                    }
                    return redirect ( '/buyerposts' )->with ( 'buyer_reloc_pet_move_status', 'Post was updated successfully' );                
                break;
                
                case RELOCATION_INTERNATIONAL:   
              		if (isset ( $_POST ['seller_list'] ) && $_POST ['seller_list'] != '') {
              			$seller_list = explode ( ",", $_POST ['seller_list'][0] );
              			$seller_list_count = count ( $seller_list );
              		}
              		
                        if(!empty($_POST ['seller_list'][0]) && $seller_list_count > 0)
                        { 
                            for($i = 0; $i <$seller_list_count; $i ++) {
                                    $buyerpost_for_sellers = new \App\Models\RelocationintBuyerSelectedSellers();
                                    $buyerpost_for_sellers->buyer_post_id = $_REQUEST['ftl_buyer_quoteid'];
                                    $buyerpost_for_sellers->seller_id = $seller_list [$i];
                                    $created_at = date ( 'Y-m-d H:i:s' );
                                    $createdIp = $_SERVER ['REMOTE_ADDR'];
                                    $buyerpost_for_sellers->lkp_service_id = RELOCATION_INTERNATIONAL;
                                    $buyerpost_for_sellers->created_by = Auth::id ();
                                    $buyerpost_for_sellers->created_at = $created_at;
                                    $buyerpost_for_sellers->created_ip = $createdIp;
                                    $buyerpost_for_sellers->save ();

                                    $buyer_selected_buyers_email = DB::table ( 'users' )->where ( 'id', $seller_list [$i] )->get ();
                                    $buyer_selected_buyers_email [0]->buyername = Auth::User ()->username;
                                    CommonComponent::send_email ( SELLER_CREATED_POST_FOR_BUYERS, $buyer_selected_buyers_email );
                                    // CommonComponent::auditLog($sellerpost_for_buyers->id,'seller_selected_buyers');
                            }
                        }
              		return redirect ( '/buyerposts' )->with ( 'message_create_post_ptl', 'Post was updated successfully' );
                break;
        }//end switch case

  	} catch ( Exception $e ) {
  		echo 'Caught exception: ', $e->getMessage (), "\n";
  	}
  	
  	
  }
  
  public function createRelocationTerm() {
  	
  	try {  	
		$postType=$_REQUEST['spot_term_value'];
		$serviceId = Session::get('service_id');
		$createQuote=TermBuyerComponent::RelocationTermBuyerCreateQuote($serviceId, $_REQUEST, $postType);
		if($createQuote!=''){
                    $postType = $_REQUEST['spot_term_value'];
                    $multi_data_count = count($_REQUEST['from_location']);

                    if (!empty($_REQUEST['confirm_but']) && isset($_REQUEST['confirm_but'])) {
                            $postStatus= OPEN;
                    } else {
                            $postStatus= SAVEDASDRAFT;
                    }
                    if($postStatus == OPEN){
                            return redirect('/relocation/creatbuyerrpost')->with('relocationtransactionNumber', $createQuote)->with('postsCount',$multi_data_count)->with('postType',$postType);
                    }else{
                            return redirect('/buyerposts')->with('sumsg', "Post was saved as draft")->with('postsCount',$multi_data_count)->with('postType',$postType);
                    }
		}
  	} catch ( Exception $e ) {
  		echo 'Caught exception: ', $e->getMessage (), "\n";
  	}
  	
  }


    /* Search place Inventory */
	public function savesearchinventorydetails(){

		try {

			$masterBedRoom=array();
			$masterBedRoom1=array();
			$masterBedRoom2=array();
			$masterBedRoom3=array();
			$lobby=array();
			$kitchen=array();
			$bathroom=array();
			$living=array();
            
			$masterBedRoomTotal=0;
			$masterBedRoom1Total=0;
			$masterBedRoom2Total=0;
			$masterBedRoom3Total=0;
			$lobbyTotal=0;
			$kitchenTotal=0;
			$bathroomTotal=0;
			$livingTotal=0;
			$Totalvolume=0;
			$Totalamount=0;
			$TotalIndentVolume=0;
			
			$masterBedRoomCrating=0;
			$masterBedRoom1Crating=0;
			$masterBedRoom2Crating=0;
			$masterBedRoom3Crating=0;
			$lobbyCrating=0;
			$kitchenCrating=0;
			$bathroomCrating=0;
			$livingCrating=0;
			
			if($_REQUEST['room_type']==1){

				$particulars=CommonComponent::getParticularsByRoomId(1);

				foreach($particulars as $particular){

					$particulardata='roomitems_'.$particular->id;
					$particularcrating='roomcrating_'.$particular->id;

					if(isset($_REQUEST[$particulardata])){


							$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
								->where('particulars.id', $particular->id)
								->select('particulars.volume')
								->get();

							$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];

							$masterBedRoom['indentvolume']=$Totalvolume;


						$masterBedRoom['number_items_'.$particular->id]=$_REQUEST[$particulardata];
						if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
							$masterBedRoom['crating_'.$particular->id]=1;
							$masterBedRoom['totcrating']=$masterBedRoomCrating++;
						}else{
							$masterBedRoom['crating_'.$particular->id]=0;
						}

						$masterBedRoomTotal=$masterBedRoomTotal+$_REQUEST[$particulardata];
						$masterBedRoom['total']=$masterBedRoomTotal;
                        $masterBedRoom['totcrating']=$masterBedRoomCrating;
					}else{
						$masterBedRoom['number_items_'.$particular->id]='';
						$masterBedRoom['crating_'.$particular->id]=0;
					}

					Session::put('masterBedRoom','');
					Session::put('masterBedRoom',$masterBedRoom);

				}
			}


			if($_REQUEST['room_type']==2){
				$particulars=CommonComponent::getParticularsByRoomId(2);
				foreach($particulars as $particular){

					$particulardata='roomitems_'.$particular->id;
					$particularcrating='roomcrating_'.$particular->id;

					if(isset($_REQUEST[$particulardata])){


							$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
								->where('particulars.id', $particular->id)
								->select('particulars.volume')
								->get();


							$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];

							$masterBedRoom1['indentvolume']=$Totalvolume;



						$masterBedRoom1['number_items_'.$particular->id]=$_REQUEST[$particulardata];
						if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
							$masterBedRoom1['crating_'.$particular->id]=1;
							$masterBedRoom1['totcrating']=$masterBedRoom1Crating++;
						}else{
							$masterBedRoom1['crating_'.$particular->id]=0;
						}
						$masterBedRoom1Total=$masterBedRoom1Total+$_REQUEST[$particulardata];
						$masterBedRoom1['total']=$masterBedRoom1Total;
                        $masterBedRoom1['totcrating']=$masterBedRoom1Crating;
					}else{
						$masterBedRoom1['number_items_'.$particular->id]='';
						$masterBedRoom1['crating_'.$particular->id]=0;
					}

					Session::put('masterBedRoom1','');
					Session::put('masterBedRoom1',$masterBedRoom1);
				}
			}

			if($_REQUEST['room_type']==3){
				$particulars=CommonComponent::getParticularsByRoomId(3);
				foreach($particulars as $particular){

					$particulardata='roomitems_'.$particular->id;
					$particularcrating='roomcrating_'.$particular->id;
					if(isset($_REQUEST[$particulardata])){


							$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
								->where('particulars.id', $particular->id)
								->select('particulars.volume')
								->get();

							$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];


							$masterBedRoom2['indentvolume']=$Totalvolume;


						$masterBedRoom2['number_items_'.$particular->id]=$_REQUEST[$particulardata];
						if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
							$masterBedRoom2['crating_'.$particular->id]=1;
							$masterBedRoom2['totcrating']=$masterBedRoom2Crating++;
						}else{
							$masterBedRoom2['crating_'.$particular->id]=0;
						}
						$masterBedRoom2Total=$masterBedRoom2Total+$_REQUEST[$particulardata];
						$masterBedRoom2['total']=$masterBedRoom2Total;
                        $masterBedRoom2['totcrating']=$masterBedRoom2Crating;
					}else{
						$masterBedRoom2['number_items_'.$particular->id]='';
						$masterBedRoom2['crating_'.$particular->id]=0;
					}

					Session::put('masterBedRoom2','');
					Session::put('masterBedRoom2',$masterBedRoom2);
				}
			}


			if($_REQUEST['room_type']==4){
				$particulars=CommonComponent::getParticularsByRoomId(4);
				foreach($particulars as $particular){

					$particulardata='roomitems_'.$particular->id;
					$particularcrating='roomcrating_'.$particular->id;
					if(isset($_REQUEST[$particulardata])){



							$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
								->where('particulars.id', $particular->id)
								->select('particulars.volume')
								->get();


							$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];

							$masterBedRoom3['indentvolume']=$Totalvolume;



						$masterBedRoom3['number_items_'.$particular->id]=$_REQUEST[$particulardata];
						if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
							$masterBedRoom3['crating_'.$particular->id]=1;
							$masterBedRoom3['totcrating']=$masterBedRoom3Crating++;
						}else{
							$masterBedRoom3['crating_'.$particular->id]=0;
						}
						$masterBedRoom3Total=$masterBedRoom3Total+$_REQUEST[$particulardata];
						$masterBedRoom3['total']=$masterBedRoom3Total;
                        $masterBedRoom3['totcrating']=$masterBedRoom3Crating;
					}else{
						$masterBedRoom3['number_items_'.$particular->id]='';
						$masterBedRoom3['crating_'.$particular->id]=0;
					}

					Session::put('masterBedRoom3','');
					Session::put('masterBedRoom3',$masterBedRoom3);
				}
			}

			if($_REQUEST['room_type']==5){
				$particulars=CommonComponent::getParticularsByRoomId(5);
				foreach($particulars as $particular){

					$particulardata='roomitems_'.$particular->id;
					$particularcrating='roomcrating_'.$particular->id;
					if(isset($_REQUEST[$particulardata])){


							$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
								->where('particulars.id', $particular->id)
								->select('particulars.volume')
								->get();


							$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];


							$lobby['indentvolume']=$Totalvolume;


						$lobby['number_items_'.$particular->id]=$_REQUEST[$particulardata];
						if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
							$lobby['crating_'.$particular->id]=1;
							$lobby['totcrating']=$lobbyCrating++;
						}else{
							$lobby['crating_'.$particular->id]=0;
						}
						$lobbyTotal=$lobbyTotal+$_REQUEST[$particulardata];
						$lobby['total']=$lobbyTotal;
                        $lobby['totcrating']=$lobbyCrating;
					}else{
						$lobby['number_items_'.$particular->id]='';
						$lobby['crating_'.$particular->id]=0;
					}

					Session::put('lobby','');
					Session::put('lobby',$lobby);
				}
			}

			if($_REQUEST['room_type']==6){
				$particulars=CommonComponent::getParticularsByRoomId(6);
				foreach($particulars as $particular){

					$particulardata='roomitems_'.$particular->id;
					$particularcrating='roomcrating_'.$particular->id;
					if(isset($_REQUEST[$particulardata])){


							$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
								->where('particulars.id', $particular->id)
								->select('particulars.volume')
								->get();

							$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];

							$Totalamount=$Totalvolume*$_REQUEST['contractprice'];

							$kitchen['indentvolume']=$Totalvolume;


						$kitchen['number_items_'.$particular->id]=$_REQUEST[$particulardata];
						if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
							$kitchen['crating_'.$particular->id]=1;
							$kitchen['totcrating']=$kitchenCrating++;
						}else{
							$kitchen['crating_'.$particular->id]=0;
						}
						$kitchenTotal=$kitchenTotal+$_REQUEST[$particulardata];
						$kitchen['total']=$kitchenTotal;
                        $kitchen['totcrating']=$kitchenCrating;
					}else{
						$kitchen['number_items_'.$particular->id]='';
						$kitchen['crating_'.$particular->id]=0;
					}

					Session::put('kitchen','');
					Session::put('kitchen',$kitchen);
				}
			}


			if($_REQUEST['room_type']==7){
				$particulars=CommonComponent::getParticularsByRoomId(7);
				foreach($particulars as $particular){

					$particulardata='roomitems_'.$particular->id;
					$particularcrating='roomcrating_'.$particular->id;
					if(isset($_REQUEST[$particulardata])){


							$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
								->where('particulars.id', $particular->id)
								->select('particulars.volume')
								->get();


							$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];


							$bathroom['indentvolume']=$Totalvolume;


						$bathroom['number_items_'.$particular->id]=$_REQUEST[$particulardata];
						if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
							$bathroom['crating_'.$particular->id]=1;
							$bathroom['totcrating']=$bathroomCrating++;
						}else{
							$bathroom['crating_'.$particular->id]=0;

						}
						$bathroomTotal=$bathroomTotal+$_REQUEST[$particulardata];
						$bathroom['total']=$bathroomTotal;
                        $bathroom['totcrating']=$bathroomCrating;
					}else{
						$bathroom['number_items_'.$particular->id]='';
						$bathroom['crating_'.$particular->id]=0;

					}


					Session::put('bathroom','');
					Session::put('bathroom',$bathroom);
				}
			}
             
			if($_REQUEST['room_type']==8){
				$particulars=CommonComponent::getParticularsByRoomId(8);
				foreach($particulars as $particular){

					$particulardata='roomitems_'.$particular->id;
					$particularcrating='roomcrating_'.$particular->id;
					if(isset($_REQUEST[$particulardata])){


							$inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
								->where('particulars.id', $particular->id)
								->select('particulars.volume')
								->get();

							$Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];


							$living['indentvolume']=$Totalvolume;


						$living['number_items_'.$particular->id]=$_REQUEST[$particulardata];
						if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
							$living['crating_'.$particular->id]=1;
							$living['totcrating']=$livingCrating++;
						}else{
							$living['crating_'.$particular->id]=0;
						}
						$livingTotal=$livingTotal+$_REQUEST[$particulardata];
						$living['total']=$livingTotal;
                        $living['totcrating']=$livingCrating;
					}else{
						$living['number_items_'.$particular->id]='';
						$living['crating_'.$particular->id]=0;
					}


					Session::put('living','');
					Session::put('living',$living);
				}

			}


				$TotalIndentVolume=0;
				$TotalCrtaing=0;
				if(Session::has('masterBedRoom')){
					$masterBedRoomVolme=Session::get('masterBedRoom');
					$TotalIndentVolume=$TotalIndentVolume+$masterBedRoomVolme['indentvolume'];
                    if(isset($masterBedRoomVolme['totcrating'])){
					$TotalCrtaing=$TotalCrtaing+$masterBedRoomVolme['totcrating'];
                    }
				}
				if(Session::has('masterBedRoom1')){
					$masterBedRoom1Volme=Session::get('masterBedRoom1');
					$TotalIndentVolume=$TotalIndentVolume+$masterBedRoom1Volme['indentvolume'];
					if(isset($masterBedRoom1Volme['totcrating'])){
                    $TotalCrtaing=$TotalCrtaing+$masterBedRoom1Volme['totcrating'];
                    }
				}
				if(Session::has('masterBedRoom2')){
					$masterBedRoom2Volme=Session::get('masterBedRoom2');
					$TotalIndentVolume=$TotalIndentVolume+$masterBedRoom2Volme['indentvolume'];
                    if(isset($masterBedRoom2Volme['totcrating'])){
					$TotalCrtaing=$TotalCrtaing+$masterBedRoom2Volme['totcrating'];
                    }
				}
				if(Session::has('masterBedRoom3')){
					$masterBedRoom3Volme=Session::get('masterBedRoom3');
					$TotalIndentVolume=$TotalIndentVolume+$masterBedRoom3Volme['indentvolume'];
                    if(isset($masterBedRoom3Volme['totcrating'])){
					$TotalCrtaing=$TotalCrtaing+$masterBedRoom3Volme['totcrating'];
                    }
				}
				if(Session::has('lobby')){
					$lobbyVolme=Session::get('lobby');
					$TotalIndentVolume=$TotalIndentVolume+$lobbyVolme['indentvolume'];
                    if(isset($lobbyVolme['totcrating'])){
					$TotalCrtaing=$TotalCrtaing+$lobbyVolme['totcrating'];
                    }
				}
				if(Session::has('kitchen')){
					$kitchenvolume=Session::get('kitchen');
					$TotalIndentVolume=$TotalIndentVolume+$kitchenvolume['indentvolume'];
                    if(isset($kitchenvolume['totcrating'])){
					$TotalCrtaing=$TotalCrtaing+$kitchenvolume['totcrating'];
                    }
				}
				if(Session::has('bathroom')){
					$bathroomVolme=Session::get('bathroom');
					$TotalIndentVolume=$TotalIndentVolume+$bathroomVolme['indentvolume'];
                    if(isset($bathroomVolme['totcrating'])){
					$TotalCrtaing=$TotalCrtaing+$bathroomVolme['totcrating'];
                    }
				}
				if(Session::has('living')){
					$livingroomVolme=Session::get('living');
					$TotalIndentVolume=$TotalIndentVolume+$livingroomVolme['indentvolume'];
                    if(isset($livingroomVolme['totcrating'])){
					$TotalCrtaing=$TotalCrtaing+$livingroomVolme['totcrating'];
                    }
				}

				$TotalIndentVolume=$TotalIndentVolume;
				$TotalCrtaing=$TotalCrtaing;
				


			$returnHTML = view('relocation.buyers.rooms_inventory_count')->render();
			return response()->json(array('success' => true,'html'=>$returnHTML,'TotalIndentVolume'=>$TotalIndentVolume,'TotalCrtaing'=>$TotalCrtaing));

		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}

	}

 public function chekckbuyerofficepost(){
 	
 	$buyerpostcheck = DB::table('relocationoffice_buyer_posts as buyerposts')
 	->where('buyerposts.from_location_id', $_REQUEST['city'])
 	->where('buyerposts.dispatch_date', $_REQUEST['from_date'])
 	->where('buyerposts.dispatch_date', $_REQUEST['to_date'])
 	->select('buyerposts.id')
 	->get();
 	
 	return count($buyerpostcheck); 
  }
  
  
/**
 * Saving Relocation Int buyer post creation
 * @author Shriram
 * @param POST data
 */
 private function saveRelocationInt($request){    
    // Below lines will generate unique transaction id
    $created_at = date ( 'Y-m-d H:i:s' );
    $createdIp = $_SERVER ['REMOTE_ADDR'];
    $postid  =   CommonComponent::getPostID(Session::get('service_id'));
    $randnumber = 'REL-INT/'.date('Y').'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
    $cartons    =   CommonComponent::getCartons();
    $tot=0;
    
    if($request['post_type']==1){
            for($i = 1; $i <= count($cartons); $i++){  
            if($request['cartons_'.$i]!=""){
                $tot+=$cartons[$i-1]->weight*$request['cartons_'.$i];
                }
            }
            $buyerPostCreate = new \App\Models\RelocationintBuyerPost();
            $buyerPostCreate->from_location_id      = $request['from_location_id'];
            $buyerPostCreate->to_location_id      = $request['to_location_id'];
            $buyerPostCreate->lkp_service_id      = RELOCATION_INTERNATIONAL;
            $buyerPostCreate->buyer_id      = Auth::id();
            $buyerPostCreate->transaction_id      = $randnumber;
            $buyerPostCreate->lkp_lead_type_id      = SPOT;
            $buyerPostCreate->lkp_post_status_id      = 2;
            $buyerPostCreate->lkp_international_type_id      = 1;
            $buyerPostCreate->lkp_quote_access_id      = $request['ptlQuoteaccessId'];
            $buyerPostCreate->dispatch_date      = CommonComponent::convertDateForDatabase($request['valid_from']);
            $buyerPostCreate->delivery_date      = CommonComponent::convertDateForDatabase($request['valid_to']);
            $buyerPostCreate->is_dispatch_flexible      = isset($request['dispatch_flexible_hidden']) ? $request['dispatch_flexible_hidden'] : 0;
            $buyerPostCreate->is_delivery_flexible      = isset($request['delivery_flexible_hidden']) ? $request['delivery_flexible_hidden'] : 0;
            $buyerPostCreate->total_cartons_weight=$tot;
            $buyerPostCreate->created_by      = Auth::id();
            $buyerPostCreate->created_ip      = $createdIp;
            $buyerPostCreate->created_at      = $created_at;
            $buyerPostCreate->save ();
            
            
            for($i = 1; $i <= count($cartons); $i++){  
                
                if($request['cartons_'.$i]!=""){
                    $petBuyerSelSeller = new \App\Models\RelocationintBuyerPostAirCarton();   
                    $petBuyerSelSeller->lkp_service_id=RELOCATION_INTERNATIONAL;
                    $petBuyerSelSeller->buyer_post_id      = $buyerPostCreate->id;
                    $petBuyerSelSeller->lkp_air_carton_type_id      = $i;
                    $petBuyerSelSeller->number_of_cartons      = $request['cartons_'.$i];
                    $petBuyerSelSeller->created_by      = Auth::id();
                    $petBuyerSelSeller->created_ip      = $createdIp;
                    $petBuyerSelSeller->created_at      = $created_at;
                    $petBuyerSelSeller->save (); 
                }
            }//exit; 
    } else {
        $getRelOceanIntId= $this->saveRelIntOceanGetQuote($request);
    }
    
    if($request['post_type']==1){
        $buyerLastPostId  = $buyerPostCreate->id;
    } else {
        $buyerLastPostId  = $getRelOceanIntId;
    }
                
    // if is private post
    if ($request['ptlQuoteaccessId'] == 2){
        
        // Checking private seller list
        if ($request['seller_list'] != '') {
            // Converting comma separated input into Array and then couting the seller list
            $seller_list = explode (",", $request['seller_list']);
            $seller_list_count = count($seller_list);
            // Initialise the Relocation Pet Selected Sellers
            
            for($i = 0; $i <$seller_list_count; $i++):      
                // Saving Records on relocationpet_buyer_selected_sellers    
                $petBuyerSelSeller = new \App\Models\RelocationintBuyerSelectedSellers();
                $petBuyerSelSeller->lkp_service_id=RELOCATION_INTERNATIONAL;
                $petBuyerSelSeller->buyer_post_id      = $buyerLastPostId;
                $petBuyerSelSeller->seller_id      = $seller_list[$i];
                $petBuyerSelSeller->created_by      = Auth::id();
                $petBuyerSelSeller->created_ip      = $createdIp;
                $petBuyerSelSeller->created_at      = $created_at;
                $petBuyerSelSeller->save ();   

//                // Saving Records on relocationpet_buyer_quote_sellers_quotes_prices
//                $petBuyerQuotePrice = new \App\Models\RelocationintBuyerQuoteSellersQuotesPrice;
//                $petBuyerQuotePrice->lkp_service_id = RELOCATION_INTERNATIONAL;
//                $petBuyerQuotePrice->buyer_id       = Auth::id();
//                $petBuyerQuotePrice->buyer_quote_id = $buyerLastPostId;
//                $petBuyerQuotePrice->seller_id      = $seller_list[$i];
//                $petBuyerQuotePrice->created_by     = Auth::id();
//                $petBuyerQuotePrice->created_by     = $created_at;
//                $petBuyerQuotePrice->created_ip     = $createdIp;
//                $petBuyerQuotePrice->save();

                $buyer_selected_buyers_email = DB::table('users')
                    ->where('id', $seller_list[$i])->get();
                $buyer_selected_buyers_email[0]->randnumber = $randnumber;
                $buyer_selected_buyers_email[0]->buyername = Auth::User()->username;
                CommonComponent::send_email(BUYER_CREATED_POST_FOR_SELLERS, $buyer_selected_buyers_email
                );
                
                //*******Send Sms to the private Sellers***********************//
                $getMobileNumber  =   CommonComponent::getMobleNumber($seller_list[$i]);
                if($getMobileNumber):
                    CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,[
                        'randnumber' => $randnumber,
                        'buyername' => Auth::User()->username,
                        'servicename' => RELOCATIONINT_BUYER_SMS_SERVICENAME
                    ]);
                endif;
                //*******Send Sms to the private Sellers***********************//
                
            endfor;
        }
    }   
    
    if($request['post_type']==1){
        //below array for matching engine in Relocation pet start
        $matchedItems['from_location_id_intre']=$_POST ['from_location_id'];
        $matchedItems['to_location_id_intre']=$_POST ['to_location_id'];
        $matchedItems['valid_from']=$_POST['valid_from'];
        $matchedItems['valid_to']=$_POST['valid_to']; 
        $matchedItems['from_location_id']=$_POST ['from_location_id'];
        $matchedItems['to_location_id']=$_POST ['to_location_id'];
        $matchedItems['from_date']=$_POST['valid_from'];
        $matchedItems['to_date']=$_POST['valid_to']; 
        $matchedItems['post_type']=$_POST['post_type']; 
        $totReqWeight   =   $buyerPostCreate->total_cartons_weight;
        $slab = DB::table('lkp_air_weight_slabs as spi' );		
        $slab->whereRaw("$totReqWeight between min_slab_weight and max_slab_weight");
        $slabInfo = $slab->first();
        if(isset($slabInfo->id)):
                $matchedItems['slab_id'] = $slabInfo->id;
                $matchedItems['weight'] = $totReqWeight;
        else:
                $matchedItems['slab_id'] = '';
                $matchedItems['weight'] = 0;
        endif;	
    } else {
        //below array for matching engine in Relocation int ocean start
        
        $matchedItems['from_location_id_intre']=$_POST ['from_location_id_intre'];
        $matchedItems['to_location_id_intre']=$_POST ['to_location_id_intre'];
        $matchedItems['valid_from']=$_POST['valid_from'];
        $matchedItems['valid_to']=$_POST['valid_to']; 
        $matchedItems['post_type']=$_POST['post_type'];
        
        global $propertyInfo;
        if(!empty($request['property_type'])){
            $propertyInfo = DB::table('lkp_property_types')->where("id",'=', (int)$request['property_type'])->first();
            $volume = $propertyInfo->volume;
        }
        $volumeincbm = number_format($volume/35.5,2);
        if($volumeincbm != 0){
            $shipmenttype = DB::table('lkp_relocation_shipment_volumes as spi' )
                            ->whereRaw("$volumeincbm between min_volume and max_volume")->first();
            $matchedItems['shipment_volume_type_id'] = $shipmenttype->id;
            
        }
    }
    
    BuyerMatchingComponent::doMatching(RELOCATION_INTERNATIONAL,$buyerLastPostId,2,$matchedItems);
    
    return redirect('/relocation/creatbuyerrpost')->with('relocationtransactionNumber', $randnumber);
 }
 
 /*
  * Relocation intenational ocean get quote form insertion
  */
 public function saveRelIntOceanGetQuote($request){     
                        //echo "<pre>"; print_r($_REQUEST); die;             		
             		$postid  =   CommonComponent::getPostID(Session::get ( 'service_id' ));            		
             		$created_year = date('Y');             		
             		$randnumber = 'REL-INTR/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT); 
                        $created_at = date ( 'Y-m-d H:i:s' );
                        $createdIp = $_SERVER ['REMOTE_ADDR'];
                        
                        $buyerPostCreate = new \App\Models\RelocationintBuyerPost();
                        $buyerPostCreate->from_location_id      = $request['from_location_id_intre'];
                        $buyerPostCreate->to_location_id        = $request['to_location_id_intre'];
                        $buyerPostCreate->lkp_property_type_id  = $request['property_type'];
                        $buyerPostCreate->lkp_service_id        = RELOCATION_INTERNATIONAL;
                        $buyerPostCreate->buyer_id              = Auth::id();
                        $buyerPostCreate->transaction_id        = $randnumber;
                        $buyerPostCreate->lkp_lead_type_id      = SPOT;
                        $buyerPostCreate->lkp_post_status_id    = 2;
                        $buyerPostCreate->lkp_international_type_id      = 2;
                        $buyerPostCreate->lkp_quote_access_id      = $request['ptlQuoteaccessId'];
                        $buyerPostCreate->dispatch_date            = CommonComponent::convertDateForDatabase($request['valid_from']);
                        $buyerPostCreate->delivery_date            = CommonComponent::convertDateForDatabase($request['valid_to']);
                        $buyerPostCreate->is_dispatch_flexible     = $request['dispatch_flexible_hidden_relocint'];
                        $buyerPostCreate->is_delivery_flexible     = $request['delivery_flexible_hidden_relocint'];                        
                        
                        if(isset($_POST['origin_storage_serivce'])){
                                $buyerPostCreate->origin_storage = 1;
                        }
                        if(isset($_POST['origin_handy_serivce'])){
                                $buyerPostCreate->origin_handyman_services = 1;
                        }                        
                        if(isset($_POST['insurance_serivce'])){
                                $buyerPostCreate->insurance = 1;
                        }
                        
                        if(isset($_POST['destination_storage_serivce'])){
                                $buyerPostCreate->destination_storage = 1;
                        }
                        if(isset($_POST['destination_handy_serivce'])){
                                $buyerPostCreate->destination_handyman_services = 1;
                        }
                        $buyerPostCreate->created_by      = Auth::id ();
                        $buyerPostCreate->created_ip      = $createdIp;
                        $buyerPostCreate->created_at      = $created_at;
                        $buyerPostCreate->save ();             		
             		
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
                       return $buyerPostCreate->id;
 }
  
  public function saveinventorydetailsRelocationOcean(){
 	
 	try {
 		
 		$masterBedRoom=array();
 		$masterBedRoom1=array();
 		$masterBedRoom2=array();
 		$masterBedRoom3=array();
 		$lobby=array();
 		$kitchen=array();
 		$bathroom=array();
 		$living=array();
 		
 		  
                $masterBedRoomTotal=0;
                $masterBedRoom1Total=0;
                $masterBedRoom2Total=0;
                $masterBedRoom3Total=0;
                $lobbyTotal=0;
                $kitchenTotal=0;
                $bathroomTotal=0;
                $livingTotal=0;
                $Totalvolume=0;
                $Totalamount=0;
                $TotalIndentVolume=0;
                $TotalIndentPrice=0;
                if($_REQUEST['room_type']==1){

                      $particulars=CommonComponent::getParticularsByRoomId(1);

                      foreach($particulars as $particular){

                         $particulardata='roomitems_'.$particular->id;
                         $particularcrating='roomcrating_'.$particular->id;

                         if(isset($_REQUEST[$particulardata])){

                              if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){

                                      $inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
                                      ->where('particulars.id', $particular->id)
                                      ->select('particulars.volume')
                                      ->get();

                                      $crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
                                      ->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
                                      ->select('sellerquotes.crating_charges')
                                      ->get();


                                      if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){

                                      $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
                                      }else{
                                      $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
                                      }
                                      $Totalamount=$Totalvolume*$_REQUEST['contractprice'];

                                      $masterBedRoom['indentvolume']=$Totalvolume;
                                      $masterBedRoom['indetnfreight']=$Totalamount;
                              }

                              $masterBedRoom['number_items_'.$particular->id]=$_REQUEST[$particulardata];
                              if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
                              $masterBedRoom['crating_'.$particular->id]=1;
                              }else{
                              $masterBedRoom['crating_'.$particular->id]=0;
                              }

                              $masterBedRoomTotal=$masterBedRoomTotal+$_REQUEST[$particulardata];
                              $masterBedRoom['total']=$masterBedRoomTotal;
                         }else{
                              $masterBedRoom['number_items_'.$particular->id]='';
                              $masterBedRoom['crating_'.$particular->id]=0;
                         }

                         Session::put('masterBedRoom','');
                         Session::put('masterBedRoom',$masterBedRoom);

                         }
                }  


                         if($_REQUEST['room_type']==2){
                              $particulars=CommonComponent::getParticularsByRoomId(2);
                              foreach($particulars as $particular){

                                      $particulardata='roomitems_'.$particular->id;
                                      $particularcrating='roomcrating_'.$particular->id;

                              if(isset($_REQUEST[$particulardata])){

                                      if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){

                                              $inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
                                              ->where('particulars.id', $particular->id)
                                              ->select('particulars.volume')
                                              ->get();

                                              $crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
                                              ->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
                                              ->select('sellerquotes.crating_charges')
                                              ->get();



                                              if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){

                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
                                              }else{
                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
                                              }

                                              $Totalamount=$Totalvolume*$_REQUEST['contractprice'];

                                              $masterBedRoom1['indentvolume']=$Totalvolume;
                                              $masterBedRoom1['indetnfreight']=$Totalamount;
                                      }

                                      $masterBedRoom1['number_items_'.$particular->id]=$_REQUEST[$particulardata];
                                      if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
                                              $masterBedRoom1['crating_'.$particular->id]=1;
                                      }else{
                                              $masterBedRoom1['crating_'.$particular->id]=0;
                                      }
                              $masterBedRoom1Total=$masterBedRoom1Total+$_REQUEST[$particulardata];
                              $masterBedRoom1['total']=$masterBedRoom1Total;
                              }else{
                                      $masterBedRoom1['number_items_'.$particular->id]='';
                                      $masterBedRoom1['crating_'.$particular->id]=0;
                              }

                              Session::put('masterBedRoom1','');
                              Session::put('masterBedRoom1',$masterBedRoom1);
                         }
                        }

                         if($_REQUEST['room_type']==3){
                              $particulars=CommonComponent::getParticularsByRoomId(3);
                              foreach($particulars as $particular){

                                      $particulardata='roomitems_'.$particular->id;
                                      $particularcrating='roomcrating_'.$particular->id;
                              if(isset($_REQUEST[$particulardata])){

                                      if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){

                                              $inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
                                              ->where('particulars.id', $particular->id)
                                              ->select('particulars.volume')
                                              ->get();

                                         $crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
                                              ->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
                                              ->select('sellerquotes.crating_charges')
                                              ->get();



                                              if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){

                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
                                              }else{
                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
                                              }
                                              $Totalamount=$Totalvolume*$_REQUEST['contractprice'];

                                              $masterBedRoom2['indentvolume']=$Totalvolume;
                                              $masterBedRoom2['indetnfreight']=$Totalamount;
                                      }

                                      $masterBedRoom2['number_items_'.$particular->id]=$_REQUEST[$particulardata];
                                      if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
                                              $masterBedRoom2['crating_'.$particular->id]=1;
                                      }else{
                                              $masterBedRoom2['crating_'.$particular->id]=0;
                                      }
                              $masterBedRoom2Total=$masterBedRoom2Total+$_REQUEST[$particulardata];
                              $masterBedRoom2['total']=$masterBedRoom2Total;
                              }else{
                                      $masterBedRoom2['number_items_'.$particular->id]='';
                                      $masterBedRoom2['crating_'.$particular->id]=0;
                              }

                              Session::put('masterBedRoom2','');
                              Session::put('masterBedRoom2',$masterBedRoom2);
                         }
                         }


                         if($_REQUEST['room_type']==4){
                              $particulars=CommonComponent::getParticularsByRoomId(4);
                              foreach($particulars as $particular){

                                      $particulardata='roomitems_'.$particular->id;
                                      $particularcrating='roomcrating_'.$particular->id;
                              if(isset($_REQUEST[$particulardata])){

                                      if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){

                                              $inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
                                              ->where('particulars.id', $particular->id)
                                              ->select('particulars.volume')
                                              ->get();

                                              $crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
                                              ->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
                                              ->select('sellerquotes.crating_charges')
                                              ->get();



                                              if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){

                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
                                              }else{
                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
                                              }
                                              $Totalamount=$Totalvolume*$_REQUEST['contractprice'];

                                              $masterBedRoom3['indentvolume']=$Totalvolume;
                                              $masterBedRoom3['indetnfreight']=$Totalamount;
                                      }

                                      $masterBedRoom3['number_items_'.$particular->id]=$_REQUEST[$particulardata];
                                      if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
                                              $masterBedRoom3['crating_'.$particular->id]=1;
                                      }else{
                                              $masterBedRoom3['crating_'.$particular->id]=0;
                                      }
                              $masterBedRoom3Total=$masterBedRoom3Total+$_REQUEST[$particulardata];
                              $masterBedRoom3['total']=$masterBedRoom3Total;
                              }else{
                                      $masterBedRoom3['number_items_'.$particular->id]='';
                                      $masterBedRoom3['crating_'.$particular->id]=0;
                              }

                              Session::put('masterBedRoom3','');
                              Session::put('masterBedRoom3',$masterBedRoom3);
                         }
                         }

                         if($_REQUEST['room_type']==5){
                              $particulars=CommonComponent::getParticularsByRoomId(5);
                              foreach($particulars as $particular){

                                      $particulardata='roomitems_'.$particular->id;
                                      $particularcrating='roomcrating_'.$particular->id;
                              if(isset($_REQUEST[$particulardata])){

                                      if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){

                                              $inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
                                              ->where('particulars.id', $particular->id)
                                              ->select('particulars.volume')
                                              ->get();

                                       $crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
                                              ->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
                                              ->select('sellerquotes.crating_charges')
                                              ->get();



                                              if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){

                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
                                              }else{
                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
                                              }

                                              $Totalamount=$Totalvolume*$_REQUEST['contractprice'];

                                              $lobby['indentvolume']=$Totalvolume;
                                              $lobby['indetnfreight']=$Totalamount;
                                      }

                                      $lobby['number_items_'.$particular->id]=$_REQUEST[$particulardata];
                                      if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
                                              $lobby['crating_'.$particular->id]=1;
                                      }else{
                                              $lobby['crating_'.$particular->id]=0;
                                      }
                              $lobbyTotal=$lobbyTotal+$_REQUEST[$particulardata];
                              $lobby['total']=$lobbyTotal;
                              }else{
                                      $lobby['number_items_'.$particular->id]='';
                                      $lobby['crating_'.$particular->id]=0;
                              }

                              Session::put('lobby','');
                              Session::put('lobby',$lobby);
                         }
                         }

                         if($_REQUEST['room_type']==6){
                              $particulars=CommonComponent::getParticularsByRoomId(6);
                              foreach($particulars as $particular){

                                      $particulardata='roomitems_'.$particular->id;
                                      $particularcrating='roomcrating_'.$particular->id;
                              if(isset($_REQUEST[$particulardata])){

                                      if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){

                                              $inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
                                              ->where('particulars.id', $particular->id)
                                              ->select('particulars.volume')
                                              ->get();

                                              $crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
                                              ->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
                                              ->select('sellerquotes.crating_charges')
                                              ->get();



                                              if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){

                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
                                              }else{
                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
                                              }

                                              $Totalamount=$Totalvolume*$_REQUEST['contractprice'];

                                              $kitchen['indentvolume']=$Totalvolume;
                                              $kitchen['indetnfreight']=$Totalamount;
                                      }

                                      $kitchen['number_items_'.$particular->id]=$_REQUEST[$particulardata];
                                      if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
                                              $kitchen['crating_'.$particular->id]=1;
                                      }else{
                                              $kitchen['crating_'.$particular->id]=0;
                                      }
                              $kitchenTotal=$kitchenTotal+$_REQUEST[$particulardata];
                              $kitchen['total']=$kitchenTotal;
                              }else{
                                      $kitchen['number_items_'.$particular->id]='';
                                      $kitchen['crating_'.$particular->id]=0;
                              }

                              Session::put('kitchen','');
                              Session::put('kitchen',$kitchen);
                         }
                         }


                         if($_REQUEST['room_type']==7){
                              $particulars=CommonComponent::getParticularsByRoomId(7);
                              foreach($particulars as $particular){

                                      $particulardata='roomitems_'.$particular->id;
                                      $particularcrating='roomcrating_'.$particular->id;
                              if(isset($_REQUEST[$particulardata])){

                                      if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){

                                              $inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
                                              ->where('particulars.id', $particular->id)
                                              ->select('particulars.volume')
                                              ->get();

                                        $crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
                                              ->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
                                              ->select('sellerquotes.crating_charges')
                                              ->get();



                                              if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){

                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
                                              }else{
                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
                                              }
                                              $Totalamount=$Totalvolume*$_REQUEST['contractprice'];

                                              $bathroom['indentvolume']=$Totalvolume;
                                              $bathroom['indetnfreight']=$Totalamount;
                                      }

                                      $bathroom['number_items_'.$particular->id]=$_REQUEST[$particulardata];
                                      if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
                                              $bathroom['crating_'.$particular->id]=1;
                                      }else{
                                              $bathroom['crating_'.$particular->id]=0;
                                      }
                              $bathroomTotal=$bathroomTotal+$_REQUEST[$particulardata];
                              $bathroom['total']=$bathroomTotal;
                              }else{
                                      $bathroom['number_items_'.$particular->id]='';
                                      $bathroom['crating_'.$particular->id]=0;
                              }


                              Session::put('bathroom','');
                              Session::put('bathroom',$bathroom);
                         }
                         } 

                         if($_REQUEST['room_type']==8){
                              $particulars=CommonComponent::getParticularsByRoomId(8);
                              foreach($particulars as $particular){

                                      $particulardata='roomitems_'.$particular->id;
                                      $particularcrating='roomcrating_'.$particular->id;
                              if(isset($_REQUEST[$particulardata])){

                                      if(isset($_REQUEST['placeindent']) && $_REQUEST['placeindent']==1){

                                              $inventoryvolume = DB::table('lkp_inventory_room_particulars as particulars')
                                              ->where('particulars.id', $particular->id)
                                              ->select('particulars.volume')
                                              ->get();

                                       $crating = DB::table('term_buyer_quote_sellers_quotes_prices as sellerquotes')
                                              ->where('sellerquotes.term_buyer_quote_id', $_REQUEST['quoteId'])
                                              ->select('sellerquotes.crating_charges')
                                              ->get();



                                              if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){

                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata]*$crating[0]->crating_charges;
                                              }else{
                                              $Totalvolume=$Totalvolume+$inventoryvolume[0]->volume*$_REQUEST[$particulardata];
                                              }
                                              $Totalamount=$Totalvolume*$_REQUEST['contractprice'];

                                              $living['indentvolume']=$Totalvolume;
                                              $living['indetnfreight']=$Totalamount;
                                      }

                                      $living['number_items_'.$particular->id]=$_REQUEST[$particulardata];
                                      if(isset($_REQUEST[$particularcrating]) && $_REQUEST[$particularcrating]=='on'){
                                              $living['crating_'.$particular->id]=1;
                                      }else{
                                              $living['crating_'.$particular->id]=0;
                                      }
                              $livingTotal=$livingTotal+$_REQUEST[$particulardata];
                              $living['total']=$livingTotal;
                              }else{
                                      $living['number_items_'.$particular->id]='';
                                      $living['crating_'.$particular->id]=0;
                              }


                              Session::put('living','');
                              Session::put('living',$living);
                         }

                        } 
 		  	  
 		if(isset($_REQUEST['placeindent'])){
 		$TotalIndentVolume=0;
 		$TotalIndentPrice=0;
 		
 		if(Session::has('masterBedRoom')){	
 		$masterBedRoomVolme=Session::get('masterBedRoom');
 		$TotalIndentVolume=$TotalIndentVolume+$masterBedRoomVolme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$masterBedRoomVolme['indetnfreight'];
 		}
 		if(Session::has('masterBedRoom1')){
 		$masterBedRoom1Volme=Session::get('masterBedRoom1');
 		$TotalIndentVolume=$TotalIndentVolume+$masterBedRoom1Volme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$masterBedRoom1Volme['indetnfreight'];
 		}
 		if(Session::has('masterBedRoom2')){
 		$masterBedRoom2Volme=Session::get('masterBedRoom2');
 		$TotalIndentVolume=$TotalIndentVolume+$masterBedRoom2Volme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$masterBedRoom2Volme['indetnfreight'];
 		}
 		if(Session::has('masterBedRoom3')){
 		$masterBedRoom3Volme=Session::get('masterBedRoom3');
 		$TotalIndentVolume=$TotalIndentVolume+$masterBedRoom3Volme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$masterBedRoom3Volme['indetnfreight'];
		}
		if(Session::has('lobby')){
 		$lobbyVolme=Session::get('lobby');
 		$TotalIndentVolume=$TotalIndentVolume+$lobbyVolme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$lobbyVolme['indetnfreight'];
		}
		if(Session::has('kitchen')){
 		$kitchenvolume=Session::get('kitchen');
 		$TotalIndentVolume=$TotalIndentVolume+$kitchenvolume['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$kitchenvolume['indetnfreight'];
		}
		if(Session::has('bathroom')){
 		$bathroomVolme=Session::get('bathroom');
 		$TotalIndentVolume=$TotalIndentVolume+$bathroomVolme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$bathroomVolme['indetnfreight'];
		}
		if(Session::has('living')){			
 		$livingroomVolme=Session::get('living');
 		$TotalIndentVolume=$TotalIndentVolume+$livingroomVolme['indentvolume'];
 		$TotalIndentPrice=$TotalIndentPrice+$livingroomVolme['indetnfreight'];
		}
 		
 		$TotalIndentVolume=$TotalIndentVolume;
 		$TotalIndentPrice=$TotalIndentPrice;
 		
 		
 		}
 		
 		$returnHTML = view('relocation.buyers.rooms_inventory_count')->render();
 		return response()->json(array('success' => true,'TotalIndentVolume'=>$TotalIndentVolume,'TotalIndentPrice'=>$TotalIndentPrice, 'html'=>$returnHTML));
 		
 	} catch ( Exception $e ) {
 		echo 'Caught exception: ', $e->getMessage (), "\n";
 	}
 	
 }
 
 /**
 * Saving Relocation Int buyer post creation
 * @author Shriram
 * @param POST data
 */
 private function saveRelocationGm($request){    
     //echo "<pre>";print_r($request);die;
    // Below lines will generate unique transaction id
    $created_at = date ( 'Y-m-d H:i:s' );
    $createdIp = $_SERVER ['REMOTE_ADDR'];
    $postid  =   CommonComponent::getPostID(Session::get('service_id'));
    $randnumber = 'RELOCATIONGM/'.date('Y').'/'. str_pad($postid, 6, "0", STR_PAD_LEFT);
    //$cartons    =   CommonComponent::getCartons();
    $tot=0;
    
    
            
            $buyerPostCreate = new \App\Models\RelocationgmBuyerPost();
            $buyerPostCreate->location_id      = $request['to_location_id'];
            $buyerPostCreate->lkp_service_id      = RELOCATION_GLOBAL_MOBILITY;
            $buyerPostCreate->buyer_id      = Auth::id();
            $buyerPostCreate->transaction_id      = $randnumber;
            $buyerPostCreate->lkp_post_status_id      = 2;
            $buyerPostCreate->lkp_quote_access_id      = $request['ptlQuoteaccessId'];
            $buyerPostCreate->dispatch_date      = CommonComponent::convertDateForDatabase($request['dispatch_date']);
            //$buyerPostCreate->delivery_date      = CommonComponent::convertDateForDatabase($request['valid_to']);
            $buyerPostCreate->is_dispatch_flexible      = isset($request['dispatch_flexible_hidden']) ? $request['dispatch_flexible_hidden'] : 0;
            //$buyerPostCreate->is_delivery_flexible      = isset($request['delivery_flexible_hidden']) ? $request['delivery_flexible_hidden'] : 0;
            //$buyerPostCreate->total_cartons_weight=$tot;
            $buyerPostCreate->created_by      = Auth::id();
            $buyerPostCreate->created_ip      = $createdIp;
            $buyerPostCreate->created_at      = $created_at;
            $buyerPostCreate->save ();
            
            
            for($i = 0; $i < count($request['service_ids']); $i++){  
                
                if($request['service_ids'][$i]!=""){
                    $petBuyerSelSeller = new \App\Models\RelocationgmBuyerQuoteItems();   
                    $petBuyerSelSeller->lkp_service_id=RELOCATION_GLOBAL_MOBILITY;
                    $petBuyerSelSeller->buyer_post_id      = $buyerPostCreate->id;
                    $petBuyerSelSeller->lkp_gm_service_id      = $request['service_ids'][$i];
                    $petBuyerSelSeller->measurement      = $request['measurements'][$i];
                    $petBuyerSelSeller->measurement_units      = $request['measurement_units'][$i];
                    $petBuyerSelSeller->created_by      = Auth::id();
                    $petBuyerSelSeller->created_ip      = $createdIp;
                    $petBuyerSelSeller->created_at      = $created_at;
                    $petBuyerSelSeller->save (); 
                }
            }//exit; 
        $buyerLastPostId  = $buyerPostCreate->id;
   
                
    // if is private post
    if ($request['ptlQuoteaccessId'] == 2){
        
        if ($request['seller_list'] != '') {
            // Converting comma separated input into Array and then couting the seller list
            $seller_list = explode (",", $request['seller_list']);
            $seller_list_count = count($seller_list);
            // Initialise the Relocation Pet Selected Sellers
            for($i = 0; $i <$seller_list_count; $i++):      
                // Saving Records on relocationpet_buyer_selected_sellers    
                $petBuyerSelSeller = new \App\Models\RelocationgmBuyerSelectedSellers();
                $petBuyerSelSeller->lkp_service_id=RELOCATION_GLOBAL_MOBILITY;
                $petBuyerSelSeller->buyer_post_id      = $buyerLastPostId;
                $petBuyerSelSeller->seller_id      = $seller_list[$i];
                $petBuyerSelSeller->created_by      = Auth::id();
                $petBuyerSelSeller->created_ip      = $createdIp;
                $petBuyerSelSeller->created_at      = $created_at;
                $petBuyerSelSeller->save ();   


                $buyer_selected_buyers_email = DB::table('users')
                    ->where('id', $seller_list[$i])->get();
                $buyer_selected_buyers_email[0]->randnumber = $randnumber;
                $buyer_selected_buyers_email[0]->buyername = Auth::User()->username;
                CommonComponent::send_email(BUYER_CREATED_POST_FOR_SELLERS, $buyer_selected_buyers_email
                );
                
                //*******Send Sms to the private Sellers***********************//
                $getMobileNumber  =   CommonComponent::getMobleNumber($seller_list[$i]);
                if($getMobileNumber):
                    CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,[
                        'randnumber' => $randnumber,
                        'buyername' => Auth::User()->username,
                        'servicename' => RELOCATIONINT_BUYER_SMS_SERVICENAME
                    ]);
                endif;
                //*******Send Sms to the private Sellers***********************//
                
            endfor;
        }
    }   
    
        //below array for matching engine in Relocation pet start
        $matchedItems['from_date']=$_POST['dispatch_date'];
        $matchedItems['to_location_id']=$_POST ['to_location_id'];
        for($i = 0; $i < count($request['service_ids']); $i++){  
        $slab = DB::table('lkp_relocationgm_services as spi' );		
        
        $slab->Where ( 'id',$request['service_ids'][$i]);
        $slabInfo = $slab->first();
        if(isset($slabInfo->id)):
                $matchedItems[$slabInfo->service_type] = $slabInfo->id;
        
        endif;
        }
        	
    
    BuyerMatchingComponent::doMatching(RELOCATION_GLOBAL_MOBILITY,$buyerLastPostId,2,$matchedItems);
    
    return redirect('/relocation/creatbuyerrpost')->with('relocationtransactionNumber', $randnumber);
 }
 

 
 
        }
