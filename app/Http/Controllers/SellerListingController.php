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
use App\Components\SellerComponent;
use App\Models\SellerPost;
use App\Models\SellerPostItem;
use App\Models\PtlSellerPost;
use App\Models\PtlSellerPostItem;
use App\Models\RailSellerPost;
use App\Models\RailSellerPostItem;
use App\Models\AirdomSellerPost;
use App\Models\AirdomSellerPostItem;
use App\Models\AirintSellerPost;
use App\Models\AirintSellerPostItem;
use App\Models\OceanSellerPost;
use App\Models\OceanSellerPostItem;
use App\Models\CourierSellerPost;
use App\Models\CourierSellerPostItem;
//Truck Haul Model
use App\Models\TruckhaulSellerPost;
use App\Models\TruckhaulSellerPostItem;
use App\Models\TruckleaseSellerPost;
use App\Models\TruckleaseSellerPostItem;
use App\Models\RelocationpetSellerPost;
use App\Models\RelocationpetSellerPostItem;


use Log;
use App\Components\Ftl\FtlSellerListingComponent;
use App\Components\Ptl\PtlSellerListingComponent;
use App\Components\Rail\RailSellerListingComponent;
use App\Components\Rail\RailSellerComponent;
use App\Components\AirDomestic\AirDomesticSellerListingComponent;
use App\Components\AirInternational\AirInternationalSellerListingComponent;
use App\Components\Occean\OcceanSellerListingComponent;
use App\Components\Courier\CourierSellerListingComponent;
use App\Components\Courier\CourierSellerComponent;
use App\Components\Relocation\RelocationSellerComponent;
use App\Components\RelocationPet\RelocationPetSellerComponent;
use App\Components\TruckHaul\TruckHaulSellerListingComponent;
use App\Components\TruckHaul\TruckHaulBuyerComponent;
use App\Components\TruckLease\TruckLeaseBuyerComponent;
use App\Components\RelocationOffice\RelocationOfficeSellerComponent;


use App\Components\Ftl\FtlSellerComponent;
use App\Components\Ftl\FtlBuyerListingComponent;
use App\Components\Ptl\PtlSellerComponent;
use App\Components\AirDomestic\AirDomesticSellerComponent;
use App\Components\AirInternational\AirInternationalSellerComponent;
use App\Components\Occean\OceanSellerComponent;
use App\Components\Matching\SellerMatchingComponent;
use App\Components\Term\TermSellerComponent;
use App\Components\TruckHaul\TruckHaulSellerComponent;
use App\Components\TruckLease\TruckLeaseSellerComponent;
use App\Components\RelocationInt\AirInt\RelocationAirSellerComponent;
use App\Components\RelocationInt\OceanInt\RelocationOceanSellerComponent;

use App\Components\RelocationGlobal\RelocationGlobalSellerComponent;


// Relocation International commponent added
use App\Components\RelocationInt\RelocationIntSellerComponent;

use ZipArchive;

class SellerListingController extends Controller {
	
	// Will store all views data in Data variable
    public $data = [];

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware ( 'auth' );	

	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//
	}
	
		
	/**
	 * Seller Posts List Page
	 *
	 * @param  object $request
	 * @return Response
	 */
	public function sellerLists() {
		Log::info('get seller posts list while seller creating post:'.Auth::id(),array('c'=>'1'));		
		try{
			$roleId = Auth::User()->lkp_role_id;
			
			//Retrieval of post statuses
			$posts_status = CommonComponent::getPostStatuses();

			//Retrieval of seller services
			$lkp_services_seller = CommonComponent::getServices();

			//Retrieval of lead types
			$lkp_lead_types = CommonComponent::getLeadTypes();
			
			//Search Form logic
			$serviceId = '';
			$type = '';
			$post_type='';
			$seller_posts_type='';
			Session::put('type', '');
			
			
			if ( !empty($_REQUEST) ){
				
				/*Check the new condtions for sessions to request variables start
				* @Srinivas Dantha
				* Date : july 15th,2016
				*/

				if(isset($_REQUEST['status']) && $_REQUEST['status'] != ''){
					$statusId = $_REQUEST['status'];					
				}else{
					$statusId = OPEN;
				}

				/*
				* End sessions to request variables for status
				*/
				
				if(isset($_REQUEST['service']) && $_REQUEST['service'] != ''){
					$serviceId = $_REQUEST['service'];
				}
				
				if(isset($_REQUEST['type']) && $_REQUEST['type'] != ''){
					$type = $_REQUEST['type'];
					Session::put('type', $_REQUEST['type']);
				}
                
                if(isset($_REQUEST['post_type']) && $_REQUEST['post_type'] != ''){
					$post_type = $_REQUEST['post_type'];
					Session::put('post_type', $_REQUEST['post_type']);
				}
				
				if(isset($_REQUEST['destinationtype']) && $_REQUEST['destinationtype'] != ''){
					$destinationtype = $_REQUEST['destinationtype'];
					Session::put('destinationtype', $_REQUEST['destinationtype']);
				}else{
					$destinationtype = Session::get('destinationtype');
				}
				
				if(isset($_GET['page'])){
					$statusId  = $_REQUEST['status'];
					$serviceId = Session::get('service_id');
				}
				
				if(isset($_REQUEST['posts_type']) && $_REQUEST['posts_type'] != ''){
					Session::put('seller_posts_type_select', $_REQUEST['posts_type']);
				}

			}else{
				$serviceId = '';				
				$statusId = OPEN;

				Session::put('destinationtype',1);
				$destinationtype = Session::get('destinationtype');
			}

			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}	

			if(Session::get ( 'seller_posts_type_select' ) != ''){
				$seller_posts_type = Session::get ( 'seller_posts_type_select' );
			}
			//echo "<pre>"; print_r($_REQUEST); die;

			switch($serviceId){
				
				case ROAD_FTL: 
					CommonComponent::activityLog("SELLER_LISTED_POSTS", SELLER_LISTED_POSTS, 0,HTTP_REFERRER,CURRENT_URL );
					if($type==2){	
						if($post_type==1):
							$grid = TermSellerComponent::getTermSellerPostlists($serviceId); 							
						else:
							$grid = FtlBuyerListingComponent::listFTLBuyerPrivatePosts($statusId, $serviceId, $roleId,$type);  
						endif;
					}else{
						$grid = FtlSellerListingComponent::listFTLSellerPosts($statusId, $serviceId, $roleId,$type);
					}									  
					return view('ftl.sellers.seller_list',$grid, [
							'statusSelected' => $statusId,
							'typeSelected' => $type,
							'posttypeSelected' => $post_type,
							'posts_status_list'=> $posts_status, 
							'services_seller'=>$lkp_services_seller,
							'lead_types_seller'=>$lkp_lead_types
					]);	
					break;

				case ROAD_PTL:
					CommonComponent::activityLog("PTL_SELLER_LISTED_POSTS", PTL_SELLER_LISTED_POSTS,0,HTTP_REFERRER,CURRENT_URL);					
					if($type==2){
                        if($post_type==1):
							$grid = TermSellerComponent::getTermSellerPostlists($serviceId);
                        else:
                            $grid = PtlSellerListingComponent::listLTLBuyerPrivatePosts($statusId, $serviceId, $roleId,$type);  
                        endif;
					}else{
						$grid =  PtlSellerListingComponent::PTLSellerList($statusId, $roleId, $serviceId);
					}
				    return view('ptl.sellers.seller_list',$grid, [
					    	'statusSelected' => $statusId,
							'typeSelected' => $type,
							'posttypeSelected' => $post_type,
							'servicetype'=>'LTL',
							'posts_status_list'=>$posts_status,
							'services_seller'=>$lkp_services_seller,
							'lead_types_seller'=>$lkp_lead_types
					]);
					break;

				case RAIL       	: 
					CommonComponent::activityLog("RAIL_SELLER_LISTED_POSTS", RAIL_SELLER_LISTED_POSTS,0, HTTP_REFERRER,CURRENT_URL);
						if($type==2){
	                        if($post_type==1){
								$grid = TermSellerComponent::getTermSellerPostlists($serviceId, $statusId);
	                        }else{
	                            $grid = RailSellerListingComponent::listRAILBuyerPrivatePosts($statusId, $serviceId, $roleId,$type);  
	                        }                                                                
	                    }else{
								$grid =  RailSellerListingComponent::RailSellerList($statusId, $roleId, $serviceId);
						}							          
			          return view('ptl.sellers.seller_list',$grid, [
							'statusSelected' => $statusId,
							'typeSelected' => $type,
	                        'posttypeSelected' => $post_type,
							'servicetype'=>'RAIL',
							'posts_status_list'=>$posts_status, 
							'services_seller'=>$lkp_services_seller,
							'lead_types_seller'=>$lkp_lead_types
						]);
			          break;

				case AIR_DOMESTIC   : 
					CommonComponent::activityLog("AIRDOM_SELLER_LISTED_POSTS", AIRDOM_SELLER_LISTED_POSTS,0,HTTP_REFERRER,CURRENT_URL);
   					  if($type==2){
                        if($post_type==1){
							$grid = TermSellerComponent::getTermSellerPostlists($serviceId, $statusId);
                          }else{
                            $grid = AirDomesticSellerListingComponent::listAIRDOMBuyerPrivatePosts($statusId, $serviceId, $roleId,$type);  
                          }
					  }else{
						$grid =  AirDomesticSellerListingComponent::AirDomesticSellerList($statusId, $roleId, $serviceId);
			     	  }
			          return view('ptl.sellers.seller_list',$grid, [
							'statusSelected' => $statusId,
							'typeSelected' => $type,
	                        'posttypeSelected' => $post_type,
							'servicetype'=>'AIR DOMESTIC',
							'posts_status_list'=>$posts_status, 
							'services_seller'=>$lkp_services_seller,
							'lead_types_seller'=>$lkp_lead_types
						 ]);
			          break;

				case AIR_INTERNATIONAL: 
					CommonComponent::activityLog("AIRINT_SELLER_LISTED_POSTS", AIRINT_SELLER_LISTED_POSTS,0, HTTP_REFERRER,CURRENT_URL);
			            if($type==2){
                           if($post_type==1){
						$grid = TermSellerComponent::getTermSellerPostlists($serviceId, $statusId);
                          }else{
                            $grid = AirInternationalSellerListingComponent::listAIRINTBuyerPrivatePosts($statusId, $serviceId, $roleId,$type);  
                          }
						}else{
				          	$grid =  AirInternationalSellerListingComponent::AirInternationalSellerList($statusId, $roleId, $serviceId);
				      	}
			          return view('ptl.sellers.seller_list',$grid, [
							'statusSelected' => $statusId,
							'typeSelected' => $type,
	                        'posttypeSelected' => $post_type,
							'servicetype'=>'AIR INTERNATIONAL',
							'posts_status_list'=>$posts_status,
							'services_seller'=>$lkp_services_seller,
							'lead_types_seller'=>$lkp_lead_types
						 ]);
			            break;

				case OCEAN   		: 
					CommonComponent::activityLog("OCCEAN_SELLER_LISTED_POSTS", OCCEAN_SELLER_LISTED_POSTS,0, HTTP_REFERRER,CURRENT_URL);
			            if($type==2){
                            if($post_type==1){
						$grid = TermSellerComponent::getTermSellerPostlists($serviceId, $statusId);
                           } else{
                               $grid = OcceanSellerListingComponent::listOCEANBuyerPrivatePosts($statusId, $serviceId, $roleId,$type);  
                          }                                                                
                        }else{
					  		   $grid =  OcceanSellerListingComponent::OcceanSellerList($statusId, $roleId, $serviceId);
					  	}
			            return view('ptl.sellers.seller_list',$grid, [
							'statusSelected' => $statusId,
							'typeSelected' => $type,
	                        'posttypeSelected' => $post_type,
							'servicetype'=>'OCEAN',
							'posts_status_list'=>$posts_status,
							'services_seller'=>$lkp_services_seller,
							'lead_types_seller'=>$lkp_lead_types
						 ]);
			            break;

				case ROAD_TRUCK_HAUL       : 
					CommonComponent::activityLog("TRUCKHAUL_SELLER_LISTED_POSTS",TRUCKHAUL_SELLER_LISTED_POSTS,0,HTTP_REFERRER,CURRENT_URL);
						if($type==2){	
                             $grid = TruckHaulBuyerComponent::listTHBuyerPrivatePosts($statusId, $serviceId, $roleId,$type);  
                        }else{
                            $grid = TruckHaulSellerListingComponent::listTruckHaulSellerPosts($statusId, $serviceId, $roleId,$type);
                        }						  
					  return view('truckhaul.sellers.seller_list',$grid, [
							'statusSelected' => $statusId,
					  		'typeSelected' => $type,                                                               
							'posts_status_list'=>$posts_status, 
                            'services_seller'=>$lkp_services_seller,
							'lead_types_seller'=>$lkp_lead_types
						]);	
			          break;	

				case COURIER       	: 
					CommonComponent::activityLog("COURIER_SELLER_LISTED_POSTS", COURIER_SELLER_LISTED_POSTS,0, HTTP_REFERRER,CURRENT_URL);
				        if($type==2){
						    if($post_type==1){
							  $grid = TermSellerComponent::getTermSellerPostlists($serviceId, $statusId);
						    } else{
							  $grid =  CourierSellerListingComponent::listCOURIERBuyerPrivatePosts($statusId, $roleId, $serviceId,$destinationtype);
						    }
				          }else{
				          	$grid =  CourierSellerListingComponent::CourierSellerList($statusId, $roleId, $serviceId);
				          }			         
			          	return view('ptl.sellers.seller_list',$grid, [
			          		'statusSelected' => $statusId,
			          		'typeSelected' => $type,
			          		'destinationSelected' => $destinationtype,
			          		'posttypeSelected' => $post_type,			          		
			          		'servicetype'=>'COURIER',
			          		'posts_status_list'=>$posts_status,
			          		'services_seller'=>$lkp_services_seller,
			          		'lead_types_seller'=>$lkp_lead_types
			          	]);
			          break;

				case RELOCATION_DOMESTIC: 
					CommonComponent::activityLog("RELOCATION_DOMESTIC_SELLER_LISTED_POSTS", RELOCATION_DOMESTIC_SELLER_LISTED_POSTS,0, HTTP_REFERRER,CURRENT_URL);				
					  	if (isset($_REQUEST['post_type']) && $_REQUEST['post_type']==2) {
					  		if(isset($_REQUEST['service_type']) && $_REQUEST['service_type']==1){					  										  		
					  			$grid = RelocationSellerComponent::RelocationSellerMarketLeads($statusId, $serviceId, $roleId,$type);
					  		}else{
					  			$grid = TermSellerComponent::getTermSellerPostlists($serviceId, $statusId);
					  		}
					  	} else {						  		
					  			$grid = RelocationSellerComponent::RelocationSpotSellerPosts($statusId, $serviceId, $roleId,$type);
					  	}			  								  
						return view('relocation.sellers.seller_list',$grid, [
							'statusSelected' => $statusId,
					  		'typeSelected' => $type,
							'posts_status_list'=>$posts_status,
							'services_seller'=>$lkp_services_seller,
							'lead_types_seller'=>$lkp_lead_types
						]);	
					   break;

				case ROAD_TRUCK_LEASE       :
					CommonComponent::activityLog("SELLER_LISTED_POSTS",SELLER_LISTED_POSTS,0,HTTP_REFERRER,CURRENT_URL);								
						if($type==2){
	                      $grid = TruckLeaseSellerComponent::llistTruckLeasePrivatePosts($serviceId, $statusId, $type);  	                                                    
	                    }else{
						  $grid = TruckLeaseSellerComponent::listTruckLeaseSellerPosts($statusId, $serviceId, $roleId,$type);
						}						  
					    return view('trucklease.sellers.seller_list',$grid, [
							'statusSelected' => $statusId,
					  		'typeSelected' => $type,
                            'posttypeSelected' => $post_type,
							'posts_status_list'=>$posts_status, 'services_seller'=>$lkp_services_seller,
							'lead_types_seller'=>$lkp_lead_types
						]);	
			          	break;

   				case RELOCATION_OFFICE_MOVE: 
   					CommonComponent::activityLog("RELOCATION_DOMESTIC_SELLER_LISTED_POSTS", RELOCATION_OFFICE_MOVE_SELLER_LISTED_POSTS,0, HTTP_REFERRER,CURRENT_URL);							          
				        if (isset($_REQUEST['post_type']) && $_REQUEST['post_type']==2) {
				          	$grid = RelocationOfficeSellerComponent::RelocationSellerMarketLeads($statusId, $serviceId, $roleId,$type);				          
				        } else {
				          	$grid = RelocationOfficeSellerComponent::RelocationSpotSellerPosts($statusId, $serviceId, $roleId,$type);
				        }				           
			          	return view('relocationoffice.sellers.seller_list',$grid, [
			          		'statusSelected' => $statusId,
			          		'typeSelected' => $post_type,
			          		'posts_status_list'=>$posts_status, 
			          		'services_seller'=>$lkp_services_seller,
			          		'lead_types_seller'=>$lkp_lead_types
			          	]);
			            break;
                                                                  
				case RELOCATION_PET_MOVE: 
					CommonComponent::activityLog("RELOCATION_DOMESTIC_SELLER_LISTED_POSTS", RELOCATION_DOMESTIC_SELLER_LISTED_POSTS,0, HTTP_REFERRER,CURRENT_URL);				
					  	if (isset($_REQUEST['post_type']) && $_REQUEST['post_type']==2) {                                                                                    
					  		$grid = RelocationPetSellerComponent::RelocationPetSellerMarketLeads($statusId, $serviceId, $roleId,$type);
					  	} else {									  		
					  		$grid = RelocationPetSellerComponent::RelocationPetSpotSellerPosts($statusId, $serviceId, $roleId,$type);
					  	}								 					  								  
						return view('relocationpet.sellers.seller_list',$grid, [
							'statusSelected' => $statusId,
					  		'typeSelected' => $type,
							'posts_status_list'=>$posts_status, 
							'services_seller'=>$lkp_services_seller,
							'lead_types_seller'=>$lkp_lead_types
						]);	 
						break;
                                                                          
   				case RELOCATION_INTERNATIONAL: 
   					CommonComponent::activityLog("RELOCATION_INTERNATIONAL_SELLER_LISTED_POSTS", RELOCATION_INTERNATIONAL_SELLER_LISTED_POSTS,0,HTTP_REFERRER,CURRENT_URL);
						if(isset($_REQUEST['int_type']) && $_REQUEST['int_type'] != ''){	
							$inttype = $_REQUEST['int_type'];
							Session::put('int_type_search', $_REQUEST['int_type']);
						}else{
							$inttype = Session::put('int_type_search','');
						}
						if (isset($_REQUEST['post_type']) && $_REQUEST['post_type']==2) {
							if(isset($_REQUEST['service_type']) && $_REQUEST['service_type']==1){
								$grid = RelocationIntSellerComponent::RelocationintSellerMarketLeads($statusId, $serviceId, $roleId,$type, $inttype);
							}else{
								$grid = TermSellerComponent::getTermSellerPostlists($serviceId);
							}
							
						} else {
							$grid = RelocationIntSellerComponent::RelocationintSpotSellerPosts($statusId, $serviceId, $roleId,$type,$inttype);
						}							           
			          	return view('relocationint.sellers.seller_list',$grid, [
			          		'statusSelected' => $statusId,
			          		'typeSelected' => $type,
			          		'posts_status_list'=>$posts_status, 
			          		'services_seller'=>$lkp_services_seller,
			          		'lead_types_seller'=>$lkp_lead_types
			          	]);
			          	break;
                                                                  
                case RELOCATION_GLOBAL_MOBILITY: 
                 	CommonComponent::activityLog("RELOCATION_DOMESTIC_SELLER_LISTED_POSTS", RELOCATION_DOMESTIC_SELLER_LISTED_POSTS,0, HTTP_REFERRER,CURRENT_URL);
				
					  	if (isset($_REQUEST['post_type']) && $_REQUEST['post_type']==2) {
					  		if(isset($_REQUEST['service_type']) && $_REQUEST['service_type']==1){					  										  		
					  			$grid = RelocationGlobalSellerComponent::RelocationSellerMarketLeads($statusId, $serviceId, $roleId,$type);
					  		}else{
					  			$grid = TermSellerComponent::getTermSellerPostlists($serviceId);
					  		}
					  	} else {									  		
					  			$grid = RelocationGlobalSellerComponent::RelocationSpotSellerPosts($statusId, $serviceId, $roleId,$type);
					  	}			  								  
					  	return view('relocationglobal.sellers.seller_list',$grid, [
							'statusSelected' => $statusId,
					  		'typeSelected' => $type,
							'posts_status_list'=>$posts_status,
							'services_seller'=>$lkp_services_seller,
							'lead_types_seller'=>$lkp_lead_types
						]);	
						break;                                  

				default             : 
					CommonComponent::activityLog("SELLER_LISTED_POSTS", SELLER_LISTED_POSTS,0, HTTP_REFERRER,CURRENT_URL);
				        $grid = FtlSellerListingComponent::listFTLSellerPosts($statusId, $serviceId, $roleId,$type);
						break;		   			  
			}
			//rendering the view with the data grid
					
		} catch (Exception $e) {
		
		}		
	}
	
	/**
	 * Seller Post Details List Page.
	 *
	 * @param
	 *        	$request
	 * @return Response
	 */

	public static function sellerPostsList($id)
	{
		Log::info('Seller posts list:'.Auth::id(),array('c'=>'1'));
		try{
			$roleId = Auth::User()->lkp_role_id;
			
			//Retrieval of post statuses
			$posts_status = CommonComponent::getPostStatuses();

			//Retrieval of seller services
			$lkp_services_seller = CommonComponent::getServices();

			//Retrieval of lead types
			$lkp_lead_types = CommonComponent::getLeadTypes();
			
			//Search Form logic
			$statusId = '';
			$serviceId = '';
			$type = '';
		if ( !empty($_REQUEST) ){
			
			
				if(isset($_REQUEST['status']) && $_REQUEST['status'] != ''){	
					$statusId = $_REQUEST['status'];
					Session::put('status_search', $_REQUEST['status']);
				}
				if(isset($_REQUEST['service']) && $_REQUEST['service'] != ''){
					$serviceId = $_REQUEST['service'];
				}
				if(isset($_GET['page'])){
					$statusId = Session::get('status_search');
					$serviceId = Session::get('service_id');
				}
			}else{
				$statusId = '';
				$serviceId = '';
				Session::put('status_search','');
			}

			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}											
			
			switch($serviceId){
				case ROAD_FTL    : CommonComponent::activityLog ( "SELLER_LISTED_POST_ITEMS", 
											 SELLER_LISTED_POST_ITEMS, 0,
											 HTTP_REFERRER, CURRENT_URL );
									  $seller_post    = DB::table('seller_posts')
													  ->leftjoin('seller_post_items','seller_post_items.seller_post_id','=','seller_posts.id')
													  ->where('seller_posts.id',$id)
													  ->select('seller_posts.*','seller_post_items.id as spi')
													  ->get();
									  $sellerselectingbuyers    = DB::table('seller_selected_buyers')
																->leftjoin('users','users.id','=','seller_selected_buyers.buyer_id')
																->where('seller_selected_buyers.seller_post_id',$id)
																->select('users.username')
																->get();
									  
									  $seller_post_items  = DB::table('seller_post_items')
														  ->where('seller_post_items.seller_post_id',$id)
														  ->select('*')
														  ->get();
									  //from location
									  if(isset($seller_post_items[0]->from_location_id)){
									  	$fromlocations  = DB::table('lkp_cities')
													    ->where('lkp_cities.id',$seller_post_items[0]->from_location_id)
													    ->select('id','city_name')
													    ->get();
									  }else{
										$fromlocations =0;
									  }
									  //Payment type
									  $payment = DB::table('lkp_payment_modes')
									    ->where('lkp_payment_modes.id',$seller_post[0]->lkp_payment_mode_id)
									    ->select('id','payment_mode')
									    ->get();
									
									  //to location
									  if(isset($seller_post_items[0]->to_location_id)){
									    $tolocations = DB::table('lkp_cities')
												->where('lkp_cities.id',$seller_post_items[0]->to_location_id)
												->select('id','city_name')
												->get();
									  }else{
										$tolocations =0;
									  }
									 
									  
									  //Viewall count
									  $getpostitemids = DB::table('seller_post_items')
									  ->where('seller_post_items.seller_post_id','=',$id)
									  ->select('seller_post_items.id')
									  ->get();
									  $allcountview =0;
									  if(count($getpostitemids)>0){
									  	for($i=0;$i<count($getpostitemids);$i++){
									  
									  		$countview = DB::table('seller_post_item_views')
									  		->where('seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
									  		->select('seller_post_item_views.id','seller_post_item_views.view_counts')
									  		->get();
									  		if(isset($countview[0]->view_counts))
									  			$allcountview +=  $countview[0]->view_counts;
									  
									  	}
									  }
									  //echo '<pre>';print_r($seller_post);exit;
				                      $grid = FtlSellerListingComponent::listFTLSellerPostItems($statusId, $roleId, $serviceId, $id);
									  return view('ftl.sellers.seller_posts_list',$grid, [
										'statusSelected' => $statusId,
										'seller_post'=>$seller_post,
										'seller_post_items'=>$seller_post_items,
                        				'seller_post_id'=>$id,
										'sellerselectingbuyers'=>$sellerselectingbuyers,
										'payment'=>$payment,
										'tolocations'=>$tolocations,
									  	'typeSelected' => $type,
										'fromlocations'=>$fromlocations,
										'posts_status_list'=>$posts_status, 
									  	'services_seller'=>$lkp_services_seller,
										'lead_types_seller'=>$lkp_lead_types,
									  	'postId'=>$id,
										'allcountview'=>$allcountview]);	
							          break;
				case ROAD_PTL       : CommonComponent::activityLog("PTL_SELLER_LISTED_POST_ITEMS",PTL_SELLER_LISTED_POST_ITEMS,0,HTTP_REFERRER,CURRENT_URL);
				
										//View Count
										$getpostitemids = DB::table('ptl_seller_post_items')
										->where('ptl_seller_post_items.seller_post_id','=',$id)
										->select('ptl_seller_post_items.id')
										->get();
										$allcountview =0;
										if(count($getpostitemids)>0){
											for($i=0;$i<count($getpostitemids);$i++){
													
												$countview = DB::table('ptl_seller_post_item_views')
												->where('ptl_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
												->select('ptl_seller_post_item_views.id','ptl_seller_post_item_views.view_counts')
												->get();
												if(isset($countview[0]->view_counts))
													$allcountview +=  $countview[0]->view_counts;
													
											}
										}
				
										$seller_post_items  = DB::table('ptl_seller_post_items')
										->where('ptl_seller_post_items.seller_post_id',$id)
										->select('*')
										->get();
										$transactionid =  DB::table('ptl_seller_posts')
										  ->where('ptl_seller_posts.id',$id)
										  ->select('ptl_seller_posts.transaction_id')
										  ->get();
									  $grid = PtlSellerListingComponent::listPTLSellerPostItems($statusId, $roleId, $serviceId ,$id);
									  $postdetails = PtlSellerListingComponent::listPTLSellertopNavPostItems($id);
									  return view('ptl.sellers.seller_posts_list',$grid,['postdetails'=>$postdetails,'transactionid'=>$transactionid[0]->transaction_id,'postId'=>$id,
                        				'seller_post_id'=>$id,
									  	'posts_status_list'=>$posts_status,
									  	'statusSelected' => $statusId,
										'seller_post_items'=>$seller_post_items,
										'allcountview'=>$allcountview]);	
							          break;
				case ROAD_INTRACITY : CommonComponent::activityLog("INTRA_SELLER_LISTED_POST_ITEMS",
											 INTRA_SELLER_LISTED_POST_ITEMS,0,
											 HTTP_REFERRER,CURRENT_URL);
				                      $grid = FtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
							          break;
				case RAIL       	: CommonComponent::activityLog("RAIL_SELLER_LISTED_POST_ITEMS",
							          RAIL_SELLER_LISTED_POST_ITEMS,0,
							          HTTP_REFERRER,CURRENT_URL);
				
										//View Count
										$getpostitemids = DB::table('rail_seller_post_items')
										->where('rail_seller_post_items.seller_post_id','=',$id)
										->select('rail_seller_post_items.id')
										->get();
										$allcountview =0;
										if(count($getpostitemids)>0){
											for($i=0;$i<count($getpostitemids);$i++){
													
												$countview = DB::table('rail_seller_post_item_views')
												->where('rail_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
												->select('rail_seller_post_item_views.id','rail_seller_post_item_views.view_counts')
												->get();
												if(isset($countview[0]->view_counts))
													$allcountview +=  $countview[0]->view_counts;
													
											}
										}
					
										$seller_post_items  = DB::table('rail_seller_post_items')
										->where('rail_seller_post_items.seller_post_id',$id)
										->select('*')
										->get();
				
										$transactionid =  DB::table('rail_seller_posts')
										  ->where('rail_seller_posts.id',$id)
										  ->select('rail_seller_posts.transaction_id')
										  ->get();
							          $grid = RailSellerListingComponent::listRailSellerPostItems($statusId, $roleId, $serviceId ,$id);
							          $postdetails = RailSellerListingComponent::listRailSellertopNavPostItems($id);
							          return view('ptl.sellers.seller_posts_list',$grid,['postdetails'=>$postdetails,'transactionid'=>$transactionid[0]->transaction_id,
                        				'seller_post_id'=>$id,
							          	'postId'=>$id,
							          	'posts_status_list'=>$posts_status,
							          	'statusSelected' => $statusId,
										'seller_post_items'=>$seller_post_items,
										'allcountview'=>$allcountview]);	
							          break;
				case AIR_DOMESTIC   : CommonComponent::activityLog("AIRDOM_SELLER_LISTED_POST_ITEMS",
							          AIRDOM_SELLER_LISTED_POST_ITEMS,0,
							          HTTP_REFERRER,CURRENT_URL);
				
										//View Count
										$getpostitemids = DB::table('airdom_seller_post_items')
										->where('airdom_seller_post_items.seller_post_id','=',$id)
										->select('airdom_seller_post_items.id')
										->get();
										$allcountview =0;
										if(count($getpostitemids)>0){
											for($i=0;$i<count($getpostitemids);$i++){
													
												$countview = DB::table('airdom_seller_post_item_views')
												->where('airdom_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
												->select('airdom_seller_post_item_views.id','airdom_seller_post_item_views.view_counts')
												->get();
												if(isset($countview[0]->view_counts))
													$allcountview +=  $countview[0]->view_counts;
													
											}
										}
				
										$seller_post_items  = DB::table('airdom_seller_post_items')
										->where('airdom_seller_post_items.seller_post_id',$id)
										->select('*')
										->get();
										$transactionid =  DB::table('airdom_seller_posts')
										  ->where('airdom_seller_posts.id',$id)
										  ->select('airdom_seller_posts.transaction_id')
										  ->get();
							          $grid = AirDomesticSellerListingComponent::listAirdomSellerPostItems($statusId, $roleId, $serviceId ,$id);
							          $postdetails = AirDomesticSellerListingComponent::listAirdomSellertopNavPostItems($id);
							          return view('ptl.sellers.seller_posts_list',$grid,['postdetails'=>$postdetails,'transactionid'=>$transactionid[0]->transaction_id,
                        				'seller_post_id'=>$id,
						          		'postId'=>$id,
						          		'posts_status_list'=>$posts_status,
						          		'statusSelected' => $statusId,
										'seller_post_items'=>$seller_post_items,
										'allcountview'=>$allcountview]);	
							          break;

				case AIR_INTERNATIONAL   : CommonComponent::activityLog("AIRINT_SELLER_LISTED_POST_ITEMS",
							          AIRINT_SELLER_LISTED_POST_ITEMS,0,
							          HTTP_REFERRER,CURRENT_URL);
				
										//View Count
										$getpostitemids = DB::table('airint_seller_post_items')
										->where('airint_seller_post_items.seller_post_id','=',$id)
										->select('airint_seller_post_items.id')
										->get();
										$allcountview =0;
										if(count($getpostitemids)>0){
											for($i=0;$i<count($getpostitemids);$i++){
													
												$countview = DB::table('airint_seller_post_item_views')
												->where('airint_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
												->select('airint_seller_post_item_views.id','airint_seller_post_item_views.view_counts')
												->get();
												if(isset($countview[0]->view_counts))
													$allcountview +=  $countview[0]->view_counts;
													
											}
										}
				
										$seller_post_items  = DB::table('airint_seller_post_items')
										->where('airint_seller_post_items.seller_post_id',$id)
										->select('*')
										->get();
									  $transactionid =  DB::table('airint_seller_posts')
										  ->where('airint_seller_posts.id',$id)
										  ->select('airint_seller_posts.transaction_id')
										  ->get();
							          $grid = AirInternationalSellerListingComponent::listAirintSellerPostItems($statusId, $roleId, $serviceId ,$id);
							          $postdetails = AirInternationalSellerListingComponent::listAirintSellertopNavPostItems($id);
							          return view('ptl.sellers.seller_posts_list',$grid,['statusSelected' => $statusId,'postdetails'=>$postdetails,'postId'=>$id,
										'posts_status_list'=>$posts_status,'transactionid'=>$transactionid[0]->transaction_id,
                        				'seller_post_id'=>$id,
						          		'postId'=>$id,
						          		'posts_status_list'=>$posts_status,
						          		'statusSelected' => $statusId,
										'seller_post_items'=>$seller_post_items,
										'allcountview'=>$allcountview]);	
							          break;
				case OCEAN   : CommonComponent::activityLog("OCCEAN_SELLER_LISTED_POST_ITEMS",
							          OCCEAN_SELLER_LISTED_POST_ITEMS,0,
							          HTTP_REFERRER,CURRENT_URL);
				
										//View Count
										$getpostitemids = DB::table('ocean_seller_post_items')
										->where('ocean_seller_post_items.seller_post_id','=',$id)
										->select('ocean_seller_post_items.id')
										->get();
										$allcountview =0;
										if(count($getpostitemids)>0){
											for($i=0;$i<count($getpostitemids);$i++){
													
												$countview = DB::table('ocean_seller_post_item_views')
												->where('ocean_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
												->select('ocean_seller_post_item_views.id','ocean_seller_post_item_views.view_counts')
												->get();
												if(isset($countview[0]->view_counts))
													$allcountview +=  $countview[0]->view_counts;
													
											}
										}
				
										$seller_post_items  = DB::table('ocean_seller_post_items')
										->where('ocean_seller_post_items.seller_post_id',$id)
										->select('*')
										->get();
				 					  $transactionid =  DB::table('ocean_seller_posts')
										  ->where('ocean_seller_posts.id',$id)
										  ->select('ocean_seller_posts.transaction_id')
										  ->get();
							          $grid = OcceanSellerListingComponent::listOcceanSellerPostItems($statusId, $roleId, $serviceId ,$id);
							          $postdetails = OcceanSellerListingComponent::listOcceanSellertopNavPostItems($id);
							          return view('ptl.sellers.seller_posts_list',$grid,['statusSelected' => $statusId,'postdetails'=>$postdetails,'postId'=>$id,
										'posts_status_list'=>$posts_status,'transactionid'=>$transactionid[0]->transaction_id,
                        				'seller_post_id'=>$id,
										'seller_post_items'=>$seller_post_items,
										'allcountview'=>$allcountview]);	
							          break;
					case COURIER   : CommonComponent::activityLog("COURIER_SELLER_LISTED_POST_ITEMS",
							          COURIER_SELLER_LISTED_POST_ITEMS,0,
							          HTTP_REFERRER,CURRENT_URL);
							          
							          //View Count
							          $getpostitemids = DB::table('courier_seller_post_items')
							          ->where('courier_seller_post_items.seller_post_id','=',$id)
							          ->select('courier_seller_post_items.id')
							          ->get();
							          $allcountview =0;
							          if(count($getpostitemids)>0){
							          	for($i=0;$i<count($getpostitemids);$i++){
							          			
							          		$countview = DB::table('courier_seller_post_item_views')
							          		->where('courier_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
							          		->select('courier_seller_post_item_views.id','courier_seller_post_item_views.view_counts')
							          		->get();
							          		if(isset($countview[0]->view_counts))
							          			$allcountview +=  $countview[0]->view_counts;
							          			
							          	}
							          }
							          
							          $seller_post_items  = DB::table('courier_seller_post_items')
							          ->where('courier_seller_post_items.seller_post_id',$id)
							          ->select('*')
							          ->get();
							          $transactionid =  DB::table('courier_seller_posts')
							          ->where('courier_seller_posts.id',$id)
							          ->select('courier_seller_posts.transaction_id')
							          ->get();
							          $grid = CourierSellerListingComponent::listCourierSellerPostItems($statusId, $roleId, $serviceId ,$id);
							          $postdetails = CourierSellerListingComponent::listCourierSellertopNavPostItems($id);
							          return view('ptl.sellers.seller_posts_list',$grid,['statusSelected' => $statusId,'postdetails'=>$postdetails,'postId'=>$id,
							          		'posts_status_list'=>$posts_status,'transactionid'=>$transactionid[0]->transaction_id,
							          		'seller_post_id'=>$id,
							          		'seller_post_items'=>$seller_post_items,
							          		'allcountview'=>$allcountview]);
							          break;

					case ROAD_TRUCK_LEASE    : CommonComponent::activityLog ( "SELLER_LISTED_POST_ITEMS",
							          SELLER_LISTED_POST_ITEMS, 0,
							          HTTP_REFERRER, CURRENT_URL );
							          $seller_post    = DB::table('trucklease_seller_posts')
							          ->leftjoin('trucklease_seller_post_items','trucklease_seller_post_items.seller_post_id','=','trucklease_seller_posts.id')
							          ->where('trucklease_seller_posts.id',$id)
							          ->select('trucklease_seller_posts.*','trucklease_seller_post_items.id as spi')
							          ->get();
							          $sellerselectingbuyers    = DB::table('trucklease_seller_selected_buyers')
							          ->leftjoin('users','users.id','=','trucklease_seller_selected_buyers.buyer_id')
							          ->where('trucklease_seller_selected_buyers.seller_post_id',$id)
							          ->select('users.username')
							          ->get();
							          	
							          $seller_post_items  = DB::table('trucklease_seller_post_items')
							          ->where('trucklease_seller_post_items.seller_post_id',$id)
							          ->select('*')
							          ->get();
							          //from location
							          if(isset($seller_post_items[0]->from_location_id)){
							          	$fromlocations  = DB::table('lkp_cities')
							          	->where('lkp_cities.id',$seller_post_items[0]->from_location_id)
							          	->select('id','city_name')
							          	->get();
							          }else{
							          	$fromlocations =0;
							          }
							          //Payment type
							          $payment = DB::table('lkp_payment_modes')
							          ->where('lkp_payment_modes.id',$seller_post[0]->lkp_payment_mode_id)
							          ->select('id','payment_mode')
							          ->get();
							          	
							          //to location
							         
							          
							          	
							          //Viewall count
							          $getpostitemids = DB::table('trucklease_seller_post_items')
							          ->where('trucklease_seller_post_items.seller_post_id','=',$id)
							          ->select('trucklease_seller_post_items.id')
							          ->get();
							          $allcountview =0;
							          if(count($getpostitemids)>0){
							          	for($i=0;$i<count($getpostitemids);$i++){
							          			
							          		$countview = DB::table('trucklease_seller_post_item_views')
							          		->where('trucklease_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
							          		->select('trucklease_seller_post_item_views.id','trucklease_seller_post_item_views.view_counts')
							          		->get();
							          		if(isset($countview[0]->view_counts))
							          			$allcountview +=  $countview[0]->view_counts;
							          			
							          	}
							          }
							          //echo '<pre>';print_r($seller_post);exit;
							          $grid = TruckLeaseSellerComponent::listTruckLeaseSellerPostItems($statusId, $roleId, $serviceId, $id);
							          return view('trucklease.sellers.seller_posts_list',$grid, [
							          		'statusSelected' => $statusId,
							          		'seller_post'=>$seller_post,
							          		'seller_post_items'=>$seller_post_items,
							          		'seller_post_id'=>$id,
							          		'sellerselectingbuyers'=>$sellerselectingbuyers,
							          		'payment'=>$payment,
							          		'typeSelected' => $type,
							          		'fromlocations'=>$fromlocations,
							          		'posts_status_list'=>$posts_status,
							          		'services_seller'=>$lkp_services_seller,
							          		'lead_types_seller'=>$lkp_lead_types,
							          		'postId'=>$id,
							          		'allcountview'=>$allcountview]);
							          break;
							          
							       
				case ROAD_TRUCK_HAUL    : CommonComponent::activityLog ( "TRUCKHAUL_SELLER_LISTED_POST_ITEMS", 
											 TRUCKHAUL_SELLER_LISTED_POST_ITEMS, 0,
											 HTTP_REFERRER, CURRENT_URL );

									  $seller_post    = DB::table('truckhaul_seller_posts')
													  ->leftjoin('truckhaul_seller_post_items','truckhaul_seller_post_items.seller_post_id','=','truckhaul_seller_posts.id')
													  ->where('truckhaul_seller_posts.id',$id)
													  ->select('truckhaul_seller_posts.*','truckhaul_seller_post_items.id as spi')
													  ->get();
									  $sellerselectingbuyers    = DB::table('truckhaul_seller_selected_buyers')
																->leftjoin('users','users.id','=','truckhaul_seller_selected_buyers.buyer_id')
																->where('truckhaul_seller_selected_buyers.seller_post_id',$id)
																->select('users.username')
																->get();
									  
									  $seller_post_items  = DB::table('truckhaul_seller_post_items')
														  ->where('truckhaul_seller_post_items.seller_post_id',$id)
														  ->select('*')
														  ->get();
									  //from location
									  if(isset($seller_post_items[0]->from_location_id)){
									  	$fromlocations  = DB::table('lkp_cities')
													    ->where('lkp_cities.id',$seller_post_items[0]->from_location_id)
													    ->select('id','city_name')
													    ->get();
									  }else{
										$fromlocations =0;
									  }
									  //Payment type
									  $payment = DB::table('lkp_payment_modes')
									    ->where('lkp_payment_modes.id',$seller_post[0]->lkp_payment_mode_id)
									    ->select('id','payment_mode')
									    ->get();
									
									  //to location
									  if(isset($seller_post_items[0]->to_location_id)){
									    $tolocations = DB::table('lkp_cities')
												->where('lkp_cities.id',$seller_post_items[0]->to_location_id)
												->select('id','city_name')
												->get();
									  }else{
										$tolocations =0;
									  }
									 
									  
									  //Viewall count
									  $getpostitemids = DB::table('truckhaul_seller_post_items')
									  ->where('truckhaul_seller_post_items.seller_post_id','=',$id)
									  ->select('truckhaul_seller_post_items.id')
									  ->get();
									  $allcountview =0;
									  if(count($getpostitemids)>0){
									  	for($i=0;$i<count($getpostitemids);$i++){
									  
									  		$countview = DB::table('truckhaul_seller_post_item_views')
									  		->where('truckhaul_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
									  		->select('truckhaul_seller_post_item_views.id','truckhaul_seller_post_item_views.view_counts')
									  		->get();
									  		if(isset($countview[0]->view_counts))
									  			$allcountview +=  $countview[0]->view_counts;
									  
									  	}
									  }
				                      $grid = TruckHaulSellerListingComponent::listTruckHaulSellerPostItems($statusId, $roleId, $serviceId, $id);
									  return view('truckhaul.sellers.seller_posts_list',$grid, [
										'statusSelected' => $statusId,
										'seller_post'=>$seller_post,
										'seller_post_items'=>$seller_post_items,
                        				'seller_post_id'=>$id,
										'sellerselectingbuyers'=>$sellerselectingbuyers,
										'payment'=>$payment,
										'tolocations'=>$tolocations,
									  	'typeSelected' => $type,
										'fromlocations'=>$fromlocations,
										'posts_status_list'=>$posts_status, 
									  	'services_seller'=>$lkp_services_seller,
										'lead_types_seller'=>$lkp_lead_types,
									  	'postId'=>$id,
										'allcountview'=>$allcountview]);	
							          break;				
				default             : break;		   			  
			}
				

		} catch( Exception $e ) {           
            return $e->message;	   
	    }

	}
	
	public function SellerSearchBuyers()
	{
		// Checking Service Selected or not
    	if( !session()->has('service_id') || empty(session('service_id')) ):
    		return redirect('home');
    	endif;
    	$this->data['serviceID'] = session('service_id');

		Log::info('Seller search for buyers what was created by seller: '.Auth::id(), ['c'=>'1']);
		try
		{	
			//Loading respective search form based on service
			switch(  $this->data['serviceID'] ){

				case ROAD_FTL: 
					CommonComponent::activityLog("FTL_SELLER_SEARCHED_BUYER_POSTS",
						FTL_SELLER_SEARCHED_BUYER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return view('seller.ftl.search.search_form', [
						'loadtypemasters' 	 => CommonComponent::getAllLoadTypes(),
						'vehicletypemasters' => CommonComponent::getAllVehicleTypes()
					]);
					break;
				
				case ROAD_PTL: 
					CommonComponent::activityLog("PTL_SELLER_SEARCHED_BUYER_POSTS",
						PTL_SELLER_SEARCHED_BUYER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return view('ptl.sellers.seller_search_buyers',[
						'loadtypemasters' 		=> CommonComponent::getAllLoadTypes(),
						'packagingtypesmasters' => CommonComponent::getAllPackageTypes()
					]);
					break;
				
				case RAIL: 
					CommonComponent::activityLog("RAIL_SELLER_SEARCHED_BUYER_POSTS",
						RAIL_SELLER_SEARCHED_BUYER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return view('ptl.sellers.seller_search_buyers',[
						'loadtypemasters' 		=> CommonComponent::getAllLoadTypes(),
						'packagingtypesmasters' => CommonComponent::getAllPackageTypes()
					]);
					break;
				
				case AIR_DOMESTIC       : CommonComponent::activityLog("AIR_DOMESTIC_SELLER_SEARCHED_BUYER_POSTS",
					AIR_DOMESTIC_SELLER_SEARCHED_BUYER_POSTS,0,HTTP_REFERRER,CURRENT_URL);
					return view('ptl.sellers.seller_search_buyers',[
						'loadtypemasters' 		=> CommonComponent::getAllLoadTypes(),
						'packagingtypesmasters' => CommonComponent::getAllPackageTypes()
					]);
					break;
				
				case COURIER: 
					CommonComponent::activityLog("COURIER_SELLER_SEARCHED_BUYER_POSTS",
						COURIER_SELLER_SEARCHED_BUYER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return view('ptl.sellers.seller_search_buyers',[
						'loadtypemasters' 		=> CommonComponent::getAllLoadTypes(),
						'packagingtypesmasters' => CommonComponent::getAllCourierTypes()
					]);
					break;
				
				case AIR_INTERNATIONAL: 
					CommonComponent::activityLog("AIR_INTERNATIONAL_SELLER_SEARCHED_BUYER_POSTS",
						AIR_INTERNATIONAL_SELLER_SEARCHED_BUYER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return view('ptl.sellers.seller_search_buyers',[
						'loadtypemasters' 		=> CommonComponent::getAllLoadTypes(),
						'packagingtypesmasters' => CommonComponent::getAllPackageTypes(),
						'shipmenttypes' 		=> CommonComponent::getAllShipmentTypes(),
						'senderidentity' 		=> CommonComponent::getAllSenderIdentities()
					]);
					break;
				
				case OCEAN: 
					CommonComponent::activityLog("OCEAN_SELLER_SEARCHED_BUYER_POSTS",
						OCEAN_SELLER_SEARCHED_BUYER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return view('ptl.sellers.seller_search_buyers',[
						'loadtypemasters' 		=> CommonComponent::getAllLoadTypes(),
						'packagingtypesmasters' => CommonComponent::getAllPackageTypes(),
						'shipmenttypes' 		=> CommonComponent::getAllShipmentTypes(),
						'senderidentity' 		=> CommonComponent::getAllSenderIdentities()
					]);
					break;
				
				case RELOCATION_DOMESTIC: 
					CommonComponent::activityLog("RELOCATION_SELLER_SEARCHED_BUYER_POSTS",
						RELOCATION_SELLER_SEARCHED_BUYER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return view('seller.relocation.home.domestic.search.search_form',[
						'ratecardTypes' => CommonComponent::getAllRatecardTypes()
					]);
					break;
				
				case ROAD_INTRACITY: break;
				
				case ROAD_TRUCK_HAUL: 
					CommonComponent::activityLog("TRUCKHAUL_SELLER_SEARCHED_BUYER_POSTS",
						TRUCKHAUL_SELLER_SEARCHED_BUYER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return view('seller.truckhaul.search.search_form',[
						'loadtypemasters' 		=> CommonComponent::getAllLoadTypes(),
						'vehicletypemasters' 	=> CommonComponent::getAllVehicleTypes()
					]);
					break;
				
				case ROAD_TRUCK_LEASE:
					CommonComponent::activityLog("TRUCKLEASE_SELLER_SEARCHED_BUYER_POSTS",
					TRUCKLEASE_SELLER_SEARCHED_BUYER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return view('seller.trucklease.search.search_form',[
						'getAllleaseTypes' 		=> CommonComponent::getAllLeaseTypes(),
						'vehicletypemasters' 	=> CommonComponent::getAllVehicleTypes()
					]);
					break;					
				
				case RELOCATION_OFFICE_MOVE :
					return view('seller.relocation.office.domestic.search.search_form');
					break;                                    
                
                case RELOCATION_PET_MOVE:
                    return view('seller.relocation.home.petmove.search.search_form', [
						'getAllPetTypes' => CommonComponent::getAllPetTypes()
					]);
					break; 
				
				case RELOCATION_INTERNATIONAL:
					return view('seller.relocation.home.international.search.search_form');
					break; 
				
				case RELOCATION_GLOBAL_MOBILITY:
					CommonComponent::activityLog("RELOCATION_GM_SELLER_SEARCHED_BUYER_POSTS",
						RELOCATION_GM_SELLER_SEARCHED_BUYER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
				    return view('seller.relocation.home.gblmobility.search.search_form',[
                   		'lkp_relgm_services' => CommonComponent::getLkpRelocationGMServices()
                    ]);
					break;  

				default:
					CommonComponent::activityLog("FTL_SELLER_SEARCHED_BUYER_POSTS",
						FTL_SELLER_SEARCHED_BUYER_POSTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return view('seller.ftl.search.search_form', [
						'loadtypemasters' 	 => CommonComponent::getAllLoadTypes(),
						'vehicletypemasters' => CommonComponent::getAllVehicleTypes()
					]);
					break;
			}

		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
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
    	$result = FtlSellerComponent::getFtlSellerSearchList(
    		$this->data['roleID'], $this->data['serviceID'], $this->data['statusID']
    	);

		$results_count_view 		= session('results_count');
		$results_count_more_view	= session('results_count_more');
		
		$lkp_load_type_name = $lkp_vehicle_type_name = '';
		if($results_count_view == 1):
			$lkp_load_type_name 	= CommonComponent::getLoadType($request->lkp_load_type_id);
			$lkp_vehicle_type_name 	= CommonComponent::getVehicleType($request->lkp_vehicle_type_id);
		endif;

		$lkp_load_type_name_results = $lkp_vehicle_type_name_results = '';
		if($results_count_more_view == 2):
			
			if($request->exists('lkp_load_type_id')){
				$lkp_load_type_name_results = CommonComponent::getLoadType(
					$request->lkp_load_type_id
				);
			}	

			$lkp_vehicle_type_name_results = CommonComponent::getVehicleType(
				$request->lkp_vehicle_type_id
			);

		endif;
		
		// Getting vehicle types based on Requested Quantity
		$vehicletypemasters = [];
		if($request->has('qty')):
			$vehicletypemasters =  CommonComponent::getQtyBasedAllVehicleTypes($request->qty);
		endif;
		                 
		// Storing Request Data to Session
		session()->put([
			'searchMod' => [
				'session_from_city_id' 	=> $request->from_city_id,
				'qty' 			=> $request->qty,
				'to_city_id' 	=> $request->to_city_id,
				'from_location' => $request->from_location,
				'to_location' 	=> $request->to_location,
				'lkp_load_type_id' => $request->lkp_load_type_id,
				'lkp_vehicle_type_id' => $request->lkp_vehicle_type_id,
				'dispatch_date' 	=> $request->dispatch_date,
				'delivery_date' 	=> $request->delivery_date,
				'seller_district_id' => $request->seller_district_id,
				'capacity' 		=> $request->capacity
			]
		]);

		return view('seller.ftl.search.search_results', [
			'grid' 				=> $result ['grid'],
		 	'filter' 			=> $result ['filter'],
		 	'posts_status_list'	=> CommonComponent::getPostStatuses(),
		 	'statusSelected' 	=> $this->data['statusID'],
			'load_type_name'  	=> $lkp_load_type_name,
			'vehicle_type_name' => $lkp_vehicle_type_name,
			'load_type_name_results'  	=> $lkp_load_type_name_results,
			'vehicle_type_name_results' => $lkp_vehicle_type_name_results,
		 	'from_city_id'  	=> session('searchMod.from_city_id' ),
			'qty'  				=> session('searchMod.qty' ),
		 	'to_city_id' 		=> session('searchMod.to_city_id' ),
		 	'from_location'  	=> session('searchMod.from_location' ),
		 	'to_location' 		=> session('searchMod.to_location' ),
			'lkp_load_type_id' 	=> session('searchMod.lkp_load_type_id' ),
			'lkp_vehicle_type_id' => session('searchMod.lkp_vehicle_type_id' ),
			'dispatch_date' 	=> session('searchMod.dispatch_date' ),
			'delivery_date' 	=> session('searchMod.delivery_date' ),
			'seller_district_id'=> session('searchMod.seller_district_id' ),
			'capacity' 			=> session('searchMod.capacity' ),
		 	'loadtypemasters' 	=> CommonComponent::getAllLoadTypes(),
			'vehicletypemasters' 		=> $vehicletypemasters,
			'vehicletypemasters_term' 	=> CommonComponent::getAllVehicleTypes(),
		] );
    }
    
    /**
	* Displaying truck haul Search results
	*	 
	* @param  $request
	* @return Response
	*/    
	private function _getTruckHaulSearchResults($request)
    {
    	$result = TruckHaulSellerComponent::getTruckHaulSellerSearchList ( 
    		$this->data['roleID'], $this->data['serviceID'], $this->data['statusID']
    	);
					 
		$results_count_view = Session::get('results_count');
		$results_count_more_view = Session::get('results_count_more');
		if($results_count_view == 1){
			$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
			$lkp_vehicle_type_name = CommonComponent::getVehicleType($_REQUEST['lkp_vehicle_type_id']);
		}else{
			$lkp_load_type_name = '';
			$lkp_vehicle_type_name = '';
		}

		if($results_count_more_view == 2){
			if(!isset($_REQUEST['lkp_load_type_id']))
				$lkp_load_type_name_results = '';
			else
			$lkp_load_type_name_results = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
			$lkp_vehicle_type_name_results = CommonComponent::getVehicleType($_REQUEST['lkp_vehicle_type_id']);
		}else{
			$lkp_load_type_name_results = '';
			$lkp_vehicle_type_name_results = '';
		}

		if($_REQUEST['qty']!='')
		 	$vehicletypemasters=  CommonComponent::getAllVehicleTypes();

		session()->put([
			'searchMod' 		=> [
				'from_city_id' 	=> $_REQUEST['from_city_id'],
				'qty' 			=> $_REQUEST['qty'],
				'to_city_id' 	=> $_REQUEST['to_city_id'],
				'from_location' => $_REQUEST['from_location'],
				'to_location' 	=> $_REQUEST['to_location'],
				'lkp_load_type_id' => $_REQUEST['lkp_load_type_id'],
				'lkp_vehicle_type_id' => $_REQUEST['lkp_vehicle_type_id'],
				'dispatch_date' 	=> $_REQUEST['dispatch_date'],
				'seller_district_id' => $_REQUEST['seller_district_id'],
				'capacity' => $_REQUEST['capacity'],
			]
		]);

		return view ( 'seller.truckhaul.search.search_results', [
			'grid' 				=> $result ['grid'],
		 	'filter' 			=> $result ['filter'],
		 	'posts_status_list'	=> CommonComponent::getPostStatuses(),
		 	'statusSelected' 	=> $this->data['statusID'],
			'load_type_name'  	=> $lkp_load_type_name,
			'vehicle_type_name' => $lkp_vehicle_type_name,
			'load_type_name_results'  	=> $lkp_load_type_name_results,
			'vehicle_type_name_results' => $lkp_vehicle_type_name_results,
		 	'from_city_id'  	=> session('searchMod.from_city_id' ),
			'qty'  				=> session('searchMod.qty' ),
		 	'to_city_id' 		=> session('searchMod.to_city_id' ),
		 	'from_location'  	=> session('searchMod.from_location' ),
		 	'to_location' 		=> session('searchMod.to_location' ),
			'lkp_load_type_id' 	=> session('searchMod.lkp_load_type_id' ),
			'lkp_vehicle_type_id' => session('searchMod.lkp_vehicle_type_id' ),
			'dispatch_date' 	=> session('searchMod.dispatch_date' ),
			'seller_district_id'=> session('searchMod.seller_district_id' ),
			'capacity' 			=> session('searchMod.capacity' ),
		 	'loadtypemasters' 	=> CommonComponent::getAllLoadTypes(),
		 	'vehicletypemasters'=> $vehicletypemasters,
		 	'vehicletypemasters_term' => CommonComponent::getAllVehicleTypes(),
		] );

    }

    /**
	* Displaying Truck Lease Search results
	*	 
	* @param  $request
	* @return Response
	*/    
	private function _getTruckLeaseSearchResults($request)
    {
    	$result = \App\Components\TruckLease\TruckLeaseSellerComponent::getSellerSearchList ( 
    		$this->data['roleID'], $this->data['serviceID'], $this->data['statusID']
    	);

		$results_count_view 		= session('results_count');
		$results_count_more_view 	= session('results_count_more');

		$lkp_lease_term_type_name = $lkp_vehicle_type_name = '';
		if($results_count_view == 1){
		 	$lkp_lease_term_type_name = CommonComponent::getAllLeaseName($_REQUEST['lkp_trucklease_lease_term_id']);
		 	$lkp_vehicle_type_name = CommonComponent::getVehicleType($_REQUEST['lkp_vehicle_type_id']);
		}

		$lkp_lease_term_type_name_results = $lkp_vehicle_type_name_results = '';
		if($results_count_more_view == 2){
		 	if(!isset($_REQUEST['lkp_trucklease_lease_term_id']))
			 	$lkp_lease_term_type_name_results = '';
		 	else
			 	$lkp_lease_term_type_name_results = CommonComponent::getAllLeaseName($_REQUEST['lkp_trucklease_lease_term_id']);

		 	$lkp_vehicle_type_name_results = CommonComponent::getVehicleType($_REQUEST['lkp_vehicle_type_id']);
		}

		session()->put([
			'searchMod' 		=> [
				'from_city_id'	=> $_REQUEST['from_city_id'],
				'from_location'	=> $_REQUEST['from_location'],
				'lkp_trucklease_lease_term_id'	=> $_REQUEST['lkp_trucklease_lease_term_id'],
				'lkp_vehicle_type_id'	=> $_REQUEST['lkp_vehicle_type_id'],
				'dispatch_date'	=> $_REQUEST['dispatch_date'],
				'delivery_date'	=> $_REQUEST['delivery_date'],
				'seller_district_id'	=> $_REQUEST['seller_district_id'],
			]
		]);

		return view('seller.trucklease.search.search_results', [
			'grid' 		=> $result ['grid'],
			'filter' 	=> $result ['filter'],
			'posts_status_list'	=> CommonComponent::getPostStatuses(),
			'statusSelected' 	=> $this->data['statusID'],
			'lease_term_type_name'  => $lkp_lease_term_type_name,
			'vehicle_type_name'  	=> $lkp_vehicle_type_name,
			'lkp_lease_term_type_name_results'  => $lkp_lease_term_type_name_results,
			'vehicle_type_name_results'  		=> $lkp_vehicle_type_name_results,
			'from_city_id'  		=> session('searchMod.from_city_id' ),
			'from_location'  		=> session('searchMod.from_location' ),
			'lkp_trucklease_lease_term_id' => session('searchMod.lkp_trucklease_lease_term_id' ),
			'lkp_vehicle_type_id' 	=> session('searchMod.lkp_vehicle_type_id' ),
			'dispatch_date'			=> session('searchMod.dispatch_date' ),
			'delivery_date' 		=> session('searchMod.delivery_date' ),
			'seller_district_id' 	=> session('searchMod.seller_district_id' ),
			'vehicletypemasters' 	=> CommonComponent::getAllVehicleTypes(),
			'driver_availability' 	=> CommonComponent::getDriverAvailabilities(),
			'getAllleaseTypes'		=> CommonComponent::getAllLeaseTypes()
		] );
    }

    /**
	* Displaying Relocation Home Domestic Search results
	*	 
	* @param  $request
	* @return Response
	*/    
	private function _getRelocHomeDomesticSearchResults($request)
    {
    	$result = RelocationSellerComponent::getRelocationSellerSearchResults( 
    		$this->data['roleID'], $this->data['serviceID'], $this->data['statusID'] 
    	);

    	session()->put([
			'searchMod' 		=> [
				'from_location'	=> $request->from_location,
		 		'from_location_id'=> $request->from_location_id,
		 		'to_location'	=> $request->to_location,
		 		'to_location_id'=> $request->to_location_id,
		 		'valid_from'	=> $request->valid_from,
		 		'valid_to'	=> $request->valid_to,
		 		'post_type'	=> $request->post_type,
			]
		]);

		return view ( 'seller.relocation.home.domestic.search.search_results', [
	 		'gridBuyer' 	=> $result ['gridBuyer'],
	 		'from_location'	=> $_REQUEST['from_location'],
	 		'from_location_id'=> $_REQUEST['from_location_id'],
	 		'to_location'	=> $_REQUEST['to_location'],
	 		'to_location_id'=> $_REQUEST['to_location_id'],
	 		'valid_from'	=> $_REQUEST['valid_from'],
	 		'valid_to'	=> $_REQUEST['valid_to'],
	 		'post_type'	=> $_REQUEST['post_type'],
		]);
    }

    /**
	* Displaying Relocation Home Petmove Search results
	*
	* @param  $request
	* @return Response
	*/    
	private function _getRelocHomePetmoveSearchResults($request)
    {
    	$result = RelocationPetSellerComponent::getRelocationPetSellerSearchResults(
    		$request, $this->data['serviceID']
    	);

    	session()->put([
			'searchMod' => [
				'from_location'		=> $request->from_location,
	            'from_location_id'	=> $request->from_location_id,
	            'to_location'		=> $request->to_location,
	            'to_location_id'	=> $request->to_location_id,
	            'valid_from'		=> $request->valid_from,
	            'valid_to'			=> $request->valid_to,
	            'pet_type'			=> $request->pet_type,
			]
		]);

        return view('seller.relocation.home.petmove.search.search_results', [
        	'gridBuyer' 		=> $result['gridBuyer'],
            'from_location'		=> $request->from_location,
            'from_location_id'	=> $request->from_location_id,
            'to_location'		=> $request->to_location,
            'to_location_id'	=> $request->to_location_id,
            'valid_from'		=> $request->valid_from,
            'valid_to'			=> $request->valid_to,
            'pet_type'			=> $request->pet_type,
            'getAllPetTypes' 	=> CommonComponent::getAllPetTypes()
        ]);
    }

    /**
	* Displaying Relocation Home Petmove Search results
	*
	* @param  $request
	* @return Response
	*/    
	private function _getRelocHomeInternationalSearchResults($request)
    {
    	if($request->exists('service_type') &&  $request->service_type== 1):
			$result = RelocationAirSellerComponent::getRelocationInternationSellerSearchResults(
				$request, $this->data['serviceID']
			);
		else:
			$result = RelocationOceanSellerComponent::getRelocationInternationSellerSearchResults(
				$request, $this->data['serviceID']
			);
		endif;

		return view('seller.relocation.home.international.search.search_results', [
			'gridBuyer' => $result['gridBuyer'],
			'from_location'		=> $request->from_location,
			'from_location_id'	=> $request->from_location_id,
			'to_location'		=> $request->to_location,
			'to_location_id'	=> $request->to_location_id,
			'valid_from'		=> $request->valid_from,
			'valid_to'			=> $request->valid_to,
			'service_type'		=> $request->service_type,
			'getAllPetTypes' 	=> CommonComponent::getAllPetTypes()
		]);
    }

    /**
	* Displaying Relocation Global Mobility Search results
	*
	* @param  $request
	* @return Response
	*/    
	private function _getRelocHomeGlobalMobilitySearchResults($request)
	{
    	$result = RelocationGlobalSellerComponent::getRelocationSellerSearchResults( 
			$this->data['roleID'], $this->data['serviceID'], $this->data['statusID']
		);           
		return view ( 'seller.relocation.home.gblmobility.search.search_results', [
	 		'gridBuyer' 		=> $result ['gridBuyer'],
	 		'lkp_relgm_services' => CommonComponent::getLkpRelocationGMServices(),
	 		'to_location'		=> $request->to_location,
	 		'to_location_id'	=> $request->to_location_id,
	 		'valid_from'		=> $request->valid_from,
	 		'valid_to'			=> $request->valid_to,
	 		'relgm_service_type'=> $request->relgm_service_type,
		]);
	}

	/**
	* Displaying Relocation Global Mobility Search results
	*
	* @param  $request
	* @return Response
	*/    
	private function _getRelocOffceDomesticSearchResults($request)
	{
		$result = RelocationOfficeSellerComponent::getRelocationOfficeSellerSearchResults( 
			$this->data['roleID'], $this->data['serviceID'], $this->data['statusID']
		);
		return view ( 'seller.relocation.office.domestic.search.search_results', [
			'gridBuyer' => $result ['gridBuyer'],
			'from_location'		=> $request->from_location,
			'from_location_id'	=> $request->from_location_id,
			'valid_from'		=> $request->valid_from,
			'valid_to'			=> $request->valid_to,
		] );
	}					 

	/**
	 * Seller Search Page.
	 *
	 * @param $request
	 * @return Response
	 */
	public function SellerSearchResults(Request $request)
	{
		// Checking Service Selected or not
    	if( !session()->has('service_id') || empty(session('service_id')) ):
    		return redirect('home');
    	endif;
    	$this->data['serviceID'] = $serviceId = session('service_id');

		session()->put('show_layered_filter', 1);

		try
		{
			// Saving Role ID	
			$this->data['roleID'] = Auth::User ()->lkp_role_id;
			
			 //Retrieval of post statuses
			$posts_status = CommonComponent::getPostStatuses();

			$statusId = '';
			if (! empty ( $_POST )) {
				if (isset ( $_POST ['status'] ) && $_POST ['status'] != '') {
					$statusId = $_POST ['status'];
					Session::put ( 'status_search', $_POST ['status'] );
				}
			} else if (isset ( $_GET ['page'] )) {
				$statusId = Session::get ( 'status_search' );
			} else {
				$statusId = '';
				Session::put ( 'status_search', '' );
			}
			$this->data['statusID'] = $statusId;
			 
			$vehicletypemasters 	= CommonComponent::getAllVehicleTypes();
			$loadtypemasters 		= CommonComponent::getAllLoadTypes();
			$packagingtypesmasters 	= CommonComponent::getAllPackageTypes();
			$shipmenttypes 			= CommonComponent::getAllShipmentTypes();
			$senderidentity 		= CommonComponent::getAllSenderIdentities();

			switch($serviceId)
			{
				case ROAD_FTL: 
					CommonComponent::activityLog("FTL_SELLER_SEARCH_FORM_RESULTS",
					 	FTL_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return $this->_getFtlSearchResults($request);
					break;

				case ROAD_PTL: 
				 	CommonComponent::activityLog("PTL_SELLER_SEARCH_FORM_RESULTS",PTL_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
				 	);
					Session::put ( 'ptl_status_search_params', $_REQUEST );
					 $searchparams = Session::get ( 'ptl_status_search_params' );

					 $result = PtlSellerComponent::getPtlSellerSearchList( $this->data['roleID'], $serviceId,$statusId );
					 
					 $results_count_view = Session::get('results_count');
					 if($results_count_view == 1){
					 	$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
					 	$lkp_package_type_name = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					 }else{
					 	if(isset($_REQUEST['lkp_load_type_id']))
					 	$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
					 	else 
					 		$lkp_load_type_name ='';
					 	if(isset($_REQUEST['lkp_packaging_type_id']))
					 	$lkp_package_type_name = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					 	else 
					 	$lkp_package_type_name = '';
					 }
					 
					 $grid = $result ['grid'];
					 $filter = $result ['filter'];
					if(isset($_REQUEST['from_location_id'])){
						Session::put('from_location', $_REQUEST['from_location']);
						Session::put('to_location', $_REQUEST['to_location']);
						Session::put('from_location_id', $_REQUEST['from_location_id']);
						Session::put('to_location_id', $_REQUEST['to_location_id']);
						Session::put('dispatch_date', $_REQUEST['dispatch_date']);
						Session::put('delivery_date', $_REQUEST['delivery_date']);
						Session::put('zone_or_location', $_REQUEST['zone_or_location']);
                        if(isset($_REQUEST['lkp_load_type_id']))
                            $packagingtypesmasters=  CommonComponent::getLoadBasedAllPackages($_REQUEST['lkp_load_type_id']);
                        $packagingtypesmasters_term = CommonComponent::getAllPackageTypes();
					 	return view ( 'ptl.sellers.seller_search', [
						 'grid' => $grid,
						 'posts_status_list'=>$posts_status,
						 'statusSelected' => $statusId,
						 'filter' => $filter,
					 	 'load_type_name'  => $lkp_load_type_name,
					 	 'package_type_name'  => $lkp_package_type_name,
						 'from_city_id'  => Session::get ( 'from_location_id' ),
						 'to_city_id' => Session::get ('to_location_id'),
						 'from_location'  => Session::get ('from_location'),
					 	 'dispatch_date'  => Session::get ('dispatch_date'),
					 	 'delivery_date'  => Session::get ('delivery_date'),
						 'to_location' => Session::get ('to_location'),
						 'loadtypemasters' => $loadtypemasters,
					 	 'vehicletypemasters' => $vehicletypemasters,
						 'zone_or_location' => Session::get ('zone_or_location'),
					 	 'packagingtypesmasters' => $packagingtypesmasters,
                         'packagingtypesmasters_term' => $packagingtypesmasters_term
					 	] );
					}else{
						return view ( 'ptl.sellers.seller_search', [
								'grid' => $grid,
								'posts_status_list'=>$posts_status,
								'statusSelected' => $statusId,
								'filter' => $filter,
								'load_type_name'  => $lkp_load_type_name,
								//'vehicle_type_name'  => $lkp_vehicle_type_name,
								'from_city_id'  => Session::get ( 'from_location_id' ),
								'to_city_id' => Session::get ('to_location_id'),
								'dispatch_date'  => Session::get ('dispatch_date'),
								'delivery_date'  => Session::get ('delivery_date'),
								'from_location'  => Session::get ('from_location'),
								'to_location' => Session::get ('to_location'),
						        'zone_or_location' => Session::get ('zone_or_location'),
							    'loadtypemasters' => $loadtypemasters,
								'vehicletypemasters' => $vehicletypemasters,
								'packagingtypesmasters' => $packagingtypesmasters
								] );
					}
					 break;

				case RAIL: 
					CommonComponent::activityLog("RAIL_SELLER_SEARCH_FORM_RESULTS",
						RAIL_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
					);
					 Session::put ( 'ptl_status_search_params', $_REQUEST );
					 $searchparams = Session::get ( 'ptl_status_search_params' );
					 
					 $result =  RailSellerComponent::getRailSellerSearchList( $this->data['roleID'], $serviceId,$statusId );
					 $results_count_view = Session::get('results_count');
					 if($results_count_view == 1){
					 	$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
					 	$lkp_package_type_name = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					 }else{
					 	if(isset($_REQUEST['lkp_load_type_id']))
					 	$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
					 	else 
					 		$lkp_load_type_name ='';
					 	if(isset($_REQUEST['lkp_packaging_type_id']))
					 	$lkp_package_type_name = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					 	else 
					 	$lkp_package_type_name = '';
					 }
					 $grid = $result ['grid'];
					 $filter = $result ['filter'];
					 if(isset($_REQUEST['from_location_id'])){
					 	Session::put('from_location', $_REQUEST['from_location']);
						Session::put('to_location', $_REQUEST['to_location']);
						Session::put('from_location_id', $_REQUEST['from_location_id']);
						Session::put('to_location_id', $_REQUEST['to_location_id']);
						Session::put('dispatch_date', $_REQUEST['dispatch_date']);
						Session::put('delivery_date', $_REQUEST['delivery_date']);
						Session::put('zone_or_location', $_REQUEST['zone_or_location']);
                        if(isset($_REQUEST['lkp_load_type_id']))
                            $packagingtypesmasters=  CommonComponent::getLoadBasedAllPackages($_REQUEST['lkp_load_type_id']);
                        $packagingtypesmasters_term = CommonComponent::getAllPackageTypes();
					 	return view ( 'ptl.sellers.seller_search', [
					 			'grid' => $grid,
					 			'posts_status_list'=>$posts_status,
					 			'statusSelected' => $statusId,
					 			'filter' => $filter,
					 			'load_type_name'  => $lkp_load_type_name,
					 	 		'package_type_name'  => $lkp_package_type_name,
					 			'from_city_id'  => Session::get ( 'from_location_id' ),
					 			'to_city_id' => Session::get ('to_location_id'),
					 			'dispatch_date'  => Session::get ('dispatch_date'),
					 			'delivery_date'  => Session::get ('delivery_date'),
					 			'from_location'  => Session::get ('from_location'),
					 			'to_location' => Session::get ('to_location'),
					 			'zone_or_location' => Session::get ('zone_or_location'),
								'loadtypemasters' => $loadtypemasters,
                                'vehicletypemasters' => $vehicletypemasters,
                                'packagingtypesmasters' => $packagingtypesmasters,
                                'packagingtypesmasters_term' => $packagingtypesmasters_term
					 			] );
					 }else{
					 	return view ( 'ptl.sellers.seller_search', [
					 			'grid' => $grid,
					 			'posts_status_list'=>$posts_status,
					 			'statusSelected' => $statusId,
					 			'filter' => $filter,
					 			'from_city_id'  => Session::get ( 'from_location_id' ),
					 			'to_city_id' => Session::get ('to_location_id'),
					 			'dispatch_date'  => Session::get ('dispatch_date'),
					 			'delivery_date'  => Session::get ('delivery_date'),
					 			'from_location'  => Session::get ('from_location'),
					 			'to_location' => Session::get ('to_location'),
					 			'zone_or_location' => Session::get ('zone_or_location'),
								'loadtypemasters' => $loadtypemasters,
					 			'vehicletypemasters' => $vehicletypemasters,
					 			'packagingtypesmasters' => $packagingtypesmasters
					 			] );
					 }
					 break;

				case AIR_DOMESTIC:
					CommonComponent::activityLog("AIR_DOMESTIC_SELLER_SEARCH_FORM_RESULTS",
					 AIR_DOMESTIC_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL);
					 Session::put ( 'airdom_status_search_params', $_REQUEST );
					 $searchparams = Session::get ( 'airdom_status_search_params' );

					 $result = AirDomesticSellerComponent::getAirDomesticSellerSearchList( $this->data['roleID'], $serviceId,$statusId );
					 $results_count_view = Session::get('results_count');
					 if($results_count_view == 1){
					 	$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
					 	$lkp_package_type_name = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					 }else{
					 	if(isset($_REQUEST['lkp_load_type_id']))
					 		$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
					 	else
					 		$lkp_load_type_name ='';
					 	if(isset($_REQUEST['lkp_packaging_type_id']))
					 		$lkp_package_type_name = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					 	else
					 		$lkp_package_type_name = '';
					 }
					 $grid = $result ['grid'];
					 $filter = $result ['filter'];
					 if(isset($_REQUEST['from_location_id'])){
					 	Session::put('from_location', $_REQUEST['from_location']);
						Session::put('to_location', $_REQUEST['to_location']);
						Session::put('from_location_id', $_REQUEST['from_location_id']);
						Session::put('to_location_id', $_REQUEST['to_location_id']);
						Session::put('dispatch_date', $_REQUEST['dispatch_date']);
						Session::put('delivery_date', $_REQUEST['delivery_date']);
						Session::put('zone_or_location', $_REQUEST['zone_or_location']);
                        if(isset($_REQUEST['lkp_load_type_id']))
                            $packagingtypesmasters=  CommonComponent::getLoadBasedAllPackages($_REQUEST['lkp_load_type_id']);
                        $packagingtypesmasters_term = CommonComponent::getAllPackageTypes();
					 	return view ( 'ptl.sellers.seller_search', [
					 			'grid' => $grid,
					 			'posts_status_list'=>$posts_status,
					 			'statusSelected' => $statusId,
					 			'filter' => $filter,
					 			'load_type_name'  => $lkp_load_type_name,
					 	 		'package_type_name'  => $lkp_package_type_name,
					 			'from_city_id'  => Session::get ( 'from_location_id' ),
					 			'to_city_id' => Session::get ('to_location_id'),
					 			'dispatch_date'  => Session::get ('dispatch_date'),
					 			'delivery_date'  => Session::get ('delivery_date'),
					 			'from_location'  => Session::get ('from_location'),
					 			'to_location' => Session::get ('to_location'),
						 		'zone_or_location' => Session::get ('zone_or_location'),
								'loadtypemasters' => $loadtypemasters,
                                'vehicletypemasters' => $vehicletypemasters,
                                'packagingtypesmasters' => $packagingtypesmasters,
                                'packagingtypesmasters_term'=>$packagingtypesmasters_term
					 			] );
					 }else{
					 	return view ( 'ptl.sellers.seller_search', [
					 			'grid' => $grid,
					 			'posts_status_list'=>$posts_status,
					 			'statusSelected' => $statusId,
					 			'filter' => $filter,
					 			'from_city_id'  => Session::get ( 'from_location_id' ),
					 			'to_city_id' => Session::get ('to_location_id'),
					 			'dispatch_date'  => Session::get ('dispatch_date'),
					 			'delivery_date'  => Session::get ('delivery_date'),
					 			'from_location'  => Session::get ('from_location'),
					 			'to_location' => Session::get ('to_location'),
					 			'zone_or_location' => Session::get ('zone_or_location'),
								'loadtypemasters' => $loadtypemasters,
								'vehicletypemasters' => $vehicletypemasters,
								'packagingtypesmasters' => $packagingtypesmasters
					 	] );
					 }
					 break;
				case AIR_INTERNATIONAL: 
					CommonComponent::activityLog("AIR_INTERNATIONAL_SELLER_SEARCH_FORM_RESULTS",
					 AIR_INTERNATIONAL_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL);
					 Session::put ( 'airint_status_search_params', $_REQUEST );
					 $searchparams = Session::get ( 'airint_status_search_params' );
					 
					 $result = AirInternationalSellerComponent::getAirInternationalSellerSearchList( $this->data['roleID'], $serviceId,$statusId );
					 $results_count_view = Session::get('results_count');
					 if($results_count_view == 1){
					 	$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
					 	$lkp_package_type_name = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					 }else{
					 	if(isset($_REQUEST['lkp_load_type_id']))
					 		$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
					 	else
					 		$lkp_load_type_name ='';
					 	if(isset($_REQUEST['lkp_packaging_type_id']))
					 		$lkp_package_type_name = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					 	else
					 		$lkp_package_type_name = '';
					 }
					 $grid = $result ['grid'];
					 $filter = $result ['filter'];
					 if(isset($_REQUEST['from_location_id'])){
					 	Session::put('from_location', $_REQUEST['from_location']);
					 	Session::put('to_location', $_REQUEST['to_location']);
					 	Session::put('from_location_id', $_REQUEST['from_location_id']);
					 	Session::put('to_location_id', $_REQUEST['to_location_id']);
                        if(isset($_REQUEST['lkp_load_type_id']))
                            $packagingtypesmasters=  CommonComponent::getLoadBasedAllPackages($_REQUEST['lkp_load_type_id']);
                        $packagingtypesmasters_term = CommonComponent::getAllPackageTypes();
					 	return view ( 'ptl.sellers.seller_search', [
					 			'grid' => $grid,
					 			'posts_status_list'=>$posts_status,
					 			'statusSelected' => $statusId,
					 			'filter' => $filter,
					 			'load_type_name'  => $lkp_load_type_name,
					 	 		'package_type_name'  => $lkp_package_type_name,
					 			'from_city_id'  => $_REQUEST['from_location_id'],
					 			'to_city_id' => $_REQUEST['to_location_id'],
					 			'from_location'  => $_REQUEST['from_location'],
					 			'dispatch_date'  => $_REQUEST['dispatch_date'],
					 			'delivery_date'  => $_REQUEST['delivery_date'],
					 			'to_location' => $_REQUEST['to_location'],
					 			'zone_or_location' =>'1',
								'loadtypemasters' => $loadtypemasters,
                                'vehicletypemasters' => $vehicletypemasters,
                                'packagingtypesmasters' => $packagingtypesmasters,
                                'shipmenttypes' => $shipmenttypes,
                                'senderidentity' =>$senderidentity,
                                'packagingtypesmasters_term'=>$packagingtypesmasters_term
					 			] );
					 }else{
					 	return view ( 'ptl.sellers.seller_search', [
					 			'grid' => $grid,
					 			'posts_status_list'=>$posts_status,
					 			'statusSelected' => $statusId,
					 			'filter' => $filter,
					 			'from_city_id'  => Session::get ( 'from_location_id' ),
					 			'to_city_id' => Session::get ('to_location_id'),
					 			'dispatch_date'  => Session::get ('dispatch_date'),
					 			'delivery_date'  => Session::get ('delivery_date'),
					 			'from_location'  => Session::get ('from_location'),
					 			'to_location' => Session::get ('to_location'),
					 			'zone_or_location' =>'1',
								'loadtypemasters' => $loadtypemasters,'vehicletypemasters' => $vehicletypemasters,'packagingtypesmasters' => $packagingtypesmasters,'shipmenttypes' => $shipmenttypes,'senderidentity' =>$senderidentity
					 			] );
					 }
					 break;

				case COURIER: 
					CommonComponent::activityLog("COURIER_SELLER_SEARCH_FORM_RESULTS",
					 COURIER_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL);
					 Session::put ( 'courier_status_search_params', $_REQUEST );
					 $searchparams = Session::get ( 'courier_status_search_params' );
					 
					 $result = CourierSellerComponent::getCourierSellerSearchList( $this->data['roleID'], $serviceId,$statusId );

					 $results_count_view = Session::get('results_count');
					 if($results_count_view == 1){
					 	$lkp_load_type_name = CommonComponent::getCourierType($_REQUEST['courier_types']);
					 	$lkp_package_type_name = CommonComponent::getCourierDeliveryType($_REQUEST['post_delivery_type']);
					 }else{
					 	if(isset($_REQUEST['courier_types']))
					 		$lkp_load_type_name = CommonComponent::getCourierType($_REQUEST['courier_types']);
					 	else
					 		$lkp_load_type_name ='';
					 	if(isset($_REQUEST['post_delivery_type']))
					 		$lkp_package_type_name = CommonComponent::getCourierDeliveryType($_REQUEST['post_delivery_type']);
					 	else
					 		$lkp_package_type_name = '';
					 }
					 $grid = $result ['grid'];
					 $filter = $result ['filter'];
					 if(isset($_REQUEST['from_location_id'])){
					 	Session::put('from_location', $_REQUEST['from_location']);
						Session::put('to_location', $_REQUEST['to_location']);
						Session::put('from_location_id', $_REQUEST['from_location_id']);
						Session::put('to_location_id', $_REQUEST['to_location_id']);
						Session::put('dispatch_date', $_REQUEST['dispatch_date']);
						Session::put('delivery_date', $_REQUEST['delivery_date']);
						Session::put('zone_or_location', $_REQUEST['zone_or_location']);
					 	return view ( 'ptl.sellers.seller_search', [
					 			'grid' => $grid,
					 			'posts_status_list'=>$posts_status,
					 			'statusSelected' => $statusId,
					 			'filter' => $filter,
					 			'load_type_name'  => Session::get('session_courier'),
					 	 		'package_type_name'  => Session::get('session_courier_delivery_type'),
					 			'from_city_id'  => Session::get ( 'from_location_id' ),
					 			'to_city_id' => Session::get ('to_location_id'),
					 			'dispatch_date'  => Session::get ('dispatch_date'),
					 			'delivery_date'  => Session::get ('delivery_date'),
					 			'from_location'  => Session::get ('from_location'),
					 			'to_location' => Session::get ('to_location'),
						 		'zone_or_location' => Session::get ('zone_or_location'),
								'loadtypemasters' => $loadtypemasters,
					 			'vehicletypemasters' => $vehicletypemasters,
					 			'packagingtypesmasters' => $packagingtypesmasters
					 			] );
					 }else{
					 	return view ( 'ptl.sellers.seller_search', [
					 			'grid' => $grid,
					 			'posts_status_list'=>$posts_status,
					 			'statusSelected' => $statusId,
					 			'filter' => $filter,
					 			'from_city_id'  => Session::get ( 'from_location_id' ),
					 			'to_city_id' => Session::get ('to_location_id'),
					 			'dispatch_date'  => Session::get ('dispatch_date'),
					 			'delivery_date'  => Session::get ('delivery_date'),
					 			'from_location'  => Session::get ('from_location'),
					 			'to_location' => Session::get ('to_location'),
					 			'zone_or_location' => Session::get ('zone_or_location'),
								'loadtypemasters' => $loadtypemasters,
					 			'vehicletypemasters' => $vehicletypemasters,
					 			'packagingtypesmasters' => $packagingtypesmasters
					 			] );
					 }
					 break;
				case OCEAN:
					CommonComponent::activityLog("OCEAN_SELLER_SEARCH_FORM_RESULTS",
					 OCEAN_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL);
					 Session::put ( 'ocean_status_search_params', $_REQUEST );
					 $searchparams = Session::get ( 'ocean_status_search_params' );

					 $result = OceanSellerComponent::getOceanSellerSearchList( $this->data['roleID'], $serviceId,$statusId );
					 $results_count_view = Session::get('results_count');
					 if($results_count_view == 1){
					 	$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
					 	$lkp_package_type_name = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					 }else{
					 	if(isset($_REQUEST['lkp_load_type_id']))
					 		$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
					 	else
					 		$lkp_load_type_name ='';
					 	if(isset($_REQUEST['lkp_packaging_type_id']))
					 		$lkp_package_type_name = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					 	else
					 		$lkp_package_type_name = '';
					 }
					 $grid = $result ['grid'];
					 $filter = $result ['filter'];
					 if(isset($_REQUEST['from_location_id'])){
					 	Session::put('from_location', $_REQUEST['from_location']);
					 	Session::put('to_location', $_REQUEST['to_location']);
					 	Session::put('from_location_id', $_REQUEST['from_location_id']);
					 	Session::put('to_location_id', $_REQUEST['to_location_id']);
	                    if(isset($_REQUEST['lkp_load_type_id']))
	                        $packagingtypesmasters=  CommonComponent::getLoadBasedAllPackages($_REQUEST['lkp_load_type_id']);
	                    $packagingtypesmasters_term = CommonComponent::getAllPackageTypes();
					 	return view ( 'ptl.sellers.seller_search', [
					 			'grid' => $grid,
					 			'posts_status_list'=>$posts_status,
					 			'statusSelected' => $statusId,
					 			'filter' => $filter,
					 			'load_type_name'  => $lkp_load_type_name,
					 	 		'package_type_name'  => $lkp_package_type_name,
					 			'from_city_id'  => $_REQUEST['from_location_id'],
					 			'to_city_id' => $_REQUEST['to_location_id'],
					 			'from_location'  => $_REQUEST['from_location'],
					 			'dispatch_date'  => $_REQUEST['dispatch_date'],
					 			'delivery_date'  => $_REQUEST['delivery_date'],
					 			'to_location' => $_REQUEST['to_location'],
					 			'zone_or_location' =>'1',
								'loadtypemasters' => $loadtypemasters,
                                'vehicletypemasters' => $vehicletypemasters,
                                'packagingtypesmasters' => $packagingtypesmasters,
                                'shipmenttypes' => $shipmenttypes,
                                'senderidentity' =>$senderidentity,
                                'packagingtypesmasters_term'=>$packagingtypesmasters_term
					 			] );
					 }else{
					 	return view ( 'ptl.sellers.seller_search', [
					 			'grid' => $grid,
					 			'posts_status_list'=>$posts_status,
					 			'statusSelected' => $statusId,
					 			'filter' => $filter,
					 			'from_city_id'  => Session::get ( 'from_location_id' ),
					 			'to_city_id' => Session::get ('to_location_id'),
					 			'dispatch_date'  => Session::get ('dispatch_date'),
					 			'delivery_date'  => Session::get ('delivery_date'),
					 			'from_location'  => Session::get ('from_location'),
					 			'to_location' => Session::get ('to_location'),
					 			'zone_or_location' =>'1',
								'loadtypemasters' => $loadtypemasters,'vehicletypemasters' => $vehicletypemasters,'packagingtypesmasters' => $packagingtypesmasters,'shipmenttypes' => $shipmenttypes,'senderidentity' =>$senderidentity
					 			] );
					 }
					 break;

				// Trasport Road IntraCity	 
				case ROAD_INTRACITY: break;

				// Truck Haul	 
				case ROAD_TRUCK_HAUL: 
					CommonComponent::activityLog("TRUCKHAUL_SELLER_SEARCH_FORM_RESULTS",
					 	TRUCKHAUL_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return $this->_getTruckHaulSearchResults($request);
					break;

				// Truck Lease
				case ROAD_TRUCK_LEASE:
					CommonComponent::activityLog("TRUCKLEASE_SELLER_SEARCH_FORM_RESULTS",TRUCKLEASE_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return $this->_getTruckLeaseSearchResults($request);
					break;

				// Relocation Home Domestic	
				case RELOCATION_DOMESTIC: 
					CommonComponent::activityLog("RELOCATION_SELLER_SEARCH_FORM_RESULTS",RELOCATION_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return $this->_getRelocHomeDomesticSearchResults($request);
					break;

				// Relocation Office MOve
				case RELOCATION_OFFICE_MOVE       : 
					CommonComponent::activityLog("RELOCATION_OFFICE_MOVE_SELLER_SEARCH_FORM_RESULTS",
						RELOCATION_OFFICE_MOVE_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,
						CURRENT_URL
					);
					return $this->_getRelocOffceDomesticSearchResults($request);
				break;
                
                // Relocation Pet move
                case RELOCATION_PET_MOVE: 
					CommonComponent::activityLog("RELOCATION_PET_SELLER_SEARCH_FORM_RESULTS",
                        RELOCATION_PET_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
                    );
                    return $this->_getRelocHomePetmoveSearchResults($request);
                    break;

                // Relocation International
				case RELOCATION_INTERNATIONAL:
					CommonComponent::activityLog("RELOCATION_PET_SELLER_SEARCH_FORM_RESULTS",
						RELOCATION_PET_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
					);
					return $this->_getRelocHomeInternationalSearchResults($request);
					break;
				
				// Relocation Global Mobility	
                case RELOCATION_GLOBAL_MOBILITY: 
                	CommonComponent::activityLog("RELOCATION_SELLER_SEARCH_FORM_RESULTS", 
                		RELOCATION_SELLER_SEARCH_FORM_RESULTS,0,HTTP_REFERRER,CURRENT_URL
                	);
                	return $this->_getRelocHomeGlobalMobilitySearchResults($request);
					break;	 

				default:
					break;
			}

		 } catch (Exception $e) {
		 echo 'Caught exception: ', $e->getMessage(), "\n";
		 }
		 
	}
	
	/**
	 * Function for Seller Post cancellation
	 *
	 * @param $postId 20th-Oct-2015
	 *        	Requirements are not cleared yet.
	 *        	Client need to clarify the business
	 *        	rules for Seller Post cancellation.
	 *        	
	 */
	public function cancelSellerPost() {
		if (isset ( $_POST ['postId'] ) && $_POST ['postId'] != '') {
			$postId = $_POST ['postId'];
			
			$hasRecord = DB::table ( 'buyer_quote_sellers_quotes_prices as bsq' )->leftJoin ( 'seller_post_items as spi', 'bsq.seller_post_item_id', '=', 'spi.id' )->leftJoin ( 'seller_posts as sp', 'spi.seller_post_id', '=', 'sp.id' )->leftJoin ( 'users', 'bsq.seller_id', '=', 'users.id' )->where ( 'bsq.seller_id', '=', Auth::User ()->id )->where ( 'spi.seller_post_id', '=', $postId )->select ( 'bsq.initial_quote_price as initial_price', 'seller_acceptence', 'firm_price' )->get ();
			
			if ($hasRecord) {
				$delete = '';
				foreach ( $hasRecord as $record ) {
					
					if ($record->seller_acceptence == 0 && $record->firm_price != '') {
						$delete = 1;
					} else {
						$delete = 0;
					}
				}
				if ($delete == 1) {
					$is_cancelled = SellerListingController::cancelPost ( $postId );
				if($is_cancelled==1){
					
					echo "Selected post has been deleted successfully";
				}
				} else {
					echo "Can't cancel the post as some buyers already have submitted quotes.";
				}
			} 

			else {
				$is_cancelled = SellerListingController::cancelPost ( $postId );
				if($is_cancelled==1){
					
					echo "Selected post has been deleted successfully";
				}
			}
		}
	}
	
	public function cancelPost($postId) {
		$updatedAt = date ( 'Y-m-d H:i:s' );
		$updatedIp = $_SERVER ["REMOTE_ADDR"];
		
		try {
			SellerPost::where ( "id", $postId )->update ( array (
					'lkp_post_status_id' => CANCELLED,
					'updated_at' => $updatedAt,
					'updated_by' => Auth::User ()->id,
					'updated_ip' => $updatedIp 
			));
			SellerPostItem::where ( "seller_post_id", $postId )->update ( array (
			'is_cancelled' => '1',
			'updated_at' => $updatedAt,
			'updated_by' => Auth::User ()->id,
			'updated_ip' => $updatedIp
			));
			
			return 1;
		} catch ( Exception $ex ) {
			
			return 0;
		}
	}
	
	/**
	 * seller posts cancel
	 */
	
	public function sellerPostCancel(){
		if(Session::get ( 'service_id' ) != ''){
			$serviceId = Session::get ( 'service_id' );
		}
		
		$updatedAt = date ( 'Y-m-d H:i:s' );
		$updatedIp = $_SERVER ["REMOTE_ADDR"];
		
		$postIds= explode(",",$_POST['postIds']);
		
		try {
			
			
		switch($serviceId){
			case ROAD_FTL       : 
				if($_POST['str']=="posts"){
					
					//check condition for post status open or not.
					$checkstatus = DB::table('seller_posts')
					->whereIn('seller_posts.id', $postIds)
					->select('seller_posts.lkp_post_status_id')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->lkp_post_status_id;
					}
					if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
										
						SellerPost::whereIn ( "id", $postIds )->update ( array (
								'lkp_post_status_id' => CANCELLED,
								'updated_at' => $updatedAt,
								'updated_by' => Auth::User ()->id,
								'updated_ip' => $updatedIp
						));
						
						$postiems=SellerPostItem::whereIn ( "seller_post_id", $postIds )->get();
						
						for($pi=0;$pi<count($postiems);$pi++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postiems[$pi]->id);
							SellerPostItem::whereIn ( "id", $postiems[$pi] )->update ( array (
									'is_cancelled' => '1',
									'lkp_post_status_id' => CANCELLED,
									'updated_at' => $updatedAt,
									'updated_by' => Auth::User ()->id,
									'updated_ip' => $updatedIp
							));
						}
						
						return "Seller posts deleted successfully!";
						
					} else {
						return "Please select open posts only";
						return 0;
					}
						
				}else{
					
					//check condition for post status open or not.
					$checkstatus = DB::table('seller_post_items')
					->whereIn('seller_post_items.id', $postIds)
					->select('seller_post_items.is_cancelled')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->is_cancelled;
					}

					if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{

						SellerPostItem::whereIn ( "id", $postIds )->update ( array (
								'is_cancelled' => '1',
								'lkp_post_status_id' => CANCELLED,
								'updated_at' => $updatedAt,
								'updated_by' => Auth::User ()->id,
								'updated_ip' => $updatedIp
						));
						//***code for update parent table while delete all post in child table
						$canceled_posts = DB::table('seller_post_items')
						->where('id', $postIds[0])
						->select('seller_post_id')
						->first();
						$canceled_posts_parent_post = DB::table('seller_post_items')
						->where('seller_post_id', $canceled_posts->seller_post_id)
						->where('is_cancelled', 0)
						->get();
						if(count($canceled_posts_parent_post) == 0){
							SellerPost::where ( "id", $canceled_posts->seller_post_id )->update ( array (
									'lkp_post_status_id' => CANCELLED,
									'updated_at' => $updatedAt,
									'updated_by' => Auth::User ()->id,
									'updated_ip' => $updatedIp
							));
						}
						//***code for update parent table while delete all post in child table
						for($i=0;$i<count($postIds);$i++){
							
							SellerMatchingComponent::removeFromMatching($serviceId,$postIds[$i]);
						}
						return "Seller posts successfully deleted";
					} else {
						return "Please select open posts only";
						return 0;
					}
				}
				
			break;
			case ROAD_TRUCK_HAUL       : 
				if($_POST['str']=="posts"){
					
					//check condition for post status open or not.
					$checkstatus = DB::table('truckhaul_seller_posts')
					->whereIn('truckhaul_seller_posts.id', $postIds)
					->select('truckhaul_seller_posts.lkp_post_status_id')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->lkp_post_status_id;
					}
					if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
										
						TruckhaulSellerPost::whereIn ( "id", $postIds )->update ( array (
								'lkp_post_status_id' => CANCELLED,
								'updated_at' => $updatedAt,
								'updated_by' => Auth::User ()->id,
								'updated_ip' => $updatedIp
						));
						
						$postiems=TruckhaulSellerPostItem::whereIn ( "seller_post_id", $postIds )->get();
						
						for($pi=0;$pi<count($postiems);$pi++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postiems[$pi]->id);
							TruckhaulSellerPostItem::whereIn ( "id", $postiems[$pi] )->update ( array (
									'is_cancelled' => '1',
									'lkp_post_status_id' => CANCELLED,
									'updated_at' => $updatedAt,
									'updated_by' => Auth::User ()->id,
									'updated_ip' => $updatedIp
							));
						}
						
						return "Seller posts deleted successfully!";
						
					} else {
						return "Please select open posts only";
						return 0;
					}
						
				}else{
					
					//check condition for post status open or not.
					$checkstatus = DB::table('truckhaul_seller_post_items')
					->whereIn('truckhaul_seller_post_items.id', $postIds)
					->select('truckhaul_seller_post_items.is_cancelled')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->is_cancelled;
					}

					if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{

						TruckhaulSellerPostItem::whereIn ( "id", $postIds )->update ( array (
								'is_cancelled' => '1',
								'lkp_post_status_id' => CANCELLED,
								'updated_at' => $updatedAt,
								'updated_by' => Auth::User ()->id,
								'updated_ip' => $updatedIp
						));
						//***code for update parent table while delete all post in child table
						$canceled_posts = DB::table('truckhaul_seller_post_items')
						->where('id', $postIds[0])
						->select('seller_post_id')
						->first();
						$canceled_posts_parent_post = DB::table('truckhaul_seller_post_items')
						->where('seller_post_id', $canceled_posts->seller_post_id)
						->where('is_cancelled', 0)
						->get();
						if(count($canceled_posts_parent_post) == 0){
							TruckhaulSellerPost::where ( "id", $canceled_posts->seller_post_id )->update ( array (
									'lkp_post_status_id' => CANCELLED,
									'updated_at' => $updatedAt,
									'updated_by' => Auth::User ()->id,
									'updated_ip' => $updatedIp
							));
						}
						//***code for update parent table while delete all post in child table
						for($i=0;$i<count($postIds);$i++){
							
							SellerMatchingComponent::removeFromMatching($serviceId,$postIds[$i]);
						}
						return "Seller posts successfully deleted";
					} else {
						return "Please select open posts only";
						return 0;
					}
				}
				
			break;
			case ROAD_PTL       : 
			if($_POST['str']=="posts"){
				
				//check condition for post status open or not.
				$checkstatus = DB::table('ptl_seller_posts')
				->whereIn('ptl_seller_posts.id', $postIds)
				->select('ptl_seller_posts.lkp_post_status_id')
				->get();
				foreach ($checkstatus as $query) {
					$results[] = $query->lkp_post_status_id;
				}
				if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
				{		
				
					PtlSellerPost::whereIn ( "id", $postIds )->update ( array (
							'lkp_post_status_id' => CANCELLED,
							'updated_at' => $updatedAt,
							'updated_by' => Auth::User ()->id,
							'updated_ip' => $updatedIp
					));
					PtlSellerPostItem::whereIn ( "seller_post_id", $postIds )->update ( array (
							'is_cancelled' => '1',
							'lkp_post_status_id' => CANCELLED,
							'updated_at' => $updatedAt,
							'updated_by' => Auth::User ()->id,
							'updated_ip' => $updatedIp
					));
					$postiems=PtlSellerPostItem::whereIn ( "seller_post_id", $postIds )->get();
					
					for($pi=0;$pi<count($postiems);$pi++){
						SellerMatchingComponent::removeFromMatching($serviceId,$postiems[$pi]->id);
					}
					return "Seller posts successfully deleted";
				} else {
						return "Please select open posts only";
						return 0;
				}
			
			}else{
				//check condition for post status open or not.
				$checkstatus = DB::table('ptl_seller_post_items')
				->whereIn('ptl_seller_post_items.id', $postIds)
				->select('ptl_seller_post_items.is_cancelled')
				->get();
				foreach ($checkstatus as $query) {
					$results[] = $query->is_cancelled;
				}

				if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
				{
				PtlSellerPostItem::whereIn ( "id", $postIds )->update ( array (
						'is_cancelled' => '1',
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
				));
				//***code for update parent table while delete all post in child table
				$canceled_posts = DB::table('ptl_seller_post_items')
				->where('id', $postIds[0])
				->select('seller_post_id')
				->first();
				$canceled_posts_parent_post = DB::table('ptl_seller_post_items')
				->where('seller_post_id', $canceled_posts->seller_post_id)
				->where('is_cancelled', 0)
				->get();
				//echo count($canceled_posts_parent_post);exit;
				if(count($canceled_posts_parent_post) == 0){
					PtlSellerPost::where ( "id", $canceled_posts->seller_post_id )->update ( array (
							'lkp_post_status_id' => CANCELLED,
							'updated_at' => $updatedAt,
							'updated_by' => Auth::User ()->id,
							'updated_ip' => $updatedIp
					));
				}
				//***code for update parent table while delete all post in child table
				
				for($i=0;$i<count($postIds);$i++){
					SellerMatchingComponent::removeFromMatching($serviceId,$postIds[$i]);
				}
				
				return "Seller posts successfully deleted";
				} else {
					return "Please select open posts only";
					return 0;
				}
			}
			
			break;
			case RAIL       :
				if($_POST['str']=="posts"){
						
					//check condition for post status open or not.
					$checkstatus = DB::table('rail_seller_posts')
					->whereIn('rail_seller_posts.id', $postIds)
					->select('rail_seller_posts.lkp_post_status_id')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->lkp_post_status_id;
					}
					if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
							
						RailSellerPost::whereIn ( "id", $postIds )->update ( array (
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
						RailSellerPostItem::whereIn ( "seller_post_id", $postIds )->update ( array (
						'is_cancelled' => '1',
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
						$postiems=RailSellerPostItem::whereIn ( "seller_post_id", $postIds )->get();
							
						for($pi=0;$pi<count($postiems);$pi++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postiems[$pi]->id);
						}
						return "Seller posts successfully deleted";
					} else {
						return "Please select open posts only";
						return 0;
					}
			
				}else{
						
					$checkstatus = DB::table('rail_seller_post_items')
					->whereIn('rail_seller_post_items.id', $postIds)
					->select('rail_seller_post_items.is_cancelled')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->is_cancelled;
					}

					if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
						RailSellerPostItem::whereIn ( "id", $postIds )->update ( array (
						'is_cancelled' => '1',
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
							
						//***code for update parent table while delete all post in child table
						$canceled_posts = DB::table('rail_seller_post_items')
						->where('id', $postIds[0])
						->select('seller_post_id')
						->first();
						$canceled_posts_parent_post = DB::table('rail_seller_post_items')
						->where('seller_post_id', $canceled_posts->seller_post_id)
						->where('is_cancelled', 0)
						->get();

						if(count($canceled_posts_parent_post) == 0){
							RailSellerPost::where ( "id", $canceled_posts->seller_post_id )->update ( array (
							'lkp_post_status_id' => CANCELLED,
							'updated_at' => $updatedAt,
							'updated_by' => Auth::User ()->id,
							'updated_ip' => $updatedIp
							));
						}
						//***code for update parent table while delete all post in child table
							
						for($i=0;$i<count($postIds);$i++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postIds[$i]);
						}
							
						return "Seller posts successfully deleted";
					} else {
						return "Please select open posts only";
						return 0;
					}
				}
					
				break;
			case AIR_DOMESTIC       :
				if($_POST['str']=="posts"){
						
					//check condition for post status open or not.
					$checkstatus = DB::table('airdom_seller_posts')
					->whereIn('airdom_seller_posts.id', $postIds)
					->select('airdom_seller_posts.lkp_post_status_id')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->lkp_post_status_id;
					}
					if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
							
						AirdomSellerPost::whereIn ( "id", $postIds )->update ( array (
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
						AirdomSellerPostItem::whereIn ( "seller_post_id", $postIds )->update ( array (
						'is_cancelled' => '1',
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
						$postiems=AirdomSellerPostItem::whereIn ( "seller_post_id", $postIds )->get();
							
						for($pi=0;$pi<count($postiems);$pi++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postiems[$pi]->id);
						}
						return "Seller posts successfully deleted";
					} else {
						return "Please select open posts only";
						return 0;
					}
			
				}else{
						
					$checkstatus = DB::table('airdom_seller_post_items')
					->whereIn('airdom_seller_post_items.id', $postIds)
					->select('airdom_seller_post_items.is_cancelled')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->is_cancelled;
					}

					if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
						AirdomSellerPostItem::whereIn ( "id", $postIds )->update ( array (
						'is_cancelled' => '1',
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
							
						//***code for update parent table while delete all post in child table
						$canceled_posts = DB::table('airdom_seller_post_items')
						->where('id', $postIds[0])
						->select('seller_post_id')
						->first();
						$canceled_posts_parent_post = DB::table('airdom_seller_post_items')
						->where('seller_post_id', $canceled_posts->seller_post_id)
						->where('is_cancelled', 0)
						->get();
						//echo count($canceled_posts_parent_post);exit;
						if(count($canceled_posts_parent_post) == 0){
							AirdomSellerPost::where ( "id", $canceled_posts->seller_post_id )->update ( array (
							'lkp_post_status_id' => CANCELLED,
							'updated_at' => $updatedAt,
							'updated_by' => Auth::User ()->id,
							'updated_ip' => $updatedIp
							));
						}
						//***code for update parent table while delete all post in child table
							
						for($i=0;$i<count($postIds);$i++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postIds[$i]);
						}
							
						return "Seller posts successfully deleted";
					} else {
						return "Please select open posts only";
						return 0;
					}
				}
					
				break;
			case AIR_INTERNATIONAL       :
				if($_POST['str']=="posts"){
						
					//check condition for post status open or not.
					$checkstatus = DB::table('airint_seller_posts')
					->whereIn('airint_seller_posts.id', $postIds)
					->select('airint_seller_posts.lkp_post_status_id')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->lkp_post_status_id;
					}
					if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
							
						AirintSellerPost::whereIn ( "id", $postIds )->update ( array (
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
						AirintSellerPostItem::whereIn ( "seller_post_id", $postIds )->update ( array (
						'is_cancelled' => '1',
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
						$postiems=AirintSellerPostItem::whereIn ( "seller_post_id", $postIds )->get();
							
						for($pi=0;$pi<count($postiems);$pi++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postiems[$pi]->id);
						}
						return "Seller posts successfully deleted";
					} else {
						return "Please select open posts only";
						return 0;
					}
			
				}else{
						
					$checkstatus = DB::table('airint_seller_post_items')
					->whereIn('airint_seller_post_items.id', $postIds)
					->select('airint_seller_post_items.is_cancelled')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->is_cancelled;
					}

					if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
						AirintSellerPostItem::whereIn ( "id", $postIds )->update ( array (
						'is_cancelled' => '1',
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
							
						//***code for update parent table while delete all post in child table
						$canceled_posts = DB::table('airint_seller_post_items')
						->where('id', $postIds[0])
						->select('seller_post_id')
						->first();
						$canceled_posts_parent_post = DB::table('airint_seller_post_items')
						->where('seller_post_id', $canceled_posts->seller_post_id)
						->where('is_cancelled', 0)
						->get();

						if(count($canceled_posts_parent_post) == 0){
							AirintSellerPost::where ( "id", $canceled_posts->seller_post_id )->update ( array (
							'lkp_post_status_id' => CANCELLED,
							'updated_at' => $updatedAt,
							'updated_by' => Auth::User ()->id,
							'updated_ip' => $updatedIp
							));
						}
						//***code for update parent table while delete all post in child table
							
						for($i=0;$i<count($postIds);$i++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postIds[$i]);
						}
							
						return "Seller posts successfully deleted";
					} else {
						return "Please select open posts only";
						return 0;
					}
				}
					
				break;
			case OCEAN       :
				if($_POST['str']=="posts"){
			
					//check condition for post status open or not.
					$checkstatus = DB::table('ocean_seller_posts')
					->whereIn('ocean_seller_posts.id', $postIds)
					->select('ocean_seller_posts.lkp_post_status_id')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->lkp_post_status_id;
					}
					if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
			
						OceanSellerPost::whereIn ( "id", $postIds )->update ( array (
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
						OceanSellerPostItem::whereIn ( "seller_post_id", $postIds )->update ( array (
						'is_cancelled' => '1',
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
						$postiems=OceanSellerPostItem::whereIn ( "seller_post_id", $postIds )->get();
							
						for($pi=0;$pi<count($postiems);$pi++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postiems[$pi]->id);
						}
						return "Seller posts successfully deleted";
					} else {
						return "Please select open posts only";
						return 0;
					}
						
				}else{
			
					$checkstatus = DB::table('ocean_seller_post_items')
					->whereIn('ocean_seller_post_items.id', $postIds)
					->select('ocean_seller_post_items.is_cancelled')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->is_cancelled;
					}

					if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
						OceanSellerPostItem::whereIn ( "id", $postIds )->update ( array (
						'is_cancelled' => '1',
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
			
						//***code for update parent table while delete all post in child table
						$canceled_posts = DB::table('ocean_seller_post_items')
						->where('id', $postIds[0])
						->select('seller_post_id')
						->first();
						$canceled_posts_parent_post = DB::table('ocean_seller_post_items')
						->where('seller_post_id', $canceled_posts->seller_post_id)
						->where('is_cancelled', 0)
						->get();
						//echo count($canceled_posts_parent_post);exit;
						if(count($canceled_posts_parent_post) == 0){
							OceanSellerPost::where ( "id", $canceled_posts->seller_post_id )->update ( array (
							'lkp_post_status_id' => CANCELLED,
							'updated_at' => $updatedAt,
							'updated_by' => Auth::User ()->id,
							'updated_ip' => $updatedIp
							));
						}
						//***code for update parent table while delete all post in child table
			
						for($i=0;$i<count($postIds);$i++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postIds[$i]);
						}
			
						return "Seller posts successfully deleted";
					} else {
						return "Please select open posts only";
						return 0;
					}
				}
					
				break;
			case COURIER       :
				if($_POST['str']=="posts"){
						
					//check condition for post status open or not.
					$checkstatus = DB::table('courier_seller_posts')
					->whereIn('courier_seller_posts.id', $postIds)
					->select('courier_seller_posts.lkp_post_status_id')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->lkp_post_status_id;
					}
					if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
							
						CourierSellerPost::whereIn ( "id", $postIds )->update ( array (
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
						CourierSellerPostItem::whereIn ( "seller_post_id", $postIds )->update ( array (
						'is_cancelled' => '1',
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
						$postiems=CourierSellerPostItem::whereIn ( "seller_post_id", $postIds )->get();
							
						for($pi=0;$pi<count($postiems);$pi++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postiems[$pi]->id);
						}
						return "Seller posts successfully deleted";
					} else {
						return "Please select open posts only";
						return 0;
					}
			
				}else{
						
					$checkstatus = DB::table('courier_seller_post_items')
					->whereIn('courier_seller_post_items.id', $postIds)
					->select('courier_seller_post_items.is_cancelled')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->is_cancelled;
					}

					if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
						CourierSellerPostItem::whereIn ( "id", $postIds )->update ( array (
						'is_cancelled' => '1',
						'lkp_post_status_id' => CANCELLED,
						'updated_at' => $updatedAt,
						'updated_by' => Auth::User ()->id,
						'updated_ip' => $updatedIp
						));
							
						//***code for update parent table while delete all post in child table
						$canceled_posts = DB::table('courier_seller_post_items')
						->where('id', $postIds[0])
						->select('seller_post_id')
						->first();
						$canceled_posts_parent_post = DB::table('courier_seller_post_items')
						->where('seller_post_id', $canceled_posts->seller_post_id)
						->where('is_cancelled', 0)
						->get();

						if(count($canceled_posts_parent_post) == 0){
							CourierSellerPost::where ( "id", $canceled_posts->seller_post_id )->update ( array (
							'lkp_post_status_id' => CANCELLED,
							'updated_at' => $updatedAt,
							'updated_by' => Auth::User ()->id,
							'updated_ip' => $updatedIp
							));
						}
						//***code for update parent table while delete all post in child table
							
						for($i=0;$i<count($postIds);$i++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postIds[$i]);
						}
							
						return "Seller posts successfully deleted";
					} else {
						return "Please select open posts only";
						return 0;
					}
				}
					
				break;
				
			case RELOCATION_DOMESTIC       :
					if($_POST['str']=="items"){

						//check condition for post status open or not.
						$checkstatus = DB::table('relocation_seller_posts')
						->whereIn('relocation_seller_posts.id', $postIds)
						->select('relocation_seller_posts.lkp_post_status_id')
						->get();
						foreach ($checkstatus as $query) {
							$results[] = $query->lkp_post_status_id;
						}
						if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
						{
								
							OceanSellerPost::whereIn ( "id", $postIds )->update ( array (
							'lkp_post_status_id' => CANCELLED,
							'updated_at' => $updatedAt,
							'updated_by' => Auth::User ()->id,
							'updated_ip' => $updatedIp
							));
							OceanSellerPostItem::whereIn ( "seller_post_id", $postIds )->update ( array (
							'is_cancelled' => '1',
							'lkp_post_status_id' => CANCELLED,
							'updated_at' => $updatedAt,
							'updated_by' => Auth::User ()->id,
							'updated_ip' => $updatedIp
							));
							$postiems=OceanSellerPostItem::whereIn ( "seller_post_id", $postIds )->get();
								
							for($pi=0;$pi<count($postiems);$pi++){
								SellerMatchingComponent::removeFromMatching($serviceId,$postiems[$pi]->id);
							}
							return "Seller posts successfully deleted";
						} else {
							return "Please select open posts only";
							return 0;
						}
				
					}
				
			case ROAD_INTRACITY :
		
				break;
			case ROAD_TRUCK_LEASE       : 
				if($_POST['str']=="posts"){
					
					//check condition for post status open or not.
					$checkstatus = DB::table('trucklease_seller_posts')
					->whereIn('trucklease_seller_posts.id', $postIds)
					->select('trucklease_seller_posts.lkp_post_status_id')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->lkp_post_status_id;
					}
					if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
										
						TruckleaseSellerPost::whereIn ( "id", $postIds )->update ( array (
								'lkp_post_status_id' => CANCELLED,
								'updated_at' => $updatedAt,
								'updated_by' => Auth::User ()->id,
								'updated_ip' => $updatedIp
						));
						
						$postiems=TruckleaseSellerPostItem::whereIn ( "seller_post_id", $postIds )->get();
						
						for($pi=0;$pi<count($postiems);$pi++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postiems[$pi]->id);
							TruckleaseSellerPostItem::whereIn ( "id", $postiems[$pi] )->update ( array (
									'is_cancelled' => '1',
									'lkp_post_status_id' => CANCELLED,
									'updated_at' => $updatedAt,
									'updated_by' => Auth::User ()->id,
									'updated_ip' => $updatedIp
							));
						}
						
						return "Seller posts deleted successfully!";
						
					} else {
						return "Please select open posts only";
						return 0;
					}
						
				}else{
					
					//check condition for post status open or not.
					$checkstatus = DB::table('trucklease_seller_post_items')
					->whereIn('trucklease_seller_post_items.id', $postIds)
					->select('trucklease_seller_post_items.is_cancelled')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->is_cancelled;
					}

					if (!in_array("1", $results) && !in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
						TruckleaseSellerPostItem::whereIn ( "id", $postIds )->update ( array (
								'is_cancelled' => '1',
								'lkp_post_status_id' => CANCELLED,
								'updated_at' => $updatedAt,
								'updated_by' => Auth::User ()->id,
								'updated_ip' => $updatedIp
						));
						//***code for update parent table while delete all post in child table
						$canceled_posts = DB::table('trucklease_seller_post_items')
						->where('id', $postIds[0])
						->select('seller_post_id')
						->first();
						$canceled_posts_parent_post = DB::table('trucklease_seller_post_items')
						->where('seller_post_id', $canceled_posts->seller_post_id)
						->where('is_cancelled', 0)
						->get();
						
						if(count($canceled_posts_parent_post) == 0){
							TruckleaseSellerPost::where ( "id", $canceled_posts->seller_post_id )->update ( array (
									'lkp_post_status_id' => CANCELLED,
									'updated_at' => $updatedAt,
									'updated_by' => Auth::User ()->id,
									'updated_ip' => $updatedIp
							));
						}
						//***code for update parent table while delete all post in child table
						for($i=0;$i<count($postIds);$i++){
							
							SellerMatchingComponent::removeFromMatching($serviceId,$postIds[$i]);
						}
						return "Seller posts successfully deleted";
					} else {
						return "Please select open posts only";
						return 0;
					}
				}
				
			break;
                        
                        case RELOCATION_PET_MOVE       :
					if($_POST['str']=="posts"){
					
					//check condition for post status open or not.
					$checkstatus = DB::table('relocationpet_seller_posts')
					->whereIn('relocationpet_seller_posts.id', $postIds)
					->select('relocationpet_seller_posts.lkp_post_status_id')
					->get();
					foreach ($checkstatus as $query) {
						$results[] = $query->lkp_post_status_id;
					}
					if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
					{
										
						RelocationpetSellerPost::whereIn ( "id", $postIds )->update ( array (
								'lkp_post_status_id' => CANCELLED,
								'updated_at' => $updatedAt,
								'updated_by' => Auth::User ()->id,
								'updated_ip' => $updatedIp
						));
						$postiems=RelocationpetSellerPostItem::whereIn ( "seller_post_id", $postIds )->get();
						
						for($pi=0;$pi<count($postiems);$pi++){
							SellerMatchingComponent::removeFromMatching($serviceId,$postiems[$pi]->id);
							
						}
						return "Seller posts deleted successfully!";
						
					} else {
						return "Please select open posts only";
						return 0;
					}
						
				}
                                
			default : 
			break;
		}
		
		} catch ( Exception $ex ) {
				
			return 0;
		}
	}

	/* update seller post count view from search*/
	public function updatesellerpostview(){
		$postId = $_POST['postId'];
		if(Session::get ( 'service_id' ) != ''){
			$serviceId = Session::get ( 'service_id' );
		}	
		
		try {			
			
			switch($serviceId){
				case ROAD_FTL       : CommonComponent::viewCountForSeller(Auth::User()->id,$postId,'seller_post_item_views');
									  break;
				case ROAD_INTRACITY : CommonComponent::viewCountForSeller(Auth::User()->id,$postId,'ict_seller_post_item_views');                            
                            		  break;
                case ROAD_PTL       : CommonComponent::viewCountForSeller(Auth::User()->id,$postId,'ptl_seller_post_item_views');                            
                            		  break;
                case RAIL           : CommonComponent::viewCountForSeller(Auth::User()->id,$postId,'rail_seller_post_item_views');
                					  break;
                case AIR_DOMESTIC   :CommonComponent::viewCountForSeller(Auth::User()->id,$postId,'airdom_seller_post_item_views');
                 					  break;
                case AIR_INTERNATIONAL : CommonComponent::viewCountForSeller(Auth::User()->id,$postId,'airint_seller_post_item_views');
                					  break;
                case OCEAN          : CommonComponent::viewCountForSeller(Auth::User()->id,$postId,'ocean_seller_post_item_views'); 
                					  break;
                case COURIER        : CommonComponent::viewCountForSeller(Auth::User()->id,$postId,'courier_seller_post_item_views');
									  break;
			}
		}catch ( Exception $ex ) {
				
			return 0;
		}
	}


	public static function sellerMarketleads($id,Request $request)
	{
		Log::info('Seller posts list:'.Auth::id(),array('c'=>'1'));
		try{
			$roleId = Auth::User()->lkp_role_id;

			//Retrieval of post statuses
			$posts_status = CommonComponent::getPostStatuses();

			//Retrieval of seller services
			$lkp_services_seller = CommonComponent::getServices();

			//Retrieval of lead types
			$lkp_lead_types = CommonComponent::getLeadTypes();

			//Search Form logic
			$statusId = '';
			$serviceId = '';
			$type = '';
			if ( !empty($_REQUEST) ){
				if(isset($_REQUEST['status']) && $_REQUEST['status'] != ''){
					$statusId = $_REQUEST['status'];
					Session::put('status_search', $_REQUEST['status']);
				}
				if(isset($_REQUEST['service']) && $_REQUEST['service'] != ''){
					$serviceId = $_REQUEST['service'];
				}
				if(isset($_GET['page'])){
					$statusId = Session::get('status_search');
					$serviceId = Session::get('service_id');
				}
			}else{
				$statusId = '';
				$serviceId = '';
				Session::put('status_search','');
			}

			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}

			switch($serviceId){
				case ROAD_FTL    : CommonComponent::activityLog ( "SELLER_LISTED_POST_ITEMS",
					SELLER_LISTED_POST_ITEMS, 0,
					HTTP_REFERRER, CURRENT_URL );
					$seller_post    = DB::table('seller_posts')
						->leftjoin('seller_post_items','seller_post_items.seller_post_id','=','seller_posts.id')
						->where('seller_posts.id',$id)
						->select('seller_posts.*','seller_post_items.id as spi')
						->get();

					$sellerselectingbuyers    = DB::table('seller_selected_buyers')
						->leftjoin('users','users.id','=','seller_selected_buyers.buyer_id')
						->where('seller_selected_buyers.seller_post_id',$id)
						->select('users.username')
						->get();

					$seller_post_items  = DB::table('seller_post_items')
						->where('seller_post_items.seller_post_id',$id)
						->select('*')
						->get();
					//from location
					if(isset($seller_post_items[0]->from_location_id)){
						$fromlocations  = DB::table('lkp_cities')
							->where('lkp_cities.id',$seller_post_items[0]->from_location_id)
							->select('id','city_name')
							->get();
					}else{
						$fromlocations =0;
					}
					//Payment type
					$payment = DB::table('lkp_payment_modes')
						->where('lkp_payment_modes.id',$seller_post[0]->lkp_payment_mode_id)
						->select('id','payment_mode')
						->get();

					//to location
					if(isset($seller_post_items[0]->to_location_id)){
						$tolocations = DB::table('lkp_cities')
							->where('lkp_cities.id',$seller_post_items[0]->to_location_id)
							->select('id','city_name')
							->get();
					}else{
						$tolocations =0;
					}


					//Viewall count
					$getpostitemids = DB::table('seller_post_items')
						->where('seller_post_items.seller_post_id','=',$id)
						->select('seller_post_items.id')
						->get();
					$allcountview =0;
					if(count($getpostitemids)>0){
						for($i=0;$i<count($getpostitemids);$i++){

							$countview = DB::table('seller_post_item_views')
								->where('seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
								->select('seller_post_item_views.id','seller_post_item_views.view_counts')
								->get();
							if(isset($countview[0]->view_counts))
								$allcountview +=  $countview[0]->view_counts;

						}
					}

					$grid = FtlSellerListingComponent::listFTLBuyerMarketLeads($statusId, $roleId, $serviceId, $id);
					return view('ftl.buyers.market_leads',$grid, [
						'statusSelected' => $statusId,
						'seller_post'=>$seller_post,
						'seller_post_items'=>$seller_post_items,
						'seller_post_id'=>$id,
						'sellerselectingbuyers'=>$sellerselectingbuyers,
						'payment'=>$payment,
						'tolocations'=>$tolocations,
						'typeSelected' => $type,
						'fromlocations'=>$fromlocations,
						'posts_status_list'=>$posts_status,
						'services_seller'=>$lkp_services_seller,
						'lead_types_seller'=>$lkp_lead_types,
						'postId'=>$id,
						'allcountview'=>$allcountview]);
					break;
				case ROAD_PTL       : CommonComponent::activityLog("PTL_SELLER_LISTED_POST_ITEMS",PTL_SELLER_LISTED_POST_ITEMS,0,HTTP_REFERRER,CURRENT_URL);

					//View Count
					$getpostitemids = DB::table('ptl_seller_post_items')
						->where('ptl_seller_post_items.seller_post_id','=',$id)
						->select('ptl_seller_post_items.id')
						->get();
					$allcountview =0;
					if(count($getpostitemids)>0){
						for($i=0;$i<count($getpostitemids);$i++){

							$countview = DB::table('ptl_seller_post_item_views')
								->where('ptl_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
								->select('ptl_seller_post_item_views.id','ptl_seller_post_item_views.view_counts')
								->get();
							if(isset($countview[0]->view_counts))
								$allcountview +=  $countview[0]->view_counts;

						}
					}

					$seller_post_items  = DB::table('ptl_seller_post_items')
						->where('ptl_seller_post_items.seller_post_id',$id)
						->select('*')
						->get();
					$transactionid =  DB::table('ptl_seller_posts')
						->where('ptl_seller_posts.id',$id)
						->select('ptl_seller_posts.transaction_id')
						->get();
					$grid = PtlSellerListingComponent::listPTLBuyerMarketLeads($statusId, $roleId, $serviceId ,$id);
					$postdetails = PtlSellerListingComponent::listPTLSellertopNavPostItems($id);
					return view('ptl.sellers.seller_posts_list',$grid,['postdetails'=>$postdetails,'transactionid'=>$transactionid[0]->transaction_id,'postId'=>$id,
						'seller_post_id'=>$id,
						'posts_status_list'=>$posts_status,
						'statusSelected' => $statusId,
						'seller_post_items'=>$seller_post_items,
						'allcountview'=>$allcountview]);
					break;
				case ROAD_INTRACITY : CommonComponent::activityLog("INTRA_SELLER_LISTED_POST_ITEMS",
					INTRA_SELLER_LISTED_POST_ITEMS,0,
					HTTP_REFERRER,CURRENT_URL);
					$grid = FtlSellerListingComponent::listIntracitySellerPostItems($statusId, $roleId, $serviceId, $id);
					break;
				case RAIL       	: CommonComponent::activityLog("RAIL_SELLER_LISTED_POST_ITEMS",
					RAIL_SELLER_LISTED_POST_ITEMS,0,
					HTTP_REFERRER,CURRENT_URL);

					//View Count
					$getpostitemids = DB::table('rail_seller_post_items')
						->where('rail_seller_post_items.seller_post_id','=',$id)
						->select('rail_seller_post_items.id')
						->get();
					$allcountview =0;
					if(count($getpostitemids)>0){
						for($i=0;$i<count($getpostitemids);$i++){

							$countview = DB::table('rail_seller_post_item_views')
								->where('rail_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
								->select('rail_seller_post_item_views.id','rail_seller_post_item_views.view_counts')
								->get();
							if(isset($countview[0]->view_counts))
								$allcountview +=  $countview[0]->view_counts;

						}
					}

					$seller_post_items  = DB::table('rail_seller_post_items')
						->where('rail_seller_post_items.seller_post_id',$id)
						->select('*')
						->get();

					$transactionid =  DB::table('rail_seller_posts')
						->where('rail_seller_posts.id',$id)
						->select('rail_seller_posts.transaction_id')
						->get();
					$grid = RailSellerListingComponent::listRailBuyerMarketLeads($statusId, $roleId, $serviceId ,$id);
					$postdetails = RailSellerListingComponent::listRailSellertopNavPostItems($id);
					return view('ptl.sellers.seller_posts_list',$grid,['postdetails'=>$postdetails,'transactionid'=>$transactionid[0]->transaction_id,
						'seller_post_id'=>$id,
						'postId'=>$id,
						'posts_status_list'=>$posts_status,
						'statusSelected' => $statusId,
						'seller_post_items'=>$seller_post_items,
						'allcountview'=>$allcountview]);
					break;
				case AIR_DOMESTIC   : CommonComponent::activityLog("AIRDOM_SELLER_LISTED_POST_ITEMS",
					AIRDOM_SELLER_LISTED_POST_ITEMS,0,
					HTTP_REFERRER,CURRENT_URL);

					//View Count
					$getpostitemids = DB::table('airdom_seller_post_items')
						->where('airdom_seller_post_items.seller_post_id','=',$id)
						->select('airdom_seller_post_items.id')
						->get();
					$allcountview =0;
					if(count($getpostitemids)>0){
						for($i=0;$i<count($getpostitemids);$i++){

							$countview = DB::table('airdom_seller_post_item_views')
								->where('airdom_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
								->select('airdom_seller_post_item_views.id','airdom_seller_post_item_views.view_counts')
								->get();
							if(isset($countview[0]->view_counts))
								$allcountview +=  $countview[0]->view_counts;

						}
					}

					$seller_post_items  = DB::table('airdom_seller_post_items')
						->where('airdom_seller_post_items.seller_post_id',$id)
						->select('*')
						->get();
					$transactionid =  DB::table('airdom_seller_posts')
						->where('airdom_seller_posts.id',$id)
						->select('airdom_seller_posts.transaction_id')
						->get();
					$grid = AirDomesticSellerListingComponent::listAirdomBuyerMarketLeads($statusId, $roleId, $serviceId ,$id);
					$postdetails = AirDomesticSellerListingComponent::listAirdomSellertopNavPostItems($id);
					return view('ptl.sellers.seller_posts_list',$grid,['postdetails'=>$postdetails,'transactionid'=>$transactionid[0]->transaction_id,
						'seller_post_id'=>$id,
						'postId'=>$id,
						'posts_status_list'=>$posts_status,
						'statusSelected' => $statusId,
						'seller_post_items'=>$seller_post_items,
						'allcountview'=>$allcountview]);
					break;

				case AIR_INTERNATIONAL   : CommonComponent::activityLog("AIRINT_SELLER_LISTED_POST_ITEMS",
					AIRINT_SELLER_LISTED_POST_ITEMS,0,
					HTTP_REFERRER,CURRENT_URL);

					//View Count
					$getpostitemids = DB::table('airint_seller_post_items')
						->where('airint_seller_post_items.seller_post_id','=',$id)
						->select('airint_seller_post_items.id')
						->get();
					$allcountview =0;
					if(count($getpostitemids)>0){
						for($i=0;$i<count($getpostitemids);$i++){

							$countview = DB::table('airint_seller_post_item_views')
								->where('airint_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
								->select('airint_seller_post_item_views.id','airint_seller_post_item_views.view_counts')
								->get();
							if(isset($countview[0]->view_counts))
								$allcountview +=  $countview[0]->view_counts;

						}
					}

					$seller_post_items  = DB::table('airint_seller_post_items')
						->where('airint_seller_post_items.seller_post_id',$id)
						->select('*')
						->get();
					$transactionid =  DB::table('airint_seller_posts')
						->where('airint_seller_posts.id',$id)
						->select('airint_seller_posts.transaction_id')
						->get();
					$grid = AirInternationalSellerListingComponent::listAirintBuyerMarketLeads($statusId, $roleId, $serviceId ,$id);
					$postdetails = AirInternationalSellerListingComponent::listAirintSellertopNavPostItems($id);
					return view('ptl.sellers.seller_posts_list',$grid,['statusSelected' => $statusId,'postdetails'=>$postdetails,'postId'=>$id,
						'posts_status_list'=>$posts_status,'transactionid'=>$transactionid[0]->transaction_id,
						'seller_post_id'=>$id,
						'postId'=>$id,
						'posts_status_list'=>$posts_status,
						'statusSelected' => $statusId,
						'seller_post_items'=>$seller_post_items,
						'allcountview'=>$allcountview]);
					break;
				case OCEAN   : CommonComponent::activityLog("OCCEAN_SELLER_LISTED_POST_ITEMS",
					OCCEAN_SELLER_LISTED_POST_ITEMS,0,
					HTTP_REFERRER,CURRENT_URL);

					//View Count
					$getpostitemids = DB::table('ocean_seller_post_items')
						->where('ocean_seller_post_items.seller_post_id','=',$id)
						->select('ocean_seller_post_items.id')
						->get();
					$allcountview =0;
					if(count($getpostitemids)>0){
						for($i=0;$i<count($getpostitemids);$i++){

							$countview = DB::table('ocean_seller_post_item_views')
								->where('ocean_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
								->select('ocean_seller_post_item_views.id','ocean_seller_post_item_views.view_counts')
								->get();
							if(isset($countview[0]->view_counts))
								$allcountview +=  $countview[0]->view_counts;

						}
					}

					$seller_post_items  = DB::table('ocean_seller_post_items')
						->where('ocean_seller_post_items.seller_post_id',$id)
						->select('*')
						->get();
					$transactionid =  DB::table('ocean_seller_posts')
						->where('ocean_seller_posts.id',$id)
						->select('ocean_seller_posts.transaction_id')
						->get();
					$grid = OcceanSellerListingComponent::listOcceanBuyerMarketLeads($statusId, $roleId, $serviceId ,$id);
					$postdetails = OcceanSellerListingComponent::listOcceanSellertopNavPostItems($id);
					return view('ptl.sellers.seller_posts_list',$grid,['statusSelected' => $statusId,'postdetails'=>$postdetails,'postId'=>$id,
						'posts_status_list'=>$posts_status,'transactionid'=>$transactionid[0]->transaction_id,
						'seller_post_id'=>$id,
						'seller_post_items'=>$seller_post_items,
						'allcountview'=>$allcountview]);
					break;
				case COURIER   : CommonComponent::activityLog("COURIER_SELLER_LISTED_POST_ITEMS",
					COURIER_SELLER_LISTED_POST_ITEMS,0,
					HTTP_REFERRER,CURRENT_URL);

					//View Count
					$getpostitemids = DB::table('courier_seller_post_items')
						->where('courier_seller_post_items.seller_post_id','=',$id)
						->select('courier_seller_post_items.id')
						->get();
					$allcountview =0;
					if(count($getpostitemids)>0){
						for($i=0;$i<count($getpostitemids);$i++){

							$countview = DB::table('courier_seller_post_item_views')
								->where('courier_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
								->select('courier_seller_post_item_views.id','courier_seller_post_item_views.view_counts')
								->get();
							if(isset($countview[0]->view_counts))
								$allcountview +=  $countview[0]->view_counts;

						}
					}

					$seller_post_items  = DB::table('courier_seller_post_items')
						->where('courier_seller_post_items.seller_post_id',$id)
						->select('*')
						->get();
					$transactionid =  DB::table('courier_seller_posts')
						->where('courier_seller_posts.id',$id)
						->select('courier_seller_posts.transaction_id')
						->get();
					$grid = CourierSellerListingComponent::listCourierBuyerMarketLeads($statusId, $roleId, $serviceId ,$id);
					$postdetails = CourierSellerListingComponent::listCourierSellertopNavPostItems($id);
					return view('ptl.sellers.seller_posts_list',$grid,['statusSelected' => $statusId,'postdetails'=>$postdetails,'postId'=>$id,
						'posts_status_list'=>$posts_status,'transactionid'=>$transactionid[0]->transaction_id,
						'seller_post_id'=>$id,
						'seller_post_items'=>$seller_post_items,
						'allcountview'=>$allcountview]);
					break;
				case ROAD_TRUCK_HAUL: CommonComponent::activityLog ( "TRUCKHAUL_SELLER_MARKET_LISTED_POST_ITEMS_VIEW",
					TRUCKHAUL_SELLER_MARKET_LISTED_POST_ITEMS_VIEW, 0,HTTP_REFERRER, CURRENT_URL );
					$seller_post    = DB::table('truckhaul_seller_posts')
						->leftjoin('truckhaul_seller_post_items','truckhaul_seller_post_items.seller_post_id','=','truckhaul_seller_posts.id')
						->where('truckhaul_seller_posts.id',$id)
						->select('truckhaul_seller_posts.*','truckhaul_seller_post_items.id as spi')
						->get();
					$sellerselectingbuyers    = DB::table('truckhaul_seller_selected_buyers')
						->leftjoin('users','users.id','=','truckhaul_seller_selected_buyers.buyer_id')
						->where('truckhaul_seller_selected_buyers.seller_post_id',$id)
						->select('users.username')
						->get();
					$seller_post_items  = DB::table('truckhaul_seller_post_items')
						->where('truckhaul_seller_post_items.seller_post_id',$id)
						->select('*')
						->get();					
					if(isset($seller_post_items[0]->from_location_id)){
						$fromlocations  = DB::table('lkp_cities')
							->where('lkp_cities.id',$seller_post_items[0]->from_location_id)
							->select('id','city_name')
							->get();
					}else{
						$fromlocations =0;
					}
					//Payment type
					$payment = DB::table('lkp_payment_modes')
						->where('lkp_payment_modes.id',$seller_post[0]->lkp_payment_mode_id)
						->select('id','payment_mode')
						->get();
					//to location
					if(isset($seller_post_items[0]->to_location_id)){
						$tolocations = DB::table('lkp_cities')
							->where('lkp_cities.id',$seller_post_items[0]->to_location_id)
							->select('id','city_name')
							->get();
					}else{
						$tolocations =0;
					}
					//Viewall count
					$getpostitemids = DB::table('truckhaul_seller_post_items')
						->where('truckhaul_seller_post_items.seller_post_id','=',$id)
						->select('truckhaul_seller_post_items.id')
						->get();
					$allcountview =0;
					if(count($getpostitemids)>0){
						for($i=0;$i<count($getpostitemids);$i++){

							$countview = DB::table('truckhaul_seller_post_item_views')
								->where('truckhaul_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
								->select('truckhaul_seller_post_item_views.id','truckhaul_seller_post_item_views.view_counts')
								->get();
							if(isset($countview[0]->view_counts))
								$allcountview +=  $countview[0]->view_counts;
						}
					}

					$grid = TruckHaulBuyerComponent::truckHaulMarketLeadsDetails($statusId, $roleId, $serviceId, $id);
					return view('truckhaul.buyers.market_leads',$grid, [
						'statusSelected' => $statusId,
						'seller_post'=>$seller_post,
						'seller_post_items'=>$seller_post_items,
						'seller_post_id'=>$id,
						'sellerselectingbuyers'=>$sellerselectingbuyers,
						'payment'=>$payment,
						'tolocations'=>$tolocations,
						'typeSelected' => $type,
						'fromlocations'=>$fromlocations,
						'posts_status_list'=>$posts_status,
						'services_seller'=>$lkp_services_seller,
						'lead_types_seller'=>$lkp_lead_types,
						'postId'=>$id,
						'allcountview'=>$allcountview]);
					break;
                                        
                                        
                                        case ROAD_TRUCK_LEASE: CommonComponent::activityLog ( "TRUCKLEASE_SELLER_MARKET_LISTED_POST_ITEMS_VIEW",
					TRUCKLEASE_SELLER_MARKET_LISTED_POST_ITEMS_VIEW, 0,HTTP_REFERRER, CURRENT_URL );
					$seller_post    = DB::table('trucklease_seller_posts')
						->leftjoin('trucklease_seller_post_items','trucklease_seller_post_items.seller_post_id','=','trucklease_seller_posts.id')
						->where('trucklease_seller_posts.id',$id)
						->select('trucklease_seller_posts.*','trucklease_seller_post_items.id as spi')
						->get();
					$sellerselectingbuyers    = DB::table('trucklease_seller_selected_buyers')
						->leftjoin('users','users.id','=','trucklease_seller_selected_buyers.buyer_id')
						->where('trucklease_seller_selected_buyers.seller_post_id',$id)
						->select('users.username')
						->get();
					$seller_post_items  = DB::table('trucklease_seller_post_items')
						->where('trucklease_seller_post_items.seller_post_id',$id)
						->select('*')
						->get();					
					if(isset($seller_post_items[0]->from_location_id)){
						$fromlocations  = DB::table('lkp_cities')
							->where('lkp_cities.id',$seller_post_items[0]->from_location_id)
							->select('id','city_name')
							->get();
					}else{
						$fromlocations =0;
					}
					//Payment type
					$payment = DB::table('lkp_payment_modes')
						->where('lkp_payment_modes.id',$seller_post[0]->lkp_payment_mode_id)
						->select('id','payment_mode')
						->get();
					//to location
					if(isset($seller_post_items[0]->to_location_id)){
						$tolocations = DB::table('lkp_cities')
							->where('lkp_cities.id',$seller_post_items[0]->to_location_id)
							->select('id','city_name')
							->get();
					}else{
						$tolocations =0;
					}
					//Viewall count
					$getpostitemids = DB::table('trucklease_seller_post_items')
						->where('trucklease_seller_post_items.seller_post_id','=',$id)
						->select('trucklease_seller_post_items.id')
						->get();
					$allcountview =0;
					if(count($getpostitemids)>0){
						for($i=0;$i<count($getpostitemids);$i++){

							$countview = DB::table('trucklease_seller_post_item_views')
								->where('trucklease_seller_post_item_views.seller_post_item_id','=',$getpostitemids[$i]->id)
								->select('trucklease_seller_post_item_views.id','trucklease_seller_post_item_views.view_counts')
								->get();
							if(isset($countview[0]->view_counts))
								$allcountview +=  $countview[0]->view_counts;
						}
					}

					$grid = TruckLeaseBuyerComponent::truckLeaseMarketLeadsDetails($statusId, $roleId, $serviceId, $id);
					return view('trucklease.buyers.market_leads',$grid, [
						'statusSelected' => $statusId,
						'seller_post'=>$seller_post,
						'seller_post_items'=>$seller_post_items,
						'seller_post_id'=>$id,
						'sellerselectingbuyers'=>$sellerselectingbuyers,
						'payment'=>$payment,
						'tolocations'=>$tolocations,
						'typeSelected' => $type,
						'fromlocations'=>$fromlocations,
						'posts_status_list'=>$posts_status,
						'services_seller'=>$lkp_services_seller,
						'lead_types_seller'=>$lkp_lead_types,
						'postId'=>$id,
						'allcountview'=>$allcountview]);
					break;
                                            
				default: break;
			}
		} catch( Exception $e ) {
			return $e->message;
		}

	}
}
