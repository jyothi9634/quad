<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Components\CommonComponent;
use App\Components\Ptl\PtlSellerListingComponent;
use App\Components\Courier\CourierSellerListingComponent;
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



class PtlSellerListingController extends Controller
{   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function __construct() {
		$this->middleware ( 'auth' );		
	}
    
    public function index()
    {
        //
    }
    
    /**
     * Seller Posts List Page
     *
     * @param  object $request
     * @return Response
     */
    public function sellerLists() {
    	try{
    	
	    	if(Session::get ( 'service_id' ) != ''){
	    		$serviceId = Session::get ( 'service_id' );
	    	}
	    	
	    	// Saving the user activity to the log table
	    	if ($serviceId == ROAD_FTL) {
	    		CommonComponent::activityLog ( "FTL_SELLER_DETAIL",
	    		FTL_SELLER_DETAIL, 0,
	    		HTTP_REFERRER, CURRENT_URL );
	    	}elseif($serviceId == ROAD_PTL){
	    		CommonComponent::activityLog("PTL_SELLER_DETAIL",
	    		PTL_SELLER_DETAIL,0,
	    		HTTP_REFERRER,CURRENT_URL);
	    	}elseif($serviceId == ROAD_INTRACITY){
	    		CommonComponent::activityLog("INTRA_SELLER_DETAIL",
	    		INTRA_SELLER_DETAIL,0,
	    		HTTP_REFERRER,CURRENT_URL);
	    	}elseif($serviceId == ROAD_TRUCK_HAUL){
	    		CommonComponent::activityLog("THAUL_SELLER_DETAIL",
	    		THAUL_SELLER_DETAIL,0,
	    		HTTP_REFERRER,CURRENT_URL);
	    	}
	    		
	    	switch($serviceId){
	    		case ROAD_FTL       : PtlSellerListingComponent::PTLSellerList();
	    							  return view('ptl.sellers.seller_list');
	    							  break;
	    		case ROAD_PTL       : PtlSellerListingComponent::listPTLSellerPostItems($statusId, $roleId, $serviceId, $id);
	    		break;
	    		case ROAD_INTRACITY : PtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
	    		break;
	    		case RAIL       	: RailSellerListingComponent::listRailSellertopNavPostItems($statusId, $roleId, $serviceId, $id);
	    		break;
	    		case AIR_DOMESTIC   : AirDomesticSellerListingComponent::listAirdomSellerPostItems($statusId, $roleId, $serviceId, $id);
	    		break;
	    		case ROAD_TRUCK_HAUL: PtlSellerListingComponent::listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id);
	    		break;
	    		case COURIER       	: CourierSellerListingComponent::listCourierSellertopNavPostItems($statusId, $roleId, $serviceId, $id);
	    		break;
	    		default             : PtlSellerListingComponent::listFTLSellerPostDetails($statusId, $roleId, $serviceId, $id);
	    		break;
	    	}
	    	
    	}catch (Exception $e) {
    	
    	}
    	return Redirect::back();
    	
    }

}
