<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Zofe\Rapyd\Facades\DataGrid;
use Redirect;
use App\Components\CommonComponent;
use App\Components\BuyerComponent;

use Log;
use App\Components\Ftl\FtlBuyerComponent;
use App\Components\Ptl\PtlBuyerComponent;
use App\Components\Rail\RailBuyerComponent;
use App\Components\AirDomestic\AirDomesticBuyerComponent;
use App\Components\Intracity\IntracityBuyerComponent;
use App\Components\Term\TermBuyerComponent;
use App\Components\Relocation\RelocationBuyerComponent;
use App\Components\TruckHaul\TruckHaulBuyerComponent;
use App\Components\TruckLease\TruckLeaseBuyerComponent;
use App\Components\RelocationOffice\RelocationOfficeBuyerComponent;
use App\Components\RelocationPet\RelocationPetBuyerComponent;
use App\Components\RelocationInt\RelocationIntBuyerComponent;
use App\Components\RelocationInt\AirInt\RelocationAirBuyerComponent;
use App\Components\RelocationInt\OceanInt\RelocationOceanBuyerComponent;
use App\Components\RelocationGlobal\RelocationGlobalBuyerComponent;


class BuyerListingController extends Controller
{
    // Will store all views data in Data variable
    public $data = [];

    /**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
		//Session::put('post_type', '');
	}	
	
    /**
	 * Seller Search Page.
	 *	 
	 * @param  $request
	 * @return Response
	 */
	public function buyerPostsList(Request $request)
	{
	Log::info('get buyer posts list while buyer creating post:'.Auth::id(),array('c'=>'1'));		
		try{
			$enquiry_type = '';
			$roleId = Auth::User()->lkp_role_id;
			
			//Retrieval of post statuses
			$status = CommonComponent::getPostStatuses();

			//Retrieval of seller services
			$services = CommonComponent::getServices();

			//Retrieval of lead types
			$enquiry_types = CommonComponent::getEnquiryTypes();

			//Search Form logic
			
			$serviceId = '';
			$post_type = '';
			$lead_types=1;

			$rel_int_type = 1;	
			$post_status ='';

			if (!empty($_POST) ){
				
				if(isset($_POST['status_id'])){	
					$post_status = $_POST['status_id'];
					Session::put('status_search', $_POST['status_id']);
				}else{
					Session::put('status_search','');
                    $post_status='';
                }

                if(isset($_POST['post_type']) && $_POST['post_type'] != ''){
                	
                	$post_type = $_POST['post_type'];
                	Session::put('post_type', $_POST['post_type']);
                }else{
                	$post_type='';
                }
				if(isset($_POST['service_id']) && $_POST['service_id'] != ''){
					$serviceId= $_POST['service_id'];
					//Session::put('service_id', $_POST['service_id']);
				}
				if(isset($_POST['lkp_enquiry_type_id']) && $_POST['lkp_enquiry_type_id'] != ''){
					$enquiry_type = $_POST['lkp_enquiry_type_id'];
					Session::put('enquiry_type', $_POST['lkp_enquiry_type_id']);
				}
				if(Session::get ( 'service_id' ) == COURIER){

					if(isset($_POST['delivery_type']) && $_POST['delivery_type'] != ''){
						$delivery_type = $_POST['delivery_type'];
		            	Session::put('delivery_type', $_REQUEST['delivery_type']);
					}else{
						$delivery_type = '1';
		            	Session::put('delivery_type', '1');
					}
				}
				if(Session::get ( 'service_id' ) == RELOCATION_DOMESTIC || Session::get ( 'service_id' ) == RELOCATION_OFFICE_MOVE || Session::get ( 'service_id' ) == RELOCATION_PET_MOVE || Session::get ( 'service_id' ) == RELOCATION_INTERNATIONAL || Session::get ( 'service_id' ) == RELOCATION_GLOBAL_MOBILITY){
					if(isset($_POST['lead_types']) && $_POST['lead_types'] != ''){						
						Session::put('lead_types', $_POST['lead_types']);
						$lead_types=Session::get('lead_types');
						
					}
				}
				
				if(Session::get ( 'service_id' ) == RELOCATION_INTERNATIONAL){
					if(isset($_POST['international_types']) && $_POST['international_types'] != ''){						
						Session::put('international_types', $_POST['international_types']);
						$rel_int_type = Session::get('international_types');
						
					}
				}
				
	            }
			else if(!empty($_GET)){
				if(isset($_GET['page'])){
					
					$post_status = Session::get('status_search');
					if($post_status == ''){
						Session::put('status_search', 2);
						$post_status = 2;
					}

					$serviceId = Session::get('service_id');		
					$enquiry_type = Session::get('lkp_enquiry_type_id');
					if(Session::get ( 'service_id' ) == COURIER){
					$delivery_type = Session::get('delivery_type');
					}
					if(Session::get ('service_id' ) == RELOCATION_DOMESTIC || Session::get ( 'service_id' ) == RELOCATION_OFFICE_MOVE || Session::get ( 'service_id' ) == RELOCATION_PET_MOVE || Session::get ( 'service_id' ) == RELOCATION_INTERNATIONAL || Session::get ( 'service_id' ) == RELOCATION_GLOBAL_MOBILITY){
						$lead_types=Session::get('lead_types');
					}
					
					if(Session::get ( 'service_id' ) == RELOCATION_INTERNATIONAL){
							$rel_int_type = Session::get('international_types');
					}
				}
			    else{
					$enquiry_type = '';
					$post_status = Session::get('status_search');
					if($post_status == ''){
						Session::put('status_search', 2);
						$post_status = 2;
					}

					Session::put('enquiry_type','');
					if(Session::get ( 'service_id' ) == COURIER){
						$delivery_type = Session::get('delivery_type');
					}
					if(Session::get ( 'service_id' ) == RELOCATION_DOMESTIC || Session::get ( 'service_id' ) == RELOCATION_OFFICE_MOVE || Session::get ( 'service_id' ) == RELOCATION_PET_MOVE  || Session::get ( 'service_id' ) == RELOCATION_INTERNATIONAL || Session::get ( 'service_id' ) == RELOCATION_GLOBAL_MOBILITY){
						$lead_types=Session::get('lead_types');
					}
					if(Session::get ( 'service_id' ) == RELOCATION_INTERNATIONAL){
							$rel_int_type = Session::get('international_types');
					}

				}
			}else{
				$enquiry_type = '';
				$post_status = 2;
				Session::put('status_search','');
				Session::put('enquiry_type','');
				Session::put('international_types','');

				if(Session::get ( 'service_id' ) == RELOCATION_DOMESTIC || Session::get ( 'service_id' ) == RELOCATION_OFFICE_MOVE || Session::get ( 'service_id' ) == RELOCATION_PET_MOVE || Session::get ( 'service_id' ) == RELOCATION_INTERNATIONAL || Session::get ( 'service_id' ) == RELOCATION_GLOBAL_MOBILITY){
					Session::put('lead_types', 1);
					$lead_types=Session::get('lead_types');
				}
				if(Session::get ( 'service_id' ) == RELOCATION_INTERNATIONAL){
						Session::put('international_types', 1);
						$rel_int_type = Session::get('international_types');
				}

				$_REQUEST = array();
	            if(Session::get ( 'service_id' ) == COURIER){
	            Session::put('delivery_type', 1);
	            $_REQUEST['delivery_type'] = Session::get('delivery_type');
	            $delivery_type = Session::get('delivery_type');
	            }
			}

			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
			if(Session::get ( 'post_type' ) != ''){
				$post_type = Session::get ( 'post_type' );
			} 

			//Loading respective service data grid
			switch($serviceId){
				case ROAD_FTL       : 
				CommonComponent::activityLog("FTL_BUYER_LISTED_POSTS",FTL_BUYER_LISTED_POSTS,0,HTTP_REFERRER,CURRENT_URL);
									  if(isset($post_type) && $post_type=='term'){
									  $result = TermBuyerComponent::getTermBuyerPostlists($serviceId,$post_status,$enquiry_type);									  	
									  } elseif(isset($post_type) && $post_type=='marketleads') {
									  $result = FtlBuyerComponent::getFtlBuyerMarketLeadsList($serviceId,$post_status,$enquiry_type);
									  } else {	
				                      $result = FtlBuyerComponent::getFtlBuyerPostsList($serviceId,$post_status,$enquiry_type);
									  }
									  $grid = $result ['grid'];
									  $filter = $result ['filter'];
									  //rendering the view with the data grid									 
									  if($post_type=='marketleads') {
									  	return view ( 'ftl.buyers.buyers_market_leads', [
									  			'grid' => $grid,
									  			'filter' => $filter
									  	], array (
									  			'services' => $services,
									  			'enquiry_types' => $enquiry_types,
									  			'enquiry_type' => $enquiry_type,
									  			'service_id' => $serviceId,
									  			'post_status'=>$post_status,
									  			'status'=>$status));
									  	
									  } else {
									  	return view ( 'ftl.buyers.buyer_posts_list', [
									  			'grid' => $grid,
									  			'filter' => $filter
									  	], array (
									  			'services' => $services,
									  			'enquiry_types' => $enquiry_types,
									  			'enquiry_type' => $enquiry_type,
									  			'service_id' => $serviceId,
									  			'post_status'=>$post_status,
									  			'status'=>$status));									  	
									  }
										  
							          break;
				case ROAD_PTL       : 
				case RAIL       :
				case AIR_DOMESTIC       :
				case AIR_INTERNATIONAL       :
				case OCEAN       :
                                    CommonComponent::activityLog("PTL_BUYER_LISTED_POSTS",
											 PTL_BUYER_LISTED_POSTS,0,
											 HTTP_REFERRER,CURRENT_URL);
                                   
                                    if(isset($post_type) && $post_type=='term'){
                                    $result = TermBuyerComponent::getTermBuyerPostlists($serviceId,$post_status,$enquiry_type);
                                    } elseif (isset($post_type) && $post_type=='marketleads') {
									$result = PtlBuyerComponent::getPtlBuyerMarketLeadsList($serviceId,$post_status,$enquiry_type);
									}  else {
                                    $result = PtlBuyerComponent::getPtlBuyerPostsList($serviceId,$post_status,$enquiry_type);
                                    } 
				                    $grid = $result ['grid'];
				                    $filter = $result ['filter'];
				                    
				                    if($post_type=='marketleads') {
				                    return view ( 'ptl.buyers.ptl_buyer_market_leads', [
				                      		'grid' => $grid,
				                      		'filter' => $filter
				                      		], array (
				                      				'services' => $services,
				                      				'enquiry_types' => $enquiry_types,
				                      				'enquiry_type' => $enquiry_type,
				                      				'service_id' => $serviceId,
				                      				'post_status'=>$post_status,
				                      				'status'=>$status));
				                    } else {
				                    	return view ( 'ptl.buyers.buyer_post_lists', [
				                    			'grid' => $grid,
				                    			'filter' => $filter
				                    	], array (
				                    			'services' => $services,
				                    			'enquiry_types' => $enquiry_types,
				                    			'enquiry_type' => $enquiry_type,
				                    			'service_id' => $serviceId,
				                    			'post_status'=>$post_status,
				                    			'status'=>$status));
				                    	
				                    }
							          break;
			
			case COURIER       :
									CommonComponent::activityLog("PTL_BUYER_LISTED_POSTS",PTL_BUYER_LISTED_POSTS,0,HTTP_REFERRER,CURRENT_URL);
							          	//echo $delivery_type;//exit;
							          	
							          	if(isset($post_type) && $post_type=='term'){
							          		$result = TermBuyerComponent::getTermBuyerPostlists($serviceId,$post_status,$enquiry_type);
							          	} elseif (isset($post_type) && $post_type=='marketleads') {
											$result = PtlBuyerComponent::getPtlBuyerMarketLeadsList($serviceId,$post_status,$enquiry_type);
										}  else{
							          		$result = PtlBuyerComponent::getCourierBuyerPostsList($serviceId,$post_status,$enquiry_type,$delivery_type);
							          		
							          	}
							          	
							          	$grid = $result ['grid'];
							          	$filter = $result ['filter'];							          	
							          	//rendering the view with the data grid
							          	if($post_type=='marketleads') {
							          		return view ( 'ptl.buyers.ptl_buyer_market_leads', [
							          				'grid' => $grid,
							          				'filter' => $filter
							          		], array (
							          				'services' => $services,
							          				'enquiry_types' => $enquiry_types,
							          				'enquiry_type' => $enquiry_type,
							          				'service_id' => $serviceId,
							          				'post_status'=>$post_status,
							          				'status'=>$status));
							          	} else {
							          	return view ( 'ptl.buyers.buyer_post_lists', [
							          			'grid' => $grid,
							          			'filter' => $filter
							          			], array (
							          					'services' => $services,
							          					'enquiry_types' => $enquiry_types,
							          					'enquiry_type' => $enquiry_type,
							          					'service_id' => $serviceId,
							          					'post_status'=>$post_status,
							          					'domestic_or_international_selected'=>Session::get('delivery_type'),
							          					'status'=>$status));
							          	}
							          	break;
                                
				case ROAD_INTRACITY :
					 CommonComponent::activityLog("INTRA_BUYER_LISTED_POSTS",
											 INTRA_BUYER_LISTED_POSTS,0,
											 HTTP_REFERRER,CURRENT_URL);
					 
					 
					 $result = IntracityBuyerComponent::getIntracityBuyerPostLists($serviceId,$roleId,$post_status);
								
				                   $grid =  $result ['grid'] ;
		 							$filter = $result ['filter'] ;
				                      return view ( 'intracity.buyers.posts_list', array (
				                      		'service_types' => $services,
				                      		'grid' => $grid,
				                      		'filter'=> $filter,
                                                                'post_status'=>$post_status,
                                                                'status'=>$status
				                      ) );
				                      
				                      break;
				case RELOCATION_DOMESTIC: CommonComponent::activityLog("THAUL_BUYER_LISTED_POSTS",
											 THAUL_BUYER_LISTED_POSTS,0,
											 HTTP_REFERRER,CURRENT_URL); 
											 
				                             //echo $lead_types;
											if($lead_types==1)
											{
										    $result = RelocationBuyerComponent::getRelocationBuyerPostsList($serviceId,$post_status,$enquiry_type);
											}elseif($lead_types==3){
											$result = TermBuyerComponent::getTermBuyerPostlists($serviceId,$post_status,$enquiry_type);
											}else{
											$result = RelocationBuyerComponent::getRelocationBuyerLeadPostsList($serviceId,$post_status,$enquiry_type);
											}
										   $grid = $result ['grid'];
										   $filter = $result ['filter'];
										   //rendering the view with the data grid
										   if($lead_types==1 || $lead_types==3){
										   return view ( 'relocation.buyers.buyer_posts_list', [
										   		'grid' => $grid,
										   		'filter' => $filter
										   ], array (
										   		'services' => $services,
										   		'enquiry_types' => $enquiry_types,
										   		'enquiry_type' => $enquiry_type,
										   		'service_id' => $serviceId,
										   		'post_status'=>$post_status,
										   		'lead_types' => $lead_types,
										   		'status'=>$status));
										   }else{
										   	
										   	return view ( 'relocation.buyers.buyer_market_leads', [
										   			'grid' => $grid,
										   			'filter' => $filter
										   	], array (
										   			'services' => $services,
										   			'enquiry_types' => $enquiry_types,
										   			'enquiry_type' => $enquiry_type,
										   			'service_id' => $serviceId,
										   			'post_status'=>$post_status,
										   			'status'=>$status));
										   }
										   break;
										   
				case ROAD_TRUCK_HAUL: CommonComponent::activityLog("TRUCKHAUL_BUYER_LISTED_POSTS",
											 TRUCKHAUL_BUYER_LISTED_POSTS,0,
											 HTTP_REFERRER,CURRENT_URL);
									  if(isset($post_type) && $post_type=='marketleads') {
									  	$result = TruckHaulBuyerComponent::getTruckHaulBuyerMarketLeadsList($serviceId,$post_status,$enquiry_type);
									  } else {	
                                        $result = TruckHaulBuyerComponent::getTruckHaulBuyerPostsList($serviceId,$post_status,$enquiry_type);
									  }
									  $grid = $result ['grid'];
									  $filter = $result ['filter'];
									  //rendering the view with the data grid									 
									  if($post_type=='marketleads') {
									  	return view ( 'truckhaul.buyers.buyers_market_leads', [
									  			'grid' => $grid,
									  			'filter' => $filter
									  	], array (
									  			'services' => $services,
									  			'enquiry_types' => $enquiry_types,
									  			'enquiry_type' => $enquiry_type,
									  			'service_id' => $serviceId,
									  			'post_status'=>$post_status,
									  			'status'=>$status));
									  	
									  } else {
									  	return view ( 'truckhaul.buyers.buyer_posts_list', [
									  			'grid' => $grid,
									  			'filter' => $filter
									  	], array (
									  			'services' => $services,
									  			'enquiry_types' => $enquiry_types,
									  			'enquiry_type' => $enquiry_type,
									  			'service_id' => $serviceId,
									  			'post_status'=>$post_status,
									  			'status'=>$status));									  	
									  }
										  
							          break;
							          
                                case ROAD_TRUCK_LEASE: CommonComponent::activityLog("TRUCKHAUL_BUYER_LISTED_POSTS",
							          TRUCKHAUL_BUYER_LISTED_POSTS,0,
							          HTTP_REFERRER,CURRENT_URL);
							          if(isset($post_type) && $post_type=='marketleads') {
							          	$result = TruckLeaseBuyerComponent::getLeaseBuyerMarketLeadsList($serviceId,$post_status,$enquiry_type);
							          } else {
							          	$result = TruckLeaseBuyerComponent::getLeaseBuyerPostsList($serviceId,$post_status,$enquiry_type);
							          }
							          $grid = $result ['grid'];
							          $filter = $result ['filter'];
							          //rendering the view with the data grid
							          if($post_type=='marketleads') {
							          	return view ( 'trucklease.buyers.buyers_market_leads', [
							          			'grid' => $grid,
							          			'filter' => $filter
							          	], array (
							          			'services' => $services,
							          			'enquiry_types' => $enquiry_types,
							          			'enquiry_type' => $enquiry_type,
							          			'service_id' => $serviceId,
							          			'post_status'=>$post_status,
							          			'status'=>$status));
							          
							          } else {
							          	return view ( 'trucklease.buyers.buyer_posts_list', [
							          			'grid' => $grid,
							          			'filter' => $filter
							          	], array (
							          			'services' => $services,
							          			'enquiry_types' => $enquiry_types,
							          			'enquiry_type' => $enquiry_type,
							          			'service_id' => $serviceId,
							          			'post_status'=>$post_status,
							          			'status'=>$status));
							          }
							          
							          break;
                                case RELOCATION_OFFICE_MOVE: CommonComponent::activityLog("RELOCATION_OFFICE_MOVE_BUYER_LISTED_POSTS",
							          THAUL_BUYER_LISTED_POSTS,0,
							          HTTP_REFERRER,CURRENT_URL);
							          
							          //echo $lead_types;
							          //exit;
							          if($lead_types==1)
							          {
							          	$result = RelocationOfficeBuyerComponent::getRelocationBuyerPostsList($serviceId,$post_status,$enquiry_type);
							          }else{
							          	
							          	$result = RelocationOfficeBuyerComponent::getRelocationBuyerLeadPostsList($serviceId,$post_status,$enquiry_type);
							          }
							          $grid = $result ['grid'];
							          $filter = $result ['filter'];
							          //rendering the view with the data grid
							          if($lead_types==1){
							          	return view ( 'relocationoffice.buyers.buyer_posts_list', [
							          			'grid' => $grid,
							          			'filter' => $filter
							          	], array (
							          			'services' => $services,
							          			'enquiry_types' => $enquiry_types,
							          			'enquiry_type' => $enquiry_type,
							          			'service_id' => $serviceId,
							          			'post_status'=>$post_status,
							          			'lead_types' => $lead_types,
							          			'status'=>$status));
							          }else{
							          
							          	return view ( 'relocationoffice.buyers.buyer_market_leads', [
							          			'grid' => $grid,
							          			'filter' => $filter
							          	], array (
							          			'services' => $services,
							          			'enquiry_types' => $enquiry_types,
							          			'enquiry_type' => $enquiry_type,
							          			'service_id' => $serviceId,
							          			'post_status'=>$post_status,
							          			'status'=>$status));
							          }
							          break;
				
				// Pet move buyer post list
				case RELOCATION_PET_MOVE:               
	                                if($lead_types==1) {
	                                	return $this->_buyerPetmovePostList($post_status); 	
	                                } else {                                                                        
	                                    return $this->_buyerPetmoveMarketLeadsList($request); 	
	                                }
									break;

				case RELOCATION_INTERNATIONAL: CommonComponent::activityLog("RELOCATION_INTERNATIONAL_BUYER_LISTED_POSTS",
											 RELOCATION_INTERNATIONAL_BUYER_LISTED_POSTS,0,
											 HTTP_REFERRER,CURRENT_URL); 
											 			//Retrieval of lead types
											$rel_int_types = CommonComponent::getRelocationInternationalTypes();
				                           					
											if($lead_types==1)
											{
                                               $result = RelocationIntBuyerComponent::getRelocationBuyerPostsList($serviceId,$post_status,$enquiry_type,$rel_int_type);
											}elseif($lead_types==3){
											$result = TermBuyerComponent::getTermBuyerPostlists($serviceId,$rel_int_type);
											}else{
											$result = RelocationIntBuyerComponent::getRelocationIntBuyerLeadPostsList($serviceId,$post_status,$enquiry_type,$rel_int_type);
											}
										   $grid = $result ['grid'];
										   $filter = $result ['filter'];
								   
										   
										   //rendering the view with the data grid
										   if($lead_types==1 || $lead_types==3){
										   return view ( 'relocationint.buyers.buyer_posts_list', [
										   		'grid' => $grid,
										   		'filter' => $filter
										   ], array (
										   		'services' => $services,
										   		'enquiry_types' => $enquiry_types,
										   		'enquiry_type' => $enquiry_type,
										   		'service_id' => $serviceId,
										   		'post_status'=>$post_status,
										   		'lead_types' => $lead_types,
										   		'status'=>$status,
										   		'international_types' => $rel_int_types,
										   		'rel_int_type'=> $rel_int_type));
										   }else{
										   	
										   	return view ( 'relocationint.buyers.buyer_market_leads', [
										   			'grid' => $grid,
										   			'filter' => $filter
										   	], array (
										   			'services' => $services,
										   			'enquiry_types' => $enquiry_types,
										   			'enquiry_type' => $enquiry_type,
										   			'service_id' => $serviceId,
										   			'post_status'=>$post_status,
										   			'status'=>$status,
										   			'international_types' => $rel_int_types,
										   			'rel_int_type'=> $rel_int_type));
										   }
										   break;

				case RELOCATION_GLOBAL_MOBILITY: CommonComponent::activityLog("RELOCATION_GM_BUYER_LISTED_POSTS",RELOCATION_GM_BUYER_LISTED_POSTS,0,HTTP_REFERRER,CURRENT_URL); 
											 $lkp_relgm_services = array();
											 $lkp_relgm_services = CommonComponent::getLkpRelocationGMServices();
                                                                                       // echo $lead_types; //die;
											if($lead_types==1)
											{
                                                    $result = RelocationGlobalBuyerComponent::getRelocationGmBuyerPostsList($serviceId,$post_status,$enquiry_type);
											}elseif($lead_types==3){
                                                    $result = TermBuyerComponent::getTermBuyerPostlists($serviceId,$post_status,$enquiry_type);
											}else{
											$result = RelocationGlobalBuyerComponent::getRelocationGmBuyerLeadPostsList($serviceId,$post_status,$enquiry_type);
											}

										   $grid = $result ['grid'];
										   $filter = $result ['filter'];
										   
										   //rendering the view with the data grid
										   if($lead_types==1 || $lead_types==3){
										   return view ( 'relocationglobal.buyers.buyer_posts_list', [
										   		'grid' => $grid,
										   		'filter' => $filter
										   ], array (
										   		'services' => $services,
										   		'enquiry_types' => $enquiry_types,
										   		'enquiry_type' => $enquiry_type,
										   		'service_id' => $serviceId,
										   		'post_status'=>$post_status,
										   		'lead_types' => $lead_types,
										   		'status'=>$status,
										   		'lkp_relgm_services' => $lkp_relgm_services
										   		));
										   }else{
										   	
										   	return view ( 'relocationglobal.buyers.buyer_market_leads', [
										   			'grid' => $grid,
										   			'filter' => $filter
										   	], array (
										   			'services' => $services,
										   			'enquiry_types' => $enquiry_types,
										   			'enquiry_type' => $enquiry_type,
										   			'service_id' => $serviceId,
										   			'post_status'=>$post_status,
										   			'status'=>$status,
										   			'lkp_relgm_services' => $lkp_relgm_services));
										   }
										   break;
				default: 
				  	CommonComponent::activityLog("FTL_BUYER_LISTED_POSTS",
						 FTL_BUYER_LISTED_POSTS,0,
						 HTTP_REFERRER,CURRENT_URL);
                  	$grid = FtlBuyerComponent::getFtlBuyerPostsList($serviceId,$post_status,$enquiry_type);
		          	break;		   			  
			}
					
		} catch (Exception $e) {
		
		}		
	}
	
    /**
	* Buyer pet move post list
	* @author Shriram
	* @return Response
	*/
	private function _buyerPetmovePostList($post_status){

		// Pet move posts listing
		$result = RelocationPetBuyerComponent::getRelocationPetmoveList(
			RELOCATION_PET_MOVE, $post_status
		);
		        
        //Retrieval of post statuses
		$status = CommonComponent::getPostStatuses();
        
        //Retrieval of post service status from drop down
        if(isset($_POST['status_id']) && $_POST['status_id'] != ''){	
            $post_status = $_POST['status_id'];
            Session::put('status_search', $_POST['status_id']);
        }else{
            $post_status='';
        }		
			
		return view('relocationpet.buyers.buyer_posts_list', [
  			'grid' 	 => $result ['grid'],
  			'filter' => $result ['filter'],
            'post_status' => $post_status,
			'status' => $status
      	]);
    }

    /**
	* Buyer pet move post Market leads list
	* @author Srinivas and date : 12th May, 2016
	* @return Response
	*/
	private function _buyerPetmoveMarketLeadsList($request){
		// Pet move posts listing
		$result = RelocationPetBuyerComponent::getRelocationPetBuyerMarketLeadsList(
			RELOCATION_PET_MOVE, $request
		);	
                
        //Retrieval of post statuses
		$status = CommonComponent::getPostStatuses();
        
        //Retrieval of post service status from drop down
        if(isset($_POST['status_id']) && $_POST['status_id'] != ''){	
            $post_status = $_POST['status_id'];
            Session::put('status_search', $_POST['status_id']);
            }else{
            $post_status='';
        }	

		return view('relocationpet.buyers.buyer_market_leads', [
  			'grid' 	 => $result ['grid'],
  			'filter' => $result ['filter'],
                        'post_status'=>$post_status,
			'status'=>$status
      	]);
    }

	/**
	* Buyer Search Page for Seller posts.
	*	 
	* @param  $request
	* @return Response
	*/
	public function buyerSearch()
    {   
    	try
    	{
    		Log::info('Get buyer searching form page: '.Auth::id(),array('c'=>'1'));
    	
    		if(Session::get('service_id' ) != ''){
				$serviceId = Session::get('service_id');
			} 

			$vehicle_type 	= CommonComponent::getAllVehicleTypes();
    		$load_type 		= CommonComponent::getAllLoadTypes();
    		$lead_type 		= CommonComponent::getLeadTypes();
    		$quote_type 	= CommonComponent::getQuoteAccesses();
    		
    		//Ptl send perameters for buyer search form --srinu    		
    		$packageTypes = CommonComponent::getAllPackageTypes();
    		$volumeWeightTypes = CommonComponent::getVolumeWeightTypes();
    		$unitsWeightTypes = CommonComponent::getUnitsWeight();

    		$cities = CommonComponent::getIntracityCities();
    	    $rate_types = CommonComponent::getIntracityRateTypes();
    	    $weight_types = CommonComponent::getIntracityUOM();
    	    
    	    //Relocation search form funtions
    	    $roomTypes = CommonComponent::getAllRoomTypes();
    	    $loadTypes = CommonComponent::getAllLoadCategories();
    	    $propertyTypes = CommonComponent::getAllPropertyTypes();
    	    $vehicletypecategories = CommonComponent::getAllVehicleCategories();
    	    $vehicletypecategorietypes = CommonComponent::getAllVehicleCategoryTypes();

    	    //Courier search form functions
    	    $courierTypes = CommonComponent::getAllCourierTypes();
    	    $courierDeliveryTypes = CommonComponent::getAllCourierDeliveryTypes();

    		//Loading respective search form based on service
			switch($serviceId){

				case ROAD_FTL: 
					CommonComponent::activityLog("FTL_BUYER_SEARCHED_SELLER_POSTS", FTL_BUYER_SEARCHED_SELLER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return view('buyer.ftl.search.search_form', [
						'vehicle_type' 	=> CommonComponent::getAllVehicleTypes(),
						'load_type' 	=> CommonComponent::getAllLoadTypes(),
						'lead_type'		=> CommonComponent::getLeadTypes(),
						'quote_type'	=> CommonComponent::getQuoteAccesses()
					]);
					break;

				case ROAD_PTL:
                case RAIL:
                case AIR_DOMESTIC:
                    CommonComponent::activityLog("PTL_BUYER_SEARCHED_SELLER_POSTS",
    					PTL_BUYER_SEARCHED_SELLER_POSTS, 0, HTTP_REFERRER,CURRENT_URL
    				);
					return view('ptl.buyers.buyer_search', [
						'packageTypes' 	=> $packageTypes,
						'loadTypes' 	=> $load_type, 
						'volumeWeightTypes' => $volumeWeightTypes,
					  	'unitsWeightTypes' 	=> $unitsWeightTypes
					]);
			        break;                                
                                
                case AIR_INTERNATIONAL: 
                case OCEAN:    
                    CommonComponent::activityLog("PTL_BUYER_SEARCHED_SELLER_POSTS",
    					PTL_BUYER_SEARCHED_SELLER_POSTS,0,HTTP_REFERRER,CURRENT_URL);
                    $senderIdentity = CommonComponent::getSenderIdentity();
                    $shipmentTypes = CommonComponent::getShipmentTypes();
					return view('ptl.buyers.buyer_search',array('packageTypes' => $packageTypes,
					  	'loadTypes' => $load_type, 'volumeWeightTypes' => $volumeWeightTypes,
					  	'unitsWeightTypes' => $unitsWeightTypes,
					  	'senderIdentity' => $senderIdentity,
					  	'shipmentTypes' => $shipmentTypes
					  	));
					break;
                    
	            case COURIER	:    
	                CommonComponent::activityLog("PTL_BUYER_SEARCHED_SELLER_POSTS",
						PTL_BUYER_SEARCHED_SELLER_POSTS,0,HTTP_REFERRER,CURRENT_URL);
						$CourierTypes = CommonComponent::getAllCourierPorposeTypes();
					return view('ptl.buyers.buyer_search',array('courierTypes' => $courierTypes,
					  	'courierDeliveryTypes' => $courierDeliveryTypes,'CourierTypes' => $CourierTypes, 'volumeWeightTypes' => $volumeWeightTypes,
					  	'unitsWeightTypes' => $unitsWeightTypes ));
					break;

				case ROAD_INTRACITY : 
					CommonComponent::activityLog("INTRA_BUYER_SEARCHED_SELLER_POSTS", INTRA_BUYER_SEARCHED_SELLER_POSTS,0,HTTP_REFERRER,CURRENT_URL);
					return view('intracity.buyers.buyer_search', [
						'vehicle_types' => $vehicle_type, 'load_type' => $load_type,
						'rate_types' => $rate_types, 'cities' => $cities, 'weight_types' => $weight_types
						]);
					break;

				case ROAD_TRUCK_HAUL: 
					CommonComponent::activityLog("TRUCKHAUL_BUYER_SEARCHED_SELLER_POSTS", 
						TRUCKHAUL_BUYER_SEARCHED_SELLER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return view('buyer.truckhaul.search.search_form', [
						'vehicle_type' 	=> CommonComponent::getAllVehicleTypes(),
						'load_type' 	=> CommonComponent::getAllLoadTypes(),
						'lead_type'		=> CommonComponent::getLeadTypes(),
						'quote_type'	=> CommonComponent::getQuoteAccesses()
					]);
					break;

				case ROAD_TRUCK_LEASE: 
					CommonComponent::activityLog("TRUCKLEASE_BUYER_SEARCHED_SELLER_POSTS",
						TRUCKLEASE_BUYER_SEARCHED_SELLER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return view('buyer.trucklease.search.search_form', [
						'driver_availability' 	=> CommonComponent::getDriverAvailabilities(),
						'vehicle_type' 			=> CommonComponent::getAllVehicleTypes(),
						'getAllleaseTypes'		=> CommonComponent::getAllLeaseTypes()
					]);
					break;

				case RELOCATION_DOMESTIC: 
					CommonComponent::activityLog("RELOCATION_DOMESTIC_BUYER_SEARCHED_SELLER_POSTS", 
						RELOCATION_DOMESTIC_BUYER_SEARCHED_SELLER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					if(Session::has('masterBedRoom')){
						Session::forget('masterBedRoom');
					}
					if(Session::has('masterBedRoom1')){
						Session::forget('masterBedRoom1');
					}
					if(Session::has('masterBedRoom2')){
						Session::forget('masterBedRoom2');
					}
					if(Session::has('masterBedRoom3')){
						Session::forget('masterBedRoom3');
					}
					if(Session::has('lobby')){
						Session::forget('lobby');
					}
					if(Session::has('kitchen')){
						Session::forget('kitchen');
					}
					if(Session::has('bathroom')){
						Session::forget('bathroom');
					}
					if(Session::has('living')){
						Session::forget('living');
					}

					return view('buyer.relocation.home.domestic.search.search_form',[
						'load_types' 				=> CommonComponent::getAllLoadCategories(),
						'room_types'				=> CommonComponent::getAllRoomTypes(),
						'property_types' 			=> CommonComponent::getAllPropertyTypes(), 
						'vehicletypecategories' 	=> CommonComponent::getAllVehicleCategories(),
						'vehicletypecategorietypes' => CommonComponent::getAllVehicleCategoryTypes(),
					]);
					break;

                case RELOCATION_PET_MOVE: 
                	CommonComponent::activityLog("RELOCATIONPET_BUYER_SEARCHED_SELLER_POSTS",
                    RELOCATIONPET_BUYER_SEARCHED_SELLER_POSTS,0,HTTP_REFERRER,CURRENT_URL);
                	
                    return view('buyer.relocation.home.petmove.search.search_form',[
                    		'getAllPetTypes' => CommonComponent::getAllPetTypes(),
                            'getAllCageTypes' => CommonComponent::getAllCageTypes(),
                    		'getAllBreedTypes' => CommonComponent::getAllBreedTypesList()
                    ]);
                    break;
                                                       
				case RELOCATION_OFFICE_MOVE: CommonComponent::activityLog("RELOCATION_OFFICE_MOVE_BUYER_SEARCH_FORM_RESULTS",
				    					RELOCATION_OFFICE_MOVE_BUYER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL);
									  $particulars = CommonComponent::getOfficeParticulars();
									  return view('relocationoffice.buyers.buyer_search',array('particulars' => $particulars,'load_types' => $loadTypes,
									  	'room_types' =>$roomTypes,'property_types' => $propertyTypes, 'vehicletypecategories' => $vehicletypecategories,'vehicletypecategorietypes' => $vehicletypecategorietypes));
									  break;
				
				case RELOCATION_INTERNATIONAL:
					CommonComponent::activityLog("RELOCATION_INTERNATIONAL_BUYER_SEARCH_FORM_RESULTS",
				    	RELOCATION_INTERNATIONAL_BUYER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
				    );
					return view('buyer.relocation.home.international.search.search_form',[
						'property_types' => $propertyTypes,
						'room_types' => $roomTypes,
						'cartons' 	=> CommonComponent::getCartons(),
					]);
					break;
				case RELOCATION_GLOBAL_MOBILITY:
					CommonComponent::activityLog("RELOCATION_GM_BUYER_SEARCH_FORM_RESULTS",
				    	RELOCATION_GM_BUYER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
				    );

					if(Session::has('session_dispatch_date_buyer')){
						Session::forget('session_dispatch_date_buyer');
					}
					if(Session::has('session_to_city_id_buyer')){
						Session::forget('session_to_city_id_buyer');
					}
					if(Session::has('session_to_location_buyer')){
						Session::forget('session_to_location_buyer');
					}
					if(Session::has('session_service_type_relocation')){
						Session::forget('session_service_type_relocation');
					}
					if(Session::has('session_measurement_relocation')){
						Session::forget('session_measurement_relocation');
					}

				    $lkp_relgm_services = array();
        			$lkp_relgm_services = CommonComponent::getLkpRelocationGMServices();				    
					return view('relocationglobal.buyers.buyer_search',[
                   		 'lkp_relgm_services' => $lkp_relgm_services
                    ]);
					break;															  


				default: CommonComponent::activityLog("FTL_BUYER_SEARCHED_SELLER_POSTS",
				    					FTL_BUYER_SEARCHED_SELLER_POSTS,0,HTTP_REFERRER,CURRENT_URL);
									  return view('ftl.buyers.buyer_search',array('vehicle_type' => $vehicle_type,
									  	'load_type' => $load_type,'lead_type'=>$lead_type,'quote_type'=>$quote_type));
							          break;		   			  
			}   		
    		
    	}    	
    	catch (Exception $e) {
    	
    	}
    
    }

    /**
	* Displaying FTL Search results
	*	 
	* @param  $request
	* @return Response
	*/    
	private function _getFtlSearchResults($request)
    {
		$result = FtlBuyerComponent::getFtlBuyerSearchList($request, $this->data['serviceID']);
		$gridBuyer = $result['gridBuyer'];

		// Checking from_location is present on request or not
		
		if($request->exists('from_location')){

			$validTo = $validFrom = '';
			if($request->has('from_date'))
                $validFrom = CommonComponent::convertDateFlexiDisplay($request->from_date);

            if($request->has('to_date'))
                $validTo = CommonComponent::convertDateFlexiDisplay($request->to_date);

            // Checking Dispatch Flexible hidden set or not
            if($request->dispatch_flexible_hidden== 1):
                $fdispatch = BuyerComponent::getPreviousNextThreeDays($validFrom);
            else:
                $fdispatch = CommonComponent::checkAndGetDate($validFrom);
            endif;

            // Checking Delivery Flexible hidden set or not
            if( $request->delivery_flexible_hidden == 1 && $request->has('to_date') ):
                $fdelivery = BuyerComponent::getPreviousNextThreeDays($validTo);
            else:
                $fdelivery = CommonComponent::checkAndGetDate($validTo);
            endif;

            // Checking Quantity set & not empty
            if( $request->has('quantity'))
                $vehicle_type = CommonComponent::getQtyBasedAllVehicleTypes($request->quantity);
            
            // Checking Filters Set or not	
            if($request->exists('filter_set'))
				session()->put('show_layered_filter',1);

			return view('buyer.ftl.search.search_results', [
            	'gridBuyer' 	=> $gridBuyer,
            	'from_location' => $request['from_location'],
    		    'from_location_id' 	=> $request['from_location_id'],
    		    'to_location' 		=> $request['to_location'],
    			'to_location_id' 	=> $request['to_location_id'],
    		    'quantity' 	=> $request['quantity'],
    		    'capacity' 	=> $request['capacity'],
    		    'from_date' => $request['from_date'],
    		    'to_date'	=> $request['to_date'],
    		    'lkp_load_type_id' 		=> $request['lkp_load_type_id'],
    		    'lkp_vehicle_type_id' 	=> $request['lkp_vehicle_type_id'],
    		    'vehicle_type' 	 => $vehicle_type,
                'load_type' 	 => CommonComponent::getAllLoadTypes(),
                'lead_type'		 => CommonComponent::getLeadTypes(),
                'quote_type' 	 => CommonComponent::getQuoteAccesses(),
                'load_type_name' => CommonComponent::getLoadType($request->lkp_load_type_id),
                'vehicle_type_name' => CommonComponent::getVehicleType( 
                		$request->lkp_vehicle_type_id
                	),
                'fdispatch'	=> $fdispatch,
    		    'fdelivery'	=> $fdelivery,
                'is_commercial' =>  session('searchMod.is_commercial_date_buyer'),
            ]);
        
        } else {

            $validTo = $validFrom = "";
            if(session('searchMod.dispatch_date_buyer') !="")
                $validFrom = CommonComponent::convertDateFlexiDisplay( session('searchMod.dispatch_date_buyer') );
            
            if(session('searchMod.delivery_date_buyer') !="")
            	$validTo = CommonComponent::convertDateFlexiDisplay( session('searchMod.delivery_date_buyer') );

            if(session('searchMod.fdispatch_date_buyer')== 1):
                $fdispatch = BuyerComponent::getPreviousNextThreeDays($validFrom);
            else:
                $fdispatch = CommonComponent::checkAndGetDate($validFrom);
            endif;

            if(session('searchMod.fdelivery_date_buyer')== 1 && $validTo!=''):
                $fdelivery = BuyerComponent::getPreviousNextThreeDays($validTo);
            else:
                $fdelivery = CommonComponent::checkAndGetDate($validTo);
            endif;
                
			if(session('searchMod.quantity_buyer')!='')
                $vehicle_type = CommonComponent::getQtyBasedAllVehicleTypes( session(
                	'searchMod.quantity_buyer'
                ));

			if( session('filter_set') )
				Session::put('show_layered_filter',1);

            return view('buyer.ftl.search.search_results', [
            	'gridBuyer' 		=> $gridBuyer,
                'from_location'		=> session('searchMod.from_location_buyer'),
                'from_location_id'	=> session('searchMod.from_city_id_buyer'),
                'to_location'		=> session('searchMod.to_location_buyer'),
                'to_location_id'	=> session('searchMod.to_city_id_buyer'),
                'quantity'			=> session('searchMod.quantity_buyer'),
                'capacity'			=> session('searchMod.capacity_buyer'),
                'from_date'			=> session('searchMod.dispatch_date_buyer'),
                'to_date'			=> session('searchMod.delivery_date_buyer'),
                'lkp_load_type_id' 		=> session('searchMod.load_type_buyer'),
                'lkp_vehicle_type_id' 	=> session('searchMod.vehicle_type_buyer'),
                'vehicle_type' 		=> $vehicle_type,
                'load_type' 		=> CommonComponent::getAllLoadTypes(),
                'load_type_name'	=> CommonComponent::getLoadType(session(
                		'searchMod.load_type_buyer'
                	)),
                'vehicle_type_name'	=> CommonComponent::getVehicleType(session(
                		'searchMod.vehicle_type_buyer'
                	)),
                'fdispatch'	=> $fdispatch,
                'fdelivery'	=> $fdelivery,
                'is_commercial' => session('searchMod.is_commercial_date_buyer'),
            ]);
        } 
        
        
    }

    /**
	* Displaying Truck Hall Search results
	*	 
	* @param  $request
	* @return Response
	*/    
	private function _getTruckHaulSearchResults($request)
    {
    	$result = TruckHaulBuyerComponent::getTruckHaulBuyerSearchList($request, 
    		$this->data['serviceID']
    	);
    	$gridBuyer = $result['gridBuyer'];
    	
       	if($request->exists('from_location'))
       	{
            $validFrom = '';
			if($request->has('from_date'))
                $validFrom = CommonComponent::convertDateDisplay($request->from_date);

            if($request->dispatch_flexible_hidden == 1):
                $fdispatch = BuyerComponent::getPreviousNextThreeDays($validFrom);
           	else:
                $fdispatch = CommonComponent::checkAndGetDate($validFrom);
            endif;
           
            if($request->has('quantity'))
                $vehicle_type = CommonComponent::getQtyHualAllVehicleTypes($request->quantity);
            
            return view('buyer.truckhaul.search.search_results', [
            	'gridBuyer' 	=> $gridBuyer,
    		    'from_location'	=> $request->from_location,
    		    'from_location_id'	=> $request->from_location_id,
    		    'to_location'	=> $request->to_location,
    			'to_location_id'=> $request->to_location_id, 
    		    'quantity'		=> $request->quantity,
    		    'capacity'		=> $request->capacity, 
    		    'from_date'		=> $request->from_date,
    		    'lkp_load_type_id' 		=> $request->lkp_load_type_id,
    		    'lkp_vehicle_type_id' 	=> $request->lkp_vehicle_type_id,
    		    'vehicle_type' 		=> $vehicle_type,
                'load_type' 		=> CommonComponent::getAllLoadTypes(),
                'lead_type'			=> CommonComponent::getLeadTypes(),
                'quote_type'		=> CommonComponent::getQuoteAccesses(),
                'load_type_name'	=> CommonComponent::getLoadType($request->lkp_load_type_id),
                'vehicle_type_name'	=> CommonComponent::getVehicleType($request->lkp_vehicle_type_id),
                'fdispatch'	=> $fdispatch,
                'is_commercial' =>  $request['is_commercial'], 
            ]);

        } else {

            $validTo = $validFrom = "";
            if(session('searchMod.dispatch_date_buyer')!=""):
            	$validFrom = CommonComponent::convertDateDisplay( session('searchMod.dispatch_date_buyer'
            	));
            endif;

            if(session('searchMod.fdispatch_date_buyer')== 1):
                $fdispatch = BuyerComponent::getPreviousNextThreeDays($validFrom);
            else:
                $fdispatch = CommonComponent::checkAndGetDate($validFrom);
            endif;
                                                   
            return view('buyer.truckhaul.search.search_results', [
            	'gridBuyer' 	=> $gridBuyer,
                'from_location'		=> session('searchMod.from_location_buyer'),
                'from_location_id'	=> session('searchMod.from_city_id_buyer'),
                'to_location'		=> session('searchMod.to_location_buyer'),
                'to_location_id'	=> session('searchMod.to_city_id_buyer'),
                'quantity'			=> session('searchMod.quantity_buyer'),
                'capacity'			=> session('searchMod.capacity_buyer'),
                'from_date'			=> session('searchMod.dispatch_date_buyer'),
                'lkp_load_type_id' 	=> session('searchMod.load_type_buyer'),
                'lkp_vehicle_type_id' => session('searchMod.vehicle_type_buyer'),
                'vehicle_type' 		=> CommonComponent::getAllVehicleType(),
                'load_type' 		=> CommonComponent::getAllLoadTypes(),
                'load_type_name' 	=> CommonComponent::getLoadType( session(
                		'searchMod.load_type_buyer'
                	)),
                'vehicle_type_name'	=>CommonComponent::getVehicleType( session(
                		'searchMod.vehicle_type_buyer'
                	)),
                'fdispatch'=> $fdispatch,
                'is_commercial' 	=>  session('searchMod.is_commercial_date_buyer'),
            ]);
        } 
    }

    /**
	* Displaying Truck Hall Search results
	*	 
	* @param  $request
	* @return Response
	*/    
	private function _getTruckLeaseSearchResults($request)
    {
    	$result = TruckLeaseBuyerComponent::getBuyerSearchList($request, $this->data['serviceID']);
		
		if($request->exists('filter_set'))
			Session::put('show_layered_filter',1);
		
		return view('buyer.trucklease.search.search_results', [
			'gridBuyer' 		=> $result['gridBuyer'],
			'from_location'		=> $request->from_location,
			'from_location_id'	=> $request->from_location_id,
			'from_date'			=> $request->from_date,
			'to_date'			=> $request->to_date,
			'lkp_trucklease_lease_term_id' 	=> $request->lkp_trucklease_lease_term_id,
			'driver_availability_id' 		=> $request->driver_availability,
			
			'driver_availability_text' => ($request->exists('driver_availability') && ($request->driver_availability)) ? "With Driver" : "Without Driver",

			'lkp_vehicle_type_id' 	=> $request->lkp_vehicle_type_id,
            'is_commercial' 		=> $request->is_commercial,
			'vehicle_type' 			=> CommonComponent::getAllVehicleTypes(),
			'load_type' 			=> CommonComponent::getAllLoadTypes(),
			'lead_type'				=> CommonComponent::getLeadTypes(),
			'quote_type'			=> CommonComponent::getQuoteAccesses(),
			
			'lease_type_name'		=> CommonComponent::getAllLeaseName($request->lkp_trucklease_lease_term_id),
			
			'vehicle_type_name'		=> CommonComponent::getVehicleType($request->lkp_vehicle_type_id),
			'fdispatch'	=> CommonComponent::checkAndGetDate($request->from_date),
			'fdelivery'	=> CommonComponent::checkAndGetDate($request->to_date),
			'driver_availability' 	=> CommonComponent::getDriverAvailabilities(),
			'getAllleaseTypes'		=> CommonComponent::getAllLeaseTypes()
		]);
    }

    /**
	* Displaying Relocation Domestic Search results
	*	 
	* @param  $request
	* @return Response
	*/    
	private function _getRelocationDomesticSearchResults($request)
    {
		$result = RelocationBuyerComponent::getRelocationBuyerSearchResults($request, $this->data['serviceID']);
		
		if(isset($request['filter_set']))
			Session::put('show_layered_filter',1);

		return view('buyer.relocation.home.domestic.search.search_results', [
			'gridBuyer' => $result['gridBuyer'],
			'request'=>$request,
			'load_types' => $this->data['loadCategories'],
		    'room_types' =>$this->data['roomTypes'],
		    'property_types' => $this->data['propertyTypes'], 
		    'vehicletypecategories' => $this->data['vehicleTypecategories'],
			'vehicletypecategorietypes' => $this->data['vehicleCategoryTypes'],
			'from_location'=> $request->from_location,
			'from_location_id'=> $request->from_location_id,
			'to_location'=> $request->to_location,
			'to_location_id'=> $request->to_location_id,
			'property_type'=> $request->property_type,
			'volume'=> $request->volume,
			'post_rate_card_type'=> $request->post_rate_card_type,
			'load_type'=> $request->load_type,
			'from_date'=> $request->from_date,
			'to_date'=> $request->to_date,
			'household_items'=> $request->household_items,
			'vehicle_category'=> $request->vehicle_category,
			'vehicle_model'=> $request->vehicle_model,
			'vehicle_category_type'=> $request->vehicle_category_type,
		]);
    }
    
    
    /**
     * Displaying Relocation Petmove Search results
     *
     * @param  $request
     * @return Response
     */
    private function _getRelocationPetMoveSearchResults($request)
    {
    	//dd($request);exit;
    	$serviceId = session('service_id');
    	$result = RelocationPetBuyerComponent::getRelocationPetBuyerSearchResults($request, $serviceId);
    	$gridBuyer = $result['gridBuyer'];
    	if(isset($request['filter_set']))
    		Session::put('show_layered_filter',1);
    
    	return view('buyer.relocation.home.petmove.search.search_results', [
    			'gridBuyer' => $gridBuyer,
				'request'=>$request,							          		
				'from_location'=> $request->data['from_location'],
				'from_location_id'=> $request->data['from_location_id'],
				'to_location'=> $request->data['to_location'],
				'to_location_id'=> $request->data['to_location_id'],						          		
				'from_date'=> $request->data['from_date'],
				'to_date'=> $request->data['to_date'],							          		
							          		
			]);
    	
    }
    /**
     * Displaying Relocation International Search results
     *
     * @param  $request
     * @return Response
     */
    private function _getRelocationInternationalSearchResults($request)
    {
    	    
    	    $serviceId = session('service_id');
	    	// Checking post type Air or Ocean
	    	if($request->post_type==1):
	    	
	    	// Calculating Carton kgs
	    	$cartons = CommonComponent::getCartons();
	    	foreach($cartons as $carton):
	    	if(strtolower($carton->carton_type) == 'carton 1')
	    		$carton_1_weight = $carton->weight;
	    	else if(strtolower($carton->carton_type) == 'carton 2')
	    		$carton_2_weight = $carton->weight;
	    	else if(strtolower($carton->carton_type) == 'carton 3')
	    		$carton_3_weight = $carton->weight;
	    	endforeach;
	    	$c1 = ((int)request('cartons_1') * $carton_1_weight);
	    	$c2 = ((int)request('cartons_2') * $carton_2_weight);
	    	$c3 = ((int)request('cartons_3') * $carton_3_weight);
	    	$totalReqWeight = ($c1+$c2+$c3);
	    	
	    	$result = RelocationAirBuyerComponent::getRelocationIntAirBuyerSearchResults(
	    			$request, $serviceId, $totalReqWeight);
	    	$gridBuyer = $result['gridBuyer'];
	    	
	    	return view('buyer.relocation.home.international.search.air_search_results', [
	    			'gridBuyer' => $gridBuyer,
	    			'slabCheck' => $result['slab_status'],
	    			'request'=>$request,
	    			'cartons' 	=> $cartons,
	    			'totalReqWeight' => $totalReqWeight,
	    			'property_types' => $this->data['propertyTypes'],
	    			'room_types' => $this->data['roomTypes'],
	    			'from_date'=> $request->from_date,
	    			'to_date'=> $request->to_date,
	    	]);
	    	
	    	elseif(request('post_type')==2):
	    	
	    	$result = RelocationOceanBuyerComponent::getRelocationIntOceanBuyerSearchResults(
	    			$request, $serviceId
	    	);
	    	$gridBuyer = $result['gridBuyer'];
	    	
	    	return view('buyer.relocation.home.international.search.ocean_search_results', [
	    			'gridBuyer' => $gridBuyer,
	    			'request'	=> $request,
	    			'property_types' => $this->data['propertyTypes'],
	    			'room_types' => $this->data['roomTypes'],
	    			'from_date'=> $request->valid_from,
	    			'to_date'=> $request->valid_to,
	    	]);
	    	
	    	else:
	    	
	    	return "Invalid post type, please check once again.";
	    	
	    	endif;
    	
    }

	/**
	* Displaying Relocation Global Mobility Search results
	*	 
	* @param  $request
	* @return Response
	*/    
	private function _getRelocationGlobalMobilitySearchResults($request)
    {
		$result = RelocationGlobalBuyerComponent::getRelocationGmBuyerSearchResults($request, $this->data['serviceID']);
		$gridBuyer = $result['gridBuyer'];
		$lkp_relgm_services = array();
		$lkp_relgm_services = CommonComponent::getLkpRelocationGMServices(); 						          
		return view('buyer.relocation.home.global_mobility.search.search_results',[
			'gridBuyer' => $gridBuyer,
			'request'=>$request,							          		
			'to_location'=> $request->to_location,
			'to_location_id'=> $request->to_location_id,						          		
			'from_date'=> $request->from_date,
			'lkp_relgm_services'=> $lkp_relgm_services,							          		
		]);
    }    


	/**
	* Displaying Relocation Office Move Search results
	*	 
	* @param  $request
	* @return Response
	*/    
	private function _getRelocationOfficeMoveSearchResults($request)
    {
		// Get Volume CFT By Requested Inventory Details
		if(isset($request['roomitems'])){
			$request['volume'] = $_REQUEST['volume'] = RelocationOfficeBuyerComponent::getSearchInventoryCFT($request['roomitems']);
		}
		$result = RelocationOfficeBuyerComponent::getRelocationOfficeBuyerSearchResults($request, $this->data['serviceID']);
		$gridBuyer = $result['gridBuyer'];

		return view('relocationoffice.buyers.buyer_search_results', [
			'gridBuyer' 				=> $gridBuyer,
			//'filter'=>$filter,
			'request'					=> $request,
			'load_types' 				=> $this->data['loadCategories'],
		    'room_types' 				=> $this->data['roomTypes'],
		    'property_types' 			=> $this->data['propertyTypes'], 
		    'vehicletypecategories' 	=> $this->data['vehicleTypecategories'],
			'vehicletypecategorietypes' => $this->data['vehicleCategoryTypes'],
			'from_location'				=> $request['from_location'],
			'from_location_id'			=> $request['from_location_id'],
			'volume'					=> $request['volume'],
			'from_date'					=> $request['from_date'],
			'to_date'					=> $request['to_date'],
			'particulars' 				=> CommonComponent::getOfficeParticulars(),
		]);
    }

    /**
	* Buyer Search results for Seller posts.
	*	 
	* @param  $request
	* @return Response
	*/    
    public function buyerSearchResults(Request $request)
    {	
    	// Checking Service Selected or not
    	if( !session()->has('service_id') || empty(session('service_id')) ):
    		return redirect('home');
    	endif;
    	$this->data['serviceID'] = $serviceId = session('service_id');

		try
    	{
    		Session::put('show_layered_filter', 1);
    		Log::info('Get buyer search results page for sellers: '.Auth::id(),array('c'=>'1'));
    		
    		// User Role ID
    		$roleId = Auth::User()->lkp_role_id;
    		
    		// Lead types
    		$this->data['leadTypes'] = CommonComponent::getLeadTypes();
    		
    		// Quote types
    		$this->data['quoteTypes'] = CommonComponent::getQuoteAccesses();

    		// Cities Dropdown
    		$this->data['citiesList'] = $cities = CommonComponent::getIntracityCities();

    		// Rate Types
    		$this->data['rateTypes'] = $rate_types = CommonComponent::getIntracityRateTypes();

    		// Weight Types
    	    $this->data['weightTypes'] = $weight_types = CommonComponent::getIntracityUOM();
    	    
    	    // Room Types
    	    $this->data['roomTypes'] = $roomTypes = CommonComponent::getAllRoomTypes();

    	    // Load Categories
    	    $this->data['loadCategories'] = $loadTypes = CommonComponent::getAllLoadCategories();

    	    // Load Property types
    	    $this->data['propertyTypes'] = $propertyTypes = CommonComponent::getAllPropertyTypes();

    	    // Load Vehicel Categories
    	    $this->data['vehicleTypecategories'] = $vehicletypecategories = CommonComponent::getAllVehicleCategories();

    	    // Load Vehicle Category types
    	    $this->data['vehicleCategoryTypes'] = $vehicletypecategorietypes = CommonComponent::getAllVehicleCategoryTypes();

    	   	//Loading respective search form grid and filters based on service
    	   	switch($serviceId){

				case ROAD_FTL:
					CommonComponent::activityLog("FTL_BUYER_SEARCH_FORM_RESULTS", FTL_BUYER_SEARCH_FORM_RESULTS, 0, HTTP_REFERRER,CURRENT_URL
					);

		    		// Load Search based FTL results
					return $this->_getFtlSearchResults($request);
					break;
				
				case ROAD_PTL:
				case RAIL:
				case AIR_DOMESTIC:
				case AIR_INTERNATIONAL:
				case OCEAN:
				case COURIER:
					CommonComponent::activityLog("BUYER_SEARCH_FORM_RESULTS",
    					BUYER_SEARCH_FORM_RESULTS,0, HTTP_REFERRER,CURRENT_URL
    				);

		            /*** Code Started  28042016  by Jagadeesh 
		            Change the code to fix search Getting issue ***/
	                $_REQUEST['ptlDispatchDate'] = explode('|',$request['sea_ptlDispatchDate']);
	                $_REQUEST['ptlDeliveryhDate'] = explode('|',$request['sea_ptlDeliveryhDate']);
	                $_REQUEST['ptlFromLocation'] = explode('|',$request['sea_ptlFromLocation']);
	                $_REQUEST['ptlToLocation'] = explode('|',$request['sea_ptlToLocation']);
	                $_REQUEST['fromlocationName'] = explode('|',$request['sea_fromlocationName']);
	                $_REQUEST['tolocationName'] = explode('|',$request['sea_tolocationName']);
	                $_REQUEST['ptlLoadType'] = explode('|',$request['sea_ptlLoadType']);
	                $_REQUEST['ptlPackageType'] = explode('|',$request['sea_ptlPackageType']);
	                $_REQUEST['ptlLength'] = explode('|',$request['sea_ptlLength']);
	                $_REQUEST['ptlWidth'] = explode('|',$request['sea_ptlWidth']);
	                $_REQUEST['ptlHeight'] = explode('|',$request['sea_ptlHeight']);
	                $_REQUEST['ptlCheckVolWeight'] = explode('|',$request['sea_ptlCheckVolWeight']);
	                $_REQUEST['ptlDisplayVolumeWeight'] = explode('|',$request['sea_ptlDisplayVolumeWeight']);
	                $_REQUEST['ptlUnitsWeight'] = explode('|',$request['sea_ptlUnitsWeight']);
	                $_REQUEST['ptlFlexiableDispatch'] = explode('|',$request['sea_ptlFlexiableDispatch']);
	                $_REQUEST['ptlDoorpickup'] = explode('|',$request['sea_ptlDoorpickup']);
	                $_REQUEST['ptlFlexiableDelivery'] = explode('|',$request['sea_ptlFlexiableDelivery']);
	                $_REQUEST['ptlDoorDelivery'] = explode('|',$request['sea_ptlDoorDelivery']);
	                $_REQUEST['ptlCheckUnitWeight'] = explode('|',$request['sea_ptlCheckUnitWeight']);
	                $_REQUEST['ptlNopackages'] = explode('|',$request['sea_ptlNopackages']);
	                $_REQUEST['ptlLoadTypeName'] = explode('|',$request['sea_ptlLoadTypeName']);
	                $_REQUEST['ptlShipmentType'] = explode('|',$request['sea_ptlShipmentType']);
	                $_REQUEST['ptlIECode'] = explode('|',$request['sea_ptlIECode']);
	                $_REQUEST['ptlSenderIdentity'] = explode('|',$request['sea_ptlSenderIdentity']);
	                $_REQUEST['ptlProductMade'] = explode('|',$request['sea_ptlProductMade']);
                
					if($serviceId==COURIER){
		                $_REQUEST['ptlLengthCourier'] = explode('|',$request['sea_ptlLengthCourier']);
		                $_REQUEST['ptlWidthCourier'] = explode('|',$request['sea_ptlWidthCourier']);
		                $_REQUEST['ptlHeightCourier'] = explode('|',$request['sea_ptlHeightCourier']);
		                $_REQUEST['ptlCheckVolWeightCourier'] = explode('|',$request['sea_ptlCheckVolWeightCourier']);
		                $_REQUEST['ptlPurposesType'] = explode('|',$request['sea_ptlPurposesType']);
		                $_REQUEST['ptlPackageType'] = explode('|',$request['sea_ptlPackageType']);
		                $_REQUEST['ptlLoadType'] = explode('|',$request['sea_ptlLoadType']);
		                $_REQUEST['packeagevalue'] = explode('|',$request['sea_packeagevalue']);
		                $_REQUEST['post_delivery_types'] = explode('|',$request['sea_post_delivery_types']);
		                $_REQUEST['courier_types'] = explode('|',$request['sea_courier_types']);
					}    
                
            		/*** Code Ended   28042016  by Jagadeesh ***/

					if(!isset($_REQUEST['filter_set'])){
					  	$result = PtlBuyerComponent::getPtlBuyerSearchList($_REQUEST, $serviceId);
                    }else{
						$result = PtlBuyerComponent::getPtlBuyerSearchList(Session::get('request'), $serviceId);
					}

					$gridBuyer = $result;
					$filter = $result['gridFilter'];
					$request=array();
					if(!isset($_REQUEST['filter_set'])){
						Session::put('request', $_REQUEST);
					}
					if(isset($_REQUEST['filter_set'])){
						Session::put('show_layered_filter',1);
					}
					return view('ptl.buyers.buyer_search_results', ['gridBuyer' => $gridBuyer,'gridFilter'=>$filter]); 
		          	break;

		        case ROAD_INTRACITY : 
					CommonComponent::activityLog("INTRA_BUYER_SEARCH_FORM_RESULTS",
				    	INTRA_BUYER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
				    );

					//moved because except intra remaining services getting errors
					if(!empty($_REQUEST))
						CommonComponent::saveSearchTerms($_REQUEST, $serviceId) ;
					
					//moved because except intra remaining services getting errors
                    $statusId = '';                    
                    $result = IntracityBuyerComponent::getIntraBuyerSearchList($roleId, $serviceId,
                    	$statusId,$_REQUEST
                    );

                    $gridBuyer = $result['gridBuyer'];
                    $filter = $result['filter'];
                    
                    //print_r($_REQUEST);exit;
                    if(!isset($_REQUEST['lkp_city_id'])){
                        $_REQUEST['lkp_city_id'] = Session::get('buyerSessionFromcityId');
                    }

                    return view('intracity.buyers.buyer_search_results', [
                    	'gridBuyer' => $gridBuyer,
                    	'filter' => $filter,
                    	'weight_types'=>CommonComponent::getIntracityUOM(),
                    	'city'=>CommonComponent::getCityName($_REQUEST['lkp_city_id']),
                    	'vehicle_type' => CommonComponent::getAllVehicleTypes(),
                    	'load_type' => CommonComponent::getAllLoadTypes(),
                    	'rate_types'=> CommonComponent::getIntracityRateTypes(),
                    	'cities'=>CommonComponent::getIntracityCities()
                    ]); 
                    break;

                case ROAD_TRUCK_HAUL:  
                	CommonComponent::activityLog("TRUCKHAUL_BUYER_SEARCH_FORM_RESULTS",TRUCKHAUL_BUYER_SEARCH_FORM_RESULTS,0, HTTP_REFERRER,CURRENT_URL
                	);
                    return $this->_getTruckHaulSearchResults($request); 
					break;

				case ROAD_TRUCK_LEASE:  
					CommonComponent::activityLog("TRUCKLEASE_BUYER_SEARCH_FORM_RESULTS",
						TRUCKLEASE_BUYER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return $this->_getTruckLeaseSearchResults($request);
					break;

				case RELOCATION_DOMESTIC: 
					CommonComponent::activityLog("RELOCATION_DOMESTIC_BUYER_SEARCH_FORM_RESULTS",
						RELOCATION_DOMESTIC_BUYER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL);
					return $this->_getRelocationDomesticSearchResults($request);
					break;
                case RELOCATION_PET_MOVE:	
                	CommonComponent::activityLog("RELOCATION_PET_BUYER_SEARCH_FORM_RESULTS",
						RELOCATION_PET_BUYER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL);
                	return $this->_getRelocationPetMoveSearchResults($request);
                    break;
				case RELOCATION_OFFICE_MOVE: 
					CommonComponent::activityLog("RELOCATION_OFFICE_MOVE_BUYER_SEARCH_FORM_RESULTS",
						RELOCATION_OFFICE_MOVE_BUYER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL);
                	return $this->_getRelocationOfficeMoveSearchResults($request);
					break;

				//@Shriram
				case RELOCATION_INTERNATIONAL:
					CommonComponent::activityLog("RELOCATION_INTERNATIONAL_BUYER_SEARCH_FORM_RESULTS",
				    	RELOCATION_INTERNATIONAL_BUYER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
				    );
					
					return $this->_getRelocationInternationalSearchResults($request);
					break;
	            case RELOCATION_GLOBAL_MOBILITY:	CommonComponent::activityLog("RELOCATION_PET_BUYER_SEARCH_FORM_RESULTS",
			          RELOCATION_PET_BUYER_SEARCH_FORM_RESULTS,0,
			          HTTP_REFERRER,CURRENT_URL);
					return $this->_getRelocationGlobalMobilitySearchResults($request);				          
			        break;

				default: CommonComponent::activityLog("FTL_BUYER_SEARCH_FORM_RESULTS",
				    					FTL_BUYER_SEARCH_FORM_RESULTS,0,
				    					HTTP_REFERRER,CURRENT_URL);
									  $result = FtlBuyerComponent::getFtlBuyerSearchList($roleId, $serviceId);
							          $gridBuyer = $result['gridBuyer'];
							          $filter = $result['filter'];
							    	  return view('ftl.buyers.buyer_search_results', ['gridBuyer' => $gridBuyer,'filter'=>$filter]); 
							          break;
			} 			
    		
    	}
    	catch (Exception $e) {
    		 
    	}
    
    }
}
